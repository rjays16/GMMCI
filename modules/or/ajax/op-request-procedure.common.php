<?php
    require('./roots.php');
    require_once($root_path.'classes/xajax/xajax.inc.php');
    $xajax = new xajax($root_path."modules/or/ajax/op-request-procedure.server.php");
    $xajax->setCharEncoding("ISO-8859-1");
    $xajax->registerFunction("populateProcedureList");
?>