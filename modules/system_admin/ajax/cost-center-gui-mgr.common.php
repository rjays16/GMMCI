<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
	$xajax = new xajax($root_path."modules/system_admin/ajax/cost-center-gui-mgr.server.php");

	$xajax->setCharEncoding("ISO-8859-1");
	$xajax->register(XAJAX_FUNCTION, "populateServices");
	$xajax->register(XAJAX_FUNCTION, "populateRadioSections");
	$xajax->register(XAJAX_FUNCTION, "populateGuiList");
	$xajax->register(XAJAX_FUNCTION, "deleteGuiItem");
	$xajax->register(XAJAX_FUNCTION, "getGuiItems");
	$xajax->register(XAJAX_FUNCTION, "setSection");
?>
