<script type="text/javascript" src="/admin/js/tinymce/jquery.tinymce.js"></script>
<script type="text/javascript">
	$(function() {

		var text_changed = false;
		$('#edit_text').tinymce({
			script_url : '/admin/js/tinymce/tiny_mce.js',
			skin : "o2k7",
			theme : "advanced",
			plugins : "jaretypograph,safari,style,table,images,advhr,advimage,advlink,inlinepopups,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",

			// Theme options
			theme_advanced_buttons1 : "bold,italic,underline,formatselect,jaretypograph,link,justifyleft,justifycenter,justifyright,pasteword,pastetext,table,images,|,bullist,numlist,|,undo,redo,|,code,fullscreen",
			theme_advanced_buttons2 : "",
			theme_advanced_buttons3 : "",
			theme_advanced_buttons4 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,
			
			// Site CSS
			content_css : "/themes/<?php echo $GLOBALS['config']['site']['theme']?>/css/typo.css",

			relative_urls : false,
			remove_script_host : true,
			
			language : "ru",
			
			setup : function(ed) {
				ed.onChange.add(function(ed, l) {
					text_changed = true;
				});
			}
		});

		$('#goOut').click(function(){
			if(text_changed) window.onbeforeunload = function () { return 'Изменения в содержании не сохранены.'; }
			document.location = '<?php echo $plink?>';
		});
		
	});
</script>


<form action="<?php echo $plink?>&edit_text=<?php echo $id?>" method="post">
<textarea id="edit_text" name="text"><?php echo $text?></textarea>

<div class="buttons">
<input type="button" value="Вернуться к списку" class="ui-button ui-state-default ui-corner-all" id="goOut" />
<input type="submit" value="Сохранить" class="ui-button ui-state-default ui-corner-all" />
</div>
</form>










