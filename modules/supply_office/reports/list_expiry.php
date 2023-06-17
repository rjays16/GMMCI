<?php

require('./roots.php');
include_once($root_path . 'include/care_api_classes/reports/JasperReport.php');
include_once($root_path . 'include/care_api_classes/inventory/class_medicine.php');
include_once($root_path . 'include/care_api_classes/class_area.php');
$jasper = new JasperReport();
$medicine = new Medicine();
$areaObj = new SegArea();

$area = @$_GET['area'];
$fromdate = @$_GET['from_date'];
$todate = @$_GET['to_date'];

if($fromdate !== '') {
    $params['from_date'] = $fromdate;
} else {
    $fromdate = '(No Date)';
}

if($todate !== '') {
    $params['to_date'] = $todate;
} else {
    $todate = '(No Date)';
}

$fromDate = strftime("%Y-%m-%d", strtotime($fromdate));
$toDate = strftime("%Y-%m-%d", strtotime($todate));

$noExpiry = @$_GET['expcheck'] === 'checked' ? true : false;
$medList = $medicine->getMedicinesList($fromDate, $toDate, $area, $noExpiry);


if($area !== '') {
    $area = $areaObj->getAreaName($area);
}

$jasper->setParams(array(
	'from_date' => $fromdate,
	'to_date' => $todate,
    'areaname' => $area,
));
if($medList) {
    $jasper->setData($medList);
}

$jasper->setJrxmlFilePath('INV_expiry-list.jrxml');
$jasper->run();



