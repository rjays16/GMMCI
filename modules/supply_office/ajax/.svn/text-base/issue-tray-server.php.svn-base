<?php
    define('__DEBUG_MODE',1);
    function populateIssueProductList($sElem,$page,$keyword,$discountID=NULL,$area=NULL,$disable_qty=false,$filter=NULL,$areadest=NULL) {
        global $db;
        $dbtable='care_pharma_products_main';
        $prctable = 'seg_pharma_prices';
        $objResponse = new xajaxResponse();
        $pc = new SegPharmaProduct();
        $exparray = "";
        $expcounter = 0;

        $maxRows = 10;
        $offset = $page * $maxRows;        

        $ergebnis = $pc->search_products_for_issuance_tray($keyword, $discountID, $area, $offset, $maxRows, $filter, $areadest);

        if ($ergebnis) {
            $total = $pc->FoundRows();
            
            $rows=$ergebnis->RecordCount();
            
            $objResponse->addScriptCall("clearList","item-list");
            
            while($result=$ergebnis->FetchRow()) {
                $exparray = "";
                $details->id = $result["bestellnum"];
                $details->name = $result["artikelname"];
                $details->desc = $result["generic"];
                $details->d = $result["dprice"];
                $details->soc = $result["is_socialized"];
                
                if($filter=='M'){
                    $expSQL = "SELECT DISTINCT expiry_date FROM seg_expiry_inventory WHERE item_code='".$result["bestellnum"]."'";
                    //echo $expSQL;
                    $expresult = $db->Execute($expSQL);
                    $expcounter = $db->Affected_Rows();
                    if($expcounter > 0){
                        while($exprow = $expresult->FetchRow()) {
                            $exparray .= "<option value='".$exprow['expiry_date']."'>".$exprow['expiry_date']."</option>\n";
                        }
                        
                    }
                }
                $details->exparray = $exparray;
                $exparray = ""; 
                //echo $exparray."<br>";
                
                $unameSQL = "SELECT a.item_code,b.unit_name,b.unit_id,b.is_unit_per_pc FROM seg_item_extended as a,seg_unit as b WHERE (a.item_code = '".$result["bestellnum"]."' AND a.pack_unit_id=b.unit_id)";  
                
                $resultname = $db->Execute($unameSQL);
                
                if($rowuname = $resultname->FetchRow()){
                
                    $details->unitdesc =  $rowuname["unit_name"];
                    $details->unitid = $rowuname["unit_id"];
                    $details->perpc = $rowuname["is_unit_per_pc"]; 
                }
                else{
                    $details->unitdesc = "piece"; 
                    $details->unitid = 2;
                    $details->perpc = 1;  
                }
                $rowacheck = 0;                
                #$mysql = "SELECT a.qty_per_pack,b.unit_id,b.unit_desc,b.is_unit_per_pc,c.requested_qty,c.served_qty FROM seg_item_extended as a,seg_unit as b,seg_requests_served as c WHERE (a.item_code = '".$result["bestellnum"]."' AND a.pack_unit_id=b.unit_id AND a.item_code=c.item_code)";
                $mysql = "SELECT a.qty_per_pack,b.unit_id,b.unit_desc,b.is_unit_per_pc,c.served_qty FROM seg_item_extended as a,seg_unit as b,seg_requests_served as c,seg_internal_request as d WHERE (a.item_code = '".$result["bestellnum"]."' AND a.pack_unit_id=b.unit_id AND a.item_code=c.item_code AND c.request_refno=d.refno AND d.area_code_dest='$area' AND d.area_code='$areadest')";
                //$mysql = "SELECT a.qty_per_pack,b.unit_id,b.unit_desc,b.is_unit_per_pc FROM seg_item_extended as a,seg_unit as b,seg_internal_request_details as c,seg_internal_request as d WHERE (a.item_code = '".$result["bestellnum"]."' AND a.pack_unit_id=b.unit_id AND a.item_code=c.item_code AND c.refno=d.refno AND d.area_code_dest='$area' AND d.area_code='$areadest')"; 
                /*
                $mysql = "SELECT DISTINCT a.item_code,a.qty_per_pack,b.unit_id,b.unit_desc,b.is_unit_per_pc,c.served_qty FROM seg_internal_request_details as d 
                            JOIN seg_item_extended as a ON d.item_code=a.item_code
                            JOIN seg_unit as b ON a.pack_unit_id=b.unit_id
                            LEFT JOIN seg_requests_served as c ON d.item_code=c.item_code
                            JOIN seg_internal_request as e ON d.refno=e.refno
                                WHERE (d.item_code = '".$result["bestellnum"]."' AND e.area_code_dest='$area' AND e.area_code='$areadest')";
                */
                $resulta = $db->Execute($mysql);
                $pending_commulative = 0;
                if($rowa=$resulta->FetchRow()){
                    $details->qtyperpack = $rowa['qty_per_pack'];
                    
                    $refnocounter=0;
                    $pending_commulative = 0;
                    //$fetchRequestedRefno = "SELECT DISTINCT a.request_refno FROM seg_requests_served as a, seg_internal_request as b WHERE (a.request_refno=b.refno AND a.item_code = '".$result["bestellnum"]."' AND b.area_code_dest='$area' AND b.area_code='$areadest')";
                    $fetchRequestedRefno = "SELECT a.refno FROM seg_internal_request_details as a JOIN seg_internal_request as b ON a.refno=b.refno WHERE (a.item_code = '".$result["bestellnum"]."' AND b.area_code_dest='$area' AND b.area_code='$areadest')";
                    /*
                    $fetchRequestedRefno = "SELECT DISTINCT b.refno FROM seg_internal_request_details as a 
                                                JOIN seg_internal_request as b ON a.refno=b.refno 
                                                WHERE ( a.item_code = '".$result["bestellnum"]."' AND b.area_code_dest='$area' AND b.area_code='$areadest')";                      
                    */
                    $resultRequestedRefno = $db->Execute($fetchRequestedRefno);
                    while($rowRequestedRefno=$resultRequestedRefno->FetchRow()) {
                        $refnocounter++; 
                        #$mysql = "SELECT a.qty_per_pack,b.unit_id,b.unit_desc,b.is_unit_per_pc,c.requested_qty,c.served_qty FROM seg_item_extended as a,seg_unit as b,seg_requests_served as c WHERE (a.item_code = '".$result["bestellnum"]."' AND a.pack_unit_id=b.unit_id AND a.item_code=c.item_code)";
                         //$mysql = "SELECT a.qty_per_pack,c.request_refno,c.item_code,c.served_qty,d.item_qty,d.unit_id,b.is_unit_per_pc FROM seg_item_extended as a,seg_unit as b,seg_requests_served as c, seg_internal_request_details as d, seg_internal_request as e WHERE (a.item_code = '".$result["bestellnum"]."' AND d.unit_id=b.unit_id AND a.item_code=c.item_code AND c.request_refno=d.refno AND c.request_refno=e.refno AND e.area_code_dest='$area' AND e.area_code='$areadest' AND d.refno='".$rowRequestedRefno['request_refno']."')";
                        $mysql = "SELECT a.qty_per_pack,c.request_refno,c.item_code,c.served_qty,d.item_qty,d.unit_id,b.is_unit_per_pc FROM seg_item_extended as a,seg_unit as b,seg_requests_served as c, seg_internal_request_details as d, seg_internal_request as e WHERE (a.item_code = '".$result["bestellnum"]."' AND d.unit_id=b.unit_id AND a.item_code=c.item_code AND c.item_code=d.item_code AND c.request_refno=d.refno AND c.request_refno=e.refno AND e.area_code_dest='$area' AND e.area_code='$areadest' AND d.refno='".$rowRequestedRefno['refno']."')";
                        /*$mysql = "SELECT DISTINCT a.item_code,a.qty_per_pack,b.unit_id,b.unit_desc,b.is_unit_per_pc,c.served_qty FROM seg_internal_request_details as d 
                            JOIN seg_item_extended as a ON d.item_code=a.item_code
                            JOIN seg_unit as b ON a.pack_unit_id=b.unit_id
                            LEFT JOIN seg_requests_served as c ON d.item_code=c.item_code
                            JOIN seg_internal_request as e ON d.refno=e.refno
                                WHERE (d.item_code = '".$result["bestellnum"]."' AND e.area_code_dest='$area' AND e.area_code='$areadest' AND d.refno='".$rowRequestedRefno['request_refno']."')";
                        */
                    
                        $resulta = $db->Execute($mysql);
                        
                        $totalserved_qty = 0;
                        $flagforRefno = 0;
                        
                        if(!$resulta->EOF){
                        
                            while($rowa=$resulta->FetchRow())
                            {   
                                //echo "sulod";
                                /*
                                $fetchAllServed = "SELECT served_qty from seg_request_served WHERE (request_refno=".$rowa['request_refno']." AND item_code=".$rowa['item_code'].")";
                                $resultAllServed = $db->Execute($fetchAllServed);
                                if($db->Affected_Rows()) {
                                    while($rowAllServed = $resultAllServed->FetchRow()){
                                    $totalserved_qty += $rowAllServed['served_qty']; 
                                    }
                                }
                                */
                                /* temporariy commented out by Bryan 010509*/
                                $totalserved_qty = $rowa['served_qty'];
                                $requested_qty = $rowa["item_qty"];
                                
                                if($rowa["is_unit_per_pc"]=='0'){
                                    $requested_qty = $requested_qty * $rowa["qty_per_pack"]; 
                                }
                                if($requested_qty > $totalserved_qty) 
                                {
                                    if($flagforRefno==0){
                                        $pending_commulative += ($requested_qty - $totalserved_qty);
                                    }
                                    else {
                                        $pending_commulative -= $totalserved_qty;
                                    }
                                }
                                /**/
                                $flagforRefno++;
                            }
                        }
                        
                        else {
                            $mysql3 = "SELECT * FROM seg_internal_request_details as a 
                                            JOIN seg_item_extended as b ON a.item_code=b.item_code
                                            JOIN seg_internal_request as c ON a.refno=c.refno 
                                            WHERE (a.item_code = '".$result["bestellnum"]."' AND c.area_code_dest='$area' AND c.area_code='$areadest' AND a.refno='".$rowRequestedRefno['refno']."')";
                            
                            $result3 = $db->Execute($mysql3);
                            if($row3=$result3->FetchRow()){
                                 $totalserved_qty = 0;

                                $requested_qty = $row3["item_qty"];
                                
                                if($rowa["is_unitperpc"]=='0'){
                                    $requested_qty = $requested_qty * $row3["qty_per_pack"]; 
                                }
                                if($requested_qty > $totalserved_qty) 
                                {
                                    if($flagforRefno==0){
                                        $pending_commulative += ($requested_qty - $totalserved_qty);
                                    }
                                    else {
                                        $pending_commulative -= $totalserved_qty;
                                    }
                                }
                                
                                $flagforRefno++;    
                            }
                        } 
                        #commented out by bryan on dec 2, 2008
                        /*
                        if($rowa["requested_qty"] > $rowa["served_qty"]) {$details->pending = $rowa["requested_qty"] - $rowa["served_qty"];}
                        else {$details->pending = 0;}
                        */
                        $details->pending = $pending_commulative;
                        /*
                        *commented out by bryan nov 17,2008
                        $details->unitid = $rowa["unit_id"];
                        $details->perpc = $rowa["is_unit_per_pc"];   
                        */                   
                    }        
                }
                else {
                     $mysql = "SELECT * from seg_internal_request_details as a JOIN seg_internal_request as b ON a.refno=b.refno WHERE (a.item_code = '".$result["bestellnum"]."' AND b.area_code_dest='$area' AND b.area_code='$areadest')";
                
                     $temp = 0;
                     $details->pending = 0;
                     $resulta = $db->Execute($mysql);   
                     while($rowa=$resulta->FetchRow()){
                        $temp = $rowa['item_qty'];
                     
                         if($rowa["is_unitperpc"]=='0'){
                             $mysql2 = "SELECT * from seg_item_extended WHERE item_code='".$result["bestellnum"]."'";
                             $result2 = $db->Execute($mysql2);
                             if($row2=$result2->FetchRow()){
                                $temp = $temp * $row2["qty_per_pack"];
                             }         
                         }
                         $details->pending += $temp;
                     }
                     $details->qtyperpack = "";
                     /*
                     *commented out by bryan nov 17,2008
                     $details->unitid = 2;
                     $details->perpc = 1;
                     */  
                }
                if($details->pending <= 0) {
                    $total--;
                    continue;
                }                              
                $objResponse->addScriptCall("addProductToList","item-list",$details);
            }
            $lastPage = floor($total/$maxRows);
            
            if ($page > $lastPage) $page=$lastPage;
            
            if($total == 0){
                $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
                $objResponse->addScriptCall("clearList","item-list");
                $objResponse->addScriptCall("addProductToList","item-list",NULL);
            }
            
            $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total); 
        }
        else {
            if (defined("__DEBUG_MODE"))
                $objResponse->addScriptCall("display",$sql);
            else
                $objResponse->addAlert("A database error has occurred. Please contact your system administrator..." . $db->ErrorMsg());
        }
        if (!$rows) {
            $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
            $objResponse->addScriptCall("clearList","item-list");
            $objResponse->addScriptCall("addProductToList","item-list",NULL);
        }
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }
        return $objResponse;
    }
    
    
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');    
    require($root_path.'include/care_api_classes/class_pharma_product.php');
    require($root_path.'include/care_api_classes/class_discount.php');
    require($root_path."modules/supply_office/ajax/issue-tray-common.php");
    $xajax->processRequests();    
?>
