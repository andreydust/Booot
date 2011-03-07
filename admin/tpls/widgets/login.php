<style type="text/css"">
.datatable td,.datatable th {
	padding-top:5px !important;
	padding-bottom:5px !important;
}
#enterButton { margin-bottom:8px; }
</style>

<form action="<?php echo $_SERVER['REQUEST_URI']?>" method="post" id="loginForm">
	<table cellpadding="0" cellspacing="0" class="datatable" style="width:400px;">
		
		<thead>
			<tr>
				<th></th>
				<th>Вход в систему</th>
			</tr>
		</thead>
		
		<tbody>
			<tr>
				<td><label for="login">Логин</label></td>
				<td><input type="text" value="" name="login" id="login"></td>
			</tr>
			<tr>
				<td><label for="password">Пароль</label></td>
				<td><input type="password" value="" name="password" id="password"></td>
			</tr>
		
		<tfoot>
			<tr>
				<td></td>
				<td>
					<button id="enterButton">Войти</button>
				</td>
			</tr>
		</tfoot>
		
	</table>
</form>

<script type="text/javascript">
$(function(){
	$('#login').focus();
	$('#enterButton').button();
	$('#loginForm').attr('action', $('#loginForm').attr('action') + window.location.hash);
});
</script>