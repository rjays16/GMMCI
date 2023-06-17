<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
	$xajax = new xajax($root_path."modules/sponsor/ajax/cmap_adjustment.server.php");
	
	$xajax->setCharEncoding("ISO-8859-1");  
  $xajax->register(XAJAX_FUNCTION, "populateAdjustments");
  $xajax->register(XAJAX_FUNCTION, "updateBalance");
  
