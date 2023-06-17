<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/supply_office/ajax/adjustment.common.php");

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
//$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
//$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
//$phpfd=$date_format;
//$phpfd=str_replace("dd", "%d", strtolower($phpfd));
//$phpfd=str_replace("mm", "%m", strtolower($phpfd));
//$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
//$phpfd=str_replace("yy","%y", strtolower($phpfd));

//$php_date_format = strtolower($date_format);
//$php_date_format = str_replace("dd","d",$php_date_format);
//$php_date_format = str_replace("mm","m",$php_date_format);
//$php_date_format = str_replace("yyyy","Y",$php_date_format);
//$php_date_format = str_replace("yy","y",$php_date_format);

$title=$LDPharmacy;
if (!$_GET['from'])
    $breakfile=$root_path."modules/supply_office/seg-supply-functions.php".URL_APPEND."&userck=$userck";
else if($_GET['from']=='phs')
    $breakfile=$root_path."modules/phs/seg-phs-function.php".URL_APPEND."&userck=$userck";
else {
    if ($_GET['from']=='CLOSE_WINDOW')
        $breakfile = "javascript:if (window.parent.myClick) window.parent.myClick(); else window.parent.cClick();";
    else
        $breakfile = $root_path.'modules/supply_office/seg-issuance-test.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$imgpath=$root_path."pharma/img/";
$thisfile='seg-inventory-adjustment.php';

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 
# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
        
include_once($root_path."include/care_api_classes/class_order.php");
$order_obj = new SegOrder("pharma");

include_once($root_path."include/care_api_classes/inventory/class_item.php");
$item_obj = new Item();

include_once($root_path."include/care_api_classes/inventory/class_inventory.php");
$inv_obj = new Inventory();

include_once($root_path."include/care_api_classes/inventory/class_sku_inventory.php");
$skuInventory = new SkuInventory();

include_once($root_path."include/care_api_classes/inventory/class_adjustment.php");
$adj_obj = new SegAdjustment();

include_once($root_path."include/care_api_classes/class_personell.php");
$persnl_obj = new Personell();

include_once($root_path."include/care_api_classes/class_pharma_product.php");
$pharma_obj = new SegPharmaProduct();

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
$smarty->assign('sToolbarTitle',"Supplies::Adjustment");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Supplies::Adjustment");

$user_location = $_SESSION['sess_user_personell_nr'];

if($_SESSION['sess_user_personell_nr']) {
    $sqlLOC = "SELECT location_nr FROM care_personell_assignment WHERE personell_nr=".$_SESSION['sess_user_personell_nr'];  
    $resultLOC = $db->Execute($sqlLOC);                                                            
    $rowLOC = $resultLOC->FetchRow();
    
    $persnl = $persnl_obj->getPersonellInfo($_SESSION['sess_user_personell_nr']); 
}

if (isset($_POST["submitted"])) {

    $adj_obj = new SegAdjustment();   
    
    //latestrefno is obatained before saving     
    if (($_GET['adjrefno'])=="")
        $lastnrthis = $adj_obj->getLastNr(date("Y-m-d")); 
    else
        $lastnrthis = $_GET['adjrefno']; 
    
    
    $dataAdj = array(
        'refno'=>$lastnrthis,
        'adjust_date'=>$_POST["adj_date"],
        'adjusting_id'=>$_SESSION['sess_user_personell_nr'],
        'area_code'=>$_POST['area_adj'],
        'remarks' => $_POST['remarks'],
        'history' => "Create: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n", 
        'modify_id' => $_SESSION['sess_temp_userid'],
        'modify_dt' => $_POST['vitdate']
    );
    $adj_obj->prepareAdjustment();
    $db->startTrans();
//    $db->debug = true;
    if ($_POST['adjrefno']) {
        $PNo = $_GET['adjrefno'];

        $dataAdj['create_id']=$_SESSION['sess_temp_userid'];
        $dataAdj['create_dt']= $_POST['adj_date'];
        $adj_obj->setDataArray($dataAdj);
        $saveok=$adj_obj->insertDataFromInternalArray();
    }
    else {  
        $PNo = $_GET['adjrefno'];
        $dataAdj["history"]=$adj_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." [".$_SESSION['sess_temp_userid']."]\n");
        $adj_obj->setDataArray($dataAdj);
        $adj_obj->where = "refno=".$db->qstr($PNo);
        $saveok=$adj_obj->updateDataFromInternalArray($PNo,FALSE);
    }
                
    if ($saveok) {
        $skuobj = new SKUInventory();
        // var_dump($_POST);die;
        foreach ($_POST["items"] as $i=>$v) {
            $sitem_code = $v;                                   // item code
            //$nqty = str_replace(',', '', $_POST['qtys'][$i]);   // qty
            $unit_id = $_POST['unit_ids'][$i];                  // unit ids
            $is_perpc = $_POST['is_unitpcs'][$i];               // is unit per pc?
            $expiry = $_POST['expiry'][$i];               // expiry
            $serial = $_POST['serial'][$i] == '-' ? '' : $_POST['serial'][$i];               // serial
            $lot = $_POST['lot'][$i];             // current qty in inventory
            $uCost = floatval($_POST['unitcost'][$i]);             // current qty in inventory
            $orig_qty = $_POST['athand'][$i];               // current qty in inventory
            $adj_qty = (floatval($_POST['adjquan'][$i])) ?: $_POST['athand'][$i];  
            $pm_qty = ($_POST['pmquan'][$i]) ?: 0;               // plus/minus qty
            $reasons = $_POST['reasons'][$i];             // reason for adjustment 
            if ($reasons == '') {
                $reasons = 'PC';
            }
            // die($adj_qty . '   ' .$pm_qty);
            if($serial == '-') $serial = '';
            
            $data = array(
                'refno'=>$lastnrthis,
                'item_code'=>$sitem_code,
                'unit_id'=>$unit_id,
                'is_unitperpc'=>$is_perpc,
                'expiry_date'=>$expiry,
                'serial_no'=>$serial,
                'lot_no'=>$lot,
                'orig_qty'=>$orig_qty,
                'adj_qty'=>$adj_qty,
                'reason'=>$reasons
            );

           // ignore if adj quantity is same with orig qty
            if($pm_qty == 0 || $orig_qty == $adj_qty) {
                $emptyAdj = true;
                $saveok = false;
                break;
                //continue;
            }

            $adj_obj->prepareAdjustmentDetails();
            
            if ($_GET['adjrefno']) {
                $saveok = $adj_obj->clearAdjustmentDetails($_GET['adjrefno']);
            }
                        
            if ($_POST['adjrefno']) {
//                $PNo = $_GET['adjrefno'];
                if ($saveok) {
                    $adj_obj->setDataArray($data);
                    $saveok=$adj_obj->insertDataFromInternalArray();

                }
            }
//            else {  
//                $PNo = $_GET['adjrefno'];
//                $adj_obj->setDataArray($data);
//                $adj_obj->where = "refno=".$db->qstr($PNo);
//                $saveok=$adj_obj->updateDataFromInternalArray($PNo,FALSE);
                //$PNo = $dataVital['or_no'];                
//            }
          
            if ($saveok) {
//                $extendedinfo = $pharma_obj->getExtendedProductInfo($sitem_code);            
//                $smallunitid = ($extendedinfo) ? $extendedinfo['pc_unit_id'] : DEFAULT_UNIT;

                $inv_obj->setInventoryParams($sitem_code,$_POST['area_adj'], $_POST['adjrefno'], ADJUST);
                $sku = $skuInventory->getSKUIdswInventory($sitem_code, '', $_POST['area_adj'], $expiry, $serial, $unit_id, $lot, $uCost);
                $unitCost = -1;
//                $receivedCost = 0;
//                if($sku) {
//                    $skuId = $sku->FetchRow();
//                    // get receive cost from purchase or issuance
//                    $receivedCost = $skuInventory->getReceivedCostOfSku($skuId['sku_id']);
//                } else {
//                    $saveok = false;
//                    break;
//                }

                if($pm_qty >= 0) {
                   
                   $unit_obj = (object) 'details';
                   $unit_obj->unit_id = $unit_id;
                   $unit_obj->is_unit_per_pc = $is_perpc;
                   // die($uCost . ' cost');
                   $saveok = $inv_obj->addInventory($pm_qty, $unit_obj, $expiry, $serial, date("Y-m-d",strtotime($_POST['adj_date'])), $uCost, $lot);
                   

                }
                else if ($pm_qty < 0) {
                    $absadjqty = abs($pm_qty);
                    if($is_perpc == 0) {
                        $qtyperpack = $item_obj->getQtyPerBigUnit($sitem_code);
                        $absadjqty *= $qtyperpack;
                    }
    //                $inv_obj->setInventoryParams($sitem_code,$_POST['area_adj']);
                    $saveok = $inv_obj->remInventory($absadjqty, $unit_id, $expiry, $serial,date("Y-m-d",strtotime($_POST['adj_date'])));
                }
                else{
                    //do nothing
                }
            }

            if (!$saveok) break;
        }
        
        if ($saveok) {
            $skuobj->clearTmpTable();
        }
    }
//    $db->failTrans();
//    die;
    $db->completeTrans();
    if (!$saveok) $adj_obj->failTrans();

    $adj_obj->completeTrans();

    if ($saveok){ 
        $smarty->assign('sysInfoMessage','<strong>Successfully saved the adjustment!</strong>');
        $sql = "SELECT area_name FROM seg_areas WHERE area_code='".$_POST['area_adj']."'";
        $result = $db->Execute($sql);
        $row = $result->FetchRow();
        //$alert_obj->postAlert('SUP', 8, '', $patient_name, 'New urgent supply request posted from '.$row['area_name'], 'h', '');
    }
    else {
        $db->FailTrans();
        header('Location: '.$_SERVER['PHP_SELF'] . '?e=1');
    }


}

//render form values

# Assign Body Onload javascript code
$onLoadJS="onload=\"init()\"";
$hasError = @$_GET['e'];

if($hasError || $emptyAdj) {
    $smarty->assign('sysErrorMessage','<strong>Error:</strong> Incomplete Form');
}

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
<script type="text/javascript" src="js/adjust-gui.js?t=<?=time()?>"></script>

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

<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.maskedinput.js"></script>
<script language="javascript" >

var $j = jQuery.noConflict();
$j(function () {
    $j('#area_adj').change(function () {
        var $this = $j(this);
        var prevValue = $this.data('prev');
        var hasOrder = $j('tr[data-item="1"]').length;
        if (prevValue != 'blank' && $this.val() != '' && hasOrder > 0) {
            var response = confirm('Change area will clear the tray. Continue?');
            if (response) {
                emptyTray();
            } else {
                $this.val(prevValue);
            }
        }
        $this.data("prev", $this.val());
    });
});

function setDateMask(elem) {
    jQuery(function($) {
        if (elem.getAttribute("maskset") != "yes") {
            $j("#"+elem.id).mask("9999-99-99");
            elem.setAttribute("maskset", "yes");
        }
    });
}

</script>
<style>
 span.alignmid {
     display:inline-block;     
     width:50px;
     text-align: center;
 }
 </style>
<script type="text/javascript" language="javascript">    
<!--    
    var trayItems = 0;
    
    function init() {
      //  refreshDiscount();
    }
    
    function keyF9() {

        if (warnClear()) { 
            emptyTray(); overlib(
        OLiframeContent('issue-select-personnel.php',
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
    
    function keyF10() {
        
        
        $('issuing_id').setAttribute('value',''); 
       
        
//        callback = self.setInterval("checker()", 1);
        
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

    function openOrderTray() {
        var area = "ALL";
        area = $('area_adj').value;
        if(area == '')
        {
            alert('Select Area First');
            return false;
        }
        var url = 'seg-adjustment-tray.php?arealimit='+area;
        overlib(
            OLiframeContent(url, 660, 420, 'fOrderTray', 0, 'no'),
            WIDTH,660, TEXTPADDING,0, BORDER,0, 
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
            CAPTIONPADDING,2, 
            CAPTION,'Add Item for Adjustment tray',
            MIDX,0, MIDY,0, 
            STATUS,'Add Item for Adjustment tray');
        return false
    }
    
    function validate() {
        if (!$('adjrefno').value) {
            alert("Please enter the reference no.");
            $('adjrefno').focus();
            return false;
        }
        
        if (!$('area_adj').value) {
            alert("Please select area.");
            $('area_adj').focus();
            return false;
        }
        
        if (document.getElementsByName('items[]').length==0) {
            alert("Warning: The item list is empty...");
            return false;
        }else{
            var adj = document.getElementsByName('adjqty[]');
            var plus = document.getElementsByName('plusminqty[]');
            for(var i=0; i<adj.length; i++){
                if(!adj[i].value/* || plus[i].value == 0*/){
                    alert("Warning: Please fill in all adjusted quantity fields.");
                    return false;
                }
            }
        }

        return confirm('Process this supply adjustment?');
    }
-->
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Assign prompt messages

$lastnr = $adj_obj->getLastNr(date("Y-m-d"));

if (isset($_POST["submitted"]) && !$saveok) {
    $smarty->assign('sAdjItems',"
                <tr>
                    <td colspan=\"14\">Adjustment list is currently empty...</td>
                </tr>");
                
    if (is_array($_POST['items'])) {
        include_once($root_path."include/care_api_classes/class_product.php");
        $prod_obj = new Product();
        $items_name_array = $prod_obj->getProductName($_REQUEST['items']);
        
        $script = '<script type="text/javascript" language="javascript">';

        $items = $_POST['items'];

        $athandAdd = array();
        $adjquanAdd = array();
        $unitidAdd = array();
        $pmquanAdd = array();
        $perpcAdd = array();
        $expiryAdd = array();
        $serialAdd = array();
        $reasonsAdd = array();
                    
        foreach ($items as $i=>$item) {
            $athandAdd[$i] = $_POST['athand'][$i];
            $unitidAdd[$i] = $_POST['unit_ids'][$i];
            $adjquanAdd[$i] = $_POST['adjquan'][$i];
            $pmquanAdd[$i] = $_POST['pmquan'][$i];
            $perpcAdd[$i] = $_POST['is_unitpcs'][$i];
            $expiryAdd[$i] = $_POST['expiry'][$i];
            $serialAdd[$i] = $_POST['serial'][$i];
            $reasonsAdd[$i] = $_POST['reasons'][$i];
        }

        $script .= "var item0 = ['" .implode("','",$items)."'];";
        $script .= "var item_name0 = ['" .implode("','",$items_name_array)."'];";
        $script .= "var athand0 = [" .implode(",",$athandAdd). "];";
        $script .= "var expiry0 = ['" .implode("','",$expiryAdd). "'];";
        $script .= "var serial0 = ['" .implode("','",$serialAdd). "'];";
        $script .= "var adjquan0 = [" .implode(",",$adjquanAdd). "];";
        $script .= "var pmquan0 = [" .implode(",",$pmquanAdd). "];";
        $script .= "var unitid0 = ['" .implode("','",$unitidAdd). "'];";
        $script .= "var reasons0 = ['" .implode("','",$reasonsAdd). "'];";
        $script .= "var perpcAdd0 = [" .implode(",",$perpcAdd). "];"; 
        $script .= "xajax_add_item(item0, item_name0, athand0, expiry0, serial0, adjquan0, pmquan0, unitid0, reasons0, perpcAdd0);";
        $script .= "</script>";

        $src = $script;
    }
    if ($src) $smarty->assign('sAdjItems',$src);
}
else {
    $smarty->assign('sAdjItems',"
                <tr>
                    <td colspan=\"14\">Adjustment list is currently empty...</td>
                </tr>");
}

    ############################################
    $adj_obj = new SegAdjustment();  
    $lastnrthis = $adj_obj->getLastNr(date("Y-m-d")); 
	
	//$smarty->assign('sAdjRefno','<input id="adjrefno" name="adjrefno" type="text" value="'.$_POST['adjrefno'].'"/>');        
    $smarty->assign('sAdjRefno','<input id="adjrefno" name="adjrefno" type="text" value="'.((($submitted && !$saveok) || (isset($_GET["adjrefno"]))) ? $_GET["adjrefno"] : $lastnrthis).'"/>'); 
    $smarty->assign('sResetRefNo','<input class="jedButton" type="button" value="Reset" onclick="xajax_reset_referenceno()"/>'); 
	
	$smarty->assign('sAdjId','<input id="adj_id" name="adj_id"  readonly="readonly"  type="text" value="'.$_SESSION['sess_login_username'].'" size="20" /> ');
      
    $dbtime_format = "Y-m-d H:i";
    $fulltime_format = "F j, Y g:ia";
    if ($_REQUEST['dateset']) {
        $curDate = date($dbtime_format,$_REQUEST['dateset']);
        $curDate_show = date($fulltime_format, $_REQUEST['dateset']);
    }
    else {
        $curDate = date($dbtime_format);
        $curDate_show = date($fulltime_format);
    }
	
	$smarty->assign('sAdjDate','<span id="show_adjdate" class="jedInput" style="margin-left:0px; margin-top:3px; font-weight:bold; color:#0000c0; padding:0px 2px;width:80px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['adj_date'])) : $curDate_show).'</span><input class="jedInput" name="adj_date" id="adj_date" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['adj_date'])) : $curDate).'" style="font:bold 12px Arial">');
	$smarty->assign('sAdjCalendar','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="adjdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:0px;cursor:pointer">');
		$jsCalScript = "<script type=\"text/javascript\">
			Calendar.setup ({
				displayArea : \"show_adjdate\",
				inputField : \"adj_date\",
				ifFormat : \"%Y-%m-%d %H:%M\", 
				daFormat : \"    %B %e, %Y %I:%M%P\", 
				showsTime : true, 
				button : \"adjdate_trigger\", 
				singleClick : true,
				step : 1
			});
		</script>";
    $smarty->assign('jsCalendarSetup', $jsCalScript); 
    
    require_once($root_path.'include/care_api_classes/class_access.php');        
    require_once($root_path.'include/care_api_classes/class_department.php');
    
    $obj = new Access();    
    $dept_nr = $obj->getDeptNr($_SESSION['sess_temp_userid']);
    
    $per_arr = explode(" ", $_SESSION['sess_permission']);

    if (in_array("System_Admin", $per_arr) || in_array("_a_0_all", $per_arr)) $dept_nr = "''";       
    
    $objdept = new Department();
    $depscomma = $dept_nr;
    
//    #$subdepar = $objdept->getSubDept($dept_nr);
//    $qry = "SELECT fn_get_children_dept(".$dept_nr.") as dps";
//
//    $rs = $db->Execute($qry);
//
//    if($rs){
//        $row =  $rs->FetchRow();
//        $depscomma = $row["dps"];
//        if (empty($depscomma)){
////            $dept_nr = '';
//            $result = $objdept->getAreasInDept($dept_nr);
//        }
//        else
//            $result = $objdept->getAreasInADept($depscomma);
//    }
$result = $area_obj->getInventoryAreas();

$count = 0;
    $s_areacode = '';
    $ori_area = "<option value=\"\" $checked> Select area </option>\n";
    if ($result) {
        foreach($result as $row){
            $checked=(strtolower($row['area_code'])==strtolower($_GET['ori_area'])) || (strtolower($row['area_code']) == strtolower($_POST['area_adj'])) ? 'selected="selected"' : "";
            $ori_area .= "<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
            
            if ($checked || ($count == 0)) $s_areacode = $row['area_code'];                                    
            if ($checked) $index = $count;
            $count++;            
        }
    }
    else
        $ori_area = "<option value=\"\" $checked>- Assigned department has no areas -</option>\n";
    
    $ori_area = '<select class="jedInput" name="area_adj" id="area_adj" data-prev="blank">'."\n".$ori_area."</select>\n".
                "<input type=\"hidden\" id=\"area2\" name=\"area2\" value=\"".$_GET['ori_area']."\"/>";   
    $smarty->assign('sAdjArea',$ori_area);  
    
    $smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="100" rows="2" style="float:left; margin-left:3px;margin-top:3px">'.($submitted && !$saveok ? $_POST['remarks'] : $remarks).'</textarea>');  
    
    ############################################

$smarty->assign('sRootPath',$root_path);
$smarty->assign('sBtnAddItem','<img class="segSimulatedLink" id="add-item" src="'.$root_path.'images/btn_additems.gif" border="0" onclick="return openOrderTray();">');
$smarty->assign('sBtnEmptyList','<img class="segSimulatedLink" id="clear-list" src="'.$root_path.'images/btn_emptylist.gif" border="0" onclick="if (confirm(\'Clear the issuance list?\')) emptyTray()"/>');
       
if($error=="refno_exists"){
    $smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
    $smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}

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
  <input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$_COOKIE[$local_user.$sid])?>">
  <input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
  <input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
  <input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
  <input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
  <input type="hidden" name="target" value="<?php echo $target ?>">
  
  <input id="discount" name="discount" type="hidden" value="'.$_POST["discount"].'"/>
  <input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>
   
  <input id="issuing_id_hidden" name="issuing_id_hidden" type="hidden" value="<?= $_SESSION['sess_user_personell_nr'] ?>"/>
    
  <input type="hidden" name="editpencnum"   id="editpencnum"   value="">    
  <input type="hidden" name="editpentrynum" id="editpentrynum" value="">
  <input type="hidden" name="editpname" id="editpname" value="">
  <input type="hidden" name="editpqty"  id="editpqty"  value="">
  <input type="hidden" name="editppk"   id="editppk"   value="">
  <input type="hidden" name="editppack" id="editppack" value="">
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
$smarty->assign('sMainBlockIncludeFile','supply_office/adjustment-form.tpl');
$smarty->display('common/mainframe.tpl');

?>

<script>
//function checker() {
//  var name = $('issuing_id_hidden').value;
//  if (name != '') {
//    self.clearInterval(callback);
//    
//    jsAreaSRCOptionChngIss(name);
//    
//  }
//}

//var callback = self.setInterval("checker()", 100000);
</script>
