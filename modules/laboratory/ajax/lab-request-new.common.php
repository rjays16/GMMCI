<?php
  #edited by VAS 03-21-2012
  #update the ajax version

require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
//Instantiate xajax object.
$xajax = new xajax($root_path.'modules/laboratory/ajax/lab-request-new.server.php');

  $xajax->setCharEncoding("ISO-8859-1");
  $xajax->register(XAJAX_FUNCTION, "populateRequestListByRefNo");
  $xajax->register(XAJAX_FUNCTION, "updateRequest");
  $xajax->register(XAJAX_FUNCTION, "existSegOverrideAmount");
  $xajax->register(XAJAX_FUNCTION, "checkAccess");
  $xajax->register(XAJAX_FUNCTION, "checkAccess");
  
  $xajax->register(XAJAX_FUNCTION, "checkTestERLab");

  $xajax->register(XAJAX_FUNCTION, "updateCoverage");
  $xajax->register(XAJAX_FUNCTION, "getTotalCharge"); //added by maimai
  $xajax->register(XAJAX_FUNCTION, "getInsurances");
?>