<div id="breadCrumbs">
<?php foreach ($crumbs as $crumb) {?>

	<?php if(!$crumb['active']) {?>
		<a href="<?php echo $crumb['link']?>"><?php echo $crumb['name']?></a>
		<span class="d">/</span>
	<?php } else {?>
		<?php echo $crumb['name']?>
	<?php }?>
	
<?php }?>
</div>