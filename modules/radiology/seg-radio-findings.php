<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System version deployment 1.1 (mysql) 2004-01-11
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* , elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/

/* Start initializations */
$lang_tables[]='departments.php';
define('LANG_FILE','konsil.php');
define('NO_2LEVEL_CHK',1);

$local_user='ck_radio_user';   # burn added : September 24, 2007
$breakfile=$root_path.'modules/radiology/radiolog.php'.URL_APPEND;   # burn commented : November 16, 2007

require_once($root_path.'include/inc_front_chain_lang.php'); ///* invoke the script lock*/

require_once($root_path.'global_conf/inc_global_address.php');

require($root_path.'modules/radiology/ajax/radio-finding.common.php');

$thisfile= basename(__FILE__);

$bgc1='#ffffff'; /* The main background color of the form */
$edit_form=0; /* Set form to non-editable*/
$read_form=1; /* Set form to read */
$edit=0; /* Set script mode to no edit*/

$formtitle=$LDRadiology;

//$db_request_table=$subtarget;
$db_request_table='radio';

//$db->debug=1;

/* Here begins the real work */
require_once($root_path.'include/inc_date_format_functions.php');

# Create personell object
include_once($root_path.'include/care_api_classes/class_personell.php');
$personell_obj=new Personell;

# Create radiology object
require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj = new SegRadio;

if(!isset($mode)) $mode='';

switch($mode){

	case 'referral':
				# the 'service_date' is optional to be filled-up when the mode is referral
			if (trim($_POST['service_date'])!=""){
				$new_service_date = formatDate2STD($_POST['service_date'], $date_format);
				if ($radio_obj->updateRadioRequestServiceDate($_POST['batch_nr'],$new_service_date)){
					$errorMsg='<font style="color:#FF0000">Successfully updated!</font>';
				}else{
					$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
				}
			}
			if (!$radio_obj->updateRadioRequestStatus($_POST['batch_nr'], $_POST['status'])){
				$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
			}
		break;
	case 'save':
				# already assumed that when the mode is save, the 'service_date' is already filled-up
			$new_service_date = formatDate2STD($_POST['service_date'], $date_format);
#echo "seg-radio-findings.php : _POST['service_date'] = '".$_POST['service_date']."' <br> \n";
			if ($radio_obj->updateRadioRequestServiceDate($_POST['batch_nr'],$new_service_date)){
					$errorMsg='<font style="color:#FF0000">Successfully updated!</font>';
			}else{
					$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
			}
			if($_POST['status']=='done'){
				if (!$radio_obj->updateRadioRequestStatus($_POST['batch_nr'], $_POST['status'])){
					$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
				}
			}
		break;
	case 'delete':
#echo "seg-radio-findings.php : delete <br> \n";

			if ($radio_obj->deleteAFinding($_POST['batch_nr'],$_POST['finding_nr'])){
					$errorMsg='<font style="color:#FF0000">Successfully deleted a finding!</font>';
#	     		$errorMsg.="<br> \n radio_obj->sql = '".$radio_obj->sql."' <br> \n";
			}else{
					$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
			}
		break;
	case 'update':   # need to be deleted [Aug 22, 2007]
	{
		# Create a core object
		include_once($root_path.'include/inc_front_chain_lang.php');
		$core = & new Core;

		$sql="UPDATE care_test_request_".$db_request_table." SET
											xray_nr='".$xray_nr."',
											r_cm_2='".$r_cm_2."',
											mtr='".$mtr."',
																					xray_date='".formatDate2Std($xray_date,$date_format)."',
											results='".addslashes(htmlspecialchars($results))."',
																					results_date='".formatDate2Std($results_date,$date_format)."',
											results_doctor='".htmlspecialchars($results_doctor)."',
											status='received',
											history=".$core->ConcatHistory("Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n").",
											modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
											modify_time='".date('YmdHis')."'
					WHERE batch_nr = '".$batch_nr."'";

		if($ergebnis=$core->Transact($sql)){
			//echo $sql;
			header("location:".$thisfile."?sid=$sid&lang=$lang&edit=$edit&saved=update&pn=$pn&station=$station&user_origin=$user_origin&status=$status&target=$target&subtarget=$subtarget&batch_nr=$batch_nr&noresize=$noresize");
			exit;
		} else {
			echo "<p>$sql<p>$LDDbNoSave";
			$mode='';
		}
		break; // end of case 'save'
	}
	default: $mode='';
}// end of switch($mode)

$radioRequestInfo = $radio_obj->getAllRadioInfoByBatch($batch_nr);

if ($radioRequestInfo){
	#unserialized
	$findings_array = unserialize($radioRequestInfo['findings']);
	$findings_date_array = unserialize($radioRequestInfo['findings_date']);
	$doctor_in_charge_array = unserialize($radioRequestInfo['doctor_in_charge']);
	$radio_impression_array  = unserialize($radioRequestInfo['radio_impression']);
#echo "radiology/seg-radio-findings.php : findings_array : "; print_r($findings_array); echo " <br> \n";
}else{
		# no data retrieved

		# place some error message to the user
		# then, redirect to the main menu of radiology
			header("location:".$breakfile);
			exit;
}

$batchrows = $radio_obj->count;
$pn = $radioRequestInfo['encounter_nr'];
$refno = $radioRequestInfo['refno'];

/* Check for the patient number = $pn. If available get the patients data */
# used in generatin the bar code [burn]
#if($batchrows && $pn){
#edited by VAN 07-10-08
if($batchrows && ($pn || $pid)){
	include_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;
	if( $enc_obj->loadEncounterData($pn)) {
#echo "enc = ".$pn;
		include_once($root_path.'include/care_api_classes/class_globalconfig.php');
		$GLOBAL_CONFIG=array();
		$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('patient_%');
		switch ($enc_obj->EncounterClass())
		{
			case '1': $full_en = ($pn + $GLOBAL_CONFIG['patient_inpatient_nr_adder']);
											 break;
			case '2': $full_en = ($pn + $GLOBAL_CONFIG['patient_outpatient_nr_adder']);
							break;
			default: $full_en = ($pn + $GLOBAL_CONFIG['patient_inpatient_nr_adder']);
		}

		if( $enc_obj->is_loaded){
			$result=&$enc_obj->encounter;

			$sql="SELECT * FROM care_test_request_".$db_request_table." WHERE batch_nr='".$batch_nr."'";
			if($ergebnis=$db->Execute($sql)){
				if($editable_rows=$ergebnis->RecordCount()){
					$stored_request=$ergebnis->FetchRow();
					$edit_form=1;
				}
			}else{
				echo "<p>$sql<p>$LDDbNoRead";
			}
		}
	}else{
		$mode='';
		$pn='';
	}
}

# Create department object
include_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

if ($radioRequestInfo['if_in_house']=='1'){
		# get the department where the requesting doctor belongs
	$requestDocInfo = $dept_obj->getDeptofDoctor($radioRequestInfo['request_doctor']);
}


# Create person object
include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj = new Person($pid);   # 'pid' will be passed as parameter aside from batch_nr
# Create address object
include_once($root_path.'include/care_api_classes/class_address.php');
$address_brgy = new Address('barangay');

if($pid){
    #added by daryl
    $pid_walk_ = $radio_obj->selectifwalkin($pid);
    $pid_walk  = $pid_walk_->FetchRow();
	if(!($basicInfo = $person_obj->getAllInfoArray($pid))){
        if (($pid_walk['ifwalk']) != 1){
		echo '<em class="warn">Sorry but the page cannot be displayed!</em> <br>';
		exit();
	}
    }
	extract($basicInfo);
	$brgy_info = $address_brgy->getAddressInfo($brgy_nr,TRUE);
	if($brgy_info){
		$brgy_row = $brgy_info->FetchRow();
	}
}else{
	echo '<em class="warn"> Sorry but the page cannot be displayed! <br> Invalid HRN!</em>';
	exit();
}


# Prepare title
#$sTitle = $LDPendingTestRequest;   # burn commented ; September 19, 2007
$sTitle = "Radiology::Findings";
if($batchrows) $sTitle = $sTitle." (".$batch_nr.")";

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('nursing');

# Title in toolbar
 $smarty->assign('sToolbarTitle',$sTitle);

	# hide back button
 $smarty->assign('pbBack',FALSE);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('pending_radio.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',$sTitle);

$smarty->assign('sOnLoadJs','onLoad="if (window.focus) window.focus();"');

 # Collect extra javascript code

 ob_start();
?>

<style type="text/css">
div.fva2_ml10 {font-family: verdana,arial; font-size: 12; margin-left: 10;}
div.fa2_ml10 {font-family: arial; font-size: 12; margin-left: 10;}
div.fva2_ml3 {font-family: verdana; font-size: 12; margin-left: 3; }
div.fa2_ml3 {font-family: arial; font-size: 12; margin-left: 3; }
.fva2_ml10 {font-family: verdana,arial; font-size: 12; margin-left: 10; color:#000000;}
.fva2b_ml10 {font-family: verdana,arial; font-size: 12; margin-left: 10; color:#000000;}
.fva0_ml10 {font-family: verdana,arial; font-size: 10; margin-left: 10; color:#000000;}
</style>

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

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg', CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<script language="javascript">
<!--

function printOut()
{
	urlholder="<?php echo $root_path; ?>modules/laboratory/labor_test_request_printpop.php?sid=<?php echo $sid ?>&lang=<?php echo $lang ?>&user_origin=<?php echo $user_origin ?>&subtarget=<?php echo $subtarget ?>&batch_nr=<?php echo $batch_nr ?>&pn=<?php echo $pn; ?>";
	testprintout<?php echo $sid ?>=window.open(urlholder,"testprintout<?php echo $sid ?>","width=800,height=600,menubar=no,resizable=yes,scrollbars=yes");
		//testprintout<?php echo $sid ?>.print();
}

//-->

<?php
	require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>

<?php
	$xajax->printJavascript($root_path.'classes/xajax');

	echo '<script type="text/javascript" language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\n";

	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\n";
/*
	echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
*/
	echo '<script language="javascript" src="'.$root_path.'js/setdatetime.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/checkdate.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/dtpick_care2x.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\n";
?>


<?php

$sTemp = ob_get_contents();

ob_end_clean();

$smarty->append('JavaScript',$sTemp);

ob_start();

if($batchrows){

?>

<form name="form_test_findings" id="form_test_findings" method="post" action="<?php echo $thisfile ?>" onSubmit="return false;">
<!--
	<input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0') ?>  title="<?php echo $LDSaveEntry ?>">
	<a href="javascript:printOut()"><img <?php echo createLDImgSrc($root_path,'printout.gif','0') ?> alt="<?php echo $LDPrintOut ?>"></a>
	<a href="<?php echo 'labor_test_findings_'.$subtarget.'.php?sid='.$sid.'&lang='.$lang.'&batch_nr='.$batch_nr.'&pn='.$pn.'&entry_date='.$stored_request['xray_date'].'&target='.$target.'&subtarget='.$subtarget.'&user_origin='.$user_origin.'&tracker='.$tracker; ?>"><img <?php echo createLDImgSrc($root_path,'enter_result.gif','0') ?> alt="<?php echo $LDEnterResult ?>"></a>
-->
	<table border=0 bgcolor="#000000" cellpadding=1 cellspacing=0 width="800">
		<tr>
			<td colspan="*" align="center" bgcolor="#ffffff" >
				<?= $errorMsg ?>
			</td>
		</tr>
		<tr>
			<td colspan="*" align="left" bgcolor="#ffffff" >
				<input type="image" name="saveButton1" id="saveButton1" <?= "style=\"display:none\"" ?><?php echo createLDImgSrc($root_path,'savedisc.gif','0') ?>  title="<?php echo $LDSaveEntry ?>" onClick="$('save').value=1; saveOnly();">
			</td>
		</tr>
		<tr>
			<td>
				<table border=0 bgcolor="#ffffff" cellpadding=0 cellspacing=0 width="100%">
					<tr>
						<td>
							<table   cellpadding=0 cellspacing=1 border=0 width="100%">
								<tr  valign="top">
									<td  bgcolor="<?php echo $bgc1 ?>" rowspan=2>
<?php
if($edit || $read_form){
// echo "<br>pid = ".$pid;
	echo '										';
	echo '<img src="'.$root_path.'main/imgcreator/barcode_label_single_large.php?sid='.$sid.'&lang='.$lang.'&fen='.$full_en.'&pid='.$pid.'&en='.$pn.'" width=282 height=178>';
}

?>
									</td>
									<td bgcolor="<?php echo $bgc1 ?>" class=fva2_ml10>
										<div class=fva2_ml10>
											<font size=5 color="#0000ff"><b></b></font>
											<br>
											<?php
													#echo $global_address[$subtarget].'<br>'.$LDTel.'&nbsp;'.$global_phone[$subtarget];
													echo $global_address[$subtarget].'<br>'.$LDTel.'&nbsp; (082) 227-2731 loc. 4501';

											?>
									</td>
								</tr>
								<tr>
									<td bgcolor="<?php echo $bgc1 ?>" align="right" valign="bottom">
<?php
	echo '										';
	echo '<font size=1 color="#990000" face="verdana,arial">'.$batch_nr.'</font>&nbsp;&nbsp;<br>';
	echo "\n";
	echo '										';
	echo "<img src='".$root_path."classes/barcode/image.php?code=".$batch_nr."&style=68&type=I25&width=145&height=40&xres=2&font=5' border=0>";
?>
									</td>
								</tr>
								<tr bgcolor="<?php echo $bgc1 ?>">
									<td  valign="top" colspan=2 >&nbsp;  </td>
								</tr>
								<tr bgcolor="<?php echo $bgc1 ?>">
									<td colspan=2>
										<div class=fva2_ml10><?php echo $LDClinicalInfo ?>:
										<p>
											<img src="../../gui/img/common/default/pixel.gif" border=0 width=20 height=45 align="left">
											<font face="courier" size=2 color="#000099">&nbsp;&nbsp;<?php echo stripslashes($radioRequestInfo['clinical_info']) ?></font>
									</td>
								</tr>
								<tr bgcolor="<?php echo $bgc1 ?>">
									<td colspan=2>
										<div class=fva2_ml10><?php echo $LDReqTest ?>:
										<p>
											<img src="../../gui/img/common/default/pixel.gif" border=0 width=20 height=45 align="left">
											<font face="courier" size=2 color="#000099">&nbsp;&nbsp;<?php echo stripslashes($radioRequestInfo['service_code']." : ".$radioRequestInfo['service_name']) ?></font>
									</td>
								</tr>
								<tr bgcolor="<?php echo $bgc1 ?>">
									<td colspan=2>
										<div class=fva2_ml10><?php echo $LDDate ?> Requested:
											<font face="courier" size=2 color="#000000">&nbsp;<?php echo formatDate2Local($radioRequestInfo['request_date'],$date_format); ?></font>
											&nbsp;
											<?php echo $LDRequestingDoc ?>:
											<font face="courier" size=2 color="#000000">&nbsp;<?php echo $radioRequestInfo['request_doctor_name'] ?></font>
										</div>
										<br>
									</td>
								</tr>
								<tr bgcolor="<?php echo $bgc1 ?>">
									<td colspan=2>
										<div class=fva2_ml10>&nbsp;<br>
											<font color="#FF0000"><b>*</b></font><font color="#000099">Date of Service</font>
											&nbsp;&nbsp;
<?php
	$phpfd=$date_format;
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

#echo "radiology/seg-radio-findings.php : 2 radioRequestInfo['service_date'] = '".$radioRequestInfo['service_date']."' <br> \n";

	$service_date = $radioRequestInfo['service_date'];

#echo "radiology/seg-radio-findings.php : service_date = '".$service_date."' <br> \n";

	if (($service_date!='0000-00-00 00:00:00')  && ($service_date!=""))
		$service_date = @formatDate2Local($service_date,$date_format);
	else
        $service_date = date("m/d/Y"); # added by mai 05-26-2014 

#echo "radiology/seg-radio-findings.php : 2 service_date = '".$service_date."' <br> \n";

						$sServiceDate= '<input name="service_date" type="text" size="15" maxlength=10 value="'.$service_date.'"'.
									'onFocus="this.select();"
									id = "service_date"
									onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
									onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
									onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
									<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="service_date_trigger" style="cursor:pointer" >
									<font size=3>[';
						ob_start();
					?>
					<script type="text/javascript">
						Calendar.setup ({
								inputField : "service_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "service_date_trigger", singleClick : true, step : 1
						});
							</script>
																		<?php
						$calendarSetup = ob_get_contents();
						ob_end_clean();

						$sServiceDate .= $calendarSetup;
						/**/
						$dfbuffer="LD_".strtr($date_format,".-/","phs");
						$sServiceDate = $sServiceDate.$$dfbuffer.']';
?>
											<?= $sServiceDate ?>
										</div>
									</td>
								</tr>
								<tr bgcolor="<?php echo $bgc1 ?>">
									<td colspan=2>
										<div class=fva2_ml10>&nbsp;<br>

				<table cellspacing="0 "cellpadding="0" border="0" class="frame" width="100%">
					<tbody>
						<tr>
							<td style="color:white; background-color: red; font-weight:bold;">
								&nbsp;List of Findings
							</td>
						 </tr>
						 <tr>
							<td bgcolor="#ffffff">
								<table id="findings-list" class="segList" border="0" cellpadding="1" cellspacing="1" width="100%" style="border:1px solid #666666;border-bottom:0px">
									<thead>
										<!-- Column headings -->
										<!--added by VAN 07-11-08 -->

										<tr id="findings-list-header" class="reg_list_titlebar" style="font-weight:bold;padding:0px;" align="center">
											<td width="1%"><b> No. </b></td>
											<td width="18%"><b> Resident In-Charge </b></td>
											<td width="*"><b> Findings </b></td>
											<td width="1%"><b>&nbsp;</b></td>
											<td width="25%"><b> Impression </b></td>
											<!--<td width="5%"><b> Status </b></td>-->
											<td width="5%"><b> Date </b></td>

											<td width="3%"><b> Edit </b></td>
											<td width="3%"><b> Delete </b></td>
										</tr>
									</thead>
									<tbody>
										<!-- List of findings -->
									</tbody>
								</table>
								<table border="0" cellpadding="2" cellspacing="1" width="600" style="border:1px solid #666666;border-top:0px;margin-top:-1px;">
									<tr class="reg_list_titlebar">
									</tr>
								</table>
								<div align="center">
									<br>
									<a href="javascript:void(0);"
										onclick="var temp = $('count_find').value; return overlib(
											OLiframeContent('seg-radio-findings-edit.php<?= URL_APPEND ?>&batch_nr=<?=$batch_nr?>&refno=<?=$_GET['refno']?>&mode=save&findings_nr='+temp, 800, 450, 'if1', 1, 'auto'),
												WIDTH,600, TEXTPADDING,0, BORDER,0,
												STICKY, SCROLL, CLOSECLICK, MODAL,
												CLOSETEXT, '<img src=<?= $root_path ?>images/x.gif border=0 onClick=refreshWindow();>',
												CAPTIONPADDING,4, CAPTION,'Add Finding', MIDX,0, MIDY,0,
												STATUS,'Add Finding');"
										onmouseout="nd();">
										<input type="image" name="addButton" id="addButton" <?= createLDImgSrc($root_path,'add_finding_02.gif','0') ?>>
									</a>
										<input type="image" name="referralButton" id="referralButton" <?= "style=\"display:none\"" ?> <?php echo createLDImgSrc($root_path,'referral.gif','0') ?>  alt="Referral" onClick="$('save').value=1; referralHandler();">
								</div>
														</td>
							</td>
						</tr>
					</tbody>
				</table>
											<br>
										</div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<p>
<?php
#		if ($radioRequestInfo['status']!='done'){
?>
	<input type="image" name="saveButton2" id="saveButton2" <?= "style=\"display:none\"" ?><?php echo createLDImgSrc($root_path,'savedisc.gif','0') ?>  title="<?php echo $LDSaveEntry ?>" onClick="$('save').value=1; saveOnly();">
	<input type="image" name="saveDoneButton" id="saveDoneButton" <?= "style=\"display:none\"" ?> <?php echo createLDImgSrc($root_path,'save_done_02.gif','0') ?>  alt="Save&Done" onClick="$('save').value=1; saveAndDone();">
<?php
	$onClick="onClick= \"viewRadioReport('$pid', '$batch_nr');\"";

	$code = stripslashes($radioRequestInfo['service_code']);
?>
<!--
	<br>
	<input type="button" name="printReport" id="printReport" height="23" style="cursor:pointer;font:bold 12px Arial" <?= "style=\"display:none\"" ?> value="Print Report" onClick="printRadioReport();">
-->
	<br>
	<br>
	<img name="printReport" id="printReport" <?=createLDImgSrc($root_path,'viewpdf.gif','0','center')?> alt="View PDF" style="cursor:pointer" <?=$onClick?> >

<?php
	# added by VAN 01-14-08
	if ($radioRequestInfo['status']=='done'){
		$requestFileForward = $root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_test&user_origin=lab&repeat=1&prevbatchnr=".$batch_nr."&prevrefno=".$refno;

?>
	&nbsp;&nbsp;
	<a href="<?=$requestFileForward?>"><img name="repeatRequest" id="repeatRequest" <?=createLDImgSrc($root_path,'repeatrequest.gif','0','center')?> alt="Repeat Request" border=0></a>
<?php
	} #end of if ($radioRequestInfo['status']=='done')
	#---------------------

#		}# end of else-stmt 'if ($radioRequestInfo['status']!='done')'
?>
<?php
	# require($root_path.'include/inc_test_request_hiddenvars.php');
?>
	<input type="hidden" name="sid" value="<?php echo $sid ?>">
	<input type="hidden" name="lang" value="<?php echo $lang ?>">
	<input type="hidden" name="station" value="<?php echo $station ?>">
	<input type="hidden" name="dept" value="<?php echo $dept ?>">
<?php
if($target!='generic'){
?>
	<input type="hidden" name="dept_nr" value="<?php echo $dept_nr; ?>">
<?php
}
?>
	<input type="hidden" name="pn" value="<?php echo $pn ?>">
	<input type="hidden" name="edit" value="<?php echo $edit ?>">
	<input type="hidden" name="target" value="<?php echo $target ?>">
	<input type="hidden" name="subtarget" value="<?php echo $subtarget ?>">
	<input type="hidden" name="tracker" value="<?php echo $tracker ?>">
	<input type="hidden" name="noresize" value="<?php echo $noresize ?>">
	<input type="hidden" name="user_origin" value="<?php echo $user_origin ?>">

	<input type="hidden" name="sub_dept_nr" id="sub_dept_nr" value="<?php echo $radioRequestInfo['service_dept_nr'] ?>">
	<input type="hidden" name="batch_nr" id="batch_nr" value="<?php echo $batch_nr ?>">
	<input type="hidden" name="pid" id="pid" value="<?php echo $pid ?>">
	<input type="hidden" name="refno" id="refno" value="<?php echo $refno ?>">
	<input type="hidden" name="count_find" id="count_find" value="<?= $count_find? $count_find:0 ?>">
	<input type="hidden" name="finding_nr" id="finding_nr" value="">
	<input type="hidden" name="status" id="status" value="">
	<input type="hidden" name="rpath" id="rpath" value="<?php echo $root_path; ?>">
	<input type="hidden" name="seg_URL_APPEND" id="seg_URL_APPEND" value="<?php echo URL_APPEND ?>">
	<input type="hidden" name="mode" id="mode" value="save">
	<input type="hidden" name="save" id="save" value="0">

	<!--added by VAN 03-05-08 -->
	<input type="hidden" name="service_date2" id="service_date2" value="<?=$service_date;?>">

	<input type="hidden" name="formtitle" id="formtitle" value="<?php echo $formtitle; ?>">
</form>

<script language="javascript" src="js/radio-findings.js"></script>

<?php
}else{   # else-stmt of if-stmt 'if($batchrows)'
?>
	<img <?php echo createMascot($root_path,'mascot1_r.gif','0','bottom') ?> align="absmiddle"><font size=3 face="verdana,arial" color="#990000"><b><?php echo $LDNoPendingRequest ?></b></font>
	<p>
	<a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'back2.gif','0') ?>></a>
<?php
}
?>
	<script type="text/javascript" language="javascript">
///		preset(<?= ($is_cash=='0')? "0":"1"?>);
		xajax_populateRadioFinding(<?=$batch_nr? $batch_nr:0?>, "<?=$_GET['target']?>");
	</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

# Assign to page template object
$smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>