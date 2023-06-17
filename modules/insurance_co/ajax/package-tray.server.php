<?php
    /**
    * @internal     Retrieve the packages and list in HTML table.
    * @access       public
    * @author       Bong S. Trazo
    * @package      include
    * @subpackage   care_api_classes
    * @global       db - database object
    * 
    * @param        sElem     - name of element in HTML indicating search progress.
    * @param        searchkey - search filter typed by user.
    * @param        page      - page in list.
    * @return       boolean TRUE if successful, FALSE otherwise.
    */    
    function populatePackageList($sElem, $searchkey, $page) {    
        global $db;
        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('pagin_patient_search_max_block_rows');
        $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
        
        $objResponse = new xajaxResponse();
        $srv = new Insurance;
        $offset = $page * $maxRows;
        $searchkey = utf8_decode($searchkey);
        
        $rset = $srv->countSearchPackage($searchkey, $maxRows, $offset);
        $total  = $srv->count;
        
        $lastPage = floor($total/$maxRows);
        
        if ((floor($total%10))==0)
            $lastPage = $lastPage-1;
        
        if ($page > $lastPage) $page=$lastPage;
        $rows=0;

        $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
        $objResponse->addScriptCall("clearList","package-list", 1);
        if ($rset) {
            $rows=$rset->RecordCount();
            while($result=$rset->FetchRow()) {
                $objResponse->addScriptCall("addPackageToList", "package-list", $result["package_id"], trim($result["package_name"]), number_format($result["package_price"], 2, '.', ','));
            }#end of while
        } #end of if

        if (!$rows) $objResponse->addScriptCall("addPackageToList","package-list",NULL);
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }
        
        return $objResponse;
    }
    
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/care_api_classes/class_globalconfig.php');
    require_once($root_path.'include/care_api_classes/class_insurance.php');
    require($root_path."modules/insurance_co/ajax/package-tray.common.php");
    $xajax->processRequests();    
?>