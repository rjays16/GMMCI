<?php
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
    require_once($root_path.'include/care_api_classes/class_department.php'); 
    require_once($root_path.'modules/supply_office/ajax/request.common.php');       
    $xajax->processRequest();       
?>
