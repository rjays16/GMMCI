<?php

    function populateRequestsList($encounter_nr, $page){
        global $db;
        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('pagin_patient_search_max_block_rows');
        $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
        
        $objResponse = new xajaxResponse();
        $encObj = new Encounter();
        $offset = $page * $maxRows;
        
        $ergebnis=$encObj->countEncounterRequests($encounter_nr);
        if($ergebnis)
            $total = $ergebnis->RecordCount();
        else
            $total = 0;
        
        $lastPage = floor($total/$maxRows);
        
        if ((floor($total%10))==0)
            $lastPage = $lastPage-1;
        
        if ($page > $lastPage) $page=$lastPage;
        $rows=0;

        $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
        $objResponse->addScriptCall("clearList","RequestList");
        $ergebnis=$encObj->getEncounterRequests($encounter_nr, $offset);
        if ($ergebnis) {
            $rows=$ergebnis->RecordCount();
            while($result=$ergebnis->FetchRow()) {
                $req_date= $result['req_date'];
                $details = '';
                $i = 0;
                $refno = $result["refno"];
                $type = $result["req_type"];
                $detObj = $encObj->getRequestDetails($refno, $type);
                while($detObj && $det_res = $detObj->FetchRow()){
                    if($i>0)
                        $details .= ", ";
                    $details .= $det_res['item'];
                    $i++;
                }
                switch($type){
                    case 1: $req_type = "Laboratory";
                            break;
                    case 2: $req_type = "Radiology/Ultrasound";
                            break;
                    case 3: $req_type = "Pharmacy";
                            break;
                    case 4: $req_type = "Ward Stock";
                            break;
                    case 5: $req_type = "MISC Services";
                            break;
                    default: $req_type = "Others";
                }
                $objResponse->addScriptCall("addPerson","RequestList",$refno,$req_date,$req_type,$result["req_by"],$details);
            }#end of while
        } #end of if

        if (!$rows) $objResponse->addScriptCall("addPerson","RequestList",NULL);
        $objResponse->addScriptCall("endAJAXSearch",'0');
        
        return $objResponse;
    }

	 require('./roots.php');
	 require($root_path.'include/inc_environment_global.php');    
	 require($root_path."modules/registration_admission/ajax/mode-history.common.php");
	 require_once($root_path.'include/care_api_classes/class_globalconfig.php');
     require_once($root_path.'include/care_api_classes/class_encounter.php');
     require_once($root_path.'include/care_api_classes/class_paginator.php');
    #-------------------------------------
	 $xajax->processRequests();
?>
