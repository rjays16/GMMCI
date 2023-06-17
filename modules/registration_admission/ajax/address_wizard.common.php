<?php

require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path."modules/registration_admission/ajax/address_wizard.server.php");
$xajax->configure('characterEncoding', "iso-8859-1");
$xajax->register(XAJAX_FUNCTION, "populateAddress");
$xajax->configure('debug',false);