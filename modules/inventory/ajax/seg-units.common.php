<?php
    require('./roots.php');
    require_once($root_path.'classes/xajax/xajax.inc.php');
    $xajax = new xajax($root_path."modules/inventory/ajax/seg-units.server.php");    
    $xajax->setCharEncoding("iso-8859-1");
    
    $xajax->registerFunction("deleteUnit");        
    $xajax->processRequests();
?>