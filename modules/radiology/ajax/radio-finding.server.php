<?php

	function setRadioStatus($batch_nr, $status, $unified=0, $service_date='0000-00-00'){
			global $db;

			$objResponse = new xajaxResponse();

			if (($service_date)&&($service_date!='0000-00-00'))
				$service_date = date("Y-m-d",strtotime($service_date));

			if ($unified){
				$sql = "UPDATE care_test_request_radio SET status='$status',
									service_date = '$service_date'
									WHERE batch_nr='$batch_nr' AND status!='deleted'";
			}else{
				$sql = "UPDATE care_test_request_radio SET status='$status'
									WHERE batch_nr='$batch_nr' AND status!='deleted'";
			}
			#$objResponse->alert($sql);
			$res = $db->Execute($sql);

			return $objResponse;
	}

    function setDoctorNr($batch_nr,$finding_nr,$sendoc,$jundoc,$condoc){
        $objResponse = new xajaxResponse();
        $radio_obj = new SegRadio;
        
        if ($radio_obj->hasBatchNR($batch_nr,$finding_nr)){
            return updateDoctorNr($batch_nr,$finding_nr,$sendoc,$jundoc,$condoc);
        }
        
        if($radio_obj->SaveDoctorNR($batch_nr,$finding_nr,$sendoc,$jundoc,$condoc)){
            $objResponse->addScriptCall("setDoctorOnLoad");
        }
        
        return $objResponse;
        
    }
    
    function updateDoctorNr($batch_nr,$finding_nr,$sendoc,$jundoc,$condoc){
         $objResponse = new xajaxResponse();
         $radio_obj = new SegRadio;
         
         if($radio_obj->UpdateDoctorNr($batch_nr,$finding_nr,$sendoc,$jundoc,$condoc)){
            $objResponse->addScriptCall("setDoctorOnLoad"); 
         }
         return $objResponse;    
    }
        
    function getRadioDoctor($role,$nr){
        global $date_format,$HTTP_SESSION_VARS;

        $objResponse = new xajaxResponse();
        $radio_obj = new SegRadio; 
        if ($doctors=&$radio_obj->getAllRadioDoctor($role,$nr)){
                $objResponse->addScriptCall("ajxClearOptions"); 
                $objResponse->addScriptCall("ajxAddOption","-Select a Doctor-",0);
                while($row = $doctors->FetchRow()){
                    $personell_nr = $row['personell_nr'];
                    $dr_name = $row['dr_name'];
                    
                $objResponse->addScriptCall("ajxAddOption",mb_strtoupper(trim($dr_name)),mb_strtoupper(trim($personell_nr)));   
                }
            }else{
                $objResponse->addScriptCall("ajxClearOptions"); 
                $objResponse->addScriptCall("ajxAddOption","-No Doctor Available-",0);
            }
          
            return $objResponse;  
    }

    function saveAddImpression($addImp,$refno,$grpcode,$service_code){
    	global $db;
    	$radio_obj = new SegRadio;
    	$objResponse = new xajaxResponse();
    	$groupinfo = $radio_obj->getRadioServiceGroupInfo($service_code);

    	if($groupinfo['department_nr']==167){
    		$sql="UPDATE seg_radio_ct_history ".
                        " SET add_impression='$addImp' ".
                        " WHERE refno='$refno' AND group_code='$grpcode'";
        }

        if($groupinfo['department_nr']==208){
        	$sql="UPDATE seg_radio_mri_history ".
                        " SET add_impression='$addImp' ".
                        " WHERE refno='$refno' AND group_code='$grpcode'";
        }

       $add_impression = $db->Execute($sql);
       return $objResponse;

    }

	function saveRadioFinding($batch_nr,$findings_nr,$findings,$radio_impression,$findings_date,$doctor_in_charge){
		global $date_format,$HTTP_SESSION_VARS;

		$objResponse = new xajaxResponse();
		$radio_obj = new SegRadio;

		if ($radio_obj->batchNrHasRadioFindings($batch_nr)){
				 # redirect to update operation
			return updateRadioFinding($batch_nr,$findings_nr,addslashes(trim($findings)),addslashes(trim($radio_impression)),$findings_date,$doctor_in_charge,1);
		}

		$findingArray = array();
		$findingArray['batch_nr'] = $batch_nr;
		$findingArray['findings'] = array($findings);
		$findingArray['findings_date'] = @formatDate2STD($findings_date, $date_format);# reformat FROM mm/dd/yyyy TO yyyy-mm-dd
		$findingArray['findings_date'] = array($findingArray['findings_date']);
		$findingArray['doctor_in_charge'] = array($doctor_in_charge);

		$findingArray['radio_impression'] = array($radio_impression);

		$findingArray['findings'] = addslashes(serialize($findingArray['findings']));
		$findingArray['findings_date'] = serialize($findingArray['findings_date']);
		$findingArray['doctor_in_charge'] = serialize($findingArray['doctor_in_charge']);
		$findingArray['radio_impression'] = addslashes(serialize($findingArray['radio_impression']));

		$findingArray['history']="Created : ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n";


		if($radio_obj->saveRadioFindingInfoFromArray($findingArray)){
			$objResponse->addScriptCall("prepareAdd",$findings_date,'');
			$objResponse->addScriptCall("saveUpdateResult",1,'save');
		}else{
			$objResponse->addScriptCall("saveUpdateResult",0,'save');

		}

		return $objResponse;
	}#end of function saveRadioFinding


	function updateRadioFinding($batch_nr,$findings_nr,$findings,$radio_impression,$findings_date,$doctor_in_charge,$mod=0){
		global $date_format;

		$objResponse = new xajaxResponse();
		$radio_obj = new SegRadio;

		$findings_date = @formatDate2STD($findings_date, $date_format);# reformat FROM mm/dd/yyyy TO yyyy-mm-dd

		#$objResponse->alert(stripslashes($findings));
		if($radio_obj->saveAFinding($batch_nr,$findings_nr,stripslashes($findings),trim($radio_impression),$findings_date,$doctor_in_charge,'Update')){
			#$objResponse->addAlert("radio-finding.server.php van : ".$radio_obj->sql);
			#added/edited  by VAN 07-07-08
			$findingInfo = $radio_obj->getAllRadioFindingsInfo($findings);
			$objResponse->addScriptCall("prepareAdd",@formatDate2Local($findings_date,$date_format),'');
			#$objResponse->addScriptCall("prepareAdd",@formatDate2Local($findings_date,$date_format),$findingInfo['description'],'');

			if ($mod)
				$objResponse->addScriptCall("saveUpdateResult",1,'save');
			else
				$objResponse->addScriptCall("saveUpdateResult",1,'update');
		}else{
			#$objResponse->addAlert("radio-finding.server.php : updateRadioFinding : Failed to saved! ");
			$objResponse->addScriptCall("saveUpdateResult",0,'update');
		}
		return $objResponse;
	}#end of function updateRadioFinding

	function deleteRadioFinding($batch_nr,$f_nr){
		$objResponse = new xajaxResponse();
		$radio_obj = new SegRadio;

		if($radio_obj->deleteAFinding($batch_nr,$f_nr)){
            $radio_obj->deleteDoctorNR($batch_nr,$f_nr+1);
			$objResponse->addScriptCall("emptyIntialFindings",0);
			_populateRadioFinding($objResponse,$batch_nr);
			$objResponse->addScriptCall("msgPopUp","Successfully deleted!");
			$objResponse->addScriptCall("refreshFindingsList");
		}else{
#			$objResponse->addAlert("radio-finding.server.php : deleteRadioFinding : Failed to deleted! ");
			$objResponse->addScriptCall("msgPopUp","Failed to delete!");
		}
#		$objResponse->addAlert("radio-finding.server.php : deleteRadioFinding : radio_obj->sql = '".$radio_obj->sql."' ");
		return $objResponse;
	}#end of function deleteRadioFinding

	function updateRadioFindingStatusServiceDate(&$objResponse,$batch_nr,$service_date,$status='',$mod=0){
		global $date_format;

#		$objResponse = new xajaxResponse();
		$radio_obj = new SegRadio;
		$ok=TRUE;

		if (trim($service_date)!=""){
			$new_service_date = @formatDate2STD($service_date, $date_format);
			if ($radio_obj->updateRadioRequestServiceDate($batch_nr,$new_service_date)){
				#edited by VAN 03-05-08
				if ($mod)
					$objResponse->addScriptCall("msgPopUp","Service date is successfully updated!");
				else
					$objResponse->addScriptCall("msgPopUp","Service date is successfully saved!");
			}else{
				$objResponse->addScriptCall("msgPopUp","Failed to update the service date.");
			}
		}
		#$objResponse->alert($radio_obj->sql);
		if (!empty($status)){
			if ($radio_obj->updateRadioRequestStatus($batch_nr, $status, $new_service_date)){
				$ok=TRUE;
#				$objResponse->addScriptCall("msgPopUp","The status is now for referral!");
			}else{
				$ok=FALSE;
#				$objResponse->addScriptCall("msgPopUp","Failed to change status for referral.");
			}
		}
#$objResponse->addAlert("radio-finding.server.php : updateRadioFindingStatusServiceDate : ok='$ok' \nradio_obj->sql = '".$radio_obj->sql."' ");
		return $ok;
	}#end of function updateRadioFindingStatusServiceDate

#edited by VAN 03-05-08
	#function referralRadioFinding($batch_nr,$service_date){
	function referralRadioFinding($batch_nr,$service_date,$mod=0){

		$objResponse = new xajaxResponse();

#$objResponse->addAlert("radio-finding.server.php : referralRadioFinding : batch_nr='$batch_nr'; service_date='$service_date' ");

		if (updateRadioFindingStatusServiceDate($objResponse,$batch_nr,$service_date,'referral',$mod)){
			$objResponse->addScriptCall("msgPopUp","The status is now for referral!");
			#added by VAN 03-05-08
			$objResponse->addScriptCall("updateServiceDate");
		}else{
			$objResponse->addScriptCall("msgPopUp","Failed to change status for referral.");
		}
		return $objResponse;
	}#end of function referralRadioFinding

#edited by VAN 03-05-08
	//function saveOnlyRadioFinding($batch_nr,$service_date){
	function saveOnlyRadioFinding($batch_nr,$service_date,$mod=0){

		$objResponse = new xajaxResponse();

#$objResponse->addAlert("radio-finding.server.php : saveOnlyRadioFinding : batch_nr='$batch_nr'; service_date='$service_date' ");
		#edited by VAN 03-05-08
		#if (updateRadioFindingStatusServiceDate($objResponse,$batch_nr,$service_date,'')){
		if (updateRadioFindingStatusServiceDate($objResponse,$batch_nr,$service_date,'',$mod)){
#			$objResponse->addScriptCall("msgPopUp","Changes have been successfully saved!");
			#added by VAN 03-05-08
			$objResponse->addScriptCall("updateServiceDate");
		}else{
			$objResponse->addScriptCall("msgPopUp","Failed to save the changes.");
		}
		return $objResponse;
	}#end of function saveOnlyRadioFinding

	function saveAndDoneRadioFinding($batch_nr,$service_date,$mod=0){

		$objResponse = new xajaxResponse();

#$objResponse->addAlert("radio-finding.server.php : saveAndDoneRadioFinding : batch_nr='$batch_nr'; service_date='$service_date' ");

		if (updateRadioFindingStatusServiceDate($objResponse,$batch_nr,$service_date,'done',$mod)){
			$objResponse->addScriptCall("emptyIntialFindings",0);
			_populateRadioFinding($objResponse,$batch_nr);
			$objResponse->addScriptCall("msgPopUp","Changes have been successfully saved!\nThe request is already done!");
			#added by VAN 03-05-08
			$objResponse->addScriptCall("updateServiceDate");
		}else{
			$objResponse->addScriptCall("msgPopUp","Failed to save the changes.");
		}
		return $objResponse;
	}#end of function saveAndDoneRadioFinding

	function _populateRadioFinding(&$objResponse,$batch_nr=0, $target=null){
		global $date_format;

		$objRadio = new SegRadio();
		$personell_obj=new Personell;

#$objResponse->addAlert("_populateRadioFinding : batch_nr='".$batch_nr."'");
		$result = $objRadio->getAllRadioInfoByBatch($batch_nr);
#		$objResponse->addAlert("_populateRadioFinding : objRadio->sql='".$objRadio->sql."'");
#$objResponse->addAlert("_populateRadioFinding : result : \n".print_r($result,TRUE));
		if ($result){
#$objResponse->addAlert("_populateRadioFinding : inside 'if ($result)' : result : \n".print_r($result,TRUE));
			#edited by VAN 07-11-08
			$objResponse->addScriptCall("columnHeader",$result['status']);
				#unserialized

			$findings_array = unserialize($result['findings']);
			$findings_date_array = unserialize($result['findings_date']);
			$doctor_in_charge_array = unserialize($result['doctor_in_charge']);
			$radio_impression_array  = unserialize($result['radio_impression']);

			if (is_array($findings_array) && !empty($findings_array)){
				foreach($findings_array as $key_finding => $value_finding){
					$report_doc_name = "";
					if ($report_doc_info = $personell_obj->get_Person_name($doctor_in_charge_array[$key_finding])){
						$report_doc_name = "Dr. ".$report_doc_info['name_first'];
						$report_doc_name.= (trim($report_doc_info['name_2'])? " ".trim($report_doc_info['name_2']):'');
						$report_doc_name.= (trim($report_doc_info['name_middle'])? " ".substr(trim($report_doc_info['name_middle']), 0, 1).".":'');
						$report_doc_name.=" ".$report_doc_info['name_last'];
					}
				$f_nr = $key_finding;
				$findings = $value_finding;
				$r_impression = $radio_impression_array[$key_finding];
				$f_date='';
				if ( (trim($findings_date_array[$key_finding])!='0000-00-00') && (trim($findings_date_array[$key_finding])!=""))
					$f_date = @formatDate2Local($findings_date_array[$key_finding],$date_format);

					$objResponse->addScriptCall("initialFindingsList",$result['batch_nr'],$f_nr,utf8_decode(stripslashes($findings)),
												stripslashes($r_impression), $f_date, $report_doc_name, $result['status'],URL_APPEND,$target);

				}# end of foreach loop
			}else{# end of if-stmt 'if (is_array($findings_array))'
				$objResponse->addScriptCall("emptyIntialFindings",1);
			}
		}else{
			$objResponse->addScriptCall("emptyIntialFindings",1);
		}
	}# end of function _populateRadioFinding

	function populateRadioFinding($batch_nr=0, $target=null){
		global $date_format;

		$objResponse = new xajaxResponse();
		_populateRadioFinding($objResponse,$batch_nr, $target);
		return $objResponse;
	}# end of function populateRadioFinding
    
    function _populateDoctor(&$objResponse,$role,$nr){
    $radio_obj = new SegRadio;  
    if ($doctors=&$radio_obj->getAllRadioDoctor($role,$nr)){
            $objResponse->addScriptCall("ajxClearOptions"); 
            $objResponse->addScriptCall("ajxAddOption","-Select a Doctor-",0);
            while($row = $doctors->FetchRow()){
                $personell_nr = $row['personell_nr'];
                $dr_name = $row['dr_name'];
                $objResponse->addScriptCall("ajxAddOption",mb_strtoupper(trim($dr_name)),mb_strtoupper(trim($personell_nr)));   
            }
        }else{
            $objResponse->addScriptCall("ajxClearOptions"); 
            $objResponse->addScriptCall("ajxAddOption","-No Doctor Available-",0);
        }
    }
    
    function setDoctor($batch_nr,$findings_nr,$role,$Cdoc,$Sdoc,$Jdoc,$onload){
        global $date_format;

        $objResponse = new xajaxResponse();
        $personell_obj=new Personell;
        $radio_obj = new SegRadio;
        
        if($onload == 1){
            if($radio_obj->hasBatchNR($batch_nr,$findings_nr)){
                $docNR = &$radio_obj->getDoctorNR($batch_nr,$findings_nr); 
                $doc_NR = $docNR->Fetchrow();
                $docs[0] =  $doc_NR['con_doctor_nr'];
                $docs[1]=  $doc_NR['sen_doctor_nr'];
                $docs[2]=  $doc_NR['jun_doctor_nr']; 
                $objResponse->addScriptCall("clearDocTray",null);
                    for($x=0;$x<=2;$x++){
                        if($x==0){
                            $pos ="(C)";
                            $objResponse->addScriptCall("hideDoctor",$docs[0],"c");
                        }else if($x==1){
                            $pos ="(S)";
                            $objResponse->addScriptCall("hideDoctor",$docs[1],"s");
                        }else{
                            $pos = "(J)";
                            $objResponse->addScriptCall("hideDoctor",$docs[2],"j");
                        }
                        if($docs[$x] != ''){
                            $personell_nr .= ','.$docs[$x]; 
                            $nr = explode(',',$docs[$x]);
                            foreach($nr as $key => $value){
                                $row_pr=$personell_obj->get_Person_name($value);
                                $dr_name = mb_strtoupper($row_pr['dr_name']).", ".$row_pr['drtitle'];
                                $details->id = $value;
                                $details->dr_name = $dr_name;
                                $details->role = $pos;
                                $details->pos =  mb_strtoupper(trim($row_pr['job_position']));
                                $objResponse->addScriptCall("addDoctorToList","doc-list",$details);
                            }    
                        }
                    }
            }else{
                _populateDoctor($objResponse,'con',null);
                $objResponse->addScriptCall("addDoctorToList","doc-list",null);
            }            
        }else{ 
            $docs[0]=  $Cdoc;
            $docs[1]=  $Sdoc;
            $docs[2]=  $Jdoc;
            $objResponse->addScriptCall("clearDocTray",null);
            for($x=0;$x<=2;$x++){
                if($x==0){
                    $pos ="(C)";                                              
                }else if($x==1){
                    $pos ="(S)";                                               
                }else{
                    $pos = "(J)";                                             
                }
                if($docs[$x] != ''){
                    $personell_nr .= ','.$docs[$x]; 
                    $nr = explode(',',$docs[$x]);
                    foreach($nr as $key => $value){
                        $row_pr=$personell_obj->get_Person_name($value);
                        $dr_name = mb_strtoupper($row_pr['dr_name']).", ".$row_pr['drtitle'];
                        $details->id = $value;
                        $details->dr_name = $dr_name;
                        $details->role = $pos;
                        $details->pos =  mb_strtoupper(trim($row_pr['job_position']));
                        $objResponse->addScriptCall("addDoctorToList","doc-list",$details);
                    }    
                }           
            }
    }
        _populateDoctor($objResponse,$role,substr($personell_nr,1)); 
        return $objResponse;
    } 

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	include_once($root_path.'include/inc_date_format_functions.php');
	# Create radiology object
	require_once($root_path.'include/care_api_classes/class_radiology.php');
	require($root_path.'include/care_api_classes/class_person.php');
	# Create personell object
	include_once($root_path.'include/care_api_classes/class_personell.php');
	require($root_path."modules/radiology/ajax/radio-finding.common.php");
	$xajax->processRequests();
?>