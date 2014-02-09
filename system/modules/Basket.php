<?php
class Basket {
	
	private $holder;
	
	private $alter_pages = array(
		'add'	=> array('method'=>'StaticAdd', 'name'=>'Добавить в корзину'),
		'del'	=> array('method'=>'StaticDel', 'name'=>'Убрать из корзины'),
		'edit'	=> array('method'=>'StaticEdit', 'name'=>'Редактировать количество'),
		'thanks'	=> array('method'=>'ThanksPage', 'name'=>'Спасибо за заказ!'),
		'fastOrder'	=> array('method'=>'CloudyNoon_FastOrder', 'name'=>'Быстрый заказ')
	);
	
	function __construct() {
		if(!session_id()) session_start();
		if(!isset($_SESSION['basket'])) $_SESSION['basket'] = array();
	}
	
	public function Output() {
		$this->holder = end($GLOBALS['path']);
		
		$alter = $this->checkAlterPage();
		if(!$alter) {
			//Главная
			if(empty($_SESSION['basket']))
				$text = $this->EmptyBasketPage();
			else
				$text = $this->BasketPage();
		} else {
			//Альтернативные
			$text = $alter;
		}
		
		return tpl('page', array(
			'name'	=> $this->holder['name'],
			'title'	=> $this->holder['name'].' — '.$GLOBALS['config']['site']['title'],
			'text'	=> $text
		));
	}
	
	private function checkAlterPage() {
		$request =
			strpos($_SERVER['REQUEST_URI'], '?')!==false
			?
				substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'))
			:
				$_SERVER['REQUEST_URI'];
		
		$mlink = trim(str_replace(linkById($this->holder['id']), '', $request));
		$mpath = array_filter(explode('/', $mlink));
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
		}
		
		if(!empty($mlink) && empty($this->path)) page404();

		if(empty($this->path)) return false;
		$check_alter = current($this->path);
		if($check_alter['type'] == 'alter_page') {
			return $this->{$check_alter['method']}();
		} else return false;
	}
	
	/**
	 * Основной метод добавления в корзину
	 *
	 * @param integer $id
	 * @param integer $count
	 */
	private function AddToBasket($id, $count=1) {
		$id = abs((int)$id);
		$p = db()->rows("SELECT * FROM `prefix_products` WHERE `deleted` = 'N' AND `show` = 'Y' AND `id` = $id");
		if(empty($p)) return false;
		if(isset($_SESSION['basket'][$id])) $_SESSION['basket'][$id]+=$count;
		else $_SESSION['basket'][$id] = $count;
		return true;
	}
	
	/**
	 * Добавление в корзину по ссылке (не аякс)
	 */
	private function StaticAdd() {
		if(isset($this->path[1]['data']) && is_numeric($this->path[1]['data']))
			$result = $this->AddToBasket((int)$this->path[1]['data']);
		if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
			header('Location: '.$_SERVER['HTTP_REFERER']);
			exit();
		} else return $result;
	}
	
	/**
	 * Добавление товара в корзину на аяксе
	 */
	public function ajaxAdd() {
		$result = false;
		if(isset($_POST['id'])) $result = $this->AddToBasket($_POST['id']);
		
		//$hint = giveObject('Catalog')->ProductInBasketHint($_POST['id']);
		
		giveJSON(array(
			'block'		=> $this->Block(),
			'buybutton'	=> giveObject('Catalog')->BuyButton($_POST['id'], $_POST['format']),
			'result'	=> $result
		));
	}
	
	/**
	 * Основной метод удаления из корзины
	 *
	 * @param integer $id
	 */
	private function DelFromBasket($id) {
		$id = abs((int)$id);
		if(isset($_SESSION['basket'][$id])) unset($_SESSION['basket'][$id]);
		else return false;
		return true;
	}
	
	/**
	 * Удаление из корзины по ссылке (не аякс)
	 */
	private function StaticDel() {
		if(isset($this->path[1]['data']) && is_numeric($this->path[1]['data']))
			$result = $this->DelFromBasket((int)$this->path[1]['data']);
		if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
			header('Location: '.$_SERVER['HTTP_REFERER']);
			exit();
		} else return $result;
	}
	
	/**
	 * Удаление товара из корзины на аяксе
	 */
	public function ajaxDel() {
		$result = false;
		if(isset($_POST['id'])) $result = $this->DelFromBasket($_POST['id']);
		
		$empty = empty($_SESSION['basket']);
		
		$totals = $this->CurrentTotals();
		$totals['count'] = $totals['count'].' '.plural($totals['count'], 'товаров', 'товар', 'товара');
		$totals['summ'] = number_format($totals['summ'], 0, '', ' ').' '.plural($totals['summ'], 'рублей', 'рубль', 'рубля');
		
		giveJSON(array(
			'block'		=> $this->Block(),
			'result'	=> $result,
			'empty'		=> $empty,
			'totals'	=> $totals
		));
	}
	
	
	private function EditCountBasket($id, $count) {
		$id = abs((int)$id);
		$count = abs((int)$count);
		
		if(isset($_SESSION['basket'][$id]) && $count != 0) {
			$_SESSION['basket'][$id] = $count;
			return true;
		}
		else return false;
	}
	
	private function StaticEdit() {
		if(!isset($_POST) || empty($_POST) || empty($_POST['count']) || !is_array($_POST['count'])) page404();
		
		$result = true;
		foreach ($_POST['count'] as $id=>$count) {
			$result = $result && $this->EditCountBasket($id, $count);
		}
		
		if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
			header('Location: '.$_SERVER['HTTP_REFERER']);
			exit();
		} else return $result;
	}
	
	public function ajaxEdit() {
		$result = false;
		if(isset($_POST['id']) && isset($_POST['count'])) $result = $this->EditCountBasket($_POST['id'], $_POST['count']);
		
		$catalog = giveObject('Catalog');
		$product = db()->query_first("SELECT * FROM `prefix_products` WHERE `deleted` = 'N' AND `show` = 'Y' AND `id` = ".(int)$_POST['id']);
		if(empty($product)) $result = false;
		
		$price = $catalog->Price($product['price'], $product['discount']);
		
		$tprice = $price * $_SESSION['basket'][(int)$_POST['id']];
		
		$totals = $this->CurrentTotals();
		$totals['count'] = $totals['count'].' '.plural($totals['count'], 'товаров', 'товар', 'товара');
		$totals['summ'] = number_format($totals['summ'], 0, '', ' ').' '.plural($totals['summ'], 'рублей', 'рубль', 'рубля');
		
		giveJSON(array(
			'block'		=> $this->Block(),
			'result'	=> $result,
			'price'		=> number_format($price, 0, '', ' '),
			'tprice'	=> number_format($tprice, 0, '', ' '),
			'totals'	=> $totals
		));
	}
	
	
	/**
	 * Страница корзины
	 */
	private function BasketPage() {
		if(empty($_SESSION['basket'])) return $this->EmptyBasketPage();
		
		//Отправка заказа
		//...в конце этого метода
		
		$catalog = giveObject('Catalog');
		$db = db();
		//debug($_SESSION['basket']);
		$ids = array_keys($_SESSION['basket']);
		//debug($ids);
		img()->PrepareImages('Catalog', $ids, true);
		
		$prod = $db->rows("SELECT * FROM `prefix_products` WHERE `deleted` = 'N' AND `show` = 'Y' AND `id` IN (".implode(',', $ids).")", MYSQLI_ASSOC, 'id');
		//debug($prod);
		
		$topics_ids = $brands_ids = array();
		foreach ($prod as $p) {
			$topics_ids[$p['top']] = true;
			$brands_ids[$p['brand']] = true;
		}
		$topics_ids = array_keys($topics_ids);
		$brands_ids = array_keys($brands_ids);
		
		$topics = $db->rows("SELECT * FROM `prefix_products_topics` WHERE `deleted` = 'N' AND `show` = 'Y' AND `id` IN (".implode(',', $topics_ids).")", MYSQLI_ASSOC, 'id');
		$brands = $db->rows("SELECT * FROM `prefix_products_brands` WHERE `deleted` = 'N' AND `show` = 'Y' AND `id` IN (".implode(',', $brands_ids).")", MYSQLI_ASSOC, 'id');
		
		$products = array();
		foreach ($ids as $id) {
			$products[$id] = $prod[$id];
			//Цена
			$products[$id]['priceOld'] = $catalog->Price($prod[$id]['price']);
			$products[$id]['price'] = $catalog->Price($prod[$id]['price'], $prod[$id]['discount']);
			
			//В корзине
			$products[$id]['inbasket'] = $_SESSION['basket'][$id];
			
			//Стоимость
			$products[$id]['tprice'] = $products[$id]['price'] * $products[$id]['inbasket'];
			
			//Ссылка
			$products[$id]['link'] = $catalog->Link($prod[$id]['top'], $prod[$id]['nav']?$prod[$id]['nav']:$prod[$id]['id']);
			
			//Топик
			$products[$id]['topic'] = $topics[$prod[$id]['top']];
			$products[$id]['topic']['link'] = $catalog->Link($topics[$prod[$id]['top']]['id']);
			
			//Бренд
			if(isset($brands[$prod[$id]['brand']])) $products[$id]['brand'] = $brands[$prod[$id]['brand']];
			else $products[$id]['brand'] = false;
			
			
		}
		
		//debug($products);
		
		//Текущее итого
		$totals = $this->CurrentTotals();
		
		//Методы оплаты
		$paymethods = $db->rows("SELECT * FROM `prefix_shop_paymethods` WHERE `show` = 'Y' ORDER BY `order`", MYSQLI_ASSOC, 'id');
		
		
		//Отправка заказа
		$errors = false;
		if(!empty($_POST)) {
			if(!isset($_POST['phone']) || empty($_POST['phone'])) $errors[] = 'Введите ваш контактный телефон';
			if(empty($_SESSION['basket'])) $errors[] = 'Странно, но, кажется, вы ничего не заказали';
			if(
				!empty($_POST['mail'])
				&&
				!preg_match('/\A(?:[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)\Z/i', $_POST['mail'])
			) $errors[] = 'Постарайтесь верно ввести электронную почту, либо попробуте другой адрес';
			
			//Если все хорошо, собираем и отправляем
			if(!$errors) {
				//Статус для нового заказа
				$newstatus = $db->query_first("SELECT * FROM `prefix_shop_statuses` WHERE `type` = 'New'");
				
				//Собираем поля
				$order = array();
				$order['name']		= $_POST['name'];
				$order['phone']		= $_POST['phone'];
				$order['mail']		= $_POST['mail'];
				$order['address']	= $_POST['address'];
				if(isset($_POST['payment']) && in_array($_POST['payment'], array_keys($paymethods))) {
					$order['payment']	= (int)$_POST['payment'];
				} else $order['payment'] = 0;
				
				//Записываем заказ
				$db->query("INSERT INTO `prefix_shop_orders` (`name`,`mail`,`phone`,`address`,`date`,`paymethod`,`status`) VALUE (
					'".q($order['name'])."',
					'".q($order['mail'])."',
					'".q($order['phone'])."',
					'".q($order['address'])."',
					NOW(),
					".$order['payment'].",
					".$newstatus['id']."
				)");
				
				$order['id'] = $db->last_insert_id();
				
				$insertOrderItems = "DELETE FROM `prefix_shop_orders_items` WHERE `order` = ".$order['id'].";";
				foreach ($products as $product) {
					$insertOrderItems .= "
						INSERT INTO `prefix_shop_orders_items` (
							`product`,
							`order`,
							`name`,
							`link`,
							`top`,
							`brand`,
							`price`,
							`count`,
							`created`,
							`modified`
						) VALUE (
							$product[id],
							$order[id],
							'".q($product['name'].($product['brand']?' '.$product['brand']['name']:''))."',
							'".q($product['link'])."',
							$product[top],
							".($product['brand']?$product['brand']['id']:0).",
							'$product[price]',
							'$product[inbasket]',
							'$product[created]',
							'$product[modified]'
						);";
				}
				
				$db->query($insertOrderItems, true);
				
				
				//Отправляем на почту уведомление о заказе
				$toMail = getSet('Shop', 'notify_mail', $GLOBALS['config']['site']['admin_mail']);
				if(!empty($toMail)) {
					$body  = "№ заказа: ".$order['id']."\r\n";
					$body .= "Имя: ".$order['name']."\r\n";
					$body .= "Почта: ".$order['mail']."\r\n";
					$body .= "Телефон: ".$order['phone']."\r\n";
					$body .= "Адрес: ".$order['address']."\r\n";
					//$body .= "Комментарий: ".$this->order['user']['comment']."\r\n";
					$body .= "IP: ".$_SERVER['REMOTE_ADDR']."\r\n";
					$body .= "Карточка заказа: http://".$_SERVER['SERVER_NAME']."/admin/?module=Shop&method=Info#open".$order['id']." \r\n";
					$body .= "Список товаров заказа: http://".$_SERVER['SERVER_NAME']."/admin/?module=Shop&method=Info&top=".$order['id']." \r\n";
					$body .= "\r\nЗаказ:\r\n";
					foreach ($products as $product) {
						$body .=
							$product['name'].
							($product['brand']?' '.$product['brand']['name']:'').' '.
							'(http://'.$_SERVER['SERVER_NAME'].$product['link'].') '.
							number_format($product['price'], 0, '', ' ').' руб. × '.$product['inbasket'].'шт. = '.
							number_format($product['price']*$product['inbasket'], 0, '', ' ')." руб.\r\n";
					}
					$body .= "\r\n";
					$body .= 'К заказу '.$totals['count'].' '.plural($totals['count'], 'товаров', 'товар', 'товара').' на сумму '.number_format($totals['summ'], 0, '', ' ').' '.plural($totals['summ'], 'рублей', 'рубль', 'рубля');
					$mail = new ZFmail($toMail, 'noreply@'.$_SERVER['SERVER_NAME'], 'Заказ с сайта '.$_SERVER['SERVER_NAME'], $body);
					$mail->send();
				}
				
				$_SESSION['basket'] = array();
				$_SESSION['orderComplete'] = true;
				
				header('Location: '.linkByModule('Basket').'/thanks');
				
			}
		}
		
		
		return tpl('modules/'.__CLASS__.'/list', array(
			'products'		=> $products,
			'totals'		=> $totals,
			'paymethods'	=> $paymethods,
			'errors'		=> $errors
		));
	}
	
	private function EmptyBasketPage() {
		return tpl('modules/'.__CLASS__.'/empty');
	}
	
	private function ThanksPage() {
		if(isset($_SESSION['orderComplete']) && empty($_SESSION['basket'])) {
			return tpl('modules/'.__CLASS__.'/thanks');
		} else page404();
	}

	private function CloudyNoon_FastOrder() {
		$order['name']		= $_POST['name'];
		$order['phone']		= $_POST['phone'];
		$order['comment']	= $_POST['order'];
		$order['productId']	= abs((int)$_POST['productId']);

		$errors = array();
		if(
			empty($order['phone']) || 
			(
				empty($order['comment']) &&
				$order['productId'] == 0
			)
		) {
			$errors[] = 'Ошибка оформления, не заполнен телефон или данные о заказе';
		}

		if($order['productId'] != 0){
			$product = db()->query_first("
				SELECT p.*, b.name AS brand_name, t.singular_name AS `product_singular_name`
				FROM `prefix_products` AS p
				LEFT JOIN `prefix_products_topics` AS t ON t.id = p.top
				LEFT JOIN `prefix_products_brands` AS b ON b.id = p.brand
				WHERE p.`id` = {$order['productId']} AND p.`deleted` = 'N' AND p.`show` = 'Y'
			");
			if($product) {
				$product['link'] = giveObject('Catalog')->Link($product['top'], $product['id']);
			}

			if(empty($order['comment']) && !$product) {
				$errors[] = 'Состав заказа не определен';
			}
		}
		
		//Можно оформлять
		if(empty($errors)) {
			$db = db();
			//Статус для нового заказа
			$newstatus = $db->query_first("SELECT `id` FROM `prefix_shop_statuses` WHERE `type` = 'New'");

			//Записываем заказ
			$db->query("INSERT INTO `prefix_shop_orders` (`name`,`phone`,`date`,`paymethod`,`status`,`comment`) VALUE (
				'".q($order['name'])."',
				'".q($order['phone'])."',
				NOW(),
				0,
				".$newstatus['id'].",
				'".q($order['comment'])."'
			)");

			$order['id'] = $db->last_insert_id();

			if($product) {
				$db->query("
					INSERT INTO `prefix_shop_orders_items` (
						`product`,
						`order`,
						`name`,
						`link`,
						`top`,
						`brand`,
						`price`,
						`count`,
						`created`,
						`modified`
					) VALUE (
						$product[id],
						$order[id],
						'".q($product['singular_name'].' '.$product['brand_name'].' '.$product['name'])."',
						'".q($product['link'])."',
						$product[top],
						".($product['brand']?$product['brand']['id']:0).",
						'$product[price]',
						1,
						'$product[created]',
						'$product[modified]'
					)
				");
			}

			//Отправляем на почту уведомление о заказе
			$toMail = getSet('Shop', 'notify_mail', $GLOBALS['config']['site']['admin_mail']);
			if(!empty($toMail)) {
				$body  = "№ заказа: ".$order['id']."\r\n";
				$body .= "Имя: ".$order['name']."\r\n";
				$body .= "Телефон: ".$order['phone']."\r\n";
				$body .= "Комментарий: ".$order['comment']."\r\n";
				$body .= "IP: ".$_SERVER['REMOTE_ADDR']."\r\n";
				$body .= "Карточка заказа: http://".$_SERVER['SERVER_NAME']."/admin/?module=Shop&method=Info#open".$order['id']." \r\n";
				$body .= "Список товаров заказа: http://".$_SERVER['SERVER_NAME']."/admin/?module=Shop&method=Info&top=".$order['id']." \r\n";
				$body .= "\r\nЗаказ:\r\n";
				$body .=
						$product['singular_name'].' '.$product['brand_name'].' '.$product['name'].
						'(http://'.$_SERVER['SERVER_NAME'].$product['link'].') '.
						number_format($product['price'], 0, '', ' ')." руб.\r\n";
				$body .= "\r\n";
				$body .= 'К заказу '.$totals['count'].' '.plural($totals['count'], 'товаров', 'товар', 'товара').' на сумму '.number_format($totals['summ'], 0, '', ' ').' '.plural($totals['summ'], 'рублей', 'рубль', 'рубля');
				$mail = new ZFmail($toMail, 'noreply@'.$_SERVER['SERVER_NAME'], 'Заказ с сайта '.$_SERVER['SERVER_NAME'], $body);
				$mail->send();
			}

			$_SESSION['orderComplete'] = true;

			header('Location: '.linkByModule('Basket').'/thanks');
		} else {
			return '
			<p>При оформлении заказа произошли ошибки</p>
			<ul>
				<li>'.implode('</li><li>', $errors).'</li>
			</ul>
			';
		}
	}
	
	/*
	public function IntegrationMenu() {
		$menu[] = array(
			'name'		=> 'Распечатать список из заказа',
			'link'		=> '/11',
			'active'	=> false
		);
		$menu[] = array(
			'name'		=> 'Сохранить заказ на этом компьютере',
			'link'		=> '/22',
			'active'	=> false
		);
		$menu[] = array(
			'name'		=> 'Отменить заказ',
			'link'		=> '/33',
			'active'	=> false
		);
		
		return $menu;
	}
	*/
	
	private function CurrentTotals() {
		$count = $summ = 0;
		if(!empty($_SESSION['basket'])) {
			$catalog = giveObject('Catalog');
			$ids = array_keys($_SESSION['basket']);
			$products = db()->rows("SELECT `id`,`price`,`discount` FROM `prefix_products` WHERE `deleted` = 'N' AND `show` = 'Y' AND `id` IN (".implode(',', $ids).")");
			foreach ($products as $k=>$product) {
				$summ += $catalog->Price($product['price'], $product['discount']) * $_SESSION['basket'][$product['id']];
				$count += $_SESSION['basket'][$product['id']];
			}
		}
		return array(
			'count'	=> $count,
			'summ'	=> $summ
		);
	}
	
	public function Block() {
		$totals = $this->CurrentTotals();
		return tpl('modules/'.__CLASS__.'/block', array(
			'count'	=> $totals['count'],
			'summ'	=> $totals['summ']
		));
	}
	
}