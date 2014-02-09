			<form  action="<?php echo linkByModule('Catalog')?>/search" method="get" class="navbar-form navbar-right" role="search">
				<div class="form-group">
					<input 
						type="text" 
						name="string" 
						value="<?=isset($_GET['string'])?htmlspecialchars($_GET['string']):''?>"
						class="form-control" 
						placeholder="Поиск по каталогу"
						id="SearchInput">
				</div>
				<button type="submit" class="btn btn-default">Найти</button>
			</form>