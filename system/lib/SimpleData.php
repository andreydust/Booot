<?php

class SimpleData {
	
	private $data = array();
	
	private $columns = array();
	
	private $static_filter = array();
	
	function __construct() {
		
	}
	
	function GetData($table, $where_filter='', $order_by='', $limit='') {
		$where_filter = trim($where_filter);
		$order_by = trim($order_by);
		$limit = trim($limit);
		
		
		if(
			isset($this->data[$table]) &&
			$this->data[$table]['key'] == $table.$where_filter.$order_by.$limit
		) return $this->data[$table]['data'];
		
		$db = db();
		
		$this->data[$table] = array();
		$this->data[$table]['key'] = $table.$where_filter.$order_by.$limit;
		$this->data[$table]['data'] = array();
		
		if(isset($this->columns[$table])) {
			$syscolumns = $this->columns[$table];
		} else {
			$db->query('SHOW COLUMNS FROM `prefix_'.$table.'`');
			
			$syscolumns = array();
			while($i = $db->fetch(MYSQL_ASSOC)) {
				$syscolumns[$i['Field']] = $i;
			}
			$this->columns[$table] = $syscolumns;
		}
		
		//Есть ли колонка статуса «удалено»
		if(isset($syscolumns['deleted'])) $where_deleted = ' AND `deleted` = \'N\'';
		else $where_deleted = '';
		
		//Задана ли сортировка
		if(!empty($order_by)) {
			$order = ' ORDER BY '.$order_by.'';
		} elseif(isset($syscolumns['order'])) {
			$order = ' ORDER BY `order`';
		} else $order = '';
		
		//Заданы ли лимиты
		if(!empty($limit)) {
			$limit_filter = 'LIMIT '.$limit.'';
		} else {
			$limit_filter = '';
		}
		
		//Определен ли id
		foreach ($syscolumns as $column) {
			if($column['Key'] == 'PRI') {
				$field_id = $column['Field'];
			}
		}
		
		//Статический фильтр
		if(isset($this->static_filter[$table]) && !empty($this->static_filter[$table])) {
			$static_filter = 'AND '.$this->static_filter[$table];
		} else {
			$static_filter = '';
		}
		
		$sql = 'SELECT * FROM `prefix_'.$table.'` WHERE 1 '.$where_deleted.' '.$where_filter.' '.$static_filter.' '.$order.' '.$limit_filter;
		$db->query($sql);
		while($i = $db->fetch(MYSQL_ASSOC)){
			if(isset($field_id)) $this->data[$table]['data'][$i[$field_id]] = $i;
			else $this->data[$table]['data'][] = $i;
		}
		
		return $this->data[$table]['data'];
	}
	
	function GetDataById($table, $id) {
		if($id==0) return false;
		if(isset($this->data[$table]['data'][$id])) return $this->data[$table]['data'][$id];
		else {
			$data = $this->GetData($table);
			if(empty($data[$id])) return false;
			return $data[$id];
		}
		
		return array();
	}
	
	
	function SetStaticFilter($table, $where) {
		$this->static_filter[$table] = $where;
	}
	
}

?>