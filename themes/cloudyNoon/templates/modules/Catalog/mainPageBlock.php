	<section class="container">
		<div id="MainPageContent">
			<div class="row">


				<div class="col-md-6">
					<?
					//Левая колонка
					$leftOrRight = 0;
					foreach ($topicsByTop[0] as $top => $topic) {
						if(++$leftOrRight%2) {
							echo tpl('modules/Catalog/mainPageItem', array(
								'topic'				=> $topic,
								'topImages'			=> $topImages,
								'top'				=> $top,
								'productsCounts'	=> $productsCounts,
								'topicsByTop'		=> $topicsByTop,
								'productsByRoot'	=> $productsByRoot
							));
						}
					}
					$leftOrRight = 0;
					?>
				</div>

				<div class="col-md-6">
					<?
					//Правая колонка
					$leftOrRight = 0;
					foreach ($topicsByTop[0] as $top => $topic) {
						if(++$leftOrRight%2==0) {
							echo tpl('modules/Catalog/mainPageItem', array(
								'topic'				=> $topic,
								'topImages'			=> $topImages,
								'top'				=> $top,
								'productsCounts'	=> $productsCounts,
								'topicsByTop'		=> $topicsByTop,
								'productsByRoot'	=> $productsByRoot
							));
						}
					}
					$leftOrRight = 0;
					?>

					<?=block('main_text')?>
				</div>

			</div>
		</div>
	</section>