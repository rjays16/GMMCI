<?php
/**
* icd10List.php
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

/**
* Data fetching goes here ....
*
*/
global $db;
$term = $_REQUEST['term'];
$sql = "SELECT SQL_CALC_FOUND_ROWS diagnosis_code, description \n".
"FROM care_icd10_en\n".
"WHERE diagnosis_code='$term' OR description REGEXP '[[:<:]]$term' \n".
"ORDER BY description ASC LIMIT 0, 10 ";

/*echo "<pre>";
		print_r($sql);
		echo "</pre>";*/

$db->SetFetchMode(ADODB_FETCH_ASSOC);
$rs = $db->Execute($sql);

$data = Array();
if ($rs !== false)
{
	$total = 0;
	$total = $db->GetOne("SELECT FOUND_ROWS()");
	$rows = $rs->GetRows();
	foreach ($rows as $row)
	{
		$data[] = Array(
			'icd_code' => $row["diagnosis_code"],
			'description' => $row["description"]
		);
	}
}
else
{
	$total = 0;
	echo $query;
}

/**
* Convert data to JSON and print
*
*/

$json = new Services_JSON;
print $json->encode($data);