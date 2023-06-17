<?php
include("roots.php");
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');

#added by VAN 02-18-08
define('NO_2LEVEL_CHK',1);

define('LANG_FILE','aufnahme.php');
$local_user='aufnahme_user';

include_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address('country');
$address_brgy = new Address('barangay');

require($root_path.'include/inc_front_chain_lang.php');

if(isset($_GET['pid'])&& $_GET['pid']){
	$pid = $_GET['pid'];
}
if(isset($_POST['pid']) && $_POST['pid']){
	$pid = $_POST['pid'];
}

#added by VAN 03-01-08
if(isset($_GET['encounter_nr'])&& $_GET['encounter_nr']){
	$encounter_nr = $_GET['encounter_nr'];
}
#echo "encounter_nr = ".$encounter_nr;

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj = new Person($pid);

include_once($root_path.'include/care_api_classes/class_cert_death.php');
$obj_deathCert = new DeathCertificate($pid);

if($pid){
	//if(!($basicInfo = $person_obj->BasicDataArray($pid))){
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

$birthYear = intval(substr($date_birth, 0, 4)); 
$birthMonth = intval(substr($date_birth, 5, 7)); 
$birthDay = intval(substr($date_birth, 8, 10)); 
/*
echo "birthYear = '".$birthYear."' <br>\n";
echo "birthMonth = '".$birthMonth."' <br>\n";
echo "birthDay = '".$birthDay."' <br>\n";
*/
/*
#print_r($basicInfo);
echo "brgy_nr = '".$brgy_nr."' <br>\n";
echo "mun_nr = '".$mun_nr."' <br>\n";
echo "prov_nr = '".$prov_nr."' <br>\n";
echo "HTTP_POST_VARS['death_date'] = '".$HTTP_POST_VARS['death_date']."' <br>\n";
#echo "brgy_row  : <br>"; print_r($brgy_row); echo "<br> \n";
echo "HTTP_POST_VARS : <br>"; print_r($HTTP_POST_VARS); echo "<br> \n";
#echo "basicInfo : <br>"; print_r($basicInfo); echo "<br> \n";

$death_age = "";
$death_months = "";
$death_days = "";
echo "death_age = '".$death_age."'; intval(death_age) = '".intval($death_age)."' <br>\n";
echo "death_months = '".$death_months."'; intval(death_months) = '".intval($death_months)."' <br>\n";
echo "death_days = '".$death_days."'; intval(death_days) = '".intval($death_days)."' <br>\n";

$HTTP_POST_VARS['hours']='23';
$HTTP_POST_VARS['minutes']='10';
$HTTP_POST_VARS['sec']='1';
echo "HTTP_POST_VARS['hours'] = '".$HTTP_POST_VARS['hours']."' <br>\n";
echo "HTTP_POST_VARS['minutes'] = '".$HTTP_POST_VARS['minutes']."' <br>\n";
echo "HTTP_POST_VARS['sec'] = '".$HTTP_POST_VARS['sec']."' <br>\n";
*/

if ( (intval($death_age)>0) || (intval($death_months)>0) || (intval($death_days)>7) ){
	#unnecessary info if age at death is more than 7 days
	$HTTP_POST_VARS['m_age']='';
	$HTTP_POST_VARS['delivery_method']='';
	$HTTP_POST_VARS['pregnancy_length']='';
	$HTTP_POST_VARS['birth_type']='';
	$HTTP_POST_VARS['birth_rank']='';
	$HTTP_POST_VARS['cause1']='';
	$HTTP_POST_VARS['cause2']='';
	$HTTP_POST_VARS['cause3']='';
	$HTTP_POST_VARS['cause4']='';
	$HTTP_POST_VARS['cause5']='';
}else{
	#unnecessary info if age at death is less than or equal to 7 days
/*
	$HTTP_POST_VARS['cause6']='';
	$HTTP_POST_VARS['cause7']='';
	$HTTP_POST_VARS['cause8']='';
	$HTTP_POST_VARS['cause9']='';
	$HTTP_POST_VARS['interval1']='';
	$HTTP_POST_VARS['interval2']='';
	$HTTP_POST_VARS['interval3']='';
*/
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

	# Serializing the  Causes of Death information
if(isset($_POST['mode'])){
		$deathCause = array();
		$deathInterval = array();

		for($i=0;$i<10;$i++){
			$cx="cause".$i;
			$ix="interval".$i;

			#echo "cx = '".$cx."'; $ $ cx = '".$$cx."'; HTTP_POST_VARS['cx'] = '".$HTTP_POST_VARS[$cx]."'; <br> \n";
			#echo "ix = '".$ix."'; $ $ ix = '".$$ix."'; HTTP_POST_VARS['ix'] = '".$HTTP_POST_VARS[$ix]."'; <br> \n";
			
			if(!empty($HTTP_POST_VARS[$cx])) $deathCause[$cx]=$HTTP_POST_VARS[$cx];
			if(!empty($HTTP_POST_VARS[$ix])) $deathCause[$ix]=$HTTP_POST_VARS[$ix];

			$HTTP_POST_VARS['death_cause']='';
				// Serialize the data
			$HTTP_POST_VARS['death_cause']=serialize($deathCause);
		}
#echo "HTTP_POST_VARS['death_cause'] = '".$HTTP_POST_VARS['death_cause']."' <br> \n";
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
#echo "HTTP_POST_VARS : <br>"; print_r($HTTP_POST_VARS); echo "<br> \n";
#exit();

#$errorMsg = '';
#echo "mode = ".$_POST['mode'];
if(isset($_POST['mode'])){
	switch($_POST['mode']){
		case 'save':
			if (trim($HTTP_POST_VARS['death_date'])!=""){
				$HTTP_POST_VARS['death_date'] = @formatDate2STD($HTTP_POST_VARS['death_date'],$date_format);
			}
			if (trim($HTTP_POST_VARS['attended_from_date'])!=""){
				$HTTP_POST_VARS['attended_from_date'] = @formatDate2STD($HTTP_POST_VARS['attended_from_date'],$date_format);
			}
			if (trim($HTTP_POST_VARS['attended_to_date'])!=""){
				$HTTP_POST_VARS['attended_to_date'] = @formatDate2STD($HTTP_POST_VARS['attended_to_date'],$date_format);
			}
			if (trim($HTTP_POST_VARS['attendant_date_sign'])!=""){
				$HTTP_POST_VARS['attendant_date_sign'] = @formatDate2STD($HTTP_POST_VARS['attendant_date_sign'],$date_format);
			}
			if (trim($HTTP_POST_VARS['informant_date_sign'])!=""){
				$HTTP_POST_VARS['informant_date_sign'] = @formatDate2STD($HTTP_POST_VARS['informant_date_sign'],$date_format);
			}
			if (trim($HTTP_POST_VARS['encoder_date_sign'])!=""){
				$HTTP_POST_VARS['encoder_date_sign'] = @formatDate2STD($HTTP_POST_VARS['encoder_date_sign'],$date_format);
			}			
			$HTTP_POST_VARS['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
			$HTTP_POST_VARS['create_id'] = $HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_time'] = date('Y-m-d H:i:s');
#echo "save HTTP_POST_VARS : <br>"; print_r($HTTP_POST_VARS); echo "<br> \n";
			//saving data of death info

			if($obj_deathCert->saveDeathCertificateInfoFromArray($HTTP_POST_VARS)){
				#$errMsg='<font style="color:#FF0000;font-style:italic">'."Saved successfully!".'</font>';
				#added by VAN
				#save the death info to care_person table
				$deathinfo['death_date'] = $HTTP_POST_VARS['death_date'];
				$deathinfo['death_encounter_nr'] = $encounter_nr;
				$deathinfo['death_cause'] = $HTTP_POST_VARS['death_cause'];
				$deathinfo['death_cause_code'] = '';
				$deathinfo['history'] = $obj_deathCert->ConcatHistory("Update : Death Reg. ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
				$deathinfo['modify_id'] = $HTTP_SESSION_VARS['sess_user_name'];
				$deathinfo['modify_time'] = date('Y-m-d H:i:s');
				$person_obj->setDeathInfo($pid, $deathinfo);
				
				echo " \n <script type=\"text/javascript\">alert(\"Saved sucessfully!\")</script>";
			}else{
				#$errorMsg='<font style="color:#FF0000">'.$obj_deathCert->getErrorMsg().'</font>';
				echo '<em class="warn"><font style="color:#FF0000"><b>'.$db->ErrorMsg().'<b></font></em>';
			}
			
		break;	
		case 'update':
			if (trim($HTTP_POST_VARS['death_date'])!=""){
				$HTTP_POST_VARS['death_date'] = @formatDate2STD($HTTP_POST_VARS['death_date'],$date_format);
			}
			if (trim($HTTP_POST_VARS['attended_from_date'])!=""){
				$HTTP_POST_VARS['attended_from_date'] = @formatDate2STD($HTTP_POST_VARS['attended_from_date'],$date_format);
			}
			if (trim($HTTP_POST_VARS['attended_to_date'])!=""){
				$HTTP_POST_VARS['attended_to_date'] = @formatDate2STD($HTTP_POST_VARS['attended_to_date'],$date_format);
			}
			if (trim($HTTP_POST_VARS['attendant_date_sign'])!=""){
				$HTTP_POST_VARS['attendant_date_sign'] = @formatDate2STD($HTTP_POST_VARS['attendant_date_sign'],$date_format);
			}
			if (trim($HTTP_POST_VARS['informant_date_sign'])!=""){
				$HTTP_POST_VARS['informant_date_sign'] = @formatDate2STD($HTTP_POST_VARS['informant_date_sign'],$date_format);
			}
			if (trim($HTTP_POST_VARS['encoder_date_sign'])!=""){
				$HTTP_POST_VARS['encoder_date_sign'] = @formatDate2STD($HTTP_POST_VARS['encoder_date_sign'],$date_format);
			}			
			$HTTP_POST_VARS['history'] = $obj_deathCert->ConcatHistory("Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
			$HTTP_POST_VARS['modify_id'] = $HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['modify_time'] = date('Y-m-d H:i:s');
			
			if($obj_deathCert->updateDeathCertificateInfoFromArray($HTTP_POST_VARS)){
				#$errMsg='<font style="color:#FF0000;font-style:italic">'."Updated successfully!".'</font>';
				#save the death info to care_person table
				$deathinfo['death_date'] = $HTTP_POST_VARS['death_date'];
				$deathinfo['death_encounter_nr'] = $encounter_nr;
				$deathinfo['death_cause'] = $HTTP_POST_VARS['death_cause'];
				$deathinfo['death_cause_code'] = '';
				$deathinfo['history'] = $obj_deathCert->ConcatHistory("Update : Death Reg. ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
				$deathinfo['modify_id'] = $HTTP_SESSION_VARS['sess_user_name'];
				$deathinfo['modify_time'] = date('Y-m-d H:i:s');
				$person_obj->setDeathInfo($pid, $deathinfo);
				
				echo " \n <script type=\"text/javascript\">alert(\"Updated sucessfully!\")</script>";
			}else{
				#$errorMsg='<font style="color:#FF0000">'.$obj_deathCert->getErrorMsg().'</font>';
				echo '<em class="warn"><font style="color:#FF0000"><b>'.$db->ErrorMsg().'<b></font></em>';
			}			 
		break;
	} //end of switch statement
}


#notes
# code for retrieving death certificate information
$deathCertInfo = $obj_deathCert->getDeathCertRecord($pid);
if($deathCertInfo){
#echo "deathCertInfo : <br>"; print_r($deathCertInfo); echo "<br> \n";
	extract($deathCertInfo);
	$delivery_method_tmp= substr(trim($deathCertInfo['delivery_method']),0,1);
	$delivery_method_info = substr(trim($deathCertInfo['delivery_method']),4);
#echo "deathCertInfo['delivery_method'] = '".$deathCertInfo['delivery_method']."' <br> \n";
#echo "delivery_method_tmp = '".$delivery_method_tmp."' <br> \n";
#echo "delivery_method_info = '".$delivery_method_info."' <br> \n";
	$death_manner_tmp = substr(trim($deathCertInfo['death_manner']),0,1);
	$death_manner_info = substr(trim($deathCertInfo['death_manner']),4);
#echo "deathCertInfo['death_manner'] = '".$deathCertInfo['death_manner']."' <br> \n";
#echo "death_manner_tmp = '".$death_manner_tmp."' <br> \n";
#echo "death_manner_info = '".$death_manner_info."' <br> \n";
	$attendant_type = substr(trim($deathCertInfo['attendant_type']),0,1);
	$attendant_type_others = substr(trim($deathCertInfo['attendant_type']),4);
#echo "deathCertInfo['attendant_type'] = '".$deathCertInfo['attendant_type']."' <br> \n";
#echo "attendant_type = '".$attendant_type."' <br> \n";
#echo "attendant_type_others = '".$attendant_type_others."' <br> \n";
	$tmp_death_cause = unserialize($deathCertInfo['death_cause']);
#echo "tmp_death_cause  = '".$tmp_death_cause."' <br> \n";
#echo "tmp_death_cause : <br> \n"; print_r($tmp_death_cause); echo"<br> \n";
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
#edited by VAN 02-16-08	
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

<body>
<SCRIPT LANGUAGE="JavaScript">

//function expandcontract(tbodyid,dis) {
//  document.getElementById(tbodyid).style.display = dis;
//}
</SCRIPT>
<?php echo $errorMsg; ?>
<form id="death_certificate" name="death_certificate" method="post" onsubmit="return chckDeathForm(this);">
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F9FFFB">
  <tr>
    <td bgcolor="#F9FFFB">
        <div align="left"><span class="style2">Republic of the Philippines</span><BR /> 
            <span class="style2">OFFICE OF THE CIVIL REGISTRAR GENERAL</span><BR /> 
            <span class="style11">CERTIFICATE OF DEATH </span><br />
            <span class="style13">Gonzales Maranan Medical Center Incorporated        </span>          </div>
    </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="9%" bgcolor="#F9FFFB"> <span class="style2">Province:</span> </td>
          <td width="63%" bgcolor="#F9FFFB">
            <input name="province" type="hidden" class="style2" id="province" value="Davao del Sur" />
            <span class="style13">Davao del Sur</span> </td>
          <td width="28%" rowspan="2" align="right" bgcolor="#F9FFFB"> <span class="style15">Registry no</span><span class="style2">. </span>
              <input name="registry_nr" id="registry_nr" type="text" class="style2" value="<?= $registry_nr ?>" onblur="trimString(this)" />
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB"> <span class="style2">City/Municipality</span> </td>
          <td bgcolor="#F9FFFB">
            <input name="muncity" type="hidden" class="style2" id="muncity" value="Davao City" />
            <span class="style13">Digos City</span> </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="5%" bgcolor="#A0C2DA"><img src="images/brdr_blgry_03.gif" width="31" height="26" /></td>
          <td width="62%" bgcolor="#A0C2DA"><span class="style9">Name </span></td>
          <td width="33%" bgcolor="#A0C2DA">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="3" bgcolor="#A0C2DA">
            <table width="100%" border="0" cellspacing="1" cellpadding="0">
              <tr>
                <td align="center" bgcolor="#FFFFFF"><br />
                  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                      <td bgcolor="#D5E3EE">
                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1" class="style2">
                          <tr>
                            <td bgcolor="#D5E3EE" class="style17">Given Name </td>
                            <td bgcolor="#D5E3EE" class="style17">Middle Name </td>
                            <td bgcolor="#D5E3EE" class="style17">Family Name </td>
                          </tr>
                          <tr>
                            <td width="20%" bgcolor="#F9FFFB" class="style17">
                              <center>
                                <table width="90%" border="0" cellspacing="2" cellpadding="0">
                                    <tr>
                                      <td>
												  	<!--
													<span class="style13">
                                        <input name="name_first" type="hidden" class="style13" id="name_first" value="<?= ucwords(strtolower($name_first)) ?>" />
                                        <b>
                                        <?= ucwords(strtolower($name_first)) ?>
                                        </b></span>
													 -->
													 <input type="text" name="name_first" id="name_first" size="30" value="<?= ucwords(strtolower($name_first)) ?>" />
													</td>
                                    </tr>
                                  </table>
                              </center>
                              </td>
                            <td width="26%" bgcolor="#F9FFFB" class="style17">
                              <center>
                                <table width="90%" border="0" cellspacing="2" cellpadding="0">
                                    <tr>
                                      <td>
												  	<!--
													<span class="style13">
                                        <input name="name_middle" type="hidden" class="style2" id="name_middle" value="<?= ucwords(strtolower($name_middle)) ?>" />
                                        <b>
                                        <?= ucwords(strtolower($name_middle)) ?>
                                        </b></span>
													-->
													<input type="text" name="name_middle" id="name_middle" size="30" value="<?= ucwords(strtolower($name_middle)) ?>" /> 
													</td>
                                    </tr>
                                  </table>
                              </center>
                              </td>
                            <td width="29%" bgcolor="#F9FFFB" class="style17">
                              <center>
                                <table width="90%" border="0" cellspacing="2" cellpadding="0">
                                    <tr>
                                      <td>
												  	<!--
													<span class="style13">
                                        <input name="name_last" type="hidden" class="style2" id="name_last" value="<?= ucwords(strtolower($name_last)) ?>" />
                                        <b>
                                        <?= ucwords(strtolower($name_last)) ?>
                                        </b></span>
													 -->
													 <input type="text" name="name_last" id="name_last" size="30" value="<?= ucwords(strtolower($name_last)) ?>" /> 
													 </td>
                                    </tr>
                                  </table>
                              </center>
                              </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
				  <br />
				  <table width="90%" border="0" cellspacing="2" cellpadding="0">
                    <tr>
                      <td bgcolor="#D5E3EE">
                        <table width="100%" border="0" cellpadding="1" cellspacing="1">
                          <tr>
                            <td width="14%" valign="top" bgcolor="#F9FFFB">
                              <table width="100%" border="0" cellpadding="1" cellspacing="1">
                                <tr>
                                  <td colspan="2" bgcolor="#D5E3EE" class="style5 style19"> SEX </td>
                                </tr>
                                <tr>
                                  <td width="37%" bgcolor="#F9FFFB" class="style17">
                                    <input type="hidden" name="sex" id="sex" value="<?= $sex ?>" />
                                    <input name="sex" id="sex" type="radio" value="radiobutton" <?php if ($sex == 'm') echo "checked"; ?> />
                                    Male </td>
                                  <td width="63%" bgcolor="#F9FFFB" class="style17">
                                    <input name="sex" id="sex" type="radio" value="radiobutton" <?php if ($sex =='f') echo "checked"; ?> />
                                    Female </td>
                                </tr>
                              </table>
                            </td>
                            <td width="20%" valign="top" bgcolor="#F9FFFB">
                              <table width="100%" border="0" cellpadding="2" cellspacing="0">
                                <tr>
                                  <td bgcolor="#D5E3EE"> <span class="style21">RELIGION</span></td>
                                </tr>
                                <tr>
                                  <td bgcolor="#F9FFFB">
                                    <select name="d_religion" id="d_religion" >
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
                              </table>
                            </td>
                            <td width="13%" valign="top" bgcolor="#F9FFFB">
                              <table width="100%" border="0" cellpadding="2" cellspacing="0">
                                <tr>
                                  <td bgcolor="#D5E3EE"> <span class="style21">AGE</span> </td>
                                </tr>
                              </table>
                            </td>
                            <td width="53%" valign="top" bgcolor="#F9FFFB"><span class="style17">
                              <?php
	$date_birth_tmp = @formatDate2Local($date_birth,$date_format);
#	$death_date = "1977-06-21";
#	$age_at_death = "22:23:24";
	if (($death_date!='0000-00-00')  && ($death_date!=""))
		$death_date_tmp = @formatDate2Local($death_date,$date_format);
	else
		$death_date_tmp='';
#echo " date_birth_tmp ='".$date_birth_tmp."' <br>\n death_date_tmp = '".$death_date_tmp."' <br>\n";
	$ageYear = $person_obj->getAge($date_birth_tmp,'',$death_date_tmp);
	if (is_numeric($ageYear) && ($ageYear>=0)){
#		echo "true :  ageYear ='".$ageYear."' <br>\n";
		if ($ageYear<1){
			$ageMonth = intval($ageYear*12);
			$ageDay = (($ageYear*12)-$ageMonth) * 30;
#echo " ageMonth ='".$ageMonth."' <br>\n ageDay = '".$ageDay."' <br>\n round(ageDay) = '".round($ageDay)."' <br>\n";

			if(($ageMonth == 0) && (round($ageDay)<1)){
				# under 1 day
				if ($age_at_death)
					list($ageHours,$ageMinutes,$ageSec) = explode(":",$age_at_death);
				$ageMonth = ''; # set age in months as empty
				$ageDay = ''; # set age in days as empty
#echo "under 1 day :  ageHours ='".$ageHours."' <br>\n ageMinutes = '".$ageMinutes."' <br>\n ageSec = '".$ageSec."' <br>\n";
			}else{
				# under 1 year but above 1 day
				$ageMonth = intval($ageYear*12);
				$ageDay = round((($ageYear*12)-$ageMonth) * 30);	
#echo "under 1 year but above 1 day : ageMonth ='".$ageMonth."' <br>\n ageDay = '".$ageDay."' <br>\n";
			}
			$ageYear = ''; # set age in years as empty
		}else{
			# above 1 year
			$ageYear = number_format($ageYear, 2);
#echo "above 1 year :  ageYear ='".$ageYear."' <br>\n";
		}
	}else{
#		echo "false :  ageYear ='".$ageYear."' <br>\n";
	}
?></span>
                              <table width="100%" border="0" cellspacing="2" cellpadding="0">
                                <tr>
                                  <td bgcolor="#D5E3EE">
                                    <table width="100%" border="0" cellpadding="1" cellspacing="1">
                                      <tr>
                                        <td width="30%" bgcolor="#D5E3EE" class="style2">a. 1 year or above </td>
                                        <td width="35%" bgcolor="#D5E3EE" class="style2">b. under 1 year </td>
                                        <td width="35%" bgcolor="#D5E3EE" class="style2">c. under 1 day </td>
                                        <!--
                <td width="174" bgcolor="#FFEFB0">a. 1 year or above </td>
                <td width="240" bgcolor="#FFEFB0">b. under 1 year </td>
                <td width="197" bgcolor="#FFEFB0">c. under 1 day </td>
-->
                                      </tr>
                                      <tr>
                                        <td align="center" valign="top" bgcolor="#F9FFFB" class="style2"> Completed Years <br />
                                            <input name="death_age" type="text" class="style2" id="death_age" maxlength="5" size="5" readonly="1" value="<?php if($death_date_tmp!='') echo $ageYear ?>" />
                                        </td>
                                        <td bgcolor="#F9FFFB" class="style2">
                                          <table width="100%" border="0" cellpadding="2" cellspacing="2">
                                            <tr>
                                              <td width="114" align="center" valign="top" class="style2"> Months <br />
                                                  <input name="death_months" type="text" class="style2" id="death_months" size="5" readonly="1" value="<?= $ageMonth ?>" />
                                              </td>
                                              <td width="84" align="center" valign="top" class="style2"> Days <br />
                                                  <input name="death_days" type="text" class="style2" id="death_days" size="5" readonly="1" value="<?= $ageDay ?>" />
                                              </td>
                                            </tr>
                                          </table>
                                        </td>
                                        <td bgcolor="#F9FFFB" class="style2">
                                          <table width="100%" border="0" cellpadding="2" cellspacing="2">
                                            <tr>
                                              <td align="center" valign="top" class="style2"> Hrs <br />
                                                  <input name="hours" type="text" class="style2" id="hours" size="3" onblur="trimString(this); checkTime(this);" <?php if ($ageHours){  echo 'value="'.$ageHours.'"'; }else{ echo "disabled"; } ?> />
                                              </td>
                                              <td align="center" valign="top" class="style2"> Min <br />
                                                  <input name="minutes" type="text" class="style2" id="minutes" size="3" onblur="trimString(this); checkTime(this);" <?php if ($ageMinutes){  echo 'value="'.$ageMinutes.'"'; }else{ echo "disabled"; }?> />
                                              </td>
                                              <td align="center" valign="top" class="style2"> Sec <br />
                                                  <input name="sec" type="text" class="style2" id="sec" size="3" onblur="trimString(this); checkTime(this);" <?php if ($ageSec){  echo 'value="'.$ageSec.'"'; }else{ echo "disabled"; }?> />
                                              </td>
                                            </tr>
                                          </table>
                                        </td>
                                      </tr>
                                    </table>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
				  <br />
                  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="2">
                    <tr>
                      <td bgcolor="#D5E3EE">
                        <table width="100%" border="0" cellpadding="2" cellspacing="1">
                          <tr>
                            <td colspan="3" bgcolor="#D5E3EE"><b class="style21">PLACE OF DEATH </b></td>
                          </tr>
                          <tr>
                            <td width="34%" valign="top" bgcolor="#F9FFFB" class="style17"> Street/Barangay <br />
                                <input name="death_place_basic" id="death_place_basic" type="hidden" value="Gonzales Maranan Medical Center Incorporated" />
                                <b class="style13">Gonzales Maranan Medical Center Incorporated </b> </td>
                            <td width="29%" valign="top" bgcolor="#F9FFFB" class="style17">City/Municipality <br />
                                <input name="death_place_mun" id="death_place_mun" type="hidden" value="Davao City" />
                                <b class="style13">Davao City </b> </td>
                            <td width="37%" valign="top" bgcolor="#F9FFFB" class="style17"> Province <br />
                                <input name="death_place_prov" id="death_place_prov" type="hidden" value="Davao del Sur" />
                                <span class="style13"><b>Davao del Sur</b> </span></td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
				  <br />
                  <table width="90%" border="0" cellpadding="0" cellspacing="1">
                    <tr>
                      <td width="55%" bgcolor="#D5E3EE">
                        <table width="100%" border="0" cellspacing="1" cellpadding="0">
                          <tr>
                            <td bgcolor="#F9FFFB">
                              <table width="100%" border="0" cellpadding="2" cellspacing="1">
                                <tr>
                                  <td colspan="3" bgcolor="#D5E3EE" class="style21">DATE OF DEATH </td>
                                </tr>
                                <tr>
                                  <input type="hidden" name="date_birth" id="date_birth" value ="<?= @formatDate2Local($date_birth, $date_format)?>"/>
                                  <td class="style17">
                                    <?php
						$phpfd=$date_format;
						$phpfd=str_replace("dd", "%d", strtolower($phpfd));
						$phpfd=str_replace("mm", "%m", strtolower($phpfd));
						$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
				
						if (($death_date!='0000-00-00')  && ($death_date!=""))
							$death_date = @formatDate2Local($death_date,$date_format);
						else
							$death_date='';
					
						$sDeathDate= '<input name="death_date" type="text" size="15" maxlength=10 value="'.$death_date.'"'. 
									'onFocus="this.select();"  
									id = "death_date" 
									onBlur="getAge(this); IsValidDate(this,\'MM/dd/yyyy\'); "
									onChange="getAge(this); IsValidDate(this,\'MM/dd/yyyy\'); "
									onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
									<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="death_date_trigger" style="cursor:pointer" >
									<font size=3>['; 			
						ob_start();
					?>
                                    <script type="text/javascript">
						Calendar.setup ({
								inputField : "death_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "death_date_trigger", singleClick : true, step : 1
						});
					          </script>
                                    <?php
						$calendarSetup = ob_get_contents();
						ob_end_clean();
				
						$sDeathDate .= $calendarSetup;
						/**/
						$dfbuffer="LD_".strtr($date_format,".-/","phs");
						$sDeathDate = $sDeathDate.$$dfbuffer.']';
					?>
                                  </td>
                                  <td>
                                    <?= $sDeathDate ?>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </td>
                      <td width="45%" valign="top" bgcolor="#D5E3EE">
                        <table width="100%" border="0" cellspacing="1" cellpadding="0">
                          <tr>
                            <td bgcolor="#F9FFFB">
                              <table width="100%" border="0" cellpadding="2" cellspacing="1">
                                <tr>
                                  <td bgcolor="#D5E3EE"> <span class="style21">CITIZENSHIP</span> </td>
                                </tr>
                                <tr>
                                  <td bgcolor="#F9FFFB">
                                    <select name="d_citizenship" id="d_citizenship">
                                      <?php
                  		$country_obj = $address_country->getAllAddress();
                  		while($result = $country_obj->FetchRow()){
                  			$selected ='';
                  			if($citizenship==$result['country_code'])
                  				$selected = 'selected';
                  			echo '<option value="'.$result['country_code'].'" '.$selected.'>'.$result['country_name']."</option> \n";	
                  		}
                  ?>
                                    </select>
                                    <!-- <input name="citizenship" id="citizenship" type="hidden" value="<?= ucwords(strtolower($citizenship))?>" />
      			        -->
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
				  <br />
                  <table width="90%" border="0" cellspacing="2" cellpadding="0">
                    <tr>
                      <td bgcolor="#D5E3EE">
                        <table width="100%" border="0" cellspacing="1" cellpadding="0">
                          <tr>
                            <td bgcolor="#F9FFFB">
                              <table width="100%" border="0" cellpadding="0" cellspacing="1">
                                <tr>
                                  <td colspan="3" bgcolor="#D5E3EE" class="style21">RESIDENCE</td>
                                </tr>
                                <tr>
                                  <td width="32%" height="30" valign="top" bgcolor="#F9FFFB" class="style2"> Street/Barangay: <br />
                                      <b>
                                      <?= ucwords(strtolower($street_name.", ".$brgy_row['brgy_name']))?>
                                      </b>
                                      <input name="residence_basic" id="residence_basic" type="hidden" class="style2" value="<?= $brgy_row['brgy_nr'] ?>"  />
                                  </td>
                                  <td width="42%" valign="top" bgcolor="#F9FFFB" class="style2">City/Municipality <br />
                                      <b>
                                      <?= ucwords(strtolower($brgy_row['mun_name'])) ?>
                                      </b>
                                      <input name="residence_mun" type="hidden" class="style2" id="residence_mun" value="<?= $brgy_row['mun_nr'] ?>" />
                                  </td>
                                  <td width="26%" valign="top" bgcolor="#F9FFFB" class="style2"> Province: <br />
                                      <b>
                                      <?= ucwords(strtolower($brgy_row['prov_name'])) ?>
                                      </b>
                                      <input name="residence_prov" type="hidden" class="style2" id="residence_prov" value="<?= $brgy_row['prov_nr'] ?>" />
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
				  <br />
                  <table width="90%" border="0" cellspacing="2" cellpadding="0">
                    <tr>
                      <td bgcolor="#D5E3EE">
                        <table width="100%" border="0" cellpadding="1" cellspacing="1">
                          <tr>
                            <td width="48%" height="76" valign="top" bgcolor="#F9FFFB">
                              <table width="100%" border="0" cellpadding="2" cellspacing="1">
                                <tr>
                                  <td colspan="3" bgcolor="#D5E3EE" class="style21">CIVIIL STATUS </td>
                                  <input name="civil_status" id="civil_status" type="hidden" value ="<?=$civil_status?>"/>
                                </tr>
                                <tr>
                                  <td bgcolor="#F8FAFC" class="style17">
                                    <input name="decease_civilstatus" type="radio" value="radiobutton" disabled <?php if($civil_status == 'single') echo "checked"; ?> />
                                    Single </td>
                                  <td bgcolor="#F8FAFC" class="style17">
                                    <input name="decease_civilstatus" type="radio" value="radiobutton" disabled <?php if($civil_status == 'widowed') echo "checked"; ?> />
                                    Widowed </td>
                                  <td bgcolor="#F8FAFC" class="style17">
                                    <input name="decease_civilstatus" type="radio" value="radiobutton" disabled <?php if($civil_status == 'unknown') echo "checked"; ?> />
                                    Unknown </td>
                                </tr>
                                <tr>
                                  <td bgcolor="#F8FAFC" class="style17">
                                    <input name="decease_civilstatus" type="radio" value="radiobutton" disabled <?php if($civil_status == 'married') echo "checked"; ?>/>
                                    Married </td>
                                  <td bgcolor="#F8FAFC" class="style17">
                                    <input name="decease_civilstatus" type="radio" value="radiobutton" disabled <?php if($civil_status == 'others') echo "checked"; ?> />
                                    Others </td>
                                  <td bgcolor="#F8FAFC" class="style17">&nbsp;</td>
                                </tr>
                              </table>
                            </td>
                            <td width="52%" valign="top" bgcolor="#F9FFFB">
                              <table width="100%" border="0" cellpadding="2" cellspacing="1">
                                <tr>
                                  <td bgcolor="#D5E3EE"> <span class="style21">OCCUPATION</span></td>
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
                              </table>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
				  <br />
                </td>
              </tr>
            </table>
          </td>
          </tr>
      </table>
      </td>
  </tr>
</table>

<!-- FOR AGES 0 TO 7 DAYS  -->
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="1">
  <thead>
  <tr>
    <td width="12%" style="cursor: pointer; cursor: hand;">
	 	  <img src="images/ok.gif" onclick="expandcontract('Row1','')">
	     <img src="images/x.gif" onclick="expandcontract('Row1','none')">	 </td>
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
					<td width="33%" valign="top" bgcolor="#F9FFFB">
						<table width="100%" border="0" cellpadding="2" cellspacing="0">
							<tr bgcolor="#CCCCFF">
								<td colspan="3" bgcolor="#D5E3EE"> <span class="style5">DATE OF BIRTH</span> </td>
							</tr>
							<tr bgcolor="#EAEAFF">
								<td align="center" bgcolor="#FFFFFF" class="style2"> (day)  </td>
								<td align="center" bgcolor="#FFFFFF" class="style2"> (month)  </td>
								<td align="center" bgcolor="#FFFFFF" class="style2"> (year)  </td>
							</tr>
							<tr bgcolor="#EAEAFF">
								<td align="center" bgcolor="#FFFFFF" class="style2">
									
<?php
	if ($birthDay<10)
		$birthDay = "0".$birthDay;
	echo "									$birthDay";
?>
								</td>
								<td align="center" bgcolor="#FFFFFF" class="style2">
<?php
#	if ($birthMonth<10)
#		$birthMonth = "0".$birthMonth;
#	echo "									$birthMonth";
	$arrayMonth = array ("","January","February","March","April","May","June","July","August","September","October","November","December");
	echo "									".$arrayMonth[$birthMonth]; 
?>
								</td>
								<td align="center" bgcolor="#FFFFFF" class="style2">
								<?= $birthYear ?>
								</td>
							</tr>
					  </table>
				</td>
					<td width="25%" bgcolor="#F9FFFB">
						<table width="100%" border="0" cellpadding="2" cellspacing="0">
							<tr bgcolor="#CCCCFF">
								<td bgcolor="#D5E3EE"> <span class="style5">12. AGE OF THE MOTHER</span> </td>
							</tr>
							<tr bgcolor="#EAEAFF">
								<td align="center" bgcolor="#FFFFFF">&nbsp; 	</td>
							</tr>
							<tr bgcolor="#EAEAFF">
								<td align="center" bgcolor="#FFFFFF"> 
					            <input name="m_age" id="m_age" type="text" size="5" onblur="trimString(this); chkInteger(this,countingNumber);" value="<?= $m_age ?>">
								</td>
							</tr>
						</table>
					</td>
					<td width="*" bgcolor="#F9FFFB">
						<table width="100%" border="0" cellpadding="2" cellspacing="0">
							<tr bgcolor="#CCCCFF">
								<td bgcolor="#D5E3EE"> <span class="style5">13. METHOD OF DELIVERY</span> </td>
							</tr>
							<tr bgcolor="#EAEAFF">
								<td bgcolor="#FFFFFF" class="style2"> 
									<input type="hidden" name="delivery_method"  id="delivery_method" value="<?php if ($delivery_method) echo $delivery_method; else echo "1"; ?>">
									<input name="delivery_method_tmp"  id="delivery_method_tmp" type="radio" value="1" onclick="methodOfDelivery(this);" <?php if ((!$delivery_method)||($delivery_method_tmp=="1")) echo "checked"; ?>>
                          Normal; spontaeneous vertex								</td>
							</tr>
							<tr bgcolor="#EAEAFF">
								<td bgcolor="#FFFFFF" class="style2"> 
		 						   <input name="delivery_method_tmp" id="delivery_method_tmp" type="radio" value="2" onclick="methodOfDelivery(this);" <?php if ($delivery_method_tmp=="2") echo "checked"; ?>>
									Others (Specify) &nbsp;
									<input type="text" name="delivery_method_info" id="delivery_method_info"  onblur="trimString(this); $('delivery_method').value= '2 - '+this.value;" value="<?php if ($delivery_method_tmp=="2") echo $delivery_method_info; ?>">
							  </td>
							</tr>
					  </table>
					</td>
			  </tr>
		  </table>
	 	</td>
  	 </tr>
	 <tr>
	 	<td colspan="2">
			<table width="100%" bgcolor="#A0C2DA" border="0" cellpadding="2" cellspacing="1">
				<tr bgcolor="#FFDDFF">
					<td bgcolor="#FFFFFF">
						<table width="100%" border="0" cellpadding="2" cellspacing="0">
							<tr>
								<td bgcolor="#D5E3EE" width="30%">					
									<span class="style5">14. LENGTH OF PREGNANCY :</span>								</td>
								<td bgcolor="#FFFFFF" class="style2">					
									<input name="pregnancy_length" type="text" id="pregnancy_length" onblur="trimString(this); chkInteger(this,countingNumber);" value="<?= $pregnancy_length ?>">
							  &nbsp; completed weeks								</td>
							</tr>
						</table>
				  </td>
				</tr>
		  </table>
		</td>
	 </tr>
  	 <tr>
	 	<td colspan="2">
			<table width="100%" bgcolor="#A0C2DA" border="0" cellpadding="2" cellspacing="1">
			  <tr>  
					<td width="40%" bgcolor="#FFFFFF">
						<table width="100%" border="0" cellpadding="2" cellspacing="0">
							<tr bgcolor="#CCCCFF">
								<td bgcolor="#D5E3EE"> <span class="style5">15. TYPE OF BIRTH</span> </td>
							</tr>
							<tr bgcolor="#EAEAFF">
								<td bgcolor="#FFFFFF"> 
									<input type="hidden" name="birth_type" id="birth_type" value="<?php if ($birth_type) echo $birth_type; else echo "1" ?>">
									&nbsp;<span class="style2">&nbsp;&nbsp;&nbsp;
									<input name="birth_type_tmp" id="birth_type_tmp" type="radio" value="1" onclick="typeOfBirth(this)" <?php if (($birth_type=='1')||(!$birth_type)) echo "checked"; ?>>
									Single
									<input name="birth_type_tmp" id="birth_type_tmp" type="radio" value="2" onclick="typeOfBirth(this)" <?php if ($birth_type=='2') echo "checked"; ?>>
									Twin
									<input name="birth_type_tmp" id="birth_type_tmp" type="radio" value="3" onclick="typeOfBirth(this)" <?php if ($birth_type=='3') echo "checked"; ?>>
							  Triplet, etc.								</span></td>
							</tr>
						</table>
				</td>
					<td width="*" bgcolor="#FFFFFF">
						<table width="100%" border="0" cellpadding="2" cellspacing="0">
							<tr bgcolor="#CCCCFF">
								<td bgcolor="#D5E3EE"> <span class="style5">16. IF MULTIPLE BIRTH, CHILD WAS </span> </td>
							</tr>
							<tr bgcolor="#EAEAFF">
								<td bgcolor="#FFFFFF"> 
									<input type="hidden" name="birth_rank" id="birth_rank" value="<?= $birth_rank ?>">
									&nbsp;&nbsp;&nbsp;<span class="style2">&nbsp;
									<input name="birth_rank_tmp" id="birth_rank_tmp" type="radio" onclick="rankOfBirth(this);" value="1" <?php if ($birth_rank=='1') echo "checked"; if (($birth_type==1)||(!$birth_type)) echo "disabled"; ?>>
									First
									<input name="birth_rank_tmp" id="birth_rank_tmp" type="radio" onclick="rankOfBirth(this)" value="2" <?php if ($birth_rank=='2') echo "checked"; if (($birth_type==1)||(!$birth_type)) echo "disabled"; ?>>
									Second
									<input name="birth_rank_tmp" id="birth_rank_tmp" type="radio" onclick="rankOfBirth(this)" value="" <?php if (intval($birth_rank) > 2) echo "checked"; if (($birth_type==1)||(!$birth_type)) echo "disabled"; ?>>
									Others (Specify) &nbsp;
									<input type="text" name="birth_rank_others" id="birth_rank_others" onblur="trimString(this); if(chkInteger(this,countingNumber)){ $('birth_rank').value=this.value; }" <?php if (intval($birth_rank) > 2) echo 'value="'.$birth_rank.'"'; else echo "disabled"; ?>>
							  </span>							  </td>
							</tr>
						</table>
				</td>
			  </tr>
		  </table>
	 	</td>
  	 </tr>
	 <tr>
			<!-- START FOR CAUSES OF DEATH FOR AGES 0 TO 7 DAYS -->		
	 	<td colspan="2">
			<table width="100%" bgcolor="#A0C2DA" border="0" cellpadding="2" cellspacing="1">
				<tr bgcolor="#FFDDFF">
					<td bgcolor="#FFFFFF">
						<table width="100%" border="0" cellpadding="2" cellspacing="0">
							<tr>
								<td colspan="2" bgcolor="#D5E3EE">					
									<span class="style5">17. CAUSES OF DEATH </span>								</td>
							</tr>
							<tr>
								<td width="33%" bgcolor="#FFFFFF" class="style2">			
							  a. Main disease/condition of infant &nbsp;</td>
							    <td width="67%" bgcolor="#FFFFFF" class="style2">
							      <input name="cause1" id="cause1" type="text" size="50" value="<?= $tmp_death_cause['cause1'] ?>" />
							    </td>
							</tr>
							<tr>
								<td bgcolor="#FFFFFF" class="style2">					
								  b. Other diseases/conditions of infant							  </td>
							    <td bgcolor="#FFFFFF" class="style2">
							      <input name="cause2" id="cause2" type="text" size="50" value="<?= $tmp_death_cause['cause2'] ?>" />
							    </td>
							</tr>
							<tr>
								<td bgcolor="#FFFFFF" class="style2">					
								  c. Main maternal disease/condition of affecting infant							  </td>
							    <td bgcolor="#FFFFFF" class="style2">
							      <input name="cause3" id="cause3" type="type" size="50" value="<?= $tmp_death_cause['cause3'] ?>" />
							    </td>
							</tr>
							<tr>
								<td bgcolor="#FFFFFF" class="style2">					
								  d. Other maternal disease/condition of affecting infant							  </td>
							    <td bgcolor="#FFFFFF" class="style2">
							      <input name="cause4" id="cause4" type="text" size="50" value="<?= $tmp_death_cause['cause4'] ?>" />
							    </td>
							</tr>
							<tr>
								<td bgcolor="#FFFFFF" class="style2">					
								  e. Other relevant circumstances							  </td>
							    <td bgcolor="#FFFFFF" class="style2">
							      <input name="cause5" id="cause5" type="text" size="50" value="<?= $tmp_death_cause['cause5'] ?>" />
							    </td>
							</tr>
					  </table>
				  </td>
				</tr>
		  </table>
		</td>
			<!-- END FOR CAUSES OF DEATH FOR AGES 0 TO 7 DAYS -->		
	 </tr>
	 	            <!-- END FOR AGES 0 TO 7 DAYS -->
  </tbody>
</table>
<!-- END - FOR AGES 0 TO 7 DAYS  -->

<!-- CAUSES OF DEATH //ABOVE 7 DAYS -->
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="2" bgcolor="#A0C2DA" id="Row2" <?= $Row2Style ?>>
  <tr>
    <td height="120">
      <table width="100%" border="0" cellpadding="2" cellspacing="1">
        <tr>
          <td colspan="3" bgcolor="#D5E3EE" class="style5">17. Causes of Death </td>
        </tr>
        <tr>
          <td width="25%" bgcolor="#FFFFFF" class="style2">
            
            I.
            Immediate cause: a.          </td>
          <td width="26%" bgcolor="#FFFFFF" class="style2">
            <input name="cause6" id="cause6" type="text" value ="<?= $tmp_death_cause['cause6'] ?>" />
          </td>
          <td width="49%" valign="top" bgcolor="#FFFFFF" class="style2">Interval Between Onset and Death<br />
            <input name="interval1" id="interval1" type="text" value="<?= $tmp_death_cause['interval1'] ?>">
		  </td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF" class="style2">&nbsp;&nbsp;&nbsp; 
            
            Antecendent cause : b.          </td>
          <td bgcolor="#FFFFFF" class="style2">
            <input name="cause7" id="cause7" type="text" value="<?= $tmp_death_cause['cause7'] ?>" />
          </td>
          <td width="49%" vlign="top" bgcolor="#FFFFFF" class="style2">
          	<input name="interval2" id="interval2" type="text" value="<?= $tmp_death_cause['interval2'] ?>">
          </td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF" class="style2">&nbsp;&nbsp;&nbsp;
            
            Underlying cause: c.          </td>
          <td bgcolor="#FFFFFF" class="style2">
            <input name="cause8" id="cause8" type="text" value="<?= $tmp_death_cause['cause8'] ?>" />
          </td>
          <td width="49%" vlign="top" bgcolor="#FFFFFF" class="style2">
          	<input name="interval3" id="interval3" type="text" value="<?= $tmp_death_cause['interval3'] ?>">
          </td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td class="style2">II. 
            
            Other significant conditions contributing to death          </td>
          <td class="style2">
            <input name="cause9" id="cause9" type="text" value="<?= $tmp_death_cause['cause9'] ?>" />
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<!--ENDS - CAUSES OF DEATH //ABOVE 7 DAYS -->

<!-- DEATH BY NON-NATURAL CAUSES -->
<table width="90%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#A0C2DA">
  <tr>
    <td bgcolor="#D5E3EE" class="style5">18. Death by non-natural causes </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">a. Manner of Death </td>
  </tr>
  <tr>
    <td bgcolor="#D5E3EE" class="style2">
      <table width="100%" border="0" cellpadding="1" cellspacing="0">
        <tr>
          <td width="20%" bgcolor="#F9FFFB" class="style2">
            
            <input name="death_manner" id="death_manner" type="hidden" value="<?php if($death_manner) echo $death_manner; else echo "1"; ?>">
            <input name="death_manner_tmp" id="death_manner_tmp" type="radio" onclick="mannerOfDeath(this);" value="1" <?php if($death_manner_tmp=='1') echo "checked"; ?>>
            1. Homicide
          </td>
          <td width="20%" bgcolor="#F9FFFB" class="style2">
            
            <input name="death_manner_tmp" id="death_manner_tmp" type="radio" onclick="mannerOfDeath(this);" value="2" <?php if($death_manner_tmp=='2') echo "checked"; ?>>
            2. Suicide
          </td>
          <td width="20%" bgcolor="#F9FFFB" class="style2">
            
            <input name="death_manner_tmp" id="death_manner_tmp" type="radio" onclick="mannerOfDeath(this);" value="3" <?php if($death_manner_tmp=='3') echo "checked"; ?>>
            3. Accident
          </td>
          <td width="*" bgcolor="#F9FFFB" class="style2">
            <input name="death_manner_tmp" id="death_manner_tmp" type="radio" onclick="mannerOfDeath(this);" value="4" <?php if($death_manner_tmp=='4') echo "checked"; ?>>
            Others (Specify) &nbsp;
            <input name="death_manner_info" type="text" size="40" id="death_manner_info" onblur="trimString(this); $('death_manner').value='4 - '+this.value;" <?php if($death_manner_tmp=='4') echo 'value="'.$death_manner_info.'"'; else echo "disabled" ?> >
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      b. Place of Occurence (e.g. home, farm, factory, street, sea, etc.)
      <input name="place_occurence" type="text" size="40" class="style2" id="place_occurence" onblur="trimString(this);" value="<?= $place_occurence ?>" />
      
    </td>
  </tr>
</table><br />
<!--ENDS -//DEATH BY NON-NATURAL CAUSES -->

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
    <td colspan="2" bgcolor="#D5E3EE" class="style5">19. Attendant </td>
    <td rowspan="4" bgcolor="#F9FFFB" class="style5" id="if_attendant" <?= $if_attendantStyle ?>> 
      <table width="100%" border="0" cellpadding="2" cellspacing="2">
        <tr>
          <td colspan="2" bgcolor="#D5E3EE" class="style5">If attended, state duration : </td>
        </tr>
        <tr>
          <td width="8%" bgcolor="#F9FFFB">
            From            
          </td>
          <td width="92%" bgcolor="#F9FFFB">
<!--
            <input name="attended_from_date" type="text" class="style2" id="attended_from_date">
-->

<?php
		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
		//$phpfd=str_replace("yy","%Y", strtolower($phpfd));
	
	if (($attended_from_date!='0000-00-00')  && ($attended_from_date!=""))
		$attended_from_date = @formatDate2Local($attended_from_date,$date_format);
	else
		$attended_from_date='';

	$sDateJS= '<input name="attended_from_date" type="text" size="15" maxlength=10 value="'.$attended_from_date.'" class="style2"'. 
				'onFocus="this.select();" 
				id = "attended_from_date"
				onBlur="IsValidDate(this,\''.$date_format.'\'); "
				onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="attended_from_date_trigger" style="cursor:pointer" >
				<font size=3>['; 			
			ob_start();
	?>
			<script type="text/javascript">
			Calendar.setup ({
					inputField : "attended_from_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "attended_from_date_trigger", singleClick : true, step : 1
			});
			</script>
<?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();
			
		$sDateJS .= $calendarSetup;
		/**/
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDateJS = $sDateJS.$$dfbuffer.']';
#echo "$ dfbuffer ='".$dfbuffer."' &nbsp;&nbsp;";
#echo "$ $ dfbuffer ='".$$dfbuffer."' &nbsp;&nbsp;";
?>
             <?= $sDateJS ?>
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB">
            To
          </td>
          <td bgcolor="#F9FFFB">
<!--
            <input name="attended_to_date" type="text" class="style2" id="attended_to_date">
-->
<?php
		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
		//$phpfd=str_replace("yy","%Y", strtolower($phpfd));
	
	if (($attended_to_date!='0000-00-00')  && ($attended_to_date!=""))
		$attended_to_date = @formatDate2Local($attended_to_date,$date_format);
	else
		$attended_to_date='';

	$sDateJS= '<input name="attended_to_date" type="text" size="15" maxlength=10 value="'.$attended_to_date.'" class="style2"'. 
				'onFocus="this.select();" 
				id = "attended_to_date"
				onBlur="IsValidDate(this,\''.$date_format.'\'); "
				onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="attended_to_date_trigger" style="cursor:pointer" >
				<font size=3>['; 			
			ob_start();
	?>
			<script type="text/javascript">
			Calendar.setup ({
					inputField : "attended_to_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "attended_to_date_trigger", singleClick : true, step : 1
			});
			</script>
<?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();
			
		$sDateJS .= $calendarSetup;
		/**/
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDateJS = $sDateJS.$$dfbuffer.']';
#echo "$ dfbuffer ='".$dfbuffer."' &nbsp;&nbsp;";
#echo "$ $ dfbuffer ='".$$dfbuffer."' &nbsp;&nbsp;";
?>
             <?= $sDateJS ?>
          </td>
        </tr>
        <tr>
          <td colspan="2"bgcolor="#F9FFFB" class="style5">&nbsp;  </td>
        </tr>
      </table>
	 </td>
  </tr>
  <tr>
    <td width="25%" bgcolor="#F9FFFB" class="style2">
      <input name="attendant_type" id="attendant_type" type="hidden" value="<?php if($attendant_type) echo $attendant_type.' - '.$attendant_type_others; else echo "1 - Private Physician";  ?>">
      <input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="1" <?php if(($attendant_type == '1')|| (!$attendant_type)) echo "checked"; ?>>
      1. Private Physician
    </td>
    <td width="45%" bgcolor="#F9FFFB" class="style2">
      <input name="attendant_type_tmp" id="attendant_type_tmp"  onclick="typeOfAttendant(this)" type="radio" value="4" <?php if($attendant_type=='4'){ echo "checked";} ?>>
      4. None
    </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      
      <input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="2" <?php if($attendant_type == '2') echo "checked"; ?>>
      2. Public Health Officer
    </td>
    <td bgcolor="#F9FFFB" class="style2">
      
      <input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="5" <?php if($attendant_type == '5') echo "checked"; ?>>
      5. Others (Specify)
      <input type="text" size="50" name="attendant_type_others" id="attendant_type_others" onblur="trimString(this); $('attendant_type').value= '5 - '+this.value;" <?php if ($attendant_type==5) echo 'value="'.$attendant_type_others.'"'; else echo "disabled"; ?>>      
    </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      <input name="attendant_type_tmp" id="attendant_type_tmp" type="radio" onclick="typeOfAttendant(this)" value="3" <?php if($attendant_type == '3') echo "checked"; ?>>
      3. Hospital Authority
    </td>
    <td bgcolor="#F9FFFB" class="style2">&nbsp;</td>
  </tr>
</table><br />
<!--ENDS //ATTENDANT -->

<!-- CERTIFICATION OF DEATH -->
<table width="90%" border="0" align="center" cellpadding="1" cellspacing="0" bgcolor="#A0C2DA">
  <!--DWLayoutTable-->
  <tr> </tr>
  <tr>
    <td valign="top">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <!--DWLayoutTable-->
        <tr>
          <td width="1202" bgcolor="#FFFFFF">
            <table width="100%" border="0" cellpadding="2" cellspacing="1">
              <tr>
                <td bgcolor="#D5E3EE" class="style5"> Certification Of Death </td>
              </tr>
              <tr>
                <td bgcolor="#F9FFFB" class="style2">I hereby certify that the foregoing particulars are correct as near as same can be ascertained and further certify that I </td>
              </tr>
              <tr>
                <td bgcolor="#F9FFFB" class="style2">
                  <input name="death_cert_attended" id="death_cert_attended" type="radio" value="0" onclick="certificationOfDeath(this);" <?php if(($death_cert_attended=='0')||(!$death_cert_attended)) echo "checked"; ?> />
                  Have not attended the deceased </td>
              </tr>
              <tr>
                <td bgcolor="#F9FFFB" class="style2">
                  <?php
	if (($death_time !='00:00:00') && ($death_time!="")){
		$death_time_tmp = convert24HourTo12HourLocal($death_time);
#		echo "death_time_tmp ='".$death_time_tmp."' &nbsp;&nbsp;";		
		$death_time_array = explode(' ',$death_time_tmp);
		$death_time = $death_time_array[0];
		$death_time_period = $death_time_array[1];
	}else{
		$death_time = '';
	}
?>
                  <input name="death_cert_attended" id="death_cert_attended" type="radio" value="1" onclick="certificationOfDeath(this);" <?php if($death_cert_attended == '1') echo "checked"; ?> />
                  Have attended the deceased and that death occured at
                  <input name="death_time" type="text" class="style2" id="death_time" onchange="setFormatTime(this);" value="<?= $death_time ?>" <?php if(($death_cert_attended=='0')||(!$death_cert_attended)) echo "disabled"; ?> />
                  <select id="selAMPM" name="selAMPM" <?php if(($death_cert_attended=='0')||(!$death_cert_attended)) echo "disabled"; ?>>
                    <option value="A.M." <?php if ($death_time_period == 'AM') echo "selected";?> >A.M.</option>
                    <option value="P.M." <?php if ($death_time_period == 'PM') echo "selected";?> >P.M.</option>
                  </select>
                  on the date indicated above. </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td height="167" valign="top" bgcolor="#FFFFFF">
            <table width="100%" border="0" cellpadding="2" cellspacing="2">
              <tr>
                <td colspan="2" bgcolor="#D5E3EE" class="style2">Signature</td>
              </tr>
              <tr>
                <td bgcolor="#F9FFFB" class="style2"> Name in Print </td>
                <td bgcolor="#F9FFFB" class="style2">
                  <input name="attendant_name" type="text" class="style2" id="attendant_name" value="<?= $attendant_name ?>"  />
                </td>
              </tr>
              <tr>
                <td bgcolor="#F9FFFB" class="style2"> Title or Position </td>
                <td bgcolor="#F9FFFB" class="style2">
                  <input name="attendant_title" type="text" class="style2" id="attendant_title" value="<?= $attendant_title ?>" />
                </td>
              </tr>
              <tr>
                <td bgcolor="#F9FFFB" class="style2"> Address<br />
                </td>
                <td bgcolor="#F9FFFB" class="style2">
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
					
						if (($attendant_date_sign!='0000-00-00')  && ($attendant_date_sign!=""))
							$attendant_date_sign = @formatDate2Local($attendant_date_sign,$date_format);
						else
							$attendant_date_sign='';
					
						$sAttendant_date= '<input name="attendant_date_sign" type="text" size="15" maxlength=10 value="'.$attendant_date_sign.'"'. 
									'onFocus="this.select();" 
									id = "attendant_date_sign"
									onBlur="IsValidDate(this,\''.$date_format.'\'); "
									onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
									<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="attendant_date_sign_trigger" style="cursor:pointer" >
									<font size=3>['; 			
						ob_start();
					?>
                  <script type="text/javascript">
						Calendar.setup ({
								inputField : "attendant_date_sign", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "attendant_date_sign_trigger", singleClick : true, step : 1
						});
					</script>
                  <?php
						$calendarSetup = ob_get_contents();
						ob_end_clean();
					
						$sAttendant_date .= $calendarSetup;
						/**/
						$dfbuffer="LD_".strtr($date_format,".-/","phs");
						$sAttendant_date = $sAttendant_date.$$dfbuffer.']';
					?>
                  <?= $sAttendant_date ?>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table><br />
<!--ENDS -  //CERTIFICATION OF DEATH -->
<table width="90%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#A0C2DA">
  <tr>
    <!-- CORPSE DISPOSAL -->
    <td width="40%" height="129" valign="top" bgcolor="#FFFFFF">
      <table width="100%" border="0" cellpadding="2" cellspacing="1">
        <tr>
          <td bgcolor="#D5E3EE" class="style5">21. Corpse Disposal </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB" class="style2">
            
            <input type="hidden" name="corpse_disposal" id="corpse_disposal" value="<?php if($corpse_disposal) echo $corpse_disposal; else echo "1 - burial"; ?>" />
            <input name="corpse_disposal_tmp" id="corpse_disposal_tmp" type="radio" value="1" <?php if($corpse_disposal == '1') echo "checked"; ?> />
            1. Burial
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB" class="style2">
            
            <input name="corpse_disposal_tmp" id="corpse_disposal_tmp" type="radio" value="2" <?php if($corpse_disposal == '2') echo "checked"; ?> />
            2. Cremation
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB" class="style2">
            
            <input name="corpse_disposal_tmp" id="corpse_disposal_tmp" type="radio" value="3" <?php if($corpse_disposal == '3') echo "checked";?> />
            3. Others
	        
			(Specify)
            <input name="corpse_disposal_others" id="corpse_disposal_others" type="text" class="style2" />
       
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB" class="style2">&nbsp;</td>
        </tr>
      </table>
    </td>
    <!-- ENDS - //CORPSE DISPOSAL -->
    
    <!-- BURIAL / CREMATION PERMIT -->
    <td width="30%" valign="top" bgcolor="#F9FFFB">
      
      <table width="100%" border="0" cellpadding="2" cellspacing="1">
        <tr>
          <td colspan="2" bgcolor="#D5E3EE" class="style5">22. Burial/Cremation Permit </td>
        </tr>
        <tr>
          <td width="27%" bgcolor="#F9FFFB" class="style2">
            Number
          </td>
          <td width="73%" bgcolor="#F9FFFB" class="style2">
            <input name="burial_permit" type="text" class="style2" id="burial_permit" />
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
							<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="burial_date_issued_trigger" style="cursor:pointer" >
							<font size=3>['; 			
				ob_start();
				?>
				<script type="text/javascript">
					Calendar.setup ({
							inputField : "burial_date_issued", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "burial_date_issued_trigger", singleClick : true, step : 1
					});
				</script>
				<?php
					$calendarSetup = ob_get_contents();
					ob_end_clean();
			
					$sDateBurial .= $calendarSetup;
					/**/
					$dfbuffer="LD_".strtr($date_format,".-/","phs");
					$sDateBurial = $sDateBurial.$$dfbuffer.']';
				?>
          </td>
          <td bgcolor="#F9FFFB" class="style2">
          	<?= $sDateBurial ?>
          </td>
        </tr>
      </table>
    </td>
	<!--ENDS - //BURIAL / CREMATION PERMIT -->    
    
    <!-- AUTOPSY -->
    <td width="30%" valign="top" bgcolor="#F9FFFB">
      <table width="100%" border="0" cellpadding="2" cellspacing="1" class="style2">
        <tr>
          <td bgcolor="#D5E3EE" class="style5">23. Autopsy </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB">
            
            <input name="is_autopsy" type="hidden" value="<?= $is_autopsy ?>" />
            <input name="autopsy_tmp" type="radio" value="radiobutton" <?php if($is_autopsy==1) echo "checked"; ?> />
            1. Yes
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB">
            
            <input name="autopsy_tmp" type="radio" value="radiobutton" <?php if($is_autopsy==0) echo "checked"; ?> />
            2. No
          </td>
        </tr>
      </table>
      <!--ENDS- //AUTOPSY -->
    </td>
  </tr>
</table><br />

<!-- NAME AND ADDRESS OF CEMETERY OR CREMATORY -->
<table width="90%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#A0C2DA">
  <tr>
    <td width="33%" bgcolor="#D5E3EE">
   	  <span class="style5">24. Name and Address of Cemetery or Crematory</span></td>
    <td width="67%" bgcolor="#FFFFFF">
      <input name="cemetery_name_address" type="text" class="style2" id="cemetery_name_address" size="50" value="<?= $cemetery_name_address ?>" />
</td>
  </tr>
</table><br />
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#A0C2DA">
  <tr>
    <td colspan="2" bgcolor="#D5E3EE"><b class="style5">25. Informant</b></td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      Name in Print      
    </td>
    <td bgcolor="#F9FFFB" class="style2">
      <input name="informant_name" type="text" class="style2" id="informant_name" value="<?= $informant_name ?>" />
    </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      Relationship to the deceased      
    </td>
    <td bgcolor="#F9FFFB" class="style2">
      <input name="informant_relation" type="text" class="style2" id="informant_relation" value="<?= $informant_relation ?>" />
    </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      Address<br />
      
    </td>
    <td bgcolor="#F9FFFB" class="style2">
      <textarea name="informant_address" cols="50" class="style2" id="informant_address"><?= $informant_address ?></textarea>
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
	
		$sDateInformant= '<input name="informant_date_sign" type="text" size="15" maxlength=10 value="'.$informant_date_sign.'"'. 
					'onFocus="this.select();" 
					id = "informant_date_sign"
					onBlur="IsValidDate(this,\''.$date_format.'\'); "
					onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
					<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="informant_date_sign_trigger" style="cursor:pointer" >
					<font size=3>['; 			
		ob_start();
		?>
		<script type="text/javascript">
			Calendar.setup ({
					inputField : "informant_date_sign", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "informant_date_sign_trigger", singleClick : true, step : 1
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
</table><br />
<!--ENDS - //NAME AND ADDRESS OF CEMETERY OR CREMATORY -->

<!-- PREPARED BY -->
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#A0C2DA">
  <tr>
    <td colspan="2" bgcolor="#D5E3EE" class="style5">26. Prepared by </td>
  </tr>
  <tr>
    <td width="9%" bgcolor="#F9FFFB" class="style2">
      Name in Print      
    </td>
    <td width="91%" bgcolor="#F9FFFB" class="style2">
      <input name="encoder_name" type="text" class="style2" id="encoder_name" value ="<?= $encoder_name ?>" />
    </td>
  </tr>
  <tr>
    <td bgcolor="#F9FFFB" class="style2">
      Title or Position      
    </td>
    <td bgcolor="#F9FFFB" class="style2">
      <input name="encoder_title" type="text" class="style2" id="encoder_title" value="<?= $encoder_title ?>" />
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
					<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="encoder_date_sign_trigger" style="cursor:pointer" >
					<font size=3>['; 			
		ob_start();
	?>
	<script type="text/javascript">
		Calendar.setup ({
				inputField : "encoder_date_sign", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "encoder_date_sign_trigger", singleClick : true, step : 1
		});
	</script>
	<?php
		$calendarSetup = ob_get_contents();
		ob_end_clean();

		$sDatePrepared .= $calendarSetup;
		/**/
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDatePrepared = $sDatePrepared.$$dfbuffer.']';
	?>
    
    </td>
    <td bgcolor="#F9FFFB" class="style2">
       <?= $sDatePrepared ?>
    </td>
  </tr>
</table>
<!--ENDS -//PREPARED BY -->

<div align="center">
<?php #echo "date_birth = ".date("m/d/Y",strtotime($date_birth));
      #echo "death_date = ".$death_date;
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