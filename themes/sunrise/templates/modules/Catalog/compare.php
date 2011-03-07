<div class="CompareModPage">
	
	<?php if(count($topics) > 1) {?>
	<div id="CompareTopics">
		<?php foreach ($topics as $topic) {?>
			<a href="#topic<?php echo $topic['id']?>" class="ajaxLink"><?php echo $topic['name']?></a>
		<?php }?>
	</div>
	<?php }?>
	
	<?php $ti=0; $tl=count($topics); foreach ($topics as $topic) {?>
	<table class="TypesGroup">
		<thead>
			<tr>
				<th class="CompareModPageColumnTitle">
					<h2 id="topic<?php echo $topic['id']?>" class="CompareModPageTitle"><?php echo $topic['name']?></h2>
				</th>
				<?php foreach ($products[$topic['id']] as $product) {?>
				<?php $img = img()->GetMainImage('Catalog', $product['id']);?>
				<th class="CompareModPageColumn">
					<?php if(is_file(DIR.$img['src'])) {?>
					<div class="ProductImageModPage ProductImage">
						<a href="<?php echo $img['src']?>" class="lightview">
							<img src="<?php echo image($img['src'], 85, 120)?>" alt="<?php echo $product['name']?><?php echo $product['brand']?' '.$product['brand']['name']:''?>" />
						</a>
					</div>
					<?php }?>
				</th>
				<?php }?>
			</tr>
			<tr>
				<th></th>
				<?php foreach ($products[$topic['id']] as $product) {?>
				<th>
					<div class="ProductTitleModPage">
						<a href="<?php echo $product['link']?>"><?php echo $product['name']?><?php echo $product['brand']?' '.$product['brand']['name']:''?></a>
					</div>
				</th>
				<?php }?>
			</tr>
			<tr>
				<th></th>
				<?php foreach ($products[$topic['id']] as $product) {?>
				<th>
					<div class="ProductRemoveModPage">
						<a class="ajaxLink ajaxCompare" id="compareRemove<?php echo $product['id']?>" href="<?php echo linkByModule('Catalog')?>/compare/del/<?php echo $product['id']?>">убрать</a>
					</div>
				</th>
				<?php }?>
			</tr>
			<tr>
				<th>Цена</th>
				<?php foreach ($products[$topic['id']] as $product) {?>
				<th>
					<div class="ProductPriceModPage">
						<div class="ProductPrice"><?php echo number_format($product['price'], 0, '', ' ')?> Р.</div>
						<?php if($product['price'] < $product['priceOld']) {?>
						<div class="ProductPriceOld">
							<?php echo number_format($product['priceOld'], 0, '', ' ')?> Р.
						</div>
						<?php }?>
					</div>
				</th>
				<?php }?>
			</tr>
		</thead>
		<tbody>
			
		<?php if(is_array($topic['types'])) foreach ($topic['types'] as $groupKey=>$types) {?>
			<?php foreach ($types['types'] as $typeKey=>$type) {?>
			<tr class="<?php echo $type['equal']?'CompareEqual':''?>">
				<td>
					<?php echo $type['name']?> <?php if(!empty($type['desc'])) {?><span><span class="WhatIsThat7"></span></span><?php }?>
					
					<?php if(!empty($type['desc']) && !isset($checkFirst)) { $checkFirst = true;?>
					<div class="TypeHintCompareWrap">
						<div class="TypeHint TypeHintInList">
							<div class="TypeHintCorner"></div>
							<div class="TypeHintName"><?php echo $type['name']?></div>
							<div class="TypeHintText"><?php echo $type['desc']?></div>
							<div class="TypeHintClose"><span class="ajaxLink">Закрыть</span></div>
						</div>
					</div>
					<?php }?>
					
				</td>
				
				<?php foreach ($products[$topic['id']] as $product) {?>
				<td>
					<div class="rel"><?php echo isset($product['rtypes'][$groupKey]['types'][$typeKey]['val'])?$product['rtypes'][$groupKey]['types'][$typeKey]['val']:'?'?>
						
					</div>
				</td>
				<?php } unset($checkFirst);?>
			</tr>
			<?php }?>
		<?php }?>
			
		</tbody>
	</table>
	
	<?php if(++$ti != $tl) {?>
		<hr />
	<?php }?>
	
	<?php }?>
	
</div>