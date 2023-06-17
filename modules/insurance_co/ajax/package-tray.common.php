<?php
    require('./roots.php');
    require_once($root_path.'classes/xajax/xajax.inc.php');
    $xajax = new xajax($root_path."modules/insurance_co/ajax/package-tray.server.php");
    $xajax->setCharEncoding("ISO-8859-1");
    $xajax->registerFunction("populatePackageList");
?>