<?php
      //created by cha 08-03-09
    require('./roots.php');
    require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
    $xajax = new xajax($root_path."modules/bloodBank/ajax/blood-donor-register.server.php");
    $xajax->setCharEncoding("iso-8859-1");
    $xajax->register(XAJAX_FUNCTION,"registerBloodDonor");
    $xajax->register(XAJAX_FUNCTION,"computeAge");
    $xajax->register(XAJAX_FUNCTION,"populateDonorList");
    $xajax->register(XAJAX_FUNCTION,"getMuniCityandProv");
    $xajax->register(XAJAX_FUNCTION,"deleteBloodDonor");
    $xajax->register(XAJAX_FUNCTION,"getDonorDetails");
    $xajax->register(XAJAX_FUNCTION,"updateBloodDonor");
    $xajax->register(XAJAX_FUNCTION,"saveBloodDetails");
    $xajax->register(XAJAX_FUNCTION,"populateDonationList");
    $xajax->register(XAJAX_FUNCTION,"deleteBloodItem");
    $xajax->register(XAJAX_FUNCTION,"updateBloodItem");
    $xajax->register(XAJAX_FUNCTION,"selectDonorList");
?>
