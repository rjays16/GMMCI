<?php
require('./roots.php');
require($root_path."/classes/fpdf/pdf.class.php");
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

class MedicalDentalCertificate extends FPDF{
	var $from;
	var $to;
	var $count_rows;

	function MedicalDentalCertificate($from, $to){
		global $db;
		$this->ColumnWidth = array(22,56,32,15,12,80,33,25);
		$this->SetTopMargin(3);
		$this->Alignment = array('C','L','C','C','C','L','C','C');
		$this->FPDF("P", 'mm', 'Letter');

		if ($from) $this->from=date("Y-m-d",strtotime($from));
		if ($to) $this->to=date("Y-m-d",strtotime($to));
	}

	function Header() {
		global $root_path, $db;
		$rowheight = 5;
		$this->Ln($rowheight*4);

		//call class here.....

		//variables
			$title = "MEDICAL / DENTAL EXAMINATION CERTIFICATE";
			$clinic_number = "2192775";
			$date = "June 28, 2010";
		//end variables for this function

		$this->SetFont('Arial', 'BU', 14);
		$this->Cell(0, $rowheight, $title,0,1,'C');
		$this->Ln();

		$this->SetFont('Arial', 'B', 10);
		$this->Cell(0, $rowheight, "Clinic No. ".$clinic_number, 0,1,'C');
		$this->Cell(0, $rowheight, "Date: ".$date, 0, 1, 'C');

		$this->Ln($rowheight*2);

	}


	function Footer()
	{
		$this->SetY(-23);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}

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

#$from = $_GET['from'];
#$to = $_GET['to'];
#$code = $_GET['icd'];

$pdf = new MedicalDentalCertificate($_GET['from'], $_GET['to']);
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Output();
?>