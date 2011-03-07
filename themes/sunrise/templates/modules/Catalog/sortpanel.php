
<div id="SortPanel">
	<?php foreach ($options as $field=>$val) {?>
		<?php if($val['current']) {?>
			<div class="SortPanelItem Active">
				<a href="<?php echo $val['link']?>"><?php echo $val['name']?></a>
				<?php echo $val['direction']=='desc'?'↑':'↓'?>
			</div>
		<?php } else {?>
			<div class="SortPanelItem"><a href="<?php echo $val['link']?>"><?php echo $val['name']?></a></div>
		<?php }?>
	<?php }?>
</div>