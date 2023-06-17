<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/inventory/class_inventory_report.php');
require_once($root_path.'include/care_api_classes/reports/JasperReport.php');
require_once($root_path.'include/care_api_classes/class_area.php');

$inventoryRep = new SegInventoryReport();
$areaObj = new SegArea();
$jasper = new JasperReport();
$fromDate = @$_GET['from_date'];
$toDate = @$_GET['to_date'];
$area = @$_GET['area'];
$fromPercent = @$_GET['from_percent'];
$toPercent = @$_GET['to_percent'];

$params = array(
    'from_percent' => $fromPercent,
    'to_percent' => $toPercent,
    'area' => $area
);


if($fromDate !== '') {
    $params['from_date'] = $fromDate;
} else {
    $fromDate = '(No Date)';
}

if($toDate !== '') {
    $params['to_date'] = $toDate;
} else {
    $toDate = '(No Date)';
}
if($area !== '') {
    $area = $areaObj->getAreaName($area);
}

$jasper->setParams(array(
    'to_date' => $toDate,
    'from_date' => $fromDate,
    'areaname' => $area,
    'from_percent' => $fromPercent,
    'to_percent' => $toPercent,
));

$items = $inventoryRep->getFastSlowMoving($params);
$jasper->setData($items);
$jasper->setJrxmlFilePath('INV_slowfast_items.jrxml');
$jasper->run();
