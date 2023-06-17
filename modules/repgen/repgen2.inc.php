<?php
#include_once($root_path."/classes/adodb/adodb.inc.php");

include_once("repgen_config.inc.php");
include_once("fpdf.php");

class RenderCell {
	var $Border;
	var $Width;
	var $Height;
	var $Align;
	var $FillColor;
	var $DrawColor;
	var $LineWidth;
	var $TextColor;
	var $FontFamily;
	var $FontSize;
	var $FontStyle;
	var $TextHeight;
	var $TextPadding;
	var $Text;
}

class RepGen extends FPDF 
{
	var $Headers;
	
	# Arrays
	var $Data;
	var $DrawColor;
	var $ColumnWidth;
	var $ColumnFontFamily;
	var $ColumnFontSize;
	var $ColumnFontStyle;
	
	# Values
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
	#var $PageOrientation;
	var $NoWrap = true;
	
	# magic variables
	var $RENDER;			# 2d array of RenderCells
	var $ROWNUM;			# the current row
	var $COLNUM;			# the current column
	var $PAGENUM; 		# the current page no.
	var $DATA;				# the current data to be rendered
	var $MAXCOLS;			# the max number of columns (for the current row)
	var $MAXROWS;			# the max number of rows

	# cell render settings
	var $DEFAULT_FONTFAMILY;
	var $DEFAULT_FONTSIZE;
	var $DEFAULT_FONTSTYLE;	
	var $DEFAULT_TEXTCOLOR;
	var $DEFAULT_FILLCOLOR;
	var $DEFAULT_DRAWCOLOR;
	var $DEFAULT_LINEWIDTH;
	var $DEFAULT_LEFTMARGIN;
	var $DEFAULT_RIGHTMARGIN;
	
	var $FONTFAMILY;
	var $FONTSIZE;
	var $FONTSTYLE;
	var $TEXTCOLOR;
	var $FILLCOLOR;
	var $DRAWCOLOR;
	var $LINEWIDTH;
	var $CELLBORDER;
	var $TEXTPADDING;
	var $TEXTHEIGHT;
	
	var $COLWIDTH;
	var $ROWHEIGHT;
	var $ALIGNMENT;
	var $LEFTMARGIN;
	var $RIGHTMARGIN;
	
	#$this->Cell($dWidth, 7, $dCell,1, 0, $dJustify, $fill);

	function RepGen($rtitle="Report Gen", $orientation="P", $format="Letter") {
		# Set default values
		$this->FPDF($orientation, __REPGEN_UNIT, $format);
		$this->rep_title = $rtitle;
		$this->setTitle($rtitle);
		#$this->PageOrientation="P";
	
		$this->Headers=array();
		$this->Data=array();
		$this->Alignment="L";

		$this->DEFAULT_ROWHEIGHT="8";
		$this->DEFAULT_TEXTPADDING="0.5";
		$this->DEFAULT_TEXTHEIGHT="5";
		$this->DEFAULT_FONTFAMILY="Arial";
		$this->DEFAULT_FONTSIZE="8";
		$this->DEFAULT_FONTSTYLE="";
		$this->DEFAULT_TEXTCOLOR=array(0,0,0);
		$this->DEFAULT_FILLCOLOR=array(255,255,255);
		$this->DEFAULT_DRAWCOLOR=array(0,0,0);
		$this->DEFAULT_LINEWIDTH=0.3;
		$this->DEFAULT_TOPMARGIN = 5;
		$this->DEFAULT_LEFTMARGIN = 10;
		$this->DEFAULT_RIGHTMARGIN = 10;
		
		$this->SetFont($this->DEFAULT_FONTFAMILY, $this->DEFAULT_FONTSTYLE, $this->DEFAULT_FONTSIZE);
		$this->SetFillColor($this->DEFAULT_FILLCOLOR[0],$this->DEFAULT_FILLCOLOR[1],$this->DEFAULT_FILLCOLOR[2]);
	}

	function _NULLIF($expr1, $expr2) {
		if (empty($expr1)) return $expr2;
		else return $expr1;
	}
	
	function _fixRenderHeights() {
		$maxLines = 0;
		foreach ($this->RENDER as $row_index=>$row_data) {
			$maxRenderHeight = 0;
			
			foreach ($row_data as $col_index=>$render_data) {
				$this->SetFont($render_data->FontFamily, $render_data->FontStyle, $render_data->FontSize);
				$width = $this->GetStringWidth($render_data->Text);
				$lines=1;
				if ($width > ($render_data->Width-$render_data->TextPadding*2)) {
					$lines = floor((float) $width / ((float)($render_data->Width)-$render_data->TextPadding*2))+1;
				}
				$renderHeight = $render_data->Height*$lines;
				$this->RENDER[$row_index][$col_index]->RenderHeight = $renderHeight;
				if ($renderHeight > $maxRenderHeight) {
					$maxRenderHeight = $renderHeight;
				}
			}
			
			foreach ($row_data as $col_index=>$render_data) 
				$this->RENDER[$row_index][$col_index]->RenderHeight = $maxRenderHeight;
		}
	}
	
	function _fixEllipsis(&$render) {
		$this->SetFont($render->FontFamily, $render->FontStyle, $render->FontSize);
		$width = $this->GetStringWidth($render->Text);
		if ($width > $render->Width-1.0) {
			$adjustWidth = $render->Width - 1.0 - $this->GetStringWidth('...');
			$ratio = (float)$adjustWidth/(float)$width;
			$render->Text = substr($render->Text,0,floor($ratio * strlen($render->Text))-1)."...";
		}
	}
	
	function Report($new_page=TRUE) {
		$this->Render = array();
		$this->RENDER = array();
		$this->ROWNUM=0;
		$this->COLNUM=0;
		$this->SetLeftMargin($this->_NULLIF($this->LEFTMARGIN, $this->DEFAULT_LEFTMARGIN));
		$this->SetRightMargin($this->_NULLIF($this->RIGHTMARGIN, $this->DEFAULT_RIGHTMARGIN));
		$this->SetTopMargin($this->_NULLIF($this->TOPMARGIN, $this->DEFAULT_TOPMARGIN));
		if ($new_page) $this->AddPage($this->PageOrientation);
		$this->MAXROWS = count($this->Data);
		
		if (method_exists($this,"BeforeData")) $this->BeforeData();
		if (is_array($this->Data)) {
			foreach ($this->Data as $row) {
				$this->MAXCOLS = count($row);
				$this->DATA=$row;
				if (method_exists($this,"BeforeRow")) $this->BeforeRow();
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
					$this->ROWHEIGHT=($this->_NULLIF($this->RowHeight, $this->DEFAULT_ROWHEIGHT));
					
					$this->DATA=$cell;
					$this->CELLBORDER=1;
					
					if ($this->DrawColor) $this->DRAWCOLOR = (is_array($this->DrawColor[0]) ? $this->DrawColor[$this->COLNUM] : $this->DrawColor);
					if ($this->ColumnFontFamily) $this->FONTFAMILY = (is_array($this->ColumnFontFamily) ? $this->ColumnFontFamily[$this->COLNUM] : $this->ColumnFontFamily);
					if ($this->ColumnFontSize) $this->FONTSIZE = (is_array($this->ColumnFontSize) ? $this->ColumnFontFamily[$this->COLNUM] : $this->ColumnFontSize);
					if ($this->ColumnFontStyle) $this->FONTSTYLE = (is_array($this->ColumnFontStyle) ? $this->ColumnFontFamily[$this->COLNUM] : $this->ColumnFontStyle);
					
					$this->TEXTHEIGHT = $this->TextHeight;
					$this->TEXTPADDING = $this->TextPadding;
					
					if (method_exists($this,"BeforeCell")) $this->BeforeCell();
					
					$text_height = $this->_NULLIF($this->TEXTHEIGHT, $this->DEFAULT_TEXTHEIGHT); 
					$text_padding = $this->_NULLIF($this->TEXTPADDING, $this->DEFAULT_TEXTPADDING); 
					
					$font_family = $this->_NULLIF($this->FONTFAMILY, $this->DEFAULT_FONTFAMILY);
					$font_style = $this->_NULLIF($this->FONTSTYLE, $this->DEFAULT_FONTSTYLE);
					$font_size = $this->_NULLIF($this->FONTSIZE, $this->DEFAULT_FONTSIZE);
					
					$line_width = $this->_NULLIF($this->LINEWIDTH, $this->DEFAULT_LINEWIDTH);
					
					$text_col = $this->_NULLIF($this->TEXTCOLOR, $this->DEFAULT_TEXTCOLOR);
					$draw_col = $this->_NULLIF($this->DRAWCOLOR, $this->DEFAULT_DRAWCOLOR);
					$fill_col = $this->_NULLIF($this->FILLCOLOR, $this->DEFAULT_FILLCOLOR);
					#$this->SetTextColor($text_col[0],$text_col[1],$text_col[2]);
					#$this->SetDrawColor($draw_col[0],$draw_col[1],$draw_col[2]);
					#$this->SetFillColor($fill_col[0],$fill_col[1],$fill_col[2]);
					
					/*
					TRY FOR RENDER
					if ($this->useMultiCell) {
						$this->Cell($this->COLWIDTH, $this->ROWHEIGHT, $this->DATA, $this->CELLBORDER, $this->ALIGNMENT, 1);
					}
					else
						$this->Cell($this->COLWIDTH, $this->ROWHEIGHT, $this->DATA, $this->CELLBORDER, 0, $this->ALIGNMENT, 1);
					*/
					
					$render = new RenderCell;
					$render->Border = $this->CELLBORDER;
					$render->Width = $this->COLWIDTH;
					$render->Height = $this->ROWHEIGHT;
					$render->Align = $this->ALIGNMENT;
					$render->FillColor = $fill_col;
					$render->DrawColor = $draw_col;
					$render->TextColor = $text_col;
					$render->LineWidth = $line_width;
					$render->FontFamily = $font_family;
					$render->FontStyle = $font_style;
					$render->FontSize = $font_size;
					$render->TextPadding = $text_padding;
					$render->TextHeight = $text_height;
					$render->Text = $this->DATA;
					
					# Fill Render Matrix
					$this->RENDER[$this->ROWNUM][$this->COLNUM] = $render;
					
					if (method_exists($this,"AfterCell")) $this->AfterCell();
					$this->COLNUM++;
				}
				if (method_exists($this,"AfterRow")) $this->AfterRow();
				# TRY FOR RENDER $this->Ln();
				$this->ROWNUM++;
			}
		}


		# Process the Render Matrix

		if (method_exists($this,"BeforeRender")) $this->BeforeRender();
		
		$this->RENDERROWNUM=0;
		$this->RENDERPAGEROWNUM=0;
		if (!$this->NoWrap) $this->_fixRenderHeights();
		foreach ($this->RENDER as $row_index=>$this->RENDERROW) {
			
			$this->RENDERROWX = $this->GetX();
			$this->RENDERROWY = $this->GetY();
			$this->RENDERCOLNUM=0;
			
			$maxRenderHeight = 0;
			
			if (method_exists($this,"BeforeRowRender")) $this->BeforeRowRender();
			foreach ($this->RENDERROW as $col_index=>$this->RENDERCELL) {
				#$this->RENDERCELL = &$render_data;

				$this->RENDERCELLX = $this->GetX();
				$this->RENDERCELLY = $this->GetY();
				
				# Render blank row to trigger PageBreak
				$renderHeight = $this->RENDERCELL->RenderHeight ? $this->RENDERCELL->RenderHeight : $this->RENDERCELL->Height;
				$this->Cell($this->RENDERCELL->Width, $renderHeight, '', 0, 0, '', 0);
				
				# Update current X,Y position in case of page break
				if ($this->GetY() < $this->RENDERCELLY) {
					$this->RENDERCELLY = $this->GetY();
					$this->RENDERROWY = $this->RENDERCELLY;
				}
				
				if (method_exists($this,"BeforeCellRender")) $this->BeforeCellRender();
				
				if ($this->NoWrap) $this->_fixEllipsis($this->RENDERCELL);
				
				$this->SetFillColor($this->RENDERCELL->FillColor[0], $this->RENDERCELL->FillColor[1], $this->RENDERCELL->FillColor[2]);
				$this->SetDrawColor($this->RENDERCELL->DrawColor[0], $this->RENDERCELL->DrawColor[1], $this->RENDERCELL->DrawColor[2]);
				$this->SetTextColor($this->RENDERCELL->TextColor[0], $this->RENDERCELL->TextColor[1], $this->RENDERCELL->TextColor[2]);
				$this->SetLineWidth($this->RENDERCELL->LineWidth);
				$this->SetFont($this->RENDERCELL->FontFamily, $this->RENDERCELL->FontStyle, $this->RENDERCELL->FontSize);
				
				# Render empty row with background fill
				$this->SetXY($this->RENDERCELLX, $this->RENDERCELLY);
				$renderHeight = $this->RENDERCELL->RenderHeight ? $this->RENDERCELL->RenderHeight : $this->RENDERCELL->Height;
				$this->Cell($this->RENDERCELL->Width, $renderHeight, '', 0, 0, '', 1);

				# Render text first before borders
				$this->SetXY($this->RENDERCELLX, $this->RENDERCELLY);
				if (!$this->NoWrap)
					$this->MultiCell($this->RENDERCELL->Width, $this->RENDERCELL->TextHeight, $this->RENDERCELL->Text, 0, $this->RENDERCELL->Align);
				else
					$this->Cell($this->RENDERCELL->Width, $this->RENDERCELL->Height, $this->RENDERCELL->Text, 0, 0, $this->RENDERCELL->Align, 0);
				
				# Render cell borders
				$this->SetXY($this->RENDERCELLX, $this->RENDERCELLY);
				$renderHeight = $this->RENDERCELL->RenderHeight ? $this->RENDERCELL->RenderHeight : $this->RENDERCELL->Height;
				$this->Cell($this->RENDERCELL->Width, $renderHeight, '', $this->RENDERCELL->Border);
				
				if ($renderHeight > $maxRenderHeight) $maxRenderHeight = $renderHeight;
				$this->RENDERCOLNUM++;
			}
			
			$this->SetXY($this->RENDERROWX, $this->RENDERROWY);
			$this->Ln($maxRenderHeight);
			$this->RENDERROWNUM++;
			$this->RENDERPAGEROWNUM++;
			
			if (method_exists($this,"AfterRowRender")) $this->AfterRowRender();
		}
		
		if (method_exists($this,"AfterRender")) $this->AfterRender();
		
		if (method_exists($this,"AfterData")) $this->AfterData();
		$this->Output();
	}

	function AcceptPageBreak() {
		$this->BreakPage = TRUE;
		if (method_exists($this,"BeforePageBreak")) $this->BeforePageBreak();
		$this->RENDERPAGEROWNUM=0;
		
		if (method_exists($this,"AfterPageBreak")) $this->AfterPageBreak();
		return $this->BreakPage;
	}	
}

?>