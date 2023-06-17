<?php

require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path."modules/cashier/ajax/cashier-main.server.php");
//$xajax->setCharEncoding("ISO-8859-1");
$xajax->register(XAJAX_FUNCTION, "checkORNoExists");
$xajax->register(XAJAX_FUNCTION, "populateReferences");
$xajax->register(XAJAX_FUNCTION, "populateDetails");
$xajax->register(XAJAX_FUNCTION, "addReference");
$xajax->register(XAJAX_FUNCTION, "addPFOItem");
$xajax->register(XAJAX_FUNCTION, "getLatestORNumber");
$xajax->register(XAJAX_FUNCTION, "populateORParticulars");

