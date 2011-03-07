<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
  <title><?php echo $title?></title>
  <?php echo tpl('parts/head')?>
</head>

<body>

	<?php echo tpl('parts/header')?>



	<div class="ContentArea">
		
		<div class="ContentAreaIn">
			
			<?php $sidebar = giveObject('Content')->SubMenu()?>
			<?php if(!empty($sidebar)) {?>
			<div class="SideBar">
				<?php echo $sidebar?>
			</div>
			<?php }?>
			
			
			<div class="<?php echo !empty($sidebar)?'Content':'ContentFullPage'?>">
				<?php echo giveObject('Content')->breadCrumbs()?>
				<h1><?php echo $name?></h1>
				<?php echo $text?>
			</div>
			
		</div>

	</div>

	<?php echo tpl('parts/footer')?>

</body>
</html>