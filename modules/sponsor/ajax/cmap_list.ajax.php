<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require_once $root_path."include/inc_environment_global.php";
require_once $root_path."classes/json/json.php";

global $db;

//header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
//header("Cache-Control: no-cache, must-revalidate" );
//header("Pragma: no-cache" );
//header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

//$check = $_REQUEST["check"];
//$pid = $_REQUEST["pid"];
//$encounter_nr = $_REQUEST["encounter_nr"];
//$name = $_REQUEST["name"];
//$status = $_REQUEST["status"];


$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'date' => 'date',
	'controlNo' => 'controlNo',
	'fullName' => 'fullName'
);

$sortName = $_REQUEST['sort'];
if (!$sortName || !array_key_exists($sortName, $sortMap))
	$sortName = 'date';

$sortSql = $sortMap[$sortName]." ".$sortDir;

//$filters = array(
//	'sort' => $sortMap[$sortName]." ".$sortDir
//);
$data = array();
//$phFilters = array();
//if(is_array($filters))
//{
//	foreach ($filters as $i=>$v) {
//		switch (strtolower($i)) {
//			case 'sort': $sort_sql = $v; break;
//		}
//	}
//}


//$sql = "SELECT SQL_CALC_FOUND_ROWS dt.refno, dt.transaction_date, dt.pid, dt.dialysis_type, dt.status, dt.encounter_nr, \n".
//			"fn_get_person_name(dt.pid) as `patient_name`, ce.is_discharged, \n".
//			"EXISTS(SELECT bill_nr FROM seg_billing_encounter AS b WHERE b.encounter_nr=dt.encounter_nr) AS `is_billed`\n".
//			"FROM seg_dialysis_transaction AS dt \n".
//			"LEFT JOIN care_encounter AS ce ON dt.encounter_nr=ce.encounter_nr \n".
//			"LEFT JOIN care_person AS cp ON dt.pid=cp.pid \n".
			//"WHERE ce.is_discharged!='1'";
//			" WHERE dt.is_deleted!='1' ";

require_once $root_path."include/care_api_classes/sponsor/class_request.php";

if ($_REQUEST['type'] == 'all')
{
	$request_types = array_keys(SegRequest::getRequestTypes());
}
else
{
	$request_types = array((int)$_REQUEST['type']);
}

//require_once $root_path.'include/care_api_classes/class_core.php';

//$core = new Core;
//$core->setTable('');


require_once $root_path."include/care_api_classes/sponsor/helpers/QueryBuilder.php";

$blueprints = array();

/**
* QueryBuilder blueprint for Lingap Billing entry
*/
if (in_array(SegRequest::BILLING_REQUEST, $request_types))
{
	$blueprints[SegRequest::BILLING_REQUEST] = Array(
		"coreTable" => "seg_cmap_entries_bill c",
		"joins" => Array(
			"INNER JOIN seg_cmap_referrals r ON r.id=c.referral_id",
			"INNER JOIN seg_cmap_accounts a ON a.account_nr=r.cmap_account",
			"INNER JOIN care_person p ON p.pid=c.pid"
		),
		"fields" => Array(
			"id"					=> "c.id",
			"costCenter" 	=> SegRequest::BILLING_REQUEST,
			"date" 				=> "c.create_time",
			"pid" 				=> "c.pid",
			"referralId" 	=> "c.referral_id",
			"controlNr" 	=> "r.control_nr",
			"cmapAccount" => "a.account_name",
			"fullName" 		=> "fn_get_person_name(c.pid)",
			"firstName" 	=> "p.name_first",
			"lastName" 		=> "p.name_last",
			"itemName" 		=> "'Hospital bill'",
			"itemGroup" 	=> "''",
			"refNo"				=> "c.ref_no",
			"itemCode"		=> "NULL",
			"amount" 			=> "c.amount",
			"encoder" 		=> "c.create_id"
		)
	);
}

/**
* QueryBuilder blueprint for Lingap Pharmacy (walkin) entry
*/
if (in_array(SegRequest::PHARMACY_REQUEST, $request_types))
{
	$blueprints[SegRequest::PHARMACY_REQUEST] = Array(
		"coreTable" => "seg_cmap_entries_pharmacy c",
		"joins" => Array(
			"INNER JOIN seg_cmap_referrals r ON r.id=c.referral_id",
			"INNER JOIN seg_cmap_accounts a ON a.account_nr=r.cmap_account",
			"INNER JOIN care_person p ON p.pid=c.pid",
			"INNER JOIN care_pharma_products_main prod ON prod.bestellnum=c.service_code"
		),
		"fields" => Array(
			"id"					=> "c.id",
			"costCenter" 	=> SegRequest::PHARMACY_REQUEST,
			"date" 				=> "c.create_time",
			"pid" 				=> "c.pid",
			"referralId" 	=> "c.referral_id",
			"controlNr" 	=> "r.control_nr",
			"cmapAccount" => "a.account_name",
			"fullName" 		=> "fn_get_person_name(c.pid)",
			"firstName" 	=> "p.name_first",
			"lastName" 		=> "p.name_last",
			"itemName" 		=> "prod.artikelname",
			"itemGroup" 	=> "IF(prod.generic,prod.generic,'Supplies')",
			"refNo"				=> "c.ref_no",
			"itemCode"		=> "c.service_code",
			"amount" 			=> "c.amount",
			"encoder" 		=> "c.create_id"
		)
	);
}

/**
* QueryBuilder blueprint for Lingap Billing entry
*/
if (in_array(SegRequest::PHARMACY_WALKIN_REQUEST, $request_types))
{
	// No walk-in requests for CMAP
}



/**
* QueryBuilder blueprint for Lingap Laboratory entry
*/
if (in_array(SegRequest::LABORATORY_REQUEST, $request_types))
{
	$blueprints[SegRequest::LABORATORY_REQUEST] = Array(
		"coreTable" => "seg_cmap_entries_laboratory c",
		"joins" => Array(
			"INNER JOIN seg_cmap_referrals r ON r.id=c.referral_id",
			"INNER JOIN seg_cmap_accounts a ON a.account_nr=r.cmap_account",
			"INNER JOIN care_person p ON p.pid=c.pid",
			"INNER JOIN seg_lab_services s ON s.service_code=c.service_code",
			"INNER JOIN seg_lab_service_groups g ON g.group_code=s.group_code"
		),
		"fields" => Array(
			"id"					=> "c.id",
			"costCenter" 	=> SegRequest::LABORATORY_REQUEST,
			"date" 				=> "c.create_time",
			"pid" 				=> "c.pid",
			"referralId" 	=> "c.referral_id",
			"controlNr" 	=> "r.control_nr",
			"cmapAccount" => "a.account_name",
			"fullName" 		=> "fn_get_person_name(c.pid)",
			"firstName" 	=> "p.name_first",
			"lastName" 		=> "p.name_last",
			"itemName" 		=> "s.name",
			"itemGroup" 	=> "g.name",
			"refNo"				=> "c.ref_no",
			"itemCode"		=> "c.service_code",
			"amount" 			=> "c.amount",
			"encoder" 		=> "c.create_id"
		)

	);
}



/**
* QueryBuilder blueprint for Lingap Radiology entry
*/
if (in_array(SegRequest::RADIOLOGY_REQUEST, $request_types))
{
	$blueprints[SegRequest::RADIOLOGY_REQUEST] = Array(
		"coreTable" => "seg_cmap_entries_radiology c",
		"joins" => Array(
			"INNER JOIN seg_cmap_referrals r ON r.id=c.referral_id",
			"INNER JOIN seg_cmap_accounts a ON a.account_nr=r.cmap_account",
			"INNER JOIN care_person p ON p.pid=c.pid",
			"INNER JOIN seg_radio_services s ON s.service_code=c.service_code",
			"INNER JOIN seg_radio_service_groups g ON g.group_code=s.group_code"
		),
		"fields" => Array(
			"id"					=> "c.id",
			"costCenter" 	=> SegRequest::RADIOLOGY_REQUEST,
			"date" 				=> "c.create_time",
			"pid" 				=> "c.pid",
			"referralId" 	=> "c.referral_id",
			"controlNr" 	=> "r.control_nr",
			"cmapAccount" => "a.account_name",
			"fullName" 		=> "fn_get_person_name(c.pid)",
			"firstName" 	=> "p.name_first",
			"lastName" 		=> "p.name_last",
			"itemName" 		=> "s.name",
			"itemGroup" 	=> "g.name",
			"refNo"				=> "c.ref_no",
			"itemCode"		=> "c.service_code",
			"amount" 			=> "c.amount",
			"encoder" 		=> "c.create_id"
		)
	);
}


/**
* QueryBuilder blueprint for Lingap Misc entry
*/
if (in_array(SegRequest::MISC_REQUEST, $request_types))
{
	$blueprints[SegRequest::MISC_REQUEST] = Array(
		"coreTable" => "seg_cmap_entries_misc c",
		"joins" => Array(
			"INNER JOIN seg_cmap_referrals r ON r.id=c.referral_id",
			"INNER JOIN seg_cmap_accounts a ON a.account_nr=r.cmap_account",
			"INNER JOIN care_person p ON p.pid=c.pid",
			"INNER JOIN seg_other_services s ON s.alt_service_code=c.service_code",
			"INNER JOIN seg_cashier_account_subtypes t ON s.account_type=t.type_id"
		),
		"fields" => Array(
			"id"					=> "c.id",
			"costCenter" 	=> SegRequest::MISC_REQUEST,
			"date" 				=> "c.create_time",
			"pid" 				=> "c.pid",
			"referralId" 	=> "c.referral_id",
			"controlNr" 	=> "r.control_nr",
			"cmapAccount" => "a.account_name",
			"fullName" 		=> "fn_get_person_name(c.pid)",
			"firstName" 	=> "p.name_first",
			"lastName" 		=> "p.name_last",
			"itemName" 		=> "s.name",
			"itemGroup" 	=> "t.name_long",
			"refNo"				=> "c.ref_no",
			"itemCode"		=> "c.service_code",
			"amount" 			=> "c.amount",
			"encoder" 		=> "c.create_id"
		)
	);
}


if (is_numeric($_REQUEST['name']))
{
	$pid = $_REQUEST['name'];
}
else
{
	$name= array();
	if (strpos($_REQUEST['name'], ',') !== false)
	{
		$name = explode(",", $_REQUEST['name']);
		$name[0] = trim($name[0]);
		$name[1] = trim($name[1]);
	}
	else
	{
		$name[0] = $_REQUEST['name'];
	}
}

$queries = array();
foreach ($request_types as $request_type)
{
	$blueprint = $blueprints[$request_type];
	if ($blueprint)
	{
		if ($pid)
		{
			$blueprint['where'][] = Array('EQ', 'pid', $pid);
		}

		if ($name[0])
		{
			$blueprint['where'][] = Array('LIKE', 'lastName', $name[0]."%");
		}

		if ($name[1])
		{
			$blueprint['where'][] = Array('LIKE', 'firstName', $name[1]."%");
		}
		$queries[] = QueryBuilder::build($blueprint, $cfr=false);
		if (!$cfr) $cfr = true; // Do not show SQL_CALC_FOUND_ROWS for the rest of the built queries
	}
}

$query = "SELECT SQL_CALC_FOUND_ROWS tt.* FROM ((\n";

$query .= implode(")\nUNION ALL\n(\n", $queries);

$query .= ")) tt\n";

$query .= "ORDER BY $sortSql\n";

$query .= "LIMIT $offset,$maxRows";



$result = $db->Execute($query);


//die($query);
/*echo "<pre>";
print_r($_REQUEST);
echo "</pre>";*/

if ($result !== FALSE) {

	$total = $db->GetOne("SELECT FOUND_ROWS()");

	$typeNames = SegRequest::getRequestTypes();

	while ($row = $result->FetchRow()) {

		$request = new SegRequest($row['costCenter'], Array("refNo"=>$row['refNo'], "itemNo"=>$row['itemCode']));
		$requestRow = $request->fetch(Array("isServed"));

		//echo "<pre>".print_r($request->getQuery())."</pre>";
		$served = $requestRow['isServed']==="1" ? 1 : 0;

		$data[] = array(
			'id' => $row['id'],
			'date' => date("d-M-Y h:i: a", strtotime($row["date"])),
			'cmapAccount' => htmlentities($row['cmapAccount']),
			'referralId' => htmlentities($row['referralId']),
			'controlNr' => htmlentities($row['controlNr']),
			'pid' => $row['pid'],
			'fullName' => htmlentities(strtoupper($row['fullName'])),
			'source' => $row['costCenter'],
			'costCenter' => $typeNames[$row['costCenter']],
			'itemName' => htmlentities($row['itemName']),
			'itemGroup' => htmlentities($row['itemGroup']),
			'refNo' => $row['refNo'],
			'itemCode' => $row['itemCode'],
			//'encoder' => $row['encoder'],
			'amount' => number_format($row['amount'], 2),
			'served' => $served,
			'options' => ''
		);
	}
}

$response = array(
	'currentPage'=>$page,
	'total'=>$total,
	'data'=>$data
 );

$json = new Services_JSON;
print $json->encode($response);