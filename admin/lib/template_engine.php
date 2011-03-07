<?php

/**
 * Возвращает указанный шаблон с подставленными данными из $vars
 *
 * @param string $name Имя шаблона из папки /admin/tpls
 * @param array $vars Массив переменных для шаблона
 * @return string
 */
function tpl($name, $vars=array()) {
	$super_secret_system_name = $name;
	extract($vars);
	ob_start();
	require DIR.'/admin/tpls/'.$super_secret_system_name.'.php';
	$ret = ob_get_contents();
	ob_end_clean();
	return $ret;
}