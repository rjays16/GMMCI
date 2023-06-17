<?php
    // ---- Modifications done by LST - 6-17-2008: a.) removed instantiation of Encounter class
    //                                               b.) removed all references to Encounter class
    //                                               c.) added reference to er_opd_datetime field in recordset
    //                                                 returned from Person class.

    function populatePersonList($sElem,$searchkey,$page,$include_firstname,$include_encounter=TRUE,$scase_no = '', $hasOnlyFinalBill=FALSE) {
        global $db,$date_format;

        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('pagin_patient_search_max_block_rows');
        $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

        $objResponse = new xajaxResponse();
        $person = new Person();
        $offset = $page * $maxRows;

        $ergebnis=$person->SearchForBilling($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname, $scase_no, $hasOnlyFinalBill);

        $total = $person->FoundRows();

        $lastPage = floor($total/$maxRows);
        if ($page > $lastPage) $page=$lastPage;

        $rows=0;

        $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
        $objResponse->addScriptCall("clearList","person-list");
        if ($ergebnis) {
            $rows=$ergebnis->RecordCount();
            while($result=$ergebnis->FetchRow()) {
//                $rowEnc=$objEnc->getEncounterInfo($result["encounter_nr"]);
                //$objResponse->addAlert("rowEnc =".print_r($rowEnc));

                $p_name = $person->concatname($result["name_last"], $result["name_first"], $result["name_middle"]);
/*----replaced by pet-----may 17, 2008----to avoid hanging commas & in preparation of making brgy_name not mandatory-------
                $addr = $result["street_name"];
                if ($result["brgy_name"])
                    $addr.=", ".$result["brgy_name"];
                if ($result["mun_name"])
                    $addr.=", ".$result["mun_name"];
                if ($result["prov_name"])
                    $addr.=", ".$result["prov_name"];
                if ($result["zipcode"])
                    $addr.=" ".$result["zipcode"];
#------------with--------------------------------------------------------------------------------------------------------*/
                if ($result["street_name"] && $result["brgy_name"])
                    $addr=$result["street_name"].", ".$result["brgy_name"].", ".$result["mun_name"]." ".$result["zipcode"]." ".$result["prov_name"];
                else {
                    if ($result["street_name"] && !$result["brgy_name"]) {
                        if ($result["mun_name"])
                            $addr=$result["street_name"].", ".$result["mun_name"]." ".$result["zipcode"]." ".$result["prov_name"];
                        else {
                            if ($result["prov_name"])
                                $addr=$result["street_name"].", ".$result["prov_name"];
                            else
                                $addr=$result["street_name"];
                             }
                        }
                    elseif (!$result["street_name"] && $result["brgy_name"])
                            $addr=$result["brgy_name"].", ".$result["mun_name"]." ".$result["zipcode"]." ".$result["prov_name"];
                      }
#------------until here only-------------------------------------------------------------------------------------fgdp----------------

                $dob = $result["date_birth"];
                //added by Francis 021914
                $dobTme = strtotime($dob);
                $dobForm = date('m/d/Y', $dobTme);

                $dod = $result["death_date"];
                if( $dod != "0000-00-00 00:00:00"){
                    $dodTme = strtotime($dod);
                    $dodForm = date('m/d/Y', $dodTme);
                }else{
                    $dodForm = '';
                }

                $age = $person->getAge($dobForm,'',$dodForm);
                $age = number_format($age);

                if (!$dob || $dob=="0000-00-00") $dob="";

                if(trim($result["er_opd_datetime"])!=''){
#                    $admission_dt = @formatDate2Local($rowEnc["admission_dt"], $date_format);
                    $enc_dt = strftime("%b %d, %Y %I:%M%p", strtotime($result["er_opd_datetime"]));
                }else{
                    $enc_dt = '';
                }

                $confinetyp = getConfineTypeDesc($result["encounter_nr"]);
                $phic = isPHIC($result["encounter_nr"]);

//                $objResponse->addScriptCall("addPerson","person-list",
//                    $result["pid"], $p_name, $dob, $result["confine_period"], ($result["class_desc"] == '' ? ($phic == '' ? "NO" : $phic) : $result["class_desc"]),
//                    $confinetyp, $result["sex"],$addr, $result["status"], $result["encounter_nr"], $result["encounter_type"],
//                    $enc_dt, $result["parent_encounter_nr"]);
                // modified by Nick, 4/11/2014 - added condition >> && trim($result["death_encounter_nr"]) == trim($result["encounter_nr"])
                if((strcmp($result["death_date"], "0000-00-00 00:00:00") !=0) && trim($result["death_encounter_nr"]) == trim($result["encounter_nr"])){
                    $f_ddate = strftime("%b %d, %Y %I:%M%p", strtotime($result["death_date"]));
                }else{
                   $f_ddate = ''; 
                }

                $objResponse->addScriptCall("addPerson","person-list",
                    $result['phic_nr'],$result["pid"], $p_name, $dob, $result["death_date"], $f_ddate, $result["confine_period"], ($result["class_desc"] == '' ? ($phic == '' ? "NO" : $phic) : $result["class_desc"]),
                    $confinetyp, $result["sex"],$addr, $result["status"], $result["encounter_nr"], $result["encounter_type"], $enc_dt, $result["bill_dte"],number_format($result['total_bill'],2), $result['promi_refno'], $age);
            }
        }
        if (!$rows) $objResponse->addScriptCall("addPerson","person-list",NULL);
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }

        return $objResponse;
    }

		function getConfineTypeDesc($enc_nr) {
			global $db;

			$ctypdesc = '';
			$strSQL = "SELECT
							      confinetypedesc
							    FROM
							      seg_type_confinement AS stc
							      INNER JOIN
							      seg_encounter_confinement AS sec
							      ON stc.confinetype_id = sec.confinetype_id
							    WHERE sec.encounter_nr = '$enc_nr'
							    ORDER BY sec.create_time DESC
							    LIMIT 1";
			if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
					while ($row = $result->FetchRow()) {
						$ctypdesc = $row['confinetypedesc'];
					}
				}
			}

			return($ctypdesc);
		}

		function isPHIC($enc_nr) {
			global $db;

			$phicdesc = '';
			$strSQL = "SELECT
					          'PHIC' AS c_desc
					        FROM
					          seg_encounter_insurance AS sei
					        WHERE encounter_nr = '$enc_nr'
					          AND EXISTS
					          (SELECT
					            *
					          FROM
					            care_insurance_firm AS cif
					          WHERE (
					              firm_id LIKE '%PHILHEALTH%'
					              OR firm_id LIKE '%PHIC%'
					            )
					            AND cif.hcare_id = sei.hcare_id)";

			if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
					while ($row = $result->FetchRow()) {
						$phicdesc = $row['c_desc'];
					}
				}
			}

			return($phicdesc);
		}

    function populateEncountersList($sElem, $page, $pid) {
        global $db,$date_format;

        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('pagin_patient_search_max_block_rows');
        $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

        $objResponse = new xajaxResponse();
        $person = new Person();
        $offset = $page * $maxRows;

        $ergebnis=$person->SearchEncountersForBilling($pid, $maxRows, $offset);
        $total = $person->FoundRows();

        $lastPage = floor($total/$maxRows);
        if ($page > $lastPage) $page=$lastPage;

        $rows=0;

//				if ($_SESSION['sess_temp_userid'] == 'medocs')
//        	$objResponse->addAlert($person->sql);

        $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
        $objResponse->addScriptCall("clearList","person-list");
        if ($ergebnis) {
            $rows=$ergebnis->RecordCount();
            $j = 1; // Counter
            while($result=$ergebnis->FetchRow()) {
                $p_name = $person->concatname($result["name_last"], $result["name_first"], $result["name_middle"]);
                if ($result["street_name"] && $result["brgy_name"])
                    $addr=$result["street_name"].", ".$result["brgy_name"].", ".$result["mun_name"]." ".$result["zipcode"]." ".$result["prov_name"];
                else {
                    if ($result["street_name"] && !$result["brgy_name"]) {
                        if ($result["mun_name"])
                            $addr=$result["street_name"].", ".$result["mun_name"]." ".$result["zipcode"]." ".$result["prov_name"];
                        else {
                            if ($result["prov_name"])
                                $addr=$result["street_name"].", ".$result["prov_name"];
                            else
                                $addr=$result["street_name"];
                             }
                        }
                    elseif (!$result["street_name"] && $result["brgy_name"])
                            $addr=$result["brgy_name"].", ".$result["mun_name"]." ".$result["zipcode"]." ".$result["prov_name"];
                }

                $dob = $result["date_birth"];
                if (!$dob || $dob=="0000-00-00") $dob="";

                if(trim($result["er_opd_datetime"])!=''){
#                    $admission_dt = @formatDate2Local($rowEnc["admission_dt"], $date_format);
                    $enc_dt = strftime("%b %d, %Y %I:%M%p", strtotime($result["er_opd_datetime"]));
                }else{
                    $enc_dt = '';
                }

                if((strcmp($result["deathdate"], "0000-00-00 00:00:00") !=0) && (trim($result["death_encounter_nr"]) == trim($result["encounter_nr"]))){
                    $f_ddate = strftime("%b %d, %Y %I:%M%p", strtotime($result["deathdate"]));
                }

                $objResponse->addScriptCall("addPerson","person-list",
                    $result['phic_nr'],$result["pid"], $p_name, $dob, $result["deathdate"], $f_ddate, $result["confine_period"], ($result["class_desc"] == '' ? ($result["is_phic"] == '' ? "NO" : $result["is_phic"]) : $result["class_desc"]),
                    $result["confine_type"], $result["sex"],$addr, $result["status"], $result["encounter_nr"], $result["encounter_type"],
                    $enc_dt, $result["bill_dte"],number_format($result['total_bill'],2), $result['promi_refno'], $age, $result["bill_dte"], $result["bill_nr"], $j++);
            }
        }
        if (!$rows) $objResponse->addScriptCall("addPerson","person-list",NULL);
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }
        return $objResponse;
    }

    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/care_api_classes/class_globalconfig.php');
#    require_once($root_path.'include/care_api_classes/class_encounter.php');
    require($root_path.'include/care_api_classes/class_person.php');
    include_once($root_path.'include/inc_date_format_functions.php');

#    require_once($root_path.'include/care_api_classes/billing/class_billing.php');
    //require($root_path."modules/pharmacy/ajax/order-psearch.common.php");
    require($root_path."modules/billing/ajax/billing-psearch.common.php");

    $xajax->processRequests();
?>