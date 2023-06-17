<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'/modules/repgen/repgen.inc.php');

class DMCRepGen extends RepGen {
	var $colored = TRUE;
	var $Conn;
	
	function DMCRepGen($report, $orientation="P", $paper="Letter", $conn=NULL, $colored=TRUE) {
		$this->Conn = &$conn;
		$this->colored = $colored;
		$this->RepGen($report, $orientation, $paper);
		if ($this->colored) {
			$this->SetFillColor(0xFF);
			#$this->SetDrawColor(0xDD);
			$this->SetDrawColor(255);
		}
	}
	
	function BeforeData() {
		$this->FONTSIZE = 8.7;
		if ($this->colored) {
			#$this->DrawColor = array(0xDD,0xDD,0xDD);
			$this->DrawColor = array(255,255,255);
		}
	}
	
	function BeforeCellRender() {
		$this->FONTSIZE = 8;
		if ($this->colored) {
			#if (($this->RENDERPAGEROWNUM%2)>0) 
			#	$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
			#else
				$this->RENDERCELL->FillColor=array(255,255,255);
		}
	}
	
	function AfterData() {
		global $db;
		
		if (!$this->_count) {
			$this->SetFont('Arial','B',10);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(0, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}
		$cols = array();
	}
	
	function Header() {
		
		# Print table header
        $this->SetFont('Arial','B',10);
		#if ($this->colored) $this->SetFillColor(0xED);
		if ($this->colored) $this->SetFillColor(255);
		$this->SetTextColor(0);
		$row=6;

		for ($i=0;$i<$this->Columns;$i++) {
			$this->Cell($this->ColumnWidth[$i],$this->RowHeight,$this->ColumnLabels[$i],1,0,'C',1);
		}
		
		$this->Ln();
	}
	
	function Footer()
	{
		$this->SetY(-7);
		$this->SetFont('Arial','',8);
		#$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}
}

?>