<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

require($root_path.'modules/medocs/ajax/medocs_common.php'); //add by mark
require($root_path.'include/inc_environment_global.php');

#require_once($root_path.'include/inc_img_fx.php'); # image functions

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
*
* See the file "copy_notice.txt" for the licence notice
*/
$thisfile=basename(__FILE__);

if(!isset($type_nr)||!$type_nr) $type_nr=1; //* 1 = history physical notes

require_once($root_path.'include/care_api_classes/class_notes.php');
$obj=new Notes;
$types=$obj->getAllTypesSort('name');
$this_type=$obj->getType($type_nr);
require($root_path.'include/care_api_classes/class_medocs.php');
$objResDisp = new Medocs;

require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_department.php');

$dept_obj=new Department;
$pers_obj=new Personell;

#added by janjan for fis integration 08/04/2015
require_once($root_path.'include/care_api_classes/curl/class_curl.php');
$curl_obj = new Rest_Curl();

require_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;


if(!isset($mode)){
	$mode='show';

# If mode=='crete or update
# Save new diagnosis and procedure to care_notes and care_encounter_diagnosis
} elseif(($mode=='create'||$mode=='update'||$mode=='new')) {
	# Prepare the posted data for saving in databank
	include_once($root_path.'include/inc_date_format_functions.php');
	#add by Mark on Apr 20, 2007
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	#edited by VAN 02-29-08
	#$enc_obj=new Encounter($encounter_nr);
	$enc_obj=new Encounter();

	if(empty($_POST['date_text_d'])){
		$_POST['date_text_d'] = date('Y-m-d');
		if(empty($_POST['time_text_d'])){
			$_POST['time_text_d'] = date('H:i:s');
		}
	}else{
		$_POST['date_text_d'] = @formatDate2STD($_POST['date_text_d'],$date_format);
	}

	#12:00
	if($_POST['selAMPM'] == 'P.M.'){
		$hr = substr($_POST['time_text_d'],0,2);
		$min = substr($_POST['time_text_d'],-3);
		//if($hr>=00){
		//edited by VAN 04-17-2011
		if(($hr>=00)&&($hr!=12)){
			$hr1 = $hr+12;
		}else
			$hr1 = $hr;

		$_POST['time_text_d'] = $hr1.$min;
	}else{
		$hr = substr($_POST['time_text_d'],0,2);
		$min = substr($_POST['time_text_d'],-3);

		if($hr==12){
			$hr1 = "00";
		}else
			$hr1 = $hr;

		$_POST['time_text_d'] = $hr1.$min;
	}

	#echo "van = ".$_POST['time_text_d'];
	#echo "wait = ".$HTTP_POST_VARS['isdischarge'];
	$HTTP_POST_VARS['aux_notes']=substr($HTTP_POST_VARS['aux_notes'],0,255);
	$HTTP_POST_VARS['history']='Entry: '.date('Y-m-d H:i:s').' '.$HTTP_SESSION_VARS['sess_user_name'];
	$HTTP_POST_VARS['date'] = $_POST['date_text_d'];
	$HTTP_POST_VARS['time']= $_POST['time_text_d'].":00";
	$HTTP_POST_VARS['type_nr']=12; // 12 = text_diagnosis

	$patient_enc_cond = $enc_obj->getPatientEncounterCond($encounter_nr);
	$patient_enc_disp = $enc_obj->getPatientEncounterDispRefer($encounter_nr);
	$patient_enc_res = $enc_obj->getPatientEncounterRes($encounter_nr);

	//Save date for ER Patient
	if($encounter_type==1){
		#added by VAN 02-27-08
		if (empty($HTTP_POST_VARS['current_doc_nr_c']))
			$dr_nr = $HTTP_POST_VARS['current_doc_nr_f'];
		elseif (empty($HTTP_POST_VARS['current_doc_nr_f']))
			$dr_nr = $HTTP_POST_VARS['current_doc_nr_c'];
		elseif (empty($consulting_dr_nr))
			$dr_nr = $current_att_dr_nr;
		elseif (empty($current_att_dr_nr))
			$dr_nr = $consulting_dr_nr;

		#commented by VAN 06-12-08
		if ((isset($HTTP_POST_VARS['isdischarge']))&&($HTTP_POST_VARS['isdischarge'])){
			$enc_obj->updateAdmittingDiagnosis($encounter_nr,$_POST['aux_notes']);
			if($enc_obj->setIsDischarged_d($encounter_nr,$HTTP_POST_VARS['date'],$HTTP_POST_VARS['time'],$dr_nr,$encounter_type)){  # burn added : June 6, 2007
				$setHidden = FALSE;
				$trans_date = date('Y-m-d H:i:s', strtotime(($_POST['txtAdmissionDate'] . ' ' . $_POST['txtAdmissionTime'])));
				$curl_obj->customerUpdateDischarge($encounter_nr,$trans_date,$HTTP_POST_VARS['date'],$HTTP_POST_VARS['time'], $pid);
			}
		}
        

		//Condition
		if(isset($_POST['cond_code'])){
			$cond_prev = $enc_obj->getEncounterConditionInfo($patient_enc_cond['cond_code']);
			$cond_current = $enc_obj->getEncounterConditionInfo($cond_code);
			#if($patient_enc_disp['encounter_nr']==$encounter_nr&&($cond_current['area_used']==$cond_prev['area_used'])){
			if($patient_enc_disp['encounter_nr']==$encounter_nr){
				$condition['cond_code']= $_POST['cond_code'];
				$condition['modify_id']= $_SESSION['sess_user_name'];
				$condition['modify_time'] = date('Y-m-d H:i:s');
				$condition['create_id']= $_SESSION['sess_user_name'];
				$condition['create_time']=date('Ymd His');
				$enc_obj->setDataArray($condition);
				if(!@$enc_obj->updateEncounterCondition($encounter_nr,$patient_enc_cond['cond_code'])) echo "<br>$LDDbNoSave";
			}else{
				$condition['encounter_nr'] = $encounter_nr;
				$condition['cond_code']= $_POST['cond_code'];
				$condition['modify_id'] = $_SESSION['sess_user_name'];
				$condition['modify_time'] = date('Y-m-d H:i:s');
				$condition['create_id'] =$_SESSION['sess_user_name'];
				$condition['create_time'] = date('Y-m-d H:i:s');
				$enc_obj->setDataArray($condition);

				if(!@$enc_obj->saveEncounterCondition($condition)) echo "<br>$LDDbNoSave";
			}
		}// end if statement for conditon

		//disposition
		if(isset($_POST['disp_code'])){
			//change this disp_code later
			$disp_prev = $enc_obj->getEncounterDispositionInfo($patient_enc_disp['disp_code']);
			$disp_current = $enc_obj->getEncounterDispositionInfo($disp_code);

			#if(($patient_enc_disp['encounter_nr']==$encounter_nr)&&($disp_current['area_used']==$disp_prev['area_used'])){
			if($patient_enc_disp['encounter_nr']==$encounter_nr){
				$disposition['disp_code']=$_POST['disp_code'];
				$disposition['modify_id'] = $_SESSION['sess_user_name'];
				$disposition['modify_time']=date('Y-m-d H:i:s');
				$disposition['create_id']= $_SESSION['sess_user_name'];
				$disposition['create_time']=date('Y-m-d H:i:s');
				$disposition['hosp_name']= $HTTP_POST_VARS['hosp_name']; //added by genz
				$disposition['hosp_add']= $HTTP_POST_VARS['hosp_add']; //added by genz
				$disposition['referral_reason_id'] = $HTTP_POST_VARS['referral_reason_id'];
				$enc_obj->setDataArray($disposition);
				if(!@$enc_obj->updateEncounterDispositionRefer($encounter_nr,$patient_enc_disp['disp_code']))
					 echo "<br>$LDDbNoSave";
			}else{
				$disposition['encounter_nr'] = $encounter_nr;
				$disposition['disp_code']=$_POST['disp_code'];
				$disposition['modify_id'] = $_SESSION['sess_user_name'];
				$disposition['modify_time']=date('Y-m-d H:i:s');
				$disposition['create_id']= $_SESSION['sess_user_name'];
				$disposition['create_time']=date('Y-m-d H:i:s');
				$disposition['hosp_name']= $HTTP_POST_VARS['hosp_name']; //added by genz
				$disposition['hosp_add']= $HTTP_POST_VARS['hosp_add']; //added by genz
				$disposition['referral_reason_id'] = $HTTP_POST_VARS['referral_reason_id'];
				$enc_obj->setDataArray($disposition);

				if(!@$enc_obj->saveEncounterDispositionRefer($disposition))
					 echo "<br>$LDDbNoSave";
			}
		}

		//Result
		if(isset($_POST['result_code'])){
			$res_prev = $enc_obj->getEncounterResultInfo($patient_enc_res['result_code']);
			$res_current = $enc_obj->getEncounterResultInfo($result_code);
			#if (($patient_enc_res['encounter_nr']==$encounter_nr)&&($res_current['area_used']==$res_prev['area_used'])){
			if ($patient_enc_res['encounter_nr']==$encounter_nr){
				$result['result_code']=$_POST['result_code'];
				$result['modify_id']=$_SESSION['sess_user_name'];
				$result['modify_time']=date('Y-m-d H:i:s');
				$enc_obj->setDataArray($result);

				if(!@$enc_obj->updateEncounterResults($encounter_nr, $patient_enc_res['result_code']))
				 echo "<br>$LDDbNoSave";
				else{
					if (($_POST['result_code']==4)||($_POST['result_code']==8)){
						$data['death_date'] = $HTTP_POST_VARS['date'];
						$data['modify_id'] = $_SESSION['sess_user_name'];
						$data['modify_time'] = date('Y-m-d H:i:s');
						$data['history'] = "Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
						$person_obj->updatePersonInfo($pid,$data);
					}
				}

			}else{
				$result['encounter_nr'] = $encounter_nr;
				$result['result_code']=$_POST['result_code'];

				$result['modify_id']=$_SESSION['sess_user_name'];
				$result['modify_time']=date('Y-m-d H:i:s');
				$result['create_id']=$_SESSION['sess_user_name'];
				$result['create_time']=date('Y-m-d H:i:s');
				$enc_obj->setDataArray($result);

				if(!@$enc_obj->saveEncounterResults($result))
					echo "<br>$LDDbNoSave";
				else{
					if (($_POST['result_code']==4)||($_POST['result_code']==8)){
						$data['death_date'] = $HTTP_POST_VARS['date'];
						$data['modify_id'] = $_SESSION['sess_user_name'];
						$data['modify_time'] = date('Y-m-d H:i:s');
						$data['history'] = "Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
						$person_obj->updatePersonInfo($pid,$data);
					}
				}
			}
			#added by VAN 06-28-08

			if (empty($_POST['death_date'])){
				$death_date = "0000-00-00";
				$death_time = "00:00";
				$DOA = 0;
				$DOA_reason = "";
			}else{
				$death_date = date("Y-m-d",strtotime($_POST['death_date']));
				$death_hr = trim($_POST['death_time']);
				$death_mer = trim($_POST['selAMPM_dt']);
				$death_mer = str_replace('.','',$death_mer);
				$death_time =  $death_hr.":00 ".$death_mer;
				$death_time = date("H:i:s",strtotime($death_time));
			}

			$person_obj->updateDeathDate($pid, $death_date, $encounter_nr, $death_time);
			$enc_obj->updateDOA($encounter_nr, $DOA, $DOA_reason);
			#--------------------------
		}

		$HTTP_POST_VARS['type_nr']=12;
		$start=FALSE;
		$g=0;
		$k=count($_POST['icdCodeID'])+count($_POST['icpCodeID']);
		$redirect=false;

		foreach ($_POST['icdCodeID'] as $i=>$v) {
			$HTTP_POST_VARS['code']=$v;
			$HTTP_POST_VARS['notes']=$_POST['icdCodeDesc'][$i];

			if ($k==$g+1) $redirect=TRUE;

			include('./include/save_admission_data.inc.php');
			$g++;

			if (!$start){
				$insid=$db->Insert_ID();
				$HTTP_POST_VARS['ref_notes_nr']=$obj->LastInsertPK('nr',$insid);
				$start=TRUE;
			}
		}
		$HTTP_POST_VARS['type_nr']=13; // 12 = text_diagnosis
		foreach ($_POST[icpCodeID] as $i=>$v) {
			$HTTP_POST_VARS['code']=$v;
			$HTTP_POST_VARS['notes']=$_POST['icpCodeDesc'][$i];

			if ($k==$g+1) $redirect=TRUE;

			include('./include/save_admission_data.inc.php');
			$g++;
		}

	//Save data for IPD Patient
	}elseif($encounter_type==4 || $encounter_type==3 ){

		//Disposition
		//set patient status is_discharged =1
		#added by VAN 02-27-08
		if (empty($HTTP_POST_VARS['current_doc_nr_f']))
			$dr_nr = $HTTP_POST_VARS['current_doc_nr_c'];
		elseif (empty($HTTP_POST_VARS['current_doc_nr_c']))
			$dr_nr = $HTTP_POST_VARS['current_doc_nr_f'];
		elseif (empty($consulting_dr_nr))
			$dr_nr = $current_att_dr_nr;
		elseif (empty($current_att_dr_nr))
			$dr_nr = $consulting_dr_nr;

		#commented by VAN 06-12-08
		if ((isset($HTTP_POST_VARS['isdischarge']))&&($HTTP_POST_VARS['isdischarge'])){
			if($enc_obj->setIsDischarged_d($encounter_nr,$HTTP_POST_VARS['date'],$HTTP_POST_VARS['time'],$dr_nr, $encounter_type)){
				$setHidden = FALSE;
				$trans_date = date('Y-m-d H:i:s', strtotime(($_POST['txtAdmissionDate'] . ' ' . $_POST['txtAdmissionTime'])));
				$curl_obj->customerUpdateDischarge($encounter_nr,$trans_date,$HTTP_POST_VARS['date'],$HTTP_POST_VARS['time'], $pid);
			}
		}

		if(isset($_POST['disp_code'])){
			//change this disp_code later
			$disp_prev = $enc_obj->getEncounterDispositionInfo($patient_enc_disp['disp_code']);
			$disp_current = $enc_obj->getEncounterDispositionInfo($disp_code);


			#if(($patient_enc_disp['encounter_nr']==$encounter_nr)&&($disp_current['area_used']==$disp_prev['area_used'])){
			if($patient_enc_disp['encounter_nr']==$encounter_nr){
				$disposition['disp_code']=$_POST['disp_code'];
				$disposition['modify_id'] = $_SESSION['sess_user_name'];
				$disposition['modify_time']=date('Y-m-d H:i:s');
				$disposition['create_id']= $_SESSION['sess_user_name'];
				$disposition['create_time']=date('Y-m-d H:i:s');
				$disposition['hosp_name']= $HTTP_POST_VARS['hosp_name']; //added by genz
				$disposition['hosp_add']= $HTTP_POST_VARS['hosp_add']; //added by genz
				$disposition['referral_reason_id'] = $HTTP_POST_VARS['referral_reason_id'];
				$enc_obj->setDataArray($disposition);

				if(!@$enc_obj->updateEncounterDispositionRefer($encounter_nr,$patient_enc_disp['disp_code'])) echo "<br>$LDDbNoSave";
			}else{
				$disposition['encounter_nr'] = $encounter_nr;
				$disposition['disp_code']=$_POST['disp_code'];
				$disposition['modify_id'] = $_SESSION['sess_user_name'];
				$disposition['modify_time']=date('Y-m-d H:i:s');
				$disposition['create_id']= $_SESSION['sess_user_name'];
				$disposition['create_time']=date('Y-m-d H:i:s');
				$disposition['hosp_name']= $HTTP_POST_VARS['hosp_name']; //added by genz
				$disposition['hosp_add']= $HTTP_POST_VARS['hosp_add']; //added by genz
				$disposition['referral_reason_id'] = $HTTP_POST_VARS['referral_reason_id'];
				$enc_obj->setDataArray($disposition);

				if(!@$enc_obj->saveEncounterDispositionRefer($disposition)) echo "<br>$LDDbNoSave";
			}

		}

		//Result
		if(isset($_POST['result_code'])){

			$res_prev = $enc_obj->getEncounterResultInfo($patient_enc_res['result_code']);
			$res_current = $enc_obj->getEncounterResultInfo($result_code);
			#if (($patient_enc_res['encounter_nr']==$encounter_nr)&&($res_current['area_used']==$res_prev['area_used'])){

			if ($patient_enc_res['encounter_nr']==$encounter_nr){
				$result['result_code']=$_POST['result_code'];
				$result['modify_id']=$_SESSION['sess_user_name'];
				$result['modify_time']=date('Y-m-d H:i:s');
				$enc_obj->setDataArray($result);

				if(!@$enc_obj->updateEncounterResults($encounter_nr, $patient_enc_res['result_code'])) echo "<br>$LDDbNoSave";

			}else{
				$result['encounter_nr'] = $encounter_nr;
				$result['result_code']=$_POST['result_code'];

				$result['modify_id']=$_SESSION['sess_user_name'];
				$result['modify_time']=date('Y-m-d H:i:s');
				$result['create_id']=$_SESSION['sess_user_name'];
				$result['create_time']=date('Y-m-d H:i:s');
				$enc_obj->setDataArray($result);

				if(!@$enc_obj->saveEncounterResults($result)) echo "<br>$LDDbNoSave";

			}
			if (empty($_POST['death_date'])){
				$death_date = "0000-00-00";
				$death_time = "00:00";
				$DOA = 0;
				$DOA_reason = "";
			}else{
				$death_date = date("Y-m-d",strtotime($_POST['death_date']));
				$death_hr = trim($_POST['death_time']);
				$death_mer = trim($_POST['selAMPM_dt']);
				$death_mer = str_replace('.','',$death_mer);
				$death_time =  $death_hr.":00 ".$death_mer;
				$death_time = date("H:i:s",strtotime($death_time));
			}

			$person_obj->updateDeathDate($pid, $death_date, $encounter_nr, $death_time);
			$enc_obj->updateDOA($encounter_nr, $DOA, $DOA_reason);
			#added by genz
			$enc_obj->updateAdmittingDiagnosis($encounter_nr,$_POST['aux_notes']);

			#--------------------------
		}

		$HTTP_POST_VARS['type_nr']=12;
		$start=FALSE;
		$g=0;
		$k=count($_POST['icdCodeID'])+count($_POST['icpCodeID']);
		$redirect=false;

		foreach ($_POST['icdCodeID'] as $i=>$v) {
			$HTTP_POST_VARS['code']=$v;
			$HTTP_POST_VARS['notes']=$_POST['icdCodeDesc'][$i];

			if ($k==$g+1) $redirect=TRUE;

			include('./include/save_admission_data.inc.php');
			$g++;

			if (!$start){
				$insid=$db->Insert_ID();
				$HTTP_POST_VARS['ref_notes_nr']=$obj->LastInsertPK('nr',$insid);
				$start=TRUE;
			}
		}
		$HTTP_POST_VARS['type_nr']=13; // 12 = text_diagnosis
		foreach ($_POST[icpCodeID] as $i=>$v) {
			$HTTP_POST_VARS['code']=$v;
			$HTTP_POST_VARS['notes']=$_POST['icpCodeDesc'][$i];

			if ($k==$g+1) $redirect=TRUE;

			include('./include/save_admission_data.inc.php');
			$g++;
		}

	// Save data for OPD patient
	}else{
		//set patient status is_discharged =1   current_doc_nr_f

			#update care_encounter table discharged time
		if (empty($HTTP_POST_VARS['current_doc_nr_c']))
			$dr_nr = $HTTP_POST_VARS['current_doc_nr_f'];
		elseif (empty($HTTP_POST_VARS['current_doc_nr_f']))
			$dr_nr = $HTTP_POST_VARS['current_doc_nr_c'];
		elseif (empty($consulting_dr_nr))
			$dr_nr = $current_att_dr_nr;
		elseif (empty($current_att_dr_nr))
			$dr_nr = $consulting_dr_nr;
		#commented by VAN 06-12-08
		if ((isset($HTTP_POST_VARS['isdischarge']))&&($HTTP_POST_VARS['isdischarge'])){
			if($enc_obj->setIsDischarged_d($encounter_nr,$HTTP_POST_VARS['date'],$HTTP_POST_VARS['time'],$dr_nr,$encounter_type)){   # burn added : June 6, 2007
			//echo "patient is discharged";
				$setHidden = FALSE;
				$enc_date = date('Y-m-d H:i:s', strtotime(($_POST['txtAdmissionDate'] . ' ' . $_POST['txtAdmissionTime'])));
				$curl_obj->customerUpdateDischarge($encounter_nr,$trans_date,$HTTP_POST_VARS['date'],$HTTP_POST_VARS['time'], $pid);
			}
		}

		$HTTP_POST_VARS['type_nr']=12;
		$start=FALSE;
		$g=0;
		$k=count($_POST['icdCodeID'])+count($_POST['icpCodeID']);
		$redirect=false;

		foreach ($_POST['icdCodeID'] as $i=>$v) {
			$HTTP_POST_VARS['code']=$v;
			$HTTP_POST_VARS['notes']=$_POST['icdCodeDesc'][$i];

			if ($k==$g+1) $redirect=TRUE;

			include('./include/save_admission_data.inc.php');
			$g++;

			if (!$start){
				$insid=$db->Insert_ID();
				$HTTP_POST_VARS['ref_notes_nr']=$obj->LastInsertPK('nr',$insid);
				$start=TRUE;
			}
		}
		$HTTP_POST_VARS['type_nr']=13; // 12 = text_diagnosis
#		$g=0;
#		$k=count($_POST[icpCodeID]);
		foreach ($_POST[icpCodeID] as $i=>$v) {
			$HTTP_POST_VARS['code']=$v;
			$HTTP_POST_VARS['notes']=$_POST['icpCodeDesc'][$i];

			if ($k==$g+1) $redirect=TRUE;

			include('./include/save_admission_data.inc.php');
			$g++;
		}
	}
}// End of (if mode='create' || mode='update') mode = create new record

require('./include/init_show.php');

$page_title=$LDMedocs;

//include_once($root_path.'include/inc_date_format_functions.php');

//comment by mark on Apr 19, 2007
# Load the entire encounter data
require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter($encounter_nr);
$enc_obj->loadEncounterData();

# Get encounter class
$enc_class=$enc_obj->EncounterClass();

$HTTP_SESSION_VARS['sess_full_en']=$encounter_nr;

if(empty($encounter_nr)&&!empty($HTTP_SESSION_VARS['sess_en'])){
	$encounter_nr=$HTTP_SESSION_VARS['sess_en'];
}elseif($encounter_nr) {
	$HTTP_SESSION_VARS['sess_en']=$encounter_nr;
}

$enc_Info = $enc_obj->getEncounterInfo($encounter_nr);

if(!empty($HTTP_SESSION_VARS['sess_login_userid']))
	$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
else
	$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
$userDeptInfo = $dept_obj->getUserDeptInfo($seg_user_name);


if (($encounter_class_nr==2)&&($encounter_type==2)){
	# Load all  doctors in OPD
	$doctor_dept=$pers_obj->getDoctors(0);
	$all_meds=&$dept_obj->getAllOPDMedicalObject(0);
}else{
	# Load all  doctors in IPD
	$doctor_dept=$pers_obj->getDoctors(1);
	$all_meds=&$dept_obj->getAllOPDMedicalObject(1);
}

require_once($root_path.'/include/care_api_classes/class_drg.php');
$objDRG= new DRG;

//It show list of diagnosis and procedures
if($mode=='show'){

	$result = array();
	$rowsDiagnosis=0;
	$rowsTherapy=0;

	$encounter_type=$enc_Info['encounter_type'];
	$encounter_class_nr = $enc_Info['encounter_class_nr'];
	$encounter_type_a = $enc_Info['encounter_type'];
    $is_maygohome = $enc_Info['is_maygohome'];
   // $bill_nr =$enc_Info['bill_nr'];

	$encounter_type = $enc_Info['encounter_type'];

	if ($result_diagnosis = $objDRG->getDiagnosisCodes($encounter_nr,$encounter_type)){
		$result['diagnosis_principal']='';
		$result['diagnosis_others']='';
		$rowsDiagnosis = $result_diagnosis->RecordCount();
		while($temp=$result_diagnosis->FetchRow()){
			if ($temp['type'])
				$result['diagnosis_principal'].= $temp['code']." : ".$temp['diagnosis']." <br> \n";
			else
				$result['diagnosis_others'].= $temp['code']." : ".$temp['diagnosis']." <br> \n";
		}
	}

	if ($result_therapy = $objDRG->getProcedureCodes($encounter_nr,$encounter_type)){
		$result['therapy_principal']='';
		$result['therapy_others']='';
		$rowsTherapy = $result_therapy->RecordCount();
		while($temp=$result_therapy->FetchRow()){
			if ($temp['type'])
				$result['therapy_principal'].= $temp['code']." : ".$temp['therapy']." <br> \n";
			else
				$result['therapy_others'].= $temp['code']." : ".$temp['therapy']." <br> \n";
		}
	}

	$rows = $rowsDiagnosis + $rowsTherapy;


//Show the detailed description of diagnosis and procedures
}elseif(($mode=='details')&&!empty($encounter_nr)){
	$sql ="SELECT c.diagnosis_code AS code, d.description as diagnosis, c.create_id,date(c.create_time) as date".
		 "\n FROM seg_encounter_icd c ".
		 "\n LEFT JOIN care_icd10_en d on c.diagnosis_code = d.diagnosis_code".
		 "\n  WHERE c.encounter_nr ='$encounter_nr'";

	#echo "sql= ".$sql;
	$result=NULL;
	if($t1=$db->Execute($sql)){
		if($rows=$t1->RecordCount()){
			$temp = $t1;
			$result=$temp->FetchRow();
			$result['code']='';
			$result['diagnosis']='';
			while($t2=$t1->FetchRow()){
				$result['diagnosis'].= $t2['code']." : ".$t2['diagnosis']."\n";
			}
		}else{
			//echo "$LDDbNoRead<p>$sql";
		}
	}else{
		//echo $sql;
	}


	$sql= "SELECT c.procedure_code AS code, d.description as therapy, c.create_id,date(c.create_time) as date".
	"\n FROM seg_encounter_icp c".
	"\n LEFT JOIN seg_icpm d on c.procedure_code = d.procedure_code".
	"\n WHERE c.encounter_nr='$encounter_nr'";

	#echo "sql= ".$sql;
	$result_icp=NULL;
	if($t1=$db->Execute($sql)){
		if($rows=$t1->RecordCount()){
			$result_icp['therapy']='';
			while($t2=$t1->FetchRow()){
				$result_icp['therapy'].= $t2['code']." : ".$t2['therapy']."\n";
			}
		}else{
			//echo "$LDDbNoRead<p>$sql";
		}
	}else{
		//echo $sql;
	}


	if ($encounter_type==1)
		$area_used = "E";
	elseif(($encounter_type==3) || ($encounter_type==4))
		$area_used = "A";

	//for result
	$sql ="SELECT r.result_desc as description FROM seg_encounter_result e LEFT JOIN seg_results r ON ".
			"\n e.result_code = r.result_code WHERE e.encounter_nr='$encounter_nr' ".
			"\n AND r.area_used='A'";

	$rResult=NULL;
	if($r=$db->Execute($sql)){
		if($rows=$r->RecordCount()){
			$rResult['description']='';
			while($h=$r->FetchRow()){
				$rResult['description'].="- ". $h['description']."<br> \n";
			}
		}else{
			//echo "$LDDbNoRead<p>$sql<br>";
		}
	}else{
		//echo "<br>".$sql;
	}


	$sql = "SELECT d.disp_desc as descrip FROM seg_encounter_disposition_refer e LEFT JOIN seg_dispositions d ON ".
			"\n e.disp_code = d.disp_code WHERE e.encounter_nr='$encounter_nr' ".
			"\n AND d.area_used='A'";

	$rDisp=NULL;
	if($d=$db->Execute($sql)){
		if($rows=$d->RecordCount()){
			$rDisp['descrip']='';
			while($s=$d->FetchRow()){
				$rDisp['descrip'].="- ". $s['descrip']."<br> \n";
			}
		}else{
			//echo "$LDDbNoRead<p>$sql<br>";
		}
	}else{
	//	echo "<br>".$sql;
	}
}


$subtitle=$LDMedocs;

$buffer=str_replace('~tag~',$title.' '.$name_last,$LDNoRecordFor);
$norecordyet=str_replace('~obj~',strtolower($subtitle),$buffer);
$HTTP_SESSION_VARS['sess_file_return']=$thisfile;

# Set break file
require('include/inc_breakfile.php');
if($mode=='show') $glob_obj->getConfig('medocs_%');

/* Load GUI page */
require('./gui_bridge/default/gui_show_medocs.php');

?>
