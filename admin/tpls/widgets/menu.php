<ul id="menu">
<?php foreach($modules as $m) {?>
	<li <?php if($_GET['module']==$m['module']) echo 'class="act"'?>><a href="?module=<?php echo $m['module']?>"><?php echo $m['name']?></a></li>
<?php }?>
</ul>