<?php
#EDITED BY VANESSA A. SAREN 02-13-08
#EDITED BY LST 06-29-2008, 08-13-2008, 12-03-2008 - Removed getSuppliesData function
require('./roots.php');
require_once($root_path."classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/billing/class_bill_info.php'); //added by jasper 04/08/2013
require_once($root_path.'include/care_api_classes/billing/class_billing.php');
require_once($root_path.'include/care_api_classes/billing/class_billareas.php');

#added by VAN 04-24-2009
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');

define('GEN_COL01', 4);		 	// in mm.
define('GEN_COL02', 2.8); 	 	// in inches.
define('GEN_COL02_D', 8.25);	 	// in mm.
define('GEN_COL02_D2', 11.75);	// in mm.
define('GEN_COL02_D3', GEN_COL02_D2 + 3.5);	// in mm.

define('COL_MID', 2);

define('COL03_WIDTH', 33.5);
define('COL04_WIDTH', 25.972);
define('COL05_WIDTH', 27.972);
define('COL06_WIDTH', 27.972);

define('FOOTER_COL01', 84);
define('FOOTER_COL02', 84);

define('NAME_LEN', 52);
define('DEPT_LEN', 24);

class BillPDF extends FPDF {
	var $encounter_nr;
    var $bill_ref_nr; //added by jasper 01/04/13
    var $prev_bill_amt; //added by jasper 04/08/2013
	var $ishousecase;
	var $isphic;
	var $death_date; //Added by Jarel 05/24/13

	var $DEFAULT_FONTSIZE;
	var $DEFAULT_FONTTYPE;
	var $DEFAULT_FONTSTYLE;

	var $WBORDER;
	var $ALIGNMENT;
	var $NEWLINE;

	var $reportTitle="";

	var $billType;

	var $Data;
	var $pfDaTa;

	var $totalCharge = array(0, 0, 0, 0, 0, 0, 0, 0, 0);
	var $totalDiscount = array(0, 0, 0, 0, 0, 0, 0, 0, 0);
	var $totalCoverage = array(0, 0, 0, 0, 0, 0, 0, 0, 0);
	var $totalExcess = array(0, 0, 0, 0, 0, 0, 0, 0, 0);

	var $personData = array();

	var $objBill; //Billing object

	var $IsDetailed;
	var $bill_date;

	var $head_name;
	var $head_position;

	var $clerk_name;
    var $clerk_italized;

	var $b_acchist_gathered = FALSE;

    var $brecalc = false;

	/*
	 * Constructor
	 * @param string encounter_nr
	 */

	function BillPDF($enc='', $bill_dt = "0000-00-00 00:00:00", $bill_frmdt = "0000-00-00 00:00:00", $old_bill_nr = '', $bcomp=false, $deathdate) {
		 if(!empty($enc)){
			$this->encounter_nr = $enc;
		 }
         //added by jasper 01/04/13
         if (!empty($old_bill_nr)) {
            $this->bill_ref_nr = $old_bill_nr;
         }
		#added by VAN 02-14-08
		 $this->IsDetailed = $_GET['IsDetailed'];
         $this->brecalc = $bcomp;

		 $pg_size = array($this->in2mm(8.5), $this->in2mm(13));                 // Default to long bond paper --- modified by LST - 04.13.2009
		 $this->FPDF("P","mm", $pg_size);
		 $this->AliasNbPages();
		 $this->AddPage("P");
//		 $this->SetTopMargin(1);

		 $this->DEFAULT_FONTTYPE = "Times";
		 $this->DEFAULT_FONTSIZE = 11;
		 $this->DEFAULT_FONTSTYLE = '';
		 $this->NEWLINE = 1;
		 $this->death_date = $deathdate;

		 //Instantiate billing object
         if ($this->brecalc) {
            $this->objBill = new Billing($this->encounter_nr, $bill_dt, $bill_frmdt, $old_bill_nr, $deathdate);
            $this->objBill->applyDiscounts();
         }
         else
            $this->objBill = unserialize($_SESSION['billobject']['main']);         // modified by LST -- 11.04.2010
		 $this->bill_date = $bill_dt;

		 //get first the confinement type
		 $this->objBill->getConfinementType();

         //added by jasper 03/18/2013
         /*if (!($this->objBill->isForFinalBilling())) {
            $this->Image('../../gui/img/logos/tentativebill.jpg',30, 50, 150,150);
         }*/
         //added by jasper 03/18/2013

	}// end of Bill_Pdf

	//Page Header
	#commented by VAN 03-15-08

	function Header() {
		//Display Page title
#----------------------- LST - 06-21-2008
		$objInfo = new Hospital_Admin();
		if ($row = $objInfo->getAllHospitalInfo()) {
			$row['hosp_agency'] = strtoupper($row['hosp_agency']);
			$row['hosp_name']   = strtoupper($row['hosp_name']);
		}
		else {
			$row['hosp_country'] = "Republic of the Philippines";
			$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$row['hosp_name']    = "DAVAO MEDICAL CENTER";
			$row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
		}

		$this->Image('../../gui/img/logos/dmc_logo.jpg',20,10,20,20);

//		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,20,20);
		$this->SetFont("Times", "B", "10");
		$this->Cell(0, 4, $row['hosp_country'], 0, 1,"C");
		$this->Cell(0, 4, $row['hosp_agency'], 0, 1 , "C");
		$this->Cell(0, 4, $row['hosp_name'], 0, 1, "C");

		$this->SetFont("Times", "", "10");
		$this->Cell(0, 4, $row['hosp_addr1'], 0, 1, "C");
#---------------------- LST  - 06-21-2008

	}// end of Bill_Header function

	//Page footer
	function Footer() {
		//Go to 1.5 cm from bottom
		$this->SetY(-15);
		$this->SetFont('Arial','I',8);
		//Page number
		$this->Cell(0, 10, 'Page '.$this->PageNo().' of {nb}',0,0,'C');
	}

	function ReportFooter() {
				#added by VAN 04-24-2009
				$labObj = new SegLab();

		$this->getBillingHead();
		$this->getBillingClerk($_SESSION['sess_temp_userid'],$_GET['encounter_nr'],$_GET['nr']);

        #italized if no save bill yet
        if ($this->clerk_italized)
            $this->SetFont('Arial','I',8);
        else
		    $this->SetFont($this->fontType, $this->fontStyle, $this->fontSize);

		// Signatories ...
		$this->Ln(4);
		$this->Cell(4, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL01, 4, "", "", 0, '');
		$this->Cell(20, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL02, 4, "Prepared by:", "", 1, '');
		$this->Ln(4);
		$this->Cell(4, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL01, 4, '', "", 0, 'C');
		$this->Cell(20, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL02, 4, $this->clerk_name, "", 1, 'C');

		$this->Cell(4, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL01, 4, '', "", 0, 'C');
		$this->Cell(20, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL02, 4, "Billing Clerk", "T", 1, 'C');

		// Confirmation ...
		//added by pol 07/24/2013
		//fix for bug # 308
		$this->Ln(4);
		$this->Cell(4, 4, "", "", 0, '');
		//end by pol
		$this->Ln(4);
		$this->Cell(4, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL01, 4, "", "", 0, '');
		$this->Cell(20, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL02, 4, "Confirmed by:", "", 1, '');



		$this->Ln(5);
		$this->Cell(4, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL01, 4, "", "", 0, '');
		$this->Cell(20, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL02, 4, "Signature over Printed Name/Relationship/Tel.#", "T", 1, 'C');

		$saccom = (!$this->ishousecase) ? strtoupper($this->objBill->getAccommodationDesc()) : "";

		$nypos = $this->GetY();
		//edited by VAN 02-14-2013
        /*if (!(strpos($saccom, "PAYWARD") === false)){
			$this->SetY(-1 * $this->in2mm(2.2));
		}else
			$this->SetY(-1 * $this->in2mm(2));*/
        $this->SetY(-1 * $this->in2mm(1.66));
		$ntmp = $this->GetY();
		if ($nypos >= $ntmp) $this->AddPage("P");

		/*if (!(strpos($saccom, "PAYWARD") === false))
			$this->SetY(-1 * $this->in2mm(2.2));
		else
			$this->SetY(-1 * $this->in2mm(2));*/
        $this->SetY(-1 * $this->in2mm(1.66));

		$this->Cell(0, 1, "", "T", 1, 'C');
		$this->Cell(0, 4, $saccom." PATIENT CLEARANCE", "", 1, 'C');
		$this->Ln(1);

		$this->Cell(4, 2, "", "", 0, '');
		$this->Cell(FOOTER_COL01, 4, "CASE #: ".$this->encounter_nr, "", 0, 'C');
		$this->Cell(20, 2, "", "", 0, '');

//		$row = $this->personData->FetchRow();
		$row = $this->personData;
		$name = strtoupper($row['name_last'].",  ".$row['name_first']." ".$row['name_middle']);
		$this->Cell(FOOTER_COL02, 4, "PATIENT: ".$name, "", 1, 'C');

		$this->Ln(2);

		#edited by VAN 04-24-2009
        #change this that not only with borrowed blood but also all patients with blood request will
        #ask for blood bank clearance
		$hasbloodborrowed = 0;
		$labObj->hasBloodRequest($this->encounter_nr);
		if ($labObj->count)
		    $hasbloodborrowed = 1;

		if ($hasbloodborrowed){
		    $this->Cell(4, 4, "", "", 0, '');
		    $this->Cell(FOOTER_COL01*0.325, 4, "BLOOD BANK: ", "", 0, '');
		    $this->Cell(4, 4, "", "", 0, '');
		    $this->Cell(FOOTER_COL01*0.675, 4, str_repeat('_', 30), "", 0, '');

			$pharmaXval =  8;
			$pharmaNextLine =  1;
			$cashierXval =  4;
			$cashierNextLine =  0;
			$nurseXval =  8;
			$nurseNextLine =  1;
			$billingXval = 4;

        }else{
			$pharmaXval =  4;
			$pharmaNextLine =  0;
			$cashierXval =  8;
			$cashierNextLine =  1;
			$nurseXval =  4;
			$nurseNextLine =  0;
			$billingXval = 8;
		}

		#$this->Cell(8, 4, "", "", 0, '');
		$this->Cell($pharmaXval, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL02*0.325, 4, (strpos($saccom, "PAYWARD") === false ? "LINEN: " : "PHARMACY: "), "", 0, '');
		$this->Cell(4, 4, "", "", 0, '');
		#$this->Cell(FOOTER_COL01*0.675, 4, str_repeat('_', 30), "", 1, '');
		$this->Cell(FOOTER_COL01*0.675, 4, str_repeat('_', 30), "", $pharmaNextLine, '');

		if ($hasbloodborrowed)
				$this->Ln(2);

		#$this->Cell(4, 4, "", "", 0, '');
		$this->Cell($cashierXval, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL01*0.325, 4, "CASHIER: ", "", 0, '');
		$this->Cell(4, 4, "", "", 0, '');
		#$this->Cell(FOOTER_COL01*0.675, 4, str_repeat('_', 30), "", 0, '');
		$this->Cell(FOOTER_COL01*0.675, 4, str_repeat('_', 30), "", $cashierNextLine, '');

		if (!$hasbloodborrowed)
		    $this->Ln(2);

		#$this->Cell(8, 4, "", "", 0, '');
		$this->Cell($nurseXval, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL02*0.325, 4, "NURSE ON DUTY: ", "", 0, '');
		$this->Cell(4, 4, "", "", 0, '');
		$this->Cell(FOOTER_COL01*0.675, 4, str_repeat('_', 30), "", $nurseNextLine, '');
	 #----------
		if (!(strpos($saccom, "PAYWARD") === false)) {
			if ($hasbloodborrowed)
								$this->Ln(2);

			#$this->Cell(4, 4, "", "", 0, '');
			$this->Cell($billingXval, 4, "", "", 0, '');
			$this->Cell(FOOTER_COL01*0.325, 4, "BILLING: ", "", 0, '');
			$this->Cell(4, 4, "", "", 0, '');
			$this->Cell(FOOTER_COL01*0.675, 4, str_repeat('_', 30), "", 1, '');
		}
	}

	function ReportTitle() {
		$this->Ln(2);
		$this->SetFont($this->fontType, "B", "10");
		$this->Cell(0, 4, $this->reportTitle, 0 , 1, "C");
	}

	function PersonInfo() {
		global $date_format;

		$rowArray = $this->getPersonInfo($this->encounter_nr);
		if (!is_bool($rowArray)) {
			$row = $rowArray->FetchRow();

			$this->personData = $row;

			$name = strtoupper($row['name_last'].",  ".$row['name_first']." ".$row['name_middle']);

			$saddr1 = '';
			$saddr2 = '';
			$saddr3 = '';
			$this->trimAddress($row['street_name'], $row['brgy_name'], $row['mun_name'], $row['prov_name'], $row['zipcode'], $saddr1, $saddr2, $saddr3);
//			$admission_dte = @formatDate2Local($row['admission_dt'], $date_format);

			//$billdte       = strftime("%b %d, %Y %I:%M %p", strtotime($this->bill_date));
            //edited by jasper 06/10/2013 REMOVE TIME FROM BILL DATE
            $billdte       = strftime("%b %d, %Y", strtotime($this->bill_date));
			if (is_null($row['admission_dt']))
				$admission_dte = strftime("%b %d, %Y %I:%M %p", strtotime($row['encounter_date']));
			else
				$admission_dte = strftime("%b %d, %Y %I:%M %p", strtotime($row['admission_dt']));

// --- Changes made by LST - $this->in2mm(4.8) to $this->in2mm(4.5)

			$this->Ln(4);
			$this->SetFont($this->fontType, $this->fontStyle, $this->fontSize);

            //added by jasper 01/04/13
            //Encounter number
            $this->Cell(20, 4, "Case #", "", 0, 'L');
            $this->Cell(1, 4, ":", "", 0, 'R');
            $this->Cell($this->in2mm(4.4), 4, $this->encounter_nr, "", 0, '');


            //Bill Reference number
            $this->Cell(22.6, 4, "Bill Ref. # ", "", 0, 'L');
            $this->Cell(1, 4, ":", "", 0, 'R');
            $this->Cell(12, 4, $this->bill_ref_nr, "", 1, '');
            //added by jasper 01/04/13

			//HRN
			$this->Cell(20, 4, "HRN ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->Cell($this->in2mm(4.4), 4, $row['pid'], "", 0, '');

			//Date
			$this->Cell(22.6, 4, "Date ", "", 0, '');
			$this->Cell(1, 4, ":", "", 0, 'R');
//			$this->Cell(12, 4, date('m/d/Y'), "", 1, '');
			$this->Cell(12, 4, $billdte, "", 1, '');

			//patient name
			$this->Cell(20, 4, "Name ", "", 0, 'L');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->Cell($this->in2mm(4.4),  4, substr($name, 0, NAME_LEN), "", 0, '');

			//Department
			$this->Cell(22.6, 4, "Dept. ", "", 0, '');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->Cell(12, 4, substr($row['dept_name'],0,DEPT_LEN), "", 1, '');

			//Address (line 1)
			$this->Cell(20, 4, "Address ", "", 0, '');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->Cell($this->in2mm(4.4), 4, strtoupper($saddr1), "", 0, '');

			//Admitted
			$this->Cell(22.6, 4, "Admitted", "", 0, '');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->Cell(35, 4, $admission_dte, "", 1, '');

            //Address (line 2)
			$this->Cell(20, 4, "", "", 0, '');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->Cell($this->in2mm(4.4), 4, strtoupper($saddr2), "", 0, '');

			//Classification
//			$sClassification = $this->objBill->getClassificationDesc();
			$sMembership = $this->objBill->getMemCategoryDesc();
            //added by jasper 04/24/2013
            $classification = $this->objBill->getClassificationDesc();


//			$this->Cell(22.75, 4, "Classification", "", 0, '');
            //edited by jasper 04/24/2013
			$this->Cell(22.75, 4, (!$this->isphic ? (!$classification ? " " : "Classification") : "Membership"), "", 0, '');
			$this->Cell(1, 4, (!$this->isphic ? (!$classification ? " " : ":") : ":"), "", 0, 'R');
//			$this->Cell(30, 4, ($sClassification == '' ? "No Classification" : $sClassification), "", 1, '');
			$this->Cell(30, 4, ($this->isphic ? ($sMembership == '' ? "Not Specified" : $sMembership) : ($classification ? $classification : "No PHIC")), "", 1, '');



			//Address (line 3)
			if ($saddr3 != '') {
				$this->Cell(20, 4, "", "", 0, '');
				$this->Cell(1, 4, ":", "", 0, 'R');
				$this->Cell($this->in2mm(4.4), 4, strtoupper($saddr3), "", 1, '');
			}

			//Room #
			if ($row['room_no'] == 0) {
                if ($this->brecalc) {
                    $this->objBill->getAccommodationHist(); // set AccommodationHist
                    $this->objBill->getRoomTypeBenefits(); // set Room type Benefits
                    $this->objBill->getConfineBenefits('AC');
                }
                else {
                    $ac = unserialize($_SESSION['billobject']['ac']);
                    if (!($ac instanceof ACBill)) {
                            $var_dump("No accommodation object retrieved!");
                    }
                    $ac->assignBillObject($this->objBill);
                }

				$accArray   = $this->objBill->getAccHist();
				if (!empty($accArray)) {
					$sroom_no   = $accArray[count($accArray)-1]->getRoomNr();
					$sward_name = $accArray[count($accArray)-1]->getTypeDesc();

                    if ($this->ishousecase) {
                        $sward_name = preg_replace("/pay[\s]*ward/i", "Ward", $sward_name);
                    }
				}
				else {
					$sroom_no   = 'None';
					$sward_name = "No Accommodation";
				}

				$this->b_acchist_gathered = TRUE;
			}
			else {
				$sroom_no   = $row['room_no'];
				$sward_name = $row['ward_name'];

                if ($this->ishousecase) {
                    $sward_name = preg_replace("/pay[\s]*ward/i", "Ward", $sward_name);
                }
			}

			$sCaseType = $this->objBill->getCaseTypeDesc();

			$this->Cell(20, 4, "Room #", "", 0, '');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->Cell(10, 4, $sroom_no, "", 0, '');
//			$this->Cell($this->in2mm(4), 4, "( ".$sward_name." )".($sCaseType == '' ? '' : " - ".$sCaseType), "", 0 ,'');

			#Last billing ...
			$lastbilldte = $this->objBill->getActualLastBillDte();
			if ( ($lastbilldte == "0000-00-00 00:00:00") && !$this->objBill->getIsCoveredByPkg() )
				$this->Cell($this->in2mm(4), 4, "( ".$sward_name." )".($sCaseType == '' ? '' : " - ".$sCaseType), "", 0 ,'');
			else {
				$this->Cell($this->in2mm(4), 4, "( ".$sward_name." )".($sCaseType == '' ? '' : " - ".$sCaseType), "", 0 ,'');

                if ( $this->objBill->getIsCoveredByPkg() ) {
                    $this->Cell(23, 4, "Package ", "", 0, '');
                    $this->Cell(1, 4, ":", "", 0, 'R');
                    $this->Cell(35, 4, $this->objBill->getPackageName(), "", 1, '');
                }
                else {
                    $this->Cell(22.6, 4, "Last Billing ", "", 0, '');
                    $this->Cell(1, 4, ":", "", 0, 'R');
                    $this->Cell(12, 4, strftime("%b %d, %Y %I:%M%p", strtotime($lastbilldte)), "", 1, '');
                }
			}

			if($this->objBill->isMedicoLegal()){
				$this->SetFont("Times", "B", "11");
				$this->Cell(50, 4,"Medico Legal", "", 1, 'R');
				$this->SetFont("Times", "", "10");
			}else{
				$this->Cell(50, 4,"", "", 1, 'R');
			}

			#Added by Jarel	06/12/2013
			if($this->death_date != ''){
				#Updated by Jane 10/17/2013
				$this->SetFont("Times", "B", "11");
				$this->Cell(152, 4, "Death Date", "", 0, 'R');
				$this->Cell(4, 4, ":", "", 0, 'R');
				$this->Cell(35, 4, strftime("%b %d, %Y %I:%M%p", strtotime($this->death_date)), "", 1, '');
				$this->SetFont("Times", "", "10");
			}else{
				$this->Cell(50, 4,"", "", 1, 'R');
			}


		}
	}//end of PersonInfo

	function TitleHeader($billType){
		switch($billType){
			case 'summary':
				$this->Ln(3);
//				$this->Cell(GEN_COL01-4, 4, "#", "TB", 0, 'C');
//				$this->Cell(4, 4, " ", "", 0, '');
				$this->Cell($this->in2mm(GEN_COL02) , 4, "Particulars", "TB", 0, 'C');
				$this->Cell(COL_MID, 4, " ", "", 0, '');
				$this->Cell(COL03_WIDTH, 4, "Actual Charges", "TB", 0, 'C');
				$this->Cell(COL_MID, 4, " ", "", 0, '');
				$this->Cell(COL04_WIDTH, 4, "Discount", "TB", 0, 'C');
				$this->Cell(COL_MID, 4, " ", "", 0, '');
				$this->Cell(COL05_WIDTH, 4, "Insurance/PHIC", "TB", 0, 'C');
				$this->Cell(COL_MID, 4, " ", "", 0, '');
				$this->Cell(COL06_WIDTH, 4, "Excess", "TB", 0, 'C');
				break;
			case 'detailed':
//				$this->Ln(3);
//				$this->Cell(8, 4, "#", "TB", 0, 'C');
//				$this->Cell(4, 4, " ", "", 0, '');
//				$this->Cell($this->in2mm(1.2) , 4, "Date Requested", "TB", 0, 'C');
//				$this->Cell(4, 4, " ", "", 0, '');
//				$this->Cell($this->in2mm(3.4) , 4, "Particulars", "TB", 0, 'C');
//				$this->Cell(4, 4, " ", "", 0, '');
//				$this->Cell(10 , 4, "Qty", "TB", 0, 'C');
//				$this->Cell(4, 4, " ", "", 0, '');
//				$this->Cell($this->in2mm(1.2) , 4, "Amount", "TB", 0, 'C');
			break;
		}
	} //end of function TitleHeader

	function PrintData(){
		$this->Ln(5);

		// Accommodation
		if (!$this->objBill->isERPatient()) $this->getAccommodationData();
		$this->getHospitalServiceData();   // Hospital services ( Laboratory & radiology)
		$this->getMedicinesData();         // Medicines
//		$this->getSuppliesData();          // Supplies
		$this->getOpsCharges();			   // Operation/Procedures
		$this->getMiscellaneousCharges();  // Miscellaneous Charges

	}// end of function PrintData

	function getPFDiscount($pfarea, $npf, $nclaim) {
		global $db;

		$n_discount = 0.00;
		$n_prevdiscount = 0.00;

		$area_array = array('AC', 'D1', 'D2', 'D3', 'D4');
        //edited by jasper 04/16/2013    -CONDITION SHOULD BE THE SAME WITH FUNCTION getBillAreaDiscount IN class_billing.php
		//if ($this->objBill->isCharity() && (in_array($pfarea, $area_array))) {
          if ($this->objBill->isCharity() && !$this->objBill->isMedicoLegal() && !$this->objBill->isPHIC() && (in_array($pfarea, $area_array))) {
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
			$strSQL = "select fn_get_bill_discount('". $this->encounter_nr. "', '". $pfarea ."', '".$this->bill_date."') as discount";
			if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
					$row = $result->FetchRow();
					if (!is_null($row['discount'])) {
						$n_discount = $row['discount'];
					}
				}
			}

			// .... get discount rate applied to bill area of encounter while at ER, if there is one.
			if ($this->objBill->prev_encounter_nr != '') {
				$strSQL = "select fn_get_bill_discount('". $this->objBill->prev_encounter_nr. "', '". $pfarea ."', '".$this->bill_date."') as discount";
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

	function Professional_Fee() {
        if ($this->brecalc) {
            $this->objBill->getProfFeesList();
            $this->objBill->getProfFeesBenefits();
        }
        else {
            $pf = unserialize($_SESSION['billobject']['pf']);
            if (!($pf instanceof PFBill)) {
                    $var_dump("No PF object retrieved!");
            }
            $pf->assignBillObject($this->objBill);
        }

		$hsp_pfs_benefits = $this->objBill->getPFBenefits(); #role area

		$this->Ln(8);
//		$this->Cell(GEN_COL01-2, 4, "ADD", "", 0, 'C');

		$this->Cell(GEN_COL01, 4, "ADD:", "", 1, 'C');

//		$this->Cell(GEN_COL02_D3 + 8, 4, ":", "", 1, 'L');
//		$this->Cell(GEN_COL01, 4, " ", "", 0, '');

		$ndiscount = 0;
		$proffees_list = $this->objBill->proffees_list;
		$prevrole_area = '';
		if(is_array($hsp_pfs_benefits) && (count($hsp_pfs_benefits) > 0)) {
			$this->Cell($this->in2mm(GEN_COL02), 4,"Professional Fees", "", 1, '');

			$pfs_confine_coverage_tmp = array();
			$pfs_confine_benefits_tmp = array();

			foreach($hsp_pfs_benefits as $key=> $value) {
				if ($value->role_area == $prevrole_area) continue;
				$prevrole_area = $value->role_area;

				$totalCharge = $this->objBill->getTotalPFCharge($value->role_area);

//				$this->objBill->getConfineBenefits($value->role_area, '', $value->getRoleLevel());
				reset($proffees_list);
                if ($this->brecalc) {
                    $this->objBill->initProfFeesCoverage($value->role_area);
                }

//				$totalCharge = $value->tot_charge;
//				$coverage    = $this->objBill->pfs_confine_coverage[$value->role_area];

				$role_desc = substr($value->role_desc, 23, strlen($value->role_desc));

//				if ($this->IsDetailed){
				$this->Ln(2);
//					$this->Cell(GEN_COL02_D, 4, "", "", 0, '');
				$this->Cell(GEN_COL01, 4, "", "", 0, '');
				$this->Cell($this->in2mm(GEN_COL02)-6, 4, $role_desc, "", 1, '');

				$bShow = count($proffees_list) > 1;

				$ndays = 0;
				$nrvu  = 0;
				$area_pf = 0;
				$area = $value->role_area;
                $coverage_sum = 0.00;
//				$this->objBill->getTotalPFParams($ndays, $nrvu, $area_pf, $area, 0, true);

				// Get the listing of doctors with corresponding claims.
//				$this->objBill->getPerDrPFandClaims($area_pf, $this->objBill->pfs_confine_coverage[$area], $area);

					$this->objBill->getPerHCareCoverage();

					// Save the computed confinement pf coverage ...
					$pfs_confine_coverage_tmp[$area] = $this->objBill->pfs_confine_coverage[$area];
					$pfs_confine_benefits_tmp[$area] = $this->objBill->pfs_confine_benefits[$area];

					$this->objBill->getPerDrPFandClaims($area);

					// Restore the computed confinement pf coverage ...
					$this->objBill->pfs_confine_coverage[$area] = $pfs_confine_coverage_tmp[$area];
					$this->objBill->pfs_confine_benefits[$area] = $pfs_confine_benefits_tmp[$area];

					#Display list of doctors in every role area
					foreach($proffees_list as $key=>$profValue){
						if($value->role_area == $profValue->role_area) {
                            if ($this->brecalc) {
                                $opcodes = $profValue->getOpCodes();
                                if ($opcodes != '') {
                                   $opcodes = explode(";", $opcodes);
                                }
                                if (is_array($opcodes)) {
                                    foreach($opcodes as $v) {
                                        $i = strpos($v, '-');
                                        if (!($i === false)) {
                                            $code = substr($v, 0, $i);
                                            if (!$profValue->getIsExcludedFlag()) {
                                                $this->objBill->getConfineBenefits($value->role_area, $profValue->getDrNr(), $profValue->getRoleLevel(), false, 0, $code);
                                            }
                                            if ($this->objBill->getIsCoveredByPkg() && !$profValue->getIsExcludedFlag()) break;
                                        }
                                    }
                                }
                                else
                                    if (!$profValue->getIsExcludedFlag()) {
                                        $this->objBill->getConfineBenefits($value->role_area, $profValue->getDrNr(), $profValue->getRoleLevel());
                                    }
                            }

//                            if (!$profValue->getIsExcludedFlag()) {
//                                $this->objBill->getConfineBenefits($value->role_area, $profValue->getDrNr(), $profValue->getRoleLevel());
//                            }

							$drName = $profValue->dr_first." ".$profValue->dr_mid.". ".$profValue->dr_last;
							$drCharge = $profValue->dr_charge;

//							$this->Cell(GEN_COL02_D2, 4, "", "", 0, '');
							$this->Cell(GEN_COL01, 4, "", "", 0, '');
							$this->Cell($this->in2mm(GEN_COL02)-8, 4, $drName, "", 0, '');

							$claim = $this->getDrClaim($profValue->getDrNr(), $value->role_area, $this->objBill->pf_claims);
							if ($bShow) {
								$this->Cell(COL_MID, 4, "", "", 0, '');
								$this->Cell(COL03_WIDTH + 4, 4, number_format($drCharge, 2, '.', ','), "", 0, 'R');
                                //jasper 04/16/2013
								$npfdiscount = $this->getPFDiscount($value->role_area, $drCharge, $claim);

								$this->Cell(COL_MID, 4, "", "", 0, '');
								$this->Cell(COL04_WIDTH, 4, number_format($npfdiscount, 2, '.', ','), "", 0, 'R');

								$this->Cell(COL_MID, 4, "", "", 0, '');
								$this->Cell(COL05_WIDTH, 4, number_format($claim, 2, '.', ','), "", 0, 'R');

								$this->Cell(COL_MID, 4, "", "", 0, '');
								$this->Cell(COL06_WIDTH, 4, number_format($drCharge - $claim - $npfdiscount, 2, '.', ','), "", 1, 'R');
							}

              $coverage = (!$profValue->getIsExcludedFlag()) ? $claim : 0.00;
//              $coverage = (!$profValue->getIsExcludedFlag()) ? $this->objBill->pfs_confine_coverage[$value->role_area] : 0.00;

							$coverage_sum += $coverage;
                        }
					} # end foreach proffees_list

					if ($bShow) {
//						$this->Cell(GEN_COL01 + $this->in2mm(GEN_COL02), 4, "", "", 0, 'R');
						$this->Cell($this->in2mm(GEN_COL02), 4, "", "", 0, 'R');
						$this->Cell(COL_MID, 4, "", "", 0, '');
						$this->Cell(COL03_WIDTH, 4, str_repeat("-", 25), "", 0, 'R');
						$this->Cell(COL_MID, 4, "", "", 0, '');
						$this->Cell(COL04_WIDTH, 4, str_repeat("-", 20), "", 0, 'R');
						$this->Cell(COL_MID, 4, "", "", 0, '');
						$this->Cell(COL05_WIDTH, 4, str_repeat("-", 23), "", 0, 'R');
						$this->Cell(COL_MID, 4, "", "", 0, '');
						$this->Cell(COL06_WIDTH, 4, str_repeat("-", 23), "", 1, 'R');

	//					$this->Cell(22, 4, "", "", 0, '');
//						$this->Cell(GEN_COL01 + $this->in2mm(GEN_COL02), 4, "Sub-Total (".$role_desc.")", "", 0, 'R');
						$this->Cell($this->in2mm(GEN_COL02)-3.5, 4, "Sub-Total (".$role_desc.")", "", 0, 'R');
					}
                    //added by jasper 05/21/2013
                    if (!$this->objBill->isSponsoredMember()) {
                        $ndiscount = $this->objBill->getBillAreaDiscount($value->role_area);
                    } else {
                        $ndiscount = 0.00;
                    }

					$this->Cell(COL_MID, 4, "", "", 0, '');
					$this->Cell(COL03_WIDTH+3.5, 4, number_format($totalCharge, 2, '.', ','), "", 0, 'R');
					$this->Cell(COL_MID, 4, "", "", 0, '');
					$this->Cell(COL04_WIDTH, 4, number_format($ndiscount, 2, '.', ','), "", 0, 'R');
					$this->Cell(COL_MID, 4, "", "", 0, '');
					$this->Cell(COL05_WIDTH, 4, number_format($coverage_sum, 2, '.', ','), "", 0, 'R');
					$this->Cell(COL_MID, 4, "", "", 0, '');
					$this->Cell(COL06_WIDTH, 4, number_format(($totalCharge-$ndiscount-$coverage_sum), 2, '.', ','), "", 0, 'R');
					$this->Ln(4);

//				}else{
//					$ndiscount = $this->objBill->getBillAreaDiscount($value->role_area);
//
				//	$this->Cell(GEN_COL02_D, 4, "", "", 0, '');
//					$this->Cell(GEN_COL01, 4, "", "", 0, '');
//					$this->Cell($this->in2mm(GEN_COL02)-6, 4, $role_desc, "", 0, '');
//					$this->Cell(COL_MID + 1.75, 4, "", "", 0, '');
//					$this->Cell(COL03_WIDTH, 4, number_format($totalCharge, 2, '.', ','), "", 0, 'R');
//					$this->Cell(COL_MID, 4, "", "", 0, '');
//					$this->Cell(COL04_WIDTH, 4, number_format($ndiscount, 2, '.', ','), "", 0, 'R');
//					$this->Cell(COL_MID, 4, "", "", 0, '');
//					$this->Cell(COL05_WIDTH, 4, number_format($coverage, 2, '.', ','), "", 0, 'R');
//					$this->Cell(COL_MID, 4, "", "", 0, '');
//					$this->Cell(COL06_WIDTH, 4, number_format(($totalCharge-$ndiscount-$coverage), 2, '.', ','), "", 0, 'R');
//					$this->Ln(4);
//				}

				$this->totalCharge[PF_AREA] += $totalCharge;
				$this->totalDiscount[PF_AREA] += $ndiscount;
				$this->totalCoverage[PF_AREA] += $coverage_sum;
				$this->totalExcess[PF_AREA] += ($totalCharge-$ndiscount-$coverage_sum);

			}#1st foreach
		}else{
			$this->Cell($this->in2mm(GEN_COL02), 4,"Professional Fees", "", 0, '');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL03_WIDTH, 4, "0.00", "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL04_WIDTH, 4, "0.00", "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL05_WIDTH, 4, "0.00", "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL06_WIDTH, 4, "0.00", "", 1, 'R');
		}

		if (count($hsp_pfs_benefits) > 1) {
			$this->Ln(2);
			$this->Pf_Sub_Total();
		}
	}#end of function Professional_Fee

	function getDrClaim($dr_nr, $role_area, $drclaims) {
		$claim = 0;
		foreach($drclaims as $k=>$v) {
			if (($v->getDrNr() == $dr_nr) && ($v->getRoleArea() == $role_area)) {
				$claim = $v->getDrClaim();
			}
		}
		return $claim;
	}

	function Sub_Total(){
		$this->Ln(2);
//		$this->Cell(GEN_COL01, 4, " ", "", 0, '');
		$this->Cell($this->in2mm(GEN_COL02), 4, "Sub-Total" , "", 0, 'R');

		//Actual charges
		#accomodation + hospital services + medicines + supplies + others
		$totalcharge = 0;
		foreach($this->totalCharge as $key=>$v) {
			if ($key != PF_AREA)
				$totalcharge += $v;
		}
#		$this->subTotal_ActualCharge = $this->totalCharge[AC_AREA] + $this->totalCharge[HS_AREA] + $this->totalCharge[MD_AREA] + $this->SPSubTotal_ActualCharge ;

//		$this->Cell(4, 4, " ", "", 0, '');    //$this->in2mm(1.35)
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL03_WIDTH, 4, number_format($totalcharge,2,'.',','), "T", 0, 'R');

		# Discount ...
		$t_discount = 0;
		foreach($this->totalDiscount as $key=>$v) {
			if ($key != PF_AREA)
				$t_discount += $v;
		}
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL04_WIDTH, 4, number_format($t_discount,2,'.',','), "T", 0, 'R');

		//Medicare Coverage
		#accomodation + hospital services + medicines + supplies + others
		$totalcoverage = 0;
		foreach($this->totalCoverage as $key=>$v) {
			if ($key != PF_AREA)
				$totalcoverage += $v;
		}
#		$this->subTotal_Medicare = $this->ACSubTotal_Medicare + $this->HSSubTotal_Medicare + $this->MDSubTotal_Medicare + $this->SPSubTotal_Medicare;
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL05_WIDTH, 4, number_format($totalcoverage,2,'.',','), "T", 0, 'R');

		//Excess
		#accomodation + hospital services + medicines + supplies + others
		$totalexcess = 0;
		foreach($this->totalExcess as $key=>$v) {
			if ($key != PF_AREA)
				$totalexcess += $v;
		}
#		$this->subTotal_Excess = $this->ACSubTotal_Excess + $this->HSSubTotal_Excess + $this->MDSubTotal_Excess + $this->SPSubTotal_Excess;

		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL06_WIDTH, 4, number_format($totalexcess,2,'.',','), "T", 0, 'R');

	}//end of function Sub_Total

	function Pf_Sub_Total(){
//		$this->Cell(GEN_COL01, 4, " ", "", 0, '');
		$this->Cell($this->in2mm(GEN_COL02), 4, "Sub-Total", "", 0, 'R');

		//Actual charges
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL03_WIDTH, 4,number_format($this->totalCharge[PF_AREA], 2, '.', ','), "T", 0, 'R');

		//Discount
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL04_WIDTH, 4, number_format($this->totalDiscount[PF_AREA], 2, '.', ','), "T", 0, 'R');

		//Insurance Coverage
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL05_WIDTH, 4, number_format($this->totalCoverage[PF_AREA], 2, '.', ','), "T", 0, 'R');

		//Excess
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL06_WIDTH, 4, number_format($this->totalExcess[PF_AREA], 2, '.', ','), "T", 0, 'R');
	}// end of function Pf_Sub_Total()

	function Totals(){
		$this->Ln(4);
//		$this->Cell(GEN_COL01, 4, " ", "", 0, '');
		$this->Cell($this->in2mm(GEN_COL02), 4, "T O T A L", "", 0, '');

//		$totalActualCharge = $this->subTotal_ActualCharge + $this->pfSubTotal_ActualCharge;
//		$totalMedicare = $this->subTotal_Medicare + $this->pfSubTotal_Medicare;
//		$totalExcess = $this->subTotal_Excess + $this->pfSubTotal_Excess;

		$totalActualCharge = 0;
		$t_discount        = 0;
		$totalMedicare     = 0;
		$totalExcess       = 0;

		foreach($this->totalCharge as $v)
			$totalActualCharge += round($v, 2);

		foreach($this->totalDiscount as $v)
			$t_discount += round($v, 2);

		foreach($this->totalCoverage as $v)
			$totalMedicare += round($v, 2);

//		foreach($this->totalExcess as $v)
//			$totalExcess += round($v, 0);
		$totalExcess = $totalActualCharge - $t_discount - $totalMedicare;

		$this->SetFont("Times", "B", "10");

		//Actual charges
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL03_WIDTH, 4,number_format($totalActualCharge, 2, '.', ','), "T", 0, 'R');

		//Discount
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL04_WIDTH, 4, number_format($t_discount, 2, '.', ','), "T", 0, 'R');

		//Insurance Coverage
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL05_WIDTH, 4, number_format($totalMedicare, 2, '.', ','), "T", 0, 'R');

		//Excess
		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL06_WIDTH, 4, number_format($totalExcess, 2, '.', ','), "T", 1, 'R');

//		$this->SetFont("Times", "", "10");
        //added by jasper 04/08/2013
        $prevbill_amt = $this->PreviousBill($this->encounter_nr, $this->bill_ref_nr);
		$this->Less($totalExcess);
	}//end of function Totals()

    //added by jasper 04/08/2013
    function PreviousBill ($enc_nr, $bill_nr) {
        //echo $enc_nr . "//" . $bill_nr;
        $objbillinfo = new BillInfo();
        $tot_prevbill_amt = 0;
        $result = $objbillinfo->getPreviousBillAmt($enc_nr, $bill_nr);
        //echo $result;
        if ($result) {
            while ($row = $result->FetchRow()) {
                $n_bill = 0;
                if (!empty($row["total_charge"])) $n_bill = $row["total_charge"];
                if (!empty($row["total_coverage"])) $n_bill -= $row["total_coverage"];
                if (!empty($row["total_computed_discount"])) $n_bill -= $row["total_computed_discount"];
                if (!empty($row["total_discount"]) && ($n_bill > 0)) $n_bill -= ($n_bill * $row["total_discount"]);
                $tot_prevbill_amt += $n_bill;
            }
        }
        //echo $enc_nr . "//" . $bill_nr . "//" . $tot_prevbill_amt;
        $this->prev_bill_amt = $tot_prevbill_amt;

        if ($tot_prevbill_amt>0) {
            $this->SetFont("Times", "B", "11");
            $this->Ln(2);
    //        $this->Cell(GEN_COL01, 4, "", "", 0, '');
            $this->Cell($this->in2mm(GEN_COL02), 4, "Add :","", 0, '');

            $this->Ln(4);
            $this->SetFont("Times", "", "10");
    //        $this->Cell(GEN_COL02_D, 4, "", "", 0, '');
            $this->Cell(GEN_COL01, 4, "", "", 0, '');
            $this->Cell($this->in2mm(GEN_COL02)-6, 4, "Previous Bill Amount","", 0, '');

            $this->SetFont("Times", "B", "11");

            $this->Cell(COL_MID + COL03_WIDTH + COL_MID + COL04_WIDTH + COL_MID + COL05_WIDTH, 4, " ", "", 0, 'R');

            $this->Cell(COL_MID+2, 4, " ", "", 0, '');
            $this->Cell(COL06_WIDTH, 4, number_format(round($tot_prevbill_amt), 2, '.', ','), "T", 1, 'R');
        }
    }
    //added by jasper 04/08/2013

	function Less($totalExcess){
		$this->SetFont("Times", "B", "11");
		$this->Ln(2);
//		$this->Cell(GEN_COL01, 4, "", "", 0, '');
		$this->Cell($this->in2mm(GEN_COL02), 4, "Less :","", 0, '');

		#added by VAN 03-11-08

		# partial payment
		$deposit = $this->objBill->getPreviousPayments();
		$this->Ln(4);
        if (!is_null($deposit) && $deposit > 0) {
		$this->SetFont("Times", "", "10");
//		$this->Cell(GEN_COL02_D, 4, "", "", 0, '');
		$this->Cell(GEN_COL01, 4, "", "", 0, '');
		$this->Cell($this->in2mm(GEN_COL02)-6, 4, "Previous Payment (Deposits)","", 0, '');

		$this->Cell(COL_MID + COL03_WIDTH + COL_MID + COL04_WIDTH + COL_MID + COL05_WIDTH + 1.5, 4, "", "", 0, 'R');

		    $this->SetFont("Times", "", "10");
            //added by jasper 07/29/2013
            //if ($deposit) {
                $this->Cell(COL_MID, 4, " ", "", 1, '');
        }

        //added by jasper 07/26/2013 -FIX FOR JIRA MS-734 OR BUG#306 - display OR# and deposit amount
        foreach ($this->objBill->prev_payments as $val) {
            $this->Cell(GEN_COL01, 4, "", "", 0, '');
            $this->Cell($this->in2mm(GEN_COL02)-6, 4, "    OR#: " .$val->getORNo(),"", 0, '');
            $this->Cell(COL_MID + COL03_WIDTH + COL_MID + COL04_WIDTH + COL_MID + COL05_WIDTH + 1.5, 4, "", "", 0, 'R');
            $this->Cell(COL_MID, 4, " ", "", 0, '');
            $this->Cell(COL06_WIDTH, 4, number_format($val->getAmountPaid(), 2, '.', ','), "", 1, 'R');
        }
        //added by jasper 07/26/2013 -FIX FOR JIRA MS-734 - display OR# and deposit amount
        
        
        //added by jasper 05/30/2013 FIX FOR OB ANNEX CHARGE
        $totalOBpayments = $this->objBill->getOBAnnexPayment();
        $deposit += $totalOBpayments;
        if (!is_null($totalOBpayments)) {
            $this->SetFont("Times", "", "10");
            $this->Cell(GEN_COL01, 4, "", "", 0, '');                                          
            $this->Cell($this->in2mm(GEN_COL02)-6, 4, "Previous Payment (Co-Payment)","", 1, '');
            foreach ($this->objBill->ob_payments as $val) {
                $this->Cell(GEN_COL01, 4, "", "", 0, '');
                $this->Cell($this->in2mm(GEN_COL02)-6, 4, "    OR#: " .$val->getORNo(), "", 0, '');
                $this->Cell(COL_MID + COL03_WIDTH + COL_MID + COL04_WIDTH + COL_MID + COL05_WIDTH + 1.5, 4, "", "", 0, 'R');
		$this->Cell(COL_MID, 4, " ", "", 0, '');
                $this->Cell(COL06_WIDTH, 4, number_format($val->getAmountPaid(),2,'.',',') ,"", 1, 'R');
            }
        }
        //added by jasper 05/30/2013 FIX FOR OB ANNEX CHARGE
        
        //removed by jasper 07/26/2013 -FIX FOR JIRA MS-734 -remove total deposit from SOA
	//$this->Cell(COL06_WIDTH, 4, number_format($deposit, 2, '.', ','), "", 0, 'R');

	//discounts
        //added by jasper 05/21/2013 FIX FOR AUTOMATIC EXCESS FOR PHIC PATIENTS
        //if (!$this->objBill->isPHIC() && !$this->objBill->isMedicoLegal() ) {
        //   $totalDiscount = $this->objBill->getTotalDiscount();
        //} else {
            $totalDiscount = 0;
        //}
        //removed by jasper 04/24/2013 AS REQUESTED BY BILLING TO HIDE DISCOUNTS FROM CLASSIFICATION
        /*if ($totalDiscount != 0 && !$this->objBill->isSponsoredMember()) {
			$this->SetFont("Times", "", "10");

			$this->Ln(4);
	//		$this->Cell(GEN_COL02_D, 4, "", "", 0, '');
			$this->Cell(GEN_COL01, 4, "", "", 0, '');
			$this->Cell($this->in2mm(GEN_COL02)-6, 4, "Classification Discount","", 0, '');

			$this->Cell(COL_MID + COL03_WIDTH + COL_MID + COL04_WIDTH + COL_MID + COL05_WIDTH + 1.5, 4, "", "", 0, 'R');

			$this->SetFont("Times", "B", "11");
			$this->Cell(COL_MID, 4, " ", "", 0, '');
			$this->Cell(COL06_WIDTH, 4, number_format($totalDiscount, 2, '.', ','), "", 0, 'R');
	} */

        //added by jasper 03/27/2013
        //NO BALANCE BILLING OR PHS (Infirmary Discount)
        if ($this->objBill->isSponsoredMember() || $this->objBill->checkIfPHS() || $this->objBill->isHSM()) {
            $this->SetFont("Times", "B", "11");

            $this->Ln(4);
            $this->Cell(GEN_COL01, 4, "", "", 0, '');
            if($this->objBill->isHSM()) {
            	$label = "HOSPITAL SPONSORED MEMBER";
            } elseif ($this->objBill->isSponsoredMember()) {
            	$label = "SPONSORED - NO BALANCE BILLING";
            } else {
            	$label = "INFIRMARY DISCOUNT";
            }
            
            $this->Cell($this->in2mm(GEN_COL02)-6, 4, $label, "", 0, '');
            $this->Cell(COL_MID + COL03_WIDTH + COL_MID + COL04_WIDTH + COL_MID + COL05_WIDTH + 1.5, 4, "", "", 0, 'R');

            $this->SetFont("Times", "B", "11");
            $this->Cell(COL_MID, 4, " ", "", 0, '');
            $netExcess = ($totalExcess + $this->prev_bill_amt) - (round($deposit, 0) + round($totalDiscount, 0) + round($this->prev_bill_amt, 0));
            //edited by jasper 04/16/2013
            //$this->Cell(COL06_WIDTH, 4, number_format($netExcess, 2, '.', ','), "", 0, 'R');
            $this->Cell(COL06_WIDTH, 4, number_format($netExcess + $totalDiscount + $this->prev_bill_amt, 2, '.', ','), "", 0, 'R');
            $netcharges  = 0.00;
        } else {
            $netcharges = ($totalExcess + $this->prev_bill_amt) - (round($deposit, 0) + round($totalDiscount, 0));
        }
        //added by jasper 03/27/2013

		# Net charge
		$this->SetFont("Times", "B", "11");

		$this->Ln(6);
//		$this->Cell(GEN_COL01, 4, "", "", 0, '');
		$this->Cell($this->in2mm(GEN_COL02), 4, "AMOUNT DUE :","", 0, '');

		$this->SetFont("Times", "B", "13");

		$this->Cell(COL_MID + COL03_WIDTH + COL_MID + COL04_WIDTH + COL_MID + COL05_WIDTH, 4, " ", "", 0, 'R');
        //removed by jasper 03/27/2013
		//$netcharges = $totalExcess - (round($deposit, 0) + round($totalDiscount, 0));

		$this->Cell(COL_MID, 4, " ", "", 0, '');
		$this->Cell(COL06_WIDTH, 4, number_format(round($netcharges), 2, '.', ','), "T", 1, 'R');

//		$this->Cell(GEN_COL01, 4, "", "", 0, '');
		$this->Cell($this->in2mm(GEN_COL02), 4, "","", 0, '');

		$this->Cell(COL_MID + COL03_WIDTH + COL_MID + COL04_WIDTH + COL_MID + COL05_WIDTH, 4, " ", "", 0, 'R');

		$this->Cell(COL_MID, 4, "", "", 0, '');
		$this->Cell(COL06_WIDTH + 1, 4, str_repeat("=", 14), "", 1, 'R');

        $this->SetFont("Times", "B", "13");
		$this->ReportFooter();
	}

	function getBillingClerk($slogin_id,$enc,$bill_nr) {
		global $db;

		$sname = '';

        #edited by VAN 02-22-2013
        $with_bill = 0;
        $this->clerk_italized = 0;
        #$strSQL1 = "Select create_id from seg_billing_encounter where encounter_nr ='".$enc."' and is_final = 1";
        $strSQL1 = "Select create_id from seg_billing_encounter where encounter_nr ='".$enc."' AND bill_nr='".$bill_nr."'";
        if ($result1 = $db->Execute($strSQL1)){
            if($result1->RecordCount()){
                $row1= $result1->FetchRow();
                $log_id = $row1['create_id'];
                $with_bill = 1;
            }else{
                $log_id = $slogin_id;
            }
        }

      #edited by VAN 02-22-2013
      #add that the billing clerk must be in billing dept when there is no SAVED BILL yet else the billing clerk is "NO FINAL BILL YET"
      $strSQL = "select pa.location_nr, cp.name_last, cp.name_first, cp.name_middle ".
						"   from care_person as cp inner join (care_users as cu inner join care_personell as cper ".
						"      on cu.personell_nr = cper.nr) on cper.pid = cp.pid ".
                        " INNER JOIN care_personell_assignment pa ON pa.personell_nr=cper.nr ".
						"   where login_id = '".$log_id."'".
                        "  AND cper.STATUS NOT IN ('deleted','hidden','inactive','void')  ";

        if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$row = $result->FetchRow();
//				$objb = new BillInfo();
//				$sname = $objb->concatname($row["name_last"], $row["name_first"], $row["name_middle"]);
                #added by VAN 02-22-2013
                #display only clerk from billing dept
				if ($row['location_nr']=='152'){
                    $sname = strtoupper($row["name_first"] . (is_null($row["name_middle"]) || ($row["name_middle"] == '') ? " " : " ".substr($row["name_middle"],0,1).". ").$row["name_last"]);
                }else{
                    $this->clerk_italized = 1;
                    if ($with_bill)
                        $sname = "NO NAME BE DISPLAYED, NOT A BILLING CLERK";
                    else
                        $sname = "NOT A BILLING CLERK and NO SAVE BILL YET";
                }
			}
		}

		$this->clerk_name = $sname;
	}

	function getBillingHead() {
		global $db;

		$shname = '';
		$shpos  = '';

        //added by VAN 02-14-2013
        //add AND cper.status NOT IN ('void','hidden','deleted','inactive')
		$strSQL = "select cp.name_last, cp.name_first, cp.name_middle, cper.job_position, cper.other_title ".
						"   from care_person as cp inner join (((care_personell as cper inner join care_personell_assignment as cpa ".
									"      on cper.nr = cpa.personell_nr) inner join care_department as cd on cpa.location_nr = cd.nr) ".
									"      inner join care_role_person as crp on cpa.role_nr = crp.nr) on cp.pid = cper.pid ".
									"   where upper(crp.role) regexp 'HEAD' and upper(cd.id) regexp 'BILLING' ".
                                    " AND cper.status NOT IN ('void','hidden','deleted','inactive') ".
									"   limit 1";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$row = $result->FetchRow();

//				$objb = new BillInfo();
//				$shname = $objb->concatname($row["name_last"], $row["name_first"], $row["name_middle"]);
                $row["other_title"] = trim($row["other_title"]);
                $shname = strtoupper($row["name_first"] . (is_null($row["name_middle"]) || ($row["name_middle"] == '') ? " " : " ".substr($row["name_middle"],0,1).". ").$row["name_last"]). ( ( ($row["other_title"] != '') && !is_null($row["other_title"]) ) ? ", ".$row["other_title"] : "" );

				$shpos  = $row["job_position"];
			}
		}

		$this->head_name = $shname;
		$this->head_position = $shpos;
	}

//	function getSuppliesData(){
//		$this->objBill->getSuppliesList(); // gathered all supplies consumed
//		$this->objBill->getSupplyBenefits();
//		$this->objBill->getConfineBenefits('MS', 'S');
//
//		$totalSupConfineCoverage = $this->objBill->getSupConfineCoverage();
//		$supBenefitsArray = $this->objBill->getSupConfineBenefits();
//		$ndiscount        = $this->objBill->getBillAreaDiscount('MS','S');
//
//		$this->Ln(2);
//		$this->Cell(GEN_COL01, 4, "", "", 0, 'C');
//		$this->Cell($this->in2mm(GEN_COL02), 4,"Supplies", "", ($this->IsDetailed && (count($supBenefitsArray) > 0)) ? 1 : 0, '');
//
//		if(is_array($supBenefitsArray)){
//			foreach($supBenefitsArray  as $key=>$value){
//				$acPrice = number_format($value->item_charge, 2, '.', ',');
//				$price   = number_format($value->item_price, 2, '.', ',');
//
//				if ($this->IsDetailed){
//					$this->Cell(GEN_COL02_D, 4, "", "", 0, '');
//					$this->Cell(GEN_COL01, 4, "", "", 0, '');
//					$stmp = ($value->getItemQty() > 1 ? "s" : "");
//
//					$this->Cell($this->in2mm(GEN_COL02)-6, 4, $value->artikelname." ".$value->getItemQty()." pc".$stmp." @ ".number_format($price, 2, '.', ','), "", 0, '');
//					$this->Cell(COL_MID, 4, "", "", 0, '');
//					$this->Cell(COL03_WIDTH, 4, number_format($acPrice, 2, '.', ','), "", 1, 'R');
//				}
//			}
//		}

//		$TotalSupCharge = $this->objBill->getTotalSupCharge();
//		$totalExcess = $TotalSupCharge - $ndiscount - $totalSupConfineCoverage;
//
//		if ($this->IsDetailed && (count($supBenefitsArray) > 0)){
//			$this->Cell(22, 4, "", "", 0, '');
//			$this->Cell(GEN_COL01 + $this->in2mm(GEN_COL02), 4, "", "", 0, 'R');
//			$this->Cell($this->in2mm(GEN_COL02), 4, "", "", 0, 'R');
//			$this->Cell(COL_MID, 4, "", "", 0, '');
//			$this->Cell(COL03_WIDTH, 4, str_repeat("-", 25), "", 0, 'R');
//			$this->Cell(COL_MID, 4, "", "", 0, '');
//			$this->Cell(COL04_WIDTH, 4, str_repeat("-", 20), "", 0, 'R');
//			$this->Cell(COL_MID, 4, "", "", 0, '');
//			$this->Cell(COL05_WIDTH, 4, str_repeat("-", 23), "", 0, 'R');
//			$this->Cell(COL_MID, 4, "", "", 0, '');
//			$this->Cell(COL06_WIDTH, 4, str_repeat("-", 23), "", 1, 'R');
//
//			$this->Cell(22, 4, "", "", 0, '');
//			$this->Cell(GEN_COL01 + $this->in2mm(GEN_COL02), 4, "Sub-Total (Supplies)", "", 0, 'R');
//			$this->Cell($this->in2mm(GEN_COL02), 4, "Sub-Total (Supplies)", "", 0, 'R');
//		}
//
//		$this->Cell(COL_MID, 4, "", "", 0, '');
//		$this->Cell(COL03_WIDTH, 4, number_format($TotalSupCharge, 2, '.', ','), "", 0, 'R');
//		$this->Cell(COL_MID, 4, "", "", 0, '');
//		$this->Cell(COL04_WIDTH, 4, number_format($ndiscount, 2, '.', ','), "", 0, 'R');
//		$this->Cell(COL_MID, 4, "", "", 0, '');
//		$this->Cell(COL05_WIDTH, 4, number_format($totalSupConfineCoverage, 2, '.', ','), "", 0, 'R');
//		$this->Cell(COL_MID, 4, "", "", 0, '');
//		$this->Cell(COL06_WIDTH, 4, number_format($totalExcess, 2, '.', ','), "", 0, 'R');
//		$this->Ln(4);
//
//		$this->totalCharge[SP_AREA] = $TotalSupCharge;
//		$this->totalDiscount[SP_AREA] = $ndiscount;
//		$this->totalCoverage[SP_AREA] = $totalSupConfineCoverage;
//		$this->totalExcess[SP_AREA] = $totalExcess;
//
//	}// end of function getSuppliesData

	function getMedicinesData(){
        if ($this->brecalc) {
//    		$this->objBill->getMedicinesList(); //gathered all medicines consumed
            $this->objBill->getMedicineBenefits();
            $this->objBill->getConfineBenefits('MS', 'M');
        }
        else {
            $md = unserialize($_SESSION['billobject']['md']);
            if (!($md instanceof MDBill)) {
                    $var_dump("No drugs and meds object retrieved!");
            }
            $md->assignBillObject($this->objBill);
        }

//		$totalMedConfineCoverage = $this->objBill->getMedConfineCoverage();
		$totalMedConfineCoverage = $this->objBill->getAppliedMedsCoverage();
		$medBenefitsArray = $this->objBill->getMedConfineBenefits();

        //added by jasper 05/21/2013
        if (!$this->objBill->isSponsoredMember()) {
            $ndiscount = $this->objBill->getBillAreaDiscount('MS','M');
        } else {
            $ndiscount = 0.00;
        }

		$this->Ln(2);
//		$this->Cell(GEN_COL01, 4, "", "", 0, 'C');
		$this->Cell($this->in2mm(GEN_COL02), 4,"Drugs & Medicines", "", ($this->IsDetailed && (count($medBenefitsArray) > 0)) ? 1 : 0, '');

		$n = 0;

		if (is_array($medBenefitsArray)) {
			$bShow = count($medBenefitsArray) > 1;

			foreach($medBenefitsArray as $key=>$value) {
				$acPrice = $value->item_charge;
				$price = $value->item_price;

				if ($this->IsDetailed) {
//					$this->Cell(GEN_COL02_D, 4, "", "", 0, '');
					$this->Cell(GEN_COL01, 4, "", "", 0, '');
					$stmp = ($value->getItemQty() > 1 ? "s" : "");
      /*              //added by jasper 03/25/2013
                    //$value->bestellnum;
                    $found = false;
                    $found_val = 0.00;
                    foreach($this->objBill->med_product_benefits as $k=>$v) {
                        if ($value->bestellnum==$v->bestellnum) {
                            $found = true;
                            $cover_val = $v->item_charge;
                            break;
                        }
                    }
                    //added by jasper 03/25/2013 */
					$this->Cell($this->in2mm(GEN_COL02)-6, 4, $value->artikelname." ".$value->getItemQty()." pc".$stmp." @ ".number_format($price, 2, '.', ','), "", 0, '');
					if ($bShow) {
						$this->Cell(COL_MID + 1.9, 4, "", "", 0, '');
						$this->Cell(COL03_WIDTH, 4, number_format($acPrice, 2, '.', ','), "", 1, 'R');
					}
					else
						$n = 1.75;
				}
			}
		}

		$TotalMedCharge = $this->objBill->getTotalMedCharge();
		$totalExcess = $TotalMedCharge - $ndiscount - $totalMedConfineCoverage;

		if ($this->IsDetailed && (count($medBenefitsArray) > 1)) {
//			$this->Cell(22, 4, "", "", 0, '');
//			$this->Cell(GEN_COL01 + $this->in2mm(GEN_COL02), 4, "", "", 0, 'R');
			$this->Cell($this->in2mm(GEN_COL02), 4, "", "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL03_WIDTH, 4, str_repeat("-", 25), "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL04_WIDTH, 4, str_repeat("-", 20), "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL05_WIDTH, 4, str_repeat("-", 23), "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL06_WIDTH, 4, str_repeat("-", 23), "", 1, 'R');

//			$this->Cell(22, 4, "", "", 0, '');
//			$this->Cell(GEN_COL01 + $this->in2mm(GEN_COL02), 4, "Sub-Total (Drugs & Medicines)", "", 0, 'R');
			$this->Cell($this->in2mm(GEN_COL02), 4, "Sub-Total (Drugs & Medicines)", "", 0, 'R');
		}

		$this->Cell(COL_MID + $n, 4, "", "", 0, '');
		$this->Cell(COL03_WIDTH, 4, number_format($TotalMedCharge, 2, '.', ','), "", 0, 'R');
		$this->Cell(COL_MID, 4, "", "", 0, '');
		$this->Cell(COL04_WIDTH, 4, number_format($ndiscount, 2, '.', ','), "", 0, 'R');
		$this->Cell(COL_MID, 4, "", "", 0, '');
		$this->Cell(COL05_WIDTH, 4, number_format($totalMedConfineCoverage, 2, '.', ','), "", 0, 'R');
		$this->Cell(COL_MID, 4, "", "", 0, '');
		$this->Cell(COL06_WIDTH, 4, number_format($totalExcess, 2, '.', ','), "", 0, 'R');
		$this->Ln(4);

		$this->totalCharge[MD_AREA] = $TotalMedCharge;
		$this->totalDiscount[MD_AREA] = $ndiscount;
		$this->totalCoverage[MD_AREA] = $totalMedConfineCoverage;
		$this->totalExcess[MD_AREA] = $totalExcess;

		#$objResponse->call("getMedicine",number_format($TotalMedCharge, 2, '.', ','), number_format($totalMedConfineCoverage,2,'.',','), $totalExcess );

	}//end of function getMedicinesData

	function getHospitalServiceData() {
        if ($this->brecalc) {
//            $this->objBill->getServicesList();
            $this->objBill->getServiceBenefits();
            $this->objBill->getConfineBenefits('HS');
        }
        else {
            $hs = unserialize($_SESSION['billobject']['hs']);
            if (!($hs instanceof HSBill)) {
                    $var_dump("No hospital services object retrieved!");
            }
            $hs->assignBillObject($this->objBill);
        }

		$total_labchrg   = 0;
		$total_radchrg   = 0;
		$total_otherchrg = 0;

		$hsp_services = $this->objBill->getSrvBenefits();

//		$totalServConfineCoverage = $this->objBill->getSrvConfineCoverage();
        $totalServConfineCoverage = $this->objBill->getAppliedHSCoverage();
		$totalServCharge = $this->objBill->getTotalSrvCharge();
        //added by jasper 05/21/2013
        if (!$this->objBill->isSponsoredMember()) {
            $ndiscount = $this->objBill->getBillAreaDiscount('HS');
        } else {
            $ndiscount = 0.00;
        }


		$this->Ln(2);
//		$this->Cell(GEN_COL01, 4, "", "", 0, 'C');

		if (is_array($hsp_services) && (count($hsp_services) > 0)) {
			$this->Cell($this->in2mm(GEN_COL02), 4,"X-Ray, Lab & Others", "", 1, '');

//			$this->Cell(GEN_COL02_D, 4, "", "", 0, 'C');
			$this->Cell(GEN_COL01, 4, "", "", 0, 'C');
			$this->Cell($this->in2mm(GEN_COL02)-6, 4, "Laboratory", "", ($this->IsDetailed)?1:0, '');
//			$this->Cell(GEN_COL02_D, 4, "Laboratory", "", ($this->IsDetailed)?1:0, '');
			#$this->Cell($this->in2mm(2.8), 4,"Laboratory", "", 1, '');

			foreach ($hsp_services as $key=>$hsValue) {
				$servPrice  = $hsValue->getServPrice();
				$servCharge = $hsValue->getServQty() * $hsValue->getServPrice();

				if ($hsValue->getServProvider()=='LB') {
					$total_labchrg += $servCharge;

					if ($this->IsDetailed) {
//						$this->Cell(GEN_COL02_D2, 4, "", "", 0, '');
						$this->Cell(GEN_COL01, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-8, 4, $hsValue->getServiceDesc(), "", 1, '');
//						$this->Cell(GEN_COL02_D3, 4, "", "", 0, '');
						$this->Cell(GEN_COL01 + 3.5, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-10, 4, $hsValue->getServQty()." @ ".number_format($servPrice, 2, '.', ',')."  (".$hsValue->getGroupDesc().")", "", 0, '');
						$this->Cell(COL_MID - 1.75, 4, "", "", 0, '');
                        //edited by jasper 03/25/2013
						//$this->Cell(COL03_WIDTH, 4, number_format($servCharge, 2, '.', ','), "", 1, 'R');
                        $this->Cell(COL03_WIDTH + 4, 4, number_format($servCharge, 2, '.', ','), "", 1, 'R');
					}
				}
			}
			if (!$this->IsDetailed) {
				$this->Cell(COL_MID + 1.75, 4, "", "", 0, '');
				$this->Cell(COL03_WIDTH, 4, number_format($total_labchrg, 2, '.', ','), "", 1, 'R');
			}
//		}
//		else {
//			$this->Cell(15, 4, "", "", 0, '');
//			$this->Cell(67, 4, "No laboratory services charged!", "", 0, '');
//			$this->Cell(10, 4, "", "", 0, '');
//			$this->Cell(26, 4, "0.00", "", 0, 'R');
//			$this->Cell(8, 4, "", "", 0, '');
//			$this->Cell(24, 4, "0.00", "", 0, 'R');
//			$this->Cell(6, 4, "", "", 0, '');
//			$this->Cell(26, 4, "0.00", "", 1, 'R');
//		}

//		if (is_array($hspRADServicesList)) {

			reset($hsp_services);

//			$this->Cell(GEN_COL02_D, 4, "", "", 0, 'C');
			$this->Cell(GEN_COL01, 4, "", "", 0, 'C');
			$this->Cell($this->in2mm(GEN_COL02)-6, 4, "Radiology", "", ($this->IsDetailed)?1:0, '');

			foreach ($hsp_services as $key=>$hsValue) {
				$servPrice  = $hsValue->getServPrice();
				$servCharge = $hsValue->getServQty() * $hsValue->getServPrice();

				if ($hsValue->getServProvider()=='RD') {
					$total_radchrg += $servCharge;

					if ($this->IsDetailed) {
//						$this->Cell(GEN_COL02_D2, 4, "", "", 0, '');
						$this->Cell(GEN_COL01, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-8, 4, $hsValue->getServiceDesc(), "", 1, '');
//						$this->Cell(GEN_COL02_D3, 4, "", "", 0, '');
						$this->Cell(GEN_COL01 + 3.5, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-10, 4, $hsValue->getServQty()." @ ".number_format($servPrice, 2, '.', ',')."  (".$hsValue->getGroupDesc().")", "", 0, '');
						$this->Cell(COL_MID - 1.75, 4, "", "", 0, '');
                        //edited by jasper 03/25/2013
                        $this->Cell(COL03_WIDTH, 4, number_format($servCharge, 2, '.', ','), "", 1, 'R');
						$this->Cell(COL03_WIDTH + 4, 4, number_format($servCharge, 2, '.', ','), "", 1, 'R');
						$this->Ln(2);
					}
				}
			}
			if (!$this->IsDetailed) {
				$this->Cell(COL_MID + 1.75, 4, "", "", 0, '');
				$this->Cell(COL03_WIDTH, 4, number_format($total_radchrg, 2, '.', ','), "", 1, 'R');
			}

			reset($hsp_services);

//			$this->Cell(GEN_COL02_D, 4, "", "", 0, 'C');
			$this->Cell(GEN_COL01, 4, "", "", 0, 'C');
			$this->Cell($this->in2mm(GEN_COL02)-6, 4, "Others", "", ($this->IsDetailed)?1:0, '');

			foreach ($hsp_services as $key=>$hsValue) {
				$servPrice  = $hsValue->getServPrice();
				$servCharge = $hsValue->getServQty() * $hsValue->getServPrice();

				if (($hsValue->getServProvider() == 'OA') || ($hsValue->getServProvider() == 'SU') || ($hsValue->getServProvider() == 'MS')) {
					$total_otherchrg += $servCharge;

					if ($this->IsDetailed) {
//						$this->Cell(GEN_COL02_D2, 4, "", "", 0, '');
						$this->Cell(GEN_COL01, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-8, 4, $hsValue->getServiceDesc(), "", 1, '');
//						$this->Cell(GEN_COL02_D3, 4, "", "", 0, '');
						$this->Cell(GEN_COL01 + 3.5, 4, "", "", 0, '');
						$this->Cell($this->in2mm(GEN_COL02)-10, 4, $hsValue->getServQty()." @ ".number_format($servPrice, 2, '.', ',')."  (".$hsValue->getGroupDesc().")", "", 0, '');
						$this->Cell(COL_MID - 1.75, 4, "", "", 0, '');
                        //edited by jasper 03/25/2013
                        //$this->Cell(COL03_WIDTH, 4, number_format($servCharge, 2, '.', ','), "", 1, 'R');
						$this->Cell(COL03_WIDTH + 4, 4, number_format($servCharge, 2, '.', ','), "", 1, 'R');
						$this->Ln(2);
					}
				}
			}
			if (!$this->IsDetailed) {
				$this->Cell(COL_MID + 1.75, 4, "", "", 0, '');
				$this->Cell(COL03_WIDTH, 4, number_format($total_otherchrg, 2, '.', ','), "", 1, 'R');
			}


			if (isset($_GET['fix']))
				$totalServConfineCoverage = $totalServCharge;

			$excess = $totalServCharge - $ndiscount - $totalServConfineCoverage;

//			$this->Cell(22, 4, "", "", 0, '');
//			$this->Cell(GEN_COL01 + $this->in2mm(GEN_COL02), 4, "", "", 0, 'R');
			$this->Cell($this->in2mm(GEN_COL02), 4, "", "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL03_WIDTH, 4, str_repeat("-", 25), "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL04_WIDTH, 4, str_repeat("-", 20), "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL05_WIDTH, 4, str_repeat("-", 23), "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL06_WIDTH, 4, str_repeat("-", 23), "", 1, 'R');

//			$this->Cell(22, 4, "", "", 0, '');
//			$this->Cell(GEN_COL01 + $this->in2mm(GEN_COL02), 4, "Sub-Total (Hosp. Services)", "", 0, 'R');
			$this->Cell($this->in2mm(GEN_COL02), 4, "Sub-Total (X-Ray, Lab, Others)", "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL03_WIDTH, 4, number_format($totalServCharge, 2, '.', ','), "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL04_WIDTH, 4, number_format($ndiscount, 2, '.', ','), "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL05_WIDTH, 4, number_format($totalServConfineCoverage, 2, '.', ','), "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL06_WIDTH, 4, number_format($excess, 2, '.', ','), "", 0, 'R');
		}
		else {
			$this->Cell($this->in2mm(GEN_COL02), 4,"X-Ray, Lab & Others", "", 0, '');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL03_WIDTH, 4, "0.00", "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL04_WIDTH, 4, "0.00", "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL05_WIDTH, 4, "0.00", "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL06_WIDTH, 4, "0.00", "", 0, 'R');
		}
		$this->Ln(4);

		$this->totalCharge[HS_AREA]   = $totalServCharge;
		$this->totalDiscount[HS_AREA] = $ndiscount;
		$this->totalCoverage[HS_AREA] = $totalServConfineCoverage;
		$this->totalExcess[HS_AREA]   = $excess;

	}// end of function LaboratoryData

	#added by VAN 02-13-08

	function getRoomTypeAttachedInfo($type_nr, $src, $accHistArray, &$typeDesc, &$sRooms) {
		$sDesc  = '';
		$sRooms = '';
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
		$typeDesc = $sDesc;
	}

	function getAccommodationData() {
        if ($this->brecalc && !$this->b_acchist_gathered) {
            $this->objBill->getAccommodationHist(); // set AccommodationHist
            $this->objBill->getRoomTypeBenefits(); // set Room type Benefits
            $this->objBill->getConfineBenefits('AC');
        }
        else {
            $ac = unserialize($_SESSION['billobject']['ac']);
            if (!($ac instanceof ACBill)) {
                    $var_dump("No accommodation object retrieved!");
            }
            $ac->assignBillObject($this->objBill);
        }

//		if (!$this->b_acchist_gathered) {
//			$this->objBill->getAccommodationHist(); // set AccomodationHist
//			$this->objBill->getRoomTypeBenefits(); // set Room type Benefits
//			$this->objBill->getConfineBenefits('AC');
//		}

		$accHistArray= $this->objBill->getAccHist(); //get accommodation object
		$accBenefitsArray = $this->objBill->getRmTypeBenefits(); //get accommodation benefits coverage
		$total_confine_coverage = $this->objBill->getAccConfineCoverage();

        //added by jasper 05/21/2013
        if (!$this->objBill->isSponsoredMember()) {
            $ndiscount = $this->objBill->getBillAreaDiscount('AC');
        } else {
            $ndiscount = 0.00;
        }

		#Display Accommodation arguments
		$total = 0;

//		$this->Cell(GEN_COL01, 4, "", "", 0, 'C');
		$this->Cell($this->in2mm(GEN_COL02), 4,"Accommodation", "", (count($accBenefitsArray) > 0 ? 1 : 0), '');

		if (is_array($accBenefitsArray) && (count($accBenefitsArray) > 0)) {
			foreach ($accBenefitsArray as $key => $accBen) {
				$total_charge = sprintf('%01.2f', $accBen->getActualCharge()); //Actual Price
				$days_count = $accBen->days_count;
				$excess_hr = $accBen->excess_hours;

				$total += $total_charge;

				$this->getRoomTypeAttachedInfo($accBen->type_nr, $accBen->getSource(), $accHistArray, $type_desc, $sRooms);

				if ($days_count>1)
					$daylabel = 'days';
				else
					$daylabel = 'day';

				if ($excess_hr>1)
					$timelabel = 'hrs';
				else
					$timelabel = 'hr';

                if ($this->ishousecase) {
                    $type_desc = preg_replace("/pay[\s]*ward/i", "Ward", $type_desc);
                }
                //updatd by jane 10/30/2013 MS811 do not show room accommodation with Zero day
                if($days_count > 0)
                {
    //				$this->Cell(GEN_COL02_D, 4, "", "", 0, '');		// Indention of details ...
					$this->Cell(GEN_COL01, 4, "", "", 0, '');
					$this->Cell($this->in2mm(GEN_COL02)-6, 4, $type_desc."-".$sRooms, "", 1, '');
	//				$this->Cell(GEN_COL02_D2, 4, "", "", 0, '');
					$this->Cell(GEN_COL01 + 3.5, 4, "", "", 0, '');
					$this->Cell($this->in2mm(GEN_COL02)-8, 4, $days_count." ".$daylabel." & ".$excess_hr." ".$timelabel." @ ".number_format($accBen->room_rate, 2, '.', ','), "", 0, '');
					if (count($accBenefitsArray) > 1) {
						$this->Cell(COL_MID, 4, "", "", 0, '');
						$this->Cell(COL03_WIDTH, 4, number_format($total_charge, 2, '.', ','), "", 1, 'R');
					}
                }
	
			}
		}

		$excess = $total - $ndiscount - $total_confine_coverage;

//		$this->Cell(22, 4, "", "", 0, '');
//		$this->Cell(60, 4, "", "", 0, 'R');
		if (count($accBenefitsArray) > 1) {
//			$this->Cell(GEN_COL01 + $this->in2mm(GEN_COL02), 4, " ", "", 0, 'R');
			$this->Cell($this->in2mm(GEN_COL02), 4, " ", "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL03_WIDTH, 4, str_repeat("-", 25), "", 0, 'R');

			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL04_WIDTH, 4, str_repeat("-", 20), "", 0, 'R');

			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL05_WIDTH, 4, str_repeat("-", 23), "", 0, 'R');

			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL06_WIDTH, 4, str_repeat("-", 23), "", 1, 'R');

//			$this->Cell(GEN_COL01 + $this->in2mm(GEN_COL02), 4, "Sub-Total (Accomodation)", "", 0, 'R');
			$this->Cell($this->in2mm(GEN_COL02), 4, "Sub-Total (Accomodation)", "", 0, 'R');
		}

		$this->Cell(COL_MID, 4, "", "", 0, '');
		$this->Cell(COL03_WIDTH + .5, 4, number_format($total, 2, '.', ','), "", 0, 'R');

		$this->Cell(COL_MID, 4, "", "", 0, '');
		$this->Cell(COL04_WIDTH, 4, number_format($ndiscount, 2, '.', ','), "", 0, 'R');

		$this->Cell(COL_MID, 4, "", "", 0, '');
		$this->Cell(COL05_WIDTH, 4, number_format($total_confine_coverage, 2, '.', ','), "", 0, 'R');

		$this->Cell(COL_MID, 4, "", "", 0, '');
		$this->Cell(COL06_WIDTH, 4, number_format($excess, 2, '.', ','), "", 0, 'R');

		$this->Ln(4);

		$this->totalCharge[AC_AREA] = $total;
		$this->totalDiscount[AC_AREA] = $ndiscount;
		$this->totalCoverage[AC_AREA] = $total_confine_coverage;
		$this->totalExcess[AC_AREA] = $excess;
	}// end of function getAccommodationData

	function getOpsCharges() {
//		$this->objBill->getOpsList();		// Get list of operations applied to patient.
//		$this->objBill->getOpBenefits();	// Get summary of operations and corresponding insurance coverage.
//		$this->objBill->getConfineBenefits('OR');

        if ($this->brecalc) {
            $this->objBill->getOpBenefits();
            $this->objBill->getConfineBenefits('OR');//Added by Jarel 05/07/2013
        }
        else {
            $op = unserialize($_SESSION['billobject']['op']);
            if (!($op instanceof OPBill)) {
                    $var_dump("No operating room object retrieved!");
            }
            $op->assignBillObject($this->objBill);
        }

		$hspOpsList       = $this->objBill->getOpsConfineBenefits();
//		$totalOpsCoverage = $this->objBill->getOpsConfineCoverage();
		$totalOpsCharge   = $this->objBill->getTotalOpCharge();
//		$this->objBill->initOpsConfineCoverage();

        //added by jasper 05/21/2013
        if (!$this->objBill->isSponsoredMember()) {
            $ndiscount = $this->objBill->getBillAreaDiscount('OR');
        } else {
            $ndiscount = 0.00;
        }

		#Display Operation/Procedure Charges ...
		$this->Ln(2);
//		$this->Cell(GEN_COL01, 4, "", "", 0, 'C');
		$this->Cell($this->in2mm(GEN_COL02), 4,"Operating/Delivery Room", "", ($this->IsDetailed && (count($hspOpsList) > 0) ? 1 : 0), '');

		if (is_array($hspOpsList) && (count($hspOpsList) > 0)) {
			$bShow = count($hspOpsList) > 1;

			foreach ($hspOpsList as $key => $opsValue) {
				$opsCharge  = number_format($opsValue->getOpCharge(), 2, '.', ',');
//				$this->objBill->getConfineBenefits('OR', $opsValue->getOpCodePerformed());

				if ($this->IsDetailed) {
//					$this->Cell(GEN_COL02_D, 4, "", "", 0, '');
					$this->Cell(GEN_COL01, 4, "", "", 0, '');
					$this->Cell($this->in2mm(GEN_COL02)-6, 4, $opsValue->getOpDesc(), "", 1, '');
//					$this->Cell(GEN_COL02_D2, 4, "", "", 0, '');
					$this->Cell(GEN_COL01 + 3.5, 4, "", "", 0, '');
					$this->Cell($this->in2mm(GEN_COL02)-8, 4, "(".$opsValue->getOpCode().") : RVU ".$opsValue->getOpRVU(), "", 0, '');
					if ($bShow) {
						$this->Cell(COL_MID, 4, "", "", 0, '');
						$this->Cell(COL03_WIDTH, 4, $opsCharge, "", 1, 'R');
					}
				}
			}
		}

		$totalOpsCoverage = $this->objBill->getOpsConfineCoverage();
		$totalExcess = $totalOpsCharge - $ndiscount - $totalOpsCoverage;

		if ($this->IsDetailed && (count($hspOpsList) > 1)) {
//			$this->Cell(22, 4, "", "", 0, '');
//			$this->Cell(GEN_COL01 + $this->in2mm(GEN_COL02), 4, "", "", 0, 'R');
			$this->Cell($this->in2mm(GEN_COL02), 4, "", "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL03_WIDTH, 4, str_repeat("-", 25), "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL04_WIDTH, 4, str_repeat("-", 20), "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL05_WIDTH, 4, str_repeat("-", 23), "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL06_WIDTH, 4, str_repeat("-", 23), "", 1, 'R');

//			$this->Cell(22, 4, "", "", 0, '');
//			$this->Cell(GEN_COL01 + $this->in2mm(GEN_COL02), 4, "Sub-Total (Operation/Procedures)", "", 0, 'R');
			$this->Cell($this->in2mm(GEN_COL02), 4, "Sub-Total (O.R./Delivery)", "", 0, 'R');
		}

		$this->Cell(COL_MID, 4, "", "", 0, '');
		$this->Cell(COL03_WIDTH, 4, number_format($totalOpsCharge, 2, '.', ','), "", 0, 'R');
		$this->Cell(COL_MID, 4, "", "", 0, '');
		$this->Cell(COL04_WIDTH, 4, number_format($ndiscount, 2, '.', ','), "", 0, 'R');
		$this->Cell(COL_MID, 4, "", "", 0, '');
		$this->Cell(COL05_WIDTH, 4, number_format($totalOpsCoverage, 2, '.', ','), "", 0, 'R');
		$this->Cell(COL_MID, 4, "", "", 0, '');
		$this->Cell(COL06_WIDTH, 4, number_format($totalExcess, 2, '.', ','), "", 0, 'R');
		$this->Ln(4);

		$this->totalCharge[OP_AREA]   = $totalOpsCharge;
		$this->totalDiscount[OP_AREA] = $ndiscount;
		$this->totalCoverage[OP_AREA] = $totalOpsCoverage;
		$this->totalExcess[OP_AREA]   = $totalExcess;
	}

	function getMiscellaneousCharges() {
        if ($this->brecalc) {
//            $this->objBill->getMiscellaneousChrgsList();
            $this->objBill->getMiscellaneousBenefits();
            $this->objBill->getConfineBenefits('XC');
        }
        else {
            $xc = unserialize($_SESSION['billobject']['xc']);
            if (!($xc instanceof XCBill)) {
                    $var_dump("No miscellaneous charges object retrieved!");
            }
            $xc->assignBillObject($this->objBill);
        }

		$hspMscList = $this->objBill->getMiscBenefits(); //listing

		$totalMscConfineCoverage = $this->objBill->getMscConfineCoverage();
		$totalMscCharge          = $this->objBill->getTotalMscCharge();

        //added by jasper 05/21/2013
        if (!$this->objBill->isSponsoredMember()) {
            $ndiscount = $this->objBill->getBillAreaDiscount('XC');
        } else {
            $ndiscount = 0.00;
        }

		#Display Miscellaneous Charges ...
		$this->Ln(2);
//		$this->Cell(GEN_COL01, 4, "", "", 0, 'C');
		$this->Cell($this->in2mm(GEN_COL02), 4,"Miscellaneous", "", ($this->IsDetailed && (count($hspMscList) > 0) ? 1 : 0), '');

		$n = 0;

		if (is_array($hspMscList) && (count($hspMscList) > 0)) {
			$bShow = count($hspMscList) > 1;

			foreach ($hspMscList as $key => $mscValue) {
				$mscCharge  = number_format($mscValue->getTotalMiscChrg(), 2, '.', ',');

				if ($this->IsDetailed) {
//					$this->Cell(GEN_COL02_D, 4, "", "", 0, '');
					$this->Cell(GEN_COL01, 4, "", "", 0, '');
					$this->Cell($this->in2mm(GEN_COL02)-6, 4, $mscValue->getMiscName()." (".$mscValue->getMiscDesc().")", "", 0, '');
//					$this->Cell(20, 4, "", "", 0, '');
//					$this->Cell(63, 4, $mscValue->getMiscQty(), "", 0, '');
					if ($bShow) {
						$this->Cell(COL_MID + 1.75, 4, "", "", 0, '');
						$this->Cell(COL03_WIDTH, 4, $mscCharge, "", 1, 'R');
					}
					else
						$n = 1.75;
				}
			}
		}

		$totalExcess = $totalMscCharge - $ndiscount - $totalMscConfineCoverage;

		if ($this->IsDetailed && (count($hspMscList) > 1)) {
//			$this->Cell(22, 4, "", "", 0, '');
//			$this->Cell(GEN_COL01 + $this->in2mm(GEN_COL02), 4, "", "", 0, 'R');
			$this->Cell($this->in2mm(GEN_COL02), 4, "", "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL03_WIDTH, 4, str_repeat("-", 25), "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL04_WIDTH, 4, str_repeat("-", 20), "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL05_WIDTH, 4, str_repeat("-", 23), "", 0, 'R');
			$this->Cell(COL_MID, 4, "", "", 0, '');
			$this->Cell(COL06_WIDTH, 4, str_repeat("-", 23), "", 1, 'R');

//			$this->Cell(22, 4, "", "", 0, '');
//			$this->Cell(GEN_COL01 + $this->in2mm(GEN_COL02), 4, "Sub-Total (Miscellaneous)", "", 0, 'R');
			$this->Cell($this->in2mm(GEN_COL02), 4, "Sub-Total (Miscellaneous)", "", 0, 'R');
		}

		$this->Cell(COL_MID + $n, 4, "", "", 0, '');
		$this->Cell(COL03_WIDTH, 4, number_format($totalMscCharge, 2, '.', ','), "", 0, 'R');
		$this->Cell(COL_MID, 4, "", "", 0, '');
		$this->Cell(COL04_WIDTH, 4, number_format($ndiscount, 2, '.', ','), "", 0, 'R');
		$this->Cell(COL_MID, 4, "", "", 0, '');
		$this->Cell(COL05_WIDTH, 4, number_format($totalMscConfineCoverage, 2, '.', ','), "", 0, 'R');
		$this->Cell(COL_MID, 4, "", "", 0, '');
		$this->Cell(COL06_WIDTH, 4, number_format($totalExcess, 2, '.', ','), "", 0, 'R');
		$this->Ln(4);

		$this->totalCharge[XC_AREA]   = $totalMscCharge;
		$this->totalDiscount[XC_AREA] = $ndiscount;
		$this->totalCoverage[XC_AREA] = $totalMscConfineCoverage;
		$this->totalExcess[XC_AREA]   = $totalExcess;
	}

	function getPersonInfo($encounter=''){
		global $db;

		if(!empty($encounter)){
			$this->encounter_nr = $encounter;
		}

// ---- Commented out by LST - 03102008 ---------------
//		$sql = "SELECT ce.*, cp.name_first, cp.name_middle, cp.name_last,
//       				cp.date_birth,
//						sb.brgy_name, sm.mun_name, sm.zipcode,
//						sp.prov_name, sr.region_name, sr.region_desc,  cd.id, cd.name_formal as dept_name,
//						ce.current_room_nr as room_no,cw.ward_id, cw.name as ward_name
//					FROM care_encounter AS ce
//						INNER JOIN care_person AS cp ON ce.pid = cp.pid
//							INNER JOIN seg_barangays AS sb ON cp.brgy_nr = sb.brgy_nr
//							INNER JOIN seg_municity AS sm ON sb.mun_nr = sm.mun_nr
//								  INNER JOIN seg_provinces AS sp ON sm.prov_nr = sp.prov_nr
//								  INNER JOIN seg_regions AS sr ON sp.region_nr = sr.region_nr
//							INNER JOIN care_department AS cd ON cd.nr = ce.consulting_dept_nr
//							INNER JOIN care_ward AS cw ON ce.current_ward_nr = cw.nr
//					WHERE ce.encounter_nr ='".$this->encounter_nr."'";

		$sql = "SELECT ce.*, cp.name_first, cp.name_middle, cp.name_last,
							cp.date_birth,
						sb.brgy_name, cp.street_name, sm.mun_name, sm.zipcode,
						sp.prov_name, sr.region_name, sr.region_desc,  cd.id, cd.name_formal as dept_name,
						ce.current_room_nr as room_no,cw.ward_id, cw.name as ward_name
					FROM (care_encounter AS ce
						INNER JOIN care_person AS cp ON ce.pid = cp.pid)
							left JOIN seg_barangays AS sb ON cp.brgy_nr = sb.brgy_nr
							left JOIN seg_municity AS sm ON cp.mun_nr = sm.mun_nr
									left JOIN seg_provinces AS sp ON sm.prov_nr = sp.prov_nr
									left JOIN seg_regions AS sr ON sp.region_nr = sr.region_nr
							left JOIN care_department AS cd ON cd.nr = ce.current_dept_nr
							left JOIN care_ward AS cw ON ce.current_ward_nr = cw.nr
					WHERE ce.encounter_nr ='".$this->encounter_nr."'";

		if($this->personData = $db->Execute($sql)){
			if($this->personData->RecordCount()){
				return $this->personData;
			}else{
				return FALSE;
			}
		}else{
			echo 'SQL - '.$sql;
		}
	}// end of getPersonInfo

	function ReportOut(){
		$this->Output();
	}

	function trimAddress($street, $brgy, $mun, $prov, $zipcode, &$s_addr1, &$s_addr2, &$s_addr3){
		$address = trim($street);
		$address1 = (!empty($address) && !empty($brgy)) ?  trim($address.", ".$brgy) : trim($address." ".$brgy);
		$s_addr1 = $address1;

//		$address2 =  (!empty($address1) && !empty($mun)) ? trim($address1.", ".$mun) : trim($address1." ".$mun);
		$address2 = trim($mun);
		$address3 =  (!empty($address2) && !empty($zipcode))? trim($address2." ".$zipcode) : $address2." ";

		$address4 = (!empty($address3) && !empty($prov))? trim($address3.", ".$prov) : trim($address3." ".$prov);
		$s_addr2  = $address4;
		$s_addr3  = '';

//		return $address4;
	}// end of  function trimAddress

	function setEncounter_nr($encounter){
		$this->encounter_nr = $encounter;
	}

	/*function setObjBilling(){
		$this->objBill = new Billing($this->encounter_nr);
	}*/

	function setFontSize($size){
		$this->DEFAULT_FONTSIZE = $size;
	}

	function setFontType($type){
		$this->DEFAULT_FONTTYPE = $type;
	}

	function setFontStyle($style){
		$this->DEFAULT_FONTSTYLE = $style;
	}

	function setBorder($border){
		$this->WBORDER = $border;
	}

	function setAlignment($alignment){
		$this->ALIGNMENT = $alignment;
	}

	function setNewLine($newline){
		$this->NEWLINE = $newline;
	}

	function setReportTitle($title){
		$this->reportTitle = $title;
	}

	function in2mm($inches){
//		return $inches * (0.35/(1/72));
				return $inches * 25.4;
	}

}//end of class Bill_Pdf

# ----------------------------------------------------------------------------------------

if(isset($_GET['pid']) && $_GET['pid']) $pid = $_GET['pid'];
if(isset($_GET['encounter_nr']) && $_GET['encounter_nr']) $encounter_nr = $_GET['encounter_nr'];

# --- Added by LST 03102008 -- to make bill date consistent with bill date in browser window ...
if (isset($_GET['from_dt']) && $_GET['from_dt'])
	$frm_dte = strftime("%Y-%m-%d %H:%M:%S", $_GET['from_dt']);
else
	$frm_dte = "0000-00-00 00:00:00";

if (isset($_GET['bill_dt']) && $_GET['bill_dt'])
//	$bill_dte = $_GET['bill_dt'];
	$bill_dte = strftime("%Y-%m-%d %H:%M:%S", $_GET['bill_dt']);
else
	$bill_dte = "0000-00-00 00:00:00";

if (isset($_GET['nr']))
		$old_bill_nr = $_GET['nr'];
else
		$old_bill_nr = '';

//Instantiate BillPDF class
$pdfBill =  new BillPDF($encounter_nr, $bill_dte, $frm_dte, $old_bill_nr, (isset($_GET['rcalc']) && ($_GET['rcalc'] == '1')), $_GET['deathdate']);

$encobj = new Encounter();
$pdfBill->isphic = $encobj->isPHIC($encounter_nr);
$pdfBill->ishousecase = $encobj->isHouseCase($encounter_nr);

$pdfBill->objBill->getAccommodationType();

$s_accommodation = strtoupper($pdfBill->objBill->getAccommodationDesc());
$pdfBill->setReportTitle(($pdfBill->IsDetailed ? "DETAILED " : "")."STATEMENT OF ACCOUNT".($s_accommodation == '' ? " - NO ACCOMMODATION" : ($pdfBill->ishousecase ? "" : " - ".$s_accommodation)));
$pdfBill->ReportTitle();

//print patient informatin
$pdfBill->PersonInfo();
//print title bar
$pdfBill->TitleHeader('summary');
#$pdfBill->TitleHeader('detailed');

//print data
$pdfBill->PrintData();
$pdfBill->Sub_Total();
$pdfBill->Professional_Fee();

$pdfBill->Totals();
//print to pdf format
$pdfBill->ReportOut();
?>
