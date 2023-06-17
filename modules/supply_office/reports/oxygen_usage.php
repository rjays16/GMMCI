<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/inventory/class_inventory_report.php');
require_once($root_path.'include/care_api_classes/reports/JasperReport.php');

$inventoryRep = new SegInventoryReport();
$jasper = new JasperReport();
$fromDate = @$_GET['from_date'];
$toDate = @$_GET['to_date'];
$params = array();
$serialNo = @$_GET['serial_no'];

if ($fromDate === '') {
    $fromDate = '(No Date)';
} else {
    $params['from_date'] = $fromDate;
}

if ($toDate === '') {
    $toDate = '(No Date)';
} else {
    $params['to_date'] = $toDate;
}

if($serialNo !== '') {
    $params['serial_number'] = $serialNo;
}

$jasper->setParams(array(
    'to_date' => $toDate,
    'from_date' => $fromDate,
    'serial_number' => $serialNo
));

$items = $inventoryRep->getOxygenUtilization($params);

$jasper->setData($items);
$jasper->setJrxmlFilePath('INV_oxygen_usage.jrxml');
$jasper->run();
