<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/or_logbook/ajax/op_common.php');

require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

require_once($root_path.'include/inc_date_format_functions.php');
//Instantiate object Department & Personell
$dept_obj = new Department;
$pers_obj = new Personell;

function delLogMainInfo($encounter,$dept, $op_nr){
	global $db;
	$objResponse = new xajaxResponse();
	
#	$objResponse->addAlert("encounter_nr=".$encounter." dept = ".$dept." op_nr=".$op_nr);
	$sql = "DELETE FROM care_encounter_op WHERE encounter_nr='$encounter' AND dept_nr = '$dept' AND op_nr='$op_nr'";
	if($d=$db->Execute($sql)){  //$d=$db->Execute($sql)
		$objResponse->addScriptCall("refreshLogInput", true);
	}else{
		$objResponse->addScriptCall("refreshLogInput", false);	
	}
#	$objResponse->addAlert("xajax->sql=".$sql);
	return $objResponse;
}

function setALLDepartment(){
	global $dept_obj;
	$objResponse = new xajaxResponse();
	
	$objResponse->addAlert("setAllDepartment");
	
	$ORNrs=&$dept_obj->getAllActiveORNrs();
	$surgery_arr=&$dept_obj->getAllActiveWithSurgery();
	
	$buffer = $dept_obj->LDvar();
	if(isset($$buffer) && !empty($$buffer)) $nbuf = $$buffer;
	else
	if($surgery_arr){
		while(list($x, $v)= each($surgery_arr)){
			if($x==42) continue;
			if($dept_nr == $v['nr'])     
			$buffer=$v['LD_var'];
			if(isset($$buffer) && !empty($$buffer)) $nbuf = $$buffer;
			else $nbuf = $v['name_formal'];
			
			#$objResponse->addAlert("name_formal".$nbuf." buffer=".$buffer);
			$objResponse->addScriptCall("ajxAddOption",0,$nbuf,$v['nr']);
		}
	}
	
	return $objResponse;
}

function setDepartment($dept_nr){
	global $dept_obj;
	$objResponse = new xajaxResponse();
	$dept = $dept_obj->getDeptAllInfo($dept_nr);
	if($dept){
		$objResponse->addScriptCall("ajxSetDepartment", $dept['nr']);
		$objResponse->addAlert("dept= ".$dept['name_formal']." dept_nr= ".$dept['nr']);
	}else{
		$objResponse->addAlert("No department exists");	
	}
	return $objResponse;
}

/*
 * Set doctors
 */
function setDoctors($dept_nr){
	global $pers_obj;
	$objResponse = new xajaxResponse();
	
	$rs = $pers_obj->getDoctorByDept($dept_nr,'');
	
	//$objResponse->addAlert(print_r($rs, true));
	$objResponse->addScriptCall("ajxClearOptions_s");
	if($rs){
//		if($pers_obj->count == 1){
//			$objResponse->addScriptCall("ajxSetDoctor_s", $pers_obj['personell_nr']);
//		}elseif($pers_obj->count> 1){
//			$objResponse->addScriptCall("ajxAddOption_s", "-Select a Doctor-", 0);
//		}
		$objResponse->addScriptCall("ajxAddOption_s","-Select a Doctor-", 0);
		while($result=$rs->FetchRow()){
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
//			$objResponse->addScriptCall("ajxAddOption_c",0,$doctor_name,$result['personell_nr']);
			$objResponse->addScriptCall("ajxAddOption_s", $doctor_name, $result["personell_nr"]);
		}
		
	}else{
		$objResponse->addAlert("setDoctors: Error retrieving Doctords information");
	}
	
	return $objResponse;
}	


function saveAppointment($pid,$date, $time,$dept_nr, $personell_nr,$purpose ,$enc_nr, $encoder){
	global $db;
	$objResponse = new xajaxResponse();
	
	$time = $time.":00";
	$sDate = date('Y-m-d', strtotime($date));
	//$create_id = $HTTP_SESSION_VARS['sess_user_name'];
	$create_id = $encoder;				
	$create_time = date('Y-m-d H:i:s');
	$history = 'Created: '.date('Y-m-d H:i:s')." ".$create_id."\n";
		
	#$objResponse->addAlert("pid=".$pid." date=".$date." time=".$time);
	
	$sql="INSERT INTO care_appointment (pid,date,time, to_dept_nr, to_personell_nr, purpose,
		  encounter_nr, history, create_id, create_time) ".
		"\n VALUES('$pid','$sDate','$time','$dept_nr','$personell_nr','$purpose','$enc_nr',
			 '$history','$create_id','$create_time')";
	
//	$objResponse->addAlert("sql=".$sql);	
	if($b = $db->Execute($sql)){
		$objResponse->addAlert("Schedule was succesfully saved");
	}else{
		$objResponse->addAlert("Failed while saving schedule data");
	}
		
	return $objResponse;
}


function getPersonell_info($personell_nr){
	
	$ojbResponse = new xajaxResponse;

	return $objResponse;
}


function populateOpLogMain($dept_nr='', $saal){ //, $thisday
	global $db, $date_format; 
	$objResponse = new xajaxResponse();
	
	$dbtable='care_encounter_op';
	//AND o.op_date='$thisday'		
	$sql="SELECT o.*,e.encounter_class_nr, p.name_last, p.name_first, p.date_birth, p.addr_str, p.addr_str_nr, p.addr_zip, t.name AS citytown_name
			FROM $dbtable AS o, care_encounter AS e, care_person AS p
				LEFT JOIN care_address_citytown AS t ON p.addr_citytown_nr=t.nr
			WHERE o.dept_nr='$dept_nr'
						AND o.op_room='$saal'
						
						AND o.encounter_nr=e.encounter_nr
						AND e.pid=p.pid
						ORDER BY o.nr
						";
	
	if($ergebnis=$db->Execute($sql)){
		if($rows=$ergebnis->RecordCount()){
			$datafound=1;
		}
	}else{
		//echo "$LDDbNoRead<br>$sql";
		$objResponse->addAlert("DB no read: ".$sql);
	} 
	
	if($datafound){
		while($pdata=$ergebnis->FetchRow()){
			
			
			$name = $pdata['name_last'].", ".$pdata['name_first'];
			
			$sDate = @formatDate2Local($pdata['op_date'],$date_format);
			$bDate = @formatDate2Local($pdata['date_birth'],$date_format);
			
			list($pyear,$pmonth,$pday)=explode('-',$pdata['op_date']);
			$wkDays = $tage[date(w,mktime(0,0,0,$pmonth,$pday,$pyear))];
			$diagnosis = nl2br($pdata['diagnosis']);
			$addr = $pdata['addr_str'].", ".$pdata['addr_str_nr'];
			$town =  $pdata['addr_zip'].", ".$pdata['citytown_name'];
			
			$diagnosis = nl2br($pdata['diagnosis']);
					
			$objResponse->addScriptCall("gui_oplogmainRow", $pdata['op_nr'],$sDate, $wkDays, $pdata['encounter_nr'], $name, 
										$bDate, addr, $town, $diagnosis);
		}
		
	}
	
	#$objResponse->addAlert("xajax->hello world");

				
	return $objResponse;
} //end of function populateOpLogMain


$xajax->processRequests();
?>