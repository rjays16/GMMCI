<?php
    require('./roots.php');
    require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
    $xajax = new xajax($root_path."modules/inventory/ajax/seg-delivery.server.php");
    
    $xajax->setCharEncoding("ISO-8859-1");
  # $xajax->configure('debug',true); 
    $xajax->register(XAJAX_FUNCTION, "reset_referenceno");
    $xajax->register(XAJAX_FUNCTION, "goAddItem");
    $xajax->register(XAJAX_FUNCTION, "getItemAvgCost");
    $xajax->register(XAJAX_FUNCTION, "searchPO");
    //added by ken for resetting the po number from fis to get new po number
    $xajax->register(XAJAX_FUNCTION, "reset_pono");
?>