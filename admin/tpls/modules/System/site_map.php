<script type="text/javascript">

var backLink = '<?php echo $modulelink?>';
var saveLink = '<?php echo $savelink?>';
$(function(){
	
	//Сохранить текущее состояние
	$('#saveButton').button({
		icons: {
        	primary: 'ui-icon-gear'
    	}
    }).click(function(){
    	//document.location = saveLink;
    	$('#sitemapForm').submit();
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
	<button id="saveButton">Генерация карты сайта</button>
</div>

<form action="<?php echo $savelink?>" method="post" id="sitemapForm">
	<table style="margin:2em 0 0 1em">
		<tr>
			<th>Тип страниц</th>
			<th>Частота обновления</th>
			<th>Приоритет, %</th>
		</tr>
		
		<tr>
			<td>Главная страница</td>
			<td>
				<select name="main[freq]">
					<option>always</option>
					<option>hourly</option>
					<option>daily</option>
					<option>weekly</option>
					<option>monthly</option>
					<option>yearly</option>
					<option>never</option>
				</select>
			</td>
			<td>
				<input type="text" value="50"  name="main[priority]">
			</td>
		</tr>
		
		<tr>
			<td>Текстовые страницы 2го уровня</td>
			<td>
				<select name="content2[freq]">
					<option>always</option>
					<option>hourly</option>
					<option>daily</option>
					<option>weekly</option>
					<option>monthly</option>
					<option>yearly</option>
					<option>never</option>
				</select>
			</td>
			<td>
				<input type="text" value="50"  name="content2[priority]">
			</td>
		</tr>
		
		<tr>
			<td>Текстовые страницы 3го и более уровней</td>
			<td>
				<select name="content3[freq]">
					<option>always</option>
					<option>hourly</option>
					<option>daily</option>
					<option>weekly</option>
					<option>monthly</option>
					<option>yearly</option>
					<option>never</option>
				</select>
			</td>
			<td>
				<input type="text" value="50"  name="content3[priority]">
			</td>
		</tr>
		
		<!--
		<tr>
			<td>Категории каталога</td>
			<td>
				<select name="catalog_category[freq]">
					<option>always</option>
					<option>hourly</option>
					<option>daily</option>
					<option>weekly</option>
					<option>monthly</option>
					<option>yearly</option>
					<option>never</option>
				</select>
			</td>
			<td>
				<input type="text" value="50"  name="catalog_category[priority]">
			</td>
		</tr>
		
		<tr>
			<td>Товары каталога</td>
			<td>
				<select name="catalog_product[freq]">
					<option>always</option>
					<option>hourly</option>
					<option>daily</option>
					<option>weekly</option>
					<option>monthly</option>
					<option>yearly</option>
					<option>never</option>
				</select>
			</td>
			<td>
				<input type="text" value="50"  name="catalog_product[priority]">
			</td>
		</tr>
		
		<tr>
			<td>Новости — список</td>
			<td>
				<select name="news_list[freq]">
					<option>always</option>
					<option>hourly</option>
					<option>daily</option>
					<option>weekly</option>
					<option>monthly</option>
					<option>yearly</option>
					<option>never</option>
				</select>
			</td>
			<td>
				<input type="text" value="50"  name="news_list[priority]">
			</td>
		</tr>
		
		<tr>
			<td>Новости — новость</td>
			<td>
				<select name="news_new[freq]">
					<option>always</option>
					<option>hourly</option>
					<option>daily</option>
					<option>weekly</option>
					<option>monthly</option>
					<option>yearly</option>
					<option>never</option>
				</select>
			</td>
			<td>
				<input type="text" value="50"  name="news_new[priority]">
			</td>
		</tr>
		
		<tr>
			<td>Другие списки — список</td>
			<td>
				<select name="lists_list[freq]">
					<option>always</option>
					<option>hourly</option>
					<option>daily</option>
					<option>weekly</option>
					<option>monthly</option>
					<option>yearly</option>
					<option>never</option>
				</select>
			</td>
			<td>
				<input type="text" value="50"  name="lists_list[priority]">
			</td>
		</tr>
		
		<tr>
			<td>Другие списки — страница позиции</td>
			<td>
				<select name="lists_item[freq]">
					<option>always</option>
					<option>hourly</option>
					<option>daily</option>
					<option>weekly</option>
					<option>monthly</option>
					<option>yearly</option>
					<option>never</option>
				</select>
			</td>
			<td>
				<input type="text" value="50"  name="lists_item[priority]">
			</td>
		</tr>
		-->
		
	</table>
</form>