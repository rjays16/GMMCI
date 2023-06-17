<?php
  #edited by VAS 03-21-2012
  #update the ajax version

  require('./roots.php');
  require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
  $xajax = new xajax($root_path."modules/bloodBank/ajax/blood-request-new.server.php");
    
  $xajax->setCharEncoding("ISO-8859-1");
  $xajax->register(XAJAX_FUNCTION, "populateRequestListByRefNo");
  $xajax->register(XAJAX_FUNCTION, "updateRequest");
  $xajax->register(XAJAX_FUNCTION, "existSegOverrideAmount");
  $xajax->register(XAJAX_FUNCTION, "checkwithDonor");
  $xajax->register(XAJAX_FUNCTION, "replacedBlood");//Added by Jarel 12/12/13
  
  $xajax->register(XAJAX_FUNCTION, "updateCoverage"); 
  $xajax->register(XAJAX_FUNCTION, "setDbData");//added by Nick, 1/14/2014
?>