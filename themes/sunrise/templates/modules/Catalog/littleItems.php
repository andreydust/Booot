<?php if(count($products) > 0) {?>
	<div class="littleProducts">
	<?php foreach ($products as $product) {?>
		<?php $img = img()->GetMainImage('Catalog', $product['id']);?>
		<div class="littleProduct">
			<?php if(is_file(DIR.$img['src'])) {?>
			<div class="lpImage">
				<a href="<?php echo $product['link']?>"><img src="<?php echo image($img['src'], 45, 45)?>" alt="<?php echo $product['name']?><?php echo $product['brand']?' '.$product['brand']['name']:''?>" /></a>
			</div>
			<?php } else {?>
			<div class="lpImageEmpty"></div>
			<?php }?>
			<div class="lpDesc">
				<div class="lpTopic"><a href="<?php echo $product['topic']['link']?>"><?php echo $product['topic']['name']?></a></div>
				<div class="lpName"><a href="<?php echo $product['link']?>"><?php echo $product['name']?><?php echo $product['brand']?' '.$product['brand']['name']:''?></a></div>
				<?php if($product['price'] > 0) {?>
				<div class="lpPrice"><?php echo number_format($product['price'], 0, '', ' ')?> Р.</div>
				<?php }?>
			</div>
		</div>
	<?php }?>
	</div>
<?php }?>