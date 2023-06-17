<?php
require_once($root_path.'include/care_api_classes/inventory/class_delivery.php');
require_once($root_path.'include/care_api_classes/inventory/class_serial.php'); 
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');
    
if ($_GET["from"]=="CLOSE_WINDOW") {
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);
}
    
# Title in the title bar
if (isset($_POST['source'])) $source = $_POST['source'];
$smarty->assign('sToolbarTitle',$source."::New Delivery");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',$source."::New Delivery");

#global $db;
 
if (isset($_POST["submitted"])) {   
    $objdel = new Delivery();
    
    $data = array(
        'refno'=>$_POST["refno"],
        'receipt_date'=>$_POST["delivery_date"],
        'receiving_id'=>$_SESSION['sess_user_personell_nr'],
        'area_code'=>$_POST['rcv_area'],
        'remarks'=>$_POST['remarks']
        );

    $objdel->useDeliveryHdr();
    $objdel->setDataArray($data); 
    
    $objdel->startTrans();    
    
    if ($_POST['old_refno'] == '') {        
        // Insert new delivery ...        
        $saveok = $objdel->insertDataFromInternalArray();
    }
    else { 
        // Update old refno.
        $saveok = $objdel->delDeliveryDetail($_POST['old_refno'], $_POST['old_deliverydte']);
        if ($saveok) $saveok = $objdel->updateDataFromInternalArray($_POST['old_refno'], FALSE);
    }
                
    if ($saveok) {                
        foreach ($_POST["items"] as $i=>$v) {
            $sitem_code = $v;                                   // item code
            $nqty = str_replace(',', '', $_POST['qtys'][$i]);   // qty
            $unit_id = $_POST['unit_ids'][$i];                  // unit ids
            $uprice  = $_POST['uprices'][$i];                   // unit price
            $is_perpc = $_POST['is_unitpcs'][$i];               // is unit per pc?
            
            $data = array(
                'refno'=>$_POST["refno"],
                'item_code'=>$sitem_code,
                'unit_price'=>$uprice,
                'item_qty'=>$nqty,
                'unit_id'=>$unit_id,
                'is_unitperpc'=>$is_perpc);
                
            #echo print_r($data, true);
            
            $objdel->useDeliveryDetails();     
            
            $expiry = $_POST['expiry_dts'][$i];                 // Expiry date
            if ($expiry == '') $expiry = NULL;
            
            $serial = $_POST['serial_nos'][$i];                 // Associated serial no.
            
            // Initialize serial object ...
            if ($serial != '') {
                $objserial = new Serial();
                $objserial->serial_no = $serial;
//                $objserial->item_code = $sitem_code;
                $objserial->area_code = $_POST['rcv_area'];
                $objserial->acquisition_cost = $uprice;
                $objserial->acquisition_date = $_POST["delivery_date"];
                $objserial->property_no = '';       // Default
            }
            else
                $objserial = NULL;
            
            $objdel->saveDeliveryDetail($_POST['rcv_area'], $data, $_POST["delivery_date"], $expiry, $objserial);

            if (!$saveok) break;
        }
        if ($saveok) $objdel->clearTmpTable();
    }    
    
    if (!$saveok) $objdel->failTrans();
    $objdel->completeTrans();
                            
    if ($saveok) 
        $smarty->assign('sysInfoMessage','<strong>Successfully saved the delivery!</strong>');
    else
        $smarty->assign('sysErrorMessage','<strong>Error:</strong> '.(($err_msg = $objdel->LastErrorMsg()) == '' ? $objdel->getErrorMsg() : $err_msg));    
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
<script type="text/javascript" src="<?=$root_path?>js/datefuncs.js"></script> 
<script type="text/javascript" src="js/seg-delivery-gui.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">

    var trayItems = 0;
    
    function init() {
<?php
    if (!$_REQUEST['viewonly']) {
?>
        // Edit/Submit shortcuts
        shortcut.add('F2', keyF2,
            {
                'type':'keydown',
                'propagate':false
            }
        );
        shortcut.add('F3', keyF3,
            {
                'type':'keydown',
                'propagate':false
            }
        );
        
<?php
    }
?>
        
    }
    
    function keyF2() {
        openItemsTray();
    }
    
    function keyF3() {
        if (confirm('Clear the delivery list?'))    emptyTray();
    }
    
    function keyF12() {
        if (validate()) document.inputform.submit()
    }
    function openItemsTray() {
        //var area = $('ori_area').value;                   +area
        var url = 'seg-delivery-tray.php?area=';
        overlib(
            OLiframeContent(url, 660, 397, 'fOrderTray', 0, 'no'),
            WIDTH,600, TEXTPADDING,0, BORDER,0, 
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
            CAPTIONPADDING,2, 
            CAPTION,'Add Item from Tray',
            MIDX,0, MIDY,0, 
            STATUS,'Add Item from Tray');
        return false
    }     
    
    function validate() {
        
        if (!$('refno').value) {
            alert("Please enter the reference no.");
            $('refno').focus();
            return false;
        }
        
        if (document.getElementsByName('items[]').length==0) {
            alert("Warning: The item list is empty...");
            return false;
        }
        return confirm('Process this delivery?');
    }

</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Render form values
if (isset($_POST["submitted"]) && !$saveok) {
    $smarty->assign('sDeliveryDetails',"
                <tr>
                    <td colspan=\"10\">Delivery is currently empty ...</td>
                </tr>");
                
    if (is_array($_POST['items'])) {
        $script = '<script type="text/javascript" language="javascript">';
        $items = $_POST['items'];
        $unitids = array();
        $expiry_dts = array();
        $serials = array();
        $is_pcs  = array();
        $qtys    = array();
        $uprices = array();
        
        foreach ($items as $i=>$item) {            
            $unitids[$i] = $_POST['unit_ids'][$i];
            $expiry_dts[$i] = $_POST['expiry_dts'][$i];
            $serials[$i] = $_POST['serial_nos'][$i]; 
            $is_pcs[$i]  = $_POST['is_unitpcs'][$i];
            $qtys[$i]    = $_POST['qtys'][$i];             
            if (!is_numeric($qtys[$i])) $qtys[$i] = '0';
            $uprices[$i] = $_POST['uprices'][$i];
        }
        
        $script .= "var items0 =['" .implode("','",$items)."'];";
        $script .= "var units0 =[" .implode(",",$unitids). "];";
        $script .= "var expirys0 = [" .implode(",",$expiry_dts). "];";
        $script .= "var serials0 = [" .implode(",",$serials). "];"; 
        $script .= "var ispcs0 = [" .implode(",",$is_pcs). "];";
        $script .= "var qtys0  = [" .implode(",",$qtys). "];";
        $script .= "var uprices0 =[" .implode(",",$uprices). "];";  
        $script .= "xajax_goAddItem(items0, units0, expirys0, serials0, ispcs0, qtys0, uprices0);";
        $script .= "</script>";
        $src = $script;    
    }
    if ($src) $smarty->assign('sDeliveryDetails',$src);
}
else {
    $smarty->assign('sDeliveryDetails',"
                <tr>
                    <td colspan=\"10\">Delivery is currently empty ...</td>
                </tr>");
}

# Render form elements
    $submitted = isset($_POST["submitted"]);    
        
    require_once($root_path.'include/care_api_classes/class_access.php');        
    require_once($root_path.'include/care_api_classes/class_department.php');
    
    $obj = new Access();    
    $dept_nr = $obj->getDeptNr($_SESSION['sess_temp_userid']);
    
    $objdept = new Department();
    $result = $objdept->getAreasInDept($dept_nr);
    
    $count = 0;    
    $s_areacode = '';
    if ($result) {
        while($row=$result->FetchRow()){
            $checked=(strtolower($row['area_code'])==strtolower($_GET['rcv_area'])) || (strtolower($row['area_code']) == strtolower($_POST['rcv_area'])) ? 'selected="selected"' : "";
            $rcv_area .= "<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
            
            if ($checked || ($count == 0)) $s_areacode = $row['area_code'];                                    
            if ($checked) $index = $count;
            $count++;            
        }
    }
    else
        $rcv_area = "<option value=\"\" $checked>- Select Receiving Area -</option>\n";
    
    $rcv_area = '<select class="segInput" name="rcv_area" id="rcv_area" onchange="jsRqstngAreaOptionChng(this, this.options[this.selectedIndex].value);">'."\n".$rcv_area."</select>\n".
                "<input type=\"hidden\" id=\"area2\" name=\"area2\" value=\"".$_GET['ori_area']."\"/>";
    $smarty->assign('sSelectArea',$rcv_area);    
    
    $smarty->assign('sReceivingID','<input id="pid" name="pid" type="hidden" value="'.$pid.'"/>');
    $smarty->assign('sReceivingNM','<input class="segInput" id="receivingname" name="receivingname" type="text" size="40" value="'.$HTTP_SESSION_VARS['sess_login_username'].'" style="font:bold 12px Arial;" disabled />');
    
    $smarty->assign('sRefNo','<input class="segInput" id="refno" name="refno" type="text" size="10" value="'.($submitted && !$saveok ? $_POST['refno'] : $lastnr).'" style="font:bold 12px Arial"/>');
    $smarty->assign('sResetRefNo','<input class="segButton" type="button" value="Reset" onclick="xajax_reset_referenceno()"/>');
    
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
    $smarty->assign('sDeliveryDate','<span id="show_deliverydate" class="segInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted && !$saveok ? date($fulltime_format,strtotime($_POST['delivery_date'])) : $curDate_show).'</span><input class="segInput" name="delivery_date" id="delivery_date" type="hidden" value="'.($submitted && !$saveok ? date($dbtime_format,strtotime($_POST['delivery_date'])) : $curDate).'" style="font:bold 12px Arial">');

    $smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="deliverydate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
    $jsCalScript = "<script type=\"text/javascript\">
        Calendar.setup ({
            displayArea : \"show_deliverydate\",
            inputField : \"delivery_date\",
            ifFormat : \"%Y-%m-%d %H:%M\", 
            daFormat : \"    %B %e, %Y %I:%M%P\", 
            showsTime : true, 
            button : \"deliverydate_trigger\", 
            singleClick : true,
            step : 1
        });
    </script>";
    $smarty->assign('jsCalendarSetup', $jsCalScript);    
    
$smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="100" rows="2" style="float:left; margin-left:0px;margin-top:3px">'.($submitted && !$saveok ? $_POST['remarks'] : '').'</textarea>');
$smarty->assign('sRootPath',$root_path);
$smarty->assign('sBtnAddItem','<img class="segSimulatedLink" id="add-item" src="'.$root_path.'images/btn_additems.gif" border="0" onclick="return openItemsTray();">');
$smarty->assign('sBtnEmptyList','<img class="segSimulatedLink" id="clear-list" src="'.$root_path.'images/btn_emptylist.gif" border="0" onclick="if (confirm(\'Clear the delivery list?\')) emptyTray()"/>');
$smarty->assign('sSearchField','<input class="segInput" id="searchField" name="searchField" type="text" size="30" placeholder="Search Purchase Order number..." value="" style="font:bold 12px Arial; margin-left:450px;" />');
$smarty->assign('sBtnSearch','<input class="segButton" type="button" value="Search" onclick="xajax_searchPO(searchField.value)"/>');


if($error=="refno_exists"){
    $smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
    $smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&target=new&clear_ck_sid=".$clear_ck_sid.'&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
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
<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
<input type="hidden" name="target" value="<?php echo $target ?>">
    
<input type="hidden" name="editpname" id="editpname" value="">
<input type="hidden" name="editpqty"  id="editpqty"  value="">
<input type="hidden" name="editppk"   id="editppk"   value="">
<input type="hidden" name="editppack" id="editppack" value="">
<input type="hidden" name="dateset" id="dateset" value="<?= $_REQUEST['dateset'] ?>"> 
<input type="hidden" name="old_refno" id="old_refno" value="">
<input type="hidden" name="old_deliverydte" id="old_deliverydte" value="">
<input type="hidden" name="source" id="source" value="<?=$source?>">
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
$smarty->assign('sMainBlockIncludeFile','inventory/delivery-form.tpl');
$smarty->display('common/mainframe.tpl');
?>

