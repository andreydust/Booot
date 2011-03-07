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
				<?php echo $selection?>
				<div id="CompareBlockWrap">
					<?php echo giveObject('Catalog')->CompareBlock()?>
				</div>
				<?php echo giveObject('Catalog')->SeenBlock(3)?>
				<?php echo giveObject('Content')->SubMenu(21)?>
			</div>

			<div class="Content">
				
				<?php echo giveObject('Content')->breadCrumbs()?>
				
				<h1><?php echo $name?></h1>
				
				<?php if((!empty($brands) && count($brands) > 1) || $slider_vals['min']+200 < $slider_vals['max']) {?>
				<div id="BrandPriceSelect">
					<form action="" method="get">
						<input type="hidden" name="addGet" value="<?php echo $brand_price_link?>" />
						
						<?php echo giveObject('Catalog')->sortPanel()?>
						
					<?php if(!empty($brands) && count($brands) > 1) {?>
						<div id="BrandsLabel">Производители:</div>
						<div class="clear"></div>
						<div id="BrandsList">
						<?php foreach ($brands as $brand) {?>
							<span class="BrandsItem"><input type="checkbox" value="<?php echo $brand['nav']?>" id="brand<?php echo $brand['id']?>" name="brands[]" <?php echo $brand['checked']?'checked="checked"':''?>><label for="brand<?php echo $brand['id']?>"><a href="<?php echo $brand['link']?>"><?php echo $brand['name']?></a></label></span>
						<?php }?>
						</div>
						<div id="BrandsControls">
							<a href="#" class="selectBrandsAll ajaxLink" id="allBrands">Выделить все</a>
							<a href="#" class="selectBrandsAll ajaxLink" id="allBrandsUnselect">Снять выделенные</a>
						</div>
					<?php }?>
					
						<div class="clear"></div>
						
						<div id="PriceAndExist">
							С ценой
							<label for="PriceFrom">от</label>
							<input type="text" value="<?php echo $slider_vals['from']?>" id="PriceFromValue" name="PriceFromValue" />
							<label for="PriceTo">до</label>
							<input type="text" value="<?php echo $slider_vals['to']?>" id="PriceToValue" name="PriceToValue" />
							руб.
	
							<span id="ShowOnlyExist">
								<input type="checkbox" name="exist" id="exist" value="Y" <?php echo $exist?'checked="checked"':''?> />
								<label for="exist">только в наличии на складе</label>
							</span>
							
							<div id="BrandPriceControls">
								<button id="BrandPriceShow">Показать</button>
								<?php if(isset($_GET['brands']) || isset($_GET['PriceFromValue']) || isset($_GET['exist']) || isset($_GET['select'])) {?>
								<a href="<?php echo $link?>" class="ajaxLink">все</a>
								<?php }?>
							</div>
						</div>
					</form>
				</div>
				<?php }?>
				
				
				<div class="ProductsList">
				<?php if(count($products) == 0) {?>
				<p>Товары не найдены или отсутствуют</p>
				<?php }?>
				<?php foreach ($products as $product) {?>
					<?php $img = img()->GetMainImage('Catalog', $product['id']);?>
					<div class="Product">
					
						<div class="ProductImage">
						<?php if(is_file(DIR.$img['src'])) {?>
							<a href="<?php echo $product['link']?>"><img src="<?php echo image($img['src'], 85, 100)?>" alt="<?php echo $product['brand_name']?> <?php echo $product['name']?>" /></a>
						<?php }?>
						</div>
					
						<div class="ProductDesc">
							<div class="ProductTitle"><a href="<?php echo $product['link']?>"><?php echo $product['brand_name']?> <?php echo $product['name']?></a></div>
							<div class="ProductText">
								<?php if(!empty($product['snippet'])) {?>
									<?php echo implode('; ', $product['snippet'])?>
								<?php } else {?>
									<?php echo $product['anons']?>
								<?php }?>
							</div>
							<div class="ProductPrice">
								
								<?php if($product['is_exist'] == 'Y') {?>
								<div>
									<?php echo giveObject('Catalog')->BuyButton($product['id'], 'inlist')?>
								</div>
								<?php } else {?>
									<span class="noexist">нет в наличии</span>
								<?php }?>
								<?php if($product['price'] > 0) {?>
									<?php echo number_format($product['price'], 0, '', ' ')?> Р.
									<?php if($product['price'] < $product['priceOld']) {?>
									<br />
									<div class="ProductPriceOld">
										<?php echo number_format($product['priceOld'], 0, '', ' ')?> Р.
									</div>
									<?php }?>
								<?php } else {?><?php }?>
							</div>
						</div>
					</div>
				<?php }?>
				</div>
				
				<?php echo $paging?>
				
			</div>
			
			<div class="clear"></div>
			
		</div>
	</div>
	
	<?php echo tpl('parts/footer')?>


</body>
</html>