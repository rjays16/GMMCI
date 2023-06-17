<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path."classes/fpdf/fpdf.php");

include_once($root_path."include/care_api_classes/class_hospital_admin.php");
include_once($root_path."include/care_api_classes/class_insurance.php");

define('DEFAULT_HCAREID', 18);

class PDF_MC_Table extends FPDF
{
	var $widths;
	var $aligns;

	function SetWidths($w){
	    $this->widths=$w;
	}

	function SetAligns($a){
	    $this->aligns=$a;
	}

	function Row($data){
	    
	    $nb=0;
	    for($i=0;$i<count($data);$i++)
	        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
	    $h=5*$nb;
	    
	    $this->CheckPageBreak($h);
	    
	    for($i=0;$i<count($data);$i++){
	        $w=$this->widths[$i];
	        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
	       
	        $x=$this->GetX();
	        $y=$this->GetY();

	        $this->Rect($x,$y,$w,$h);
	        $this->MultiCell($w,5,$data[$i],0,$a);
	        $this->SetXY($x+$w,$y);
	    }
	    $this->Ln($h);
	}

	function CheckPageBreak($h){

	    if($this->GetY()+$h>$this->PageBreakTrigger)
	        $this->AddPage($this->CurOrientation);
	}

	function NbLines($w,$txt){
	    $cw=&$this->CurrentFont['cw'];
	    if($w==0)
	        $w=$this->w-$this->rMargin-$this->x;
	    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	    $s=str_replace("\r",'',$txt);
	    $nb=strlen($s);
	    if($nb>0 and $s[$nb-1]=="\n")
	        $nb--;
	    $sep=-1;
	    $i=0;
	    $j=0;
	    $l=0;
	    $nl=1;
	    while($i<$nb)
	    {
	        $c=$s[$i];
	        if($c=="\n")
	        {
	            $i++;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	            continue;
	        }
	        if($c==' ')
	            $sep=$i;
	        $l+=$cw[$c];
	        if($l>$wmax)
	        {
	            if($sep==-1)
	            {
	                if($i==$j)
	                    $i++;
	            }
	            else
	                $i=$sep+1;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	        }
	        else
	            $i++;
	    }
	    return $nl;
	}
}

class RegGen_TransmittalLetter extends PDF_MC_Table {
	var $fontStyle;
	var $fontSize;
	var $cellHeight;
	var $cellWidth;
	var $border1;
	
	var $transmit_no;	
	var $hcare_id;
	var $insurance_name;
	var $hosp_info;
	var $columns;

	function RegGen_TransmittalLetter($insurance_name) {
		global $db;

		$pg_size = array('215.9', '330.2');
		$this->FPDF("P","mm", $pg_size);
		$this->AliasNbPages();
		$this->AddPage("L");

		$this->cellHeight = 5;
		$this->cellWidth = 33;
		$this->fontStyle = "Times";
		$this->fontSize = 12;
		$this->border1 = 1;

		$this->columns = array(34, 47, 47, 52, 21, 84, 25);
		$this->insurance_name = $insurance_name;
	}

	function reportHeader(){
		$objInfo = new Hospital_Admin();
		
		$row = $objInfo->getAllHospitalInfo();

		$this->SetFont($this->fontStyle, "", $this->fontSize);
		$this->Cell(0, 4, $this->insurance_name, 0, 1, "C");

		$this->SetFont($this->fontStyle, "B", $this->fontSize+1);
		$this->Cell(0, 4, strtoupper($row['hosp_name']), 0, 1, "C");
		
		$this->SetFont($this->fontStyle, "", $this->fontSize-2);
		$this->Cell(0, 4, strtoupper($row['hosp_addr1']), 0, 1, "C");
		
		$this->SetFont($this->fontStyle, "B", $this->fontSize);
		$this->Cell(0, 4, "TRANSMITTAL LETTER", 0, 1, "C");

		$this->hosp_info = $row;
		$this->Ln(10);
	}

	function detailHeader(){
		$objInsurance = new Insurance();
		
		$this->Cell(0, $this->cellHeight, "DIALYSIS CLAIMS", 0, 1, "L");

		$this->Ln(2);

		$this->fontSize = 9;

		//row1
		$this->SetFont($this->fontStyle, "", $this->fontSize);
		
		$this->SetAligns(array("L", "L", "L"));
		$this->SetWidths(array(81, 120, 109));

		$data = array("HOSPITAL NAME", "ADDRESS", "NAME & SIGNATURE OF AUTHORIZED HOSPITAL REPRESENTATIVE");
		$this->Row($data);

		$this->SetFont($this->fontStyle, "B", $this->fontSize);
		
		$this->SetAligns(array("C", "L", "L", "C"));
		$this->SetWidths(array(81, 120, 45, 64));

		$data = array(strtoupper($this->hosp_info['hosp_name']),
						strtoupper($this->hosp_info['hosp_addr1']),
						strtoupper($this->hosp_info['authrep']),
						strtoupper($this->hosp_info['designation'])
						);

		$this->Row($data);
	
		$this->SetWidths(array(34, 47, 47, 52, 21, 45, 39, 25));
		$this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'L', 'R', 'C'));

		//row2
		$this->SetFont($this->fontStyle, "", $this->fontSize);
		$data = array("PHIC ACCREDITATION", "HOSP.CATEGORY",
							"AUTHORIZED BED CAPACITY", "NAME OF ATTENDING PHYSICIAN", "",
							"PHIC ACCREDITATION NO.", "TIN ACCOUNT",
							""
						);

		$this->Row($data);

		//row3
		$this->SetFont($this->fontStyle, "B", $this->fontSize);
		$data = array($objInsurance->getAccreditationNo(DEFAULT_HCAREID), "TERTIARY", 
							$this->hosp_info['bed_capacity'], $this->getDoctors(), "",
								"H11018706", $this->hosp_info["tax_acctno"], 
									"BENEFIT CLAIMS"
					);

		$this->Row($data);
		//row4

		$this->SetWidths($this->columns);
		$this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C'));

		$data = array("PHIC NUMBER", "NAME OF MEMBER", 
							"NAME OF PATIENT", "CONFINEMENT PERIOD", 
								"NO. OF CLAIMS", "FINAL DIAGNOSIS", 
									"HOSPITAL"
					);

		$this->Row($data);
	}

	function details(){
		$insuranceObj = new Insurance();
		$res_categories = $insuranceObj->allCategory();
		
		$this->SetWidths($this->columns);
		$this->SetAligns(array('L', 'L', 'L', 'C', 'C', 'L', 'R'));
		
		while($row = $res_categories->FetchRow()){	
			$detailsPatient = $this->detailsPatient($row['memcategory_id']);
			
			if($detailsPatient){
				$count = 1;
				$this->SetFont($this->fontStyle, "B", $this->fontSize);
				
				$this->Cell(0, $this->cellHeight, $row['memcategory_desc'], 1, 1, 'L');

				while($row1 = $detailsPatient->FetchRow()){
					$this->SetFont($this->fontStyle, "", $this->fontSize);

					$date = "";
					$prev_month = "";
					
					$i=0;

					$dialysisData = $insuranceObj->dialysisInfo($row1['dialysis_id']);
					 // added by Mats 07202016
					$date_counts = $insuranceObj->date_count($row1['dialysis_id']);

					while($row3 = $date_counts->FetchRow()){
						$count_date = $row3['date_count'];
					}
					//ended by Mats 07202016

					while($row2 = $dialysisData->FetchRow()){
			
						$month = substr($row2['session_date'], 5, 2);
						$year = substr($row2['session_date'], 0, 4);

						if($month != $prev_month){
							$date .= $month."/";
						}

						$date .= substr($row2['session_date'], 8, 2)."-";

						$prev_month = $month;
						$year = substr($row2['session_date'], 0, 4);

						$i++;
					}

					$date = substr($date, 0, strlen($date)-1)."/".$year;

					$data = array($count." .".$row1['insurance_nr'],
									$row1['member'], 
									$row1['patient'],
									$date,
									// commented by Mats 07202016
									//$row1['claims']
									//added by mats 07202016
									$count_date,
									
									strtoupper($this->diagnosisPatient($row1['encounter_nrs'])),
									number_format($this->totalCoverage($row1['bill_nrs'], 2))
								);

					$this->Row($data);
					
					$count++;
				}
			}
		}

		$this->Cell(0, $this->cellHeight, "NOTHING FOLLOWS.....", 0, 0, "L");
	}

	function totalCoverage($bill_nrs){
		global $db;
		
		return $db->GetOne("SELECT 
							  SUM(
							    IFNULL(sbc.total_msc_coverage, 0) + IFNULL(sbc.total_acc_coverage, 0) + IFNULL(sbc.total_med_coverage, 0) + IFNULL(sbc.total_srv_coverage, 0) + IFNULL(sbc.total_ops_coverage, 0)
							  ) + SUM(
							    IFNULL(sbc.total_d1_coverage, 0) + IFNULL(sbc.total_d2_coverage, 0) + IFNULL(sbc.total_d3_coverage, 0) + IFNULL(sbc.total_d4_coverage, 0)
							 ) total_coverage 
							FROM
							  seg_billing_coverage sbc 
							WHERE bill_nr IN ($bill_nrs) AND sbc.hcare_id = ".$db->qstr($this->hcare_id));
	}

	function diagnosisPatient($encounter_nrs){
		global $db;

		return $db->GetOne("SELECT 
							  GROUP_CONCAT(
							    DISTINCT (ced.`diagnosis_description`)
							  ) diagnosis 
							FROM
							  care_encounter_diagnosis ced 
							WHERE ced.`encounter_nr` IN ($encounter_nrs) 
							  AND STATUS <> 'deleted'");
	}

	function detailsPatient($categ_id){
		global $db;

		$sql = 'SELECT 
				  d.`id` AS dialysis_id,
				  m.`insurance_nr`,
				  GROUP_CONCAT(CONCAT(\'"\',b.`bill_nr`,\'"\')) bill_nrs,
				  d.`session_date`,
				  GROUP_CONCAT(CONCAT(\'"\',t.`encounter_nr`,\'"\')) encounter_nrs,
				  CONCAT(
				    member_lname,
				    ", ",
				    member_fname,
				    " ",
				    IF(
				      m.`member_mname` <> "",
				      CONCAT(LEFT(m.`member_mname`, 1), "."),
				      " "
				    )
				  ) member,
				  fn_get_person_lastname_first (ce.`pid`) patient,
				  COUNT(d.`id`) AS claims 
				FROM
				  seg_transmittal_details t 
				  LEFT JOIN care_encounter ce 
				    ON ce.`encounter_nr` = t.`encounter_nr` 
				  LEFT JOIN seg_billing_encounter b 
				    ON (
				      b.`encounter_nr` = t.`encounter_nr` 
				      AND b.`is_deleted` IS NULL 
				      AND b.`is_final` = 1
				    ) 
				  LEFT JOIN seg_insurance_member_info m 
				    ON (
				      m.`pid` = ce.`pid` 
				      AND m.`hcare_id` = '.$db->qstr($this->hcare_id).'
				    ) 
				  LEFT JOIN seg_encounter_memcategory sem 
				    ON sem.`encounter_nr` = t.`encounter_nr` 
				  LEFT JOIN seg_dialysis_transaction_d d 
				    ON d.`bill_nr` = b.`bill_nr` 
				WHERE t.`transmit_no` = '.$db->qstr($this->transmit_no).' 
				  AND sem.`memcategory_id` = '.$db->qstr($categ_id).' 
				GROUP BY d.`id` ORDER BY member';

		/*$sql = 'SELECT 
				  m.`insurance_nr`,
				  t.`encounter_nr`,
				  CONCAT(
					    member_lname,
					    ", ",
					    member_fname,
					    " ",
					    IF(
					      m.`member_mname` <> "",
					      CONCAT(LEFT(m.`member_mname`, 1), "."),
					      " "
					    )
					  ) member,
				  fn_get_person_lastname_first (ce.`pid`) patient,
				  (SELECT 
				    COUNT(id) 
				  FROM
				    seg_dialysis_transaction_d 
				  WHERE id = 
				    (SELECT 
				      d.`id` 
				    FROM
				      seg_dialysis_transaction_d d 
				      LEFT JOIN seg_billing_encounter b 
				        ON b.`bill_nr` = d.`bill_nr` 
				    WHERE b.`encounter_nr` = t.`encounter_nr`)) claims 
				FROM
				  seg_transmittal_details t 
				  LEFT JOIN care_encounter ce 
				    ON ce.`encounter_nr` = t.`encounter_nr` 
				  LEFT JOIN seg_insurance_member_info m 
				    ON (
				      m.`pid` = ce.`pid` 
				      AND m.`hcare_id` = '.$db->qstr($this->hcare_id).'
				    ) 
				  LEFT JOIN seg_encounter_memcategory sem 
				    ON sem.`encounter_nr` = t.`encounter_nr` 
				WHERE t.`transmit_no` = '.$db->qstr($this->transmit_no).' 
				  AND sem.`memcategory_id` = '.$db->qstr($categ_id);*/
				
		$result = $db->Execute($sql);

		if($result){
			if($result->RecordCount() > 0){
				return $result;
			}
		}

		return false;
	}

	function getDoctors(){
		global $db;
		
		$dr = "";

		$sql = "SELECT DISTINCT 
				  (
				    CONCAT('DR. ', fn_get_personell_name2 (sbp.`dr_nr`))
				  ) AS doc_name 
				FROM
				  seg_billing_pf AS sbp 
				WHERE sbp.`bill_nr` IN (".$this->selectBillNr().")";

		if($result = $db->Execute($sql)){
			while($row = $result->FetchRow()){
				$dr .= $row['doc_name'];
			}
		}

		return $dr;
	}

	function selectBillNr(){
		global $db;

		return  $db->GetOne("SELECT 
				    GROUP_CONCAT(CONCAT('".'"'."',bill_nr,'".'"'."')) 
				  FROM
				    seg_dialysis_transaction_d 
				  WHERE id IN 
				    (SELECT 
				      d.`id` 
				    FROM
				      seg_transmittal_details t 
				      LEFT JOIN seg_billing_encounter b 
				        ON (
				          b.`encounter_nr` = t.`encounter_nr` 
				          AND b.`is_deleted` IS NULL
				        ) 
				      LEFT JOIN seg_dialysis_transaction_d d 
				        ON d.`bill_nr` = b.`bill_nr` 
				    WHERE t.`transmit_no` = ".$db->qstr($this->transmit_no).")");
		
	}

	function report(){
		$this->Output();
	}
}


/****************************************************************************************************/

global $db;

$insurance_name = $db->GetOne("SELECT cif.name FROM seg_transmittal AS st LEFT JOIN care_insurance_firm AS cif ON st.hcare_id = cif.hcare_id WHERE st.transmit_no = '".$_GET['nr']."'");
$hcare_id = $db->GetOne("SELECT st.hcare_id FROM seg_transmittal AS st WHERE st.transmit_no = '".$_GET['nr']."'");

$rep = new RegGen_TransmittalLetter(ucwords($insurance_name));
$rep->transmit_no = $_GET['nr'];
$rep->hcare_id = $hcare_id;
$rep->reportHeader();
$rep->detailHeader();
$rep->details();
$rep->report();