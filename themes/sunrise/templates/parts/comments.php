<?php
// vim: sw=4:ts=4:noet:sta:
?>
<div class="comment_clear"></div>
<div id="comments_outer">
	<script type="text/javascript" src="/js/comments.js"></script>
	<div class="block_comments"><?php cmt_sub($comments, 0)?></div>
	<a href="#" id="comment_root">Оставить комментарий</a>
	<div id="cmt_boxroot" class="comment_add" style="display:none">
	<input type="hidden" name="hash" value="<?php echo $hash; ?>" />
	<input type="hidden" name="parent_id" value="0" />
	<?php if (@$_SESSION['user']['type'] == 'a') { ?><input type="hidden" name="is_admin" value="1" /><?php } ?>
	<div class="field50"><div class="field_place">
		<label for="cmt_author">Имя</label><input id="cmt_author" name="author" type="text" value="<?php if (isset($_COOKIE['cmt_name'])) echo $_COOKIE['cmt_name']?>" />
	</div></div>
	<div class="field50"><div class="field_place">
		<label for="cmt_email">E-mail</label><input id="cmt_email" name="email" type="text" value="<?php if (isset($_COOKIE['cmt_email'])) echo $_COOKIE['cmt_email']?>" />
	</div></div>
	<div class="field"><div class="field_place">
		<label for="cmt_text">Комментарий</label>
		<textarea name="text" id="cmt_text"></textarea>
	</div></div>
	<div class="comment_clear"></div>
	<a class="cmt_add">Добавить</a>
	</div>

	<?php function cmt_sub(&$cmts, $parent_id) { ?>
		<?php if (!isset($cmts[$parent_id]) || !is_array($cmts[$parent_id])) return; ?>
		<div class="comment_lvl">
			<?php foreach ($cmts[$parent_id] as $cid => $c) { ?>
			<div class="comment <?php if($c['deleted']=='Y') echo 'comment-deleted' ?>" rel="<?php echo $cid; ?>">
				<?php if ($c['deleted'] == 'Y') { ?>
				<div class="user"><span class="avatar"><img src="http://gravatar.com/avatar/<?php echo md5('holy.cheater+ufo@gmail.com')?>?s=24" width="24" height="24" alt="" /></span>Неопознанный писатель, <?php echo comments_ts_printable($c['timestamp']); ?></div>
				<p>НЛО уверяет, что не имеет никакого отношения к этой надписи</p>
				<?php } else { ?>
				<div class="user"><span class="avatar"><img src="http://gravatar.com/avatar/<?php echo md5(strtolower($c['email']))?>?s=24" width="24" height="24" alt="" /></span><?php echo $c['author']; ?>, <?php echo comments_ts_printable($c['timestamp']); ?></div>
				<p><?php echo $c['text']; ?></p>
				<?php } ?>
			</div>
			<?php cmt_sub($cmts, $cid); ?>
		<?php } ?>
		</div>
	<?php } ?>
</div>
