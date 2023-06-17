<?php        
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    
    require_once($root_path.'include/care_api_classes/inventory/class_unit.php');
    require_once($root_path.'include/care_api_classes/class_globalconfig.php');
    
    require_once($root_path.'modules/inventory/ajax/seg-units.common.php');         
    
    function deleteUnit($unit_id, $unit_name){
        global $db;
        
        $unit_obj = new Unit();
        
        $objResponse = new xajaxResponse();
        
        if ($unit_obj->deleteUnit($unit_id)) { 
            $objResponse->addScriptCall("removeUnit", $unit_id);
            $objResponse->addAlert("The unit ".strtoupper($unit_name)." successfully deleted!"); 
        }
        else
            $objResponse->addAlert("The unit ".strtoupper($unit_name)." cannot be deleted.\n".$unit_obj->getErrorMsg());   
        
        return $objResponse;
    }
    
    $xajax->processRequests();
?>