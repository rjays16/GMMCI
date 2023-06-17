<?php
/***************
*
*   Modifications:
*   1.  Made adjustments to from and to dates of patient's transactions to be considered for Form 2.  ---- 05.28.2010
*
*/
require_once($root_path.'include/inc_environment_global.php'); //added by jasper 05/02/2013
include_once($root_path.'include/care_api_classes/class_insurance.php');
include_once($root_path.'include/care_api_classes/class_hospital_admin.php');
include_once($root_path.'include/care_api_classes/billing/class_billing.php');

define('WELLBABY', 12); //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY
define('ISSRVD_EFFECTIVITY', '2012-10-09');   // Constant applicable to SPMC only: date when is_served is considered in computing
                                              // laboratory and radiology charges -- by LST -- 09.22.2012
                                              // ... changed to 10.09.2012 (added single quotes around ISSRVD_EFFECTIVITY)
define('DEFAULT_NBPKG_NAME','NEW BORN');//Added By Jarel 12/09/2013


function setToUTF8() {
	global $db;
	$db->Execute("set names 'utf8'");
}

// Added by LST ----- 05.28.2010 -------------------------------------
function getEarliestFromDate($enc_nr) {
	global $db;

	$frmdate = "0000-00-00 00:00:00";
	$strSQL = "select bill_frmdte " .
				"   from seg_billing_encounter " .
				"   where (encounter_nr = '". $enc_nr ."') and is_deleted IS NULL " .
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
				"   where (encounter_nr = '". $enc_nr ."') and is_deleted IS NULL " .
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

function getLatestBillNr($enc_nr) {
    global $db;

    $strSQL = "SELECT
                  bill_nr
                FROM
                  seg_billing_encounter
                WHERE encounter_nr = '$enc_nr' and is_deleted IS NULL
                ORDER BY bill_dte DESC
                LIMIT 1";
    $row = $db->GetRow($strSQL);
    if ($row) {
        return $row['bill_nr'];
    }
    return false;
}

function getBillingData($enc_nr, $pkglimit, $issurgical) {
    global $db;

    if ($billnr = getLatestBillNr($enc_nr)) {
        //added by jasper 08/23/2013 TO FIX PATIENTS COVERED WITH PACKAGE
        $bill_date = getLatestRefDate($enc_nr);
        //$objbilling = new Billing($enc_nr, NULL, NULL, $billnr);
        $objbilling = new Billing($enc_nr, $bill_date, NULL, $billnr);
        $issurgical = $objbilling->isSurgicalCase();
        $pkglimit = $objbilling->getPkgAmountLimit();
    }
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
					where sbc.hcare_id = $hcare_id and sbe.encounter_nr = '$enc_nr' and sbe.is_deleted IS NULL
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

    // Changed ce.discharge_date to ce.mgh_setdte per request of Billing to reflect correct days for claim ... 03.29.2012 -- LST
    //edited by jasper 05/02/2013 ce.encounter_type, 05/16/2013 cp.death_encounter_nr
    $strSQL = "SELECT
                  ce.admission_dt,
                  ce.discharge_date,
                  cp.death_date,
                  cp.death_time,
                  cp.death_encounter_nr,
                  ce.mgh_setdte,
                  ce.discharge_time,
                  ce.encounter_type,
                  ce.encounter_date,
                  DATEDIFF(
                    (CASE WHEN ce.discharge_date < ce.mgh_setdte
                        THEN ce.discharge_date
                        ELSE
                          CASE WHEN (ce.mgh_setdte != '0000-00-00 00:00:00') THEN ce.mgh_setdte ELSE ce.discharge_date END
                     END),
                    ce.admission_dt
                  ) claim_days
                FROM
                  care_encounter AS ce
                  INNER JOIN
                  care_person AS cp
                  ON ce.pid = cp.pid
                WHERE ce.encounter_nr = '$enc_nr'";

//	$strSQL = "select ce.admission_dt, ce.discharge_date, cp.death_date, ce.discharge_time,
//				 (select sum(confine_days) as ndays
//					 from seg_confinement_tracker as sct inner join seg_billing_encounter as sbe
//						on sct.bill_nr = sbe.bill_nr
//					 where sbe.encounter_nr = ce.encounter_nr and sct.hcare_id = $hcare_id) as claim_days
//					from care_encounter as ce inner join care_person as cp
//					 on ce.pid = cp.pid
//					where ce.encounter_nr = '$enc_nr'";

    //added by jasper 06/07/2013
    //$bill_date = getLatestRefDate($enc_nr);

	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			if ($row = $result->FetchRow()) {
				$obj = new Confinement();
                //added by jasper 05/02/2013 FIX FOR OUTPATIENT
               if ($row['encounter_type'] == OUT_PATIENT) {
                    $discharge_dte = strftime("%m/%d/%Y", strtotime(getOPDateOPD($enc_nr)));
                } else {
                    $discharge_dte = strftime("%m/%d/%Y", strtotime($row['mgh_setdte']));
                    if ( strtotime($discharge_dte) >= strtotime('2013-05-04') && strtotime($discharge_dte) <= strtotime('2013-06-10') ) {
                        $discharge_time = strftime("%I:%M %p", strtotime('+2 hours', strtotime($row['mgh_setdte'])));
                    } else {
                        $discharge_time = strftime("%I:%M %p", strtotime($row['mgh_setdte']));
                    }
                }
                //added by jasper 05/02/2013
                //added by jasper 05/16/2013 FIX FOR DEAD PATIENTS IN DIFFERENT ENCOUNTER
                if ($row['death_encounter_nr'] == $enc_nr) {
                    $obj->death_dt = ($row['death_date'] == '0000-00-00') ? '00-00-0000' : strftime("%m/%d/%Y", strtotime($row['death_date']));
                    $discharge_dte  = strftime("%m/%d/%Y", strtotime($row['death_date']));
                    $discharge_time = strftime("%I:%M %p", strtotime($row['death_time']));
                    $obj->claim_days = floor((strtotime($discharge_dte) - strtotime($row['admission_dt']))/3600/24)+1;
                } else {
                    $obj->death_dt = '00-00-0000';
                    $obj->claim_days = $row['claim_days'];
                }
                //added by jasper 05/16/2013
                $obj->discharge_dt = $discharge_dte;
                //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY
                $obj->enc_type = $row['encounter_type'];
                if ($row['encounter_type'] == WELLBABY) {
				    $obj->admit_dt = strftime("%m/%d/%Y", strtotime($row['encounter_date']));
                    $obj->admit_tm = strftime("%I:%M %p", strtotime($row['encounter_date']));
                } else {
                $obj->admit_dt = strftime("%m/%d/%Y", strtotime($row['admission_dt']));
                    $obj->admit_tm = strftime("%I:%M %p", strtotime($row['admission_dt']));
                }
                //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY

                //$obj->claim_days    = $row['claim_days'];
                /*if (strpos($row['discharge_time'],"24:") === 0) {
                    $row['discharge_time'] = "00:".substr($row['discharge_time'],3);
                    $obj->discharge_tm  = strftime("%I:%M %p", strtotime($row['discharge_time']));*/
                if (strpos($discharge_time,"24:") === 0) {
                    $row['discharge_time'] = "00:".substr($discharge_time,3);
                    $obj->discharge_tm  = strftime("%I:%M %p", strtotime($row['discharge_time']));
                }
                else
				    $obj->discharge_tm  = strftime("%I:%M %p", strtotime($discharge_time));
                    //$obj->discharge_tm  = strftime("%I:%M %p", strtotime($row['discharge_time']));

				$obj->admit_tm = strftime("%I:%M %p", strtotime($row['admission_dt']));

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

	// fix for issue HISSPMC-143 ----- start ----
	$to_date   = getLatestRefDate($enc_nr);
	$strSQL = "select confinetype_id from seg_encounter_confinement ".
						"   where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
						"      and str_to_date(classify_dte, '%Y-%m-%d %H:%i:%s') < '".$to_date."' " .
						"   order by classify_dte desc limit 1";

	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			if ($row = $result->FetchRow()) {
				$n_id  = $row["confinetype_id"];
			}
		}
	}

	if ($n_id == 0) {
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

		if ($n_id == 0)
			$n_id = 1;
		else {
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
		}
	}
	// fix for issue HISSPMC-143  --- end ----

	return $n_id;
}

function getAuthorizedRep(&$auth_array) {
	$auth_array = array();

	$objhosp = new Hospital_Admin();
	if ($rs = $objhosp->getAllHospitalInfo()) {
		$auth_array[0] = $rs["authrep"];
		$auth_array[1] = $rs["designation"];
	}
}


function getAddedAccommodationType($enc_nr, $from_date, $to_date, &$ntype, &$sname) {
	global $db;

	$ntype = 0;
	$sname = '';
//	$filter = '';

//	$from_date = getEarliestFromDate($enc_nr);
//	$to_date   = getLatestRefDate($enc_nr);

	$strSQL = "select cw.accomodation_type, accomodation_name ".
				"   from (seg_encounter_location_addtl as sela inner join care_ward as cw on sela.group_nr = cw.nr) ".
				"      inner join seg_accomodation_type as sat on cw.accomodation_type = sat.accomodation_nr ".
				"   where (sela.encounter_nr = '$enc_nr' or sela.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
				"      and (str_to_date(sela.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "' ".
				"      and str_to_date(sela.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $to_date . "') ".
				"   order by entry_no desc limit 1";

	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			while ($row = $result->FetchRow()) {
				$ntype = $row['accomodation_type'];
				$sname = $row['accomodation_name'];
			}
		}
	}

	return($db->ErrorMsg() == '');
}

function getAccommodationType($enc_nr, &$ntype, &$sname) {
	global $db;

	$ntype = 0;
	$sname = '';
//	$filter = '';

	$from_date = getEarliestFromDate($enc_nr);
	$to_date   = getLatestRefDate($enc_nr);

    //edited by jasper 07/08/2013 SEG_ENCOUNTER_LOCATION_ADDTL WILL BE BASED ON ENTRY NUMBER
    /*$strSQL = "select
                STR_TO_DATE(CONCAT(DATE_FORMAT(date_from, '%Y-%m-%d'), ' ', DATE_FORMAT(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') occupy_date,
                cw.accomodation_type, accomodation_name ".
                 "   from ((care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr) ".
                "      inner join seg_accomodation_type as sat on cw.accomodation_type = sat.accomodation_nr) ".
                "      left join seg_encounter_location_rate as selr on cel.nr = selr.loc_enc_nr and cel.encounter_nr = selr.encounter_nr ".
                "   where (cel.encounter_nr = '$enc_nr' or cel.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
                "      and exists (select nr ".
                "                     from care_type_location as ctl ".
                "                     where upper(type) = 'WARD' and ctl.nr = cel.type_nr) ".
                "      and ((str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "' ".
                "      and str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $to_date . "') ".
                "         or ".
                "      (str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "' ".
                "      and str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $to_date . "') ".
                "      or ".
                "      str_to_date(concat(date_format(ifnull(date_to, '0000-00-00'), '%Y-%m-%d'), ' ', date_format(ifnull(time_to, '00:00:00'), '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') = '0000-00-00 00:00:00') ".
        " UNION ALL
          SELECT occupy_date, cw.accomodation_type, accomodation_name
            FROM (seg_encounter_location_addtl sel INNER JOIN care_ward AS cw ON sel.group_nr = cw.nr)
              INNER JOIN seg_accomodation_type sat ON cw.accomodation_type = sat.accomodation_nr
          WHERE (sel.encounter_nr = '$enc_nr' or sel.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr'))
            AND (
              STR_TO_DATE(
                sel.create_dt,
                '%Y-%m-%d %H:%i:%s'
              ) >= '" . $from_date . "'
              AND STR_TO_DATE(
                sel.create_dt,
                '%Y-%m-%d %H:%i:%s'
              ) < '" . $to_date . "'
            )
          ORDER BY occupy_date DESC LIMIT 1";*/
	$strSQL = "select 0 AS entry_no,
                STR_TO_DATE(CONCAT(DATE_FORMAT(date_from, '%Y-%m-%d'), ' ', DATE_FORMAT(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') occupy_date,
                cw.accomodation_type, accomodation_name ".
 				"   from ((care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr) ".
				"      inner join seg_accomodation_type as sat on cw.accomodation_type = sat.accomodation_nr) ".
				"      left join seg_encounter_location_rate as selr on cel.nr = selr.loc_enc_nr and cel.encounter_nr = selr.encounter_nr ".
				"   where (cel.encounter_nr = '$enc_nr' or cel.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
				"      and exists (select nr ".
				"                     from care_type_location as ctl ".
				"                     where upper(type) = 'WARD' and ctl.nr = cel.type_nr) ".
				"      and ((str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "' ".
				"      and str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $to_date . "') ".
				"         or ".
				"      (str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "' ".
				"      and str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $to_date . "') ".
				"      or ".
				"      str_to_date(concat(date_format(ifnull(date_to, '0000-00-00'), '%Y-%m-%d'), ' ', date_format(ifnull(time_to, '00:00:00'), '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') = '0000-00-00 00:00:00') ".
        " UNION ALL
          SELECT entry_no, occupy_date, cw.accomodation_type, accomodation_name
            FROM (seg_encounter_location_addtl sel INNER JOIN care_ward AS cw ON sel.group_nr = cw.nr)
              INNER JOIN seg_accomodation_type sat ON cw.accomodation_type = sat.accomodation_nr
          WHERE (sel.encounter_nr = '$enc_nr' or sel.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr'))
            AND (
              STR_TO_DATE(
                sel.create_dt,
                '%Y-%m-%d %H:%i:%s'
              ) >= '" . $from_date . "'
              AND STR_TO_DATE(
                sel.create_dt,
                '%Y-%m-%d %H:%i:%s'
              ) <= '" . $to_date . "'
            )
          ORDER BY entry_no DESC LIMIT 1";
	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			while ($row = $result->FetchRow()) {
				$ntype = $row['accomodation_type'];
				$sname = $row['accomodation_name'];
			}
		}
	}

	if ($ntype == 0)
		return(getAddedAccommodationType($enc_nr, $from_date, $to_date, $ntype, $sname));
	else
		return($db->ErrorMsg() == '');
}

function isCharity($enc_nr) {
  $actype = '';
  $acname = '';
  getAccommodationType($enc_nr, $actype, $acname);
  return (!(strpos(strtoupper($acname), 'CHARITY', 0) === false));
}

function fillDiagnosisCaseTypeData($enc_nr) {
	global $db;

	$data = array();

	$n_id = getCaseTypeID($enc_nr);
//	$strSQL = "select ced.code, ifnull(if(sd.description = '', cie.description, sd.description), ifnull(cie.description, '')) as description
//						  from (care_encounter_diagnosis as ced inner join care_icd10_en as cie
//							 on ced.code = cie.diagnosis_code) left join seg_encounter_diagnosis as sd
//								on sd.encounter_nr = ced.encounter_nr and sd.code = ced.code and sd.is_deleted = 0
//						  where ced.encounter_nr = '$enc_nr' and status NOT IN ('deleted','hidden','inactive','void')
//						  order by diagnosis_nr";
    // Edited by LST -- 02.09.2012 -- per request of claims clerk of BPH MG

//edited by jasper 06/14/2013 CHANGED order by diagnosis_nr to sd.entry_no
$strSQL = "select distinct ced.code,
           (CASE WHEN sd.code_alt IS NULL OR sd.code_alt = '' or sd.code_alt = 'undefined' THEN ced.code ELSE sd.code_alt END) AS alt_code,
           ifnull(sd.description, ifnull(cie.description, '')) as description
                  from (care_encounter_diagnosis as ced inner join care_icd10_en as cie
                     on ced.code = cie.diagnosis_code) Inner join seg_encounter_diagnosis as sd
                        on sd.encounter_nr = ced.encounter_nr and sd.code = ced.code and sd.is_deleted = 0
                  where ced.encounter_nr = '$enc_nr' and ced.status NOT IN ('deleted','hidden','inactive','void') order by sd.entry_no";
     //echo $strSQL;
	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			while ($row = $result->FetchRow()) {
				$obj = new Diagnosis();
				$obj->code = $row["alt_code"];
				$obj->fin_diagnosis = $row["description"];
				$obj->case_type = $n_id;
				$data[] = $obj;
			}
		}
	}

	if (empty($data)) {
		$obj = new Diagnosis();
		$obj->fin_diagnosis = "";
		$obj->code = "";
		$obj->case_type = $n_id;
		$data[] = $obj;
	}

	return $data;
}
//added by jasper 05/02/2013
function getOPDateOPD($enc_nr) {
    global $db;

    $strSQL = "SELECT DISTINCT
                                ceo.op_date
                            FROM (seg_ops_personell AS sop
                                 INNER JOIN seg_ops_serv AS sos
                                     ON sop.refno = sos.refno)
                                LEFT JOIN care_encounter_op AS ceo
                                    ON sos.refno = ceo.refno
                            WHERE sos.encounter_nr = '$enc_nr'
                                    UNION ALL
                            SELECT DISTINCT
                                    smod.op_date
                                    FROM (seg_misc_ops AS smo
                                    INNER JOIN seg_misc_ops_details AS smod
                                    ON smod.refno = smo.refno)
                                    INNER JOIN seg_ops_chrg_dr AS socd
                                    ON smod.refno = socd.ops_refno
                                    AND smod.entry_no = socd.ops_entryno
                                    AND smod.ops_code = socd.ops_code
                                    WHERE smo.encounter_nr = '$enc_nr'
                            ORDER BY op_date";
    $op_date = "";
    if ($result = $db->Execute($strSQL)) {
        if ($result->RecordCount()) {
            while ($row = $result->FetchRow()) {
                if (!is_null($row['op_date'])) {
                    $op_date = $row['op_date'];
                }
            }
        }
    }
    return $op_date;
}
//added by jasper 05/02/2013

//edited by jasper 03/01/2013
function getOPDate($enc_nr, $dr_nr, $tmpservices) {
	global $db;

	$strSQL = "SELECT DISTINCT
								ceo.op_date, ceo.ops_code
							FROM (seg_ops_personell AS sop
								 INNER JOIN seg_ops_serv AS sos
									 ON sop.refno = sos.refno)
								LEFT JOIN care_encounter_op AS ceo
									ON sos.refno = ceo.refno
							WHERE sos.encounter_nr = '$enc_nr'
									AND sop.dr_nr = $dr_nr
                                    UNION ALL
                            SELECT DISTINCT
									smod.op_date, smod.ops_code
									FROM (seg_misc_ops AS smo
									INNER JOIN seg_misc_ops_details AS smod
									ON smod.refno = smo.refno)
									INNER JOIN seg_ops_chrg_dr AS socd
									ON smod.refno = socd.ops_refno
									AND smod.entry_no = socd.ops_entryno
									AND smod.ops_code = socd.ops_code
									WHERE smo.encounter_nr = '$enc_nr'
									AND socd.dr_nr = $dr_nr
							ORDER BY op_date";

//	$strSQL = "select distinct ceo.op_date \n
//					from (seg_ops_personell as sop inner join seg_ops_serv as sos on sop.refno = sos.refno) \n
//					 left join care_encounter_op as ceo on sos.refno = ceo.refno \n
//					where sos.encounter_nr = '$enc_nr' and sop.dr_nr = $dr_nr    \n
//					order by ceo.op_date";

	$op_dates = array();
    $op_code = array();
    $tmpserv =  explode("; ", $tmpservices);
	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
				    if (!is_null($row['op_date'])) {
                       //if (substr_count($tmpservices, trim($row['ops_code']))) {
                           $op_dates[] = $row['op_date'];
                           $op_code[] = $row['ops_code'];
                       //}
                  }
                    //$op_dates['code'] = $row["ops_code"];
            }
		}
	}
    $op_dte = array();
    for($i=0;$i<count($tmpserv);$i++) {
       $pos = strpos($tmpserv[$i], "(");
       if ($pos==0) { //for surgical and package covered cases
           $pos = strlen($tmpserv[$i]);
       }
       for($y=0;$y<count($op_code);$y++) {
           if (substr($tmpserv[$i],0,$pos)==$op_code[$y]) {
              $op_dte[] = $op_dates[$y];
           }
       }
       $operation_dates .= format_opdates($op_dte) . ($i<count($tmpserv)-1 ? "; " : "");
       $op_dte = array();
    }
    //$operation_dates = substr($operation_dates, 0, strlen($operation_dates) - 1);
	// .... could be a miscellaneous procedure added in billing ....
//	if (count($op_dates) == 0) {
//	if (strcmp($op_date, "0000-00-00") == 0) {
//		$strSQL = "select distinct smod.op_date   \n
//						from (seg_misc_ops as smo inner join seg_misc_ops_details as smod on smod.refno = smo.refno)  \n
//						 inner join seg_ops_chrg_dr as socd on smod.refno = socd.ops_refno and   \n
//							smod.entry_no = socd.ops_entryno and smod.ops_code = socd.ops_code     \n
//						where smo.encounter_nr = '$enc_nr' and socd.dr_nr = $dr_nr               \n
//						order by smod.op_date";

//		if ($result = $db->Execute($strSQL)) {
//			if ($result->RecordCount()) {
//				while ($row = $result->FetchRow()) {
//					if (!is_null($row["op_date"])) $op_dates[] = $row["op_date"];
//				}
//			}
//		}
//	}
    return $operation_dates;
	//return $op_dates;
}

function format_opdates($opdates) {
	$month = 0;
	$yr = 0;
	$day = 0;
	$days = "";
	$fdates = array();
	if (is_array($opdates) && (count($opdates) > 0)) {
		foreach($opdates as $date) {
			$dt = getdate(strtotime($date));
			if ($dt['year'] != $yr) {
				if ($yr != 0) {
					$fdates[] = $month."/".$days."/".$yr;
					$days = "";
				}
				$yr = $dt['year'];
				$month = $dt['mon'];
				$day = $dt['mday'];
				$days .= $day;
				continue;
			}
			if ($dt['mon'] != $month) {
				if ($month != 0) {
					$fdates[] = $month."/".$days;
					$days = "";
				}
				$month = $dt['mon'];
				$day = $dt['mday'];
				$days .= $day;
				continue;
			}
			if ($dt['mday'] != $day) {
				$day = $dt['mday'];
				if ($day != 0) $days .= (($days != '') ? "," : "").$day;
			}
		}
		$fdates[] = $month."/".$days."/".$yr;
		return implode(",",$fdates);
	}
	else
		return false;
}

function isHouseCase($enc_nr) {
	global $db, $pdf;

	$case = '';
    //edited by jasper 05/09/2013 - order by FROM  modify_dt CHANGED TO create_dt
	$sql = "select st.casetype_desc from seg_encounter_case sc
								inner join seg_type_case st on sc.casetype_id = st.casetype_id ".
				 "   where encounter_nr = '".$enc_nr."' and !sc.is_deleted ".
				 "   order by sc.modify_dt desc limit 1";

	if($result = $db->Execute($sql)){
			if($result->RecordCount()){
					if ($row = $result->FetchRow()) {
						$case = $row['casetype_desc'];
					}
			}
	}

//	return !(strpos($case, 'HOUSE') === false) && !$pdf->hasPrivateAccommodation($enc_nr);
	return !(strpos($case, 'HOUSE') === false);
}

function getHouseCaseDoctor($hcare_id, $role) {
	global $db;

  switch ($role) {
    case 'D1':
    case 'D2':
      $filter = "cpr.is_housecase_attdr = 1";
      break;
    case 'D3':
      $filter = "cpr.is_housecase_surgeon = 1";
      break;
    case 'D4':
      $filter = "cpr.is_housecase_anesth = 1";
  }
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

//edited by jasper 06/11/2013 TO FIX SEMICOLON AND WRAPPING OF DATES
/*function addPFInfo($role, $pfarr, $obj, $cnt = 0) {
  foreach($pfarr as $key=> $value) {
      if ($value->role_area == $role) {
        $value->servperformance .= (!empty($value->servperformance) ? "; " : "").$obj->servperformance;
        $value->profcharges += $obj->profcharges;
        $value->claim_physician += $obj->claim_physician;
        if ($role == 'D3')
          $value->operation_dt .= (!empty($value->operation_dt) ? "; " : "").$obj->operation_dt;
        else
          $value->inclusive_dates .= (!empty($value->inclusive_dates) ? "; " : "").$obj->inclusive_dates;
        $pfarr[$key] = $value;
        break;
      }
  }
}*/
function addPFInfo($role, $pfarr, $obj, $cnt = 0) {
  foreach($pfarr as $key=> $value) {
      if ($value->role_area == $role) {
        //$value->servperformance .= (!empty($value->servperformance) ? ";" : "").$obj->servperformance;
        $value->profcharges += $obj->profcharges;
        $value->claim_physician += $obj->claim_physician;
        if ($role == 'D3') {
          $value->servperformance .= (!empty($value->servperformance) ? "; " : "").$obj->servperformance;
          $value->operation_dt .= (!empty($value->operation_dt) ? "; " : "").$obj->operation_dt;
        }
        else {
          if ( in_array($role, array('D1', 'D2')) ) {
              if ($cnt==0) {
                  $value->servperformance .= (!empty($value->servperformance) ? ";" : "").$obj->servperformance;
                  $value->inclusive_dates .= (!empty($value->inclusive_dates) ? ";" : "").$obj->inclusive_dates;
              }
          } else {
              $value->servperformance .= (!empty($value->servperformance) ? "; " : "").$obj->servperformance;
              $value->inclusive_dates .= (!empty($value->inclusive_dates) ? "; " : "").$obj->inclusive_dates;
          }
        }
        $pfarr[$key] = $value;
        break;
      }
  }
}

function fillPFData($enc_nr, $hcare_id, &$data1, &$data2, &$confinementdata) {
	global $db, $pdf;

    if ($billnr = getLatestBillNr($enc_nr)) {
        $objbilling = new Billing($enc_nr, NULL, NULL, $billnr);
    }
    //added by jasper 04/23/2013
    if (!empty($confinementdata)) {
            foreach($confinementdata as $objconf){
                $date_admitted = $objconf->admit_dt;
                $time_admitted = $objconf->admit_tm;
                $date_discharged = $objconf->discharge_dt;
                $time_discharged = $objconf->discharge_tm;
                $claim_days = $objconf->claim_days;
                //added by jasper 07/01/2013
                $date_death = $objconf->death_dt;
                $encounter_type = $objconf->enc_type; //added by jasper 07/31/2103 FOR BUGZILLA #188
            }
    }
    //added by jasper 04/23/2013
	$data1 = array();
	$data2 = array();

	$from_date = getEarliestFromDate($enc_nr);
	$to_date   = getLatestRefDate($enc_nr);

	$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($to_date)));

	$hosp_obj = new Hospital_Admin();
	$cutoff_hrs = $hosp_obj->getCutoff_Hrs();
    /* editted by jasper 02/21/13 SQL changed to CAST(CONCAT(sor.code,'(',sor.rvu,')')AS CHAR) ORDER BY sor.code DESC SEPARATOR '; '
    * from sor.code ORDER BY sor.code DESC SEPARATOR '; '
    * 06/05/2013 CHANGED discharge_date TO mgh_setdte
    */
	$strSQL = "select attending_dr_nr as dr_nr, name_last, name_first, name_middle, 'Attending Physician' as role,
	 \n           (select sum(dr_charge) as tcharge from seg_billing_pf as sbp inner join seg_billing_encounter as sbe1
	 \n             on sbp.bill_nr = sbe1.bill_nr
	 \n             where sbp.dr_nr = t.attending_dr_nr and sbp.hcare_id = $hcare_id and sbe1.encounter_nr = '$enc_nr' and sbe1.is_deleted IS NULL) as charge,
	 \n          (select sum(dr_claim) as tclaim from seg_billing_pf as sbp2 inner join seg_billing_encounter as sbe2
	 \n             on sbp2.bill_nr = sbe2.bill_nr
	 \n             where sbp2.dr_nr = t.attending_dr_nr and sbp2.hcare_id = $hcare_id and sbe2.encounter_nr = '$enc_nr' and sbe2.is_deleted IS NULL) as claim,
	 \n          role_nr, role_area, 0 as rvu, 0 as multiplier,
	 \n          sum(fn_days_attended(attend_start, if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), ".$cutoff_hrs.")) as services, tin,
	 \n          (select accreditation_nr from seg_dr_accreditation as sda where sda.dr_nr = t.attending_dr_nr and sda.hcare_id = $hcare_id) as accno,
	 \n					 concat(date_format(attend_start, '%m/%d/%Y'), '-', date_format(if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), '%m/%d/%Y')) as inc_dates
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
	 \n          select spd.dr_nr, name_last, name_first, name_middle, (CASE WHEN crp.role_area <> 'D2' THEN `name` ELSE CONCAT(`name`, ' - private') END) as role,
	 \n              (select sum(dr_charge) as tcharge from seg_billing_pf as sbp inner join seg_billing_encounter as sbe1
	 \n                 on sbp.bill_nr = sbe1.bill_nr
	 \n                 where sbp.dr_nr = spd.dr_nr and sbp.hcare_id = $hcare_id and sbe1.encounter_nr = '$enc_nr' AND sbp.role_area = crp.role_area and sbe1.is_deleted IS NULL) as charge,
	 \n              (select sum(dr_claim) as tclaim from seg_billing_pf as sbp2 inner join seg_billing_encounter as sbe2
	 \n                 on sbp2.bill_nr = sbe2.bill_nr
	 \n                 where sbp2.dr_nr = spd.dr_nr and sbp2.hcare_id = $hcare_id and sbe2.encounter_nr = '$enc_nr' AND sbp2.role_area = crp.role_area and sbe2.is_deleted IS NULL) as claim,
	 \n              spd.dr_role_type_nr, role_area, sum(ifnull(socd.rvu,0)) as tot_rvu, (sum(ifnull(socd.multiplier,0) * ifnull(socd.rvu,0))/sum(ifnull(socd.rvu,0))) as avg_multiplier,
	 \n              (CASE WHEN crp.role_area = 'D1' OR crp.role_area = 'D2'
     \n                      THEN days_attended
     \n                      ELSE GROUP_CONCAT(DISTINCT CAST(CONCAT(sor.code,'(',sor.rvu,')')AS CHAR) ORDER BY sor.code DESC SEPARATOR '; ') END) as services, tin,
     \n              (select accreditation_nr from seg_dr_accreditation as sda where sda.dr_nr = spd.dr_nr and sda.hcare_id = $hcare_id) as accno,
     \n              (CASE WHEN (role_area = 'D1') OR (role_area = 'D2') THEN
     \n                   (CONCAT(DATE_FORMAT(spd.from_date, '%m/%d/%Y'), '-', DATE_FORMAT(spd.to_date, '%m/%d/%Y'))) ELSE '' END) as inc_dates
     \n              from ((seg_encounter_privy_dr as spd left join (seg_ops_chrg_dr as socd inner join seg_ops_rvs as sor on socd.ops_code = sor.code) on
     \n                 spd.encounter_nr = socd.encounter_nr and spd.dr_nr = socd.dr_nr and
     \n                 spd.dr_role_type_nr = socd.dr_role_type_nr) inner join (care_personell as cpn
     \n                 inner join care_person as cp on cpn.pid = cp.pid) on spd.dr_nr = cpn.nr)
     \n                 inner join care_role_person as crp on spd.dr_role_type_nr = crp.nr
     \n              where (spd.encounter_nr = '$enc_nr' or spd.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr'))
     \n                 and is_excluded = 0
     \n                 and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'
     \n                 and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'
     \n              group by spd.dr_nr, name_last, name_first, name_middle, crp.role_area
     \n          union
     \n           select dr_nr, name_last, name_first, name_middle, concat(name, ' - ', cop.code) as role,
     \n              (select sum(dr_charge) as tcharge from seg_billing_pf as sbp inner join seg_billing_encounter as sbe1
     \n                 on sbp.bill_nr = sbe1.bill_nr
     \n                 where sbp.dr_nr = sop.dr_nr and sbp.hcare_id = $hcare_id and sbe1.encounter_nr = '$enc_nr' and sbe1.is_deleted IS NULL) as charge,
     \n              (select sum(dr_claim) as tclaim from seg_billing_pf as sbp2 inner join seg_billing_encounter as sbe2
     \n                 on sbp2.bill_nr = sbe2.bill_nr
     \n                 where sbp2.dr_nr = sop.dr_nr and sbp2.hcare_id = $hcare_id and sbe2.encounter_nr = '$enc_nr' and sbe2.is_deleted IS NULL) as claim,
     \n           sop.role_type_nr, role_area, sum(sosd.rvu) as tot_rvu, (sum(multiplier * sosd.rvu)/sum(sosd.rvu)) as avg_multiplier,
     \n           group_concat(DISTINCT sor.code ORDER BY sor.code DESC SEPARATOR '; ') as services, tin,
     \n          (select accreditation_nr from seg_dr_accreditation as sda where sda.dr_nr = sop.dr_nr and sda.hcare_id = $hcare_id) as accno, '' as inc_dates
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
     \n              group by dr_nr, role_area
     \n           ORDER BY name_last, name_first, name_middle, role_area";
    //removed by jasper 06/18/2013
    /*$strSQL = "select attending_dr_nr as dr_nr, name_last, name_first, name_middle, 'Attending Physician' as role,
     \n           (select sum(dr_charge) as tcharge from seg_billing_pf as sbp inner join seg_billing_encounter as sbe1
     \n             on sbp.bill_nr = sbe1.bill_nr
     \n             where sbp.dr_nr = t.attending_dr_nr and sbp.hcare_id = $hcare_id and sbe1.encounter_nr = '$enc_nr' and sbe1.is_deleted IS NULL) as charge,
     \n          (select sum(dr_claim) as tclaim from seg_billing_pf as sbp2 inner join seg_billing_encounter as sbe2
     \n             on sbp2.bill_nr = sbe2.bill_nr
     \n             where sbp2.dr_nr = t.attending_dr_nr and sbp2.hcare_id = $hcare_id and sbe2.encounter_nr = '$enc_nr' and sbe2.is_deleted IS NULL) as claim,
     \n          role_nr, role_area, 0 as rvu, 0 as multiplier,
     \n          sum(fn_days_attended(attend_start, if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), ".$cutoff_hrs.")) as services, tin,
     \n          (select accreditation_nr from seg_dr_accreditation as sda where sda.dr_nr = t.attending_dr_nr and sda.hcare_id = $hcare_id) as accno,
     \n                     concat(date_format(attend_start, '%m/%d/%Y'), '-', date_format(if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), '%m/%d/%Y')) as inc_dates
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
     \n          select spd.dr_nr, name_last, name_first, name_middle, (CASE WHEN crp.role_area <> 'D2' THEN `name` ELSE CONCAT(`name`, ' - private') END) as role,
     \n              (select sum(dr_charge) as tcharge from seg_billing_pf as sbp inner join seg_billing_encounter as sbe1
     \n                 on sbp.bill_nr = sbe1.bill_nr
     \n                 where sbp.dr_nr = spd.dr_nr and sbp.hcare_id = $hcare_id and sbe1.encounter_nr = '$enc_nr' AND sbp.role_area = crp.role_area and sbe1.is_deleted IS NULL) as charge,
     \n              (select sum(dr_claim) as tclaim from seg_billing_pf as sbp2 inner join seg_billing_encounter as sbe2
     \n                 on sbp2.bill_nr = sbe2.bill_nr
     \n                 where sbp2.dr_nr = spd.dr_nr and sbp2.hcare_id = $hcare_id and sbe2.encounter_nr = '$enc_nr' AND sbp2.role_area = crp.role_area and sbe2.is_deleted IS NULL) as claim,
     \n              spd.dr_role_type_nr, role_area, sum(ifnull(socd.rvu,0)) as tot_rvu, (sum(ifnull(socd.multiplier,0) * ifnull(socd.rvu,0))/sum(ifnull(socd.rvu,0))) as avg_multiplier,
     \n              (CASE WHEN crp.role_area = 'D1' OR crp.role_area = 'D2'
                           THEN (CASE WHEN days_attended = 0
                                      THEN DATEDIFF((select mgh_setdte from care_encounter where encounter_nr = '$enc_nr'), (select admission_dt from care_encounter where encounter_nr = '$enc_nr'))
                                      ELSE CASE WHEN days_attended < DATEDIFF((SELECT mgh_setdte FROM care_encounter WHERE encounter_nr = '$enc_nr'),
                                      (SELECT admission_dt FROM care_encounter WHERE encounter_nr = '$enc_nr')) THEN days_attended ELSE
                                      DATEDIFF((SELECT mgh_setdte FROM care_encounter WHERE encounter_nr = '$enc_nr'), (SELECT admission_dt
                                      FROM care_encounter WHERE encounter_nr = '$enc_nr')) END
                                 END)
                           ELSE GROUP_CONCAT(DISTINCT CAST(CONCAT(sor.code,'(',sor.rvu,')')AS CHAR) ORDER BY sor.code DESC SEPARATOR '; ') END) as services, tin,
	 \n              (select accreditation_nr from seg_dr_accreditation as sda where sda.dr_nr = spd.dr_nr and sda.hcare_id = $hcare_id) as accno,
	 \n              (CASE WHEN (role_area = 'D1') OR (role_area = 'D2') THEN
											concat(date_format((select admission_dt from care_encounter where encounter_nr = '$enc_nr'), '%m/%d/%Y'), '-',
														 date_format((select mgh_setdte from care_encounter where encounter_nr = '$enc_nr'), '%m/%d/%Y')) ELSE '' END) as inc_dates
	 \n              from ((seg_encounter_privy_dr as spd left join (seg_ops_chrg_dr as socd inner join seg_ops_rvs as sor on socd.ops_code = sor.code) on
	 \n                 spd.encounter_nr = socd.encounter_nr and spd.dr_nr = socd.dr_nr and
	 \n                 spd.dr_role_type_nr = socd.dr_role_type_nr) inner join (care_personell as cpn
	 \n                 inner join care_person as cp on cpn.pid = cp.pid) on spd.dr_nr = cpn.nr)
	 \n                 inner join care_role_person as crp on spd.dr_role_type_nr = crp.nr
	 \n              where (spd.encounter_nr = '$enc_nr' or spd.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr'))
	 \n                 and is_excluded = 0
	 \n                 and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'
	 \n                 and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'
	 \n              group by spd.dr_nr, name_last, name_first, name_middle, crp.role_area
	 \n          union
	 \n           select dr_nr, name_last, name_first, name_middle, concat(name, ' - ', cop.code) as role,
	 \n              (select sum(dr_charge) as tcharge from seg_billing_pf as sbp inner join seg_billing_encounter as sbe1
	 \n                 on sbp.bill_nr = sbe1.bill_nr
	 \n                 where sbp.dr_nr = sop.dr_nr and sbp.hcare_id = $hcare_id and sbe1.encounter_nr = '$enc_nr' and sbe1.is_deleted IS NULL) as charge,
	 \n              (select sum(dr_claim) as tclaim from seg_billing_pf as sbp2 inner join seg_billing_encounter as sbe2
	 \n                 on sbp2.bill_nr = sbe2.bill_nr
	 \n                 where sbp2.dr_nr = sop.dr_nr and sbp2.hcare_id = $hcare_id and sbe2.encounter_nr = '$enc_nr' and sbe2.is_deleted IS NULL) as claim,
	 \n           sop.role_type_nr, role_area, sum(sosd.rvu) as tot_rvu, (sum(multiplier * sosd.rvu)/sum(sosd.rvu)) as avg_multiplier,
	 \n           group_concat(DISTINCT sor.code ORDER BY sor.code DESC SEPARATOR '; ') as services, tin,
	 \n          (select accreditation_nr from seg_dr_accreditation as sda where sda.dr_nr = sop.dr_nr and sda.hcare_id = $hcare_id) as accno, '' as inc_dates
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
	 \n              group by dr_nr, role_area
     \n           ORDER BY name_last, name_first, name_middle, role_area";*/

	/*$strSQL = "select attending_dr_nr as dr_nr, name_last, name_first, name_middle, 'Attending Physician' as role,
	 \n           (select sum(dr_charge) as tcharge from seg_billing_pf as sbp inner join seg_billing_encounter as sbe1
	 \n             on sbp.bill_nr = sbe1.bill_nr
	 \n             where sbp.dr_nr = t.attending_dr_nr and sbp.hcare_id = $hcare_id and sbe1.encounter_nr = '$enc_nr' and sbe1.is_deleted IS NULL) as charge,
	 \n          (select sum(dr_claim) as tclaim from seg_billing_pf as sbp2 inner join seg_billing_encounter as sbe2
	 \n             on sbp2.bill_nr = sbe2.bill_nr
	 \n             where sbp2.dr_nr = t.attending_dr_nr and sbp2.hcare_id = $hcare_id and sbe2.encounter_nr = '$enc_nr' and sbe2.is_deleted IS NULL) as claim,
	 \n          role_nr, role_area, 0 as rvu, 0 as multiplier,
	 \n          sum(fn_days_attended(attend_start, if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), ".$cutoff_hrs.")) as services, tin,
	 \n          (select accreditation_nr from seg_dr_accreditation as sda where sda.dr_nr = t.attending_dr_nr and sda.hcare_id = $hcare_id) as accno,
	 \n					 concat(date_format(attend_start, '%m/%d/%Y'), '-', date_format(if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), '%m/%d/%Y')) as inc_dates
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
	 \n          select spd.dr_nr, name_last, name_first, name_middle, (CASE WHEN crp.role_area <> 'D2' THEN `name` ELSE CONCAT(`name`, ' - private') END) as role,
	 \n              (select sum(dr_charge) as tcharge from seg_billing_pf as sbp inner join seg_billing_encounter as sbe1
	 \n                 on sbp.bill_nr = sbe1.bill_nr
	 \n                 where sbp.dr_nr = spd.dr_nr and sbp.hcare_id = $hcare_id and sbe1.encounter_nr = '$enc_nr' AND sbp.role_area = crp.role_area and sbe1.is_deleted IS NULL) as charge,
	 \n              (select sum(dr_claim) as tclaim from seg_billing_pf as sbp2 inner join seg_billing_encounter as sbe2
	 \n                 on sbp2.bill_nr = sbe2.bill_nr
	 \n                 where sbp2.dr_nr = spd.dr_nr and sbp2.hcare_id = $hcare_id and sbe2.encounter_nr = '$enc_nr' AND sbp2.role_area = crp.role_area and sbe2.is_deleted IS NULL) as claim,
	 \n              spd.dr_role_type_nr, role_area, sum(ifnull(socd.rvu,0)) as tot_rvu, (sum(ifnull(socd.multiplier,0) * ifnull(socd.rvu,0))/sum(ifnull(socd.rvu,0))) as avg_multiplier,
	 \n              (CASE WHEN crp.role_area = 'D1' OR crp.role_area = 'D2'
                           THEN (CASE WHEN days_attended = 0
                                      THEN DATEDIFF((select discharge_date from care_encounter where encounter_nr = '$enc_nr'), (select admission_dt from care_encounter where encounter_nr = '$enc_nr'))
                                      ELSE days_attended
                                 END)
                           ELSE GROUP_CONCAT(DISTINCT CAST(CONCAT(sor.code,'(',sor.rvu,')')AS CHAR) ORDER BY sor.code DESC SEPARATOR '; ') END) as services, tin,
	 \n              (select accreditation_nr from seg_dr_accreditation as sda where sda.dr_nr = spd.dr_nr and sda.hcare_id = $hcare_id) as accno,
	 \n              (CASE WHEN (role_area = 'D1') OR (role_area = 'D2') THEN
											concat(date_format((select admission_dt from care_encounter where encounter_nr = '$enc_nr'), '%m/%d/%Y'), '-',
														 date_format((select discharge_date from care_encounter where encounter_nr = '$enc_nr'), '%m/%d/%Y')) ELSE '' END) as inc_dates
	 \n              from ((seg_encounter_privy_dr as spd left join (seg_ops_chrg_dr as socd inner join seg_ops_rvs as sor on socd.ops_code = sor.code) on
	 \n                 spd.encounter_nr = socd.encounter_nr and spd.dr_nr = socd.dr_nr and
	 \n                 spd.dr_role_type_nr = socd.dr_role_type_nr) inner join (care_personell as cpn
	 \n                 inner join care_person as cp on cpn.pid = cp.pid) on spd.dr_nr = cpn.nr)
	 \n                 inner join care_role_person as crp on spd.dr_role_type_nr = crp.nr
	 \n              where (spd.encounter_nr = '$enc_nr' or spd.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr'))
	 \n                 and is_excluded = 0
	 \n                 and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'
	 \n                 and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'
	 \n              group by spd.dr_nr, name_last, name_first, name_middle, crp.role_area
	 \n          union
	 \n           select dr_nr, name_last, name_first, name_middle, concat(name, ' - ', cop.code) as role,
	 \n              (select sum(dr_charge) as tcharge from seg_billing_pf as sbp inner join seg_billing_encounter as sbe1
	 \n                 on sbp.bill_nr = sbe1.bill_nr
	 \n                 where sbp.dr_nr = sop.dr_nr and sbp.hcare_id = $hcare_id and sbe1.encounter_nr = '$enc_nr' and sbe1.is_deleted IS NULL) as charge,
	 \n              (select sum(dr_claim) as tclaim from seg_billing_pf as sbp2 inner join seg_billing_encounter as sbe2
	 \n                 on sbp2.bill_nr = sbe2.bill_nr
	 \n                 where sbp2.dr_nr = sop.dr_nr and sbp2.hcare_id = $hcare_id and sbe2.encounter_nr = '$enc_nr' and sbe2.is_deleted IS NULL) as claim,
	 \n           sop.role_type_nr, role_area, sum(sosd.rvu) as tot_rvu, (sum(multiplier * sosd.rvu)/sum(sosd.rvu)) as avg_multiplier,
	 \n           group_concat(DISTINCT sor.code ORDER BY sor.code DESC SEPARATOR '; ') as services, tin,
	 \n          (select accreditation_nr from seg_dr_accreditation as sda where sda.dr_nr = sop.dr_nr and sda.hcare_id = $hcare_id) as accno, '' as inc_dates
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
	 \n              group by dr_nr, role_area
	 \n           ORDER BY name_last, name_first, name_middle, role_area";*/
     /*if ($_SESSION['sess_temp_userid']=='medocs')
     {
       echo $strSQL;
     }*/

     $bConsultantNoted = false;
	 $bSurgeonNoted = false;
	 $bAnesthNoted = false;
     //added by jasper 07/11/2013
     $role_areas = array('D1', 'D2', 'D3', 'D4');
     $housecase_doctor_numbers = "";
     foreach($role_areas as $area) {
        $result = getHouseCaseDoctor($hcare_id, $area);
        $housecase_doctor_numbers .= $result['nr'] . ", ";
     }
     $arrDocNos =  explode(", ", $housecase_doctor_numbers);
     //added by jasper 07/11/2013

     //added by jasper 02/28/2013
     $HouseCaseConsultant_cnt = 0;
	 if ($result = $db->Execute($strSQL)) {
         //added by jasper 07/11/2013
         $record_count = $result->RecordCount();
         /*if ($_SESSION['sess_temp_userid']=='medocs')
         {
            echo "R_count: " . $record_count . " HouseCase: " . isHouseCase($enc_nr) . " Surgical: " . $objbilling->isSurgicalCase() . " HS: " . $housecase_doctor_numbers;
            echo in_array($row["dr_nr"], $arrDocNos);

         }*/
		if ($result->RecordCount()) {
			while ($row = $result->FetchRow()) {
        $nclaim = (is_null($row["claim"])) ? 0 : $row["claim"];
        if ($nclaim != 0) {
            if ($row["role_area"] == 'D3')
              $obj = new Surgeon();
            else
              $obj = new HealthPersonnel();

            $res = false;
            $bjustadd = false;
            if (isHouseCase($enc_nr)) { //&& !isCharity($enc_nr) ) {
              if ( in_array($row["role_area"], array('D1', 'D2')) ) {
                // Consultant ...
                if ( !$bConsultantNoted ) {
                  //added by jasper 06/26/2013
                    //DR. DOLENDO WILL BE THE ATTENDING PHYSICIAN IF THE PATIENT HAS SURGICAL PROCEDURE. IF NOT DR. VEGA IS THE AP
                    if ($objbilling->isSurgicalCase()) {
                        if (!(in_array($row["dr_nr"], $arrDocNos))) $res = getHouseCaseDoctor($hcare_id, $row["role_area"]);
                        if ($res) $bConsultantNoted = true;
                    } else { //ADDED BY JASPER 07/11/2013 - AUTOMATIC DR. VEGA IF NOT SURIGICAL
                         $res = getHouseCaseDoctor($hcare_id, "D3");
                         if ($res) $bConsultantNoted = true;
                    }
                    //added by jasper 06/26/2013
                }
                else
                  $bjustadd = true;
              }
              else if ( $row["role_area"] == 'D3' ) {
                // Surgeon ...
                if ( !$bSurgeonNoted ) {
                  $res = getHouseCaseDoctor($hcare_id, $row["role_area"]);
                  if ($res) $bSurgeonNoted = true;
                }
                else
                  $bjustadd = true;
              }
              else {
                // Anaesthesiologist ...
                if ( $row["role_area"] == 'D4' ) {
                  if ( !$bAnesthNoted ) {
                    $res = getHouseCaseDoctor($hcare_id, $row["role_area"]);
                    if ($res) $bAnesthNoted = true;
                  }
                  else
                    $bjustadd = true;
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
            //edited by jasper 02/28/2013
            //$obj->servperformance = (is_null($row["services"])) ? "" : $row["services"];
            if ( in_array($row["role_area"], array('D3', 'D4')) ) {
                //if ($pdf->issurgical && $pdf->pkglimit()>0) { - cannot accomodate NSD and CS???
                if ($objbilling->isSurgicalCase() && $objbilling->getPkgAmountLimit()>0) {
                    $obj->servperformance = formatOpsCodePackage((is_null($row["services"])) ? "" : $row["services"]);
                } else {
                    $obj->servperformance = countOpsCodebyDate($enc_nr, $row["dr_nr"],(is_null($row["services"])) ? "" : $row["services"]);
                }
            }
            //to remove the last semicolon
            $obj->servperformance = substr($obj->servperformance,0, strlen($obj->servperformance)-2);
            //$obj->servperformance = sortOPCodesbyDate($enc_nr, $row["dr_nr"],(is_null($row["services"])) ? "" : $row["services"]);
            $obj->profcharges = (is_null($row["charge"])) ? 0 : $row["charge"];
            $obj->claim_physician = (is_null($row["claim"])) ? 0 : $row["claim"];
            $obj->claim_patient = 0;
            $obj->role_area = $row["role_area"];
            if ($row["role_area"] == 'D3') {
              $op_dte = getOPDate($enc_nr, $row["dr_nr"],$obj->servperformance);
              //added by jasper 02/28/2013
              //if (is_array($op_dte) && (count($op_dte) > 0)) $obj->operation_dt = format_opdates($op_dte);
              if ($op_dte<>"" && !(is_null($op_dte))) $obj->operation_dt = $op_dte;
              if ($bjustadd)
                addPFInfo($row["role_area"], $data2, $obj);
              else
                $data2[] = $obj;
            }
            else {
              if ($row["role_area"] == 'D4') {
                $op_dte = getOPDate($enc_nr, $row["dr_nr"],$obj->servperformance);
                //if (is_array($op_dte) && (count($op_dte) > 0)) $obj->inclusive_dates = format_opdates($op_dte);
                if($op_dte<>"" && !(is_null($op_dte))) $obj->inclusive_dates = $op_dte;
              }
              else {
                //Edited by jasper 03/26/2013 to display days attended by attending doctor
                    if ( in_array($row["role_area"], array('D1', 'D2')) ) {
                        //if (($bHouseCase = isHouseCase($enc_nr)) && !isCharity($enc_nr)) {
                        if (isHouseCase($enc_nr)) {
                             if ($HouseCaseConsultant_cnt==0) {
                                 $HouseCaseConsultant_cnt++;
                                 $obj->servperformance = $claim_days;
                                 $obj->inclusive_dates = $date_admitted . "-" . $date_discharged;
                             }
                        } else {
                             $obj->servperformance = (is_null($row["services"])) ? "" : $row["services"];
                            /* added by jasper 06/26/2013
                             * added by jasper 07/01/2013 - $date_death <> '00-00-0000'*/
                            if (is_null($row["inc_dates"])) { // || $date_death <> '00-00-0000') {
                                $obj->inclusive_dates = $date_admitted . "-" . $date_discharged;
                            } else {
                                    $obj->inclusive_dates = $row["inc_dates"];
                                }
                            }
                        //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY
                        if (isNewBornPackage($enc_nr)) {
                            $obj->servperformance = $objbilling->getPkgCode($to_date);
                            $obj->inclusive_dates = "";
                        }
                        //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY
                    }
                    //$obj->inclusive_dates = $row["inc_dates"];
              }
              if ($bjustadd)
                addPFInfo($row["role_area"], $data1, $obj, $HouseCaseConsultant_cnt);
              else
                $data1[] = $obj;
            }
        } // nclaim != 0
			} 	// while loop
		}
	}
}


function isNewBornPackage($enc_nr) {
    global $db;

    $strSQL = "SELECT
                  sp.package_name
                FROM
                  (
                    seg_packages sp
                    INNER JOIN
                    seg_billing_pkg sbp
                    ON sp.package_id = sbp.package_id
                  )
                  INNER JOIN
                  seg_billing_encounter sbe
                  ON sbp.ref_no = sbe.bill_nr
                WHERE sbe.encounter_nr = '$enc_nr'";

	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
            if ($row = $result->FetchRow()){
            	$pkg_name = $row['package_name'];
                if (!(strpos(trim(strtoupper($pkg_name)), DEFAULT_NBPKG_NAME, 0) === false)) {
                    return True;
                }else{return false;}
            }else{return false;}
        }else{return false;}
	}else{return false;}
}


//added by jasper 02/28/2013
function formatOpsCodePackage($tmpservices) {
   $tmpserv =  explode("; ", $tmpservices);
   for($i=0;$i<count($tmpserv);$i++) {
        $pos = strpos($tmpserv[$i], "(");
        if ($i==count($tmpserv)-1)
            $code .= substr($tmpserv[$i],0,$pos) . "(P); ";
        else
            $code .= substr($tmpserv[$i],0,$pos) . "; ";
   }
   return $code;
}


function countOpsCodebyDate($enc_nr, $dr_nr, $tmpservices) {
   global $db;
   $strSQL = "SELECT GROUP_CONCAT(ceo.ops_code ORDER BY ceo.op_date SEPARATOR ' ' ) AS codes
                            FROM (seg_ops_personell AS sop
                                 INNER JOIN seg_ops_serv AS sos
                                     ON sop.refno = sos.refno)
                                LEFT JOIN care_encounter_op AS ceo
                                    ON sos.refno = ceo.refno
                            WHERE sos.encounter_nr = '$enc_nr'
                                    AND sop.dr_nr = '$dr_nr'
                                    UNION
              SELECT GROUP_CONCAT(smod.ops_code ORDER BY smod.op_date SEPARATOR ' ' ) AS codes
                                    FROM (seg_misc_ops AS smo
                                    INNER JOIN seg_misc_ops_details AS smod
                                    ON smod.refno = smo.refno)
                                    INNER JOIN seg_ops_chrg_dr AS socd
                                    ON smod.refno = socd.ops_refno
                                    AND smod.entry_no = socd.ops_entryno
                                    AND smod.ops_code = socd.ops_code
                                    WHERE smo.encounter_nr = '$enc_nr'
                                    AND socd.dr_nr = '$dr_nr'";

   $ops_codes = array();
   $tmpserv =  explode("; ", $tmpservices);

   if ($result = $db->Execute($strSQL)) {
        if ($result->RecordCount()) {
            while ($row = $result->FetchRow()) {
                if (!is_null($row["codes"])) {
                    $ops_codes['codes'] = $row["codes"];
                }
            }

            if ($ops_codes['codes'] && !(is_null($ops_codes['codes'])) && $ops_codes['codes']<>""){
                for($i=0;$i<count($tmpserv);$i++) {
                    $pos = strpos($tmpserv[$i], "(");
                    //if (!is_null($ops_codes['codes']))
                    $code_cnt = substr_count($ops_codes['codes'], substr($tmpserv[$i],0,$pos));
                    //else
                    //    $code_cnt = 0;

                    if ($code_cnt > 1)
                      $code .= $tmpserv[$i] . $code_cnt . "X; ";
                    else
                      $code .=  $tmpserv[$i] . "; ";

           //    if (substr($tmpserv[$i],0,$pos)==$op_code[$y]) {
           /*         if (!is_null($ops_codes['codes']))
                        $code_cnt = substr_count($ops_codes['codes'], $tmpcode);
                    else
                        $code_cnt = 0;

                    if ($code_cnt > 1)
                      $cnt = $code_cnt . "X";
                    else
                      $cnt = ""; */
                 }
            }else{
                if (!(is_null($ops_codes['codes'])) && trim($ops_codes['codes'])<>"")
                    $code .=  $tmpserv[$i] . "; ";
            }
        }
   }
   //$code = substr($code,0,strlen($code)-2);
   return $code;
}

/**
* Created by Jarel
* Created on 10/23/2013
* Used to get the parent encounter
* @param string enc_nr
* @return string prev_encounter_nr
*/
function getParentEncounter($enc_nr)
{
	global $db;

	$strSQL = "select parent_encounter_nr
					from care_encounter
					where encounter_nr = '".$enc_nr."'";
	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			$row = $result->FetchRow();
			$prev_encounter_nr = $row['parent_encounter_nr'];
		}
	}

	return($prev_encounter_nr);
}



//added by jasper 02/28/2013

function getDrugsandMeds($enc_nr, $hcare_id) {
	global $db;

	$data = array();

	$from_date = getEarliestFromDate($enc_nr);
	$to_date   = getLatestRefDate($enc_nr);

	/*$strSQL = "select bestellnum, generic, artikelname, description, max(flag) as flag, sum(qty) as qty, (sum(price * qty)/sum(qty)) as price, sum(itemcharge) as itemcharge, ".
				"   (select sum(coverage) as tcoverage from seg_applied_coverage as sac
						where (exists (select * from seg_billing_encounter as sbe where sbe.bill_nr = sac.ref_no and sbe.encounter_nr = '$enc_nr') or
						sac.ref_no = concat('T', '$enc_nr')) and hcare_id = $hcare_id and source = 'M' and sac.item_code = t.bestellnum) as claim ".
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
					group by bestellnum, artikelname order by artikelname";*/
    //removed by jasper 06/07/2013
	/*$strSQL = "select bestellnum, generic, artikelname, description, max(flag) as flag, sum(qty) as qty, (sum(price * qty)/sum(qty)) as price, sum(itemcharge) as itemcharge, ".
				"   (select sum(coverage) as tcoverage
                        from seg_applied_coverage sac left join seg_billing_encounter sbe on sbe.bill_nr = sac.ref_no
					 where (sbe.encounter_nr = '$enc_nr' or sac.ref_no = concat('T', '$enc_nr'))
                        and hcare_id = $hcare_id and source = 'M' and sac.item_code = t.bestellnum and sbe.is_deleted IS NULL) as claim ".
				" from ".
				"(select 0 as flag, pd.bestellnum, generic, artikelname, description, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as price, sum((pd.quantity - ifnull(spri.quantity, 0)) * pricecharge) as itemcharge ".
				"   from ((seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
				"         inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'M') ".
				"         left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum ".
				"      where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and is_cash = 0 and pd.serve_status <> 'N' and pd.request_flag is null ".
				"        and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
				"        and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'" .
				"        and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'" .
				"   group by pd.bestellnum, artikelname ".
				" union all ".
				"select 1 as flag, mpd.bestellnum, generic, artikelname, description, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as price, sum(quantity * unit_price) as itemcharge ".
				"   from (seg_more_phorder as mph inner join seg_more_phorder_details as mpd on mph.refno = mpd.refno) ".
				"      inner join care_pharma_products_main as p on mpd.bestellnum = p.bestellnum and p.prod_class = 'M' ".
				"   where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
				"       and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
				"       and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
				"   group by mpd.bestellnum, artikelname) as t
				    LEFT JOIN (SELECT * FROM seg_applied_coverage c
				    				      WHERE hcare_id = $hcare_id AND source = 'M'
				    				         AND (c.ref_no = CONCAT('T', '$enc_nr')
				    				            OR c.ref_no = (SELECT bill_nr FROM seg_billing_encounter sbe WHERE sbe.encounter_nr = '$enc_nr' and sbe.is_deleted IS NULL))
						) AS sac ON sac.item_code = t.bestellnum
											group by bestellnum, artikelname order by artikelname";*/
    //edited by jasper 06/07/2013
    /*  ORIGINAL left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum ".
        NEW               left join (SELECT rd.ref_no, rd.bestellnum, SUM(quantity) AS quantity FROM seg_pharma_return_items AS rd " .
                "         INNER JOIN seg_pharma_returns AS rh ON rd.return_nr = rh.return_nr AND (rh.encounter_nr = '$enc_nr') " .
                "         WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = '$enc_nr') AND rd.ref_no = oh.refno) GROUP BY rd.ref_no, rd.bestellnum) AS spri ON pd.refno = spri.ref_no AND pd.bestellnum = spri.bestellnum " .*/
    //edited by jasper 07/23/2013 - REMOVED  or sac.ref_no = concat('T', '$enc_nr') TO AVOID INCONSISTENCIES IN DRUGS AND MEDS.
    //THIS CONDITION IS FOR TEMPORY COVERAGE TO PHIC WHICH IS NOT INCLUDED IN SAVED BILL.
    /*$strSQL = "select bestellnum, generic, artikelname, description, max(flag) as flag, sum(qty) as qty, (sum(price * qty)/sum(qty)) as price, sum(itemcharge) as itemcharge, ".
                "   (select sum(coverage) as tcoverage
                        from seg_applied_coverage sac left join seg_billing_encounter sbe on sbe.bill_nr = sac.ref_no
                     where (sbe.encounter_nr = '$enc_nr' or sac.ref_no = concat('T', '$enc_nr'))
                        and hcare_id = $hcare_id and source = 'M' and sac.item_code = t.bestellnum and sbe.is_deleted IS NULL) as claim ".
                " from ".
                "(select 0 as flag, pd.bestellnum, generic, artikelname, description, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as price, sum((pd.quantity - ifnull(spri.quantity, 0)) * pricecharge) as itemcharge ".
                "   from ((seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
                "         inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'M') ".
                "         left join (SELECT rd.ref_no, rd.bestellnum, SUM(quantity) AS quantity FROM seg_pharma_return_items AS rd " .
                "         INNER JOIN seg_pharma_returns AS rh ON rd.return_nr = rh.return_nr AND (rh.encounter_nr = '$enc_nr') " .
                "         WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = '$enc_nr') AND rd.ref_no = oh.refno) GROUP BY rd.ref_no, rd.bestellnum) AS spri ON pd.refno = spri.ref_no AND pd.bestellnum = spri.bestellnum " .
                "      where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and is_cash = 0 and pd.serve_status <> 'N' and pd.request_flag is null ".
                "        and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
                "        and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'" .
                "        and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'" .
                "   group by pd.bestellnum, artikelname ".
                " union all ".
                "select 1 as flag, mpd.bestellnum, generic, artikelname, description, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as price, sum(quantity * unit_price) as itemcharge ".
                "   from (seg_more_phorder as mph inner join seg_more_phorder_details as mpd on mph.refno = mpd.refno) ".
                "      inner join care_pharma_products_main as p on mpd.bestellnum = p.bestellnum and p.prod_class = 'M' ".
                "   where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
                "       and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
                "       and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
                "   group by mpd.bestellnum, artikelname) as t
                    LEFT JOIN (SELECT * FROM seg_applied_coverage c
                                          WHERE hcare_id = $hcare_id AND source = 'M'
                                             AND (c.ref_no = CONCAT('T', '$enc_nr')
                                                OR c.ref_no = (SELECT bill_nr FROM seg_billing_encounter sbe WHERE sbe.encounter_nr = '$enc_nr' and sbe.is_deleted IS NULL))
                        ) AS sac ON sac.item_code = t.bestellnum
                                            group by bestellnum, artikelname order by artikelname";*/
     $strSQL = "select bestellnum, generic, artikelname, description, max(flag) as flag, sum(qty) as qty, (sum(price * qty)/sum(qty)) as price, sum(itemcharge) as itemcharge, ".
                "   (select sum(coverage) as tcoverage
                        from seg_applied_coverage sac left join seg_billing_encounter sbe on sbe.bill_nr = sac.ref_no
                     where (sbe.encounter_nr = '$enc_nr')
                        and hcare_id = $hcare_id and source = 'M' and sac.item_code = t.bestellnum and sbe.is_deleted IS NULL) as claim ".
                " from ".
                "(select 0 as flag, pd.bestellnum, generic, artikelname, description, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as price, sum((pd.quantity - ifnull(spri.quantity, 0)) * pricecharge) as itemcharge ".
                "   from ((seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
                "         inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'M') ".
                "         left join (SELECT rd.ref_no, rd.bestellnum, SUM(quantity) AS quantity FROM seg_pharma_return_items AS rd " .
                "         INNER JOIN seg_pharma_returns AS rh ON rd.return_nr = rh.return_nr AND (rh.encounter_nr = '$enc_nr') " .
                "         WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = '$enc_nr') AND rd.ref_no = oh.refno) GROUP BY rd.ref_no, rd.bestellnum) AS spri ON pd.refno = spri.ref_no AND pd.bestellnum = spri.bestellnum " .
                "      where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) and is_cash = 0 and pd.serve_status <> 'N' and pd.request_flag is null ".
                "        and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
                "        and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'" .
                "        and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'" .
                "   group by pd.bestellnum, artikelname ".
                " union all ".
                "select 1 as flag, mpd.bestellnum, generic, artikelname, description, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as price, sum(quantity * unit_price) as itemcharge ".
                "   from (seg_more_phorder as mph inner join seg_more_phorder_details as mpd on mph.refno = mpd.refno) ".
                "      inner join care_pharma_products_main as p on mpd.bestellnum = p.bestellnum and p.prod_class = 'M' ".
                "   where (encounter_nr = '$enc_nr' or encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
                "       and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
                "       and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
                "   group by mpd.bestellnum, artikelname) as t
                    LEFT JOIN (SELECT * FROM seg_applied_coverage c
                                          WHERE hcare_id = $hcare_id AND source = 'M'
                                             AND (c.ref_no = (SELECT bill_nr FROM seg_billing_encounter sbe WHERE sbe.encounter_nr = '$enc_nr' and sbe.is_deleted IS NULL))
                        ) AS sac ON sac.item_code = t.bestellnum
                                            group by bestellnum, artikelname order by artikelname";
    /*if ($_SESSION['sess_temp_userid']=='medocs')
     {
        echo $strSQL;
     }*/
	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			while ($row = $result->FetchRow()) {
				$obj = new Form2Meds();
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

//function getXRayLabOthers($enc_nr, $hcare_id, &$xraylab_array, &$supp_array, &$others_array) {
function getXRayLabOthers($enc_nr, $hcare_id, &$xray_array, &$lab_array, &$supp_array) {
//	global $db;

	//$xraylab_array = array();
	$xray_array 	 = array();
	$lab_array		 = array();
	$supp_array    = array();
	//$others_array  = array();

	$from_date = getEarliestFromDate($enc_nr);
	$to_date   = getLatestRefDate($enc_nr);

	#Added by Jarel 10/23/2013 get parent encounter
	$parent_enc_nr = getParentEncounter($enc_nr);
	$filter = '';
	if ($parent_enc_nr != '') $filter = " or encounter_nr = '$parent_enc_nr'";

	$strSQL = "select distinct ld.service_code, ls.name as service_desc, sum(ld.quantity) as qty, (sum(ld.price_charge * ld.quantity)/sum(ld.quantity)) as price,
									 sum(ld.quantity * ld.price_charge) as itemcharge, fn_gettotalclaim($hcare_id, ld.service_code, '$enc_nr') as claim, 'LB' as source " .
						"   from (((seg_lab_serv as lh inner join seg_lab_servdetails as ld on lh.refno = ld.refno) " .
						"          inner join seg_lab_services as ls on ld.service_code = ls.service_code) " .
						"          inner join seg_lab_service_groups as lsg on ls.group_code = lsg.group_code) ".
					    "      WHERE (CASE WHEN serv_dt >= DATE('".ISSRVD_EFFECTIVITY."') THEN ld.is_served ELSE 1 END) AND ".
						"         /*ld.is_served <> 0 and*/ lh.is_cash = 0 and (ld.request_flag is null OR ld.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
						"         and (encounter_nr = '$enc_nr' ".$filter.") and upper(trim(ld.status)) <> 'DELETED' " .
						"         and str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "' ".
						"         and str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "' ".
						"      group by ld.service_code, ls.name, ls.group_code, lsg.name, source";
	putinXLOitemized_array($strSQL, $xray_array, $lab_array, $supp_array);
	
	$strSQL = "select rd.service_code, rs.name as service_desc, count(rd.service_code) as qty, (sum(rd.price_charge)/count(rd.service_code)) as price,
										sum(rd.price_charge) as itemcharge, fn_gettotalclaim($hcare_id, rd.service_code, '$enc_nr') as claim, 'RD' as source " .
						 "   from (((seg_radio_serv as rh inner join care_test_request_radio as rd on rh.refno = rd.refno) " .
						 "          inner join seg_radio_services as rs on rd.service_code = rs.service_code) " .
						 "          inner join seg_radio_service_groups as rsg on rs.group_code = rsg.group_code) ".
                        "      WHERE (CASE WHEN rh.request_date >= DATE('".ISSRVD_EFFECTIVITY."') THEN rd.is_served ELSE 1 END) AND ".
						"         /*upper(rd.status) = 'DONE' and*/ rh.is_cash = 0 and (rd.request_flag is null OR rd.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
						"         and str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
						"         and str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
						"         and (encounter_nr = '$enc_nr' ".$filter.") and upper(trim(rd.status)) <> 'DELETED' " .
						"         and upper(trim(rd.status)) <> 'DELETED' ".
						"   group by rd.service_code, rs.name, rs.group_code, rsg.name, source ";
	putinXLOitemized_array($strSQL, $xray_array, $lab_array, $supp_array);

	$strSQL = "select service_code, service_desc, sum(qty) as qty, (sum(price * qty)/sum(qty)) as price, sum(itemcharge) as itemcharge, ".
						"   fn_gettotalclaim($hcare_id, service_code, '$enc_nr') as claim, t.source ".
						" from ".
						"(select 0 as grp, pd.bestellnum as service_code, artikelname as service_desc, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as price,
									 sum((pd.quantity - ifnull(spri.quantity, 0)) * pricecharge) as itemcharge, 'SU' as source ".
						"   from (((seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
						"      inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'S') ".
						"      left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum) ".
						"   where (encounter_nr = '$enc_nr' ".$filter.") and is_cash = 0 and pd.serve_status <> 'N' and pd.request_flag is null ".
						"      and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
						"      and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
						"      and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
						"   group by pd.bestellnum, artikelname ".
						" UNION ".
						"select 1 as grp, mphd.bestellnum as service_code, artikelname as service_desc, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as price,
									 sum(quantity * unit_price) as itemcharge, 'SU' as source ".
						"   from ((seg_more_phorder_details as mphd inner join seg_more_phorder as mph on mphd.refno = mph.refno) ".
						"      inner join care_pharma_products_main as p on mphd.bestellnum = p.bestellnum and p.prod_class = 'S') ".
						"   where (encounter_nr = '$enc_nr' ".$filter.") ".
						"      and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
						"      and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
						"   group by mphd.bestellnum, artikelname) as t
								GROUP BY service_desc ORDER BY service_desc";
	putinXLOitemized_array($strSQL, $xray_array, $lab_array, $supp_array);

	$strSQL = "select eqd.equipment_id as service_code, artikelname as service_desc, sum(number_of_usage) as qty, (sum(discounted_price * number_of_usage)/sum(number_of_usage)) as price,
									sum(number_of_usage * discounted_price) as itemcharge, fn_gettotalclaim($hcare_id, eqd.equipment_id, '$enc_nr') as claim, 'OE' as source
							 from ((seg_equipment_orders as eqh inner join seg_equipment_order_items as eqd on eqh.refno = eqd.refno)
									 inner join seg_ops_serv as sos on sos.refno = eqh.request_refno)
									 inner join care_pharma_products_main as cppm on cppm.bestellnum = eqd.equipment_id
							 where (sos.encounter_nr = '$enc_nr' ".$filter.")
									and str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'
									and str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'
							 group by eqd.equipment_id, artikelname";
	putinXLOitemized_array($strSQL, $xray_array, $lab_array, $supp_array);

	$strSQL = "select md.service_code, ms.name as service_desc, sum(md.quantity) as qty, (sum(chrg_amnt * md.quantity)/sum(md.quantity)) as price,
									 sum(md.quantity * chrg_amnt) as itemcharge, fn_gettotalclaim($hcare_id, md.service_code, '$enc_nr') as claim, 'OA' as source ".
						"   from ((seg_misc_service as m inner join seg_misc_service_details as md on m.refno = md.refno) ".
						"      inner join seg_other_services as ms on md.service_code = ms.alt_service_code) ".
						"   where (encounter_nr = '$enc_nr' ".$filter.") ".
						"      and md.request_flag is null and str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
						"      and str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
						"   group by md.service_code, ms.name";
	putinXLOitemized_array($strSQL, $xray_array, $lab_array, $supp_array);

	$strSQL = "select mcd.service_code, ms.name as service_desc, sum(mcd.quantity) as qty, (sum(chrg_amnt * mcd.quantity)/sum(mcd.quantity)) as price,
									 sum(mcd.quantity * chrg_amnt) as itemcharge, fn_gettotalclaim($hcare_id, mcd.service_code, '$enc_nr') as claim, 'OC' as source ".
						"   from ((seg_misc_chrg as mc inner join seg_misc_chrg_details as mcd on mc.refno = mcd.refno) ".
						"      inner join seg_other_services as ms on mcd.service_code = ms.service_code)
								where (encounter_nr = '$enc_nr' ".$filter.") ".
						"      and str_to_date(mc.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $from_date . "'".
						"      and str_to_date(mc.chrge_dte, '%Y-%m-%d %H:%i:%s') <= '" . $to_date . "'".
						"   group by mcd.service_code, ms.name";
	putinXLOitemized_array($strSQL, $xray_array, $lab_array, $supp_array);

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
//				"select mphd.bestellnum, artikelname, 'MS' as group_code, 'Supplies' as group_desc, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as serv_charge, 'MS' as source ".
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

function putinXLOitemized_array($strSQL, &$xray_array, &$lab_array, &$supp_array) {
	global $db;

	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			while ($row = $result->FetchRow()) {
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
			 /*
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
				} */

				switch ($row["source"]) {
					case 'LB':
						$lab_array[] = $obj;
						break;
					case 'RD':
						$xray_array[] = $obj;
						break;

					case 'SU':
					case 'MS':

					case 'OE':
					case 'OA':
					case 'OC':
						$supp_array[] = $obj;
				}

			}
		}
	}
}

setToUTF8();

$pdf->encounter_nr = $_GET['encounter_nr'];
$pdf->hcare_id = $_GET['id'];

//added by jasper 08/27/2013 - FIX FOR BUGZILLA 209 ROOM AND BOARD
$pdf->HouseCase = isHouseCase($pdf->encounter_nr);
$pdf->Charity = isCharity($pdf->encounter_nr);
//added by jasper 08/27/2013 - FIX FOR BUGZILLA 209 ROOM AND BOARD

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
fillPFData($pdf->encounter_nr, $pdf->hcare_id, $pdf->anesth_array, $pdf->surgeon_array,$pdf->confinement_array);

// Get the charged Drugs and Medicines ...
$pdf->meds_array = getDrugsandMeds($pdf->encounter_nr, $pdf->hcare_id);
// Get the charged X-Ray, Lab and Others ...
//getXRayLabOthers($pdf->encounter_nr, $pdf->hcare_id, $pdf->lab_array, $pdf->sup_array, $pdf->others_array);
getXRayLabOthers($pdf->encounter_nr, $pdf->hcare_id, $pdf->xray_array, $pdf->lab_array, $pdf->sup_array);

getBillingData($pdf->encounter_nr, &$pdf->pkglimit, &$pdf->issurgical);
?>
