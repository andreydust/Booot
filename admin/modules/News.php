<?php
class News extends AdminModule {
	
	const name = 'Новости (пресс-центр)';
	
	const order = 8;
	
	function Info() {
		
		$this->title = 'Новости';
		$_GET['orderd'] = 'DESC';
		$this->content = $this->DataTable('news',array(
			//Имена системных полей
			'nouns'	=> array(
				'id'		=> 'id',		// INT
				'name'		=> 'name',		// VARCHAR
				//'order'		=> 'order',		// INT
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
			),
			'tabs' => array(
				'_Seo'	=> 'SEO'
			)
		),
		array(
			'id' 		=> array('name' => '№', 'class' => 'min'),
			'name'		=> array('name' => 'Имя новости', 'length'=>'1-128'),
			'anons'		=> array('name' => 'Анонс новости', 'length'=>'0-140', 'hide_from_table'=>true),
			'show'		=> array('name' => 'Показывать', 'class'=>'min'),
			'date'		=> array('name' => 'Дата публикации', 'transform'=>function($str){ return goodDate($str); })
			//'order'		=> array('name' => 'Порядок', 'class'=>'min')
		),'','date');
		
		$this->hint['text'] = 'Вы можете добавить анонс новости в ее свойствах <img src="/admin/images/icons/pencil.png" style="vertival-align:middle" /><br>или изменить саму новость в редактировании содержания <img src="/admin/images/icons/document-text-image.png" style="vertival-align:middle" />';
	}
}

?>
