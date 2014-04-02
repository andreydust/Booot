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

			<?
			//Подкатегории
			if(is_array($subCats) && !empty($subCats)) {
				?>
				<div class="row">
				<?
				foreach ($subCats as $key => $subCat) {
					?>
					<div class="col-md-6">
						<p class="lead"><a href="<?=$subCat['link']?>"><?=$subCat['name']?><span class="c-catalog-incat-list-num">(<?=(int)$subCat['productsCount']['count']?>)</span></a></p>
						<p><?=$subCat['anons']?></p>
					</div>
					<?
					if(++$c%2==0 && $c!=count($subCats)) {
						?>
						</div><div class="row">
						<?
					}
				}
				?>
				</div>
				<?
			}

			//Товары
			if(is_array($products) && !empty($products)) {
				if(is_array($subCats) && !empty($subCats)) {
					?>
					<h2>Товары <?=mb_strtolower($name)?></h2>
					<?
				}
				?>
				<div class="row">
				<?
				foreach ($products as $key => $product) {
					$img = img()->GetMainImage('Catalog', $product['id']);
					$productName = trim($product['product_singular_name'].' '.$product['brand_name'].' '.$product['name']);
					?>
					<div class="col-md-6">
						<div class="row">
							<div class="col-xs-3 c-catalog-item-img">
								<?if(is_file(DIR.$img['src'])) {?>
								<a href="<?=$product['link']?>"><img src="<?=image($img['src'], 150, 150)?>" alt="<?=$productName?>" class="img-responsive"></a>
								<?}?>
							</div>
							<div class="col-xs-9">
								<p class="lead"><a href="<?=$product['link']?>"><?=$productName?></a></p>
								<p><?=$product['anons']?></p>
							</div>
						</div>
					</div>
					<?
					if(++$c%2==0 && $c!=count($products)) {
						?>
						</div><div class="row">
						<?
					}
				}
				?>
				</div>
				<?
			}
			?>

			<?=$paging?>

			<?=empty($text)?'':'<div class="c-catalog-list-desc">'.$text.'</div>'?>
			
		</section>


		<?=tpl('modules/Basket/orderForm')?>

		<?=tpl('parts/footer')?>

	</body>
</html>