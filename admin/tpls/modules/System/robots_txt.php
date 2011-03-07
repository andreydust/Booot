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


<form action="" method="post">

	<div class="controlPanel">
		<button id="backButton">Назад</button>
		<button id="saveButton">Сохранить текущее состояние</button>
	</div>

</form>