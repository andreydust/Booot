<?php

function admGetContentTable($table = 'content') {
	
	$siteDocuments = array();
	
	$db = db();
	
	$db->query("SELECT * FROM `prefix_$table` WHERE `deleted` = 'N' AND `show` = 'Y' ORDER BY `top`,`order`");
	while ($i = $db->fetch()) {
		$siteDocuments[$i['id']] = $i;
	}
	
	$siteDocumentsByModule = array();
	foreach($siteDocuments as $i) {
		$siteDocumentsByParent[$i['top']][$i['id']] = $i;
		if(isset($i['module'])) {
			if(!isset($siteDocumentsByModule[$i['module']])) {
				$siteDocumentsByModule[$i['module']] = $i;
			}
		}
	}
	
	return array(
		$siteDocuments,
		$siteDocumentsByParent,
		$siteDocumentsByModule
	);
}

function admLinkById($id, $table = 'content') {
	list($siteDocuments, $siteDocumentsByParent, $siteDocumentsByModule) = admGetContentTable($table);
	
	$admLinkById_ = function($id, $siteDocuments, $siteDocumentsByParent, $siteDocumentsByModule, $admLinkById_) {
		if($id==0) return;
		$link = $admLinkById_($siteDocuments[$id]['top'], $siteDocuments, $siteDocumentsByParent, $siteDocumentsByModule, $admLinkById_).'/'.$siteDocuments[$id]['nav'];
		return $link;
	};
	
	return $admLinkById_($id, $siteDocuments, $siteDocumentsByParent, $siteDocumentsByModule, $admLinkById_);
}


function admLinkByModule($module, $table = 'content') {
	list($siteDocuments, $siteDocumentsByParent, $siteDocumentsByModule) = admGetContentTable($table);
	
	return admLinkById($siteDocumentsByModule[$module]['id']);
}


$modulesSettings = array();
function admGetSet($module, $callname, $default='') {
	global $modulesSettings;
	if(empty($modulesSettings)) {
		$sets = db()->rows("SELECT * FROM `prefix_settings`", MYSQL_ASSOC);
		foreach ($sets as $k=>$v) {
			$modulesSettings[$v['module']][$v['callname']] = $v;
		}
	}
	
	if(isset($modulesSettings[$module][$callname])) return $modulesSettings[$module][$callname]['value'];
	else return $default;
}