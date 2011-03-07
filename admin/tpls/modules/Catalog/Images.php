<div style="overflow:hidden; margin:5px 0;">
<?php foreach ($images as $i) { ?>
	<div style="width:100px; height:100px; float:left; margin:15px 10px 0 15px; position:relative;" class="imagesImage">
	<?php if($i['main'] == 'Y') {?>
		<img src="/admin/images/icons/star.png" style="position:absolute; top:-12px; left:-8px; z-index:2" alt="Картинка по-умолчанию">
	<?php }?>
		<a style="position:absolute; top:0; left:0; z-index:1" href="<?php echo $i['src']?>" target="_blank"><img src="<?php echo image($i['src'], 100, 100)?>"></a>
		<div style="width:100px; height:20px; position:absolute; left:0; bottom:0; z-index:4; overflow: hidden; display:none;" class="imagesIconsPanel">
			<a title="Сделать изображением по-умолчанию" class="imageMakeStar" href="<?php echo $link?>&id=<?php echo $module_id?>&star=<?php echo $i['id']?>" target="_blank">
				<img src="/admin/images/icons/star-mini.png" style="float: left; margin:2px 0 0 5px;">
			</a>
			<a title="Увеличить" class="imageMagnify" href="<?php echo $i['src']?>" target="_blank">
				<img src="/admin/images/icons/magnifier-left.png" style="float: left; margin:2px 0 0 8px;">
			</a>
			<a title="Удалить" class="imageDelImage" href="<?php echo $link?>&id=<?php echo $module_id?>&del=<?php echo $i['id']?>" target="_blank">
				<img src="/admin/images/icons/cross.png" style="float:right; margin:2px 5px 0 0;">
			</a>
		</div>
		<div style="width:100px; height:20px; position:absolute; left:0; bottom:0; background-color:white; opacity: 0.5; z-index: 3; display:none;" class="imagesIconsPanelBack"></div>
	</div>
<?php }?>
</div>

<form action="<?php echo $link?>" method="post" enctype="multipart/form-data" id="ImagesCatalogForm">
	<input type="hidden" name="id" value="<?php echo $module_id?>">
	<input type="file" name="image" id="UploadCatalogImage">
</form>

<script type="text/javascript">

	//Сделать по-умолчанию
	$('.imageMakeStar').click(function(){
		var url = $(this).attr('href');
		$.ajax({
			"url": url,
			success: function(html){
				$('#ImagesCatalogForm').parent().html(html);
			}
		});
		return false;
	});
	//Увеличение
	$('.imageMagnify').click(function(){
		window.open( $(this).attr('href') );
		return false;
	});
	//Удаление
	$('.imageDelImage').click(function(){
		if(confirm('Удалить изображение?')) {
			var url = $(this).attr('href');
			$.ajax({
				"url": url,
				success: function(html){
					$('#ImagesCatalogForm').parent().html(html);
				}
			});
		}
		return false;
	});
	//Показ панели в картинке
	$('.imagesImage').hover(function(){
			$(this).find('.imagesIconsPanel,.imagesIconsPanelBack').show();
		}, function(){
			$(this).find('.imagesIconsPanel,.imagesIconsPanelBack').hide();
		}
	);
	//АвтоЗагрузка
	$('#UploadCatalogImage').change(function(){
		$(this).parent().ajaxSubmit({
			target:        '#ui-tabs-'+currentTabId
		});
	});

</script>