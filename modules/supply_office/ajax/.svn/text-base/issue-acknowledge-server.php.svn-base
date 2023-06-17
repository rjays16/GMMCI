<?php
#created by Bryan on November 18,2008

    function populateIssueAcknowledge($refno, $issdate, $srcarea, $area, $authid, $issid, $srcareaname, $areaname, $authidname, $issidname) {
        global $db;
        $objResponse = new xajaxResponse();
        $x=0;
        # Later: Put this in a Class
        if (!is_array($refno)) $refno = array($refno);
        if (!is_array($issdate)) $issdate = array($issdate);
        if (!is_array($srcarea)) $srcarea = array($srcarea);
        if (!is_array($area)) $area = array($area);
        if (!is_array($authid)) $authid = array($authid);
        if (!is_array($issid)) $issid = array($issid);
        
        if (!is_array($srcareaname)) $srcareaname = array($srcareaname); 
        if (!is_array($areaname)) $areaname = array($areaname);
        if (!is_array($authidname)) $authidname = array($authidname); 
        if (!is_array($issidname)) $issidname = array($issidname);
        
                                           
        foreach ($refno as $i=>$refno) {
            //$objResponse->call("clearIssue",NULL);
        
            $obj = (object) 'details';
            
            $obj->refno = $refno;
            $obj->issdate = $issdate[$i];
            $obj->srcarea= $srcarea[$i];
            $obj->area = $area[$i];
            $obj->authid = $authid[$i];
            $obj->issid = $issid[$i];
            /*
            $sql = "SELECT area_name FROM seg_areas WHERE area_code='".$obj->srcarea;
            $result = $db->Execute($sql);
            $row = $result->FetchRow();
            */
            
            $obj->srcareaname = $srcareaname[$i];
            $obj->areaname = $areaname[$i];
            $obj->authidname = $authidname[$i];
            $obj->issidname = $issidname[$i];
            
            $objResponse->call("appendTheIssuanceList", NULL, $obj);  
        }
        return $objResponse;
    }
    
    function populateIssueDetailsAck($itemcode, $qty, $unitid, $perpc, $serial, $expiry, $itemname, $unitname) {
        global $db;
        $objResponse = new xajaxResponse();
        $x=0;
        # Later: Put this in a Class
        if (!is_array($itemcode)) $itemcode = array($itemcode);
        if (!is_array($qty)) $qty = array($qty);
        if (!is_array($unitid)) $unitid = array($unitid);
        if (!is_array($perpc)) $perpc = array($perpc);
        if (!is_array($serial)) $serial = array($serial);
        if (!is_array($expiry)) $expiry = array($expiry);
        
        if (!is_array($itemname)) $itemname = array($itemname);
        if (!is_array($unitname)) $unitname = array($unitname);

        foreach ($itemcode as $i=>$itemcode) {
            //$objResponse->call("clearIssue",NULL);
        
            $obj = (object) 'details';
            
            $obj->itemcode = $itemcode;
            $obj->qty = $qty[$i];
            $obj->unitid= $unitid[$i];
            $obj->perpc = $perpc[$i];
            $obj->serial = $serial[$i];
            $obj->expiry = $expiry[$i];
            
            $obj->itemname = $itemname[$i];
            $obj->unitname = $unitname[$i];
            
            $objResponse->call("appendTheIssuanceDetailsList", NULL, $obj);   
        }
        return $objResponse;
    }
    

    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'modules/supply_office/ajax/issue-acknowledge-common.php');
    
    $xajax->processRequest();
?>


