<?php
require('./roots.php');
require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
//Instantiate xajax object.
$xajax = new xajax($root_path.'modules/nursing/ajax/nursing-station-radio-server.php');

//register a function here for xajax script
$xajax->registerFunction("getServiceGroup");  // get service group of particular service (radio / lab)
$xajax->registerFunction("psrv");             // populate service  

$xajax->registerFunction("srvGui"); //display initial table list of service
$xajax->registerFunction("getAjxGui");
//$xajax->registerFunction("clrTable"); //clear table if no service found in this group

$xajax->registerFunction("srvList"); // display requested services
//$xajax->registerFunction("srvTable");
$xajax->registerFunction("delSrv");
$xajax->registerFunction("getConstructedTab");

$xajax->registerFunction("populateSrvListAll");

$xajax->registerFunction("populateRequestListByRefNo");
$xajax->registerFunction("get_charity_discounts");

$xajax->registerFunction("getCharityDiscounts");
$xajax->registerFunction("setCharityDiscounts");

?>