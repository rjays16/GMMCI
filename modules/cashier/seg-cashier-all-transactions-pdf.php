<?php
//created by Maimai 01-01-2015
//cash transactions

require('./roots.php');
require_once($root_path."classes/fpdf/fpdf.php");
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
include($root_path."include/care_api_classes/class_cashier.php");

define('GEN_COL01', 4);
define('GEN_COL02', 2);
define('GEN_COL03', 0.7);
define('GEN_COL04', 0.7);
define('COL_MID', 6);

class CashPDF extends FPDF{
	//object
	var $csh_obj;
	var $enc_obj;

	//variables
	var $encounter_nr;
	var $p_data;
	var $total;
	
	//fpdf
	var $fontStyle;
	var $fontSize;

	function CashPDF($or_no){
		//page setup
		$pg_size = array($this->in2mm(8.5), $this->in2mm(11));
		$this->FPDF("P","mm", $pg_size);
		$this->AliasNbPages();
		$this->AddPage("P");
		$this->fontFam = "times";
		$this->fontSize = "12";

		//initalize values
		$this->csh_obj = new SegCashier();
		$this->enc_obj = new Encounter();

		$this->encounter_nr = $this->csh_obj->getEncounterNrOr($or_no);
		$this->p_data = $this->enc_obj->getEncounterInfo($this->encounter_nr);
	}

	function setReportTitle(){
		$hospInfo = new Hospital_Admin();
		
		if($row = $hospInfo->getAllHospitalInfo()){
			$row['hosp_name'] = strtoupper($row['hosp_name']);
			$row['hosp_addr1'] = $row['hosp_addr1'];
		}else{
			$row['hosp_name']    = "GONZALES MARANAN MEDICAL CENTER INCORPORATED";
			$row['hosp_addr1']   = "Quezon Ave., Digos City";
		}

		$this->Image('../../gui/img/logos/dmc_logo.jpg',20,10,20,20);

		$this->SetFont($this->fontFam, "B", $this->fontSize+3);
		$this->Ln(5);
		$this->Cell(0, 4, $row['hosp_name'], 0, 1, "C");

		$this->Ln(2);
		$this->SetFont($this->fontFam, "", $this->fontSize+2);
		$this->Cell(0, 4, $row['hosp_addr1'], 0, 1, "C");

		$this->Ln(7);
		$this->SetFont($this->fontFam, "B", $this->fontSize);
		$this->Cell(0, 4, "DETAILED CASH TRANSACTIONS", 0, 1, "C");		
	}

	function patientData(){
		$this->Ln(6);
		$this->SetFont($this->fontFam, "", $this->fontSize);

		//encounter #
		$this->Cell(20, 4, "Case #", "", 0, 'L');
        $this->Cell(1, 4, ":", "", 0, 'R');
        $this->Cell($this->in2mm(4.4), 4, $this->encounter_nr, "", 0, '');

        //hrn
        $this->Cell(22.6, 4, "HRN # ", "", 0, 'L');
		$this->Cell(1, 4, ":", "", 0, 'R');
        $this->Cell(12, 4, $this->p_data['pid'], "", 1, '');
		
		//name
		$name = strtoupper($this->p_data['name_last'].",  ".$this->p_data['name_first']." ".$this->p_data['name_middle']);
		$this->Cell(20, 4, "Name ", "", 0, 'L');
		$this->Cell(1, 4, ":", "", 0, 'R');
		$this->Cell($this->in2mm(4.4),  4, $name, "", 0, '');

		//age
		$this->Cell(22.6, 4, "Age ", "", 0, '');
		$this->Cell(1, 4, ":", "", 0, 'R');
		$this->Cell(12, 4, $this->p_data['age'], "", 1, '');
		
		//address
		$saddr1 = '';
		$saddr2 = '';
		$saddr3 = '';
		$this->trimAddress($this->p_data['street_name'], $this->p_data['brgy_name'], $this->p_data['mun_name'], $this->p_data['prov_name'], $this->p_data['zipcode'], $saddr1, $saddr2, $saddr3);
		
		$this->Cell(20, 4, "Address ", "", 0, '');
		$this->Cell(1, 4, ":", "", 0, 'R');
		$this->Cell($this->in2mm(4.4), 4, strtoupper($saddr1), "", 1, '');

		$this->Cell(20, 4, "", "", 0, '');
		$this->Cell(1, 4, ":", "", 0, 'R');
		$this->Cell($this->in2mm(4.4), 4, strtoupper($saddr2), "", 1, '');
		
		if ($saddr3 != '') {
			$this->Cell(20, 4, "", "", 0, '');
			$this->Cell(1, 4, ":", "", 0, 'R');
			$this->Cell($this->in2mm(4.4), 4, strtoupper($saddr3), "", 1, '');
		}

		//print_r($this->p_data);
	}

	function transactionData(){

		$this->Ln(6);
		$this->Cell($this->in2mm(GEN_COL04) , 4, "Date", "TB", 0, 'C');
		$this->Cell(COL_MID, 4, " ", "", 0, 'C');
		$this->Cell($this->in2mm(GEN_COL03) , 4, "OR #", "TB", 0, 'C');
		$this->Cell(COL_MID, 4, " ", "", 0, 'C');
		$this->Cell($this->in2mm(GEN_COL01) , 4, "Particulars", "TB", 0, 'C');
		$this->Cell(COL_MID, 4, " ", "", 0, 'C');
		$this->Cell(0 , 4, "Total Amount", "TB", 1, 'C');
		$this->Ln(2);

		$sections = array("Laboratory", "Radiology", "Drugs and Medicines", "Miscellaneous", "Final Bill", "Partial Payment", "Others");
		$ref_source = array("LD", "RD", "PH", "MISC", "FB", "PP", "OTHER");

		for($i=0; $i<count($sections); $i++){
			$this->enumData($sections[$i], $ref_source[$i]);
		}

		$this->Ln(7);
		$this->SetFont($this->fontFam, "", $this->fontSize+1);
		$this->Cell($this->in2mm(GEN_COL01+GEN_COL03+GEN_COL04)+COL_MID+COL_MID , 4, "(TOTAL)", "", 0, 'R');
		$this->Cell(COL_MID, 4, " ", "", 0, 'C');
		$this->SetFont($this->fontFam, "B", $this->fontSize+1);
		$this->Cell(0, 4, number_format($this->total,2), "T", 1, 'R');
	}

	function enumData($title, $ref_source){
		$data = array();
		$total = 0;

		$data = $this->csh_obj->getCashTransactions($this->encounter_nr, $ref_source);
		
		while($row = $data->FetchRow()){
			$total_per_item = $row['amount_due'];

			$details[] = array("item_name"=>$row['item_name'],
							"qty"=>$row['qty'],
							"amount_due"=>$row['amount_due']/$row['qty'],
							"sum_item"=>$total_per_item,
							"or_no"=>$row['or_no'],
							"or_date"=>$row['or_date']);

			$total += $total_per_item;
		}

		if($total){
			$this->SetFont($this->fontFam, "B", $this->fontSize+1);
			$this->Cell($this->in2mm(GEN_COL04)+COL_MID+$this->in2mm(GEN_COL03)+COL_MID,4,"","","");
			$this->Cell(20, 4, $title, "", 1, "");
			
			for($i=0; $i<count($details); $i++){
				$this->SetFont($this->fontFam, "", $this->fontSize);
				//Date
				$this->Cell($this->in2mm(GEN_COL04) , 4, $details[$i]['or_date'], "", 0, 'C');
				$this->Cell(COL_MID, 4, " ", "", 0, 'C');
				
				//OR #
				$this->Cell($this->in2mm(GEN_COL03) , 4, $details[$i]['or_no'], "", 0, 'C');
				$this->Cell(COL_MID+5, 4, "", 0, 0);

				//Item name
				$this->Cell(0, 4, $details[$i]['item_name'], "", 1, "");
				$this->Cell($this->in2mm(GEN_COL04)+ $this->in2mm(GEN_COL03)+15+5, 4, "", "", "");

				//Amount Due
				$this->Cell(112, 4, $details[$i]['qty']." @ ".number_format($details[$i]['amount_due'], 2), "", 0, "");
				$this->Cell(COL_MID, 4, "", 0, 0);

				//Sum
				$this->Cell(0, 4, number_format($details[$i]['sum_item'], 2), "", 1, "R");
			}

			$this->Ln(2);
			$this->SetFont($this->fontFam, "", $this->fontSize+1);
			$this->Cell($this->in2mm(GEN_COL01+GEN_COL03+GEN_COL04)+COL_MID+COL_MID, 4, "(Sub-Total)", "", 0, 'R');
			$this->Cell(COL_MID, 4, " ", "", 0, 'C');
			$this->Cell(0, 4, number_format($total,2), "T", 1, 'R');

			$this->total += $total;
		}
	}

	function reportOut(){
		$this->Output();
	}

	function in2mm($inches){
		return $inches * 25.4;
	}

	function trimAddress($street, $brgy, $mun, $prov, $zipcode, &$s_addr1, &$s_addr2, &$s_addr3){
		$address = trim($street);
		$address1 = (!empty($address) && !empty($brgy)) ?  trim($address.", ".$brgy) : trim($address." ".$brgy);
		$s_addr1 = $address1;

		$address2 = trim($mun);
		$address3 =  (!empty($address2) && !empty($zipcode))? trim($address2." ".$zipcode) : $address2." ";

		$address4 = (!empty($address3) && !empty($prov))? trim($address3.", ".$prov) : trim($address3." ".$prov);
		$s_addr2  = $address4;
		$s_addr3  = '';

	}
}

$pdfCash = new CashPDF($_GET['or']);
$pdfCash->setReportTitle();
$pdfCash->patientData();
$pdfCash->transactionData();
$pdfCash->reportOut();
?>