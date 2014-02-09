<div class="row paging">
	<div class="col-md-6">
		<div class="row">
			<div class="col-xs-9 col-xs-offset-3">
				<ul class="list-inline">
					<?
					for ($i=1; $i<=$pages_count; $i++) {
						if($page_current==$i) {?>
							<li class="lead"><?=$i?></li>
						<?} else {?>
							<li class="lead"><a href="<?=$topicLink?><?=$i==1?getget(array('page'=>false)):getget(array('page'=>$i))?>"><?=$i?></a></li>
						<?
						}
					}
					?>
				</ul>
			</div>
		</div>
	</div>
</div>