<?php

class Catalog {
	
	private $data, $holder;
	public $path, $topic, $product, $menuOpened;
	
	private $localCacheLink, $productsFilter, $currentTopicTypes;
	
	private $alter_pages = array(
		'seen'		=> array('method'=>'Seen', 'name'=>'Вы смотрели'),
		'search'	=> array('method'=>'Search', 'name'=>'Поиск по каталогу'),
		'compare'	=> array('method'=>'Compare', 'name'=>'Сравнение товаров')
	);
	
	/**
	 * Конструктор модуля
	 */
	function __construct() {
		if(!session_id()) session_start();
		$this->data = $GLOBALS['data'];
	}
	
	/**
	 * Метод вызываемый для работы модуля
	 */
	function Output() {
		$this->buildModulePath();
		return $this->startController();
	}
	
	/**
	 * SEO для каталога
	 * @param int $id
	 */
	private function SEO($table, $id, $module=__CLASS__) {
		$this->seo = db()->query_first("SELECT * FROM `prefix_seo` WHERE `module` = '".$module."' AND `module_id` = ".(int)$id." AND `module_table` = '".$table."'");
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
			}
		}
	}
	
	/**
	 * Создает и проверяет путь модуля на валидность
	 * заполняет необходимые для работы модуля переменные
	 * $this->holder, $this->path, $this->topic, $this->product
	 *
	 * При случае неверной адресации отдает 404
	 *
	 * Валидный адрес:
	 * [группа](/.../[группа](/[товар]))
	 *
	 */
	private function buildModulePath() {
		$this->holder = end($GLOBALS['path']);
		
		$request =
			strpos($_SERVER['REQUEST_URI'], '?')!==false
			?
				substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'))
			:
				$_SERVER['REQUEST_URI'];
		
		$mlink = trim(str_replace(linkById($this->holder['id']), '', $request));
		$mpath = array_filter(explode('/', $mlink));
		
		//Альтернативные страницы
		$check_alter = current($mpath);
		if(isset($this->alter_pages[$check_alter])) {
			if(!method_exists($this, $this->alter_pages[$check_alter]['method'])) page404();
			
			$params_path = array();
			$params_path[] = array(
				'type'		=> 'alter_page',
				'method'	=> $this->alter_pages[$check_alter]['method'],
				'data'		=> $check_alter
			);
			
			foreach ($mpath as $ppath) {
				if(!isset($skipFirst)) { $skipFirst=true; continue; }
				$params_path[] = array(
					'type'	=> 'param',
					'data'	=> $ppath
				);
			}
			$this->path = $params_path;
			return true;
		}
		
		$topics = $this->data->GetData('products_topics', "AND `show` = 'Y'");
		$topicsByTop = array();
		foreach ($topics as $topic) $topicsByTop[$topic['top']][] = $topic;
		
		$top = 0;
		$path = array();
		//По путю модуля
		foreach ($mpath as $k=>$chunk) {
			//По топикам
			if(isset($topicsByTop[$top]))
			foreach ($topicsByTop[$top] as $topic) {
				if($topic['nav'] == $chunk) {
					$path[$k]['type'] = 'topic';
					$path[$k]['data'] = $topic;
					$this->topic = $topic;
					$top = $topic['id'];
					unset($mpath[$k]);
					break;
				}
			}
		}
		
		//Если остался неизвестный трэшняк, то сори, 404
		if(count($mpath) > 1) page404();
		
		//А так это возможно товар
		if(count($mpath) == 1) {
			$product = current($mpath);
			$curTopic = end($path);
			$curTopic = $curTopic['data'];
			$curProduct = end($mpath);
			if(is_numeric($curProduct)) {
				$product_sql = "AND p.`id` = ".(int)$curProduct;
			} else {
				$product_sql = "AND p.`nav` = '".q($curProduct)."'";
			}
			$productData = db()->rows("
				SELECT p.*, b.name AS `brand_name`, t.singular_name AS `product_singular_name`
				FROM `prefix_products` AS p
				LEFT JOIN `prefix_products_brands` AS b ON b.id = p.brand
				LEFT JOIN `prefix_products_topics` AS t ON t.id = p.top
				WHERE p.`deleted` = 'N' AND p.`show` = 'Y' AND p.`top` = ".(int)$curTopic['id']." $product_sql
			");
			$productCount = count($productData);
			if($productCount == 0) page404();
			elseif($productCount > 1) $productData = current($productData);
			else $productData = current($productData);
			
			$this->product = $productData;
			
			$path[] = array(
				'type'	=> 'product',
				'data'	=> $productData
			);
		}
		
		$this->path = $path;
		return true;
	}
	
	/**
	 * Запуск нужного метода модуля
	 */
	private function startController() {
		//Альтернативные страницы
		$check_alter = current($this->path);
		if($check_alter['type'] == 'alter_page') {
			//unset($this->path[0]);
			return $this->$check_alter['method']();
		}
		
		$last = end($this->path);
		
		//Главная каталога
		if(empty($this->path)) {
			return $this->MainPage();
		}
		//Товар
		elseif($last['type'] == 'product') {
			return $this->Product();
		}
		//Группа (список товаров)
		else {
			return $this->ProductsList();
		}
	}
	
	
	public function MainPage() {
		$this->SEO('content', $this->holder['id'], 'Content');
		
		return tpl('modules/'.__CLASS__.'/mainPage', array(
			'name'	=> $this->holder['name'],
			'title'	=> isset($this->seo['title'])&&!empty($this->seo['title'])?$this->seo['title']:$this->holder['name'].' — '.$GLOBALS['config']['site']['title']
		));
	}
	


	/**
	 * Блок каталога на главной странице в теме cloudyNoon
	 */
	public function MainPage_cloudyNoon() {
		$mostPopularProducts =  array();

		//Список всех категорий по родительским
		$topicsByTop = $this->CatalogMenuTopics();

		//Количества товаров в категориях
		$productsCounts = db()->rows("
			SELECT `top`, COUNT(*) AS 'count' FROM `prefix_products` WHERE `deleted` = 'N' AND `show` = 'Y'
			GROUP BY `top`
		", MYSQLI_ASSOC, 'top');

		//Самые популярные товары в топовых категориях
		function getChildIds($key, $topicsByTop, $findedKeys) {
			if(is_array($topicsByTop[$key])) {
				foreach ($topicsByTop[$key] as $ckey => $cvalue) {
					$findedKeys[$ckey] = $ckey;
					$findedKeys = array_merge($findedKeys, getChildIds($ckey, $topicsByTop, $findedKeys));
				}
			}
			return array_unique($findedKeys);
		}
		foreach ($topicsByTop[0] as $key => $value) {
			$mostPopularProducts[$key] = getChildIds($key, $topicsByTop, array());
			$mostPopularProducts[$key][] = $key;
		}
		$rootTopicsSql = array();
		foreach ($mostPopularProducts as $rootTopicId => $topicIds) {
			$rootTopicsSql[] = "
				(
					SELECT p.id, p.top, p.rate, p.name AS pname, t.singular_name, b.name AS bname, $rootTopicId AS root
					FROM `prefix_products` as p
					LEFT JOIN `prefix_products_brands` AS b ON b.id = p.brand
					INNER JOIN `prefix_products_topics` AS t ON t.id = p.top
					WHERE p.`deleted` = 'N' AND p.`show` = 'Y' AND p.`is_exist` = 'Y' AND p.`top` IN (".implode(',', $topicIds).")
					ORDER BY `rate` DESC
					LIMIT 8
				)
			";
		}
		$rootTopicsSql = implode(' UNION ', $rootTopicsSql);
		$products = db()->rows($rootTopicsSql, MYSQLI_ASSOC);
		$productsByRoot = array();
		foreach ($products as $product) {
			$productsByRoot[$product['root']][$product['id']] = $product;
			$productsByRoot[$product['root']][$product['id']]['link'] = $this->Link($product['top'], $product['id']);
		}

		return tpl('modules/'.__CLASS__.'/mainPageBlock', array(
			'topicsByTop'		=> $topicsByTop,
			'productsCounts'	=> $productsCounts,
			'productsByRoot'	=> $productsByRoot
		));
	}

	
	/**
	 * Меню каталога
	 */
	function Menu() {
		
		$topicsByTop = $this->CatalogMenuTopics();
		
		return tpl('modules/'.__CLASS__.'/menu', array(
			'topics'	=> $topicsByTop
		));
	}


	/**
	 * Возвращает массив категрий сгруппированный по родительской категории
	 */
	private function CatalogMenuTopics() {
		$counts_ = db()->rows("SELECT `top` , COUNT(`id`) AS `count` FROM `prefix_products` WHERE `deleted`='N' AND `show`='Y' GROUP BY `top`");
		$counts = array();
		foreach ($counts_ as $count) {
			$counts[$count['top']] = $count['count'];
		}

		$active_ids = array();
		if(!empty($this->path)) {
			foreach ($this->path as $v) {
				if($v['type'] == 'topic') {
					$active_ids[] = $v['data']['id'];
				}
			}
		}

		$topics = $this->data->GetData('products_topics', "AND `show` = 'Y'");
		$topicsByTop = array();
		foreach ($topics as $v) {
			$v['link'] = $this->Link($v['id']);
			$v['count'] = isset($counts[$v['id']])?$counts[$v['id']]:0;
			
			//set active
			if(in_array($v['id'], $active_ids)) {
				$v['active'] = true;
				$this->menuOpened = true;
			} else {
				$v['active'] = false;
			}
			
			
			$topicsByTop[$v['top']][$v['id']] = $v;
		}
		unset($topics);

		return $topicsByTop;
	}


	/**
	 * Возвращает массив топовых категрий для модуля контента (для генерации меню)
	 */
	public function SubMenu() {
		$topicsByTop = $this->CatalogMenuTopics();
		return $topicsByTop[0];
	}
	
	
	/**
	 * Генерация ссылки
	 */
	function Link($topic_id=0, $product_id=0) {
		$topic_id=(int)$topic_id;
		
		if($topic_id != 0 || $product_id != 0) {
			$topics = $this->data->GetData('products_topics', "AND `show` = 'Y'");
			
			if($product_id !== 0) {
				//if($topic_id == 0) {
				//	$product = $this->data->GetDataById('products', $product_id);
				//	$topic_id = $product['top'];
				//}
				$product = $this->data->GetDataById('products', $product_id);
				if(isset($product['nav']) && !empty($product['nav'])) {
					$product_link = '/'.$product['nav'];
				} else {
					$product_link = '/'.$product_id;
				}
			} else {
				$product_link = '';
			}
			
			foreach($topics as $i) $topicsByTop[$i['top']][$i['id']] = $i;
			$linkById = function($topic_id, $topicsByTop, $topics, $linkById) {
				if($topic_id==0) return;
				return $linkById($topics[$topic_id]['top'], $topicsByTop, $topics, $linkById).'/'.$topics[$topic_id]['nav'];
			};
			$topic_link = $linkById($topic_id, $topicsByTop, $topics, $linkById);
			
			$prepared_link = $topic_link.$product_link;
		}
		
		if(empty($this->localCacheLink)) $this->localCacheLink = linkByModule(__CLASS__);
		
		return $this->localCacheLink.$prepared_link;
	}
	
	
	/**
	 * Массив для хлебных крошек модуля
	 *
	 * array( array('name','link') )
	 */
	public function breadCrumbs() {
		if(!empty($this->path)) {
			$ret = array();
			foreach ($this->path as $i) {
				if($i['type'] == 'alter_page') {
					$ret[] = array(
						'name'	=> $this->alter_pages[$i['data']]['name'],
						'link'	=> linkByModule('Catalog').'/'.$i['data']
					);
					break;
				}
				$link = '';
				$name = $i['data']['name'];
				if($i['type'] == 'topic') $link = $this->Link($i['data']['id']);
				if($i['type'] == 'product') {
					$name = $i['data']['product_singular_name'].' '.$i['data']['brand_name'].' '.$i['data']['name'];
					$link = $this->Link($i['data']['top'], $i['data']['id']);
				}
				$ret[] = array('name'=>$name, 'link'=>$link);
			}
			return $ret;
		} else return array();
	}
	
	function GetSorting() {
		$this->sortPanel();
		return array($this->sorting['field'], $this->sorting['direction']);
	}
	
	function sortPanel() {
		if(isset($this->sorting['html'])) return $this->sorting['html'];
		//Значения по-умолчанию
		$options = array(
			'rate'	=> array('name'=>'По рейтингу', 'current'=>true, 'direction'=>'desc'),
			'name'	=> array('name'=>'По названию', 'current'=>false, 'direction'=>'asc'),
			'price'	=> array('name'=>'По цене', 'current'=>false, 'direction'=>'asc')
		);
		
		//Проставляем ссылки
		foreach ($options as $field=>$val) {
			$direction = $val['direction'];//=='asc'?'desc':'asc';
			$options[$field]['link'] = getget(array('page'=>false, 'order'=>$field, 'orderd'=>$direction));
			if($val['current']) $options[$field]['link'] = getget(array('page'=>false, 'order'=>$field, 'orderd'=>$direction=='asc'?'desc':'asc'));
			else $options[$field]['link'] = getget(array('page'=>false, 'order'=>$field, 'orderd'=>$direction));
		}
		
		//Переопределения
		if(isset($_GET['order']) && isset($_GET['orderd'])) {
			if(isset($options[$_GET['order']]) && ($_GET['orderd']=='asc'||$_GET['orderd']=='desc')) {
				foreach ($options as $field=>$val) {
					if($_GET['order'] == $field) {
						$direction = $_GET['orderd'];//=='asc'?'desc':'asc';
						$options[$field]['current'] = true;
						$options[$field]['direction'] = $direction;
						$options[$field]['link'] = getget(array('page'=>false, 'order'=>$field, 'orderd'=>$_GET['orderd']=='asc'?'desc':'asc'));
					} else {
						$options[$field]['current'] = false;
					}
				}
			}
		}
		
		$this->sorting = array();
		foreach ($options as $field=>$val) {
			if($val['current']) {
				$this->sorting['field'] = $field;
				$this->sorting['direction'] = $val['direction'];
				break;
			}
		}
		
		$this->sorting['html'] = tpl('modules/'.__CLASS__.'/sortpanel', array(
			'options'	=> $options
		));
		return $this->sorting['html'];
	}
	
	
	/**
	 * Листинг товаров
	 */
	function ProductsList() {
		
		$additional_brand_title = '';
		$product_ids = array();
		$addGet = array();
		$db = db();
		
		//SEO
		$this->SEO('products_topics', $this->topic['id']);
		
		//Запрос
		if(isset($_GET['addGet'])) {
			parse_str($_GET['addGet'], $addGet);
			unset($_GET['addGet']);
			$_GET = array_merge($_GET, $addGet);
		}
		
		//Хар-ки топика
		if(empty($this->currentTopicTypes)) $this->currentTopicTypes = unserialize($this->topic['types']);
		
		//Сортировка
		$this->GetSorting();
		if(isset($this->sorting)) {
			$sortField = '`'.$this->sorting['field'].'`';
			if($this->sorting['field'] == 'price') {
				$sortField = '(`price` * (1 - `discount` / 100))';
			}
			
			$sqlOrder = 'ORDER BY `is_exist`, '.$sortField.' '.$this->sorting['direction'];
		} else $sqlOrder = 'ORDER BY `rate` DESC';
		
		//Товары
		$products = $db->rows("
			SELECT p.*, b.name AS `brand_name`, t.singular_name AS `product_singular_name` FROM `prefix_products` AS p
			LEFT JOIN `prefix_products_brands` AS b ON b.id = p.brand
			LEFT JOIN `prefix_products_topics` AS t ON p.top = t.id
			WHERE
				p.`deleted` = 'N' AND
				p.`show` = 'Y' AND
				p.`top` = ".(int)$this->topic['id']."
			$sqlOrder
		", MYSQL_ASSOC);
		foreach ($products as $k=>$product) {
			//Цена
			$products[$k]['priceOld'] = $this->Price($product['price']);
			$products[$k]['price'] = $this->Price($product['price'], $product['discount']);
			
			//Распаковка характеристик
			$products[$k]['types'] = unserialize($product['types']);
			
			//Ссылка
			$products[$k]['link'] = $this->Link($product['top'], $product['nav']?$product['nav']:$product['id']);
			
			//Сравнение
			$products[$k]['inCompare'] = false;
			if(isset($_SESSION['compare']) && is_array($_SESSION['compare'])) {
				if(isset($_SESSION['compare'][$product['id']])) {
					$products[$k]['inCompare'] = true;
				}
			}
			
			//Сниппет
			$snippet = array();
			if(!empty($this->currentTopicTypes) && !empty($products[$k]['types']))
			foreach ($products[$k]['types'] as $groupKey=>$group) {
				foreach ($group as $typeKey=>$type) {
					if(!empty($type)) {
						if(isset($this->currentTopicTypes[$groupKey]['types'][$typeKey]) && $this->currentTopicTypes[$groupKey]['types'][$typeKey]['main']) {
							switch ($this->currentTopicTypes[$groupKey]['types'][$typeKey]['type']) {
								case 'float':
									if($type !== '') {
										$snippet[] = $this->currentTopicTypes[$groupKey]['types'][$typeKey]['name'].': '.$type.''.$this->currentTopicTypes[$groupKey]['types'][$typeKey]['unit'];
									}
								break;
								
								case 'range':
									if($type['from'] !== '' && $type['to'] !== '') {
										if($type['from'] == $type['to']) {
											$snippet[] = $this->currentTopicTypes[$groupKey]['types'][$typeKey]['name'].': '.$type['from'].''.$this->currentTopicTypes[$groupKey]['types'][$typeKey]['unit'];
										} else {
											$snippet[] = $this->currentTopicTypes[$groupKey]['types'][$typeKey]['name'].': '.$type['from'].'—'.$type['to'].''.$this->currentTopicTypes[$groupKey]['types'][$typeKey]['unit'];
										}
									}
									elseif($type['from'] !== '') {
										$snippet[] = $this->currentTopicTypes[$groupKey]['types'][$typeKey]['name'].': от '.$type['from'].''.$this->currentTopicTypes[$groupKey]['types'][$typeKey]['unit'];
									}
									elseif($type['to'] !== '') {
										$snippet[] = $this->currentTopicTypes[$groupKey]['types'][$typeKey]['name'].': до '.$type['to'].''.$this->currentTopicTypes[$groupKey]['types'][$typeKey]['unit'];
									}
								break;
								
								case 'yn':
									if($type == 'Y') $snippet[] = $this->currentTopicTypes[$groupKey]['types'][$typeKey]['name'];
								break;
								
								case 'select':
									if($type !== '' && $type !== 0) {
										if(isset($this->currentTopicTypes[$groupKey]['types'][$typeKey]['select'][$type])) {
											$snippet[] = $this->currentTopicTypes[$groupKey]['types'][$typeKey]['name'].': '.$this->currentTopicTypes[$groupKey]['types'][$typeKey]['select'][$type];
										}
									}
								break;
								
								case 'text':
									if(!empty($type)) {
										$snippet[] = $this->currentTopicTypes[$groupKey]['types'][$typeKey]['name'].': '.$type;
									}
								break;
							}
						}
					}
				}
			}
			$products[$k]['snippet'] = $snippet;
		}
		
		//Бренды
		$brands_ids = array();
		if(isset($_GET['brands']) && is_array($_GET['brands'])) {
			$sBrands = $_GET['brands'];
		} else {
			$sBrands = array();
		}
		foreach ($products as $p) $brands_ids[$p['brand']] = true;
		$brands_ids = array_keys($brands_ids);
		$brands = array();
		if(!empty($brands_ids)) {
			$brands = $db->rows("
				SELECT * FROM `prefix_products_brands`
				WHERE
					`deleted` = 'N' AND
					`show` = 'Y' AND
					`id` IN (".implode(',', $brands_ids).")
				ORDER BY `order`
			", MYSQL_ASSOC);
			foreach ($brands as $k=>$v) {
				$brands[$k]['checked'] = false;
				if(!empty($sBrands)) {
					foreach ($sBrands as $bk=>$bv) {
						if($bv == $v['nav']) {
							$brands[$k]['checked'] = true;
							$additional_brand_title = $brands[$k]['name'];
							$this->productsFilter['brands'][] = $brands[$k]['id'];
						}
					}
				}
				$brands[$k]['link'] = getget(array('brands'=>array($v['nav']),'page'=>false));
			}
		}
		if(count($sBrands) > 1) $additional_brand_title = '';
		
		//Диапазон цен для слайдера
		$price_range = $db->query_first("SELECT MIN(`price`) AS `min`, MAX(`price`) AS `max` FROM `prefix_products` WHERE `deleted` = 'N' AND `show` = 'Y' AND `top` = ".(int)$this->topic['id']."");
		//echo "SELECT MIN(`price`) AS `min`, MAX(`price`) AS `max` FROM `prefix_products` WHERE `deleted` = 'N' AND `show` = 'Y' AND `top` = ".(int)$this->topic['id']."";
		$discounted_min = $db->query_first("SELECT `price` * (1 - `discount` / 100) AS `dprice` FROM `prefix_products` WHERE `deleted` = 'N' AND `show` = 'Y' AND `top` = ".(int)$this->topic['id']." ORDER BY `dprice` ASC LIMIT 1");
		$price_min = $this->Price($discounted_min['dprice']); //Скидка здесь уже содержится, пересчитывать ее не нужно
		$price_min = $price_min<0?0:$price_min;
		$price_max = $this->Price($price_range['max']);
		$price_max = $price_max<0?0:$price_max;
		$price_from = isset($_GET['PriceFromValue'])?(int)$_GET['PriceFromValue']:$price_min;
		$price_to = isset($_GET['PriceToValue'])?(int)$_GET['PriceToValue']:$price_max;
		$slider_vals = array(
			'min'	=> $price_min,
			'max'	=> $price_max,
			'from'	=> $price_from,
			'to'	=> $price_to,
			'step'	=> pow(10, abs(getSet('Catalog', 'price_round')))
		);
		if(isset($_GET['PriceFromValue']) && isset($_GET['PriceToValue'])) {
			$this->productsFilter['price'] = array(
				'form'	=> $price_from,
				'to'	=> $price_to
			);
		}
		
		//Только в наличии
		if(isset($_GET['exist'])) $this->productsFilter['exist'] = true;
		else $this->productsFilter['exist'] = false;
		
		//Блок подбора
		$selection = $this->SelectionBlock();
		
		//Фильтр товаров по брендам, цене и характеристикам
		$this->SelectionFilter($products);
		
		//Пэйджинг
		$products_paged = $this->Paging($products);
		$products = $products_paged['products'];
		$paging = $products_paged['rendered'];
		unset($products_paged);
		if($_GET['page'] > 1) $addPageTitle = ' (страница '.abs((int)$_GET['page']).')';
		else $addPageTitle = '';
		
		//Тайтл страницы (<head> <title>)
		if(isset($this->seo['title'])&&!empty($this->seo['title'])) $head_title = $this->seo['title'];
		else {
			$backpath = array_reverse($this->path);
			$head_title = array();
			foreach ($backpath as $i) {
				if($i['type'] == 'topic') {
					$head_title[] = $i['data']['name'];
				}
			}
			if(!empty($head_title)) $head_title[0] = $head_title[0].(empty($additional_brand_title)?'':' '.$additional_brand_title);
			$head_title = implode(' — ', $head_title).' — '.$GLOBALS['config']['site']['title'];
		}
		$head_title = $head_title.$addPageTitle;
		
		//Финальные приготовления
		$page_title = $this->topic['name'].(empty($additional_brand_title)?'':' '.$additional_brand_title).$addPageTitle;
		$brand_price_link = getget(array('page'=>false,'brands'=>false,'PriceFromValue'=>false,'PriceToValue'=>false), 1);
		//Подготавливаем картинки
		foreach ($products as $product) $product_ids[] = $product['id'];
		img()->PrepareImages('Catalog', $product_ids);
		//Ссылка текущей категории
		$link = $this->Link($this->topic['id']);
		
		/*
		 'name'	=> $this->holder['name'],
			'title'	=> isset($this->seo['title'])&&!empty($this->seo['title'])?$this->seo['title']:$this->holder['name'].' — '.$GLOBALS['config']['site']['title']
		 */

		//Вложенные категории
		$subCats = $db->rows("
			SELECT `id`, `name`, `anons` 
			FROM `prefix_products_topics` 
			WHERE `deleted` = 'N' AND `show` = 'Y' AND `top` = ".(int)$this->topic['id']."
			ORDER BY `rate` DESC
		", MYSQLI_ASSOC);
		$productsCounts = db()->rows("
			SELECT `top`, COUNT(*) AS 'count' FROM `prefix_products` WHERE `deleted` = 'N' AND `show` = 'Y'
			GROUP BY `top`
		", MYSQLI_ASSOC, 'top');
		foreach ($subCats as $key => $subCat) {
			$subCats[$key]['link'] = $this->Link($subCat['id']);
			$subCats[$key]['productsCount'] = $productsCounts[$subCat['id']];
		}

		return tpl('modules/'.__CLASS__.'/list', array(
			'title'			=> $head_title,
			'name'			=> $page_title,
			'brands'		=> $brands,
			'link'			=> $link,
			'brand_price_link'	=> $brand_price_link,
			'slider_vals'	=> $slider_vals,
			'exist'			=> $this->productsFilter['exist'],
		
			'selection'		=> $selection,
			
			'products'		=> $products,
			'paging'		=> $paging,
			'subCats'		=> $subCats
		));
	}
	
	function CustomProductsList($products) {
		if(empty($products) || !is_array($products)) return false;
		
		$db = db();
		
		$topics = array();
		$brands = array();
		$ids = array();
		foreach ($products as $k=>$product) {
			//$products[$k]['types'] = unserialize($product['types']);
			$ids[] = $product['id'];
			$topics[$product['top']] = true;
			$brands[$product['brand']] = true;
		}
		$topics = array_keys($topics);
		$brands = array_keys($brands);
		
		if(empty($topics)) return false;
		
		img()->PrepareImages('Catalog', $ids, true);
		
		$ptopics = $db->rows("SELECT * FROM `prefix_products_topics` WHERE `deleted` = 'N' AND `show` = 'Y' AND `id` IN (".implode(',', $topics).")", MYSQLI_ASSOC);
		$topics = array();
		foreach ($ptopics as $topic) {
			$topic['link'] = $this->Link($topic['id']);
			$topics[$topic['id']] = $topic;
		}
		
		$pbrands = $db->rows("SELECT * FROM `prefix_products_brands` WHERE `deleted` = 'N' AND `show` = 'Y' AND `id` IN (".implode(',', $brands).")", MYSQLI_ASSOC);
		$brands = array();
		foreach ($pbrands as $brand) $brands[$brand['id']] = $brand;
		
		foreach ($products as $k=>$product) {
			if(!isset($topics[$product['top']])) continue;
			
			//Цена
			//$products[$k]['price'] = $this->Price($product['price']);
			$products[$k]['priceOld'] = $this->Price($product['price']);
			$products[$k]['price'] = $this->Price($product['price'], $product['discount']);
			
			//Распаковка характеристик
			if(!is_array($products[$k]['types'])) $products[$k]['types'] = unserialize($product['types']);
			if(!is_array($topics[$product['top']]['types'])) $topics[$product['top']]['types'] = unserialize($topics[$product['top']]['types']);
			
			//Ссылка
			$products[$k]['link'] = $this->Link($product['top'], $product['nav']?$product['nav']:$product['id']);
			
			//Топик
			$products[$k]['topic'] = $topics[$product['top']];
			
			//Бренд
			if(isset($brands[$product['brand']])) {
				$products[$k]['brand'] = $brands[$product['brand']];
			} else {
				$products[$k]['brand'] = false;
			}
			
			//Сниппет
			$snippet = array();
			if(!empty($topics[$product['top']]['types']) && !empty($products[$k]['types']))
			foreach ($products[$k]['types'] as $groupKey=>$group) {
				foreach ($group as $typeKey=>$type) {
					if(!empty($type)) {
						if(isset($topics[$product['top']]['types'][$groupKey]['types'][$typeKey]) && $topics[$product['top']]['types'][$groupKey]['types'][$typeKey]['main']) {
							switch ($topics[$product['top']]['types'][$groupKey]['types'][$typeKey]['type']) {
								case 'float':
									if($type !== '') {
										$snippet[] = $topics[$product['top']]['types'][$groupKey]['types'][$typeKey]['name'].': '.$type.''.$topics[$product['top']]['types'][$groupKey]['types'][$typeKey]['unit'];
									}
								break;
								
								case 'range':
									if($type['from'] !== '' && $type['to'] !== '') {
										if($type['from'] == $type['to']) {
											$snippet[] = $topics[$product['top']]['types'][$groupKey]['types'][$typeKey]['name'].': '.$type['from'].''.$topics[$product['top']]['types'][$groupKey]['types'][$typeKey]['unit'];
										} else {
											$snippet[] = $topics[$product['top']]['types'][$groupKey]['types'][$typeKey]['name'].': '.$type['from'].'—'.$type['to'].''.$topics[$product['top']]['types'][$groupKey]['types'][$typeKey]['unit'];
										}
									}
									elseif($type['from'] !== '') {
										$snippet[] = $topics[$product['top']]['types'][$groupKey]['types'][$typeKey]['name'].': от '.$type['from'].''.$topics[$product['top']]['types'][$groupKey]['types'][$typeKey]['unit'];
									}
									elseif($type['to'] !== '') {
										$snippet[] = $topics[$product['top']]['types'][$groupKey]['types'][$typeKey]['name'].': до '.$type['to'].''.$topics[$product['top']]['types'][$groupKey]['types'][$typeKey]['unit'];
									}
								break;
								
								case 'yn':
									if($type == 'Y') $snippet[] = $topics[$product['top']]['types'][$groupKey]['types'][$typeKey]['name'];
								break;
								
								case 'select':
									if($type !== '' && $type !== 0) {
										if(isset($topics[$product['top']]['types'][$groupKey]['types'][$typeKey]['select'][$type])) {
											$snippet[] = $topics[$product['top']]['types'][$groupKey]['types'][$typeKey]['name'].': '.$topics[$product['top']]['types'][$groupKey]['types'][$typeKey]['select'][$type];
										}
									}
								break;
								
								case 'text':
									if(!empty($type)) {
										$snippet[] = $topics[$product['top']]['types'][$groupKey]['types'][$typeKey]['name'].': '.$type;
									}
								break;
							}
						}
					}
				}
			}
			$products[$k]['snippet'] = $snippet;
		}
		
		//Пэйджинг (сохраняется в $this->paging_rendered)
		$products_paged = $this->Paging($products);
		$products = $products_paged['products'];
		
		return tpl('modules/'.__CLASS__.'/items', array(
			'products'	=> $products
		));
	}
	
	function CustomLittleProductsList($products) {
		if(empty($products) || !is_array($products)) return false;
		
		$db = db();
		
		$topics = array();
		$brands = array();
		$ids = array();
		foreach ($products as $k=>$product) {
			//$products[$k]['types'] = unserialize($product['types']);
			$ids[] = $product['id'];
			$topics[$product['top']] = true;
			$brands[$product['brand']] = true;
		}
		$topics = array_keys($topics);
		$brands = array_keys($brands);
		
		if(empty($topics)) return false;
		
		img()->PrepareImages('Catalog', $ids, true);
		
		$ptopics = $db->rows("SELECT * FROM `prefix_products_topics` WHERE `deleted` = 'N' AND `show` = 'Y' AND `id` IN (".implode(',', $topics).")", MYSQLI_ASSOC);
		$topics = array();
		foreach ($ptopics as $topic) {
			$topic['link'] = $this->Link($topic['id']);
			$topics[$topic['id']] = $topic;
		}
		
		$pbrands = $db->rows("SELECT * FROM `prefix_products_brands` WHERE `deleted` = 'N' AND `show` = 'Y' AND `id` IN (".implode(',', $brands).")", MYSQLI_ASSOC);
		$brands = array();
		foreach ($pbrands as $brand) $brands[$brand['id']] = $brand;
		
		foreach ($products as $k=>$product) {
			if(!isset($topics[$product['top']])) continue;
			
			//Цена
			//$products[$k]['price'] = $this->Price($product['price']);
			$products[$k]['priceOld'] = $this->Price($product['price']);
			$products[$k]['price'] = $this->Price($product['price'], $product['discount']);
			
			//Ссылка
			$products[$k]['link'] = $this->Link($product['top'], $product['nav']?$product['nav']:$product['id']);
			
			//Топик
			$products[$k]['topic'] = $topics[$product['top']];
			
			//Бренд
			if(isset($brands[$product['brand']])) {
				$products[$k]['brand'] = $brands[$product['brand']];
			} else {
				$products[$k]['brand'] = false;
			}
		}
		
		return tpl('modules/'.__CLASS__.'/littleItems', array(
			'products'	=> $products
		));
	}
	
	function FeaturedList($limit=0) {
		if($limit > 0) $sql_limit = 'LIMIT '.(int)$limit;
		else $sql_limit = '';
		$products = db()->rows("SELECT * FROM `prefix_products` WHERE `is_featured` = 'Y' AND `deleted` = 'N' AND `show` = 'Y' AND `is_exist` = 'Y' ORDER BY RAND() DESC $sql_limit", MYSQLI_ASSOC);
		
		return $this->CustomProductsList($products);
	}
	
	function Seen() {
		if(!isset($_SESSION['seen']) || empty($_SESSION['seen'])) page404();
		
		$ids = array_keys($_SESSION['seen']);
		
		$ids = array_reverse($ids);
		
		$prod = db()->rows("SELECT * FROM `prefix_products` WHERE `id` IN (".implode(',', $ids).") AND `deleted` = 'N' AND `show` = 'Y' ", MYSQLI_ASSOC);
		
		$productsById = array();
		foreach ($prod as $i) $productsById[$i['id']] = $i;
		unset($prod);
		
		$products = array();
		foreach ($ids as $id) $products[$id] = $productsById[$id];
		unset($productsById);
		
		$text = $this->CustomProductsList($products);
		$text .= $this->paging_rendered;
		
		return tpl('page', array(
			'title'	=> $this->alter_pages['seen']['name'],
			'name'	=> $this->alter_pages['seen']['name'],
			'text'	=> $text
		));
	}
	
	function SeenBlock($limit=0) {
		if(!isset($_SESSION['seen']) || empty($_SESSION['seen'])) return ;
		
		$ids = array_keys($_SESSION['seen']);
		
		$ids = array_reverse($ids);
		if($limit > 0) $ids = array_slice($ids, 0, $limit);
		
		$prod = db()->rows("SELECT * FROM `prefix_products` WHERE `id` IN (".implode(',', $ids).") AND `deleted` = 'N' AND `show` = 'Y'", MYSQLI_ASSOC);
		
		$productsById = array();
		foreach ($prod as $i) $productsById[$i['id']] = $i;
		unset($prod);
		
		$products = array();
		foreach ($ids as $id) $products[$id] = $productsById[$id];
		unset($productsById);
		
		return tpl('modules/'.__CLASS__.'/promoBlocks/seen', array(
			'littleProductsList' => $this->CustomLittleProductsList($products),
			'limit'	=> $limit,
			'count'	=> count($_SESSION['seen'])
		));
	}
	
	function AlsoBoughtBlock($limit=0) {
		if(!isset($this->product['relations']) || empty($this->product['relations'])) return ;
		
		if($limit > 0) $sql_limit = 'LIMIT '.(int)$limit;
		else $sql_limit = '';
		
		$products = db()->rows("SELECT * FROM `prefix_products` WHERE `id` IN (".$this->product['relations'].") AND `deleted` = 'N' AND `show` = 'Y' $sql_limit", MYSQLI_ASSOC);
		
		return tpl('modules/'.__CLASS__.'/promoBlocks/alsoBought', array(
			'littleProductsList' => $this->CustomLittleProductsList($products))
		);
	}
	
	function addCompare($id) {
		$product = db()->query_first("SELECT * FROM `prefix_products` WHERE `id` = ".abs((int)$id)." AND `deleted` = 'N' AND `show` = 'Y'");
		if(!empty($product)) $_SESSION['compare'][$id] = true;
	}
	
	function delCompare($id) {
		if(isset($_SESSION['compare'][$id])) unset($_SESSION['compare'][$id]);
	}
	
	function cleanCompare() {
		if(isset($_SESSION['compare'])) unset($_SESSION['compare']);
	}
	
	function ajaxCompare() {
		if(isset($_POST['add'])) $this->addCompare($_POST['add']);
		
		if(isset($_POST['del'])) $this->delCompare($_POST['del']);
		
		if(isset($_POST['clean'])) $this->cleanCompare();
		
		giveJSON(array(
			'block'	=> $this->CompareBlock()
		));
	}
	
	/**
	 * Блок сравнения
	 */
	function CompareBlock() {
		if(!isset($_SESSION['compare']) || empty($_SESSION['compare']))
			return tpl('modules/'.__CLASS__.'/promoBlocks/compare', array('products' => array(), 'topics' => array()));
		
		$ids = array_keys($_SESSION['compare']);
		$ids = array_reverse($ids);
		
		$prod = db()->rows("SELECT * FROM `prefix_products` WHERE `id` IN (".implode(',', $ids).") AND `deleted` = 'N' AND `show` = 'Y'", MYSQLI_ASSOC, 'id');
		
		foreach ($prod as $k=>$product) {
			//$prod[$k]['price'] = $this->Price($product['price']);
			$prod[$k]['priceOld'] = $this->Price($product['price']);
			$prod[$k]['price'] = $this->Price($product['price'], $product['discount']);
		}
		
		$brands = db()->rows("SELECT * FROM `prefix_products_brands` WHERE `deleted` = 'N' AND `show` = 'Y'", MYSQLI_ASSOC, 'id');
		
		$products = array();
		foreach ($ids as $id) {
			$products [$prod[$id]['top']] [$id] = $prod[$id];
			$products [$prod[$id]['top']] [$id] ['link'] = $this->Link($prod[$id]['top'], $prod[$id]['id']);
			if(isset($brands[$prod[$id]['brand']])) $products [$prod[$id]['top']] [$id] ['brand'] = $brands[$prod[$id]['brand']];
			else $products [$prod[$id]['top']] [$id] ['brand'] = false;
		}
		
		$topics = db()->rows("SELECT * FROM `prefix_products_topics` WHERE `deleted` = 'N' AND `show` = 'Y' AND `id` IN (".implode(',', array_keys($products)).")", MYSQL_ASSOC, 'id');
		foreach ($topics as $id=>$topic) $topics[$id]['link'] = $this->Link($topic['id']);
		
		foreach ($products as $k=>$v) {
			$products[$k] = array_slice($v, 0, 3);
		}
		
		return tpl('modules/'.__CLASS__.'/promoBlocks/compare', array(
			'products'	=> $products,
			'topics'	=> $topics,
			'count'	=> count($_SESSION['compare'])
		));
	}
	
	/**
	 * Страница сравнения, добавление/удаление товаров в сравнении
	 */
	function Compare() {
		// Добавление/удаление товаров сравнения
		if(isset($this->path[1]) && isset($this->path[2]) && $this->path[1]['data']=='add') {$this->addCompare($this->path[2]['data']); $backMe=true;}
		if(isset($this->path[1]) && isset($this->path[2]) && $this->path[1]['data']=='del') {$this->delCompare($this->path[2]['data']); $backMe=true;}
		if(isset($this->path[1]) && $this->path[1]['data']=='clean') {$this->cleanCompare(); $backMe=true;}
		if(isset($backMe) && isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
			header('Location: '.$_SERVER['HTTP_REFERER']);
			exit();
		}
		
		if(!isset($_SESSION['compare']) || empty($_SESSION['compare'])) page404();
		
		//Подготовка данных для сравнения
		$ids = array_keys($_SESSION['compare']);
		$ids = array_reverse($ids);
		$prod = db()->rows("SELECT * FROM `prefix_products` WHERE `id` IN (".implode(',', $ids).") AND `deleted` = 'N' AND `show` = 'Y'", MYSQLI_ASSOC, 'id');
		foreach ($prod as $k=>$product) {
			//$prod[$k]['price'] = $this->Price($product['price']);
			$prod[$k]['priceOld'] = $this->Price($product['price']);
			$prod[$k]['price'] = $this->Price($product['price'], $product['discount']);
		}
		$brands = db()->rows("SELECT * FROM `prefix_products_brands` WHERE `deleted` = 'N' AND `show` = 'Y'", MYSQLI_ASSOC, 'id');
		$products = array();
		foreach ($ids as $id) {
			$products [$prod[$id]['top']] [$id] = $prod[$id];
			$products [$prod[$id]['top']] [$id] ['link'] = $this->Link($prod[$id]['top'], $prod[$id]['id']);
			$products [$prod[$id]['top']] [$id] ['types'] = unserialize($prod[$id]['types']);
			if(isset($brands[$prod[$id]['brand']])) $products [$prod[$id]['top']] [$id] ['brand'] = $brands[$prod[$id]['brand']];
			else $products [$prod[$id]['top']] [$id] ['brand'] = false;
		}
		$topics = db()->rows("SELECT * FROM `prefix_products_topics` WHERE `deleted` = 'N' AND `show` = 'Y' AND `id` IN (".implode(',', array_keys($products)).")", MYSQL_ASSOC, 'id');
		foreach ($topics as $id=>$topic) {
			$topics[$id]['link'] = $this->Link($topic['id']);
			$topics[$id]['types'] = unserialize($topics[$id]['types']);
			//debug($topics[$id]['types']);
		}
		
		//Характеристики
		foreach ($products as $top_id=>$product_group) {
			foreach ($product_group as $prod_id=>$product) {
				$types = array();
				$pTypes = $product['types'];
				if(is_array($topics[$top_id]['types']))
				foreach ($topics[$top_id]['types'] as $groupKey=>$group) {
					$types[$groupKey]['name'] = $group['name'];
					foreach ($group['types'] as $typeKey=>$type) {
						
						switch ($type['type']) {
							case 'float':
								if($pTypes[$groupKey][$typeKey] !== '') {
									$types[$groupKey]['types'][$typeKey] = array(
										'name'	=> $type['name'],
										'desc'	=> $type['desc'],
										'val'	=> $pTypes[$groupKey][$typeKey].$type['unit']
									);
								}
							break;
							
							case 'range':
								if(isset($pTypes[$groupKey][$typeKey]['from']) && isset($pTypes[$groupKey][$typeKey]['to']))
								if($pTypes[$groupKey][$typeKey]['from'] !== '' || $pTypes[$groupKey][$typeKey]['to'] !== '') {
									if($pTypes[$groupKey][$typeKey]['from'] !== '' && $pTypes[$groupKey][$typeKey]['to'] !== '') {
										if($pTypes[$groupKey][$typeKey]['from'] == $pTypes[$groupKey][$typeKey]['to']) {
											$val = $pTypes[$groupKey][$typeKey]['from'].$type['unit'];
										} else {
											$val = $pTypes[$groupKey][$typeKey]['from'].'—'.$pTypes[$groupKey][$typeKey]['to'].$type['unit'];
										}
									}
									elseif($pTypes[$groupKey][$typeKey]['from'] !== '') {
										$val = 'от '.$pTypes[$groupKey][$typeKey]['from'];
									}
									elseif($pTypes[$groupKey][$typeKey]['to'] !== '') {
										$val = 'до '.$pTypes[$groupKey][$typeKey]['to'];
									}
									$types[$groupKey]['types'][$typeKey] = array(
										'name'	=> $type['name'],
										'desc'	=> $type['desc'],
										'val'	=> $val
									);
								}
							break;
							
							case 'yn':
								if($pTypes[$groupKey][$typeKey] == 'Y') $types[$groupKey]['types'][$typeKey] = array(
									'name'	=> $type['name'],
									'desc'	=> $type['desc'],
									'val'	=> 'Есть'
								);
								if($pTypes[$groupKey][$typeKey] == 'N') $types[$groupKey]['types'][$typeKey] = array(
									'name'	=> $type['name'],
									'desc'	=> $type['desc'],
									'val'	=> 'Нет'
								);
							break;
							
							case 'select':
								if($pTypes[$groupKey][$typeKey] !== '' && $pTypes[$groupKey][$typeKey] !== 0) {
									if(isset($type['select'][ $pTypes[$groupKey][$typeKey] ])) {
										$types[$groupKey]['types'][$typeKey] = array(
											'name'	=> $type['name'],
											'desc'	=> $type['desc'],
											'val'	=> $type['select'][ $pTypes[$groupKey][$typeKey] ]
										);
									}
								}
							break;
							
							case 'text':
								if(!empty($pTypes[$groupKey][$typeKey])) {
									$types[$groupKey]['types'][$typeKey] = array(
										'name'	=> $type['name'],
										'desc'	=> $type['desc'],
										'val'	=> $pTypes[$groupKey][$typeKey]
									);
								}
							break;
						}
						
					}
					if(empty($types[$groupKey]['types'])) unset($types[$groupKey]);
				}
				
				$products [$top_id] [$prod_id] ['rtypes'] = $types;
			}
			
			//Нахождение различающихся характеристик
			if(is_array($topics[$top_id]['types']))
			foreach ($topics[$top_id]['types'] as $groupKey=>$types) {
				foreach ($types['types'] as $typeKey=>$type) {
					$stored = false;
					$topics[$top_id]['types'][$groupKey]['types'][$typeKey]['equal'] = true;
					foreach ($products[$topic['id']] as $product) {
						if($stored !== false && $stored != @$product['types'][$groupKey][$typeKey]) {
							$topics[$top_id]['types'][$groupKey]['types'][$typeKey]['equal'] = false;
							break;
						}
						@$stored = $product['types'][$groupKey][$typeKey];
					}
					$stored = false;
				}
			}
			
		}
		
		$text = tpl('modules/'.__CLASS__.'/compare', array(
			'products'	=> $products,
			'topics'	=> $topics
		));
		
		return tpl('page', array(
			'title'	=> $this->alter_pages['compare']['name'],
			'name'	=> $this->alter_pages['compare']['name'],
			'text'	=> $text
		));
	}
	
	
	/**
	 * Простой и незамысловатый поиск
	 */
	function Search() {
		$string = trim($_GET['string']);
		
		$error = '';
		if(empty($string)) $error = '<p>Пустой поисковый запрос, мы так ничего не найдем!</p>';
		if(mb_strlen($string) <= 1) $error = '<p>Очень короткий поисковый запрос, вы сами устанете искать то что нужно из всех совпадений!</p>';
		
		if(!empty($error)) return tpl('page', array('title' => $this->alter_pages['search']['name'],'name'	=> $this->alter_pages['search']['name'],'text' => $error));
		
		$strings = explode(' ', $string);
		$highlight = array();
		
		//Убираем окончания
		$stemmer = new Lingua_Stem_Ru();
		foreach ($strings as $k=>$v) {
			$trimv = trim($v);
			$strings[$k] = $stemmer->stem_word($trimv);
			$highlight[] = $trimv;
		}
		
		//Окончательно фильтруем
		$strings = array_filter($strings);
		
		$like = array();
		foreach ($strings as $v) {
			$like[] = '(t.`name` LIKE \'%'.q($v).'%\' OR p.`name` LIKE \'%'.q($v).'%\' OR b.`name` LIKE \'%'.q($v).'%\')';
		}
		
		$products = array();
		if(is_array($like) && !empty($like)) {
			$products = db()->rows("
				SELECT p.*, b.name AS `brand_name`, t.singular_name AS `product_singular_name`
				FROM `prefix_products` AS p
				LEFT JOIN `prefix_products_topics` AS t ON t.id = p.top
				LEFT JOIN `prefix_products_brands` AS b ON b.id = p.brand
				WHERE p.`deleted` = 'N' AND p.`show` = 'Y' AND (".implode(' AND ', $like).")
				ORDER BY `rate` DESC
			");
			
			if(count($products) == 0) {
				$products = db()->rows("
					SELECT p.*, b.name AS `brand_name`, t.singular_name AS `product_singular_name`
					FROM `prefix_products` AS p
					LEFT JOIN `prefix_products_topics` AS t ON t.id = p.top
					LEFT JOIN `prefix_products_brands` AS b ON b.id = p.brand
					WHERE p.`deleted` = 'N' AND p.`show` = 'Y' AND (".implode(' OR ', $like).")
					ORDER BY `rate` DESC
				");
			}
		}
		
		if(count($products) == 0) $error = '<p>Товары не найдены, попробуйте поискать с другим запросом!</p>';
		
		if(!empty($error)) return tpl('page', array('title' => $this->alter_pages['search']['name'],'name'	=> $this->alter_pages['search']['name'],'text' => $error));
		
		$text = $this->CustomProductsList($products);
		$text .= $this->paging_rendered;
		
		return tpl('page', array(
			'title'	=> $this->alter_pages['search']['name'],
			'name'	=> $this->alter_pages['search']['name'],
			'text'	=> $text
		));
	}
	
	function SearchForExample() {
		$example = db()->query_first("
			SELECT p.`name` AS `pname`, b.`name` AS `bname` FROM `prefix_products` AS p
			LEFT JOIN `prefix_products_brands` AS b ON b.`id` = p.`brand`
			WHERE
				p.`deleted` = 'N' AND
				p.`show` = 'Y'
			ORDER BY RAND()
			LIMIT 1
		");
		
		$exampleString = trim($example['pname'].' '.$example['bname']);
		
		return $exampleString;
	}
	
	
	function Paging($products) {
		$products_count = count($products);
		$products_onpage = getSet('Catalog', 'onpage', 8);
		$pages_count = ceil($products_count/$products_onpage);
		if(isset($_GET['page'])) $page_current = abs((int)$_GET['page']);
		else $page_current = 1;
		$products_from = ($page_current-1)*$products_onpage;
		
		if($products_count <= $products_onpage) {
			$rendered = '';
		} else {
			$rendered = tpl('modules/'.__CLASS__.'/paging', array(
				'pages_count'		=> $pages_count,
				'page_current'		=> $page_current,
				'products_count'	=> $products_count,
				'products_from'		=> $products_from + 1,
				'products_to'		=> $products_onpage*$page_current>$products_count?$products_count:$products_onpage*$page_current,
				'topicLink'			=> $this->Link($this->topic['id'])
			));
		}
		
		$this->paging_rendered = $rendered;
		
		return array(
			'products'	=> array_slice($products, $products_from, $products_onpage),
			'rendered'	=> $rendered
		);
	}
	
	function GetPaging() {
		if(isset($this->paging_rendered)) {
			return $this->paging_rendered;
		} else {
			return false;
		}
	}
	
	private function SelectionBlock() {
		if(empty($this->currentTopicTypes)) $this->currentTopicTypes = unserialize($this->topic['types']);
		
		if(empty($this->currentTopicTypes)) return ;
		
		//Сортировка групп
		usort($this->currentTopicTypes, function($a,$b) {
			if ($a['order'] == $b['order']) return 0;
    		return ($a['order'] < $b['order']) ? -1 : 1;
		});
		
		//Подготовка списка характеристик
		$readyTypes = array();
		foreach ($this->currentTopicTypes as $groupKey=>$group) {
			foreach ($group['types'] as $typeKey=>$type) {
				if($type['main']) {
					$type['groupKey'] = $groupKey;
					$type['typeKey'] = $typeKey;
					if(isset($_GET['select'][$groupKey][$typeKey])) {
						switch ($type['type']) {
							case 'float': case 'range':
								if(isset($_GET['select'][$groupKey][$typeKey]['from']) && isset($_GET['select'][$groupKey][$typeKey]['to'])) {
									//Не задано ОТ
									if(empty($_GET['select'][$groupKey][$typeKey]['from']) && !empty($_GET['select'][$groupKey][$typeKey]['to'])) {
										$type['val']['to'] = (float)$_GET['select'][$groupKey][$typeKey]['to'];
										$this->productsFilter['select'][$groupKey][$typeKey]['to'] = $type['val']['to'];
									}
									//Не задано ДО
									elseif(!empty($_GET['select'][$groupKey][$typeKey]['from']) && empty($_GET['select'][$groupKey][$typeKey]['to'])) {
										$type['val']['from'] = (float)$_GET['select'][$groupKey][$typeKey]['from'];
										$this->productsFilter['select'][$groupKey][$typeKey]['from'] = $type['val']['from'];
									}
									//Все задано
									elseif(!empty($_GET['select'][$groupKey][$typeKey]['from']) && !empty($_GET['select'][$groupKey][$typeKey]['to'])) {
										$type['val']['from'] = (float)$_GET['select'][$groupKey][$typeKey]['from'];
										$type['val']['to'] = (float)$_GET['select'][$groupKey][$typeKey]['to'];
										$this->productsFilter['select'][$groupKey][$typeKey]['from'] = $type['val']['from'];
										$this->productsFilter['select'][$groupKey][$typeKey]['to'] = $type['val']['to'];
									}
								}
							break;
							
							case 'yn':
								if($_GET['select'][$groupKey][$typeKey] == 'Y') {
									$type['val'] = 'Y';
									$this->productsFilter['select'][$groupKey][$typeKey] = $type['val'];
								}
							break;
								
							case 'select':
								if(in_array($_GET['select'][$groupKey][$typeKey], array_keys($type['select']))) {
									$type['val'] = $_GET['select'][$groupKey][$typeKey];
									$this->productsFilter['select'][$groupKey][$typeKey] = $type['val'];
								}
							break;
						}
					}
					$readyTypes[] = $type;
				}
			}
		}
		
		if(empty($readyTypes)) return ;
		
		//Добавочный URL
		$link = getget(array('page'=>false, 'select'=>false), 1);
		
		//Очищающий URL
		$cleanLink = getget(array('page'=>false, 'select'=>false));
		
		return tpl('modules/'.__CLASS__.'/selection', array(
			'types'	=> $readyTypes,
			'link'	=> $link,
			'cleanLink'	=> $cleanLink
		));
	}
	
	function SelectionFilter(&$products) {
		foreach ($products as $k=>$product) {
			//Бренды
			if(isset($this->productsFilter['brands']) && !in_array($product['brand'], $this->productsFilter['brands'])) {
				unset($products[$k]);
			}
			//Цена
			if(isset($this->productsFilter['price']) && ($product['price'] < $this->productsFilter['price']['form'] || $product['price'] > $this->productsFilter['price']['to'])) {
				unset($products[$k]);
			}
			//Наличие
			if($this->productsFilter['exist'] && $product['is_exist'] != 'Y') {
				unset($products[$k]);
			}
			//Характеристики
			if(!empty($this->currentTopicTypes)) $tTypes = $this->currentTopicTypes;
			else $tTypes = $this->currentTopicTypes = unserialize($this->topic['types']);
			if(isset($this->productsFilter['select'])) {
				if(empty($product['types'])) { unset($products[$k]); continue; }
				foreach($product['types'] as $groupKey=>$group) {
					if(empty($group)) { unset($products[$k]); continue; }
					foreach($group as $typeKey=>$type) {
						switch ($tTypes[$groupKey]['types'][$typeKey]['type']) {
							case 'float':
								if(!isset($this->productsFilter['select'][$groupKey][$typeKey])) continue;
								//Если хоть что-то задано, а у товара эта характеристика не указана
								if(
									(
										isset($this->productsFilter['select'][$groupKey][$typeKey]['from'])
										||
										isset($this->productsFilter['select'][$groupKey][$typeKey]['to'])
									)
									&&
									$type === ''
								) {
									unset($products[$k]);
								}
								//Если не задано от скольки
								if(!isset($this->productsFilter['select'][$groupKey][$typeKey]['from'])) {
									if($type > $this->productsFilter['select'][$groupKey][$typeKey]['to']) {
										unset($products[$k]);
									}
								}
								//Если не задано до скольки
								elseif(!isset($this->productsFilter['select'][$groupKey][$typeKey]['to'])) {
									if($type < $this->productsFilter['select'][$groupKey][$typeKey]['from']) {
										unset($products[$k]);
									}
								}
								//Если задано все
								elseif(
									$type < $this->productsFilter['select'][$groupKey][$typeKey]['from']
									||
									$type > $this->productsFilter['select'][$groupKey][$typeKey]['to']
								) {
									unset($products[$k]);
								}
							break;
							
							case 'range':
								if(isset($this->productsFilter['select'][$groupKey][$typeKey]['from']) || isset($this->productsFilter['select'][$groupKey][$typeKey]['to'])) {
									//Заданы обе границы (пользователем)
									if(
										isset($this->productsFilter['select'][$groupKey][$typeKey]['from'])
										&&
										isset($this->productsFilter['select'][$groupKey][$typeKey]['to'])
									) {
										//У товара заданы обе границы
										if($type['from'] !== '' && $type['to'] !== '') {
											if(
												$this->productsFilter['select'][$groupKey][$typeKey]['from'] > $type['to']
												||
												$this->productsFilter['select'][$groupKey][$typeKey]['to'] < $type['from']
											) unset($products[$k]);
										}
										//У товара задана левая граница
										elseif($type['from'] !== '') {
											if(
												$this->productsFilter['select'][$groupKey][$typeKey]['to'] < $type['from']
											) unset($products[$k]);
										}
										//У товара задана правая граница
										elseif($type['to'] !== '') {
											if(
												$this->productsFilter['select'][$groupKey][$typeKey]['from'] > $type['to']
											) unset($products[$k]);
										}
									}
									//Задана левая граница (пользователем)
									elseif(
										isset($this->productsFilter['select'][$groupKey][$typeKey]['from'])
									) {
										//У товара заданы обе границы
										if($type['from'] !== '' && $type['to'] !== '') {
											if(
												$this->productsFilter['select'][$groupKey][$typeKey]['from'] > $type['to']
											) unset($products[$k]);
										}
										//У товара задана левая граница
										elseif($type['from'] !== '') {
											// ok go
										}
										//У товара задана правая граница
										elseif($type['to'] !== '') {
											if(
												$this->productsFilter['select'][$groupKey][$typeKey]['from'] > $type['to']
											) unset($products[$k]);
										}
									}
									//Задана правая граница (пользователем)
									elseif(
										isset($this->productsFilter['select'][$groupKey][$typeKey]['to'])
									) {
										//У товара заданы обе границы
										if($type['from'] !== '' && $type['to'] !== '') {
											if(
												$this->productsFilter['select'][$groupKey][$typeKey]['to'] < $type['from']
											) unset($products[$k]);
										}
										//У товара задана левая граница
										elseif($type['from'] !== '') {
											if(
												$this->productsFilter['select'][$groupKey][$typeKey]['to'] < $type['from']
											) unset($products[$k]);
										}
										//У товара задана правая граница
										elseif($type['to'] !== '') {
											// ok go
										}
									}
								}
							break;
							
							case 'yn':
								if(isset($this->productsFilter['select'][$groupKey][$typeKey]))
								if($type != $this->productsFilter['select'][$groupKey][$typeKey]) {
									unset($products[$k]);
								}
							break;
							
							case 'select':
								if(!isset($this->productsFilter['select'][$groupKey][$typeKey]) || $this->productsFilter['select'][$groupKey][$typeKey] == 0) {
									continue;
								}
								if($type != $this->productsFilter['select'][$groupKey][$typeKey]) {
									unset($products[$k]);
								}
							break;
						}
					}
				}
			}
		}
	}
	
	
	function Product() {
		
		$db = db();
		
		//SEO
		$this->SEO('products', $this->product['id']);
		
		//Добавление рейтинга товару и его группе
		if(!isset($_SESSION['seen'][$this->product['id']])) {
			$db->query("
				UPDATE `prefix_products` SET `rate` = `rate` + 1 WHERE `id` = ".$this->product['id'].";
				UPDATE `prefix_products_topics` SET `rate` = `rate` + 1 WHERE `id` = ".$this->topic['id'].";
			", true);
		}
		
		//Пишем в сессию что юзер видел этот товар
		if(!isset($_SESSION['seen'])) $_SESSION['seen'] = array();
		$_SESSION['seen'][$this->product['id']] = true;
		
		//Товар
		$product = $db->query_first("
			SELECT p.*, b.`name` AS `brand_name`, t.singular_name AS `product_singular_name` FROM `prefix_products` AS p
			LEFT JOIN `prefix_products_brands` AS b ON b.`id` = p.`brand`
			LEFT JOIN `prefix_products_topics` AS t ON t.id = p.top
			WHERE p.`deleted` = 'N' AND p.`show` = 'Y' AND p.`id` = ".$this->product['id']."
		");
		
		//Топик
		$topic = $db->query_first("SELECT * FROM `prefix_products_topics` WHERE `id` = $product[top]");
		/*
		if(isset($this->seo['title'])&&!empty($this->seo['title'])) $head_title = $this->seo['title'];
		else {
			$backpath = array_reverse($this->path);
			$head_title = array();
			foreach ($backpath as $i) {
				if($i['type'] == 'topic') {
					$head_title[] = $i['data']['name'];
				}
			}
			if(!empty($head_title)) $head_title[0] = $head_title[0].(empty($additional_brand_title)?'':' '.$additional_brand_title);
			$head_title = implode(' — ', $head_title).' — '.$GLOBALS['config']['site']['title'];
		}
		*/
		//Тайтл страницы (<head> <title>)
		if(isset($this->seo['title'])&&!empty($this->seo['title'])) $head_title = $this->seo['title'];
		else {
			$backpath = array_reverse($this->path);
			$head_title = array();
			$head_title[] = $product['product_singular_name'].' '.$product['brand_name'].' '.$product['name'];
			foreach ($backpath as $i) {
				if($i['type'] == 'topic') {
					$head_title[] = $i['data']['name'];
				}
			}
			$head_title = implode(' — ', $head_title).' — '.$GLOBALS['config']['site']['title'];
		}
		
		//Цена
		//$product['price'] = $this->Price($product['price']);
		$product['priceOld'] = $this->Price($product['price']);
		$product['price'] = $this->Price($product['price'], $product['discount']);
		
		//В корзине ль
		$product['inbasket'] = false;
		if(isset($_SESSION['basket'][$product['id']]) && $_SESSION['basket'][$product['id']] != 0) {
			$product['inbasket'] = $_SESSION['basket'][$product['id']];
		}
		$product['inbaskethint'] = $this->ProductInBasketHint($product['id']);
		
		//Характеристики
		$types = array();
		$tTypes = unserialize($topic['types']);
		$pTypes = unserialize($product['types']);
		if(is_array($tTypes))
		foreach ($tTypes as $groupKey=>$group) {
			$types[$groupKey]['name'] = $group['name'];
			foreach ($group['types'] as $typeKey=>$type) {
				
				switch ($type['type']) {
					case 'float':
						if($pTypes[$groupKey][$typeKey] !== '') {
							$types[$groupKey]['types'][] = array(
								'name'	=> $type['name'],
								'desc'	=> $type['desc'],
								'val'	=> $pTypes[$groupKey][$typeKey].$type['unit']
							);
						}
					break;
					
					case 'range':
						if(isset($pTypes[$groupKey][$typeKey]['from']) && isset($pTypes[$groupKey][$typeKey]['to']))
						if($pTypes[$groupKey][$typeKey]['from'] !== '' || $pTypes[$groupKey][$typeKey]['to'] !== '') {
							if($pTypes[$groupKey][$typeKey]['from'] !== '' && $pTypes[$groupKey][$typeKey]['to'] !== '') {
								if($pTypes[$groupKey][$typeKey]['from'] == $pTypes[$groupKey][$typeKey]['to']) {
									$val = $pTypes[$groupKey][$typeKey]['from'].$type['unit'];
								} else {
									$val = $pTypes[$groupKey][$typeKey]['from'].'—'.$pTypes[$groupKey][$typeKey]['to'].$type['unit'];
								}
							}
							elseif($pTypes[$groupKey][$typeKey]['from'] !== '') {
								$val = 'от '.$pTypes[$groupKey][$typeKey]['from'];
							}
							elseif($pTypes[$groupKey][$typeKey]['to'] !== '') {
								$val = 'до '.$pTypes[$groupKey][$typeKey]['to'];
							}
							$types[$groupKey]['types'][$typeKey] = array(
								'name'	=> $type['name'],
								'desc'	=> $type['desc'],
								'val'	=> $val
							);
						}
					break;
					
					case 'yn':
						if(isset($pTypes[$groupKey][$typeKey]) && $pTypes[$groupKey][$typeKey] == 'Y') $types[$groupKey]['types'][] = array(
							'name'	=> $type['name'],
							'desc'	=> $type['desc'],
							'val'	=> 'Есть'
						);
						if(isset($pTypes[$groupKey][$typeKey]) && $pTypes[$groupKey][$typeKey] == 'N') $types[$groupKey]['types'][] = array(
							'name'	=> $type['name'],
							'desc'	=> $type['desc'],
							'val'	=> 'Нет'
						);
					break;
					
					case 'select':
						if($pTypes[$groupKey][$typeKey] !== '' && $pTypes[$groupKey][$typeKey] !== 0) {
							if(isset($type['select'][ $pTypes[$groupKey][$typeKey] ])) {
								$types[$groupKey]['types'][] = array(
									'name'	=> $type['name'],
									'desc'	=> $type['desc'],
									'val'	=> $type['select'][ $pTypes[$groupKey][$typeKey] ]
								);
							}
						}
					break;
					
					case 'text':
						if(!empty($pTypes[$groupKey][$typeKey])) {
							$types[$groupKey]['types'][] = array(
								'name'	=> $type['name'],
								'desc'	=> $type['desc'],
								'val'	=> $pTypes[$groupKey][$typeKey]
							);
						}
					break;
				}
				
			}
			if(empty($types[$groupKey]['types'])) unset($types[$groupKey]);
		}
		//debug($types);
		
		//Файлы
		$filesByGroup = files()->GetFiles('Catalog', $product['id']);
		$files = array();
		foreach ($filesByGroup as $fileByGroup) {
			foreach ($fileByGroup as $filesH) {
				$files[] = $filesH;
			}
		}
		
		//Сравнение
		$product['inCompare'] = false;
		if(isset($_SESSION['compare']) && is_array($_SESSION['compare'])) {
			if(isset($_SESSION['compare'][$product['id']])) {
				$product['inCompare'] = true;
			}
		}
		
		//Финальные приготовления
		$page_title = $product['product_singular_name'].' '.$product['brand_name'].' '.$product['name'];
		//Разбиваем группы характеристик пополам
		$groupTypeExplode = ceil(count($types)/2);
		if(!empty($types)) {
			$typesByCol['left'] = array_slice($types, 0, $groupTypeExplode);
			$typesByCol['right'] = array_slice($types, $groupTypeExplode);
		} else {
			$typesByCol = array();
		}
		
		return tpl('modules/'.__CLASS__.'/one', array(
			'title'			=> $head_title,
			'name'			=> $page_title,
			'product'		=> $product,
			'files'			=> $files,
			'types'			=> $typesByCol,
			'show_comments'	=> getSet(__CLASS__, 'comments', 'N')=='Y'?true:false
		));
	}
	
	public function ProductInBasketHint($id) {
		$id = abs((int)$id);
		$product = db()->query_first("
			SELECT p.*, b.name AS `brand_name` FROM `prefix_products` AS p
			LEFT JOIN `prefix_products_brands` AS b ON b.`id` = p.`brand`
			WHERE p.`id` = $id
		");
		
		if(empty($product)) return '';
		
		$text = $product['name'].' '.$product['brand_name'].' в корзине';
		$text .= (isset($_SESSION['basket'][$product['id']])&&$_SESSION['basket'][$product['id']]>1?' ('.$_SESSION['basket'][$product['id']].' '.plural($_SESSION['basket'][$product['id']], 'штук', 'штука', 'штуки').')':'').', ';
		$text .= 'Вы можете <a href="'.linkByModule('Basket').'">оформить заказ</a> или <a href="'.$this->Link($product['top']).'">продолжить покупки</a>. ';
		$text .= !empty($product['relations'])?'Обратите внимание, что еще покупают с этим товаром. ':'';
		
		return $text;
	}
	
	
	public function ProductsScroller() {
		$db = db();
		
		$products = $db->rows("SELECT * FROM `prefix_products` WHERE `deleted` = 'N' AND `show` = 'Y' AND `is_exist` = 'Y' ORDER BY `is_lider`, `rate` DESC LIMIT 50", MYSQLI_ASSOC, 'id');
		
		$topics = array();
		$brands = array();
		$ids = array();
		foreach ($products as $k=>$product) {
			//$products[$k]['types'] = unserialize($product['types']);
			$ids[] = $product['id'];
			$topics[$product['top']] = true;
			$brands[$product['brand']] = true;
		}
		$topics = array_keys($topics);
		$brands = array_keys($brands);
		
		if(empty($topics)) return false;
		
		img()->PrepareImages('Catalog', $ids, true);
		
		$ptopics = $db->rows("SELECT * FROM `prefix_products_topics` WHERE `deleted` = 'N' AND `show` = 'Y' AND `id` IN (".implode(',', $topics).")", MYSQLI_ASSOC);
		$topics = array();
		foreach ($ptopics as $topic) {
			$topic['link'] = $this->Link($topic['id']);
			$topics[$topic['id']] = $topic;
		}
		
		$pbrands = $db->rows("SELECT * FROM `prefix_products_brands` WHERE `deleted` = 'N' AND `show` = 'Y' AND `id` IN (".implode(',', $brands).")", MYSQLI_ASSOC);
		$brands = array();
		foreach ($pbrands as $brand) $brands[$brand['id']] = $brand;
		
		foreach ($products as $k=>$product) {
			if(!isset($topics[$product['top']])) continue;
			
			//Цена
			//$products[$k]['price'] = $this->Price($product['price']);
			$products[$k]['priceOld'] = $this->Price($product['price']);
			$products[$k]['price'] = $this->Price($product['price'], $product['discount']);
			
			//Ссылка
			$products[$k]['link'] = $this->Link($product['top'], $product['nav']?$product['nav']:$product['id']);
			
			//Топик
			$products[$k]['topic'] = $topics[$product['top']];
			
			//Бренд
			if(isset($brands[$product['brand']])) {
				$products[$k]['brand'] = $brands[$product['brand']];
			} else {
				$products[$k]['brand'] = false;
			}
		}
		
		return tpl('modules/'.__CLASS__.'/promoBlocks/lidersScroller', array(
			'products'	=> $products
		));
	}
	
	
	public function BuyButton($id, $format='one') {
		// Форматы: one, inlist
		
		$inbasket = isset($_SESSION['basket'][$id]);
		
		return tpl('modules/'.__CLASS__.'/buyButton', array(
			'id'		=> $id,
			'format'	=> $format,
			'inbasket'	=> $inbasket
		));
	}
	
	private $currency;
	function Price($price, $discount=0) {
		if($price > 0) {
			//Расчет курса (выполняется один раз!)
			if(empty($this->currency)) {
				$currencies = unserialize(getVar('currency'));
				$selectedCurrency = getSet('Catalog', 'inner_currency');
				if($currencies && isset($currencies[$selectedCurrency]) && $currencies[$selectedCurrency] > 0)	{
					$this->currency = $currencies[$selectedCurrency];
				} else $this->currency = 1;
				
				//Надбавка к конвертации
				if($this->currency > 1) {
					$this->currency = $this->currency * ( 1 + (getSet('Catalog', 'currency_margin') / 100) );
				}
			}
			//Конвертация цены товара от валюты
			$price = $price * $this->currency;
			
			//Расчет скидки на товар (акции и подобное)
			$price = $price * (1 - $discount / 100);
			
			//Округление
			$price = round($price, getSet('Catalog', 'price_round'));
		}
		
		return $price;
	}
	
	public function ajaxProductNameById() {
		$productId = abs((int)$_GET['productId']);

		$data = db()->query_first("
			SELECT p.id, p.name, b.name AS brand_name, t.singular_name AS product_singular_name
			FROM `prefix_products` AS p
			LEFT JOIN `prefix_products_topics` AS t ON t.id = p.top
			LEFT JOIN `prefix_products_brands` AS b ON b.id = p.brand
			WHERE p.id = {$productId} AND p.deleted = 'N' AND p.show = 'Y' AND t.deleted = 'N' AND t.show = 'Y'
		", MYSQLI_ASSOC);

		return json_encode(array(
			'productName'	=> $data['product_singular_name'].' '.$data['brand_name'].' '.$data['name']
		));
	}
	
}

?>