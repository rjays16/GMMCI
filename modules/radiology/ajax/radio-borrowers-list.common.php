<?php
	
require('./roots.php');
require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
$xajax = new xajax($root_path."modules/radiology/ajax/radio-borrowers-list.server.php");

$xajax->setCharEncoding("iso-8859-1");
$xajax->registerFunction("PopulateRadioBorrowerList");
?>