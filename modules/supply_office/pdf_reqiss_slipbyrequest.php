<?php
 /**
    * SegHIS - Hospital Information System (BPH Deployment)
    * Date Created - 05-24-2017
    * @author       Julius Moncal 
*/    

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
include_once($root_path . 'include/care_api_classes/reports/JasperReport.php');
include_once($root_path.'include/care_api_classes/class_department.php');
include_once($root_path.'include/care_api_classes/inventory/class_inventory.php');
include_once($root_path.'include/care_api_classes/inventory/class_request.php');
include_once($root_path.'include/care_api_classes/inventory/class_issuance.php');
include_once($root_path.'include/care_api_classes/class_personell.php');
include_once($root_path.'include/care_api_classes/inventory/class_unit.php');
include_once($root_path.'include/care_api_classes/class_pharma_product.php');
include_once($root_path."include/care_api_classes/class_hospital_admin.php"); 
include_once($root_path."include/care_api_classes/class_area.php");
include_once($root_path.'include/care_api_classes/inventory/class_sku_inventory.php');
include_once($root_path.'include/care_api_classes/inventory/class_inventory_report.php');

$refno = $_GET['refno'];

$jasper = new JasperReport();
$objInfo = new Hospital_Admin();
$req_obj = new Request();
$iss_obj =  new Issuance();
$objarea = new SegArea();
$unit_obj = new Unit();
$prod_obj = new SegPharmaProduct();
$person_obj = new Personell();


//get hospital info and requested area
$row = $objInfo->getAllHospitalInfo();
$header = $req_obj->getRequestHeader($refno);
$arearqstd = $objarea->getAreaName($header['area_code_dest']);
$arearqstng = $objarea->getAreaName($header['area_code']);
$issrefno = $iss_obj->getIssuanceNoByRequest($refno)==""? "Not Issued" : $iss_obj->getIssuanceNoByRequest($refno); 
$reqheader = $req_obj->getRequestHeader($refno);
$request = $person_obj->getPersonnelRequisitionDetails($reqheader['requestor_id']);

//fetch items requested
$reqdetails = $req_obj->getRequestDetails($refno);
while($rowreqdet = $reqdetails->FetchRow()){
$iteminfo = $prod_obj->getProductInfo($rowreqdet['item_code']);
$params[] = array('item_code' => $rowreqdet['item_code'],
                  'req_qty'  => $rowreqdet['item_qty'],
                  'req_unit' => $unit_obj->getUnitName($rowreqdet['unit_id']),
                  'req_desc' => $iteminfo['artikelname']." ",
                  'issue_qty' => " ",
                  'issue_cost' => " ",
                  'total_val' => " ",
                  'field1' => " ",
 );

}


//send data to jasper
$data =  $params;
$jasper->setParams(array(
    'hosp_name' => $row['hosp_name']." ",
    'hosp_addr1' => $row['hosp_addr1']." ",
    'title' => "R E Q U I S I T I O N  S L I P ",
    'reqstd_area' => $arearqstd." ",
    'reqsting_area' => $arearqstng." ",
    'req_no' => $refno." ",
    'issue_no' => $issrefno." ",
    'req_date' => date("Y-m-d",strtotime($header['request_date']))." ",
    'request_by' => $request['name']." ",
    'request_position' => $request['title']." ",
    ));

$jasper->setJrxmlFilePath('Requisition_issue_slip.jrxml');
$jasper->setData($data);
$jasper->run();

?>