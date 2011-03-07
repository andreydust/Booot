<?php if(empty($news)) return ;?>
<div id="NewsBlock">
	<h2>Новости <a href="<?php echo linkByModule('News')?>?rss" title="RSS подписка на новости"><img src="<?php echo $sdir?>/images/feed.png" width="24" height="24" alt="RSS" style="vertical-align: middle;" /></a></h2>
	<div class="NewsBlock">
	<?php foreach ($news as $i) { ?>
		<div class="OneNewBlock">
			<a href="<?php echo $i['link']?>"><?php echo $i['name']?></a>
			<p class="newsText"><?php echo $i['text']?></p>
			<div class="Date"><?php echo $i['date']?></div>
		</div>
	<?php } ?>

		<div class="NewsLink"><a href="<?php echo linkByModule('News')?>">Все новости</a></div>
	</div>
</div>