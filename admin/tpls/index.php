<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo $title?></title>
	<link rel="stylesheet" href="/admin/css/layout.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="/admin/css/jQueryUI/redmond/jquery-ui-1.8.4.custom.css" type="text/css" />
	<link rel="stylesheet" href="/admin/css/DataTableAdvanced/table_jui.css" type="text/css" />
	<link rel="stylesheet" href="/admin/css/TableCSS/style.css" type="text/css" />
	<script type="text/javascript" src="/admin/js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="/admin/js/jquery-ui-1.8.4.custom.min.js"></script>
	<script type="text/javascript" src="/admin/js/jquery.form.js"></script>
	<script type="text/javascript" src="/admin/js/jquery.cookie.js"></script>
	<script type="text/javascript" src="/admin/js/DataTableAdvanced/jquery.dataTables.min.js"></script>
	
	<!--[if lte IE 7]>
	<script type="text/javascript" src="http://dustweb.ru/ieSunset/ieSunset.js"></script>
	<![endif]-->
	
	<link rel="icon" href="/admin/images/icons/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="/admin/images/icons/favicon.ico" type="image/x-icon">
</head>

<body>
	<div id="header">
		<div id="logo"><a href="/admin/"><img src="/admin/images/logo.png" width="135" height="72" alt="Booot™" /></a></div>
		
		<?php echo $menu?>
		
		<?php echo $userbar?>
		
	</div>
	
	<h1><?php echo $h1?></h1>
	
	<div id="content">
		<div id="module" style="<?php echo empty($hint)&&empty($sidemenu)?'width:80%':''?>">
		
			<?php echo $content?>
			
		</div>
		
		<div id="sidebar">
		
			<?php echo $sidemenu?>
			
			<?php echo $hint?>
			
		</div>
		
	</div>
	
	<div id="footer">
		<span>© 2009&ndash;<?php echo date('Y')?>  —  <a href="http://booot.ru/" target="_blank">Booot</a></span>
	</div>
</body>
</html>
