<?php

$GLOBALS['boootdir'] = realpath('.');
define('DIR',$GLOBALS['boootdir']);

require DIR.'/config.php';

//Временная зона
date_default_timezone_set($GLOBALS['config']['timezone']);

//Кодировка и сжатие
ob_start("ob_gzhandler");
header("Content-Type: text/html; charset=utf-8");
setlocale(LC_ALL, "ru_RU.UTF8");
setlocale(LC_NUMERIC, "C");
mb_internal_encoding("UTF-8");

//Создание объекта БД
require_once DIR.'/admin/lib/db.php';
$MySQL_obj = new MySQL();
function db() {
	global $MySQL_obj;
	return clone $MySQL_obj;
}

//Загрузка библиотек сайта
$lib = scandir(DIR.'/system/lib',1);
foreach($lib as $file) {
	if(substr($file,-3,3) == 'php') include DIR.'/system/lib/'.$file;
}

session_start();

//Подключение модулей
$modules = array();
$mods = scandir(DIR.'/system/modules',1);
foreach($mods as $file) {
	if(substr($file,-3,3) == 'php') {
		include DIR.'/system/modules/'.$file;
	}
}

//Определение некоторых глобальных переменных
$GLOBALS['head_add'] = '';

//Переопределяем конфиг
@$GLOBALS['config']['site']['title']			= getSet('Blocks', 'site_title', $GLOBALS['config']['site']['title']);
@$GLOBALS['config']['site']['admin_mail']		= getSet('Blocks', 'admin_mail', $GLOBALS['config']['site']['admin_mail']);
@$GLOBALS['config']['site']['theme']			= getSet('Blocks', 'site_theme', $GLOBALS['config']['site']['theme']);