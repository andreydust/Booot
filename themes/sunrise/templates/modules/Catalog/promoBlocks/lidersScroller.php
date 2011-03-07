<?php if(isset($products) && count($products) > 0) {?>
	<div id="Liders">
		<h2>Лидеры продаж</h2>
		<div class="ProductsScroller">
			<div class="ProductsScrollerHr"></div>
			
			<div class="ProductsScrollerMid">
				<div class="ProductsScrollerLeft"><div class="ProductsScrollerLeftArrow"></div></div>
				<div class="ProductsScrollerWrap">
					<div class="ProductsScrollerWrapInner">
					<?php foreach ($products as $product) {?>
						<?php $img = img()->GetMainImage('Catalog', $product['id']);?>
						<?php if(!is_file(DIR.$img['src'])) continue;?>
						<div class="ProductsScrollerProductW">
							<div class="ProductsScrollerProduct">
								<div class="ProductScrollerImageWrap">
									<div class="ProductScrollerImage">
										<a href="<?php echo $product['link']?>"><img src="<?php echo image($img['src'], 82, 82)?>" alt="<?php echo $product['name']?><?php echo $product['brand']?' '.$product['brand']['name']:''?>" /></a>
									</div>
								</div>
								<div class="ProductScrollerTitle"><a href="<?php echo $product['link']?>"><?php echo $product['name']?><?php echo $product['brand']?' '.$product['brand']['name']:''?></a></div>
								<?php if($product['price'] > 0) {?>
								<div class="ProductScrollerPrice"><?php echo number_format($product['price'], 0, '', ' ')?> Р.</div>
								<?php }?>
							</div>
						</div>
					<?php }?>
					</div>
				</div>
				<div class="ProductsScrollerRight"><div class="ProductsScrollerRightArrow"></div></div>
			</div>
			
			<div class="ProductsScrollerHr"></div>
		</div>
	</div>
<?php }?>