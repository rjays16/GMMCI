<?php
/**
 * add icd code to the list
 */
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/medocs/ajax/medocs_common.php");
require($root_path.'include/care_api_classes/class_medocs.php');
require($root_path.'include/care_api_classes/class_icd10.php');
require($root_path.'include/care_api_classes/class_icpm.php');
require($root_path.'include/care_api_classes/class_drg.php');
//require($root_path.'include/care_api_classes/class_notes');
include_once($root_path.'include/care_api_classes/class_encounter.php');   # burn added : April 28, 2007
require_once($root_path.'include/care_api_classes/class_ward.php');
/* Create the helper class for the personell table */
include_once($root_path.'include/care_api_classes/class_personell.php');
include_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/curl/class_curl.php');
//$cdObj=new Medocs;
$dept_obj=new Department;
$pers_obj=new Personell;

#-----added by VAN 03-28-08
/*
function populateICD_ICP($target){
	$icdObj=new Icd($code);
	$icpObj=new Icpm($code);
	$objResponse = new xajaxResponse();

	switch ($target){
		case "icd":
							break;
		case "icp":
							break;
	}
	return $objResponse;
}
*/
function populateICD_ICP($target, $searchkey='') {
		global $db;
		$cdObj=new Medocs;

		$objResponse = new xajaxResponse();
		#$objResponse->addAlert('target = '.$target);
		#$objResponse->addScriptCall("ajxClearOptions_ICD_ICP",$target);
		if ($target=='icd')
			$objResponse->addScriptCall("ajxClearOptions_ICD");
		else
			$objResponse->addScriptCall("ajxClearOptions_ICP");

		#$objResponse->addAlert('searchkey = '.$searchkey);
		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		#$objResponse->addAlert('searchkey2 = '.$searchkey);

		$rs=$cdObj->getICD_ICP($target, $searchkey);
		#$objResponse->addAlert('sql = '.$cdObj->sql);
		#$objResponse->addAlert('sql = '.$cdObj->count);
		if ($cdObj->count>7)
			$length = 10;
		else
			$length = $cdObj->count;

		if ($rs){
			while ($result=$rs->FetchRow()) {
				#$pos = strpos(trim($result["description"]), " ");
				#if ($pos)
					#$desc = substr(trim($result["description"]),0,$pos);
				#else
					$desc = trim($result["description"]);

				#$objResponse->addScriptCall("appendToSelection",$result["code"],$desc, $i);
				#$objResponse->addAlert('desc = '.$desc);
				#if($target=='icd')
					$objResponse->addScriptCall("ajxAddOption_ICD_ICP",$desc,$result["code"], $length,$target);
				#else
					#$objResponse->addScriptCall("ajxAddOption_ICD_ICP",$desc,$result["code"], $length,$target);
			}
		}else{
			//if ($target == 'icd'){
				//$objResponse->addAlert("ICD code does not exists...");
				//$objResponse->addScriptCall("ajxAddOption_ICD_ICP",0,0,0,false);
				$objResponse->addScriptCall("hideDiv",$target);
			//}else{
				//$objResponse->addAlert("ICP code does not exists...");
				//$objResponse->addScriptCall("ajxAddOption_ICD_ICP",0,0,0,false);
				//$objResponse->addScriptCall("hideDiv",$target);
			//}
		}
		return $objResponse;
	}
#-----------------------------

//added by daryl
//function for verify
//11/15/2013
function save_Seg_encounter_diagnoses($encounter,$code,$create_id,$descr)
{
	$cdObj=new Medocs;
	$icdObj=new Icd($code);
	$objResponse = new xajaxResponse();
	global $db;
	$rw=$icdObj->getIcd10Info($code);
	$desc=$rw->FetchRow();				
	//$descr = $desc['description'];
	$descr= explode("'", strtoupper($desc['description']));//added by genz
	$description = implode('', $descr);//added by genz
	//$descr=strtoupper($desc['description']);
	

	$cdObj->SaveSED($encounter,$code,$create_id,$description);

	return $objResponse;

}

function primaryVerify($sql_ver)
{
	global $db;
			$sql_verifyPrim = "SELECT encounter_nr,type_nr,status FROM care_encounter_diagnoses
								WHERE encounter_nr=".$db->qstr($encounter)."
								AND   type_nr = '1' 
								AND   status = ''";
			$sql_ver = $db->Execute($sql_verifyPrim);
		
return $sql_ver;
}

//getDiagnosisCodes
function addCode($encounter,$encounter_type,$xdate,$code,$doc_nr,$dept_nr,$create_id,$target,$type){
	$cdObj=new Medocs;
	//$cbjNotes=new Notes;
	$icdObj=new Icd($code);
	$icpObj=new Icpm($code);
	$objResponse = new xajaxResponse();
	global $db;

	//$timestamp= strtotime($xdate);
	//$aDate= date('Y-m-d H:m:s',$timestamp);
	#$objResponse->addAlert("icd->".$code);
	$aDate = $xdate;
	//$objResponse->addAlert("xDate =".$xdate);
	#$objResponse->addAlert("encounter->".$encounter." code->".$code." create_id->".$create_id." target->".$target);
	#$objResponse->addAlert("target =".$target);
	switch ($target){
		case "icd":
			//$xcode=strtoupper($code);
			#$objResponse->addAlert("icd->".$code);
						#added by VAN 10-28-08
						#check if icd is already exist in database or libraries
					 # $objResponse->addAlert(trim($code));

			if($rw=$icdObj->getIcd10Info($code)){
				#$objResponse->addAlert("sql icd true".$icdObj->sql);
				$desc=$rw->FetchRow();
				$xcode=strtoupper($desc['diagnosis_code']);


				$descr= explode("'", strtoupper($desc['description']));//added by genz
				$description = implode('', $descr);//added by genz

				$sql_encd = "SELECT * FROM seg_encounter_diagnosis WHERE (encounter_nr='".$encounter."' AND CODE ='".$code."');";
						$rs_encd = $db->Execute($sql_encd);
						if($row_encd = $rs_encd->FetchRow())
						{
							$result=$cdObj->AddCode($encounter,$encounter_type,$aDate,trim($xcode),$doc_nr,$dept_nr,$create_id,$target,$type);
						}
						else 
						{
							$result=$cdObj->AddCode($encounter,$encounter_type,$aDate,trim($xcode),$doc_nr,$dept_nr,$create_id,$target,$type);
							$result2 = $cdObj->save_Seg_encounter_diagnoses($encounter,$code,$create_id,$description);
						}


				#$objResponse->addAlert("cdObj->sql = '".$cdObj->sql."'; \n result = '".$description."'");
			#	$objResponse->addScriptCall("setType3",$type);

				if($result){
					if($type == 1){
						$typeName = "P";
						//add by daryl
						//11/15/2013
						$objResponse->addScriptCall("setType2",$typeName);

					
					}else{
						$typeName = "O";
											}
					$ok = 0;
					if ($encounter_type==2){
						$sql_disc = "SELECT is_discharged FROM care_encounter WHERE encounter_nr='".$encounter."'";
						$rs_disc = $db->Execute($sql_disc);
						$row_disc = $rs_disc->FetchRow();

						if (!$row_disc['is_discharged']){
								$history = "CONCAT(history,'System Discharged Upon ICD encoding: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n')";

								$sql_update = "UPDATE care_encounter
																SET is_discharged=1,
																		discharge_date='".date('Y-m-d')."',
																		discharge_time='".date('h:i:s')."',
																		history = $history,
																		modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
																		modify_time = '".date('Y-m-d H:i:s')."'
																WHERE encounter_nr='".$encounter."'";
								$rs_update = $db->Execute($sql_update);
								$ok = 1;
						}
					}

					$sql_enc = "SELECT * FROM care_encounter_diagnosis where encounter_nr='".$encounter."' AND code='".trim($xcode)."'";
					$rs_enc = $db->Execute($sql_enc);
					$row_enc = $rs_enc->FetchRow();

					$sql_doc = "SELECT fn_get_personell_name('".$doc_nr."') AS doctor";
					$rs = $db->Execute($sql_doc);
					$row_doc = $rs->FetchRow();

					$sql_dept = "SELECT fn_get_department_name('".$dept_nr."') AS department";
					$rs_dept = $db->Execute($sql_dept);
					$row_dept = $rs_dept->FetchRow();

					$doctor = trim($row_doc['doctor']);
					if (empty($doctor))
						$doctor = '';

					$dept = trim($row_dept['department']);
					if (empty($dept))
						$dept = '';

					$objResponse->addScriptCall("gui_addIcdCodeRow",$encounter,trim($desc['diagnosis_code']),$desc['description'],$target,$create_id,$typeName, $doctor, $dept, $ok);

					//$objResponse->addScriptCall("clearField",'icdCode'); clrField(icdCode);
					#commented by VAN 02-25-08
					#$objResponse->addScriptCall("clrField",'icdCode','blur');
					#$objResponse->addScriptCall("clrField",'icdCode');
				}else{
					#$objResponse->addAlert("sql icd true".$icdObj->sql);
					//$objResponse->addAlert(print_r($cdObj->sql,TRUE));
					#$objResponse->addAlert("No recordset found");
					#edited by VAN 02-25-08
					$objResponse->addAlert("Saving of the recordset failed!");
				}
			}else{
								#if not exists
				#$objResponse->addAlert("sql false".$icdObj->sql);
				$objResponse->addAlert("No Icd10 code records exists.");
								$objResponse->addScriptCall("ajxPromptDialog",$encounter,$encounter_type,$aDate,$code,$doc_nr,$dept_nr,$create_id,$target,$type);
								/*
									$objResponse->addAlert("It will be automatically added in the libraries.");

										$icdObj->saveICD($code);
										 if($rw=$icdObj->getIcd10Info($code)){
											 $desc=$rw->FetchRow();
											 $xcode=strtoupper($desc['diagnosis_code']);
											 $result=$cdObj->AddCode($encounter,$encounter_type,$aDate,trim($xcode),$doc_nr,$dept_nr,$create_id,$target,$type);

												if($result){
														 if($type == 1){
															 $typeName = "P";
														 }else{
															 $typeName = "O";
														 }

														 $objResponse->addScriptCall("gui_addIcdCodeRow",$encounter,trim($desc['diagnosis_code']),$desc['description'],$target,$create_id,$typeName);
												}else{
														$objResponse->addAlert("Saving of the recordset failed!");
												 }

						}    */

			}
		break;
		//For ICPM code Entry
		case "icp":
			if($rw=$icpObj->getIcpmInfo($code)){
				#$objResponse->addAlert("sql icp true".$icpObj->sql);
				$desc=$rw->FetchRow();

				#$objResponse->addAlert("aDate=".$aDate." dept_nr =".$dept_nr." doc_nr=".$doc_nr);
				#$dept = getDeptInfo($dept_nr);
				#$doc  = getDocInfo($doc_nr);

				#$objResponse->addAlert("dept=".$dept." doc=".$doc);
				#$objResponse->addAlert("$target=".$target);
#$objResponse->addAlert("desc['code'] = '".$desc['code']."'");
				$result=$cdObj->AddCode($encounter,$encounter_type,$aDate,trim($desc['code']),$doc_nr, $dept_nr,$create_id,$target,$type);
#$objResponse->addAlert("cdObj->sql = '".$cdObj->sql."'; \n result = '".$result."'");
				if($result){
					if($type == 1){
						$typeName = "P";
					}else{
						$typeName = "O";
					}

					$sql_enc = "SELECT * FROM care_encounter_procedure where encounter_nr='".$encounter."' AND code='".trim($desc['code'])."'";
					$rs_enc = $db->Execute($sql_enc);
					$row_enc = $rs_enc->FetchRow();

					$sql_doc = "SELECT fn_get_personell_name('".$row_enc['responsible_clinician']."') AS doctor";
					#$objResponse->alert($sql_doc);
					$rs = $db->Execute($sql_doc);
					$row_doc = $rs->FetchRow();

					$sql_dept = "SELECT fn_get_department_name('".$row_enc['responsible_dept_nr']."') AS department";
					$rs_dept = $db->Execute($sql_dept);
					$row_dept = $rs_dept->FetchRow();

					$doctor = trim($row_doc['doctor']);
					if (empty($doctor))
						$doctor = '';

					$dept = trim($row_dept['department']);
					if (empty($dept))
						$dept = '';

					#$objResponse->addAlert($typeName." , ".trim($desc['code'])." , ".$desc['description']);
					$objResponse->addScriptCall("gui_addIcpCodeRow",$encounter,trim($desc['code']),$desc['description'],$target,$create_id,$typeName,$doctor,$dept);
					//clear field
					#commented by VAN 02-25-08
					#$objResponse->addScriptCall("clrField",'icpCode','blur');
					#$objResponse->addScriptCall("clearField",'icpCode');
				}else{
					//$objResponse->addAlert(print_r($icpObj->sql,TRUE));
					$objResponse->addAlert("Saving of the recordset failed!");
				}
			}else{
				$objResponse->addAlert("No Icp code records exists.");
			}
		break;
	}
	return $objResponse;
}// End addCode Function

//Added by Dommie 03-07-14
function saveAltDiagDesc($enc_nr, $code, $desc, $create_id) {
		$objResponse = new xajaxResponse();

		$objmdoc = new Medocs();
		if (!$objmdoc->saveAltDiagDesc($enc_nr, $code, $desc, $create_id)) {
			$objResponse->addAlert("ERROR: ".$objmdoc->sql);
		}

		return $objResponse;
	}

function saveICDifnotExist($encounter,$encounter_type,$aDate,$code,$doc_nr,$dept_nr,$create_id,$target,$type){
		$icdObj=new Icd($code);
		$cdObj=new Medocs;
		$objResponse = new xajaxResponse();
		global $db;

		$objResponse->addAlert("It will be automatically added in the libraries.");

		$icdObj->saveICD($code);
		 # $objResponse->addAlert($icdObj->sql);
		if($rw=$icdObj->getIcd10Info($code)){
			 $desc=$rw->FetchRow();
			 $xcode=strtoupper($desc['diagnosis_code']);
			#$objResponse->addAlert($encounter.",".$encounter_type.",".$aDate.",".$code.",".$doc_nr.",".$dept_nr.",".$create_id.",".$target.",".$type);
			$result=$cdObj->AddCode($encounter,$encounter_type,$aDate,$code,$doc_nr,$dept_nr,$create_id,$target,$type);
			# $objResponse->addAlert($icdObj->sql);
			 if($result){
				 if($type == 1){
						 $typeName = "P";
				 }else{
						 $typeName = "O";
				 }
				 $ok = 0;
				 if ($encounter_type==2){
						$sql_disc = "SELECT is_discharged FROM care_encounter WHERE encounter_nr='".$encounter."'";
						$rs_disc = $db->Execute($sql_disc);
						$row_disc = $rs_disc->FetchRow();

						if (!$row_disc['is_discharged']){
								$history = "CONCAT(history,'System Discharged Upon ICD encoding: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n')";

								$sql_update = "UPDATE care_encounter
																SET is_discharged=1,
																		discharge_date='".date('Y-m-d')."',
																		discharge_time='".date('h:i:s')."',
																		history = $history,
																		modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
																		modify_time = '".date('Y-m-d H:i:s')."'
																WHERE encounter_nr='".$encounter."'";
								$rs_update = $db->Execute($sql_update);
								$ok = 1;
						}
					}

		 $sql_enc = "SELECT * FROM care_encounter_diagnosis where encounter_nr='".$encounter."' AND code='".$code."'";
		 $rs_enc = $db->Execute($sql_enc);
		 $row_enc = $rs_enc->FetchRow();

		 $sql_doc = "SELECT fn_get_personell_name('".$row_enc['diagnosing_clinician']."') AS doctor";
		 $rs = $db->Execute($sql_doc);
		 $row_doc = $rs->FetchRow();

		 $sql_dept = "SELECT fn_get_department_name('".$row_enc['diagnosing_dept_nr']."') AS department";
		 $rs_dept = $db->Execute($sql_dept);
		 $row_dept = $rs_dept->FetchRow();

		 $doctor = trim($row_doc['doctor']);
		 if (empty($doctor))
			$doctor = '';

		 $dept = trim($row_dept['department']);
		 if (empty($dept))
			$dept = '';

				 $objResponse->addScriptCall("gui_addIcdCodeRow",$encounter,trim($desc['diagnosis_code']),$desc['description'],$target,$create_id,$typeName, $doctor, $dept, $ok);

			}else{
				 $objResponse->addAlert("Saving of the recordset failed!");
			}
		}
		return $objResponse;
}

//Note FIXED remarks: Done by mark on March 28, 2007
function rmvCode($type,$encounter,$code,$target,$rowno,$create_id){
	$cdObj=new Medocs;
	$objResponse = new xajaxResponse();

	if($target=="icd"){
		$icdObjct = new Icd($code);
		if($row=$icdObjct->getIcd10Info($code)){
			#$objResponse->addAlert("icd->row->".$row);
			//$objResponse->addAlert("Do you want to delete this record?"); //removed enc_type by: Mark March 2c8, 2007
			$result=$cdObj->removeCode($encounter,$code,$target,$create_id);

			//added by daryl
			//11/15/2013
			if ($result)
			{
			$objResponse->addScriptCall("setType3",$type);
		
          	}
			else
			{
			$objResponse->addScriptCall("setType3",$type);
			
				
			}
			//TODO fix error messaging
			#$objResponse->addAlert("result->".$result);
			#remove the alert as requested by Ma'am Rhia 
            #edited by VAN 03-01-2013
            #$objResponse->addAlert("Data has been successfully deleted");
			$objResponse->addAssign("icdCode", "focus()", true);
		}else{
			$objResponse->addAlert("No record to delete");
		}
	}elseif($target=="icp"){
		$icpObjct = new Icpm($code);
		if($row=$icpObjct->getIcpmInfo($code)){

			#$objResponse->addAlert("icp->row->".$row);
			//$objResponse->addAlert("Do you want to delete this record?"); //removed enc_type by: Mark March 28, 2007
			$result=$cdObj->removeCode($encounter,$code,$target,$create_id);

			$objResponse->addAlert("Data has been successfully deleted");
		}else{
			$objResponse->addAlert("No record to delete");
		}
	}

	if($result){
		//$objResponse->addAlert("result->rowno".$rowno);
		if($target=='icd'){
			$objResponse->addScriptCall("gui_rmvIcdCodeRow",$rowno);
		}elseif($target=='icp'){
			$objResponse->addScriptCall("gui_rmvIcpCodeRow",$rowno);
		}
	}else{
		$objResponse->addAlert(print_r($cdObj->sql,TRUE));
	}
	return $objResponse;
}

// Note add:  dept_nr March 28, 2007
//change populateCode($encounter, $target)
function populateCode($encounter,$encounter_type,$target){
	$objDRG= new DRG;
	$objResponse = new xajaxResponse();
	global $db;
    
	switch ($target){
		case "icd":
			//get diagnosis
			$cdDiagnosis=$objDRG->getDiagnosisCodes($encounter, $encounter_type);
			
			//added by daryl
			//11/15/2013
			$icdverify=$objDRG->primaryVerify($encounter);

			#$objResponse->addAlert("icd->cdDiagnosis->".$cdDiagnosis);
			#$objResponse->addAlert("icd->cdDiagnosis->".$objDRG->sql);
			if($cdDiagnosis){
				$dCount = $cdDiagnosis->RecordCount();
				#$objResponse->addAlert("icd->dCount->".$dCount);
				if($dCount>0){
					
						//added by daryl
					//11/15/2013
						if ($icdverify>0)
						{
							$objResponse->addScriptCall("setType2","P");
									
						}
						else

					{
						$objResponse->addScriptCall("setType2","O");

					}
					
					while($result=$cdDiagnosis->FetchRow()){
						if($result['status']!='deleted'){
							#$objResponse->addAlert("icd->result->".$result['code']." target->".$target." tabs->".$tabs);
							#$objResponse->addAlert($result['type']);
							
							#$objResponse->addAlert("icd->encounter".$result['encounter_nr']." result[code]->".$result['code']." result[diagnosis]->".$result['diagnosis']);
							if($result['type']!=0){
								$type="P"; // set type = "P"  for other diagnosis
								
							}else{
								$type="O"; // set type = "O" for pricipal diagnosis
							
									}

							 #$sql_enc = "SELECT * FROM care_encounter_diagnosis where encounter_nr='".$encounter."' AND code='".$result['code']."'";
							 #$rs_enc = $db->Execute($sql_enc);
							 #$row_enc = $rs_enc->FetchRow();

							 $sql_doc = "SELECT fn_get_personell_name('".$result['diagnosing_clinician']."') AS doctor";
							 $rs = $db->Execute($sql_doc);
							 $row_doc = $rs->FetchRow();

							 $sql_dept = "SELECT fn_get_department_name('".$result['diagnosing_dept_nr']."') AS department";
							 $rs_dept = $db->Execute($sql_dept);
							 $row_dept = $rs_dept->FetchRow();

							 $doctor = trim($row_doc['doctor']);
							 if (empty($doctor))
								$doctor = '';

							 $dept = trim($row_dept['department']);
							 if (empty($dept))
								$dept = '';

							#$objResponse->addAlert("type->".$type);
							$objResponse->addScriptCall("gui_addIcdCodeRow",$result['encounter_nr'],$result['code'],$result['diagnosis'],$target,$result['create_id'],$type, $doctor, $dept);
						}
					}// end while statement
				}
			//}else{
				//$objResponse->addAlert(print_r($cdDiagnosis,true));
				//$objResponse->addAlert("No recordset found");
			}

			break;
		case "icp":
			$cdProcedure=$objDRG->getProcedureCodes($encounter,$encounter_type);
			#$objResponse->addAlert("sql = ".$objDRG->sql);
			if ($cdProcedure){
				$pCount = $cdProcedure->Recordcount();
				if($pCount>0){
					while($p=$cdProcedure->FetchRow()){
						if($p['status']!='deleted'){
							#$objResponse->addAlert("icp->target->".$target." icp-tabs->".$tabs);
							#$objResponse->addAlert("encounter->".$p['encounter_nr']." procedure_code->".$p['code']." description->".$p['therapy']." type_nr".$p['type_nr']);
							if($p['type']!=0){
								$type= "P";  //set type = P for Principal diagnosis
							}else{
								$type= "O"; //set type = O for Other diagnosis
							}
							#$docName = getDocInfo($p['responsible_clinician']);
							#$sql_enc = "SELECT * FROM care_encounter_procedure where encounter_nr='".$encounter."' AND code='".trim($desc['code'])."'";
							#$rs_enc = $db->Execute($sql_enc);
							#$row_enc = $rs_enc->FetchRow();

							$sql_doc = "SELECT fn_get_personell_name('".$p['responsible_clinician']."') AS doctor";
							$rs = $db->Execute($sql_doc);
							$row_doc = $rs->FetchRow();

							$sql_dept = "SELECT fn_get_department_name('".$p['responsible_dept_nr']."') AS department";
							$rs_dept = $db->Execute($sql_dept);
							$row_dept = $rs_dept->FetchRow();

							$doctor = trim($row_doc['doctor']);
							if (empty($doctor))
								$doctor = '';

							$dept = trim($row_dept['department']);
							if (empty($dept))
								$dept = '';

							$objResponse->addScriptCall("gui_addIcpCodeRow",$p['encounter_nr'],$p['code'],$p['therapy'],$target,$p['create_id'],$type,$doctor, $dept);
						}// End of If status is not deleted
					}//End of While loop
				}
			//}else{
				//$objResponse->addAlert(print_r($cdProcedure,true));
				//$objResponse->addAlert("No recordset found");
			}

			break;
	}//end switch statement

	return $objResponse;
}//end populateCode


function getDocInfo($personell_nr = 0){
	global $pers_obj;
	$objResponse = new xajaxResponse();
	if($personell_nr != 0){
		$result = $pers_obj->get_Person_name($personell_nr);
		if($result){
			$fullname = $result['name_last'].", ".$result['name_first'];
		}else{
			$objResponse->addAlert("No doctors in the list");
			return false;
		}
	}
	return $fullname;
} //end function getDoctorsPersonalInfo()

function getDeptInfo($dept=0){
	global $dept_obj; //getDeptAllInfo() //getDeptofDoctor()
	$objResponse = new xajaxResponse();
	if($dept !=0){
		$result = $dept_obj->getDeptAllInfo($dept);
		if($result){
			$deptName = $result['description'];
		}else{
			$objResponse->addAlert("No department exists");
			return false;
		}
	}
	return $deptName;
}


//Set Department for Diagnosis
function setDepartments_d($personell_nr=0) {
	global $dept_obj,$pers_obj;

	$objResponse = new xajaxResponse();
	#$objResponse->addAlert("setDepartments= $personell_nr");
	if ($personell_nr!=0){
		$result=$dept_obj->getDeptofDoctor($personell_nr);
		#$objResponse->addAlert("sql : $dept_obj->sql");
		#$objResponse->addAlert("name_formal = ".$result["name_formal"]." - ".$result["nr"]);
		if ($result) {
#			$list = $pers_obj->getAncestorChildrenDept($result["nr"]);   # burn commented : July 19, 2007
			$list = $dept_obj->getAncestorChildrenDept($result["nr"]);   # burn added : July 19, 2007
#$objResponse->addAlert("setDepartments_d : list = '$list'; result['nr'] = '".$result['nr']."'");
			if (trim($list)!="")
				$list .= ",".$result["nr"];
			else
				$list .= $result["nr"];
#$objResponse->addAlert("setDepartments_d : list 2 = '$list' ");
			$objResponse->addScriptCall("ajxSetDepartment_d",$result["nr"],$list);
			//$objResponse->addScriptCall("ajxSetDoctor",$personell_nr);
		}

		#else{
		#	$objResponse->addAlert("setDepartments : Error retrieving Department information...");
		#}
	}
	return $objResponse;
}//End of function setDepartments_d

//Set Doctors for Diagnosis
function setDoctors_d($admit_inpatient=0, $dept_nr=0) {
	global $pers_obj;
	$objResponse = new xajaxResponse();

	if ($dept_nr) $rs=$pers_obj->getDoctorByDept($dept_nr, $admit_inpatient);
	else $rs=$pers_obj->getDoctors($admit_inpatient);

	 #$objResponse->addAlert("sql : $pers_obj->sql");
		$objResponse->addScriptCall("ajxClearOptions_d",0);
	if ($rs) {
		if($pers_obj->count > 1){
			$objResponse->addScriptCall("ajxAddOption_d",0,"-Select a Doctor-",0);
		}else{
			if ($dept_nr){
				$objResponse->addScriptCall("ajxAddOption_d",0,"-No Doctor Available-",0);
			}else{
				$objResponse->addScriptCall("ajxAddOption_d",0,"-Select a Doctor-",0);
			}
		}

		while ($result=$rs->FetchRow()) {
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
			#$doctor_name = $result["name_first"]." ".$result["name_2"]." ".$middleInitial.$result["name_last"];
			#$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
			if (trim($result["name_middle"]))
					$dot  = ".";

				$doctor_name = trim($result["name_last"]).", ".trim($result["name_first"])." ".substr(trim($result["name_middle"]),0,1).$dot;
				$doctor_name = ucwords(strtolower($doctor_name)).", MD";

			$objResponse->addScriptCall("ajxAddOption_d",0,$doctor_name,$result["personell_nr"]);
		}

	}else{
		$objResponse->addAlert("setDoctors : Error retrieving Doctors information...");
	}

	return $objResponse;
}

/*
 * Set All Departments for Diagnosis
 * @param 0 OPD, 1 IPD
 */
function setALLDepartment_d($admit_inpatient,$dept_nr){
	global $dept_obj;
	#$dept_obj=new Department;

	$objResponse = new xajaxResponse();

	//if dept_nr = 0 load all departments..
	if($dept_nr!=0 || !empty($dept_nr)){
		$rs=$dept_obj->getAllOPDMedicalObject1($admit_inpatient,$dept_nr);
	}else{
		$rs=$dept_obj->getAllOPDMedicalObject($admit_inpatient);
	}
	//$objResponse->addAlert("dept_obj->dept_count = '".$dept_obj->dept_count."'");
	//$objResponse->addAlert($rs);
	//$objResponse->addAlert($dept_obj->sql);

	$objResponse->addScriptCall("ajxClearOptions_d",1);
	if ($rs) {
		if ($dept_obj->dept_count > 1){
			$objResponse->addScriptCall("ajxAddOption_d",1,"-Select a Department-",0);
		}
		while ($result=$rs->FetchRow()) {
			 $objResponse->addScriptCall("ajxAddOption_d",1,$result["name_formal"],$result["nr"]);
		}
		//$objResponse->addScriptCall("ajxSelectDept",$dept_nr);
	}else{
		//$objResponse->addAlert("setALLDepartment_d : Error retrieving Department information...");
	}

	return $objResponse;
}

// Set Departments for Procedure
function setDepartments_p($personell_nr=0) {
	global $dept_obj,$pers_obj;

	$objResponse = new xajaxResponse();
#$objResponse->addAlert("setDepartments_p : personell_nr = '$personell_nr'");
	if ($personell_nr!=0){
		$result=$dept_obj->getDeptofDoctor($personell_nr);
		if ($result) {
#			$list = $pers_obj->getAncestorChildrenDept($result["nr"]);   # burn commented : July 19, 2007
			$list = $dept_obj->getAncestorChildrenDept($result["nr"]);   # burn added : July 19, 2007
#$objResponse->addAlert("setDepartments_p : list = '$list'; result['nr'] = '".$result['nr']."'");
			if (trim($list)!="")
				$list .= ",".$result["nr"];
			else
				$list .= $result["nr"];
#$objResponse->addAlert("setDepartments_p : list 2 = '$list' ");
			$objResponse->addScriptCall("ajxSetDepartment_p",$result["nr"],$list);
			//$objResponse->addScriptCall("ajxSetDoctor",$personell_nr);
		}

	}
	return $objResponse;
}//End of function setDepartments_p

// Set Doctors for Procedure
function setDoctors_p($admit_inpatient=0, $dept_nr=0) {
	global $pers_obj;
	$objResponse = new xajaxResponse();

	#$cond=" AND d.does_surgery='1' ";
	if ($dept_nr){
		$rs=$pers_obj->getDoctorByDept($dept_nr, $admit_inpatient);
	}else{
		$rs=$pers_obj->getDoctors($admit_inpatient,$cond);
	}
#$objResponse->addAlert("setDoctors_p : pers_obj->sql = '".$pers_obj->sql."'; \n rs ='$rs' ");
	$objResponse->addScriptCall("ajxClearOptions_p",0);
	if ($rs) {
		$objResponse->addScriptCall("ajxAddOption_p",0,"-Select a Doctor-",0);
		while ($result=$rs->FetchRow()) {
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
			#$doctor_name = $result["name_first"]." ".$result["name_2"]." ".$middleInitial.$result["name_last"];
			#$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
			if (trim($result["name_middle"]))
					$dot  = ".";

				$doctor_name = trim($result["name_last"]).", ".trim($result["name_first"])." ".substr(trim($result["name_middle"]),0,1).$dot;
				$doctor_name = ucwords(strtolower($doctor_name)).", MD";

			$objResponse->addScriptCall("ajxAddOption_p",0,$doctor_name,$result["personell_nr"]);
		}

	}else{
		$objResponse->addScriptCall("ajxAddOption_p",0,"-No Available Doctor-",0);
#		$objResponse->addAlert("setDoctors_p : Error retrieving Doctors information...");
	}

	return $objResponse;
} //End of setDoctors

/*
 * Set All Departments for Procedure
 * @param 0 OPD, 1 IPD
 */
function setALLDepartment_p($admit_inpatient,$dept_nr){
	global $dept_obj;
	#$dept_obj=new Department;

	$objResponse = new xajaxResponse();
#	$objResponse->addAlert("setALLDepartment_p : dept_nr->".$dept_nr);
#	$objResponse->addAlert("ENTER setALLDepartment_p ");
	#$rs=$dept_obj->getAllOPDMedicalObject($admit_inpatient);

	#$cond=" AND does_surgery='1' ";
#	$cond='';
	//if dept_nr = 0 load all departments..
	if($dept_nr!=0 || !empty($dept_nr)){
#$objResponse->addAlert("setALLDepartment_p : TRUE dept_nr = '".$dept_nr."'");
		$rs=$dept_obj->getAllOPDMedicalObject1($admit_inpatient,$dept_nr,$cond);
	}else{
#$objResponse->addAlert("setALLDepartment_p : FALSE dept_nr = '".$dept_nr."'");
		$rs=$dept_obj->getAllOPDMedicalObject($admit_inpatient,$cond);
	}
	#$objResponse->addAlert("rs->".$rs);

#	$objResponse->addAlert("setALLDepartment_p : dept_object->sql '".$dept_obj->sql."'; \n rs='$rs'; \n dept_obj->dept_count ='$dept_obj->dept_count'");

	$objResponse->addScriptCall("ajxClearOptions_p",1);
#	$objResponse->addScriptCall("ajxAddOption_p",1,"-Select a burn Department-",0);
	if ($rs) {
#		if ($dept_obj->dept_count > 1){
			$objResponse->addScriptCall("ajxAddOption_p",1,"-Select a Department-",0);
#		}
		while ($result=$rs->FetchRow()) {
			$objResponse->addScriptCall("ajxAddOption_p",1,$result["name_formal"],$result["nr"]);
		}
	}else{
		$objResponse->addScriptCall("ajxAddOption_p",1,"-No Available Department-",0);
#		$objResponse->addAlert("setALLDepartment_p : Error retrieving Department information...");
	}

	return $objResponse;
} //End of function setALLDepartments_p

#commented by VAN 02-18-08
// Set consulting Department
/*
function setDepartments_c($personell_nr =0 ){
	global $dept_obj;

	$objResponse = new xajaxResponse();
	if($personell_nr != 0){
		$result = $dept_obj->getDeptofDoctor($personell_nr);
		if($result){
			$objResponse->addScriptCall("ajxSetDepartment_c", $result['nr']);
		}
	}
	return $objResponse;
}//End of Function setDepartments_c
*/

#edited by VAN 02-18-08
// Set consulting Department
function setDepartments_c($personell_nr=0) {
	global $dept_obj,$pers_obj;

	$objResponse = new xajaxResponse();
	#$objResponse->alert("personell_nr = ".$personell_nr);
	if ($personell_nr!=0){
		$result=$dept_obj->getDeptofDoctor($personell_nr);
		#$objResponse->addAlert("sql = ".$dept_obj->sql);
		if ($result) {
			$list = $dept_obj->getAncestorChildrenDept($result["nr"]);   # burn added : July 19, 2007
			if (trim($list)!="")
				$list .= ",".$result["nr"];
			else
				$list .= $result["nr"];
			#$objResponse->addAlert("result = ".$result["nr"]);
			$objResponse->addScriptCall("ajxSetDepartment_c",$result["nr"],$list);
		}
	}
	return $objResponse;
}//End of function setDepartments_c



// Set Departments for Final diagnosis / procedure
function setDepartments_f($personell_nr=0) {
	global $dept_obj;

	$objResponse = new xajaxResponse();
	$objResponse->addAlert("setDepartments= $personell_nr");

	if ($personell_nr!=0){
		$result=$dept_obj->getDeptofDoctor($personell_nr);
		if ($result) {
			$objResponse->addScriptCall("ajxSetDepartment_f",$result["nr"]);
		}
	}
	return $objResponse;
}//End of function setDepartments_f

// Set Doctors for Final Diagnosis / procedure
function setDoctors_f($admit_inpatient=0, $dept_nr=0, $personell_nr=0) {
	global $pers_obj;
	$objResponse = new xajaxResponse();

	if ($dept_nr){
		$rs=$pers_obj->getDoctorByDept($dept_nr, $admit_inpatient);
	}else{
		$rs=$pers_obj->getDoctors($admit_inpatient);
	}

	$objResponse->addScriptCall("ajxClearOptions_f",0);

	#$objResponse->addAlert("pers_obj->sql=".$pers_obj->sql);
	#$objResponse->addAlert("dr =".$personell_nr);

	if ($rs) {

		if($pers_obj->count > 1){
			$objResponse->addScriptCall("ajxAddOption_f",0,"-Select a Doctor-",0);
		}else{
			if ($dept_nr){
				$objResponse->addScriptCall("ajxAddOption_f",0,"-No Doctor Available-",0);
			}else{
				$objResponse->addScriptCall("ajxAddOption_f",0,"-Select a Doctor-",0);
			}
		}

		while ($result=$rs->FetchRow()) {
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
			#$doctor_name = $result["name_first"]." ".$result["name_2"]." ".$middleInitial.$result["name_last"];
			#$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
			if (trim($result["name_middle"]))
					$dot  = ".";

				$doctor_name = trim($result["name_last"]).", ".trim($result["name_first"])." ".substr(trim($result["name_middle"]),0,1).$dot;
				$doctor_name = ucwords(strtolower($doctor_name)).", MD";

			$objResponse->addScriptCall("ajxAddOption_f",0,$doctor_name,$result["personell_nr"]);
		}
		if ($personell_nr)
			$objResponse->addScriptCall("ajxSetDoctor_f",$personell_nr);

	}else{
		$objResponse->addAlert("setDoctors : Error retrieving Doctors information...");
	}

	return $objResponse;
} //End of setDoctors

/*
 * Set All Departments for Final Diagnosis / procedure
 * @param 0 OPD, 1 IPD
 */
function setALLDepartment_f($admit_inpatient, $dept_nr){
	global $dept_obj;
	#$dept_obj=new Department;

	$objResponse = new xajaxResponse();

	//if dept_nr = 0 load all departments..
	#if($dept_nr!=0 || !empty($dept_nr)){
	#	$rs=$dept_obj->getAllOPDMedicalObject1($admit_inpatient,$dept_nr);
	#}else{
		$rs=$dept_obj->getAllOPDMedicalObject($admit_inpatient);
	#}

	#$objResponse->addAlert("dept_nr =".$dept_nr);
	#$objResponse->addAlert("dept_obj->sql=".$dept_obj->sql);

	if ($rs) {
		$objResponse->addScriptCall("ajxClearOptions_f",1);
		if ($dept_obj->dept_count > 1){
			$objResponse->addScriptCall("ajxAddOption_f",1,"-Select a Department-",0);
		}
		while ($result=$rs->FetchRow()) {
			$objResponse->addScriptCall("ajxAddOption_f",1,$result["name_formal"],$result["nr"]);
		}

				if ($dept_nr)
						$objResponse->addScriptCall("ajxSetDepartment_f",$dept_nr);
	}else{
		//$objResponse->addAlert("setALLDepartment_f : Error retrieving Department information...");
	}

	return $objResponse;
} //End of function setALLDepartments_p


// Set consulting Doctors

function setDoctors_c($admit_inpatient=0, $dept_nr=0, $personell_nr=0) {
	global $pers_obj;
	$objResponse = new xajaxResponse();

	#$objResponse->addAlert("setDoctors_c : dept_nr = '".$dept_nr."'");

	if ($dept_nr){
		$rs=$pers_obj->getDoctorByDept($dept_nr, $admit_inpatient);
	}else{
		$rs=$pers_obj->getDoctors($admit_inpatient);
	}

	$objResponse->addScriptCall("ajxClearOptions_c",0);

	#$objResponse->addAlert("setDoctors->rs=".$rs);

	if ($rs) {
        if($pers_obj->count > 1){
			$objResponse->addScriptCall("ajxAddOption_c",0,"-Select a Doctor-",0);
		}else{
			if ($dept_nr){
				$objResponse->addScriptCall("ajxAddOption_c",0,"-No Doctor Available-",0);
			}else{
				$objResponse->addScriptCall("ajxAddOption_c",0,"-Select a Doctor-",0);
			}
		}

		while ($result=$rs->FetchRow()) {
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
			#$doctor_name = $result['name_first']." ".$result['name_2']." ".$middleInitial.$result['name_last'];
			#$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
			if (trim($result["name_middle"]))
					$dot  = ".";

				$doctor_name = trim($result["name_last"]).", ".trim($result["name_first"])." ".substr(trim($result["name_middle"]),0,1).$dot;
				$doctor_name = ucwords(strtolower($doctor_name)).", MD";
			$objResponse->addScriptCall("ajxAddOption_c",0,$doctor_name,$result['personell_nr']);
		}
		#$objResponse->addAlert("personell_nr = ".$personell_nr);
		if ($personell_nr)
			$objResponse->addScriptCall("ajxSetDoctor_c",$personell_nr);

	}else{
		$objResponse->addAlert("setDoctors : Error retrieving Doctors information...");
	}

	return $objResponse;
} //End of function setDoctors for consultation

/*
// Set consulting Doctors
function setDoctors_c($admit_inpatient=0, $dept_nr=0) {
	global $pers_obj;
	$objResponse = new xajaxResponse();

	if ($dept_nr) $rs=$pers_obj->getDoctorByDept($dept_nr, $admit_inpatient);
	else $rs=$pers_obj->getDoctors($admit_inpatient);

	#$objResponse->addAlert('admit_inpatient, dept_nr = '.$admit_inpatient." - ".$dept_nr);
	#$objResponse->addAlert("setDoctors_c : sql = ".$pers_obj->sql);

	$objResponse->addScriptCall("ajxClearOptions_c",0);
	if ($rs) {
		if($pers_obj->count ==1){
			$objResponse->addScriptCall("ajxSetDoctor_c",$pers_obj['personell_nr']);
		}elseif($pers_obj->count > 1){
			$objResponse->addScriptCall("ajxAddOption_c",0,"-Select a Doctor-",0);
		}else{
			if ($dept_nr){
				$objResponse->addScriptCall("ajxAddOption_c",0,"-No Doctor Available-",0);
			}else{
				$objResponse->addScriptCall("ajxAddOption_c",0,"-Select a Doctor-",0);
			}
		}

		while ($result=$rs->FetchRow()) {
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
			$objResponse->addScriptCall("ajxAddOption_c",0,$doctor_name,$result["personell_nr"]);
		}

	}else{
		$objResponse->addAlert("setDoctors : Error retrieving Doctors information...");
	}

	return $objResponse;
}
*/
#commented by VAN 02-18-08
//set all consulting departments
/*
function setALLDepartment_c($admit_inpatient, $dept_nr){
	global $dept_obj;

	$objResponse = new xajaxResponse();

	#$objResponse->addAlert("setALLDepartment_c : admit_inpatient = '".$admit_inpatient."'");
	#$objResponse->addAlert("setALLDepartment_c : dept_nr = '".$dept_nr."'");

	//if dept_nr = 0 load all departments..
	if($dept_nr!=0 || !empty($dept_nr)){
		$rs=$dept_obj->getAllOPDMedicalObject1($admit_inpatient,$dept_nr);
	}else{
		$rs=$dept_obj->getAllOPDMedicalObject($admit_inpatient);
	}
	//debug -1
	#$objResponse->addAlert("setALLDepartment_c : dept_obj->sql='".$dept_obj->sql."'");
	#$objResponse->addAlert("setALLDepartment_c : rs='".$rs."'");

	if($rs){
		$objResponse->addScriptCall("ajxClearOptions_c", 1);
		if($dept_obj->dept_count > 1){
			$objResponse->addScriptCall("ajxAddOption_c", 1, "-Select a Department-", 0);
		}
		while($result = $rs->FetchRow()){
			$objResponse->addScriptCall("ajxAddOption_c", 1, $result['name_formal'], $result['nr']);
		}
	}

	return $objResponse;
}//end of function setALLDepartment_c
*/

// edited by VAN 02-18-08
function setALLDepartment_c($admit_inpatient,$dept_nr){
	global $dept_obj;

	$objResponse = new xajaxResponse();
	#$objResponse->addAlert('admit_inpatient, dept_nr = '.$admit_inpatient." - ".$dept_nr);
	//if dept_nr = 0 load all departments..
	if($dept_nr!=0 || !empty($dept_nr)){
		$rs=$dept_obj->getAllOPDMedicalObject1($admit_inpatient,$dept_nr);
	}else{
		$rs=$dept_obj->getAllOPDMedicalObject($admit_inpatient);
	}
	#$objResponse->addAlert("setALLDepartment_c : dept_obj->sql='".$dept_obj->sql."'");

	$objResponse->addScriptCall("ajxClearOptions_c",1);
	if ($rs) {
	#$objResponse->addAlert("setALLDepartment_c : dept_obj->count='".$dept_obj->dept_count);
		if ($dept_obj->dept_count > 1){
			$objResponse->addScriptCall("ajxAddOption_c",1,"-Select a Department-",0);
		}
		while ($result=$rs->FetchRow()) {
			$objResponse->addScriptCall("ajxAddOption_c",1,$result["name_formal"],$result["nr"]);
		}
	}else{
	}

	return $objResponse;
}


function showDiagnosisTherapy($encounter_nr, $encounter_type,$lnk){
	$objDRG = new DRG;
	$objEncounter = new Encounter($encounter_nr);
	$objResponse = new xajaxResponse();

	$result = array();
	$rowsDiagnosis=0;
	$rowsTherapy=0;
	$principalCount=0;
	$otherCount=0;

	$result['diagnosis_principal']='';
	$result['diagnosis_others']='';
	if ($result_diagnosis = $objDRG->getDiagnosisCodes($encounter_nr,$encounter_type)){
		$rowsDiagnosis = $result_diagnosis->RecordCount();
		#echo "   code  :   diagnosis <br> \n";
		while($temp=$result_diagnosis->FetchRow()){
			#echo $temp['code']." : ".$temp['diagnosis']." <br> \n";
			if ($temp['type']){
				$result['diagnosis_principal'].= $temp['code']." : ".$temp['diagnosis']." <br> \n";
				$principalCount++;
			}else{
				$result['diagnosis_others'].= $temp['code']." : ".$temp['diagnosis']." <br> \n";
				$otherCount++;
			}
		}
	}

#$objResponse->addAlert("showDiagnosisTherapy : result_diagnosis = '".$result_diagnosis."'");
#$objResponse->addAlert("showDiagnosisTherapy : objDRG->sql = '".$objDRG->sql."'");

	$result['therapy_principal']='';
	$result['therapy_others']='';
	if ($result_therapy = $objDRG->getProcedureCodes($encounter_nr,$encounter_type)){
		$rowsTherapy = $result_therapy->RecordCount();
		#echo "   code  :   therapy <br> \n";
		while($temp=$result_therapy->FetchRow()){
			#echo $temp['code']." : ".$temp['therapy']." <br> \n";
			if ($temp['type']){
				$result['therapy_principal'].= $temp['code']." : ".$temp['therapy']." <br> \n";
				$principalCount++;
			}else{
				$result['therapy_others'].= $temp['code']." : ".$temp['therapy']." <br> \n";
				$otherCount++;
			}
		}
	}

#$objResponse->addAlert("showDiagnosisTherapy : principalCount = '".$principalCount."'");
#$objResponse->addAlert("showDiagnosisTherapy : otherCount = '".$otherCount."'");
		$msg='';
		if ($principalCount==0){
			$msg ='	<td colspan="2" align="center">
							<font color="red">No Principal Diagnosis/Procedure</font>
						</td>';
		}else{
			$msg ='	<td>'.$result['diagnosis_principal'].'</td>
						<td>'.$result['therapy_principal'].'</td>
			';
		}
#$objResponse->addAlert("showDiagnosisTherapy : principal; msg ='".$msg."'");
		$objResponse->addAssign("principal","innerHTML",$msg);

		if ($otherCount==0){
			$msg ='	<td colspan="2" align="center">
							<font color="red">No Other Diagnosis/Procedure</font>
						</td>';
		}else{
			$msg ='	<td>'.$result['diagnosis_others'].'</td>
						<td>'.$result['therapy_others'].'</td>
			';
		}
#$objResponse->addAlert("showDiagnosisTherapy : others; msg ='".$msg."'");
		$objResponse->addAssign("others","innerHTML",$msg);

		$lnk = "<a href=\"$lnk\">Enter new record</a>";

		$objResponse->addAssign("enterNewRecord","innerHTML",$lnk);


			# burn added : April 30, 2007
		if ($encounter_type==1){
			$segEncounterType="ER";
		}elseif ($encounter_type==2){
			$segEncounterType="OPD";
		}elseif ($encounter_type==3){
			$segEncounterType="Inpatient (ER)";
		}elseif ($encounter_type==4){
			$segEncounterType="Inpatient (OPD)";
		}
		$objResponse->addAssign("segEncounterType","innerHTML",$segEncounterType);   # burn added : April 28, 2007

#		$discharged = $objEncounter->Is_Discharged($encounter_nr);
#$objResponse->addAlert("showDiagnosisTherapy : discharged = '".$discharged."'");
#$objResponse->addAlert("showDiagnosisTherapy : root_path = '".$root_path."'");

			# burn added : April 28, 2007
		if ($objEncounter->Is_Discharged($encounter_nr)){
			if ($encounter_type==1){
				# Clinical Cover Sheet for ER patient
				$formToPrint = "<a href=\"".$root_path."../../modules/registration_admission/show_er_clinical_form.php?encounter_nr=$encounter_nr\" target=_blank>ER Clinical Form Sheet</a>";
			}elseif ($encounter_type==2){
				# Clinical Cover Sheet for Outpatient
				$formToPrint = "<a href=\"".$root_path."../../modules/registration_admission/show_opd_clinical_form.php?encounter_nr=$encounter_nr\" target=_blank>OPD Clinical Form Sheet</a>";
			}elseif ($encounter_type==3){
				# Clinical Cover Sheet for Inpatient
				$formToPrint = "<a href=\"".$root_path."../../modules/registration_admission/show_cover_sheet.php?encounter_nr=$encounter_nr\" target=_blank>Inpatient Clinical Cover Sheet</a>";
			}elseif ($encounter_type==4){
				# Clinical Cover Sheet for Inpatient
				$formToPrint = "<a href=\"".$root_path."../../modules/registration_admission/show_cover_sheet.php?encounter_nr=$encounter_nr\" target=_blank>Inpatient Clinical Cover Sheet</a>";
			}
			$objResponse->addAssign("printForm","innerHTML",$formToPrint);
		}
#$objResponse->addAlert("showDiagnosisTherapy : formToPrint = '".$formToPrint."'");
	return $objResponse;
}//end of function

#added by VAN 02-18-09
function updateReceivedDate($encounter_nr, $objvalue){
	global $db, $HTTP_SESSION_VARS;
	$objResponse = new xajaxResponse();
	#$objResponse->alert("data = ".$objvalue['discharged_time']);
	#$objResponse->alert("data2 = ".date("H:i:s",strtotime($objvalue['discharged_time'])));
	$discharged_update = "";
	if ($objvalue['discharged_date']){
		$discharged_update = "  is_discharged = 1,
								discharge_date = '".date("Y-m-d",strtotime($objvalue['discharged_date']))."',
								discharge_time = '".date("H:i:s",strtotime($objvalue['discharged_time']))."',";

		$history = "CONCAT(history,'Update Received and Discharged Date: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n')";
	}else{
		$history = "CONCAT(history,'Update Received: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n')";
	}

	$sql_update = "UPDATE care_encounter SET
						received_date='".date("Y-m-d",strtotime($objvalue['received_date']))."',
						".$discharged_update."
						history = $history,
						modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
						modify_time = '".date('Y-m-d H:i:s')."'
						WHERE encounter_nr='".$encounter_nr."'";

	#$objResponse->alert($sql_update);
	$res_update=$db->Execute($sql_update);

	if ($res_update)
		$objResponse->alert("The patient status is successfully changed.");
	else
		$objResponse->alert("Changing patient's status is failed.");

	$objResponse->addScriptCall("ReloadWindow");

	return $objResponse;
}
#---------------------------

#added by VAN 06-08-09
function cancelDischarged($encounter_nr){
		global $db, $HTTP_SESSION_VARS;
		$ward_obj = new Ward;
		$objResponse = new xajaxResponse();

		#added by janjan for fis integration 08/14/2015
		
		$curl_obj=new Rest_Curl();

		$bed_info = $ward_obj->getLastBedNr($encounter_nr);
		$hasbed = $ward_obj->count;
		#$objResponse->alert($hasbed);
		if ($hasbed){
			$in_ward = 1;
		}else
				$in_ward = 0;

		$discharged_update = "  in_ward = ".$in_ward.",
														is_discharged = 0,
														discharge_date = '',
														discharge_time = '',
														received_date= '',";

		$history = "CONCAT(history,'Cancel Discharge: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n')";

		#undo the discharge in care_encounter
		$sql_update = "UPDATE care_encounter SET
											received_date='".date("Y-m-d",strtotime($objvalue['received_date']))."',
											".$discharged_update."
											history = $history,
											modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
											modify_time = '".date('Y-m-d H:i:s')."'
											WHERE encounter_nr='".$encounter_nr."'";


		#added by VAN 11-03-09
		#undo the care_encounter_location
		$sql_update_loc = "UPDATE care_encounter_location SET
												status='',
												date_to='',
												time_to='',
												discharge_type_nr = 0,
												status = '',
												history = $history,
												modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
												modify_time = '".date('Y-m-d H:i:s')."'
												WHERE encounter_nr='".$encounter_nr."'
												ORDER BY modify_time DESC LIMIT 3";

		#delete the seg_encounter_result
		$sql_update_result = "DELETE FROM seg_encounter_result WHERE encounter_nr='".$encounter_nr."'";

		#delete the seg_encounter_disposition
		$sql_update_disposition = "DELETE FROM seg_encounter_disposition_refer WHERE encounter_nr='".$encounter_nr."'";
		#-------------------------------

		#$objResponse->alert($sql_update);

		$db->BeginTrans();

		#undo the discharge in care_encounter
		$ok=$db->Execute($sql_update);

		#undo the care_encounter_location
		$ok=$db->Execute($sql_update_loc);

		#delete the seg_encounter_result
		$ok=$db->Execute($sql_update_result);

		#delete the seg_encounter_disposition
		$ok=$db->Execute($sql_update_disposition);


		if ($ok){
				
				$curl_obj->customerCancelDischarge($encounter_nr);


				$db->CommitTrans();
				$objResponse->alert("The patient status is successfully changed.");
		}else{
				$db->RollbackTrans();
				$objResponse->alert("Changing patient's status is failed.");
		}

		$objResponse->addScriptCall("ReloadWindow");

		return $objResponse;
}

#Added By Jarel 03-04-2013
function cancelDeath($encounter_nr,$pid){
        global $db, $HTTP_SESSION_VARS;
        $objResponse = new xajaxResponse();

        $history = "CONCAT(history,'Cancel Death: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n')";

        $sql_update = "UPDATE seg_encounter_result SET
                                            result_code = 1,
                                            modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
                                            modify_time = '".date('Y-m-d H:i:s')."'
                                            WHERE encounter_nr='".$encounter_nr."'";


        $sql_update_person = "UPDATE care_person SET
                                                death_date ='0000-00-00',
                                                death_time ='00:00:00',
                                                death_encounter_nr =0,
                                                death_cause = '',
                                                death_cause_code ='',
                                                history = $history,
                                                modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
                                                modify_time = '".date('Y-m-d H:i:s')."'
                                                WHERE pid='".$pid."'";
                                                


        $db->BeginTrans();

        #UPDATE seg_encounter_result
        $ok=$db->Execute($sql_update);

        #update care_person
        $ok=$db->Execute($sql_update_person);


        if ($ok){
                $db->CommitTrans();
                $objResponse->alert("Succesfully cancelled the death.");
        }else{
                $db->RollbackTrans();
                $objResponse->alert("Changing patient's status is failed.");
        }

        $objResponse->addScriptCall("ReloadWindow");

        return $objResponse;    
}

#added by VAS 12-20-2011
function undoCancellation($encounter_nr, $pid){
   global $db, $HTTP_SESSION_VARS;
   $objResponse = new xajaxResponse();

   $sql = "SELECT encounter_nr FROM care_encounter
            WHERE pid = '$pid'
            AND encounter_date > (SELECT encounter_date FROM care_encounter WHERE encounter_nr='$encounter_nr')";
   $rs = $db->Execute($sql);
   $rowcount = $rs->RecordCount();
   
   if ($rowcount==0){
       $history = "CONCAT(history,'Undo Case Cancellation: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n')";

       $sql = "UPDATE care_encounter SET
                                        encounter_status='',
                                        is_discharged=0,
                                        discharge_date=NULL,
                                        discharge_time=NULL,
                                        status='', 
                                        history = $history,
                                        modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
                                        modify_time = '".date('Y-m-d H:i:s')."'
                                        WHERE encounter_nr='".$encounter_nr."'";

       $db->BeginTrans();

       $ok=$db->Execute($sql);


       if ($ok){
          $db->CommitTrans();
          $objResponse->alert("The patient's case record is now available.");
       }else{
          $db->RollbackTrans();
          $objResponse->alert("Deleting of the cancellation patient's case record is failed.");
       }

       $objResponse->addScriptCall("ReloadWindow");
   }else{
       $objResponse->alert("Can't undo the cancellation because there is still a recent case record 
                            with this patient that is not yet discharged.");
   }   

   return $objResponse; 
}

function cancelReceived($encounter_nr){
		global $db, $HTTP_SESSION_VARS;
		$objResponse = new xajaxResponse();

		$history = "CONCAT(history,'Cancel Received Chart: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n')";

		$sql = "UPDATE care_encounter SET
											received_date=NULL,
											history = $history,
											modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
											modify_time = '".date('Y-m-d H:i:s')."'
											WHERE encounter_nr='".$encounter_nr."'";

		$db->BeginTrans();

		$ok=$db->Execute($sql);


		if ($ok){
				$db->CommitTrans();
				$objResponse->alert("The patient's received chart is successfully cancelled.");
		}else{
				$db->RollbackTrans();
				$objResponse->alert("Cancelling of the patient's received chart is failed.");
		}

		$objResponse->addScriptCall("ReloadWindow");

		return $objResponse;
}

//---------notification
function addNotificationCode($encounter_nr, $id, $request_date){
    global $db;
    $cdObj=new Medocs;
    $objResponse = new xajaxResponse();
    
    if($res=$cdObj->getNotificationInfo($id)){
        $row=$res->FetchRow();
        $xcode=strtoupper($row['id']);
        $result=$cdObj->AddNotificationCode($encounter_nr, trim($xcode),$request_date);
        
        if($result){
            $objResponse->addScriptCall("gui_addNotificationCodeRow",$encounter_nr,$xcode,$row['description'],$request_date);
        }else{
            $objResponse->alert("Saving of the recordset failed!");
        }
    }else{
        #$objResponse->alert("No Notification code records exists.");
        #added by VAN 06-06-2013
        $objResponse->addScriptCall("addNotification");
        
    } 
       
    return $objResponse;    
}

function rmvNotificationCode($encounter,$id,$rowno){
    global $db;
    $cdObj=new Medocs;
    $objResponse = new xajaxResponse();
    
    if($res=$cdObj->getNotificationInfo($id)){
        $result=$cdObj->removeNotificationCode($encounter,$id);
        $objResponse->addAssign("notificationCode", "focus()", true);
    }else{
        $objResponse->alert("No record to delete");
    }
    
    if($result){
        $objResponse->addScriptCall("gui_rmvNotificationCodeRow",$rowno);
    }else{
        $objResponse->alert('Error in deleting the notification. '.print_r($cdObj->sql,TRUE));
    }  
    
    return $objResponse;    
}

function populateNotification($encounter_nr){
    global $db;
    $cdObj=new Medocs;
    $objResponse = new xajaxResponse();
    
    //get diagnosis
    $res=$cdObj->getNotificationEnc($encounter_nr);
    
    if($res){
        $dCount = $res->RecordCount();
        if($dCount>0){
            while($row=$res->FetchRow()){
                if($row['is_deleted']!='1'){
                    $objResponse->addScriptCall("gui_addNotificationCodeRow",$encounter_nr,$row['notification_id'],$row['description'],date("m/d/Y", strtotime($row['date_requested'])));
                }
            }// end while statement
        }
    }
    return $objResponse;
}

    
//--------------------------notification
#update Shandy's work by VAN 06-23-2013
function undoMGH($encounter_nr){
  global $db;
  $objEncounter = new Encounter;
  $objResponse = new xajaxResponse(); 
  
  $history = "CONCAT(history,'Undo MGH status: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n')";
   
 // if ($is_maygohome==0){
                  $sql_update = "UPDATE care_encounter SET
                                            is_maygohome='0',
                                            mgh_setdte = '0000-00-00 00:00:00',
                                            history= $history,
                                            modify_id = '".$_SESSION['sess_temp_userid']."',
                                            modify_time = '".date('Y-m-d H:i:s')."'
                                            WHERE encounter_nr=".$db->qstr($encounter_nr);
                
                                            
                                                     
          $db->BeginTrans();               
            //is_maygohome
                //$objResponse->addAlert($sql_update);
                $ok = $db->Execute($sql_update);
               // $objResponse->addAlert('error ='.$db->ErrorMsg());
                
   
       if ($ok){
          #added by VAN 06-23-2013
          #update Shandy's work on undo mgh task
          #check if encounter has a saved Bill
          $sql_bill = "SELECT * FROM seg_billing_encounter 
                        WHERE encounter_nr=".$db->qstr($encounter_nr)."
                        AND is_final=1
                        ORDER BY bill_dte DESC LIMIT 1";
          $bill = $db->GetRow($sql_bill);              
          
          $with_bill_update = 0;
          
          if ($bill['bill_nr']){
              $sql_update = "UPDATE seg_billing_encounter SET
                                            is_final='0',
                                            modify_id = '".$_SESSION['sess_user_name']."',
                                            modify_dt = '".date('Y-m-d H:i:s')."'
                                            WHERE bill_nr=".$db->qstr($bill['bill_nr']); 
              
              $ok = $db->Execute($sql_update);
              
              if ($ok)
                $with_bill_update = 1;
          }              
           
          $db->CommitTrans();
          //$objResponse->addAlert(print_r($encounter_nr,1));
          
          if ($with_bill_update)
            $add_caption = "\nIt has a saved FINAL Bill and this bill was set to NOT FINAL.";
            
          $objResponse->alert("The patient's MGH status was successfully cancelled. ".$add_caption);
       }else{
          $db->RollbackTrans();
          $objResponse->alert("querry error.");
       }
           $objResponse->addScriptCall("ReloadWindow"); 
           return $objResponse;
 } 

 #update Shandy's work by VAN 06-23-2013
 #added by shandy 05-21-2013 for undo MGH
 function undoIsfinal($encounter_nr){
  global $db;
  $objEncounter = new Encounter;
  $objResponse = new xajaxResponse(); 
  
 // if ($is_maygohome==0){
                $sql_update = "UPDATE seg_billing_encounter SET
                                            is_final='0',
                                            modify_id = '".$_SESSION['sess_user_name']."',
                                            modify_dt = '".date('Y-m-d H:i:s')."'
                                            WHERE encounter_nr=".$db->qstr($encounter_nr); 
                $ok = $db->Execute($sql_update);
   
       if ($ok){
         $db->CommitTrans();
          //$objResponse->alert("This Patient is UNDO Final bill.");
       }else{
          $db->RollbackTrans();
          #$objResponse->alert("Field.");
       }
             
           return $objResponse;
 } 
 
 
  //addede by shandy
  function InsertNotificationCode($encounter_nr, $description, $date_request){
        global $db;
        $cdObj=new Medocs;
        $objResponse = new xajaxResponse();
        
        #updated VAN 06-06-2013 
        $sql_uid = "SELECT UUID() id";
        $id = $db->GetOne($sql_uid);
        $ok = $cdObj->AddNotification($id, $description);
        
        if ($ok){
             $objResponse->alert("Data has been successfully saved.");
             #added by VAN 06-06-2013
             #update details of the patient
             $objResponse->addScriptCall("prepareAddNotificationCode",$encounter_nr,$id,$date_request);
        }else{
             $objResponse->alert("Saving of the recordset failed!");
        }     
        return $objResponse;  
     }

$xajax->processRequests();
?>