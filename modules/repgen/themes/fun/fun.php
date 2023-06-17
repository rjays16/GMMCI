<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'/modules/repgen/repgen.inc.php');

class FunRepGen extends RepGen {
	var $colored = TRUE;
	var $Conn;
	
	function FunRepGen($report, $orientation="P", $paper="Letter", $conn=NULL, $colored=TRUE) {
		global $root_path;
		$this->ThemePath = $root_path."modules/repgen/themes/fun/";
		$this->Conn = &$conn;
		$this->colored = $colored;
		$this->RepGen($report, $orientation, $paper);
		if ($this->colored) {
			$this->SetFillColor(0xFF);
			$this->SetDrawColor(0xDD);
		}
	}
	
	function BeforeData() {
		if ($this->colored) {
			$this->DrawColor = array(0xDD,0xDD,0xDD);
		}
	}
	
	function BeforeCellRender() {
		$this->FONTSIZE = 8;
		if ($this->colored) {
			$this->RENDERCELL->Border='C';
			if ($this->RENDERCOLNUM==0)
				$this->RENDERCELL->BorderColorLeft=array(0x3a, 0x4a, 0x30);
			elseif ($this->RENDERCOLNUM==$this->Columns-1)
				$this->RENDERCELL->BorderColorRight=array(0x3a, 0x4a, 0x30);			
			if (($this->RENDERPAGEROWNUM%2)==0) {
				$this->RENDERCELL->BorderColorTop=array(0x24,0x3d,0x02);
				$this->RENDERCELL->TextColor = array(0x2e,0x2e,0x2e);
				$this->RENDERCELL->Image = $this->ThemePath."images/td1.jpg";
			}
			else {
				$this->RENDERCELL->BorderColorTop=array(0x5b,0x7c,0x25);
				$this->RENDERCELL->FillColor=array(0x7d, 0xbb, 0x27);
			}
		}
	}
	
	function AfterData() {
		global $db;
		
		if (!$this->_count) {
			$this->SetFont('Arial','B',9);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(0, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}
		$cols = array();
	}
	
	function Header() {
		# Print table header
    $this->SetFont('Arial','B',9);
		if ($this->colored) $this->SetFillColor(0xED);
		$thHeight = 10;
		$this->SetDrawColor(0x36,0x52,0x18);
		$this->SetTextColor(255);
		$this->Image($this->ThemePath."images/th.jpg", $this->GetX(), $this->GetY(), $this->TotalWidth, $thHeight);
		for ($i=0;$i<$this->Columns;$i++) {			
			$border = "T";
			if ($i==0) $border="TL";
			elseif ($i==$this->Columns-1) $border="TR";
			$this->Cell($this->ColumnWidth[$i], $thHeight,$this->ColumnLabels[$i],$border,0,'C',0);
		}
		
		$this->Ln();
	}
	
	function Footer()
	{
		$this->Image($this->ThemePath."images/foot.jpg", $this->GetX(), $this->GetY(), $this->TotalWidth, 10);
		$this->SetDrawColor(0x24,0x3d,0x02);
		$this->Cell($this->TotalWidth,10,'', 'T',0,'C',0);

		$this->SetY($this->GetY()-1.5);
		$this->SetFont('Arial','I',8);
		$this->SetTextColor(255);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}
}

?>