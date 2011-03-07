<?php
class Catalog extends AdminModule {
	
	const name = 'Каталог товаров';
	
	const order = 4;
	
	public $submenu = array(
		'Info'		=> 'Каталог',
		'Brands'	=> 'Бренды',
		'MakeYML'	=> 'Обновить YML'
	);
	
	function Info() {
		//Таблица товаров
		if(isset($_GET['top'])) {
			$i = $this->db->rows("SELECT * FROM `prefix_products_topics` WHERE `id` = ".(int)$_GET['top']);
			
			$this->title = '<a href="'.$this->GetLink().'">Каталог</a> → '.$i[0]['name'];
			
			$this->content = $this->DataTableAdvanced('products',array(
				//Имена системных полей
				'nouns'	=> array(
					'id'		=> 'id',		// INT
					'name'		=> 'name',		// VARCHAR
					'order'		=> 'order',		// INT
					'deleted'	=> 'deleted',	// ENUM(Y,N)
					'created'	=> 'created',	// DATETIME
					'modified'	=> 'modified',	// DATETIME
					'text'		=> 'text'		// TEXT
				),
				//Отображение контролов
				'controls' => array(
					'add',
					'edit',
					'del'
				),
				//Табы (методы этого класса)
				'tabs'	=> array(
					'Images'		=> 'Изображения',
					'_Seo'			=> 'SEO'
				)
			),
			array(
				'id' 		=> array('name' => '№', 'class' => 'min'),
				'name'		=> array('name' => 'Наименование', 'length'=>'1-128'),
				'nav'		=> array('name' => 'Опциональная URI ссылка', 'length'=>'0-100', 'hide_from_table'=>true),
				'brand'		=> array(
					'name'		=> 'Бренд',
					'select'	=> array(
						//Обязательные
						'table'		=> 'products_brands',
						'name'		=> 'name',
						//Необязательные
						'id'		=> 'id',
						'order'		=> 'order',
						'allow_null'=> true,
						'deleted'	=> 'deleted'
					)
				),
				'price'		=> array('name' => 'Цена', 'length'=>'1-16', 'regex'=>'/^[0-9]*\.?[0-9]*$/i', 'regex_error'=>'Цена может быть только числовой и положительной', 'default'=>0),
				'anons'		=> array('name' => 'Анонс товара', 'hide_from_table'=>true),
				'show'		=> array('name' => 'Показывать', 'class'=>'min'),
				//'is_action'	=> array('name' => 'Акция', 'class'=>'min'),
				'is_featured'	=> array('name' => 'Рекомендуемый', 'class'=>'min'),
				'is_exist'	=> array('name' => 'В наличии', 'class'=>'min'),
				'top'		=> array(
					'name'		=> 'Раздел',
					//'class'		=> 'min',
					'default'	=> (int)$_GET['top'],
					'hide_from_table'	=> true,
					'select'	=> array(
						//Обязательные
						'table'		=> 'products_topics',
						'name'		=> 'name',
						//Необязательные
						'id'		=> 'id',
						'order'		=> 'order',
						//'allow_null'=> true,
						'top'		=> 'top',
						'deleted'	=> 'deleted'
					)
				),
				'content_top'		=> array(
					'name'		=> 'Относится к разделам (для примера, не рабочее поле)',
					//'class'		=> 'min',
					//'default'	=> (int)$_GET['top'],
					'multiselect'	=> array(
						//Обязательные
						'table'		=> 'content',
						'name'		=> 'name',
						//Необязательные
						'id'		=> 'id',
						'order'		=> 'order',
						//'allow_null'=> true,
						'top'		=> 'top',
						'deleted'	=> 'deleted'
					)
				),
				'rate' 		=> array('name' => 'Рейтинг (количество просмотров)', 'hide_from_table' => true),
				'relations'	=> array('name' => 'С этим товаром также покупают', 'length'=>'0-250', 'regex'=>'/^([\d]+,?)*$/i', 'regex_error'=>'Зависимые товары должны быть перечислены по их номерам (id товара) через запятую!', 'hide_from_table' => true),
				'discount' 	=> array('name' => 'Скидка, %', 'hide_from_table' => true)
			),'`top` = '.(int)$_GET['top']);
		}
		//Дерево разделов
		else {
			
			//Менюшечка
			/*
			$this->hint['text'] = '
				<ul>
					<li><a href="'.$this->GetLink('MakeYML').'">Обновить YML</a></li>
				</ul>
			';
			*/
			
			$this->title = 'Каталог';
			$this->content = $this->DataTree('products_topics',array(
					//Имена системных полей
					'nouns'	=> array(
						'id'		=> 'id',		// INT
						'name'		=> 'name',		// VARCHAR
						'order'		=> 'order',		// INT
						'deleted'	=> 'deleted',	// ENUM(Y,N)
						'created'	=> 'created',	// DATETIME
						'modified'	=> 'modified',	// DATETIME
						'text'		=> 'text',		// TEXT
						'top'		=> 'top'
					),
					//Отображение контролов
					'controls' => array(
						'add_root',
						'add_sub',
						'edit',
						'list' => $this->GetLink().'&top={id}',
						'del'
					),
					//Зависимая таблица (напрмер товары или новости по рубрикам)
					'inner'	=> array(
						'table'		=> 'products',	//Имя таблицы
						'top_key'	=> 'top',		//Ключ соответствия категории товарам
						'deleted'	=> 'deleted'	//Поле «удалено»
					),
					//Табы (методы этого класса)
					'tabs'	=> array(
						'_Seo'			=> 'SEO'
					)
				),
				array(
					'id' 		=> array('name' => '№', 'class' => 'min'),
					'name'		=> array('name' => 'Наименование', 'length'=>'1-128', 'link'=>$this->GetLink().'&top={id}'),
					'nav'		=> array('name' => 'URI ссылка', 'length'=>'0-32', 'regex'=>'/^([a-z0-9-_]+)?$/i', 'regex_error' => 'URI ссылка может быть только из цифр, латинских букв и дефиса', 'if_empty_make_uri' => 'name'),
					'order'		=> array('name' => 'Порядок', 'class'=>'min'),
					'content_top'		=> array(
						'name'		=> 'Относится к разделам (для примера, не рабочее поле)',
						//'class'		=> 'min',
						//'default'	=> (int)$_GET['top'],
						'multiselect'	=> array(
							//Обязательные
							'table'		=> 'content',
							'name'		=> 'name',
							//Необязательные
							'id'		=> 'id',
							'order'		=> 'order',
							//'allow_null'=> true,
							'top'		=> 'top',
							'deleted'	=> 'deleted',
							'size'		=> 5
						)
					),
					'rate' 		=> array('name' => 'Рейтинг (количество просмотров)')
				)
			);
		}
	}
	
	function Brands() {
			$this->title = 'Бренды';
			$this->content = $this->DataTableAdvanced('products_brands',array(
				//Имена системных полей
				'nouns'	=> array(
					'id'		=> 'id',		// INT
					'name'		=> 'name',		// VARCHAR
					'order'		=> 'order',		// INT
					'deleted'	=> 'deleted',	// ENUM(Y,N)
					'created'	=> 'created',	// DATETIME
					'modified'	=> 'modified',	// DATETIME
					'text'		=> 'text',		// TEXT
					//'image'		=> true
				),
				//Отображение контролов
				'controls' => array(
					'add',
					'edit',
					'del'
				)
			),
			array(
				'id' 		=> array('name' => '№', 'class' => 'min'),
				'name'		=> array('name' => 'Имя бренда', 'length'=>'1-128'),
				'nav'		=> array('name' => 'Адрес', 'length'=>'0-32', 'regex'=>'/^([a-z0-9-_]+)?$/i', 'regex_error' => 'URI ссылка может быть только из цифр, латинских букв и дефиса', 'if_empty_make_uri' => 'name'),
				'show'		=> array('name' => 'Показывать', 'class'=>'min', 'transform'=>'YesNo'),
				'order'		=> array('name' => 'Порядок', 'class'=>'min')
			)
		);
	}

	function Images() {
		$id = (int)(isset($_REQUEST['id'])?$_REQUEST['id']:0);
		
		if($id == 0) {
			echo 'Сначала создайте запись';
			exit();
		}
		
		$images = new Images();
		
		//Добавление картинки
		if(!empty($_FILES)) {
			$images->AddImage($_FILES['image']['tmp_name'], __CLASS__, $id, $_FILES['image']['name']);
		}
		
		//Задание картинки по-умолчанию
		if(isset($_GET['star'])) {
			$images->StarImage($_GET['star']);
		}
		
		//Удаление
		if(isset($_GET['del'])) {
			$images->DelImage($_GET['del']);
		}
		
		echo tpl('modules/'.__CLASS__.'/'.__FUNCTION__, array(
			'images'	=> $images->GetImages(__CLASS__, $id),
			'link'		=> $this->GetLink(),
			'module'	=> __CLASS__,
			'module_id'	=> $id
		));
		exit();
	}
	
	
	function MakeYML() {
		
		$yml_file = '/yandex.xml';
		$topics_table = 'products_topics';
		//$brands_table = 'brands';
		$products_table = 'products';
		
		$img = new Images();
		
		$yml = '<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="'.date('Y-m-d H:i').'">
<shop>
	<name>'.$_SERVER['SERVER_NAME'].'</name>
	<company>'.$GLOBALS['config']['site']['title'].'</company>
	<url>http://'.$_SERVER['SERVER_NAME'].'/</url>
	
	<currencies>
		<currency id="RUR" rate="1"/>
	</currencies>
	
	<categories>';
		
		$db = db();
		
		//admGetSet('Catalog', 'inner_currency');
		
		//Категории
		$topics = array();
		$db->query("SELECT * FROM `prefix_$topics_table` WHERE `deleted` = 'N' ORDER BY `top`,`order`");
		while($t = $db->fetch()) {
			if($t['top'] == 0) {
				$yml .= '
		<category id="'.$t['id'].'">'.$t['name'].'</category>';
			} else {
				$yml .= '
		<category id="'.$t['id'].'" parentId="'.$t['top'].'">'.$t['name'].'</category>';
			}
			$topics[$t['id']] = $t;
		}
		
		$yml .= '
	</categories>
	<offers>';
		
		//Бренды
		$brands_table_exist = false;
		$brands = array();
		if(isset($brands_table) && !empty($brands_table)) {
			if($db->query("SELECT * FROM `prefix_$brands_table`")) {
				while ($b = $db->fetch()) {
					$brands[$b['id']] = $b;
				}
				$brands_table_exist = true;
			}
		}
		
		//Товары
		$db->query("SELECT * FROM `prefix_$products_table` WHERE `deleted` = 'N' AND `show` = 'Y' ORDER BY `top`,`order`");
		while($i = $db->fetch()) {
			
			//Находим бренд
			if($brands_table_exist) {
				$brand = $brands[$i['brand']]['name'];
			} else {
				if(isset($i['brand_name'])) {
					$brand = $i['brand_name'];
				} elseif(isset($i['brand'])) {
					$brand = $i['brand'];
				} else {
					$brand = '';
				}
			}
			
			//Картинка
			$image = $img->GetMainImage(__CLASS__,$i['id']);
			if(!isset($image['src'])) {
				$picture = '';
			} else {
				$picture = 'http://'.$_SERVER['SERVER_NAME'].$image['src'];
			}
			
			$yml .= '
		<offer id="'.$i['id'].'" type="vendor.model" available="true">
			
  			<url>http://'.$_SERVER['SERVER_NAME'].admLinkByModule(__CLASS__).admLinkById($i['top'],$topics_table).'/'.(empty($i['nav'])?$i['id']:$i['nav']).'</url>
  			<price>'.$this->Price($i['price'], $i['discount']).'</price>
  			<currencyId>RUR</currencyId>
  			<categoryId>'.$i['top'].'</categoryId>
			
  			<picture>'.$picture.'</picture>
  			<typePrefix>'.htmlspecialchars($topics[$i['top']]['name']).'</typePrefix>
  			<vendor>'.htmlspecialchars($brand).'</vendor>
			
  			<model>'.htmlspecialchars($i['name']).'</model>
  			<description>'.htmlspecialchars(strip_tags($i['text'])).'</description>
		</offer>
			';
		}
		
		$yml .= '
	</offers>
</shop>
</yml_catalog>';
		
		
		if(is_file(DIR.$yml_file)) {
			if(is_writable(DIR.$yml_file)) {
				if(file_put_contents(DIR.$yml_file, $yml)) {
					$this->content = 'Файл '.$yml_file.' успешно обновлен.';
				} else {
					$this->content = 'Неведомая ошибка записи';
				}
			} else {
				$this->content = 'Невозможно записать файл '.$yml_file.', нет прав!';
			}
		} else {
			if(is_writable(DIR)) {
				if(file_put_contents(DIR.$yml_file, $yml)) {
					$this->content = 'Файл '.$yml_file.' успешно создан.';
				} else {
					$this->content = 'Неведомая ошибка записи';
				}
			} else {
				$this->content = 'Невозможно создать файл '.$yml_file.', нет прав! Попробуйте создать его самостоятельно и назначьте права 0777.';
			}
		}
		
	}
	
	
	
	private $currency;
	function Price($price, $discount=0) {
		if($price > 0) {
			//Расчет курса (выполняется один раз!)
			if(empty($this->currency)) {
				$currencies = unserialize(getVar('currency'));
				$selectedCurrency = admGetSet('Catalog', 'inner_currency');
				if($currencies && isset($currencies[$selectedCurrency]) && $currencies[$selectedCurrency] > 0)	{
					$this->currency = $currencies[$selectedCurrency];
				} else $this->currency = 1;
				
				//Надбавка к конвертации
				if($this->currency > 1) {
					$this->currency = $this->currency * ( 1 + (admGetSet('Catalog', 'currency_margin') / 100) );
				}
			}
			//Конвертация цены товара от валюты
			$price = $price * $this->currency;
			
			//Расчет скидки на товар (акции и подобное)
			$price = $price * (1 - $discount / 100);
			
			//Округление
			$price = round($price, admGetSet('Catalog', 'price_round'));
		}
		
		return $price;
	}
	
}

?>