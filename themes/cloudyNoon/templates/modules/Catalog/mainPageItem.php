				<h3><a href="<?=$topic['link']?>"><?=$topic['name']?></a></h3>
				<? if(
					(is_array($productsByRoot[$top]) && !empty($productsByRoot[$top]))
					||
					(isset($topicsByTop[$top]) && !empty($topicsByTop[$top]))
				) {?>

						<div class="row">
							<?
							//3 топовых товара
							if(is_array($productsByRoot[$top]) && !empty($productsByRoot[$top])) {
								$threeTopProducts = array_slice($productsByRoot[$top], 0, 3);
								$threeTopProductsOrLess = count($threeTopProducts);
								foreach ($threeTopProducts as $productId => $product) {
									$img = img()->GetMainImage('Catalog', $product['id']);
									?>
									<div class="col-sm-<?=$threeTopProductsOrLess==3?'3':'5'?>">
										<a href="<?=$product['link']?>" class="c-img-catalog-item">
											<?if(is_file(DIR.$img['src'])) {?>
												<img src="<?php echo image($img['src'], 120, 120)?>" class="img-responsive hidden-xs" alt="<?=$product['singular_name'].' '.$product['bname'].' '.$product['pname']?>">
											<?}?>
											<?=$product['singular_name'].' '.$product['bname'].' '.$product['pname']?>
										</a>
									</div>
									<?
								}
							}
							?>
						</div>


						<? 
						if(isset($topicsByTop[$top]) && !empty($topicsByTop[$top])) {
							//Подкатегории, если они существуют
							?>
							<h4 class="hidden-xs">Каталог <?=mb_strtolower($topic['name'])?></h4>
            				<ul class="c-catalog-list">
            					<?
            					foreach ($topicsByTop[$top] as $subTop => $subTopic) {
            						?>
            						<li><a href="<?=$subTopic['link']?>"><?=$subTopic['name']?></a> (<?=(int)$productsCounts[$subTop]['count']?>)</li>
            						<?
            					}
            					?>
            				</ul>
							<?
						} else {
							//Еще товары из этой категории
							if(count($productsByRoot[$top]) > 3) {
							?>
								<h4 class="hidden-xs">Каталог <?=mb_strtolower($topic['name'])?></h4>
								<ul class="c-catalog-list">
								<?
									$otherProducts = array_slice($productsByRoot[$top], 3);
									foreach ($otherProducts as $productId => $product) {
										?>
										<li><a href="<?=$product['link']?>"><?=$product['singular_name'].' '.$product['bname'].' '.$product['pname']?></a></li>
										<?
									}
								?>
								</ul>
							<?
							}
						}
						?>

				<?}?>
