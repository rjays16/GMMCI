<?php                                                                
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/supply_office/ajax/issue.common.php");

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
0* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_prod_order_user';

global $db;

require_once($root_path.'include/inc_front_chain_lang.php');

# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$php_date_format = strtolower($date_format);
$php_date_format = str_replace("dd","d",$php_date_format);
$php_date_format = str_replace("mm","m",$php_date_format);
$php_date_format = str_replace("yyyy","Y",$php_date_format);
$php_date_format = str_replace("yy","y",$php_date_format);

$title=$LDPharmacy;
if (isset($_GET['from']) && (strcmp($_GET['from'], 'list') == 0)) 
    $breakfile=$root_path."modules/inventory/seg-trans-list.php".URL_APPEND."&userck=$userck&list=issuances";    
else if($_GET['from']=='phs')
        $breakfile=$root_path."modules/phs/seg-phs-function.php".URL_APPEND."&userck=$userck";
else    
    $breakfile=$root_path."modules/supply_office/seg-supply-functions.php".URL_APPEND."&userck=$userck";
    
//if (!$_GET['from'])
//    $breakfile=$root_path."modules/supply_office/seg-supply-functions.php".URL_APPEND."&userck=$userck";
//else {
//    if ($_GET['from']=='CLOSE_WINDOW')
//        $breakfile = "javascript:if (window.parent.myClick) window.parent.myClick(); else window.parent.cClick();";
//    else
//        $breakfile = $root_path.'modules/supply_office/seg-supply-functions.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
//}

$imgpath=$root_path."pharma/img/";
$thisfile='seg-issuance-edit.php';

//$enc = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)');

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 
# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
        
include_once($root_path."include/care_api_classes/class_order.php");
$order_obj = new SegOrder("pharma");

include_once($root_path."include/care_api_classes/inventory/class_issuance.php");
$issue_obj = new Issuance();

include_once($root_path."include/care_api_classes/class_personell.php");
$persnl_obj = new Personell();

include_once($root_path."include/care_api_classes/class_pharma_product.php");
$pp_obj = new SegPharmaProduct();

include_once($root_path."include/care_api_classes/inventory/class_item.php");
$itmobj = new Item();

include_once($root_path."include/care_api_classes/inventory/class_unit.php");
$unitobj = new Unit();

global $db;

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');
    
if ($_GET["from"]=="CLOSE_WINDOW") {
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);
}
    
# Title in the title bar
$smarty->assign('sToolbarTitle',"Supplies::Issuance::Edit");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Supplies::Issuance::Edit");

$user_location = $HTTP_SESSION_VARS['sess_user_personell_nr'];

if($HTTP_SESSION_VARS['sess_user_personell_nr']) {
    $sqlLOC = "SELECT location_nr FROM care_personell_assignment WHERE personell_nr=".$HTTP_SESSION_VARS['sess_user_personell_nr'];  
    $resultLOC = $db->Execute($sqlLOC);                                                            
    $rowLOC = $resultLOC->FetchRow();
    
    $persnl = $persnl_obj->getPersonellInfo($HTTP_SESSION_VARS['sess_user_personell_nr']); 
}
//echo $sqlLOC."<br>";
//echo "checkpass";
if (isset($_POST["submitted"])) {        
    $bulk = array();
    $total = 0;
    
    $issobj = new Issuance();     
    
    $data = array(
        'refno'=>$_POST["refno"],
        'issue_date'=>$_POST['issue_date'],
        'src_area_code'=>$_POST['area_issued'],
        'area_code'=>$_POST['area_dest'],
        'authorizing_id'=>$_POST['authorizing_id_hidden'],
        'issuing_id'=>$_POST['issuing_id_hidden'],
        'issue_type'=>$_POST['iss_type']
        );
    
    $issobj->prepareIssuance(); 
    $issobj->setDataArray($data);
    
    $issobj->startTrans();       
    
    if ($_POST['old_refno'] == '') {        
        // Insert new request ...        
        $saveok = $issobj->insertDataFromInternalArray();
    }
    else { 
        // Update old refno.
        $saveok = $issobj->delIssuanceDetails($_POST['old_refno'], $_POST['dateset']);
//        $saveok = $issobj->delIssDetails($_POST['old_refno']);
//        if ($saveok) $saveok = $issobj->delIssServed($_POST['old_refno']);
        if ($saveok) {
            $issobj->setWhereCondition("refno = '".$_POST['old_refno']."'");     
            $saveok = $issobj->updateDataFromInternalArray($_POST['old_refno'], FALSE);
        }                
    }                
   /*
   $sql =  "INSERT INTO seg_issuance (refno,issue_date,src_area_code,area_code,authorizing_id,issuing_id,issue_type) VALUES 
   ('".$_POST['refno']."','".$_POST['issue_date']."','".$_POST['area_issued']."','".$_POST['area_dest']."',".$_POST['authorizing_id_hidden'].",".$_POST['issuing_id_hidden'].",'".$_POST['iss_type']."')";  
   //echo  $sql."<br>";
   $result = $db->Execute($sql);
   $error = $db->ErrorMsg();
   $okba = $db->Affected_Rows();
   #echo "checkpoint1";
   */
   include_once($root_path."include/care_api_classes/inventory/class_inventory.php");
   $inventory_obj = new Inventory();
    #echo "checkpoint2";
    if ($saveok) {    
//        $counter=0;
//        $allqty=0;
        foreach($_POST["items"] as $i=>$v) {
            #echo "checkpass4"; 
//            $inventory_obj->setInventoryParams($i,$_REQUEST['area_issued']);             

            $inventory_obj->setInventoryParams($v, $_POST['area_issued'], $_POST["refno"], ISSUANCE); 

// --------------- commented out by LST --- 11.19.2009 --- not necessary anymore ... done in delIssuanceDetails method of class Issuance ....                                                                                        
//            if ($_POST['old_refno'] != '') {        
                // Update old refno. with inventory removed from UNedited version of transaction                
//                if($_POST['expdate'][$i]!="-") {   
//                    $saveok = $inventory_obj->addInventory($_POST['pending'][$i], $_POST['unitid'][$i], $_POST['expdate'][$i], NULL,$_POST['issue_date']);                 
//                }  
//                else if($_POST['serial'][$i]!="-"){
//                    $saveok = $inventory_obj->addInventory($_POST['pending'][$i], $_POST['unitid'][$i], NULL, $_POST['serial'][$i],$_POST['issue_date']);     
//                }  
//                else {
//                    $saveok = $inventory_obj->addInventory($_REQUEST['pending'][$counter], $_REQUEST['unitid'][$counter], NULL, NULL,$_REQUEST['issue_date']);
//                }                
//            }                   

            $allqty = $_POST['pending'][$i];
            $qtyperpack = $itmobj->getQtyPerBigUnit($v);
            
            if ($unitobj->isUnitIDBigUnit($_POST['unitid'][$i])) {
                $allqty = $allqty * $qtyperpack;                
            }
            
            $strSQL = "select rd.refno, sum(case when rd.is_unitperpc then rd.item_qty else ($qtyperpack * rd.item_qty) end) as rqty, 
                             ifnull(sum(case when id.is_unitperpc then id.item_qty else ($qtyperpack * id.item_qty)  end),0) as sqty 
                          from seg_internal_request_details as rd left join 
                                (seg_requests_served as rs inner join seg_issuance_details as id on rs.item_code = id.item_code and rs.issue_refno = id.refno)
                             on rd.refno = rs.request_refno and rd.item_code = rs.item_code 
                          where rd.item_code = '$v'
                          group by rd.refno having sum(case when rd.is_unitperpc then rd.item_qty else ($qtyperpack * rd.item_qty) end) > 0
                             and sum(case when rd.is_unitperpc then rd.item_qty else ($qtyperpack * rd.item_qty) end) > ifnull(sum(served_qty),0)
                          for update";
            if ($result = $db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                    $servedqty = 0;                     
                    while (($row = $result->FetchRow()) && ($allqty > 0)) {                       
                        $balqty = $row['rqty'] - $row['sqty'];                        
                        if ($allqty > $balqty) {
                            $allqty -= $balqty;
                            $balqty = $row['rqty'];  
                        }
                        else {
                            $balqty = $allqty; 
                            $allqty = 0;                             
                        }
                        
                        $servedqty += $balqty; 
                                                     
                        $fldArray = array('request_refno'=>"'{$row['refno']}'", 'issue_refno'=>"'{$_POST["refno"]}'", 'item_code'=>"'$v'", 'served_qty'=>"{$balqty}");
                        $saveok = $db->Replace('seg_requests_served', $fldArray, array('request_refno', 'issue_refno', 'item_code'));
                        
                        if (!$saveok) break;                                                
                    }                
                }
                else
                    $saveok = false;    
            }
            else
                $saveok = false;
            
            if ($saveok && ($servedqty > 0)) {
                $runqty = $servedqty; 
                
                $info_arr = array(); 
                            
                $strSQL = "select expiry_date, serial_no, eod_qty from seg_eod_inventory as e1 
                              where (item_code='$v' 
                                 and area_code = '{$_POST['area_issued']}'   
                                 and eod_date <= date('{$_POST['issue_date']}')
                                 and eod_date = (select max(eod_date) from seg_eod_inventory as e2 
                                                    where e2.item_code = e1.item_code 
                                                       and e2.area_code = e1.area_code
                                                       and e2.expiry_date = e1.expiry_date
                                                       and e2.serial_no = e1.serial_no))
                              order by expiry_date, serial_no for update";                
                if ($result = $db->Execute($strSQL)) {
                    while ($row = $result->FetchRow()) {
                        $nqty = $row['eod_qty'];                                      
                                            
                        if ($runqty > $nqty) {
                            $runqty -= $nqty;
                        }
                        else {
                            $nqty = $runqty;
                            $runqty = 0;                            
                        }
                        
                        if ($unitobj->isUnitIDBigUnit($_POST['unitid'][$i])) {
                            $nqty = $nqty / $qtyperpack;                
                        }                                                                                    
                        $fldArray = array('refno'=>"'{$_POST["refno"]}'", 'item_code'=>"'$v'", 'item_qty'=>"{$nqty}", 'unit_id'=>"{$_POST['unitid'][$i]}", 
                                          'is_unitperpc'=>"{$_POST['perpc'][$i]}", 'expiry_date'=>"'{$row['expiry_date']}'", 'serial_no'=>"'{$row['serial_no']}'", 
                                          'avg_cost'=>"{$_POST['avg'][$i]}");
                        $saveok = $db->Replace('seg_issuance_details', $fldArray, array('refno', 'item_code', 'serial_no', 'expiry_date'));
                        
                        if ($saveok) {
                            $saveok = $inventory_obj->remInventory($nqty, $_POST['unitid'][$i], $row['expiry_date'], $row['serial_no'], $_POST['issue_date']);
                            
                            if ($saveok) {
                                $prodInformation = $pp_obj->getProductInfo($_POST['items'][$i]);
            
                                if($prodInformation['prod_class'] == 'E'){
                                    $saveok = $issue_obj->setCustodianDetails($_POST['refno'], $v, $row['expiry_date'], $row['serial_no'], $_POST['epropno'][$i], $_POST['eestlife'][$i]);    
                                }                                                            
                            }
                        }                         
                        
                        if (!$saveok) break;
                        if ($runqty == 0) break;
                    } 
                }                                                                                                                                                                                                                                      
            }  
                  
// ------------- commented out by LST --- 11.19.2009 ------------------ replaced with simplified algorithm above --------------------                        
//            $allqty = $_REQUEST['pending'][$counter];
//            $thissql = "SELECT is_unit_per_pc FROM seg_unit WHERE unit_id=".$_POST['unitid'][$counter];
//            $thisresult = $db->Execute($thissql); 
//            $rowsql = $thisresult->FetchRow();
//            
//            if($rowsql['is_unit_per_pc']=='0') {
//                $sqlSTR="SELECT qty_per_pack FROM seg_item_extended WHERE item_code = '$i'";
//                $myresult = $db->Execute($sqlSTR);
//                $myrow=$myresult->FetchRow();
//                $allqty = $allqty * $myrow["qty_per_pack"];  
//            }
//            
            //$fetchRequestedRefno = "SELECT DISTINCT a.request_refno FROM seg_requests_served as a, seg_internal_request as b WHERE (a.request_refno=b.refno AND a.item_code = '$i' AND b.area_code_dest='".$_REQUEST['area_issued']."' AND b.area_code='".$_REQUEST['area_dest']."') ORDER BY b.request_date ASC";
//            $fetchRequestedRefno = "SELECT DISTINCT a.refno FROM seg_internal_request_details as a JOIN seg_internal_request as b ON a.refno=b.refno WHERE (a.item_code = '$i' AND b.area_code_dest='".$_REQUEST['area_issued']."' AND b.area_code='".$_REQUEST['area_dest']."') ORDER BY b.request_date ASC"; 
//            $resultRequestedRefno = $db->Execute($fetchRequestedRefno);
//            while($rowRequestedRefno=$resultRequestedRefno->FetchRow()) {
//            
//                $sqlqty = "SELECT * from seg_internal_request as a
//                        JOIN seg_internal_request_details as b ON a.refno=b.refno
//                        JOIN seg_requests_served as c ON a.refno=c.request_refno
//                        JOIN seg_item_extended as d ON d.item_code=b.item_code
//                        WHERE (b.item_code='$i' AND b.item_code=c.item_code AND a.area_code_dest='".$_REQUEST['area_issued']."' AND a.area_code='".$_REQUEST['area_dest']."' AND a.refno='".$rowRequestedRefno['refno']."')
//                        ORDER BY a.request_date ASC";
//                $resultqty = $db->Execute($sqlqty);
                //echo $allqty." , ".$sqlqty."<br>";
//                if(!$resultqty->EOF){
//                    if($rowqty = $resultqty->FetchRow()){

//                        if($allqty>0){
//                            
//                            $requested_qty = $rowqty["item_qty"];
//                            $totalserved_qty = 0;
//                            
//                            if($rowqty["is_unitperpc"]=='0'){
//                                $requested_qty = $requested_qty * $rowqty["qty_per_pack"]; 
//                            }
//                            
//                            $fetchAllServed="SELECT served_qty from seg_requests_served WHERE (request_refno='".$rowqty['request_refno']."' AND item_code='".$rowqty['item_code']."')";
//                            $resultAllServed = $db->Execute($fetchAllServed);
//                            while($rowAllServed = $resultAllServed->FetchRow()){
//                                $totalserved_qty += $rowAllServed['served_qty']; 
//                            }
//                            #start db trans                    
//                            $db->StartTrans();
//                            $bSuccess = FALSE;
//                            
//                            $kulang = $requested_qty - $totalserved_qty;
//                        
//                            if($kulang <= $allqty)
//                            {
//                                 if($rowqty['issue_refno']!='' && $kulang!=0){
//                                    $sql101 = "INSERT INTO seg_requests_served (request_refno,issue_refno,item_code,served_qty) VALUES ('".$rowqty['request_refno']."','".$_POST['refno']."','".$rowqty['item_code']."',$kulang)";
//                                    $bSuccess = $db->Execute($sql101);
//                                 }
//                                 else {
//                                     if($kulang!=0)
//                                     {
//                                         $sql100 = "UPDATE seg_requests_served SET served_qty=$kulang,issue_refno='".$_POST['refno']."' WHERE (item_code='".$rowqty['item_code']."' AND request_refno='".$rowqty['request_refno']."')"; 
//                                         $bSuccess = $db->Execute($sql100);
//                                     }
//                                 }
//                                 $allqty = $allqty - $kulang;
//                            }
//                            else
//                            {
//                                 if($rowqty['issue_refno']!='' && $allqty!=0){
//                                     $sql101 = "INSERT INTO seg_requests_served (request_refno,issue_refno,item_code,served_qty) VALUES ('".$rowqty['request_refno']."','".$_POST['refno']."','".$rowqty['item_code']."',$allqty)";
//                                     $bSuccess = $db->Execute($sql101);
//                                 }
//                                 else {
//                                     if($allqty!=0)
//                                     {
//                                         $sql100 = "UPDATE seg_requests_served SET served_qty=$allqty,issue_refno='".$_POST['refno']."' WHERE (item_code='".$rowqty['item_code']."' AND request_refno='".$rowqty['request_refno']."')"; 
//                                         $bSuccess = $db->Execute($sql100);
//                                     }
//                                 }
//                                 $allqty = $allqty - $allqty;
//                            }
//                            
//                            if ($bSuccess){
//                                $db->CompleteTrans();
//                            }
//                            else {
//                                $db->FailTrans();
//                                $db->CompleteTrans(); 
//                            }

//                        }    
//                    }
//                }
//                else {
//                    if($allqty > 0)
//                    {
//                        $db->StartTrans();
//                        $bSuccess = FALSE;
//                        $fetchRequested = "select * from seg_internal_request as a JOIN seg_internal_request_details as b on a.refno=b.refno WHERE a.refno='".$rowRequestedRefno['refno']."'";
//                        $resultRequested = $db->Execute($fetchRequested);
//                        if($rowRequested = $resultRequested->Fetchrow())
//                        {
//                            $requested_qty = $rowRequested["item_qty"];
//                            
//                            if($rowqty["is_unitperpc"]=='0'){
//                                $requested_qty = $requested_qty * $rowRequested["qty_per_pack"]; 
//                            }
//                            
//                            if($allqty >= $requested_qty) 
//                            {
//                                $toadd = $requested_qty;
//                            
//                                $sql101 = "INSERT INTO seg_requests_served (request_refno,issue_refno,item_code,served_qty) VALUES ('".$rowRequestedRefno['refno']."','".$_POST['refno']."','$i',$toadd)";
//           
//                                $bSuccess = $db->Execute($sql101);
//                                $allqty = $allqty - $requested_qty;
//                            }
//                            else
//                            {
//                                $toadd = $allqty;
//                            
//                                $sql101 = "INSERT INTO seg_requests_served (request_refno,issue_refno,item_code,served_qty) VALUES ('".$rowRequestedRefno['refno']."','".$_POST['refno']."','$i',$toadd)";
//           
//                                $bSuccess = $db->Execute($sql101);
//                                $allqty = $allqty - $allqty;   
//                            }
//                            
//                        }
//                        
//                        if ($bSuccess){
//                            $db->CompleteTrans();
//                        }
//                        else {
//                            $db->FailTrans();
//                            $db->CompleteTrans(); 
//                        }
//                    } 
//                } 
//            }

//            if($_REQUEST['expdate'][$counter]!="-"){
//                $resultExp = $expiry_obj->getExpiriesofItem($i, $_REQUEST['area_issued']);
//                if($resultExp){
//                    while($rowExp = $resultExp->FetchRow() && $isscounter>0){
//                        $expiryqty = $eod_obj->getCurrentEODQty($i, $_REQUEST['area_issued'], $_POST['issue_date'], $rowExp['expiry_date']);
//                        
//                        if($expiryqty >= $isscounters){
//                            $sql2 = "INSERT INTO seg_issuance_details (refno,item_code,item_qty,unit_id,is_unitperpc,expiry_date,avg_cost) VALUES ('".$_POST['refno']."','".$_POST['items'][$counter]."',".$_POST['pending'][$counter].",".$_POST['unitid'][$counter].",".$_POST['perpc'][$counter].",'".$rowExp['expiry_date']."',".$_POST['avg'][$counter].")";  
//                            $inventory_obj->remInventory($_REQUEST['pending'][$counter], $_REQUEST['unitid'][$counter], $rowExp['expiry_date'], NULL,$_REQUEST['issue_date']);
//                            $isscounters = 0;
//                        }
//                        else{
//                            $sqlSTR="SELECT pc_unit_id FROM seg_item_extended WHERE item_code = '$i'";
//                            $myresult2 = $db->Execute($sqlSTR);
//                            $myrow2=$myresult2->FetchRow();
//                            
//                            $sqlexp = "INSERT INTO seg_issuance_details (refno,item_code,item_qty,unit_id,is_unitperpc,expiry_date,avg_cost) VALUES ('".$_POST['refno']."','".$_POST['items'][$counter]."',".$expiryqty.",".$myrow2['pc_unit_id'].",".$_POST['perpc'][$counter].",'".$rowExp['expiry_date']."',".$_POST['avg'][$counter].")";  
//                            $inventory_obj->remInventory($expiryqty, $myrow2['pc_unit_id'], $rowExp['expiry_date'], NULL,$_REQUEST['issue_date']);
//                        
//                            $db->StartTrans();
//                           $bSuccess = FALSE;
//                 
//                           $bSuccess = $db->Execute($sqlexp);
//                           
//                            if ($bSuccess){

//                                $db->CompleteTrans();
//                            }
//                            else {

//                                $db->FailTrans();
//                                $db->CompleteTrans();
//                            }
//                        } 
//                    }
//                }
//                 
//            }  
//            else if($_REQUEST['serial'][$counter]!="-"){
//                
//                $inventory_obj->setSerialObject('1',0.00,'2008-01-01',100);
//                $sql2 = "INSERT INTO seg_issuance_details (refno,item_code,item_qty,unit_id,is_unitperpc,serial_no,avg_cost) VALUES ('".$_POST['refno']."','".$_POST['items'][$counter]."',".$_POST['pending'][$counter].",".$_POST['unitid'][$counter].",".$_POST['perpc'][$counter].",'".$_REQUEST['serial'][$counter]."',".$_POST['avg'][$counter].")";
//    
//                $inventory_obj->remInventory($_REQUEST['pending'][$counter], $_REQUEST['unitid'][$counter], NULL, $_REQUEST['serial'][$counter],$_REQUEST['issue_date']);     
//            }  
//            else { 
//                $sql2 = "INSERT INTO seg_issuance_details (refno,item_code,item_qty,unit_id,is_unitperpc,avg_cost) VALUES ('".$_POST['refno']."','".$_POST['items'][$counter]."',".$_POST['pending'][$counter].",".$_POST['unitid'][$counter].",".$_POST['perpc'][$counter].",".$_POST['avg'][$counter].")";
//                $inventory_obj->remInventory($_REQUEST['pending'][$counter], $_REQUEST['unitid'][$counter], NULL, NULL,$_REQUEST['issue_date']);
//            }
//           
//           $db->StartTrans();
//           $bSuccess = FALSE;
           //echo  $sql2."yeah"; 
//           $bSuccess = $db->Execute($sql2);
//           
//            if ($bSuccess){
                //echo "1";
//                $db->CompleteTrans();
//            }
//            else {
                //echo "2";
//                $db->FailTrans();
//                $db->CompleteTrans();
//            }
//          
//           $error2 = $db->ErrorMsg();
//           $okba2 = $db->Affected_Rows();
//           $counter++; 
// ------------- commented out by LST --- 11.19.2009 ------------------ replaced with simplified algorithm above --------------------             
            
        }
        #########################
        
        if (!$saveok) $issue_obj->failTrans();
        $issue_obj->completeTrans();         
        
        $smarty->assign('sMsgTitle','Supply issuance successfully saved!');
        $smarty->assign('sMsgBody','The issue details have been saved into the database...');
        $sBreakImg ='close2.gif';
        $smarty->assign('sBreakButton','<img class="segSimulatedLink" '.createLDImgSrc($root_path,$sBreakImg,'0','absmiddle').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
        
        #print_r($_REQUEST);
        
        # Assign submitted form values
        $smarty->assign('sIssueDate', $_REQUEST['issue_date']);
    //    $smarty->assign('sRefNo', $data['refno']);
        $smarty->assign('sRefNo', $_REQUEST['refno']);
        
        $smarty->assign('sAuthBy', $_REQUEST['authorizing_id']);
        $smarty->assign('sIssBy', $_REQUEST['issuing_id']);
        
        # need to edit
        $fetchAreaFromDepartment = "SELECT area_name FROM seg_areas WHERE area_code='".$_POST['area_issued']."'";
        $areaResult = $db->Execute($fetchAreaFromDepartment);
        $areaRow = $areaResult->FetchRow();
        
        $smarty->assign('sArea', $areaRow['area_name']);
        
        # need to edit
        $fetchAreaFromDepartment = "SELECT area_name FROM seg_areas WHERE area_code='".$_POST['area_dest']."'";
        $areaResult = $db->Execute($fetchAreaFromDepartment);
        $areaRow = $areaResult->FetchRow();
        
        $smarty->assign('sSrcArea', $areaRow['area_name']);
          
        foreach ($_REQUEST['items'] as $i=>$v){            
            $items_table[] = "<tr><td>".$_REQUEST['items'][$i]."</td><td>".$_REQUEST['name'][$i]."</td><td>". $_REQUEST['pending'][$i] ."</td><td>". $_REQUEST['unitdesc'][$i] ."</td><td>". $_REQUEST['serial'][$i] ."</td><td>". $_REQUEST['expdate'][$i] ."</td></tr>";                    
        }

        $show_items = implode("",$items_table);
        $smarty->assign('sItems',$show_items);
        
        $smarty->assign('sMainBlockIncludeFile','supply_office/oksave.tpl');
        $smarty->display('common/mainframe.tpl');
        exit;
    }
    else {
        $errorMsg = $db->ErrorMsg();
        if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
            $smarty->assign('sysErrorMessage','<strong>Error:</strong> An Issuance with the same Ref number already exists in the database.');
        else {                
//            $smarty->assign('sysErrorMessage',"<strong>Error:</strong> $errorMsg");{$issobj->sql}
            $smarty->assign('sysErrorMessage',"<strong>Error:</strong> Error in saving!");
        }
    }
}

# Assign Body Onload javascript code
$onLoadJS="onload=\"init()\"";
$smarty->assign('sOnLoadJs',$onLoadJS);
#$smarty->assign('bShowQuickKeys',!$_REQUEST['viewonly']);
$smarty->assign('bShowQuickKeys',FALSE);

# Collect javascript code
ob_start();
     # Load the javascript code
?>
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<!-- YU Library -->
<script type="text/javascript" src="<?=$root_path?>js/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/event/event.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dom/dom.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/connection/connection.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dragdrop/dragdrop.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/container/container_core.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/container/container.js"></script>
<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/container/assets/container.css">
<script type="text/javascript" src="js/issue-gui.js?t=<?=time()?>"></script>



<!-- START for setting the DATE (NOTE: should be IN this ORDER) -->
<script type="text/javascript" language="javascript">
<?php
    require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
 <script type="text/javascript" src="<?=$root_path?>js/gen_routines.js"></script>

<script type="text/javascript" language="javascript">
<!--
    var trayItems = 0;
    
    function init() {
      //  refreshDiscount();
    }
    
    function keyF2() {
        openOrderTray();
    }
    
    function keyF3() {
        if (confirm('Clear the issue list?'))    emptyTray();
    }
    
    function keyF9() {

//        if (warnClear()) { 
//            emptyTray(); 
        overlib(
        OLiframeContent('issue-select-personnel.php',
                700, 400, 'select_personnel', 0, 'no'),
        WIDTH,700, TEXTPADDING,0, BORDER,0,
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
        CAPTIONPADDING,2, 
                CAPTION,'Select registered personnel',
        MIDX,0, MIDY,0, 
        STATUS,'Select registered personnel'); 
//        } 
        return false;
    }
    
    function keyF10() {
        
        
        $('issuing_id').setAttribute('value',''); 
       
        
        callback = self.setInterval("checker()", 1);
        
         $('issuing_id_hidden').setAttribute('value','');
        

        if (warnClear()) { 
            emptyTray(); overlib(
        OLiframeContent('issue-select-personnel2.php',
                700, 400, 'select_personnel', 0, 'no'),
        WIDTH,700, TEXTPADDING,0, BORDER,0,
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
        CAPTIONPADDING,2, 
                CAPTION,'Select registered personnel',
        MIDX,0, MIDY,0, 
        STATUS,'Select registered personnel'); 
        
        } 

        return false;
    }

    function keyF12() {
        if (validate()) document.inputform.submit()
    }
    function openOrderTray() {
        var area = "ALL";
        var area_destination ="ALL";
        area = $('area_issued').value;
        area_destination = $('area_dest').value; 
        //alert(area);
        var url = 'seg-issue-tray.php?arealimit='+area+'&arealimitdest='+area_destination;
        overlib(
            OLiframeContent(url, 660, 420, 'fOrderTray', 0, 'no'),
            WIDTH,660, TEXTPADDING,0, BORDER,0, 
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
            CAPTIONPADDING,2, 
            CAPTION,'Add Item for Issuance tray',
            MIDX,0, MIDY,0, 
            STATUS,'Add Item for Issuance tray');
        return false
    }
    
    function validate() {
        if (!$('refno').value) {
            alert("Please enter the reference no.");
            $('refno').focus();
            return false;
        }
        if (!$('authorizing_id').value) {
            alert("Please select a registered person for authorization using the person search function...");
            return false;
        }
        if (!$('issuing_id').value) {
            alert("Please select a registered person for issuance using the person search function...");
            return false;
        }
        if (document.getElementsByName('items[]').length==0) {
            alert("Item list is empty...");
            return false;
        }
        return confirm('Process this supply issuance?');
    }
-->
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$lastnr = $order_obj->getLastNr(date("Y-m-d"));

//if ($_REQUEST['encounterset']) {
//    $person = $order_obj->getPersonInfoFromEncounter($_REQUEST['encounterset']);
//}

if (isset($_GET["refno"])) {   
    $smarty->assign('sIssueItems',"
                <tr>
                    <td colspan=\"8\">Issue list is currently empty...</td>
                </tr>");
    
    // Populate the header array and details ...
    if (!isset($issue_obj)) $issue_obj = new Issuance();            
        
    if ($result = $issue_obj->getIssuanceHeader($_GET["refno"])) {                               
        $lastnr = $result["refno"]; 
        $_POST['refno'] = $lastnr;      
        $_REQUEST['dateset'] = $result["issue_date"];
//        $_POST['orderdate'] = $result["issue_date"];
        //echo "dateset: ".$_REQUEST['dateset']." orderdate: ".$_POST['orderdate'];
        $pid = $result["issuing_id"];
        $name = $result["issuer"];                
        $_POST['area_issued'] = $result["src_area_code"]; 
        $_POST['area_dest'] = $result["area_code"];
        $_POST['authorizing_id_hidden'] = $result['authorizing_id'];
        
        $rowperson = $persnl_obj->get_Person_name($result['authorizing_id']);
        $_POST['authorizing_id'] = $rowperson['dr_name'];
                        
        if ($result = $issue_obj->getIssuanceDetails($_GET["refno"])) {                    
            $script1 = '<script type="text/javascript" language="javascript">';                     
            $items1 = array();
            $unitids1 = array();
            $is_pcs1  = array();
            $qtys1    = array();
            $expiries1  = array();
            $serials1    = array();
            $item_names1 = array();
            $unitnames1 = array();
            $descs1 = array();
            $avg1 = array();
            if ($result->RecordCount()) {                
                while ($row = $result->FetchRow()) {  

                    $items1[]   = $row["item_code"];
                    $unitids1[] = $row["unit_id"];
                    $is_pcs1[]  = $row["is_unitperpc"];
                    $qtys1[]    = $row["item_qty"];
 
                    $expiries1[]= "-";
                    $serials1[] = "-"; 
                    $prod_sample = $pp_obj->getProductInfo($row["item_code"]);
                    $item_names1[] = $prod_sample['artikelname'];
                    $unitnames1[] = $unitobj->getUnitName($row["unit_id"]);
                    $descs1[] = "";
                    $avg1[] = $row["avg_cost"]; 
                }                                
          
                $script1 .= "var items01 =['" .implode("','",$items1)."'];";
                $script1 .= "var units01 =[" .implode(",",$unitids1). "];";
                $script1 .= "var ispcs01 =[" .implode(",",$is_pcs1). "];";
                $script1 .= "var qtys01  =[" .implode(",",$qtys1). "];";
                $script1 .= "var expdate01 =['" .implode("','",$expiries1). "'];";
                $script1 .= "var serial01  =['" .implode("','",$serials1). "'];";
                $script1 .= "var item_name01 =['" .implode("','",$item_names1). "'];";
                $script1 .= "var unitdesc01  =['" .implode("','",$unitnames1). "'];";
                $script1 .= "var desc01  =['" .implode("','",$descs1). "'];";
                $script1 .= "var avg01  =[" .implode(",",$avg1). "];";

                $script1 .= "xajax_add_item(items01, item_name01, desc01, qtys01, units01, ispcs01, unitdesc01, expdate01, serial01, avg01);";
                $script1 .= "</script>";
                $src1 = $script1;   
            }
            else {
                $smarty->assign('sIssueItems',"<tr>
                                                    <td colspan=\"8\">Issuance list is currently empty ...</td>
                                                 </tr>"); 
            }
            if ($src1) $smarty->assign('sIssueItems',$src1);        
        } 
    }
}
else {
    $pid  = $_SESSION['sess_user_personell_nr'];
    $name = $_SESSION['sess_login_username'];
}

# Render form elements
    $submitted = isset($_POST["submitted"]);
//    $readOnly = ($submitted && (!$_POST['iscash'] || $_POST['pid'])) ? 'readonly="readonly"' : "";

//    if ($person) {
//        $_POST['pid'] = $person['pid'];
//        $_POST['encounter_nr'] = $person['encounter_nr'];
//        $_POST['ordername'] = $person['name_first']." ".$person['name_last'];
//        
//        $addr = implode(", ",array_filter(array($person['street_name'], $person["brgy_name"], $person["mun_name"])));
//        if ($person["zipcode"])
//            $addr.=" ".$person["zipcode"];
//        if ($person["prov_name"])
//            $addr.=" ".$person["prov_name"];
//        $_POST['orderaddress'] = $addr;
//        $_POST['discount_id'] = $person['discount_id'];
//        $_POST['discount'] = $person['discount'];
//    }
    
//    require_once($root_path.'include/care_api_classes/class_product.php');
//    $prod_obj=new Product;
//    $prod=$prod_obj->getAllPharmaAreas();
//    $disabled = (strtolower($_GET['area']) != 'all') ? ' disabled="disabled"' : '';
//    $index = 0;
//    $count = 0;
//    $select_area = '';
//    while($row=$prod->FetchRow()){
//        $checked=strtolower($row['area_code'])==strtolower($_GET['area']) ? 'selected="selected"' : "";
//        $select_area .= "    <option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
//        if ($checked) $index = $count;
//        $count++;
//    }
    
    $lastnrthis = $issue_obj->getLastNr(date("Y-m-d"));
    
    //$smarty->assign('sRefno','<input id="refno" name="refno" type="text" value="'.($submitted && !$saveok ? $_POST['refno'] : $lastnrthis).'"/>');
    $smarty->assign('sRefno','<input class="jedInput" id="refno" name="refno" type="text" size="10" value="'.((($submitted && !$saveok) || (isset($_GET["refno"]))) ? $lastnr : $lastnrthis).'" style="font:bold 12px Arial"/>'); 
    $smarty->assign('sResetRefNo','<input class="jedButton" type="button" value="Reset" onclick="xajax_reset_referenceno()"/>');                            
      
    $dbtime_format = "Y-m-d H:i";
    $fulltime_format = "F j, Y g:ia";
    if ($_REQUEST['dateset']) {
        //$curDate = date($dbtime_format,$_REQUEST['dateset']);
        //$curDate_show = date($fulltime_format, $_REQUEST['dateset']);
        $curDate = date($dbtime_format, strtotime($_REQUEST['dateset']));
        $curDate_show = date($fulltime_format, strtotime($_REQUEST['dateset']));
    }
    else {
        $curDate = date($dbtime_format);
        $curDate_show = date($fulltime_format);
    }
    
    $smarty->assign('sIssueDate','<span id="show_issuedate" class="jedInput" style="margin-left:0px; margin-top:3px; font-weight:bold; color:#0000c0; padding:0px 2px;width:80px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['issue_date'])) : $curDate_show).'</span><input class="jedInput" name="issue_date" id="issue_date" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['issue_date'])) : $curDate).'" style="font:bold 12px Arial">');
    $smarty->assign('sIssueCalendar','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="issuedate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:0px;cursor:pointer">');
        $jsCalScript = "<script type=\"text/javascript\">
            Calendar.setup ({
                displayArea : \"show_issuedate\",
                inputField : \"issue_date\",
                ifFormat : \"%Y-%m-%d %H:%M\", 
                daFormat : \"    %B %e, %Y %I:%M%P\", 
                showsTime : true, 
                button : \"issuedate_trigger\", 
                singleClick : true,
                step : 1
            });
        </script>";
    $smarty->assign('jsCalendarSetup', $jsCalScript); 
    

    ############################################
    
    require_once($root_path.'include/care_api_classes/class_access.php');        
    require_once($root_path.'include/care_api_classes/class_department.php');
    
    $obj = new Access();    
    $dept_nr = $obj->getDeptNr($_SESSION['sess_temp_userid']); 
    
    $per_arr = explode(" ", $HTTP_SESSION_VARS['sess_permission']);

    if (in_array("System_Admin", $per_arr) || in_array("_a_0_all", $per_arr)) {
        $dept_nr = "''";
    }
    else {
        $dept_nr = "''";
    }

    $objdept = new Department();    
    
    #$subdepar = $objdept->getSubDept($dept_nr);  
    $qry = "SELECT fn_get_children_dept(".$dept_nr.") as dps";
    $rs = $db->Execute($qry);
    
    if($rs){
        $row =  $rs->FetchRow();
        $depscomma = $row["dps"];
        if (empty($depscomma)){
//            $dept_nr = '';
            $result = $objdept->getAreasInDept($dept_nr);   
        }
        else
            $result = $objdept->getAreasInADept($depscomma);
        }  

    $count = 0;    
    $s_areacode = '';
    if ($result) {
        while($row=$result->FetchRow()){
            $checked=(strtolower($row['area_code'])==strtolower($_GET['ori_area'])) || (strtolower($row['area_code']) == strtolower($_POST['area_issued'])) ? 'selected="selected"' : "";
            $ori_area .= "<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
            
            if ($checked || ($count == 0)) $s_areacode = $row['area_code'];                                    
            if ($checked) $index = $count;
            $count++;            
        }
    }
    else
        $ori_area = "<option value=\"\" $checked>- Assigned department has no areas -</option>\n";
    
    $ori_area = '<select class="jedInput" id="area_issued" name="area_issued" onchange="jsRqstngAreaOptionChngIss(this, this.options[this.selectedIndex].value);">'."\n".$ori_area."</select>\n".
    //$ori_area = '<select class="jedInput" id="area_issued" name="area_issued" onchange="alert(this.options[this.selectedIndex].value);">'."\n".$ori_area."</select>\n".
                "<input type=\"hidden\" id=\"area2\" name=\"area2\" value=\"".$_GET['area_issued']."\"/>";
    $smarty->assign('sAreaIssued',$ori_area);    
    
    //dest    
    $result = $objdept->getAllAreas($s_areacode);
    if ($result) {
        while($row=$result->FetchRow()){
            $checked=(strtolower($row['area_code'])==strtolower($_GET['area_dest'])) || (strtolower($row['area_code']) == strtolower($_POST['area_dest'])) ? 'selected="selected"' : "";
            $dest_area .= "<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
            if ($checked) $index = $count;
            $count++;
        }
        $dest_area = '<select class="jedInput" id="area_dest" name="area_dest" onchange="openOrderTray();">'."\n".$dest_area."</select>\n".
            "<input type=\"hidden\" id=\"area3\" name=\"area3\" value=\"".$_GET['area_dest']."\"/>";
        $smarty->assign('sAreaDest',$dest_area);
    }
    
    //issuance type
    $result = $issue_obj->getIssueType();
    $iss = "";
    if ($result) {
        while($row=$result->FetchRow()){
            $checked=(strtolower($row['iss_type_id'])==strtolower($_GET['iss_type'])) || (strtolower($row['area_code']) == strtolower($_POST['iss_type'])) ? 'selected="selected"' : "";
            $iss .= "<option value=\"".$row['iss_type_id']."\" $checked>".$row['iss_type_name']."</option>\n";
            if ($checked) $index = $count;
            $count++;
        }
        $issuetypes = '<select class="jedInput" id="iss_type" name="iss_type" >'."\n".$iss."</select>\n";
        $smarty->assign('sIssuanceType',$issuetypes);
    }
    
    
    ############################################
       
    $smarty->assign('sAuthorizedId','<input id="authorizing_id" name="authorizing_id" readonly="readonly" type="text" value="'.$_POST['authorizing_id'].'" size="35"/>');
    $smarty->assign('sAuthorizedButton','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer"
       onclick="keyF9()"
       onmouseout="nd();" />');  
    
    $smarty->assign('sIssuingId','<input id="issuing_id" name="issuing_id"  readonly="readonly" valign="absmiddle" type="text" value="'.$HTTP_SESSION_VARS['sess_login_username'].'" size="35" /> ');
    /*commented out by bryan on feb 20,2009
    $smarty->assign('sIssueButton','<img id="select-enc1" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer;"
       onclick="keyF10()"
       onmouseout="nd();" />');  
    */
    

# LINGAP/CMAP
//if (true) {
//    $sponsorHTML = '<select class="jedInput" name="sponsor" id="sponsor">
//<option value="" style="font-weight:bold">No coverage</option>
//';
//    include_once($root_path."include/care_api_classes/class_sponsor.php");
//    $sc = new SegSponsor();
//    $sponsors = $sc->get();
//    while($row=$sponsors->FetchRow()){
//        $sponsorHTML .= "                                    <option value=\"".$row['sp_id']."\">".$row['sp_name']."</option>\n";
//    }
//    $sponsorHTML .= "                    </select>";
//    $smarty->assign('sSponsor',$sponsorHTML);
//}

//$smarty->assign('sSWClass',($_POST['discountid'] ? $_POST['discountid'] : 'None'));
//$smarty->assign('sNormalPriority','<input class="jedInput" type="radio" name="priority" id="p0" value="0" '.(($_POST["priority"]!="1")?'checked="checked" ':'').'/><label class="jedInput" for="p0">Normal</label>');
//$smarty->assign('sUrgentPriority','<input class="jedInput" type="radio" name="priority" id="p1" value="1" '.(($_POST["priority"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="p1">Urgent</label>');
//$smarty->assign('sComments','<textarea class="jedInput" name="comments" cols="14" rows="2" style="float:left; margin-left:3px;margin-top:3px">'.$_POST['comment'].'</textarea>');
/*
    if ($_REQUEST['billing'])
        $smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="opacity:0.2"/>');
    else
        $smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer" onclick="keyF9()" onmouseout="nd();" />');
*/
$smarty->assign('sRootPath',$root_path);

$smarty->assign('sBtnAddItem','<img class="segSimulatedLink" id="add-item" src="'.$root_path.'images/btn_additems.gif" border="0" onclick="return openOrderTray();">');
$smarty->assign('sBtnEmptyList','<img class="segSimulatedLink" id="clear-list" src="'.$root_path.'images/btn_emptylist.gif" border="0" onclick="if (confirm(\'Clear the issuance list?\')) emptyTray()"/>');

$smarty->assign('sDiscountShow','<input type="checkbox" name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' onclick="seniorCitizen()"><label class="jedInput" for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');

/*
$smarty->assign('sDiscountInfo','<img src="'.$root_path.'images/discount.gif">');
$smarty->assign('sBtnDiscounts','<input class="segInput" type="image" id="btndiscount" src="'.$root_path.'images/btn_discounts.gif"
       onclick="overlib(
        OLiframeContent(\'seg-order-discounts.php\', 380, 125, \'if1\', 1, \'auto\'),
        WIDTH,380, TEXTPADDING,0, BORDER,0, 
                STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
                CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
                CAPTION,\'Change discount options\',
        REF,\'btndiscount\', REFC,\'LL\', REFP,\'UL\', REFY,2, 
        STATUS,\'Change discount options\'); return false;"
       onmouseout="nd();">');
*/
#$smarty->assign('sBtnPDF','<a href="#"><img src="'.$root_path.'images/btn_printpdf.gif" border="0"></a>');
/*
    $jsCalScript = "<script type=\"text/javascript\">
        Calendar.setup ({
            inputField : \"orderdate\", ifFormat : \"$phpfd\", showsTime : false, button : \"orderdate_trigger\", singleClick : true, step : 1
        });
    </script>
    ";
$smarty->assign('jsCalendarSetup', $jsCalScript);*/
    
if($error=="refno_exists"){
    $smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
    $smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}

$qs = "";
if ( $_GET['pid'] ) $qs .= "&pid=".$_GET['pid'];
//if ( $_GET['encounterset'] ) $qs .= "&encounterset=".$_GET['encounterset'];

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.$qs.'&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

?>
  <input type="hidden" name="submitted" value="1" />
  <input type="hidden" name="sid" value="<?php echo $sid?>">
  <input type="hidden" name="lang" value="<?php echo $lang?>">
  <input type="hidden" name="cat" value="<?php echo $cat?>">
  <input type="hidden" name="userck" value="<?php echo $userck?>">  
  <input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
  <input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
  <input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
  <input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
  <input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
  <input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
  <input type="hidden" name="target" value="<?php echo $target ?>">
  
  <input id="discount" name="discount" type="hidden" value="'.$_POST["discount"].'"/>
  <input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>
    
    <input id="authorizing_id_hidden" name="authorizing_id_hidden" type="hidden" value="<?= $_REQUEST['authorizing_id_hidden'] ?>"/>
    <input id="issuing_id_hidden" name="issuing_id_hidden" type="hidden" value="<?= $_SESSION['sess_user_personell_nr'] ?>"/>
    
    <input type="hidden" name="editpencnum"   id="editpencnum"   value="">    
    <input type="hidden" name="editpentrynum" id="editpentrynum" value="">
    <input type="hidden" name="editpname" id="editpname" value="">
    <input type="hidden" name="editpqty"  id="editpqty"  value="">
    <input type="hidden" name="editppk"   id="editppk"   value="">
    <input type="hidden" name="editppack" id="editppack" value="">
    <input type="hidden" name="dateset" id="dateset" value="<?= $_REQUEST['dateset'] ?>">
    <input type="hidden" name="old_refno" id="old_refno" value="<?= $_GET['refno'] ?>">
<?php 

$sTemp = ob_get_contents();
ob_end_clean();

/*
global $GPC;
echo $GPC;
echo "<hr>sid:$sid;clear:$clear_ck_sid";
*/

$sBreakImg ='close2.gif';    
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
$smarty->assign('sContinueButton','<img src="'.$root_path.'images/btn_submitorder.gif" align="center" onclick="if (validate()) document.inputform.submit()"  style="cursor:pointer" />');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','supply_office/supply-issuance-form.tpl');
$smarty->display('common/mainframe.tpl');

?>

<script>
function checker() {
  var name = $('issuing_id_hidden').value;
  if (name != '') {
    self.clearInterval(callback);
    
    //jsAreaSRCOptionChngIss(name);
    
  }
}

var callback = self.setInterval("checker()", 100000);
</script> 

