<?php
# adapted from cert_birth_certificate.php and cert_death_certificate.php,  created by pet on may 29, 2008

require_once("roots.php");
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');

define('NO_2LEVEL_CHK',1);

define('LANG_FILE','aufnahme.php');
$local_user='aufnahme_user';

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;

# Create the helper class for the country table
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

if (isset($_GET['pid']) && $_GET['pid']){
	$pid = $_GET['pid'];
}
if (isset($_POST['pid']) && $_POST['pid']){
	$pid = $_POST['pid'];
}

if(isset($_GET['encounter_nr'])&& $_GET['encounter_nr']){
	$encounter_nr = $_GET['encounter_nr'];
}

include_once($root_path.'include/care_api_classes/class_cert_death_fetal.php');
$obj_fetalDeathCert = new FetalDeathCertificate($pid);

$birthYear = intval(substr($date_birth, 0, 4));
$birthMonth = intval(substr($date_birth, 5, 7));
$birthDay = intval(substr($date_birth, 8, 10));

$errorMsg='';

	# Serializing the Causes of Death information
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

$HTTP_POST_VARS['mother_fname'] = $HTTP_POST_VARS['m_name_first'];
$HTTP_POST_VARS['mother_maidenname'] = $HTTP_POST_VARS['m_name_middle'];
$HTTP_POST_VARS['mother_mname'] = $HTTP_POST_VARS['m_name_last'];

#N/A
if ($HTTP_POST_VARS['f_name_first']=="n/a")
	$HTTP_POST_VARS['father_fname'] = " ";
else
	$HTTP_POST_VARS['father_fname'] = $HTTP_POST_VARS['f_name_first'];

if ($HTTP_POST_VARS['f_name_middle']=="n/a")
	$HTTP_POST_VARS['father_mname'] = " ";
else
	$HTTP_POST_VARS['father_mname'] = $HTTP_POST_VARS['f_name_middle'];

if ($HTTP_POST_VARS['f_name_last']=="n/a")
	$HTTP_POST_VARS['father_lname'] = " ";
else
	$HTTP_POST_VARS['father_lname'] = $HTTP_POST_VARS['f_name_last'];

$bdate = $HTTP_POST_VARS['birth_year']."-".$HTTP_POST_VARS['birth_month']."-".$HTTP_POST_VARS['birth_day'];
$HTTP_POST_VARS['date_birth'] = date("Y-m-d",strtotime($bdate));

if (empty($HTTP_POST_VARS['parent_marriage_date']))
	$HTTP_POST_VARS['parent_marriage_date'] = " ";

if (isset($_POST['mode'])){
	switch($_POST['mode']) {
		case 'save':
			$tmp=array();
			if (trim($HTTP_POST_VARS['attendant_date_sign'])!=""){
				$HTTP_POST_VARS['attendant_date_sign'] = formatDate2STD($HTTP_POST_VARS['attendant_date_sign'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['burial_date_issued'])!=""){
				$HTTP_POST_VARS['burial_date_issued'] = formatDate2STD($HTTP_POST_VARS['burial_date_issued'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['informant_date_sign'])!=""){
				$HTTP_POST_VARS['informant_date_sign'] = formatDate2STD($HTTP_POST_VARS['informant_date_sign'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['encoder_date_sign'])!=""){
				$HTTP_POST_VARS['encoder_date_sign'] = formatDate2STD($HTTP_POST_VARS['encoder_date_sign'],$date_format,$tmp);
			}

			if (trim($HTTP_POST_VARS['parent_marriage_date'])!=""){
				$HTTP_POST_VARS['parent_marriage_date'] = formatDate2STD($HTTP_POST_VARS['parent_marriage_date'],$date_format,$tmp);
			}

			$HTTP_POST_VARS['history'] = " \n"." Created Fetal Death Cert. ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_dt']=date('Y-m-d H:i:s');

			$HTTP_POST_VARS['street_name'] = $HTTP_POST_VARS['m_residence_basic'];
			$HTTP_POST_VARS['brgy_nr'] = $HTTP_POST_VARS['m_residence_brgy'];


			extract($HTTP_POST_VARS);	# ADDED BY ALVIN

			if ($obj_fetalDeathCert->saveFetalDeathCertInfoFromArray($HTTP_POST_VARS)){

				$HTTP_POST_VARS['death_date'] = $HTTP_POST_VARS['date_birth'];
				$HTTP_POST_VARS['death_encounter_nr'] = $encounter_nr;
				$HTTP_POST_VARS['death_cause'] = $HTTP_POST_VARS['death_cause'];
				$HTTP_POST_VARS['death_cause_code'] = '';
				$HTTP_POST_VARS['citizenship'] = $HTTP_POST_VARS['d_citizenship'];
				$HTTP_POST_VARS['religion'] = $HTTP_POST_VARS['d_religion'];
				$HTTP_POST_VARS['civil_status'] = $HTTP_POST_VARS['decease_civilstatus'];
				$HTTP_POST_VARS['street_name'] = $HTTP_POST_VARS['residence_basic'];
				$HTTP_POST_VARS['brgy_nr'] = $HTTP_POST_VARS['residence_brgy'];
				$HTTP_POST_VARS['age'] = $HTTP_POST_VARS['death_age'];

				$bdate = $HTTP_POST_VARS['birth_year']."-".$HTTP_POST_VARS['birth_month']."-".$HTTP_POST_VARS['birth_day'];
				$HTTP_POST_VARS['date_birth'] = date("Y-m-d",strtotime($bdate));

				$HTTP_POST_VARS['history'] = $obj_fetalDeathCert->ConcatHistory(" \n"." Saved/Updated Reg Info (Fetal Death) ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']);
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
			if (trim($HTTP_POST_VARS['attendant_date_sign'])!=""){
				$HTTP_POST_VARS['attendant_date_sign'] = formatDate2STD($HTTP_POST_VARS['attendant_date_sign'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['burial_date_issued'])!=""){
				$HTTP_POST_VARS['burial_date_issued'] = formatDate2STD($HTTP_POST_VARS['burial_date_issued'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['informant_date_sign'])!=""){
				$HTTP_POST_VARS['informant_date_sign'] = formatDate2STD($HTTP_POST_VARS['informant_date_sign'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['encoder_date_sign'])!=""){
				$HTTP_POST_VARS['encoder_date_sign'] = formatDate2STD($HTTP_POST_VARS['encoder_date_sign'],$date_format,$tmp);
			}

			if (trim($HTTP_POST_VARS['parent_marriage_date'])!=""){
				$HTTP_POST_VARS['parent_marriage_date'] = formatDate2STD($HTTP_POST_VARS['parent_marriage_date'],$date_format,$tmp);
			}

			$HTTP_POST_VARS['history'] = $obj_fetalDeathCert->ConcatHistory(" \n"." Updated Fetal Death Cert. ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']);
			$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['modify_dt']=date('Y-m-d H:i:s');

			if (empty($HTTP_POST_VARS['m_residence_basic']))
				$HTTP_POST_VARS['m_residence_basic'] = ' ';

			$HTTP_POST_VARS['street_name'] = $HTTP_POST_VARS['m_residence_basic'];
			$HTTP_POST_VARS['brgy_nr'] = $HTTP_POST_VARS['m_residence_brgy'];

			if ($obj_fetalDeathCert->updateFetalDeathCertInfoFromArray($HTTP_POST_VARS)){

				$HTTP_POST_VARS['death_date'] = $HTTP_POST_VARS['date_birth'];
				$HTTP_POST_VARS['death_encounter_nr'] = $encounter_nr;
				$HTTP_POST_VARS['death_cause'] = $HTTP_POST_VARS['death_cause'];
				$HTTP_POST_VARS['death_cause_code'] = '';
				$HTTP_POST_VARS['citizenship'] = $HTTP_POST_VARS['d_citizenship'];
				$HTTP_POST_VARS['religion'] = $HTTP_POST_VARS['d_religion'];

				if($HTTP_POST_VARS['decease_civilstatus']=="others"){
					$HTTP_POST_VARS['civil_status'] = $HTTP_POST_VARS['d_civil_status'];
				}else{
					$HTTP_POST_VARS['civil_status'] = $HTTP_POST_VARS['decease_civilstatus'];
				}

				#$HTTP_POST_VARS['street_name'] = $HTTP_POST_VARS['residence_basic'];
				#$HTTP_POST_VARS['brgy_nr'] = $HTTP_POST_VARS['residence_brgy'];
				$HTTP_POST_VARS['age'] = $HTTP_POST_VARS['death_age'];

				$bdate = $HTTP_POST_VARS['birth_year']."-".$HTTP_POST_VARS['birth_month']."-".$HTTP_POST_VARS['birth_day'];
				$HTTP_POST_VARS['date_birth'] = date("Y-m-d",strtotime($bdate));

				$HTTP_POST_VARS['history'] = $obj_fetalDeathCert->ConcatHistory(" \n"." Updated Reg Info (Fetal Death) ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']);
				$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
				$HTTP_POST_VARS['modify_time']=date('Y-m-d H:i:s');

				$person_obj->updatePersonInfo($pid, $HTTP_POST_VARS);
				#echo "".$person_obj->sql;
				echo " \n <script type=\"text/javascript\">alert(\"Updated sucessfully!\")</script>";
			}else{
				echo '<em class="warn"><font style="color:#FF0000"><b>'.$db->ErrorMsg().'<b></font></em>';
			}
		break;
	}# end of switch statement
}

if ($pid){
	 if (!($basicInfo=$person_obj->BasicDataArray($pid))){
		echo '<em class="warn"> Sorry, the page cannot be displayed!</em>';
		exit();
		 }
	extract($basicInfo);
	}
else{
	echo '<em class="warn">Sorry, the page cannot be displayed! <br> Invalid HRN!</em>';
	exit();
	}

$fetalDeathCertInfo = $obj_fetalDeathCert->getFetalDeathCertRecord($pid);

if ($fetalDeathCertInfo){
	extract($fetalDeathCertInfo);
	$delivery_method_tmp= substr(trim($fetalDeathCertInfo['delivery_method']),0,1);
	$delivery_method_info = substr(trim($fetalDeathCertInfo['delivery_method']),4);
	$attendant_type_tmp = substr(trim($fetalDeathCertInfo['attendant_type']),0,1);
	$attendant_type_others = substr(trim($fetalDeathCertInfo['attendant_type']),4);
	$death_occurrence = substr(trim($fetalDeathCertInfo['death_occurrence']),0,1);
	$corpse_disposal_tmp= substr(trim($fetalDeathCertInfo['corpse_disposal']),0,1);
	$corpse_disposal_others = substr(trim($fetalDeathCertInfo['corpse_disposal']),4);
	$is_autopsy = substr(trim($fetalDeathCertInfo['is_autopsy']),0,1);
	$tmp_death_cause = unserialize($fetalDeathCertInfo['death_cause']);
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Certificate of Fetal Death</title>
<style type="text/css">

.style2 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
body {
	background-color: #FFFFFF;
}
.style4 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style5 {	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	color: #FFFFFF;
	font-weight: bold;
}
.style6 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; }
.style8 {color: #7C3501}
.style9 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #333300; }
.style11 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #7C3501; }
.style12 {color: #000066}
.style13 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #000066; }
.style15 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #FF6600; }
.style18 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #F9FFFB; }
a {
	font-size: 10px;
	color: #006600;
	font-family: Arial, Helvetica, sans-serif;
}
a:link {
	text-decoration: none;
}
a:visited {
	text-decoration: none;
}
a:hover {
	text-decoration: none;
}
a:active {
	text-decoration: none;
}

</style>

<script language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>

<?php
	echo '<script type="text/javascript" language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\n";
	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\n";
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

<script type="text/javascript" src="<?=$root_path?>modules/registration_admission/certificates/js/death_fetal_cert.js"></script>

</head>

<body onload="preSet();">

<?php echo $errorMsg; ?><a name="goto"></a>
<form id="fetaldeath_certificate" name="fetaldeath_certificate" method="post" action="" onsubmit="return chckForm(this);">
	&nbsp;
	<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
		<tr>
			<td align="left" bgcolor="#FFCCCC"><div align="center"><span class="style2"><?=$row['hosp_country'];?></span><br />
							<span class="style15"><font color="#CC3399" size="+1">CERTIFICATE OF FETAL DEATH</font></span> </div></td>
		</tr>
		<tr>
			<td bgcolor="#FFFFFF"><table width="100%" height="44" border="0" cellpadding="0" cellspacing="2">
					<tr>
						<td width="72%" height="19" bgcolor="#F9FFFB"><span class="style2">Province : &nbsp;&nbsp;</span> <span class="style6">
							<input type="hidden" name="province" id="province" value="<?=$row['prov_name'];?>" />
							<b><?=$row['prov_name'];?></b></span> </td>
						<td width="28%" rowspan="2" align="right" bgcolor="#F9FFFB"><span class="style15">Registry No.</span><span class="style2"> &nbsp;&nbsp;</span>
								<input type="text" name="registry_nr" id="registry_nr" value="<?= $registry_nr ?>" onblur="trimString(this)" />
						</td>
					</tr>
					<tr>
						<td height="19" bgcolor="#F9FFFB"><span class="style2">City/Municipality : &nbsp;&nbsp;</span> <span class="style6">
							<input type="hidden" name="muncity" id="muncity" value="<?=$row['mun_name'];?>" />
							<b><?=$row['mun_name'];?></b></span> </td>
					</tr>
				</table>
					<br />
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="5%" class="style15">Go to</td>
							<td width="15%"><a href="#mother">Mother Profile</a></td>
							<td width="15%"><a href="#father">Father Profile</a></td>
							<td width="15%"><a href="#medcert">Medical Certificate</a></td>
							<td width="50%">&nbsp;</td>
						</tr>
					</table>
				<br /></td>
		</tr>
		<tr>	<!-- FETUS table begins here -->
			<td align="center" bordercolor="#FFCCFF" bordercolordark="#000000"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
					<tr>
						<td width="3%" bgcolor="#FFCCCC" ><img src="images/brdr_pnk.gif" width="31" height="26" /></td>
						<td width="94%" bgcolor="#FFCCCC"><span class="style5">FETUS</span></td>
						<td width="3%" bgcolor="#FFCCCC">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3" bgcolor="#98D228"><table width="100%" border="0" cellspacing="1" cellpadding="1">
								<tr>
									<td height="410" align="center" bgcolor="#F9FFFB">
							<br />
								 <div align="center">
	<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td  bgcolor="#FFCCCC">
				<center>
				<?php
			if (!$fetalDeathCertInfo || empty($fetalDeathCertInfo)){
				echo '<input type="hidden" name="mode" id="mode" value="save">'."\n";
				echo '<input type="submit" name="Submit" value="Save">'."\n";
			}else{
				echo '<input type="hidden" name="mode" id="mode" value="update">'."\n";
				echo '<input type="button" name="Print" value="Print" onClick="printFetalDeathCert(\''.$pid.'\')">'."\n &nbsp; &nbsp;";
				echo '<input type="submit" name="Submit" value="Update">'."\n";
			}
			echo '<input type="hidden" name="pid" id="pid" value="'.$pid.'">'."\n";
			?>
	&nbsp; &nbsp;
	<input type="button" name="Cancel" value="Cancel"  onclick="window.close()" />
			 </center>
		 </td>
		</tr>
	</table>
</div><br />
			<!-- Name of Fetus -->
											<table width="90%" border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td height="36" colspan="3" valign="top" bgcolor="#98D228"><table width="100%" border="0" cellpadding="0" cellspacing="1">
															<tr>
																<?php
								$spacevar2 = "&nbsp;&nbsp;";
								$spacevar4 = "&nbsp;&nbsp;&nbsp;&nbsp;";
								$spacevar6 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
								?>
																<td width="18%" align="left" valign="middle" bgcolor="#FFCCCC" class="style6">&nbsp;1. &nbsp; NAME OF FETUS<br/>
																		<?= $spacevar6 ?> <?= $spacevar6 ?>
																(if given) </td>
																<td width="26%" align="center" valign="top" bgcolor="#F9FFFB"><span class="style2">Given Name </span><br />
																		<input type="text" name="name_first" id="name_first" size="30" value="<?= ucwords(strtolower($name_first)) ?>" />
																</td>
																<td width="27%" align="center" valign="top" bgcolor="#F9FFFB"><span class="style2">Middle Name </span><br />
																		<input type="text" name="name_middle" id="name_middle" size="30" value="<?= ucwords(strtolower($name_middle)) ?>" />
																		<input type="hidden" name="name_middle2" id="name_middle2" value="<?= ucwords(strtolower($name_middle)) ?>" />
																</td>
																<td width="29%" align="center" valign="top" bgcolor="#F9FFFB"><span class="style2">Last Name </span><br />
																		<input type="text" name="name_last" id="name_last" size="30" value="<?= ucwords(strtolower($name_last)) ?>" />
																		<input type="hidden" name="name_last2" id="name_last2" value="<?= ucwords(strtolower($name_last)) ?>" />
																</td>
															</tr>
													</table></td>
												</tr>
											</table>
										 
			<!-- Gender and Date of Delivery-->
											<table width="90%" border="0" cellpadding="0" cellspacing="0">
												<tr>
				<!-- on gender -->
													<td width="40%" height="74" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellspacing="0" cellpadding="0">
															<tr>
																<td width="55%" valign="top" bgcolor="#F9FFFB" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
																		<tr>
																			<td height="57" valign="top" bgcolor="#98D228"><table width="100%" border="0" cellpadding="4" cellspacing="1">
																					<tr>
																						<td height="27" colspan="3" bgcolor="#FFCCCC" class="style6">2. &nbsp; SEX<span class="style2">
																							<input type="hidden" name="sex2" id="sex2" value="<?= $sex ?>" />
																						</span></td>
																					</tr>
																					<tr>
																						<td width="23%" height="41" align="center" bgcolor="#F9FFFB" class="style2"><center>
																								<input name="sex" id="sex" type="radio" value="m" <?php if ($sex=='m') echo "checked"; ?> />
																							Male
																						</center></td>
																						<td width="32%" height="41" align="center" bgcolor="#F9FFFB" class="style2"><center>
																								<input name="sex" id="sex" type="radio" value="f" <?php if ($sex=='f') echo "checked"; ?> />
																							Female
																						</center></td>
																						<td width="45%" height="41" align="center" bgcolor="#F9FFFB" class="style2"><center>
																								<input name="sex" id="sex" type="radio" value="u" <?php if ($sex=='u') echo "checked"; ?> />
																							Undetermined
																						</center></td>
																					</tr>
																			</table></td>
																		</tr>
																</table></td>
															</tr>
													</table></td>

					<!--- on Date of Delivery --->
													<td width="*" height="74" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellspacing="0" cellpadding="0">
														<tr>
															<td align="right" bgcolor="#F9FFFB"><table width="100%" border="0" cellspacing="0" cellpadding="0">
																 <tr>
																		<td height="57" valign="top" bgcolor="#98D228"><table width="100%" border="0" cellpadding="4" cellspacing="1">
																			<tr>
																				<td height="27" colspan="3" bgcolor="#FFCCCC" class="style6">3. &nbsp; DATE OF DELIVERY </td>
																				</tr>
																					<tr>
																						<td width="26%" height="41" align="center" bgcolor="#F9FFFB" class="style2"><center>
																							Day &nbsp;&nbsp;
																									<?php
													$birthDay = date("d",strtotime($date_birth));
													$dategen->genDay('','birth_day', $birthDay);
														 ?>
																							<br />
																						</center></td>
																						<td width="45%"  height="41" align="center" bgcolor="#F9FFFB" class="style2"><center>
																							Month &nbsp;&nbsp;
																									<?php
													$birthMonth = date("m",strtotime($date_birth));
													$dategen->genMonth('','birth_month','long', $birthMonth);
														 ?>
																							<br />
																						</center></td>
																						<td width="29%"  height="41" align="center" bgcolor="#F9FFFB" class="style2"><center>
																							Year &nbsp;&nbsp;
																									<?php
													$birthYear = date("Y",strtotime($date_birth));
													$dategen->genYear('','birth_year','20',$birthYear);
												 ?>
																							<br />
																						</center>										    </td>
																					</tr>
																			</table></td>
																	</tr>
																</table></td>
														</tr>
													</table></td>
					<!--- end of date of delivery --->
												</tr>
						<tr>
							<td bgcolor="#F9FFFB">&nbsp;</td>
						</tr>
											</table>
		<!--- for place of delivery --->
										<table width="90%" border="0" cellspacing="1" cellpadding="0">
											<tr>
												<td bgcolor="#98D228"><table width="100%" border="0" cellpadding="4" cellspacing="1">
							<tr>
							<td colspan="3" bgcolor="#FFCCCC" class="style6">4. &nbsp; PLACE OF DELIVERY </td>
							</tr>
							<?php
									if (empty($birth_place_basic))
										$birth_place_basic = $row['hosp_name'];

									if (empty($birth_place_mun))
										$birth_place_mun = $row['mun_name'];

									if (empty($birth_place_prov))
										$birth_place_prov = $row['prov_name'];
						 ?>
							<tr align="center" valign="top">
							<td width="40%" height="55" bgcolor="#F9FFFB"><span class="style2">Name of Hospital / Clinic / Institution / <br />
								House No., Street, Barangay <br />
							</span>
														<input type="hidden" name="hosp_name" id="hosp_name" value="<?=$row['hosp_name']?>">
														<input type="hidden" name="mun_name" id="mun_name" value="<?=$row['mun_name']?>">
								<span class="style6"><b><?php echo mb_strtoupper($birth_place_basic).", ".mb_strtoupper($birth_place_mun);?></b>
								<input type="hidden" name="birth_place_basic" id="birth_place_basic" value="<?php echo $birth_place_basic;?>" />
								</span></td>
							<td width="30%" height="55" bgcolor="#F9FFFB"><span class="style2">City / Municipality <br />
								<br /></span>
								<span class="style6"><b><?=mb_strtoupper($birth_place_mun);?></b>
								<input type="hidden" name="birth_place_mun" id="birth_place_mun" value="<?=$birth_place_mun;?>" />
								</span></td>
							<td width="30%" height="55" bgcolor="#F9FFFB"><span class="style2">Province <br />
								<br /></span>
								<span class="style6"><b><?=mb_strtoupper($birth_place_prov);?></b>
								<input type="hidden" name="birth_place_prov" id="birth_place_prov" value="<?=$birth_place_prov;?>" />
								</span></td>
							</tr>
													</table></td>
											</tr>
						<tr>
							<td bgcolor="#F9FFFB">&nbsp;</td>
						</tr>
										</table>

		<!--- type of delivery --->
										<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
												<tr>
													<td height="108" valign="top" bgcolor="#98D228"><table width="100%" border="0" cellspacing="0" cellpadding="0">
															<tr>
																<td height="108" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#F9FFFB">
																		<tr>
																			<td width="50%" height="102" align="left" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellspacing="0" cellpadding="0">
																					<tr>
																						<td width="50%" bgcolor="#98D228"><table width="100%" border="0" cellpadding="4" cellspacing="1">
																								<tr>
																									<td colspan="2" width="100%" height="27" bgcolor="#FFCCCC" class="style6"> 5a. &nbsp; TYPE OF DELIVERY <span class="style2">
																										<input type="hidden" name="birth_type" id="birth_type" value="<?php if ($birth_type) echo $birth_type; else echo "1" ?>" />
																									</span></td>
																								</tr>
																								<tr>
																									<td height="70" valign="top" bgcolor="#F9FFFB" class="style2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
																											<tr>
																												<td width="38%" bgcolor="#F9FFFB" class="style2"><input name="birth_type_tmp" id="birth_type_tmp" type="radio" value="1" onclick="typeOfBirth(this)"
															<?php if (($birth_type=='1')||(!$birth_type)) echo "checked"; ?> />
																													Single</td>
																												<td width="62%" rowspan="3" valign="top">&nbsp;</td>
																											</tr>
																											<tr>
																												<td bgcolor="#F9FFFB" class="style2"><input name="birth_type_tmp" id="birth_type_tmp" type="radio" value="2" onclick="typeOfBirth(this)"
															<?php if ($birth_type=='2') echo "checked"; ?> />
																													Twin</td>
																											</tr>
																											<tr>
																												<td bgcolor="#F9FFFB" class="style2"><input name="birth_type_tmp" id="birth_type_tmp" type="radio" value="3" onclick="typeOfBirth(this)"
															<?php if ($birth_type=='3') echo "checked"; ?> />
																													Triplet, etc.</td>
																											</tr>
																									</table></td>
																								</tr>
																						</table></td>
																					</tr>
																			</table></td>
																			<td width="50%" height="102" align="left" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellspacing="0" cellpadding="0">
																					<tr>
																						<td width="50%" bgcolor="#98D228"><table width="100%" border="0" cellpadding="4" cellspacing="1">
																								<tr>
																									<td colspan="2" width="100%" height="27" bgcolor="#FFCCCC" class="style6">b. &nbsp; IF MULTIPLE DELIVERY, FETUS WAS <span class="style2">
																										<input type="hidden" name="birth_rank" id="birth_rank" value="<?= $birth_rank ?>" />
													</span></td>
																								</tr>
																								<tr>
																									<td height="70" valign="top" bgcolor="#F9FFFB" class="style2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
																											<tr>
																												<td width="81%" bgcolor="#F9FFFB" class="style2"><input name="birth_rank_tmp" id="birth_rank_tmp" type="radio" onclick="rankOfBirth(this);" value="first"
															 <?php if ($birth_rank=='first') echo "checked "; if (($birth_type==1)||(!$birth_type)) echo "disabled"; ?> />
																												First</td>
																												<td width="19%" bgcolor="#F9FFFB" rowspan="3" valign="top">&nbsp;</td>
																											</tr>
																											<tr>
																												<td bgcolor="#F9FFFB" class="style2"><input name="birth_rank_tmp" id="birth_rank_tmp" type="radio" onclick="rankOfBirth(this)" value="second"
															<?php if ($birth_rank=='second') echo "checked "; if (($birth_type==2)||(!$birth_type)) echo "disabled"; ?> />
																													Second</td>
																											</tr>
																											<tr>
																												<td bgcolor="#F9FFFB" class="style2"><input name="birth_rank_tmp" id="birth_rank_tmp" type="radio" onclick="rankOfBirth(this)" value="third"
															<?php if ($birth_rank=='third') echo "checked "; if (($birth_type==3)||(!$birth_type)) echo "disabled"; ?> />
																													Others, Specify &nbsp;&nbsp;&nbsp;
																													<input type="text" name="birth_rank_others" id="birth_rank_others" onblur="trimString(this); convertToWords(this); updateBirthRank();"
															<?php if (($birth_rank!='first')&&($birth_rank!='second')) echo 'value="'.$birth_rank.'"'; else echo "disabled"; ?> />
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
													</table></td>
												</tr>
										</table></td>
								</tr>
						</table></td>
					</tr>
					<tr>
						<td height="15">&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
			</table></td>
		</tr>

	<!--- MOTHER --->
		<tr>
			<td align="center" bordercolor="#FFCCCC" bordercolordark="#000000"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FEC194">
					<tr>
						<td width="2%"><img src="images/brdr_pnk.gif" width="31" height="26" /></td>
						<td width="65%" bgcolor="#FFCCCC" class="style5"><a name="mother"></a>MOTHER</td>
						<td width="33%" align="right" bgcolor="#FFCCCC" class="style5"><a href="#goto">Go to Top</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
					</tr>
					<tr>
						<td colspan="3" bgcolor="#FEC194"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
								<tr>
									<td height="490" bgcolor="#F9FFFB"><table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td height="80" valign="top" bgcolor="#98D228"><table width="100%" border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td height="80" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#F9FFFB">
																<tr>
																	<td width="35%" height="80" align="left" valign="top" bgcolor="#F9FFFB"><table width="99%" border="0" cellspacing="0" cellpadding="0">
																			<tr>
																				<td width="35%" bgcolor="#FFECDD"><table width="100%" border="0" cellpadding="4" cellspacing="1">
																						<tr>
																							<td bordercolor="#FFCCCC" bgcolor="#FFCCCC" class="style6"> c. &nbsp; METHOD OF DELIVERY <span class="style2">
																								<input type="hidden" name="delivery_method" id="delivery_method" value="<?php if ($delivery_method) echo $delivery_method; else echo "1"; ?>" />
																							</span></td>
																						</tr>
																						<tr>
																							<td valign="top" bgcolor="#F9FFFB" class="style2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
																									<tr>
																										<td width="92%" height="27" bgcolor="#F9FFFB" class="style2">
														<input name="delivery_method_tmp"  id="delivery_method_tmp" type="radio" value="1" onclick="methodOfDelivery(this);"
														<?php if ((!$delivery_method)||($delivery_method_tmp=="1")) echo "checked"; ?> />
																													Normal spontaneous vertex </td>
																										<td width="8%" rowspan="3" valign="top">&nbsp;</td>
																									</tr>
																								<td bgcolor="#F9FFFB" class="style2">
													<input name="delivery_method_tmp" id="delivery_method_tmp" type="radio" value="2" onclick="methodOfDelivery(this);"
													<?php if ($delivery_method_tmp=="2") echo "checked"; ?> />
																											Others (specify)
																											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																									 <input type="text" name="delivery_method_info" id="delivery_method_info" onblur="trimString(this); $('delivery_method').value = '2 - '+this.value;"
															value="<?php if ($delivery_method_tmp=="2") echo $delivery_method_info; ?>" />
																								</td>
																							 </table></td>
																						</tr>
																				</table></td>
																			</tr>
																	</table></td>
																	<td width="45%" align="right" valign="top" bordercolor="#FFECDDD" bgcolor="#F9FFFB"><table width="100%" border="0" cellspacing="0" cellpadding="0">
																			<tr>
																				<td valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="4" cellspacing="1">
																						<tr>
																							<td width="100%" height="27" bordercolor="#FFCCCC" bgcolor="#FFCCCC" class="style6">d. &nbsp; BIRTH ORDER </td>
																						</tr>
																				</table></td>
																			</tr>
																			<tr>
																				<td width="100%" valign="top" bgcolor="#F9FFFB" class="style2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
																						<tr>
																							<td bgcolor="#F9FFFB" class="style2">&nbsp;&nbsp; (live births and fetal deaths including this delivery) </td>
																						</tr>
																						<tr>
																							<td bgcolor="#F9FFFB" class="style2">
													<input type="text" name="birth_order" id="birth_order" value="<?= $birth_order ?>" onblur="trimString(this); convertToWords(this);" />
																								&nbsp; (first, second, third, etc.) </td>
																						</tr>
																					</table>
																						<table width="100%" border="0" cellspacing="0" cellpadding="0">
																							<tr>
																								<td bgcolor="#F9FFFB" class="style2"></td>
																							</tr>
																					</table></td>
																			</tr>
																	</table></td>
																	<td width="20%" align="right" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellspacing="0" cellpadding="0">
																			<tr>
																				<td valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="4" cellspacing="1">
																						<tr>
																							<td width="100%" height="27" bordercolor="#FFCCCC" bgcolor="#FFCCCC" class="style6">e. &nbsp; WEIGHT OF FETUS </td>
																						</tr>
																						<tr>
																							<td bgcolor="#F9FFFB" class="style2">
												<input name="birth_weight" id="birth_weight" type="text" size="10" onblur="trimString(this); chkDecimal(this);"
													onkeypress="return keyPressHandler(event);" maxlength="4" value="<?= number_format($birth_weight,0) ?>" />
																								&nbsp; grams </td>
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
														                      
				 <table width="90%" border="0" align="center" cellspacing="1">
					<tr>
					<td bgcolor="#FEC194"><table width="100%" border="0" cellpadding="4" cellspacing="0" bordercolor="#FFECDD" class="style2">
						<tr>
							<td width="17%" align="left" valign="middle" bordercolor="#FFCCCC" bgcolor="#FFCCCC" class="style6">6. &nbsp; MAIDEN NAME</td>
							<td width="35%" align="center" bordercolor="#FFECDD" bgcolor="#FFF7F0"><center>
							Given Name
							<?php
								if (empty($m_name_first)){
									$m_name_first = strtoupper($mother_fname);
								}
								if (empty($m_name_middle)){
									$m_name_middle = strtoupper($mother_maidenname);
								}
								if (empty($m_name_last)){
									$m_name_last = strtoupper($mother_mname);
								}
							 ?>
							<br />
							<input name="m_name_first" type="text" class="style2" id="m_name_first" onblur="trimString(this);" value="<?= $m_name_first ?>" size="50" />
							</center></td>
							<td width="22%" align="center" bordercolor="#FFECDD" bgcolor="#FFF7F0"><center>
							Middle Name <br />
							<input name="m_name_middle" type="text" class="style2" id="m_name_middle" onblur="trimString(this);" value="<?= $m_name_middle ?>" size="30" />
							</center></td>
							<td width="26%" align="center" bordercolor="#FFECDD" bgcolor="#FFF7F0"><center>
							Last Name <br />
							<input name="m_name_last" type="text" class="style2" id="m_name_last" 
                                 onblur="trimString(this); updateChildSName();" value="<?= $m_name_last ?>" size="30" />
							</center></td>
						</tr>
						</table></td>
					</tr>
					</table>
										 
					<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
					<tr>
						<td height="74" valign="top" bgcolor="#98D228"><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td height="74" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#F9FFFB">
								<tr>
								<td width="22%" height="71" align="left" valign="top" bgcolor="#F9FFFB"><table width="99%" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td width="35%" bgcolor="#FFECDD"><table width="100%" border="0" cellpadding="4" cellspacing="1">
											<tr>
											<td height="37" bordercolor="#FFCCCC" bgcolor="#FFCCCC" class="style6"> 7. &nbsp; CITIZENSHIP</td>
											</tr>
											<tr>
											<td bgcolor="#F9FFFB" class="style2">
											<select id="m_citizenship" name="m_citizenship">
												<?php
												# Retrieve record set of all citizenships
												$m_citizenship_obj = $obj_fetalDeathCert->getCitizenship();
												if (empty($m_citizenship)|| !$m_citizenship)
													$m_citizenship='PH';   # default citizenship, per users' request, is FILIPINO
												while ($result=$m_citizenship_obj ->FetchRow()){
													if (empty($result['citizenship']))
														$result['citizenship'] = $result['country_name'];
													$selected='';
													if ($m_citizenship==$result['country_code'])
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
								<td width="28%" height="71" align="right" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="4" cellspacing="1">
											<tr>
											<td colspan="2" width="100%" height="37" bordercolor="#FFCCCC" bgcolor="#FFCCCC" class="style6">8. &nbsp; RELIGION </td>
											</tr>
										</table></td>
									</tr>
									<tr>
										<td width="100%" valign="top" bgcolor="#F9FFFB" class="style2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
											<td height="31" bgcolor="#F9FFFB" class="style2"><select name="m_religion" id="m_religion">
												<?php
													# Retrieve record set of all religions
												$religion_obj = $person_obj->getReligion();
												if (empty($m_religion)|| !$m_religion)
													$m_religion=62;   # default religion, 'Roman Catholic', per users' request
												while ($result=$religion_obj->FetchRow()){
													$selected='';
													if ($m_religion==$result['religion_nr'])
														$selected='selected';
													echo '<option value="'.$result['religion_nr'].'" '.$selected.'>'.$result['religion_name']."</option> \n";
													}
												 ?>
											</select></td>
																							</tr>
																						</table>
																						<table width="100%" border="0" cellspacing="0" cellpadding="0">
																								<tr>
																									<td bgcolor="#F9FFFB" class="style2"></td>
																								</tr>
																						</table></td>
																				</tr>
																		</table></td>
																		<td width="27%" align="right" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellspacing="0" cellpadding="0">
																				<tr>
																					<td valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="4" cellspacing="1">
																							<tr>
																								<td width="100%" height="38" bordercolor="#FFCCCC" bgcolor="#FFCCCC" class="style6">9. &nbsp; OCCUPATION </td>
																							</tr>
																							<tr>
																								<td bgcolor="#F9FFFB" class="style2"><select name="m_occupation" id="m_occupation">
																									<?php
														# Retrieve record set of all occupations
													$occupation_obj = $person_obj->getOccupation();
													if (empty($m_occupation)|| !$m_occupation)
														$m_occupation=5;   # default occupation, 'Housekeeper', per users' request
													while ($result=$occupation_obj->FetchRow()){
														$selected='';
														if ($m_occupation==$result['occupation_nr'])
															$selected='selected';
														echo '<option value="'.$result['occupation_nr'].'" '.$selected.'>'.$result['occupation_name']."</option> \n";
														}
													 ?>
																								</select></td>
																							</tr>
																					</table></td>
																				</tr>
																		</table></td>
																		<td width="23%" align="right" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellspacing="0" cellpadding="0">
																				<tr>
																					<td valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="4" cellspacing="1">
																							<tr>
																								<td width="100%" height="23" bordercolor="#FFCCCC" bgcolor="#FFCCCC" class="style6">10. &nbsp; Age at the time<br />
																									<?= $spacevar6 ?> &nbsp; of this delivery: </td>
																							</tr>
																							<tr>
																								<td bgcolor="#F9FFFB" class="style2">
													<input name="m_age" id="m_age" type="text" size="5" onblur="trimString(this); chkInteger(this,countingNumber);"
														onkeypress="return keyPressHandler(event);" value="<?= $m_age ?>" />
																									&nbsp; years </td>
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
										 
										<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
											<tr>
												<td height="74" valign="top" bgcolor="#98D228"><table width="100%" border="0" cellspacing="0" cellpadding="0">
														<tr>
															<td height="74" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#F9FFFB">
																	<tr>
																		<td width="22%" height="71" align="left" valign="top" bgcolor="#F9FFFB"><table width="99%" border="0" cellspacing="0" cellpadding="0">
																				<tr>
																					<td width="35%" bgcolor="#FFECDD"><table width="100%" border="0" cellpadding="4" cellspacing="1">
																							<tr>
																								<td valign="top" height="37" bordercolor="#FFCCCC" bgcolor="#FFCCCC" class="style6"> 11a. &nbsp; Total number of<br />
																									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; children born alive:</td>
																							</tr>
																							<tr>
																								<td bgcolor="#F9FFFB" class="style2">
													<input name="m_total_alive" id="m_total_alive" type="text" size="5" onblur="trimString(this); chkInteger(this,wholeNumber);"
														onkeypress="return keyPressHandler(event);" value="<?= $m_total_alive ?>" /></td>
																							</tr>
																					</table></td>
																				</tr>
																		</table></td>
																		<td width="27%" align="right" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellspacing="0" cellpadding="0">
																				<tr>
																					<td valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="4" cellspacing="1">
																							<tr>
																								<td valign="top" width="100%" height="38" bordercolor="#FFCCCC" bgcolor="#FFCCCC" class="style6">b. &nbsp; No. of children still living:</td>
																							</tr>
																							<tr>
																								<td bgcolor="#F9FFFB" class="style2">
													<input name="m_still_living" id="m_still_living" type="text" size="5" onblur="trimString(this); chkInteger(this,wholeNumber);"
														onkeypress="return keyPressHandler(event);" value="<?= $m_still_living ?>" /></td>
																							</tr>
																					</table></td>
																				</tr>
																		</table></td>
																		<td width="27%" align="right" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellspacing="0" cellpadding="0">
																				<tr>
																					<td valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="4" cellspacing="1">
																							<tr>
																								<td valign="top" width="100%" height="38" bordercolor="#FFCCCC" bgcolor="#FFCCCC" class="style6">c. &nbsp; No. of children born alive but<br />
																									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;are now dead:</td>
																							</tr>
																							<tr>
																								<td bgcolor="#F9FFFB" class="style2">
													<input name="m_now_dead" id="m_now_dead" type="text" size="5" onblur="trimString(this); chkInteger(this,wholeNumber);"
														onkeypress="return keyPressHandler(event);" value="<?= $m_now_dead ?>" /></td>
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
										 
										<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
											<tr>
												<td bgcolor="#FEC194"><table width="100%" border="0" cellpadding="4" cellspacing="1">
														<tr>
															<td colspan="3" bgcolor="#FFCCCC" class="style6"> 12. &nbsp; RESIDENCE</td>
														</tr>
														<tr align="center">
															<?php
										if (empty($m_residence_basic))
											$m_residence_basic = mb_strtoupper($street_name);
										else
											$m_residence_basic = mb_strtoupper($m_residence_basic);

									if (empty($m_residence_brgy)|| !$m_residence_brgy){
									$m_residence_brgy = $brgy_nr;
									$municity = $address_country->getMunicityByBrgy($m_residence_brgy);
									$m_residence_mun = $municity['mun_nr'];

									$prov = $address_country->getProvinceByBrgy($m_residence_mun);
									$m_residence_prov = $prov['prov_nr'];
									}
								 ?>
															<td width="40%" bgcolor="#FFF7F0" class="style2"> House No. / Street / Barangay <br />
																	<input type="text" name="m_residence_basic" id="m_residence_basic" onblur="trimString(this);UpdateInformantAddress();" value="<?= $m_residence_basic ?>" size="60" />
																	<select id="m_residence_brgy" name="m_residence_brgy" onchange="UpdateInformantAddress()">
																		<option value="0">-Select a Barangay-</option>
																		<?php
										# Retrieve record set of all barangays
										$brgy_obj = $address_country->getBarangay();
										while ($result=$brgy_obj ->FetchRow()){
											$selected='';
											if ($m_residence_brgy==$result['brgy_nr'])
												$selected='selected';
												echo '<option value="'.$result['brgy_nr'].'" '.$selected.'>'.$result['brgy_name']."</option> \n";
											}
										?>
																	</select>
															</td>
															<td width="30%" bgcolor="#FFF7F0" class="style2">City / Municipality <br />
																	<select id="m_residence_mun" name="m_residence_mun" onchange="UpdateInformantAddress()">
																		<?php
										# Retrieve record set of all municipalities or cities
										$mun_obj = $address_country->getMunicipal();
										if (empty($m_residence_mun)|| !$m_residence_mun)
											$m_residence_mun=$row['mun_nr'];   # default municipality or city, 'Malaybalay City'

										while ($result=$mun_obj ->FetchRow()){
											$selected='';
											if ($m_residence_mun==$result['mun_nr'])
												$selected='selected';
												echo '<option value="'.$result['mun_nr'].'" '.$selected.'>'.$result['mun_name']."</option> \n";
											}
										?>
																	</select>
															</td>
															<td width="30%" bgcolor="#FFF7F0" class="style2"> Province <br />
																	<select id="m_residence_prov" name="m_residence_prov" onchange="UpdateInformantAddress()">
																		<?php
										# Retrieve record set of all provinces
										$prov_obj = $address_country->getProvince();
										if (empty($m_residence_prov)|| !$m_residence_prov)
											#$m_residence_prov=101300000;   # default province, 'Bukidnon'
											$m_residence_prov=$row['prov_nr'];   # default province, 'Bukidnon'
										while ($result=$prov_obj ->FetchRow()){
											$selected='';
											if ($m_residence_prov==$result['prov_nr'])
												$selected='selected';
												echo '<option value="'.$result['prov_nr'].'" '.$selected.'>'.$result['prov_name']."</option> \n";
											}
									?>
																	</select>
															</td>
														</tr>
												</table></td>
											</tr>
									</table></td></tr>
						</table></td>
					</tr>
					<tr>
						<td bgcolor="#F9FFFB">&nbsp;</td>
						<td bgcolor="#F9FFFB">&nbsp;</td>
						<td bgcolor="#F9FFFB">&nbsp;</td>
					</tr>
			</table></td>
		</tr>

	<!--- FATHER --->
		<tr>
			<td align="center" bordercolor="#FFCCCC" bordercolordark="#000000"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#A0C2DA" bgcolor="#A0C2DA">
					<tr>
						<td width="2%"><img src="images/brdr_pnk.gif" width="31" height="26" /></td>
						<td bgcolor="#FFCCCC" class="style5"><a name="father"></a>FATHER</td>
						<td align="right" bgcolor="#FFCCCC" class="style5"><a href="#goto">Go to Top</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3"  bordercolor="#A0C2DA"  bgcolor="#A0C2DA"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
								<tr>
									<td height="160" bgcolor="#F9FFFB">
		<!---Name of Father --->
					<table colspan="3" width="90%" border="0" align="center" cellpadding="1" cellspacing="0">
												<tr>
													<td bgcolor="#A0C2DA"><table width="100%" border="0" cellpadding="0" cellspacing="0" bordercolor="#FFECDD" class="style2">
															<tr>
																<td width="17%" align="left" valign="middle" bordercolor="#A0C2DA" bgcolor="#FFCCCC" class="style6">&nbsp; 13. &nbsp; NAME</td>
																<?php
									if (empty($f_name_first)){
										$f_name_first = strtoupper($father_fname);
									}
									if (empty($f_name_middle)){
										$f_name_middle = strtoupper($father_mname);
									}
									if (empty($f_name_last)){
										$f_name_last = strtoupper($father_lname);
									}
								 ?>
																<td width="25%" align="center" bordercolor="#A0C2DA" bgcolor="#EAF1F7"><center>
																	Given Name <br />
																	<input type="text" size="30" name="f_name_first" id="f_name_first"
										onblur="trimString(this); ack = checkFather(this); updateChildSName();" value="<?= $f_name_first ?>" />
																</center></td>
																<td width="25%" align="center" bordercolor="#A0C2DA" bgcolor="#EAF1F7"><center>
																	Middle Name <br />
																	<input type="text" size="30" name="f_name_middle" id="f_name_middle"
										onblur="trimString(this); ack = checkFather(this); updateChildSName();" value="<?= $f_name_middle ?>" />
																</center></td>
																<td width="25%" align="center" bordercolor="#A0C2DA" bgcolor="#EAF1F7"><center>
																	Last Name <br />
																	<input type="text" size="30" name="f_name_last" id="f_name_last"
										onblur="trimString(this); ack = checkFather(this); updateChildSName();" value="<?= $f_name_last ?>" />
																</center></td>
															</tr>
													</table></td>
												</tr>
						</table>&nbsp;

			<!-- Citizenship Etc.-->
					<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
						<td height="74" valign="top" bgcolor="#98D228"><table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td height="74" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#F9FFFB">
									<tr>
									<td width="22%" height="71" align="left" valign="top" bgcolor="#F9FFFB"><table width="99%" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td width="35%" bgcolor="#FFECDD"><table width="100%" border="0" cellpadding="4" cellspacing="1">
												<tr>
												<td height="37" bordercolor="#FFCCCC" bgcolor="#FFCCCC" class="style6"> 14. &nbsp; CITIZENSHIP</td>
												</tr>
												<tr>
												<td bgcolor="#F9FFFB" class="style2">
												<select id="f_citizenship" name="f_citizenship">
													<?php
													# Retrieve record set of all citizenships
													$f_citizenship_obj = $obj_fetalDeathCert->getCitizenship();
													if (empty($f_citizenship)|| !$f_citizenship)
														$f_citizenship='PH';   # default citizenship, per users' request, is FILIPINO
													while ($result=$f_citizenship_obj ->FetchRow()){
														if (empty($result['citizenship']))
															$result['citizenship'] = $result['country_name'];
														$selected='';
														if ($f_citizenship==$result['country_code'])
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
									<td width="28%" height="71" align="right" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="4" cellspacing="1">
												<tr>
												<td colspan="2" width="100%" height="37" bordercolor="#FFECDD" bgcolor="#FFCCCC" class="style6">15. &nbsp; RELIGION </td>
												</tr>
											</table></td>
										</tr>
										<tr>
											<td width="100%" valign="top" bgcolor="#F9FFFB" class="style2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
												<tr>
												<td height="31" bgcolor="#F9FFFB" class="style2"><select name="f_religion" id="f_religion">
													<?php
															# Retrieve record set of all religions
														$religion_obj = $person_obj->getReligion();
														if (empty($f_religion)|| !$f_religion)
															$f_religion=62;   # default religion, 'Roman Catholic', per users' request
														while ($result=$religion_obj->FetchRow()){
															$selected='';
															if ($f_religion==$result['religion_nr'])
																$selected='selected';
															echo '<option value="'.$result['religion_nr'].'" '.$selected.'>'.$result['religion_name']."</option> \n";
															}
													?>
												</select></td>
												</tr>
											</table>
												<table width="100%" border="0" cellspacing="0" cellpadding="0">
												<tr>
													<td bgcolor="#F9FFFB" class="style2"></td>
												</tr>
											</table></td>
										</tr>
									</table></td>
									<td width="27%" align="right" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="4" cellspacing="1">
												<tr>
												<td width="100%" height="38" bordercolor="#FFECDD" bgcolor="#FFCCCC" class="style6">16. &nbsp; OCCUPATION </td>
												</tr>
												<tr>
												<td bgcolor="#F9FFFB" class="style2"><select name="f_occupation" id="f_occupation">
												<?php
													# Retrieve record set of all occupations
													$occupation_obj = $person_obj->getOccupation();
													if (empty($f_occupation)|| !$f_occupation)
														$f_occupation=1;   # default occupation, 'Not Indicated'
													while ($result=$occupation_obj->FetchRow()){
														$selected='';
														if ($f_occupation==$result['occupation_nr'])
														$selected='selected';
														echo '<option value="'.$result['occupation_nr'].'" '.$selected.'>'.$result['occupation_name']."</option> \n";
														}
												 ?>
													</select></td>
												</tr>
											</table></td>
										</tr>
									</table></td>
									<td width="23%" align="right" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="4" cellspacing="1">
												<tr>
												<td width="100%" height="23" bordercolor="#FFECDD" bgcolor="#FFCCCC" class="style6">17. &nbsp; Age at the time<br />
												<?= $spacevar6 ?> &nbsp; of this delivery: </td>
												</tr>
												<tr>
												<td bgcolor="#F9FFFB" class="style2">
													<input name="f_age" id="f_age" type="text" size="5" onblur="trimString(this); chkInteger(this,countingNumber);"
														onkeypress="return keyPressHandler(event);" value="<?= $f_age ?>" />
													&nbsp; years </td>
												</tr>
											</table></td>
										</tr>
									</table></td>
									</tr>
								</table></td>
							</tr>
						</table></td>
						</tr>
					</table>	<!---end of Citizenship Etc.--->
								</table></td>
					</tr>
		</table></td>
		</tr>
	</table></td>
				 </tr>
&nbsp;

<!--- for Date and Place --->
	<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#A0C2DA">
	<tr>
		<td height="26" colspan="2" bgcolor="#F9FFFB" class="style6">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFCCCC">
				<tr>
					<td width="3%"><img src="images/brdr_pnk.gif" width="31" height="32" /></td>
					<td width="97%" class="style6">
				<?= $spacevar6 ?><?= $spacevar4 ?>18. &nbsp; DATE AND PLACE OF MARRIAGE OF PARENTS (if applicable) </td>
				</tr>
			</table>
			</td>
		</tr>

 <tr>
	<td bordercolor="#98D228"  bgcolor="#98D228"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
	<tr>
	 <td height="42" bordercolor="98D228" bgcolor="#F9FFFB" class="style2" id="married_parents">
		<?= $spacevar6 ?> <?= $spacevar6 ?> <?= $spacevar2 ?>
		<?php
			#$parent_marriage_info_tmp
			$phpfd=$date_format;
			$phpfd=str_replace("dd", "%d", strtolower($phpfd));
			$phpfd=str_replace("mm", "%m", strtolower($phpfd));
			$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

			if (($parent_marriage_date!='0000-00-00')  && ($parent_marriage_date!=""))
				$parent_marriage_date = @formatDate2Local($parent_marriage_date,$date_format);
			else
				$parent_marriage_date='';

			$sDateMarriage= '<input name="parent_marriage_date" type="text" size="15" maxlength=10 value="'.$parent_marriage_date.'"'.
							'onFocus="this.select();"
							id = "parent_marriage_date"
							onBlur="IsValidDate(this,\''.$date_format.'\'); updateChildSName();"
							onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
							<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="marriage_date_trigger" style="cursor:pointer" >
							<font size=3>[';
			ob_start();
			?>
		<script type="text/javascript">
			Calendar.setup ({
				inputField : "parent_marriage_date", ifFormat : "<?php echo $phpfd?>", showsTime : false,
					button : "marriage_date_trigger",
					onClose: function(cal) { cal.hide(); updateChildSName();},
					singleClick : true, step : 1
			});
		</script>
		<?php

				if ((empty($parent_marriage_place)) || ($parent_marriage_place=='N/A'))
				$parent_marriage_place = " ";

			$calendarSetup = ob_get_contents();
			ob_end_clean();

			$sDateMarriage .= $calendarSetup;
			$dfbuffer="LD_".strtr($date_format,".-/","phs");
			$sDateMarriage = $sDateMarriage.$$dfbuffer.']';
			?>
		 &nbsp;&nbsp; <?= $sDateMarriage ?>

		at &nbsp;<input type="text" name="parent_marriage_place" id="parent_marriage_place" size="60"
			value="<?=$parent_marriage_place?>" />
		</td>
	 </tr>
	 </table></td>
	 </tr>
	 <tr>
	<td bgcolor="#F9FFFB">&nbsp;</td>
	 </tr>
	</table>

<!--- for medical certificate --->
	 <tr>
	 <td align="center" bordercolor="#FFCCFF" bordercolordark="#000000"><table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
			 <tr>
				 <td width="42%" bgcolor="#FFCCCC"><img src="images/brdr_pnk.gif" width="31" height="32" /></td>
				 <td width="36%" bgcolor="#FFCCCC"><span class="style6"><font size="+1"><a name="medcert"></a>MEDICAL CERTIFICATE</font></span></td>
				 <td width="20%" align="right" bgcolor="#FFCCCC" class="style5"><a href="#goto">Go to Top</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			 </tr>
			 <tr>
				 <td colspan="3" bgcolor="#98D228"><table width="100%" border="0" cellspacing="1" cellpadding="1">
						 <tr>
							 <td height="650" align="center" bgcolor="#F9FFFB"><!--- causes of fetal death --->
									 <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
										 <tr>
											 <td bgcolor="#FEC194"><table width="100%" border="0" cellpadding="4" cellspacing="1">
													 <tr>
														 <td colspan="3" bgcolor="#FFCCCC" class="style6"> 19. &nbsp; CAUSES OF FETAL DEATH</td>
													 </tr>
													 <tr>
														 <td width="33%" bgcolor="#FFFFFF" class="style2"> a. Main disease/condition of fetus &nbsp;</td>
														 <td width="67%" bgcolor="#FFFFFF" class="style2">
								<!-- <input name="cause1" id="cause1" type="text" size="50" value="<?= $tmp_death_cause['cause1'] ?>" /> -->
								<input name="cause1" id="cause1" type="text" size="50"
									value="<? if($tmp_death_cause['cause1']) echo $tmp_death_cause['cause1']; else  echo "Still Born Baby"; ?>" />
														 </td>
													 </tr>
													 <tr>
														 <td bgcolor="#FFFFFF" class="style2"> b. Other diseases/conditions of fetus</td>
														 <td bgcolor="#FFFFFF" class="style2"><input name="cause2" id="cause2" type="text" size="50" value="<?= $tmp_death_cause['cause2'] ?>" />
														 </td>
													 </tr>
													 <tr>
														 <td bgcolor="#FFFFFF" class="style2"> c. Main maternal disease/condition affecting fetus</td>
														 <td bgcolor="#FFFFFF" class="style2"><input name="cause3" id="cause3" type="type" size="50" value="<?= $tmp_death_cause['cause3'] ?>" />
														 </td>
													 </tr>
													 <tr>
														 <td bgcolor="#FFFFFF" class="style2"> d. Other maternal disease/condition affecting fetus</td>
														 <td bgcolor="#FFFFFF" class="style2"><input name="cause4" id="cause4" type="text" size="50" value="<?= $tmp_death_cause['cause4'] ?>" />
														 </td>
													 </tr>
													 <tr>
														 <td bgcolor="#FFFFFF" class="style2"> e. Other relevant circumstances</td>
														 <td bgcolor="#FFFFFF" class="style2"><input name="cause5" id="cause5" type="text" size="50" value="<?= $tmp_death_cause['cause5'] ?>" />
														 </td>
													 </tr>
											 </table></td>
										 </tr>
										 <tr>
											 <td bgcolor="#F9FFFB">&nbsp;</td>
										 </tr>
									 </table>
						 <!--- fetus died before/during --->
									 <table width="90%" bgcolor="#FEC194" border="0" cellpadding="2" cellspacing="1">
										 <tr bgcolor="#FFCCCC">
											 <td bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="2" cellspacing="0">
													 <tr>
														 <td bgcolor="#FFCCCC" width="15%"><span class="style6">20. &nbsp; FETUS DIED: </span> </td>
														 <td width="85%" bgcolor="#FFFFFF" class="style2">
								<input type="hidden" name="death_occurrence" id="death_occurrence" value="<?php if($death_occurrence) echo $death_occurrence; else echo "1 - Before labor"; ?>" />
															 &nbsp;
															 <?= $spacevar3 ?>
															 <input name="death_occurrence_tmp" id="death_occurrence_tmp" type="radio" onclick="occurrenceOfFetalDeath(this);" value="1" <?php if(($death_occurrence_tmp == '1')||(!$death_occurrence_tmp)) echo "checked"; ?> />
															 1. Before labor
															 <?= $spacevar6 ?>
															 <input name="death_occurrence_tmp" id="death_occurrence_tmp" type="radio" onclick="occurrenceOfFetalDeath(this);" value="2" <?php if($death_occurrence_tmp == '2') echo "checked"; ?> />
															 2. During labor/delivery
															 <?= $spacevar6 ?>
															 <input name="death_occurrence_tmp" id="death_occurrence_tmp" type="radio" onclick="occurrenceOfFetalDeath(this);" value="3" <?php if($death_occurrence_tmp == '3') echo "checked"; ?> />
															 3. Unknown </td>
													 </tr>
											 </table></td>
										 </tr>
										 <tr>
											 <td bgcolor="#F9FFFB">&nbsp;</td>
										 </tr>
									 </table>
							<!--- length of pregnancy --->
									 <table width="90%" bgcolor="#FEC194" border="0" cellpadding="2" cellspacing="1">
										 <tr bgcolor="#FFDDFF">
											 <td bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="2" cellspacing="0">
													 <tr>
														 <td bgcolor="#FFCCCC" width="25%"><span class="style6">21. &nbsp; LENGTH OF PREGNANCY: </span> </td>
														 <td width="75%" bgcolor="#FFFFFF" class="style2"><input name="pregnancy_length" type="text" id="pregnancy_length" onblur="trimString(this); chkInteger(this,countingNumber);" value="<?= $pregnancy_length ?>" maxlength="4" onkeypress="return keyPressHandler(event);" />
															 &nbsp; completed weeks </td>
													 </tr>
											 </table></td>
										 </tr>
										 <tr>
											 <td bgcolor="#F9FFFB">&nbsp;</td>
										 </tr>
									 </table>
							 <!--- attendant --->
									 <table width="90%" bgcolor="#FEC194" border="0" cellpadding="2" cellspacing="1">
										 <tr bgcolor="#FFDDFF">
											 <td bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="2" cellspacing="0">
													 <tr>
														 <td colspan="3" rowspan="3" valign="top" bgcolor="#FFCCCC" width="25%"><span class="style6">22a. &nbsp; ATTENDANT: </span> </td>
														 <td width="25%" bgcolor="#F9FFFB" class="style2"><input name="attendant_type" id="attendant_type" type="hidden" value="<?php if($attendant_type) echo $attendant_type; else echo "1 - Physician";  ?>" />
															 <input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="1" <?php if(($attendant_type_tmp == '1')||(!$attendant_type_tmp)) echo "checked"; ?> />
															 1. Physician </td>
														 <td width="45%" bgcolor="#F9FFFB" class="style2"><input name="attendant_type_tmp" id="attendant_type_tmp"  onclick="typeOfAttendant(this)" type="radio" value="4" <?php if($attendant_type_tmp == '4') echo "checked"; ?> />
															 4. Hilot (Traditional Midwife) </td>
													 </tr>
													 <tr>
														 <td bgcolor="#F9FFFB" class="style2"><input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="2" <?php if($attendant_type_tmp == '2') echo "checked"; ?> />
															 2. Nurse </td>
														 <td bgcolor="#F9FFFB" class="style2"><input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="5" <?php if($attendant_type_tmp == '5') echo "checked"; ?> />
															 5. Others (Specify)
															 <input type="text" size="50" name="attendant_type_others" id="attendant_type_others" onblur="trimString(this); $('attendant_type').value = '5 - '+this.value;"
									 <?php if ($attendant_type_tmp == '5') echo 'value="'.$attendant_type_others.'"'; else echo "disabled"; ?> />
														 </td>
													 </tr>
													 <tr>
														 <td bgcolor="#F9FFFB" class="style2"><input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="3" <?php if($attendant_type_tmp == '3') echo "checked"; ?> />
															 3. Midwife </td>
														 <td bgcolor="#F9FFFB" class="style2"><input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="6" <?php if($attendant_type_tmp == '6') echo "checked"; ?> />
															 6. None</td>
													 </tr>
											 </table></td>
										 </tr>
										 <tr>
											 <td bgcolor="#F9FFFB">&nbsp;</td>
										 </tr>
									 </table>
							 <!-- certification -->
									 <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
										 <tr>
											 <td bgcolor="#FEC194"><table width="100%" border="0" cellpadding="4" cellspacing="1">
													 <tr>
														 <td colspan="3" bgcolor="#FFCCCC" class="style6"> 22b. &nbsp; CERTIFICATION </td>
													 </tr>
													 <tr align="center">
														 <td bgcolor="#FFFFFF" align="justify"><span class="style2">
															 <?= $spacevar6 ?>
															 <?= $spacevar6 ?>
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
															 I hereby certify that the foregoing particulars are correct as near as same can be ascertained and I further certify that the fetus was born dead at &nbsp;&nbsp;
													<input name="death_time" type="text" class="style2" id="death_time" onchange="setFormatTime(this);" value="<?= $death_time ?>" onkeypress="return keyPressHandler(event);" />
															 &nbsp;o'clock &nbsp;
								 <select id="selAMPM" name="selAMPM">
								 <option value="A.M." <?php if ($death_time_period == 'AM') echo "selected";?> >A.M.</option>
								 <option value="P.M." <?php if ($death_time_period == 'PM') echo "selected";?> >P.M.</option>
								 </select>
															 on the date indicated above. </span></td>
													 </tr>
													 <tr>
														 <td bgcolor="#F9FFFB"><table width="90%" border="0" align="center" cellspacing="2">
																 <tr>
																	 <td width="50%" colspan="2" bgcolor="#EAF1F7" class="style2">Signature</td>
																	 <td width="50%" rowspan="2" bgcolor="#EAF1F7" class="style2"> Address<br />
																				<?php
																							if (empty($attendant_address)){
																								$attendant_address = $row['hosp_name'].", ".$row['brgy_name'].", ".$row['mun_name'];
																							}
																				?>
																			 <textarea name="attendant_address" id="attendant_address" cols="50" rows="2"><?= $attendant_address ?></textarea>
																	 </td>
																 </tr>
																 <tr>
																	 <td bgcolor="#EAF1F7" class="style2"> Name in Print </td>
																	 <td bgcolor="#EAF1F7" class="style2"><select id="attendant_name" name="attendant_name">
																			 <option value="0">-Select a Doctor-</option>
																			 <?php
											# Retrieve record set of all doctors
											$doctor_obj = $pers_obj->getDoctors(1," AND d.nr IN (139,140,155) ");

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
																	 <td bgcolor="#EAF1F7" class="style2"> Title or Position </td>
																	 <td bgcolor="#EAF1F7" class="style2"><input type="text" size="50" name="attendant_title" id="attendant_title" onblur="trimString(this)" value="<?= $attendant_title ?>" />
																	 </td>
																	 <td bgcolor="#EAF1F7" class="style2">
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
												<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="attendant_date_trigger" style="cursor:pointer" >
												<font size=3>[';
											ob_start();
										?>
																		 <script type="text/javascript">
										Calendar.setup ({
												inputField : "attendant_date_sign", ifFormat : "<?php echo $phpfd?>",
													showsTime : false, button : "attendant_date_trigger",
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
																		 Date &nbsp;&nbsp; <?= $sDateAttend ?>
																	 </td>
																 </tr>
														 </table></td>
													 </tr>
					 <!--- --->    </table> 	<!--- --->
											 </td>
										 </tr>
								 </table></td>
						 </tr>
				 </table></td>
			 </tr>
			 <tr>
				 <td bgcolor="#F9FFFB">&nbsp;</td>
			 </tr>
			 <!--- CORPSE DISPOSAL etc. --->
			 <tr>
				 <td colspan="3" height="100" valign="top" bgcolor="#FFFFFF"><table width="100%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#A0C2DA">
						 <tr>
							 <!-- CORPSE DISPOSAL -->
							 <td width="37%" height="100%" valign="top" bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="2" cellspacing="1">
									 <tr>
										 <td bgcolor="#FFCCCC" class="style6"><?= $spacevar6 ?> <?= $spacevar6 ?> <?= $spacevar4 ?> 23. &nbsp; Corpse Disposal </td>
									 </tr>
									 <tr>
						<?php
						if (!$corpse_disposal_tmp)
							$corpse_disposal_tmp = 0;
					?>
										 <td bgcolor="#F9FFFB" class="style2"><input type="hidden" name="corpse_disposal" id="corpse_disposal" value="<?php if($corpse_disposal) echo $corpse_disposal; else echo "1"; ?>" />
												 <?= $spacevar6 ?> <?= $spacevar6 ?> <?= $spacevar4 ?> <input name="corpse_disposal_tmp" id="corpse_disposal_tmp" type="radio" onclick="typeOfDisposal(this)" value="1" <?php if($corpse_disposal_tmp == '1') echo "checked"; ?> />
											 1. Burial </td>
									 </tr>
									 <tr>
										 <td bgcolor="#F9FFFB" class="style2"><?= $spacevar6 ?> <?= $spacevar6 ?> <?= $spacevar4 ?> <input name="corpse_disposal_tmp" id="corpse_disposal_tmp" type="radio" onclick="typeOfDisposal(this)" value="2" <?php if($corpse_disposal_tmp == '2') echo "checked"; ?> />
											 2. Cremation </td>
									 </tr>
									 <tr>
										 <td bgcolor="#F9FFFB" class="style2"><?= $spacevar6 ?> <?= $spacevar6 ?> <?= $spacevar4 ?> <input name="corpse_disposal_tmp" id="corpse_disposal_tmp" type="radio" onclick="typeOfDisposal(this)" value="3" <?php if($corpse_disposal_tmp == '3') echo "checked";?> />
											 3. Others (Specify)
											 <input  type="text" class="style2" name="corpse_disposal_others" id="corpse_disposal_others" onblur="trimString(this); $('corpse_disposal').value= '3 - '+this.value;" value="<?php if ($corpse_disposal_tmp == '3') echo $corpse_disposal_others; ?>" />
										 </td>
									 </tr>
									 <tr>
										 <td bgcolor="#F9FFFB" class="style2">&nbsp;</td>
									 </tr>
							 </table></td>

							 <!-- BURIAL / CREMATION PERMIT -->
							 <td width="33%" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="2" cellspacing="1">
									 <tr>
										 <td colspan="2" bgcolor="#FFCCCC" class="style6">24. &nbsp; Burial/Cremation Permit </td>
									 </tr>
									 <tr>
										 <td width="27%" bgcolor="#F9FFFB" class="style2"> Number </td>
										 <td width="73%" bgcolor="#F9FFFB" class="style2"><input name="burial_permit" type="text" class="style2" id="burial_permit" value="<?=$burial_permit?>"/></td>
									 </tr>
									 <tr>
										 <td bgcolor="#F9FFFB" class="style2"> Date Issued
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
								<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="burial_date_issued_trigger" style="cursor:pointer" >
								<font size=3>[';
							ob_start();
						 ?>
											<script type="text/javascript">
							Calendar.setup ({
									inputField : "burial_date_issued", ifFormat : "<?php echo $phpfd ?>",
									showsTime : false, button : "burial_date_issued_trigger",
									onClose: function(cal) { cal.hide();},
									singleClick : true, step : 1
							});
						 </script>
											<?php
							$calendarSetup = ob_get_contents();
							ob_end_clean();
							$sDateBurial .= $calendarSetup;
							$dfbuffer="LD_".strtr($date_format,".-/","phs");
							$sDateBurial = $sDateBurial.$$dfbuffer.']';
						 ?></td>
										 <td bgcolor="#F9FFFB" class="style2"><?= $sDateBurial ?>
					 </td>
									 </tr>
							 </table></td>

							 <!-- AUTOPSY -->
							 <td width="30%" valign="top" bgcolor="#F9FFFB"><table width="100%" border="0" cellpadding="2" cellspacing="1" class="style2">
									 <tr>
										 <td bgcolor="#FFCCCC" class="style6">25. &nbsp; Autopsy </td>
									 </tr>
									 <tr>
										 <td bgcolor="#F9FFFB"><input name="is_autopsy" type="hidden" id="is_autopsy" value="<?php if($is_autopsy) echo $is_autopsy; else echo "2"; ?>" />
												 <input name="autopsy_tmp" id="autopsy_tmp" type="radio" onclick="checkIfAutopsied(this);" value="1" <?php if($is_autopsy=='1') echo "checked"; ?> />
											 1. Yes </td>
									 </tr>
									 <tr>
										 <td bgcolor="#F9FFFB">
							 <input name="autopsy_tmp" id="autopsy_tmp" type="radio" onclick="checkIfAutopsied(this);" value="2" <?php if($is_autopsy=='2') echo "checked"; ?> />
											 2. No </td>
									 </tr>
							 </table></td>
						 </tr>
				 </table></td>
			 </tr>
	</table></td>
	</tr>
	 <br />
	 <!-- NAME AND ADDRESS OF CEMETERY OR CREMATORY -->
	 <tr>
	 <td height="30" valign="top" bgcolor="#FFFFFF"><table width="90%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#A0C2DA">
		 <tr>
			 <td width="33%" bgcolor="#FFCCCC"><span class="style6"><?= $spacevar6 ?> <?= $spacevar6 ?> <?= $spacevar4 ?> 26. &nbsp; Name and Address of Cemetery or Crematory</span></td>
			 <td width="67%" bgcolor="#FFFFFF"><input name="cemetery_name_address" type="text" class="style2" id="cemetery_name_address" size="50" value="<?= $cemetery_name_address ?>" />
			 </td>
		 </tr>
	 </table></td>
	 </tr>
	 <tr>
	 <td bgcolor="#F9FFFB">&nbsp;</td>
	 <td bgcolor="#F9FFFB">&nbsp;</td>
	 </tr>

<!--- Informant --->
	<table height="200" width="90%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#A0C2DA">
	<tr>
		<td height="26" colspan="2" bgcolor="#F9FFFB" class="style6">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFCCCC">
				<tr>
					<td width="3%"><img src="images/brdr_pnk.gif" width="31" height="32" /></td>
					<td width="97%" class="style6"><?= $spacevar6 ?><?= $spacevar3 ?> 27. &nbsp; INFORMANT </td>
				</tr>
			</table>
			</td>
		</tr>

		<tr>
	 <td colspan="3" bgcolor="#98D228"><table width="100%" border="0" cellspacing="1" cellpadding="1">
		<tr>
			<td height="155" align="center" bgcolor="#F9FFFB"><table width="90%" border="0" align="center" cellpadding="0" cellspacing="1">
		<tr>
		 <td bordercolor="#98D228" bgcolor="#F9FFFB" class="style2"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="2">
			 <tr>
			 <td width="50%" height="34" bgcolor="#EAF1F7" class="style2">Signature</td>
			 <td width="50%" rowspan="2" bgcolor="#EAF1F7" class="style2"> Address <br />
				 <textarea name="informant_address" id="informant_address" cols="50" rows="2"><?= $informant_address ?></textarea>
			 </td>
			 </tr>
			 <tr>
			 <td height="57" bgcolor="#EAF1F7" class="style2"> Name in Print&nbsp;&nbsp;
				 <input type="text" size="50" name="informant_name" id="informant_name" onblur="trimString(this)" value="<?= $informant_name ?>" />
			 </td>
			 </tr>
			 <tr>
			 <td height="54" bgcolor="#EAF1F7" class="style2"> Relationship to the fetus &nbsp;&nbsp;
				 <input type="text" size="40" name="informant_relation" id="informant_relation" onblur="trimString(this)" value="<?= $informant_relation ?>" />
			 </td>
			 <td bgcolor="#EAF1F7" class="style2">
				 <?php
					$phpfd=$date_format;
					$phpfd=str_replace("dd", "%d", strtolower($phpfd));
					$phpfd=str_replace("mm", "%m", strtolower($phpfd));
					$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

					if (($informant_date_sign!='0000-00-00')  && ($informant_date_sign!=""))
						$informant_date_sign = @formatDate2Local($informant_date_sign,$date_format);
					else
						$informant_date_sign='';

					$sDateInform= '<input name="informant_date_sign" type="text" size="15" maxlength=10 value="'.$informant_date_sign.'"'.
								'onFocus="this.select();"
								id = "informant_date_sign"
								onBlur="IsValidDate(this,\''.$date_format.'\'); "
								onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
								<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="informant_date_trigger" style="cursor:pointer" >
								<font size=3>[';
							ob_start();
				?>
								<script type="text/javascript">
					Calendar.setup ({
							inputField : "informant_date_sign",
							ifFormat : "<?php echo $phpfd?>",
							showsTime : false, button : "informant_date_trigger",
							onClose: function(cal) { cal.hide();},
							singleClick : true, step : 1
					});
				</script>
								<?php
					$calendarSetup = ob_get_contents();
					ob_end_clean();
					$sDateInform .= $calendarSetup;
					$dfbuffer="LD_".strtr($date_format,".-/","phs");
					$sDateInform = $sDateInform.$$dfbuffer.']';
				?>
											 Date &nbsp;&nbsp;
											 <?= $sDateInform ?>
										 </td>
									 </tr>
							 </table></td>
						 </tr>
				 </table></td>
			</tr>
			</table></td>
	 </tr>
	 <tr>
	<td bgcolor="#F9FFFB">&nbsp;</td>
	 </tr>
	</table>

<!--- prepared by --->
<tr>
 <td class="style2"><table  align="center" width="90%" border="0" cellpadding="0" cellspacing="0">
	 <tr>
	 <td bgcolor="#FF6600" colspan="2"><span class="style6"><?= $spacevar6 ?> <?= $spacevar6 ?> <?= $spacevar4 ?> 28. &nbsp; PREPARED BY</span></td>
	 <td bgcolor="#FF6600"></td>
	 </tr>
	 <tr>
	 <td colspan="2" class="style2" align="center">
		 <table width="100%" border="0" cellspacing="2">
			 <tr>
			 <td height="25" colspan="2" bgcolor="#FFF3EC" class="style2">
			 <?= $spacevar6 ?> <?= $spacevar6 ?> <?= $spacevar6 ?> Signature</td>
			 </tr>
			 <tr>
			 <td bgcolor="#FFF3EC" class="style2" width="22%">
			 <?= $spacevar6 ?> <?= $spacevar6 ?> <?= $spacevar6 ?> Name in Print &nbsp;&nbsp;</td>
			 <?php
				/*if (empty($encoder_name))
					$encoder_name = strtoupper($HTTP_SESSION_VARS['sess_user_fullname']);

				$personell = $pers_obj->getPersonellInfo($HTTP_SESSION_VARS['sess_user_personell_nr']);

				if (empty($encoder_title)){
					if ($personell['job_position'])
						$encoder_title = strtoupper($personell['job_position']);
					else
						$encoder_title = strtoupper($personell['job_function_title']);
					*/
					if (empty($encoder_name)){
							$encoder_info = $pers_obj->get_Signatory('fetaldeath');
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
				#}
				?>
			 <td bgcolor="#FFF3EC" width="78%"><input name="encoder_name" type="text" class="style2" id="encoder_name" onblur="trimString(this)" value="<?= $encoder_name ?>" size="50" />
			 </td>
			 </tr>
			 <tr>
			 <td bgcolor="#FFF3EC" class="style2">
			 <?= $spacevar6 ?> <?= $spacevar6 ?> <?= $spacevar6 ?> Title or Position&nbsp;&nbsp;</td>
			 <td bgcolor="#FFF3EC"><input name="encoder_title" type="text" class="style2" id="encoder_title" onblur="trimString(this)" value="<?= $encoder_title ?>" size="40" />
			 </td>
			 </tr>
			 <tr>
			 <td bgcolor="#FFF3EC" class="style2">
			 <?= $spacevar6 ?> <?= $spacevar6 ?> <?= $spacevar6 ?> Date&nbsp;&nbsp;</td>
			 <td bgcolor="#FFF3EC" class="style2">
				 <?php
					$phpfd=$date_format;
					$phpfd=str_replace("dd", "%d", strtolower($phpfd));
					$phpfd=str_replace("mm", "%m", strtolower($phpfd));
					$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

					if (($encoder_date_sign!='0000-00-00')  && ($encoder_date_sign!=""))
						$encoder_date_sign = @formatDate2Local($encoder_date_sign,$date_format);
					else
						$encoder_date_sign='';

					$sDatePrep= '<input name="encoder_date_sign" type="text" size="15" maxlength=10 value="'.$encoder_date_sign.'"'.
								'onFocus="this.select();"
								id = "encoder_date_sign"
								onBlur="IsValidDate(this,\''.$date_format.'\'); "
								onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
								<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="encoder_date_trigger" style="cursor:pointer" >
								<font size=3>[';
							ob_start();
				?>
				<script type="text/javascript">
					Calendar.setup ({
						inputField : "encoder_date_sign", ifFormat : "<?php echo $phpfd?>",
						showsTime : false, button : "encoder_date_trigger",
						onClose: function(cal) { cal.hide();},
						singleClick : true, step : 1
					});
				</script>
				<?php
					$calendarSetup = ob_get_contents();
					ob_end_clean();
					$sDatePrep .= $calendarSetup;
					$dfbuffer="LD_".strtr($date_format,".-/","phs");
					$sDatePrep = $sDatePrep.$$dfbuffer.']';
				 ?>
								<?= $sDatePrep ?>
							 </td>
						 </tr>
						 <tr>
							 <td colspan="2" bgcolor="#FFF3EC">&nbsp;</td>
						 </tr>
					 </table></td>
				 <td width="0%" bgcolor="#F9FFFB" class="style2">&nbsp;</td>
			 </tr>
		 </table>
	 <div align="center">
	<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td  bgcolor="#FFCCCC">
				<center>
				<?php
			if (!$fetalDeathCertInfo || empty($fetalDeathCertInfo)){
				echo '<input type="hidden" name="mode" id="mode" value="save">'."\n";
				echo '<input type="submit" name="Submit" value="Save">'."\n";
			}else{
				echo '<input type="hidden" name="mode" id="mode" value="update">'."\n";
				echo '<input type="button" name="Print" value="Print" onClick="printFetalDeathCert(\''.$pid.'\')">'."\n &nbsp; &nbsp;";
				echo '<input type="submit" name="Submit" value="Update">'."\n";
			}
			echo '<input type="hidden" name="pid" id="pid" value="'.$pid.'">'."\n";
			?>
	&nbsp; &nbsp;
	<input type="button" name="Cancel" value="Cancel"  onclick="window.close()" />
			 </center>
		 </td>
		</tr>
	</table>
</div>
</form>
<script language="javascript">
<?php
	echo $presetJavascript;
?>
</script>

</body>
</html>
