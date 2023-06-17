<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path.'modules/laboratory/ajax/lab-manual-result.php');
$xajax->setCharEncoding("ISO-8859-1");

$xajax->register(XAJAX_FUNCTION, "saveTestGroup");
$xajax->register(XAJAX_FUNCTION, "deleteTestGroup");
$xajax->register(XAJAX_FUNCTION, "populateTestGroup");
$xajax->register(XAJAX_FUNCTION, "updateTestGroup");
$xajax->register(XAJAX_FUNCTION, "saveTestParameter");
$xajax->register(XAJAX_FUNCTION, "deleteTestParameter");
$xajax->register(XAJAX_FUNCTION, "updateTestParameter");
$xajax->register(XAJAX_FUNCTION, "saveParamGroup");
$xajax->register(XAJAX_FUNCTION, "deleteParamGroup");
$xajax->register(XAJAX_FUNCTION, "updateParamGroup");
$xajax->register(XAJAX_FUNCTION, "removeGrpAssignment");
$xajax->register(XAJAX_FUNCTION, "emptyParameters");
$xajax->register(XAJAX_FUNCTION, "newGroupId");
$xajax->register(XAJAX_FUNCTION, "newOrderNo");
$xajax->register(XAJAX_FUNCTION, "copyParams");
$xajax->register(XAJAX_FUNCTION, "undoCopyOfParams");
$xajax->register(XAJAX_FUNCTION, "checkExistingParam");
$xajax->register(XAJAX_FUNCTION, "populateParamChecklist");
$xajax->register(XAJAX_FUNCTION, "addNewFormGroup");
$xajax->register(XAJAX_FUNCTION, "DeleteFormGroup");
$xajax->register(XAJAX_FUNCTION, "UpdateFormName");

?>
