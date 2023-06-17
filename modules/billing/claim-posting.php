<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/billing/ajax/claim-posting.common.php');
require_once($root_path.'include/care_api_classes/billing/class_claim.php');
require_once($root_path.'include/care_api_classes/curl/class_curl.php');

$lang_tables[]='search.php';
define('LANG_FILE','finance.php');
$local_user='aufnahme_user';

require_once($root_path.'include/inc_front_chain_lang.php');
$thisfile=basename(__FILE__);
global $db;
$from = $_GET["from"];
if (($from == "") || (!isset($from))) 
    $breakfile=$root_path.'modules/billing/bill-main-menu.php'.URL_APPEND."&userck=$userck";
else
    $breakfile=$_GET["from"].".php".URL_APPEND;

# Start Smarty templating here
/**
* LOAD Smarty
*/
# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',"$LDBillingMain :: $LDBillingClaim");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('claim-posting.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);
 
if (($from != "") && (isset($from))) 
    $_SESSION["breakfile"] = $breakfile;
else
    unset($_SESSION["breakfile"]);
 
 # Window bar title
 $smarty->assign('sWindowTitle',"$LDBillingMain :: $LDListAll");
 
 define("NUM_COLS", 13);
 $transmittal_no = 0;

 if (isset($_POST["submitted"])) { 
    $objclaim = new Claim();
    $curl_obj = new Rest_Curl();

    if ($_POST['old_ref_no'] == '')
        $shist = "Create: ".date('Y-m-d H:i:s')." = ".$_SESSION['sess_user_name'];
    else
        $shist = "\nUpdate: ".date('Y-m-d H:i:s')." = ".$_SESSION['sess_user_name'];
    
    $data = array(
        'ref_no'=>str_replace(" ", "", $_POST["ref_no"]),
        'post_dte'=>$_POST["post_dte"],
        'category_id'=>$_POST["category_list"],
        'apv_no'=>$_POST["apv_no"],
        'check_no'=>$_POST["chk_no"],
        'apv_date'=>$_POST["apv_dte"],
        'hcare_id'=>$_POST["hcare_id"],
        'history'=>$shist,  
        'modify_id'=>$_SESSION['sess_user_name'],
        'create_id'=>$_SESSION['sess_user_name']        
        );   
    $objclaim->setDataArray($data);
    $objclaim->startTrans();
    
    if ($_POST['old_ref_no'] == '') {        
        // Insert new claim ...        
        $saveok = $objclaim->insertDataFromInternalArray();
    }
    else { 
        // Update old ref no.        
        $saveok = $objclaim->delClaimDetails($_POST['old_ref_no']);            
        if ($saveok) {            
            $objclaim->setWhereCondition("ref_no = '".$_POST['old_ref_no']."'");
            $saveok = $objclaim->updateDataFromInternalArray($_POST['old_ref_no'], FALSE);
        }
    }
    
    if ($saveok) {
        $curl_obj->unpostEntries(array('refno'=>$_POST['ref_no'], 'src'=>'PRF'));
        $curl_obj->unpostEntries(array('refno'=>$_POST['ref_no'], 'src'=>'ARM'));

        foreach ($_POST["cases"] as $i=>$v) {
            $is_denied   = ($_POST["is_denied"][$i] == '0') ? false : true;
            $is_returned = ($_POST["is_ret"][$i] == '0') ? false : true;
            
            if ($is_denied) {               // denied ...
                $data = array(
                    'ref_no'=>$_POST["ref_no"],
                    'encounter_nr'=>$v,
                    'remarks'=>'');
                
                $objclaim->useClaimDenied();
                $objclaim->setDataArray($data);                
                $saveok = $objclaim->insertDataFromInternalArray();                
            }
            else if ($is_returned) {        // returned ...
                $data = array(
                    'ref_no'=>$_POST["ref_no"],
                    'encounter_nr'=>$v,
                    'remarks'=>'');
                
                $objclaim->useClaimReturned();
                $objclaim->setDataArray($data); 
                $strSQL = "UPDATE seg_transmittal_details SET is_returned=1 WHERE encounter_nr='".$data['encounter_nr'] ."'";
                if($db->Execute($strSQL)) {            
                $saveok = $objclaim->insertDataFromInternalArray();   }                             
            }
            else {                          // paid ...
                foreach ($_POST["cases2_".$v] as $j=>$dv) {
                    $priority = $_POST["priority_".$v][$j];

                    switch ($priority) {
                        case 1: // Payment for professional fees ...
                            $pid = $_POST["pid_".$v][$j];

                            if ($_POST["d1_".$v][$j] != 0) { 
                                $role_area = 'D1';
                              
                                $dr_pay = str_replace(",","",$_POST["d1_pay_".$v][$j]);
                                
                            }
                            elseif ($_POST["d2_".$v][$j] != 0) {
                                $role_area = 'D2';
                                
                                $dr_pay = str_replace(",","",$_POST["d2_pay_".$v][$j]);
                            }
                            elseif ($_POST["d3_".$v][$j] != 0) {
                                $role_area = 'D3';
                                
                                $dr_pay = str_replace(",","",$_POST["d3_pay_".$v][$j]);
                            }
                            elseif ($_POST["d4_".$v][$j] != 0) {                                                    
                                $role_area = 'D4';
                               
                                $dr_pay = str_replace(",","",$_POST["d4_pay_".$v][$j]);
                            }
                            
                            //if doctor has excess
                           $doc_ExcessPfCharge = $curl_obj->GetExcessPFcharge($v,$_POST["pid_".$v][$j]); 
                          if(empty($doc_ExcessPfCharge)){

                           $doc_ExcessPfCharge = "0";
                          }

                            $data = array(
                                'ref_no'=>$_POST["ref_no"],
                                'encounter_nr'=>$v,
                                'dr_nr'=>$_POST["pid_".$v][$j],
                                'role_area'=>$role_area,
                                'dr_pay'=>(floatval($dr_pay)+floatval($doc_ExcessPfCharge)),
                                'tax_wheld'=>str_replace(",","",$_POST["tax_wheld_".$v][$j])
                                );                        
                            
                          
                            $objclaim->useClaimPayPF();
                            $objclaim->setDataArray($data);
                            $saveok = $objclaim->insertDataFromInternalArray(); 
                            
                            //post in fis
                            $curl_obj->postHmoPf($v, $_POST['ref_no'], $_POST['hcare_id'], $_POST["pid_".$v][$j]);       
                            break;
                        
                        case 2: // Payment for hospital claims ...
                            $data = array(
                                'ref_no'=>$_POST["ref_no"],
                                'encounter_nr'=>$v,
                                'acc_pay'=>str_replace(",","",$_POST["acc_pay_".$v][$j]),
                                'med_pay'=>str_replace(",","",$_POST["med_pay_".$v][$j]),
                                'srv_pay'=>str_replace(",","",$_POST["srv_pay_".$v][$j]),
                                'ops_pay'=>str_replace(",","",$_POST["ops_pay_".$v][$j]),
                                'msc_pay'=>str_replace(",","",$_POST["msc_pay_".$v][$j]),
                                'd1_pay'=>str_replace(",","",$_POST["d1_pay_".$v][$j]),
                                'd2_pay'=>str_replace(",","",$_POST["d2_pay_".$v][$j]),  
                                'd3_pay'=>str_replace(",","",$_POST["d3_pay_".$v][$j]),
                                'd4_pay'=>str_replace(",","",$_POST["d4_pay_".$v][$j]),                                                                
                                'tax_wheld'=>str_replace(",","",$_POST["tax_wheld_".$v][$j])
                                ); 
                                
                            $objclaim->useClaimPayHosp();
                            $objclaim->setDataArray($data);
                            $saveok = $objclaim->insertDataFromInternalArray();
                            $curl_obj->postHmoHCI($v, $_POST['ref_no']);
                            break;
                            
                        case 3: // Payment for patient claim ...                              
                            $data = array(
                                'ref_no'=>$_POST["ref_no"],
                                'encounter_nr'=>$v,
                                'patient_pay'=>str_replace(",","",($_POST["med_pay_".$v][$j] + $_POST["srv_pay_".$v][$j])),                                                               
                                'tax_wheld'=>str_replace(",","",$_POST["tax_wheld_".$v][$j])
                                );                         
                        
                            $objclaim->useClaimPayPatient();
                            $objclaim->setDataArray($data);
                            $saveok = $objclaim->insertDataFromInternalArray();                        
                            break;
                                                  
                    }                                                                                      
                }
            }
            
        }                    
    }
    
    if (!$saveok) $objclaim->failTrans();
    $objclaim->completeTrans();    
                                             
    if ($saveok) {
        $smarty->assign('sysInfoMessage','<strong>Successfully saved claim '.$_POST["ref_no"].'!</strong>');
        echo "<script>location.href='claim-posting.php".URL_REDIRECT_APPEND."'</script>";
    }else{
//        $smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$objclaim->getErrorMsg());    
        $smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$objclaim->sql);  
    }
}
 
 # Buffer page output
 ob_start();
?>
<!-- prototype -->
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>/js/shortcut.js"></script>

<!-- Calendar -->
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins: -->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript">
<!--
 OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">
<!--
.olbg {
    background-image:url("<?= $root_path ?>images/bar_05.gif");
    background-color:#ffffff;
    border:1px outset #3d3d3d;
}
.olcg {
    background-color:#ffffff; 
    background-image:url("<?= $root_path ?>images/bar_05.gif");
    text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
    background-color:#ffffff; 
    text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
    font-family:Arial; font-size:13px; 
    font-weight:bold; 
    color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}

a {color:#338855;font-weight:bold;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style>

<!-- YUI Library -->
<script type="text/javascript" src="<?=$root_path?>js/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/event/event.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dom/dom.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/connection/connection.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dragdrop/dragdrop.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/container/container_core.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/yui/container/container.js"></script>
<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/container/assets/container.css">

<!-- include billing.css -->
<link rel="stylesheet" type="text/css" href="css/billing.css" />
<script type="text/javascript" src="<?=$root_path?>js/gen_routines.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" /> 
<script type='text/javascript' src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.datetimepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.datetimepicker/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript">var $j = jQuery.noConflict();</script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.numeric.js?t=<?= time() ?>"></script>
<script type="text/javascript" src="js/claim-posting.js"></script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
?>
<script>    
    YAHOO.util.Event.addListener(window, "load", init);  
</script>

<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

//$smarty->assign('sOnUnloadJs',"onUnload=\"remLink()\"");

$sref_no = '';
$dpost_dte = strftime("%Y-%m-%d %H:%M:%S");
$dapv_dte = strftime("%Y-%m-%d %H:%M:%S"); 
$nhcare_id    = 0;
$shcare_nm    = '';
$shcare_addr  = '';

if (isset($_GET['nr'])) {    
    $smarty->assign('sClaims',"
                <tr>
                    <td colspan=\"".NUM_COLS."\">Claims list is currently empty ...</td>
                </tr>");

    // Get the claim header info and cases associated with it ...
    $objclaim = new Claim();    
    if ($result = $objclaim->getPostedClaim($_GET['nr'])) {          
        $sref_no     = $result["ref_no"];
        $dpost_dte   = strftime("%Y-%m-%d", strtotime($result["post_dte"])); 
        $ncateg_id   = $result["category_id"];
        $ncateg_desc = $result["memcategory_desc"];
        $sapv_no     = $result["apv_no"]; 
        $chkno     = $result["check_no"]; 
        $dapv_dte    = strftime("%Y-%m-%d", strtotime($result["apv_date"]));
        $nhcare_id   = $result["hcare_id"]; 
        $shcare_nm   = $result["name"];    
        
        if ($result = $objclaim->getEncountersInClaim($_GET['nr'])) {        
            $cases = array();
            while ($row = $result->FetchRow()) {
                $cases[] = $row["encounter_nr"];
            }                        
                        
            if (!empty($cases)) {
                $script = '<script type="text/javascript" language="javascript">'; 
                $script .= "var encrs =['".implode("','",$cases)."'];";                
                $script .= "var hcare_id =".$nhcare_id.";";                
                $script .= "xajax_addClaimDet(encrs, hcare_id);";
                $script .= "$('tbl_claim_details_hdr_row1').style.display = 'none';"; 
                $script .= "$('tbl_claim_details_hdr_row2').style.display = '';";
                $script .= "</script>";       
                
                $s_encounters = implode("','",$cases);                       
            }
            else
                $script = '';
                
            $src = $script;        
        }
        if ($src) $smarty->assign('sClaims', $src);         

          //added by maimai 01-14-2015
        $transmittal = $objclaim->getTransmittalNoEnc($cases[0]);
        $transmittal_no = $transmittal['transmit_no'];        
    }                             
}
else {
    $smarty->assign('sClaims',"
                <tr>
                    <td colspan=\"".NUM_COLS."\">Claims list is currently empty ...</td>
                </tr>");
}

# Render form values
if (isset($_POST["submitted"]) && !$saveok) {
    $smarty->assign('sClaims',"
                <tr>
                    <td colspan=\"".NUM_COLS."\">Claims list is currently empty ...</td>
                </tr>");    
                
    if (is_array($_POST['cases'])) {
        $encrs = $_POST['cases'];             
                
        if (count($encrs) > 0) {
            $s_encrs = "'".implode("','",$encrs)."'";
            
            $script = '<script type="text/javascript" language="javascript">';                        
            $script .= "var encrs  = [".$s_encrs."];";
            $script .= "var hcare_id =".$_POST["hcare_id"].";";  
            $script .= "xajax_addClaimDet(encrs, hcare_id);";
            $script .= "</script>";            
        }  
        else
            $script = '';             
            
        $src = $script;    
    }
    if ($src) $smarty->assign('sClaims',$src);
}
elseif (!isset($_GET['nr'])) {
    $smarty->assign('sClaims',"
                <tr>
                    <td colspan=\"".NUM_COLS."\">Claims list is currently empty ...</td>
                </tr>");
}

$submitted = isset($_POST["submitted"]);

$smarty->assign('sRefNo', '<input class="segInput" id="ref_no" name="ref_no" type="text" size="20" value="'.(($submitted && !$saveok) ? $_POST['ref_no'] : $sref_no).'" style="font:bold 12px Arial; float;left; text-align:left">'); 
$smarty->assign('sResetRefNo','<input class="segButton" type="button" value="Reset" onclick="xajax_resetRefNo()"/>');
$smarty->assign('sHCareDesc', '<input class="segInput" id="hcname" name="hcname" type="text" size="60" value="'.(($submitted && !$saveok) ? $_POST['hcname'] : $shcare_nm).'" style="font:bold 12px Arial; float;left;" readOnly >'); 

//Select Health Insurance
$smarty->assign('sSelectHCare','<input class="segInput" id="select-hcare" type="image" src="../../images/FIND.gif" border="0" style=""
       onclick="if (bClickedHCare) overlib(
        OLiframeContent(\'billing-select-hcare.php'.URL_APPEND.'&from=1\', 700, 400, \'fSelHCare\', 0, \'auto\'),
        WIDTH,700, TEXTPADDING,0, BORDER,0, 
                STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
                CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
                CAPTION,\'Select Health Insurance\',
        MIDX,0, MIDY,0, 
        STATUS,\'Select health insurance\'); return false;" 
       onmouseout="nd();" />');
                                              
$curTme  = strftime("%Y-%m-%d", strtotime($dapv_dte));         
$curDate = strftime("%b %d, %Y", strtotime($curTme));       


$smarty->assign('sCheckNo','<input class="segInput" id="chk_no" name="chk_no" type="text" size="20" value="'.(($submitted && !$saveok) ? $_POST['chk_no'] : $chkno).'" style="font:bold 12px Arial; float;left; text-align:left">'); 
$smarty->assign('sHCareAddress','<textarea class="segInput" id="hcaddress" name="hcaddress" cols="57" rows="2" style="font:bold 12px Arial" readOnly>'.(($submitted && !$saveok) ? $_POST['hcaddress'] : $shcare_addr).'</textarea>');
$smarty->assign('sAPVNo','<input class="segInput" id="apv_no" name="apv_no" type="text" size="20" value="'.(($submitted && !$saveok) ? $_POST['apv_no'] : $sapv_no).'" style="font:bold 12px Arial; float;left; text-align:left">'); 
$smarty->assign('sWitholdingTax','<input class="segInput" disabled id="w_tax" name="w_tax" type="text" size="2" value="'.(($submitted && !$saveok) ? $_POST['w_tax'] : $w_tax).'" style="font:bold 12px Arial; float;left; text-align:left">'); 
$smarty->assign('sAPVDate', '<span id="show_apvdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.$curDate.'</span><input class="jedInput" name="apv_dte" id="apv_dte" type="hidden" value="'.($submitted ? strftime("%Y-%m-%d", strtotime($_POST['apv_dte'])) : $curTme).'" style="font:bold 12px Arial">');
$smarty->assign('sAPVCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="apvdte_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
$jsCalScript = "<script type=\"text/javascript\">
    Calendar.setup ({
        displayArea : \"show_apvdate\",
        inputField : \"apv_dte\",
        ifFormat : \"%Y-%m-%d\", 
        daFormat : \"%b %d, %Y\", 
        showsTime : false, 
        button : \"apvdte_trigger\", 
        singleClick : true,
        step : 1
    });
</script>";
$smarty->assign('jsAPVCalendarSetup', $jsCalScript);

//$smarty->assign('sCategory','<select id="category_list" name="category_list" onchange="jsCategoryOptionChange(this, this.options[this.selectedIndex].value, this.options[this.selectedIndex].text)">
//                                <option value="">-Select Category-</option>
//                             </select>');

$smarty->assign('sCategory','<select id="category_list" name="category_list">
                                <option value="">-Select Category-</option>
                             </select>');
                             
if($_GET['nr']){
    //added by maimai 01-14-2014
    $smarty->assign('sBtnStatusOpt', '<select id="status_list" class="segInput" name="status_list">
                                        <option value="all ">All</option>
                                        <option value="paid">Paid</option>
                                        <option value="returned">Returned</option>
                                        <option value="denied">Denied</option>
                                        </select>'); 
    $smarty->assign('sBtnPrintStatus', '&nbsp;<img id="btnPrintStatus" style="cursor:pointer" src="'.$root_path.'/images/btn_printpdf.gif" border=0 onclick="printStatus();"');
    //end added by maimai
}

$curTme  = strftime("%Y-%m-%d", strtotime($dpost_dte));         
$curDate = strftime("%b %d, %Y", strtotime($curTme));                              

$smarty->assign('sDate', '<span id="show_postdte" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.$curDate.'</span><input class="jedInput" name="post_dte" id="post_dte" type="hidden" value="'.($submitted ? strftime("%Y-%m-%d %H:%M:%S", strtotime($_POST['post_dte'])) : $curTme).'" style="font:bold 12px Arial">');
$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="postdte_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
$jsCalScript = "<script type=\"text/javascript\">
    Calendar.setup ({
        displayArea : \"show_postdte\",
        inputField : \"post_dte\",
        ifFormat : \"%Y-%m-%d\", 
        daFormat : \"%b %d, %Y\", 
        showsTime : false, 
        button : \"postdte_trigger\", 
        singleClick : true,
        step : 1
    });
</script>";
$smarty->assign('jsCalendarSetup', $jsCalScript);

$smarty->assign('sBtnAddItem','<a href="javascript:void(0);"
       onclick="getEncounterNosWithClaims(); return overlib(
        OLiframeContent(\'billing-transmittal-hist.php'.URL_APPEND.'&src=claim&hid=\'+$(\'hcare_id\').value+\'\', 800, 400, \'fBillingTray\', 1, \'auto\'),
        WIDTH,800, TEXTPADDING,0, BORDER,0, 
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
                CAPTION,\'Add Transmitted Claim\',
        MIDX,0, MIDY,0, 
        STATUS,\'Add transmitted claim.\');"
       onmouseout="nd();">
             <img name="btnadd" id="btnadd" src="'.$root_path.'images/btn_add.gif" border="0"></a>');
             
$curref_no = ($submitted) ? $_POST['ref_no'] : $_GET['nr'];            
if ($curref_no != '') {
//    $smarty->assign('sBtnPrint', '<img id="btnPrint" style="cursor:pointer" src="'.$root_path.'/images/btn_printpdf.gif" border=0 >&nbsp;');
   /* if (isset($_GET['nr']) && ($_GET['nr'] != ''))
        $smarty->assign('sBtnDelete', '&nbsp;<img id="btnDelete" style="cursor:pointer" src="'.$root_path.'/images/btn_delete.gif" border=0 onclick="if (confirm(\'Delete this posted claim?\')) xajax_delPostedClaim(\''.$curref_no.'\')">');
    else
        $smarty->assign('sBtnDelete', '');*/
    $smarty->assign('sShowButtons', '');
    $smarty->assign('sNoShowButtons', 'style="display:none"');
}
else {
//    $smarty->assign('sBtnPrint', '');
    $smarty->assign('sBtnDelete', '');
    $smarty->assign('sShowButtons', 'style="display:none"');
    $smarty->assign('sNoShowButtons', '');
}
 
if($_GET['nr']){            
    $smarty->assign('sBtnSave', '<img id="btnSave" style="cursor:pointer" src="'.$root_path.'/images/btn_save.gif" border=0 onclick="if (validate()) document.claim_form.submit()" >');             
}else{
     $smarty->assign('sBtnSave', '<img id="btnSave" style="cursor:pointer; display:none;" src="'.$root_path.'/images/btn_save.gif" border=0 onclick="if (validate()) document.claim_form.submit()" >'); 
}

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'" method="POST" id="claim_form" name="claim_form" onSubmit="return validate();">');
$smarty->assign('sFormEnd','</form>');             

ob_start();
?>
<input type="hidden" name="submitted" value="1" /> 
<input type="hidden" id="transmittal_no" value="<?=$transmittal_no?>"> 
<input type="hidden" id="hcare_id" name="hcare_id" value="<?=(($submitted && !$saveok) ? $_POST['hcare_id'] : $nhcare_id)?>" >
<input type="hidden" id="root_path" name="root_path" value="<?php echo $root_path ?>" />
<input type="hidden" id="old_ref_no" name="old_ref_no" value="<?=(($submitted && $saveok) ? $_POST['ref_no'] : $_GET['nr'])?>">
<input type="hidden" id="seg_URL_APPEND" name="seg_URL_APPEND" value="<?=URL_APPEND?>"  />         
<input type="hidden" id="categ_id" name="categ_id" value="<?=(($submitted && !$saveok) ? $_POST['category_list'] : $ncateg_id)?>" > 
<div style="display:none" id="cases"><?=$s_encounters?></div>
<?php
$stemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sHiddenItems', $stemp);     
# Assign page output to the mainframe template

//$smarty->assign('sMainFrameBlockData',$sTemp);
$smarty->assign('sMainBlockIncludeFile','billing/claim-posting.tpl');
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>