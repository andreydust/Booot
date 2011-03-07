<?php

/**
 * Выводит блок с подсказками
 *
 * @param array( title, text [, link] ) $data
 */
function hint($data) {
	if(empty($data)) return '';
	return tpl('widgets/hint', $data);
}

/**
 * Выводит меню
 *
 * @param array( title, text [, link] ) $data
 */
function menu($modules) {
	if(empty($modules)) return '';
	
	//Не показываем модули с сортировкой 0
	if(isset($modules['modules'][0])) unset($modules['modules'][0]);
	
	//Не показываем скрытые модули
	foreach ($modules['modules'] as $k=>$v) {
		if($v['hide']) unset($modules['modules'][$k]);
	}
	
	return tpl('widgets/menu', $modules);
}