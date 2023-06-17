<?php
require('roots.php');
require($root_path.'classes/xajax-0.2.5/xajax.inc.php');

$xajax = new xajax($root_path.'modules/or_logbook/ajax/op_server.php');
$xajax->registerFunction("delLogMainInfo");

$xajax->registerFunction("setALLDepartment");
$xajax->registerFunction("setDepartment");

$xajax->registerFunction("setDoctors");
$xajax->registerFunction("populateOpLogMain");

//OP schedule for Operation
$xajax->registerFunction("saveAppointment");
?>