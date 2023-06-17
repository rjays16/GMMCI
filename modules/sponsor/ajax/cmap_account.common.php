<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path."modules/sponsor/ajax/cmap_account.server.php");

$xajax->setCharEncoding("ISO-8859-1");
$xajax->register(XAJAX_FUNCTION, "populateAllotments");
$xajax->register(XAJAX_FUNCTION, "updateBalance");
$xajax->register(XAJAX_FUNCTION, "saveAccount");	#added by cha, june 16, 2010
$xajax->register(XAJAX_FUNCTION, "deleteAccount");	#added by cha, june 16, 2010
$xajax->register(XAJAX_FUNCTION, "updateAccount");	#added by cha, june 17, 2010

