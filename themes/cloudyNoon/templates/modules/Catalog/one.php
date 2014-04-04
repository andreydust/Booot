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
			
			<div class="row c-product-page">
				<div class="col-sm-4">
					<?
					$img = img()->GetMainImage('Catalog', $product['id']);
					if(is_file(DIR.$img['src'])) {
						$images = img()->GetImages('Catalog',$product['id']);
						if(is_array($images) && count($images) > 0) {
							foreach ($images as $image) {
								$counter++;
								?>
								<div class="c-product-image">
									<img 
										src="<?=image($image['src'], 338, 600)?>" 
										class="img-responsive margin-auto" 
										alt="<?=$product['product_singular_name'].' '.
											$product['brand_name'].' '.
											$product['name'].
											($counter>1?' (изображение '.$counter.')':'')?>">
								</div>
								<?
							}
						}
					}
					?>
					
				</div>

				<div class="col-sm-8">
					<div class="c-product-types">
						<?=$product['anons']?>

						<?if($product['price'] > 0) {?>
							<div class="c-product-price-block">
							<?if($product['is_exist'] == 'Y') {?>
								<?if($product['price'] < $product['priceOld']) {?>
									<div class="c-product-price-old">
										<?=number_format($product['priceOld'], 0, '', ' ')?> ₷
									</div>
								<?}?>
								<div>
									<span class="c-product-price"><?=number_format($product['price'], 0, '', ' ')?> ₷</span>
									<div class="c-product-status">
										Есть в наличии
									</div>
								</div>
							<?} else {?>
								<div>
									<div class="c-product-status c-product-status-notexist">
										Нет в наличии
									</div>
								</div>
							<?}?>
							</div>
						<?}?>

						<?if($product['is_exist'] == 'Y') {?>
							<div class="c-product-order">
								<button 
									type="button" 
									class="btn btn-default c-btn-large orderButton" 
									data-product-id="<?=$product['id']?>">
								<?if($product['price'] > 0) {?>	
									Купить
								<?} else {?>
									Заказать
								<?}?>
								</button>
							</div>
						<?}?>
					<?/*
						<h2>Технические характеристики</h2>
						<table>
							<tr>
								<th>Номер чертежа</th>
								<td>1711.40.024 (ГОСТ 3269-78) или 1711.40.024 (ГОСТ 3269-78)</td>
							</tr>
							<tr>
								<th>Код ОКП</th>
								<td>31 8400</td>
							</tr>
						</table>
						<h3>Вертикальная таблица</h3>
						<div class="table-responsive">
							<table class="table table-striped c-product-table">
								<thead>
									<tr>
										<th>Номер</th>
										<th>Код</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>123</td>
										<td>543</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="c-product-order">
							<button type="submit" class="btn btn-default c-btn-large">Сделать заказ</button>
						</div>
					*/?>
					</div>
					<div class="c-product-desc">
						<?=$product['text']?>
					</div>
				</div>
			</div>
			<?/*
			<h2>Полное описание тормозного неповоротного башмака ГОСТ 3269-78</h2>
			<p>Комплекс включает в себя гидравлическую станцию, гидроскобу и поворотную консоль с механизмом перемещения скобы.</p>
			*/?>

		</section>


		<?=tpl('modules/Basket/orderForm')?>

		<?=tpl('parts/footer')?>

	</body>
</html>