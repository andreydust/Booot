<style type="text/css">
		#<?php echo $table?>_dialog label, input { display:block; }
		#<?php echo $table?>_dialog input.text,textarea { margin-bottom:12px; width:95%; padding: .4em; outline:none; }
		#<?php echo $table?>_dialog input.radio { display:inline; margin-bottom:12px; outline:none; }
		#<?php echo $table?>_dialog textarea {height:100px;}
		#<?php echo $table?>_dialog fieldset { padding:0; border:0; margin-top:25px; }
		#<?php echo $table?>_dialog select { padding:5px 5px 4px 5px; width:100%; outline:none; margin-bottom:12px; }
		#<?php echo $table?>_dialog .ui-button { outline: 0; margin:0; padding: .4em 1em .5em; text-decoration:none;  !important; cursor:pointer; position: relative; text-align: center; }
		#<?php echo $table?>_dialog .ui-dialog .ui-state-highlight, .ui-dialog .ui-state-error { padding: .3em;  }
		.ui-datepicker { z-index: 10000 !important; }
		.datefield { width: 150px !important; }
		.autocomplete { background: white url("/admin/images/icons/autocompete_arrow.png") no-repeat right center; }
		.tabs { padding:0.2em 0 0 0 !important; }

		.floating_fields { float:left; width:300px; margin: 0 1.8em 0 0; }
		.fullwidth_fields { clear:both; }

		.ui-autocomplete {
			max-height: 133px;
			overflow-y: auto;
		}
		/* IE 6 doesn't support max-height
		 * we use height instead, but this forces the menu to always be this tall
		 */
		* html .ui-autocomplete {
			height: 133px;
		}
		
		
		
</style>

<!--<script type="text/javascript" src="/admin/js/tinymce/jquery.tinymce.js"></script>-->

<script type="text/javascript">
var currentEditId = 0;
var currentTabId = 0;
	$(function() {
		
		<?php echo implode("\r\n", $addjs)?>
		
		allFields = $('#<?php echo $table?>_dialog input,textarea');
		tips = $("#validateTips");

		function updateTips(t) {
			tips.text(t).effect("highlight",{},1500);
		}

		function checkLength(o,n,min,max) {

			if ( o.val().length > max || o.val().length < min ) {
				o.addClass('ui-state-error');
				updateTips("Размер поля «" + n + "» должен быть от "+min+" до "+max+" знаков.");
				return false;
			} else {
				return true;
			}

		}

		function checkRegexp(o,regexp,n) {

			if ( !( regexp.test( o.val() ) ) ) {
				o.addClass('ui-state-error');
				updateTips(n);
				return false;
			} else {
				return true;
			}

		}

		var currentTab = 'Main';
		//var currentTabId = 0;
		$("#<?php echo $table?>_dialog").dialog({
			bgiframe: false,
			autoOpen: false,
			closeOnEscape: false,
			width:'70%',
			height: 'auto',
			modal: true,
			buttons: {
				'Ok': function() {
					if(currentTab == 'Main') {
						var bValid = true;
						allFields.removeClass('ui-state-error');
						
						<?php echo $js?>
						
						if (bValid) {
							$('#<?php echo $table?>_form').submit();
						}
					} else {
						var cform = $('#ui-tabs-'+currentTabId+' form');
						if(cform.length == 0) $(this).dialog('close');

						//Находим текущий id
						//Если мы в таблице
						if($('#datatable_<?php echo $table?> .edit_link').length > 0) {
							var cid = currentEditId;
						}
						//Если мы в дереве
						else {
							var cid = $.tree.focused().selected.find("a").attr("id").substr(1);
						}
						
						cform.prepend('<input type="hidden" name="id" value="' + cid + '">');
						cform.prepend('<input type="hidden" name="table" value="<?php echo $table?>">');
						
						cform.ajaxSubmit({
							target:        '#ui-tabs-'+currentTabId,
							beforeSubmit:  function(){},
							success: function(){
								//var ctip = $('#validateTips'+currentTab);
								//ctip.effect("highlight",{},1500);
							}
						});
					}
				},
				'Отмена': function() {
					$(this).dialog('close');
				}
			},
			close: function() {
				//allFields.val('').removeClass('ui-state-error');
				//Чистим хэш у пути
				location.hash = '';
				tips.html('');
				$('.allTips').html('');
				$('.tabs').tabs( "select" , 0 );
			}
		});
		
		
		//Редактировать запись
		$('#datatable_<?php echo $table?> .edit_link').live('click', function() {
			currentEditId = $(this).attr('iid');
			jEditWindow(currentEditId);
			return false;
		});
		

		//Добавить запись
		$('#datatable_<?php echo $table?> .add_link, .add_link').click(function() {

			//Задаем id равным 0, мы же добавляем, а не изменяем
			$('#se_<?php echo $table?>_id').val('0');
			
			// Да—нет радио-чекбоксы
			$('#<?php echo $table?>_dialog input.radio').each(function(){
				if($(this).attr('def')==1) $(this).attr('checked',true);
				else $(this).attr('checked',false);
			});

			//Поля ввода input, textarea
			$('#<?php echo $table?>_dialog input.text,textarea').each(function(){
				$(this).val($(this).attr('def'));
			});

			//Картинка
			$('#se_<?php echo $table?>_image_prev').html('');

			$('#<?php echo $table?>_dialog').dialog('option', 'title','Новая запись');
			
			$('#<?php echo $table?>_dialog').dialog('open');
			$('#<?php echo $table?>_dialog').trigger('new_record');
			return false;
		});


		$(".datefield").datepicker({
			dateFormat: "yy-mm-dd"
		});

		/*
		$('textarea.tinymce').tinymce({
			// Location of TinyMCE script
			script_url : '/admin/js/tinymce/tiny_mce.js',

			// General options
			theme : "advanced",
			plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

			// Theme options
			theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,

			// Example content CSS (should be your site CSS)
			//content_css : "admin/css/content.css"
		});
		*/

		//Табы
		<?php if(!empty($tabs)) {?>
		$(function() {
			$(".tabs").tabs({
				select: function(event, ui) {
					//Находим текущий id
					//Если мы в таблице
					if($('#datatable_<?php echo $table?> .edit_link').length > 0) {
						var cid = currentEditId;
					}
					//Если мы в дереве
					else if(typeof($.tree) != 'undefined') {
						var cid = $.tree.focused().selected.find("a").attr("id").substr(1);
					} else {
						var cid = 0;
					}
					
					currentTab = $(ui.panel).attr("id").substr(3);
					currentTabId = ui.index;

					if(currentTabId == 0) return;
					
				    $(this).tabs("url", currentTabId, $(".tabs ul a[href='#ui-tabs-"+currentTabId+"']").data('href.tabs') + '&id=' + cid);
				},
				load: function() {
					//Позционирование по центру при смене таба, но че-то с артефактами работает
					//$( "#<?php echo $table?>_dialog" ).dialog( "option", "position", 'center' );
				}
			});
		});
		<?php }?>
	});

	function jEditWindow(id) {
		$.getJSON('<?php echo $plink?>&getFieldsById='+id,
			function(data){
				//Задаем id записи для формы
				$('#se_<?php echo $table?>_id').val(id);
				//Прописываем хэш к пути
				location.hash = '#open' + currentEditId;
				//Заполняем поля
				$.each(data, function(i,field){
					if(field.type == "enum('Y','N')") {
						if(field.value == 'Y') {
							$('#se_<?php echo $table?>_' + field.name+'_y').attr('checked',true);
							$('#se_<?php echo $table?>_' + field.name+'_n').attr('checked',false);
						} else {
							$('#se_<?php echo $table?>_' + field.name+'_y').attr('checked',false);
							$('#se_<?php echo $table?>_' + field.name+'_n').attr('checked',true);
						}
					} else if(field.type.substr(0,4) == 'enum') {
						$('#se_<?php echo $table?>_' + field.name + ' input').attr('checked',false);
						$('#se_<?php echo $table?>_' + field.name+'_'+field.value).attr('checked',true);
					} else if(field.type == 'Image') {
						$('#se_<?php echo $table?>_image_prev').html('<img src="'+field.value+'" height="80" />');
					} else {
						$('#se_<?php echo $table?>_' + field.name).val(field.value);
					}
					
				});
				$('#<?php echo $table?>_dialog').trigger('data_loaded');
			}
		);
		
		$('#<?php echo $table?>_dialog').dialog('option', 'title','Редактирование');
		
		$('#<?php echo $table?>_dialog').dialog('open');
		return false;
	}

//Автооткрытие по id записи
$(function(){ if (location.hash.match(/open[\d]+/i)) {
	currentEditId = location.hash.substr(5);
	jEditWindow(currentEditId);
}
});
</script>


<div id="<?php echo $table?>_dialog" <?php if(!empty($tabs)) {?>class="tabs"<?php }?> title="Добавление/Редактирование">
<?php if(!empty($tabs)) {?>
	<ul>
		<li><a href="#tabMain">Общие</a></li>
	<?php foreach ($tabs as $tab) {?>
		<li><a href="<?php echo $plink?>&method=<?php echo $tab['method']?>"><?php echo $tab['name']?></a></li>
	<?php }?>
	</ul>
<?php }?>
	
	<div id="tabMain">
		<p id="validateTips" class="allTips"></p>
	
		<form action="<?php echo $plink?>" id="<?php echo $table?>_form" method="post" <?php echo $we_have_files_fields?'enctype="multipart/form-data"':''?>>
		<fieldset>
			<input type="hidden" name="id" value="0" id="se_<?php echo $table?>_id" />
		<?php foreach($html as $v) {?>
			<?php echo $v?>
		<?php }?>
		</fieldset>
		</form>
	</div>
	
<?php /* if(!empty($tabs)) {?>
	<?php foreach ($tabs as $tab) {?>
		<div id="tab<?php echo $tab['method']?>">
			<p id="validateTips<?php echo $tab['method']?>" class="allTips"></p>
			
			<?php echo $tab['content']?>
		</div>
	<?php }?>
<?php }*/?>

</div>


