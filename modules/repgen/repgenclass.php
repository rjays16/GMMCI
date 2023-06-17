<?php
#include_once($root_path."/classes/adodb/adodb.inc.php");

include_once("repgen_config.inc.php");
include_once("fpdf.php");

class RenderCell {
	var $Border;
	var $BorderColorLeft;
	var $BorderColorRight;
	var $BorderColorTop;
	var $BorderColorBottom;
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
	var $Image;
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
	var $Theme;

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

		$this->DEFAULT_ROWHEIGHT="6 ";
		$this->DEFAULT_TEXTPADDING=array('T'=>'1','B'=>'1','L'=>'1','R'=>'1');
		$this->DEFAULT_TEXTHEIGHT="3";
		$this->DEFAULT_FONTFAMILY="Arial";
		$this->DEFAULT_FONTSIZE="8";
		$this->DEFAULT_FONTSTYLE="";
		$this->DEFAULT_TEXTCOLOR=array(0,0,0);
		$this->DEFAULT_FILLCOLOR=array(255,255,255);
		$this->DEFAULT_DRAWCOLOR=array(0,0,0);
		$this->DEFAULT_LINEWIDTH=0.2;
		$this->DEFAULT_TOPMARGIN = 10;
		$this->DEFAULT_LEFTMARGIN = 10;
		$this->DEFAULT_RIGHTMARGIN = 10;

		$this->SetFont($this->DEFAULT_FONTFAMILY, $this->DEFAULT_FONTSTYLE, $this->DEFAULT_FONTSIZE);
		$this->SetFillColor($this->DEFAULT_FILLCOLOR[0],$this->DEFAULT_FILLCOLOR[1],$this->DEFAULT_FILLCOLOR[2]);
	}

	function _nullif($expr1, $expr2) {
		if (empty($expr1) && $expr1!=="0") return $expr2;
		else return $expr1;
	}

	function UseTheme($theme) {
		global $root_path;
		require("themes/$theme/$theme.php");
		$this->Theme = new RepGenTheme($this);
		$this->ThemePath = $root_path."modules/repgen/themes/$theme/";
	}

	function UnloadTheme() {
		$this->Theme = NULL;
	}

	function _fixRenderHeights() {
		$maxLines = 0;
		foreach ($this->RENDER as $row_index=>$row_data) {
			$maxRenderHeight = 0;

			foreach ($row_data as $col_index=>$render_data) {
				$this->SetFont($render_data->FontFamily, $render_data->FontStyle, $render_data->FontSize);
				$str_width = $this->GetStringWidth($render_data->Text);
				$cell_width = ((float) $render_data->Width) - ((float) $render_data->TextPadding['L']) - ((float) $render_data->TextPadding['R']);
				$lines=1;
				if ($str_width > $cell_width) {
					$lines = floor((float) $str_width / $cell_width)+1;
				}
				$renderHeight = $render_data->TextHeight*$lines + $render_data->TextPadding['T'] + $render_data->TextPadding['B'];
				$this->RENDER[$row_index][$col_index]->RenderHeight = $renderHeight;
				if ($renderHeight > $maxRenderHeight) {
					$maxRenderHeight = $renderHeight;
				}
			}

			foreach ($row_data as $col_index=>$render_data)
				$this->RENDER[$row_index][$col_index]->RenderHeight = $maxRenderHeight;
		}
	}

	function Header() {
		$this->TotalWidth = 0;
		foreach ($this->ColumnWidth as $w) {
			$this->TotalWidth += (float)$w;
		}
		if ($this->Theme) $this->Theme->Header();
		else {
			$this->SetFont('Arial','B',9);
			$tcol = $this->_nullif($this->TEXTCOLOR, $this->DEFAULT_TEXTCOLOR);
			if (is_array($tcol))
				$this->SetTextColor( $tcol[0], $tcol[1], $tcol[2]);
			else
				$this->SetTextColor($tcol);
			$this->SetLineWidth($this->_nullif($this->LINEWIDTH, $this->DEFAULT_LINEWIDTH));
			$row=6;
			for ($i=0;$i<$this->Columns;$i++) {
				$this->Cell($this->ColumnWidth[$i],$this->RowHeight,$this->ColumnLabels[$i],1,0,'C',1);
			}
			$this->Ln();
		}
	}

	function Footer() {
		if ($this->Theme) $this->Theme->Footer();
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

	function WriteText($text) {
		$intPosIni = 0;
		$intPosFim = 0;
		if (strpos($text,'<')!==false and strpos($text,'[')!==false) {
			if (strpos($text,'<')<strpos($text,'[')) {
				$this->Write(5,substr($text,0,strpos($text,'<')));
				$intPosIni = strpos($text,'<');
				$intPosFim = strpos($text,'>');
				$this->SetFont('','B');
				$this->Write(5,substr($text,$intPosIni+1,$intPosFim-$intPosIni-1));
				$this->SetFont('','');
				$this->WriteText(substr($text,$intPosFim+1,strlen($text)));
			}
			else {
				$this->Write(5,substr($text,0,strpos($text,'[')));
				$intPosIni = strpos($text,'[');
				$intPosFim = strpos($text,']');
				$w=$this->GetStringWidth('a')*($intPosFim-$intPosIni-1);
				$this->Cell($w,$this->FontSize+0.75,substr($text,$intPosIni+1,$intPosFim-$intPosIni-1),1,0,'');
				$this->WriteText(substr($text,$intPosFim+1,strlen($text)));
			}
		}
		else {
			if (strpos($text,'<')!==false) {
				$this->Write(5,substr($text,0,strpos($text,'<')));
				$intPosIni = strpos($text,'<');
				$intPosFim = strpos($text,'>');
				$this->SetFont('','B');
				$this->WriteText(substr($text,$intPosIni+1,$intPosFim-$intPosIni-1));
				$this->SetFont('','');
				$this->WriteText(substr($text,$intPosFim+1,strlen($text)));
			}
			elseif (strpos($text,'[')!==false) {
				$this->Write(5,substr($text,0,strpos($text,'[')));
				$intPosIni = strpos($text,'[');
				$intPosFim = strpos($text,']');
				$w=$this->GetStringWidth('a')*($intPosFim-$intPosIni-1);
				$this->Cell($w,$this->FontSize+0.75,substr($text,$intPosIni+1,$intPosFim-$intPosIni-1),1,0,'');
				$this->WriteText(substr($text,$intPosFim+1,strlen($text)));
			}
			else {
				$this->Write(5,$text);
			}
		}
	}

	function Report() {

	require('./roots.php');
    require_once($root_path.'include/error_handlers/permission_handler.php');
    $errorObject = new PermissionHandler();
    $hasSession = $errorObject->hasSession(); //true //false return

    if($hasSession['hasSession']){
		$this->Render = array();
		$this->ROWNUM=0;
		$this->COLNUM=0;

		$this->LEFTMARGIN = $this->LeftMargin;
		$this->RIGHTMARGIN = $this->RightMargin;
		$this->TOPMARGIN = $this->TopMargin;

		$this->SetLeftMargin($this->_nullif($this->LEFTMARGIN, $this->DEFAULT_LEFTMARGIN));
		$this->SetRightMargin($this->_nullif($this->RIGHTMARGIN, $this->DEFAULT_RIGHTMARGIN));
		$this->SetTopMargin($this->_nullif($this->TOPMARGIN, $this->DEFAULT_TOPMARGIN));
		$this->AddPage($this->PageOrientation);
		$this->MAXROWS = count($this->Data);

		# BeforeData
		if (method_exists($this->Theme,"BeforeData")) $this->Theme->BeforeData();
		if (method_exists($this,"BeforeData")) $this->BeforeData();

		if (is_array($this->Data)) {
			foreach ($this->Data as $row) {
				$this->MAXCOLS = count($row);
				$this->DATA=$row;

				# BeforeRow
				if (method_exists($this->Theme,"BeforeRow")) $this->Theme->BeforeRow();
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
					$this->ROWHEIGHT=($this->_nullif($this->RowHeight, $this->DEFAULT_ROWHEIGHT));

					$this->DATA=$cell;
					$this->CELLBORDER=1;

					if ($this->DrawColor) $this->DRAWCOLOR = (is_array($this->DrawColor[0]) ? $this->DrawColor[$this->COLNUM] : $this->DrawColor);
					if ($this->ColumnFontFamily) $this->FONTFAMILY = (is_array($this->ColumnFontFamily) ? $this->ColumnFontFamily[$this->COLNUM] : $this->ColumnFontFamily);
					if ($this->ColumnFontSize) $this->FONTSIZE = (is_array($this->ColumnFontSize) ? $this->ColumnFontFamily[$this->COLNUM] : $this->ColumnFontSize);
					if ($this->ColumnFontStyle) $this->FONTSTYLE = (is_array($this->ColumnFontStyle) ? $this->ColumnFontFamily[$this->COLNUM] : $this->ColumnFontStyle);

					$this->TEXTHEIGHT = $this->TextHeight;
					$this->TEXTPADDING = $this->TextPadding;

					# BeforeCell
					if (method_exists($this->Theme,"BeforeCell")) $this->Theme->BeforeCell();
					if (method_exists($this,"BeforeCell")) $this->BeforeCell();

					$text_height = $this->_nullif($this->TEXTHEIGHT, $this->DEFAULT_TEXTHEIGHT);
					$text_padding = $this->_nullif($this->TEXTPADDING, $this->DEFAULT_TEXTPADDING);

					$font_family = $this->_nullif($this->FONTFAMILY, $this->DEFAULT_FONTFAMILY);
					$font_style = $this->_nullif($this->FONTSTYLE, $this->DEFAULT_FONTSTYLE);
					$font_size = $this->_nullif($this->FONTSIZE, $this->DEFAULT_FONTSIZE);

					$line_width = $this->_nullif($this->LINEWIDTH, $this->DEFAULT_LINEWIDTH);

					$text_col = $this->_nullif($this->TEXTCOLOR, $this->DEFAULT_TEXTCOLOR);
					$draw_col = $this->_nullif($this->DRAWCOLOR, $this->DEFAULT_DRAWCOLOR);
					$fill_col = $this->_nullif($this->FILLCOLOR, $this->DEFAULT_FILLCOLOR);

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

					# Fill values for the Render Matrix
					$this->RENDER[$this->ROWNUM][$this->COLNUM] = $render;

					# AfterCell
					if (method_exists($this->Theme,"AfterCell")) $this->Theme->AfterCell();
					if (method_exists($this,"AfterCell")) $this->AfterCell();

					$this->COLNUM++;
				}
				if (method_exists($this,"AfterRow")) $this->AfterRow();
				# TRY FOR RENDER $this->Ln();
				$this->ROWNUM++;
			}
		}


		# Process the Render Matrix

		#BeforeRender
		if (method_exists($this->Theme,"BeforeRender")) $this->Theme->BeforeRender();
		if (method_exists($this,"BeforeRender")) $this->BeforeRender();

		$this->RENDERROWNUM=0;
		$this->RENDERPAGEROWNUM=0;
		if (!$this->NoWrap) $this->_fixRenderHeights();
		foreach ($this->RENDER as $row_index=>$this->RENDERROW) {

			$this->RENDERROWX = $this->GetX();
			$this->RENDERROWY = $this->GetY();
			$this->RENDERCOLNUM=0;

			$maxRenderHeight = 0;

			# BeforeRowRender
			if (method_exists($this->Theme,"BeforeRowRender")) $this->Theme->BeforeRowRender();
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

				# BeforeCellRender
				if (method_exists($this->Theme,"BeforeCellRender")) $this->Theme->BeforeCellRender();
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
				if ($this->RENDERCELL->Image) {
					$this->Image($this->RENDERCELL->Image, $this->RENDERCELLX, $this->RENDERCELLY, $this->RENDERCELL->Width, $renderHeight);
				}
				else
					$this->Cell($this->RENDERCELL->Width, $renderHeight, '', 0, 0, '', 1);

				# Render text first before borders
				$this->SetXY($this->RENDERCELLX, $this->RENDERCELLY);

				# Top padding
				$this->Cell($this->RENDERCELL->Width, $this->RENDERCELL->TextPadding['T'], '', 0, 0, 0);
				$this->SetXY($this->RENDERCELLX+$this->RENDERCELL->Padding['L'], $this->RENDERCELLY + $this->RENDERCELL->TextPadding['T']);

				#$this->SetDrawColor(255,0,0);
				if (!$this->NoWrap)
					$this->MultiCell($this->RENDERCELL->Width - $this->RENDERCELL->Padding['L'] - $this->RENDERCELL->Padding['R'], $this->RENDERCELL->TextHeight, $this->RENDERCELL->Text, 0, $this->RENDERCELL->Align);
				else
					$this->Cell($this->RENDERCELL->Width - $this->RENDERCELL->Padding['L'] - $this->RENDERCELL->Padding['R'], $this->RENDERCELL->Height, $this->RENDERCELL->Text, 0, 0, $this->RENDERCELL->Align, 0);
				#$this->SetDrawColor(0);

				# Render cell borders
				$this->SetXY($this->RENDERCELLX, $this->RENDERCELLY);
				$renderHeight = $this->RENDERCELL->RenderHeight ? $this->RENDERCELL->RenderHeight : $this->RENDERCELL->Height;
				if ($this->RENDERCELL->Border != "C") {
					// None-custom borders
					$this->Cell($this->RENDERCELL->Width, $renderHeight, '', $this->RENDERCELL->Border);
				}
				else {
					// Render custom borders
					if ($this->RENDERCELL->BorderColorTop) {
						$this->SetDrawColor($this->RENDERCELL->BorderColorTop[0], $this->RENDERCELL->BorderColorTop[1], $this->RENDERCELL->BorderColorTop[2]);
						$this->Cell($this->RENDERCELL->Width, $renderHeight, '', 'T');
					}
					if ($this->RENDERCELL->BorderColorRight) {
						$this->SetXY($this->RENDERCELLX, $this->RENDERCELLY);
						$this->SetDrawColor($this->RENDERCELL->BorderColorRight[0], $this->RENDERCELL->BorderColorRight[1], $this->RENDERCELL->BorderColorRight[2]);
						$this->Cell($this->RENDERCELL->Width, $renderHeight, '', 'R');
					}
					if ($this->RENDERCELL->BorderColorBottom) {
						$this->SetXY($this->RENDERCELLX, $this->RENDERCELLY);
						$this->SetDrawColor($this->RENDERCELL->BorderColorBottom[0], $this->RENDERCELL->BorderColorBottom[1], $this->RENDERCELL->BorderColorBottom[2]);
						$this->Cell($this->RENDERCELL->Width, $renderHeight, '', 'B');
					}
					if ($this->RENDERCELL->BorderColorLeft) {
						$this->SetXY($this->RENDERCELLX, $this->RENDERCELLY);
						$this->SetDrawColor($this->RENDERCELL->BorderColorLeft[0], $this->RENDERCELL->BorderColorLeft[1], $this->RENDERCELL->BorderColorLeft[2]);
						$this->Cell($this->RENDERCELL->Width, $renderHeight, '', 'L');
					}
				}

				if ($renderHeight > $maxRenderHeight) $maxRenderHeight = $renderHeight;

				# AfterCellRender
				if (method_exists($this->Theme,"AfterCellRender")) $this->Theme->AfterCellRender();
				if (method_exists($this,"AfterCellRender")) $this->AfterCellRender();

				$this->RENDERCOLNUM++;
			}
			$this->SetXY($this->RENDERROWX, $this->RENDERROWY);
			$this->Ln($maxRenderHeight);
			$this->RENDERROWNUM++;
			$this->RENDERPAGEROWNUM++;

			# AfterRowRender
			if (method_exists($this->Theme,"AfterRowRender")) $this->Theme->AfterRowRender();
			if (method_exists($this,"AfterRowRender")) $this->AfterRowRender();
		}

		#AfterRender
		if (method_exists($this->Theme,"AfterRender")) $this->Theme->AfterRender();
		if (method_exists($this,"AfterRender")) $this->AfterRender();

		#AfterData
		if (method_exists($this->Theme,"AfterData")) $this->Theme->AfterData();
		if (method_exists($this,"AfterData")) $this->AfterData();

		$this->Output();
	} else {
		

	    echo $hasSession['message'];
	    die();
	}
	}

	function AcceptPageBreak() {
		$this->BreakPage = TRUE;
		if (method_exists($this->Theme,"BeforePageBreak")) $this->Theme->BeforePageBreak();
		if (method_exists($this,"BeforePageBreak")) $this->BeforePageBreak();
		$this->RENDERPAGEROWNUM=0;
		if (method_exists($this->Theme,"AfterPageBreak")) $this->Theme->AfterPageBreak();
		if (method_exists($this,"AfterPageBreak")) $this->AfterPageBreak();
		return $this->BreakPage;
	}
}

?>