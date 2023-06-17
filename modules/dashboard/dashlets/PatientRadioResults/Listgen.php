<?php
/**
* ListGen.php
*
*
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require_once $root_path."include/inc_environment_global.php";
require_once $root_path."include/care_api_classes/dashboard/DashletSession.php";
require_once $root_path."classes/json/json.php";

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$sortName = $_REQUEST['sort'];
if (!$sortName)
	$sortName = 'date';
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	//'date' => 'r.request_date, r.request_time',
	'date' => 'request_date',
);
//if (!$sortMap[$sortName]) $sort = 'request_date, request_time DESC';
if (!$sortMap[$sortName]) $sort = 'request_date DESC';
else	$sort = $sortMap[$sortName]." ".$sortDir;

global $db;

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
$encounter_nr = $session->get('ActivePatientFile');

$query = "SELECT pid FROM care_encounter WHERE encounter_nr=".$db->qstr($encounter_nr);

$pid = $db->GetOne($query);
$data = Array();
if($pid) {
	/*query updated by mai 08-13-2014*/
	$query = "SELECT 
				  SQL_CALC_FOUND_ROWS f.batch_nr AS refno,
				  r.request_flag,
				  s.ordername AS patient_name,
				  rs.name AS services,
				  s.`encounter_nr`,
				  s.`is_urgent`,
				  r.`service_date`,
				  CONCAT(
				    s.request_date,
				    ' ',
				    s.request_time
				  ) AS `request_date`,
				  CONCAT(
				    s.request_date,
				    ' ',
				    s.request_time
				  ) AS `date_request`,
				  s.pid 
				FROM
				  care_test_findings_radio f 
				  LEFT JOIN care_test_request_radio r 
				    ON r.batch_nr = f.batch_nr 
				  LEFT JOIN seg_radio_serv s 
				    ON r.refno = s.refno 
				  LEFT JOIN seg_radio_services rs 
    				ON rs.service_code = r.service_code 
				WHERE r.status NOT IN (
				    'deleted',
				    'hidden',
				    'inactive',
				    'void'
				  ) 
				  AND s.status NOT IN (
				    'deleted',
				    'hidden',
				    'inactive',
				    'void'
				  ) 
				  AND s.`encounter_nr` = $db->qstr($encounter_nr)  
				ORDER BY UNIX_TIMESTAMP(date_request) DESC 
				LIMIT $offset, $maxRows ";

	$db->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $db->Execute($query);

	$data = Array();
	if ($rs !== false)
	{
		$total = 0;
		$total = $db->GetOne("SELECT FOUND_ROWS()");
		$rows = $rs->GetRows();
		foreach ($rows as $row)
		{
			$data[] = Array(
				'date' => nl2br(date("M-d-Y\nh:ia", strtotime($row["request_date"]." ".$row["request_time"]))),
				'service' => strtoupper($row['services']) ,
				'refno' => $row["refno"],
				'pid' => $row['pid'],
				'patient_name'=>$row['patient_name']
			);
		}
	}
}

if (!$data)
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