<?php
		require('./roots.php');

		require_once($root_path.'include/inc_environment_global.php');
		require_once($root_path."include/care_api_classes/billing/class_bill_info.php");
		require_once($root_path."include/care_api_classes/billing/class_transmittal.php");

		require_once($root_path.'include/care_api_classes/class_globalconfig.php');
		require_once($root_path.'include/care_api_classes/class_encounter.php');
		require_once($root_path.'include/care_api_classes/class_insurance.php');
		require_once($root_path.'include/care_api_classes/class_department.php');
		require_once($root_path.'include/care_api_classes/class_personell.php');
		require_once($root_path.'include/care_api_classes/class_person.php');
		require_once($root_path.'include/care_api_classes/class_medocs.php');
		require_once($root_path.'include/care_api_classes/class_icd10.php');
		require_once($root_path.'include/care_api_classes/billing/class_ops.php');

		require_once($root_path."modules/billing/ajax/billing-transmittal.common.php");

		require_once($root_path . 'include/care_api_classes/eTransmittal/XmlTransmittal.php');

//		function downloadXmlFile_bak($transmit_no,$memcat){
//			$objResponse = new xajaxResponse();
//
//			$start = microtime(true);
//
//			$objXml = new eTransmittalXml($transmit_no,$memcat);
//			$xml = $objXml->Generate();
//			if(!$xml){
//				$objResponse->call('saveXmlFile',$objXml->getXmlBody(),0);
//			}else{
//				$objResponse->call('saveXmlFile',$objXml->getXmlBody(),1);
//			}
//
//			$end = microtime(true);
//			$diff = round($end - $start,2);
//			$objResponse->call('console.log',$diff);
//
//			$objResponse->call('hideXmlLoading');
//			return $objResponse;
//		}

function downloadXmlFile($transmitNumber,$memberCategoryId){
			$objResponse = new xajaxResponse();
	$xmlTransmittal = new XmlTransmittal($transmitNumber,$memberCategoryId);
	$objResponse->call('saveXmlFile',$xmlTransmittal->xml->getXmlBody(),1);
	$objResponse->call('hideXmlLoading');
	return $objResponse;
}

        //added by Nick, 06/23/2014
        function downloadClaimsXmlArchive($transmit_no,$memcat,$memcat_desc){
            $objResponse = new xajaxResponse();
            $objResponse->call('showXmlLoading');
            $filename = $transmit_no."_Membership_Category-".$memcat_desc;
            $objXml = new eTransmittalXml($transmit_no,$memcat);
            $objXml->GenerateZip($filename);
            $objResponse->call('downloadClaimsXmlArchive',$objXml->tmpFolder,$objXml->zip_archive);
            return $objResponse;
        }

        function getBillsCount($transmit_no,$memcat){
            $objResponse = new xajaxResponse();
            $objXml = new eTransmittalXml($transmit_no,$memcat);
            $transmittals = $objXml->getTransmittals();

            if(is_array($transmittals)){
                $objResponse->assign('billsCount','innerHTML',count($transmittals));
			}else{
                $objResponse->assign('billsCount','innerHTML',0);
			}

			return $objResponse;
		}

		function assignToSessionVar($enc_nrs) {
				$objResponse = new xajaxResponse();

				$_SESSION['cases'] = explode(",",$enc_nrs);

				return $objResponse;
		}

		function showTransmittalDetails($hcare_id, $s_cases, $ref) {
				global $db;

				$objResponse = new xajaxResponse();

//				$s_encrs = implode("', '", $_SESSION['cases']);
				if (is_array($s_cases))
						$s_encrs = implode("','", $s_cases);
				else {
						$cases = explode(",", $s_cases);
						$s_encrs = implode("','", $cases);
				}
				$s_encrs = "('".$s_encrs."')";

				if (!is_array($ref))
						$scolumn = ", (select patient_claim from seg_transmittal_details as std where std.transmit_no = '$ref' and std.encounter_nr = ce.encounter_nr) as pclaim";
				else
						$scolumn = "";

				if (($s_encrs) && ($s_encrs != '')) {
						$strSQL = "select cpi.insurance_nr, (case when isnull(sem.memcategory_id) then 0 else sem.memcategory_id end) as categ_id,
													 (case when isnull(sm.memcategory_desc) then 'NONE' else sm.memcategory_desc end) as categ_desc,
													 (select concat(date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p'), ' to ', date_format(concat((case when discharge_date is null or discharge_date = '' then '0000-00-00' else discharge_date end), ' ', (case when discharge_time is null or discharge_time = '' then '00:00:00' else discharge_time end)), '%b %e, %Y %l:%i%p')) as prd
															 from care_encounter as ce1 where ce1.encounter_nr = ce.encounter_nr) as confine_period,
													 ce.encounter_nr, name_last, name_first, name_middle,
													 (select sum(total_acc_coverage + total_med_coverage + total_sup_coverage + total_srv_coverage + total_ops_coverage + total_d1_coverage + total_d2_coverage + total_d3_coverage + total_d4_coverage + total_msc_coverage) as tclaim
															from seg_billing_coverage as sbc3 inner join seg_billing_encounter as sbe3 on sbc3.bill_nr = sbe3.bill_nr
															where sbc3.hcare_id = ".$hcare_id." and sbe3.encounter_nr = ce.encounter_nr) as this_coverage,
															(case when DATE(case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end) >= ".CF2_EFFECTIVITY." then 1 else 0 end) as new_form".$scolumn."
												from ((care_encounter as ce inner join
															(care_person as cp inner join care_person_insurance as cpi on cp.pid = cpi.pid and cpi.hcare_id = ".$hcare_id.") on ce.pid = cp.pid)
																 inner join seg_encounter_insurance as sei on (ce.encounter_nr = sei.encounter_nr OR ce.parent_encounter_nr = sei.encounter_nr) and sei.hcare_id = ".$hcare_id.")
																 left join (seg_encounter_memcategory as sem inner join seg_memcategory as sm on sem.memcategory_id = sm.memcategory_id)
																 on ce.encounter_nr = sem.encounter_nr
												where ce.encounter_nr in $s_encrs
												order by discharge_date asc";

						if ($result = $db->Execute($strSQL)) {
								if ($result->RecordCount()) {
										$objResponse->call("js_showDetailsSection");

										$objbill = new BillInfo();

										while ($row = $result->FetchRow()) {
												$spatient = $objbill->concatname((is_null($row["name_last"]) ? "" : $row["name_last"]), (is_null($row["name_first"]) ? "" : $row["name_first"]), (is_null($row["name_middle"]) ? "" : $row["name_middle"]));

												$obj = (object) 'details';
												$obj->insurance_nr = $row["insurance_nr"];
												$obj->categ_id = $row["categ_id"];
												$obj->categ_desc = $row["categ_desc"];
												$obj->prd = $row["confine_period"];
												$obj->enc_nr = $row["encounter_nr"];
												$obj->patient = $spatient;
												$obj->claim = $row["this_coverage"];
												$obj->pclaim = ((isset($row["pclaim"])) ? $row["pclaim"] : ((isset($ref[$row["encounter_nr"]])) ? $ref[$row["encounter_nr"]] : 0) );
												$obj->newform = $row["new_form"];

												$objResponse->call("js_addClaim", $obj);
										}
								}
						}
						else
								$objResponse->alert("ERROR: ".$db->ErrorMsg());
				}

				return $objResponse;
		}

		function setMemCategoryOptionsForPrint($categ_id=0) {
				global $db;
				$objResponse = new xajaxResponse();

				$strSQL = "select * from seg_memcategory order by memcategory_desc";
				if ($result = $db->Execute($strSQL)) {
						if ($result->RecordCount()) {
								$objResponse->call("js_ClearOptions","category_list");
								$objResponse->call("js_AddOptions","category_list","-Select Classification-", 0);
								while ($row = $result->FetchRow()) {
										$objResponse->call("js_AddOptions", "category_list", $row['memcategory_desc'], $row['memcategory_id'], (($row['memcategory_id'] == $categ_id) ? '1' : '0'));
								}
						}
				} else {
						$objResponse->alert("ERROR: Cannot retrieve membership categories ...");
				}

				return $objResponse;
		}

		function setMemCategoryOptionsForPrintNew($categ_id=0) {
				global $db;
				$objResponse = new xajaxResponse();

				$strSQL = "select * from seg_memcategory order by memcategory_desc";
				if ($result = $db->Execute($strSQL)) {
						if ($result->RecordCount()) {
								$objResponse->call("js_ClearOptions","newcategory_list");
								$objResponse->call("js_AddOptions","newcategory_list","-Select Classification-", 0);
								while ($row = $result->FetchRow()) {
										$objResponse->call("js_AddOptions", "newcategory_list", $row['memcategory_desc'], $row['memcategory_id'], (($row['memcategory_id'] == $categ_id) ? '1' : '0'));
								}
						}
				} else {
						$objResponse->alert("ERROR: Cannot retrieve membership categories ...");
				}

				return $objResponse;
		}

		function setMemCategoryOptions($categ_id) {
				global $db;
				$objResponse = new xajaxResponse();

				$strSQL = "select * from seg_memcategory order by memcategory_desc";
				if ($result = $db->Execute($strSQL)) {
						if ($result->RecordCount()) {
								$objResponse->call("js_ClearOptions","entrycategory_list");
								$objResponse->call("js_AddOptions","entrycategory_list","-Select Classification-", 0);
								while ($row = $result->FetchRow()) {
										$objResponse->call("js_AddOptions", "entrycategory_list", $row['memcategory_desc'], $row['memcategory_id'], (($row['memcategory_id'] == $categ_id) ? '1' : '0'));
								}
						}
				} else {
						$objResponse->alert("ERROR: Cannot retrieve membership categories ...");
				}

				return $objResponse;
		}

		function setFormsForSelection() {
				$objResponse = new xajaxResponse();

				$objResponse->call("js_ClearOptions","forms_list");
				$objResponse->call("js_AddOptions","forms_list","-Select Form-", 0);
				$objResponse->call("js_AddOptions", "forms_list", "Form 1", 1, '0');
				$objResponse->call("js_AddOptions", "forms_list", "Form 2", 2, '0');
				$objResponse->call("js_AddOptions", "forms_list", "NEW Form 2", 3, '0');

//				$objResponse->call("js_AddOptions", "forms_list", "Form 3", 3, '0');

				return $objResponse;
		}

		function delTransmittal($transmit_no) {
				$objResponse = new xajaxResponse();

				$objtr = new Transmittal();
				if ($objtr->delTransmittal($transmit_no)) {
						$objResponse->alert("Transmittal {$transmit_no} successfully deleted!");
						$objResponse->call("gotoBreakFile", $_SESSION["breakfile"]);
				}
				else
						$objResponse->alert("ERROR: ".$objtr->getErrorMsg());

				return $objResponse;
		}

		function setMemCategory($aFormValues) {
				global $db;

				$bSuccess = false;
				$msg = '';

				$objResponse = new xajaxResponse();

				$s_enc_nr = $aFormValues['memcateg_enc'];
				$n_id      = $aFormValues['categ_id'];
				$sDesc    = $aFormValues['categ_desc'];

				$db->StartTrans();
				$strSQL = "delete from seg_encounter_memcategory where encounter_nr = '".$s_enc_nr."'";
				$bSuccess = $db->Execute($strSQL);

				if ($bSuccess) {
						$strSQL = "insert into seg_encounter_memcategory (encounter_nr, memcategory_id) ".
											"   values ('".$s_enc_nr."', ".$n_id.")";
						$bSuccess = $db->Execute($strSQL);
				}

				if (!$bSuccess) {
						$msg = $db->ErrorMsg();
						$db->FailTrans();
				}
				$db->CompleteTrans();

				if (!$bSuccess)
						$objResponse->alert("ERROR: ".$msg);
				else {
						$objResponse->call('assignMemCategDesc', $s_enc_nr, $sDesc);
				}

				return $objResponse;
		}

		function removeCaseInClaim($enc_nr) {
				$objResponse = new xajaxResponse();

				if ((isset($_SESSION['cases'][$enc_nr])) && ($_SESSION['cases'][$enc_nr])) {
					unset($_SESSION['cases'][$enc_nr]);
				}

				return $objResponse;
		}

		function populateDiagnosisList($encounter_nr, $page) {
			global $db;
			$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
			$glob_obj->getConfig('pagin_patient_search_max_block_rows');
			$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

			$objResponse = new xajaxResponse();
			$enc_obj=new Encounter;
			$pers_obj=new Personell;

			#$objResponse->addAlert('enc = '.$encounter_nr);

			$offset = $page * $maxRows;

			#$searchkey = utf8_decode($searchkey);

			#$total_srv = $enc_obj->countSearchDiagnosisList($encounter_nr, $maxRows,$offset);
			#$objResponse->addAlert($enc_obj->sql);
			#$objResponse->addAlert('total = '.$total);
			$ergebnis=$enc_obj->SearchDiagnosisList($encounter_nr, $maxRows, $offset);
			$total = $enc_obj->rec_count;

			$lastPage = floor($total/$maxRows);

			if ((floor($total%10))==0)
				$lastPage = $lastPage-1;

			if ($page > $lastPage) $page=$lastPage;

			$rows=0;

			$objResponse->call("setPaginationICD",$page,$lastPage,$maxRows,$total);
			$objResponse->call("clearList","diagnosisList");

			if ($ergebnis) {
				$rows=$ergebnis->RecordCount();
				while($result=$ergebnis->FetchRow()) {
					$doctorinfo = $pers_obj->get_Person_name($result['diagnosing_clinician']);
					#$objResponse->addAlert($pers_obj->sql);
					$middleInitial = "";
					if (trim($doctorinfo['name_middle'])!=""){
						$thisMI=split(" ",$doctorinfo['name_middle']);
						foreach($thisMI as $value){
							if (!trim($value)=="")
							$middleInitial .= $value[0];
						}
						if (trim($middleInitial)!="")
						$middleInitial .= ".";
					}

					#$doctor_name = $doctorinfo['name_first']." ".$doctorinfo['name_2']." ".$middleInitial." ".$doctorinfo['name_last'];
					$doctor_name = $pers_obj->concatname((is_null($doctorinfo["name_last"])) ? "" : $doctorinfo["name_last"],
																							 (is_null($doctorinfo["name_first"])) ? "" : $doctorinfo["name_first"], $middleInitial);
					$doctor_name = ucwords(strtolower($doctor_name));
					$doctor_name = htmlspecialchars($doctor_name);

					$altdesc = (is_null($result["alt_desc"])) ? "" : $result["alt_desc"];

					if ($result['is_confidential']==1){
						$doctor_name = '<font size=1 color="red"><strong>CONFIDENTIAL</strong></font>';
					}

					$objResponse->call("addDiagnosisToList","diagnosisList",trim($result["diagnosis_nr"]),$result["code"],$result["description"],$doctor_name, $altdesc, $result["type_nr"]);
				}#end of while
			} #end of if

			if (!$rows) $objResponse->call("addDiagnosisToList","diagnosisList",NULL);
			#if ($sElem) {
				$objResponse->call("endpopulateICDList");
			#}

			return $objResponse;
		}

		function getLatestBillDte($enc_nr) {
			global $db;

			$lastbill_dte = "0000-00-00 00:00:00";
			$strSQL = "select bill_dte " .
						"   from seg_billing_encounter " .
						"   where (encounter_nr = '". $enc_nr ."') " .
						"   order by bill_dte desc limit 1";

			if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
					while ($row = $result->FetchRow())
						$lastbill_dte = $row['bill_dte'];
				}
			}

			return($lastbill_dte);
		}

		# Added by LST -- 3.30.2010 --- to synch data extracted by billing and data for Form 2 ...
		function getEarliestFromDate($enc_nr) {
			global $db;

			$frmdate = "0000-00-00 00:00:00";
			$strSQL = "select bill_frmdte " .
						"   from seg_billing_encounter " .
						"   where (encounter_nr = '". $enc_nr ."') " .
						"   order by bill_frmdte asc limit 1";

			if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
					while ($row = $result->FetchRow())
						$frmdate = $row['bill_frmdte'];
				}
			}

			return($frmdate);
		}

		function getCurrentOpsInEncounter($enc_nr, $page, $b_all = 0) {
			global $db;
			$glob_obj = new GlobalConfig($GLOBAL_CONFIG);

			$glob_obj->getConfig('pagin_patient_search_max_block_rows');
			$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];

			$objResponse = new xajaxResponse();
			$srv=new SegOps;

			$bill_frmdte = getEarliestFromDate($enc_nr);
			$bill_dt = getLatestBillDte($enc_nr);

			$b_all = ($b_all != 0);

			$offset = $page * $maxRows;
			$total_srv = $srv->countCurrentOP($enc_nr, $bill_frmdte, $bill_dt, $maxRows, $offset, $b_all);

			$total = $srv->count;

			$lastPage = floor($total/$maxRows);

			if ((floor($total%$maxRows))==0)
				$lastPage = $lastPage-1;

			if ($page > $lastPage) $page=$lastPage;
			$ergebnis=$srv->SearchOpsForForm2($enc_nr, $bill_frmdte, $bill_dt, $maxRows, $offset);
			#$objResponse->alert("Qry = ".$srv->sql);

			$rows=0;

			$objResponse->call("setPaginationICP",$page,$lastPage,$maxRows,$total);
			$objResponse->call("clearList","proceduresList");

			if ($ergebnis) {
				$rows=$ergebnis->RecordCount();
				while($result=$ergebnis->FetchRow()) {
					$description_short = $result["description"];
					if (strlen($description_short)>50)
						$description_short = substr(trim($result["description"]),0,50)."...";

					$objResponse->call("addCurrentOpsToList","proceduresList", trim($result["code"]), (is_null($result["op_date"]) ? '00-00-0000' : strftime("%m-%d-%Y", strtotime($result["op_date"]))), trim($description_short),trim($result["description"]), trim($result["refno"]), $result["entry_no"], $result["provider"]);
				}#end of while
			} #end of if

			if (!$rows) $objResponse->call("addCurrentOpsToList","proceduresList",NULL);

			$objResponse->call("endpopulateICPMList");

			return $objResponse;
		}

		function getDischrgDateTime($enc_nr) {
			global $db;

			$objResponse = new xajaxResponse();
			$strSQL = "select discharge_date, discharge_time
									 from care_encounter
									 where encounter_nr = '".$enc_nr."'
										and is_discharged = 1
										and upper(encounter_status) not in ('CANCELLED')";
			if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
					if ($row = $result->FetchRow()) {
						$dischrgdte = $row['discharge_date'];
						$dischrgtme  = $row['discharge_time'];

						$dischrgdte = strftime("%A, %d %B %Y", strtotime($dischrgdte));
						$dischrgtme = strftime("%H:%M:%S", strtotime($dischrgtme));

						$objResponse->call("showDischargeDateTime", $dischrgdte, $dischrgtme);
					}
				}
			}
			return $objResponse;
		}

		# Save ICD descriptions ...
		function saveICDDescs($enc_nr, $user_id, $icdcodes, $icddescs) {
			global $db;

			$bSuccess = 1;

			$objResponse = new xajaxResponse();
			$objmdoc = new Medocs();

			foreach($icdcodes as $k=>$v) {
				if (!$objmdoc->saveAltDesc($enc_nr, $v, $icddescs[$k], $user_id)) {
						$objResponse->alert("ERROR: ".$objmdoc->sql);
						$bSuccess = 0;
						break;
				}
			}

			if ($bSuccess)
				$objResponse->call("saveICPChanges", $enc_nr, $user_id);
			else
				$objResponse->call("showSaveStatus", 0, "ERROR: ".$objmdoc->sql);

			return $objResponse;
		}

		# Save ICP descriptions ...
		function saveICPDetails($enc_nr, $user_id, $codes, $refnos, $entrynos, $sources, $pdescs, $pdates) {
			global $db;

			$bSuccess = 1;
			$errMsg = '';

			$objResponse = new xajaxResponse();
			$objmdoc = new Medocs();

			foreach($codes as $k=>$v) {
				if ($objmdoc->saveProcDesc($v, $refnos[$k], $entrynos[$k], $pdescs[$k], $user_id, $sources[$k])) {
						# Update date of operation of particular procedure ...
						if ($sources[$k] == 'OR') {
							$strSQL = "update care_encounter_op
														set op_date = '". strftime("%Y-%m-%d", strtotime(strftime("%m-%d-%Y",strtotime($pdates[$k]))) ) ."'
														where refno = '". $refnos[$k] ."'";
						}
						else {
							$strSQL = "update seg_misc_ops_details
														set op_date = '". strftime("%Y-%m-%d", strtotime(strftime("%m-%d-%Y",strtotime($pdates[$k]))) ) ."'
														where refno = '". $refnos[$k] ."'
															 and ops_code = '" . $v . "'
															 and entry_no = ". $entrynos[$k];
						}
						if (!$db->Execute($strSQL)) {
							$objResponse->alert("ERROR: ".$strSQL);
							$errMsg = "ERROR: ".$strSQL;
							$bSuccess = 0;
							break;
						}
				}
				else {
					$objResponse->alert("ERROR: ".$objmdoc->sql);
					$errMsg = "ERROR: ".$objmdoc->sql;
					$bSuccess = 0;
					break;
				}
			}

			$objResponse->call("showSaveStatus", $bSuccess, $errMsg);
			return $objResponse;
		}

		function saveEncounterInfo($data, $user_id, $hcare_id) {
			global $db;

			$bSuccess = 0;
			$tbl = '';

			$objResponse = new xajaxResponse();

			# $objResponse->alert("Array passed = ".print_r($data, true));

			$enc_nr = $data["memcateg_enc"];
			$categid = $data["categ_id"];
			$dischrgdte = $data["dischrgdate"];
			$dischrgtme = $data["dischrgtme"];
			$categdesc = $data["categ_desc"];

			# Update classification or membership category of patient ...
			$fldArray = array('encounter_nr'=>"'$enc_nr'", 'memcategory_id'=>"$categid");
			$bSuccess = $db->Replace('seg_encounter_memcategory', $fldArray, array('encounter_nr'));
			$tbl = "seg_encounter_memcategory";

			if ($bSuccess) {
				# Update discharge date and time ...
				$fldArray = array('encounter_nr'=>$db->qstr($enc_nr), 'discharge_date'=>$db->qstr(strftime("%Y-%m-%d", strtotime($dischrgdte))), 'discharge_time'=>$db->qstr(strftime("%H:%M:%S", strtotime($dischrgtme))),'modify_id'=>$db->qstr($user_id));
				$bSuccess = $db->Replace('care_encounter', $fldArray, array('encounter_nr'));
				$tbl = "care_encounter";
			}

			if ($bSuccess) {
				$enc = new Encounter();
				$pid = $enc->getValue('pid',$enc_nr);
				# $insurance_nr = getPersonInsuranceNr($pid, $hcare_id);
				$insurance_nr = $data["insurance_nr"];

				$lastnm = $data["membernmlast"];
				$firstnm = $data["membernmfirst"];
				$midnm = $data["membernmmid"];
				$streetaddr = $data["street_addr"];
				$brgy_nr = $data["barangay_nr"];
				$mun_nr = $data["municipality_nr"];
				$src = $data["meminfosrc"];

				# Update the principal member's information ....
				if ($src == 1) {
					$fldArray = array('pid'=>$db->qstr($pid), 'name_last'=>$db->qstr($lastnm), 'name_first'=>$db->qstr($firstnm), 'name_middle'=>$db->qstr($midnm),
														'street_name'=>$db->qstr($streetaddr), 'brgy_nr'=>$db->qstr($brgy_nr), 'mun_nr'=>$db->qstr($mun_nr), 'modify_id'=>$db->qstr($user_id));
					$bSuccess = $db->Replace('care_person', $fldArray, array('pid'));
					$tbl = "care_person";
				}
				else {
//					$fldArray = array('pid'=>"'$pid'", 'hcare_id'=>"$hcare_id", 'insurance_nr'=>"'$insurance_nr'",  'member_lname'=>"'$lastnm'", 'member_fname'=>"'$firstnm'",
//														'member_mname'=>"'$midnm'", 'street_name'=>"'$streetaddr'", 'brgy_nr'=>"$brgy_nr", 'mun_nr'=>"$mun_nr");
//					$bSuccess = $db->Replace('seg_insurance_member_info', $fldArray, array('pid', 'hcare_id', 'insurance_nr'));

					$strSQL = "UPDATE seg_insurance_member_info
											SET insurance_nr = ".$db->qstr($insurance_nr).",
													member_lname = ".$db->qstr($lastnm).",
													member_fname = ".$db->qstr($firstnm).",
													member_mname = ".$db->qstr($midnm).",
													street_name  = ".$db->qstr($streetaddr).",
													brgy_nr = ".$db->qstr($brgy_nr).",
													mun_nr = ".$db->qstr($mun_nr)."
											WHERE pid = ".$db->qstr($pid)."
													AND hcare_id = ".$db->qstr($hcare_id)."
													AND insurance_nr = ".$db->qstr($data["oldinsurance_nr"]);
					$bSuccess = $db->Execute($strSQL);
					$tbl = "seg_insurance_member_info";
				}

				// Update the insurance no. in care_person_insurance ...
				if ($bSuccess) {
					$strSQL = "UPDATE care_person_insurance
											SET insurance_nr = ".$db->qstr($insurance_nr).",
													modify_id = '".$_SESSION['sess_temp_userid']."',
													modify_time = NOW()
											WHERE pid = ".$db->qstr($pid)."
													AND hcare_id = ".$db->qstr($hcare_id);
					$bSuccess = $db->Execute($strSQL);
				}
			}

			if ($bSuccess) {
				if ($data["insurance_nr"] != $data["oldinsurance_nr"]) {
					$objResponse->call("assignInsuranceNr", $enc_nr, $data["insurance_nr"]);
				}
				$objResponse->call("assignMemCategDesc", $enc_nr, $categdesc);
				$objResponse->call("saveICDChanges", $enc_nr, $user_id);
			}
			else
				$objResponse->call("showSaveStatus", 0, "ERROR updating $tbl:".$db->ErrorMsg());

			return $objResponse;
		}

		function getPersonInsuranceNr($pid, $hcare_id) {
			global $db;

			$insurance_nr = "";
			$strSQL = "SELECT insurance_nr
									FROM care_person_insurance i
									WHERE i.pid = '$pid'
											AND i.hcare_id = $hcare_id";
			if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
					if ($row = $result->FetchRow()) {
						$insurance_nr = $row["insurance_nr"];
					}
				}
			}

			return $insurance_nr;
		}

		function getPolicyHolderInfo($enc_nr, $hcare_id, $ins_nr) {
			$objResponse = new xajaxResponse();

			$src = 2;

			$enc = new Encounter();
			$pid = $enc->getValue('pid',$enc_nr);

			$objins = new PersonInsurance($pid);
			$person = new Person();
			$policyinfo = array();
			if ($objins->isPrincipal($pid, $hcare_id)) {
				if ($person->preloadPersonInfo($pid)) {
					$policyinfo["lastname"] = $person->LastName();
					$policyinfo["firstname"] = $person->FirstName();
					$policyinfo["midname"] = $person->MiddleName();
					$policyinfo["street_name"] = $person->getValue('street_name');
					$policyinfo["brgy_nr"] = $person->getValue('brgy_nr');
					$policyinfo["mun_nr"] =  $person->getValue('mun_nr');

					if ($res = $person->getPrincipalAddr($pid)) {
						$row = $res->FetchRow();
						$policyinfo["barangay"] = $row["Barangay"];
						$policyinfo["municity"] = $row["Municity"];
					}
				}

				$src = 1;
			}
			else {
				$info = $objins->getPrincipalHolder($ins_nr, $hcare_id);
				if ($info) {
						$policyinfo["lastname"] = $info['last_name'];
						$policyinfo["firstname"] = $info['first_name'];
						$policyinfo["midname"] = $info['middle_name'];
						$policyinfo["street_name"] = $info['street'];
						$policyinfo["brgy_nr"] = $info['brgy_nr'];
						$policyinfo["mun_nr"] =  $info['mun_nr'];
						$policyinfo["barangay"] = $info["barangay"];
						$policyinfo["municity"] = $info["municipality"];
						$src = 1;
				}
				else {
//					if ($rs =  $person->getPrincipalNmFromTmp($enc_nr)) {
		        $row = $objins->is_member_info_editable($pid, $hcare_id, $ins_nr);
						if ($row) {
							$policyinfo["lastname"] = $row['last_name'];
							$policyinfo["firstname"] = $row['first_name'];
							$policyinfo["midname"] = $row['middle_name'];
							$policyinfo["street_name"] = $row['street'];
							$policyinfo["brgy_nr"] = $row['barangay'];
							$policyinfo["mun_nr"] =  $row['municipality'];

							$paddr = getLocationDetails($row['barangay']);
							if ($paddr) {
								$policyinfo["barangay"] = $paddr['brgy_name'];
								$policyinfo["municity"] = $paddr['mun_name'];
							}
							else {
								$policyinfo["barangay"] = "";
								$policyinfo["municity"] = "";
							}
				    }
				}	// principal holder is not yet in care_person ...
			}

			#$objResponse->alert("Member info = ".print_r($policyinfo, true));

			if (count($policyinfo) > 0) {
				$obj = (object) 'details';
				$obj->lastname    = $policyinfo["lastname"];
				$obj->firstname   = $policyinfo["firstname"];
				$obj->midname     = $policyinfo["midname"];
				$obj->street_name = $policyinfo["street_name"];
				$obj->brgy_nr     = $policyinfo["brgy_nr"];
				$obj->mun_nr      = $policyinfo["mun_nr"];
				$obj->barangay    = $policyinfo["barangay"];
				$obj->municity    = $policyinfo["municity"];
				$obj->infosource  = $src;

				$objResponse->call("assignMemberInfo", $obj);
			}
			else
				$objResponse->call("assignMemberInfo", NULL);

			// Show the insurance no. of patient ...
//			$objResponse->call("showInsuranceNr", getPersonInsuranceNr($pid, $hcare_id));
			$objResponse->call("showInsuranceNr", $ins_nr);

			return $objResponse;
		}

		# getDiagnosisCodes
		function addCode($encounter, $encounter_type, $dchrgdttm, $doc_nr, $code, $type) {
				$cdObj=new Medocs;
				$icdObj=new Icd($code);
				$objResponse = new xajaxResponse();

				if($rw=$icdObj->getIcd10Info($code)) {
						$desc=$rw->FetchRow();
						$xcode = strtoupper($desc['diagnosis_code']);

						$pers_obj=new Personell();

						if ($docinfo = $pers_obj->get_Person_name2($doc_nr)) {
								$doc_name = $pers_obj->concatname((is_null($docinfo["name_last"])) ? "" : $docinfo["name_last"],
																									(is_null($docinfo["name_first"])) ? "" : $docinfo["name_first"],
																									(is_null($docinfo["name_middle"])) ? "" : $docinfo["name_middle"]);
								$dept_nr = (is_null($docinfo["location_nr"])) ? 0 : $docinfo["location_nr"];
						}
						else {
								$doc_name = "";
								$dept_nr = 0;
						}

						if (!isset($_SESSION['sess_login_userid']) || ($_SESSION['sess_login_userid'] == ''))
							$create_id = $_SESSION["sess_temp_userid"];
						else
							$create_id = $_SESSION["sess_login_userid"];

						$result=$cdObj->AddCode($encounter, $encounter_type, $dchrgdttm, trim($xcode), $doc_nr, $dept_nr, $create_id, 'icd', $type);
						#$objResponse->alert($cdObj->sql);
						if($result){
								$diagnosis_nr = $cdObj->getLatestDiagnosisNr();
								$objResponse->call("addDiagnosisToList", 'diagnosisList', $diagnosis_nr, trim($code), $desc['description'], $doc_name, $desc['description'], $type);
								$objResponse->call("clearICDFields");
						}else{
								$objResponse->alert("Saving of the ICD failed!");
						}
				}else
						$objResponse->alert("Invalid ICD Code!");

				return $objResponse;
		}// End addCode Function

		function rmvCode($diagnosis_nr, $create_id){
				$cdObj=new Medocs;
				$objResponse = new xajaxResponse();

				if ($result=$cdObj->removeICDCode($diagnosis_nr, $create_id))
						$objResponse->alert("Data has been successfully deleted");
						#$objResponse->addAssign("icdCode", "focus()", true);

				if($result){
						$objResponse->call("removeAddedICD",$diagnosis_nr);
				}else{
						$objResponse->alert(print_r($cdObj->sql,TRUE));
				}
				return $objResponse;
		}

		function getPatientEncounterInfo($enc_nr) {
			$objResponse = new xajaxResponse();
			$encobj = new Encounter();
			$encinfo = $encobj->getPatientEncounter($enc_nr);
			$dchrgdttm = strftime("%Y-%m-%d", strtotime($encinfo["discharge_date"])). ' '.strftime("%H:%M:%S",  strtotime($encinfo["discharge_time"]));
			$doc_nr = $encinfo["current_att_dr_nr"];
			if ($doc_nr == 0) {
				$doc_nr = $encinfo["specialist_dr_nr"];
				if ($doc_nr == 0) {
					$doc_nr = $encinfo["consulting_dr_nr"];
				}
			}
			$objResponse->call("setEncounterParams", $encinfo["encounter_type"], $dchrgdttm, $doc_nr, $encinfo["sex"]);
			return $objResponse;
		}

		function getLocationDetails($brgy_nr) {
			global $db;

			$strSQL = "SELECT b.brgy_name, m.mun_nr, m.mun_name, p.prov_nr, p.prov_name \n
										FROM (seg_barangays as b inner join seg_municity as m \n
											 on b.mun_nr = m.mun_nr) inner join seg_provinces as p \n
											 on m.prov_nr = p.prov_nr \n
											 where b.brgy_nr = $brgy_nr";
			if ($result = $db->Execute($strSQL)) {
					if ($row = $result->FetchRow()) {
						return $row;
					}
			}
			return false;
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
							# $objResponse->call("setProvince", (is_null($row['prov_nr']) ? 0 : $row['prov_nr']), (is_null($row['prov_name']) ? '' : $row['prov_name']));
					}
			}

			return $objResponse;
		}

		//Added by EJ 10/07/2014
		function resetTransmitNo() {
	        global $db;
	        $objResponse = new xajaxResponse();

	        $objtr = new Transmittal();
	        $lasttransnr = $objtr->getLastTransmitNo();

	        if ($lasttransnr)
	            $objResponse->call("resetTransmitNo",$lasttransnr);
	        else
	            $objResponse->call("resetTransmitNo","ERROR!",1);

	        return $objResponse;
	    } 

		$xajax->processRequest();
?>