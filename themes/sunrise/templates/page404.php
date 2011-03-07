<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
  <title>404 — <?php echo $GLOBALS['config']['site']['title']?></title>
  <?php echo tpl('parts/head')?>
</head>

<body>
	
	<?php echo tpl('parts/header')?>
	
	<div class="ContentArea">
		
		<div class="ContentAreaIn">
			<div class="ContentFullPage">
				<h1>404 — страница не найдена</h1>
				<p>Такое бывает, попробуй зайти на <a href="/">главную страницу</a>, или выбери нужную в меню, чувак.</p>
				<p><?php echo $debug?></p>
				<p><img src="/data/jnb.jpg" /></p>
			</div>
		</div>
		
	</div>
	
	<?php echo tpl('parts/footer')?>
	
</body>
</html>