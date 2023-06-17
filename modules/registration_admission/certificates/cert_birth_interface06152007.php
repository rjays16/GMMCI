<?php
include("roots.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;
	# Create the helper class for the country table 
include_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address('country');


if (isset($_GET['pid']) && $_GET['pid']){
	$pid = $_GET['pid'];
}
if (isset($_POST['pid']) && $_POST['pid']){
	$pid = $_POST['pid'];
}

include_once($root_path.'include/care_api_classes/class_cert_birth.php');
$obj_birthCert = new BirthCertificate($pid);

if ($pid){
	if (!($basicInfo=$person_obj->BasicDataArray($pid))){
		echo '<em class="warn"> sorry byt the page cannot be displayed!</em>';
		exit();
	}
#	echo "basicInfo : <br> \n"; print_r($basicInfo); echo "<br>\n";
	extract($basicInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid PID!</em>';
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
$errorMsg='';
/*
echo "HTTP_POST_VARS : <br>"; print_r($HTTP_POST_VARS); echo "<br> \n";
echo "medCertInfo : <br>"; print_r($medCertInfo); echo "<br> \n";
echo "encounter_nr = '".$encounter_nr."' <br> \n";
echo "obj_medCert->sql = '".$obj_medCert->sql."' <br> \n";
#exit();
*/
if (isset($_POST['mode'])){
	switch($_POST['mode']) {
		case 'save':
			$HTTP_POST_VARS['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
			$HTTP_POST_VARS['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_dt']=date('Y-m-d H:i:s');
			if ($obj_birthCert->saveBirthCertificateInfoFromArray($HTTP_POST_VARS)){
				$errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
			}else{
				$errorMsg='<font style="color:#FF0000">'.$obj_birthCert->getErrorMsg().'</font>';			
			}
		break;
		case 'update':
			$HTTP_POST_VARS['history'] = "Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
			$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['modify_dt']=date('Y-m-d H:i:s');
			if ($obj_birthCert->updateBirthCertificateInfoFromArray($HTTP_POST_VARS)){
				$errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated sucessfully!".'</font>';
			}else{
				$errorMsg='<font style="color:#FF0000">'.$obj_birthCert->getErrorMsg().'</font>';			
			}
		break;
	}# end of switch statement
}

$birthCertInfo = $obj_birthCert->getBirthCertRecord($pid);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Certificate of Live Birth</title>
<style type="text/css">
<!--
.style1 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
}
.style2 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.style3 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 18px;
}
body {
	background-color: #E0FCF1;
}
-->
</style>
<?php
		echo '<script type="text/javascript" language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>';
?>
<script language="javascript">
		/*	
				This will trim the string i.e. no whitespaces in the
				beginning and end of a string AND only a single
				whitespace appears in between tokens/words 
				input: object
				output: object (string) value is trimmed
		*/
	function trimString(objct){
		objct.value = objct.value.replace(/^\s+|\s+$/g,"");
		objct.value = objct.value.replace(/\s+/g," "); 
	}/* end of function trimString */

	var seg_validTime=false;

	function setFormatTime(thisTime){
	//	var time = $('time_text_d');
		var stime = thisTime.value;
		var hour, minute;
		var ftime ="";
		var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
		var f2 = /^[0-9]\:[0-5][0-9]$/;
		
		trimString(thisTime);
	
		if (thisTime.value==''){
			seg_validTime=false;
			return;
		}
	
		stime = stime.replace(':', '');
	
		if (stime.length == 3){
			hour = stime.substring(0,1);
			minute = stime.substring(1,3);
		} else if (stime.length == 4){
			hour = stime.substring(0,2);
			minute = stime.substring(2,4);
		}else{
			alert("Invalid time format.");
			thisTime.value = "";
			seg_validTime=false;
			return;
		}
	
		if(hour >12){
			 hour -= 12;
			 $('selAMPM').value = "P.M.";
		}
	
		ftime =  hour + ":" + minute;
		
		if(!ftime.match(f1) && !ftime.match(f2)){
			thisTime.value = "";
			alert("Invalid time format.");
			seg_validTime=false;   
		}else{
			thisTime.value = ftime;
			seg_validTime=true;   
		}
	}// end of function setFormatTime
	
	function chkInteger(obj){
		var objValue = obj.value;
		var objName = obj.name;

		if (objValue=="")
			return false;
		if (!parseInt(objValue, 10)){
			alert("Not an integer!");
			obj.value="";
			obj.focus();
			return false;
		}
		obj.value = parseInt(objValue, 10);
		return true;
	}// end of function chkInteger

	function chkDecimal(obj){
		var objValue = obj.value;
		var objName = obj.name;

		if (objValue=="")
			return false;
		if (!parseFloat(objValue)){
			alert("Not a number!");
			obj.value="";
			obj.focus();
			return false;
		}
		obj.value = parseFloat(objValue);
		return true;
	}// end of function chkDecimal

</script>
</head>

<body>
<form id="livebirth_certificate" name="livebirth_certificate" method="post" action="">
<table width="90%" border="0" align="center" cellpadding="4" cellspacing="4" bgcolor="#EEFFDD">
  <tr>
    <td bgcolor="#EEFFDD">
      <center>
        <span class="style2">Republic of the Phillippines</span><BR /> 
        <span class="style1">OFFICE OF THE CIVIL REGISTRAR GENERAL</span><BR /> 
        <span class="style3">CERTIFICATE OF LIVE BIRTH</span> 
      </center>
    </td>
  </tr>
  <tr>
    <td>
      <table width="100%" height="58" border="0" cellpadding="4" cellspacing="2">
        <tr>
          <td width="72%" bgcolor="#F9FFFB">
              <label><span class="style2">Province &nbsp;&nbsp;</span> 
                <input type="hidden" name="province" id="province" value="Davao del Sur">
					 <b>Davao del Sur</b>
              </label>
          </td>
          <td width="28%" rowspan="2" bgcolor="#F9FFFB">
            <label><span class="style2">Registry no. &nbsp;&nbsp;</span>
            <input type="text" name="registry_nr" id="registry_nr" value="<?= $registry_nr ?>" onblur="trimString(this)">
            </label>
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB">
              <label><span class="style2">City/Municipality &nbsp;&nbsp;</span>
                <input type="hidden" name="muncity" id="muncity" value="Davao City">
					 <b>Davao City</b>
              </label>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr>
		  		<td colspan="*">
					NAME
				</td>
		  </tr>
        <tr>
          <td width="40%" align="center" bgcolor="#F9FFFB">
            <label>Given Name
				<br>
				<b><?= ucwords(strtolower($name_first)) ?></b>
            <input type="hidden" name="name_first" id="name_first" value="<?= ucwords(strtolower($name_first)) ?>">
				</label>
          </td>
          <td width="30%" align="center" bgcolor="#F9FFFB">
            <label>Middle Name
				<br>
				<b><?= ucwords(strtolower($name_middle)) ?></b>
            <input type="hidden" name="name_middle" id="name_middle" value="<?= ucwords(strtolower($name_middle)) ?>">
            </label>
          </td>
          <td width="30%" align="center" bgcolor="#F9FFFB">
            <label>Last Name
				<br>
				<b><?= ucwords(strtolower($name_last)) ?></b>
            <input type="hidden" name="name_last" id="name_last" value="<?= ucwords(strtolower($name_last)) ?>">
            </label>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr>
          <td width="32%">
            <table width="100%" border="0" cellpadding="4" cellspacing="2">
              <tr>
                <td colspan="2" bgcolor="#F9FFFB">SEX</td>					 
              </tr>
              <tr>
                <td width="50%" bgcolor="#F9FFFB">
					 	<input type="hidden" name="sex" id="sex" value="<?= $sex ?>">
                  <label>
                  <input name="radiobutton" type="radio" value="radiobutton" disabled <?php if ($sex=='m') echo "checked"; ?> >
                  Male</label>
                </td>
                <td width="50%" bgcolor="#F9FFFB">
                  <label>
                  <input name="radiobutton" type="radio" value="radiobutton" disabled <?php if ($sex=='f') echo "checked"; ?>>
                  Female</label>
                </td>
              </tr>
            </table>
          </td>
          <td width="68%">
            <table width="100%" border="0" cellpadding="4" cellspacing="2">
              <tr>
                <td colspan="3" bgcolor="#F9FFFB">DATE OF BIRTH</td>
              </tr>
              <tr>
                <td width="22%" align="center" bgcolor="#F9FFFB">
                  <label>Day
						<br>
						<b><?= $birthDay ?></b>
						<input type="hidden" name="birth_day" id="birth_day" value="<?= $birthDay ?>">
                  <select name="select" disabled>
<?php
for ($i=1; $i<=31; $i++){
	if ($birthDay==$i)
		echo "							<option value='$i' selected>$i</option> \n";
	else
		echo "							<option value='$i'>$i</option> \n";
}
?>
                  </select>
                  </label>
                </td>
                <td width="40%" bgcolor="#F9FFFB">
                  <label>Month
						<br>
						<b>
							<?php 
								switch($birthMonth ){
									case 1: echo"January"; break;
									case 2: echo"February"; break;									
									case 3: echo"March"; break;									
									case 4: echo"April"; break;									
									case 5: echo"May"; break;									
									case 6: echo"June"; break;									
									case 7: echo"July"; break;									
									case 8: echo"August"; break;									
									case 9: echo"September"; break;									
									case 10: echo"October"; break;									
									case 11: echo"November"; break;									
									case 12: echo"December"; break;									
								}
							?>
						</b>
						<input type="hidden" name="birth_month" id="birth_month" value="<?= $birthMonth ?>">
                  <select name="select2" disabled>
                    <option value="1" <?php if ($birthMonth==1) echo "selected"; ?>>January</option>
                    <option value="2" <?php if ($birthMonth==2) echo "selected"; ?>>February</option>
                    <option value="3" <?php if ($birthMonth==3) echo "selected"; ?>>March</option>
                    <option value="4" <?php if ($birthMonth==4) echo "selected"; ?>>April</option>
                    <option value="5" <?php if ($birthMonth==5) echo "selected"; ?>>May </option>
                    <option value="6" <?php if ($birthMonth==6) echo "selected"; ?>>June</option>
                    <option value="7" <?php if ($birthMonth==7) echo "selected"; ?>>July</option>
                    <option value="8" <?php if ($birthMonth==8) echo "selected"; ?>>August</option>
                    <option value="9" <?php if ($birthMonth==9) echo "selected"; ?>>September</option>
                    <option value="10" <?php if ($birthMonth==10) echo "selected"; ?>>October</option>
                    <option value="11" <?php if ($birthMonth==11) echo "selected"; ?>>November</option>
                    <option value="12" <?php if ($birthMonth==12) echo "selected"; ?>>December</option>
                  </select>
                  </label>
                </td>
                <td width="38%" bgcolor="#F9FFFB">
                  <label>Year
						<br>
						<b><?= $birthYear ?></b>
						<input type="hidden" name="birth_year" id="birth_year" value="<?= $birthYear ?>">
                  <select name="select3" disabled>
							
<?php
for ($i=date(Y); $i>=1900; $i--){
	if ($birthYear==$i)
		echo "							<option value='$i' selected>$i</option>\n";
	else
		echo "							<option value='$i'>$i</option>\n";
}
?>
                  </select>
                  </label>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr>
          <td colspan="3" bgcolor="#F9FFFB">PLACE OF BIRTH</td>
        </tr>
        <tr align="center" valign="top">
          <td width="40%" bgcolor="#F9FFFB">
            <label>Name of Hospital/Clinic/Institution/ <br> House No.,Street,Barangay
				<br>
				<b>Gonzales Maranan Medical Center Incorporated</b>
				<input type="hidden" name="birth_place_basic" id="birth_place_basic" value="Gonzales Maranan Medical Center Incorporated">
            </label>
          </td>
          <td width="30%" bgcolor="#F9FFFB">
			 	<label>City/Municipality
				<br><br>
				<b>Digos CITY</b>
				<input type="hidden" name="birth_place_mun" id="birth_place_mun" value="Davao City">
				</label>
			 </td>
          <td width="30%" bgcolor="#F9FFFB">
            <label>Province
				<br><br>
				<b>DAVAO DEL SUR</b>
				<input type="hidden" name="birth_place_prov" id="birth_place_prov" value="Davao del Sur">
            </label>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellpadding="4" cellspacing="2" bgcolor="#F9FFFB">
        <tr>
          <td width="50%">
            <table width="100%" border="0" cellpadding="4" cellspacing="2">
              <tr>
                <td bgcolor="#F9FFFB">5a. TYPE OF BIRTH</td>
              </tr>
              <tr>
                <td bgcolor="#F9FFFB">
					 	<input type="hidden" name="birth_type" id="birth_type" value="<?= $birth_type ?>">
                  <label>
                  <input name="birth_type" id="birth_type" type="radio" value="1" <?php if ($birth_type=='1') echo "checked"; ?>>
                  Single
                  <input name="birth_type" id="birth_type" type="radio" value="2" <?php if ($birth_type=='2') echo "checked"; ?>>
                  Twin
                  <input name="birth_type" id="birth_type" type="radio" value="3" <?php if ($birth_type=='3') echo "checked"; ?>>
                  Triplet, etc.</label>
                </td>
              </tr>
              <tr>
                <td bgcolor="#F9FFFB">c. BIRTH ORDER (live births and fetal deaths including this delivery) </td>
              </tr>
              <tr align="center">
                <td bgcolor="#F9FFFB">
					 	 <input type="text" name="birth_order" id="birth_order" value="<?= $birth_order ?>" onblur="trimString(this); chkInteger(this);">
						&nbsp; (first, second, third, etc.) 
					 </td>
              </tr>
            </table>
          </td>
          <td width="50%">
            <table width="100%" border="0" cellpadding="4" cellspacing="2">
              <tr>
                <td width="100%" bgcolor="#F9FFFB">b. IF MULTIPLE BIRTH, CHILD WAS</td>
              </tr>
              <tr>
                <td width="100%" bgcolor="#F9FFFB">
						<input type="hidden" name="birth_rank" id="birth_rank" value="<?= $birth_rank ?>">
                  <label>
                  <input name="birth_rank" id="birth_rank" type="radio" value="radiobutton" <?php if ($birth_rank=='1') echo "checked"; ?>>
                  First
                  <input name="birth_rank" id="birth_rank" type="radio" value="radiobutton" <?php if ($birth_rank=='2') echo "checked"; ?>>
                  Second
                  <input name="birth_rank" id="birth_rank" type="radio" value="radiobutton" <?php if ($birth_rank > 2) echo "checked"; ?>>
                  Others, Specify
                  <input type="text" name="birth_rank_others" id="birth_rank_others" onblur="trimString(this); chkInteger(this);" value="<?php if ($birth_rank > 2) echo $birth_rank; ?>">
                  </label>
                </td>
              </tr>
              <tr>
                <td width="100%" bgcolor="#F9FFFB">d. WEIGHT AT BIRTH</td>
              </tr>
              <tr align="center">
                <td width="100%" bgcolor="#F9FFFB">
						 <label>
						 <input name="textfield12" type="text" size="10" onblur="trimString(this); chkDecimal(this);" value="<?= $birth_weight ?>">&nbsp; grams
						 </label>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr>
          <td width="59%" height="140" bgcolor="#F9FFFB">
            <table width="100%" border="0" cellpadding="4" cellspacing="2">
              <tr>
                <td bgcolor="#F9FFFB">c. BIRTH ORDER (live births and fetal deaths including this delivery) </td>
              </tr>
              <tr>
                <td bgcolor="#F9FFFB">
                  <label>
                  <input type="text" name="birth_order" id="birth_order" value="<?= $birth_order ?>" onblur="trimString(this); chkInteger(this);">
                  (first, second, third, etc.) </label>
                </td>
              </tr>
            </table>
          </td>
          <td width="41%" bgcolor="#F9FFFB">
            <table width="100%" border="0" cellspacing="2">
              <tr>
                <td height="87" bgcolor="#F9FFFB">
                  <label>d. Weight at birth
                  <input name="textfield12" type="text" size="10" onblur="trimString(this); chkDecimal(this);"  value="<?= $birth_weight ?>">
grams</label>
</td>
              </tr>
            </table>
            <label></label>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr>
          <td colspan="3" bgcolor="#F9FFFB">6. MAIDEN NAME</td>
        </tr>
        <tr>
          <td width="40%" align="center" bgcolor="#F9FFFB">
            <label>First Name
				<br>
            <input type="text" size="50" name="m_name_first" id="m_name_first" onblur="trimString(this)" value="<?= $m_name_first ?>">
            </label>
          </td>
          <td width="30%" align="center" bgcolor="#F9FFFB">
            <label>Middle Name
				<br>
            <input type="text" size="30" name="m_name_middle" id="m_name_middle" onblur="trimString(this)" value="<?= $m_name_middle ?>">
            </label>
          </td>
          <td width="30%" align="center" bgcolor="#F9FFFB">
            <label>Last Name
				<br>
            <input type="text" size="30" name="m_name_last" id="m_name_last" onblur="trimString(this)" value="<?= $m_name_last ?>">
            </label>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr>
          <td width="50%" bgcolor="#F9FFFB">
            <label>7. CITIZENSHIP &nbsp;&nbsp; 
            <select name="m_citizenship" id="m_citizenship">
<?php
		# Retrieve record set of all countries
	$country_obj = $address_country->getAllAddress();
	if (empty($m_citizenship)|| !$m_citizenship) 
		$m_citizenship="PH";   # default country of citizenship
	while ($result=$country_obj->FetchRow()){
		$selected='';
		if ($m_citizenship==$result['country_code'])
			$selected='selected';
		echo '					<option value="'.$result['country_code'].'" '.$selected.'>'.$result['country_name']."</option> \n";
	}
?>
				</select>
            </label>
          </td>
          <td width="50%" bgcolor="#F9FFFB">
            <label>8. RELIGION &nbsp;&nbsp;
				<select name="m_religion" id="m_religion">
<?php
		# Retrieve record set of all religions
	$religion_obj = $person_obj->getReligion();
	if (empty($m_religion)|| !$m_religion) 
		$m_religion=1;   # default religion, 'Not Indicated'
	while ($result=$religion_obj->FetchRow()){
		$selected='';
		if ($m_religion==$result['religion_nr'])
			$selected='selected';
		echo '					<option value="'.$result['religion_nr'].'" '.$selected.'>'.$result['religion_name']."</option> \n";
	}
?>
				</select>
            </label>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr>
          <td width="33%" bgcolor="#F9FFFB">
            <label>9a. Total number of children born alive &nbsp;
            <input name="m_total_alive" id="m_total_alive" type="text" size="5" onblur="trimString(this); chkInteger(this);" value="<?= $m_total_alive ?>">
            </label>
          </td>
          <td width="33%" bgcolor="#F9FFFB">
            <label>b. No. of children still living including this birth &nbsp;
            <input name="m_still_living" id="m_still_living" type="text" size="5" onblur="trimString(this); chkInteger(this);" value="<?= $m_still_living ?>">
            </label>
          </td>
          <td width="34%" bgcolor="#F9FFFB">
            <label>c. No. of children born alive but now are dead &nbsp;
            <input name="m_now_dead" id="m_now_dead" type="text" size="5" onblur="trimString(this); chkInteger(this);" value="<?= $m_now_dead ?>">
            </label>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr>
          <td width="47%" bgcolor="#F9FFFB">
            <label>10. OCCUPATION &nbsp;&nbsp;
				<select name="m_occupation" id="m_occupation">
<?php
		# Retrieve record set of all occupations
	$occupation_obj = $person_obj->getOccupation();
	if (empty($m_occupation)|| !$m_occupation) 
		$m_occupation=1;   # default occupation, 'Not Indicated'
	while ($result=$occupation_obj->FetchRow()){
		$selected='';
		if ($m_occupation==$result['occupation_nr'])
			$selected='selected';
		echo '					<option value="'.$result['occupation_nr'].'" '.$selected.'>'.$result['occupation_name']."</option> \n";
	}
?>
				</select>
            </label>
          </td>
          <td width="53%" bgcolor="#F9FFFB">
            <label>11. Age at the time of this birth &nbsp;
            <input name="m_age" id="m_age" type="text" size="5" onblur="trimString(this); chkInteger(this);" value="<?= $m_age ?>">
            </label>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr>
          <td colspan="3" bgcolor="#F9FFFB">12. RESIDENCE</td>
        </tr>
        <tr align="center">
          <td width="40%" bgcolor="#F9FFFB">
            <label>Street/Baranggay
					<br>
              <input type="text" name="m_residence_basic" id="m_residence_basic" onblur="trimString(this)" value="<?= $m_residence_basic ?>" size="50">
            </label>
          </td>
          <td width="30%" bgcolor="#F9FFFB">City/Municipality
			 	<br>
            <input type="text" name="m_residence_mun" id="m_residence_mun" onblur="trimString(this)" value="<?= $m_residence_mun ?>" size="30">
          </td>
          <td width="30%" bgcolor="#F9FFFB">
            <label>Province
					<br>
              <input type="text" name="m_residence_prov" id="m_residence_prov" onblur="trimString(this)" value="<?= $m_residence_prov ?>" size="30">
            </label>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr>
          <td colspan="3" bgcolor="#F9FFFB">13. NAME</td>
        </tr>
        <tr align="center">
          <td width="40%" bgcolor="#F9FFFB">
            <label>First Name
				<br>
            <input type="text" size="50" name="f_name_first" id="f_name_first" onblur="trimString(this)" value="<?= $f_name_first ?>">
            </label>
          </td>
          <td width="30%" bgcolor="#F9FFFB">
            <label>Middle Name
				<br>
            <input type="text" size="30" name="f_name_middle" id="f_name_middle" onblur="trimString(this)" value="<?= $f_name_middle ?>">
            </label>
          </td>
          <td width="30%" bgcolor="#F9FFFB">
            <label>Last Name
				<br>
            <input type="text" size="30" name="f_name_last" id="f_name_last" onblur="trimString(this)" value="<?= $f_name_last ?>">
            </label>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr>
          <td width="50%" bgcolor="#F9FFFB">
            <label>14. CITIZENSHIP &nbsp;&nbsp;
				<select name="f_citizenship" id="f_citizenship">
<?php
		# Retrieve record set of all countries
	$country_obj = $address_country->getAllAddress();
	if (empty($f_citizenship)|| !$f_citizenship) 
		$f_citizenship="PH";   # default country of citizenship
	while ($result=$country_obj->FetchRow()){
		$selected='';
		if ($f_citizenship==$result['country_code'])
			$selected='selected';
		echo '					<option value="'.$result['country_code'].'" '.$selected.'>'.$result['country_name']."</option> \n";
	}
?>
				</select>            </label>
          </td>
          <td width="50%" bgcolor="#F9FFFB">
            <label>15. RELIGION &nbsp;&nbsp;
				<select name="f_religion" id="f_religion">
<?php
		# Retrieve record set of all religions
	$religion_obj = $person_obj->getReligion();
	if (empty($f_religion)|| !$f_religion) 
		$f_religion=1;   # default religion, 'Not Indicated'
	while ($result=$religion_obj->FetchRow()){
		$selected='';
		if ($f_religion==$result['religion_nr'])
			$selected='selected';
		echo '					<option value="'.$result['religion_nr'].'" '.$selected.'>'.$result['religion_name']."</option> \n";
	}
?>
				</select>
            </label>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="style2">
      <table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr>
          <td width="47%" bgcolor="#F9FFFB">
            <label>16. OCCUPATION &nbsp;&nbsp;
				<select name="f_occupation" id="f_occupation">
<?php
		# Retrieve record set of all occupations
	$occupation_obj = $person_obj->getOccupation();
	if (empty($f_occupation)|| !$f_occupation) 
		$f_occupation=1;   # default occupation, 'Not Indicated'
	while ($result=$occupation_obj->FetchRow()){
		$selected='';
		if ($f_occupation==$result['occupation_nr'])
			$selected='selected';
		echo '					<option value="'.$result['occupation_nr'].'" '.$selected.'>'.$result['occupation_name']."</option> \n";
	}
?>
				</select>

            </label>
          </td>
          <td width="53%" bgcolor="#F9FFFB">
            <label>17. Age at the time of this birth
	            <input name="f_age" id="f_age" type="text" size="5" onblur="trimString(this); chkInteger(this);" value="<?= $f_age ?>">
            </label>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  
</table>
<table width="90%" border="0" align="center" cellpadding="4" cellspacing="2" bgcolor="#EEFFDD">
  <tr>
    <td colspan="2" bgcolor="#F9FFFB" class="style2">18. DATE AND PLACE OF MARRIAE OF PARENTS (If not married , accomplish Affidavit of Acknowledgment/Admission of Paternity) 
       <label>
      <input type="text" size="100" name="parent_marriage_info" id="parent_marriage_info" onblur="trimString(this)" value="<?= $parent_marriage_info ?>">
      </label>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="style2">
      <table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr>
          <td colspan="2" bgcolor="#F9FFFB">19. ATTENDANT</td>
        </tr>
        <tr>
          <td width="35%" bgcolor="#F9FFFB">
            <table width="100%" border="0" cellpadding="4" cellspacing="2">
              <tr>
                <td>
					 	<input type="hidden" name="attendant_type" id="attendant_type" value="<?= $attendant_type ?>">
                  <label>
                  <input type="radio" name="attendant_type" id="attendant_type" value="radiobutton">
                  1. Physician</label>
                </td>
              </tr>
              <tr>
                <td>
                  <input type="radio" name="attendant_type" id="attendant_type" value="radiobutton"> 
                  4. Hilot (Traditional Midwife)</td>
              </tr>
            </table>
          </td>
          <td bgcolor="#F9FFFB">
            <table width="100%" border="0" cellpadding="4" cellspacing="2">
              <tr>
                <td>
                  <label>
                  <input type="radio" name="attendant_type" id="attendant_type" value="radiobutton"> 
                  2. Nurse</label>
                </td>
                <td>
                  <input type="radio" name="attendant_type" id="attendant_type" value="radiobutton"> 
                  3. Midwife</td>
              </tr>
              <tr>
                <td colspan="2">
                  <input type="radio" name="attendant_type" id="attendant_type" value="radiobutton"> 
                  <label>5. Others (Specify) &nbsp;&nbsp;
                  <input type="text" size="50" name="attendant_type_others" id="attendant_type_others" onblur="trimString(this)">
                  </label>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="style2">19b. CERTIFICATION OF BIRTH <br>
	 I hereby certify that I attended the birth of the child who was born alive at &nbsp;&nbsp;
	   <label>
	   <input type="text" size="3" name="birth_time" id="birth_time" value="<?= $birth_time ?>" onchange="setFormatTime(this);"> 
	   &nbsp;o'clock &nbsp; 
			<select id='selAMPM' name="selAMPM">
				<option value="A.M.">A.M.</option>
				<option value="P.M.">P.M.</option>
			</select>
		on the date stated above.</label>
	   <br>
       <table width="100%" border="0" cellspacing="2">
         <tr>
           <td width="50%" bgcolor="#F9FFFB">Signature</td>
           <td width="50%" rowspan="2" bgcolor="#F9FFFB">
             <label>Address<br />			 
             <textarea name="attendant_address" id="attendant_address" cols="50" rows="2"><?= $attendant_address ?></textarea>
             </label>
           </td>
         </tr>
         <tr>
           <td bgcolor="#F9FFFB">
             <label>Name in Print &nbsp;&nbsp;
             <input type="text" size="50" name="attendant_name" id="attendant_name" onblur="trimString(this)" value="<?= $attendant_name ?>">
             </label>
           </td>
         </tr>
         <tr>
           <td bgcolor="#F9FFFB">
             <label>Title or Position &nbsp;&nbsp;
             <input type="text" size="50" name="attendant_title" id="attendant_title" onblur="trimString(this)" value="<?= $attendant_title ?>">
             </label>
           </td>
           <td bgcolor="#F9FFFB">
             <label>Date &nbsp;&nbsp;
             <input type="text" name="attendant_date_sign" id="attendant_date_sign" onblur="trimString(this)" value="<?= $attendant_date_sign ?>">
             </label>
           </td>
         </tr>
       </table>
       <label>      </label>
    </td>
  </tr>
  <tr>
    <td colspan="2" class="style2">
      <table width="100%" border="0">
        <tr>
          <td bgcolor="#F9FFFB">20. INFORMANT</td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB">
            <table width="100%" border="0" cellspacing="2">
              <tr>
                <td width="50%">Signature</td>
                <td width="50%" rowspan="2">
                  <label>Address <br>
		             <textarea name="informant_address" id="informant_address" cols="50" rows="2"><?= $informant_address ?></textarea>
                  </label>
                </td>
              </tr>
              <tr>
                <td>
                  <label>Name in Print&nbsp;&nbsp;
		             <input type="text" size="50" name="informant_name" id="informant_name" onblur="trimString(this)" value="<?= $informant_name ?>">
                  </label>
                </td>
              </tr>
              <tr>
                <td>
                  <label>Title or Position&nbsp;&nbsp;
		             <input type="text" size="50" name="informant_title" id="informant_title" onblur="trimString(this)" value="<?= $informant_title ?>">
						</label>
                </td>
                <td>
                  <label>Date
		             <input type="text" name="informant_date_sign" id="informant_date_sign" onblur="trimString(this)" value="<?= $informant_date_sign ?>">
                  </label>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td width="50%" class="style2">21. PREPARED BY
       <table width="100%" border="0" cellspacing="2">
        <tr>
          <td bgcolor="#F9FFFB">Signature</td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB">
            <label>Name in Print &nbsp;&nbsp;
             <input type="text" size="50" name="encoder_name" id="encoder_name" onblur="trimString(this)" value="<?= $encoder_name ?>">
            </label>
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB">
            <label>Title or Position&nbsp;&nbsp;
             <input type="text" size="50" name="encoder_title" id="encoder_title" onblur="trimString(this)" value="<?= $encoder_title ?>">
            </label>
          </td>
        </tr>
      </table>
    </td>
    <td width="50%" class="style2">&nbsp;</td>
  </tr>
</table>
</form>
</body>
</html>
