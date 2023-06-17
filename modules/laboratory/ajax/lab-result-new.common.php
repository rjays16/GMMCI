<?php

    require('./roots.php');

    require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
    $xajax = new xajax($root_path."modules/laboratory/ajax/lab-result-new.server.php");
    $xajax->setCharEncoding("ISO-8859-1");

    $xajax->register(XAJAX_FUNCTION, "saveResult");
    $xajax->register(XAJAX_FUNCTION, "checkResult");
    $xajax->register(XAJAX_FUNCTION, "deleteResult");
    $xajax->register(XAJAX_FUNCTION, "saveOfficialResult");

?>