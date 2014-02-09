		<ul class="list-inline" id="FooterMenu">
		<? foreach($menu as $item) {?>
			<li><a href="<?=$item['root']['link']?>"><?=$item['root']['name']?></a></li>
		<? } ?>
		</ul>