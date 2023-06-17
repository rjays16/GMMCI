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
define('LANG_FILE','products.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');
# Create products object
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab;

#---added by VAS
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

//$db->debug=1;

$dbtable='care_config_global'; // Taboile name for global configurations
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

#commented by VAS
/*
# Check for the department nr., else show department selector
if(!isset($dept_nr)||!$dept_nr){
	if($cfg['thispc_dept_nr']){
		$dept_nr=$cfg['thispc_dept_nr'];
	}else{
		header('Location:seg-lab-select-dept.php'.URL_REDIRECT_APPEND.'&target=plist&retpath='.$retpath);
		exit;
	}
}
*/
$breakfile=$root_path."modules/laboratory/labor.php".URL_APPEND."&userck=$userck";
$imgpath=$root_path."pharma/img/";
$thisfile='seg-lab-test-request.php';

# Save data routine
#------comment by VAN
echo "<br>mode = $mode<br>";
if ($mode=='save') include($root_path.'include/inc_lab_request_db_save_mod.php');
#------------------


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
 $smarty->assign('sToolbarTitle',"Laboratory::New test request");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Laboratory::New test request");

 # Assign Body Onload javascript code
 #echo "dept_nr = ".$dept_nr;
 $onLoadJS='onLoad="';
 if($mode!='save'&&$mode!='update') 
 	$onLoadJS.="";

	echo "dept_nr = $dept_nr";
	echo "<br>is_cash = $is_cash";
/**commented by van**/

 #if ($saveok) $onLoadJS.="xajax_populateServiceGroups($dept_nr);xajax_populateServices($dept_nr,$is_cash);";
/***************/
 #echo "<br>saveok = $saveok<br>";
 $onLoadJS.="xajax_populateServiceGroups($dept_nr);xajax_populateServices($dept_nr,$is_cash);";
 #$onLoadJS.="jsGetServices();";
 
 #if ($saveok) $onLoadJS.="xajax_populateServiceGroups();xajax_populateServices($is_cash);";
 $onLoadJS.='"';
 #echo "onLoadJS = $onLoadJS";
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

if($error=="refno_exists"){
	$smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
	$smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}

if($update&&(!$updateok)&&($mode=='save')) $smarty->assign('sNoSave',$LDDataNoSaved.'<a href="'.$thisfile.URL_APPEND.'"><u>'.$LDClk2EnterNew.'</u></a>');

 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="post" name="inputform" id="inputform" onSubmit="return prufformlab(this)">');
 $smarty->assign('sFormEnd','</form>');

 # Assign form inputs (or values)

 //if ($saveok||$update) $smarty->assign('sOrderNrInput',$bestellnum.'</b><input type="hidden" name="bestellnum" value="'.$bestellnum.'">');
 //	else $smarty->assign('sOrderNrInput','<input type="text" name="bestellnum" value="'.$bestellnum.'" size=20 maxlength=20>');

#added by VAS
#echo "dept_nr = ".$dept_nr;
$deptObj = $dept_obj->getDeptAllInfo($dept_nr);
#echo "dept = ".$deptObj['name_formal'];
if ($saveok){
	$smarty->assign('sReferenceNoInput',$refno . '<input type="hidden" name="refno" size="20" maxlength="20" value="'.$refno.'"><input type="hidden" name="refnoex" id="refnoex" size="20" maxlength="20" value="'.$refno.'">');
	$smarty->assign('sPurchaseDateInput',$purchasedt.'<input type="hidden" name="purchasedt" id="purchase_date" size="20" value="'.$purchasedt.'">');
	$smarty->assign('sPayerNameInput',$pname . '<input type="hidden" id="payer_text" name="pname" size="20" readonly="1" value="'.$pname.'">');
	#$smarty->assign('sDeptNameInput',$dept_name . '<input type="hidden" id="dept_name" name="dept_name" size="20" readonly="1" value="'.$dept_name.'">');
	#edited by VAS
	$smarty->assign('sDeptNameInput',$deptObj['name_formal'] . '<input type="hidden" id="dept_nr" name="dept_nr" size="20" value="'.$dept_nr.'">');
	$smarty->assign('sPayerID','<input type="hidden" id="payer_id" name="pencnum"  value="'.$pencnum.'">');
	#commented by VAS
	#$smarty->assign('sDeptID','<input type="hidden" id="dept_nr" name="dept_nr"  value="'.$dept_nr.'">');
	$smarty->assign('sIsCashCheckBox',($is_cash?"Cash":"Charge").'<input type="hidden" name="is_cash" value="'.($is_cash?1:0).'">');
	#added by VAS
	$smarty->assign('sDeptName',$deptObj['name_formal']);
	
}else{
	$smarty->assign('sReferenceNoInput','<input type="text" name="refno" id="refno" size="20" maxlength="20" value="'.$refno.'"><input type="hidden" name="refnoex" id="refnoex" size="20" maxlength="20" value="'.$refnoex.'">');
	$smarty->assign('sPurchaseDateInput','<input type="text" name="purchasedt" id="purchase_date" size="20" maxlength="20" value="'.$purchasedt.'">');
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="purchase_date_trigger" align="absmiddle" style="cursor:pointer">');
	$smarty->assign('sPayerNameInput','<input type="text" id="payer_text" name="pname" size="20" readonly="1" value="'.$pname.'">'); # burn modified c/o alvin: August 10, 2006
	#commented by VAS
	#$smarty->assign('sDeptNameInput','<input type="text" id="dept_name" name="dept_name" size="20" readonly="1" value="'.$dept_name.'">');
	#$smarty->assign('sDeptName',$dept_name);
	
	#added by VAS
	$smarty->assign('sDeptName',$deptObj['name_formal']);
	
	$smarty->assign('sPayerID','<input type="hidden" id="payer_id" name="pencnum"  value="'.$pencnum.'">');
	#commented by VAS
	#$smarty->assign('sDeptID','<input type="hidden" id="dept_nr" name="dept_nr"  value="'.$dept_nr.'">');
	#$smarty->assign('sDeptName',$dept_name);
	#added by VAS
	$smarty->assign('sDeptName',$deptObj['name_formal']);
	#commented by VAS
	#$smarty->assign('sDeptSelectButton','<input type="button" name="deptselect" value="Select" onclick="window.open(\'' . $root_path."modules/laboratory/seg-lab-select-dept.php".URL_APPEND."&asdf=1" . '\',\'dept_select\',\'width=890,height=640,menubar=no,resizable=yes,scrollbars=yes\')">');
	#added by VAS
	$all_meds=&$dept_obj->getAllCommonMedical();
	#echo "dept_nr = ".$_POST['dept_nr'];
	
	$sTemp = '';
	#$sTemp = $sTemp.'<select name="dept_nr" id="dept_nr" onChange="jsGetServices(inputform);">
				#		  <option value="0">Select a Department</option>';
	$sTemp = $sTemp.'<select name="dept_nr" id="dept_nr" onChange="jsGetServices(); document.inputform.submit();">
						  <option value="0">Select a Department</option>';			
	
	if(is_object($all_meds)){
		while($deptrow=$all_meds->FetchRow()){
			$sTemp = $sTemp.'
								<option value="'.$deptrow['nr'].'" ';
			if(isset($dept_nr)&&($dept_nr==$deptrow['nr'])) $sTemp = $sTemp.'selected';
				$sTemp = $sTemp.'>';
				if($$deptrow['LD_var']!='') $sTemp = $sTemp.$$deptrow['LD_var'];
				else $sTemp = $sTemp.$deptrow['name_formal'];
			$sTemp = $sTemp.'</option>';
		}
	}
	$sTemp = $sTemp.'</select><font size=1><img '.createComIcon($root_path,'redpfeil_l.gif','0','',TRUE).'> Department</font>';
	$smarty->assign('sDeptSelectButton',$sTemp);
	#---------------------
	
	$smarty->assign('sPayerSelectButton','<input type="button" name="payerselect" value="Select" onclick="window.open(\'' . $root_path."modules/laboratory/seg-lab-patient-select.php".URL_APPEND."&clear_ck_sid=$clear_ck_sid" . '\',\'patient_select\',\'width=890,height=640,menubar=no,resizable=yes,scrollbars=yes\')">');
	
	if (!isset($is_cash))
		#$smarty->assign('sIsCashCheckBox','<input type="radio" name="is_cash" id="is_cash_0" value="1" checked> Cash&nbsp;&nbsp;<input type="radio" name="is_cash" id="is_cash_1" value="0"> Charge');
		$smarty->assign('sIsCashCheckBox','<input type="radio" name="is_cash" id="is_cash" value="1" checked onClick="jsGetServices(); document.inputform.submit();"> Cash&nbsp;&nbsp;<input type="radio" name="is_cash" id="is_cash" value="0" onClick="jsGetServices(); document.inputform.submit();"> Charge');
	else
		#$smarty->assign('sIsCashCheckBox','<input type="radio" name="is_cash" id="is_cash_0" value="1" '.(($is_cash)?"checked":"").'> Cash&nbsp;&nbsp;<input type="radio" name="is_cash" id="is_cash_1" value="0" '.((!$is_cash)?"checked":"").'> Charge');
		$smarty->assign('sIsCashCheckBox','<input type="radio" name="is_cash" id="is_cash" value="1" '.(($is_cash)?"checked":"").' onClick="jsGetServices(); document.inputform.submit();"> Cash&nbsp;&nbsp;<input type="radio" name="is_cash" id="is_cash" value="0" '.((!$is_cash)?"checked":"").' onClick="jsGetServices(); document.inputform.submit();"> Charge');

	$jsCalScript = "<script type=\"text/javascript\">
Calendar.setup ({
	inputField : \"purchase_date\", ifFormat : \"$phpfd\", showsTime : false, button : \"purchase_date_trigger\", singleClick : true, step : 1
});
</script>
";
	$smarty->assign('jsCalendarSetup', $jsCalScript);
}

# Collect hidden inputs

ob_start();
$sTemp='';
 ?>

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
	$smarty->assign('sUpdateButton','<input type="image" '.createLDImgSrc($root_path,'update.gif','0').'>');
	$sBreakImg ='close2.gif';	

}else{
	$sBreakImg ='cancel.gif';
}
echo "saveok = $saveok";
//$smarty->assign('sDebug', "saveok:'$saveok'");
if ($saveok) {
	# Display the transaction details
	//$smarty->assign('sTransactionDetailsDivision','');
	//$smarty->assign('sTransactionDetailsControls', file_get_contents("seg-pharma-retail-new-rdetails.inc.php"));
	#echo "sulod";
	ob_start();
	include("seg-lab-new-rdetails.inc.php");
	#include("seg-lab-new-discount.inc.php");
	$sDiscount = ob_get_contents();
	ob_end_clean();
	#echo "sDiscount = ".$sDiscount;
	$smarty->assign('sDiscountControls', $sDiscount);
}
else {
}
	$smarty->assign('sHiddenInputs',$sTemp);
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','left').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
	#-----commented by VAS
	/*
	if (!$saveok) 
		$smarty->assign('sContinueButton','<input type="image" '.createLDImgSrc($root_path,'continue.gif','0','left').' align="absmiddle">');
	*/#---------------
/*	else
		$smarty->assign('sTailScripts', '<script language="javascript">xajax_populateDetails("'.$refno.'");xajax_populateDiscountSelection();xajax_populateRetailDiscounts("'.$refno.'");</script>'); */
	#added by VAS
	
	/*---commented by van*/
	/*
	if (!$saveok){ 
		$smarty->assign('sContinueButton','<input type="image" '.createLDImgSrc($root_path,'continue.gif','0','left').' align="absmiddle">');
		#$smarty->assign('sNewButton','<input type="image" '.createLDImgSrc($root_path,'newdata.gif','0','left').' align="absmiddle" onClick="resetField(inputform);">');
		#$smarty->assign('sNewButton','<input type="image" '.createLDImgSrc($root_path,'newdata.gif','0','left').' align="absmiddle" onClick="thisform();">');
	}	
	*/
	/***********************/
	
	# Assign the form template to mainframe
	$smarty->assign('sMainBlockIncludeFile','laboratory/form.tpl');
  
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>