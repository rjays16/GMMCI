<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path.'modules/prescription/ajax/seg-soap.server.php');
$xajax->setCharEncoding("ISO-8859-1");
$xajax->register(XAJAX_FUNCTION, "saveSoapNote");
$xajax->register(XAJAX_FUNCTION, "showNotes");
$xajax->register(XAJAX_FUNCTION, "deleteSoapNote");
$xajax->register(XAJAX_FUNCTION, "undoDeleteSoapNote");
$xajax->register(XAJAX_FUNCTION, "toggleDoctor");
$xajax->register(XAJAX_FUNCTION, "unToggleDoctor");
