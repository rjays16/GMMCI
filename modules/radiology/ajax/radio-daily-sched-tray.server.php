<?php                                                                
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');      
require_once($root_path.'include/care_api_classes/class_radiology.php');  
require_once($root_path.'include/care_api_classes/class_paginator.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');   
require_once($root_path.'modules/radiology/ajax/radio-daily-sched-tray.common.php');    
require_once($root_path.'include/care_api_classes/class_personell.php');    
 

function get_radiology_today($date, $department)
{
	global $db;
	$objResponse = new xajaxResponse();          
	$no_error = true;      
	$pers_obj=new Personell;         			 
	$db->StartTrans();
	#get department number
	$sql = "SELECT nr FROM care_department WHERE name_formal =".$db->qstr($department);
	$result = $db->Execute($sql);            
	$row = mysql_fetch_array($result);
	$dept_nr = $row['nr'];  
	#get radiology schedule for the day
	$sql ="SELECT seg_or_main.time_operation, seg_or_main.dr_nr, seg_or_main.or_procedure, ". 
			"care_person.name_first, care_person.name_last, care_person.name_middle, care_person.sex, care_person.date_birth ".
			"FROM seg_or_main ".
			"JOIN care_encounter ON seg_or_main.encounter_nr = care_encounter.encounter_nr ".
			"JOIN care_person ON care_person.pid = care_encounter.pid ".
			"WHERE seg_or_main.date_operation=".$db->qstr($date)." AND seg_or_main.dept_nr=".$db->qstr($dept_nr).
			" ORDER by seg_or_main.time_operation";                                     
	if($result = $db->Execute($sql))
	{ 
		$num_rows = $result->RecordCount();
		if($num_rows>=1)
		{
			#display 
			for($i=0;$i<$num_rows;$i++)
			{
					echo $age=getAge($row['date_birth']);
					alert($patient_info);
					$patient_info = ucfirst($row['name_last']).", ".ucfirst($row['name_first']).
					" ".ucfirst($row['name_middle'])."\t\t(".ucfirst($row['sex']).", ".$age.")"; 
					echo $time = $row['time_operation'];
					echo $person=&$pers_obj->getPersonellInfo($row['dr_nr']);   
					echo $doctors_name = ucfirst($person['name_last']).', '.ucfirst($person['name_first']);
					echo $procedures = $row['or_procedure'];      				
			}
			$db->CompleteTrans();
		}
	}
	else{
		echo "<br>ERROR2 @ display radiology today:".$sql."<br>".$db->ErrorMsg()."<br>";
		$no_error=false;
		$db->FailTrans();
	}     
	if($no_error)
	{
		$objResponse->alert("Save successful!");
		$objResponse->call("window.parent.location.reload()");
	}
	else
	{
		$objResponse->alert("ERROR!");
	}
	return $objResponse;     	
}

 function getAge($mysql_date) {
	list($y,$m,$d) = explode("-",$mysql_date);
	$age = date('Y')-$y;
	date('md')<$m.$d ? $age--:null;
	return $age;
}

?>
