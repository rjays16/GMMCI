<?php
require('roots.php');
require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
$xajax = new xajax($root_path.'modules/billing/ajax/bill-print.server.php');
$xajax->setCharEncoding("ISO-8859-1");

$xajax->registerFunction("initMain");

?>