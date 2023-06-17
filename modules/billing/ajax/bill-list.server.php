<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path."include/care_api_classes/billing/class_bill_info.php");
require_once($root_path."include/care_api_classes/billing/class_transmittal.php");
require_once($root_path."include/care_api_classes/class_promissory_note.php");
require_once($root_path."include/care_api_classes/curl/class_curl.php");
require_once($root_path."include/care_api_classes/class_pf.php");
require_once($root_path.'modules/billing/ajax/bill-list.common.php');

function updateFilterOption($noption, $bchecked='') {
	$objResponse = new xajaxResponse();

	$_SESSION["filteroption"][$noption] = $bchecked;

	return $objResponse;
}

function updateFilterTrackers($sfiltertype, $ofilter) {
	$objResponse = new xajaxResponse();

	$_SESSION["filtertype"] = $sfiltertype;
	$_SESSION["filter"] = $ofilter;

	return $objResponse;
}

function updatePageTracker($npage) {
	$objResponse = new xajaxResponse();
	$_SESSION["current_page"] = $npage;

#	$objResponse->alert($_SESSION["current_page"]);
	return $objResponse;
}

function clearFilterTrackers() {
	$objResponse = new xajaxResponse();

	unset($_SESSION["filtertype"]);
	unset($_SESSION["filter"]);

	return $objResponse;
}

function noteSelectedEncounter($enc_nr) {
		$objResponse = new xajaxResponse();

		if (!isset($_SESSION['cases'])) $_SESSION['cases'] = array();
		if (!isset($_SESSION['cases'][$enc_nr]))
				$_SESSION['cases'][$enc_nr] = $enc_nr;
		else
				unset($_SESSION['cases'][$enc_nr]);

		return $objResponse;
}

function clearPageTracker() {
	$objResponse = new xajaxResponse();
	unset($_SESSION["current_page"]);
	return $objResponse;
}

function assignToSessionVar($enc_nrs) {
	$objResponse = new xajaxResponse();

	$_SESSION['cases'] = explode(",",$enc_nrs);

	return $objResponse;
}

function showTransmittalDetails($hcare_id, $s_cases) {
	global $db;

	$objResponse = new xajaxResponse();

		$cases = explode(",", $s_cases);
		$s_encrs = implode("','", $cases);
	$s_encrs = "('".$s_encrs."')";

	#$objResponse->addAlert($s_encrs);

	if (($s_encrs) && ($s_encrs != '')) {
		$strSQL = "select cpi.insurance_nr, (case when isnull(sem.memcategory_id) then '' else sem.memcategory_id end) as categ_id,
						 (case when isnull(sm.memcategory_desc) then 'NONE' else sm.memcategory_desc end) as categ_desc,
						 (select concat(date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p'), ' to ', date_format(concat((case when discharge_date is null or discharge_date = '' then '0000-00-00' else discharge_date end), ' ', (case when discharge_time is null or discharge_time = '' then '00:00:00' else discharge_time end)), '%b %e, %Y %l:%i%p')) as prd
							 from care_encounter as ce1 where ce1.encounter_nr = ce.encounter_nr) as confine_period,
						 ce.encounter_nr, name_last, name_first, name_middle,
						 (select sum(total_acc_coverage + total_med_coverage + total_sup_coverage + total_srv_coverage + total_ops_coverage + total_d1_coverage + total_d2_coverage + total_d3_coverage + total_d4_coverage + total_msc_coverage) as tclaim
							from seg_billing_coverage as sbc3 inner join seg_billing_encounter as sbe3 on sbc3.bill_nr = sbe3.bill_nr
							where sbc3.hcare_id = ".$hcare_id." and sbe3.encounter_nr = ce.encounter_nr and sbe3.is_deleted IS NULL) as this_coverage,
							(case when DATE(case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end) >= ".CF2_EFFECTIVITY." then 1 else 0 end) as new_form
					from ((care_encounter as ce inner join
							(care_person as cp inner join care_person_insurance as cpi on cp.pid = cpi.pid and cpi.hcare_id = ".$hcare_id.") on ce.pid = cp.pid)
							 inner join seg_encounter_insurance as sei on (ce.encounter_nr = sei.encounter_nr or ce.parent_encounter_nr = sei.encounter_nr)
							 and sei.hcare_id = ".$hcare_id.")
							 left join (seg_encounter_memcategory as sem inner join seg_memcategory as sm on sem.memcategory_id = sm.memcategory_id)
							 on ce.encounter_nr = sem.encounter_nr
					where ce.encounter_nr in $s_encrs
					order by discharge_date asc";

//		$objResponse->addAlert("SQL: ".$strSQL);

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$objResponse->addScriptCall("showDetailsSection");

				$objbill = new BillInfo();

				while ($row = $result->FetchRow()) {
					$spatient = $objbill->concatname($row["name_last"], $row["name_first"], $row["name_middle"]);
					$objResponse->addScriptCall("addClaim", $row["insurance_nr"], $row["categ_id"], $row["categ_desc"], $row["confine_period"], $row["encounter_nr"], $spatient, $row["this_coverage"], 0, $row["new_form"]);
				}
			}
		}
		else
			$objResponse->addAlert("ERROR: ".$db->ErrorMsg());
	}

	return $objResponse;
}

function toggleTransmittal($control_no, $enc_nr, $b_reject) {
		$objResponse = new xajaxResponse();
		$obj = new Transmittal();
		if ($obj->toggleReject($control_no, $enc_nr, $b_reject)) {
				$objResponse->addScriptCall("refreshStatus", $control_no, $enc_nr, $obj->isRejected($control_no, $enc_nr));
		}
		return $objResponse;
}

function addClaimDet($s_encrs, $hcare_id) {
	global $db;

	$objResponse = new xajaxResponse();
	$objtransmit = new Transmittal();

	$cases = "('".implode("','",$s_encrs)."')";
	if (($cases) && ($cases != '')) {
		$strSQL = "select ce.pid, cpi.insurance_nr, (case when isnull(sem.memcategory_id) then 0 else sem.memcategory_id end) as categ_id,
						 (case when isnull(sm.memcategory_desc) then 'NONE' else sm.memcategory_desc end) as categ_desc,
						 (select concat(date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p'), ' to ', date_format(concat((case when discharge_date is null or discharge_date = '' then '0000-00-00' else discharge_date end), ' ', (case when discharge_time is null or discharge_time = '' then '00:00:00' else discharge_time end)), '%b %e, %Y %l:%i%p')) as prd
							 from care_encounter as ce1 where ce1.encounter_nr = ce.encounter_nr) as confine_period,
						 ce.encounter_nr, fn_get_pid_lastfirstmi(ce.pid) as full_name
					from ((care_encounter as ce inner join care_person_insurance as cpi on ce.pid = cpi.pid and cpi.hcare_id = $hcare_id)
							 inner join seg_encounter_insurance as sei on ce.encounter_nr = sei.encounter_nr and sei.hcare_id = $hcare_id)
							 left join (seg_encounter_memcategory as sem inner join seg_memcategory as sm on sem.memcategory_id = sm.memcategory_id)
							 on ce.encounter_nr = sem.encounter_nr
					where ce.encounter_nr in $cases
					order by discharge_date asc";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$obj = (object) 'details';
					$obj->enc_nr   = $row["encounter_nr"];
					$obj->patient  = $row["full_name"];
					$obj->prd      = $row["confine_period"];
					$obj->insurance_nr = $row["insurance_nr"];
					if ($objtransmit->isPersonPrincipal($row["pid"],$hcare_id))
						$obj->member   = $row["full_name"];
					else
						$obj->member = $objtransmit->getFullNameOfMember($row["pid"],$hcare_id);
					$obj->categ_desc   = $row["categ_desc"];

					$objResponse->addScriptCall("js_addclaimdet", $obj);
				}
			}
		}
		else
			$objResponse->addAlert("ERROR: ".$db->ErrorMsg());
	}

	return $objResponse;
}

function removePromi($refno){
	$objResponse = new xajaxResponse();
	$objPromi = new Promissory_note();

	if($objPromi->removePromi($refno)){
		$objResponse->addScriptCall("refreshPage");
	}else{
		$objResponse->alert("Unable to delete this promissory note.");
	}
	return $objResponse;
}

function untagPF($refno){
	$pf_obj = new Pf();
	$objResponse = new xajaxResponse();

	$response = "Error. Unable to cancel payment";

	if($pf_obj->deletePf($refno)){
		$response = "Successfully cancelled payment";
		$objResponse->addScriptCall("refreshPage");
	}

	$objResponse->alert($response);

	return $objResponse;

}

$xajax->processRequests();
?>