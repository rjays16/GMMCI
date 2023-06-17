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

$sortName = $_REQUEST['sort'];
if (!$sortName)
	$sortName = 'patient';
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'date' => 'e.encounter_date',
	'name' => 'patient',
	'confinement' => 't.type'
);
if (!$sortMap[$sortName]) $sort = 'patient ASC';
else	$sort = $sortMap[$sortName]." ".$sortDir;

global $db;

$dr_nr = $db->GetOne("SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION['sess_temp_userid']));
if ($dr_nr)
{
	$query = "SELECT SQL_CALC_FOUND_ROWS e.encounter_nr `encounter`,\n".
			"e.pid,e.encounter_date,t.type,e.encounter_nr,fn_get_person_name(p.pid) `patient`\n".
		"FROM care_encounter e\n".
			"INNER JOIN care_type_encounter t ON t.type_nr=e.encounter_type\n".
			"INNER JOIN care_person p ON p.pid=e.pid\n".
		"WHERE NOT e.is_discharged AND e.status NOT IN ('deleted')\n".
			"AND (current_att_dr_nr=".$db->qstr($dr_nr)." OR consulting_dr_nr=".$db->qstr($dr_nr).")\n".
		"ORDER BY $sort\n".
		"LIMIT $offset, $maxRows";
}


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
			'pid' => $row['pid'],
			'date' => nl2br(date("Y-m-d\nh:ia", strtotime($row['encounter_date']))),
			'name' => strtoupper($row['patient']),
			'encounter' => strtoupper($row['encounter']),
			'confinement' => $row['type']
		);
	}
}
else
{
	$total = 0;
	echo $query;
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