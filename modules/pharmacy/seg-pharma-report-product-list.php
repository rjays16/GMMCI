<?php

require('./roots.php');
include_once($root_path . 'include/care_api_classes/reports/JasperReport.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_pharma_product.php');
include_once($root_path."include/care_api_classes/inventory/class_sku_inventory.php");
include_once($root_path.'include/care_api_classes/inventory/class_request.php');
require_once($root_path.'include/inc_environment_global.php');

$jasper = new JasperReport();
$objInfo = new Hospital_Admin();
$objPharma = new SegPharmaProduct();
$pclass = new SegPharmaProduct();
$req_obj = new Request();

define('REPORT_NAME', 'List of Products');

if ($row = $objInfo->getAllHospitalInfo()) {
	$row['hosp_add'] = strtoupper($row['hosp_addr1']);
	$row['hosp_name']   = strtoupper($row['hosp_name']);
	$row['hosp_agency'] = $row['hosp_agency'];
}else{
	$row['hosp_name'] = "GONZALES MARANAN MEDICAL CENTER, INC.";
	$row['hosp_add'] = "QUEZON AVE. ZONE 1, DIGOS CITY, DAVAO DEL SUR";
	$row['hosp_agency'] = "Department of Health";
}

$jasper->setParams(array(
	'hosp_name' 	=> $row['hosp_name'],
	'hosp_add' 		=> $row['hosp_add'],
	'agency' 		=> $row['hosp_agency'],
	'report_name' 	=> REPORT_NAME,
	'image_path' 	=>$jasper->getLogoPath(),
));

$expiry_type = $_GET['expiry'];
$f['no_limit'] = TRUE; //DISPLAY ALL
// $f['offset'] = 0;
// $f['rowcount'] = 500;

$result=$pclass->search($f);
$DATA = array();
$datas = array();
			while($row = $result->FetchRow()) {
				if($expiry_type == 'all'){

					$skuObj = new SkuInventory();

					$qty =  $skuObj->getItemQty($row['bestellnum']);
					$delivery_info = $req_obj->getDeliveryInfo($row['bestellnum']);

					if(!$delivery_info[0]['expiry_date']){
					 	$expiry = 'NO EXPIRY DATE';
					}else{
						$expiry = date('M. d, Y',strtotime($delivery_info[0]['expiry_date']));
					}

					$datas[] = array('artikelname' 	=> $row['artikelname'],
									 'generic' 		=> $row['generic'],
									 'stock' 		=> (int)$qty,
									 'expiry' 		=> $expiry );

				}else if($expiry_type == 'withExpiry'){

					$skuObj = new SkuInventory();

					$qty =  $skuObj->getItemQty($row['bestellnum']);
					$delivery_info = $req_obj->getDeliveryInfo($row['bestellnum']);

					if(!$delivery_info[0]['expiry_date']){
					 	$expiry = 'NO EXPIRY DATE';
					}else{
						$expiry = date('M. d, Y',strtotime($delivery_info[0]['expiry_date']));

						$datas[] = array('artikelname' 	=> $row['artikelname'],
									 'generic' 		=> $row['generic'],
									 'stock' 		=> (int)$qty,
									 'expiry' 		=> $expiry );
					}

				}else if($expiry_type == 'withOutExpiry'){

					$skuObj = new SkuInventory();

					$qty =  $skuObj->getItemQty($row['bestellnum']);
					$delivery_info = $req_obj->getDeliveryInfo($row['bestellnum']);

					if(!$delivery_info[0]['expiry_date']){
					 	$expiry = 'NO EXPIRY DATE';

					 	$datas[] = array('artikelname' 	=> $row['artikelname'],
									 'generic' 		=> $row['generic'],
									 'stock' 		=> (int)$qty,
									 'expiry' 		=> $expiry );

					}else{
						$expiry = date('M. d, Y',strtotime($delivery_info[0]['expiry_date']));
					}

				}
			} //end while

$jasper->setData($datas);
$jasper->setJrxmlFilePath('pharma_product_list.jrxml');
$jasper->run();

?>