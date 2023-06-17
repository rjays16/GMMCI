<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";
require_once($root_path.'include/care_api_classes/prescription/class_prescription_writer.php');

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$item_name = $_REQUEST['name'];
$item_code = $_REQUEST['code'];

$pres_obj = new SegPrescription();
$result = $pres_obj->getRecentMeds($item_code, $offset, $maxRows);

$data = array();
if ($result !== FALSE) {
	$total = $pres_obj->FoundRows();

	while ($row = $result->FetchRow()) {

		switch($row['period_interval'])
		{
			case 'D': $interval="Days"; break;
			case 'W': $interval="Weeks"; break;
			case 'M': $interval="Months"; break;
		}

		$data[] = array(
			'drug_name'=>$row['generic']." [".$row['item_name']."]",
			'drug_qty'=>number_format($row['quantity'],0),
			'drug_dosage'=>$row['dosage'],
			'drug_period'=>$row['period_count']." ".$interval,
			'options'=>'<button class="segButton" onclick="addDrug(\''.$row['item_code'].'\',\''.trim($row['item_name']).'\',
				\''.number_format($row['quantity'],0).'\',\''.$row['dosage'].'\',\''.$row['period_count'].'\',\''.$row['period_interval'].'\',
				\''.$row['generic'].'\',\''.$row['availability'].'\');
				return false;"><img src="../../gui/img/common/default/pill_add.png"/>Add</button>'
		);
	}
}

$response = array(
	'currentPage'=>$page,
	'total'=>$total,
	'data'=>$data
 );

/**
* Convert data to JSON and print
*
*/

$json = new Services_JSON;
print $json->encode($response);