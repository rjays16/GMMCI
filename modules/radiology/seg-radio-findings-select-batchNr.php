<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
#require($root_path."modules/pharmacy/ajax/order-psearch.common.php");
require_once($root_path.'modules/radiology/ajax/radio-undone-request.common.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','products.php');
#$local_user='ck_prod_db_user';
$local_user='ck_radio_user';   # burn added : November 28, 2007
	$lang_tables[] = 'departments.php';
	define('LANG_FILE','konsil.php');
	define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

#echo "seg-radio-schedule-select-batchNr.php : _GET : <br> \n"; print_r($_GET);echo "<br> \n";
$thisfile=basename(__FILE__);

if (isset($_GET['refno']) && $_GET['refno']){
	$refno = $_GET['refno'];
}
if (isset($_GET['batch_nr']) && $_GET['batch_nr']){
	$batch_nr = $_GET['batch_nr'];
}
if (isset($_GET['pid']) && $_GET['pid']){
	$pid = $_GET['pid'];
}

 #echo "seg-radio-findings-select-batchNr.php : _GET : <br> \n"; print_r($_GET); echo" <br> \n";
 #echo "seg-radio-findings-select-batchNr.php : batch_nr = '".$batch_nr."' <br> \n";
 #echo "seg-radio-findings-select-batchNr.php : pid = '".$pid."' <br> \n";
 #echo "seg-radio-findings-select-batchNr.php : refno = '".$refno."' <br> \n";
# Create radiology object
 require_once($root_path.'include/care_api_classes/class_radiology.php');
 $obj_radio = new SegRadio;

# $recordByRefNo = $obj_radio->getScheduledRadioRequestInfo('','',$sub_dept_nr,date("m/d/Y",mktime(0, 0, 0, $pmonth, $pday, $pyear)));
#($ref_nr='', $batch_nr='', $sub_dept_nr='')
 #edited by VAN 03-04-08
 #$recordByRefNo = $obj_radio->getAllRadioInfoByRefNo($refno,$sub_dept_nr);
 $recordByRefNo = $obj_radio->getAllRadioInfoByRefNo($refno,$batch_nr,$sub_dept_nr);
#echo "sql = ".$obj_radio->sql;
/*
 echo "seg-radio-findings-select-batchNr.php : recordByRefNo : <br> \n"; print_r($recordByRefNo); echo" <br> \n";
 echo "seg-radio-findings-select-batchNr.php : recordByRefNo = '".$recordByRefNo."' <br> \n";
 echo "seg-radio-findings-select-batchNr.php : obj_radio->sql = '".$obj_radio->sql."' <br> \n";
 echo "seg-radio-findings-select-batchNr.php : refno = '".$refno."' <br> \n";
//exit();
*/

if (is_object($recordByRefNo)){
	$sScheduledForTheDay = '';
	$myCount=1;
	while($batchNrInfo=$recordByRefNo->FetchRow()){
#echo "seg-radio-findings-select-batchNr.php : batchNrInfo : <br> \n"; print_r($batchNrInfo); echo" <br> \n";
		$sub_dept_name = $batchNrInfo['sub_dept_name'];
			# FORMATTING of Date Requested
		$request_date = $batchNrInfo['request_date'];
		if (($request_date!='0000-00-00 00:00:00')  && ($request_date!=""))
			$request_date = @formatDate2Local($request_date,$date_format);
		else
			$request_date='';

		$patient_name=$batchNrInfo['name_last'].', '.$batchNrInfo['name_first'];
		if (!empty($batchNrInfo['name_middle'])){
			$patient_name .= ' <font style="font-style:italic; color:#FF0000">'.$batchNrInfo['name_middle'].'</font>';
		}
		$add_options = "";
		if ($batch_nr==$batchNrInfo['batch_nr']){
			$add_options = "checked disabled";
		}
# echo "seg-radio-findings-select-batchNr.php : batch_nr = '".$batch_nr."' <br> \n";
		$sScheduledForTheDay .='
			<tr>
				<td align="center">'.
				'	<input id='.$batchNrInfo['batch_nr'].' name="chk[\''.$batchNrInfo['batch_nr'].'\']" type="checkbox" onclick="$(\'selectedcount\').innerHTML=countSelected(\'batchNr-list\')" '.$add_options.'>'.
				'</td>
				<td align="left">'.$batchNrInfo['batch_nr'].'</td>
				<td align="left">'.$batchNrInfo['service_code'].'</td>
				<td align="left">'.$request_date.'</td>
				<td align="left">'.$batchNrInfo['request_doctor_name'].'</td>
				<td align="left">'.$batchNrInfo['request_status'].'</td>				
			</tr>'."\n";
		#echo "batchNrInfo : <br>\n "; print_r($batchNrInfo); echo"<br>\n";
	}
}# end of if stmt 'if (is_object($recordByRefNo))'
else{
	$sScheduledForTheDay='
			<tr>
				<td align="left" colspan="6">No request scheduled for the day</td>
			</tr>';
}

/*
 echo "seg-radio-findings-select-batchNr.php : refno = '".$refno."' <br> \n";
	$sScheduledForTheDay = '';
	while($batchNrInfo=$recordByRefNo->FetchRow()){
#		$sub_dept_name = $batchNrInfo['sub_dept_name'];
			# FORMATTING of Date Requested
		$request_date = $batchNrInfo['request_date'];
		if (($request_date!='0000-00-00 00:00:00')  && ($request_date!=""))
			$request_date = @formatDate2Local($request_date,$date_format);
		else
			$request_date='';
		$patient_name=$batchNrInfo['name_last'].', '.$batchNrInfo['name_first'];
		if (!empty($batchNrInfo['name_middle'])){
			$patient_name .= ' <font style="font-style:italic; color:#FF0000">'.$batchNrInfo['name_middle'].'</font>';
		}
	$sScheduledForTheDay .='
		<tr>
			<td align="right">'.
			'	<input id='.$result['batch_nr'].' name="chk["'.$result['batch_nr'].'"]" type="checkbox" onclick="$(\'selectedcount\').innerHTML=countSelected(\'batchNr-list\')">'.
			'</td>
			<td align="left">'.$batchNrInfo['batch_nr'].'</td>
			<td align="left">'.$batchNrInfo['service_code'].'</td>
			<td align="right">'.$request_date.'</td>
			<td align="right">'.$batchNrInfo['request_doctor_name'].'</td>
		</tr>'."\n";
		#echo "batchNrInfo : <br>\n "; print_r($batchNrInfo); echo"<br>\n";
	}
*/

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# $smarty->assign('bHideTitleBar',TRUE);
# $smarty->assign('bHideCopyright',TRUE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle', "Radiology :: ".$sub_dept_name." :: Findings :: ".$patient_name);

 # Window bar title
 $smarty->assign('sWindowTitle',"Radiology :: ".$sub_dept_name." :: Findings :: ".$patient_name);

 # href for the close button 
 $smarty->assign('breakfile','javascript:window.parent.pSearchClose();');

# href for the back button
 $smarty->assign('pbBack','');

 # href for the help button
# $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");
 $smarty->assign('pbHelp',"");

/*
 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$title $LDPharmaDb $LDSearch");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title $LDPharmaDb $LDSearch");

 # Assign Body Onload javascript code
# $smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');
*/
 # Collect javascript code
 ob_start()
?>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<?php
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

$sPanelHeaderBatchNrList = "List of Requests with Reference No. ".$refno;

# Buffer page output

ob_start();
?>

	<div align="left">
		Selected: <span id="selectedcount">1</span>
	</div>
	<table border="0" id="batchNr-list" cellspacing="2" cellpadding="2" width="100%" align="left" class="segList">
		<thead>
			<tr>
				<td class="segPanelHeader" align="left" colspan="6"> <?=$sPanelHeaderBatchNrList?></td>
			</tr>
			<tr class="segPanel" style=" font-weight:bold;">
				<td align="center" width="2%">
					<input id="chkall" type="checkbox" onClick="checkAll('batchNr-list',this.checked);$('selectedcount').innerHTML=countSelected('batchNr-list');">				
				</td>
				<td align="center" width="12%">Batch No.</td>
				<td align="center" width="20%">Service Code</td>
				<td align="center" width="20%">Date Requested</td>
				<td align="center" width="*">Requesting Doctor</td>
				<td align="center" width="10%">Status</td>
			</tr>
		</thead>
		<tbody>
			<?=$sScheduledForTheDay?>
		</tbody>
	</table>
<br clear="left">
<br clear="left">
<script language="javascript" >

		function checkAll(parent, flag) {
			var p=$(parent);
			var cList=p.getElementsByTagName('input');		
			for (var i=0;i<cList.length;i++) {
				if (cList[i].type=="checkbox")
					cList[i].checked=flag;
				if (cList[i].id==$F('batch_nr'))
					cList[i].checked=true;
			}
		}
	
		function countSelected(parent) {
			var count=0;
			var p=$(parent);
			var cList=p.getElementsByTagName('input');		
			for (var i=0;i<cList.length;i++) {
				if (cList[i].type=="checkbox") {
					if (cList[i].checked&&cList[i].id!='chkall') count++;
				}
			}
			return count;
		}
	
		function getCheck(parent) {
			var p=$(parent);
			var check_grp = new Array();
			//alert("parent = "+parent);
			//alert("$(parent) = "+$(parent));
			
			var cList=p.getElementsByTagName('input');		
			for (var i=0;i<cList.length;i++) {
				if (cList[i].type=="checkbox") {
					if (cList[i].checked&&cList[i].id!='chkall') {
						check_grp.push(cList[i].id);
					}
				}
			}
			return check_grp;
		}

	function printRadioReport(){
		
		var w=window.screen.width;
		var h=window.screen.height;
		var ww=500;
		var wh=500;
		var rpath=$F('rpath');
		var pid=$F('pid');
		var batch_nr=$F('batch_nr');
		var seg_URL_APPEND=$F('seg_URL_APPEND');
		var refno=$F('refno');
		var batch_nr_grp = new Array();

		batch_nr_grp = getCheck('batchNr-list');
		//alert('pid, batch, refno = '+pid+" , "+batch_nr+" , "+refno);
//		alert("batch_nr_grp = '"+batch_nr_grp+"'");
//		return;
		urlholder=rpath+"modules/radiology/certificates/seg-radio-report-pdf.php"+seg_URL_APPEND+"&pid="+pid+"&batch_nr_grp="+batch_nr_grp;

		if (window.showModalDialog){  //for IE
			window.showModalDialog(urlholder,"width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
		}else{
//			window.open("createCampus.php?i="+id,"createCampus","modal, width=480,height=320,menubar=no,resizable=no,scrollbars=no");
			popWindowEditFinding=window.open(urlholder,"Print Report","width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
			window.popWindowEditFinding.moveTo((w/2)+80,(h/2)-(wh/2));
		}
		window.parent.pSearchClose();
	}
</script>

	<img <?=createLDImgSrc($root_path,'viewpdf.gif','0','center')?> alt="View PDF" onClick="printRadioReport();" style="cursor:pointer">
	
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">

	<input type="hidden" name="thisfile" id="thisfile" value="<?php echo $thisfile; ?>">
	<input type="hidden" name="rpath" id="rpath" value="<?php echo $root_path; ?>">
	<input type="hidden" name="pgx" id="pgx" value="<?php echo $pgx; ?>">
	<input type="hidden" name="oitem" id="oitem" value="<?= $oitem? $oitem:'create_dt' ?>">
	<input type="hidden" name="odir" id="odir" value="<?= $odir? $odir:'ASC' ?>">
	<input type="hidden" name="sub_dept_nr" id="sub_dept_nr" value="<?php echo $sub_dept_nr; ?>">

	<input type="hidden" name="batch_nr" id="batch_nr" value="<?php echo $batch_nr ?>">
	<input type="hidden" name="pid" id="pid" value="<?php echo $pid ?>">
	<input type="hidden" name="refno" id="refno" value="<?php echo $refno ?>">
	<input type="hidden" name="seg_URL_APPEND" id="seg_URL_APPEND" value="<?php echo URL_APPEND ?>">

<?php

# Workaround to force display of results  form
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

<form action="<?php echo $breakfile?>" method="post">
	<input type="hidden" name="sid" value="<?php echo $sid ?>">
	<input type="hidden" name="lang" value="<?php echo $lang ?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
</form>
<?php if ($from=="multiple")
echo '
<form name=backbut onSubmit="return false">
<input type="hidden" name="sid" value="'.$sid.'">
<input type="hidden" name="lang" value="'.$lang.'">
<input type="hidden" name="userck" value="'.$userck.'">
</form>
';
?>
</div>
<?php


$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>