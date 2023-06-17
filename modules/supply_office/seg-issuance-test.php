<?php
                                                                
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/supply_office/ajax/issue.common.php");

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org
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
if (!$_GET['from'])
    $breakfile=$root_path."modules/supply_office/seg-supply-functions.php".URL_APPEND."&userck=$userck";
else if($_GET['from']=='phs')
    $breakfile=$root_path."modules/phs/seg-phs-function.php".URL_APPEND."&userck=$userck"; 
else {
    if ($_GET['from']=='CLOSE_WINDOW')
        $breakfile = "javascript:if (window.parent.myClick) window.parent.myClick(); else window.parent.cClick();";
    else
				$breakfile = $root_path.'modules/supply_office/seg-supply-functions.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$imgpath=$root_path."pharma/img/";
$thisfile='seg-issuance-test.php';

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

include_once($root_path."include/care_api_classes/inventory/class_expiry.php");
$expiry_obj = new Expiry();

include_once($root_path."include/care_api_classes/inventory/class_eodinventory.php");
$eod_obj = new EODInventory();

include_once($root_path."include/care_api_classes/inventory/class_pharma_product.php");
$pharmaprod_obj = new SegPharmaProduct();

include_once($root_path."include/care_api_classes/inventory/class_item.php");
$itmobj = new Item();

include_once($root_path."include/care_api_classes/inventory/class_unit.php");
$unitobj = new Unit();

#added by bryan 12-01-09
require_once($root_path.'include/care_api_classes/alerts/class_alert.php');
$alert_obj = new SegAlert();

#added by bryan 12-01-09
require_once($root_path.'include/care_api_classes/class_area.php');
$area_obj = new SegArea();

global $db;

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');
    
if ($_GET["from"]=="CLOSE_WINDOW") {
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);
}
    
# Title in the title bar
$smarty->assign('sToolbarTitle',"Supplies::Issuance::New");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Supplies::Issuance::New");

$user_location = $_SESSION['sess_user_personell_nr'];



if($_SESSION['sess_user_personell_nr']) {
    $sqlLOC = "SELECT location_nr FROM care_personell_assignment WHERE personell_nr=".$_SESSION['sess_user_personell_nr'];  
    $resultLOC = $db->Execute($sqlLOC);                                                            
    $rowLOC = $resultLOC->FetchRow();
    $persnl = $persnl_obj->getPersonellInfo($_SESSION['sess_user_personell_nr']); 
}

if (isset($_POST["submitted"])) {
    //$db->debug = true;
    $bulk = array();
    $total = 0;    

    //added by bryan on 112609
    //latestrefno is obatained before saving
    if (($_POST['old_refno'])=="")
        $lastnrthis = $issue_obj->getLastNr(date("Y-m-d")); 
    else
        $lastnrthis = $_POST['old_refno'];  
    

    $authorizing_id  = (empty($_POST['authorizing_id_hidden'])) ? $_SESSION['sess_user_personell_nr'] : $_POST['authorizing_id_hidden']; //Added by EJ 11/03/2014

    $data = array(
        'refno'=>$lastnrthis,
        'issue_date'=>$_POST['issue_date'],
        'src_area_code'=>$_POST['area_issued'],
        'area_code'=>$_POST['area_dest'],
        'authorizing_id'=>$authorizing_id,
        'issuing_id'=>$_POST['issuing_id_hidden'],
        'issue_type'=>$_POST['iss_type']
        );
    
    $issue_obj->prepareIssuance(); 
    $issue_obj->setDataArray($data);
    $db->StartTrans();

    // Insert new issuance ....
    $saveok = $issue_obj->insertDataFromInternalArray();         

    include_once($root_path."include/care_api_classes/inventory/class_inventory.php");
    $inventory_obj = new Inventory();

    if ($saveok) {
        foreach($_POST["items"] as $i=>$v) {

            $inventory_obj->setInventoryParams($v, $_POST['area_issued'], $lastnrthis, ISSUANCE);
            
            $allqty = $_POST['request_pending'][$i];

            $qtyperpack = $itmobj->getQtyPerBigUnit($v);

            /*$strSQL = "SELECT
                      rd.refno,
                      rd.item_qty AS rqty,
                     rs.served_qty AS sqty FROM seg_internal_request_details AS rd LEFT JOIN
                            (    AS rs INNER JOIN seg_issuance_details AS id ON rs.item_code = id.item_code AND rs.issue_refno = id.refno)
                         ON rd.refno = rs.request_refno AND rd.item_code = rs.item_code 
                      WHERE rd.item_code = '$v'
                      GROUP BY rd.refno HAVING SUM(CASE WHEN rd.is_unitperpc THEN rd.item_qty ELSE ($qtyperpack * rd.item_qty) END) > 0
                         AND SUM(CASE WHEN rd.is_unitperpc THEN rd.item_qty ELSE ($qtyperpack * rd.item_qty) END) > IFNULL(SUM(served_qty),0)
                      FOR UPDATE";*/

        $strSQL = $db->Prepare("SELECT
                      rd.refno,
                      rd.item_qty AS rqty,
                     rs.served_qty AS sqty FROM seg_internal_request_details AS rd LEFT JOIN
                            (seg_requests_served AS rs INNER JOIN seg_issuance_details AS id ON rs.item_code = id.item_code AND rs.issue_refno = id.refno)
                         ON rd.refno = rs.request_refno AND rd.item_code = rs.item_code AND id.`unit_id` = rd.`unit_id`
                      WHERE rd.item_code = ? AND rd.`unit_id` = ?
                      GROUP BY rd.refno FOR UPDATE");
             
        if($result = $db->GetAll( $strSQL , array($v, $_POST['unitid'][$i]))){
            $servedqty = 0;
            if($allqty > 0){   
                foreach ($result as $row) {
                    $balqty = $row['rqty'] - $row['sqty'];
                    if ($allqty > $balqty) {
                        $allqty -= $balqty;
                        $balqty = $row['rqty'];  
                    }
                    else {
                        $balqty = $allqty; 
                        $allqty = 0;                             
                    }

                    //replaced by EJ 10/30/2014 
                    $row['refno'] = $_POST['reqrefno'][$i];
                    $balqty = $_POST['request_pending'][$i];

                    $servedqty = $balqty;
                    $fldArray = array('request_refno'=>"'{$row['refno']}'", 'issue_refno'=>"'{$lastnrthis}'", 'item_code'=>"'$v'", 'served_qty'=>"{$_POST['request_pending'][$i]}");
                    
                    $saveok = $db->Replace('seg_requests_served', $fldArray, array('request_refno', 'issue_refno', 'item_code'));

                    if (!$saveok) break; 
                }
            }else
                $saveok = false; 
        }else
            $saveok = false;          
            
            if ($saveok && ($servedqty > 0)) {
//                $runqty = $servedqty;
                $perpc = $_POST['perpc'][$i];
                $avgcost = $_POST['avg_cost'][$i];

                if ($unitobj->isUnitIDBigUnit($_POST['unitid'][$i])) {
                    $servedqty *= $qtyperpack;               
                }

                $saveok = $inventory_obj->remInventory($servedqty, $_POST['unitid'][$i], NULL, NULL, '', TRUE);
                
                   /* if ($unitobj->isUnitIDBigUnit($_POST['unitid'][$i])) {
                        //$servedqty /= $qtyperpack;
                        $servedqty /= $qtyperpack;
                        //$avgcost *= $qtyperpack;
                        $perpc = '0';
                    }*/

                if ($saveok) {
                    $skuobj = new SKUInventory();
                    $skuobj->clearTmpTable();
                }
                else {
                    $errorMsg = $inventory_obj->sql;
                }

                if ($saveok) {

                    $saved_items = $inventory_obj->getRemovedItemsFromInventory();                                                
                    $prodInformation = $pharmaprod_obj->getProductInfo($_POST['items'][$i]);
                    if (!empty($saved_items)) {                                                                        
                        foreach($saved_items as $k=>$row) {
                            $fldArray = array('refno'=>"'{$lastnrthis}'", 'item_code'=>"'$v'", 'item_qty'=>"{$_POST['request_pending'][$i]}", 'unit_id'=>"{$_POST['unitid'][$i]}",
                                              'is_unitperpc'=>"{$perpc}", 'expiry_date'=>"'{$row['expiry']}'", 'serial_no'=>"'{$row['serial']}'", 
                                              'avg_cost'=>"'{$avgcost}'");                                              
                            $exp_serial['expdate'][$i] = $row['expiry'];
                            $exp_serial['serial'][$i] = $row['serial'];
                            $saveok = $db->Replace('seg_issuance_details', $fldArray, array('refno', 'item_code'));

                            if ($saveok) {
                                if ($prodInformation['prod_class'] == 'E') {
                                    $saveok = $issue_obj->setCustodianDetails($lastnrthis, $v, $row['expiry'], $row['serial'], $_POST['epropno'][$i], $_POST['eestlife'][$i]);
                                    if (!$saveok) {
                                        $errorMsg = $issue_obj->sql;
                                        break;
                                    }                                       
                                }
                            }
                            else {
                                $errorMsg = $db->ErrorMsg();
                                break;
                            }                             
                        }                                
                    }
                }               
                                                            
                if (!$saveok){
                    $db->failTrans();
                    break;
                }
            }
        }
        $db->CompleteTrans();
//        $db->failTrans();


        if ($saveok) {
            $smarty->assign('sMsgTitle','Supply issuance successfully saved!');
            $smarty->assign('sMsgBody','The issue details have been saved into the database...');
            $sBreakImg ='close2.gif';
            $smarty->assign('sBreakButton','<img class="segSimulatedLink" '.createLDImgSrc($root_path,$sBreakImg,'0','absmiddle').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
            $smarty->assign('sPrintButton',"<img class=\"segSimulatedLink\" src=\"".$root_path."images/btn_printpdf.gif\" border=\"0\" align=\"absmiddle\" onclick=\"window.open('".$root_path."modules/supply_office/pdf_reqiss_slip.php?refno=".$lastnrthis."',null,'height=600,width=870,status=yes,toolbar=no,menubar=no,location=no,resizable=yes');\"/>");

            # Assign submitted form values
            $smarty->assign('sIssueDate', $_REQUEST['issue_date']);

            $smarty->assign('sRefNo', !empty($_GET['refno']) ? @$_GET['refno'] : $lastnrthis);

            $smarty->assign('sAuthBy', $_REQUEST['authorizing_id']);
            $smarty->assign('sIssBy', $_REQUEST['issuing_id']);

            # need to edit
            $fetchAreaFromDepartment = "SELECT area_name FROM seg_areas WHERE area_code='".$_POST['area_issued']."'";
            $areaResult = $db->Execute($fetchAreaFromDepartment);
            $areaRow = $areaResult->FetchRow();

            $smarty->assign('sSrcArea', $areaRow['area_name']);

            # need to edit
            $fetchAreaFromDepartment = "SELECT area_name FROM seg_areas WHERE area_code='".$_POST['area_dest']."'";
            $areaResult = $db->Execute($fetchAreaFromDepartment);
            $areaRow = $areaResult->FetchRow();

            $smarty->assign('sArea', $areaRow['area_name']);

            foreach ($_REQUEST['items'] as $i=>$v){    
          
                $items_table[] = "<tr><td>".$_REQUEST['items'][$i]."</td><td>".$_REQUEST['name'][$i]."</td><td>". $_REQUEST['request_pending'][$i] ."</td><td>". $_REQUEST['unitdesc'][$i] ."</td><td>". $exp_serial['serial'][$i] ."</td><td>". $exp_serial['expdate'][$i] ."</td></tr>";                    
            }

            $show_items = implode("",$items_table);
            $smarty->assign('sItems',$show_items);
            $smarty->assign('sMainBlockIncludeFile','supply_office/oksave.tpl');
            $smarty->display('common/mainframe.tpl');
            exit;
        }                                          
        else {
//            $errorMsg = $db->ErrorMsg();
            if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
                $smarty->assign('sysErrorMessage','<strong>Error:</strong> An Issuance with the same Ref number already exists in the database.');
            else
                $smarty->assign('sysErrorMessage',"<strong>Error:</strong> $errorMsg");
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
//        if (!$('refno').value) {
//            alert("Please enter the reference no.");
//            $('refno').focus();
//            return false;
//        }
        if (!$('authorizing_id').value) {
            alert("Please select a registered person for authorization using the person search function...");
            return false;
        }
        if (!$('area_issued').value) {
            alert("Please select issuing area...");
            return false;
        }
        
        if (!$('area_dest').value) {
            alert("Please select requesting area...");
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

        var all_req = document.getElementsByName("request_pending[]");
                var all_athandstock = document.getElementsByName("athand[]");   //added by julz
        for(var i = 0, max = all_req.length; i < max; i++) 
        {   
            if(all_req[i].value <= 0){
                alert("Cannot process less than or equal to 0 request");
                return;
            }
            // alert(all_req[i].innerHTML);
        }

        //  //added by julz
        // for(var i = 0;i < all_req.length; i++) 
        // {      
        //     if(parseFloatEx(all_req[i].value) > parseFloatEx(all_athandstock[i].value))
        //     {
        //         alert("Cannot process issuance. Some items is greater than the available quantity");
        //          return;
        //     }
        // }
        return confirm('Process this supply issuance?');
    }

 //added by julz
    function parseFloatEx(x) {
    var str = x.toString().replace(/\,|\s/,'')
    return parseFloat(str)    
}
-->
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Assign prompt messages

$lastnr = $order_obj->getLastNr(date("Y-m-d"));


if ($_REQUEST['encounterset']) {
    $person = $order_obj->getPersonInfoFromEncounter($_REQUEST['encounterset']);
}

# Render form values
if (isset($_POST["submitted"]) && !$okba) {
    $smarty->assign('sIssueItems',"
                <tr>
                    <td colspan=\"8\">Issue list is currently empty...</td>
                </tr>");
                
    if (is_array($_POST['items'])) {
        include_once($root_path."include/care_api_classes/class_product.php");
        $prod_obj = new Product();
        $items_name_array = $prod_obj->getProductName($_REQUEST['items']);
        
        $script = '<script type="text/javascript" language="javascript">';

        $items = $_POST['items'];
        $pendingAdd = array();                                           
        $descAdd = array();
        $unitidAdd = array();
        $unitdescAdd = array();
        $perpcAdd = array();
        $expdateAdd = array();
        $serialAdd = array();
        //
        $avgAdd = array();
        
        #$ihap = 0;
        foreach ($items as $i=>$item) {
            $pendingAdd[$i] = $_POST['pending'][$i];
            $descAdd[$i] = $_POST['desc'][$i];
            $unitidAdd[$i] = $_POST['unitid'][$i];
            $unitdescAdd[$i] = $_POST['unitdesc'][$i];
            $perpcAdd[$i] = $_POST['perpc'][$i];
            $expdateAdd[$i] = $_POST['expdate'][$i];
            $serialAdd[$i] = $_POST['serial'][$i];
            //
            $avgAdd[$i] = $_POST['avg'][$i];
            #$items_namesAdd[$i] = $items_name_array[$i];
            #echo $items[$ihap].",".$items_name_array[$ihap].",";
            #$ihap++;
        }
        #/*
        $script .= "var item0 = ['" .implode("','",$items)."'];";
        $script .= "var item_name0= ['" .implode("','",$items_name_array)."'];";
        $script .= "var desc0 = ['" .implode("','",$descAdd). "'];";
        $script .= "var pending0 = [" .implode(",",$pendingAdd). "];";
        $script .= "var unitid0= [" .implode(",",$unitidAdd). "];";
        $script .= "var unitdesc0= ['" .implode("','",$unitdescAdd). "'];";
        $script .= "var perpc0 = [" .implode(",",$perpcAdd). "];";
        $script .= "var expdate0= ['" .implode("','",$expdateAdd). "'];";
        $script .= "var serial0 = ['" .implode("','",$serialAdd). "'];";
        $script .= "var avg0 = [" .implode(",",$avgAdd). "];";
        #*/
        /*
        $script .= "var item0= ".$_POST['items'].";";
        $script .= "var item_name0= ".$items_name_array.";";
        $script .= "var desc0 = ".$_POST['desc'].";";
        $script .= "var pending0 = ".$_POST['pending'].";";
        $script .= "var unitid0= ".$_POST['unitid'].";";
        $script .= "var perpc0 = ".$_POST['perpc'].";";
        */
        $script .= "xajax_add_item(item0, item_name0, desc0, pending0, unitid0, perpc0, unitdesc0, expdate0, serial0, avg0);";
        $script .= "</script>";
        $src = $script;
    }
    if ($src) $smarty->assign('sIssueItems',$src);
}
else {
    $smarty->assign('sIssueItems',"
                <tr>
                    <td colspan=\"8\">Issue list is currently empty...</td>
                </tr>");
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
    
    require_once($root_path.'include/care_api_classes/class_product.php');
    $prod_obj=new Product;
    $prod=$prod_obj->getAllPharmaAreas();
    $disabled = (strtolower($_GET['area']) != 'all') ? ' disabled="disabled"' : '';
    $index = 0;
    $count = 0;
    $select_area = '';
    while($row=$prod->FetchRow()){
        $checked=strtolower($row['area_code'])==strtolower($_GET['area']) ? 'selected="selected"' : "";
        $select_area .= "    <option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
        if ($checked) $index = $count;
        $count++;
    }
      
    //blank since there is no edit issuance
    $smarty->assign('sRefno', '');
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
    
    $smarty->assign('sIssueDate','<span id="show_issuedate" class="jedInput" style="margin-left:0px; margin-top:3px; font-weight:bold; color:#0000c0; padding:0px 2px;width:80px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span>
        <input class="jedInput" name="issue_date" id="issue_date" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="font:bold 12px Arial">');
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
        else{
            //modified by bryan 112609
            $depscomma = $depscomma.",".$dept_nr;
            $result = $area_obj->getInventoryAreas();
//            $result = $objdept->getAreasInADept($depscomma);
        }
    }
    
    $count = 0;    
    $s_areacode = '';
    $ori_area = "<option value=\"\" $checked>- Select Area -</option>\n";
    if ($result) {
        foreach($result as $row){
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
    $dest_area = "<option value=\"\" $checked>- Select Area -</option>\n";
    $result = $area_obj->getInventoryAreas();
    if ($result) {
        foreach($result as $row){
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
    
    $authorized  = (empty($_POST['authorizing_id'])) ? $_SESSION['sess_login_username'] : $_POST['authorizing_id']; //Added by EJ 11/03/2014

    $smarty->assign('sAuthorizedId','<input id="authorizing_id" name="authorizing_id" size="35" readonly="readonly" type="text" value="'.$authorized.'"/>');
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
    
if($error=="refno_exists"){
    $smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
    $smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}

$qs = "";
//if ( $_GET['billing'] ) $qs .= "&billing=".$_GET['billing'];
//if ( $_GET['pid'] ) $qs .= "&pid=".$_GET['pid'];
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
  <input id="ref_no" name="ref_no" type="hidden" value="<?php echo $_GET['refno']; ?>"/>
    
    <input id="authorizing_id_hidden" name="authorizing_id_hidden" type="hidden" value="<?= $_REQUEST['authorizing_id_hidden'] ?>"/>
    <input id="issuing_id_hidden" name="issuing_id_hidden" type="hidden" value="<?= $_SESSION['sess_user_personell_nr'] ?>"/>
    
    <input type="hidden" name="editpencnum"   id="editpencnum"   value="">    
    <input type="hidden" name="editpentrynum" id="editpentrynum" value="">
    <input type="hidden" name="editpname" id="editpname" value="">
    <input type="hidden" name="editpqty"  id="editpqty"  value="">
    <input type="hidden" name="editppk"   id="editppk"   value="">
    <input type="hidden" name="editppack" id="editppack" value="">
    <input type="hidden" name="billing" id="billing" value="<?= $_REQUEST['billing'] ?>">
    <input type="hidden" name="dateset" id="dateset" value="<?= $_REQUEST['dateset'] ?>">
    <input type="hidden" name="encounterset" id="encounterset" value="<?= $_REQUEST['encounterset'] ?>">
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