<script language="javascript" type="text/javascript"> 
function openRpt(scpt, apnd){
        window.open('reports/'+scpt+'.php'+apnd,null,'height=600,width=870,status=yes,toolbar=no,menubar=no,location=no,resizable=yes');
    }
</script>

<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','cashier.php');
$local_user='ck_cashier_user';
require_once($root_path.'include/inc_front_chain_lang.php');

# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj = new Department();

require_once($root_path.'include/care_api_classes/class_pharma_product.php');
$prod_obj = new SegPharmaProduct();

require_once($root_path.'include/care_api_classes/inventory/class_adjustment.php');
$adj_obj = new SegAdjustment();

require_once($root_path.'include/care_api_classes/class_access.php');
require_once($root_path.'include/care_api_classes/class_area.php');
$area_obj = new SegArea();

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

if($_GET['from']=='phs' || $_POST['from']=='phs')
    $breakfile=$root_path."modules/phs/seg-phs-function.php".URL_APPEND."&userck=$userck";
else
    $breakfile=$root_path."modules/supply_office/seg-supply-functions.php".URL_APPEND."&userck=$userck";

$imgpath=$root_path."pharma/img/";
$thisfile='seg-inventory-report.php';

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Inventory::Reports");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Inventory::Reports");

 # Assign Body Onload javascript code
$onLoadJS='';
if ($_POST['selreport']) {
		$append = "?area=".$_POST["list_position_area"]."&date=".$_POST["dt"]."&exp_date=".$_POST["expdt"]."&from_date=".$_POST["fromdt"]."&to_date=".$_POST["todt"]."&itemtype=".$_POST["itemtype"]."&from_percent=".$_POST["from_percent"]."&to_percent=".$_POST["to_percent"]."&serial_no=".$_POST["serial_num"]."&adjustreason=".$_POST["adjustreason"]."&expcheck=".$_POST["expchkbox"]."&month=".$_POST["stock_position_month"]."&year=".$_POST["stock_position_year"]."&point=".$_POST['item_count']."&item=".$_POST['item'];
        if($_POST['selreport'] == 9){
             $db->Execute("CALL sp_populate_consolidated_inventory('".$_POST["fromdt"]."','".$_POST["todt"]."')");
        }
        $result=$db->Execute("SELECT * FROM seg_inventory_reptbl WHERE rep_nr=".$_POST['selreport']);
        $row=$result->FetchRow();
        $report = $row['rep_script'];
		$onLoadJS.="onload=\"openRpt('$report','$append');\"";        
 }
 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code
 ob_start()

?>
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script language="javascript" type="text/javascript">
<!--

var $J = jQuery.noConflict();

    var URL_FORWARD = "<?= URL_APPEND."&clear_ck_sid=$clear_ck_sid" ?>";

    function pSearchClose() {
        cClick();
    }
    
    function selOnChange() {
        var optSelected = $('selreport').options[$('selreport').selectedIndex];
        var spans = document.getElementsByName('selOptions');
        for (var i=0; i<=spans.length; i++) {
            if (optSelected) {
                if (spans[i].getAttribute("segOption") == optSelected.value) {
                    spans[i].style.display = "";
                }
                else
                    spans[i].style.display = "none";
            }
        }
    }

    // updated by janjan 08/18/2015
    function openItemsTray()
    {
            var url = 'seg-inventory-stockcard-tray.php';
            overlib(
                OLiframeContent(url, 660, 397, 'fOrderTray', 0, 'no'),
                WIDTH,600, TEXTPADDING,0, BORDER,0, 
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
                CAPTIONPADDING,2, 
                CAPTION,'Select Item',
                MIDX,0, MIDY,0, 
                STATUS,'Select Item');
            return false
        }

    function validate() {
        var x = $J('#item'), y = $J('#area');
        if (x.val()=='') {
            alert('Please specify an inventory item...');
            x.focus();
            return false;
        }
        else if(y.val()==''){
            alert('Please specify an inventory area..');
            y.focus();
            return false;
        }
        return true;
    }

    // added by janjan 08/18/2015
    function clearItem() {
        $J('#item_name').val('');
        $J('#item').val('');
    }
-->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
# Buffer page output
#include($root_path."include/care_api_classes/class_order.php");
#$order = new SegOrder('pharma');
//dito
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="post" name="inputform" >');
$smarty->assign('sFormEnd','</form>');
 
$options="";
$options2="";
$result=$db->Execute("SELECT * FROM seg_inventory_reptbl");
$options="<option value='0'>-Select Report-</option>";
while ($reptbl=$result->FetchRow()) {
    $checked=strtolower($reptbl["rep_nr"])==strtolower($_POST['selreport']) ? 'selected="selected"' : "";
    $options.="<option value='".$reptbl["rep_nr"]."' $checked>".$reptbl["rep_name"]."</option>";
}
$smarty->assign('sReportSelect',"<select name=\"selreport\" id=\"selreport\" class='segInput' onChange=\"DisplayDept(this.value);\">
$options
</select>");

//$options2="<option value=''>-All areas-</option>";
//$options2=""; 
$obj = new Access();    
$dept_nr = $obj->getDeptNr($_SESSION['sess_temp_userid']);

$per_arr = explode(" ", $_SESSION['sess_permission']);
if (in_array("System_Admin", $per_arr) || in_array("_a_0_all", $per_arr)) $dept_nr = "''";
#added by VAN 01-28-2011
//if (!empty($dept_nr)) {
//    $qry = "SELECT fn_get_children_dept(".$dept_nr.") as dps";
//    $rs = $db->Execute($qry);
//    if ($rs) {
//        $row =  $rs->FetchRow();
//        $depscomma = $row["dps"];
//        if (empty($depscomma)){
//            $result = $dept_obj->getAreasInDept($dept_nr);
//        }
//        else {
//            //modified by bryan 112609
//            $depscomma = $depscomma.",".$dept_nr;
//            $result = $dept_obj->getAreasInADept($depscomma);
//        }
//	}
//} else {
//    $result =  $dept_obj->getAreasInDept($dept_nr);
//}

$result = $area_obj->getInventoryAreas();

//if($result = $dept_obj->getAllAreas()){
//$options2="<option value=''>          -- All areas --         </option>";    
if ($result) {
    foreach($result as $row){
        $checked=(strtolower($row['area_code'])==strtolower($_GET['list_position_area'])) || (strtolower($row['area_code']) == strtolower($_POST['list_position_area'])) ? 'selected="selected"' : "";
        $options2.="<option value=\"".$row['area_code']."\" $checked>".$row['area_name']." </option>\n";
    }
}

$smarty->assign('sReportSelectArea',"<select name=\"list_position_area\" id=\"list_position_area\" class='segInput'>
$options2
</select>");
$smarty->assign('sSCItemInput',
    '<input class="segInput" type="text" id="item_name" name="item_name" readonly="readonly" value="'.$_POST['item_name'].'" size="40">' .
    '<input type="hidden" id="item" name="item" value="'.$_POST['item'].'">');
$smarty->assign('sSCItemIcon',' <img id="select-item" ' . createComIcon($root_path,'btn_encounter_smalls.gif','0') . ' align="absmiddle" style="cursor:pointer" onclick="openItemsTray();"/>'.
                                '<input type="button" id="clear-item" class="segButton" onclick="clearItem();" value="&times;"/>');

$optionstypes = $prod_obj->getProdClassOption(); 
$smarty->assign('sReportSelectItemType',"<select name=\"itemtype\" id=\"itemtype\" class='segInput'>
$optionstypes
</select>");

$optionsadjust = "<option value=''>-All reasons-</option>";
if($result = $adj_obj->getAllAdjustReasons()){
    while($row=$result->FetchRow()){
        $optionsadjust.="<option value=\"".$row['adj_reason_id']."\">".$row['adj_reason_name']." </option>\n";
    }
}
$smarty->assign('sAdjustSelectItemType',"<select name=\"adjustreason\" id=\"adjustreason\" class='segInput'> $optionsadjust </select>");

$smarty->assign('sItemsBelow', '<input type="text" class="segInput" id="item_count" name="item_count">');
$smarty->assign('sExpiriesCheckBox',"<input class=\"segInput\" type='checkbox' name='expchkbox' value='checked'>");
$smarty->assign('sGenerateButton',"<input type='button' value='View report' class='segButton' onclick='return prufformreport(this)'/>");

$smarty->assign('sDateInput','<input class="segInput" type="text" name="dt" id="date" value="">');
$smarty->assign('sDateIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="date_trigger" align="absmiddle" style="cursor:pointer">[mm/dd/YYYY]');

$smarty->assign('sExpDateInput','<input class="segInput" type="text" name="expdt" id="exp_date" value="">');
$smarty->assign('sExpDateIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="exp_date_trigger" align="absmiddle" style="cursor:pointer">[mm/dd/YYYY]');
$smarty->assign('sExpDateInputs','<input class="segInput" type="text" name="expdt" id="exp_date" value="">');

$smarty->assign('sFromDateInput','<input class="segInput" type="text" name="fromdt" id="from_date" value="">');
$smarty->assign('sFromDateIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="from_date_trigger" align="absmiddle" style="cursor:pointer">[mm/dd/YYYY]');
$smarty->assign('sToDateInput','<input class="segInput" type="text" name="todt" id="to_date" value="">');
$smarty->assign('sToDateIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="to_date_trigger" align="absmiddle" style="cursor:pointer">[mm/dd/YYYY]');

$smarty->assign('sFromPercent','<input class="segInput" type="text" name="from_percent" id="from_percent" size="1" value="0">');

$smarty->assign('sToPercent','<input class="segInput" type="text" name="to_percent" id="to_percent" size="1" value="100">');
$smarty->assign('sSerialNumber','<input class="segInput" type="text" name="serial_num" id="serial_num" size="8" value="">');

#Added by Cherry 01-21-11
$month = array("January", "February", "March", "April" , "May", "June",
								 "July", "August", "September",
								 "October", "November", "December"
								 );

	$i=0;
    $nmonth = isset($_POST["stock_position_month"]) ? $_POST["stock_position_month"] : 0;
	$month_options .= "\t<option value='0' ".(($nmonth == 0) ? ' selected' : '').">Select Month</option>\n";    
	while($i<count($month))
	{
		$month_options.= "<option value='".($i+1)."'".( ($nmonth == ($i+1)) ? ' selected' : '' ).">".$month[$i]."</option>";
		$i++;
	}

$smarty->assign('sMonth',
'<select class="segInput" name="stock_position_month" id="stock_position_month" paramName="month" segOption="stock_position">'.$month_options.'</select>');

	$yearSelect = date("Y");
	$yearbegin = date("Y");
	$currentYear = date("Y");
	$yearCount = 10;
	$year_options .= "\t<option value='0' $selected>Select Year</option>\n";
	for($i = $yearbegin-$yearCount; $i <= $yearbegin; $i++)
	{
		$selected = $yearSelect == $i ? 'selected="selected"' : '';
		$year_options .= "\t<option value='$i' $selected>$i</option>\n";
	}

$smarty->assign('sYear',
'<select class="segInput" name="stock_position_year" id="stock_position_year" paramName="year" segOption="stock_position">'.$year_options.'</select>');

#End Cherry

$jsCalScript = "<script type=\"text/javascript\">
    
Calendar.setup (
{
    inputField : \"from_date\", 
    ifFormat : \"%m/%d/%Y\", 
    showsTime : false, 
    button : \"from_date_trigger\", 
    singleClick : true, 
    step : 1
}
);
Calendar.setup (
{
    inputField : \"to_date\", 
    ifFormat : \"%m/%d/%Y\", 
    showsTime : false, 
    button : \"to_date_trigger\", 
    singleClick : true, 
    step : 1
}
);
Calendar.setup ({
    inputField : \"date\", 
    ifFormat : \"%m/%d/%Y\", 
    showsTime : false, 
    button : \"date_trigger\", 
    singleClick : true, 
    step : 1
});
Calendar.setup (
{
    inputField : \"exp_date\", 
    ifFormat : \"%m/%d/%Y\", 
    showsTime : false, 
    button : \"exp_date_trigger\", 
    singleClick : true, 
    step : 1
}
);
function prufformreport(d){
    var x = document.getElementById('selreport');
    //alert(x.value);
      if (x.value==0) {
            alert('Select the kind of report you want to generate.');
            x.focus();
            return false;
     /*}else if (x.value==5) {
            alert('Enter the starting date.');
            d.from_date.focus();
            return false;
     }else if (x.value==5 ) {
            alert('Enter the end date.');
            d.to_date.focus();
            return false;*/
     }else{
        //openReport();
        document.inputform.submit();
        //window.open('seg-invreport-list_expiry.php',null,'height=600,width=800,status=yes,toolbar=no,menubar=no,location=no,resizable=yes');
        return true;
     }    
}

function openReport() {
    /*var rep = $('selreport').options[$('selreport').selectedIndex].value
    var url = 'seg-invreport-'+rep+'.php?'
    var query = new Array()
    var params = document.getElementsByName('param')
    for (var i=0; i<params.length; i++) {
        if (params[i].getAttribute('segOption') == rep) {
            var mit;
            if (params[i].type=='checkbox') mit=params[i].checked;
            else if (params[i].type=='radio') mit=params[i].checked;
            else mit=params[i].value;
            if (mit) query.push(params[i].getAttribute('paramName')+'='+params[i].value)
        }
    }
    //alert(url+query.join('&'))
    window.open(url+query.join('&'),rep,'width=800,height=600,menubar=no,resizable=yes,scrollbars=no');*/
}
    
    
</script>
";    
    $smarty->assign('jsCalendarSetup', $jsCalScript);

ob_start();
?>

<script type="text/javascript">
    
    function DisplayDept(rep_nr){
        document.getElementById('area_row').style.display='none';    
        document.getElementById('fromdate_row').style.display='none';
        document.getElementById('todate_row').style.display='none';
        document.getElementById('date_row').style.display='none';
        document.getElementById('expdate_row').style.display='none';
        document.getElementById('percent_row').style.display='none';
        document.getElementById('itemtype_row').style.display='none'; 
        document.getElementById('serialno_row').style.display='none';
        document.getElementById('adjusttype_row').style.display='none';
        document.getElementById('expchk_row').style.display='none'; 
        document.getElementById('expdate_row').style.display='none';
        $('item_row').style.display='none';
				document.getElementById('month_row').style.display='none';
				document.getElementById('year_row').style.display='none';
				if ((rep_nr==1)||(rep_nr==2)||(rep_nr==3)||(rep_nr==5)||(rep_nr==6)||(rep_nr==7)||(rep_nr==10)||(rep_nr==11)){
            document.getElementById('area_row').style.display='';
        }
				if((rep_nr==1)||(rep_nr==3)||(rep_nr==6)||(rep_nr==7)||(rep_nr==10)||(rep_nr==11)){
            document.getElementById('date_row').style.display='';
        }
				if((rep_nr==4)){
						document.getElementById('area_row').style.display='';
						document.getElementById('month_row').style.display='';
						document.getElementById('year_row').style.display='';
				}
        if((rep_nr==2)){
            document.getElementById('fromdate_row').style.display='';
            document.getElementById('todate_row').style.display='';
            document.getElementById('expchk_row').style.display='';
        }
        if((rep_nr==5)){
            document.getElementById('fromdate_row').style.display='';
            document.getElementById('todate_row').style.display='';
            document.getElementById('percent_row').style.display='';
        }
		if(rep_nr==7){
            document.getElementById('adjusttype_row').style.display='';
		}
        if((rep_nr==8)){
            document.getElementById('fromdate_row').style.display='';
            document.getElementById('todate_row').style.display='';
            document.getElementById('serialno_row').style.display='';
        }
				//modifieed 05-18-2011
				//if((rep_nr==9)){
				if((rep_nr==9)||(rep_nr==3)){
            document.getElementById('fromdate_row').style.display='';
            document.getElementById('todate_row').style.display='';
        }
        if(rep_nr==12){ //re-ordering point
            $('item_row').style.display='';
            $('date_row').style.display='';
            $('area_row').style.display='';  
        }
        if(rep_nr==13){//Inventory list of all meds
            $('date_row').style.display='';
            document.getElementById('expdate_row').style.display='';
        }
    }
    
    
</script>

<br>

<form action="<?= $thisfile.URL_APPEND."&target=list&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform" onSubmit="return validate()">
<div style="width:500px">
    <table width="100%" border="0" style="font-size:12px; margin-top:5px" cellspacing="2" cellpadding="2">    
        <tbody>
        </tbody>
    </table>
</div>

<?php
if($_POST['selreport']){
    echo "<script>DisplayDept('".$_POST['selreport']."')</script>";
}
# Workaround to force display of results form
$bShowThisForm = TRUE;

# If smarty object is not available create one
if(!isset($smarty)){
/**
 * LOAD Smarty
 * param 2 = FALSE = dont initialize
 * param 3 = FALSE = show no copyright
 * param 4 = FALSE = load no javascript code
 */
    include_once($root_path.'gui/smarty_template/smarty_care.class.php');
    $smarty = new smarty_care('common',FALSE,FALSE,FALSE);
    
    # Set a flag to display this page as standalone
    $bShowThisForm=TRUE;
}

?>

<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">  
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">  
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">  
<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">

<!--added by bryan-->
<input type="hidden" id="from" name="from" value="<?= $_REQUEST['from'] ?>" />

<input type="hidden" id="delete" name="delete" value="" />
<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump">



</form>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<input class="segInput" type="button" align="center" value="Cancel payment">');

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);
 $smarty->assign('sMainBlockIncludeFile','supply_office/rep_form.tpl');

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>