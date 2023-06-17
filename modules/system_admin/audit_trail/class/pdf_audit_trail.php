<?php
/**
* SegHIS - Hospital Information System
* Enhanced by Segworks Technologies Corporation
* Transmittal Letter
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

include_once($root_path."include/care_api_classes/class_hospital_admin.php");
include_once($root_path."include/care_api_classes/class_insurance.php");
require($root_path.'include/inc_environment_global.php');
//require($root_path.'/modules/repgen/repgen.inc.php');

require($root_path.'/modules/repgen/themes/dmc/dmc2.php');

define('DEFAULT_HCAREID', 18);

class RegGen_TransmittalLetter extends DMCRepGen {
        var $is_detailed = FALSE;
        var $transmit_no;
        var $transmit_date;
        var $classification;
        var $ColumnBorders;

        var $frmdte;
        var $todte;

        var $from_date;
        var $to_date;

        var $prev_date;

        var $PgTotals;
        var $GrTotals;
        var $bEndOfReport;

        function RegGen_TransmittalLetter($insurance_name, $bdetailed = false, $bSummaryRep = false) {
                global $db;

                $pg_size = array('215.9', '330.2');
                $this->DMCRepGen($insurance_name, "L", $pg_size, $db, TRUE);
                $this->Caption = "TRANSMITTAL LETTER";
                $this->is_detailed = $bdetailed;
                if ($this->is_detailed) {
                        $this->ColumnWidth = array(22,40,40,23,23,15,15,15,15,15,15,15,15,15,15,15);
                        $this->Columns = 16;
                        $this->ColumnLabels = array(
                                'Philhealth No.',
                                'Name of Patient',
                                'Name of Member',
                                'Admitted',
                                'Discharged',
                                'Rm & Brd',
                                'Drgs/Meds',
                                'X-Ray/L/O',
                                'OR Fee',
                                'Total',
                                'PF Visit',
                                'Surgeon',
                                'Anesth.',
                                'Total',
                                'Gr. Total',
                                'Refund'
                        );
                        $this->ColumnBorders = array(
                                'LBR',
                                'LBR',
                                'LBR',
                                1,
                                1,
                                1,
                                1,
                                1,
                                1,
                                1,
                                1,
                                1,
                                1,
                                1,
                                'LBR',
                                'LBR'
                        );
                        $this->Alignment = array('C','L','L','C','C','R','R','R','R','R','R','R','R','R','R','R');
                        $this->PgTotals  = array(0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
                        $this->GrTotals  = array(0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);
                }
                else {
                    if (!$bSummaryRep) {
                            $this->ColumnWidth = array(30,50,50,32,32,30,30,31,30);
                            $this->Columns = 9;
                            $this->ColumnLabels = array(
                                    'Philhealth No.',
                                    'Name of Patient',
                                    'Name of Member',
                                    'Admitted',
                                    'Discharged',
                                    'Hosp. Charges',
                                    'Prof. Fee',
                                    'Grand Total',
                                    'Patient\'s Refund'
                            );
                            $this->Alignment = array('C','L','L','C','C','R','R','R','R');
                    }
                    else {
                            $this->ColumnWidth = array(19,24,47,47,31,31,29,29,29,29);
                            $this->Columns = 10;
                            $this->ColumnLabels = array(
                                    'Date',
                                    'Philhealth No.',
                                    'Name of Patient',
                                    'Name of Member',
                                    'Admitted',
                                    'Discharged',
                                    'Hosp. Charges',
                                    'Prof. Fee',
                                    'Grand Total',
                                    'Patient\'s Refund'
                            );
                            $this->Alignment = array('C','C','L','L','C','C','R','R','R','R');
                    }
                    $this->PgTotals  = array(0.00, 0.00, 0.00, 0.00);
                    $this->GrTotals  = array(0.00, 0.00, 0.00, 0.00);
                }

                $this->TotalWidth = array_sum($this->ColumnWidth);

                $this->RowHeight = 6;
                $this->TextHeight = 6;

                $this->SetDrawColor(0,0,0);

                $this->PageOrientation = "L";

                $this->NoWrap = FALSE;
        }

        function Footer() {
            if ($this->bEndOfReport)
                $this->PrintGrandTotals();
            else
                $this->PrintPageTotals();
        }

        function AfterData() {
            $this->PrintPageTotals();
            $this->bEndOfReport = true;
        }

        function BeforeData() {
                $cell_height = $this->RowHeight;
                $indention   = 3;

                $this->prev_date = '0000-00-00';

                $this->SetFontSize(14);
                $this->Cell(0, $cell_height, $this->title, 0, 1, 'C');

                if ($this->transmit_no != '') {
                    $this->Cell(0, $cell_height, "TRANSMITTAL LETTER",0,1,'C');
                    $this->Ln(2);
                    $this->SetFontSize($this->DEFAULT_FONTSIZE);

                    $this->SetFont($this->DEFAULT_FONTFAMILY, $this->DEFAULT_FONTSTYLE, 11);

                    $this->SetFontSize(12);
                    $sTmp = "Transmittal No: ".$this->transmit_no;
                    $cwidth = ($this->rMargin - $this->lMargin)/2;
                    $this->Cell($cwidth, $cell_height, $sTmp, 0, 1, "L");

                    $sTmp = "Classification: ".$this->getClassificationDesc();
                    $cwidth = ($this->rMargin - $this->lMargin)/2;
                    $this->Cell($cwidth, $cell_height, $sTmp, 0, 0, "L");

                    $sTmp = "Transmittal Date: ".$this->transmit_date;
                    $this->Cell($cwidth, $cell_height, $sTmp, 0, 1, "R");
                    $this->SetFont($this->DEFAULT_FONTFAMILY, $this->DEFAULT_FONTSTYLE, $this->DEFAULT_FONTSIZE);

                    if ($this->is_detailed)
                            $colwidths = array($this->ColumnWidth[0] + $this->ColumnWidth[1], $this->ColumnWidth[2] + $this->ColumnWidth[3] + $this->ColumnWidth[4] + $this->ColumnWidth[5] + $this->ColumnWidth[6],
                                                                 $this->ColumnWidth[7] + $this->ColumnWidth[8] + $this->ColumnWidth[9] + $this->ColumnWidth[10], $this->ColumnWidth[11] + $this->ColumnWidth[12] + $this->ColumnWidth[13] + $this->ColumnWidth[14] + $this->ColumnWidth[15]);
                    else
                            $colwidths = array($this->ColumnWidth[0] + $this->ColumnWidth[1], $this->ColumnWidth[2] + $this->ColumnWidth[3], $this->ColumnWidth[4] + $this->ColumnWidth[5], $this->ColumnWidth[6] + $this->ColumnWidth[7] + $this->ColumnWidth[8]);

                    $x = $this->GetX();
                    $y = $this->GetY();

                    $this->SetX($x + $indention);
                    $this->Cell($colwidths[0], $cell_height, "Hospital Name", 0, 0, "L");
                    $this->Rect($x, $y, $colwidths[0], $cell_height + ($cell_height + 8));

                    $x = $x + $colwidths[0];
                    $this->SetX($x + $indention);
                    $this->Cell($colwidths[1], $cell_height, "Address", 0, 0, "L");
                    $this->Rect($x, $y, $colwidths[1], $cell_height + ($cell_height + 8));

                    $x = $x + $colwidths[1];
                    $this->SetX($x + $indention);
                    $this->Cell($colwidths[2], $cell_height, "Name & Signature of Hospital Representative", 0, 1, "L");
                    $this->Rect($x, $y, $colwidths[2] + $colwidths[3], $cell_height + ($cell_height + 8));

                    $objhosp = new Hospital_Admin();
                    $hosp = $objhosp->getHospitalInfo();

                    $this->Ln();

    //                $this->SetFontSize(14);
                    $this->SetFont("", "B", 12);        // per request ... font size is changed from 11 to 14 --- by LST -- 06.30.2010

                    $this->SetX($this->lMargin);

                    $length = $this->GetStringWidth(strtoupper($hosp["hosp_name"]));
                    if ($length > $colwidths[0]) {
                        $xpos = $this->GetX();
                        $ypos = $this->GetY();
                        $this->SetXY($xpos, $ypos-4);
                        $this->MultiCell($colwidths[0], $cell_height, strtoupper($hosp["hosp_name"]), 0, "L");
                        $this->SetXY($xpos, $ypos);
                        $this->Cell($colwidths[0], $cell_height+2, "", 0, 0, "C");
                    }
                    else
                        $this->Cell($colwidths[0], $cell_height+2, strtoupper($hosp["hosp_name"]), 0, 0, "C");

                    $length = $this->GetStringWidth(strtoupper(trim($hosp["addr_no_street"]).", ".trim($hosp["brgy_name"]).", ".trim($hosp["mun_name"])));
                    if ($length > $colwidths[1]) {
                        $xpos = $this->GetX();
                        $ypos = $this->GetY();
                        $this->SetXY($xpos, $ypos-4);
                        $this->MultiCell($colwidths[1], $cell_height, strtoupper(trim($hosp["addr_no_street"]).", ".trim($hosp["brgy_name"]).", ".trim($hosp["mun_name"])), 0, "L");
                        $this->SetXY($xpos, $ypos);
                        $this->Cell($colwidths[1], $cell_height+2, "", 0, 0, "C");
                    }
                    else {
                        $this->Cell($colwidths[1], $cell_height+2, strtoupper(trim($hosp["addr_no_street"]).", ".trim($hosp["brgy_name"]).", ".trim($hosp["mun_name"])), 0, 0, "C");
                    }

                    $this->Cell($colwidths[2], $cell_height+2, strtoupper($hosp["authrep"]), 0, 0, "C");
                    $this->Cell($colwidths[3], $cell_height+2, $hosp["designation"], 0, 1, "C");
    //                $this->SetFontSize($this->DEFAULT_FONTSIZE);

                    $this->SetFont($this->DEFAULT_FONTFAMILY, $this->DEFAULT_FONTSTYLE, $this->DEFAULT_FONTSIZE);

                    // Second line of header ...
                    if ($this->is_detailed)
                            $colwidths = array($this->ColumnWidth[0] + $this->ColumnWidth[1], $this->ColumnWidth[2] + $this->ColumnWidth[3], $this->ColumnWidth[4] + $this->ColumnWidth[5] + $this->ColumnWidth[6],
                                                                 $this->ColumnWidth[7] + $this->ColumnWidth[8] + $this->ColumnWidth[9] + $this->ColumnWidth[10] + $this->ColumnWidth[11], $this->ColumnWidth[12] + $this->ColumnWidth[13] + $this->ColumnWidth[14] + $this->ColumnWidth[15]);
                    else
                            $colwidths = array($this->ColumnWidth[0] + ($this->ColumnWidth[1]/2), ($this->ColumnWidth[1]/2) + ($this->ColumnWidth[2]/2), ($this->ColumnWidth[2]/2) + $this->ColumnWidth[3], $this->ColumnWidth[4] + $this->ColumnWidth[5], $this->ColumnWidth[6] + $this->ColumnWidth[7] + $this->ColumnWidth[8]);

                    $this->Ln(1);

                    $x = $this->GetX();
                    $y = $this->GetY()-1;

                    $this->SetY($this->GetY());
                    $this->Cell($colwidths[0], $cell_height, "PHIC Accreditation No.", 0, 0, "C");
                    $this->Rect($x, $y, $colwidths[0], $cell_height + ($cell_height + 4));

                    $x = $x + $colwidths[0];
                    $this->Cell($colwidths[1], $cell_height, "Hospital Category", 0, 0, "C");
                    $this->Rect($x, $y, $colwidths[1], $cell_height + ($cell_height + 4));

                    $x = $x + $colwidths[1];
                    $this->Cell($colwidths[2], $cell_height, "Authorized Bed Capacity", 0, 0, "C");
                    $this->Rect($x, $y, $colwidths[2], $cell_height + ($cell_height + 4));

                    $x = $x + $colwidths[2];
                    $this->Cell($colwidths[3], $cell_height, "PHIC Employer's Number", 0, 0, "C");
                    $this->Rect($x, $y, $colwidths[3], $cell_height + ($cell_height + 4));

                    $x = $x + $colwidths[3];
                    $this->Cell($colwidths[4], $cell_height, "Tax Account No.", 0, 0, "C");
                    $this->Rect($x, $y, $colwidths[4], $cell_height + ($cell_height + 4));

                    $this->Ln();

    //                $this->SetFontSize(10);
                    $this->SetFont("", "B", 12);

                    switch ($hosp["hosp_type"]) {
                        case "TH":
                            $hosptype = "Tertiary";
                            break;

                        case "SH":
                            $hosptype = "Secondary";
                            break;

                        default:
                            $hosptype = "Primary";
                            break;
                    }

                    $insurance = new Insurance();
                    $this->Cell($colwidths[0], $cell_height+2, $insurance->getAccreditationNo(DEFAULT_HCAREID), 0, 0, "C");   // PHIC Accreditation No.
                    $this->Cell($colwidths[1], $cell_height+2, $hosptype, 0, 0, "C");                                                                       // Hospital Category
                    $this->Cell($colwidths[2], $cell_height+2, ($hosp["bed_capacity"] == 0) ? " " : $hosp["bed_capacity"], 0, 0, "C");                                                       // Authorized Bed Capacity
                    $this->Cell($colwidths[3], $cell_height+2, $insurance->getHospitalEmployerNo(DEFAULT_HCAREID), 0, 0, "C");// PHIC Employer's No.
                    $this->Cell($colwidths[4], $cell_height+2, $hosp["tax_acctno"], 0, 1, "C");                                                         // Tax Account No.
    //                $this->SetFontSize($this->DEFAULT_FONTSIZE);
                }
                else {
                    // ... if summary report of transmittals ...
                    $this->Cell(0, $cell_height, "SUMMARY OF AUDIT TRAIL",0,1,'C');
                    $this->Ln(2);
                    $this->SetFontSize($this->DEFAULT_FONTSIZE);

                    $this->SetFont($this->DEFAULT_FONTFAMILY, $this->DEFAULT_FONTSTYLE, 11);

                    $this->SetFontSize(12);
                    $sTmp = "Covered Period: ".$this->from_date." to ".$this->to_date;
                    $cwidth = ($this->rMargin - $this->lMargin)/2;
                    $this->Cell($cwidth, $cell_height, $sTmp, 0, 1, "C");
                    $this->Ln(1);
                }

                $this->SetFont($this->DEFAULT_FONTFAMILY, $this->DEFAULT_FONTSTYLE, $this->DEFAULT_FONTSIZE);

                if ($this->is_detailed) {
                        $this->Ln(1.2);
                        $this->SetX($this->GetX());
                        $this->Cell($this->ColumnWidth[0], $this->RowHeight+1, " ", "TLR", 0, "C");
                        $this->Cell($this->ColumnWidth[1], $this->RowHeight+1, " ", "TLR", 0, "C");
                        $this->Cell($this->ColumnWidth[2], $this->RowHeight+1, " ", "TLR", 0, "C");
                        $this->Cell($this->ColumnWidth[3] + $this->ColumnWidth[4], $this->RowHeight+1, "Confinement Period", 1, 0, "C");                                                                             // Confinement Period
                        $this->Cell($this->ColumnWidth[5] + $this->ColumnWidth[6] + $this->ColumnWidth[7] + $this->ColumnWidth[8] + $this->ColumnWidth[9], $this->RowHeight+1, "Hospital Charges", 1, 0, "C");     // Hospital Charges
                        $this->Cell($this->ColumnWidth[10] + $this->ColumnWidth[11] + $this->ColumnWidth[12] + $this->ColumnWidth[13], $this->RowHeight+1, "Professional Fee", 1, 0, "C");                           // Professional Fee
                        $this->Cell($this->ColumnWidth[14], $this->RowHeight+1, " ", "TLR", 0, "C");
                        $this->Cell($this->ColumnWidth[15], $this->RowHeight+1, "Patient's", "TLR", 1, "C");
                }

                if ($this->is_detailed)
                        $this->SetFontSize($this->DEFAULT_FONTSIZE);
                else {
                        $this->SetFontSize(10);
                        if ($this->transmit_no == '') $this->SetFont($this->DEFAULT_FONTFAMILY, "B", 10);
                }

                if ($this->is_detailed) {
                    if ($this->colored) $this->SetFillColor(255);
                    $this->SetTextColor(0);
                    for ($i=0;$i<$this->Columns;$i++) {
                        $this->Cell($this->ColumnWidth[$i],$this->RowHeight,$this->ColumnLabels[$i],$this->ColumnBorders[$i],0,'C',1);
                    }
                    $this->Ln();
                }
                else
                    parent::Header();

                $this->bEndOfReport = false;
        }

        function Header() {
                if ($this->PageNo() > 1) {
                        if ($this->is_detailed) {
                                $this->SetX($this->GetX());
                                $this->Cell($this->ColumnWidth[0], $this->RowHeight, " ", "TLR", 0, "C");
                                $this->Cell($this->ColumnWidth[1], $this->RowHeight, " ", "TLR", 0, "C");
                                $this->Cell($this->ColumnWidth[2], $this->RowHeight, " ", "TLR", 0, "C");
                                $this->Cell($this->ColumnWidth[3] + $this->ColumnWidth[4], $this->RowHeight, "Confinement Period", 1, 0, "C");                                                                             // Confinement Period
                                $this->Cell($this->ColumnWidth[5] + $this->ColumnWidth[6] + $this->ColumnWidth[7] + $this->ColumnWidth[8] + $this->ColumnWidth[9], $this->RowHeight, "Hospital Charges", 1, 0, "C");     // Hospital Charges
                                $this->Cell($this->ColumnWidth[10] + $this->ColumnWidth[11] + $this->ColumnWidth[12] + $this->ColumnWidth[13], $this->RowHeight, "Professional Fee", 1, 0, "C");                           // Professional Fee
                                $this->Cell($this->ColumnWidth[14], $this->RowHeight, " ", "TLR", 0, "C");
                                $this->Cell($this->ColumnWidth[15], $this->RowHeight, "Patient's", "TLR", 1, "C");

                                $this->SetFontSize($this->DEFAULT_FONTSIZE);
                        }
                        else {
                                $this->SetFontSize(10);
                                if ($this->transmit_no == '') $this->SetFont($this->DEFAULT_FONTFAMILY, "B", 10);
                        }

                        if ($this->is_detailed) {
                            if ($this->colored) $this->SetFillColor(255);
                            $this->SetTextColor(0);
                            for ($i=0;$i<$this->Columns;$i++) {
                                $this->Cell($this->ColumnWidth[$i],$this->RowHeight,$this->ColumnLabels[$i],$this->ColumnBorders[$i],0,'C',1);
                            }
                            $this->Ln();
                        }
                        else
                            parent::Header();
                }
        }

        function getClassificationDesc() {
                if (($this->classification != '') && ($this->classification != '0')) {
                    $strSQL = "select memcategory_desc from seg_memcategory
                                                where memcategory_id = $this->classification";
                    $sDesc = '';
                    if ($result=$this->Conn->Execute($strSQL)) {
                            $this->Conn->SetFetchMode(ADODB_FETCH_ASSOC);
                             if ($row = $result->FetchRow()) {
                                    $sDesc = (is_null($row["memcategory_desc"])) ? "" : $row["memcategory_desc"];
                             }
                    }
                }
                else
                    $sDesc = "ALL MEMBER CLASSIFICATIONS";
                return $sDesc;
        }

        function concatname($slast, $sfirst, $smid) {
                $stmp = "";

                if (!empty($slast)) $stmp .= $slast;
                if (!empty($sfirst)) {
                        if (!empty($stmp)) $stmp .= ", ";
                        $stmp .= $sfirst;
                }
                if (!empty($smid)) {
                        if (!empty($stmp)) $stmp .= " ";
                        $stmp .= $smid;
                }
                return($stmp);
        }

        function getPrincipalHolder($s_pid, $nhcareid, $ins_nr) {
                global $db;

                $sprincipal = "";
                $pinsure_obj = new PersonInsurance($s_pid);
                $row = $pinsure_obj->is_member_info_editable($s_pid, $nhcareid, $ins_nr);
                if (!$row) {
                        $strSQL = "select cp.pid, cp.name_last, cp.name_first, cp.name_middle \n
                                                    from care_person_insurance as cpi0 inner join care_person as cp on cpi0.pid = cp.pid \n
                                                    where exists (select * from care_person_insurance as cpi1 \n
                                                                                     where cpi1.pid = '$s_pid' and cpi1.hcare_id = $nhcareid \n
                                                                                            and cpi1.pid <> cpi0.pid and cpi1.hcare_id = cpi0.hcare_id \n
                                                         and cpi1.insurance_nr = cpi0.insurance_nr) \n
                                                         and cpi0.is_principal <> 0";

                        if ($result = $db->Execute($strSQL)) {
                                if ($result->RecordCount()) {
                                        while ($row = $result->FetchRow())
                                                $sprincipal = $this->concatname((is_null($row['name_last']) ? '' : $row['name_last']),
                                                                                                                (is_null($row['name_first']) ? '' : $row['name_first']),
                                                                                                                (is_null($row['name_middle']) ? '' : $row['name_middle']));
                                }
                        }
                }
                else {
                        $sprincipal = $this->concatname((is_null($row['last_name']) ? '' : $row['last_name']),
                                                                                        (is_null($row['first_name']) ? '' : $row['first_name']),
                                                                                        (is_null($row['middle_name']) ? '' : $row['middle_name']));
                }

                return($sprincipal);
        }

        function AfterRowRender() {
//            if ($this->RENDERCOLNUM == ($this->MAXCOLS-1)) {
                $nlimit = ($this->is_detailed) ? 11 : 4;

                if ($this->transmit_no != '')
                    $indx = 5;
                else
                    $indx = 6;

                for($i=0; $i < $nlimit; $i++) {
                        $this->PgTotals[$i] += str_replace(',', '', $this->Data[$this->RENDERROWNUM-1][$i+$indx]);
                        $this->GrTotals[$i] += str_replace(',', '', $this->Data[$this->RENDERROWNUM-1][$i+$indx]);
            }
//            }
        }

        function BeforeCell() {
            if ($this->COLNUM == 0) {
                if ($this->transmit_no == '') {
                    if (strcmp($this->prev_date, $this->DATA) != 0) {
                        $this->prev_date = $this->DATA;
                        $this->DATA = strftime("%m-%d-%Y", strtotime($this->DATA));
                    }
                    else
                        $this->DATA = " ";
                }
            }
        }

        function BeforeCellRender() {
            if ($this->RENDERCOLNUM == 0) {
                $this->SetXY($this->RENDERROWX - 5, $this->RENDERROWY);
                $this->Cell(4, $this->RowHeight, $this->RENDERROWNUM + 1, 0, 0, "R");
                $this->SetXY($this->RENDERROWX, $this->RENDERROWY);
            }
        }

        function PrintPageTotals() {
                $nlimit = ($this->is_detailed) ? 11 : 4;

                if ($this->transmit_no != '') {
                    $this->SetX($this->GetX() + $this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2]);
                    $this->Cell($this->ColumnWidth[3] + $this->ColumnWidth[4], $this->RowHeight, "PAGE TOTAL", 1, 0, "C");
                    $indx = 5;
                }
                else {
                    $this->SetX($this->GetX() + $this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2] + $this->ColumnWidth[3]);
                    $this->Cell($this->ColumnWidth[4] + $this->ColumnWidth[5], $this->RowHeight, "PAGE TOTAL", 1, 0, "C");
                    $indx = 6;
                }

                for($i=0; $i < $nlimit; $i++) {
                        $this->Cell($this->ColumnWidth[$i+$indx], $this->RowHeight, number_format($this->PgTotals[$i], 2, '.', ','), 1, 0, "R");
                        $this->PgTotals[$i] = 0.00;
                }
                $this->Ln();
        }

        function PrintGrandTotals() {
                $nlimit = ($this->is_detailed) ? 11 : 4;

                if ($this->transmit_no != '') {
                    $this->SetX($this->GetX() + $this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2]);
                    $this->Cell($this->ColumnWidth[3] + $this->ColumnWidth[4], $this->RowHeight, "GRAND TOTAL", 1, 0, "C");
                    $indx = 5;
                }
                else {
                    $this->SetX($this->GetX() + $this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2] + $this->ColumnWidth[3]);
                    $this->Cell($this->ColumnWidth[4] + $this->ColumnWidth[5], $this->RowHeight, "GRAND TOTAL", 1, 0, "C");
                    $indx = 6;
                }
                for($i=0; $i < $nlimit; $i++) {
                        $this->Cell($this->ColumnWidth[$i+$indx], $this->RowHeight, number_format($this->GrTotals[$i], 2, '.', ','), 1, 0, "R");
                }
                $this->Ln();
        }

        function FetchData() {
//        $strSQL = "select cpi.insurance_nr, is_principal, cp.name_last, cp.name_first, cp.name_middle, date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p') as date_admission, \n
//                         date_format(str_to_date(ce.modify_time, '%Y-%m-%d %H:%i:%s'), '%b %e, %Y %l:%i%p') as date_discharge, acc_coverage, \n
//                         med_coverage, xlo_coverage, or_fee, pf_visit, surgeon_coverage, anesth_coverage \n
//                      from (((((seg_transmittal as h inner join seg_transmittal_details as d on h.transmit_no = d.transmit_no) \n
//                         inner join care_encounter as ce on d.encounter_nr = ce.encounter_nr) inner join care_person as cp on ce.pid = cp.pid) \n
//                         inner join care_person_insurance as cpi on cpi.pid = ce.pid and cpi.hcare_id = h.hcare_id) \n
//                         inner join (select encounter_nr, hcare_id, sum(total_acc_coverage) as acc_coverage,sum(total_med_coverage) as med_coverage, sum(total_srv_coverage + total_msc_coverage) as xlo_coverage, \n
//                                        sum(total_ops_coverage) as or_fee, sum(total_d1_coverage + total_d2_coverage) as pf_visit, sum(total_d3_coverage) as surgeon_coverage, sum(total_d4_coverage) as anesth_coverage \n
//                                        from seg_billing_coverage as sbc inner join seg_billing_encounter as sbe on sbc.bill_nr = sbe.bill_nr \n
//                                        group by encounter_nr, hcare_id) as t on \n
//                            t.encounter_nr = d.encounter_nr and t.hcare_id = h.hcare_id) \n
//                         inner join seg_encounter_memcategory as sem on sem.encounter_nr = d.encounter_nr \n
//                      where h.transmit_no = '$this->transmit_no' and memcategory_id = $this->classification \n
//                      order by cp.name_last, cp.name_first, cp.name_middle";

                if ($this->transmit_no != '') {
                    $strSQL = "select cp.pid, h.hcare_id, cpi.insurance_nr, is_principal, memcategory_desc, cp.name_last, cp.name_first, cp.name_middle, date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p') as date_admission, \n
                                                     date_format(str_to_date(ce.mgh_setdte, '%Y-%m-%d %H:%i:%s'), '%b %e, %Y %l:%i%p') as date_discharge, acc_coverage, \n
                                                     med_coverage, xlo_coverage, or_fee, pf_visit, surgeon_coverage, anesth_coverage, patient_claim \n
                                                from (((((seg_transmittal as h inner join seg_transmittal_details as d on h.transmit_no = d.transmit_no) \n
                                                     inner join care_encounter as ce on d.encounter_nr = ce.encounter_nr) inner join care_person as cp on ce.pid = cp.pid) \n
                                                     inner join care_person_insurance as cpi on cpi.pid = ce.pid and cpi.hcare_id = h.hcare_id) \n
                                                     inner join (select encounter_nr, hcare_id, sum(total_acc_coverage) as acc_coverage,sum(total_med_coverage) as med_coverage, sum(total_srv_coverage + total_msc_coverage) as xlo_coverage, \n
                                                                                    sum(total_ops_coverage) as or_fee, sum(total_d1_coverage + total_d2_coverage) as pf_visit, sum(total_d3_coverage) as surgeon_coverage, sum(total_d4_coverage) as anesth_coverage \n
                                                                                    from seg_billing_coverage as sbc inner join seg_billing_encounter as sbe on sbc.bill_nr = sbe.bill_nr \n
                                                                                    group by encounter_nr, hcare_id) as t on \n
                                                            t.encounter_nr = d.encounter_nr and t.hcare_id = h.hcare_id) \n
                                                     left join (seg_encounter_memcategory as sem inner join seg_memcategory as sm on sem.memcategory_id = sm.memcategory_id) \n
                                                            on sem.encounter_nr = d.encounter_nr \n
                                                where h.transmit_no = '$this->transmit_no' ".(($this->classification == '') || ($this->classification == '0') ? '' : "and sem.memcategory_id = $this->classification")." \n
                                                order by cp.name_last, cp.name_first, cp.name_middle";
                }
                else {
                    $strSQL = "select DATE(h.transmit_dte) as transmitdate, cp.pid, h.hcare_id, cpi.insurance_nr, is_principal, memcategory_desc, cp.name_last, cp.name_first, cp.name_middle, date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p') as date_admission, \n
                                                     date_format(str_to_date(ce.mgh_setdte, '%Y-%m-%d %H:%i:%s'), '%b %e, %Y %l:%i%p') as date_discharge, acc_coverage, \n
                                                     med_coverage, xlo_coverage, or_fee, pf_visit, surgeon_coverage, anesth_coverage, patient_claim \n
                                                from (((((seg_transmittal as h inner join seg_transmittal_details as d on h.transmit_no = d.transmit_no) \n
                                                     inner join care_encounter as ce on d.encounter_nr = ce.encounter_nr) inner join care_person as cp on ce.pid = cp.pid) \n
                                                     inner join care_person_insurance as cpi on cpi.pid = ce.pid and cpi.hcare_id = h.hcare_id) \n
                                                     inner join (select encounter_nr, hcare_id, sum(total_acc_coverage) as acc_coverage,sum(total_med_coverage) as med_coverage, sum(total_srv_coverage + total_msc_coverage) as xlo_coverage, \n
                                                                                    sum(total_ops_coverage) as or_fee, sum(total_d1_coverage + total_d2_coverage) as pf_visit, sum(total_d3_coverage) as surgeon_coverage, sum(total_d4_coverage) as anesth_coverage \n
                                                                                    from seg_billing_coverage as sbc inner join seg_billing_encounter as sbe on sbc.bill_nr = sbe.bill_nr \n
                                                                                    group by encounter_nr, hcare_id) as t on \n
                                                            t.encounter_nr = d.encounter_nr and t.hcare_id = h.hcare_id) \n
                                                     left join (seg_encounter_memcategory as sem inner join seg_memcategory as sm on sem.memcategory_id = sm.memcategory_id) \n
                                                            on sem.encounter_nr = d.encounter_nr \n
                                                where DATE(h.transmit_dte) >= DATE('".$this->frmdte."') and DATE(h.transmit_dte) <= DATE('".$this->todte."')
                                                order by DATE(h.transmit_dte), cp.name_last, cp.name_first, cp.name_middle";
                }

                $result=$this->Conn->Execute($strSQL);
                $this->_count = $result->RecordCount();
                $this->Conn->SetFetchMode(ADODB_FETCH_ASSOC);
                if ($result) {
                        $this->Data=array();
                        while ($row=$result->FetchRow()) {
                                $patient = $this->concatname((is_null($row['name_last']) ? '' : $row['name_last']),
                                                                                         (is_null($row['name_first']) ? '' : $row['name_first']),
                                                                                         (is_null($row['name_middle']) ? '' : $row['name_middle']));

                                if ($row["is_principal"] == 0)
                                        $member = $this->getPrincipalHolder($row["pid"], $row["hcare_id"], $row["insurance_nr"]);
                                else
                                        $member = $patient;

                                $total_1 = (is_null($row["acc_coverage"]) ? 0 : $row["acc_coverage"]) +
                                                     (is_null($row["med_coverage"]) ? 0 : $row["med_coverage"]) +
                                                     (is_null($row["xlo_coverage"]) ? 0 : $row["xlo_coverage"]) +
                                                     (is_null($row["or_fee"]) ? 0 : $row["or_fee"]);

                                $total_2 = (is_null($row["pf_visit"]) ? 0 : $row["pf_visit"]) +
                                                     (is_null($row["surgeon_coverage"]) ? 0 : $row["surgeon_coverage"]) +
                                                     (is_null($row["anesth_coverage"]) ? 0 : $row["anesth_coverage"]);

                                if ($this->is_detailed) {
                                        $this->Data[]=array(
                                                $row["insurance_nr"],
                                                $patient,
                                                $member,
                                                $row["date_admission"],
                                                $row["date_discharge"],
                                                number_format($row["acc_coverage"], 2, '.',','),
                                                number_format($row["med_coverage"], 2, '.',','),
                                                number_format($row["xlo_coverage"], 2, '.',','),
                                                number_format($row["or_fee"], 2, '.',','),
                                                number_format($total_1, 2, '.',','),
                                                number_format($row["pf_visit"], 2, '.',','),
                                                number_format($row["surgeon_coverage"], 2, '.',','),
                                                number_format($row["anesth_coverage"], 2, '.',','),
                                                number_format($total_2, 2, '.',','),
                                                number_format($total_1 + $total_2, 2, '.',','),
                                                number_format($row["patient_claim"], 2, '.',',')
                                        );
                                }
                                else {
                                    if ($this->transmit_no != '')
                                        $this->Data[]=array(
                                                $row["insurance_nr"],
                                                strtoupper($patient),                            // made uppercase .... per request by billing .... 06.30.2010 by LST
                                                strtoupper($member),
                                                $row["date_admission"],
                                                $row["date_discharge"],
                                                number_format($total_1, 2, '.',','),
                                                number_format($total_2, 2, '.',','),
                                                number_format($total_1 + $total_2, 2, '.',','),
                                                number_format($row["patient_claim"], 2, '.',',')
                                        );
                                    else
                                        $this->Data[]=array(
                                                $row["transmitdate"],
                                                $row["insurance_nr"],
                                                strtoupper($patient),
                                                strtoupper($member),
                                                $row["date_admission"],
                                                $row["date_discharge"],
                                                number_format($total_1, 2, '.',','),
                                                number_format($total_2, 2, '.',','),
                                                number_format($total_1 + $total_2, 2, '.',','),
                                                number_format($row["patient_claim"], 2, '.',',')
                                        );
                                }
                        }
                }
                else
                        echo $this->Conn->ErrorMsg();
        }
}

if (isset($_GET['nr']) && $_GET['nr']) {
    $rep = new RegGen_TransmittalLetter('Philippine Health Insurance Corporation', ($_GET['detailed'] == '1'));
    $rep->transmit_no = $_GET['nr'];
}
else {
    $rep = new RegGen_TransmittalLetter('Southern Philippines Medical Center', ($_GET['detailed'] == '1'), true);
    $rep->transmit_no = "";
}

if (isset($_GET['class']) && $_GET['class'])
    $rep->classification = $_GET['class'];
else
        $rep->classification = "";

if (isset($_GET['trdte']) && $_GET['trdte'])
    $rep->transmit_date = strftime("%B %d, %Y", $_GET['trdte']);
else
    $rep->transmit_date = strftime("%B %d, %Y");

if (isset($_GET['fromdte']) && $_GET['fromdte']) {
    $rep->from_date = strftime("%B %d, %Y", $_GET['fromdte']);
    $rep->frmdte = strftime("%Y-%m-%d", $_GET['fromdte']);
}
else
    $rep->from_date = strftime("%B %d, %Y");

if (isset($_GET['todte']) && $_GET['todte']) {
    $rep->to_date = strftime("%B %d, %Y", $_GET['todte']);
    $rep->todte = strftime("%Y-%m-%d", $_GET['todte']);
}
else
    $rep->to_date = strftime("%B %d, %Y");

//$rep->TransmittalHeader();
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>
