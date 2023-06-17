<?php
//    define('__DEBUG_MODE',1);
    function populateProductList($sElem,$page,$keyword,$type) {
        global $db;
        $dbtable='care_pharma_products_main';
        $prctable = 'seg_unit';
        $objResponse = new xajaxResponse();
        $pc = new SegPharmaProduct();
        
        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);

        $glob_obj->getConfig('pagin_patient_search_max_block_rows');
        $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
        $offset = $page * $maxRows;        
        
        $def_bigunitid = 0;
        $def_bigunitnm = '';
        $def_smallunitid = 0;
        $def_smallunitnm = '';
       
        #$ergebnis = $pc->search_products_for_tray($keyword, $discountID, $area, $offset, $maxRows,$filter);
        $ergebnis = $pc->searchItemsForReqstOrIssuance($keyword, $type, $offset, $maxRows);
        
        $total = $pc->FoundRows();
        $lastPage = floor($total/$maxRows);
        if ($page > $lastPage) $page=$lastPage;        
        
        // Get default units for big unit and small unit of measure ...
        $pc->getDefBigUnitID($def_bigunitid, $def_bigunitnm);
        $pc->getDefSmallUnitID($def_smallunitid, $def_smallunitnm);
        
        #$objResponse->addScriptCall("display",$pc->sql);
        #return $objResponse;
        
        if ($ergebnis) {            
            $rows=$ergebnis->RecordCount();

            $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
            $objResponse->addScriptCall("clearList","product-list");
                    
            while($result=$ergebnis->FetchRow()) {
                $details = (object) 'details';
                $details->id = $result["bestellnum"];
                $details->name = $result["artikelname"];
                $details->desc = $result["generic"];
                $details->pck_unitid   = (is_null($result["pack_unit_id"]) ? $def_bigunitid : $result["pack_unit_id"]);
                $details->pc_unitid    = (is_null($result["pc_unit_id"]) ? $def_smallunitid : $result["pc_unit_id"]);
                $details->pck_unitname = (is_null($result["pack_unitname"]) ? $def_bigunitnm : $result["pack_unitname"]);
                $details->pc_unitname  = (is_null($result["pc_unitname"]) ? $def_smallunitnm : $result["pc_unitname"]);
                                                                            
                $objResponse->addScriptCall("addProductToList","product-list",$details);
            }
        }
        else {
            if (defined("__DEBUG_MODE"))
                $objResponse->addScriptCall("display",$sql);
            else
                $objResponse->addAlert("A database error has occurred. Please contact your system administrator..." . $db->ErrorMsg());
        }
        if (!$rows) {
            $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
            $objResponse->addScriptCall("clearList","product-list");
            $objResponse->addScriptCall("addProductToList","product-list",NULL);
        }
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }
        return $objResponse;
    }
    
    function populateTypesCombo() {
        $objResponse = new xajaxResponse();
        $pc = new SegPharmaProduct();
        
        $result = $pc->getTypes();
        if ($result) {                       
            if ($result->RecordCount()) {                
                $objResponse->addScriptCall("js_ClearOptions", "item_type");
                $objResponse->addScriptCall("js_AddOptions","item_type", "- All Types -", 0);
                        
                while($row=$result->FetchRow()) {
                    $objResponse->addScriptCall("js_AddOptions","item_type", $row['name'], $row['nr']);  
                }
            }
        }  
        
        return $objResponse;                                         
    }

    require('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');        
    require_once($root_path.'include/care_api_classes/class_pharma_product.php');
    require_once($root_path.'include/care_api_classes/class_globalconfig.php');
    require($root_path."modules/supply_office/ajax/seg-supply-office-request-tray.common.php");
    $xajax->processRequests();  
?>