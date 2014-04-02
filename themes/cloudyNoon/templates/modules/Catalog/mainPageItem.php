				<h3><a href="<?=$topic['link']?>"><?=$topic['name']?></a></h3>

				<? if(
					(is_array($productsByRoot[$top]) && !empty($productsByRoot[$top]))
					||
					(isset($topicsByTop[$top]) && !empty($topicsByTop[$top]))
				) {

					//Подкатегории
					if(isset($topicsByTop[$top]) && !empty($topicsByTop[$top])) {
						$firstThree = array_slice($topicsByTop[$top], 0, 3);
						$otherSome = array_slice($topicsByTop[$top], 3);
						$threeTopicsOrLess = count($firstThree);
						?>
						<div class="row">
							<?
							foreach ($firstThree as $subTopic) {
								$img = img()->GetMainImage('Catalog', $topImages[$subTopic['id']]);
								?>
								<div class="col-sm-<?=$threeTopicsOrLess==3?'3':'5'?>">
									<a href="<?=$subTopic['link']?>" class="c-img-catalog-item">
										<?if(is_file(DIR.$img['src'])) {?>
											<img src="<?php echo image($img['src'], 120, 120)?>" class="img-responsive hidden-xs" alt="<?=$subTopic['name']?>">
										<?}?>
										<?=$subTopic['name']?><span class="c-catalog-list-num">(<?=(int)$productsCounts[$subTopic['id']]['count']?>)</span>
									</a>
								</div>
								<?
							}
							?>
						</div>
						<? if(!empty($otherSome)) {?>
	            			<ul class="c-catalog-list">
	            				<?
	            				foreach ($otherSome as $subTopic) {
	            					?>
	           						<li><a href="<?=$subTopic['link']?>"><?=$subTopic['name']?><span class="c-catalog-list-num">(<?=(int)$productsCounts[$subTopic['id']]['count']?>)</span></a></li>
	            					<?
	            				}
	            				?>
	            			</ul>
            			<?
            			}
					}

					//Товары
					else {?>
						<div class="row">
						<?
							$threeTopProducts = array_slice($productsByRoot[$top], 0, 3);
							$otherSomeProducts = array_slice($productsByRoot[$top], 3, 5);
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
						?>
						</div>
						<?
						if(!empty($otherSomeProducts)) {?>
							<h4 class="hidden-xs">Еще <?=mb_strtolower($topic['name'])?></h4>
	            			<ul class="c-catalog-list">
	            				<?
	            				foreach ($otherSomeProducts as $productId => $product) {
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
