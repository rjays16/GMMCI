<?php
/***************
*
*   Modifications:
*   1.  Made adjustments to from and to dates of patient's transactions to be considered for Form 2.  ---- 05.28.2010
*
*/
include_once($root_path.'include/care_api_classes/class_insurance.php');
include_once($root_path.'include/care_api_classes/class_hospital_admin.php');

// Added by LST ----- 05.28.2010 -------------------------------------
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

function getLatestRefDate($enc_nr) {
	global $db;

	$todate = "0000-00-00 00:00:00";
	$strSQL = "select bill_dte " .
				"   from seg_billing_encounter " .
				"   where (encounter_nr = '". $enc_nr ."') " .
				"   order by bill_dte desc limit 1";

	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			while ($row = $result->FetchRow())
				$todate = $row['bill_dte'];
		}
	}

	return($todate);
}

// Added by LST ----- 05.28.2010 -------------------------------------

function concatname($slast, $sfirst, $smid) {
	$stmp = "";

	if (!empty($slast)) $stmp .= $slast;
	if (!empty($sfirst)) {
		if (!empty($stmp)) $stmp .= ", ";
		$stmp .= $sfirst;
	}
	if (!empty($smid)) {
		if (!empty($stmp)) $stmp .= " ";
		$stmp .= $smid;
	}
	return($stmp);
}

function getHRNinEncounter($enc_nr) {
	global $db;

	$sHRN = '';

	$strSQL = "select pid from care_encounter where encounter_nr = '$enc_nr'";
	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			if ($row = $result->FetchRow()) {
				$sHRN = (is_null($row["pid"]) ? "" : $row["pid"]);
			}
		}
	}

	return $sHRN;
}

/**
* @internal     returns the array of hospital/ambulatory services charged.
* @access       public
* @author       Bong S. Trazo
* @package      include
* @subpackage   care_api_classes
* @global       db - database object
*
* @param        enc_nr   - encounter no.
* @param        hcare_id - insurance id.
* @return       array of objects of HospServices.
*/
function fillHospAmbulSrvData($enc_nr, $hcare_id) {
	global $db;

	$strSQL = "select encounter_nr, sum(total_acc_charge) as acc_charge, sum(total_med_charge) as med_charge,
					 sum(total_srv_charge + total_msc_charge) as srv_charge, sum(total_ops_charge) as ops_charge,
					 sum(total_acc_coverage) as acc_coverage, sum(total_med_coverage) as med_coverage,
					 sum(total_srv_coverage + total_msc_coverage) as srv_coverage, sum(total_ops_coverage) as ops_coverage
					from seg_billing_encounter as sbe left join seg_billing_coverage as sbc
					 on sbe.bill_nr = sbc.bill_nr
					where sbc.hcare_id = $hcare_id and sbe.encounter_nr = '$enc_nr'
					group by encounter_nr";

	$services = array();
	$bloaded = false;

	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			if ($row = $result->FetchRow()) {
				// Room and Board
				$obj = new HospServices();
				$obj->charges = $row["acc_charge"];
				$obj->claim_hospital = $row["acc_coverage"];
				$obj->claim_patient = 0;
				$services[]  = $obj;

				// Drugs and Medicines
				$obj = new HospServices();
				$obj->charges = $row["med_charge"];
				$obj->claim_hospital = $row["med_coverage"];
				$obj->claim_patient = 0;
				$services[] = $obj;

				// X-Ray, Lab and Others
				$obj = new HospServices();
				$obj->charges = $row["srv_charge"];
				$obj->claim_hospital = $row["srv_coverage"];
				$obj->claim_patient = 0;
				$services[] = $obj;

				// O.R. Fees
				$obj = new HospServices();
				$obj->charges = $row["ops_charge"];
				$obj->claim_hospital = $row["ops_coverage"];
				$obj->claim_patient = 0;
				$services[] = $obj;

				// Medicines, Lab, Other charges reimbursible to patient.
				$pclaim = (isset($_GET['claim'])) ? $_GET['claim'] : 0;
				$obj = new HospServices();
				$obj->charges = $pclaim;
				$obj->claim_hospital = 0;
				$obj->claim_patient = $pclaim;
				$services[] = $obj;

				$bloaded = true;
			}
		}
	}

	if (!$bloaded) {
		// Room and Board
		$obj = new HospServices();
		$obj->charges = 0;
		$obj->claim_hospital = 0;
		$obj->claim_patient = 0;
		$services[]  = $obj;

		// Drugs and Medicines
		$obj = new HospServices();
		$obj->charges = 0;
		$obj->claim_hospital = 0;
		$obj->claim_patient = 0;
		$services[] = $obj;

		// X-Ray, Lab and Others
		$obj = new HospServices();
		$obj->charges = 0;
		$obj->claim_hospital = 0;
		$obj->claim_patient = 0;
		$services[] = $obj;

		// O.R. Fees
		$obj = new HospServices();
		$obj->charges = 0;
		$obj->claim_hospital = 0;
		$obj->claim_patient = 0;
		$services[] = $obj;

		// Medicines, Lab, Other charges reimbursible to patient.
		$pclaim = (isset($_GET['claim'])) ? $_GET['claim'] : 0;
		$obj = new HospServices();
		$obj->charges = $pclaim;
		$obj->claim_hospital = 0;
		$obj->claim_patient = $pclaim;
		$services[] = $obj;
	}

	return($services);
}

/**
* @internal     returns the array of objects with confinement-related information.
* @access       public
* @author       Bong S. Trazo
* @package      include
* @subpackage   care_api_classes
* @global       db - database object
*
* @param        enc_nr   - encounter no.
* @param        hcare_id - insurance id.
* @return       array of objects with confinement-related information.
*/
function fillConfinementData($enc_nr, $hcare_id) {
	global $db;

	$data = array();

	$strSQL = "select ce.admission_dt, ce.discharge_date, cp.death_date, ce.discharge_time,
				 (select sum(confine_days) as ndays
					 from seg_confinement_tracker as sct inner join seg_billing_encounter as sbe
						on sct.bill_nr = sbe.bill_nr
					 where sbe.encounter_nr = ce.encounter_nr and sct.hcare_id = $hcare_id) as claim_days
					from care_encounter as ce inner join care_person as cp
					 on ce.pid = cp.pid
					where ce.encounter_nr = '$enc_nr'";

	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			if ($row = $result->FetchRow()) {
				$obj = new Confinement();

				$obj->admit_dt      = strftime("%m/%d/%Y", strtotime($row['admission_dt']));
				$obj->discharge_dt  = strftime("%m/%d/%Y", strtotime($row['discharge_date']));
				$obj->death_dt      = ($row['death_date'] == '0000-00-00') ? '00-00-0000' : strftime("%m/%d/%Y", strtotime($row['death_date']));
				$obj->claim_days    = $row['claim_days'];
				$obj->discharge_tm  = strftime("%I:%M %p", strtotime($row['discharge_time']));
				$obj->admit_tm      = strftime("%I:%M %p", strtotime($row['admission_dt']));

				$data[] = $obj;
			}
		}
	}

	return $data;
}

function getCaseTypeID($enc_nr) {
	global $db;

	$sdesc = '';
	$n_id  = 0;

	$strSQL = "select stci.confinetype_id, confinetypedesc from seg_type_confinement_icds as stci inner join seg_type_confinement as stc
					on stci.confinetype_id = stc.confinetype_id
					where exists(select * from care_encounter_diagnosis as ced0
									where substring(code, 1, if(instr(code, '.') = 0, length(code), instr(code, '.')-1)) =
										substring(stci.diagnosis_code, 1, if(instr(stci.diagnosis_code, '.') = 0, length(stci.diagnosis_code), instr(stci.diagnosis_code, '.')-1))
					and ((exists(select * from care_encounter_diagnosis as ced where instr(stci.paired_codes, ced.code) > 0 and ced.code <> ced0.code and status <> 'deleted') and stci.paired_codes <> '') or stci.paired_codes = '')
									 and (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and str_to_date(create_time, '%Y-%m-%d %H:%i:%s') < str_to_date(now(), '%Y-%m-%d %H:%i:%s')
									 and status <> 'deleted')
					order by confinetype_id desc limit 1";

	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			if ($row = $result->FetchRow()) {
				$n_id  = $row["confinetype_id"];
				$sdesc = $row["confinetypedesc"];
			}
		}
	}

	switch (strtoupper($sdesc)) {
		case "B":
		case "INTENSIVE":
			$n_id = 2;
			break;

		case "C":
		case "CATASTROPHIC":
			$n_id = 3;
			break;

		case "D":
			$n_id = 4;
			break;

		default:
			$n_id = 1;

	}

	return $n_id;
}

function getAuthorizedRep(&$auth_array) {
	global $db;

	$auth_array = array();

	$objhosp = new Hospital_Admin();
	if ($rs = $objhosp->getAllHospitalInfo()) {
		$auth_array[0] = $rs["authrep"];
		$auth_array[1] = $rs["designation"];
	}
}

function fillDiagnosisCaseTypeData($enc_nr) {
	global $db;

	$data = array();

	$n_id = getCaseTypeID($enc_nr);
//	$strSQL = "select ced.code, ifnull(if(sd.description = '', cie.description, sd.description), ifnull(cie.description, '')) as description
//				  from (care_encounter_diagnosis as ced inner join care_icd10_en as cie
//					 on ced.code = cie.diagnosis_code) left join seg_encounter_diagnosis as sd
//						on sd.encounter_nr = ced.encounter_nr and sd.code = ced.code and sd.is_deleted = 0
//				  where ced.encounter_nr = '$enc_nr' and status NOT IN ('deleted','hidden','inactive','void')";

	$strSQL = "SELECT code, description
					 FROM seg_encounter_diagnosis
					 WHERE encounter_nr = '$enc_nr' AND is_deleted = 0
					 ORDER BY entry_no";

	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			while ($row = $result->FetchRow()) {
				$obj = new Diagnosis();
				$obj->code = $row["code"];
				$obj->fin_diagnosis = $row["description"];
				$obj->case_type = $n_id;
				$data[] = $obj;
			}
		}
	}

	if (empty($data)) {
		$obj = new Diagnosis();
		$obj->fin_diagnosis = "No Diagnosis!";
		$obj->case_type = $n_id;
		$data[] = $obj;
	}

	return $data;
}

function getOPDate($enc_nr, $dr_nr) {
	global $db;

	$strSQL = "select distinct ceo.op_date \n
					from (seg_ops_personell as sop inner join seg_ops_serv as sos on sop.refno = sos.refno) \n
					 left join care_encounter_op as ceo on sos.refno = ceo.refno \n
					where sos.encounter_nr = '$enc_nr' and sop.dr_nr = $dr_nr";

	$op_date = "0000-00-00";
	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			if ($row = $result->FetchRow()) {
				$op_date = (is_null($row["op_date"])) ? $op_date : $row["op_date"];
			}
		}
	}

	// .... could be a miscellaneous procedure added in billing ....
	if (strcmp($op_date, "0000-00-00") == 0) {
		$strSQL = "select distinct smod.op_date   \n
						from (seg_misc_ops as smo inner join seg_misc_ops_details as smod on smod.refno = smo.refno)  \n
						 inner join seg_ops_chrg_dr as socd on smod.refno = socd.ops_refno and   \n
							smod.entry_no = socd.ops_entryno and smod.ops_code = socd.ops_code   \n
						where smo.encounter_nr = '$enc_nr' and socd.dr_nr = $dr_nr";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				if ($row = $result->FetchRow()) {
					$op_date = (is_null($row["op_date"])) ? $op_date : $row["op_date"];
				}
			}
		}
	}

	return $op_date;
}

function isHouseCase($enc_nr) {
	global $db;

	$case = '';
	$sql = "select st.casetype_desc from seg_encounter_case sc
								inner join seg_type_case st on sc.casetype_id = st.casetype_id ".
				 "   where encounter_nr = '".$enc_nr."' ".
				 "   order by sc.modify_dt desc limit 1";

	if($result = $db->Execute($sql)){
			if($result->RecordCount()){
					if ($row = $result->FetchRow()) {
						$case = $row['casetype_desc'];
					}
			}
	}

	return !(strpos($case, 'HOUSE') === false);
}

function getHouseCaseDoctor($hcare_id, $bSurgeon) {
	global $db;

	$filter = ($bSurgeon) ? "cpr.is_housecase_surgeon = 1" : "cpr.is_housecase_anesth = 1";
	$strSQL = "SELECT cpr.nr, cp.name_last, cp.name_first, cp.name_middle, tin,     \n
									 (SELECT accreditation_nr FROM seg_dr_accreditation AS sda WHERE sda.dr_nr = cpr.nr AND sda.hcare_id = $hcare_id) AS accno   \n
								FROM care_personell cpr INNER JOIN care_person cp ON cpr.pid = cp.pid   \n
								WHERE $filter";
	if($result = $db->Execute($strSQL)) {
			if($result->RecordCount()) {
					if ($row = $result->FetchRow()) {
						return $row;
					}
			}
	}
	return false;
}

function fillPFData($enc_nr, $hcare_id, &$data0, &$data1, &$data2) {
	global $db;

	$data1 = array();
	$data2 = array();

	$from_date = getEarliestFromDate($enc_nr);
	$to_date   = getLatestRefDate($enc_nr);

	$strSQL = "select attending_dr_nr as dr_nr, name_last, name_first, name_middle, 'Attending Physician' as role,
	 \n           (select sum(dr_charge) as tcharge from seg_billing_pf as sbp inner join seg_billing_encounter as sbe1
	 \n             on sbp.bill_nr = sbe1.bill_nr
	 \n             where sbp.dr_nr = t.attending_dr_nr and sbp.hcare_id = $hcare_id and sbe1.encounter_nr = '$enc_nr') as charge,
	 \n          (select sum(dr_claim) as tclaim from seg_billing_pf as sbp2 inner join seg_billing_encounter as sbe2
	 \n             on sbp2.bill_nr = sbe2.bill_nr
	 \n             where sbp2.dr_nr = t.attending_dr_nr and sbp2.hcare_id = $hcare_id and sbe2.encounter_nr = '$enc_nr') as claim,
	 \n          role_nr, role_area, 0 as rvu, 0 as multiplier, 'Hospital Visit' as services, tin,
	 \n          (select accreditation_nr from seg_dr_accreditation as sda where sda.dr_nr = t.attending_dr_nr and sda.hcare_id = $hcare_id) as accno
	 \n          from
	 \n             (select distinct attending_dr_nr, name_last, name_first, name_middle, attend_start,
	 \n                 subdate((select attend_start
	 \n                             from seg_encounter_dr_mgt as dm2
	 \n                             where dm2.encounter_nr = dm1.encounter_nr and
	 \n                                   dm2.att_hist_no > dm1.att_hist_no
	 \n                             order by dm2.att_hist_no asc limit 1), 1) as attend_end, daily_rate, cpa.role_nr, role_area, discharge_date, tin
	 \n                 from (seg_encounter_dr_mgt as dm1 inner join (((care_personell as cpn
	 \n                    inner join care_person as cp on cpn.pid = cp.pid) inner join care_personell_assignment as cpa
	 \n                    on cpn.nr = cpa.personell_nr) inner join care_role_person as crp on
	 \n                    cpa.role_nr = crp.nr) on dm1.attending_dr_nr = cpn.nr) inner join care_encounter as ce
	 \n                    on dm1.encounter_nr = ce.encounter_nr
	 \n                 where (dm1.encounter_nr = '$enc_nr' or dm1.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr'))
	 \n                    and str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'
	 \n                    and str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'
	 \n                 order by att_hist_no) as t
	 \n          group by attending_dr_nr, role_area
	 \n          union
	 \n          select distinct spd.dr_nr, name_last, name_first, name_middle, concat(name, ' - private') as role,
	 \n              (select sum(dr_charge) as tcharge from seg_billing_pf as sbp inner join seg_billing_encounter as sbe1
	 \n                 on sbp.bill_nr = sbe1.bill_nr
	 \n                 where sbp.dr_nr = spd.dr_nr and sbp.hcare_id = $hcare_id and sbe1.encounter_nr = '$enc_nr') as charge,
	 \n              (select sum(dr_claim) as tclaim from seg_billing_pf as sbp2 inner join seg_billing_encounter as sbe2
	 \n                 on sbp2.bill_nr = sbe2.bill_nr
	 \n                 where sbp2.dr_nr = spd.dr_nr and sbp2.hcare_id = $hcare_id and sbe2.encounter_nr = '$enc_nr') as claim,
	 \n              spd.dr_role_type_nr, role_area, sum(ifnull(socd.rvu,0)) as tot_rvu, (sum(ifnull(socd.multiplier,0) * ifnull(socd.rvu,0))/sum(ifnull(socd.rvu,0))) as avg_multiplier,
	 \n              group_concat(DISTINCT sor.description ORDER BY sor.description DESC SEPARATOR '; ') as services, tin,
	 \n              (select accreditation_nr from seg_dr_accreditation as sda where sda.dr_nr = spd.dr_nr and sda.hcare_id = $hcare_id) as accno
	 \n              from ((seg_encounter_privy_dr as spd left join (seg_ops_chrg_dr as socd inner join seg_ops_rvs as sor on socd.ops_code = sor.code) on
	 \n                 spd.encounter_nr = socd.encounter_nr and spd.dr_nr = socd.dr_nr and
	 \n                 spd.dr_role_type_nr = socd.dr_role_type_nr) inner join (care_personell as cpn
	 \n                 inner join care_person as cp on cpn.pid = cp.pid) on spd.dr_nr = cpn.nr)
	 \n                 inner join care_role_person as crp on spd.dr_role_type_nr = crp.nr
	 \n              where (spd.encounter_nr = '$enc_nr' or spd.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr'))
	 \n                 and is_excluded = 0
	 \n                 and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'
	 \n                 and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'
	 \n              group by spd.dr_nr, name_last, name_first, name_middle
	 \n          union
	 \n           select dr_nr, name_last, name_first, name_middle, concat(name, ' - ', cop.code) as role,
	 \n              (select sum(dr_charge) as tcharge from seg_billing_pf as sbp inner join seg_billing_encounter as sbe1
	 \n                 on sbp.bill_nr = sbe1.bill_nr
	 \n                 where sbp.dr_nr = sop.dr_nr and sbp.hcare_id = $hcare_id and sbe1.encounter_nr = '$enc_nr') as charge,
	 \n              (select sum(dr_claim) as tclaim from seg_billing_pf as sbp2 inner join seg_billing_encounter as sbe2
	 \n                 on sbp2.bill_nr = sbe2.bill_nr
	 \n                 where sbp2.dr_nr = sop.dr_nr and sbp2.hcare_id = $hcare_id and sbe2.encounter_nr = '$enc_nr') as claim,
	 \n           sop.role_type_nr, role_area, sum(sosd.rvu) as tot_rvu, (sum(multiplier * sosd.rvu)/sum(sosd.rvu)) as avg_multiplier,
	 \n           group_concat(DISTINCT sor.description ORDER BY sor.description DESC SEPARATOR '; ') as services, tin,
	 \n          (select accreditation_nr from seg_dr_accreditation as sda where sda.dr_nr = sop.dr_nr and sda.hcare_id = $hcare_id) as accno
	 \n              from (((seg_ops_personell as sop inner join (care_personell as cpn
	 \n                 inner join care_person as cp on cpn.pid = cp.pid) on sop.dr_nr = cpn.nr)
	 \n                 inner join (seg_ops_serv as sos inner join (seg_ops_servdetails as sosd inner join seg_ops_rvs as sor on sosd.ops_code = sor.code)
	 \n                    on sos.refno = sosd.refno) on sop.refno = sos.refno)
	 \n                 inner join care_role_person as crp on sop.role_type_nr = crp.nr)
	 \n                 inner join seg_ops_rvs as cop on sop.ops_code = cop.code
	 \n              where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and upper(trim(sos.status)) <> 'DELETED'
	 \n                 and str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'
	 \n                 and str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'
	 \n                 and role_area is not null
	 \n              group by dr_nr, role_area";


	 $bSurgeonNoted = false;
	 $bAnesthNoted = false;

	 if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			while ($row = $result->FetchRow()) {
				if ($row["role_area"] == 'D3')
					$obj = new Surgeon();
				else
					$obj = new HealthPersonnel();

				$res = false;
				if (($bHouseCase = isHouseCase($enc_nr))) {
					if ($row["role_area"] == 'D3') {
						// Surgeon ...
						$res = getHouseCaseDoctor($hcare_id, true);
						if ($res) $bSurgeonNoted = true;
					}
					else {
						// Anaesthesiologist ...
						if ($row["role_area"] == 'D4') {
							$res = getHouseCaseDoctor($hcare_id, false);
							if ($res) $bAnesthNoted = true;
						}
					}
				}

				if ($res) {
					$obj->name = concatname((is_null($res["name_last"])) ? "" : $res["name_last"],
											(is_null($res["name_first"])) ? "" : $res["name_first"],
											(is_null($res["name_middle"])) ? "" : $res["name_middle"]);
					$obj->accnum = (is_null($res["accno"])) ? "" : $res["accno"];
					$obj->bir_tin_num = (is_null($res["tin"])) ? "" : $res["tin"];
				}
				else {
					$obj->name = concatname((is_null($row["name_last"])) ? "" : $row["name_last"],
											(is_null($row["name_first"])) ? "" : $row["name_first"],
											(is_null($row["name_middle"])) ? "" : $row["name_middle"]);
					$obj->accnum = (is_null($row["accno"])) ? "" : $row["accno"];
					$obj->bir_tin_num = (is_null($row["tin"])) ? "" : $row["tin"];
				}

				$obj->servperformance = ($row["role_area"] == 'D4') ? "" : ((is_null($row["services"])) ? "" : $row["services"]);
//				$obj->servperformance = (is_null($row["services"])) ? "" : $row["services"];
				$obj->profcharges = (is_null($row["charge"])) ? 0 : $row["charge"];
				$obj->claim_physician = (is_null($row["claim"])) ? 0 : $row["claim"];
				$obj->claim_patient = 0;
				$obj->role_area = $row["role_area"];

				if ($row["role_area"] == 'D3') {
					$op_dte = getOPDate($enc_nr, $row["dr_nr"]);
					if (strcmp($op_dte, "0000-00-00") != 0) $obj->operation_dt = strftime("%m-%d-%Y", strtotime($op_dte));
					$data2[] = $obj;
				}
				else {
					if ($row["role_area"] == 'D4')
						$data1[] = $obj;
					else
						$data0[] = $obj;
				}

				if ($bHouseCase && $bSurgeonNoted && $bAnesthNoted) break;
			} 	// while loop
		}
	}
}

function getDrugsandMeds($enc_nr, $hcare_id) {
	global $db;

	$data = array();

	$from_date = getEarliestFromDate($enc_nr);
	$to_date   = getLatestRefDate($enc_nr);

	$strSQL = "select bestellnum, generic, artikelname, description, max(flag) as flag, sum(qty) as qty, (sum(price * qty)/sum(qty)) as price, sum(itemcharge) as itemcharge, ".
				"   max(coverage) as claim ".
				" from ".
				"(select 0 as flag, pd.bestellnum, generic, artikelname, description, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as price, sum((pd.quantity - ifnull(spri.quantity, 0)) * pricecharge) as itemcharge ".
				"   from ((seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
				"         inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'M') ".
				"         left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum ".
				"      where (((encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and is_cash = 0 and pd.serve_status <> 'N' and pd.request_flag is null ".
				"         and exists (select * from (seg_hcare_products as shp inner join seg_hcare_bsked as shb ".
				"                           on shp.bsked_id = shb.bsked_id) inner join seg_encounter_insurance as si on shb.hcare_id = si.hcare_id ".
				"                        where shp.bestellnum = pd.bestellnum ".
				"                           and (select max(effectvty_dte) as latest ".
				"                                   from seg_hcare_bsked as shb2 ".
				"                                   where shb2.hcare_id = shb.hcare_id ".
				"                                      and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte and    ".
				"                           (si.encounter_nr = '$enc_nr' or si.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')))) ".
				"         or ((encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and is_cash = 0)) " .
				"        and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
				"        and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'" .
				"        and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'" .
				"   group by pd.bestellnum, artikelname ".
				" union ".
				"select 1 as flag, mpd.bestellnum, generic, artikelname, description, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as price, sum(quantity * unit_price) as itemcharge ".
				"   from (seg_more_phorder as mph inner join seg_more_phorder_details as mpd on mph.refno = mpd.refno) ".
				"      inner join care_pharma_products_main as p on mpd.bestellnum = p.bestellnum and p.prod_class = 'M' ".
				"   where (((encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
				"      and exists (select * from (seg_hcare_products as shp inner join seg_hcare_bsked as shb ".
				"                        on shp.bsked_id = shb.bsked_id) inner join seg_encounter_insurance as si on shb.hcare_id = si.hcare_id ".
				"                     where shp.bestellnum = mpd.bestellnum ".
				"                        and (select max(effectvty_dte) as latest ".
				"                                from seg_hcare_bsked as shb2 ".
				"                                where shb2.hcare_id = shb.hcare_id ".
				"                                   and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte and ".
				"                        (si.encounter_nr = '$enc_nr' or si.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')))) ".
				"         or ((encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')))) ".
				"       and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
				"       and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
				"   group by mpd.bestellnum, artikelname) as t
						LEFT JOIN (SELECT * FROM seg_applied_coverage AS c
								WHERE hcare_id = $hcare_id AND source = 'M'
										AND (EXISTS (SELECT * FROM seg_billing_encounter AS sbe WHERE sbe.bill_nr = c.ref_no AND sbe.encounter_nr = '$enc_nr') OR
												c.ref_no = CONCAT('T', '$enc_nr'))) AS sac ON sac.item_code = t.bestellnum
					group by bestellnum, artikelname order by artikelname";

	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			while ($row = $result->FetchRow()) {
				$obj = new Medicine();
				$obj->gen_name    = (is_null($row["generic"]) || $row["generic"] == '') ? $row["artikelname"] : $row["generic"];
				$obj->brand       = is_null($row["artikelname"]) ? "" : $row["artikelname"];
				$obj->preparation = is_null($row["description"]) ? "" : $row["description"];
				$obj->qty         = $row["qty"];
				$obj->unit_price  = $row["price"];
				$obj->actual_charges = $row["itemcharge"];
				$obj->claim_hospital = $row["claim"];
				$obj->claim_patient  = 0;
				$data[] = $obj;
			}
		}
	}

	return $data;
}

function getXRayLabOthers($enc_nr, $hcare_id, &$xraylab_array, &$supp_array, &$others_array) {
	global $db;

	$xraylab_array = array();
	$supp_array    = array();
	$others_array  = array();

	$from_date = getEarliestFromDate($enc_nr);
	$to_date   = getLatestRefDate($enc_nr);


	/**
	* Fix for errorneous lab charges, 10-04-2010
	* @author ajmq
	*/
	$query = "select parent_encounter_nr\n".
		"from care_encounter\n".
		"where encounter_nr ='".$enc_enr."'";
	$prev = $db->GetOne($query);

	if ($prev)
	{
		$prevSQL = " or encounter_nr = '$prev'";
	}

	$strSQL = "select distinct ld.service_code, ls.name as service_desc, sum(ld.quantity) as qty, (sum(ld.price_charge * ld.quantity)/sum(ld.quantity)) as price,
									 sum(ld.quantity * ld.price_charge) as itemcharge, max(coverage) AS claim, 'LB' as source " .
						"   from (((seg_lab_serv as lh inner join seg_lab_servdetails as ld on lh.refno = ld.refno) " .
						"          inner join seg_lab_services as ls on ld.service_code = ls.service_code) " .
						"          inner join seg_lab_service_groups as lsg on ls.group_code = lsg.group_code)
									 LEFT JOIN (SELECT * FROM seg_applied_coverage AS c
															WHERE hcare_id = $hcare_id AND source <> 'M'
																	AND (EXISTS (SELECT * FROM seg_billing_encounter AS sbe WHERE sbe.bill_nr = c.ref_no AND sbe.encounter_nr = '$enc_nr') OR
																			c.ref_no = CONCAT('T', '$enc_nr'))) AS sac ON sac.item_code = ld.service_code ".
						"      where /*ld.is_served <> 0 and*/ lh.is_cash = 0 and (ld.request_flag is null OR ld.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
						"         and (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and upper(trim(lh.status)) <> 'DELETED' " .
						"         and str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "' ".
						"         and str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "' ".
						"      group by ld.service_code, ls.name";


//	$strSQL = "select ld.service_code, ls.name as service_desc, ls.group_code, " .
//					"   lsg.name as group_desc, SUM(ld.quantity as qty, ld.price_charge as serv_charge, 'LB' as source " .


	$strSQL = "select distinct ld.service_code, ls.name as service_desc, sum(ld.quantity) as qty, (sum(ld.price_charge * ld.quantity)/sum(ld.quantity)) as price,\n".
							"sum(ld.quantity * ld.price_charge) as itemcharge, sum(ld.quantity * ld.price_charge) AS claim, 'LB' as source " .
					"   from ((seg_lab_serv as lh inner join seg_lab_servdetails as ld on lh.refno = ld.refno) " .
					"          inner join seg_lab_services as ls on ld.service_code = ls.service_code) " .
					"          inner join seg_lab_service_groups as lsg on ls.group_code = lsg.group_code " .
					"      where /*(ld.is_served <> 0 and)*/ lh.is_cash = 0 and (ld.request_flag is null OR ld.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
					"         and (encounter_nr = '" . $enc_nr . "'".$prevSQL.") and upper(trim(lh.status)) <> 'DELETED' " .
					"         and (str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "' " .
					"            and str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $to_date . "') " .
					"   group by ld.service_code, ls.name";


	putinXLOitemized_array($strSQL, $xraylab_array, $supp_array, $others_array);

	$strSQL = "select rd.service_code, rs.name as service_desc, count(rd.service_code) as qty, (sum(rd.price_charge)/count(rd.service_code)) as price,
										sum(rd.price_charge) as itemcharge, sum(rd.price_charge) AS claim, 'RD' as source " .
						 "   from (((seg_radio_serv as rh inner join care_test_request_radio as rd on rh.refno = rd.refno) " .
						 "          inner join seg_radio_services as rs on rd.service_code = rs.service_code) " .
						 "          inner join seg_radio_service_groups as rsg on rs.group_code = rsg.group_code)
											 LEFT JOIN (SELECT * FROM seg_applied_coverage AS c
																	WHERE hcare_id = $hcare_id AND source <> 'M'
																			AND (EXISTS (SELECT * FROM seg_billing_encounter AS sbe WHERE sbe.bill_nr = c.ref_no AND sbe.encounter_nr = '$enc_nr') OR
																					c.ref_no = CONCAT('T', '$enc_nr'))) AS sac ON sac.item_code = rd.service_code ".
						"      where /*upper(rd.status) = 'DONE' and*/ rh.is_cash = 0 and (rd.request_flag is null OR rd.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
						"         and str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
						"         and str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
						"         and (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and upper(trim(rh.status)) <> 'DELETED' " .
						"   group by rd.service_code, rs.name";
	putinXLOitemized_array($strSQL, $xraylab_array, $supp_array, $others_array);

	$strSQL = "select service_code, service_desc, sum(qty) as qty, (sum(price * qty)/sum(qty)) as price, sum(price * qty) as itemcharge, ".
						"   max(claim) as claim, t.source ".
						" from ".
						"(select 0 as grp, pd.bestellnum as service_code, artikelname as service_desc, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as price,
									 sum((pd.quantity - ifnull(spri.quantity, 0)) * pricecharge) as itemcharge, max(coverage) as claim, 'SU' as source ".
						"   from (((seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
						"      inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'S') ".
						"      left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum)
											 LEFT JOIN (SELECT * FROM seg_applied_coverage AS c
																	WHERE hcare_id = $hcare_id AND source <> 'M'
																			AND (EXISTS (SELECT * FROM seg_billing_encounter AS sbe WHERE sbe.bill_nr = c.ref_no AND sbe.encounter_nr = '$enc_nr') OR
																					c.ref_no = CONCAT('T', '$enc_nr'))) AS sac ON sac.item_code = pd.bestellnum ".
						"   where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and is_cash = 0 and pd.serve_status <> 'N' and pd.request_flag is null ".
						"      and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
						"      and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
						"      and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
						"   group by pd.bestellnum, artikelname ".
						" UNION ".
						"select 1 as grp, mphd.bestellnum as service_code, artikelname as service_desc, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as price,
									 sum(quantity * unit_price) as itemcharge, max(coverage) as claim, 'SU' as source ".
						"   from ((seg_more_phorder_details as mphd inner join seg_more_phorder as mph on mphd.refno = mph.refno) ".
						"      inner join care_pharma_products_main as p on mphd.bestellnum = p.bestellnum and p.prod_class = 'S')
											 LEFT JOIN (SELECT * FROM seg_applied_coverage AS c
																	WHERE hcare_id = $hcare_id AND source <> 'M'
																			AND (EXISTS (SELECT * FROM seg_billing_encounter AS sbe WHERE sbe.bill_nr = c.ref_no AND sbe.encounter_nr = '$enc_nr') OR
																					c.ref_no = CONCAT('T', '$enc_nr'))) AS sac ON sac.item_code = mphd.bestellnum ".
						"   where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
						"      and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
						"      and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
						"   group by mphd.bestellnum, artikelname) as t
								GROUP BY service_desc ORDER BY service_desc";
	putinXLOitemized_array($strSQL, $xraylab_array, $supp_array, $others_array);

	$strSQL = "select eqd.equipment_id as service_code, artikelname as service_desc, sum(number_of_usage) as qty, (sum(discounted_price * number_of_usage)/sum(number_of_usage)) as price,
									sum(number_of_usage * discounted_price) as itemcharge, max(coverage) as claim, 'OE' as source
							 from ((seg_equipment_orders as eqh inner join seg_equipment_order_items as eqd on eqh.refno = eqd.refno)
									 inner join seg_ops_serv as sos on sos.refno = eqh.request_refno)
											 LEFT JOIN (SELECT * FROM seg_applied_coverage AS c
																	WHERE hcare_id = $hcare_id AND source <> 'M'
																			AND (EXISTS (SELECT * FROM seg_billing_encounter AS sbe WHERE sbe.bill_nr = c.ref_no AND sbe.encounter_nr = '$enc_nr') OR
																					c.ref_no = CONCAT('T', '$enc_nr'))) AS sac ON sac.item_code = eqd.equipment_id
									 inner join care_pharma_products_main as cppm on cppm.bestellnum = eqd.equipment_id
							 where (sos.encounter_nr = '$enc_nr' or sos.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr'))
									and str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'
									and str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'
							 group by eqd.equipment_id, artikelname";
	putinXLOitemized_array($strSQL, $xraylab_array, $supp_array, $others_array);

	$strSQL = "select md.service_code, ms.name as service_desc, sum(md.quantity) as qty, (sum(chrg_amnt * md.quantity)/sum(md.quantity)) as price,
									 sum(md.quantity * chrg_amnt) as itemcharge, max(coverage) as claim, 'OA' as source ".
						"   from ((seg_misc_service as m inner join seg_misc_service_details as md on m.refno = md.refno) ".
						"      inner join seg_other_services as ms on md.service_code = ms.alt_service_code)
											 LEFT JOIN (SELECT * FROM seg_applied_coverage AS c
																	WHERE hcare_id = $hcare_id AND source <> 'M'
																			AND (EXISTS (SELECT * FROM seg_billing_encounter AS sbe WHERE sbe.bill_nr = c.ref_no AND sbe.encounter_nr = '$enc_nr') OR
																					c.ref_no = CONCAT('T', '$enc_nr'))) AS sac ON sac.item_code = md.service_code ".
						"   where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
						"      and str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
						"      and str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
						"   group by md.service_code, ms.name";
	putinXLOitemized_array($strSQL, $xraylab_array, $supp_array, $others_array);

	$strSQL = "select mcd.service_code, ms.name as service_desc, sum(mcd.quantity) as qty, (sum(chrg_amnt * mcd.quantity)/sum(mcd.quantity)) as price,
									 sum(mcd.quantity * chrg_amnt) as itemcharge, max(coverage) as claim, 'OC' as source ".
						"   from ((seg_misc_chrg as mc inner join seg_misc_chrg_details as mcd on mc.refno = mcd.refno) ".
						"      inner join seg_other_services as ms on mcd.service_code = ms.service_code)
											 LEFT JOIN (SELECT * FROM seg_applied_coverage AS c
																	WHERE hcare_id = $hcare_id AND source <> 'M'
																			AND (EXISTS (SELECT * FROM seg_billing_encounter AS sbe WHERE sbe.bill_nr = c.ref_no AND sbe.encounter_nr = '$enc_nr') OR
																					c.ref_no = CONCAT('T', '$enc_nr'))) AS sac ON sac.item_code = mcd.service_code
								where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
						"      and str_to_date(mc.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
						"      and str_to_date(mc.chrge_dte, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
						"   group by mcd.service_code, ms.name";
	putinXLOitemized_array($strSQL, $xraylab_array, $supp_array, $others_array);

//	$strSQL = "select service_code, service_desc, sum(qty) as qty, (sum(serv_charge * qty)/sum(qty)) as price, sum(serv_charge * qty) as itemcharge, ".
//				"   sum(coverage) as claim, t.source ".
//				" from ".
//				"(select ld.service_code, ls.name as service_desc, ls.group_code, " .
//				"   lsg.name as group_desc, sum(ld.quantity) as qty, (sum(ld.price_charge * ld.quantity)/sum(ld.quantity)) as serv_charge, 'LB' as source " .
//				"   from ((seg_lab_serv as lh inner join seg_lab_servdetails as ld on lh.refno = ld.refno) " .
//				"          inner join seg_lab_services as ls on ld.service_code = ls.service_code) " .
//				"          inner join seg_lab_service_groups as lsg on ls.group_code = lsg.group_code " .
//				"      where /*ld.is_served <> 0 and*/ lh.is_cash = 0 and (ld.request_flag is null OR ld.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
//				"         and (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and upper(trim(lh.status)) <> 'DELETED' " .
//				"         and str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "' ".
//				"         and str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "' ".
//				"   group by ld.service_code, ls.name, ls.group_code, lsg.name, source " .
//				" union ".
//				"select rd.service_code, rs.name as service_desc, rs.group_code, " .
//				"   rsg.name as group_desc, count(rd.service_code) as qty, (sum(rd.price_charge)/count(rd.service_code)) as serv_charge, 'RD' as source " .
//				"   from ((seg_radio_serv as rh inner join care_test_request_radio as rd on rh.refno = rd.refno) " .
//				 "          inner join seg_radio_services as rs on rd.service_code = rs.service_code) " .
//				"          inner join seg_radio_service_groups as rsg on rs.group_code = rsg.group_code " .
//				"      where /*upper(rd.status) = 'DONE' and*/ rh.is_cash = 0 and (rd.request_flag is null OR rd.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
//				"         and str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
//				"         and str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
//				"         and (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and upper(trim(rh.status)) <> 'DELETED' " .
//				"   group by rd.service_code, rs.name, rs.group_code, rsg.name, source ".
//				" union ".
//				"select pd.bestellnum, artikelname, 'SU' as group_code, 'Supplies' as group_desc, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as serv_charge, 'SU' as source ".
//				"   from ((seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
//				"      inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'S') ".
//				"      left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum ".
//				"   where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and is_cash = 0 and pd.serve_status <> 'N' and pd.request_flag is null ".
//				"      and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
//				"      and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
//				"      and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
//				"   group by pd.bestellnum, artikelname ".
//				" union ".
//				"select mphd.bestellnum, artikelname, 'MS' as group_code, 'Supplies' as group_desc, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as serv_charge, 'SU' as source ".
//				"   from (seg_more_phorder_details as mphd inner join seg_more_phorder as mph on mphd.refno = mph.refno) ".
//				"      inner join care_pharma_products_main as p on mphd.bestellnum = p.bestellnum and p.prod_class = 'S' ".
//				"   where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
//				"      and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
//				"      and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
//				"   group by mphd.bestellnum, artikelname ".
//				" union ".
//				"select eqd.equipment_id, artikelname, '' as group_code, 'Equipment' as group_desc, sum(number_of_usage) as qty, (sum(discounted_price * number_of_usage)/sum(number_of_usage))  as uprice, 'OE' as source
//					 from ((seg_equipment_orders as eqh inner join seg_equipment_order_items as eqd on eqh.refno = eqd.refno)
//					 inner join seg_ops_serv as sos on sos.refno = eqh.request_refno) inner join care_pharma_products_main as
//					 cppm on cppm.bestellnum = eqd.equipment_id
//					 where (sos.encounter_nr = '$enc_nr' or sos.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr'))
//							and str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'
//							and str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'
//					 group by eqd.equipment_id, artikelname ".
//				" union ".
//				"select md.service_code, ms.name as service_desc, '' as group_code, ".
//				 "      '' as group_desc, sum(md.quantity) as qty, (sum(chrg_amnt * md.quantity)/sum(md.quantity)) as serv_charge, 'OA' as source ".
//				 "   from (seg_misc_service as m inner join seg_misc_service_details as md on m.refno = md.refno) ".
//				"      inner join seg_other_services as ms on md.service_code = ms.alt_service_code ".
//				 "   where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
//				 "      and str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
//				 "      and str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
//				 "   group by md.service_code, ms.name ".
//				 " union ".
//				 "select mcd.service_code, ms.name as service_desc, '' as group_code, ".
//				 "   '' as group_desc, sum(mcd.quantity) as qty, (sum(chrg_amnt * mcd.quantity)/sum(mcd.quantity)) as serv_charge, 'OC' as source ".
//				 "   from (seg_misc_chrg as mc inner join seg_misc_chrg_details as mcd on mc.refno = mcd.refno) ".
//				 "      inner join seg_other_services as ms on mcd.service_code = ms.service_code ".
//				 "   where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
//				 "      and str_to_date(mc.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
//				 "      and str_to_date(mc.chrge_dte, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
//				 "   group by mcd.service_code, ms.name) as t
//						LEFT JOIN (SELECT * FROM seg_applied_coverage AS c
//								WHERE hcare_id = $hcare_id AND source <> 'M'
//										AND (EXISTS (SELECT * FROM seg_billing_encounter AS sbe WHERE sbe.bill_nr = c.ref_no AND sbe.encounter_nr = '$enc_nr') OR
//												c.ref_no = CONCAT('T', '$enc_nr'))) AS sac ON sac.item_code = t.service_code
//				 group by t.source, service_desc order by t.source, service_desc";
}

function putinXLOitemized_array($strSQL, &$xraylab_array, &$supp_array, &$others_array) {
	global $db, $pdf;


	/**
	* Fix for errorneous lab charges, 10-04-2010
	* @author ajmq
	*/

	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			while ($row = $result->FetchRow()) {

				$pdf->_fix_xlo += (float) $row["claim"];

				$obj = new Laboratories();

				switch ($row["source"]) {
					case 'LB':
						$generic_item = "Lab Service";
						break;

					case 'RD':
						$generic_item = "X-Ray Service";
						break;

					case 'SU':
					case 'MS':
						$generic_item = "Supply Item";
						break;

					case 'OE':
						$generic_item = "Equipment Charge";
						break;

					case 'OA':
					case 'OC':
						$generic_item = "Miscellaneous Charge";
				}

				$obj->particulars    = is_null($row["service_desc"]) ? $generic_item : $row["service_desc"];
				$obj->qty            = $row["qty"];
				$obj->unit_price     = $row["price"];
				$obj->actual_charges = $row["itemcharge"];
				$obj->claim_hospital = $row["claim"];
				$obj->claim_patient  = 0;

				switch ($row["source"]) {
					case 'LB':
					case 'RD':
						$xraylab_array[] = $obj;
						break;

					case 'SU':
					case 'MS':
						$supp_array[] = $obj;
						break;

					case 'OE':
					case 'OA':
					case 'OC':
						$others_array[] = $obj;
				}
			}
		}
	}

}

$pdf->encounter_nr = $_GET['encounter_nr'];
$pdf->hcare_id = $_GET['id'];

// Get the hospital number of patient ...
$pdf->hospnum = getHRNinEncounter($pdf->encounter_nr);

// Get the accreditation no. of the hospital ...
$objinsurance = new Insurance();
if ($a_no = $objinsurance->getAccreditationNo($pdf->hcare_id)) $pdf->hospaccnum = $a_no;

// Get the Hospital/Ambulatory services ...
$pdf->hospserv_array    = fillHospAmbulSrvData($pdf->encounter_nr, $pdf->hcare_id);

// Get the Confinement information ...
$pdf->confinement_array = fillConfinementData($pdf->encounter_nr, $pdf->hcare_id);

// Get the complete final diagnosis ...
$pdf->diagnosis_array   = fillDiagnosisCaseTypeData($pdf->encounter_nr);

$rep_array = array();
getAuthorizedRep($rep_array);
if (!empty($rep_array)) {
	$pdf->auth_rep = strtoupper($rep_array[0]);
	$pdf->rep_capacity = strtoupper($rep_array[1]);
}

// Get the Professional Data and Charges ...
fillPFData($pdf->encounter_nr, $pdf->hcare_id, $pdf->healthperson_array, $pdf->anesth_array, $pdf->surgeon_array);

// Get the charged Drugs and Medicines ...
$pdf->meds_array = getDrugsandMeds($pdf->encounter_nr, $pdf->hcare_id);


$pdf->_fix_xlo = 0;
// Get the charged X-Ray, Lab and Others ...
getXRayLabOthers($pdf->encounter_nr, $pdf->hcare_id, $pdf->lab_array, $pdf->sup_array, $pdf->others_array);


/**
* Fix for errorneous lab charges, 10-04-2010
* @author ajmq
*/
$pdf->hospserv_array[2]->claim_hospital = $pdf->_fix_xlo;
$pdf->hospserv_array[2]->charges = $pdf->_fix_xlo;
?>