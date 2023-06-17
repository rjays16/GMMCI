<?php
//    define('__DEBUG_MODE',1);
    function populateAdjustProductList($sElem, $page, $keyword, $area=NULL, $disable_qty=false, $filter=NULL,$date_asof='') {
        $objResponse = new xajaxResponse();
        $pc = new SegPharmaProduct();
        $inv = new Inventory();
        $adj = new SegAdjustment();
        $exparray = "";
        
        $maxRows = intval(10);
        $offset = $page * $maxRows;     
        
        $def_bigunitid = 0;
        $def_bigunitnm = '';
        $def_smallunitid = 0;
        $def_smallunitnm = '';   
        
        // Get default units for big unit and small unit of measure ...
        $pc->getDefBigUnitID($def_bigunitid, $def_bigunitnm);
        $pc->getDefSmallUnitID($def_smallunitid, $def_smallunitnm);

        $ergebnis = $inv->getItemsinArea($filter, $keyword, $offset, $maxRows);
        if ($ergebnis) {
            $total = $pc->FoundRows();
            
            $rows=$ergebnis->RecordCount();
            
            $objResponse->addScriptCall("clearList","adjust-list");
            
            $skuobj = new SKUInventory();            
            while ($result=$ergebnis->FetchRow()) {
                // var_dump($result);die;
                $reasons = $adj->getAdjustReasonOptions();
                $prodinfos = $pc->getProductInfo($result["bestellnum"]);
                $expiry = ($prodinfos['prod_class']=='M') ? $adj->getExpiryofIteminArea($result["bestellnum"],$area) : '-';
                $serial = $adj->getSerialofIteminArea($result["bestellnum"],$area);                
                if(!$serial) $serial='-';
                
//                $athand = $eodinv->getCurrentEODQty($result["bestellnum"],$area,$date,'0000-00-00','');
                //$athand = $inv->getInventoryAtHand($result["bestellnum"],$area);                
                $exparray = "";

                $details = (object) 'details';
                $details->pck_unitid   = (is_null($result["pack_unit_id"]) ? $def_bigunitid : $result["pack_unit_id"]);
                $details->pc_unitid    = (is_null($result["pc_unit_id"]) ? $def_smallunitid : $result["pc_unit_id"]);
                $details->pck_unitname = (is_null($result["pack_unitname"]) ? $def_bigunitnm : $result["pack_unitname"]);
                $details->pc_unitname  = (is_null($result["pc_unitname"]) ? $def_smallunitnm : $result["pc_unitname"]);
                
                $details->id = $result["bestellnum"];
                $details->name = $result["artikelname"];
                $details->desc = $result["generic"];
                //$details->athand = $athand['t_qty'];
                $skuids = $skuobj->getSKUIds($result["bestellnum"], $area, '', '', $details->pck_unitid);
                $details->pksathand = $skuobj->getQtyofSKUs($skuids);
                
                #added by monmon : avg unit cost per area
                $details->unit_cost = $skuobj->getItemAvgCost($result["bestellnum"],'',$area);

                //$skuids = $skuobj->getItemQty($result["bestellnum"], $date_asof, $area);
                $details->pcsathand = $skuobj->getItemQty($result["bestellnum"], $date_asof, $area);
                
                $details->reasons = $reasons;
                $details->expiry = $expiry;
                $details->serial = $serial;                
                // var_dump($details);die;
                $objResponse->addScriptCall("addToAdjustmentList","adjust-list",$details);
            }
            $lastPage = floor($total/$maxRows);
            
            if ($page > $lastPage) $page=$lastPage;
            
            if($total == 0) {
                $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
                $objResponse->addScriptCall("clearList","adjust-list");
                $objResponse->addScriptCall("addToAdjustmentList","adjust-list",NULL);
            }
            
            $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total); 
        }
        else {
            if (defined("__DEBUG_MODE"))
                $objResponse->addScriptCall("display",$sql);
            else
                $objResponse->addAlert("A database error has occurred. Please contact your system administrator..." . $inv->sql);
        }
        if (!$rows) {
            $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
            $objResponse->addScriptCall("clearList","adjust-list");
            $objResponse->addScriptCall("addToAdjustmentList","adjust-list",NULL);
        }
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }
        return $objResponse;
    }
    
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');    
    require($root_path.'include/care_api_classes/class_pharma_product.php');
    require($root_path.'include/care_api_classes/inventory/class_eodinventory.php');
    require($root_path.'include/care_api_classes/inventory/class_adjustment.php');
    require($root_path.'include/care_api_classes/class_discount.php');
    require($root_path.'include/care_api_classes/inventory/class_inventory.php');
    require($root_path."modules/supply_office/ajax/adjust-tray-common.php");
    $xajax->processRequests();    
?>