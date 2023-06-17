<?php
require('./roots.php');
require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
//Instantiate xajax object.
$xajax = new xajax($root_path.'modules/or/ajax/op-request-new.server.php');
/*
//register a function here for xajax script
$xajax->registerFunction("getServiceGroup");  // get service group of particular service (radio / lab)
$xajax->registerFunction("psrv");             // populate service

$xajax->registerFunction("srvGui"); //display initial table list of service
$xajax->registerFunction("getAjxGui");
//$xajax->registerFunction("clrTable"); //clear table if no service found in this group

$xajax->registerFunction("srvList"); // display requested services
//$xajax->registerFunction("srvTable");
$xajax->registerFunction("delSrv");
$xajax->registerFunction("getConstructedTab");

$xajax->registerFunction("populateSrvListAll");
$xajax->registerFunction("get_charity_discounts");
*/
	$xajax->registerFunction("populateOpsCodeListByRefNo");
	$xajax->registerFunction("populatePersonnel");

	#added by VAN 06-24-08
	$xajax->registerFunction("populateORroomByDept");

		$xajax->registerFunction('populate_or_main_anesthesia'); //added by Omick, December 18, 2008
		$xajax->registerFunction('populate_order');   //added by Omick
		$xajax->registerFunction('add_equipment');    //added by Omick
		$xajax->registerFunction('populate_equipment_order');  //added by Omick
		$xajax->registerFunction('populate_events'); //added by Omick, February 18, 2009
		$xajax->registerFunction('laboratory_test'); //added by Omick, February 25, 2009
		$xajax->registerFunction('blood_test'); //added by Omick, February 25, 2009
		$xajax->registerFunction('radiology_test'); //added by Omick, February 25, 2009
		$xajax->registerFunction('populate_sponge_list'); //added by Omick, March 05, 2009
		$xajax->registerFunction('delete_blood_request'); //added by Omick, March 11, 2009
		$xajax->registerFunction('delete_radiology_request'); //added by Omick, March 11, 2009
		$xajax->registerFunction('delete_laboratory_request'); //added by Omick, March 11, 2009
		$xajax->registerFunction('delete_laboratory_service_code'); //added by Omick, March 13, 2009
		$xajax->registerFunction('delete_blood_service_code'); //added by Omick, March 14, 2009
		$xajax->registerFunction('delete_radiology_service_code'); //added by Omick, March 14, 2009
		$xajax->registerFunction('is_already_billed'); //added by Omick, March 19, 2009
		$xajax->registerFunction('add_oxygen'); //added by Omick, June 3, 2009
		$xajax->registerFunction('populate_equipment_oxygen'); //added by Omick, June 5, 2009
		$xajax->registerFunction('select_dr_patient'); //added by Omick, August 25, 2009
		$xajax->registerFunction('populate_accomodation'); //added by Omick, October 6, 2009
		$xajax->registerFunction('add_misc'); //added by Omic, October 7, 2009
		$xajax->registerFunction('populate_misc_order'); //added by Omick, October 8 2009
		$xajax->registerFunction('populate_room_list'); //added by Omick, Octotober 10 2009
		$xajax->registerFunction('get_room_rate'); //added by Omick, Octotober 10 2009
		$xajax->registerFunction('populate_accommodation'); //added by Omick, October 13 2009
		$xajax->registerFunction('set_pharma_refno'); //added by Omick, October 15 2009
		$xajax->registerFunction('set_equipment_refno'); //added by Omick, October 15 2009
		$xajax->registerFunction('get_package_clinics'); //added by Omick, December 4 2009
	$xajax->registerFunction('populate_sub_anesthesia'); //added by CHA, November 16 2009
	$xajax->registerFunction('show_added_anesthesia'); //added by CHA, November 16 2009
	$xajax->registerFunction('refresh_anesthesia'); //added by CHA, January 8, 2010
	$xajax->registerFunction('refresh_order_anesthetics'); //added by CHA, January 8, 2010
	$xajax->registerFunction('get_package_item_details'); //added by CHA, February 12, 2010
	$xajax->registerFunction('get_misc_request_by_refno'); //added by CHA, August 27, 2010
	$xajax->registerFunction('isSpongeType'); //added by ngel, september 09.2010

?>