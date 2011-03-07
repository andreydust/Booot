<?php
// vim: sw=4:ts=4:noet:sta:

if (isset($_POST['lib_comments'])) {
	if (!session_id())
		session_start();
	ob_start();
	$action = $_POST['action'];
	if ($action == 'add') {
		$r = comments_add();
	} else if ($action == 'del') {
		$r = comments_del();
	} else if ($action == 'mass_prune') {
		$r = comments_mass_prune();
	}
	ob_end_clean();
	echo $r;
	exit;
}

function comments_add()
{
	$parent_id = (int)$_POST['parent_id'];
	$author = mysql_escape_string($_POST['author']);
	$email = mysql_escape_string($_POST['email']);

	$jevix = new Jevix;
	$jevix->cfgAllowTags(array('p','blockquote','cite','b','i','br'));
	$text = $jevix->parse($_POST['text']);

	$text = mysql_escape_string($text);
	$hash = mysql_escape_string($_POST['hash']);


	if (!$author)
		return '{"error":"empty_author"}';
	if (!$email)
		return '{"error":"empty_email"}';
	if (!$text)
		return '{"error":"empty_text"}';
	setcookie('cmt_name', $_POST['author'], time()+86400*365);
	setcookie('cmt_email', $_POST['email'], time()+86400*365);
	$_COOKIE['cmt_name'] = $_POST['author'];
	$_COOKIE['cmt_email'] = $_POST['email'];

	$sql = "
		INSERT INTO prefix_comments
		(parent_id, hash, author, email, text)
		VALUES ({$parent_id}, '{$hash}', '{$author}', '{$email}', '{$text}')";
	db()->query($sql);
	return comments_block($hash, true);
}

function comments_del()
{
	$is_admin = ($_SESSION['user']['type'] == 'a');
	if (!$is_admin)
		return '{"error":"not_authorized"}';
	$id = (int)$_POST['comment_id'];
	if (!$id)
		return '{"error":"no_comment_id"}';
	$sql = "UPDATE prefix_comments SET `deleted`='Y' WHERE `id`={$id}";
	db()->query($sql);
	return comments_block($_POST['hash'], true);
}

function comments_mass_prune()
{
	$is_admin = ($_SESSION['user']['type'] == 'a');
	if (!$is_admin)
		return '{"error":"not_authorized"}';
	$ids = $_POST['comment_ids'];
	foreach ($ids as $k => $v) {
		if (!$v)
			unset($ids[$k]);
		if ($v != (int)$v)
			unset($ids[$k]);
		$ids[$k] = (int)$v;
	}
	if (!count($ids))
		return '{"error":"no_comment_ids"}';
	$ids_str = implode(',', $ids);
	$sql = "UPDATE prefix_comments SET `deleted`='Y' WHERE `id` IN ({$ids_str})";
	db()->query($sql);
	return comments_block($_POST['hash'], true);
}

function comments_block($hash_input, $is_hash = false)
{
	$hash = comments_hash($hash_input);
	if ($is_hash)
		$hash = $hash_input;

	$sql = "SELECT * FROM prefix_comments
		WHERE hash = '{$hash}'
		ORDER BY `timestamp` ASC";
	$rows = db()->rows($sql);
	$comments = array();
	foreach ($rows as $r) {
		$comments[$r['parent_id']][$r['id']] = $r;
	}

	return tpl('parts/comments',
		array('comments' => $comments, 'hash' => $hash)
	);
}

function comments_hash($hash_input)
{
	$last = end($GLOBALS['path']);
	if ($last['id'])
		return md5($last['id'].md5($hash_input));
	return md5($hash_input);
}

function comments_ts_printable($ts)
{
	if (!is_int($ts))
		$ts = strtotime($ts);
	$months = array(1 => 'января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
	return date('d', $ts). ' '.$months[(int)date('m',$ts)].' '.date('Y', $ts).', '.date('H:i', $ts);
}

?>
