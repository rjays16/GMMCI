<?php
require('./roots.php');
include_once($root_path.'include/care_api_classes/reports/JasperReport.php');
include_once($root_path.'include/care_api_classes/inventory/class_inventory_report.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';

$jasper = new JasperReport();
$inventory = new SegInventoryReport();
$objInfo = new Hospital_Admin();

if ($row1 = $objInfo->getAllHospitalInfo()) {
	$row1['hosp_name']   = strtoupper($row1['hosp_name']);
	$row1['hosp_addr']   = strtoupper($row1['hosp_addr1']);
}
else {
	$row1['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
	$row1['hosp_addr']   = "Quezon Ave., Digos City, Davao del Sur";
}

$point = $_GET['point'];
$area = $_GET['area'];
$date = strftime("%Y-%m-%d", strtotime($_GET['date']));
$inv_res = $inventory->reOrderingPoint($point, $date, $area);

if($inv_res){
	$i=0;
	while($row = $inv_res->FetchRow()){
		$data[$i] = array(
			"no"=>($i+1),
			"item"=>$row['item'],
			"qty"=>number_format($row['qty'],0),
			"code"=>$row['item_code']
			);
		$i++;
	}
}

$jasper->setParams(array(
			"hosp_name"=>$row1['hosp_name'],
			"hosp_addr"=>$row1['hosp_addr'],
			"date_range"=>"As of ".$_GET['date'],
			"rep_title"=>"List of items' re-ordering point"
	));

$jasper->setData($data);
$jasper->setJrxmlFilePath('INV_reordering-point.jrxml');
$jasper->run();