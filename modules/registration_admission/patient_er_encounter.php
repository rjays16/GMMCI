<?php
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');

	include_once($root_path.'include/inc_date_format_functions.php');
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	
	require_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj=new Person();
		/* Create encounter object */
	$encounter_obj=new Encounter($encounter_nr);
	/* Get the patient global configs */
	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('patient_%');
	$glob_obj->getConfig('encounter_%'); 

	$newdata=1;
	
	if ($_GET['ptype'])
		$ptype = $_GET['ptype'];
	elseif ($_POST['ptype'])
		$ptype = $_POST['ptype'];	
	#echo "ptype = ".$ptype;	
    
    if ($_GET['encounter_date'])
        $encounter_date = $_GET['encounter_date'];
    else
        $encounter_date = date('Y-m-d H:i:s');    
    

		/* Determine the format of the encounter number */
	if($GLOBAL_CONFIG['encounter_nr_fullyear_prepend'])
		$ref_nr=(int)date('Y').$GLOBAL_CONFIG['encounter_nr_init'];
	else 
		$ref_nr=$GLOBAL_CONFIG['encounter_nr_init'];
		//echo $ref_nr;
		# get an encounter number
		# NOTE: 2nd argument is ALWAYS 1 --- this file is ONLY being used by ER
	#$HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr+$GLOBAL_CONFIG['patient_er_nr_adder'],1);
	  $triage = 'er';
		$last_enc_nr = $encounter_obj->getLastEncounterNr($triage);
							
		if ($last_enc_nr)
			$ref_nr = $last_enc_nr;
									
		$HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr,1);
		
  	if($encoder=='') 
		$encoder=$HTTP_SESSION_VARS['sess_login_username'];
		
	$HTTP_POST_VARS['pid']=$pid;
	$HTTP_POST_VARS['current_dept_nr']=$current_dept_nr;
	
	$HTTP_POST_VARS['category']=$category;
	
	#$HTTP_POST_VARS['encounter_date']=date('Y-m-d H:i:s');
    $HTTP_POST_VARS['encounter_date']=$encounter_date;
	$HTTP_POST_VARS['encounter_class_nr']=1;   # NOTE: 2nd argument is ALWAYS 1 --- this file is ONLY being used by ER
	$HTTP_POST_VARS['encounter_type']=1;
	$HTTP_POST_VARS['modify_id']=$encoder;
	$HTTP_POST_VARS['modify_time']=date('YmdHis');
	$HTTP_POST_VARS['create_id']=$encoder;
	$HTTP_POST_VARS['create_time']=date('YmdHis');
	$HTTP_POST_VARS['history']='Create: '.date('Y-m-d H:i:s').' = '.$encoder;
	
	$encounter_obj->setDataArray($HTTP_POST_VARS);
	$db->StartTrans();								
	if($encounter_obj->insertDataFromInternalArray()){
		$bSuccess = $encounter_obj->update_Encounter_Tracker($HTTP_POST_VARS['encounter_nr'],$triage);
		/* Get last insert id */
		if($dbtype=='mysql'){
			$encounter_nr=$db->Insert_ID();
		}else{
			$encounter_nr=$encounter_obj->postgre_Insert_ID($dbtable,'encounter_nr',$db->Insert_ID());
		}
	  	if(empty($encounter_nr)) 
			$encounter_nr=$HTTP_POST_VARS['encounter_nr'];

		header("Location: aufnahme_daten_zeigen.php".URL_REDIRECT_APPEND."&encounter_nr=$encounter_nr&origin=admit&target=entry&newdata=1&ERSave=1&ptype=$ptype");
	}else{
		echo $LDDbNoSave.'<p>'.$encounter_obj->getLastQuery();
	}
	if (!$bSuccess) $db->FailTrans();
	$db->CompleteTrans();
?>