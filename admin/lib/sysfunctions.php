<?php

/**
 * Время выполения части скрипта
 *
 * @param $partname string <p>Имя засекаемого блока</p>
 * @param $stop bool Окончание измерения
 * @return void
 */
function timegen($partname='', $stop=false) {
	if(!$GLOBALS['config']['develop']) return false;
	
	if(!isset($GLOBALS['timegen'])) $GLOBALS['timegen'] = array();
	if(!isset($GLOBALS['timegen'][$partname])) $GLOBALS['timegen'][$partname] = array();
	
	if(!$stop) {
		$GLOBALS['timegen'][$partname]['start'] = microtime(true);
		return true;
	} else {
		$GLOBALS['timegen'][$partname]['time'] = microtime(true)-$GLOBALS['timegen'][$partname]['start'];
	}
	
	if(!isset($GLOBALS['timegen'][$partname]['result'])) $GLOBALS['timegen'][$partname]['result'] = 0;
	$GLOBALS['timegen'][$partname]['result'] += $GLOBALS['timegen'][$partname]['time'];

	return $GLOBALS['timegen'][$partname]['time'];
}


/**
 * Отображает блок с результатами измерений
 *
 * @return void
 */
function timegen_result() {
	if(!$GLOBALS['config']['develop']) return false;
	
	if(
		!isset($GLOBALS['timegen']) ||
		!is_array($GLOBALS['timegen']) ||
		count($GLOBALS['timegen']) == 0
	) {
		echo '
		<script type="text/javascript">
			if(typeof(console) != "undefined") {
				console.log("'.$GLOBALS['config']['msg']['timegen_nb'].'");
			}
		</script>';
		return false;
	}
	
	$log = array();
	$log[] = $GLOBALS['config']['msg']['timegen'];
	foreach($GLOBALS['timegen'] as $k=>$v) {
		if(!isset($v['time'])) {
			$current_result = microtime(true)-$v['start'];
			$log[] = $k.': '.sprintf('%.5f',$current_result).' '.$GLOBALS['config']['msg']['timegen_ns'];
		} else {
			$log[] = $k.': '.sprintf('%.5f',$v['result']);
		}
	}
	$log[] = 'Текущее количество запросов: '.$GLOBALS['query_count'];
	if($GLOBALS['config']['db']['show_query_devmode']) {
		echo implode("<br />",$GLOBALS['query_log']);
	}
	
	//Память
	$log[] = 'Пик памяти: '.bytes_to_str(memory_get_peak_usage());
	
	echo '
		<script type="text/javascript">
			if(typeof(console) != "undefined") {
				console.log('.json_encode($log).');
			}
		</script>';
}


/**
 * Возвращает максимальный размер для загружаемых файлов
 *
 * @param $bytes boolean Если да, то вернет в байтах
 * @return void
 */
function get_max_filesize($bytes=false) {
	$sizes = array(
		str_to_bytes(ini_get('memory_limit')),
		str_to_bytes(ini_get('post_max_size')),
		str_to_bytes(ini_get('upload_max_filesize'))
	);
	$maxfile = min($sizes);
	
	if(!$bytes) $ret = bytes_to_str($maxfile);
	else $ret = $maxfile;
	
	return $ret;
}


function bytes_to_str($bytes) {
	$d = '';
	if($bytes >= 1048576) {
		$num = $bytes/1048576;
		$d = 'Mb';
	} elseif($bytes >= 1024) {
		$num = $bytes/1024;
		$d = 'kb';
	} else {
		$num = $bytes;
		$d = 'b';
	}
	
	return number_format($num, 2, ',', ' ').$d;
}


function str_to_bytes($val) {
    $val = trim(str_replace(',','.',$val));
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // 'G' модификатор доступен начиная с PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}


/**
 * Перевод фразы через Google Translate
 *
 * @param $ru_str string Фраза по-русски
 * @return string
 */
function translate($ru_str) {
		$curlHandle = curl_init(); // init curl
        // options
		$postData=array();
		$postData['client']= 't';
		$postData['text']= $ru_str;
		$postData['hl'] = 'en';
		$postData['sl'] = 'ru';
		$postData['tl'] = 'en';
        curl_setopt($curlHandle, CURLOPT_URL, 'http://translate.google.com/translate_a/t'); // set the url to fetch
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array(
        	'User-Agent: Mozilla/5.0 (X11; U; Linux i686; ru; rv:1.9.1.4) Gecko/20091016 Firefox/3.5.4',
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Accept-Language: ru,en-us;q=0.7,en;q=0.3',
			'Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7',
			'Keep-Alive: 300',
			'Connection: keep-alive'
        ));
        curl_setopt($curlHandle, CURLOPT_HEADER, 0); // set headers (0 = no headers in result)
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1); // type of transfer (1 = to string)
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 10); // time to wait in
		curl_setopt($curlHandle, CURLOPT_POST, 0);
		if ( $postData!==false ) {
			curl_setopt($curlHandle, CURLOPT_POSTFIELDS, http_build_query($postData));
    	}
    	
        $content = curl_exec($curlHandle); // make the call
        curl_close($curlHandle); // close the connection
        $content = str_replace(',,',',"",',$content);
        $content = str_replace(',,',',"",',$content);
        $content = str_replace(',]',',""]',$content);
        $content = str_replace('[,','["",',$content);
    	$result = json_decode($content);
    	return $result[0][0][0];
}


function makeURI($str) {
	$str = translate($str);
	return strtolower(preg_replace('/[^\w]+/i', '-', $str));
}



function getVar($name) {
	$db = db();
	$db->query("SELECT * FROM `prefix_data` WHERE `key` = '".$name."'");
	if($db->row_count() == 0) return false;
	$i = $db->fetch();
	return $i['data'];
}

function setVar($name, $data) {
	$db = db();
	$db->query("SELECT * FROM `prefix_data` WHERE `key` = '".$name."'");
	if($db->row_count() == 0) {
		$db->query("INSERT INTO `prefix_data` (`key`,`data`) VALUES ('$name', '$data')");
	} else {
		$db->query("UPDATE `prefix_data` SET `data` = '$data' WHERE `key` = '$name'");
	}
	return true;
}


function giveJSON($data) {
	if(!is_array($data)) return false;
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-type: application/json');
	echo json_encode($data);
	exit();
}

function q($str) {
	return mysqli_real_escape_string(db()->link, $str);
}



