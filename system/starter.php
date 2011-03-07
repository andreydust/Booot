<?php

if (preg_match('%/([^?#]+)?%i', $_SERVER['REQUEST_URI'], $regs)) {
	
	$GLOBALS['path'] = array();
	$GLOBALS['path_nav'] = array();
	
	if(isset($regs[1])) {
		$uri = $regs[1];
		$uriPath = explode('/',$uri);
		$uriPath = array_filter($uriPath);
	}
	
	$siteDocuments = $GLOBALS['data']->GetData('content', "AND `show` = 'Y'");
	
	foreach($siteDocuments as $i) {
		$siteDocumentsByParent[$i['top']][$i['id']] = $i;
		if(!isset($siteDocumentsByModule[$i['module']])) {
			$siteDocumentsByModule[$i['module']] = $i;
		}
	}
	
	
	/**
	 * Ссылка по ID записи
	 *
	 * @param int $id ID записи
	 */
	function linkById($id) {
		global $siteDocuments,$siteDocumentsByParent;
		if($id==0) return;
		$link = linkById($siteDocuments[$id]['top']).'/'.$siteDocuments[$id]['nav'];
		return $link;
	}
	
	/**
	 * Ссылка по имени модуля
	 *
	 * @param int $id ID записи
	 */
	function linkByModule($module) {
		global $siteDocumentsByModule;
		$link = linkById($siteDocumentsByModule[$module]['id'],true);
		return $link;
	}
	
	//Если запрос пришел аяксом
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		$module = $uriPath[0];
		$method = 'ajax'.$uriPath[1];
		if(class_exists($module)) {
			$moduleObj = giveObject($module);
			if(method_exists($moduleObj, $method)) {
				//Финальный вывод модуля здесь!
				echo $moduleObj->$method();
			} else {
				error('В модуле '.$module.' не задан метод '.$method);
			}
		} else {
			error('Модуль '.$module.' не найден!');
		}
		exit();
	}
	
	//Главная страница
	if(!isset($regs[1])) {
		$module = new MainPage();
		echo $module->Output();
		return;
	}
	
	
	/**
	 * Проверка корректности пути и составление карты пути ($GLOBALS['path'])
	 *
	 * @param int $urinum
	 * @param int $parent
	 */
	function validateURI($urinum=0, $parent=0) {
		global $uriPath, $siteDocumentsByParent;
		
		if($urinum > count($uriPath)-1) return 'content';
		
		if(isset($siteDocumentsByParent[$parent])) {
			
			foreach($siteDocumentsByParent[$parent] as $id=>$i) {
				
				//Все в порядке, продолжаем
				if($i['nav'] == $uriPath[$urinum]) {
					
					$GLOBALS['path'][] = $i;
					$GLOBALS['path_nav'][] = $i['nav'];
					
					//Обнаружен модуль, дальше не нужно проверять URI
					if(!empty($i['module'])) {
						return 'module';
					}
					
					return validateURI($urinum+1, $id);
				}
			}
			
			return false;
		}
	}
	
	/**
	 * Запуск модуля
	 *
	 * @param string $name
	 */
	function runModule($name) {
		if(class_exists($name)) {
			$module = giveObject($name);
			if(method_exists($module, 'Output')) {
				
				//Финальный вывод модуля здесь!
				echo $module->Output();
				
			} else {
				error('В модуле '.$name.' не задан метод Output, он должен возвращать вывод модуля');
			}
		} else {
			error('Модуль '.$name.' не найден!');
		}
	}
	
	switch (validateURI()) {
		case 'content':
			$module = new Content();
			echo $module->Output();
		break;
		
		case 'module':
			runModule($GLOBALS['path'][count($GLOBALS['path'])-1]['module']);
		break;
		
		default:
			page404();
	}
	
} else {
	page404();
}
