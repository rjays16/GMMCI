<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/*
CARE 2X Integrated Information System Deployment 2.1 - 2004-10-02 for Hospitals and Health Care Organizations and Services
Copyright (C) 2002,2003,2004,2005  Elpidio Latorilla & Intellin.org	

GNU GPL. For details read file "copy_notice.txt".
*/

if(!extension_loaded('gd')) dl('php_gd.dll');
$lang_tables[]='aufnahme.php';
define('LANG_FILE','konsil.php');
define('NO_CHAIN',1);
require_once($root_path.'include/inc_front_chain_lang.php');
header ('Content-type: image/png');


		include_once($root_path.'include/care_api_classes/class_ward.php');
		include_once($root_path.'include/care_api_classes/class_radiology.php');#added by daryl
		$obj=new Ward;
		$rdo_obj=new SegRadio;
		#edited by VAN 03-04-08
		#edited by VAN 07-02-08
		if ($en){
			if($obj->loadEncounterData($en)){
				$result=&$obj->encounter;
			}
			
		}else{
			#edited bydaryl
			$ifwalk_= $rdo_obj->selectifwalkin($pid);
			$ifwalk = $ifwalk_->FetchRow();
			if ($ifwalk['ifwalk'] == 1){
				$obj->loadEncounterDataByPidWalkin($pid);
				$result=&$obj->encounter;
			
			}else{
			if($obj->loadEncounterDataByPid($pid)){
				#echo "here";
				$result=&$obj->encounter;
			}
			}
		}	
		$trial = $obj->sql;
	
		# Create insurance object
		include_once($root_path.'include/care_api_classes/class_insurance.php');
		$ins_obj=new Insurance;
		
		#edited by VAN 07-10-08
		if ($en)
			$fen=$en;
		else
			$fen=$pid;	
	    /*// get orig data
	    $dbtable="care_admission_patient";
	    $sql="SELECT * FROM $dbtable WHERE patnum='$pn' ";
	    if($ergebnis=$db->Execute($sql))
       	{
			if($rows=$ergebnis->RecordCount())
				{
					$result=$ergebnis->FetchRow();
					if($edit&&$result['discharge_date']) $edit=0;
				}
		}
		else {print "<p>$sql$LDDbNoRead"; exit;}*/
       
	   include_once($root_path.'include/inc_date_format_functions.php');
	  
	  # Get location data
	  #edited by VAN 07-10-08
	  if ($en){
		$location=&$obj->EncounterLocationsInfo($en);
		$result['pdate']=@formatDate2Local($result['encounter_date'],$date_format);
	  }else{
	  	$result['pdate']=@formatDate2Local($result['date_reg'],$date_format);
		$locstr2 = "WALKIN";
	  }		
		 
	   # Localize date data   
	   $result['date_birth']=@formatDate2Local($result['date_birth'],$date_format);
	   #commented by VAN 07-10-08
	   #
	   #$result['pdate']=@formatDate2Local($result['encounter_date'],$date_format);
		# Decode admission class
	 #edited by VAN 07-10-08
	 if ($en){
		switch($result['encounter_class_nr']){
			case 1: $admit_type=$LDStationary; break;
			case 2: $admit_type=$LDAmbulant; break;
			default : $admit_type='';
		}
	}	
		
	   if($child_img)
	   {
	   
	       if($subtarget=='chemlabor' || $subtarget=='baclabor')
	       {
	           $sql="SELECT * FROM care_test_request_".$subtarget." WHERE batch_nr='".$batch_nr."'";
	   		            if($ergebnis=$db->Execute($sql))
       		            {
				            if($editable_rows=$ergebnis->RecordCount())
					        {
							
     					       $stored_request=$ergebnis->FetchRow();
							   
							   
							    if(isset($stored_request['parameters']))
							   {
							      //echo $stored_request['parameters'];
   						          parse_str($stored_request['parameters'],$stored_param);
                               }
							   
							   // parse the material type 
							   if(isset($stored_request['material']))
							   {
   						          parse_str($stored_request['material'],$stored_material);
							   }
							   // parse the test type 
							   if(isset($stored_request['test_type']))
							   {
   						          parse_str($stored_request['test_type'],$stored_test_type);
							   }
							}
			             }
	       }	   

	       if($subtarget=='baclabor')
	       {
	           $sql="SELECT * FROM care_test_findings_baclabor WHERE batch_nr='".$batch_nr."'";
	   		            if($ergebnis=$db->Execute($sql))
       		            {
				            if($editable_rows=$ergebnis->RecordCount())
					        {
							
     					       $stored_findings=$ergebnis->FetchRow();
							   
							       parse_str($stored_findings['type_general'],$parsed_type);
							       parse_str($stored_findings['resist_anaerob'],$parsed_resist_anaerob);
							       parse_str($stored_findings['resist_aerob'],$parsed_resist_aerob);
							       parse_str($stored_findings['findings'],$parsed_findings);
							}
			             }
	   
	       }
	    } // end of if($child_img)

		
    $addr=explode("\r\n",$result['address']);

    if($lang=="de") $result['sex']=strtr($result['sex'],"mfMF","mwMW");
    if($lang=="tr") $result['sex']=strtr($result['sex'],"mfMF","ekEK");
 
	# Load the image generation script based on the language
	if($lang=='ar'||$lang=='fa') include($root_path.'main/imgcreator/inc_label_single_large_ar.php'); 
	if($lang=='tr') include($root_path.'main/imgcreator/inc_label_single_large_tr.php');
		else include($root_path.'main/imgcreator/inc_label_single_large.php');
			
		
/*
}
*/
 ?>
