<?php
require('./roots.php');
require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
$xajax = new xajax($root_path."modules/nursing/ajax/seg-attending-doctors-server.php");

//register function here
$xajax->registerFunction("setDepartments");
$xajax->registerFunction("setALLDepartment");
$xajax->registerFunction("setDoctors");

$xajax->registerFunction("PopulateRow");
$xajax->registerFunction("addAttendingDoctors");
$xajax->registerFunction("delAttendingDoctors");
	
?>