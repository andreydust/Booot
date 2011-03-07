<?php

require_once 'system/loading.php';
if (@$_SESSION['godmode_ref'] && isset($_GET['return'])) {
    header("Location: {$_SESSION['godmode_ref']}");
    unset($_SESSION['godmode_ref']);
    exit;
}

timegen('main');

require_once 'system/starter.php';

echo tpl('index',array(
	'title'		=> (!empty($module->title)?strip_tags($module->title).' — ':'').'Booot',
	'h1'		=> $module->title,
	'content'	=> $module->content,
	'menu'		=> menu(array('modules'=>$modules)),
	'hint'		=> $module->Hint(),
	'sidemenu'	=> $module->SubMenu(),
	'userbar'	=> $user->userBar()
));

timegen_result();