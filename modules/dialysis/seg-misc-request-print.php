<?php
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'/include/care_api_classes/class_drg.php');
$objDRG= new DRG;

include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;
    
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();

require_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person($pid);

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

require_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address();

global $db;



include_once($root_path."include/care_api_classes/class_order.php");
    $order_obj = new SegOrder("pharma");

include_once($root_path.'include/care_api_classes/or/class_segOr_miscCharges.php');
  $seg_ormisc = new SegOR_MiscCharges();

    $infoResult = $seg_ormisc->getOrderInfo($_REQUEST['ref']);
    if ($infoResult)    $info = $infoResult->FetchRow();
    



    include_once($root_path."include/care_api_classes/class_product.php");
    $prod_obj = new Product();

if ($_GET['report'] == 'acr_daily'){
    $date_span = date('M d,Y', strtotime($_GET['date']));
}else if($_GET['report'] == 'acr_monthly'){
    $tmp_date = strtotime($_GET['year'].'-'.$_GET['month'].'-01');
    $date_span = date('M',$tmp_date) . " " . date('Y',$tmp_date);
}

$objInfo = new Hospital_Admin();
if ($row = $objInfo->getAllHospitalInfo()) {
    $row['hosp_agency'] = strtoupper($row['hosp_agency']);
    $row['hosp_name']   = strtoupper($row['hosp_name']);
}
else {
    $row['hosp_country'] = "Republic of the Philippines";
    $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
    $row['hosp_name']    = "DAVAO MEDICAL CENTER";
    $row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
}
#--------------------------------------------------------------------------------------

$areaname = $db->GetOne("SELECT area_name FROM seg_pharma_areas WHERE area_code='".$info['pharma_area']."'");

   $type = ($info["is_cash"] == "1") ? "CASH" : "CHARGE";
   $stype = "MISCELLANOUS ORDER REQUEST ($type)";

    $urgent = "";
    if ($info["is_urgent"] == "1") {
       $urgent = "URGENT";
    }


$query = "";

$rs = $db->Execute($query);

$result = $seg_ormisc->getOrderItemsFullInfo($_REQUEST['ref']);

   $infoResult3 = $seg_ormisc->get_create_person($info['refno']);
    if ($infoResult3)    $info3 = $infoResult3->FetchRow();


   $infoResult4 = $seg_ormisc->get_modify_person($info['refno']);
    if ($infoResult4)    $info4 = $infoResult4->FetchRow();

$total = 0;

        $i = 0;
        while($res = $result->FetchRow()){
        $amount = $res['adjusted_amnt'];
        // $data[0]['amount']  = number_format($amount,2);
        $total += $amount;

             $data[$i] = array('item_no'=>$res['service_code'],
                              'desc'=>$res['name'],
                              'price'=>number_format($res['chrg_amnt'],2),
                              'qty'=>$res['quantity'],
                              'amount'=>number_format($amount,2)
                             );
        $data[$i]['total']  = number_format($total,2);
        $data[$i]['request']  = $info3['name'];

        if($info3['name'] != $info4['name']){
             $smodify = $info4['name'];
             $smodifylbl = "Edited by:";
        }
      
             $data[$i]['modify']  =   $smodify ;
             $data[$i]['modifylbl']  =    $smodifylbl;
        
            $i++;  

        }

        $infoResult4 = $seg_ormisc->getinsurance($info['pid']);

        while($res = $infoResult4->FetchRow()){
$firm_id .= $res['firm_id']." / ";
                          
        }


        $data[0]['firm_id']  = $firm_id;
        // $data[0]['areaname']  = $areaname;
        $data[0]['hosp_name']  = $row['hosp_name'];
        $data[0]['hosp_add']  = $row['hosp_addr1'];
        $data[0]['type']  = $stype;
        // $data[0]['urgent']  = $urgent;
        $data[0]['ref_no']  = $info['refno'];
        $data[0]['pid']  = $info['pid'];
        $data[0]['order_date']  = $info['orderdate'];
        $data[0]['case_no']  = $info['encounter_nr'];
        $data[0]['name']  = $info['ordername'];
        $data[0]['address']  = $info['orderaddress'];


#added by daryl
        // var_dump($order_obj->getpatientinfo($info['pid'], $info['encounter_nr']));die;
     $infoResult2 = $seg_ormisc->getpatientinfo($info['pid'], $info['encounter_nr']);

    if ($infoResult2)    $info2 = $infoResult2->FetchRow();



#ended by daryl
        $data[0]['ASS']  = $info2['age']." / ".strtoupper($info2['sex'])." / ".strtoupper($info2['civil']);
        $data[0]['room']  = $info2['room'];



  

showReport('Misc-print-request',$params,$data,"PDF");


?>