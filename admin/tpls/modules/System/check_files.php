<script type="text/javascript">

var backLink = '<?php echo $modulelink?>';
var saveLink = '<?php echo $savelink?>';
$(function(){
	
	//Сохранить текущее состояние
	$('#saveButton').button({
		icons: {
        	primary: 'ui-icon-disk'
    	}
    }).click(function(){
    	document.location = saveLink;
	});

	//Назад
	$('#backButton').button({
		icons: {
        	primary: 'ui-icon-arrowthick-1-w'
    	}
    }).click(function(){
		document.location = backLink;
	});
    
});
</script>

<div class="controlPanel">
	<button id="backButton">Назад</button>
	<button id="saveButton">Сохранить текущее состояние</button>
</div>

<table>
 <tr>
  <th>Имя файла</th>
  <th style="width:150px">Размер</th>
  <th style="width:150px">Последнее изменение</th>
  <th style="width:150px">Состояние</th>
 </tr>
<?php foreach ($files as $file) {?>
 <tr>
  <td><?php echo $file['file']?></td>
  <td><?php echo bytes_to_str($file['size'])?></td>
  <td><?php echo date('d.m.Y H:i', $file['date'])?></td>
  <td><?php echo $file['status']?></td>
 </tr>
<?php }?>
</table>