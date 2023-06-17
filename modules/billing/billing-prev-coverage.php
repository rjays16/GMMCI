<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/billing/ajax/bill-prev-coverage.common.php');

/* Define language and local user for this module */
$thisfile=basename(__FILE__);
$lang_tables[]='prompt.php';
define('LANG_FILE','aufnahme.php');
define('NO_2LEVEL_CHK',1);

require_once($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'include/care_api_classes/billing/class_hcare_benefit.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php');

$smarty = new smarty_care('common');

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

ob_start();
?>
<!-- prototype -->
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>/js/shortcut.js"></script>

<!-- YUI Library -->
<script type="text/javascript" src="<?=$root_path?>js/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/event/event.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dom/dom.js" ></script>

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

<script type="text/javascript">
<!--
 OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>
<script type="text/javascript" src="<?=$root_path?>js/datefuncs.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/gen_routines.js"></script>
<script type="text/javascript" src="js/bill-prev-coverage.js"></script>
<?php 
$xajax->printJavascript($root_path.'classes/xajax');
?>
<script>
	YAHOO.util.Event.addListener(window, "load", init);
</script>
<?php 
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

// Timestamp format of from reference date of billing.
$curTme  = strftime("%Y-%m-%d %H:%M:%S");
$frm_dte = (isset($_GET['frmdte']) ? $_GET['frmdte'] : $curTme);
$curDate = strftime("%b %d, %Y %I:%M%p", strtotime($frm_dte));

// Encounter No.
$enc_nr  = $_GET['nr'];	
$mode    = $_GET['mode'];

if ($_POST['save_clicked']) {		// Save button was clicked ...
	if (is_array($_POST['amntbox'])) {
		$flds = array();
		$ref_flds = array('disclose_id', 
						  'hcare_id');
	
		foreach ($_POST['amntbox'] as $v) { 		
			$flds['used_'.$v.'_'.(strcasecmp($v, 'days') == 0 ? 'covered' : 'coverage')] = $_POST[$v];
			$ref_flds[] = 'used_'.$v.'_'.(strcasecmp($v, 'days') == 0 ? 'covered' : 'coverage');
		}
		
		$hdr = array('disclose_dte'=>$_POST['postdate'],
					 'encounter_nr'=>$_POST['enc_nr'],
					 'modify_id'=>$_SESSION['sess_temp_userid'],
					 'create_id'=>$_SESSION['sess_temp_userid']);					 
					 
		$objb = new HealthCareBenefit();
		
		$objb->useHeaderTable();
		$objb->setDataArray($hdr); 
				
		if ($_POST['disclose_id'] != '') {			
			$objb->setWhereCondition("disclose_id = '".$_POST['disclose_id']."'");
			$bOk = $objb->updateDataFromInternalArray();
		}
		else {
			$bOk = $objb->insertDataFromInternalArray();			
		}
			
		if ($bOk) {										
			$d_id = $objb->getIDofUsedCoverageLogged($_POST['enc_nr'], $frm_dte);			
			$details = array('disclose_id'=>$d_id,
						     'hcare_id'=>$_POST['hcare_id']);							 
			$details = array_merge($details, $flds);
													 
			$objb->useDetailsTable();						
			$objb->setRefArray($ref_flds);
			$objb->setDataArray($details);		
			
			if (strcasecmp($_POST['mode'],'edit') == 0) {
				$objb->setWhereCondition("disclose_id = '".$_POST['disclose_id']."' and hcare_id = ".$_POST['old_hcare_id']." and entry_no = ".$_POST['entry_no']);
				$bOk = $objb->updateDataFromInternalArray();				
			}	 
			else			
				$bOk = $objb->insertDataFromInternalArray();
		}
		
		if (!bOk) $smarty->assign('sWarning',"<strong>Error:</strong> $objb->getErrorMsg()");
	}
}

if ($mode == '') {
	$objb = new HealthCareBenefit();
	$id = $objb->getIDofUsedCoverageLogged($enc_nr, $frm_dte);
	if ($id == '') $mode = 'add';
}
else
	$id = '';

$smarty->assign('sDate', '<span id="show_postdte" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.$curDate.'</span><input class="jedInput" name="postdate" id="postdate" type="hidden" value="'.strftime("%Y-%m-%d %H:%M:%S", strtotime($frm_dte)).'" style="font:bold 12px Arial">');
$smarty->assign('sCalendarIcon','<img '.($mode == '' ? 'style="visibility:hidden"' : '').' '. createComIcon($root_path,'show-calendar.gif','0') . ' id="postdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
$jsCalScript = "<script type=\"text/javascript\">
	Calendar.setup ({
		displayArea : \"show_postdte\",
		inputField : \"postdate\",
		ifFormat : \"%Y-%m-%d %H:%M:%S\", 
		daFormat : \"%b %d, %Y %I:%M%p\", 
		showsTime : true, 
		button : \"postdate_trigger\", 
		singleClick : true,
		step : 1
	});
</script>";
$smarty->assign('jsCalendarSetup', $jsCalScript);

$smarty->assign('sInsuranceCombo', '<select id="hcare_combo" style="font:bold 12px Arial" onchange="js_showCoverageDetails($(\'disclose_id\').value, this.options[this.selectedIndex].value, ($(\'mode\').value == \'\' ? 1 : 0))">
									<option value="0">- Select Health Insurance -</option>
								</select>');
								
$smarty->assign('sSaveButton', '<img id="btnSave" style="cursor:pointer" src="'.$root_path.'/images/btn_save.gif" border=0 >');
$smarty->assign('sCancelButton', '<img id="btnCancel" style="cursor:pointer" src="'.$root_path.'/gui/img/control/default/en/en_cancel.gif" border=0 >');
$smarty->assign('sAddButton', '<img id="btnAdd" style="cursor:pointer" src="'.$root_path.'/images/btn_add.gif" border=0 >');
$smarty->assign('sEditButton', '<img id="btnEdit" style="cursor:pointer" src="'.$root_path.'/images/btn_edit.gif" border=0 >');
$smarty->assign('sDelButton', '<img id="btnDelete" style="cursor:pointer" src="'.$root_path.'/images/btn_delete.gif" border=0 >');

# Assign Body Onload javascript code
$smarty->assign('sOnLoadJs','onLoad="fillInsuranceCombo(\''.$enc_nr.'\',\''.$frm_dte.'\','.($id == '' ? 0 : 1).');"');

$smarty->assign('sPrevLink', '<div id="prevRec" style="float:left; visibility:hidden" onclick="jumpToRec(PREV_REC)">
					<img style="cursor:pointer" title="Previous" src="'.$root_path.'/images/previous_off.gif" border="0" align="absmiddle"/>
					<span style="cursor:pointer" title="Previous"><b>Previous</b></span>
				</div>');
												
$smarty->assign('sNextLink','<div id="nextRec" style="float:left; visibility:hidden" onclick="jumpToRec(NEXT_REC)">
					<span style="cursor:pointer" title="Next"><b>Next</b></span>
					<img style="cursor:pointer" title="Next" src="'.$root_path.'/images/next_off.gif" border="0" align="absmiddle"/>
				</div>');
				
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&nr=".$enc_nr."&frmdte=".$frm_dte."&clear_ck_sid=".$clear_ck_sid.'" method="POST" id="coverage_form" name="coverage_form" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');				
	
ob_start();				
?>	
<input type="hidden" name="enc_nr" id="enc_nr" value="<?php echo $nr ?>">
<input type="hidden" name="disclose_dte" id="disclose_dte" value="<?php echo $frm_dte ?>">
<input type="hidden" name="mode" id="mode" value="<?php echo $mode ?>">
<input type="hidden" name="disclose_id" id="disclose_id" value="<?php echo $id ?>">
<input type="hidden" name="hcare_id" id="hcare_id" value="">	
<input type="hidden" name="old_hcare_id" id="old_hcare_id" value="">	
<input type="hidden" name="entry_no" id="entry_no" value="">
<input type="hidden" name="save_clicked" id="save_clicked" value="0">
<input type="hidden" name="add_clicked" id="add_clicked" value="<?= (isset($_GET['add_clicked']) ? $_GET['add_clicked'] : '0') ?>">
<input type="hidden" id="seg_URL_APPEND" name="seg_URL_APPEND" value="<?=URL_APPEND?>"  />
<?php
$stemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sHiddenInputs', $stemp); 	

//$smarty->assign('sMainFrameBlockData',$sTemp);
$smarty->assign('sMainBlockIncludeFile','billing/bill_prev_coverage.tpl');
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
