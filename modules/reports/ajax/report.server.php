<?php
    function getMuniCityandProv($brgy_nr) {
        global $db;
        
        $objResponse = new xajaxResponse();
        
        $sql = "SELECT p.prov_nr, m.mun_nr, p.prov_name, m.mun_name \n
                      FROM (seg_barangays b INNER JOIN seg_municity m \n
                         ON b.mun_nr = m.mun_nr) INNER JOIN seg_provinces p \n
                         ON m.prov_nr = p.prov_nr \n
                         WHERE b.brgy_nr = $brgy_nr";
        #$objResponse->alert($sql); 
        if ($result = $db->Execute($sql)) {
            if ($row = $result->FetchRow()) {
                $objResponse->call("setMuniCity", (is_null($row['mun_nr']) ? 0 : $row['mun_nr']), (is_null($row['mun_name']) ? '' : trim($row['mun_name'])));
                $objResponse->call("setProvince", (is_null($row['prov_nr']) ? 0 : $row['prov_nr']), (is_null($row['prov_name']) ? '' : trim($row['prov_name'])));
            }
        }
        
        return $objResponse;
    }
    
    function getProvince($mun_nr) {
        global $db;
        
        $objResponse = new xajaxResponse();
        
        $sql = "SELECT p.prov_nr, p.prov_name \n
                      FROM seg_municity m INNER JOIN seg_provinces p \n
                         ON m.prov_nr = p.prov_nr \n
                      WHERE m.mun_nr = $mun_nr";
        #$objResponse->alert($sql);
        if ($result = $db->Execute($sql)) {
            if ($row = $result->FetchRow()) {
                $objResponse->call("setProvince", (is_null($row['prov_nr']) ? 0 : $row['prov_nr']), (is_null($row['prov_name']) ? '' : trim($row['prov_name'])));
            }
        }
        
        return $objResponse;    
    }

    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'modules/reports/ajax/report.common.php');        
    $xajax->processRequest();
?>