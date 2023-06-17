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

$errorMsg='';

#------------------------------

if (isset($_POST['mode'])){
	switch($_POST['mode']) {
		case 'save':

			$pid = $encInfo['pid'];

			$check_mss = $socserv->getSocServPatient($pid);

			$is_exists = $socserv->count;


			if (!($is_exists)){
				$mss_no = $socserv->getLastMSSnr(date("Y-m-d"),"'".$GLOBAL_CONFIG['mss_nr_init']."'");

				$data_patient = array('pid'=>$pid,
														'mss_no'=>$mss_no,
														'status'=>'',
														'history'=>"Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
														'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
														'modify_time'=>date('Y-m-d H:i:s'),
														'create_id'=>$HTTP_SESSION_VARS['sess_user_name'],
														'create_time'=>date('Y-m-d H:i:s')
											);

				$ok = $socserv->saveSocialPatientArray($data_patient);
			}else{
				$mss_no = $check_mss['mss_no'];
				$ok = 1;
			}

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

			if ($ok && $socserv->saveSocServPatientArray($data)){
				echo " \n <script type=\"text/javascript\">alert(\"Saved sucessfully!\")</script>";
			}else{
				#$errorMsg='<font style="color:#FF0000">'.$obj_birthCert->getErrorMsg().'</font>';
				echo '<em class="warn"><font style="color:#FF0000"><b>'.$db->ErrorMsg().'<b></font></em>';
				#echo '<em class="warn"><font style="color:#FF0000"><b>'.$socserv->sql.'<b></font></em>';
			}
		break;
		case 'update':

			$pid = $encInfo['pid'];
			$check_mss = $socserv->getSocServPatient($pid);
			$mss_no = $check_mss['mss_no'];

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
										'remarks'=>$_POST['remarks'],
										'social_worker'=>$_POST['social_worker'],
										'other_source_income'=>$_POST['other_source_income']
									);


			if ($socserv->updateSocServPatientArray($mss_no, $encounter_nr, $data)){
				echo " \n <script type=\"text/javascript\">alert(\"Updated sucessfully!\")</script>";
			}else{
				#$errorMsg='<font style="color:#FF0000"> error '.$obj_birthCert->getErrorMsg().'</font>';
				echo '<em class="warn"><font style="color:#FF0000"><b>'.$db->ErrorMsg().'<b></font></em>';
			}
		break;
	}# end of switch statement
}

$socInfo = $socserv->getSocialServPatientEncounter($encounter_nr);

if(!$socInfo || empty($socInfo) || $socInfo==''){
	$socInfo = 1;
}
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
/*
	echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
*/
#edited by VAN 02-16-08
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

function key_check(e, value){
			if((e.keyCode>=48 && e.keyCode<=57) || (e.keyCode==8) || ((e.keyCode==110)||(e.keyCode==190)) || (e.keyCode>=96 && e.keyCode<=105)){
			return true;
			}else
			return false;
		}

	//Added by Cherry 08-26-10
	function formatValue(num,dec){
	var nf = new NumberFormat(num.value);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	num.value = nf.toFormatted();
}

	function computeCapita(){
	var percapita;
	var nodep = document.getElementById('nr_dependents').value;
	var mincome = document.getElementById('monthly_income').value;
	if (((mincome)&&(mincome!=0)) && ((nodep)&&(nodep)!=0)){
		percapita = parseInt(mincome) / parseInt(nodep);
		document.getElementById('per_capita_income').value = percapita.toFixed(2);
		//document.getElementById('m_cincome').value = percapita;
	}else{
		if ((mincome)&&(mincome!=0)){
			document.getElementById('per_capita_income').value = mincome.toFixed(2);
			//document.getElementById('m_cincome').value = mincome;
		}else{
			document.getElementById('per_capita_income').value = "";
			//document.getElementById('m_cincome').value = 0;
		}
	}
}

	function computeTotal(){
	//	alert('cscsdcds');
	var total=0, food=0, light = 0, fuel = 0, education=0, househelp=0;
	var water=0, transport=0, other=0, clothing=0, med=0, insurance=0;
	//alert($F('hauz_lot2'));

	if ($F('fuel_expense'))
		fuel = parseInt($F('fuel_expense'));

	if ($F('education_expense'))
		education = parseInt($F('education_expense'));

	if ($F('househelp_expense'))
		househelp = parseInt($F('househelp_expense'));

	if ($F('clothing_expense'))
		clothing = parseInt($F('clothing_expense'));

	if ($F('med_expenditure'))
		med = parseInt($F('med_expenditure'));

	if ($F('insurance_mortgage'))
		insurance = parseInt($F('insurance_mortgage'));

	if ($F('food_expense'))
		food = parseInt($F('food_expense'));

	if (($F('ligth_expense')))
		light = parseInt($F('ligth_expense'));

	if (($F('water_expense')))
		water = parseInt($F('water_expense'));

	if ($F('transport_expense'))
		transport = parseInt($F('transport_expense'));

	if ($F('other_expense'))
		other = parseInt($F('other_expense'));

	total = food + light + water + transport + other + insurance + fuel + education + househelp + clothing + med;

	document.getElementById('total_monthly_expense').value = total.toFixed(2);
}
	//--------------

	function preSet(){

	}
	//number only and decimal point is allowed
	function keyPressHandler(e){
		var unicode=e.charCode? e.charCode : e.keyCode
		//if (unicode>31 && (unicode<46 || unicode == 47 ||unicode>57)) //if not a number
		if (unicode>31 && (unicode<48 || unicode>57)) //if not a number
			return false //disable key press
	}
	//-------------------------

	function printMSWDForm(pid, encounter_nr){
			window.open("seg-socserv-mswd_form3.php?pid="+pid+"&encounter_nr="+encounter_nr,"MSWD Form 3","modal, width=900,height=700,menubar=no,resizable=yes,scrollbars=no");
		//}

	}/* end of function printBirthCert */

	function chckForm(a){
		var d = document.social_service;

		if((d.monthly_income.value=='0.00') || (d.food_expense.value=='0.00') || (d.ligth_expense.value=='0.00') || (d.water_expense.value=='0.00') || (d.transport_expense.value=='0.00') || (d.house_type.value=='0.00')){
			alert('Please fill-in all the fields');
			$('ligth_expense').focus();
			return false;
		}else{
			return true;
		}
	}/* end of function chckForm */


	function stristr( haystack, needle, bool ) {
		// http://kevin.vanzonneveld.net
		// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// *     example 1: stristr('Kevin van Zonneveld', 'Van');
		// *     returns 1: 'van Zonneveld'
		// *     example 2: stristr('Kevin van Zonneveld', 'VAN', true);
		// *     returns 2: 'Kevin '

		var pos = 0;

		pos = haystack.toLowerCase().indexOf( needle.toLowerCase() );
		if( pos == -1 ){
				return false;
		} else{
				if( bool ){
						return haystack.substr( 0, pos );
				} else{
						return haystack.slice( pos );
				}
		}
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
	//-----------------------
</script>
</head>

<body onload="preSet();">
<?php echo $errorMsg; ?>
<form id="social_service" name="social_service" method="post" action="" onsubmit="return chckForm(this);">
<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
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
				 <div align="center">

</div>

		<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<!--<td width="3%" bgcolor="#98D228" ><img src="images/brdr_03.gif" width="31" height="26" /></td>
					<td width="94%" bgcolor="#98D228"><span class="style5">CHILD</span></td>
					<td width="3%" bgcolor="#98D228">&nbsp;</td>-->
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
						if($result_interview){
							$row_interview = $result_interview->FetchRow();
							if($row_interview['grant_dte']){
								$date_of_interview = date("F j, Y", strtotime($row_interview['grant_dte']));
							}
						}else{
							$date_of_interview = "";
						}

					?>
					<input type="text" name="date_of_interview" id="date_of_interview" size="20" value="<?=$date_of_interview?>">
					</td>
					<?//print_r($encInfo);
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
					<!--
					<td width="20%" bgcolor="#A0C2DA" class="style9" align="center">Ward&nbsp;&nbsp;&nbsp;
					<input type="text" name="ward" id="ward" size="20"><br>ER&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="text" name="er" id="er" size="20"><br>OPD&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="text" name="opd" id="opd" size="20">
					</td>-->
					<td width="15%" bgcolor="#A0C2DA" class="style9" align="center">Hospital Number
					<input type="text" name="hosp_num" id="hosp_num" size="15" value="<?=$encInfo['pid']?>" readonly="readonly">
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
								<td height="468" align="center" bgcolor="#F9FFFB"> <br />
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td height="36" colspan="3" valign="top" bgcolor="#98D228">
													<table width="100%" border="0" cellpadding="0" cellspacing="1">
														<tr>
															<td colspan="3" align="left" valign="top" bgcolor="#CFEC9B" class="style9">&nbsp;&nbsp;PATIENT'S NAME:
															</td>
															<td width="10%" align="left" valign="top" bgcolor="#CFEC9B" class="style9">&nbsp;&nbsp;AGE:</td>
															<td colspan="2" align="center" valign="top" bgcolor="#CFEC9B" class="style9">GENDER</td>
															<td colspan="5" align="center" valign="top" bgcolor="#CFEC9B" class="style9">CIVIL STATUS</td>
														</tr>

														<tr>
															<td width="20%" align="center" bgcolor="#F9FFFB" class="style2" height="50">
															SURNAME
															<input type="text" name="surname" id="surname" size="25" value="<?=$encInfo['name_last']?>" readonly="readonly">
															</td>
															<td width="20%" align="center" bgcolor="#F9FFFB" class="style2" height="45">
															FIRST
															<input type="text" name="firstname" id="firstname" size="25" value="<?=$encInfo['name_first']?>" readonly="readonly">
															</td>
															<td width="20%" align="center" bgcolor="#F9FFFB" class="style2" height="50">
															MIDDLE
															<input type="text" name="middlename" id="middlename" size="25" value="<?=$encInfo['name_middle']?>" readonly="readonly">
															</td>
															<td width="10%" align="center" bgcolor="#F9FFFB" class="style2">
															<input type="text" align="center" name="age" id="age" size="10" value="<?=$encInfo['age'].' old'?>"readonly="readonly" >
															</td>
															<td width="5%" align="center" bgcolor="#F9FFFB" class="style2">Female
															<?
																if($encInfo['sex']=="m"){
																	$is_male = "checked";
																}else{
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
																}else if($encInfo['civil_status']=='married'){
																	$is_married="checked";
																}else if($encInfo['civil_status']=='separated'){
																	$is_separated="checked";
																}else if($encInfo['civil_status']=='widowed'){
																	$is_widowed="checked";
																}else if($encInfo['civil_status']=='divorced' || $encInfo['civil_status']=='annulled'){
																	$is_da="checked";
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

											<!--Address, religion, date of birth, place of birth -->
											<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td height="36" colspan="3" valign="top" bgcolor="#98D228">
													<table width="100%" border="0" cellpadding="0" cellspacing="1">
														<tr>
															<td colspan="2" align="center" valign="top" bgcolor="#CFEC9B" class="style9">ADDRESS
															</td>
															<td width="20%" align="center" valign="top" bgcolor="#CFEC9B" class="style9">RELIGION</td>
															<td width="15%" align="center" valign="top" bgcolor="#CFEC9B" class="style9">DATE OF BIRTH</td>
															<td width="15%" align="center" valign="top" bgcolor="#CFEC9B" class="style9">PLACE OF BIRTH</td>
														</tr>

														<tr>
															<td width="25%" align="left" bgcolor="#F9FFFB" class="style2" valign="top" height="90">&nbsp;Permanent<br>&nbsp;&nbsp;&nbsp;
															<?
											if (trim($encInfo['street_name'])){
													if (trim($encInfo["brgy_name"])!="NOT PROVIDED")
														$street_name = trim($encInfo['street_name']).", ";
													else
														$street_name = trim($encInfo['street_name']).", ";
											}else{
													$street_name = "";
											}

											if ((!(trim($encInfo["brgy_name"]))) || (trim($encInfo["brgy_name"])=="NOT PROVIDED"))
												$brgy_name = "";
											else
												$brgy_name  = trim($encInfo["brgy_name"]).", ";

											if ((!(trim($encInfo["mun_name"]))) || (trim($encInfo["mun_name"])=="NOT PROVIDED"))
												$mun_name = "";
											else{
												if ($brgy_name)
													$mun_name = trim($encInfo["mun_name"]);
												else
													$mun_name = trim($encInfo["mun_name"]);
											}

											if ((!(trim($encInfo["prov_name"]))) || (trim($encInfo["prov_name"])=="NOT PROVIDED"))
												$prov_name = "";
											else
												$prov_name = trim($encInfo["prov_name"]);

											if(stristr(trim($encInfo["mun_name"]), 'city') === FALSE){
												if ((!empty($encInfo["mun_name"]))&&(!empty($encInfo["prov_name"]))){
													if ($prov_name!="NOT PROVIDED")
														$prov_name = ", ".trim($prov_name);
													else
														$prov_name = trim($prov_name);
												}else{
													#$province = trim($prov_name);
													$prov_name = "";
												}
											}else
												$prov_name = "";

											$permanent_address = trim($street_name).trim($brgy_name).trim($mun_name).trim($prov_name);
															?>
															<textarea name="permanent_address" id="permanent_address" cols="25" rows="3" readonly="readonly"><? echo $permanent_address; ?></textarea>
															</td>
															<td width="25%" align="left" bgcolor="#F9FFFB" class="style2" valign="top">&nbsp;Temporary<br>&nbsp;&nbsp;&nbsp;
															<textarea name="address" id="address" cols="25" rows="3"><? echo $socInfo['address']; ?></textarea>
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="religion" id="religion" size="25" value="<?=$encInfo['religion']?>" readonly="readonly">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<?
																$date_of_birth = date("F j, Y", strtotime($encInfo['date_birth']));
															?>
															<input type="text" name="date_birth" id="date_birth" size="20" value="<? echo $date_of_birth; ?>" readonly="readonly">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<textarea name="place_birth" id="place_birth" cols="16" rows="3"><? echo $encInfo['place_birth']; ?></textarea>
															</td>
														</tr>

													</table>
												</td>
											</tr>
											</table>

											<!--Educational attainment, employer, monthly income-->
											<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td height="36" colspan="3" valign="top" bgcolor="#98D228">
													<table width="100%" border="0" cellpadding="0" cellspacing="1">
														<tr>
															<td width="25" align="center" valign="top" bgcolor="#CFEC9B" class="style9">EDUCATIONAL ATTAINMENT
															</td>
															<td width="50%" align="center" valign="top" bgcolor="#CFEC9B" class="style9">EMPLOYER</td>
															<td width="25%" align="center" valign="top" bgcolor="#CFEC9B" class="style9">MONTHLY INCOME</td>
														</tr>

														<tr>
															<td width="25%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle" height="30">
															<!--<input type="text" name="educ_attain" id="educ_attain" size="35">-->
															<select name="educational_attain" id="educational_attain">
															<?php
																if (empty($socInfo['educational_attain']))
																	$socInfo['educational_attain'] = 0;
																				//print_r($education);
																foreach($listEducAttain as $key=>$value){
																	if ($socInfo['educational_attain']==$key){
																		echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
																	}else{
																		echo "				<option value='".$key."'>".$value."</option> \n";
																	}
																}
															?>
															</select>
															</td>
															<td width="25%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="employer" id="employer" size="80" value="<?=$socInfo['employer']?>">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="monthly_income" id="monthly_income" size="35" value="<?=number_format($socInfo['monthly_income'],2,'.','')?>" onchange="this.value = Number(this.value).toFixed(2)" onkeydown="return key_check(event, this.value)">
															</td>

														</tr>

													</table>
												</td>
											</tr>
											</table>

											<!-- Occupation, address, per capita income -->
											<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td height="36" colspan="3" valign="top" bgcolor="#98D228">
													<table width="100%" border="0" cellpadding="0" cellspacing="1">
														<tr>
															<td width="25" align="center" valign="top" bgcolor="#CFEC9B" class="style9">OCCUPATION
															</td>
															<td width="50%" align="center" valign="top" bgcolor="#CFEC9B" class="style9">ADDRESS</td>
															<td width="25%" align="center" valign="top" bgcolor="#CFEC9B" class="style9">PER CAPITA INCOME</td>
														</tr>

														<tr>
															<td width="25%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle" height="30">
															<!--<input type="text" name="occupation" id="occupation" size="35" value="<?=$socInfo['occupation']?>">-->
															<select name="occupation" id="occupation">
															<?php


																foreach($listOccupation as $key=>$value){
																	if ($socInfo['occupation']==$key){
																		echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
																	}else{
																		echo "				<option value='".$key."'>".$value."</option> \n";
																	}
																}
															?>
															</select>
															</td>
															<td width="25%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="employer_address" id="employer_address" size="80" value="<?=$socInfo['employer_address']?>">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="per_capita_income" id="per_capita_income" size="35" value="<?=number_format($socInfo['per_capita_income'],2,'.','')?>" onchange="this.value = Number(this.value).toFixed(2)" onkeydown="return key_check(event, this.value)">
															</td>

														</tr>

													</table>
												</td>
											</tr>
											</table>

											<!-- Number of Children, Number of Dependents -->
											<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td height="36" colspan="3" valign="middle" bgcolor="#F9FFFB">
													<table width="100%" border="0" cellpadding="0" cellspacing="1">
														<tr>
															<td width="50%" align="center" valign="top" bgcolor="#CFEC9B" class="style9">NUMBER OF CHILDREN:
															&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															<input type="text" id="nr_children" name="nr_children" size="20" value="<?=$socInfo['nr_children']?>" onchange="this.value = Number(this.value); computeCapita();" onkeydown="return key_check(event, this.value)">
															</td>
															<td width="50%" align="center" valign="top" bgcolor="#CFEC9B" class="style9">NUMBER OF DEPENDENTS:
															&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															<input type="text" id="nr_dependents" name="nr_dependents" size="20" value="<?=$socInfo['nr_dependents']?>" onchange="this.value = Number(this.value); computeCapita();" onkeydown="return key_check(event, this.value)">
															</td>
														</tr>
													</table>
												</td>
											</tr>
											</table>

											<!-- Philhealth, govt, pri, ipm, non-pay, ofw, others, mss_classification -->
											<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td height="36" colspan="3" valign="top" bgcolor="#98D228">
													<table width="100%" border="0" cellpadding="0" cellspacing="1">
														<tr>
															<td width="25%" align="left" valign="top" bgcolor="#CFEC9B" class="style9" valign="middle">PHILHEALTH
															</td>
															<td width="5%" align="center" bgcolor="#CFEC9B" class="style9" valign="middle">GOV'T</td>
															<td width="5%" align="center" bgcolor="#CFEC9B" class="style9" valign="middle">PRI</td>
															<td width="5%" align="center" bgcolor="#CFEC9B" class="style9" valign="middle">IPM</td>
															<td width="8%" align="center" bgcolor="#CFEC9B" class="style9" valign="middle">NON PAY</td>
															<td width="5%" align="center" bgcolor="#CFEC9B" class="style9" valign="middle">OFW</td>
															<td width="5%" align="center" bgcolor="#CFEC9B" class="style9" valign="middle">OTHERS</td>
															<td colspan="6" align="center" valign="middle" bgcolor="#CFEC9B" class="style9">MSS CLASSIFICATION</td>
														</tr>

														<tr>
															<td width="25%" align="left" bgcolor="#F9FFFB" class="style2" valign="middle" height="30">
															&nbsp;&nbsp;MEMBER
															<?php
																if($encInfo['encounter_type']==2){
																	$sql_class = "SELECT cgp.discountid, cgp.id_number, sd.parentid, sem.memcategory_id, cpi.is_principal
FROM seg_charity_grants_pid AS cgp
LEFT JOIN care_encounter AS e ON e.pid = cgp.pid
LEFT JOIN seg_discount AS sd ON sd.discountid = cgp.discountid
LEFT JOIN care_person_insurance AS cpi ON cpi.pid = cgp.pid
LEFT JOIN seg_encounter_memcategory AS sem ON sem.encounter_nr = e.encounter_nr
WHERE cgp.pid='".$pid."'
ORDER BY grant_dte DESC;";
																}else if($encInfo['encounter_type']== 1 || $encInfo['encounter_type']==3 || $encInfo['encounter_type']==4){
																	$sql_class = "SELECT cg.discountid, cg.id_number, sd.parentid, sem.memcategory_id, cpi.is_principal
FROM seg_charity_grants AS cg
LEFT JOIN care_encounter AS e ON e.encounter_nr = cg.encounter_nr
LEFT JOIN seg_discount AS sd ON sd.discountid = cg.discountid
LEFT JOIN care_person_insurance AS cpi ON cpi.pid = e.pid
LEFT JOIN seg_encounter_memcategory AS sem ON sem.encounter_nr = cg.encounter_nr
WHERE cg.encounter_nr='".$encounter_nr."'
ORDER BY cg.grant_dte DESC;";
																}

																$result_class = $db->Execute($sql_class);
																if($result_class){
																	$row_class = $result_class->FetchRow();
																	if($row_class['is_principal']==1 && $row_class['memcategory_id']=='1'){
																		$member_govt = "checked";
																	}else if($row_class['is_principal']==1 && $row_class['memcategory_id']=='2'){
																		$member_pri = "checked";
																	}else if($row_class['is_principal']==1 && $row_class['memcategory_id']=='3'){
																		$member_ofw = "checked";
																	}else if($row_class['is_principal']==1 && $row_class['memcategory_id']=='4'){
																		$member_ipm = "checked";
																	}else if($row_class['is_principal']==1 && $row_class['memcategory_id']=='5'){
																		$member_others = "checked";
																	}else if($row_class['is_principal']==1 && $row_class['memcategory_id']=='6'){
																		$member_non = "checked";
																	}

																	if($row_class['is_principal']==0 && $row_class['memcategory_id']=='1'){
																		$dependent_govt = "checked";
																	}else if($row_class['is_principal']==0 && $row_class['memcategory_id']=='2'){
																		$dependent_pri = "checked";
																	}else if($row_class['is_principal']==0 && $row_class['memcategory_id']=='3'){
																		$dependent_ofw = "checked";
																	}else if($row_class['is_principal']==0 && $row_class['memcategory_id']=='4'){
																		$dependent_ipm = "checked";
																	}else if($row_class['is_principal']==0 && $row_class['memcategory_id']=='5'){
																		$dependent_others = "checked";
																	}else if($row_class['is_principal']==0 && $row_class['memcategory_id']=='6'){
																		$dependent_non = "checked";
																	}


																	if($row_class['discountid']=='A' || $row_class['parentid']=='A'){
																		$class_a = "checked";
																	}else if($row_class['discountid']=='C1' || $row_class['parentid']=='C1'){
																		$class_c1 = "checked";
																	}else if($row_class['discountid']=='C3' || $row_class['parentid']=='C3'){
																		$class_c3 = "checked";
																	}else if($row_class['discountid']=='B' || $row_class['parentid']=='B'){
																		$class_b = "checked";
																	}else if($row_class['discountid']=='C2' || $row_class['parentid']=='C2'){
																		$class_c2 = "checked";
																	}else if($row_class['discountid']=='D' || $row_class['parentid']=='D'){
																		$class_d = "checked";
																	}

																	if($row_class['discountid']=='SC')
																		$senior = $row_class['id_number'];
																	else if($row_class['discountid']=='Brgy')
																		$brgy = $row_class['id_number'];
																	else if($row_class['discountid']=='BHW')
																		$bhw = $row_class['id_number'];
																	else if($row_class['discountid']=='PWD')
																		$pwd = $row_class['id_number'];
																	else if($row_class['discountid']=='Indi')
																		$indi = $row_class['id_number'];
																	else if($row_class['discountid']=='Ins')
																		$ins = $row_class['id_number'];
																	else if($row_class['discountid']=='OT')
																		$other = $row_class['id_number'];
																}

															?>
															</td>
															<td width="5%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="member" id="member" value="1" <? echo $member_govt;?>>
															</td>
															<td width="5%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="member" id="member" value="2" <? echo $member_pri;?>>
															</td>
															<td width="5%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="member" id="member" value="3" <? echo $member_ipm;?>>
															</td>
															<td width="5%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="member" id="member" value="4" <? echo $member_non;?>>
															</td>
															<td width="5%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="member" id="member" value="5" <?echo $member_ofw;?>>
															</td>
															<td width="5%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="member" id="member" value="6" <? echo $member_others;?>>
															</td>
															<td width="10%" align="left" bgcolor="#F9FFFB" class="style2" valign="middle">&nbsp;&nbsp;A</td>
															<td width="4%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="mss_class" id="mss_class" value="a" <? echo $class_a; ?>>
															</td>
															<td width="10%" align="left" bgcolor="#F9FFFB" class="style2" valign="middle">&nbsp;&nbsp;C1</td>
															<td width="4%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="mss_class" id="mss_class" value="c1" <? echo $class_c1; ?>>
															</td>
															<td width="10%" align="left" bgcolor="#F9FFFB" class="style2" valign="middle">&nbsp;&nbsp;C3</td>
															<td width="4%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="mss_class" id="mss_class" value="c3" <? echo $class_c3 ?>>
															</td>
														</tr>

														<tr>
															<td width="25%" align="left" bgcolor="#F9FFFB" class="style2" valign="middle" height="30">
															&nbsp;&nbsp;DEPENDENT
															</td>
															<td width="5%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="dependent" id="dependent" value="1" <? echo $dependent_govt; ?>>
															</td>
															<td width="5%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="dependent" id="dependent" value="2" <? echo $dependent_pri; ?>>
															</td>
															<td width="5%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="dependent" id="dependent" value="3" <? echo $dependent_ipm; ?>>
															</td>
															<td width="5%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="dependent" id="dependent" value="4" <? echo $dependent_non; ?>>
															</td>
															<td width="5%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="dependent" id="dependent" value="5" <? echo $dependent_ofw; ?>>
															</td>
															<td width="5%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="dependent" id="dependent" value="6" <? echo $dependent_others ?>>
															</td>
															<td width="10%" align="left" bgcolor="#F9FFFB" class="style2" valign="middle">&nbsp;&nbsp;B</td>
															<td width="4%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="mss_class" id="mss_class" value="b" <? echo $class_b; ?>>
															</td>
															<td width="10%" align="left" bgcolor="#F9FFFB" class="style2" valign="middle">&nbsp;&nbsp;C2</td>
															<td width="4%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="mss_class" id="mss_class" value="c2" <? echo $class_c2; ?>>
															</td>
															<td width="10%" align="left" bgcolor="#F9FFFB" class="style2" valign="middle">&nbsp;&nbsp;D</td>
															<td width="4%" align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="radio" name="mss_class" id="mss_class" value="d" <? echo $class_d; ?>>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											</table>

										<!-- other sectorial membership -->
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td height="36" colspan="3" valign="top" bgcolor="#98D228">
													<table width="100%" border="0" cellpadding="0" cellspacing="1">
														<tr>
															<td colspan="8" align="center" valign="top" bgcolor="#CFEC9B" class="style9">OTHER SECTORIAL MEMBERSHIP
															</td>

														</tr>

														<tr>
															<td width="15%" align="center" bgcolor="#F9FFFB" class="style2" valign="top" height="40">SENIOR CITIZEN<br>
															<input type="text" name="sc" id="sc" size="15" <?=$senior?>>
															</td>
															<td width="15%" align="center" bgcolor="#F9FFFB" class="style2" valign="top">BRGY OFFICIAL<br>
															<input type="text" name="brgy_official" id="brgy_official" size="15" <?=$brgy?>>
															</td>
															<td width="10%" align="center" bgcolor="#F9FFFB" class="style2" valign="top">BHW<br>
															<input type="text" name="bhw" id="bhw" size="10" <?=$bhw?>>
															</td>
															<td width="10%" align="center" bgcolor="#F9FFFB" class="style2" valign="top">PWD<br>
															<input type="text" name="pwd" id="pwd" size="10" <?=$pwd?>>
															</td>
															<td width="10%" align="center" bgcolor="#F9FFFB" class="style2" valign="top">INDIGENOUS<br>
															<input type="text" name="indigenous" id="indigenous" size="10" <?=$indi?>>
															</td>
															<td width="15%" align="center" bgcolor="#F9FFFB" class="style2" valign="top">INSTITUTIONALIZED<br>
															<input type="text" name="institutionalized" id="institutionalized" size="15" <?=$ins?>>
															</td>
															<td width="10%" align="center" bgcolor="#F9FFFB" class="style2" valign="top">VAWC<br>
															<input type="text" name="vawc" id="vawc" size="10" >
															</td>
															<td width="15%" align="center" bgcolor="#F9FFFB" class="style2" valign="top">OTHER<br>
															<input type="text" name="others" id="others" size="15" <?=other?>>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											</table>

										<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
											<tr>
												<td valign="top" bgcolor="#98D228">
													<table width="100%" border="0" cellspacing="0" cellpadding="0">
														<tr>
															<td valign="middle" align="center" class="style9" height="35">FAMILY COMPOSITION</td>
														</tr>
														<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td height="36" colspan="3" valign="top" bgcolor="#98D228">
													<table width="100%" border="0" cellpadding="0" cellspacing="1">
														<tr>
															<td width="25%" align="center" valign="middle" bgcolor="#CFEC9B" class="style9">NAME</td>
															<td width="5%" align="center" valign="middle" bgcolor="#CFEC9B" class="style9">AGE</td>
															<td width="10%" align="center" valign="middle" bgcolor="#CFEC9B" class="style9">STATUS</td>
															<td width="15%" align="center" valign="middle" bgcolor="#CFEC9B" class="style9">RELATION TO PATIENT</td>
															<td width="15%" align="center" valign="middle" bgcolor="#CFEC9B" class="style9">EDUC. ATTAINMENT</td>
															<td width="15%" align="center" valign="middle" bgcolor="#CFEC9B" class="style9">OCCUPATION</td>
															<td width="15%" align="center" valign="middle" bgcolor="#CFEC9B" class="style9">MONTHLY INCOME</td>
														</tr>

														<tr>
															<td align="left" bgcolor="#F9FFFB" class="style2" valign="middle">&nbsp;1.&nbsp;&nbsp;
															<input type="text" name="family_name[]" id="family_name1" size="35">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle" height="35">
															<input type="text" name="family_age[]" id="family_age1" size="3">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_status[]" id="family_status1" size="10">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<!--<input type="text" name="family_relation[]" id="family_relation1" size="20">-->
															<select name="family_relation[]" id="family_relation1">
															<?php


																foreach($listRelation as $key=>$value){
																	if ($socInfo['family_relation']==$key){
																		echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
																	}else{
																		echo "				<option value='".$key."'>".$value."</option> \n";
																	}
																}
															?>
															</select>
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<!--<input type="text" name="family_educ[]" id="family_educ1" size="20">-->
															<select name="family_educ[]" id="family_educ1">
															<?php


																foreach($listEducAttain as $key=>$value){
																	if ($socInfo['family_educ']==$key){
																		echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
																	}else{
																		echo "				<option value='".$key."'>".$value."</option> \n";
																	}
																}
															?>
															</select>
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<!--<input type="text" name="family_occupation[]" id="family_occupation1" size="20">-->
															<select name="family_occupation[]" id="family_occupation1">
															<?php


																foreach($listOccupation as $key=>$value){
																	if ($socInfo['family_occupation']==$key){
																		echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
																	}else{
																		echo "				<option value='".$key."'>".$value."</option> \n";
																	}
																}
															?>
															</select>
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">Php&nbsp;
															<input type="text" name="family_income[]" id="family_income1" size="15">
															</td>
														</tr>

														<tr>
															<td align="left" bgcolor="#F9FFFB" class="style2" valign="middle">&nbsp;2.&nbsp;&nbsp;
															<input type="text" name="family_name[]" id="family_name2" size="35">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle" height="35">
															<input type="text" name="family_age[]" id="family_age2" size="3">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_status[]" id="family_status2" size="10">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_relation[]" id="family_relation2" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_educ[]" id="family_educ2" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_occupation[]" id="family_occupation2" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">Php&nbsp;
															<input type="text" name="family_income[]" id="family_income2" size="15">
															</td>
														</tr>

														<tr>
															<td align="left" bgcolor="#F9FFFB" class="style2" valign="middle">&nbsp;3.&nbsp;&nbsp;
															<input type="text" name="family_name[]" id="family_name3" size="35">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle" height="35">
															<input type="text" name="family_age[]" id="family_age3" size="3">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_status[]" id="family_status3" size="10">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_relation[]" id="family_relation3" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_educ[]" id="family_educ3" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_occupation[]" id="family_occupation3" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">Php&nbsp;
															<input type="text" name="family_income[]" id="family_income3" size="15">
															</td>
														</tr>

														<tr>
															<td align="left" bgcolor="#F9FFFB" class="style2" valign="middle">&nbsp;4.&nbsp;&nbsp;
															<input type="text" name="family_name[]" id="family_name4" size="35">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle" height="35">
															<input type="text" name="family_age[]" id="family_age4" size="3">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_status[]" id="family_status4" size="10">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_relation[]" id="family_relation4" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_educ[]" id="family_educ4" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_occupation[]" id="family_occupation4" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">Php&nbsp;
															<input type="text" name="family_income[]" id="family_income4" size="15">
															</td>
														</tr>

														<tr>
															<td align="left" bgcolor="#F9FFFB" class="style2" valign="middle">&nbsp;5.&nbsp;&nbsp;
															<input type="text" name="family_name[]" id="family_name5" size="35">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle" height="35">
															<input type="text" name="family_age[]" id="family_age5" size="3">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_status[]" id="family_status5" size="10">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_relation[]" id="family_relation5" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_educ[]" id="family_educ5" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_occupation[]" id="family_occupation5" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">Php&nbsp;
															<input type="text" name="family_income[]" id="family_income5" size="15">
															</td>
														</tr>

														<tr>
															<td align="left" bgcolor="#F9FFFB" class="style2" valign="middle">&nbsp;6.&nbsp;&nbsp;
															<input type="text" name="family_name[]" id="family_name6" size="35">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle" height="35">
															<input type="text" name="family_age[]" id="family_age6" size="3">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_status[]" id="family_status6" size="10">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_relation[]" id="family_relation6" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_educ[]" id="family_educ6" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_occupation[]" id="family_occupation6" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">Php&nbsp;
															<input type="text" name="family_income[]" id="family_income6" size="15">
															</td>
														</tr>

														<tr>
															<td align="left" bgcolor="#F9FFFB" class="style2" valign="middle">&nbsp;7.&nbsp;&nbsp;
															<input type="text" name="family_name[]" id="family_name7" size="35">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle" height="35">
															<input type="text" name="family_age[]" id="family_age7" size="3">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_status[]" id="family_status7" size="10">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_relation[]" id="family_relation7" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_educ[]" id="family_educ7" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_occupation[]" id="family_occupation7" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">Php&nbsp;
															<input type="text" name="family_income[]" id="family_income7" size="15">
															</td>
														</tr>

														<tr>
															<td align="left" bgcolor="#F9FFFB" class="style2" valign="middle">&nbsp;8.&nbsp;&nbsp;
															<input type="text" name="family_name[]" id="family_name8" size="35">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle" height="35">
															<input type="text" name="family_age[]" id="family_age8" size="3">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_status[]" id="family_status8" size="10">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_relation[]" id="family_relation8" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_educ[]" id="family_educ8" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_occupation[]" id="family_occupation8" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">Php&nbsp;
															<input type="text" name="family_income[]" id="family_income8" size="15">
															</td>
														</tr>

														<tr>
															<td align="left" bgcolor="#F9FFFB" class="style2" valign="middle">&nbsp;9.&nbsp;&nbsp;
															<input type="text" name="family_name[]" id="family_name9" size="35">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle" height="35">
															<input type="text" name="family_age[]" id="family_age9" size="3">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_status[]" id="family_status9" size="10">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_relation[]" id="family_relation9" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_educ[]" id="family_educ9" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_occupation[]" id="family_occupation9" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">Php&nbsp;
															<input type="text" name="family_income[]" id="family_income9" size="15">
															</td>
														</tr>

														<tr>
															<td align="left" bgcolor="#F9FFFB" class="style2" valign="middle">&nbsp;10.&nbsp;
															<input type="text" name="family_name[]" id="family_name10" size="35">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle" height="35">
															<input type="text" name="family_age[]" id="family_age10" size="3">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_status[]" id="family_status10" size="10">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_relation[]" id="family_relation10" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_educ[]" id="family_educ10" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">
															<input type="text" name="family_occupation[]" id="family_occupation10" size="20">
															</td>
															<td align="center" bgcolor="#F9FFFB" class="style2" valign="middle">Php&nbsp;
															<input type="text" name="family_income[]" id="family_income10" size="15">
															</td>
														</tr>

														<tr>
															<td colspan="7" align="left" bgcolor="#F9FFFB" class="style2" valign="middle" height="35">&nbsp;OTHER SOURCE OF INCOME:&nbsp;&nbsp;&nbsp;
																<input type="text" name="other_source_income" id="other_source_income" size="120" value="<?=$socInfo['other_source_income']?>">
															</td>
														</tr>
													</table>
											<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
											<tr>
												<td valign="top" bgcolor="#98D228">
													<table width="100%" border="0" cellspacing="0" cellpadding="0">
														<tr>
															<td valign="middle" align="center" class="style9" height="35">MONTHLY EXPENSES</td>
														</tr>
													</table>

											<tr>
												<td height="36" colspan="3" valign="top" bgcolor="#98D228">
													<table width="100%" border="0" cellpadding="1" cellspacing="1">
														<tr>
															<td width="25%" align="left" valign="middle" bgcolor="#F9FFFB" class="style2">HOUSE & LOT<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															<?php
																if($socInfo['house_type']=='1'){
																	$free = "checked";
																}else if ($socInfo['house_type']=='2'){
																	$owned = "checked";
																}else if ($socInfo['house_type']=='3'){
																	$rent = "checked";
																}else if ($socInfo['house_type']=='4'){
																	$shared = "checked";
																}else if($socInfo['house_type']=='5'){
																	$amortization = "checked";
																}
															?>
															<input type="radio" name="house_type" id="house_type" class="style2" value="1" <? echo $free; ?>>Free<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															<input type="radio" name="house_type" id="house_type" class="style2" value="2" <? echo $owned; ?>>Owned<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															<input type="radio" name="house_type" id="house_type" class="style2" value="3" <? echo $rent; ?>>Rent<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															<input type="radio" name="house_type" id="house_type" class="style2" value="4" <? echo $shared; ?>>Shared<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															<input type="radio" name="house_type" id="house_type" class="style2" value="5" <? echo $amortization; ?>>Monthly Amortization
															</td>
															<td colspan="3">
																<table width="100%" border="0" cellpadding="1" cellspacing="1">
																	<tr>
																		<td align="left" width="25%" valign="top" bgcolor="#F9FFFB" class="style2" height="45">LIGHT<br>
																		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Php&nbsp;&nbsp;
																		<input type="text" name="ligth_expense" id="ligth_expense" size="25" value="<?=number_format($socInfo['ligth_expense'],2,'.','')?>" onchange="this.value = Number(this.value).toFixed(2); computeTotal();" onkeydown="return key_check(event, this.value)";
																		</td>
																		<td align="left" width="25%" valign="top" bgcolor="#F9FFFB" class="style2">FOOD/PROVISIONS<br>
																		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Php&nbsp;&nbsp;
																		<input type="text" name="food_expense" id="food_expense" size="25" value="<?=number_format($socInfo['food_expense'],2,'.','')?>" onchange="this.value = Number(this.value).toFixed(2); computeTotal();" onkeydown="return key_check(event, this.value)">
																		</td>
																		<td align="left" width="25%" valign="top" bgcolor="#F9FFFB" class="style2">TRANSPORTATION<br>
																		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Php&nbsp;&nbsp;
																		<input type="text" name="transport_expense" id="transport_expense" size="25" value="<?=number_format($socInfo['transport_expense'],2,'.','')?>" onchange="this.value = Number(this.value).toFixed(2); computeTotal();" onkeydown="return key_check(event, this.value)">
																		</td>
																	</tr>
																	<tr>
																		<td align="left" width="25%" valign="top" bgcolor="#F9FFFB" class="style2" height="45">WATER<br>
																		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Php&nbsp;&nbsp;
																		<input type="text" name="water_expense" id="water_expense" size="25" value="<?=number_format($socInfo['water_expense'],2,'.','')?>" onchange="this.value = Number(this.value).toFixed(2); computeTotal();" onkeydown="return key_check(event, this.value)">
																		</td>
																		<td align="left" width="25%" valign="top" bgcolor="#F9FFFB" class="style2">EDUCATION<br>
																		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Php&nbsp;&nbsp;
																		<input type="text" name="education_expense" id="education_expense" size="25" value="<?=number_format($socInfo['education_expense'],2,'.','')?>" onchange="this.value = Number(this.value).toFixed(2); computeTotal();" onkeydown="return key_check(event, this.value)">
																		</td>
																		<td align="left" width="25%" valign="top" bgcolor="#F9FFFB" class="style2">HOUSEHELP<br>
																		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Php&nbsp;&nbsp;
																		<input type="text" name="househelp_expense" id="househelp_expense" size="25" value="<?=number_format($socInfo['househelp_expense'],2,'.','')?>" onchange="this.value = Number(this.value).toFixed(2); computeTotal();" onkeydown="return key_check(event, this.value)">
																		</td>
																	</tr>
																	<tr>
																		<td align="left" width="25%" valign="top" bgcolor="#F9FFFB" class="style2" height="45">FUEL<br>
																		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Php&nbsp;&nbsp;
																		<input type="text" name="fuel_expense" id="fuel_expense" size="25" value="<?=number_format($socInfo['fuel_expense'],2,'.','')?>" onchange="this.value = Number(this.value).toFixed(2); computeTotal();" onkeydown="return key_check(event, this.value)">
																		</td>
																		<td align="left" width="25%" valign="top" bgcolor="#F9FFFB" class="style2">CLOTHING<br>
																		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Php&nbsp;&nbsp;
																		<input type="text" name="clothing_expense" id="clothing_expense" size="25" value="<?=number_format($socInfo['clothing_expense'],2,'.','')?>" onchange="this.value = Number(this.value).toFixed(2); computeTotal();" onkeydown="return key_check(event, this.value)">
																		</td>
																		<td align="left" width="25%" valign="top" bgcolor="#F9FFFB" class="style2">MED. EXPENDITURE<br>
																		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Php&nbsp;&nbsp;
																		<input type="text" name="med_expenditure" id="med_expenditure" size="25" value="<?=number_format($socInfo['med_expenditure'],2,'.','')?>" onchange="this.value = Number(this.value).toFixed(2); computeTotal();" onkeydown="return key_check(event, this.value)">
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
												</td>

												</tr>

												</table>
												</td>
											</tr>
											</table>

											<table width="100%" border="0" cellspacing="1" cellpadding="1">
														<tr>
															<td width="50%" valign="middle" align="left" class="style2" height="25" bgcolor="#F9FFFB">INSURANCE / MORTGAGE&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															<input type="text" size="40" name="insurance_mortgage" id="insurance_mortgage" value="<?=number_format($socInfo['insurance_mortgage'],2,'.','')?>" onchange="this.value = Number(this.value).toFixed(2); computeTotal();" onkeydown="return key_check(event, this.value)">
															</td>
															<td width="50%" valign="middle" align="left" class="style2" height="25" bgcolor="#F9FFFB">OTHERS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															<input type="text" size="40" name="other_expense" id="other_expense" value="<?=number_format($socInfo['other_expense'],2,'.','')?>" onchange="this.value = Number(this.value).toFixed(2); computeTotal();" onkeydown="return key_check(event, this.value)"></td>
														</tr>
														<tr>
															<td colspan="2" valign="middle" align="left" class="style2" height="25" bgcolor="#F9FFFB">
															TOTAL MONTHLY EXPENDITURE: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															Php &nbsp;&nbsp;<input type="text" name="total_monthly_expense" id="total_monthly_expense" size="28" value="<?=number_format($socInfo['total_monthly_expense'],2,'.','')?>" onkeydown="return key_check(event, this.value)" onblur="this.value = Number(this.value).toFixed(2)" readonly="readonly">
															</td>
														</tr>

												</table>
												<tr>
													<td bgcolor="#F9FFFB">&nbsp;</td>
												</tr>
												<tr>
													<td align="left" bgcolor="#98D228" class="style9">II. MEDICAL DATA</td>
												</tr>
												<tr>
													<td bgcolor="#F9FFFB">&nbsp;</td>
												</tr>
												<tr>
													<td bgcolor="#98D228">
														<table width="100%" border="0" cellspacing="1" cellpadding="1">
														<tr>
															<td width="50%" valign="middle" align="left" class="style2" height="100" bgcolor="#F9FFFB">Admitting Diagnosis:<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															<?
																if($encInfo['encounter_type']=='3' || $encInfo['encounter_type']=='4'){
																	$diag = $encInfo['er_opd_diagnosis'];
																}else if($encInfo['encounter_type']=='1' || $encInfo['encounter_type']=='2'){
																	$diag = $encInfo['chief_complaint'];
																}
															?>
															<textarea name="admitting_diagnosis" id="admitting_diagnosis" cols="53" rows="3" readonly="readonly"><? echo $diag ?></textarea>
															</td>
															<td width="50%" valign="middle" align="left" class="style2" height="100" bgcolor="#F9FFFB">Final Diagnosis:<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															<textarea name="final_diagnosis" id="final_diagnosis" cols="53" rows="3"><? $socInfo['final_diagnosis']?></textarea>
															</td>
														</tr>
														<tr>
															<td valign="middle" align="left" class="style2" height="25" bgcolor="#F9FFFB">
															 Duration of Problem/symptoms: &nbsp;&nbsp;
															<input type="text" name="duration_problem" id="duration_problem" size="48" value="<?=$socInfo['duration_problem']?>">
															</td>
															<td valign="middle" align="left" class="style2" height="25" bgcolor="#F9FFFB">
															 Previous Treatment/Duration: &nbsp;&nbsp;
															 <input type="text" name="duration_treatment" id="duration_treatment" size="50" value="<?=$socInfo['duration_treatment']?>">
															</td>
														</tr>

														<tr>
															<td valign="middle" align="left" class="style2" height="25" bgcolor="#F9FFFB">
															 Present Treatment Plan: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															<input type="text" name="treatment_plan" id="treatment_plan" size="48" value="<?=$socInfo['treatment_plan']?>">
															</td>
															<td valign="middle" align="left" class="style2" height="25" bgcolor="#F9FFFB">
															 Health Accessibility Problem: &nbsp;&nbsp;
															 <input type="text" name="accessibility_problem" id="accessibility_problem" size="50" value="<?=$socInfo['accessibility_problem']?>">
															</td>
														</tr>

												</table>
												<table width="100%" border="0" cellspacing="1" cellpadding="1">
														<tr>
															<td width="30%" valign="middle" align="left" class="style2" height="70" bgcolor="#F9FFFB">SOURCE OF REFERRAL<br>Mr. / Ms.&nbsp;&nbsp;
															<input type="text" size="40" name="informant_name" id="informant_name" value="<?=$socInfo['informant_name']?>">
															</td>
															<td width="55%" valign="middle" align="left" class="style2" height="70" bgcolor="#F9FFFB">AGENCY&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" size="50" name="info_agency" id="info_agency" value="<?=$socInfo['info_agency']?>"><br><br>
															ADDRESS&nbsp;&nbsp;
															<input type="text" size="80" name="info_address" id="info_address" value="<?=$socInfo['info_address']?>">
															</td>
															<td width="15%" valign="middle" align="left" class="style2" height="70" bgcolor="#F9FFFB">
															CONTACT NO:<br>&nbsp;&nbsp;
															<input type="text" size="20" name="info_contact_no" id="info_contact_no" value="<?=$socInfo['info_contact_no']?>">
															</td>
														</tr>
												</table>

												<table width="100%" border="0" cellspacing="1" cellpadding="1">
														<tr>
															<td width="70%" valign="middle" align="left" class="style2" height="70" bgcolor="#F9FFFB">REMARKS<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															<textarea name="remarks" id="remarks" rows="3" cols="73"><? echo $socInfo['remarks'] ?></textarea>
															</td>
															<td width="30%" valign="top" align="left" class="style2" height="70" bgcolor="#F9FFFB">
															SOCIAL WORKER<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															<input type="text" size="45" name="social_worker" id="social_worker" value="<?=$HTTP_SESSION_VARS['sess_user_name']?>" readonly="readonly">
															</td>
														</tr>

												</table>
																	</td>
																</tr>
																<tr>
																	<td>

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
		</td>
	</tr>
</table>



</table>
<div align="center">
	<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td  bgcolor="#A0C2DA">
				<center>
	<?php
			if (!$socInfo || empty($socInfo) || $socInfo=='' || $socInfo=='1'){
				echo '			<input type="hidden" name="mode" id="mode" value="save">'."\n";
				echo '			<input type="submit" name="Submit" value="Save">'."\n";
			}else{
				echo '			<input type="hidden" name="mode" id="mode" value="update">'."\n";
				echo '			<input type="button" name="Print" value="Print" onClick="printMSWDForm(\''.$pid.'\',\''.$encounter_nr.'\')">'."\n &nbsp; &nbsp;";
				echo '			<input type="submit" name="Submit" value="Update">'."\n";
			}
			echo '			<input type="hidden" name="pid" id="pid" value="'.$pid.'">'."\n";
?>
	&nbsp; &nbsp;
	<!--<input type="button" name="Cancel" value="Cancel"  onclick="window.close()" />-->
			 </center>
		 </td>
		</tr>
	</table>
</div>
</form>
<script language="javascript">
<?php
	//echo $presetJavascript;
?>
</script>

</body>
</html>
