<?php
    define('__DEBUG_MODE',1);
    
    function updateValueAtHandIss($expiry='0000-00-00',$area='',$item='') {
        global $db;
        $objResponse = new xajaxResponse();
        $date = date("Y-m-d");

        $eodobj = new EODInventory();
        $newqty = $eodobj->getCurrentEODQty($item,$area,$date,$expiry,NULL);

        $objResponse->addScriptcall("changeValueAtHandIss", $newqty, $item, $expiry);
          
        return $objResponse;   
        
        
    }
    
    function updateValueAtHandIssSer($serial='',$area='',$item='') {
        global $db;                            
        $objResponse = new xajaxResponse();
        $date = date("Y-m-d");

        $eodobj = new EODInventory();
        $newqty = $eodobj->getCurrentEODQty($item,$area,$date,'0000-00-00',$serial);
        //$objResponse->addScriptcall("alert",$newqty);
        
        $objResponse->addScriptcall("changeValueAtHandIssSer", $newqty, $item, $serial);
          
        return $objResponse;                
    }


    
    /**
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
    function getPendingQty($request_refno, $itemcode, $src, $dest, $qtyperpk) {
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

    function getRequestQty($itemCode,$request_refno) {
        global $db;
        $sql = "SELECT
                      IFNULL(SUM(served_qty),0) served_qty
                FROM
                  seg_requests_served  WHERE item_code = $itemCode
                  AND request_refno = $request_refno";
                 //echo $sql; exit;
        $result = $db->GetRow($sql);
        if($result) {
            return intval($result['served_qty']);
        }
        return 0;
    }
        
    function populateIssueProductList($sElem,$page,$keyword,$discountID=NULL,$area=NULL,$disable_qty=false,$filter=NULL,$areadest=NULL) {
        global $db;

        //$db->debug = true;
        $dbtable='care_pharma_products_main';
        $prctable = 'seg_pharma_prices';
        $objResponse = new xajaxResponse();
        
        $skuInventory = new SkuInventory();
        $pc = new SegPharmaProduct();
        $inv = new Inventory();
        $adj = new SegAdjustment();
        $unit_obj =  new Unit();     
        $objrqst = new Request();          
        
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
            $ergebnis = $pc->getListofPedingRequisition($area, $areadest);
            //$ergebnis = $pc->search_products_for_issuance_tray($keyword, $discountID, $area, $offset, $maxRows, $filter, $areadest);        
            //die("SQL = ".$pc->sql);
            if ($ergebnis) {
                $total = $pc->FoundRows();
                $requested = $total;

                $rows=$ergebnis->RecordCount();

                $objResponse->addScriptCall("clearList","item-list");

                // Get default units for big unit and small unit of measure ...
                $pc->getDefBigUnitID($def_bigunitid, $def_bigunitnm);
                $pc->getDefSmallUnitID($def_smallunitid, $def_smallunitnm);

                while($result=$ergebnis->FetchRow()) {

                    //Added by EJ 11/07/2014
                       /* $mycheck = $objrqst->checkIfNoRequestPending($result["request_refno"]);
                        if($mycheck == 1){
                            $requested--;
                            $total = $requested;
                            continue;
                        }*/

                    $exparray = "";

                    $details->req_refno = $result["request_refno"];
                    $details->request_date = $result["request_date"];
                    $details->requestor = $result["requestor"];
                    $details->particulars = $result["item_names"];
                    $details->item_codes = explode(',',$result["item_codes"]);
                    $details->area = $area;
                    $details->areadest = $areadest;

                    /* $details->id = $result["bestellnum"];
                    $details->name = $result["artikelname"];
                    $details->desc = $result["generic"];
                    $details->d = $result["dprice"];
                    $details->soc = $result["is_socialized"];
                    $rowavg = $pc->getExtendedProductInfo($result["bestellnum"]);   

                   
                    
                    $details->qtyperpack = $rowavg['qty_per_pack'];
                    $details->pck_unitid1   = (is_null($rowavg["pack_unit_id"]) ? $def_bigunitid : $rowavg["pack_unit_id"]);
                    $details->pc_unitid1    = (is_null($rowavg["pc_unit_id"]) ? $def_smallunitid : $rowavg["pc_unit_id"]);
                    $details->pck_unitname1 = $unit_obj->getUnitName($details->pck_unitid1);
                    $details->pc_unitname1  = $unit_obj->getUnitName($details->pc_unitid1); */

                    //$athand = $inv->getInventoryAtHandIncludeDate($result["bestellnum"],$area,$date);

                   /* $prodinfos = $pc->getProductInfo($result["bestellnum"]);

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
                    $details->athand = $athand;                
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
                    
                    $details->perpc = $result["is_unitperpc"]; // added by LST 07.04.2012
                    $details->exparray = $exparray;*/

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
        }
        
        if (!$rows) {
            $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
            $objResponse->addScriptCall("clearList","item-list");
            $objResponse->addScriptCall("addProductToList","item-list",NULL);
        }
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }
        
        if (!($area && $areadest)) {
            $objResponse->addAlert("Properly indicate the Issuing and Requesting Areas!");
        }
        
        return $objResponse;
    }
    
    function populateTypesComboIss() {
        $objResponse = new xajaxResponse();
        $pc = new SegPharmaProduct();
 
        $result = $pc->getTypes();
        if ($result) {                       
            if ($result->RecordCount()) {                
                $objResponse->addScriptCall("js_ClearOptions", "iss_item_type");
                $objResponse->addScriptCall("js_AddOptions","iss_item_type", "- All Types -", "0");
                        
                while($row=$result->FetchRow()) {
                    $objResponse->addScriptCall("js_AddOptions","iss_item_type", $row['name'], $row['prod_class']);  
                }
            }
        }  
        
        return $objResponse;                                         
    }
    
    
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/care_api_classes/inventory/class_sku_inventory.php');
    require_once($root_path.'include/care_api_classes/class_pharma_product.php');
//    require($root_path.'include/care_api_classes/class_discount.php');
    require_once($root_path.'include/care_api_classes/inventory/class_inventory.php'); 
    require_once($root_path.'include/care_api_classes/inventory/class_adjustment.php');      
    //require($root_path.'include/care_api_classes/inventory/class_unit.php');
    require_once($root_path."modules/supply_office/ajax/issue-tray-common.php");
    require_once($root_path."include/care_api_classes/inventory/class_request.php");
    $xajax->processRequests();