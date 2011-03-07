<style type="text/css">
#userAccessForm { }
#userAccessForm label { display: inline; }
#userAccessForm input { display: inline; }
#userAccessMessage { margin:1em 0; }
</style>

<div id="userAccessMessage"><?php echo $message?></div>

<form method="post" action="<?php echo $link?>" id="userAccessForm">
<?php foreach ($modules as $module) {?>
	<div>
		<input type="checkbox" name="access[<?php echo $module['module']?>]" id="access<?php echo $module['module']?>" <?php echo $module['access']?'checked="checked"':''?>>
		<label for="access<?php echo $module['module']?>"><?php echo $module['name']?> (<?php echo $module['module']?><?php echo $module['hide']?', <b>скрытый раздел</b>':''?>)</label>
	</div>
<?php }?>
</form>