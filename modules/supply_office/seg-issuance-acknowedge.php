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
if (!$_GET['from'])
    $breakfile=$root_path."modules/supply_office/seg-supply-functions.php".URL_APPEND."&userck=$userck";
else {
    if ($_GET['from']=='CLOSE_WINDOW')
        $breakfile = "javascript:if (window.parent.myClick) window.parent.myClick(); else window.parent.cClick();";
    else
        $breakfile = $root_path.'modules/supply_office/seg-issuance-test.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$imgpath=$root_path."pharma/img/";
$thisfile='seg-issuance-test.php';


$enc = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)');

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

$user_location = $HTTP_SESSION_VARS['sess_user_personell_nr'];

if($HTTP_SESSION_VARS['sess_user_personell_nr']) {
    $sqlLOC = "SELECT location_nr FROM care_personell_assignment WHERE personell_nr=".$HTTP_SESSION_VARS['sess_user_personell_nr'];  $resultLOC = $db->Execute($sqlLOC);                                                            
    $rowLOC = $resultLOC->FetchRow();
}

if (isset($_POST["submitted"])) {
        
    $bulk = array();
    $total = 0;
    /*
    foreach ($_POST["items"] as $i=>$v) {
        $bulk[] = array($_POST["items"][$i],$_POST["pending"][$i],$_POST["unitid"][$i],$_POST["perpc"][$i]);
    }
    $data = array(
        'refno'=>$_POST['refno'],
        'issue_date'=>$_POST['issue_date'],
        'dept_issued'=>$_POST['dept_issued'],
        'authorizing_id'=>$_POST['authorizing_id'],
        'issuing_id'=>$_POST['issuing_id']
    );
    
    echo $_POST['unitdesc'][0].",".$_POST['unitdesc'][1].",";
    */
   $sql =  "INSERT INTO seg_issuance (refno,issue_date,src_dept_nr,dept_nr,authorizing_id,issuing_id) VALUES ('".$_POST['refno']."','".$_POST['issue_date']."',".$rowLOC['location_nr'].",".$_POST['dept_issued'].",".$_POST['authorizing_id_hidden'].",".$_POST['issuing_id_hidden'].")";
   echo  $sql."<br>";
   $result = $db->Execute($sql);
   $error = $db->ErrorMsg();
   $okba = $db->Affected_Rows();
   
   echo $_REQUEST['expdate'][0];
   //echo $sql."<br>".$_POST['name'][0];
      
    if ($okba) {
        $counter=0;
        foreach($_POST["items"] as $i) {
            
            //$sql1000 = "SELECT prod_class FROM care_pharma_products_main WHERE bestellnum='".."'";
            if($_REQUEST['expdate'][$counter]!="-")  $sql2 = "INSERT INTO seg_issuance_details (refno,item_code,item_qty,unit_id,is_unitperpc,expiry_date) VALUES ('".$_POST['refno']."','".$_POST['items'][$counter]."',".$_POST['pending'][$counter].",".$_POST['unitid'][$counter].",".$_POST['perpc'][$counter].",'".$_REQUEST['expdate'][$counter]."')"; 
            else if($_REQUEST['serial'][$counter]!="-")  $sql2 = "INSERT INTO seg_issuance_details (refno,item_code,item_qty,unit_id,is_unitperpc,serial_no) VALUES ('".$_POST['refno']."','".$_POST['items'][$counter]."',".$_POST['pending'][$counter].",".$_POST['unitid'][$counter].",".$_POST['perpc'][$counter].",'".$_REQUEST['serial'][$counter]."')";
            else $sql2 = "INSERT INTO seg_issuance_details (refno,item_code,item_qty,unit_id,is_unitperpc) VALUES ('".$_POST['refno']."','".$_POST['items'][$counter]."',".$_POST['pending'][$counter].",".$_POST['unitid'][$counter].",".$_POST['perpc'][$counter].")";
           echo  $sql2; 
           $result2 = $db->Execute($sql2);
           $error2 = $db->ErrorMsg();
           $okba2 = $db->Affected_Rows();
            
           $counter++;
            
        }
        
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
        
        $fetchDepartment = "SELECT name_formal FROM care_department WHERE nr=".$_REQUEST['dept_issued'];
        $deptResult = $db->Execute($fetchDepartment);
        $deptRow = $deptResult->FetchRow();
        
        $smarty->assign('sDepartment', $deptRow['name_formal']);
        
        $fetchDepartment = "SELECT name_formal FROM care_department WHERE nr=".$rowLOC['location_nr'];
        $deptResult = $db->Execute($fetchDepartment);
        $deptSrcRow = $deptResult->FetchRow();
        
        $smarty->assign('sSrcDepartment', $deptSrcRow['name_formal']);
          
        foreach ($_REQUEST['items'] as $i=>$v){
            
            $items_table[] = "<tr><td>".$_REQUEST['items'][$i]."</td><td>".$_REQUEST['name'][$i]."</td><td>". $_REQUEST['pending'][$i] ."</td><td>". $_REQUEST['unitdesc'][$i] ."</td><td>". $_REQUEST['serial'][$i] ."</td><td>". $_REQUEST['expdate'][$i] ."</td></tr>";
            
        
        }
            //$items[] = "<td>". $_REQUEST['pending'][$i] . " " .$items_array[$v]. "</li>";
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
        else
            $smarty->assign('sysErrorMessage',"<strong>Error:</strong> $errorMsg");
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


<script type="text/javascript" language="javascript">
<!--
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
        shortcut.add('F9', keyF9,
            {
                'type':'keydown',
                'propagate':false,
            }
        );
        shortcut.add('F12', keyF12,
            {
                'type':'keydown',
                'propagate':false,
            }
        );
<?php
    }
?>
      //  refreshDiscount();
    }
    
    function keyF2() {
        openOrderTray();
    }
    
    function keyF3() {
        if (confirm('Clear the issue list?'))    emptyTray();
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
        var url = 'seg-issue-tray.php';
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
        
        #$ihap = 0;
        foreach ($items as $i=>$item) {
            $pendingAdd[$i] = $_POST['pending'][$i];
            $descAdd[$i] = $_POST['desc'][$i];
            $unitidAdd[$i] = $_POST['unitid'][$i];
            $unitdescAdd[$i] = $_POST['unitdesc'][$i];
            $perpcAdd[$i] = $_POST['perpc'][$i];
            $expdateAdd[$i] = $_POST['expdate'][$i];
            $serialAdd[$i] = $_POST['serial'][$i];
            
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
        #*/
        /*
        $script .= "var item0= ".$_POST['items'].";";
        $script .= "var item_name0= ".$items_name_array.";";
        $script .= "var desc0 = ".$_POST['desc'].";";
        $script .= "var pending0 = ".$_POST['pending'].";";
        $script .= "var unitid0= ".$_POST['unitid'].";";
        $script .= "var perpc0 = ".$_POST['perpc'].";";
        */
        $script .= "xajax_add_item(item0, item_name0, desc0, pending0, unitid0, perpc0, unitdesc0, expdate0, serial0);";
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
    $readOnly = ($submitted && (!$_POST['iscash'] || $_POST['pid'])) ? 'readonly="readonly"' : "";

    if ($person) {
        $_POST['pid'] = $person['pid'];
        $_POST['encounter_nr'] = $person['encounter_nr'];
        $_POST['ordername'] = $person['name_first']." ".$person['name_last'];
        
        $addr = implode(", ",array_filter(array($person['street_name'], $person["brgy_name"], $person["mun_name"])));
        if ($person["zipcode"])
            $addr.=" ".$person["zipcode"];
        if ($person["prov_name"])
            $addr.=" ".$person["prov_name"];
        $_POST['orderaddress'] = $addr;
        $_POST['discount_id'] = $person['discount_id'];
        $_POST['discount'] = $person['discount'];
    }
    
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
    
    $smarty->assign('sRefno','<input id="refno" name="refno" type="text" value="'.$_POST['refno'].'"/>');
      
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
    
    $smarty->assign('sIssueDate','<span id="show_issuedate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="jedInput" name="issue_date" id="issue_date" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="font:bold 12px Arial">');
    $smarty->assign('sIssueCalendar','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="issuedate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
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
    
    $deptHTML = '<select class="jedInput" id="dept_issued" name="dept_issued">';

    include_once($root_path."include/care_api_classes/class_department.php");
    $dep = new Department();
    $dept = $dep->getAllActiveObject();
    
    if (isset($_POST["submitted"])) {
    $fetchDepartment = "SELECT name_formal FROM care_department WHERE nr=".$_REQUEST['dept_issued'];
    $deptResult = $db->Execute($fetchDepartment);
    $deptRow = $deptResult->FetchRow();
    
    $deptHTML .= "<option value=\"".$_POST['dept_issued']."\">".$deptRow['name_formal']."</option>\n";
    }
    
    while($row=$dept->FetchRow()){
        $deptHTML .= "                                    <option value=\"".$row['nr']."\">".$row['name_formal']."</option>\n";
    }
    $deptHTML .= "                    </select>";
    
    $smarty->assign('sDepartmentIssued',$deptHTML);
       
    $smarty->assign('sAuthorizedId','<input id="authorizing_id" name="authorizing_id" type="text" value="'.$_POST['authorizing_id'].'" size="18"/>');
    $smarty->assign('sAuthorizedButton','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer"
       onclick="keyF9()"
       onmouseout="nd();" />');  
    
    $smarty->assign('sIssuingId','<input id="issuing_id" name="issuing_id" type="text" value="'.$_POST['issuing_id'].'" size="18"/> ');
    $smarty->assign('sIssueButton','<img id="select-enc1" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer"
       onclick="keyF10()"
       onmouseout="nd();" />');  
    
    

# LINGAP/CMAP
if (true) {
    $sponsorHTML = '<select class="jedInput" name="sponsor" id="sponsor">
<option value="" style="font-weight:bold">No coverage</option>
';
    include_once($root_path."include/care_api_classes/class_sponsor.php");
    $sc = new SegSponsor();
    $sponsors = $sc->get();
    while($row=$sponsors->FetchRow()){
        $sponsorHTML .= "                                    <option value=\"".$row['sp_id']."\">".$row['sp_name']."</option>\n";
    }
    $sponsorHTML .= "                    </select>";
    $smarty->assign('sSponsor',$sponsorHTML);
}

$smarty->assign('sSWClass',($_POST['discountid'] ? $_POST['discountid'] : 'None'));
$smarty->assign('sNormalPriority','<input class="jedInput" type="radio" name="priority" id="p0" value="0" '.(($_POST["priority"]!="1")?'checked="checked" ':'').'/><label class="jedInput" for="p0">Normal</label>');
$smarty->assign('sUrgentPriority','<input class="jedInput" type="radio" name="priority" id="p1" value="1" '.(($_POST["priority"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="p1">Urgent</label>');
$smarty->assign('sComments','<textarea class="jedInput" name="comments" cols="14" rows="2" style="float:left; margin-left:3px;margin-top:3px">'.$_POST['comment'].'</textarea>');
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
if ( $_GET['billing'] ) $qs .= "&billing=".$_GET['billing'];
if ( $_GET['pid'] ) $qs .= "&pid=".$_GET['pid'];
if ( $_GET['encounterset'] ) $qs .= "&encounterset=".$_GET['encounterset'];

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
    <input id="issuing_id_hidden" name="issuing_id_hidden" type="hidden" value="<?= $_REQUEST['issuing_id_hidden'] ?>"/>
    
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
$smarty->assign('sMainBlockIncludeFile','supply_office/supply-acknowledge-form.tpl');
$smarty->display('common/mainframe.tpl');

?>
