    <?php echo giveObject('Content')->MainMenu()?>

    <header class="container">
      <div class="row" itemscope itemtype="http://schema.org/LocalBusiness">
        <div class="col-md-3 col-sm-3 col-xs-3" id="Logo">
          <?if(!empty($GLOBALS['path'])){?> <a href="/"> <?}?>
            <img src="<?=$sdir?>/images/logo.png" class="img-responsive" alt="<?php echo $GLOBALS['config']['site']['title']?>" itemprop="logo">
          <?if(!empty($GLOBALS['path'])){?> </a> <?}?>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-9" id="Address">
          <div id="AddressName"><span itemprop="name"><?php echo $GLOBALS['config']['site']['title']?></span> &mdash; <?block('sub_logo', false, true)?></div>
          <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
            <address id="AddressSubCaption">
              <?block('cloudynoon_address_header', false, true)?>
            </address>
          </div>
          <div id="AddressMapLink"><a href="">Посмотреть на карте</a></div>
          <meta itemprop="email" content="<?=$GLOBALS['config']['site']['admin_mail']?>">
        </div>
        <div class="clearfix visible-xs"></div>
        <div class="col-md-4 col-sm-4 col-xs-offset-3 col-sm-offset-0" id="Phone">
          <div id="PhoneName">
          <? $phones = explode(',', block('phone', true)); if(!empty($phones)) { ?>
            <? $c=0 ;foreach($phones as $phone) { $c++; 
              if($c < 3) {?>
                <span class="nowrap" itemprop="telephone"><?=trim($phone)?></span><?=$c==1?',':''?>
              <? } else { ?>
                <meta itemprop="telephone" content="+7 (343) 385-98-97">
              <? } ?>
            <? } ?>
          <? } ?>
          </div>
          <div id="PhoneWorkingTime">
          <? $workingtime = block('workingtime', true); if(!empty($workingtime)) { ?>
            <div class="visible-lg fleft" id="WorkingTimeWeekLabel">Мы работаем: </div>
            <div class="fleft">
              <meta itemprop="openingHours" content="<?=block('workingtime_schema', true)?>"><?=block('workingtime', false, true)?>
            </div>
          <? } ?>
          </div>
          <div id="PhoneCallback"><!--<a href="">Заказать обратный звонок</a>--></div>
        </div>
      </div>
    </header>