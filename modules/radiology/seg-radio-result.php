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

/* We need to differentiate from where the user is coming: 
*  $user_origin != lab ;  from patient charts folder
*  $user_origin == lab ;  from the laboratory
*  and set the user cookie name and break or return filename
*/
if($user_origin=='lab'){
	$local_user='ck_lab_user';
	$breakfile=$root_path.'modules/radiology/radiolog.php'.URL_APPEND;
}elseif($user_origin=='amb'){
	$local_user='ck_lab_user';
	$breakfile=$root_path.'modules/ambulatory/ambulatory.php'.URL_APPEND;
}else{
	$local_user='ck_pflege_user';
	$breakfile=$root_path."modules/nursing/nursing-station-patientdaten.php".URL_APPEND."&edit=$edit&station=$station&pn=$pn";
}

require_once($root_path.'include/inc_front_chain_lang.php'); ///* invoke the script lock*/

require_once($root_path.'global_conf/inc_global_address.php');

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

/*
PARAMETER needed : 
	[1] batch_nr (encounter_nr?)
	[2] row number

	(
		SELECT CONCAT(cp_2.name_first,' ', IF(TRIM(cp_2.name_middle)<>'',CONCAT(LEFT(cp_2.name_middle,1),'. '),''), cp_2.name_last) AS fullname
		FROM care_personell AS cpl_2, care_person AS cp_2
		WHERE cpl_2.nr = '100005' AND cp_2.pid=cpl_2.pid
		WHERE cpl_2.nr = r_request.request_doctor AND cp_2.pid=cpl_2.pid
	) AS request_doctor_name


SELECT enc.pid,
	IF((ISNULL(r_request.if_in_house) ||  r_request.if_in_house='0'),
		r_request.request_doctor,
		IF(STRCMP(r_request.request_doctor,CAST(r_request.request_doctor AS UNSIGNED INTEGER)),
			r_request.request_doctor,
			fn_get_personell_name(r_request.request_doctor))
	) AS request_doctor_name,
	r_request.request_doctor,
	r_request.batch_nr, r_request.encounter_nr, r_request.clinical_info, 
	r_request.service_code, r_request.service_date,	r_request.if_in_house, 
	r_request.request_date, r_request.status,
	r_request.encoder AS request_encoder,
	r_findings.findings, r_findings.findings_date, r_findings.doctor_in_charge,
	r_findings.encoder AS findings_encoder,
	r_services.name AS service_name, r_services.price_cash, r_services.price_charge,
	r_serv_group.group_code AS group_code, r_serv_group.name AS group_name, r_serv_group.other_name,
	dept.name_formal AS service_dept_name
FROM care_test_request_radio AS r_request
	LEFT JOIN care_encounter AS enc ON enc.encounter_nr = r_request.encounter_nr
	LEFT JOIN care_test_findings_radio AS r_findings ON r_request.batch_nr = r_findings.batch_nr
	LEFT JOIN seg_radio_services AS r_services ON r_request.service_code = r_services.service_code
		LEFT JOIN seg_radio_service_groups AS r_serv_group ON r_services.group_code = r_serv_group.group_code
			LEFT JOIN care_department AS dept ON r_serv_group.department_nr = dept.nr
WHERE r_request.batch_nr='2007000005'
*/  

if(!isset($mode))   $mode='';

switch($mode){
	case 'update':
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

# Create radiology object
require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj = new SegRadio;
$radioRequestInfo = $radio_obj->getAllRadioInfoByBatch($batch_nr);
$batchrows = $radio_obj->count;

# Create department object
include_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

if ($radioRequestInfo['if_in_house']=='1'){
		# get the department where the requesting doctor belongs
	$requestDocInfo = $dept_obj->getDeptofDoctor($radioRequestInfo['request_doctor']);
		# $requestDocInfo['nr'] , department number
		# $requestDocInfo['id'] , department id name
		# $requestDocInfo['name_formal'] , department full name
}

# Create person object
include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj = new Person($pid);   # 'pid' will be passed as parameter aside from batch_nr

if($pid){
	if(!($basicInfo = $person_obj->getAllInfoArray($pid))){
		echo '<em class="warn">Sorry but the page cannot be displayed!</em> <br>';
		echo "<em class='warn'> intval(pid) = '".intval($pid)."' </em> <br> \n";
		echo "<em class='warn'> person_obj->sql = '".$person_obj->sql."' </em> <br> \n";
		exit();
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
$sTitle = $LDPendingTestRequest;
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

<script language="javascript">
<!-- 

function chkForm(d)
{ 
	if(d.results.value=="" || d.results.value==" ") 
	{
	  return false;
	}
	else if(d.results_date.value=="" || d.results_date.value==" ")
	  {
	     alert('<?php echo $LDPlsEnterDate ?>');
		 d.results_date.focus();
		 return false;
	  }
	  else if(d.results_doctor.value=="" || d.results_doctor.value=="")
		{
	     alert('<?php echo $LDPlsEnterDoctorName ?>');
		 d.results_doctor.focus();
		   return false;
		}
		else return true; 
}

function printOut()
{
	urlholder="<?php echo $root_path; ?>modules/laboratory/labor_test_request_printpop.php?sid=<?php echo $sid ?>&lang=<?php echo $lang ?>&user_origin=<?php echo $user_origin ?>&subtarget=<?php echo $subtarget ?>&batch_nr=<?php echo $batch_nr ?>&pn=<?php echo $pn; ?>";
	testprintout<?php echo $sid ?>=window.open(urlholder,"testprintout<?php echo $sid ?>","width=800,height=600,menubar=no,resizable=yes,scrollbars=yes");
    //testprintout<?php echo $sid ?>.print();
}

<?php require($root_path.'include/inc_checkdate_lang.php'); ?>

//-->
</script>
<script language="javascript" src="<?php echo $root_path; ?>js/setdatetime.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/checkdate.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/dtpick_care2x.js"></script>
<?php

$sTemp = ob_get_contents();

ob_end_clean();

$smarty->append('JavaScript',$sTemp);

ob_start();

if($batchrows){

?>
<form name="form_test_request" method="post" action="<?php echo $thisfile ?>" onSubmit="return chkForm(this)">
	<input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0') ?>  title="<?php echo $LDSaveEntry ?>"> 
	<a href="javascript:printOut()"><img <?php echo createLDImgSrc($root_path,'printout.gif','0') ?> alt="<?php echo $LDPrintOut ?>"></a>
	<a href="<?php echo 'labor_test_findings_'.$subtarget.'.php?sid='.$sid.'&lang='.$lang.'&batch_nr='.$batch_nr.'&pn='.$pn.'&entry_date='.$stored_request['xray_date'].'&target='.$target.'&subtarget='.$subtarget.'&user_origin='.$user_origin.'&tracker='.$tracker; ?>"><img <?php echo createLDImgSrc($root_path,'enter_result.gif','0') ?> alt="<?php echo $LDEnterResult ?>"></a>

	<table border=0 bgcolor="#000000" cellpadding=1 cellspacing=0>
		<tr>
			<td>	
				<table border=0 bgcolor="#ffffff" cellpadding=0 cellspacing=0>
					<tr>
						<td>
							<table   cellpadding=0 cellspacing=1 border=0 width=700>
								<tr  valign="top">
									<td  bgcolor="<?php echo $bgc1 ?>" rowspan=2>
<?php
if($edit || $read_form){
	echo '										'; 
	echo '<img src="'.$root_path.'main/imgcreator/barcode_label_single_large.php?sid='.$sid.'&lang='.$lang.'&fen='.$full_en.'&en='.$pn.'" width=282 height=178>';
}
?>
									</td>
									<td bgcolor="<?php echo $bgc1 ?>" class=fva2_ml10>
										<div class=fva2_ml10>
											<font size=5 color="#0000ff"><b><?php echo $formtitle ?></b></font>
											<br>
											<?php echo $global_address[$subtarget].'<br>'.$LDTel.'&nbsp;'.$global_phone[$subtarget]; ?>
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
											<font face="courier" size=2 color="#000099">&nbsp;&nbsp;<?php echo stripslashes($stored_request['clinical_info']) ?></font>
									</td>
								</tr>	
								<tr bgcolor="<?php echo $bgc1 ?>">
									<td colspan=2>
										<div class=fva2_ml10><?php echo $LDReqTest ?>:
										<p>
											<img src="../../gui/img/common/default/pixel.gif" border=0 width=20 height=45 align="left">
											<font face="courier" size=2 color="#000099">&nbsp;&nbsp;<?php echo stripslashes($stored_request['test_request']) ?></font>
									</td>
								</tr>	
								<tr bgcolor="<?php echo $bgc1 ?>">
									<td colspan=2 align="right">
										<div class=fva2_ml10><?php echo $LDDate ?>:
											<font face="courier" size=2 color="#000000">&nbsp;<?php echo formatDate2Local($stored_request['send_date'],$date_format); ?></font>
											&nbsp;
											<?php echo $LDRequestingDoc ?>:
											<font face="courier" size=2 color="#000000">&nbsp;<?php echo $stored_request['send_doctor'] ?></font>
										</div>
										<br>
									</td>
								</tr>
								<tr bgcolor="<?php echo $bgc1 ?>">
									<td colspan=2> 
										<div class=fva2_ml10>&nbsp;<br>
											<font color="#000099"><?php echo $LDNotesTempReport ?></font><br>
											<textarea name="results" cols=80 rows=5 wrap="physical"><?php if($read_form && $stored_request['results']) echo stripslashes($stored_request['results']) ?></textarea>
										</div>
									</td>
								</tr>	
								<tr bgcolor="<?php echo $bgc1 ?>">
									<td colspan=2 align="right">
										<div class=fva2_ml10>
											<font color="#000099"><?php echo $LDDate ?>
											<input type="text" name="results_date" 
					value="<?php 
						if($read_form && $stored_request['results_date']!=DBF_NODATE){
							echo formatDate2Local($stored_request['results_date'],$date_format); 
						}else{
							echo formatDate2Local(date('Y-m-d'),$date_format);
						}
					?>" size=10 maxlength=10 onBlur="IsValidDate(this,'<?php echo $date_format ?>')"  onKeyUp="setDate(this,'<?php echo $date_format ?>','<?php echo $lang ?>')">
											<a href="javascript:show_calendar('form_test_request.results_date','<?php echo $date_format ?>')">
												<img <?php echo createComIcon($root_path,'show-calendar.gif','0','absmiddle'); ?>></a><font size=1 face="arial">
											</a>
											<?php echo $LDReportingDoc ?>
											<input type="text" name="results_doctor" value="<?php if($read_form && $stored_request['results_doctor']) echo $stored_request['results_doctor']; ?>" size=35 maxlength=35> 
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
	<input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0') ?>  title="<?php echo $LDSaveEntry ?>"> 
	<a href="javascript:printOut()"><img <?php echo createLDImgSrc($root_path,'printout.gif','0') ?> alt="<?php echo $LDPrintOut ?>"></a>
	<a href="<?php echo 'labor_test_findings_'.$subtarget.'.php?sid='.$sid.'&lang='.$lang.'&batch_nr='.$batch_nr.'&pn='.$pn.'&entry_date='.$stored_request['xray_date'].'&target='.$target.'&subtarget='.$subtarget.'&user_origin='.$user_origin.'&tracker='.$tracker; ?>"><img <?php echo createLDImgSrc($root_path,'enter_result.gif','0') ?> alt="<?php echo $LDEnterResult ?>"></a>
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
	<input type="hidden" name="batch_nr" value="<?php echo $batch_nr ?>">
	<input type="hidden" name="edit" value="<?php echo $edit ?>">
	<input type="hidden" name="target" value="<?php echo $target ?>">
	<input type="hidden" name="subtarget" value="<?php echo $subtarget ?>">
	<input type="hidden" name="tracker" value="<?php echo $tracker ?>">
	<input type="hidden" name="noresize" value="<?php echo $noresize ?>">
	<input type="hidden" name="user_origin" value="<?php echo $user_origin ?>">
	<input type="hidden" name="status" value="pending">
	<input type="hidden" name="mode" value="<?php if($mode=="edit") echo "update"; else echo $mode ?>">
	<input type="hidden" name="formtitle" value="<?php echo $formtitle; ?>">
</form>
<?php
}else{   # else-stmt of if-stmt 'if($batchrows)'
?>
	<img <?php echo createMascot($root_path,'mascot1_r.gif','0','bottom') ?> align="absmiddle"><font size=3 face="verdana,arial" color="#990000"><b><?php echo $LDNoPendingRequest ?></b></font>
	<p>
	<a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'back2.gif','0') ?>></a>
<?php
}

$sTemp = ob_get_contents();
ob_end_clean();

# Assign to page template object
$smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>