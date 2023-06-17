<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');  
include_once($root_path.'include/care_api_classes/class_personell.php');     
require_once($root_path."modules/or/ajax/or_schedule_viewer.common.php");         


function get_or_today($date_chosen)
{
	global $db;
	$objResponse = new xajaxResponse(); 
	$no_error = true;      
	$pers_obj=new Personell;             
	$db->StartTrans();

	#get OR schedule for the day
	$sql ="SELECT seg_or_main.time_operation, seg_or_main.dr_nr, seg_or_main.or_procedure, ". 
			"care_person.name_first, care_person.name_last, care_person.name_middle, care_person.sex, care_person.date_birth ".
			"FROM seg_or_main ".
			"JOIN care_encounter ON seg_or_main.encounter_nr = care_encounter.encounter_nr ".
			"JOIN care_person ON care_person.pid = care_encounter.pid ".
			"WHERE seg_or_main.date_operation=".$db->qstr($date_chosen).//" AND seg_or_main.dept_nr=".$db->qstr($dept_nr).
			" ORDER by seg_or_main.time_operation";   
			$details = '';                                   														 
	if($result = $db->Execute($sql)){                            
		$num_rows = $result->RecordCount();
			if($num_rows>=1){
				$bgColor= $num_rows;
				$details = '<table cellpadding="3" cellspacing="0" align="LEFT" valign="MIDDLE" style="width:100%">';
				while($row = $result->FetchRow()) {   
					$patient_name = ucfirst(strtolower($row['name_last'])).", ".ucfirst(strtolower($row['name_first']))." ".ucfirst(strtolower($row['name_middle']));
					//compute age
					list($y,$m,$d) = explode("-",$row['date_birth']);
					$age = date('Y')-$y;
					date('md')<$m.$d ? $age--:null;          
					//set time format     					
					$dbtime=explode(" ",$row['time_operation']);           
					$operation_date = reformat_date($dbtime[0]);
					$operation_date .= " - ".reformat_date($dbtime[2]);    
					
					$person=&$pers_obj->getPersonellInfo($row['dr_nr']);
					$doctors_name = ucfirst(strtolower($person['name_last'])).', '.ucfirst(strtolower($person['name_first']));                          
					
					if($bgColor%2==0)  $details .= '<tbody class="wardlistrow1"><tr><td></td><td></td><td></td><td></td></tr>';
					else   						 $details .= '<tbody class="wardlistrow2"><tr><td></td><td></td><td></td><td></td></tr>';   
					$details .= '<tr><td>  </td><td><b>Patient name</td><td>:</b></td><td>'.$patient_name.'  ('.ucfirst($row['sex']).', '.$age.')</td>'.
										 //'<tr><td>  </td><td></td><td></td><td>('.ucfirst($row['sex']).', '.$age.')</td></tr>'.
										 '<tr><td>  </td><td><b>Time</td><td>:</b></td><td>'.$operation_date.'</td></tr>'.
										 '<tr><td>  </td><td><b>Doctor</td><td>:</b></td><td>'.$doctors_name.'</td></tr>'.
										 '<tr><td>  </td><td><b>Procedure</td><td>:</b></td><td>'.$row['or_procedure'].'</td></tr>'.  
										 '<tr><td class="vspace" colspan="4"><img height="1" width="5" src="../../gui/img/common/default/pixel.gif"></td></tr>'.
										 '</tbody>';       
					$bgColor--;					
				}                                        
				$details.='</table>';
				$objResponse->assign('body', 'innerHTML', $details);
				$db->CompleteTrans();
			}	     
			else{
				//call function to display "no operation scheduled today"
				$message = '<table style="width:100%"><tbody class="wardlistrow1"><tr style="width:100% height:100%">'.
									 '<td valign="MIDDLE" align="CENTER" height="218"><b>NO SCHEDULED OPERATION</b></td></tr></tbody></table>';
				$objResponse->assign('body','innerHTML',$message);       
			}
	}     	
																		
	else{
		echo "<br>ERROR2 @ view calendar schedule:".$sql."<br>".$db->ErrorMsg()."<br>";
		$no_error=false;
		$db->FailTrans();
	}     
	if($no_error)
	{
		//$objResponse->alert("SQL VIEW SUCCESS!");        
		//$objResponse->call("window.parent.location.reload()");
	}
	else
	{
		$objResponse->alert("ERROR!");
	}
	return $objResponse;     	  
}


function reformat_date($d){
	 $temp = explode(":",$d);
	 if($temp[0]>12){                            
			$new_dbtime = ($temp[0]-12).":".$temp[1];
			if($temp[0]==24)	$new_dbtime .= " am";
			else							$new_dbtime .= " pm";   
		}
		else if($temp[0]==0){
			$new_dbtime = "12:".$temp[1]." am";
		}
		else if ($temp[0]==12){
			$new_dbtime = $d." pm";   
		}
		else{
			$new_dbtime = $d." am";
		}
		return $new_dbtime;
}


 
 $xajax->processRequest();