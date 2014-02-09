<?php

/**
 * Нормальная, хорошая дата, например 23 февраля 2010
 */
function goodDate($dateStr) {
	$date = strtotime($dateStr);
	$monthesIn = array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
	
	$Year = (int)date('Y',$date);
	$Month = (int)date('m',$date);
	$Day = (int)date('d',$date);
	
	return $Day.' '.$monthesIn[$Month-1].' '.$Year;
}

/**
 * Множественное число
 *
 * @param int $n
 * @param string $str0
 * @param string $str1
 * @param string $str2
 */
function plural($n, $str0, $str1, $str2) {
	$n = (int)$n;
	return $n%10==1&&$n%100!=11?$str1:($n%10>=2&&$n%10<=4&&($n%100<10||$n%100>=20)?$str2:$str0);
}

/**
 * Удобный вывод массивов и другой отладочной информации
 * @param mixed $data
 */
function debug($data) {
	if(!$GLOBALS['config']['develop']) return false;
	
	//Аяксовый запрос
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		if(is_array($data)) {
			$output = print_r($data, 1);
		} else {
			$output = $data;
		}
	}
	//Обычный запрос
	else {
		if(is_array($data)) {
			$output = '<pre>'.print_r($data, 1).'</pre>';
		} else {
			$output = '<pre>'.$data.'</pre>';
		}
	}
	
	echo $output;
}


function jabber($to, $message) {
	@require_once(DIR.'/system/lib/XMPPHP/XMPP.php');
	  
	@$conn = new XMPPHP_XMPP(
		$GLOBALS['config']['jabber']['host'],
		$GLOBALS['config']['jabber']['port'],
		$GLOBALS['config']['jabber']['user'],
		$GLOBALS['config']['jabber']['password'],
		$GLOBALS['config']['jabber']['resource'],
		$GLOBALS['config']['jabber']['server'],
		$printlog = false,
		$loglevel = XMPPHP_Log::LEVEL_INFO
	);
	
	try {
		@$conn->connect();
		@$conn->processUntil('session_start');
		@$conn->presence();
		@$conn->message($to, $message);
		@$conn->disconnect();
		return true;
	} catch(XMPPHP_Exception $e) {
		return false;
	}
}

function icqStatus($uin, $return=false) {
	$stored = getVar('icq:'.$uin);
	$upd = true;
	$nowtime = time();
	if(!empty($stored)) {
		$stored = unserialize($stored);
		if($stored['when'] + 3600 <= $nowtime) {
			$upd = true;
		} else {
			$upd = false;
		}
	}
	
	if($upd) {
		//Проверка icq статуса
		$status = 'na';
		$fp = @fsockopen("status.icq.com", 80);
		if($fp) {
			fputs($fp, "GET /online.gif?icq=$uin&img=5 HTTP/1.0\n\n");
			while ($line = fgets($fp, 128)) {
				if (strpos($line, 'Location') !== false) {
					if (strpos($line, 'online1')!== false) $status = 'online';
					elseif (strpos($line, 'online0') !== false) $status = 'offline';
					break;
				}
			}
		}
		
		$stored = array(
			'when'		=> $nowtime,
			'status'	=> $status
		);
		
		setVar('icq:'.$uin, serialize($stored));
	}
	
	if($return) return $stored['status'];
	else echo '<img src="/Images/Icons/icq/'.$stored['status'].'.gif" width="15" height="15" alt="icq '.$stored['status'].'" /> '.$uin;
	
}

/**
 * Добавляет, изменяет или удаляет переменные
 * на основании текущего GET запроса
 * и отдает строку полученного запроса
 *
 * @param array $param array('key'=>'val'[, ...])
 * @param boolean $not_complete Не отдавать «?» в начале запроса
 */
function getget($param=array(), $not_complete=false) {
	$get = $_GET;
	
	if(!empty($param)) {
		foreach ($get as $k=>$v) {
			if(isset($param[$k])) {
				
				//unset
				if($param[$k] === false) {
					unset($get[$k]);
					continue;
				}
				
				$get[$k] = $param[$k];
				unset($param[$k]);
			}
		}
		
		//clean up params
		foreach ($param as $k=>$v) if(!$v) unset($param[$k]);
		
		$get = array_merge($get, $param);
	}
	
	$query = http_build_query($get,'','&amp;');
	$return = ($not_complete?'':'?').$query;
	if($return == '?') return '';
	else return $return;
}


class Lingua_Stem_Ru
{
    var $VERSION = "0.02";
    var $Stem_Caching = 0;
    var $Stem_Cache = array();
    var $VOWEL = '/аеиоуыэюя/';
    var $PERFECTIVEGROUND = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$/';
    var $REFLEXIVE = '/(с[яь])$/';
    var $ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|еых|ую|юю|ая|яя|ою|ею)$/';
    var $PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$/';
    var $VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/';
    var $NOUN = '/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|й|и|ы|ь|ию|ью|ю|ия|ья|я)$/';
    var $RVRE = '/^(.*?[аеиоуыэюя])(.*)$/';
    var $DERIVATIONAL = '/[^аеиоуыэюя][аеиоуыэюя]+[^аеиоуыэюя]+[аеиоуыэюя].*(?<=о)сть?$/';
 
    function s(&$s, $re, $to)
    {
        $orig = $s;
        $s = preg_replace($re, $to, $s);
        return $orig !== $s;
    }
 
    function m($s, $re)
    {
        return preg_match($re, $s);
    }
 
    function stem_word($word)
    {
        $word = mb_strtolower($word);
 
        $word = str_replace("ё","е",$word);
        # Check against cache of stemmed words
        if ($this->Stem_Caching && isset($this->Stem_Cache[$word])) {
            return $this->Stem_Cache[$word];
        }
        $stem = $word;
        do {
          if (!preg_match($this->RVRE, $word, $p)) break;
          $start = $p[1];
          $RV = $p[2];
          if (!$RV) break;
 
          # Step 1
          if (!$this->s($RV, $this->PERFECTIVEGROUND, '')) {
              $this->s($RV, $this->REFLEXIVE, '');
 
              if ($this->s($RV, $this->ADJECTIVE, '')) {
                  $this->s($RV, $this->PARTICIPLE, '');
              } else {
                  if (!$this->s($RV, $this->VERB, ''))
                      $this->s($RV, $this->NOUN, '');
              }
          }
 
          # Step 2
          $this->s($RV, '/и$/', '');
 
          # Step 3
          if ($this->m($RV, $this->DERIVATIONAL))
              $this->s($RV, '/ость?$/', '');
 
          # Step 4
          if (!$this->s($RV, '/ь$/', '')) {
              $this->s($RV, '/ейше?/', '');
              $this->s($RV, '/нн$/', 'н');
          }
 
          $stem = $start.$RV;
        } while(false);
        if ($this->Stem_Caching) $this->Stem_Cache[$word] = $stem;
        return $stem;
    }
 
    function stem_caching($parm_ref)
    {
        $caching_level = @$parm_ref['-level'];
        if ($caching_level) {
            if (!$this->m($caching_level, '/^[012]$/')) {
                die(__CLASS__ . "::stem_caching() - Legal values are '0','1' or '2'. '$caching_level' is not a legal value");
            }
            $this->Stem_Caching = $caching_level;
        }
        return $this->Stem_Caching;
    }
 
    function clear_stem_cache()
    {
        $this->Stem_Cache = array();
    }
}