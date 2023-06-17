<?php
    function getMuniCityandProv($brgy_nr) {
        global $db;
        
        $objResponse = new xajaxResponse();
        
        $strSQL = "SELECT p.prov_nr, m.mun_nr, p.prov_name, m.mun_name \n
                      FROM (seg_barangays as b inner join seg_municity as m \n
                         on b.mun_nr = m.mun_nr) inner join seg_provinces as p \n
                         on m.prov_nr = p.prov_nr \n
                         where b.brgy_nr = $brgy_nr";
        
        if ($result = $db->Execute($strSQL)) {
            if ($row = $result->FetchRow()) {
                $objResponse->call("setMuniCity", (is_null($row['mun_nr']) ? 0 : $row['mun_nr']), (is_null($row['mun_name']) ? '' : $row['mun_name']));
                $objResponse->call("setProvince", (is_null($row['prov_nr']) ? 0 : $row['prov_nr']), (is_null($row['prov_name']) ? '' : $row['prov_name']));
            }
        }
        
        return $objResponse;
    }
    
    function getProvince($mun_nr) {
        global $db;
        
        $objResponse = new xajaxResponse();
        
        $strSQL = "SELECT p.prov_nr, p.prov_name \n
                      FROM seg_municity as m inner join seg_provinces as p \n
                         on m.prov_nr = p.prov_nr \n
                      where m.mun_nr = $mun_nr";
        
        if ($result = $db->Execute($strSQL)) {
            if ($row = $result->FetchRow()) {
                $objResponse->call("setProvince", (is_null($row['prov_nr']) ? 0 : $row['prov_nr']), (is_null($row['prov_name']) ? '' : $row['prov_name']));
            }
        }
        
        return $objResponse;    
    }

    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'modules/system_admin/ajax/hosp_info.common.php');        
    $xajax->processRequest();
?>