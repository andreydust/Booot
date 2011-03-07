<div id="UserBarWrapper">
	<div id="UserBar" style="<?php if($user['type'] == 'a') {?>background-color:#ff7f50;<?php }?>">
		<div id="UserBarName">
			<?php echo $login?>,
			<?php echo $user['name']?>
			<?php echo empty($user['post'])?'':'<b>'.$user['post'].'</b>'?>
			<?php echo empty($user['email'])?'':'<a href="mailto:'.$user['email'].'">'.$user['email'].'</a>'?>
		</div>
		
		<div id="UserBarLogoutWrapper">
		<?php if($user['type'] == 'a') {?>
			<a id="UserBarSystem" href="/admin/?module=System" title="Системные настройки и утилиты"><img src="/admin/images/icons/wrench.png"></a>
		<?php }?>
			<a id="UserBarLogout" href="?logout" title="Это почти бессмысленно, лучше просто закройте браузер">Выйти</a>
		</div>
		
	</div>
</div>
<script type="text/javascript">
$('#UserBar')
	.hover(function(){
		$(this).css('left', '192px');
	}, function(){
		$(this).css('left', '195px');
	})
	.click(function(){
		$(this).animate({'left': '0px', 'opacity': 1}, 500);
		$(this).unbind('hover').unbind('mouseover').unbind('mouseout');
	});
</script>