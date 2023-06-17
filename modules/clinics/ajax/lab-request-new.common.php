<?php
require('./roots.php');
require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
//Instantiate xajax object.
$xajax = new xajax($root_path.'modules/clinics/ajax/lab-request-new.server.php');

$xajax->registerFunction("getCharityDiscounts");
$xajax->registerFunction("setCharityDiscounts");
$xajax->registerFunction("existSegCharityAmount");
$xajax->registerFunction("checkIfalreadyPaid_Granted");
#$xajax->registerFunction("checkIfhasResult");
?>