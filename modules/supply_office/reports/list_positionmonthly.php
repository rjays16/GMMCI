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
require($root_path . 'include/inc_environment_global.php');
include_once($root_path . "include/care_api_classes/class_hospital_admin.php");
include_once($root_path . 'include/care_api_classes/class_area.php');
include_once($root_path . 'include/care_api_classes/class_pharma_product.php');
require_once($root_path . 'include/care_api_classes/inventory/class_sku_inventory.php');
require_once($root_path . 'include/care_api_classes/reports/JasperReport.php');
require_once($root_path . 'include/care_api_classes/inventory/class_item.php');
require_once($root_path . 'include/care_api_classes/inventory/class_unit.php');

$areacode = $_GET['area'];

// Get last day of the month ...
//$month = strftime("%m", strtotime($asofdate));
//$year  = strftime("%Y", strtotime($asofdate));
$month = $_GET['month'];
$year = $_GET['year'];
$jasper = new JasperReport();
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

$imgpath = $jasper->getLogoPath();

// Assign the report and format to generate ...
$report = 'INV_monthly-position';
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
//    $db->debug = true;
    $data = array();
    $unit = new Unit();

    // Get the first day of the month ...
    $month = strftime("%m", strtotime($refdate));
    $year  = strftime("%Y", strtotime($refdate));
    $firstday = strftime("%Y-%m-%d", strtotime($year."-".$month."-01"));

    $strSQL = "SELECT
                sku.unit_cost,
                sku.`item_code`,
                sel.tr_code,
                m.`artikelname`,
                sku.`unit_id`,

                (SELECT unit_name FROM seg_unit u WHERE u.unit_id = sku.unit_id) unitname,
                sku.`expiry_date`,
                m.`price_cash`,
                sel.sku_id
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

              ORDER BY m.`artikelname`";
//                  HAVING SUM(mvmnt_qty * packqty) > 0
    $result = $db->Execute($strSQL);
    if ($result) {            
        if ($result->RecordCount()) {
            $skuobj = new SKUInventory();
//            printR($result->getArray(), true);

            while ($row = $result->FetchRow()) {

                if (empty($row['expiry_date']) || $row['expiry_date'] == "0000-00-00") {
                    $expiry_date = "NO EXPIRY";
                } else {
                    $expiry_date = date('m/d/Y', strtotime($row['expiry_date']));
                }

                $trCode = $row['tr_code'];
                $item = new Item();
                $extended = $item->getExtendedProductInfo($row['item_code']);
                $packQty = 1;
                if($extended)
                    $packQty = $extended['qty_per_pack'];

                $isBigUnit = $unit->isUnitIDBigUnit($row['unit_id']);

                $skuids = $skuobj->getSKUIds($row['item_code'], $areacode, $row['expiry_date'], '', $row['unit_id']);
                $avg = $skuobj->getAvgUnitCostOfSkus($skuids);
                $begqty = $skuobj->getQtyofSKUs($skuids, $firstday, TRUE);
                $begCost = $skuobj->getCostInPeriod($skuids, '1970-01-01', $firstday);

                if($isBigUnit) {
                   $avg = $avg * $packQty;
                    $begCost = $begCost * $packQty;
                }

                $inqty = $skuobj->getQtyInPeriod($skuids, $firstday, $refdate);

                $incost = $skuobj->getCostInPeriod($skuids, $firstday, $refdate);


                $qty = $begqty + $inqty;

                $incost = $avg;

                $skuAvgCost = round($incost*$qty) / $qty;
                $outQty = doubleval($skuobj->getQtyInPeriod($skuids, $firstday, $refdate, TRUE));
                $outCost = $outQty * $incost;

                $endqty = $inqty+$begqty - $outQty;

                if($extended) {
                    $isCritical = $extended['min_qty'] <= $endqty ? 'true' : 'false';
                } else {
                    $isCritical = $endqty <= 1 ? 'true' : 'false';
                }

                $endCost = $endqty < 0 ? 0 : $endqty * $incost;
                $begCost = $begqty < 0 ? 0 : $begCost * $begqty;
                $data[] = array(
                            'item_code'   => $row['item_code'],
                            'item_name'   => $row['artikelname'],
                            'unit_id'     => $row['unit_id'],
                            'unit_name'   => $row['unitname'],
                            'expiry_date' => $expiry_date,
                            'unit_price'  => doubleval($row['price_cash']),
                            'beg_qty'     => doubleval($begqty),
                            'beg_cost'    => $begCost,
                            'in_qty'      => doubleval($inqty),
                            'in_cost'     => $incost*$inqty,
                            'out_qty'     => $outQty,
                            'out_cost'    => $outCost,
                            'end_qty'     => $endqty,
                            'end_cost'    => $endCost,
                            'is_critical' => $isCritical,
                           );
            }
        }
    }

    return $data;                    
                
}

// Provide the arraylist of data ...
$data = getItemsWithMovement($areacode, $lastday);
//die;
include($root_path . 'modules/reports/render-rep-arraylistsrc.php');
?>