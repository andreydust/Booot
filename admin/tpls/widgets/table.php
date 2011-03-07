<?php
$allow_change_order = false;
$colspan=0;
?>

<!-- CSS3 Table -->
<table cellpadding="0" cellspacing="0" id="datatable_<?php echo $table?>" class="datatable">
	<thead>
		<tr>
		<?php if(!empty($thead)) foreach($thead as $k=>$v) {
			if(!isset($v['orderd'])) $v['orderd'] = 'DESC';
			?>
			<th style="<?php echo isset($v['style'])?$v['style']:''?>" class="<?php if(isset($v['class'])) echo $v['class']?>">
			 <?php if($v['field'] != @$options['nouns']['order'] || !isset($v['order'])){?><a href="<?php echo $plink?>&order=<?php echo $v['field']?>&orderd=<?php  echo $v['orderd']=='ASC'?'DESC':'ASC'?>"><?php }?>
			  <?php echo $v['name']; if(isset($v['order'])){
			  	if($v['field'] == @$options['nouns']['order']) {
			  		echo ' ⥮';
			  		$allow_change_order = true;
			  	} else {
			  		echo $v['orderd']=='ASC'?'▲':'▼';
			  	}
			  }?>
			 <?php if($v['field'] != @$options['nouns']['order'] || !isset($v['order'])){?></a><?php }?>
			</th>
		<?php }?>
			<th class="min"></th>
			<th class="min"></th>
			<th class="min"></th>
		</tr>
	</thead>
	
	<tbody>
	<?php foreach ($tbody as $row) {?>
		<tr id="tr<?php echo $table?>_<?php echo strip_tags($row[$options['nouns']['id']])?>">
		<?php foreach ($row as $fk=>$field) { ?>
		
			<?php // ДА / НЕТ
			if($syscolumns[$fk]['Type'] == "enum('Y','N')" && ($field=='Y' || $field=='N')) {?>
				
				<td style="<?php echo isset($thead[$fk]['style'])?$thead[$fk]['style']:''?>">
					<div class="yesnoSlider" val="<?php echo $field?>" iid="<?php echo strip_tags($row[$options['nouns']['id']])?>" iname="<?php echo $fk?>" itable="<?php echo $table?>">
						<div class="yesnoLabelSlider">
							<span class="yesnoLabelYesSlider">Да</span>
							<span class="yesnoLabelNoSlider">Нет</span>
						</div>
						<?php if($field=='Y') {?>
						<div class="yesnoInSlider">Да</div>
						<?php } elseif($field=='N') {?>
						<div class="yesnoInSlider" style="left:30px">Нет</div>
						<?php }?>
					</div>
				</td>
				
			<?php // Текст
			} else {?>
				<td style="<?php echo isset($thead[$fk]['style'])?$thead[$fk]['style']:''?>" class="<?php echo $allow_change_order?'order':''?> <?php echo $fk==@$options['nouns']['order']?'sortorder':''?> <?php echo $fk==$options['nouns']['name']?'namefordel':''?>"><?php echo $field?></td>
			<?php }?>
			
		<?php }?>
			<?php if(isset($options['nouns']['text'])){$colspan++;?><td><a href="<?php echo $plink.'&edit_text='.strip_tags($row[$options['nouns']['id']])?>" class="edit_text_link" title="Редактировать текстовое содержание записи"></a></td><?php }?>
			<?php if(in_array('edit',$options['controls'])){$colspan++;?><td><a href="<?php echo $plink?>" class="edit_link" iid="<?php echo strip_tags($row[$options['nouns']['id']])?>" title="Редактировать поля"></a></td><?php }?>
			<?php if(in_array('del',$options['controls'])){$colspan++;?><td><a href="<?php echo $plink?>&delete=<?php echo strip_tags($row[$options['nouns']['id']])?>" class="del_link" iid="<?php echo strip_tags($row[$options['nouns']['id']])?>" title="Удалить"></a></td><?php }?>
		</tr>
	 <?php }?>
	</tbody>
	
	<tfoot>
		<tr>
			<td colspan="<?php echo count($thead)+$colspan?>">
				<?php if(in_array('add',$options['controls'])){?><a href="<?php echo $plink?>" class="add_link">Добавить запись</a><?php }?>
			</td>
		</tr>
	</tfoot>
</table>


<div id="del_dialog" title="Удаление записи">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Вы уверены что хотите удалить запись «<span class="itWillName"></span>»?</p>
</div>


<script type="text/javascript">
	$(function(){

		<?php if($highlight != 0) {?>
		$('#tr<?php echo $table?>_<?php echo $highlight?>').effect("highlight",{},1500);
		<?php }?>
		
		<?php if($allow_change_order) {?>
		$("#datatable_<?php echo $table?> tbody")
			.sortable({
				opacity: 0.6,
				distance: 5,
				update: function(){
					$.post('<?php echo $plink?>', $(this).sortable('serialize'));
					$('.sortorder').each(function(i,e){
						$(e).html(i);
					});
				}
			})
			.disableSelection();
		<?php }?>
		
		var to_del_id = '';
		$('.del_link').click(function(){
			to_del_id = $(this).attr('iid');
			$("#del_dialog .itWillName").html($('#tr<?php echo $table?>_'+to_del_id+' td.namefordel').html());
			$("#del_dialog").dialog('open');
			return false;
		});
		
		$("#del_dialog").dialog({
			autoOpen: false,
			resizable: false,
			modal: true,
			overlay: {
				backgroundColor: '#000',
				opacity: 0.5
			},
			buttons: {
				'Да': function() {
					document.location = '<?php echo $plink?>&delete=' + to_del_id;
					$(this).dialog('close');
				},
				'Нет': function() {
					$(this).dialog('close');
				}
			}
		});

		//ДА / НЕТ
		$('.yesnoInSlider').draggable({
				containment: 'parent',
				drag: function(event, ui) {
					var left = parseInt($(event.target).css('left'));
					if(left > 15) $(event.target).html('Нет');
					else $(event.target).html('Да');
				},
				stop: function(event, ui) {
					var left = parseInt($(event.target).css('left'));
					if(left > 15) $(event.target).parent().parent().find('.yesnoLabelNoSlider').trigger('click');
					else $(event.target).parent().parent().find('.yesnoLabelYesSlider').trigger('click');
				}
		});
		$('.yesnoLabelYesSlider').live('click', function(){
			var sliderWd = $(this).parent().parent();
			var slider = sliderWd.find('.yesnoInSlider');
			slider.css('left','0px').html('Да');
			yesno(sliderWd.attr('itable'), sliderWd.attr('iid'), sliderWd.attr('iname'), 'Y');
		});
		$('.yesnoLabelNoSlider').live('click', function(){
			var sliderWd = $(this).parent().parent();
			var slider = sliderWd.find('.yesnoInSlider');
			slider.css('left','30px').html('Нет');
			yesno(sliderWd.attr('itable'), sliderWd.attr('iid'), sliderWd.attr('iname'), 'N');
		});
		function yesno(table, id, field, val) {
			$.ajax({
				"url":		"<?php echo $plink?>&update",
				"type":		"POST",
				"data":		{
					'table':	table,
					'id':		id,
					'field':	field,
					'val':		val
				},
				"success":	function(result) {}
			});
		}

	});
</script>