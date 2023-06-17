<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";
require_once $root_path.'include/care_api_classes/dashboard/DashletSession.php';

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$sortName = $_REQUEST['sort'];
if (!$sortName)
	$sortName = 'r.referral_date';
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'date' => 'r.referral_date',
	'department' => 'department',
	'reason' => 'reason'
);
if (!$sortMap[$sortName]) $sort = 'r.referral_date ASC';
else	$sort = $sortMap[$sortName]." ".$sortDir;

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
$enc = $session->get('ActivePatientFile');

global $db;

$dr_nr = $db->GetOne("SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION['sess_temp_userid']));
if ($dr_nr)
{
	$query = "SELECT SQL_CALC_FOUND_ROWS r.referral_nr, r.referral_date,
			 (SELECT d.name_formal FROM care_department d WHERE d.nr=r.referrer_dept) AS `department`,	
			 (SELECT s.reason FROM seg_referral_reason s WHERE s.id=r.reason_referral_nr) AS `reason` 
			 FROM seg_referral r WHERE encounter_nr='$enc' AND referrer_dr='$dr_nr'\n".
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
			'referral_nr' => $row['referral_nr'],
			'date' => nl2br(date("Y-m-d", strtotime($row['referral_date']))),
			'department' => strtoupper($row['department']),
			'reason' => $row['reason']
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