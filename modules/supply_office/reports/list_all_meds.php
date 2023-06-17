<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
include_once($root_path . "include/care_api_classes/class_hospital_admin.php");
include_once($root_path . 'include/care_api_classes/class_all_meds.php');
require_once($root_path . 'include/care_api_classes/reports/JasperReport.php');

// $areacode = $_GET['area'];
// $asofdate = $_GET['date'];

// $jasper = new JasperReport();
// $trDate = '';
// if($asofdate == '') {
//     $asof_date = '(No Date)';
// } else {
//     $asof_date = date("m/d/Y", strtotime($asofdate));
//     $trDate = date("Y-m-d", strtotime($asofdate));
// }

$objInfo = new Hospital_Admin();
$row = $objInfo->getAllHospitalInfo();
if ($row) {
//     $row['hosp_agency'] = strtoupper($row['hosp_agency']);
    $row['hosp_name']   = strtoupper($row['hosp_name']);
}
else {
//     $row['hosp_country'] = "Republic of the Philippines";
//     $row['hosp_agency']  = "PROVINCE OF BUKIDNON";
    $row['hosp_name']    = "BPH";
//     $row['hosp_addr1']   = "Bukidnon, Philippines";
}

// $area_obj = new SegArea();
// $areaname = $area_obj->getAreaName($areacode);

// $imgpath = $jasper->getLogoPath();

// Assign the report and format to generate ...
// $report = 'INV_meds-list';
// $report = 'report1';
// $repformat = 'pdf';

// $params = array();
// $params[] = array("hospcountry", $row['hosp_country'], 'java.lang.String');
// $params[] = array("hospagency", $row['hosp_agency'], 'java.lang.String');
// $params[] = array("hospname", $row['hosp_name'], 'java.lang.String');
// $params[] = array("hospaddr", $row['hosp_addr1'], 'java.lang.String');
// $params[] = array("imagepath", $imgpath, 'java.lang.String');
// $params[] = array("areacode", $areacode, 'java.lang.String');
// $params[] = array("areaname", $areaname, 'java.lang.String');
// $params[] = array("asofdate", $asof_date, 'java.lang.String');
// $params[] = array("trDate", $trDate, 'java.lang.String');

// include($root_path . 'modules/reports/render_report_jasper.php');

$jasper = new JasperReport();
$allMeds = new SegAllMeds();
// $medicine = new Medicine();
// $areaObj = new SegArea();

// $area = '';
// $fromdate = '';
// $todate = '';

// if($fromdate !== '') {
//     $params['from_date'] = '';
// } else {
//     $fromdate = '''';
// }

// if($todate !== '') {
//     $params['to_date'] = $todate;
// } else {
//     $todate = '(No Date)';
// }

// $fromDate = strftime("%Y-%m-%d", strtotime($fromdate));
// $toDate = strftime("%Y-%m-%d", strtotime($todate));
$asofdate = '';
$asofdate = strftime("%Y-%m-%d", strtotime($_GET['date']));
$item = $_GET['item'];
$data = $allMeds->getAllMedsInventory($asofdate,$item);
// $noExpiry = @$_GET['expcheck'] === 'checked' ? true : false;
// $medList = $medicine->getMedicinesList($fromDate, $toDate, $area, $noExpiry);


// if($area !== '') {
//     $area = $areaObj->getAreaName($area);
// }

// $jasper->setParams(array(
// 	'from_date' => '',
// 	'to_date' => '',
//     'areaname' => '',
// ));

// if($medList) {
//     $jasper->setData($medList);

// echo "sadad";
// print_r($data);
// $stat = false;
foreach($data as $key => $value)
{
	$data[$key]['mvmnt_qty'] = (double)$value['mvmnt_qty'];
	
}

// if(is_double($data[1]['mvmnt_qty'])){
// 	echo "inter";
// 	echo $data[1]['mvmnt_qty'];
// }
// else{
// 	echo "not";
// 	echo $data[1]['mvmnt_qty'];
// }
// echo is_int($data['mvmnt_qty'][1]);
// die;
$jasper->setParams(array(
	'agency' => $row['hosp_name'], # pass as agency
	'enddate' => $asofdate,
	'item' => ($item==''?'All items':$allMeds->getArtikelName($item))));
$jasper->setData($data);
$jasper->setJrxmlFilePath('INV_all_meds.jrxml');
$jasper->run();
?>