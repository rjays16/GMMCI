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
	$sortName = 'code';
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'code' => 'icd_code',
);
if (!$sortMap[$sortName]) $sort = 'icd_code ASC';
else	$sort = $sortMap[$sortName]." ".$sortDir;

global $db;

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
$encounter_nr = $session->get('ActivePatientFile');
$sql = "SELECT personell_nr FROM care_users WHERE login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
$dr_nr = $db->GetOne($sql);

if($dr_nr) {
	$query = "SELECT SQL_CALC_FOUND_ROWS dr.icd_code, icd.description\n".
									"FROM seg_doctors_diagnosis AS dr\n".
									"INNER JOIN care_icd10_en AS icd ON dr.icd_code=icd.diagnosis_code\n".
									"WHERE dr.encounter_nr=".$db->qstr($encounter_nr)." \n".
									"AND personell_nr=".$db->qstr($dr_nr)."\n".
									"ORDER BY $sort \n".
									"LIMIT $offset, $maxRows";
		/*echo "<pre>";
		print_r($query);
		echo "</pre>";*/
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
			'code' => $row["icd_code"],
			'description' => $row["description"]
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