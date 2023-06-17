<?php
    function populateMedandSupplyList($sElem,$page,$keyword) {
        global $db;
        
        $objResponse = new xajaxResponse();
        $pc = new SegPharmaProduct();

        $maxRows = 10;
        $offset = $page * $maxRows;

        $ergebnis = $pc->search_products_for_tray($keyword, $discountID, $area, $offset, $maxRows);
        #$objResponse->call("display",$pc->sql);
        #return $objResponse;
        if ($ergebnis) {
            $total = $pc->FoundRows();
            $lastPage = floor($total/$maxRows);
            
            if ((floor($total%$maxRows))==0)
                $lastPage = $lastPage-1;
                        
            if ($page > $lastPage) $page=$lastPage;
                        
            $rows=$ergebnis->RecordCount();

            $objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
            $objResponse->call("clearList","pharma_items-list");
            
            while($result=$ergebnis->FetchRow()) {
                $details->id = $result["bestellnum"];
                $details->name = $result["artikelname"];
                $details->desc = $result["generic"];
                $details->prodclass = $result["prod_class"];
                $details->uprice = $result["price_charge"];
                $details->qty = 1;
                $objResponse->call("addPharmaItemtoList","pharma_items-list",$details);
            }
        }
        else {
            if (defined("__DEBUG_MODE"))
                $objResponse->call("display",$sql);
            else
                $objResponse->alert("A database error has occurred. Please contact your system administrator..." . $db->ErrorMsg());
        }
        if (!$rows) {
            $objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
            $objResponse->call("clearList","pharma_items-list");
            $objResponse->call("addPharmaItemtoList","pharma_items-list",NULL);
        }
        if ($sElem) {
            $objResponse->call("endAJAXSearch",$sElem);
        }
        return $objResponse;
    }
    
    function getPharma_Areas() {
        global $db;
        
        $objResponse = new xajaxResponse();
        
        $strSQL = "select sa.* from seg_areas as sa ".
                  "      inner join care_department as cd on sa.dept_nr = cd.nr ".
                  "   where name_formal regexp '.*pharma.*|.*supply.*' ".
                  "   order by name_formal";
                  
        if ($result = $db->Execute($strSQL)) {                
            if ($result->RecordCount()) {                
                $objResponse->call("js_ClearOptions", "area_combo");
                $objResponse->call("js_AddOptions","area_combo", "- Select Pharmacy Area -", "-");
                
                while ($row = $result->FetchRow()) 
                    $objResponse->call("js_AddOptions","area_combo", $row["area_name"], $row["area_code"]);
            }
        }        
        
         return $objResponse;
    }

    require('./roots.php');

    require($root_path.'include/inc_environment_global.php');        
    require($root_path.'include/care_api_classes/class_pharma_product.php');  
    require($root_path."modules/billing/ajax/billing-more-pharmaorder.common.php");
    $xajax->processRequest();    
?>
