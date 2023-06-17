<?php
require('./roots.php');
#require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
require_once($root_path.'classes/xajax/xajax.inc.php');
//Instantiate xajax object.
$xajax = new xajax($root_path.'modules/ic_lab/ajax/iclab-request-new.server.php');
$xajax->setCharEncoding("iso-8859-1");
//register a function here for xajax script
$xajax->registerFunction("populateRequestListByRefNo");

$xajax->registerFunction("updateRequest");
$xajax->registerFunction("existSegOverrideAmount");

?>