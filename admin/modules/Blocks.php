<?php
class Blocks extends AdminModule {
	
	const name = 'Блоки';
	
	const order = 2;
	
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
}

?>