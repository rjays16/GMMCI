<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/billing/ajax/billing.common.php');

$lang_tables[]='search.php';
define('LANG_FILE','finance.php');

if ($_GET['area'] == 'DIALYSIS')
	$local_user = 'ck_dialysis_user';
else
	$local_user = 'aufnahme_user';

require_once($root_path.'include/inc_front_chain_lang.php');

if (isset($_GET["from"]))
	$from = $_GET["from"];
else
	$from = "";
if (($from == "") || (!isset($from)))
		$breakfile=$root_path.'modules/billing/bill-main-menu.php'.URL_APPEND."&userck=$userck";
else
		$breakfile = $from.".php".URL_APPEND;
$thisfile=basename(__FILE__);

# Start Smarty templating here
/**
* LOAD Smarty
*/
# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('system_admin');

#added by VAN 12-19-08
require_once($root_path.'include/care_api_classes/class_person.php');


# Title in toolbar
if (!isset($_GET['area']))
	$smarty->assign('sToolbarTitle',"$LDBillingMain :: Process Billing");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('billing-main.php')");

 # href for close button
 #edited by VAN 12-19-08
 if (($_GET['area']=='ER') || ($_GET['area'] == 'DIALYSIS'))
		 $smarty->assign('breakfile','javascript:window.parent.cClick();');
 else
		 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDBillingMain :: $LDListAll");

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

<!-- include Billing javascript -->
<script type="text/javascript" src="<?=$root_path?>js/gen_routines.js"></script>
<!--<script type="text/javascript" src="js/billing-main.js"></script>-->
<script type="text/javascript" src="<?=$root_path?>js/datefuncs.js"></script>
<!--<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.min.js"></script>-->

<link rel="stylesheet" href="<?=$root_path?>js/jquery/css/jquery-ui.css" />
<script type='text/javascript' src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>

<script type="text/javascript" src="js/billing-main.gui.js?t=<?=time();?>"></script>
<script type="text/javascript">
var $j = jQuery.noConflict();
jQuery(function($) {
	$j(document).ready(function() {
		$j("tbody").find(".toggle").hide();

		$j('thead.togglehdr').each(function(idx, obj) {
			var obj = $j(obj);
			obj.find('th.toggleth').click( function() {
				obj.parent().children('tbody.toggle').toggle();
				obj.find(".arrow").toggleClass("up");
			});
		});
	});
});
</script>
<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
?>
<script>
		YAHOO.namespace("pbill.container");
		YAHOO.namespace("dbill.container");
		YAHOO.namespace("categprompt.container");
		YAHOO.util.Event.onDOMReady(initDialogBox);
		YAHOO.util.Event.onDOMReady(initCategoryPrompt);
		YAHOO.util.Event.onDOMReady(initAccomPrompt);
		YAHOO.util.Event.onDOMReady(initOpAccomChrgPrompt);
		YAHOO.util.Event.addListener(window, "load", init);

		function myClick() {
				js_Recalculate();
				cClick();
		}

		//added by VAN 12-19-08
		function preSet() {
			//  alert('wait');
				 jsBilling();
		}
</script>


<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

//$smarty->assign('sOnUnloadJs',"onUnload=\"remLink()\"");

#added by VAN 12-19-08
require_once $root_path . 'include/care_api_classes/class_acl.php';
$smarty->assign('sOnLoadJs',"onLoad=\"preSet()\"");

$submitted = isset($_POST["submitted"]);
global $allowedarea;
    $allowedarea = array('_a_3_billDeleteBtn');
     if (validarea($_SESSION['sess_permission'],1)) {
            $canDelete = $smarty->assign('sBillingButton2', '<img id="btnDelete" style="cursor:pointer" src="'.$root_path.'/images/btn_delete.gif" border=0 >');
         }
         
       
if ($_GET['nr'] == '') {
        #$smarty->assign('row_width', '"38%"');
        $smarty->assign('sBillingButton0', '<td width="8" valign="bottom" align="center"><img id="btnPrevCoverage" style="cursor:pointer" src="'.$root_path.'/images/btn_prev.gif" border=0 ></td>');
        #added by pol 10/05/2013
        $smarty->assign('sPreviousPackage', '<td width="8" valign="bottom" align="center"><img id="btnPrevPack" style="cursor:pointer" src="'.$root_path.'/images/btn_package.gif" border=0 ></td>');
        #end by pol 10/05/2013
        $smarty->assign('sBillingButton1', '<img id="btnSave" style="cursor:pointer" src="'.$root_path.'/images/btn_save.gif" border=0 >');
        $smarty->assign('sBillingButton2', '<img id="btnRecalc" style="cursor:pointer" src="'.$root_path.'/images/btn_recalc.gif" border=0 >');
        $smarty->assign('sBillingButton3', '<img id="btnPrint" style="cursor:pointer" src="'.$root_path.'/images/btn_printpdf.gif" border=0 >');
//    $smarty->assign('sBillingButton4', '<img id="btnDraft" style="cursor:pointer" src="'.$root_path.'/images/btn_draft.gif" border=0 >');

        #added by VAN 08-13-08
        $smarty->assign('sBillingButton5', '<td width="8" valign="bottom" align="center"><img id="btnInsurance" style="cursor:pointer" src="'.$root_path.'/images/btn_insurance.gif" border=0 ></td>');
        $smarty->assign('sBillingButton6', '<td width="8" valign="bottom" align="center"><img id="btnDiagnosis" style="cursor:pointer" src="'.$root_path.'/images/btn_diagnosis.gif" border=0 ></td>');

        // ... added by LST ... 06.08.2010 ----------
        $smarty->assign('sMGHCheckBox', '<td id="chkboxrow" width="18%" align="left" bgcolor="#FF0000" valign="middle" style="border:solid 1px;"><input type="checkbox" name="isFinalBill" id="isFinalBill" style="vertical-align:middle; cursor:pointer" onclick="toggleFinalBill();"><span style="color:white"><b>Check if Final Bill.<b></span></td>');
} 
else if (validarea($_SESSION['sess_permission'],1)) {
        $canDelete = $smarty->assign('sBillingButton2', '<img id="btnDelete" style="cursor:pointer" src="'.$root_path.'/images/btn_delete.gif" border=0 >'); 
                     $smarty->assign('sBillingButton3', '<img id="btnPrint" style="cursor:pointer" src="'.$root_path.'/images/btn_printpdf.gif" border=0 >');
                     $smarty->assign('sBillingButton1', '<img id="btnNew" style="cursor:pointer" src="'.$root_path.'/images/btn_newbill.gif" border=0 >');
    }
else {
        #$smarty->assign('row_width', '"42%"');
        $smarty->assign('sBillingButton0', '');
        $smarty->assign('', '<img id="btnNew" style="cursor:pointer" src="'.$root_path.'/images/btn_newbill.gif" border=0 >');
        $smarty->assign('', '<img id="btnDelete" style="cursor:pointer" src="'.$root_path.'/images/btn_delete.gif" border=0 >');
        $smarty->assign('sBillingButton3', '<img id="btnPrint" style="cursor:pointer" src="'.$root_path.'/images/btn_printpdf.gif" border=0 >');
//    $smarty->assign('sBillingButton4', '<img id="btnDraft" style="cursor:pointer" src="'.$root_path.'/images/btn_draft.gif" border=0 >');

        #added by VAN 08-13-08
        $smarty->assign('sBillingButton5', '');
}

if (!isset($_GET['nr']) || ($_GET['nr'] == '')) {
		if ($_GET['enc_nr']) {
			$encounter_nr = $_GET['enc_nr'];

			include_once($root_path.'include/care_api_classes/class_encounter.php');

			$encobj = new Encounter($encounter_nr);
			$encobj->loadEncounterData($encounter_nr);
			$pid = $encobj->PID($encounter_nr);
			
		}
		#added by VAN 12-19-08
		else {
			if ($_POST["pid"])
					$pid = $_POST["pid"];
			else
					$pid = $_GET["pid"];
		}

		if ($pid) {
			$person_obj=new Person($pid);
			$person_info = $person_obj->getAllInfoArray($pid);
			#echo "name = ". $person_obj->sql;
			extract($person_info);

			if ($pid)
					$name_patient =  $name_last.", ".$name_first." ".$name_middle;
			else
					$name_patient =  "";
			#echo "name = ".$street_name;
			if ($street_name){
				if ($brgy_name!="NOT PROVIDED")
						$street_name = $street_name.", ";
				else
						$street_name = $street_name.", ";
			}#else
							#$street_name = "";

			if ((!($brgy_name)) || ($brgy_name=="NOT PROVIDED"))
					$brgy_name = "";
			else
					$brgy_name  = $brgy_name.", ";

			if ((!($mun_name)) || ($mun_name=="NOT PROVIDED"))
					$mun_name = "";
			else{
					if ($brgy_name)
							$mun_name = $mun_name;
					#else
							#$mun_name = $mun_name;
			}

			if ((!($prov_name)) || ($prov_name=="NOT PROVIDED"))
					$prov_name = "";
			#else
			#    $prov_name = $prov_name;

			if(stristr(trim($mun_name), 'city') === FALSE){
					if ((!empty($mun_name))&&(!empty($prov_name))){
							if ($prov_name!="NOT PROVIDED")
									$prov_name = ", ".trim($prov_name);
							else
									$prov_name = "";
					}else{
							#$province = trim($prov_name);
							$prov_name = "";
					}
			}else
					$prov_name = " ";

			$address = $street_name.$brgy_name.$mun_name.$prov_name;

		//	if ($_POST['orderaddress'])
		//			$address = $_POST['orderaddress'];

			if ($_POST["encounter_nr"])
					$encounter_nr=$_POST["encounter_nr"];

			if($_POST["admission_date"])
					$admission_date = $_POST["admission_date"];
			else{
					if (($encounter_type==3)||($encounter_type==4))
						$admission_date = date("M d, Y h:i A", strtotime($admission_dt));
					else
						$admission_date = date("M d, Y h:i A", strtotime($encounter_date));

					$enc_date =  date("M d, Y h:i A", strtotime($encounter_date));
		//			if(($encounter_type==1)||($encounter_type==2))
		//					$admission_date = date("F d, Y h:i A", strtotime($encounter_date));
		//			elseif (($encounter_type==3)||($encounter_type==4))
		//					$admission_date = date("F d, Y h:i A", strtotime($admission_dt));
			}
		}
#--------------------
if (isset($encounter_nr)) {
	if ($encounter_nr != '')
				$smarty->assign('sOnLoadJs',"onLoad=\"clickHandler('$encounter_nr', '".strftime("%Y-%m-%d %H:%M:%S", strtotime("now"))."')\"");
	}
}

$smarty->assign('sProgBar','<img src="'.$root_path.'/images/ajax_bar.gif" border=0>');
#Left Column
//Patient ID  //
$smarty->assign('sMembershipCategory','<a title="Edit" href="#">'.($_GET['nr'] == '' ? '<img id="memcateg" class="segSimulatedLink" src="'.$root_path.'images/cashier_edit.gif" border="0" align="absmiddle" >' : '').'</a>');
#edited by VAN 12-19-08
$smarty->assign('sPid','<input class="segInput" id="pid" name="pid" type="text" size="15" value="'.$pid.'" style="font:bold 12px Arial; float;left;" readOnly >');





$smarty->assign('sConfineType', '<select'.($_GET['nr'] == '' ? "" : " disabled").' id="confineTypeOption" style="font:bold 12px Arial">
																		<option value="0">- Select Case Type -</option>
																</select>');
//$smarty->assign('sConfineType', '<select disabled id="confineTypeOption" style="font:bold 12px Arial">
//                                    <option value="0">- Select Case Type -</option>
//                                </select>');
$smarty->assign('sCaseType', '<select'.($_GET['nr'] == '' ? "" : " disabled").' id="caseTypeOption" style="font:bold 12px Arial">
																		<option value="0">- Select Confinement Type -</option>
																</select>');
#edited by VAN 12-19-08
//Patient name
$smarty->assign('sPatientName','<input class="segInput" id="pname" name="pname" type="text" size="28" value="'.mb_strtoupper($name_patient).'" style="font:bold 12px Arial; float;left;" readOnly >');
//Patient Address
$smarty->assign('sPatientAddress','<textarea class="segInput" id="paddress" name="paddress" cols="29" rows="2" style="font:bold 12px Arial" readOnly>'.mb_strtoupper($address).'</textarea>');
#--------------
//Select Patient
if (isset($_GET['area']))
	$smarty->assign('sSelectPatient','');
else
	$smarty->assign('sSelectPatient','<input class="segInput"'.($_GET['nr'] == '' ? "" : " disabled").' id="select-enc" type="image" src="../../images/btn_encounter_small.gif" border="0" style=""
				 onclick="overlib(
					OLiframeContent(\'billing-select-enc.php\', 800, 410, \'fSelEnc\', 0, \'auto\'),
					WIDTH,800, TEXTPADDING,0, BORDER,0,
									STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
									CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
					CAPTIONPADDING,4,
									CAPTION,\'Select registered person\',
					MIDX,0, MIDY,0,
					STATUS,\'Select registered person\'); return false;"
				 onmouseout="nd();" />');
//clear patient record  //onclick="clearEncounter()"
//$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" '.(($_POST['pid'])?'':' disabled="disabled"').' />');

#Right Column
//Patient Admission Encounter
#edited by VAN 12-19-08

$smarty->assign('sPatientEnc','<input class="segInput" id="encounter_nr" name="encounter_nr" type="text" size="16" value="'.$encounter_nr.'" style="font:bold 12px Arial; float;left;" readOnly >');
//added by pol 07-23-13
//updated by jane 10/17/2013 - added color:#ff0000 (red) value to style attribute
$smarty->assign('sPhic','<input class="segInput" id="phic" name="phic" type="text" size="15" value="'.$phic.'" style="color: #ff0000;font:bold 12px Arial; float;left;" readOnly >');
//Date$
// $curDate = date("m/d/Y");
$curTme  = strftime("%Y-%m-%d %H:%M:%S");
$curDate = strftime("%b %d, %Y %I:%M%p", strtotime($curTme));

//$smarty->assign('sDate','<input class="segInput" name="billing_dte" type="text" size="17" value="'.$curDate.'" style="font:bold 12px Arial" readOnly>');
$smarty->assign('sDate', '<span id="show_billdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px; vertical-align:middle">'.$curDate.'</span><input class="jedInput" name="billdate" id="billdate" type="hidden" value="'.($submitted ? strftime("%Y-%m-%d %H:%M:%S", strtotime($_POST['billdate'])) : $curTme).'" style="font:bold 12px Arial">');
$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="billdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
$jsCalScript = "<script type=\"text/javascript\">
		Calendar.setup ({
				displayArea : \"show_billdate\",
				inputField : \"billdate\",
				ifFormat : \"%Y-%m-%d %H:%M:%S\",
				daFormat : \"%b %d, %Y %I:%M%p\",
				showsTime : true,
				button : \"billdate_trigger\",
				singleClick : true,
				step : 1
		});
</script>";
$smarty->assign('jsCalendarSetup', $jsCalScript);

//Added by Jarel 05/16/2013
$smarty->assign('sDDate', '<span id="death_date" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px; vertical-align:middle">'.$curDate.'</span><input class="jedInput" name="deathdate" id="deathdate" type="hidden" value="'.($submitted ? strftime("%Y-%m-%d %H:%M:%S", strtotime($_POST['deathdate'])) : $curTme).'" style="font:bold 12px Arial" onchange="changeBilldate();">');
$smarty->assign('sDCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="deathdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
$jsDCalScript = "<script type=\"text/javascript\">
		Calendar.setup ({
				displayArea : \"death_date\",
				inputField : \"deathdate\",
				ifFormat : \"%Y-%m-%d %H:%M:%S\",
				daFormat : \"%b %d, %Y %I:%M%p\",
				showsTime : true,
				button : \"deathdate_trigger\",
				singleClick : true,
				step : 1
		});
</script>";
$smarty->assign('jsDCalendarSetup', $jsDCalScript);

//Encounter date (not admission date).
$smarty->assign('sAdmissionDate','<input class="segInput" id="admission_date" name="admission_date" type="text" size="19"  value="'.$enc_date.'" style="font:bold 12px Arial" readOnly>');

// Last bill date.
$smarty->assign('sLastBillDate','<input class="segInput" id="lastbill_date" name="lastbill_date" type="text" size="19"  value="" style="font:bold 12px Arial" readOnly>');

//Admission date.
$smarty->assign('sAdmitDate','<input class="segInput" id="date_admitted" name="date_admitted" type="text" size="19"  value="'.$admission_date.'" style="font:bold 12px Arial" readOnly>');

//Show Btn Details
#$smarty->assign('btnShowDetails','<input id="btnShowDetails" name="btnShowDetails" type="button" value="Show Billing"/>');
#$smarty->assign('sAddMiscOps', '<span style="padding:12px"><button class="jedButton" style="cursor:pointer"'.($_GET['nr'] == '' ? "" : " disabled").' id="btnaddmisc_ops">Misc. Procedures</button></span>');

$smarty->assign('sAddAccommodation', '<span style="padding:12px"><button class="jedButton" style="cursor:pointer"'.($_GET['nr'] == '' ? "" : " disabled").' id="btnaccommodation">Additional Accommodation</button></span>');
//$smarty->assign('sAddAccommodationBeta', '<span style="padding:12px"><button class="jedButton" style="cursor:pointer"'.($_GET['nr'] == '' ? "" : " disabled").' id="btnaccommodationBeta" onclick="openAccomodationBeta();">Additional Accommodation Beta</button></span>');

$smarty->assign('sAddOPAccommodation', '<span style="padding:12px"><button class="jedButton" style="cursor:pointer"'.($_GET['nr'] == '' ? "" : " disabled").' id="btnOPaccommodation">O.R. Use</button></span>');

$smarty->assign('sAddMedsandSupplies','<span style="padding:12px"><button class="jedButton" style="cursor:pointer"'.($_GET['nr'] == '' ? "" : " disabled").' id="btnmedsandsupplies">More Meds and Supplies</button></span>');

$smarty->assign('sAddMiscOps','<button class="jedButton" style="cursor:pointer"'.($_GET['nr'] == '' ? "" : " disabled").' name="btnaddmisc_ops" id="btnaddmisc_ops" onclick="js_AddMiscOps()">Misc. Procedures</button></a>');

$smarty->assign('sAddDoctorsButton', '<span style="padding:12px"><button class="jedButton" style="cursor:pointer"'.($_GET['nr'] == '' ? "" : " disabled").' id="btnadddoctors">Add Doctors</button></span>');

$smarty->assign('sAddMiscService','<button class="jedButton" style="cursor:pointer"'.($_GET['nr'] == '' ? "" : " disabled").' name="btnaddmisc_srvc" id="btnaddmisc_srvc" onclick="js_AddMiscService()">Misc. Services</button></a>');

$smarty->assign('sAddMiscChrg','<button class="jedButton" style="cursor:pointer"'.($_GET['nr'] == '' ? "" : " disabled").' name="btnaddmisc_chrg" id="btnaddmisc_chrg" onclick="js_AddMiscChrg()">Misc. Charges</button></a>');

$smarty->assign('sDiscountDetails','<span style="padding:12px"><button class="jedButton" style="cursor:pointer"'.($_GET['nr'] == '' ? "" : " disabled").' id="btnadd_discount"onmouseout="nd();" onclick="js_DiscountDetails()">Discount Details</button></span>');

//$smarty->assign('sDiscountDetails', '<a href="javascript:void(0);"
//			 onclick="return overlib(
//				OLiframeContent(\'billing-discounts.php\', 725, 380, \'fDiscTray\', 1, \'auto\'),
//				WIDTH, 380, TEXTPADDING,0, BORDER,0,
//								STICKY, SCROLL, CLOSECLICK, MODAL,
//								CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
//				CAPTIONPADDING,4,
//								CAPTION,\'Applicable Discounts\',
//				MIDX,0, MIDY,0,
//				STATUS,\'Applicable Discounts\');"
//			 onmouseout="nd();">
//							 <button class="jedButton" style="cursor:pointer"'.($_GET['nr'] == '' ? "" : " disabled").' name="btnadd_discount" id="btnadd_discount">Discount Details</button></a>');

// Select operation procedures charged by Doctors ...
$smarty->assign('sSelectOpsForPF', '<a title="Procedures done by Doctor" href="#"><img id="ops4pf_selected"
				onclick="overlib(OLiframeContent(\'bill-ops-chrgdaccom.php'.URL_APPEND.'&enc_nr=\'+document.getElementById(\'encounter_nr\').value+\'&target=dr&dr=\'+document.getElementById(\'dr_nr\').value, 725, 380, \'fSelOps4Dr\', 1, \'auto\'),
				WIDTH, 380, TEXTPADDING,0, BORDER,0,
								STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
								CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
				CAPTIONPADDING,4,
								CAPTION,\'Select Procedures done by Doctor\',
				MIDX,0, MIDY,0,
				STATUS,\'Select Procedures done by Doctor\'); return false;"
			 onmouseout="nd();"
			 class="segSimulatedLink" src="'.$root_path.'gui/img/common/default/task_tree.gif"
			 border="0" align="absmiddle" ></a>');

// Select operation procedures charged with accommodation ...
$smarty->assign('sSelectOps', '<a title="Procedures" href="#"><img id="ops_selected"
				onclick="overlib(OLiframeContent(\'bill-ops-chrgdaccom.php'.URL_APPEND.'&enc_nr=\'+document.getElementById(\'encounter_nr\').value+\'&target=ac\', 725, 380, \'fSelOps4Acc\', 1, \'auto\'),
				WIDTH, 380, TEXTPADDING,0, BORDER,0,
								STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
								CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
				CAPTIONPADDING,4,
								CAPTION,\'Select Procedures w/ Accommodation\',
				MIDX,0, MIDY,0,
				STATUS,\'Select Procedures w/ Accommodation\'); return false;"
			 onmouseout="nd();"
			 class="segSimulatedLink" src="'.$root_path.'gui/img/common/default/task_tree.gif"
			 border="0" align="absmiddle" ></a>');

$smarty->assign('sBillStatus','<span id="bill_status" name="bill_status" style="color:white; background-color:red"></span>');
$pkgcbo = "<option value=\"\">- Select Package -</option>\n";
$pkgcbo = '<select style="cursor:pointer" class="segInput" name="this_pkg" id="this_pkg" onchange="getPkgCoverageAmount();">'."\n".$pkgcbo."</select>\n";
$smarty->assign('sPkgCbo',$pkgcbo);


ob_start();
?>
<input type="hidden" id="hasbloodborrowed" name="hasbloodborrowed" value="" />
<input type="hidden" id="enc" name="enc" value="<?= $encounter_nr ?>" />
<input type="hidden" id="dr_nr" name="dr_nr" value="" />
<input type="hidden" id="role_nr" name="role_nr" value="" />
<input type="hidden" id="tier_nr" name="tier_nr" value="" />
<input type="hidden" id="bill_dte" name="bill_dte" value="" />
<input type="hidden" id="death_dte" name="death_dte" value="" />
<input type="hidden" id="admission_dte" name="admission_dte" value="<?= (($encounter_type==3)||($encounter_type==4)) ? $admission_dt : $encounter_date; ?>" />
<input type="hidden" id="excluded" name="excluded" value="0" />
<div style="display:none" id="opstaken"></div>
<?php
$xtemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sHiddenInputs', $xtemp);

ob_start();
?>
<input type="hidden" id="memcateg_enc" name="memcateg_enc" value="<?= $encounter_nr ?>" />
<input type="hidden" id="categ_id" name="categ_id" value="" />
<input type="hidden" id="categ_desc" name="categ_desc" value="" />
<?php
$xtemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sMemCategHiddenInputs', $xtemp);

ob_start();
?>
<input type="hidden" id="acc_enc_nr" name="acc_enc_nr" value="<?= $encounter_nr ?>" />
<input type="hidden" id="ward_nr" name="ward_nr" value="" />
<input type="hidden" id="rm_nr" name="rm_nr" value="" />
<?php
$xtemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sAccAddHiddenInputs', $xtemp);

ob_start();
?>
<input type="hidden" id="opacc_enc_nr" name="opacc_enc_nr" value="<?= $encounter_nr ?>" />
<input type="hidden" id="opw_nr" name="opw_nr" value="" />
<input type="hidden" id="opr_nr" name="opr_nr" value="" />
<input type="hidden" id="ops_entry" name="ops_entry" value="" />

<?php
$xtemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sOpAccChrgHiddenInputs', $xtemp);

ob_start();
?>
<input type="hidden" name="submitted" value="1"/>
<input type="hidden" id="userck" name="userck" value="<?php echo $userck ?>">
<input type="hidden" id="rpath" name="rpath" value="<?=$root_path?>" >
<input type="hidden" id="classify_id" name="classify_id" value="<?=$HTTP_SESSION_VARS['sess_user_name']?>" >
<input type="hidden" id="seg_URL_APPEND" name="seg_URL_APPEND" value="<?=URL_APPEND?>"  />
<input type="hidden" id="bill_frmdte" name="bill_frmdte" value=""  />
<input type="hidden" id="old_bill_nr" name="old_bill_nr" value="<?=$_GET['nr']?>" />
<input type="hidden" id="bill_pkgid" name="bill_pkgid" value=""/>
<input type="hidden" id="is_adjusted" name="is_adjusted" value=""/>
<input type="hidden" id="del_stat" name="del_stat" value="0" />
<input type="hidden" id="is_dialysis" name="is_dialysis" value="0" />
<input type="hidden" id="prev_billed_amt" name="prev_billed_amt" value="0" />
<?php
$stemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sTailScripts', $stemp);
# Assign page output to the mainframe template

//$smarty->assign('sMainFrameBlockData',$sTemp);
$smarty->assign('sMainBlockIncludeFile','billing/billing_form.tpl');
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>