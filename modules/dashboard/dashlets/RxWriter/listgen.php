<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/care_api_classes/dashboard/DashletSession.php';
require_once $root_path.'classes/json/json.php';
require_once $root_path.'include/care_api_classes/prescription/class_prescription_writer.php';

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$sortName = $_REQUEST['sort'];
if (!$sortName)
	$sortName = 'Name';
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'date' => 'rx.create_time',
	'name' => 'patient'
);
if (!$sortMap[$sortName]) $sort = 'date DESC';
else	$sort = $sortMap[$sortName]." ".$sortDir;


global $db;

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
$activePatient = $session->get('ActivePatientFile');


$query = "SELECT SQL_CALC_FOUND_ROWS rx.id,rx.create_time `date`,fn_get_person_name(p.pid) `patient`,\n".
		"e.encounter_nr `encounter`\n".
	"FROM seg_prescription rx\n".
		"INNER JOIN care_encounter e ON e.encounter_nr=rx.encounter_nr\n".
		"INNER JOIN care_person p ON p.pid=e.pid\n".
	"WHERE\n".
		//"rx.create_id=".$db->qstr($_SESSION['sess_temp_userid'])."\n".
		"rx.encounter_nr=".$db->qstr($activePatient)."\n".
		"AND NOT rx.is_deleted\n".
	"ORDER BY $sort\n".
	"LIMIT $offset, $maxRows";


$db->SetFetchMode(ADODB_FETCH_ASSOC);
$rs = $db->Execute($query);

$data = Array();
if ($rs !== false)
{
	$total = $db->GetOne("SELECT FOUND_ROWS()");
	$rows = $rs->GetRows();
	foreach ($rows as $row)
	{
		$data[] = Array(
			'id' => $row['id'],
			'date' => nl2br(date("Y-m-d\nh:ia", strtotime($row['date']))),
			'name' => strtoupper($row['patient']),
			'encounter' => strtoupper($row['encounter'])
		);
	}
}
else
{
	$total = 0;
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

