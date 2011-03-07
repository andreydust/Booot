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
				
				<?php $image = img()->GetMainImage('Catalog',$product['id'])?>
				<?php if(is_file(DIR.$image['src'])) {?>
					<?php $images = img()->GetImages('Catalog',$product['id'])?>
					<div id="ProductImages">
						<div class="ProductImagesBlock">
							<div class="ProductImage">
								<a href="<?php echo $image['src']?>" id="MainProductImage" class="lightview" rel="productGallery"><img src="<?php echo image($image['src'], 200, 250)?>" alt="<?php echo $product['name']?>" /></a>
							</div>
							<?php if(is_array($images) && count($images) > 1) {?>
							<div class="ProductSmallImages">
								<?php foreach ($images as $img) {
									if($img['id'] == $image['id']) $imgAct = 'Active';
									else  $imgAct = '';
								?>
									<a href="<?php echo $img['src']?>" class="lightview hiddenImages" id="hiddenImage<?php echo $img['id']?>" rel="productGallery<?php echo $imgAct?>"></a>
									<div class="lpImage <?php echo $imgAct?>">
										<a href="<?php echo $img['src']?>" id="visibleImage<?php echo $img['id']?>" rel="<?php echo image($img['src'], 200, 250)?>" class="SmallProductImage"><img src="<?php echo image($img['src'], 56, 45)?>" alt="<?php echo $product['name']?>" /></a>
									</div>
								<?php }?>
							</div>
							<?php }?>
						</div>
					</div>
				<?php }?>

				<?php echo giveObject('Catalog')->AlsoBoughtBlock()?>

				<div id="CompareBlockWrap">
					<?php echo giveObject('Catalog')->CompareBlock()?>
				</div>
				
				<?php echo giveObject('Catalog')->SeenBlock(3)?>

			</div>
			
			
			<div class="Content">
				
				<?php echo giveObject('Content')->breadCrumbs()?>
				
				<div id="ProductMainPanel">
					<div id="ProductMainPanelLeft">
						<h1><?php echo $name?></h1>
						<div id="ProductExistingInfo">
							<?php if($product['is_exist'] == 'Y') {?>
								<div id="ProductWarehouse">Есть в наличии</div>
								<div id="ProductDelivery"><?php block('product_delivery_time')?></div>
							<?php } else {?>
								<div id="ProductWarehouse">Товара сейчас нет на складе</div>
								<div id="ProductDelivery">Уточнить возможность поставки можно по телефону <?php block('phone')?></div>
							<?php }?>
						</div>
					</div>
					<div id="ProductMainPanelRight">
					<?php if($product['price'] > 0) {?>
						<div class="Price"><?php echo number_format($product['price'], 0, '', ' ')?> Р.</div>
						<?php if($product['price'] < $product['priceOld']) {?>
						<div class="ProductPriceOld">
							<?php echo number_format($product['priceOld'], 0, '', ' ')?> Р.
						</div>
						<?php }?>
					<?php } else {?>
						<div class="Price"> </div>
					<?php }?>
						<!--
						<a
								href="<?php echo linkByModule('Catalog')?>/compare/add/<?php echo $product['id']?>"
								style="<?php echo $product['inCompare']?'display:none':''?>"
								id="compare<?php echo $product['id']?>"
								class="ajaxLink ajaxCompare CompareInCard">добавить в сравнение</a>
						-->
						<?php if($product['is_exist'] == 'Y') {?>
						<div id="BuyButtonWrap">
							<?php echo giveObject('Catalog')->BuyButton($product['id'], 'one')?>
						</div>
						<?php } else {?>
							<span class="noexistOne">нет в наличии</span>
						<?php }?>
						
					</div>
					<div class="clear"></div>
				</div>



				<div class="ProductDetails">
					<noscript>
						<style type="text/css">
						/*.ProductDetailsMenu { display:none; }*/
						#Description,#Types,#Docs,#Comments { display:block; margin-bottom: 2em; }
						.noscriptTitle { display: block; }
						</style>
					</noscript>
					<div class="ProductDetailsMenu">
						<?php $activeTab = 'Active'; $showTab = 'display:block;'; ?>
						<?php if(!empty($product['text'])) {?>
							<div class="ProductDetailsMenuItem <?php echo $activeTab; $activeTab = '';?>">
								<a href="#Description" class="ajaxLink">Описание</a>
								<div class="ActiveCorner"><div class="ActiveCornerIn"></div></div>
							</div>
						<?php }?>
						<?php if(!empty($types)) {?>
							<div class="ProductDetailsMenuItem <?php echo $activeTab; $activeTab = '';?>">
								<a href="#Types" class="ajaxLink">Характеристики</a>
								<div class="ActiveCorner"><div class="ActiveCornerIn"></div></div>
							</div>
						<?php }?>
						<?php if(!empty($files)) {?>
							<div class="ProductDetailsMenuItem <?php echo $activeTab; $activeTab = '';?>">
								<a href="#Docs" class="ajaxLink">Документы</a>
								<div class="ActiveCorner"><div class="ActiveCornerIn"></div></div>
							</div>
						<?php }?>
						<?php if($show_comments) {?>
						<div class="ProductDetailsMenuItem <?php echo $activeTab; $activeTab = '';?>">
							<a href="#Comments" class="ajaxLink">Отзывы и комментарии</a>
							<div class="ActiveCorner"><div class="ActiveCornerIn"></div></div>
						</div>
						<?php }?>
					</div>
					
						<div class="ProductDetailsContainer">
							
							<?php if(!empty($product['text'])) {?>
							<div id="Description" style="<?php echo $showTab; $showTab = ''; ?>">
								<div class="noscriptTitle">Описание <?php echo mb_strtolower($name)?></div>
								<?php echo $product['text']?>
							</div>
							<?php }?>
							
							<?php if(!empty($types)) {?>
							<div id="Types" style="<?php echo $showTab; $showTab = ''; ?>">
								<div class="noscriptTitle">Характеристики <?php echo mb_strtolower($name)?></div>
								<table>
									<tr>
										<td class="w50 colSmaller colLeft">
										<?php foreach ($types['left'] as $typeGroup) {?>
											<table class="TypesGroup">
												<thead><tr><th colspan="2"><?php echo $typeGroup['name']?></th></tr></thead>
												<tbody>
												<?php foreach ($typeGroup['types'] as $type) {?>
													<tr>
														<td><?php echo $type['name']?> <?php if(!empty($type['desc'])) {?><span><span class="WhatIsThat7"></span></span><?php }?></td>
														<td><div class="rel"><?php echo $type['val']?>
														<?php if(!empty($type['desc'])) {?>
															<div class="TypeHint TypeHintInList">
																<div class="TypeHintCorner"></div>
																<div class="TypeHintName"><?php echo $type['name']?></div>
																<div class="TypeHintText"><?php echo $type['desc']?></div>
																<div class="TypeHintClose"><span class="ajaxLink">Закрыть</span></div>
															</div>
														<?php }?>
														</div></td>
													</tr>
												<?php }?>
												</tbody>
											</table>
										<?php }?>
										</td>
										<td class="w50 colSmaller colRight">
										<?php foreach ($types['right'] as $typeGroup) {?>
											<table class="TypesGroup">
												<thead><tr><th colspan="2"><?php echo $typeGroup['name']?></th></tr></thead>
												<tbody>
												<?php foreach ($typeGroup['types'] as $type) {?>
													<tr>
														<td><?php echo $type['name']?> <?php if(!empty($type['desc'])) {?><span><span class="WhatIsThat7"></span></span><?php }?></td>
														<td><div class="rel"><?php echo $type['val']?>
														<?php if(!empty($type['desc'])) {?>
															<div class="TypeHint TypeHintInList">
																<div class="TypeHintCorner"></div>
																<div class="TypeHintName"><?php echo $type['name']?></div>
																<div class="TypeHintText"><?php echo $type['desc']?></div>
																<div class="TypeHintClose"><span class="ajaxLink">Закрыть</span></div>
															</div>
														<?php }?>
														</div></td>
													</tr>
												<?php }?>
												</tbody>
											</table>
										<?php }?>
										</td>
									</tr>
								</table>
							</div>
							<?php }?>
							
							<?php if(!empty($files)) {?>
							<div id="Docs" style="<?php echo $showTab; $showTab = ''; ?>">
								<div class="noscriptTitle">Файлы и документация <?php echo mb_strtolower($name)?></div>
								<div id="DocsWrap">
									
								<?php foreach ($files as $file) {?>
									<div class="Doc">
										<div class="DocIcon"><a href="<?php echo $file['src']?>"><img src="<?php echo $file['icon']?>" width="48" height="48" alt="<?php echo strtoupper($file['filetype'])?>" /></a></div>
										<div class="DocDetails">
											<div class="DocName"><a href="<?php echo $file['src']?>"><?php echo $file['name']?></a></div>
											<div class="DocType"><?php echo strtoupper($file['filetype'])?> <?php echo bytes_to_str($file['fileinfo']['size'])?></div>
										</div>
									</div>
								<?php }?>
								
								</div>
							</div>
							<?php }?>
							
							<?php if($show_comments) {?>
							<div id="Comments" style="<?php echo $showTab; $showTab = ''; ?>">
								<div class="noscriptTitle">Отзывы и комментарии <?php echo mb_strtolower($name)?></div>
									
								<?php echo comments_block('catalog-'.$product['id']) ?>
							</div>
							<?php }?>
							
						</div>
				</div>
				
			</div>

		</div>



		<div class="clear"></div>
	</div>
	
	

	<?php echo tpl('parts/footer')?>

</body>
</html>