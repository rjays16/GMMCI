<?php        
	require('./roots.php');
	require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
	$xajax = new xajax($root_path."modules/pharmacy/ajax/order-tray.server.php");
	#$xajax->configure('debug',true);
	$xajax->setCharEncoding("ISO-8859-1");
	$xajax->register(XAJAX_FUNCTION, "populateProductList");
	$xajax->register(XAJAX_FUNCTION, "populateORProductList");	#added by CHA, Jan 8, 2010
	$xajax->register(XAJAX_FUNCTION, "populatePackageItemList");	#added by CHA, Feb 10, 2010        
?>