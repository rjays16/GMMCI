<?php

	function setRegion($regionID='') {
		global $address_region, $address_prov, $address_municity, $address_zipcode, $address_brgy;

		$objResponse = new xajaxResponse();

		fillUpOption($objResponse,$address_prov,"prov_nr","WHERE region_nr=".$regionID);
			# sets the condition for retrieving the list of municipalities/cities where region_nr=$regionID
		$where=" AS t1 , seg_provinces AS t2, seg_regions AS t3 ".
				 " WHERE t1.prov_nr=t2.prov_nr ".
				 " AND t2.region_nr=t3.region_nr AND t3.region_nr=".$regionID;
		fillUpOption($objResponse,$address_municity,"mun_nr",$where);
		fillUpOption($objResponse,$address_zipcode,"zipcode",$where);
			# sets the condition for retrieving the list of barangays where region_nr=$regionID
		$where=" AS t, seg_municity AS t1 , seg_provinces AS t2, seg_regions AS t3 ".
				 " WHERE t.mun_nr=t1.mun_nr AND t1.prov_nr=t2.prov_nr ".
				 " AND t2.region_nr=t3.region_nr AND t3.region_nr=".$regionID;
		fillUpOption($objResponse,$address_brgy,"brgy_nr",$where);

			# sets the region's name of the selected region
		$objResponse->addScriptCall("setByRegion",$regionID);

		return $objResponse;
	}/* end of function setRegion */

	function setProvince($provID='') {
		global $address_region, $address_prov, $address_municity, $address_zipcode, $address_brgy;

		$objResponse = new xajaxResponse();

		$rs_prov=$address_prov->getAddressInfo($provID,TRUE);
		if ($rs_prov) {
			$result=$rs_prov->FetchRow();

			$msg=" result['region_nr'] = '".$result['region_nr']."' \n ";
			$msg.=" result['prov_nr'] = '".$result['prov_nr']."' \n ";
			$msg.=" result['mun_nr'] = '".$result['mun_nr']."' \n";
			$msg.=" result['brgy_nr'] = '".$result['brgy_nr']."'";
#			$objResponse->addAlert("setProvince: $msg");

			fillUpOption($objResponse,$address_prov,"prov_nr","WHERE region_nr=".$result['region_nr']);
			fillUpOption($objResponse,$address_municity,"mun_nr","WHERE prov_nr=".$result['prov_nr']);
			fillUpOption($objResponse,$address_zipcode,"zipcode","WHERE prov_nr=".$result['prov_nr']);
				# sets the condition for retrieving the list of barangays where prov_nr=$provID
			$where=" AS t, seg_municity AS t1 , seg_provinces AS t2 ".
					 " WHERE t.mun_nr=t1.mun_nr AND t1.prov_nr=t2.prov_nr AND t2.prov_nr=".$provID;
			fillUpOption($objResponse,$address_brgy,"brgy_nr",$where);

				# sets the region's, and province's name of the selected province
			$objResponse->addScriptCall("setByProvince",$result['region_nr'],$result['prov_nr']);
		} else {
			$objResponse->addAlert("Error retrieving municipality/city information...");
		}

		return $objResponse;
	}/* end of function setProvince */


	function setMuniCity($municityID='') {
		global $address_region, $address_prov, $address_municity, $address_zipcode, $address_brgy;

		$objResponse = new xajaxResponse();

		$rs_municity=$address_municity->getAddressInfo($municityID,TRUE);
		if ($rs_municity) {
			$result=$rs_municity->FetchRow();

			$msg=" result['region_nr'] = '".$result['region_nr']."' \n ";
			$msg.=" result['prov_nr'] = '".$result['prov_nr']."' \n ";
			$msg.=" result['mun_nr'] = '".$result['mun_nr']."' \n";
			$msg.=" result['brgy_nr'] = '".$result['brgy_nr']."'";
#			$objResponse->addAlert("setMuniCity: $msg");

			fillUpOption($objResponse,$address_prov,"prov_nr","WHERE region_nr=".$result['region_nr']);
			fillUpOption($objResponse,$address_municity,"mun_nr","WHERE prov_nr=".$result['prov_nr']);
			fillUpOption($objResponse,$address_zipcode,"zipcode","WHERE prov_nr=".$result['prov_nr']);
			fillUpOption($objResponse,$address_brgy,"brgy_nr","WHERE mun_nr=".$result['mun_nr']);

				# sets the region's, province's, and municipality/city's name, and
				# zip code of the selected municipality/city
			$objResponse->addScriptCall("setByMuniCity",$result['region_nr'],$result['prov_nr'],$result['mun_nr'],$result['zipcode']);
		} else {
			$objResponse->addAlert("Error retrieving municipality/city information...");
		}

		return $objResponse;
	}/* end of function setMuniCity */

	function setZipcode($zipcode='') {
		global $address_region, $address_prov, $address_municity, $address_zipcode, $address_brgy;

		$objResponse = new xajaxResponse();

		$rs_zipcode=$address_zipcode->getAllAddress(' WHERE zipcode='.$zipcode);
		if ($rs_zipcode) {
			$result_zip=$rs_zipcode->FetchRow();
			$rs_municity=$address_municity->getAddressInfo($result_zip['mun_nr'],TRUE);

			if ($rs_municity) {
				$result=$rs_municity->FetchRow();

				$msg=" result['region_nr'] = '".$result['region_nr']."' \n ";
				$msg.=" result['prov_nr'] = '".$result['prov_nr']."' \n ";
				$msg.=" result['mun_nr'] = '".$result['mun_nr']."' \n";
				$msg.=" result['brgy_nr'] = '".$result['brgy_nr']."'";
#				$objResponse->addAlert("setZipcode: $msg");

				fillUpOption($objResponse,$address_prov,"prov_nr","WHERE region_nr=".$result['region_nr']);
				fillUpOption($objResponse,$address_municity,"mun_nr","WHERE prov_nr=".$result['prov_nr']);
				fillUpOption($objResponse,$address_zipcode,"zipcode","WHERE prov_nr=".$result['prov_nr']);
				fillUpOption($objResponse,$address_brgy,"brgy_nr","WHERE mun_nr=".$result['mun_nr']);

					# sets the region's, province's, and municipality/city's name, and
					# zip code of the selected zip code
				$objResponse->addScriptCall("setByZipcode",$result['region_nr'], $result['prov_nr'],$result['mun_nr'],$result['zipcode']);
			} else {
				$objResponse->addAlert("Error retrieving zip code information...");
			}
		} else {
			$objResponse->addAlert("Error retrieving zip code information...");
		}

		return $objResponse;
	}/* end of function setZipcode */


	function setBarangay($brgyID='') {
		global $address_region, $address_prov, $address_municity, $address_zipcode, $address_brgy;

		$objResponse = new xajaxResponse();

		$rs=$address_brgy->getAddressInfo($brgyID,TRUE);
		if ($rs) {
			$rs=$address_brgy->getAddressInfo($brgyID,TRUE);
			if ($rs){
				$result=$rs->FetchRow();
				$msg=" result['region_nr'] = '".$result['region_nr']."' \n ";
				$msg.=" result['prov_nr'] = '".$result['prov_nr']."' \n ";
				$msg.=" result['mun_nr'] = '".$result['mun_nr']."' \n";
				$msg.=" result['brgy_nr'] = '".$result['brgy_nr']."'";
#				$objResponse->addAlert("setBarangay: $msg");

				fillUpOption($objResponse,$address_region,"region_nr");
				fillUpOption($objResponse,$address_prov,"prov_nr","WHERE region_nr=".$result['region_nr']);
				fillUpOption($objResponse,$address_municity,"mun_nr","WHERE prov_nr=".$result['prov_nr']);
				fillUpOption($objResponse,$address_zipcode,"zipcode","WHERE prov_nr=".$result['prov_nr']);
				fillUpOption($objResponse,$address_brgy,"brgy_nr","WHERE mun_nr=".$result['mun_nr']);

					# sets the region's, province's, municipality/city's, 
					# and barangay's name, and zip code of the selected barangay
				$objResponse->addScriptCall("setByBarangay",$result['region_nr'], $result['prov_nr'], $result['mun_nr'],$result['zipcode'],$result['brgy_nr']);	
			}
		}
		else {
			$objResponse->addAlert("Error retrieving barangay information...");
		}

		return $objResponse;
	}/* end of function setBarangay */

	/**
	* Fills up the option.
	* NOTE: To invoke this function
	*			fillUpOption(<arg1>,<arg2>,<arg3>,<arg4>);	
	* @param object, the instance of 'xajaxResponse()' 
	* @param object, the instance of the class 
	*			($address_region, $address_prov, $address_municity, OR $address_brgy) 
	* @param string, the option id/name to fill (region_nr, prov_nr, mun_nr, zipcode, OR brgy_nr)
	* @param string, the condition in retrieving the information
	*/
	function fillUpOption(&$objResponse, $address_obj, $option2fill, $cond=''){

		if ($option2fill=="region_nr"){
			$msg="Region";
		}elseif ($option2fill=="prov_nr"){
			$msg="Province";
		}elseif ($option2fill=="mun_nr"){
			$msg="Municipality/City";
		}elseif ($option2fill=="zipcode"){
			$msg="Zip Code";
		}else{
			$msg="Barangay";
		}

		$value = $address_obj->fld_primary_key;
		$text = $address_obj->fld_primary_name;
		if ($option2fill=='zipcode'){
			$value = 'zipcode';
			$text = 'zipcode';
		}

			# clears the list of option to be filled up
		$objResponse->addScriptCall("ajxClearAddress",$option2fill);

#$objResponse->addAlert("fillUpOption: \n option2fill = '$option2fill' \n value = '$value' \n text = '$text' \n msg = '$msg'");
		$rs=$address_obj->getAllAddress($cond);
#$objResponse->addAlert("fillUpOption: $address_obj->sql");
		if ($rs) {
				# sets the default name
			$objResponse->addScriptCall("ajxAddAddress",$option2fill,"-Select $msg-",0);
				# fills up the list of region's name, province's name, municipality/city's name, 
				# zip code, OR barangay's name of the selected option
			while ($result=$rs->FetchRow()) {				
#$objResponse->addAlert("setAll: result[$text] = '$result[$text]'; result[$value] = '$result[$value]'");
				$objResponse->addScriptCall("ajxAddAddress",$option2fill,$result[$text],$result[$value]);
			}
		} else {
				# NO list of $msg retrieved
			$objResponse->addScriptCall("ajxAddAddress",$option2fill,"-No $msg Available-",0);
		}
	}/* end of function fillUpOption */

	function setAll($location='barangay',$region_nr=0,$prov_nr=0,$mun_nr=0) {
		global $address_region, $address_prov, $address_municity, $address_zipcode, $address_brgy;

		$objResponse = new xajaxResponse();

		$select="";
		$where="";
		if ($location=='region'){
			$address_obj = $address_region;
			$option2fill = "region_nr";
			$msg = "Region";
		} elseif ($location=='province'){
			$address_obj = $address_prov;
			$option2fill = "prov_nr";
			$msg = "Province";
			if ($region_nr){
				$where.=" WHERE region_nr=".$region_nr;
			}
		} elseif ($location=='municity'){
			$address_obj = $address_municity;
			$option2fill = "mun_nr";
			$msg = "Municipality/City";
			if ($region_nr){
				$select.="AS t1, seg_provinces AS t2";
				$where.=" WHERE t2.region_nr=".$region_nr.
						  " AND t1.prov_nr=t2.prov_nr";
			}
			if ($prov_nr){
				$where.=" WHERE prov_nr=".$prov_nr;
			}
		} elseif ($location=='zipcode'){
			$address_obj = $address_zipcode;
			$option2fill = "zipcode";
			$msg = "Zip Code";
			if ($region_nr){
				$select.="AS t1, seg_provinces AS t2";
				$where.=" WHERE t2.region_nr=".$region_nr.
						  " AND t1.prov_nr=t2.prov_nr";
			}
			if ($prov_nr){
				$where.=" WHERE prov_nr=".$prov_nr;
			}
		}else{
			$address_obj = $address_brgy;
			$option2fill = "brgy_nr";
			$msg = "Barangay";
			if ($region_nr){
				$select.=" AS t1, seg_municity AS t2, seg_provinces AS t3 ";
				$where.=" WHERE t3.region_nr=".$region_nr.
						  " AND t2.prov_nr=t3.prov_nr AND t1.mun_nr=t2.mun_nr ";
			}
			if ($prov_nr){
				$select.=" AS t1, seg_municity AS t2 ";
				$where.=" WHERE t2.prov_nr=".$prov_nr.
						  " AND t1.mun_nr=t2.mun_nr ";
			}
			if ($mun_nr){
				$where.=" WHERE mun_nr=".$mun_nr;
			}
		}

		$value = $address_obj->fld_primary_key;
		$text = $address_obj->fld_primary_name;
		if ($location=='zipcode'){
			$value = 'zipcode';
			$text = 'zipcode';
		}

		fillUpOption($objResponse,$address_obj,$option2fill,"$select $where");

		return $objResponse;
	}/* end of function setAll */
	
	
	$root_path="../../";
/*
	include($root_path."config.php");
	include($root_path."include/adodb/adodb.inc.php");
	include($root_path."include/constants.php");
	$DB = &ADONewConnection($DBType);
	$DB->Connect($DBHost, $DBUser, $DBPassword, $DBName);
*/	

require($root_path.'include/inc_environment_global.php');

	/* Create the helper class for the enrollment-term table */
	include_once($root_path.'include/care_api_classes/class_address.php');
	$address_region = new Address('region');
	$address_prov = new Address('province');
	$address_municity = new Address('municity');
	$address_zipcode = new Address('municity');
	$address_brgy = new Address('barangay');
		
	require("address.common.php");
	$xajax->processRequests();
?>