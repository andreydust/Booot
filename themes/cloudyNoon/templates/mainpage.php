<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge"><![endif]-->
		<title><?php echo $GLOBALS['config']['site']['title']?></title>
		<?php echo tpl('parts/head')?>
	</head>


	<body>

		<?php echo tpl('parts/header')?>

		<?php echo giveObject('Content')->PromoSlider()?>

		<?php echo giveObject('Catalog')->MainPage_cloudyNoon()?>

		<?php echo tpl('modules/Basket/orderForm')?>

		<?php echo tpl('parts/footer')?>

	</body>
</html>