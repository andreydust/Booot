<div style="overflow: hidden;">
<?php foreach ($mediaTypes as $type=>$name_exts) {?>
	<div style="float: left; width:300px; margin:1em 1em 0 0; padding: 0.3em 1em 1em; font-size:0.9em; border:1px solid white;" class="mediaBlock">
		<div style="font-size: 1.5em; color:#555; margin:0 0 0.5em 0;"><?php echo $name_exts['name']?> (<?php echo implode(',',$name_exts['exts'])?>)</div>
	<?php if(isset($files[$type])) {?>
		<ul style="list-style: none; margin:0; padding:0;" class="sortableFiles">
		<?php foreach ($files[$type] as $file) {?>
			<li style="margin:0 0 1em 0; padding:0; cursor:move;" id="mediaFile<?php echo $file['id']?>">
				<a href="<?php echo $file['src']?>" target="_blank"><?php echo $file['name']?> (<?php echo $file['filetype']?>)</a>
				<a title="Удалить" class="imageDelImage" href="<?php echo $link?>&id=<?php echo $id?>&del=<?php echo $file['id']?>" target="_blank">
					<img src="/admin/images/icons/cross.png" style="float:right; margin:2px 5px 0 0;">
				</a>
				<?php switch ($type) {
				case 'video':
				?>
				<div style="color:#aaa;">
					Длина: <?php echo $file['fileinfo']['info']['duration']['raw']?>,
					Разрешение: <?php echo $file['fileinfo']['info']['width']?>x<?php echo $file['fileinfo']['info']['height']?>,
					Размер: <?php echo $file['fileinfo']['sizeStr']?>
				</div>
				<?php
				break;
					
				case 'audio':
				?>
				<div style="color:#aaa;">
					Длина: <?php echo $file['fileinfo']['info']['duration']['raw']?>,
					Битрейт: <?php echo $file['fileinfo']['info']['audioBitrate']?>,
					Размер: <?php echo $file['fileinfo']['sizeStr']?>
				</div>
				<?php
				break;
					
				case 'doc':
				?>
				<div style="color:#aaa;">
					Размер: <?php echo $file['fileinfo']['sizeStr']?>
				</div>
				<?php
				break;
				}?>
				
			</li>
		<?php }?>
		</ul>
	<?php }?>
	</div>
<?php }?>
</div>

<form action="<?php echo $link?>" method="post" enctype="multipart/form-data" style="overflow: hidden;" id="MediaFileUploadForm">
	<input type="hidden" name="id" value="<?php echo $id?>">
	<input type="file" name="file">
</form>

<script type="text/javascript">

//Рамка блоков
$('.mediaBlock').hover(function(){
	$(this).css('border-color','#ddd');
},function(){
	$(this).css('border-color','transparent');
});

//Удаление
$('.imageDelImage').click(function(){
	if(confirm('Удалить файл?')) {
		var url = $(this).attr('href');
		$.ajax({
			"url": url,
			success: function(html){
				$('#MediaFileUploadForm').parent().html(html);
			}
		});
	}
	return false;
});

$(".sortableFiles").sortable({
	distance: 5,
	update: function() {
		var sorting = {};
		$(this).find('li').each(function(i,e){
			sorting[$(e).attr('id').substr(9)] = i;
		});
		$.ajax({
			url: '<?php echo $link?>&id=<?php echo $id?>&sorting',
			type: "POST",
			data: {"sorting": sorting},
			success: function(html){
				//$('#MediaFileUploadForm').parent().html(html);
			}
		});
		console.log(sorting);
	}
});
$(".sortableFiles").disableSelection();

</script>