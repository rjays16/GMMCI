<?php
require('roots.php');
require_once($root_path.'classes/xajax/xajax.inc.php');
$xajax = new xajax($root_path.'modules/billing/ajax/billing-discounts.server.php');
$xajax->setCharEncoding("ISO-8859-1");

$xajax->registerFunction("getApplicableDiscounts");
$xajax->registerFunction("fillDiscountsCbo");
$xajax->registerFunction("SaveAppliedDiscount");
$xajax->registerFunction("getDiscountInfo");
$xajax->registerFunction("deleteDiscount");
$xajax->registerFunction("getBillAreasApplied");
$xajax->registerFunction("getDiscount");
?>
