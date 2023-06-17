<?php 

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/supply_office/ajax/issue-acknowledge-common.php");


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
        $breakfile = $root_path.'modules/supply_office/seg-issuance-acknowledge.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$imgpath=$root_path."pharma/img/";
$thisfile='seg-issuance-acknowledge.php';


$enc = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)');

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 
# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
include_once($root_path."include/care_api_classes/class_area.php");
include_once($root_path."include/care_api_classes/class_personell.php");
include_once($root_path."include/care_api_classes/class_order.php");
$order_obj = new SegOrder("pharma");

global $db;

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');
    
if ($_GET["from"]=="CLOSE_WINDOW") {
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);
}

    
# Title in the title bar
$smarty->assign('sToolbarTitle',"Supplies::Issuance::Acknowledge");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Supplies::Issuance::Acknowledge");

$user_location = $_SESSION['sess_user_personell_nr'];


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


 
<!-- <script type="text/javascript" src="js/issue-acknowledge-gui.js"></script> -->

<script type="text/javascript" src="js/issue-acknowledge.js"></script>  

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

<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/container/assets/container.css">




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

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" /> 
<script type='text/javascript' src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.datetimepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.datetimepicker/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript">var $j = jQuery.noConflict();</script>


<script type="text/javascript" language="javascript">
<!--
    var trayItems = 0;
    
    var oldcClick = cClick;
    cClick = function (){
        oldcClick();
        window.location.href=window.location.href;    
    }
    
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
        shortcut.add('F9', keyF9,
            {
                'type':'keydown',
                'propagate':false
            }
        );
        shortcut.add('F12', keyF12,
            {
                'type':'keydown',
                'propagate':false
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
    
    function validate() {
        return true;
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
    
    function seldateOnChange() {
        var optSelected = $('seldate').options[$('seldate').selectedIndex]
        var spans = document.getElementsByName('seldateoptions')
        for (var i=0; i<spans.length; i++) {
            if (optSelected) {
                if (spans[i].getAttribute("segOption") == optSelected.value) {
                    spans[i].style.display = ""
                }
                else
                    spans[i].style.display = "none"
            }
        }
    }
    
    function openIssueDetails(refno,area,srcarea) {
        //var area = "ALL";
        var enc = $j('#encounter_nr').val();
        var pid = $j('#pid').val();
        var seg_URL_APPEND = $j('#seg_URL_APPEND').val();
        var url = 'seg-issue-details.php?refno='+refno+'&destination='+area+'&source='+srcarea;
        dialogInsuance = $j('<div></div>')
            .html('<iframe style="border: 0px; " src="' + url + '" width="100%" height=450px></caiframe>')
            .dialog({
                autoOpen: true,
                modal: true,
                show: 'fade',
                hide: 'fade',
                height: 'auto',
                width: '65%',
                title: 'View Issuance Details for Acknowledgement',
                position: 'top',
                close: function(event, ui){
                    location.reload(); 
                }
            });
        return false;


        
        // overlib(
        //     OLiframeContent(url, 700, 420, 'fOrderTray', 0, 'no'),
        //     WIDTH,660, TEXTPADDING,0, BORDER,0, 
        //     STICKY, SCROLL, CLOSECLICK, MODAL,
        //     CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
        //     CAPTIONPADDING,2, 
        //     CAPTION,'View Issuance Details for Acknowledgement',
        //     MIDX,0, MIDY,0, 
        //     STATUS,'View Issuance Details for Acknowledgement');
        // return false
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

if($_SESSION['sess_user_personell_nr']) {
    $sqlLOC = "SELECT location_nr FROM care_personell_assignment WHERE personell_nr=".$_SESSION['sess_user_personell_nr'];  
    $resultLOC = $db->Execute($sqlLOC);                                                            
    $rowLOC = $resultLOC->FetchRow();
}
/*
$smarty->assign('sIssueItems',"
                <tr>
                    <td colspan=\"8\">Issue list is currently empty...</td>
                </tr>");
 */ 
 /*
$title_sufx = "Issuances";             
$sql = "SELECT area_code FROM seg_areas WHERE dept_nr=".$rowLOC['location_nr']; 
$result = $db->Execute($sql);
$a = 0;
$ic = 0;
echo $sql."<br>";
if($rowLOC['location_nr']){
    while($rowa = $result->fetchRow())
    {
        $a++;
    }
}
else $a=1;
*/
$a=1;
if ($a>0) {  
        if ($_REQUEST['chkdate']) {
            switch(strtolower($_REQUEST["seldate"])) {
                case "today":
                    $search_title = "Today's $title_sufx";
                    $filters['DATETODAY'] = "";
                break;
                case "thisweek":
                    $search_title = "This Week's $title_sufx";
                    $filters['DATETHISWEEK'] = "";
                break;
                case "thismonth":
                    $search_title = "This Month's $title_sufx";
                    $filters['DATETHISMONTH'] = "";
                break;
                case "specificdate":
                    $search_title = "$title_sufx On " . date("F j, Y",strtotime($_REQUEST["specificdate"]));
                    $dDate = date("Y-m-d",strtotime($_REQUEST["specificdate"]));                
                    $filters['DATE'] = $dDate;
                break;
                case "between":
                    $search_title = "$title_sufx From " . date("F j, Y",strtotime($_REQUEST["between1"])) . " To " . date("F j, Y",strtotime($_REQUEST["between2"]));
                    $dDate1 = date("Y-m-d",strtotime($_REQUEST["between1"]));
                    $dDate2 = date("Y-m-d",strtotime($_REQUEST["between2"]));
                    $filters['DATEBETWEEN'] = array($dDate1,$dDate2);
                break;
            }
        }
             
        include_once($root_path."include/care_api_classes/inventory/class_issuance.php");
        $issue_obj = new Issuance();
        $i = 0;
        $script = '<script type="text/javascript" language="javascript">'; 
        $refnoAdd = array();
        $issdateAdd = array();
        $srcareaAdd = array();
        $areaAdd = array();
        $authidAdd = array();
        $issidAdd = array(); 
        
        $srcareanameAdd = array();
        $areanameAdd = array();
        $authidnameAdd = array();
        $issidnameAdd = array();
        
        require_once($root_path.'include/care_api_classes/class_access.php');        
        require_once($root_path.'include/care_api_classes/class_department.php');
        
        $obj = new Access();    
        $dept_nr = $obj->getDeptNr($_SESSION['sess_temp_userid']);
        
        $per_arr = explode(" ", $_SESSION['sess_permission']);

        if (in_array("System_Admin", $per_arr) || in_array("_a_0_all", $per_arr)) {
            $dept_nr = "''";
        }
        else {
            $dept_nr = "''";
        }
        
//        $objdept = new Department();        
        
        #$subdepar = $objdept->getSubDept($dept_nr);  
        $qry = "SELECT fn_get_children_dept(".$dept_nr.") as dps";        
        
        $rs = $db->Execute($qry);        
        if($rs){
            $row =  $rs->FetchRow();
            $depscomma = $row["dps"];
            $depscomma = trim($depscomma);
            if (empty($depscomma) || $depscomma != "''"){
                //$dept_nr = '';
                $resultDept = $issue_obj->getIssuancebyDepartment($dept_nr, $filters); 
            }
            else {
                $depscomma = $depscomma.",".$dept_nr;
                $resultDept = $issue_obj->getIssuancebyDepartment($depscomma, $filters);
            }
        }        
                        
        //$resultDept = $issue_obj->getIssuancebyDepartment($rowLOC['location_nr'], $filters);
        $ic = $db->Affected_Rows();             
//        echo "ic = ".$ic;        
        if($ic>0){            
            $areaobj = new SegArea();
            $personell = new Personell();
            
            $i = 0;
            while($issueRow = $resultDept->FetchRow()){
                $refnoAdd[$i] = $issueRow['refno'];
                $issdateAdd[$i] = $issueRow['issue_date'];
                
                $srcareaAdd[$i] = $issueRow['src_area_code'];                
                $srcareanameAdd[$i] = $areaobj->getAreaName($issueRow['src_area_code']);
                
                $areaAdd[$i] = $issueRow['area_code'];                
                $areanameAdd[$i] = $areaobj->getAreaName($issueRow['area_code']);
                
                $authidAdd[$i] = $issueRow['authorizing_id'];                
                $person = $personell->get_Person_name2($issueRow['authorizing_id']);
                $name = $personell->concatname($person['name_last'], $person['name_first'], '');                
                $authidnameAdd[$i] = $name;
                
                $issidAdd[$i] = $issueRow['issuing_id'];
                $person = $personell->get_Person_name2($issueRow['issuing_id']);
                $name = $personell->concatname($person['name_last'], $person['name_first'], '');                
                $issidnameAdd[$i] = $name;                                
                #echo $issueRow['refno'].",".$issueRow['issue_date'].",".$issueRow['src_area_code'].",".$issueRow['area_code']."<br>";
                $i++;
            } 
        
        $script .= "var refno0 = ['" .implode("','",$refnoAdd)."'];";
        $script .= "var issdate0= ['" .implode("','",$issdateAdd)."'];";
        $script .= "var srcarea0 = ['" .implode("','",$srcareaAdd). "'];";
        $script .= "var area0 = ['" .implode("','",$areaAdd). "'];";
        $script .= "var authid0= ['" .implode("','",$authidAdd). "'];";
        $script .= "var issid0= ['" .implode("','",$issidAdd). "'];";
        
        $script .= "var srcareaname0 = ['" .implode("','",$srcareanameAdd). "'];";
        $script .= "var areaname0 = ['" .implode("','",$areanameAdd). "'];";
        $script .= "var authidname0 = ['" .implode("','",$authidnameAdd). "'];";
        $script .= "var issidname0 = ['" .implode("','",$issidnameAdd). "'];";
        #$script .= "alert(refno0);";
        $script .= "xajax_populateIssueAcknowledge(refno0, issdate0, srcarea0, area0, authid0, issid0, srcareaname0, areaname0, authidname0, issidname0);";
        $script .= "</script>";
        $src = $script;
    
        if ($src) $smarty->assign('sIssueItems',$src);
        }
        
        else {
            //echo "null ang result<br>";
             $smarty->assign('sIssueItems',"
                <tr>
                    <td colspan=\"8\">Issue list is currently empty...</td>
                </tr>");
        } 
}
else {
    $smarty->assign('sIssueItems',"
                <tr>
                    <td colspan=\"8\">Issue list is currently empty...</td>
                </tr>");
}

#added by Bryan on Feb 23,2009
#FILTER for issuance acknowledgement
######################
$datecheckHTML = "<input type=\"checkbox\" id=\"chkdate\" name=\"chkdate\" ".($_REQUEST['chkdate'] ? 'checked="checked"' : '') ."/>";
$smarty->assign('sIssDateCheckbox', $datecheckHTML);

$dateHTML = '
<select class="jedInput" id="seldate" name="seldate" onchange="seldateOnChange()">
    <option value="today" '. ($_REQUEST['seldate']=='today' ? 'selected="selected"' : '') .'>Today</option>
    <option value="thisweek" '. ($_REQUEST['seldate']=='thisweek' ? 'selected="selected"' : '') .'>This week</option>
    <option value="thismonth" '. ($_REQUEST['seldate']=='thismonth' ? 'selected="selected"' : '') .'>This month</option>
    <option value="specificdate" '. ($_REQUEST['seldate']=='specificdate' ? 'selected="selected"' : '') .'>Specific date</option>
    <option value="between" '. ($_REQUEST['seldate']=='between' ? 'selected="selected"' : '') .'>Between</option>
</select>
<span name="seldateoptions" segOption="specificdate" '. (($_REQUEST["seldate"]=="specificdate") ? '' : 'style="display:none"') .'>
    <input class="jedInput" name="specificdate" id="specificdate" type="text" size="8" value="'. $_REQUEST['specificdate'] .'"/>
    <img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_specificdate" align="absmiddle" style="cursor:pointer"  />
    <script type="text/javascript">
        Calendar.setup ({
            inputField : "specificdate", ifFormat : "'. $phpfd .'", showsTime : false, button : "tg_specificdate", singleClick : true, step : 1
        });
    </script>
</span>
<span name="seldateoptions" segOption="between" '. (($_REQUEST['seldate']=='between') ? '' : 'style="display:none"') .'>
    <input class="jedInput" name="between1" id="between1" type="text" size="8" value="'. $_REQUEST['between1'] .'"/>
    <img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_between1" align="absmiddle" style="cursor:pointer;"  />
    <script type="text/javascript">
        Calendar.setup ({
            inputField : "between1", ifFormat : "'. $phpfd .'", showsTime : false, button : "tg_between1", singleClick : true, step : 1
        });
    </script>
    to
    <input class="jedInput" name="between2" id="between2" type="text" size="8" value="'. $_REQUEST['between2'] .'"/>
    <img src="'. $root_path .'gui/img/common/default/show-calendar.gif" id="tg_between2" align="absmiddle" style="cursor:pointer"  />
    <script type="text/javascript">
        Calendar.setup ({
            inputField : "between2", ifFormat : "'. $phpfd .'", showsTime : false, button : "tg_between2", singleClick : true, step : 1
        });
    </script>
</span>';

$smarty->assign('sIssDate', $dateHTML);

########################
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
$smarty->assign('sMainBlockIncludeFile','supply_office/supply-new-acknowledge.tpl');
$smarty->display('common/mainframe.tpl');
?>