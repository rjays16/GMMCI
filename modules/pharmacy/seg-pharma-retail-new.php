<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/pharmacy/ajax/retail-new.common.php");
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Create products object
require_once($root_path.'include/care_api_classes/class_pharma_transaction.php');
$pharma_obj=new SegPharma;

//$db->debug=1;

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

$title=$LDPharmacy;
$breakfile=$root_path."modules/pharmacy/seg-pharma-retail-functions.php".URL_APPEND."&userck=$userck";
$imgpath=$root_path."pharma/img/";
$thisfile='seg-pharma-retail-new.php';

# Save data routine
# if ($mode=='save') include($root_path.'include/inc_retail_db_save_mod.php');
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
 $smarty->assign('sToolbarTitle',"$title::Retail::New purchase");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title::Retail::New purchase");

 # Assign Body Onload javascript code
 $onLoadJS='onLoad="';
 if($mode!='save'&&$mode!='update') 
 	$onLoadJS.='document.inputform.bestellnum.focus();';
 //if ($saveok)	$onLoadJS.="alert('$refno');xajax_populateDetails('$refno');";
 if ($saveok) $onLoadJS.='';
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
	echo '<script type="text/javascript" src="'.$root_path.'modules/pharmacy/js/retail-new-gui-functions.js"></script>'."\r\n";
	echo '<script type="text/javascript">
	var djConfig = { isDebug: true };
</script>
<script type="text/javascript" src="'.$root_path.'js/dojo/dojo.js"></script>
<script type="text/javascript">
	dojo.require("dojo.widget.TabContainer");
	dojo.require("dojo.widget.LinkPane");
	dojo.require("dojo.widget.ContentPane");
	dojo.require("dojo.widget.LayoutContainer");
	dojo.require("dojo.widget.Checkbox");
</script>

<style type="text/css">
body {
	font-family : sans-serif;
}
.dojoTabPaneWrapper {
  padding : 10px 10px 10px 10px;
}
</style>';
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
 
if ($mode=="deldetails") {
	if ($deleteok) $smarty->assign('sDeleteOK',"Transaction detail deleted."); 
	else $smarty->assign('sDeleteFailed',"Unable to delete transaction detail.");
}
else {
	if($saveok){
	//if ($senddetail) {
	//	$smarty->assign('sSaveFeedBack',"HAHAHAHAHA");
	//}
		if($update) $smarty->assign('sSaveFeedBack',"Update was successful.");
		else $smarty->assign('sSaveFeedBack',"Data was successfully saved.");
	}
}

if($error=="refno_exists"){
	$smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
	$smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}

if($update&&(!$updateok)&&($mode=='save')) $smarty->assign('sNoSave',$LDDataNoSaved.'<a href="'.$thisfile.URL_APPEND.'"><u>'.$LDClk2EnterNew.'</u></a>');

 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="post" name="inputform" onSubmit="return prufform(this)">');
 $smarty->assign('sFormEnd','</form>');

 # Assign form inputs (or values)

 //if ($saveok||$update) $smarty->assign('sOrderNrInput',$bestellnum.'</b><input type="hidden" name="bestellnum" value="'.$bestellnum.'">');
 //	else $smarty->assign('sOrderNrInput','<input type="text" name="bestellnum" value="'.$bestellnum.'" size=20 maxlength=20>');

if ($saveok){
	$smarty->assign('sReferenceNoInput',$refno . '<input type="hidden" name="refno" size="20" maxlength="20" value="'.$refno.'"><input type="hidden" name="refnoex" id="refnoex" size="20" maxlength="20" value="'.$refno.'">');
	$smarty->assign('sPurchaseDateInput',$purchasedt.'<input type="hidden" name="purchasedt" id="purchase_date" size="20" value="'.$purchasedt.'">');
	$smarty->assign('sPayerNameInput',$pname . '<input type="hidden" id="payer_text" name="pname" size="20" readonly="1" value="'.$pname.'">');
	$smarty->assign('sPayerID','<input type="hidden" id="payer_id" name="pencnum"  value="'.$pencnum.'">');
	$smarty->assign('sIsCashCheckBox',($is_cash?"Cash":"Charge").'<input type="hidden" name="is_cash" value="'.($is_cash?1:0).'">');

}else{
	$smarty->assign('sReferenceNoInput','<input type="text" name="refno" size="20" maxlength="20" value="'.$refno.'"><input type="hidden" name="refnoex" id="refnoex" size="20" maxlength="20" value="'.$refnoex.'">');
	$smarty->assign('sPurchaseDateInput','<input type="text" name="purchasedt" id="purchase_date" size="20" maxlength="20" value="'.$purchasedt.'">');
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="purchase_date_trigger" align="absmiddle" style="cursor:pointer">');
	$smarty->assign('sPayerNameInput','<input type="text" id="payer_text" name="pname" size="20" readonly="1" value="'.$pname.'">'); # burn modified c/o alvin: August 10, 2006
	$smarty->assign('sPayerID','<input type="hidden" id="payer_id" name="pencnum"  value="'.$pencnum.'">');
	$smarty->assign('sPayerSelectButton',
		'<input type="button" id="payerselect" value="Select" onclick="window.open(\'' . $root_path."modules/pharmacy/seg-pharma-patient-select.php".URL_APPEND."&clear_ck_sid=$clear_ck_sid" . '\',\'patient_select\',\'width=890,height=640,menubar=no,resizable=yes,scrollbars=yes\')" style="margin-top:2px" />
<input type="button" id="clear" value="Clear" style="margin-top:2px" />');
	if (!isset($is_cash))
		$smarty->assign('sIsCashCheckBox','<input type="radio" name="is_cash" id="is_cash_0" value="1" checked> Cash&nbsp;&nbsp;<input type="radio" name="is_cash" id="is_cash_1" value="0"> Charge');
	else
		$smarty->assign('sIsCashCheckBox','<input type="radio" name="is_cash" id="is_cash_0" value="1" '.(($is_cash)?"checked":"").'> Cash&nbsp;&nbsp;<input type="radio" name="is_cash" id="is_cash_1" value="0" '.((!$is_cash)?"checked":"").'> Charge');

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

//$smarty->assign('sDebug', "saveok:'$saveok'");
if ($saveok) {
	# Display the transaction details
	$smarty->assign('sTransactionDetailsDivision','');
	
	//$smarty->assign('sTransactionDetailsControls', file_get_contents("seg-pharma-retail-new-rdetails.inc.php"));
	ob_start();
	include("seg-pharma-retail-new-rdetails.inc.php");
	include("seg-pharma-retail-new-discount.inc.php");
	$sDiscount = ob_get_contents();
	ob_end_clean();
	$smarty->assign('sDiscountControls', $sDiscount);
	//$smarty->assign('sDiscountControls', file_get_contents("seg-pharma-retail-new-discount.inc.php"));

	
	/*
	$rdetails = "
	<br/><img src=\"\" vspace=\"2\" width=\"1\" height=\"1\"><br/>
Search keyword 
<input type=\"text\" id=\"inputKeyword\" value=\"\" size=\"35\" onkeyup=\"prepareSendKeyword(this.value,300)\">
<input type=\"button\" value=\"Clear\" onclick=\"document.getElementById('inputKeyword').value='';\">
<br/><img src=\"\" vspace=\"1\" width=\"1\" height=\"2\"><br/>
<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%\">
	<tr>
		<td width=\"60%\" valign=\"top\">		
			<div style=\"width:100%;height:224px;overflow:hidden;border:1px solid black;\">
			<div style=\"width:100%;height:240px;overflow:scroll;border:1px solid black\">
			<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" id=\"srcRowsTable\">
				<thead>
					<tr class=\"reg_list_titlebar\" style=\"font-weight:bold \" id=\"srcRowsHeader\">
						<th width=\"20%\" nowrap>&nbsp;No</th>
						<th width=\"45%\" nowrap>Product name</th>
						<th width=\"15%\">&nbsp;Price</th>
						<th width=\"10%\">&nbsp;Qty</th>
						<th width=\"10%\">&nbsp;Add</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			</div></div>
		</td>
		<td width=\"5\">&nbsp;&nbsp;</td>
		<td bgcolor=\"666666\">&nbsp;</td>
		<td width=\"5\">&nbsp;&nbsp;</td>
		<td valign=\"top\">
			<div style=\"width:100%;height:224px;overflow:hidden;border:1px solid black;\">
			<div style=\"width:100%;height:240px;overflow:scroll;border:1px solid black\">
			<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" id=\"destRowsTable\">
				<tr class=\"reg_list_titlebar\" style=\"font-weight:bold\">
					<td width=\"15%\" nowrap>&nbsp;No</td>
					<td width=\"35%\" nowrap>
						Product name
					</td>
					<td width=\"5%\">&nbsp;Qty</td>
					<td width=\"5%\">&nbsp;Rmv</td>
				</tr>
			</table>
			</div></div>
		</td>
	</tr>
</table>"; */
	/*
	$smarty->assign('sTransactionControls',"
	<table style=\"border:1px solid black;margin:5px 0px;\" width=\"100%\">
		<tr class=\"reg_list_titlebar\">
			<td colspan=\"5\"><b>Add transaction detail</b></td>
		</tr>
		<tr class=\"wardlistrow1\">
			<td nowrap style=\"	\">
				<input type=\"text\" name=\"prodname\" id=\"product_name\" size=\"20\" value=\"\" readonly=\"1\">
				<input type=\"hidden\" name=\"prodid\" id=\"product_id\" value=\"\">
				<!-- <input type=\"button\" id=\"select_product\" value=\"Select product\"> -->
				<input type=\"button\" id=\"select_product\" value=\"Select product\" onclick=\"window.open('".$root_path."modules/pharmacy/seg-retail-product-search.php".URL_APPEND."&clear_ck_sid=$clear_ck_sid" . "','patient_select','width=890,height=640,menubar=no,resizable=yes,scrollbars=yes')\">
			</td>
			<td nowrap>
				&nbsp;&nbsp;Qty <input type=\"text\" name=\"prodqty\" id=\"qty_text\" value=\"\" size=\"3\">
			</td>
			<td nowrap>
				&nbsp;&nbsp;Price per pack <input type=\"text\" name=\"prodppk\" id=\"prodppk_text\" value=\"\" size=\"3\">
			</td>
			<td nowrap>
				&nbsp;&nbsp;Package unit <input type=\"text\" name=\"produnit\" id=\"produnit_text\" value=\"\" size=\"5\">
			</td>
			<td width=\"50%\" align=\"right\">
				<input type=\"hidden\" name=\"senddetail\" id=\"idsenddetail\" value=\"\">
				<img ".createLDImgSrc($root_path,'send.gif','0')." border=\"0\" style=\"cursor:pointer\" onclick=\"if (validateDetailsSubmit(document.forms[0])) {changeMode('savedetails');document.forms[0].submit();}\">
			</td>
		</tr>
	</table>
");
	$smarty->assign('sShowTransactions',$rdetails);
	*/
}
else {
}
	$smarty->assign('sHiddenInputs',$sTemp);
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','left').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
	if (!$saveok) 
		$smarty->assign('sContinueButton','<input type="image" '.createLDImgSrc($root_path,'continue.gif','0','left').' align="absmiddle">');
	else
		$smarty->assign('sTailScripts', '<script language="javascript">xajax_populateDetails("'.$refno.'");xajax_populateDiscountSelection();xajax_populateRetailDiscounts("'.$refno.'");</script>');

	# Assign the form template to mainframe
	$smarty->assign('sMainBlockIncludeFile','retail/form.tpl');
	$smarty->display('common/mainframe.tpl');
?>