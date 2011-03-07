<script src="/admin/js/CodeMirror/codemirror.js" type="text/javascript"></script>

<form action="<?php echo $form_action; ?>" method="post">

<div id="edit_source_wrap">
<textarea id="edit_source" name="text"><?php echo $text?></textarea>
</div>

<?php if($writable) {?>
<div class="buttons">
<!--<input type="button" value="Вернуться к списку" class="ui-button ui-state-default ui-corner-all" id="goOut" />-->
<input type="submit" value="Сохранить" class="ui-button ui-state-default ui-corner-all" />
</div>
<?php } else {?>
<div class="error">Нет прав для редактирования файла <?php echo $file?></div>
<?php }?>
</form>

<script type="text/javascript">
  var editor = CodeMirror.fromTextArea('edit_source', {
	height: '600px',
    parserfile: <?php echo $parserfiles?>,
    stylesheet: <?php echo $stylesheets?>,
    path: "/admin/js/CodeMirror/"
  });
</script>