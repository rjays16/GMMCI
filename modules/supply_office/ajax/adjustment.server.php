<?php
#added by bryan on April 13,2008
#  
    define('__DEBUG_MODE',1);
    
    function add_item($items, $item_names, $athand, $expiry=NULL, $serial=NULL, $adjquan, $pmquan, $unitid, $reasons, $perpc) {
        $objResponse = new xajaxResponse();
        $adj_obj = new SegAdjustment();
        $unit_obj =  new Unit();

        # Later: Put this in a Class
        if (!is_array($items)) $items = array($items);
        if (!is_array($item_names)) $item_names = array($item_names);
        if (!is_array($athand)) $athand = array($athand);
        if (!is_array($expiry)) $expiry = array($expiry);
        if (!is_array($serial)) $serial = array($serial);
        if (!is_array($adjquan)) $adjquan = array($adjquan);
        if (!is_array($pmquan)) $pmquan = array($pmquan);
        if (!is_array($unitid)) $unitid = array($unitid);
        if (!is_array($reasons)) $reasons = array($reasons);
        if (!is_array($perpc)) $perpc = array($perpc); 

        
        foreach ($items as $i=>$item) {
        
            #$objResponse->call("clearOrder",NULL);
        
            $obj = (object) 'details';
            $obj->id = $items[$i];
            $obj->name = $item_names[$i];
            $obj->athand= $athand[$i];
            $obj->expiry = $expiry[$i];
            $obj->serial = $serial[$i];
            $obj->adjquan = $adjquan[$i];
            $obj->pmquan = $pmquan[$i];
            $obj->unitid = $unitid[$i];
            $obj->unitdesc = $unit_obj->getUnitName($unitid[$i]);
            $obj->reasons = $reasons[$i];
            $obj->reasons_name = $adj_obj->getReasonName($reasons[$i]);
            $obj->perpc = $perpc[$i];
            
            $objResponse->call("appendOrderPopulate", NULL, $obj);

        }
        return $objResponse;
    }
        
    function updateValueAtHand($expiry='', $serial='', $area='',$item='',$rowno='', $unitid=0) {
        $objResponse = new xajaxResponse();
        $date = date("Y-m-d");

//        $eodobj = new EODInventory();
//        $newqty = $eodobj->getCurrentEODQty($item,$area,$date,$expiry,NULL);
        
        $skuobj = new SKUInventory();
        $skuids = $skuobj->getSKUIds($item, $area, $expiry, $serial, $unitid);
        $newqty = $skuobj->getQtyofSKUs($skuids, $date);
        $objResponse->call("changeValueAtHand", $newqty, $item, $expiry, $serial, $rowno);
          
        return $objResponse;                
    }
    
    function getRequestedAreasIss($s_areacode, $r_areacode='') {
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
    
    function reset_referenceno() {
        $objResponse = new xajaxResponse();
        
        $sc = new SegAdjustment();
        $lastnr = $sc->getLastNr(date("Y-m-d"));

        if ($lastnr)
            $objResponse->call("resetRefNo",$lastnr);
        else
            $objResponse->call("resetRefNo","Error!",1);
        return $objResponse;
    }
    
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');

    
//    require($root_path.'include/care_api_classes/inventory/class_eodinventory.php');
    require_once($root_path.'include/care_api_classes/inventory/class_sku_inventory.php');
    require_once($root_path.'include/care_api_classes/inventory/class_adjustment.php');
    require_once($root_path.'include/care_api_classes/inventory/class_unit.php');
    require_once($root_path.'include/care_api_classes/class_department.php');
    require_once($root_path.'include/care_api_classes/class_personell.php');
    require_once($root_path.'modules/supply_office/ajax/adjustment.common.php');
    $xajax->processRequest();
?>