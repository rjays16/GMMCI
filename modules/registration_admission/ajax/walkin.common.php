<?php
//created by cha, 12-14-2010
	require './roots.php';
	require_once $root_path.'classes/xajax/xajax_core/xajax.inc.php';
	$xajax = new xajax($root_path."modules/registration_admission/ajax/walkin.server.php");
	$xajax->setCharEncoding("ISO-8859-1");
	$xajax->register(XAJAX_FUNCTION, "registerWalkin");
	$xajax->register(XAJAX_FUNCTION, "checkExistingWalkin");
	$xajax->register(XAJAX_FUNCTION, "showWalkinDetails");
	$xajax->register(XAJAX_FUNCTION, "updateWalkin");
	$xajax->register(XAJAX_FUNCTION, "deleteWalkin");