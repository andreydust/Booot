<?php
$allow_change_order = false;
$colspan=0;
?>

<style type="text/css">
#datatable_<?php echo $table?> { width: 100%;}
#datatable_<?php echo $table?> td { padding: 3px 5px; font-size: 0.95em; color:#333; }
#datatable_<?php echo $table?> th { padding: 3px 5px; font-size: 0.95em; cursor: pointer; font-weight: normal; text-shadow:0 1px 0 #FFFFFF; }
#datatable_<?php echo $table?> th.btn { border-left:none !important; border-right:none !important; }
.dataTables_info { padding-top: 0; }
.dataTables_paginate { padding-top: 0; }
.css_right { float: right; }
#datatable_<?php echo $table?>_wrapper .fg-toolbar { font-size: 0.8em; color:#FFF; text-shadow:0 -1px 0 #4F7F9F; }
#theme_links span { float: left; padding: 2px 10px; }
.dataTables_filter label, input { display:inline !important; }
.dataTables_paginate { text-shadow:none; }
.ex_highlight #datatable_<?php echo $table?> tbody tr.even:hover, #datatable_<?php echo $table?> tbody tr.even td.highlighted {
	background-color: #ECFFB3;
}
.ex_highlight #datatable_<?php echo $table?> tbody tr.odd:hover, #datatable_<?php echo $table?> tbody tr.odd td.highlighted {
	background-color: #E6FF99;
}
.noborder { border: none; }
#datatable_<?php echo $table?> td { white-space: nowrap; }
.add_block { margin: 0 0 0.5em 0; }
</style>

<?php if(in_array('add',$options['controls'])){?><div class="add_block"><a href="<?php echo $plink?>" class="add_link">Добавить запись</a></div><?php }?>

<?php if(!empty($thead)) {?>
<table cellpadding="0" cellspacing="0" id="datatable_<?php echo $table?>">
	<thead>
		<tr>
		<?php if(!empty($thead)) foreach($thead as $k=>$v) {?>
			<th><?php echo $v['name']?></th>
		<?php }?>
			<?php if(isset($options['nouns']['text'])){?><th class="btn"></th><?php }?>
			<?php if(in_array('edit',$options['controls'])){?><th class="btn"></th><?php }?>
			<?php if(in_array('del',$options['controls'])){?><th class="btn"></th><?php }?>
		</tr>
	</thead>
	
	<tbody>
	<?php foreach ($tbody as $row) {?>
		<tr id="tr<?php echo $table?>_<?php echo strip_tags($row[$options['nouns']['id']])?>">
		<?php foreach ($row as $fk=>$field) { ?>
		
			<?php // ДА / НЕТ
			if($syscolumns[$fk]['Type'] == "enum('Y','N')" && ($field=='Y' || $field=='N')) {?>
				
				<td>
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
				<td class="<?php echo $fk==$options['nouns']['name']?'namefordel':''?>"><?php echo $field?></td>
			<?php }?>
			
		<?php }?>
			<?php if(isset($options['nouns']['text'])){?><td><a href="<?php echo $plink.'&edit_text='.$row[$options['nouns']['id']]?>" class="edit_text_link" title="Редактировать текстовое содержание записи"></a></td><?php }?>
			<?php if(in_array('edit',$options['controls'])){?><td><a href="<?php echo $plink?>" class="edit_link" iid="<?php echo strip_tags($row[$options['nouns']['id']])?>" title="Редактировать поля"></a></td><?php }?>
			<?php if(in_array('del',$options['controls'])){?><td><a href="<?php echo $plink?>&delete=<?php echo strip_tags($row[$options['nouns']['id']])?>" class="del_link" iid="<?php echo strip_tags($row[$options['nouns']['id']])?>" title="Удалить"></a></td><?php }?>
		</tr>
	 <?php }?>
	</tbody>
</table>
<?php } else {?>
	<p>Нет записей<?php if(in_array('add',$options['controls'])){?>, но можно <a href="<?php echo $plink?>" class="add_link">добавить</a><?php }?></p>
<?php }?>

<div id="del_dialog" title="Удаление записи">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Вы уверены что хотите удалить запись «<span class="itWillName"></span>»?</p>
</div>


<script type="text/javascript">
	$(function(){

		$('#datatable_<?php echo $table?>').dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bAutoWidth": false,
			"bStateSave": true,
			"oLanguage": {
				"sLengthMenu": "Показывать по _MENU_ записей",
				"sSearch" : "Найти",
				"sZeroRecords": "Ничего не найдено",
				"sInfo": "Показаны с _START_ по _END_ из _TOTAL_ записей",
				"sInfoEmtpy": "Нет записей",
				"sInfoFiltered": "(отфильтровано из _MAX_ возможных записей)",
				"oPaginate": {
					"sFirst":    "Первая",
					"sPrevious": "←",
					"sNext":     "→",
					"sLast":     "Последняя"
				}
			},
			"aoColumns": [
			<?php if(!empty($thead)) foreach($thead as $k=>$v) {?>
				null,
			<?php }?>
				<?php if(isset($options['nouns']['text'])){?>{"bSearchable": false, "bSortable": false},<?php }?>
				<?php if(in_array('edit',$options['controls'])){?>{"bSearchable": false, "bSortable": false},<?php }?>
				<?php if(in_array('del',$options['controls'])){?>{"bSearchable": false, "bSortable": false}<?php }?>
			],
			"fnDrawCallback": function() {
				
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
				
			}
		});
		
		var to_del_id = '';
		$('.del_link').live('click', function(){
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