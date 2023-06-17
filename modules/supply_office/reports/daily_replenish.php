<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/inventory/class_inventory_report.php');
require_once($root_path.'include/care_api_classes/reports/JasperReport.php');
require_once($root_path.'include/care_api_classes/class_area.php');

$inventoryRep = new SegInventoryReport();
$jasper = new JasperReport();
$asOfDate = @$_GET['date'];
$area = @$_GET['area'];
$areaObj = new SegArea();
$params = array();

if($asOfDate === '') {
    $asOfDate = '(No Date)';
} else {
    $params['as_of_date'] = $asOfDate;
    $params['area'] = $area;
}

if($area !== '') {
    $area = $areaObj->getAreaName($area);
}

$jasper->setParams(array(
    'areaname' => $area,
    'asofdate' => $asOfDate,
));

$items = $inventoryRep->getDailyReplenishment($params);
//printR($items, true);
$jasper->setData($items);
$jasper->setJrxmlFilePath('INV_daily_replenish.jrxml');
$jasper->run();
