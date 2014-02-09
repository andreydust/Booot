<?php
class Images {
	
	private $alldata, $db;
	
	private $allowed_exts = array('gif','jpg','jpeg','png','tif','tiff');
	
	function AddImage($file, $module, $module_id=0, $file_name='', $is_main=false) {
		if(is_file($file)) {
			
			//Альтернативный ключ (не по integer id)
			if(!empty($module_id) && !is_numeric($module_id)) {
				if (preg_match('%[\\\\/:*?<>|]+%', $module_id)) {
					return false;
				} else {
					$sql_select = "`alter_key` = '$module_id'";
					$sql_update = "`alter_key` = '$module_id'";
					$sql_insert = "`alter_key`";
				}
			} else {
				$sql_select = "`module_id` = '$module_id'";
				$sql_update = "`module_id` = '$module_id'";
				$sql_insert = "`module_id`";
			}
			
			$md5 = md5_file($file);
			if(isset($this->alldata[$module][$module_id][$md5])) return false;
			$ext = '';
			if(!empty($file_name)) {
				$ext = explode('.', $file_name);
				$ext = end($ext);
				$ext = strtolower($ext);
			}
			
			//Проверка расширения
			if(!in_array($ext, $this->allowed_exts)) return false;
			
			$dir = '/data/moduleImages/'.$module.'/'.$module_id;
			$src = $dir.'/'.$md5.'.'.$ext;
			$dest = DIR.$src;
			if(!is_dir(DIR.$dir)) {
				mkdir(DIR.$dir, 0777, true);
			}
			copy($file, $dest);
			
			$db = db();
			
			$db->query("SELECT * FROM `prefix_images` WHERE `module` = '$module' AND $sql_select AND `main` = 'Y'");
			if($db->row_count() == 0) $is_main = true;
			
			if($is_main) {
				db()->query("UPDATE `prefix_images` SET `main` = 'N'  WHERE `module` = '$module' AND $sql_update AND `main` = 'Y'");
			}
			$db->query("
				INSERT INTO `prefix_images`
					(`src`, `md5`, `module`, $sql_insert, `main`)
				VALUES (
					'$src',
					'$md5',
					'$module',
					'$module_id',
					'".($is_main?'Y':'N')."'
				)
			");
			return $db->last_insert_id();
		} else {
			return false;
		}
	}
	
	function StarImage($id) {
		$id = (int)$id;
		$img = $this->GetImage($id);
		if (!$img)
			return false;
		db()->query("UPDATE prefix_images SET main = 'N' WHERE module = '{$img['module']}' AND ((module_id={$img['module_id']} AND module_id != 0) OR (alter_key='{$img['alter_key']}' AND alter_key != ''))");
		db()->query("UPDATE prefix_images SET main = 'Y' WHERE id = {$id}");

		return true;
	}
	
	function DelImage($id) {
		$id = (int)$id;
		$img = $this->GetImage($id);
		if (!$img)
			return false;
		db()->query("DELETE FROM prefix_images WHERE id = {$id}");
		if ($img['main'] == 'Y')
			db()->query("UPDATE prefix_images SET main='Y' WHERE module='{$img['module']}' AND ((module_id = {$img['module_id']} AND module_id != 0) OR (alter_key = '{$img['alter_key']}' AND alter_key != '')) LIMIT 1");
		return true;
	}

	function DelImages($module, $module_id) {
		db()->query("DELETE FROM prefix_images WHERE module='{$module}' AND ((module_id = '{$module_id}' AND module_id != 0) OR (alter_key = '{$module_id}' AND alter_key != ''))");
	}

	function GetImage($id)
	{
		$id = (int)$id;
		return db()->query_first("SELECT * FROM prefix_images WHERE id = {$id}");
	}
	
	function GetImages($module, $module_id) {
		return db()->rows("SELECT * FROM prefix_images WHERE module = '{$module}' AND ((module_id = '{$module_id}' AND module_id != 0) OR (alter_key = '{$module_id}' AND alter_key != '')) ORDER BY `main`");
	}
	
	function GetMainImage($module, $module_id) {
		if(isset($this->preparedImgs[$module][$module_id])) {
			if(!$this->preparedImgs[$module][$module_id]) return false;
			return current($this->preparedImgs[$module][$module_id]);
		}
		return db()->query_first("SELECT * FROM prefix_images WHERE module = '{$module}' AND ((module_id = '{$module_id}' AND module_id != 0) OR (alter_key = '{$module_id}' AND alter_key != '')) AND main = 'Y' LIMIT 1");
	}
	
	function PrepareImages($module, $module_ids, $only_main=true) {
		if(empty($module_ids)) return false;
		$only_main = $only_main?" AND `main` = 'Y'":'';
		$pi = db()->rows("SELECT * FROM prefix_images WHERE module = '{$module}' AND (module_id IN (".implode(',', $module_ids).") OR alter_key IN ('".implode("','", $module_ids)."')) $only_main");
		foreach ($pi as $p) $this->preparedImgs[$module][$p['module_id']][] = $p;
		foreach ($module_ids as $mids) {
			if(!isset($this->preparedImgs[$module][$mids])) {
				$this->preparedImgs[$module][$mids] = false;
			}
		}
	}
}
