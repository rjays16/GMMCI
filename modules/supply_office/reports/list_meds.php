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
include_once($root_path . 'include/care_api_classes/inventory/class_medical_supply.php');
$supplies = new MedicalSupply();
$jasper = new JasperReport();
$area_obj = new SegArea();

$areacode = $_GET['area'];
$asofdate = $_GET['date'];

$trDate = '';
if($asofdate == '') {
    $asof_date = '(No Date)';
} else {
    $asof_date = date("m/d/Y", strtotime($asofdate));
    $trDate = date("Y-m-d", strtotime($asofdate));
}

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

$list = $supplies->getMedsList($asofdate, $area);
$area_name = $area_obj->getAreaName($area);

$areaname = $area_obj->getAreaName($areacode);

$imgpath = $jasper->getLogoPath();

// Assign the report and format to generate ...
$jasper->setParams(array(
    'asofdate' => $asofdate,
    'areaname' => $areaname,
));

if($list) {
    $jasper->setData($list);
}

$jasper->setJrxmlFilePath('INV_meds-list.jrxml');
$jasper->run();
?>