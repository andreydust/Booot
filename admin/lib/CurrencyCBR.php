<?php
class CurrencyCBR {
	
	function __construct() {}
	
	function Update() {
		$data = simplexml_load_file('http://www.cbr.ru/scripts/XML_daily.asp');
		$currencies = array();
		foreach ($data->Valute as $c) {
			$charcode = current($c->CharCode[0]);
			$currency = number_format((float)str_replace(',', '.', $c->Value[0]),2);
			$currencies[$charcode] = $currency;
		}
		if(!empty($currencies)) setVar('currency', serialize($currencies));
	}
}