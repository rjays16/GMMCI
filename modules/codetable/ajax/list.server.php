<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/codetable/ajax/list.common.php');

function delete($objectName, $pK, $comments='') {
	global $root_path;
	$objResponse = new xajaxResponse();
	
	// get bean
	require_once "{$root_path}modules/codetable/beans/bean_{$objectName}.php";
	$beanClass = "{$objectName}bean";
	$bean = new $beanClass();

	if ($pK) {
		$bean->setKeyValues($pK);
	}
	
	if ($bean->delete($comments)) {
		$objResponse->call('CodeTable.info','Info:','Item successfully deleted...');
		$objResponse->script("$('listview').list.refresh()");	
	}
	else {
		$objResponse->call('CodeTable.alert','Error:','Error occurred while trying to delete item...');
	}
	return $objResponse;
}


function restore($objectName, $pK, $comments='') {
	global $root_path;
	$objResponse = new xajaxResponse();
	
	// get bean
	require_once "{$root_path}modules/codetable/beans/bean_{$objectName}.php";
	$beanClass = "{$objectName}bean";
	$bean = new $beanClass();

	if ($pK) {
		$bean->setKeyValues($pK);
	}
	
	if ($bean->restore($comments)) {
		$objResponse->call('CodeTable.info','Info:','Item successfully restored...');
		$objResponse->script("$('listview').list.refresh()");	
	}
	else {
		$objResponse->call('CodeTable.alert','Error:','Error occurred while trying to restore deleted item...');
	}
	return $objResponse;
}

$xajax->processRequest();