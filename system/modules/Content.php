<?php
//vim:ts=4:sw=4:noet

class Content {
	
	private $table = 'content';
	
	private $current_document;
	
	function __construct() {
		$this->current_document = end($GLOBALS['path']);
	}
	
	public function Output() {
		$this->SEO();
		
		return tpl('page', array(
			'title'	=> $this->seo['title'],
			'name'	=> $this->current_document['name'],
			'text'	=> $this->current_document['text'],
		));
	}
	
	private function SEO($id=false) {
		if($id===false) $id = $this->current_document['id'];
		else $id = (int)$id;
		if($id==0) { $this->seo = array(); return false; }
		$this->seo = db()->query_first("SELECT * FROM `prefix_seo` WHERE `module` = '".__CLASS__."' AND `module_id` = ".$id." AND `module_table` = '".$this->table."'");
		if(!empty($this->seo)) {
			//Keywords
			if(!empty($this->seo['keywords'])) {
				$GLOBALS['head_add'] .= "\r\n".'<meta name="keywords" content="'.htmlspecialchars($this->seo['keywords']).'" />';
			}
			//Description
			if(!empty($this->seo['description'])) {
				$GLOBALS['head_add'] .= "\r\n".'<meta name="description" content="'.htmlspecialchars($this->seo['description']).'" />';
			}
			$GLOBALS['head_add'] .= "\r\n";
			//Title
			if(!empty($this->seo['title'])) {
				$this->seo['title'] = $this->seo['title'];
			} else {
				$this->seo['title'] = $this->current_document['name'].' — '.$GLOBALS['config']['site']['title'];
			}
		} else {
			$this->seo['title'] = $this->current_document['name'].' — '.$GLOBALS['config']['site']['title'];
		}
	}
	
	function MainMenu() {
		$pages = $GLOBALS['data']->GetData($this->table, "AND `show` = 'Y'");
		
		$menuByTop = array();
		$activeSet = false;
		foreach($pages as $id=>$i) {
			if($i['showmenu'] == 'N') continue;
			//Отмечаем текущую страницу
			if(in_array($i['nav'], $GLOBALS['path_nav'])) {
				$i['active'] = true;
				$activeSet = true;
			}
			else $i['active'] = false;
			
			$menuByTop[$i['top']][] = $i;
		}
		
		if(empty($menuByTop[0])) return tpl('parts/mainmenu', array('menu'=>array()));
		
		//if(!$activeSet) $menuByTop[0][0]['active'] = true;
		
		foreach($menuByTop[0] as $top=>$i) {
			$item['root'] = $i;
			$item['sub'] = array();
			
			//Проверка на подменю из модуля
			if(!empty($i['module']) && $i['active']) {
				$obj = giveObject($i['module']);
				if(method_exists($obj, 'SubMenu')) {
					$item['sub'] = $obj->SubMenu();
				}
			} else {
				if(isset($menuByTop[$i['id']]))
				foreach($menuByTop[$i['id']] as $id=>$j) {
					$item['sub'][] = $j;
				}
			}
			
			$menu[] = $item;
		}
		
		return tpl('parts/mainmenu', array('menu'=>$menu));
	}
	
	
	function SubMenu($id=0) {
		
		if($id != 0) {
			$toppage = $GLOBALS['data']->GetDataById($this->table, (int)$id);
		} else {
			end($GLOBALS['path']);
			prev($GLOBALS['path']);
			$toppage = current($GLOBALS['path']);
			if(empty($toppage)) {
				$toppage = $GLOBALS['path'][0];
			}
		}
		
		$activeSet = false;
		$menu = array();
		
		//Если модуль нам попался невзначай
		if(!empty($toppage['module'])) {
			if(class_exists($toppage['module'])) {
				$module = giveObject($toppage['module']);
				if(method_exists($module, 'IntegrationMenu')) {
					$menu = $module->IntegrationMenu();
					foreach ($menu as $i) {
						if(isset($i['active']) && $i['active']) $activeSet = true;
					}
					return tpl('parts/submenu', array('menu'=>$menu, 'toppage'=>$toppage, 'activeSet'=>$activeSet));
				}
			}
		}
		
		//Подменю уже просто из контента
		$pages = $GLOBALS['data']->GetData($this->table, 'AND `top` = '.(int)$toppage['id']);
		foreach($pages as $id=>$i) {
			if($i['showmenu'] == 'N') continue;
			//Отмечаем текущую страницу
			if(in_array($i['nav'], $GLOBALS['path_nav'])) {
				$i['active'] = true;
				$activeSet = true;
			}
			else $i['active'] = false;
			
			$i['link'] = linkById($i['id']);
			
			$menu[] = $i;
		}
		
		return tpl('parts/submenu', array('menu'=>$menu, 'toppage'=>$toppage, 'activeSet'=>$activeSet));
	}
	
	/**
	 * Перезвон
	 */
	function ajaxCallMe() {
		$name = htmlspecialchars(trim($_POST['name']));
		$phone = htmlspecialchars(trim($_POST['phone']));
		
		if(empty($name) || empty($phone)) {
			echo 'fields_error';
			exit();
		}
		
		$body = 'Посетитель сайта '.$_SERVER['SERVER_NAME'].' '.$name.' попросил перезвонить вас ему по телефону '.$phone.'';
		
		$mail = new ZFmail($GLOBALS['config']['site']['admin_mail'], 'noreply@'.$_SERVER['SERVER_NAME'], 'Прошу перезвонить, '.$name, $body);
		if($mail->send()) {
			echo 'ok';
		} else {
			echo 'error';
		}
		exit();
	}
	
	/**
	 * Хлебные крошки
	 */
	function breadCrumbs() {
		if(empty($GLOBALS['path'])) return '';
		
		$crumbs = array();
		$crumbs[] = array('name'=>'Главная', 'link'=>'/');
		$i = 0; $pc = count($GLOBALS['path']);
		foreach ($GLOBALS['path'] as $crumb) {
			$crumbs[] = array('name'=>$crumb['name'], 'link'=>linkById($crumb['id']));
			$i++;
		}
		
		$last = end($GLOBALS['path']);
		
		//А вдруг модуль
		if(isset($last['module']) && !empty($last['module'])) {
			if(class_exists($last['module'])) {
				$module = giveObject($last['module']);
				if(method_exists($module, 'breadCrumbs')) {
					$moduleCrumbs = $module->breadCrumbs();
					$crumbs = array_merge($crumbs, $moduleCrumbs);
				}
			}
		}
		
		$crumbs = array_filter($crumbs);
		
		foreach ($crumbs as $k=>$crumb) $crumbs[$k]['active'] = false;
		$crumbs[count($crumbs)-1]['active'] = true;
		
		return tpl('parts/breadcrumbs', array('crumbs'=>$crumbs));
	}

	function ajaxGodmode()
	{
		$suspend = false;
		if (@$_POST['suspend'])
			$suspend = true;
		$_SESSION['godmode_suspended'] = $suspend;
		giveJSON(array('suspended'=>$_SESSION['godmode_suspended']));
	}
	
}
