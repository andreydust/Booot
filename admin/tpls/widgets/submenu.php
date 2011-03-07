<div class="sideitem">
	<ul>
	<?php foreach ($data as $i) {?>
		<li class="<?php echo $i['act']?'act':''?>"><a href="<?php echo $i['link']?>"><?php echo $i['name']?></a></li>
	<?php }?>
	</ul>
</div>