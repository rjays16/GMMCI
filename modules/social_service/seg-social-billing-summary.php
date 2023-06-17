<?php
#Edited By Jarel 01/26/2013
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
    var $netAmountBill = 0;
    var $grossAmountBill= 0;

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

		 $pg_size = array($this->in2mm(8.5), $this->in2mm(6.5));                 // Default to long bond paper --- modified by LST - 04.13.2009
		 $this->FPDF("P","mm", $pg_size);
		 $this->AliasNbPages();
		 $this->AddPage("P");

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
         if (!($this->objBill->isForFinalBilling())) {
            $this->Image('../../gui/img/logos/tentativebill.jpg',30, 50, 150,150);
         }
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
			$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
			$row['hosp_addr1']   = "Quezon Ave., Digos City";
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

			$billdte       = strftime("%b %d, %Y %I:%M %p", strtotime($this->bill_date));
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

			#Last billing ...
			$lastbilldte = $this->objBill->getActualLastBillDte();
			if ( ($lastbilldte == "0000-00-00 00:00:00") && !$this->objBill->getIsCoveredByPkg() )
				$this->Cell($this->in2mm(4), 4, "( ".$sward_name." )".($sCaseType == '' ? '' : " - ".$sCaseType), "", 1 ,'');
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
		}
	}//end of PersonInfo



	function getBillingDetails()
	{
		global $db;
		$strSQL = "SELECT fn_billing_compute_net_amount(".$db->qstr($this->bill_ref_nr).") AS net, fn_billing_compute_gross_amount(".$db->qstr($this->bill_ref_nr).") as gross";
				if ($result = $db->Execute($strSQL)) {
					if ($result->RecordCount()) {
						$row = $result->FetchRow();
                $this->netAmountBill = $row['net'];
                $this->grossAmountBill = $row['gross'];
						}
					}
				}

	function DisplayData(){
		$this->SetFont("Times", "", "11");
		$this->Ln(4);
		$this->Cell($this->in2mm(GEN_COL02), 4, "T O T A L", "", 0, '');
		$totalExcess = 0;

		$this->getBillingDetails();
		$total = $this->grossAmountBill;
		$totalDue = $this->netAmountBill;
		$totalDiscount = $total - $totalDue;

		$this->Cell(COL06_WIDTH, 4, number_format($total, 2, '.', ','), "", 1, 'R');

		if ($this->objBill->isSponsoredMember() || $this->objBill->checkIfPHS() || $this->objBill->isHSM()) {
			$this->SetFont("Times", "", "11");
			$this->Ln(4);
			if($this->objBill->isHSM()) {
            	$label = "HOSPITAL SPONSORED MEMBER";
            } elseif ($this->objBill->isSponsoredMember()) {
            	$label = "SPONSORED - NO BALANCE BILLING";
            } else {
            	$label = "INFIRMARY DISCOUNT";
            }
			$this->Cell($this->in2mm(GEN_COL02), 4, $label, "", 0, '');
			$this->Cell(COL06_WIDTH, 4, number_format($total, 2, '.', ','), "", 1, 'R');
			$this->SetFont("Times", "B", "11");
			$this->Ln(4);
			$this->Cell($this->in2mm(GEN_COL02), 4, "Total Amount Due", "", 0, '');
			$this->Cell(COL06_WIDTH, 4, "0.00", "", 1, 'R');
		}else{
			$this->SetFont("Times", "", "11");
			$this->Ln(4);
			$this->Cell($this->in2mm(GEN_COL02), 4, "Less Classification Discount", "", 0, '');
			$this->Cell(COL06_WIDTH, 4, number_format($totalDiscount, 2, '.', ','), "", 1, 'R');
			$this->SetFont("Times", "B", "11");
			$this->Ln(4);
			$this->Cell($this->in2mm(GEN_COL02), 4, "Total Amount Due", "", 0, '');
			$this->Cell(COL06_WIDTH, 4, number_format($totalDue, 2, '.', ','), "", 1, 'R');
        }

        

		$this->Ln(16);
		$this->SetFont("Times", "B", "11");
		$this->Cell($this->in2mm(GEN_COL02), 4, "Social Worker: ".$_SESSION['sess_user_name'], "", 0, '');
	}


	function getPersonInfo($encounter=''){
		global $db;

		if(!empty($encounter)){
			$this->encounter_nr = $encounter;
		}

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

$pdfBill->DisplayData();
//print to pdf format
$pdfBill->ReportOut();
?>
