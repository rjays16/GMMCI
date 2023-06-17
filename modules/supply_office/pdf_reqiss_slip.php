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

$refnoissue = $_GET['refno'];

$jasper = new JasperReport();
$objInfo = new Hospital_Admin();
$req_obj = new Request();
$iss_obj =  new Issuance();
$objarea = new SegArea();
$unit_obj = new Unit();
$prod_obj = new SegPharmaProduct();
$person_obj = new Personell();
$skuInventory = new SkuInventory(); 


//get hospital info and requested area
$hospinfo = $objInfo->getAllHospitalInfo();
$req_served = $iss_obj->getReqServedbyIss($refnoissue);
$row = $req_served->FetchRow();
$header = $req_obj->getRequestHeader($row['request_refno']);
$arearqstd = $objarea->getAreaName($header['area_code']);
$arearqstng = $objarea->getAreaName($header['area_code_dest']);
$header = $req_obj->getRequestHeader($row['request_refno']);

//fetch items requested
$reqserveddetails = $iss_obj->getReqServedbyIss($refnoissue);
$reqheader = $req_obj->getRequestHeader($header['refno']);
$issheader = $iss_obj->getIssuanceHeader($refnoissue);
$request = $person_obj->get_Person_name($reqheader['requestor_id']);
$issue = $person_obj->get_Person_name($issheader['issuing_id']);

$requestname = $request['name_first'].' '.$request['name_last'];
$issuename = $issue['name_first'].' '.$issue['name_last'];

while($rowreqdet = $reqserveddetails->FetchRow()){
    $iteminfo = $prod_obj->getProductInfo($rowreqdet['item_code']);

    $reqdetails = $req_obj->getRequestDetailInfo($rowreqdet['request_refno'], $rowreqdet['item_code']);


    $issdetails = $iss_obj->getIssuanceDetailInfo($rowreqdet['issue_refno'],$rowreqdet['item_code']);
    $delivery_info = $req_obj->getDeliveryInfo($rowreqdet['item_code']);
    $unitPrice = $skuInventory->getsupplierprice($rowreqdet['item_code']);
    $rowissdet = $issdetails->FetchRow();

    while($rowreqdetitem = $reqdetails->FetchRow()){ 
        $comqty = $rowreqdetitem['item_qty'];
    }


    $params[] = array('item_code' => $rowreqdet['item_code'],
                      'req_qty'  => number_format($rowreqdet['served_qty'],0,'.',','),
                      'req_unit' => $unit_obj->getUnitName($rowissdet['unit_id']),
                      'req_desc' => $iteminfo['artikelname']." ",
                      'lot_no' => $delivery_info[0]['lot_no']==""?" ": $delivery_info[0]['lot_no'],
                      'exp_date' => $delivery_info[0]['expiry_date']==""?" ":$delivery_info[0]['expiry_date'],
                      'supplier' => $delivery_info[0]['manufacturer']==""?" ":$delivery_info[0]['manufacturer'],
                      'qtyIsu' => number_format($rowreqdet['served_qty'],0,'.',','),
                      'uniPricce' => number_format($unitPrice, 2, '.', ','),
                     'totalval'=> number_format($rowreqdet['served_qty'] * $unitPrice, 2, '.', ','),
     );

}

//send data to jasper
$data =  $params;
$jasper->setParams(array(
    'hosp_name' => $hospinfo['hosp_name']." ",
    'hosp_addr1' => $hospinfo['hosp_addr1']." ",
    'title' => "I S S U A N C E  S L I P ",
    'reqstd_area' => $arearqstd." ",
    'reqsting_area' => $arearqstng." ",
    'req_no' => $header['refno']." ",
    'issue_no' => $refnoissue." ",
    'req_date' => date("Y-m-d",strtotime($header['request_date']))." ",
    'request_position' => $requestname." ",
    'issueby' => $issuename." ",
    ));

$jasper->setJrxmlFilePath('Issuance_issue_slip.jrxml');
$jasper->setData($data);
$jasper->run();

?>