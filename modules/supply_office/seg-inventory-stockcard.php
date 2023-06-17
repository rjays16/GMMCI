<script language="javascript" type="text/javascript"> 
function openRpt(scpt, apnd){
        window.open('pdf_'+scpt+'.php'+apnd,null,'height=600,width=870,status=yes,toolbar=no,menubar=no,location=no,resizable=yes');
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
define('NO_CHAIN',1);
require_once($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_access.php');   
$dept_obj = new Department();               

require_once($root_path.'include/care_api_classes/class_pharma_product.php');
$prod_obj = new SegPharmaProduct();

require_once($root_path.'include/care_api_classes/inventory/class_adjustment.php');
$adj_obj = new SegAdjustment();

require_once($root_path.'include/care_api_classes/class_area.php');
$area_obj = new SegArea();

$breakfile=$root_path."modules/supply_office/seg-supply-functions.php".URL_APPEND."&userck=$userck";
$thisfile='seg-inventory-stockcard.php';

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Inventory::Stock Card");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Inventory::Stock Card");

 # Assign Body Onload javascript code
$onLoadJS='';
if ($_POST['item']) {
	$params = array( 'area', 'item', 'from_date', 'to_date' );
	$append = array();
	foreach ($params as $param) {
		$append[] = $param . '=' . $_POST[$param];
	}
        $report = "stockcard";
	$append = '?' . implode('&', $append);
	$onLoadJS.="onload=\"openRpt('$report','$append')\"";

	$smarty->assign('data', $_POST);
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
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript">

var $J = jQuery.noConflict();

var URL_FORWARD = "<?= URL_APPEND."&clear_ck_sid=$clear_ck_sid" ?>";

function openRpt(scpt, apnd){
	window.open('pdf_'+scpt+'.php'+apnd,null,'height=600,width=870,status=yes,toolbar=no,menubar=no,location=no,resizable=yes');
}

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
</script>

<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="post" name="inputform" onsubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');
 
//$options="";
$options2="";
$obj = new Access();    
$dept_nr = $obj->getDeptNr($_SESSION['sess_temp_userid']);
$per_arr = explode(" ", $_SESSION['sess_permission']);
if (in_array("System_Admin", $per_arr) || in_array("_a_0_all", $per_arr)) $dept_nr = "''";
//if (!empty($dept_nr)){
//    $qry = "SELECT fn_get_children_dept(".$dept_nr.") as dps";
//    $rs = $db->Execute($qry);
//    if($rs){
//        $row =  $rs->FetchRow();
//        $depscomma = $row["dps"];
//        if (empty($depscomma)){
//            $result = $dept_obj->getAreasInDept($dept_nr);
//        }
//        else{
//            //modified by bryan 112609
//            $depscomma = $depscomma.",".$dept_nr;
//            $result = $dept_obj->getAreasInADept($depscomma);
//        }
//    }
//}else{
//	$result = $dept_obj->getAreasInDept($dept_nr);
//}
$result = $area_obj->getInventoryAreas();
if ($result) {
    $rcount = count($result);
    foreach($result as $row) {
		$checked=(strtolower($row['area_code'])==strtolower($_GET['area'])) || (strtolower($row['area_code']) == strtolower($_POST['area'])) || ($rcount == 1)? 'selected="selected"' : "";
        $options2.="<option value=\"".$row['area_code']."\" $checked>".$row['area_name']." </option>\n";
    }
}
$smarty->assign('sSCSelectArea',"<select name=\"area\" id=\"area\" class='segInput'>
$options2
</select>");
$smarty->assign('sSCItemInput',
	'<input class="segInput" type="text" id="item_name" name="item_name" readonly="readonly" value="'.$_POST['item_name'].'" size="40">' .
	'<input type="hidden" id="item" name="item" value="'.$_POST['item'].'">');
$smarty->assign('sSCItemIcon',' <img id="select-item" ' . createComIcon($root_path,'btn_encounter_smalls.gif','0') . ' align="absmiddle" style="cursor:pointer" onclick="openItemsTray();"/>');

ob_start();
?>
<?php

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

<input type="hidden" id="delete" name="delete" value="" />
<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<input class="segInput" type="button" align="center" value="Cancel payment">');

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);
 $smarty->assign('sMainBlockIncludeFile','supply_office/inventory-stockcard.tpl');

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>