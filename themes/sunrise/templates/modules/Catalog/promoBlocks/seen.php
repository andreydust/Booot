<?php if($count<1) return ;?>
<div id="SeenBlock">
	<?php if($count>$limit) {?>
	<h3><a href="<?php echo linkByModule('Catalog')?>/seen">Вы смотрели</a> (<?php echo $count?>)</h3>
	<?php } else {?>
	<h3>Вы смотрели</h3>
	<?php }?>
	
	<?php echo $littleProductsList?>
</div>