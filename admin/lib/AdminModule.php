<?php

class AdminModule {
	
	/**
	 * Нужно ли скрыть модуль
	 * @var bool
	 */
	const hide = false;
	
	/**
	 * Определяет путь до шаблонов модуля
	 * @var string
	 */
	protected $tpls;
	
	/**
	 * Содержит объект для работы с БД
	 * @var object
	 */
	protected $db;
	
	/**
	 * Заголовок/название действия модуля
	 * @var string
	 */
	public $title;
	
	/**
	 * Содержимое действия модуля
	 * @var string
	 */
	public $content;
	
	/**
	 * Содержание для подсказки справа от таблицы
	 * @var array(title,text,link)
	 */
	public $hint;
	
	public $submenu = array();
	
	protected $called_class;
	
	function __construct() {
		$this->called_class = get_called_class();
		$this->tpls = 'modules/'.__CLASS__.'/';
		$this->db = db();
		
		//Вдруг у модуля есть настройки
		$this->db->query("SELECT * FROM `prefix_settings` WHERE `module` = '".$this->called_class."'");
		if($this->db->row_count() > 0) {
			$this->submenu['_Settings'] = 'Настройки модуля';
		}
		
		if(isset($_GET['method'])) {
			if(method_exists($this, $_GET['method'])) $this->$_GET['method']();
			else if(method_exists($this, 'Info')) $this->Info();
		} else {
			if(method_exists($this, 'Info')) $this->Info();
		}
	}
	
	/**
	 * Отдает список с настройками текущего модуля
	 */
	function _Settings() {
		global $modules;
		$modulesForSelect = array();
		foreach ($modules as $m) if(!$m['hide']) $modulesForSelect[] = $m['module'];
		
		$this->title = 'Настройки модуля '.$this->called_class;
		$this->content = $this->DataTable('settings',array(
			//Имена системных полей
			'nouns'	=> array(
				'id'		=> 'id',		// INT
				'name'		=> 'name',		// VARCHAR
				'order'		=> 'order'		// INT
			),
			//Отображение контролов
			'controls' => array(
				'add',
				'edit'
			)
		),
		array(
			'id' 		=> array('name' => '№', 'class' => 'min'),
			'module'	=> array(
				'name'			=> 'Модуль',
				'length'		=> '0-32',
				'autocomplete'	=> $modulesForSelect,
				'default'		=> $this->called_class
			),
			'name'		=> array('name' => 'Название настройки', 'length'=>'1-128'),
			'callname'	=> array('name' => 'Имя для вызова', 'length'=>'0-128'),
			'value'		=> array('name' => 'Значение')
		),"`module` = '".$this->called_class."'");
	}
	
	/**
	 * Таб с сеошными данными
	 * title, keywords, description
	 */
	function _Seo() {
		$id = (int)(isset($_REQUEST['id'])?$_REQUEST['id']:0);
		if($id == 0) { echo 'Сначала создайте запись'; exit(); }
		
		$module = get_called_class();
		$message = '';
		//debug($_POST);
		
		$data = $this->db->query_first("SELECT * FROM `prefix_seo` WHERE `module` = '$module' AND `module_id` = $id");
		
		if(!empty($_POST)) {
			if(empty($data)) {
				if($this->db->query("
					INSERT INTO `prefix_seo` (`module`, `module_id`, `module_table`, `title`, `keywords`, `description`) VALUES
					('$module', $id, '".q($_POST['table'])."', '".q($_POST['title'])."', '".q($_POST['keywords'])."', '".q($_POST['description'])."')
				")) $message = 'SEO данные добавлены';
			} else {
				if($this->db->query("
					UPDATE `prefix_seo`
					SET `title` = '".q($_POST['title'])."', `keywords` = '".q($_POST['keywords'])."', `description` = '".q($_POST['description'])."'
					WHERE `module` = '$module' AND `module_id` = $id AND `module_table` = '".q($_POST['table'])."'
				")) $message = 'SEO данные обновлены';
			}
			$data = $this->db->query_first("SELECT * FROM `prefix_seo` WHERE `module` = '$module' AND `module_id` = $id");
		}
		
		if(empty($data)) $data = array('title'=>'', 'keywords'=>'', 'description'=>'');
		
		echo tpl('widgets/seo', array(
			'title'			=> $data['title'],
			'keywords'		=> $data['keywords'],
			'description'	=> $data['description'],
			'link'			=> $this->GetLink('_Seo', array(), get_called_class()),
			'message'		=> $message
		));
		exit();
	}
	
	/**
	 * Генерация ссылки для админки
	 *
	 * @param string $method
	 * @param array $data
	 * @param string $module
	 *
	 * @return string
	 */
	protected function GetLink($method='', $data=array(), $module='') {
		$level = 1;
		$debug_info = debug_backtrace();
		if($debug_info[$level]['class'] == 'AdminModule' && isset($debug_info[$level+1])) $level++;
		
		$url = array(
			'module'	=> empty($module)?$this->called_class:$module,
			'method'	=> empty($method)?$debug_info[$level]['function']:$method
		);
		/*
		$level = 1;
		$debug_info = debug_backtrace();
		if($debug_info[$level]['class'] == 'AdminModule' && isset($debug_info[$level+1])) $level++;
		
		$url = array(
			'module'	=> empty($module)?$debug_info[$level]['class']:$module,
			'method'	=> empty($method)?$debug_info[$level]['function']:$method
		);
		*/
		$url = array_merge($url, $data);
		return '?'.http_build_query($url);
	}
	
	/**
	 * Собираем данные о полях таблицы
	 *
	 * @param $table
	 */
	function TableColumns($table) {
		$this->db->query("SHOW COLUMNS FROM `prefix_$table`");
		$syscolumns = array();
		while($i = $this->db->fetch(MYSQL_ASSOC)) {
			$syscolumns[$i['Field']] = $i;
		}
		return $syscolumns;
	}
	
	
	function DataTable($table, $options=array(), $fields=array(), $where='', $order='') {
		
		//Собираем данные о полях таблицы
		$syscolumns = $this->TableColumns($table);
		$deleted_field_exist = false;
		if(isset($options['nouns']['deleted'])) {
			foreach ($syscolumns as $i) {
				if($i['Field'] == $options['nouns']['deleted']) $deleted_field_exist = true;
			}
		}
		
		//Удаление
		if(isset($_GET['delete'])) {
			if($deleted_field_exist) {
				$this->db->query("UPDATE `prefix_$table` SET `{$options['nouns']['deleted']}` = 'Y' WHERE `{$options['nouns']['id']}` = ".(int)$_GET['delete']);
			} else {
				$this->db->query("DELETE FROM `prefix_$table` WHERE `{$options['nouns']['id']}` = ".(int)$_GET['delete']);
			}
			//header('Location: '.$this->GetLink());
			//Ссылка для возврата назад, похоже самое простое решение это убрать delete из запроса
			$plink = preg_replace('/&?delete=[\d]+/i', '', $_SERVER['REQUEST_URI']);
			header('Location: '.$plink);
		}
		
		//Сохранение сортировки
		if(isset($_POST['tr'.$table]) && is_array($_POST['tr'.$table])) {
			
			foreach($_POST['tr'.$table] as $k=>$v) {
				$this->db->query("UPDATE `prefix_$table` SET `{$options['nouns']['order']}` = $k  WHERE `{$options['nouns']['id']}` = $v");
			}
			exit();
		}
		
		//Редактирование данных из таблицы (аякс)
		if(isset($_GET['update'])) {
			$this->db->query("UPDATE `prefix_".q($_POST['table'])."` SET `".q($_POST['field'])."` = '".q($_POST['val'])."' WHERE `{$options['nouns']['id']}` = ".(int)$_POST['id']);
			exit();
		}
		
		//Редактирование текстового содержания
		if(isset($_GET['edit_text'])) {
			
			//Сохранение
			if(isset($_POST['text'])) {
				$newtext = q($_POST['text']);
				$this->db->query("UPDATE `prefix_$table` SET `{$options['nouns']['text']}` = '$newtext' WHERE `{$options['nouns']['id']}` = ".(int)$_GET['edit_text']);
			}
			
			$i = $this->db->rows("SELECT `{$options['nouns']['id']}`,`{$options['nouns']['name']}`,`{$options['nouns']['text']}` FROM `prefix_$table` WHERE `{$options['nouns']['id']}` = ".(int)$_GET['edit_text']);
			$text = $i[0][$options['nouns']['text']];
			$name = $i[0][$options['nouns']['name']];
			$id = $i[0][$options['nouns']['id']];
			
			//Ссылка для возврата назад, похоже самое простое решение это убрать edit_text из запроса
			$plink = preg_replace('/&?edit_text=[\d]+/i', '', $_SERVER['REQUEST_URI']);
			
			$ret = tpl('widgets/table_edit_text', array(
				'plink'	=> $plink,
				'text'	=> $text,
				'name'	=> $name,
				'id'	=> $id
			));
			
			$this->title = 'Редактирование «'.$name.'»';
			
			$this->hint = array(
				'title'	=> 'О редактировании текстов',
				'text'	=> '
					<p>Вы можете использовать блоки в формате <code>{block:1}</code> их можно добавить в разделе <a href="'.$this->GetLink('Info',array(),'Blocks').'">блоков</a></p>'
			);
			
			return $ret;
		}
		
		if(empty($where)) {
			$where = '1';
		}
		if(!empty($fields)) {
			
		}
		if(isset($_GET['order']) && in_array($_GET['order'], array_keys($syscolumns))) $order = q($_GET['order']);
		
		if(!isset($_GET['order']) && isset($options['nouns']['order']) && in_array($options['nouns']['order'],array_keys($syscolumns)) && in_array($options['nouns']['order'],array_keys($fields))) $order = $options['nouns']['order'];
		
		if(empty($order)) $order = $options['nouns']['id'];
		
		if(isset($_GET['orderd']) && in_array($_GET['orderd'], array('ASC','DESC'))) $orderd = $_GET['orderd'];
		else $orderd = 'ASC';
		
		if($deleted_field_exist) {
			$where .= " AND `{$options['nouns']['deleted']}` = 'N'";
		}
				
		$this->db->query("SELECT * FROM `prefix_$table` WHERE $where ORDER BY `$order` $orderd");
		
		$rows = array();
		$db2 = clone $this->db;
		while($i = $this->db->fetch(MYSQL_ASSOC)) {
			if(!isset($header)) {
				$header = array();
				foreach($i as $k=>$v) {
					if(empty($fields)) {
						$header[$k] = array(
							'field'	=> $k,
							'name'	=> $k
						);
					} else {
						if(in_array($k,array_keys($fields)) && !isset($fields[$k]['hide_from_table'])) {
							$header[$k] = array(
								'field'	=> $k,
								'name'	=> $fields[$k]['name'],
								'class'	=> isset($fields[$k]['class'])?$fields[$k]['class']:''
							);
						}
					}
					if($order == $k) {
						$header[$k] = array_merge($header[$k], array('order'=>true,'orderd'=>$orderd));
					}
					
					if(isset($fields[$k]['style'])) $header[$k]['style'] = $fields[$k]['style'];
				}
				//Страница контента (Принадлежит)
				if(isset($options['nouns']['holder'])) {
					$header[$options['nouns']['holder']] = array(
						'field'	=> $options['nouns']['holder'],
						'name'	=> 'Принадлежит',
						'class'	=> ''
					);
				}
			}
			
			$row = array();
			foreach($i as $field => $value) {
				if((in_array($field,array_keys($fields)) && !isset($fields[$field]['hide_from_table'])) || empty($fields)) {
					
					//МОДИФИКАЦИЯ ДАННЫХ В ТАБЛИЦЕ
					
					//Модификация через пользовательскую функцию (transform)
					if(isset($fields[$field]['transform'])) {
						if(is_object($fields[$field]['transform'])) {
							$row[$field] = $fields[$field]['transform']($value, $i);
							continue;
						}
						elseif(method_exists($this, $fields[$field]['transform'])) {
							$row[$field] = $this->$fields[$field]['transform']($value, $i);
							continue;
						}
					}
					
					//Селект
					//если поле заявлено как селект (select)
					if(isset($fields[$field]['multiselect'])) $fields[$field]['select'] = $fields[$field]['multiselect'];
					if(isset($fields[$field]['select']) && isset($fields[$field]['select']['table']) && isset($fields[$field]['select']['name'])) {
						if(!isset($select_replace_text[ $fields[$field]['select']['table'] ])) {
							//$select_replace_text = array();
							if(isset($fields[$field]['select']['deleted'])) $is_deleted = "AND `{$fields[$field]['select']['deleted']}` = 'N'";
							else $is_deleted = '';
							$db2->query("SELECT * FROM `prefix_{$fields[$field]['select']['table']}` WHERE 1 $is_deleted");
							while ($si = $db2->fetch()) {
								if(isset($fields[$field]['select']['id'])) {
									$skey = $si[ $fields[$field]['select']['id'] ];
								} else {
									$skey = $si[ $fields[$field]['select']['name'] ];
								}
								$select_replace_text[ $fields[$field]['select']['table'] ] [$skey] = $si[ $fields[$field]['select']['name'] ];
							}
						}
						
						if(isset($select_replace_text[ $fields[$field]['select']['table'] ] [$value])) {
							$row[$field] = $select_replace_text[ $fields[$field]['select']['table'] ] [$value];
						} else {
							$row[$field] = $value;
						}
						
						//Мультиселект
						if(isset($fields[$field]['multiselect'])) {
							$curr_values = @unserialize($value);
							if(is_array($curr_values)) {
								$row_vals = array();
								foreach ($curr_values as $mvalue) {
									$row_vals[] = $select_replace_text[ $fields[$field]['select']['table'] ] [$mvalue];
								}
								$row[$field] = implode(', ', $row_vals);
							}
						}
						
						continue;
					}
					
					//Ссылка
					if(isset($fields[$field]['link'])) {
						$row[$field] = '<a href="'.str_replace('{id}',$i[$options['nouns']['id']],$fields[$field]['link']).'">'.$value.'</a>';
						continue;
					}
					
					
					//ДАННЫЕ ВЫВОДЯТСЯ НАПРЯМУЮ БЕЗ ОБРАБОТКИ
					$row[$field] = htmlspecialchars($value);
				}
			}
			
			//Страница контента (Принадлежит)
			if(isset($options['nouns']['holder'])) {
				$db2->query("SELECT * FROM `prefix_content` WHERE `id` = ".$i['holder']);
				if($i['holder'] == 0) {
					$row['holder'] = '<span style="font-size:12px; color:#aaa;">не ограничено</span>';
				} else if($db2->row_count() > 0) {
					$holder = $db2->fetch();
					$row['holder'] = '<span style="font-size:12px;">'.$holder['name'].'</span>';
				} else {
					$row['holder'] = '<span style="font-size:12px; color:red;">раздел не существует</span>';
				}
			}
			
			$rows[] = $row;
		}
		
		//Подсветка измененного
		$highlight = 0;
		if(isset($_SERVER['HTTP_REFERER'])) {
			parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY),$parsedref);
			if(isset($parsedref['edit_text'])) {
				$highlight = $parsedref['edit_text'];
			}
		}
		
		//Дополнительный GET параметр top
		if(isset($_GET['top'])) $top = '&top='.$_GET['top'];
		else $top = '';
		
		$datatable = tpl('widgets/table',array(
			'thead'	=> @$header,
			'tbody'	=> $rows,
			'plink'	=> $this->GetLink().$top,
			'table'	=> $table,
			'options'	=> $options,
			'syscolumns'	=> $syscolumns,
			'highlight'	=> $highlight
		));
		
		//Форма добавления/редактирования jQueryUI
		$edit = $this->DataTable_AddEdit($_SERVER['REQUEST_URI'], $table, $options, $syscolumns, $fields);
		
		return $datatable.$edit;
	}
	
	protected function DataTable_AddEdit($link, $table, $options, $syscolumns=array(), $fields=array()) {
		if(empty($syscolumns)) {
			//Собираем данные о полях таблицы
			$syscolumns = $this->TableColumns($table);
		}
		
		//Отдаем JSON для формы редактирования
		if(isset($_GET['getFieldsById'])) {
			$f = $this->db->rows("SELECT * FROM `prefix_$table` WHERE `id` = ".(int)$_GET['getFieldsById'], MYSQL_ASSOC);
			$jsoni = array();
			foreach ($f[0] as $k=>$v) {
				if(!empty($fields) && !in_array($k, array_keys($fields)) && (isset($options['nouns']['holder']) && $options['nouns']['holder'] != $k) ) continue;
				
				//Мультиселект
				if(isset($fields[$k]['multiselect'])) {
					$v = @unserialize($v);
				}
				
				$jsoni[] = array(
					'name'	=> $k,
					'value'	=> $v,//str_replace(array("\r\n",'"'),array('\r\n','\\"'),$v),
					'type'	=> $syscolumns[$k]['Type']
				);
			}
			
			//Изображение
			if(isset($options['nouns']['image']) && $options['nouns']['image']) {
				if(is_file(DIR.'/data/'.$table.'/'.$f[0]['id'].'/'.$f[0]['id'])) {
					$jsoni[] = array(
						'name'	=> 'image',
						'value'	=> '/data/'.$table.'/'.$f[0]['id'].'/'.$f[0]['id'].'?rnd='.rand(1000,20000),
						'type'	=> 'Image'
					);
				} else {
					$jsoni[] = array(
						'name'	=> 'image',
						'value'	=> '',
						'type'	=> 'Image'
					);
				}
			}
			
			$json = json_encode($jsoni);
			
			echo $json;
			exit();
		}
		
		//Добавление записи
		if(isset($_POST['id']) && $_POST['id'] == 0) {
			$sql = "INSERT INTO `prefix_$table` ";// VALUES";
			
			$insert_fields = array();
			foreach($_POST as $k=>$v) {
				if($k == 'id') continue;
				
				//Перевод и создание URI
				if(isset($fields[$k]['if_empty_make_uri']) && empty($v)) {
					if(isset($_POST[$fields[$k]['if_empty_make_uri']])) {
						$v = makeURI($_POST[$fields[$k]['if_empty_make_uri']]);
					}
				}
				
				//Мультиселект
				if(isset($fields[$k]['multiselect'])) {
					$v = serialize($v);
				}
				
				$insert_fields[] = "`$k`";
				$insert_values[] = "'".q($v)."'";
			}
			
			//Дата создания, подставляем автоматически, если такое поле есть
			if(in_array($options['nouns']['created'], array_keys($syscolumns))) {
				$insert_fields[] = "`{$options['nouns']['created']}`";
				$insert_values[] = "NOW()";
			}
				
			$sql .= '('.implode(',',$insert_fields).') VALUES ('.implode(',',$insert_values).')';
			$this->db->query($sql);
			//echo $sql;
			$id = $this->db->last_insert_id();
			if(isset($_FILES['image']['name'])) {
				$ext = strtolower(substr($_FILES['image']['name'],-3));
				if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png') {
					if(!is_dir(DIR.'/data')) mkdir(DIR.'/data');
					if(!is_dir(DIR.'/data/'.$table)) mkdir(DIR.'/data/'.$table);
					if(!is_dir(DIR.'/data/'.$table.'/'.$id)) mkdir(DIR.'/data/'.$table.'/'.$id);
					copy($_FILES['image']['tmp_name'], DIR.'/data/'.$table.'/'.$id.'/'.$id);
				}
			}
			
			//Событие
			if(method_exists($this, 'OnDataTableAdd')) $this->OnDataTableAdd($_POST);
			
			header("Location: ".$link);
		}
		
		//Редактирование записи
		if(isset($_POST['id']) && $_POST['id'] != 0) {
			$sql = "UPDATE `prefix_$table` SET ";// VALUES";
			$insert_fields = array();
			
			foreach($_POST as $k=>$v) {
				if($k == 'id') continue;
				
				//Перевод и создание URI
				if(isset($fields[$k]['if_empty_make_uri']) && empty($v)) {
					if(isset($_POST[$fields[$k]['if_empty_make_uri']])) {
						$v = makeURI($_POST[$fields[$k]['if_empty_make_uri']]);
					}
				}
				
				//Мультиселект
				if(isset($fields[$k]['multiselect'])) {
					$v = serialize($v);
				}
				
				$update_values[] = "`$k` = '".q($v)."'";
			}
			
			//Дата создания, подставляем автоматически, если такое поле есть
			if(in_array($options['nouns']['modified'], array_keys($syscolumns))) {
				$update_values[] = "`{$options['nouns']['modified']}` = NOW()";
			}
				
			$sql .= implode(',',$update_values).' WHERE `id` = '.(int)$_POST['id'];
			$this->db->query($sql);
			
			//print_r($_FILES);
			if(isset($_FILES['image']['name'])) {
				$ext = strtolower(substr($_FILES['image']['name'],-3));
				if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'png') {
					if(!is_dir(DIR.'/data')) mkdir(DIR.'/data');
					if(!is_dir(DIR.'/data/'.$table)) mkdir(DIR.'/data/'.$table);
					if(!is_dir(DIR.'/data/'.$table.'/'.$_POST['id'])) mkdir(DIR.'/data/'.$table.'/'.$_POST['id']);
					copy($_FILES['image']['tmp_name'], DIR.'/data/'.$table.'/'.$_POST['id'].'/'.$_POST['id']);
					//Удаляем старые отресайзеные файлы
					if(is_dir(DIR.'/data/thumbs/data/'.$table.'/'.$_POST['id'])) {
						$thumbs = scandir(DIR.'/data/thumbs/data/'.$table.'/'.$_POST['id']);
						foreach ($thumbs as $thumb) {
							if(is_file(DIR.'/data/thumbs/data/'.$table.'/'.$_POST['id'].'/'.$thumb)) {
								unlink(DIR.'/data/thumbs/data/'.$table.'/'.$_POST['id'].'/'.$thumb);
							}
						}
					}
				}
			}
			
			//Событие
			if(method_exists($this, 'OnDataTableEdit')) $this->OnDataTableEdit($_POST);
			
			header("Location: ".$link);
		}
		
		//Готовим поля для формы
		$we_have_files_fields = false;
		$addjs = array();
		foreach($syscolumns as $k=>$column) {
			if(!empty($fields) && !in_array($k, array_keys($fields)) && (!isset($options['nouns']['holder']) || $options['nouns']['holder'] != $k) ) continue;
			
			if($column['Key'] == 'PRI') continue;
			
			if(isset($fields[$k]['default'])) {
				$default = $fields[$k]['default'];
			} elseif(!empty($column['Default'])) {
				$default = $column['Default'];
			} else {
				$default = '';
			}
			
			//КАСТОМНЫЕ ПОЛЯ
			
			//Автокомплит (jQ Autocomplete)
			if(isset($fields[$k]['autocomplete'])) {
				//Если генерим автокомплит из этого же поля
				//т.е. с данными, которые уже были
				$vjsi = array();
				if($fields[$k]['autocomplete'] == 'this') {
					$this->db->query("SELECT DISTINCT `$k` FROM `prefix_$table` WHERE `$k` != ''");
					while ($i = $this->db->fetch()) {
						$vjsi[] = '"'.$i[$k].'"';
					}
				}
				elseif (is_array($fields[$k]['autocomplete'])) {
					foreach ($fields[$k]['autocomplete'] as $i) {
						$vjsi[] = '"'.$i.'"';
					}
				}
				
				$html[] = '
					<div class="floating_fields">
						<label for="se_'.$table.'_'.$k.'">'.$fields[$k]['name'].'</label>
						<input name="'.$k.'" id="se_'.$table.'_'.$k.'" def="'.$default.'" class="text ui-widget-content ui-corner-all autocomplete" />
					</div>';
				$minLength = 0;
				if(count($vjsi) > 100) $minLength = 1;
				$vjs = '
					$("#se_'.$table.'_'.$k.'").autocomplete({
						minLength: '.$minLength.',
						source: ['.implode(',', $vjsi).']
					}).click(function(){
						$("#se_'.$table.'_'.$k.'").autocomplete("search", "");
					});
				';
				$addjs[] = $vjs;
				
				continue;
			}
			
			//Селект (select)
			if(isset($fields[$k]['multiselect'])) $fields[$k]['select'] = $fields[$k]['multiselect'];
			if(isset($fields[$k]['select']) && isset($fields[$k]['select']['table']) && isset($fields[$k]['select']['name'])) {
				if(isset($fields[$k]['select']['id'])) $id_field = ', `'.$fields[$k]['select']['id'].'`';
				else $id_field = '';
				if(isset($fields[$k]['select']['order'])) $order_field = 'ORDER BY `'.$fields[$k]['select']['order'].'`';
				else $order_field = '';
				if(isset($fields[$k]['select']['deleted'])) $is_deleted = "AND `{$fields[$k]['select']['deleted']}` = 'N'";
				else $is_deleted = '';
				if(isset($fields[$k]['select']['where'])) $where_field = $fields[$k]['select']['where'];
				else $where_field = '';
				
				//Если данные нужно построить деревом
				if(isset($fields[$k]['select']['top'])) {
					if(isset($fields[$k]['select']['allow_null']) && $fields[$k]['select']['allow_null']) {
						$opts = '<option value=""></option>';
					} else {
						$opts = '';
					}
					//Рекурсивно собираем категории
					$make_select_tree = function($tid, $field, $default, $c=0, $order_field, $is_deleted, $where_field, $make_select_tree) {
						$db = db();
						$db->query("
							SELECT `{$field['select']['name']}`, `{$field['select']['id']}`
							FROM `prefix_{$field['select']['table']}`
							WHERE `{$field['select']['top']}` = $tid
							$where_field
							$is_deleted
							$order_field");
						$opts = '';
						while ($i = $db->fetch()) {
							$value = $i[$field['select']['id']];
							
							if($default == $value) {
								$selected = 'selected="selected"';
							} else {
								$selected = '';
							}
							if($c>0) {
								$prefix = str_repeat('-', $c).' ';
							} else {
								$prefix = '';
							}
							$opts .= '<option value="'.$value.'" '.$selected.'>'.$prefix.htmlspecialchars($i[$field['select']['name']]).'</option>';
							$opts .= $make_select_tree($i[$field['select']['id']], $field, $default, $c+1, $order_field, $is_deleted, $where_field, $make_select_tree);
						}
						return $opts;
					};
					$opts = $make_select_tree(0, $fields[$k], $default, 0, $order_field, $is_deleted, $where_field, $make_select_tree);
				}
				//Данные простым списком
				else {
					$this->db->query("SELECT `{$fields[$k]['select']['name']}` $id_field FROM `prefix_{$fields[$k]['select']['table']}` WHERE 1 $where_field $is_deleted $order_field");
					if(isset($fields[$k]['select']['allow_null']) && $fields[$k]['select']['allow_null']) {
						$opts = '<option value=""></option>';
					} else {
						$opts = '';
					}
					while ($i = $this->db->fetch()) {
						if(isset($fields[$k]['select']['id'])) {
							$value = $i[$fields[$k]['select']['id']];
						} else {
							$value = $i[$fields[$k]['select']['name']];
						}
						if($default == $value) {
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}
						$opts .= '<option value="'.$value.'" '.$selected.'>'.htmlspecialchars($i[$fields[$k]['select']['name']]).'</option>';
					}
				}
				
				//Если у нас мультиселект
				if(isset($fields[$k]['multiselect'])) {
					if(isset($fields[$k]['select']['size'])) $multi_size_val = $fields[$k]['select']['size'];
					else $multi_size_val = 4;
					$multi = 'multiple="multiple"';
					$multi_array = '[]';
					$multi_size = 'size="'.$multi_size_val.'"';
				} else {
					$multi = '';
					$multi_array = '';
					$multi_size = '';
				}
				
				$html[] = '
					<div class="floating_fields">
						<label for="se_'.$table.'_'.$k.'">'.$fields[$k]['name'].'</label>
						<select name="'.$k.$multi_array.'" id="se_'.$table.'_'.$k.'" class="text ui-widget-content ui-corner-all" '.$multi_size.' '.$multi.'>
							'.$opts.'
						</select>
					</div>';
				
				continue;
			}
			
			//АВТООПРЕДЕЛЕНИЕ ПОЛЕЙ
			switch ($column['Type']) {
				
				//Текст (textarea)
				case 'text':
					$html[] = '
					<div class="fullwidth_fields">
						<label for="se_'.$table.'_'.$k.'">'.$fields[$k]['name'].'</label>
						<textarea name="'.$k.'" id="se_'.$table.'_'.$k.'" class="text ui-widget-content ui-corner-all tinymce" def="'.$default.'"></textarea>
					</div>';
					
					$vjs = '';
					if(isset($fields[$k]['length']) && !empty($fields[$k]['length'])) {
						$vjs .= 'bValid = bValid && checkLength($("#se_'.$table.'_'.$k.'"),"'.$fields[$k]['name'].'",'.str_replace('-',',',$fields[$k]['length']).');';
					}
					if(isset($fields[$k]['regex']) && !empty($fields[$k]['regex'])) {
						$vjs .= 'bValid = bValid && checkRegexp($("#se_'.$table.'_'.$k.'"),'.$fields[$k]['regex'].',"'.$fields[$k]['regex_error'].'");';
					}
					
					$js[] = $vjs;
				break;
				
				//Да — нет
				case "enum('Y','N')":
					$html[] = '
					<div class="fullwidth_fields">
						<label>'.$fields[$k]['name'].'</label>
						<input type="radio" name="'.$k.'" value="Y" id="se_'.$table.'_'.$k.'_y" class="radio" '.($column['Default']=='Y'?'checked="checked" def="1"':'').' /> <label for="se_'.$table.'_'.$k.'_y" style="display:inline">Да</label>
						<input type="radio" name="'.$k.'" value="N" id="se_'.$table.'_'.$k.'_n" class="radio" '.($column['Default']=='N'?'checked="checked" def="1"':'').' /> <label for="se_'.$table.'_'.$k.'_n" style="display:inline">Нет</label>
					</div>';
					
					$vjs = '';
					$js[] = $vjs;
					 
				break;
				
				//Календарик с датами
				case 'datetime': case 'date':
					$html[] = '
					<div class="floating_fields">
						<label for="se_'.$table.'_'.$k.'">'.$fields[$k]['name'].'</label>
						<input name="'.$k.'" id="se_'.$table.'_'.$k.'" class="text ui-widget-content ui-corner-all datefield" def="'.$default.'" />
					</div>';
					
					$vjs = '';
					$vjs .= 'bValid = bValid && checkRegexp($("#se_'.$table.'_'.$k.'"),/(19|20)[0-9]{2}[\- \/.](0[1-9]|1[012])[\- \/.](0[1-9]|[12][0-9]|3[01])/im,"Не верно заполнено поле с датой");';
					
					$js[] = $vjs;
				break;
				
				
				default:
					//Страница контента (Принадлежит) (SELECT)
					if(isset($options['nouns']['holder']) && $column['Field'] == $options['nouns']['holder']) {
						if(isset($options['nouns']['holder_module'])) {
							$module_name = $options['nouns']['holder_module'];
						} else {
							$array_debug  = debug_backtrace();
							$module_name = $array_debug[0]['class'];
						}
						
						$this->db->query("SELECT * FROM `prefix_content` WHERE `module` = '$module_name'");
						$holder_select = '
						<div class="fullwidth_fields">
							<label for="se_'.$table.'_'.$k.'">Запись принадлежит разделу</label>
							<select name="'.$k.'" id="se_'.$table.'_'.$k.'" style="margin-bottom:12px">
								<option value="0">не ограничено разделом</option>';
						while($i = $this->db->fetch()) {
							$holder_select .= '<option value="'.$i['id'].'">'.$i['name'].'</option>';
						}
						$holder_select .= '</select>
						</div>';
						
						$html[] = $holder_select;
						
						$vjs = '';
						$js[] = $vjs;
					}
					//ENUM(...)
					else if (strpos($column['Type'], 'enum(') === 0) {
						$radio = explode("','", substr($column['Type'], 6, -2));
						
						$enum_radio = '<label>'.$fields[$k]['name'].'</label><div id="se_'.$table.'_'.$k.'">';
						foreach($radio as $i) {
							$radio_name = $i==''?'Не указано':$i;
							$enum_radio .= '<input type="radio" name="'.$k.'" value="'.$i.'" id="se_'.$table.'_'.$k.'_'.$i.'" class="radio" '.($default==$i?'checked="checked" def="1"':'').' /> <label for="se_'.$table.'_'.$k.'_'.$i.'" style="display:inline">'.$radio_name.'</label>';
						}
						
						
						$html[] = '
						<div class="fullwidth_fields">
							'.$enum_radio.'
						</div>';
						
						$vjs = '';
						$js[] = $vjs;
						
					}
					//Текстовое поле (input)
					else {
						$html[] = '
						<div class="floating_fields">
							<label for="se_'.$table.'_'.$k.'">'.$fields[$k]['name'].'</label>
							<input name="'.$k.'" id="se_'.$table.'_'.$k.'" class="text ui-widget-content ui-corner-all" def="'.$default.'" />
						</div>';
						$vjs = '';
						if(isset($fields[$k]['length']) && !empty($fields[$k]['length'])) {
							$vjs .= 'bValid = bValid && checkLength($("#se_'.$table.'_'.$k.'"),"'.$fields[$k]['name'].'",'.str_replace('-',',',$fields[$k]['length']).');';
						}
						if(isset($fields[$k]['regex']) && !empty($fields[$k]['regex'])) {
							$vjs .= 'bValid = bValid && checkRegexp($("#se_'.$table.'_'.$k.'"),'.$fields[$k]['regex'].',"'.$fields[$k]['regex_error'].'");';
						}
						
						$js[] = $vjs;
					}
				break;
			}
		}
		
		//Картинка
		if(isset($options['nouns']['image']) && $options['nouns']['image']) {
			//if(is_file(DIR.'/data/'.$table.'/'.$_POST['id'].'/'.$_POST['id'].'.jpg'))
			$html[] = '
			<div class="fullwidth_fields">
				<label for="se_'.$table.'_image">Изображение (Максимальный размер '.get_max_filesize().')</label>
				<input type="file" name="image" id="se_'.$table.'_image" class="text ui-widget-content ui-corner-all" />
				<div id="se_'.$table.'_image_prev"></div>
			</div>
				';
			$we_have_files_fields = true;
		}
		
		//Табы
		$tabs = array();
		if(isset($options['tabs'])) {
			foreach ($options['tabs'] as $k=>$v) {
				if(!method_exists($this, $k)) continue;
				$tabs[$k] = array(
					'method'	=> $k,
					'name'		=> $v
				);
			}
		}
		
		$form = tpl('widgets/simple_edit',array(
			'plink'	=> $link,
			'table'	=> $table,
			'js'	=> implode("\r\n",$js),
			'html'	=> $html,
			'we_have_files_fields' => $we_have_files_fields,
			'tabs'	=> $tabs,
			'addjs'	=> $addjs
		));
		
		return $form;
	}
	
	
	function DataTableAdvanced($table, $options=array(), $fields=array(), $where='', $order='') {
		
		//Собираем данные о полях таблицы
		$syscolumns = $this->TableColumns($table);
		$deleted_field_exist = false;
		if(isset($options['nouns']['deleted'])) {
			foreach ($syscolumns as $i) {
				if($i['Field'] == $options['nouns']['deleted']) $deleted_field_exist = true;
			}
		}
		
		//Удаление
		if(isset($_GET['delete'])) {
			if($deleted_field_exist) {
				$this->db->query("UPDATE `prefix_$table` SET `{$options['nouns']['deleted']}` = 'Y' WHERE `{$options['nouns']['id']}` = ".(int)$_GET['delete']);
			} else {
				$this->db->query("DELETE FROM `prefix_$table` WHERE `{$options['nouns']['id']}` = ".(int)$_GET['delete']);
			}
			//header('Location: '.$this->GetLink());
			//Ссылка для возврата назад, похоже самое простое решение это убрать delete из запроса
			$plink = preg_replace('/&?delete=[\d]+/i', '', $_SERVER['REQUEST_URI']);
			header('Location: '.$plink);
		}
		
		//Сохранение сортировки
		if(isset($_POST['tr'.$table]) && is_array($_POST['tr'.$table])) {
			
			foreach($_POST['tr'.$table] as $k=>$v) {
				$this->db->query("UPDATE `prefix_$table` SET `{$options['nouns']['order']}` = ".((int)$k)."  WHERE `{$options['nouns']['id']}` = ".(int)$v);
			}
			exit();
		}
		
		//Редактирование данных из таблицы (аякс)
		if(isset($_GET['update'])) {
			$this->db->query("UPDATE `prefix_".q($_POST['table'])."` SET `".q($_POST['field'])."` = '".q($_POST['val'])."' WHERE `{$options['nouns']['id']}` = ".(int)$_POST['id']);
			exit();
		}
		
		//Редактирование текстового содержания
		if(isset($_GET['edit_text'])) {
			
			//Сохранение
			if(isset($_POST['text'])) {
				$newtext = $_POST['text'];//str_replace("'","\\'",$_POST['text']);
				$this->db->query("UPDATE `prefix_$table` SET `{$options['nouns']['text']}` = '".q($newtext)."' WHERE `{$options['nouns']['id']}` = ".(int)$_GET['edit_text']);
			}
			
			$i = $this->db->rows("SELECT `{$options['nouns']['id']}`,`{$options['nouns']['name']}`,`{$options['nouns']['text']}` FROM `prefix_$table` WHERE `{$options['nouns']['id']}` = ".(int)$_GET['edit_text']);
			$text = $i[0][$options['nouns']['text']];
			$name = $i[0][$options['nouns']['name']];
			$id = $i[0][$options['nouns']['id']];
			
			//Ссылка для возврата назад, похоже самое простое решение это убрать edit_text из запроса
			$plink = preg_replace('/&?edit_text=[\d]+/i', '', $_SERVER['REQUEST_URI']);
			
			$ret = tpl('widgets/table_edit_text', array(
				'plink'	=> $plink,
				'text'	=> $text,
				'name'	=> $name,
				'id'	=> $id
			));
			
			$this->title = 'Редактирование «'.$name.'»';
			
			$this->hint = array(
				'title'	=> 'О редактировании текстов',
				'text'	=> '
					<p>Вы можете использовать блоки в формате <code>{block:1}</code> их можно добавить в разделе <a href="'.$this->GetLink('Info',array(),'Blocks').'">блоков</a></p>'
			);
			
			return $ret;
		}
		
		if(empty($where)) {
			$where = '1';
		}
		if(!empty($fields)) {
			
		}
		if(isset($_GET['order'])) $order = q($_GET['order']);
		
		if(!isset($_GET['order']) && isset($options['nouns']['order']) && in_array($options['nouns']['order'],array_keys($syscolumns)) && in_array($options['nouns']['order'],array_keys($fields))) $order = $options['nouns']['order'];
		
		if(empty($order)) $order = $options['nouns']['id'];
		
		if(isset($_GET['orderd'])) $orderd = $_GET['orderd'];
		else $orderd = 'ASC';
		
		if($deleted_field_exist) {
			$where .= " AND `{$options['nouns']['deleted']}` = 'N'";
		}
		
		$this->db->query("SELECT * FROM `prefix_$table` WHERE $where ORDER BY `$order` $orderd");
		
		$rows = array();
		$db2 = clone $this->db;
		while($i = $this->db->fetch(MYSQL_ASSOC)) {
			if(!isset($header)) {
				$header = array();
				foreach($i as $k=>$v) {
					if(empty($fields)) {
						$header[$k] = array(
							'field'	=> $k,
							'name'	=> $k
						);
					} else {
						if(in_array($k,array_keys($fields)) && !isset($fields[$k]['hide_from_table'])) {
							$header[$k] = array(
								'field'	=> $k,
								'name'	=> $fields[$k]['name'],
								'class'	=> isset($fields[$k]['class'])?$fields[$k]['class']:''
							);
						}
					}
					if($order == $k) {
						$header[$k] = array_merge($header[$k], array('order'=>true,'orderd'=>$orderd));
					}
					
					if(isset($fields[$k]['style'])) $header[$k]['style'] = $fields[$k]['style'];
				}
				//Страница контента (Принадлежит)
				if(isset($options['nouns']['holder'])) {
					$header[$options['nouns']['holder']] = array(
						'field'	=> $options['nouns']['holder'],
						'name'	=> 'Принадлежит',
						'class'	=> ''
					);
				}
			}
			
			$row = array();
			foreach($i as $field => $value) {
				if((in_array($field,array_keys($fields)) && !isset($fields[$field]['hide_from_table'])) || empty($fields)) {
					
					//МОДИФИКАЦИЯ ДАННЫХ В ТАБЛИЦЕ
					
					//Модификация через пользовательскую функцию (transform)
					if(isset($fields[$field]['transform'])) {
						if(is_object($fields[$field]['transform'])) {
							$row[$field] = $fields[$field]['transform']($value, $i);
							continue;
						}
						elseif(method_exists($this, $fields[$field]['transform'])) {
							$row[$field] = $this->$fields[$field]['transform']($value, $i);
							continue;
						}
					}
					
					//Селект
					//если поле заявлено как селект (select)
					if(isset($fields[$field]['multiselect'])) $fields[$field]['select'] = $fields[$field]['multiselect'];
					if(isset($fields[$field]['select']) && isset($fields[$field]['select']['table']) && isset($fields[$field]['select']['name'])) {
						if(!isset($select_replace_text[ $fields[$field]['select']['table'] ])) {
							//$select_replace_text = array();
							if(isset($fields[$field]['select']['deleted'])) $is_deleted = "AND `{$fields[$field]['select']['deleted']}` = 'N'";
							else $is_deleted = '';
							$db2->query("SELECT * FROM `prefix_{$fields[$field]['select']['table']}` WHERE 1 $is_deleted");
							while ($si = $db2->fetch()) {
								if(isset($fields[$field]['select']['id'])) {
									$skey = $si[ $fields[$field]['select']['id'] ];
								} else {
									$skey = $si[ $fields[$field]['select']['name'] ];
								}
								$select_replace_text[ $fields[$field]['select']['table'] ] [$skey] = $si[ $fields[$field]['select']['name'] ];
							}
						}
						
						if(isset($select_replace_text[ $fields[$field]['select']['table'] ] [$value])) {
							$row[$field] = $select_replace_text[ $fields[$field]['select']['table'] ] [$value];
						} else {
							$row[$field] = $value;
						}
						
						//Мультиселект
						if(isset($fields[$field]['multiselect'])) {
							$curr_values = @unserialize($value);
							if(is_array($curr_values)) {
								$row_vals = array();
								foreach ($curr_values as $mvalue) {
									$row_vals[] = $select_replace_text[ $fields[$field]['select']['table'] ] [$mvalue];
								}
								$row[$field] = implode(', ', $row_vals);
							}
						}
						
						continue;
					}
					
					//Ссылка
					if(isset($fields[$field]['link'])) {
						$row[$field] = '<a href="'.str_replace('{id}',$i[$options['nouns']['id']],$fields[$field]['link']).'">'.$value.'</a>';
						continue;
					}
					
					//ДАННЫЕ ВЫВОДЯТСЯ НАПРЯМУЮ БЕЗ ОБРАБОТКИ
					$row[$field] = htmlspecialchars($value);
				}
			}
			
			//Страница контента (Принадлежит)
			if(isset($options['nouns']['holder'])) {
				$db2->query("SELECT * FROM `prefix_content` WHERE `id` = ".$i['holder']);
				if($i['holder'] == 0) {
					$row['holder'] = '<span style="font-size:12px; color:#aaa;">не ограничено</span>';
				} else if($db2->row_count() > 0) {
					$holder = $db2->fetch();
					$row['holder'] = '<span style="font-size:12px;">'.$holder['name'].'</span>';
				} else {
					$row['holder'] = '<span style="font-size:12px; color:red;">раздел не существует</span>';
				}
			}
			
			$rows[] = $row;
		}
		
		//Подсветка измененного
		$highlight = 0;
		if(isset($_SERVER['HTTP_REFERER'])) {
			parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY),$parsedref);
			if(isset($parsedref['edit_text'])) {
				$highlight = $parsedref['edit_text'];
			}
		}
		
		//Дополнительный GET параметр top
		if(isset($_GET['top'])) $top = '&top='.$_GET['top'];
		else $top = '';
		
		$datatable = tpl('widgets/table_advanced',array(
			'thead'	=> @$header,
			'tbody'	=> $rows,
			'plink'	=> $this->GetLink().$top,
			'table'	=> $table,
			'options'		=> $options,
			'syscolumns'	=> $syscolumns,
			'highlight'		=> $highlight
		));
		
		//Форма добавления/редактирования jQueryUI
		$edit = $this->DataTable_AddEdit($_SERVER['REQUEST_URI'], $table, $options, $syscolumns, $fields);
		
		return $datatable.$edit;
	}
	
	
	
	function FileEdit($file, $form_action = '') {
		
		if(!is_file($file)) {
			$ofile = $file;
			$file = DIR.'/'.$file;
			if(!is_file($file)) {
				$this->title = 'Не найден файл «'.$ofile.'»';
				return ;
			}
			$file = realpath($file);
			if(!is_readable($file)) {
				$this->title = 'Невозможно прочитать файл «'.$ofile.'»';
				return ;
			}
		}

		//Сохранение
		if(isset($_POST['text'])) {
			$newtext = $_POST['text'];
			if(!is_writable($file)) {
				$this->title = 'Невозможно записать в файл «'.$ofile.'»';
				return ;
			} else {
				file_put_contents($file, $newtext);
			}
		}
		
		//Заголовок и хинт
		$this->title = 'Редактирование файла «'.$file.'»';
		$this->hint = array(
			'title'	=> 'О редактировании файлов',
			'text'	=> '
				<p>Подсветка синтаксиса предоставлена <a href="http://marijn.haverbeke.nl/codemirror/" target="_blank">CodeMirror</a></p>'
		);
		
		//Открываем файл
		$pathinfo	= pathinfo($file); // dirname basename extension filename
		$text		= file_get_contents($file);
		$writable	= is_writable($file);
		
		//Подбираем подсветку синтаксиса
		switch (strtolower($pathinfo['extension'])) {
			case 'xml':
				$parserfiles = '"parsexml.js"';
				$stylesheets = '"/admin/css/CodeMirror/xmlcolors.css"';
			break;
			
			case 'js':
				$parserfiles = '["tokenizejavascript.js", "parsejavascript.js"]';
				$stylesheets = '"/admin/css/CodeMirror/jscolors.css"';
			break;
			
			case 'css':
				$parserfiles = '"parsecss.js"';
				$stylesheets = '"/admin/css/CodeMirror/csscolors.css"';
			break;
			
			case 'htm': case 'html':
				$parserfiles = '["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "parsehtmlmixed.js"]';
				$stylesheets = '["/admin/css/CodeMirror/xmlcolors.css", "/admin/css/CodeMirror/jscolors.css", "/admin/css/CodeMirror/csscolors.css"]';
			break;
			
			case 'php': case 'tpl':
				$parserfiles = '["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "tokenizephp.js", "parsephp.js", "parsephphtmlmixed.js"]';
				$stylesheets = '["/admin/css/CodeMirror/xmlcolors.css", "/admin/css/CodeMirror/jscolors.css", "/admin/css/CodeMirror/csscolors.css", "/admin/css/CodeMirror/phpcolors.css"]';
			break;
			
			case 'sql':
				$parserfiles = '"parsesql.js"';
				$stylesheets = '"/admin/css/CodeMirror/sqlcolors.css"';
			break;
			
			default:
				$parserfiles = '"parsedummy.js"';
				$stylesheets = '""';
			break;
		}
		
		$form = tpl('widgets/file_edit',array(
			'text'			=> $text,
			'parserfiles'	=> $parserfiles,
			'stylesheets'	=> $stylesheets,
			'pathinfo'		=> $pathinfo,
			'writable'		=> $writable,
			'form_action'	=> $form_action,
			'file'			=> $file
		));
		
		return $form;
	}
	
	
	function DataTree($table, $options, $fields) {
		
		if(!isset($options['nouns']['top'])) {
			$this->title = 'Не задано поле иерархии';
			return '<p>Пожалуйста, укажите поле иерахии для таблицы «'.$table.'». Это поле используется для соотношения подразделов к разделам, как правило, называется top, parent и т.д. Задается в секции «nouns» аргументов метода DataTree.</p>';
		}
		
		//Собираем данные о полях таблицы
		$syscolumns = $this->TableColumns($table);
		$deleted_field_exist = false;
		if(isset($options['nouns']['deleted'])) {
			foreach ($syscolumns as $i) {
				if($i['Field'] == $options['nouns']['deleted']) $deleted_field_exist = true;
			}
		}
		
		//Добавление
		if(isset($_GET['create'])) {
			
			//Фикс для if_empty_make_uri
			$insert_uri_fields = '';
			$insert_uri_values = '';
			foreach ($fields as $field=>$helpers) {
				if(isset($helpers['if_empty_make_uri']) && $helpers['if_empty_make_uri'] == $options['nouns']['name']) {
					$insert_uri_fields .= ', `'.$field.'`';
					$insert_uri_values .= ", '".q(makeURI($_POST['name']))."'";
				}
			}
			
			//$id = (int)$_POST['id'];
			$name = $_POST['name'];
			$ref_id = (int)$_POST['ref_id'];
			switch ($_POST['type']) {
				case 'before':
					$new_top = $this->db->rows("SELECT `".$options['nouns']['top']."` FROM `prefix_$table` WHERE `id` = $ref_id");
					$new_top = $new_top[0][0];
					$this->db->query("
						SELECT
							`".$options['nouns']['id']."`,
							`".$options['nouns']['order']."`
						FROM `prefix_$table`
						WHERE
							`".$options['nouns']['top']."` = $new_top
						ORDER BY `".$options['nouns']['order']."`
					");
					$branch = array();
					while ($i = $this->db->fetch()) {
						$branchItems[$i[$options['nouns']['id']]] = $i[$options['nouns']['order']];
					}
					//Расчет будущего порядка order
					//$branchItems[0] = $branchItems[$ref_id] - 0.5;
					
					asort($branchItems);
					
					$i = 0;
					foreach ($branchItems as $k=>$v) {
						if($k == $ref_id) {
							$this->db->query("
								INSERT INTO `prefix_$table`
									(
										`".$options['nouns']['name']."`,
										`".$options['nouns']['order']."`,
										`".$options['nouns']['top']."`,
										`".$options['nouns']['created']."`
										$insert_uri_fields
									)
								VALUES (
									'".q($name)."',
									".$i++.",
									$new_top,
									NOW()
									$insert_uri_values
								)
							");
							echo $this->db->last_insert_id();
						}
						$this->db->query("
							UPDATE `prefix_$table`
							SET
								`".$options['nouns']['top']."` = $new_top,
								`".$options['nouns']['order']."` = ".$i++."
							WHERE `".$options['nouns']['id']."` = $k
						");
					}
				break;
				
				case 'after':
					$new_top = $this->db->rows("SELECT `".$options['nouns']['top']."` FROM `prefix_$table` WHERE `id` = $ref_id");
					$new_top = $new_top[0][0];
					$this->db->query("
						SELECT
							`".$options['nouns']['id']."`,
							`".$options['nouns']['order']."`
						FROM `prefix_$table`
						WHERE
							`".$options['nouns']['top']."` = $new_top
						ORDER BY `".$options['nouns']['order']."`
					");
					$branch = array();
					while ($i = $this->db->fetch()) {
						$branchItems[$i[$options['nouns']['id']]] = $i[$options['nouns']['order']];
					}
					//Расчет будущего порядка order
					//$branchItems[0] = $branchItems[$ref_id] - 0.5;
					
					asort($branchItems);
					
					$i = 0;
					foreach ($branchItems as $k=>$v) {
						if($k == $ref_id) {
							$this->db->query("
								INSERT INTO `prefix_$table`
									(
										`".$options['nouns']['name']."`,
										`".$options['nouns']['order']."`,
										`".$options['nouns']['top']."`,
										`".$options['nouns']['created']."`
										$insert_uri_fields
									)
								VALUES (
									'".q($name)."',
									".(2 + $i++).",
									$new_top,
									NOW()
									$insert_uri_values
								)
							");
							echo $this->db->last_insert_id();
						}
						$this->db->query("
							UPDATE `prefix_$table`
							SET
								`".$options['nouns']['top']."` = $new_top,
								`".$options['nouns']['order']."` = ".$i++."
							WHERE `".$options['nouns']['id']."` = $k
						");
					}
				break;
				
				case 'inside':
					$this->db->query("
						INSERT INTO `prefix_$table`
							(
								`".$options['nouns']['name']."`,
								`".$options['nouns']['order']."`,
								`".$options['nouns']['top']."`,
								`".$options['nouns']['created']."`
								$insert_uri_fields
							)
						VALUES (
							'".q($name)."',
							0,
							$ref_id,
							NOW()
							$insert_uri_values
						)
					");
					echo $this->db->last_insert_id();
				break;
			}
			exit();
		}
		
		//Удаление
		if(isset($_GET['delete'])) {
			$id = (int)$_POST['id'];
			
			//Собираем внутренние топики для удаления
			$recDelTopics = function($topic_id, $options, $table, $recDelTopics){
				$db = db();
				$topics_id = array();
				$db->query("SELECT `{$options['nouns']['id']}` FROM `prefix_$table` WHERE `{$options['nouns']['top']}` = $topic_id");
				while ($i = $db->fetch()) {
					$topics_id[] = $i[$options['nouns']['id']];
					$topics_id = array_merge($topics_id, $recDelTopics($i[$options['nouns']['id']], $options, $table, $recDelTopics));
				}
				return $topics_id;
			};
			$toDelTopics = $recDelTopics($id, $options, $table, $recDelTopics);
			
			//Добавляем указанный верхний к удалению
			$toDelTopics[] = $id;
			
			//Удаление топиков
			if($deleted_field_exist) {
				$this->db->query("UPDATE `prefix_$table` SET `{$options['nouns']['deleted']}` = 'Y' WHERE `{$options['nouns']['id']}` IN (".implode(',',$toDelTopics).")");
			} else {
				$this->db->query("DELETE FROM `prefix_$table` WHERE `{$options['nouns']['id']}` IN (".implode(',',$toDelTopics).")");
			}
			
			//Удаление записей подопечной таблицы
			if(isset($options['inner']['table'])) {
				if(isset($options['inner']['deleted'])) {
					$this->db->query("UPDATE `prefix_{$options['inner']['table']}` SET `{$options['inner']['deleted']}` = 'Y' WHERE `{$options['inner']['top_key']}` IN (".implode(',',$toDelTopics).")");
				} else {
					$this->db->query("DELETE FROM `prefix_{$options['inner']['table']}` WHERE `{$options['inner']['top_key']}` IN (".implode(',',$toDelTopics).")");
				}
			}
			
			exit();
		}
		
		//Переименование
		if(isset($_GET['rename'])) {
			
			//Фикс для if_empty_make_uri
			$update_uri_fields = '';
			foreach ($fields as $field=>$helpers) {
				if(isset($helpers['if_empty_make_uri']) && $helpers['if_empty_make_uri'] == $options['nouns']['name']) {
					$update_uri_fields .= ", `$field` = '".makeURI($_POST['name'])."'";
				}
			}
			
			$id = (int)$_POST['id'];
			$name = $_POST['name'];
			$this->db->query("
				UPDATE `prefix_$table`
				SET
					`".$options['nouns']['name']."` = '".q($name)."'
					$update_uri_fields
				WHERE `".$options['nouns']['id']."` = $id
			");
			exit();
		}
		
		//Смена сортировки или перемещение
		if(isset($_GET['move'])) {
			$id = (int)$_POST['id'];
			$ref_id = (int)$_POST['ref_id'];
			switch ($_POST['type']) {
				case 'before':
					$new_top = $this->db->rows("SELECT `".$options['nouns']['top']."` FROM `prefix_$table` WHERE `id` = $ref_id");
					$new_top = $new_top[0][0];
					$this->db->query("
						SELECT
							`".$options['nouns']['id']."`,
							`".$options['nouns']['order']."`
						FROM `prefix_$table`
						WHERE
							`".$options['nouns']['top']."` = $new_top
						ORDER BY `".$options['nouns']['order']."`
					");
					$branch = array();
					while ($i = $this->db->fetch()) {
						$branchItems[$i[$options['nouns']['id']]] = $i[$options['nouns']['order']];
					}
					//Вставка итема, который мы переносим (-0.5 как бы намекает что перед нужным итемом)
					$branchItems[$id] = $branchItems[$ref_id] - 0.5;
					
					asort($branchItems);
					
					$i = 0;
					foreach ($branchItems as $k=>$v) {
						$this->db->query("
							UPDATE `prefix_$table`
							SET
								`".$options['nouns']['top']."` = $new_top,
								`".$options['nouns']['order']."` = ".$i++."
							WHERE `".$options['nouns']['id']."` = $k
						");
					}
				break;
				
				case 'after':
					$new_top = $this->db->rows("SELECT `".$options['nouns']['top']."` FROM `prefix_$table` WHERE `id` = $ref_id");
					$new_top = $new_top[0][0];
					$this->db->query("
						SELECT
							`".$options['nouns']['id']."`,
							`".$options['nouns']['order']."`
						FROM `prefix_$table`
						WHERE
							`".$options['nouns']['top']."` = $new_top
						ORDER BY `".$options['nouns']['order']."`
					");
					$branch = array();
					while ($i = $this->db->fetch()) {
						$branchItems[$i[$options['nouns']['id']]] = $i[$options['nouns']['order']];
					}
					//Вставка итема, который мы переносим (+0.5 как бы намекает что после нужного итема)
					$branchItems[$id] = $branchItems[$ref_id] + 0.5;
					
					asort($branchItems);
					
					$i = 0;
					foreach ($branchItems as $k=>$v) {
						$this->db->query("
							UPDATE `prefix_$table`
							SET
								`".$options['nouns']['top']."` = $new_top,
								`".$options['nouns']['order']."` = ".$i++."
							WHERE `".$options['nouns']['id']."` = $k
						");
					}
				break;
				
				case 'inside':
					$this->db->query("
						UPDATE `prefix_$table`
						SET
							`".$options['nouns']['top']."` = $ref_id,
							`".$options['nouns']['order']."` = 0
						WHERE `".$options['nouns']['id']."` = $id
					");
				break;
			}
			exit();
		}
		
		//JSON для дерева
		if(isset($_GET['json'])) {
			
			//Строим дерево
			if(isset($options['nouns']['order'])) {
				$order = $options['nouns']['order'];
			} else {
				$order = $options['nouns']['id'];
			}
			$where = '';
			if($deleted_field_exist) {
				$where .= " AND `{$options['nouns']['deleted']}` = 'N'";
			}
			$this->db->query("SELECT * FROM `prefix_$table` WHERE 1 $where ORDER BY `$order`");
			$branches = array();
			while ($i = $this->db->fetch()) {
				$branches[$i[$options['nouns']['top']]][$i[$options['nouns']['id']]] = $i;
				$tree_list[$i[$options['nouns']['id']]] = $i;
			}
			
			//Количества подопечной таблицы
			if(isset($options['inner']['table']) && isset($options['inner']['top_key'])) {
				if(isset($options['inner']['deleted'])) {
					$inner_where = "AND `".$options['inner']['deleted']."` = 'N'";
				} else {
					$inner_where = '';
				}
				$this->db->query("
					SELECT
						`".$options['inner']['top_key']."`,
						COUNT(*) AS `counts`
					FROM `prefix_".$options['inner']['table']."`
					WHERE 1 $inner_where
					GROUP BY `".$options['inner']['top_key']."`");
				$innerList = array();
				while ($i = $this->db->fetch()) {
					$innerList[$i[$options['inner']['top_key']]] = $i['counts'];
				}
			}
			
			function treeBuild($branches, $options, $branch_id=0, $innerList=array()) {
				$branch = array();
				foreach ($branches[$branch_id] as $b) {
					//Количества подопечной таблицы
					if(isset($options['inner']['table']) && isset($options['inner']['top_key'])) {
						if(isset($innerList[$b[$options['nouns']['id']]]) && $innerList[$b[$options['nouns']['id']]] > 0) {
							$add_count = ' ('.$innerList[$b[$options['nouns']['id']]].')';
						} else {
							$add_count = '';
						}
					} else {
						$add_count = '';
					}
					//Если запись — узел (ветка)
					if(isset($branches[$b[$options['nouns']['id']]])) {
						$branch[] = array(
							'data'		=> array(
								'title'			=> $b[$options['nouns']['name']].$add_count,
								'attributes'	=> array('id' => 't'.$b[$options['nouns']['id']])
							),
							'children'	=> treeBuild($branches, $options, $b[$options['nouns']['id']], $innerList)
						);
					}
					//Если запись — лист
					else {
						$branch[] = array(
							'data'		=> array(
								'title'			=> $b[$options['nouns']['name']].$add_count,
								//'icon'			=> '/admin/images/icons/document-text-image.png',
								'attributes'	=> array('id' => 't'.$b[$options['nouns']['id']])
							)
						);
					}
				}
				return $branch;
			}
			$tree = treeBuild($branches, $options, 0, $innerList);
			
			$json_tree = json_encode($tree);
			
			echo $json_tree;
			
			exit();
		}
		
		if(isset($options['controls'])) {
			$listLink = $options['controls']['list'];
		} else {
			$listLink = '';
		}
		
		$tree_html = tpl('widgets/tree', array(
			'table'		=> $table,
			'link'		=> $this->GetLink(),
			'listLink'	=> $listLink
		));
		
		//Форма добавления/редактирования jQueryUI
		$edit = $this->DataTable_AddEdit($_SERVER['REQUEST_URI'], $table, $options, NULL, $fields);
		
		return $tree_html.$edit;
	}
	
	
	
	
	
	
	function Hint() {
		if(isset($this->hint) && is_array($this->hint) && isset($this->hint['text'])) {
			if(!isset($this->hint['title'])) $this->hint['title'] = $this->title;
			$ret = tpl('widgets/hint',$this->hint);
			return $ret;
		} else {
			return '';
		}
	}
	
	
	function SubMenu() {
		if(isset($this->submenu) && is_array($this->submenu) && !empty($this->submenu)) {
			foreach ($this->submenu as $k=>$v) {
				$this->submenu[$k] = array();
				$this->submenu[$k]['act'] = false;
				if(isset($_GET['method'])) {
					if($_GET['method'] == $k) $this->submenu[$k]['act'] = true;
				} elseif($k == 'Info') $this->submenu[$k]['act'] = true;
				$this->submenu[$k]['name'] = $v;
				$this->submenu[$k]['link'] = $this->GetLink($k, array(), $this->called_class);
			}
			return tpl('widgets/submenu', array('data'=>$this->submenu));
		} else {
			return '';
		}
	}
	
}

?>