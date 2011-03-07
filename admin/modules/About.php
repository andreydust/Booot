<?php

class About extends AdminModule {
	
	const name = 'О системе';
	
	const order = 0;
	
	function Info() {
		$this->content = tpl('/modules/'.__CLASS__.'/cms_info');
		$this->title = 'Booot CMS';
	}
}

?>