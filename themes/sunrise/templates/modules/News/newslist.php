<?php foreach($news as $n) {?>
<div class="NewsItem">
	<p class="date"><?php echo $n['date']?></p>
	<p class="news"><a href="<?php echo $n['link']?>" class="newslink"><?php echo $n['name']?></a><br />
	<?php echo $n['anons']?></p>
</div>
<?php }?>