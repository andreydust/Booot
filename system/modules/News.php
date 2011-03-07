<?php

class News {
	
	private $data;
	
	private $table = 'news';
	
	private $return;
	
	private $year = 0;
	private $month = 0;
	private $id = 0;
	
	private $monthesIn = array('января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря');
	private $monthes   = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
	
	function __construct() {
		$this->data = $GLOBALS['data'];
	}
	
	
	function Output() {
		
		//RSS
		if(isset($_GET['rss'])) { $this->RSS(); }
		$GLOBALS['head_add'] .= '
		<link rel="alternate" type="application/rss+xml" title="Новости '.$GLOBALS['config']['site']['title'].'" href="http://'.$_SERVER['SERVER_NAME'].linkByModule('News').'?rss" />';
		
		if (preg_match('%/?((?P<year>[\d]{4})/((?P<month>[\d]{2})/((?P<id>[\d]+))?)?)?\z%i', $_SERVER['REQUEST_URI'], $newsURI)) {
			$this->data->GetData($this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC');
			
			if(isset($newsURI['year']))		$this->year		= abs((int)$newsURI['year']);
			if(isset($newsURI['month']))	$this->month	= abs((int)$newsURI['month']);
			if(isset($newsURI['id']))		$this->id		= abs((int)$newsURI['id']);
			
			//$GLOBALS['sidebar'] = $this->Menu();
			
			if($this->year!=0 && $this->month!=0 && $this->id!=0) {
				return $this->OneNews();
			} elseif($this->year!=0 && $this->month!=0) {
				return $this->MonthNews();
			} elseif($this->year!=0) {
				return $this->YearNews();
			} else {
				return $this->MainPage();
			}
		} else {
			page404();
		}
	}
	
	/**
	 * SEO для новостей
	 * @param int $id
	 */
	private function SEO() {
		$this->seo = db()->query_first("SELECT * FROM `prefix_seo` WHERE `module` = '".__CLASS__."' AND `module_id` = ".(int)$this->id." AND `module_table` = 'news'");
		if(!empty($this->seo)) {
			//Keywords
			if(!empty($this->seo['keywords'])) {
				$GLOBALS['head_add'] .= "\r\n".'<meta name="keywords" content="'.htmlspecialchars($this->seo['keywords']).'" />';
			}
			//Description
			if(!empty($this->seo['description'])) {
				$GLOBALS['head_add'] .= "\r\n".'<meta name="description" content="'.htmlspecialchars($this->seo['description']).'" />';
			}
			$GLOBALS['head_add'] .= "\r\n";
			//Title
			if(!empty($this->seo['title'])) {
				$this->seo['title'] = $this->seo['title'];
			}
		}
	}
	
	/**
	 * Генерация массива разделов для подменю,
	 * вызывается модулем Content из метода SubMenu
	 *
	 * array( array('name','link','active') )
	 */
	public function IntegrationMenu() {
		$dates = db()->rows("
			SELECT
				YEAR(`date`) AS `year`,
				MONTH(`date`) AS `month`
			FROM `prefix_news`
			WHERE `deleted` = 'N' AND `show` = 'Y' AND `date` <= NOW()
			GROUP BY MONTH(`date`), YEAR(`date`)
			ORDER BY `date` DESC");
		$data = $menu = array();
		foreach ($dates as $date) {
			$data[$date['year']][] = $date['month'];
		}
		$i = 0;
		foreach ($data as $year=>$monthes) {
			$menu[$i] = array(
				'name'	=> 'Новости за <strong>'.$year.'</strong> год'
			);
			foreach ($monthes as $month) {
				if($this->year == $year && $this->month == $month) $active = true;
				else $active = false;
				$menu[$i]['sub'][] = array(
					'name'		=> $this->monthes[$month-1],
					'link'		=> $this->Link($year, $month),
					'active'	=> $active
				);
			}
			$i++;
		}
		return $menu;
	}

	
	/**
	 * Массив для хлебных крошек модуля
	 *
	 * array( array('name','link') )
	 */
	public function breadCrumbs() {
		if($this->year!=0 && $this->month!=0 && $this->id!=0) {
			$news = $this->data->GetDataById($this->table, $this->id);
			return array(
				array('name' => 'Новости за '.mb_strtolower($this->monthes[$this->month-1]).' '.$this->year, 'link'=>$this->Link($this->year, $this->month)),
				array('name' => $news['name'], 'link'=>$this->Link($this->year, $this->month, $this->id))
			);
		} elseif($this->year!=0 && $this->month!=0) {
			return array(
				array('name' => 'Новости за '.mb_strtolower($this->monthes[$this->month-1]).' '.$this->year, 'link'=>$this->Link($this->year, $this->month))
			);
		}
		
		return array();
	}
	
	/**
	 * Генерация и выдача RSS новостей
	 */
	private function RSS() {
		$news = $this->data->GetData($this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC', '20');
		
		$items = '';
		foreach ($news as $new) {
			$parsedDate	= strtotime($new['date']);
			$newYear = date('Y',$parsedDate);
			$newMonth = date('m',$parsedDate);
			
			$items .= '
	<item>
		<title>'.$new['name'].'</title>
		<description>'.$new['anons'].'</description>
		<link>http://'.$_SERVER['SERVER_NAME'].$this->Link($newYear, $newMonth, $new['id']).'</link>
		<guid>'.$new['id'].'</guid>
		<pubDate>'.date('r',strtotime($new['date'])).'</pubDate>
	</item>';
			
			
		}
		
		
		
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
<channel>
	<title>Новости '.$_SERVER['SERVER_NAME'].'</title>
	<description>Новости сайта '.$_SERVER['SERVER_NAME'].' '.$GLOBALS['config']['site']['title'].'</description>
	<link>http://'.$_SERVER['SERVER_NAME'].'</link>
	<lastBuildDate>'.date('r').'</lastBuildDate>
	<language>ru</language>
	<generator>Booot CMS</generator>
	'.$items.'
</channel>
</rss>';
		
		header("Content-Type: application/xml; charset=UTF-8");
		echo $xml;
		exit();
	}
	
	
	private function OneNews() {
		$news = $this->data->GetDataById($this->table, $this->id);
		
		if(empty($news)) page404();
		
		$this->SEO();
		
		$parsedDate	= strtotime($news['date']);
		$newsYear = date('Y',$parsedDate);
		$newsMonth = date('m',$parsedDate);
		
		$vars = array(
			'title'		=> $news['name'],
			'h1'		=> $news['name'],
			'date'		=> goodDate($news['date']),
			'content'	=> $news['text'],
			'link'		=> $this->Link(),
			'news_id' => $this->id
		);

		$content = tpl('modules/'.__CLASS__.'/newsone',$vars);
		return tpl('page', array(
			'title'	=> isset($this->seo['title'])&&!empty($this->seo['title'])?$this->seo['title']:$vars['title'].' — '.$GLOBALS['config']['site']['title'],
			'name'	=> $vars['title'],
			'text'	=> $content
		));
	}
	
	
	private function MonthNews() {
		$news = $this->data->GetData($this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC');
		
		if(empty($news)) page404();
		
		foreach($news as $k=>$v) {
			
			$parsedDate	= strtotime($v['date']);
			$newsYear = date('Y',$parsedDate);
			$newsMonth = date('m',$parsedDate);
			
			if($newsMonth != $this->month) continue;
			if($newsYear != $this->year) continue;
			
			$newsList[] = array(
				'name'	=> $v['name'],
				'anons'	=> $v['anons'],
				'link'	=> $this->Link($newsYear, $newsMonth, $v['id']),
				'date'	=> goodDate($v['date']),
				'text'	=> $v['text']
			);
		}
		
		$vars = array(
			'title'		=> 'Новости за '.mb_strtolower($this->monthes[$this->month-1]).' '.$this->year,
			'news'		=> $newsList,
			'link'		=> $this->Link(),
		);
		$GLOBALS['sidebar'] = $this->Menu();
		
		$content = tpl('modules/'.__CLASS__.'/newslist',$vars);
		return tpl('page', array(
			'title'	=>$vars['title'],
			'name'	=>$vars['title'],
			'text'	=>$content
		));
	}
	
	
	private function YearNews() {
		$news = $this->data->GetData($this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC');
		
		if(empty($news)) page404();
		
		foreach($news as $k=>$v) {
			$parsedDate	= strtotime($v['date']);
			$newsYear = date('Y',$parsedDate);
			if($newsYear == $this->year) {
				$newsMonth = date('m',$parsedDate);
				break;
			}
		}
		
		if(!isset($newsMonth)) page404();
		
		header('Location: '.$this->Link($this->year, $newsMonth));
	}
	
	
	private function MainPage() {
		$news = $this->data->GetData($this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC');
		
		if(empty($news)) return tpl('page', array('title'=>'Новости', 'name'=>'Новости', 'text'=>tpl('modules/'.__CLASS__.'/nonews')));
		
		$lastNews = current($news);
		
		$parsedDate	= strtotime($lastNews['date']);
		$newsYear = date('Y',$parsedDate);
		$newsMonth = date('m',$parsedDate);
		
		header('Location: '.$this->Link($newsYear, $newsMonth));
	}
	
	
	function Link($year=0, $month=0, $id=0) {
		if($month < 10) $month = '0'.(int)$month;
		return '/'.implode('/',$GLOBALS['path_nav']).'/'.($year==0?'':($year.'/'.($month==0?'':$month.'/'.($id==0?'':$id))));
	}
	
	
	function Menu() {
		$news = $this->data->GetData($this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC');
		
		$menu = array();
		
		foreach($news as $k=>$v) {
			
			$parsedDate	= strtotime($v['date']);
			$newsYear = date('Y',$parsedDate);
			$newsMonth = date('m',$parsedDate);
			//$newsDay = (int)date('d',$parsedDate);
			
			if($newsMonth == $this->month) $active = true;
			else $active = false;
			
			$menu[$newsYear][$newsMonth] = array(
				'number'	=> $newsMonth,
				'name'		=> $this->monthes[$newsMonth-1],
				'link'		=> $this->Link($newsYear, $newsMonth),
				'active'	=> $active
			);
			
		}
		
		return tpl('modules/'.__CLASS__.'/menu',array('menu'=>$menu));
	}
	
	
	function LastNewsBlock() {
		$news = array_slice($this->data->GetData($this->table, 'AND `show` = \'Y\' AND `date` <= NOW()', '`date` DESC'),0,3);
		
		$newsList = array();
		foreach($news as $k=>$v) {
			
			$parsedDate	= strtotime($v['date']);
			$newsYear = date('Y',$parsedDate);
			$newsMonth = date('m',$parsedDate);
						
			$newsList[] = array(
				'name'	=> $v['name'],
				'text'	=> $v['anons'],
				'link'	=> linkByModule(__CLASS__).'/'.$newsYear.'/'.$newsMonth.'/'.$v['id'],
				'date'	=> goodDate($v['date'])
			);
		}
		
		return tpl('modules/News/lastnewsblock', array(
			'link'	=> $this->Link(),
			'news'	=> $newsList
		));
	}

	function BlockMain($limit = 5)
	{
		$rows = db()->rows("SELECT * FROM prefix_news WHERE `show`='Y' AND `deleted`='N' ORDER BY `date` DESC, `id` DESC LIMIT {$limit}");
		return tpl('modules/News/blockmain', array('items'=>$rows));
	}
}

