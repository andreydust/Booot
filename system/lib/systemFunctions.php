<?php

/**
 * Берем функции из админки (тоже очень полезные)
 */
require_once DIR.'/admin/lib/sysfunctions.php';


/**
 * Если объект уже создан, отдает. Если нет, то создаст и отдаст.
 *
 * @param $classname
 * @return object
 */
function giveObject($classname) {
	if(!isset($GLOBALS['modules'])) $GLOBALS['modules'] = array();
	
	if(!isset($GLOBALS['modules'][$classname])) {
		if(!class_exists($classname)) {
			$array_debug  = debug_backtrace();
			error('Не могу найти объект '.$classname.' в файле <code style="font-weight:bold;">'.$array_debug[0]['file'].'</code> на строке: <code style="font-weight:bold;">'.$array_debug[0]['line'].'</code>');
		}
		$GLOBALS['modules'][$classname] = new $classname();
	}
	
	return $GLOBALS['modules'][$classname];
}


$modulesSettings = array();

function getSet($module, $callname, $default='') {
	global $modulesSettings;
	if(empty($modulesSettings)) {
		$sets = db()->rows("SELECT * FROM `prefix_settings`", MYSQL_ASSOC);
		foreach ($sets as $k=>$v) {
			$modulesSettings[$v['module']][$v['callname']] = $v;
		}
	}
	
	if(isset($modulesSettings[$module][$callname])) return $modulesSettings[$module][$callname]['value'];
	else return $default;
}


/**
 * Кэш блоков на время выполнения
 */
$blocksByCallname = $blocksById = array();
/**
 * Возвращает содержимое блока
 *
 * @param int $id ID блока
 * @param bool $return Вернуть вызвавшему, иначе echo
 */
function block($id, $return=false, $adminEditableInline=false) {
	global $blocksByCallname, $blocksById;
	if(empty($blocksByCallname)) {
		$blocks = $GLOBALS['data']->GetData('blocks');
		foreach ($blocks as $b) {
			$blocksByCallname[$b['callname']] = $b;
			$blocksById[$b['id']] = $b;
		}
	}
	
	if(is_numeric($id)) {
		if(isset($blocksById[$id])) $block = $blocksById[$id];
		else error('Не нашел блок '.$id);
	} else {
		if(isset($blocksByCallname[$id])) $block = $blocksByCallname[$id];
		else error('Не нашел блок №'.$id);
	}
	
	//Если админ
	if (!session_id()) session_start();
	if(isset($_SESSION['user']['type']) && !$return) {
		$is_admin = ($_SESSION['user']['type'] == 'a');
		if($is_admin && @!$_SESSION['godmode_suspended']) {
			//Если html содержимое
			if(mb_strlen(strip_tags($block['text'])) != mb_strlen($block['text'])) {
				$edit_block_link = '/admin/?module=Blocks&method=Info&edit_text='.$block['id'];
			} else {
				$edit_block_link = '/admin/?module=Blocks#open'.$block['id'];
			}
			$block['text'] = 
				'<div style="border:dashed 1px grey; position:relative; '.($adminEditableInline?'display:inline-block;':'').'">
					'.$block['text'].'
					<a target="_blank" style="position:absolute; top:0; right:-8px; z-index:100;" href="'.$edit_block_link.'" title="Редактировать">
						<img src="/admin/images/icons/pencil.png" alt="Редактировать" />
					</a>
				</div>';
		}
	}
	
	if($block['show'] == 'Y') {
		if($return) return $block['text'];
		else echo $block['text'];
	} else {
		return false;
	}
}


/**
 * Создает форму из модуля «Формы»
 *
 * @param int $id ID формы
 * @param bool $return Вернуть вызвавшему, иначе echo
 */
function form($id, $return=false) {
	
	$cutValueLength = 10240;
	$emptyValueAlert = 'Пожалуйста, заполните поле «{label}»';
	$successMsg = 'Спасибо за сообщение!';
	
	if(!is_numeric($id)) {
		$form = $GLOBALS['data']->GetData('forms', "AND `callname` = '".q($id)."'");
		$form = current($form);
		if(empty($form)) error('Не нашел форму '.$id);
	} else {
		$form = $GLOBALS['data']->GetDataById('forms', $id);
		if(empty($form)) error('Не нашел форму №'.$id);
	}
	//Генерация полей
	$fields = $GLOBALS['data']->GetData('forms_fields', "AND `form` = $form[id] AND `show` = 'Y'");
	$html = $js = $as_js = '';
	
	$mailFields = array(); $p = false; $error = false;
	if(!empty($_POST)) {
		$p = true;
	}
	
	$requiredCount = 0;
	//Антиспам
	$timeCheck = time();
	if(!session_id()) session_start();
	if(!$p)	$_SESSION['formTimeCheck'][$id] = $timeCheck;
	$as = array(
		array(
			'type' => 'text',
			'label' => 'Имя',
			'name' => strtolower(str_rot13(str_replace('.', '', $_SERVER['SERVER_NAME']))),
			'default' => ''
		),
		array(
			'type' => 'textarea',
			'label' => 'Сообщение',
			'name' => strtolower(str_rot13(strstr($GLOBALS['config']['site']['admin_mail'], '@', true))),
			'default' => ''
		),
		array(
			'type' => 'text',
			'label' => 'Почта',
			'name' => 'mails',
			'default' => ''
		),
		array(
			'type' => 'text',
			'label' => 'Email',
			'name' => 'emails',
			'default' => ''
		)
	);
	
	$c = 0;
	foreach ($fields as $field) {
		
		//Заполнение полей
		$checked = $value = '';
		if(isset($_POST[$field['name']])) {
			if($field['type'] == 'checkbox') {
				$checked = 'checked="checked"';
			} else {
				$value = htmlspecialchars(substr(trim($_POST[$field['name']]), 0, $cutValueLength));
			}
		} else {
			$value = htmlspecialchars($field['default']);
		}
		
		//Антиспам поля
		if(isset($as[$c])) {
			$html .= '
					<label for="autoform_'.$as[$c]['name'].'" class="required">'.$as[$c]['label'].' <span class="jep">*</span></label>
					<input type="text" name="'.$as[$c]['name'].'" id="autoform_'.$as[$c]['name'].'" value="'.$value.'" />
				';
			$selector = str_split('#autoform_'.$as[$c]['name'].', label[for=autoform_'.$as[$c]['name'].']', rand(2, 10));
			$as_js .= '
					$("'.implode('"+"', $selector).'").hide();';
		}
		$c++;
		
		//Обязательное поле
		if($field['required'] == 'Y') { $required = 'required'; $requiredCount++; }
		else $required = '';
		
		//Содержание письма, для отправки, проверка
		if(!empty($_POST)) {
			//Проверка на заполнение обязательного поля
			if($field['type'] == 'checkbox') {
				if(!empty($required) && empty($checked)) $error = true;
			} else {
				if(!empty($required) && empty($value)) $error = true;
			}
		}
		
		switch ($field['type']) {
			case 'text':
				if($p) {
					$mailFields[] = $field['label'].': '.$value;
				}
				$html .= '
					<label for="autoform_'.$field['name'].'" class="'.$required.'">'.$field['label'].($required?' <span class="jep">*</span>':'').'</label>
					<input type="text" name="'.$field['name'].'" id="autoform_'.$field['name'].'" value="'.$value.'" class="form-control" />
				';
				if(!empty($required)) {
					$js .= '
					if($.trim($("#autoform_'.$field['name'].'").val()) == "") {
						alert("'.str_replace('{label}', $field['label'], $emptyValueAlert).'");
						$("#autoform_'.$field['name'].'").focus();
						return false;
					}';
				}
			break;
			
			case 'textarea':
				if($p) {
					$mailFields[] = $field['label'].": \r\n".str_repeat('—', 10)."\r\n".$value."\r\n".str_repeat('—', 10);
				}
				$html .= '
					<label for="autoform_'.$field['name'].'" class="'.$required.'">'.$field['label'].($required?' <span class="jep">*</span>':'').'</label>
					<textarea name="'.$field['name'].'" id="autoform_'.$field['name'].'" class="form-control">'.$value.'</textarea>
				';
				if(!empty($required)) {
					$js .= '
					if($.trim($("#autoform_'.$field['name'].'").val()) == "") {
						alert("'.str_replace('{label}', $field['label'], $emptyValueAlert).'");
						$("#autoform_'.$field['name'].'").focus();
						return false;
					}';
				}
			break;
			
			case 'checkbox':
				if($p) {
					if(!empty($checked)) {
						$mailFields[] = $field['label'].': Да';
					} else {
						$mailFields[] = $field['label'].': Не указано';
					}
				}
				$html .= '
				<div class="autoformCheckbox">
					<input type="checkbox" name="'.$field['name'].'" id="autoform_'.$field['name'].'" value="Y" '.$checked.' />
					<label for="autoform_'.$field['name'].'" class="'.$required.'">'.$field['label'].($required?' <span class="jep">*</span>':'').'</label>
				</div>
				';
				if(!empty($required)) {
					$js .= '
					if(!$("#autoform_'.$field['name'].'").attr("checked")) {
						alert("'.str_replace('{label}', $field['label'], $emptyValueAlert).'");
						$("#autoform_'.$field['name'].'").focus();
						return false;
					}';
				}
			break;
			
			default:
				;
			break;
		}
	}
	$html = '
	<div class="autoform form-group" id="autoform'.$form['id'].'">
		<h2>'.$form['name'].'</h2>
		<form method="post" action="">
			'.$html.'
			<button type="submit" class="btn btn-default c-btn-large">Отправить</button>
			
			'.($requiredCount>0?'
				<div class="requiredHint"><span class="jep">*</span> — '.($requiredCount==1?'поле обязательно':'поля обязательны').' для заполнения</div>
			':'').'
			
		</form>
	</div>';
	
	$html .= '
	<script type="text/javascript">
	function hideTraps() {
		'.$as_js.'
		$("#autoform'.$form['id'].'").submit(function(){
			'.$js.'
		});
	}
	</script>
	';
	
	$success_sended_html = '
	<div class="autoform" id="autoform'.$form['id'].'">
		<h2>'.$successMsg.'</h2>
	</div>';
	
	//Если админ
	if (!session_id()) session_start();
	if(isset($_SESSION['user']['type'])) {
		$is_admin = ($_SESSION['user']['type'] == 'a');
		if($is_admin && @!$_SESSION['godmode_suspended']) {
			$edit_block_link = '/admin/?module=Forms&method=Info&top='.$form['id'];
			$html = '<div style="border:dashed 1px grey; position:relative;">'.$html.'<a target="_blank" style="position:absolute; top:0; right:-8px; z-index:100;" href="'.$edit_block_link.'" title="Редактировать"><img src="/admin/images/icons/pencil.png" alt="Редактировать" /></a></div>';
		}
	}
	
	//Отправка письма
	if($p && !$error) {
		
		//Антиспам
		//По времени (время заполнения формы)
		if(time() - $_SESSION['formTimeCheck'][$id] <= count($fields) * 2) {
			if($return) return $success_sended_html;
			else { echo $success_sended_html; return true; }
		}
		//По скрытым полям
		foreach ($as as $s) {
			if(isset($_POST[$s['name']]) && !empty($_POST[$s['name']])) {
				if($return) return $success_sended_html;
				else { echo $success_sended_html; return true; }
			}
		}
		
		$addSysFields[] = 'IP: '.$_SERVER['REMOTE_ADDR'].' http://ipgeobase.ru/?address='.$_SERVER['REMOTE_ADDR'];
		$addSysFields[] = 'Дата: '.goodDate(date('c')).' '.date('H:i');
		$addSysFields[] = 'Страница: http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	
		$mailText = implode("\r\n", $mailFields)."\r\n\r\n".implode("\r\n", $addSysFields);
		
		$mail = new ZFmail(
			$form['email'],
			'noreply@'.$_SERVER['SERVER_NAME'],
			$_SERVER['SERVER_NAME'].': '.$form['name'],
			$mailText);
		if($mail->send()) {
			if($return) return $success_sended_html;
			else { echo $success_sended_html; return true; }
		}
	}
	
	if($form['show'] == 'Y') {
		if($return) return $html;
		else echo $html;
	} else {
		return false;
	}
}


/**
 * Страница с 404 ошибкой
 */
function page404() {
	//echo '<pre>'.print_r(debug_backtrace(),1).'</pre>';
	if($GLOBALS['config']['develop']) {
		$array_debug  = debug_backtrace();
		$debug = 'Страница не найдена файлом <code style="font-weight:bold;">'.$array_debug[0]['file'].'</code> на строке: <code style="font-weight:bold;">'.$array_debug[0]['line'].'</code>';
	} else $debug = '';
	global $documents;
	header("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
	echo tpl('page404', array(
		'debug' => $debug
	));
	exit();
}


/**
 * Вывод ошибки
 * @param string $error
 */
function error($error) {
	if(!$GLOBALS['config']['develop']) return false;
	$array_debug  = debug_backtrace();
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		echo $array_debug[0]['file'].': '.$array_debug[0]['line']."\r\n".$error;
	} else {
		echo '
<div style="background:white; color:black display:block;font-family:Trebuchet MS,Arial,sans-serif;">
	<h1 style="color:#0498EC;font-size:1.5em;">Ошибка</h1>
	<ul style="padding: 0em 1em 1em 1.55em;font-size:0.8em;margin:0 0 0 2.5em;">
		<li>Файл <code style="font-weight:bold;">'.$array_debug[0]['file'].'</code> на строке: <code style="font-weight:bold;">'.$array_debug[0]['line'].'</code> говорит что:</li>
		<li>'.$error.'</li>
	</ul>
</div>';
	}
}