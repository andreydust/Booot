<?php
class MySQL {
	
	var $link;
	var $query_string;
	var $queryResult;
	var $dbResultLine;
	var $error_string;
	var $array_debug;
	
	function __construct() {
		if(!$this->link = mysqli_connect($GLOBALS['config']['db']['server'], $GLOBALS['config']['db']['user'], $GLOBALS['config']['db']['password'], $GLOBALS['config']['db']['db'])) die($GLOBALS['config']['msg']['mysqlftl']);
		//if(!$this->link = mysql_connect($GLOBALS['config']['db']['server'], $GLOBALS['config']['db']['user'], $GLOBALS['config']['db']['password'], true)) die($GLOBALS['config']['msg']['mysqlftl']);
		//mysql_select_db($GLOBALS['config']['db']['db'], $this->link);
		if($GLOBALS['config']['db']['set_names_utf8']) $this->query("SET NAMES UTF8");
		return $this->link;
	}
	
	function error() {
		if(!$GLOBALS['config']['develop']) return '';
		echo '
		<div style="background:white; color:black display:block;font-family:Trebuchet MS,Arial,sans-serif;">
			<h1 style="color:#0498EC;font-size:1.5em;">'.$GLOBALS['config']['msg']['mysqlerr'].'</h1>
			<ul style="padding: 0em 1em 1em 1.55em;font-size:0.8em;margin:0 0 0 2.5em;">
				<li>'.$this->error_string.'</li>
				<li>'.htmlspecialchars($this->query_string).'</li>
				<li>'.$this->array_debug[1]['file'].', '.$this->array_debug[1]['line'].'</li>
			</ul>
		</div>';
    }
	
	function query($query, $multi=false) {
		if(!isset($GLOBALS['query_count'])) $GLOBALS['query_count'] = 0;
		$GLOBALS['query_count']++;
		if($GLOBALS['config']['db']['show_query_devmode']) {
			$qplace = debug_backtrace();
			$qplace_path_info = pathinfo($qplace[0]['file']);
			if($qplace_path_info['basename'] == 'SimpleData.php') {
				$qplace = '<b>SimpleData — '.$qplace[1]['file'].' '.$qplace[1]['line'].':</b> ';
			} else {
				$qplace = '<b>'.$qplace[0]['file'].' '.$qplace[0]['line'].':</b> ';
			}
		} else $qplace = '';
		if(!isset($GLOBALS['query_log'])) $GLOBALS['query_log'] = array();
		$GLOBALS['query_log'][] = $qplace.$query;
		
		$this->query_string  = str_replace('prefix_',$GLOBALS['config']['db']['prefix'],$query);
		//$this->queryResult = mysql_query($this->query_string, $this->link);
		
		if($multi) {
			
			if (mysqli_multi_query($this->link, $this->query_string)) {
				do {
					if ($result = mysqli_store_result($this->link)) {
						//Затычка
						//...
						mysqli_free_result($result);
					}
					if (!mysqli_more_results($this->link)) break;
				} while (mysqli_next_result($this->link));
			}
			
		} else {
			$this->queryResult = mysqli_query($this->link, $this->query_string);
		}
		
		if($this->queryResult) return true;
		else {
			//$this->error_string = mysql_error();
			$this->error_string = mysqli_error($this->link);
			$this->array_debug  = debug_backtrace();
			$this->error();
			return false;
		}
	}
	
	function last_insert_id() {
		//return mysql_insert_id($this->link);
		return mysqli_insert_id($this->link);
	}
	
	function fetch($result_type=MYSQL_BOTH) {
		//$this->dbResultLine = mysql_fetch_array($this->queryResult, $result_type);
		$this->dbResultLine = mysqli_fetch_array($this->queryResult, $result_type);
		return $this->dbResultLine;
	}
	
	function row_count() {
		//return mysql_num_rows($this->queryResult);
		return mysqli_num_rows($this->queryResult);
	}
	
	function rows($query, $result_type=MYSQL_BOTH, $key='') {
		$this->query($query);
		$ret = array();
			while($i = $this->fetch($result_type)) {
			if(!empty($key)) {
				$ret[$i[$key]] = $i;
			} else {
				$ret[] = $i;
			}
		}
		return $ret;
	}

	/**
	 * query first row
	 */
	function query_first($query, $result_type=MYSQL_BOTH)
	{
		if ($this->query($query)) {
			$row = $this->fetch($result_type);
			mysqli_free_result($this->queryResult);
			return $row;
		}
		return false;
	}

	/**
	 * query first value of first row
	 */
	function query_value($query)
	{
		$res = $this->rows($query, MYSQL_NUM);
		return @$res[0][0];
	}
}
