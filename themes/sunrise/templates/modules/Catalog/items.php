<?php if(count($products) > 0) {?>
	<div class="ProductsList">
	<?php foreach ($products as $product) {?>
		<?php $img = img()->GetMainImage('Catalog', $product['id']);?>
		<div class="Product">
			
			<div class="ProductImage">
			<?php if(is_file(DIR.$img['src'])) {?>
				<a href="<?php echo $product['link']?>"><img src="<?php echo image($img['src'], 85, 85)?>" alt="<?php echo $product['name']?><?php echo $product['brand']?' '.$product['brand']['name']:''?>" /></a>
			<?php }?>
			</div>
			
			<div class="ProductDesc">
				<!--<div class="ProductTopic"><a href="<?php echo $product['topic']['link']?>"><?php echo $product['topic']['name']?></a></div>-->
				<div class="ProductTitle"><a href="<?php echo $product['link']?>"><?php echo $product['name']?><?php echo $product['brand']?' '.$product['brand']['name']:''?></a></div>
				<div class="ProductText">
					<?php if(!empty($product['snippet'])) {?>
						<?php echo implode('; ', $product['snippet'])?>
					<?php } else {?>
						<?php echo $product['anons']?>
					<?php }?>
				</div>
				<div class="ProductPrice">
				<?php if($product['price'] > 0) {?>
					<?php if($product['price'] < $product['priceOld']) {?>
					<div class="ProductPriceOld">
						<?php echo number_format($product['priceOld'], 0, '', ' ')?> Р.
					</div>
					<?php }?>
					<?php echo number_format($product['price'], 0, '', ' ')?> Р.
				<?php }?>
				</div>
			</div>
		</div>
	<?php }?>
	</div>
<?php }?>