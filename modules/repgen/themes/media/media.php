<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'/modules/repgen/repgen.inc.php');

class MediaRepGen extends RepGen {
	var $colored = TRUE;
	var $Conn;
	
	function MediaRepGen($report, $orientation="P", $paper="Letter", $conn=NULL, $colored=TRUE) {
		global $root_path;
		$this->ThemePath = $root_path."modules/repgen/themes/media/";
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
				$this->RENDERCELL->BorderColorLeft=array(0xff, 0xca, 0x5e);
			elseif ($this->RENDERCOLNUM==$this->Columns-1)
				$this->RENDERCELL->BorderColorRight=array(0xff, 0xca, 0x5e);
			$this->RENDERCELL->BorderColorTop=array(0xff, 0xca, 0x5e);
			if (($this->RENDERPAGEROWNUM%2)==0) {
				$this->RENDERCELL->TextColor = array(0x2e,0x2e,0x2e);
				$this->RENDERCELL->Image = $this->ThemePath."images/bg_td1.jpg";
			}
			else {
				$this->RENDERCELL->Image = $this->ThemePath."images/bg_td2.jpg";
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
		$thHeight = 10;
		
		# Print table caption
		$x=$this->GetX();
		$y=$this->GetY();
		if ($this->Caption) {
			$dWidth = $this->TotalWidth * 0.4;
			$this->Image($this->ThemePath."images/bg_caption.jpg", $x, $y, $this->TotalWidth, $thHeight);
			$this->Image($this->ThemePath."images/bg_caption2.jpg", $x+$this->TotalWidth-$dWidth, $y, $dWidth, $thHeight);
			
	    $this->SetFont('Arial','B',13);
			$this->SetTextColor(0xff, 0xca, 0x5e);
			$this->Cell($$this->TotalWidth, $thHeight, $this->Caption, 0,1,'L');
		}

		# Column headers
    $this->SetFont('Arial','B',9);
		if ($this->colored) $this->SetFillColor(0xED);

		$this->SetTextColor(255);
		for ($i=0;$i<$this->Columns;$i++) {			
			$border = 'T';
			if ($i==0) $border="TL";
			elseif ($i==$this->Columns-1) $border="TR";
			$x=$this->GetX();
			$y=$this->GetY();
			$this->Image($this->ThemePath."images/bg_th.jpg", $x, $y, $this->ColumnWidth[$i]-3, $thHeight);
			$this->Image($this->ThemePath."images/bg_th2.jpg", $x+$this->ColumnWidth[$i]-3, $y, 3, $thHeight);
			$this->SetDrawColor(255);
			$this->Cell($this->ColumnWidth[$i], $thHeight,'', 1,0,'C',0);
			$this->SetXY($x, $y);
			$this->SetDrawColor(0xff, 0xca, 0x5e);
			$this->Cell($this->ColumnWidth[$i], $thHeight,$this->ColumnLabels[$i],$border,0,'C',0);
		}
		
		$this->Ln();
	}
	
	function Footer()
	{
		$this->Image($this->ThemePath."images/bg_total.jpg", $this->GetX(), $this->GetY(), $this->TotalWidth, 10);
		$this->SetDrawColor(0xff, 0xca, 0x5e);
		$this->Cell($this->TotalWidth,10,'', 1,0,'C',0);
		$this->SetY($this->GetY()-0);
		$this->SetFont('Arial','BI',8);
		$this->SetTextColor(255);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}
}

?>