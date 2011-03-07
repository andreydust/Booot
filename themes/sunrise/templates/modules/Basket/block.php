<?php if($count==0) {?>
	<div id="Basket"><div id="BasketTitle">Корзина пуста</div></div>
<?php } else {?>
	<div id="Basket">
		<div id="BasketTitle"><a href="<?php echo linkByModule('Basket')?>">Корзина</a></div>
		<div id="BasketIn"><?php echo $count?> <?php echo plural($count, 'товаров', 'товар', 'товара')?> на сумму <?php echo number_format($summ, 0, '', ' ')?> <?php echo plural($summ, 'рублей', 'рубль', 'рубля')?></div>
	</div>
<?php }?>