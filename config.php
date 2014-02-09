<?php

$GLOBALS['config'] = array();

//Разрабатываемая версия
$GLOBALS['config']['develop'] = true;

//Настройки БД
$GLOBALS['config']['db'] = array(
	'server'				=> 'localhost',
	'user'					=> 'booot',
	'password'				=> '',
	'db'					=> 'booot',
	'prefix'				=> 'bm_',
	'set_names_utf8'		=> true,
	'show_query_devmode'	=> false
);

//Временная зона
$GLOBALS['config']['timezone'] = 'Asia/Yekaterinburg';

//Системные оповещения RU
$GLOBALS['config']['msg'] = array(
	'mysqlftl'	=> 'Ошибка соединения с сервером MySQL',
	'mysqlerr'	=> 'Ошибка MySQL',
	'timegen'	=> 'Сводка времени выполнения блоков',
	'timegen_ns'	=> 'на текущее время',
	'timegen_nb'	=> 'Секундомеры не установлены'
);

$GLOBALS['config']['site'] = array(
	'title'			=> 'Booot Free',
	'admin_mail'	=> 'andreydust@gmail.com',
	'theme'			=> 'cloudyNoon'
);

$GLOBALS['config']['jabber'] = array(
	'host'		=>	'talk.google.com',
	'port'		=>	5222,
	'user'		=>	'example@gmail.com',
	'password'	=>	'mysecretpass',
	'resource'	=>	'xmpphp',
	'server'	=>	'gmail.com'
);