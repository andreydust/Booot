    <footer>
      <div class="container" id="Footer">
        <div class="row">
          <div class="col-md-6 c-footer-block">
            <div id="FooterCopy">&copy; <?=date('Y')?> <?php echo $GLOBALS['config']['site']['title']?></div>
            <div><address id="FooterAddress"><?block('address', false, true)?></address>&nbsp;&mdash;&nbsp;<a href="">на&nbsp;карте</a></div>
            <div>Электронная почта: <a href="mailto:<?=$GLOBALS['config']['site']['admin_mail']?>"><?=$GLOBALS['config']['site']['admin_mail']?></a></div>
            <? $skype = block('skype', true); if(!empty($skype)) { ?>
              <div>Skype: <a href="skype:<? echo $skype ?>?call" title="Позвонить по Skype"><?block('skype', false, true)?></a></div>
            <? } ?>
          </div>
          <div class="col-md-5 c-footer-block">
          <? $phones = explode(',', block('phone', true)); if(!empty($phones)) { ?>
            <div><?if(count($phones) > 1) echo 'Наши телефоны'; else echo 'Наш телефон';?>:</div>
            <div id="FooterPhone"><span class="nowrap"><?=implode(',</span> <span class="nowrap">', $phones)?></span></div>
          <? } ?>
          <? $workingtime = block('workingtime', true); if(!empty($workingtime)) { ?>
            <div>Мы работаем: <?=block('workingtime', false, true)?></div>
          <? } ?>
          </div>
          <div class="col-md-1 c-footer-block">
            <img src="<?=$sdir?>/images/iso-certified.gif" alt="ISO 9000:2001 Certified" title="ISO 9000:2001 Certified" class="img-responsive">
          </div>
        </div>

        <?php echo giveObject('Content')->FooterMenu()?>

      </div>
    </footer>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="<?=$sdir?>/js/bootstrap.min.js"></script>
    <script src="<?=$sdir?>/js/interface.js"></script>