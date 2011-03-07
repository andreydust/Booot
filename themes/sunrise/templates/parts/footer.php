	<div id="footer">
		<div id="footerIn">
			<hr />
			<div id="footerLogo"><a href="/" title="На главную страницу <?php echo $GLOBALS['config']['site']['title']?>"><img src="<?php echo $sdir?>/images/footerLogo.png" alt="<?php echo $GLOBALS['config']['site']['title']?>" /></a></div>
			<div id="footerAddress"><address><?php block('address')?></address></div>
			<div id="footerPhone"><?php block('phone')?></div>
			<div id="footerWriteMe"><a href="/feedback">Напишите нам</a></div>
			<div id="footerMenu">
				<ul>
					<?php echo giveObject('Content')->MainMenu()?>
				</ul>
			</div>
			<div id="footerShopDesc"><?php block('about_footer')?></div>
			<div id="footerCopyRights"><?php block('details_footer')?></div>
			<div id="DevelopersDevelopersDevelopers">
				<div id="weboutsource"><a href="http://weboutsource.ru/create-and-sell-ishop" title="Создание и продажа интернет-магазинов"><img src="<?php echo $sdir?>/images/weboutsource.png" alt="WebOutsource.ru" /></a></div>
			</div>
		</div>
	</div>