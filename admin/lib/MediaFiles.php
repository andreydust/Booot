<?php
class MediaFiles {
	
	private $alldata, $db;
	
	private $supported = array(
		'video'	=> array('flv'),
		'audio'	=> array('mp3'),
		'doc'	=> array('pdf','doc','xls','docx','xlsx','odt','ods','txt','rtf'),
		'pack'	=> array('zip','rar','gz')
	), $supported_exts;
	
	private $mustConvert = array(
		'flv'	=> array('3gp','avi','dat','m4v','mkv','mov','mp4','rm','vob','wmv'),
		'mp3'	=> array('wma','wav','flac','ogg')
	);
	
	public $mediaTypes = array(
		'video'	=> array(
			'name'	=> 'Видео',
			'exts'	=> array()
		),
		'audio'	=> array(
			'name'	=> 'Аудио',
			'exts'	=> array()
		),
		'doc'	=> array(
			'name'	=> 'Документы',
			'exts'	=> array()
		),
		'pack'	=> array(
			'name'	=> 'Архивы',
			'exts'	=> array()
		)
	);
	
	private $icons = array(
		'flv'	=> 'video',
		'mp3'	=> 'mp3',
		'pdf'	=> 'pdf',
		'doc'	=> 'doc',
		'xls'	=> 'xls',
		'docx'	=> 'doc',
		'xlsx'	=> 'xls',
		'odt'	=> 'empty',
		'ods'	=> 'empty',
		'txt'	=> 'txt',
		'rtf'	=> 'doc',
		'ppt'	=> 'ppt',
		'rar'	=> 'rar',
		'zip'	=> 'zip'
	);
	
	function __construct(){
		$this->supported_exts = array();
		foreach ($this->supported as $v) {
			foreach ($v as $sv) {
				$this->supported_exts[] = $sv;
			}
		}
		foreach ($this->mediaTypes as $k=>$v) {
			$this->mediaTypes[$k]['exts'] = $this->supported[$k];
		}
		$this->UpdateData();
	}
	
	function GetType($ext) {
		foreach ($this->supported as $k=>$v) {
			foreach ($v as $e) {
				if($e == $ext) return $k;
			}
		}
	}
	
	function ConvertIfNeed(&$file, &$file_name) {
		//Тип
		$exte = explode('.', $file_name);
		$ext = strtolower(end($exte));
		
		//Имя
		$name = implode('.', array_slice($exte,0,-1));
		
		$toType = '';
		$fromType = '';
		foreach ($this->mustConvert as $to=>$formats) {
			foreach ($formats as $type) {
				if($type == $ext) {
					$toType = $to;
					$fromType = $ext;
				}
			}
		}
		
		if(empty($toType) || empty($fromType)) return false;
		
		$temp_file = sys_get_temp_dir().'/mediaFile'.rand(1000,10^7);
		
		chmod($file,'0777');
		
		$from_file = $file.'.'.$fromType;
		copy($file, $from_file);
		
		switch ($toType) {
			case 'flv':
				$temp_file .= '.flv';
				$output = shell_exec('ffmpeg -i '.$from_file.' -f flv '.$temp_file);
			break;
			
			case 'mp3':
				$temp_file .= '.mp3';
				$output = shell_exec('ffmpeg -i '.$from_file.' '.$temp_file);
			break;
			
			default:
				return false;
			break;
		}
		
		if(is_file($temp_file)) {
			$file = $temp_file;
			$file_name = $name.'.'.$toType;
		} else return false;
	}
	
	function AddFile($file, $file_name, $module, $module_id=0) {
		if(is_file($file)) {
			$md5 = md5_file($file);
			if(isset($this->alldata[$module][$module_id][$md5])) return false;
			
			//Конвертирование, если необходимо
			//$this->ConvertIfNeed($file, $file_name);
			
			//Полная информация о медафайле
			$info = $this->GetFileDetails($file, $file_name);
			$sinfo = serialize($info);
			
			//Расширение
			$ext = $info['type'];
			if(!in_array($ext, $this->supported_exts)) return false;
			
			//Имя
			$name = $info['name'];
			
			//Копируем
			$dir = '/data/moduleMediaFiles/'.$module.'/'.$module_id;
			$src = $dir.'/'.$md5.'.'.$ext;
			$dest = DIR.$src;
			if(!is_dir(DIR.$dir)) {
				mkdir(DIR.$dir, 0777, true);
			}
			copy($file, $dest);
			
			$db = db();
			
			$db->query("SELECT MAX(`order`) FROM `prefix_mediafiles` WHERE `module` = '$module' AND `module_id` = $module_id");
			$max_order = $db->fetch();
			$new_order = $max_order[0] + 1;
			
			$db->query("
				INSERT INTO `prefix_mediafiles`
					(`src`, `md5`, `filetype`, `fileinfo`, `name`, `text`, `module`, `module_id`, `order`, `date`)
				VALUES (
					'$src',
					'$md5',
					'$ext',
					'".addcslashes($sinfo,'\'\\')."',
					'".addcslashes($name,'\'\\')."',
					'',
					'$module',
					'$module_id',
					'$new_order',
					NOW()
				)
			");
			$this->UpdateData();
			return true;
		} else {
			return false;
		}
	}
	
	private function GetFileDetails($file, $file_name) {
		//Тип
		$exte = explode('.', $file_name);
		$ext = strtolower(end($exte));
		
		//Имя
		$name = implode('.', array_slice($exte,0,-1));
		
		//Размер
		$size = filesize($file);
		$sizeStr = bytes_to_str($size);
		
		//Информация по разным типам файлов
		$info = array();
		switch ($ext) {
			case 'flv': case 'mp3':
				$info = $this->get_video_size($file);
			break;
			
			default:
				if(!in_array($ext, $this->supported_exts)) break;
				
				//Вроде и все ))
			break;
		}
		
		return array(
			'type'		=> $ext,
			'name'		=> $name,
			'size'		=> $size,
			'sizeStr'	=> $sizeStr,
			'info'		=> $info
		);
	}
	
	/*
	function StarImage($id) {
		$id = (int)$id;
		
		if($id <= 0) return false;
		
		$db = db();
		$db->query("SELECT * FROM `prefix_images` WHERE `id` = $id");
		$i = $db->fetch();
		$db->query("UPDATE `prefix_images` SET `main` = 'N'  WHERE `module` = '$i[module]' AND `module_id` = '$i[module_id]' AND `main` = 'Y'");
		$db->query("UPDATE `prefix_images` SET `main` = 'Y'  WHERE `id` = $id");
		$this->UpdateData();
		return true;
	}
	*/
	
	function DelFile($id) {
		$id = (int)$id;
		
		if($id <= 0) return false;
		
		$db = db();
		$db->query("SELECT * FROM `prefix_mediafiles` WHERE `id` = $id");
		$i = $db->fetch();
		
		$file = DIR.$i['src'];
		if(is_file($file)) {
			if(unlink($file)) {
				$db->query("DELETE FROM `prefix_mediafiles` WHERE `id` = $id");
			}
		} else if(!file_exists($file)) {
			$db->query("DELETE FROM `prefix_mediafiles` WHERE `id` = $id");
		}
		/*
		$db->query("DELETE FROM `prefix_mediafiles` WHERE `id` = $id");
		if($i['main'] == 'Y') {
			$db->query("UPDATE `prefix_mediafiles` SET `main` = 'Y' WHERE `module` = '$i[module]' AND `module_id` = '$i[module_id]' LIMIT 1");
		}
		*/
		$this->UpdateData();
		return true;
	}
	
	/**
	 * Сортировка заданных файлов по заданному массиву
	 *
	 * @param array(file_id, order) $sorting
	 */
	function SortFiles($sorting) {
		$db = db();
		foreach ($sorting as $id=>$sort) {
			$db->query("UPDATE `prefix_mediafiles` SET `order` = '$sort' WHERE `id` = $id");
		}
		$this->UpdateData();
	}
	
	function GetFiles($module, $module_id) {
		if(isset($this->alldata[$module][$module_id])) {
			return $this->alldata[$module][$module_id];
		} else {
			return array();
		}
	}
	
	/*
	function GetMainImage($module, $module_id) {
		if(!isset($this->alldata[$module][$module_id]) || empty($this->alldata[$module][$module_id])) return false;
		
		foreach($this->alldata[$module][$module_id] as $i) {
			if($i['main'] == 'Y') {
				return $i;
			}
		}
		return false;
	}
	*/
	
	private function UpdateData() {
		$db = db();
		$db->query("SELECT * FROM `prefix_mediafiles` ORDER BY `order`");
		$data = array();
		while ($i = $db->fetch()) {
			$type = $this->GetType($i['filetype']);
			$i['fileinfo'] = unserialize($i['fileinfo']);
			if(isset($this->icons[$i['filetype']])) $i['icon'] = '/images/mimetypes/'.$this->icons[$i['filetype']].'.png';
			else $i['icon'] = '/images/mimetypes/empty.png';
			$data[$i['module']][$i['module_id']][$type][$i['md5']] = $i;
		}
		$this->alldata = $data;
	}
	
	
	
	/**
	 * method to get size of the video
	 * @param $videofile path of the video
	 * @returns Array(width, height)
	 */
	private function get_video_size($videofile){
	    //global $ffmpeg_path;
	    $ffmpeg_path = 'ffmpeg';
	    $duration = array();
	    $bitrate = '';
	    $video_bitrate = 0;
	    $vwidth = 320;
	    $vheight = 240;
	    $owidth = 0;
	    $oheight = 0;
	    $sfrequency = 0;
	    $audio_bitrate = 0;
	    
	    $ffmpeg_output = array();
	    $ffmpeg_cmd = $ffmpeg_path . " -i '" . $videofile . '\' 2>&1 | cat | egrep -e \'(Duration|Stream)\'';
	    @exec($ffmpeg_cmd, $ffmpeg_output, $code);
	    
	    // if file not found just return null
	    if(sizeof($ffmpeg_output) == 0)return null;
	    
	    foreach($ffmpeg_output as $line){
	        $ma = array();
	        // get duration and video bitrate
	        if(strpos($line, 'Duration:') !== false){
	            preg_match('/(?P<hours>\d+):(?P<minutes>\d+):(?P<seconds>\d+)\.(?P<fractions>\d+)/', $line, $ma);
	            $duration = array(
	                'raw' => $ma['hours'] . ':' . $ma['minutes'] . ':' . $ma['seconds'] . '.' . $ma['fractions'],
	                'hours' => intval($ma['hours']),
	                'minutes' => intval($ma['minutes']),
	                'seconds' => intval($ma['seconds']),
	                'fractions' => intval($ma['fractions']),
	                'rawSeconds' => intval($ma['hours']) * 60 * 60 +
	                                intval($ma['minutes']) * 60 + intval($ma['seconds']) +
	                                (intval($ma['fractions']) != 0 ? 1 : 0)
	            );
	            
	            preg_match('/bitrate:\s(?P<bitrate>\d+)\skb\/s/', $line, $ma);
	            $bitrate = $ma['bitrate'];
	        }
	        
	        // get video size
	        if(strpos($line, 'Video:') !== false){
	            preg_match('/(?P<width>\d+)x(?P<height>\d+)/i', $line, $ma);
	            
	            $owidth = $ma['width'];
	            $oheight = $ma['height'];
	            $vwidth = $owidth;
	            $vheight = $oheight;
	        }
	        
	        // get audio quality
	        if(strpos($line, 'Audio:') !== false){
	            preg_match('/,\s(?P<sfrequency>\d+)\sHz,/', $line, $ma);
	            $sfrequency = $ma['sfrequency'];
	            
	            preg_match('/,\s(?P<bitrate>\d+)\skb\/s/', $line, $ma);
	            $audio_bitrate = $ma['bitrate'];
	        }
	    }
		
	    // frame size must be a multiple of 2
	    $vwidth = $vwidth % 2 != 0 ? $vwidth - 1 : $vwidth;
	    $vheight = $vheight % 2 != 0 ? $vheight - 1 : $vheight;
	    
	    // end of image size detection
	    // return all information about video and
	    // data about new size with originally proportion
	    return array(
	        'width' => $vwidth,
	        'height' => $vheight,
	        'srcWidth' => $owidth,
	        'srcHeight' => $oheight,
	        'duration' => $duration,
	        'bitrate' => $bitrate,
	        'audioBitrate' => $audio_bitrate,
	        'audioSampleFrequency' => $sfrequency
	    );
	}
	
}