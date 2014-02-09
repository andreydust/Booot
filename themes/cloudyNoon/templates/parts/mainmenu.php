
	<nav class="navbar navbar-inverse navbar-static-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Показать меню</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="collapse navbar-collapse" id="MenuWrap">
          <ul class="nav navbar-nav">
          <? foreach($menu as $parentItem) {
              if(empty($parentItem['sub'])) {?>
                <li class="<?=$parentItem['root']['active']?'active':''?>">
                  <a href="<?=$parentItem['root']['link']?>"><?=$parentItem['root']['name']?></a>
                </li>
              <? } else { ?>
                <li class="dropdown <?=$parentItem['root']['active']?'active':''?>">
                  <a href="<?=$parentItem['root']['link']?>" class="dropdown-toggle" data-toggle="dropdown"><?=$parentItem['root']['name']?><b class="caret"></b></a>
                  <ul class="dropdown-menu">
                  <? foreach ($parentItem['sub'] as $childItem) {?>
                    <li class="<?=$childItem['active']?'active':''?>"><a href="<?=$childItem['link']?>"><?=$childItem['name']?></a></li>
                  <? } ?>
                  </ul>
                </li>
              <? } ?>
          <? } ?>
          </ul>
          <?=tpl('parts/search')?>
        </div>
      </div>
    </nav>