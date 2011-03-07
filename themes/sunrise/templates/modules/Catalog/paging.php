<div id="Paging">
	<div id="PagingPages">
		Страницы:
		<?php for ($i=1; $i<=$pages_count; $i++) {?><a <?php echo ($page_current==$i?'class="Active"':'')?> href="<?php echo getget(array('page'=>$i))?>"><?php echo $i?></a><?php }?>
	</div>
	<div id="PagingShown">Показано <?php echo $products_from?>—<?php echo $products_to?> из <?php echo $products_count?> <?php echo plural($products_count, 'товаров', 'товара', 'товаров')?></div>
	<div class="clear"></div>
</div>