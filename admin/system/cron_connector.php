<?php

//Больше памяти крону
ini_set('memory_limit', '128M');

$bootdirparts = pathinfo(__FILE__);
$GLOBALS['boootdir'] = realpath($bootdirparts['dirname'].'/../..');
define('DIR',$GLOBALS['boootdir']);

require DIR.'/config.php';

//Временная зона
date_default_timezone_set($GLOBALS['config']['timezone']);

//Кодировка и сжатие
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


$CronJobs = new CronJobs();