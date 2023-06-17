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
*   @author 	   :	Lemuel 'Bong' S. Trazo
*	  @version	   :	1.0
*	  @date created:	July 27, 2007
*	  @date updated:	October 14, 2009
*
*   Modification: a.  Added filter on type_charge for seg_lab_serv and seg_radio_serv charges    -------    10.14.2009
* 								b.  Incorporated changes in computing PF charges from BPH code.								 -------		08.05.2010
* 								c.  Made changes in inner join of seg_pharma_order_items to solve slow query log
* 								    at DMC 		                                                                 -------    09.03.2010
*
* 								d. placed fix for HISSPC-123 issue "(case when is_excluded <> 0 then 0 else spd.days_attended end)"
* 								e. Made changes in inner join of seg_pharma_order_items to solve slow query log ------    05.26.2011
*                    (Fix for issue HISSPMC-176)
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
//added by jasper 04/25/2013 for MS625 APPLY INFIRMARY DISCOUNT
require_once($root_path.'include/care_api_classes/class_encounter.php');

define('ER_PATIENT', 1);
define('OUT_PATIENT', 2);
define('DIALYSIS_PATIENT', 5);
define('WELLBABY', 12); //added by jasper 07/31/2013 FOR BUGZILLA #188 WELLBABY
define('DEFAULT_PCF', 40);
define('CHARITY', 'CHARITY');
define('SPONSORED', '5');
define('CHARITYWARD', 1);
define('NOBALANCEBILLING','NBB');
define('INFIRMARY', 'PHS');
define('SENIORCITIZEN', 'SENIOR'); //added by jasper 07/17/2013 - FOR BUG#120
define('OBANNEX', 'OB-ANNEX'); //added by jasper 07/24/2013 
define('SERVICEWARD', 'SERVICE'); //added by jasper 07/12/2013
define('ANNEXWARD', 'ANNEX'); //added by jasper 07/12/2013
define('ICUWARD','ICU'); //Added by jarel 10/18/2013 for ICU Ward
define('NEWBORN_A', 24);       //added by jasper 09/03/2013 FOR BUG#305
define('NEWBORN_B', 27);       //added by jasper 09/03/2013 FOR BUG#305
define('DEFAULT_NBPKG_RATE', 1750);  //added by jasper 09/04/2013 FOR BUG#305
define('HSM','9'); //Added by Jarel for HSM
define('DEFAULT_NBPKG_NAME','NEW BORN');//Added By Jarel 12/09/2013

define('SKED_EFFECTIVITY','2010-10-07');	  // Constant applicable to DMC only -- by LST --- 10.07.2010
define('ISSRVD_EFFECTIVITY', '2012-10-09');   // Constant applicable to SPMC only: date when is_served is considered in computing
                                              // laboratory and radiology charges -- by LST -- 09.22.2012
                                              // ... changed to 10.09.2012 (added single quotes around ISSRVD_EFFECTIVITY)

class Billing extends Core {
	var $current_enr;
	var $prev_encounter_no;                         // Previous encounter no. -- used for admitted ER patients.
	var $confinetype_id;
	var $confinetype_desc;
	var $encounter_type;                     //added by jasper 09/03/2013 FOR BUG#305

	var $accomm_typ_nr;
	var $accomm_typ_desc;
	var $accomm_ward_name; //added by jasper 07/24/2013 FOR BUGZILLA BUG ID:302
	var $nonDiscountablePF = 0; //added by jasper 09/01/2013 FOR BUGZILLA BUG ID:302

	var $bill_frmdte = "0000-00-00 00:00:00";		// Start date and time covered by this billing.
	var $bill_dte;
	var $tempbill_dte; // Use for dead patient to calculate accomodation Jarel /8/28/2013
	var $isdied = false;

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

	var $total_srv_charge;
	var $total_med_charge;
	//added by jasper 09/12/2013
    var $total_acc_charge;
    var $total_pf_charge;
    var $total_op_charge;
    var $total_misc_charge;
    //added by jasper 09/12/2013

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
	var $acc_confine_benefits;		        // ... accommodation
	var $med_confine_benefits;		        // ... medicines
	var $sup_confine_benefits;		        // ... supplies
	var $srv_confine_benefits;		        // ... hospital services.
	var $ops_confine_benefits;				// ... operation procedure.
	var $pfs_confine_benefits = array();	// ... doctors' fees.
	var $msc_confine_benefits;
	var $pkg_benefits;

	var $acc_confine_coverage;				// Maximum coverage for accommodation.
	var $med_confine_coverage;				// Maximum coverage for medicines.
	var $sup_confine_coverage = 0;		 	// Maximum coverage for supplies.
	var $srv_confine_coverage;				// Maximum coverage for hospital services.
	var $ops_confine_coverage;				// Maximum coverage for operation procedures.
	var $pfs_confine_coverage = array();	// Maximum coverage for doctors' fees.
	var $msc_confine_coverage;

	var $discounts;							// array of applied discounts.

	var $prev_payments;						// array of partial payments (deposits).
        var $ob_payments = array();                       // array for OB co-payments ADDED BY JASPER 08/28/2013 for BUG# 279
	var $total_ob_payments;				  // array for OB co-payments ADDED BY JASPER 10/03/2013 for BUG# 279
	
	var $old_bill_nr = '';

	var $hcare_coverage;					// array of health insurances availed by patient with corresponding total coverage per area.
	var $pf_claims;							// array of prof fees and corresponding claims.
	var $pf_claims_per_hcare;				// array of prof fees and corresponding claims per health insurance.

	var $valid_covered_items;               // array of valid items with coverage applied.
	var $temp_valid_items;					// array of items with coverage applied for further validation.
	var $is_coveredbypkg = false;
	var $package_id = 0;

	var $is_withdeposit = false;

	var $adjusted_coverage = array();       // Array of manually adjusted coverage.

	var $debugSQL = "";

	var $forceCompute = false;              // setting to force recomputation of specific values.

    var $isfreedist = false;

    var $memcategory = '';
    var $memcategoryId = '';
    var $excess; //added by jasper 04/16/2013
    var $ob_amt; //added by jasper 05/30/2013

	function Billing($enr = '', $billdte = "0000-00-00 00:00:00", $frmdte = "0000-00-00 00:00:00", $old_billnr = '', $deathdate = '') {
		$this->current_enr = $enr;
		$this->old_bill_nr = $old_billnr;

		$this->getPrevEncounterNr();    // Get parent encounter no., if there is ...

		if ((strcmp($frmdte, "0000-00-00 00:00:00") == 0) || (trim($frmdte) == '')) {
			$this->bill_frmdte = $this->getLatestBillDte();

			if (strcmp($this->bill_frmdte, "0000-00-00 00:00:00") == 0)
				$this->bill_frmdte = $this->getEncounterDte();

			if (strcmp($this->bill_frmdte, "0000-00-00 00:00:00") == 0)
				$this->bill_frmdte = $this->getActualAdmissionDte();
		}
		else
			$this->bill_frmdte = $frmdte;

		$this->isdied = false;
		if ($deathdate!=''){
			$this->bill_dte = $billdte;
			$this->tempbill_dte = $deathdate;
			$this->isdied = true;
		}elseif(strcmp($billdte, "0000-00-00 00:00:00") != 0){
			$this->bill_dte = $billdte;
			$this->tempbill_dte = $billdte;
		}else{
			$this->bill_dte = strftime("%Y-%m-%d %H:%M:%S");
			$this->tempbill_dte =strftime("%Y-%m-%d %H:%M:%S");
		}
		// Default to current date and time.

		if ($old_billnr != '') {
			$ncutoff = $this->getAppliedHrsCutoff();
			$this->correctBillDates();
		}
		else
			$ncutoff = -1;

		$hosp_obj = new Hospital_Admin();
		$this->cutoff_hrs = ($ncutoff == -1) ? $hosp_obj->getCutoff_Hrs() : $ncutoff;
		$this->pcf = $hosp_obj->getDefinedPCF();
		$this->pcf = ($this->pcf == 0) ? DEFAULT_PCF : $this->pcf;

//        $this->applyPHIPInsurance();    // Apply automatically the PHIP insurance if member ....
		$this->chkIfCoveredByPackage();             // Check if bill is associated with a package ...
		$this->getCoverageAdjustments();
	}

	function getHouseCasePCF() {
		global $db;

		$bhousecase = 0;
//		$strSQL = "select fn_isHouseCase('".$this->current_enr."') as casetype";
    $strSQL = "select fn_isHouseCaseAsOfRefDate('".$this->current_enr."', '".$this->bill_dte."') as casetype";
		if ($result=$db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				if ($row = $result->FetchRow()) {
					 $bhousecase = is_null($row["casetype"]) ? 0 : $row["casetype"];
				}
			}
		}

		if ($bhousecase)
			return DEFAULT_PCF;
		else
			return 0;
	}

	function chkIfCoveredByPackage() {
		global $db;

		$srefno = ($this->old_bill_nr != '') ? $this->old_bill_nr : "T".$this->current_enr;
		$strSQL = "select package_id, is_freedist from seg_billing_pkg where ref_no = '$srefno'";
		if ($result=$db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				if ($row = $result->FetchRow()) {
					$this->package_id = is_null($row["package_id"]) ? 0 : $row["package_id"];
					$this->is_coveredbypkg = ($this->package_id != 0);
                    $this->isfreedist = (!is_null($row["is_freedist"]) && ($row["is_freedist"] != 0));
				}
			}
		}
	}

    function getCaseRatePkgLimit($sBillArea, $issurgical) {
        global $db;

        $sfield = "";
        $share = 0.00;
        if ($sBillArea == 'D3')
            $sfield = "dist_pfsurgeon share";
        elseif ($sBillArea == 'D4')
            $sfield = "dist_pfanesth share";
        elseif (in_array($sBillArea, array('D1','D2'))) {
            $sfield = "dist_pfdaily share";
        }
        else
            $sfield = "dist_hosp share";

        $strSQL = "SELECT $sfield
                    FROM seg_caseratepkgdist
                    WHERE effect_date <= DATE('".$this->bill_dte."')
                       AND case_type = '".(($issurgical) ? 'S' : 'M')."'
                    ORDER BY effect_date DESC LIMIT 1";
		if ($result=$db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				if ($row = $result->FetchRow()) {
                    $share = (is_null($row['share'])) ? 0.00 :  $row['share'];
				}
			}
		}

        return $share;
    }

	function getDischargeDate($bill_dt) {
		global $db;

		$dischrg_date = $bill_dt;
		$strSQL = "select discharge_date, discharge_time
						 from care_encounter
						 where encounter_nr = '$this->current_enr'
							and is_discharged = 1
							and upper(encounter_status) not in ('CANCELLED')";
		if ($result=$db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				if ($row = $result->FetchRow()) {
					if (!is_null($row["discharge_date"]) && !is_null($row["discharge_time"])) {
						$dischrg_date = strftime("%Y-%m-%d", strtotime($row["discharge_date"])). ' '.strftime("%H:%M:%S",  strtotime($row["discharge_time"]));
						$this->is_discharged = true;
					}
				}
			}
		}

		return $dischrg_date;
	}

	function getCoverageAdjustments() {
		global $db;

		$this->adjusted_coverage = array();
		$nhcare_id = 0;

		$srefno = ($this->old_bill_nr != '') ? $this->old_bill_nr : "T".$this->current_enr;
		$strSQL = "select *
					 from seg_billingcoverage_adjustment
					 where ref_no = '$srefno'
					 order by hcare_id, priority";
		if ($result=$db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					if ($nhcare_id != $row["hcare_id"]) {
						$this->adjusted_coverage[$row["hcare_id"]] = array();
						$nhcare_id = $row["hcare_id"];
					}
					$this->adjusted_coverage[$nhcare_id][$row["bill_area"]] = is_null($row["coverage"]) ? 0 : $row["coverage"];
				}
			}
		}
	}

	function hasCoverageAdjustments() {
		return !empty($this->adjusted_coverage);
	}

	function forceEncounterStartDte() {
		$this->bill_frmdte = $this->getEncounterDte();
		if (strcmp($this->bill_frmdte, "0000-00-00 00:00:00") == 0)
			$this->bill_frmdte = $this->getActualAdmissionDte();
	}

	function correctBillDates() {
		global $db;

		if ($this->old_bill_nr != '') {
			$strSQL = "select bill_dte, bill_frmdte from seg_billing_encounter where bill_nr = '$this->old_bill_nr' and is_deleted IS NULL";
			if ($result=$db->Execute($strSQL)) {
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
		if ($result=$db->Execute($strSQL)) {
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

	function setForceCompute($bflag) {
		$this->forceCompute = $bflag;
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

    /***
     *  Function which returns if encounter is medico legal or not.
     *  (Fix for MS-538)
     */
    function isMedicoLegal() {
        global $db;
        //edited by jasper 04/25/2013
        $filter = ($this->prev_encounter_nr != '') ? "('{$this->prev_encounter_nr}'" : "";
        $filter .= (($filter != "") ? "," : "(")."'{$this->current_enr}')";
        //$filter = ($this->prev_encounter_nr != '') ? "'{$this->prev_encounter_nr}'" : "";
        //$filter .= "(".(($filter != "") ? "," : "")."'{$this->current_enr}')";
        $strSQL = "SELECT ".
                  "     is_medico ".
                  "  FROM care_encounter ".
                  "  WHERE encounter_nr IN {$filter} ".
                  "     LIMIT 1";
        $ismedico = false;
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$row = $result->FetchRow();
				$ismedico = ($row['is_medico'] != 0);
			}
		}

		return $ismedico;
    }

    /***
     *  Function which returns if patient during this encounter is PHIC covered or not.
     *  (Fix for MS-538)
     */
    function isPHIC() {
        global $db;

        $ncount = 0;
        //$filter = ($this->prev_encounter_nr != '') ? "'{$this->prev_encounter_nr}'" : "";
        //$filter .= "(".(($filter != "") ? "," : "")."'{$this->current_enr}')";
        //removed by jasper 07/24/2013 - Benefits should only be based on current encounter. not previous encounter.
        //$filter = ($this->prev_encounter_nr != '') ? "('{$this->prev_encounter_nr}'" : "";
        $filter .= (($filter != "") ? "," : "(")."'{$this->current_enr}')";
        $strSQL = "SELECT ".
                  "     COUNT(*) isphic ".
                  "   FROM seg_encounter_insurance ".
                  "   WHERE encounter_nr IN {$filter} ".
                  "      AND hcare_id = ".PHIC_ID.
                  "   ORDER BY priority LIMIT 1";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$row = $result->FetchRow();
				$ncount = $row['isphic'];
			}
		}
		return ($ncount > 0);
    }

	function isInPatient() {
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

		return ($enc_type != ER_PATIENT) && ($enc_type != OUT_PATIENT) && ($enc_type != DIALYSIS_PATIENT);
	}

	function isDialysisPatient() {
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

		return ($enc_type == DIALYSIS_PATIENT);
	}

	// This function returns type of encounter of patient: surgical or non-surgical
	function isSurgicalCase() {
		global $db;

        $flag = 0;
        /*if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
        $strSQL = "select count(*) as pcount
                        from
                    (select 1 as tr_id, os.refno
                        from seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno
                        where (encounter_nr = '". $this->current_enr. "'".$filter.") and is_cash = 0 and upper(trim(os.status)) <> 'DELETED'
                     union
                     select 2 as tr_id, mo.refno
                        from seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno
                        where (encounter_nr = '". $this->current_enr. "'".$filter.")) as t";
        $result = $db->Execute($strSQL);
        if ($result) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $flag = (is_null($row['is_surgical'])) ? 0 : $row['is_surgical'];
            }
        }*/
        //removed by jasper 07/10/2013
        /*$strSQL = "SELECT
                      is_surgical
                    FROM
                      seg_packages
                    WHERE package_id = $this->package_id";*/
        //added by jasper 07/11/2013
        $strSQL = "select count(*) as is_surgical
                        from
                    (select 1 as tr_id, os.refno
                        from seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno
                        where (encounter_nr = '". $this->current_enr. "') and is_cash = 0 and upper(trim(os.status)) <> 'DELETED'
                     union
                     select 2 as tr_id, mo.refno
                        from seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno
                        where (encounter_nr = '". $this->current_enr. "')) as t";

        $row = $db->GetRow($strSQL);
        $flag = (is_null($row['is_surgical'])) ? 0 : $row['is_surgical'];

        return ($flag != 0);
	}

  function isFreeDistribution() {
      return $this->isfreedist;
  }

	function hasPostedItemsOfPkg() {
		global $db;

		$strSQL = "SELECT
									1        AS src,
									ph.refno
								FROM seg_more_phorder ph
								WHERE ph.encounter_nr = '".$this->current_enr."'
									 UNION SELECT
														 2         AS src,
														 s.refno
													 FROM seg_misc_service s
													 WHERE s.encounter_nr = '".$this->current_enr."'
															UNION SELECT
																			 3          AS src,
																			 ch.refno
																		 FROM seg_misc_chrg ch
																		 WHERE ch.encounter_nr = '".$this->current_enr."'";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				return true;
			}
		}
		return false;
	}

	// This function is applicable to BPH Requirement only ...
	function applyPHIPInsurance() {
		global $db;

		$bSuccess = true;
		$strSQL = "select cpi.*, sei.hcare_id as id from (care_person_insurance as cpi inner join care_encounter as ce
					on cpi.pid = ce.pid and encounter_nr = '".$this->current_enr."') left join seg_encounter_insurance as sei
					on cpi.hcare_id = sei.hcare_id and sei.encounter_nr = ce.encounter_nr
					where exists (select * from care_insurance_firm as cif
					where cif.default_classification = 'D' and cif.hcare_id = cpi.hcare_id) and is_void = 0";
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
	function initOpsConfineCoverage() {
		$this->ops_confine_benefits = array();
		$this->ops_confine_coverage = 0.00;
	}

	function initProfFeesCoverage($pfarea) {
		$this->pfs_confine_coverage[$pfarea] = 0.00;
		$this->pfs_confine_benefits[$pfarea] = array();
	}

	function getMscConfineCoverage() {
		return($this->msc_confine_coverage);
	}

	function getMiscBenefits() {
		return($this->hsp_msc_benefits);
	}

	function getPkgBenefits() {
		return($this->pkg_benefits);
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

	function setIsCoveredByPkg($flag) {
		$this->is_coveredbypkg = $flag;
	}

	function getIsCoveredByPkg() {
		return($this->is_coveredbypkg);
	}

	function getIsWithDeposit() {
		return($this->is_withdeposit);
	}

	function getMGHDate() {
		if (strcmp($this->mgh_date, "0000-00-00 00:00:00") == 0)
			return "";
		else
			return strftime("%m-%d-%Y %I:%M %p", strtotime($this->mgh_date));
	}

	#
	#   Added by LST - 10.14.2009
	#
	function getPackageID() {
		return($this->package_id);
	}

	#
	#   Added by LST - 10.14.2009
	#
	function getPackageName() {
		global $db;

		$pkg_name = '';
		$this->sql = "select package_name           \n
						 from seg_packages          \n
						 where package_id = {$this->package_id}";
		if ($this->result = $db->Execute($this->sql)) {
			if ($this->result->RecordCount()) {
				$row = $this->result->FetchRow();
                //added by jasper 09/09/2013 FOR BUGZILLA #188 - WELLBABY
				$pkg_name = is_null($row['package_name']) ? "" : $row['package_name'];
                if (!(strpos(trim(strtoupper($pkg_name)), DEFAULT_NBPKG_NAME, 0) === false)) {
                    $pkg_name = substr($pkg_name, 0, strlen($pkg_name) - 2);
                }
                //added by jasper 09/09/2013 FOR BUGZILLA #188 - WELLBABY
			}
		}

		return($pkg_name);
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

	function getBillStartDate() {
		return($this->bill_frmdte);
	}

	function setBillDate($nbill_dte) {
		$this->bill_dte = $nbill_dte;
	}

	function getAppliedHrsCutoff() {
		global $db;

		$n_cutoff = -1;

		$strSQL = "select applied_hrs_cutoff ".
							"   from seg_billing_encounter ".
							"   where bill_nr = '".$this->old_bill_nr."' and is_deleted IS NULL";
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

    // Classification discount only applies to patients with charity accommodation ...
    // Requested by SPMC billing ... 06.27.2012...
    if ($this->isCharity() || $this->isERPatient()) {
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
		$filter = array('','');

		if ($this->prev_encounter_nr != '') {
            $filter[0] = " or cel.encounter_nr = '$this->prev_encounter_nr'";
            $filter[1] = " or sel.encounter_nr = '$this->prev_encounter_nr'";
        }

        //edited by jasper 07/08/2013 SEG_ENCOUNTER_LOCATION_ADDTL WILL BE BASED ON ENTRY NUMBER
		/*$strSQL = "select
                  STR_TO_DATE(CONCAT(DATE_FORMAT(date_from, '%Y-%m-%d'), ' ', DATE_FORMAT(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') occupy_date,
                  cw.accomodation_type, accomodation_name ".
					"   from ((care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr) ".
					"      inner join seg_accomodation_type as sat on cw.accomodation_type = sat.accomodation_nr) ".
					"      left join seg_encounter_location_rate as selr on cel.nr = selr.loc_enc_nr and cel.encounter_nr = selr.encounter_nr ".
					"   where (cel.encounter_nr = '". $this->current_enr. "'".$filter[0].") ".
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
          " UNION ALL
            SELECT occupy_date, cw.accomodation_type, accomodation_name
              FROM (seg_encounter_location_addtl sel INNER JOIN care_ward AS cw ON sel.group_nr = cw.nr)
                INNER JOIN seg_accomodation_type sat ON cw.accomodation_type = sat.accomodation_nr
            WHERE (sel.encounter_nr = '". $this->current_enr. "'".$filter[1].")
              AND (
                STR_TO_DATE(
                  sel.create_dt,
                  '%Y-%m-%d %H:%i:%s'
                ) >= '" . $this->bill_frmdte . "'
                AND STR_TO_DATE(
                  sel.create_dt,
                  '%Y-%m-%d %H:%i:%s'
                ) < '" . $this->bill_dte . "'
              )
            ORDER BY occupy_date DESC LIMIT 1";*/
        //edited by jasper 07/24/2013 - ADDED cw.name AS ward_name
        //FIX FOR BUGZILLA BUGID:302
            $strSQL = "select 0 AS entry_no,
                  STR_TO_DATE(CONCAT(DATE_FORMAT(date_from, '%Y-%m-%d'), ' ', DATE_FORMAT(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') occupy_date,
                  cw.accomodation_type, accomodation_name, cw.name AS ward_name ".
                    "   from ((care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr) ".
                    "      inner join seg_accomodation_type as sat on cw.accomodation_type = sat.accomodation_nr) ".
                    "      left join seg_encounter_location_rate as selr on cel.nr = selr.loc_enc_nr and cel.encounter_nr = selr.encounter_nr ".
                    "   where (cel.encounter_nr = '". $this->current_enr. "'".$filter[0].") ".
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
          " UNION ALL
            SELECT entry_no, occupy_date, cw.accomodation_type, accomodation_name, cw.name AS ward_name
              FROM (seg_encounter_location_addtl sel INNER JOIN care_ward AS cw ON sel.group_nr = cw.nr)
                INNER JOIN seg_accomodation_type sat ON cw.accomodation_type = sat.accomodation_nr
            WHERE (sel.encounter_nr = '". $this->current_enr. "'".$filter[1].")
              AND (
                STR_TO_DATE(
                  sel.create_dt,
                  '%Y-%m-%d %H:%i:%s'
                ) >= '" . $this->bill_frmdte . "'
                AND STR_TO_DATE(
                  sel.create_dt,
                  '%Y-%m-%d %H:%i:%s'
                ) < '" . $this->bill_dte . "'
              )
            ORDER BY entry_no DESC LIMIT 1";

        $this->debugSQL = $strSQL;
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$ntype = $row['accomodation_type'];
					$sname = $row['accomodation_name'];
                    $ward_name = $row['ward_name'];
				}
			}
		}

        $this->accomm_typ_nr = $ntype;
        $this->accomm_typ_desc = $sname;
        $this->accomm_ward_name = $ward_name;

        return($db->ErrorMsg() == '');

//		if ($ntype == 0)
//			return($this->getAddedAccommodationType());
//		else {
//			$this->accomm_typ_nr = $ntype;
//			$this->accomm_typ_desc = $sname;
//
//			return($db->ErrorMsg() == '');
//		}
	}
    //added by jasper 04/03/2013
    function getBillNo() {
        return $this->old_bill_nr;
    }
    //added by jasper 04/03/2013

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

    //added by jasper 07/24/2013 FIX FOR BUGZILLA ID 302
    function isOBAnnex() {
        if ($this->accomm_typ_desc == '') {
            $this->getAccommodationType();
        }
        return (!(strpos(strtoupper($this->accomm_ward_name), OBANNEX, 0) === false));
    }

    //added by jasper 07/24/2013

	function isSponsoredMember() {
		if ($this->memcategory == '') {
			$this->memcategory = $this->getMemCategoryDesc();
		}

        #added by VAN 05-27-2013
        #if payward, no balance billing will be applied
        if ($this->isCharity())
			return (!(strpos(strtoupper($this->memcategoryId), '5', 0) === false));
        else
            return false;
	}


	//Added by Jarel 12/04/2013 for HSM
	function isHSM() 
	{
		if ($this->memcategory == '') {
			$this->memcategory = $this->getMemCategoryDesc();
		}

		if ($this->isCharity())
			return (!(strpos(strtoupper($this->memcategoryId), HSM, 0) === false));
        else
            return false;

	}

	function getMemCategoryDesc() {
		global $db;

		$s_desc= "";
		$filter = '';

		if ($this->prev_encounter_nr != '') $filter = " or sem.encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select memcategory_desc , sem.memcategory_id  ".
					"from seg_memcategory as sm inner join seg_encounter_memcategory as sem ".
					"on sm.memcategory_id = sem.memcategory_id ".
					"where (sem.encounter_nr = '". $this->current_enr. "'".$filter.")";

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$s_desc = $row['memcategory_desc'];
					$this->memcategoryId = $row['memcategory_id'];
				}
			}
		}

		return $s_desc;
	}

	function getConfinementType() {
		global $db;

		$n_id = 0;
		$filter = '';

		if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";

		$strSQL = "select confinetype_id from seg_encounter_confinement ".
					"   where (encounter_nr = '". $this->current_enr. "'".$filter.") ".
					"      and str_to_date(classify_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "' " .
					"   order by create_time desc limit 1";

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
                    "      and !sec.is_deleted ".
					"   order by sec.create_dt desc limit 1";
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
					$enc_dte = strftime("%Y-%m-%d %H:%M", strtotime($row['encounter_date'])).":00";
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
					$admit_dte = strftime("%Y-%m-%d %H:%M", strtotime($row['admission_dt'])).":00";
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
					"   where (encounter_nr = '". $this->current_enr ."'".$filter.") and is_deleted IS NULL " .
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
					"      and str_to_date(bill_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "' and is_deleted IS NULL ".
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

		if ($this->temp_valid_items[$item_code]) {
			$this->temp_valid_items[$item_code] -= $tcharge;
		}
		else {
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
				$this->valid_covered_items[] = str_replace(',', '', $item_code);
			}
			else {
				$this->temp_valid_items[$item_code] = $tcoverage - $tcharge;
			}
		}
	}

	function clearInvalidItemsFromCoverage($bMeds = false) {
		global $db;

		if (is_array($this->temp_valid_items) && (count($this->temp_valid_items) > 0)) {
			foreach ($this->temp_valid_items as $k=>$v) {
				if ($v <= 0) $this->valid_covered_items[] = $k;
			}
		}
		$valid_items = "'".implode(",",$this->valid_covered_items)."'";
		$strSQL = "delete from seg_applied_coverage\n
						where ref_no = concat('T','".$this->current_enr."') \n
						 and source ".($bMeds ? "= 'M'" : "<> 'M'")." \n
						 and find_in_set(REPLACE(item_code, ',' ''), $valid_items) = 0 ";
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
        //edited by jasper 04/11/2013
/*		$strSQL = "select cel.encounter_nr, location_nr, cr.type_nr, concat(ctr.name,' (',cw.name,')') as name, ".
					"      (case when not (isnull(selr.rate) OR selr.rate=0)  then selr.rate else ctr.room_rate end) as rm_rate, 0 as days_stay, 0 as hrs_stay, ".
					"      date_from, date_to, time_from, time_to, 'AD' as source, mandatory_excess ".
					"   from ((care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr) ".
					"      left join seg_encounter_location_rate as selr on cel.nr = selr.loc_enc_nr and cel.encounter_nr = selr.encounter_nr) ".
					"      inner join (care_room as cr inner join care_type_room as ctr on cr.type_nr = ctr.nr) ".
							"      on cel.location_nr = cr.room_nr and cel.group_nr = cr.ward_nr ".
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
							"      (case when not (isnull(sel.rate) OR sel.rate=0)  then sel.rate else ctr.room_rate end) as rm_rate, days_stay, hrs_stay, ".
					"      date(sel.create_dt) as date_from, '0000-00-00' as date_to, time(sel.create_dt) as time_from, '00:00:00' as time_to, 'BL' as source, mandatory_excess ".
					"   from (seg_encounter_location_addtl as sel inner join care_ward as cw on sel.group_nr = cw.nr) ".
							"      inner join (care_room as cr inner join care_type_room as ctr on cr.type_nr = ctr.nr) on sel.room_nr = cr.nr and sel.group_nr = cr.ward_nr ".
					"   where (sel.encounter_nr = '". $this->current_enr. "'".$filter[1].") ".
					"      and (str_to_date(sel.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
					"      and str_to_date(sel.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
					"   order by source, date_from, time_from";     */
        $strSQL = "select cel.encounter_nr, location_nr, cr.type_nr, cw.accomodation_type, concat(ctr.name,' (',cw.name,')') as name, ".
                    "      (case when not (isnull(selr.rate) OR selr.rate=0)  then selr.rate else ctr.room_rate end) as rm_rate, 0 as days_stay, 0 as hrs_stay, ".
                    "      date_from, date_to, time_from, time_to, 'AD' as source, mandatory_excess ".
                    "   from ((care_encounter_location as cel inner join care_ward as cw on cel.group_nr = cw.nr) ".
                    "      left join seg_encounter_location_rate as selr on cel.nr = selr.loc_enc_nr and cel.encounter_nr = selr.encounter_nr) ".
                    "      inner join (care_room as cr inner join care_type_room as ctr on cr.type_nr = ctr.nr) ".
                            "      on cel.location_nr = cr.room_nr and cel.group_nr = cr.ward_nr ".
                    "   where (cel.encounter_nr = '". $this->current_enr. "'".$filter[0].") ".
                    "      and exists (select nr ".
                    "                     from care_type_location as ctl ".
                    "                        where upper(type) = 'ROOM' and ctl.nr = cel.type_nr) ".
                    "      and ((str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
                    "         and str_to_date(concat(date_format(date_from, '%Y-%m-%d'), ' ', date_format(time_from, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
                    "             or ".
                    "       (str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
                    "         and str_to_date(concat(date_format(date_to, '%Y-%m-%d'), ' ', date_format(time_to, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
                    "          or ".
                    "        str_to_date(concat(date_format(ifnull(date_to, '0000-00-00'), '%Y-%m-%d'), ' ', date_format(ifnull(time_to, '00:00:00'), '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') = '0000-00-00 00:00:00') ".
                    " union ".
                    "select sel.encounter_nr, cr.room_nr, cr.type_nr, cw.accomodation_type, concat(ctr.name,' (',cw.name,')') as name, ".
                            "      (case when not (isnull(sel.rate) OR sel.rate=0)  then sel.rate else ctr.room_rate end) as rm_rate, days_stay, hrs_stay, ".
                    "      date(sel.create_dt) as date_from, '0000-00-00' as date_to, time(sel.create_dt) as time_from, '00:00:00' as time_to, 'BL' as source, mandatory_excess ".
                    "   from (seg_encounter_location_addtl as sel inner join care_ward as cw on sel.group_nr = cw.nr) ".
                            "      inner join (care_room as cr inner join care_type_room as ctr on cr.type_nr = ctr.nr) on sel.room_nr = cr.nr and sel.group_nr = cr.ward_nr ".
                    "   where (sel.encounter_nr = '". $this->current_enr. "'".$filter[1].") ".
                    "      and (str_to_date(sel.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
                    "      and str_to_date(sel.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
                    "   order by source, date_from, time_from";
        $this->debugSQL = $strSQL;
		if ($result = $db->Execute($strSQL)) {
			$this->accommodation_hist = array();
			if ($result->RecordCount()) {
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
							$tmpdate_to = strftime("%Y-%m-%d", strtotime($this->tempbill_dte));
							$tmptime_to = strftime("%H:%M:%S", strtotime($this->tempbill_dte));
						}
						else {
							$tmptime_to = $row['time_to'];
							$tmpref_dte = strftime("%Y-%m-%d", strtotime($tmpdate_to)). ' '.strftime("%H:%M:%S",  strtotime($tmptime_to));

							if (strtotime($tmpref_dte) > strtotime($this->tempbill_dte)) {
								$tmpdate_to = strftime("%Y-%m-%d", strtotime($this->tempbill_dte));
								$tmptime_to = strftime("%H:%M:%S", strtotime($this->tempbill_dte));
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
                    //added by jasper 07/12/2013 - FIX FOR MS-728 TO ACCOMMODATE NEW ROOM RATES BASED ON CASE TYPE FROM HOSPITAL ADMINISTRATIVE ORDER NO, 18 s.2013
                    $confinement_type = $this->getConfinementType();
                    if ($this->isPHIC()) {
                    	$room_rate = $this->getRoomRateByCaseType($confinement_type, $row['name']);
                        if ($room_rate > 0) {
                        } else {
                            $room_rate = $row['rm_rate'];
                        }
                    } else {
                    	if($this->isdied){
                    		if($this->isCharity() && !$this->isMedicoLegal()){
                    			$room_rate = $this->getdeathroomrate($row['name']); #Added by Jarel 10/16/2013 set death room rate if dead for N-PHIC
                    			if($room_rate==0){
                    				$room_rate = $row['rm_rate'];
                    			}	
                    		}else{
                    			$room_rate = $row['rm_rate'];
                    		}
                    	}else{
                    		$room_rate = $row['rm_rate'];
                    	}
                        
                    }
                    //$this->debugSQL = $status . "DESC: " . $row['name'] . " CONFINEMENT TYPE: " . $confinement_type . " ROOMRATE: " . $room_rate;
                    //added by jasper 07/12/2013 - FIX FOR MS-728 TO ACCOMMODATE NEW ROOM RATES BASED ON CASE TYPE FROM HOSPITAL ADMINISTRATIVE ORDER NO, 18 s.2013
                    $objAcc->setRoomRate($room_rate);    
					$objAcc->setSource($row['source']);
                    $objAcc->setExcess($row['mandatory_excess']);
                    $objAcc->setAccomodationType($row['accomodation_type']);

					// Add new accommodation object in collection (array) of accommodations for this billing.
					$this->accommodation_hist[] = $objAcc;
				}
			}
		}
	}

//added by jasper 07/12/2013 - FIX FOR MS-728 TO ACCOMMODATE NEW ROOM RATES BASED ON CASE TYPE FROM HOSPITAL ADMINISTRATIVE ORDER NO, 18 s.2013
function getRoomRateByCaseType($casetypeid = '', $warddesc = '') {
    global $db;

    $strSQL = "";
    if (!(strpos(strtoupper($warddesc), SERVICEWARD, 0) === false) && (strpos(strtoupper($warddesc), ICUWARD, 0) === false) || (!strpos(strtoupper($warddesc), OBANNEX, 0) === false)) {
        $strSQL = "SELECT service_ward_roomrate AS room_rate FROM seg_confinementtype_room_rate WHERE confinetype_id = " . $casetypeid;
    }
    else if (!(strpos(strtoupper($warddesc), ANNEXWARD, 0) === false)) {
        $strSQL = "SELECT annex_roomrate AS room_rate FROM seg_confinementtype_room_rate WHERE confinetype_id = " . $casetypeid;
    }

    if ($strSQL<>"") {
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                $row = $result->FetchRow();
                $rm_rate = $row['room_rate'];
            }
        }
    } else {
        $rm_rate = 0;
    }
    return $rm_rate;
}
//added by jasper 07/12/2013 - FIX FOR MS-728 TO ACCOMMODATE NEW ROOM RATES BASED ON CASE TYPE FROM HOSPITAL ADMINISTRATIVE ORDER NO, 18 s.2013
    
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
		$this->services_list = array();
// "         and exists (select * from seg_lab_results as slr where slr.refno = lh.refno limit 1) " .

		// Get all the services charged to current encounter ...
		if ($this->prev_encounter_nr != '') $filter[0] = " or encounter_nr = '$this->prev_encounter_nr'";
		if ($this->prev_encounter_nr != '') $filter[1] = " or sos.encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select lh.refno, serv_dt, serv_tm, ld.service_code, ls.name as service_desc, ls.group_code, " .
					"   lsg.name as group_desc, ld.quantity as qty, ld.price_charge as serv_charge, 'LB' as source " .
					"   from ((seg_lab_serv as lh inner join seg_lab_servdetails as ld on lh.refno = ld.refno) " .
					"          inner join seg_lab_services as ls on ld.service_code = ls.service_code) " .
					"          inner join seg_lab_service_groups as lsg on ls.group_code = lsg.group_code " .
					"      WHERE (CASE WHEN serv_dt >= DATE('".ISSRVD_EFFECTIVITY."') THEN ld.is_served ELSE 1 END) AND ".
					"      /* where (ld.is_served <> 0 and) */  ".
					"         UPPER(TRIM(ld.STATUS)) <> 'DELETED' AND lh.is_cash = 0 and (ld.request_flag is null OR ld.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
					"         and (encounter_nr = '" . $this->current_enr. "'".$filter[0].") and upper(trim(lh.status)) <> 'DELETED' " .
					"         and (str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"            and str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
							"   group by lh.refno, serv_dt, serv_tm, ld.service_code, ls.name, ls.group_code, lsg.name";
		$this->putinservices_list($strSQL);

		$strSQL = "select rh.refno, rh.request_date as serv_dt, rh.request_time as serv_tm, rd.service_code, rs.name as service_desc, rs.group_code, " .
					"   rsg.name as group_desc, count(rd.service_code) as qty, (sum(rd.price_charge)/count(rd.service_code)) as serv_charge, 'RD' as source " .
					"   from ((seg_radio_serv as rh inner join care_test_request_radio as rd on rh.refno = rd.refno) " .
					"          inner join seg_radio_services as rs on rd.service_code = rs.service_code) " .
					"          inner join seg_radio_service_groups as rsg on rs.group_code = rsg.group_code " .
					"      WHERE (CASE WHEN rh.request_date >= DATE('".ISSRVD_EFFECTIVITY."') THEN rd.is_served ELSE 1 END) AND ".
					"      /* where upper(rd.status) = 'DONE' and*/ UPPER(TRIM(rd.STATUS)) <> 'DELETED' AND rh.is_cash = 0 and (rd.request_flag is null OR rd.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
					"         and (encounter_nr = '" . $this->current_enr. "'".$filter[0].") and upper(trim(rh.status)) <> 'DELETED' and upper(trim(rd.status)) <> 'DELETED' " .
					"         and (str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"            and str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
							"   group by rh.refno, rh.request_date, rh.request_time, rd.service_code, rs.name, rs.group_code, rsg.name";
		$this->putinservices_list($strSQL);

		$strSQL =	"select ph.refno, date(ph.orderdate) as serv_dt, time(ph.orderdate) as serv_tm, pd.bestellnum as service_code, artikelname as service_desc, 'SU' as group_code, ".
							"      'Supplies' as group_desc, pd.quantity - ifnull(spri.quantity, 0) as qty, pricecharge as serv_charge, 'SU' as source ".
					"   from ((seg_pharma_orders as ph inner join
												 seg_pharma_order_items pd on ph.refno = pd.refno and pd.serve_status <> 'N' and pd.request_flag is null) ".
					"      inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'S') ".
					"      left join
							(SELECT rd.ref_no, rd.bestellnum, SUM(quantity) AS quantity
																	FROM seg_pharma_return_items AS rd INNER JOIN seg_pharma_returns AS rh
																		 ON rd.return_nr = rh.return_nr AND (rh.encounter_nr = '". $this->current_enr. "'".$filter[0].")
																	WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = '". $this->current_enr. "'".$filter[0].") AND rd.ref_no = oh.refno)
										GROUP BY rd.ref_no, rd.bestellnum) as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum ".
					"   where (encounter_nr = '". $this->current_enr. "'".$filter[0].") and is_cash = 0 ".
					"      and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
					"      and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
							"      and (pd.quantity - ifnull(spri.quantity, 0)) > 0";

		$this->putinservices_list($strSQL);

		$strSQL = "select mph.refno, date(mph.chrge_dte) as serv_dt, time(mph.chrge_dte) as serv_tm, mphd.bestellnum as service_code, artikelname as service_desc, 'MS' as group_code, ".
							"      'Supplies' as group_desc, quantity as qty, unit_price as serv_charge, 'MS' as source ".
					"   from (seg_more_phorder_details as mphd inner join seg_more_phorder as mph on mphd.refno = mph.refno) ".
					"      inner join care_pharma_products_main as p on mphd.bestellnum = p.bestellnum and p.prod_class = 'S' ".
					"   where (encounter_nr = '". $this->current_enr. "'".$filter[0].") ".
					"      and (str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
					"         and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
							"   group by mph.refno, mph.chrge_dte, mphd.bestellnum, artikelname";
		$this->putinservices_list($strSQL);

		$strSQL = "select sos.refno, date(eqh.order_date) as serv_dt, time(eqh.order_date) as serv_tm, eqd.equipment_id, artikelname, '' as group_code,
						 'Equipment' as group_desc, sum(number_of_usage) as qty, (sum(discounted_price * number_of_usage)/sum(number_of_usage)) as uprice, 'OE' as source
						 from ((seg_equipment_orders as eqh inner join seg_equipment_order_items as eqd on eqh.refno = eqd.refno)
						 left join seg_ops_serv as sos on sos.refno = eqh.request_refno) inner join care_pharma_products_main as
						 cppm on cppm.bestellnum = eqd.equipment_id
						 where (sos.encounter_nr = '". $this->current_enr. "'".$filter[1].")
							and (str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "'
							and str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "')
								 group by sos.refno, eqh.order_date, eqd.equipment_id, artikelname";
		 $this->putinservices_list($strSQL);

		 $strSQL = "select m.refno, date(m.chrge_dte) as serv_dt, time(m.chrge_dte) as serv_tm, md.service_code, ms.name as service_desc, '' as group_code, ".
					"      'Others' as group_desc, sum(md.quantity) as qty, (sum(chrg_amnt * md.quantity)/sum(md.quantity)) as serv_charge, 'OA' as source ".
					"   from (seg_misc_service as m inner join seg_misc_service_details as md on m.refno = md.refno) ".
					"      inner join seg_other_services as ms on md.service_code = ms.alt_service_code ".
					"   where (encounter_nr = '" . $this->current_enr. "'".$filter[0].") and md.request_flag is null ".
					"      and (str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
					"      and str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
					"   group by m.refno, m.chrge_dte, md.service_code, ms.name";
		 $this->putinservices_list($strSQL);
//		if ($result = $db->Execute($strSQL)) {
//			$this->services_list = array();

//			if ($result->RecordCount()) {
//				while ($row = $result->FetchRow()) {
//					$objServ = new Service;

//					$objServ->setRefNo($row['refno']);
//					$objServ->setTransDteTime($row['serv_dt']  , $row['serv_tm']);
//					$objServ->setServiceCode($row['service_code']);
//					$objServ->setServiceDesc($row['service_desc']);
//					$objServ->setGroupCode($row['group_code']);
//					$objServ->setGroupDesc($row['group_desc']);
//					$objServ->setServQty($row['qty']);
//					$objServ->setServPrice($row['serv_charge']);
//					$objServ->setServProvider($row['source']);

					// Add new Service object in collection (array) of services charged in this billing.
//					$this->services_list[] = $objServ;
//				}
//			}
//		}
	}

	function putinservices_list($strSQL) {
		global $db;

		if ($result = $db->Execute($strSQL)) {
//			$this->services_list = array();

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
					"      WHERE (CASE WHEN rh.request_date >= DATE('".ISSRVD_EFFECTIVITY."') THEN rd.is_served ELSE 1 END) AND ".
					"      /* where upper(rd.status) = 'DONE' and*/ UPPER(TRIM(rd.STATUS)) <> 'DELETED' AND rh.is_cash = 0 and (rd.request_flag is null OR rd.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
					"         and (encounter_nr = '" . $this->current_enr. "'".$filter.") and upper(trim(rh.status)) <> 'DELETED' and upper(trim(rd.status)) <> 'DELETED' " .
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
					"   lsg.name as group_desc, quantity as qty, ld.price_charge as serv_charge, 'LB' as source " .
					"   from ((seg_lab_serv as lh inner join seg_lab_servdetails as ld on lh.refno = ld.refno) " .
					"          inner join seg_lab_services as ls on ld.service_code = ls.service_code) " .
					"          inner join seg_lab_service_groups as lsg on ls.group_code = lsg.group_code " .
					"      WHERE (CASE WHEN serv_dt >= DATE('".ISSRVD_EFFECTIVITY."') THEN ld.is_served ELSE 1 END) AND ".
					"      /* where (ld.is_served <> 0 and) */ UPPER(TRIM(ld.STATUS)) <> 'DELETED' AND lh.is_cash = 0 and (ld.request_flag is null OR ld.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
					"         and (encounter_nr = '" . $this->current_enr. "'".$filter.") and upper(trim(lh.status)) <> 'DELETED' " .
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
					"   from ((seg_pharma_orders as ph inner join
												seg_pharma_order_items pd on ph.refno = pd.refno and pd.serve_status <> 'N' and pd.request_flag is null) ".
					"      inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'M') ".
					"      left join
(SELECT rd.ref_no, rd.bestellnum, SUM(quantity) AS quantity
																	FROM seg_pharma_return_items AS rd INNER JOIN seg_pharma_returns AS rh
																		 ON rd.return_nr = rh.return_nr AND (rh.encounter_nr = '". $this->current_enr. "'".$filter.")
																	WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = '". $this->current_enr. "'".$filter.") AND rd.ref_no = oh.refno)
										GROUP BY rd.ref_no, rd.bestellnum) as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum ".
					"   where (encounter_nr = '". $this->current_enr. "'".$filter.") and is_cash = 0 and pd.serve_status <> 'N' and pd.request_flag is null ".
					"      and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
					"      and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
					"      and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
					" union ".
					"select mph.refno, mph.chrge_dte, 'O' as department, mphd.bestellnum, artikelname, quantity, unit_price ".
					"   from (seg_more_phorder_details as mphd inner join seg_more_phorder as mph on mphd.refno = mph.refno) ".
					"      inner join care_pharma_products_main as p on mphd.bestellnum = p.bestellnum and p.prod_class = 'M' ".
					"   where (encounter_nr = '". $this->current_enr. "'".$filter.") ".
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

    $issurgical  = $this->isSurgicalCase();
    //added by jasper 09/03/2013 FOR BUG#305
    if ($this->getPackageName() == DEFAULT_NBPKG_NAME) {
        $amountlimit = DEFAULT_NBPKG_RATE;
    } else {
    $amountlimit = $this->getPkgAmountLimit();
    }
    //added by jasper 09/03/2013 FOR BUG#305

    $hc_pf = $this->getHouseCasePCF();
		$strSQL = "select attending_dr_nr as dr_nr, name_last, name_first, name_middle, 'Attending Doctor' as role, ".
					"   sum(fn_days_attended(attend_start, if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), ".$this->cutoff_hrs.")) as num_days, daily_rate, ".
					"   '' as opcodes, daily_rate * sum(fn_days_attended(attend_start, if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), ".$this->cutoff_hrs.")) as dr_charge, ".
					"   0 as is_excluded, role_nr, role_area, 0 as role_type_level, 0 as rvu, 0 as multiplier ".
					"   from ".
					"      (select distinct attending_dr_nr, name_last, name_first, name_middle, attend_start, ".
					"          subdate((select attend_start ".
					"                      from seg_encounter_dr_mgt as dm2 ".
					"                      where dm2.encounter_nr = dm1.encounter_nr and ".
					"                            dm2.att_hist_no > dm1.att_hist_no ".
					"                      order by dm2.att_hist_no asc limit 1), 1) as attend_end, fn_getdailyrate('{$this->current_enr}', date('{$this->bill_dte}'), tier_nr, {$this->confinetype_id}, attending_dr_nr) as daily_rate, cpa.role_nr, fn_getDailyVisitRoleArea(tier_nr) as role_area, discharge_date ".
					"          from (seg_encounter_dr_mgt as dm1 inner join (((care_personell as cpn ".
					"             inner join care_person as cp on cpn.pid = cp.pid) inner join care_personell_assignment as cpa ".
					"             on cpn.nr = cpa.personell_nr) inner join care_role_person as crp on ".
					"             cpa.role_nr = crp.nr) on dm1.attending_dr_nr = cpn.nr) inner join care_encounter as ce ".
					"             on dm1.encounter_nr = ce.encounter_nr ".
					"          where (dm1.encounter_nr = '" . $this->current_enr. "'".$filter[0].") " .
					"             and (str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"                and str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
					"          order by att_hist_no) as t ".
					"   group by attending_dr_nr, role_area, role_nr ".
					" union all ".
					"select distinct spd.dr_nr, name_last, name_first, name_middle, concat(name, ' - private') as role, spd.days_attended as num_days, 0 as daily_rate, ".
					"      GROUP_CONCAT(DISTINCT CONCAT(socd.ops_code, '-', IFNULL(socd.rvu,0), '(', socd.ops_entryno, ')') SEPARATOR ';') AS opcodes,
                           (CASE WHEN NOT ".($this->is_coveredbypkg ? "1" : "0")." THEN SUM(ifnull(socd.rvu,0) * IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$this->bill_dte."'), role_area, ifnull(role_type_level, tier_nr), ifnull(socd.rvu,0), $this->confinetype_id, spd.dr_nr), ifnull(socd.multiplier,0))) * fn_getrvuadjustment('$this->current_enr', date('".$this->bill_dte."'), role_area, ifnull(role_type_level, tier_nr), ifnull(socd.rvu,0), $this->confinetype_id)) ELSE {$amountlimit} * IF(is_excluded OR ".($this->isfreedist ? "1" : "0").", 0, fn_getcaseratepkglimit(role_area, ".($issurgical ? '1' : '0').", DATE('".$this->bill_dte."'))) END) + dr_charge as dr_charge, is_excluded, ".
					"      spd.dr_role_type_nr, role_area, IFNULL(role_type_level, IFNULL(dr_level, tier_nr)) as role_type_level, SUM(ifnull(socd.rvu,0)) as tot_rvu, SUM(IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$this->bill_dte."'), role_area, ifnull(role_type_level, tier_nr), ifnull(socd.rvu,0), $this->confinetype_id, spd.dr_nr), ifnull(socd.multiplier,0))) * ifnull(socd.rvu,0))/SUM(ifnull(socd.rvu,0)) as avg_multiplier ".
					"   from ((seg_encounter_privy_dr as spd left join seg_ops_chrg_dr as socd on ".
					"      spd.encounter_nr = socd.encounter_nr and spd.dr_nr = socd.dr_nr and ".
					"      spd.dr_role_type_nr = socd.dr_role_type_nr) inner join (care_personell as cpn ".
					"      inner join care_person as cp on cpn.pid = cp.pid) on spd.dr_nr = cpn.nr) ".
					"      inner join care_role_person as crp on spd.dr_role_type_nr = crp.nr ".
					"   where (spd.encounter_nr = '" . $this->current_enr. "'".$filter[1].") ".
					"      and (str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
					"      and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
					"   group by spd.dr_nr, role_area, dr_role_type_nr, spd.entry_no ".
					" union all ".
					"select dr_nr, name_last, name_first, name_middle, concat(name, ' - ', cop.code) as role, null as num_days, 0 as daily_rate, GROUP_CONCAT(DISTINCT CONCAT(sosd.ops_code,'-',IFNULL(sosd.rvu,0)) SEPARATOR ';') AS opcodes, ".
					"      (CASE WHEN NOT ".($this->is_coveredbypkg ? "1" : "0")." THEN SUM(ifnull(sosd.rvu,0) * IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$this->bill_dte."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id, dr_nr), ifnull(multiplier,0))) * fn_getrvuadjustment('$this->current_enr', date('".$this->bill_dte."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id)) ELSE {$amountlimit} * IF(".($this->isfreedist ? "1" : "0").", 0, fn_getcaseratepkglimit(role_area, ".($issurgical ? '1' : '0').", DATE('".$this->bill_dte."'))) END) + ops_charge as dr_charge, 0 as is_excluded, ".
					"      sop.role_type_nr, role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), sum(sosd.rvu) as tot_rvu, (sum(IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$this->bill_dte."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id, dr_nr), ifnull(multiplier,0))) * sosd.rvu)/sum(sosd.rvu)) as avg_multiplier ".
					"   from (((seg_ops_personell as sop inner join (care_personell as cpn ".
					"      inner join care_person as cp on cpn.pid = cp.pid) on sop.dr_nr = cpn.nr) ".
					"      inner join (seg_ops_serv as sos inner join
							(SELECT sd.refno, ops_code, rvu, multiplier, group_code
								FROM seg_ops_servdetails AS sd INNER JOIN seg_ops_serv AS sh
									ON sd.refno = sh.refno
								WHERE sh.encounter_nr = '$this->current_enr'
									HAVING (rvu = (SELECT MAX(rvu) AS rvumax
													FROM seg_ops_servdetails AS d
													WHERE d.refno = sd.refno AND d.group_code = sd.group_code)
										 AND sd.group_code <> '') OR sd.group_code = '') as sosd ".
					"         on sos.refno = sosd.refno) on sop.refno = sos.refno) ".
					"      inner join care_role_person as crp on sop.role_type_nr = crp.nr) ".
					"      inner join seg_ops_rvs as cop on sop.ops_code = cop.code ".
					"   where (encounter_nr = '" . $this->current_enr. "'".$filter[2].") and upper(trim(sos.status)) <> 'DELETED' ".
					"      and (str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"         and str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
					"      and role_area is not null and crp.role not like '%_asst%' " .
					"      and sosd.ops_code = sop.ops_code ".
					"   group by dr_nr, role_area, role_type_nr";

//		if ($_SESSION['sess_temp_userid']=='medocs')
//			$this->debugSQL = $strSQL;

		if ($result = $db->Execute($strSQL)) {
			$this->proffees_list = array();

			if ($result->RecordCount()) {
        $bhasD4 = false;
        $d3indx = -1;
        $indx = 0;
				while ($row = $result->FetchRow()) {
					$objpf = new ProfFee;

          if ($row['role_area'] == 'D4') $bhasD4 = true;
          if ($row['role_area'] == 'D3' && !$row['is_excluded']) $d3indx = $indx;

					$objpf->setDrNr($row['dr_nr']);
					$objpf->setDrLast($row['name_last']);
					$objpf->setDrFirst($row['name_first']);
					$objpf->setDrMid((is_null($row['name_middle'])) ? '' : $row['name_middle']);
					$objpf->setRoleNo($row['role_nr']);
					$objpf->setRoleDesc($row['role']);
					$objpf->setRoleBenefit($row['role_area']);
					$objpf->setRoleLevel($row['role_type_level']);
					$objpf->setDaysAttended($row['num_days']);
					$objpf->setDrDailyRate($row['daily_rate']);
					$objpf->setDrCharge($row['dr_charge']);
					$objpf->setRVU($row['rvu']);
					$objpf->setMultiplier($row['multiplier']);
					$objpf->setChrgForCoverage((($row['is_excluded'] != 0) ? 0 : $row['dr_charge']));
					$objpf->setIsExcludedFlag(($row['is_excluded'] != 0));
					$objpf->setOpCodes($row['opcodes']);
					//added by jasper 09/01/2013 - FOR BUG#302 SURGEON'S PF IS NOT DISCOUNTABLE
					//FOR PATIENTS WITHOUT PHIC IN OBANNEX
					$opcodes = $row['opcodes'];
					if ($opcodes != '') {
					    $opcodes = explode(";", $opcodes);
					    if (is_array($opcodes)) {
						foreach($opcodes as $v) {
						    $i = strpos($v, '-');
						    if (!($i === false)) {
							$code = substr($v, 0, $i);
							if ($row['role_area'] == 'D3' && $this->findOPcodeNormalDelivery($code) && !$this->isPHIC() && $this->isOBAnnex()) {
							   $this->nonDiscountablePF += $row['dr_charge'];
							}		
						    }
						}
					    } else {
						$i = strpos($opcodes, '-');
						if (!($i === false)) {
						    $code = substr($opcodes, 0, $i);
						    if ($row['role_area'] == 'D3' && $this->findOPcodeNormalDelivery($code) && !$this->isPHIC() && $this->isOBAnnex()) {
						       $this->nonDiscountablePF += $row['dr_charge'];
						    }		
						}    
					    }
					}
					//added by jasper 09/01/2013 - FOR BUG#302
					// Add new Service object in collection (array) of doctors' fees charged in this billing.
					$this->proffees_list[] = $objpf;

          $indx++;
				}

        if (!$bhasD4 && ($d3indx != -1)) {
          $this->proffees_list[$d3indx]->setDrCharge( $this->proffees_list[$d3indx]->getDrCharge() + ($amountlimit * $this->getCaseRatePkgLimit('D4', $issurgical)) );
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

		$toDate = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($this->bill_dte)));

//		if ($_SESSION['sess_temp_userid']=='medocs')
//			$time_start = microtime(true);

		$filter = array('','');

		$this->hsp_service_benefits = array();
		$this->valid_covered_items = array();

		// Get all the services charged to current encounter ...
		if ($this->prev_encounter_nr != '') $filter[0] = " or encounter_nr = '$this->prev_encounter_nr'";
		if ($this->prev_encounter_nr != '') $filter[1] = " or sos.encounter_nr = '$this->prev_encounter_nr'";
		#edited by VAS 03-22-2012
        #add a filtering for deleted status under detail table   at line 1720
		$strSQL = "select ld.service_code, ls.name as service_desc, ls.group_code, " .
							"   lsg.name as group_desc, sum(ld.quantity) as qty, (sum(ld.price_charge * ld.quantity)/sum(ld.quantity)) as serv_charge, sum(ld.price_charge * ld.quantity) as serv_total, 'LB' as source " .
					"   from ((seg_lab_serv as lh inner join seg_lab_servdetails as ld on lh.refno = ld.refno) " .
					"          inner join seg_lab_services as ls on ld.service_code = ls.service_code) " .
					"          inner join seg_lab_service_groups as lsg on ls.group_code = lsg.group_code " .
					"      WHERE (CASE WHEN serv_dt >= DATE('".ISSRVD_EFFECTIVITY."') THEN ld.is_served ELSE 1 END) AND ".
					"      /* where (ld.is_served <> 0 and) */ UPPER(TRIM(ld.STATUS)) <> 'DELETED' AND lh.is_cash = 0 and (ld.request_flag is null OR ld.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
					"         and (encounter_nr = '" . $this->current_enr. "'".$filter[0].") and upper(trim(lh.status)) <> 'DELETED' " .
					"         and CAST(CONCAT(serv_dt, ' ', serv_tm) AS DATETIME) BETWEEN CAST('". $this->bill_frmdte ."' AS DATETIME) AND CAST('". $toDate ."' AS DATETIME) " .
					"   group by ld.service_code, ls.name, ls.group_code, lsg.name, source";
		$this->putinservice_benefits($strSQL);

        #edited by VAS 03-22-2012
        #add a filtering for deleted status under detail table   at line 1733
		$strSQL = "select rd.service_code, rs.name as service_desc, rs.group_code, " .
							"   rsg.name as group_desc, count(rd.service_code) as qty, (sum(rd.price_charge)/count(rd.service_code)) as serv_charge, sum(rd.price_charge) as serv_total, 'RD' as source " .
					"   from ((seg_radio_serv as rh inner join care_test_request_radio as rd on rh.refno = rd.refno) " .
					"          inner join seg_radio_services as rs on rd.service_code = rs.service_code) " .
					"          inner join seg_radio_service_groups as rsg on rs.group_code = rsg.group_code " .
					"      WHERE (CASE WHEN rh.request_date >= DATE('".ISSRVD_EFFECTIVITY."') THEN rd.is_served ELSE 1 END) AND ".
					"      /* where upper(rd.status) = 'DONE' and*/ UPPER(TRIM(rd.STATUS)) <> 'DELETED' AND rh.is_cash = 0 and (rd.request_flag is null OR rd.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
					"         and (encounter_nr = '" . $this->current_enr. "'".$filter[0].") and upper(trim(rh.status)) <> 'DELETED' and upper(trim(rd.status)) <> 'DELETED' " .
					"         and CAST(CONCAT(rh.request_date, ' ', rh.request_time) AS DATETIME) BETWEEN CAST('". $this->bill_frmdte ."' AS DATETIME) AND CAST('". $toDate ."' AS DATETIME) " .
					"   group by rd.service_code, rs.name, rs.group_code, rsg.name, source";
		$this->putinservice_benefits($strSQL);

//		$strSQL = "select pd.bestellnum as service_code, artikelname as service_desc, 'SU' as group_code, 'Supplies' as group_desc, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as serv_charge, sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0))) as serv_total, 'SU' as source ".
//					"   from ((seg_pharma_orders as ph inner join
//								(select * from seg_pharma_order_items d
//										where d.serve_status <> 'N' and d.request_flag is null) as pd on ph.refno = pd.refno) ".
//					"      inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'S') ".
//					"      left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum ".
//					"   where (encounter_nr = '". $this->current_enr. "'".$filter[0].") and is_cash = 0 ".
//					"      and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
//					"      and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
//					"      and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
//							"   group by pd.bestellnum, artikelname";


/**
* Query tuning - old query is joining from a subquery of a table scan of seg_pharma_order_items
* filtered only by d.serve_status and d.request_flag. The alternative is to JOIN to the
* seg_pharma_order_items table which makes use of indices. Testing reports increased query speed
* from 2s-3.7s to 0.12s-0.15s.
* @author Alvin
*/

		$strSQL = "SELECT p.bestellnum AS service_code, p.artikelname AS service_desc, 'SU' AS group_code,\n".
			"'Supplies' AS group_desc,\n".
			"SUM(oi.quantity - IFNULL(ri.quantity, 0)) AS qty,\n".
			"(SUM(oi.pricecharge * (oi.quantity - IFNULL(ri.quantity, 0)))/SUM(oi.quantity - IFNULL(ri.quantity, 0))) AS serv_charge,\n".
			"SUM(pricecharge * (oi.quantity - IFNULL(ri.quantity, 0))) AS serv_total,\n".
			"'SU' AS source\n".
		"FROM seg_pharma_order_items oi\n".
			"INNER JOIN seg_pharma_orders o ON oi.refno=o.refno\n".
			"INNER JOIN care_pharma_products_main p ON p.bestellnum=oi.bestellnum AND p.prod_class = 'S'\n".
			"LEFT JOIN (SELECT rd.ref_no, rd.bestellnum, SUM(quantity) AS quantity
																	FROM seg_pharma_return_items AS rd INNER JOIN seg_pharma_returns AS rh
																		 ON rd.return_nr = rh.return_nr AND (rh.encounter_nr = '". $this->current_enr. "'".$filter[0].")
																	WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = '". $this->current_enr. "'".$filter[0].") AND rd.ref_no = oh.refno)
										GROUP BY rd.ref_no, rd.bestellnum) ri ON ri.ref_no=oi.refno AND ri.bestellnum=oi.bestellnum\n".
			"WHERE\n".
				"(encounter_nr = '". $this->current_enr. "'".$filter[0].")\n".
				"AND o.is_cash = 0\n".
				"AND oi.request_flag IS NULL\n".
				"AND oi.serve_status<>'N'\n".
				"AND o.orderdate BETWEEN CAST(".$db->qstr($this->bill_frmdte)." AS DATETIME) AND CAST(".$db->qstr($toDate)." AS DATETIME) \n".
				"AND (oi.quantity - IFNULL(ri.quantity, 0)) > 0\n".
			"GROUP BY oi.bestellnum, p.artikelname";

		$this->putinservice_benefits($strSQL);

		$strSQL = "select mphd.bestellnum as service_code, artikelname as service_desc, 'MS' as group_code, 'Supplies' as group_desc, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as serv_charge, sum(unit_price * quantity) as serv_total, 'MS' as source ".
					"   from (seg_more_phorder_details as mphd inner join seg_more_phorder as mph on mphd.refno = mph.refno) ".
					"      inner join care_pharma_products_main as p on mphd.bestellnum = p.bestellnum and p.prod_class = 'S' ".
					"   where (encounter_nr = '". $this->current_enr. "'".$filter[0].") ".
					"      and (str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
					"         and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
							"   group by mphd.bestellnum, artikelname";
		$this->putinservice_benefits($strSQL);

		$strSQL = "select eqd.equipment_id as service_code, artikelname as service_desc, '' as group_code, 'Equipment' as group_desc, sum(number_of_usage) as qty, (sum(discounted_price * number_of_usage)/sum(number_of_usage)) as serv_charge, sum(discounted_price * number_of_usage) as serv_total, 'OE' as source
						 from ((seg_equipment_orders as eqh inner join seg_equipment_order_items as eqd on eqh.refno = eqd.refno)
						 left join seg_ops_serv as sos on sos.refno = eqh.request_refno) inner join care_pharma_products_main as
						 cppm on cppm.bestellnum = eqd.equipment_id
						 where (eqh.encounter_nr = '". $this->current_enr. "'".$filter[1].")
							and (str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "'
							and str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "')
								 group by eqd.equipment_id, artikelname";
		$this->putinservice_benefits($strSQL);

		$strSQL = "select md.service_code, ms.name as service_desc, '' as group_code, ".
							"      '' as group_desc, sum(md.quantity) as qty, (sum(chrg_amnt * md.quantity)/sum(md.quantity)) as serv_charge, sum(chrg_amnt * md.quantity) as serv_total, 'OA' as source ".
					"   from (seg_misc_service as m inner join seg_misc_service_details as md on m.refno = md.refno) ".
					"      inner join seg_other_services as ms on md.service_code = ms.alt_service_code ".
					"   where (encounter_nr = '" . $this->current_enr. "'".$filter[0].") and md.request_flag is null and is_cash = 0".
					"      and (str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
					"      and str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
					"   group by md.service_code, ms.name";
		$this->putinservice_benefits($strSQL);

		$this->clearInvalidItemsFromCoverage(false);

//		if ($result = $db->Execute($strSQL)) {
//			$this->hsp_service_benefits = array();
//			$this->valid_covered_items = array();

//			if ($result->RecordCount()) {
//				while ($row = $result->FetchRow()) {
//					$objServ = new PerServiceCoverage;

//					$objServ->setBillDte($this->bill_dte);
//					$objServ->setCurrentEncounterNr($this->current_enr);
//					$objServ->setPrevEncounterNr($this->prev_encounter_no);
//					$objServ->setServiceCode($row['service_code']);
//					$objServ->setServiceDesc($row['service_desc']);
//					$objServ->setGroupCode($row['group_code']);
//					$objServ->setGroupDesc($row['group_desc']);
//					$objServ->setServQty($row['qty']);
//					$objServ->setServPrice($row['serv_charge']);
//					$objServ->setServProvider($row['source']);

//					$objServ->computeTotalCoverage($this->getBillAreaDRate('HS'));

					// Add new Service object in collection (array) of services charged in this billing.
//					$this->hsp_service_benefits[] = $objServ;

//					if ($this->old_bill_nr == '') {
//							$this->getValidItemWithAppliedCoverage($this->current_enr, $row['source'], $row['service_code'], ($row['qty'] * $row['serv_charge']));
//					}
//				} // ... while loop
//			}	  // ... if ... recordcount
//			else
//				$this->errmsg = "No laboratory service!";

//			$this->clearInvalidItemsFromCoverage(false);
//		}	      // ... if ... execute

//		if ($_SESSION['sess_temp_userid']=='medocs')
//		{
//			$time_end = microtime(true);
//			$this->logger->debug('getServiceBenefits ['.($time_end-$time_start).']');
//		}
	}

	function putinservice_benefits($strSQL) {
		global $db;

		if ($result = $db->Execute($strSQL)) {
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
							$this->getValidItemWithAppliedCoverage($this->current_enr, $row['source'], $row['service_code'], $row['serv_total']);
					}
				} // ... while loop
			}	  // ... if ... recordcount
		}
//		if ($_SESSION['sess_temp_userid']=='medocs')
//		{
//			$time_end = microtime(true);
//			$this->logger->info('['.($time_end-$time_start).'] '.$strSQL);
//		}
	}

	function getMedicineBenefits() {
		global $db;

		$toDate = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($this->bill_dte)));

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



//		if ($this->prev_encounter_nr != '') $filter[0] = " or encounter_nr = '$this->prev_encounter_nr'";
//		if ($this->prev_encounter_nr != '') $filter[1] = " or si.encounter_nr = '$this->prev_encounter_nr'";
//		$strSQL = "select bestellnum, artikelname, max(flag) as flag, sum(qty) as qty, (sum(price * qty)/sum(qty)) as price, sum(itemcharge) as itemcharge ".
//					" from ".
//					"(select 0 as flag, pd.bestellnum, (case when (isnull(generic) or (generic = '')) then artikelname else generic end) as artikelname, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as price, sum((pd.quantity - ifnull(spri.quantity, 0)) * pricecharge) as itemcharge ".
//					"   from ((seg_pharma_orders as ph inner join
//								(select * from seg_pharma_order_items d
//										where d.serve_status <> 'N' and d.request_flag is null) as pd on ph.refno = pd.refno) ".
//					"         inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'M') ".
//					"         left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum ".
//					"      where (((encounter_nr = '". $this->current_enr. "'".$filter[0].") and is_cash = 0 ".
//					"         and exists (select * from (seg_hcare_products as shp inner join seg_hcare_bsked as shb ".
//					"                           on shp.bsked_id = shb.bsked_id) inner join seg_encounter_insurance as si on shb.hcare_id = si.hcare_id ".
//					"                        where shp.bestellnum = pd.bestellnum and ".
//					"                           str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
//					"                           and (select max(effectvty_dte) as latest ".
//					"                                   from seg_hcare_bsked as shb2 ".
//					"                                   where str_to_date(shb2.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
//					"                                      and shb2.hcare_id = shb.hcare_id ".
//					"                                      and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte and	".
//					"                           (si.encounter_nr = '". $this->current_enr. "'".$filter[1]."))) ".
//					"         or ((encounter_nr = '". $this->current_enr. "'".$filter[0].") and is_cash = 0)) " .
//					"        and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
//					"           and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
//					"        and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
//					"   group by pd.bestellnum ".
//					" union ".
//					"select 1 as flag, mpd.bestellnum, (case when (isnull(generic) or (generic = '')) then artikelname else generic end) as artikelname, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as price, sum(quantity * unit_price) as itemcharge ".
//					"   from (seg_more_phorder as mph inner join seg_more_phorder_details as mpd on mph.refno = mpd.refno) ".
//					"      inner join care_pharma_products_main as p on mpd.bestellnum = p.bestellnum and p.prod_class = 'M' ".
//					"   where (((encounter_nr = '". $this->current_enr. "'".$filter[0].") ".
//					"      and exists (select * from (seg_hcare_products as shp inner join seg_hcare_bsked as shb ".
//					"                        on shp.bsked_id = shb.bsked_id) inner join seg_encounter_insurance as si on shb.hcare_id = si.hcare_id ".
//					"                     where shp.bestellnum = mpd.bestellnum and ".
//					"                        str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
//					"                        and (select max(effectvty_dte) as latest ".
//					"                                from seg_hcare_bsked as shb2 ".
//					"                                where str_to_date(shb2.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
//					"                                   and shb2.hcare_id = shb.hcare_id ".
//					"                                   and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte and ".
//					"                        (si.encounter_nr = '". $this->current_enr. "'".$filter[1]."))) ".
//					"         or ((encounter_nr = '". $this->current_enr. "'".$filter[0]."))) ".
//					"        and (str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
//					"           and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
//					"   group by mpd.bestellnum) as t ".
//					" group by bestellnum, artikelname order by artikelname";


if ($this->prev_encounter_nr != '') $filter1 = " OR ph.encounter_nr = '$this->prev_encounter_nr'";
if ($this->prev_encounter_nr != '') $filter2 = " OR mph.encounter_nr = '$this->prev_encounter_nr'";
if ($this->prev_encounter_nr != '') $filter3 = " OR encounter_nr = '$this->prev_encounter_nr'";
$strSQL = "SELECT bestellnum, artikelname, MAX(flag) AS flag, SUM(qty) AS qty,\n".
		"(SUM(price * qty)/SUM(qty)) AS price,\n".
		"SUM(itemcharge) AS itemcharge\n".
	"FROM (\n".

	"SELECT 0 AS flag, pd.bestellnum,\n".
		"(CASE WHEN (ISNULL(generic) OR (generic = '')) THEN artikelname ELSE generic END) AS artikelname,\n".
		"SUM(pd.quantity - IFNULL(spri.quantity, 0)) AS qty,\n".
		"(SUM(pricecharge * (pd.quantity - IFNULL(spri.quantity, 0)))/SUM(pd.quantity - IFNULL(spri.quantity, 0))) AS price,\n".
		"SUM((pd.quantity - IFNULL(spri.quantity, 0)) * pricecharge) AS itemcharge\n".
	"FROM seg_pharma_order_items AS pd\n".
		"INNER JOIN seg_pharma_orders AS ph ON ph.refno = pd.refno\n".
		"INNER JOIN care_pharma_products_main AS p ON pd.bestellnum = p.bestellnum \n".
		"LEFT JOIN (SELECT rd.ref_no, rd.bestellnum, SUM(quantity) AS quantity
																	FROM seg_pharma_return_items AS rd INNER JOIN seg_pharma_returns AS rh
																		 ON rd.return_nr = rh.return_nr AND (rh.encounter_nr = '". $this->current_enr. "'".$filter3.")
																	WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = '". $this->current_enr. "'".$filter3.") AND rd.ref_no = oh.refno)
										GROUP BY rd.ref_no, rd.bestellnum) AS spri ON pd.refno = spri.ref_no AND pd.bestellnum = spri.bestellnum\n".
	"WHERE\n".
		"pd.serve_status <> 'N' AND pd.request_flag IS NULL AND !ph.is_cash AND p.prod_class = 'M'\n".
		"AND (ph.encounter_nr = '". $this->current_enr. "'".$filter1.")\n".
		"AND (ph.orderdate BETWEEN CAST('".$this->bill_frmdte."' AS DATETIME) AND CAST('".$toDate."' AS DATETIME))\n".
		"AND (pd.quantity - IFNULL(spri.quantity, 0)) > 0\n".
	"GROUP BY pd.bestellnum\n".

	"UNION ALL\n".

	"SELECT 1 AS flag, mpd.bestellnum,\n".
		"(CASE WHEN (ISNULL(generic) OR (generic = '')) THEN artikelname ELSE generic END) AS artikelname,\n".
		"SUM(quantity) AS qty,\n".
		"(SUM(unit_price * quantity)/SUM(quantity)) AS price,\n".
		"SUM(quantity * unit_price) AS itemcharge\n".
	"FROM seg_more_phorder AS mph\n".
		"INNER JOIN seg_more_phorder_details AS mpd ON mph.refno = mpd.refno\n".
		"INNER JOIN care_pharma_products_main AS p ON mpd.bestellnum = p.bestellnum AND p.prod_class = 'M'\n".
	"WHERE\n".
		"(mph.encounter_nr = '". $this->current_enr. "'".$filter2.")\n".
		"AND (mph.chrge_dte BETWEEN CAST('".$this->bill_frmdte."' AS DATETIME) AND CAST('".$toDate."' AS DATETIME))\n".
	"GROUP BY mpd.bestellnum\n".

	") AS t\n".
	"GROUP BY bestellnum, artikelname ORDER BY artikelname\n";

		if ($result = $db->Execute($strSQL)) {
			$this->med_product_benefits = array();
			$this->valid_covered_items = array();
			$this->temp_valid_items    = array();

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
		$strSQL = "select ops_code, opcode, description, provider, sum(rvu) as sum_rvu,
								fn_getOPRvuRate('$this->current_enr', date('".$this->bill_dte."'), sum(rvu), $this->confinetype_id) as op_multiplier,
		/* (sum(multiplier * rvu)/sum(rvu)) as op_multiplier ,*/ sum(op_charge) as tot_charge ".
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
					"      concat('OR-', cast(oad.room_nr as char)) as ops_code,
								 fn_getopcode(oah.refno, oad.entry_no) AS opcode,
								 concat((select ifnull(name, '') from care_ward where nr = oad.group_nr), '- Room ', cast(cr.room_nr as char)) as description, ".
					"      (select ifnull(sum(rvu), 0) as trvu from seg_ops_chrgd_accommodation as soca where soca.refno = oah.refno and soca.entry_no = oad.entry_no) as rvu,
								(select multiplier from seg_ops_chrgd_accommodation as soca2 where soca2.refno = oah.refno and soca2.entry_no = oad.entry_no limit 1) as multiplier, oad.charge as op_charge, 'RU' as provider ".
					"   from (seg_opaccommodation as oah inner join seg_opaccommodation_details as oad on oah.refno = oad.refno) ".
					"      inner join care_room as cr on oad.room_nr = cr.nr ".
					"   where (encounter_nr = '". $this->current_enr ."'".$filter.") and (str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
					"      and str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."')) as t ".
					"group by provider, ops_code, description, opcode, entry_no
					 order by ops_code";    // modified by LST - 11.12.2011 --- Issue (from SOW 10-001)

		if ($result = $db->Execute($strSQL)) {
			$this->hsp_ops_benefits = array();

			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					$objOp = new PerOpCoverage;

					$objOp->setBillDte($this->bill_dte);
					$objOp->setCurrentEncounterNr($this->current_enr);
					$objOp->setPrevEncounterNr($this->prev_encounter_no);
					$objOp->setOpCode($row['ops_code']);
					$objOp->setOpCodePerformed($row['opcode']);
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
		$bill_date = $this->bill_dte;
		$filter = array('','','');

    $issurgical  = $this->isSurgicalCase();
    //added by jasper 09/03/2013 FOR BUG#305
    if ($this->getPackageName() == DEFAULT_NBPKG_NAME) {
        $amountlimit = DEFAULT_NBPKG_RATE;
    } else {
    $amountlimit = $this->getPkgAmountLimit();
    }
    //added by jasper 09/03/2013 FOR BUG#305

    $hc_pf = $this->getHouseCasePCF();

		if ($this->prev_encounter_nr != '') $filter[0] = " or dm1.encounter_nr = '$this->prev_encounter_nr'";
		if ($this->prev_encounter_nr != '') $filter[1] = " or spd.encounter_nr = '$this->prev_encounter_nr'";
		if ($this->prev_encounter_nr != '') $filter[2] = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select dr_nr, role_area, role_type_level, opcodes, sum(num_days) as totaldays, sum(rvu) as totalrvu, (sum(multiplier * rvu)/sum(rvu)) as avgmuliplier, sum(dr_charge) as totalcharge, ".
					"\n     sum(case when is_excluded <> 0 then 0 else dr_charge end) as chrg_for_coverage ".
					"\n  from ".
					"\n  (select attending_dr_nr as dr_nr, name_last, name_first, name_middle, 'Attending Doctor' as role, ".
					"\n   sum(fn_days_attended(attend_start, if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), ".$this->cutoff_hrs.")) as num_days, daily_rate, ".
					"\n   '' as opcodes, daily_rate * sum(fn_days_attended(attend_start, if(isnull(attend_end), if(isnull(discharge_date) or discharge_date = '0000-00-00', str_to_date('".$tmp_dte."', '%Y-%m-%d %H:%i:%s'), discharge_date), attend_end), ".$this->cutoff_hrs.")) as dr_charge, ".
					"\n   0 as is_excluded, role_nr, role_area, 0 as role_type_level, 0 as rvu, 0 as multiplier ".
					"\n   from ".
					"\n      (select distinct attending_dr_nr, name_last, name_first, name_middle, attend_start, ".
					"\n          subdate((select attend_start ".
					"\n                      from seg_encounter_dr_mgt as dm2 ".
					"\n                      where dm2.encounter_nr = dm1.encounter_nr and ".
					"\n                            dm2.att_hist_no > dm1.att_hist_no ".
					"\n                      order by dm2.att_hist_no asc limit 1), 1) as attend_end, fn_getdailyrate('{$this->current_enr}', date('{$this->bill_dte}'), tier_nr, {$this->confinetype_id}, attending_dr_nr) as daily_rate, cpa.role_nr, fn_getDailyVisitRoleArea(tier_nr) as role_area, discharge_date ".
					"\n          from (seg_encounter_dr_mgt as dm1 inner join (((care_personell as cpn ".
					"\n             inner join care_person as cp on cpn.pid = cp.pid) inner join care_personell_assignment as cpa ".
					"\n             on cpn.nr = cpa.personell_nr) inner join care_role_person as crp on ".
					"\n             cpa.role_nr = crp.nr) on dm1.attending_dr_nr = cpn.nr) inner join care_encounter as ce ".
					"\n             on dm1.encounter_nr = ce.encounter_nr ".
					"\n          where (dm1.encounter_nr = '" . $this->current_enr. "'".$filter[0].") " .
					"\n             and (str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"\n                and str_to_date(dm1.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $bill_date . "') " .
					"\n          order by att_hist_no) as t ".
					"\n   group by attending_dr_nr, role_area, role_nr ".
					"\n union ".
					"\nselect distinct spd.dr_nr, name_last, name_first, name_middle, concat(name, ' - private') as role, (case when is_excluded <> 0 then 0 else spd.days_attended end) as num_days, 0 as daily_rate, GROUP_CONCAT(DISTINCT CONCAT(socd.ops_code, '-', IFNULL(socd.rvu,0)) SEPARATOR ';') AS opcodes, ".
					"\n      (CASE WHEN NOT ".($this->is_coveredbypkg ? "1" : "0")." THEN SUM(ifnull(socd.rvu,0) * IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$bill_date."'), role_area, ifnull(role_type_level, tier_nr), ifnull(socd.rvu,0), $this->confinetype_id, spd.dr_nr), ifnull(socd.multiplier,0))) * fn_getrvuadjustment('$this->current_enr', date('".$bill_date."'), role_area, ifnull(role_type_level, tier_nr), ifnull(socd.rvu,0), $this->confinetype_id)) ELSE {$amountlimit} * IF(is_excluded OR ".($this->isfreedist ? "1" : "0").", 0, fn_getcaseratepkglimit(role_area, ".($issurgical ? '1' : '0').", DATE('".$bill_date."'))) END) + dr_charge as dr_charge, is_excluded, ".
					"\n      spd.dr_role_type_nr, role_area, IFNULL(role_type_level, IFNULL(dr_level, tier_nr)) as role_type_level, sum(ifnull(socd.rvu,0)) as tot_rvu, (sum(IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$bill_date."'), role_area, ifnull(role_type_level, tier_nr), ifnull(socd.rvu,0), $this->confinetype_id, spd.dr_nr), ifnull(socd.multiplier,0))) * ifnull(socd.rvu,0))/sum(ifnull(socd.rvu,0))) as avg_multiplier ".
					"\n   from ((seg_encounter_privy_dr as spd left join seg_ops_chrg_dr as socd on ".
					"\n      spd.encounter_nr = socd.encounter_nr and spd.dr_nr = socd.dr_nr and ".
					"\n      spd.dr_role_type_nr = socd.dr_role_type_nr) inner join (care_personell as cpn ".
					"\n      inner join care_person as cp on cpn.pid = cp.pid) on spd.dr_nr = cpn.nr) ".
					"\n      inner join care_role_person as crp on spd.dr_role_type_nr = crp.nr ".
					"\n   where (spd.encounter_nr = '" . $this->current_enr. "'".$filter[1].") ".
					"\n      and (str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
					"\n      and str_to_date(spd.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $bill_date . "') ".
					"\n   group by spd.dr_nr, role_area, dr_role_type_nr, spd.entry_no ".
					"\n union ".
					"\n   select dr_nr, name_last, name_first, name_middle, concat(name, ' - ', cop.code) as role, null as num_days, 0 as daily_rate, GROUP_CONCAT(DISTINCT CONCAT(sosd.ops_code,'-',IFNULL(sosd.rvu,0)) SEPARATOR ';') AS opcodes, ".
					"\n         (CASE WHEN NOT ".($this->is_coveredbypkg ? "1" : "0")." THEN SUM(ifnull(sosd.rvu,0) * IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$bill_date."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id, dr_nr), ifnull(multiplier,0))) * fn_getrvuadjustment('$this->current_enr', date('".$bill_date."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id)) ELSE {$amountlimit} * IF(".($this->isfreedist ? "1" : "0").", 0, fn_getcaseratepkglimit(role_area, ".($issurgical ? '1' : '0').", DATE('".$bill_date."'))) END) + ops_charge as dr_charge, ".
					"\n         0 as is_excluded, sop.role_type_nr, role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), sum(sosd.rvu) as tot_rvu, (sum(IF(".$hc_pf.", ".$hc_pf.", ifnull(fn_getPCF('$this->current_enr', date('".$bill_date."'), role_area, if(ifnull(role_type_level, 0) = 0, fn_getDocTier(dr_nr), role_type_level), ifnull(sosd.rvu,0), $this->confinetype_id, dr_nr), ifnull(multiplier,0))) * sosd.rvu)/sum(sosd.rvu)) as avg_multiplier ".
					"\n      from (((seg_ops_personell as sop inner join (care_personell as cpn ".
					"\n         inner join care_person as cp on cpn.pid = cp.pid) on sop.dr_nr = cpn.nr) ".
					"\n         inner join (seg_ops_serv as sos inner join
								(SELECT sd.refno, ops_code, rvu, multiplier, group_code
								FROM seg_ops_servdetails AS sd INNER JOIN seg_ops_serv AS sh
									ON sd.refno = sh.refno
								WHERE sh.encounter_nr = '$this->current_enr'
									HAVING (rvu = (SELECT MAX(rvu) AS rvumax
													FROM seg_ops_servdetails AS d
													WHERE d.refno = sd.refno AND d.group_code = sd.group_code)
										 AND sd.group_code <> '') OR sd.group_code = '') as sosd ".
					"\n            on sos.refno = sosd.refno) on sop.refno = sos.refno) ".
					"\n         inner join care_role_person as crp on sop.role_type_nr = crp.nr) ".
					"\n         inner join seg_ops_rvs as cop on sop.ops_code = cop.code ".
					"\n      where (encounter_nr = '" . $this->current_enr. "'".$filter[2].") and upper(trim(sos.status)) <> 'DELETED' ".
					"\n         and (str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"\n            and str_to_date(sop.create_dt, '%Y-%m-%d %H:%i:%s') < '" . $bill_date . "') " .
					"\n         and role_area is not null and crp.role not like '%_asst%' " .
					"\n               and sosd.ops_code = sop.ops_code ".
					"\n      group by dr_nr, role_area, role_type_nr) ".
					"\n as o group by role_area, dr_nr";

//		if ($_SESSION['sess_temp_userid']=='medocs')
//		{
//			$time_end = microtime(true);
//			$this->logger->debug('getProfFeesBenefits ['.($time_end-$time_start).'] '.$strSQL);
//		}

		if ($result = $db->Execute($strSQL)) {
			$this->hsp_pfs_benefits = array();
			$this->pfs_confine_coverage = array();

			if ($result->RecordCount()) {
                $bhasD4 = false;
                $d3indx = -1;
                $indx = 0;

				while ($row = $result->FetchRow()) {
					$objpfc = new ProfFeeCoverage;

                    if ($row['role_area'] == 'D4') $bhasD4 = true;
                    if ($row['role_area'] == 'D3' && ($row['chrg_for_coverage'] > 0)) $d3indx = $indx;

					$objpfc->setDrNr($row['dr_nr']);
					$objpfc->setRoleBenefit($row['role_area']);
					$objpfc->setRoleLevel((is_null($row['role_type_level']) ? 0 : $row['role_type_level']));
					$objpfc->getRoleDesc();
					if (is_null($row['totaldays']))
						$objpfc->setDaysAttended(0);
					else
						$objpfc->setDaysAttended($row['totaldays']);
					
                    //added by jasper 09/03/2013 -FOR BUG#302
                    //if ($this->isWellBaby()) {//&& $this->is_coveredbypkg && ($this->package_id == NEWBORN_A || $this->package_id == NEWBORN_B)) {
                    //    $objpfc->setDrCharge(525);
                    //} else {                      
					$objpfc->setDrCharge($row['totalcharge']);
                    //}
                    //added by jasper 09/03/2013 -FOR BUG#302
                    
					$objpfc->setRVU($row['totalrvu']);
					$objpfc->setMultiplier($row['avgmuliplier']);
					$objpfc->setChrgForCoverage($row["chrg_for_coverage"]);
					$objpfc->setOpCodes($row['opcodes']);

					// Add new object in collection (array) of doctors' fees charged in this billing.
					$this->hsp_pfs_benefits[] = $objpfc;

                    $indx++;
				}

                if (!$bhasD4 && ($d3indx != -1)) {
                    $this->hsp_pfs_benefits[$d3indx]->setDrCharge( $this->hsp_pfs_benefits[$d3indx]->getDrCharge() + ($amountlimit * $this->getCaseRatePkgLimit('D4', $issurgical)) );
                    $this->hsp_pfs_benefits[$d3indx]->setChrgForCoverage( $this->hsp_pfs_benefits[$d3indx]->getChrgForCoverage() + ($amountlimit * $this->getCaseRatePkgLimit('D4', $issurgical)) );
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

		if (!empty($this->acc_roomtype_benefits) && is_array($this->acc_roomtype_benefits))
			foreach($this->acc_roomtype_benefits as $objrmtyp) {
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
		$this->total_acc_charge = $nCharge; //added by jasper 09/12/2013 FOR SENIOR CITIZEN DISCOUNT AND PAYWARD ACCOMMODATION
		return $nCharge;
	}

    function compTotalAccommodationCharity() {
        $nCharge = 0;

        if (!empty($this->acc_roomtype_benefits) && is_array($this->acc_roomtype_benefits))
            foreach($this->acc_roomtype_benefits as $objrmtyp) {
                if ($objrmtyp->getAccomodationType()==CHARITYWARD) {
                    $nCharge += $objrmtyp->getActualCharge();
                }
            }

        return $nCharge;
    }

    function getTotalMandatoryExcess() {
		$nExcess = 0;
		if (!empty($this->acc_roomtype_benefits) && is_array($this->acc_roomtype_benefits)) {
			foreach($this->acc_roomtype_benefits as $objrmtyp) {
				$nExcess += $objrmtyp->getExcess() * $objrmtyp->getDaysCount();
			}
        }
		return $nExcess;
    }

	function isPersonPrincipal($n_hcareid) {
		global $db;

		$this->bPrincipal = false;
		$filter = '';

		if ($this->prev_encounter_nr != '') $filter = " or encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select is_principal ".
					"   from care_person_insurance as cpi inner join care_encounter as ce on cpi.pid = ce.pid ".
					"   where (encounter_nr = '". $this->current_enr. "'".$filter.") and is_void = 0 ".
					"      and hcare_id = ". $n_hcareid;

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

		if (!empty($this->acc_roomtype_benefits) && is_array($this->acc_roomtype_benefits))
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

		if (!empty($this->hsp_service_benefits) && is_array($this->hsp_service_benefits))
			foreach($this->hsp_service_benefits as $objhsp) {
				if (!empty($objhsp->available_limitedhplans) && is_array($objhsp->available_limitedhplans))
					foreach($objhsp->available_limitedhplans as $objsrv) {
						if ($objsrv->getID() == $nhcare_id)
							$ntotal += $objsrv->getCoverage();
					}
			}

		return($ntotal);
	}

	function getTotalMscCoverage($nhcare_id) {
		$ntotal = 0;

		if (!empty($this->hsp_msc_benefits) && is_array($this->hsp_msc_benefits))
			foreach($this->hsp_msc_benefits as $objb) {
				if (!empty($objb->available_limitedhplans) && is_array($objb->available_limitedhplans))
					foreach($objb->available_limitedhplans as $objmsc) {
						if ($objmsc->getID() == $nhcare_id)
							$ntotal += $objmsc->getCoverage();
					}
			}

		return($ntotal);
	}

	function getTotalMedCoverage($nhcare_id) {
		$ntotal = 0;

		if (!empty($this->med_product_benefits) && is_array($this->med_product_benefits))
			foreach($this->med_product_benefits as $objmb) {
				if (!empty($objmb->available_limitedhplans) && is_array($objmb->available_limitedhplans))
					foreach($objmb->available_limitedhplans as $objmed) {
						if ($objmed->getID() == $nhcare_id)
							$ntotal += $objmed->getCoverage();
					}
			}

		return($ntotal);
	}

	function getTotalSupCoverage($nhcare_id) {
		$ntotal = 0;

		if (!empty($this->sup_product_benefits) && is_array($this->sup_product_benefits))
			foreach($this->sup_product_benefits as $objsb) {
				if (!empty($objsb->available_limitedhplans) && is_array($objsb->available_limitedhplans))
					foreach($objsb->available_limitedhplans as $objsup) {
						if ($objsup->getID() == $nhcare_id)
							$ntotal += $objsup->getCoverage();
					}
			}

		return($ntotal);
	}

	function getTotalOpCoverage($nhcare_id) {
		$ntotal = 0;

		if (!empty($this->hsp_ops_benefits) && is_array($this->hsp_ops_benefits))
			foreach($this->hsp_ops_benefits as $objOp) {
				if (!empty($objOp->available_limitedhplans) && is_array($objOp->available_limitedhplans))
					foreach($objOp->available_limitedhplans as $objOpCare) {
						if ($objOpCare->getID() == $nhcare_id)
							$ntotal += $objOpCare->getCoverage();
					}
			}

		return($ntotal);
	}

	function getTotalSrvCharge() {
		global $db;

		if (isset($this->total_srv_charge) && !$this->forceCompute) return $this->total_srv_charge;

		$ntotal = 0;
		$filter = array('','');

		// Get all the services charged to current encounter ...
		if ($this->prev_encounter_nr != '') $filter[0] = " or encounter_nr = '$this->prev_encounter_nr'";
		if ($this->prev_encounter_nr != '') $filter[1] = " or sos.encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select sum(qty * serv_charge) as total_charge from " .
					"(select ld.service_code, sum(ld.quantity) as qty, (sum(ld.price_charge * ld.quantity)/sum(ld.quantity)) as serv_charge, 'LB' as source " .
					"   from ((seg_lab_serv as lh inner join seg_lab_servdetails as ld on lh.refno = ld.refno) " .
					"          inner join seg_lab_services as ls on ld.service_code = ls.service_code) " .
					"          inner join seg_lab_service_groups as lsg on ls.group_code = lsg.group_code " .
					"      WHERE (CASE WHEN serv_dt >= DATE('".ISSRVD_EFFECTIVITY."') THEN ld.is_served ELSE 1 END) AND ".
					"      /* where (ld.is_served <> 0) and */ UPPER(TRIM(ld.STATUS)) <> 'DELETED' AND lh.is_cash = 0 and (ld.request_flag is null OR ld.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
					"         and (encounter_nr = '" . $this->current_enr. "'".$filter[0].") and upper(trim(lh.status)) <> 'DELETED' " .
					"         and (str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"            and str_to_date(concat(date_format(serv_dt, '%Y-%m-%d'), ' ', date_format(serv_tm, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
							"   group by ld.service_code) as t";
		$ntotal = $this->computeTotalSrvCharge($strSQL, $ntotal);

		$strSQL = "select sum(qty * serv_charge) as total_charge from " .
							"(select rd.service_code, count(rd.service_code) as qty, (sum(rd.price_charge)/count(rd.service_code)) as serv_charge, 'RD' as source " .
					"   from ((seg_radio_serv as rh inner join care_test_request_radio as rd on rh.refno = rd.refno) " .
					"          inner join seg_radio_services as rs on rd.service_code = rs.service_code) " .
					"          inner join seg_radio_service_groups as rsg on rs.group_code = rsg.group_code " .
					"      WHERE (CASE WHEN rh.request_date >= DATE('".ISSRVD_EFFECTIVITY."') THEN rd.is_served ELSE 1 END) AND ".
					"      /* where upper(rd.status) = 'DONE' and*/ UPPER(TRIM(rd.STATUS)) <> 'DELETED' AND rh.is_cash = 0 and (rd.request_flag is null OR rd.request_flag IN (SELECT id FROM seg_type_charge WHERE is_excludedfrombilling=0)) ".
					"         and (encounter_nr = '" . $this->current_enr. "'".$filter[0].") and upper(trim(rh.status)) <> 'DELETED' and upper(trim(rd.status)) <> 'DELETED' " .
					"         and (str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"            and str_to_date(concat(date_format(rh.request_date, '%Y-%m-%d'), ' ', date_format(rh.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
							"   group by rd.service_code) as t";
		$ntotal = $this->computeTotalSrvCharge($strSQL, $ntotal);

		$strSQL = "select sum(qty * serv_charge) as total_charge from " .
							"(select pd.bestellnum, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as serv_charge, 'SU' as source ".
					"   from ((seg_pharma_orders as ph inner join
											 seg_pharma_order_items pd ON ph.refno = pd.refno AND pd.serve_status <> 'N' AND pd.request_flag IS NULL) ".
					"      inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum) ".
					"      left join (SELECT rd.ref_no, rd.bestellnum, SUM(quantity) AS quantity
																	FROM seg_pharma_return_items AS rd INNER JOIN seg_pharma_returns AS rh
																		 ON rd.return_nr = rh.return_nr AND (rh.encounter_nr = '". $this->current_enr. "'".$filter[0].")
																	WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = '". $this->current_enr. "'".$filter[0].") AND rd.ref_no = oh.refno)
										GROUP BY rd.ref_no, rd.bestellnum) as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum ".
					"   where (encounter_nr = '". $this->current_enr. "'".$filter[0].") and is_cash = 0 and p.prod_class = 'S' ".
					"      and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
					"      and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
					"      and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
							"   group by pd.bestellnum) as t";
		$ntotal  = $this->computeTotalSrvCharge($strSQL, $ntotal);

		$strSQL = "select sum(qty * serv_charge) as total_charge from " .
					"(select mphd.bestellnum, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as serv_charge, 'MS' as source ".
					"   from (seg_more_phorder_details as mphd inner join seg_more_phorder as mph on mphd.refno = mph.refno) ".
					"      inner join care_pharma_products_main as p on mphd.bestellnum = p.bestellnum and p.prod_class = 'S' ".
					"   where (encounter_nr = '". $this->current_enr. "'".$filter[0].") ".
					"      and (str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
					"         and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
							"   group by mphd.bestellnum) as t";
		$ntotal = $this->computeTotalSrvCharge($strSQL, $ntotal);

		$strSQL = "select sum(qty * serv_charge) as total_charge from " .
							"(select eqd.equipment_id, sum(number_of_usage) as qty, (sum(discounted_price * number_of_usage)/sum(number_of_usage)) as uprice, 'OE' as source
						 from ((seg_equipment_orders as eqh inner join seg_equipment_order_items as eqd on eqh.refno = eqd.refno)
						 left join seg_ops_serv as sos on sos.refno = eqh.request_refno) inner join care_pharma_products_main as
						 cppm on cppm.bestellnum = eqd.equipment_id
						 where (eqh.encounter_nr = '". $this->current_enr. "'".$filter[1].")
							and (str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "'
							and str_to_date(eqh.order_date, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "')
									 group by eqd.equipment_id) as t";
		$ntotal = $this->computeTotalSrvCharge($strSQL, $ntotal);

		$strSQL = "select sum(qty * serv_charge) as total_charge from " .
							"(select md.service_code, sum(md.quantity) as qty, (sum(chrg_amnt * md.quantity)/sum(md.quantity)) as serv_charge, 'OA' as source ".
					"   from (seg_misc_service as m inner join seg_misc_service_details as md on m.refno = md.refno) ".
					"      inner join seg_other_services as ms on md.service_code = ms.alt_service_code ".
					"   where (encounter_nr = '" . $this->current_enr. "'".$filter[0].") and md.request_flag is null and is_cash = 0 ".
					"      and (str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
					"      and str_to_date(m.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
					"   group by md.service_code) as t";
		$ntotal = $this->computeTotalSrvCharge($strSQL, $ntotal);

//		if ($result = $db->Execute($strSQL)) {
//			if ($result->RecordCount()) {
//				while ($row = $result->FetchRow()) {
//					if (!is_null($row['total_charge']))
//						$ntotal += $row['total_charge'];
//				}
//			}
//		}

		$this->total_srv_charge = $ntotal;
		return($ntotal);
	}

	function computeTotalSrvCharge($strSQL, $ntotal) {
		global $db;

		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					if (!is_null($row['total_charge']))
						$ntotal += $row['total_charge'];
				}
			}
		}

		return ($ntotal);
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
		$this->total_misc_charge = $ntotal;
		return($ntotal);
	}

	function getTotalMedCharge() {
		global $db;

		if (isset($this->total_med_charge) && !$this->forceCompute) return $this->total_med_charge;

		$ntotal = 0;
		$filter = array('','');

		if ($this->prev_encounter_nr != '') $filter[0] = " or encounter_nr = '$this->prev_encounter_nr'";
		if ($this->prev_encounter_nr != '') $filter[1] = " or si.encounter_nr = '$this->prev_encounter_nr'";


		/**
		* 05-28-2011
		*
		* Incompatible queries produce negative results in medicine totals in hospital bill
		*
		* Modified line: select 0 as flag, pd.bestellnum, artikelname, sum(pd.quantity ...
		* To: select 0 as flag, pd.bestellnum, (CASE WHEN (ISNULL(generic) OR (generic = '')) THEN artikelname ELSE generic END) AS artikelname, sum(pd.quantity ...
		*
		* @author Alvin
		*/
//		$strSQL = "select sum(itemcharge) as itemcharge ".
//					" from ".
//					"(select 0 as flag, pd.bestellnum, (CASE WHEN (ISNULL(generic) OR (generic = '')) THEN artikelname ELSE generic END) AS artikelname, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as price, sum((pd.quantity - ifnull(spri.quantity, 0)) * pricecharge) as itemcharge ".
//					"   from ((seg_pharma_orders as ph inner join
//											 seg_pharma_order_items pd ON ph.refno = pd.refno AND pd.serve_status <> 'N' AND pd.request_flag IS NULL) ".
//					"         inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'M') ".
//					"         left join (SELECT rd.ref_no, rd.bestellnum, SUM(quantity) AS quantity
//																	FROM seg_pharma_return_items AS rd INNER JOIN seg_pharma_returns AS rh
//																		 ON rd.return_nr = rh.return_nr
//																	WHERE (rh.encounter_nr = '". $this->current_enr. "'".$filter[0].") OR
//																		 EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = '". $this->current_enr. "'".$filter[0].") AND rd.ref_no = oh.refno)
//										GROUP BY rd.ref_no, rd.bestellnum) as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum ".
//					"      where (((encounter_nr = '". $this->current_enr. "'".$filter[0].") and is_cash = 0 ".
//					"          AND pd.serve_status <> 'N' AND pd.request_flag IS NULL ".
//					 "         and exists (select * from (seg_hcare_products as shp inner join seg_hcare_bsked as shb ".
//					"                           on shp.bsked_id = shb.bsked_id) inner join seg_encounter_insurance as si on shb.hcare_id = si.hcare_id ".
//					"                        where shp.bestellnum = pd.bestellnum and ".
//					"                           str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
//					"                           and (select max(effectvty_dte) as latest ".
//					"                                   from seg_hcare_bsked as shb2 ".
//					"                                   where str_to_date(shb2.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
//					"                                      and shb2.hcare_id = shb.hcare_id ".
//					"                                      and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte and    ".
//					"                           (si.encounter_nr = '". $this->current_enr. "'".$filter[1]."))) ".
//					"         or ((encounter_nr = '". $this->current_enr. "'".$filter[0].") and is_cash = 0)) " .
//					"        and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
//					"           and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
//					"        and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
//					"   group by pd.bestellnum, artikelname ".
//					" union ".
//					"select 1 as flag, mpd.bestellnum, (CASE WHEN (ISNULL(generic) OR (generic = '')) THEN artikelname ELSE generic END) AS artikelname, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as price, sum(quantity * unit_price) as itemcharge ".
//					"   from (seg_more_phorder as mph inner join seg_more_phorder_details as mpd on mph.refno = mpd.refno) ".
//					"      inner join care_pharma_products_main as p on mpd.bestellnum = p.bestellnum and p.prod_class = 'M' ".
//					"   where (((encounter_nr = '". $this->current_enr. "'".$filter[0].") ".
//					"      and exists (select * from (seg_hcare_products as shp inner join seg_hcare_bsked as shb ".
//					"                        on shp.bsked_id = shb.bsked_id) inner join seg_encounter_insurance as si on shb.hcare_id = si.hcare_id ".
//					"                     where shp.bestellnum = mpd.bestellnum and ".
//					"                        str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
//					"                        and (select max(effectvty_dte) as latest ".
//					"                                from seg_hcare_bsked as shb2 ".
//					"                                where str_to_date(shb2.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
//					"                                   and shb2.hcare_id = shb.hcare_id ".
//					"                                   and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte and ".
//					"                        (si.encounter_nr = '". $this->current_enr. "'".$filter[1]."))) ".
//					"         or ((encounter_nr = '". $this->current_enr. "'".$filter[0]."))) ".
//					"        and (str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
//					"           and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
//					"   group by mpd.bestellnum, artikelname) as t";


/*****
* Optimized by LST - 05.31.2011
*/
		$strSQL = "select sum(itemcharge) as itemcharge ".
					" from ".
					"(select 0 as flag, pd.bestellnum, (CASE WHEN (ISNULL(generic) OR (generic = '')) THEN artikelname ELSE generic END) AS artikelname, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty, (sum(pricecharge * (pd.quantity - ifnull(spri.quantity, 0)))/sum(pd.quantity - ifnull(spri.quantity, 0))) as price, sum((pd.quantity - ifnull(spri.quantity, 0)) * pricecharge) as itemcharge ".
					"   from ((seg_pharma_orders as ph inner join
											 seg_pharma_order_items pd ON ph.refno = pd.refno) ".
					"         inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum) ".
					"         left join (SELECT rd.ref_no, rd.bestellnum, SUM(quantity) AS quantity
																	FROM seg_pharma_return_items AS rd INNER JOIN seg_pharma_returns AS rh
																		 ON rd.return_nr = rh.return_nr AND (rh.encounter_nr = '". $this->current_enr. "'".$filter[0].")
																	WHERE EXISTS (SELECT * FROM seg_pharma_orders AS oh WHERE (encounter_nr = '". $this->current_enr. "'".$filter[0].") AND rd.ref_no = oh.refno)
										GROUP BY rd.ref_no, rd.bestellnum) as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum ".
					"      where (encounter_nr = '". $this->current_enr. "'".$filter[0].") and is_cash = 0 and p.prod_class = 'M' ".
					"          AND pd.serve_status <> 'N' AND pd.request_flag IS NULL ".
					"        and (str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
					"           and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
					"        and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
					"   group by pd.bestellnum ".
					" union all ".
					"select 1 as flag, mpd.bestellnum, (CASE WHEN (ISNULL(generic) OR (generic = '')) THEN artikelname ELSE generic END) AS artikelname, sum(quantity) as qty, (sum(unit_price * quantity)/sum(quantity)) as price, sum(quantity * unit_price) as itemcharge ".
					"   from (seg_more_phorder as mph inner join seg_more_phorder_details as mpd on mph.refno = mpd.refno) ".
					"      inner join care_pharma_products_main as p on mpd.bestellnum = p.bestellnum ".
					"   where (encounter_nr = '". $this->current_enr. "'".$filter[0].") and p.prod_class = 'M' ".
					"        and (str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
					"           and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') ".
					"   group by mpd.bestellnum) as t";

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

		$this->total_med_charge = $ntotal;
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
		$this->total_op_charge = $ntotal;
		return($ntotal);
	}

	function getTotalPFParams(&$n_days, &$n_rvu, &$n_pf, $role_area = '', $role_level = 0, $b_noexcluded = false, $drnr = '', $opcode = '') {
		$n_days = 0;
		$n_rvu = 0;
		$n_pf = 0;

		if (!empty($this->hsp_pfs_benefits) && is_array($this->hsp_pfs_benefits))
			foreach ($this->hsp_pfs_benefits as $objpf) {
				if ($objpf->getRoleBenefit() == $role_area) {
					if ($role_level != 0) {
						if ($role_level == $objpf->getRoleLevel()) {
							if ($drnr != '') {
								if ($drnr == $objpf->getDrNr()) {
									$n_days += $objpf->getDaysAttended();
									if ($opcode != '') {
										$opcodes = $objpf->getOpCodes();
										if ($opcodes != '') $opcodes = explode(";", $opcodes);
										if (is_array($opcodes)) {
											foreach($opcodes as $v) {
												$i = strpos($v, '-');
												if (!($i === false)) {
													$code = substr($v, 0, $i);
													if ($code == $opcode) {
															$n = strpos($v, '(');
															if (!($n === false))
																 $n_rvu += substr($v, $i+1, $n-($i+1));
															else
																 $n_rvu += substr($v, $i+1);
															break;
													}
												}
											}
										}
									}
									else
										$n_rvu  += $objpf->getRVU();
									$n_pf   += ($b_noexcluded) ? $objpf->getChrgForCoverage() : $objpf->getDrCharge();
								}
							}
							else {
								$n_days += $objpf->getDaysAttended();
								$n_rvu  += $objpf->getRVU();
								$n_pf   += ($b_noexcluded) ? $objpf->getChrgForCoverage() : $objpf->getDrCharge();
							}
						}
					}
					else {
						if ($drnr != '') {
							if ($drnr == $objpf->getDrNr()) {
						$n_days += $objpf->getDaysAttended();
								if ($opcode != '') {
									$opcodes = $objpf->getOpCodes();
									if ($opcodes != '') $opcodes = explode(";", $opcodes);
									if (is_array($opcodes)) {
										foreach($opcodes as $v) {
											$i = strpos($v, '-');
											if (!($i === false)) {
												$code = substr($v, 0, $i);
												if ($code == $opcode) {
														$n = strpos($v, '(');
														if (!($n === false))
															 $n_rvu += substr($v, $i+1, $n-($i+1));
														else
															 $n_rvu += substr($v, $i+1);
														break;
//														$n_rvu += substr($v, $i+1);
//														break;
												}
											}
										}
									}
								}
								else
						$n_rvu  += $objpf->getRVU();
					    $n_pf   += ($b_noexcluded) ? $objpf->getChrgForCoverage() : $objpf->getDrCharge() - $this->nonDiscountablePF;
					}
				}
						else {
							$n_days += $objpf->getDaysAttended();
							$n_rvu  += $objpf->getRVU();
							$n_pf   += ($b_noexcluded) ? $objpf->getChrgForCoverage() : $objpf->getDrCharge();
						}
//						$n_days += $objpf->getDaysAttended();
//						$n_rvu  += $objpf->getRVU();
//						$n_pf   += ($b_noexcluded) ? $objpf->getChrgForCoverage() : $objpf->getDrCharge();
					}
				}
			}
	}

	function getTotalPFCharge($pfarea = '') {
		// Compute total doctors' fees ...
		$npf      = 0;
		$ndays    = 0;
		$nrvu     = 0;
		$total_df = 0;

		// .... D1 role
		$this->getTotalPFParams($ndays, $nrvu, $npf, 'D1');
		$total_df += $npf;
		if ($pfarea == 'D1') return $npf;

		// .... D2 role
		$this->getTotalPFParams($ndays, $nrvu, $npf, 'D2');
		$total_df += $npf;
		if ($pfarea == 'D2') return $npf;

		// .... D3 role
		$this->getTotalPFParams($ndays, $nrvu, $npf, 'D3');
		$total_df += $npf;
		if ($pfarea == 'D3') return $npf;

		// .... D4 role
		$this->getTotalPFParams($ndays, $nrvu, $npf, 'D4');
		$total_df += $npf;
		if ($pfarea == 'D4') return $npf;

		$this->total_pf_charge = $total_df;
		return($total_df);
	}

	function getTotalPFCoverage() {
		$total = 0;

		$total += $this->pfs_confine_coverage['D1'];
		$total += $this->pfs_confine_coverage['D2'];
		$total += $this->pfs_confine_coverage['D3'];
		$total += $this->pfs_confine_coverage['D4'];

		return(round($total,4));
	}

	function getTotalRVU($opscode = '') {
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
// ------ commented out by LST ----- 10.28.2010 --------------- START ------------------------------------------------------
//		$strSQL = "select sum(rvu) as total_rvu
//					from
//					(select ops_code, max(rvu) as rvu from
//					(select os.refno, 1 as entry_no, os.request_date, os.request_time, od.ops_code, od.rvu, od.multiplier, (od.rvu * od.multiplier) as op_charge, group_code, 'OR' as provider
//						 from seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno
//						 where (encounter_nr = '". $this->current_enr. "'".$filter.") and is_cash = 0 and upper(trim(os.status)) <> 'DELETED'
//							and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."'
//							and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."')
//							and group_code <> ''
//					 union
//					 select mo.refno, entry_no, DATE_FORMAT(mo.chrge_dte, '%Y:%m:%d') as chrgdate, DATE_FORMAT(mo.chrge_dte, '%H:%i:%s') as chrgtime,
//							 md.ops_code, md.rvu, md.multiplier, md.chrg_amnt, group_code, 'OA' as provider
//						from seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno
//						where (encounter_nr = '". $this->current_enr. "'".$filter.") and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."'
//							 and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."')
//							 and group_code <> ''
//						order by rvu desc) as t
//					group by group_code
//					union
//					select ops_code, sum(rvu) as rvu from
//					(select os.refno, 1 as entry_no, os.request_date, os.request_time, od.ops_code, od.rvu, od.multiplier, (od.rvu * od.multiplier) as op_charge, group_code, 'OR' as provider
//						 from seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno
//						 where (encounter_nr = '". $this->current_enr. "'".$filter.") and is_cash = 0 and upper(trim(os.status)) <> 'DELETED'
//							and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."'
//							and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."')
//							and group_code = ''
//					 union
//					 select mo.refno, entry_no, DATE_FORMAT(mo.chrge_dte, '%Y:%m:%d') as chrgdate, DATE_FORMAT(mo.chrge_dte, '%H:%i:%s') as chrgtime,
//							 md.ops_code, md.rvu, md.multiplier, md.chrg_amnt, group_code, 'OA' as provider
//						from seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno
//						where (encounter_nr = '". $this->current_enr. "'".$filter.") and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."'
//							 and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."')
//							 and group_code = '') as t2
//					group by ops_code) as t3";
// ------ commented out by LST ----- 10.28.2010 --------------------- END ------------------------------------------------------

//		$strSQL = "select sum(rvu) as total_rvu from " .
//				  "(select os.refno, 1 as entry_no, os.request_date, os.request_time, od.ops_code, description, od.rvu, od.multiplier, (od.rvu * od.multiplier) as op_charge, 'OR' as provider ".
//   				  "   from (seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno) ".
//         		  "      inner join seg_ops_rvs as om on od.ops_code = om.code ".
//   				  "   where (encounter_nr = '". $this->current_enr. "'".$filter.") and is_cash = 0 and upper(trim(os.status)) <> 'DELETED' ".
//         		  "      and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
//            	  "      and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."') ".
//				  " union ".
//				  " select mo.refno, entry_no, DATE_FORMAT(mo.chrge_dte, '%Y:%m:%d') as chrgdate, DATE_FORMAT(mo.chrge_dte, '%H:%i:%s') as chrgtime, ".
//   				  "       md.ops_code, description, md.rvu, md.multiplier, md.chrg_amnt, 'OA' as provider ".
//   				  "    from (seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno) ".
//         		  "       inner join seg_ops_rvs as om on md.ops_code = om.code ".
//   				  "    where (encounter_nr = '". $this->current_enr. "'".$filter.") and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
//            	  "       and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."')) as t";

//                  " union ".
//                  " select oah.refno, entry_no, DATE_FORMAT(oah.chrge_dte, '%Y:%m:%d') as chrgdate, DATE_FORMAT(oah.chrge_dte, '%H:%i:%s') as chrgtime, ".
//                  "       concat('OR-', cast(oad.room_nr as char)) as ops_code, concat((select ifnull(name, '') from care_ward where nr = oad.group_nr), '- Room ', cast(cr.room_nr as char)) as description, ".
//                  "       (select ifnull(sum(rvu), 0) as trvu from seg_ops_chrgd_accommodation as soca where soca.refno = oah.refno and soca.entry_no = oad.entry_no) as rvu, ".
//                  "       (select multiplier from seg_ops_chrgd_accommodation as soca2 where soca2.refno = oah.refno and soca2.entry_no = oad.entry_no limit 1) as multiplier, oad.charge, 'RU' as provider ".
//                  "    from (seg_opaccommodation as oah inner join seg_opaccommodation_details as oad on oah.refno = oad.refno) ".
//                  "      inner join care_room as cr on oad.room_nr = cr.nr ".
//                  "    where encounter_nr = '". $this->current_enr ."' and (str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $this->bill_frmdte ."' ".
//                  "       and str_to_date(oah.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $this->bill_dte ."')) as t";

		if ($opscode == '') {
			$strSQL = "SELECT
                            SUM(rvu) as total_rvu
                        FROM seg_ops_chrgd_accommodation ops
                            INNER JOIN seg_opaccommodation oph
                                ON ops.refno = oph.refno
                        WHERE (encounter_nr = '". $this->current_enr. "'".$filter.")";
		}
		else {
			$strSQL = "SELECT
                            SUM(rvu) as total_rvu
                        FROM seg_ops_chrgd_accommodation ops
                            INNER JOIN seg_opaccommodation oph
                                ON ops.refno = oph.refno
                        WHERE (encounter_nr = '". $this->current_enr. "'".$filter.")
                             AND ops_code = '$opscode'
                        GROUP BY ops_code, entry_no LIMIT 1";    // modified by LST - 11.12.2011 --- Issue (from SOW 10-001)
		}

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

		if (is_array($this->accommodation_hist)) {
				foreach($this->accommodation_hist as $objacc) {
					++$indx;

					if ($objacc->getSource() == 'AD') {
						// Compute the no. of days and excess hours ...
	#					$ndiff = abs(strtotime($objacc->admission_dtetime) - strtotime($objacc->discharge_dtetime));
	#					$ndiff = strtotime($objacc->discharge_dtetime) - strtotime($objacc->admission_dtetime);

						// Modified per request that no. of days be computed by no. of actual days since admission. -- 05.20.2009 -- by LST
						$ndiff = abs(strtotime(date('Y-m-d', strtotime($objacc->admission_dtetime))) - strtotime(date('Y-m-d', strtotime($objacc->discharge_dtetime))));

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
                        $objrmtyp->setExcess($objacc->getExcess());
						$objrmtyp->setSource($objacc->getSource());
                        $objrmtyp->setAccomodationType($objacc->getAccomodationType());

						$objrmtyp->computeTotalCoverage($this->getBillAreaDRate('AC'));

						$this->acc_roomtype_benefits[] = $objrmtyp;
					}
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

		if ($this->prev_encounter_nr != '') $filter[0] = " and sbe.encounter_nr <> '$this->prev_encounter_nr'";
		if ($this->prev_encounter_nr != '') $filter[1] = " or suc.encounter_nr = '$this->prev_encounter_nr'";
		$strSQL = "select sum(tdays) as days_total ".
					"   from ".
					"(select sum(confine_days) as tdays
						from seg_confinement_tracker as sct inner join seg_billing_encounter as sbe
						 on sct.bill_nr = sbe.bill_nr
						where (sbe.encounter_nr <> '{$this->current_enr}'{$filter[0]}) and hcare_id = {$nhcareid} and sbe.is_deleted IS NULL
						 and (sct.pid = (select pid from care_encounter as ce1 where ce1.encounter_nr = '{$this->current_enr}')
							or sct.principal_pid = (select pid from care_encounter as ce1 where ce1.encounter_nr = '{$this->current_enr}'))
						 and current_year = year('{$this->bill_dte}')
					 union ".
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
					"             and (sbe.encounter_nr = '". $this->current_enr. "'".$filter[0].") and sbc.hcare_id = ".$nhcareid." and sbe.is_deleted IS NULL ".
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

	function isWithRoleLevel($sBillArea) {
		global $db;

		$nwithlevel = 0;

		$strSQL = "select is_withlevel from seg_hcare_benefits where bill_area = '$sBillArea'";
		if ($result = $db->Execute($strSQL)) {
			if ($row = $result->FetchRow()) {
				$nwithlevel = (is_null($row["is_withlevel"])) ? 0 : $row["is_withlevel"];
			}
		}

		return(($nwithlevel != 0));
	}

	function getAnesthAdjustment($nrvu, $nRoleLevel) {
		global $db;

		$adjstmnt = 0.4;
		$strSQL = "SELECT fn_getrvuadjustment('".$this->current_enr."', DATE('".$this->bill_dte."'), 'D4', ".$nRoleLevel.", ".$nrvu.", ".$this->confinetype_id.") AS adjustmnt";
		if ($result = $db->Execute($strSQL)) {
			if ($row = $result->FetchRow()) {
				 $adjstmnt = (is_null($row["adjustmnt"])) ? 0.4 : $row["adjustmnt"];
			}
		}
		return $adjstmnt;
	}

	function getConfineBenefits($sBillArea, $sProdClass = '', $nRoleLevel = 0, $bCoverageInSked = false, $hcareid = 0, $opcode = '') {
		global $db;

		$totalCoverage = 0;
		$filter = '';

		//if ($this->prev_encounter_nr != '') $filter = " or si.encounter_nr = '$this->prev_encounter_nr'";
		switch ($sBillArea) {
			case 'AC':
				$this->acc_confine_benefits = array();

				if (!$this->is_coveredbypkg) {
					$strSQL = "select distinct ci.hcare_id, firm_id, name, hb.benefit_id, bs.bsked_id ".
							"   from ((care_insurance_firm as ci inner join ".
							"            (select * from seg_hcare_bsked as shb ".
							"                where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
								"                   and (shb.basis & 1) ".
							"                   and (select max(effectvty_dte) as latest ".
							"                           from seg_hcare_bsked as shb2 ".
								"                           where str_to_date(shb2.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
								"                              and shb2.hcare_id = shb.hcare_id ".
								"                              and shb2.benefit_id = shb.benefit_id
																							 and shb2.basis & 1
																							 and exists (select * from seg_hcare_confinetype as sc ".
								"                                             where sc.bsked_id = shb2.bsked_id and ".
								"                                                sc.confinetype_id = ". $this->confinetype_id. ")) = shb.effectvty_dte) as bs on ci.hcare_id = bs.hcare_id) ".
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

								
								$rate = $this->skedvalues['rateperday'];
								
								//edited by jasper 03/26/2013 -- restored by LST 04/02/2013
                                $nCoverage = $ndays * $this->skedvalues['rateperday'];
//								$nCoverage = ($ndays * $this->skedvalues['rateperday']) - ($this->compTotalAccommodationChrg() * $this->getBillAreaDRate($sBillArea));
								if ($nhrs > 0) {
									$nCoverage += ($nhrs * ($this->skedvalues['rateperday'] / 24));
								}

								// Take into consideration the coverage already applied in previous billings or disclosed used coverage ...
								$prevCoverage = $this->getTotalUsedCoverage($nhcare_id, $sBillArea, $sProdClass);

								if ((($nCoverage > ($this->skedvalues['amountlimit'] - $prevCoverage)) && ($this->skedvalues['amountlimit'] > 0)) || ($this->skedvalues['rateperday'] == 0)) {
//									$nCoverage = $this->skedvalues['amountlimit'] - $prevCoverage - ($this->compTotalAccommodationChrg() * $this->getBillAreaDRate($sBillArea));
									$nCoverage = $this->skedvalues['amountlimit'] - $prevCoverage; // restored by LST 04/02/2013
								}

								// Check if actual charge < prescribed coverage ... if yes, cover only actual charge.
								if ($nCoverage > $nCharge) $nCoverage = $nCharge;

								if ($nCoverage > 0) {
									$objhcare = new HCareCoverage;

									$objhcare->setID($row['hcare_id']);
									$objhcare->setFirmID($row['firm_id']);
									$objhcare->setDesc($row['name']);

										// Apply adjusted coverage ...
										if (isset($this->adjusted_coverage[$row['hcare_id']][$sBillArea])) $nCoverage = ($nCoverage > $this->adjusted_coverage[$row['hcare_id']][$sBillArea]) ? $this->adjusted_coverage[$row['hcare_id']][$sBillArea] : $nCoverage;

									$objhcare->setCoverage($nCoverage);
									$objhcare->setDaysCovered($ndays);
									$objhcare->setAmountLimit( ($this->skedvalues['amountlimit'] - $prevCoverage) < 0 ? 0 : ($this->skedvalues['amountlimit'] - $prevCoverage) );

									// Add new supply object in collection (array) of the list of applicable benefits based on confinement.
									$this->acc_confine_benefits[] = $objhcare;

									$totalCoverage += $nCoverage;
								}
							}  // while loop ...
						}	// RecordCount() ...
					}	// Execute() ...
				}
				break;

			case 'MS':
				if ($sProdClass == 'M')
					$this->med_confine_benefits = array();
				else
					$this->sup_confine_benefits = array();

				if (!$this->is_coveredbypkg) {
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

						$strSQL = "select distinct ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis, bs.bsked_id ".
								"   from ((care_insurance_firm as ci inner join ".
								"            (select * from seg_hcare_bsked as shb ".
								"                where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
								"                   and (shb.basis & 1) ".
								"                   and (select max(effectvty_dte) as latest ".
								"                           from seg_hcare_bsked as shb2 ".
									"                           where str_to_date(shb2.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
									"                              and shb2.hcare_id = shb.hcare_id ".
								"                              and shb2.benefit_id = shb.benefit_id
																							 and shb2.basis & 1
																							 and exists (select * from seg_hcare_confinetype as sc ".
								"                                             where sc.bsked_id = shb2.bsked_id and ".
								"                                                sc.confinetype_id = ". $this->confinetype_id. ")) = shb.effectvty_dte
																		 AND EXISTS (SELECT * FROM seg_hcare_benefits hb1
																									 WHERE hb1.benefit_id = shb.benefit_id AND hb1.bill_area = '". $sBillArea."')
																		 AND shb.tier_nr = 0
																		 ORDER BY shb.effectvty_dte DESC LIMIT 1) as bs on ci.hcare_id = bs.hcare_id) ".
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

								if (!$bCoverageInSked) {
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
								}

								// Take into consideration the coverage already applied in previous billings or disclosed used coverage ...
								$prevCoverage = $this->getTotalUsedCoverage($nhcare_id, $sBillArea, $sProdClass);

								if (!$bCoverageInSked) {
									if (($nCoverage > ($this->skedvalues['amountlimit'] - $prevCoverage)) && ($this->skedvalues['amountlimit'] > 0))
										$nCoverage = ($this->skedvalues['amountlimit'] - $prevCoverage) - ($this->getTotalMedCharge() * $this->getBillAreaDRate($sBillArea));
								}
								else {
//                                    if (in_array($_SESSION['sess_temp_userid'], array('admin', 'medocs')))
//                                        $prevCoverage = 0;

									if ($this->skedvalues['amountlimit'] > 0)
										$nCoverage = ($this->skedvalues['amountlimit'] - $prevCoverage) - ($this->getTotalMedCharge() * $this->getBillAreaDRate($sBillArea));
								}

								if ($nCoverage > 0) {
									$objhcare = new HCareCoverage;

									$objhcare->setID($row['hcare_id']);
									$objhcare->setFirmID($row['firm_id']);
									$objhcare->setDesc($row['name']);

										// Apply adjusted coverage ...
										if (isset($this->adjusted_coverage[$row['hcare_id']][$sBillArea])) $nCoverage = ($nCoverage > $this->adjusted_coverage[$row['hcare_id']][$sBillArea]) ? $this->adjusted_coverage[$row['hcare_id']][$sBillArea] : $nCoverage;

										$objhcare->setCoverage($nCoverage);

									$objhcare->setAmountLimit( (($this->skedvalues['amountlimit'] - $prevCoverage) < 0) ? 0 : ($this->skedvalues['amountlimit'] - $prevCoverage) );

									// Add new supply object in collection (array) of the list of applicable benefits based on confinement.
									if ($sProdClass == 'M')
										$this->med_confine_benefits[] = $objhcare;
									else
										$this->sup_confine_benefits[] = $objhcare;

									if ($nCoverage > 0) $totalCoverage += $nCoverage;
								}
							}  // while loop ...
						}	// RecordCount() ...
					}	// Execute() ...
				}
				break;

			case 'HS':
				$this->srv_confine_benefits = array();

				if (!$this->is_coveredbypkg) {
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

					$strSQL = "select distinct ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis, bs.bsked_id ".
							"   from ((care_insurance_firm as ci inner join ".
							"            (select * from seg_hcare_bsked as shb ".
							"                where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
							"                   and (shb.basis & 1) ".
							"                   and (select max(effectvty_dte) as latest ".
							"                           from seg_hcare_bsked as shb2 ".
								"                           where str_to_date(shb2.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
								"                              and shb2.hcare_id = shb.hcare_id ".
								"                              and shb2.benefit_id = shb.benefit_id
																							 and shb2.basis & 1
																							 and exists (select * from seg_hcare_confinetype as sc ".
								"                                             where sc.bsked_id = shb2.bsked_id and ".
								"                                                sc.confinetype_id = ". $this->confinetype_id. ")) = shb.effectvty_dte
																	AND EXISTS (SELECT * FROM seg_hcare_benefits hb1
																								WHERE hb1.benefit_id = shb.benefit_id AND hb1.bill_area = '". $sBillArea."')
																	AND shb.tier_nr = 0
																	ORDER BY shb.effectvty_dte DESC LIMIT 1) as bs on ci.hcare_id = bs.hcare_id) ".
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

                                if (!$bCoverageInSked) {
                                    $nCoverage = 0;
                                    if ($row['basis'] & 8) {
                                        $nCoverage = $this->getTotalSrvCoverage($nhcare_id);
                                        $nCoverage = is_null($nCoverage) ? 0 : $nCoverage;
                                    }

                                    if (($row['basis'] & 1) && ($this->skedvalues['amountlimit'] > 0)) {
                                        $nCoverage += $this->getTotalSrvCharge() * (1 - $this->getBillAreaDRate($sBillArea));
                                    }
                                }

								// Take into consideration the coverage already applied in previous billings or disclosed used coverage ...
								$prevCoverage = $this->getTotalUsedCoverage($nhcare_id, $sBillArea, $sProdClass);

                                if (!$bCoverageInSked) {
                                    if (($nCoverage > ($this->skedvalues['amountlimit'] - $prevCoverage)) && ($this->skedvalues['amountlimit'] > 0))
                                        //edited by jasper 03/26/2013
                                        //$nCoverage = $this->skedvalues['amountlimit'] - $prevCoverage;
                                        $nCoverage = ($this->skedvalues['amountlimit'] - $prevCoverage) - ($this->getTotalSrvCharge() * $this->getBillAreaDRate($sBillArea));
                                }
                                else {
									if ($this->skedvalues['amountlimit'] > 0)
                                        //edited by jasper 03/26/2013
                                        //$nCoverage = $this->skedvalues['amountlimit'] - $prevCoverage;
										$nCoverage = ($this->skedvalues['amountlimit'] - $prevCoverage) - ($this->getTotalSrvCharge() * $this->getBillAreaDRate($sBillArea));
                                }

								if (($nCoverage > 0) || (($this->skedvalues['amountlimit'] - $prevCoverage) > 0)) {
									$objhcare = new HCareCoverage;

									$objhcare->setID($row['hcare_id']);
									$objhcare->setFirmID($row['firm_id']);
									$objhcare->setDesc($row['name']);

                                    // Apply adjusted coverage ...
                                    if (isset($this->adjusted_coverage[$row['hcare_id']][$sBillArea])) $nCoverage = ($nCoverage > $this->adjusted_coverage[$row['hcare_id']][$sBillArea]) ? $this->adjusted_coverage[$row['hcare_id']][$sBillArea] : $nCoverage;

                                    $objhcare->setCoverage($nCoverage);

									$objhcare->setAmountLimit( ($this->skedvalues['amountlimit'] - $prevCoverage) < 0 ? 0 : ($this->skedvalues['amountlimit'] - $prevCoverage) );

									// Add new supply object in collection (array) of the list of applicable benefits based on confinement.
									$this->srv_confine_benefits[] = $objhcare;

									if ($nCoverage > 0) $totalCoverage += $nCoverage;
								}
							}  // while loop ...
						}	// RecordCount() ...
					}	// Execute() ...
				}
				break;

			case 'OR':
//				$this->ops_confine_benefits = array();

				if (!$this->is_coveredbypkg) {
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

					$stmp   = ($nRoleLevel == 0) ? " and bs.tier_nr = 0" : " and bs.tier_nr = ".$nRoleLevel;
					$stmp2   = ($nRoleLevel == 0) ? " and shb.tier_nr = 0" : " and shb.tier_nr = ".$nRoleLevel;
						$strSQL = "select distinct ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis, bs.bsked_id ".
								"   from ((care_insurance_firm as ci inner join ".
								"            (select * from seg_hcare_bsked as shb ".
								"                where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
								"                   and (shb.basis & 1 or shb.basis & 4)
																		AND EXISTS(SELECT * FROM seg_encounter_insurance si2
																								WHERE si2.hcare_id = shb.hcare_id
																									 AND (si2.encounter_nr = '". $this->current_enr. "'".$filter2.")) ".
								"                   and (select max(effectvty_dte) as latest ".
								"                           from seg_hcare_bsked as shb2 ".
									"                           where str_to_date(shb2.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
									"                              and shb2.hcare_id = shb.hcare_id ".
								"                                            and shb2.benefit_id = shb.benefit_id
																														 AND ( ((shb.basis & 1) OR (shb.basis & 4))
																																		 AND (EXISTS (SELECT * FROM seg_hcare_confinetype AS sc
																																										WHERE sc.bsked_id = shb2.bsked_id AND
																																										sc.confinetype_id = ". $this->confinetype_id. ")
																																		 OR EXISTS (SELECT * FROM seg_hcare_rvurange AS shr
																																										WHERE shr.bsked_id = shb2.bsked_id)) )
																											 ) = shb.effectvty_dte
																		AND EXISTS (SELECT * FROM seg_hcare_benefits hb1
																									WHERE hb1.benefit_id = shb.benefit_id AND hb1.bill_area = '". $sBillArea."')".$stmp2."
																		ORDER BY shb.effectvty_dte DESC LIMIT 1) as bs on ci.hcare_id = bs.hcare_id) ".
								"            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
								"            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
								"   where hb.bill_area = '". $sBillArea."'".$stmp." and (si.encounter_nr = '". $this->current_enr. "'".$filter.") ".
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
							$this->total_RVU = $this->getTotalRVU($sProdClass);

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
								if (($row['basis'] & 8) || (($row['basis'] & 4) && ($this->total_RVU == 0))) {
									$nCoverage = $this->getTotalOpCoverage($nhcare_id);
									$nCoverage = is_null($nCoverage) ? 0 : $nCoverage;
								}
								#else
								if (($this->skedvalues['amountlimit'] > 0) || (isset($this->skedvalues['fixedamount']) && ($this->skedvalues['fixedamount'] > 0)) ||
									 (isset($this->skedvalues['minamount']) && ($this->skedvalues['minamount'] > 0))) {
									 $nCoverage += $this->getTotalOpCharge() * (1 - $this->getBillAreaDRate($sBillArea));
								}

								if ($nCoverage > 0) {
									if (isset($this->skedvalues['fixedamount'])) {
										if ($this->skedvalues['fixedamount'] > 0) {
                                            //edited by bong and jasper 03/26/2013
                                            //$nCoverage = $this->skedvalues['fixedamount'] - $prevCoverage
                                            $nCoverage = ($this->skedvalues['fixedamount'] - $prevCoverage) - ($this->getTotalOpCharge() * $this->getBillAreaDRate($sBillArea));
                                        }
									}
									else {
										if (isset($this->skedvalues['minamount'])) {
											if (($this->skedvalues['minamount'] > 0) && ($nCoverage < $this->skedvalues['minamount'])) {
                                                //edited by bong and jasper 03/26/2013
                                                //$nCoverage = $this->skedvalues['minamount']  - $prevCoverage;
												$nCoverage = ($this->skedvalues['minamount']  - $prevCoverage) - ($this->getTotalOpCharge() * $this->getBillAreaDRate($sBillArea));
                                            }
										}
									}
								}

								if (($this->skedvalues['rateperRVU'] > 0) && ($this->total_RVU > 0)) {
									$nTmpCoverage = $this->skedvalues['rateperRVU'] * $this->total_RVU;

									if (($nTmpCoverage > $this->skedvalues['limit_rvubased']) && ($this->skedvalues['limit_rvubased'] > 0))
										$nTmpCoverage = $this->skedvalues['limit_rvubased'];

									if ($this->skedvalues['amountlimit'] <= 0) $nCoverage += $this->getTotalOpCharge() * (1 - $this->getBillAreaDRate($sBillArea));
//										if (($nCoverage > $nTmpCoverage) && (!isset($this->skedvalues['minamount']) || ($this->skedvalues['minamount'] = 0))
//																										 && (!isset($this->skedvalues['fixedamount']) || ($this->skedvalues['fixedamount'] = 0))
//																										 && (!isset($this->skedvalues['amountlimit']) || ($this->skedvalues['amountlimit'] = 0))) {
										if (($nCoverage > 0) && (!isset($this->skedvalues['minamount']) || ($this->skedvalues['minamount'] == 0))
																				 && (!isset($this->skedvalues['fixedamount']) || ($this->skedvalues['fixedamount'] == 0))
																				 && (!isset($this->skedvalues['amountlimit']) || ($this->skedvalues['amountlimit'] == 0))) {
                                             //edited by bong and jasper 03/26/2013
                                             $nCoverage = $nTmpCoverage;
											 $nCoverage = $nTmpCoverage - ($this->getTotalOpCharge() * $this->getBillAreaDRate($sBillArea));
										}
								}

								// Take into consideration the coverage already applied in previous billings or disclosed used coverage ...
								$prevCoverage = $this->getTotalUsedCoverage($nhcare_id, $sBillArea, $sProdClass);

								if (($nCoverage > ($this->skedvalues['amountlimit'] - $prevCoverage)) && ($this->skedvalues['amountlimit'] > 0))
									$nCoverage = $this->skedvalues['amountlimit'] - $prevCoverage - ($this->getTotalOpCharge() * $this->getBillAreaDRate($sBillArea));

								if ($nCoverage > 0) {
									$objhcare = new HCareCoverage;

									$objhcare->setID($row['hcare_id']);
									$objhcare->setFirmID($row['firm_id']);
									$objhcare->setDesc($row['name']);

										// Apply adjusted coverage ...
										if (isset($this->adjusted_coverage[$row['hcare_id']][$sBillArea])) $nCoverage = ($nCoverage > $this->adjusted_coverage[$row['hcare_id']][$sBillArea]) ? $this->adjusted_coverage[$row['hcare_id']][$sBillArea] : $nCoverage;

									$objhcare->setCoverage($nCoverage);
									$objhcare->setAmountLimit( ($this->skedvalues['amountlimit'] - $prevCoverage) < 0 ? 0 : ($this->skedvalues['amountlimit'] - $prevCoverage) );

									// Add ops (operation procedure) object in collection (array) of the list of applicable benefits based on confinement.
									$this->ops_confine_benefits[] = $objhcare;
									$totalCoverage += $nCoverage;
								}
							}  // while loop ...
						}	// RecordCount() ...
					}	// Execute() ...
				}
				break;

			case 'D1':
			case 'D2':
			case 'D3':
			case 'D4':
//				$this->pfs_confine_benefits[$sBillArea] = array();

				$b_noRVU = false;
				$b_daily = false;

				if (!$this->is_coveredbypkg) {
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

//					$stmp   = ($nRoleLevel == 0) ? " and bs.tier_nr = ".(($this->isWithRoleLevel($sBillArea)) ? 1 : 0) : " and bs.tier_nr = ".$nRoleLevel;

						//  START --- modification by LST --- 10.07.2010 --------------------------------------------------------------
//						$stmp   = " and bs.tier_nr = ".$nRoleLevel;
//						$stmp   = ($nRoleLevel == 0) ? " and bs.tier_nr = ".$nRoleLevel : " and bs.tier_nr in (0, ".$nRoleLevel.")";
						if (($sBillArea == 'D1') || ($sBillArea == 'D2')) {
							if (strftime("%Y-%m-%d", strtotime($this->bill_dte)) < SKED_EFFECTIVITY)
								$stmp   = ($nRoleLevel == 0) ? " and bs.tier_nr = ".$nRoleLevel : " and bs.tier_nr in (0, ".$nRoleLevel.")";
							else
								$stmp   = " and bs.tier_nr = ".$nRoleLevel;
						}
						else if (($sBillArea == 'D3') || ($sBillArea == 'D4')) {
							$stmp   = " and bs.tier_nr = ".$nRoleLevel;
						}
						//  END --- modification by LST --- 10.07.2010 --------------------------------------------------------------

						$strSQL = "select distinct ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis, bs.bsked_id ".
								"   from ((care_insurance_firm as ci inner join ".
								"            (select * from seg_hcare_bsked as shb ".
								"                where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
								"                   and (shb.basis & 1 or shb.basis & 4) ".
								"                   and (select max(effectvty_dte) as latest ".
								"                           from seg_hcare_bsked as shb2 ".
									"                           where str_to_date(shb2.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
									"                              and shb2.hcare_id = shb.hcare_id ".
									"                              and shb2.benefit_id = shb.benefit_id ".
									"                              and shb2.tier_nr = shb.tier_nr ".
									"                              and (shb.basis & 1 or shb.basis & 4) ".
									"                              and (exists (SELECT * FROM seg_hcare_confinetype AS sc2 \n".                    // -- 01.24.2010 by LST
									"                                             WHERE sc2.bsked_id = shb2.bsked_id AND  \n".                    // -- 01.24.2010 by LST
									"                                                sc2.confinetype_id = ". $this->confinetype_id. ") OR
																		 EXISTS (SELECT * FROM seg_hcare_rvurange AS shr2
																				 WHERE shr2.bsked_id = shb2.bsked_id))) = shb.effectvty_dte) as bs
																			on ci.hcare_id = bs.hcare_id) ".	 // -- 01.24.2010 by LST
								"            inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id) ".
								"            inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id ".
								"   where hb.bill_area = '". $sBillArea."'".$stmp." and (si.encounter_nr = '". $this->current_enr. "'".$filter.") ".
								"      ".(($hcareid != 0) ? "and ci.hcare_id = $hcareid" : "") .
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
                            $pfcharge = 0.00;
							$this->getTotalPFParams($pf_Days, $pf_RVU, $pfcharge, $sBillArea, $nRoleLevel, true, $sProdClass, $opcode);

                            $nCoverage = $pfcharge;
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
											$nCoverage = $nTmpCoverage - ($pfcharge * $this->getBillAreaDRate($sBillArea));

										$b_noRVU = true;
										$b_daily = true;
									}

								if (!$b_noRVU)
									if (($this->skedvalues['rateperRVU'] > 0) && ($pf_RVU > 0)) {
										if ($this->skedvalues['rateperRVU'] < 1)
											$nTmpCoverage = $this->skedvalues['rateperRVU'] * $pf_RVU * $this->pcf;
										else {
											$npcf = $this->getHouseCasePCF();
											if ($npcf == 0)
												$nTmpCoverage = $this->skedvalues['rateperRVU'] * $pf_RVU;
											else
												$nTmpCoverage = $npcf * $pf_RVU;

											if ($sBillArea == 'D4') {
												$nTmpCoverage = $nTmpCoverage * $this->getAnesthAdjustment($pf_RVU, $nRoleLevel);
											}
										}

										if (($nTmpCoverage > $this->skedvalues['limit_rvubased']) && ($this->skedvalues['limit_rvubased'] > 0))
											$nTmpCoverage = $this->skedvalues['limit_rvubased'];

										if ($nCoverage > $nTmpCoverage)
											$nCoverage = $nTmpCoverage - ($pfcharge * $this->getBillAreaDRate($sBillArea));
									}

								// Take into consideration the coverage already applied in previous billings or disclosed used coverage ...
								$prevCoverage = $this->getTotalUsedCoverage($nhcare_id, $sBillArea, $sProdClass);

								if ($nCoverage > 0) {
									if (isset($this->skedvalues['fixedamount'])) {
										if ($this->skedvalues['fixedamount'] > 0) $nCoverage = $this->skedvalues['fixedamount'] - $prevCoverage - ($pfcharge * $this->getBillAreaDRate($sBillArea));
									}
									else {
										if (isset($this->skedvalues['minamount'])) {
											if (($this->skedvalues['minamount'] > 0) && ($nCoverage < $this->skedvalues['minamount']))
												$nCoverage = $this->skedvalues['minamount'] - $prevCoverage - ($pfcharge * $this->getBillAreaDRate($sBillArea));
										}
									}
								}

//								if (!$b_daily) {
									if (($nCoverage > ($this->skedvalues['amountlimit'] - $prevCoverage)) && ($this->skedvalues['amountlimit'] > 0)) {
										$nCoverage = $this->skedvalues['amountlimit'] - $prevCoverage - ($pfcharge * $this->getBillAreaDRate($sBillArea));
									}
//								}

								if ($nCoverage > 0) {
									$objhcare = new HCareCoverage;

									$objhcare->setID($row['hcare_id']);
									$objhcare->setFirmID($row['firm_id']);
									$objhcare->setDesc($row['name']);

										// Apply adjusted coverage ...
										if (isset($this->adjusted_coverage[$row['hcare_id']][$sBillArea])) $nCoverage = ($nCoverage > $this->adjusted_coverage[$row['hcare_id']][$sBillArea]) ? $this->adjusted_coverage[$row['hcare_id']][$sBillArea] : $nCoverage;

									$objhcare->setCoverage($nCoverage);
									$objhcare->setAmountLimit( ($this->skedvalues['amountlimit'] - $prevCoverage) < 0 ? 0 : ($this->skedvalues['amountlimit'] - $prevCoverage) );

									// Add professional fees object in collection (array) of the list of applicable benefits based on confinement or RVU range.
									$this->pfs_confine_benefits[$sBillArea][] = $objhcare;

									$totalCoverage += $nCoverage;
								}
							}  // while loop ...
						}	// RecordCount() ...
					}	// Execute() ...
				}
				break;

			case 'XC':
				$this->msc_confine_benefits = array();

				if (!$this->is_coveredbypkg) {
						$strSQL = "select distinct ci.hcare_id, firm_id, name, hb.benefit_id, bs.basis, bs.bsked_id ".
								"   from ((care_insurance_firm as ci inner join ".
								"            (select * from seg_hcare_bsked as shb ".
								"                where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
								"                   and (shb.basis & 1) ".
								"                   and (select max(effectvty_dte) as latest ".
								"                           from seg_hcare_bsked as shb2 ".
									"                           where str_to_date(shb2.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "' ".
									"                              and shb2.hcare_id = shb.hcare_id ".
								"                              and shb2.benefit_id = shb.benefit_id
																							 and (shb.basis & 1)
																							 and exists (select * from seg_hcare_confinetype as sc ".
								"                                             where sc.bsked_id = shb2.bsked_id and ".
								"                                                sc.confinetype_id = ". $this->confinetype_id. ")) = shb.effectvty_dte) as bs on ci.hcare_id = bs.hcare_id) ".
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
									$nCoverage = $this->skedvalues['amountlimit'] - $prevCoverage - ($this->getTotalMscCharge() * $this->getBillAreaDRate($sBillArea));

								if ($nCoverage > 0) {
									$objhcare = new HCareCoverage;

									$objhcare->setID($row['hcare_id']);
									$objhcare->setFirmID($row['firm_id']);
									$objhcare->setDesc($row['name']);

										// Apply adjusted coverage ...
										if (isset($this->adjusted_coverage[$row['hcare_id']][$sBillArea])) $nCoverage = ($nCoverage > $this->adjusted_coverage[$row['hcare_id']][$sBillArea]) ? $this->adjusted_coverage[$row['hcare_id']][$sBillArea] : $nCoverage;

									$objhcare->setCoverage($nCoverage);
									$objhcare->setAmountLimit( ($this->skedvalues['amountlimit'] - $prevCoverage) < 0 ? 0 : ($this->skedvalues['amountlimit'] - $prevCoverage) );

									// Add new supply object in collection (array) of the list of applicable benefits based on confinement.
									$this->msc_confine_benefits[] = $objhcare;

									$totalCoverage += $nCoverage;
								}
							}  // while loop ...
						}	// RecordCount() ...
					}	// Execute() ...
				}
				break;

			default:

		}

		if ($this->is_coveredbypkg) {
			$strSQL = "select distinct ci.hcare_id, firm_id, name, hb.benefit_id, bs.bsked_id, pkg.coverage                    \n
							from (((care_insurance_firm as ci inner join                                                       \n
									 (select * from seg_hcare_bsked as shb                                                     \n
										 where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '{$this->bill_dte}'      \n
											and (shb.basis & 16)                                                               \n
											and (select max(effectvty_dte) as latest                                           \n
													 from seg_hcare_bsked as shb2                                              \n
													 where str_to_date(shb2.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '{$this->bill_dte}'     \n
														and shb2.hcare_id = shb.hcare_id                                       \n
														and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte) as bs on ci.hcare_id = bs.hcare_id)        \n
							 inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id)                             \n
							 inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id)                            \n
							 inner join seg_applied_pkgcoverage as pkg on pkg.bill_area = '$sBillArea' and                     \n
								ci.hcare_id = pkg.hcare_id and (pkg.ref_no = concat('T', si.encounter_nr) or pkg.ref_no = '{$this->old_bill_nr}') \n
							where hb.is_overall = 1 and (si.encounter_nr = '". $this->current_enr. "'".$filter.")              \n
							order by pkg.priority, bs.effectvty_dte desc";

			if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
					while ($row = $result->FetchRow()) {
						if ($sBillArea == 'AC') {
							$ndays = $this->days_count;                // Actual days of accommodation.
							$nhrs  = $this->excess_hours;
						}

						$nCoverage = (is_null($row["coverage"])) ? 0 : $row["coverage"];

						if ($nCoverage > 0) {
							$objhcare = new HCareCoverage;

							$objhcare->setID($row['hcare_id']);
							$objhcare->setFirmID($row['firm_id']);
							$objhcare->setDesc($row['name']);
							$objhcare->setCoverage($nCoverage);
							if ($sBillArea == 'AC') $objhcare->setDaysCovered($ndays);
							$objhcare->setAmountLimit($nCoverage);

							// Add new supply object in collection (array) of the list of applicable benefits based on confinement.
							switch ($sBillArea) {
								case 'AC':
									$this->acc_confine_benefits[] = $objhcare;
									break;

								case 'MS':
									// Add new supply object in collection (array) of the list of applicable benefits based on confinement.
									if ($sProdClass == 'M')
										$this->med_confine_benefits[] = $objhcare;
									else
										$this->sup_confine_benefits[] = $objhcare;
									break;

								case 'HS':
									$this->srv_confine_benefits[] = $objhcare;
									break;

								case 'OR':
									$this->ops_confine_benefits[] = $objhcare;
									break;

								case 'D1':
								case 'D2':
								case 'D3':
								case 'D4':
									$this->pfs_confine_benefits[$sBillArea][] = $objhcare;
									break;

								case 'XC':
									$this->msc_confine_benefits[] = $objhcare;
							}

							$totalCoverage += $nCoverage;
						}
					}
				}
                else
                    if (!$this->isfreedist) {      // ... no specific application of package limit yet ...
                        $issurgical  = $this->isSurgicalCase();
                        $strSQL = "select si.hcare_id, ci.firm_id, ci.name, sp.amountlimit                                                                                        \n
                                        from seg_hcare_packages as sp                                                                           \n
                                         inner join (select * from seg_hcare_bsked as shb                                                       \n
                                                        where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '{$this->bill_dte}'        \n
                                                             and (shb.basis & 16)                                                               \n
                                                             and (select max(effectvty_dte) as latest                                           \n
                                                                    from seg_hcare_bsked as shb2                                                \n
                                                                    where shb2.hcare_id = shb.hcare_id                                          \n
                                                                     and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte) as bs on bs.bsked_id = sp.bsked_id   \n
                                         inner join seg_encounter_insurance as si on si.hcare_id = bs.hcare_id                                  \n
                                         inner join seg_packages as spm on sp.package_id = spm.package_id                                       \n
                                         INNER JOIN care_insurance_firm ci ON ci.hcare_id = si.hcare_id                                         \n
                                        where (si.encounter_nr = '". $this->current_enr. "'".$filter.") and sp.package_id = {$this->package_id} \n
                                        order by priority, bs.effectvty_dte desc";
                        if ($result = $db->Execute($strSQL)) {
                            if ($result->RecordCount()) {
                                while ($row = $result->FetchRow()) {
                                    $nCoverage = 0;
                                    $amountlimit = $row['amountlimit'];

                                    switch ($sBillArea) {
                                        case 'AC':
                                            break;

                                        case 'MS':
                                            break;

                                        case 'HS':
                                            break;

                                        case 'OR':
                                            break;

                                        case 'D1':
                                        case 'D2':
                                            $rate = $this->getCaseRatePkgLimit('D2', $issurgical);
                                            $nCoverage = $amountlimit * $rate;

                                            $total_pf = 0;
//                                            if ($sBillArea == 'D2')
//                                                $this->getTotalPFParams($ndays, $nrvu, $npf, 'D2', $nRoleLevel, true, $sProdClass, $opcode);
//                                            else
//                                                $this->getTotalPFParams($ndays, $nrvu, $npf, 'D2', 0, true, '', '');
                                            $npf = $this->getTotalPFCharge('D2');
                                            $total_pf += $npf;

                                            if ($total_pf < $nCoverage) {
                                                if ($sBillArea == 'D2') {
                                                    $nCoverage = $total_pf;
                                                    break;
                                                }
                                                else {
                                                    $nCoverage -= $total_pf;
                                                }
                                            }
                                            else {
                                                if ($sBillArea == 'D2')
                                                    break;
                                                else
                                                    $nCoverage = 0;
                                            }

                                            if ($nCoverage > 0) {
                                                $this->getTotalPFParams($ndays, $nrvu, $npf, 'D1', $nRoleLevel, true, $sProdClass, $opcode);
                                                if ($npf < $nCoverage) {
                                                    $nCoverage = $npf;
                                                }
                                            }
                                            break;

                                        case 'D3':
                                            // Compute the % for surgeons ...
                                            $rate = $this->getCaseRatePkgLimit('D3', $issurgical);
                                            $nCoverage = $amountlimit * $rate;

                                            $this->getTotalPFParams($ndays, $nrvu, $npf_d3, 'D3', $nRoleLevel, true, $sProdClass, $opcode);
                                            if ($npf_d3 < $nCoverage) {
                                                $nCoverage = $npf_d3;
                                            }

                                            // Compute if there is no anaesthesiologist PF ...
                                            $pfd4 = $this->getTotalPFCharge('D4');
                                            if ($pfd4 == 0) {
                                                $rate = $this->getCaseRatePkgLimit('D4', $issurgical);
                                                $nCoverage += $amountlimit * $rate;

                                                if ($npf_d3 < $nCoverage) {
                                                    $nCoverage = $npf_d3;
                                                }
                                            }
                                            break;

                                        case 'D4':
                                            // Compute the % for anaesthesiologists ...
                                            $rate = $this->getCaseRatePkgLimit('D4', $issurgical);
                                            $nCoverage = $amountlimit * $rate;

                                            $this->getTotalPFParams($ndays, $nrvu, $npf, 'D4', $nRoleLevel, true, $sProdClass, $opcode);
                                            if ($npf < $nCoverage) {
                                                $nCoverage = $npf;
                                            }
                                            break;

                                        case 'XC':

                                    }

                                    if ($nCoverage > 0) {
                                        $objhcare = new HCareCoverage;

                                        $objhcare->setID($row['hcare_id']);
                                        $objhcare->setFirmID($row['firm_id']);
                                        $objhcare->setDesc($row['name']);
                                        $objhcare->setCoverage($nCoverage);
                                        $objhcare->setAmountLimit($nCoverage);

                                        switch ($sBillArea) {
                                            case 'AC':
                                                $this->acc_confine_benefits[] = $objhcare;
                                                break;

                                            case 'MS':
                                                // Add new supply object in collection (array) of the list of applicable benefits based on confinement.
                                                if ($sProdClass == 'M')
                                                    $this->med_confine_benefits[] = $objhcare;
                                                else
                                                    $this->sup_confine_benefits[] = $objhcare;
                                                break;

                                            case 'HS':
                                                $this->srv_confine_benefits[] = $objhcare;
                                                break;

                                            case 'OR':
                                                $this->ops_confine_benefits[] = $objhcare;
                                                break;

                                            case 'D1':
                                            case 'D2':
                                            case 'D3':
                                            case 'D4':
                                                $this->pfs_confine_benefits[$sBillArea][] = $objhcare;
                                                break;

                                            case 'XC':
                                                $this->msc_confine_benefits[] = $objhcare;
                                        }

                                        $totalCoverage += $nCoverage;
                                    }
                                }       // while loop ...
                            }   // recordcount() ...
                        }   // execute() ...
                    }   // ... no specific application of package limit yet ...
			}
		}

		switch ($sBillArea) {
			case 'AC':
				if (!empty($this->acc_roomtype_benefits) && is_array($this->acc_roomtype_benefits))
					foreach($this->acc_roomtype_benefits as $objrb) {
							$this->acc_confine_benefits = array_merge($this->acc_confine_benefits, (array)$objrb->available_hplans);
							$totalCoverage += $objrb->getTotalCoverage();
					}
				$this->acc_confine_coverage = $totalCoverage;
				break;

			case 'MS':
				if ($sProdClass == 'M') {
					if (!empty($this->med_product_benefits) && is_array($this->med_product_benefits))
						foreach($this->med_product_benefits as $objmb) {
							$this->med_confine_benefits = array_merge($this->med_confine_benefits, (array)$objmb->available_hplans);
							$totalCoverage += $objmb->getTotalCoverage();
						}

					if (!$bCoverageInSked)
						$this->med_confine_coverage = $totalCoverage;
					else
						return $totalCoverage;
				}
				else {
					if (!empty($this->sup_product_benefits) && is_array($this->sup_product_benefits))
						foreach($this->sup_product_benefits as $objsb) {
							$this->sup_confine_benefits = array_merge($this->sup_confine_benefits, (array)$objsb->available_hplans);
							$totalCoverage += $objsb->getTotalCoverage();
						}
					$this->sup_confine_coverage = $totalCoverage;
				}
				break;

			case 'HS':
				if (!empty($this->hsp_service_benefits) && is_array($this->hsp_service_benefits)) {
					foreach($this->hsp_service_benefits as $objsrv) {
						if (!empty($objsrv->available_hplans))	{
							$this->srv_confine_benefits = array_merge($this->srv_confine_benefits, (array)$objsrv->available_hplans);
							$totalCoverage += $objsrv->getTotalCoverage();
						}
					}
				}
                if (!$bCoverageInSked)
                    $this->srv_confine_coverage = $totalCoverage;
                else
                    return $totalCoverage;
				break;

			case 'OR':
				if (!empty($this->hsp_ops_benefits) && is_array($this->hsp_ops_benefits)) {
					foreach($this->hsp_ops_benefits as $objOp) {
						if ($objOp->getOpCode() == $sProdClass) {
							$this->ops_confine_benefits = array_merge($this->ops_confine_benefits, (array)$objOp->available_hplans);
							$totalCoverage += $objOp->getTotalCoverage();
						}
					}
				}
				$this->ops_confine_coverage += $totalCoverage;
				break;

			case 'D1':
			case 'D2':
			case 'D3':
			case 'D4':
				$this->pfs_confine_coverage[$sBillArea] += $totalCoverage;
				break;

			case 'XC':
				if (!empty($this->hsp_msc_benefits) && is_array($this->hsp_msc_benefits)) {
					foreach($this->hsp_msc_benefits as $objmsc) {
						$this->msc_confine_benefits = array_merge($this->msc_confine_benefits, (array)$objmsc->available_hplans);
						$totalCoverage += $objmsc->getTotalCoverage();
					}
				}
				$this->msc_confine_coverage = $totalCoverage;
				break;

			default:

		}

        if (!$bCoverageInSked) return $totalCoverage;
	}	// .... end of getConfineBenefits

    function getPkgAmountLimit() {
        global $db;

		$filter = '';
		if ($this->prev_encounter_nr != '') $filter = " or si.encounter_nr = '$this->prev_encounter_nr'";

        $total = 0;
        $strSQL = "select sp.amountlimit                                                                                        \n
                        from seg_hcare_packages as sp                                                                           \n
                         inner join (select * from seg_hcare_bsked as shb                                                       \n
                                        where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '{$this->bill_dte}'        \n
                                             and (shb.basis & 16)                                                               \n
                                             and (select max(effectvty_dte) as latest                                           \n
                                                    from seg_hcare_bsked as shb2                                                \n
                                                    where shb2.hcare_id = shb.hcare_id                                          \n
                                                     and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte) as bs on bs.bsked_id = sp.bsked_id   \n
                         inner join seg_encounter_insurance as si on si.hcare_id = bs.hcare_id                                  \n
                         inner join seg_packages as spm on sp.package_id = spm.package_id                                       \n
                        where (si.encounter_nr = '". $this->current_enr. "'".$filter.") and sp.package_id = {$this->package_id} \n
                        order by priority, bs.effectvty_dte desc limit 1";

//		if ($_SESSION['sess_temp_userid']=='medocs')
//			$this->debugSQL = $strSQL;

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow())
                    $total += (is_null($row['amountlimit'])) ? 0 : $row['amountlimit'];
            }
        }

        return $total;
    }

    //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY
    function getPkgCode($bill_date) {
        global $db;

        $filter = '';
        if ($this->prev_encounter_nr != '') $filter = " or si.encounter_nr = '$this->prev_encounter_nr'";

        $strSQL = "select spm.pkg_phiccode                                                                                        \n
                        from seg_hcare_packages as sp                                                                           \n
                         inner join (select * from seg_hcare_bsked as shb                                                       \n
                                        where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '{$bill_date}'        \n
                                             and (shb.basis & 16)                                                               \n
                                             and (select max(effectvty_dte) as latest                                           \n
                                                    from seg_hcare_bsked as shb2                                                \n
                                                    where shb2.hcare_id = shb.hcare_id                                          \n
                                                     and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte) as bs on bs.bsked_id = sp.bsked_id   \n
                         inner join seg_encounter_insurance as si on si.hcare_id = bs.hcare_id                                  \n
                         inner join seg_packages as spm on sp.package_id = spm.package_id                                       \n
                        where (si.encounter_nr = '". $this->current_enr. "') and sp.package_id = {$this->package_id} \n
                        order by priority, bs.effectvty_dte desc limit 1";

        //if ($_SESSION['sess_temp_userid']=='medocs')
        //    $this->debugSQL = $strSQL;

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow())
                    $pkgcode = $row['pkg_phiccode'];
            }
        }

        return $pkgcode;
    }

    //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY

	function getPackageBenefits($pkg_id) {
		global $db;

		$this->pkg_benefits = array();

		$this->sql = "select ci.hcare_id, firm_id, name, hb.benefit_id, bs.bsked_id, sp.amountlimit
						 from (((care_insurance_firm as ci inner join
									 (select * from seg_hcare_bsked as shb
										 where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "'
											and (shb.basis & 16)
											and (select max(effectvty_dte) as latest
													from seg_hcare_bsked as shb2
													where str_to_date(shb2.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "'
													 and shb2.hcare_id = shb.hcare_id
													 and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte) as bs on ci.hcare_id = bs.hcare_id)
									 inner join seg_hcare_benefits as hb on bs.benefit_id = hb.benefit_id)
									 inner join seg_encounter_insurance as si on si.hcare_id = ci.hcare_id)
									 inner join seg_hcare_packages as sp on sp.bsked_id = bs.bsked_id
							where hb.is_overall = 1 and (si.encounter_nr = '". $this->current_enr. "'".$filter.") and sp.package_id = $pkg_id
							order by priority, bs.effectvty_dte desc";
		if ($this->result = $db->Execute($this->sql)) {
			if ($this->result->RecordCount()) {
				while ($row = $this->result->FetchRow()) {
					$objhcare = new HCareCoverage;

					$objhcare->setID($row['hcare_id']);
					$objhcare->setFirmID($row['firm_id']);
					$objhcare->setDesc($row['name']);
					$objhcare->setCoverage($row['amountlimit']);
					$objhcare->setAmountLimit($row['amountlimit']);

					// Add new health care object in collection (array) of the list of applicable benefits for package.
					$this->pkg_benefits[] = $objhcare;
				}

				return TRUE;
			}
		}

		return FALSE;
	}

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
    //edited by jasper 05/10/2013
    function applyClassificationDiscount() {
        global $db;
            $strSQL = "select scg.discountid, discountdesc, scg.discount, scg.discount_amnt ".
                  "   from seg_charity_grants as scg inner join seg_discount as sd ".
                  "      on scg.discountid = sd.discountid ".
                  "   where scg.encounter_nr = '". $this->current_enr . "'".
                  "   order by grant_dte desc limit 1";
            if ($result = $db->Execute($strSQL)) {
              $this->discounts = array();

              if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $objd = new BillingDiscount;

                    $objd->setDiscountID($row['discountid']);
                    $objd->setDiscountDesc($row['discountdesc']);

                    if ( ($row['discount_amnt'] == 0.00) || is_null($row['discount_amnt']) ) {
                        $objd->setDiscountRate($row['discount']);
                        $objd->setDiscountAmount(0.00);
                    } else {
                        $objd->setDiscountRate(0.00);
                        $objd->setDiscountAmount($row['discount_amnt']);
                    }

                    $this->discounts[] = $objd;
                }
              }
            }
    }
    //edited by jasper 05/10/2013
    function applyDiscounts() {
        global $db;

        if ( ($this->isCharity() || $this->isERPatient()) && ($this->old_bill_nr != '') ) {
            if ($this->prev_encounter_nr != '') $filter = " or scg.encounter_nr = '$this->prev_encounter_nr'";
            $strSQL = "select scg.discountid, discountdesc, scg.discount, scg.discount_amnt ".
                  "   from seg_charity_grants as scg inner join seg_discount as sd ".
                  "      on scg.discountid = sd.discountid ".
                  "   where (scg.encounter_nr = '". $this->current_enr. "'".$filter.") ".
                  "   order by grant_dte desc limit 1";
            if ($result = $db->Execute($strSQL)) {
              $this->discounts = array();

              if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    $objd = new BillingDiscount;

                    $objd->setDiscountID($row['discountid']);
                    $objd->setDiscountDesc($row['discountdesc']);
                    if ( ($row['discount_amnt'] == 0.00) || is_null($row['discount_amnt']) )
                        $objd->setDiscountRate($row['discount']);
                    else
                        $objd->setDiscountRate(0.00);
                    $objd->setDiscountAmount($row['discount_amnt']);

                    $this->discounts[] = $objd;
                }
              }
            }
        //edited by jasper 04/26/2013
            if (!empty($this->discounts)) {
                //$strSQL = "delete from seg_billing_discount where bill_nr = '". $this->old_bill_nr ."'";
                //$bSuccess = $db->Execute($strSQL);

                if ($bSuccess) {
                    $strSQL = "insert into seg_billing_discount (bill_nr, discountid, discount, discount_amnt) " .
                                        "   values ";
                    $i = 0;
                    if ($this->isSponsoredMember()) {
                        if ($i > 0) $strSQL .= ",";
                        $strSQL .= "('". $sbill_nr ."', '". 'NBB' ."', 0, ".$this->excess.")";
                    } elseif ($this->checkIfPHS()) {
                        if ($i > 0) $strSQL .= ",";
                            $strSQL .= "('". $sbill_nr ."', '". 'Inf' ."', 0, ".$this->excess.")";
                    } elseif ($this->isHSM()) {
                        if ($i > 0) $strSQL .= ",";
                            $strSQL .= "('". $sbill_nr ."', '". 'HSM' ."', 0, ".$this->excess.")";
                    } else {
                        foreach($this->discounts as $objdiscount) {
                            if ($i++ > 0) $strSQL .= ",";
                                $strSQL .= "('". $sbill_nr ."', '". $objdiscount->getDiscountID() ."', ". $objdiscount->getDiscountRate() .", ".$objdiscount->getDiscountAmount().")";
                        }
                    }
                }
                //added by jasper 04/16/2013
                $billdscnt_sql = $strSQL;
            } else {
                $strSQL = "insert into seg_billing_discount (bill_nr, discountid, discount, discount_amnt) " .
                                    "   values ";
                $i = 0;
                if ($this->isSponsoredMember() || $this->checkIfPHS()|| $this->isHSM()) {
                    if ($i > 0) $strSQL .= ",";

                    if($this->isSponsoredMember()) {
                    	$discountid = 'NBB';
                    } elseif ($this->isHSM()) {
                    	$discountid = 'HSM';
                    } else {
						$discountid = 'Inf';
                    }

                    $strSQL .= "('". $sbill_nr ."', '". $discountid ."', 0 , ".$this->excess.")";
                    $billdscnt_sql = $strSQL;
                }
            }

            $billdscnt_sql = $strSQL;

            // Save the discount applied ...
            if ($billdscnt_sql != "") $bSuccess = $db->Execute($billdscnt_sql);

        //removed by jasper 05/07/2013
       /*     if (!empty($this->discounts)) {
                $strSQL = "delete from seg_billing_discount where bill_nr = '". $this->old_bill_nr ."'";
                $bSuccess = $db->Execute($strSQL);

                if ($bSuccess) {
                    $strSQL = "insert into seg_billing_discount (bill_nr, discountid, discount, discount_amnt) " .
                                        "   values ";
                    $i = 0;
                    foreach($this->discounts as $objdiscount) {
                        if ($i++ > 0) $strSQL .= ",";
                        $strSQL .= "('". $this->old_bill_nr ."', '". $objdiscount->getDiscountID() ."', ". $objdiscount->getDiscountRate() .", ".$objdiscount->getDiscountAmount().")";
                    }

                    $billdscnt_sql = $strSQL;

                    // Save the discount applied ...
                    if ($billdscnt_sql != "") $bSuccess = $db->Execute($billdscnt_sql);
                }
            }     */    //removed by jasper 05/07/2013
        }
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
        // Discount only applies to in-patient with charity accommodation ...
        if ($this->isCharity() || $this->isERPatient()) {
          $filter = '';

          if ($this->prev_encounter_nr != '') $filter = " or scg.encounter_nr = '$this->prev_encounter_nr'";
      //		$strSQL = "select scg.discountid, discountdesc, scg.discount ".
      //   				  "   from (seg_charity_grants_pid as scg inner join seg_discount as sd ".
      //      			  "      on scg.discountid = sd.discountid) inner join care_encounter as ce ".
      //                  "      on ce.pid = scg.pid ".
      //   				  "   where (ce.encounter_nr = '". $this->current_enr. "'".$filter.") ".
      //      			  "      and str_to_date(grant_dte, '%Y-%m-%d %H:%i:%s') < '".$this->bill_dte."' ".
      //   				  "   order by grant_dte desc limit 1";


          if ($this->old_bill_nr == '') {
            $strSQL = "select scg.discountid, discountdesc, scg.discount, scg.discount_amnt ".
                  "   from seg_charity_grants as scg inner join seg_discount as sd ".
                  "      on scg.discountid = sd.discountid ".
                  "   where (scg.encounter_nr = '". $this->current_enr. "'".$filter.") ".
                  "      and str_to_date(grant_dte, '%Y-%m-%d %H:%i:%s') < '".$this->bill_dte."' ".
                  "   order by grant_dte desc limit 1";
          }
          else {
            $strSQL = "select sbd.discountid, discountdesc, sbd.discount, sbd.discount_amnt
                        from seg_billing_discount sbd inner join seg_discount sd
                          on sbd.discountid = sd.discountid
                        where sbd.bill_nr = '".$this->old_bill_nr."' and sbd.discountid<>'" . NOBALANCEBILLING . "'";
          }

          if ($result = $db->Execute($strSQL)) {
            $this->discounts = array();

            if ($result->RecordCount()) {
              while ($row = $result->FetchRow()) {
                $objd = new BillingDiscount;

                $objd->setDiscountID($row['discountid']);
                $objd->setDiscountDesc($row['discountdesc']);
                if ( ($row['discount_amnt'] == 0.00) || is_null($row['discount_amnt']) )
                    $objd->setDiscountRate($row['discount']);
                else
                    $objd->setDiscountRate(0.00);
                $objd->setDiscountAmount($row['discount_amnt']);

                $this->discounts[] = $objd;
              }
      //                $this->correctDiscount();       // Correct the discount applied from classification if net due becomes negative ...
            }
          }
        } // ... if isCharity()
	}   // getDiscounts

	function getTotalDiscount() {
		$n_discount = 0.000;
        $n_amount = 0.00;

		if (!isset($this->discounts) && !is_array($this->discounts)) {
//		if (empty($this->discounts))
			$this->getDiscounts();
		}

//		if (!empty($this->discounts)) {
		if (is_array($this->discounts) && (count($this->discounts) > 0)) {
			$i = 1;
			foreach($this->discounts as $objd) {
                $n_amount += $objd->getDiscountAmount();
                if ($i++ == 1)
                    $n_discount = $objd->getDiscountRate();
                else
                    $n_discount *= $objd->getDiscountRate();
			}
		}

		// Get discounts in billable areas ....
		$adiscount = $this->getBillAreaDiscount('AC');
		$adiscount += $this->getBillAreaDiscount('MS','M');
		$adiscount += $this->getBillAreaDiscount('MS','S');
		$adiscount += $this->getBillAreaDiscount('HS');
		$adiscount += $this->getBillAreaDiscount('OR');
		$adiscount += $this->getBillAreaDiscount('D1');
		$adiscount += $this->getBillAreaDiscount('D2');
		$adiscount += $this->getBillAreaDiscount('D3');
		$adiscount += $this->getBillAreaDiscount('D4');
		$adiscount += $this->getBillAreaDiscount('XC');

		// Correct the discount applied from classification if net due becomes negative ...
        return ($n_amount != 0.00) ? $n_amount : round( (($this->getTotalBillAmount() - $this->getTotalCoverage() - $adiscount - $this->getPreviousPayments()) * $n_discount), 2 );
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
		$area_array = array('AC', 'D1', 'D2', 'D3', 'D4');
		if (!($this->isCharity() && (in_array($sbill_area, $area_array)))) {
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
		}
		return($n_rate);
	}

	function getBillAreaDiscount($sbill_area, $sms_class = '') {
		global $db;

		$n_discount = 0.00;
		$n_prevdiscount = 0.00;
        $excess = 0.00;

//		$area_array = array('AC', 'D1', 'D2', 'D3', 'D4');
//		if ($this->isCharity() && (in_array($sbill_area, $area_array))) {
        // Fix for MS-538 ....
//        if ($this->isCharity()) {
        if ($this->isCharity() && !$this->isMedicoLegal() && !$this->isPHIC()) {
			switch ($sbill_area) {
				case 'AC':
					$n_discount = $this->compTotalAccommodationChrg() - $this->acc_confine_coverage - $this->getTotalMandatoryExcess();
                    $totalCharityCharge = $this->compTotalAccommodationCharity();
                    $n_discount = ($n_discount > $totalCharityCharge) ? $totalCharityCharge : $n_discount;
					break;

				// Apply excess as discount for billable areas 'MS', 'HS', 'OR' and 'XC' if membership category is "Sponsored" ...
                case 'MS':
                    if ($this->isSponsoredMember() || $this->isHSM()) {
                      if ($sms_class == 'M')
                          $n_discount = $this->getTotalMedCharge() - $this->med_confine_coverage;
                      else
                          $n_discount = $this->getTotalSupCharge() - $this->sup_confine_coverage;
                    }
                    break;

                case 'HS':
                    if ($this->isSponsoredMember() || $this->isHSM()) {
                        $n_discount = $this->getTotalSrvCharge() - $this->srv_confine_coverage;
                    }
                    break;

                case 'OR':
                    if ($this->isSponsoredMember() || $this->isHSM()) {
                        $n_discount = $this->getTotalOpCharge() - $this->ops_confine_coverage;
                    }
                    break;

				case 'D1':
				case 'D2':
				case 'D3':
				case 'D4':
					$this->getTotalPFParams($ndays, $nrvu, $npf, $sbill_area);
					if ($sbill_area == "D3")
					    $n_discount = $npf - $this->pfs_confine_coverage[$sbill_area]- $this->nonDiscountablePF;
					else
					    $n_discount = $npf - $this->pfs_confine_coverage[$sbill_area]; //- $this->nonDiscountablePF;
					break;

                case 'XC':
                    if ($this->isSponsoredMember() || $this->isHSM()) {
                        $n_discount = $this->getTotalMscCharge() - $this->msc_confine_coverage;
                    }
                    break;
			}
		}
		else {
            $strSQL = "select fn_get_bill_discountamnt('". $this->current_enr. "', '". $sbill_area ."', '".$this->bill_dte."') as discount";
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
                $strSQL = "select fn_get_bill_discountamnt('". $this->prev_encounter_nr. "', '". $sbill_area ."', '".$this->bill_dte."') as discount";
                if ($result = $db->Execute($strSQL)) {
                    if ($result->RecordCount()) {
                        $row = $result->FetchRow();
                        if (!is_null($row['discount'])) {
                            $n_prevdiscount = $row['discount'];
                        }
                    }
                }
            }

            $n_discount += $n_prevdiscount;

            if ($n_discount == 0) {
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
                $n_discount = ($n_discount > $n_prevdiscount) ? $n_discount : $n_prevdiscount;      // Return the highest discount applied.

                $npf      = 0;
                $ndays    = 0;
                $nrvu     = 0;
                //edited by jasper 03/21/2013
                switch ($sbill_area) {
                    case 'AC':
                        $n_discount *= $this->compTotalAccommodationChrg();
                        //removed by jasper with bong 07/17/2013
                        //$totalCharityCharge = $this->compTotalAccommodationCharity();
                        //$n_discount = ($n_discount > $totalCharityCharge) ? $totalCharityCharge : $n_discount;
                        break;

                    case 'MS':
                        if ($sms_class == 'M') {
                            $n_discount *= $this->getTotalMedCharge();
                        } else {
                            $n_discount *= $this->getTotalSupCharge();
                        }
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
            }  // if discount rate != 0
            else {  // if discount rate != 0
               switch ($sbill_area) {
                    case 'AC':
                        if ($this->compTotalAccommodationChrg() == 0)
                            $n_discount = 0.00;
                        break;

                    case 'MS':
                        if ($sms_class == 'M')
                            if ($this->getTotalMedCharge() == 0)
                                $n_discount = 0.00;
                        else
                            if ($this->getTotalSupCharge() == 0)
                                $n_discount = 0.00;
                        break;

                    case 'HS':
                        if ($this->getTotalSrvCharge() == 0)
                            $n_discount = 0.00;
                        break;

                    case 'OR':
                        if ($this->getTotalOpCharge() == 0)
                            $n_discount = 0.00;
                        break;

                    case 'D1':
                    case 'D2':
                    case 'D3':
                    case 'D4':
                        $this->getTotalPFParams($ndays, $nrvu, $npf, $sbill_area);
                        if ($npf == 0)
                            $n_discount = 0.00;
                        break;

                    case 'XC':
                        if ($this->getTotalMscCharge() == 0)
                            $n_discount = 0.00;
                        break;
                }
            }
		}
		return round($n_discount, 2);
	}

    //added by jasper 03/06/2013
    function UndiscountableService($servcode) {
        global $db;
        //$strSQL = "SELECT service_code, alt_service_code, name, price FROM seg_other_services WHERE is_discountable = 1";
        $strSQL = "SELECT is_discountable FROM seg_other_services WHERE service_code = '" . $servcode . "'";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    if ($row['is_discountable']==1)
                       return true;
                    else
                       return false;
                }
             }
         }
    }
    //added by jasper 03/06/2013

	function getPreviousPayments() {
		global $db;

		if (isset($this->total_prevpayment) && !$this->forceCompute) {
			return $this->total_prevpayment;
		}

		$total_payment = 0;

		$this->prev_payments = array();

		$filter = array('','');

		if ($this->prev_encounter_nr != '') $filter[0] = " or sp.encounter_nr = '$this->prev_encounter_nr'";
		if ($this->prev_encounter_nr != '') $filter[1] = " or spd.encounter_nr = '$this->prev_encounter_nr'";
//		$strSQL = "select spr.or_no, or_date, sum(sp.amount_due) as or_amnt ".
//					"   from seg_pay as sp inner join ".
//					"      (seg_pay_request as spr left join seg_billing_encounter as sbe ".
//					"         on spr.ref_no = sbe.bill_nr and spr.ref_source = 'PP') ".
//					"      on sp.or_no = spr.or_no " .
//					"   where (sp.encounter_nr = '". $this->current_enr. "'".$filter[0].") ".
//					"      and (str_to_date(or_date, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
//					"         and str_to_date(or_date, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
//					"      and spr.ref_source = 'PP' and cancel_date is null ".
//					"   group by spr.or_no, or_date ".
//					" union ".
//					"select spd.or_no, or_date, sum(deposit) as or_amnt ".
//					"   from seg_pay as sp1 inner join seg_pay_deposit as spd ".
//					"      on sp1.or_no = spd.or_no " .
//					"   where (spd.encounter_nr = '". $this->current_enr. "'".$filter[1].") ".
//					"      and (str_to_date(or_date, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' " .
//					"         and str_to_date(or_date, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "') " .
//					"      and cancel_date is null ".
//					"   group by spd.or_no, or_date ".
//					"   order by or_date";
        //edited by jasper 08/29/2103 -Fix for OB Annex co-payments BUG#:279
		$strSQL = "select spr.or_no, or_date, sum(sp.amount_due) as or_amnt ".
					"   from seg_pay as sp inner join ".
					"      (seg_pay_request as spr left join seg_billing_encounter as sbe ".
					"         on spr.ref_no = sbe.bill_nr and spr.ref_source = 'PP') ".
					"      on sp.or_no = spr.or_no " .
					"   where (sp.encounter_nr = '". $this->current_enr. "'".$filter[0].") ".
					"         and str_to_date(or_date, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "' " .
					"      and spr.ref_source = 'PP' and spr.service_code <> 'OBANNEX' and cancel_date is null and sbe.is_deleted IS NULL ".
					"   group by spr.or_no, or_date ".
					" union ".
					"select spd.or_no, or_date, sum(deposit) as or_amnt ".
					"   from seg_pay as sp1 inner join seg_pay_deposit as spd ".
					"      on sp1.or_no = spd.or_no " .
					"   where (spd.encounter_nr = '". $this->current_enr. "'".$filter[1].") ".
					"         and str_to_date(or_date, '%Y-%m-%d %H:%i:%s') < '" . $this->bill_dte . "' " .
					"      and cancel_date is null ".
					"   group by spd.or_no, or_date ".
					"   order by or_date";
        //edited by jasper 08/29/2103 -Fix for OB Annex co-payments BUG#:279

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

		return $total_payment;
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

		return $s_bill_nr;
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
		return $ntotal;
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

		return $ntotal;
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

//    function getTotalActualPFCoverage($sbill_area) {
//
//    }

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
	function getAppliedHSCoverage($nhcareid = -1, $source = '') {
		global $db;

		$srefno = ($this->old_bill_nr == '') ? 'T'.$this->current_enr : $this->old_bill_nr;
		$total  = 0;

		$firm_filter = ($nhcareid == -1) ? "" : " and hcare_id = $nhcareid";
		if ($source == '') {
		$strSQL = "select sum(coverage) as totalcoverage
						from seg_applied_coverage
						where ref_no = '$srefno' and source <> 'M'".$firm_filter;
		}
		else
			if (is_array($source)) {
				$srcgrp = "('".implode("','",$source)."')";
				$strSQL = "select sum(coverage) as totalcoverage
								from seg_applied_coverage
								where ref_no = '$srefno' and source in {$srcgrp}".$firm_filter;
			}
			else {
				$strSQL = "select sum(coverage) as totalcoverage
								from seg_applied_coverage
								where ref_no = '$srefno' and source = '{$source}'".$firm_filter;
			}
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				if ($row = $result->FetchRow())
					$total = (is_null($row['totalcoverage']) || $row['totalcoverage'] == '') ? 0 : $row['totalcoverage'];
			}
		}

		$this->srv_confine_coverage = $total;

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

		$this->med_confine_coverage = $total;

		return($total);
	}

    //added by jasper 09/13/2013 - FIX FOR BUG#120
    //Get Discount first before setting the claims of Doctor's PF
    function getPFDiscount($pfarea, $npf, $nclaim) {
        global $db;

        $n_discount = 0.00;
        $n_prevdiscount = 0.00;

        $area_array = array('AC', 'D1', 'D2', 'D3', 'D4');
        //edited by jasper 04/16/2013    -CONDITION SHOULD BE THE SAME WITH FUNCTION getBillAreaDiscount IN class_billing.php
        //if ($this->objBill->isCharity() && (in_array($pfarea, $area_array))) {
        if ($this->isCharity() && !$this->isMedicoLegal() && !$this->isPHIC() && (in_array($pfarea, $area_array))) {
            switch ($pfarea) {
                case 'D1':
                case 'D2':
                case 'D3':
                case 'D4':
                    $n_discount = $npf - $nclaim;
                    break;
            }
        }
        else {
            $strSQL = "select fn_get_bill_discount('". $this->current_enr. "', '". $pfarea ."', '".$this->bill_date."') as discount";
            if ($result = $db->Execute($strSQL)) {
                if ($result->RecordCount()) {
                    $row = $result->FetchRow();
                    if (!is_null($row['discount'])) {
                        $n_discount = $row['discount'];
                    }
                }
            }

            // .... get discount rate applied to bill area of encounter while at ER, if there is one.
            if ($this->prev_encounter_no != '') {
                $strSQL = "select fn_get_bill_discount('". $this->prev_encounter_no. "', '". $pfarea ."', '".$this->bill_date."') as discount";
                if ($result = $db->Execute($strSQL)) {
                    if ($result->RecordCount()) {
                        $row = $result->FetchRow();
                        if (!is_null($row['discount'])) {
                            $n_prevdiscount = $row['discount'];
                        }
                    }
                }
            }

            $n_discount = ($n_discount > $n_prevdiscount) ? $n_discount : $n_prevdiscount;      // Return the highest discount applied.
            switch ($pfarea) {
                case 'D1':
                case 'D2':
                case 'D3':
                case 'D4':
                    $n_discount *= $npf;
                    break;
            }
        }
        return round($n_discount, 2);
    }
    //added by jasper 09/13/2013 - FIX FOR BUG#120
    
    
	// This function constructs the array of doctor's charges and claims per applicable health insurance of patient ...
	// ASSUMPTION:  1.  getProfFeesList() has already been called.
	//              2.  getProfFeesBenefits() has already been called.
	//              3.  getConfineBenefits() for bill areas D1 to D4 has already been called.
//	function getPerDrPFandClaims($n_pf = 0, $n_pfcoverage = 0, $sRoleArea) {
	function getPerDrPFandClaims($sRoleArea) {
		$this->pf_claims = array();

		if ((!empty($this->hcare_coverage)) && (!empty($this->proffees_list))) {
			foreach($this->hcare_coverage as $objhcare) {
				foreach($this->proffees_list as $objpf) {
					$this->initProfFeesCoverage($sRoleArea);
					if (($objpf->getRoleBenefit() == $sRoleArea) && !$objpf->getIsExcludedFlag()) {
						$objclaim = new PFClaimPerHCare;

						$objclaim->setDrNr($objpf->getDrNr());
						$objclaim->setDrCharge($objpf->getDrCharge());
						$objclaim->setRoleArea($sRoleArea);

						// ... compute corresponding claim of doctor.
	//					if ($n_pf != 0)
	//						$n_claim = ($objpf->getDrCharge() * $n_pfcoverage) / $n_pf;
	//					else
	//						$n_claim = 0;

	//					$n = round($n_claim, 2);
	//					if ($n < $n_claim) $n = $n + 0.01;
	//					if ($n > $objpf->getDrCharge()) $n = $objpf->getDrCharge();

						$opcodes = $objpf->getOpCodes();
						if ($opcodes != '') $opcodes = explode(";", $opcodes);
						if (is_array($opcodes)) {
							foreach($opcodes as $v) {
								$i = strpos($v, '-');
								if (!($i === false)) {
									$code = substr($v, 0, $i);
									$this->getConfineBenefits($sRoleArea, $objpf->getDrNr(), $objpf->getRoleLevel(), false, $objhcare->getID(), $code);

                                    if ($this->is_coveredbypkg) break;
								}
							}
						}
						else
							$this->getConfineBenefits($sRoleArea, $objpf->getDrNr(), $objpf->getRoleLevel(), false, $objhcare->getID());

                        //added by jasper 09/13/2013 - FIX FOR BUG#120    
                        $pf_discount = $this->getPFDiscount($sRoleArea, $objpf->getDrCharge(), $this->pfs_confine_coverage[$sRoleArea]);    
                        if ($objpf->getDrCharge() - $pf_discount <= $this->pfs_confine_coverage[$sRoleArea]) {
                            $pf_confine_coverage_perrole = $objpf->getDrCharge() - $pf_discount;    
                        } else {
                            $pf_confine_coverage_perrole = $this->pfs_confine_coverage[$sRoleArea];                            
                        }
                        //added by jasper 09/13/2013 - FIX FOR BUG#120
                        
                        //edited by jasper 09/13/2013
						//$objclaim->setDrClaim($this->pfs_confine_coverage[$sRoleArea]);
                        $objclaim->setDrClaim($pf_confine_coverage_perrole);
						$objclaim->setID($objhcare->getID());
						$objclaim->setDesc($objhcare->getDesc());

						$this->pf_claims[] = $objclaim;
					}
				}
				$this->initProfFeesCoverage($sRoleArea);

			}  // for loop
		}    // if empty hcare_coverage
	} // end of function

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
					$objhcare->setFirmID($row["firm_id"]);
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

//	function getPerDrClaimPerHCare($n_pf = 0) {
//		$this->pf_claims_per_hcare = array();

//		if (!empty($this->hcare_coverage)) {
//			foreach($this->hcare_coverage as $objhcare) {
//				if (!empty($this->pf_claims)) {
//					foreach($this->pf_claims as $objpf) {
//						$objclaim = $this->isPFClaimExists($objhcare->getID(), $objpf->getDrNr());
//						if (!$objclaim) {
//							$objclaim = new PFClaimPerHCare;
//							$objclaim->setID($objhcare->getID());
//							$objclaim->setDesc($objhcare->getDesc());
//							$objclaim->setDrNr($objpf->getDrNr());

//							$n_charge = 0;
//							$n_claim  = 0;

//							$bExists = false;
//						}
//						else {
//							$n_charge = $objclaim->getDrCharge();
//							$n_claim  = $objclaim->getDrClaim();

//							$bExists = true;
//						}

//						if ($n_pf != 0) {
							// Review this computation ...
//							$n = $this->getActualPFCoverage($objhcare->getID());
//							$dr_claim  = ($objpf->getDrCharge() * $n) / $n_pf;
//							if ($objpf->getDrClaim() != 0)
//								$dr_charge = ($dr_claim * $objpf->getDrCharge()) / $objpf->getDrClaim();
//							else
//								$dr_charge = 0;

//							$n_charge += $dr_charge;
//							$n_claim  += $dr_claim;
//						}

//						$n = round($n_charge, 2);
//						if ($n < $n_charge) $n = $n + 0.01;
//						if ($n > $objpf->getDrCharge()) $n = $objpf->getDrCharge();
//						$objclaim->setDrCharge($n);

//						$n = round($n_claim, 2);
//						if ($n < $n_claim) $n = $n + 0.01;
//						if ($n > $objpf->getDrClaim()) $n = $objpf->getDrClaim();
//						$objclaim->setDrClaim($n);

//						if (!$bExists) $this->pf_claims_per_hcare[] = $objclaim;
//					}
//				}
//			}
//		}
//	}

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
					"                       and cpi1.pid <> cpi0.pid and and cpi1.is_void = 0 and cpi1.hcare_id = cpi0.hcare_id ".
					"      and cpi1.insurance_nr = cpi0.insurance_nr) ".
					"      and cpi0.is_principal <> 0 and cpi0.is_void = 0";

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

//		$this->sql = "select * from seg_encounter_confinement ".
//					 "   where str_to_date(classify_dte, '%Y-%m-%d %H:%i:%s') < '" . $bill_dte ."' and ".
//					 "      encounter_nr = '$this->current_enr' ".
//					 "   order by classify_dte desc limit 1 for update";

//		if($result = $db->Execute($this->sql)){
/***
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
			}else{   ***/

		//Insert new data to seg_encounter_confinement
		if (strcmp($bill_dte, "0000-00-00 00:00:00") == 0) {
			$classify_dte = date('Y-m-d H:i:s');
			$create_time = date('Y-m-d H:i:s');
		}
		else {
			$classify_dte = $bill_dte;
			$create_time  = date('Y-m-d H:i:s');
		}
		$classify_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($classify_dte)));
		$create_time = strftime("%Y-%m-%d %H:%M:%S", strtotime($create_time));

		$this->sql = "INSERT INTO seg_encounter_confinement(encounter_nr, confinetype_id, classify_id, classify_dte, create_id, create_time) ".
					 "   VALUES('".$this->current_enr."', ".$this->confinetype_id.", '".$_SESSION['sess_user_name']."' , '".$classify_dte."' , '".$_SESSION['sess_user_name']."','".$create_time."')";
		$bSuccess = $this->Transact($this->sql);

//			}
//		}

		return $bSuccess;
	}

	// This function saves the billing info ...
	// ASSUMPTION:  1.  All the functions that extract the accommodation, medicines, supplies, doctors' fees and
	//			            other transactions of patient have been called.
	//				      2.  getTotalDiscount() has been called.
	//			        3.  getPreviousPayments() has been called.
	function saveBilling() {
		global $db;

		$bSuccess = false;
		$sbill_nr = "";

		$billdscnt_sql = "";
		$billcover_sql = "";
		$conftrack_sql = "";
		$pf_sql = "";

		if (!isset($this->confinetype_id)) {
			$this->errmsg = "System cannot save a billing without the case type set!";
			return(FALSE);
		}

		// Check if for final billing ....
		$this->isForFinalBilling();

		// Compute total doctors' fees ...
		$total_df = $this->getTotalPFCharge();

		// Save confinement type derived from the ICD in care_encounter_diagnosis ...
		$bSuccess = $this->saveConfinementType();

		$this->getAccommodationType();

		$nTotalAccChrg = $this->compTotalAccommodationChrg();
		$nTotalMedChrg = $this->getTotalMedCharge();
		$nTotalSupChrg = $this->getTotalSupCharge();
		$nTotalSrvChrg = $this->getTotalSrvCharge();
		$nTotalOpsChrg = $this->getTotalOpCharge();
		$nTotalMscChrg = $this->getTotalMscCharge();
		// array for OB co-payments ADDED BY JASPER 10/03/2013 for BUG# 279
		$total_previous_payment = $this->total_prevpayment + $this->total_ob_payments;
		// array for OB co-payments ADDED BY JASPER 10/03/2013 for BUG# 279
		if ($this->old_bill_nr == '') {
			$sbill_nr = $this->getNewBillingNr();

			// i.e new billing ... no previous saved billing.
			$strSQL = "insert into seg_billing_encounter (bill_nr, bill_dte, bill_frmdte, encounter_nr, accommodation_type, total_acc_charge, total_med_charge, ".
						"      total_sup_charge, total_srv_charge, total_ops_charge, total_doc_charge, total_msc_charge, total_prevpayments, applied_hrs_cutoff, is_final, " .
						"      modify_id, create_id, create_dt) ".
						"   values ('".$sbill_nr."', '".$this->bill_dte."', '".$this->bill_frmdte."', '".$this->current_enr."', ".$this->accomm_typ_nr.", ".
						"           ".$nTotalAccChrg.", ".$nTotalMedChrg.", ".$nTotalSupChrg.", ".
						"           ".$nTotalSrvChrg.", ".$nTotalOpsChrg.", ".$total_df.", ".$nTotalMscChrg.", ".$total_previous_payment.", ".$this->cutoff_hrs.", ".
						"           ".($this->bfinal ? 1 : 0).", '".$_SESSION['sess_temp_userid']."', '".$_SESSION['sess_temp_userid']."', NOW())";
			$bSuccess = $db->Execute($strSQL);
			if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot insert billing for encounter ".$this->current_enr."."."\n".$strSQL;
		}
		else {
			$sbill_nr = $this->old_bill_nr;
		}

        //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY
        if ($bSuccess) {
            if ($this->isWellBaby()) {
                $arrDischarge = explode(" ", $this->bill_dte);
                $disch_date = $arrDischarge[0];
                $disch_time = $arrDischarge[1];
                $strSQL = "UPDATE care_encounter SET is_discharged = 1, discharge_date = '" . $disch_date . "', discharge_time = '" . $disch_time . "' " .
                          " WHERE encounter_nr = '" . $this->current_enr ."'";
                $bSuccess = $db->Execute($strSQL);
                if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot update record for encounter ".$this->current_enr."."."\n".$strSQL;
            }
        }
        //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY

		if ($bSuccess) {
			$this->getPerHCareCoverage();		// Get the actual coverage of all billable areas per health insurance.
			$s_pid = $this->getPIDinEncounter();

            //added by jasper 09/12/2013 - FOR BUG#120 PATIENTS WITH SENIOR CITIZEN DISCOUNT AND PAYWARD
            $acc_confine_coverage = round($this->acc_confine_coverage, 2);
            $med_confine_coverage = round($this->getAppliedMedsCoverage(), 2);
            $sup_confine_coverage = round($this->sup_confine_coverage, 2);
            $srv_confine_coverage = round($this->getAppliedHSCoverage(), 2);
            $ops_confine_coverage = round($this->ops_confine_coverage, 2);
            $pfs_confine_coverage_d1 = round($this->pfs_confine_coverage['D1'], 2);
            $pfs_confine_coverage_d2 = round($this->pfs_confine_coverage['D2'], 2);
            $pfs_confine_coverage_d3 = round($this->pfs_confine_coverage['D3'], 2);
            $pfs_confine_coverage_d4 = round($this->pfs_confine_coverage['D4'], 2);
            $msc_confine_coverage = round($this->getMscConfineCoverage(), 2);
            $ms_confine_coverage = 0; //Added by Jarel 10/10/2013 Initialize this variable;
            
            if ((!$this->isCharity() && $this->iswithSCDiscount()) || (!$this->isPHIC() && !$this->isMedicoLegal())) { 
                $total_discount_acc = round($this->getBillAreaDiscount('AC'), 2);
                $total_discount_hs = round($this->getBillAreaDiscount('HS'), 2);
                $total_discount_ms = round($this->getBillAreaDiscount('MS','M'), 2);
                $total_discount_ms += round($this->getBillAreaDiscount('MS','S'), 2);
                $total_discount_or = round($this->getBillAreaDiscount('OR'), 2);
                $total_discount_xc = round($this->getBillAreaDiscount('XC'), 2);
                
                $pf_benefits = $this->getPFBenefits();
                $prevrole_area = '';
                if (!empty($pf_benefits) && is_array($pf_benefits)) {
                        foreach ($pf_benefits as $key=> $value) {
                        if ($value->role_area == $prevrole_area) continue;
                                $prevrole_area = $value->role_area;
                                if (!$this->isPHIC() && $this->isOBAnnex() && $value->role_area=='D3') {
                                    $total_discount_pf += 0;
                                } else {
                                    $total_discount_pf += round($this->getBillAreaDiscount($value->role_area), 2);
                                }       
                        }
                }       
                $total_discount = $total_discount_acc + $total_discount_hs + $total_discount_ms + $total_discount_or + $total_discount_xc + $total_discount_pf;
            }   
        
            if ($this->isPHIC()) {
                //adjust ACCOMMODATION coverage from SC discount
                if ($this->total_acc_charge - $total_discount_acc <= $acc_confine_coverage) {
                    $acc_confine_coverage = $this->total_acc_charge - $total_discount_acc;
                }
                
                //adjust XLC or Hospital Services coverage from SC discount
                $total_service_charge = $this->getTotalSrvCharge();
                if ($total_service_charge - $total_discount_hs <= $srv_confine_coverage) {
                    $srv_confine_coverage = $total_service_charge - $total_discount_hs;
                }
                
                //adjust Drugs and Meds coverage from SC discount
                $ms_confine_coverage = $med_confine_coverage + $sup_confine_coverage;
                if ($this->total_med_charge - $total_discount_ms <= $ms_confine_coverage) {
                    $ms_confine_coverage = $this->total_med_charge - $total_discount_ms;
                }
                
                //adjust OPS coverage from SC discount
                if ($this->total_op_charge - $total_discount_or <= $ops_confine_coverage) {
                    $ops_confine_coverage = $this->total_op_charge - $total_discount_or;
                }
                
                //adjust PF coverage from SC discount
                $pf_confine_coverage = $pfs_confine_coverage_d1 + $pfs_confine_coverage_d2 + $pfs_confine_coverage_d3 + $pfs_confine_coverage_d4;
                if ($this->total_pf_charge - $total_discount_pf <= $pf_confine_coverage) {
                    $pf_confine_coverage = $this->total_pf_charge - $total_discount_pf;
                }
                
                //adjust Misc coverage from SC discount
                if ($this->total_misc_charge - $total_discount_xc <= $msc_confine_coverage) {
                    $msc_confine_coverage = $this->total_misc_charge - $total_discount_xc;
                }
            }   
            //added by jasper 09/12/2013 - FOR BUG#120 PATIENTS WITH SENIOR CITIZEN DISCOUNT AND PAYWARD

			if (!empty($this->hcare_coverage)) {
				$billcover_sql = "insert into seg_billing_coverage (bill_nr, hcare_id, total_acc_coverage, total_med_coverage, total_sup_coverage, ".
												 "                                  total_srv_coverage, total_ops_coverage, total_d1_coverage, total_d2_coverage, ".
												 "                                  total_d3_coverage, total_d4_coverage, total_msc_coverage) " .
												 "   values ";
				$conftrack_sql = "insert into seg_confinement_tracker (pid, current_year, bill_nr, hcare_id, confine_days, principal_pid) " .
												 "   values ";
				$i = 0;
				foreach($this->hcare_coverage as $objhcare) {
					if ($i > 0) $billcover_sql .= ",";
					//$billcover_sql .= "('".$sbill_nr."', ".$objhcare->getID().", ".$objhcare->getAccCoverage().", ".$objhcare->getMedCoverage().", ".
					//									"            ".$objhcare->getSupCoverage().", ".$objhcare->getSrvCoverage().", ".$objhcare->getOpsCoverage().", ".
					//									"            ".$objhcare->getD1Coverage().", ".$objhcare->getD2Coverage().", ".$objhcare->getD3Coverage().", ".
					//
				//edited by jasper 09/12/2013 - FOR BUG#120					"            ".$objhcare->getD4Coverage().", ".$objhcare->getMscCoverage().")";
                    $billcover_sql .= "('".$sbill_nr."', ".$objhcare->getID().", ".$acc_confine_coverage.", ".$ms_confine_coverage.", ".
                                                        "            ".$objhcare->getSupCoverage().", ".$srv_confine_coverage.", ".$ops_confine_coverage.", ".
														"            ".$objhcare->getD1Coverage().", ".$objhcare->getD2Coverage().", ".$objhcare->getD3Coverage().", ".
                                                        "            ".$objhcare->getD4Coverage().", ".$msc_confine_coverage.")";
                                //edited by jasper 09/12/2013 - FOR BUG#120
					if (!$this->isPersonPrincipal($objhcare->getID()))
						$sprincipal_pid = $this->getPrincipalPIDofHCare($s_pid, $objhcare->getID());
					else
						$sprincipal_pid = "";

					if ($i++ > 0) $conftrack_sql .= ",";
					$conftrack_sql .= "('". $s_pid . "', ". strftime("%Y", strtotime($this->bill_dte)) .", '". $sbill_nr ."', ". $objhcare->getID() .", ".
														"  ". $objhcare->getDaysCovered() .", '". $sprincipal_pid ."')";
				}
			}

			// Save the discount applied ...
            //edited by jasper 04/26/2013
            $this->applyClassificationDiscount();
			if (!empty($this->discounts)) {
				$strSQL = "insert into seg_billing_discount (bill_nr, discountid, discount, discount_amnt) " .
									"   values ";
				$i = 0;
                if ($this->isSponsoredMember()) {
                    if ($i > 0) $strSQL .= ",";
                    $strSQL .= "('". $sbill_nr ."', '". 'NBB' ."', 0, ".$this->excess.")";
                } elseif ($this->checkIfPHS()) {
                    if ($i > 0) $strSQL .= ",";
                        $strSQL .= "('". $sbill_nr ."', '". 'Inf' ."', 0, ".$this->excess.")";
                } elseif ($this->isHSM()) {
                    if ($i > 0) $strSQL .= ",";
                        $strSQL .= "('". $sbill_nr ."', '". 'HSM' ."', 0, ".$this->excess.")";
                } else {
				    foreach($this->discounts as $objdiscount) {
				        if ($i++ > 0) $strSQL .= ",";
					        $strSQL .= "('". $sbill_nr ."', '". $objdiscount->getDiscountID() ."', ". $objdiscount->getDiscountRate() .", ".$objdiscount->getDiscountAmount().")";
				    }
                }
                //added by jasper 04/16/2013
				$billdscnt_sql = $strSQL;
			} else {
                $strSQL = "insert into seg_billing_discount (bill_nr, discountid, discount, discount_amnt) " .
                                    "   values ";
                $i = 0;
                if ($this->isSponsoredMember() || $this->checkIfPHS() || $this->isHSM()) {
                    if ($i > 0) $strSQL .= ",";

                    if($this->isSponsoredMember()) {
                    	$discountid = 'NBB';
                    } elseif ($this->isHSM()) {
                    	$discountid = 'HSM';
                    } else {
						$discountid = 'Inf';
                    }

                    $strSQL .= "('". $sbill_nr ."', '". $discountid  ."', 0, ".$this->excess.")";
                    $billdscnt_sql = $strSQL;
                }
            }
            $this->debugSQL = $billdscnt_sql;

			$dscnt_sql = "insert into seg_billingcomputed_discount (bill_nr, total_acc_discount, total_med_discount, total_sup_discount, ".
									 "                                  total_srv_discount, total_ops_discount, total_d1_discount, total_d2_discount, ".
									 "                                  total_d3_discount, total_d4_discount, total_msc_discount) " .
									 "   values ('".$sbill_nr."', ".$this->getBillAreaDiscount('AC').", ".$this->getBillAreaDiscount('MS', 'M').", ".
									 "            ".$this->getBillAreaDiscount('MS', 'S').", ".$this->getBillAreaDiscount('HS').", ".$this->getBillAreaDiscount('OR').", ".
									 "            ".$this->getBillAreaDiscount('D1').", ".$this->getBillAreaDiscount('D2').", ".$this->getBillAreaDiscount('D3').", ".
									 "            ".$this->getBillAreaDiscount('D4').", ".$this->getBillAreaDiscount('XC').")";

			$role_areas = array('D1', 'D2', 'D3', 'D4');
			$strSQL = "insert into seg_billing_pf (bill_nr, hcare_id, dr_nr, role_area, dr_charge, dr_claim) ".
								"   values ";
			$j = 0;
			$bhasPF = false;
			foreach($role_areas as $area) {
//				$ndays = 0;
//				$nrvu  = 0;
//				$area_pf = 0;
//				$this->getTotalPFParams($ndays, $nrvu, $area_pf, $area, 0, true);
//				$this->getPerDrPFandClaims($area_pf, $this->pfs_confine_coverage[$area], $area);        // Get the listing of doctors with corresponding claims.
				$this->getPerDrPFandClaims($area);
//				$this->getPerDrClaimPerHCare($area_pf);                                // Get the listing of doctors with corresponding claims per health insurance.
				// Save the professional fees of doctors per health insurance ...
//				if (!empty($this->pf_claims_per_hcare)) {
				if (!empty($this->pf_claims)) {
					if (!$bhasPF) $bhasPF = true;
					if ($j++ > 0) $strSQL .= ",";
					$i = 0;
					foreach($this->pf_claims as $objpf) {
						if ($i++ > 0) $strSQL .= ",";
						$strSQL .= "('". $sbill_nr ."', ". $objpf->getID() .", ". $objpf->getDrNr() .", '$area', ".
											 "  ". $objpf->getDrCharge() .", ". $objpf->getDrClaim() .")";
					}
				}
			}
			if ($bhasPF) $pf_sql = $strSQL;

//			$db->BeginTrans();
			$db->StartTrans();

			// Delete first the confinement tracker table ...
			if ($this->old_bill_nr != '') {
				$strSQL = "delete from seg_confinement_tracker where bill_nr = '". $sbill_nr ."'";
				$bSuccess = $db->Execute($strSQL);
			}

			if ($bSuccess) {
				if ($this->old_bill_nr != '') {
					$strSQL = "delete from seg_billing_coverage where bill_nr = '". $sbill_nr ."'";
					$bSuccess = $db->Execute($strSQL);

					if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot delete existing seg_billing_coverage of encounter ".$this->current_enr.".";
				}
			}
			else
				$this->errmsg = $db->ErrorMsg().".\nERROR: Cannot delete tracking of confinement in seg_confinement_tracker for encounter ".$this->current_enr.".";

			if ($bSuccess)
				if ($this->old_bill_nr != '') {
					$strSQL = "delete from seg_billing_discount where bill_nr = '". $sbill_nr ."'";
					$bSuccess = $db->Execute($strSQL);

					if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot delete existing billing discount of encounter ".$this->current_enr.".";
				}

			if ($bSuccess)
				if ($this->old_bill_nr != '') {
					$strSQL = "delete from seg_billingcomputed_discount where bill_nr = '$sbill_nr'";
					$bSuccess = $db->Execute($strSQL);

					if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot delete existing computed discount in bill!";
				}

			if ($bSuccess)
				if ($this->old_bill_nr != '') {
					$strSQL = "delete from seg_billing_pf where bill_nr = '". $sbill_nr ."'";
					$bSuccess = $db->Execute($strSQL);

					if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot delete existing billing pf of encounter ".$this->current_enr.".";
				}

			if ($this->old_bill_nr != '') {
				// i.e. edit previously saved billing.
				$strSQL = "update seg_billing_encounter set " .
							"   bill_dte           = '". $this->bill_dte ."', " .
							"   bill_frmdte        = '". $this->bill_frmdte ."', " .
							"   accommodation_type =  ". $this->accomm_typ_nr . ", ".
							"   total_acc_charge   =  ". $nTotalAccChrg .", " .
							"   total_med_charge   =  ". $nTotalMedChrg .", " .
							"	  total_sup_charge   =  ". $nTotalSupChrg .", " .
							"   total_srv_charge   =  ". $nTotalSrvChrg .", " .
							"   total_ops_charge   =  ". $nTotalOpsChrg  .", " .
							"   total_doc_charge   =  ". $total_df .", " .
							"   total_msc_charge   =  ". $nTotalMscChrg .", " .
							"   total_prevpayments =  ". $this->total_prevpayment .", " .
							"   applied_hrs_cutoff =  ". $this->cutoff_hrs .", ".
							"   is_final           =  ". ($this->bfinal ? 1 : 0) ." ".
							"   where bill_nr      = '". $this->old_bill_nr ."'";
				$bSuccess = $db->Execute($strSQL);
				if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot update billing for encounter ".$this->current_enr."."."\n".$strSQL;
			}

			if ($bSuccess) {
				if ($billcover_sql != "") $bSuccess = $db->Execute($billcover_sql);
				if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot insert billing coverge or confinement tracker of encounter ".$this->current_enr.".\n".$billcover_sql;

				if ($bSuccess) {
					if ($conftrack_sql != "") $bSuccess = $db->Execute($conftrack_sql);
					if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot insert billing coverge or confinement tracker of encounter ".$this->current_enr.".\n".$conftrack_sql;
				}
			}

			if ($bSuccess) {
				// Update the reference no. in seg_applied_coverage ...
				$strSQL = "update seg_applied_coverage set
								ref_no = '$sbill_nr'
								where ref_no = 'T".$this->current_enr."'";
				$bSuccess = $db->Execute($strSQL);
				if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot update applied coverage for meds, x-ray, lab or others for encounter ".$this->current_enr."."."\n".$strSQL;
			}

			if ($bSuccess && $this->is_coveredbypkg) {
				$strSQL = "update seg_billing_pkg set
								ref_no = $sbill_nr
								where ref_no = 'T".$this->current_enr."'";
				$bSuccess = $db->Execute($strSQL);
				if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot update package association for encounter ".$this->current_enr."."."\n".$strSQL;

				if ($bSuccess) {
					// Update the reference no. in seg_applied_pkgcoverage ...
					$strSQL = "update seg_applied_pkgcoverage set
									ref_no = $sbill_nr
									where ref_no = 'T".$this->current_enr."'";
					$bSuccess = $db->Execute($strSQL);
					if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot update distributed coverage of package for encounter ".$this->current_enr."."."\n".$strSQL;
				}
			}

			if ($bSuccess) {
				// Update the reference no. in seg_applied_deposit ...
//				$strSQL = "update seg_applied_deposit set
//								ref_no = $sbill_nr
//								where ref_no = 'T".$this->current_enr."'";
//				$bSuccess = $db->Execute($strSQL);
//				if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot update applied distribution of deposit for encounter ".$this->current_enr."."."\n".$strSQL;
			}

			if ($bSuccess) {
				// Update the reference no. in seg_billingcoverage_adjustment ...
				$strSQL = "update seg_billingcoverage_adjustment set
								ref_no = $sbill_nr
								where ref_no = 'T".$this->current_enr."'";
				$bSuccess = $db->Execute($strSQL);
				if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot update adjustment of coverage for encounter ".$this->current_enr."."."\n".$strSQL;
			}

			if ($bSuccess) {
				// Save the discount applied ...
				if ($billdscnt_sql != "") $bSuccess = $db->Execute($billdscnt_sql);
				if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot insert billing discounts of encounter ".$this->current_enr.".";
			}

			// Save the computed discount ... particularly for Charity patients.
			if ($bSuccess) {
				if ($dscnt_sql != "") $bSuccess = $db->Execute($dscnt_sql);
				if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot insert computed discount in bill!";
			}

			if ($bSuccess) {
				if ($pf_sql != "") $bSuccess = $db->Execute($pf_sql);
				if (!$bSuccess) $this->errmsg = $db->ErrorMsg().".\nERROR: Cannot insert billing pf of encounter ".$this->current_enr.".\n$pf_sql";
			}

            //added by jasper 04/03/2013 from HISCGH
            if ($bSuccess) {
                //$db->Execute("CALL sp_compute_billing_breakdown('$sbill_nr')");
                $this->old_bill_nr = $sbill_nr;        // Take note of the new bill no.
            }
            //added by jasper 04/03/2013 from HISCGH


			if (!$bSuccess) $db->FailTrans();
			$db->CompleteTrans();
		}

		if (!$bSuccess && ($this->old_bill_nr == '')) {
			$strSQL = "delete from seg_billing_encounter where bill_nr = '$sbill_nr'";
			$db->Execute($strSQL);
		}

		return($bSuccess);
	}

	//added by jasper 09/03/2013 -FOR BUG#302
    function getEncounterType() {
        $objEnc = new Encounter();
        
        $result = $objEnc->getEncounterInfo($this->current_enr);
        
        return $result;
    }
    //added by jasper 09/03/2013 -FOR BUG#302
    
    //added by jasper 04/25/2013
    function checkIfPHS() {
        $objEnc = new Encounter();

        $result = $objEnc->getEncounterInfo($this->current_enr);

        return ($result['discountid'] == "PHS");
    }
    //added by jasper 04/25/2013
	
    //added by jasper 05/30/2013 for BUG# 279 
    function getOBAnnexPayment() {
        global $db;
        
        $this->ob_payments = array();
        $total_payment = 0;
        $strSQL = "SELECT sp.or_no, sp.or_date, spr.amount_due AS ob_amt FROM seg_pay AS sp " .
                  "INNER JOIN seg_pay_request AS spr ON sp.or_no = spr.or_no  " .
                  "WHERE sp.encounter_nr = '" . $this->current_enr . "' " .
                  "AND sp.cancel_date is null AND spr.service_code = 'OBANNEX'";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()) {
                    
                    $objpay = new Payment;

                    $objpay->setORNo($row['or_no']);
                    $objpay->setORDate($row['or_date']);
                    $objpay->setAmountPaid($row['ob_amt']);

                    $this->ob_payments[] = $objpay;

                    $total_payment += $row['ob_amt']; 
                }
            }
        }
	$this->total_ob_payments = $total_payment;
        return $total_payment;
    }
    //added by jasper 06/06/2013

    //added by jasper 07/17/2013
    //FIX FOR SENIOR CITIZEN DISCOUNT IN PAYWARD
    function iswithSCDiscount() {
        global $db;

        $strSQL = "SELECT COUNT(*) FROM seg_billingapplied_discount ".
              "   WHERE encounter_nr = '" . $this->current_enr . "' AND (STR_TO_DATE(entry_dte, '%Y-%m-%d %H:%i:%s') >= '" . $this->bill_frmdte . "' ".
              "      AND STR_TO_DATE(entry_dte, '%Y-%m-%d %H:%i:%s') <= '" . $this->bill_dte . "') " .
              "      AND discountdesc LIKE '%" . SENIORCITIZEN . "%' ";

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                return true;
            } else {
                return false;
            }
        }
     //added by jasper 04/25/2013 - FIX FOR BUG#120   
    }

    //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY
    function isWellBaby() {
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

        return ($enc_type == WELLBABY);
    }
    //added by jasper 07/31/2013 FOR BUGZILLA #188 - WELLBABY
    
    //added by jasper 07/24/2013 FOR BUGZILLA ID 302
    function findOPcodeNormalDelivery($op_code) {
	global $db;
    
	$strSQL = "SELECT COUNT(ops_code) AS cnt FROM seg_ops_normaldelivery WHERE ops_code = '" . $op_code . "'";
	if ($result = $db->Execute($strSQL)) {
	    if ($result->RecordCount()) {
		$row = $result->FetchRow();
		if ($row['cnt'] == 1) {
		    return true;
		} else {
		    return false;
		}
		//while ($row = $result->FetchRow()) {
		//    if ($op_code == $row['ops_code']) {
		//	return true;
		//	break;
		//    }
		//}
	    } else {
		return false;
	    }
	}
    }
    //added by jasper 07/24/2013 FOR BUGZILLA ID 302

    //added by pol
function FinalBillChecker() {
    	global $db;
        
    	$strSQL = "SELECT ".
    			  " bill_nr ". 
				  " FROM seg_billing_encounter" .
				  " WHERE encounter_nr = '" . $this->current_enr . "'" .
				  " AND is_final = '1' ".
				  " AND ISNULL(is_deleted) ".
                  "     LIMIT 1";
		$Fbill = false;
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$row = $result->FetchRow();
				$Fbill = ($row['bill_nr'] != 0);
			}
		}

		return $Fbill;
    }
//ended by pol

//added by pol 08/05/2013
     function GetPreviousPackage($encnr,$pid) {
        global $db;

           $SQLstr = ("SELECT package_name,
						cp.`name_first`,
						cp.`name_middle`,
						cp.`name_last`,
						package_price,
						ce.`encounter_nr`,
						DATE_FORMAT(ce.`encounter_date`,'%M %e %Y %r') AS DateAdmitted,
						DATE_FORMAT(ce.`mgh_setdte`,'%M %e %Y %r') AS DateDischarged,
						DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d %T'),DATE_FORMAT(ce.`discharge_date`,'%Y-%m-%d %T')) AS daydifferent
						FROM care_encounter `ce`
						INNER JOIN seg_billing_encounter `sbe`
						ON ce.`encounter_nr` = sbe.`encounter_nr`
						INNER JOIN seg_billing_pkg `sbp`
						ON sbe.`bill_nr` = sbp.`ref_no`
						INNER JOIN seg_hcare_packages `shp`
						ON sbp.`package_id` = shp.`package_id`
						INNER JOIN seg_packages `sp`
						ON sbp.`package_id` = sp.`package_id`
						INNER JOIN care_person `cp`
						ON cp.`pid` = ce.`pid`
						WHERE ce.`pid` ='".$pid."'
						AND ce.`encounter_nr` !='".$encnr."'
						AND DATEDIFF(DATE_FORMAT(NOW(),'%Y-%m-%d %T'),DATE_FORMAT(ce.`discharge_date`,'%Y-%m-%d %T')) <= '90'");     
       
      
        if ($result = $db->Execute($SQLstr)) {
            if ($result->RecordCount()) {
                return $result;
            } else {
                return false;
            }
        }
    }
    //end by pol

   	/**
	* Created by Jarel
	* Created on 10/18/2013
   	* Used to Fetch death room rate according its room type
   	* @param string warddesc
   	* @return string rate
   	*/
	function getdeathroomrate($warddesc){
		global $db;

		if (!(strpos(strtoupper($warddesc), SERVICEWARD, 0) === false) && (strpos(strtoupper($warddesc), ICUWARD, 0) === false)) {
	        $strSQL = "SELECT service_rate AS room_rate FROM seg_death_room_rate";
	    }
	    else if (!(strpos(strtoupper($warddesc), ANNEXWARD, 0) === false)) {
	        $strSQL = "SELECT annex_rate  AS room_rate FROM seg_death_room_rate";
	    }

		if($result = $db->Execute($strSQL)){
			if ($result->RecordCount()) {
				if ($row = $result->FetchRow()) {
					$rate = $row['room_rate'];
				}
			}
		}

		return $rate;
	}


}