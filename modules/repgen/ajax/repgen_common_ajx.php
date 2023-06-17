<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path."modules/repgen/ajax/repgen_select_rep_ajx.php");

//register function here

		$xajax->register(XAJAX_FUNCTION, "ProcessReportType");
		$xajax->register(XAJAX_FUNCTION, "setCodes");  #---added by CHA 09-01-09
		$xajax->register(XAJAX_FUNCTION, "getICD");  #---added by CHA 09-01-09
		$xajax->register(XAJAX_FUNCTION, "getICP");  #---added by CHA 09-01-09

		#added by VAN 09-14-2010
		$xajax->register(XAJAX_FUNCTION, "getListReport");
?>
