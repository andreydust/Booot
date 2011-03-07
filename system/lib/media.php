<?php


function image($src, $width, $height, $format='png') {
	$new_file_src = '/data/thumbs'.$src.$width.'x'.$height.'.'.$format;
	$new_file = DIR.$new_file_src;
	
	if(is_file($new_file)) return $new_file_src;
	
	$file = DIR.$src;
	if(is_file($file)) {
		$new_file_path_parts = pathinfo($new_file);
		if(!is_dir($new_file_path_parts['dirname'])) {
			mkdir($new_file_path_parts['dirname'], 0777, true);
		}
		system('convert '.$file.' -resize '.$width.'x'.$height.'\> '.$new_file);
		
		//Первый кадр анимированного гифа
		if(!is_file($new_file)) {
			$check_anim = substr($new_file, 0, -(strlen($format)+1)).'-0.'.$format;
			if(is_file($check_anim)) {
				rename($check_anim, $new_file);
			} else {
				return image('/data/jnb.jpg', $width, $height);
			}
		}
		
		return $new_file_src;
	} else {
		return image('/data/jnb.jpg', $width, $height);
	}
}

function imageLandscape($src) {
	list($width, $height) = getimagesize(DIR.$src);
	if($width>0 && $height>0) {
		if($width > $height) return true;
	}
	return false;
}


function img() {
	require_once DIR.'/admin/lib/Images.php';
	return giveObject('Images');
}

function files() {
	require_once DIR.'/admin/lib/MediaFiles.php';
	return giveObject('MediaFiles');
}


