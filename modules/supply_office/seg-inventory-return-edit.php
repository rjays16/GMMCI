<?php
//require_once($root_path.'include/care_api_classes/inventory/class_external_request.php'); 
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

require_once($root_path.'include/care_api_classes/alerts/class_alert.php');
$alert_obj = new SegAlert();
    
# Title in the title bar
$smarty->assign('sToolbarTitle',"Supplies::$target Returns");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Returns::$target Returns");
 
if (isset($_POST["submitted"])) {    
    $objrqst = new SegExternalRequest();   
    
    $data = array(
        'refno'=>$_POST["refno"],
        'request_date'=>$_POST["orderdate"],
        'requestor_id'=>$_SESSION['sess_user_personell_nr'],
        'area_code'=>$_POST['ori_area'],
        'procurer_nr'=>$_POST['des_area'],
        'remarks'=>$_POST['remarks']);

    $objrqst->setExternalRequestHdr(); 
    $objrqst->setDataArray($data); 
    
    $objrqst->startTrans();    
    
    if ($_POST['old_refno'] == '') {        
        // Insert new external request ...        
        $saveok = $objrqst->insertDataFromInternalArray();
    }
    else { 
        // Update old refno.
        $saveok = $objrqst->delExternalRqstDetails($_POST['old_refno']);        
        if ($saveok) {
            $objrqst->setWhereCondition("refno = '".$_POST['old_refno']."'");     
            $saveok = $objrqst->updateDataFromInternalArray($_POST['old_refno'], FALSE);
        }
    }            
    if ($saveok) {        
        foreach ($_POST["items"] as $i=>$v) {
            $sitem_code = $v;                                   // item code
            $nqty = str_replace(',', '', $_POST['qtys'][$i]);   // qty
            $unit_id = $_POST['unit_ids'][$i];                  // unit ids
            $is_perpc = $_POST['is_unitpcs'][$i];               // is unit per pc?
            
            $data = array(
                'refno'=>$_POST["refno"],
                'item_code'=>$sitem_code,
                'item_qty'=>$nqty,
                'unit_id'=>$unit_id,
                'is_unitperpc'=>$is_perpc);
            
            $objrqst->setExternalRequestDetail(); 
            $objrqst->setDataArray($data);      
            
            $saveok = $objrqst->insertExternalRequestDetail();

            if (!$saveok) break;
        }
    }    
    
    if (!$saveok) $objrqst->failTrans();
    $objrqst->completeTrans();
                            
    if ($saveok){       
        $smarty->assign('sPrintOption','<img class="segSimulatedLink" id="print-ext" src="'.$root_path.'images/btn_printpdf.gif" border="0" onclick="window.open(\'pdf_external_request.php?refno='.$_POST["refno"].'\',null,\'height=600,width=870,status=yes,toolbar=no,menubar=no,location=no,resizable=yes\');">');
        $smarty->assign('sysInfoMessage','<strong>Successfully saved the external request!</strong>');
        $sql = "SELECT area_name FROM seg_areas WHERE area_code='".$_POST['ori_area']."'";
        $result = $db->Execute($sql);
        $row = $result->FetchRow();
        
        $alert_obj->postAlert('SUP', 8, '', $patient_name, 'New urgent supply request posted from '.$row['area_name'], 'h', '');
    }
    else
        $smarty->assign('sysErrorMessage','<strong>Error:</strong> '.(($err_msg = $objrqst->LastErrorMsg()) == '' ? $objrqst->getErrorMsg() : $err_msg));    
}

# Assign Body Onload javascript code
$onLoadJS="onload=\"init()\"";
$smarty->assign('sOnLoadJs',$onLoadJS);
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
<script type="text/javascript" src="js/return-gui.js?t=<?=time()?>"></script>

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
                'propagate':false,
            }
        );
        shortcut.add('F3', keyF3,
            {
                'type':'keydown',
                'propagate':false,
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
        if (confirm('Clear the request list?'))    emptyTray();
    }
    
    function keyF12() {
        if (validate()) document.inputform.submit();
    }
    function openItemsTray() {
        //var area = $('ori_area').value;                   +area
        var url = 'seg-supply-office-request-tray.php?area=';
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
        
        if (!$('des_area').value) {
            alert("Please choose a procuring entity.");
            $('des_area').focus();
            return false;
        }
        
        if (document.getElementsByName('items[]').length==0) {
            alert("Warning: The item list is empty...");
            return false;
        }
        return confirm('Process this request?');
    }
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

if (isset($_GET["refno"])) {    
    $smarty->assign('sRetItems',"
                <tr>
                    <td colspan=\"6\">Return list is currently empty ...</td>
                </tr>"); 
    
    // Populate the header array and details ...
    if (!isset($objrqst)) $objrqst = new SegExternalRequest();
    
    if ($result = $objrqst->getExternalRequestHeader($_GET["refno"])) {                               
        $lastnr = $result["refno"]; 
        $_POST['old_refno'] = $lastnr;      
        $_REQUEST['dateset'] = $result["request_date"];
        $pid = $result["requestor_id"];
        $name = $result["requestor"];                
        $_POST['ori_area'] = $result["area_code"]; 
        $_POST['des_area'] = $result["procurer_nr"];
        $_POST['remarks'] = $result["remarks"];
                        
        if ($result = $objrqst->getExternalRequestDetails($_GET["refno"])) {                    
            $script = '<script type="text/javascript" language="javascript">';            
            $items = array();
            $unitids = array();
            $is_pcs  = array();
            $qtys    = array();
            
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {  
                    $items[]   = $row["item_code"];
                    $unitids[] = $row["unit_id"];
                    $is_pcs[]  = $row["is_unitperpc"];
                    $qtys[]    = $row["item_qty"];
                }
                                
                $script .= "var items0 =['" .implode("','",$items)."'];";
                $script .= "var units0 =[" .implode(",",$unitids). "];";
                $script .= "var ispcs0 =[" .implode(",",$is_pcs). "];";
                $script .= "var qtys0  =[" .implode(",",$qtys). "];";
                $script .= "xajax_goAddItem(items0, units0, ispcs0, qtys0);";
                $script .= "</script>";
                $src = $script;    
            }
            else
                $smarty->assign('sRetItems',"<tr>
                                                    <td colspan=\"6\">Return list is currently empty ...</td>
                                                 </tr>"); 
            if ($src) $smarty->assign('sRetItems',$src);        
        } 
    }
    else
        var_dump($objrqst->sql);
}
else {
    $pid  = $_SESSION['sess_user_personell_nr'];
    $name = $_SESSION['sess_login_username'];
}

# Render form values
if (isset($_POST["submitted"]) && !$saveok) {
    $smarty->assign('sRetItems',"
                <tr>
                    <td colspan=\"6\">Return list is currently empty ...</td>
                </tr>");
                
    if (is_array($_POST['items'])) {
        $script = '<script type="text/javascript" language="javascript">';
        $items = $_POST['items'];
        $unitids = array();
        $is_pcs  = array();
        $qtys    = array();
        
        foreach ($items as $i=>$item) {            
            $unitids[$i] = $_POST['unit_ids'][$i];
            $is_pcs[$i]  = $_POST['is_unitpcs'][$i];
            $qtys[$i]    = $_POST['qtys'][$i]; 
            if (!is_numeric($qtys[$i])) $qtys[$i] = '0';
        }
        
        $script .= "var items0 =['" .implode("','",$items)."'];";
        $script .= "var units0 =[" .implode(",",$unitids). "];";
        $script .= "var ispcs0 =[" .implode(",",$is_pcs). "];";
        $script .= "var qtys0  =[" .implode(",",$qtys). "];";
        $script .= "xajax_goAddItem(items0, units0, ispcs0, qtys0);";
        $script .= "</script>";
        $src = $script;    
    }
    if ($src) $smarty->assign('sRequestItems',$src);
}
elseif (!isset($_GET["refno"])) {       
    $smarty->assign('sRetItems',"
                <tr>
                    <td colspan=\"6\">Return list is currently empty ...</td>
                </tr>");
                
    if (is_array($_POST['items'])) {
        $script = '<script type="text/javascript" language="javascript">';
        $items = $_POST['items'];
        $unitids = array();
        $is_pcs  = array();
        $qtys    = array();
        
        foreach ($items as $i=>$item) {            
            $unitids[$i] = $_POST['unit_ids'][$i];
            $is_pcs[$i]  = $_POST['is_unitpcs'][$i];
            $qtys[$i]    = $_POST['qtys'][$i]; 
            if (!is_numeric($qtys[$i])) $qtys[$i] = '0';
        }
        
        $script .= "var items0 =['" .implode("','",$items)."'];";
        $script .= "var units0 =[" .implode(",",$unitids). "];";
        $script .= "var ispcs0 =[" .implode(",",$is_pcs). "];";
        $script .= "var qtys0  =[" .implode(",",$qtys). "];";
        $script .= "xajax_goAddItem(items0, units0, ispcs0, qtys0);";
        $script .= "</script>";
        $src = $script;    
    }
    if ($src) $smarty->assign('sRetItems',$src);
}

# Render form elements
    $submitted = isset($_POST["submitted"]);
        
    require_once($root_path.'include/care_api_classes/class_access.php');        
    require_once($root_path.'include/care_api_classes/class_department.php');
    require_once($root_path.'include/care_api_classes/inventory/class_supplier.php'); 
    
    $obj = new Access();    
    $dept_nrin = $obj->getDeptNr($_SESSION['sess_temp_userid']);
    
    $per_arr = explode(" ", $HTTP_SESSION_VARS['sess_permission']);

    if (in_array("System_Admin", $per_arr)) $dept_nr = '';
    
    $objdept = new Department();
    $result = $objdept->getAreasInDept($dept_nr);

    
    $count = 0;    
    $s_areacode = '';
    if ($result) {
        while($row=$result->FetchRow()){
            $checked=(strtolower($row['area_code'])==strtolower($_GET['ori_area'])) || (strtolower($row['area_code']) == strtolower($_POST['ori_area'])) ? 'selected="selected"' : "";
            $ori_area .= "<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
            
            if ($checked || ($count == 0)) $s_areacode = $row['area_code'];                                    
            if ($checked) $index = $count;
            $count++;            
        }
    }
    else
        $ori_area = "<option value=\"\" $checked>- Select Requesting Area -</option>\n";
    
    $ori_area = '<select class="jedInput" name="from_area" id="from_area" onchange="jsFromAreaOptionChng(this, this.options[this.selectedIndex].value);">'."\n".$ori_area."</select>\n".
                "<input type=\"hidden\" id=\"area2\" name=\"area2\" value=\"".$_GET['ori_area']."\"/>";
    $smarty->assign('sReturnAreaFrom',$ori_area);    

    //dest    
    $result = $objdept->getAllAreas($s_areacode);
    if ($result) {
        while($row=$result->FetchRow()){
            $checked=(strtolower($row['area_code'])==strtolower($_GET['area_dest'])) || (strtolower($row['area_code']) == strtolower($_POST['area_dest'])) ? 'selected="selected"' : "";
            $dest_area .= "<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
            if ($checked) $index = $count;
            $count++;
        }
        $dest_area = '<select class="jedInput" id="to_area" name="to_area">'."\n".$dest_area."</select>\n".
            "<input type=\"hidden\" id=\"area3\" name=\"area3\" value=\"".$_GET['area_dest']."\"/>";
        $smarty->assign('sReturnArea',$dest_area);
    }
    
    $smarty->assign('sRetId','<input id="pid" name="pid" type="hidden" value="'.$pid.'"/>');
    $smarty->assign('sRetName','<input class="jedInput" id="returnname" name="returnname" type="text" size="40" value="'.$name.'" style="font:bold 12px Arial;" disabled />');
    $smarty->assign('sRetRefNo','<input class="jedInput" id="refno" name="refno" type="text" size="10" value="'.($submitted && !$saveok ? $_POST['refno'] : $lastnr).'" style="font:bold 12px Arial"/>');
    $smarty->assign('sResetRetRefNo','<input class="jedButton" type="button" value="Reset" onclick="xajax_reset_referenceno()"/>');
    
    $dbtime_format = "Y-m-d H:i";
    $fulltime_format = "F j, Y g:ia";
    if ($_REQUEST['dateset']) {
        $curDate = date($dbtime_format, strtotime($_REQUEST['dateset']));
        $curDate_show = date($fulltime_format, strtotime($_REQUEST['dateset']));
    }
    else {
        $curDate = date($dbtime_format);
        $curDate_show = date($fulltime_format);
    }
    $smarty->assign('sRetDate','<span id="show_returndate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted && !$saveok ? date($fulltime_format,strtotime($_POST['returndate'])) : $curDate_show).'</span><input class="jedInput" name="returndate" id="returndate" type="hidden" value="'.($submitted && !$saveok ? date($dbtime_format,strtotime($_POST['returndate'])) : $curDate).'" style="font:bold 12px Arial">');

    $smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="returndate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
    $jsCalScript = "<script type=\"text/javascript\">
        Calendar.setup ({
            displayArea : \"show_returndate\",
            inputField : \"returndate\",
            ifFormat : \"%Y-%m-%d %H:%M\", 
            daFormat : \"    %B %e, %Y %I:%M%P\", 
            showsTime : true, 
            button : \"returndate_trigger\", 
            singleClick : true,
            step : 1
        });
    </script>";
    $smarty->assign('jsCalendarSetup', $jsCalScript);  
    
    $smarty->assign('sRetRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="100" rows="2" style="float:left; margin-left:3px;margin-top:3px">'.($submitted && !$saveok ? $_POST['remarks'] : $remarks).'</textarea>');           
    
$smarty->assign('sRootPath',$root_path);
$smarty->assign('sBtnAddItem','<img class="segSimulatedLink" id="add-item" src="'.$root_path.'images/btn_additems.gif" border="0" onclick="return openItemsTray();">');
$smarty->assign('sBtnEmptyList','<img class="segSimulatedLink" id="clear-list" src="'.$root_path.'images/btn_emptylist.gif" border="0" onclick="if (confirm(\'Clear the order list?\')) emptyTray()"/>');
   
if($error=="refno_exists"){
    $smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
    $smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&target=New&clear_ck_sid=".$clear_ck_sid.'&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
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
<input type="hidden" name="old_refno" id="old_refno" value="<?= $_GET['refno'] ?>">
<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';    
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
$smarty->assign('sContinueButton','<img src="'.$root_path.'images/btn_submitorder.gif" align="center" onclick="if (validate()) document.inputform.submit()"  style="cursor:pointer" />');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','supply_office/returns-form.tpl');
$smarty->display('common/mainframe.tpl');
?>
