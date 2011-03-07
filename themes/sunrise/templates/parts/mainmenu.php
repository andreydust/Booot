<?php foreach($menu as $root) {?>

<?php if($root['root']['active']) {?>
	<li class="Active"><a href="/<?php echo $root['root']['nav']?>"><?php echo $root['root']['name']?></a></li>
<?php } else {?>
	<li><a href="/<?php echo $root['root']['nav']?>"><?php echo $root['root']['name']?></a></li>
<?php }?>

<?php }?>