<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path."modules/billing/ajax/pkg-coverage.server.php");

$xajax->register(XAJAX_FUNCTION, "showOverrideFlag");
$xajax->register(XAJAX_FUNCTION, "saveCoverage");
?>