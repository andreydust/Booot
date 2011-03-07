				<div id="BasketBlock">
					
					<form action="<?php echo linkByModule('Basket')?>/edit" method="post" id="basketListForm">
						<table id="BasketList">
						<?php foreach ($products as $product) {?>
						<?php $img = img()->GetMainImage('Catalog', $product['id']);?>
							<tr>
								<td class="blImage">
								<?php if(is_file(DIR.$img['src'])) {?>
									<a href="<?php echo $product['link']?>"><img src="<?php echo image($img['src'], 80, 100)?>" alt="<?php echo $product['name']?><?php echo $product['brand']?' '.$product['brand']['name']:''?>" /></a>
								<?php }?>
								</td>
								<td class="blName">
									<a href="<?php echo $product['topic']['link']?>" class="smallLabel"><?php echo $product['topic']['name']?></a>
									<a href="<?php echo $product['link']?>" class="blNameName"><?php echo $product['name']?><?php echo $product['brand']?' '.$product['brand']['name']:''?></a>
								</td>
								<td class="blCount">
									<label for="count1" class="smallLabel">Количество</label>
									<input type="text" name="count[<?php echo $product['id']?>]" id="count<?php echo $product['id']?>" class="editCount" value="<?php echo $product['inbasket']?>" />
								</td>
								<td class="blPrice">
								<?php if($product['price'] > 0) {?>
									<span class="smallLabel">Цена</span>
									<span class="blPriceVal">
										<span class="dPrice"><?php echo number_format($product['price'], 0, '', ' ')?></span> Р.
									</span>
								<?php }?>
								</td>
								<td class="blPrice">
								<?php if($product['tprice'] > 0) {?>
									<span class="smallLabel">Стоимость</span>
									<span class="blPriceVal">
										<span class="dTPrice"><?php echo number_format($product['tprice'], 0, '', ' ')?></span> Р.
									</span>
								<?php }?>
								</td>
								<td class="blDel">
									<a href="<?php echo linkByModule('Basket')?>/del/<?php echo $product['id']?>" id="del<?php echo $product['id']?>" class="ajaxLink">Убрать</a>
								</td>
							</tr>
						<?php }?>
						</table>
						
						<hr class="w98" />
						
						<div id="BasketTotal">
							К заказу <span id="dSTCount"><?php echo $totals['count']?> <?php echo plural($totals['count'], 'товаров', 'товар', 'товара')?></span>
							на сумму <span id="dSTPrice"><?php echo number_format($totals['summ'], 0, '', ' ')?> <?php echo plural($totals['summ'], 'рублей', 'рубль', 'рубля')?></span>
							<noscript><button id="BasketReCountBtn">Пересчитать</button></noscript>
						</div>
						
					</form>

					<h2>Оформление заказа</h2>
					
					<?php if(!empty($errors)) {?>
					<ul class="jep jepErrors">
						<?php foreach ($errors as $error) {?>
							<li><?php echo $error?></li>
						<?php }?>
					</ul>
					<?php }?>
					
					<div id="Order">
						<form action="<?php echo linkByModule('Basket')?>" method="post">
							<div class="orderField">
								<label for="Oname">Представьтесь, пожалуйста</label>
								<input type="text" name="name" id="Oname" value="<?php echo isset($_POST['name'])?htmlspecialchars($_POST['name']):''?>" />
							</div>
							<div class="orderFieldHalf">
								<label for="Ophone">Контактный телефон <span class="jep">*</span></label>
								<input type="text" name="phone" id="Ophone" value="<?php echo isset($_POST['phone'])?htmlspecialchars($_POST['phone']):''?>" />
							</div>
							<div class="orderFieldHalf">
								<label for="Omail">Электронная почта</label>
								<input type="text" name="mail" id="Omail" value="<?php echo isset($_POST['mail'])?htmlspecialchars($_POST['mail']):''?>" />
							</div>
							<div class="orderField">
								<label for="Oaddress">Адрес доставки</label>
								<input type="text" name="address" id="Oaddress" value="<?php echo isset($_POST['address'])?htmlspecialchars($_POST['address']):''?>" />
							</div>

							<?php if(!empty($paymethods)) {?>
							<div id="PayMethod">
								<label for="PayMethodSelect">Способ оплаты</label>
								<div id="PayMethodSelect">
									<div id="PayMethodSelectText">
										<?php foreach ($paymethods as $pm) { $showFirst = !isset($showFirst)?'display:block;':'';?>
										<div id="Payment<?php echo $pm['id']?>" style="<?php echo $showFirst?>">
											<h3><?php echo $pm['name']?></h3>
											<?php echo $pm['text']?>
										</div>
										<?php } unset($showFirst);?>
									</div>
									<div id="PayMethodSelectVariants">
										<ul>
										<?php foreach ($paymethods as $pm) { $showFirst = !isset($showFirst)?'Active':'';?>
											<li class="<?php echo $showFirst?>">
												<input type="radio" name="payment" style="display:none;" id="PaymentMethod<?php echo $pm['id']?>" value="<?php echo $pm['id']?>" <?php echo empty($showFirst)?'':'checked="checked"'?> />
												<label for="PaymentMethod<?php echo $pm['id']?>"><a href="#Payment<?php echo $pm['id']?>" class="ajaxLink"><?php echo $pm['name']?></a></label>
											</li>
										<?php }?>
										</ul>
									</div>
									<script type="text/javascript">
									$('#' + $('#PayMethodSelectVariants .Active label').attr('for')).attr('checked', true);
									</script>
								</div>
							</div>
							<?php }?>
							<div>
								<button id="OrderButton">Купить</button>
							</div>
						</form>
					</div>
				</div>