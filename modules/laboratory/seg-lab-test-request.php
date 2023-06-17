<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/laboratory/ajax/lab-new.common.php");
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');
# Create laboratory object
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab();

#---added by VAS
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

require_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;

require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

//$db->debug=1;

$dbtable='care_config_global'; // Taboile name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$glob_obj->getConfig('refno_%');
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];

#$glob_obj->getConfig('refno_%'); 

$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$breakfile=$root_path."modules/laboratory/labor.php".URL_APPEND."&userck=$userck";
#$imgpath=$root_path."pharma/img/";
#echo "imgpath = ".$imgpath;
$thisfile='seg-lab-test-request.php';

# Save data routine
if (($mode=='save')&&($saverequest))
	include($root_path.'include/inc_lab_request_db_save_mod.php');

#if ($saveok) include($root_path.'include/inc_retail_display_rdetails.php');
//if ($send_details) include($root_path.'include/inc_retail_display_rdetails.php');

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$LDLab::$LDLabNewTest");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDLab::$LDLabNewTest");

 # Assign Body Onload javascript code
 $onLoadJS='onLoad="';
 if($mode!='save'&&$mode!='update') 
 	$onLoadJS.="";

if ($parameterselect==NULL){
	$parameterselect = "none";
}

if ($is_cash==NULL){
	$is_cash = 1;	
}
	
 $onLoadJS.="preset(0);jsgetDate();jsGetServices();";
 
 $onLoadJS.='"';
 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code

ob_start();

	 # Load the javascript code
	require($root_path.'include/inc_js_retail.php');
	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\r\n";
	echo '<script type="text/javascript" src="'.$root_path.'modules/laboratory/js/lab-new-gui.js"></script>'."\r\n";
	$xajax->printJavascript($root_path.'classes/xajax');
	$sTemp = ob_get_contents();
ob_end_clean();
$sTemp.="
<script type='text/javascript'>	
	var init=false;
	var refno='$refno';
</script>";

$smarty->append('JavaScript',$sTemp);

# Assign prompt messages
	if($saveok){
		if($update) $smarty->assign('sSaveFeedBack',"Update was successful.");
		else $smarty->assign('sSaveFeedBack',"Data was successfully saved.");
	}

if($update&&(!$updateok)&&($mode=='save')&&($saverequest)) 
	$smarty->assign('sNoSave',$LDDataNoSaved.'<a href="'.$thisfile.URL_APPEND.'"><u>'.$LDClk2EnterNew.'</u></a>');

 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="post" name="inputform" id="inputform" onSubmit="return prufformlab(this)">');
 $smarty->assign('sFormEnd','</form>');

$grpObj = $srvObj->getAllLabGroupInfo($parameterselect);
if ($saveok){
	$smarty->assign('sReference',"Reference No.");
	$smarty->assign('sReferenceNoInput',$refno . '<input type="hidden" name="refno" id="refno" size="20" maxlength="20" value="'.$refno.'"><input type="hidden" name="refnoex" id="refnoex" size="20" maxlength="20" value="'.$refno.'">');
	
	$smarty->assign('sPurchaseDateInput',$purchasedt.'<input type="hidden" name="purchasedt" id="purchasedt" size="20" value="'.$purchasedt.'">');
	$smarty->assign('sPayerNameInput',$pname . '<input type="hidden" id="payer_text" name="pname" size="20" readonly="1" value="'.$pname.'">');
	$smarty->assign('sGrpNameInput','<input type="hidden" id="parameterselect" name="parameterselect" size="20" value="'.$parameterselect.'">');
	
	$smarty->assign('sPayerID','<input type="hidden" id="payer_id" name="pencnum"  value="'.$pencnum.'">');
	$smarty->assign('sIsCashCheckBox',($is_cash?"Cash":"Charge").'<input type="hidden" name="is_cash" value="'.($is_cash?1:0).'">');
	$smarty->assign('sPatientName',"Patient's Name");
	
	if ($pencnum!=NULL){
		$encounter = $enc_obj->getEncounter($pencnum);
		
		if (($encounter['encounter_type'] == 2) || ($encounter['encounter_nr'] == NULL)){
				$enc_type = "OPD / Walkin";
		}else{
				$enc_type = "Inpatient";
		}
	}
	$smarty->assign('sLabGroup',"Patient's Type");
	$smarty->assign('sParamGroupSelect',$enc_type);
	
}else{
	$smarty->assign('sPurchaseDateInput','<input type="text" name="purchasedt" id="purchasedt" size="20" maxlength="20" value="'.$purchasedt.'">');
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="purchasedt_trigger" align="absmiddle" style="cursor:pointer">');
	$smarty->assign('sPayerNameInput','<input type="text" id="payer_text" name="pname" size="35" readonly="1" value="'.$pname.'">'); # burn modified c/o alvin: August 10, 2006
	$smarty->assign('sLabGroup',"Laboratory Service Group");
	$smarty->assign('sPatientName',"Select patient");
	$smarty->assign('sPayerID','<input type="hidden" id="payer_id" name="pencnum"  value="'.$pencnum.'">');
	$smarty->assign('sGroupName',$grpObj['name']);
	
	$all_labgrp=&$srvObj->getLabServiceGroups2();
	$sTemp = '';
	$sTemp = $sTemp.'<select name="parameterselect" id="parameterselect" onChange="hideThis(this,\'stable\',0);jsViewServices(1);get_check_value();">
								<option value="none">Select a Laboratory Service Group</option>';

				if(!empty($all_labgrp)&&$all_labgrp->RecordCount()){
						while($result=$all_labgrp->FetchRow()){
							$sTemp = $sTemp.'
								<option value="'.$result['group_code'].'" ';
							if(isset($parameterselect)&&($parameterselect==$result['group_code'])) $sTemp = $sTemp.'selected';
							$sTemp = $sTemp.'>'.$result['name'].'</option>';
						}
				}
					$sTemp = $sTemp.'</select>
							<font size=1><img '.createComIcon($root_path,'redpfeil_l.gif','0','',TRUE).'> Lab Service Group</font>';
	$smarty->assign('sParamGroupSelect',$sTemp);

	#---------------------
	
	$smarty->assign('sPayerSelectButton','<input type="button" name="payerselect" value="Select" onclick="window.open(\'' . $root_path."modules/laboratory/seg-lab-patient-select.php".URL_APPEND."&clear_ck_sid=$clear_ck_sid" . '\',\'patient_select\',\'width=890,height=640,menubar=no,resizable=yes,scrollbars=yes\')">');
	
	if (!isset($is_cash))
		$smarty->assign('sIsCashCheckBox','<input type="radio" name="is_cash" id="is_cash" value="1" onClick="jsViewServices();" checked > Cash&nbsp;&nbsp;<input type="radio" name="is_cash" id="is_cash" value="0" onClick="jsViewServices();"> Charge');
	else
		$smarty->assign('sIsCashCheckBox','<input type="radio" name="is_cash" id="is_cash" value="1" '.(($is_cash)?"checked":"").' onClick="jsViewServices();"> Cash&nbsp;&nbsp;<input type="radio" name="is_cash" id="is_cash" value="0" '.((!$is_cash)?"checked":"").' onClick="jsViewServices();"> Charge');

	$jsCalScript = "<script type=\"text/javascript\">
Calendar.setup ({
	inputField : \"purchasedt\", ifFormat : \"$phpfd\", showsTime : false, button : \"purchasedt_trigger\", singleClick : true, onClose: function(cal) { cal.hide();document.inputform.purchasedt2.value=document.inputform.purchasedt.value;}, step : 1
});
</script>
";

	$smarty->assign('jsCalendarSetup', $jsCalScript);
}

# Collect hidden inputs

ob_start();
$sTemp='';
 ?>

  <input type="hidden" name="sid" id="sid" value="<?php echo $sid?>">
  <input type="hidden" name="lang" id="lang" value="<?php echo $lang?>">
  <input type="hidden" name="cat" id="cat" value="<?php echo $cat?>">
  <input type="hidden" name="userck" id="userck" value="<?php echo $userck?>">  
  <input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
  <input type="hidden" name="encoder" id="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
  <input type="hidden" name="dstamp" id="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
  <input type="hidden" name="tstamp" id="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
  <input type="hidden" name="lockflag" id="lockflag" value="<?php echo  $lockflag?>">
  <input type="hidden" name="update" id="update" value="<?php if($saveok) echo "1"; else echo $update;?>">

<!--Added by VAN -->
	<input type="hidden" name="saverequest"   id="saverequest"   value="<?php echo $saverequest; ?>">
	<input type="hidden" name="serviceArray_prev" id="serviceArray_prev" value="<?php echo $serviceArray_prev; ?>" size="100">
	<input type="hidden" name="serviceArray" id="serviceArray" value="<?php echo $serviceArray; ?>" size="100">
	<input type="hidden" name="curdate"   id="curdate"   value="<?php echo date("m/d/y"); ?>">
	<input type="hidden" name="purchasedt2" id="purchasedt2" value="<?= $purchasedt2?>">
	<input type="hidden" name="refno" id="refno" value="<?= $refno ?>">
	
	<!--<input type="text" name="serviceArray_prev" id="serviceArray_prev" value="<?php echo $serviceArray; ?>" size="100">-->
	
	<input type="hidden" name="editpencnum"   id="editpencnum"   value="">	
	<input type="hidden" name="editpentrynum" id="editpentrynum" value="">
	<input type="hidden" name="editpname" id="editpname" value="">
	<input type="hidden" name="editpqty"  id="editpqty"  value="">
	<input type="hidden" name="editppk"   id="editppk"   value="">
	<input type="hidden" name="editppack" id="editppack" value="">	
<?php 

$sTemp = ob_get_contents();
ob_end_clean();

# Append more hidden inputs acc. to mode

if($update){
	if($mode!="save"){ 
		$sTemp = $sTemp.'
	  <input type="hidden" name="ref_bnum" value="'.$bestellnum.'">
	  <input type="hidden" name="ref_artnum" value="'.$artnum.'">
 	 <input type="hidden" name="ref_indusnum" value="'.$indusnum.'">
 	 <input type="hidden" name="ref_artname" value="'.$artname.'">
 	 ';
	}else{ 
		$sTemp = $sTemp.'
 	 <input type="hidden" name="ref_bnum" value="'.$ref_bnum.'">
	 <input type="hidden" name="ref_artnum" value="'.$ref_artnum.'">
 	 <input type="hidden" name="ref_indusnum" value="'.$ref_indusnum.'">
 	 <input type="hidden" name="ref_artname" value="'.$ref_artname.'">
	  ';
	}
}

if($saveok){

	$smarty->assign('sNewProduct',"<a href=\"".$thisfile.URL_APPEND."&cat=$cat&userck=$userck&update=0\">$LDNewProduct</a>");
	
	# Show update button
	$smarty->assign('sUpdateButton','<input type="image" '.createLDImgSrc($root_path,'update.gif','0').' onClick="resetSave();">');
	$sBreakImg ='close2.gif';	

}else{
	$sBreakImg ='cancel.gif';
}

if($saveok){
	ob_start();
	include("seg-lab-request-details.inc.php");
	$sDiscount = ob_get_contents();
	ob_end_clean();
	
	$smarty->assign('sDiscountControls', $sDiscount);
	#$smarty->assign('sViewPDF','<input type="button" value="View PDF File" style="float:left;margin-left:4px;margin-top:4px; cursor:pointer;" onClick="viewPatientRequest('.$is_cash.');">');
	$smarty->assign('sViewPDF','<input type="image" '.createLDImgSrc($root_path,'viewpdf.gif','0','left').' align="absmiddle" name="viewfile" id="viewfile" onClick="viewPatientRequest('.$is_cash.');">');
}
else {
	$smarty->assign('sFilter','Filter: <input type="text" id="searchserv" name="searchserv" style="width:120px" value="" onKeyUp="fetchServList(300);">&nbsp;<img src="../../gui/img/common/default/redpfeil_l.gif"><font size=1>&nbsp;Laboratory Services</font>');
		
	$sDiscount = '';
	$sDiscount = $sDiscount.'<span id="stable">
										<h3 style="margin:4px">Clinical Laboratory Services</h3>
										<div id="listcontainer" align="center">
											<span>
												Selected:<span id="selectedcount">0</span>
											</span>
								  		<table id="srcRowsTable" style="margin-botton:5px" width="85%" border="0" cellpadding="0" cellspacing="0">
			
								  		</table>
		
										</div><br></span>';
	$smarty->assign('sDiscountControls', $sDiscount);
	
	$smarty->assign('sContinueButton','<input type="image" '.createLDImgSrc($root_path,'savedisc.gif','0','left').' align="absmiddle" name="savebutton" id="savebutton" onClick="jsSaveRequest();get_check_value();">');
}

	$smarty->assign('sHiddenInputs',$sTemp);
	#cancel button
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','left').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
	
	$fileforward="seg-lab-test-request.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;

	$smarty->assign('sAddNewRequest','<a href="'.$fileforward.'"><img '.createLDImgSrc($root_path,'newrequest.gif','0','left').' border=0 alt="Enter New Lab Request"></a>');
	
	$fileforward2="seg-lab-request-list.inc.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;

	$smarty->assign('sViewRequest','<a href="'.$fileforward2.'"><img '.createLDImgSrc($root_path,'showrequest.gif','0','left').' border=0 alt="View the List of Requestors"></a>');
	

	#--------------------
	
	# Assign the form template to mainframe
	$smarty->assign('sMainBlockIncludeFile','laboratory/form.tpl');
  
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>