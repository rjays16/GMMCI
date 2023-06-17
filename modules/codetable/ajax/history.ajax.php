<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";
require "{$root_path}modules/codetable/dynamicfields/class_dynamicfield.php";
$objectName = $_REQUEST['object'] or die('Object type not specified...');
$dynField = new DynamicField();

// get bean
require_once "{$root_path}modules/codetable/beans/bean_{$objectName}.php";
$beanClass = "{$objectName}bean";
$bean = new $beanClass();

// send HTML headers
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';	// sort direction
$sortMap = array(
	'dt' => 'audit_timestamp',
	'act' => 'action',
	'user' => 'login_id'
);
$sortName = $_REQUEST['sort'];
if (!$sortName || !in_array($sortName, $sortMap))
	$sortName = 'dt';
$sort = $sortMap[$sortName]." ".$sortDir;

$data = array();
$bean->setKeyValues($_REQUEST['pk']);
$rows = $bean->getAuditTrail( $offset, $maxRows, $sort, $calc_found_rows = true);
if ($rows !== FALSE) {
	foreach ($rows as $row) {
		$data_item  = array();
		$data_item['dt'] = nl2br(date("Y-m-d\nh:ia",strtotime($row['audit_timestamp'])));
		$data_item['act'] = ucfirst($row['action']);
		$data_item['user'] = $db->GetOne("SELECT name FROM care_users WHERE login_id=".$db->qstr($row['login_id']));
		
		$action = strtolower($row['action']);
		if ($action!='create') {
			$audit_details = $bean->getAuditDetails($row['audit_id']);
			$details = "";
			foreach ($audit_details as $i=>$v) {
				switch ($action) {
					case 'update':
						$details .= "<div><span style=\"color:#000000\">{$i}</span>: <span style=\"color:#c00000\">'".htmlentities($v['before_value'])."'</span> &rarr; <span style=\"color:#00c000\">'".htmlentities($v['after_value'])."'</span></div>";
						break;
					case 'delete': case 'restore':
						$details .= "<div>Reason: <span style=\"color:#000080\">".htmlentities($v['before_value'])."</span></div>";
						break;
				}
			}
			$data_item['details'] = $details;
		}
		else {
			$data_item['details'] = '-';
		}		
		$data[] = $data_item;
	}
}

$response = array(
	'total'=>$bean->foundRows,
	'data'=>$data
 );

$json = new Services_JSON;
print $json->encode($response);