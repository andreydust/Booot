<?php

/**
 * Возвращает указанный шаблон с подставленными данными из $vars
 *
 * @param string $name Имя шаблона из папки /templates
 * @param array $vars Массив переменных для шаблона
 * @return string
 */
function tpl($name, $vars=array()) {
	$super_secret_system_name = $name;
	extract($vars);
	ob_start();
	
	$sdir = '/themes/'.$GLOBALS['config']['site']['theme'];
	
	if(is_file(DIR.$sdir.'/templates/'.$super_secret_system_name.'.php')) {
		include DIR.$sdir.'/templates/'.$super_secret_system_name.'.php';
	} else {
		error('Не найден шаблон <code style="font-weight:bold">'.$super_secret_system_name.'.php</code>');
	}
	
	$ret = ob_get_contents();
	ob_end_clean();
	
	//Подмена вставленных блоков и форм {block:block_name_or_id} {form:form-name-or-id}
	preg_match_all('/\{(block|form):([\w_-]+)\}/simx', $ret, $result, PREG_PATTERN_ORDER);
	if(isset($result[0]) && !empty($result)) {
		for($i=0,$c=count($result[0]); $i<$c; $i++){
			if(isset($result[1][$i])) {
				if($result[1][$i] == 'block') {
					$insert = block($result[2][$i], 1);
				} else if($result[1][$i] == 'form') {
					$insert = form($result[2][$i], 1);
				}
				$ret = str_replace($result[0][$i], $insert, $ret);
			}
		}
	}
	
	//Сжатие и склейка CSS и JS в файле parts/head.php
	if($super_secret_system_name == 'parts/head') {
		timegen('minify');
		
		//Убираем комментарии и хаки (те что обычно для ie)
		$nocomment = preg_replace('/<!--.*?-->/si', '', $ret);
		
		//Находим и заменяем все «screen» css файлы на один сжатый
		preg_match_all('/<link.*href.*=.*"(.*\.css[^"]*)".*media="screen".*>/i', $nocomment, $result, PREG_PATTERN_ORDER);
		$allcssTime = @filemtime(DIR.'/css/allcss_'.$GLOBALS['config']['site']['theme'].'.css');
		if(!$allcssTime) $mustReBuild = true;
		else $mustReBuild = false;
		foreach ($result[1] as $csslink) {
			$existFileTime = @filemtime(DIR.$csslink);
			if(!$existFileTime) continue;
			if($allcssTime < $existFileTime) $mustReBuild = true;
		}
		if($mustReBuild) require_once DIR.'/system/lib/minifiers/cssmin.php';
		$allcss = ''; $c=0; $tc = count($result[0]);
		foreach ($result[0] as $k=>$linktag) {
			if(++$c == $tc) $ret = str_replace($linktag, '<link href="/css/allcss_'.$GLOBALS['config']['site']['theme'].'.css" rel="stylesheet" type="text/css" media="screen" />', $ret);
			else $ret = str_replace($linktag, '', $ret);
			if($mustReBuild) {
				$css = file_get_contents(DIR.$result[1][$k]);
				$pathcss = pathinfo($result[1][$k]);
				$pathcss = $pathcss['dirname'];
				//Замена путей
				preg_match_all('/url *?\( *?["\']?(.*[^\'"])["\']?\)/i', $css, $url_result, PREG_PATTERN_ORDER);
				foreach ($url_result[0] as $urlk=>$urldef) {
					//Если путь относительный, меняем на абсолютный
					if($url_result[1][$urlk][0] != '/') {
						$css = str_replace($urldef, 'url('.$pathcss.'/'.$url_result[1][$urlk].')', $css);
					}
				}
				
				$allcss .= CssMin::minify($css)."\r\n";
			}
		}
		if($mustReBuild) {
			file_put_contents(DIR.'/css/allcss_'.$GLOBALS['config']['site']['theme'].'.css', $allcss);
			chmod(DIR.'/css/allcss_'.$GLOBALS['config']['site']['theme'].'.css', 0755);
			$gz = gzopen(DIR.'/css/allcss_'.$GLOBALS['config']['site']['theme'].'.css.gz','w9');
			gzwrite($gz, $allcss);
			gzclose($gz);
			chmod(DIR.'/css/allcss_'.$GLOBALS['config']['site']['theme'].'.css.gz', 0755);
		}
		
		//Находим и заменяем все js файлы на один сжатый
		preg_match_all('/<script.*src="(.*\.js[^"]*)".*>/i', $nocomment, $result, PREG_PATTERN_ORDER);
		$alljsTime = @filemtime(DIR.'/js/alljs_'.$GLOBALS['config']['site']['theme'].'.js');
		if(!$alljsTime) $mustReBuild = true;
		else $mustReBuild = false;
		foreach ($result[1] as $jslink) {
			$existFileTime = @filemtime(DIR.$jslink);
			if(!$existFileTime) continue;
			if($alljsTime < $existFileTime) $mustReBuild = true;
		}
		if($mustReBuild) require_once DIR.'/system/lib/minifiers/jsmin.php';
		$alljs = ''; $c=0; $tc = count($result[0]);
		foreach ($result[0] as $k=>$scripttag) {
			if(++$c == $tc) $ret = str_replace($scripttag, '<script type="text/javascript" src="/js/alljs_'.$GLOBALS['config']['site']['theme'].'.js"></script>', $ret);
			else $ret = str_replace($scripttag, '', $ret);
			if($mustReBuild) {
				$js = file_get_contents(DIR.$result[1][$k]);
				$alljs .= JSMin::minify($js)."\r\n";
			}
		}
		if($mustReBuild) {
			file_put_contents(DIR.'/js/alljs_'.$GLOBALS['config']['site']['theme'].'.js', $alljs);
			chmod(DIR.'/js/alljs_'.$GLOBALS['config']['site']['theme'].'.js', 0755);
			$gz = gzopen(DIR.'/js/alljs_'.$GLOBALS['config']['site']['theme'].'.js.gz','w9');
			gzwrite($gz, $alljs);
			gzclose($gz);
			chmod(DIR.'/js/alljs_'.$GLOBALS['config']['site']['theme'].'.js.gz', 0755);
		}
		
		timegen('minify',1);
	}
	
	return $ret;
}