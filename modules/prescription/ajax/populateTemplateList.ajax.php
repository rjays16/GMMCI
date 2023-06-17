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
$template_name = $_REQUEST['name'];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'template_name'=>'name',
	'template_date'=>'create_time'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'template_name';

$filters = array(
	'sort' => $sortMap[$sortName]." ".$sortDir
);
$data = array();
if(is_array($filters))
{
	foreach ($filters as $i=>$v) {
		switch (strtolower($i)) {
			case 'sort': $sort_sql = $v; break;
		}
	}
}

$pres_obj = new SegPrescription();
$result = $pres_obj->listTemplates($template_name, $sort_sql, $offset, $maxRows);
if ($result !== FALSE) {
	$total = $pres_obj->FoundRows();
	while ($row = $result->FetchRow()) {

		$disable=0;
		if($_SESSION['sess_temp_userid']!==$row['owner']){
			$disable=1;
		}

		$data[] = array(
			'template_name'=>ucfirst($row['name']),
			'template_owner'=>ucfirst($row['owner_name']),
			'template_date'=>date('d-M-Y h:i: a', strtotime($row['create_time'])),
			'options'=>
				'<button class="segButton" '.($disable==1?'disabled="disabled"':'onclick="updateTemplate(\''.$row['id'].'\',\''.$row['name'].'\');return false;" style="cursor:pointer"').'><img src="../../gui/img/common/default/application_edit.png"/>Edit</button>&nbsp;'.
				'<button class="segButton" '.($disable==1?'disabled="disabled"':'onclick="deleteTemplate(\''.$row['id'].'\');return false;" style="cursor:pointer"').'><img src="../../gui/img/common/default/delete.png"/>Delete</button>'
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