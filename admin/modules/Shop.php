<?php

class Shop extends AdminModule {
	
	const name = 'Магазин';
	
	const order = 5;
	
	public $submenu = array(
		'Info'			=> 'Список заказов',
		'PayMethods'	=> 'Методы оплаты',
		'Statuses'		=> 'Статусы заказов'
	);
	
	function Info() {
		
		//Топ
		if(!isset($_GET['top'])) {
			$this->title = 'Список заказов';
			//Так проще задать сортировку
			if(!isset($_GET['orderd'])) $_GET['orderd'] = 'DESC';
			$this->content = $this->DataTable('shop_orders',array(
				//Имена системных полей
				'nouns'	=> array(
					'id'		=> 'id',		// INT
					'name'		=> 'name',		// VARCHAR
					'deleted'	=> 'deleted',	// ENUM(Y,N)
					'created'	=> 'date',		// DATETIME
					'modified'	=> 'modified'	// DATETIME
				),
				//Отображение контролов
				'controls' => array('add','edit','del'),
				'tabs'	=> array(
					//'Items'	=> 'Заказ'
				)
			),
			array(
				'id' 		=> array('name' => '№', 'class' => 'min', 'link'=>$this->GetLink().'&top={id}'),
				'name'		=> array('name' => 'Имя', 'length'=>'0-100'),
				'mail'		=> array('name' => 'Почта', 'length'=>'0-100', 'hide_from_table'=>true),
				'phone'		=> array('name' => 'Телефон', 'length'=>'1-100'),
				'address'	=> array('name' => 'Адрес', 'length'=>'0-120', 'hide_from_table'=>true),
				'date'		=> array('name' => 'Дата заказа'),
				'status'	=> array(
					'name' => 'Статус',
					'select' => array(
						//Обязательные
						'table'		=> 'shop_statuses',
						'name'		=> 'name',
						//Необязательные
						'id'		=> 'id'
					)
				),
				'paymethod'	=> array(
					'name' => 'Метод оплаты',
					'select' => array(
						//Обязательные
						'table'		=> 'shop_paymethods',
						'name'		=> 'name',
						//Необязательные
						'id'		=> 'id',
						'order'		=> 'order'
					),
					'hide_from_table'=>true
				)
			), '', 'date');
		} else {
			$this->title = '<a href="'.$this->GetLink().'#open'.(int)$_GET['top'].'">Заказ №'.(int)$_GET['top'].'</a>';
			$this->content = $this->DataTable('shop_orders_items',array(
				//Имена системных полей
				'nouns'	=> array(
					'id'		=> 'id',		// INT
					'name'		=> 'name'		// VARCHAR
				),
				//Отображение контролов
				'controls' => array('add','edit','del')
			),
			array(
				'id' 		=> array('name' => '№', 'class' => 'min'),
				/*
				'product'	=> array(
					'name' => 'Текущее имя товара',
					'select' => array(
						//Обязательные
						'table'		=> 'products',
						'name'		=> 'name',
						//Необязательные
						'id'		=> 'id',
						'deleted'	=> 'deleted'
					)
				),
				*/
				'name'		=> array(
					'name'		=> 'Имя товара на момент заказа',
					'length'	=> '1-120',
					'transform'	=> function($v, $row){
						return '<a target="_blank" href="http://'.$_SERVER['SERVER_NAME'].$row['link'].'">'.$v.'</a>';
					}
				),
				'price'		=> array('name' => 'Цена', 'style' => 'text-align:right'),
				'count'		=> array('name' => 'Количество', 'style' => 'text-align:right'),
				'total_fake'=> array(
					'name' => 'Итого',
					'style' => 'text-align:right',
					'transform'=>function($v, $row){
						return number_format($row['price']*$row['count'], 2, '.', '');
					}
				),
				
				'mail'		=> array('name' => 'Почта', 'length'=>'0-100'),
				'phone'		=> array('name' => 'Телефон', 'length'=>'0-100'),
				'address'	=> array('name' => 'Адрес', 'length'=>'0-120'),
				'date'		=> array('name' => 'Дата заказа'),

			), '`order` = '.(int)$_GET['top']);
		}
		
		
	}
	
	
	function PayMethods() {
		$this->content = $this->DataTable('shop_paymethods',array(
			//Имена системных полей
			'nouns'	=> array(
				'id'		=> 'id',		// INT
				'name'		=> 'name',		// VARCHAR
				'order'		=> 'order',
				'text'		=> 'text'
			),
			//Отображение контролов
			'controls' => array('add','edit','del')
		),
		array(
			'id' 		=> array('name' => '№', 'class' => 'min'),
			'type'		=> array('name' => 'Тип метода оплаты', 'length'=>'1-32', 'default'=>'Manual', 'autocomplete'=>array('WM','YD','Robokassa','RBK','Manual','PayOnlineSystem','Chronopay','PaymentSlip')),
			'name'		=> array('name' => 'Название', 'length'=>'1-64'),
			'order'		=> array('name' => 'Порядок', 'class'=>'min'),
			'show'		=> array('name' => 'Показывать'),
			'text'		=> array('name' => 'Описание метода оплаты', 'hide_from_table'=>true)
		));
	}
	
	function Statuses() {
		$this->content = $this->DataTable('shop_statuses',array(
			//Имена системных полей
			'nouns'	=> array(
				'id'		=> 'id',		// INT
				'name'		=> 'name',		// VARCHAR
				'order'		=> 'order'
			),
			//Отображение контролов
			'controls' => array('add','edit','del')
		),
		array(
			'id' 		=> array('name' => '№', 'class' => 'min'),
			'type'		=> array('name' => 'Тип статуса', 'length'=>'0-32', 'autocomplete'=>array('New','Profit','Noprofit')),
			'name'		=> array('name' => 'Название', 'length'=>'1-64'),
			'order'		=> array('name' => 'Порядок', 'class'=>'min')
		));
	}
	

	
}

?>