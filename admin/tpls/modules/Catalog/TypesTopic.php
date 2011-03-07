<?php
$typetype = array('text'=>'Текстовый','float'=>'Числовой','yn'=>'Есть/Нет/Не задано','select'=>'Селект','range'=>'Диапазон');
?>
<style type="text/css">
.typesGroups { padding:5px; }
.typesGroup { display:inline-block; vertical-align:top; width:300px; margin:1em 1em 0 0; padding: .3em 0 1em 0; font-size:0.9em; border:1px solid white; background:#fff; }
.typesGroup input, .typesGroup select { width: 99%; margin-bottom: 0.5em; }
.typesGroup textarea { width: 95%; height: 50px !important; }
.typesGroup:hover { border-color: #ddd; -webkit-box-shadow: 0px 0px 5px #bdbdbd;-moz-box-shadow: 0px 0px 5px #bdbdbd;box-shadow: 0px 0px 5px #bdbdbd; }
.typesGroupTitle { font-size: 1.5em; color:#555; margin:0 0 .5em .3em; }
#addTypesGroup { display: inline; }
#addTypesGroupBlock { margin:0 0 0 1em; }
.types { padding:0 0 0 0; margin:0; margin:0 0 0.5em 0; }
.types li {
	padding:.3em 0 0 1.2em;
	margin:0 0 0 0;
	border-bottom:solid 1px #ddd;
	display:block;
	background:url("/admin/images/icons/arrow-000-small.png") no-repeat left 4px;
}
.types li:hover { background-color: #eee; }
.addTypeForm { display: none; margin:0.5em 0 0 0; padding:.5em; }
.editTypeForm { display: none; margin-left:-.5em; padding-right:.5em; }
.editTypeForm label, .addTypeLabel { margin-left: .5em; }
.additional { display:none; }
.selectExample { display:block; }
.selectExampleCode { display: none; }
.selectAddField textarea { height:60px !important; }
.typesGroupSubmit { overflow: hidden; }
.typesGroupSubmit .submitTypeBtn { width:90px; float: right; margin-top: 1em }
.addType { margin:0 0 0 .3em; }
.editType { margin:0 0 0 1em; }
.editImage { margin:0 0 0 1em; }
.editImageDialog { display:none; }
.delType { margin:0 0 0 1em; }
.typeName { float:left; width:15em; }
.typeControls { float:right; width:7.6em; }
.typesGroupMain { float:left; width:15em; white-space: nowrap; margin:0.9em 0 0 0; }
.typesGroupMain label { display: inline !important; margin-left: 0 !important; }
.typesGroupMain input { display: inline !important; width:auto !important; vertical-align: middle; }
.editTypeGroupInput { width:75% !important; display:inline !important; }
.editTypeGroupForm { display: none; }
</style>

<div id="addTypesGroupBlock">
	<input type="text" id="addTypesGroup" class="ui-widget-content ui-corner-all">
	<a href="#addTypesGroup" class="add_link" id="addTypesGroupGo">Добавить группу характеристик</a>
</div>

<div class="typesGroups">

<?php foreach($types as $gid=>$g) {?>
	<div class="typesGroup" id="group_<?php echo $gid?>">
		<div class="typesGroupTitle">
			<span class="typeGroupName"><?php echo $g['name']?></span>
			<form method="post" action="<?php echo $link?>" class="editTypeGroupForm">
				<input type="hidden" name="editTypeGroup" value="Y">
				<input type="hidden" name="id" value="<?php echo $id?>">
				<input type="hidden" name="groupTypeId" value="<?php echo $gid?>">
				<input class="editTypeGroupInput" type="text" value="<?php echo $g['name']?>" name="name">
			</form>
			<a class="editTypeGroup" href="#" title="Редактировать"><img src="/admin/images/icons/pencil.png" alt="Редактировать"></a>
			<a class="delTypeGroup" href="gid=<?php echo $gid?>&act=delGroup" title="Удалить"><img src="/admin/images/icons/cross.png" alt="Удалить"></a>
		</div>
		
		<ul class="types">
		<?php foreach($g['types'] as $tid=>$t) {?>
			<li id="type_<?php echo $tid?>">
				<div class="typeName" style="<?php echo $t['main']?'font-weight:bold;':''?>">
					<?php echo $t['name']?> (<?php echo $typetype[$t['type']]?>)
				</div>
				<div class="typeControls">
					<a class="editType" href="#" title="Редактировать"><img src="/admin/images/icons/pencil.png" alt="Редактировать"></a>
					<a class="editImage" href="#editImageDialog<?php echo $gid?>_<?php echo $tid?>" title="Добавить картинку"><img src="/admin/images/icons/image-balloon.png" alt="Редактировать"></a>
				<!-- Форма картинок для селекта -->
				<?php if($t['type'] == 'select') {?>
					<div class="editImageDialog" id="editImageDialog<?php echo $gid?>_<?php echo $tid?>" title="Редактирование картинки характеристики">
						<form action="<?php echo $link?>&editImage" method="post" enctype="multipart/form-data">
							<input type="hidden" name="id" value="<?php echo $id?>">
							<input type="hidden" name="gid" value="<?php echo $gid?>">
							<input type="hidden" name="tid" value="<?php echo $tid?>">
						<?php foreach ($t['select'] as $selid=>$sel) {?>
							<div style="clear:both; height:90px;">
								<?php if(isset($images[$gid][$tid][$selid]['src'])) {?><img alt="" src="<?php echo $images[$gid][$tid][$selid]['src']?>" style="float:left; max-width: 80px; max-height: 80px;"><?php }?>
								<div style="float:right; margin-left:1em;">
									<label for="typeImageSelectName<?php echo $gid?>_<?php echo $tid?>_<?php echo $selid?>"><?php echo $sel?></label>
									<input type="file" name="image[<?php echo $selid?>]" id="typeImageSelectName<?php echo $gid?>_<?php echo $tid?>_<?php echo $selid?>">
									<div><input type="checkbox" name="del<?php echo $selid?>" style="display: inline" id="typeImageDelCheck<?php echo $gid?>_<?php echo $tid?>_<?php echo $selid?>"> <label for="typeImageDelCheck<?php echo $gid?>_<?php echo $tid?>_<?php echo $selid?>">Удалить текущую картинку</label></div>
								</div>
							</div>
						<?php }?>
							<div style="overflow:hidden; padding: 2em 1em 1em 0; clear:both;"><input style="float:right;" type="submit" value="Сохранить"></div>
						</form>
					</div>
				<!-- Форма картинок для остальных характеристик -->
				<?php } else {?>
					<div class="editImageDialog" id="editImageDialog<?php echo $gid?>_<?php echo $tid?>" title="Редактирование картинки характеристики">
						<form action="<?php echo $link?>&editImage" method="post" enctype="multipart/form-data">
							<?php if(isset($images[$gid][$tid]['src'])) {?><img alt="" src="<?php echo $images[$gid][$tid]['src']?>" style="float:left; max-width: 80px"><?php }?>
							<div style="float:right; margin-left:1em;">
								<input type="hidden" name="id" value="<?php echo $id?>">
								<input type="hidden" name="gid" value="<?php echo $gid?>">
								<input type="hidden" name="tid" value="<?php echo $tid?>">
								<input type="file" name="image">
								<div><input type="checkbox" name="del" style="display: inline" id="typeImageDelCheck<?php echo $gid?>_<?php echo $tid?>"> <label for="typeImageDelCheck<?php echo $gid?>_<?php echo $tid?>">Удалить текущую картинку</label></div>
								<div style="overflow:hidden; padding: 2em 1em 1em 0;"><input style="float:right;" type="submit" value="Сохранить"></div>
							</div>
						</form>
					</div>
				<?php }?>
					<a class="delType" href="gid=<?php echo $gid?>&tid=<?php echo $tid?>&act=del" title="Удалить"><img src="/admin/images/icons/cross.png" alt="Удалить"></a>
				</div>
				<div style="clear:both"></div>
				
				<form method="post" action="<?php echo $link?>" class="editTypeForm">
					<input type="hidden" name="id" value="<?php echo $id?>">
					<input type="hidden" name="groupTypeId" value="<?php echo $gid?>">
					<input type="hidden" name="typeId" value="<?php echo $tid?>">
					<input type="hidden" name="act" value="editType">
					
					<label for="typesGroupName<?php echo $gid?>_<?php echo $tid?>">Наименование</label>
					<input type="text" id="typesGroupName<?php echo $gid?>_<?php echo $tid?>" name="name" value="<?php echo $t['name']?>">
					
					<label for="typesGroupText<?php echo $gid?>_<?php echo $tid?>">Описание</label>
					<textarea id="typesGroupText<?php echo $gid?>_<?php echo $tid?>" name="desc"><?php echo $t['desc']?></textarea>
					
					<label for="typesGroupType<?php echo $gid?>_<?php echo $tid?>">Тип</label>
					<select id="typesGroupType<?php echo $gid?>_<?php echo $tid?>" class="typesGroupType" name="type">
					<?php foreach ($typetype as $ttl=>$ttn) {
						if($ttl == $t['type']) $selected = 'selected="selected"';
						else $selected = ''; ?>
						<option value="<?php echo $ttl?>" <?php echo $selected?>><?php echo $ttn?></option>
					<?php }?>
					</select>
					
					<div class="floatAddField additional">
						<label for="floatAddField<?php echo $gid?>_<?php echo $tid?>">Единицы измерения</label>
						<input type="text" id="floatAddField<?php echo $gid?>_<?php echo $tid?>" name="unit" value="<?php echo $t['unit']?>">
					</div>
					
					<div class="rangeAddField additional">
						<label for="rangeAddField<?php echo $gid?>_<?php echo $tid?>">Единицы измерения</label>
						<input type="text" id="rangeAddField<?php echo $gid?>_<?php echo $tid?>" name="unitRange" value="<?php echo $t['unit']?>">
					</div>
					
					<div class="selectAddField additional">
						<label for="selectAddField<?php echo $gid?>_<?php echo $tid?>">Варианты</label>
						<textarea id="selectAddField<?php echo $gid?>_<?php echo $tid?>" name="select"><?php foreach ($t['select'] as $sid=>$s) {
							?><?php echo $sid?>=<?php echo $s?>;<?php echo "\r\n";
						}?></textarea>
					</div>
					
					<div class="typesGroupSubmit">
						<div class="typesGroupMain">
						<?php if($t['main']) $main_checked = 'checked="checked"'; else $main_checked = '';?>
							<input type="checkbox" value="Y" name="main" id="typesGroupMain<?php echo $gid?>_<?php echo $tid?>" <?php echo $main_checked?>> <label for="typesGroupMain<?php echo $gid?>_<?php echo $tid?>">Главная характеристика</label>
						</div>
						<input type="submit" value="Сохранить" class="submitTypeBtn">
					</div>
				</form>
				<div style="clear:both"></div>
				
			</li>
		<?php }?>
		<!--
			<li>
				<div style="float:left; width:17em;">
					Поддерживаемые операционные системы (Селект)
				</div>
				<div style="float:right; width:5em;">
					<a class="editType" href="#" title="Редактировать"><img src="/admin/images/icons/pencil.png" alt="Редактировать"></a>
					<a class="delType" href="#" title="Удалить"><img src="/admin/images/icons/cross.png" alt="Удалить"></a>
				</div>
				<div style="clear:both"></div>
			</li>
			<li>Системные требования (Текст)</li>
		-->
		</ul>
		
		<a class="add_link addType" href="#addType">Добавить характеристику</a>
		<form method="post" action="<?php echo $link?>" class="addTypeForm">
			<input type="hidden" name="id" value="<?php echo $id?>">
			<input type="hidden" name="groupTypeId" value="<?php echo $gid?>">
			<input type="hidden" name="act" value="addType">
			
			<label class="addTypeLabel" for="typesGroupName<?php echo $gid?>">Наименование</label>
			<input type="text" id="typesGroupName<?php echo $gid?>" name="name">
			
			<label class="addTypeLabel" for="typesGroupText<?php echo $gid?>">Описание</label>
			<textarea id="typesGroupText<?php echo $gid?>" name="desc"></textarea>
			
			<label class="addTypeLabel" for="typesGroupType<?php echo $gid?>">Тип</label>
			<select id="typesGroupType<?php echo $gid?>" class="typesGroupType" name="type">
					<?php foreach ($typetype as $ttl=>$ttn) { ?>
						<option value="<?php echo $ttl?>"><?php echo $ttn?></option>
					<?php }?>
					</select>
			
			<div class="floatAddField additional">
				<label class="addTypeLabel" for="floatAddField<?php echo $gid?>">Единицы измерения</label>
				<input type="text" id="floatAddField<?php echo $gid?>" name="unit">
			</div>
			
			<div class="rangeAddField additional">
				<label class="addTypeLabel" for="rangeAddField<?php echo $gid?>">Единицы измерения</label>
				<input type="text" id="rangeAddField<?php echo $gid?>" name="unitRange">
			</div>
			
			<div class="selectAddField additional">
				<label class="addTypeLabel" for="selectAddField<?php echo $gid?>">Варианты</label>
				<textarea id="selectAddField<?php echo $gid?>" name="select"></textarea>
				
				<a class="selectExample" href="#">Пример</a>
				<code class="selectExampleCode">1=одноблочная система;<br>2=компонентная система;<br>3=двухблочная система;</code>
			</div>
			
			<div class="typesGroupSubmit">
				<div class="typesGroupMain">
					<input type="checkbox" value="Y" name="main" id="typesGroupMain<?php echo $gid?>"> <label for="typesGroupMain<?php echo $gid?>">Главная характеристика</label>
				</div>
				<input type="submit" value="Добавить" class="submitTypeBtn">
			</div>
		</form>
		
	</div>
<?php }?>

</div>

<script type="text/javascript">

var id = <?php echo $id?>;

//$('.typesGroups img').load(function(){
	
	$('.addType').click(function(){
		var $form = $(this).next();
		$form.toggle();
		$form.find('input[type=text]:first').focus();
		return false;
	});
	
	$('.typesGroupType').change(function(){
		$(this).parent().find('.additional').hide();
		$(this).parent().find('.'+$(this).val()+'AddField').show();
	});
	$('.typesGroupType').trigger('change');
	
	$('.editType').click(function(){
		//Форма
		var $form = $(this).parent().next().next();
		$form.toggle();
		//Первый инпут формы
		$form.find('input[type=text]:first').focus();
		return false;
	});
	
	//Картинка для характеристики
	var typesImagesDialogs = [];
	var typesImagesDialogsStr = '';
	$('.editImageDialog').each(function(){
		typesImagesDialogs.push('#'+$(this).attr('id'));
	});
	typesImagesDialogsStr = typesImagesDialogs.join(',');
	$(typesImagesDialogsStr).dialog('destroy');
	$('.ui-dialog[aria-labelledby!=ui-dialog-title-products_topics_dialog]').remove();
	$('body > .editImageDialog').remove();
	
	$('.editImageDialog').dialog({
		width: 420,
		modal: true,
		autoOpen: false
	});
	var typesImagesOnce = true;
	if(typesImagesOnce) {
		typesImagesOnce = false;
		$('.editImage').live('click', function(){
			var imgDialog = $($(this).attr('href'));
			imgDialog
				.dialog("open")
				.find('form')
					.ajaxForm({
						success: function(html) {
							$('#ui-tabs-'+currentTabId).parent().tabs('load', currentTabId);
						}
					});
			return false;
		});
	}
	
	$('.selectExample').click(function(){
		$(this).hide().next().show();
		return false;
	});
	
	//Добавление группы
	$('#addTypesGroupGo').click(function(){
		var val = $('#addTypesGroup').val();
		if(val=='') {
			alert('Заполните имя группы');
			$('#addTypesGroup').focus();
			return false;
		}
		var brk = false;
		$('.typesGroupTitle').each(function(){
			if($(this).text().trim() == val) {
				brk = true;
				return false;
			}
		});
		if(brk) {
			alert('Такая группа уже есть');
			$('#addTypesGroup').focus();
			return false;
		}
		
		$.ajax({
			url: '<?php echo $link?>',
			type: 'POST',
			data: {"id": id, "newTypeGroup": val},
			success: function(html) {
				$('#addTypesGroupBlock').parent().html(html);
			}
		});
	
		return false;
	});

	//Редактирование группы
	$('.editTypeGroup').click(function(){
		var form = $(this).parent().find('.editTypeGroupForm');
		form.show().css('display','inline');
		$(this).parent().find('.typeGroupName').hide();
		$(this).unbind();
		form.ajaxForm({
			success: function(html) {
				$('#addTypesGroupBlock').parent().html(html);
			}
		});
		$(this).click(function(){
			form.submit();
			return false;
		});
		return false;
	});
	
	//Удаление группы
	$('.delTypeGroup').click(function(){
		if(confirm('Удалить группу?')) {
			$.ajax({
				url: '<?php echo $link?>&id='+id,
				type: 'POST',
				data: $(this).attr('href'),
				success: function(html) {
					$('#addTypesGroupBlock').parent().html(html);
				}
			});
		}
		return false;
	});
	
	//Сабмит формы редактирования характеристики
	$('.editTypeForm').ajaxForm({
		success: function(html) {
			$('#addTypesGroupBlock').parent().html(html);
		}
	});
	
	//Сабмит формы добавления характеристики
	$('.addTypeForm').ajaxForm({
		success: function(html) {
			$('#addTypesGroupBlock').parent().html(html);
		}
	});
	
	//Удаление характеристики
	$('.delType').click(function(){
		if(confirm('Удалить характеристику?')) {
			$.ajax({
				url: '<?php echo $link?>&id='+id,
				type: 'POST',
				data: $(this).attr('href'),
				success: function(html) {
					$('#addTypesGroupBlock').parent().html(html);
				}
			});
		}
		return false;
	});


	$('.typesGroups').sortable({
		'distance': 10,
		'opacity': .5,
		update: function(){
			$.post('<?php echo $link?>', $(this).sortable('serialize') + '&act=sortTypeGroup&id=' + id);
		}
	});
	$('.types').sortable({
		'distance': 5,
		'opacity': .5,
		update: function(){
			var gid = $(this).parent().attr('id').substr(6);
			var data = $(this).sortable('serialize') + '&act=sortTypes&gid=' + gid + '&id=' + id;
			$.post('<?php echo $link?>', data);
		}
	});


//});
</script>











