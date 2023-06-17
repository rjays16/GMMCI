<?php
    
    /*define('__DEBUG_MODE',1);
    
    function savedServedPatient($refno, $group_id,$is_served, $service_code=''){
        global $db, $HTTP_SESSION_VARS;
        
        $objResponse = new xajaxResponse();
        $srv=new SegLab;
        $objResponse->alert("ajax : refno, code = ".$refno." , ".$group_id);
        
        if ($is_served)
            $date_served = date("Y-m-d H:i:s");
        else
            $date_served = '';
        
        $save = $srv->ServedLabRequest($refno, $group_id, $is_served, $date_served, $service_code);
        #$objResponse->addAlert("sql = ".$srv->sql);
        if ($save){
            #$objResponse->addScriptCall("ReloadWindow");
            $objResponse->call("ReloadWindow");
        }
            
        return $objResponse;
        
    }
    
    function populateLabRequestsList($encounter_nr,$page){
        
        global $db;
        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('pagin_patient_search_max_block_rows');
        $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
        
        $objResponse = new xajaxResponse();
        $enc_obj=new Encounter;
        $ward_obj=new Ward;
        $dept_obj=new Department;
        $pers_obj=new Personell;
        $lab_obj = new Lab_Results;
        
        $offset = $page * $maxRows;
        $searchkey = utf8_decode($searchkey);
        $total_srv = $lab_obj->countSearchLabRequestsList($encounter_nr, $searchkey,$maxRows,$offset);
        $objResponse->addAlert($total_srv);
        $total = $lab_obj->count;
        
        $lastPage = floor($total/$maxRows);
        
        if ((floor($total%10))==0)
            $lastPage = $lastPage-1;
        
        if ($page > $lastPage) $page=$lastPage;
        $ergebnis=$lab_obj->SearchLabRequestsList($encounter_nr, $searchkey,$maxRows,$offset);
        $rows=0;
        
        #$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
        $objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
        #$objResponse->addScriptCall("clearList","historyList");
        $objResponse->call("clearList","historyList");
        if ($ergebnis) {
            $rows=$ergebnis->RecordCount();
            while($result=$ergebnis->FetchRow()) {
                $refno = $result["refno"];
                $date = date("m/d/Y",strtotime($result["service_date"]));
                $service = $result['name'];
                
                #$objResponse->addScriptCall("addAdmissionToList","historyList",$refno,$service, $date);
                $objResponse->call("addAdmissionToList","historyList",$refno,$service, $date);
            }#end of while
            if (!$rows) $objResponse->addScriptCall("addAdmissionToList","historyList",NULL);
        } #end of if
        else
#            $objResponse->addScriptCall("addAdmissionToList","historyList",NULL); 
            $objResponse->call("addAdmissionToList","historyList",NULL); 
        if ($sElem) {
            #$objResponse->addScriptCall("endAJAXSearch",$sElem);
            $objResponse->call("endAJAXSearch",$sElem);
        }
        return $objResponse;
    }
    
    require_once($root_path.'include/care_api_classes/class_department.php');
    require_once($root_path.'include/care_api_classes/class_personell.php');
    require_once($root_path.'include/care_api_classes/class_ward.php');
    
    require_once($root_path.'include/care_api_classes/class_person.php');
    
    require_once($root_path.'include/care_api_classes/class_encounter.php');
    require_once($root_path.'include/care_api_classes/class_lab_results.php'); 

    #$xajax->processRequests();
    $xajax->processRequest();   */
    #define('__DEBUG_MODE',1);
    
    function savedServedPatient($refno, $group_id,$is_served, $service_code=''){
        global $db, $HTTP_SESSION_VARS;
        
        $objResponse = new xajaxResponse();
        $srv=new SegLab;
        #$objResponse->alert("ajax : refno, code = ".$refno." , ".$group_id);
        
        if ($is_served)
            $date_served = date("Y-m-d H:i:s");
        else
            $date_served = '';
        
        $save = $srv->ServedLabRequest($refno, $group_id, $is_served, $date_served, $service_code);
        #$objResponse->addAlert("sql = ".$srv->sql);
        if ($save){
            #$objResponse->addScriptCall("ReloadWindow");
            $objResponse->call("ReloadWindow");
        }
            
        return $objResponse;
        
    }
    
    #added by Raissa 02-08-2010
    function contMonitor($refno, $service_code, $cont) {
        global $db;
        
        $objResponse = new xajaxResponse();
        #$objResponse->alert($refno." ".$service_code." ".$cont);
        
        if($cont=='true'){
            #$sql = "SELECT fn_create_labrequest_monitoring('$refno', '$service_code')";;
            $sql = "SELECT sv.encounter_nr, sv.pid, sv.is_cash, sv.type_charge, sv.is_urgent, sv.is_tpl, sv.is_approved, 
                    sv.create_id, sv.create_dt, sv.comments, sv.ordername, sv.orderaddress, sv.loc_code, sv.walkin_pid,
                    sd.service_code, sd.price_cash, sd.price_cash_orig, sd.price_charge, sd.request_doctor, 
                    sd.request_dept, sd.is_in_house, sd.clinical_info, sd.quantity, sd.monitor_time
                    FROM seg_lab_servdetails AS sd 
                    LEFT JOIN seg_lab_serv AS sv ON sv.refno = sd.refno
                    WHERE sd.refno='$refno' AND sd.service_code='$service_code' AND (NOT(ISNULL(sd.monitor_time)) AND sd.monitor_time>0)";
            $result=$db->Execute($sql);
            if($result && $row = $result->FetchRow()){
                $serv_dt = $row['serv_dt'];
                $serv_tm = $row['serv_tm'];
                $encounter_nr = $row['encounter_nr'];
                $pid = $row['pid'];
                $is_cash = $row['is_cash'];
                $type_charge = $row['type_charge'];
                $is_urgent = $row['is_urgent'];
                $is_tpl = $row['is_tpl'];
                $create_id = $row['create_id'];
                $dte = $row['create_dt'];
                $comments = $row['comments'];
                $ordername = $row['ordername'];
                $orderaddress = $row['orderaddress'];
                $loc_code = $row['loc_code'];
                $walkin_pid = $row['walkin_pid'];
                $service_code = $row['service_code'];
                $price_cash = $row['price_cash'];
                $price_cash_orig = $row['price_cash_orig'];
                $price_charge = $row['price_charge'];
                $request_doctor = $row['request_doctor'];
                $request_dept = $row['request_dept'];
                $is_in_house = $row['is_in_house'];
                $clinical_info = $row['clinical_info'];
                $quantity = $row['quantity'];
                $monitor_time = $row['monitor_time'];
                $sql = "UPDATE seg_lab_servdetails SET monitor_time=0 WHERE refno='$refno' AND service_code='$service_code'";
                $result=$db->Execute($sql);
                $sql = "SELECT fn_get_new_refno_lab() AS refno";
                $result=$db->Execute($sql);
                if($result && $row = $result->FetchRow()){
                    $refno = $row['refno'];
                    $sql = "SELECT ADDTIME('$dte', CONCAT('0 ',$monitor_time,':0:0')) AS date";
                    $result=$db->Execute($sql);
                    if($result && $row = $result->FetchRow()){
                        $create_dt = $row['date'];
                        $history = "Create: ".$create_dt." [".$create_id."]";
                        $serv_dt = substr($create_dt,0,10);
                        $serv_tm = substr($create_dt,11,8);
                        $sql = "INSERT INTO seg_lab_serv (refno, serv_dt, serv_tm, encounter_nr, pid, is_cash, type_charge, is_urgent, is_tpl, is_approved, create_id, create_dt, history, comments, ordername, orderaddress, status, loc_code, walkin_pid)
                            VALUES('$refno', '$serv_dt', '$serv_tm', '$encounter_nr', '$pid', $is_cash, $type_charge, $is_urgent, $is_tpl, '$is_approved', '$create_id', '$create_dt', '$history', '$comments', '$ordername', '$orderaddress', '', $loc_code, '$walkin_pid')";
                        if($result=$db->Execute($sql)){
                            $sql = "INSERT INTO seg_lab_servdetails (refno, service_code, price_cash, price_cash_orig, price_charge, request_doctor, request_dept, is_in_house, clinical_info, quantity, monitor_time)
                                VALUES('$refno', '$service_code', $price_cash, $price_cash_orig, $price_charge, $request_doctor, $request_dept, $is_in_house, '$clinical_info', $quantity, $monitor_time)";
                        }
                    }
                }
            }
        }
        else{
            $sql = "UPDATE seg_lab_servdetails SET monitor_time=0 WHERE refno='$refno' AND service_code='$service_code'";
        }
        #$objResponse->alert($sql);
        if($result=$db->Execute($sql)){
            #$objResponse->alert($sql);
        }
        else
            $objResponse->alert("FAIL ON ".$sql);
        
        return $objResponse;
    }
    
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/care_api_classes/class_globalconfig.php');
    
    require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
    
    require($root_path.'modules/laboratory/ajax/lab-result.common.php');
    
    #
    /*
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');

    
    require($root_path.'include/care_api_classes/inventory/class_eodinventory.php');
    require($root_path.'include/care_api_classes/inventory/class_adjustment.php');
    require($root_path.'include/care_api_classes/inventory/class_unit.php');
    require_once($root_path.'include/care_api_classes/class_department.php');
    require_once($root_path.'include/care_api_classes/class_personell.php');
    require_once($root_path.'modules/supply_office/ajax/adjustment.common.php');*/
    $xajax->processRequest();
?>