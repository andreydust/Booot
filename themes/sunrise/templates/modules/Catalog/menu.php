
<?php if(!empty($topics)) {?>
	<div id="catalogMenu">
		<ul id="catalogTopMenu">
		<?php foreach ($topics[0] as $root) {?>
			<?php if(isset($topics[$root['id']]) && !empty($topics[$root['id']])) {?>
				<li class="<?php echo $root['active']?'Active':''?>">
					<a href="<?php echo $root['link']?>" id="menuTopic<?php echo $root['id']?>" class="ajaxLink"><?php echo $root['name']?></a>
					<div class="catalogMenuCorner"></div>
				</li>
			<?php } else {?>
				<li class="<?php echo $root['active']?'Active':''?>"><a href="<?php echo $root['link']?>"><?php echo $root['name']?></a></li>
			<?php }?>
		<?php }?>
		</ul>
		<div id="catalogSubMenu">
		<?php foreach ($topics[0] as $root) {?>
			<ul id="subMenuTopic<?php echo $root['id']?>" class="catalogSubMenu <?php echo $root['active']?'Active':''?>">
			<?php if(isset($topics[$root['id']])) foreach ($topics[$root['id']] as $sub) {?>
				<li class="<?php echo $sub['active']?'ActiveSub':''?>"><a href="<?php echo $sub['link']?>"><?php echo $sub['name']?></a> (<?php echo $sub['count']?>)</li>
			<?php }?>
			</ul>
		<?php }?>
		</div>
	</div>
<?php }?>