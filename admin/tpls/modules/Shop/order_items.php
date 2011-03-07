<style type="text/css">
#orderListitemsTable { border-collapse: collapse; }
#orderListitemsTable th, #orderListitemsTable td { border: solid 1px #A6C9E2; padding: 0.5em }
</style>

<table id="orderListitemsTable">
	<tr>
		<th>Категория</th>
		<th>Товар</th>
		<th>Цена</th>
		<th>Кол-во</th>
		<th>Стоимость</th>
	</tr>
<?php foreach ($items as $i) {?>
	<tr>
		<td><?php echo $i['topic']['name']?></td>
		<td><a href="<?php echo $i['link']?>" target="_blank"><?php echo $i['name']?></a></td>
		<td><?php echo $i['prettyPrice']?></td>
		<td><?php echo $i['count']?></td>
		<td><?php echo $i['prettyTotal']?></td>
	</tr>
<?php }?>
	<tr>
		<td colspan="4" style="text-align: right;">Итого:</td>
		<td><b><?php echo $prettyTotal?></b></td>
	</tr>
</table>
