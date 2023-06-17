<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path."modules/pharmacy/ajax/wardstock-list.server.php");

$xajax->setCharEncoding("ISO-8859-1");

/*    $xajax->registerFunction("populate_stock");
    $xajax->registerFunction("reset_stocknr");*/

    $xajax->register(XAJAX_FUNCTION, "populateWardstockList");
    //$xajax->configure('debug',TRUE);
    #added by bryan on sept 26, 2008
    $xajax->register(XAJAX_FUNCTION,"populate_stock");
    $xajax->register(XAJAX_FUNCTION,"reset_stocknr");
?>