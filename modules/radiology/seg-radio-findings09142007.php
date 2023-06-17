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
# Create personell object
include_once($root_path.'include/care_api_classes/class_personell.php');
$personell_obj=new Personell;

# Create radiology object
require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj = new SegRadio;

if(!isset($mode)) $mode='';

echo "seg-radio-findings.php : _POST : <br>\n"; print_r($_POST); echo " <br><br> \n";

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

#echo "seg-radio-findings.php : batch_nr = '".$batch_nr."' <br> \n";
#echo "seg-radio-findings.php : pid = '".$pid."' <br> \n";
/*
$temp1 = array('100098','100097','100099','100101');
$temp2 = array('findings 1','findings 2','findings 3 findings 3 findings 3 findings 3 findings 3 findings 3 findings 3','findings 4');
$temp3 = array('2007-08-01','2007-08-03','2007-08-06','2007-08-08');
$temp4 = array('impression 1','impression 2 impression 2 impression 2 impression 2 impression 2','impression 3','impression 4');

echo "radiology/seg-radio-findings.php : temp1 : "; print_r($temp1); echo " <br> \n";
echo "radiology/seg-radio-findings.php : temp2 : "; print_r($temp2); echo " <br> \n";
echo "radiology/seg-radio-findings.php : temp3 : "; print_r($temp3); echo " <br> \n";
echo "radiology/seg-radio-findings.php : temp4 : "; print_r($temp4); echo " <br> \n";

$temp1 = serialize($temp1);
$temp2 = serialize($temp2);
$temp3 = serialize($temp3);
$temp4 = serialize($temp4);

echo "radiology/seg-radio-findings.php : temp1 = '".$temp1."' <br> \n";
echo "radiology/seg-radio-findings.php : temp2 = '".$temp2."' <br> \n";
echo "radiology/seg-radio-findings.php : temp3 = '".$temp3."' <br> \n";
echo "radiology/seg-radio-findings.php : temp4 = '".$temp4."' <br> \n";
*/
$radioRequestInfo = $radio_obj->getAllRadioInfoByBatch($batch_nr);
#echo "radiology/seg-radio-findings.php : radio_obj->sql = '".$radio_obj->sql."' <br> \n";
#echo "radiology/seg-radio-findings.php : radioRequestInfo : "; print_r($radioRequestInfo); echo " <br> \n";

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

/* Check for the patient number = $pn. If available get the patients data */
# used in generatin the bar code [burn]
if($batchrows && $pn){
	include_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;
	if( $enc_obj->loadEncounterData($pn)) {

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
		# $requestDocInfo['nr'] , department number
		# $requestDocInfo['id'] , department id name
		# $requestDocInfo['name_formal'] , department full name
}

# Create person object
include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj = new Person($pid);   # 'pid' will be passed as parameter aside from batch_nr
# Create address object
include_once($root_path.'include/care_api_classes/class_address.php');
$address_brgy = new Address('barangay');

if($pid){
	if(!($basicInfo = $person_obj->getAllInfoArray($pid))){
		echo '<em class="warn">Sorry but the page cannot be displayed!</em> <br>';
#		echo "<em class='warn'> intval(pid) = '".intval($pid)."' </em> <br> \n";
#		echo "<em class='warn'> person_obj->sql = '".$person_obj->sql."' </em> <br> \n";
		exit();
	}
	extract($basicInfo);
	$brgy_info = $address_brgy->getAddressInfo($brgy_nr,TRUE);
	if($brgy_info){
		$brgy_row = $brgy_info->FetchRow();
	}
}else{
	echo '<em class="warn"> Sorry but the page cannot be displayed! <br> Invalid PID!</em>';
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

<style type="text/css">
<!--
.olbg {
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	background-color:#0000ff;
	border:1px solid #4d4d4d;
}
.olcg {
	background-color:#aa00aa; 
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
	background-color:#ffffcc; 
	text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
	font-family:Arial; font-size:12px; 
	font-weight:bold; 
	color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}

a {color:#338855;font-weight:bold;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style> 


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
	<table border=0 bgcolor="#000000" cellpadding=1 cellspacing=0>
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
				<table border=0 bgcolor="#ffffff" cellpadding=0 cellspacing=0>
					<tr>
						<td>
							<table   cellpadding=0 cellspacing=1 border=0 width="99%">
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
											<font color="#000099">Date of Service</font>
											&nbsp;&nbsp; 
<?php
	$phpfd=$date_format;
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

#echo "radiology/seg-radio-findings.php : 2 radioRequestInfo['service_date'] = '".$radioRequestInfo['service_date']."' <br> \n";

	$service_date = $radioRequestInfo['service_date'];

#echo "radiology/seg-radio-findings.php : service_date = '".$service_date."' <br> \n";

	if (($service_date!='0000-00-00')  && ($service_date!=""))
		$service_date = @formatDate2Local($service_date,$date_format);
	else
		$service_date='';

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

				<table cellspacing="0 "cellpadding="0" border="0" class="frame">
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
										<tr id="findings-list-header" class="reg_list_titlebar" style="font-weight:bold;padding:0px;" align="center">
											<td width="2%">
												<b><?php echo "No.";  ?></b>
											</td>
											<td  width="18%">
												<b><?php echo "Resident in-charged";  ?></b>
											</td>
											<td width="31%">
												<b><?php echo "Findings";  ?></b>
											</td>
											<td width="31%">
												<b><?php echo "Impression";  ?></b>
											</td>
											<td width="8%">
												<b><?php echo "Date";  ?></b>
											</td>
<?php
		if ($radioRequestInfo['status']!='done'){
?>
<!--
											<td width="10%" colspan="2">
												<b><?php echo "Options";  ?></b>
											</td>
-->
											<td width="4%">
												<b><?php echo "Edit";  ?></b>
											</td>
											<td width="6%">
												<b><?php echo "Delete";  ?></b>
											</td>
<?php
		}# end of if-stmt 'if ($radioRequestInfo['status']!='done')'
?>
										</tr>
									</thead>
									<tbody>
										<!-- List of findings -->
									</tbody>
								</table>
								<table border="0" cellpadding="2" cellspacing="1" width="600" style="border:1px solid #666666;border-top:0px;margin-top:-1px;">
									<tr class="reg_list_titlebar" >
									</tr>
								</table>
								<div align="center">
									<br>
	<?php
#		if ($radioRequestInfo['status']!='done'){
#			echo "<input type='image' ".createLDImgSrc($root_path,'add_sm.gif','0')." title='Add Finding' name='addButton' id='addButton' onClick=\"$('save').value=0; popEditFinding(".$batch_nr.",".$count_find.");\">";
?>
<a href="javascript:void(0);"
	onclick="var temp = $('count_find').value; return overlib(
		OLiframeContent('seg-radio-findings-edit.php<?= URL_APPEND ?>&batch_nr=<?=$batch_nr?>&findings_nr='+temp, 500, 475, 'if1', 1, 'auto'),
			WIDTH,500, TEXTPADDING,0, BORDER,0, 
			STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
			CLOSETEXT, '<img src=<?= $root_path ?>images/x.gif border=0>',
			CAPTIONPADDING,4, CAPTION,'Add Finding', MIDX,0, MIDY,0, 
			STATUS,'Add Finding');"
	onmouseout="nd();">
	<input type="image" name="addButton" id="addButton" <?= createLDImgSrc($root_path,'add_finding_02.gif','0') ?>> 
</a>
<?php 
/*
			# if count_finding is greater than 1
			# burn added : August 27, 2007; September 13, 2007
			if ($count_find){
				$show_referralButton = 'style="display:\'\'"';
			}else{# end of if-stmt 'if ($count_find)'
				$show_referralButton = 'style="display:none"';				
			}
*/
?>
	<input type="image" name="referralButton" id="referralButton" <?= "style=\"display:none\"" ?> <?php echo createLDImgSrc($root_path,'referral.gif','0') ?>  alt="Referral" onClick="$('save').value=1; referralHandler();">
<?php
#		}# end of if-stmt 'if ($radioRequestInfo['status']!='done')'
?>
								</div>
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
#		}else{# end of if-stmt 'if ($radioRequestInfo['status']!='done')'
?>
	<input type="button" name="printReport" id="printReport" <?= "style=\"display:none\"" ?> value="Print Report" onClick="printRadioReport();">
<?php
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

	<input type="hidden" name="batch_nr" id="batch_nr" value="<?php echo $batch_nr ?>">
	<input type="hidden" name="pid" value="<?php echo $pid ?>">
	<input type="hidden" name="count_find" id="count_find" value="<?= $count_find? $count_find:0 ?>">
	<input type="hidden" name="finding_nr" id="finding_nr" value="">
	<input type="hidden" name="status" id="status" value="">
	<input type="hidden" name="mode" id="mode" value="save">
	<input type="hidden" name="save" id="save" value="0">

	<input type="hidden" name="formtitle" value="<?php echo $formtitle; ?>">
</form>


<script language="javascript">

	function fSubmit(id) {
		if ($(id).submit)
			$(id).submit();
	}
	
	function checkFindingsForm(){

//		alert("$F('save') = '"+$F('save')+"'");
		if ($F('save')=='0'){
//			alert("false : $F('save') = '"+$F('save')+"'");
			// if the button clicked is not the for Referral, SAVE or SAVE&DONE buttons
			return false;
		}
		if ($F('mode') != 'referral'){
			if (($F('count_find')=='0') || ($F('count_find')=='')){
				alert("Please add a finding first.");
				$('addButton').focus();
				return false;	
			}else if($F('service_date') == ''){
				alert("Please indicate the date of service.");
				$('service_date').focus();
				return false;
			}
		}
		return true;
	}

	function referralHandler(){
	alert("referralHandler : 1 F('batch_nr')='"+$F('batch_nr')+" \nF('service_date')='"+$F('service_date')+"'");
		$('mode').value = 'referral';
		$('status').value = 'referral';
	alert("referralHandler : 2 F('batch_nr')='"+$F('batch_nr')+" \nF('service_date')='"+$F('service_date')+"'");
		xajax_referralRadioFinding($F('batch_nr'),$F('service_date'));
//		return false;
//		fSubmit('form_test_findings');		
	}
	
	function saveOnly(){
		if (checkFindingsForm()){
			$('mode').value = 'save';
alert("saveOnly : F('batch_nr')='"+$F('batch_nr')+" \nF('service_date')='"+$F('service_date')+"'");
		xajax_saveOnlyRadioFinding($F('batch_nr'),$F('service_date'));
//			fSubmit('form_test_findings');
		}
//		return false;
	}

	function saveAndDone(){
		if (checkFindingsForm()){
			$('mode').value = 'save';
			$('status').value = 'done';
alert("saveAndDone : F('batch_nr')='"+$F('batch_nr')+" \nF('service_date')='"+$F('service_date')+"'");
		xajax_saveAndDoneRadioFinding($F('batch_nr'),$F('service_date'));
//			fSubmit('form_test_findings');
		}
//		return false;
	}

	function msgPopUp(msg){
		alert(msg);
	}

	function deleteFinding(batch_nr,nr){
		var answer = confirm("You are about to delete finding #"+(nr+1)+". Are you sure?");
		//alert("answer = '"+answer+"'");
		if (answer){
			$('mode').value = 'delete';
			$('finding_nr').value = nr;
//			fSubmit('form_test_findings');			
			xajax_deleteRadioFinding(batch_nr,nr);
//			refreshFindingList();
		}
	}
/*
	function popEditFinding(batch_nr,nr){
		
		var w=window.screen.width;
		var h=window.screen.height;
		var ww=500;
		var wh=475;
		urlholder="seg-radio-findings-edit.php<?= URL_APPEND ?>&batch_nr="+batch_nr+"&findings_nr="+nr;

		if (window.showModalDialog){  //for IE
			window.showModalDialog(urlholder,"width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
		}else{
//			window.open("createCampus.php?i="+id,"createCampus","modal, width=480,height=320,menubar=no,resizable=no,scrollbars=no");
			popWindowEditFinding=window.open(urlholder,"EditFinding","width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
			window.popWindowEditFinding.moveTo((w/2)+80,(h/2)-(wh/2));
		}
	}
*/	
	function clearFindings(list) {	
		if (list) {
			var dBody=list.getElementsByTagName("tbody")[0];
			if (dBody) {
				trayItems = 0;
				dBody.innerHTML = "";
				return true;
			}
		}
		return false;
	}

	function appendFinding(list,details) {
alert("appendFinding : list : \n"+list);
		if (list) {
			var dBody=list.getElementsByTagName("tbody")[0];
alert("appendFinding : dBody : \n"+dBody);
			if (dBody) {
				var src;
				var items = document.getElementsByName('items[]');
						dRows = dBody.getElementsByTagName("tr");
			
				if (details) {
					var id = details.no;
					if (items) {
						for (var i=0;i<items.length;i++) {
							if (items[i].value == details.no) {
								$('docName'+id).innerHTML = details.docName;
								$('finding'+id).innerHTML = details.finding;
								$('r_impress'+id).innerHTML = details.r_impression;
								$('f_date'+id).innerHTML = details.f_date;
								return true;
							}
						}
						if (items.length == 0)
							clearFindings(list);
					}
	
					alt = (dRows.length%2)+1;
					
					src = 
						'<tr class="wardlistrow'+alt+'" id="row'+id+'" style="font-weight:bold;padding:0px"> '+
						'	<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
						'	<td align="center"><b> '+(parseInt(id)+1)+'</b></td> '+
						'	<td id="docName'+id+'"><b> '+details.docName+' </b></td> '+
						'	<td id="finding'+id+'"><b> '+details.finding+' </b></td> '+
						'	<td id="r_impress'+id+'"><b> '+details.r_impression+' </b></td> '+
						'	<td id="f_date'+id+'"><b> '+details.f_date+' </b></td> ';
					if (details.status!='done'){
						src +=
							'	<td align="center"> '+
							'		<a href="javascript:void(0);" '+
							'			onclick="return overlib( '+
							'				OLiframeContent(\''+details.f_link+'\', 500, 475, \'if1\', 1, \'auto\'), '+
							'					WIDTH,500, TEXTPADDING,0, BORDER,0,  '+
							'					STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE, '+
							'					CLOSETEXT, \'<img src=<?= $root_path ?>images/x.gif border=0>\', '+
							'					CAPTIONPADDING,4, CAPTION,\'Update findings\', MIDX,0, MIDY,0,  '+
							'					STATUS,\'Update findings\');" '+
							'			onmouseout="nd();"> '+
							'			<img name="edit'+details.no+'" id="edit'+details.no+'" <?= createLDImgSrc($root_path,'edit_icon_06.gif','0') ?>> '+
							'		</a> '+
							'	</td> '+
							'	<td align="center"> '+
							'		<img name="delete'+id+'" id="delete'+id+'" <?= createLDImgSrc($root_path,'trash_06.gif','0') ?> onClick="deleteFinding('+details.batch_nr+','+id+');"> '+
							'	</td> ';
						$('referralButton').style.display = '';
						$('saveButton1').style.display = '';
						$('saveButton2').style.display = '';
						$('saveDoneButton').style.display = '';
					}//end of if-stmt "if (details.status!='done')"
					else{
						src +=
							'	<td align="center"> - </td> '+
							'	<td align="center"> - </td> ';
						$('printReport').style.display = '';
						$('saveButton1').style.display = 'none';
						$('saveButton2').style.display = 'none';
						$('saveDoneButton').style.display = 'none';
						$('referralButton').style.display = 'none';
						$('addButton').style.display = 'none';
					}
					src +='</tr>';
					$('count_find').value = parseInt($('count_find').value) + 1;
				}// end of if-stmt 'if (details)'
				else {
//					src = "<tr><td colspan=\"7\">List of findings is currently empty...</td></tr>";	
					src = "									<tr> "+
							"											<td colspan=\"7\" align=\"center\" bgcolor=\"#FFFFFF\" style=\"color:#FF0000; font-family:'Arial', Courier, mono; font-style:Bold; font-weight:bold; font-size:12px;\"> "+
							"												List of findings is currently empty... "+
							"											</td> "+
							"										</tr> ";
				}
alert("appendFinding : src : \n"+src);
				dBody.innerHTML += src;
				return true;
			}
		}
		return false;
	}

/*
	function removeFinding(id) {
		var destTable, destRows;
		var table = $('order-list');
		var rmvRow=document.getElementById("row"+id);
		if (table && rmvRow) {
			$('rowID'+id).parentNode.removeChild($('rowID'+id));
			$('rowPrcCash'+id).parentNode.removeChild($('rowPrcCash'+id));
			$('rowPrcCharge'+id).parentNode.removeChild($('rowPrcCharge'+id));		
			$('rowQty'+id).parentNode.removeChild($('rowQty'+id));
			var rndx = rmvRow.rowIndex-1;
			table.deleteRow(rmvRow.rowIndex);
			reclassRows(table,rndx);
		}
//		refreshFindingList();
}
*/

function refreshFindingsList(){
	var items = document.getElementsByName('items[]');
	if (items.length == 0){
		$('count_find').value = 0;
		$('referralButton').style.display = 'none';
	}
}
/*
		burn added : September 13, 2007
*/
function emptyIntialFindings(showEmptyMsg){
alert("emptyIntialFindings : 1 showEmptyMsg='"+showEmptyMsg+"'");
	clearFindings($('findings-list'));
alert("emptyIntialFindings : 2 showEmptyMsg='"+showEmptyMsg+"'");
	if (showEmptyMsg=='1'){
alert("emptyIntialFindings : 3 showEmptyMsg='"+showEmptyMsg+"'");
		appendFinding($('findings-list'),null);
	}
}

function initialFindingsList(batch_nr,f_nr,findings,radio_impression,f_date,docName,status) {
	var details = new Object();

		details.batch_nr = batch_nr;
		details.no = f_nr;
		details.finding = findings;
		details.r_impression = radio_impression;
		details.f_date = f_date;
		details.docName = docName;
		details.status = status;
		
		details.f_link="seg-radio-findings-edit.php<?= URL_APPEND ?>&batch_nr="+batch_nr+"&findings_nr="+f_nr;
		var msg = "details.status='"+details.status+"'\ndetails.batch_nr='"+details.batch_nr+
					 "\ndetails.no='"+details.no+"'\ndetails.finding='"+details.finding+
					 "'\ndetails.r_impression='"+details.r_impression+
					 "'\ndetails.f_date='"+details.f_date+"'\ndetails.docName='"+details.docName+"'\n";	
		alert("initialFindingsList : "+msg);
		var list =document.getElementById('findings-list');
		alert("initialFindingsList : list : "+list);
		result = appendFinding(list,details);
}

/*
	function popEditFinding(batch_nr,nr){
		
		var w=window.screen.width;
		var h=window.screen.height;
		var ww=500;
		var wh=475;
		urlholder="seg-radio-findings-edit.php<?= URL_APPEND ?>&batch_nr="+batch_nr+"&findings_nr="+nr;

		if (window.showModalDialog){  //for IE
			window.showModalDialog(urlholder,"width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
		}else{
//			window.open("createCampus.php?i="+id,"createCampus","modal, width=480,height=320,menubar=no,resizable=no,scrollbars=no");
			popWindowEditFinding=window.open(urlholder,"EditFinding","width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
			window.popWindowEditFinding.moveTo((w/2)+80,(h/2)-(wh/2));
		}
	}
*/

	function printRadioReport(){
		
		var w=window.screen.width;
		var h=window.screen.height;
		var ww=500;
		var wh=500;
//		AUGUST 9, 2007
//			kuya mark : 
//				change nyo po ang	urlholder="PHP file that will handle the report generation of ROENTGENOLOGICAL REPORT"
//		urlholder="seg-radio-findings-edit.php<?= URL_APPEND ?>&batch_nr="+batch_nr+"&findings_nr="+nr;

		alert("ROENTGENOLOGICAL REPORT in pdf format");
/*
		if (window.showModalDialog){  //for IE
			window.showModalDialog(urlholder,"width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
		}else{
//			window.open("createCampus.php?i="+id,"createCampus","modal, width=480,height=320,menubar=no,resizable=no,scrollbars=no");
			popWindowEditFinding=window.open(urlholder,"EditFinding","width=" + ww + ",height=" + wh + ",menubar=no,resizable=yes,scrollbars=yes,dependent=yes");
			window.popWindowEditFinding.moveTo((w/2)+80,(h/2)-(wh/2));
		}
*/
	}

</script>

<?php
		#$count_find=count($radioRequestInfo['findings']);
#echo "radiology/seg-radio-findings.php : radioRequestInfo['findings'] = '".$radioRequestInfo['findings']."' <br> \n";		
#echo "radiology/seg-radio-findings.php : count_find = '".$count_find."' <br> \n";

#		echo "<br>";
#		if ($radioRequestInfo['status']!='done'){
#			echo "<input type='image' ".createLDImgSrc($root_path,'add_sm.gif','0')." title='Add Request' name='addButton' id='addButton' onClick=\"$('save').value=0; popEditFinding(".$batch_nr.",".$count_find.");\">";
#		}# end of if-stmt 'if ($radioRequestInfo['status']!='done')'
		
#		echo "<img ".createLDImgSrc($root_path,'add_sm.gif','0')." name='addButton' id='addButton' width='15' height='10' alt='".$LDPrintOut."' onClick=\"popEditFinding(".$batch_nr.",".$count_find.");\">";
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
		xajax_populateRadioFinding(<?=$batch_nr? $batch_nr:0?>);	
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