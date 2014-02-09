<? if(!empty($crumbs)) { ?>
	<div id="Breadcrumbs">
		<div class="container">
			<ol class="breadcrumb">
			<? foreach ($crumbs as $crumb) {
				if(!$crumb['active']) {?>
				<li><a href="<?php echo $crumb['link']?>"><?php echo $crumb['name']?></a></li>
				<? } else { ?>
				<li class="active"><?php echo $crumb['name']?></li>
				<? } ?>
			<? } ?>
			</ol>
		</div>
	</div>
<? } ?>