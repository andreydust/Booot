<?php
// vim: sw=4:ts=4:et:sta:

//set_time_limit(0);

class MarketFetcher {
	
	public $error = false;
	public $error_text = '';
	
    private static function curl_populate_options(&$h)
    {
        $user_agent = 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1.3) Gecko/20090924 Ubuntu/9.10 (karmic) Firefox/3.5.3';
        curl_setopt($h, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($h, CURLOPT_TIMEOUT, 15);
        curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($h, CURLOPT_AUTOREFERER, true);
        curl_setopt($h, CURLOPT_REFERER, 'http://market.yandex.ru/');
    }

    /* Отсылает запрос на поиск на Яндекс.Маркете */
    private static function market_get_list($search_str)
    {
        $h = curl_init();
        self::curl_populate_options($h);
        curl_setopt($h, CURLOPT_URL, 'http://market.yandex.ru/search.xml?text='.urlencode($search_str));
        $html = curl_exec($h);
        curl_close($h);

        return $html;
    }

    /* Парсит запрос на Яндекс.Маркете */
    private static function market_parse_list($html)
    {
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $list = $xpath->query("//a/@href[starts-with(.,'/model.xml')]");
        return @$list->item(0)->textContent;
    }

    /* Запрашивает страницу с инфо о товаре */
    private static function market_get_info($url)
    {
        $h = curl_init();
        self::curl_populate_options($h);
        curl_setopt($h, CURLOPT_URL, $url);
        $html = curl_exec($h);
        curl_close($h);

        return $html;
    }

    /**
     * Парсит страничку с инфо о товаре.
     * Возвращает ассоциативный массив (поле/значение)
     */
    private static function market_parse_info($html)
    {
        $dom = new DOMDocument;
        @$dom->loadHTML($html);

        $k = array();
        $v = array();

        $xpath = new DOMXPath($dom);
        $list = $xpath->query("//div[@id='full-spec-cont']/table/tbody/tr/td[@class='label']/span");
        for ($i=0; $i<=$list->length; $i++)
            $k[] = @$list->item($i)->textContent;
        $list = $xpath->query("//div[@id='full-spec-cont']/table/tbody/tr/td[@class='label']/parent::node()/td[2]");
        for ($i=0; $i<=$list->length; $i++)
            $v[] = @$list->item($i)->textContent;
        return array_combine($k, $v);
    }

    private static function market_parse_pics($html)
    {
        $dom = new DOMDocument;
        @$dom->loadHTML($html);

        $xpath = new DOMXPath($dom);
        $list = $xpath->query("//td[@class='bigpic']//a/@href | //td[@class='smallpic']//a/@href");
        $res = array();
        for ($i=0; $i < $list->length; $i++)
            $res[] = $list->item($i)->textContent;
        return $res;
    }
	
    /*
    public static function array2json($arr)
    {
        $parts = array();
        $is_list = false;
        if (!is_array($arr))
            return;
        if (count($arr)<1)
            return '{}';

        //Find out if the given array is a numerical array
        $keys = array_keys($arr);
        $max_length = count($arr)-1;
        if (($keys[0] == 0) && ($keys[$max_length] == $max_length)) {
            //See if the first key is 0 and last key is length - 1
            $is_list = true;
            for ($i=0; $i<count($keys); $i++) { //See if each key correspondes to its position
                if ($i != $keys[$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }

        foreach ($arr as $key=>$value) {
        if (is_array($value)) { //Custom handling for arrays
            if ($is_list)
                $parts[] = self::array2json($value); // :RECURSION:
            else
                $parts[] = '"' . $key . '":' . self::array2json($value); // :RECURSION:
            } else {
                $str = '';
                if (!$is_list)
                    $str = '"' . $key . '":';

                //Custom handling for multiple data types
                if (is_numeric($value))
                    $str .= $value; //Numbers
                else if ($value === false)
                    $str .= 'false'; //The booleans
                else if ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes($value) . '"'; //All other things
                $parts[] = $str;
            }
        }
        $json = implode(',',$parts);

        if ($is_list)
            return '[' . $json . ']'; //Return numerical JSON
        return '{' . $json . '}'; //Return associative JSON
    }
    */

    public function query($brand, $product)
    {
        $list = self::market_get_list($brand.' '.$product);
        if(!$list) {
        	$this->error = true;
        	$this->error_text = 'Ошибка при поиске';
        	return false;
        }
            //die('err:Ошибка при поиске');
        sleep(1);
        $url = self::market_parse_list($list);
        if (!$url) {
            $product_ss = str_replace(' ', '', $product);
            if ($product != $product_ss) {
                sleep(1);
                $list = self::market_get_list($brand.' '.$product_ss);
                if(!$list) {
		        	$this->error = true;
		        	$this->error_text = 'Ошибка при поиске';
		        	return false;
		        }
                    //die('err:Ошибка при поиске');
                sleep(1);
                $url = self::market_parse_list($list);
                if (!$url) {
		        	$this->error = true;
		        	$this->error_text = 'Товар не найден';
		        	return false;
		        }
                    //die('err:Товар не найден');
            } else {
            	$this->error = true;
		        $this->error_text = 'Товар не найден';
		        return false;
                //die('err:Товар не найден');
            }
        }
        sleep(rand(2,5));
        $info = self::market_get_info('http://market.yandex.ru'.$url);
        if (!$info) {
		    $this->error = true;
		    $this->error_text = 'Ошибка при получении инфо о товаре';
		    return false;
		}
            //die('err:Ошибка при получении инфо о товаре');
        if (isset($_GET['pics'])) {
            $data = self::market_parse_pics($info);
        } else {
            $data = self::market_parse_info($info);
        }
        if (!$data) {
		    $this->error = true;
		    $this->error_text = 'В информации о товаре нет данных';
		    return false;
		}
            //die('err:В информации о товаре нет данных');
        if (isset($_GET['pics'])) {
            $res['pics'] = $data;
            //$res['x'] = 'y';
            return $res;
        }

        $res['fields'] = array();
        //$res['x'] = 'y';
        foreach ($data as $k => $v) {
            if(empty($k))
                continue;
            $v = str_replace("\n", "", $v);
            $res['fields'][] = array('name' => addslashes($k), 'value' => addslashes($v));
        }
        return $res;
    }
}

/*
header('Content-Type: text/html; charset=utf-8');
$brand = $_GET['brand'];
$product = $_GET['product'];
if (!$brand || !$product)
    die('err:query string is empty!');
$r = MarketFetcher::query($brand, $product);
echo MarketFetcher::array2json($r);
*/

?>