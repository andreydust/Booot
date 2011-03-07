	<div id="header">
		<div id="headerIn">
			<div id="logo">
				<a href="/" title="На главную страницу <?php echo $GLOBALS['config']['site']['title']?>"><img src="<?php echo $sdir?>/images/logo.png" width="227" height="82" alt="<?php echo $GLOBALS['config']['site']['title']?>" /></a>
				<div id="subLogo"><?php block('sub_logo')?></div>
			</div>
			<div id="mainMenu">
				<ul>
					<?php echo giveObject('Content')->MainMenu()?>
				</ul>
			</div>
			<div id="topPhone"><?php block('phone')?></div>
			<div id="callMe"><span class="ajaxLink ajaxLinkGrey">Позвоните мне</span>
				<div id="callMeWindow">
					<div id="callMeWindowName">Позвоните мне</div>
					<div id="callMeWindowClose"></div>
					<div id="callMeWindowForm">
						<label for="callMeWindowFormName">Ваше имя</label>
						<input type="text" name="CallMeName" id="callMeWindowFormName" />
						<label for="callMeWindowFormPhone">Номер телефона</label>
						<input type="text" name="CallMePhone" id="callMeWindowFormPhone" />
						<button id="callMeWindowSend">Отправить</button>
					</div>
				</div>
			</div>
			<div id="BasketBlockWrap"><?php echo giveObject('Basket')->Block()?></div>
			
		</div>
	</div>

	<div id="menu">
		<div id="menuIn">
			<div id="searchBar">
				<div id="searchBarLabel">Каталог товаров</div>
				<div id="searchBarInput">
					<form action="<?php echo linkByModule('Catalog')?>/search" method="get"><div>
						<input type="text" name="string" class="search" id="searchBarInputString" value="<?php echo isset($_GET['string'])?htmlspecialchars($_GET['string']):''?>" />
						<button id="searchBarInputBtn" type="submit">&crarr;</button>
					</div></form>
				</div>
			</div>
			<div id="menuSplitter"></div>
			
			<?php echo giveObject('Catalog')->Menu();?>

		</div>
	</div>