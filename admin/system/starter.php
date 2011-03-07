<?php

if(isset($_GET['module'])) {
	$module_exist = false;
	foreach($modules as $m) {
		if($_GET['module'] == $m['module']) {
			$module_exist = true;
			break;
		}
	}
	if($module_exist) $mod = $_GET['module'];
	else $mod = $_GET['module'] = 'About';
} else {
	$mod = $_GET['module'] = 'About';
}

$module = new $mod();