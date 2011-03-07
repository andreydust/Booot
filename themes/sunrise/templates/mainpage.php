<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
	<title><?php echo $GLOBALS['config']['site']['title']?></title>
	<?php echo tpl('parts/head')?>
</head>

<body>

	<?php echo tpl('parts/header')?>



	<div class="ContentArea">
		
		<div class="ContentAreaIn">
			<div class="SideBar">
				<?php echo giveObject('News')->LastNewsBlock()?>
			</div>
			<div class="Content">
				<div id="Recommended">
					<?php $recommend = giveObject('Catalog')->FeaturedList(4)?>
					<?php if(!empty($recommend)) {?>
						<h2>Рекомендуемые товары</h2>
						<?php echo $recommend?>
					<?php }?>
				</div>
			</div>

			<hr />
		</div>
		
		
		<div class="ContentAreaIn">
			<div class="SideBar">
				<div class="PromoBlock">
					<?php block('main_promo')?>
				</div>
			</div>

			<div class="Content">

				<?php echo giveObject('Catalog')->ProductsScroller()?>

				<div id="AboutUs">
					<?php block('main_text')?>
				</div>

			</div>

			<div class="clear"></div>
		</div>
	</div>

	<?php echo tpl('parts/footer')?>

</body>
</html>