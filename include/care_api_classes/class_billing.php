<?php
/**
* @package SegHIS_api
*/

/******
*
*	Class containing all properties and methods related to an encounter's billing.
*
*   Note this class should be instantiated only after a "$db" adodb  connector object
*   has been established by an adodb instance.
*
*   @author 	 :	Lemuel 'Bong' S. Trazo
*	@version	 :	1.0
*	@date created:	July 27, 2007
*	@date updated:	March 23, 2009
*
*****/

require_once('roots.php');
require_once($root_path.'include/care_api_classes/billing/class_coverage.php');
require_once($root_path.'include/care_api_classes/billing/class_accommodation.php');
require_once($root_path.'include/care_api_classes/billing/class_medicine.php');
require_once($root_path.'include/care_api_classes/billing/class_supply.php');
require_once($root_path.'include/care_api_classes/billing/class_services.php');
require_once($root_path.'include/care_api_classes/billing/class_bill_ops.php');
require_once($root_path.'include/care_api_classes/billing/class_prof_fees.php');
require_once($root_path.'include/care_api_classes/billing/class_payment.php');
require_once($root_path.'include/care_api_classes/billing/class_actual_coverage.php');
require_once($root_path.'include/care_api_classes/billing/class_pf_claim.php');
require_once($root_path.'include/care_api_classes/billing/class_msc_chrg.php');
require_once($root_path.'include/care_api_classes/billing/class_billing_discount.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_core.php');

define(ER_PATIENT, 1);
define(DEFAULT_PCF, 40);
define(CHARITY, 'CHARITY');

class Billing extends Core {
	var $current_enr;
		var $prev_encounter_no;                         // Previous encounter no. -- used for admitted ER patients.
	var $confinetype_id;
	var $confinetype_desc;

	var $accomm_typ_nr;
	var $accomm_typ_desc;

	var $bill_frmdte = "0000-00-00 00:00:00";		// Start date and time covered by this billing.
	var $bill_dte;

	var $bPrincipal;
	var $total_RVU;

	var $total_prevpayment;

	var $errmsg;

	// Accommodation ...
	var $days_count;
	var $excess_hours;

	var $cutoff_hrs = 0;
		var $pcf;
		var $bfinal = false;

	var $accommodation_hist;
	var $medicines_list;
	var $supplies_list;
	var $services_list;
	var $ops_list;
	var $proffees_list;
	var $msc_chrgs_list;

	var $skedvalues;

	#added by VAN 02-13-08
	var $RADservices_list;
	var $LABservices_list;

	// Benefits based on specifics ...
	var $acc_roomtype_benefits;
	var $med_product_benefits;
	var $sup_product_benefits;
	var $hsp_service_benefits;
	var $hsp_ops_benefits;
	var $hsp_pfs_benefits;
	var $hsp_msc_benefits;

	// Benefits based on confinement ...
	var $acc_confine_benefits;		            // ... accommodation
	var $med_confine_benefits;		            // ... medicines
	var $sup_confine_benefits;		            // ... supplies
	var $srv_confine_benefits;		            // ... hospital services.
	var $ops_confine_benefits;				    // ... operation procedure.
	var $pfs_confine_benefits = array();		// ... doctors' fees.
	var $msc_confine_benefits;

	var $acc_confine_coverage;					// Maximum coverage for accommodation.
	var $med_confine_coverage;					// Maximum coverage for medicines.
	var $sup_confine_coverage;					// Maximum coverage for supplies.
	var $srv_confine_coverage;					// Maximum coverage for hospital services.
	var $ops_confine_coverage;					// Maximum coverage for operation procedures.
	var $pfs_confine_coverage = array();		// Maximum coverage for doctors' fees.
	var $msc_confine_coverage;

	var $discounts;								// array of applied discounts.

	var $prev_payments;							// array of partial payments (deposits).

	var $old_bill_nr = '';

	var $hcare_coverage;						// array of health insurances availed by patient with corresponding total coverage per area.
	var $pf_claims;							    // array of prof fees and corresponding claims.
	var $pf_claims_per_hcare;					// array of prof fees and corresponding claims per health insurance.

		var $valid_covered_items;                   // array of valid items with coverage applied.

	function Billing($enr = '', $billdte = "0000-00-00 00:00:00", $frmdte = "0000-00-00 00:00:00", $old_billnr = '') {
		$this->current_enr = $enr;
		$this->old_bill_nr = $old_billnr;

				$this->getPrevEncounterNr();    // Get parent encounter no., if there is ...

		if (strcmp($frmdte, "0000-00-00 00:00:00") == 0) {
			$this->bill_frmdte = $this->getLatestBillDte();

			if (strcmp($this->bill_frmdte, "0000-00-00 00:00:00") == 0)
				$this->bill_frmdte = $this->getEncounterDte();

			if (strcmp($this->bill_frmdte, "0000-00-00 00:00:00") == 0)
				$this->bill_frmdte = $this->getActualAdmissionDte();
		}
		else
			$this->bill_frmdte = $frmdte;

		if (strcmp($billdte, "0000-00-00 00:00:00") != 0)
			$this->bill_dte = $billdte;
		else
			$this->bill_dte = strftime("%Y-%m-%d %H:%M:%S");		// Default to current date and time.

		if ($old_billnr != '')
			$ncutoff = $this->getAppliedHrsCutoff();
		else
			$ncutoff = -1;

		if ($ncutoff == -1) {
			$hosp_obj = new Hospital_Admin();
			$this->cutoff_hrs = $hosp_obj->getCutoff_Hrs();
						$this->pcf        = $hosp_obj->getDefinedPCF();
		}
		else {
			$this->cutoff_hrs = $ncutoff;
						$this->pcf        = DEFAULT_PCF;
				}

//        $this->applyPHIPInsurance();    // Apply automatically the PHIP insurance if member ....
	}

		function correctBillDates() {
				global $db;

				if ($this->old_bill_nr != '') {
						$strSQL = "select bill_dte, bill_frmdte from seg_billing_encounter where bill_nr = '$this->old_bill_nr'";
						if ($result=$db->Execute($strSQL)){
								if ($result->RecordCount()) {
										if ($row = $result->FetchRow()) {
												$this->bill_frmdte = is_null($row["bill_frmdte"]) ? $this->bill_frmdte : $row["bill_frmdte"];
												$this->bill_dte    = is_null($row["bill_dte"]) ? $this->bill_dte : $row["bill_dte"];
										}
								}
						}
				}
		}

		function isForFinalBilling() {
				global $db;

				$bForFinalBilling = false;

				$strSQL = "select is_maygohome, mgh_setdte ".
									"   from care_encounter ".
									"   where encounter_nr = '$this->current_enr'";
				if ($result=$db->Execute($strSQL)){
						if ($result->RecordCount()) {
								if ($row = $result->FetchRow()) {
										if ($row["is_maygohome"]) {
												$bForFinalBilling = (strtotime($row["mgh_setdte"]) <= strtotime($this->bill_dte));
										}
								}
						}
				}

				if (is_null($bForFinalBilling) || ($bForFinalBilling == '')) $bForFinalBilling = false;

				$this->bfinal = $bForFinalBilling;
				return $bForFinalBilling;
		}

		function isERPatient() {
				global $db;

				$enc_type = 0;
				$strSQL = "select encounter_type ".
									"   from care_encounter ".
									"   where encounter_nr = '".$this->current_enr."'";
				if ($result = $db->Execute($strSQL)) {
						if ($result->RecordCount()) {
								$row = $result->FetchRow();
								$enc_type = $row['encounter_type'];
						}
				}

				return ($enc_type == ER_PATIENT);
		}

		// This function is applicable to BPH Requirement only ...
		function applyPHIPInsurance() {
				global $db;

				$bSuccess = true;
				$strSQL = "select cpi.*, sei.hcare_id as id from (care_person_insurance as cpi inner join care_encounter as ce
										on cpi.pid = ce.pid and encounter_nr = '".$this->current_enr."') left join seg_encounter_insurance as sei
										on cpi.hcare_id = sei.hcare_id and sei.encounter_nr = ce.encounter_nr
										where exists (select * from care_insurance_firm as cif
										where cif.default_classification = 'D' and cif.hcare_id = cpi.hcare_id)";
				if ($result = $db->Execute($strSQL)) {
						if ($result->RecordCount()) {
								$row      = $result->FetchRow();
								$nhcare_id = $row['id'];
								$src_id    = $row['hcare_id'];

								if (is_null($nhcare_id) and (!is_null($src_id))) {
										$strSQL = "insert into seg_encounter_insurance (encounter_nr, hcare_id, modify_id, create_id, create_dt)
																	 values('".$this->current_enr."', ".$src_id.", '".$_SESSION['sess_user_name']."', '".$_SESSION['sess_user_name']."',now())";
										if (!$db->Execute($strSQL)) $bSuccess = false;
								}
						}
				}

				return $bSuccess;
		}

	function getAccHist() {
		return($this->accommodation_hist);
	}

	function getRmTypeBenefits() {
		return($this->acc_roomtype_benefits);
	}

	function getAccConfineCoverage() {
		return($this->acc_confine_coverage);
	}

	function getSrvList() {
		return($this->services_list);
	}

	function getSrvBenefits() {
		return($this->hsp_service_benefits);
	}

	function getMedConfineBenefits() {
		return($this->med_product_benefits);
	}

	function getMedConfineCoverage() {
		return($this->med_confine_coverage);
	}

	function getSupConfineCoverage() {
		return($this->sup_confine_coverage);
	}

	function getSupConfineBenefits() {
		return($this->sup_product_benefits);
	}

	function getOpsConfineBenefits() {
		return($this->hsp_ops_benefits);
	}

	function getSrvConfineCoverage() {
		return($this->srv_confine_coverage);
	}

	function getOpsConfineCoverage() {
		return($this->ops_confine_coverage);
	}

	function getMscConfineCoverage() {
		return($this->msc_confine_coverage);
	}

	function getMiscBenefits() {
		return($this->hsp_msc_benefits);
	}

	function getPFBenefits() {
		return($this->hsp_pfs_benefits);
	}

	function getCurrentEncounterNr() {
		return($this->current_enr);
	}

	function setCurrentEncounterNr($enr) {
		$this->current_enr = $enr;
	}

		function getPrevEncounterNr() {
				global $db;

				$strSQL = "select parent_encounter_nr
											from care_encounter
											where encounter_nr = '".$this->current_enr."'";
				if ($result = $db->Execute($strSQL)) {
						if ($result->RecordCount()) {
								$row = $result->FetchRow();
								$this->prev_encounter_nr = $row['parent_encounter_nr'];
						}
				}

				return($this->prev_encounter_nr);
		}

		function setPrevEncounterNr($enr) {
				$this->prev_encounter_nr = $enr;
		}

		function getBillDate() {
				return($this->bill_dte);
		}

	function setBillDate($nbill_dte) {
		$this->bill_dte = $nbill_dte;
	}

	function getAppliedHrsCutoff() {
		global $db;

		$n_cutoff = -1;

		$strSQL = "select applied_hrs_cutoff ".
					"   from seg_billing_encounter ".
					"   where bill_nr = '".$this->old_bill_nr."'";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$row = $result->FetchRow();
				$n_cutoff = $row['applied_hrs_cutoff'];
			}
		}

		return($n_cutoff);
	}

	function getClassificationDesc() {
		global $db;

		$s_desc= "";
				$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or scg.encounter_nr = '$this->prev_encounter_nr'";
//		$strSQL = "select discountdesc ".
//				  "   from (seg_discount as sd inner join seg_charity_grants_pid as scg on sd.discountid = scg.discountid) ".
//                  "      inner join care_encounter as ce on scg.pid = ce.pid ".
//				  "   where (ce.encounter_nr = '". $this->current_enr. "'".$filter.") ".
//				  "      and str_to_date(grant_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "' " .
//				  "   order by grant_dte desc limit 1";

				// Classification is taken from classification based on encounter ...
				$strSQL = "select discountdesc ".
									"   from seg_discount as sd inner join seg_charity_grants as scg on sd.discountid = scg.discountid ".
									"   where (scg.encounter_nr = '". $this->current_enr. "'".$filter.") ".
									"      and str_to_date(grant_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "' " .
									"   order by grant_dte desc limit 1";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$s_desc = $row['discountdesc'];
				}
			}
		}

		return($s_desc);
	}

	function getAddedAccommodationType() {
		global $db;

		$ntype = 0;
		$sname = '';
				$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or sela.encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select cw.accomodation_type, accomodation_name ".
						"   from (seg_encounter_location_addtl as sela inner join care_ward as cw on sela.group_nr = cw.nr) ".
							"      inner join seg_accomodation_type as sat on cw.accomodation_type = sat.accomodation_nr ".
						"   where (sela.encounter_nr = '". $this->current_enr. "'".$filter.") ".
							"      and (str_to_date(sela.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
							"      and str_to_date(sela.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
					"   order by entry_no desc limit 1";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$ntype = $row['accomodation_type'];
					$sname = $row['accomodation_name'];
				}
			}
		}

		$this->accomm_typ_nr = $ntype;
		$this->accomm_typ_desc = $sname;

		return($db->ErrorMsg() == '');
	}

	function getAccommodationType() {
		global $db;

		$ntype = 0;
		$sname = '';
				$filter = '';

		if ($this->prev_encounter_nr != '') $filter = " or cel.encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select cw.accomodation_type, accomodation_name ".
						"   from ((care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr) ".
								"      inner join seg_accomodation_type as sat on cw.accomodation_type = sat.accomodation_nr) ".
							"      left join seg_encounter_location_rate as selr on cel.nr = selr.loc_enc_nr and cel.encounter_nr = selr.encounter_nr ".
						"   where (cel.encounter_nr = '". $this->current_enr. "'".$filter.") ".
							"      and exists (select nr ".
									"                     from care_type_location as ctl ".
									"                     where upper(type) = 'WARD' and ctl.nr = cel.type_nr) ".
									"      and ((str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
									"      and str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
						"         or ".
									"      (str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
									"      and str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
									"      or ".
									"      str_to_date(concat(date_format(ifnull(date_to, '0000-00-00'), '%Y-%m-%d'), ' ', date_format(ifnull(time_to, '00:00:00'), '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') = '0000-00-00 00:00:00') ".
									"order by date_from desc, time_from desc limit 1";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$ntype = $row['accomodation_type'];
					$sname = $row['accomodation_name'];
				}
			}
		}

		if ($ntype == 0)
			return($this->getAddedAccommodationType());
		else {
			$this->accomm_typ_nr = $ntype;
			$this->accomm_typ_desc = $sname;

			return($db->ErrorMsg() == '');
		}
	}

	function getAccommodationCode() {
		return($this->accomm_typ_nr);
	}

	function getAccommodationDesc() {
		return($this->accomm_typ_desc);
	}

		function isCharity() {
				if ($this->accomm_typ_desc == '') {
						$this->getAccommodationType();
				}
				return (!(strpos(strtoupper($this->accomm_typ_desc), CHARITY, 0) === false));
		}

	function getMemCategoryDesc() {
		global $db;

		$s_desc= "";
				$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or sem.encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select memcategory_desc ".
					"   from seg_memcategory as sm inner join seg_encounter_memcategory as sem ".
					"      on sm.memcategory_id = sem.memcategory_id ".
					"   where (sem.encounter_nr = '". $this->current_enr. "'".$filter.")";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$s_desc = $row['memcategory_desc'];
				}
			}
		}

		return($s_desc);
	}

	function getConfinementType() {
		global $db;

		$n_id = 0;
				$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";

		$strSQL = "select confinetype_id from seg_encounter_confinement ".
					"   where (encounter_nr = '". $this->current_enr. "'".$filter.") ".
						"      and str_to_date(classify_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "' " .
					"   order by classify_dte desc limit 1";

				if ($result = $db->Execute($strSQL)) {
						if ($result->RecordCount()) {
								while ($row = $result->FetchRow()) {
										$n_id = $row['confinetype_id'];
								}
						}
				}

				if ($n_id == 0) {
						$strSQL = "select confinetype_id from seg_type_confinement_icds as stci
													where exists(select * from care_encounter_diagnosis as ced0
																					where substring(code, 1, if(instr(code, '.') = 0, length(code), instr(code, '.')-1)) =
																								substring(stci.diagnosis_code, 1, if(instr(stci.diagnosis_code, '.') = 0, length(stci.diagnosis_code), instr(stci.diagnosis_code, '.')-1))
														and ((exists(select * from care_encounter_diagnosis as ced where instr(stci.paired_codes, ced.code) > 0 and ced.code <> ced0.code and status <> 'deleted') and stci.paired_codes <> '') or stci.paired_codes = '')
																						 and (encounter_nr = '". $this->current_enr. "'".$filter.") and str_to_date(create_time, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "'
																						 and status <> 'deleted')
													order by confinetype_id desc limit 1";

				if ($result = $db->Execute($strSQL)) {
					if ($result->RecordCount()) {
						while ($row = $result->FetchRow()) {
							$n_id = $row['confinetype_id'];
						}
					}
				}

						if ($n_id == 0) {
								$strSQL = "select confinetype_id from seg_type_confinement
															where is_default = 1";
								if ($result = $db->Execute($strSQL)) {
										if ($result->RecordCount()) {
												while ($row = $result->FetchRow()) {
														$n_id = $row['confinetype_id'];
												}
										}
								}
						}
				}

		$this->confinetype_id = $n_id;
		return($n_id);
	}

	function getCaseTypeDesc() {
		global $db;

		$sdesc = '';
				$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select casetype_desc ".
						"   from seg_encounter_case as sec inner join seg_type_case as stc ".
							"      on sec.casetype_id = stc.casetype_id ".
						"   where (encounter_nr = '". $this->current_enr. "'".$filter.") ".
							"      and str_to_date(sec.modify_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "' ".
						"   order by sec.modify_dt desc limit 1";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$sdesc = $row['casetype_desc'];
				}
			}
		}

		return($sdesc);
	}

	function getEncounterDte() {
		global $db;

		$enc_dte = "0000-00-00 00:00:00";
				$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select encounter_date " .
					"   from care_encounter " .
					"   where (encounter_nr = '". $this->current_enr ."'".$filter.")
											order by encounter_date limit 1";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow())
					$enc_dte = $row['encounter_date'];
			}
		}

		return($enc_dte);
	}

	function getActualAdmissionDte() {
		global $db;

		$admit_dte = "0000-00-00 00:00:00";
				$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select admission_dt " .
					"   from care_encounter " .
									"   where (encounter_nr = '". $this->current_enr ."'".$filter.")
												 and admission_dt is not null
											order by encounter_date limit 1";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow())
					$admit_dte = $row['admission_dt'];
			}
		}

		return($admit_dte);
	}

	function getLatestBillDte() {
		global $db;

		$lastbill_dte = "0000-00-00 00:00:00";
				$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select bill_dte " .
					"   from seg_billing_encounter " .
					"   where (encounter_nr = '". $this->current_enr ."'".$filter.") " .
					"   order by bill_dte desc limit 1";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow())
					$lastbill_dte = $row['bill_dte'];
			}
		}

		// Adjust latest bill date forward by 1 second ...
		if (strcmp($lastbill_dte, "0000-00-00 00:00:00") != 0) {
			$lastbill_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("+1 second", strtotime($lastbill_dte)));
		}

		return($lastbill_dte);
	}

	function getActualLastBillDte() {
		global $db;

		$lastbill_dte = "0000-00-00 00:00:00";
		$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select bill_dte " .
					"   from seg_billing_encounter " .
					"   where (encounter_nr = '". $this->current_enr ."'".$filter.") " .
					"      and str_to_date(bill_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "' ".
					"   order by bill_dte desc limit 1";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$row = $result->FetchRow();
				$lastbill_dte = $row['bill_dte'];
			}
		}

		return($lastbill_dte);
	}

		function getValidItemWithAppliedCoverage($refno, $source, $item_code, $tcharge) {
				global $db;

				$tcoverage = 0;

				$source = ($source == 'MS') ? 'S' : substr($source, 0, 1);
				$strSQL = "select sum(coverage) as tcoverage
									\n  from seg_applied_coverage
									\n  where ref_no = concat('T','$refno') and source = '$source'
									\n     and item_code = '$item_code'";

				if ($result = $db->Execute($strSQL)) {
						if ($result->RecordCount()) {
								$row = $result->FetchRow();
								$tcoverage = $row['tcoverage'];
						}
				}

				if ($tcoverage <= $tcharge) {
//            $strSQL = "delete from seg_applied_coverage
//                      \n  where ref_no = concat('T','$refno') and source = '$source'
//                      \n     and item_code = '$item_code'";
//            $db->Execute($strSQL);
						$this->valid_covered_items[] = $item_code;
				}
		}

		function clearInvalidItemsFromCoverage($bMeds = false) {
				global $db;

				$valid_items = "'".implode(",",$this->valid_covered_items)."'";
				$strSQL = "delete from seg_applied_coverage\n
											where ref_no = concat('T','".$this->current_enr."') \n
												 and source ".($bMeds ? "= 'M'" : "<> 'M'")." \n
												 and find_in_set(item_code, $valid_items) = 0 ";
				$bSuccess = $db->Execute($strSQL);
				return $bSuccess;
		}

	function getAccommodationHist() {
			global $db;

//		$strSQL = "select encounter_nr, location_nr, group_nr, cw.name, cw.ward_rate, date_from, date_to, time_from, time_to ".
//				  "   from care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr ".

//		$strSQL = "select encounter_nr, location_nr, group_nr, concat(ctr.name,' (',cw.name,')') as name, ctr.room_rate, date_from, date_to, time_from, time_to ".
//   				  "   from (care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr) ".
//      			  "      inner join (care_room as cr inner join care_type_room as ctr on cr.type_nr = ctr.nr) ".
				$filter = array('','');

				if ($this->prev_encounter_nr != '') $filter[0] = " or cel.encounter_nr = '$this->prev_encounter_nr'";
				if ($this->prev_encounter_nr != '') $filter[1] = " or sel.encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select cel.encounter_nr, location_nr, cr.type_nr, concat(ctr.name,' (',cw.name,')') as name, ".
					"      (case when not isnull(selr.rate) then selr.rate else ctr.room_rate end) as rm_rate, 0 as days_stay, 0 as hrs_stay, ".
					"      date_from, date_to, time_from, time_to, 'AD' as source ".
						"   from ((care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr) ".
							"      left join seg_encounter_location_rate as selr on cel.nr = selr.loc_enc_nr and cel.encounter_nr = selr.encounter_nr) ".
							"      inner join (care_room as cr inner join care_type_room as ctr on cr.type_nr = ctr.nr) ".
							"      on cel.location_nr = cr.room_nr ".
					"   where (cel.encounter_nr = '". $this->current_enr. "'".$filter[0].") ".
					"      and exists (select nr ".
					"                     from care_type_location as ctl ".
					"                        where upper(type) = 'ROOM' and ctl.nr = cel.type_nr) ".
									"      and ((str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"         and str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
					"			 or ".
						"       (str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
									"         and str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
						"          or ".
						"        str_to_date(concat(date_format(ifnull(date_to, '0000-00-00'), '%Y-%m-%d'), ' ', date_format(ifnull(time_to, '00:00:00'), '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') = '0000-00-00 00:00:00') ".
					" union ".
					"select sel.encounter_nr, cr.room_nr, cr.type_nr, concat(ctr.name,' (',cw.name,')') as name, ".
							"      (case when not isnull(sel.rate) then sel.rate else ctr.room_rate end) as rm_rate, days_stay, hrs_stay, ".
					"      date(sel.create_dt) as date_from, '0000-00-00' as date_to, time(sel.create_dt) as time_from, '00:00:00' as time_to, 'BL' as source ".
						"   from (seg_encounter_location_addtl as sel inner join care_ward as cw on sel.group_nr = cw.nr) ".
							"      inner join (care_room as cr inner join care_type_room as ctr on cr.type_nr = ctr.nr) on sel.room_nr = cr.nr ".
						"   where (sel.encounter_nr = '". $this->current_enr. "'".$filter[1].") ".
							"      and (str_to_date(sel.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
							"      and str_to_date(sel.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
					"   order by source, date_from, time_from";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$this->accommodation_hist = array();
				$j = 0;

				while ($row = $result->FetchRow()) {
					$objAcc = new Accommodation;

					if ($row['source'] == 'AD') {
						if ($j == 0) {
							$tmpadmit_dte = $this->getActualAdmissionDte();
							$tmpref_dte = $this->bill_frmdte;

							if (strtotime($tmpadmit_dte) < strtotime($tmpref_dte))
								$tmpadmit_dte = $tmpref_dte;

							$tmpdate_from = strftime("%Y-%m-%d", strtotime($tmpadmit_dte));
							$tmptime_from = strftime("%H:%M:%S", strtotime($tmpadmit_dte));

							$j++;
						}
						else {
							$tmpdate_from = $row['date_from'];
							$tmptime_from = $row['time_from'];
						}
						$objAcc->setAdmissionDteTime($tmpdate_from, $tmptime_from);

						// If discharge date is still 0000-00-00, then patient is not yet discharged ...
						$tmpdate_to = $row['date_to'];
						if (strcmp($tmpdate_to, "0000-00-00") == 0) {
							$tmpdate_to = strftime("%Y-%m-%d", strtotime($this->bill_dte));
							$tmptime_to = strftime("%H:%M:%S", strtotime($this->bill_dte));
						}
						else {
							$tmptime_to = $row['time_to'];
							$tmpref_dte = strftime("%Y-%m-%d", strtotime($tmpdate_to)). ' '.strftime("%H:%M:%S",  strtotime($tmptime_to));

							if (strtotime($tmpref_dte) > strtotime($this->bill_dte)) {
								$tmpdate_to = strftime("%Y-%m-%d", strtotime($this->bill_dte));
								$tmptime_to = strftime("%H:%M:%S", strtotime($this->bill_dte));
							}
						}
						$objAcc->setDischargeDteTime($tmpdate_to, $tmptime_to);

						$objAcc->setActualDays(0);
						$objAcc->setExcessHrs(0);
					}
					else {
						if ($row['hrs_stay'] > $this->cutoff_hrs)
							$objAcc->setActualDays($row['days_stay'] + 1);		// Excess hours is rounded to 1 day.
						else
							$objAcc->setActualDays($row['days_stay']);
						$objAcc->setExcessHrs(0);
					}

					$objAcc->setRoomNr($row['location_nr']);
					$objAcc->setTypeNr($row['type_nr']);
					$objAcc->setTypeDesc($row['name']);
					$objAcc->setRoomRate($row['rm_rate']);
					$objAcc->setSource($row['source']);

					// Add new accommodation object in collection (array) of accommodations for this billing.
					$this->accommodation_hist[] = $objAcc;
				}
			}
		}
	}

	function getSuppliesList() {
		global $db;

		// Get all the supplies charged to current encounter ...
//		$strSQL = "select ph.refno, ph.orderdate, ph.department, pd.bestellnum, artikelname, quantity, pricecharge ".
//				  "    from (seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
//				  "          inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum ".
//				  "    where encounter_nr = '". $this->current_enr. "' and is_cash = 0 and p.prod_class = 'S' ".
//                  "        and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
//				  "           and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
//				  "    order by ph.department, ph.orderdate, artikelname";
//
//		if ($result = $db->Execute($strSQL)) {
			$this->supplies_list = array();

//			if ($result->RecordCount()) {
//				while ($row = $result->FetchRow()) {
//					$objSup = new Supply;
//
//					$objSup->setRefNo($row['refno']);
//					$objSup->setTransDte($row['orderdate']);
//					$objSup->setDept($row['department']);
//					$objSup->setBestellNum($row['bestellnum']);
//					$objSup->setArtikelName($row['artikelname']);
//					$objSup->setItemQty($row['quantity']);
//					$objSup->setItemPrice($row['pricecharge']);

					// Add new supply object in collection (array) of the list of supplies in this billing.
//					$this->supplies_list[] = $objSup;
//				}
//			}
//		}
	}

	function getMiscellaneousChrgsList() {
		global $db;
				$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select mc.refno, mc.chrge_dte, mcd.service_code, sos.name, sos.description, sum(mcd.quantity) as qty, (sum(quantity * chrg_amnt)/sum(mcd.quantity)) as avg_chrg, ".
							"      sum(quantity * chrg_amnt) as total_chrg ".
						"   from (seg_misc_chrg as mc inner join seg_misc_chrg_details as mcd on ".
							"      mc.refno = mcd.refno) inner join seg_other_services as sos on ".
							"      mcd.service_code = sos.service_code ".
						"   where (encounter_nr = '" . $this->current_enr. "'".$filter.") ".
							"      and (str_to_date(mc.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
							"      and str_to_date(mc.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
						"   group by mc.refno, mc.chrge_dte, mcd.service_code, sos.name ".
					"   order by mc.chrge_dte, sos.name";

		if ($result = $db->Execute($strSQL)) {
			$this->msc_chrgs_list = array();

			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$objMsc = new Miscellaneous;

					$objMsc->setRefNo($row['refno']);
					$objMsc->setChrgeDteTme($row['chrge_dte']);
					$objMsc->setMiscCode($row['service_code']);
					$objMsc->setMiscName($row['name']);
					$objMsc->setMiscDesc($row['description']);
					$objMsc->setMiscQty($row['qty']);
					$objMsc->setMiscChrg($row['avg_chrg']);

					// Add new Service object in collection (array) of miscellaneous charges in this billing.
					$this->msc_chrgs_list[] = $objMsc;
				}
			}
		}
	}

	function getServicesList() {
		global $db;

		$filter = array('','');
// "         and exists (select * from seg_lab_results as slr where slr.refno = lh.refno limit 1) " .

		// Get all the services charged to current encounter ...
				if ($this->prev_encounter_nr != '') $filter[0] = " or encounter_nr = '$this->prev_encounter_nr'";
				if ($this->prev_encounter_nr != '') $filter[1] = " or sos.encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select lh.refno, serv_dt, serv_tm, ld.service_code, ls.name as service_desc, ls.group_code, " .
						"   lsg.name as group_desc, /*count(ld.service_code)*/ ld.quantity as qty, ld.price_charge as serv_charge, 'LB' as source " .
					"   from ((seg_lab_serv as lh inner join seg_lab_servdetails as ld on lh.refno = ld.refno) " .
					"          inner join seg_lab_services as ls on ld.service_code = ls.service_code) " .
						"          inner join seg_lab_service_groups as lsg on ls.group_code = lsg.group_code " .
									"      where lh.is_cash = 0 and (encounter_nr = '" . $this->current_enr. "'".$filter[0].") and upper(trim(lh.status)) <> 'DELETED' " .
									"         and (str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"            and str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
									"   group by lh.refno, serv_dt, serv_tm, ld.service_code, ls.name, ls.group_code, lsg.name ".
					" union ".
					"select rh.refno, rh.request_date as serv_dt, rh.request_time as serv_tm, rd.service_code, rs.name as service_desc, rs.group_code, " .
					"   rsg.name as group_desc, count(rd.service_code) as qty, (sum(rd.price_charge)/count(rd.service_code)) as serv_charge, 'RD' as source " .
						"   from ((seg_radio_serv as rh inner join care_test_request_radio as rd on rh.refno = rd.refno) " .
							"          inner join seg_radio_services as rs on rd.service_code = rs.service_code) " .
									"          inner join seg_radio_service_groups as rsg on rs.group_code = rsg.group_code " .
						"      where rh.is_cash = 0 and (encounter_nr = '" . $this->current_enr. "'".$filter[0].") and upper(trim(rh.status)) <> 'DELETED' " .
									"         and (str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"            and str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
						"   group by rh.refno, rh.request_date, rh.request_time, rd.service_code, rs.name, rs.group_code, rsg.name ".
					" union ".
									"select ph.refno, date(ph.orderdate) as serv_dt, time(ph.orderdate) as serv_tm, pd.bestellnum, artikelname, 'SU' as group_code, ".
									"      'Supplies' as group_desc, pd.quantity - ifnull(spri.quantity, 0) as quantity, pricecharge, 'SU' as source ".
									"   from ((seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
									"      left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum) ".
									"      inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum ".
									"   where (encounter_nr = '". $this->current_enr. "'".$filter[0].") and is_cash = 0 and p.prod_class = 'S' ".
									"      and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
									"      and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
									"      and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
									" union ".
									"select mph.refno, date(mph.chrge_dte) as serv_dt, time(mph.chrge_dte) as serv_tm, mphd.bestellnum, artikelname, 'MS' as group_code, ".
									"      'Supplies' as group_desc, quantity, unit_price, 'MS' as source ".
									"   from (seg_more_phorder_details as mphd inner join seg_more_phorder as mph on mphd.refno = mph.refno) ".
									"      inner join care_pharma_products_main as p on mphd.bestellnum = p.bestellnum ".
									"   where (encounter_nr = '". $this->current_enr. "'".$filter[0].") and p.prod_class = 'S' ".
									"      and (str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
									"         and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
									"   group by mph.refno, mph.chrge_dte, mphd.bestellnum, artikelname ".
									" union ".
									"select sos.refno, date(eqh.order_date) as serv_dt, time(eqh.order_date) as serv_tm, eqd.equipment_id, artikelname, '' as group_code,
												 'Equipment' as group_desc, sum(number_of_usage) as qty, (sum(discounted_price * number_of_usage)/sum(number_of_usage)) as uprice, 'OE' as source
											 from ((seg_equipment_orders as eqh inner join seg_equipment_order_items as eqd on eqh.refno = eqd.refno)
												 inner join seg_ops_serv as sos on sos.refno = eqh.request_refno) inner join care_pharma_products_main as
												 cppm on cppm.bestellnum = eqd.equipment_id
											 where (sos.encounter_nr = '". $this->current_enr. "'".$filter[1].")
													and (str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "'
													and str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "')
											 group by sos.refno, eqh.order_date, eqd.equipment_id, artikelname ".
									" union ".
					"select m.refno, date(m.chrge_dte) as serv_dt, time(m.chrge_dte) as serv_tm, md.service_code, ms.name as service_desc, '' as group_code, ".
						"      'Others' as group_desc, sum(md.quantity) as qty, (sum(chrg_amnt * md.quantity)/sum(md.quantity)) as serv_charge, 'OA' as source ".
						"   from (seg_misc_service as m inner join seg_misc_service_details as md on m.refno = md.refno) ".
							"      inner join seg_other_services as ms on md.service_code = ms.alt_service_code ".
					"   where (encounter_nr = '" . $this->current_enr. "'".$filter[0].") ".
							"      and (str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
							"      and str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
						"   group by m.refno, m.chrge_dte, md.service_code, ms.name";
		if ($result = $db->Execute($strSQL)) {
			$this->services_list = array();

			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$objServ = new Service;

					$objServ->setRefNo($row['refno']);
					$objServ->setTransDteTime($row['serv_dt']  , $row['serv_tm']);
					$objServ->setServiceCode($row['service_code']);
					$objServ->setServiceDesc($row['service_desc']);
					$objServ->setGroupCode($row['group_code']);
					$objServ->setGroupDesc($row['group_desc']);
					$objServ->setServQty($row['qty']);
					$objServ->setServPrice($row['serv_charge']);
					$objServ->setServProvider($row['source']);

					// Add new Service object in collection (array) of services charged in this billing.
					$this->services_list[] = $objServ;
				}
			}
		}
	}

	#added by VAN 02-14-08
	function getRADServicesList() {
		global $db;

				$filter = '';

		// Get all the services charged to current encounter ...
				if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select rh.refno, rh.request_date as serv_dt, rh.request_time as serv_tm, rd.service_code, rs.name as service_desc, rs.group_code, " .
					"   rsg.name as group_desc, count(rd.service_code) as qty, (sum(rd.price_charge)/count(rd.service_code)) as serv_charge, 'RD' as source " .
						"   from ((seg_radio_serv as rh inner join care_test_request_radio as rd on rh.refno = rd.refno) " .
							"          inner join seg_radio_services as rs on rd.service_code = rs.service_code) " .
									"          inner join seg_radio_service_groups as rsg on rs.group_code = rsg.group_code " .
						"      where rh.is_cash = 0 and (encounter_nr = '" . $this->current_enr. "'".$filter.") and upper(trim(rh.status)) <> 'DELETED' " .
									"         and (str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"            and str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
						"   group by rh.refno, rh.request_date, rh.request_time, rd.service_code, rs.name, rs.group_code, rsg.name";
		#echo "<br>sql = ".$strSQL;
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$this->RADservices_list = array();

				while ($row = $result->FetchRow()) {
					$objServ = new Service;

					$objServ->setRefNo($row['refno']);
					$objServ->setTransDteTime($row['serv_dt']  , $row['serv_tm']);
					$objServ->setServiceCode($row['service_code']);
					$objServ->setServiceDesc($row['service_desc']);
					$objServ->setGroupCode($row['group_code']);
					$objServ->setGroupDesc($row['group_desc']);
					$objServ->setServQty($row['qty']);
					$objServ->setServPrice($row['serv_charge']);
					$objServ->setServProvider($row['source']);

					// Add new Service object in collection (array) of services charged in this billing.
					$this->RADservices_list[] = $objServ;
				}
			}
		}
	}

	function getLABServicesList() {
		global $db;

				$filter = '';

		// Get all the services charged to current encounter ...
				if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select lh.refno, serv_dt, serv_tm, ld.service_code, ls.name as service_desc, ls.group_code, " .
						"   lsg.name as group_desc, /*count(ld.service_code)*/ quantity as qty, ld.price_charge as serv_charge, 'LB' as source " .
					"   from ((seg_lab_serv as lh inner join seg_lab_servdetails as ld on lh.refno = ld.refno) " .
					"          inner join seg_lab_services as ls on ld.service_code = ls.service_code) " .
						"          inner join seg_lab_service_groups as lsg on ls.group_code = lsg.group_code " .
									"      where lh.is_cash = 0 and (encounter_nr = '" . $this->current_enr. "'".$filter.") and upper(trim(lh.status)) <> 'DELETED' " .
									"         and (str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"            and str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
									"   group by lh.refno, serv_dt, serv_tm, ld.service_code, ls.name, ls.group_code, lsg.name ";
		#echo "<br>sql = ".$strSQL;
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$this->LABservices_list = array();

				while ($row = $result->FetchRow()) {
					$objServ = new Service;

					$objServ->setRefNo($row['refno']);
					$objServ->setTransDteTime($row['serv_dt']  , $row['serv_tm']);
					$objServ->setServiceCode($row['service_code']);
					$objServ->setServiceDesc($row['service_desc']);
					$objServ->setGroupCode($row['group_code']);
					$objServ->setGroupDesc($row['group_desc']);
					$objServ->setServQty($row['qty']);
					$objServ->setServPrice($row['serv_charge']);
					$objServ->setServProvider($row['source']);

					// Add new Service object in collection (array) of services charged in this billing.
					$this->LABservices_list[] = $objServ;
				}
			}
		}
	}

#------------------------------------------------------

	function getMedicinesList() {
		global $db;

				$filter = '';

		// Get all the medicines charged to current encounter ...
				if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
				$strSQL = "select ph.refno, ph.orderdate, ph.department, pd.bestellnum, artikelname, pd.quantity - ifnull(spri.quantity, 0) as quantity, pricecharge ".
									"   from ((seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
									"      left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum) ".
									"      inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum ".
									"   where (encounter_nr = '". $this->current_enr. "'".$filter.") and is_cash = 0 and p.prod_class = 'M' ".
									"      and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
									"      and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
									"      and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
									" union ".
									"select mph.refno, mph.chrge_dte, 'O' as department, mphd.bestellnum, artikelname, quantity, unit_price ".
									"   from (seg_more_phorder_details as mphd inner join seg_more_phorder as mph on mphd.refno = mph.refno) ".
									"      inner join care_pharma_products_main as p on mphd.bestellnum = p.bestellnum ".
									"   where (encounter_nr = '". $this->current_enr. "'".$filter.") and p.prod_class = 'M' ".
									"      and (str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
									"         and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
									"   order by department, orderdate, artikelname";

		if ($result = $db->Execute($strSQL)) {
			$this->medicines_list = array();

			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$objMed = new Medicine;

					$objMed->setRefNo($row['refno']);
					$objMed->setTransDte($row['orderdate']);
					$objMed->setDept($row['department']);
					$objMed->setBestellNum($row['bestellnum']);
					$objMed->setArtikelName($row['artikelname']);
					$objMed->setItemQty($row['quantity']);
					$objMed->setItemPrice($row['pricecharge']);

					// Add new medicine object in collection (array) of the list of medicines in this billing.
					$this->medicines_list[] = $objMed;
				}
			}
		}
	}	// .... end of getMedicinesList

	function getOpsList() {
		global $db;

				$filter = '';
// 				  "         exists (select * from care_encounter_op as eop where eop.nr = os.nr limit 1) " .

				if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL	= "select refno, request_date, request_time, ops_code, description, rvu, multiplier, op_charge ".
					"   from ".
//				  "(select os.refno, 1 as entry_no, os.request_date, os.request_time, od.ops_code, description, od.rvu, od.multiplier, (od.rvu * od.multiplier) as op_charge, 'OR' as provider " .
//    			  "   from (seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno) " .
//          		  "         inner join seg_ops_rvs as om on od.ops_code = om.code " .
//    			  "   where encounter_nr = '" . $this->current_enr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED' " .
//                  "         and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
//                  "            and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
//				  " union ".
//				  "select mo.refno, entry_no, DATE_FORMAT(mo.chrge_dte, '%Y:%m:%d') as chrgdate, DATE_FORMAT(mo.chrge_dte, '%H:%i:%s') as chrgtime, ".
//				  "      md.ops_code, description, md.rvu, md.multiplier, md.chrg_amnt, 'OA' as provider ".
//				  "   from (seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno) ".
//				  "      inner join seg_ops_rvs as om on md.ops_code = om.code ".
//				  "   where encounter_nr = '". $this->current_enr ."' and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
//				  "         and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."') ".
//                  " union ".
									"(select oah.refno, entry_no, DATE_FORMAT(oah.chrge_dte, '%Y:%m:%d') as request_date, DATE_FORMAT(oah.chrge_dte, '%H:%i:%s') as request_time, ".
									"      concat('OR-', cast(oad.room_nr as char)) as ops_code, concat((select ifnull(name, '') from care_ward where nr = oad.group_nr), '- Room ', cast(cr.room_nr as char)) as description, ".
									"      (select ifnull(sum(rvu), 0) as trvu from seg_ops_chrgd_accommodation as soca where soca.refno = oah.refno and soca.entry_no = oad.entry_no) as rvu, ".
									"      (select multiplier from seg_ops_chrgd_accommodation as soca2 where soca2.refno = oah.refno and soca2.entry_no = oad.entry_no limit 1) as multiplier, oad.charge as op_charge, 'RU' as provider ".
									"   from (seg_opaccommodation as oah inner join seg_opaccommodation_details as oad on oah.refno = oad.refno) ".
									"      inner join care_room as cr on oad.room_nr = cr.nr ".
									"   where (encounter_nr = '". $this->current_enr ."'".$filter.") and (str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
									"      and str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."')) as t ".
						"order by request_date, request_time, description";

		if ($result = $db->Execute($strSQL)) {
			$this->ops_list = array();

			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$objOp = new Operation;

					$objOp->setRefNo($row['refno']);
					$objOp->setTransDteTime($row['request_date'], $row['request_time']);
					$objOp->setOpCode($row['ops_code']);
					$objOp->setOpDesc($row['description']);
					$objOp->setOpRVU($row['rvu']);
					$objOp->setOpMultiplier($row['multiplier']);
					$objOp->setOpCharge($row['op_charge']);
					$objOp->setOpProvider($row['provider']);

					// Add new procedure object in collection (array) of the list of procedures in this billing.
					$this->ops_list[] = $objOp;
				}
			}
		}
	}

	function getProfFeesList() {
		global $db;

		$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($this->bill_dte)));
				$filter = array('','','');

				if ($this->prev_encounter_nr != '') $filter[0] = " or dm1.encounter_nr = '$this->prev_encounter_nr'";
				if ($this->prev_encounter_nr != '') $filter[1] = " or spd.encounter_nr = '$this->prev_encounter_nr'";
				if ($this->prev_encounter_nr != '') $filter[2] = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select attending_dr_nr as dr_nr, name_last, name_first, name_middle, 'Attending Doctor' as role, ".
					"   sum(fn_days_attended(attend_start, if(isnull(attend_end), ifnull(discharge_date, str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s')), attend_end), ".$this->cutoff_hrs.")) as num_days, daily_rate, ".
					"   daily_rate * sum(fn_days_attended(attend_start, if(isnull(attend_end), ifnull(discharge_date, str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s')), attend_end), ".$this->cutoff_hrs.")) as dr_charge, ".
					"   role_nr, role_area, 0 as rvu, 0 as multiplier ".
					"   from ".
					"      (select attending_dr_nr, name_last, name_first, name_middle, attend_start, ".
					"          subdate((select attend_start ".
					"                      from seg_encounter_dr_mgt as dm2 ".
								"                      where dm2.encounter_nr = dm1.encounter_nr and ".
									"                            dm2.att_hist_no > dm1.att_hist_no ".
								"                      order by dm2.att_hist_no asc limit 1), 1) as attend_end, daily_rate, cpa.role_nr, role_area, discharge_date ".
									"          from (seg_encounter_dr_mgt as dm1 inner join (((care_personell as cpn ".
									"             inner join care_person as cp on cpn.pid = cp.pid) inner join care_personell_assignment as cpa ".
					"             on cpn.nr = cpa.personell_nr) inner join care_role_person as crp on ".
					"             cpa.role_nr = crp.nr) on dm1.attending_dr_nr = cpn.nr) inner join care_encounter as ce ".
					"             on dm1.encounter_nr = ce.encounter_nr ".
									"          where (dm1.encounter_nr = '" . $this->current_enr. "'".$filter[0].") " .
					"             and (str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"                and str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
					"          order by att_hist_no) as t ".
					"   group by attending_dr_nr, role_area ".
					" union ".
									"select distinct spd.dr_nr, name_last, name_first, name_middle, name + ' - private' as role, (select sum(days_stay) from seg_encounter_location_addtl as sela where sela.encounter_nr = spd.encounter_nr) as num_days, 0 as daily_rate, ".
									"      sum(ifnull(socd.rvu,0) * ifnull(socd.multiplier,0) * fn_getrvuadjustment(date('".$this->bill_dte."'), role_area)) + dr_charge as dr_charge, spd.dr_role_type_nr, role_area, sum(ifnull(socd.rvu,0)) as tot_rvu, (sum(ifnull(socd.multiplier,0) * ifnull(socd.rvu,0))/sum(ifnull(socd.rvu,0))) as avg_multiplier ".
									"   from ((seg_encounter_privy_dr as spd left join seg_ops_chrg_dr as socd on ".
									"      spd.encounter_nr = socd.encounter_nr and spd.dr_nr = socd.dr_nr and ".
									"      spd.dr_role_type_nr = socd.dr_role_type_nr) inner join (care_personell as cpn ".
									"      inner join care_person as cp on cpn.pid = cp.pid) on spd.dr_nr = cpn.nr) ".
									"      inner join care_role_person as crp on spd.dr_role_type_nr = crp.nr ".
									"   where (spd.encounter_nr = '" . $this->current_enr. "'".$filter[1].") ".
									"      and (str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
									"      and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
									"   group by spd.dr_nr, name_last, name_first, name_middle ".
									" union ".
					"select dr_nr, name_last, name_first, name_middle, name + ' - ' + cop.code as role, null as num_days, 0 as daily_rate, ".
									"   sum((ifnull(sosd.rvu,0) * ifnull(multiplier,0) * fn_getrvuadjustment(date('".$this->bill_dte."'), role_area)) + ops_charge) as dr_charge, sop.role_type_nr, role_area, sum(sosd.rvu) as tot_rvu, (sum(multiplier * sosd.rvu)/sum(sosd.rvu)) as avg_multiplier ".
									"   from (((seg_ops_personell as sop inner join (care_personell as cpn ".
									"      inner join care_person as cp on cpn.pid = cp.pid) on sop.dr_nr = cpn.nr) ".
									"      inner join (seg_ops_serv as sos inner join seg_ops_servdetails as sosd ".
									"         on sos.refno = sosd.refno) on sop.refno = sos.refno) ".
									"      inner join care_role_person as crp on sop.role_type_nr = crp.nr) ".
									"      inner join seg_ops_rvs as cop on sop.ops_code = cop.code ".
									"   where (encounter_nr = '" . $this->current_enr. "'".$filter[2].") and upper(trim(sos.status)) <> 'DELETED' ".
									"      and (str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"         and str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
					"      and role_area is not null " .
									"   group by dr_nr, role_area";

		if ($result = $db->Execute($strSQL)) {
			$this->proffees_list = array();

			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$objpf = new ProfFee;

					$objpf->setDrNr($row['dr_nr']);
					$objpf->setDrLast($row['name_last']);
					$objpf->setDrFirst($row['name_first']);
					$objpf->setDrMid($row['name_middle']);
										$objpf->setRoleNo($row['role_nr']);
					$objpf->setRoleDesc($row['role']);
					$objpf->setRoleBenefit($row['role_area']);
					$objpf->setDaysAttended($row['num_days']);
					$objpf->setDrDailyRate($row['daily_rate']);
					$objpf->setDrCharge($row['dr_charge']);
					$objpf->setRVU($row['rvu']);
					$objpf->setMultiplier($row['multiplier']);

					// Add new Service object in collection (array) of doctors' fees charged in this billing.
					$this->proffees_list[] = $objpf;
				}
			}
		}
	}

	function getMiscellaneousBenefits() {
		global $db;

				$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select mcd.service_code, sos.name, sos.description, sum(mcd.quantity) as qty, (sum(quantity * chrg_amnt)/sum(mcd.quantity)) as avg_chrg, ".
							"      sum(quantity * chrg_amnt) as total_chrg ".
						"   from (seg_misc_chrg as mc inner join seg_misc_chrg_details as mcd on ".
							"      mc.refno = mcd.refno) inner join seg_other_services as sos on ".
							"      mcd.service_code = sos.service_code ".
						"   where (encounter_nr = '" . $this->current_enr. "'".$filter.") ".
							"      and (str_to_date(mc.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
							"      and str_to_date(mc.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
						"   group by mcd.service_code, sos.name ".
					"   order by sos.name";

		if ($result = $db->Execute($strSQL)) {
			$this->hsp_msc_benefits = array();

			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$objMsc = new PerMscChrgCoverage;

					$objMsc->setBillDte($this->bill_dte);
					$objMsc->setCurrentEncounterNr($this->current_enr);
										$objMsc->setPrevEncounterNr($this->prev_encounter_no);
					$objMsc->setMiscCode($row['service_code']);
					$objMsc->setMiscName($row['name']);
					$objMsc->setMiscDesc($row['description']);
					$objMsc->setMiscQty($row['qty']);
					$objMsc->setMiscChrg($row['avg_chrg']);

					$objMsc->computeTotalCoverage($this->getBillAreaDRate('XC'));

					// Add new Service object in collection (array) of miscellaneous charges in this billing.
					$this->hsp_msc_benefits[] = $objMsc;
				}
			}
		}
	}

	function getServiceBenefits() {
		global $db;

				$filter = array('','');

		// Get all the services charged to current encounter ...
				if ($this->prev_encounter_nr != '') $filter[0] = " or encounter_nr = '$this->prev_encounter_nr'";
				if ($this->prev_encounter_nr != '') $filter[1] = " or sos.encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select ld.service_code, ls.name as service_desc, ls.group_code, " .
						"   lsg.name as group_desc, /*count(ld.service_code)*/ sum(ld.quantity) as qty, (sum(ld.price_charge * ld.quantity)/sum(ld.quantity)) as serv_charge, 'LB' as source " .
					"   from ((seg_lab_serv as lh inner join seg_lab_servdetails as ld on lh.refno = ld.refno) " .
					"          inner join seg_lab_services as ls on ld.service_code = ls.service_code) " .
						"          inner join seg_lab_service_groups as lsg on ls.group_code = lsg.group_code " .
									"      where lh.is_cash = 0 and (encounter_nr = '" . $this->current_enr. "'".$filter[0].") and upper(trim(lh.status)) <> 'DELETED' " .
					"         and (str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"            and str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
									"   group by ld.service_code, ls.name, ls.group_code, lsg.name, source " .
					" union ".
					"select rd.service_code, rs.name as service_desc, rs.group_code, " .
					"   rsg.name as group_desc, count(rd.service_code) as qty, (sum(rd.price_charge)/count(rd.service_code)) as serv_charge, 'RD' as source " .
						"   from ((seg_radio_serv as rh inner join care_test_request_radio as rd on rh.refno = rd.refno) " .
							"          inner join seg_radio_services as rs on rd.service_code = rs.service_code) " .
									"          inner join seg_radio_service_groups as rsg on rs.group_code = rsg.group_code " .
						"      where rh.is_cash = 0 and (encounter_nr = '" . $this->current_enr. "'".$filter[0].") and upper(trim(rh.status)) <> 'DELETED' " .
					"         and (str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"            and str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
						"   group by rd.service_code, rs.name, rs.group_code, rsg.name, source ".
					" union ".
									"select pd.bestellnum, artikelname, 'SU' as group_code, 'Supplies' as group_desc, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as serv_charge, 'SU' as source ".
									"   from ((seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
									"      left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum) ".
									"      inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum ".
									"   where (encounter_nr = '". $this->current_enr. "'".$filter[0].") and is_cash = 0 and p.prod_class = 'S' ".
									"      and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
									"      and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
									"      and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
									"   group by pd.bestellnum, artikelname ".
									" union ".
									"select mphd.bestellnum, artikelname, 'MS' as group_code, 'Supplies' as group_desc, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as serv_charge, 'MS' as source ".
									"   from (seg_more_phorder_details as mphd inner join seg_more_phorder as mph on mphd.refno = mph.refno) ".
									"      inner join care_pharma_products_main as p on mphd.bestellnum = p.bestellnum ".
									"   where (encounter_nr = '". $this->current_enr. "'".$filter[0].") and p.prod_class = 'S' ".
									"      and (str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
									"         and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
									"   group by mphd.bestellnum, artikelname ".
									" union ".
									"select eqd.equipment_id, artikelname, '' as group_code, 'Equipment' as group_desc, sum(number_of_usage) as qty, (sum(discounted_price * number_of_usage)/sum(number_of_usage))  as uprice, 'OE' as source
											 from ((seg_equipment_orders as eqh inner join seg_equipment_order_items as eqd on eqh.refno = eqd.refno)
												 inner join seg_ops_serv as sos on sos.refno = eqh.request_refno) inner join care_pharma_products_main as
												 cppm on cppm.bestellnum = eqd.equipment_id
											 where (sos.encounter_nr = '". $this->current_enr. "'".$filter[1].")
													and (str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "'
													and str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "')
											 group by eqd.equipment_id, artikelname ".
									" union ".
					"select md.service_code, ms.name as service_desc, '' as group_code, ".
						"      '' as group_desc, sum(md.quantity) as qty, (sum(chrg_amnt * md.quantity)/sum(md.quantity)) as serv_charge, 'OA' as source ".
						"   from (seg_misc_service as m inner join seg_misc_service_details as md on m.refno = md.refno) ".
							"      inner join seg_other_services as ms on md.service_code = ms.alt_service_code ".
						"   where (encounter_nr = '" . $this->current_enr. "'".$filter[0].") ".
							"      and (str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
							"      and str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
						"   group by md.service_code, ms.name";

		if ($result = $db->Execute($strSQL)) {
			$this->hsp_service_benefits = array();
						$this->valid_covered_items = array();

			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$objServ = new PerServiceCoverage;

					$objServ->setBillDte($this->bill_dte);
					$objServ->setCurrentEncounterNr($this->current_enr);
										$objServ->setPrevEncounterNr($this->prev_encounter_no);
					$objServ->setServiceCode($row['service_code']);
					$objServ->setServiceDesc($row['service_desc']);
					$objServ->setGroupCode($row['group_code']);
					$objServ->setGroupDesc($row['group_desc']);
					$objServ->setServQty($row['qty']);
					$objServ->setServPrice($row['serv_charge']);
					$objServ->setServProvider($row['source']);

					$objServ->computeTotalCoverage($this->getBillAreaDRate('HS'));

					// Add new Service object in collection (array) of services charged in this billing.
					$this->hsp_service_benefits[] = $objServ;

										if ($this->old_bill_nr == '') {
												$this->getValidItemWithAppliedCoverage($this->current_enr, $row['source'], $row['service_code'], ($row['qty'] * $row['serv_charge']));
										}
				} // ... while loop
			}	  // ... if ... recordcount
			else
				$this->errmsg = "No laboratory service!";

						$this->clearInvalidItemsFromCoverage(false);
		}	      // ... if ... execute
	}

	function getMedicineBenefits() {
		global $db;

		$filter = array('','');
/*		$strSQL = "select pd.bestellnum, artikelname, sum(quantity) as qty, avg(pricecharge) as price, sum(quantity * pricecharge) as itemcharge ".
						"   from (seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
								"         inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum ".
						"      where ((encounter_nr = '". $this->current_enr. "' and is_cash = 0 and p.prod_class = 'M' ".
					"         and exists (select * from seg_hcare_products as shp inner join seg_encounter_insurance as si ".
									"                        on shp.hcare_id = si.hcare_id where shp.bestellnum = pd.bestellnum and ".
						"                        si.encounter_nr = '". $this->current_enr. "')) ".
					"         or (encounter_nr = '". $this->current_enr. "' and is_cash = 0 and p.prod_class = 'M')) " .
									"        and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"           and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
									"   group by pd.bestellnum, artikelname";*/

				if ($this->prev_encounter_nr != '') $filter[0] = " or encounter_nr = '$this->prev_encounter_nr'";
				if ($this->prev_encounter_nr != '') $filter[1] = " or si.encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select bestellnum, artikelname, max(flag) as flag, sum(qty) as qty, (sum(price * qty)/sum(qty)) as price, sum(itemcharge) as itemcharge ".
									" from ".
									"(select 0 as flag, pd.bestellnum, artikelname, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as price, sum((pd.quantity - ifnull(spri.quantity, 0)) * pricecharge) as itemcharge ".
						"   from ((seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
									"         left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum) ".
								"         inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum ".
						"      where (((encounter_nr = '". $this->current_enr. "'".$filter[0].") and is_cash = 0 and p.prod_class = 'M' ".
							"         and exists (select * from (seg_hcare_products as shp inner join seg_hcare_bsked as shb ".
					"                           on shp.bsked_id = shb.bsked_id) inner join seg_encounter_insurance as si on shb.hcare_id = si.hcare_id ".
					"                        where shp.bestellnum = pd.bestellnum and ".
					"                           str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
					"                           and (select max(effectvty_dte) as latest ".
					"                                   from seg_hcare_bsked as shb2 ".
									"                                   where shb2.hcare_id = shb.hcare_id ".
									"                                      and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte and	".
							"                           (si.encounter_nr = '". $this->current_enr. "'".$filter[1]."))) ".
					"         or ((encounter_nr = '". $this->current_enr. "'".$filter[0].") and is_cash = 0 and p.prod_class = 'M')) " .
									"        and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"           and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
									"        and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
									"   group by pd.bestellnum, artikelname ".
									" union ".
									"select 1 as flag, mpd.bestellnum, artikelname, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as price, sum(quantity * unit_price) as itemcharge ".
									"   from (seg_more_phorder as mph inner join seg_more_phorder_details as mpd on mph.refno = mpd.refno) ".
									"      inner join care_pharma_products_main as p on mpd.bestellnum = p.bestellnum ".
									"   where (((encounter_nr = '". $this->current_enr. "'".$filter[0].") and p.prod_class = 'M' ".
									"      and exists (select * from (seg_hcare_products as shp inner join seg_hcare_bsked as shb ".
									"                        on shp.bsked_id = shb.bsked_id) inner join seg_encounter_insurance as si on shb.hcare_id = si.hcare_id ".
									"                     where shp.bestellnum = mpd.bestellnum and ".
									"                        str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
									"                        and (select max(effectvty_dte) as latest ".
									"                                from seg_hcare_bsked as shb2 ".
									"                                where shb2.hcare_id = shb.hcare_id ".
									"                                   and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte and ".
									"                        (si.encounter_nr = '". $this->current_enr. "'".$filter[1]."))) ".
									"         or ((encounter_nr = '". $this->current_enr. "'".$filter[0].") and p.prod_class = 'M')) ".
									"        and (str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
									"           and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
									"   group by mpd.bestellnum, artikelname) as t ".
									" group by bestellnum, artikelname order by artikelname";

		if ($result = $db->Execute($strSQL)) {
			$this->med_product_benefits = array();
						$this->valid_covered_items = array();

			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$objMB = new PerMedicineCoverage;

					$objMB->setBillDte($this->bill_dte);
					$objMB->setCurrentEncounterNr($this->current_enr);
										$objMB->setPrevEncounterNr($this->prev_encounter_no);
					$objMB->setBestellNum($row['bestellnum']);
					$objMB->setArtikelName($row['artikelname']);
					$objMB->setItemQty($row['qty']);
					$objMB->setItemPrice($row['price']);
					$objMB->setItemCharge($row['itemcharge']);
										$objMB->setMedsAddedFlag(($row['flag'] > 0));

					$objMB->computeTotalCoverage($this->getBillAreaDRate('MS'));

					// Add new medicine object in collection (array) of the list of medicines in this billing.
					$this->med_product_benefits[] = $objMB;

										if ($this->old_bill_nr == '') {
												$this->getValidItemWithAppliedCoverage($this->current_enr, 'M', $row['bestellnum'], $row['itemcharge']);
										}
				}
			}

						$this->clearInvalidItemsFromCoverage(true);
		}
	}

	function getSupplyBenefits() {
//		global $db;

//		$strSQL = "select pd.bestellnum, artikelname, sum(quantity) as qty, avg(pricecharge) as price, sum(quantity * pricecharge) as itemcharge ".
//    			  "   from (seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
//          		  "         inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum ".
//    			  "      where ((encounter_nr = '". $this->current_enr. "' and is_cash = 0 and p.prod_class = 'S' ".
//         		  "         and exists (select * from (seg_hcare_products as shp inner join seg_hcare_bsked as shb ".
//				  "                           on shp.bsked_id = shb.bsked_id) inner join seg_encounter_insurance as si on shb.hcare_id = si.hcare_id ".
//				  "                        where shp.bestellnum = pd.bestellnum and ".
//				  "                           str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
//				  "                           and (select max(effectvty_dte) as latest ".
//				  "                                   from seg_hcare_bsked as shb2 ".
//                  "                                   where shb2.hcare_id = shb.hcare_id ".
//                  "                                      and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte and	".
//		          "                           si.encounter_nr = '". $this->current_enr. "')) ".
//				  "         or (encounter_nr = '". $this->current_enr. "' and is_cash = 0 and p.prod_class = 'S')) " .
//                  "        and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
//				  "           and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
//                  "   group by pd.bestellnum, artikelname";

//		if ($result = $db->Execute($strSQL)) {
			$this->sup_product_benefits = array();

//			if ($result->RecordCount()) {
//				while ($row = $result->FetchRow()) {
//					$objSB = new PerSupplyCoverage;
//
//					$objSB->setBillDte($this->bill_dte);
//					$objSB->setCurrentEncounterNr($this->current_enr);
//					$objSB->setBestellNum($row['bestellnum']);
//					$objSB->setArtikelName($row['artikelname']);
//					$objSB->setItemQty($row['qty']);
//					$objSB->setItemPrice($row['price']);
//					$objSB->setItemCharge($row['itemcharge']);
//
//					$objSB->computeTotalCoverage($this->getBillAreaDRate('MS'));
//
					// Add new medicine object in collection (array) of the list of medicines in this billing.
//					$this->sup_product_benefits[] = $objSB;
//				}
//			}
//		}
	}

	function getOpBenefits() {
		global $db;

//		$strSQL = "select od.ops_code, description, sum(od.rvu) as sum_rvu, avg(od.multiplier) as op_multiplier " .
//   				  "   from (seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno) " .
//         		  "         inner join care_ops301_en as om on od.ops_code = om.code " .
//   				  "   where encounter_nr = '". $this->current_enr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED' " .
//                  "         and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
//				  "            and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
//   				  "   group by od.ops_code, description " .
//   				  "   order by od.ops_code";

				$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select ops_code, description, provider, sum(rvu) as sum_rvu, (sum(multiplier * rvu)/sum(rvu)) as op_multiplier, sum(op_charge) as tot_charge ".
					"   from ".
//				  "(select os.refno, 1 as entry_no, os.request_date, os.request_time, od.ops_code, description, od.rvu, od.multiplier, (od.rvu * od.multiplier) as op_charge, 'OR' as provider ".
//   				  "   from (seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno) ".
//         		  "      inner join seg_ops_rvs as om on od.ops_code = om.code ".
//   				  "   where encounter_nr = '". $this->current_enr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED' ".
//         		  "      and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
//            	  "      and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."') ".
//				  " union ".
//				  " select mo.refno, entry_no, DATE_FORMAT(mo.chrge_dte, '%Y:%m:%d') as chrgdate, DATE_FORMAT(mo.chrge_dte, '%H:%i:%s') as chrgtime, ".
//   				  "       md.ops_code, description, md.rvu, md.multiplier, md.chrg_amnt, 'OA' as provider ".
//   				  "    from (seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno) ".
//         		  "       inner join seg_ops_rvs as om on md.ops_code = om.code ".
//   				  "    where encounter_nr = '". $this->current_enr. "' and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
//            	  "       and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."') ".
//                  " union ".
									"(select oah.refno, entry_no, DATE_FORMAT(oah.chrge_dte, '%Y:%m:%d') as chrgdate, DATE_FORMAT(oah.chrge_dte, '%H:%i:%s') as chrgtime, ".
									"      concat('OR-', cast(oad.room_nr as char)) as ops_code, concat((select ifnull(name, '') from care_ward where nr = oad.group_nr), '- Room ', cast(cr.room_nr as char)) as description, ".
									"      (select ifnull(sum(rvu), 0) as trvu from seg_ops_chrgd_accommodation as soca where soca.refno = oah.refno and soca.entry_no = oad.entry_no) as rvu, ".
									"      (select multiplier from seg_ops_chrgd_accommodation as soca2 where soca2.refno = oah.refno and soca2.entry_no = oad.entry_no limit 1) as multiplier, oad.charge as op_charge, 'RU' as provider ".
									"   from (seg_opaccommodation as oah inner join seg_opaccommodation_details as oad on oah.refno = oad.refno) ".
									"      inner join care_room as cr on oad.room_nr = cr.nr ".
									"   where (encounter_nr = '". $this->current_enr ."'".$filter.") and (str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
									"      and str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."')) as t ".
					"group by provider, ops_code, description order by ops_code";

		if ($result = $db->Execute($strSQL)) {
			$this->hsp_ops_benefits = array();

			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$objOp = new PerOpCoverage;

					$objOp->setBillDte($this->bill_dte);
					$objOp->setCurrentEncounterNr($this->current_enr);
										$objOp->setPrevEncounterNr($this->prev_encounter_no);
					$objOp->setOpCode($row['ops_code']);
					$objOp->setOpDesc($row['description']);
					$objOp->setOpRVU($row['sum_rvu']);
					$objOp->setOpMultiplier($row['op_multiplier']);
					$objOp->setOpCharge($row['tot_charge']);
					$objOp->setOpProvider($row['provider']);

					$objOp->computeTotalCoverage($this->getBillAreaDRate('OR'));

					// Add new medicine object in collection (array) of the list of medicines in this billing.
					$this->hsp_ops_benefits[] = $objOp;
				}
			}
		}
	}

	function getProfFeesBenefits() {
		global $db;

		$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($this->bill_dte)));

				$filter = array('','','');

				if ($this->prev_encounter_nr != '') $filter[0] = " or dm1.encounter_nr = '$this->prev_encounter_nr'";
				if ($this->prev_encounter_nr != '') $filter[1] = " or spd.encounter_nr = '$this->prev_encounter_nr'";
				if ($this->prev_encounter_nr != '') $filter[2] = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select role_area, role_type_level, sum(num_days) as totaldays, sum(rvu) as totalrvu, (sum(multiplier * rvu)/sum(rvu)) as avgmuliplier, sum(dr_charge) as totalcharge ".
					"  from ".
							"  (select attending_dr_nr as dr_nr, name_last, name_first, name_middle, 'Attending Doctor' as role, ".
					"   sum(fn_days_attended(attend_start, if(isnull(attend_end), ifnull(discharge_date, str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s')), attend_end), ".$this->cutoff_hrs.")) as num_days, daily_rate, ".
					"   daily_rate * sum(fn_days_attended(attend_start, if(isnull(attend_end), ifnull(discharge_date, str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s')), attend_end), ".$this->cutoff_hrs.")) as dr_charge, ".
					"   role_area, 0 as role_type_level, 0 as rvu, 0 as multiplier ".
					"   from ".
					"      (select attending_dr_nr, name_last, name_first, name_middle, attend_start, ".
					"          subdate((select attend_start ".
					"                      from seg_encounter_dr_mgt as dm2 ".
								"                      where dm2.encounter_nr = dm1.encounter_nr and ".
									"                            dm2.att_hist_no > dm1.att_hist_no ".
								"                      order by dm2.att_hist_no asc limit 1), 1) as attend_end, daily_rate, role_area, discharge_date ".
									"          from (seg_encounter_dr_mgt as dm1 inner join (((care_personell as cpn ".
									"             inner join care_person as cp on cpn.pid = cp.pid) inner join care_personell_assignment as cpa ".
					"             on cpn.nr = cpa.personell_nr) inner join care_role_person as crp on ".
					"             cpa.role_nr = crp.nr) on dm1.attending_dr_nr = cpn.nr) inner join care_encounter as ce ".
					"             on dm1.encounter_nr = ce.encounter_nr ".
									"          where (dm1.encounter_nr = '" . $this->current_enr. "'".$filter[0].") " .
					"             and (str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"                and str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
					"          order by att_hist_no) as t ".
					"   group by attending_dr_nr, role_area ".
					" union ".
									"select distinct spd.dr_nr, name_last, name_first, name_middle, name + ' - private' as role, (select sum(days_stay) from seg_encounter_location_addtl as sela where sela.encounter_nr = spd.encounter_nr) as num_days, 0 as daily_rate, ".
									"      sum(ifnull(socd.rvu,0) * ifnull(socd.multiplier,0) * fn_getrvuadjustment(date('".$this->bill_dte."'), role_area)) + dr_charge as dr_charge, role_area, role_type_level, sum(ifnull(socd.rvu,0)) as tot_rvu, (sum(ifnull(socd.multiplier,0) * ifnull(socd.rvu,0))/sum(ifnull(socd.rvu,0))) as avg_multiplier ".
									"   from ((seg_encounter_privy_dr as spd left join seg_ops_chrg_dr as socd on ".
									"      spd.encounter_nr = socd.encounter_nr and spd.dr_nr = socd.dr_nr and ".
									"      spd.dr_role_type_nr = socd.dr_role_type_nr) inner join (care_personell as cpn ".
									"      inner join care_person as cp on cpn.pid = cp.pid) on spd.dr_nr = cpn.nr) ".
									"      inner join care_role_person as crp on spd.dr_role_type_nr = crp.nr ".
									"   where (spd.encounter_nr = '" . $this->current_enr. "'".$filter[1].") ".
									"      and (str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
									"      and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
									"   group by spd.dr_nr, name_last, name_first, name_middle ".
									" union ".
					"   select dr_nr, name_last, name_first, name_middle, name + ' - ' + cop.code as role, null as num_days, 0 as daily_rate, ".
									"      sum((ifnull(sosd.rvu,0) * ifnull(multiplier,0) * fn_getrvuadjustment(date('".$this->bill_dte."'), role_area)) + ops_charge) as dr_charge, role_area, role_type_level, sum(sosd.rvu) as tot_rvu, (sum(multiplier * sosd.rvu)/sum(sosd.rvu)) as avg_multiplier ".
									"      from (((seg_ops_personell as sop inner join (care_personell as cpn ".
									"         inner join care_person as cp on cpn.pid = cp.pid) on sop.dr_nr = cpn.nr) ".
									"         inner join (seg_ops_serv as sos inner join seg_ops_servdetails as sosd ".
							"            on sos.refno = sosd.refno) on sop.refno = sos.refno) ".
									"         inner join care_role_person as crp on sop.role_type_nr = crp.nr) ".
									"         inner join seg_ops_rvs as cop on sop.ops_code = cop.code ".
									"      where (encounter_nr = '" . $this->current_enr. "'".$filter[2].") and upper(trim(sos.status)) <> 'DELETED' ".
									"         and (str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"            and str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
					"         and role_area is not null " .
									"      group by dr_nr, role_area) ".
					" as o group by role_area";

		if ($result = $db->Execute($strSQL)) {
			$this->hsp_pfs_benefits = array();

			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$objpfc = new ProfFeeCoverage;

					$objpfc->setRoleBenefit($row['role_area']);
										$objpfc->setRoleLevel((is_null($row['role_type_level']) ? 0 : $row['role_type_level']));
					$objpfc->getRoleDesc();
					if (is_null($row['totaldays']))
						$objpfc->setDaysAttended(0);
					else
						$objpfc->setDaysAttended($row['totaldays']);
					$objpfc->setDrCharge($row['totalcharge']);
					$objpfc->setRVU($row['totalrvu']);
					$objpfc->setMultiplier($row['avgmuliplier']);

					// Add new object in collection (array) of doctors' fees charged in this billing.
					$this->hsp_pfs_benefits[] = $objpfc;
				}
			}
		}
	}

	function getHCareSkedPerConfine($nbsked_id, $nconfinetype_id) {
		global $db;

		$strSQL = "select * ".
							"   from seg_hcare_confinetype ".
					"   where bsked_id       = ". $nbsked_id ." and ".
					"         confinetype_id = ". $nconfinetype_id;

		if ($result = $db->Execute($strSQL)) {
			$this->skedvalues = array();
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$this->skedvalues['rateperday']             = $row['rateperday'];
					$this->skedvalues['amountlimit']            = $row['amountlimit'];
					$this->skedvalues['dayslimit']              = $row['dayslimit'];
					$this->skedvalues['rateperRVU']			    = $row['rateperRVU'];
										$this->skedvalues['limit_rvubased']         = $row['limit_rvubased'];
					$this->skedvalues['year_dayslimit']         = $row['year_dayslimit'];
					$this->skedvalues['year_dayslimit_alldeps'] = $row['year_dayslimit_alldeps'];
				}
			}
		}
	}

	function getHCareSkedPerRVURange($nbsked_id, $nRVU) {
		global $db;

		$strSQL = "select shrvu.* ".
					"   from seg_hcare_rvurange as shrvu ".
					"   where bsked_id       = $nbsked_id and ".
					"         ((range_start <= $nRVU and range_end >= $nRVU) or " .
					"          (range_start <= $nRVU and range_end = 0))";

		if ($result = $db->Execute($strSQL)) {
			$this->skedvalues = array();
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$this->skedvalues['rateperRVU']  = $row['rateperRVU'];
										$this->skedvalues['fixedamount'] = $row['fixedamount'];
										$this->skedvalues['minamount']   = $row['minamount'];
					$this->skedvalues['amountlimit'] = $row['amountlimit'];
				}
			}
		}
	}

	// Before calling this function, the accommodation_hist array must already be populated with
	// patient's accommodation history and getRoomTypeBenefits() already called ....
	function compTotalAccommodationChrg() {
		$nCharge = 0;
		$ndays   = 0;
		$nhrs    = 0;

		if (!empty($this->acc_roomtype_benefits))
			foreach ($this->acc_roomtype_benefits as $objrmtyp) {
				$nCharge += $objrmtyp->getActualCharge();
				$ndays   += $objrmtyp->getDaysCount();
				$nhrs    += $objrmtyp->getExcessHours();
			}

		// Correct accumulated duration of accommodation in days and hours ...
		$ndaysadded = intval($nhrs / 24);
		$nhrs       = $nhrs % 24;
		$ndays 	   += $ndaysadded;

		if ($nhrs > $this->cutoff_hrs) {
			$ndays++;
			$nhrs = 0;
		}
		else
			$nhrs = 0;

		$this->days_count   = $ndays;
		$this->excess_hours = $nhrs;

		return($nCharge);
	}

	function isPersonPrincipal($n_hcareid) {
		global $db;

		$this->bPrincipal = false;
				$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select is_principal ".
					"   from care_person_insurance as cpi inner join care_encounter as ce on cpi.pid = ce.pid ".
					"   where (encounter_nr = '". $this->current_enr. "'".$filter.") and hcare_id = ". $n_hcareid;

		if ($result = $db->Execute($strSQL))
			if ($result->RecordCount())
				while ($row = $result->FetchRow()) {
					if ($row['is_principal'])
						$this->bPrincipal = true;
					else
						$this->bPrincipal = false;
				}

		return($this->bPrincipal);
	}

	function getPreviousConfineDays($nyear, $nhcareid) {
		global $db;

		$ndays = 0;

		if ($this->bPrincipal)
			$strSQL = "select sum(confine_days) as tdays ".
						"   from seg_confinement_tracker as sct ".
						"   where current_year = ". $nyear. " ".
						"      and exists (select * from care_encounter as ce ".
						"                     where ce.pid = sct.pid and ce.encounter_nr = '". $this->current_enr. "') ".
						"      and hcare_id = ". $nhcareid;
		else
			$strSQL = "select sum(confine_days) as tdays ".
						"   from seg_confinement_tracker as sct1 ".
						"   where exists (select principal_pid ".
						"                    from seg_confinement_tracker as sct0 ".
						"                    where current_year = ". $nyear. " ".
						"                       and exists (select * from care_encounter as ce ".
						"                                      where ce.pid = sct0.pid and ce.encounter_nr = '". $this->current_enr. "') ".
						"                       and sct0.hcare_id = ". $nhcareid ." ".
						"                       and sct0.principal_pid = sct1.principal_pid) ".
						"      and sct1.hcare_id = ". $nhcareid ." ".
						"      and sct1.current_year = ". $nyear;

		if ($result = $db->Execute($strSQL))
			if ($result->RecordCount())
				while ($row = $result->FetchRow()) {
					$ndays = $row['tdays'];
				}

		return($ndays);
	}

	function isRoomTypeInHist($ntype_nr, $src) {
		$nindx = -1;
		$i = 0;

		if (!empty($this->acc_roomtype_benefits))
			foreach($this->acc_roomtype_benefits as $objrmtyp) {
				if (($objrmtyp->type_nr == $ntype_nr) && ($objrmtyp->getSource() == $src)) {
					$nindx = $i;
					break;
				}
				$i += 1;
			}

		return($nindx);
	}

	function getTotalSrvCoverage($nhcare_id) {
		$ntotal = 0;

		if (!empty($this->hsp_service_benefits))
			foreach($this->hsp_service_benefits as $objhsp) {
				if (!empty($objhsp->available_limitedhplans))
					foreach($objhsp->available_limitedhplans as $objsrv) {
						if ($objsrv->getID() == $nhcare_id)
							$ntotal += $objsrv->getCoverage();
					}
			}

		return($ntotal);
	}

	function getTotalMscCoverage($nhcare_id) {
		$ntotal = 0;

		if (!empty($this->hsp_msc_benefits))
			foreach($this->hsp_msc_benefits as $objb) {
				if (!empty($objb->available_limitedhplans))
					foreach($objb->available_limitedhplans as $objmsc) {
						if ($objmsc->getID() == $nhcare_id)
							$ntotal += $objmsc->getCoverage();
					}
			}

		return($ntotal);
	}

	function getTotalMedCoverage($nhcare_id) {
		$ntotal = 0;

		if (!empty($this->med_product_benefits))
			foreach($this->med_product_benefits as $objmb) {
				if (!empty($objmb->available_limitedhplans))
					foreach($objmb->available_limitedhplans as $objmed) {
						if ($objmed->getID() == $nhcare_id)
							$ntotal += $objmed->getCoverage();
					}
			}

		return($ntotal);
	}

	function getTotalSupCoverage($nhcare_id) {
		$ntotal = 0;

		if (!empty($this->sup_product_benefits))
			foreach($this->sup_product_benefits as $objsb) {
				if (!empty($objsb->available_limitedhplans))
					foreach($objsb->available_limitedhplans as $objsup) {
						if ($objsup->getID() == $nhcare_id)
							$ntotal += $objsup->getCoverage();
					}
			}

		return($ntotal);
	}

	function getTotalOpCoverage($nhcare_id) {
		$ntotal = 0;

		if (!empty($this->hsp_ops_benefits))
			foreach($this->hsp_ops_benefits as $objOp) {
				if (!empty($objOp->available_limitedhplans))
					foreach($objOp->available_limitedhplans as $objOpCare) {
						if ($objOpCare->getID() == $nhcare_id)
							$ntotal += $objOpCare->getCoverage();
					}
			}

		return($ntotal);
	}

	function getTotalSrvCharge() {
		global $db;

		$ntotal = 0;
				$filter = array('','');

		// Get all the services charged to current encounter ...
				if ($this->prev_encounter_nr != '') $filter[0] = " or encounter_nr = '$this->prev_encounter_nr'";
				if ($this->prev_encounter_nr != '') $filter[1] = " or sos.encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select sum(qty * serv_charge) as total_charge from " .
					"(select ld.service_code, /*count(ld.service_code)*/ sum(ld.quantity) as qty, (sum(ld.price_charge * ld.quantity)/sum(ld.quantity)) as serv_charge, 'LB' as source " .
					"   from ((seg_lab_serv as lh inner join seg_lab_servdetails as ld on lh.refno = ld.refno) " .
					"          inner join seg_lab_services as ls on ld.service_code = ls.service_code) " .
						"          inner join seg_lab_service_groups as lsg on ls.group_code = lsg.group_code " .
									"      where lh.is_cash = 0 and (encounter_nr = '" . $this->current_enr. "'".$filter[0].") and upper(trim(lh.status)) <> 'DELETED' " .
					"         and (str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"            and str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
									"   group by ld.service_code " .
					" union ".
					"select rd.service_code, count(rd.service_code) as qty, (sum(rd.price_charge)/count(rd.service_code)) as serv_charge, 'RD' as source " .
						"   from ((seg_radio_serv as rh inner join care_test_request_radio as rd on rh.refno = rd.refno) " .
							"          inner join seg_radio_services as rs on rd.service_code = rs.service_code) " .
									"          inner join seg_radio_service_groups as rsg on rs.group_code = rsg.group_code " .
						"      where rh.is_cash = 0 and (encounter_nr = '" . $this->current_enr. "'".$filter[0].") and upper(trim(rh.status)) <> 'DELETED' " .
					"         and (str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"            and str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
						"   group by rd.service_code ".
					" union ".
									"select pd.bestellnum, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as serv_charge, 'SU' as source ".
									"   from ((seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
									"      left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum) ".
									"      inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum ".
									"   where (encounter_nr = '". $this->current_enr. "'".$filter[0].") and is_cash = 0 and p.prod_class = 'S' ".
									"      and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
									"      and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
									"      and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
									"   group by pd.bestellnum ".
									" union ".
									"select mphd.bestellnum, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as serv_charge, 'MS' as source ".
									"   from (seg_more_phorder_details as mphd inner join seg_more_phorder as mph on mphd.refno = mph.refno) ".
									"      inner join care_pharma_products_main as p on mphd.bestellnum = p.bestellnum ".
									"   where (encounter_nr = '". $this->current_enr. "'".$filter[0].") and p.prod_class = 'S' ".
									"      and (str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
									"         and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
									"   group by mphd.bestellnum ".
									" union ".
									"select eqd.equipment_id, sum(number_of_usage) as qty, (sum(discounted_price * number_of_usage)/sum(number_of_usage)) as uprice, 'OE' as source
											 from ((seg_equipment_orders as eqh inner join seg_equipment_order_items as eqd on eqh.refno = eqd.refno)
												 inner join seg_ops_serv as sos on sos.refno = eqh.request_refno) inner join care_pharma_products_main as
												 cppm on cppm.bestellnum = eqd.equipment_id
											 where (sos.encounter_nr = '". $this->current_enr. "'".$filter[1].")
													and (str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "'
													and str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "')
											 group by eqd.equipment_id ".
									" union ".
					"select md.service_code, sum(md.quantity) as qty, (sum(chrg_amnt * md.quantity)/sum(md.quantity)) as serv_charge, 'OA' as source ".
						"   from (seg_misc_service as m inner join seg_misc_service_details as md on m.refno = md.refno) ".
							"      inner join seg_other_services as ms on md.service_code = ms.alt_service_code ".
						"   where (encounter_nr = '" . $this->current_enr. "'".$filter[0].") ".
							"      and (str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
							"      and str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
						"   group by md.service_code) as t";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					if (!is_null($row['total_charge']))
						$ntotal += $row['total_charge'];
				}
			}
		}

		return($ntotal);
	}

	function getTotalMscCharge() {
		global $db;

		$ntotal = 0;
				$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select sum(chrg_amnt * quantity) as total_chrg ".
						"   from (seg_misc_chrg as mc inner join seg_misc_chrg_details as mcd on ".
							"      mc.refno = mcd.refno) inner join seg_other_services as sos on ".
							"      mcd.service_code = sos.service_code ".
						"   where (encounter_nr = '". $this->current_enr. "'".$filter.") ".
							"      and (str_to_date(mc.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
							"      and str_to_date(mc.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "')";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					if (!is_null($row['total_chrg']))
						$ntotal += $row['total_chrg'];
				}
			}
		}

		return($ntotal);
	}

	function getTotalMedCharge() {
		global $db;

		$ntotal = 0;
				$filter = array('','');

				if ($this->prev_encounter_nr != '') $filter[0] = " or encounter_nr = '$this->prev_encounter_nr'";
				if ($this->prev_encounter_nr != '') $filter[1] = " or si.encounter_nr = '$this->prev_encounter_nr'";
				$strSQL = "select sum(itemcharge) as itemcharge ".
									" from ".
									"(select 0 as flag, pd.bestellnum, artikelname, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as price, sum((pd.quantity - ifnull(spri.quantity, 0)) * pricecharge) as itemcharge ".
									"   from ((seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
									"         left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum) ".
										"         inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum ".
									"      where (((encounter_nr = '". $this->current_enr. "'".$filter[0].") and is_cash = 0 and p.prod_class = 'M' ".
									 "         and exists (select * from (seg_hcare_products as shp inner join seg_hcare_bsked as shb ".
									"                           on shp.bsked_id = shb.bsked_id) inner join seg_encounter_insurance as si on shb.hcare_id = si.hcare_id ".
									"                        where shp.bestellnum = pd.bestellnum and ".
									"                           str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
									"                           and (select max(effectvty_dte) as latest ".
									"                                   from seg_hcare_bsked as shb2 ".
									"                                   where shb2.hcare_id = shb.hcare_id ".
									"                                      and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte and    ".
									"                           (si.encounter_nr = '". $this->current_enr. "'".$filter[1]."))) ".
									"         or ((encounter_nr = '". $this->current_enr. "'".$filter[0].") and is_cash = 0 and p.prod_class = 'M')) " .
									"        and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
									"           and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
									"        and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
									"   group by pd.bestellnum, artikelname ".
									" union ".
									"select 1 as flag, mpd.bestellnum, artikelname, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as price, sum(quantity * unit_price) as itemcharge ".
									"   from (seg_more_phorder as mph inner join seg_more_phorder_details as mpd on mph.refno = mpd.refno) ".
									"      inner join care_pharma_products_main as p on mpd.bestellnum = p.bestellnum ".
									"   where (((encounter_nr = '". $this->current_enr. "'".$filter[0].") and p.prod_class = 'M' ".
									"      and exists (select * from (seg_hcare_products as shp inner join seg_hcare_bsked as shb ".
									"                        on shp.bsked_id = shb.bsked_id) inner join seg_encounter_insurance as si on shb.hcare_id = si.hcare_id ".
									"                     where shp.bestellnum = mpd.bestellnum and ".
									"                        str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
									"                        and (select max(effectvty_dte) as latest ".
									"                                from seg_hcare_bsked as shb2 ".
									"                                where shb2.hcare_id = shb.hcare_id ".
									"                                   and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte and ".
									"                        (si.encounter_nr = '". $this->current_enr. "'".$filter[1]."))) ".
									"         or ((encounter_nr = '". $this->current_enr. "'".$filter[0].") and p.prod_class = 'M')) ".
									"        and (str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
									"           and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
									"   group by mpd.bestellnum, artikelname) as t";

//		$strSQL = "select sum(itemcharge) as itemcharge ".
//                  " from ".
//                  "(select '1' as source, sum((pd.quantity - ifnull(spri.quantity, 0)) * pricecharge) as itemcharge ".
//                  "   from ((seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
//                  "      left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum) ".
//                  "      inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum ".
//                  "   where (encounter_nr = '". $this->current_enr. "'".$filter.") and is_cash = 0 and p.prod_class = 'M' and pd.serve_dt is not null ".
//                  "      and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
//                  "      and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
//                  "      and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
//                  " union ".
//                  "select '2' as source, sum(quantity * unit_price) as itemcharge ".
//                  "   from (seg_more_phorder as mph inner join seg_more_phorder_details as mpd on mph.refno = mpd.refno) ".
//                  "         inner join care_pharma_products_main as p on mpd.bestellnum = p.bestellnum ".
//                  "      where (encounter_nr = '". $this->current_enr. "'".$filter.") and p.prod_class = 'M' ".
//                  "         and (str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
//                  "            and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "')) as t ";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					if (!is_null($row['itemcharge']))
						$ntotal += $row['itemcharge'];
				}
			}
		}

		return($ntotal);
	}

	function getTotalSupCharge() {
//		global $db;

		$ntotal = 0;
//		$strSQL = "select sum(quantity * pricecharge) as itemcharge ".
//    			  "   from (seg_pharma_orders as ph inner join seg_pharma_order_items as pd on ph.refno = pd.refno) ".
//				  "         inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum ".
//    			  "      where encounter_nr = '". $this->current_enr. "' and is_cash = 0 and p.prod_class = 'S' " .
//                  "        and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
//				  "           and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "')";
//
//		if ($result = $db->Execute($strSQL)) {
//			if ($result->RecordCount()) {
//				while ($row = $result->FetchRow()) {
//					if (!is_null($row['itemcharge']))
//						$ntotal += $row['itemcharge'];
//				}
//			}
//		}

		return($ntotal);
	}

	function getTotalOpCharge() {
		global $db;

		$ntotal = 0;

//		$strSQL = "select sum(od.rvu * od.multiplier) as op_charge " .
//   				  "   from (seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno) " .
//         		  "         inner join care_ops301_en as om on od.ops_code = om.code " .
//   				  "   where encounter_nr = '". $this->current_enr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED' " .
//                  "         and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
//				  "            and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "')";

				$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select sum(op_charge) as tot_charge from " .
//				  "(select os.refno, 1 as entry_no, os.request_date, os.request_time, od.ops_code, description, od.rvu, od.multiplier, (od.rvu * od.multiplier) as op_charge, 'OR' as provider ".
//   				  "   from (seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno) ".
//         		  "      inner join seg_ops_rvs as om on od.ops_code = om.code ".
//   				  "   where encounter_nr = '". $this->current_enr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED' ".
//         		  "      and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
//            	  "      and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."') ".
//				  " union ".
//				  " select mo.refno, entry_no, DATE_FORMAT(mo.chrge_dte, '%Y:%m:%d') as chrgdate, DATE_FORMAT(mo.chrge_dte, '%H:%i:%s') as chrgtime, ".
//   				  "       md.ops_code, description, md.rvu, md.multiplier, md.chrg_amnt, 'OA' as provider ".
//   				  "    from (seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno) ".
//         		  "       inner join seg_ops_rvs as om on md.ops_code = om.code ".
//   				  "    where encounter_nr = '". $this->current_enr. "' and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
//            	  "       and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."') ".
//                  " union ".
									"(select oah.refno, entry_no, DATE_FORMAT(oah.chrge_dte, '%Y:%m:%d') as chrgdate, DATE_FORMAT(oah.chrge_dte, '%H:%i:%s') as chrgtime, ".
									"      concat('OR-', cast(oad.room_nr as char)) as ops_code, concat((select ifnull(name, '') from care_ward where nr = oad.group_nr), '- Room ', cast(cr.room_nr as char)) as description, ".
									"      (select ifnull(sum(rvu), 0) as trvu from seg_ops_chrgd_accommodation as soca where soca.refno = oah.refno and soca.entry_no = oad.entry_no) as rvu, ".
									"      (select multiplier from seg_ops_chrgd_accommodation as soca2 where soca2.refno = oah.refno and soca2.entry_no = oad.entry_no limit 1) as multiplier, oad.charge as op_charge, 'RU' as provider ".
									"   from (seg_opaccommodation as oah inner join seg_opaccommodation_details as oad on oah.refno = oad.refno) ".
									"      inner join care_room as cr on oad.room_nr = cr.nr ".
									"   where (encounter_nr = '". $this->current_enr ."'".$filter.") and (str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
									"      and str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."')) as t";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					if (!is_null($row['tot_charge']))
						$ntotal += $row['tot_charge'];
				}
			}
		}

		return($ntotal);
	}

	function getTotalPFParams(&$n_days, &$n_rvu, &$n_pf, $role_area = '', $role_level = 0) {
		$n_days = 0;
		$n_rvu = 0;
		$n_pf = 0;

		if (!empty($this->hsp_pfs_benefits))
			foreach ($this->hsp_pfs_benefits as $objpf) {
				if ($objpf->getRoleBenefit() == $role_area) {
										if ($role_level != 0) {
												if ($role_level == $objpf->getRoleLevel()) {
														$n_days += $objpf->getDaysAttended();
														$n_rvu  += $objpf->getRVU();
														$n_pf   += $objpf->getDrCharge();
												}
										}
										else {
							$n_days += $objpf->getDaysAttended();
							$n_rvu  += $objpf->getRVU();
							$n_pf   += $objpf->getDrCharge();
										}
				}
			}
	}

	function getTotalPFCharge() {
		// Compute total doctors' fees ...
		$npf      = 0;
		$ndays    = 0;
		$nrvu     = 0;
		$total_df = 0;

		// .... D1 role
		$this->getTotalPFParams($ndays, $nrvu, $npf, 'D1');
		$total_df += $npf;

		// .... D2 role
		$this->getTotalPFParams($ndays, $nrvu, $npf, 'D2');
		$total_df += $npf;

		// .... D3 role
		$this->getTotalPFParams($ndays, $nrvu, $npf, 'D3');
		$total_df += $npf;

		// .... D4 role
		$this->getTotalPFParams($ndays, $nrvu, $npf, 'D4');
		$total_df += $npf;

		return($total_df);
	}

	function getTotalPFCoverage() {
		$total = 0;

		$total += $this->pfs_confine_coverage['D1'];
		$total += $this->pfs_confine_coverage['D2'];
		$total += $this->pfs_confine_coverage['D3'];
		$total += $this->pfs_confine_coverage['D4'];

		return($total);
	}

	function getTotalRVU() {
		global $db;

		$ntotal = 0;

//		$strSQL = "select sum(od.rvu) as total_rvu " .
//   				  "   from (seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno) " .
//         		  "         inner join care_ops301_en as om on od.ops_code = om.code " .
//   				  "   where encounter_nr = '". $this->current_enr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED' " .
//                  "         and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
//				  "            and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "')";

				$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select sum(rvu) as total_rvu from " .
					"(select os.refno, 1 as entry_no, os.request_date, os.request_time, od.ops_code, description, od.rvu, od.multiplier, (od.rvu * od.multiplier) as op_charge, 'OR' as provider ".
						"   from (seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno) ".
							"      inner join seg_ops_rvs as om on od.ops_code = om.code ".
						"   where (encounter_nr = '". $this->current_enr. "'".$filter.") and is_cash = 0 and upper(trim(os.status)) <> 'DELETED' ".
							"      and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
								"      and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."') ".
					" union ".
					" select mo.refno, entry_no, DATE_FORMAT(mo.chrge_dte, '%Y:%m:%d') as chrgdate, DATE_FORMAT(mo.chrge_dte, '%H:%i:%s') as chrgtime, ".
						"       md.ops_code, description, md.rvu, md.multiplier, md.chrg_amnt, 'OA' as provider ".
						"    from (seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno) ".
							"       inner join seg_ops_rvs as om on md.ops_code = om.code ".
						"    where (encounter_nr = '". $this->current_enr. "'".$filter.") and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
								"       and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."')) as t";
//                  " union ".
//                  " select oah.refno, entry_no, DATE_FORMAT(oah.chrge_dte, '%Y:%m:%d') as chrgdate, DATE_FORMAT(oah.chrge_dte, '%H:%i:%s') as chrgtime, ".
//                  "       concat('OR-', cast(oad.room_nr as char)) as ops_code, concat((select ifnull(name, '') from care_ward where nr = oad.group_nr), '- Room ', cast(cr.room_nr as char)) as description, ".
//                  "       (select ifnull(sum(rvu), 0) as trvu from seg_ops_chrgd_accommodation as soca where soca.refno = oah.refno and soca.entry_no = oad.entry_no) as rvu, ".
//                  "       (select multiplier from seg_ops_chrgd_accommodation as soca2 where soca2.refno = oah.refno and soca2.entry_no = oad.entry_no limit 1) as multiplier, oad.charge, 'RU' as provider ".
//                  "    from (seg_opaccommodation as oah inner join seg_opaccommodation_details as oad on oah.refno = oad.refno) ".
//                  "      inner join care_room as cr on oad.room_nr = cr.nr ".
//                  "    where encounter_nr = '". $this->current_enr ."' and (str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
//                  "       and str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."')) as t";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					if (!is_null($row['total_rvu']))
						$ntotal += $row['total_rvu'];
				}
			}
		}

		return($ntotal);
	}

	// Assumption ... getAccommodationHist() already called.
	function getRoomTypeBenefits() {
		$this->acc_roomtype_benefits = array();

		$hrs_accum = 0;
		$indx      = 0;

		$sec_min  = 60;
		$sec_hour = $sec_min * 60;
		$sec_day  = $sec_hour * 24;

#		$this->errmsg = '';

		// Construct the array of room type benefits ...
		if (!empty($this->accommodation_hist)) {
			if (is_array($this->accommodation_hist))
				$indx_cnt = count($this->accommodation_hist);
			else
				$indx_cnt = 1;

			foreach($this->accommodation_hist as $objacc) {
				++$indx;

				if ($objacc->getSource() == 'AD') {
					// Compute the no. of days and excess hours ...
					$ndiff = abs(strtotime($objacc->admission_dtetime) - strtotime($objacc->discharge_dtetime));
#					$ndiff = strtotime($objacc->discharge_dtetime) - strtotime($objacc->admission_dtetime);

					$ndays = intval($ndiff / $sec_day);			// No. of days of stay.
					$nhrs = $ndiff % $sec_day;					// Excess hours.
					$nhrs = intval($nhrs / $sec_hour);
				}
				else {
					$ndays = $objacc->getActualDays();
					$nhrs  = $objacc->getExcessHrs();
				}

				if ($indx < $indx_cnt) {
					if ($this->accommodation_hist[$indx]->getSource() != $objacc->getSource()) {
						$nhrs += $hrs_accum;
						if ($nhrs > $this->cutoff_hrs) {
							$ndays++;
							$nhrs = 0;
						}
						else
							$nhrs = 0;

						$hrs_accum = 0;
					}
					else {
						$hrs_accum += $nhrs;
						$nhrs = 0;
					}
				}
				else {
					$nhrs += $hrs_accum;
					if ($nhrs > $this->cutoff_hrs) {
						$ndays++;
						$nhrs = 0;
					}
					else
						$nhrs = 0;

					$hrs_accum = 0;
				}

				$i = $this->isRoomTypeInHist($objacc->getTypeNr(), $objacc->getSource());
				if ($i > -1) {
					if ($objacc->getSource() == 'AD') {
						$thrs = $this->acc_roomtype_benefits[$i]->excess_hours + $nhrs;

						$ndaystoadd = intval($thrs / 24);
						$nhrs       = $thrs % 24;
						$ndays 	   += $ndaystoadd;

						$this->acc_roomtype_benefits[$i]->days_count   += $ndays;
						$this->acc_roomtype_benefits[$i]->excess_hours  = $nhrs;
					}
					else {
						if (($this->acc_roomtype_benefits[$i]->getRoomRate() == $objacc->getRoomRate()) && !$this->acc_roomtype_benefits[$i]->isDaysDefaulted()) {
							$thrs = $this->acc_roomtype_benefits[$i]->excess_hours + $nhrs;

							$ndaystoadd = intval($thrs / 24);
							$nhrs       = $thrs % 24;
							$ndays 	   += $ndaystoadd;

							$this->acc_roomtype_benefits[$i]->days_count   += $ndays;
							$this->acc_roomtype_benefits[$i]->excess_hours  = $nhrs;
						}
						else
							$this->acc_roomtype_benefits[$i]->addRoomRate($objacc->getRoomRate());
					}

					$this->acc_roomtype_benefits[$i]->computeTotalCoverage($this->getBillAreaDRate('AC'));
				}
				else {
					$objrmtyp = new RoomTypeAccommodation;

					$objrmtyp->setCutoffHrs($this->cutoff_hrs);
					$objrmtyp->setBillDte($this->bill_dte);
					$objrmtyp->setCurrentEncounterNr($this->current_enr);
										$objrmtyp->setPrevEncounterNr($this->prev_encounter_no);
					$objrmtyp->setTypeNr($objacc->getTypeNr());
					$objrmtyp->setDaysCount($ndays);
					$objrmtyp->setExcessHours($nhrs);
					$objrmtyp->setRoomRate($objacc->getRoomRate());
					$objrmtyp->setSource($objacc->getSource());

					$objrmtyp->computeTotalCoverage($this->getBillAreaDRate('AC'));

					$this->acc_roomtype_benefits[] = $objrmtyp;
				}
			}
		}
		else
			$this->errmsg = 'Accommodation history is empty!';
	}

	function getTotalDaysCovered($nhcareid) {
		global $db;

		$ndays = 0;
				$filter = array('','');

				if ($this->prev_encounter_nr != '') $filter[0] = " or sbe.encounter_nr = '$this->prev_encounter_nr'";
				if ($this->prev_encounter_nr != '') $filter[1] = " or suc.encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select sum(tdays) as days_total ".
					"   from ".
					" (select sum(confine_days) as tdays ".
						"    from seg_confinement_tracker as sct inner join seg_billing_encounter as sbe ".
							"       on sct.bill_nr = sbe.bill_nr ".
						"    where (sbe.encounter_nr = '".$this->current_enr."'".$filter[0].") and hcare_id = ".$nhcareid." ".
					" union ".
					" select sum(used_days_covered) as tdays ".
						"    from seg_used_coverage_details as sucd inner join seg_used_coverage as suc ".
							"       on sucd.disclose_id = suc.disclose_id ".
						"    where str_to_date(suc.disclose_dte, '%Y-%m-%d %H:%i:%s') <= '". $this->bill_frmdte ."' ".
							"       and (suc.encounter_nr = '".$this->current_enr."'".$filter[1].") and hcare_id = ".$nhcareid.") as t";
		if ($result = $db->Execute($strSQL))
			if ($result->RecordCount())
				while ($row = $result->FetchRow()) {
					$ndays = $row['days_total'];
				}

		return($ndays);
	}

	function getTotalUsedCoverage($nhcareid, $sBillArea, $sProdClass = '') {
		global $db;

		$total_used = 0;
		$fldname    = '';

		switch ($sBillArea) {
			case 'AC':
				$fldname = "acc";
				break;

			case 'MS':
				if ($sProdClass == 'M')
					$fldname = "med";
				else
					$fldname = "sup";
				break;

			case 'HS':
				$fldname = "srv";
				break;

			case 'OR':
				$fldname = "ops";
				break;

			case 'D1':
			case 'D2':
			case 'D3':
			case 'D4':
				$fldname = strtolower($sBillArea);
				break;

			case 'XC':
				$fldname = "msc";

		}

				$filter = array('','');

				if ($this->prev_encounter_nr != '') $filter[0] = " or sbe.encounter_nr = '$this->prev_encounter_nr'";
				if ($this->prev_encounter_nr != '') $filter[1] = " or suc.encounter_nr = '$this->prev_encounter_nr'";

		$strSQL = "select sum(total_coverage) as used_coverage ".
					"   from ".
					"      (select sum(total_".$fldname."_coverage) as total_coverage ".
						"          from seg_billing_coverage as sbc inner join seg_billing_encounter as sbe ".
					"             on sbc.bill_nr = sbe.bill_nr ".
						"          where str_to_date(sbe.bill_dte, '%Y-%m-%d %H:%i:%s') < '". $this->bill_frmdte ."' ".
					"             and (sbe.encounter_nr = '". $this->current_enr. "'".$filter[0].") and sbc.hcare_id = ".$nhcareid." ".
					"       union ".
					"       select sum(used_".$fldname."_coverage) as total_used ".
						"          from seg_used_coverage_details as sucd inner join seg_used_coverage as suc ".
							"             on sucd.disclose_id = suc.disclose_id ".
						"          where str_to_date(suc.disclose_dte, '%Y-%m-%d %H:%i:%s') <= '". $this->bill_frmdte ."' ".
					"             and (suc.encounter_nr = '". $this->current_enr. "'".$filter[1].") and sucd.hcare_id = ".$nhcareid.") ".
					"   as t";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					if (!is_null($row['used_coverage']))
						$total_used += $row['used_coverage'];
				}
			}
		}

		return($total_used);
	}

	function getConfineBenefits($sBillArea, $sProdClass = '', $nRoleLevel = 0) {
		global $db;

		$totalCoverage = 0;
				$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or si.encounter_nr = '$this->prev_encounter_nr'";
		switch ($sBillArea) {
			case 'AC':
				$this->acc_confine_benefits = array();

				$strSQL = "select ci.hcare_id, firm_id, name, hb.benefit_id, bs.bsked_id ".
							"   from ((care_insurance_firm as ci inner join ".
								"            (select * from seg_hcare_bsked as shb ".
										"                where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
													"                   and (shb.basis = 1) ".
													"                   and (select max(effectvty_dte) as latest ".
													"                           from seg_hcare_bsked as shb2 ".
										"                           where shb2.hcare_id = shb.hcare_id ".
									"                              and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte) as bs on ci.hcare_id = bs.hcare_id) ".
							"            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
							"            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
							"   where hb.bill_area = '". $sBillArea. "' and (si.encounter_nr = '". $this->current_enr. "'".$filter.") ".
							"      and exists (select * from seg_hcare_confinetype as sc ".
													"                     where sc.bsked_id = bs.bsked_id and ".
							"                        sc.confinetype_id = ". $this->confinetype_id. ") ".
													"   order by priority, bs.effectvty_dte desc";
//                        "   order by bs.effectvty_dte desc, priority limit 1";
//						  "   order by priority";

				if ($result = $db->Execute($strSQL)) {
					if ($result->RecordCount()) {
						$nCharge = $this->compTotalAccommodationChrg() * (1 - $this->getBillAreaDRate($sBillArea));
						$actualdays = $this->days_count;

						while ($row = $result->FetchRow()) {
							$nhcare_id   = $row['hcare_id'];		// Insurance id
							$nbenefit_id = $row['benefit_id'];		// Health benefit id
							$nbsked_id   = $row['bsked_id'];

							$this->getHCareSkedPerConfine($nbsked_id, $this->confinetype_id);

							$ndays = $this->days_count;				// Actual days of accommodation.
							$nhrs  = $this->excess_hours;

							// Take into consideration the no. of days already covered previous billings or disclosed ...
							$nprevdays = $this->getTotalDaysCovered($nhcare_id);

							if (($ndays > ($this->skedvalues['dayslimit'] - $nprevdays)) && ($this->skedvalues['dayslimit'] > 0)) {
								$ndays = $this->skedvalues['dayslimit'] - $nprevdays;
								$nhrs  = 0;			// Cannot anymore cover for the extra hours ....
							}

							if ($actualdays <= DAYS_IN_YEAR) {
								// Check if there is a limit in total number of days in a year for this benefit ....
								if (($this->skedvalues['year_dayslimit'] > 0) || ($this->skedvalues['year_dayslimit_alldeps'] > 0)) {
									// .... there is.
									$bPrincipalPID = $this->isPersonPrincipal($nhcare_id);  // Check if admitted patient is principal insurance holder.
									$nprevdays = $this->getPreviousConfineDays(date('Y', $this->bill_dte), $nhcare_id) + $nprevdays;

									if ($bPrincipalPID)
										$nprevdays = $this->skedvalues['year_dayslimit'] - $nprevdays;
									else
										$nprevdays = $this->skedvalues['year_dayslimit_alldeps'] - $nprevdays;

									if ($nprevdays >= 0) {
										if ($ndays >= $nprevdays) {
											$ndays = $nprevdays;
											$nhrs  = 0;			// Cannot anymore cover for the extra hours ....
										}
									}
									else {
										$ndays = 0;
										$nhrs  = 0;			// Cannot anymore cover for the extra hours ....
									}
								}
							}

							$nCoverage = $ndays * $this->skedvalues['rateperday'];
							if ($nhrs > 0) {
								$nCoverage += ($nhrs * ($this->skedvalues['rateperday'] / 24));
							}

							// Take into consideration the coverage already applied in previous billings or disclosed used coverage ...
							$prevCoverage = $this->getTotalUsedCoverage($nhcare_id, $sBillArea, $sProdClass);

							if ((($nCoverage > ($this->skedvalues['amountlimit'] - $prevCoverage)) && ($this->skedvalues['amountlimit'] > 0)) || ($this->skedvalues['rateperday'] == 0)) {
								$nCoverage = $this->skedvalues['amountlimit'] - $prevCoverage;
							}

							// Check if actual charge < prescribed coverage ... if yes, cover only actual charge.
							if ($nCoverage > $nCharge) $nCoverage = $nCharge;

							if ($nCoverage > 0) {
								$objhcare = new HCareCoverage;

								$objhcare->setID($row['hcare_id']);
																$objhcare->setFirmID($row['firm_id']);
								$objhcare->setDesc($row['name']);
								$objhcare->setCoverage($nCoverage);
								$objhcare->setDaysCovered($ndays);
																$objhcare->setAmountLimit(($this->skedvalues['amountlimit'] - $prevCoverage) < 0 ? 0 : ($this->skedvalues['amountlimit'] - $prevCoverage));

								// Add new supply object in collection (array) of the list of applicable benefits based on confinement.
								$this->acc_confine_benefits[] = $objhcare;

								$totalCoverage += $nCoverage;
							}
						}  // while loop ...
					}	// RecordCount() ...
				}	// Execute() ...
				break;

			case 'MS':
				if ($sProdClass == 'M')
					$this->med_confine_benefits = array();
				else
					$this->sup_confine_benefits = array();

/*				$strSQL = "select ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis ".
							"   from ((care_insurance_firm as ci inner join ".
							"            seg_hcare_bsked as bs on ci.hcare_id = bs.hcare_id) ".
							"            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
							"            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
							"   where hb.bill_area = '". $sBillArea. "' and (bs.basis & 1) and si.encounter_nr = '". $this->current_enr. "' ".
							"      and exists (select * from seg_hcare_confinetype as sc ".
							"                 where sc.hcare_id = ci.hcare_id and ".
							"                    sc.benefit_id = hb.benefit_id and ".
							"                    sc.confinetype_id = ". $this->confinetype_id. ") ".
							"   order by priority";*/

				$strSQL = "select ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis, bs.bsked_id ".
							"   from ((care_insurance_firm as ci inner join ".
								"            (select * from seg_hcare_bsked as shb ".
										"                where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
													"                   and (shb.basis & 1) ".
													"                   and (select max(effectvty_dte) as latest ".
													"                           from seg_hcare_bsked as shb2 ".
										"                           where shb2.hcare_id = shb.hcare_id ".
									"                              and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte) as bs on ci.hcare_id = bs.hcare_id) ".
							"            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
							"            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
							"   where hb.bill_area = '". $sBillArea. "' and (si.encounter_nr = '". $this->current_enr. "'".$filter.") ".
							"      and exists (select * from seg_hcare_confinetype as sc ".
													"                     where sc.bsked_id = bs.bsked_id and ".
							"                        sc.confinetype_id = ". $this->confinetype_id. ") ".
													"   order by priority, bs.effectvty_dte desc";
//                          "   order by bs.effectvty_dte desc, priority limit 1";
//						  "   order by priority";

				if ($result = $db->Execute($strSQL)) {
					if ($result->RecordCount()) {
						while ($row = $result->FetchRow()) {
							$nhcare_id   = $row['hcare_id'];		// Insurance id
							$nbenefit_id = $row['benefit_id'];		// Health benefit id
							$nbsked_id   = $row['bsked_id'];

							$this->getHCareSkedPerConfine($nbsked_id, $this->confinetype_id);

														$nCoverage = 0;
							if ($sProdClass == 'M') {
								if ($row['basis'] & 8) {
									$nCoverage = $this->getTotalMedCoverage($nhcare_id);
																		$nCoverage = is_null($nCoverage) ? 0 : $nCoverage;
																}

																if ($this->skedvalues['amountlimit'] > 0) $nCoverage += $this->getTotalMedCharge() * (1 - $this->getBillAreaDRate($sBillArea));
							}
							else {
								if ($row['basis'] & 8) {
									$nCoverage = $this->getTotalSupCoverage($nhcare_id);
																		$nCoverage = is_null($nCoverage) ? 0 : $nCoverage;
																}

								if ($this->skedvalues['amountlimit'] > 0) $nCoverage += $this->getTotalSupCharge() * (1 - $this->getBillAreaDRate($sBillArea));
														}

							// Take into consideration the coverage already applied in previous billings or disclosed used coverage ...
							$prevCoverage = $this->getTotalUsedCoverage($nhcare_id, $sBillArea, $sProdClass);

							if (($nCoverage > ($this->skedvalues['amountlimit'] - $prevCoverage)) && ($this->skedvalues['amountlimit'] > 0))
								$nCoverage = $this->skedvalues['amountlimit'] - $prevCoverage;

							if ($nCoverage > 0) {
								$objhcare = new HCareCoverage;

								$objhcare->setID($row['hcare_id']);
																$objhcare->setFirmID($row['firm_id']);
								$objhcare->setDesc($row['name']);
								$objhcare->setCoverage($nCoverage);
																$objhcare->setAmountLimit((($this->skedvalues['amountlimit'] - $prevCoverage) < 0) ? 0 : ($this->skedvalues['amountlimit'] - $prevCoverage));

								// Add new supply object in collection (array) of the list of applicable benefits based on confinement.
								if ($sProdClass == 'M')
									$this->med_confine_benefits[] = $objhcare;
								else
									$this->sup_confine_benefits[] = $objhcare;

								$totalCoverage += $nCoverage;
							}
						}  // while loop ...
					}	// RecordCount() ...
				}	// Execute() ...
				break;

			case 'HS':
				$this->srv_confine_benefits = array();

/*				$strSQL = "select ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis ".
							"   from ((care_insurance_firm as ci inner join ".
							"            seg_hcare_bsked as bs on ci.hcare_id = bs.hcare_id) ".
							"            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
							"            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
							"   where hb.bill_area = '". $sBillArea. "' and (bs.basis & 1) and si.encounter_nr = '". $this->current_enr. "' ".
							"      and exists (select * from seg_hcare_confinetype as sc ".
							"                 where sc.hcare_id = ci.hcare_id and ".
							"                    sc.benefit_id = hb.benefit_id and ".
							"                    sc.confinetype_id = ". $this->confinetype_id. ") ".
							"   order by priority";*/

				$strSQL = "select ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis, bs.bsked_id ".
							"   from ((care_insurance_firm as ci inner join ".
								"            (select * from seg_hcare_bsked as shb ".
										"                where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
													"                   and (shb.basis & 1) ".
													"                   and (select max(effectvty_dte) as latest ".
													"                           from seg_hcare_bsked as shb2 ".
										"                           where shb2.hcare_id = shb.hcare_id ".
									"                              and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte) as bs on ci.hcare_id = bs.hcare_id) ".
							"            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
							"            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
							"   where hb.bill_area = '". $sBillArea. "' and (si.encounter_nr = '". $this->current_enr. "'".$filter.") ".
							"      and exists (select * from seg_hcare_confinetype as sc ".
													"                     where sc.bsked_id = bs.bsked_id and ".
							"                        sc.confinetype_id = ". $this->confinetype_id .") ".
													"   order by priority, bs.effectvty_dte desc";
//                          "   order by bs.effectvty_dte desc, priority limit 1";
//						  "   order by priority";

				if ($result = $db->Execute($strSQL)) {
					if ($result->RecordCount()) {
						while ($row = $result->FetchRow()) {
							$nhcare_id   = $row['hcare_id'];		// Insurance id
							$nbenefit_id = $row['benefit_id'];		// Health benefit id
							$nbsked_id   = $row['bsked_id'];

							$this->getHCareSkedPerConfine($nbsked_id, $this->confinetype_id);

														$nCoverage = 0;
							if ($row['basis'] & 8) {
								$nCoverage = $this->getTotalSrvCoverage($nhcare_id);
																$nCoverage = is_null($nCoverage) ? 0 : $nCoverage;
							}

														if (($row['basis'] & 1) && ($this->skedvalues['amountlimit'] > 0)) {
								$nCoverage += $this->getTotalSrvCharge() * (1 - $this->getBillAreaDRate($sBillArea));
							}

							// Take into consideration the coverage already applied in previous billings or disclosed used coverage ...
							$prevCoverage = $this->getTotalUsedCoverage($nhcare_id, $sBillArea, $sProdClass);

							if (($nCoverage > ($this->skedvalues['amountlimit'] - $prevCoverage)) && ($this->skedvalues['amountlimit'] > 0))
								$nCoverage = $this->skedvalues['amountlimit'] - $prevCoverage;

							if ($nCoverage > 0) {
								$objhcare = new HCareCoverage;

								$objhcare->setID($row['hcare_id']);
																$objhcare->setFirmID($row['firm_id']);
								$objhcare->setDesc($row['name']);
								$objhcare->setCoverage($nCoverage);
																$objhcare->setAmountLimit(($this->skedvalues['amountlimit'] - $prevCoverage) < 0 ? 0 : ($this->skedvalues['amountlimit'] - $prevCoverage));

								// Add new supply object in collection (array) of the list of applicable benefits based on confinement.
								$this->srv_confine_benefits[] = $objhcare;

								$totalCoverage += $nCoverage;
							}
						}  // while loop ...
					}	// RecordCount() ...
				}	// Execute() ...
				break;

			case 'OR':
				$this->ops_confine_benefits = array();

/*				$strSQL = "select ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis ".
							"   from ((care_insurance_firm as ci inner join ".
							"            seg_hcare_bsked as bs on ci.hcare_id = bs.hcare_id) ".
							"            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
							"            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
							"   where hb.bill_area = '". $sBillArea. "' and (bs.basis & 1 or bs.basis & 4) and si.encounter_nr = '". $this->current_enr. "' ".
							"      and exists (select * from seg_hcare_confinetype as sc ".
							"                 where sc.hcare_id = ci.hcare_id and ".
							"                    sc.benefit_id = hb.benefit_id and ".
							"                    sc.confinetype_id = ". $this->confinetype_id. ") ".
							"   order by priority";*/

				$strSQL = "select ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis, bs.bsked_id ".
								"   from ((care_insurance_firm as ci inner join ".
								"            (select * from seg_hcare_bsked as shb ".
										"                where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
													"                   and (shb.basis & 1 or shb.basis & 4) ".
													"                   and (select max(effectvty_dte) as latest ".
													"                           from seg_hcare_bsked as shb2 ".
										"                           where shb2.hcare_id = shb.hcare_id ".
									"                              and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte) as bs on ci.hcare_id = bs.hcare_id) ".
													"            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
													"            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
													"   where hb.bill_area = '". $sBillArea. "' and (si.encounter_nr = '". $this->current_enr. "'".$filter.") ".
													"      and (exists (select * from seg_hcare_confinetype as sc ".
													"                     where sc.bsked_id = bs.bsked_id and ".
													"                     sc.confinetype_id = ". $this->confinetype_id. ") or ".
													"           exists (select * from seg_hcare_rvurange as shr ".
													"                     where shr.bsked_id = bs.bsked_id)) ".
													"   order by priority, bs.effectvty_dte desc";
//                          "   order by bs.effectvty_dte desc, priority limit 1";
//                          "   order by priority";

				if ($result = $db->Execute($strSQL)) {
					if ($result->RecordCount()) {
						$this->total_RVU = $this->getTotalRVU();

						while ($row = $result->FetchRow()) {
							$nhcare_id   = $row['hcare_id'];		// Insurance id
							$nbenefit_id = $row['benefit_id'];		// Health benefit id
							$nbsked_id   = $row['bsked_id'];

							if ($row['basis'] & 4)
								$this->getHCareSkedPerRVURange($nbsked_id, $this->total_RVU);
							else
								$this->getHCareSkedPerConfine($nbsked_id, $this->confinetype_id);

														// Take into consideration the coverage already applied in previous billings or disclosed used coverage ...
														$prevCoverage = $this->getTotalUsedCoverage($nhcare_id, $sBillArea, $sProdClass);

														$nCoverage = 0;
							if ($row['basis'] & 8) {
								$nCoverage = $this->getTotalOpCoverage($nhcare_id);
																$nCoverage = is_null($nCoverage) ? 0 : $nCoverage;
														}
							#else
														if (($this->skedvalues['amountlimit'] > 0) || (isset($this->skedvalues['fixedamount']) && ($this->skedvalues['fixedamount'] > 0)) ||
																 (isset($this->skedvalues['minamount']) && ($this->skedvalues['minamount'] > 0))) {
																 $nCoverage += $this->getTotalOpCharge() * (1 - $this->getBillAreaDRate($sBillArea));
														}

														if (isset($this->skedvalues['fixedamount'])) {
																if ($this->skedvalues['fixedamount'] > 0) $nCoverage = $this->skedvalues['fixedamount'] - $prevCoverage;
														}
														else {
																if (isset($this->skedvalues['minamount'])) {
																		if (($this->skedvalues['minamount'] > 0) && ($nCoverage < $this->skedvalues['minamount']))
																				$nCoverage = $this->skedvalues['minamount']  - $prevCoverage;
																}
														}

							if ($this->skedvalues['rateperRVU'] > 0) {
								$nTmpCoverage = $this->skedvalues['rateperRVU'] * $this->total_RVU;

																if (($nTmpCoverage > $this->skedvalues['limit_rvubased']) && ($this->skedvalues['limit_rvubased'] > 0))
																		$nTmpCoverage = $this->skedvalues['limit_rvubased'];

																if ($this->skedvalues['amountlimit'] <= 0) $nCoverage += $this->getTotalOpCharge() * (1 - $this->getBillAreaDRate($sBillArea));
								if ($nCoverage > $nTmpCoverage)
									$nCoverage = $nTmpCoverage;
							}

							if (($nCoverage > ($this->skedvalues['amountlimit'] - $prevCoverage)) && ($this->skedvalues['amountlimit'] > 0))
								$nCoverage = $this->skedvalues['amountlimit'] - $prevCoverage;

							if ($nCoverage > 0) {
								$objhcare = new HCareCoverage;

								$objhcare->setID($row['hcare_id']);
																$objhcare->setFirmID($row['firm_id']);
								$objhcare->setDesc($row['name']);
								$objhcare->setCoverage($nCoverage);
																$objhcare->setAmountLimit(($this->skedvalues['amountlimit'] - $prevCoverage) < 0 ? 0 : ($this->skedvalues['amountlimit'] - $prevCoverage));

								// Add ops (operation procedure) object in collection (array) of the list of applicable benefits based on confinement.
								$this->ops_confine_benefits[] = $objhcare;

								$totalCoverage += $nCoverage;
							}
						}  // while loop ...
					}	// RecordCount() ...
				}	// Execute() ...
				break;

			case 'D1':
			case 'D2':
			case 'D3':
			case 'D4':
				$this->pfs_confine_benefits[$sBillArea] = array();

				$b_noRVU = false;

/*				$strSQL = "select ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis ".
							"   from ((care_insurance_firm as ci inner join ".
							"            seg_hcare_bsked as bs on ci.hcare_id = bs.hcare_id) ".
							"            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
							"            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
							"   where hb.bill_area = '". $sBillArea. "' and (bs.basis & 1 or bs.basis & 4) and si.encounter_nr = '". $this->current_enr. "' ".
							"      and exists (select * from seg_hcare_confinetype as sc ".
							"                 where sc.hcare_id = ci.hcare_id and ".
							"                    sc.benefit_id = hb.benefit_id and ".
							"                    sc.confinetype_id = ". $this->confinetype_id. ") ".
							"   order by priority";*/

								$stmp   = ($nRoleLevel == 0) ? "" : " and hb.level = ".$nRoleLevel;
				$strSQL = "select ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis, bs.bsked_id ".
							"   from ((care_insurance_firm as ci inner join ".
								"            (select * from seg_hcare_bsked as shb ".
										"                where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
													"                   and (shb.basis & 1 or shb.basis & 4) ".
													"                   and (select max(effectvty_dte) as latest ".
													"                           from seg_hcare_bsked as shb2 ".
										"                           where shb2.hcare_id = shb.hcare_id ".
									"                              and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte) as bs on ci.hcare_id = bs.hcare_id) ".
							"            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
							"            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
							"   where hb.bill_area = '". $sBillArea."'".$stmp." and (si.encounter_nr = '". $this->current_enr. "'".$filter.") ".
							"      and (exists (select * from seg_hcare_confinetype as sc ".
													"                     where sc.bsked_id = bs.bsked_id and ".
							"                        sc.confinetype_id = ". $this->confinetype_id. ") or ".
													"           exists (select * from seg_hcare_rvurange as shr ".
													"                     where shr.bsked_id = bs.bsked_id)) ".
													"   order by priority, bs.effectvty_dte desc";
//                          "   order by bs.effectvty_dte desc, priority limit 1";
//						  "   order by priority";

				if ($result = $db->Execute($strSQL)) {
					if ($result->RecordCount()) {
						$this->getTotalPFParams($pf_Days, $pf_RVU, $nCoverage, $sBillArea, $nRoleLevel);

						$nCoverage *= (1 - $this->getBillAreaDRate($sBillArea));

						while ($row = $result->FetchRow()) {
							$nhcare_id   = $row['hcare_id'];		// Insurance id
							$nbenefit_id = $row['benefit_id'];		// Health benefit id
							$nbsked_id   = $row['bsked_id'];

							if ($row['basis'] & 4)
								$this->getHCareSkedPerRVURange($nbsked_id, $pf_RVU);
							else
								$this->getHCareSkedPerConfine($nbsked_id, $this->confinetype_id);

							if (array_key_exists('rateperday', $this->skedvalues))
								if (($this->skedvalues['rateperday'] > 0) && ($pf_Days > 0)) {
									$nTmpCoverage = $this->skedvalues['rateperday'] * $pf_Days;

									if ($nCoverage > $nTmpCoverage)
										$nCoverage = $nTmpCoverage;

									$b_noRVU = true;
								}

							if (!$b_noRVU)
								if (($this->skedvalues['rateperRVU'] > 0) && ($pf_RVU > 0)) {
																		if ($this->skedvalues['rateperRVU'] < 1)
											$nTmpCoverage = $this->skedvalues['rateperRVU'] * $pf_RVU * $this->pcf;
																		else
																				$nTmpCoverage = $this->skedvalues['rateperRVU'] * $pf_RVU;

																		if (($nTmpCoverage > $this->skedvalues['limit_rvubased']) && ($this->skedvalues['limit_rvubased'] > 0))
																				$nTmpCoverage = $this->skedvalues['limit_rvubased'];

									if ($nCoverage > $nTmpCoverage)
										$nCoverage = $nTmpCoverage;
								}

							// Take into consideration the coverage already applied in previous billings or disclosed used coverage ...
							$prevCoverage = $this->getTotalUsedCoverage($nhcare_id, $sBillArea, $sProdClass);

														if (isset($this->skedvalues['fixedamount'])) {
																if ($this->skedvalues['fixedamount'] > 0) $nCoverage = $this->skedvalues['fixedamount'] - $prevCoverage;
														}
														else {
																if (isset($this->skedvalues['minamount'])) {
																		if (($this->skedvalues['minamount'] > 0) && ($nCoverage < $this->skedvalues['minamount']))
																				$nCoverage = $this->skedvalues['minamount']  - $prevCoverage;
																}
														}

							if (($nCoverage > ($this->skedvalues['amountlimit'] - $prevCoverage)) && ($this->skedvalues['amountlimit'] > 0)) {
								$nCoverage = $this->skedvalues['amountlimit'] - $prevCoverage;
							}

							if ($nCoverage > 0) {
								$objhcare = new HCareCoverage;

								$objhcare->setID($row['hcare_id']);
																$objhcare->setFirmID($row['firm_id']);
								$objhcare->setDesc($row['name']);
								$objhcare->setCoverage($nCoverage);
																$objhcare->setAmountLimit(($this->skedvalues['amountlimit'] - $prevCoverage) < 0 ? 0 : ($this->skedvalues['amountlimit'] - $prevCoverage));

								// Add professional fees object in collection (array) of the list of applicable benefits based on confinement or RVU range.
								$this->pfs_confine_benefits[$sBillArea][] = $objhcare;

								$totalCoverage += $nCoverage;
							}
						}  // while loop ...
					}	// RecordCount() ...
				}	// Execute() ...

				break;

			case 'XC':
				$this->msc_confine_benefits = array();

				$strSQL = "select ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis, bs.bsked_id ".
							"   from ((care_insurance_firm as ci inner join ".
								"            (select * from seg_hcare_bsked as shb ".
										"                where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
													"                   and (shb.basis & 1) ".
													"                   and (select max(effectvty_dte) as latest ".
													"                           from seg_hcare_bsked as shb2 ".
										"                           where shb2.hcare_id = shb.hcare_id ".
									"                              and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte) as bs on ci.hcare_id = bs.hcare_id) ".
							"            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
							"            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
							"   where hb.bill_area = '". $sBillArea. "' and (si.encounter_nr = '". $this->current_enr. "'".$filter.") ".
							"      and exists (select * from seg_hcare_confinetype as sc ".
													"                     where sc.bsked_id = bs.bsked_id and ".
							"                        sc.confinetype_id = ". $this->confinetype_id .") ".
													"   order by priority, bs.effectvty_dte desc";
//                          "   order by bs.effectvty_dte desc, priority limit 1";
//						  "   order by priority";

				if ($result = $db->Execute($strSQL)) {
					if ($result->RecordCount()) {
						while ($row = $result->FetchRow()) {
							$nhcare_id   = $row['hcare_id'];		// Insurance id
							$nbenefit_id = $row['benefit_id'];		// Health benefit id
							$nbsked_id   = $row['bsked_id'];

							$this->getHCareSkedPerConfine($nbsked_id, $this->confinetype_id);

														$nCoverage = 0;
							if ($row['basis'] & 8) {
								$nCoverage = $this->getTotalMscCoverage($nhcare_id);
																$nCoverage = is_null($nCoverage) ? 0 : $nCoverage;
							}
#							else {

														$nCoverage += $this->getTotalMscCharge() * (1 - $this->getBillAreaDRate($sBillArea));

#							}

							// Take into consideration the coverage already applied in previous billings or disclosed used coverage ...
							$prevCoverage = $this->getTotalUsedCoverage($nhcare_id, $sBillArea, $sProdClass);

							if (($nCoverage > ($this->skedvalues['amountlimit'] - $prevCoverage)) && ($this->skedvalues['amountlimit'] > 0))
								$nCoverage = $this->skedvalues['amountlimit'] - $prevCoverage;

							if ($nCoverage > 0) {
								$objhcare = new HCareCoverage;

								$objhcare->setID($row['hcare_id']);
																$objhcare->setFirmID($row['firm_id']);
								$objhcare->setDesc($row['name']);
								$objhcare->setCoverage($nCoverage);
																$objhcare->setAmountLimit(($this->skedvalues['amountlimit'] - $prevCoverage) < 0 ? 0 : ($this->skedvalues['amountlimit'] - $prevCoverage));

								// Add new supply object in collection (array) of the list of applicable benefits based on confinement.
								$this->msc_confine_benefits[] = $objhcare;

								$totalCoverage += $nCoverage;
							}
						}  // while loop ...
					}	// RecordCount() ...
				}	// Execute() ...
				break;

			default:

		}

		switch ($sBillArea) {
			case 'AC':
				if (!empty($this->acc_roomtype_benefits))
					foreach($this->acc_roomtype_benefits as $objrb) {
							$this->acc_confine_benefits = array_merge($this->acc_confine_benefits, $objrb->available_hplans);
							$totalCoverage += $objrb->getTotalCoverage();
					}
				$this->acc_confine_coverage = $totalCoverage;
				break;

			case 'MS':
				if ($sProdClass == 'M') {
					if (!empty($this->med_product_benefits))
						foreach($this->med_product_benefits as $objmb) {
							$this->med_confine_benefits = array_merge($this->med_confine_benefits, $objmb->available_hplans);
							$totalCoverage += $objmb->getTotalCoverage();
						}
					$this->med_confine_coverage = $totalCoverage;
				}
				else {
					if (!empty($this->sup_product_benefits))
						foreach($this->sup_product_benefits as $objsb) {
							$this->sup_confine_benefits = array_merge($this->sup_confine_benefits, $objsb->available_hplans);
							$totalCoverage += $objsb->getTotalCoverage();
						}
					$this->sup_confine_coverage = $totalCoverage;
				}
				break;

			case 'HS':
				if (!empty($this->hsp_service_benefits)) {
					foreach($this->hsp_service_benefits as $objsrv) {
												if (!empty($objsrv->available_hplans))	{
								$this->srv_confine_benefits = array_merge($this->srv_confine_benefits, $objsrv->available_hplans);
								$totalCoverage += $objsrv->getTotalCoverage();
												}
					}
				}
				$this->srv_confine_coverage = $totalCoverage;
				break;

			case 'OR':
				if (!empty($this->hsp_ops_benefits)) {
					foreach($this->hsp_ops_benefits as $objOp) {
						$this->ops_confine_benefits = array_merge($this->ops_confine_benefits, $objOp->available_hplans);
						$totalCoverage += $objOp->getTotalCoverage();
					}
				}
				$this->ops_confine_coverage = $totalCoverage;
				break;

			case 'D1':
			case 'D2':
			case 'D3':
			case 'D4':
				$this->pfs_confine_coverage[$sBillArea] = $totalCoverage;
				break;

			case 'XC':
				if (!empty($this->hsp_msc_benefits)) {
					foreach($this->hsp_msc_benefits as $objmsc) {
						$this->msc_confine_benefits = array_merge($this->msc_confine_benefits, $objmsc->available_hplans);
						$totalCoverage += $objmsc->getTotalCoverage();
					}
				}
				$this->msc_confine_coverage = $totalCoverage;
				break;

			default:

		}


	}	// .... end of getConfineBenefits

		function getTotalCoverage() {
				$total = 0;
				$total += $this->acc_confine_coverage;
				$total += $this->med_confine_coverage;
				$total += $this->sup_confine_coverage;
				$total += $this->srv_confine_coverage;
				$total += $this->ops_confine_coverage;

				$total += $this->pfs_confine_coverage['D1'];
				$total += $this->pfs_confine_coverage['D2'];
				$total += $this->pfs_confine_coverage['D3'];
				$total += $this->pfs_confine_coverage['D4'];

				$total += $this->msc_confine_coverage;
				return($total);
		}

	function getTotalBillAmount() {
		$total_bill = 0;

		// Compute total bill of patient's encounter ...
		$total_bill += $this->compTotalAccommodationChrg();			// accommodation
		$total_bill += $this->getTotalSrvCharge();					// hospital services
		$total_bill += $this->getTotalMedCharge();					// medicines
		$total_bill += $this->getTotalSupCharge();					// supplies
		$total_bill += $this->getTotalOpCharge();					// operation
		$total_bill += $this->getTotalMscCharge();					// miscellaneous
		$total_bill += $this->getTotalPFCharge();

		return($total_bill);
	}

//    function correctDiscount() {
//        $total_bill = $this->getTotalBillAmount();
//        $total_cvrg = $this->getTotalCoverage();
//        $total_disc = $this->getTotalDiscount();
//
//        if (($total_bill - $total_cvrg) < $total_disc) {
//            $new_disc = ($total_bill - $total_cvrg) / $total_bill;
//            $this->discounts[0]->setDiscountRate($new_disc);
//        }
//    }

	function getRoundedTotalBillAmount() {
		$total_bill = 0;

		// Compute total bill of patient's encounter ...
		$total_bill += round($this->compTotalAccommodationChrg());			// accommodation
		$total_bill += round($this->getTotalSrvCharge());					// hospital services
		$total_bill += round($this->getTotalMedCharge());					// medicines
		$total_bill += round($this->getTotalSupCharge());					// supplies
		$total_bill += round($this->getTotalOpCharge());					// operation
		$total_bill += round($this->getTotalMscCharge());					// miscellaneous
		$total_bill += round($this->getTotalPFCharge());

		return($total_bill);
	}

	function getDiscounts() {
		global $db;

		// Get discount of classification given to this encounter ...
/*		$strSQL = "select discountid, discountdesc, sum(discount) as tdiscount from ".
					"   (select discountid, discountdesc, discount, '0' as src from ".
					"      (select scg.discountid, discountdesc, scg.discount ".
						"         from seg_charity_grants as scg inner join seg_discount as sd ".
							"            on scg.discountid = sd.discountid ".
						"         where encounter_nr = '". $this->current_enr. "' ".
							"            and str_to_date(grant_dte, '%Y-%m-%d %H:%i:%s') < '".$this->bill_dte."' ".
						"         order by grant_dte desc limit 1) as t ".
					"   union ".
					"   select discountid, discountdesc, sum(discount) as tdiscount, '1' as src ".
						"      from seg_billingapplied_discount as sbd ".
						"      where encounter_nr = '". $this->current_enr. "' and (str_to_date(entry_dte, '%Y-%m-%d %H:%i:%s') >= '".$this->bill_frmdte."' ".
							"         and str_to_date(entry_dte, '%Y-%m-%d %H:%i:%s') < '".$this->bill_dte."') ".
					"      group by discountid, discountdesc) as t2 ".
						"   group by discountid, discountdesc ".
					"   order by discountdesc";
*/
				$filter = '';

				if ($this->prev_encounter_nr != '') $filter = " or scg.encounter_nr = '$this->prev_encounter_nr'";
//		$strSQL = "select scg.discountid, discountdesc, scg.discount ".
//   				  "   from (seg_charity_grants_pid as scg inner join seg_discount as sd ".
//      			  "      on scg.discountid = sd.discountid) inner join care_encounter as ce ".
//                  "      on ce.pid = scg.pid ".
//   				  "   where (ce.encounter_nr = '". $this->current_enr. "'".$filter.") ".
//      			  "      and str_to_date(grant_dte, '%Y-%m-%d %H:%i:%s') < '".$this->bill_dte."' ".
//   				  "   order by grant_dte desc limit 1";

				$strSQL = "select scg.discountid, discountdesc, scg.discount ".
									"   from seg_charity_grants as scg inner join seg_discount as sd ".
									"      on scg.discountid = sd.discountid ".
									"   where (scg.encounter_nr = '". $this->current_enr. "'".$filter.") ".
									"      and str_to_date(grant_dte, '%Y-%m-%d %H:%i:%s') < '".$this->bill_dte."' ".
									"   order by grant_dte desc limit 1";

		if ($result = $db->Execute($strSQL)) {
			$this->discounts = array();

			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$objd = new BillingDiscount;

					$objd->setDiscountID($row['discountid']);
					$objd->setDiscountDesc($row['discountdesc']);
					$objd->setDiscountRate($row['discount']);

					$this->discounts[] = $objd;
				}

//                $this->correctDiscount();       // Correct the discount applied from classification if net due becomes negative ...
			}
		}
	}   // getDiscounts

	function getTotalDiscount() {
		$n_discount = 0;

		if (empty($this->discounts))
			$this->getDiscounts();

		if (!empty($this->discounts)) {
			$i = 1;
			foreach($this->discounts as $objd) {
				if ($i++ == 1)
					$n_discount = $objd->getDiscountRate();
				else
					$n_discount *= $objd->getDiscountRate();
			}
		}

				// Correct the discount applied from classification if net due becomes negative ...
		return(($this->getTotalBillAmount() - $this->getTotalCoverage() - $this->getPreviousPayments()) * $n_discount);
	}	// getTotalDiscount

	function getBillAreaDRate($sbill_area) {
		global $db;

		$n_rate = 0;
				$n_prevrate = 0;

//        if ($this->isCharity()) {
//            switch ($sbill_area) {
//                case 'AC':
//                    if (($n = $this->compTotalAccommodationChrg()) > 0) {
//                        $n_rate = ($n - $this->acc_confine_coverage)/$n;
//                    }
//                    break;
//
//                case 'MS':
//                    if ($sms_class == 'M')
//                        if (($n = $this->getTotalMedCharge()) > 0) {
//                            $n_rate = ($n - $this->med_confine_coverage)/$n;
//                        }
//                    else
//                        if (($n = $this->getTotalSupCharge()) > 0) {
//                            $n_rate = ($n - $this->sup_confine_coverage)/$n;
//                        }
//                    break;
//
//                case 'HS':
//                    if (($n = $this->getTotalSrvCharge()) > 0) {
//                        $n_rate = ($n - $this->srv_confine_coverage)/$n;
//                    }
//                    break;
//
//                case 'OR':
//                    if (($n = $this->getTotalOpCharge()) > 0) {
//                        $n_rate = ($n - $this->ops_confine_coverage)/$n;
//                    }
//                    break;
//
//                case 'D1':
//                case 'D2':
//                case 'D3':
//                case 'D4':
//                    $this->getTotalPFParams($ndays, $nrvu, $npf, $sbill_area);
//                    if ($npf > 0) {
//                        $n_rate = ($npf - $this->pfs_confine_coverage[$sbill_area])/$npf;
//                    }
//                    break;
//
//                case 'XC':
//                    if (($n = $this->getTotalMscCharge()) > 0) {
//                        $n_rate = ($n - $this->msc_confine_coverage)/$n;
//                    }
//                    break;
//            }
//        }
//        else {
						// Get discount rate applicable to bill area of current encounter ...
				$strSQL = "select fn_get_bill_discount('". $this->current_enr. "', '". $sbill_area ."', '".$this->bill_dte."') as discount";
				if ($result = $db->Execute($strSQL)) {
					if ($result->RecordCount()) {
						$row = $result->FetchRow();
						if (!is_null($row['discount'])) {
							$n_rate = $row['discount'];
						}
					}
				}

						// .... get discount rate applied to bill area of encounter while at ER, if there is one.
						if ($this->prev_encounter_nr != '') {
								$strSQL = "select fn_get_bill_discount('". $this->prev_encounter_nr. "', '". $sbill_area ."', '".$this->bill_dte."') as discount";
								if ($result = $db->Execute($strSQL)) {
										if ($result->RecordCount()) {
												$row = $result->FetchRow();
												if (!is_null($row['discount'])) {
														$n_prevrate = $row['discount'];
												}
										}
								}
						}

						$n_rate = ($n_rate > $n_prevrate ? $n_rate : $n_prevrate);      // Return the highest discount applied.
//        }
		return($n_rate);
	}

	function getBillAreaDiscount($sbill_area, $sms_class = '') {
		global $db;

		$n_discount = 0;
				$n_prevdiscount = 0;

				$area_array = array('AC', 'D1', 'D2', 'D3', 'D4');
				if ($this->isCharity() && (in_array($sbill_area, $area_array))) {
						switch ($sbill_area) {
								case 'AC':
										$n_discount = $this->compTotalAccommodationChrg() - $this->acc_confine_coverage;
										break;

//                case 'MS':
//                    if ($sms_class == 'M')
//                        $n_discount = $this->getTotalMedCharge() - $this->med_confine_coverage;
//                    else
//                        $n_discount = $this->getTotalSupCharge() - $this->sup_confine_coverage;
//                    break;

//                case 'HS':
//                    $n_discount = $this->getTotalSrvCharge() - $this->srv_confine_coverage;
//                    break;

//                case 'OR':
//                    $n_discount = $this->getTotalOpCharge() - $this->ops_confine_coverage;
//                    break;

								case 'D1':
								case 'D2':
								case 'D3':
								case 'D4':
										$this->getTotalPFParams($ndays, $nrvu, $npf, $sbill_area);
										$n_discount = $npf - $this->pfs_confine_coverage[$sbill_area];
										break;

//                case 'XC':
//                    $n_discount = $this->getTotalMscCharge() - $this->msc_confine_coverage;
//                    break;
						}
				}
				else {
				$strSQL = "select fn_get_bill_discount('". $this->current_enr. "', '". $sbill_area ."', '".$this->bill_dte."') as discount";
				if ($result = $db->Execute($strSQL)) {
					if ($result->RecordCount()) {
						$row = $result->FetchRow();
						if (!is_null($row['discount'])) {
							$n_discount = $row['discount'];
						}
					}
				}

						// .... get discount rate applied to bill area of encounter while at ER, if there is one.
						if ($this->prev_encounter_nr != '') {
								$strSQL = "select fn_get_bill_discount('". $this->prev_encounter_nr. "', '". $sbill_area ."', '".$this->bill_dte."') as discount";
								if ($result = $db->Execute($strSQL)) {
										if ($result->RecordCount()) {
												$row = $result->FetchRow();
												if (!is_null($row['discount'])) {
														$n_prevdiscount = $row['discount'];
												}
										}
								}
						}
						$n_discount = ($n_discount > $n_prevdiscount ? $n_discount : $n_prevdiscount);      // Return the highest discount applied.

				$npf      = 0;
				$ndays    = 0;
				$nrvu     = 0;

				switch ($sbill_area) {
					case 'AC':
						$n_discount *= $this->compTotalAccommodationChrg();
						break;

					case 'MS':
						if ($sms_class == 'M')
							$n_discount *= $this->getTotalMedCharge();
						else
							$n_discount *= $this->getTotalSupCharge();
						break;

					case 'HS':
						$n_discount *= $this->getTotalSrvCharge();
						break;

					case 'OR':
						$n_discount *= $this->getTotalOpCharge();
						break;

					case 'D1':
					case 'D2':
					case 'D3':
					case 'D4':
						$this->getTotalPFParams($ndays, $nrvu, $npf, $sbill_area);
						$n_discount *= $npf;
						break;

					case 'XC':
						$n_discount *= $this->getTotalMscCharge();
						break;
				}
				}
		return($n_discount);
	}

	function getPreviousPayments() {
		global $db;

		$total_payment = 0;

		$this->prev_payments = array();

				$filter = array('','');

		if ($this->prev_encounter_nr != '') $filter[0] = " or sp.encounter_nr = '$this->prev_encounter_nr'";
				if ($this->prev_encounter_nr != '') $filter[1] = " or spd.encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select spr.or_no, or_date, sum(sp.amount_due) as or_amnt ".
					"   from seg_pay as sp inner join ".
					"      (seg_pay_request as spr left join seg_billing_encounter as sbe ".
					"         on spr.ref_no = sbe.bill_nr and spr.ref_source = 'PP') ".
						"      on sp.or_no = spr.or_no " .
					"   where (sp.encounter_nr = '". $this->current_enr. "'".$filter[0].") ".
					"      and (str_to_date(or_date, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"         and str_to_date(or_date, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
					"      and spr.ref_source = 'PP' ".
					"   group by spr.or_no, or_date ".
					" union ".
					"select spd.or_no, or_date, sum(deposit) as or_amnt ".
					"   from seg_pay as sp1 inner join seg_pay_deposit as spd ".
					"      on sp1.or_no = spd.or_no " .
					"   where (spd.encounter_nr = '". $this->current_enr. "'".$filter[1].") ".
					"      and (str_to_date(or_date, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"         and str_to_date(or_date, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
					"   group by spd.or_no, or_date ".
					"   order by or_date";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$objpay = new Payment;

					$objpay->setORNo($row['or_no']);
					$objpay->setORDate($row['or_date']);
					$objpay->setAmountPaid($row['or_amnt']);

					$this->prev_payments[] = $objpay;

					$total_payment += $row['or_amnt'];
				}
			}
		}

		$this->total_prevpayment = $total_payment;

		return($total_payment);
	}

	function getNewBillingNr() {
		global $db;

		$s_bill_nr = "";

		$strSQL = "select fn_get_new_billing_nr() as bill_nr";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow())
					$s_bill_nr = $row['bill_nr'];
			}
		}

		return($s_bill_nr);
	}

	function getActualAccCoverage($nhcare_id, &$ndayscovered) {
		$ntotal        = 0;
		$nprevcoverage = 0;
		$ndays         = 0;
		$nprevdays     = 0;

		if (!empty($this->acc_confine_benefits))
			foreach($this->acc_confine_benefits as $objhcare) {
				$ncharge = $this->compTotalAccommodationChrg() - $nprevcoverage;
				if ($ncharge > 0) {
					$ncoverage = $objhcare->getCoverage();
					if ($ncharge < $ncoverage)
						$ncoverage = $ncharge;
				}
				else
					$ncoverage = 0;

				$ncovered_days = $this->days_count - $nprevdays;
				if ($ncovered_days > 0) {
					if ($ncovered_days > $objhcare->getDaysCovered())
						$ncovered_days = $objhcare->getDaysCovered();
				}
				else
					$ncovered_days = 0;

				if ($objhcare->getID() == $nhcare_id) {
					$ntotal += $ncoverage;
					$ndays  += 	$ncovered_days;
				}

				$nprevcoverage += $ncoverage;
				$nprevdays     += $ncovered_days;
			}

		$ndayscovered  = $ndays;
		return($ntotal);
	}

	function getActualMedCoverage($nhcare_id) {
//		$ntotal = 0;

//		if (!empty($this->med_confine_benefits))
//			foreach($this->med_confine_benefits as $objhcare) {
//				if ($objhcare->getID() == $nhcare_id)
//					$ntotal += $objhcare->getCoverage();
//			}
//
//		return($ntotal);
				return $this->getAppliedMedsCoverage($nhcare_id);
	}

	function getActualSupCoverage($nhcare_id) {
		$ntotal = 0;

		if (!empty($this->sup_confine_benefits))
			foreach($this->sup_confine_benefits as $objhcare) {
				if ($objhcare->getID() == $nhcare_id)
					$ntotal += $objhcare->getCoverage();
			}

		return($ntotal);
	}

	function getActualSrvCoverage($nhcare_id) {
//		$ntotal = 0;

//		if (!empty($this->srv_confine_benefits))
//			foreach($this->srv_confine_benefits as $objhcare) {
//				if ($objhcare->getID() == $nhcare_id)
//					$ntotal += $objhcare->getCoverage();
//			}

//		return($ntotal);
				return($this->getAppliedHSCoverage($nhcare_id));
	}

	function getActualOpsCoverage($nhcare_id) {
		$ntotal = 0;

		if (!empty($this->ops_confine_benefits))
			foreach($this->ops_confine_benefits as $objhcare) {
				if ($objhcare->getID() == $nhcare_id)
					$ntotal += $objhcare->getCoverage();
			}

		return($ntotal);
	}

	function getActualMscCoverage($nhcare_id) {
		$ntotal = 0;

		if (!empty($this->msc_confine_benefits))
			foreach($this->msc_confine_benefits as $objhcare) {
				if ($objhcare->getID() == $nhcare_id)
					$ntotal += $objhcare->getCoverage();
			}

		return($ntotal);
	}

	function getActualPFCoverage($nhcare_id, $sbill_area = '') {
		$ntotal = 0;

		if ($sbill_area == '') {
			for ($i = 1; $i <= 4; $i++) {
				$sbill_area = "D".$i;

				if (is_array($this->pfs_confine_benefits[$sbill_area])) {
					if (!empty($this->pfs_confine_benefits[$sbill_area]))
						foreach($this->pfs_confine_benefits[$sbill_area] as $objhcare) {
							if ($objhcare->getID() == $nhcare_id)
								$ntotal += $objhcare->getCoverage();
						}
				}
			}
		}
		else {
			if (is_array($this->pfs_confine_benefits[$sbill_area])) {
				if (!empty($this->pfs_confine_benefits[$sbill_area]))
					foreach($this->pfs_confine_benefits[$sbill_area] as $objhcare) {
						if ($objhcare->getID() == $nhcare_id)
							$ntotal += $objhcare->getCoverage();
					}
			}
		}

		return($ntotal);
	}

		function getTotalActualPFCoverage($sbill_area) {

		}

		/**
		* @internal     returns the total applied coverage for X-Ray, Lab and Others.
		* @access       public
		* @author       Bong S. Trazo
		* @package      include
		* @subpackage   care_api_classes
		* @global       db - database object
		*
		* @param        hcare_id - optional, if passed coverage is specific to insurance with hcare id.
		* @return       currency - total applied coverage for X-Ray, Lab and Others.
		*/
		function getAppliedHSCoverage($nhcareid = -1) {
				global $db;

				$srefno = ($this->old_bill_nr == '') ? 'T'.$this->current_enr : $this->old_bill_nr;
				$total  = 0;

				$firm_filter = ($nhcareid == -1) ? "" : " and hcare_id = $nhcareid";
				$strSQL = "select sum(coverage) as totalcoverage
											from seg_applied_coverage
											where ref_no = '$srefno' and source <> 'M'".$firm_filter;

				if ($result = $db->Execute($strSQL)) {
						if ($result->RecordCount()) {
								if ($row = $result->FetchRow())
										$total = (is_null($row['totalcoverage']) || $row['totalcoverage'] == '') ? 0 : $row['totalcoverage'];
						}
				}

				return($total);
		}

		/**
		* @internal     returns the total applied coverage for Drugs and Medicines.
		* @access       public
		* @author       Bong S. Trazo
		* @package      include
		* @subpackage   care_api_classes
		* @global       db - database object
		*
		* @param        hcare_id - optional, if passed coverage is specific to insurance with hcare id.
		* @return       currency - total applied coverage for Drugs and Medicines.
		*/
		function getAppliedMedsCoverage($nhcareid = -1) {
				global $db;

				$srefno = ($this->old_bill_nr == '') ? 'T'.$this->current_enr : $this->old_bill_nr;
				$total  = 0;

				$firm_filter = ($nhcareid == -1) ? "" : " and hcare_id = ".$nhcareid;
				$strSQL = "select sum(coverage) as totalcoverage
											from seg_applied_coverage
											where ref_no = '$srefno' and source = 'M'".$firm_filter;
				if ($result = $db->Execute($strSQL)) {
						if ($result->RecordCount()) {
								if ($row = $result->FetchRow())
										$total = $row['totalcoverage'];
						}
				}

				return($total);
		}

	// This function constructs the array of doctor's charges and claims per applicable health insurance of patient ...
	// ASSUMPTION:  1.  getProfFeesList() has already been called.
	//              2.  getProfFeesBenefits() has already been called.
	//              3.  getConfineBenefits() for bill areas D1 to D4 has already been called.
	function getPerDrPFandClaims($n_pf = 0, $n_pfcoverage = 0) {
		$this->pf_claims = array();

		if (!empty($this->proffees_list)) {
			foreach($this->proffees_list as $objpf) {
				$objclaim = new PFClaim;

				$objclaim->setDrNr($objpf->getDrNr());
				$objclaim->setDrCharge($objpf->getDrCharge());

				// ... compute corresponding claim of doctor.
				if ($n_pf != 0)
					$n_claim = ($objpf->getDrCharge() * $n_pfcoverage) / $n_pf;
				else
					$n_claim = 0;

				$n = round($n_claim, 2);
				if ($n < $n_claim) $n = $n + 0.01;
				if ($n > $objpf->getDrCharge()) $n = $objpf->getDrCharge();

				$objclaim->setDrClaim($n);

				$this->pf_claims[] = $objclaim;
			}
		}
	}

	function getPerHCareCoverage() {
		global $db;

		$ndays = 0;

		$this->hcare_coverage = array();
				$filter = '';

		if ($this->prev_encounter_nr != '') $filter = " or si.encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select distinct ci.hcare_id, firm_id, name ".
						"   from care_insurance_firm as ci ".
						"   where exists (select * from seg_encounter_insurance as si " .
					"                    where (si.encounter_nr = '". $this->current_enr. "'".$filter.") ".
							"                       and si.hcare_id = ci.hcare_id)";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$objhcare = new HCareActualCoverage();

										$n_id = $row['hcare_id'];

					$objhcare->setID($n_id);
					$objhcare->setDesc($row['name']);
					$objhcare->setAccCoverage($this->getActualAccCoverage($n_id, $ndays));
					$objhcare->setMedCoverage($this->getAppliedMedsCoverage($n_id));
					$objhcare->setSupCoverage($this->getActualSupCoverage($n_id));
					$objhcare->setSrvCoverage($this->getActualSrvCoverage($n_id));
					$objhcare->setOpsCoverage($this->getActualOpsCoverage($n_id));
					$objhcare->setD1Coverage($this->getActualPFCoverage($n_id, 'D1'));
					$objhcare->setD2Coverage($this->getActualPFCoverage($n_id, 'D2'));
					$objhcare->setD3Coverage($this->getActualPFCoverage($n_id, 'D3'));
					$objhcare->setD4Coverage($this->getActualPFCoverage($n_id, 'D4'));
					$objhcare->setMscCoverage($this->getActualMscCoverage($n_id));
					$objhcare->setDaysCovered($ndays);

					$this->hcare_coverage[] = $objhcare;
				}
			}
		}
	}

		function isPFClaimExists($hcare_id, $dr_nr) {
				if (!empty($this->pf_claims_per_hcare))
						foreach($this->pf_claims_per_hcare as $objclaim) {
								if (($objclaim->getID() == $hcare_id) && ($objclaim->getDrNr() == $dr_nr)) {
										return $objclaim;
								}
						}
				return false;
		}

	function getPerDrClaimPerHCare($n_pf = 0) {
		$this->pf_claims_per_hcare = array();

		if (!empty($this->hcare_coverage)) {
			foreach($this->hcare_coverage as $objhcare) {
				if (!empty($this->pf_claims)) {
					foreach($this->pf_claims as $objpf) {
												$objclaim = $this->isPFClaimExists($objhcare->getID(), $objpf->getDrNr());
												if (!$objclaim) {
														$objclaim = new PFClaimPerHCare;
								$objclaim->setID($objhcare->getID());
								$objclaim->setDesc($objhcare->getDesc());
								$objclaim->setDrNr($objpf->getDrNr());

														$n_charge = 0;
														$n_claim  = 0;

														$bExists = false;
												}
												else {
														$n_charge = $objclaim->getDrCharge();
														$n_claim  = $objclaim->getDrClaim();

														$bExists = true;
												}

						if ($n_pf != 0) {
														// Review this computation ...
														$n = $this->getActualPFCoverage($objhcare->getID());
														if ($n == 0)
																$n_charge = $objpf->getDrCharge();
														else
									$n_charge += ($objpf->getDrCharge() * $n) / $n_pf;
							$n_claim  += ($objpf->getDrClaim()  * $n) / $n_pf;
						}

						$n = round($n_charge, 2);
						if ($n < $n_charge) $n = $n + 0.01;
						if ($n > $objpf->getDrCharge()) $n = $objpf->getDrCharge();
						$objclaim->setDrCharge($n);

						$n = round($n_claim, 2);
						if ($n < $n_claim) $n = $n + 0.01;
						if ($n > $objpf->getDrClaim()) $n = $objpf->getDrClaim();
						$objclaim->setDrClaim($n);

												if (!$bExists) $this->pf_claims_per_hcare[] = $objclaim;
					}
				}
			}
		}
	}

	function getPIDinEncounter() {
		global $db;

		$s_pid = "";
				$filter = '';

		if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select pid " .
					"   from care_encounter ".
					"   where (encounter_nr = '". $this->current_enr. "'".$filter.")";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow())
					$s_pid = $row['pid'];
			}
		}

		return($s_pid);
	}

	function getPrincipalPIDofHCare($s_pid, $nhcareid) {
		global $db;

		$sprincipal_pid = "";

		$strSQL = "select pid ".
					"   from care_person_insurance as cpi0 ".
					"   where exists (select * from care_person_insurance as cpi1 ".
					"                    where cpi1.pid = '". $s_pid ."' and cpi1.hcare_id = ". $nhcareid ." ".
					"                       and cpi1.pid <> cpi0.pid and cpi1.hcare_id = cpi0.hcare_id ".
					"      and cpi1.insurance_nr = cpi0.insurance_nr) ".
					"      and cpi0.is_principal <> 0";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow())
					$sprincipal_pid = $row['pid'];
			}
		}

		return($sprincipal_pid);
	}

		function saveConfinementType() {
				global $db;

				$bSuccess = false;
				$bill_dte = $this->bill_dte;

				$this->sql = "select * from seg_encounter_confinement ".
										 "   where str_to_date(classify_dte, '%Y-%m-%d %H:%i:%s') < '" . $bill_dte ."' and ".
										 "      encounter_nr = '$this->current_enr' ".
										 "   order by classify_dte desc limit 1 for update";

				if($result = $db->Execute($this->sql)){
						if ($result->RecordCount()){
								// update data to seg_encounter_confinement
								$row = $result->FetchRow();
								$classify_dte = $row['classify_dte'];
								$confine_id   = $row['confinetype_id'];

//                $strSQL = "select * from seg_encounter_confinement where encounter_nr = ''";
//                $result = $db->Execute($this->sql);

								if (strcmp($bill_dte, "0000-00-00 00:00:00") == 0)
										$n_classify_dte = date('Y-m-d H:i:s');
								else
										$n_classify_dte = $bill_dte;
								$n_classify_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($n_classify_dte)));

								$this->sql = "update seg_encounter_confinement SET confinetype_id = ".$this->confinetype_id.",
																 classify_id = '".$_SESSION['sess_user_name']."',
																 classify_dte = '".$n_classify_dte."'
																 WHERE encounter_nr = '".$this->current_enr."'
																		and confinetype_id = ". $confine_id ."
																		and classify_dte = '". $classify_dte ."'";
								$bSuccess = $this->Transact($this->sql);
						}else{
								//Insert new data to seg_encounter_confinement
								if (strcmp($bill_dte, "0000-00-00 00:00:00") == 0) {
										$classify_dte = date('Y-m-d H:i:s');
										$create_time = date('Y-m-d H:i:s');
								}
								else {
										$classify_dte = $bill_dte;
										$create_time  = $bill_dte;
								}
								$classify_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($classify_dte)));
								$create_time = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($create_time)));

								$this->sql = "INSERT INTO seg_encounter_confinement(encounter_nr, confinetype_id, classify_id, classify_dte, create_id, create_time) ".
														 "   VALUES('".$this->current_enr."', ".$this->confinetype_id.", '".$_SESSION['sess_user_name']."' , '".$classify_dte."' , '".$_SESSION['sess_user_name']."','".$create_time."')";
								$bSuccess = $this->Transact($this->sql);
						}
				}

				return $bSuccess;
		}

	// This function saves the billing info ...
	// ASSUMPTION:  1.  All the functions that extract the accommodation, medicines, supplies, doctors' fees and
	//			        other transactions of patient have been called.
	//				2.  getTotalDiscount() has been called.
	//			    3.  getPreviousPayments() has been called.
	function saveBilling() {
		global $db;

		$bSuccess = false;
		$sbill_nr = "";

		if (!isset($this->confinetype_id)) {
			$this->errmsg = "System cannot save a billing without the case type set!";
			return(FALSE);
		}

		// Compute total doctors' fees ...
		$total_df = $this->getTotalPFCharge();

				// Save confinement type derived from the ICD in care_encounter_diagnosis ...
				$bSuccess = $this->saveConfinementType();

//        $this->startTrans();
				$db->StartTrans();

		$this->getAccommodationType();

		if ($this->old_bill_nr == '') {
			$sbill_nr = $this->getNewBillingNr();

			// i.e new billing ... no previous saved billing.
			$strSQL = "insert into seg_billing_encounter (bill_nr, bill_dte, bill_frmdte, encounter_nr, accommodation_type, total_acc_charge, total_med_charge, ".
								"      total_sup_charge, total_srv_charge, total_ops_charge, total_doc_charge, total_msc_charge, total_prevpayments, applied_hrs_cutoff, is_final) " .
								"   values ('".$sbill_nr."', '".$this->bill_dte."', '".$this->bill_frmdte."', '".$this->current_enr."', ".$this->accomm_typ_nr.", ".
						"           ".$this->compTotalAccommodationChrg().", ".$this->getTotalMedCharge().", ".$this->getTotalSupCharge().", ".
						"           ".$this->getTotalSrvCharge().", ".$this->getTotalOpCharge().", ".$total_df.", ".$this->getTotalMscCharge().", ".$this->total_prevpayment.", ".$this->cutoff_hrs.", ".
											"           ".($this->bfinal ? 1 : 0).")";
		}
		else {
			$sbill_nr = $this->old_bill_nr;

			// i.e. edit previously saved billing.
			$strSQL = "update seg_billing_encounter set " .
						"   bill_dte           = '". $this->bill_dte ."', " .
						"   bill_frmdte        = '". $this->bill_frmdte ."', " .
						"   accommodation_type =  ". $this->accomm_typ_nr . ", ".
						"   total_acc_charge   =  ". $this->compTotalAccommodationChrg() .", " .
						"   total_med_charge   =  ". $this->getTotalMedCharge() .", " .
						"	  total_sup_charge   =  ". $this->getTotalSupCharge() .", " .
						"   total_srv_charge   =  ". $this->getTotalSrvCharge() .", " .
						"   total_ops_charge   =  ". $this->getTotalOpCharge()  .", " .
						"   total_doc_charge   =  ". $total_df .", " .
						"   total_msc_charge   =  ". $this->getTotalMscCharge() .", " .
						"   total_prevpayments =  ". $this->total_prevpayment .", " .
						"   applied_hrs_cutoff =  ". $this->cutoff_hrs .", ".
											"   is_final           =  ". ($this->bfinal ? 1 : 0) ." ".
						"   where bill_nr      = '". $this->old_bill_nr ."'";
		}
		$bSuccess = $db->Execute($strSQL);
				if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot insert or update billing for encounter ".$this->current_enr."."."\n".$strSQL;

				if ($bSuccess) {
				// Update the confinement tracker table ...
			$strSQL = "delete from seg_confinement_tracker where bill_nr = '". $sbill_nr ."'";
			$bSuccess = $db->Execute($strSQL);

				if ($bSuccess) {
					if ($this->old_bill_nr != '') {
						$strSQL = "delete from seg_billing_coverage where bill_nr = '". $sbill_nr ."'";
						$bSuccess = $db->Execute($strSQL);

						if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot delete existing seg_billing_coverage of encounter ".$this->current_enr.".";
					}
						}
						else
								$this->errmsg = $db->ErrorMsg().".\nERROR: Cannot delete tracking of confinement in seg_confinement_tracker for encounter ".$this->current_enr.".";
				}

		if ($bSuccess) {
			$this->getPerHCareCoverage();		// Get the actual coverage of all billable areas per health insurance.
			$s_pid = $this->getPIDinEncounter();

			if (!empty($this->hcare_coverage)) {
				foreach($this->hcare_coverage as $objhcare) {
					$strSQL = "insert into seg_billing_coverage (bill_nr, hcare_id, total_acc_coverage, total_med_coverage, total_sup_coverage, ".
								"                                  total_srv_coverage, total_ops_coverage, total_d1_coverage, total_d2_coverage, ".
								"                                  total_d3_coverage, total_d4_coverage, total_msc_coverage) " .
								"   values ('".$sbill_nr."', ".$objhcare->getID().", ".$objhcare->getAccCoverage().", ".$objhcare->getMedCoverage().", ".
								"            ".$objhcare->getSupCoverage().", ".$objhcare->getSrvCoverage().", ".$objhcare->getOpsCoverage().", ".
								"            ".$objhcare->getD1Coverage().", ".$objhcare->getD2Coverage().", ".$objhcare->getD3Coverage().", ".
								"            ".$objhcare->getD4Coverage().", ".$objhcare->getMscCoverage().")";
					$bSuccess = $db->Execute($strSQL);

					if (!$bSuccess) break;

					if (!$this->isPersonPrincipal($objhcare->getID()))
						$sprincipal_pid = $this->getPrincipalPIDofHCare($s_pid, $objhcare->getID());
					else
						$sprincipal_pid = "";

					$strSQL = "insert into seg_confinement_tracker (pid, current_year, bill_nr, hcare_id, confine_days, principal_pid) " .
								"   values ('". $s_pid . "', ". strftime("%Y", strtotime($this->bill_dte)) .", '". $sbill_nr ."', ". $objhcare->getID() .", ".
								"            ". $objhcare->getDaysCovered() .", '". $sprincipal_pid ."')";
					$bSuccess = $db->Execute($strSQL);

					if (!$bSuccess) break;
				}

				if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot insert billing coverge or confinement tracker of encounter ".$this->current_enr.".\n".$strSQL;
			}
		}

				if ($bSuccess) {
						// Update the reference no. in seg_applied_coverage ...
						$strSQL = "update seg_applied_coverage set
													ref_no = $sbill_nr
													where ref_no = 'T".$this->current_enr."'";
						$bSuccess = $db->Execute($strSQL);
						if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot update applied coverage for meds, x-ray, lab or others for encounter ".$this->current_enr."."."\n".$strSQL;
				}

		if ($bSuccess)
			if ($this->old_bill_nr != '') {
				$strSQL = "delete from seg_billing_discount where bill_nr = '". $sbill_nr ."'";
				$bSuccess = $db->Execute($strSQL);

				if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot delete existing billing discount of encounter ".$this->current_enr.".";
			}

		if ($bSuccess) {
			// Save the discount applied ...
			if (!empty($this->discounts)) {
				foreach($this->discounts as $objdiscount) {
					$strSQL = "insert into seg_billing_discount (bill_nr, discountid, discount) " .
								"   values ('". $sbill_nr ."', '". $objdiscount->getDiscountID() ."', ". $objdiscount->getDiscountRate() .")";
					$bSuccess = $db->Execute($strSQL);

					if (!$bSuccess) break;
				}

				if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot insert billing discounts of encounter ".$this->current_enr.".";
			}
		}

		if ($bSuccess)
			if ($this->old_bill_nr != '') {
				$strSQL = "delete from seg_billing_pf where bill_nr = '". $sbill_nr ."'";
				$bSuccess = $db->Execute($strSQL);

				if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot delete existing billing pf of encounter ".$this->current_enr.".";
			}

		if ($bSuccess) {
			$this->getPerDrPFandClaims($total_df, $this->getTotalPFCoverage());		// Get the listing of doctors with corresponding claims.
			$this->getPerDrClaimPerHCare($total_df);								// Get the listing of doctors with corresponding claims per health insurance.

			// Save the professional fees of doctors per health insurance ...
			if (!empty($this->pf_claims_per_hcare)) {
				foreach($this->pf_claims_per_hcare as $objpf) {
					$strSQL = "insert into seg_billing_pf (bill_nr, hcare_id, dr_nr, dr_charge, dr_claim) ".
								"   values ('". $sbill_nr ."', ". $objpf->getID() .", ". $objpf->getDrNr() .", ".
								"            ". $objpf->getDrCharge() .", ". $objpf->getDrClaim() .")";
					$bSuccess = $db->Execute($strSQL);
					if (!$bSuccess) break;
				}

				if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot insert billing pf of encounter ".$this->current_enr.".";
			}
		}

		if (!$bSuccess) $db->FailTrans();
		$db->CompleteTrans();

		return($bSuccess);
	}
}
?>
