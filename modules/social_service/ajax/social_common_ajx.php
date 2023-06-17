<?php
	require('./roots.php');
	require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
	$xajax = new xajax($root_path."modules/social_service/ajax/social_server_ajx.php");
		
	//$xajax->setCharEncoding("iso-8859-1");
	$xajax->registerFunction("listRow"); //display list of social service classification
	$xajax->registerFunction("refresh"); //refresh page
	$xajax->registerFunction("deleteData");
	
?>