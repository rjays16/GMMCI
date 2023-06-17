<?php
	function populateDiagnosisList($encounter_nr, $sElem,$page, $frombilling=0) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$enc_obj=new Encounter;
		$ward_obj=new Ward;
		$dept_obj=new Department;
		$pers_obj=new Personell;

		#$objResponse->addAlert('enc = '.$encounter_nr);

		$offset = $page * $maxRows;
		$searchkey = utf8_decode($searchkey);
//		$total_srv = $enc_obj->countSearchDiagnosisList($encounter_nr, $maxRows,$offset);
        //added by jasper 06/14/2013
        if ($frombilling==1) $maxRows = 20;
		$ergebnis=$enc_obj->SearchDiagnosisList($encounter_nr, $maxRows, $offset, $frombilling);
//		$objResponse->addAlert($enc_obj->sql);
		#$total = $enc_obj->count;
        $total = $enc_obj->FoundRows();
	//	$objResponse->addAlert('total = '.$total);

		$lastPage = floor($total/$maxRows);

		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;
//		$ergebnis=$enc_obj->SearchDiagnosisList($encounter_nr, $maxRows,$offset);
//		$objResponse->addAlert("sql = ".$enc_obj->sql);
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","DiagnosisList");
		
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
				$doctorinfo = $pers_obj->get_Person_name($result['diagnosing_clinician']);
//				$objResponse->addAlert($pers_obj->sql);
				$middleInitial = "";
				if (trim($doctorinfo['name_middle'])!=""){
					$thisMI=split(" ",$doctorinfo['name_middle']);
					foreach($thisMI as $value){
						if (!trim($value)=="")
						$middleInitial .= $value[0];
					}
					if (trim($middleInitial)!="")
					$middleInitial .= ".";
				}

//				$doctor_name = $doctorinfo['name_first']." ".$doctorinfo['name_2']." ".$middleInitial." ".$doctorinfo['name_last'];
				$doctor_name = $pers_obj->concatname((is_null($doctorinfo["name_last"])) ? "" : $doctorinfo["name_last"],
													 (is_null($doctorinfo["name_first"])) ? "" : $doctorinfo["name_first"], $middleInitial);
				$doctor_name = ucwords(strtolower($doctor_name));
				$doctor_name = htmlspecialchars($doctor_name);

				$altdesc = (is_null($result["alt_desc"])) ? "" : $result["alt_desc"];

				if ($result['is_confidential']==1){
					$doctor_name = '<font size=1 color="red"><strong>CONFIDENTIAL</strong></font>';
				}

				$objResponse->addScriptCall("addDiagnosisToList","DiagnosisList",trim($result["diagnosis_nr"]),$result["code"],$result["description"],$doctor_name, 1, $altdesc, $result["type_nr"],$result['alt_code']);
			}#end of while
		} #end of if

		if (!$rows) $objResponse->addScriptCall("addDiagnosisToList","DiagnosisList",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}


	function addCode($encounter,$encounter_type,$xdate,$code,$doc_nr,$create_id, $type, $mp) {
		$cdObj=new Medocs;
		$icdIcpObj=new Icd_Icp($code);
		$objResponse = new xajaxResponse();

		// $objResponse->addAlert($mp);

		if($mp=='icd'){
			$alert_fail = "Saving of the ICD failed!";
			$alert_invalid = "Invalid ICD Code!";
			$list = 'DiagnosisList';
		}else if($mp=='icp'){
			$alert_fail = "Saving of the ICP failed!";
			$alert_invalid = "Invalid ICP Code!";
			$list = 'ProcedureList';
		}else{
			return FALSE;
		}

	   if($rw=$icdIcpObj->getInfo($code)){
			$desc=$rw->FetchRow();
			$xcode = strtoupper($desc['code']);

			$pers_obj=new Personell();

			if ($docinfo = $pers_obj->get_Person_name2($doc_nr)) {
				$doc_name = $pers_obj->concatname((is_null($docinfo["name_last"])) ? "" : $docinfo["name_last"],
												  (is_null($docinfo["name_first"])) ? "" : $docinfo["name_first"],
												  (is_null($docinfo["name_middle"])) ? "" : $docinfo["name_middle"]);
				$dept_nr = (is_null($docinfo["location_nr"])) ? 0 : $docinfo["location_nr"];
			}
			else {
				$doc_name = "";
				$dept_nr = 0;
			}

			$result=$cdObj->AddCode($encounter, $encounter_type, $xdate, trim($xcode), $doc_nr, $dept_nr, $create_id, $mp, $type);

			if($result){
				$objResponse->addScriptCall("addDiagnosisToList", $list, $diagnosis_nr, trim($code), $desc['description'], $doc_name, 1, '', $type,  trim($code));
				if($mp=='icd'){				
					$objResponse->addScriptCall("clearICDFields");
				}else{
					$objResponse->addScriptCall("clearICPFields");
				}
			}else{
				$objResponse->addAlert($alert_fail);
			}
		}else
			$objResponse->addAlert($alert_invalid);

		return $objResponse;
	}// End addCode Function

	// //getProcedureCodes
	// function addICPCode($encounter,$encounter_type,$xdate,$code,$doc_nr,$create_id, $type) {
	// 	$cdObj=new Medocs;
	// 	$icdObj=new Icd_Icp($code);
	// 	$objResponse = new xajaxResponse();

	//    if($rw=$icdObj->getIcpInfo($code)){
	// 		$desc=$rw->FetchRow();
	// 		$xcode = strtoupper($desc['rvs_code']);

	// 		$pers_obj=new Personell();

	// 		if ($docinfo = $pers_obj->get_Person_name2($doc_nr)) {
	// 			$doc_name = $pers_obj->concatname((is_null($docinfo["name_last"])) ? "" : $docinfo["name_last"],
	// 											  (is_null($docinfo["name_first"])) ? "" : $docinfo["name_first"],
	// 											  (is_null($docinfo["name_middle"])) ? "" : $docinfo["name_middle"]);
	// 			$dept_nr = (is_null($docinfo["location_nr"])) ? 0 : $docinfo["location_nr"];
	// 		}
	// 		else {
	// 			$doc_name = "";
	// 			$dept_nr = 0;
	// 		}

	// 		$result=$cdObj->AddCode($encounter, $encounter_type, $xdate, trim($xcode), $doc_nr, $dept_nr, $create_id, 'icd', $type);
	// 		if($result){
	// 			$diagnosis_nr = $cdObj->getLatestDiagnosisNr();
	// 			$objResponse->addScriptCall("addDiagnosisToList", 'DiagnosisList', $diagnosis_nr, trim($code), $desc['description'], $doc_name, 1, '', $type,  trim($code));
	// 			$objResponse->addScriptCall("clearICDFields");
	// 		}else{
	// 			$objResponse->addAlert("Saving of the ICP failed!");
	// 		}
	// 	}else
	// 		$objResponse->addAlert("Invalid ICP Code!");

	// 	return $objResponse;
	// }// End addICPCode Function

	function rmvCode($diagnosis_nr, $create_id){
		$icdObj=new Icd_Icp($code);
		$objResponse = new xajaxResponse();

		if ($result=$icdObj->removeICDCode($diagnosis_nr, $create_id))
			$objResponse->addAlert("Data has been successfully deleted");

		if($result){
			$objResponse->addScriptCall("removeAddedICD",$diagnosis_nr);
		}else{
			$objResponse->addAlert(print_r($icdObj->sql,TRUE));
		}
		return $objResponse;
	}

	// Added by LST -- 08.18.2009
	function saveAltDesc($enc_nr, $code, $desc, $create_id) {
		$objResponse = new xajaxResponse();

		$objmdoc = new Medocs();
		if (!$objmdoc->saveAltDesc($enc_nr, $code, $desc, $create_id)) {
			$objResponse->addAlert("ERROR: ".$objmdoc->sql);
		}

		return $objResponse;
	}

    //added by jasper 06/30/2013
    function saveAltCode($enc_nr, $code, $altcode, $create_id) {
        $objResponse = new xajaxResponse();

        $objmdoc = new Medocs();
        if (!$objmdoc->saveAltCode($enc_nr, $code, $altcode, $create_id)) {
            $objResponse->addAlert("ERROR: ".$objmdoc->sql);
        }

        return $objResponse;
    }
    //added by jasper 06/30/2013

    //added by jasper 04/24/2013
    function updateAltICD($icd10values, $encnr=0) {
        $objResponse = new xajaxResponse();

        $objmdoc = new Medocs();
        $y=0;
        for($i=0;$i<count($icd10values);$i++) {

                if (checkICDExist($encnr, $icd10values[$i]['code'])) {
                    //update seg_encounter_diagnosis
                    if(updateICDEntNr($encnr, $icd10values[$i]['code'], $icd10values[$i]['entry_no'], $icd10values[$i]['alt_code'])){
                    	$y++;
                    }
                    else{
                    	$y--;
                    }

                } else {
                    //insert seg_encounter_diagnosis
                    if(addICD($encnr, $icd10values[$i]['code'], $icd10values[$i]['diag'], $icd10values[$i]['alt_code'])){
                    	$y++;
                    }
                    else{
                    	$y--;
                    }
                }
        }
        if( $y==$i )$objResponse->alert("Sequence Updated!");
        else $objResponse->alert("Failed to update sequence!");
        return $objResponse;
    }

    function checkICDExist($encnr, $code) {
        global $db;

        $strSQL = "SELECT * FROM seg_encounter_diagnosis WHERE encounter_nr = '" . $encnr . "' AND code = '" . $code . "' AND is_deleted = 0";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()>0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    //added by jasper 04/24/2013

    //added by Francis 04/26/2013
    function updateICDEntNr($encnr, $code, $entnr, $alt_code = "") {
        global $db;
        global $HTTP_SESSION_VARS;

        $modID = $HTTP_SESSION_VARS['sess_temp_userid'];


        $strSQL = "UPDATE seg_encounter_diagnosis SET entry_no ='" .$entnr. "', " .
                   "code_alt = '" .$alt_code. "', " .
                   "modify_id = '" .$modID. "', " .
                   "modify_time = NOW() " .
                   "WHERE encounter_nr = '" . $encnr . "' AND code = '" . $code . "'";

        if ($db->Execute($strSQL)) {
            if ($db->Affected_Rows()) {
                return true;
            }
        }else{
            	return false;
        }
    }

    function addICD($encnr, $code, $diag, $alt_code = "") {
        global $db;
        global $HTTP_SESSION_VARS;

        $createID = $HTTP_SESSION_VARS['sess_temp_userid'];
        $diagnosis = str_replace("'", "\'", $diag);


        $strSQL = "INSERT INTO seg_encounter_diagnosis(encounter_nr,code,description,code_alt,is_deleted,modify_id,modify_time,create_id,create_time) ".
        		  "VALUES ('".$encnr."','".$code."','".$diagnosis."', '".$alt_code."','0','$createID',NOW(),'$createID',NOW())";
        if ($db->Execute($strSQL)) {
            if ($db->Affected_Rows()) {
                return true;
            }
        }else{
            	return false;
        }
    }
    //added by Francis 04/26/2013

	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path."modules/billing_new/ajax/billing-icd.common.php");

	require_once($root_path.'include/care_api_classes/class_globalconfig.php');

	require_once($root_path.'include/care_api_classes/class_encounter.php');
	require_once($root_path.'include/care_api_classes/class_ward.php');
	require_once($root_path.'include/care_api_classes/class_department.php');
	require_once($root_path.'include/care_api_classes/class_personell.php');
	require($root_path.'include/care_api_classes/class_medocs.php');
	require($root_path.'include/care_api_classes/class_icd10.php');
	require($root_path.'include/care_api_classes/class_caserate_icd_icp.php');

	$xajax->processRequests();
?>