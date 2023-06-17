<?php
    require('./roots.php');
    //require($root_path.'include/inc_environment_global.php');
    //require_once($root_path.'classes/xajax/xajax.inc.php');
    #require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
    require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
    $xajax = new xajax($root_path."modules/laboratory/ajax/lab-result.server.php");
    $xajax->setCharEncoding("ISO-8859-1");
    #$xajax->configure('debug',true); 
    $xajax->register(XAJAX_FUNCTION, "populateLabRequestsList");
    $xajax->register(XAJAX_FUNCTION, "savedServedPatient");
    $xajax->register(XAJAX_FUNCTION, "contMonitor");
?>