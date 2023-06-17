<?php
require_once("roots.php");

require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_social_service.php');

#added by VAN 02-18-08
define('NO_2LEVEL_CHK',1);

define('LANG_FILE','aufnahme.php');
$local_user='aufnahme_user';

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;
	# Create the helper class for the country table
include_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address('country');

#added by VAN 05-23-08
require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

#Added by Cherry 08-24-10
include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

#Added by Cherry 08-25-10
include_once($root_path.'include/care_api_classes/class_department.php');
$dep_obj = new Department;

#Added by Cherry 08-25-10
include_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_med_cert.php');
$ic_obj = new SegICCertMed;

include_once($root_path.'include/care_api_classes/class_social_service.php');
$socserv = new SocialService;

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();

		if ($row = $objInfo->getAllHospitalInfo()) {
			$row['hosp_agency'] = strtoupper($row['hosp_agency']);
			$row['hosp_name']   = strtoupper($row['hosp_name']);
		}
		else {
			$row['hosp_country'] = "Republic of the Philippines";
			$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$row['hosp_name']    = "SOUTHERN PHILIPPINES MEDICAL CENTER";
			$row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
		}

#added by VAN 05-19-08
require_once($root_path.'include/care_api_classes/class_dateGenerator.php');
$dategen = new DateGenerator;
#------------------

require($root_path.'include/inc_front_chain_lang.php');

if (isset($_GET['pid']) && $_GET['pid']){
	$pid = $_GET['pid'];
}
if (isset($_POST['pid']) && $_POST['pid']){
	$pid = $_POST['pid'];
}
#echo "pid = ".$pid;
include_once($root_path.'include/care_api_classes/class_cert_birth.php');
$obj_birthCert = new BirthCertificate($pid);

if($encounter_nr){
#    if(!($encInfo = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
		if(!($encInfo=$enc_obj->getEncounterInfo($encounter_nr))){
				echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
				exit();
		}
		#extract($encInfo);
}else{
		echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
		exit();
}

$birthYear = intval(substr($date_birth, 0, 4));
$birthMonth = intval(substr($date_birth, 5, 7));
$birthDay = intval(substr($date_birth, 8, 10));

$errorMsg='';

if (isset($_POST['mode'])){
	switch($_POST['mode']) {
		case 'save':

			$mss_no = $socserv->getLastMSSnr(date("Y-m-d"),"'".$GLOBAL_CONFIG['mss_nr_init']."'");
			$pid = $encInfo['pid'];

			$data_patient = array('pid'=>$pid,
														'mss_no'=>$mss_no,
														'status'=>'',
														'history'=>"Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
														'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
														'modify_time'=>date('Y-m-d H:i:s'),
														'create_id'=>$HTTP_SESSION_VARS['sess_user_name'],
														'create_time'=>date('Y-m-d H:i:s')
											);

			$data = array('mss_no'=>$mss_no,
										'encounter_nr'=>$encounter_nr,
										'informant_name'=>$_POST['informant_name'],
										'info_address'=>$_POST['info_address'],
										'educational_attain'=>$_POST['educational_attain'],
										'occupation'=>$_POST['occupation'],
										'employer'=>$_POST['employer'],
										'employer_address'=>$_POST['employer_address'],
										'source_income'=>$_POST['source_income'],
										'monthly_income'=>$_POST['monthly_income'],
										'per_capita_income'=>$_POST['per_capita_income'],
										'food_expense'=>$_POST['food_expense'],
										'ligth_expense'=>$_POST['ligth_expense'],
										'water_expense'=>$_POST['water_expense'],
										'transport_expense'=>$_POST['transport_expense'],
										'other_expense'=>$_POST['other_expense'],
										'status'=>'',
										'history'=> "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
										'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
										'modify_time'=>date('Y-m-d H:i:s'),
										'create_id'=>$HTTP_SESSION_VARS['sess_user_name'],
										'create_time'=>date('Y-m-d H:i:s'),
										'education_expense'=>$_POST['education_expense'],
										'househelp_expense'=>$_POST['househelp_expense'],
										'fuel_expense'=>$_POST['fuel_expense'],
										'clothing_expense'=>$_POST['clothing_expense'],
										'med_expenditure'=>$_POST['med_expenditure'],
										'insurance_mortgage'=>$_POST['insurance_mortgage'],
										'total_monthly_expense'=>$_POST['total_monthly_expense'],
										'address'=>$_POST['address'],
										'house_type'=>$_POST['house_type'],
										'final_diagnosis'=>$_POST['final_diagnosis'],
										'duration_problem'=>$_POST['duration_problem'],
										'duration_treatment'=>$_POST['duration_treatment'],
										'treatment_plan'=>$_POST['treatment_plan'],
										'accessibility_problem'=>$_POST['accessibility_problem'],
										'info_agency'=>$_POST['info_agency'],
										'info_contact_no'=>$_POST['info_contact_no'],
										'remarks'=>$_POST['remarks'],
										'social_worker'=>$_POST['social_worker']
									);

			//$ok = $socserv->saveSocialPatientArray($data_patient);
			$family_data = array('family_name'=>$_POST['family_name'],
													 'family_age'=>$_POST['family_age'],
													 'family_status'=>$_POST['family_status'],
													 'family_relation'=>$_POST['family_relation'],
													 'family_educ'=>$_POST['family_educ'],
													 'family_occupation'=>$_POST['family_occupation'],
													 'family_income'=>$_POST['family_income']
											);

			#echo "<br><br>HELLLO = <br>";
			#print_r($family_data);
			$try = $socserv->saveFamilyComposition($mss_no, $family_data);
			//if ($ok && $socserv->saveSocServPatientArray($data)){
			if($try){
				echo " \n <script type=\"text/javascript\">alert(\"Saved sucessfully!\")</script>";
			}else{
				echo '<em class="warn"><font style="color:#FF0000"><b>'.$db->ErrorMsg().'<b></font></em>';
				#echo '<em class="warn"><font style="color:#FF0000"><b>'.$socserv->sql.'<b></font></em>';
			}
		break;
		case 'update':

			$data = array('mss_no'=>$mss_no,
										'encounter_nr'=>$encounter_nr,
										'informant_name'=>$_POST['informant_name'],
										'info_address'=>$_POST['info_address'],
										'educational_attain'=>$_POST['educational_attain'],
										'occupation'=>$_POST['occupation'],
										'employer'=>$_POST['employer'],
										'employer_address'=>$_POST['employer_address'],
										'source_income'=>$_POST['source_income'],
										'monthly_income'=>$_POST['monthly_income'],
										'per_capita_income'=>$_POST['per_capita_income'],
										'food_expense'=>$_POST['food_expense'],
										'ligth_expense'=>$_POST['ligth_expense'],
										'water_expense'=>$_POST['water_expense'],
										'transport_expense'=>$_POST['transport_expense'],
										'other_expense'=>$_POST['other_expense'],
										'status'=>'',
										'history'=> "Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
										'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
										'modify_time'=>date('Y-m-d H:i:s'),
										'education_expense'=>$_POST['education_expense'],
										'househelp_expense'=>$_POST['househelp_expense'],
										'fuel_expense'=>$_POST['fuel_expense'],
										'clothing_expense'=>$_POST['clothing_expense'],
										'med_expenditure'=>$_POST['med_expenditure'],
										'insurance_mortgage'=>$_POST['insurance_mortgage'],
										'total_monthly_expense'=>$_POST['total_monthly_expense'],
										'address'=>$_POST['address'],
										'house_type'=>$_POST['house_type'],
										'final_diagnosis'=>$_POST['final_diagnosis'],
										'duration_problem'=>$_POST['duration_problem'],
										'duration_treatment'=>$_POST['duration_treatment'],
										'treatment_plan'=>$_POST['treatment_plan'],
										'accessibility_problem'=>$_POST['accessibility_problem'],
										'info_agency'=>$_POST['info_agency'],
										'info_contact_no'=>$_POST['info_contact_no'],
										'remarks'=>$_POST['remarks']
									);


			if ($socserv->updateSocServPatientArray($mss_no, $encounter_nr, $data)){

				echo " \n <script type=\"text/javascript\">alert(\"Updated sucessfully!\")</script>";
			}else{
				echo '<em class="warn"><font style="color:#FF0000"><b>'.$db->ErrorMsg().'<b></font></em>';
			}
		break;
	}# end of switch statement
}

$socInfo = $socserv->getSocialServPatientEncounter($encounter_nr);
$education = $ic_obj->getEducationalAttainment();
$occupation = $socserv->getOccupation();
$relationship = $socserv->getRelation();

#---------setup options for Educational Attainment---------#
$listEducAttain=array();
	if(is_object($education)){
		while($educInfo = $education->FetchRow()){
				$listEducAttain[$educInfo['educ_attain_nr']] = $educInfo['educ_attain_name'];
		}
	}
#---------------end------------------#

#---------setup options for Occupation---------#
$listOccupation=array();
	if(is_object($occupation)){
		while($occuInfo = $occupation->FetchRow()){
				$listOccupation[$occuInfo['occupation_nr']] = $occuInfo['occupation_name'];
		}
	}
#---------------end------------------#

#---------setup options for Occupation---------#
$listRelation=array();
	if(is_object($relationship)){
		while($relInfo = $relationship->FetchRow()){
				$listRelation[$relInfo['id']] = $relInfo['child_relation'];
		}
	}
#---------------end------------------#

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Social Service Patient Profile</title>
<style type="text/css">
<!--
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
-->
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

	function trimString2(value){
		value = value.replace(/^\s+|\s+$/g,"");
		value = value.replace(/\s+/g," ");
	}/* end of function trimString */

	function blocking(objectName, flag){
//		alert("objectName = '"+objectName+"' \n flag = '"+flag+"'");
		if (document.layers) {
			document.layers[objectName].display = (flag) ? '' : 'none';
		} else if (document.all) {
			document.all[objectName].style.display = (flag) ? '' : 'none';
		} else if (document.getElementById) {
			document.getElementById(objectName).style.display = (flag) ? '' : 'none';
		}
	}/* end of function blocking*/



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
			thisTime.focus();
			return;
		}

		if (hour==0){
			 hour = 12;
			 $('selAMPM').value = "A.M.";
		}else	if((hour > 12)&&(hour < 24)){
			 hour -= 12;
			 $('selAMPM').value = "P.M.";
		}

		ftime =  hour + ":" + minute;

		if(!ftime.match(f1) && !ftime.match(f2)){
			thisTime.value = "";
			alert("Invalid time format.");
			seg_validTime=false;
			thisTime.focus();
		}else{
			thisTime.value = ftime;
			seg_validTime=true;
		}
	}// end of function setFormatTime

	//check if the object value exists in the array
	Array.prototype.in_array = function ( obj ) {
	var len = this.length;
	for ( var x = 0 ; x <= len ; x++ ) {
		if ( this[x] == obj ) return true;
	}
	return false;
	}

	//added by VAN
	//number only and decimal point is allowed
	function keyPressHandler(e){
		var unicode=e.charCode? e.charCode : e.keyCode
		//if (unicode>31 && (unicode<46 || unicode == 47 ||unicode>57)) //if not a number
		if (unicode>31 && (unicode<48 || unicode>57)) //if not a number
			return false //disable key press
	}


	function ucwords( str ) {
		// http://kevin.vanzonneveld.net
		// +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
		// +   improved by: _argos
		// *     example 1: ucwords('kevin van zonneveld');
		// *     returns 1: 'Kevin Van Zonneveld'
		// *     example 2: ucwords('HELLO WORLD');
		// *     returns 2: 'HELLO WORLD'

		return str.replace(/^(.)|\s(.)/g, function ( $1 ) { return $1.toUpperCase ( ); } );
	}

	function preSet(){

	}

	//-----------------------
</script>
</head>

<body onload="preSet();">
<?php echo $errorMsg; ?>

<form id="livebirth_certificate" name="livebirth_certificate" method="post" action="" onsubmit="return chckForm(this);">
<div align="center">
<table width="100%" border="1" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td align="center" bgcolor="#F9FFFB">

				<span class="style2"><?=$row['hosp_country']?></span><BR />
				<span class="style6"><?=$row['hosp_agency']?></span><BR />
				<span class="style15"><?=$row['hosp_name']?></span>    </td>
	</tr>

	<tr>
		<td>&nbsp;</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
	</tr>

	<tr>
		<td align="center" bordercolor="#FFCCCC" bordercolordark="#000000">
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td width="20%" bgcolor="#A0C2DA" class="style9" align="center">Date of Interview
					<?
						global $db;

						$pid = $encInfo['pid'];
						if($encInfo['encounter_type']=='2'){
							$sql_interview = "SELECT grant_dte FROM seg_charity_grants_pid WHERE pid = '".$pid."'";
						}else if($encInfo['encounter_type']=='1' || $encInfo['encounter_type']=='3' || $encInfo['encounter_type']=='4'){
							$sql_interview = "SELECT grant_dte FROM seg_charity_grants WHERE encounter_nr = '".$encounter_nr."'";
						}

						$result_interview = $db->Execute($sql_interview);
						$row_interview = $result_interview->FetchRow();
						$date_of_interview = date("F j, Y", strtotime($row_interview['grant_dte']));
					?>
					<input type="text" name="date_of_interview" id="date_of_interview" size="20" value="<?=$date_of_interview?>">
					</td>
					<?#print_r($encInfo);
					?>
					<td width="29%" bgcolor="#A0C2DA" class="style9" align="center">Date of Admission / Consultation
					<?
						if($encInfo['encounter_type']=='1' || $encInfo['encounter_type']=='2'){
							$admission_dt = date("F j, Y", strtotime($encInfo['encounter_date']));
						}else if($encInfo['encounter_type']=='3' || $encInfo['encounter_type']=='4'){
							$admission_dt = date("F j, Y", strtotime($encInfo['admission_dt']));
						}
					?>
					<input type="text" name="admission_dt" id="admission_dt" size="20" value="<?=$admission_dt?>" readonly="readonly">
					</td>
					<td width="1%" bgcolor="#A0C2DA" class="style9" align="left">Ward<br>
					ER<br>
					OPD<br>
					</td>
					<td width="20%" bgcolor="#A0C2DA" class="style9" align="center">
					<?
						//global $db;

						if($encInfo['encounter_type']=='1'){
							$ward = "";
							$er =	$dep_obj->FormalName($encInfo['current_dept_nr']);
							$opd = "";
						}else if($encInfo['encounter_type']=='2'){
							$ward = "";
							$er = "";
							$opd = $dep_obj->FormalName($encInfo['current_dept_nr']);
						}else if($encInfo['encounter_type']=='3' || $encInfo['encounter_type']=='4'){
							$room_nr = $encInfo['current_room_nr'];
							$sql_ward="SELECT cw.name FROM care_ward AS cw
												INNER JOIN care_room AS cr ON cr.ward_nr=cw.nr
												WHERE room_nr='".$room_nr."'";
							$result_ward = $db->Execute($sql_ward);
							$row_ward = $result_ward->FetchRow();

							$ward = $row_ward['name'];
							$er = "";
							$opd = "";
						}
					?>
					<input type="text" name="ward" id="ward" size="20" value="<?=$ward?>" readonly="readonly"><br>
					<input type="text" name="er" id="er" size="20" value="<?=$er?>" readonly="readonly"><br>
					<input type="text" name="opd" id="opd" size="20" value="<?=$opd?>" readonly="readonly">
					</td>
					<td width="15%" bgcolor="#A0C2DA" class="style9" align="center">Hospital Number
					<input type="text" name="hosp_num" id="hosp_num" size="15" value="<?=$encounter_nr?>" readonly="readonly">
					</td>
					<td width="15%" bgcolor="#A0C2DA" class="style9" align="center">MSS Number
					<input type="text" name="mss_num" id="mss_num" size="15" value="<?=$encInfo['mss_no'];?>" readonly="readonly">
					</td>
				</tr>
				<tr bgcolor="#98D228">
					<td align="left" class="style9" colspan="6">&nbsp;&nbsp;I. DEMOGRAPHIC DATA:</td>
				</tr>
				<tr>
					<td colspan="6" bgcolor="#98D228">
						<table width="100%" border="0" cellspacing="1" cellpadding="1">
							<tr>
								<td align="center" bgcolor="#F9FFFB">
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td height="36" colspan="3" valign="top" bgcolor="#98D228">
													<table width="100%" border="0" cellpadding="0" cellspacing="1">
														<tr>
															<td width="*" colspan="2" align="left" valign="top" bgcolor="#CFEC9B" class="style9">&nbsp;&nbsp;PATIENT'S NAME:
															</td>
															<td width="10%" align="left" valign="top" bgcolor="#CFEC9B" class="style9">&nbsp;&nbsp;AGE:</td>
															<td colspan="2" align="center" valign="top" bgcolor="#CFEC9B" class="style9">GENDER</td>
															<td colspan="5" align="center" valign="top" bgcolor="#CFEC9B" class="style9">CIVIL STATUS</td>
														</tr>

														<tr>
															<td width="*" colspan="2" align="left" valign="top" bgcolor="#CFEC9B" class="style9">
															 <table border="1">
																<tr>
																<td width="10%" align="center" bgcolor="#F9FFFB" class="style2" height="50">
																SURNAME
																<input type="text" name="surname" id="surname" size="25" value="<?=$encInfo['name_last']?>" readonly="readonly">
																</td>
																<td width="10%" align="center" bgcolor="#F9FFFB" class="style2" height="45">
																FIRST
																<input type="text" name="firstname" id="firstname" size="25" value="<?=$encInfo['name_first']?>" readonly="readonly">
																</td>
																<td width="10%" align="center" bgcolor="#F9FFFB" class="style2" height="50">
																MIDDLE
																<input type="text" name="middlename" id="middlename" size="25" value="<?=$encInfo['name_middle']?>" readonly="readonly">
																</td>
																<td width="10%" align="center" bgcolor="#F9FFFB" class="style2">
																<input type="text" align="center" name="age" id="age" size="10" value="<?=$encInfo['age'].' old'?>"readonly="readonly" >
																</td>
															</tr>
														 </table>
														</td>

															<td width="5%" align="center" bgcolor="#F9FFFB" class="style2">Female
															<?
																if($encInfo['sex']=="m"){
																	$is_male = "checked";
																	$is_female = "";
																}else{
																	$is_male = "";
																	$is_female = "checked";
																}
															?>
															<input type="radio" name="gender" id="gender" disabled="disabled" value="f" <? echo $is_female; ?>>
															</td>
															<td width="5%" align="center" bgcolor="#F9FFFB" class="style2">Male<br>
															<input type="radio" name="gender" id="gender" disabled="disabled" value="m" <? echo $is_male; ?>>
															</td>
															<td width="4%" align="center" bgcolor="#F9FFFB" class="style2">S<br>
															<?
																if($encInfo['civil_status']=='single'){
																	$is_single="checked";
																	$is_married="";
																	$is_separated="";
																	$is_widowed="";
																	$is_da="";
																}else if($encInfo['civil_status']=='married'){
																	$is_single="";
																	$is_married="checked";
																	$is_separated="";
																	$is_widowed="";
																	$is_da="";
																}else if($encInfo['civil_status']=='separated'){
																	$is_single="";
																	$is_married="";
																	$is_separated="checked";
																	$is_widowed="";
																	$is_da="";
																}else if($encInfo['civil_status']=='widowed'){
																	$is_single="";
																	$is_married="";
																	$is_separated="";
																	$is_widowed="checked";
																	$is_da="";
																}else if($encInfo['civil_status']=='widowed'){ //question.....
																	$is_single="";
																	$is_married="";
																	$is_separated="";
																	$is_widowed="";
																	$is_da="checked";
																}else{
																	$is_single="";
																	$is_married="";
																	$is_separated="";
																	$is_widowed="";
																	$is_da="";
																}
															?>
															<input type="radio" name="civil_status" id="civil_status" value="single" <? echo $is_single;?> disabled="disabled">
															</td>
															<td width="4%" align="center" bgcolor="#F9FFFB" class="style2">M<br>
															<input type="radio" name="civil_status" id="civil_status" value="married" <? echo $is_married; ?> disabled="disabled">
															</td>

															<td width="4%" align="center" bgcolor="#F9FFFB" class="style2">SEP<br>
															<input type="radio" name="civil_status" id="civil_status" value="separated" <? echo $is_separated; ?> disabled="disabled">
															</td>
															<td width="4%" align="center" bgcolor="#F9FFFB" class="style2">W<br>
															<input type="radio" name="civil_status" id="civil_status" value="widowed" <? echo $is_widowed; ?> disabled="disabled">
															</td>
															 <td width="4%" align="center" bgcolor="#F9FFFB" class="style2">D/A<br>
															<input type="radio" name="civil_status" id="civil_status" value="da" <? echo $is_da; ?>  disabled="disabled">
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
</div>

</table>
<br>
<div align="center">
	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td  bgcolor="#A0C2DA">
				<center>
	<?php
			if (!$socInfo || empty($socInfo)){
				echo '			<input type="hidden" name="mode" id="mode" value="save">'."\n";
				echo '			<input type="submit" name="Submit" value="Save">'."\n";
			}else{
				echo '			<input type="hidden" name="mode" id="mode" value="update">'."\n";
				echo '			<input type="button" name="Print" value="Print" onClick="printBirthCert(\''.$pid.'\')">'."\n &nbsp; &nbsp;";
				#added by VAN 05-26-08
				echo '			<input type="submit" name="Submit" value="Update">'."\n";
			}
			echo '			<input type="hidden" name="pid" id="pid" value="'.$pid.'">'."\n";
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
