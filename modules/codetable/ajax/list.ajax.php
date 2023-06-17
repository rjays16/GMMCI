<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";
require "{$root_path}modules/codetable/dynamicfields/class_dynamicfield.php";
$objectName = $_REQUEST['object'] or die('Object type not specified...');
$dynField = new DynamicField();
$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE | SERVICES_JSON_SUPPRESS_ERRORS);

// get bean
require_once "{$root_path}modules/codetable/beans/bean_{$objectName}.php";
$beanClass = "{$objectName}bean";
$bean = new $beanClass();

// create a quick reference to the ListView metadata columns definition
require_once "{$root_path}modules/codetable/metadata/{$objectName}/{$objectName}_listview.php";
$listView =& $Views['List'][$objectName];
$columns =& $Views['List'][$objectName]['columns'];

// send HTML headers
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';	// sort direction
$sortObject = $columns[ $_REQUEST['sort'] ]['field'];	// refer to ListView definition for the proper sort field mapping
if ($sortObject) {
	$sort = "{$sortObject} {$sortDir}";
}

if ($_REQUEST['search']) {
	$filters = $json->decode(stripslashes($_REQUEST['filters']));
	$filterDefs = $listView['search'][$_REQUEST['search']]['filters'];

	$where = array();
	foreach ($filters as $key=>$filter) {
		$filterDef = $filterDefs[$key];
		$fieldDef = $bean->dictionary['fields'][$filterDef['field']];
		$field =& $dynField->getField( $fieldDef['type'], $bean->emptyValue, $fieldDef['metaOptions'] );
		$filter['field'] = $filterDef['field'];
		$where[$filterDef['field']] = $field->getFilters($filter);
	}
}

$data = array();
$mapping = array();

// get `field` value of the LitView definiton ...
foreach ($columns as $i=>$v) {
	$mapping[$i] = $v['field'];
}
// ...and flip to get reverse mapping from db field to ListView defintion entry
$mapping = array_flip($mapping);

// get bean instance as pecified in loaded Bean
$rows = $bean->getListViewRows( $columns, $where, $offset, $maxRows, $sort, $calc_found_rows = true);
if ($rows !== FALSE) {
	foreach ($rows as $row) {
		$pk = array();
		$data_item  = array();
		foreach ($row as $i=>$v) {
			// check if the current field is part of the primary Key
			if (in_array($i, $bean->dictionary['primaryKeys'])) {
				$pk[] = $v;
			}

			$mapIndex = $mapping[$i];

			// transform data here
			$fieldDef = $bean->dictionary['fields'][$i];
			$field =& $dynField->getField( $fieldDef['type'], $v, $fieldDef['metaOptions'] );
			$field->value = $v;

			// get ListView options
			$v = $field->getListView($columns[$mapIndex]['displayParams']);

			// map result field to ListView definition
			$data_item[ $mapIndex ] = $v;
		}
		if ($bean->deleteFlag) {
			$data_item['_deleted'] = $row['_deleted'];
		}
		else {
			$data_item['_deleted'] = '';
		}
		$data_item['PK'] = $pk;
		$data[] = $data_item;
	}
}

$response = array(
	'total'=>$bean->foundRows,
	'data'=>$data
 );

print $json->encode($response);