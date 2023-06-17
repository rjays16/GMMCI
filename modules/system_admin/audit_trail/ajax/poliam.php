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
//    'sort' => $sortMap[$sortName]." ".$sortDir
//);
$data = array();
//$phFilters = array();
//if(is_array($filters))
//{
//    foreach ($filters as $i=>$v) {
//        switch (strtolower($i)) {
//            case 'sort': $sort_sql = $v; break;
//        }
//    }
//}


//$sql = "SELECT SQL_CALC_FOUND_ROWS dt.refno, dt.transaction_date, dt.pid, dt.dialysis_type, dt.status, dt.encounter_nr, \n".
//            "fn_get_person_name(dt.pid) as `patient_name`, ce.is_discharged, \n".
//            "EXISTS(SELECT bill_nr FROM seg_billing_encounter AS b WHERE b.encounter_nr=dt.encounter_nr) AS `is_billed`\n".
//            "FROM seg_dialysis_transaction AS dt \n".
//            "LEFT JOIN care_encounter AS ce ON dt.encounter_nr=ce.encounter_nr \n".
//            "LEFT JOIN care_person AS cp ON dt.pid=cp.pid \n".
            //"WHERE ce.is_discharged!='1'";
//            " WHERE dt.is_deleted!='1' ";

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
        "coreTable" => "seg_lingap_entries_bill ld",
        "joins" => Array(
            "INNER JOIN seg_lingap_entries l ON l.id=ld.entry_id",
            "INNER JOIN care_person p ON p.pid=l.pid"
        ),
        "fields" => Array(
            "id"                    => "l.id",
            "costCenter"     => SegRequest::BILLING_REQUEST,
            "date"                 => "l.entry_date",
            "pid"                 => "l.pid",
            "controlNo"     => "l.control_nr",
            "ssNo"                 => "l.ss_nr",
            "isAdvance"     => "l.is_advance",
            "fullName"         => "fn_get_person_name(l.pid)",
            "firstName"     => "p.name_first",
            "lastName"         => "p.name_last",
            "itemName"         => "'Hospital bill'",
            "itemGroup"     => "''",
            "refNo"                => "ld.ref_no",
            "itemCode"        => "NULL",
            "amount"             => "ld.amount",
            "encoder"         => "l.create_id"
        )
    );
}

/**
* QueryBuilder blueprint for Lingap Pharmacy (walkin) entry
*/
if (in_array(SegRequest::PHARMACY_REQUEST, $request_types))
{
    $blueprints[SegRequest::PHARMACY_REQUEST] = Array(
        "coreTable" => "seg_lingap_entries_pharmacy ld",
        "joins" => Array(
            "INNER JOIN seg_lingap_entries l ON l.id=ld.entry_id",
            "INNER JOIN care_person p ON p.pid=l.pid",
            "INNER JOIN care_pharma_products_main prod ON prod.bestellnum=ld.service_code"
        ),
        "fields" => Array(
            "id"                    => "l.id",
            "costCenter"     => SegRequest::PHARMACY_REQUEST,
            "date"                 => "l.entry_date",
            "pid"                 => "l.pid",
            "controlNo"     => "l.control_nr",
            "ssNo"                 => "l.ss_nr",
            "isAdvance"     => "l.is_advance",
            "fullName"         => "fn_get_person_name(l.pid)",
            "firstName"     => "p.name_first",
            "lastName"         => "p.name_last",
            "itemName"         => "prod.artikelname",
            "itemGroup"     => "IF(prod.generic,prod.generic,'Supplies')",
            "refNo"                => "ld.ref_no",
            "itemCode"        => "ld.service_code",
            "amount"             => "ld.amount",
            "encoder"         => "l.create_id"
        )
    );
}

/**
* QueryBuilder blueprint for Lingap Billing entry
*/
if (in_array(SegRequest::PHARMACY_WALKIN_REQUEST, $request_types))
{
    $blueprints[SegRequest::PHARMACY_WALKIN_REQUEST] = Array(
        "coreTable" => "seg_lingap_entries_pharmacy_walkin ld",
        "joins" => Array(
            "INNER JOIN seg_lingap_entries l ON l.id=ld.entry_id",
            "INNER JOIN seg_walkin p ON p.pid=l.walkin_pid",
            "INNER JOIN care_pharma_products_main prod ON prod.bestellnum=ld.service_code"
        ),
        "fields" => Array(
            "id"                    => "l.id",
            "costCenter"     => SegRequest::PHARMACY_WALKIN_REQUEST,
            "date"                 => "l.entry_date",
            "pid"                 => "l.pid",
            "controlNo"     => "l.control_nr",
            "ssNo"                 => "'Walk-in'",
            "isAdvance"     => "l.is_advance",
            "fullName"         => "fn_get_walkin_name(l.walkin_pid)",
            "firstName"     => "p.name_first",
            "lastName"         => "p.name_last",
            "itemName"         => "prod.artikelname",
            "itemGroup"     => "IF(prod.generic,prod.generic,'Supplies')",
            "refNo"                => "ld.ref_no",
            "itemCode"        => "ld.service_code",
            "amount"             => "ld.amount",
            "encoder"         => "l.create_id"
        )

    );
}



/**
* QueryBuilder blueprint for Lingap Laboratory entry
*/
if (in_array(SegRequest::LABORATORY_REQUEST, $request_types))
{
    $blueprints[SegRequest::LABORATORY_REQUEST] = Array(
        "coreTable" => "seg_lingap_entries_laboratory ld",
        "joins" => Array(
            "INNER JOIN seg_lingap_entries l ON l.id=ld.entry_id",
            "INNER JOIN care_person p ON p.pid=l.pid",
            "INNER JOIN seg_lab_services s ON s.service_code=ld.service_code",
            "INNER JOIN seg_lab_service_groups g ON g.group_code=s.group_code"
        ),
        "fields" => Array(
            "id"                    => "l.id",
            "costCenter"     => SegRequest::LABORATORY_REQUEST,
            "date"                 => "l.entry_date",
            "pid"                 => "l.pid",
            "controlNo"     => "l.control_nr",
            "ssNo"                 => "l.ss_nr",
            "isAdvance"     => "l.is_advance",
            "fullName"         => "fn_get_person_name(l.pid)",
            "firstName"     => "p.name_first",
            "lastName"         => "p.name_last",
            "itemName"         => "s.name",
            "itemGroup"     => "g.name",
            "refNo"                => "ld.ref_no",
            "itemCode"        => "ld.service_code",
            "amount"             => "ld.amount",
            "encoder"         => "l.create_id"
        )

    );
}



/**
* QueryBuilder blueprint for Lingap Radiology entry
*/
if (in_array(SegRequest::RADIOLOGY_REQUEST, $request_types))
{
    $blueprints[SegRequest::RADIOLOGY_REQUEST] = Array(
        "coreTable" => "seg_lingap_entries_radiology ld",
        "joins" => Array(
            "INNER JOIN seg_lingap_entries l ON l.id=ld.entry_id",
            "INNER JOIN care_person p ON p.pid=l.pid",
            "INNER JOIN seg_radio_services s ON s.service_code=ld.service_code",
            "INNER JOIN seg_radio_service_groups g ON g.group_code=s.group_code"
        ),
        "fields" => Array(
            "id"                    => "l.id",
            "costCenter"     => SegRequest::RADIOLOGY_REQUEST,
            "date"                 => "l.entry_date",
            "pid"                 => "l.pid",
            "controlNo"     => "l.control_nr",
            "ssNo"                 => "l.ss_nr",
            "isAdvance"     => "l.is_advance",
            "fullName"         => "fn_get_person_name(l.pid)",
            "firstName"     => "p.name_first",
            "lastName"         => "p.name_last",
            "itemName"         => "s.name",
            "itemGroup"     => "g.name",
            "refNo"                => "ld.ref_no",
            "itemCode"        => "ld.service_code",
            "amount"             => "ld.amount",
            "encoder"         => "l.create_id"
        )
    );
}


/**
* QueryBuilder blueprint for Lingap Misc entry
*/
if (in_array(SegRequest::MISC_REQUEST, $request_types))
{
    $blueprints[SegRequest::MISC_REQUEST] = Array(
        "coreTable" => "seg_lingap_entries_misc ld",
        "joins" => Array(
            "INNER JOIN seg_lingap_entries l ON l.id=ld.entry_id",
            "INNER JOIN care_person p ON p.pid=l.pid",
            "INNER JOIN seg_other_services s ON s.alt_service_code=ld.service_code",
            "INNER JOIN seg_cashier_account_subtypes t ON s.account_type=t.type_id"
        ),
        "fields" => Array(
            "id"                    => "l.id",
            "costCenter"     => SegRequest::MISC_REQUEST,
            "date"                 => "l.entry_date",
            "pid"                 => "l.pid",
            "controlNo"     => "l.control_nr",
            "ssNo"                 => "l.ss_nr",
            "isAdvance"     => "l.is_advance",
            "fullName"         => "fn_get_person_name(l.pid)",
            "firstName"     => "p.name_first",
            "lastName"         => "p.name_last",
            "itemName"         => "s.name",
            "itemGroup"     => "t.name_long",
            "refNo"                => "ld.ref_no",
            "itemCode"        => "ld.service_code",
            "amount"             => "ld.amount",
            "encoder"         => "l.create_id"
        )
    );
}


$queries = Array();

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

foreach ($request_types as $request_type)
{
    $blueprint = $blueprints[$request_type];
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

    if ($blueprint)
    {
        //$blueprint['where'][] = QueryBuilder::express($blueprint, "MONTHSBEFORE", "date", "3");
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

        $request = new SegRequest($row['costCenter'], Array("refNo"=>$row['refNo'], "itemNo"=>$row['itemNo']));
        $requestRow = $request->fetch(Array("isServed"));
        $served = $requestRow['isServed']==="1" ? 1 : 0;

        $data[] = array(
            'id' => $row['id'],
            'date' => date("d-M-Y h:i: a", strtotime($row["date"])),
            'controlNo' => htmlentities($row['controlNo']),
            'ssNo' => htmlentities($row['ssNo']),
            'pid' => $row['pid'],
            'fullName' => htmlentities(strtoupper($row['fullName'])),
            'source' => $row['costCenter'],
            'costCenter' => $typeNames[$row['costCenter']],
            'itemName' => htmlentities($row['itemName']),
            'itemGroup' => htmlentities($row['itemGroup']),
            'refNo' => $row['refNo'],
            'itemCode' => $row['itemCode'],
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