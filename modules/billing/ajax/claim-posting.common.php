<?php
    require('./roots.php');
    require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
    $xajax = new xajax($root_path."modules/billing/ajax/claim-posting.server.php");
    
    $xajax->setCharEncoding("ISO-8859-1");
  # $xajax->configure('debug',true); 
    $xajax->register(XAJAX_FUNCTION, "resetRefNo");   
    $xajax->register(XAJAX_FUNCTION, "setMemCategoryOptions"); 
    $xajax->register(XAJAX_FUNCTION, "addClaimDet");
    $xajax->register(XAJAX_FUNCTION, "showClaimsPayment");               
    $xajax->register(XAJAX_FUNCTION, "delPostedClaim");
    $xajax->register(XAJAX_FUNCTION, "assignToClaimsSessionVar");
?>