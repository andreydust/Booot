<?php
session_start();
if(!isset($_GET['step'])) {
	if(is_file('admin/.htaccess')) {
		if(is_writable('admin/.htaccess')) {
			file_put_contents('admin/.htaccess', '');
		}
	}
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Установка — Booot CMS</title>
	<link rel="stylesheet" href="/admin/css/layout.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="/admin/css/jQueryUI/redmond/jquery-ui-1.8.4.custom.css" type="text/css" />

	<script type="text/javascript" src="/admin/js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="/admin/js/jquery-ui-1.8.4.custom.min.js"></script>
	
	<!--[if lte IE 7]>
	<script type="text/javascript" src="http://dustweb.ru/ieSunset/ieSunset.js"></script>
	<![endif]-->
	
	<link rel="icon" href="/admin/images/icons/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="/admin/images/icons/favicon.ico" type="image/x-icon">

</head>

<body>
	<div id="header">
		<div id="logo"><a href="/install.php"><img src="/admin/images/logo.png" width="135" height="72" alt="Booot" /></a></div>
		
<ul id="menu">
	<li class="act"><a>Установка Booot CMS</a></li>
</ul>
		
	</div>
	

<?php

//Конфигурация
if(isset($_GET['step']) && $_GET['step'] == 5) {
$timezones = array('Pacific/Kwajalein','Pacific/Samoa','US/Hawaii','US/Alaska','US/Pacific','US/Arizona','America/Mexico_City','US/East-Indiana','America/Santiago','America/Buenos_Aires','Brazil/DeNoronha','Atlantic/Cape_Verde','Europe/London','Europe/Berlin','Europe/Kiev','Europe/Moscow','Europe/Samara','Asia/Yekaterinburg','Asia/Novosibirsk','Asia/Krasnoyarsk','Asia/Irkutsk','Asia/Yakutsk','Asia/Vladivostok','Asia/Magadan','Asia/Kamchatka','Pacific/Tongatapu','Pacific/Kiritimati');
?>
	<h1>Спасибо!</h1>
	
	<div id="content">

		<div id="module" style="width:80%">
<?php
	//КОНФИГ ФАЙЛ
	$config_file = realpath($_SERVER['DOCUMENT_ROOT']).'/config.php';
	$config_content = file_get_contents($config_file);
	//БД
	foreach ($_SESSION['db'] as $key=>$val) {
		$config_content = preg_replace('/(\$GLOBALS\[[\']config[\']\]\[[\']db[\']\][\s]*=[\s]*array[\s]*\([^;]*\''.$key.'\'[\s]*=>[\s]*\')(.*)(\')/i', '$1'.$val.'$3', $config_content);
	}
	//Конфиг
	foreach ($_SESSION['config'] as $key=>$val) {
		$config_content = preg_replace('/(\$GLOBALS\[[\']config[\']\]\[[\']site[\']\][\s]*=[\s]*array[\s]*\([^;]*\''.$key.'\'[\s]*=>[\s]*\')(.*)(\')/i', '$1'.$val.'$3', $config_content);
	}
	//Таймзона
	$config_content = preg_replace('/(\$GLOBALS\[[\']config[\']\]\[[\']timezone[\']\][\s]*=[\s]*\')(.*)(\')/i', '$1'.$_SESSION['timezone'].'$3', $config_content);

	file_put_contents($config_file, $config_content);
	
	//HTACCESS
	$htaccess_content = 'AuthType Basic
AuthName "The answer to life, the universe, and everything"
AuthUserFile '.realpath($_SERVER['DOCUMENT_ROOT']).'/admin/.htpasswd
Require valid-user';
	$htaccess = realpath($_SERVER['DOCUMENT_ROOT']).'/admin/.htaccess';
	file_put_contents($htaccess, $htaccess_content);
	
	//HTPASSWD
	define('DIR', realpath($_SERVER['DOCUMENT_ROOT']));
	$htpasswd = DIR.'/admin/.htpasswd';
	if(is_file($htpasswd)) file_put_contents($htpasswd, '');
	include DIR.'/admin/lib/PasswdAuth.php';
	$auth = new PasswdAuth();
	$auth->addUser($_SESSION['auth']['login'], $_SESSION['auth']['pass']);

	$mysqli = new mysqli($_SESSION['db']['server'], $_SESSION['db']['user'], $_SESSION['db']['password'], $_SESSION['db']['db']);
	$query  = "SET NAMES UTF8;
	TRUNCATE TABLE `{$_SESSION['db']['prefix']}admin_users`;
	INSERT INTO `{$_SESSION['db']['prefix']}admin_users` (`login`,`password`,`type`,`post`,`email`,`created`) VALUES
	('{$_SESSION['auth']['login']}','{$_SESSION['auth']['pass']}','a','Администратор','{$_SESSION['config']['admin_mail']}',NOW());";
	$mysqli->multi_query($query);
	$mysqli->close();
	
	//CLEAN UP
	unlink(DIR.'/install.php');

?>

<p>Установка успешно завершена, теперь вы можете <a href="/admin">авторизоваться в системе администрирования</a> или <a href="/">перейти на сайт</a>.</p>

<?php }

//Конфигурация
else if(isset($_GET['step']) && $_GET['step'] == 4) {
$timezones = array('Pacific/Kwajalein','Pacific/Samoa','US/Hawaii','US/Alaska','US/Pacific','US/Arizona','America/Mexico_City','US/East-Indiana','America/Santiago','America/Buenos_Aires','Brazil/DeNoronha','Atlantic/Cape_Verde','Europe/London','Europe/Berlin','Europe/Kiev','Europe/Moscow','Europe/Samara','Asia/Yekaterinburg','Asia/Novosibirsk','Asia/Krasnoyarsk','Asia/Irkutsk','Asia/Yakutsk','Asia/Vladivostok','Asia/Magadan','Asia/Kamchatka','Pacific/Tongatapu','Pacific/Kiritimati');
?>
	<h1>Конфигурация</h1>
	
	<div id="content">

		<div id="module" style="width:80%">

<?php
$config_message = '';
if(!empty($_POST)) {
	if (preg_match('/\A[a-z][a-z_0-9]*\z/i', $_POST['login'])) {
		if (preg_match('/^[A-Z0-9._%+-]+@(?:[A-Z0-9-]+\.)+[A-Z]{2,4}$/i', $_POST['email'])) {
			if(!empty($_POST['pass'])){
				$_SESSION['config'] = array(
					'title' 		=> addcslashes($_POST['site_name'],"'\'"),
					'admin_mail'	=> $_POST['email']
				);
				$_SESSION['timezone'] = $_POST['timezone'];
				$_SESSION['auth'] = array(
					'login'		=> $_POST['login'],
					'pass'		=> $_POST['pass']
				);
				//header('Location: ?step=5');
				echo '<script type="text/javascript">document.location = "?step=5";</script>';
			} else {
				$config_message = 'Пустой пароль администратора';
			}
		} else {
			$config_message = 'Некорректная почта администратора';
		}
	} else {
		$config_message = 'Некорректный логин администратора';
	}
}
?>

<form action="?step=4" method="post">
<table cellpadding="0" cellspacing="0" id="datatable_content" class="datatable" style="width:500px;">
	<thead>
		<tr>
			<th class="left"></th>
			<th class="right"></th>
		</tr>
	</thead>
	
	<tbody>
		<tr>
			<td>Логин администратора</td>
			<td><input type="text" value="<?php echo isset($_POST['login'])?$_POST['login']:'admin'?>" name="login"></td>
		</tr>
		<tr>
			<td>Пароль администратора</td>
			<td><input type="password" value="<?php echo isset($_POST['pass'])?$_POST['pass']:''?>" name="pass"></td>
		</tr>
		<tr>
			<td>Почта администратора</td>
			<td><input type="text" value="<?php echo isset($_POST['email'])?$_POST['email']:''?>" name="email"></td>
		</tr>
		<tr>
			<td>Имя сайта</td>
			<td><input type="text" value="<?php echo isset($_POST['site_name'])?$_POST['site_name']:'Интернет-магазин «На диване и в сауне»'?>" name="site_name"></td>
		</tr>
		<tr>
			<td>Временная зона сайта</td>
			<td>
				<select name="timezone" >
				<?php foreach ($timezones as $timezone) {
					if(isset($_POST['timezone']) && $timezone == $_POST['timezone']) {
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}
					?>
					<option value="<?php echo $timezone?>" <?php echo $selected?>><?php echo $timezone?></option>
					<?php
				}?>
				</select>
			</td>
		</tr>
	</tbody>
	
	<tfoot>
		<tr>
			<td colspan="2" style="color:grey;"><?php echo $config_message?></td>
		</tr>
	</tfoot>
</table>

<button id="nextButton" style="margin:2em 0 0 0;">Далее</button>
</form>

<script type="text/javascript">
$('#nextButton').button({
	icons: { primary: 'ui-icon-arrowthick-1-e' }
});
</script>
<?php
}
//База данных (запись)
else if(isset($_GET['step']) && $_GET['step'] == 3) {
?>
	<h1>База данных</h1>
	
	<div id="content">

		<div id="module" style="width:80%">

<?php

$mysqli = new mysqli($_SESSION['db']['server'], $_SESSION['db']['user'], $_SESSION['db']['password'], $_SESSION['db']['db']);
$query  = str_replace('bt_',$_SESSION['db']['prefix'], file_get_contents(realpath($_SERVER['DOCUMENT_ROOT']).'/booot.sql'));
if($mysqli->multi_query($query)) {
	?>
	<p>База данных успешно создана</p>
	
	<button id="nextButton" style="margin:2em 0 0 0;">Далее</button>
	
	<script type="text/javascript">
	$('#nextButton').button({
		icons: { primary: 'ui-icon-arrowthick-1-e' }
	}).click(function(){
		document.location = '?step=4';
	});
	</script>
	<?php
}
$mysqli->close();

}
//База данных
else if(isset($_GET['step']) && $_GET['step'] == 2) {
?>
	<h1>База данных</h1>
	
	<div id="content">

		<div id="module" style="width:80%">

<p>Введите реквизиты для базы данных</p>

<?php
$db_message = '';
if(!empty($_POST)) {
	if(@mysql_connect($_POST['server'], $_POST['user'], $_POST['password'])) {
		if(@mysql_select_db($_POST['db'])) {
			if(!empty($_POST['prefix']) && preg_match('/\A[a-z][a-z_0-9]*\z/i', $_POST['prefix'])) {
				$_SESSION['db'] = array(
					'server'	=> $_POST['server'],
					'user'		=> $_POST['user'],
					'password'	=> $_POST['password'],
					'db'		=> $_POST['db'],
					'prefix'	=> $_POST['prefix']
				);
				//header('Location: ?step=3');
				echo '<script type="text/javascript">document.location = "?step=3";</script>';
			} else {
				$db_message = 'Префикс '.$_POST['prefix'].' задан некорректно';
			}
		} else {
			$db_message = 'Не удалось найти базу '.$_POST['db'];
		}
	} else {
		$db_message = 'Не удалось подключиться к серверу '.$_POST['server'];
	}
}
?>

<form action="?step=2" method="post">
<table cellpadding="0" cellspacing="0" id="datatable_content" class="datatable" style="width:400px;">
	<thead>
		<tr>
			<th class="left"></th>
			<th class="right"></th>
		</tr>
	</thead>
	
	<tbody>
		<tr>
			<td>Сервер</td>
			<td><input type="text" value="<?php echo isset($_POST['server'])?$_POST['server']:'localhost'?>" name="server"></td>
		</tr>
		<tr>
			<td>Пользователь</td>
			<td><input type="text" value="<?php echo isset($_POST['user'])?$_POST['user']:'root'?>" name="user"></td>
		</tr>
		<tr>
			<td>Пароль</td>
			<td><input type="password" value="<?php echo isset($_POST['password'])?$_POST['password']:''?>" name="password"></td>
		</tr>
		<tr>
			<td>Имя базы данных</td>
			<td><input type="text" value="<?php echo isset($_POST['db'])?$_POST['db']:''?>" name="db"></td>
		</tr>
		<tr>
			<td>Префикс таблиц</td>
			<td><input type="text" value="<?php echo isset($_POST['prefix'])?$_POST['prefix']:'bt_'?>" name="prefix"></td>
		</tr>
	</tbody>
	
	<tfoot>
		<tr>
			<td colspan="2" style="color:grey;"><?php echo $db_message?></td>
		</tr>
	</tfoot>
</table>

<button id="nextButton" style="margin:2em 0 0 0;">Далее</button>
</form>

<script type="text/javascript">
$('#nextButton').button({
	icons: { primary: 'ui-icon-arrowthick-1-e' }
});
</script>


<?php
}
//Проверка системных требований
else {

$fatal = false;
$warn = false;
?>
	<h1>Проверка системных требований</h1>
	
	<div id="content">

		<div id="module" style="width:80%">
		
<table cellpadding="0" cellspacing="0" id="datatable_content" class="datatable">
	<thead>
		<tr>
			<th class="left">Требование</th>
			<th>Необходимое значение</th>
			<th>Установлено</th>
			<th class="right">Результат</th>
		</tr>
	</thead>
	
	<tbody>
		<tr>
			<td>Права доступа к файлу <code>config.php</code></td>
			<td>
				<?php
				$file = realpath($_SERVER['DOCUMENT_ROOT']).'/config.php';
				$filep = substr(sprintf('%o', fileperms($file)), -4);?>
				Чтение/запись для всех (0777)
			</td>
			<td>
				<?php echo $filep?>
			</td>
			<td>
				<?php
					if(!is_writable($file)) {
						echo 'Не хватает прав';
						$warn = true;
					} else {
						echo 'OK';
					}
				?>
			</td>
		</tr>
		
		<tr>
			<td>Права доступа к файлу <code>install.php</code></td>
			<td>
				<?php
				$file = realpath($_SERVER['DOCUMENT_ROOT']).'/install.php';
				$filep = substr(sprintf('%o', fileperms($file)), -4);?>
				Чтение/запись для всех (0777)
			</td>
			<td>
				<?php echo $filep?>
			</td>
			<td>
				<?php
					if(!is_writable($file)) {
						echo 'Не хватает прав';
						$warn = true;
					} else {
						echo 'OK';
					}
				?>
			</td>
		</tr>
	
		<tr>
			<td>Права доступа к папке <code>data</code></td>
			<td>
				<?php
				$file = realpath($_SERVER['DOCUMENT_ROOT']).'/data';
				$filep = substr(sprintf('%o', fileperms($file)), -4);?>
				Чтение/запись для всех (0777)
			</td>
			<td>
				<?php echo $filep?>
			</td>
			<td>
				<?php
					if(!is_writable($file)) {
						echo 'Не хватает прав';
						$fatal = true;
					} else {
						echo 'OK';
					}
				?>
			</td>
		</tr>
		
		<tr>
			<td>Права доступа к папке <code>Js</code></td>
			<td>
				<?php
				$file = realpath($_SERVER['DOCUMENT_ROOT']).'/Js';
				$filep = substr(sprintf('%o', fileperms($file)), -4);?>
				Чтение/запись для всех (0777)
			</td>
			<td>
				<?php echo $filep?>
			</td>
			<td>
				<?php
					if(!is_writable($file)) {
						echo 'Не хватает прав';
						$warn = true;
					} else {
						echo 'OK';
					}
				?>
			</td>
		</tr>
		
		<tr>
			<td>Права доступа к папке <code>Styles</code></td>
			<td>
				<?php
				$file = realpath($_SERVER['DOCUMENT_ROOT']).'/Styles';
				$filep = substr(sprintf('%o', fileperms($file)), -4);?>
				Чтение/запись для всех (0777)
			</td>
			<td>
				<?php echo $filep?>
			</td>
			<td>
				<?php
					if(!is_writable($file)) {
						echo 'Не хватает прав';
						$warn = true;
					} else {
						echo 'OK';
					}
				?>
			</td>
		</tr>
		
		<tr>
			<td>Права доступа к папке <code>templates</code></td>
			<td>
				<?php
				$file = realpath($_SERVER['DOCUMENT_ROOT']).'/templates';
				$filep = substr(sprintf('%o', fileperms($file)), -4);?>
				Чтение/запись для всех (0777)
			</td>
			<td>
				<?php echo $filep?>
			</td>
			<td>
				<?php
					if(!is_writable($file)) {
						echo 'Не хватает прав';
						$warn = true;
					} else {
						echo 'OK';
					}
				?>
			</td>
		</tr>
		
		<tr>
			<td>Права доступа к файлу <code>admin/.htpasswd</code></td>
			<td>
				<?php
				$file = realpath($_SERVER['DOCUMENT_ROOT']).'/admin/.htpasswd';
				$filep = substr(sprintf('%o', fileperms($file)), -4);?>
				Чтение/запись для всех (0777)
			</td>
			<td>
				<?php echo $filep?>
			</td>
			<td>
				<?php
					if(!is_writable($file)) {
						echo 'Не хватает прав';
						$fatal = true;
					} else {
						echo 'OK';
					}
				?>
			</td>
		</tr>
		
		<tr>
			<td>Права доступа к файлу <code>admin/.htaccess</code></td>
			<td>
				<?php
				$file = realpath($_SERVER['DOCUMENT_ROOT']).'/admin/.htaccess';
				$filep = substr(sprintf('%o', fileperms($file)), -4);?>
				Чтение/запись для всех (0777)
			</td>
			<td>
				<?php echo $filep?>
			</td>
			<td>
				<?php
					if(!is_writable($file)) {
						echo 'Не хватает прав';
						$fatal = true;
					} else {
						echo 'OK';
					}
				?>
			</td>
		</tr>
		
		<tr>
			<td>Проверка на корневую директорию</td>
			<td>
				<?php echo realpath($_SERVER['DOCUMENT_ROOT'])?>
			</td>
			<td>
				<?php echo realpath('.')?>
			</td>
			<td>
				<?php
					if(realpath($_SERVER['DOCUMENT_ROOT']) != realpath('.')) {
						echo 'Booot может работать только из корневой директории';
						$fatal = true;
					} else {
						echo 'OK';
					}
				?>
			</td>
		</tr>
		
		<tr>
			<td>Совместимая OS сервера</td>
			<td>
				Linux, Unix
			</td>
			<td>
				<?php echo PHP_OS?>
			</td>
			<td>
				<?php
					if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {
						echo 'Несовместимая с жизнью OS';
						$fatal = true;
					} else {
						echo 'OK';
					}
				?>
			</td>
		</tr>
		
		<tr>
			<td>Версия PHP</td>
			<td>
				>= 5.3.0
			</td>
			<td>
				<?php echo PHP_VERSION?>
			</td>
			<td>
				<?php
					if (version_compare(PHP_VERSION, '5.3.0', '<')) {
						echo 'Недостаточная версия PHP';
						$fatal = true;
					} else {
						echo 'OK';
					}
				?>
			</td>
		</tr>
		
		<tr>
			<td>Safe mode</td>
			<td>
				false
			</td>
			<td>
				<?php echo ini_get('safe_mode')?'true':'false'?>
			</td>
			<td>
				<?php
					if (ini_get('safe_mode')) {
						echo 'В safe mode ничего не работает, извините';
						$fatal = true;
					} else {
						echo 'OK';
					}
				?>
			</td>
		</tr>
		
		<tr>
			<td>Register globals</td>
			<td>
				false
			</td>
			<td>
				<?php echo ini_get('register_globals')?'true':'false'?>
			</td>
			<td>
				<?php
					if (ini_get('register_globals')) {
						echo 'Странно, но register globals оказались включены! Это fail';
						$fatal = true;
					} else {
						echo 'OK';
					}
				?>
			</td>
		</tr>
		
		<tr>
			<td>Magic quotes</td>
			<td>
				false
			</td>
			<td>
				<?php echo get_magic_quotes_gpc()?'true':'false'?>
			</td>
			<td>
				<?php
					if (get_magic_quotes_gpc()) {
						echo 'Magic quotes лучше выключить';
						$warn = true;
					} else {
						echo 'OK';
					}
				?>
			</td>
		</tr>
		
		<tr>
			<td>GD library</td>
			<td>
				Установлена
			</td>
			<td>
				<?php
					if (extension_loaded('gd') && function_exists('gd_info')) {
						echo 'Установлена';
					} else {
						echo 'Не найдена';
					}
				?>
			</td>
			<td>
				<?php
					if (!extension_loaded('gd') || !function_exists('gd_info')) {
						echo 'Для работы <a href="http://dustweb.ru/projects/tinymce_images/">Image Manager</a> нужна библиотека GD';
						$warn = true;
					} else {
						echo 'OK';
					}
				?>
			</td>
		</tr>
		
		<tr>
			<td>ImageMagick</td>
			<td>
				Установлен
			</td>
			<td>
				<?php
				$output = array();
				exec('convert 2>&1', $output);
				if( strstr(implode(' ', $output), 'ImageMagick') ) {
					echo 'Установлен';
					$im_exist = true;
				} else {
					echo 'Не найден';
					$im_exist = false;
					$warn = true;
				}
				
				?>
			</td>
			<td>
				<?php
					if (!$im_exist) {
						echo 'Без <a href="http://www.imagemagick.org/script/index.php">ImageMagick</a> все печально';
						$warn = true;
					} else {
						echo 'OK';
					}
				?>
			</td>
		</tr>
		
	</tbody>
	
	<tfoot>
		<tr>
			<td colspan="4" style="color:grey">
				<?php if(!$fatal && !$warn) {?>
				Все прошло отлично, можно продолжать
				<?php } elseif (!$fatal && $warn) {?>
				Есть важные упущения, рекомендуется их устранить, хотя можно продолжить на свой страх и риск
				<?php } else {?>
				Найдены системные несоответствия, установка невозможна
				<?php }?>
			</td>
		</tr>
	</tfoot>
</table>

<?php if(!$fatal) {?>

<?php if(!$warn) {?>
<button id="nextButton" style="margin:2em 0 0 0;">Установка</button>
<?php } else {?>
<button id="nextButton" style="margin:2em 0 0 0;">Все равно установить</button>
<?php }?>


<script type="text/javascript">
$('#nextButton').button({
	icons: { primary: 'ui-icon-arrowthick-1-e' }
}).click(function(){
	document.location = '?step=2';
});
</script>

<?php }

}
?>
			
		</div>
		
				
	</div>
	
	<div id="footer">
		<span>© 2009—2010  —  <a href="http://booot.ru/">Booot</a>™</span>
		<span><a href="mailto:mail@weboutsource.ru?subject=От администратора booot (Booot CMS)&body=Максимально полное описание проблемы или вопроса по работе Booot CMS на сайте booot:%0A%0A" title="Если вы считаете что нашли ошибку в системе, просьба описать ее как можно подробнее">Письмо разработчикам</a></span>
	</div>
</body>
</html>