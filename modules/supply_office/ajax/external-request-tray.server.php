<?php
    function reset_referenceno() {
        global $db;
        $objResponse = new xajaxResponse();
        
        $rqst_obj = new SegExternalRequest();  
        $lastnr = $rqst_obj->getLastNr(date("Y-m-d"));

        if ($lastnr)
            $objResponse->call("resetRefNo",$lastnr);
        else
            $objResponse->call("resetRefNo","Error!",1);
        return $objResponse;
    }    
    
    function goAddItem($items, $unitids, $is_pcs, $qtys)  {
        global $db;
        
        $objResponse = new xajaxResponse();
        
        $dbtable='care_pharma_products_main';
        $objunit = new Unit();
        
        if (!is_array($items)) $items = array($items);
        if (!is_array($unitids)) $unitids = array($unitids);
        if (!is_array($is_pcs)) $is_pcs = array($is_pcs);
        if (!is_array($qtys)) $qtys = array($qtys);
        
        foreach ($items as $i=>$item) {
            $strSQL = "select artikelname, generic, avg_cost, qty_per_pack from care_pharma_products_main as cppm left join seg_item_extended as ie on cppm.bestellnum=ie.item_code where cppm.bestellnum = '$item'";
            $result = $db->Execute($strSQL);
            
            if ($result) {
                if ($result->RecordCount()) {
                    if ($row = $result->FetchRow()) {
                        $obj = (object) 'details';
                        $obj->id        = $item;
                        $obj->name      = $row["artikelname"];
                        $obj->desc      = $row["generic"];
                        $obj->unit      = $unitids[$i];
                        $obj->qty       = $qtys[$i];
                        $obj->unit_name = $objunit->getUnitName($unitids[$i]);
                        $obj->is_perpc  = $is_pcs[$i];
                        $obj->avg  = $row["avg_cost"];  
                        $obj->perpack = $row["qty_per_pack"]; 
                                             
                        $objResponse->call("addItemToRequest", NULL, $obj); 
                        //$objResponse->addScriptCall("addProductToList","product-list",$details);                                         
                    }
                }
            }
            else {            
                if (defined('__DEBUG_MODE'))
                    $objResponse->call("display",$sql);
                else
                    $objResponse->alert("ERROR: ".$db->ErrorMsg());
            }  
        }          
                        
        return $objResponse;        
    }        
      
    function getRequestedAreas($s_areacode, $r_areacode='') {
        $objResponse = new xajaxResponse();
        
        $objdept = new Department();
      #  $result = $objdept->getAreasInDept($dept_nr);

        $result = $objdept->getAllAreas($s_areacode);
        $count = 0;
        if ($result) {
            while($row=$result->FetchRow()){
                $checked=strtolower($row['area_code'])==strtolower($r_areacode) ? 'selected="selected"' : "";
                $dest_area .= "<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
                if ($checked) $index = $count;
                $count++;
            }
            $dest_area = '<select class="jedInput" name="des_area" id="des_area" >'."\n".$dest_area."</select>\n".
                "<input type=\"hidden\" id=\"area3\" name=\"area3\" value=\"".$r_areacode."\"/>";
                
            $objResponse->call("showRequestedAreas",$dest_area);
        }  
        
        return $objResponse;
    }  
    
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'modules/supply_office/ajax/external-request-tray.common.php');
    require_once($root_path.'include/care_api_classes/inventory/class_external_request.php');
    require_once($root_path.'include/care_api_classes/inventory/class_unit.php');
    $xajax->processRequest();
?>
