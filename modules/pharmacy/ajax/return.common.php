<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
	$xajax = new xajax($root_path."modules/pharmacy/ajax/return.server.php");
	$xajax->setCharEncoding("ISO-8859-1");
	$xajax->register(XAJAX_FUNCTION,"reset_returnNr");
	$xajax->register(XAJAX_FUNCTION,"populate_items");
	$xajax->register(XAJAX_FUNCTION,"returnItem");     //added by cha, July 8, 2010
	$xajax->register(XAJAX_FUNCTION,"populate_info");     //added by cha, July 8, 2010
