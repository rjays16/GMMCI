<?php
require('./roots.php');
require($root_path."/classes/fpdf/pdf.class.php");
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class DailyAccomplishment extends FPDF{
	var $from;
	var $observation;
	var $material;
	var $environment;
	var $endorsement;

	function DailyAccomplishment($from, $observation, $material, $environment, $endorsement){
		global $db;
		$this->ColumnWidth = array(25, 105, 20, 50);
		$this->SetTopMargin(3);
		$this->Alignment = array('C','L','C','C');
		$this->FPDF("P", 'mm', 'Legal');

		$this->observation = $observation;
		$this->material = $material;
		$this->environment = $environment;
		$this->endorsement = $endorsement;

		if ($from) $this->from=date("Y-m-d",strtotime($from));
	}

	function Header_() {
		global $root_path, $db;

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

		$this->SetFont('Arial', 'B', 10);
		$this->Cell(0, 5, $row['hosp_name'],0,1,'C');
		$this->Ln();
		$this->SetFont('Arial', 'B', 12);
		$this->Cell(0, 5, "AMBULATORY SURGERY UNIT NURSE SUPERVISOR'S",0,1,'C');
		$this->Cell(0, 5, "DAILY ACCOMPLISHMENT REPORT",0,1,'C');
		$this->Ln();

		$this->SetFont('Arial', 'B', 10);
		$this->Cell(30, 5, "CENSUS AS OF",0,0,'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+4.5, $x+40, $y+4.5);
		$this->Cell(40, 5, date("F j, Y", strtotime($this->from)), 0, 1, 'L');
		$this->Ln();
		/*$this->SetFont('Arial', 'B', 12);
		if($this->from == $this->to){
			$this->Cell(0,6, "As of ".date("F j, Y",strtotime($this->from)), 0,1,'C');
		}
		else{
			$this->Cell(0, 4, "From ".date("F j, Y", strtotime($this->from))." to ".date("F j, Y",strtotime($this->to)),0,1,'C');
		} */

	}

	function GetData(){
		global $db;
		$rowheight = 5.5;
		$limit = 10;
		$this->Ln();

		$this->SetFont('Arial', 'B', 10);

		//table header
		$this->Cell($this->ColumnWidth[0], $rowheight, "", "TLR", 0);
		$this->Cell($this->ColumnWidth[1], $rowheight, "PROCEDURE",1,0,'C');
		$this->Cell($this->ColumnWidth[2], $rowheight, "QUANTITY", 1,0, 'C');
		$this->Cell($this->ColumnWidth[3], $rowheight, "REMARKS", 1, 1, 'C');

		$xcol = $this->GetX();
		$ycol = $this->GetY();

		$this->Cell($this->ColumnWidth[0], $rowheight, "I. GENERAL", "LR", 0, 'L');
		$xcol2 = $this->GetX();
		$ycol2 = $this->GetY();
		$this->Ln();
		$this->Cell($this->ColumnWidth[0], $rowheight, "    SURGERY", "LR", 1, 'L');


		$sql_gen = "SELECT distinct som.or_procedure,
			(SELECT COUNT(sm.encounter_nr) FROM seg_or_main AS sm WHERE sm.or_procedure = som.or_procedure
					AND DATE(sm.date_operation) BETWEEN '".$this->from."' AND '".$this->from."'
					AND dept_nr = 159) AS num
			FROM seg_or_main AS som WHERE dept_nr = 159
			AND DATE(som.date_operation) BETWEEN '".$this->from."' AND '".$this->from."';";
	 /*
		$sql_gen = "SELECT distinct som.or_procedure,
							(SELECT COUNT(sm.encounter_nr) FROM seg_or_main AS sm WHERE sm.or_procedure = som.or_procedure
								AND DATE(sm.date_operation) BETWEEN '2010-05-05' AND '2010-05-08'
								AND dept_nr = 159) AS num
							FROM seg_or_main AS som WHERE dept_nr = 159
							AND DATE(som.date_operation) BETWEEN '2010-05-05' AND '2010-05-08';"; */
		$result_gen = $db->Execute($sql_gen);
		if($result_gen){
			$count_gen = $result_gen->RecordCount();
			if($count_gen < $limit)
				$blankrows_gen = $limit - $count_gen;
			$this->SetFont('Arial', '', 8.5);
			while($row_gen = $result_gen->FetchRow()){
				 $this->SetXY($xcol2, $ycol2);
				 $this->Cell($this->ColumnWidth[1], $rowheight, $row_gen['or_procedure'], 1, 0, $this->Alignment[1]);
				 $this->Cell($this->ColumnWidth[2], $rowheight, $row_gen['num'], 1, 0, $this->Alignment[2]);
				 $this->Cell($this->ColumnWidth[3], $rowheight, "", 1, 1, $this->Alignment[3]);
				 $ycol2 = $this->GetY();
			}
			for($cnt_gen = 0; $cnt_gen < $blankrows_gen; $cnt_gen++){
				$this->SetXY($xcol2, $ycol2);
				$this->Cell($this->ColumnWidth[1], $rowheight, "", 1, 0, $this->Alignment[1]);
				$this->Cell($this->ColumnWidth[2], $rowheight, "", 1, 0, $this->Alignment[2]);
				$this->Cell($this->ColumnWidth[3], $rowheight, "", 1, 1, $this->Alignment[3]);
				$ycol2 = $this->GetY();
			}


		}else{
			for($i = 0; $i < $limit; $i++){
				$this->SetXY($xcol2, $ycol2);
				$this->Cell($this->ColumnWidth[1], $rowheight, "", 1, 0, $this->Alignment[1]);
				$this->Cell($this->ColumnWidth[2], $rowheight, "", 1, 0, $this->Alignment[2]);
				$this->Cell($this->ColumnWidth[3], $rowheight, "", 1, 0, $this->Alignment[3]);
				$ycol2 = $this->GetY();
			}
		}
		$this->Line($xcol, $ycol, $xcol, $ycol2);

		//--------------------Ambu-Optha-------------
		$this->SetFont('Arial', 'B', 10);
		$xcol = $this->GetX();
		$ycol = $this->GetY();

		$this->Cell($this->ColumnWidth[0], $rowheight, "II. OPTHA", "TLR", 0, 'L');
		$xcol2 = $this->GetX();
		$ycol2 = $this->GetY();

		$sql_optha = "SELECT distinct som.or_procedure,
							(SELECT COUNT(sm.encounter_nr) FROM seg_or_main AS sm WHERE sm.or_procedure = som.or_procedure
								AND DATE(sm.date_operation) BETWEEN '".$this->from."' AND '".$this->from."'
								AND dept_nr = 160) AS num
							FROM seg_or_main AS som WHERE dept_nr = 160
							AND DATE(som.date_operation) BETWEEN '".$this->from."' AND '".$this->from."';";
		$result_optha = $db->Execute($sql_optha);
		if($result_optha){
			$count_optha = $result_optha->RecordCount();
			if($count_optha < $limit)
				$blankrows_optha = $limit - $count_optha;
			$this->SetFont('Arial', '', 8.5);
				while($row_optha = $result_optha->FetchRow()){
				 $this->SetXY($xcol2, $ycol2);
				 $this->Cell($this->ColumnWidth[1], $rowheight, $row_optha['or_procedure'], 1, 0, $this->Alignment[1]);
				 $this->Cell($this->ColumnWidth[2], $rowheight, $row_optha['num'], 1, 0, $this->Alignment[2]);
				 $this->Cell($this->ColumnWidth[3], $rowheight, "", 1, 1, $this->Alignment[3]);
				 $ycol2 = $this->GetY();
			}
			for($cnt_optha = 0; $cnt_optha < $blankrows_optha; $cnt_optha++){
				$this->SetXY($xcol2, $ycol2);
				$this->Cell($this->ColumnWidth[1], $rowheight, "", 1, 0, $this->Alignment[1]);
				$this->Cell($this->ColumnWidth[2], $rowheight, "", 1, 0, $this->Alignment[2]);
				$this->Cell($this->ColumnWidth[3], $rowheight, "", 1, 1, $this->Alignment[3]);
				$ycol2 = $this->GetY();
			}
		}else{
			for($i = 0; $i < $limit; $i++){
				$this->SetXY($xcol2, $ycol2);
				$this->Cell($this->ColumnWidth[1], $rowheight, "", 1, 0, $this->Alignment[1]);
				$this->Cell($this->ColumnWidth[2], $rowheight, "", 1, 0, $this->Alignment[2]);
				$this->Cell($this->ColumnWidth[3], $rowheight, "", 1, 0, $this->Alignment[3]);
				$ycol2 = $this->GetY();
			}
		}
		$this->Line($xcol, $ycol, $xcol, $ycol2);

		//----------------Ambu ENT-HNS------------------
		$this->SetFont('Arial', 'B', 10);
		$xcol = $this->GetX();
		$ycol = $this->GetY();

		$this->Cell($this->ColumnWidth[0], $rowheight, "III. ENT-HNS", "TLR", 0, 'L');
		$xcol2 = $this->GetX();
		$ycol2 = $this->GetY();

		$sql_ent = "SELECT distinct som.or_procedure,
							(SELECT COUNT(sm.encounter_nr) FROM seg_or_main AS sm WHERE sm.or_procedure = som.or_procedure
								AND DATE(sm.date_operation) BETWEEN '".$this->from."' AND '".$this->from."'
								AND dept_nr = 162) AS num
							FROM seg_or_main AS som WHERE dept_nr = 162
							AND DATE(som.date_operation) BETWEEN '".$this->from."' AND '".$this->from."';";
		$result_ent = $db->Execute($sql_ent);
		if($result_ent){
			$count_ent = $result_ent->RecordCount();
			if($count_ent < $limit)
				$blankrows_ent = $limit - $count_ent;
			$this->SetFont('Arial', '', 8.5);
				while($row_ent = $result_ent->FetchRow()){
				 $this->SetXY($xcol2, $ycol2);
				 $this->Cell($this->ColumnWidth[1], $rowheight, $row_ent['or_procedure'], 1, 0, $this->Alignment[1]);
				 $this->Cell($this->ColumnWidth[2], $rowheight, $row_ent['num'], 1, 0, $this->Alignment[2]);
				 $this->Cell($this->ColumnWidth[3], $rowheight, "", 1, 1, $this->Alignment[3]);
				 $ycol2 = $this->GetY();
			}
			for($cnt_ent = 0; $cnt_ent < $blankrows_ent; $cnt_ent++){
				$this->SetXY($xcol2, $ycol2);
				$this->Cell($this->ColumnWidth[1], $rowheight, "", 1, 0, $this->Alignment[1]);
				$this->Cell($this->ColumnWidth[2], $rowheight, "", 1, 0, $this->Alignment[2]);
				$this->Cell($this->ColumnWidth[3], $rowheight, "", 1, 1, $this->Alignment[3]);
				$ycol2 = $this->GetY();
			}
		}else{
			for($i = 0; $i < $limit; $i++){
				$this->SetXY($xcol2, $ycol2);
				$this->Cell($this->ColumnWidth[1], $rowheight, "", 1, 0, $this->Alignment[1]);
				$this->Cell($this->ColumnWidth[2], $rowheight, "", 1, 0, $this->Alignment[2]);
				$this->Cell($this->ColumnWidth[3], $rowheight, "", 1, 0, $this->Alignment[3]);
				$ycol2 = $this->GetY();
			}
		}
		$this->Line($xcol, $ycol, $xcol, $ycol2);

		//---------------------Ambu OB-Gyne--------------------
		$this->SetFont('Arial', 'B', 10);
		$xcol = $this->GetX();
		$ycol = $this->GetY();

		$this->Cell($this->ColumnWidth[0], $rowheight, "IV. OB-GYNE", "TLR", 0, 'L');
		$xcol2 = $this->GetX();
		$ycol2 = $this->GetY();

		$sql_gyne = "SELECT distinct som.or_procedure,
							(SELECT COUNT(sm.encounter_nr) FROM seg_or_main AS sm WHERE sm.or_procedure = som.or_procedure
								AND DATE(sm.date_operation) BETWEEN '".$this->from."' AND '".$this->from."'
								AND dept_nr = 163) AS num
							FROM seg_or_main AS som WHERE dept_nr = 163
							AND DATE(som.date_operation) BETWEEN '".$this->from."' AND '".$this->from."';";
		$result_gyne = $db->Execute($sql_gyne);
		if($result_gyne){
			$count_gyne = $result_gyne->RecordCount();
			if($count_gyne < $limit)
				$blankrows_gyne = $limit - $count_gyne;
			$this->SetFont('Arial', '', 8.5);
				while($row_gyne = $result_gyne->FetchRow()){
				 $this->SetXY($xcol2, $ycol2);
				 $this->Cell($this->ColumnWidth[1], $rowheight, $row_gyne['or_procedure'], 1, 0, $this->Alignment[1]);
				 $this->Cell($this->ColumnWidth[2], $rowheight, $row_gyne['num'], 1, 0, $this->Alignment[2]);
				 $this->Cell($this->ColumnWidth[3], $rowheight, "", 1, 1, $this->Alignment[3]);
				 $ycol2 = $this->GetY();
			}
			for($cnt_gyne = 0; $cnt_gyne < $blankrows_gyne; $cnt_gyne++){
				$this->SetXY($xcol2, $ycol2);
				$this->Cell($this->ColumnWidth[1], $rowheight, "", 1, 0, $this->Alignment[1]);
				$this->Cell($this->ColumnWidth[2], $rowheight, "", 1, 0, $this->Alignment[2]);
				$this->Cell($this->ColumnWidth[3], $rowheight, "", 1, 1, $this->Alignment[3]);
				$ycol2 = $this->GetY();
			}
		}else{
			for($i = 0; $i < $limit; $i++){
				$this->SetXY($xcol2, $ycol2);
				$this->Cell($this->ColumnWidth[1], $rowheight, "", 1, 0, $this->Alignment[1]);
				$this->Cell($this->ColumnWidth[2], $rowheight, "", 1, 0, $this->Alignment[2]);
				$this->Cell($this->ColumnWidth[3], $rowheight, "", 1, 0, $this->Alignment[3]);
				$ycol2 = $this->GetY();
			}
		}
		$this->Line($xcol, $ycol, $xcol, $ycol2);

		//-------------------Ambu Ortho-----------------
		$this->SetFont('Arial', 'B', 10);
		$xcol = $this->GetX();
		$ycol = $this->GetY();

		$this->Cell($this->ColumnWidth[0], $rowheight, "V. ORTHO", "TLR", 0, 'L');
		$xcol2 = $this->GetX();
		$ycol2 = $this->GetY();

		$sql_ortho = "SELECT distinct som.or_procedure,
							(SELECT COUNT(sm.encounter_nr) FROM seg_or_main AS sm WHERE sm.or_procedure = som.or_procedure
								AND DATE(sm.date_operation) BETWEEN '".$this->from."' AND '".$this->from."'
								AND dept_nr = 161) AS num
							FROM seg_or_main AS som WHERE dept_nr = 161
							AND DATE(som.date_operation) BETWEEN '".$this->from."' AND '".$this->from."';";
		$result_ortho = $db->Execute($sql_ortho);
		if($result_ortho){
			$count_ortho = $result_ortho->RecordCount();
			if($count_ortho < $limit)
				$blankrows_ortho = $limit - $count_ortho;
			$this->SetFont('Arial', '', 8.5);
				while($row_ortho = $result_ortho->FetchRow()){
				 $this->SetXY($xcol2, $ycol2);
				 $this->Cell($this->ColumnWidth[1], $rowheight, $row_ortho['or_procedure'], 1, 0, $this->Alignment[1]);
				 $this->Cell($this->ColumnWidth[2], $rowheight, $row_ortho['num'], 1, 0, $this->Alignment[2]);
				 $this->Cell($this->ColumnWidth[3], $rowheight, "", 1, 1, $this->Alignment[3]);
				 $ycol2 = $this->GetY();
			}
			for($cnt_ortho = 0; $cnt_ortho < $blankrows_ortho; $cnt_ortho++){
				$this->SetXY($xcol2, $ycol2);
				$this->Cell($this->ColumnWidth[1], $rowheight, "", 1, 0, $this->Alignment[1]);
				$this->Cell($this->ColumnWidth[2], $rowheight, "", 1, 0, $this->Alignment[2]);
				$this->Cell($this->ColumnWidth[3], $rowheight, "", 1, 1, $this->Alignment[3]);
				$ycol2 = $this->GetY();
			}
		}else{
			for($i = 0; $i < $limit; $i++){
				$this->SetXY($xcol2, $ycol2);
				$this->Cell($this->ColumnWidth[1], $rowheight, "", 1, 0, $this->Alignment[1]);
				$this->Cell($this->ColumnWidth[2], $rowheight, "", 1, 0, $this->Alignment[2]);
				$this->Cell($this->ColumnWidth[3], $rowheight, "", 1, 0, $this->Alignment[3]);
				$ycol2 = $this->GetY();
			}
		}
		$this->Line($xcol, $ycol, $xcol, $ycol2);
		$this->Line($xcol, $ycol2, $xcol2, $ycol2);
	}

	function Problems(){
		$this->SetFont('Arial', 'B', 10);
		$rowheight = 5;

		$x = $this->GetX();
		$this->Cell(0, $rowheight, "PERTINENT REPORTS:", 0, 1, 'L');
		$this->Ln();
		$y = $this->GetY();

		$this->Cell(200, $rowheight, "HUMAN RESOURCE PROBLEMS/OBSERVATIONS AND ACTION TAKEN.",0,1, 'L');
		$this->SetFont('Arial', '', 9);
		$this->MultiCell(200, $rowheight, $this->observation, 0, 'L');

		$this->SetXY($x, $y+70);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(200, $rowheight, "MATERIALS / EQUIPMENT PROBLEMS / ACTION TAKEN.",0,1, 'L');
		$this->SetFont('Arial', '', 9);
		$this->MultiCell(200, $rowheight, $this->material, 0, 'L');


		$this->SetXY($x, $y+140);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(200, $rowheight, "PHYSICAL ENVIRONMENT PROBLEMS / ACTION PLAN TAKEN.",0,1, 'L');
		$this->SetFont('Arial', '', 9);
		$this->MultiCell(200, $rowheight, $this->environment, 0, 'L');

		$this->SetXY($x, $y+210);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(200, $rowheight, "SPECIAL ENDORSEMENT",0,1, 'L');
		$this->SetFont('Arial', '', 9);
		$this->MultiCell(200, $rowheight, $this->endorsement, 0, 'L');

		//signature
		$this->SetFont('Arial', 'B', 10);
		$this->SetXY(165,330);
		$this->Cell(30, $rowheight, "Supervisor's Signature", 0, 1, 'C');
		$this->Line(150, 329.5, 210, 329.5);
	}

	/*function Footer()
	{
		$this->SetY(-23);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	} */

	//-------------------------------------
	 function SetWidths($w)
	 {
			//Set the array of column widths
			$this->widths=$w;
	 }

	 function SetAligns($a)
	 {
			//Set the array of column alignments
			$this->aligns=$a;
	 }

	 function Row($data)
	 {
		$row = 4;
			//Calculate the height of the row
			$nb=0;
			for($i=0;$i<count($data);$i++)
					$nb=max($nb,$this->NbLines($this->ColumnWidth[$i],$data[$i]));
					$nb2=$this->NbLines($this->ColumnWidth[1],$data[1]);
					$nb3=$this->NbLines($this->ColumnWidth[5],$data[5]);
					if($nb2>$nb3){
						$nbdiff = $nb2 - $nb3;
						 $nbdiff = $nbdiff*$row;
						k == 1;
					}
					else if($nb3>$nb2){
						$nbdiff = $nb3 - $nb2;
						 $nbdiff = $nbdiff*$row;
						k==0;
					}
					else{
						$nbdiff = 0;
					}

					//$nb3=max($nb,$this->NbLines($this->widths[0],$data[0]));
					//print_r($nb2, $nb3);

					//$nb = $nb*2;
					//print_r($nb);
			$h=$row*$nb;
			//Issue a page break first if needed
			$this->CheckPageBreak($h);
			//Draw the cells of the row

			for($i=0;$i<count($data);$i++)
			{
					$w=$this->ColumnWidth[$i];
					$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
					//$a = isset($this->Alignment[$i]) ? $this->Alignment[$i] : 'L';
					//Save the current position

					$x=$this->GetX();
					$y=$this->GetY();
					//Draw the border

							$length = $this->GetStringWidth($data[$i]);
							if($length < $this->ColumnWidth[$i]){
								//$this->Cell($w, $h, $data[$i],1,0,'L');
								$this->Cell($w, $h, $data[$i], 1, 0, $this->Alignment[$i]);
							}
							else{
								$nbrow = 3;
								// $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
								//$this->MultiCell($w, $row,$data[$i],1,'L');
								$this->MultiCell($w, $row, $data[$i], 1,$this->Alignment[$i]);

								//$this->MultiCell($length, $row,$data[$i],1,'L');

							}

					//Put the position to the right of the cell
					$this->SetXY($x+$w,$y);
			}
			//Go to the next line
			$this->Ln($h);
		}

		function CheckPageBreak($h) {
				//If the height h would cause an overflow, add a new page immediately
				if($this->GetY()+$h>$this->PageBreakTrigger)
						$this->AddPage($this->CurOrientation);
		}

		function NbLines($w,$txt) {
				//Computes the number of lines a MultiCell of width w will take
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

$from = $_GET['from'];
$observation = $_GET['observation'];
$material = $_GET['material'];
$environment = $_GET['environment'];
$endorsement = $_GET['endorsement'];

$pdf = new DailyAccomplishment($from, $observation, $material, $environment, $endorsement);
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Header_();
$pdf->GetData();
$pdf->AddPage();
$pdf->Problems();
$pdf->Output();
?>