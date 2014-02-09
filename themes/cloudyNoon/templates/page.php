<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge"><![endif]-->
		<title><?=$title?></title>
		<?=tpl('parts/head')?>
	</head>


	<body>

		<?=tpl('parts/header')?>


		<?=giveObject('Content')->breadCrumbs()?>

		<section class="container">
			<h1><?=$name?></h1>
			<?=$text?>
		</section>


		<?=tpl('modules/Basket/orderForm')?>

		<?=tpl('parts/footer')?>

	</body>
</html>