<?php
require('./roots.php');
require_once $root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php';

//Instantiate xajax object.
$xajax = new xajax($root_path.'modules/or/ajax/or_charges.server.php');
$xajax->register(XAJAX_FUNCTION,"populateOpsCodeListByRefNo");
$xajax->register(XAJAX_FUNCTION,"populatePersonnel");
$xajax->register(XAJAX_FUNCTION,"populateORroomByDept");
$xajax->register(XAJAX_FUNCTION,'populate_or_main_anesthesia'); //added by Omick, December 18, 2008
$xajax->register(XAJAX_FUNCTION,'populate_order');   //added by Omick
$xajax->register(XAJAX_FUNCTION,'add_equipment');    //added by Omick
$xajax->register(XAJAX_FUNCTION,'populate_equipment_order');  //added by Omick
$xajax->register(XAJAX_FUNCTION,'populate_events'); //added by Omick, February 18, 2009
$xajax->register(XAJAX_FUNCTION,'laboratory_test'); //added by Omick, February 25, 2009
$xajax->register(XAJAX_FUNCTION,'blood_test'); //added by Omick, February 25, 2009
$xajax->register(XAJAX_FUNCTION,'radiology_test'); //added by Omick, February 25, 2009
$xajax->register(XAJAX_FUNCTION,'populate_sponge_list'); //added by Omick, March 05, 2009
$xajax->register(XAJAX_FUNCTION,'delete_blood_request'); //added by Omick, March 11, 2009
$xajax->register(XAJAX_FUNCTION,'delete_radiology_request'); //added by Omick, March 11, 2009
$xajax->register(XAJAX_FUNCTION,'delete_laboratory_request'); //added by Omick, March 11, 2009
$xajax->register(XAJAX_FUNCTION,'delete_laboratory_service_code'); //added by Omick, March 13, 2009
$xajax->register(XAJAX_FUNCTION,'delete_blood_service_code'); //added by Omick, March 14, 2009
$xajax->register(XAJAX_FUNCTION,'delete_radiology_service_code'); //added by Omick, March 14, 2009
$xajax->register(XAJAX_FUNCTION,'is_already_billed'); //added by Omick, March 19, 2009
$xajax->register(XAJAX_FUNCTION,'add_oxygen'); //added by Omick, June 3, 2009
$xajax->register(XAJAX_FUNCTION,'populate_equipment_oxygen'); //added by Omick, June 5, 2009
$xajax->register(XAJAX_FUNCTION,'select_dr_patient'); //added by Omick, August 25, 2009
$xajax->register(XAJAX_FUNCTION,'populate_accomodation'); //added by Omick, October 6, 2009
$xajax->register(XAJAX_FUNCTION,'add_misc'); //added by Omic, October 7, 2009
$xajax->register(XAJAX_FUNCTION,'populate_misc_order'); //added by Omick, October 8 2009
$xajax->register(XAJAX_FUNCTION,'populate_room_list'); //added by Omick, Octotober 10 2009
$xajax->register(XAJAX_FUNCTION,'get_room_rate'); //added by Omick, Octotober 10 2009
$xajax->register(XAJAX_FUNCTION,'populate_accommodation'); //added by Omick, October 13 2009
$xajax->register(XAJAX_FUNCTION,'set_pharma_refno'); //added by Omick, October 15 2009
$xajax->register(XAJAX_FUNCTION,'set_equipment_refno'); //added by Omick, October 15 2009
$xajax->register(XAJAX_FUNCTION,'get_package_clinics'); //added by Omick, December 4 2009
$xajax->register(XAJAX_FUNCTION,'populate_sub_anesthesia'); //added by CHA, November 16 2009
$xajax->register(XAJAX_FUNCTION,'show_added_anesthesia'); //added by CHA, November 16 2009
$xajax->register(XAJAX_FUNCTION,'refresh_anesthesia'); //added by CHA, January 8, 2010
$xajax->register(XAJAX_FUNCTION,'refresh_order_anesthetics'); //added by CHA, January 8, 2010
$xajax->register(XAJAX_FUNCTION,'get_package_item_details'); //added by CHA, February 12, 2010

	$xajax->register(XAJAX_FUNCTION,'setORWardRooms'); //added by CHA, April 9, 2010 *function copied from billing
	$xajax->register(XAJAX_FUNCTION,'setORWardOptions'); //added by CHA, April 9, 2010 *function copied from billing
	$xajax->register(XAJAX_FUNCTION,'updateRVUTotal'); //added by CHA, April 9, 2010 *function copied from billing
	$xajax->register(XAJAX_FUNCTION,'getOPCharge'); //added by CHA, April 9, 2010 *function copied from billing
	$xajax->register(XAJAX_FUNCTION,'saveORAccommodation'); //added by CHA, April 11, 2010 *function copied from billing
	$xajax->register(XAJAX_FUNCTION,'delOpAccommodation'); //added by CHA, April 12, 2010 *function copied from billing
	$xajax->register(XAJAX_FUNCTION,'delAllOpAccommodation'); //added by CHA, April 12, 2010
	$xajax->register(XAJAX_FUNCTION,'saveAdditionalRoomCharge'); //added by CHA, September 17, 2010
	$xajax->register(XAJAX_FUNCTION,'populate_room_accommodation'); //added by CHA, September 29, 2010
	$xajax->register(XAJAX_FUNCTION,'deleteRoomAccommodation'); //added by CHA, September 29, 2010