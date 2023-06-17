<?php

require('./roots.php');
include_once($root_path . 'include/care_api_classes/reports/JasperReport.php');
include_once($root_path . 'include/care_api_classes/inventory/class_medical_supply.php');
include_once($root_path . 'include/care_api_classes/class_area.php');

$jasper = new JasperReport();
$supplies = new MedicalSupply();
$areaObj = new SegArea();
$asOfDate = @$_GET['date'];
$area = @$_GET['area'];

$list = $supplies->getSuppliestList($asOfDate, $area);

if($asOfDate === '') {
    $asOfDate = '(No Date)';
}

if($area !== '') {
    $area = $areaObj->getAreaName($area);
}

$jasper->setParams(array(
    'asofdate' => $asOfDate,
    'areaname' => $area,
));

if($list) {
    $jasper->setData($list);
}

$jasper->setJrxmlFilePath('INV_supplies-list.jrxml');
$jasper->run();



