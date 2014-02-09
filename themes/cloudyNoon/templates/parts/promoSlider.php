<aside id="PromoCarousel">
	<div class="container">
		<div id="carousel">
			<div id="carousel-generic" class="carousel slide" data-ride="carousel">
				<ol class="carousel-indicators">
					<?
					$slidesCount = count($slides);
					for($i=0; $i<$slidesCount; $i++) {
						?>
						<li data-target="#carousel-generic" data-slide-to="<?=$i?>" class="<?=$i==0?'active':''?>"></li>
						<?
					}
					?>
				</ol>

				<div class="carousel-inner">

				<?
				foreach ($slides as $slide) {
					?>
					<div class="item <?if(!isset($iNeverSeeYouBefore)) { echo 'active'; $iNeverSeeYouBefore=1; }?>">
						<img src="<?=image($slide['image'], 500, 250)?>" alt="<?=$slide['title']?>">
						<div class="carousel-caption">
							<h3><?=$slide['title']?></h3>
							<p><?=$slide['description']?></p>
							<? if(!empty($slide['button_name'])) {?>
								<button 
									type="button" 
									<? if(!empty($slide['link'])) {?>
									onclick="document.location='<?=$slide['link']?>'" 
									<?}?>
									class="btn btn-default c-btn-large">
									<?=$slide['button_name']?>
								</button>
							<?}?>
						</div>
					</div>
					<?
				}
				?>

				</div>
			</div>
		</div>
	</div>
</aside>