<?php
	require('./roots.php');
	#commented out bry bryan on Sept 15, 2008
	/*require_once($root_path.'classes/xajax/xajax.inc.php');*/

	#added by bryan on Sept 15, 2008
	require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
	$xajax = new xajax($root_path."modules/or/ajax/order.server.php");

	$xajax->setCharEncoding("ISO-8859-1");
	#$xajax->configure('debug',true);

			#added by omick on January 13, 2009
		$xajax->register(XAJAX_FUNCTION, "delete_order");
		$xajax->register(XAJAX_FUNCTION, "cancel_or_main_request");

	$xajax->register(XAJAX_FUNCTION, "reset_referenceno");
	$xajax->register(XAJAX_FUNCTION, "add_item");
	$xajax->register(XAJAX_FUNCTION, "populate_order");
	$xajax->register(XAJAX_FUNCTION, "get_charity_discounts");

	#added by bryan on Sept 18,2008
	$xajax->register(XAJAX_FUNCTION, "populateOrderList");
	$xajax->register(XAJAX_FUNCTION, "deleteOrder");


	#added by CELSY on 07/17/2010
	$xajax->register(XAJAX_FUNCTION, "delete_checklist_item");

	#Added by Cherry on 11-10-2010
	$xajax->register(XAJAX_FUNCTION, "delete_suture");

	#added by Cherry on 08-08-10
	$xajax->register(XAJAX_FUNCTION, "getData");

	#added by Cherry on 09-12-10
	$xajax->register(XAJAX_FUNCTION, "getBeds");
?>