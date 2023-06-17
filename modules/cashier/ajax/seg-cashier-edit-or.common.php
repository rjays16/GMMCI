<?php
    //created by cha 05-20-09
    require('./roots.php');
    require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
    $xajax = new xajax($root_path."modules/cashier/ajax/seg-cashier-edit-or.server.php");
    $xajax->setCharEncoding("iso-8859-1");
    $xajax->register(XAJAX_FUNCTION,"populateORList");
    $xajax->register(XAJAX_FUNCTION,"generateNewOR");
    $xajax->register(XAJAX_FUNCTION,"saveChanges");
    $xajax->register(XAJAX_FUNCTION,"checkIfORExists");
    $xajax->register(XAJAX_FUNCTION,"checkORNos");
?>
