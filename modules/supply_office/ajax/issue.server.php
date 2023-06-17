<?php
#added by bryan on Sept 18,2008
#  
    function add_item($items, $item_names, $desc, $pending, $unitid, $perpc, $unitdesc, $expdate=NULL, $serial=NULL, $avg=NULL) {
        global $db;
        $objResponse = new xajaxResponse();
        
        //added by bryan
        $objprod = new SegPharmaProduct();

        # Later: Put this in a Class
        if (!is_array($items)) $items = array($items);
        if (!is_array($item_names)) $item_names = array($item_names);
        if (!is_array($desc)) $desc = array($desc);
        if (!is_array($pending)) $pending = array($pending);
        if (!is_array($unitid)) $unitid = array($unitid);
        if (!is_array($unitdesc)) $unitdesc = array($unitdesc);
        if (!is_array($perpc)) $perpc = array($perpc);
        if (!is_array($expdate)) $expdate = array($expdate);
        if (!is_array($serial)) $serial = array($serial);
        if (!is_array($avg)) $avg = array($avg); 
        
        foreach ($items as $i=>$item) {
             //added by bryan on 091409
             $extendedrow = $objprod->getExtendedProductInfo($item);
        
            #$objResponse->call("clearOrder",NULL);
            $obj = (object) 'details';
            $obj->id = $items[$i];
            $obj->name = $item_names[$i];
            $obj->desc= $desc[$i];
            $obj->pending = $pending[$i];
            $obj->unitid = $unitid[$i];
            $obj->unitdesc = $unitdesc[$i];
            $obj->perpc = $perpc[$i];
            $obj->expdate = $expdate[$i];
            $obj->serial = $serial[$i];
            //$obj->avg = $avg[$i]; 
            $obj->avg = $extendedrow['avg_cost'];  
            $obj->qtyperpack = $extendedrow['qty_per_pack'];                                

            $objResponse->call("appendOrder", NULL, $obj, false, $i);
        }
        return $objResponse;
    }
    
    function getRequestedAreasIss($s_areacode, $r_areacode='') {
         global $db;
        $objResponse = new xajaxResponse();
        
        $objdept = new Department();
        $area = new SegArea();
        $result = $area->getInventoryAreas();

        $count = 0;
        if ($result) {
            foreach($result as $row) {
                $checked=strtolower($row['area_code'])==strtolower($r_areacode) ? 'selected="selected"' : "";
                if($row['area_code'] == $s_areacode)
                    continue;
                $dest_area .= "<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
                if ($checked) $index = $count;
                $count++;
            }
            $dest_area = '<select class="jedInput" name="area_dest" id="area_dest" >'."\n".$dest_area."</select>\n".
                "<input type=\"hidden\" id=\"area3\" name=\"area3\" value=\"".$r_areacode."\"/>"; 
                   
            $objResponse->call("showRequestedAreasIss",$dest_area);
        }  
        
        return $objResponse;
    }  
    
    function getSourceAreasbypidIss($iss_id, $r_areacode='') {
         global $db;
        $objResponse = new xajaxResponse();
        
        $objdept = new Department();
        $objpnl = new Personell();

        $fetchNR = "select nr from care_personell where pid=$iss_id";
        $resultNR = $db->Execute($fetchNR);
        $rowNR = $resultNR->FetchRow();
         
        $deptofpersonnel = $objpnl->getPersonellInfo($rowNR['nr']);
        
        $result = $objdept->getAreasInDept($deptofpersonnel['location_nr']);
        $dest_area = "";
        $count = 0;
        if ($result) {
            if($row=$result->FetchRow())
            {
                $checked=strtolower($row['area_code'])==strtolower($r_areacode) ? 'selected="selected"' : "";
                $dest_area .= "<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
                if ($checked) $index = $count;

                $count++;
                
                $firstfetched = $row['area_code'];

                while($row=$result->FetchRow()){
                    $checked=strtolower($row['area_code'])==strtolower($r_areacode) ? 'selected="selected"' : "";          
                    $dest_area .= "<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
                    if ($checked) $index = $count;

                    $count++;
                }
                $dest_area = '<select class="jedInput" id="area_issued" name="area_issued" onchange="jsRqstngAreaOptionChngIss(this, this.options[this.selectedIndex].value);">'."\n".$dest_area."</select>\n".
                    "<input type=\"hidden\" id=\"area3\" name=\"area3\" value=\"".$r_areacode."\"/>"; 
            } 
                  
            $objResponse->call("showRequestedSRCAreasIss",$dest_area);  
            
            $dest_area = "";
            
            $result = $objdept->getAllAreas($firstfetched);
            $count = 0;
            if ($result) {
                while($row=$result->FetchRow()){
                    $checked=strtolower($row['area_code'])==strtolower($r_areacode) ? 'selected="selected"' : "";
                    $dest_area .= "<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
                    if ($checked) $index = $count;
                    $count++;
                }
                $dest_area = '<select class="jedInput" name="area_dest" id="area_dest" >'."\n".$dest_area."</select>\n".
                    "<input type=\"hidden\" id=\"area3\" name=\"area3\" value=\"".$r_areacode."\"/>"; 
                       
                $objResponse->call("showRequestedAreasIss",$dest_area);
            }  
            
                
        } 
         else {
            $dest_area_no = "<option value=\"\"> No areas for issuing personnel </option>\n"; 
            $dest_area = '<select class="jedInput" id="area_issued" name="area_issued" onchange="jsRqstngAreaOptionChngIss(this, this.options[this.selectedIndex].value);">'."\n".$dest_area_no."</select>\n".
                "<input type=\"hidden\" id=\"area3\" name=\"area3\" value=\"".$r_areacode."\"/>"; 
                
            $objResponse->call("showRequestedSRCAreasIss",$dest_area);
           
        }       

        return $objResponse;
    } 
    
    //Created by EJ 11/04/2014
    function getAvailableQty($id, $area) {
        global $db;
        $date = date("Y-m-d");

        $inv = new Inventory();
        $objResponse = new xajaxResponse();

        $athand = $inv->getInventoryAtHandIncludeDate($id,$area,$date);

        $objResponse->call("displayAvailableStock",$id, $athand);

        return $objResponse;
    }

    //Created by EJ 11/04/2014
    function getPendingQty($id, $refno, $area, $area_dest) {
        global $db;

        $inv = new Inventory();
        $objResponse = new xajaxResponse();

        $pending = $inv->getPendingQty($id, $refno, $area, $area_dest);

        $objResponse->call("displayPendingStock",$id, $pending);

        return $objResponse;
    }

    //Created by EJ 11/04/2014
    function getAvgCost($id, $area, $row) {
        global $db;

        $inv = new Inventory();
        $sku = new SkuInventory();
        $objResponse = new xajaxResponse();

        $avg = $sku->getItemAvgCost($id, '', $area);

        $objResponse->call("displayAvgCost",$id, $row, $avg);

        return $objResponse;
    }

    //Created by EJ 11/04/2014
    function getTotalCost($id, $refno, $area, $area_dest) {
        global $db;

        $inv = new Inventory();
        $sku = new SkuInventory();
        $objResponse = new xajaxResponse();

        $pending = $inv->getPendingQty($id, $refno, $area, $area_dest);
        $avg = $sku->getItemAvgCost($id, '', $area);

        $total = $pending * $avg;
        $objResponse->call("displayTotalCost",$id, $total);

        return $objResponse;
    }

    function reset_referenceno() {
        global $db;
        $objResponse = new xajaxResponse();
        
        $sc = new Issuance();
        $lastnr = $sc->getLastNr(date("Y-m-d"));

        if ($lastnr)
            $objResponse->call("resetRefNo",$lastnr);
        else
            $objResponse->call("resetRefNo","Error!",1);
        return $objResponse;
    }


    function populateIssueProduct($refno, $area, $areadest) {
       global $db;

        $objResponse = new xajaxResponse();
        $dbtable='care_pharma_products_main';
        $prctable = 'seg_pharma_prices';

       
        $skuInventory = new SkuInventory();
        $pc = new SegPharmaProduct();
        $inv = new Inventory();
        $adj = new SegAdjustment();
        $unit_obj =  new Unit();
        $skuobj = new SKUInventory();               
        
        $exparray = "";
        $expcounter = 0;
        $date = date("Y-m-d"); 

        $maxRows = 10;
        $offset = $page * $maxRows; 
        
        $def_bigunitid = 0;
        $def_bigunitnm = '';
        $def_smallunitid = 0;
        $def_smallunitnm = '';       
        
        if ($area && $areadest) {
             $ergebnis = $pc->getListofPendingItemsRefno($refno);
            //$ergebnis = $pc->search_products_for_issuance_tray2($refno, $area, $areadest, $filter=NULL);
            //$objResponse->alert($pc->sql);
            //die("SQL = ".$pc->sql);
            if ($ergebnis) {
                $total = $pc->FoundRows();

                $rows=$total; //$ergebnis->RecordCount();
                //echo 'ewam';
                $objResponse->call("clearList","order-list");

                // Get default units for big unit and small unit of measure ...
                $pc->getDefBigUnitID($def_bigunitid, $def_bigunitnm);
                $pc->getDefSmallUnitID($def_smallunitid, $def_smallunitnm);

                while($result=$ergebnis->FetchRow()) {

                    $exparray = "";
                    $details->id = $result["bestellnum"];
                    $details->name = $result["artikelname"];
                    $details->desc = $result["generic"];
                    $details->d = $result["dprice"];
                    //$details->soc = $result["is_socialized"];
                    $rowavg = $pc->getExtendedProductInfo($result["bestellnum"]);   

                    $details->avg = $skuInventory->getItemAvgCost($result["bestellnum"], '', $area);
                    
                   // $details->avg = $skuInventory->getsupplierprice($result["bestellnum"]);

                    $details->qtyperpack = $rowavg['qty_per_pack'];
                    $details->pck_unitid1   = (is_null($rowavg["pack_unit_id"]) ? $def_bigunitid : $rowavg["pack_unit_id"]);
                    $details->pc_unitid1    = (is_null($rowavg["pc_unit_id"]) ? $def_smallunitid : $rowavg["pc_unit_id"]);
                    $details->pck_unitname1 = $unit_obj->getUnitName($details->pck_unitid1);
                    $details->pc_unitname1  = $unit_obj->getUnitName($details->pc_unitid1); 

                    $details->unitid = $result["unit_id"];
                    $details->unitdesc = $result["unit_name"];
                    $details->perpc = $result["is_unit_per_pc"];

                    $athand = $inv->getInventoryAtHandIncludeDate($result["bestellnum"],$area,$date,$result["unit_id"]);
                    /*$skuids = $skuobj->getSKUIds($result["bestellnum"], $area, '', '', $details->unitid);
                    $athand = $skuobj->getQtyofSKUs($skuids);*/


                    $prodinfos = $pc->getProductInfo($result["bestellnum"]);

                    $expiry = "";
                    $expiry = $adj->getEarliestExpiryofIteminArea($result["bestellnum"],$area); 

                    if($expiry=="")
                        $expiry = '-';

                    $details->expiry = $expiry;

                    if($prodinfos['prod_class']=='E')
                        $serial = $adj->getSerialofIteminArea($result["bestellnum"],$area);
                    else
                        $serial = '-';

                    $details->serial = $serial;                
                    $details->athand = number_format($athand,0);                
                    $details->prod_class = $prodinfos['prod_class'];

                    if($filter=='M' || $filter=='') {
                        $expSQL = "SELECT DISTINCT expiry_date FROM seg_inventory WHERE item_code='".$result["bestellnum"]."' AND area_code='$area' AND expiry_date <> '0000-00-00'";
                        $expresult = $db->Execute($expSQL);
                        $expcounter = $db->Affected_Rows();
                        if($expcounter > 0){
                            while($exprow = $expresult->FetchRow()) {
                                $exparray .= "<option value='".$exprow['expiry_date']."'>".$exprow['expiry_date']."</option>\n";
                            }

                        }
                    }
                    //$details->perpc = $result["is_unitperpc"]; // added by LST 07.04.2012
                    $details->exparray = $exparray;
                    $details->refno = $refno;

                    $details->pending = $result['qty_requested'];//$inv->getPendingQty($result["bestellnum"], $refno, $area, $areadest);
                    /*if($details->pending <= 0) {
                        $total--;
                        continue;
                    }   */
                
                    $objResponse->call("appendOrder","order-list",$details);
                }
                $lastPage = floor($total/$maxRows);

                if ($page > $lastPage) $page=$lastPage;

                if($total == 0){
                    $objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
                    $objResponse->call("clearList","order-list");
                    $objResponse->call("appendOrder","order-list",NULL);
                }

                $objResponse->call("setPagination",$page,$lastPage,$maxRows,$total); 
            }
            else {
                if (defined("__DEBUG_MODE"))
                    $objResponse->call("display",$sql);
                else
                    $objResponse->alert("A database error has occurred. Please contact your system administrator..." . $db->ErrorMsg());
            }
        }
        
        if (!$rows) {
            $objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
            $objResponse->call("clearList","order-list");
            $objResponse->call("appendOrder","order-list",NULL);
        }
        if ($sElem) {
            $objResponse->call("endAJAXSearch",$sElem);
        }
        
        if (!($area && $areadest)) {
            $objResponse->alert("Properly indicate the Issuing and Requesting Areas!");
        }
        
        return $objResponse;
    }

/*    *
    * Gets the quantity not yet served.
     *
     * @access private
     * @param string item code
     * @param string source area
     * @param string destination area
     * @param double quantity per pack
     * @author LST
     * @created 07.04.2012
        */
    function getPendingQty2($request_refno, $itemcode, $src, $dest, $qtyperpk) {
      global $db;
      $strSQL = "SELECT 
                  a.item_qty,
                  a.is_unitperpc
                FROM
                  seg_internal_request_details a
                  INNER JOIN
                  seg_internal_request b
                  ON a.refno = b.refno 
                WHERE (
                    a.item_code = '$itemcode' 
                    AND b.area_code_dest = '$src' 
                    AND b.area_code = '$dest'
                  )";
      $rqty = 0;
      if($buf=$db->Execute($strSQL)) {
          if($buf->RecordCount()) {
            while ($row = $buf->FetchRow()) {
              $tmpq = is_null($row["item_qty"]) ? 0 : $row["item_qty"];
              if (!$row["is_unitperpc"]) {
                $tmpq *= $qtyperpk;
              }
              $rqty += $tmpq;
            }            
          }
      }
      
      return $rqty - getRequestQty($itemcode, $request_refno);
    }
    
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');

    require($root_path.'include/care_api_classes/class_discount.php');
    require($root_path.'include/care_api_classes/class_order.php');
    require_once($root_path.'include/care_api_classes/class_department.php');
    require_once($root_path.'include/care_api_classes/inventory/class_issuance.php');
    require_once($root_path.'include/care_api_classes/class_personell.php');
    require_once($root_path.'include/care_api_classes/class_pharma_product.php');   
    require_once($root_path.'modules/supply_office/ajax/issue.common.php');
    require_once($root_path.'include/care_api_classes/inventory/class_adjustment.php');   
    require_once($root_path.'include/care_api_classes/inventory/class_sku_inventory.php');
    
    $xajax->processRequest();
?>
