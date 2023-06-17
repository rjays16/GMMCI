<?php
		//require_once ($root"../../xajax.inc.php");
		require('./roots.php');
		//require($root_path.'include/inc_environment_global.php');
		require_once($root_path.'classes/xajax/xajax.inc.php');
		#require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
		$xajax = new xajax($root_path."modules/registration_admission/ajax/med_cert.server.php");
		$xajax->setCharEncoding("ISO-8859-1");
		$xajax->registerFunction("populateMedCertEncRef");
		$xajax->registerFunction("populateConfiCertEncRefHistory");// add by shandy 08/28/2013
		$xajax->registerFunction("deleteCertificate");
                $xajax->registerFunction("deleteCertificateConf");// add by shandy 08/28/2013
?>