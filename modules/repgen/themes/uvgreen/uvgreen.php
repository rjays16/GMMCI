<?php

class RepGenTheme {
	var $ThemeName;
	var $ThemePath;
	
	function RepGenTheme(&$report) {
		$this->ThemeName = "UV Green";
		$this->Report =& $report;
	}
							
	function BeforeCellRender() {
		$this->Report->FONTSIZE = 8;
		$this->Report->RENDERCELL->Border='C';
		if ($this->Report->RENDERCOLNUM==0)
			$this->Report->RENDERCELL->BorderColorLeft=array(0x3a, 0x4a, 0x30);
		elseif ($this->Report->RENDERCOLNUM==$this->Report->Columns-1)
			$this->Report->RENDERCELL->BorderColorRight=array(0x3a, 0x4a, 0x30);			
		if (($this->Report->RENDERPAGEROWNUM%2)==0) {
			$this->Report->RENDERCELL->BorderColorTop=array(0x24,0x3d,0x02);
			$this->Report->RENDERCELL->TextColor = array(0x2e,0x2e,0x2e);
			$this->Report->RENDERCELL->Image = $this->Report->ThemePath."images/td1.jpg";
		}
		else {
			$this->Report->RENDERCELL->BorderColorTop=array(0x5b,0x7c,0x25);
			$this->Report->RENDERCELL->FillColor=array(0x7d, 0xbb, 0x27);
		}
	}
	
	function AfterData() {
		global $db;
		
		if (!$this->Report->_count) {
			$this->Report->SetFont('Arial','B',9);
			$this->Report->SetFillColor(255);
			$this->Report->SetTextColor(0);
			$this->Report->Cell(0, $this->Report->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}
	}
	
	function Header() {
		# Print table caption
		$thHeight = 10;
		$x=$this->Report->GetX();
		$y=$this->Report->GetY();
		if ($this->Report->Caption) {
			$this->Report->Image($this->Report->ThemePath."images/caption.jpg", $x, $y, $this->Report->TotalWidth, $thHeight);
	    $this->Report->SetFont('Arial','B',13);
			$this->Report->SetTextColor(0x24, 0x3d, 0x02);
			$this->Report->Cell($this->Report->TotalWidth, $thHeight, $this->Report->Caption, 0,1,'L');
		}
		
		# Column headers
		$x=$this->Report->GetX();
		$y=$this->Report->GetY();
    $this->Report->SetFont('Arial','B',9);
		$this->Report->SetFillColor(0xED);
		$this->Report->SetDrawColor(0x36,0x52,0x18);
		$this->Report->SetTextColor(255);
		$this->Report->Image($this->Report->ThemePath."images/th.jpg", $this->Report->GetX(), $this->Report->GetY(), $this->Report->TotalWidth, $thHeight);
		for ($i=0;$i<$this->Report->Columns;$i++) {			
			$border = "T";
			if ($i==0) $border="TL";
			elseif ($i==$this->Report->Columns-1) $border="TR";
			$this->Report->Cell($this->Report->ColumnWidth[$i], $thHeight,$this->Report->ColumnLabels[$i],$border,0,'C',0);
		}
		
		$this->Report->Ln();
	}
	
	function Footer()
	{
		$this->Report->Image($this->Report->ThemePath."images/foot.jpg", $this->Report->GetX(), $this->Report->GetY(), $this->Report->TotalWidth, 10);
		$this->Report->SetDrawColor(0x24,0x3d,0x02);
		$this->Report->Cell($this->Report->TotalWidth,10,'', 'T',0,'C',0);

		$this->Report->SetY($this->Report->GetY()-1.5);
		$this->Report->SetFont('Arial','I',8);
		$this->Report->SetTextColor(255);
		$this->Report->Cell(0,10,'Page '.$this->Report->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}
}

?>