<?php
    require('./roots.php');
    require_once($root_path.'classes/xajax/xajax.inc.php');
    $xajax = new xajax($root_path.'modules/billing_new/ajax/billing-select-ops.server.php');
    
    $xajax->registerFunction("populateAppliedOpsList"); 
?>
