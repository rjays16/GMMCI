<?php
    function getItemAvgCost($itm_code) {
        $objResponse = new xajaxResponse();        
        $objitm = new Item();
        $nCost = $objitm->getAvgCost($itm_code); 
        $objResponse->call("assignAvgCost", $nCost);           
        return $objResponse;    
    }
    
    function reset_referenceno() {
        global $db;
        $objResponse = new xajaxResponse();
        
        $objdel = new Delivery();
        $lastnr = $objdel->getLastNr(date("Y-m-d"));

        if ($lastnr)
            $objResponse->call("resetRefNo",$lastnr);
        else
            $objResponse->call("resetRefNo","Error!",1);
        return $objResponse;
    }    

    function goAddItem($items, $unitids, $expiry_dts, $serial_nos, $is_pcs, $qtys, $uprices, $lotnos, $qty_per_pck, $small_unit, $is_fg,$man)  {
        global $db;

        $objResponse = new xajaxResponse();
        $objunit = new Unit();
       
        if (!is_array($items)) $items = array($items);
        if (!is_array($unitids)) $unitids = array($unitids);
        if (!is_array($expiry_dts)) $expiry_dts = array($expiry_dts);
        if (!is_array($serial_nos)) $serial_nos = array($serial_nos);
        if (!is_array($is_pcs)) $is_pcs = array($is_pcs);
        if (!is_array($qtys)) $qtys = array($qtys);
        if (!is_array($uprices)) $uprices = array($uprices);
        if (!is_array($lotnos)) $lotnos = array($lotnos);
        if (!is_array($is_fg)) $is_fg = array($is_fg);
         if (!is_array($man)) $man = array($man);

        foreach ($items as $i=>$item) {
            $strSQL = "select artikelname, generic from care_pharma_products_main as cppm where cppm.bestellnum = '$item'";
            $result = $db->Execute($strSQL);
            
            if ($result) {
                if ($result->RecordCount()) {
                    if ($row = $result->FetchRow()) {
                        $obj = (object) 'details';
                        $obj->id        = $item;
                        $obj->name      = $row["artikelname"];
                        $obj->desc      = $row["generic"];
                        $obj->expiry    = $expiry_dts[$i];                   
                        $obj->serial_no = $serial_nos[$i];
                        $obj->unit      = $unitids[$i];
                        $obj->qty       = $qtys[$i];
                        $obj->unit_name = $objunit->getUnitName($unitids[$i]);
                        $obj->qty_per_pck = $qty_per_pck[$i];
                        $obj->is_perpc  = $is_pcs[$i];   
                        $obj->small_unit = $small_unit[$i];
//                        $obj->uprice    = $uprices[$i];                                                                     
                        $obj->price_add    = $uprices[$i];                                                
                        $obj->lot_no = $lotnos[$i];
                        $obj->is_fg = $is_fg[$i];
                        $obj->man = $man[$i];
                        $objResponse->call("addItemInDelivery", NULL, $obj);                                          
                    }
                }
            }
            else {            
                if (defined('__DEBUG_MODE'))
                    $objResponse->call("display",$strSQL);
                else
                    $objResponse->alert("ERROR: ".$db->ErrorMsg());
            }  
        }          
                        
        return $objResponse;        
    }

    //added by ken 7/16/2014 for getting PO in FIS
    function searchPO($ref){
        $objResponse = new xajaxResponse();
        $curl_obj = new Rest_Curl();
        $unit_obj = new Unit();
        $item_obj = new Item();

        $item = $curl_obj->getPurchaseDetails($ref);
        $info = $curl_obj->getPurchaseInfo($ref);
        $info = json_decode($info, true);
        $item = json_decode($item, true);

        if(!$info && !$item)
            $objResponse->alert('No purchased order found!');
        else{
            $objResponse->call('emptyTray');
            $objResponse->call('add_po_no_data', $ref);
        }

        foreach($item as $key => $values){
            $unit_res = $unit_obj->getInfo($values['units']);

            $obj = (object) 'details';
            $obj->po_detail = $values['po_detail_item'];
            $obj->id = $values['item_code'];
            $obj->name = $values["description"];    
            $obj->unit_name = $values['units'];
            $obj->oqty = $values['quantity_ordered'];
            $obj->qty = $values['quantity_ordered'] - $values['quantity_received'];
            // $obj->price_add = $item_obj->getSuppPrice($values['item_code']); //$values['unit_price'];
            $obj->unit = $item_obj->getPcUnit($values['item_code']);
            $obj->small_unit = $item_obj->getPcUnit($values['item_code']);
            $obj->order = $info['0'];
            $obj->supp_id = $info['1'];
            $obj->loc = $info['6'];
            $obj->del_date = $values['delivery_date'];
            $obj->is_perpc = $unit_res['is_unit_per_pc'] ? true : false;
            $obj->qty_per_pck = ($unit_res['is_unit_per_pc'] ? 1 : $item_obj->getQtyPerBigUnit($values['item_code']));
            $obj->expiry = "";
            $obj->serial_no = "";
            $obj->lot_no = "";
            $obj->manufacturer = "";
            $obj->is_fg = 0;

            if($obj->is_perpc){
                $unit_cost = $values['unit_price'] ;
            }else{
                $unit_cost = $values['unit_price'] / $obj->qty_per_pck;
            }

            $obj->price_add = $unit_cost; //$item_obj->getSuppPrice($values['item_code']); 
            
            if($obj->qty > 0)
                $objResponse->call("addItemInDelivery", NULL, $obj);
        }

        //$objResponse->call('disabledAdd_item');


        return $objResponse;
    }  

    function reset_pono(){
        $objResponse = new xajaxResponse();
        $curl_obj = new Rest_Curl();

        $po_no = $curl_obj->getPoNo();
        $po_no = json_decode($po_no, true);

        $objResponse->call('add_po_no_data', $po_no[0]+1);

        return $objResponse;
    }     
    //ended by ken
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');     
    require_once($root_path.'include/care_api_classes/inventory/class_item.php'); 
    require_once($root_path.'include/care_api_classes/inventory/class_delivery.php');
    require_once($root_path.'include/care_api_classes/curl/class_curl.php');
    require_once($root_path.'include/care_api_classes/inventory/class_unit.php');
    require_once($root_path.'modules/inventory/ajax/seg-delivery.common.php');        
    $xajax->processRequest();
?>