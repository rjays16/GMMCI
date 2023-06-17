<?php
    function populateIssuePersonnelList($sElem,$searchkey,$page,$include_firstname,$include_encounter=TRUE) {
        global $db;
        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('pagin_patient_search_max_block_rows');
        $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
        
        $objResponse = new xajaxResponse();
        
        $offset = $page * $maxRows;
        $total=0;
        
        $result = $db->Execute("SELECT a.pid,b.sex,b.name_first,b.name_last,a.date_exit FROM care_personell as a JOIN care_person as b ON a.pid=b.pid WHERE (a.date_exit>NOW() AND (b.name_last LIKE '%$searchkey%' OR b.name_first LIKE '%$searchkey%'))");
        while($row=$result->FetchRow()){
            $total++;
        }

        $lastPage = floor($total/$maxRows);
        if ($page > $lastPage) $page=$lastPage;
        
        $rows=0;

        $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
        $objResponse->addScriptCall("clearList","person-list");
        $details = (object) 'details';
        
        $result = $db->Execute("SELECT a.nr,b.sex,b.name_first,b.name_last,a.date_exit FROM care_personell as a JOIN care_person as b ON a.pid=b.pid WHERE (a.date_exit>NOW() AND (b.name_last LIKE '%$searchkey%' OR b.name_first LIKE '%$searchkey%'))");
        while($row=$result->FetchRow()){
                
            $details->pid = $row["nr"];
            $details->sex = $row["sex"];
            $details->lname = $row["name_last"];
            $details->fname = $row["name_first"];
            $details->edate = $row["date_exit"];
            
            $objResponse->addScriptCall("addPerson","person-list", $details);
        }

        if (!$rows) $objResponse->addScriptCall("addPerson","person-list",$details);
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }
        return $objResponse;
    }
    
    function populateIssuePersonnelList2($sElem,$searchkey,$page,$include_firstname,$include_encounter=TRUE) {
        global $db;
        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('pagin_patient_search_max_block_rows');
        $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
        
        $objResponse = new xajaxResponse();
        
        $offset = $page * $maxRows;
        $total=0;
        
        
        $result = $db->Execute("SELECT a.pid,b.sex,b.name_first,b.name_last,a.date_exit FROM care_personell as a JOIN care_person as b ON a.pid=b.pid WHERE (a.date_exit>NOW() AND (b.name_last LIKE '%$searchkey%' OR b.name_first LIKE '%$searchkey%'))");
        while($row=$result->FetchRow()){
            $total++;
        }

        $lastPage = floor($total/$maxRows);
        if ($page > $lastPage) $page=$lastPage;
        
        $rows=0;

        $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
        $objResponse->addScriptCall("clearList","person-list");
        $details = (object) 'details';
        
       $result = $db->Execute("SELECT a.nr,b.sex,b.name_first,b.name_last,a.date_exit FROM care_personell as a JOIN care_person as b ON a.pid=b.pid WHERE (a.date_exit>NOW() AND (b.name_last LIKE '%$searchkey%' OR b.name_first LIKE '%$searchkey%'))");
        while($row=$result->FetchRow()){
                
            $details->pid = $row["nr"];
            $details->sex = $row["sex"];
            $details->lname = $row["name_last"];
            $details->fname = $row["name_first"];
            $details->edate = $row["date_exit"];
            
            $objResponse->addScriptCall("addPerson2","person-list", $details);
        }

        if (!$rows) $objResponse->addScriptCall("addPerson2","person-list",$details);
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }
        return $objResponse;
    }

    require('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    require_once($root_path.'classes/adodb/adodb-lib.inc.php');
    require_once($root_path.'include/care_api_classes/class_globalconfig.php');
    require_once($root_path.'include/care_api_classes/class_person.php');
    require_once($root_path."modules/supply_office/ajax/issue-psearch.common.php");
    
    #added by VAN 06-02-08
    require_once($root_path.'include/care_api_classes/class_department.php');
    require_once($root_path.'include/care_api_classes/class_ward.php');
    
    #added by VAN 06-25-08
    require_once($root_path.'include/care_api_classes/class_social_service.php');
    
    $xajax->processRequests();
?>
