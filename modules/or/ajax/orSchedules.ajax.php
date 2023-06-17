<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require_once $root_path."include/inc_environment_global.php";
require_once $root_path."include/care_api_classes/class_core.php";
require_once $root_path."classes/json/json.php";

global $db;

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$d1 = $_REQUEST['d1'];
$d2 = $_REQUEST['d2'];

$where = array();
if ($_REQUEST['module'] == 'orasu') {
	$where[]="is_main=0";
}
else {
	$where[]="is_main=1";
}

if ($_REQUEST['dept'])
{
	$where[] = "o.dept_nr=".$db->qstr($_REQUEST['dept']);
}

//added by cha, 11-18-2010
if($_REQUEST['priority'])
{
	$where[] = "o.request_priority=".$db->qstr($_REQUEST['priority']);
}


$where[]="IFNULL(final_date_operation,date_operation) BETWEEN CAST(".$db->qstr($d1)." AS DATE) AND CAST(".$db->qstr($d2)." AS DATE)";

$query = "SELECT e.pid, o.encounter_nr,cp.name_last, cp.name_first,\n".
		"o.trans_type,o.request_priority,\n".
		"final_date_operation `final_date`,date_operation `initial_date`,\n".
		"IFNULL(o.final_date_operation,o.date_operation) `date`,\n".
		"or_main_refno `refno`,or_procedure `procedure`,o.status,\n".
		"pj.name_last `surgeon_lastname`,pj.name_first `surgeon_firstname`,\n".
		"IFNULL(p.or_no,'NONE') `or_no`, IFNULL(p.or_date,'N/A')  `or_date`\n".
	"FROM seg_or_main o\n".
		"INNER JOIN care_encounter e ON e.encounter_nr=o.encounter_nr\n".
		"INNER JOIN care_person cp ON e.pid=cp.pid\n".
		"LEFT JOIN care_personell j ON j.nr=o.dr_nr\n".
		"LEFT JOIN care_person pj ON pj.pid=j.pid\n".
		"LEFT JOIN seg_pay_request r ON r.ref_source='OR' AND r.ref_no=o.or_main_refno\n".
		"LEFT JOIN seg_pay p ON p.or_no=r.or_no\n".
	"WHERE (".implode(") AND (", $where).")\n".
	"ORDER BY o.final_date_operation DESC,IF(o.trans_type=0, o.date_request, IFNULL(p.or_date,'9999-99-99')) ASC,o.date_operation DESC,p.or_date DESC";

//$core = new Core;
//$core->setupLogger();
//$core->logger->debug($query);

$response = Array();
$rs = $db->Execute($query);
if ($rs !== false) {
	$statusColors = Array(
		'request' 		=> 'mediumblue',
		'cancelled' 	=> 'lightcoral',
		'post' 				=> 'orangered',
		'pre_op' 			=> 'blueviolet',
		'approved' 		=> 'green',
		'disapproved' => 'crimson',
		'resched'			=> 'pink'
	);
	$statusMessage 	= Array(
		'request' 		=> 'Request pending',
		'cancelled' 	=> 'Cancelled',
		'post' 				=> 'Post-operative',
		'pre_op' 			=> 'Pre-operative',
		'approved' 		=> 'Approved',
		'disapproved' => 'Disapproved',
		'resched'			=> 'Rescheduled'
	);
	while ($row = $rs->FetchRow()) {
		$strdate = $row['date'];
		$timestamp = strtotime($strdate);

		$response[] = Array(
			'Id'					=> $row['refno'],
			'Y' 					=> date('Y', $timestamp),
			'M'						=> (int)date('m', $timestamp)-1,
			'D'						=> date('d', $timestamp),
//			'h'						=> date('H', $timestamp),
//			'm'						=> date('i', $timestamp),
//			's'						=> date('s', $timestamp),
			'OrNo'				=> ($row['trans_type'] == 1) ? $row['or_no'] : 'Charge',
			'OrDate' 			=> $row['or_date'],
			'Pid' 				=> $row['pid'],
			'Case' 				=> $row['encounter_nr'],
			'Name' 				=> (strtoupper($row['name_last'].", ".$row['name_first'])),
			'Procedure'		=> $row['procedure'],
			'Schedule1'		=> $row['initial_date'],
			'Schedule2'		=> $row['final_date'],
			'Schedule'		=> $row['date'],
			'Color' 			=> $statusColors[$row['status']],
			'Status' 			=> $statusMessage[$row['status']],
			'Priority' => ($row['request_priority']=='Emergency' ? ' <span style="color:#b00">[STAT]</span>' : '<span style="color:#b00">[ELECTIVE]</span>'),	//added by cha, 11-18-2010
			'Surgeon'			=> $row['surgeon_lastname'] ? ($row['surgeon_lastname'].', '.$row['surgeon_firstname']) : 'Not assigned'
		);
	}
}
else {
}

$json = new Services_JSON;
print $json->encode($response);