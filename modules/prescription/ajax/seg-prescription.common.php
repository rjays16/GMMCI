<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path.'modules/prescription/ajax/seg-prescription.server.php');
$xajax->setCharEncoding("ISO-8859-1");
$xajax->register(XAJAX_FUNCTION, "savePrescription");
$xajax->register(XAJAX_FUNCTION, "saveTemplate");
$xajax->register(XAJAX_FUNCTION, "deleteTemplate");
$xajax->register(XAJAX_FUNCTION, "showEditTemplate");
$xajax->register(XAJAX_FUNCTION, "updateTemplate");
?>
