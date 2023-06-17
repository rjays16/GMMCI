<?php
/**
*Created by mai 09-10-2014
*/

require('./roots.php');
require_once($root_path."classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class PromiPDF extends FPDF{
	/*params*/
	var $refno;
	var $patient_name;
	var $encounter_date;
	var $dischare_date;
	var $due_date;
	var $days_to_pay;
	var $due_amount;
	var $patient_address;
	var $name_g;
	var $address_g;
	var $rel_patient;

	/*fdpf*/
	var $fontsize;
	var $fonttype;
	var $fontstyle;
	var $newline;

	function PromiPDF($refno){
		 $this->refno = $refno;

		 $this->fontstyle = "Times";
		 $this->fontsize = 11;
		 $this->fonttype = '';
		 $this->newline = 1;

		 $pg_size = array($this->in2mm(8.5), $this->in2mm(11));                 // Default to long bond paper --- modified by LST - 04.13.2009
		 $this->FPDF("P","mm", $pg_size);
		 $this->AliasNbPages();
		 $this->AddPage("P");
	}

	function Header(){
		$hospInfo = new Hospital_Admin();
		
		if($row = $hospInfo->getAllHospitalInfo()){
			$row['hosp_name'] = strtoupper($row['hosp_name']);
			$row['hosp_addr1'] = strtoupper($row['hosp_addr1']);
		}else{
			$row['hosp_name'] = "CAINGLET MEDICAL HOSPITAL, INC.";
			$row['hosp_addr1'] = "#2081 National Highway, Salvacion, Panabo City";
		}

		$this->Ln(5);
		//hospital name
		$this->SetFont($this->fontstyle,'B',$this->fontsize+6);
		$this->Cell(0, 4, $row['hosp_name'], 0, 1, 'C');

		//hospital address
		$this->Ln(1);
		$this->SetFont($this->fontstyle,'B',$this->fontsize);
		$this->Cell(0, 4, $row['hosp_addr1'], 0, 1, 'C');
		
		//logo
		$this->Image('../../gui/img/logos/dmc_logo.jpg',10,10,22,20);
		$this->ReportTitle();
	}

	function Footer(){

	}

	function ReportTitle(){
		$this->Ln(10);
		$this->Cell(150, 10, '', '', 0, 'R');
		$this->Cell(0, 10, date('F j, Y'), 'B', 1, 'C');
		$this->Ln(5);
		$this->Cell(0, 10, 'PROMISSORY NOTE', 0, 1, 'C');
	}

	function ReportOut(){
		$this->Output();
	}

	function in2mm($inches){
		return $inches * 25.4;
	}

	function printData(){

		//Patient
		$this->Ln(5);
		$this->SetFont($this->fontstyle, '', $this->fontsize);
		$this->Cell(15, 7, '', "", 0, 'C');
		$this->Cell(65, 10, 'I hereby promise to pay the account of', 0, 0, 'J');
		$this->SetFont($this->fontstyle, 'B', $this->fontsize);
		$this->Cell(103, 7, $this->patient_name, "B", 0, 'C');
		$this->SetFont($this->fontstyle, '', $this->fontsize);
		$this->Cell(0, 10, 'in the', 0, 1, 'J');
		$this->SetFont($this->fontstyle, '', $this->fontsize);
		$this->Cell(20, 10, 'amount of', 0, 0, 'J');
		$this->SetFont($this->fontstyle, '', $this->fontsize);
		$this->Cell(20, 7, $this->due_amount, "B", 0, 'C');
		$this->SetFont($this->fontstyle, '', $this->fontsize);
		$this->Cell(30, 10, 'on or before', 0, 0, 'C');
		$this->SetFont($this->fontstyle, '', $this->fontsize);
		$this->Cell(20, 7, $this->duedate, "B", 0, 'C');
		
		//Patient Name and Signature
		$this->Ln(20);
		$this->SetFont($this->fontstyle, 'B', $this->fontsize);
		$this->Cell(100, 7, '', "", 0, 'C');
		$this->Cell(0, 7, $this->patient_name, "B", 1, 'C');
		$this->SetFont($this->fontstyle, '', $this->fontsize);
		$this->Cell(100, 7, '', "", 0, 'C');
		$this->Cell(0, 7, "Printed Name & Signature of Patient", "", 1, 'C');

		//Complete Address
		$this->Ln(8);
		$this->SetFont($this->fontstyle, 'B', $this->fontsize);
		$this->Cell(100, 7, '', "", 0, 'C');
		$this->MultiCell(0, 7, $this->patient_address, "B", 'C');
		$this->SetFont($this->fontstyle, '', $this->fontsize);
		$this->Cell(100, 7, '', "", 0, 'C');
		$this->Cell(0, 7, "Complete Address", "", 1, 'C');

		//Guarantor
		$this->Ln(25);
		$this->SetFont($this->fontstyle, '', $this->fontsize);
		$this->Cell(15, 7, '', "", 0, 'C');
		$this->Cell(75, 10, 'I hereby guarantee payment of the account of', 0, 0, 'J');
		$this->SetFont($this->fontstyle, 'B', $this->fontsize);
		$this->Cell(95, 7, $this->patient_name, "B", 0, 'C');
		$this->SetFont($this->fontstyle, '', $this->fontsize);
		$this->Cell(0, 10, 'in the', 0, 1, 'J');
		$this->SetFont($this->fontstyle, '', $this->fontsize);
		$this->Cell(20, 10, 'amount of', 0, 0, 'J');
		$this->SetFont($this->fontstyle, '', $this->fontsize);
		$this->Cell(20, 7, $this->due_amount, "B", 0, 'C');
		$this->SetFont($this->fontstyle, '', $this->fontsize);
		$this->Cell(0, 10, " and further state that failure to pay the above party, I promise to pay the same.", "", 0, 'L');
		
		//Patient Name and Signature
		$this->Ln(20);
		$this->SetFont($this->fontstyle, 'B', $this->fontsize);
		$this->Cell(90, 7, $this->address_g, 'B', 0, 'C');
		$this->Cell(10, 7, '', "", 0, 'C');
		$this->Cell(0, 7, $this->name_g, "B", 1, 'C');
		$this->SetFont($this->fontstyle, '', $this->fontsize);
		$this->Cell(100, 7, 'Complete Address', "", 0, 'C');
		$this->Cell(0, 7, "Printed Name & Signature of Guarantor", "", 1, 'C');

		//Complete Address
		$this->Ln(8);
		$this->SetFont($this->fontstyle, 'B', $this->fontsize);
		$this->Cell(100, 7, '', "", 0, 'C');
		$this->Cell(0, 7, $this->rel_patient, "B", 1, 'C');
		$this->SetFont($this->fontstyle, '', $this->fontsize);
		$this->Cell(100, 7, '', "", 0, 'C');
		$this->Cell(0, 7, "Relationship to Patient", "", 1, 'C');

	}

	function getDetails(){
		global $db;

		$sql = "SELECT 
					  spn.`encounter_nr`,
					  fn_get_person_lastname_first (ce.`pid`) AS patient_name,
					  fn_get_complete_address(ce.pid) AS patient_add,
					  DATE(ce.`encounter_date`) AS encounter_date,
					  sbe.`bill_dte` AS discharge_date,
					  spn.amount,
					  spn.name_guarantor,
					  spn.address_guarantor,
					  spn.relationship_patient,
					  due_date,
					  DATE(spn.due_date) - DATE(spn.create_dt) AS days_to_pay,
					  remarks 
					FROM
					  seg_promissory_note spn 
					  LEFT JOIN care_encounter ce 
					    ON ce.`encounter_nr` = spn.`encounter_nr` 
					  LEFT JOIN care_person cp 
					    ON cp.pid = ce.`pid` 
					  LEFT JOIN seg_billing_encounter sbe 
					    ON sbe.encounter_nr = spn.encounter_nr  
					WHERE refno =  ".$db->qstr($this->refno);
				
		$result=$db->Execute($sql);
		if($row = $result->FetchRow()){
			$this->patient_name = $row['patient_name'];
			$this->patient_address = trim($row['patient_add']);
			$this->name_g = $row['name_guarantor'];
			$this->address_g = $row['address_guarantor'];
			$this->rel_patient = $row['relationship_patient'];
			$this->due_amount = "P".number_format($row['amount'],2);
			$encounter_date = new Datetime($row['encounter_date']);
			$this->encounter_date = date_format($encounter_date, 'm/d/Y');
			$this->days_to_pay = $row['days_to_pay'];

			$due_date = new Datetime($row['due_date']);
			$due_date = date_format($due_date, 'm/d/Y');
			
			$this->duedate = $due_date;
		}
	}

}

$pdf = new PromiPDF($_GET['refno']);
$pdf->getDetails();
$pdf->printData();
$pdf->ReportOut();
?>