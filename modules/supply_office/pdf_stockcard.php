<?php
/**
 * SegHIS - Hospital Information System (BPH Deployment)
 * by Segworks Technologies Corporation
 *  
 * (Using JRXML)
 * 
 * @author  Bong S. Trazo 
 * @uses Jasper Report
 */

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
include_once($root_path . 'include/care_api_classes/reports/JasperReport.php');
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
include_once($root_path.'include/care_api_classes/class_area.php');
include_once($root_path.'include/care_api_classes/class_pharma_product.php');
require_once($root_path.'include/care_api_classes/inventory/class_sku_inventory.php');
require_once($root_path.'include/care_api_classes/inventory/class_inventory_report.php');

$itemcode = $_GET['item'];

$areacode = $_GET['area'];
$fromdte = $_GET['from_date'];
$todte = $_GET['to_date'];

$from_date = date("Y-m-d", strtotime($fromdte));
$to_date   = date("Y-m-d", strtotime($todte));

$objInfo = new Hospital_Admin();
$row = $objInfo->getAllHospitalInfo();

if ($row) {      
  $row['hosp_agency'] = strtoupper($row['hosp_agency']);
  $row['hosp_name']   = strtoupper($row['hosp_name']);
}
$agency = $row['hosp_agency']."-".$row['hosp_name'];

$area_obj = new SegArea();
$areaname = $area_obj->getAreaName($areacode);

$prod_obj = new SegPharmaProduct();
$prodinfo = $prod_obj->getProductInfo($itemcode);
$prodextend = $prod_obj->getExtendedProductInfo($itemcode);

$minqty = 0;
if ($prodextend) {
    $minqty  = $prodextend['min_qty'];
}
$itemname = "";
if ($prodinfo) {
    $itemname = $prodinfo['artikelname'];
}

$skuobj = new SKUInventory();
$beginning_qty = $skuobj->getRecentItemQty($itemcode, $from_date, $areacode,1);
$beginning_cost = $skuobj->getItemAvgCost($itemcode, $from_date, $areacode, '', '', 0, true);
$beginning_Ucost = $skuobj->bgngcost($itemcode, $from_date, $areacode);

if($beginning_qty == null || $beginning_qty == "")
{
    $beginning_qty = 0;   
}

$jasper = new JasperReport();
$invReport = new SegInventoryReport();


$data = $invReport->getStockCard($itemcode, $areacode, $from_date, $to_date, $beginning_qty, $beginning_cost);

$jasper->setParams(array(
    'itemcode' => $itemcode,
    'areacode' => $areacode,
    'startdate' => $from_date,
    'enddate' => $to_date,
    'agency' => $agency,
    'area' => $areaname,
    'minqty' => intval($minqty),
    'itemname' => $itemname,
    'beg_Ucost' => number_format($beginning_Ucost,2),
    'beginning_qty' => floatval($beginning_qty),
    'beginning_cost' => floatval($beginning_cost),
));

$jasper->setJrxmlFilePath('INV_stock-card.jrxml');
$jasper->setData($data);

$jasper->run();
?>