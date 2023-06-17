<?php
		require('./roots.php');
		require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
		$xajax = new xajax($root_path."modules/price_adjustments/ajax/price_adjustments.server.php");
		$xajax->setCharEncoding("iso-8859-1");
		#$xajax->register(XAJAX_FUNCTION,"populateHospitalService");
		$xajax->register(XAJAX_FUNCTION,"savePriceAdjustments");
		$xajax->register(XAJAX_FUNCTION,"populatePriceHistory");
		$xajax->register(XAJAX_FUNCTION,"updatePriceAdjustment");
		$xajax->register(XAJAX_FUNCTION,"deletePriceAdjustment");

		$xajax->register(XAJAX_FUNCTION,"populateLabServiceList");
		$xajax->register(XAJAX_FUNCTION,"populateRadioServiceList");
		$xajax->register(XAJAX_FUNCTION,"populatePharmaServiceList");
		$xajax->register(XAJAX_FUNCTION,"populateMiscServiceList");
		$xajax->register(XAJAX_FUNCTION,"populateOtherServiceList");

		#added by VAN 07-14-2010
		$xajax->register(XAJAX_FUNCTION,"savePriceList");
		$xajax->register(XAJAX_FUNCTION,"populatePriceListHistory");
		$xajax->register(XAJAX_FUNCTION,"updatePriceList");
		$xajax->register(XAJAX_FUNCTION,"deletePriceList");
		#----------
?>
