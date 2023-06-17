<?php
require('./roots.php');

require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'/classes/fpdf/pdf.class.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/billing/class_billing.php');

include_once($root_path.'include/care_api_classes/class_person.php');
include_once($root_path.'include/care_api_classes/class_encounter.php');


if(isset($_GET['pid']) && $_GET['pid']) $pid = $_GET['pid'];
if(isset($_GET['encounter_nr']) && $_GET['encounter_nr']) $encounter_nr = $_GET['encounter_nr'];

$objPerson = new Person;
$objEncounter = new Encounter;
//Extract patient information 
if($pid){
	if(!($basicInfo = $objPerson->getAllInfoArray($pid))){
		echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
		exit();
	}
	extract($basicInfo);
}else{
	echo '<em class="warn"> Sorry but the page cannot be displayed! <br> Invalid PID!</em>';
	exit();
}

if($encounter_nr){
	if(!($encounterInfo = $objEncounter->getEncounterInfo($encounter_nr))){
		echo '<em class="warn"> Sorry but the page cannot be displayed!"</em>';
		exit();
	}
	extract($encounterInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed!"</em>';
	exit();
}

/*
	SELECT enc.current_ward_nr, cp.pid, enc.encounter_nr, 
	cp.name_last, cp.name_first, cp.name_2, cp.name_3, cp.name_middle,
	enc.encounter_date AS er_opd_datetime, 
	dept.name_formal,
	cp.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,
	cp.phone_1_nr, cp.phone_2_nr, cp.cellphone_1_nr, cp.cellphone_2_nr, 
	cp.sex, cp.civil_status, cp.blood_group,
	IF(fn_calculate_age(enc.encounter_date,cp.date_birth),fn_get_age(enc.encounter_date,cp.date_birth),'') AS age,
	IF(fn_calculate_age(enc.encounter_date,date_birth),date_birth,'') AS date_birth,							 
	cp.place_birth,
	sc.country_name AS citizenship, 
	sreli.religion_name AS religion, 
	so.occupation_name AS occupation, 
	cp.mother_name, cp.father_name, cp.spouse_name, cp.guardian_name,							
	enc.informant_name, enc.info_address, enc.relation_informant, 
	enc.encounter_type, 
	enc.encounter_class_nr,
	enc.encounter_status,
	enc.official_receipt_nr,
	enc.referrer_dept,
	enc.referrer_dr,
	enc.referrer_diagnosis,
	enc.consulting_dept_nr AS er_opd_admitting_dept_nr,
*/ 

$sAddress = trim($street_name);
if(!empty($sAddress) && !empty($bry_name))
	$sAddress = trim($sAddress.", ".$brgy_name);
else
	$sAddress = trim($sAddress." ".$brgy_name);
if(!empty($sAddress) && !empty($mun_name))
	$sAddress = trim($sAddress.", ".$mun_name);
else
	$sAddress = trim($sAddress." ".$mun_name);
if(!empty($zipcode))
	$sAddress = trim($sAddress." ".$zipcode);
if(!empty($sAddress) && !empty($prov_name))
	$sAddress = trim($sAddress.", ".$prov_name);
else
	$sAddress = trim($sAddress." ".$prov_name);


class convert{
	function in2mm($inches){
		$result = $inches *(0.35 /(1/72));
		return $result;
	}
}

//Instantiate Billing object
$objBill = new Billing($encounter_nr);
//set confinement type
$objBill->getConfinementType();

$objCalc = new convert;
// PDF Creation 
//Initialize pdf object
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage("P");

//Border Setup
$borderYes = "1"; //with border
$borderNo = "0"; //without border
$newLineYes = "1"; //return new line
$newLineNo = "0"; // continue on the same row
$margin = 10; // margin
$space = 2; 

//Font Setup
$fontSizeLabel = 8;
$fontSizeText1 = 10;
$fontSizeText2 = 12;
$fontSizeHeader1 = 14;
$fontSizeHeader2 = 16;

//Font type setup
$fontTypeArial = "Arial";
$fontTypeTimes = "Times";

//START BUILDING PDF 

$pdf->SetTopMargin(1);
$pdf->SetFont($fontTypeTimes , "", $fontSizeText);
$pdf->Cell(185, 4, "CASE NO. ".$encounter_nr ,$borderNo, $newLineYes, 'R'); 


//Letter Header
$pdf->SetFont($fontTypeTimes , "B", $fontSizeHeader);
$pdf->Cell(0, 4, "Republic of the Philippines", $borderNo, $newLineYes, "C");
$pdf->Cell(0, 4, "DEPARTMENT OF HEALTH", $borderNo, $newLineYes, "C");
$pdf->SetFont($fontTypeTimes , "B", $fontSizeHeader1);
$pdf->Cell(0, 4, "Gonzales Maranan Medical Center Incorporated", $borderNo, $newLineYes, "C");
$pdf->SetFont($fontTypeTimes , "", $fontSizeText1);
$pdf->Cell(0, 4, "Digos City", $borderNo, $newLineYes, "C");

$pdf->Ln(2);
$pdf->SetFont($fontTypeTimes , "B", $fontSizeHeader);
$pdf->Cell(0, 4, "BILLING SUMMARY", $borderNo, $newLineYes, "C");


//Patient Information 
$pdf->Ln(4);
//Name
$pdf->SetFont($fontTypeTimes , "B", $fontSizeText2);
$pdf->Cell(20, 4, "Name 	   : ","", 0, '');
$pdf->Cell($objCalc->in2mm(4.8), 4, strtoupper($name_last.', '.$name_first.' '.$name_middle), "", 0, ''); //patient name
//PID
$pdf->SetFont($fontTypeTimes , "B", $fontSizeText2);
$pdf->Cell(12, 4, "PID : ", "", $newLineNo, '');
$pdf->SetFont($fontTypeTimes , "", $fontSizeText2);
$pdf->Cell(12, 4, $pid, "", $newLineYes, '');          // pid 
//Address
$pdf->SetFont($fontTypeTimes , "B", $fontSizeText2);
$pdf->Cell(20, 4, "Address : ", "", 0, '');
$pdf->SetFont($fontTypeTimes , "", $fontSizeText2);
$pdf->Cell($objCalc->in2mm(4.8), 4, $sAddress, "", $newLineNo, '');    // Address
//Department
$pdf->SetFont($fontTypeTimes , "B", $fontSizeText2);
$pdf->Cell(12,4, "Dept.:", "", 0, 'R');
$pdf->SetFont($fontTypeTimes , "", $fontSizeText2);
$pdf->Cell(12, 4, strtoupper($name_formal), "", $newLineYes, '');          //Department  
//Date of admission 
$pdf->SetFont($fontTypeTimes , "B", $fontSizeText2);
$pdf->Cell(30,4, "Date Admitted :", $borderNo, $newLineNo, '');
$pdf->SetFont($fontTypeTimes , "", $fontSizeText2);
$pdf->Cell($objCalc->in2mm(3.5), 4, "11/29/2007", "", $newLineNo, '');     //Date admitted
//Discharge date
$pdf->SetFont($fontTypeTimes , "B", $fontSizeText2);
$pdf->Cell(35, 4, "Date Billed :", "", 0, 'R');
$pdf->SetFont($fontTypeTimes , "", $fontSizeText2);

$pdf->Cell(12, 4, date('m-d-Y'), "", $newLineYes, '');   //Date discharged <- variable here
//Room or Ward of the patient
$pdf->SetFont($fontTypeTimes , "B", $fontSizeText2);
$pdf->Cell(22, 4, "Room No. :", $borderNo, $newLineNo, '');
$pdf->SetFont($fontTypeTimes , "", $fontSizeText2);         
$pdf->Cell($objCalc->in2mm(3.78), 4, "2004", "", $newLineNo, '');  // Room and ward 
//Time billde
$pdf->SetFont($fontTypeTimes , "B", $fontSizeText2);
$pdf->Cell(36, 4, "Time :", $borderNo, $newLineNo, 'R');
$pdf->SetFont($fontTypeTimes , "", $fontSizeText2);
$pdf->Cell(12, 4, date('g:i a'), "", $newLineYes, '');    //Time billed

//First line 
$pdf->Line(12, $objCalc->in2mm(2.3) , $objCalc->in2mm(7.8), $objCalc->in2mm(2.3)); 
//sub Header [Description , Charges, Medicare(PHIC), Excess]
$pdf->SetFont($fontTypeTimes , "B", $fontSizeText2);
$pdf->Ln(3);
$pdf->Cell($objCalc->in2mm(3.5), 4, "Particulars", "", 0, "C");
$pdf->Cell($objCalc->in2mm(1.3),4 , "Actual Charges", "", 0, "C");
$pdf->Cell($objCalc->in2mm(1.3),4 , "PHIC", "", 0, "C");
$pdf->Cell($objCalc->in2mm(1.3), 4, "Excess", "", 1, "C");
$pdf->Ln(3);
$pdf->Line(12, $objCalc->in2mm(2.55) , $objCalc->in2mm(7.8), $objCalc->in2mm(2.55)); 

//line break
$pdf->Ln(2);
//Later modification ( create a pdf billing class ) //11-29-2007 ::mark :P <3
//ACCOMMODATION 
$objBill->getAccommodationHist(); //set AccommodationHist
$objBill->getRoomTypeBenefits(); // set Room type Benefits
$objBill->getConfineBenefits('AC');

$accHistArray = $objBill->accommodation_hist;
$accBenefitsArray = $objBill->acc_roomtype_benefits; // get accommodation benefits coverage
$total_confine_coverage = $objBill->acc_confine_coverage;

if(is_array($accHistArray)){
	$totalAcc = 0;
	foreach($accHistArray as $accHist){
		foreach($accBenefitsArray as $accBen){
			if($accHist->type_nr == $accBen->type_nr){
				$total_charge = round($accBen->getActualCharge());  // Charges
				$total_coverage = round($accBen->getTotalCoverage()); // healthcare coverage
				$excess = $total_charge - $total_coverage;
				$days_count = $accBen->days_count;
				$excess_hr = $accBen->excess_hours;
				
				$totalAcc += $total_charge;
			}
		}
		$type_desc = $accHist->getTypeDesc();
		$room_rate = round($accHist->getRoomRate());
		$type_nr = $accHist->type_nr;
		
		$pdf->SetFont($fontTypeTimes , "", $fontSizeText2);
		#$pdf->Cell($objCalc->in2mm(3), 4, "Accom. ", "", 0, '');
		$pdf->Cell(25, 4, "Accom. ", "", 0, '');
		$pdf->Cell(26, 4, $days_count ." Day/s   @", "", 0, '');
		$pdf->Cell(26, 4, $room_rate,"", 0, '');
		
	}
	
	$totalConfineCoverage = (empty($total_confine_coverage) || $total_confine_coverage == 0) ? "0": $total_confine_coverage;
	$totalAccExcess = ($total_charge - $total_confine_coverage);
	//Chargees 
	$pdf->Cell($objCalc->in2mm(1.35), 4, number_format($totalAcc), "", 0, 'R');   //Charges
	$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalConfineCoverage), "", 0, "R");   //Phic || medicare
	$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalAccExcess), "", $newLineYes, "R");   // accommodation excess
}


//HOSPITAL SERVICES -> Laboratory
$objBill->getServicesList();
$objBill->getServiceBenefits();
$objBill->getConfineBenefits('HS');

$hspSrvConfineBen = $objBill->srv_confine_benefits;
$hspSrvBenifits = $objBill->hsp_service_benefits; // listing of services

$totalSrvConfineCoverage = $objBill->srv_confine_coverage; //reserved
$totalSrvCharge = $objBill->getTotalSrvCharge();           //reserved 

if(is_array($hspSrvBenifits)){
	$totalSrvChargeLab = 0;
	$totalSrvChargeRad = 0;
	foreach($hspSrvBenifits as $hspSrv){
		$srvCharge = 0;
		$srvCharge = $hspSrv->getServCharge();
		if($hspSrv->getServProvider() == 'LD'){
			$totalSrvChargeLab += $srvCharge;
		}elseif($hspSrv->getServProvider() == 'RD'){
			$totalSrvChargeRad += $srvCharge;
		}
	}
}
//NOte laboratories coverage & radiology
if( $totalSrvChargeLab != 0){

	$totalSrvCoverageLad = 0;
	$totalLabExcess = 0;
	//laboratory
	$pdf->SetFont($fontTypeTimes , "", $fontSizeText2);
	$pdf->Cell($objCalc->in2mm(3.5), 4, "LABORATORY", "", 0, '');
	$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalSrvChargeLab), "", 0, 'R');   //charge
	$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalSrvCoverageLad), "", 0, 'R'); // coverage
	$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalLabExcess) , "", $newLineYes, 'R');  //excess
}elseif( $totalSrvChargeRad !=0){
			
	$totalSrvCoverageRad = 0;
	$totalRadExcess = round(($totalSrvChargeRad -$totalSrvCoverageRad));
	//radiology 
	$pdf->SetFont($fontTypeTimes , "", $fontSizeText2);
	$pdf->Cell($objCalc->in2mm(3.102), 4, "RADIOLOGY", "", 0, '');
	$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalSrvChargeRad), "", 0, 'R');    //charge
	$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalSrvCoverageRad), "", 0, 'R');  //coverage
	$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalRadExcess) , "", $newLineYes, 'R'); //excess
	
}

//MEDICINES
$objBill->getMedicinesList();
$objBill->getMedicineBenefits();
$objBill->getConfineBenefits('MS', 'M');

$totalMedConfineCoverage = $objBill->med_confine_coverage;
$medBenefitsArray = $objBill->med_product_benefits;

if(is_array($medBenefitsArray)){
	//$totalMedCharge = 0;
	foreach($medBenefitsArray as $medBen){
		//$totalMedCharge += $medBen->item_charge;
	}
}

$totalMedCharge = $objBill->getTotalMedCharge();
$totalMedExcess =  round(($totalMedCharge - $totalMedConfineCoverage));

$pdf->SetFont($fontTypeTimes , "", $fontSizeText2);
$pdf->Cell($objCalc->in2mm(3.102), 4, "MEDICINES", "", 0, '');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalMedCharge), "", 0, 'R');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalMedConfineCoverage), "", 0, 'R');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalMedExcess), "", $newLineYes, 'R');

//SUPPLIES 
$objBill->getSuppliesList();
$objBill->getSupplyBenefits();
$objBill->getConfineBenefits('MS', 'S');

$totalSupConfineCoverage = round($objBill->sup_confine_coverage);
$supBenefitsArray = $objBill->sup_product_benefits;

if(is_array($supBenefitsArray)){
	foreach($supBenefitsArray as $supBen){
		$supActualPrice = $supBen->item_charge;
		$supItemPrice = $supBen->item_price;
	}
}
$totalSupCharge = round($objBill->getTotalSupCharge());
$totalSupExcess = round(($totalSupCharge - $totalSupConfineCoverage));

$pdf->SetFont($fontTypeTimes , "", $fontSizeText2);
$pdf->Cell($objCalc->in2mm(3.102), 4, "SUPPLIES", "", 0, '');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalSupCharge), "", 0, 'R');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalSupConfineCoverage), "", 0, 'R');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalSupExcess), "", $newLineYes, 'R');



//SUB-TOTAL
$pdf->Cell($objCalc->in2mm(3.102), 4, "", "", 0, '');
for($i= 0 ; $i<=2 ; $i++){
	$c=(!($i>=2))? $c=0: $c="1";
	$pdf->Cell(13, 4, "", "", 0, ''); $pdf->Cell(20, 4, "", "T", $c, 'R');
}
$pdf->Cell($objCalc->in2mm(3.102), 4, "TOTAL", "", 0, '');

$subTotalCharges = ( $totalAcc + $totalSrvChargeLab + $totalSrvChargeRad + 
					 $totalMedCharge + $totalSupCharge + $totalPfCharge);

$subTotalCoverage = ( $totalConfineCoverage + $totalSrvCoverageLad + $totalSrvCoverageRad +
					  $totalMedConfineCoverage + $totalSupConfineCoverage );

$subTotalExcess = ( $totalAccExcess + $totalLabExcess + $totalRadExcess +
					$totalMedExcess + $totalSupExcess );
					
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($subTotalCharges), "", 0, 'R');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($subTotalCoverage), "", 0, 'R');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($subTotalExcess), "", $newLineYes, 'R');

//Less discounts and return items like medicine or supplies
$pdf->Ln(3);
$pdf->Cell($objCalc->in2mm(3.102), 4, "Less : (CM)", "", $newLineYes, '');
$pdf->Cell(25, 4, "----------------", "", 1, 'C');

$pdf->Cell($objCalc->in2mm(3.102), 4, "", "", $newLineNo, '');

$pdf->Ln(3);


$pdf->Cell($objCalc->in2mm(3.102), 4, "", "", $newLineNo, '');
$pdf->Cell($objCalc->in2mm(1.3), 4, "0", "", 0, 'R');
$pdf->Cell($objCalc->in2mm(1.3), 4, "0", "", 0, 'R');
$pdf->Cell($objCalc->in2mm(1.3), 4, "0", "", $newLineYes, 'R');

//Draw a line  
$pdf->Cell($objCalc->in2mm(3.102), 4, "", "", 0, '');
for($i= 0 ; $i<=2 ; $i++){
	$c=(!($i>=2))? $c=0: $c="1";
	$pdf->Cell(13, 4, "", "", 0, ''); $pdf->Cell(20, 4, "", "T", $c, 'R');
}

//sub-total
$subTotallessCharge = ($subTotalCharges - $totalDiscountCharge);
$subTotallessExcess = ( $subTotalExcess - $totalDiscountCharge);

$pdf->Cell($objCalc->in2mm(3.102), 4, "SUB-TOTAL", "", 0, '');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($subTotallessCharge), "", 0, 'R');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($subTotalCoverage), "", 0, 'R');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($subTotallessExcess), "", $newLineYes, 'R');  //excess


//PROFESSIONAL FEE
$objBill->getProfFeesList();
$objBill->getProfFeesBenefits();
$pfBenefits = $objBill->hsp_pfs_benefits;

if(is_array($pfBenefits)){
	$totalPfCharge = 0;
	$totalPfCoverage = 0;
	foreach($pfBenefits as $pfBen){
		$objBill->getConfineBenefits($pfBen->role_area, '');
		$pfList = $objBilling->proffess_list;
		
		$totalPfCharge += round($pfBen->tot_charge);
		$totalPfCoverage += round($objBill->pfs_confine_coverage[$pfBen->role_area]);	
	}
}

$totalPfExcess = round(($totalPfCharge - $totalPfCoverage));
$pdf->Ln(3);
$pdf->SetFont($fontTypeTimes , "", $fontSizeText2);
$pdf->Cell($objCalc->in2mm(3.102), 4, "PROFESSIONAL FEES", "", 0, '');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalPfCharge), "", 0, 'R');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalPfCoverage), "", 0, 'R');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalPfExcess), "", $newLineYes, 'R');

//Draw a line  
$pdf->Cell($objCalc->in2mm(3.102), 4, "", "", 0, '');
for($i= 0 ; $i<=2 ; $i++){
	$c=(!($i>=2))? $c=0: $c="1";
	$pdf->Cell(13, 4, "", "", 0, ''); $pdf->Cell(20, 4, "", "T", $c, 'R');
}
// Sub-total + Prof fees
$totalPfSubCharge = ($subTotallessCharge + $totalPfCharge);
$totalPfSubCoverage =( $subTotalCoverage + $totalPfCoverage);
$totalPfSubExcess =  ($subTotallessExcess + $totalPfExcess);

$pdf->Cell($objCalc->in2mm(3.102), 4, "TOTAL", "", 0, '');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalPfSubCharge), "", 0, 'R');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalPfSubCoverage), "", 0, 'R');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalPfSubExcess), "", $newLineYes, 'R');



//AMOUNT DUE
$pdf->Ln(3);
$pdf->SetFont($fontTypeTimes , "B", $fontSizeText2);
$pdf->Cell($objCalc->in2mm(3.102), 4, "AMOUNT DUE", "", 0, '');
$pdf->Cell($objCalc->in2mm(1.3), 4, "" , "", 0, 'R');
$pdf->Cell($objCalc->in2mm(1.3), 4, "", "", 0, 'R');
$pdf->Cell($objCalc->in2mm(1.3), 4, number_format($totalPfSubExcess), "TLRB", $newLineYes, 'R');



//Write on pdf format
$pdf->Output();
?>