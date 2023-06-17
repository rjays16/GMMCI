<?php

#created by Cherry 09-14-09
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

#Added by Cherry 08-05-10
require_once($root_path.'include/care_api_classes/class_dateGenerator.php');
$dategen = new DateGenerator;

require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_department.php');

#Added by Cherry 08-05-10
require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj = new SegRadio;

require_once($root_path.'include/care_api_classes/class_vitalsign.php');
$vital_sign = new SegVitalsign();

$dept_obj=new Department;
$pers_obj=new Personell;

if (isset($_GET['encounter_nr']) && $_GET['encounter_nr']){
		$encounter_nr = $_GET['encounter_nr'];
}
if (isset($_POST['encounter_nr']) && $_POST['encounter_nr']){
		$encounter_nr = $_POST['encounter_nr'];
}
# echo "encounter_nr= ".$encounter_nr."<br>";
#Added by Cherry 08-09-10
if (isset($_GET['refno']) && $_GET['refno']){
	$refno = $_GET['refno'];
}
if (isset($_POST['refno']) && $_POST['refno']){
	$refno = $_POST['refno'];
}
# echo "refno= ".$refno."<br>";
#echo "had_surgery= ".$_POST['had_surgery']."<br>";
include_once($root_path.'include/care_api_classes/class_cert_med.php');
$obj_medCert = new MedCertificate($encounter_nr);


$errorMsg='';

//Added by Cherry 08-12-10
global $HTTP_SESSION_VARS;

$seg_user_nr = $HTTP_SESSION_VARS['sess_temp_personell_nr'];

 if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];

 $dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);

$personell = $pers_obj->get_Personell_info($seg_user_nr);

$person_details = $pers_obj->getPersonellInfo($seg_user_name);
# echo "s = ".$dept_obj->sql;

if (stristr($personell['job_function_title'],'doctor')===FALSE)
		$is_doctor = 0;
else
		$is_doctor = 1;

		$area = $_GET['area'];
	$area_type = $_GET['area_type'];

//End Cherry


#----------------------------------------------
//$HTTP_POST_VARS['dr_nr'] = $_POST['doctors'];
 /*
if (isset($_POST['mode'])){

	if(!$_POST['had_surgery'])
		$_POST['had_surgery']='0';
	if(!$_POST['has_blood_chem'])
		$_POST['has_blood_chem']='0';
	if(!$_POST['has_xray'])
		$_POST['has_xray']='0';
	if(!$_POST['has_ultrasound'])
		$_POST['has_ultrasound']='0';
	if(!$_POST['has_ct_mri'])
		$_POST['has_ct_mri']='0';
	if(!$_POST['has_biopsy'])
		$_POST['has_biopsy']='0';

	 $data = array('encounter_nr'=>$encounter_nr,
													'subj_comp'=>$_POST['subj_comp'],
													'obj_comp'=>$_POST['obj_comp'],
													'assessment'=>$_POST['assessment'],
													'has_conscious'=>$_POST['has_conscious'],
													'did_vomit'=>$_POST['did_vomit'],
													'gcs'=>$_POST['gcs'],
													'rls'=>$_POST['rls'],
													'had_surgery'=>$_POST['had_surgery'],
													'surgery_date'=>date('Y-m-d H:i:s', strtotime($_POST['surgery_date'])),
													'surgery_proc'=>$_POST['surgery_proc'],
													'has_blood_chem'=>$_POST['has_blood_chem'],
													'has_xray'=>$_POST['has_xray'],
													'has_ultrasound'=>$_POST['has_ultrasound'],
													'has_ct_mri'=>$_POST['has_ct_mri'],
													'has_biopsy'=>$_POST['has_biopsy']

						);
	switch($_POST['mode']) {
				case 'save':
						$save = $radio_obj->saveCTHistory($data);
						if($save){
								$errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
						}else{
								$errorMsg='<font style="color:#FF0000">'."Failed to save data".'</font>';
						}
				break;
				case 'update':
						$check = $radio_obj->deleteCTHistory($encounter_nr);
						if($check){
							$update = $radio_obj->saveCTHistory($data);
						}

						if ($update){
								$errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated sucessfully!".'</font>';
						}else{
								$errorMsg='<font style="color:#FF0000">'.$obj_medCert->getErrorMsg().'</font>';
						}
				break;
		}# end of switch statement
}  */

//-----------Added by Cherry 08-12-10------------

if ($_GET['dept_nr'])
	$_POST['dept_nr'] = $_GET['dept_nr'];

if ($_GET['dr_nr'])
	$_POST['dr_nr'] = $_GET['dr_nr'];

if (empty($_POST['systole']))
	$_POST['systole'] = 'NULL';

if (empty($_POST['diastole']))
	$_POST['diastole'] = 'NULL';

if (empty($_POST['temp']))
	$_POST['temp'] = 'NULL';

if (empty($_POST['weight']))
	$_POST['weight'] = 'NULL';

if (empty($_POST['resp_rate']))
	$_POST['resp_rate'] = 'NULL';

if (empty($_POST['pulse_rate']))
	$_POST['pulse_rate'] = 'NULL';

if (isset($_POST['mode'])){

	 $data_encounter = array(
														'chief_complaint'=>$_POST['chief_complaint'],
														'consulting_dept_nr'=>$_POST['dept_nr'],
														'consulting_dr_nr'=>$_POST['doc_nr'],
														'history'=>"CONCAT('Created ',NOW(),' ','{$_SESSION['sess_user_name']}','\n')",
														'modify_id'=>$_SESSION['sess_user_name'],
														'modify_time'=>'NOW()',
						);

	 $data_vitalsigns = array('encounter_nr'=>$encounter_nr,
														'pid'=>$pid,
														'systole'=>$_POST['systole'],
														'diastole'=>$_POST['diastole'],
														'temp'=>$_POST['temp'],
														'weight'=>$_POST['weight'],
														'resp_rate'=>$_POST['resp_rate'],
														'pulse_rate'=>$_POST['pulse_rate'],
														'bp_unit'=>'5',
														'temp_unit'=>'1',
														'weight_unit'=>'6',
														'rr_unit'=>'4',
														'pr_unit'=>'3',
														'date'=>'NOW()',
														'history'=>"CONCAT('Created ',NOW(),' ','{$_SESSION['sess_user_name']}','\n')",
														'modify_id'=>$_SESSION['sess_user_name'],
														'modify_dt'=>'NOW()',
														'create_id'=>$_SESSION['sess_user_name'],
														'create_dt'=>'NOW()',
		);

	switch($_POST['mode']) {
				case 'save':
						$save1 = $enc_obj->updateConsulation($data_encounter, $encounter_nr);
						#echo $enc_obj->sql;
						if(!$vs)
						$save2 = $vital_sign->add_new_vital_sign($data_vitalsigns);
						#echo "<br>dd = ".$vital_sign->sql;
						if($save1 && $save2){
						//if($save2){
								$errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved successfully!".'</font>';
						}else{
								$errorMsg='<font style="color:#FF0000">'."Failed to save data".'</font>';
						}
				break;
				case 'update':
						//$check = $radio_obj->deleteCTHistory($encounter_nr);
						//if($check){
						//	$update = $radio_obj->saveCTHistory($data);
						//}
						$check = $vital_sign->deleteVitalSignEncounter($encounter_nr);
						if($check){
							$update2 =  $vital_sign->add_new_vital_sign($data_vitalsigns);
						}
						$update1 = $enc_obj->updateConsulation($data_encounter, $encounter_nr);


						if ($update1 && $update2){
								$errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated successfully!".'</font>';
						}else{
								$errorMsg='<font style="color:#FF0000">'.$obj_medCert->getErrorMsg().'</font>';
						}
				break;
		}# end of switch statement
}

//-------------------End Cherry------------------


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

//$confCertInfo = $obj_medCert->getConfCertRecord($encounter_nr);
//$reinstateCertInfo = $obj_medCert->getReinstatementCertRecord($encounter_nr);
$cthistoryInfo = $radio_obj->getCTHistory($encounter_nr);   //Added by Cherry 08-05-10
#$medico_cases = $enc_obj->getMedicoCases();
$vs = $vital_sign->get_latest_vital_signs($encInfo['pid'], $encounter_nr);
//print_r($vs);
//echo "ss= ".$encInfo['consulting_dr_nr'];
//print_r($encInfo);

#echo "encInfo['encounter_type'] = '".$encInfo['encounter_type']."' <br> \n";
$listDoctors=array();

#echo "encInfo['current_dept_nr'] = '".$encInfo['current_dept_nr']."' <br> \n";
#added by VAN 06-28-08
	if ($encInfo['current_dept_nr'])
		$dept_nr = $encInfo['current_dept_nr'];
	else
		$dept_nr = $encInfo['consulting_dept_nr'];


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

				if ($F('purpose')==''){
						alert(" Please enter the purpose of requesting the certificate.");
						$('purpose').focus();
						return false;
				}

				return true;
		}

		//Added by Cherry 08-05-10
		function viewResult(batch_nr,pid,dept_nr,rep){
			if(rep==1)
				window.open('<?=$root_path?>modules/laboratory/seg-lab-result-pdf.php?pid='+pid,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
			else
				window.open('<?=$root_path?>modules/radiology/certificates/seg-radio-unified-report-pdf.php?batch_nr='+batch_nr+'&pid='+pid+'&dept_nr='+dept_nr,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
			//window.open("cert_reinstatement_pdf.php?id="+id,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
			//window.open('<?=$root_path?>modules/laboratory/seg-lab-result-pdf.php?pid='+pid,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');

		}

		function key_check(e, value){
			if((e.keyCode>=48 && e.keyCode<=57) || (e.keyCode==8) || ((e.keyCode==110)||(e.keyCode==190)) || (e.keyCode>=96 && e.keyCode<=105)){
			return true;
			}else
			return false;
		}

		function key_check2(e, value){
			if((e.keyCode>=48 && e.keyCode<=57) || (e.keyCode==8) || (e.keyCode>=96 && e.keyCode<=105)){
			return true;
			}else
			return false;
		}




		//End Cherry

		function closing(){
			 alert('hello');
			//close = <td align="RIGHT"><a href="javascript:return '+fnRef+'cClick();" '+closeevent+'="return '+fnRef+'cClick();" style="color: '+o3_closecolor+'; font-family: '+o3_closefont+'; font-size: '+o3_closesize+o3_closesizeunit+'; text-decoration: '+o3_closedecoration+'; font-weight: '+o3_closeweight+'; font-style:'+o3_closestyle+';">'+close+'</a></td>';
			cClick();
		}

		function checkType(thisType){

				type = thisType;
		}



		function preset(){
			//alert('HOY!');
			var d = document.clinical_history;
			//var encounter_nr = window.parent.$('encounter_nr').value;
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
//    alert("seg_setValidDate : seg_validDate ='"+seg_validDate+"'");
}

function closeWindow(){
	window.parent.pSearchClose();
}

var seg_validTime=false;
function setFormatTime(thisTime,AMPM){
//    var time = $('time_text_d');
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
		}else    if((hour > 12)&&(hour < 24)){
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
								//<body onLoad="preset();"
</script>
</head>

<body onLoad="preset();">
<table class="segPanel" width="520" height="236" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
				<td colspan="*"><?= $errorMsg ?></td>
		</tr>
		<tr>
				<td class="segPanel" colspan="*" height="23">
						<table width="100%" border="0">
								<tr>
										<td width="18%"></td>
										<td width="37%" >&nbsp;</td>
										<!--<td width="28%" align="right" ><? echo 'Case No. '?></td>
										<td width="17%" align="left"><? echo $encounter_nr; ?></td>  -->
										<td width="28%" align="right"></td>
										<td width="17%" align="left"></td>
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
												<!--<span class="style3"><? echo "Age :  "?></span><? echo $encInfo['age'].' old';?>  -->

										</td>
										<td>&nbsp;</td>
								</tr>
								<!--<tr>
										<td><span class="style3"><? echo "Age :  "?></span><? echo $encInfo['age'].' old';?></td>
										<td><span class="style2"><? echo "Sex :  "?></span><? echo $encInfo['sex']?></td>
										<!--<td><span class="style3"><? echo "Address :"?></span></td>
										<td colspan="2" class="style2">
												<? echo stripslashes(strtoupper($encInfo['street_name']))."&nbsp;&nbsp; ".stripslashes(strtoupper($encInfo['brgy_name']))."&nbsp;&nbsp; ".stripslashes(strtoupper($encInfo['mun_name'])). ", ".stripslashes(strtoupper($encInfo['prov_name'])). "&nbsp;&nbsp;".stripslashes(strtoupper($encInfo['zipcode']));?>
										</td>
										<td>&nbsp;</td>
								</tr> -->
							<tr>
										<td><span class="style3"><? echo "Age :"?> </span></td>
										<td colspan="2" class="style2">
												<?
														echo $encInfo['age'].' old';

												?>  <? #echo stripslashes(strtoupper($encInfo['name_last']));?>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<span class="style3"><? echo "Sex :  "?></span><?
													if($encInfo['sex']=='f')
														echo "Female";
													else
														echo "Male";

													?>

										</td>
										<td>&nbsp;</td>
								</tr>

							 <tr>
										<td><span class="style3"><? echo "Hospital # :"?> </span></td>
										<td colspan="2" class="style2">
												<?
													echo $encInfo['pid'];
												?>  <? #echo stripslashes(strtoupper($encInfo['name_last']));?>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												&nbsp;&nbsp;
												<span class="style3"><? echo "Case # :  "?></span><? echo $encInfo['encounter_nr'];?>

										</td>
										<td>&nbsp;</td>
								</tr>

						</table>
				</td>
		</tr>
		<tr>
				<td colspan="*" align="left" valign="top">&nbsp;</td>
		</tr>
		<tr>
				<td colspan="*" width="467" height="23">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
										<td width="15" align="left" background="images/top_05.jpg">&nbsp;</td>
										<td width="442" background="images/top_05.jpg">&nbsp;</td>
										<td width="10" background="images/top_05.jpg" >&nbsp;</td>
								</tr>
						</table>
				</td>
		</tr>
		<form id="clinical_history" name="clinical_history" method="post" action="" onSubmit="return chkForm()">
		<?php

						if ($result_diagnosis = $objDRG->getDiagnosisCodes($encounter_nr,$encInfo['encounter_type'])){
								$rowsDiagnosis = $result_diagnosis->RecordCount();
								while($temp=$result_diagnosis->FetchRow()){

								}
						}

						if ($result_therapy = $objDRG->getProcedureCodes($encounter_nr,$encInfo['encounter_type'])){
								$rowsTherapy = $result_therapy->RecordCount();
								while($temp=$result_therapy->FetchRow()){


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



		?>


		<tr id="space5">
				<td>&nbsp;</td>
		</tr>
		<tr>
				<td valign="top">
						<span class="style3">Diagnosis</span>
				</td>
		</tr>
		<tr>
						<td align="right">
								<textarea cols="43" rows="5" name="chief_complaint" id="chief_complaint" wrap="physical"><?php echo $encInfo['chief_complaint']; ?></textarea>            </td>

		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
				<td colspan="*" height="23">
					<table width="100%" border="0">
								<tr>
										<td width="35%" >
										<span class="style3">Vital Signs:</span> </td>
										<td width="20%" >&nbsp;</td>
										<td width="25%" align="left"></td>

								</tr>
								<tr>
									<td width="35%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style3">Blood Pressure:</span>

									</td>

									<td width="50%" align="left">
										<input type="text" size="10" id="systole" maxlength="3" name="systole" onkeydown="return key_check2(event, this.value)" value="<? echo $vs['systole']; ?>">&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;
										<input type="text" size="10" id="diastole" maxlength="3" name="diastole" onkeydown="return key_check2(event, this.value)" value="<?= $vs['diastole'];?>">
									</td>

								</tr>

								<tr>
									<td width="35%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style3">Temperature (T)</span></td>
									<td width="50%" align="left">
										<input type="text" size="10" id="temp" maxlength="5" name="temp" onkeydown="return key_check(event, this.value)" value="<?=$vs['temp'];?>">
									</td>
								</tr>

								 <tr>
									<td width="35%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style3">Weight (W)</span></td>
									<td width="50%" align="left">
										<input type="text" size="10" id="weight" maxlength="6"  name="weight" onkeydown="return key_check(event, this.value)" value="<?=$vs['weight']; ?>">
									</td>
								</tr>

								 <tr>
									<td width="35%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style3">Resp. Rate (RR)</span></td>
									<td width="50%" align="left">
										<input type="text" size="10" id="resp_rate" maxlength="3" name="resp_rate" onkeydown="return key_check2(event, this.value)" value="<?=$vs['resp_rate']; ?>">
									</td>
								</tr>

								 <tr>
									<td width="35%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style3">Pulse Rate (PR)</span></td>
									<td width="50%" align="left">
										<input type="text" size="10" id="pulse_rate" maxlength="3" name="pulse_rate" onkeydown="return key_check2(event, this.value)" value="<?=$vs['pulse_rate'];?>">
									</td>
								</tr>

					 </table>
				</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<td>Consulting Doctor: &nbsp;&nbsp;&nbsp;&nbsp;
				<input type="text" size="30" id="doc_name" name="doc_name" value="<?=$HTTP_SESSION_VARS["sess_user_name"]; ?>" readonly="readonly">
				<br>
				<input type="hidden" size="30" id="doc_nr" name="doc_nr" value="<?=$seg_user_nr; ?>">
				<br>
				<input type="hidden" size="30" id="dept_nr" name="dept_nr" value="<?= $person_details['location_nr']?>">
			</td>
		</tr>

		<tr>
				<td align="center" background="images/top_05.jpg">
<?php
						if ($encInfo['consulting_dr_nr']=='0' || empty($vs) || !$encInfo['chief_complaint']){
								echo '            <input type="hidden" name="mode" id="mode" value="save">'."\n";
								echo '            <input type="submit" name="Submit" value="Save">'."\n";
						}else{
								echo '            <input type="hidden" name="mode" id="mode" value="update">'."\n";
								//echo '            <input type="button" name="Print" value="Print" onClick="printCTHistory('.$encounter_nr.', '.$pid.')">'."\n &nbsp; &nbsp;";
								echo '            <input type="submit" name="Submit" value="Update">'."\n";
						}
						echo '            <input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">'."\n";
						echo '						<input type="hidden" name="refno" id="refno" value="'.$refno.'">'."\n";
?>
						&nbsp; &nbsp;
						<!--<input type="button" name="Cancel" value="Cancel"  onclick="cClick();"-->
						<input type="hidden" name="pid" id="pid" value="<?=$encInfo['pid']?>">
						<!--<input type="text" name="refno" id="refno" value="<?=$refno?>"> -->
				</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>

		</form>
</table>

</body>
</html>
