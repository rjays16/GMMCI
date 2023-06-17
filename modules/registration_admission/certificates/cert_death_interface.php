<?php		//--- with modifications by pet from previous cert_death_interface.js started by kuya carlo, burn & vanessa ---

include("roots.php");
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');

define('NO_2LEVEL_CHK',1);

define('LANG_FILE','aufnahme.php');
$local_user='aufnahme_user';

include_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address('country');
$address_brgy = new Address('barangay');

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

require_once($root_path.'include/care_api_classes/class_dateGenerator.php');
$dategen = new DateGenerator;

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();

if ($row = $objInfo->getAllHospitalInfo()) {
		$row['hosp_agency'] = strtoupper($row['hosp_agency']);
		$row['hosp_name']   = strtoupper($row['hosp_name']);
}
else {
		$row['hosp_country'] = "Republic of the Philippines";
		$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
		$row['hosp_name']    = "BUKIDNON PROVINCIAL HOSPITAL - MALAYBALAY";
		$row['hosp_addr1']   = "Malaybalay, Bukidnon";
		$row['mun_name']     = "Malaybalay";
		$row['prov_name']     = "Bukidnon";
		$row['region_name']     = "Region X";
}


require($root_path.'include/inc_front_chain_lang.php');

if(isset($_GET['pid'])&& $_GET['pid']){
	$pid = $_GET['pid'];
}
if(isset($_POST['pid']) && $_POST['pid']){
	$pid = $_POST['pid'];
}

if(isset($_GET['encounter_nr'])&& $_GET['encounter_nr']){
	$encounter_nr = $_GET['encounter_nr'];
}

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj = new Person($pid);

include_once($root_path.'include/care_api_classes/class_cert_death.php');
$obj_deathCert = new DeathCertificate($pid);
$birthYear = intval(substr($date_birth, 0, 4));
$birthMonth = intval(substr($date_birth, 5, 7));
$birthDay = intval(substr($date_birth, 8, 10));

$hTime = 0;
$mTime = 0;
$sTime = 0;
if ( (intval($death_age)>0) || (intval($death_months)>0) || (intval($death_days)>7) ){
	$HTTP_POST_VARS['age_at_death']= $death_age;
}else{
	#unnecessary info if age at death is less than or equal to 7 days
	if ( (intval($death_age)==0) && (intval($death_months)==0) && (intval($death_days)==0) ){
		# set the number of hours, minutes, seconds if age at death is under 1 day
		if ( (intval($HTTP_POST_VARS['hours'])!=0) ||
				(intval($HTTP_POST_VARS['minutes'])!=0) ||
				(intval($HTTP_POST_VARS['sec'])!=0) ){

			$hTime = $HTTP_POST_VARS['hours'];
			if (intval($HTTP_POST_VARS['hours'])<10)
				$hTime = "0".$HTTP_POST_VARS['hours'];
			$mTime = $HTTP_POST_VARS['minutes'];
			if (intval($HTTP_POST_VARS['minutes'])<10)
				$mTime = "0".$HTTP_POST_VARS['minutes'];
			$sTime = $HTTP_POST_VARS['sec'];
			if (intval($HTTP_POST_VARS['sec'])<10)
				$sTime = "0".$HTTP_POST_VARS['sec'];
			$HTTP_POST_VARS['age_at_death']= $hTime.':'.$mTime.':'.$sTime;
		}
	}
}

#added by VAN 07-15-09
$HTTP_POST_VARS['death_hour'] = $hTime;
$HTTP_POST_VARS['death_min'] = $mTime;
$HTTP_POST_VARS['death_sec'] = $sTime;
#--------------------------

	# Serializing the  Causes of Death information
if(isset($_POST['mode'])){
		$deathCause = array();
		$deathInterval = array();

		for($i=0;$i<10;$i++){
			$cx="cause".$i;
			$ix="interval".$i;

			if(!empty($HTTP_POST_VARS[$cx])) $deathCause[$cx]=$HTTP_POST_VARS[$cx];
			if(!empty($HTTP_POST_VARS[$ix])) $deathCause[$ix]=$HTTP_POST_VARS[$ix];

			$HTTP_POST_VARS['death_cause']='';
				// Serialize the data
			$HTTP_POST_VARS['death_cause']=serialize($deathCause);
		}
}

	# set the time of death
if (trim($HTTP_POST_VARS['death_time'])!=''){
	#convert to 24-Hour time
	$death_time_array_tmp = explode(':',$HTTP_POST_VARS['death_time']);
	$hr = intval($death_time_array_tmp[0]);
	$min = $death_time_array_tmp[1];
	if($_POST['selAMPM'] == 'P.M.'){
		if(($hr>0)&&($hr<12)){
		$hr = $hr+12;
		}
		$HTTP_POST_VARS['death_time'] = $hr.':'.$min.':00';
	}elseif(($_POST['selAMPM'] == 'A.M.')&&($hr==12)){
		$HTTP_POST_VARS['death_time'] = '00:'.$min.':00';
	}
}

if ($HTTP_POST_VARS['attendant_type']=="4"){
		# unnecessary info, if there was no ATTENDANT prior to or at the time of death
	$HTTP_POST_VARS['attended_from_date']='';
	$HTTP_POST_VARS['attended_to_date']='';
}

if(isset($_POST['mode'])){
	switch($_POST['mode']){
		case 'save':
			$tmp=array();
			if (trim($HTTP_POST_VARS['death_date'])!="")
				$HTTP_POST_VARS['death_date'] = formatDate2STD($HTTP_POST_VARS['death_date'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['attended_from_date'])!="")
				$HTTP_POST_VARS['attended_from_date'] = formatDate2STD($HTTP_POST_VARS['attended_from_date'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['attended_to_date'])!="")
				$HTTP_POST_VARS['attended_to_date'] = formatDate2STD($HTTP_POST_VARS['attended_to_date'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['attendant_date_sign'])!="")
				$HTTP_POST_VARS['attendant_date_sign'] = formatDate2STD($HTTP_POST_VARS['attendant_date_sign'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['burial_date_issued'])!="")
				$HTTP_POST_VARS['burial_date_issued'] = formatDate2STD($HTTP_POST_VARS['burial_date_issued'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['late_bcdate'])!="")
				$HTTP_POST_VARS['late_bcdate'] = formatDate2STD($HTTP_POST_VARS['late_bcdate'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['informant_date_sign'])!="")
				$HTTP_POST_VARS['informant_date_sign'] = formatDate2STD($HTTP_POST_VARS['informant_date_sign'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['affiant_com_tax_date'])!="")
				$HTTP_POST_VARS['affiant_com_tax_date'] = formatDate2STD($HTTP_POST_VARS['affiant_com_tax_date'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['late_officer_date_sign'])!="")
				$HTTP_POST_VARS['late_officer_date_sign'] = formatDate2STD($HTTP_POST_VARS['late_officer_date_sign'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['encoder_date_sign'])!="")
				$HTTP_POST_VARS['encoder_date_sign'] = formatDate2STD($HTTP_POST_VARS['encoder_date_sign'],$date_format,$tmp);

			$HTTP_POST_VARS['history'] = " \n"." Created Death Certificate ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_id'] = $HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_time'] = date('Y-m-d H:i:s');
			//saving data of death info

			if($obj_deathCert->saveDeathCertificateInfoFromArray($HTTP_POST_VARS)){

				#$HTTP_POST_VARS['death_date'] = $HTTP_POST_VARS['death_date'];
				$HTTP_POST_VARS['death_encounter_nr'] = $encounter_nr;
				$HTTP_POST_VARS['death_cause'] = $HTTP_POST_VARS['death_cause'];
				$HTTP_POST_VARS['death_cause_code'] = '';
				$HTTP_POST_VARS['citizenship'] = $HTTP_POST_VARS['d_citizenship'];
				$HTTP_POST_VARS['religion'] = $HTTP_POST_VARS['d_religion'];

				if($decease_civilstatus=="others"){
					$HTTP_POST_VARS['civil_status'] = $d_civilstat_tmp;
				}else{
					$HTTP_POST_VARS['civil_status'] = $decease_civilstatus;
				}

				$HTTP_POST_VARS['street_name'] = $HTTP_POST_VARS['residence_basic'];
				$HTTP_POST_VARS['brgy_nr'] = $HTTP_POST_VARS['residence_brgy'];
				$HTTP_POST_VARS['age'] = $HTTP_POST_VARS['death_age'];

				if ($date_birth3=="0000-00-00")
					$HTTP_POST_VARS['date_birth'] = $date_birth3;
				else {
					$bdate = $HTTP_POST_VARS['birth_year']."-".$HTTP_POST_VARS['birth_month']."-".$HTTP_POST_VARS['birth_day'];
					$HTTP_POST_VARS['date_birth'] = date("Y-m-d",strtotime($bdate));
				}

				#$ddate = $HTTP_POST_VARS['death_year']."-".$HTTP_POST_VARS['death_month']."-".$HTTP_POST_VARS['death_day'];
				#$HTTP_POST_VARS['death_date'] = date("Y-m-d",strtotime($ddate));

				$HTTP_POST_VARS['history'] = $obj_deathCert->ConcatHistory(" \n"." Saved/Updated Reg Info (Death Certif.) ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']);
				$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
				$HTTP_POST_VARS['modify_time']=date('Y-m-d H:i:s');

				$person_obj->updatePersonInfo($pid, $HTTP_POST_VARS);

				echo " \n <script type=\"text/javascript\">alert(\"Saved sucessfully!\")</script>";
			}else{
				echo '<em class="warn"><font style="color:#FF0000"><b>'.$db->ErrorMsg().'<b></font></em>';
			}

		break;
		case 'update':
			$tmp=array();
			if (trim($HTTP_POST_VARS['death_date'])!="")
				$HTTP_POST_VARS['death_date'] = formatDate2STD($HTTP_POST_VARS['death_date'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['attended_from_date'])!="")
				$HTTP_POST_VARS['attended_from_date'] = formatDate2STD($HTTP_POST_VARS['attended_from_date'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['attended_to_date'])!="")
				$HTTP_POST_VARS['attended_to_date'] = formatDate2STD($HTTP_POST_VARS['attended_to_date'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['attendant_date_sign'])!="")
				$HTTP_POST_VARS['attendant_date_sign'] = formatDate2STD($HTTP_POST_VARS['attendant_date_sign'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['burial_date_issued'])!="")
				$HTTP_POST_VARS['burial_date_issued'] = formatDate2STD($HTTP_POST_VARS['burial_date_issued'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['late_bcdate'])!="")
				$HTTP_POST_VARS['late_bcdate'] = formatDate2STD($HTTP_POST_VARS['late_bcdate'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['informant_date_sign'])!="")
				$HTTP_POST_VARS['informant_date_sign'] = formatDate2STD($HTTP_POST_VARS['informant_date_sign'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['affiant_com_tax_date'])!="")
				$HTTP_POST_VARS['affiant_com_tax_date'] = formatDate2STD($HTTP_POST_VARS['affiant_com_tax_date'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['late_officer_date_sign'])!="")
				$HTTP_POST_VARS['late_officer_date_sign'] = formatDate2STD($HTTP_POST_VARS['late_officer_date_sign'],$date_format,$tmp);

			if (trim($HTTP_POST_VARS['encoder_date_sign'])!="")
				$HTTP_POST_VARS['encoder_date_sign'] = formatDate2STD($HTTP_POST_VARS['encoder_date_sign'],$date_format,$tmp);

			$HTTP_POST_VARS['history'] = $obj_deathCert->ConcatHistory(" \n"." Updated Death Certificate ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']);
			$HTTP_POST_VARS['modify_id'] = $HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['modify_time'] = date('Y-m-d H:i:s');

			if($obj_deathCert->updateDeathCertificateInfoFromArray($HTTP_POST_VARS)){

				#$HTTP_POST_VARS['death_date'] = $HTTP_POST_VARS['death_date'];
				$HTTP_POST_VARS['death_encounter_nr'] = $encounter_nr;
				$HTTP_POST_VARS['death_cause'] = $HTTP_POST_VARS['death_cause'];
				$HTTP_POST_VARS['death_cause_code'] = '';
				$HTTP_POST_VARS['citizenship'] = $HTTP_POST_VARS['d_citizenship'];
				$HTTP_POST_VARS['religion'] = $HTTP_POST_VARS['d_religion'];

				if(($decease_civilstatus=="others")&&($d_civilstat_tmp)){
					$HTTP_POST_VARS['civil_status'] = $d_civilstat_tmp;
				}else{
					$HTTP_POST_VARS['civil_status'] = $decease_civilstatus;
				}

				$HTTP_POST_VARS['street_name'] = $HTTP_POST_VARS['residence_basic'];
				$HTTP_POST_VARS['brgy_nr'] = $HTTP_POST_VARS['residence_brgy'];
				$HTTP_POST_VARS['age'] = $HTTP_POST_VARS['death_age'];

				if ($date_birth3=="0000-00-00")
					$HTTP_POST_VARS['date_birth'] = $date_birth3;
				else {
					$bdate = $HTTP_POST_VARS['birth_year']."-".$HTTP_POST_VARS['birth_month']."-".$HTTP_POST_VARS['birth_day'];
					$HTTP_POST_VARS['date_birth'] = date("Y-m-d",strtotime($bdate));
				}

				#$ddate = $HTTP_POST_VARS['death_year']."-".$HTTP_POST_VARS['death_month']."-".$HTTP_POST_VARS['death_day'];
				#$HTTP_POST_VARS['death_date'] = date("Y-m-d",strtotime($ddate));

				$HTTP_POST_VARS['history'] = $obj_deathCert->ConcatHistory(" \n"."Updated Reg. Info (Death Certif.) ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']);
				$HTTP_POST_VARS['modify_id'] = $HTTP_SESSION_VARS['sess_user_name'];
				$HTTP_POST_VARS['modify_time'] = date('Y-m-d H:i:s');

				$person_obj->updatePersonInfo($pid, $HTTP_POST_VARS);

				echo " \n <script type=\"text/javascript\">alert(\"Updated sucessfully!\")</script>";
			}else{
				echo '<em class="warn"><font style="color:#FF0000"><b>'.$db->ErrorMsg().'<b></font></em>';
			}
		break;
	} //end of switch statement
}

if($pid){
	//if(!($basicInfo = $person_obj->BasicDataArray($pid))){
	#if(!($basicInfo = $person_obj->getAllInfoArray($pid))){
    if(!($basicInfo = $person_obj->getAllInfoArrayEnc($pid, $encounter_nr))){
    	echo '<em class="warn">Sorry, the page cannot be displayed!</em> <br>';
		exit();
	}
	extract($basicInfo);

}else{
	echo '<em class="warn"> Sorry, the page cannot be displayed! <br> Invalid HRN!</em>';
	exit();
}
#echo $person_obj->sql;

# code for retrieving death certificate information
$deathCertInfo = $obj_deathCert->getDeathCertRecord($pid);
#echo $obj_deathCert->sql;
if($deathCertInfo){
	extract($deathCertInfo);
	$delivery_method_tmp= substr(trim($deathCertInfo['delivery_method']),0,1);
	$delivery_method_info = substr(trim($deathCertInfo['delivery_method']),4);
	$death_manner_tmp = substr(trim($deathCertInfo['death_manner']),0,1);
	$death_manner_accident = substr(trim($deathCertInfo['death_manner']),4);
	$death_manner_info = substr(trim($deathCertInfo['death_manner']),4);
	$attendant_type_tmp = substr(trim($deathCertInfo['attendant_type']),0,1);
	$attendant_type_others = substr(trim($deathCertInfo['attendant_type']),4);
	$corpse_disposal_tmp= substr(trim($deathCertInfo['corpse_disposal']),0,1);
	$corpse_disposal_others = substr(trim($deathCertInfo['corpse_disposal']),4);
	$is_autopsy = substr(trim($deathCertInfo['is_autopsy']),0,1);
	$tmp_death_cause = unserialize($deathCertInfo['death_cause']);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Certificate of Death</title>
<style type="text/css">
<!--
.style2 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
body {
	background-color: #FFFFFF;
}
.style5 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 14px; }
.style9 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; color: #FFFFFF; }
.style11 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 14px; color: #FF3300; }
.style13 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold; }
.style15 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #FF3300; }
.style17 {font-size: 10px}
.style19 {
	font-size: 12px;
	color: #284964;
}
.style21 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; font-weight: bold; color: #284964; }
-->
</style>
<script language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>

<?php
	echo '<script type="text/javascript" language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\n";
	echo '<link  rel="stylesheet" type="text/css" media="all" href="'.$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/setdatetime.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/checkdate.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/dtpick_care2x.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\n";

?>

<script type="text/javascript" src="<?=$root_path?>js/yui/yahoo/yahoo-min.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/event/event-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dom/dom-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/assets/dpSyntaxHighlighter.js"></script>

<script type="text/javascript" src="<?=$root_path?>modules/registration_admission/certificates/js/death_cert.js"></script>

</head>

<body onload="preSet();">

<?php echo $errorMsg; ?>
<form id="death_certificate" name="death_certificate" method="post" onsubmit="return chckDeathForm(this);">
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F9FFFB">
 <tr>
	<td bgcolor="#F9FFFB">
	 <div align="center"><span class="style2"><?=$row['hosp_country'];?></span><BR />
	<span class="style2">OFFICE OF THE CIVIL REGISTRAR GENERAL</span><BR />
	<span class="style11"><font size="+1">CERTIFICATE OF DEATH</font></span><br />
	<span class="style13"><?php echo $row['hosp_name']." - ".$row['mun_name'];?></span>
	 </div>
	</td>
 </tr>
 <tr>
	<td bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="0" cellspacing="0">
	 <tr>
		<td width="10%" bgcolor="#F9FFFB"> <span class="style2">Province : </span> </td>
		<td width="63%" bgcolor="#F9FFFB">
		 <input name="province" type="hidden" class="style2" id="province" value="<?=$row['prov_name'];?>" />
		 <span class="style13">&nbsp;&nbsp;<?=$row['prov_name'];?></span> </td>
		<td width="28%" rowspan="2" align="right" bgcolor="#F9FFFB"> <span class="style15">Registry No</span><span class="style2">. </span>
		 <input name="registry_nr" id="registry_nr" type="text" class="style2" value="<?= $registry_nr ?>" onblur="trimString(this)" />
		</td>
	 </tr>
	 <tr>
	<td bgcolor="#F9FFFB"> <span class="style2">City/Municipality : </span> </td>
	<td bgcolor="#F9FFFB">
	 <input name="muncity" type="hidden" class="style2" id="muncity" value="<?=$row['mun_name'];?>" />
	 <span class="style13">&nbsp;&nbsp;<?=$row['mun_name'];?></span> </td>
	 </tr>
	</table></td>
 </tr>
 <tr><td>&nbsp;</td></tr>
 <tr>
	<td class="style2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	 <tr>
	<td width="5%" bgcolor="#A0C2DA"><img src="images/brdr_blgry_03.gif" width="31" height="26" /></td>
	<td width="62%" bgcolor="#A0C2DA"></td>
	<td width="33%" bgcolor="#A0C2DA">&nbsp;</td>
	 </tr>
	 <tr>
	<td colspan="3" bgcolor="#A0C2DA"><table width="100%" border="0" cellspacing="1" cellpadding="0">
	 <tr>
		<td height="510" align="center" bgcolor="#FFFFFF"><br />
		<div align="center">
<?php
	if (!$deathCertInfo || empty($deathCertInfo)){
		echo '			<input type="hidden" name="mode" id="mode" value="save">'."\n";
		echo '			<input type="submit" name="Submit" value="Save">'."\n";
	}else{
		echo '			<input type="hidden" name="mode" id="mode" value="update">'."\n";
		echo '			<input type="button" name="Print" value="Print" onClick="printDeathCert(\''.$pid.'\')">'."\n &nbsp; &nbsp;";
		echo '			<input type="submit" name="Submit" value="Update">'."\n";
	}
	echo '			<input type="hidden" name="pid" id="pid" value="'.$pid.'">'."\n";
?>
&nbsp; &nbsp;
<input type="button" name="Cancel" value="Cancel"  onclick="window.close()">
<input type="hidden" name="age0to7days" id="age0to7days" value="<?= $age0to7days?>">

<!--added by VAN 02-20-08 -->
<input type="hidden" name="birth_date" id="birth_date" value="<?= date("m/d/Y",strtotime($date_birth))?>" />

</div>
<br>
<!-- Name starts here -->
		 <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#A0C2DA">
			<tr>
			 <td width="90%" bgcolor="#FFFFFF">
			<tr>
			 <td height="36" colspan="3" valign="top" bgcolor="#D5E3EE"><table width="100%" border="0" cellpadding="0" cellspacing="1">
				<tr>
			 <?php
				$spacevar2 = "&nbsp;&nbsp;";
				$spacevar4 = "&nbsp;&nbsp;&nbsp;&nbsp;";
				$spacevar6 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			 ?>
			 <td width="18%" align="left" valign="middle" bgcolor="#D5E3EE" class="style5">&nbsp;1. &nbsp; NAME </td>
			 <td width="26%" align="center" valign="top" bgcolor="#F9FFFB"><span class="style2">Given Name </span><br />
				<input type="text" name="name_first" id="name_first" size="30" value="<?= ucwords(strtolower($name_first)) ?>" />
			 </td>
			 <td width="27%" align="center" valign="top" bgcolor="#F9FFFB"><span class="style2">Middle Name </span><br />
				<input type="text" name="name_middle" id="name_middle" size="30" value="<?= ucwords(strtolower($name_middle)) ?>" />

			 </td>
			 <td width="29%" align="center" valign="top" bgcolor="#F9FFFB"><span class="style2">Last Name </span><br />
				<input type="text" name="name_last" id="name_last" size="30" value="<?= ucwords(strtolower($name_last)) ?>" />

			 </td>
				</tr>
			 </table></td>
				</tr>
			 </table><br />

<!-- Gender, Religion, Age starts here; Name ends here -->
		<table width="90%" border="0" align="center" cellspacing="0" cellpadding="0">
	 <tr>
		<td bgcolor="#D5E3EE"><table width="100%" border="0" cellpadding="1" cellspacing="1">
		 <tr>

	<!-- Gender -->
		<td width="18%" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="1" cellspacing="1">
		 <tr>
			<td colspan="2" bgcolor="#D5E3EE" class="style5">&nbsp;2. &nbsp; SEX </td>
		 </tr>
		 <tr>
			<td width="45%" bgcolor="#F9FFFB" class="style2">
			 <input type="hidden" name="sex" id="sex" value="<?= $sex ?>" />&nbsp;&nbsp;&nbsp;
			 <input name="sex" id="sex" type="radio" value="m" <?php if ($sex == 'm') echo "checked"; ?> />
			 Male
			</td>
				 </tr>
				 <tr>
					<td width="55%" bgcolor="#F9FFFB" class="style2">&nbsp;&nbsp;&nbsp;
			 <input name="sex" id="sex" type="radio" value="f" <?php if ($sex =='f') echo "checked"; ?> />
			 Female
			</td>
		 </tr>
		</table></td>

	<!-- Religion -->
		<td width="22%" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="2" cellspacing="0">
		 <tr>
			<td bgcolor="#D5E3EE"> <span class="style5">&nbsp; 3. &nbsp; RELIGION</span></td>
		 </tr>
		 <tr>
			<td bgcolor="#F9FFFB">
			<br />
			 <select name="d_religion" id="d_religion">
			<?php
				 $religion_obj = $person_obj->getReligion();
				 if (empty($religion)|| !$religion)
					 $religion=1;   # default religion, 'Not Indicated'
				 while ($result=$religion_obj->FetchRow()){
					 $selected='';
					 if ($religion==$result['religion_nr'])
						 $selected='selected';
					 echo '<option value="'.$result['religion_nr'].'" '.$selected.'>'.$result['religion_name']."</option> \n";
				 }
				 ?>
			 </select>
			</td>
		 </tr>
		</table></td>

	<!-- Age -->
				<td width="7%" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="2" cellspacing="0">
		 <tr>
			<td bgcolor="#D5E3EE"> <span class="style5">&nbsp; 4. &nbsp; AGE</span> </td>
		 </tr>
		</table>
					<span class="style17">
					<?php
			$date_birth_tmp = @formatDate2Local($date_birth,$date_format);
			if (($death_date!='0000-00-00')  && ($death_date!=""))
				$death_date_tmp = @formatDate2Local($death_date,$date_format);
			else
				$death_date_tmp='';
			$ageYear = $person_obj->getAge($date_birth_tmp,'',$death_date_tmp);

			if (is_numeric($ageYear) && ($ageYear>=0)){
				if ($ageYear<1){
					$ageMonth = intval($ageYear*12);
					$ageDay = (($ageYear*12)-$ageMonth) * 30;

					if(($ageMonth == 0) && (round($ageDay)<1)){
						# under 1 day
						if ($age_at_death)
							list($ageHours,$ageMinutes,$ageSec) = explode(":",$age_at_death);
						$ageMonth = ''; # set age in months as empty
						$ageDay = ''; # set age in days as empty
					}else{
						# under 1 year but above 1 day
						$ageMonth = intval($ageYear*12);
						$ageDay = round((($ageYear*12)-$ageMonth) * 30);
					}
					$ageYear = ''; # set age in years as empty
				}else{
					# above 1 year
					$ageYear = number_format($ageYear, 2);
				}
			}else{
			#		echo "false :  ageYear ='".$ageYear."' <br>\n";
			}
		 ?>
					</span></td>
		<td width="53%" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellspacing="2" cellpadding="0">
		 <tr>
			<td bgcolor="#D5E3EE"><table width="100%" border="0" cellpadding="1" cellspacing="1">
			 <tr>
			<td width="30%" bgcolor="#D5E3EE" class="style2">a. 1 year or above </td>
			<td width="35%" bgcolor="#D5E3EE" class="style2">b. under 1 year </td>
			<td width="35%" bgcolor="#D5E3EE" class="style2">c. under 1 day </td>
			 </tr>
			 <tr>
			<td align="center" valign="top" bgcolor="#F9FFFB" class="style2"> Completed Years <br />
		<!--	 <input name="death_age" type="text" class="style2" id="death_age" maxlength="3" size="5"
				value="<?php //if($death_date_tmp!='') echo number_format(floor($ageYear)); ?>"
				onblur="trimString(this); showforchild();" onkeypress="return keyPressHandler(event);" /> -->
		<!--	<input name="death_age" type="text" class="style2" id="death_age" maxlength="3" size="5"
				value="<?php //if ($age_at_death) echo $age_at_death; else echo number_format(floor($ageYear)); ?>"
				onblur="trimString(this); showforchild(); updateBdate2(); setestB();"
				onchange=" setestC();"
				onkeypress="return keyPressHandler(event);" /> -->
				<input type="hidden" name="date_birth3" id="date_birth3" value="<?= $date_birth ?>"/>
				<!--
				<input name="death_age" type="text" class="style2" id="death_age" maxlength="3" size="5"
				value="<?php if ($age_at_death) echo $age_at_death; else echo number_format(floor($ageYear)); ?>"
				onblur="trimString(this); showforchild(); updateBdate2(this);"
				onkeypress="return keyPressHandler(event);" />
				-->
				<input name="death_age" type="text" class="style2" id="death_age" maxlength="3" size="5"
				value="<?php if ($ageYear>=1) echo $age_at_death; else echo number_format(floor($ageYear)); ?>"
				onblur="trimString(this); showforchild(); updateBdate2(this);"
				onkeypress="return keyPressHandler(event);" />

			</td>
			<td bgcolor="#F9FFFB" class="style2"><table width="100%" border="0" cellpadding="2" cellspacing="2">
				 <tr>
				<td width="114" align="center" valign="top" class="style2"> Months <br />
				 <input name="death_months" type="text" class="style2" id="death_months" size="5"  value="<?= $ageMonth ?>" />
				</td>
				<td width="84" align="center" valign="top" class="style2"> Days <br />
				 <input name="death_days" type="text" class="style2" id="death_days" size="5"  value="<?= $ageDay ?>" />
				</td>
			 </tr>
			</table></td>
			<td bgcolor="#F9FFFB" class="style2"><table width="100%" border="0" cellpadding="2" cellspacing="2">
			 <tr>
				<td align="center" valign="top" class="style2"> Hrs <br />
					<!--<input name="hours" type="text" class="style2" id="hours" size="3" onblur="trimString(this); checkTime(this);" <?php if ($ageHours){  echo 'value="'.$ageHours.'"'; }else{ echo "readonly"; } ?> />-->
					<input name="hours" type="text" class="style2" id="hours" size="3" onblur="trimString(this); checkTime(this);" <?php if (($ageYear<1)&&($ageMonth<1)&&($ageDay<1)){  echo 'value="'.$ageHours.'"'; }else{ echo "readonly"; } ?> />
				</td>
				<td align="center" valign="top" class="style2"> Min <br />
					<!--<input name="minutes" type="text" class="style2" id="minutes" size="3" onblur="trimString(this); checkTime(this);" <?php if ($ageMinutes){  echo 'value="'.$ageMinutes.'"'; }else{ echo "readonly"; }?> />-->
					<input name="minutes" type="text" class="style2" id="minutes" size="3" onblur="trimString(this); checkTime(this);" <?php if (($ageYear<1)&&($ageMonth<1)&&($ageDay<1)){  echo 'value="'.$ageMinutes.'"'; }else{ echo "readonly"; }?> />
				</td>
				<td align="center" valign="top" class="style2"> Sec <br />
					<!--<input name="sec" type="text" class="style2" id="sec" size="3" onblur="trimString(this); checkTime(this);" <?php if ($ageSec){  echo 'value="'.$ageSec.'"'; }else{ echo "readonly"; }?> />-->
					<input name="sec" type="text" class="style2" id="sec" size="3" onblur="trimString(this); checkTime(this);" <?php if (($ageYear<1)&&($ageMonth<1)&&($ageDay<1)){  echo 'value="'.$ageSec.'"'; }else{ echo "readonly"; }?> />
				</td>
			 </tr>
				</table></td>
			 </tr>
			</table></td>
		 </tr>
			</table></td>
			 </tr>
			</table></td>
		 </tr>
		</table>
		<br />

	<!-- Place of Death -->
	<table width="90%" border="0" align="center" cellpadding="0" cellspacing="2">
	 <tr>
		<td bgcolor="#D5E3EE"><table width="100%" border="0" cellpadding="2" cellspacing="1">
			 <tr>
			<td colspan="3" bgcolor="#D5E3EE"><b class="style5">&nbsp;5. &nbsp; PLACE OF DEATH </b></td>
			 </tr>
			 <tr>
			 <?php
						if (empty($death_place_basic))
							$death_place_basic = $row['hosp_name'];

						if (empty($death_place_mun))
							$death_place_mun = $row['mun_name'];

						if (empty($death_place_prov))
							$death_place_prov = $row['prov_name'];
			 ?>
			<td width="34%" align="center" valign="top" bgcolor="#F9FFFB" class="style2">&nbsp;&nbsp;&nbsp;Street/Barangay <br />
		 <!--<input name="death_place_basic" id="death_place_basic" type="text" size=50 value="<?php echo $death_place_basic;?>" />-->
				 <textarea name="death_place_basic" id="death_place_basic" cols="45" rows="1" wrap="physical" ><? echo $death_place_basic ?></textarea>
		 <!--<b class="style13">&nbsp;&nbsp;&nbsp;<?php echo $row['hosp_name']."-".$row['mun_name']?></b> --></td>
			<td width="29%" align="center" valign="top" bgcolor="#F9FFFB" class="style2">City/Municipality <br />
		 <input name="death_place_mun" id="death_place_mun" type="hidden" value="<?=$death_place_mun;?>" />
		 <b class="style13"><?=$row['mun_name'];?></b> </td>
			<td width="37%" align="center" valign="top" bgcolor="#F9FFFB" class="style2">Province<br />
		 <input name="death_place_prov" id="death_place_prov" type="hidden" value="<?=$death_place_prov?>" />
		 <span class="style13"><b><?=$row['prov_name'];?></b> </span></td>
			 </tr>
			</table></td>
		 </tr>
		</table>
		<br />

	<!-- Date of Death -->
	<table width="90%" border="0" cellpadding="0" cellspacing="1">
	 <tr>
		<td width="55%" bgcolor="#D5E3EE"><table width="100%" border="0" cellspacing="1" cellpadding="0">
			 <tr>
			<td bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="2" cellspacing="1">
		 <tr>
			<td colspan="3" bgcolor="#D5E3EE" class="style5">&nbsp;6. &nbsp; DATE OF DEATH </td>
		 </tr>
		 <tr>
			<td class="style17">
			 <?php
			$phpfd=$date_format;
			$phpfd=str_replace("dd", "%d", strtolower($phpfd));
			$phpfd=str_replace("mm", "%m", strtolower($phpfd));
			$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

			if (($death_date!='0000-00-00')  && ($death_date!=""))
			//}
				$death_date = @formatDate2Local($death_date,$date_format);
				//if (($birth_date3)||((!$birth_date3)&&($death_age)))
				//	$hasdate=true;
			//}
			else {
				$death_date='';
				//$hasdate=false;
			}

			$sDeathDate= '<input name="death_date" type="text" size="15" maxlength=10 value="'.$death_date.'"'.
						'onFocus="this.select();" id = "death_date"
						onBlur="getAge2(this); IsValidDate(this,\'MM/dd/yyyy\'); UpdateDeathDate(0); updateBdate();"
						onChange="getAge2(this); IsValidDate(this,\'MM/dd/yyyy\'); "
						onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
						<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="death_date_trigger" style="cursor:pointer" >
						<font size=2.5>[';
			ob_start();
			 ?>
			 <script type="text/javascript">
			Calendar.setup ({
				inputField : "death_date", ifFormat : "<?php echo $phpfd?>",
				showsTime : false, button : "death_date_trigger",
				onClose: function(cal) { cal.hide();$('attended_to_date').value=$('death_date').value;enableOneDayOldText();updateBdate();},
				singleClick : true, step : 1});
				 </script>
			 <?php
			 $calendarSetup = ob_get_contents();
			 ob_end_clean();
			 $sDeathDate .= $calendarSetup;
			 $dfbuffer="LD_".strtr($date_format,".-/","phs");
			 $sDeathDate = $sDeathDate.$$dfbuffer.']';
			 ?>
			</td>
			<td><?= $sDeathDate ?></td>
		 </tr>
			</table></td>
			 </tr>
			</table></td>

	<!-- Citizenship -->
		<td width="45%" valign="top" bgcolor="#D5E3EE"><table width="100%" border="0" cellspacing="1" cellpadding="0">
			 <tr>
			<td bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="2" cellspacing="1">
			 <tr>
			<td bgcolor="#D5E3EE"> <span class="style5">&nbsp;7. &nbsp; CITIZENSHIP</span> </td>
			 </tr>
			 <tr>
			<td bgcolor="#F9FFFB">
			<select id="dcitizenship" name="dcitizenship">
				<?php
				# Retrieve record set of all citizenships
				$dcitizenship_obj = $obj_deathCert->getCitizenship();
				if (empty($dcitizenship)|| !$dcitizenship)
					$dcitizenship='PH';   # default citizenship, per users' request, is FILIPINO
				while ($result=$dcitizenship_obj ->FetchRow()){
					if (empty($result['citizenship']))
						$result['citizenship'] = $result['country_name'];
					$selected='';
					if ($dcitizenship==$result['country_code'])
						$selected='selected';
					echo '<option value="'.$result['country_code'].'" '.$selected.'>'.$result['citizenship']."</option> \n";
				}
				?>
			</select>
			 </td>
				</tr>
			 </table></td>
				</tr>
			 </table></td>
			</tr>
		 </table>
		 <br />

	<!-- Residence -->
	 <table width="90%" border="0" cellspacing="2" cellpadding="0">
		<tr>
		 <td bgcolor="#D5E3EE"><table width="100%" border="0" cellspacing="1" cellpadding="0">
				<tr>
		 <td bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="0" cellspacing="1">
			<tr>
			 <td colspan="3" bgcolor="#D5E3EE" class="style5">&nbsp;8. &nbsp; RESIDENCE</td>
			</tr>
			<tr>
			 <td width="32%" height="30" valign="top" bgcolor="#F9FFFB" class="style2"> House No., Street, Barangay: <br />
			<?php
				#ucwords(strtolower($street_name.", ".$brgy_row['brgy_name']))
				#$residence_basic = mb_strtoupper($street_name);
				$residence_basic = $street_name;
				$residence_brgy = $brgy_nr;
				$municity = $address_country->getMunicityByBrgy($residence_brgy);
				$residence_mun = $municity['mun_nr'];
				$prov = $address_country->getProvinceByBrgy($residence_mun);
				$residence_prov = $prov['prov_nr'];
			 ?>
			<input type="text" name="residence_basic" id="residence_basic" onblur="trimString(this);UpdateInformantAddress();" value="<?= $residence_basic ?>" size="50" />
			<select id="residence_brgy" name="residence_brgy" onchange="UpdateInformantAddress()">
			 <option value="0">-Select a Barangay-</option>
				<?php
				# Retrieve record set of all barangays
				$brgy_obj = $address_country->getBarangay();
				while ($result=$brgy_obj ->FetchRow()){
					$selected='';
					if ($residence_brgy==$result['brgy_nr'])
						$selected='selected';
					echo '<option value="'.$result['brgy_nr'].'" '.$selected.'>'.$result['brgy_name']."</option> \n";
				}
				 ?>
				</select>		   </td>
			 <td width="42%" valign="top" bgcolor="#F9FFFB" class="style2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				 City/Municipality: <br />
				 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="hidden" name="hosp_name" id="hosp_name" value="<?=$row['hosp_name']?>">
							<input type="hidden" name="mun_name" id="mun_name" value="<?=$row['mun_name']?>">
						 <select id="residence_mun" name="residence_mun" onchange="UpdateInformantAddress()">
							<?php
				# Retrieve record set of all municipalities/cities
				$mun_obj = $address_country->getMunicipal();
				if (empty($residence_mun)|| !$residence_mun)
					$residence_mun=$row['mun_nr'];   # default country of citizenship, 'Philippines'

								while ($result=$mun_obj ->FetchRow()){
					$selected='';
					if ($residence_mun==$result['mun_nr'])
					$selected='selected';
					echo '<option value="'.$result['mun_nr'].'" '.$selected.'>'.$result['mun_name']."</option> \n";
				}
				?>
					 </select></td>
			 <td width="26%" valign="top" bgcolor="#F9FFFB" class="style2"> Province: <br />
			 <select id="residence_prov" name="residence_prov" onchange="UpdateInformantAddress()">
				<?php
				# Retrieve record set of all provinces
				$prov_obj = $address_country->getProvince();
				if (empty($residence_prov)|| !$residence_prov)
					$residence_prov=$row['prov_nr'];   # default country of citizenship, 'Philippines'
				while ($result=$prov_obj ->FetchRow()){
					$selected='';
					if ($residence_prov==$result['prov_nr'])
						$selected='selected';
						echo '<option value="'.$result['prov_nr'].'" '.$selected.'>'.$result['prov_name']."</option> \n";
					}
				?>
			 </select>		    </td>
			 </tr>
				</table></td>
				 </tr>
				</table></td>
			 </tr>
			</table>
<?php
	if(($civil_status != 'single')&&($civil_status != 'married')&&($civil_status != 'widowed')&&($civil_status != 'unknown')){
		# Others
			$if_civilstatStyle = 'style="display:\'\'"';
	}else{
		# not Others
			$if_civilstatStyle = 'style="display:none"';
	}
?>
	<!-- Civil Status -->
		<table width="90%" align="center" border="0" cellspacing="1" cellpadding="0">
		 <tr>
			<td bgcolor="#D5E3EE"><table width="100%" border="0" cellpadding="0" cellspacing="1">
				 <tr>
				<td width="100%" height="76" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="1" cellspacing="1">
					 <tr>
			 <td width="55%" height="76" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="1" cellspacing="1">
							 <tr>
								 <td colspan="3" bgcolor="#D5E3EE" class="style5">&nbsp;9. &nbsp; CIVIL STATUS </td>
								 <td rowspan="4" bgcolor="#F9FFFB" class="style2" id="if_civilstatOthers" <?= $if_civilstatStyle ?>>
				 <table width="100%" border="0" cellpadding="1" cellspacing="1">
										 <tr>
											 <td bgcolor="#D5E3EE" class="style13">
							<?php
							if (($civil_status=="child")||($civil_status=="divorced")||($civil_status=="separated"))
								echo $LDCivilStatOther;
							else echo $LDCivilStatSel;
						 ?>
						</td>
										 </tr>
										 <tr>
											 <td bgcolor="#F9FFFB" class="style2">
							 <input name="d_civilstat_tmp" id="d_civilstat_tmp" type="radio" value="child"
							 onclick="DCivilStat(this,this);" <?php if($d_civilstat_tmp == 'child') echo "checked"; ?> />
												 Child &nbsp;<br />
												 <input name="d_civilstat_tmp" id="d_civilstat_tmp" type="radio" value="divorced"
							 onclick="DCivilStat(this,this);" <?php if($d_civilstat_tmp == 'divorced') echo "checked"; ?> />
												 Divorced &nbsp;<br />
												 <input name="d_civilstat_tmp" id="d_civilstat_tmp" type="radio" value="separated"
							 onclick="DCivilStat(this,this);" <?php if($d_civilstat_tmp == 'separated') echo "checked"; ?> />
												 Separated &nbsp;
						 </td>
										 </tr></table></td></tr>
					 <tr> <td bgcolor="#F9FFFB">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
										 <input name="civil_status" id="civil_status" type="hidden" value ="<?php if($civil_status) echo $civil_status; else echo "others";  ?>"/>
										 <tr>
											 <td bgcolor="#F8FAFC" class="style2"><input name="decease_civilstatus" id="decease_civilstatus" type="radio" value="single"
				 onclick="DCivilStat(this,'');"  <?php if($civil_status == 'single') echo "checked"; ?> />
												 Single </td>
											 <td bgcolor="#F8FAFC" class="style2"><input name="decease_civilstatus" id="decease_civilstatus" type="radio" value="widowed"
					onclick="DCivilStat(this,'');"  <?php if($civil_status == 'widowed') echo "checked"; ?> />
												 Widowed </td>
											 <td bgcolor="#F8FAFC" class="style2"><input name="decease_civilstatus" id="decease_civilstatus" type="radio" value="unknown"
					onclick="DCivilStat(this,'');"  <?php if($civil_status == 'unknown') echo "checked"; ?> />
												 Unknown </td>
										 </tr>
										 <tr>
											 <td bgcolor="#F8FAFC" class="style2"><input name="decease_civilstatus" id="decease_civilstatus" type="radio" value="married"
					onclick="DCivilStat(this,'');"  <?php if($civil_status == 'married') echo "checked"; ?>/>
												 Married </td>
											 <td bgcolor="#F8FAFC" colspan="2" class="style2"><input name="decease_civilstatus" id="decease_civilstatus" type="radio" value="others"
					onclick="DCivilStat(this,this);"  <?php if($civil_status == 'others') echo "checked"; ?> />
												 Others </td>
										 </tr>
								 </table>

					</td></tr></table></td>

			 <!-- Occupation -->
				<td width="45%" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="1" cellspacing="1">
				 <tr>
					<td bgcolor="#D5E3EE"> <span class="style5">&nbsp;10. &nbsp; OCCUPATION</span></td>
				 </tr>
				 <tr>
					<td bgcolor="#F9FFFB">
				 <select name = "occupation" class="style2" id="occupation">
						<?php
						 $occupation_obj = $person_obj->getOccupation($occupation);
					 if($occupation_obj){
						while($result=$occupation_obj->FetchRow()){
						$selected = '';
						if($occupation == $result['occupation_nr'])
							$selected = 'selected';
						echo '<option value="'.$result['occupation_nr'].'" '.$selected.'>'.$result['occupation_name']."</option> \n";
					}
				 }
					 ?>
				 </select>
			</td>
				 </tr>
				</table></td>

				 </tr>
				</table></td>
			 </tr>
			</table></td><br />
		 </tr>

		</table></td><br />
	 </tr>
	</table></td>
 </tr>
</table>
<br />

<!-- FOR AGES 0 TO 7 DAYS  -->
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="1">
 <thead>
	<tr>
	 <td width="12%" style="cursor: pointer; cursor: hand;">
	<img src="images/ok.gif" onclick=<?php if (((number_format(floor($ageYear))) < "1")&&($death_date)) echo "expandcontract('Row1','')"; ?> >
	<img src="images/x.gif" onclick="expandcontract('Row1','none')"></td>
	 <td bgcolor="#F9FFFB">
		<center>
		 <table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			 <td width="6%" align="right"><img src="images/brdr_blgry_03.gif" width="31" height="26" /></td>
			 <td width="94%" bgcolor="#A0C2DA"><span class="style9">For Ages 0 to 7 days (Click the check button on the left side.) </span></td>
			</tr>
		 </table>
		</center>
	 </td>
	</tr>
 </thead>
 <?php
	if ( ($death_date_tmp=='') ||
			( (intval($ageYear)==0) && (intval($ageMonth)==0) && (intval($ageDay)<=7))
		){
		# age at death is less than or equal to 7 days
			$Row1Style = 'style="display:\'\'"';
			$Row2Style = 'style="display:none"';
			$age0to7days = 1;
	}else{
		# age at death is more than 7 days
			$Row1Style = 'style="display:none"';
			$Row2Style = 'style="display:\'\'"';
			$age0to7days = 0;
	}
	?>
 <tbody id="Row1" <?= $Row1Style ?>>

	<!-- START FOR AGES 0 TO 7 DAYS -->
	<tr>
	 <td colspan="2">
	<table width="100%" bgcolor="#A0C2DA" border="0" cellpadding="2" cellspacing="1">
	 <tr>

		<!-- Date of Birth -->
		<td width="33%" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="2" cellspacing="0">
		 <tr bgcolor="#CCCCFF">
		<td colspan="3" bgcolor="#D5E3EE"> <span class="style5">&nbsp;11. &nbsp; DATE OF BIRTH</span> </td>
		 </tr>
		 <tr bgcolor="#EAEAFF">
		<td align="center" bgcolor="#FFFFFF" class="style2"> (day)  </td>
		<td align="center" bgcolor="#FFFFFF" class="style2"> (month)  </td>
		<td align="center" bgcolor="#FFFFFF" class="style2"> (year)  </td>
		 </tr>
		 <tr bgcolor="#EAEAFF">
		<td align="center" bgcolor="#FFFFFF" class="style2">
		 <input type="hidden" name="date_birth2" id="date_birth2" value="<?=date("m/d/Y",strtotime($date_birth))?>" />
		 <?php
				if ($date_birth!="0000-00-00") $birthDay = date("d",strtotime($date_birth));
				else {
					//if (($death_age)&&(($death_age!="0")||($death_age!=" "))) $birthDay = "0";
					//else $birthDay = date("d")-7;
					$birthDay = "0";
				}
				$dategen->genDay('updateBdate()','birth_day', $birthDay);
			?>
		</td>
		<td align="center" bgcolor="#FFFFFF" class="style2">
		 <?php
				if ($date_birth!="0000-00-00") $birthMonth = date("m",strtotime($date_birth));
				else {
					//if ($death_age) $birthMonth = "0";
					//else $birthMonth = date("m");
					$birthMonth = "0";
				}
				$dategen->genMonth('updateBdate()','birth_month','long', $birthMonth);
			?>
		</td>
		<td align="center" bgcolor="#FFFFFF" class="style2">
		 <?php
				if ($date_birth!="0000-00-00") $birthYear = date("Y",strtotime($date_birth));
				else {
					//if ($death_age) $birthYear = "0";
					//else $birthYear = date("Y");
					$birthYear = "0";
				}
				$dategen->genYear('updateBdate()','birth_year','100',$birthYear);
			?>
		</td>
		 </tr>
			</table></td>

		<!-- Age of the Mother -->
		<td width="25%" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="2" cellspacing="0">
		 <tr bgcolor="#CCCCFF">
		<td bgcolor="#D5E3EE"> <span class="style5">&nbsp;12. &nbsp; AGE OF THE MOTHER</span> </td>
		 </tr>
		 <tr bgcolor="#EAEAFF">
		<td align="center" bgcolor="#FFFFFF"><input name="m_age" id="m_age" type="text" maxlength="3" size="5"
			 onblur="trimString(this); chkInteger(this,countingNumber);" onkeypress="return keyPressHandler(event);"
			 value="<?= $m_age ?>" /></td>
		 </tr>
		 <tr bgcolor="#EAEAFF">
		<td align="center" bgcolor="#FFFFFF">&nbsp;</td>
		 </tr>
		</table></td>

		<!-- Method of Delivery -->
		<td width="*" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="2" cellspacing="0">
		 <tr bgcolor="#CCCCFF">
		<td bgcolor="#D5E3EE"> <span class="style5">&nbsp;13. &nbsp; METHOD OF DELIVERY</span> </td>
				 </tr>
		 <tr bgcolor="#EAEAFF">
			<td bgcolor="#FFFFFF" class="style2">
			 <input type="hidden" name="delivery_method"  id="delivery_method"
				 value="<?php if ($delivery_method) echo $delivery_method; else echo "1"; ?>">
			 <input name="delivery_method_tmp"  id="delivery_method_tmp" type="radio" value="1"
				 onclick="methodOfDelivery(this);" <?php if ((!$delivery_method)||($delivery_method_tmp=="1")) echo "checked"; ?>>
				Normal; spontaeneous vertex
			</td>
		 </tr>
		 <tr bgcolor="#EAEAFF">
			<td bgcolor="#FFFFFF" class="style2">
			 <input name="delivery_method_tmp" id="delivery_method_tmp" type="radio" value="2"
				 onclick="methodOfDelivery(this);" <?php if ($delivery_method_tmp=="2") echo "checked"; ?>>
			Others (Specify) &nbsp;
			 <input type="text" name="delivery_method_info" id="delivery_method_info"
				 onblur="trimString(this); $('delivery_method').value= '2 - '+this.value;"
			 value="<?php if ($delivery_method_tmp=="2") echo $delivery_method_info; ?>">
			</td>
		 </tr>
			</table></td>
	<!-- End of Method of Delivery -->
		 </tr>
		</table></td>
		 </tr>

	<!-- Length of Pregnancy -->
	 <tr>
		<td colspan="2"><table width="100%" bgcolor="#A0C2DA" border="0" cellpadding="2" cellspacing="1">
		<tr bgcolor="#FFDDFF">
		 <td bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="2" cellspacing="0">
			<tr>
			 <td bgcolor="#D5E3EE" width="30%">
			<span class="style5">&nbsp;14. &nbsp; LENGTH OF PREGNANCY :</span>
			 </td>
			 <td bgcolor="#FFFFFF" class="style2">
				<input name="pregnancy_length" type="text" id="pregnancy_length"
				onblur="trimString(this); chkInteger(this,countingNumber);"
				value="<?= $pregnancy_length ?>" maxlength="4" onkeypress="return keyPressHandler(event);" />&nbsp; completed weeks
				 </td>
			</tr>
		 </table></td>
		</tr>
		 </table></td>
		</tr>

	<!-- Type of Birth -->
			<tr>
		 <td colspan="2"><table width="100%" bgcolor="#A0C2DA" border="0" cellpadding="2" cellspacing="1">
		<tr>
		 <td width="40%" bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="2" cellspacing="0">
					<tr bgcolor="#CCCCFF">
			 <td bgcolor="#D5E3EE"> <span class="style5">&nbsp;15. &nbsp; TYPE OF BIRTH</span> </td>
			</tr>
			<tr bgcolor="#EAEAFF">
			 <td bgcolor="#FFFFFF">
			<input type="hidden" name="birth_type" id="birth_type" value="<?php if ($birth_type) echo $birth_type; else echo "1" ?>">
			 &nbsp;<span class="style2">&nbsp;&nbsp;&nbsp;
			<input name="birth_type_tmp" id="birth_type_tmp" type="radio" value="1"
				onclick="typeOfBirth(this)" <?php if (($birth_type=='1')||(!$birth_type)) echo "checked"; ?>>
			Single
			<input name="birth_type_tmp" id="birth_type_tmp" type="radio" value="2"
				onclick="typeOfBirth(this)" <?php if ($birth_type=='2') echo "checked"; ?>>
			Twin
			<input name="birth_type_tmp" id="birth_type_tmp" type="radio" value="3"
				onclick="typeOfBirth(this)" <?php if ($birth_type=='3') echo "checked"; ?>>
				Triplet, etc. </span>
			 </td>
				</tr>
		 </table></td>

	<!-- If Multiple Birth -->
		 <td width="*" bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="2" cellspacing="0">
			<tr bgcolor="#CCCCFF">
			 <td bgcolor="#D5E3EE"> <span class="style5">&nbsp;16. &nbsp; IF MULTIPLE BIRTH, CHILD WAS </span> </td>
			</tr>
			 <tr bgcolor="#EAEAFF">
				<td bgcolor="#FFFFFF">
					<?php
						#added by VAN 08-09-08
						if (($birth_type!=1)&&(empty($birth_rank)))
							$birth_rank = "first";
				?>
				 <input type="hidden" name="birth_rank" id="birth_rank" value="<?= $birth_rank ?>">
				&nbsp;&nbsp;&nbsp;
				 <span class="style2">&nbsp;
				 <input name="birth_rank_tmp" id="birth_rank_tmp" type="radio" onclick="rankOfBirth(this);"
						value="first" <?php if ($birth_rank=='first') echo "checked";
											if (($birth_type==1)||(!$birth_type)) echo "disabled"; ?> />
				First
				 <input name="birth_rank_tmp" id="birth_rank_tmp" type="radio" onclick="rankOfBirth(this)"
						value="second" <?php if ($birth_rank=='second') echo "checked";
											if (($birth_type==1)||(!$birth_type)) echo "disabled"; ?> />
				Second

				<input name="birth_rank_tmp" id="birth_rank_tmp" type="radio" onclick="rankOfBirth(this)"
						value="third" <?php if (($birth_rank!='first')&&($birth_rank!='second')) echo "checked ";
											if (($birth_type==3)||(!$birth_type)) echo "disabled"; ?> />
				Others (Specify)&nbsp;
				<input type="text" name="birth_rank_others" id="birth_rank_others"
						onblur="trimString(this); convertToWords(this); updateBirthRank();"
					<?php if (($birth_rank!='first')&&($birth_rank!='second')) echo 'value="'.$birth_rank.'"';
								else echo "disabled"; ?> />
</span>
				</td>
			 </tr>
			</table></td>
			 </tr>
			</table></td>
				 </tr>

	<!-- START FOR CAUSES OF DEATH FOR AGES 0 TO 7 DAYS -->
			 <tr>
			<td colspan="2"><table width="100%" bgcolor="#A0C2DA" border="0" cellpadding="2" cellspacing="1">
			 <tr bgcolor="#FFDDFF">
			<td bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="2" cellspacing="0">
			 <tr>
				<td colspan="2" bgcolor="#D5E3EE">
				 <span class="style5">&nbsp;17. &nbsp; CAUSES OF DEATH </span>
				</td>
			 </tr>
			 <tr>
				<td width="33%" bgcolor="#FFFFFF" class="style2">
				 a. Main disease/condition of infant &nbsp;
				</td>
				<td width="67%" bgcolor="#FFFFFF" class="style2">
					<input name="cause1" id="cause1" type="text" size="50" value="<?= $tmp_death_cause['cause1'] ?>" />
				</td>
			 </tr>
			 <tr>
					<td bgcolor="#FFFFFF" class="style2">
					b. Other diseases/conditions of infant
				</td>
				 <td bgcolor="#FFFFFF" class="style2">
					<input name="cause2" id="cause2" type="text" size="50" value="<?= $tmp_death_cause['cause2'] ?>" />
				 </td>
			 </tr>
			 <tr>
				 <td bgcolor="#FFFFFF" class="style2">
				c. Main maternal disease/condition of affecting infant
				 </td>
				 <td bgcolor="#FFFFFF" class="style2">
					<input name="cause3" id="cause3" type="type" size="50" value="<?= $tmp_death_cause['cause3'] ?>" />
				 </td>
			 </tr>
			 <tr>
				 <td bgcolor="#FFFFFF" class="style2">
				d. Other maternal disease/condition of affecting infant
				 </td>
				 <td bgcolor="#FFFFFF" class="style2">
					<input name="cause4" id="cause4" type="text" size="50" value="<?= $tmp_death_cause['cause4'] ?>" />
				 </td>
			 </tr>
			 <tr>
				 <td bgcolor="#FFFFFF" class="style2">
				e. Other relevant circumstances
				 </td>
				 <td bgcolor="#FFFFFF" class="style2">
					<input name="cause5" id="cause5" type="text" size="50" value="<?= $tmp_death_cause['cause5'] ?>" />
				 </td>
				</tr>
			 </table></td>
			</tr>
			 </table></td>
			<!-- END FOR CAUSES OF DEATH FOR AGES 0 TO 7 DAYS -->
			 </tr>
 </tbody>
</table><br /><!-- END - FOR AGES 0 TO 7 DAYS  -->

<!-- CAUSES OF DEATH //ABOVE 7 DAYS -->
<table width="90%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#A0C2DA" id="Row2" <?= $Row2Style ?>>
 <tr>
	<td height="120"><table width="100%" border="0" cellpadding="1" cellspacing="1">
	 <tr>
		<td colspan="3" bgcolor="#D5E3EE" class="style5">&nbsp;17. &nbsp; CAUSES OF DEATH </td>
	 </tr>
	 <tr>
	<td width="26%" bgcolor="#FFFFFF" class="style2">
		I.
		Immediate cause &nbsp;&nbsp;&nbsp; : &nbsp;&nbsp; a.	</td>
	<td width="25%"  bgcolor="#FFFFFF" class="style2">
		<input name="cause6" id="cause6" type="text" value ="<?= $tmp_death_cause['cause6'] ?>" readonly="1" />
	</td>
	<td width="49%" valign="top" bgcolor="#FFFFFF" class="style2">Interval Between Onset and Death<br />
		<input name="interval1" id="interval1" type="text" value="<?= $tmp_death_cause['interval1'] ?>" readonly="1">
	</td>
	 </tr>
	 <tr>
	<td bgcolor="#FFFFFF" class="style2">&nbsp;&nbsp;&nbsp;
		Antecendent cause : &nbsp;&nbsp; b.
	</td>
	<td bgcolor="#FFFFFF" class="style2">
		<input name="cause7" id="cause7" type="text" value="<?= $tmp_death_cause['cause7'] ?>" readonly="1" />
	</td>
	<td width="49%" vlign="top" bgcolor="#FFFFFF" class="style2">
		<input name="interval2" id="interval2" type="text" value="<?= $tmp_death_cause['interval2'] ?>" readonly="1">
	</td>
	 </tr>
	 <tr>
	<td bgcolor="#FFFFFF" class="style2">&nbsp;&nbsp;&nbsp;
		Underlying cause &nbsp;&nbsp; : &nbsp;&nbsp; c.
		</td>
	<td bgcolor="#FFFFFF" class="style2">
		<input name="cause8" id="cause8" type="text" value="<?= $tmp_death_cause['cause8'] ?>" readonly="1"/>
	</td>
	<td width="49%" vlign="top" bgcolor="#FFFFFF" class="style2">
		<input name="interval3" id="interval3" type="text" value="<?= $tmp_death_cause['interval3'] ?>" readonly="1">
	</td>
	 </tr>
	 <tr bgcolor="#FFFFFF">
	<td class="style2">
		II.
		Other significant conditions contributing to death:</td>
	<td class="style2">
		<input name="cause9" id="cause9" type="text" value="<?= $tmp_death_cause['cause9'] ?>" readonly="1" />
	</td>
	 </tr>
	</table></td>
 </tr>
</table><br />

<!-- DEATH BY NON-NATURAL CAUSES -->
<table width="90%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#A0C2DA">
	<tr>
		<td bgcolor="#D5E3EE" class="style5">&nbsp;18. &nbsp; DEATH BY NON-NATURAL CAUSES
		<input type="checkbox" name="unnatural_death" id="unnatural_death" value="1" <?php if($death_manner_tmp) echo "checked"; else echo ""; ?> onchange="checkUnnatural();" />
	 </td>
	</tr>
	<tr>
	 <td bgcolor="#F9FFFB" class="style2">a. Manner of Death </td>
	</tr>
	<tr>
	 <td bgcolor="#D5E3EE" class="style2"><table width="100%" border="0" cellpadding="1" cellspacing="0">
		<tr>
		 <td width="20%" bgcolor="#F9FFFB" class="style2">
			 <input name="death_manner" id="death_manner" type="hidden" value="<?php if($death_manner) echo $death_manner; else echo "1"; ?>" />
			 <input name="death_manner_tmp" id="death_manner_tmp" type="radio"
				onclick="mannerOfDeath(this);" value="1" <?php if($death_manner_tmp=='1') echo "checked"; ?>>
						1. Homicide      </td>
			<td width="20%" bgcolor="#F9FFFB" class="style2">
			 <input name="death_manner_tmp" id="death_manner_tmp" type="radio" onclick="mannerOfDeath(this);"
				value="2" <?php if($death_manner_tmp=='2') echo "checked"; ?>>
						2. Suicide      </td>
			<td width="20%" bgcolor="#F9FFFB" class="style2">
				<input name="death_manner_tmp" id="death_manner_tmp" type="radio" onclick="mannerOfDeath(this);"
			value="3" <?php if($death_manner_tmp=='3') echo "checked"; ?>>
						3. Accident
						<input type="text" name="death_manner_accident" id="death_manner_accident"
			onblur="trimString(this); $('death_manner').value='3 - '+this.value;"
			<?php if($death_manner_tmp=='3') echo 'value="'.$death_manner_accident.'"';
											 else echo "disabled" ?> /></td>
			<td width="*" bgcolor="#F9FFFB" class="style2">
				<input name="death_manner_tmp" id="death_manner_tmp" type="radio" onclick="mannerOfDeath(this);"
			value="4" <?php if($death_manner_tmp=='4') echo "checked"; ?>>
						Others (Specify) &nbsp;
				<input name="death_manner_info" type="text" size="40" id="death_manner_info"
			onblur="trimString(this); $('death_manner').value='4 - '+this.value;"
			<?php if($death_manner_tmp=='4') echo 'value="'.$death_manner_info.'"'; else echo "disabled" ?> >      </td>
		 </tr>
		</table></td>
	 </tr>

	 <tr>
		<td bgcolor="#F9FFFB" class="style2">
			b. Place of Occurence (e.g. home, farm, factory, street, sea, etc.)
			<input name="place_occurrence" type="text" size="40" class="style2" id="place_occurrence"
				onblur="trimString(this);" value="<?= $place_occurrence ?>" />
		</td>
	 </tr>
</table><br />
<!-- ENDS -//DEATH BY NON-NATURAL CAUSES -->

<?php
	if($attendant_type!='4'){
		# had Attendant
			$if_attendantStyle = 'style="display:\'\'"';
	}else{
		# no Attendant
			$if_attendantStyle = 'style="display:none"';
	}
?>

<!-- ATTENDANT -->
<table width="90%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#A0C2DA">
 <tr>
	<td colspan="2" bgcolor="#D5E3EE" class="style5">&nbsp;19. &nbsp; ATTENDANT </td>
	<td rowspan="4" bgcolor="#F9FFFB" class="style5" id="if_attendant" <?= $if_attendantStyle ?>>
	 <table width="100%" border="0" cellpadding="2" cellspacing="2">
		<tr>
		 <td colspan="2" bgcolor="#D5E3EE" class="style5">If attended, state duration : </td>
		</tr>
		<tr>
		 <td class="style5" width="8%" bgcolor="#F9FFFB"> From </td>
		 <td width="92%" bgcolor="#F9FFFB" style="font-weight:100">

	<?php
		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

	if (($attended_from_date!='0000-00-00')  && ($attended_from_date!="")){
		$attended_from_date = @formatDate2Local($attended_from_date,$date_format);
	}else{
		if ($admission_dt)
			$attended_from_date = @formatDate2Local($admission_dt,$date_format);
		elseif ($encounter_date!='0000-00-00 00:00:00')
			$attended_from_date = @formatDate2Local($encounter_date,$date_format);
		else
			$attended_from_date='';    
	}
	$sDateJS= '<input name="attended_from_date" type="text" size="15" maxlength=10 value="'.$attended_from_date.'" class="style2"'.
				'onFocus="this.select();"
				id = "attended_from_date"
				onBlur="IsValidDate(this,\''.$date_format.'\');"
				onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').'
						id="attended_from_date_trigger" style="cursor:pointer" >
				<font size=2.5>[';
	ob_start();
	?>

	<script type="text/javascript">
	Calendar.setup ({
			inputField : "attended_from_date", ifFormat : "<?php echo $phpfd?>",
			showsTime : false, button : "attended_from_date_trigger", singleClick : true, step : 1
	});
	</script>

	<?php
		$calendarSetup = ob_get_contents();
		ob_end_clean();
		$sDateJS .= $calendarSetup;
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDateJS = $sDateJS.$$dfbuffer.']';
	 ?>

		<?= $sDateJS ?>
		 </td>
		</tr>
		<tr>
		 <td class="style5" bgcolor="#F9FFFB"> To  </td>
		 <td bgcolor="#F9FFFB" style="font-weight:100">

		<?php
		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

		if (($attended_to_date!='0000-00-00')  && ($attended_to_date!=""))
			$attended_to_date = @formatDate2Local($attended_to_date,$date_format);
		else{
		if ($death_date)
			$attended_to_date = $death_date;
		else
			$attended_to_date='';
		}
		$sDateJS= '<input name="attended_to_date" type="text" size="15" maxlength=10 value="'.$attended_to_date.'" class="style2"'.
				'onFocus="this.select();"
				id = "attended_to_date"
				onBlur="getAge2(this); IsValidDate(this,\'MM/dd/yyyy\'); UpdateDeathDate(1);"
				onChange="getAge2(this); IsValidDate(this,\'MM/dd/yyyy\'); "
				onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').'
						id="attended_to_date_trigger" style="cursor:pointer" >
				<font size=2.5>[';
		ob_start();
		?>

		<script type="text/javascript">
		Calendar.setup ({
			inputField : "attended_to_date", ifFormat : "<?php echo $phpfd?>",
			showsTime : false, button : "attended_to_date_trigger",
			onClose: function(cal) { cal.hide();$('death_date').value=$('attended_to_date').value}, singleClick : true, step : 1
		});
		</script>

		<?php
		$calendarSetup = ob_get_contents();
		ob_end_clean();
		$sDateJS .= $calendarSetup;
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDateJS = $sDateJS.$$dfbuffer.']';
		?>

		<?= $sDateJS ?>
		 </td>
		</tr>
		<tr>
		 <td colspan="2"bgcolor="#F9FFFB" class="style5">&nbsp;  </td>
		</tr>
	 </table></td>
	</tr>
	<tr>
	 <td width="25%" bgcolor="#F9FFFB" class="style2">
			<input name="attendant_type" id="attendant_type" type="hidden"
				value="<?php if($attendant_type) echo $attendant_type; else echo "3 - Hospital Authority";  ?>">
		<input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)"
			value="1" <?php if($attendant_type_tmp == '1') echo "checked"; ?>>
			1. Private Physician
	 </td>
	 <td width="45%" bgcolor="#F9FFFB" class="style2">
				<input name="attendant_type_tmp" id="attendant_type_tmp"  onclick="typeOfAttendant(this)" type="radio"
				value="4" <?php if($attendant_type_tmp =='4'){ echo "checked";} ?>>
			4. None
	 </td>
	</tr>
	<tr>
	 <td bgcolor="#F9FFFB" class="style2">
			<input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)"
				value="2" <?php if($attendant_type_tmp  == '2') echo "checked"; ?>>
			2. Public Health Officer
	 </td>
	 <td bgcolor="#F9FFFB" class="style2">
			<input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)"
				value="5" <?php if($attendant_type_tmp == '5') echo "checked"; ?>>
			5. Others (Specify)
			<input type="text" size="50" name="attendant_type_others" id="attendant_type_others" onblur="trimString(this); $('attendant_type').value= '5 - '+this.value;" <?php if ($attendant_type_tmp ==5) echo 'value="'.$attendant_type_others.'"'; else echo "disabled"; ?>>
		</td>
	</tr>
	<tr>
		<td bgcolor="#F9FFFB" class="style2">
			<input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="3" <?php if(($attendant_type_tmp == '3')||(!$attendant_type_tmp)) echo "checked"; ?>>
			3. Hospital Authority
		</td>
		<td bgcolor="#F9FFFB" class="style2">&nbsp;</td>
	</tr>
</table><br /><!--ENDS //ATTENDANT -->

<!-- CERTIFICATION OF DEATH -->
<table width="90%" border="0" align="center" cellpadding="1" cellspacing="0" bgcolor="#A0C2DA">
 <!-- DWLayoutTable -->
 <tr>
	<td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
	 <!-- DWLayoutTable -->
	 <tr>
		<td width="1202" bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="2" cellspacing="1">
	 <tr>
		<td bgcolor="#D5E3EE" class="style5">&nbsp;20. &nbsp; CERTIFICATION OF DEATH </td>
	 </tr>
	 <tr>
		<td bgcolor="#F9FFFB" class="style2">
			I hereby certify that the foregoing particulars are correct as near as same can be ascertained and I further certify that I
		</td>
	 </tr>
	 <tr>
		<td bgcolor="#F9FFFB" class="style2">
		 <input name="death_cert_attended" id="death_cert_attended" type="radio" value="0"
				onclick="certificationOfDeath(this);" <?php if(($death_cert_attended=='0')||(!$death_cert_attended)) echo "checked"; ?> />
					have not attended the deceased
		</td>
	 </tr>
	 <tr>
		<td bgcolor="#F9FFFB" class="style2">
			 <?php
			#if (($death_time !='00:00:00') && ($death_time!="")){
						if ($death_time!=""){
				$death_time_tmp = convert24HourTo12HourLocal($death_time);
				$death_time_array = explode(' ',$death_time_tmp);
				$death_time = $death_time_array[0];
				$death_time_period = $death_time_array[1];
			}else{
				$death_time = '';
			}
		?>
			<input name="death_cert_attended" id="death_cert_attended" type="radio" value="1"
			onclick="certificationOfDeath(this);" <?php if($death_cert_attended == '1') echo "checked"; ?> />
					have attended the deceased and that death occured at
			<input name="death_time" type="text" class="style2" id="death_time" size="10" onchange="setFormatTime(this);"
			value="<?= $death_time ?>" onkeypress="return keyPressHandler(event);"
		 <?php if(($death_cert_attended=='0')||(!$death_cert_attended)) echo "disabled"; ?> />
			<select id="selAMPM" name="selAMPM" <?php if(($death_cert_attended=='0')||(!$death_cert_attended)) echo "disabled"; ?>>
		<option value="A.M." <?php if ($death_time_period == 'AM') echo "selected";?> >A.M.</option>
		<option value="P.M." <?php if ($death_time_period == 'PM') echo "selected";?> >P.M.</option>
			</select>
				on the date indicated above.
		</td>
		 </tr>
		</table></td>
	 </tr>
	 <tr>
		<td height="167" valign="top" bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="2" cellspacing="2">
	 <tr>
			<td colspan="2" bgcolor="#D5E3EE" class="style2">Signature</td>
	 </tr>
	 <tr>
		<td bgcolor="#F9FFFB" class="style2"> Name in Print </td>
		<td bgcolor="#F9FFFB" class="style2">
		 <!-- <select id="attendant_name" name="attendant_name" onchange="showDrLate()"> -->
		 <select id="attendant_name" name="attendant_name">
			<option value="0">-Select a Doctor-</option>
		 <?php
			# Retrieve record set of all doctors
			$doctor_obj = $pers_obj->getDoctors(1);

			while ($result=$doctor_obj->FetchRow()){
				$selected='';
				if ($attendant_name==$result['personell_nr'])
					$selected='selected';

				$middleInitial = "";
				if (trim($result['name_middle'])!=""){
					$thisMI=split(" ",$result['name_middle']);
					foreach($thisMI as $value){
						if (!trim($value)=="")
						$middleInitial .= $value[0];
					}
					if (trim($middleInitial)!="")
					$middleInitial .= ". ";
				}
				#$doctor_name = $result["name_first"]." ".$result["name_2"]." ".$middleInitial.$result["name_last"];
				#$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
				$doctor_name = trim($result["name_last"]).", ".trim($result["name_first"])." ".$middleInitial; #substr(trim($drInfo["name_middle"]),0,1).$dot;
				$doctor_name = ucwords(strtolower($doctor_name)).", MD";

				echo '<option value="'.$result['personell_nr'].'" '.$selected.'>'.$doctor_name."</option> \n";
			}
			?>
		 </select>
		</td>
	 </tr>
	 <tr>
		<td bgcolor="#F9FFFB" class="style2"> Title or Position </td>
		<td bgcolor="#F9FFFB" class="style2">
		 <input name="attendant_title" type="text" class="style2" id="attendant_title" value="<?= $attendant_title ?>" size="50" />
		</td>
	 </tr>
	 <tr>
		<td bgcolor="#F9FFFB" class="style2"> Address<br /></td>
		<td bgcolor="#F9FFFB" class="style2">
			<?php
						if (empty($attendant_address)){
							$attendant_address = $row['hosp_name'].", ".$row['brgy_name'].", ".$row['mun_name'];
						}
			?>
		 <textarea name="attendant_address" cols="50" class="style2" id="attendant_address"><?= $attendant_address ?></textarea>
		</td>
	 </tr>
	 <tr>
		<td width="7%" bgcolor="#F9FFFB" class="style2"> Date </td>
		<td width="93%" bgcolor="#F9FFFB" class="style2">
			<?php
			$phpfd=$date_format;
			$phpfd=str_replace("dd", "%d", strtolower($phpfd));
			$phpfd=str_replace("mm", "%m", strtolower($phpfd));
			$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

			if (($attendant_date_sign!='0000-00-00') && ($attendant_date_sign!=""))
				$attendant_date_sign = @formatDate2Local($attendant_date_sign,$date_format);
			else
				$attendant_date_sign='';

			$sDateAttend= '<input name="attendant_date_sign" type="text" size="15"
					maxlength=10 value="'.$attendant_date_sign.'"'.
					'onFocus="this.select();"
					id = "attendant_date_sign"
					onBlur="IsValidDate(this,\''.$date_format.'\'); "
					onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
					<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="attendant_date_sign_trigger" style="cursor:pointer" >
					<font size=2.5>[';
				ob_start();
			?>
		 <script type="text/javascript">
			Calendar.setup ({
					inputField : "attendant_date_sign", ifFormat : "<?php echo $phpfd?>",
						showsTime : false, button : "attendant_date_sign_trigger",
						onClose: function(cal) { cal.hide();}, singleClick : true, step : 1
			});
		 </script>
		 <?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();
			$sDateAttend .= $calendarSetup;
			$dfbuffer="LD_".strtr($date_format,".-/","phs");
			$sDateAttend = $sDateAttend.$$dfbuffer.']';
			?>
		 <?= $sDateAttend ?>
			</td>
		 </tr>
		</table></td>
	 </tr>
	</table></td>
 </tr>
</table><br /><!--ENDS -  //CERTIFICATION OF DEATH -->

<table width="90%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#A0C2DA">
 <tr>
	<!-- CORPSE DISPOSAL -->
	<td width="38%" height="129" valign="top" bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="2" cellspacing="1">
	 <tr>
		<td bgcolor="#D5E3EE" class="style5">&nbsp;21. &nbsp; CORPSE DISPOSAL </td>
	 </tr>
	 <tr>
		<td bgcolor="#F9FFFB" class="style2">
		<input type="hidden" name="corpse_disposal" id="corpse_disposal" value="<?php if($corpse_disposal) echo $corpse_disposal; else echo "1"; ?>" />
		<input name="corpse_disposal_tmp" id="corpse_disposal_tmp" type="radio" onclick="typeOfDisposal(this)" value="1" <?php if($corpse_disposal_tmp == '1') echo "checked"; ?> />
			1. Burial
	</td>
	 </tr>
	 <tr>
		<td bgcolor="#F9FFFB" class="style2">
				<input name="corpse_disposal_tmp" id="corpse_disposal_tmp" type="radio" onclick="typeOfDisposal(this)" value="2" <?php if($corpse_disposal_tmp == '2') echo "checked"; ?> />
						2. Cremation
		</td>
	 </tr>
	 <tr>
		<td bgcolor="#F9FFFB" class="style2">
				<input name="corpse_disposal_tmp" id="corpse_disposal_tmp" type="radio" onclick="typeOfDisposal(this)" value="3" <?php if($corpse_disposal_tmp == '3') echo "checked";?> />
						3. Others (Specify)
				<input  type="text" class="style2" name="corpse_disposal_others" id="corpse_disposal_others" onblur="trimString(this); $('corpse_disposal').value= '3 - '+this.value;" value="<?php if ($corpse_disposal_tmp == '3') echo $corpse_disposal_others; ?>" />
		</td>
	 </tr>
	 <tr>
		<td bgcolor="#F9FFFB" class="style2">&nbsp;</td>
	 </tr>
	</table></td>   <!-- ENDS - //CORPSE DISPOSAL -->

		<!-- BURIAL / CREMATION PERMIT -->
	<td width="32%" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="2" cellspacing="1">
	 <tr>
		<td colspan="2" bgcolor="#D5E3EE" class="style5">&nbsp;22. &nbsp; BURIAL/CREMATION PERMIT </td>
	 </tr>
	 <tr>
		<td width="27%" bgcolor="#F9FFFB" class="style2"> Number </td>
		<td width="73%" bgcolor="#F9FFFB" class="style2">
			<input name="burial_permit" type="text" class="style2" id="burial_permit" value="<?=$burial_permit?>"/>
		</td>
	 </tr>
	 <tr>
		<td bgcolor="#F9FFFB" class="style2">
		Date Issued
		<?php
			$phpfd=$date_format;
			$phpfd=str_replace("dd", "%d", strtolower($phpfd));
			$phpfd=str_replace("mm", "%m", strtolower($phpfd));
			$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

			if (($burial_date_issued!='0000-00-00')  && ($burial_date_issued!=""))
				$burial_date_issued = @formatDate2Local($burial_date_issued,$date_format);
			else
				$burial_date_issued='';

			$sDateBurial= '<input name="burial_date_issued" type="text" size="15" maxlength=10 value="'.$burial_date_issued.'"'.
						'onFocus="this.select();"
						id = "burial_date_issued"
						onBlur="IsValidDate(this,\''.$date_format.'\'); "
						onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
						<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').'
								id="burial_date_issued_trigger" style="cursor:pointer" >
						<font size=2.5>[';
			ob_start();
		 ?>
		<script type="text/javascript">
			Calendar.setup ({
					inputField : "burial_date_issued", ifFormat : "<?php echo $phpfd?>",
					showsTime : false, button : "burial_date_issued_trigger", singleClick : true, step : 1
			});
		</script>
		<?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();
			$sDateBurial .= $calendarSetup;
			$dfbuffer="LD_".strtr($date_format,".-/","phs");
			$sDateBurial = $sDateBurial.$$dfbuffer.']';
		?>
			</td>
			<td bgcolor="#F9FFFB" class="style2"> <?= $sDateBurial ?></td>
		 </tr>
		</table></td>  <!--ENDS - //BURIAL / CREMATION PERMIT -->

		<!-- AUTOPSY -->
		<td width="30%" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="2" cellspacing="1" class="style2">
		 <tr>
			<td bgcolor="#D5E3EE" class="style5">&nbsp;23. &nbsp; AUTOPSY </td>
		 </tr>
		 <tr>
			<td bgcolor="#F9FFFB">
	<input name="is_autopsy" type="hidden" id="is_autopsy" value="<?php if($is_autopsy) echo $is_autopsy; else echo "2"; ?>" />
			 <input name="autopsy_tmp" id="autopsy_tmp" type="radio" onclick="checkIfAutopsied(this);" value="1" <?php if($is_autopsy=='1') echo "checked"; ?> />
		 1. Yes </td>
	 </tr>
	 <tr>
	 <td bgcolor="#F9FFFB">
		 <input name="autopsy_tmp" id="autopsy_tmp" type="radio" onclick="checkIfAutopsied(this);" value="2" <?php if($is_autopsy=='2') echo "checked"; ?> />
		 2. No </td>
		 </tr>
		</table></td><!--ENDS- //AUTOPSY -->
	</tr>
</table><br />

<!-- NAME AND ADDRESS OF CEMETERY OR CREMATORY -->
<table width="90%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#A0C2DA">
	<tr>
		<td width="44%" bgcolor="#D5E3EE">
			<span class="style5">&nbsp;24. &nbsp; NAME AND ADDRESS OF CEMETERY OR CREMATORY</span></td>
		<td width="56%" bgcolor="#FFFFFF">
			<input name="cemetery_name_address" type="text" class="style2" id="cemetery_name_address" size="50"
				value="<?= $cemetery_name_address ?>" />
		</td>
	</tr>
</table><br />

<!-- INFORMANT -->
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#A0C2DA">
	<tr>
		<td colspan="2" bgcolor="#D5E3EE"><b class="style5">&nbsp;25. &nbsp; INFORMANT</b></td>
	</tr>
	<tr>
		<td bgcolor="#F9FFFB" class="style2">
			Name in Print
		</td>
		<td bgcolor="#F9FFFB" class="style2">
			<input name="informant_name" type="text" class="style2" id="informant_name"
				value="<?= strtoupper($informant_name) ?>" size="50" />
		</td>
	</tr>
	<tr>
		<td bgcolor="#F9FFFB" class="style2">
			Relationship to the deceased
		</td>
		<td bgcolor="#F9FFFB" class="style2">
			<input name="informant_relation" type="text" class="style2" id="informant_relation"
				value="<?= strtoupper($informant_relation) ?>" size="50" />
		</td>
	</tr>
	<tr>
		<td bgcolor="#F9FFFB" class="style2">
			Address<br />
		</td>
		<td bgcolor="#F9FFFB" class="style2">
			<textarea name="informant_address" id="informant_address" cols="50" rows="2"><?= $informant_address ?></textarea>
		</td>
	</tr>
	<tr>
		<td width="9%" bgcolor="#F9FFFB" class="style2">
			Date
		<?php
			$phpfd=$date_format;
			$phpfd=str_replace("dd", "%d", strtolower($phpfd));
			$phpfd=str_replace("mm", "%m", strtolower($phpfd));
			$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

			if (($informant_date_sign!='0000-00-00')  && ($informant_date_sign!=""))
				$informant_date_sign = @formatDate2Local($informant_date_sign,$date_format);
			else
				$informant_date_sign='';

			$sDateInformant= '<input name="informant_date_sign" type="text" size="15" maxlength=10
								value="'.$informant_date_sign.'"'.
							'onFocus="this.select();"
							id = "informant_date_sign"
							onBlur="IsValidDate(this,\''.$date_format.'\'); "
							onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
							<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').'
										id="informant_date_sign_trigger" style="cursor:pointer" >
							<font size=2.5>[';
			ob_start();
		 ?>
		<script type="text/javascript">
			Calendar.setup ({
					inputField : "informant_date_sign", ifFormat : "<?php echo $phpfd?>",
					showsTime : false, button : "informant_date_sign_trigger", singleClick : true, step : 1
			});
		</script>
		<?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();

			$sDateInformant .= $calendarSetup;
			/**/
			$dfbuffer="LD_".strtr($date_format,".-/","phs");
			$sDateInformant = $sDateInformant.$$dfbuffer.']';
		 ?>
		</td>
		<td width="91%" bgcolor="#F9FFFB" class="style2">
			<?= $sDateInformant ?>
		</td>
	</tr>
</table><br /> <!--ENDS - //INFORMANT -->

<!-- DELAYED REGISTRATION -->
<table width="90%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#A0C2DA">
 <tr>
	<td colspan="2" class="style2"><table width="100%" border="0" cellpadding="0" cellspacing="0">
	 <tr>
		<td height="26" colspan="2" bgcolor="#F9FFFB" class="style6">
		 <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#A0C2DA">
			<tr>
		 <td width="2%"><img src="images/brdr_blgry_03.gif" width="31" height="26" /></td>
		 <td width="97%" align="center" class="style5"><span class="style9"> DELAYED REGISTRATION OF DEATH </span></td>
			</tr>
		 </table>
		</td>
	 </tr>
	 <tr bgcolor="#F9FFFB">
		<?php
		if ($is_late_reg)
			$checked = "checked";
		else
			$checked = "";
		 ?>
	<td width="100%"  class="style13">
		<input type="checkbox" name="is_late_reg" id="is_late_reg" value="1"
		onclick="showLateReg(this);" <?= $checked; ?>/>&nbsp;<span><strong>IS DELAYED REGISTRATION? </strong></span>
	</td>
	 </tr>

	 <tr id="late_reg" style="display:none">
	<td width="100%" bgcolor="#F9FFFB" colspan="2"><table id="lateTable" width="100%" border="0" cellpadding="4" cellspacing="0">
	 <tr>
		<td colspan="2" bgcolor="#F9FFFB">&nbsp;
		 <table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="10%" bgcolor="#F9FFFB"> <span class="style2">&nbsp;&nbsp;&nbsp;Republic of the Philippines </span> </td>
		</tr>
		 </table>
		 <table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td width="13%" bgcolor="#F9FFFB"> <span class="style2">&nbsp;&nbsp;&nbsp;Province of </span> </td>
			<td width="87%" bgcolor="#F9FFFB">
			 <input name="province" type="hidden" class="style2" id="province" value="<?=$row['prov_name'];?>" />
			 <span class="style13">&nbsp;&nbsp;<?=$row['prov_name'];?></span>
			</td>
		</tr>
		<tr>
			<td width="13%" bgcolor="#F9FFFB"> <span class="style2">&nbsp;&nbsp;&nbsp;City/Municipality of </span> </td>
			<td width="87%" bgcolor="#F9FFFB">
			 <input name="muncity" type="hidden" class="style2" id="muncity" value="<?=$row['mun_name'];?>" />
			 <span class="style13">&nbsp;&nbsp;<?=$row['mun_name'];?></span>
			</td>
		</tr>
		 </table>
		</td>
	 </tr>
	 <tr>
		<td colspan="3" align="center">
			<b><span class="style5"><font size="+1"> AFFIDAVIT FOR DELAYED REGISTRATION OF DEATH </font></span></b>
			<br>
		</td>
	 </tr>
	 <tr>
		<td>&nbsp;</td>
	 </tr>
	 <tr>
		<td class="style2">
		 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		 I,  &nbsp;
		 <input type="text" name="late_affiant_name" id="late_affiant_name" size="60" value="<?=$late_affiant_name?>" />
		 &nbsp;, of legal age, single/married, after being duly sworn to in accordance with the law, do hereby depose and say:
		</td>
	 </tr>
	 <tr>
		<td><br /></td>
	 </tr>
	 <tr>
			<td class="style2">
			<?php
			$late_dead_name = ucwords(strtolower($name_first))." ".ucwords(strtolower($name_middle))." ".ucwords(strtolower($name_last));
			 ?>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			1. That
			<!-- <input type="text" name="late_dead_name" id="late_dead_name" size="60" value="<?=$late_dead_name?>" readonly="1" /> -->
			<input type="text" name="late_dead_name" id="late_dead_name" size="60" value="<?=$late_dead_name?>" />
			died on

			<?php
				$phpfd=$date_format;
				$phpfd=str_replace("dd", "%d", strtolower($phpfd));
				$phpfd=str_replace("mm", "%m", strtolower($phpfd));
				$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

				$late_ddate = $death_date;

				$sDateLateDeath= '<input name="late_ddate" type="text" size="15" maxlength=10 value="'.$late_ddate.'"'.
								'onFocus="this.select();"
								id = "late_ddate"
								onBlur="IsValidDate(this,\''.$date_format.'\'); UpdateLDeathDate();"
								onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
								<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').'
										id="late_ddate_trigger" style="cursor:pointer" >
								<font size=2.5>[';
				ob_start();
			 ?>
			<script type="text/javascript">
				Calendar.setup ({
					inputField : "late_ddate", ifFormat : "<?php echo $phpfd?>",
					showsTime : false, button : "late_ddate_trigger",
					onClose: function(cal) { cal.hide(); UpdateLDeathDate(); }, singleClick : true, step : 1
				});
			</script>
			<?php
				$calendarSetup = ob_get_contents();
				ob_end_clean();

				$sDateLateDeath .= $calendarSetup;
				$dfbuffer="LD_".strtr($date_format,".-/","phs");
				$sDateLateDeath = $sDateLateDeath.$$dfbuffer.']';
			 ?>
			&nbsp;<?= $sDateLateDeath ?>

			<?php
				$late_cemetery = ucwords(strtolower($cemetery_name_address));
			 ?>
				 &nbsp; in  &nbsp;
					<?php
						if (empty($late_place_death)){
							$late_place_death = $row['hosp_name']."-".$row['mun_name'];
						}
				?>
					 <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				 <input type="text" id="late_place_death" name="late_place_death" value="<?=$late_place_death?>" size="100" />
				 <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; and was buried/cremated in
				 <input type="text" name="late_cemetery" id="late_cemetery" size="60"
						value="<?=$late_cemetery?>" onblur="UpdateCemetery();" />
				 &nbsp; on
			<?php
				$phpfd=$date_format;
				$phpfd=str_replace("dd", "%d", strtolower($phpfd));
				$phpfd=str_replace("mm", "%m", strtolower($phpfd));
				$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

				if (($late_bcdate!='0000-00-00') && ($late_bcdate!=""))
					$late_bcdate = @formatDate2Local($late_bcdate,$date_format);
				else
					$late_bcdate='';

				$sDateLateBurial= '<input name="late_bcdate" type="text" size="15" maxlength=10 value="'.$late_bcdate.'"'.
								'onFocus="this.select();"
								id = "late_bcdate"
								onBlur="IsValidDate(this,\''.$date_format.'\'); "
								onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
								<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').'
										id="late_bcdate_trigger" style="cursor:pointer" >
								<font size=2.5>[';
				ob_start();
			 ?>
			<script type="text/javascript">
				Calendar.setup ({
					inputField : "late_bcdate", ifFormat : "<?php echo $phpfd?>",
					showsTime : false, button : "late_bcdate_trigger",
					onClose: function(cal) { cal.hide(); },singleClick : true, step : 1
				});
			</script>
				<?php
				$calendarSetup = ob_get_contents();
				ob_end_clean();
				$sDateLateBurial .= $calendarSetup;
				$dfbuffer="LD_".strtr($date_format,".-/","phs");
				$sDateLateBurial = $sDateLateBurial.$$dfbuffer.']';
			 ?>
			&nbsp;&nbsp; <?= $sDateLateBurial ?>.
			</td>
	 </tr>
	 <tr>
		<td class="style2">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			2. That the deceased was/was not attended to at the time of his death. &nbsp;
		</td>
	 </tr>
	 <tr>
		<td class="style2">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			3. That the reason for the delay in registering this death was due to
			<input type="text" id="late_reason" name="late_reason" size="100" value="<?=$late_reason?>" />&nbsp;.
		</td>
	 </tr>
	 <tr>
		<td colspan="2">&nbsp;</td>
	 </tr>
	 <tr>
		<td><table width="100%" border="0" cellpadding="4" cellspacing="0">
		 <tr>
		<td width="62%">&nbsp;</td>
		<td width="38%" class="style2">_________________________________________</td>
		 </tr>
		 <tr>
		<td>&nbsp;</td>
		<td class="style2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				(Signature of Affiant)
		</td>
		 </tr>
		 <tr>
		<td colspan="2">&nbsp;</td>
		 </tr>
		 <tr>
		<td>&nbsp;</td>
		<td class="style2">Community Tax No.&nbsp;&nbsp;&nbsp;
					<input type="text" id="affiant_com_tax_nr" name="affiant_com_tax_nr" size="30" value="<?=$affiant_com_tax_nr?>"> </td>
		 </tr>
		 <tr>
		<td>&nbsp;</td>
				<td class="style2">
			 <?php
				$phpfd=$date_format;
				$phpfd=str_replace("dd", "%d", strtolower($phpfd));
				$phpfd=str_replace("mm", "%m", strtolower($phpfd));
				$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

				if (($affiant_com_tax_date!='0000-00-00')  && ($affiant_com_tax_date!=""))
					$affiant_com_tax_date = @formatDate2Local($affiant_com_tax_date,$date_format);
				else
					$affiant_com_tax_date='';

				$sDateJS= '<input name="affiant_com_tax_date" type="text" size="15" maxlength=10 value="'.$affiant_com_tax_date.'"'.
							'onFocus="this.select();"
							id = "affiant_com_tax_date"

							onBlur="IsValidDate(this,\''.$date_format.'\'); "
							onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
							<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').'
									id="affiant_com_tax_date_trigger" style="cursor:pointer" >
							<font size=2.5>[';
				ob_start();
			?>
			 <script type="text/javascript">
				Calendar.setup ({
					inputField : "affiant_com_tax_date", ifFormat : "<?php echo $phpfd?>",
					showsTime : false, button : "affiant_com_tax_date_trigger", singleClick : true, step : 1
				});
			 </script>
			 <?php
				$calendarSetup = ob_get_contents();
				ob_end_clean();
				$sDateJS .= $calendarSetup;
				$dfbuffer="LD_".strtr($date_format,".-/","phs");
				$sDateJS = $sDateJS.$$dfbuffer.']';
			?>
			Date Issued &nbsp;&nbsp; <?= $sDateJS ?>
				</td>
		 </tr>
		 <tr>
		<td>&nbsp;</td>
		<td class="style2">Place Issued&nbsp;&nbsp;&nbsp;
				<input type="text" id="affiant_com_tax_place" name="affiant_com_tax_place" size="30"
					value="<?=$affiant_com_tax_place?>">
		</td>
		 </tr>
		</table></td>
	 </tr>
	 <tr>
		<td>&nbsp;</td>
	 </tr>
		 <tr>
		<?php
			$phpfd=$date_format;
			$phpfd=str_replace("dd", "%d", strtolower($phpfd));
			$phpfd=str_replace("mm", "%m", strtolower($phpfd));
			$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

			if (($late_officer_date_sign!='0000-00-00') && ($late_officer_date_sign!=""))
				$late_officer_date_sign = @formatDate2Local($late_officer_date_sign,$date_format);
			else
				$late_officer_date_sign='';

			$sDateJS= '<input name="late_officer_date_sign" type="text" size="15" maxlength=10 value="'.$late_officer_date_sign.'"'.
						'onFocus="this.select();"
						id = "late_officer_date_sign"
						onBlur="IsValidDate(this,\''.$date_format.'\'); "
						onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
						<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').'
								id="late_officer_date_sign_trigger" style="cursor:pointer" >
						<font size=2.5>[';
			ob_start();
		 ?>
		<script type="text/javascript">
			Calendar.setup ({
				inputField : "late_officer_date_sign", ifFormat : "<?php echo $phpfd?>",
				showsTime : false, button : "late_officer_date_sign_trigger", singleClick : true, step : 1
			});
		</script>
		<?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();

			$sDateJS .= $calendarSetup;

			$dfbuffer="LD_".strtr($date_format,".-/","phs");
			$sDateJS = $sDateJS.$$dfbuffer.']';
		 ?>

			<td colspan="2" class="style2" align="justify">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<b> SUBSCRIBED AND SWORN </b> to before me this
			<?= $sDateJS ?>
			<br />
				&nbsp;&nbsp;&nbsp;at
				<input name="late_officer_place_sign" id="late_officer_place_sign" size="60" onblur="trimString(this)"
						value="<?= $late_officer_place_sign ?>" > , Philippines.
			</td>
	 </tr>
	 <tr>
		<td>&nbsp;</td>
	 </tr>
	 <tr>
		<td><table width="100%" border="0" cellpadding="4" cellspacing="0">
		 <tr>
		<td class="style2" align="center">_____________________________________________</td>
		<td>&nbsp;</td>
		<td class="style2" align="center">
				<input name="late_officer_title" id="late_officer_title" onblur="trimString(this)" size="30"
						value="<?= $late_officer_title ?>" >
		</td>
		 </tr>
		 <tr>
		<td class="style2" align="center">(Signature of Administering Officer)</td>
		<td>&nbsp;</td>
		<td class="style2" align="center">(Title / Designation)</td>
		 </tr>
		 <tr>
		<td class="style2" align="center" valign="bottom">
				<input name="late_officer_name" id="late_officer_name" onblur="trimString(this)" size="50"
						value="<?= $late_officer_name ?>" >
		</td>
		<td>&nbsp;</td>
		<td class="style2" align="center">
				<textarea name="late_officer_address" id="late_officer_address" cols="50" rows="2"><?= $late_officer_address ?></textarea>
		</td>
		 </tr>
		 <tr>
		<td class="style2" align="center">(Name in Print)</td>
		<td>&nbsp;</td>
		<td class="style2" align="center">(Address)</td>
		 </tr>
		</table></td>
	 </tr>
	 <tr>
		<td>&nbsp;</td>
	 </tr>
	</table></td>
	 </tr>
	</table></td>
 </tr>
</table>
<br />	<!-- Delayed Registration ends here -->

<!-- PREPARED BY -->
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#A0C2DA">
 <tr>
	<td colspan="2" bgcolor="#D5E3EE" class="style5">&nbsp;26. &nbsp; PREPARED BY: </td>
 </tr>
 <tr>
	<td width="9%" bgcolor="#F9FFFB" class="style2">
			Name in Print
	</td>
	<td width="91%" bgcolor="#F9FFFB" class="style2">
	<?php
		/*
		if (empty($encoder_name))
			//$encoder_name = strtoupper($HTTP_SESSION_VARS['sess_user_fullname']);
			$encoder_name = "RENAFE ALMERIA";
		//$personell = $pers_obj->getPersonellInfo($HTTP_SESSION_VARS['sess_user_personell_nr']);

		if (empty($encoder_title))
			//$encoder_title = strtoupper($personell['job_function_title']);
			$encoder_title = "ADM. AIDE I";
		*/
		#edited by VAN 07-04-08
					if (empty($encoder_name)){
							$encoder_info = $pers_obj->get_Signatory('deathcert');
							$encoder_name = $encoder_info['name'];
							$encoder_title = $encoder_info['signatory_position'];
					}else{
							$personell = $pers_obj->getPersonellInfo($HTTP_SESSION_VARS['sess_user_personell_nr']);
							#echo "".$pers_obj->sql;
							if (empty($encoder_title)){
								if ($personell['job_position'])
									$encoder_title = strtoupper($personell['job_position']);
								else
									$encoder_title = strtoupper($personell['job_function_title']);
							}
					}
	 ?>
			<input name="encoder_name" type="text" class="style2" id="encoder_name" value ="<?= $encoder_name ?>" size="50" />
	</td>
 </tr>
 <tr>
	<td bgcolor="#F9FFFB" class="style2">
			Title or Position
	</td>
	<td bgcolor="#F9FFFB" class="style2">
			<input name="encoder_title" type="text" class="style2" id="encoder_title" value="<?= $encoder_title ?>" size="50" />
	</td>
 </tr>
 <tr>
	<td bgcolor="#F9FFFB" class="style2">
		Date
		<?php
				$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

		if (($encoder_date_sign!='0000-00-00')  && ($encoder_date_sign!=""))
			$encoder_date_sign = @formatDate2Local($encoder_date_sign,$date_format);
		else
			$encoder_date_sign='';

		$sDatePrepared= '<input name="encoder_date_sign" type="text" size="15" maxlength=10 value="'.$encoder_date_sign.'"'.
					'onFocus="this.select();"
					id = "encoder_date_sign"
					onBlur="IsValidDate(this,\''.$date_format.'\'); "
					onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
					<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').'
							id="encoder_date_sign_trigger" style="cursor:pointer" >
					<font size=2.5>[';
		ob_start();
	 ?>
	<script type="text/javascript">
		Calendar.setup ({
				inputField : "encoder_date_sign", ifFormat : "<?php echo $phpfd?>",
				showsTime : false, button : "encoder_date_sign_trigger", singleClick : true, step : 1
		});
	</script>
	<?php
		$calendarSetup = ob_get_contents();
		ob_end_clean();

		$sDatePrepared .= $calendarSetup;
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDatePrepared = $sDatePrepared.$$dfbuffer.']';
	 ?>
	</td>
	<td bgcolor="#F9FFFB" class="style2">
		<?= $sDatePrepared ?>
	</td>
 </tr>
 <tr>
	<td colspan="2">&nbsp;</td>
 </tr>
</table>	<!--ENDS -//PREPARED BY -->

<div align="center">
<?php
	if (!$deathCertInfo || empty($deathCertInfo)){
		echo '			<input type="hidden" name="mode" id="mode" value="save">'."\n";
		echo '			<input type="submit" name="Submit" value="Save">'."\n";
	}else{
		echo '			<input type="hidden" name="mode" id="mode" value="update">'."\n";
		echo '			<input type="button" name="Print" value="Print" onClick="printDeathCert(\''.$pid.'\')">'."\n &nbsp; &nbsp;";
		echo '			<input type="submit" name="Submit" value="Update">'."\n";
	}
	echo '			<input type="hidden" name="pid" id="pid" value="'.$pid.'">'."\n";
?>
&nbsp; &nbsp;
<input type="button" name="Cancel" value="Cancel"  onclick="window.close()">
<input type="hidden" name="age0to7days" id="age0to7days" value="<?= $age0to7days?>">

<!--added by VAN 02-20-08 -->
<input type="hidden" name="birth_date" id="birth_date" value="<?= date("m/d/Y",strtotime($date_birth))?>" />

</div>
</form>
<p>&nbsp;</p>
</body>
</html>