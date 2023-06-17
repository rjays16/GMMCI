<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require_once $root_path."include/inc_environment_global.php";
require_once $root_path."include/care_api_classes/dashboard/DashletSession.php";
require_once $root_path."classes/json/json.php";

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$sortName = $_REQUEST['sort'];
if (!$sortName)
	$sortName = 'patient';
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'date' => 'date',
	'admission' => 'admission',
	'department' => 'department'
);
if (!$sortMap[$sortName]) $sort = 'patient ASC';
else	$sort = $sortMap[$sortName]." ".$sortDir;

global $db;

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
$file = $session->get('ActivePatientFile');

$query = "SELECT pid FROM care_encounter WHERE encounter_nr=".$db->qstr($file);

$pid = $db->GetOne($query);

$data = Array();
if ($pid)
{
	$query = "SELECT SQL_CALC_FOUND_ROWS e.pid,e.encounter_nr `encounter`,e.encounter_date `date`,\n".
		"t.type `admission`,d.name_formal `department`\n".
		"FROM care_encounter e\n".
			"INNER JOIN care_type_encounter t ON t.type_nr=e.encounter_type\n".
			"INNER JOIN care_department d ON d.nr=e.current_dept_nr\n".
		"WHERE e.pid=".$db->qstr($pid)."\n".
		"ORDER BY $sort\n".
		"LIMIT $offset, $maxRows";

	$db->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $db->Execute($query);

	if ($rs !== false)
	{
		$total = 0;
		$total = $db->GetOne("SELECT FOUND_ROWS()");
		$rows = $rs->GetRows();
		foreach ($rows as $row)
		{
			$data[] = Array(
				'pid' => $row['pid'],
				'date' => nl2br(date("Y-m-d\nh:ia", strtotime($row['date']))),
				'encounter' => $row['encounter'],
				'admission' => strtoupper($row['admission']),
				'department' => $row['department']
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