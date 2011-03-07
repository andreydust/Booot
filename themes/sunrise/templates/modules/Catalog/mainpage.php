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
		
			<div class="SideBar">
				<?php echo giveObject('Catalog')->CompareBlock()?>
				<?php echo giveObject('Catalog')->SeenBlock(3)?>
				<?php echo giveObject('Content')->SubMenu(21)?>
			</div>

			<div class="Content">
				
				<?php echo giveObject('Content')->breadCrumbs()?>
				
				<h1><?php echo $name?></h1>
				
				

				<div id="Recommended">
					<h2>Рекомендуемые товары</h2>
					<?php echo giveObject('Catalog')->FeaturedList(4)?>
				</div>
				
				<?php echo giveObject('Catalog')->ProductsScroller()?>
				
			</div>
			
			<div class="clear"></div>
			
		</div>
	</div>
	
	<?php echo tpl('parts/footer')?>


</body>
</html>