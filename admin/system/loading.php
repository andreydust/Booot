<?php

$GLOBALS['boootdir'] = realpath('../');
define('DIR',$GLOBALS['boootdir']);

require DIR.'/config.php';

//Временная зона
date_default_timezone_set($GLOBALS['config']['timezone']);

//Кодировка и сжатие
ob_start("ob_gzhandler");
header("Content-Type: text/html; charset=utf-8");
setlocale(LC_ALL, "ru_RU.UTF8");
setlocale(LC_NUMERIC, "en_US.utf8");
mb_internal_encoding("UTF-8");

//Загрузка библиотек админки
$lib = scandir(DIR.'/admin/lib',1);
foreach($lib as $file) {
	if(substr($file,-3,3) == 'php') include DIR.'/admin/lib/'.$file;
}

//Библиотеки с сайтовой части
include_once DIR.'/system/lib/additionalFunctions.php';
include_once DIR.'/system/lib/media.php';

//Создание объекта БД
$MySQL_obj = new MySQL();
function db() {
	global $MySQL_obj;
	return clone $MySQL_obj;
}

//Авторизация пользователя
$user = new AdminUsers();
//echo '<pre>'.print_r($_SESSION,1).'</pre>';

//Подключение модулей
$modules = array();
$mods = scandir(DIR.'/admin/modules',1);
foreach($mods as $file) {
	if(substr($file,-3,3) == 'php') {

		$module_name = substr($file,0,-4);
		
		//Доспуп пользователя к модулям
		if(!$user->isAllowed($module_name)) continue;
		
		include DIR.'/admin/modules/'.$file;
		
		$modules[$module_name::order] = array(
			'module'=>$module_name,
			'name'=>$module_name::name,
			'hide'=>($module_name::hide)?($module_name::hide):false
		);
	}
}

ksort($modules);

//Переопределяем конфиг
//@$GLOBALS['config']['site']['title']			= admGetSet('Blocks', 'site_title', $GLOBALS['config']['site']['title']);
//@$GLOBALS['config']['site']['admin_mail']		= admGetSet('Blocks', 'admin_mail', $GLOBALS['config']['site']['admin_mail']);
//@$GLOBALS['config']['site']['theme']			= admGetSet('Blocks', 'site_theme', $GLOBALS['config']['site']['theme']);