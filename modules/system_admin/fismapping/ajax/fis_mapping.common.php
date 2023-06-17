<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path.'modules/system_admin/fismapping/ajax/fis_mapping.server.php');
$xajax->setCharEncoding("ISO-8859-1");
$xajax->register(XAJAX_FUNCTION, "SaveMappingWithCostCenter");
$xajax->register(XAJAX_FUNCTION, "CheckIfHasAreas");
$xajax->register(XAJAX_FUNCTION, "SaveMappingWithOutCostCenter");

?>
