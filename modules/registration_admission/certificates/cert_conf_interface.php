<?php

#created by VAN 03-27-08
include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

#added by VAN 02-18-08
define('NO_2LEVEL_CHK',1);
require($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'/include/care_api_classes/class_drg.php');
$objDRG= new DRG;

include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_department.php');

$dept_obj=new Department;
$pers_obj=new Personell;


if (isset($_GET['encounter_nr']) && $_GET['encounter_nr']){
	$encounter_nr = $_GET['encounter_nr'];
}
if (isset($_POST['encounter_nr']) && $_POST['encounter_nr']){
	$encounter_nr = $_POST['encounter_nr'];
}

include_once($root_path.'include/care_api_classes/class_cert_med.php');
$obj_medCert = new MedCertificate($encounter_nr);

#if($_GET['encounter_nr']){
/*
if($encounter_nr){
#	if(!($encInfo = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
	if(!($encInfo=$enc_obj->getEncounterInfo($encounter_nr))){
		echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
		exit();
	}
	#extract($encInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
	exit();
}
*/
$errorMsg='';
/*
echo "HTTP_POST_VARS : <br>"; print_r($HTTP_POST_VARS); echo "<br> \n";
echo "medCertInfo : <br>"; print_r($confCertInfo); echo "<br> \n";
echo "encounter_nr = '".$encounter_nr."' <br> \n";
echo "obj_medCert->sql = '".$obj_medCert->sql."' <br> \n";
#exit();
*/
#echo "type = ".isset($_POST['is_vehicular_accident']);

if (isset($_POST['is_vehicular_accident'])){
	#echo "is set";
	$HTTP_POST_VARS['is_vehicular_accident'] = "1";
}else{
	#echo "is not set";
	$HTTP_POST_VARS['is_vehicular_accident'] = "0";	
}

$HTTP_POST_VARS['is_medico_legal']=$_POST['cert_type'];

#added by VAN 04-28
$HTTP_POST_VARS['is_doc_sig']=$_POST['signatory'];

if ($HTTP_POST_VARS['is_doc_sig']){
	$HTTP_POST_VARS['dr_nr'] = $_POST['doctors'];
}else{
	if ($_POST['medrec'])
	    $HTTP_POST_VARS['dr_nr'] = $_POST['medrec'];
	else
		$HTTP_POST_VARS['dr_nr'] = ' ';
	
}
	
$HTTP_POST_VARS['nurse_on_duty'] = $_POST['nurses'];

#added by VAN 06-13-08
$HTTP_POST_VARS['DOI'] = date("y-m-d",strtotime($HTTP_POST_VARS['DOI']));
$time = $HTTP_POST_VARS['TOI'].":00 ".$HTTP_POST_VARS['selAMPM'];
$prev_hr = $HTTP_POST_VARS['TOI'];
$prev_mer = $HTTP_POST_VARS['selAMPM'];
#echo "time = ".$time;
#echo "hr, mer = ".$prev_hr." - ".$prev_mer;
$HTTP_POST_VARS['TOI'] = date("H:i:s",strtotime($time));
#echo "TOI = ".$HTTP_POST_VARS['TOI'];

if ( (strstr($prev_hr,'12'))&&($prev_mer=='AM')){
	$HTTP_POST_VARS['TOI'] = '24:'.date("i",strtotime($HTTP_POST_VARS['TOI'])).":00";
}	
	
if (!$HTTP_POST_VARS['cert_type']){
	#$HTTP_POST_VARS['is_medico'] = '0';
	$HTTP_POST_VARS['POI'] = "";
	$HTTP_POST_VARS['TOI'] = "";
	$HTTP_POST_VARS['DOI'] = "";
}	
#echo "TOI = ".$HTTP_POST_VARS['TOI'];								
$medico_cases = $enc_obj->getMedicoCases();
#----------------------------------------------

if (isset($_POST['mode'])){
	switch($_POST['mode']) {
		case 'save':
			$HTTP_POST_VARS['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
			$HTTP_POST_VARS['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_dt']=date('Y-m-d H:i:s');
			
			if ($obj_medCert->saveConfCertificateInfoFromArray($HTTP_POST_VARS)){
				#added by VAN 06-13-08
				$enc_obj->setMedico($HTTP_POST_VARS['cert_type'],$encounter_nr);
									
				$cases = array();
				if(is_object($medico_cases)){
					while($result=$medico_cases->FetchRow()) {
						if ($HTTP_POST_VARS['medico'.$result['code']]){
														#$cases[] = array($HTTP_POST_VARS['medico'.$result['code']]);
#modify by shandy | remove alert
														 if ($HTTP_POST_VARS['medico'.$result['code']]=='OT')
																$desc =  $HTTP_POST_VARS['description'];
														else
																$desc = "none";

														$cases[] = array($HTTP_POST_VARS['medico'.$result['code']],$desc);
						}
					}
				}		
											
				if (!$HTTP_POST_VARS['cert_type']){
					$enc_obj->deleteMedicoCasesEncounter($encounter_nr,$pid);
				}else{
					$enc_obj->deleteMedicoCasesEncounter($encounter_nr,$pid);
					$enc_obj->addMedicoCasesEncounter($encounter_nr,$pid,$cases);
				}
				$enc_obj->updateIncident($encounter_nr,$HTTP_POST_VARS['POI'],$HTTP_POST_VARS['TOI'],$HTTP_POST_VARS['DOI'],"Update from Certificate of Confinement Form");
				#----------------------
				
				$errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
			}else{
				$errorMsg='<font style="color:#FF0000">'.$obj_medCert->getErrorMsg().'</font>';			
			}
		break;
		case 'update':
			$HTTP_POST_VARS['history'] = "Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
			$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['modify_dt']=date('Y-m-d H:i:s');
			#print_r($HTTP_POST_VARS);
			if ($obj_medCert->updateConfCertificateInfoFromArray($HTTP_POST_VARS)){
				#added by VAN 06-13-08
				$enc_obj->setMedico($HTTP_POST_VARS['cert_type'],$encounter_nr);
									
				$cases = array();
				if(is_object($medico_cases)){
					while($result=$medico_cases->FetchRow()) {
						if ($HTTP_POST_VARS['medico'.$result['code']]){#modify by shandy : remove alert
														if ($HTTP_POST_VARS['medico'.$result['code']]=='OT')
																$desc =  $HTTP_POST_VARS['description'];
														else
																$desc = "none";

														$cases[] = array($HTTP_POST_VARS['medico'.$result['code']],$desc);
						}
					}
				}		
											
				if (!$HTTP_POST_VARS['cert_type']){
					$enc_obj->deleteMedicoCasesEncounter($encounter_nr,$pid);
				}else{
					$enc_obj->deleteMedicoCasesEncounter($encounter_nr,$pid);
					$enc_obj->addMedicoCasesEncounter($encounter_nr,$pid,$cases);
				}
				$enc_obj->updateIncident($encounter_nr,$HTTP_POST_VARS['POI'],$HTTP_POST_VARS['TOI'],$HTTP_POST_VARS['DOI'],"Update from Certificate of Confinement Form");
				#echo $enc_obj->sql;
				#----------------------
				$errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated sucessfully!".'</font>';
			}else{
				$errorMsg='<font style="color:#FF0000">'.$obj_medCert->getErrorMsg().'</font>';			
			}
			#echo "sql = ".$obj_medCert->sql;
		break;
	}# end of switch statement
}

#echo "errorMsg = '".$errorMsg."' <br> \n";

//$_GET['encounter_nr'] = 2007500006;
#$encounter_nr = $_GET['encounter_nr'];
//$encounter_nr = '2007500006';
#$info = $enc_obj->getEncounterInfo($encounter_nr);

#transferred by VAN 06-13-08
if($encounter_nr){
#	if(!($encInfo = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
	if(!($encInfo=$enc_obj->getEncounterInfo($encounter_nr))){
		echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
		exit();
	}
	#extract($encInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
	exit();
}

$confCertInfo = $obj_medCert->getConfCertRecord($encounter_nr);
$medico_cases = $enc_obj->getMedicoCases();

//print_r($encInfo);

#echo "encInfo['encounter_type'] = '".$encInfo['encounter_type']."' <br> \n";
$listDoctors=array();

#added by VAN 06-28-08
  if ($encInfo['current_dept_nr'])	
	$dept_nr = $encInfo['current_dept_nr'];
  else	
	$dept_nr = $encInfo['consulting_dept_nr'];

	#commented by VAN 07-31-08
	/*	
  if ($dept_nr)		
 	 $doctors = $pers_obj->getDoctorsOfDept($dept_nr);
  else
  */
  	 $doctors = $pers_obj->getDoctors(1);	 

  if (is_object($doctors)){	
	while($drInfo=$doctors->FetchRow()){
	
		$middleInitial = "";
		if (trim($drInfo['name_middle'])!=""){
			$thisMI=split(" ",$drInfo['name_middle']);	
			foreach($thisMI as $value){
				if (!trim($value)=="")
					$middleInitial .= $value[0];
			}
			if (trim($middleInitial)!="")
				$middleInitial .= ". ";
		}
			
		#$name_doctor = trim($drInfo["name_first"])." ".trim($drInfo["name_2"])." ".$middleInitial.trim($drInfo["name_last"]);
		#$name_doctor = "Dr. ".$name_doctor;
		$name_doctor = trim($drInfo["name_last"]).", ".trim($drInfo["name_first"])." ".$middleInitial; #substr(trim($drInfo["name_middle"]),0,1).$dot;
		$name_doctor = ucwords(strtolower($name_doctor)).", MD";
		
		#echo "<br> dr = ".$name_doctor;
		#$listDoctors['doctor_name']=$name_doctor;
		
		$listDoctors[$drInfo["personell_nr"]]=$name_doctor;
		#$listDoctors['doctor_nr']=$drInfo["personell_nr"];
			
		#print_r($listDoctors);
	}	
 }	
	
//added by daryl
 //set data

if (empty($confCertInfo['admitting_diagnosis'])){
$patient_diag = $enc_obj->getPatientEncounter($encounter_nr);
$admitting_diagnosis = $patient_diag['er_opd_diagnosis'];
}else{
$admitting_diagnosis = $confCertInfo['admitting_diagnosis'];
}
//Added by genz
$docInfo = $pers_obj->getPersonellInfo($confCertInfo['dr_nr']);
//end by genz
//$lic_no = $confCertInfo['lic_no'];
$lic_no = $docInfo['license_nr'];
$ptr_no = $docInfo['ptr_no'];
$operation_performed = $confCertInfo['operation_performed'];
if (empty($confCertInfo['prepared_by'])){
$prepared_by = $_SESSION['sess_user_name'];
}else{
$prepared_by = $confCertInfo['prepared_by'];
}


#----------------------

?>
<html>
<head>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #F8F9FA;
}
.style2 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
}

.style3 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: normal;
}

-->
</style>
<?php
echo '<script type="text/javascript" language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>';

#added by VAN 06-13-08
#require($root_path.'include/inc_checkdate_lang.php');
echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\n";
echo '<script language="javascript" src="'.$root_path.'js/setdatetime.js"></script>'."\n";
echo '<script language="javascript" src="'.$root_path.'js/checkdate.js"></script>'."\n";
/*echo '<script language="javascript" src="'.$root_path.'js/dtpick_care2x.js"></script>'."\n";*/
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\n";
?>

<script language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>

<script language="javascript">
	String.prototype.trim = function() { return this.replace(/^\s+|\s+$/, ''); };

	function chkForm(){
		//$('diagnosis_verbatim').value=$F('diagnosis_verbatim').trim();
		if ($F('purpose')==''){
			alert(" Please enter the purpose of requesting the certificate.");
			$('purpose').focus();
			return false;
		}
		
		if ($F('requested_by')==''){
			alert(" Please enter the name of the one who request the certificate of confinement.");
			$('requested_by').focus();
			return false;
		}
		
		if ($F('relation_to_patient')==''){
			alert(" Please enter the relationship of the one who request the certificate of confinement to the patient");
			$('relation_to_patient').focus();
			return false;
		}
		
		return true;
	}

	//edited by VAN 03-27-08
	//var type=<?php if (isset($_POST['cert_type'])) echo "'".$_POST['cert_type']."'"; else echo "'NML'"; ?>;	
	var type=<?php if (isset($_POST['cert_type'])) echo "'".$_POST['cert_type']."'"; else echo "0"; ?>;	
	function checkType(thisType){
		//alert($('cert_type').value);
		type = thisType;
	}
	
	function printMedCert(id){
		var doc = document.getElementById('doctors');
		var doc_name = doc.options[doc.selectedIndex].text;
		if (doc.selectedIndex == 0)
			doc_name='';
		var msg = "doc = '"+doc+"' \n"+
					"doc.selectedIndex = '"+doc.selectedIndex+"' \n"+
					"doc_name = '"+doc_name+"' \n";
//alert(msg);
		if (id==0) 
			id="";
		if (window.showModalDialog){  //for IE
			window.showModalDialog("cert_conf_pdf.php?id="+id+"&type="+type+"&doc_name="+doc_name,"width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
		}else{
			window.open("cert_conf_pdf.php?id="+id+"&type="+type+"&doc_name="+doc_name,"medicalCertificate","modal, width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
		}
	}
	
	//added by VAN 04-28-08
	function checkSignatory(val){
		//alert('hello ='+val);
		if (val==1){
			//show doctor as signatory
			document.getElementById('doc_sig').style.display = '';
			document.getElementById('medrec_sig').style.display = 'none';
		}else{
			//hide doctor as signatory
			document.getElementById('doc_sig').style.display = 'none';	
			document.getElementById('medrec_sig').style.display = '';
		}	
	}
	
	function preset(){
		//alert('preset');
		var d = document.med_certificate;
		//alert(d.signatory[0].checked);
		//alert(d.signatory[1].checked);
		
		checkMedico();
		
		if (d.signatory[0].checked){
			//show doctor as signatory
			document.getElementById('doc_sig').style.display = '';
			document.getElementById('medrec_sig').style.display = 'none';
			
		}else if(d.signatory[1].checked){
			//hide doctor as signatory
			document.getElementById('doc_sig').style.display = 'none';	
			document.getElementById('medrec_sig').style.display = '';
		}
		
	}
	
	//added by VAN 06-13-08
	function checkMedico(){
		var d = document.med_certificate;
		
		if (d.cert_type[0].checked){
			//show medico info
			document.getElementById('ERMedico').style.display = '';
			document.getElementById('ERMedicoPOI').style.display = '';
			document.getElementById('ERMedicoTOI').style.display = '';
			document.getElementById('ERMedicoDOI').style.display = '';
			
			document.getElementById('space1').style.display = '';
			document.getElementById('space2').style.display = '';
			document.getElementById('space3').style.display = '';
			document.getElementById('space4').style.display = '';
			document.getElementById('space5').style.display = '';
			
		}else if(d.cert_type[1].checked){
			//hide medico info
			document.getElementById('ERMedico').style.display = 'none';	
			document.getElementById('ERMedicoPOI').style.display = 'none';	
			document.getElementById('ERMedicoTOI').style.display = 'none';	
			document.getElementById('ERMedicoDOI').style.display = 'none';	
			
			document.getElementById('space1').style.display = 'none';
			document.getElementById('space2').style.display = 'none';
			document.getElementById('space3').style.display = 'none';
			document.getElementById('space4').style.display = 'none';
			document.getElementById('space5').style.display = 'none';
		}
	}
	
	function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g,""); 
}/* end of function trimString */

var js_time = "";
function js_setTime(jstime){
	js_time = jstime;	
}

function js_getTime(){
	return js_time;	
}

function validateTime(S) {
    return /^([01]?[0-9])(:[0-5][0-9])?$/.test(S);
}

var seg_validDate=true;
//var seg_validTime=false;

function seg_setValidDate(bol){
	seg_validDate=bol;
//	alert("seg_setValidDate : seg_validDate ='"+seg_validDate+"'");	
}

var seg_validTime=false;
function setFormatTime(thisTime,AMPM){
//	var time = $('time_text_d');
	var stime = thisTime.value;
	var hour, minute;
	var ftime ="";
	var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
	var f2 = /^[0-9]\:[0-5][0-9]$/;
	var jtime = "";
	
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
	
	jtime = hour + ":" + minute;
	js_setTime(jtime);
	
	if (hour==0){
		 hour = 12;
		 document.getElementById(AMPM).value = "A.M.";		
	}else	if((hour > 12)&&(hour < 24)){
		 hour -= 12;
		 document.getElementById(AMPM).value = "P.M.";
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

	//-------------------
	
</script>
</head>

<body onLoad="preset();">
<table width="520" height="236" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#BAD0FC" class="style2">
	<tr>
		<td colspan="*"><?= $errorMsg ?></td>
	</tr>
	<tr>		
		<td colspan="*" height="23" bgcolor="#FFFFFF" >
			<table width="100%" border="0" bgcolor="#F8F9FA"class="style3">
				<tr>
					<td width="18%" ></td>
					<td width="37%" >&nbsp;</td>
					<td width="28%" align="right" ><? echo 'Case No. '?></td>
					<td width="17%" align="left"><? echo $encounter_nr; ?></td>
				</tr>
				<tr>
					<td><span class="style3"><? echo "Name :"?> </span></td>
					<td colspan="2" class="style2">
						<? 
							#edited by VAN 02-28-08 
							$name = stripslashes(strtoupper($encInfo['name_first'])).' '.stripslashes(strtoupper($encInfo['name_middle'])).' '.stripslashes(strtoupper($encInfo['name_last']));
							echo $name;
						
						?>  <? #echo stripslashes(strtoupper($encInfo['name_last']));?>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
						<span class="style3"><? echo "Age :  "?></span><? echo $encInfo['age'].' old';?>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><span class="style3"><? echo "Address :"?></span></td>
					<td colspan="2" class="style2">
						<? echo stripslashes(strtoupper($encInfo['street_name']))."&nbsp;&nbsp; ".stripslashes(strtoupper($encInfo['brgy_name']))."&nbsp;&nbsp; ".stripslashes(strtoupper($encInfo['mun_name'])). ", ".stripslashes(strtoupper($encInfo['prov_name'])). "&nbsp;&nbsp;".stripslashes(strtoupper($encInfo['zipcode']));?>
					</td>
					<td>&nbsp;</td>
				</tr>

			</table>
		</td>
	</tr>
	<tr>
		<td colspan="*" align="left" valign="top" bgcolor="#F8F9FA">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="*" width="467" height="23" bgcolor="#FFFFFF">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="15" align="left" background="images/top_05.jpg" bgcolor="#FFFFFF">&nbsp;</td>
					<td width="442" background="images/top_05.jpg">&nbsp;</td>
					<td width="10" background="images/top_05.jpg" bgcolor="#FFFFFF">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<form id="med_certificate" name="med_certificate" method="post" action="" onSubmit="return chkForm()">
	<?php 
			/*
			if ($encInfo['current_dept_nr'])	
				$dept_nr = $encInfo['current_dept_nr'];
			else	
				$dept_nr = $encInfo['consulting_dept_nr'];
				
			if ($encInfo['current_att_dr_nr'])
				$dr_nr = $encInfo['current_att_dr_nr'];
			else
				$dr_nr = $encInfo['consulting_dr_nr'];		
			
			$drInfo = $pers_obj->get_Person_name($dr_nr);
			
			$middleInitial = "";
			if (trim($drInfo['name_middle'])!=""){
				$thisMI=split(" ",$drInfo['name_middle']);	
				foreach($thisMI as $value){
					if (!trim($value)=="")
						$middleInitial .= $value[0];
				}
				if (trim($middleInitial)!="")
					$middleInitial .= ". ";
			}
			
			$name_doctor = $drInfo["name_first"]." ".$drInfo["name_2"]." ".$middleInitial.$drInfo["name_last"];
			$name_doctor = "Dr. ".ucwords(strtolower($name_doctor));
			
			$listDoctors[$temp['doctor']]=$name_doctor;	
			*/
			#echo "enc =".$encounter_nr;
			#added by VAN 04-28-08
			if ($result_diagnosis = $objDRG->getDiagnosisCodes($encounter_nr,$encInfo['encounter_type'])){
				$rowsDiagnosis = $result_diagnosis->RecordCount();
				while($temp=$result_diagnosis->FetchRow()){
					#commented by VAN 06-28-08
					#$listDoctors[$temp['diagnosing_clinician']]=$temp['diagnosing_clinician_name'];
				}
			}

			if ($result_therapy = $objDRG->getProcedureCodes($encounter_nr,$encInfo['encounter_type'])){
				$rowsTherapy = $result_therapy->RecordCount();
				while($temp=$result_therapy->FetchRow()){
					#commented by VAN 06-28-08
					#$listDoctors[$temp['responsible_clinician']]=$temp['responsible_clinician_name'];
				}
			}
			
			$patientEncInfo = $enc_obj->getEncounterInfo($encounter_nr);
			$consulting_dr = $pers_obj->getPersonellInfo($patientEncInfo['consulting_dr_nr']);
	
			$consulting_dr_middleInitial = "";
			if (trim($consulting_dr['name_middle'])!=""){
				$thisMI=split(" ",$consulting_dr['name_middle']);	
				foreach($thisMI as $value){
					if (!trim($value)=="")
						$consulting_dr_middleInitial .= $value[0];
				}		
				if (trim($consulting_dr_middleInitial)!="")
					$consulting_dr_middleInitial = " ".$consulting_dr_middleInitial.".";
			}
	
			$attending_dr = $pers_obj->getPersonellInfo($patientEncInfo['current_att_dr_nr']);
	
			$attending_dr_middleInitial = "";
			if (trim($attending_dr['name_middle'])!=""){
				$thisMI=split(" ",$attending_dr['name_middle']);	
		
				foreach($thisMI as $value){
					if (!trim($value)=="")
						$attending_dr_middleInitial .= $value[0];
				}		
					if (trim($attending_dr_middleInitial)!=""){
						$attending_dr_middleInitial = " ".$attending_dr_middleInitial.".";
					}	
			}	
		
			$consulting_dr_name = "Dr. ".$consulting_dr['name_first']." ".$consulting_dr['name_2']." ".$consulting_dr_middleInitial." ".$consulting_dr['name_last'];
			$attending_dr_name = "Dr. ".$attending_dr['name_first']." ".$attending_dr['name_2']." ".$attending_dr_middleInitial." ".$attending_dr['name_last'];
			
			#commented by VAN 06-28-08
			/*
			if (sizeof($listDoctors)==0){
				$listDoctors[$patientEncInfo['consulting_dr_nr']] = 	$consulting_dr_name;
				$listDoctors[$patientEncInfo['current_att_dr_nr']] = 	$attending_dr_name;
			}
			*/		

	?>
	<tr>
		<td valign="top" bgcolor="#F8F9FA">
			Certificate Type &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;:  &nbsp;&nbsp;&nbsp;
			<?php 	/*
					if ($confCertInfo['is_medico_legal']=="1"){
						$checked1 = "checked";
						$checked2 = "";
					}elseif (($confCertInfo['is_medico_legal']=="0")||(!isset($_POST['cert_type']))){
						$checked1 = "";
						$checked2 = "checked";
					}
					*/
				if ($confCertInfo){
					if ($confCertInfo['is_medico_legal']!="0"){
						$checked1 = "checked";
						$checked2 = "";
					}else{
						$checked1 = "";
						$checked2 = "checked";
					}
				}else{
					#from care_encounter
					if ($encInfo['is_medico']!="0"){
						$checked1 = "checked";
						$checked2 = "";
					}else{
						$checked1 = "";
						$checked2 = "checked";
					}
				}	
					
			?>
			
			<input name="cert_type" id="cert_type" type="radio" value="1" onClick="checkType(this.value);checkMedico();" <?php echo $checked1; ?>>Medicolegal
			&nbsp;&nbsp;
			<input name="cert_type" id="cert_type" type="radio" value="0" onClick="checkType(this.value);checkMedico();" <?php echo $checked2; ?>>Non-Medicolegal
			
		</td>
	</tr>
	<!--commented by VAN 06-13-08 -->
	<!--
	<tr>
		<td valign="top" bgcolor="#F8F9FA">
			Vehicular Accident &nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:  &nbsp;&nbsp;&nbsp; 
			<?php 
					if ($confCertInfo['is_vehicular_accident']){
						$checked = "checked";
					}else{
						$checked = "";
					}
			?>
			
			<input name="is_vehicular_accident" id="is_vehicular_accident" type="checkbox" value="1" <?php echo $checked; ?>>
		</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#F8F9FA">
			Signatory &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;
			
			<?php 
				if ($confCertInfo['is_doc_sig']!="0"){
					$checked1 = "checked";
					$checked2 = "";
				}else{
					$checked1 = "";
					$checked2 = "checked";
				}
			?>

			<input name="signatory" id="signatory" type="radio" value="1" onClick="checkSignatory(this.value);" <?php echo $checked1; ?>>Doctor
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input name="signatory" id="signatory" type="radio" value="0" onClick="checkSignatory(this.value);" <?php echo $checked2; ?>>Medical Record Officer
		</td>
	</tr>
	-->
	<tr id="space1">
		<td>&nbsp;</td>
	</tr>
	<!-- added by VAN -->
	<tr id="ERMedico">
		<?php
				
				if(is_object($medico_cases)){
					$sTemp = '';
					$count=0;
				#echo "enc = ".$encounter_nr;
					while($result=$medico_cases->FetchRow()) {
						$sTemp = $sTemp.'<input name="medico'.$result['code'].'" id="medico'.$result['code'].'" type="checkbox" value="'.$result['code'].'" ';
						
						$medico=$enc_obj->getEncounterByMedicoCases($encounter_nr,$encInfo['pid'],$result['code']);
						#echo "<br>sql = ".$enc_obj->sql;
						if($medico['medico_cases']==$result['code']) $sTemp = $sTemp.'checked';
						
						$sTemp = $sTemp.'>';
						$sTemp = $sTemp.$result['medico_cases']."<br>";
						if($count<=3){
							$rowMedicoA =$sTemp;
							if($count==3){$sTemp='';}
						}else{ $rowMedicoB =$sTemp; }
						$count++;
					}
					
				}		
		?>
		<td class="adm_item" width="30%">
			Medico Legal Cases &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;
			<table width="40%" height="84" border="0" cellpadding="1" id="srcMedicoTable" style="width:100%; font-size:12px">
				<td width="36%" height="80" valign="middle" id="leftTdMedico">
					<?=$rowMedicoA?>					
				</td>
				<td width="64%" valign="middle" id="rightTdMedico">
					<?=$rowMedicoB?>					
				</td>
			</table>
			
		</td>
					
	</tr>
	<tr id="space2">
		<td>&nbsp;</td>
	</tr>

	<tr id="ERMedicoPOI">
		<td class="adm_item">
			Place of Incident (POI) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;
			<input name="POI" id="POI" type="text" size="50" value="<?=ucwords(strtolower(trim($encInfo['POI'])))?>">
		</td>
	</tr>
	<tr id="space3">
		<td>&nbsp;</td>
	</tr>
	
	<tr id="ERMedicoTOI">
		<?php
				$meridian = date("A",strtotime($encInfo['TOI']));
				#echo "meridian = ".$meridian;
				if ($meridian=='PM'){
					$selected1 = "";
					$selected2 = "selected";
				}else{
					$selected1 = "selected";
					$selected2 = "";
				}
				
				if ($encInfo['TOI']=='00:00:00'){
					$TOI_val = "";
				}else{
					if (strstr($encInfo['TOI'],'24')){
						$TOI_val = "12:".substr($encInfo['TOI'],3,2);
						$selected1 = "selected";
						$selected2 = "";
					}else
						$TOI_val = date("h:i",strtotime($encInfo['TOI']));
				}
		?>
		<td class="adm_item">
			Time of Incident (TOI) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;
			<input type="text" id="TOI" name="TOI" size="4" maxlength="5" value="<?=$TOI_val?>" onChange="setFormatTime(this,'selAMPM')" />
			<select id="selAMPM" name="selAMPM">
				<option value="AM" <?=$selected1?>>A.M.</option>
				<option value="PM" <?=$selected2?>>P.M.</option>
			</select>&nbsp;<font size=1>[hh:mm]</font>
		</td>
		
	</tr>
	<tr id="space4">
		<td>&nbsp;</td>
	</tr>
	<tr id="ERMedicoDOI">
		<td class="adm_item">
			 <?php
				$phpfd=$date_format;
				$phpfd=str_replace("dd", "%d", strtolower($phpfd));
				$phpfd=str_replace("mm", "%m", strtolower($phpfd));
				$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
	
				if (($encInfo['DOI']!='0000-00-00') && ($encInfo['DOI']!=""))
					$DOI_val = @formatDate2Local($encInfo['DOI'],$date_format);
				else
					$DOI_val='';

				$sDateJS= '<input name="DOI" type="text" size="15" maxlength=10 value="'.$DOI_val.'"'. 
							'onFocus="this.select();" 
							id = "DOI"
							onBlur="IsValidDate(this,\''.$date_format.'\'); "
							onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
							<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="DOI_trigger" style="cursor:pointer" >
							<font size=1>['; 			
							ob_start();
			?>
          <script type="text/javascript">
			Calendar.setup ({
					inputField : "DOI", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "DOI_trigger", singleClick : true, step : 1
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
                        Date of Incident (DOI) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;
                        <?= $sDateJS ?>
                      </span>
		</td>
	</tr>
	<!-- -->
	<tr id="space5">
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#F8F9FA">
			Signatory &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;&nbsp;&nbsp;
			
			<?php 
				if ($confCertInfo['is_doc_sig']!="0"){
					$checked1 = "checked";
					$checked2 = "";
				}else{
					$checked1 = "";
					$checked2 = "checked";
				}
			?>

			<input name="signatory" id="signatory" type="radio" value="1" onClick="checkSignatory(this.value);" <?php echo $checked1; ?>>Doctor
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input name="signatory" id="signatory" type="radio" value="0" onClick="checkSignatory(this.value);" <?php echo $checked2; ?>>Medical Record Officer
		</td>
	</tr>
	<tr id="space5">
		<td>&nbsp;</td>
	</tr>
	<tr id="doc_sig">
		<td valign="top" bgcolor="#F8F9FA">
			Consulting/Attending Doctor :  &nbsp;&nbsp;&nbsp;
			<select name="doctors" id="doctors">
				<!--<option value='0'>-Select a doctor-</option>-->
<?php
	/*
	function print_doctors($value, $key){
		echo "				<option value='".$key."'>".$value."</option> \n";
	}
	*/
	
	#$listDoctors = array_unique($listDoctors);
	
	#if ($listDoctors){
		#array_walk($listDoctors, 'print_doctors');
		#edited by VAN 04-28-08
		##commented by VAN 06-28-08
		
		$listDoctors = array_unique($listDoctors);
		foreach($listDoctors as $key=>$value){
			if ($confCertInfo['dr_nr']==$key){
				echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
			}else{
				echo "				<option value='".$key."'>".$value."</option> \n";
			}	
		}
		
	#}
	/*
	else{
		
		if (($encInfo['encounter_class_nr']==2)&&($encInfo['encounter_type']==2)){
			# Load all  doctors in OPD
			$doctor_dept=$pers_obj->getDoctorByDept($dept_nr, 0);
			#$all_meds=&$dept_obj->getAllOPDMedicalObject(0);
		}else{
			# Load all  doctors in IPD
			$doctor_dept=$pers_obj->getDoctorByDept($dept_nr, 1);
			#$all_meds=&$dept_obj->getAllOPDMedicalObject(1);
		}
		
		echo "<option value='0'>-Select a doctor-</option>";
		while ($result = $doctor_dept->FetchRow()){
			#$doctor_name = $buffer['name_first']." ".$buffer['name_middle']." ".$buffer['name_last'];
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
			$doctor_name = $result["name_first"]." ".$result["name_2"]." ".$middleInitial.$result["name_last"];
			$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
			
			if ($dr_nr==$result['personell_nr'])
				$selected = "selected";
			else
				$selected = "";	
			
			echo "				<option value='".$result['personell_nr']."' ".$selected." >".$doctor_name."</option> \n";
		}	
	}	*/
?>
			</select>
		</td>
		
	</tr>

	<!--added by daryl-->
	<!--LIC-->
		<tr>
		<td>&nbsp;</td>
	</tr>
	<!-- <tr>
		<td valign="top" bgcolor="#F8F9FA">
			Lic. no. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;:  &nbsp;&nbsp;&nbsp;
			<input readonly type="text" name="lic_no" id="lic_no" size="40" value="<?php echo $lic_no; ?>">			
		</td>
	</tr> -->

		<!--added by daryl-->
	<!--PTR no-->
<!-- 	<tr>
		<td>&nbsp;</td>
	</tr> -->
	<!-- <tr>
		<td valign="top" bgcolor="#F8F9FA">
			PTR no. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;:  &nbsp;&nbsp;&nbsp;
			<input readonly type="text" name="ptr_no" id="ptr_no" size="40" value="<?php echo $ptr_no; ?>">			
		</td>
	</tr> -->
	<!--ended by daryl -->
	<!--ended by daryl -->

	<tr id="medrec_sig">
		<td valign="top" bgcolor="#F8F9FA">
			Medical Records Head &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:  &nbsp;&nbsp;&nbsp;
			<select name="medrec" id="medrec">
				<option value='0' selected="selected">-Default Signatory-</option>
<?php
		$medrec_dept = $pers_obj->get_AllOfficer_Dept('Department Head', '151');
		if($medrec_dept){
		while ($result = $medrec_dept->FetchRow()){
			
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
			
			$medrec_name = trim($result["name_last"]).", ".trim($result["name_first"])." ".$middleInitial; #substr(trim($drInfo["name_middle"]),0,1).$dot;
		    $medrec_name = strtoupper($medrec_name);
			
			if ($confCertInfo['dr_nr']==$result['personell_nr'])
				$selected = "selected";
			else
				$selected = "";	
			
			echo "				<option value='".$result['personell_nr']."' ".$selected." >".$medrec_name."</option> \n";
		}
	}
?>
			</select>
		</td>
		
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	
		<tr>
		<!-- <td valign="top" bgcolor="#F8F9FA">
			Nurse on Duty &nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:  &nbsp;&nbsp;&nbsp;
			<select name="nurses" id="nurses">
				<option value='0'>-Select a nurse-</option> -->
<?php
		
		// $nurses_dept=$pers_obj->getAllNurse(1);
		
		// while ($result = $nurses_dept->FetchRow()){
		// 	#$doctor_name = $buffer['name_first']." ".$buffer['name_middle']." ".$buffer['name_last'];
		// 	$middleInitial = "";
		// 	if (trim($result['name_middle'])!=""){
		// 		$thisMI=split(" ",$result['name_middle']);	
		// 		foreach($thisMI as $value){
		// 			if (!trim($value)=="")
		// 				$middleInitial .= $value[0];
		// 		}
		// 		if (trim($middleInitial)!="")
		// 			$middleInitial .= ". ";
		// 	}
		// 	$nurse_name = $result["name_first"]." ".$result["name_2"]." ".$middleInitial.$result["name_last"];
		// 	$nurse_name = ucwords(strtolower($nurse_name));
			
		// 	if ($result['personell_nr']==$confCertInfo['nurse_on_duty']){
		// 		echo "				<option value='".$result['personell_nr']."' selected=\"selected\">".$nurse_name."</option> \n";
		// 	}else{
		// 		echo "				<option value='".$result['personell_nr']."'>".$nurse_name."</option> \n";
		// 	}	
		// }	
	
?>
			<!-- </select>
		</td> -->
		
	</tr>

<!--added by daryl-->
<!--admitting diagnosis-->

<tr>
		<td>&nbsp;</td>

</tr><tr>
		<td valign="top" bgcolor="#F8F9FA">
			Admitting Diagnosis &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:  &nbsp;&nbsp;&nbsp;
	</td>
</tr>
<tr>
<td>

		


&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		 &nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<textarea cols="33" rows="5" name="admitting_diagnosis" id="admitting_diagnosis" wrap="physical"><?php echo $admitting_diagnosis;?></textarea>		
		
</td>
</tr>
<!--Operation-->

<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#F8F9FA">
			Operation Performed &nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;:  &nbsp;&nbsp;&nbsp;
			<input type="text" name="operation_performed" id="operation_performed" size="40" value="<?php echo $operation_performed; ?>">			
		</td>
	</tr>


<!--admitting diagnosis-->
<!--end daryl-->



<!-- 	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#F8F9FA">
			Attending Doctors &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;:  &nbsp;&nbsp;&nbsp;
		</td>
	</tr>
	<tr>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				 &nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<textarea cols="33" rows="5" name="attending_doctor" id="attending_doctor" wrap="physical"><?php echo $confCertInfo['attending_doctor']; ?></textarea>			</td>
	</tr> -->
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#F8F9FA">
			Purpose &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;:  &nbsp;&nbsp;&nbsp;
		</td>
	</tr>
	<tr>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				 &nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<textarea cols="33" rows="5" name="purpose" id="purpose" wrap="physical"><?php echo $confCertInfo['purpose']; ?></textarea>			</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#F8F9FA">
			Requested By &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;:  &nbsp;&nbsp;&nbsp;
			<input type="text" name="requested_by" id="requested_by" size="40" value="<?php echo $confCertInfo['requested_by']; ?>">			
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#F8F9FA">
			Relation to Patient &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;:  &nbsp;&nbsp;&nbsp;
			<input type="text" name="relation_to_patient" id="relation_to_patient" size="40" value="<?php echo $confCertInfo['relation_to_patient']; ?>">			
		</td>
	</tr>

	<!--added by daryl-->
	<!--prepared by-->
		<tr>
		<td>&nbsp;</td>
	</tr>
	<!-- <tr>
		<td valign="top" bgcolor="#F8F9FA">
			Prepared By &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp&nbsp;&nbsp;&nbsp;&nbsp;:  &nbsp;&nbsp;&nbsp;
			<input type="text" name="prepared_by" id="prepared_by" size="40" value="<?php echo $prepared_by; ?>">			
		</td>
	</tr> -->
	<!--ended by daryl -->
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center" background="images/top_05.jpg" bgcolor="#EDF2FE">
<?php
			if (!$confCertInfo || empty($confCertInfo)){
				echo '			<input type="hidden" name="mode" id="mode" value="save">'."\n";
				echo '			<input type="submit" name="Submit" value="Save">'."\n";
			}else{
				echo '			<input type="hidden" name="mode" id="mode" value="update">'."\n";
				echo '			<input type="button" name="Print" value="Print" onClick="printMedCert('.$encounter_nr.')">'."\n &nbsp; &nbsp;";
				echo '			<input type="submit" name="Submit" value="Update">'."\n";
			}
			echo '			<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">'."\n";
?>
			&nbsp; &nbsp;
			<input type="button" name="Cancel" value="Cancel"  onclick="window.close()">
			<input type="hidden" name="pid" id="pid" value="<?=$encInfo['pid']?>">
		</td>
	</tr>
	
	</form>
</table>

</body>
</html>
