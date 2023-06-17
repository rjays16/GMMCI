<?php
    //created by cha 10-13-09
    require('./roots.php');
    require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
    $xajax = new xajax($root_path."modules/pharmacy/ajax/pharma-walkin.server.php");
    $xajax->setCharEncoding("iso-8859-1");
    $xajax->register(XAJAX_FUNCTION,"searchPharmaWalkin");
    $xajax->register(XAJAX_FUNCTION,"saveNewAccount");
    $xajax->register(XAJAX_FUNCTION,"deleteWalkin");
    $xajax->register(XAJAX_FUNCTION,"saveEditAccount");
    $xajax->register(XAJAX_FUNCTION,"getPID");
?>
