<?php

#include_once($root_path."/classes/adodb/adodb.inc.php");
include_once("repgen_config.inc.php");
include_once("fpdf.php");

class RepGen extends FPDF 
{
	var $Headers;
	var $Data;
	var $ColumnWidth;
	var $RowHeight;
	var $Alignment;
	var $PageOrientation;
	
	# Current header settings
	var $header_font;
	var $header_style;
	var $header_size;
	var $header_border;
	
	# Current row settings
	var $row_font;
	var $row_height;
	var $row_border;
	var $row_linestyle;
	var $row_align;
	var $row_fill;
	
	# Curent report settings
	var $ReportTitle;
	var $PageOrientation;
	
	# magic variables
	var $ROWNUM;			# the current row
	var $COLNUM;			# the current column
	var $PAGENUM; 		# the current page no.
	var $DATA;				# the current data to be rendered

	# cell render settings
	var $DEFAULT_FONTFAMILY;
	var $DEFAULT_FONTSIZE;
	var $DEFAULT_FONTSTYLE;	
	var $DEFAULT_TEXTCOLOR;
	var $DEFAULT_FILLCOLOR;
	var $DEFAULT_DRAWCOLOR;
	var $DEFAULT_LINEWIDTH;
	
	var $FONTFAMILY;
	var $FONTSIZE;
	var $FONTSTYLE;
	var $TEXTCOLOR;
	var $FILLCOLOR;
	var $DRAWCOLOR;
	var $LINEWIDTH;
	var $CELLBORDER;
	
	var $COLWIDTH;
	var $ROWHEIGHT;
	var $ALIGNMENT;
	
	function RepGen($rtitle="Report Gen") {
		# Set default values
		$this->FPDF();
		$this->rep_title = $rtitle;
		$this->setTitle($rtitle);
		$this->PageOrientation="P";
	
		$this->Headers=array();
		$this->Data=array();
		$this->ColumnWidth=40;
		$this->Alignment="L";
		$this->RowHeight=8;
		
		$this->DEFAULT_FONTFAMILY="Arial";
		$this->DEFAULT_FONTSIZE="10";
		$this->DEFAULT_FONTSTYLE="";
		$this->DEFAULT_TEXTCOLOR=array(48,48,48);
		$this->DEFAULT_FILLCOLOR=array(230,230,230);
		$this->DEFAULT_DRAWCOLOR=array(48,48,48);
		$this->DEFAULT_LINEWIDTH=0.3;
	}

	function _NULLIF($expr1, $expr2) {
		if (empty($expr1)) return $expr2;
		else return $expr1;
	}
	
	function Report() {
		$this->ROWNUM=0;
		$this->COLNUM=0;
		$this->AddPage($this->PageOrientation);
		foreach ($this->Data as $row) {
			$this->DATA=$row;
			$this->BeforeRow();
			$this->COLNUM=0;
			$mod_row=$this->DATA;
			foreach ($mod_row as $cell) {
				if ($this->Alignment===NULL)
					$this->ALIGNMENT=(is_numeric($dCell))?"R":"L";
				elseif (is_array($this->Alignment)) 
					$this->ALIGNMENT=$this->Alignment[$this->COLNUM];
				else
					$this->ALIGNMENT=$this->Alignment;
				if (is_array($this->ColumnWidth)) 
					$this->COLWIDTH=$this->ColumnWidth[$this->COLNUM];
				else
					$this->COLWIDTH=$this->ColumnWidth;
				$this->ROWHEIGHT=$this->RowHeight;
				
				$this->DATA=$cell;
				$this->SetFont(
					$this->_NULLIF($this->FONTFAMILY, $this->DEFAULT_FONTFAMILY),
					$this->_NULLIF($this->FONTSTYLE, $this->DEFAULT_FONTSTYLE),
					$this->_NULLIF($this->FONTSIZE, $this->DEFAULT_FONTSIZE)
				);
				$this->SetLineWidth($this->_NULLIF($this->LINEWIDTH, $this->DEFAULT_LINEWIDTH));
				$text_col = $this->_NULLIF($this->TEXTCOLOR, $this->DEFAULT_TEXTCOLOR);
				$draw_col = $this->_NULLIF($this->CRAWCOLOR, $this->DEFAULT_DRAWCOLOR);
				$fill_col = $this->_NULLIF($this->FILLCOLOR, $this->DEFAULT_FILLCOLOR);
				$this->SetTextColor($text_col[0],$text_col[1],$text_col[2]);
				$this->SetDrawColor($draw_col[0],$draw_col[1],$draw_col[2]);
				$this->SetFillColor($fill_col[0],$fill_col[1],$fill_col[2]);
				$this->CELLBORDER=1;
				
				$this->BeforeCell();
				$this->Cell($this->COLWIDTH, $this->ROWHEIGHT, $this->DATA, $this->CELLBORDER, 0, $this->ALIGNMENT, 1);
				$this->AfterCell();
				$this->COLNUM++;
			}
			$this->AfterRow();
			$this->Ln();
			$this->ROWNUM++;
		}
		$this->Output();
	}
	
	# Prototype functions
	function BeforeRow() {
	}
	
	function AfterRow() {
	}

	function BeforeCell() {
	}
	
	function AfterCell() {
	}
}

?>