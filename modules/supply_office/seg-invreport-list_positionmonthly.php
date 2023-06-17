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
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
include_once($root_path.'include/care_api_classes/class_area.php');
include_once($root_path.'include/care_api_classes/class_pharma_product.php');
require_once($root_path.'include/care_api_classes/inventory/class_sku_inventory.php');

$areacode = $_GET['area'];

// Get last day of the month ...
//$month = strftime("%m", strtotime($asofdate));
//$year  = strftime("%Y", strtotime($asofdate));
$month = $_GET['month'];
$year = $_GET['year'];

$firstday = strftime("%Y-%m-%d", strtotime($year."-".$month."-01"));
$lastday = strtotime("+1 month", strtotime($firstday));
$lastday = strtotime("-1 day", $lastday);
$lastday = strftime("%Y-%m-%d", $lastday);

$objInfo = new Hospital_Admin();
$row = $objInfo->getAllHospitalInfo();
if ($row) {      
    $row['hosp_agency'] = strtoupper($row['hosp_agency']);
    $row['hosp_name']   = strtoupper($row['hosp_name']);
}
else {
    $row['hosp_country'] = "Republic of the Philippines";
    $row['hosp_agency']  = "PROVINCE OF BUKIDNON";
    $row['hosp_name']    = "BPH";
    $row['hosp_addr1']   = "Bukidnon, Philippines";      
}
//$agency = $row['hosp_agency']."-".$row['hosp_name'];

$area_obj = new SegArea();
$areaname = $area_obj->getAreaName($areacode);

$imgpath = java_resource.'bphlogo.jpg';

// Assign the report and format to generate ...
$report = 'monthly-position';
$repformat = 'pdf';

$params = array();
$params[] = array("hospcountry", $row['hosp_country'], 'java.lang.String');
$params[] = array("hospagency", $row['hosp_agency'], 'java.lang.String');
$params[] = array("hospname", $row['hosp_name'], 'java.lang.String');
$params[] = array("hospaddr", $row['hosp_addr1'], 'java.lang.String');
$params[] = array("imagepath", $imgpath, 'java.lang.String');
$params[] = array("areacode", $areacode, 'java.lang.String');
$params[] = array("areaname", $areaname, 'java.lang.String');
$params[] = array("asofdate", $lastday, 'java.lang.String');

/***
 * Function to get the items to be listed in this report ...
 */
function getItemsWithMovement($areacode, $refdate) {
    global $db;
    
    $data = array();
    
    // Get the first day of the month ...
    $month = strftime("%m", strtotime($refdate));
    $year  = strftime("%Y", strtotime($refdate));
    $firstday = strftime("%Y-%m-%d", strtotime($year."-".$month."-01"));

    $strSQL = "SELECT 
                sku.`item_code`,
                m.`artikelname`,
                sku.`unit_id`,
                (SELECT unit_name FROM seg_unit u WHERE u.unit_id = sku.unit_id) unitname,
                sku.`expiry_date`,
                m.`price_cash`
              FROM
                seg_inventory_ledger sel 
                INNER JOIN (
                    seg_sku_catalog sku 
                    INNER JOIN care_pharma_products_main m 
                      ON m.`bestellnum` = sku.`item_code`
                  ) 
                  ON sel.`sku_id` = sku.`sku_id` 
              WHERE tr_date <= DATE('{$refdate}') 
                AND sku.`area_code` = '{$areacode}' 
              GROUP BY sku.`item_code`, sku.`unit_id`, sku.`expiry_date`  
              HAVING SUM(mvmnt_qty * packqty) > 0 
              ORDER BY m.`artikelname`";                
                
    $result = $db->Execute($strSQL);
    if ($result) {            
        if ($result->RecordCount()) {
            $skuobj = new SKUInventory();
            while ($row = $result->FetchRow()) {
                $skuids = $skuobj->getSKUIds($row['item_code'], $areacode, $row['expiry_date'], '', $row['unit_id']);
                $begqty = $skuobj->getQtyofSKUs($skuids, $firstday, TRUE);
                $endqty = $skuobj->getQtyofSKUs($skuids, $refdate);
                
                $inqty = $skuobj->getQtyInPeriod($skuids, $firstday, $refdate);
                $incost = $skuobj->getCostInPeriod($skuids, $firstday, $refdate);
                $incost = round(($incost / $inqty), 2) * $inqty;
                
                $data[] = array(
                            'item_code'   => $row['item_code'],
                            'item_name'   => $row['artikelname'],
                            'unit_id'     => $row['unit_id'],
                            'unit_name'   => $row['unitname'],
                            'expiry_date' => $row['expiry_date'],
                            'unit_price'  => doubleval($row['price_cash']),
                            'beg_qty'     => doubleval($begqty),
                            'beg_cost'    => $begqty * $skuobj->getAvgCostofSKUs($skuids, $firstday, TRUE),
                            'in_qty'      => doubleval($inqty),
                            'in_cost'     => doubleval($incost),
                            'out_qty'     => doubleval($skuobj->getQtyInPeriod($skuids, $firstday, $refdate, TRUE)),
                            'out_cost'    => doubleval($skuobj->getCostInPeriod($skuids, $firstday, $refdate, TRUE)),
                            'end_qty'     => doubleval($endqty),
                            'end_cost'    => $endqty * $skuobj->getAvgCostofSKUs($skuids, $refdate)                 
                           );
            }
        }
    }

    return $data;                    
                
}

// Provide the arraylist of data ...
$data = getItemsWithMovement($areacode, $lastday);

include($root_path.'modules/reports/render-rep-arraylistsrc.php');
?>