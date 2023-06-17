<?php
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require($root_path."modules/registration_admission/ajax/reg-insurance.common.php");
    #added by VAN 04-17-08
    require_once($root_path.'include/care_api_classes/class_globalconfig.php');
    require_once($root_path.'include/care_api_classes/class_encounter.php');
    require_once($root_path.'include/care_api_classes/class_person.php');
    require_once($root_path.'include/care_api_classes/class_insurance.php');
    #require_once($root_path.'include/care_api_classes/eclaims/eligibility/class_eligibilityprofile.php');
    #require_once($root_path.'include/care_api_classes/eclaims/aboutxml/XML2Array.php');    

	#edited by VAN 04-17-08
	function populateInsurance($sElem,$keyword, $pid, $page) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_insurance_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_insurance_search_max_block_rows'];

		$objResponse = new xajaxResponse();
		$insObj=new Insurance;
		$offset = $page * $maxRows;

		$searchkey = utf8_decode($searchkey);
		$total_srv = $insObj->countSearchSelect($keyword,$maxRows,$offset);
		#$objResponse->addAlert($insObj->sql);
		$total = $insObj->count;
		#$objResponse->addAlert('total = '.$total);
		#$objResponse->addAlert('ajax pid = '.$pid);

		$lastPage = floor($total/$maxRows);
		#$objResponse->addAlert('total = '.$lastPage);
		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;

		if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$insObj->SearchSelect($keyword,$maxRows,$offset);
		#$objResponse->addAlert("sql = ".$insObj->sql);
		$rows=0;

		$objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->call("clearList","product-list");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {

				#added by VAN 08-14-08
				$patient_Insinfo = $insObj->getPatientInsuranceInfo($pid, trim($result["hcare_id"]));
				#$objResponse->addAlert('patient_Insinfo id, prin = '.$patient_Insinfo['insurance_nr']." - ".$patient_Insinfo['is_principal']);
				/*
				if ($patient_Insinfo['insurance_nr'])
					$insurance_no = $patient_Insinfo['insurance_nr'];
				else
					$insurance_no = 0;

				if ($patient_Insinfo['insurance_nr'])
					$insurance_no = $patient_Insinfo['is_principal'];
				else
					$insurance_no = 0;
				*/

				#$objResponse->addScriptCall("addProductToList","product-list",trim($result["hcare_id"]),trim($result["firm_id"]),trim($result["name"]),$cnt);
				$objResponse->call("addProductToList","product-list",trim($result["hcare_id"]),trim($result["firm_id"]),trim($result["name"]),$patient_Insinfo['insurance_nr'], $patient_Insinfo['is_principal']);
			}#end of while
		} #end of if

		if (!$rows) $objResponse->call("addProductToList","product-list",NULL);
		if ($sElem) {
			$objResponse->call("endAJAXSearch",$sElem);
		}

		return $objResponse;
	}

  function check_holder_data($firm_id, $insurance_number) {
    $objResponse = new xajaxResponse();

    $person_insurance = new PersonInsurance();
    if ( preg_match('/te?mp/', $insurance_number) ) {
        $insurance_number .= time();
		}
		$pholder = $person_insurance->getPrincipalHolder($insurance_number, $firm_id);

//    $result = $person_insurance->is_holder_existing($firm_id, $insurance_number);

//    if (!$result) {
//      $objResponse->addScriptCall("open_member_insurance_details_info", $firm_id, $insurance_number);
//    }
//    else {
		if ($pholder) {
			$obj = (object) 'details';
			$obj->pid          = $pholder["pid"];
			$obj->last_name    = $pholder["last_name"];
			$obj->first_name   = $pholder["first_name"];
			$obj->middle_name  = $pholder["middle_name"];
			$obj->street       = $pholder["street"];
			$obj->insurance_nr = $insurance_number;
			$obj->infosrc      = 1;
//			$obj->barangay     = $pholder["barangay"];
//			$obj->municipality = $pholder["municipality"];
		}
		else {
			$obj = (object) 'details';
			$obj->pid          = '';
			$obj->last_name    = '';
			$obj->first_name   = '';
			$obj->middle_name  = '';
			$obj->street       = '';
			$obj->insurance_nr = $insurance_number;
			$obj->infosrc      = 2;
		}

//		$objResponse->addScriptCall("prepareAdd", $firm_id, ($b_isPrincipal == 'false'), $obj);

		$objResponse->call("verifyEligibility", $firm_id);

//    }

    return $objResponse;
  }

  function getMuniCityandProv($brgy_nr) {
        global $db;

        $objResponse = new xajaxResponse();

        $strSQL = "SELECT p.prov_nr, m.mun_nr, p.prov_name, m.mun_name \n
                      FROM (seg_barangays as b inner join seg_municity as m \n
                         on b.mun_nr = m.mun_nr) inner join seg_provinces as p \n
                         on m.prov_nr = p.prov_nr \n
                         where b.brgy_nr = $brgy_nr";

        if ($result = $db->Execute($strSQL)) {
            if ($row = $result->FetchRow()) {
                $objResponse->call("setMuniCity", (is_null($row['mun_nr']) ? 0 : $row['mun_nr']), (is_null($row['mun_name']) ? '' : $row['mun_name']));
                //$objResponse->call("setProvince", (is_null($row['prov_nr']) ? 0 : $row['prov_nr']), (is_null($row['prov_name']) ? '' : $row['prov_name']));
            }
        }

        return $objResponse;
    }

  function get_barangay_municipality_name($barangay_nr, $municipality_nr) {
    global $db;
    $objResponse = new xajaxResponse();
    $sql = "select (select brgy_name from seg_barangays where brgy_nr=$barangay_nr) as barangay,
            (select mun_name from seg_municity where mun_nr=$municipality_nr) as municipality";
    if ($result = $db->Execute($sql)) {
      if ($result->RecordCount() == 1)  {
        if ($row = $result->FetchRow()) {
            $objResponse->call('set_barangay_municipality_name', $row['barangay'], $row['municipality']);
        }
      }
    }
    return $objResponse;
  }

    function saveMemberInfo($paramobj) {
        global $db;
        
        $objResponse = new xajaxResponse();
        
        $enc = new Encounter();
        $pid = $enc->getValue('pid',$paramobj['encounter_nr']);        
        $person = new Person();
        $person->preloadPersonInfo($pid);                
                
        $fields = array('pid' => $db->qstr($pid), 
            'hcare_id' => PHIC_ID,
            'insurance_nr' => $db->qstr($paramobj['insurance_nr']),
            'member_lname' => $db->qstr($paramobj['memberlname']),
            'member_fname' => $db->qstr($paramobj['memberfname']),
            'member_mname' => $db->qstr($paramobj['membermname']),
            'member_suffix' => $db->qstr($paramobj['membersuffx']),
            'member_bdate' => $db->qstr(date('Y-m-d', strtotime($paramobj['memberbdate']))),
            'street_name' => $db->qstr($person->StreetName()), 
            'brgy_nr' => $person->getValue('brgy_nr'),
            'mun_nr' => $person->getValue('mun_nr'),
            'member_type' => $db->qstr($paramobj['membertype']),
            'member_relation' => $db->qstr($paramobj['memberprel']),
            'member_employerno' => $db->qstr($paramobj['pempno']),
            'member_employernm' => $db->qstr($paramobj['pempnm']));
        $bsuccess = $db->Replace('seg_insurance_member_info', $fields, array('pid','hcare_id','insurance_nr'));  
        if ($bsuccess)
            $objResponse->alert("Successfully saved member information!");
        else
            $objResponse->alert("ERROR in saving member information!");        
        return $objResponse;
    }

    $xajax->processRequest();
