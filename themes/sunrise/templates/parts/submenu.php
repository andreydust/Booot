<?php if(empty($menu)) return?>

<div class="SubMenu">
	<?php if(isset($toppage)) {?>
	<h2><?php
		if($activeSet) {?>
			<a href="<?php echo linkById($toppage['id'])?>"><?php echo $toppage['name']?></a>
		<?php } else {?>
			<?php echo $toppage['name']?>
		<?php }?>
	</h2>
	<?php }?>
	
	<div class="SubMenuBlock">
		<ul>
		<?php foreach($menu as $i) {?>
			<li <?php echo isset($i['active'])&&$i['active']?'class="Active"':''?>>
			<?php if(isset($i['link'])) {?>
				<a href="<?php echo $i['link']?>"><?php echo $i['name']?></a>
			<?php } else {?>
				<?php echo $i['name']?>
			<?php }?>
				<?php if(isset($i['sub']) && is_array($i['sub'])){?>
				<ul>
				<?php foreach($i['sub'] as $s) {?>
					<li <?php echo isset($s['active'])&&$s['active']?'class="Active"':''?>><a href="<?php echo $s['link']?>"><?php echo $s['name']?></a></li>
				<?php }?>
				</ul>
				<?php }?>
			</li>
		<?php }?>
		</ul>
	</div>
</div>