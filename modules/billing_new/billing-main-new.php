<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/billing_new/ajax/billing_new.common.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_acl.php');

//test comment

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

//added by Nick 05-12-2014
$objAcl = new Acl($_SESSION['sess_temp_userid']);
$updateCaseTypePermission = $objAcl->checkPermissionRaw('_a_2_billUpdateCaseType');
//end Nick

# Start Smarty templating here
/**
* LOAD Smarty
*/
# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('system_admin');

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

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins: -->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<!-- include billing.css -->
<link rel="stylesheet" type="text/css" href="<?=$root_path?>modules/billing/css/billing.css" />

<!-- include Billing javascript -->
<script type="text/javascript" src="<?=$root_path?>js/gen_routines.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/datefuncs.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" /> 
<script type='text/javascript' src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.datetimepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.datetimepicker/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript">var $j = jQuery.noConflict();</script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.numeric.js?t=<?= time() ?>"></script>
<script type="text/javascript" src="js/billing-main-new.js?t=<?=time();?>"></script>

<script type="text/javascript">

//added by EJ 10/03/2014
function getPatientNote(){
    var pn = $j("#save_encounter_nr").val();
	return overlib(
					OLiframeContent('<?php echo $root_path ?>modules/billing_new/billing-notes.php<?php echo URL_REDIRECT_APPEND ?>&dept_nr=<?echo $ward_nr?>&location_nr=<?echo $ward_nr?>&pday=<?echo $pday?>&pmonth=<?echo $pmonth?>&pyear=<?echo $pyear?>&station=<?echo $station?>&pn='+pn,
					850, 450, 'fGroupTray', 0, 'auto'),
					WIDTH,50, TEXTPADDING,0, BORDER,0,
					STICKY, SCROLL, CLOSECLICK, MODAL,
					CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="pSearchClose();">',
					CAPTIONPADDING,2, CAPTION,'Notes',
					MIDX,0, MIDY,0,
					STATUS,'Notes');

}

</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
?>

<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$smarty->assign('sOnLoadJs',"onLoad=\"preset();\"");

$submitted = isset($_POST["submitted"]);
/*global $allowedarea;
    $allowedarea = array('_a_3_billDeleteBtn');
    if (validarea($_SESSION['sess_permission'],1)){
        $canDelete = $smarty->assign('sBtnDelete', '<button id="btnDelete" name="btnDelete" class="" type="submit" style="margin-left: 4px; cursor: pointer; font:bold 12px Arial;">
        								  	         Delete</button>');
    }    */    
if ($_GET['nr'] == '') {
        $smarty->assign('sBtnPrevPackage', '<button id="btnPrevPack" name="btnPrevPack" class="PrevPack" type="submit" style="margin-left: 4px; height: 30px; width: 170px; cursor: pointer; font:bold 12px Arial;">
        									Previous Package</button>');
        $smarty->assign('sBtnSave', '<td width="8" valign="bottom" align="left">
        								<button id="btnSave" name="btnSave" class="" type="submit" style="margin-left: 4px; height: 30px; cursor: pointer; font:bold 12px Arial;">
        								Save</button></td>');
        $smarty->assign('sBtnPrint', '<td width="8" valign="bottom" align="left">
        								<button id="btnPrint" onclick="js_btnHandler()" name="btnPrint" class="" type="submit" style="margin-left: 4px; height: 30px; cursor: pointer; font:bold 12px Arial;">
        								Print</button></td>');
        $smarty->assign('sBtnInsurance', '<button id="btnInsurance" name="btnInsurance" class="" type="submit" style="margin-left: 4px; height: 30px; cursor: pointer; font:bold 12px Arial;">
        								  	Insurance</button>');
        $smarty->assign('sBtnDiagnosis', '<button id="btnDiagnosis" name="btnDiagnosis" class="" type="submit" style="margin-left: 4px; height: 30px; width: 200px; cursor: pointer; font:bold 12px Arial;">
        								  	Diagnosis And Procedure</button>');
        $smarty->assign('sChckFinal', '<td id="chkboxrow" height="20" width="160x" align="left" bgcolor="#FF0000" valign="middle" style="border:solid 1px;">
        							   <input type="checkbox" name="isFinalBill" id="isFinalBill" style="height: 20px; vertical-align:middle; cursor:pointer" onclick="toggleFinalBill();">
        							   <span style="font:bold 12px Arial; color:white"><b>Check if Final Bill.<b></span></td>');
        $smarty->assign('sChckDetail', '<td cellspacing="10" width="98px" height="20" align="left" bgcolor="#FF0000" valign="middle" style="height: 30px; border:solid 1px;">
        								<input type="checkbox" name="IsDetailed" id="IsDetailed" style="height: 20px; vertical-align:middle">
        								<span style="font:bold 12px Arial; color:white"><b>Detailed?<b></span></td>');
		$smarty->assign('sBtnDelete', '<td width="8" valign="bottom" align="left">
        								<button id="btnDelete" name="btnDelete" class="" type="submit" style="margin-left: 4px; height: 30px; cursor: pointer; font:bold 12px Arial; display:none;">
        								  	         Delete</button></td>');
		$smarty->assign('sBtnCF2Part3', '<td width="10" valign="bottom" align="left">
        								<button id="btnCF2Part3" name="btnCF2Part3" onclick="showCF2Part3();" type="submit" style="margin-left: 4px; height: 30px; cursor: pointer; font:bold 12px Arial; display:none;">
        								  	         Page 2</button></td>');
		// Added by Johnmel Sulla 07-09-2018
		$smarty->assign('sBtnCSFp2', '<td width="19" valign="bottom" align="left">
					<button id="btnCSFp2" name="btnCSFp2" class="" onclick="" type="submit" style="margin-left: 4px; height: 30px; width: 82px; cursor: pointer; font:bold 12px Arial; display:;">
					  	         CSF p2</button></td>');

}else /*if (validarea($_SESSION['sess_permission'],1))*/ {
        $canDelete = $smarty->assign('sBtnDelete', '<td width="8" valign="bottom" align="left"><button id="btnDelete" name="btnDelete" class="" type="submit" style="margin-left: 4px;  height: 30px; width: 70px; cursor: pointer; font:bold 12px Arial;">
        								  	         Delete</button></td>');
        			 $smarty->assign('sBtnPrint', '<td width="8" valign="bottom" align="left"><button onclick="js_btnHandler()" id="btnPrint" name="btnPrint" class="" type="submit" style="height: 30px; width: 70px; margin-left: 4px; cursor: pointer; font:bold 12px Arial;">
        										   	 Print</button></td>');
                     $smarty->assign('sBtnSave', '<td width="8" valign="bottom" align="left"><button id="btnSave" name="btnSave" class="" type="submit" style="margin-left: 4px; height: 30px; cursor: pointer; font:bold 12px Arial;">
        								            Save</button></td>');
        			//added by art 1/10/2014
        			 $smarty->assign('sChckDetail', '<td cellspacing="10" width="98px" height="20" align="left" bgcolor="#FF0000" valign="middle" style="height: 30px; border:solid 1px;">
        								<input type="checkbox" name="IsDetailed" id="IsDetailed" style="height: 20px; vertical-align:middle">
        								<span style="font:bold 12px Arial; color:white"><b>Detailed?<b></span></td>');
        			 //end by art
        			 //added by art 1/11/2014
        			 $smarty->assign('sBtnInsurance', '<button id="btnInsurance" name="btnInsurance" class="" type="submit" style="margin-left: 4px; height: 30px; cursor: pointer; font:bold 12px Arial;">
        								  	Insurance</button>');
        			 $smarty->assign('sBtnDiagnosis', '<button id="btnDiagnosis" name="btnDiagnosis" class="" type="submit" style="margin-left: -150px; height: 30px; width: 200px; cursor: pointer; font:bold 12px Arial;">
        								  	Diagnosis And Procedure</button>');

        			 $smarty->assign('sBtnCF2Part3', '<td width="10" valign="bottom" align="left">
        								<button id="btnCF2Part3" name="btnCF2Part3" onclick="showCF2Part3();" type="submit" style="margin-left: 4px; height: 30px; cursor: pointer; font:bold 12px Arial;">
        								  	         Page 2</button></td>');
                    $smarty->assign('sChckFinal', '<td id="chkboxrow" height="20" width="160x" align="left" bgcolor="#FF0000" valign="middle" style="border:solid 1px;">
        							   <input type="checkbox" name="isFinalBill" id="isFinalBill" style="height: 20px; vertical-align:middle; cursor:pointer" onclick="toggleFinalBill();">
        							   <span style="font:bold 12px Arial; color:white"><b>Check if Final Bill.<b></span></td>');
        			 //end by art
                    		$smarty->assign('sBtnCSFp2', '<td width="19" valign="bottom" align="left">
					<button id="btnCSFp2" name="btnCSFp2" class="" onclick="" type="submit" style="margin-left: 4px; height: 30px; width: 82px; cursor: pointer; font:bold 12px Arial; display:;">
					  	         CSF p2</button></td>');

}

//added by borj 2014-06-01
include($root_path."include/care_api_classes/billing/class_bill_info.php");
$objbill = new BillInfo();
if ($_POST['delete']) {
if($objbill->IsDischarge( $_POST['enc_nr'], $_POST['bill_nr']) || $objbill->isWellBaby($enc_nr))  {
		if ($objbill->deleteBillInfo($_POST['delete'], $_POST['enc_nr'], $_POST['bill_nr'])) {
				$sWarning = 'Billing successfully deleted!';
		}
		else {
				$sWarning = 'Error in billing deletion: '.$db->ErrorMsg();
		}
}
}

if (!isset($_GET['nr']) || ($_GET['nr'] == '')) {
		if ($_GET['enc_nr']) {
			$encounter_nr = $_GET['enc_nr'];

			include_once($root_path.'include/care_api_classes/class_encounter.php');

			$encobj = new Encounter($encounter_nr);
			$encobj->loadEncounterData($encounter_nr);
			$pid = $encobj->PID($encounter_nr);
			
		} else {
			if ($_POST["pid"])
					$pid = $_POST["pid"];
			else
					$pid = $_GET["pid"];
		}

		if ($pid) {
			$person_obj=new Person($pid);
			$countRegularDialysis = $person_obj->countRegularDialysis($pid);
			$person_info = $person_obj->getAllInfoArray($pid);
			extract($person_info);

			if ($pid)
					$name_patient =  $name_last.", ".$name_first." ".$name_middle;
			else
					$name_patient =  "";
			if ($street_name){
				if ($brgy_name!="NOT PROVIDED")
						$street_name = $street_name.", ";
				else
						$street_name = $street_name.", ";
			}

			if ((!($brgy_name)) || ($brgy_name=="NOT PROVIDED"))
					$brgy_name = "";
			else
					$brgy_name  = $brgy_name.", ";

			if ((!($mun_name)) || ($mun_name=="NOT PROVIDED"))
					$mun_name = "";
			else{
					if ($brgy_name)
							$mun_name = $mun_name;
			}

			if ((!($prov_name)) || ($prov_name=="NOT PROVIDED"))
					$prov_name = "";

			if(stristr(trim($mun_name), 'city') === FALSE){
					if ((!empty($mun_name))&&(!empty($prov_name))){
							if ($prov_name!="NOT PROVIDED")
									$prov_name = ", ".trim($prov_name);
							else
									$prov_name = "";
					}else{
							$prov_name = "";
					}
			}else
					$prov_name = " ";

			$address = $street_name.$brgy_name.$mun_name.$prov_name;


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
			}

		}

#--------------------
if (isset($encounter_nr)) {
	if ($encounter_nr != '')
        $smarty->assign('sOnLoadJs',"onLoad=\"preset();populateBill();disabled_button2();\"");
	}
}else{
			$bill_nr = $_GET['nr'];
			
			include_once($root_path.'include/care_api_classes/class_encounter.php');

			$encobj = new Encounter($encounter_nr);
			$encounter_nr=$encobj->getEnc($bill_nr);
			$encobj->loadEncounterData($encounter_nr);
			$pid = $encobj->PID($encounter_nr);
			$admission_date = $encobj->GetAdmission($encounter_nr);
			$bill_date = $encobj->GetBillDate($bill_nr);

			if ($pid)
					$pid = $pid;
			else
					$pid = $pid;
		

		if ($pid) {
			$person_obj=new Person($pid);
			$countRegularDialysis = $person_obj->countRegularDialysis($pid);
			$person_info = $person_obj->getAllInfoArray($pid);
			extract($person_info);

			if ($pid)
					$name_patient =  $name_last.", ".$name_first." ".$name_middle;
			else
					$name_patient =  "";
			if ($street_name){
				if ($brgy_name!="NOT PROVIDED")
						$street_name = $street_name.", ";
				else
						$street_name = $street_name.", ";
			}

			if ((!($brgy_name)) || ($brgy_name=="NOT PROVIDED"))
					$brgy_name = "";
			else
					$brgy_name  = $brgy_name.", ";

			if ((!($mun_name)) || ($mun_name=="NOT PROVIDED"))
					$mun_name = "";
			else{
					if ($brgy_name)
							$mun_name = $mun_name;
			}

			if ((!($prov_name)) || ($prov_name=="NOT PROVIDED"))
					$prov_name = "";

			if(stristr(trim($mun_name), 'city') === FALSE){
					if ((!empty($mun_name))&&(!empty($prov_name))){
							if ($prov_name!="NOT PROVIDED")
									$prov_name = ", ".trim($prov_name);
							else
									$prov_name = "";
					}else{
							$prov_name = "";
					}
			}else
					$prov_name = " ";

			$address = $street_name.$brgy_name.$mun_name.$prov_name;


			if ($encounter_nr)
					$encounter_nr=$encounter_nr;

			if($admission_date)
					$admission_date = $admission_date;
			else{
					if (($encounter_type==3)||($encounter_type==4))
						$admission_date = date("M d, Y h:i A", strtotime($admission_dt));
					else
						$admission_date = date("M d, Y h:i A", strtotime($encounter_date));

					$enc_date =  date("M d, Y h:i A", strtotime($encounter_date));
			}
		}
}


$smarty->assign('sProgBar','<img src="'.$root_path.'/images/ajax_bar.gif" border=0>');

$smarty->assign('sLblMembershipCategory','<strong></strong>');

$smarty->assign('sPid','<input class="segInput" id="pid" name="pid" type="text" size="15" value="'.$pid.'" style="font:bold 12px Arial; float;left;" readOnly >');
//edited by poliam 01/05/2014
$smarty->assign('sDrpConfinement', '<select'.($_GET['nr'] == '' ? "" : " disabled").' id="confineTypeOption" name= "confineTypeOption"  onchange="jsOnchangeConfineType()" style="font:bold 12px Arial">
																		<option value="0">- Select Confinement Type -</option>
																</select>');
//end by poliam 01/05/2014
$smarty->assign('sDrpCaseType', '<select'.(($_GET['nr'] == '' && $updateCaseTypePermission == 1) ? "" : " disabled").' id="caseTypeOption" style="font:bold 12px Arial" onchange="jsOnchangeCaseType()">
																		<option value="0">- Select Case Type -</option>
																</select>');
$smarty->assign('sPatientName','<input class="segInput" id="pname" name="pname" type="text" size="28" value="'.mb_strtoupper($name_patient).'" style="font:bold 12px Arial; float;left;" readOnly >');
//Patient Address
$smarty->assign('sPatientAddress','<textarea class="segInput" id="paddress" name="paddress" cols="29" rows="2" style="font:bold 12px Arial" readOnly>'.mb_strtoupper($address).'</textarea>');
#--------------
//Select Patient
if (isset($_GET['area']))
	$smarty->assign('sSelectPatient','');
else
	//edited by poliam 01/04/2014
	$smarty->assign('sSelectPatient','<input class="segInput"'.($_GET['nr'] == '' ? "" : " disabled").' id="select-enc" type="hidden" border="0" style=""/>');
	//end by poliam 01/04/2014

$smarty->assign('sEncounter','<input class="segInput" id="encounter_nr" name="encounter_nr" type="text" size="16" value="'.$encounter_nr.'" style="font:bold 12px Arial; float;left;" readOnly >');
//added by pol 07-23-13
//updated by jane 10/17/2013 - added color:#ff0000 (red) value to style attribute
$smarty->assign('sPhic','<input class="segInput" id="phic" name="phic" type="text" size="15" value="'.$phic.'" style="color: #ff0000;font:bold 12px Arial; float;left;" readOnly >');
//Date$
// $curDate = date("m/d/Y");
$curTme  = strftime("%Y-%m-%d %H:%M:%S");
$curDate = strftime("%b %d, %Y %I:%M%p", strtotime($curTme));
if ($_GET['nr']){
$smarty->assign('sDate', '<input class="segInput" id="billdate_display" name="billdate_display" type="text" size="16" value="'.$curDate.'" style="font:bold 12px Arial; float;left;" >
						  <input class="jedInput" name="billdate" id="billdate" type="hidden" value="'.($submitted ? strftime("%Y-%m-%d %H:%M:%S", strtotime($bill_date)) : $curTme).'" style="font:bold 12px Arial">');
}else{
$smarty->assign('sDate', '<input class="segInput" id="billdate_display" name="billdate_display" type="text" size="16" value="'.$curDate.'" style="font:bold 12px Arial; float;left;" >
						  <input class="jedInput" name="billdate" id="billdate" type="hidden" value="'.($submitted ? strftime("%Y-%m-%d %H:%M:%S", strtotime($_POST['billdate'])) : $curTme).'" style="font:bold 12px Arial">');
}
$smarty->assign('sDeathDate',  '<input class="segInput" id="death_date" name="death_date" type="text" size="16" value="'.$curDate.'" style="font:bold 12px Arial; float;left;" >
						  		<input class="jedInput" name="deathdate" id="deathdate" type="hidden" value="'.($submitted ? strftime("%Y-%m-%d %H:%M:%S", strtotime($_POST['deathdate'])) : $curTme).'" style="font:bold 12px Arial">');


# assigning the toggle of the check box of dialysis added by Genz
$smarty->assign('sDialysis',  '<input class="segInput" id="reg_dialysis" name="reg_dialysis" type="text" readOnly="" size="4"  style="font:bold 12px Arial; float;left;" >');

//Encounter date (not admission date).
$smarty->assign('sAdmissionDate','<input class="segInput" id="admission_date" name="admission_date" type="text" size="16"  value="'.$enc_date.'" style="font:bold 12px Arial" readOnly>');

// Last bill date.
$smarty->assign('sLastBillDate','<input class="segInput" id="lastbill_date" name="lastbill_date" type="text" size="16"  value="" style="font:bold 12px Arial" readOnly>');

//Admission date.
$smarty->assign('sAdmitDate','<input class="segInput" id="date_admitted" name="date_admitted" type="text" size="16"  value="'.$admission_date.'" style="font:bold 12px Arial" readOnly>');

//Age
$smarty->assign('sAge','<input class="segInput" id="age" name="age" type="text" size="16"  value="" style="font:bold 12px Arial" readOnly>');

//Days Admitted
$smarty->assign('sAdmDays','<input class="segInput" id="days_admitted" name="days_admitted" type="text" size="16"  value="" style="font:bold 12px Arial" readOnly>');

//Other Insurance
$smarty->assign('sOtherInsurances','<input class="segInput" id="other_insurance" name="other_insurance" type="text" size="16"  value="" style="font:bold 12px Arial" readOnly>');

//Added by EJ 10/03/2014
$sPatientNote = '<button href="javascript:void(0);" onClick="getPatientNote()"><img ';
				if($bed['ward_notes']) {
					$sPatientNote = $sPatientNote.createComIcon($root_path,'bubble3.gif','0','',TRUE);
				}
				else {
					$sPatientNote = $sPatientNote.createComIcon($root_path,'bubble2.gif','0','',TRUE);
				}
$smarty->assign('sPatientNote','<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" style="font:bold 12px Arial; height:30; width:138px; href="javascript:void(0);" onClick="getPatientNote()">View Patient Note</button></a>');

//Show Btn Details
#$smarty->assign('btnShowDetails','<input id="btnShowDetails" name="btnShowDetails" type="button" value="Show Billing"/>');
#$smarty->assign('sAddMiscOps', '<span style="padding:12px"><button class="jedButton" style="cursor:pointer"'.($_GET['nr'] == '' ? "" : " disabled").' id="btnaddmisc_ops">Misc. Procedures</button></span>');

$smarty->assign('sBtnAddAccommodation', '<span style="padding:12px"><button class="" style="font:bold 12px Arial; cursor:pointer"'.($_GET['nr'] == '' ? "" : " ").' id="btnaccommodation">Additional Accommodation</button></span>');
//$smarty->assign('sAddAccommodationBeta', '<span style="padding:12px"><button class="jedButton" style="cursor:pointer"'.($_GET['nr'] == '' ? "" : " disabled").' id="btnaccommodationBeta" onclick="openAccomodationBeta();">Additional Accommodation Beta</button></span>');

$smarty->assign('sAddOPAccommodation', '<span style="padding:12px"><button class="" style="font:bold 12px Arial; cursor:pointer"'.($_GET['nr'] == '' ? "" : " ").' id="btnOPaccommodation">O.R. Use</button></span>');

$smarty->assign('sAddMedsandSupplies','<span style="padding:12px"><button class="" style="font:bold 12px Arial; cursor:pointer"'.($_GET['nr'] == '' ? "" : " ").' id="btnmedsandsupplies">More Meds</button></span>');

$smarty->assign('sAddMiscOps','<button class="" style="font:bold 12px Arial; cursor:pointer"'.($_GET['nr'] == '' ? "" : " ").' name="btnaddmisc_ops" id="btnaddmisc_ops" onclick="js_AddMiscOps()">Misc. Procedures</button></a>');

$smarty->assign('sBtnAddDoctorsButton', '<span style="padding:12px"><button class="" style="font:bold 12px Arial; cursor:pointer"'.($_GET['nr'] == '' ? "" : " ").' id="btnadddoctors">Add Doctors</button></span>');

$smarty->assign('sBtnAddMiscService','<button class="" style="font:bold 12px Arial; cursor:pointer"'.($_GET['nr'] == '' ? "" : " ").' name="btnaddmisc_srvc" id="btnaddmisc_srvc">Misc. Services and Supplies</button></a>');

$smarty->assign('sAddMiscChrg','<button class="" style="font:bold 12px Arial; cursor:pointer"'.($_GET['nr'] == '' ? "" : " ").' name="btnaddmisc_chrg" id="btnaddmisc_chrg" >Misc. Charges</button></a>');

$smarty->assign('sBtnDiscountDetails','<span style="padding:12px"><button class="" style="font:bold 12px Arial; cursor:pointer"'.($_GET['nr'] == '' ? "" : " ").' id="btnadd_discount"onmouseout="nd();">Discount Details</button></span>');

#commented by genz removed the disable as of request of GMMCI
// $smarty->assign('sBtnOutMedsXLO','<span style="padding:12px"><button class="" style="font:bold 12px Arial; cursor:pointer"'.($_GET['nr'] == '' ? "" : " disabled").' id="btnOutMedsXLO" onmouseout="nd();">Outside Meds And XLO</button></span>');
$smarty->assign('sBtnOutMedsXLO','<span style="padding:12px"><button class="" style="font:bold 12px Arial; cursor:pointer"'.($_GET['nr'] == '' ? "" : " ").' id="btnOutMedsXLO" onmouseout="nd();">Outside Meds And XLO</button></span>');

//$smarty->assign('sBtnOtherInsurance','<span style="padding:12px"><button class="" style="font:bold 12px Arial; cursor:pointer"'.($_GET['nr'] == '' ? "" : " disabled").' id="btnOtherInsurance" onmouseout="nd();">Other Insurance</button></span>');
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
			 class="segSimulatedLink" src="'.$root_path.'gui/img/common/default/task_tree.gif"
			 border="0" align="absmiddle" ></a>');

// Select operation procedures charged with accommodation ...
$smarty->assign('sSelectOps', '<img id="ops_selected"
			 onmouseout="nd();" class="segSimulatedLink" src="'.$root_path.'gui/img/common/default/task_tree.gif"
			 border="0" align="absmiddle" >');

$smarty->assign('sBillStatus','<span id="bill_status" name="bill_status" style="color:white; background-color:red"></span>');
$pkgcbo = "<option value=\"\">- Select Package -</option>\n";
$pkgcbo = '<select style="cursor:pointer" class="segInput" name="this_pkg" id="this_pkg" onchange="getPkgCoverageAmount();">'."\n".$pkgcbo."</select>\n";
$smarty->assign('sPkgCbo',$pkgcbo);

ob_start();
?>
<input type="hidden" id="enc" name="enc" value="<?= $encounter_nr ?>" />
<input type="hidden" id="dr_nr" name="dr_nr" value="" />
<input type="hidden" id="role_nr" name="role_nr" value="" />
<input type="hidden" id="tier_nr" name="tier_nr" value="" />
<input type="hidden" id="bill_dte" name="bill_dte" value="" />
<input type="hidden" id="death_dte" name="death_dte" value="" />
<input type="hidden" id="admission_dte" name="admission_dte" value="<?= (($encounter_type==3)||($encounter_type==4)) ? ((isset($_GET['bill_fromdate']))? $_GET['bill_fromdate'] : $admission_dt) :((isset($_GET['bill_fromdate']))? $_GET['bill_fromdate'] : $encounter_date); ?>" />
<input type="hidden" id="excluded" name="excluded" value="0" />
<input type="hidden" id="firstRate" name="firstRate" value="" />
<input type="hidden" id="secondRate" name="secondRate" value="" />
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
<input type="hidden" id="bill_nr" name="bill_nr" value="<?=$_GET['nr']?>">
<input type="hidden" id="bill_pkgid" name="bill_pkgid" value=""/>
<input type="hidden" id="is_adjusted" name="is_adjusted" value=""/>
<input type="hidden" id="del_stat" name="del_stat" value="0" />
<input type="hidden" id="is_dialysis" name="is_dialysis" value="0" />
<input type="hidden" id="prev_billed_amt" name="prev_billed_amt" value="0" />
<input type="hidden" id="is_final" name="is_final" value="0" />
<input type="hidden" id="memcategory_id" name="memcategory_id" value="0" />
<input type="hidden" id="accomodation_type" name="accomodation_type" value="0" />

<?php
$stemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sTailScripts', $stemp);


ob_start();
?>
<input type="hidden" id="save_bill_nr" name="save_bill_nr" value=""/>
<input type="hidden" id="save_bill_dte" name="save_bill_dte" value=""/>
<input type="hidden" id="save_frmdte" name="save_frmdte" value=""/>
<input type="hidden" id="save_encounter_nr" name="save_encounter_nr" value=""/>
<input type="hidden" id="save_accommodation_type" name="save_accommodation_type" value=""/>
<input type="hidden" id="save_total_acc_charge" name="save_total_acc_charge" value=""/>
<input type="hidden" id="save_total_med_charge" name="save_total_med_charge" value=""/>
<input type="hidden" id="save_total_srv_charge" name="save_total_srv_charge" value=""/>
<input type="hidden" id="save_total_ops_charge" name="save_total_ops_charge" value=""/>
<input type="hidden" id="save_total_doc_charge" name="save_total_doc_charge" value=""/>
<input type="hidden" id="save_total_msc_charge" name="save_total_msc_charge" value=""/>
<input type="hidden" id="save_total_prevpayment" name="save_total_prevpayment" value=""/>
<input type="hidden" id="save_applied_hrs_cutoff" name="save_applied_hrs_cutoff" value=""/>
<input type="hidden" id="save_is_final" name="save_is_final" value=""/>
<input type="hidden" id="save_request_flag" name="save_request_flag" value=""/>
<input type="hidden" id="save_modify_id" name="save_modify_id" value=""/>
<input type="hidden" id="save_modify_dt" name="save_modify_dt" value=""/>
<input type="hidden" id="save_create_id" name="save_create_id" value=""/>
<input type="hidden" id="save_create_dt" name="save_create_dt" value=""/>
<input type="hidden" id="save_is_deleted" name="save_is_deleted" value=""/>
<input type="hidden" id="save_pid" name="save_pid" value=""/>
<input type="hidden" id="save_current_year" name="save_current_year" value=""/>
<input type="hidden" id="save_hcare_id" name="save_hcare_id" value=""/>
<input type="hidden" id="save_confine_days" name="save_confine_days" value=""/>
<input type="hidden" id="save_prinicipal_pid" name="save_prinicipal_pid" value=""/>
<input type="hidden" id="save_total_acc_coverage" name="save_total_acc_coverage" value=""/>
<input type="hidden" id="save_total_med_coverage" name="save_total_med_coverage" value=""/>
<input type="hidden" id="save_total_srv_coverage" name="save_total_srv_coverage" value=""/>
<input type="hidden" id="save_total_ops_coverage" name="save_total_ops_coverage" value=""/>
<input type="hidden" id="save_total_d1_coverage" name="save_total_d1_coverage" value=""/>
<input type="hidden" id="save_total_msc_coverage" name="save_total_msc_coverage" value=""/>
<input type="hidden" id="save_discountid" name="save_discountid" value=""/>
<input type="hidden" id="save_discount" name="save_discount" value=""/>
<input type="hidden" id="save_discount_amnt" name="save_discount_amnt" value=""/>
<input type="hidden" id="save_total_acc_discount" name="save_total_acc_discount" value=""/>
<input type="hidden" id="save_total_med_discount" name="save_total_med_discount" value=""/>
<input type="hidden" id="save_total_srv_discount" name="save_total_srv_discount" value=""/>
<input type="hidden" id="save_total_ops_discount" name="save_total_ops_discount" value=""/>
<input type="hidden" id="save_total_d1_discount" name="save_total_d1_discount" value=""/>
<input type="hidden" id="save_dr_nr" name="save_dr_nr" value=""/>
<input type="hidden" id="save_role_area" name="save_role_area" value=""/>
<input type="hidden" id="save_dr_charge" name="save_dr_charge" value=""/>
<input type="hidden" id="save_claim" name="save_claim" value=""/>
<input type="hidden" id="save_total_package" name="save_total_package" value=""/>
<input type="hidden" id="hasbloodborrowed" name="hasbloodborrowed" value=""/>
<?php

$stemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sSaveinputs', $stemp);
# Assign page output to the mainframe template

//$smarty->assign('sMainFrameBlockData',$sTemp);
$smarty->assign('sMainBlockIncludeFile','billing_new/billing_form_new.tpl');
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>