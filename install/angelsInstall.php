<?php

#/opt/lampp/bin/php ./angelsInstall.php -dbslocalhost -dburoot -dbpmysecretpass -dbbbooot_test -btuadmin -btpadmin -btmtest@test.ru

if(!isset($argv) || empty($argv)) return false;

unset($argv[0]);

$data = array();
foreach($argv as $k) {
	$data[substr($k,1,3)] = substr($k,4);
}

//print_r($data);

//КОНФИГ ФАЙЛ
$path_parts = pathinfo(__FILE__);
$dir = $path_parts['dirname'];
$config_file = $dir.'/config.php';
$config_content = file_get_contents($config_file);
$dbDataMatch = array('server'=>'dbs','user'=>'dbu','password'=>'dbp','db'=>'dbb');
foreach($dbDataMatch as $configVar=>$argName) {
	if (preg_match('/[\s]*\''.$configVar.'\'[\s]*=>[\s]*\'([^\']*)\'/i', $config_content, $regs)) {
		$newVal = str_replace($regs[1], $data[$argName], $regs[0]);
		$config_content = str_replace($regs[0], $newVal, $config_content);
	} else return false;
}
file_put_contents($config_file, $config_content);

//БАЗА
$mysqli = new mysqli($data['dbs'], $data['dbu'], $data['dbp'], $data['dbb']);
$query  = file_get_contents($dir.'/booot.sql');
if ($mysqli->multi_query($query)) {
    do {
        if ($result = $mysqli->store_result()) {
            while ($row = $result->fetch_row()) {}
            $result->free();
        }
        if ($mysqli->more_results()) {}
    } while (@$mysqli->next_result());
}
$mysqli->close();

//НАСТРОЙКИ ДОСТУПА
//HTACCESS
$htaccess_content = 'AuthType Basic
AuthName "The answer to life, the universe, and everything"
AuthUserFile '.$dir.'/admin/.htpasswd
Require valid-user';
$htaccess = $dir.'/admin/.htaccess';
file_put_contents($htaccess, $htaccess_content);
//HTPASSWD
define('DIR', $dir);
$htpasswd = $dir.'/admin/.htpasswd';
if(is_file($htpasswd)) file_put_contents($htpasswd, '');
include $dir.'/admin/lib/PasswdAuth.php';
$auth = new PasswdAuth();
$auth->addUser($data['btu'], $data['btp']);


//ПОЛЬЗОВАТЕЛЯ В БАЗУ
$mysqli = new mysqli($data['dbs'], $data['dbu'], $data['dbp'], $data['dbb']);
$mysqli->query("SET NAMES UTF8");
$mysqli->query("TRUNCATE TABLE `bt_admin_users`");
$mysqli->query("INSERT INTO `bt_admin_users` (`login`,`password`,`type`,`post`,`email`,`created`) VALUES
	('{$data['btu']}','{$data['btp']}','a','Администратор','{$data['btm']}',NOW())");
$mysqli->close();

//CRON пока не надо
//system('crontab -l > tmp; echo 'somerecord' >> tmp; cat tmp | crontab -');



return true;
