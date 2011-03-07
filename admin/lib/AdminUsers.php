<?php
class AdminUsers {
	
	public $user;
	private $allAlowed = array('About');
	
	function __construct() {
		if(!session_id()) session_start();
		
		if(isset($_GET['logout'])) $this->logout();
		
		if($this->isLogged()) {
			$this->auth();
		} else {
			$this->login();
		}
	}
	
	function isLogged() {
		if(isset($_SESSION['user'])) return true;
		else return false;
	}
	
	function isAllowed($module) {
		if($_SESSION['user']['type'] == 'a') return true;
		
		if(isset($_SESSION['user']['access'][$module]) || in_array($module, $this->allAlowed)) return true;
		else return false;
	}
	
	
	/**
	 * Выводит блок пользователя
	 */
	function userBar() {
		return tpl('widgets/userbar', array(
			'login'	=> $this->user['login'],
			'user'	=> $this->user
		));
	}
	
	/**
	 * Логин
	 */
	function login() {
        if (@!$_SESSION['godmode_ref'])
            $_SESSION['godmode_ref'] = @$_SERVER['HTTP_REFERER'];
		if(!empty($_POST['login']) && !empty($_POST['password'])) {
			$user = db()->query_first("SELECT * FROM `prefix_admin_users` WHERE `login` = '".q($_POST['login'])."' AND `password` = '".md5($_POST['password'])."'");
			if(!empty($user)) {
				$_SESSION['user'] = $user;
				$_SESSION['useradminkey'] = md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'].$_SESSION['user']['password'].'douglas');
				$this->auth();
				return true;
			}
			else $this->errorLogin();
		} else {
			$title = 'Система администрирования сайта';
			$login = tpl('widgets/login', array());
			echo tpl('index', array(
				'title'		=> $title,
				'h1'		=> $title,
				'menu'		=> '',
				'userbar'	=> '',
				'hint'		=> '',
				'sidemenu'	=> '',
				'content'	=> $login
			));
			exit();
		}
	}
	
	/**
	 * Авторизация
	 */
	function auth() {
		if($_SESSION['useradminkey'] != md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'].$_SESSION['user']['password'].'douglas')) $this->logout();
		$_SESSION['user'] = db()->query_first("SELECT * FROM `prefix_admin_users` WHERE `id` = ".((int)$_SESSION['user']['id'])." AND `login` = '".q($_SESSION['user']['login'])."' AND `password` = '".q($_SESSION['user']['password'])."'");
		if(empty($_SESSION['user'])) $this->logout();
		$_SESSION['user']['access'] = unserialize($_SESSION['user']['access']);
		$this->user = $_SESSION['user'];
	}
	
	/**
	 * Указание браузеру авторизоваться по ложным данным
	 */
	function logout() {
		unset($_SESSION['user']);
		unset($_SESSION['useradminkey']);
		session_destroy();
		header('Location: /');
		exit();
	}
	
	/**
	 * Если пользователь авторизовался, но в базе такого нет
	 */
	function errorLogin() {
		$title = 'Система администрирования сайта';
		$login = tpl('widgets/login', array());
		echo tpl('index', array(
			'title'		=> $title,
			'h1'		=> '<span style="color:red">Ошибка авторизации</span>',
			'menu'		=> '',
			'userbar'	=> '',
			'hint'		=> '',
			'sidemenu'	=> '',
			'content'	=> $login
		));
		exit();
	}
	
}