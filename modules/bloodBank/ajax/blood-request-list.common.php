<?php
require('./roots.php');
require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
//Instantiate xajax object.
$xajax = new xajax($root_path.'modules/bloodBank/ajax/blood-request-list.server.php');
$xajax->setCharEncoding("iso-8859-1");
//register a function here for xajax script
$xajax->registerFunction("populateRequestList");
$xajax->registerFunction("deleteRequest");
$xajax->registerFunction("populate_promissory_note"); 

#added by VAN 01-09-10
$xajax->registerFunction("savedSentOutRequest");
$xajax->registerFunction("servedRequest");
$xajax->registerFunction("servedRequest");
?>