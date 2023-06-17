<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System beta 2.0.1 - 2004-07-04
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
#echo "start";

#--------------- EDITED BY VANESSA -----------------------

$lang_tables[]='departments.php';
$lang_tables[]='prompt.php';
$lang_tables[]='help.php';
$lang_tables[]='person.php';
define('LANG_FILE','aufnahme.php');
$local_user='aufnahme_user';

#ob_start();  #---------------ob-van----

require($root_path.'include/inc_front_chain_lang.php');

/* If patient nr is invallid jump to registration search module*/
/*if(!isset($pid) || !$pid)
{
	header('Location:patient_register_search.php'.URL_APPEND.'&origin=admit');
	exit;
}
*/
//require_once($root_path.'include/inc_config_color.php');
require_once($root_path.'include/inc_date_format_functions.php');

require_once($root_path.'include/care_api_classes/class_person.php');

require_once($root_path.'include/care_api_classes/class_insurance.php');

//require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');

require_once($root_path.'include/care_api_classes/class_personell.php');

include_once($root_path.'include/care_api_classes/class_department.php');

$dept_obj=new Department;
$pers_obj=new Personell;

#---------------ob-van----
#$x=ob_get_contents(); 
#ob_end_clean();
#var_dump($x);
#exit();
#---------------ob-van----

#-------added 03-07-07------------
#$dept_belong = $dept_obj->getUserDeptInfo($HTTP_SESSION_VARS['sess_user_name']);
	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);

#echo "<br>User : ".$HTTP_SESSION_VARS['sess_user_name'];

/*if ($dept_belong['id'] == "Admission"){
	#aufnahme_daten_such.php'.URL_APPEND.'&target=search
	#header("Location:aufnahme_daten_such.php?sid=8335788c6754e4f6f98fcc0dea1deb77&lang=en&target=search");
	header("location:aufnahme_daten_such.php".URL_REDIRECT_APPEND."&lang=en&target=search");
	exit();
}*/
#---------------------------------

$thisfile=basename(__FILE__);
if($origin=='patreg_reg') $breakfile = 'patient_register_show.php'.URL_APPEND.'&pid='.$pid;
	elseif($HTTP_COOKIE_VARS["ck_login_logged".$sid]) $breakfile = $root_path.'main/startframe.php'.URL_APPEND;
		elseif(!empty($HTTP_SESSION_VARS['sess_path_referer'])) $breakfile=$root_path.$HTTP_SESSION_VARS['sess_path_referer'].URL_APPEND.'&pid='.$pid;
			else $breakfile = "aufnahme_pass.php".URL_APPEND."&target=entry";

$newdata=1;

/* Default path for fotos. Make sure that this directory exists! */
$default_photo_path=$root_path.'fotos/registration';
$photo_filename='nopic';
$error=0;

if(!isset($pid)) $pid=0;
if(!isset($encounter_nr)) $encounter_nr=0;
if(!isset($mode)) $mode='';
if(!isset($forcesave)) $forcesave=0;
if(!isset($update)) $update=0;

if(!session_is_registered('sess_pid')) session_register('sess_pid');
if(!session_is_registered('sess_full_pid')) session_register('sess_full_pid');
if(!session_is_registered('sess_en')) session_register('sess_en');
if(!session_is_registered('sess_full_en')) session_register('sess_full_en');

$patregtable='care_person';  // The table of the patient registration data

$dbtable='care_encounter'; // The table of admission data

/* Create new person's insurance object */
$pinsure_obj=new PersonInsurance($pid);	 
/* Get the insurance classes */
$insurance_classes=&$pinsure_obj->getInsuranceClassInfoObject('class_nr,name,LD_var AS "LD_var"');

/* Create new person object */
$person_obj=new Person($pid);
/* Create encounter object */
$encounter_obj=new Encounter($encounter_nr);
/* Get all encounter classes */
$encounter_classes=$encounter_obj->AllEncounterClassesObject();


#-------added 03-08-07------------

if ($encounter_nr!=NULL){
	
	$patient_enc = $encounter_obj->getPatientEncounter($encounter_nr);
	$patient_enc_cond = $encounter_obj->getPatientEncounterCond($encounter_nr);
	$patient_enc_disp = $encounter_obj->getPatientEncounterDisp($encounter_nr);
	$patient_enc_res = $encounter_obj->getPatientEncounterRes($encounter_nr);
	
	$condition_classes=$encounter_obj->AllConditionClassesObject();
	$results_classes=$encounter_obj->AllResultsClassesObject();
	$disposition_classes=$encounter_obj->AllDispositionClassesObject();
	
	$patient_doc = $dept_obj->getDeptAllInfo($patient_enc['current_dept_nr']);
	$patient_consulting_doc = $dept_obj->getDeptAllInfo($patient_enc['consulting_dept_nr']);   # burn added : May 17, 2007	
	$patient_dept_nr = $patient_enc['current_dept_nr'];
	
	#-------added by van 04-30-07--------
	$dr_dept = $pers_obj->get_Dr_Dept_nr($name1, $name2, $lname);
	$ref_dr = $dr_dept['personell_nr'];
	$ref_dept = $dr_dept['nr'];
	#-----------------------------------
	
		# burn added : May 24, 2007
	if ($patient_enc['encounter_status']=='direct_admission') 
		$seg_direct_admission=1;   # set to indicate direct admission

	if (($patient_enc['encounter_type']==3)||($patient_enc['encounter_type']==4)){
		$isadmitted = 1;
		if ($patient_enc['encounter_type']==3){
			$eclass = $LDStationary2;
		}elseif($patient_enc['encounter_type']==4){
			$eclass = $LDAmbulant2;
		}
	}else{
		$isadmitted = 0;
	}
}
#--------------------------------

if($pid!='' || $encounter_nr!=''){

	   	/* Get the patient global configs */
        $glob_obj=new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('patient_%');
        $glob_obj->getConfig('person_foto_path'); 
        $glob_obj->getConfig('encounter_%'); 
		
		if(!$GLOBAL_CONFIG['patient_service_care_hide']){
			/* Get the care service classes*/
			$care_service=$encounter_obj->AllCareServiceClassesObject();
		}
		if(!$GLOBAL_CONFIG['patient_service_room_hide']){
			/* Get the room service classes */
			$room_service=$encounter_obj->AllRoomServiceClassesObject();
		}
		if(!$GLOBAL_CONFIG['patient_service_att_dr_hide']){
			/* Get the attending doctor service classes */
			$att_dr_service=$encounter_obj->AllAttDrServiceClassesObject();
		}		
		
        /* Check whether config path exists, else use default path */			
        $photo_path = (is_dir($root_path.$GLOBAL_CONFIG['person_foto_path'])) ? $GLOBAL_CONFIG['person_foto_path'] : $default_photo_path;

        if ($pid)
        {	
		  		#------------commented by vanessa 03-14-07--------------
				/*
		  		# Check whether the person is currently admitted. If yes jump to display admission data
		  		if(!$update&&$encounter_nr=$encounter_obj->isPIDCurrentlyAdmitted($pid)){
		      	header('Location:aufnahme_daten_zeigen.php'.URL_REDIRECT_APPEND.'&encounter_nr='.$encounter_nr.'&origin=admit&sem=isadmitted&target=entry');
			  		exit;
		  		}
 			 	*/
				       
			 /* Get the related insurance data */
			 $p_insurance=&$pinsure_obj->getPersonInsuranceObject($pid);
			 if($p_insurance==false) {
				$insurance_show=true;
			 } else {
				if(!$p_insurance->RecordCount()) {
				    $insurance_show=true;
				} elseif ($p_insurance->RecordCount()==1){
				    $buffer= $p_insurance->FetchRow();
					extract($buffer);
				    $insurance_show=true;
				    $insurance_firm_name=$pinsure_obj->getFirmName($insurance_firm_id); 
				} else { $insurance_show=false;}
			 } 

			
            if (($mode=='save') || ($forcesave!=''))
            {	
					 #echo "sulod diri:mode =".$mode." - ".!$forcesave;	
	             if(!$forcesave)
	             {
	                  //clean and check input data variables
					  /**
					  *  $error = 1 will cause to show the "save anyway" override button to save the incomplete data
					  *  $error = 2 will cause to force the user to enter a data in an input element (no override allowed)
					  */
	                  $encoder=trim($encoder); 
					  if($encoder=='') $encoder=$HTTP_SESSION_VARS['sess_user_name'];
					  
					  #----------commented 03-14-07------------
					  /*
	                  $referrer_diagnosis=trim($referrer_diagnosis);
					  if ($referrer_diagnosis=='') { $errordiagnose=1; $error=1; $errornum++; };
					  
	                  $referrer_dr=trim($referrer_dr);
					  if ($referrer_dr=='') { $errorreferrer=1; $error=1; $errornum++;};
					  
	                  $referrer_recom_therapy=trim($referrer_recom_therapy);
					  if ($referrer_recom_therapy=='') { $errortherapie=1; $error=1; $errornum++;};
					  
	                  $referrer_notes=trim($referrer_notes);
					  if ($referrer_notes=='') { $errorbesonder=1; $error=1; $errornum++;};
					  
	                  $encounter_class_nr=trim($encounter_class_nr);
					  if ($encounter_class_nr=='') { $errorstatus=1; $error=1; $errornum++;};
	
			          if($insurance_show) {
                          if(trim($insurance_nr) &&  trim($insurance_firm_name)=='') { $error_ins_co=1; $error=1; $errornum++;}
		              }
						  */
	              }
 				
					

                 if(!$error) 
	             {	
					
						if(!$GLOBAL_CONFIG['patient_service_care_hide']){
						    if(!empty($sc_care_start)) $sc_care_start=formatDate2Std($sc_care_start,$date_format);
						    if(!empty($sc_care_end)) $sc_care_end=formatDate2Std($sc_care_end,$date_format);
						    $care_class=compact('sc_care_nr','sc_care_class_nr', 'sc_care_start', 'sc_care_end','encoder');
						}
						if(!$GLOBAL_CONFIG['patient_service_room_hide']){
						    if(!empty($sc_room_start)) $sc_room_start=formatDate2Std($sc_room_start,$date_format);
						    if(!empty($sc_room_end)) $sc_room_end=formatDate2Std($sc_room_end,$date_format);
						    $room_class=compact('sc_room_nr','sc_room_class_nr', 'sc_room_start', 'sc_room_end','encoder');
						}
						if(!$GLOBAL_CONFIG['patient_service_att_dr_hide']){
						    if(!empty($sc_att_dr_start)) $sc_att_dr_start=formatDate2Std($sc_att_dr_start,$date_format);
						    if(!empty($sc_att_dr_end)) $sc_att_dr_end=formatDate2Std($sc_att_dr_end,$date_format);
						    $att_dr_class=compact('sc_att_dr_nr','sc_att_dr_class_nr','sc_att_dr_start', 'sc_att_dr_end','encoder');
						}

				      if($update || $encounter_nr)
					  {
							//echo formatDate2STD($geburtsdatum,$date_format);
							$itemno=$itemname;		
									$HTTP_POST_VARS['modify_id']=$encoder;
									if($dbtype=='mysql'){
										$HTTP_POST_VARS['history']= "CONCAT(history,\"\n Update: ".date('Y-m-d H:i:s')." = $encoder\")";
									}else{
										$HTTP_POST_VARS['history']= "(history || '\n Update: ".date('Y-m-d H:i:s')." = $encoder')";
									}
									if(isset($HTTP_POST_VARS['encounter_nr'])) unset($HTTP_POST_VARS['encounter_nr']);		
									if(isset($HTTP_POST_VARS['pid'])) unset($HTTP_POST_VARS['pid']);		
									
								#------------edited by vanessa 03-27-07--------
									if ($informant_name==NULL)
										$HTTP_POST_VARS['informant_name']= " ";
									if ($info_address==NULL)
										$HTTP_POST_VARS['info_address']= " ";
									if ($relation_informant==NULL)
										$HTTP_POST_VARS['relation_informant']= " ";
									if ($current_ward_nr==NULL)
										$HTTP_POST_VARS['current_ward_nr']= " ";
									if ($referrer_diagnosis==NULL)
										$HTTP_POST_VARS['referrer_diagnosis']= " ";
									if ($referrer_recom_therapy==NULL)
										$HTTP_POST_VARS['referrer_recom_therapy']= " ";
									if (($referrer_dr==NULL)||($dr_dept['personell_nr']==NULL)){
										$HTTP_POST_VARS['referrer_dr']= $referrer_dr_name;
										$HTTP_POST_VARS['referrer_dept']= " ";
									}elseif ($dr_dept['personell_nr']!=NULL){	
										#$HTTP_POST_VARS['referrer_dr'] = $dr_dept['personell_nr'];   # burn commented : May 17, 2007
										#$HTTP_POST_VARS['referrer_dept'] = $dr_dept['nr'];   # burn commented : May 17, 2007
									}elseif($referrer_dr!=NULL){
										$HTTP_POST_VARS['referrer_dr']= $referrer_dr;
									}	
									if ($referrer_institution==NULL)
										$HTTP_POST_VARS['referrer_institution']= " ";
									if ($referrer_notes==NULL)
										$HTTP_POST_VARS['referrer_notes']= " ";
								#-----------------------------------------------	
												
										# burn added: March 28, 2007
									$HTTP_POST_VARS['admission_dt']=NULL; 
									if ( ($HTTP_POST_VARS['firstUpdate']) &&
										  (($HTTP_POST_VARS['encounter_type']==3)||($HTTP_POST_VARS['encounter_type']==4))){
										$HTTP_POST_VARS['admission_dt']=date('Y-m-d H:i:s'); 
									}

										# burn added: June 1, 2007
										# first entry, consulting and current dept/doctor are the same, 
										# except for direct admission
									if (($HTTP_POST_VARS['encounter_type']==1)||($HTTP_POST_VARS['encounter_type']==2)){
										$HTTP_POST_VARS['consulting_dept_nr']=$HTTP_POST_VARS['current_dept_nr'];
										$HTTP_POST_VARS['consulting_dr_nr']=$HTTP_POST_VARS['current_att_dr_nr'];
									}

									$encounter_obj->setDataArray($HTTP_POST_VARS);

									if($encounter_obj->updateEncounterFromInternalArray($encounter_nr))
									{	
									    /* Save the service classes */									   
									    if(!$GLOBAL_CONFIG['patient_service_care_hide']){
										    $encounter_obj->updateCareServiceClass($care_class);
										 }
									    if(!$GLOBAL_CONFIG['patient_service_room_hide']){
										    $encounter_obj->updateRoomServiceClass($room_class);
										 }
									    if(!$GLOBAL_CONFIG['patient_service_att_dr_hide']){
										    $encounter_obj->updateAttDrServiceClass($att_dr_class);
										 }
										 
										#----------------save a data to seg_encounter_condition, disposition, and result-----------
										if ($dept_belong['id']=="Admission"){
											#---------condition----------------------
											if (isset($HTTP_POST_VARS['cond_code'])){
												$cond_prev = $encounter_obj->getEncounterConditionInfo($patient_enc_cond['cond_code']);
												$cond_current = $encounter_obj->getEncounterConditionInfo($cond_code);
												
												if (($patient_enc_disp['encounter_nr']==$encounter_nr)&&($cond_current['area_used']==$cond_prev['area_used'])){
													$condition['cond_code']=$cond_code;
													$condition['modify_id']=$encoder;
													$condition['modify_time']=date('YmdHis');
													$encounter_obj->setDataArray($condition);
													
												   if(!@$encounter_obj->updateEncounterCondition($encounter_nr,$patient_enc_cond['cond_code'])) echo "<br>$LDDbNoSave";
												}else{
													$condition['encounter_nr'] = $encounter_nr;
													$condition['cond_code']=$cond_code;
													$condition['modify_id']=$encoder;
													$condition['modify_time']=date('YmdHis');
													$condition['create_id']=$encoder;
													$condition['create_time']=date('YmdHis');
													$encounter_obj->setDataArray($condition);
													
													if(!@$encounter_obj->saveEncounterCondition($condition)) echo "<br>$LDDbNoSave";
												}	
											}
											#----------disposition----------------------
											if(isset($HTTP_POST_VARS['disp_code'])){
												$disp_prev = $encounter_obj->getEncounterDispositionInfo($patient_enc_disp['disp_code']);
												$disp_current = $encounter_obj->getEncounterDispositionInfo($disp_code);
												
												if (($patient_enc_disp['encounter_nr']==$encounter_nr)&&($disp_current['area_used']==$disp_prev['area_used'])){
												   
													$disposition['disp_code']=$disp_code;
													$disposition['modify_id']=$encoder;
													$disposition['modify_time']=date('YmdHis');
													$encounter_obj->setDataArray($disposition);
													
													if(!@$encounter_obj->updateEncounterDisposition($encounter_nr,$patient_enc_disp['disp_code'])) echo "<br>$LDDbNoSave";
												
												}else{
													$disposition['encounter_nr'] = $encounter_nr;
													$disposition['disp_code']=$disp_code;
													$disposition['modify_id']=$encoder;
													$disposition['modify_time']=date('YmdHis');
													$disposition['create_id']=$encoder;
													$disposition['create_time']=date('YmdHis');
													$encounter_obj->setDataArray($disposition);
													
													if(!@$encounter_obj->saveEncounterDisposition($disposition)) echo "<br>$LDDbNoSave";
												}	
											#----------results-------------------------
											}
											
											if(isset($HTTP_POST_VARS['result_code'])){
												$res_prev = $encounter_obj->getEncounterResultInfo($patient_enc_res['result_code']);
												$res_current = $encounter_obj->getEncounterResultInfo($result_code);
												
												if (($patient_enc_res['encounter_nr']==$encounter_nr)&&($res_current['area_used']==$res_prev['area_used'])){
													$result['result_code']=$result_code;
													$result['modify_id']=$encoder;
													$result['modify_time']=date('YmdHis');
													$encounter_obj->setDataArray($result);
													
													if(!@$encounter_obj->updateEncounterResults($encounter_nr, $patient_enc_res['result_code'])) echo "<br>$LDDbNoSave";
												}else{
													$result['encounter_nr'] = $encounter_nr;
													$result['result_code']=$result_code;
													$result['modify_id']=$encoder;
													$result['modify_time']=date('YmdHis');
													$result['create_id']=$encoder;
													$result['create_time']=date('YmdHis');
													$encounter_obj->setDataArray($result);
													
													if(!@$encounter_obj->saveEncounterResults($result)) echo "<br>$LDDbNoSave";
												}	
											}
										}
										#-----------------------------------------	
										  #header("Location: show_opd_clinical_form.php".URL_REDIRECT_APPEND."&encounter_nr=$encounter_nr&target=_blank"); 	
							           header("Location: aufnahme_daten_zeigen.php".URL_REDIRECT_APPEND."&encounter_nr=$encounter_nr&origin=admit&target=entry&newdata=$newdata&update=1&cond_code=$cond_code&disp_code=$disp_code&result_code=$result_code&enc_type=".$patient_enc['encounter_type']); 
								        exit;
								    }
									 
								
					  
					  
					  }else{ #if($update || $encounter_nr)
					  	    $newdata=1;
							/* Determine the format of the encounter number */
							if($GLOBAL_CONFIG['encounter_nr_fullyear_prepend']) $ref_nr=(int)date('Y').$GLOBAL_CONFIG['encounter_nr_init'];
								else $ref_nr=$GLOBAL_CONFIG['encounter_nr_init'];
							switch($HTTP_POST_VARS['encounter_class_nr'])
							{
								#case '1':
								#case '3': 
								case '1': $HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr+$GLOBAL_CONFIG['patient_inpatient_nr_adder'],1);
											break;
								case '2': $HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr+$GLOBAL_CONFIG['patient_outpatient_nr_adder'],2);
							}
								
								# burn added: May 24, 2007
							$HTTP_POST_VARS['admission_dt']=NULL; 
							if ( (!isset($HTTP_POST_VARS['update'])) &&
								  (isset($HTTP_POST_VARS['encounter_status']))
								){
								$HTTP_POST_VARS['admission_dt']=date('Y-m-d H:i:s'); 
							}

								# burn added: May 17, 2007
								# first entry, consulting and current dept/doctor are the same, 
								# except for direct admission
							if (!isset($HTTP_POST_VARS['encounter_status'])){
								$HTTP_POST_VARS['consulting_dept_nr']=$HTTP_POST_VARS['current_dept_nr'];
								$HTTP_POST_VARS['consulting_dr_nr']=$HTTP_POST_VARS['current_att_dr_nr'];
							}

							$HTTP_POST_VARS['encounter_date']=date('Y-m-d H:i:s');
							$HTTP_POST_VARS['modify_id']=$encoder;
							//$HTTP_POST_VARS['modify_time']='NULL';
							$HTTP_POST_VARS['create_id']=$encoder;
							$HTTP_POST_VARS['create_time']=date('YmdHis');
							$HTTP_POST_VARS['history']='Create: '.date('Y-m-d H:i:s').' = '.$encoder;

							$encounter_obj->setDataArray($HTTP_POST_VARS);

									if($encounter_obj->insertDataFromInternalArray())
									{
									    /* Get last insert id */
										if($dbtype=='mysql'){
											$encounter_nr=$db->Insert_ID();
										}else{
											$encounter_nr=$encounter_obj->postgre_Insert_ID($dbtable,'encounter_nr',$db->Insert_ID());
										}
										
										# If appointment number available, mark appointment as "done"
										if(isset($appt_nr)&&$appt_nr) $encounter_obj->markAppointmentDone($appt_nr,$HTTP_POST_VARS['encounter_class_nr'],$encounter_nr);
							            header("Location: aufnahme_daten_zeigen.php".URL_REDIRECT_APPEND."&encounter_nr=".$HTTP_POST_VARS['encounter_nr']."&origin=admit&target=entry&newdata=$newdata"); 
										   exit;
								    }else{
										echo $LDDbNoSave.'<p>'.$encounter_obj->getLastQuery();
									}
									
					 }// end of if(update) else()                 
                  }	// end of if($error)
             } // end of if($mode)

        }elseif($encounter_nr!='') {
			  /* Load encounter data */
			  $encounter_obj->loadEncounterData();
			  if($encounter_obj->is_loaded) {
		          $zeile=&$encounter_obj->encounter;

					//load data
				  extract($zeile);

               // Get insurance firm name
			      $insurance_firm_name=$pinsure_obj->getFirmName($insurance_firm_id);

			  /* GEt the patient's services classes */
			  
			  if(!empty($GLOBAL_CONFIG['patient_financial_class_single_result'])) $encounter_obj->setSingleResult(true);

				if(!$GLOBAL_CONFIG['patient_service_care_hide']){
                	if($buff=&$encounter_obj->CareServiceClass()){
					    while($care_class=$buff->FetchRow()){
							extract($care_class);
						}   
						reset($care_class);
					}    			  
				}
				if(!$GLOBAL_CONFIG['patient_service_room_hide']){
                	if($buff=&$encounter_obj->RoomServiceClass()){
					    while($room_class=$buff->FetchRow()){
							extract($room_class);
						}   
						reset($room_class);
					}    			  
				}
				if(!$GLOBAL_CONFIG['patient_service_att_dr_hide']){
                	if($buff=&$encounter_obj->AttDrServiceClass()){
					    while($att_dr_class=$buff->FetchRow()){
							extract($att_dr_class);
						}   
						reset($att_dr_class);
					}    			  
				}
        	} 	

		}

	 if (($encounter_class_nr==2)&&($encounter_type==2)){
	 	# Load all  doctors in OPD 
		$doctor_dept=$pers_obj->getDoctors(0);
		$all_meds=&$dept_obj->getAllOPDMedicalObject(0);
	 }else{
	 	# Load all  doctors in IPD 
		$doctor_dept=$pers_obj->getDoctors(1);
		$all_meds=&$dept_obj->getAllOPDMedicalObject(1);
	 }	

	 #----IPD-------------	
	 
    #if(!$encounter_nr||$encounter_class_nr==1){	#-----comment 03-07-07------
	 if(!$encounter_nr||$update==1){
		# Load all  wards info 
		$ward_obj=new Ward;
		$items='nr,name';
		$ward_info=&$ward_obj->getAllWardsItemsObject($items);
	}
	
	#----OPD-----------------
       
	$person_obj->setPID($pid);
	if($data=&$person_obj->BasicDataArray($pid)){
		extract($data);  
	}     

	# Prepare the photo filename
	include_once($root_path.'include/inc_photo_filename_resolve.php');
	/* Get the citytown name */
	$addr_citytown_name=$person_obj->CityTownName($addr_citytown_nr);
	
}
# Prepare text and resolve the numbers
include_once($root_path.'include/inc_patient_encounter_type.php');

# Prepare the title
if($encounter_nr) $headframe_title = "$headframe_title $headframe_append ";

# Prepare onLoad JS code
if(!$encounter_nr && !$pid) $sOnLoadJs ='onLoad="if(document.searchform.searchkey.focus) document.searchform.searchkey.focus();"';


# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in the toolbar
 $smarty->assign('sToolbarTitle',$headframe_title);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('admission_how2new.php')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$headframe_title);

 # Onload Javascript code
 $smarty->assign('sOnLoadJs',$sOnLoadJs);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('person_admit.php')");

 # Hide the return button
 $smarty->assign('pbBack',FALSE);

 # Start collectiong extra Javascript code
 ob_start();

require_once("doctor-dept.common.php");

if ($xajax) {
		$xajax->printJavascript('../../classes/xajax');
		
}

# If  pid exists, output the form checker javascript
if(isset($pid) && $pid){

?>

<script  language="javascript">
<!--

		/*	
				This will trim the string i.e. no whitespaces in the
				beginning and end of a string AND only a single
				whitespace appears in between tokens/words 
				input: object
				output: object (string) value is trimmed
		*/
	function trimString(objct){
		objct.value = objct.value.replace(/^\s+|\s+$/g,"");
		objct.value = objct.value.replace(/\s+/g,""); 
	}/* end of function trimString */

//------------added by van 03-16-07----------

function valButton(btn) {
	var cnt = -1;
	var temp = document.getElementsByName(btn);
	if (!$(btn))	{
		return null;
	}

	for (var i=temp.length-1; i > -1; i--) {
		if (temp[i].checked) {
			cnt = i; 
			i = -1;
		}
    }
	 
	if (cnt > -1) return temp[cnt].value;
		else return null;
}


function chkform(d) {
	var encr=<?php if ($encounter_class_nr) {echo $encounter_class_nr; } else {echo '0';} ?>;
	var update = <?php echo $update; ?>;
	var dept_belong = "<?php echo $dept_belong['id']; ?>";
	var direct_admission = <?php if ($seg_direct_admission) echo $seg_direct_admission; else echo '0'; ?>;
		
	if(dept_belong == "Admission"){

	var btncond = valButton("cond_code");
	var btnres = valButton("result_code");
	var btndisp = valButton("disp_code");

	var is_admit_obj = document.getElementsByName('is_admit');

		if($('is_admit')){
			if((update==1) && !d.is_admit.checked){
				alert("<?php echo $LDPlsCheckAdmit; ?>");
				d.is_admit.focus();
				return false;
			}
		}

		if((d.consulting_dr_nr.value==0)&&(!direct_admission)){
			alert("Please select an Admitting Physician.");
			d.consulting_dr_nr.focus();
			return false;

		}else if(d.current_dept_nr.value==0){		
			alert("Please select the Attending Department.");
			d.current_dept_nr.focus();
			return false;
			consulting_dr_nr
		}else if((d.er_opd_diagnosis.value=="")&&(!direct_admission)){
			alert("Please enter Admitting Diagnosis.");
			d.er_opd_diagnosis.focus();
			return false;
		}else if(d.encoder.value==""){
			alert("<?php echo $LDPlsEnterFullName; ?>");
			d.encoder.focus();
			return false;
<?php
	if (!$seg_direct_admission){
?>
		//}else if((encr!=2)&&(!d.insurance_class_nr[2].checked) && (d.insurance_nr.value==" ")){
		}else if((!d.insurance_class_nr[2].checked) && (d.insurance_nr.value==" ")){
			alert("<?php echo $LDPlsEnterInsuranceNo; ?>");
			d.insurance_nr.focus();
			return false;
		//}else if((encr!=2)&&(!d.insurance_class_nr[2].checked) && (insurance_firm_name.value==" ")){
		}else if((!d.insurance_class_nr[2].checked) && (insurance_firm_name.value==" ")){
			alert("<?php echo $LDPlsEnterInsuranceCo; ?>");
			d.insurance_firm_name.focus();
			return false;	
		}else if((encr!=2)&& (btncond == null)){
			alert("<?php echo $LDPlsSelectCond; ?>");
			d.cond_code[0].focus();
			return false;
		}else if((encr!=2)&&(btnres == null)){
			alert("<?php echo $LDPlsSelectRes; ?>");
			d.result_code[0].focus();
			return false;
		}else if((encr!=2)&&(btndisp == null)){	
			alert("<?php echo $LDPlsSelectDisp; ?>");
			d.disp_code[0].focus();
			return false;
<?php
	}
?>
		}else{
			return true;
		} 
	}else{
			// ONLY consulting department is required for consultation
		if((encr==2)&&(d.official_receipt_nr.value=="")){
			alert("Please enter official receipt number.");
			d.official_receipt_nr.focus();
			return false;
		}else if(d.current_dept_nr.value==0){		
			alert("<?php echo $LDPlsSelectDept; ?>");
			d.current_dept_nr.focus();
			return false;
		}else{
			return true;
		}	
	}	
}
function resolveLoc(){
	d=document.aufnahmeform;
	
	if(d.encounter_class_nr[1].checked==true){  // for OPD
		d.current_ward_nr.selectedIndex=0;
		document.aufnahmeform.current_ward_nr.disabled = true;
		document.getElementById("doctor_label").innerHTML = "Consultant Doctor";
		xajax_setDoctors(0,0);       // get all OPD doctors
		xajax_setALLDepartment(0);	  // get all OPD Department	

	}else {  // for IPD, ER-Admission and OPD-Admission
		
		if (d.encounter_class_nr[0].checked==true){
			d.current_ward_nr.selectedIndex=0;
			document.aufnahmeform.current_ward_nr.disabled = true;
		}else{
			document.aufnahmeform.current_ward_nr.disabled = false;
		}
		d.current_dept_nr.selectedIndex=0;
		document.getElementById("doctor_label").innerHTML = "Attending Doctor";
		xajax_setDoctors(1,0);		// get all IPD doctors
		xajax_setALLDepartment(1);	// get all OPD Department
		
	}
}

function ReferralHandler1(){
	var d = document.aufnahmeform;
	d.referrer_dr.value = " ";
}


function GetDr_nr(){
	var d = document.aufnahmeform;
	var dr_split;
	var i;
	var dr_name = d.referrer_dr_name.value;
	dr_name = dr_name.toLowerCase();
	
	if (dr_name.match("dr.")!=null){ 	
		dr_name_split = dr_name.substr(4);
	}else{
		dr_name_split = dr_name;
	}
	
	dr_split = dr_name_split.split(" ");
	
	if (dr_split.length==2){
		d.name1.value = dr_split[0];
		d.lname.value = dr_split[1];
	}else if(dr_split.length==3){
		d.name1.value = dr_split[0];
		d.name2.value = dr_split[1];
		d.lname.value = dr_split[2];
	}
}

function preset(){
	
	var d = document.aufnahmeform;
	var encounter_class_nr;
	var update = <?php echo $update; ?>;
	var encounter_type;
	var dept_belong = "<?php echo $dept_belong['id']; ?>";
	var dept_nr = "<?php echo $current_dept_nr; ?>";
	var er_dept_nr = "<?php echo $consulting_dept_nr; ?>";  // burn added : May 16, 2007
	var er_dr_nr = "<?php echo $consulting_dr_nr; ?>";  // burn added : May 16, 2007
		
	encounter_class_nr = <?php echo $encounter_class_nr; ?>;
	encounter_type = "<?php echo $encounter_type; ?>";
	
	var doc = "<?php echo $current_att_dr_nr; ?>";
	var dr_name = "<?php echo $patient_enc['consulting_dr']; ?>";
	var dr_dept = "<?php echo $patient_doc['name_formal']; ?>";
	var cond_code = "<?php echo $patient_enc_cond['cond_code']; ?>";
	var disp_code = "<?php echo $patient_enc_disp['disp_code']; ?>";
	var res_code = "<?php echo $patient_enc_res['result_code']; ?>";
	
<?php
	if ($seg_direct_admission){
?>
		xajax_setAllDepartmentEROPD(0,er_dept_nr);		
		xajax_setDoctorsEROPD(0, er_dept_nr, er_dr_nr);
		xajax_setALLDepartment(1);	// get all IPD Department
		xajax_setDoctors(1,dept_nr);
		xajax_setDepartments(doc,dept_nr);
<?php
	}else{
?>

		if ((encounter_type==1) || (encounter_type==2))
			jsShowConditionResultDisposition(false);   // burn added : May 16, 2007
		if ((encounter_class_nr == 2)||((encounter_class_nr == 2)&&(encounter_type == 2))){
		if (dept_belong!="Admission"){
				if (dept_nr){
					xajax_setDoctors(0,dept_nr);		// get all OPD doctors
					xajax_setDoctorsEROPD(0,er_dept_nr,er_dr_nr);		// get all OPD doctors; burn added: May 16, 2007
				}else{
					xajax_setDoctors(0,0);		// get all OPD doctors
				}
				
				xajax_setALLDepartment(0);	// get all OPD Department
				
				xajax_setDepartments(doc,dept_nr);
			
			}else{
				if (dept_nr){
					xajax_setDoctors(1,dept_nr);		// get all IPD doctors
					xajax_setDoctorsEROPD(0,er_dept_nr,er_dr_nr);		// get all ER/OPD doctors; burn added: May 16, 2007
				}		
				xajax_setALLDepartment(1);	// get all IPD Department
				xajax_setDepartments(doc,dept_nr);
				
			}	
		}else{ // if(encounter_class_nr == 1){
			if (dept_nr){
				xajax_setDoctors(1,dept_nr);		// get all IPD doctors
				xajax_setDoctorsEROPD(1,er_dept_nr,er_dr_nr);		// get all IPD doctors; burn added: May 16, 2007
			}else{
				xajax_setDoctors(1,0);		// get all IPD doctors
			}	
			xajax_setALLDepartment(1);	// get all IPD Department
			xajax_setDepartments(doc,dept_nr);
			
		}	
		
		// for the insurance attribute
		//edited by van 083007
		//if ((update == 1)&&(dept_belong=="Admission")&&(encounter_class_nr!=2)){
		if ((update == 1)&&(dept_belong=="Admission")){
			
			if (d.insurance_class_nr[2].checked == true){
				d.insurance_nr.readOnly = true;
				d.insurance_nr.value=" ";
				d.insurance_firm_name.value=" ";
				document.getElementById('iconIns').style.display = 'none';
			}else if((d.insurance_class_nr[0].checked == true)||(d.insurance_class_nr[1].checked == true)){
				//alert("diri sulod");
				d.insurance_nr.readOnly = false;
				document.getElementById('iconIns').style.display = '';
			}else{
				
				d.insurance_class_nr[2].checked = true;
				d.insurance_nr.readOnly = true;
				d.insurance_nr.value=" ";
				d.insurance_firm_name.value=" ";
				document.getElementById('iconIns').style.display = 'none';
			}
			
			if (dept_belong=="Admission"){			
				if(cond_code)
					d.cond_code[cond_code-1].checked = true; 
				if(disp_code)
					d.disp_code[disp_code-1].checked = true; 
				if(res_code)
					d.result_code[res_code-1].checked = true; 
				
				d.consulting_dr.value = dr_name;
				
			}// end of if-stmt "if (dept_belong=="Admission")"				
		}//end of if-stmt "if ((update == 1)&&(dept_belong=="Admission")&&(encounter_class_nr!=2))"
<?php
	} // end of else
?>
}

//---- AJAX -----------------------
	/*
		burn added : May 16, 2007	
	*/
function ajxClearOptionEROPDDrDept(DrDept) {
	var optionsList;
	var el;

	if (DrDept==0)
		el=document.aufnahmeform.consulting_dr_nr;
	else
		el=document.aufnahmeform.consulting_dept_nr;
	 
	if (el) {
		optionsList = el.getElementsByTagName('OPTION');
		for (var i=optionsList.length-1;i>=0;i--) {
			optionsList[i].parentNode.removeChild(optionsList[i]);
		}
	}
}/* end of function ajxClearOptionEROPDDrDept */

function ajxSetEROPDDrDept(DrDept,DrDept_nr) {
	if (DrDept==0)
		document.aufnahmeform.consulting_dr_nr.value = DrDept_nr;		
	else
		document.aufnahmeform.consulting_dept_nr.value = DrDept_nr;
}/* end of function ajxSetEROPDDrDept */

	/*
		burn added : May 16, 2007	
	*/
function ajxAddOptionEROPDDrDept(DrDept, text, value) {
	var grpEl;

	if (DrDept==0)
		grpEl=document.aufnahmeform.consulting_dr_nr;
	else
		grpEl=document.aufnahmeform.consulting_dept_nr;

	if (grpEl) {
		var opt = new Option( text, value );
		opt.id = value;
		grpEl.appendChild(opt);
	}	
}/* end of function ajxAddOptionEROPDDrDept */

	/*
		burn added : May 24, 2007	
	*/
function jsGetEROPDDepartment(){
		var d = document.aufnahmeform;
		var aPersonell_nr=d.consulting_dr_nr.value;
		
		if (aPersonell_nr != 0) {
			xajax_setDepartmentEROPD(aPersonell_nr);
		} else{
			d.consulting_dept_nr.value = 0;
		}	
}
	/*
		burn added : May 24, 2007	
	*/
function jsGetEROPDDoctors(){
		var d = document.aufnahmeform;
		var aDepartment_nr=d.consulting_dept_nr.value;
		
		if (aDepartment_nr != 0) {
			xajax_setDoctorsEROPD(0,aDepartment_nr);	//get the list of ER-OPD doctors under "aDepartment_nr" department
		} else{
			xajax_setDoctorsEROPD(0);	//get the list of ER-OPD doctors
		}	
}

function ajxClearOptions(status) {
var optionsList;
var el;

	if (status==0){
		el=document.aufnahmeform.current_att_dr_nr;
	}else{
		el=document.aufnahmeform.current_dept_nr;
	}
	 
	if (el) {
		optionsList = el.getElementsByTagName('OPTION');
		for (var i=optionsList.length-1;i>=0;i--) {
			optionsList[i].parentNode.removeChild(optionsList[i]);
		}
	}
}/* end of function ajxClearOptions */

function ajxAddOption(status, text, value) {
var grpEl;

	if (status==0){
		grpEl=document.aufnahmeform.current_att_dr_nr;
	}else{
		grpEl=document.aufnahmeform.current_dept_nr;
	}
	
	if (grpEl) {
		var opt = new Option( text, value );
		opt.id = value;
		grpEl.appendChild(opt);
	}
	var optionsList = grpEl.getElementsByTagName('OPTION');
	
}/* end of function ajxAddOption */


function ajxSetDepartment(dept_nr) {
	document.aufnahmeform.current_dept_nr.value = dept_nr;
	
}

function ajxSetDoctor(personell_nr) {
	document.aufnahmeform.current_att_dr_nr.value = personell_nr;
	
}

function jsGetDepartment(){
		var d = document.aufnahmeform;
		var aDoctor=d.current_att_dr_nr;
		var aPersonell_nr;
		var optionsList;
		
		d.consulting_dr.value = d.current_att_dr_nr.options[d.current_att_dr_nr.selectedIndex].text;
		aPersonell_nr = aDoctor.value;

		if (aPersonell_nr != 0) {
			xajax_setDepartments(aPersonell_nr,0);		
			optionsList = aDoctor.getElementsByTagName('OPTION');
		} else{
			d.current_dept_nr.value = 0;
		}	
		
		if (d.current_att_dr_nr.options[d.current_att_dr_nr.selectedIndex].text != "-Select a Doctor-"){
			d.consulting_dr.value = d.current_att_dr_nr.options[d.current_att_dr_nr.selectedIndex].text;
		}else{
			d.consulting_dr.value = " ";
		}			
}

function jsGetDoctors(){
	var d = document.aufnahmeform;
	var aDepartment=d.current_dept_nr;
	var aDepartment_nr;
	var optionsList;
	var encounter_class_nr = <?php echo $encounter_class_nr; ?>;
	var update = <?php echo $update; ?>;
	var encounter_type = "<?php echo $encounter_type; ?>";
	var dept_belong = "<?php echo $dept_belong['id']; ?>";
	var aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;
		
		
	d.consulting_dr.value = " ";
		
	if (update != 1){	
		
		if (encounter_class_nr == 1){
			if (aDepartment_nr != 0){
				xajax_setDoctors(1,aDepartment_nr);	
			}else{
				xajax_setDoctors(1,0);			// get all IPD doctors
			}
		}else{ 
			if (aDepartment_nr != 0){
				xajax_setDoctors(0,aDepartment_nr);	
			}else{
				xajax_setDoctors(0,0);			// get all OPD doctors
			}
		}
	}else{	
		
		if ((encounter_type==2)&&(encounter_class_nr==2)&&(dept_belong!="Admission")){
			if (aDepartment_nr != 0){
				xajax_setDoctors(0,aDepartment_nr);	
			}else{
				xajax_setDoctors(0,0);			// get all IPD doctors
			}
		}else{
		   if (aDepartment_nr != 0){
				xajax_setDoctors(1,aDepartment_nr);	
			}else{
				xajax_setDoctors(1,0);			// get all IPD doctors
			}
		}		
	}
}

//-----------EDITED BY VAN
function DisableInsurance(){
	var d = document.aufnahmeform;
	
	if(d.insurance_class_nr[2].checked==true){
		d.insurance_nr.value=" ";
		d.insurance_firm_name.value=" ";
		d.insurance_nr.readOnly = 1;
		document.getElementById('iconIns').style.display = 'none';
		
	}else{
		d.insurance_nr.value=" ";
		d.insurance_firm_name.value=" ";
		d.insurance_nr.readOnly = 0;
		document.getElementById('iconIns').style.display = '';
				
	}
	
	d.insurance_firm_id.value = " ";
}

	/*
	*	burn added: April 20, 2007
	*	NOTE : Assuming that Admit option is the FIRST option in 'disp_code' radio button
	*/
	function checkAdmit(obj){
		if (obj.checked){
			if (document.aufnahmeform.disp_code)
				document.aufnahmeform.disp_code[0].checked=true;
			jsShowConditionResultDisposition(true);
		} else {
			if (document.aufnahmeform.disp_code)
				document.aufnahmeform.disp_code[0].checked=false;
			jsShowConditionResultDisposition(false);
		}
	}/* end of function checkAdmit */

	function getElementsByClass(searchClass,node,tag) {
		var classElements = new Array();
		if ( node == null )
			node = document;
		if ( tag == null )
			tag = '*';
		var els = node.getElementsByTagName(tag);
		var elsLen = els.length;
		var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
		for (i = 0, j = 0; i < elsLen; i++) {
			if ( pattern.test(els[i].className) ) {
				classElements[j] = els[i];
				j++;
			}
		}
		return classElements;
	}/* end of function getElementsByClass */

		function jsShowConditionResultDisposition(showDetails){
			var personDetails = getElementsByClass("ERDetails");
			for (var i=0; i< personDetails.length; i++){
				personDetails[i].style.display = (showDetails) ? '' : 'none';
			}
			
		}/* end of function jsShowConditionResultDisposition */

<?php require($root_path.'include/inc_checkdate_lang.php'); ?>

-->
</script>
<script language="javascript" src="<?php echo $root_path; ?>js/setdatetime.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/checkdate.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/dtpick_care2x.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/jsprototype/prototype.js"></script>

<body onLoad="preset();">

<?php

} // End of if(isset(pid))

require('./include/js_popsearchwindow.inc.php');

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Load tabs
$target='entry';

$parent_admit = TRUE;

include('./gui_bridge/default/gui_tabs_patadmit.php');

# If the origin is admission link, show the search prompt
if(!isset($pid) || !$pid){

	# Set color values for the search mask
	$searchmask_bgcolor="#f3f3f3";
	$searchprompt=$LDEntryPrompt;
	$entry_block_bgcolor='#fff3f3';
	$entry_body_bgcolor='#ffffff';
	
	$smarty->assign('entry_border_bgcolor','#6666ee');

	$smarty->assign('sSearchPromptImg','<img '.createComIcon($root_path,'angle_down_l.gif','0','',TRUE).'>');

	$smarty->assign('LDPlsSelectPatientFirst',$LDPlsSelectPatientFirst);
	$smarty->assign('sMascotImg','<img '.createMascot($root_path,'mascot1_l.gif','0','absmiddle').'>');

	# Start buffering the searchmask

	ob_start();

	$search_script='patient_register_search.php';
	$user_origin='admit';
	include($root_path.'include/inc_patient_searchmask.php');
	
	$sTemp = ob_get_contents();
	
	ob_end_clean();

	$smarty->assign('sSearchMask',$sTemp);
	$smarty->assign('sWarnIcon','<img '.createComIcon($root_path,'warn.gif','0','absmiddle',TRUE).'>');
	$smarty->assign('LDRedirectToRegistry',$LDRedirectToRegistry);

}else{

	$smarty->assign('bSetAsForm',TRUE);

	if($error){
		$smarty->assign('error',TRUE);
		$smarty->assign('sMascotImg','<img '.createMascot($root_path,'mascot1_r.gif','0','bottom').' align="absmiddle">');

		 if ($errornum>1) $smarty->assign('LDError',$LDErrorS);
		 	else 	$smarty->assign('LDError',$LDError);
	}

	$smarty->assign('LDCaseNr',$LDCaseNr);
	if(isset($encounter_nr)&&$encounter_nr) 	$smarty->assign('encounter_nr',$encounter_nr);
		else  $smarty->assign('encounter_nr','<font color="red">'.$LDNotYetAdmitted.'</font>');

	$smarty->assign('img_source',"<img $img_source>");

		# burn added: May 15, 2007
	if (($patient_enc['encounter_type']=='3')||($patient_enc['encounter_type']=='4')){
		$smarty->assign('LDAdmitDate',$LDAdmitDate);
		$smarty->assign('LDAdmitTime',$LDAdmitTime);
	}else{
		$smarty->assign('LDAdmitDate',$LDConsultDate);
		$smarty->assign('LDAdmitTime',$LDConsultTime);
	}

		   # burn added: March 29, 2007
	 if(isset($encounter_nr)&&$encounter_nr){
		if (($patient_enc['encounter_type']==1) || ($patient_enc['encounter_type']==2)){
				# ER/OPD
			$segAdmitDateTime = $patient_enc['encounter_date'];
		}else{
				# Inpatient
			$segAdmitDateTime = $patient_enc['admission_dt'];
		}
		$smarty->assign('sAdmitDate',@formatDate2Local($segAdmitDateTime,$date_format));		
		$smarty->assign('sAdmitTime',@formatDate2Local($segAdmitDateTime,$date_format,1,1));
	}

	$smarty->assign('LDTitle',$LDTitle);
	$smarty->assign('title',$title);
	$smarty->assign('LDLastName',$LDLastName);
	$smarty->assign('name_last',$name_last);
	$smarty->assign('LDFirstName',$LDFirstName);
	$smarty->assign('name_first',$name_first);
	
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
		$segAddress=$street_name.', '.$brgy_name.', '.$mun_name.' '.$zipcode.' '.$prov_name;   # burn added: March 12, 2007
		$smarty->assign('segAddress',$segAddress);   # burn added: March 12, 2007
		
		$smarty->assign('LDAdmitClass',$LDAdmitClass);
		if ($HTTP_SESSION_VARS['sess_user_name'] == "Administrator"){	
			if(is_object($encounter_classes)){
				
				$sTemp = '';
				while($result=$encounter_classes->FetchRow()) {
					$LD=$result['LD_var'];
					
					if($encounter_nr ){ # If admitted, freeze encounter class
						if ($encounter_class_nr==$result['class_nr']){
							if(isset($$LD)&&!empty($$LD)) $sTemp = $sTemp.$$LD; 
								else $sTemp = $sTemp.$result['name'];
							$sTemp = $sTemp.'<input name="encounter_class_nr" type="hidden"  value="'.$encounter_class_nr.'">';
							break;
						}
					}else{
						
						$sTemp = $sTemp.'<input name="encounter_class_nr" onClick="resolveLoc()" type="radio"  value="'.$result['class_nr'].'" ';
						if($encounter_class_nr==$result['class_nr']) $sTemp = $sTemp.'checked';
						$sTemp = $sTemp.'>';
						
						if(isset($$LD)&&!empty($$LD)) $sTemp = $sTemp.$$LD;
							else $sTemp = $sTemp.$result['name'];
					} #end of IF-else
				} #end of While loop
			} # end of if(is_object($encounter_classes))
			
			$smarty->assign('sAdmitClassInput',$sTemp);
		}else{
			if (($patient_enc['encounter_type']!=3)&&($patient_enc['encounter_type']!=4)){
				if($dept_belong['id'] == "OPD-Triage"){
					$admission_class = $LDAmbulant;
					$encounter_type = 2;
				}elseif($dept_belong['id'] == "ER"){
					$admission_class = $LDStationary;
					$encounter_type = 1;
				}elseif($dept_belong['id'] == "Admission"){
					$sAdmit ="<input type=\"checkbox\" name=\"is_admit\" id=\"is_admit\" onChange=\"checkAdmit(this);\" value=\"1\">";
					if ($encounter_class_nr == 2){ 
						$admission_class = $LDAmbulant;
						$encounter_class_nr = 2;
						$encounter_type = 4;
					}else{
						$admission_class = $LDStationary;
						$encounter_class_nr = 1;
						$encounter_type = 3;
					}	
				}	
			}elseif(($patient_enc['encounter_type']==3)||($patient_enc['encounter_type']==4)){
				if ($patient_enc['encounter_type']==3)
					$admission_class = $LDStationary2;
				elseif($patient_enc['encounter_type']==4)	
					$admission_class = $LDAmbulant2;
			}

			$sTemp = '';
			if($dept_belong['id'] == "Admission"){
				if ($patient_enc['encounter_type']==1){
					$sTemp = $sTemp.'<label id="admission_class"><font color="red"><b>'.strtoupper($admission_class).'</b></font></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$sAdmit."&nbsp;&nbsp;Pls. check if you want to Admit the patient.";
				}elseif($patient_enc['encounter_type']==2){
					$sTemp = $sTemp.'<label id="admission_class"><font color="blue"><b>'.strtoupper($admission_class).'</b></font></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$sAdmit."&nbsp;&nbsp;Pls. check if you want to Admit the patient.";
				}else{
					$sTemp = $sTemp.'<label id="admission_class"><font color="green"><b>'.strtoupper($admission_class).'</b></font></label>';	
				}
			}else{
				if($patient_enc['encounter_type']!=NULL){
					if ($patient_enc['encounter_type']==1){
						$sTemp = $sTemp.'<label id="admission_class"><font color="red"><b>'.strtoupper($admission_class).'</b></font></label>';
					}elseif($patient_enc['encounter_type']==2){
						$sTemp = $sTemp.'<label id="admission_class"><font color="blue"><b>'.strtoupper($admission_class).'</b></font></label>';
					}
				}else{
					if ($encounter_class_nr==1){
						$sTemp = $sTemp.'<label id="admission_class"><font color="red"><b>'.strtoupper($admission_class).'</b></font></label>';
					}elseif($encounter_class_nr==2){
						$sTemp = $sTemp.'<label id="admission_class"><font color="blue"><b>'.strtoupper($admission_class).'</b></font></label>';
					}
				}	
			}

			$sTemp2 = $sTemp2.'<input name="encounter_class_nr" id="encounter_class_nr" type="hidden" size="5" value="'.$encounter_class_nr.'">';
			$sTemp3 = $sTemp3.'<input name="encounter_type" id="encounter_type" type="hidden" size="5" value="'.$encounter_type.'">';

			if ($seg_direct_admission){
				if ($update)
					$sTemp = '<label id="admission_class"><font color="green"><b>'.strtoupper($LDInpatientDirectAdmission).'</b></font></label>';
				else
					$sTemp = '<label id="admission_class"><font color="red"><b>'.strtoupper($LDDirectAdmission).'</b></font></label>';
			}
										
			$smarty->assign('sAdmitClassInput',$sTemp);
			$smarty->assign('sAdmitClassInput2',$sTemp2);
			$smarty->assign('sAdmitClassInput3',$sTemp3);
		}	#end of if ($HTTP_SESSION_VARS['sess_user_name'] == "Administrator")
			
			#-------------------add 03-07-07--------------------
			if(($dept_belong['id']=="Admission")||($dept_belong['id']=="ER")||($dept_belong['id']!="OPD-Triage")){
				$smarty->assign('LDInformant',$LDInformant);
				$smarty->assign('informant_name','<input name="informant_name" id="informant_name" type="text" size="60" value="'.ucwords(strtolower(trim($informant_name))).'">');
			
				$smarty->assign('LDInfoAdd',$LDInfoAdd);
				$smarty->assign('info_address','<input name="info_address" id="info_address" type="text" size="60" value="'.ucwords(strtolower(trim($info_address))).'">');
			
				$smarty->assign('LDInfoRelation',$LDInfoRelation);
				$smarty->assign('relation_informant','<input name="relation_informant" id="relation_informant" type="text" size="60" value="'.ucwords(strtolower(trim($relation_informant))).'">');
			}
			#------------add 02-27-07-------------------
			
			# If no encounter nr or inpatient, show ward/station info, 1 = inpatient
			
			if(!$encounter_nr||$update==1 && $dept_belong['id']=="Admission"){
			#if(!$encounter_nr||$encounter_class_nr==1){
				
				if ($errorward||$update==1) $smarty->assign('LDWard',"<font color=red>$LDWard</font>");
				#if ($errorward||$encounter_class_nr==1) $smarty->assign('LDWard',"<font color=red>$LDWard</font>");	
					
			if ($dept_belong['id']=="Admission"){
					$smarty->assign('LDWard',$LDWard);
				$sTemp = '';
				
				if($in_ward){

					while($station=$ward_info->FetchRow()){
						if(isset($current_ward_nr)&&($current_ward_nr==$station['nr'])){
							$sTemp = $sTemp.$station['name'];
							$sTemp = $sTemp.'<input name="current_ward_nr" type="hidden"  value="'.$current_ward_nr.'">';
							break;
						}
					}
				}else{
					$sTemp = $sTemp.'<select name="current_ward_nr" id="current_ward_nr">
								<option value=""></option>';
					if(!empty($ward_info)&&$ward_info->RecordCount()){
						while($station=$ward_info->FetchRow()){
							$sTemp = $sTemp.'
								<option value="'.$station['nr'].'" ';
							if(isset($current_ward_nr)&&($current_ward_nr==$station['nr'])) $sTemp = $sTemp.'selected';
							$sTemp = $sTemp.'>'.$station['name'].'</option>';
						}
					}
					$sTemp = $sTemp.'</select>
							<font size=1><img '.createComIcon($root_path,'redpfeil_l.gif','0','',TRUE).'> '.$LDForInpatient.'</font>';
				}
				$smarty->assign('sWardInput',$sTemp);
			  } # end for WARD field		
			} //  End of if no encounter nr
			
			
			# Official receipt number, available/required ONLY when generating OPD encounter
			if ($encounter_type == 2) {
				$smarty->assign('segORNumber',"OR Number");				
				$smarty->assign('sORNumber','<input name="official_receipt_nr" id="official_receipt_nr" type="text" onBlur="trimString(this);" size="30" value="'.ucwords(strtolower(trim($official_receipt_nr))).'">');				
			}elseif ($encounter_type == 4){
				$smarty->assign('segORNumber',"OR Number");				
				$smarty->assign('sORNumber','<input name="official_receipt_nr" id="official_receipt_nr" type="text" size="30" value="'.ucwords(strtolower(trim($official_receipt_nr))).'" readonly>');
			}
			
			#Doctor's name
			
			if (($encounter_type == 3)||($encounter_type == 4)){
				$sTemp = '';
				$sTemp = $sTemp.'<label id="doctor_label">'.$LDDoctor3.'</label>';
				$smarty->assign('LDDoctor',$sTemp);
			}else{
				$sTemp = '';
				$sTemp = $sTemp.'<label id="doctor_label">'.$LDDoctor1.'</label>';
				$smarty->assign('LDDoctor',$sTemp);
			}	
			
			$sTemp ='';
			$sTemp = $sTemp.'<select name="current_att_dr_nr" id="current_att_dr_nr" onChange="jsGetDepartment();">
								<option value="0">-Select a Doctor-</option>';
			$sTemp = $sTemp.'</select>';
					
			$smarty->assign('doctor_name',$sTemp);
			$smarty->assign('doctor_name2','<input name="consulting_dr" id="consulting_dr" type="hidden" size="60" >');
			#-------------------------------------------
			
			# If no encounter nr or outpatient, show clinic/department info, 2 = outpatient

				   # burn added: May 16, 2007
				if ($dept_belong['dept_nr']==149){   # burn added : May 16, 2007
						# the user is from ER-Triage
					$smarty->assign('LDDepartment',"Consulting $LDDepartment");
				}
				if ($dept_belong['dept_nr']==150){   # burn added : May 16, 2007
						# the user is from OPD-Triage
					$smarty->assign('LDDepartment',"Consulting $LDClinic");
				}

				$smarty->assign('segERDetailsHideable',"");
				if ($dept_belong['dept_nr']==148){   # burn added : May 16, 2007
						# the user is from Admitting section
					$smarty->assign('LDDepartment',"Attending $LDDepartment");
					$smarty->assign('segERDetailsHideable'," class='ERDetails'");
								
					$sTemp_ERDr_option='';
					if ($seg_direct_admission){
						$sTemp_ERDr_option = ' onChange="jsGetEROPDDepartment();" ';
					}
					$sTemp_ERDr ='';
					$sTemp_ERDr = $sTemp_ERDr.'<select name="consulting_dr_nr" id="consulting_dr_nr"'.$sTemp_ERDr_option.'>';
					$sTemp_ERDr = $sTemp_ERDr."</select> \n";

					$smarty->assign('sERDrInput',$sTemp_ERDr);
					$sTemp_ERDr ='';

					$smarty->assign('segEROPDDr',"<font color='red'>Admitting Physician</font>");
					if ($patient_enc['encounter_class_nr']==1){
						$smarty->assign('segEROPDDepartment',"<font color='red'>Admitting $LDDepartment</font>");
					}elseif ($patient_enc['encounter_class_nr']==2){
						$smarty->assign('segEROPDDepartment',"<font color='red'>Admitting $LDClinic</font>");
					}

					if ($seg_direct_admission){
						$smarty->assign('segEROPDDr',"Admitting Physician");
						$smarty->assign('segEROPDDepartment',"Admitting $LDDepartment");
						$sTemp = '';
						$sTemp = $sTemp.'
							<select name="consulting_dept_nr" id="consulting_dept_nr" onChange="jsGetEROPDDoctors();">
							</select>';
						$smarty->assign('sERDeptInput',$sTemp);
					}else{
						$smarty->assign('sERDeptInput','<input name="consulting_dept_nr" id="consulting_dept_nr" type="hidden" value="'.$patient_enc['consulting_dept_nr'].'">'.$patient_consulting_doc['name_formal']);
					}
				}# end of if-stmt "if ($dept_belong['dept_nr']==148)"

				$sTemp = '';
				
				if($in_dept){
					while($deptrow=$all_meds->FetchRow()){
						if(isset($current_dept_nr)&&($current_dept_nr==$deptrow['nr'])){
							$sTemp = $sTemp.$deptrow['name_formal'];
							$sTemp = $sTemp.'<input name="current_dept_nr" type="hidden"  value="'.$current_dept_nr.'">';
							break;
						}
					}
				}else{
					
					$sTemp = $sTemp.'<select name="current_dept_nr" onChange="jsGetDoctors();">
							<option value="0">-Select a Department-</option>';
					$sTemp = $sTemp.'</select>';
				}
				$smarty->assign('sDeptInput',$sTemp);
			#} // End of if no encounter nr #---------comment 03-06-07----------

		#--------added condition 03-14-07 by vanessa ---------
		if ($dept_belong['id']!="OPD-Triage"){  	
			$readonly=" readonly ";   # burn added : May 16, 2007
			if ($dept_belong['dept_nr']==149){   # burn added : May 16, 2007
					# the user is from ER-Triage
				$readonly="";				
			}
			if ($dept_belong['dept_nr']==148){   # burn added : May 16, 2007
					# the user is from Admitting section
				$smarty->assign('segERDiagnosis',"<font color='red'>Admitting ".$LDDiagnosis."</font>");
					# burn added : June 1, 2007
				if ($seg_direct_admission) # not required if direct admission
					$smarty->assign('segERDiagnosis',"Admitting ".$LDDiagnosis);
				$smarty->assign('er_opd_diagnosis','<textarea name="er_opd_diagnosis" id="er_opd_diagnosis" cols="55" rows="3" wrap="VIRTUAL">'.trim($er_opd_diagnosis).'</textarea>');   # burn added: May 16, 2007
			}# end of if-stmt "if ($dept_belong['dept_nr']==148)"

				# burn added: May 16, 2007
			if (($encounter_class_nr==1)&&(!$seg_direct_admission)){
				$smarty->assign('segShowIfFromER',"true");
			}else{
				$smarty->assign('segShowIfFromER',"");
			}

			if ($encounter_class_nr!=2){
				$smarty->assign('LDDiagnosis',"Referred ".$LDDiagnosis);
				$smarty->assign('referrer_diagnosis','<textarea name="referrer_diagnosis" id="referrer_diagnosis" cols="55" rows="3" wrap="VIRTUAL"'.$readonly.'>'.ucwords(strtolower(trim($referrer_diagnosis))).'</textarea>');   # burn added: April 20, 2007
				$smarty->assign('LDRecIns',$LDRecIns);
				$smarty->assign('referrer_institution','<input name="referrer_institution" id="referrer_institution" type="text" size="73" value="'.ucwords(strtolower(trim($referrer_institution))).'"'.$readonly.'>');
				$smarty->assign('LDSpecials',$LDSpecials);
				$smarty->assign('referrer_notes','<textarea name="referrer_notes" id="referrer_notes" cols="55" rows="2" wrap="VIRTUAL"'.$readonly.'>'.ucwords(strtolower(trim($referrer_notes))).'</textarea>');   # burn added: May 16, 2007
				$smarty->assign('LDRecBy',$LDRecBy);
			}

			if ((($patient_enc['encounter_type']==1)||($patient_enc['encounter_type']==2))&&($update==1)&&($dept_belong['id']=="Admission")){
				$smarty->assign('referrer_dr_name','<input name="referrer_dr_name" id="referrer_dr_name" onKeyUp="ReferralHandler1()" type="text" size="73" value="'.ucwords(strtolower(trim($referrer_dr))).'"'.$readonly.'>');
				$smarty->assign('referrer_dr','<input name="referrer_dr" id="referrer_dr" type="hidden" value="'.$patient_enc['referrer_dr'].'">');

			}else{

				$dr_name = $pers_obj->get_Person_name($patient_enc['referrer_dr']);
				$dr_fname = ucwords(strtolower(trim($dr_name['name_first'])));
				$dr_fname2 = ucwords(strtolower(trim($dr_name['name_2'])));
				$dr_lname = ucwords(strtolower(trim($dr_name['name_last'])));
				$ref_dr_name= "Dr. ".$dr_fname." ".$dr_fname2."".$dr_lname;
				
				if (is_numeric($referrer_dr)){

					$smarty->assign('referrer_dr_name','<input name="referrer_dr_name" id="referrer_dr_name" type="text" onKeyUp="ReferralHandler1();"  onBlur="GetDr_nr();" size="73" value="'.trim($ref_dr_name).'"'.$readonly.'>');
					$smarty->assign('referrer_dr','<input name="referrer_dr" id="referrer_dr" type="hidden" value="'.$patient_enc['referrer_dr'].'">');
					$smarty->assign('referrer_dept','<input name="referrer_dept" id="referrer_dept" type="hidden" size="5" value="'.$patient_enc['referrer_dept'].'">');
				}else{

					$smarty->assign('referrer_dr_name','<input name="referrer_dr_name" id="referrer_dr_name" type="text" onKeyUp="ReferralHandler1();" onBlur="GetDr_nr();" size="73" value="'.ucwords(strtolower(trim($referrer_dr))).'"'.$readonly.'>');
					$smarty->assign('referrer_dr','<input name="referrer_dr" id="referrer_dr" type="hidden" value="'.ucwords(strtolower(trim($referrer_dr))).'">');
					$smarty->assign('referrer_dept','<input name="referrer_dept" id="referrer_dept" type="hidden" size="5" value="">');
				}	
				$smarty->assign('name1','<input name="name1" id="name1" type="hidden" size="10" value="">');
				$smarty->assign('name2','<input name="name2" id="name2" type="hidden" size="10"  value="">');
				$smarty->assign('lname','<input name="lname" id="lname" type="hidden" size="10"  value="">');
			}
			
			if($dept_belong['id']=="Admission"){
				if ($errorinsclass) $smarty->assign('LDBillType',"<font color=red>$LDBillType</font>");
					else  $smarty->assign('LDBillType',$LDBillType);
	
				$sTemp = '';
				if(is_object($insurance_classes)){
					while($result=$insurance_classes->FetchRow()) {
	
						$sTemp = $sTemp.'<input name="insurance_class_nr" id="insurance_class_nr" type="radio" onChange="DisableInsurance();"  value="'.$result['class_nr'].'" ';
						if($insurance_class_nr==$result['class_nr']) $sTemp = $sTemp.'checked';
						$sTemp = $sTemp.'>';
	
						$LD=$result['LD_var'];
						if(isset($$LD)&&!empty($$LD)) $sTemp = $sTemp.$$LD;
							else $sTemp = $sTemp.$result['name'];
					}
				}
				$smarty->assign('sBillTypeInput',$sTemp);
				$sTemp = '';
				if ($error_ins_nr) $smarty->assign('LDInsuranceNr',"<font color=red>$LDInsuranceNr</font>");
					else  $smarty->assign('LDInsuranceNr',$LDInsuranceNr);
				 if(isset($insurance_nr)&&$insurance_nr) $sTemp = $insurance_nr;
				
				$smarty->assign('insurance_nr','<input name="insurance_nr" id="insurance_nr" type="text" size="60" readonly="1" value="'.$sTemp.'">');
				
				$sTemp = '';
				if(isset($insurance_firm_name)) $sTemp = $insurance_firm_name;
				if ($error_ins_co) $smarty->assign('LDInsuranceCo',"<font color=red>$LDInsuranceCo</font>");
				else $smarty->assign('LDInsuranceCo',$LDInsuranceCo);
				
				#edit by VAN 083007
				#$sBuffer ="<a href=\"javascript:popSearchWin('insurance','aufnahmeform.insurance_firm_id','aufnahmeform.insurance_firm_name2')\" onBlur=\"FillInsuranceCo();\"><img ".createComIcon($root_path,'l-arrowgrnlrg.gif','0','',TRUE)."></a>";
				$sBuffer ="<a href=\"javascript:popSearchWin('insurance','aufnahmeform.insurance_firm_id','aufnahmeform.insurance_firm_name')\" ><img ".createComIcon($root_path,'l-arrowgrnlrg.gif','0','',TRUE)."></a>";
			
				#$smarty->assign('insurance_firm_name2','<input name="insurance_firm_name2" id="insurance_firm_name2" disabled type="text" size="60" value="'.$sTemp.'">'.$sBuffer." Click Here.");
				#edit by VAN 083007
				#$smarty->assign('insurance_firm_name2','<input name="insurance_firm_name2" id="insurance_firm_name2" disabled type="text" size="73" value="'.$sTemp.'"><span name="iconIns" id="iconIns" style="display:none">'.$sBuffer." Click Here.</span>");
				
				$smarty->assign('insurance_firm_name','<input name="insurance_firm_name" id="insurance_firm_name" readonly="1" type="text" size="60" value="'.$sTemp.'"><span name="iconIns" id="iconIns" style="display:none">'.$sBuffer." Click Here.</span>");
						
				#---------added 03-06-07-------------
				#comment by VAN 083007
				#$smarty->assign('insurance_firm_name','<input name="insurance_firm_name" id="insurance_firm_name" type="hidden" size="73" value="'.$sTemp.'">');

			} # end if Admission

		} # end if not OPD-Triage
	  
			if (!$GLOBAL_CONFIG['patient_service_care_hide']&& is_object($care_service)){
				$smarty->assign('LDCareServiceClass',$LDCareServiceClass);
				$sTemp = '';

				$sTemp = $sTemp.'<select name="sc_care_class_nr" >';

				while($buffer=$care_service->FetchRow()){
					$sTemp = $sTemp.'
						<option value="'.$buffer['class_nr'].'" ';
					if($sc_care_class_nr==$buffer['class_nr']) $sTemp = $sTemp.'selected';
					$sTemp = $sTemp.'>';
					if(empty($$buffer['LD_var'])) $sTemp = $sTemp.$buffer['name'];
						else $sTemp = $sTemp.$$buffer['LD_var'];
					$sTemp = $sTemp.'</option>';
				}
				$sTemp = $sTemp.'</select>';

				$smarty->assign('sCareServiceInput',$sTemp);

				$smarty->assign('LDFrom',$LDFrom);
				$sTemp = '';
				 if(!empty($sc_care_start)) $sTemp = @formatDate2Local($sc_care_start,$date_format);

				$smarty->assign('sCSFromInput','<input type="text" name="sc_care_start"  value="'.$sTemp.'" size=9 maxlength=10   onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">');
				$smarty->assign('LDTo',$LDTo);
				$sTemp = '';
				 if(!empty($sc_care_end)) $sTemp = @formatDate2Local($sc_care_end,$date_format);
				$smarty->assign('sCSToInput','<input type="text" name="sc_care_end"  value="'.$sTemp.'"  size=9 maxlength=10   onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">');
				$smarty->assign('sCSHidden','<input type="hidden" name="sc_care_nr" value="'.$sc_care_nr.'">');

			}

			if (!$GLOBAL_CONFIG['patient_service_room_hide']&& is_object($room_service)){
				$smarty->assign('LDRoomServiceClass',$LDRoomServiceClass);
				$sTemp = '';

				$sTemp = $sTemp.'<select name="sc_room_class_nr" >';

				while($buffer=$room_service->FetchRow()){
					$sTemp = $sTemp.'
						<option value="'.$buffer['class_nr'].'" ';
					if($sc_room_class_nr==$buffer['class_nr']) $sTemp = $sTemp.'selected';
					$sTemp = $sTemp.'>';
					if(empty($$buffer['LD_var'])) $sTemp = $sTemp.$buffer['name'];
						else $sTemp = $sTemp.$$buffer['LD_var'];
					$sTemp = $sTemp.'</option>';
				}
				$sTemp = $sTemp.'</select>';

				$smarty->assign('sCareRoomInput',$sTemp);

				//$smarty->assign('LDFrom',$LDFrom);
				$sTemp = '';
				 if(!empty($sc_room_start)) $sTemp = @formatDate2Local($sc_room_start,$date_format);

				$smarty->assign('sRSFromInput','<input type="text" name="sc_room_start"  value="'.$sTemp.'" size=9 maxlength=10   onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">');
				//$smarty->assign('LDTo',$LDTo);
				$sTemp = '';
				 if(!empty($sc_room_end)) $sTemp = @formatDate2Local($sc_room_end,$date_format);
				$smarty->assign('sRSToInput','<input type="text" name="sc_room_end"  value="'.$sTemp.'"  size=9 maxlength=10   onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">');
				$smarty->assign('sRSHidden','<input type="hidden" name="sc_room_nr" value="'.$sc_room_nr.'">');

			}

			if (!$GLOBAL_CONFIG['patient_service_att_dr_hide']&& is_object($att_dr_service)){
				$smarty->assign('LDAttDrServiceClass',$LDAttDrServiceClass);
				$sTemp = '';

				$sTemp = $sTemp.'<select name="sc_att_dr_class_nr" >';

				while($buffer=$att_dr_service->FetchRow()){
					$sTemp = $sTemp.'
						<option value="'.$buffer['class_nr'].'" ';
					if($sc_att_dr_class_nr==$buffer['class_nr']) $sTemp = $sTemp.'selected';
					$sTemp = $sTemp.'>';
					if(empty($$buffer['LD_var'])) $sTemp = $sTemp.$buffer['name'];
						else $sTemp = $sTemp.$$buffer['LD_var'];
					$sTemp = $sTemp.'</option>';
				}
				$sTemp = $sTemp.'</select>';

				$smarty->assign('sCareDrInput',$sTemp);

				//$smarty->assign('LDFrom',$LDFrom);
				$sTemp = '';
				 if(!empty($sc_att_dr_start)) $sTemp = @formatDate2Local($sc_att_dr_start,$date_format);

				$smarty->assign('sDSFromInput','<input type="text" name="sc_att_dr_start"  value="'.$sTemp.'" size=9 maxlength=10   onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">');
				//$smarty->assign('LDTo',$LDTo);
				$sTemp = '';
				 if(!empty($sc_att_dr_end)) $sTemp = @formatDate2Local($sc_att_dr_end,$date_format);
				$smarty->assign('sDSToInput','<input type="text" name="sc_att_dr_end"  value="'.$sTemp.'"  size=9 maxlength=10   onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">');
				$smarty->assign('sDSHidden','<input type="hidden" name="sc_att_dr_nr" value="'.$sc_att_dr_nr.'">');

			}

			$smarty->assign('LDAdmitBy',$LDAdmitBy);
			if (empty($encoder)) $encoder = $HTTP_COOKIE_VARS[$local_user.$sid];
			$smarty->assign('encoder','<input  name="encoder" type="text" value="'.$encoder.'" size="28" readonly>');
			
			$smarty->assign('LDDeptBelong',$LDDepartment);
			$smarty->assign('sDeptBelong','<input  name="sDeptBelong" type="text" value="'.$dept_belong['name_formal'].'" size="28" readonly>');
			
			#----------------added 03-08-07-------------------
			if($dept_belong['id']=="Admission"){
		 		
				#condition
				$smarty->assign('LDCondition',$LDCondition);
				
				if(is_object($condition_classes)){
					$sTemp = '';
					while($result=$condition_classes->FetchRow()) {
						$sTemp = $sTemp.'<input name="cond_code" id="cond_code" type="radio"  value="'.$result['cond_code'].'" ';
							if($cond_code==$result['cond_code']) $sTemp = $sTemp.'checked';
						$sTemp = $sTemp.'>';
						$sTemp = $sTemp.$result['cond_desc']."<br>";
						
					}
				}		
				$smarty->assign('sCondition',$sTemp);
				
				#results	
				$smarty->assign('LDResults',$LDResults." from ER");
				
				if(is_object($results_classes)){
					$sTemp = '';
					while($result=$results_classes->FetchRow()) {
						$sTemp = $sTemp.'<input name="result_code" id="result_code" type="radio" value="'.$result['result_code'].'" ';
							if($result_code==$result['result_code']) $sTemp = $sTemp.'checked';
						$sTemp = $sTemp.'>';
						$sTemp = $sTemp.$result['result_desc']."<br>";
						
					}
				}		
				$smarty->assign('sResults',$sTemp);
				
				
				#disposition
				$smarty->assign('LDDisposition',$LDDisposition." from ER");
				if(is_object($disposition_classes)){
					$sTemp = '';
					while($result=$disposition_classes->FetchRow()) {
						$sTemp = $sTemp.'<input name="disp_code" id="disp_code" type="radio" value="'.$result['disp_code'].'" ';
							if($disp_code==$result['disp_code']) $sTemp = $sTemp.'checked';
						$sTemp = $sTemp.'>';
						$sTemp = $sTemp.$result['disp_desc']."<br>";
						
					}
				}		
				$smarty->assign('sDisposition',$sTemp);
			}
			#------------------------------------------------
			
			$sTemp = '<input type="hidden" name="pid" value="'.$pid.'">
				<input type="hidden" name="encounter_nr" value="'.$encounter_nr.'">
				<input type="hidden" name="appt_nr" value="'.$appt_nr.'">
				<input type="hidden" name="sid" value="'.$sid.'">
				<input type="hidden" name="lang" value="'.$lang.'">
				<input type="hidden" name="mode" value="save">
				<input type="hidden" name="insurance_firm_id" value="'.$insurance_firm_id.'">
				<input type="hidden" name="insurance_show" value="'.$insurance_show.'">';
				

			if($update) $sTemp = $sTemp."\n<input type='hidden' name=update value=1>";

				# burn added : May 24, 2007
			if ($seg_direct_admission){
				# if direct admission, set encounter_status AS direct admission
				$sTemp = $sTemp."\n				<input name='encounter_status' id='encounter_status' type='hidden' value='direct_admission'>";
			}		

				# burn added: March 29, 2007
			if (($patient_enc['encounter_type']==1) ||($patient_enc['encounter_type']==2)){
				$sTemp = $sTemp."\n				<input type='hidden' name='firstUpdate' value=1>";
			}else{
				$sTemp = $sTemp."\n				<input type='hidden' name='firstUpdate' value=0>";
			}

			$smarty->assign('sHiddenInputs',$sTemp);

			$smarty->assign('pbSave','<input  type="image" '.createLDImgSrc($root_path,'savedisc.gif','0').' title="'.$LDSaveData.'" align="absmiddle">');

			$smarty->assign('pbRegData','<a href="patient_register_show.php'.URL_APPEND.'&pid='.$pid.'"><img '.createLDImgSrc($root_path,'reg_data.gif','0').'  title="'.$LDRegistration.'"  align="absmiddle"></a>');
			
#			$smarty->assign('pbCancel','<a href="aufnahme_daten_zeigen.php'.URL_REDIRECT_APPEND.'&encounter_nr='.$encounter_nr.'&origin=admit&sem=isadmitted&target=entry"><img '.createLDImgSrc($root_path,'cancel.gif','0').'  title="'.$LDCancel.'"  align="absmiddle"></a>');   # burn commented: March 12, 2007

				# burn added: March 12, 2007
			if($origin=='patreg_reg') {
				$smarty->assign('pbCancel','<a href="'.$breakfile.'"><img '.createLDImgSrc($root_path,'cancel.gif','0').'  title="'.$LDCancel.'"  align="absmiddle"></a>');
			}else{
				$smarty->assign('pbCancel','<a href="aufnahme_daten_zeigen.php'.URL_REDIRECT_APPEND.'&encounter_nr='.$encounter_nr.'&origin=admit&sem=isadmitted&target=entry"><img '.createLDImgSrc($root_path,'cancel.gif','0').'  title="'.$LDCancel.'"  align="absmiddle"></a>');
			}
			//<!-- Note: uncomment the ff: line if you want to have a reset button  -->
			/*<!--
			$smarty->assign('pbRefresh','<a href="javascript:document.aufnahmeform.reset()"><img '.createLDImgSrc($root_path,'reset.gif','0').' alt="'.$LDResetData.'"  align="absmiddle"></a>');
			-->
			*/
			
			if($error==1)
				$smarty->assign('sErrorHidInputs','<input type="hidden" name="forcesave" value="1">
				<input  type="submit" value="'.$LDForceSave.'">');

	if (!($newdata)) {

		$sTemp = '
		<form name="admission" action='.$thisfile.' method=post>
		<input type="hidden" name=sid value='.$sid.'>
		<input type="hidden" name=patnum value="">
		<input type="hidden" name="lang" value="'.$lang.'">
		<input type=submit value="'.$LDNewForm.'">
		</form>';
		
		$smarty->assign('sNewDataForm',$sTemp);
	}

}  // end of if !isset($pid...

# Prepare shortcut links to other functions

$smarty->assign('sSearchLink','<img '.createComIcon($root_path,'varrow.gif','0').'> <a href="aufnahme_daten_such.php'.URL_APPEND.'">'.$LDPatientSearch.'</a>');
$smarty->assign('sArchiveLink','<img '.createComIcon($root_path,'varrow.gif','0').'> <a href="aufnahme_list.php'.URL_APPEND.'&newdata=1&from=entry">'.$LDArchive.'</a>');

$smarty->assign('sMainBlockIncludeFile','registration_admission/admit_input.tpl');

$smarty->display('common/mainframe.tpl');
?>
</body>