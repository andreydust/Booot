<?php if(!empty($products)) {
$link = linkByModule('Catalog').'/compare';
?>
<div id="CompareBlock">
	<h3><a href="<?php echo $link?>">Сравнить</a> (<?php echo $count?>)</h3>
	
	<?php foreach ($products as $topic_id=>$products) {?>
	<div class="CompareTopic"><a href="<?php echo $topics[$topic_id]['link']?>"><?php echo $topics[$topic_id]['name']?></a></div>
	<div class="littleProducts">
		<?php foreach ($products as $product) {?>
		<?php $img = img()->GetMainImage('Catalog', $product['id']);?>
		<div class="littleProduct">
			<?php if(is_file(DIR.$img['src'])) {?>
			<div class="lpImage">
				<a href="<?php echo $product['link']?>"><img src="<?php echo image($img['src'], 45, 45)?>" alt="<?php echo $product['brand']?$product['brand']['name'].' ':''?> <?php echo $product['name']?>" /></a>
			</div>
			<?php } else {?>
			<div class="lpImageEmpty"></div>
			<?php }?>
			<div class="lpDesc">
				<div class="lpName"><a href="<?php echo $product['link']?>"><?php echo $product['brand']?$product['brand']['name'].' ':''?><?php echo $product['name']?></a></div>
				<div class="lpPrice">
					<?php if($product['price'] > 0) {?>
					<span><?php echo number_format($product['price'], 0, '', ' ')?> Р.</span>
					<?php }?>
					<a class="ajaxLink ajaxCompareRemove" id="compareRemove<?php echo $product['id']?>" href="<?php echo linkByModule('Catalog')?>/compare/del/<?php echo $product['id']?>">убрать</a>
				</div>
			</div>
		</div>
		<?php }?>
	</div>
	<?php }?>

	<div>
		<form action="<?php echo $link?>" method="get">
			<button id="compareShow">Сравнить</button>
			<a href="<?php echo $link?>/clean" id="compareClear" class="ajaxLink">очистить список</a>
		</form>
	</div>
</div>
<?php }?>