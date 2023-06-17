<?php

	function setRegion($regionID='') {
		#global $DB;
		global $address_region, $address_prov, $address_municity, $address_brgy;

		$objResponse = new xajaxResponse();

#		$objResponse->addAlert("setRegion: regionID = '$regionID'");
			# sets the condition for retrieving the list of barangays where region_nr=$regionID
		$where=" AS t, seg_municity AS t1 , seg_provinces AS t2, seg_regions AS t3 ".
				 " WHERE t.mun_nr=t1.mun_nr AND t1.prov_nr=t2.prov_nr ".
				 " AND t2.region_nr=t3.region_nr AND t3.region_nr=".$regionID;
		$rs_brgy=$address_brgy->getAllAddress($where);
			# sets the condition for retrieving the list of barangays where region_nr=$regionID
		$where=" AS t1 , seg_provinces AS t2, seg_regions AS t3 ".
				 " WHERE t1.prov_nr=t2.prov_nr ".
				 " AND t2.region_nr=t3.region_nr AND t3.region_nr=".$regionID;
		$rs_municity=$address_municity->getAllAddress($where);
		$rs_prov=$address_prov->getAllAddress(' WHERE region_nr='.$regionID);
#		$rs_region=$address_region->getAddressInfo($regionID,TRUE);
/*
		$objResponse->addAlert("setRegion: rs_brgy = '$rs_brgy'");
		$objResponse->addAlert("setRegion: address_brgy->sql = '$address_brgy->sql'");
		$objResponse->addAlert("setRegion: rs_municity = '$rs_municity'");
		$objResponse->addAlert("setRegion: address_municity->sql = '$address_municity->sql'");
		$objResponse->addAlert("setRegion: rs_prov = '$rs_prov'");
		$objResponse->addAlert("setRegion: address_prov->sql = '$address_prov->sql'");
*/
#		$objResponse->addAlert("setRegion: rs_region = '$rs_region'");
#		$objResponse->addAlert("setRegion: address_region->sql = '$address_region->sql'");
			# clears the list of barangays
		$objResponse->addScriptCall("ajxClearAddress","brgy_nr");
		if ($rs_brgy){
				# barangay's default name
			$objResponse->addScriptCall("ajxAddAddress","brgy_nr","-Select Barangay-",0);
				# fills up the list of barangays where mun_nr=$municityID
			while ($result=$rs_brgy->FetchRow()) {				
				$msg=" result['brgy_nr'] = '".$result['brgy_nr']."' \n ";
				$msg.=" result['brgy_name'] = '".$result['brgy_name']."' \n ";
#				$objResponse->addAlert("Province (rs_brgy): $msg");													 
				$objResponse->addScriptCall("ajxAddAddress","brgy_nr",$result['brgy_name'],$result['brgy_nr']);
			}		
		} else {
				# NO list of barangays retrieved where mun_nr=$municityID
			$objResponse->addScriptCall("ajxAddAddress","brgy_nr","-No Barangay Available-",0);
		}
			# clears the list of municipalities/cities
		$objResponse->addScriptCall("ajxClearAddress","mun_nr");
			# clears the list of zipcodes
		$objResponse->addScriptCall("ajxClearAddress","zipcode");
		if ($rs_municity){
				# municipality/city's default name
			$objResponse->addScriptCall("ajxAddAddress","mun_nr","-Select Municipality/City-",0);
				# zipcode's default name
			$objResponse->addScriptCall("ajxAddAddress","zipcode","-Select Zip Code-",0);
				# fills up the list of municipalities/cities where prov_nr=$provID
			while ($result=$rs_municity->FetchRow()) {				
				$msg=" result['mun_nr'] = '".$result['mun_nr']."' \n ";
				$msg.=" result['mun_name'] = '".$result['mun_name']."' \n ";
				$msg.=" result['zipcode'] = '".$result['zipcode']."' \n ";
#				$objResponse->addAlert("Province (rs_municity): $msg");													 
				$objResponse->addScriptCall("ajxAddAddress","mun_nr",$result['mun_name'],$result['mun_nr']);
				$objResponse->addScriptCall("ajxAddAddress","zipcode",$result['zipcode'],$result['zipcode']);
			}		
		} else {
				# NO list of municipalities/cities retrieved where prov_nr=$provID
			$objResponse->addScriptCall("ajxAddAddress","mun_nr","-No Municipality/City Available-",0);
				# NO list of zip codes retrieved where prov_nr=$provID
			$objResponse->addScriptCall("ajxAddAddress","zipcode","-No Zip Code Available-",0);
		}
			# clears the list of provinces
		$objResponse->addScriptCall("ajxClearAddress","prov_nr");
		if ($rs_prov){
				# province's default name
			$objResponse->addScriptCall("ajxAddAddress","prov_nr","-Select Province-",0);
				# fills up the list of municipalities/cities where prov_nr=$provID
			while ($result=$rs_prov->FetchRow()) {				
				$msg=" result['prov_nr'] = '".$result['prov_nr']."' \n ";
				$msg.=" result['prov_name'] = '".$result['prov_name']."' \n ";
#				$objResponse->addAlert("Province (rs_municity): $msg");													 
				$objResponse->addScriptCall("ajxAddAddress","prov_nr",$result['prov_name'],$result['prov_nr']);
			}		
		} else {
				# NO list of provinces retrieved where region_nr=$regionID
			$objResponse->addScriptCall("ajxAddAddress","prov_nr","-No Province Available-",0);
		}
		return $objResponse;
	}/* end of function setRegion */

	function setProvince($provID='') {
		#global $DB;
		global $address_prov, $address_municity, $address_brgy;

		$objResponse = new xajaxResponse();

#		$objResponse->addAlert("setProvince: provID = '$provID'");
			# sets the condition for retrieving the list of barangays where prov_nr=$provID
		$where=" AS t, seg_municity AS t1 , seg_provinces AS t2 ".
				 " WHERE t.mun_nr=t1.mun_nr AND t1.prov_nr=t2.prov_nr AND t2.prov_nr=".$provID;
		$rs_brgy=$address_brgy->getAllAddress($where);
		$rs_municity=$address_municity->getAllAddress(' WHERE prov_nr='.$provID);
		$rs_prov=$address_prov->getAddressInfo($provID,TRUE);
/*
		$objResponse->addAlert("setProvince: rs_brgy = '$rs_brgy'");
		$objResponse->addAlert("setProvince: address_brgy->sql = '$address_brgy->sql'");
		$objResponse->addAlert("setProvince: rs_municity = '$rs_municity'");
		$objResponse->addAlert("setProvince: address_municity->sql = '$address_municity->sql'");
		$objResponse->addAlert("setProvince: rs_prov = '$rs_prov'");
		$objResponse->addAlert("setProvince: address_prov->sql = '$address_prov->sql'");
*/
			# clears the list of barangays
		$objResponse->addScriptCall("ajxClearAddress","brgy_nr");
		if ($rs_brgy){
				# barangay's default name
			$objResponse->addScriptCall("ajxAddAddress","brgy_nr","-Select Barangay-",0);
				# fills up the list of barangays where mun_nr=$municityID
			while ($result=$rs_brgy->FetchRow()) {				
				$msg=" result['brgy_nr'] = '".$result['brgy_nr']."' \n ";
				$msg.=" result['brgy_name'] = '".$result['brgy_name']."' \n ";
#				$objResponse->addAlert("Province (rs_brgy): $msg");													 
				$objResponse->addScriptCall("ajxAddAddress","brgy_nr",$result['brgy_name'],$result['brgy_nr']);
			}		
		} else {
				# NO list of barangays retrieved where mun_nr=$municityID
			$objResponse->addScriptCall("ajxAddAddress","brgy_nr","-No Barangay Available-",0);
		}
			# clears the list of municipalities/cities
		$objResponse->addScriptCall("ajxClearAddress","mun_nr");
			# clears the list of zipcodes
		$objResponse->addScriptCall("ajxClearAddress","zipcode");
		if ($rs_municity){
				# municipality/city's default name
			$objResponse->addScriptCall("ajxAddAddress","mun_nr","-Select Municipality/City-",0);
				# zipcode's default name
			$objResponse->addScriptCall("ajxAddAddress","zipcode","-Select Zip Code-",0);
				# fills up the list of municipalities/cities where prov_nr=$provID
			while ($result=$rs_municity->FetchRow()) {				
				$msg=" result['mun_nr'] = '".$result['mun_nr']."' \n ";
				$msg.=" result['mun_name'] = '".$result['mun_name']."' \n ";
				$msg.=" result['zipcode'] = '".$result['zipcode']."' \n ";
#				$objResponse->addAlert("Province (rs_municity): $msg");													 
				$objResponse->addScriptCall("ajxAddAddress","mun_nr",$result['mun_name'],$result['mun_nr']);
				$objResponse->addScriptCall("ajxAddAddress","zipcode",$result['zipcode'],$result['zipcode']);
			}		
		} else {
				# NO list of municipalities/cities retrieved where prov_nr=$provID
			$objResponse->addScriptCall("ajxAddAddress","mun_nr","-No Municipality/City Available-",0);
				# NO list of zip codes retrieved where prov_nr=$provID
			$objResponse->addScriptCall("ajxAddAddress","zipcode","-No Zip Code Available-",0);
		}
		if ($rs_prov) {
				# retrieves the province's information
			$result=$rs_prov->FetchRow();
			$msg=" result['region_nr'] = '".$result['region_nr']."' \n ";
#			$objResponse->addAlert("Province (rs_municity->FetchRow) : $msg");

			$rs_prov_list=$address_prov->getAllAddress(' WHERE region_nr='.$result['region_nr']);
				# clears the list of provinces
			$objResponse->addScriptCall("ajxClearAddress","prov_nr");
			if ($rs_prov_list){
					# province's default name
				$objResponse->addScriptCall("ajxAddAddress","prov_nr","-Select Province-",0);
					# fills up the list of municipalities/cities where prov_nr=$provID
				while ($result_inner=$rs_prov_list->FetchRow()) {				
					$msg=" result_inner['prov_nr'] = '".$result_inner['prov_nr']."' \n ";
					$msg.=" result_inner['prov_name'] = '".$result_inner['prov_name']."' \n ";
	#				$objResponse->addAlert("Province (rs_prov_list): $msg");													 
					$objResponse->addScriptCall("ajxAddAddress","prov_nr",$result_inner['prov_name'],$result_inner['prov_nr']);
				}		
				# set the region's name of the selected municipality/city
				$objResponse->addScriptCall("setByProvince",$result['region_nr'],$result['prov_nr']);
			} else {
					# NO list of provinces retrieved where region_nr=$regionID
				$objResponse->addScriptCall("ajxAddAddress","prov_nr","-No Province Available-",0);
			}
		}
		else {
			$objResponse->addAlert("Error retrieving province information...");
		}
		return $objResponse;
	}/* end of function setProvince */


	function setMuniCity($municityID='') {
		#global $DB;
		global $address_municity, $address_brgy;

		$objResponse = new xajaxResponse();

#		$objResponse->addAlert("setMuniCity: municityID = '$municityID'");
		$rs_brgy=$address_brgy->getAllAddress(' WHERE mun_nr='.$municityID);
		$rs_municity=$address_municity->getAddressInfo($municityID,TRUE);
/*
		$objResponse->addAlert("setMuniCity: rs_brgy = '$rs_brgy'");
		$objResponse->addAlert("setMuniCity: address_brgy->sql = '$address_brgy->sql'");
		$objResponse->addAlert("setMuniCity: rs_municity = '$rs_municity'");
		$objResponse->addAlert("setMuniCity: address_municity->sql = '$address_municity->sql'");
*/
			# clears the list of barangays
		$objResponse->addScriptCall("ajxClearAddress","brgy_nr");
		if ($rs_brgy){
				# barangay's default name
			$objResponse->addScriptCall("ajxAddAddress","brgy_nr","-Select Barangay-",0);
				# fills up the list of barangays where mun_nr=$municityID
			while ($result=$rs_brgy->FetchRow()) {				
				$msg=" result['brgy_nr'] = '".$result['brgy_nr']."' \n ";
				$msg.=" result['brgy_name'] = '".$result['brgy_name']."' \n ";
#				$objResponse->addAlert("setMuniCity (rs_brgy): $msg");													 
				$objResponse->addScriptCall("ajxAddAddress","brgy_nr",$result['brgy_name'],$result['brgy_nr']);
			}		
		} else {
				# NO list of barangays retrieved where mun_nr=$municityID
			$objResponse->addScriptCall("ajxAddAddress","brgy_nr","-No Barangay Available-",0);
		}
		if ($rs_municity) {
/*
				# set the region's name, and province's name
				# of the selected municipality/city
			$objResponse->addAlert("inside if ($rs_municity) ");
			while ($result=$rs_municity->FetchRow()) {				
				$msg=" result['region_nr'] = '".$result['region_nr']."' \n ";
				$msg.=" result['prov_nr'] = '".$result['prov_nr']."' \n ";
				$msg.=" result['zipcode'] = '".$result['zipcode']."' \n ";
				$objResponse->addAlert("setMuniCity (rs_municity->FetchRow) : $msg");
				$objResponse->addScriptCall("setByMuniCity",$result['region_nr'],$result['prov_nr'],$result['zipcode']);
			}
*/
				# retrieves the province's information
			$result=$rs_municity->FetchRow();
			$rs_municity_list=$address_municity->getAllAddress(' WHERE prov_nr='.$result['prov_nr']);

				# clears the list of municipalities/cities
			$objResponse->addScriptCall("ajxClearAddress","mun_nr");
				# clears the list of zipcodes
			$objResponse->addScriptCall("ajxClearAddress","zipcode");

			if ($rs_municity_list){
					# municipality/city's default name
				$objResponse->addScriptCall("ajxAddAddress","mun_nr","-Select Municipality/City-",0);
					# zipcode's default name
				$objResponse->addScriptCall("ajxAddAddress","zipcode","-Select Zip Code-",0);
					# fills up the list of municipalities/cities where prov_nr=$provID
				while ($result_inner=$rs_municity_list->FetchRow()) {				
					$msg=" result_inner['mun_nr'] = '".$result_inner['mun_nr']."' \n ";
					$msg.=" result_inner['mun_name'] = '".$result_inner['mun_name']."' \n ";
					$msg.=" result_inner['zipcode'] = '".$result_inner['zipcode']."' \n ";
#					$objResponse->addAlert("MuniCity (rs_municity): $msg");													 
					$objResponse->addScriptCall("ajxAddAddress","mun_nr",$result_inner['mun_name'],$result_inner['mun_nr']);
					$objResponse->addScriptCall("ajxAddAddress","zipcode",$result_inner['zipcode'],$result_inner['zipcode']);
				}		
				# set the region's name of the selected municipality/city
				$objResponse->addScriptCall("setByMuniCity",$result['region_nr'],$result['prov_nr'],$result['mun_nr'],$result['zipcode']);
			} else {
					# NO list of municipalities/cities retrieved where prov_nr=$provID
				$objResponse->addScriptCall("ajxAddAddress","mun_nr","-No Municipality/City Available-",0);
					# NO list of zip codes retrieved where prov_nr=$provID
				$objResponse->addScriptCall("ajxAddAddress","zipcode","-No Zip Code Available-",0);
			}
		}
		else {
			$objResponse->addAlert("Error retrieving municipality/city information...");
		}
		return $objResponse;
	}/* end of function setMuniCity */

	function setZipcode($zipcode='') {
		#global $DB;
		global $address_municity, $address_zipcode, $address_brgy;

		$objResponse = new xajaxResponse();

		$objResponse->addAlert("setZipcode: zipcode = '$zipcode'");
		$rs_zipcode=$address_zipcode->getAllAddress(' WHERE zipcode='.$zipcode);
		
		$result=$rs_zipcode->FetchRow();
/*
		$objResponse->addAlert("setZipcode: result = '$result'");
		$objResponse->addAlert("setZipcode: rs_zipcode = '$rs_zipcode'");
		$objResponse->addAlert("setZipcode: address_zipcode->sql = '$address_zipcode->sql'");
*/		
		$rs_municity=$address_municity->getAddressInfo($result['mun_nr'],TRUE);
		$rs_brgy=$address_brgy->getAllAddress(' WHERE mun_nr='.$result['mun_nr']);
/*
		$objResponse->addAlert("setZipcode: rs_brgy = '$rs_brgy'");
		$objResponse->addAlert("setZipcode: address_brgy->sql = '$address_brgy->sql'");
		$objResponse->addAlert("setZipcode: rs_municity = '$rs_municity'");
		$objResponse->addAlert("setZipcode: address_municity->sql = '$address_municity->sql'");
*/
			# clears the list of barangays
		$objResponse->addScriptCall("ajxClearAddress","brgy_nr");
		if ($rs_brgy){
				# barangay's default name
			$objResponse->addScriptCall("ajxAddAddress","brgy_nr","-Select Barangay-",0);
				# fills up the list of barangays where mun_nr=$municityID
			while ($result=$rs_brgy->FetchRow()) {				
				$msg=" result['brgy_nr'] = '".$result['brgy_nr']."' \n ";
				$msg.=" result['brgy_name'] = '".$result['brgy_name']."' \n ";
#				$objResponse->addAlert("setMuniCity (rs_brgy): $msg");													 
				$objResponse->addScriptCall("ajxAddAddress","brgy_nr",$result['brgy_name'],$result['brgy_nr']);
			}		
		} else {
				# NO list of barangays retrieved where mun_nr=$municityID
			$objResponse->addScriptCall("ajxAddAddress","brgy_nr","-No Barangay Available-",0);
		}
		if ($rs_municity) {
				# set the region's name, and province's name
				# of the selected municipality/city
			while ($result=$rs_municity->FetchRow()) {				
				$msg=" result['region_nr'] = '".$result['region_nr']."' \n ";
				$msg.=" result['prov_nr'] = '".$result['prov_nr']."' \n ";
				$msg.=" result['mun_nr'] = '".$result['mun_nr']."' \n ";
#				$objResponse->addAlert("setByZipcode (rs_municity->FetchRow) : $msg");
				$objResponse->addScriptCall("setByZipcode",$result['region_nr'], $result['prov_nr'],$result['mun_nr']);
			}
		}
		else {
			$objResponse->addAlert("Error retrieving zip code information...");
		}
		return $objResponse;
	}/* end of function setZipcode */


	function setBarangay($brgyID='') {
		#global $DB;
		global $address_brgy;

		$objResponse = new xajaxResponse();

#		$objResponse->addAlert("setBarangay: brgyID = '$brgyID'");
		$rs=$address_brgy->getAddressInfo($brgyID,TRUE);
		if ($rs) {
			# $objResponse->addScriptCall("ajxClearOptions");
				# set the region's name, province's name, 
				# and municipality/city's name of the selected barangay
			while ($result=$rs->FetchRow()) {				
				$msg=" result['region_nr'] = '".$result['region_nr']."' \n ";
				$msg.=" result['prov_nr'] = '".$result['prov_nr']."' \n ";
				$msg.=" result['mun_nr'] = '".$result['mun_nr']."'";
#				$objResponse->addAlert("setBarangay: $msg");
				$objResponse->addScriptCall("setByBarangay",$result['region_nr'], $result['prov_nr'], $result['mun_nr'],$result['zipcode']);
			}
			$rs=$address_brgy->getAddressInfo($brgyID,TRUE);
			if ($rs){
				$objResponse->addScriptCall("setByBarangay",$result['region_nr'], $result['prov_nr'], $result['mun_nr'],$result['zipcode']);				
			}
		}
		else {
			$objResponse->addAlert("Error retrieving barangay information...");
		}
		return $objResponse;
	}/* end of function setBarangay */

/*
*/
	/**
	* Fills up the option.
	* NOTE: To invoke this function
	*			$this->fillUpOption(<arg1>,<arg2>,<arg3>);	
	* @param object, the instance of 'xajaxResponse()' 
	* @param object, the instance of the class 
	*			($address_region, $address_prov, $address_municity, OR $address_brgy) 
	* @param string, the option id/name to fill (region_nr, prov_nr, mun_nr, zipcode, OR brgy_nr)
	* @param string, the condition in retrieving the information
	*/
	function fillUpOption(&$objResponse, $address_obj, $option2fill, $cond){
#		$objResponse = new xajaxResponse();

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

$objResponse->addAlert("fillUpOption: \n location = '$location' \n value = '$value' \n text = '$text' \n msg = '$msg'");
		$rs=$address_obj->getAllAddress($cond);
$objResponse->addAlert("fillUpOption: $address_obj->sql");
			# clears the list of barangays
		$objResponse->addScriptCall("ajxClearAddress",$option2fill);
		if ($rs) {
				# default name
			$objResponse->addScriptCall("ajxAddAddress",$option2fill,"-Select $msg-",0);
				# set the region's name, province's name, municipality/city's name, 
				# zip code, OR barangay's name of the selected option
			while ($result=$rs->FetchRow()) {				
$objResponse->addAlert("setAll: result[$text] = '$result[$text]'; result[$value] = '$result[$value]'");
				$objResponse->addScriptCall("ajxAddAddress",$option2fill,$result[$text],$result[$value]);
			}
		}
		else {
				# NO list of $msg retrieved
			$objResponse->addScriptCall("ajxAddAddress",$option2fill,"-No $msg Available-",0);
			#$objResponse->addAlert("Error retrieving address information...");
		}
#		return $objResponse;
	}/* end of function fillUpOption */

	function setAll($location='barangay',$region_nr=0,$prov_nr=0,$mun_nr=0) {
		#global $DB;
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
		} elseif ($location=='municity'){
			$address_obj = $address_municity;
			$option2fill = "mun_nr";
			$msg = "Municipality/City";
			if ($region_nr){
				$select.="AS t1, seg_provinces AS t2";
				$where.=" WHERE t2.region_nr=".$region_nr;
			}
			if ($prov_nr){
				$where.=" WHERE prov_nr=".$prov_nr;
			}
/*
			if (($region_nr) || ($prov_nr)) {
				$select=" AS t1 ";
				if ($region_nr){
					$select.=", seg_provinces AS t2";
					$where.=" WHERE t2.region_nr=".$region_nr;
				}else{
					$where.=" WHERE 1 ";					
				}
				if ($prov_nr)
					$where.=" AND t1.prov_nr=".$prov_nr;
				else
					$where.=" AND t1.prov_nr=t2.prov_nr ";
			}
*/
		} elseif ($location=='zipcode'){
			$address_obj = $address_zipcode;
			$option2fill = "zipcode";
			$msg = "Zip Code";
			if ($region_nr){
				$select.="AS t1, seg_provinces AS t2";
				$where.=" WHERE t2.region_nr=".$region_nr;
			}
			if ($prov_nr){
				$where.=" WHERE prov_nr=".$prov_nr;
			}
/*
			if (($region_nr) || ($prov_nr)) {
				
				$select=" AS t1 ";
				if ($region_nr){
					$select.=", seg_provinces AS t2";
					$where.=" WHERE t2.region_nr=".$region_nr;
				}else{
					$where.=" WHERE 1 ";					
				}
				if ($prov_nr)
					$where.=" AND t1.prov_nr=".$prov_nr;
				else
					$where.=" AND t1.prov_nr=t2.prov_nr ";
			}
*/
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
				#select.=" AS t1, seg_municity AS t2 "
				$where.=" WHERE mun_nr=".$mun_nr;
			}
/*			
			if (($region_nr) || ($prov_nr) || ($mun_nr)) {
				$select=" AS t1";
				$where=" WHERE ";
				if ($region_nr){
					$select.=", seg_provinces AS t3 ";
					$where.=" t3.region_nr=".$region_nr;
				}else{
					$where.=" 1 ";					
				}
				$select.=", seg_municity AS t2 ";
				if ($prov_nr){
					$where.=" AND t2.prov_nr=".$prov_nr;
				}else{
					$where.=" AND t2.prov_nr=t3.prov_nr ";
#					$where.=" AND 1 ";
				}
				if ($mun_nr)
					$where.=" AND t1.mun_nr=".$mun_nr;
				else
					$where.=" AND t1.mun_nr=t2.mun_nr ";
			}
*/
		}

		$value = $address_obj->fld_primary_key;
		$text = $address_obj->fld_primary_name;
		if ($location=='zipcode'){
			$value = 'zipcode';
			$text = 'zipcode';
		}

		fillUpOption($objResponse,$address_obj,$option2fill,"$select $where");
/*
$objResponse->addAlert("setAll: \n location = '$location' \n value = '$value' \n text = '$text' \n msg = '$msg'");
		$rs=$address_obj->getAllAddress($select." ".$where);
$objResponse->addAlert("setAll: $address_obj->sql");
			# clears the list of barangays
		$objResponse->addScriptCall("ajxClearAddress",$option2fill);
		if ($rs) {
				# default name
			$objResponse->addScriptCall("ajxAddAddress",$option2fill,"-Select $msg-",0);
				# set the region's name, province's name, 
				# and municipality/city's name of the selected barangay
			while ($result=$rs->FetchRow()) {				
$objResponse->addAlert("setAll: result[$text] = '$result[$text]'; result[$value] = '$result[$value]'");
				$objResponse->addScriptCall("ajxAddAddress",$option2fill,$result[$text],$result[$value]);
			}
		}
		else {
				# NO list of $msg retrieved
			$objResponse->addScriptCall("ajxAddAddress",$option2fill,"-No $msg Available-",0);
			#$objResponse->addAlert("Error retrieving address information...");
		}
*/
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