<?php
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');    
    require_once($root_path.'include/care_api_classes/class_pharma_product.php');    
    require_once($root_path.'modules/supply_office/ajax/seg-item-types.common.php');         
    
    function deleteItemType($type_nr, $type_name) {             
        $objResponse = new xajaxResponse();
        
        $itmobj = new SegPharmaProduct; 
        $stat = $itmobj->delItemType($type_nr);
        if (!$stat)        
            $objResponse->alert($itmobj->getErrorMsg());
        else {
            $objResponse->call("removeItemType", $type_nr); 
            $objResponse->alert("The item type ".strtoupper($type_name)." is successfully deleted."); 
        }
                
        return $objResponse;
    }
    
    $xajax->processRequest();
?>
