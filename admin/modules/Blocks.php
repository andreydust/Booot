<?php
class Blocks extends AdminModule {
	
	const name = 'Блоки';
	
	const order = 2;

	public $submenu = array(
		'Info'	=> 'Блоки'
	);

	function Construct() {
		if($GLOBALS['config']['site']['theme'] == 'cloudyNoon') {
			$this->submenu['CloudyNoonBanner'] = 'Баннер Cloudy Noon';
		}
	}
	
	function Info() {
		
		$this->title = 'Блоки';
		$this->hint['text'] = 'Для вставки блока в шаблон используйте php-синтаксис — <code>&lt;?php block(1)?&gt;</code><br /> Для вставки в любой текст — <code>{block:1}</code><br /><br /> Где 1 — № нужного блока, он находится в первом столбце таблицы';
		$this->content = $this->DataTable('blocks',array(
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
				'edit'
			)
		),
		array(
			'id' 		=> array('name' => '№', 'class' => 'min'),
			'name'		=> array('name' => 'Название блока', 'length'=>'1-128'),
			'callname'	=> array('name' => 'Имя для вызова', 'length'=>'0-128'),
			'text'		=> array('name' => 'HTML код блока', 'hide_from_table' => true),
			'show'		=> array('name' => 'Показывать', 'class'=>'min'),
			'order'		=> array('name' => 'Порядок', 'class'=>'min')
		));
		
	}


	function CloudyNoonBanner() {
		$this->title = 'Баннер на главной для темы Cloudy Noon';
		$this->content = $this->DataTable('cloudynoon_banner',array(
			//Имена системных полей
			'nouns'	=> array(
				'id'		=> 'id',		// INT
				'name'		=> 'name',		// VARCHAR
				'order'		=> 'order',		// INT
				'deleted'	=> 'deleted',	// ENUM(Y,N)
				'created'	=> 'created',	// DATETIME
				'modified'	=> 'modified'	// DATETIME
			),
			//Отображение контролов
			'controls' => array(
				'add',
				'edit',
				'del'
			),
			//Табы (методы этого класса)
			'tabs'	=> array(
				'CloudyNoonBannerImages'	=> 'Изображения'
			)
		),
		array(
			'id' 			=> array('name' => '№', 'class' => 'min'),
			'name'			=> array('name' => 'Название слайда', 'length'=>'1-128'),
			'title'			=> array('name' => 'Заголовок слайда', 'length'=>'0-128', 'hide_from_table' => true),
			'description'	=> array('name' => 'Описание под заголовком', 'length'=>'0-128', 'hide_from_table' => true),
			'button_name'	=> array('name' => 'Текст кнопки', 'length'=>'0-128', 'hide_from_table' => true),
			'link'			=> array('name' => 'Ссылка на кнопке', 'length'=>'0-128', 'hide_from_table' => true),
			//'text'			=> array('name' => 'HTML код слайда', 'hide_from_table' => true),
			'show'			=> array('name' => 'Показывать', 'class'=>'min'),
			'order'			=> array('name' => 'Порядок', 'class'=>'min')
		));
	}

	function CloudyNoonBannerImages() {
		$id = (int)(isset($_REQUEST['id'])?$_REQUEST['id']:0);
		
		if($id == 0) {
			echo 'Сначала создайте запись';
			exit();
		}
		
		$images = new Images();
		
		//Добавление картинки
		if(!empty($_FILES)) {
			$images->AddImage($_FILES['image']['tmp_name'], 'CloudyNoonBanner', $id, $_FILES['image']['name']);
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
			'images'	=> $images->GetImages('CloudyNoonBanner', $id),
			'link'		=> $this->GetLink(),
			'module'	=> 'CloudyNoonBanner',
			'module_id'	=> $id
		));
		exit();
	}
}

?>