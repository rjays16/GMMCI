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

#----------------------------------------------
//$HTTP_POST_VARS['dr_nr'] = $_POST['doctors'];

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

//$confCertInfo = $obj_medCert->getConfCertRecord($encounter_nr);
//$reinstateCertInfo = $obj_medCert->getReinstatementCertRecord($encounter_nr);
$cthistoryInfo = $radio_obj->getCTHistory($encounter_nr);   //Added by Cherry 08-05-10
$medico_cases = $enc_obj->getMedicoCases();

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

		function showSurgicalDetails(){
			if(document.clinical_history.had_surgery.checked==true){
				document.getElementById('proc_date').style.display = '';
				document.getElementById('proc').style.display='';
			}else{
				document.getElementById('proc_date').style.display = 'none';
				document.getElementById('proc').style.display = 'none';
			}
		}

		function showLabDetails(){
				if(document.clinical_history.laboratory.checked==true){
				document.getElementById('show_lab_date').style.display = '';
				document.getElementById('show_lab_result').style.display = '';
				document.getElementById('biopsy').style.display = '';
				document.getElementById('ct_mri').style.display='';
				document.getElementById('ultrasound').style.display='';
				document.getElementById('xray').style.display='';
				document.getElementById('blood_chem').style.display='';
			}else{

				document.clinical_history.has_biopsy.checked =false;
				document.clinical_history.has_ct_mri.checked=false;
				document.clinical_history.has_ultrasound.checked=false;
				document.clinical_history.has_xray.checked=false;
				document.clinical_history.has_blood_chem.checked=false;

				document.getElementById('show_lab_date').style.display = 'none';
				document.getElementById('show_lab_result').style.display = 'none';
				document.getElementById('biopsy').style.display = 'none';
				document.getElementById('biopsy1').style.display = 'none';
				document.getElementById('biopsy2').style.display = 'none';
				document.getElementById('biopsy3').style.display = 'none';
				document.getElementById('ct_mri').style.display = 'none';
				document.getElementById('ct_mri1').style.display = 'none';
				document.getElementById('ct_mri2').style.display = 'none';
				document.getElementById('ct_mri3').style.display = 'none';
				document.getElementById('ultrasound').style.display = 'none';
				document.getElementById('ultrasound1').style.display = 'none';
				document.getElementById('ultrasound2').style.display = 'none';
				document.getElementById('ultrasound3').style.display = 'none';
				document.getElementById('xray').style.display = 'none';
				document.getElementById('xray1').style.display = 'none';
				document.getElementById('xray2').style.display = 'none';
				document.getElementById('xray3').style.display = 'none';
				document.getElementById('blood_chem').style.display = 'none';
				document.getElementById('blood_chem1').style.display = 'none';
				document.getElementById('blood_chem2').style.display = 'none';
				document.getElementById('blood_chem3').style.display = 'none';

			}
		}

		function viewBiopsyDetails(){
			if(document.clinical_history.has_biopsy.checked==true){
				document.getElementById('biopsy1').style.display='';
				document.getElementById('biopsy2').style.display='';
				document.getElementById('biopsy3').style.display='';
			}else{
				document.getElementById('biopsy1').style.display='none';
				document.getElementById('biopsy2').style.display='none';
				document.getElementById('biopsy3').style.display='none';
			}
		}

		function viewCTDetails(){
			if(document.clinical_history.has_ct_mri.checked==true){
				document.getElementById('ct_mri1').style.display='';
				document.getElementById('ct_mri2').style.display='';
				document.getElementById('ct_mri3').style.display='';
			}else{
				document.getElementById('ct_mri1').style.display='none';
				document.getElementById('ct_mri2').style.display='none';
				document.getElementById('ct_mri3').style.display='none';
			}
		}

		function viewUltrasoundDetails(){
			if(document.clinical_history.has_ultrasound.checked==true){
				document.getElementById('ultrasound1').style.display='';
				document.getElementById('ultrasound2').style.display='';
				document.getElementById('ultrasound3').style.display='';
			}else{
				document.getElementById('ultrasound1').style.display='none';
				document.getElementById('ultrasound2').style.display='none';
				document.getElementById('ultrasound3').style.display='none';
			}
		}

		function viewXrayDetails(){
			if(document.clinical_history.has_xray.checked==true){
				document.getElementById('xray1').style.display='';
				document.getElementById('xray2').style.display='';
				document.getElementById('xray3').style.display='';
			}else{
				document.getElementById('xray1').style.display='none';
				document.getElementById('xray2').style.display='none';
				document.getElementById('xray3').style.display='none';
			}
		}

		function viewBloodChemDetails(){
			if(document.clinical_history.has_blood_chem.checked==true){
				document.getElementById('blood_chem1').style.display='';
				document.getElementById('blood_chem2').style.display='';
				document.getElementById('blood_chem3').style.display='';
			}else{
				document.getElementById('blood_chem1').style.display='none';
				document.getElementById('blood_chem2').style.display='none';
				document.getElementById('blood_chem3').style.display='none';
			}
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

		function printMedCert(id){


						window.open("cert_reinstatement_pdf.php?id="+id,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
		}

		//Added by Cherry 08-09-10
		function printCTHistory(encounter_nr,pid){
			//alert('try lang');
				window.open("seg-radio-ct-history-pdf.php?encounter_nr="+encounter_nr+"&pid="+pid,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
		}


		function preset(){
			//alert('HOY!');
				var d = document.clinical_history;

				var encounter_nr = window.parent.$('encounter_nr').value;

				//alert(encounter_nr);

				if(d.had_surgery.checked==true){
					 document.getElementById('proc_date').style.display = '';
					 document.getElementById('proc').style.display = '';
				}else{
					document.getElementById('proc_date').style.display = 'none';
					document.getElementById('proc').style.display = 'none';
				}

				if(d.laboratory.checked==true){
					document.getElementById('show_lab_date').style.display = '';
					document.getElementById('show_lab_result').style.display = '';
					document.getElementById('biopsy').style.display = '';
					document.getElementById('ct_mri').style.display = '';
					document.getElementById('ultrasound').style.display = '';
					document.getElementById('xray').style.display = '';
					document.getElementById('blood_chem').style.display = '';

						if(d.has_biopsy.checked==true){
							document.getElementById('biopsy1').style.display = '';
							document.getElementById('biopsy2').style.display = '';
							document.getElementById('biopsy3').style.display = '';
						}else{
							document.getElementById('biopsy1').style.display = 'none';
							document.getElementById('biopsy2').style.display = 'none';
							document.getElementById('biopsy3').style.display = 'none';
						}

						if(d.has_ct_mri.checked==true){
							document.getElementById('ct_mri1').style.display = '';
							document.getElementById('ct_mri2').style.display = '';
							document.getElementById('ct_mri3').style.display = '';
						}else{
							document.getElementById('ct_mri1').style.display = 'none';
							document.getElementById('ct_mri2').style.display = 'none';
							document.getElementById('ct_mri3').style.display = 'none';
						}

						if(d.has_ultrasound.checked==true){
							document.getElementById('ultrasound1').style.display = '';
							document.getElementById('ultrasound2').style.display = '';
							document.getElementById('ultrasound3').style.display = '';
						}else{
							document.getElementById('ultrasound1').style.display = 'none';
							document.getElementById('ultrasound2').style.display = 'none';
							document.getElementById('ultrasound3').style.display = 'none';
						}

						if(d.has_xray.checked==true){
							document.getElementById('xray1').style.display = '';
							document.getElementById('xray2').style.display = '';
							document.getElementById('xray3').style.display = '';
						}else{
							document.getElementById('xray1').style.display = 'none';
							document.getElementById('xray2').style.display = 'none';
							document.getElementById('xray3').style.display = 'none';
						}

						if(d.has_blood_chem.checked==true){
							document.getElementById('blood_chem1').style.display = '';
							document.getElementById('blood_chem2').style.display = '';
							document.getElementById('blood_chem3').style.display = '';
						}else{
							document.getElementById('blood_chem1').style.display = 'none';
							document.getElementById('blood_chem2').style.display = 'none';
							document.getElementById('blood_chem3').style.display = 'none';
						}

				}else{
					document.getElementById('show_lab_date').style.display = 'none';
					document.getElementById('show_lab_result').style.display = 'none';
					document.getElementById('biopsy').style.display = 'none';
					document.getElementById('biopsy1').style.display = 'none';
					document.getElementById('biopsy2').style.display = 'none';
					document.getElementById('biopsy3').style.display = 'none';
					document.getElementById('ct_mri').style.display = 'none';
					document.getElementById('ct_mri1').style.display = 'none';
					document.getElementById('ct_mri2').style.display = 'none';
					document.getElementById('ct_mri3').style.display = 'none';
					document.getElementById('ultrasound').style.display = 'none';
					document.getElementById('ultrasound1').style.display = 'none';
					document.getElementById('ultrasound2').style.display = 'none';
					document.getElementById('ultrasound3').style.display = 'none';
					document.getElementById('xray').style.display = 'none';
					document.getElementById('xray1').style.display = 'none';
					document.getElementById('xray2').style.display = 'none';
					document.getElementById('xray3').style.display = 'none';
					document.getElementById('blood_chem').style.display = 'none';
					document.getElementById('blood_chem1').style.display = 'none';
					document.getElementById('blood_chem2').style.display = 'none';
					document.getElementById('blood_chem3').style.display = 'none';
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
		<!--<tr>
				<td valign="top" bgcolor="#F8F9FA">
						<strong>S</strong> (Subjective complaints):  &nbsp;&nbsp;&nbsp;

				</td>
				<textarea></textarea>   -->
		<tr>
				<td valign="top" bgcolor="#F8F9FA">
						S: (Subjective complaints)
				</td>
		</tr>
		<tr>
						<td align="right">
								<textarea cols="43" rows="5" name="subj_comp" id="subj_comp" wrap="physical"><?php echo $cthistoryInfo['subj_comp']; ?></textarea>            </td>
		</tr>
		<tr>
				<td>&nbsp;</td>
		</tr>

		<tr>
				<td valign="top" bgcolor="#F8F9FA">
						O: (pertinent PE findings)
				</td>
		</tr>
		<tr>
						<td align="right">
								<textarea cols="43" rows="5" name="obj_comp" id="obj_comp" wrap="physical"><?php echo $cthistoryInfo['obj_comp']; ?></textarea>            </td>
		</tr>
		<tr>
				<td>&nbsp;</td>
		</tr>

		<tr>
			<td valign="top" bgcolor="#F8F9FA">
				A: Assessment
			</td>
		</tr>
		<tr>
			<td align="right">
				<textarea cols="43" rows="5" name="assessment" id="assessment" wrap="physical"><?php echo $cthistoryInfo['assessment']; ?></textarea>
			</td>
		</tr>

		<tr>
			<td>
				<?php
					if($cthistoryInfo){
						if($cthistoryInfo['had_surgery']!="0")
							$checked = "checked";
					}else{
						$checked = "";
					}
				?>
				<!--<input type="checkbox" name="had_surgery" id="had_surgery" value="1" onclick="showSurgicalDetails()"> -->
				<input type="checkbox" name="had_surgery" id="had_surgery" value="1" onclick="showSurgicalDetails();" <?php echo $checked; //echo "<script language='javascript'>document.getElementById('proc').style.show='';<script>"?>>
				&nbsp;&nbsp;Surgical Procedure Done
			</td>
		</tr>
		<tr id="proc_date">
			<td>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<!--<input type="text" name="from_date" id="from_date" value="">
			<img src="images/show-calendar.gif" id="to_date_trigger" align="absmiddle" style="cursor:pointer">
			<!--<input type="button" src="images/show-cale">-->

		<?php
							$phpfd=$date_format;
							$phpfd=str_replace("dd", "%d", strtolower($phpfd));
							$phpfd=str_replace("mm", "%m", strtolower($phpfd));
							$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
							/*
							if (($medCertInfo['consultation_date']!='0000-00-00') && ($medCertInfo['consultation_date']!=""))
								$consultation_date= @formatDate2Local($medCertInfo['consultation_date'],$date_format);
							else
								$consultation_date = @formatDate2Local($encInfo['encounter_date'],$date_format);  */

							if (($cthistoryInfo['surgery_date']!='0000-00-00') && ($cthistoryInfo['surgery_date']!=""))
								$surgery_date= @formatDate2Local($cthistoryInfo['surgery_date'],$date_format);
							else
								$surgery_date = @formatDate2Local($cthistoryInfo['surgery_date'],$date_format);

							$sDateJS= '<input name="surgery_date" type="text" size="15" maxlength=10 value="'.$surgery_date.'"'.
								'onFocus="this.select();"
								id = "surgery_date"
								onBlur="IsValidDate(this,\''.$date_format.'\'); "
								onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
								<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="surgery_date_trigger" style="cursor:pointer" >
								<font size=1>[';
								ob_start();
						?>
									<script type="text/javascript">
							Calendar.setup ({
								inputField : "surgery_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "surgery_date_trigger", singleClick : true, step : 1
							});
								</script>
												<?php
							$calendarSetup = ob_get_contents();
							ob_end_clean();

							$sDateJS .= $calendarSetup;
							/**/
							$dfbuffer="LD_".strtr($date_format,".-/","phs");
							$sDateJS = $sDateJS.$$dfbuffer.']';
						?>
						Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;
												<?= $sDateJS ?>
											</span>

			</td>
		</tr>
		<tr id="proc">
			<td>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Procedure&nbsp;&nbsp;: &nbsp;
				<textarea id="surgery_proc" name="surgery_proc"><?php echo $cthistoryInfo['surgery_proc'] ?></textarea>
			</td>
		</tr>

		<tr>
				<td>&nbsp;</td>
		</tr>
	<!--
		<tr>
			<td colspan="1">
				<input type="checkbox" name="laboratory" value="1" onclick="showLabDetails()">
				&nbsp;&nbsp;Laboratory Work-Up Done
			</td>
			<td>Date
			</td>
		</tr>   -->

		<tr>
				<td colspan="*" height="23" bgcolor="#FFFFFF" >
					<?php
					global $db;

					$sql_date_lab = "SELECT s.encounter_nr, ss.date_served FROM seg_lab_serv AS s
									LEFT JOIN seg_lab_servdetails AS ss ON ss.refno = s.refno
									WHERE s.encounter_nr = '".$encInfo['encounter_nr']."'
									ORDER BY date_served DESC;";
				$result_date_lab = $db->Execute($sql_date_lab);
				$row_date_lab = $result_date_lab->FetchRow();

				if($row_date_lab['date_served']){
					$date_blood_chem = date("m/d/y", strtotime($row_date_lab['date_served']));
					$date_biopsy = date("m/d/y", strtotime($row_date_lab['date_served']));
				}else{
					$date_blood_chem="";
					$date_biopsy="";
				}

				$sql_date_xray = "SELECT sr.refno, cr.service_date FROM seg_radio_serv AS sr
													LEFT JOIN care_test_request_radio AS cr ON cr.refno = sr.refno
													LEFT JOIN seg_radio_services AS srs ON srs.service_code = cr.service_code
													LEFT JOIN seg_radio_service_groups AS g ON g.group_code = srs.group_code
													WHERE sr.encounter_nr = '".$encInfo['encounter_nr']."'
													AND g.department_nr = '164'
													ORDER BY cr.service_date DESC;";
				$result_date_xray = $db->Execute($sql_date_xray);
				$row_date_xray = $result_date_xray->FetchRow();

				if($row_date_xray['service_date']){
					$date_xray = date("m/d/y", strtotime($row_date_xray['service_date']));
				}else{
					 $date_xray = "";
				}

				$sql_date_ultrasound= "SELECT sr.refno, cr.service_date FROM seg_radio_serv AS sr
													LEFT JOIN care_test_request_radio AS cr ON cr.refno = sr.refno
													LEFT JOIN seg_radio_services AS srs ON srs.service_code = cr.service_code
													LEFT JOIN seg_radio_service_groups AS g ON g.group_code = srs.group_code
													WHERE sr.encounter_nr = '".$encInfo['encounter_nr']."'
													AND g.department_nr = '165'
													ORDER BY cr.service_date DESC;";
				$result_date_ultrasound = $db->Execute($sql_date_ultrasound);
				$row_date_ultrasound = $result_date_ultrasound->FetchRow();

				if($row_date_ultrasound['service_date']){
					$date_ultrasound = date("m/d/y", strtotime($row_date_ultrasound['service_date']));
				}else{
					 $date_ultrasound="";
				}

					$sql_date_ct_mri= "SELECT sr.refno, cr.service_date FROM seg_radio_serv AS sr
													LEFT JOIN care_test_request_radio AS cr ON cr.refno = sr.refno
													LEFT JOIN seg_radio_services AS srs ON srs.service_code = cr.service_code
													LEFT JOIN seg_radio_service_groups AS g ON g.group_code = srs.group_code
													WHERE sr.encounter_nr = '".$encInfo['encounter_nr']."'
													AND g.department_nr = '165'
													ORDER BY cr.service_date DESC;";
				$result_date_ct_mri = $db->Execute($sql_date_ct_mri);
				$row_date_ct_mri = $result_date_ct_mri->FetchRow();

				if($row_date_ct_mri['service_date']){
					$date_ct_mri = date("m/d/y", strtotime($row_date_ct_mri['service_date']));
				}else{
					 $date_ct_mri="";
				}

					$cnt = 0;

						if($cthistoryInfo){
							if($cthistoryInfo['has_blood_chem']=='1'){
								$checked_has_blood_chem = "checked";
								$cnt++;
							}
							if($cthistoryInfo['has_xray']=='1'){
								$checked_has_xray = "checked";
								$cnt++;
							}
							if($cthistoryInfo['has_ultrasound']=='1'){
								$checked_has_ultrasound = "checked";
								$cnt++;
							}
							if($cthistoryInfo['has_ct_mri']=='1'){
								$checked_has_ct_mri = "checked";
								$cnt++;
							}
							if($cthistoryInfo['has_biopsy']){
								$checked_has_biopsy = "checked";
								$cnt++;
							}
							if($cnt!=0)
								$checked_has_lab = "checked";
						}else{
								$checked_has_blood_chem = "";
								$checked_has_xray = "";
								$checked_has_ultrasound = "";
								$checked_has_ct_mri = "";
								$checked_has_biopsy = "";
								$checked_has_lab = "";
						}

					?>

						<table width="100%" border="0" bgcolor="#F8F9FA"class="style2">
								<tr>
										<td width="35%" >
										<input type="checkbox" name="laboratory" value="1" onclick="showLabDetails();" <?php echo $checked_has_lab; ?>>
										Laboratory Work-Up Done </td>
										<td width="20%" >&nbsp;</td>
										<td width="25%" align="left" id="show_lab_date">Date</td>
										<td width="20%" align="left" id="show_lab_result">Result</td>
								</tr>
								<tr>
									<td width="35%" id="blood_chem">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="checkbox" name="has_blood_chem" id="has_blood_chem" value="1" onclick="viewBloodChemDetails()" <?php echo $checked_has_blood_chem; ?>>Blood Chemistry
									</td>
									<td width="20%" id="blood_chem1">&nbsp;</td>
									<td width="25%" align="left" id="blood_chem2"><?php echo $date_blood_chem; ?></td>
									<td width="20%" align="left" id="blood_chem3">
										<img src="images/chart_icon.jpg" onclick="viewResult(<?=$refno?>,<?=$encInfo['pid']?>,156,1)">
									</td>
								</tr>
								<tr>
									<td width="35%" id="xray">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<input type="checkbox" name="has_xray" id="has_xray" value="1" onclick="viewXrayDetails()" <?php echo $checked_has_xray; ?>>X-ray <br>
									</td>
									<td width="20%" id="xray1">&nbsp;</td>
									<td width="25%" align="left" id="xray2"><?php echo $date_xray; ?> </td>
									<td width="20%" align="left" id="xray3">
										<img src="images/photo.png" onclick="viewResult(<?=$refno?>,<?=$encInfo['pid']?>,164,2)">
									</td>
								</tr>
								<tr>
									<td width="35%" id="ultrasound">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="checkbox" name="has_ultrasound" id="has_ultrasound" value="1" onclick="viewUltrasoundDetails()" <?php echo $checked_has_ultrasound; ?>>Ultrasound
									</td>
									<td width="20%" id="ultrasound1">&nbsp;</td>
									<td width="25%" align="left" id="ultrasound2"><?php echo $date_ultrasound; ?> </td>
									<td width="20%" align="left" id="ultrasound3">
										<img src="images/bilder.gif" onclick="viewResult(<?=$refno?>, <?=$encInfo['pid']?>, 165, 2)">
									</td>
								</tr>
								<tr>
									<td width="35%" id="ct_mri">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="checkbox" name="has_ct_mri" id="has_ct_mri" value="1" onclick="viewCTDetails()" <?php echo $checked_has_ct_mri; ?>>CT/MRI
									</td>
									<td width="20%" id="ct_mri1">&nbsp;</td>
									<td width="25%" align="left" id="ct_mri2"><?php echo $date_ct_mri ?></td>
									<td width="20%" align="left" id="ct_mri3">
										<img src="images/Appointment.gif" onclick="viewResult(<?=$refno?>,<?=$encInfo['pid']?>,166,2)">
									</td>
								</tr>
								<tr>
									<td width="35%" id="biopsy">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="checkbox" name="has_biopsy" id="has_biopsy" value="1" onclick="viewBiopsyDetails()" <?php echo $checked_has_biopsy; ?>>Biopsy
									</td>
									<td width="20%" id="biopsy1">&nbsp;</td>
									<td width="25%" align="left" id="biopsy2"><?php $date_biopsy; ?></td>
									<td width="20%" align="left" id="biopsy3">
										<img src="images/torso_br.gif" onclick="viewResult(<?=$refno?>,<?=$encInfo['pid']?>,156,1)">
									</td>
								</tr>
								<!--<tr>
									<td>
											<input type="checkbox" name="has_blood_chem" id="has_blood_chem" value="1">Blood Chemistry <br>
											<input type="checkbox" name="has_xray" id="has_xray" value="1">X-ray <br>
											<input type="checkbox" name="has_ultrasound" id="has_ultrasound" value="1">Ultrasound <br>
											<input type="checkbox" name="has_ct_mri" id="has_ct_mri" value="1">CT/MRI<br>
											<input type="checkbox" name="has_biopsy" id="has_biopsy" value="1">Biopsy<br>

									</td>
							</tr>-->

						</table>
				</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>
	 <!--
		<tr>
			<td>
				<input type="checkbox" name="has_blood_chem" id="has_blood_chem" value="1">Blood Chemistry <br>
				<input type="checkbox" name="has_xray" id="has_xray" value="1">X-ray <br>
				<input type="checkbox" name="has_ultrasound" id="has_ultrasound" value="1">Ultrasound <br>
				<input type="checkbox" name="has_ct_mri" id="has_ct_mri" value="1">CT/MRI<br>
				<input type="checkbox" name="has_biopsy" id="has_biopsy" value="1">Biopsy<br>

			</td>
		</tr>  -->

		<tr>
			<td>
				<?php
					if($cthistoryInfo){
						if($cthistoryInfo['has_conscious']=='1')
							$checked_has_conscious = "checked";
						if($cthistoryInfo['did_vomit']=='1')
							$checked_did_vomit = "checked";
					}

				?>

				<input type="checkbox" name="has_conscious" id="has_conscious" value="1" <?php echo $checked_has_conscious; ?>>
				Loss of consciousness<br>
				<input type="checkbox" name="did_vomit" id="did_vomit" value="1" <?php echo $checked_did_vomit; ?>>
				Vomiting<br>
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<td>GCS &nbsp;&nbsp;
				<input type="text" name="gcs" id="gcs" value="<?=$cthistoryInfo['gcs']?>">
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<td>RLS &nbsp;&nbsp;
				<input type="text" name="rls" id="rls" value="<?=$cthistoryInfo['rls']?>">
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
				<td align="center" background="images/top_05.jpg" bgcolor="#EDF2FE">
<?php
						if (!$cthistoryInfo || empty($cthistoryInfo)){
								echo '            <input type="hidden" name="mode" id="mode" value="save">'."\n";
								echo '            <input type="submit" name="Submit" value="Save">'."\n";
						}else{
								echo '            <input type="hidden" name="mode" id="mode" value="update">'."\n";
								echo '            <input type="button" name="Print" value="Print" onClick="printCTHistory('.$encounter_nr.', '.$pid.')">'."\n &nbsp; &nbsp;";
								echo '            <input type="submit" name="Submit" value="Update">'."\n";
						}
						echo '            <input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">'."\n";
						echo '						<input type="hidden" name="refno" id="refno" value="'.$refno.'">'."\n";
?>
						&nbsp; &nbsp;
						<input type="button" name="Cancel" value="Cancel"  onclick="closeWindow();"
						<input type="hidden" name="pid" id="pid" value="<?=$encInfo['pid']?>">
						<!--<input type="text" name="refno" id="refno" value="<?=$refno?>"> -->
				</td>
		</tr>

		</form>
</table>

</body>
</html>
