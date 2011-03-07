<?php switch ($format) {
	
	
	
	case 'one':
		if($inbasket) {?>
		
			<div class="Buy" id="productOneId<?php echo $id?>">
				<a class="BuyInBasket" href="<?php echo linkByModule('Basket')?>">в корзине</a>
			</div>
			
		<?php } else {?>
		
			<div class="Buy" id="productOneId<?php echo $id?>">
				<button id="AddBasket">заказать</button>
			</div>
			
		<?php }
	break;
	
	
	
	case 'inlist':
		if($inbasket) {?>
		
			<a class="BuyListInBasket" href="<?php echo linkByModule('Basket')?>">в корзине</a>
			
		<?php } else {?>
		
			<button class="AddBasketList" id="productListId<?php echo $id?>">заказать</button>
			
		<?php }
	break;
	
	
	
}?>