<?php
/*
created by Nick, 11/29/2013 10:00 PM
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include('parameters.php');

#_________________________________________________
$params->put('hosp_name',mb_strtoupper($hosp_name));
$from = date("F j, Y", strtotime($from_date_format) );
$to = date("F j, Y", strtotime($to_date_format) );
$params->put('date_span',$from . ' to ' . $to);
#_________________________________________________

$sql = "SELECT  a.`ordername` AS pat_name,
				(SELECT NAME FROM seg_lab_services WHERE service_code = b.`service_code`) AS unit_test,
				(CASE b.`result`
					WHEN 'noresult' THEN 'NO RESULT'
					WHEN 'compat' THEN 'COMPATIBLE'
					WHEN 'incompat' THEN 'INCOMPATIBLE'
					WHEN 'retype' THEN 'RETYPING'
					ELSE b.`result` END) AS result,
				DATE_FORMAT(a.`create_dt`,'%m-%d-%Y %r') AS date_recorded,
				DATE_FORMAT(b.`received_date`,'%m-%d-%Y %r') AS date_received,
				DATE_FORMAT(c.`done_date`,'%m-%d-%Y %r') AS date_done,				
				TIMEDIFF(c.`done_date`,b.`received_date`) AS turn_around_time
		FROM seg_lab_serv AS a
		INNER JOIN seg_blood_received_details AS b ON a.`refno` = b.`refno`
		INNER JOIN seg_blood_received_status AS c ON a.`refno` = c.`refno`
		WHERE DATE(a.`create_dt`) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format);

$rs = $db->Execute($sql);
$date = array();
$rowIndex = 0;

if(is_object($rs)){
	while($row=$rs->FetchRow()){
		$data[$rowIndex] = array('pat_name' => $row['pat_name'],
								 'unit_test' => $row['unit_test'],
								 'result' => $row['result'],
								 'date_recorded' => $row['date_received'],
								 'date_done' => $row['date_done'],
								 'turn_around_time' => $row['turn_around_time']);
		$rowIndex++;
	}
}else{
	$data[0]['pat_name'] = 'No records';
}

?>