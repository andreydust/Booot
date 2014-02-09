			<?
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