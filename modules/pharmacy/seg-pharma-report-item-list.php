<?php

require('./roots.php');
include_once($root_path . 'include/care_api_classes/reports/JasperReport.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_pharma_product.php');
require_once($root_path.'include/inc_environment_global.php');

$jasper = new JasperReport();
$objInfo = new Hospital_Admin();
$objPharma = new SegPharmaProduct();

if ($row = $objInfo->getAllHospitalInfo()) {
	$row['hosp_add'] = strtoupper($row['hosp_addr1']);
	$row['hosp_name']   = strtoupper($row['hosp_name']);
}else{
	$row['hosp_name'] = "GONZALES MARANAN MEDICAL CENTER, INC.";
	$row['hosp_add'] = "QUEZON AVE. ZONE 1, DIGOS CITY, DAVAO DEL SUR";
}

$result = $objPharma->get_list();
$i=1;

if($result){
	while($value=$result->FetchRow()){
		$data[] = array('no'=>$i,
							'item_code'=>$value['bestellnum'],
							'item_name'=>$value['artikelname'],
							'item_type'=>$value['item_type'],
							'item_price'=>number_format($value['price_cash'],2)
							);
		$i++;
	}
}

$jasper->setParams(array(
	'hosp_name' => $row['hosp_name'],
	'hosp_add' => $row['hosp_add']
));


$jasper->setData($data);
$jasper->setJrxmlFilePath('pharma_items_list.jrxml');
$jasper->run();

?>