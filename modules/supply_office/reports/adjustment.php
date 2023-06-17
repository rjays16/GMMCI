<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/inventory/class_adjustment.php');
require_once($root_path.'include/care_api_classes/reports/JasperReport.php');
require_once($root_path.'include/care_api_classes/class_area.php');

$adjustment = new SegAdjustment();
$areaObj = new SegArea();

$jasper = new JasperReport();
$params['area'] = @$_GET['area'];
$asOfDate = @$_GET['date'];
$area = @$_GET['area'];
$adjustReason = @$_GET['adjustreason'];

if($area === '')
    $area = 'All Areas';
else {
    $area = $areaObj->getAreaName($area);
}

if($adjustReason === '') {
    $adjustReason = 'All Reason';
} else {
    $params['adjustr'] = $adjustReason;
    $adjustReason = $adjustment->getReasonName($adjustReason);
}

if($asOfDate === '') {
    $asOfDate = '(No Date)';
    $params['as_of_date'] = '';
}

else {
    $params['as_of_date'] = $asOfDate;
}

$jasper->setParams(array(
    'areaname' => $area,
    'asofdate' => $asOfDate,
    'adjust_reason' => $adjustReason
));

$items = $adjustment->getAdjustments($params);
$jasper->setData($items);
$jasper->setJrxmlFilePath('INV_adjustment.jrxml');
$jasper->run();
