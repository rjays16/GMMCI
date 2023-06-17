<?php

#require_once($root_path.'include/care_api_classes/class_access.php');
require_once($root_path.'include/care_api_classes/inventory/class_request.php'); 
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

#added by bryan 03-31-09
require_once($root_path.'include/care_api_classes/alerts/class_alert.php');
$alert_obj = new SegAlert();

#added by bryan 12-01-09
require_once($root_path.'include/care_api_classes/class_area.php');
$area_obj = new SegArea();

require_once($root_path.'include/care_api_classes/class_order.php');
$order_obj = new SegOrder("pharma");
        
//if ($_GET["from"]=="CLOSE_WINDOW") {
// $smarty->assign('bHideTitleBar',TRUE);
// $smarty->assign('bHideCopyright',TRUE);
//}
    
# Title in the title bar
$smarty->assign('sToolbarTitle',"Supplies::$target Request");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Requisition::$target Request");

#global $db;
 
if (isset($_POST["submitted"])) {    
    $objrqst = new Request();   
    
    //added by bryan on 112609
    //latestrefno is obatained before saving
    if (($_POST['old_refno'])=="")
        $lastnrthis = $objrqst->getLastNr(date("Y-m-d"));
    else
        $lastnrthis = $_POST['old_refno']; 
    
    $data = array(
        'refno'=>$lastnrthis,
        'request_date'=>$_POST["orderdate"],
        'requestor_id'=>$_SESSION['sess_user_personell_nr'],
        'area_code'=>$_POST['ori_area'],
        'area_code_dest'=>$_POST['des_area']);

    $objrqst->setRequestHdr(); 
    $objrqst->setDataArray($data); 
    
    $objrqst->startTrans();    
    #echo print_r($data, true);
    
    if ($_POST['old_refno'] == '') {        
        // Insert new request ...        
        $saveok = $objrqst->insertDataFromInternalArray();
    }
    else { 
        // Update old refno.
        $saveok = $objrqst->delRqstDetails($_POST['old_refno']);        
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
            $descripption = $_POST['descs'][$i]; //description
            $total = $_POST['tot_'][$i];
            $avgerage = $_POST['avg_'][$i];
            $unitname = $_POST['is_unitname'][$i];
            $itemname = $_POST['itemName'][$i];

   

            $data = array(
                'refno'=>$lastnrthis,
                'item_code'=>$sitem_code,
                'item_qty'=>$nqty,
                'unit_id'=>$unit_id,
                'is_unitperpc'=>$is_perpc);
                
            
            $objrqst->setRequestDetail(); 
            $objrqst->setDataArray($data);      
            
            $saveok = $objrqst->insertRequestDetail();
            //added by julz
            $tablesRows.="<tr>
                  <td width='2%'> </td>
                  <td width='4%'>".$sitem_code."</td>
                  <td width='*'>".$itemname."</td>
                  <td width='5%' align='right'>".number_format((float)$avgerage, 2, '.', '')."</td>
                  <td class='centerAlign' nowrap='nowrap'>".$nqty."</td>
                  <td width='4%'>".$unitname."</td>
                  <td width='5%' align='right'>".number_format((float)$total, 2, '.', '')."</td>
                </tr>";

            if (!$saveok) break;
        }
    }    
    if (!$saveok) $objrqst->failTrans();
    $objrqst->completeTrans();
                            
    if ($saveok){ 
        //$smarty->assign('sPrintOption','<img class="segSimulatedLink" id="print-ext" src="'.$root_path.'images/btn_printpdf.gif" border="0" onclick="window.open(\'pdf_reqiss_slipbyrequest.php?refno='.$_POST["refno"].'\',null,\'height=600,width=870,status=yes,toolbar=no,menubar=no,location=no,resizable=yes\');">');     
        $smarty->assign('sysInfoMessage','<strong>Successfully saved the request!</strong>');
       //added by julz
        $smarty->assign('sRequestItems',$tablesRows);
         

        $sql = "SELECT area_name FROM seg_areas WHERE area_code='".$_POST['ori_area']."'";
        $result = $db->Execute($sql);
        $row = $result->FetchRow();
     
        

        //added by bryan on 120109
        $alert_obj->postAlert(
          $_POST['des_area'], 
          8, 
          '', 
          '',
          'New supply request posted from '.$area_obj->getAreaName($_POST['ori_area']), 
          'H', 
          serialize(
            array(
              'Serve' => 'modules/supply_office/seg-check-user.php?ntid=false&lang=en&userck=&target=issuancenew'
            )
          )
        );
        //echo $alert_obj->sql;
    }
    else
        $smarty->assign('sysErrorMessage','<strong>Error:</strong> '.(($err_msg = $objrqst->LastErrorMsg()) == '' ? $objrqst->getErrorMsg() : $err_msg));    
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
<script type="text/javascript" src="js/request-gui.js?t=<?=time()?>"></script>

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
        if (validate()) document.inputform.submit()
    }
    function openItemsTray() {
        var area = $('des_area').value;    
        var seg_URL_APPEND = $('seg_URL_APPEND').value;  
         
        //alert(area);             //+area
        var url = 'seg-supply-office-request-tray.php'+seg_URL_APPEND+'&area='+area;
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
        
//        if (!$('refno').value) {
//            alert("Please enter the reference no.");
//            $('refno').focus();
//            return false;
//        }
        
        if (!$('ori_area').value) {
            alert("Please enter requesting area.");
            $('ori_area').focus();
            return false;
        }
        
        if (!$('des_area').value) {
            alert("Please enter requested area.");
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
    $smarty->assign('sRequestItems',"
                <tr>
                    <td colspan=\"7\">Request list is currently empty ...</td>
                </tr>"); 
    
    // Populate the header array and details ...
    if (!isset($objrqst)) $objrqst = new Request();
    
    if ($result = $objrqst->getRequestHeader($_GET["refno"])) {                               
        $lastnr = $result["refno"]; 
        $_POST['old_refno'] = $lastnr;      
        $_REQUEST['dateset'] = $result["request_date"];
        $pid = $result["requestor_id"];
        $name = $result["requestor"];                
        $_POST['ori_area'] = $result["area_code"]; 
        $_POST['des_area'] = $result["area_code_dest"];
        $areaFrom = $_POST['des_area'];
        if ($result = $objrqst->getRequestDetails($_GET["refno"])) {                    
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
                $script .= "xajax_goAddItem(items0, units0, ispcs0, qtys0, '" . $areaFrom . "');";
                $script .= "</script>";
                $src = $script;    
            }
            else
                $smarty->assign('sRequestItems',"<tr>
                                                    <td colspan=\"6\">Request list is currently empty ...</td>
                                                 </tr>"); 
            if ($src) $smarty->assign('sRequestItems',$src);        
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
    $smarty->assign('sRequestItems',"
                <tr>
                    <td colspan=\"7\">Request list is currently empty ...</td>
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
// elseif (!isset($_GET["refno"])) {       
//     $smarty->assign('sRequestItems',"
//                 <tr>
//                     <td colspan=\"7\">Request list is currently empty ...</td>
//                 </tr>");
// }

# Render form elements
    $submitted = isset($_POST["submitted"]);
        
    require_once($root_path.'include/care_api_classes/class_access.php');        
    require_once($root_path.'include/care_api_classes/class_department.php');
    
    $objrqst = new Request();
    
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
    
    $depscomma = $dept_nr;
    
    #$subdepar = $objdept->getSubDept($dept_nr);  
    $qry = "SELECT fn_get_children_dept(".$dept_nr.") as dps";
    $rs = $db->Execute($qry);
    
    if($rs){
        $row =  $rs->FetchRow();
        $depscomma = $row["dps"];
        if (empty($depscomma)){

            $dept_nr = '';
            $result = $objdept->getAreasInDept($dept_nr);   
        }
        else{

            //modified by bryan 112609
            $depscomma = $depscomma.",".$dept_nr;
            $area = new SegArea();
            $result = $area->getInventoryAreas();
        }    
    }
    
    $count = 0;    
    $s_areacode = '';

    if ($result) {
        foreach($result as $row){
            $checked=(strtolower($row['area_code'])==strtolower($_GET['ori_area'])) || (strtolower($row['area_code']) == strtolower($_POST['ori_area'])) ? 'selected="selected"' : "";
            $ori_area .= "<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
        
            if ($checked || ($count == 0)) $s_areacode = $row['area_code'];                                    
            if ($checked) $index = $count;
            $count++;            
        }
    }
    else
        $ori_area = "<option value=\"\" $checked>- Assigned department has no areas -</option>\n";
    
    //edited by cha 10-07-09
          if($_GET['from']=="OR")
          {
          	$ori_area .= "<option value='OR' selected=''>OR Pharmacy</option>";
           /*	$ori_area = '<select class="jedInput" name="ori_area" id="ori_area" onclick="jsRqstngAreaOptionChng(this, this.options[this.selectedIndex].value);">'."\n".$or_area_option."</select>\n".
                "<input type=\"hidden\" id=\"area2\" name=\"area2\" value=\"".$_GET['ori_area']."\"/>";*/
		  }
		    $ori_area = '<select class="jedInput" name="ori_area" id="ori_area" onchange="jsRqstngAreaOptionChng(this, this.options[this.selectedIndex].value);">'."\n".$ori_area."</select>\n".
                "<input type=\"hidden\" id=\"area2\" name=\"area2\" value=\"".$_GET['ori_area']."\"/>"; 
      $smarty->assign('sSelectArea',$ori_area);
    
    //dest 
    $dest_area = "<option value=\"\" $checked>- Select Area -</option>\n";   
    $result = $area->getInventoryAreas();
    
    if ($result) {
        foreach($result as $row){
            $checked=(strtolower($row['area_code'])==strtolower($_GET['des_area'])) || (strtolower($row['area_code']) == strtolower($_POST['des_area'])) ? 'selected="selected"' : "";
            $dest_area .= "<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
            if ($checked) $index = $count;
            $count++;
        }
        $dest_area = '<select class="jedInput" name="des_area" id="des_area" >'."\n".$dest_area."</select>\n".
            "<input type=\"hidden\" id=\"area3\" name=\"area3\" value=\"".$_GET['des_area']."\"/>";
        $smarty->assign('sDestArea',$dest_area);
    }
    $smarty->assign('sReqId','<input id="pid" name="pid" type="hidden" value="'.$pid.'"/>');
    $smarty->assign('sReqName','<input class="jedInput" id="ordername" name="ordername" type="text" size="40" value="'.$name.'" style="font:bold 12px Arial;" disabled />');
    $smarty->assign('sRefNo', @$_GET["refno"]);
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
    $smarty->assign('sReqDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted && !$saveok ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted && !$saveok ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="font:bold 12px Arial">');

//    if ($_REQUEST['billing']) {
//        $smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" align="absmiddle" style="margin-left:2px;opacity:0.2">');
//    }
//    else {

    $smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
    $jsCalScript = "<script type=\"text/javascript\">
        Calendar.setup ({
            displayArea : \"show_orderdate\",
            inputField : \"orderdate\",
            ifFormat : \"%Y-%m-%d %H:%M\", 
            daFormat : \"    %B %e, %Y %I:%M%P\", 
            showsTime : true, 
            button : \"orderdate_trigger\", 
            singleClick : true,
            step : 1
        });
    </script>";
    $smarty->assign('jsCalendarSetup', $jsCalScript);    
    
//    }
    
//$smarty->assign('sRemarks','<textarea class="jedInput" name="comments" cols="14" rows="2" style="float:left; margin-left:3px;margin-top:3px">'.$_POST['comment'].'</textarea>');
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

<input type="hidden" id="seg_URL_APPEND" name="seg_URL_APPEND" value="<?=URL_APPEND?>"/> 
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
$smarty->assign('sMainBlockIncludeFile','supply_office/supply-request-form.tpl');
$smarty->display('common/mainframe.tpl');
?>