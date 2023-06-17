<?php
    require('./roots.php');
    require_once($root_path.'classes/xajax/xajax.inc.php');
    $xajax = new xajax($root_path."modules/supply_office/ajax/issue-tray-server.php");
    $xajax->setCharEncoding("ISO-8859-1");
    $xajax->registerFunction("populateIssueProductList");
    $xajax->registerFunction("populateTypesComboIss");
    $xajax->registerFunction("updateValueAtHandIss");
    $xajax->registerFunction("updateValueAtHandIssSer");
    $xajax->registerFunction("getInventoryAtHandIncludeDate");
    
    
?>