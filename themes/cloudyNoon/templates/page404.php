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
			<h1>404 — страница не найдена</h1>
			<?=$debug?'<p>'.$debug.'</p>':''?>
			<p>Вы можете перейти <a href="/">на главную страницу</a>, <a href="#SearchInput" onclick="$('#SearchInput').focus(); return false;">воспользоваться поиском</a> или выбрать интересующую категорию товаров ниже</p>
		</section>

		<?php echo giveObject('Catalog')->MainPage_cloudyNoon()?>


		<?=tpl('modules/Basket/orderForm')?>

		<?=tpl('parts/footer')?>

	</body>
</html>