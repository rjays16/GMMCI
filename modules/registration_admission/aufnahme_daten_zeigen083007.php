<?php
//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/*
CARE2X Integrated Information System beta 2.0.1 - 2004-07-04 for Hospitals and Health Care Organizations and Services
Copyright (C) 2002,2003,2004,2005  Elpidio Latorilla & Intellin.org	
GNU GPL. 
For details read file "copy_notice.txt".
*/

#--------------- EDITED BY VANESSA -----------------------

$lang_tables[]='prompt.php';
$lang_tables[]='person.php';
$lang_tables[]='departments.php';
define('LANG_FILE','aufnahme.php');
$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_insurance.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');

#---------added 03-05-07-----------
include_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

$dept_obj=new Department;
$pers_obj=new Personell;

#$dept_belong = $dept_obj->getUserDeptInfo($HTTP_SESSION_VARS['sess_user_name']);
	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);

#---------added 03-05-07-----------

if(!session_is_registered('sess_parent_mod')) session_register('sess_parent_mod');
# Create objects

$encounter_obj=new Encounter($encounter_nr);
$person_obj=new Person();
$insurance_obj=new Insurance;

#----------------added 03-09-07----------------
if ($encounter_nr!=NULL){
	$patient_enc = $encounter_obj->getPatientEncounter($encounter_nr);
}
#-------------------------------------------------

$thisfile=basename(__FILE__);

if($HTTP_COOKIE_VARS['ck_login_logged'.$sid]) $breakfile=$root_path.'main/startframe.php'.URL_APPEND;
	else $breakfile='aufnahme_pass.php'.URL_APPEND.'&target=entry';

//$breakfile='aufnahme_pass.php'.URL_APPEND;

$GLOBAL_CONFIG=array();
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);

/* Get the patient global configs */	
$glob_obj->getConfig('patient_%');
$glob_obj->getConfig('person_foto_path');

$updatefile='aufnahme_start.php';

/* Default path for fotos. Make sure that this directory exists! */
$default_photo_path=$root_path.'fotos/registration';
$photo_filename='nopic';

$dbtable='care_encounter';

//$db->debug=1;

/*		$sql='SELECT * FROM '.$dbtable.' AS enc LEFT JOIN care_person AS reg ON reg.pid = enc.pid
		         WHERE enc.encounter_nr="'.$encounter_nr.'"';
				 
       	if($ergebnis=$db->Execute($sql)) {
		    if($ergebnis->RecordCount()) {
                 $encounter=$ergebnis->FetchRow();
		 	     while(list($x,$v)=each($encounter)) $$x=$v;
		    }
		}*/
	if(!empty($GLOBAL_CONFIG['patient_financial_class_single_result'])) $encounter_obj->setSingleResult(true);
	
	if(!$GLOBAL_CONFIG['patient_service_care_hide']){
	/* Get the care service classes*/
		$care_service=$encounter_obj->AllCareServiceClassesObject();
		
		if($buff=&$encounter_obj->CareServiceClass()){
		    $care_class=$buff->FetchRow();
			//while(list($x,$v)=each($care_class))	$$x=$v;
			extract($care_class);      
			reset($care_class);
		}    			  
	}
	if(!$GLOBAL_CONFIG['patient_service_room_hide']){
	/* Get the room service classes */
		$room_service=$encounter_obj->AllRoomServiceClassesObject();
		
		if($buff=&$encounter_obj->RoomServiceClass()){
			$room_class=$buff->FetchRow();
			//while(list($x,$v)=each($room_class))	$$x=$v;
			extract($room_class);      
			reset($room_class);
		}    			  
	}
	if(!$GLOBAL_CONFIG['patient_service_att_dr_hide']){
		/* Get the attending doctor service classes */
		$att_dr_service=$encounter_obj->AllAttDrServiceClassesObject();
		
		if($buff=&$encounter_obj->AttDrServiceClass()){
			$att_dr_class=$buff->FetchRow();
			//while(list($x,$v)=each($att_dr_class))	$$x=$v;
			extract($att_dr_class);      
			reset($att_dr_class);
		}    			  
	}		
		
	$encounter_obj->loadEncounterData();
	if($encounter_obj->is_loaded) {
		$row=&$encounter_obj->encounter;
		//load data
		//while(list($x,$v)=each($row)) $$x=$v;
		extract($row);
		# Set edit mode
		if(!$is_discharged) $edit=true;
			else $edit=false;
		# Fetch insurance and encounter classes
		$insurance_class=&$encounter_obj->getInsuranceClassInfo($insurance_class_nr);
		$encounter_class=&$encounter_obj->getEncounterClassInfo($encounter_class_nr);
				
		#------------added 03-08-07---------------------
		$patient_enc_cond = $encounter_obj->getPatientEncounterCond($encounter_nr);
		$patient_enc_disp = $encounter_obj->getPatientEncounterDisp($encounter_nr);
		$patient_enc_res = $encounter_obj->getPatientEncounterRes($encounter_nr);
			
		if ($update==1){
			if($cond_code!=NULL){
				$encounter_condition=&$encounter_obj->getEncounterConditionInfo($cond_code);
			}else{
				$encounter_condition=&$encounter_obj->getEncounterConditionInfo($patient_enc_cond['cond_code']);
			}
			$encounter_disposition=&$encounter_obj->getEncounterDispositionInfo($disp_code);
			$encounter_result=&$encounter_obj->getEncounterResultInfo($result_code);
		}else{
			$encounter_condition=&$encounter_obj->getEncounterConditionInfo($patient_enc_cond['cond_code']);
			$encounter_disposition=&$encounter_obj->getEncounterDispositionInfo($patient_enc_disp['disp_code']);
			$encounter_result=&$encounter_obj->getEncounterResultInfo($patient_enc_res['result_code']);
		}	
		
		#-----------------------------------------------

		//if($data_obj=&$person_obj->getAllInfoObject($pid))
		$list='title,name_first,name_last,name_2,name_3,name_middle,name_maiden,name_others,date_birth,
		         sex,addr_str,addr_str_nr,addr_zip,addr_citytown_nr,photo_filename';
			
		$person_obj->setPID($pid);
		if($row=&$person_obj->getValueByList($list))
		{
			//while(list($x,$v)=each($row))	$$x=$v;
			extract($row);      
		}      

		$addr_citytown_name=$person_obj->CityTownName($addr_citytown_nr);
		$encoder=$encounter_obj->RecordModifierID();
		# Get current encounter to check if current encounter is this encounter nr
		$current_encounter=$person_obj->CurrentEncounter($pid);
		
		# Get the overall status
		if($stat=&$encounter_obj->AllStatus($encounter_nr)){
			$enc_status=$stat->FetchRow();
		}

		# Get ward or department infos
		#if($encounter_class_nr==1){
		if (($dept_belong['id']=="Admission")&&(($encounter_type==3)||($encounter_type==4))){
			# Get ward name
			include_once($root_path.'include/care_api_classes/class_ward.php');
			$ward_obj=new Ward;
			$current_ward_name=$ward_obj->WardName($current_ward_nr);
		}#elseif($encounter_class_nr==2){  #----------03-05-07---------------
			# Get ward name
			#include_once($root_path.'include/care_api_classes/class_department.php');
			#$dept_obj=new Department;
			//$current_dept_name=$dept_obj->FormalName($current_dept_nr);
			$current_dept_LDvar=$dept_obj->LDvar($current_dept_nr);

			if(isset($$current_dept_LDvar)&&!empty($$current_dept_LDvar)) $current_dept_name=$$current_dept_LDvar;
				else $current_dept_name=$dept_obj->FormalName($current_dept_nr);
		#}#----------03-05-07---------------

	}

	include_once($root_path.'include/inc_date_format_functions.php');
        
	/* Update History */
	if(!$newdata) $encounter_obj->setHistorySeen($HTTP_SESSION_VARS['sess_user_name'],$encounter_nr);
	/* Get insurance firm name*/
	$insurance_firm_name=$insurance_obj->getFirmName($insurance_firm_id);
	/* Check whether config path exists, else use default path */			
	$photo_path = (is_dir($root_path.$GLOBAL_CONFIG['person_foto_path'])) ? $GLOBAL_CONFIG['person_foto_path'] : $default_photo_path;


/* Prepare text and resolve the numbers */
require_once($root_path.'include/inc_patient_encounter_type.php');		 

/* Save encounter nrs to session */
$HTTP_SESSION_VARS['sess_pid']=$pid;
$HTTP_SESSION_VARS['sess_en']=$encounter_nr;
$HTTP_SESSION_VARS['sess_full_en']=$full_en;
$HTTP_SESSION_VARS['sess_parent_mod']='admission';
$HTTP_SESSION_VARS['sess_user_origin']='admission';
$HTTP_SESSION_VARS['sess_file_return']=$thisfile;
#$HTTP_SESSION_VARS['sess_dr'] = $consulting_dr;
#$HTTP_SESSION_VARS['sess_dr_nr'] = $current_att_dr_nr;
#$HTTP_SESSION_VARS['sess_dept'] = $current_dept_nr;

/* Prepare the photo filename */
require_once($root_path.'include/inc_photo_filename_resolve.php');

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in the toolbar
# $smarty->assign('sToolbarTitle',$LDPatientData.' ('.$encounter_nr.')');   # burn commented : May 15, 2007
	if (($encounter_type=='1')||($encounter_type=='2')){
		$smarty->assign('sToolbarTitle',$LDConsultationData.' ('.$encounter_nr.')');   # burn added : May 15, 2007
	}else{
		$smarty->assign('sToolbarTitle',$LDPatientData.' ('.$encounter_nr.')');   # burn added : May 15, 2007
	}

	if ($ERSave){
		$openWindow = "
				if (window.showModalDialog){  
					window.showModalDialog('".$root_path."modules/registration_admission/show_er_clinical_form.php?encounter_nr=".$encounter_nr."','width=500,height=500,menubar=no,resizable=yes,scrollbars=no');
				}else{
					window.open('".$root_path."modules/registration_admission/show_er_clinical_form.php?encounter_nr=".$encounter_nr."','ERClinicalForm','modal,width=500,height=500,menubar=no,resizable=yes,scrollbars=no');
				}";
		$smarty->assign('sOnLoadJs',"onLoad=\"javascript: $openWindow \"");
	}

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('admission_how2new.php')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
# $smarty->assign('title',$LDPatientData.' ('.$encounter_nr.')');   # burn commented : May 15, 2007
	if (($encounter_type=='1')||($encounter_type=='2')){
		$smarty->assign('title',$LDConsultationData.' ('.$encounter_nr.')');   # burn added : May 15, 2007
	}else{
		$smarty->assign('title',$LDPatientData.' ('.$encounter_nr.')');   # burn added : May 15, 2007
	}

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('admission_show.php','$from')");

 # Hide the return button
 $smarty->assign('pbBack',FALSE);

 # Collect extra javascript
 
 ob_start();

require($root_path.'include/inc_js_barcode_wristband_popwin.php');
require('./include/js_poprecordhistorywindow.inc.php');

$sTemp = ob_get_contents();

ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Load tabs
$parent_admit = TRUE;
//$target='entry';
include('./gui_bridge/default/gui_tabs_patadmit.php');

if($is_discharged){
	
	$smarty->assign('is_discharged',TRUE);
	$smarty->assign('sWarnIcon',"<img ".createComIcon($root_path,'warn.gif','0','absmiddle').">");
	if($current_encounter) $smarty->assign('sDischarged',$LDEncounterClosed);
		else $smarty->assign('sDischarged',$LDPatientIsDischarged);
}

$smarty->assign('LDCaseNr',$LDCaseNr);
$smarty->assign('encounter_nr',$encounter_nr);
	
# Create the encounter barcode image
	
if(file_exists($root_path.'cache/barcodes/en_'.$encounter_nr.'.png')) {
	$smarty->assign('sEncBarcode','<img src="'.$root_path.'cache/barcodes/en_'.$encounter_nr.'.png" border=0 width=180 height=35>');
}else{
	$smarty->assign('sHiddenBarcode',"<img src='".$root_path."classes/barcode/image.php?code=".$encounter_nr."&style=68&type=I25&width=180&height=50&xres=2&font=5&label=2&form_file=en' border=0 width=0 height=0>");
	$smarty->assign('sEncBarcode',"<img src='".$root_path."classes/barcode/image.php?code=".$encounter_nr."&style=68&type=I25&width=180&height=40&xres=2&font=5' border=0>");
}

$smarty->assign('img_source',"<img $img_source>");

	if (($encounter_type=='1')||($encounter_type=='2')){
		$smarty->assign('LDAdmitDate',$LDConsultDate);
		$smarty->assign('LDAdmitTime',$LDConsultTime);
	}else{
		$smarty->assign('LDAdmitDate',$LDAdmitDate);
		$smarty->assign('LDAdmitTime',$LDAdmitTime);
	}

#$smarty->assign('sAdmitDate', @formatDate2Local($encounter_date,$date_format));   # burn commented: March 29, 2007

#$smarty->assign('sAdmitTime',@formatDate2Local($encounter_date,$date_format,1,1));   # burn commented: March 29, 2007

   # burn added: March 29, 2007
	if (($encounter_type==1) || ($encounter_type==2)){
			# ER/OPD
		$segAdmitDateTime = $encounter_date;
	}else{
			# Inpatient
		$segAdmitDateTime = $admission_dt;
	}	
	$smarty->assign('sAdmitDate', @formatDate2Local($segAdmitDateTime,$date_format));   # burn added: March 29, 2007
	$smarty->assign('sAdmitTime',@formatDate2Local($segAdmitDateTime,$date_format,1,1));   # burn added: March 29, 2007

$smarty->assign('LDTitle',$LDTitle);
$smarty->assign('title',$title);
$smarty->assign('LDLastName',$LDLastName);
$smarty->assign('name_last',$name_last);
$smarty->assign('LDFirstName',$LDFirstName);
$smarty->assign('name_first',$name_first);

# If person is dead show a black cross and assign death date

if($death_date && $death_date != DBF_NODATE){
	$smarty->assign('sCrossImg','<img '.createComIcon($root_path,'blackcross_sm.gif','0').'>');
	$smarty->assign('sDeathDate',@formatDate2Local($death_date,$date_format));
}

	# Set a row span counter, initialize with 6
	$iRowSpan = 6;

	if($GLOBAL_CONFIG['patient_name_2_show']&&$name_2){
		$smarty->assign('LDName2',$LDName2);
		$smarty->assign('name_2',$name_2);
		$iRowSpan++;
	}

	if($GLOBAL_CONFIG['patient_name_3_show']&&$name_3){
		$smarty->assign('LDName3',$LDName3);
		$smarty->assign('name_3',$name_3);
		$iRowSpan++;
	}

	if($GLOBAL_CONFIG['patient_name_middle_show']&&$name_middle){
		$smarty->assign('LDNameMid',$LDNameMid);
		$smarty->assign('name_middle',$name_middle);
		$iRowSpan++;
	}
		$smarty->assign('sRowSpan',"rowspan=\"$iRowSpan\"");

$smarty->assign('LDBday',$LDBday);
$smarty->assign('sBdayDate',@formatDate2Local($date_birth,$date_format));

$smarty->assign('LDSex',$LDSex);
if($sex=='m') $smarty->assign('sSexType',$LDMale);
	elseif($sex=='f') $smarty->assign('sSexType',$LDFemale);
	
$smarty->assign('LDBloodGroup',$LDBloodGroup);
if($blood_group){
	$buf='LD'.$blood_group;
	$smarty->assign('blood_group',$$buf);
}

$smarty->assign('LDAddress',$LDAddress);
#echo "street_name = '".$street_name."' <br> \n";		
		$segAddress=$street_name.', '.$brgy_name.', '.$mun_name.' '.$zipcode.' '.$prov_name;   # burn added: March 12, 2007
#echo "segAddress = '".$segAddress."' <br> \n";		
$smarty->assign('segAddress',$segAddress);   # burn added: March 12, 2007
/*		   # burn commented: March 12, 2007
$smarty->assign('addr_str',$addr_str);
$smarty->assign('addr_str_nr',$addr_str_nr);
$smarty->assign('addr_zip',$addr_zip);
$smarty->assign('addr_citytown',$addr_citytown_name);
*/
$smarty->assign('LDAdmitClass',$LDAdmitClass);

# Suggested by Dr. Sarat Nayak to emphasize the OUTPATIENT encounter type

if (isset($$encounter_class['LD_var']) && !empty($$encounter_class['LD_var'])){
	$eclass=$$encounter_class['LD_var'];
	//$fcolor='red';
}else{
	$eclass= $encounter_class['name'];
} 


if (($encounter_type == 1)||($encounter_type == 3)||($encounter_type == 4)){
	
	if($encounter_type == 3){
		$eclass = $LDStationary2;
	}elseif($encounter_type == 4){
		$eclass = $LDAmbulant2;
	}
	
	if ($encounter_type == 1){
		$fcolor='red';
	}else{
		$fcolor='green';
	}
	
	$eclass='<b>'.strtoupper($eclass).'</b>';

}elseif($encounter_type == 2){
	$fcolor='blue';
	$eclass='<b>'.strtoupper($eclass).'</b>';
}

#echo "aufnahme_daten_zeigen.php : encounter_status = '".$encounter_status."' <br> \n";
if ($encounter_status=='direct_admission'){
	$fcolor='green';
	$eclass='<b>'.strtoupper($LDInpatientDirectAdmission).'</b>';
}

	$smarty->assign('sAdmitClassInput',"<font color=$fcolor>$eclass</font>");

			# Official receipt number, available/required ONLY when generating OPD encounter
			if (($encounter_type == 2) || ($encounter_type == 4)) {
				$smarty->assign('segORNumber',"OR Number");				
				$smarty->assign('sORNumber',ucwords(strtolower(trim($official_receipt_nr))));
			}


#----------comment 03-05-07-------------------
/*
if($encounter_class_nr==1){
	
	$smarty->assign('LDWard',$LDWard);

	$smarty->assign('sWardInput','<a href="'.$root_path.'modules/nursing/'.strtr('nursing-station-pass.php'.URL_APPEND.'&rt=pflege&edit=1&station='.$current_ward_name.'&location_id='.$current_ward_name.'&ward_nr='.$current_ward_nr,' ',' ').'">'.$current_ward_name.'</a>');

}elseif($encounter_class_nr==2){

	$smarty->assign('LDWard',"$LDClinic/$LDDepartment");

	$smarty->assign('sWardInput','<a href="'.$root_path.'modules/ambulatory/'.strtr('amb_clinic_patients_pass.php'.URL_APPEND.'&rt=pflege&edit=1&dept='.$$current_dept_LDvar.'&location_id='.$$current_dept_LDvar.'&dept_nr='.$current_dept_nr,' ',' ').'">'.$current_dept_name.'</a>');

}
*/

#information info
if ($encounter_class_nr!=2){  # not OPD
	$smarty->assign('LDInformant',$LDInformant);
	$smarty->assign('informant_name',ucwords(strtolower(trim($informant_name))));

	$smarty->assign('LDInfoAdd',$LDInfoAdd);
	$smarty->assign('info_address',ucwords(strtolower(trim($info_address))));

	$smarty->assign('LDInfoRelation',$LDInfoRelation);
	$smarty->assign('relation_informant',ucwords(strtolower(trim($relation_informant))));
}	
/*
echo "current_att_dr_nr = '".$current_att_dr_nr."' <br> \n";
echo "encounter_class_nr = '".$encounter_class_nr."' <br> \n";
echo "encounter_type = '".$encounter_type."' <br> \n";
*/
	# burn added : May 17, 2007
$consulting_dr_name='';
if ($current_att_dr_nr){
	if ($doc_info = $pers_obj->getPersonellInfo($current_att_dr_nr)){
#		echo "doc_info : <br> "; print_r($doc_info); echo" <br> \n";
#		echo "doc_info['name_last'] = '".$doc_info['name_last']."' <br> \n";

		$middleInitial = "";
		if (trim($doc_info['name_middle'])!=""){
			$thisMI=split(" ",$doc_info['name_middle']);	
			foreach($thisMI as $value){
				if (!trim($value)=="")
					$middleInitial .= $value[0];
			}
			if (trim($middleInitial)!="")
				$middleInitial = " ".$middleInitial.".";
		}
			# the lastest attending/consultin physician
		$consulting_dr_name="Dr. ".$doc_info['name_first']." ".$doc_info['name_2'].$middleInitial." ".$doc_info['name_last'];
	}
}

#for IPD with ward
if (($dept_belong['id']=="Admission")&&(($encounter_type==3)||($encounter_type==4))){
	$smarty->assign('LDWard',$LDWard);
	$smarty->assign('sWardInput','<a href="'.$root_path.'modules/nursing/'.strtr('nursing-station-pass.php'.URL_APPEND.'&rt=pflege&edit=1&station='.$current_ward_name.'&location_id='.$current_ward_name.'&ward_nr='.$current_ward_nr,' ',' ').'">'.$current_ward_name.'</a>');
	
	$smarty->assign('LDDoctor',$LDDoctor3);
	$smarty->assign('doctor_name',$consulting_dr_name);

}elseif($encounter_class_nr==1){
	$smarty->assign('LDDoctor',$LDDoctor3);
	$smarty->assign('doctor_name',$consulting_dr_name);
	
}elseif($encounter_class_nr==2){
	$smarty->assign('LDDoctor',$LDDoctor1);
	$smarty->assign('doctor_name',$consulting_dr_name);
}

	#$smarty->assign('doctor_name',"Dr. ".$consulting_dr);
	
	$smarty->assign('LDDepartment',"$LDClinic/$LDDepartment");
	$smarty->assign('sDeptInput','<a href="'.$root_path.'modules/ambulatory/'.strtr('amb_clinic_patients_pass.php'.URL_APPEND.'&rt=pflege&edit=1&dept='.$$current_dept_LDvar.'&location_id='.$$current_dept_LDvar.'&dept_nr='.$current_dept_nr,' ',' ').'">'.$current_dept_name.'</a>');

	# burn added : May 17, 2007
if ($encounter_type==1){
	$smarty->assign('LDDepartment',"Consulting $LDDepartment");
	$smarty->assign('LDDoctor',"Consulting Physician");
	$smarty->assign('doctor_name',$consulting_dr_name);
}elseif ($encounter_type==2){
	$smarty->assign('LDDepartment',"Consulting $LDClinic");
	$smarty->assign('LDDoctor',"Consulting Physician");
	$smarty->assign('doctor_name',$consulting_dr_name);
}elseif (($encounter_type==3)||($encounter_type==4)){
	$smarty->assign('LDDepartment',"Attending $LDDepartment");
	$smarty->assign('LDDoctor',"Attending Physician");
	$smarty->assign('doctor_name',$consulting_dr_name);
}


#-------------added 03-14-07-------------

if ($dept_belong['id']!="OPD-Triage"){

	$smarty->assign('LDDiagnosis',$LDDiagnosis);
	$smarty->assign('referrer_diagnosis',ucwords(strtolower(trim($referrer_diagnosis))));

#	$smarty->assign('LDTherapy',$LDTherapy);   # burn commented : May 16, 2007
#	$smarty->assign('referrer_recom_therapy',ucwords(strtolower(trim($referrer_recom_therapy))));   # burn commented : May 16, 2007

	$smarty->assign('LDRecBy',$LDRecBy);
	#echo "referrer_dr = ".$referrer_dr;
	#echo "bol = ".is_numeric($referrer_dr);
	$dr_name = $pers_obj->get_Person_name($referrer_dr);
	$dr_fname = ucwords(strtolower(trim($dr_name['name_first'])));
	$dr_fname2 = ucwords(strtolower(trim($dr_name['name_2'])));
	$dr_lname = ucwords(strtolower(trim($dr_name['name_last'])));
	$ref_dr_name= "Dr. ".$dr_fname." ".$dr_fname2." ".$dr_lname;
	
	if (is_numeric($referrer_dr)){
		$smarty->assign('referrer_dr_name',ucwords(strtolower(trim($ref_dr_name))));
	}else{
		$smarty->assign('referrer_dr_name',ucwords(strtolower(trim($referrer_dr))));	
	}
	
	$smarty->assign('LDRecDept',$LDRecDept);
	
	if (is_numeric($referrer_dept)){
		$smarty->assign('referrer_dept_name',trim($dept_obj->FormalName($referrer_dept)));
	}else{	
		$smarty->assign('referrer_dept_name',ucwords(strtolower(trim($referrer_dept))));
	}
		
	if ($encounter_class_nr!=2){  # not OPD
		$smarty->assign('LDRecIns',$LDRecIns);
		$smarty->assign('referrer_institution',ucwords(strtolower(trim($referrer_institution))));
	}
	
	$smarty->assign('LDSpecials',$LDSpecials);
	$smarty->assign('referrer_notes',ucwords(strtolower(trim($referrer_notes))));
	
 if($dept_belong['id']!="ER"){	
	$smarty->assign('LDBillType',$LDBillType);

	if (isset($$insurance_class['LD_var'])&&!empty($$insurance_class['LD_var'])) $smarty->assign('sBillTypeInput',$$insurance_class['LD_var']);
    	else $smarty->assign('sBillTypeInput',$insurance_class['name']); 
	
	$smarty->assign('LDInsuranceNr',$LDInsuranceNr);
	if(isset($insurance_nr)&&$insurance_nr) $smarty->assign('insurance_nr2',$insurance_nr);

	$smarty->assign('LDInsuranceCo',$LDInsuranceCo);
	$smarty->assign('insurance_firm_name2',$insurance_firm_name);
	}	# ---------end if ER
} #-----end if OPD

	$smarty->assign('LDFrom',$LDFrom);
	$smarty->assign('LDTo',$LDTo);


if(!$GLOBAL_CONFIG['patient_service_care_hide'] && $sc_care_class_nr){
	$smarty->assign('LDCareServiceClass',$LDCareServiceClass);

	while($buffer=$care_service->FetchRow()){
		if($sc_care_class_nr==$buffer['class_nr']){
			if(empty($$buffer['LD_var'])) $smarty->assign('sCareServiceInput',$buffer['name']);
				else $smarty->assign('sCareServiceInput',$$buffer['LD_var']);
			break;
		}
	}

	if($sc_care_start && $sc_care_start != DBF_NODATE){
		$smarty->assign('sCSFromInput',' [ '.@formatDate2Local($sc_care_start,$date_format).' ] ');
		$smarty->assign('sCSToInput',' [ '.@formatDate2Local($sc_care_end,$date_format).' ]');
	}
}


if(!$GLOBAL_CONFIG['patient_service_room_hide'] && $sc_room_class_nr){
	$smarty->assign('LDRoomServiceClass',$LDRoomServiceClass);

	while($buffer=$room_service->FetchRow()){
		if($sc_room_class_nr==$buffer['class_nr']){
			if(empty($$buffer['LD_var'])) $smarty->assign('sCareRoomInput',$buffer['name']); 
				else $smarty->assign('sCareRoomInput',$$buffer['LD_var']);
				break;
		}
	}
	if($sc_room_start && $sc_room_start != DBF_NODATE){
		$smarty->assign('sRSFromInput',' [ '.@formatDate2Local($sc_room_start,$date_format).' ] ');
		$smarty->assign('sRSToInput',' [ '.@formatDate2Local($sc_room_end,$date_format).' ]');
	}
}

if(!$GLOBAL_CONFIG['patient_service_att_dr_hide'] && $sc_att_dr_class_nr){
	$smarty->assign('LDAttDrServiceClass',$LDAttDrServiceClass);

	while($buffer=$att_dr_service->FetchRow()){
		if($sc_att_dr_class_nr==$buffer['class_nr']){
			if(empty($$buffer['LD_var'])) $smarty->assign('sCareDrInput',$buffer['name']);
				else $smarty->assign('sCareDrInput',$$buffer['LD_var']);
			break;
		}
	}
	if($sc_att_dr_start && $sc_att_dr_start != DBF_NODATE){
		$smarty->assign('sDSFromInput',' [ '.@formatDate2Local($sc_att_dr_start,$date_format).' ] ');
		$smarty->assign('sDSToInput',' [ '.@formatDate2Local($sc_att_dr_end,$date_format).' ]');
	}
}

#--------------------added 03-08-07----------------------
if (($dept_belong['id']=="Admission")&&(($patient_enc['encounter_type']==3)||($patient_enc['encounter_type']==4))){
	$smarty->assign('LDCondition',$LDCondition);
	$smarty->assign('sCondition',$encounter_condition['cond_desc']); 
	$smarty->assign('LDResults',$LDResults);
	$smarty->assign('sResults',$encounter_result['result_desc']); 
	$smarty->assign('LDDisposition',$LDDisposition);
	$smarty->assign('sDisposition',$encounter_disposition['disp_desc']); 
}	

#-------------------------------------------------------

$smarty->assign('LDAdmitBy',$LDAdmitBy);
#if (empty($encoder)) $encoder = $HTTP_COOKIE_VARS[$local_user.$sid];  #-----comment by van 03-16-07
if (empty($encoder)) $encoder = $patient_enc['modify_id'];
$smarty->assign('encoder',$encoder);

$smarty->assign('LDDeptBelong',$LDDepartment);
$smarty->assign('sDeptBelong',$dept_belong['name_formal']);

# Buffer the options block

ob_start();
	
	require('./gui_bridge/default/gui_patient_encounter_showdata_options.php');
	$sTemp = ob_get_contents();

ob_end_clean();

$smarty->assign('sAdmitOptions',$sTemp);
$sTemp = '';

if(!$is_discharged){

	# Buffer the control buttons
	ob_start();
		
		include('./include/bottom_controls_admission.inc.php');
		$sTemp = ob_get_contents();
		
	ob_end_clean();
	
	$smarty->assign('sAdmitBottomControls',$sTemp);
}


$breakfile = 'patient_register_show.php'.URL_APPEND.'&pid='.$pid;  #-----added by vanessa--------

$smarty->assign('pbBottomClose','<a href="'.$breakfile.'"><img '.createLDImgSrc($root_path,'close2.gif','0').'  title="'.$LDCancel.'"  align="absmiddle"></a>');



if ($dept_belong['id']!="Admission"){
	if ($dept_belong['id']=="ER"){
		$smarty->assign('sAdmitLink','<img '.createComIcon($root_path,'varrow.gif','0').'> <a href="aufnahme_start.php'.URL_APPEND.'&mode=?">'.$LDIPDWantEntry.'</a>');
	}elseif ($dept_belong['id']=="OPD-Triage"){
		$smarty->assign('sAdmitLink','<img '.createComIcon($root_path,'varrow.gif','0').'> <a href="aufnahme_start.php'.URL_APPEND.'&mode=?">'.$LDOPDWantEntry.'</a>');
	}		
}
$smarty->assign('sSearchLink','<img '.createComIcon($root_path,'varrow.gif','0').'> <a href="aufnahme_daten_such.php'.URL_APPEND.'">'.$LDAdmWantSearch.'</a>');
$smarty->assign('sArchiveLink','<img '.createComIcon($root_path,'varrow.gif','0').'> <a href="aufnahme_list.php'.URL_APPEND.'&newdata=1">'.$LDAdmWantArchive.'</a>');

$smarty->assign('sMainBlockIncludeFile','registration_admission/admit_show.tpl');

$smarty->display('common/mainframe.tpl');

?>
