<?php

class Content extends AdminModule {
	
	const name = 'Страницы и содержание';
	
	const order = 1;
	
	function Info() {
		
		//Модули сайта
		$siteModules = scandir(DIR.'/system/modules');
		$notShowingModules = array('Content','MainPage');
		foreach ($siteModules as $m) {
			if($m=='.'||$m=='..') continue;
			
			$m = strstr($m, '.', true);
			if(in_array($m, $notShowingModules)) continue;
			$modulesForSelect[] = $m;
		}
		
		//Топ
		if(!isset($_GET['top'])) {
			$this->title = 'Страницы и содержание';
		
			$this->content = $this->DataTable('content',array(
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
				'tabs' => array(
					'_Seo'	=> 'SEO'
				)
			),
			array(
				'id' 		=> array('name' => '№', 'class' => 'min'),
				'name'		=> array('name' => 'Название и подразделы', 'length'=>'1-100', 'class'=>'max', 'link'=>$this->GetLink().'&top={id}'),
				'text'		=> array('name' => 'HTML текст страницы', 'hide_from_table'=>true),
				'nav'		=> array(
					'name' => 'URI ссылка',
					'length'=>'0-32',
					'regex'=>'/^([a-z0-9-_]+)?$/i',
					'regex_error' => 'URI ссылка может быть только из цифр, латинских букв и дефиса',
					'if_empty_make_uri'	=> 'name'
				),
				'module'	=> array(
					'name'			=> 'Модуль',
					'length'		=> '0-32',
					'autocomplete'	=> $modulesForSelect
				),
				'show'		=> array('name' => 'Показывать', 'class'=>'min'),
				'showmenu'	=> array('name' => 'Показывать в меню', 'class'=>'min', 'transform' => 'YesNo'),
				'order'		=> array('name' => 'Порядок', 'class'=>'min')
			),
			'`top` = 0');
			
			//$this->hint['text'] = 'Если у раздела нет описания, то страница будет перенаправлена на первую вложенную';
			
		}
		//Содержание
		else {
			$i = $this->db->rows("SELECT * FROM `prefix_content` WHERE `id` = ".(int)$_GET['top']);
			
			$this->title = '<a href="'.$this->GetLink().'">Разделы</a> → '.$i[0]['name'];
		
			$this->content = $this->DataTable('content',array(
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
				'tabs' => array(
					'_Seo'	=> 'SEO'
				)
			),
			array(
				'id' 		=> array('name' => '№', 'class' => 'min'),
				'name'		=> array('name' => 'Название раздела', 'length'=>'1-100', 'link'=>$this->GetLink().'&top={id}'),
				'nav'		=> array(
					'name' => 'URI ссылка',
					'length'=>'0-32',
					'regex'=>'/^([a-z0-9-_]+)?$/i',
					'regex_error' => 'URI ссылка может быть только из цифр, латинских букв и дефиса',
					'if_empty_make_uri'	=> 'name'
				),
				'module'	=> array(
					'name'			=> 'Модуль',
					'length'		=> '0-32',
					'autocomplete'	=> $modulesForSelect
				),
				'show'		=> array('name' => 'Показывать', 'class'=>'min'),
				'showmenu'	=> array('name' => 'Показывать в меню', 'class'=>'min', 'transform' => 'YesNo'),
				'order'		=> array('name' => 'Порядок', 'class'=>'min'),
				'top'		=> array(
					'name'		=> 'Раздел',
					//'class'		=> 'min',
					'default'	=> $_GET['top'],
					'select'	=> array(
						//Обязательные
						'table'		=> 'content',
						'name'		=> 'name',
						//Необязательные
						'id'		=> 'id',
						'order'		=> 'order',
						'allow_null'=> true,
						'top'		=> 'top'
					)
				)
			),
			'`top` = '.(int)$_GET['top']);
		}
		
		
	}
	
	
	
	
}

?>