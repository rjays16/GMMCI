<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/billing/class_billing.php');
require_once($root_path.'include/care_api_classes/billing/class_billareas.php');
require_once($root_path.'include/care_api_classes/billing/class_bill_info.php');
require_once($root_path.'include/care_api_classes/dialysis/class_dialysis.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
//require_once($root_path.'include/care_api_classes/eclaims/accreditation/class_doctor_accreditation.php');
include_once($root_path.'include/care_api_classes/class_personell.php');
include_once($root_path.'include/care_api_classes/class_department.php');

require_once($root_path.'modules/billing/ajax/billing.common.php');
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');

define('AC_AREA', 1);            // Accommodation
define('HS_AREA', 2);            // Hospital services
define('MD_AREA', 3);            // Medicines
define('SP_AREA', 4);            // Supplies
define('PF_AREA', 5);            // Professional Fees (Doctors' Fees)
define('OP_AREA', 6);            // Operation (Procedures)
define('XC_AREA', 7);            // Miscellaneous charges
define('PP_AREA', 8);            // Previous payments
define('DS_AREA', 9);            // Discounts
define('SPONSORED', 'SPONSORED'); //added by jasper 03/11/2013
define('SERVICEWARD', 'SERVICE'); //added by jasper 07/12/2013 FIX FOR MS-728
define('ANNEXWARD', 'ANNEX'); //added by jasper 07/12/2013 FIX FOR MS-728

function clearBilling() {
		$objResponse = new xajaxResponse();
		$formatted_bill_dte = strftime("%b %d, %Y %I:%M%p");
		$objResponse->call("clearBillingHeaderInfo", date("Y-m-d H:i:s"), $formatted_bill_dte);
		return $objResponse;
}

function showBilling($sbill_nr) {
		$objResponse = new xajaxResponse();

		$objbillinfo = new BillInfo();
		$result = $objbillinfo->getBillingHeaderInfo($sbill_nr);
		if ($result) {
				if ($row = $result->FetchRow()) {
						$spatient_name = $objbillinfo->concatname($row["name_last"], $row["name_first"], $row["name_middle"]);

						$addr = $row["street_name"];
						if ($row["brgy_name"])
								$addr.=", ".$row["brgy_name"];
						if ($row["mun_name"])
								$addr.=", ".$row["mun_name"];
						if ($row["prov_name"])
								$addr.=", ".$row["prov_name"];
						if ($row["zipcode"])
								$addr.=" ".$row["zipcode"];

						$admission_dt       = strftime("%b %d, %Y %I:%M%p", strtotime($row["admission_date"]));
						$formatted_bill_dte = strftime("%b %d, %Y %I:%M%p", strtotime($row["bill_dte"]));

                        if(strcmp($row["deathdate"], "0000-00-00 00:00:00") != 0){
                            $formatted_ddte = strftime("%b %d, %Y %I:%M%p", strtotime($row["deathdate"]));
                            $ddate = $row["deathdate"];
                        }else{
                            $formatted_ddte = '';
                            $ddate = '';
                        }
                        $objResponse->call("assignBillingHeaderInfo", $row["bill_nr"], $row["encounter_nr"], $row["pid"], $row["bill_dte"], $row["bill_frmdte"], $formatted_bill_dte, $admission_dt, $spatient_name, $addr, $formatted_ddte, $ddate);
                        //$objResponse->alert( $row["encounter_nr"] . " " . $row["bill_nr"]);
				}
		}

		return $objResponse;
}

function deleteBilling($sbill_nr, $enc_nr) {
		$objResponse = new xajaxResponse();
		$objbill = new BillInfo();
        //$objResponse->alert($objbill->deleteBillInfo($sbill_nr, $enc_nr));
		if ($objbill->deleteBillInfo($sbill_nr, $enc_nr)) {
					$sWarning = 'Billing successfully deleted!';
			clearBilling();
			//$objResponse->call("closeSaveIndicator"); //removed by jasper 04/04/2013
            //added by jasper 04/04/2013
            $objResponse->call("js_NewBilling");
		}
		else
				$sWarning = 'ERROR: '.$objbill->getErrorMsg();

		$objResponse->alert($sWarning);
		return $objResponse;
}

function delPostedItemsForDialysisPkg($enc_nr) {
	global $db;

	$objResponse = new xajaxResponse();

	$db->StartTrans();
	$db->LogSQL();
	$strSQL = "DELETE FROM seg_more_phorder WHERE encounter_nr = '$enc_nr'";
	if ($db->Execute($strSQL)) {
        # Fix for MS-535 by Bong
		$strSQL = "DELETE FROM seg_misc_service WHERE encounter_nr = '$enc_nr' AND !is_cash";
		if ($db->Execute($strSQL)) {
			$strSQL = "DELETE FROM seg_misc_chrg WHERE encounter_nr = '$enc_nr'";
			$bSuccess = $db->Execute($strSQL);
		}
	}
	$db->LogSQL(false);
	if (!$bSuccess) $db->FailTrans();
	$db->CompleteTrans();

	return $objResponse;
}

function showBillingStatus(&$objResponse, &$objBilling) {
		$bStatus = $objBilling->isForFinalBilling();
		$objResponse->call("showBillingStatus", ($bStatus ? 1 : 0));
}

function clearSessionVars() {
		$objResponse = new xajaxResponse();
		unset($_SESSION['isDialysis']);
		unset($_SESSION['billobject']);
		unset($_SESSION['billobject']['main']);
		unset($_SESSION['billobject']['ac']);
		unset($_SESSION['billobject']['md']);
		unset($_SESSION['billobject']['hs']);
		unset($_SESSION['billobject']['op']);
		unset($_SESSION['billobject']['pf']);
		unset($_SESSION['billobject']['xc']);
		unset($_SESSION['ac_done']);
		unset($_SESSION['md_done']);
		unset($_SESSION['hs_done']);
		unset($_SESSION['op_done']);
		unset($_SESSION['pf_done']);
		unset($_SESSION['xc_done']);
		unset($_SESSION['computeflag']);
		return $objResponse;
}

function mainBilling($encounter_nr, $bill_date = "0000-00-00 00:00:00", $frmdte = "0000-00-00 00:00:00", $old_billnr = '', $deathdate=''){
		$objResponse = new xajaxResponse();

        $objBilling = new Billing($encounter_nr, $bill_date, $frmdte, $old_billnr, $deathdate);

		$bDialysis = $objBilling->isDialysisPatient();

        $objBilling->setCurrentEncounterNr($encounter_nr);

        setHasBloodTrans($objResponse,$encounter_nr);

        //added by pol 10/09/2013
        if ($objBilling->FinalBillChecker()) {
            $objResponse->call("FinalBill",1);
        }else{
            $objResponse->call("FinalBill",0);
        }
        //ended by pol 10/09/2013
//    $objResponse->alert("Encounter No. = ".$encounter_nr."; Bill Date = ".$bill_date."; From Date = ".$objBilling->bill_frmdte."; Current Bill No. = ".$old_billnr);

//    $objResponse->script("cClick()");
		$objResponse->script("YAHOO.pbill.container.bBody.show()");

		$objResponse->call("showIPDBillAreas", !$bDialysis);

		$objResponse->call("assignFromDte", $objBilling->bill_frmdte);

		$objResponse->call("assignPkgID", $objBilling->getPackageID());

		$objResponse->call("setCoverageAdjustedFlag", $objBilling->hasCoverageAdjustments());

		showBillingStatus($objResponse,$objBilling);

		getConfineType($objResponse, $objBilling, $encounter_nr, $bill_date);
		getCaseType($objResponse, $encounter_nr, $bill_date);

		if (!$bDialysis) $objBilling->getConfinementType();

		$objResponse->call("assignAdmitDte", ($admitdte = $objBilling->getActualAdmissionDte()) == "0000-00-00 00:00:00" ? NULL : strftime("%b %d, %Y %I:%M%p", strtotime($admitdte)));

		$objResponse->call("assignLastBillDte", ($lastbilldte = $objBilling->getActualLastBillDte()) == "0000-00-00 00:00:00" ? NULL : strftime("%b %d, %Y %I:%M%p", strtotime($lastbilldte)));

        //added by jasper 04/25/2013 and edited by pol 05/21/2013
        //$objResponse->alert($objBilling->checkIfPHS($encounter_nr));
        //$objResponse->alert($objBilling->isMedicoLegal());
        if ($objBilling->isMedicoLegal()) {
           // $objResponse->call("showMedicoLegal");
           // $objResponse->call("showMedicoLegal",1);
            $objResponse->call("showMedicoLegal",1,ShowMedicoCases($encounter_nr));             //edited by pol
          	 
        } else {
            $objResponse->call("showMedicoLegal",0);
        }

        $objResponse->call("GetPhicNo",GetPhicNumber($encounter_nr)); //added by pol
		getClassification($objResponse,$objBilling);

		getMemCategory($objResponse, $objBilling);

		if ($bDialysis) {
			$objResponse->call("setDialysisFlag", 1);
			if (!$objBilling->hasPostedItemsOfPkg()) {
				postMedsorSupplies($encounter_nr, $objBilling->bill_dte, $objBilling->bill_frmdte);
				postPkgXLService($encounter_nr, $objBilling->bill_dte, $objBilling->bill_frmdte);
				postPkgMiscChrgs($encounter_nr, $objBilling->bill_dte, $objBilling->bill_frmdte);
			}
		}

  /*      //added by jasper 04/04/2013
        $objbillinfo = new BillInfo();
        $tot_prevbill_amt = 0;
        $result = $objbillinfo->getPreviousBillAmt($encounter_nr, $old_billnr);
        if ($result) {
            while ($row = $result->FetchRow()) {
                $n_bill = 0;
                if (!empty($row["total_charge"])) $n_bill = $row["total_charge"];
                if (!empty($row["total_coverage"])) $n_bill -= $row["total_coverage"];
                if (!empty($row["total_computed_discount"])) $n_bill -= $row["total_computed_discount"];
                if (!empty($row["total_discount"]) && ($n_bill > 0)) $n_bill -= ($n_bill * $row["total_discount"]);
                $tot_prevbill_amt += $n_bill;
            }
            $objResponse->call("assignPreviousBilledAmount", number_format($tot_prevbill_amt, 2, '.', ','));
        }
        //added by jasper 04/04/2013    */

		$objResponse->call("doneComputing");

		$_SESSION['computeflag'] = false;
		$_SESSION['isDialysis'] = $bDialysis;
		$_SESSION['billobject']['main'] = serialize($objBilling);
		$_SESSION['billobject']['ac']   = serialize(new ACBill());
		$_SESSION['billobject']['md']   = serialize(new MDBill());
		$_SESSION['billobject']['hs']   = serialize(new HSBill());
		$_SESSION['billobject']['op']   = serialize(new OPBill());
		$_SESSION['billobject']['pf']   = serialize(new PFBill());
		$_SESSION['billobject']['xc']   = serialize(new XCBill());

		$_SESSION['ac_done'] = false;
		$_SESSION['md_done'] = false;
		$_SESSION['hs_done'] = false;
		$_SESSION['op_done'] = false;
		$_SESSION['pf_done'] = false;
		$_SESSION['xc_done'] = false;

		$objResponse->call("togglePkgControls", "0");

		if (!$bDialysis) $objResponse->call("toggleBillAreaStatus", 'AC', 1, 1);    // fix for HISSPMC-115
//		$objResponse->call("toggleBillAreaStatus", 'HS', 1);                         fix for HISSPMC-115
//		$objResponse->call("toggleBillAreaStatus", 'MD', 1);                         fix for HISSPMC-115
//		if (!$bDialysis) $objResponse->call("toggleBillAreaStatus", 'PF', 1);        fix for HISSPMC-115
//		if (!$bDialysis) $objResponse->call("toggleBillAreaStatus", 'OP', 1);        fix for HISSPMC-115
//		$objResponse->call("toggleBillAreaStatus", 'XC', 1);                         fix for HISSPMC-115

		return $objResponse;
}

function setActivityFlag($area) {
	$objResponse = new xajaxResponse();
	switch ($area) {
		case 'AC':
			$_SESSION['ac_done'] = true;
			break;

		case 'MD':
			$_SESSION['md_done'] = true;
			break;

		case 'HS':
			$_SESSION['hs_done'] = true;
			break;

		case 'OP':
			$_SESSION['op_done'] = true;
			break;

		case 'PF':
			$_SESSION['pf_done'] = true;
			break;

		case 'XC':
			$_SESSION['xc_done'] = true;
			break;
	}
	return $objResponse;
}

function computeAccommodation() {
		$objResponse = new xajaxResponse();

		$bDialysis = $_SESSION['isDialysis'];
		if (!$bDialysis) {      // fix for HISSPMC-115
			$bDone = false;
			$objBilling = unserialize($_SESSION['billobject']['main']);
			showAccommodation($objResponse,$objBilling);
			while (!$bDone) {
				$ac = new ACBill();
				$ac->makeObject($objBilling);
		//		$_SESSION['billobject']['main'] = serialize($objBilling);
				$_SESSION['billobject']['ac']   = serialize($ac);
				$bDone = true;
			}
		}                      // fix for HISSPMC-115

		return $objResponse;
}

function computeXLO() {
		$objResponse = new xajaxResponse();

		$bDone = false;
		$objBilling = unserialize($_SESSION['billobject']['main']);
		$objBilling->setForceCompute($_SESSION['computeflag']);

		//Hospital services Objects
		HSLaboratory(&$objResponse, &$objBilling);

		while (!$bDone) {
			$hs = new HSBill();
			$hs->makeObject($objBilling);
	//		$_SESSION['billobject']['main'] = serialize($objBilling);
			$_SESSION['billobject']['hs']   = serialize($hs);
			$bDone = true;
		}

		return $objResponse;
}

function computeDrugsMeds() {
		$objResponse = new xajaxResponse();

		$bDone = false;
		$objBilling = unserialize($_SESSION['billobject']['main']);
		$objBilling->setForceCompute($_SESSION['computeflag']);

		//Medicines Objects
		Medicines(&$objResponse, &$objBilling);                    // 3

		while (!$bDone) {
			$md = new MDBill();
			$md->makeObject($objBilling);
	//		$_SESSION['billobject']['main'] = serialize($objBilling);
			$_SESSION['billobject']['md'] = serialize($md);
			$bDone = true;
		}

		return $objResponse;
}

function computePF() {
		$objResponse = new xajaxResponse();

		$bDialysis = $_SESSION['isDialysis'];
		if (!$bDialysis) {           // fix for HISSPMC-115
			$bDone = false;
			$objBilling = unserialize($_SESSION['billobject']['main']);
			$objBilling->setForceCompute($_SESSION['computeflag']);

			//Doctors fee
			ProfFees($objResponse, $objBilling);                    // 5

			while (!$bDone) {
				$pf = new PFBill();
				$pf->makeObject($objBilling);
		//		$_SESSION['billobject']['main'] = serialize($objBilling);
				$_SESSION['billobject']['pf']   = serialize($pf);
				$bDone = true;
			}
		}                           // fix for HISSPMC-115

//		$_SESSION['pf_object'] = serialize($objBilling);
//		if (!$bDialysis) $objResponse->call("toggleBillAreaStatus", 'OP', 1);

		return $objResponse;
}

function computeOP() {
		$objResponse = new xajaxResponse();

		$bDialysis = $_SESSION['isDialysis'];
		if (!$bDialysis) {                  // fix for HISSPMC-115
			$bDone = false;
//			$objBilling->setForceCompute($_SESSION['computeflag']);
			$objBilling = unserialize($_SESSION['billobject']['main']);

			// Operation (procedures) ...                            // 6
			getBilledOps($objResponse, $objBilling);

			while (!$bDone) {
				$op = new OPBill();
				$op->makeObject($objBilling);
		//		$_SESSION['billobject']['main'] = serialize($objBilling);
				$_SESSION['billobject']['op']   = serialize($op);
				$bDone = true;
			}
		}                                  // fix for HISSPMC-115
//		$_SESSION['op_object'] = serialize($objBilling);
//		$objResponse->call("toggleBillAreaStatus", 'XC', 1);

		return $objResponse;
}

function computeMisc() {
		$objResponse = new xajaxResponse();
		$bDone = false;
		$objBilling = unserialize($_SESSION['billobject']['main']);

		// Miscellaneous charges ...
		getMiscellaneous($objResponse, $objBilling);            // 7

		while (!$bDone) {
			$xc = new XCBill();
			$xc->makeObject($objBilling);
	//		$_SESSION['billobject']['main'] = serialize($objBilling);
			$_SESSION['billobject']['xc']   = serialize($xc);
			$bDone = true;
		}

//		$_SESSION['xc_object'] = serialize($objBilling);
//		$objResponse->call("toggleLastPartStatus", 1);

		return $objResponse;
}

function computeLastPart() {
		$objResponse = new xajaxResponse();

		if (!$_SESSION['ac_done'])  {
			$bDialysis = $_SESSION['isDialysis'];
			if (!$bDialysis) $objResponse->call("toggleBillAreaStatus", 'AC', 1);
//			$objResponse->call("setDoneStatus", "AC");
			return $objResponse;
		}

		if (!$_SESSION['md_done']) {
//			$objResponse->call("setDoneStatus", "MD");
			$objResponse->call("toggleBillAreaStatus", 'MD', 1);
			return $objResponse;
		}

		if (!$_SESSION['hs_done']) {
			$objResponse->call("toggleBillAreaStatus", 'HS', 1);
//			$objResponse->call("setDoneStatus", "HS");
			return $objResponse;
		}

		if (!$_SESSION['op_done']) {
			$bDialysis = $_SESSION['isDialysis'];
			if (!$bDialysis) $objResponse->call("toggleBillAreaStatus", 'OP', 1);
//			$objResponse->call("setDoneStatus", "OP");
			return $objResponse;
		}

		if (!$_SESSION['pf_done']) {
			$bDialysis = $_SESSION['isDialysis'];
			if (!$bDialysis) $objResponse->call("toggleBillAreaStatus", 'PF', 1);
//			$objResponse->call("setDoneStatus", "PF");
			return $objResponse;
		}

		if (!$_SESSION['xc_done']) {
			$objResponse->call("toggleBillAreaStatus", 'XC', 1);
//			$objResponse->call("setDoneStatus", "XC");
			return $objResponse;
		}

		$objBilling = unserialize($_SESSION['billobject']['main']);

		if (is_object($objBilling)) {
			$ac_obj = unserialize($_SESSION['billobject']['ac']);
			$md_obj = unserialize($_SESSION['billobject']['md']);
			$hs_obj = unserialize($_SESSION['billobject']['hs']);
			$op_obj = unserialize($_SESSION['billobject']['op']);
			$pf_obj = unserialize($_SESSION['billobject']['pf']);
			$xc_obj = unserialize($_SESSION['billobject']['xc']);

			if (is_object($ac_obj))
				$ac_obj->assignBillObject($objBilling);
			if (is_object($md_obj))
				$md_obj->assignBillObject($objBilling);
			if (is_object($hs_obj))
				$hs_obj->assignBillObject($objBilling);
			if (is_object($op_obj))
				$op_obj->assignBillObject($objBilling);
			if (is_object($pf_obj))
				$pf_obj->assignBillObject($objBilling);
			if (is_object($xc_obj))
				$xc_obj->assignBillObject($objBilling);

//			$objResponse->alert("AC obj = ".print_r($ac_obj, true)." time = ".time());
//			$objResponse->alert("MD obj = ".print_r($md_obj, true)." time = ".time());
//			$objResponse->alert("HS obj = ".print_r($hs_obj, true)." time = ".time());
//			$objResponse->alert("OP obj = ".print_r($op_obj, true)." time = ".time());
//			$objResponse->alert("PF obj = ".print_r($pf_obj, true)." time = ".time());
//			$objResponse->alert("XC obj = ".print_r($xc_obj, true)." time = ".time());

			//deposits or previous payment
			getPreviousPayment($objResponse, $objBilling);            // 8

			//discounts
            //edited by jasper 04/16/2013
            //getDiscount($objResponse, $objBilling);
			$totalDiscount = getDiscount($objResponse, $objBilling);                    // 9
           	//		$objResponse->call("togglePkgControls", "0");

			// ... compute total amount billed.
            //edited by jasper 04/16/2013
            //getTotalBillAmnt($objResponse, $objBilling);
			getTotalBillAmnt($objResponse, $objBilling, $totalDiscount);

			$_SESSION['billobject']['main'] = serialize($objBilling);
//			$objResponse->call("keepBillObject", json_encode($objBilling));
		}
		$objResponse->call("doneComputing", 1);
		$_SESSION['computeflag'] = false;
		return $objResponse;
}

function doLastPartComputation() {
		$objResponse = new xajaxResponse();
		$objResponse->call("toggleLastPartStatus", 1);
		return $objResponse;
}

//function recomputeBilling(&$objResponse, &$objBilling, $area_index = 0) {
//		if ($area_index != AC_AREA) {
//				$objBilling->getAccommodationHist();                 // get AccommodationHist
//				$objBilling->getRoomTypeBenefits();                 // get Room type Benefits
//				$objBilling->getConfineBenefits('AC');
//		}

//		if ($area_index != HS_AREA) {
//				$objBilling->getServicesList();
//				$objBilling->getServiceBenefits();
//				$objBilling->getConfineBenefits('HS');
//		}

//		if ($area_index != MD_AREA) {
//				$objBilling->getMedicinesList();                     //gather all medicines consumed
//				$objBilling->getMedicineBenefits();
//				$objBilling->getConfineBenefits('MS', 'M');
//		}

//    #if ($area_index != SP_AREA) {
//    #    $objBilling->getSuppliesList();                     // gather all supplies consumed
//    #    $objBilling->getSupplyBenefits();
//    #    $objBilling->getConfineBenefits('MS', 'S');
//    #}

//		if ($area_index != PF_AREA) {
//				$objBilling->getProfFeesList();
//				$objBilling->getProfFeesBenefits();

//				$hsp_pfs_benefits = $objBilling->hsp_pfs_benefits; //role area
//				if (is_array($hsp_pfs_benefits) && (count($hsp_pfs_benefits) > 0)) {
//						foreach($hsp_pfs_benefits as $key=> $value)
//								$objBilling->getConfineBenefits($value->role_area, '', $value->getRoleLevel());
//				}
//		}

//		if ($area_index != OP_AREA) {
//				$objBilling->getOpsList();
//				$objBilling->getOpBenefits();
//				$objBilling->getConfineBenefits('OR');
//		}

//		if ($area_index != XC_AREA) {
//				$objBilling->getMiscellaneousChrgsList();
//				$objBilling->getMiscellaneousBenefits();
//				$objBilling->getConfineBenefits('XC');
//		}

//		if ($area_index != PP_AREA) $objBilling->getPreviousPayments();
//    #if ($area_index != DS_AREA) $objBilling->getTotalDiscount();

//		getDiscount($objResponse, $objBilling);
//}

function getRoomTypeAttachedInfo($type_nr, $src, $accHistArray, &$typeDesc, &$sRooms) {
		$sDesc  = '';
		$sRooms = '';
		if (!empty($accHistArray) && is_array($accHistArray)) {
				foreach ($accHistArray as $key => $accHist) {
						if (($accHist->type_nr == $type_nr) && ($accHist->getSource() == $src)) {
								if ($sDesc == '') $sDesc = $accHist->getTypeDesc();
								$pos = strpos($sRooms, $accHist->getRoomNr());
								if ($pos === false) {
										if ($sRooms != '') $sRooms .= ', ';
										$sRooms .= $accHist->getRoomNr();
								}
						}
				}
		}
		$typeDesc = $sDesc;
}

function getClassification(&$objResponse, &$objBilling) {
		$s_classdesc = $objBilling->getClassificationDesc();
//    $objResponse->alert($s_classdesc);
		$objResponse->call("showClassification", strtoupper($s_classdesc));
}

function getMemCategory(&$objResponse, &$objBilling) {
		$sDesc = $objBilling->getMemCategoryDesc();
		$objResponse->call('assignMemCategDesc', $sDesc);
}

//edited by jasper 07/12/2013 - TO ACCOMMODATE NEW ROOM RATES BASING ON CASE TYPE FROM HOSPITAL ADMINISTRATIVE ORDER NO, 18 s.2013
function getRoomRate($ward_nr, $rm_nr, $casetypeid = '', $warddesc = '') {
		global $db;

		$objResponse = new xajaxResponse();
        $objBilling = unserialize($_SESSION['billobject']['main']);
		$rm_rate = 0;
        $strSQLdefault = "select ctr.room_rate ".
							"   from care_type_room as ctr ".
							"      inner join care_room as cr on ctr.nr = cr.type_nr ".
							"   where cr.nr = ".$rm_nr." and ward_nr = ".$ward_nr;

        if ($objBilling->isPHIC()) {
            if (!(strpos(strtoupper($warddesc), SERVICEWARD, 0) === false)) {
                $strSQL = "SELECT service_ward_roomrate AS room_rate FROM seg_confinementtype_room_rate WHERE confinetype_id = " . $casetypeid;
            }
            else if (!(strpos(strtoupper($warddesc), ANNEXWARD, 0) === false)) {
                $strSQL = "SELECT annex_roomrate AS room_rate FROM seg_confinementtype_room_rate WHERE confinetype_id = " . $casetypeid;
            } else {
                $strSQL = $strSQLdefault;
            }
        } else {
            $strSQL = $strSQLdefault;
            }

		if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
						$row = $result->FetchRow();
						$rm_rate = $row['room_rate'];
				}
		}

//    $objResponse->alert("Room Rate: ".$rm_rate);

		$objResponse->call('assignRmRate', number_format($rm_rate, 2, '.', ','));

		return $objResponse;
}

//added by jasper 07/12/2013 - TO ACCOMMODATE NEW ROOM RATES BASED ON CASE TYPE FROM HOSPITAL ADMINISTRATIVE ORDER NO, 18 s.2013
function getRoomRateByCaseType($casetypeid = '', $warddesc = '') {
    global $db;

    $strSQL = "";
    if (!(strpos(strtoupper($warddesc), SERVICEWARD, 0) === false)) {
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

function getRoomRateDefault($type_no) {
    global $db;

    $strSQL = "SELECT room_rate FROM care_type_room WHERE nr = " . $type_no;

    if ($result = $db->Execute($strSQL)) {
        if ($result->RecordCount()) {
            $row = $result->FetchRow();
            $rm_rate = $row['room_rate'];
        }
    }

    return $rm_rate;
}
//added by jasper 07/12/2013 - TO ACCOMMODATE NEW ROOM RATES BASED ON CASE TYPE FROM HOSPITAL ADMINISTRATIVE ORDER NO, 18 s.2013

function postERDefaultCharges($bill_dt, $enc_nr) {
		global $db;

		$s_errmsg = '';
		$strSQL = "select * from seg_other_services as sos ".
							"   where is_ER_default <> 0 and ".
							"      not exists (select * ".
							"                     from seg_misc_chrg_details as smcd ".
							"                        inner join seg_misc_chrg as smc on smcd.refno = smc.refno ".
							"                     where smcd.service_code = sos.service_code ".
							"                        and smc.encounter_nr = '".$enc_nr."')";

		if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
						while ($row = $result->FetchRow()) {
								$s_errmsg = chargeMiscChrg($bill_dt, $enc_nr, $row["service_code"], $row["account_type"], $row["price"], 1, false);
								if ($s_errmsg != '') break;
						}
				}
		}
		else
				$s_errmsg = $db->ErrorMsg();

		return($s_errmsg);
}

/***
* This routine will post default charges for inpatient.
*
* @param date $bill_dt
* @param string $enc_nr
*/
function postIPDefaultCharges($bill_dt, $enc_nr) {
		global $db;

		$s_errmsg = '';
		$strSQL = "select * from seg_other_services as sos ".
							"   where is_IP_default <> 0 and ".
							"      not exists (select * ".
							"                     from seg_misc_chrg_details as smcd ".
							"                        inner join seg_misc_chrg as smc on smcd.refno = smc.refno ".
							"                     where smcd.service_code = sos.service_code ".
							"                        and smc.encounter_nr = '".$enc_nr."')";

		if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
						while ($row = $result->FetchRow()) {
								$s_errmsg = chargeMiscChrg($bill_dt, $enc_nr, $row["service_code"], $row["account_type"], $row["price"], 1, false);
								if ($s_errmsg != '') break;
						}
				}
		}
		else
				$s_errmsg = $db->ErrorMsg();

		return($s_errmsg);
}

function updateObjBilling($objBilling, $area) {
	$objResponse = new xajaxResponse();
	switch ($area) {
		case 'AC':
			$ac = new ACBill();
			$ac->makeObject($objBilling);
			$_SESSION['billobject']['main'] = serialize($objBilling);
			$_SESSION['billobject']['ac']   = serialize($ac);
			break;

		case 'HS':
			$hs = new HSBill();
			$hs->makeObject($objBilling);
			$_SESSION['billobject']['main'] = serialize($objBilling);
			$_SESSION['billobject']['hs']   = serialize($hs);
			break;

		case 'MD':
			$md = new MDBill();
			$md->makeObject($objBilling);
			$_SESSION['billobject']['main'] = serialize($objBilling);
			$_SESSION['billobject']['md']   = serialize($md);
			break;

		case 'OP':
			$op = new ACBill();
			$op->makeObject($objBilling);
			$_SESSION['billobject']['main'] = serialize($objBilling);
			$_SESSION['billobject']['op']   = serialize($op);
			break;

		case 'PF':
			$pf = new ACBill();
			$pf->makeObject($objBilling);
			$_SESSION['billobject']['main'] = serialize($objBilling);
			$_SESSION['billobject']['pf']   = serialize($pf);
			break;

		case 'XC':
			$xc = new ACBill();
			$xc->makeObject($objBilling);
			$_SESSION['billobject']['main'] = serialize($objBilling);
			$_SESSION['billobject']['xc']   = serialize($xc);
			break;
	}
	return $objResponse;
}

function showAccommodation(&$objResponse, &$objBilling){
		if ($objBilling->isERPatient()) {
				if ($objBilling->old_bill_nr == '') {
						$err_msg = postERDefaultCharges($objBilling->getBillDate(), $objBilling->getCurrentEncounterNr());
						if ($err_msg != '') $objResponse->alert($err_msg);
				}

				$objResponse->call("showAccommodationList", false);
		}
		else {
				$objResponse->call("showAccommodationList", true);

				if ($objBilling->isInPatient()) {
					if ($objBilling->old_bill_nr == '') {
							$err_msg = postIPDefaultCharges($objBilling->getBillDate(), $objBilling->getCurrentEncounterNr());
							if ($err_msg != '') $objResponse->alert($err_msg);
					}
				}

				$objBilling->getAccommodationHist(); // set AccommodationHist
				$objBilling->getRoomTypeBenefits(); // set Room type Benefits
				$objBilling->getConfineBenefits('AC');
                //added by jasper 07/12/2013 FIX FOR MS-728
                $confinement_type = $objBilling->getConfinementType();
                //added by jasper 07/12/2013 FIX FOR MS-728
				$accHistArray= $objBilling->accommodation_hist; //get accommodation object
				$accBenefitsArray = $objBilling->acc_roomtype_benefits; //get accommodation benefits coverage
				$total_confine_coverage = $objBilling->acc_confine_coverage;
				//Display Accommodation arguments
				$total = 0;
				$objResponse->call("jsClearList", 'body_accListDetails');

				if (is_array($accBenefitsArray) && (count($accBenefitsArray) > 0)) {
						foreach ($accBenefitsArray as $key => $accBen) {
								$total_charge = sprintf('%01.2f', $accBen->getActualCharge()); //Actual Price
								$days_count = $accBen->days_count;
								$excess_hr = $accBen->excess_hours;

								$total += $total_charge;

								getRoomTypeAttachedInfo($accBen->type_nr, $accBen->getSource(), $accHistArray, $type_desc, $sRooms);
                                //added by jasper 05/09/2013
                                //FIX FOR MULTIPLE BILLING - not to display if days_count and excess_hr is 0
                                if ($days_count<>0 || $excess_hr<>0) {
								    $objResponse->call("jsAccommodationList", $accBen->type_nr, $sRooms, $type_desc, $days_count, $excess_hr, number_format($accBen->room_rate, 2, '.', ','), number_format($total_charge, 2, '.', ','), $accBen->getSource(), ($objBilling->old_bill_nr == ''));
                                }
						}
				}
				else
						$objResponse->call("jsAccommodationList", NULL, '', '', 0, 0, number_format(0, 2, '.', ','), number_format(0, 2, '.', ','), 'AD', FALSE);

                //added by jasper 05/21/2013 FIX FOR AUTOMATIC EXCESS FOR PHIC PATIENTS
                //BUT NOT FOR PATIENTS WITH SENIOR CITIZEN DISCOUNT AND PAYWARD ACCOMMODATION
                //07/17/2013 - FIX FOR BUG#120
                if (!$objBilling->isCharity() && $objBilling->iswithSCDiscount()) {
                    $ndiscount = $objBilling->getBillAreaDiscount('AC');
                } else if (!$objBilling->isPHIC() && !$objBilling->isMedicoLegal()) {
                    $ndiscount = $objBilling->getBillAreaDiscount('AC');
                } else {
                    $ndiscount = 0.00;
                }
                //added by jasper 07/16/2013 - FIX FOR BUG#120
                if ($objBilling->isPHIC()) {
                    if ($total - $ndiscount <= $total_confine_coverage) {
                        $total_confine_coverage = $total - $ndiscount;
                    }
                }

                $excess = sprintf('%01.2f', ($total - $ndiscount - $total_confine_coverage));
				$objResponse->call("getAccommodation", number_format($total, 2, '.', ','), number_format($ndiscount, 2, '.', ','), number_format($total_confine_coverage, 2, '.', ','), number_format($excess, 2, '.', ','));
		}

//		$ac = new ACBill();
//		$ac->makeObject($objBilling);
//		$_SESSION['billobject']['main'] = serialize($objBilling);
//		$_SESSION['billobject']['ac']   = serialize($ac);

//		$objResponse->call("toggleBillAreaStatus", 'AC', 0);

//		$_SESSION['ac_object'] = serialize($objBilling);

//		$ac = new ACBill();
//		$ac->makeObject($objBilling);
//		$_SESSION["ac_object"] = serialize($ac);

//		$objResponse->call("toggleBillAreaStatus", 'AC', 0);

//		$objResponse->alert('AC Obj in ShowAccommodation = '.print_r(unserialize($_SESSION["ac_object"]), true));

//		$bDialysis = $_SESSION['isDialysis'];
//		if (!$bDialysis) $objResponse->call("toggleBillAreaStatus", 'PF', 1);
}// end of Accommodation

function HSLaboratory(&$objResponse, &$objBilling) {
//		$objBilling->getServicesList();
		$objBilling->getServiceBenefits();

//		if ($_SESSION['sess_temp_userid'] == 'medocs') {
//			$objResponse->alert(print_r($objBilling->getSrvBenefits(), true));
//        }

		$objBilling->getConfineBenefits('HS');
		$hspServicesList = $objBilling->getSrvBenefits();

//    $totalServConfineCoverage = $objBilling->srv_confine_coverage;
        $totalServConfineCoverage = $objBilling->getAppliedHSCoverage();
		//$totalServCharge = $objBilling->getTotalSrvCharge();

		$objResponse->call("jsClearList", "body_hsListDetails");

		$totalCharge = 0;
		if (is_array($hspServicesList) && (count($hspServicesList) > 0)) {
				foreach ($hspServicesList as $key=>$hsValue) {
						$servPrice  = number_format($hsValue->getServPrice(), 2, '.', ',');
						$servCharge = number_format(($hsValue->getServQty() * $hsValue->getServPrice()), 2, '.', ',');
						$totalCharge += ($hsValue->getServQty() * $hsValue->getServPrice());
						$objResponse->call("jsHospitalServices","body_hsListDetails", $objBilling->bill_frmdte, $hsValue->getServiceCode(), $hsValue->getServiceDesc(),
																																		$hsValue->getServQty(), $servPrice, $servCharge , $hsValue->getServProvider(),
																																		$hsValue->getGroupDesc(), ($objBilling->old_bill_nr == ''));
				}
		}
		else {
				$objResponse->call("jsHospitalServices", "body_hsListDetails", 0, NULL, '', 0, 0, 0, '', '', FALSE);
		}

		//if ($_SESSION['sess_temp_userid'] == 'medocs')
		//$objResponse->alert($totalCharge . " : " . $totalServCharge);
		$totalServCharge = $totalCharge;

        //added by jasper 05/21/2013 FIX FOR AUTOMATIC EXCESS FOR PHIC PATIENTS
        //BUT NOT FOR PATIENTS WITH SENIOR CITIZEN DISCOUNT AND PAYWARD ACCOMMODATION 
        //07/17/2013 - FIX FOR BUG#120
        if (!$objBilling->isCharity() && $objBilling->iswithSCDiscount()) {
            //$objResponse->alert($objBilling->debugSQL);
            $ndiscount = $objBilling->getBillAreaDiscount('HS');
        } else if (!$objBilling->isPHIC() && !$objBilling->isMedicoLegal()) { //added by jasper 05/21/2013 FIX FOR AUTOMATIC EXCESS FOR PHIC PATIENTS
            $ndiscount = $objBilling->getBillAreaDiscount('HS');
        } else {
            $ndiscount = 0.00;
        }

        //added by jasper 07/16/2013 - FIX FOR BUG#120
        if ($objBilling->isPHIC()) {
            if ($totalServCharge - $ndiscount <= $totalServConfineCoverage) {
                $totalServConfineCoverage = $totalServCharge - $ndiscount;
            }
        }

        $excess = number_format(($totalServCharge - $ndiscount - $totalServConfineCoverage), 2, '.', ',');
		$objResponse->call("getHospitalServices", number_format($totalServCharge, 2, '.', ','), number_format($ndiscount, 2, '.', ','), number_format($totalServConfineCoverage, 2, '.', ','), $excess );

//		$objResponse->call("toggleBillAreaStatus", 'HS', 0);

//		$objResponse->call("toggleBillAreaStatus", 'MD', 1);
} //end of Laboratory

function getMiscellaneous(&$objResponse, &$objBilling) {
//		$objBilling->getMiscellaneousChrgsList();
		$objBilling->getMiscellaneousBenefits();
		$objBilling->getConfineBenefits('XC');

//    $hspMscList     = $objBilling->msc_chrgs_list;
		$hspMscList = $objBilling->hsp_msc_benefits; //listing

		$totalMscConfineCoverage = $objBilling->msc_confine_coverage;
		$totalMscCharge          = $objBilling->getTotalMscCharge();

		$objResponse->call("jsClearList", "body_mscListDetails");

		if (is_array($hspMscList) && (count($hspMscList) > 0)) {
			foreach ($hspMscList as $key=>$mscValue) {
				$mscCharge  = number_format($mscValue->getMiscChrg(), 2, '.', ',');
						$objResponse->call("jsMiscellaneousList", $objBilling->bill_frmdte, $mscValue->getMiscCode(), $mscValue->getMiscName(), $mscValue->getMiscDesc(),
																																		$mscValue->getMiscQty(), $mscCharge, ($objBilling->old_bill_nr == ''));
			}
		}
		else {
				$objResponse->call("jsMiscellaneousList", 0, NULL, '', '', 0, 0, FALSE);
		}

        //added by jasper 05/21/2013 FIX FOR AUTOMATIC EXCESS FOR PHIC PATIENTS
        //BUT NOT FOR PATIENTS WITH SENIOR CITIZEN DISCOUNT AND PAYWARD ACCOMMODATION 
        //07/17/2013 - FIX FOR BUG#120
        if (!$objBilling->isCharity() && $objBilling->iswithSCDiscount()) {
            $ndiscount = $objBilling->getBillAreaDiscount('XC');
            //$objResponse->alert($objBilling->debugSQL);
        } else if (!$objBilling->isPHIC() && !$objBilling->isMedicoLegal()) { //added by jasper 05/21/2013 FIX FOR AUTOMATIC EXCESS FOR PHIC PATIENTS
            $ndiscount = $objBilling->getBillAreaDiscount('XC');
        } else {
            $ndiscount = 0.00;
        }

        //added by jasper 07/16/2013 - FIX FOR BUG#120
        if ($objBilling->isPHIC()) {
            if ($totalMscCharge - $ndiscount <= $totalMscConfineCoverage) {
                $totalMscConfineCoverage = $totalMscCharge - $ndiscount;
            }
        }

        $excess = number_format(($totalMscCharge - $ndiscount - $totalMscConfineCoverage), 2, '.', ',');
		$objResponse->call("showMiscSummary", number_format($totalMscCharge, 2, '.', ',') , number_format($ndiscount, 2, '.', ','),                                       number_format($totalMscConfineCoverage, 2, '.', ','), $excess );

//		$objResponse->call("toggleBillAreaStatus", 'XC', 0);
}

function getBilledOps(&$objResponse, &$objBilling) {
//		$objBilling->getOpsList();        // Get list of operations applied to patient.
		$objBilling->getOpBenefits();    // Get summary of operations and corresponding insurance coverage.
//		$objBilling->getConfineBenefits('OR');

		$opsBenefitsArray = $objBilling->hsp_ops_benefits;
		$totalOpsCharge   = $objBilling->getTotalOpCharge();
//		$totalOpsCoverage = $objBilling->ops_confine_coverage;
		$objBilling->initOpsConfineCoverage();

		$objResponse->call("jsClearList", "body_opsListDetails");

		if (is_array($opsBenefitsArray) && (count($opsBenefitsArray) > 0)) {
                //$objBilling->getConfineBenefits('OR');
                $i = 0;
				foreach ($opsBenefitsArray as $key=>$value) {
						$n_rvu   = number_format($value->op_rvu, 0, '', ',');
						$n_mult  = number_format($value->op_multiplier, 2, '.', ',');
						$n_total = number_format($value->getOpCharge(), 2, '.', ',');
                        //added by jasper 05/23/2013 FIX FOR BILLS COVERED BY PACKAGE
                        if ($objBilling->is_coveredbypkg) {
                            if ($i==0) {
                                $i++;
                                $objBilling->getConfineBenefits('OR');
                            }
                        } else {
                            $objBilling->getConfineBenefits('OR', $value->getOpCodePerformed());
                        }
                        //added by jasper 05/23/2013 FIX FOR BILLS COVERED BY PACKAGE
						$objResponse->call("jsOpsList", $objBilling->bill_frmdte, $value->op_code, $value->op_desc, $n_rvu, $n_mult, $n_total, $value->getOpProvider(), ($objBilling->old_bill_nr == ''));
				}
		}
		else
				$objResponse->call("jsOpsList", 0, NULL, '', 0, 0, 0, '', False);

//		if ($_SESSION['sess_temp_userid'] == 'medocs')
//			$objResponse->alert("Ops Coverage = ".$objBilling->getActualOpsCoverage(18));
        //$objResponse->alert($objBilling->debugSQL);

        //added by jasper 05/21/2013 FIX FOR AUTOMATIC EXCESS FOR PHIC PATIENTS
        //BUT NOT FOR PATIENTS WITH SENIOR CITIZEN DISCOUNT AND PAYWARD ACCOMMODATION 
        //07/17/2013 - FIX FOR BUG#120
        if (!$objBilling->isCharity() && $objBilling->iswithSCDiscount()) {
            $ndiscount = $objBilling->getBillAreaDiscount('OR');
            //$objResponse->alert($objBilling->debugSQL);
        } else if (!$objBilling->isPHIC() && !$objBilling->isMedicoLegal()) { //added by jasper 05/21/2013 FIX FOR AUTOMATIC EXCESS FOR PHIC PATIENTS
            $ndiscount = $objBilling->getBillAreaDiscount('OR');
        } else {
            $ndiscount = 0.00;
        }

        //added by jasper 07/16/2013 - FIX FOR BUG#120
        if ($objBilling->isPHIC()) {
            if ($totalOpsCharge - $ndiscount <= $totalOpsCoverage) {
                $totalOpsCoverage = $totalOpsCharge - $ndiscount;
            }
        }

		$totalOpsCoverage = $objBilling->ops_confine_coverage;
        $excess = number_format(($totalOpsCharge - $ndiscount - $totalOpsCoverage), 2, '.', ',');
		$objResponse->call("showOpsTotals", number_format($totalOpsCharge, 2, '.', ','), number_format($ndiscount, 2, '.', ','), number_format($totalOpsCoverage, 2, '.', ','), $excess);

//		$objResponse->call("toggleBillAreaStatus", 'OP', 0);
//		$objResponse->call("toggleBillAreaStatus", 'HS', 1);
}

function Medicines(&$objResponse, &$objBilling) {
//		$objBilling->getMedicinesList(); //gathered all medicines consumed
		$objBilling->getMedicineBenefits();
		$objBilling->getConfineBenefits('MS', 'M');

//    $totalMedConfineCoverage = $objBilling->med_confine_coverage;
        $totalMedConfineCoverage = round($objBilling->getAppliedMedsCoverage(), 2);

#    $objResponse->alert("totalMedConfineCoverage=".$totalMedConfineCoverage);
		#$objResponse->alert("Medicines -> confineBenefitsArray=".print_r($confineBenefitsArray, true));
		//$medicinesArray = $objBilling->medicines_list; //get all medicines objects

		$medBenefitsArray = $objBilling->med_product_benefits;

		#$objResponse->alert("med_confine_benefits  =".print_r($medBenefitsArray, true )); //med_confine_benefits
		#$objResponse->alert("medBenefitsArray  =".print_r($medBenefitsArray, true )); //med_product_benefits

		$objResponse->call("jsClearList", 'body_mdListDetails');

		$totalPrice = 0;
		if(is_array($medBenefitsArray) && (count($medBenefitsArray) > 0)){
				foreach($medBenefitsArray as $key=>$value){
						$acPrice = number_format($value->item_charge, 2, '.', ',');
						$price = number_format($value->item_price, 2, '.', ',');
						$totalPrice += $value->item_charge;
						$objResponse->call("jsMedicineList", "body_mdListDetails", $value->bestellnum, $value->artikelname, $value->item_qty, $price, $acPrice, $value->getMedsAddedFlag(), $objBilling->bill_frmdte, ($objBilling->old_bill_nr == ''));
				}
		}
		else
				$objResponse->call("jsMedicineList", "body_mdListDetails", NULL, '', 0, 0, 0, false, $objBilling->bill_frmdte, false);

		//$objResponse->alert($totalPrice);
		//$TotalMedCharge = $objBilling->getTotalMedCharge();
		/**
		* Edited by Alvin
		*/
		$TotalMedCharge = round($totalPrice, 2);

        //added by jasper 05/21/2013 FIX FOR AUTOMATIC EXCESS FOR PHIC PATIENTS
        //BUT NOT FOR PATIENTS WITH SENIOR CITIZEN DISCOUNT AND PAYWARD ACCOMMODATION
        //07/17/2013 - FIX FOR BUG#120
        if (!$objBilling->isCharity() && $objBilling->iswithSCDiscount()) {
            $ndiscount = round($objBilling->getBillAreaDiscount('MS', 'M'), 2);
            //$objResponse->alert($objBilling->debugSQL);
        } else if (!$objBilling->isPHIC() && !$objBilling->isMedicoLegal()) { //added by jasper 05/21/2013 FIX FOR AUTOMATIC EXCESS FOR PHIC PATIENTS
            $ndiscount = round($objBilling->getBillAreaDiscount('MS', 'M'), 2);
        } else {
            $ndiscount = 0.00;
        }

        //added by jasper 07/16/2013 - FIX FOR BUG#120
        if ($objBilling->isPHIC()) {
            if ($TotalMedCharge - $ndiscount < $totalMedConfineCoverage) {
                $totalMedConfineCoverage = $TotalMedCharge - $ndiscount;
            }
        }

        $totalExcess = number_format(($TotalMedCharge - $ndiscount - $totalMedConfineCoverage),2,'.',',');

		$objResponse->call("getMedicine",number_format($TotalMedCharge, 2, '.', ','), number_format($ndiscount, 2, '.', ','),number_format($totalMedConfineCoverage,2,'.',','), $totalExcess );
//		$_SESSION['md_object'] = serialize($objBilling);

//		$objResponse->call("toggleBillAreaStatus", 'MD', 0);

//		$objResponse->call("toggleBillAreaStatus", 'XC', 1);
} // end of Medicines

//function Supplies(&$objResponse, &$objBilling ){
//    $objBilling->getSuppliesList(); // gathered all supplies consumed
//    $objBilling->getSupplyBenefits();
//    $objBilling->getConfineBenefits('MS', 'S');
//
//    $totalSupConfineCoverage = $objBilling->sup_confine_coverage;
//
//    $supBenefitsArray = $objBilling->sup_product_benefits;
//
//    $objResponse->call("jsClearList", 'body_supListDetails');
//
//    if(is_array($supBenefitsArray) && (count($supBenefitsArray) > 0)){
//        foreach($supBenefitsArray  as $key=>$value){
//            $acPrice = number_format($value->item_charge, 2, '.', ',');
//            $price   = number_format($value->item_price, 2, '.', ',');
//            $objResponse->call("jsMedicineList", "body_supListDetails",$value->bestellnum, $value->artikelname, $value->item_qty, $price, $acPrice);
//        }
//    }
//    else
//        $objResponse->call("jsMedicineList", "body_supListDetails", NULL, '', 0, 0, 0);
//
//    $TotalSupCharge = $objBilling->getTotalSupCharge();
//
//    $ndiscount = $objBilling->getBillAreaDiscount('MS', 'S');
//    $totalExcess = number_format(($TotalSupCharge - $ndiscount - $totalSupConfineCoverage), 2, '.', ',');
//
//    $objResponse->call("getSupplies",number_format($TotalSupCharge, 2, '.', ','), number_format($ndiscount, 2, '.', ','),number_format($totalSupConfineCoverage, 2, '.', ','), $totalExcess);
//}//end of Supplies

function ProfFees(&$objResponse, &$objBilling) {
		$objBilling->getProfFeesList();
//		$objResponse->alert(print_r($objBilling->proffees_list, true)."\nLENGTH = ".count($objBilling->proffees_list));
		$objBilling->getProfFeesBenefits();

//		if ($_SESSION['sess_temp_userid']=='medocs')
//			$objResponse->alert($objBilling->debugSQL);

		$hsp_pfs_benefits = $objBilling->getPFBenefits(); //role area
		$ndiscount = 0;
		$proffees_list = $objBilling->proffees_list;
		$objResponse->call("jsClearList", "body_docRoleArea");

		$prevrole_area = '';
		if(is_array($hsp_pfs_benefits) && (count($hsp_pfs_benefits) > 0)) {
				foreach($hsp_pfs_benefits as $key=> $value) {
						if ($value->role_area == $prevrole_area) continue;
						$prevrole_area = $value->role_area;
						reset($proffees_list);
						$objBilling->initProfFeesCoverage($value->role_area);

						#$totalCharge = number_format($value->tot_charge, 2, '.', ',');
						$totalCharge = number_format($objBilling->getTotalPFCharge($value->role_area), 2);
						$coverage    = number_format($objBilling->pfs_confine_coverage[$value->role_area], 2, '.', ',');

						#$ndiscount += $objBilling->getBillAreaDiscount($value->role_area);

						//Display role area header
						$objResponse->call("jsDoctorsFees", "body_docRoleArea", $value->role_area, $value->role_desc, $totalCharge, $coverage);

						//Display list of doctors in every role area
						$tr ='';
						if (!empty($proffees_list) && is_array($proffees_list)) {
								foreach($proffees_list as $key=>$profValue){
										if($value->role_area == $profValue->role_area) {
												$opcodes = $profValue->getOpCodes();
												if ($opcodes != '') {
												   $opcodes = explode(";", $opcodes);
												}
												if (is_array($opcodes)) {
													foreach($opcodes as $v) {
														$i = strpos($v, '-');
														if (!($i === false)) {
															$code = substr($v, 0, $i);
                              //$objResponse->alert($value->role_area . ':' . $code);
                              //added by jasper 07/24/2013
                              //$code1 .= $code;
                              //if ($value->role_area == 'D3' && findOPcodeNormalDelivery($code)) {
                              //    $nonDiscountablePF += $profValue->dr_charge;
                              //} else {
                              //    $nonDiscountablePF += 0;    
                              //}
                              //added by jasper 07/24/2013
                              if (!$profValue->getIsExcludedFlag()) {
                                  $objBilling->getConfineBenefits($value->role_area, $profValue->getDrNr(), $profValue->getRoleLevel(), false, 0, $code);
                              }
                              if ($objBilling->getIsCoveredByPkg() && !$profValue->getIsExcludedFlag()) break;
														}
													}
												}
												else {
                            if (!$profValue->getIsExcludedFlag()) {
                                $objBilling->getConfineBenefits($value->role_area, $profValue->getDrNr(), $profValue->getRoleLevel());
                            }
                        }

												$drName = $profValue->dr_first." ".$profValue->dr_mid.(substr($profValue->dr_mid, strlen($profValue->dr_mid)-1,1) == '.' ? " " : ". ").$profValue->dr_last;
												$drCharge = number_format($profValue->dr_charge, 2, '.', ',');
                                                //added by jasper 07/22/2013
                                                $totalPF += $profValue->dr_charge;
                                                //added by jasper 07/22/2013
												if(isDoctorsExists($objBilling->getCurrentEncounterNr(), $profValue->dr_nr, $profValue->getRoleNo())){
														$xtd = "<td align=\"center\" style=\"border-right:solid #999999 thin; border-top:solid #999999 thin\" width=\"5%\"><img src=\"../../images/btn_delitem.gif\" style=\"cursor:pointer; display:".($objBilling->old_bill_nr == '' ? "" : "none")."\" onclick=\"initMsgDialog(".$profValue->dr_nr.",".$profValue->getRoleNo().")\"></td><td style=\"border-right:solid #999999 thin; border-top:solid #999999 thin\" width=\"75%\">".$drName."</td>";
												}else{
//														$xtd = "<td style=\"border-right:solid #999999 thin; border-top:solid #999999 thin\" width=\"5%\">&nbsp;</td><td style=\"border-right:solid #999999 thin; border-top:solid #999999 thin\" width=\"75%\">".$drName."</td>";
														$xtd = "<td align=\"center\" style=\"border-right:solid #999999 thin; border-top:solid #999999 thin\" width=\"5%\"><img src=\"../../images/btn_delitem.gif\" style=\"cursor:pointer; display:".($objBilling->old_bill_nr == '' ? "" : "none")."\" onclick=\"initMsgDialog2(".$profValue->dr_nr.",".$profValue->getRoleNo().")\"></td><td style=\"border-right:solid #999999 thin; border-top:solid #999999 thin\" width=\"75%\">".$drName."</td>";
												}

												$xtd .= "<td style=\"border-top:solid #999999 thin\" width=\"20%\" align=\"right\">".$drCharge."</td>";

												$tr .=  "<tr id=\"dr".$profValue->dr_nr."-".$profValue->getRoleNo()."\">".$xtd."</tr>";
												$objResponse->assign($value->role_area, "innerHTML", $tr);
												$objResponse->assign("coverage_".$value->role_area, "innerHTML", number_format($objBilling->pfs_confine_coverage[$value->role_area], 2, '.', ','));
										}
								} // end foreach proffees_list
						}

                        //added by jasper 05/21/2013 FIX FOR AUTOMATIC EXCESS FOR PHIC PATIENTS
                        //BUT NOT FOR PATIENTS WITH SENIOR CITIZEN DISCOUNT AND IN PAYWARD ACCOMMODATION 
                        //07/17/2013 - FIX FOR BUG#120
                        if (!$objBilling->isCharity() && $objBilling->iswithSCDiscount()) {
                            //$objResponse->alert($objBilling->debugSQL);
                            $ndiscount += $objBilling->getBillAreaDiscount($value->role_area);
                        } else if (!$objBilling->isPHIC() && !$objBilling->isMedicoLegal()) {
                            $ndiscount += $objBilling->getBillAreaDiscount($value->role_area);
                        } else {
                            $ndiscount = 0.00;
                        }
                        $totalPFCoverage += $objBilling->pfs_confine_coverage[$value->role_area];
				}//1st foreach
                //added by jasper 07/16/2013 - FIX FOR BUG#120
                //$objResponse->alert($totalPF . " " . $ndiscount . " " . $totalPFCoverage);
                //$objResponse->alert($code1);
                if ($objBilling->isPHIC()) {
                    if ($totalPF - $ndiscount <= $totalPFCoverage) {
                        $totalPFCoverage = $totalPF - $ndiscount;
                    }
                }
		}
		else
				$objResponse->call("jsDoctorsFees", "body_docRoleArea", NULL, '', 0, 0);

//    $objResponse->alert(print_r($objBilling->pfs_confine_benefits, true));
		$nPFCharge = round($objBilling->getTotalPFCharge(),4);
        //added by jasper 07/23/2013 FIX FOR BUGZILLA ID 302
        //if (!$objBilling->isPHIC() && $objBilling->isOBAnnex()) {
        //    $ndiscount = $ndiscount - $nonDiscountablePF;
        //    $_SESSION['nonDiscountablePF'] = $nonDiscountablePF;
        //} else {
        //    $_SESSION['nonDiscountablePF'] = 0;
        //}
        //$objResponse->alert($objBilling->nonDiscountablePF . " / " . $objBilling->getBillAreaDiscount("D3"));
        //$_SESSION['nonDiscountablePF'] = $objBilling->nonDiscountablePF;
        //added by jasper 07/23/2013 FIX FOR BUGZILLA ID 302
        //removed by jasper 07/22/2013
        //$totalExcess = number_format($nPFCharge - round($ndiscount,4) - $objBilling->getTotalPFCoverage(), 2, '.', ',');
        $totalExcess = number_format($nPFCharge - round($ndiscount,4) - $totalPFCoverage, 2, '.', ',');

        //removed by jasper 07/29/2013
        //$objResponse->call("showPFTotals",number_format($nPFCharge, 2, '.', ','), number_format($ndiscount, 2, '.', ','), number_format($objBilling->getTotalPFCoverage(), 2, '.', ','), $totalExcess);
        $objResponse->call("showPFTotals",number_format($nPFCharge, 2, '.', ','), number_format($ndiscount, 2, '.', ','), number_format($totalPFCoverage, 2, '.', ','), $totalExcess);

//		$objResponse->call("toggleBillAreaStatus", 'PF', 0);

//		$bDialysis = $_SESSION['isDialysis'];
//		if (!$bDialysis) $objResponse->call("toggleBillAreaStatus", 'OP', 1);
}// end of function ProfFees

function setConfinementType($enc, $type, $classify_id, $create_id, $bill_dte = "0000-00-00 00:00:00"){
        global $db;
        $objResponse = new xajaxResponse();

/***
        $sql_a = "select * from seg_encounter_confinement ".
                         "   where str_to_date(classify_dte, '%Y-%m-%d %H:%i:%s') < '" . $bill_dte ."' and ".
                         "      encounter_nr = '".$enc."' ".
                         "   order by classify_dte desc limit 1";

        if($result = $db->Execute($sql_a)){
                if($result->RecordCount()){
                        // update data to seg_encounter_confinement
                        $row = $result->FetchRow();
                        $classify_dte = $row['classify_dte'];
                        $confine_id   = $row['confinetype_id'];

                        if (strcmp($bill_dte, "0000-00-00 00:00:00") == 0)
                                $n_classify_dte = date('Y-m-d H:i:s');
                        else
                                $n_classify_dte = $bill_dte;
                        $n_classify_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($n_classify_dte)));

                        #$objResponse->alert("update confinement: confinement type  =".$type." \n classify_id = ".$classify_id." \n classify_dte = ".$classify_dte);
                        $sql_b = "UPDATE seg_encounter_confinement SET confinetype_id = ".$type.", classify_id = '".$classify_id."', classify_dte = '".$n_classify_dte."' ".
                                         "\n WHERE encounter_nr = '".$enc."' and confinetype_id = ". $confine_id ." and classify_dte = '". $classify_dte ."'";

                        if($db->Execute($sql_b)){
                                $objResponse->alert("Case type is successfully updated!");
                                $objResponse->call("js_Recalculate");
                                #$objResponse->call("xajax_mainBilling", $enc, $bill_dte);
                        }else{
                                $objResponse->alert("DB Error =" .$sql_b);
                        }
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

        #Added by Jarel 06/17/2013
        $fldarray = array('encounter_nr' => $db->qstr($enc),
                'confinetype_id'  => $db->qstr($type),
                'classify_id'    => $db->qstr($classify_id),
                'classify_dte'    => $db->qstr($classify_dte),
                'create_id'    => $db->qstr($create_id),
                'create_time'    => $db->qstr($create_time)
               );

        $bsuccess = $db->Replace('seg_encounter_confinement', $fldarray, array('encounter_nr', 'confinetype_id'));
       
        if($bsuccess){
                $objResponse->alert("Case type is successfully saved!");
                $objResponse->call("js_Recalculate");
        }else{
                $objResponse->alert("Case type is NOT saved!");
        }

        return $objResponse;
}

function setCaseType($enc, $type, $modify_id, $create_id, $bill_dte = "0000-00-00 00:00:00") {
		global $db;
		$objResponse = new xajaxResponse();

    /***
		$sql_a = "select * from seg_encounter_case ".
						 "   where str_to_date(modify_dt, '%Y-%m-%d %H:%i:%s') < '" . $bill_dte ."' and ".
						 "      encounter_nr = '".$enc."' ".
						 "   order by modify_dt desc limit 1";

		if($result = $db->Execute($sql_a)){
				if($result->RecordCount()){
						// update data to seg_encounter_case
						$row = $result->FetchRow();
						$modify_dt   = $row['modify_dt'];
						$casetype_id = $row['casetype_id'];

						if (strcmp($bill_dte, "0000-00-00 00:00:00") == 0)
								$n_modify_dt = date('Y-m-d H:i:s');
						else
								$n_modify_dt = $bill_dte;
						$n_modify_dt = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($n_modify_dt)));

						#$objResponse->alert("update confinement: confinement type  =".$type." \n classify_id = ".$classify_id." \n classify_dte = ".$classify_dte);
						$sql_b = "UPDATE seg_encounter_case SET casetype_id = ".$type.", modify_id = '".$modify_id."', modify_dt = '".$n_modify_dt."' ".
										 "\n WHERE encounter_nr = '".$enc."' and casetype_id = ". $casetype_id ." and modify_dt = '". $modify_dt ."'";

						if($db->Execute($sql_b)){
								$objResponse->alert("Confinement type is successfully updated!");
						}else{
								$objResponse->alert("DB Error =" .$sql_b);
						}
				}else{

    ****/

    //Insert new data to seg_encounter_confinement
    if (strcmp($bill_dte, "0000-00-00 00:00:00") == 0) {
        $modify_dt = date('Y-m-d H:i:s');
        $create_dt = date('Y-m-d H:i:s');
    }
    else {
        $modify_dt = $bill_dte;
        $create_dt = date('Y-m-d H:i:s');
    }
    $modify_dt = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($modify_dt)));
//						$create_dt = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($create_dt)));
    $create_dt = strftime("%Y-%m-%d %H:%M:%S", strtotime($create_dt));

    #$objResponse->alert("save confinement encounter= ".$enc." \n type =".$type." \n classify_id = ".$classify_id." \n classify_dte = " .classify_dte." \n create_id = ". $create_id);
    if ($type != '0') {
      $fldarray = array('encounter_nr' => $db->qstr($enc),
                        'casetype_id'  => $db->qstr($type),
                        'modify_id'    => $db->qstr($modify_id),
                        'modify_dt'    => $db->qstr($modify_dt),
                        'create_id'    => $db->qstr($create_id),
                        'create_dt'    => $db->qstr($create_dt),
                        'is_deleted'    => $db->qstr(0) //added by jasper 05/10/2013
                       );
      //edited by jasper 05/10/2013 - TO INCLUDE IS_DELETED FIELD. ONE CASE TYPE EVERY ENCOUNTER ONLY EXCEPT DELETED
      //$bsuccess = $db->Replace('seg_encounter_case', $fldarray, array('encounter_nr', 'casetype_id', 'modify_dt'));
      $bsuccess = $db->Replace('seg_encounter_case', $fldarray, array('encounter_nr', 'is_deleted'));

//      $sql_c = "INSERT INTO seg_encounter_case (encounter_nr, casetype_id, modify_id, modify_dt, create_id, create_dt) ".
//                "\n VALUES('".$enc."', '".$type."', '".$modify_id."' , '".$modify_dt."' , '".$create_id."','".$create_dt."')";
//      if ($db->Execute($sql_c)) {

      if ($bsuccess) {
          $objResponse->alert("Confinement type is successfully saved!");
      }else
      	$objResponse->alert("Confinement type is NOT saved!");
    }
    else {
      // Logically delete the set encounter case types later than current bill date ...
      //edited by jasper 05/09/2013 - FROM >= CHANGED TO <=
      $strSQL = "UPDATE seg_encounter_case SET
                    is_deleted = 1,
                    modify_id = '".$modify_id."',
                    modify_dt = NOW()
                 WHERE
                    str_to_date(modify_dt, '%Y-%m-%d %H:%i:%s') <= '" . $bill_dte ."'
					          AND encounter_nr = '".$enc."' and !is_deleted";
      //$objResponse->alert($strSQL);
      if ($db->Execute($strSQL)) {
          $strSQL = "SELECT ROW_COUNT() rcount";
          if ( $result = $db->Execute($strSQL) ) {
            if ( $row = $result->FetchRow() ) {
              if ($row['rcount']) $objResponse->alert($row['rcount']." case types set at a later date deleted!");
            }
          }
      }
    }

//				}
//		}else{
//				$objResponse->alert("DB Error = ".$sql_a);
//		}

		return $objResponse;
} // setCaseType

function getConfineTypeOption(&$objResponse, $confinementId=''){
		global $db;
		//$objResponse = new xajaxResponse();
	$sql = "select confinetype_id as id , confinetypedesc as typedesc       \n
				 from seg_type_confinement                                    \n
				 where is_deleted = 0";

		if($result = $db->Execute($sql)){
				if($result->RecordCount()){
						$objResponse->call("js_ClearOptions", "confineTypeOption");
						$objResponse->call("js_AddOptions","confineTypeOption", "- Select Case Type -", 0);
						while($row = $result->FetchRow()){
								$objResponse->call("js_AddOptions","confineTypeOption", $row['typedesc'], $row['id']);
						}

						if($confinementId){
								#$objResponse->alert("confinementType = ".$confinementId);
								$objResponse->call("js_setOption", "confineTypeOption", $confinementId);
						}else{
								//$objResponse->alert("No case type set as of indicated bill date!");
								$objResponse->call("js_setOption", "confineTypeOption", "- Select Case Type -");
						}
				}else{
						//$objResponse->alert("ERROR: No case type found");
				}
		}

		//return $objResponse;
} // end of function getConfineTypeOption

function getCaseTypeOption(&$objResponse, $ncasetypeid=''){
		global $db;

		$sql = "select casetype_id as id, casetype_desc as typedesc from seg_type_case";

		if($result = $db->Execute($sql)){
				if($result->RecordCount()){
						$objResponse->call("js_ClearOptions", "caseTypeOption");
						$objResponse->call("js_AddOptions","caseTypeOption", "- Select Confinement Type -", 0);
						while($row = $result->FetchRow()){
								$objResponse->call("js_AddOptions","caseTypeOption", $row['typedesc'], $row['id']);
						}

						if($ncasetypeid){
								$objResponse->call("js_setOption", "caseTypeOption", $ncasetypeid);
						}else{
								//$objResponse->alert("No confinement type set as of indicated bill date!");
								$objResponse->call("js_setOption", "caseTypeOption", "- Select Confinement Type -");
						}
				}else{
						//$objResponse->alert("ERROR: No confinement types found");
				}
		}
} // getCaseTypeOption

function getConfineType(&$objResponse, &$objBilling, $enc, $bill_dte = "0000-00-00 00:00:00"){
//    global $db;

//    $sql = "select * from seg_encounter_confinement ".
//           "   where str_to_date(classify_dte, '%Y-%m-%d %H:%i:%s') < '" . $bill_dte ."' and ".
//           "      encounter_nr = '".$enc."' ".
//           "   order by classify_dte desc limit 1";

//    if($result = $db->Execute($sql)){
//        if($result->RecordCount()){
//            $row = $result->FetchRow();
//            getConfineTypeOption($objResponse, $row['confinetype_id']);
//        }else{
//            getConfineTypeOption($objResponse, "");
//        }
//    }

		getConfineTypeOption($objResponse, $objBilling->getConfinementType());
}// end of function getConfineType

function getCaseType(&$objResponse, $enc, $bill_dte = "0000-00-00 00:00:00") {
		global $db;

		$sql = "select * from seg_encounter_case ".
					 "   where str_to_date(modify_dt, '%Y-%m-%d %H:%i:%s') < '" . $bill_dte ."' and ".
					 "      encounter_nr = '".$enc."' and !is_deleted ".
					 "   order by modify_dt desc, create_dt desc limit 1";

		if($result = $db->Execute($sql)){
				if($result->RecordCount()){
						$row = $result->FetchRow();
						getCaseTypeOption($objResponse, $row['casetype_id']);
				}else{
						getCaseTypeOption($objResponse, "");
				}
		}
}// getCaseType

function setALLDepartment($admit_inpatient){
		$dept_obj = new Department;
		$objResponse = new xajaxResponse();

		$rs=$dept_obj->getAllOPDMedicalObject($admit_inpatient);

		#$objResponse->alert("rs = ".print_r($rs, true));

		if ($rs) {
				$objResponse->call("js_ClearOptions","doclist"); // set to department status ..
				if ($dept_obj->count > 0){
						$objResponse->call("js_AddOptions","doclist","-Select a Department-",0);
				}else{
						$objResponse->call("js_AddOptions","doclist", "-Select a Department-",0);
				}
				while ($result=$rs->FetchRow()) {
					 $objResponse->call("js_AddOptions","doclist",$result["name_formal"],$result["nr"]);
				}
		}
		else {
				$objResponse->alert("setALLDepartment : Error retrieving Department information...");
		}
		return $objResponse;
}// end of function setALLDepartment

function setMemCategoryOptions() {
		global $db;
		$objResponse = new xajaxResponse();

		$strSQL = "select * from seg_memcategory order by memcategory_desc";
		if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
						$objResponse->call("js_ClearOptions","category_list");
						$objResponse->call("js_AddOptions","category_list","-Select Category-", 0);
						while ($row = $result->FetchRow()) {
								$objResponse->call("js_AddOptions", "category_list", $row['memcategory_desc'], $row['memcategory_id']);
						}
				}
		} else {
				$objResponse->alert("ERROR: Cannot retrieve membership categories ...");
		}

		return $objResponse;
}

function setWardOptions() {
		$objResponse = new xajaxResponse();

		$objwrd = new Ward();
		$wards = $objwrd->getAllActiveWards();
		if ($wards) {
				$objResponse->call("js_ClearOptions","wardlist");
				$objResponse->call("js_ClearOptions","roomlist");

				if ($wards->RecordCount()) {
						$objResponse->call("js_AddOptions","wardlist","- Select Ward -", 0);
						$objResponse->call("js_AddOptions","roomlist","- Select Room -", 0);

						while ($row = $wards->FetchRow()) {
								$objResponse->call("js_AddOptions", "wardlist", ucwords($row['name']), $row['nr']);
						}
				}
		}
		else
				$objResponse->alert("ERROR: Cannot retrieve available wards!");

		return $objResponse;
}

function setORWardOptions() {
		$objResponse = new xajaxResponse();

		$objwrd = new Ward();
		$wards = $objwrd->getAllOpWards();
		if ($wards) {
				$objResponse->call("js_ClearOptions","opwardlist");
				$objResponse->call("js_ClearOptions","orlist");

				if ($wards->RecordCount()) {
						$objResponse->call("js_AddOptions","opwardlist","- Select O.R. Ward -", 0);
						$objResponse->call("js_AddOptions","orlist","- Select Operating Room -", 0);

						while ($row = $wards->FetchRow()) {
								$objResponse->call("js_AddOptions", "opwardlist", $row['name'], $row['nr']);
						}
				}
		}
		else
				$objResponse->alert("ERROR: Cannot retrieve available O.R. wards!");

		return $objResponse;
}

function setWardRooms($ward_nr) {
		$objResponse = new xajaxResponse();

		$objwrd = new Ward();
		$wards = $objwrd->getRoomsData($ward_nr);
		if ($wards) {
				if ($wards->RecordCount()) {
						$objResponse->call("js_ClearOptions","roomlist");
						$objResponse->call("js_AddOptions","roomlist","- Select Room -", 0);
						while ($row = $wards->FetchRow()) {
								$objResponse->call("js_AddOptions", "roomlist", $row['room_nr'], $row['nr']);
						}
				}
		}
		else
				$objResponse->alert("ERROR: Cannot retrieve available rooms!");

		return $objResponse;
}

function setORWardRooms($ward_nr) {
		$objResponse = new xajaxResponse();

		$objwrd = new Ward();
		$wards = $objwrd->getRoomsData($ward_nr);
		if ($wards) {
				if ($wards->RecordCount()) {
						$objResponse->call("js_ClearOptions","orlist");
						$objResponse->call("js_AddOptions","orlist","- Select Operating Room -", 0);
						while ($row = $wards->FetchRow()) {
								$objResponse->call("js_AddOptions", "orlist", $row['room_nr'], $row['nr']);
						}
				}
		}
		else
				$objResponse->alert("ERROR: Cannot retrieve available rooms!");

		return $objResponse;
}

function setDoctors($admit_inpatient=0, $dept_nr=0, $personell_nr=0) {
		$pers_obj=new Personell;
		$objbillinfo = new BillInfo();

		$objResponse = new xajaxResponse();
		#$objResponse->alert("dept : $dept_nr");
		if ($dept_nr)
				$rs=$pers_obj->getDoctorByDept($dept_nr, $admit_inpatient);
		else
				$rs=$pers_obj->getDoctors($admit_inpatient);

		if ($rs) {
				$objResponse->call("js_ClearOptions","doclist");
				if ($pers_obj->count > 0){
						$objResponse->call("js_AddOptions","doclist","-Select a Doctor-",0);
				}else{
						if ($dept_nr)
							$objResponse->call("js_AddOptions","doclist","-No Doctor Available-",0);
						else
							$objResponse->call("js_AddOptions","doclist","-Select a Doctor-",0);
						}

				while ($result=$rs->FetchRow()) {
           				//$doctor_name = $result["name_first"]." ".$result["name_2"]." ".$result["name_last"];
           				//$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
						$doctor_name = $objbillinfo->concatname($result["name_last"], $result["name_first"], $result["name_middle"]);
						$doctor_name = ucwords(strtolower($doctor_name));
                        $objResponse->call("js_AddOptions","doclist",$doctor_name . " [" . $result["personell_nr"] . "]", $result["personell_nr"]);
				}
		}
		else {
				$objResponse->alert("setDoctors : Error retrieving Doctors information...");
		}

		return $objResponse;
}// end of function setDoctors()

//added by jasper 11/18/12
/****
function getDoctorInfo($personel_nr, $admission_date, $discharge_date){
   $objPerson =  new Personell();
   $objResponse = new xajaxResponse();
   $objAccreditation = new DoctorAccreditation();
   $objPerson->loadPersonellData($personel_nr);
   $result = $objPerson->personell_data;
   $AccreCode = $objPerson->getDoctorAccreditation($personel_nr);
   $fullname = $objPerson->concatname($result["name_last"], $result["name_first"], $result["name_middle"]);
   //$admission_date = "02-02-2000";
   //$discharge_date = "02-10-2000";
   $objAccreditation->setDoctorInfoFromHospital((string)$AccreCode[0],
                                               $result["name_first"],
                                                $result["name_last"],
                                                $result["name_middle"],
                                               $fullname,
                                                $result["tin"],
                                                $result["date_birth"],
                                                $admission_date,
                                                $discharge_date
                                               );
 *
 */
   /*$objResponse->alert($admission_date . " " .
                       $discharge_date . " " .
                       $result["pid"] . " " .
                       $personel_nr . " " .
                       $AccreCode[0] . " " .
                       $result["name_last"] . " " .
                       $result["name_first"] . " " .
                       $result["name_middle"] . " " .
                       $result["date_birth"] . " " .
                       $result["tin"]); */
/*   extract($objAccreditation->isDoctorAccredited());
   //$objResponse->alert(print_r($objAccreditation->isDoctorAccredited(),1));
   $objResponse->alert("Doctor's Name: " . $name . "\n" .
                       "Accreditation Code: " . $accrecode . "\n" .
                       "Validity Period: " . $validity . "\n" .
                       "Admission Date: " . $admissiondate . "  Discharge Date: " . $dischargedate . "\n" .
                       "\n" . $type);
   //$objResponse->alert($type);
   return $objResponse;
}
//added by jasper 11/18/12
***/

function setRoleArea($jobType){
		global $db;
		$objResponse = new xajaxResponse();

		$sql = "SELECT crp.nr, crp.job_type_nr, crp.role, crp.name, crp.role_area ".
						"\n  FROM care_role_person as crp WHERE job_type_nr = '".$jobType."'";

		if($result = $db->Execute($sql)){
				if($result->RecordCount()){
						$objResponse->call("js_ClearOptions","rolearea");
						$objResponse->call("js_AddOptions","rolearea","-Select a Role area-",0);
						while($row = $result->FetchRow()){
								$objResponse->call("js_AddOptions", "rolearea", $row['name'], $row['nr']);
						}
				}
		}else{
				$objResponse->alert("setRoleArea : Error retrieving role person information...");
		}

		return $objResponse;
}

function setOptionRoleLevel() {
		global $db;
		$objResponse = new xajaxResponse();

		$sql = "SELECT * ".
						"\n  FROM seg_role_tier";

		if($result = $db->Execute($sql)){
				if($result->RecordCount()){
						$objResponse->call("js_ClearOptions", "role_level");
						$objResponse->call("js_AddOptions", "role_level", "-Select Level-",0);
						while($row = $result->FetchRow()){
								$objResponse->call("js_AddOptions", "role_level", $row['tier_desc'], $row['tier_nr']);
						}
				}
		}else{
				$objResponse->alert("setOptionRoleLevel : Error retrieving levels of role ...");
		}

		return $objResponse;
}

function getNumDays($enc_nr, $bill_date) {
	global $db;

	$ndays = 1;
	//$strSQL = "SELECT DATEDIFF(date_to, date_from) AS days_attended
    //edited jasper 06/07/2013
    $strSQL = "SELECT DATEDIFF('$bill_date', date_from) AS days_attended
					FROM
						(SELECT MIN(date_from) AS date_from, MIN(time_from) AS time_from,
						 MAX(CASE WHEN date_to = '0000-00-00' OR date_to IS NULL THEN CURDATE() ELSE date_to END) AS date_to,
						 MAX(CASE WHEN time_to = '00:00:00' OR time_to IS NULL THEN CURTIME() ELSE time_to END) AS time_to
						FROM care_encounter_location WHERE encounter_nr = '$enc_nr'
						ORDER BY date_from, time_from) as t";
	if ($result = $db->Execute($strSQL)){
		if ($result->RecordCount()) {
			if ($row = $result->FetchRow()) {
				$ndays = $row['days_attended'];
			}
		}
	}

	return $ndays;
}

//-------------------------------------------------------------------------------------
//
// This assigns the default PF charge in case rate package given the role of doctor ...
//
//-------------------------------------------------------------------------------------
function assignDefaultPkgPF($pkgid, $enc_nr, $role_nr, $bill_dt, $is_excluded) {
    global $db;

    $dr_charge = 0;

    $objResponse = new xajaxResponse();

    if ($is_excluded == '0') {
        $strSQL = "SELECT role_area
                        FROM care_role_person
                        WHERE nr = '$role_nr'";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                    $objbilling = new Billing($enc_nr, $bill_dt);
                    $issurgical = $objbilling->isSurgicalCase();

                    $amountlimit = $objbilling->getPkgAmountLimit();

                    $rate = $objbilling->getCaseRatePkgLimit($row['role_area'], $issurgical);
                    $dr_charge = $amountlimit * $rate;
                }
            }
        }
    }
    else
        $objResponse->call("setDrCharge", $dr_charge);

    return $objResponse;
}

function assignDefaultCharge($dr_nr, $role_nr, $tier_nr, $enc_nr, $bill_dt, $confinetype_id, $days_attended = 0) {
	global $db;

	$dr_charge = 0;

	$objResponse = new xajaxResponse();
    //added jasper 06/07/2013
    $bill_dte = strftime("%Y-%m-%d", strtotime($bill_dt));
	if ($days_attended == 0) {
		//$days_attended = getNumDays($enc_nr);
        $days_attended = getNumDays($enc_nr, $bill_dte);
		$objResponse->call("setDaysAttended", $days_attended);
	}
	$strSQL = "SELECT role_area
					FROM care_role_person
					WHERE nr = '$role_nr' AND role_area IN ('D1','D2')";

	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			if ($row = $result->FetchRow()) {
				$strSQL = "SELECT (fn_getdailyrate('$enc_nr', date('$bill_dt'), $tier_nr, $confinetype_id, $dr_nr) * $days_attended) as dr_charge";
				if ($result = $db->Execute($strSQL)) {
					if ($result->RecordCount()) {
						if ($row = $result->FetchRow()) {
							$dr_charge = $row['dr_charge'];
						}
					}
				}

				$objResponse->call("setDrCharge", $dr_charge);
			}
		}
	}

	return $objResponse;
}

function getMiscOpRefNo($bill_frmdte, $enc_nr) {
		global $db;

		$srefno = '';
		$strSQL = "select refno ".
							"   from seg_misc_ops ".
							"   where str_to_date(chrge_dte, '%Y-%m-%d %H:%i:%s') >= '".$bill_frmdte."' ".
							"      and encounter_nr = '".$enc_nr."' ".
							"   order by chrge_dte limit 1";

		if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
						while ($row = $result->FetchRow())
								$srefno = $row['refno'];
				}
		}

		return($srefno);
}

function getOpAccommodationRefNo($bill_frmdte, $enc_nr) {
		global $db;

		$srefno = '';
		$strSQL = "select refno ".
							"   from seg_opaccommodation ".
							"   where str_to_date(chrge_dte, '%Y-%m-%d %H:%i:%s') >= '".$bill_frmdte."' ".
							"      and encounter_nr = '".$enc_nr."' ".
							"   order by chrge_dte limit 1";

		if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
						while ($row = $result->FetchRow())
								$srefno = $row['refno'];
				}
		}

		return($srefno);
}

function getMaxNoFromOPAccomDetails($refno) {
		global $db;

		$n = 0;
		$strSQL = "select ifnull(max(entry_no), 0) as latest_no ".
							"   from seg_opaccommodation_details as sod ".
							"   where refno = '".$refno."'";

		if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
						while ($row = $result->FetchRow())
								$n = $row['latest_no'];
				}
		}

		return($n);
}

function getMaxNoFromPrivyDr($enc_nr, $drnr, $roletypenr) {
		global $db;

		$n = 0;
		$strSQL = "select ifnull(max(entry_no), 0) as latest_no ".
							"   from seg_encounter_privy_dr as sod ".
							"   where encounter_nr = '".$enc_nr."' ".
							"      and dr_nr = ".$drnr.
							"      and dr_role_type_nr = ".$roletypenr;

		if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
						while ($row = $result->FetchRow())
								$n = $row['latest_no'];
				}
		}

		return($n);
}

function getMiscChrgRefNo($bill_frmdte, $enc_nr) {
		global $db;

		$srefno = '';
		$strSQL = "select refno ".
							"   from seg_misc_chrg ".
							"   where str_to_date(chrge_dte, '%Y-%m-%d %H:%i:%s') >= '".$bill_frmdte."' ".
							"      and encounter_nr = '".$enc_nr."' ".
							"   order by chrge_dte limit 1";

		if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
						while ($row = $result->FetchRow())
								$srefno = $row['refno'];
				}
		}

		return($srefno);
}

function getMiscSrvcRefNo($bill_frmdte, $enc_nr) {
		global $db;

		$srefno = '';
        # Fix for MS-535 by Bong
		$strSQL = "select refno ".
							"   from seg_misc_service ".
							"   where str_to_date(chrge_dte, '%Y-%m-%d %H:%i:%s') >= '".$bill_frmdte."' ".
							"      and encounter_nr = '".$enc_nr."' ".
                            "      and !is_cash ".
							"   order by chrge_dte limit 1";

		if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
						while ($row = $result->FetchRow())
								$srefno = $row['refno'];
				}
		}

		return($srefno);
}

function postPkgMiscChrgs($enc_nr, $bill_dt, $enc_date) {
		global $db;

		$bSuccess = true;
		if ($enc_nr != '') {
				// Adjust charge date by 1 second earlier than billing date ...
				if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
						$tmp_dte = $bill_dt;
				else
						$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

				$tmpbill_dte = $tmp_dte;
				$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($tmp_dte)));

				$refno = getMiscChrgRefNo($enc_date, $enc_nr);
				if ($refno == '') {
						$strSQL = "insert into seg_misc_chrg (chrge_dte, encounter_nr, modify_id, create_id, create_dt) ".
											"   values ('".$tmp_dte."', '".$enc_nr."', '".$_SESSION['sess_user_name']."', '".$_SESSION['sess_user_name']."', ".
											"          '".$tmp_dte."')";
						if ($db->Execute($strSQL))
								$refno = getMiscChrgRefNo($enc_date, $enc_nr);
						else {
								$bSuccess = false;
						}
				}

				if ($bSuccess) {
					$bSuccess = false;
					$obj = new SegDialysis();
					$i = 0;
					$tmpSQL = "";
					$strSQL = "insert into seg_misc_chrg_details (refno, service_code, quantity, chrg_amnt) ".
										"   values ";
					if ($res = $obj->getDialysisPackageItems($enc_nr, 'OT')) {
						while ($row = $res->FetchRow()) {
							if ($i++ > 0) $strSQL .= ",";
							$strSQL .= "('".$refno."', '".$row["item_code"]."', ".$row["quantity"].", ".$row["item_price"].")";
						}
						if ($i > 0) $tmpSQL = $strSQL;
					}
					if ($tmpSQL != "") {
						$db->StartTrans();
						$bSuccess = $db->Execute($tmpSQL);
						if (!$bSuccess) $db->FailTrans();
						$db->CompleteTrans();
					}
				}

				if (!$bSuccess && ($refno != '')) {
					$db->LogSQL();
					$strSQL = "delete from seg_misc_chrg where refno = '$refno'";
					$bSuccess = $db->Execute($strSQL);
					$db->LogSQL(false);
				}
		}
}

function chargeMiscChrg($bill_dt, $enc_nr, $misc_code, $acct_typ, $misc_charge, $qty = 1, $bUpdateForm = true) {
		global $db;

		if ($bUpdateForm) $objResponse = new xajaxResponse();

		$s_errmsg = '';

		$bSuccess = true;
		if ($enc_nr != '') {
				// Adjust charge date by 1 second earlier than billing date ...
				if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
						$tmp_dte = $bill_dt;
				else
						$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

				$tmpbill_dte = $tmp_dte;
				$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($tmp_dte)));

//				$objBilling = new Billing($enc_nr, $tmpbill_dte);
				$objBilling = unserialize($_SESSION['billobject']['main']);

				$db->StartTrans();

				$refno = getMiscChrgRefNo($objBilling->bill_frmdte, $enc_nr);
				if ($refno == '') {
						$strSQL = "insert into seg_misc_chrg (chrge_dte, encounter_nr, modify_id, create_id, create_dt) ".
											"   values ('".$tmp_dte."', '".$enc_nr."', '".$_SESSION['sess_user_name']."', '".$_SESSION['sess_user_name']."', ".
											"          '".$tmp_dte."')";
						if ($db->Execute($strSQL))
								$refno = getMiscChrgRefNo($objBilling->bill_frmdte, $enc_nr);
						else {
								$bSuccess = false;
								$s_errmsg = $db->ErrorMsg();
						}
				}

				#$objResponse->alert("RefNo = ". $refno);

				if ($bSuccess) {
						$misc_charge = str_replace(",", "", $misc_charge);
						$strSQL = "insert into seg_misc_chrg_details (refno, service_code, ".((isset($acct_typ) and ($acct_typ != '')) ? "account_type, " : "")."chrg_amnt, quantity) ".
											"   values ('".$refno."', '".$misc_code."', ".((isset($acct_typ) and ($acct_typ != '')) ? $acct_typ.", " : "").$misc_charge.", ".$qty.")";
						$bSuccess = $db->Execute($strSQL);
						$s_errmsg = $db->ErrorMsg();
						//$s_errmsg = $strSQL;
				}

				if (!$bSuccess) $db->FailTrans();
				$db->CompleteTrans();

				if ($bUpdateForm) {
						if ($bSuccess) {
//								$objBilling->getConfinementType();
//								getMiscellaneous($objResponse, $objBilling);
//								recomputeBilling($objResponse, $objBilling, XC_AREA);
//								getTotalBillAmnt($objResponse, $objBilling);
								$_SESSION['computeflag'] = true;
								$_SESSION['xc_done'] = false;
								$objResponse->call("toggleBillAreaStatus", 'XC', 1);
						}
						else {
		//            $objResponse->alert("Query = ". $strSQL);
								$objResponse->alert("ERROR: ". $s_errmsg);
						}
				}
		}

		if ($bUpdateForm)
				return $objResponse;
		else
				return $s_errmsg;
}// end of function chargeMiscChrg()

function postPkgXLService($enc_nr, $bill_dt, $enc_date) {
		global $db;

		$bSuccess = true;
		if ($enc_nr != '') {
				// Adjust charge date by 1 second earlier than billing date ...
				if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
						$tmp_dte = $bill_dt;
				else
						$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

				$tmpbill_dte = $tmp_dte;
				$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($tmp_dte)));

				$refno = getMiscSrvcRefNo($enc_date, $enc_nr);
				if ($refno == '') {
						$strSQL = "insert into seg_misc_service (chrge_dte, encounter_nr, modify_id, create_id, create_dt) ".
											"   values ('".$tmp_dte."', '".$enc_nr."', '".$_SESSION['sess_user_name']."', '".$_SESSION['sess_user_name']."', ".
											"          '".$tmp_dte."')";
						if ($db->Execute($strSQL))
								$refno = getMiscSrvcRefNo($enc_date, $enc_nr);
						else {
								$bSuccess = false;
						}
				}

				if ($bSuccess) {
					$bSuccess = false;
					$obj = new SegDialysis();
					$i = 0;
					$tmpSQL = "";
					$strSQL = "insert into seg_misc_service_details (refno, service_code, quantity, chrg_amnt) ".
										"   values ";
					if ($res = $obj->getDialysisPackageItems($enc_nr, 'LB')) {
						while ($row = $res->FetchRow()) {
							if ($i++ > 0) $strSQL .= ",";
							$strSQL .= "('".$refno."', '".$row["item_code"]."', ".$row["quantity"].", ".$row["item_price"].")";
						}
						if ($i > 0) $tmpSQL = $strSQL;
					}

					if ($res = $obj->getDialysisPackageItems($enc_nr, 'RD')) {
						while ($row = $res->FetchRow()) {
							if ($i++ > 0) $strSQL .= ",";
							$strSQL .= "('".$refno."', '".$row["item_code"]."', ".$row["quantity"].", ".$row["item_price"].")";
						}
						if ($i > 0) $tmpSQL = $strSQL;
					}

					if ($tmpSQL != "") {
						$db->StartTrans();
						$bSuccess = $db->Execute($tmpSQL);
						if (!$bSuccess) $db->FailTrans();
						$db->CompleteTrans();
					}
				}

				if (!$bSuccess && ($refno != '')) {
					$db->LogSQL();
                    # Fix for MS-535 by Bong
					$strSQL = "delete from seg_misc_service where refno = '$refno' and !is_cash";
					$bSuccess = $db->Execute($strSQL);
					$db->LogSQL(false);
				}
		}
}

function chargeMiscService($bill_dt, $enc_nr, $misc_code, $acct_typ, $misc_charge, $qty) {
		global $db;

		$objResponse = new xajaxResponse();

		$s_errmsg = '';

		$bSuccess = true;
		if ($enc_nr != '') {
				// Adjust charge date by 1 second earlier than billing date ...
				if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
						$tmp_dte = $bill_dt;
				else
						$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

				$tmpbill_dte = $tmp_dte;
				$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($tmp_dte)));

//				$objBilling = new Billing($enc_nr, $tmpbill_dte);
				$objBilling = unserialize($_SESSION['billobject']['main']);

				$db->StartTrans();

				$refno = getMiscSrvcRefNo($objBilling->bill_frmdte, $enc_nr);
				if ($refno == '') {
						$strSQL = "insert into seg_misc_service (chrge_dte, encounter_nr, modify_id, create_id, create_dt) ".
											"   values ('".$tmp_dte."', '".$enc_nr."', '".$_SESSION['sess_user_name']."', '".$_SESSION['sess_user_name']."', ".
											"          '".$tmp_dte."')";
						if ($db->Execute($strSQL))
								$refno = getMiscSrvcRefNo($objBilling->bill_frmdte, $enc_nr);
						else {
								$bSuccess = false;
								$s_errmsg = $db->ErrorMsg();
						}
				}

				#$objResponse->alert("Misc Code = ". $misc_code);

				if ($bSuccess) {
						$misc_charge = str_replace(",", "", $misc_charge);
						$strSQL = "insert into seg_misc_service_details (refno, service_code, ".((isset($acct_typ) and ($acct_typ != '')) ? "account_type, " : "")."chrg_amnt, quantity) ".
											"   values ('".$refno."', '".$misc_code."', ".((isset($acct_typ) and ($acct_typ != '')) ? $acct_typ.", " : "").$misc_charge.", ".$qty.")";
						$bSuccess = $db->Execute($strSQL);
						$s_errmsg = $db->ErrorMsg();
				}

				if (!$bSuccess) $db->FailTrans();
				$db->CompleteTrans();

				if ($bSuccess) {
//						$objBilling->getConfinementType();
//						HSLaboratory($objResponse, $objBilling);
//						recomputeBilling($objResponse, $objBilling, HS_AREA);
//						getTotalBillAmnt($objResponse, $objBilling);
					$_SESSION['computeflag'] = true;
					$_SESSION['hs_done'] = false;
					$objResponse->call("toggleBillAreaStatus", 'HS', 1);
				}
				else {
						$objResponse->alert("Query = ". $strSQL);
						$objResponse->alert("ERROR: ". $s_errmsg);
				}
		}

		return $objResponse;
}// end of function chargeMiscService()

function getPharmaChrgRefNo($bill_frmdte, $enc_nr) {
		global $db;

		$srefno = '';
		$strSQL = "select refno ".
							"   from seg_more_phorder ".
							"   where str_to_date(chrge_dte, '%Y-%m-%d %H:%i:%s') >= '".$bill_frmdte."' ".
							"      and encounter_nr = '".$enc_nr."' ".
							"   order by chrge_dte limit 1";

		if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
						while ($row = $result->FetchRow())
								$srefno = $row['refno'];
				}
		}

		return($srefno);
}

function postMedsorSupplies($enc_nr, $bill_dt, $enc_date) {
		global $db;

		$bSuccess = true;
		if ($enc_nr != '') {
				// Adjust charge date by 1 second earlier than billing date ...
				if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
						$tmp_dte = $bill_dt;
				else
						$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

				$tmpbill_dte = $tmp_dte;
				$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($tmp_dte)));

				$refno = getPharmaChrgRefNo($enc_date, $enc_nr);
				$area_code = 'IP';
				if ($refno == '') {
						$strSQL = "insert into seg_more_phorder (chrge_dte, encounter_nr, area_code, modify_id, create_id, create_dt) ".
											"   values ('".$tmp_dte."', '".$enc_nr."', '".$area_code."', '" .$_SESSION['sess_user_name']."', ".
											"           '".$_SESSION['sess_user_name']."', '".$tmp_dte."')";
						if ($db->Execute($strSQL))
								$refno = getPharmaChrgRefNo($enc_date, $enc_nr);
						else {
								$bSuccess = false;
						}
				}

				if ($bSuccess) {
					$bSuccess = false;
					$obj = new SegDialysis();
					$phSQL = "";
					$strSQL = "insert into seg_more_phorder_details (refno, bestellnum, quantity, unit_price) ".
										"   values ";
					if ($res = $obj->getDialysisPackageItems($enc_nr, 'PH')) {
						$i = 0;
						while ($row = $res->FetchRow()) {
							if ($i++ > 0) $strSQL .= ",";
							$strSQL .= "('".$refno."', '".$row["item_code"]."', ".$row["quantity"].", ".$row["item_price"].")";
						}

						if ($i > 0) $phSQL = $strSQL;
					}

					if ($phSQL != "") {
						$db->StartTrans();
						$bSuccess = $db->Execute($phSQL);
						if (!$bSuccess) $db->FailTrans();
						$db->CompleteTrans();
					}
				}

				if (!$bSuccess && ($refno != '')) {
					$db->LogSQL();
					$strSQL = "delete from seg_more_phorder where refno = '$refno'";
					$bSuccess = $db->Execute($strSQL);
					$db->LogSQL(false);
				}
		}
}

function chargeMedorSupply($bill_dt, $enc_nr, $code, $area_code, $uprice, $qty) {
		global $db;

		$objResponse = new xajaxResponse();

		$s_errmsg = '';

		$bSuccess = true;
		if ($enc_nr != '') {
				// Adjust charge date by 1 second earlier than billing date ...
				if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
						$tmp_dte = $bill_dt;
				else
						$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

				$tmpbill_dte = $tmp_dte;
				$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($tmp_dte)));

//				$objBilling = new Billing($enc_nr, $tmpbill_dte);
				$objBilling = unserialize($_SESSION['billobject']['main']);

				$db->StartTrans();

				$refno = getPharmaChrgRefNo($objBilling->bill_frmdte, $enc_nr);
				if ($refno == '') {
						$strSQL = "insert into seg_more_phorder (chrge_dte, encounter_nr, area_code, modify_id, create_id, create_dt) ".
											"   values ('".$tmp_dte."', '".$enc_nr."', '".$area_code."', '" .$_SESSION['sess_user_name']."', ".
											"           '".$_SESSION['sess_user_name']."', '".$tmp_dte."')";
						if ($db->Execute($strSQL))
								$refno = getPharmaChrgRefNo($objBilling->bill_frmdte, $enc_nr);
						else {
								$bSuccess = false;
								$s_errmsg = $db->ErrorMsg();
						}
				}

				#$objResponse->alert("Misc Code = ". $misc_code);

				if ($bSuccess) {
						$uprice = str_replace(",", "", $uprice);
						$qty = str_replace(",", "", $qty);
						$strSQL = "insert into seg_more_phorder_details (refno, bestellnum, quantity, unit_price) ".
											"   values ('".$refno."', '".$code."', ".$qty.", ".$uprice.")";
						$bSuccess = $db->Execute($strSQL);
						$s_errmsg = $db->ErrorMsg();
				}

				if (!$bSuccess) $db->FailTrans();
				$db->CompleteTrans();

				if ($bSuccess) {
//						$objBilling->getConfinementType();
//						HSLaboratory($objResponse, $objBilling);
//						Medicines($objResponse, $objBilling);
//						recomputeBilling($objResponse, $objBilling, HS_AREA);
//						recomputeBilling($objResponse, $objBilling, MD_AREA);
//						getTotalBillAmnt($objResponse, $objBilling);
					$_SESSION['computeflag'] = true;
					$_SESSION['md_done'] = false;
					$_SESSION['hs_done'] = false;
					$objResponse->call("toggleBillAreaStatus", 'HS', 1);
					$objResponse->call("toggleBillAreaStatus", 'MD', 1);
				}
				else {
						$objResponse->alert("Query = ". $strSQL);
						$objResponse->alert("ERROR: ". $s_errmsg);
				}
		}

		return $objResponse;
}// end of function chargeMedorSupply()

function chargeMiscProcedure($bill_dt, $enc_nr, $op_code, $op_rvu, $op_mult, $op_charge, $op_date) {
		global $db;

		$objResponse = new xajaxResponse();

		$bSuccess = true;
		if ($enc_nr != '') {
				// Adjust charge date by 1 second earlier than billing date ...
				if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
						$tmp_dte = $bill_dt;
				else
						$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

				$tmpbill_dte = $tmp_dte;
				$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($tmp_dte)));

//				$objBilling = new Billing($enc_nr, $tmpbill_dte);
				$objBilling = unserialize($_SESSION['billobject']['main']);

				$db->StartTrans();

				$refno = getMiscOpRefNo($objBilling->bill_frmdte, $enc_nr);
				if ($refno == '') {
						$strSQL = "insert into seg_misc_ops (chrge_dte, encounter_nr, modify_id, create_id, create_dt) ".
											"   values ('".$tmp_dte."', '".$enc_nr."', '".$_SESSION['sess_user_name']."', '".$_SESSION['sess_user_name']."', ".
											"          '".$tmp_dte."')";
						if ($db->Execute($strSQL))
								$refno = getMiscOpRefNo($objBilling->bill_frmdte, $enc_nr);
						else
								$bSuccess = false;
				}

				#$objResponse->alert("RefNo = ". $refno);

				if ($bSuccess) {
						$op_charge = str_replace(",", "", $op_charge);
						$strSQL = "insert into seg_misc_ops_details (refno, ops_code, rvu, multiplier, chrg_amnt, op_date) ".
											"   values ('".$refno."', '".$op_code."', ".$op_rvu.", ".$op_mult.", ".$op_charge.", '".$op_date."')";
						$bSuccess = $db->Execute($strSQL);
				}

				if (!$bSuccess) $db->FailTrans();
				$db->CompleteTrans();

				$objResponse->call("doneAddOp");

				if ($bSuccess) {
						$objResponse->alert("Miscellaneous procedure successfully added!");
//						$objBilling->getConfinementType();
//						getBilledOps($objResponse, $objBilling);
//						recomputeBilling($objResponse, $objBilling, OP_AREA);
//						getTotalBillAmnt($objResponse, $objBilling);
						$_SESSION['op_done'] = false;
						$objResponse->call("toggleBillAreaStatus", 'OP', 1);
				}
				else
						$objResponse->alert("Query = ". $strSQL);
		}
		else
			$objResponse->call("doneAddOp");

		return $objResponse;
}// end of function chargeMiscProcedure()

function setMemCategory($aFormValues) {
		global $db;

		$bSuccess = false;
		$msg = '';

		$s_enc_nr = $aFormValues['memcateg_enc'];
		$n_id      = $aFormValues['categ_id'];
		$sDesc    = $aFormValues['categ_desc'];

		$objResponse = new xajaxResponse();

		$db->StartTrans();
		$db->LogSQL();
		$strSQL = "delete from seg_encounter_memcategory where encounter_nr = '".$s_enc_nr."'";
		$bSuccess = $db->Execute($strSQL);

		if ($bSuccess) {
				$strSQL = "insert into seg_encounter_memcategory (encounter_nr, memcategory_id) ".
									"   values ('".$s_enc_nr."', ".$n_id.")";
				$bSuccess = $db->Execute($strSQL);
		}

		$db->LogSQL(false);

		if (!$bSuccess) {
				$msg = $db->ErrorMsg();
				$db->FailTrans();
		}
		$db->CompleteTrans();

		if (!$bSuccess)
				$objResponse->alert("ERROR: ".$msg);
		else
				$objResponse->call('assignMemCategDesc', $sDesc);

		return $objResponse;
}

function delAccommodation($enc_nr, $bill_dt) {
		global $db;

		$objResponse = new xajaxResponse();

		$db->LogSQL();

		$strSQL = "delete from seg_encounter_location_addtl ".
							"   where encounter_nr = '".$enc_nr."' ".
							"   order by entry_no desc limit 1";
		if ($db->Execute($strSQL)) {
//				if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
//						$tmp_dte = $bill_dt;
//				else
//						$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

//				$objBilling = new Billing($enc_nr, $tmp_dte);
//			$objBilling->getConfinementType();
//			showAccommodation($objResponse, $objBilling);
//			recomputeBilling($objResponse, $objBilling, AC_AREA);
//			getTotalBillAmnt($objResponse, $objBilling);
//			$_SESSION['ac_done'] = false;
//			$objResponse->call("toggleBillAreaStatus", 'AC', 1, 1);
            $objResponse->call("js_Recalculate");
		}
		else
				$objResponse->alert("ERROR: ".$db->ErrorMsg());

		$db->LogSQL(false);

		return $objResponse;
}

function delAccom($enc_nr, $bill_dt) {
	global $db;

	$objResponse = new xajaxResponse();

	$db->LogSQL();

	$strSQL = "DELETE FROM care_encounter_location ".
						"   WHERE encounter_nr = '".$enc_nr."' ".
						"   ORDER BY nr DESC LIMIT 3";
	if ($db->Execute($strSQL)) {
//		if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
//			$tmp_dte = $bill_dt;
//		else
//			$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

//		$objBilling = new Billing($enc_nr, $tmp_dte);
//		$objBilling->getConfinementType();
//		showAccommodation($objResponse, $objBilling);
//		recomputeBilling($objResponse, $objBilling, AC_AREA);
//		getTotalBillAmnt($objResponse, $objBilling);

			$_SESSION['ac_done'] = false;
			$objResponse->call("toggleBillAreaStatus", 'AC', 1);
	}
	else
		$objResponse->alert("ERROR: ".$db->ErrorMsg());

	$db->LogSQL(false);

	return $objResponse;
}

function saveAccommodation($aFormValues, $bill_dt) {
		global $db;

		$objResponse = new xajaxResponse();
		$s_enc_nr = $aFormValues['acc_enc_nr'];
		if ($s_enc_nr != '') {
				// Adjust charge date by 1 second earlier than billing date ...
				if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
						$tmp_dte = $bill_dt;
				else
						$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

				$tmpbill_dte = $tmp_dte;
				$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($tmp_dte)));

				$w_nr      = $aFormValues['ward_nr'];
				$rm_nr    = $aFormValues['rm_nr'];
				$ndays    = $aFormValues['days_stay'];
				$nhrs     = $aFormValues['hrs_stay'];
				$nrate    = str_replace(",", "", $aFormValues['rate']);
                if ( ($aFormValues['occupydate'] == '') || is_null($aFormValues['occupydate']) )
                    $occupydate = date('Y-m-d', strtotime($tmp_dte));
                else
                    $occupydate = date('Y-m-d', strtotime($aFormValues['occupydate']));

				$ndays = ($ndays == '') || is_null($ndays) ? 1 : $ndays;
				$nhrs  = ($nhrs == '')  || is_null($nhrs)  ? 0 : $nhrs;

				$strSQL = "insert into seg_encounter_location_addtl (encounter_nr, room_nr, group_nr, days_stay, hrs_stay, rate, occupy_date, modify_id, create_id, create_dt) ".
									"   values ('".$s_enc_nr."', ".$rm_nr.", ".$w_nr.", ".$ndays.", ".$nhrs.", ".$nrate.", '".$occupydate."', '".$_SESSION['sess_user_name']."', ".
									"           '".$_SESSION['sess_user_name']."', '".$tmp_dte."')";
				if ($db->Execute($strSQL)) {
//						$objBilling = new Billing($s_enc_nr, $tmpbill_dte);
//						$objBilling->getConfinementType();
//						showAccommodation($objResponse, $objBilling);
//						recomputeBilling($objResponse, $objBilling, AC_AREA);
//						getTotalBillAmnt($objResponse, $objBilling);
//						$_SESSION['ac_done'] = false;
//						$objResponse->call("toggleBillAreaStatus", 'AC', 1, 1);
                        $objResponse->call("js_Recalculate");
				}
				else
						$objResponse->alert("ERROR: ".$db->ErrorMsg());
//            $objResponse->alert("SQL: ".$strSQL);
		}
		return $objResponse;
}

function saveORAccommodation($aFormValues, $bill_dt, $ops='') {
		global $db;

		$err_msg = '';

		$objResponse = new xajaxResponse();

		$s_enc_nr = $aFormValues['opacc_enc_nr'];
		$bSuccess = true;

		#$objResponse->alert("Encounter no:".$s_enc_nr);

		if ($s_enc_nr != '') {
				// Adjust charge date by 1 second earlier than billing date ...
				if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
						$tmp_dte = $bill_dt;
				else
						$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

				$tmpbill_dte = $tmp_dte;
				$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($tmp_dte)));

				$w_nr        = $aFormValues['opw_nr'];
				$rm_nr       = $aFormValues['opr_nr'];
				$nrvu        = $aFormValues['total_rvu'];
				$nmultiplier = $aFormValues['multiplier'];
				$nchrg       = str_replace(",", "", $aFormValues['oprm_chrg']);

//				$objBilling = new Billing($s_enc_nr, $tmpbill_dte);
				$objBilling = unserialize($_SESSION['billobject']['main']);

				$db->StartTrans();

				$refno = getOpAccommodationRefNo($objBilling->bill_frmdte, $s_enc_nr);
				if ($refno == '') {
						$strSQL = "insert into seg_opaccommodation (chrge_dte, encounter_nr, modify_id, create_id, create_dt) ".
											"   values ('".$tmp_dte."', '".$s_enc_nr."', '".$_SESSION['sess_user_name']."', '".$_SESSION['sess_user_name']."', ".
											"          '".$tmp_dte."')";
						if ($db->Execute($strSQL))
								$refno = getOpAccommodationRefNo($objBilling->bill_frmdte, $s_enc_nr);
						else {
								$bSuccess = false;
								$err_msg = $db->ErrorMsg();
						}
				}

#        $objResponse->alert("RefNo = ". $refno);

				$n = 0;
				if ($bSuccess) {
						$strSQL = "insert into seg_opaccommodation_details (refno, room_nr, group_nr, charge, modify_id, create_id, create_dt) ".
											"   values ('".$refno."', ".$rm_nr.", ".$w_nr.", ".$nchrg.", '".$_SESSION['sess_user_name']."', '".$_SESSION['sess_user_name']."', ".
											"          '".$tmp_dte."')";
						if ($db->Execute($strSQL)) {
								$n = getMaxNoFromOPAccomDetails($refno);
								$bSuccess = ($n > 0);
						}
						else {
								$bSuccess = false;
								$err_msg = $db->ErrorMsg();
						}
				}

				if ($bSuccess) {
						$opsarr = explode("#",$ops);

						foreach ($opsarr as $k => $row) {
								$v = explode(";", $row);

								$strSQL = "insert into seg_ops_chrgd_accommodation (refno, entry_no, ops_refno, ops_entryno, ops_code, rvu, multiplier) ".
													"   value ('".$refno."', ".$n.", '".$v[0]."', ".$v[1].", '".$v[2]."', ".$v[3].", ".$v[4].")";

//								$objResponse->alert($strSQL);

								if (!$db->Execute($strSQL)) {
										$bSuccess = false;
										$err_msg = $db->ErrorMsg();
										break;
								}
						}
				}

				if (!$bSuccess) $db->FailTrans();
				$db->CompleteTrans();

				if ($bSuccess) {
//						$objBilling->getConfinementType();
//						getBilledOps($objResponse, $objBilling);
//						recomputeBilling($objResponse, $objBilling, OP_AREA);
//						getTotalBillAmnt($objResponse, $objBilling);
						$_SESSION['op_done'] = false;
						$objResponse->call("toggleBillAreaStatus", 'OP', 1);
				}
				else
						$objResponse->alert("ERROR: ".$err_msg);
//            $objResponse->alert("SQL: ".$strSQL);
		}
		return $objResponse;
}

function ProcessPrivateDrCharge($aFormValues, $bill_dt = "0000-00-00 00:00:00", $ops='') {
		global $db;
		$objResponse = new xajaxResponse();

		$bolError = false;
		$err_msg = '';
		$bSuccess = true;

		if ($aFormValues['enc'] != '') {
				if($aFormValues['enc'] == '' && $aFormValues['dr_nr'] == ''){
						$objResponse->alert("Please select a  doctor");
						$bolError = true;
				}else{
						// Adjust current time by 1 second earlier than cut-off date in billing ...
						if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
								$tmp_dte = $bill_dt;
						else
								$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

						$tmpbill_dte = $tmp_dte;
						$tmp_dte = strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($tmp_dte)));

//						$objBilling = new Billing($aFormValues['enc'], $tmpbill_dte);
						$objBilling = unserialize($_SESSION['billobject']['main']);
				}
				if($aFormValues['role_nr'] == ''){
						$objResponse->alert("Please specify doctors role.");
						$bolError = true;
				}

				$n = 0;
				if(!$bolError){
						$nCharge = str_replace(",", "", $aFormValues['charge']);

						$db->StartTrans();

						$aFormValues['ndays'] = (($aFormValues['ndays'] == "") ? 0 : $aFormValues['ndays']);

						$sql = "insert into seg_encounter_privy_dr (encounter_nr, dr_nr, dr_role_type_nr, dr_level, days_attended, dr_charge, is_excluded, create_id, create_dt) " .
								 "   values ('".$aFormValues['enc']."', ".$aFormValues['dr_nr'].", ".$aFormValues['role_nr'].", ".$aFormValues['role_level'][0].", ".$aFormValues['ndays'].", ".$nCharge.", ".
												 "            ".$aFormValues['excluded'].", '".$_SESSION['sess_user_name']."', '". $tmp_dte ."')";

						if ($db->Execute($sql)) {
								$n = getMaxNoFromPrivyDr($aFormValues['enc'], $aFormValues['dr_nr'], $aFormValues['role_nr']);
								$bSuccess = ($n > 0);
						}
						else {
								$bSuccess = false;
								$err_msg = $db->ErrorMsg();
						}

						if($bSuccess) {
								if ($ops != '') {
										//.... there is at least one procedure performed by this doctor.
										$opsarr = explode("#",$ops);

										foreach ($opsarr as $k => $row) {
												$v = explode(";", $row);
												$sqltmp = array();
												$sqltmp[0] = ($aFormValues['tier_nr'] != 0) ? 'role_type_level, ' : '';
												$sqltmp[1] = ($aFormValues['tier_nr'] != 0) ? $aFormValues['tier_nr'].", " : '';

                        // Change multiplier if house case ..
                        $objBilling = unserialize($_SESSION['billobject']['main']);
                        if (is_object($objBilling)) {
                          $tmpmult = $objBilling->getHouseCasePCF();
                          if ($tmpmult != 0) $v[4] = $tmpmult;
                        }

												$strSQL = "insert into seg_ops_chrg_dr (encounter_nr, dr_nr, dr_role_type_nr, entry_no, ".$sqltmp[0]."ops_refno, ops_entryno, ops_code, rvu, multiplier) ".
																	"   value ('".$aFormValues['enc']."', ".$aFormValues['dr_nr'].", ".$aFormValues['role_nr'].", ".$n.", ".$sqltmp[1]."'".$v[0]."', ".$v[1].", ".
																	"          '".$v[2]."', ".$v[3].", ".$v[4].")";

//												$objResponse->alert("SQL = ". $sql);

												if (!$db->Execute($strSQL)) {
														$bSuccess = false;
														$err_msg = $db->ErrorMsg();
														break;
												}
										}
								}
						}
						else {
								$bSuccess = false;
								$err_msg = $db->ErrorMsg();
								#$objResponse->alert("SQL = ". $sql);
						}

						if (!$bSuccess) $db->FailTrans();
						$db->CompleteTrans();

						if ($bSuccess) {
								$objResponse->alert("New doctor successfully added in the list!");
//								$objBilling->getConfinementType();
//								ProfFees($objResponse, $objBilling);
//								recomputeBilling($objResponse, $objBilling, PF_AREA);
//								getTotalBillAmnt($objResponse, $objBilling);
							$_SESSION['computeflag'] = true;
							$_SESSION['pf_done'] = false;
							$objResponse->call("toggleBillAreaStatus", 'PF', 1);
						}
						else
								$objResponse->alert("ERROR: ".$err_msg);
				}
		}

		return $objResponse;
}// end of function ProcessPrivateDrCharge()

function recalcDiscount($enc_nr, $bill_dt) {
		$objResponse = new xajaxResponse();

		$bDialysis = $_SESSION['isDialysis'];

		$_SESSION['ac_done'] = false;
		$_SESSION['md_done'] = false;
		$_SESSION['hs_done'] = false;
		$_SESSION['op_done'] = false;
		$_SESSION['pf_done'] = false;
		$_SESSION['xc_done'] = false;

		if (!$bDialysis) $objResponse->call("toggleBillAreaStatus", 'AC', 1, 1); // fix for HISSPMC-115
//		$objResponse->call("toggleBillAreaStatus", 'HS', 1);                   fix for HISSPMC-115
//		$objResponse->call("toggleBillAreaStatus", 'MD', 1);                   fix for HISSPMC-115
//		if (!$bDialysis) $objResponse->call("toggleBillAreaStatus", 'PF', 1);  fix for HISSPMC-115
//		if (!$bDialysis) $objResponse->call("toggleBillAreaStatus", 'OP', 1);  fix for HISSPMC-115
//		$objResponse->call("toggleBillAreaStatus", 'XC', 1);                   fix for HISSPMC-115

//		$objBilling = new Billing($enc_nr, $bill_dt);
//		$objBilling->getConfinementType();


		//Accommodation Objects
//		showAccommodation($objResponse, $objBilling);                // 1

		//Hospital services Objects
//		HSLaboratory(&$objResponse, &$objBilling);                // 2

		//Medicines Objects
//		Medicines(&$objResponse, &$objBilling);                    // 3

		//Supplies Objects
//    Supplies($objResponse, $objBilling);                    // 4

		//Doctors fee
//		ProfFees($objResponse, $objBilling);                    // 5

		// Operation (procedures) ...                            // 6
//		getBilledOps($objResponse, $objBilling);

		// Miscellaneous charges ...
//		getMiscellaneous($objResponse, $objBilling);            // 7

		//deposits or previous payment
//		getPreviousPayment($objResponse, $objBilling);            // 8

		// ... compute total amount billed.
//		getTotalBillAmnt($objResponse, $objBilling);

//    recomputeBilling($objResponse, $objBilling, DS_AREA);
//    getTotalBillAmnt($objResponse, $objBilling);

		return $objResponse;
}

function isDoctorsExists($enc_nr, $dr_nr='', $roletype_nr){
		global $db;

		$sql = "SELECT * FROM seg_encounter_privy_dr WHERE encounter_nr = '$enc_nr' and dr_nr = $dr_nr and dr_role_type_nr = $roletype_nr";
		if($result = $db->Execute($sql)){
				#$objResponse->alert("sqll - " .$sql);
				if($result->RecordCount() >= 1){
						return true;
				}
		}else{
				return false;
		}
}// end of function isDoctorsExists()

function rmPrivateDr($encounter_nr, $dr_nr, $role_nr, $bill_dt){
		global $db;

//		if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
//				$tmp_dte = $bill_dt;
//		else
//				$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

//		$objBilling = new Billing($encounter_nr, $tmp_dte);
		$objResponse = new xajaxResponse();

		$db->LogSQL();
//		$objBilling->getConfinementType();

//    $sql = "DELETE FROM seg_encounter_privy_dr ".
//           "WHERE dr_nr = '".$dr_nr."' ".
//           "   and encounter_nr = '".$encounter_nr."' ".
//           "   order by create_dt desc limit 1";

		$sql = "DELETE FROM seg_encounter_privy_dr ".
					 "WHERE dr_nr = ".$dr_nr." ".
					 "   and encounter_nr = '".$encounter_nr."' ".
					 "   and dr_role_type_nr = $role_nr ".
					 " order by entry_no desc limit 1";
		if($db->Execute($sql)){
//				ProfFees($objResponse, $objBilling);
//				recomputeBilling($objResponse, $objBilling, PF_AREA);
//				getTotalBillAmnt($objResponse, $objBilling);
				$_SESSION['computeflag'] = true;
				$_SESSION['pf_done'] = false;
				$objResponse->call("toggleBillAreaStatus", 'PF', 1);
		}else{
				$objResponse->alert("Deleting file failed");
		}

		$db->LogSQL(false);

		return $objResponse;
}// end of function rmPrivateDr

function rmDr($encounter_nr, $dr_nr, $role_nr, $bill_dt){
		global $db;

//		if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
//				$tmp_dte = $bill_dt;
//		else
//				$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

//		$objBilling = new Billing($encounter_nr, $tmp_dte);
		$objResponse = new xajaxResponse();
//		$objBilling->getConfinementType();

		$db->LogSQL();

		$strSQL = "DELETE FROM seg_encounter_dr_mgt ".
							"   WHERE attending_dr_nr = ".$dr_nr." ".
							"      AND encounter_nr = '".$encounter_nr."'";
		if($db->Execute($strSQL)){
//				ProfFees($objResponse, $objBilling);
//				recomputeBilling($objResponse, $objBilling, PF_AREA);
				$_SESSION['computeflag'] = true;
				$_SESSION['pf_done'] = false;
//				getTotalBillAmnt($objResponse, $objBilling);
				$objResponse->call("toggleBillAreaStatus", 'PF', 1);
		}else{
				$objResponse->alert("Deletion of attending doctor failed!\nSQL = ".$strSQL);
		}

		$db->LogSQL(false);

		return $objResponse;
}// end of function rmDr

function delMiscOp($enc_nr, $bill_dt, $bill_frmdte, $op_code) {
		global $db;

		$bSuccess = false;
		$objResponse = new xajaxResponse();

		$parent_encnr = getParentEncounterNr($enc_nr);
		if ($parent_encnr != '')
				$encounter = "('$parent_encnr','$enc_nr')";
		else
				$encounter = "('$enc_nr')";

		$strSQL = "select * from seg_misc_ops_details ".
							"   where ops_code = '".$op_code."' and exists (select * from seg_misc_ops as smo where smo.refno = seg_misc_ops_details.refno ".
							"      and smo.encounter_nr in $encounter and smo.chrge_dte >= '".$bill_frmdte."') ".
				      "      and not EXISTS(SELECT * FROM seg_ops_chrgd_accommodation AS soca WHERE soca.ops_refno = seg_misc_ops_details.refno AND
										 soca.ops_entryno = seg_misc_ops_details.entry_no AND soca.ops_code = seg_misc_ops_details.ops_code)
					 and not EXISTS(SELECT * FROM seg_ops_chrg_dr AS socd WHERE socd.ops_refno = seg_misc_ops_details.refno AND
										 socd.ops_entryno = seg_misc_ops_details.entry_no AND socd.ops_code = seg_misc_ops_details.ops_code) ".
							"      and get_lock('smops_lock', 10) ".
							"   order by entry_no desc limit 1";
	  $rs = $db->Execute($strSQL);
	  if ($rs) {
//			$db->StartTrans();
			$row = $rs->FetchRow();
			if ($row) {
				$refno = $row['refno'];
				$entryno = $row['entry_no'];

				$db->LogSQL();

	//			$strSQL = "delete from seg_misc_ops_details ".
	//								"   where ops_code = '".$op_code."' and exists (select * from seg_misc_ops as smo where smo.refno = seg_misc_ops_details.refno ".
	//								"      and smo.encounter_nr in $encounter and smo.chrge_dte >= '".$bill_frmdte."') ".
	//					      "      and not EXISTS(SELECT * FROM seg_ops_chrgd_accommodation AS soca WHERE soca.ops_refno = seg_misc_ops_details.refno AND
	//											 soca.ops_entryno = seg_misc_ops_details.entry_no AND soca.ops_code = seg_misc_ops_details.ops_code)
	//						 and not EXISTS(SELECT * FROM seg_ops_chrg_dr AS socd WHERE socd.ops_refno = seg_misc_ops_details.refno AND
	//											 socd.ops_entryno = seg_misc_ops_details.entry_no AND socd.ops_code = seg_misc_ops_details.ops_code) ".
	//								"   order by entry_no desc limit 1";

				$strSQL = "delete from seg_misc_ops_details where refno = '$refno' and entry_no = $entryno and ops_code = '$op_code'";
				$bSuccess = $db->Execute($strSQL);

				$strSQL = "select RELEASE_LOCK('smops_lock')";
				$db->Execute($strSQL);

				if ($bSuccess) {
						// Delete this header if already without details ...
						$dcount = 0;
						$strSQL = "select count(*) dcount from seg_misc_ops_details where refno = '$refno'";
 					  $rs = $db->Execute($strSQL);
 					  if ($rs) {
							$row = $rs->FetchRow();
							$dcount = ($row) ? $row['dcount'] : 0;
							if ($dcount == 0) {
									$strSQL = "delete from seg_misc_ops where refno = '$refno'";
									$db->Execute($strSQL);
							}
 					  }
				}
				else
					$msg = $db->ErrorMsg();

				$db->LogSQL(false);

//			if (!$bSuccess) {
//					$pers_obj=new Personell;
//					$msg = $pers_obj->LastErrorMsg();
//					$db->FailTrans();
//			}
//			$db->CompleteTrans();
			}
  }
	else
		$msg = $db->ErrorMsg();

	$objResponse->call("doneDelOp");

		if ($bSuccess) {
//		$objResponse->call("goSetDeleteFlag");
				$objResponse->alert("Procedure successfully deleted!");
//				if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
//						$tmp_dte = $bill_dt;
//				else
//						$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

//				$objBilling = new Billing($enc_nr, $tmp_dte);
//				$objBilling->getConfinementType();
//				getBilledOps($objResponse, $objBilling);
//				recomputeBilling($objResponse, $objBilling, OP_AREA);
//				getTotalBillAmnt($objResponse, $objBilling);
				$_SESSION['op_done'] = false;
				$objResponse->call("toggleBillAreaStatus", 'OP', 1);
    }
		else {
//		$objResponse->call("noSetDeleteFlag");
				$objResponse->alert("No miscellaneous procedure deleted!");
//		if ($bSuccess)
//			$objResponse->alert("No miscellaneous operation deleted!");
//		else
//			$objResponse->alert("ERROR: ".$msg);
		}
		return $objResponse;
}

function delOpAccommodation($enc_nr, $bill_dt, $bill_frmdte, $op_code) {
		global $db;

		$bSuccess = false;
		$objResponse = new xajaxResponse();

		$parent_encnr = getParentEncounterNr($enc_nr);
		if ($parent_encnr != '')
				$encounter = "('$parent_encnr','$enc_nr')";
		else
				$encounter = "('$enc_nr')";

		$rm_nr = substr($op_code, 3); // Discard 'OR-'
	  $strSQL = "select * from seg_opaccommodation_details ".
							"   where room_nr = ".$rm_nr." and exists (select * from seg_opaccommodation as so where so.refno = seg_opaccommodation_details.refno ".
							"      and so.encounter_nr in $encounter and so.chrge_dte >= '".$bill_frmdte."') ".
							"      and get_lock('sopd_lock', 10) ".
							"   order by entry_no desc limit 1";
	 	$rs = $db->Execute($strSQL);
	 	if ($rs) {
//				$db->StartTrans();
				$row = $rs->FetchRow();
				if ($row) {
						$refno = $row['refno'];
						$entryno = $row['entry_no'];

						$db->LogSQL();

		//				$strSQL = "delete from seg_opaccommodation_details ".
		//									"   where room_nr = ".$rm_nr." and exists (select * from seg_opaccommodation as so where so.refno = seg_opaccommodation_details.refno ".
		//									"      and so.encounter_nr in $encounter and so.chrge_dte >= '".$bill_frmdte."') ".
		//									"   order by entry_no desc limit 1";

						$strSQL = "delete from seg_opaccommodation_details where room_nr = $rm_nr and entry_no = $entryno and refno = '$refno'";
						$bSuccess = $db->Execute($strSQL);

						$strSQL = "select RELEASE_LOCK('sopd_lock')";
						$db->Execute($strSQL);

						if ($bSuccess) {
								// Delete this header if already without details ...
								$dcount = 0;
								$strSQL = "select count(*) dcount from seg_opaccommodation_details where refno = '$refno'";
 								$rs = $db->Execute($strSQL);
 								if ($rs) {
 										$row = $rs->FetchRow();
 										$dcount = ($row) ? $row['dcount'] : 0;
 										if ($dcount == 0) {
												$strSQL = "delete from seg_opaccommodation WHERE refno = '$refno'";
												$db->Execute($strSQL);
										}
								}
						}
						else
							$msg = $db->ErrorMsg();

						$db->LogSQL(false);

//				if (!$bSuccess) {
//						$msg = $db->ErrorMsg();
//						$db->FailTrans();
//				}
//				$db->CompleteTrans();
				}
		}
		else
				$msg = $db->ErrorMsg();

		if ($bSuccess) {
				if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
						$tmp_dte = $bill_dt;
				else
						$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

//				$objBilling = new Billing($enc_nr, $tmp_dte);
//				$objBilling->getConfinementType();
//				getBilledOps($objResponse, $objBilling);
//				recomputeBilling($objResponse, $objBilling, OP_AREA);
//				getTotalBillAmnt($objResponse, $objBilling);
				$_SESSION['op_done'] = false;
				$objResponse->call("toggleBillAreaStatus", 'OP', 1);
		}
		else
				$objResponse->alert("ERROR: ".$msg);

		return $objResponse;
}

function getParentEncounterNr($enc_nr) {
	global $db;

	$parent_encnr = '';
	$strSQL = "select parent_encounter_nr
							from care_encounter
							where encounter_nr = '$enc_nr'";
	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			$row = $result->FetchRow();
			$parent_encnr = $row['parent_encounter_nr'];
		}
	}
	return $parent_encnr;
}

function delMiscChrg($enc_nr, $bill_dt, $bill_frmdte, $misc_code) {
		global $db;

		$bSuccess = false;
		$objResponse = new xajaxResponse();

		$strSQL = "select * from seg_misc_chrg_details ".
							"   where service_code = '".$misc_code."' and exists (select * from seg_misc_chrg as smc where smc.refno = seg_misc_chrg_details.refno ".
							"      and (smc.encounter_nr = '$enc_nr' or smc.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
							"      and smc.chrge_dte >= '".$bill_frmdte."') ".
							"      and get_lock('smcd_lock', 10) ".
							"   order by entry_no desc limit 1";
		$rs = $db->Execute($strSQL);
		if ($rs) {
				$row = $rs->FetchRow();
				if ($row) {
						$refno = $row['refno'];
						$entryno = $row['entry_no'];

		//				$db->StartTrans();

						$db->LogSQL();

		//				$strSQL = "delete from seg_misc_chrg_details ".
		//						"   where service_code = '".$misc_code."' and exists (select * from seg_misc_chrg as smc where smc.refno = seg_misc_chrg_details.refno ".
		//									"      and (smc.encounter_nr = '$enc_nr' or smc.encounter_nr = (select parent_encounter_nr from care_encounter as ce2 where ce2.encounter_nr = '$enc_nr')) ".
		//									"      and smc.chrge_dte >= '".$bill_frmdte."') ".
		//									"   order by entry_no desc limit 1";

						$strSQL = "delete from seg_misc_chrg_details where refno = '$refno' and entry_no = $entryno and service_code = '$misc_code'";
						$bSuccess = $db->Execute($strSQL);

						$strSQL = "select RELEASE_LOCK('smcd_lock')";
						$db->Execute($strSQL);

						if ($bSuccess) {
								// Delete this header if already without details ...
 								$dcount = 0;
 								$strSQL = "select count(*) dcount from seg_misc_chrg_details where refno = '$refno'";
								$rs = $db->Execute($strSQL);
								if ($rs) {
										$row = $rs->FetchRow();
										$dcount = ($row) ? $row['dcount'] : 0;
										if ($dcount == 0) {
												$strSQL = "delete from seg_misc_chrg WHERE refno = '$refno'";
												$db->Execute($strSQL);
										}
								}
						}
						else
							$msg = $db->ErrorMsg();

						$db->LogSQL(false);

		//				if (!$bSuccess) {
		//						$msg = $db->ErrorMsg();
		//						$db->FailTrans();
		//				}
		//				$db->CompleteTrans();
				}
		}
		else
				$msg = $db->ErrorMsg();

		if ($bSuccess) {
				if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
						$tmp_dte = $bill_dt;
				else
						$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

//				$objBilling = new Billing($enc_nr, $tmp_dte);
//				$objBilling->getConfinementType();
//				getMiscellaneous($objResponse, $objBilling);
//				recomputeBilling($objResponse, $objBilling, XC_AREA);
//				getTotalBillAmnt($objResponse, $objBilling);
				$_SESSION['xc_done'] = false;
				$objResponse->call("toggleBillAreaStatus", 'XC', 1);
		}
		else
				$objResponse->alert("ERROR: ".$msg);

		return $objResponse;
}

function delMiscService($enc_nr, $bill_dt, $bill_frmdte, $srvc_code) {
		global $db;

		$bSuccess = false;
		$objResponse = new xajaxResponse();

		$parent_encnr = getParentEncounterNr($enc_nr);
		if ($parent_encnr != '')
			$encounter = "('$parent_encnr','$enc_nr')";
		else
			$encounter = "('$enc_nr')";

        # Fix for MS-535 by Bong
		$strSQL = "select * from seg_misc_service_details ".
							"   where service_code = '$srvc_code' ".
							"      and exists (select * from seg_misc_service as sms where sms.refno = seg_misc_service_details.refno ".
							"                     and !is_cash and sms.encounter_nr in $encounter and sms.chrge_dte >= '".$bill_frmdte."')
							       and get_lock('sms_lock', 10) ".
							"   order by entry_no desc limit 1 for update";
		$rs = $db->Execute($strSQL);
		if ($rs) {
			if ($row = $rs->FetchRow()) {
					$refno = $row['refno'];
					$entryno = $row['entry_no'];
//			$db->StartTrans();
					$strSQL = "delete from seg_misc_service_details where service_code = '$srvc_code' and entry_no = $entryno and refno = '$refno'";
//					$strSQL = "delete from seg_misc_service_details ".
//										"   where service_code = '".$srvc_code."' and exists (select * from seg_misc_service as sms where sms.refno = seg_misc_service_details.refno ".
//										"      and sms.encounter_nr in $encounter and sms.chrge_dte >= '".$bill_frmdte."') ".
//										"   order by entry_no desc limit 1";
					$bSuccess = $db->Execute($strSQL);

					$strSQL = "select RELEASE_LOCK('sms_lock')";
					$db->Execute($strSQL);

					if ($bSuccess) {
							// Delete any header without details ...
							$dcount = 0;
							$strSQL = "select count(*) dcount from seg_misc_service_details where refno = '$refno'";
							$rs = $db->Execute($strSQL);
							if ($rs) {
								$row = $rs->FetchRow();
								if ($row) {
									$dcount = is_null($row['dcount']) ? 0 : $row['dcount'];
								}
								if ($dcount == 0) {
										$strSQL = "DELETE FROM seg_misc_service WHERE refno = '$refno'";
										$db->Execute($strSQL);
								}
							}
					}
					else
							$msg = $db->ErrorMsg();

//			if (!$bSuccess) {
//					$msg = $db->ErrorMsg();
//					$db->FailTrans();
//			}
//			$db->CompleteTrans();
			}
		}
		else
			$msg = $db->ErrorMsg();

		if ($bSuccess) {
				if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
						$tmp_dte = $bill_dt;
				else
						$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

//				$objBilling = new Billing($enc_nr, $tmp_dte);
//				$objBilling->getConfinementType();
//				HSLaboratory($objResponse, $objBilling);
//				recomputeBilling($objResponse, $objBilling, HS_AREA);
//				getTotalBillAmnt($objResponse, $objBilling);
				$_SESSION['computeflag'] = true;
				$_SESSION['hs_done'] = false;
				$objResponse->call("toggleBillAreaStatus", 'HS', 1);
		}
		else
				$objResponse->alert("ERROR: ".$msg);

		return $objResponse;
}

function delSupply($enc_nr, $bill_dt, $bill_frmdte, $srvc_code, $bill_area) {
		global $db;

		$bSuccess = false;
		$objResponse = new xajaxResponse();

		$parent_encnr = getParentEncounterNr($enc_nr);
		if ($parent_encnr != '')
			$encounter = "('$parent_encnr','$enc_nr')";
		else
			$encounter = "('$enc_nr')";

		$strSQL = "select * from seg_more_phorder_details ".
						  "   where bestellnum = '".$srvc_code."' and exists (select * from seg_more_phorder as sms where sms.refno = seg_more_phorder_details.refno ".
						  "      and sms.encounter_nr in $encounter and sms.chrge_dte >= '".$bill_frmdte."') ".
							"      and get_lock('smpo_lock', 10) ".
							"   order by entry_no desc limit 1 for update";
		$rs = $db->Execute($strSQL);
		if ($rs) {
			$row = $rs->FetchRow();
			if ($row) {
					$refno = $row['refno'];
					$entryno = $row['entry_no'];

		//			$db->StartTrans();

		//			$strSQL = "delete from seg_more_phorder_details ".
		//								"   where bestellnum = '".$srvc_code."' and exists (select * from seg_more_phorder as sms where sms.refno = seg_more_phorder_details.refno ".
		//								"         and sms.encounter_nr in $encounter and sms.chrge_dte >= '".$bill_frmdte."') ".
		//								"      order by entry_no desc limit 1";

					$strSQL = "delete from seg_more_phorder_details where refno = '$refno' and entry_no = $entryno and bestellnum = '$srvc_code'";
					$bSuccess = $db->Execute($strSQL);

					$strSQL = "select RELEASE_LOCK('smpo_lock')";
					$db->Execute($strSQL);

					if ($bSuccess) {
							// Delete this header if already without details ...
							$dcount = 0;
							$strSQL = "select count(*) dcount from seg_more_phorder_details where refno = '$refno'";
							$rs = $db->Execute($strSQL);
							if ($rs) {
								$row = $rs->FetchRow();
								$dcount = ($row) ? $row['dcount'] : 0;
								if ($dcount == 0) {
										$strSQL = "delete from seg_more_phorder WHERE refno = '$refno'";
										$db->Execute($strSQL);
								}
							}
					}
					else
						$msg = $db->ErrorMsg();

		//			if (!$bSuccess) {
		//					$msg = $db->ErrorMsg();
		//					$db->FailTrans();
		//			}
		//			$db->CompleteTrans();

			}
		}
		else
			$msg = $db->ErrorMsg();

		if ($bSuccess) {
				if (strcmp($bill_dt, "0000-00-00 00:00:00") != 0)
						$tmp_dte = $bill_dt;
				else
						$tmp_dte = strftime("%Y-%m-%d %H:%M:%S");

//				$objBilling = new Billing($enc_nr, $tmp_dte);
//				$objBilling->getConfinementType();
				$_SESSION['computeflag'] = true;
				if (strcmp($bill_area, 'MS') == 0) {
//						Medicines($objResponse, $objBilling);
//						recomputeBilling($objResponse, $objBilling, MD_AREA);
					$_SESSION['md_done'] = false;
					$objResponse->call("toggleBillAreaStatus", 'MD', 1);
				}
				else {
//						HSLaboratory($objResponse, $objBilling);
//						recomputeBilling($objResponse, $objBilling, HS_AREA);
					$_SESSION['hs_done'] = false;
					$objResponse->call("toggleBillAreaStatus", 'HS', 1);
				}
//				getTotalBillAmnt($objResponse, $objBilling);
		}
		else
				$objResponse->alert("ERROR: ".$msg);

		return $objResponse;
}

function getDiscount(&$objResponse, &$objBilling){
		//$totalDiscount = number_format($objBilling->getTotalDiscount(), 2, '.', ',');
        //edited by jasper 04/24/2013 AS REQUESTED BY BILLING TO HIDE DISCOUNTS FROM CLASSIFICATION
        //discount is applied only for those charity patient without PHIC
        //added by jasper 05/21/2013 FIX FOR PATIENTS WITH PHIC AUTOMATIC EXCESS
        //if ($objBilling->isCharity() && !$objBilling->isMedicoLegal() && !$objBilling->isPHIC()) {
            //$totalDiscount = $objBilling->getTotalDiscount();
        //} else {
            $totalDiscount = 0;
        //}
        //$totalDiscount = $objBilling->getTotalDiscount();
        //$objResponse->alert($totalDiscount);
        
        //edited by jasper 04/16/2013
        if (!$objBilling->isSponsoredMember() || !$objBilling->checkIfPHS() || !$objBilling->isHSM()) {
		    $objResponse->call("getDiscount", number_format($totalDiscount, 2, '.', ','));
        }else{
            $objResponse->call("getDiscount", 0.00);
        }
        return $totalDiscount;
}// end of function getDiscount

function getPreviousPayment(&$objResponse, &$objBilling){
		//added by jasper 10/03/2013 FIX FOR MS-784 OBANNEX COPAYMENTS
		$deposit = number_format($objBilling->getPreviousPayments() + $objBilling->getOBAnnexPayment(), 2, '.', ',');
		//added by jasper 10/03/2013 FIX FOR MS-784 OBANNEX COPAYMENTS
		$objResponse->call("getPreviousPayment", $deposit);
}// end of function getPreviousPayment

function getTotalBillAmnt(&$objResponse, &$objBilling, &$discountAmt=0) {
//		$totalBillAmnt = $objBilling->getRoundedTotalBillAmount();
		$totalBillAmnt = $objBilling->getTotalBillAmount();

		$acc_confine_coverage = round($objBilling->acc_confine_coverage, 2);
//    $med_confine_coverage = round($objBilling->med_confine_coverage, 0);
		$med_confine_coverage = round($objBilling->getAppliedMedsCoverage(), 2);
		$sup_confine_coverage = round($objBilling->sup_confine_coverage, 2);
		$ms_confine_coverage = $med_confine_coverage + $sup_confine_coverage;
//    $srv_confine_coverage = round($objBilling->srv_confine_coverage, 0);
		$srv_confine_coverage = round($objBilling->getAppliedHSCoverage(), 2);
		$ops_confine_coverage = round($objBilling->ops_confine_coverage, 2);

		$pfs_confine_converage_d1 = round($objBilling->pfs_confine_coverage['D1'], 2);
		$pfs_confine_converage_d2 = round($objBilling->pfs_confine_coverage['D2'], 2);
		$pfs_confine_converage_d3 = round($objBilling->pfs_confine_coverage['D3'], 2);
		$pfs_confine_converage_d4 = round($objBilling->pfs_confine_coverage['D4'], 2);
		$pf_confine_coverage = $pfs_confine_converage_d1 + $pfs_confine_converage_d2 + $pfs_confine_converage_d3 + $pfs_confine_converage_d4;
		$msc_confine_coverage = round($objBilling->getMscConfineCoverage(), 2);
		//added by jasper 10/03/2013 FIX FOR MS-784 OBANNEX COPAYMENTS		
		$previous_payment = round($objBilling->getPreviousPayments() + $objBilling->getOBAnnexPayment(), 2);
		//added by jasper 10/03/2013 FIX FOR MS-784 OBANNEX COPAYMENTS
		$total_discount = 0;
        //added by jasper 09/12/2013 - FIX FOR BUG#120 PATIENTS WITH SENIOR CITIZEN DISCOUNT AND PAYWARD
        if ((!$objBilling->isCharity() && $objBilling->iswithSCDiscount()) || (!$objBilling->isPHIC() && !$objBilling->isMedicoLegal())) { 
            $total_discount_acc = round($objBilling->getBillAreaDiscount('AC'), 2);
            $total_discount_hs = round($objBilling->getBillAreaDiscount('HS'), 2);
            $total_discount_ms = round($objBilling->getBillAreaDiscount('MS','M'), 2);
            $total_discount_ms += round($objBilling->getBillAreaDiscount('MS','S'), 2);
            $total_discount_or = round($objBilling->getBillAreaDiscount('OR'), 2);
            $total_discount_xc = round($objBilling->getBillAreaDiscount('XC'), 2);

		$pf_benefits = $objBilling->getPFBenefits();
        $prevrole_area = '';
		if (!empty($pf_benefits) && is_array($pf_benefits)) {
				foreach ($pf_benefits as $key=> $value) {
                if ($value->role_area == $prevrole_area) continue;
                $prevrole_area = $value->role_area;
                            //$total_discount += round($objBilling->getBillAreaDiscount($value->role_area), 2);
                            //added by jasper 07/24/2013
                            //if (!$objBilling->isPHIC() && $objBilling->isOBAnnex() && $value->role_area=='D3') {
                            //    $total_discount_pf += 0;
                            //} else {
                                $total_discount_pf += round($objBilling->getBillAreaDiscount($value->role_area), 2);
                            //}
                            //added by jasper 07/24/2013       
				}
		}
            $total_discount = $total_discount_acc + $total_discount_hs + $total_discount_ms + $total_discount_or + $total_discount_xc + $total_discount_pf;
        }   
        //10/02/2013 FIX FOR BUG#302
        $objBilling->getProfFeesList();
        $total_discount = $total_discount - $objBilling->nonDiscountablePF;
        //added by jasper 05/21/2013 FIX FOR AUTOMATIC EXCESS FOR PHIC PATIENTS
        /*if ($objBilling->isPHIC() || $objBilling->isMedicoLegal()) {
            $total_discount = 0.00;
        }*/
        
        if ($objBilling->isPHIC()) {
            //adjust ACCOMMODATION coverage from SC discount
            if ($objBilling->total_acc_charge - $total_discount_acc <= $acc_confine_coverage) {
                $acc_confine_coverage = $objBilling->total_acc_charge - $total_discount_acc;
        }

            //adjust XLC coverage from SC discount
            $total_service_charge = $objBilling->getTotalSrvCharge();
            if ($total_service_charge - $total_discount_hs <= $srv_confine_coverage) {
                $srv_confine_coverage = $total_service_charge - $total_discount_hs;
            }
            
            //adjust Drugs and Meds coverage from SC discount
            $ms_confine_coverage = $med_confine_coverage + $sup_confine_coverage;
            if ($objBilling->total_med_charge - $total_discount_ms <= $ms_confine_coverage) {
                $ms_confine_coverage = $objBilling->total_med_charge - $total_discount_ms;
            }

            //adjust OPS coverage from SC discount
            if ($objBilling->total_op_charge - $total_discount_or <= $ops_confine_coverage) {
                $ops_confine_coverage = $objBilling->total_op_charge - $total_discount_or;
            }

            //adjust PF coverage from SC discount
            $pf_confine_coverage = $pfs_confine_converage_d1 + $pfs_confine_converage_d2 + $pfs_confine_converage_d3 + $pfs_confine_converage_d4;
            if ($objBilling->total_pf_charge - $total_discount_pf <= $pf_confine_coverage) {
                $pf_confine_coverage = $objBilling->total_pf_charge - $total_discount_pf;
            }
            
            //adjust Misc coverage from SC discount
            if ($objBilling->total_misc_charge - $total_discount_xc <= $msc_confine_coverage) {
                $msc_confine_coverage = $objBilling->total_misc_charge - $total_discount_xc;
            }
        }
        //added by jasper 09/12/2013 - FIX FOR BUG#120 PATIENTS WITH SENIOR CITIZEN DISCOUNT AND PAYWARD
        
		$disc_classification = 0;//round($objBilling->getTotalDiscount(), 2);
        $total_coverage = $acc_confine_coverage + $ms_confine_coverage + $srv_confine_coverage + $ops_confine_coverage + $pf_confine_coverage + $msc_confine_coverage;
        //$netTotal =  ($totalBillAmnt - $total_discount - $acc_confine_coverage -  $med_confine_coverage - $sup_confine_coverage - $srv_confine_coverage -  $ops_confine_coverage - $pfs_confine_converage_d1 - $pfs_confine_converage_d2 - $pfs_confine_converage_d3 - $pfs_confine_converage_d4 - $msc_confine_coverage - $previous_payment);
        $netTotal =  ($totalBillAmnt - $total_discount - $total_coverage - $previous_payment);

        //removed by jasper 04/08/2013
        //$billTotalAmnt = number_format(round($netTotal - $disc_classification), 2, '.', ',');
        $billTotalAmnt = round($netTotal - $disc_classification, 2);

        //added by jasper 04/04/2013
        $objbillinfo = new BillInfo();
        $tot_prevbill_amt = 0;
        $result = $objbillinfo->getPreviousBillAmt($objBilling->current_enr, $objBilling->old_bill_nr);
        if ($result) {
            while ($row = $result->FetchRow()) {
                $n_bill = 0;
                if (!empty($row["total_charge"])) $n_bill = $row["total_charge"];
                if (!empty($row["total_coverage"])) $n_bill -= $row["total_coverage"];
                if (!empty($row["total_computed_discount"])) $n_bill -= $row["total_computed_discount"];
                //removed by jasper 05/09/2013 - NOT TO SHOW DISCOUNT ON PREVIOUS BILL
                //if (!empty($row["total_discount"]) && ($n_bill > 0)) $n_bill -= ($n_bill * $row["total_discount"]);
                //removed by jasper 05/09/2013
                $tot_prevbill_amt += $n_bill;
            }
            $objResponse->call("assignPreviousBilledAmount", number_format($tot_prevbill_amt, 2, '.', ','));
        }
        //added by jasper 04/04/2013
        //$objResponse->alert($billTotalAmnt . " " . $tot_prevbill_amt);
        //added by jasper 04/01/2013
        $objResponse->call("showPreviousBilledAmount");
        if ($objBilling->isSponsoredMember() || $objBilling->checkIfPHS()) {
            //$objBilling->excess = $billTotalAmnt + $tot_prevbill_amt; //+ $discountAmt;
            $objBilling->excess = $billTotalAmnt + $tot_prevbill_amt + $discountAmt;
            $objResponse->call(($objBilling->isSponsoredMember() ? "showNoBalanceBilling" : "showInfirmaryDiscount"), number_format($billTotalAmnt + $tot_prevbill_amt + $discountAmt, 2, '.', ','));
            //$objResponse->call("showNoBalanceBilling", number_format($billTotalAmnt + $tot_prevbill_amt + $discountAmt, 2, '.', ','));
            //$objResponse->call("showNoBalanceBilling", number_format($billTotalAmnt + $tot_prevbill_amt, 2, '.', ','));
            $objResponse->call((!$objBilling->isSponsoredMember() ? "showNoBalanceBilling" : "showInfirmaryDiscount"), 0);
            $objResponse->call("getTotalBillAmnt",  '0.00');
        } elseif ($objBilling->isHSM()){
        	$objBilling->excess = $billTotalAmnt + $tot_prevbill_amt + $discountAmt;
        	$objResponse->call("showPointOfCare", number_format($billTotalAmnt + $tot_prevbill_amt + $discountAmt, 2, '.', ','));
           	$objResponse->call("getTotalBillAmnt",  '0.00');

        } else{
            $objResponse->call("showNoBalanceBilling", 0.00);
            $objResponse->call("showInfirmaryDiscount", 0.00);
            $objResponse->call("getTotalBillAmnt",  number_format($billTotalAmnt + $tot_prevbill_amt, 2, '.', ','));
        }
        //added by jasper 04/01/2013
        //removed by jasper 04/01/2013
		//$objResponse->call("getTotalBillAmnt",  $billTotalAmnt);
		$objResponse->call("toggleLastPartStatus", 0);
}//end of function getTotalBillAmnt ;

function saveThisBilling($encounter_nr, $bill_date = "0000-00-00 00:00:00", $pid, $deathdate) {
		$objResponse = new xajaxResponse();
//		$objBilling = new Billing($encounter_nr, $bill_date);
		$objBilling = unserialize($_SESSION['billobject']['main']);
		if (is_object($objBilling)) {
            if($deathdate!=''){
                setDeathDate($objResponse,$pid,$encounter_nr,$deathdate);
            }
//-------------------------------------------------------------------   fix for HISSPMC-115
//			$ac_obj = unserialize($_SESSION['billobject']['ac']);
//			$md_obj = unserialize($_SESSION['billobject']['md']);
//			$hs_obj = unserialize($_SESSION['billobject']['hs']);
//			$op_obj = unserialize($_SESSION['billobject']['op']);
//			$pf_obj = unserialize($_SESSION['billobject']['pf']);
//			$xc_obj = unserialize($_SESSION['billobject']['xc']);

//			if (is_object($ac_obj))
//				$ac_obj->assignBillObject($objBilling);
//			if (is_object($md_obj))
//				$md_obj->assignBillObject($objBilling);
//			if (is_object($hs_obj))
//				$hs_obj->assignBillObject($objBilling);
//			if (is_object($op_obj))
//				$op_obj->assignBillObject($objBilling);
//			if (is_object($pf_obj))
//				$pf_obj->assignBillObject($objBilling);
//			if (is_object($xc_obj))
//				$xc_obj->assignBillObject($objBilling);

//		$objBilling->getConfinementType();

		// Accommodation
//		$objBilling->getAccommodationHist();                 // get AccommodationHist
//		$objBilling->getRoomTypeBenefits();                 // get Room type Benefits
//		$objBilling->getConfineBenefits('AC');

		// Hospital services
//		$objBilling->getServicesList();
//		$objBilling->getServiceBenefits();
//		$objBilling->getConfineBenefits('HS');

		// Medicines
//		$objBilling->getMedicinesList();                     //gather all medicines consumed
//		$objBilling->getMedicineBenefits();
//		$objBilling->getConfineBenefits('MS', 'M');

		// Supplies
//    $objBilling->getSuppliesList();                     // gather all supplies consumed
//    $objBilling->getSupplyBenefits();
//    $objBilling->getConfineBenefits('MS', 'S');

		// Professional Fees (Doctors' Fees)
//		$objBilling->getProfFeesList();
//		$objBilling->getProfFeesBenefits();

//		$hsp_pfs_benefits = $objBilling->hsp_pfs_benefits; //role area
//		if (is_array($hsp_pfs_benefits) && (count($hsp_pfs_benefits) > 0)) {
//				foreach($hsp_pfs_benefits as $key=>$value)
//						$objBilling->getConfineBenefits($value->role_area, '', $value->getRoleLevel());
//		}

		// Operation (Procedures)
//		$objBilling->getOpsList();
//		$objBilling->getOpBenefits();
//		$objBilling->getConfineBenefits('OR');

		// Miscellaneous
//		$objBilling->getMiscellaneousBenefits();
//		$objBilling->getConfineBenefits('XC');

		// Previous payments
//		$objBilling->getPreviousPayments();

		// Discounts
//		$objBilling->getTotalDiscount();

//-------------------------------------------------------------------       fix for HISSPMC-115
			if ($objBilling->saveBilling()) {
                    //edited by jasper 04/03/2013
                    $objResponse->Call("closeSaveIndicator");
					//$objResponse->addScriptCall("closeSaveIndicator");
                    //$objResponse->alert($objBilling->getBillNo());
                    //$objResponse->alert($objBilling->debugSQL);
					$objResponse->alert("Successfully saved billing for encounter ". $encounter_nr. "!");
                    $objResponse->Call("setBillNo", $objBilling->getBillNo());
                    //removed by jasper 04/03/2013
					//$formatted_bill_dte = strftime("%b %d, %Y %I:%M%p");
					//$objResponse->call("clearBillingHeaderInfo", date("Y-m-d H:i:s"), $formatted_bill_dte);
			}
			else {
					$objResponse->call("closeSaveIndicator");
					$objResponse->alert($objBilling->errmsg);
			}
		}                                                                      // fix for HISSPMC-115
		else                                                                   // fix for HISSPMC-115
			$objResponse->alert("Please retry saving bill!");                    // fix for HISSPMC-115

		return $objResponse;
}

function updateRVUTotal($ops, $enc_nr, $bill_dt, $casetyp) {
		$objResponse = new xajaxResponse();

		$opsarr = explode("#",$ops);

		$nrvu = 0;
		$nmultiplier = 0;
		foreach ($opsarr as $k => $row) {
				$v = explode(";", $row);
				$nrvu += $v[3];
				$nmultiplier = $v[4];
				$target      = $v[5];
		}

	if (strcmp($target, 'dr') != 0) $objResponse->call("applyRVUandMult", 0, 0, 0);
	if (strcmp($target, 'dr') != 0) {

//        $objResponse->alert("Encounter = ".$enc_nr."; Bill date = ".$bill_dt."; RVU = ".$nrvu."; Case Type = ".$casetyp);

		$ncharge = getOPCharge($enc_nr, $bill_dt, $nrvu, $casetyp);
		$objResponse->call("applyRVUandMult", $nrvu, $nmultiplier, $ncharge);
	}

	return $objResponse;
}
//added by pol 5/21/2013
function ShowMedicoCases($enc_nr){
	global $db;

	$sql_mc = "SELECT fn_get_medico_legal(".$enc_nr.") AS medico_cases";
	$medicoCase = $db->getOne($sql_mc);

return $medicoCase;
}
//end by pol

//added by pol 7/24/2013
function GetPhicNumber($enc_nr){
	global $db;

	$phic_nr = $db->GetOne("SELECT fn_get_phic_number('".$enc_nr."') AS `phic_nr`");

return $phic_nr;
}
//end by pol

function getOPCharge($enc_nr, $bill_dt, $nrvu, $casetyp) {
	global $db;

	$ncharge = 0;

	$strSQL = "select fn_getORCharge('{$enc_nr}', date('{$bill_dt}'), {$nrvu}, {$casetyp}) as opcharge";
	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			if ($row = $result->FetchRow()) {
				$ncharge = $row["opcharge"];
			}
		}
	}

	return $ncharge;
}

function populatePkgCbo($pkg_id = '', $enc_nr = '', $bill_dt = '0000-00-00 00:00:00') {
	global $db;

	$objResponse = new xajaxResponse();

	if ($enc_nr != '') {
		$strSQL = "select sp.package_id, package_name                                                                         \n
						from seg_hcare_packages as sp                                                                           \n
						 inner join (select * from seg_hcare_bsked as shb                                                     \n
										where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '$bill_dt'               \n
											 and (shb.basis & 16)                                                               \n
											 and (select max(effectvty_dte) as latest                                           \n
													from seg_hcare_bsked as shb2                                                \n
													where shb2.hcare_id = shb.hcare_id                                          \n
													 and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte) as bs on bs.bsked_id = sp.bsked_id   \n
						 inner join seg_encounter_insurance as si on si.hcare_id = bs.hcare_id                                \n
						 inner join seg_packages as spm on sp.package_id = spm.package_id                                     \n
						where si.encounter_nr = '$enc_nr'                                                                       \n
						order by priority, bs.effectvty_dte desc";
	}
	else {
		$strSQL = "select *             \n
					 from seg_packages  \n
					 order by package_name";
	}

	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			$options = "<option value=\"\">- Select Package -</option>\n";

			while ($row = $result->FetchRow()) {
				$checked = ($pkg_id == $row["package_id"]) ? ' selected="selected"' : "";
				$options .= "<option value=\"".$row['package_id']."\" $checked>".$row['package_name']."</option>\n";
			}

			$objResponse->call("assignPkgOption", $options);
		}
	}

		return $objResponse;
}
   //added by pol
function checkPHIC($enc) {
	global $db;

	$objResponse = new xajaxResponse();
	/*$PhicSql =  "SELECT cpi.`insurance_nr`
				FROM care_person_insurance `cpi`
				INNER JOIN care_encounter `ce`
				ON cpi.`pid` = ce.`pid`
				WHERE ce.`encounter_nr` =".$enc;*/
    //edited by jasper 07/11/2013
    $PhicSql = "SELECT cpi.insurance_nr FROM care_person_insurance AS cpi " .
               "INNER JOIN care_encounter AS ce ON cpi.pid = ce.pid " .
               "WHERE EXISTS (SELECT encounter_nr FROM seg_encounter_insurance WHERE encounter_nr = '" . $enc . "') AND ce.encounter_nr = '" .$enc . "'";
	$PhicSuccess = $db->Execute($PhicSql);
	while ($row = $PhicSuccess->FetchRow())
		$PHIC_nr = $row["insurance_nr"];

	if (is_null($PHIC_nr))
	{
		$objResponse->alert("This Patient has no PHIC no.");
	}
	else
	{
		$objResponse->call("showCategoryPrompt");
	}

	return $objResponse;
}


function toggleMGH($enc_nr, $mgh_date = "0000-00-00 00:00:00", $bsetMGH = 0) {
	global $db;

	$objResponse = new xajaxResponse();

	if (strcmp($mgh_date, "0000-00-00 00:00:00") != 0)
		$mgh_date = "'".strftime("%Y-%m-%d %H:%M:%S", strtotime($mgh_date))."'";
        //removed by jasper 06/05/2013 FIX FOR MGH DATE
        //$mgh_date = "'".strftime("%Y-%m-%d %H:%M:%S", strtotime("-1 second", strtotime($mgh_date)))."'";
	else
		$mgh_date = "NULL";

	$strSQL = "UPDATE care_encounter SET
								is_maygohome = $bsetMGH,
								mgh_setdte   = $mgh_date
						 WHERE encounter_nr = '$enc_nr'
								and is_maygohome = ".(($bsetMGH == 1) ? 0 : 1);
	$bSuccess = $db->Execute($strSQL);
	if (!$bSuccess) {
		$objResponse->alert($db->ErrorMsg());
	}

	return $objResponse;
}

function showPkgCoveredAmount($pkg_id = '', $enc_nr = '', $bill_dt = '0000-00-00 00:00:00') {
	global $db;

	$objResponse = new xajaxResponse();

	$total = 0;
	$strSQL = "select sp.amountlimit                                                                                      \n
					from seg_hcare_packages as sp                                                                           \n
					 inner join (select * from seg_hcare_bsked as shb                                                     \n
									where str_to_date(shb.effectvty_dte, '%Y-%m-%d %H:%i:%s') <= '$bill_dt'               \n
										 and (shb.basis & 16)                                                               \n
										 and (select max(effectvty_dte) as latest                                           \n
												from seg_hcare_bsked as shb2                                                \n
												where shb2.hcare_id = shb.hcare_id                                          \n
												 and shb2.benefit_id = shb.benefit_id) = shb.effectvty_dte) as bs on bs.bsked_id = sp.bsked_id   \n
					 inner join seg_encounter_insurance as si on si.hcare_id = bs.hcare_id                                \n
					 inner join seg_packages as spm on sp.package_id = spm.package_id                                     \n
					where si.encounter_nr = '$enc_nr' and sp.package_id = $pkg_id                                           \n
					order by priority, bs.effectvty_dte desc";
	if ($result = $db->Execute($strSQL)) {
		if ($result->RecordCount()) {
			while ($row = $result->FetchRow())
				$total += (is_null($row['amountlimit'])) ? 0 : $row['amountlimit'];
		}
	}

	$objResponse->call("showPkgCoveredAmount", $total);

	return $objResponse;
}

function removePkgDist($enc_nr) {
	global $db;

	$objResponse = new xajaxResponse();

	$db->BeginTrans();

	$strSQL = "delete from seg_billing_pkg where ref_no = 'T{$enc_nr}'";
	$bSuccess = $db->Execute($strSQL);

	if ($bSuccess) {
		$strSQL = "delete from seg_applied_pkgcoverage where ref_no = 'T{$enc_nr}'";
		$bSuccess = $db->Execute($strSQL);

		if ($bSuccess) $objResponse->call("js_Recalculate");
	}

	if ($bSuccess)
		$db->CommitTrans();
	else
		$db->RollbackTrans();

	return $objResponse;
}

function removeCoverageAdjustments($enc_nr) {
	global $db;

	$objResponse = new xajaxResponse();

	$db->BeginTrans();

	$strSQL = "delete from seg_billingcoverage_adjustment where ref_no = 'T{$enc_nr}'";
	$bSuccess = $db->Execute($strSQL);
	if ($bSuccess) {
		$db->CommitTrans();
		$objResponse->call("setCoverageAdjustedFlag", false);
		$objResponse->call("js_Recalculate");
	}
	else
		$db->RollbackTrans();

	return $objResponse;
}

#Added by Jarel 05/18/2013
function setDeathDate(&$objResponse, $pid, $enc = '0', $deathdate = "0000-00-00 00:00:00"){
    global $db, $HTTP_SESSION_VARS;

    //$objResponse = new xajaxResponse();
    $db->BeginTrans();

    $userid = $HTTP_SESSION_VARS['sess_user_name'];
    $strSQL = "UPDATE care_person SET
                                death_date = DATE_FORMAT('$deathdate', '%Y-%m-%d'),
                                death_time = DATE_FORMAT('$deathdate', '%H:%i:%s'),
                                history = CONCAT(history,'Update: ',NOW(),' [$userid]\\n'),
                                modify_id = '$userid',
                                modify_time = NOW(),
                                death_encounter_nr = ".$db->qstr($enc)."
                         WHERE pid = ".$db->qstr($pid);
    $bSuccess1 = $db->Execute($strSQL);

    if($bSuccess1){

      $fldarray = array('encounter_nr' => $db->qstr($enc),
                'result_code'  => '4',
                'modify_id'    => $db->qstr($userid),
                'modify_time'    => 'NOW()',
                'create_id'    => $db->qstr($userid),
                'create_time'    => 'NOW()'
               );

      $bSuccess2 = $db->Replace('seg_encounter_result', $fldarray, array('encounter_nr'));
    }

    if (!$bSuccess1 || !$bSuccess2) {
    	$db->RollbackTrans();
        $objResponse->alert($db->ErrorMsg());
    }else{
    	$db->CommitTrans();
    }

    //return $objResponse;
}


#Added by Jarel 05/18/2013
function UnsetDeathDate($pid, $enc = '0', $deathdate = "0000-00-00 00:00:00"){
    global $db, $HTTP_SESSION_VARS;

    $objResponse = new xajaxResponse();

    $userid = $HTTP_SESSION_VARS['sess_user_name'];
    
    $sql = "SELECT * FROM care_person WHERE death_encounter_nr<>'0' AND pid =".$pid;
    if ($result=$db->Execute($sql)){
        if ($count = $result->RecordCount()){
        		$db->BeginTrans();

                $strSQL =   "UPDATE care_person SET
                                death_date = DATE_FORMAT('$deathdate', '%Y-%m-%d'),
                                death_time = DATE_FORMAT('$deathdate', '%H:%i:%s'),
                                history = CONCAT(history,'Update: ',NOW(),' [$userid]\\n'),
                                modify_id = '$userid',
                                modify_time = NOW(),
                                death_encounter_nr = '0'
                            WHERE pid = ".$pid;
                $bSuccess1 = $db->Execute($strSQL);

                if($bSuccess1){

                $fldarray = array('encounter_nr' => $db->qstr($enc),
                            'result_code'  => '3',
                            'modify_id'    => $db->qstr($userid),
                            'modify_time'    => 'NOW()',
                            'create_id'    => $db->qstr($userid),
                            'create_time'    => 'NOW()'
                           );

                $bSuccess2 = $db->Replace('seg_encounter_result', $fldarray, array('encounter_nr'));
                }

			    if (!$bSuccess1 || !$bSuccess2) {
			    	$db->RollbackTrans();
			        $objResponse->alert($db->ErrorMsg());
			    }else{
			    	$db->CommitTrans();
			    }
        }else return FALSE;
    }else{return FALSE;}

    return $objResponse;
}

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
	    } else {
		return false;
	    }
	}
}
//added by jasper 07/24/2013 FOR BUGZILLA ID 302


//Added by Jarel 12/12/13 Check if has transaction in Blood bank
function setHasBloodTrans(&$objResponse,$enc)
{
  	
	$labObj = new SegLab();
	$hasbloodborrowed = 0;
	$labObj->hasBloodRequest($enc);
	if ($labObj->count)
	    $hasbloodborrowed = 1;

	$objResponse->call('setHasBloodTrans',$hasbloodborrowed);

}

function getClerks(){
	$objResponse = new xajaxResponse();
	global $db;

	$billing_clerks = array();

	$sql = "SELECT a.personell_nr AS personell_nr,
	               p.name_last AS lname,
	               p.name_first AS fname,
	               p.name_middle AS mname,
  				   cu.login_id AS login_id
			FROM
				  care_personell_assignment AS a,
				  care_personell AS ps,
				  care_person AS p,
				  care_users AS cu
			WHERE (ps.short_id LIKE 'G%') 
  			AND cu.personell_nr = ps.nr
  			AND a.location_nr = 152 
  			AND (a.date_end = '0000-00-00' OR a.date_end >= '".date('Y-m-d')."')
  			AND a.status NOT IN ('deleted','hidden','inactive','void') 
  			AND a.personell_nr = ps.nr 
  			AND ps.pid = p.pid 
			ORDER BY p.name_last,p.name_first,p.name_middle";

	$rs = $db->Execute($sql);
	if($rs){
		if($rs->RecordCount()>0){
			while($row = $rs->FetchRow()){
				$staff_name = mb_strtoupper($row['lname']) . ", " . mb_strtoupper($row['fname']) . mb_strtoupper($row['mname']);
				$staff_id = $row['login_id'];
				array_push($billing_clerks, array($staff_id,$staff_name));
			}
		}else{
			
		}
	}else{
		
	}

	array_unshift($billing_clerks, array("all","ALL"));
	$objResponse->call('setClerks',$billing_clerks);

	return $objResponse;
}

$xajax->processRequest();
?>