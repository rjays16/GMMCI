<?php
/**
* SegHIS - Hospital Information System 
* Enhanced by Segworks Technologies Corporation
* Transmittal Letter
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');
//require($root_path.'/modules/repgen/repgen.inc.php');

require($root_path.'/modules/repgen/themes/dmc/dmc2.php');

class RegGen_TransmittalLetter extends DMCRepGen {
    var $is_detailed = false; 
    var $transmit_no;
    var $classification;
    
    function RegGen_TransmittalLetter($insurance_name, $bdetailed = false) {
        global $db;
        
        $pg_size = array(215.9, 330.2);
        $this->DMCRepGen($insurance_name, "L", $pg_size, $db, TRUE);
        $this->Caption = "TRANSMITTAL LETTER";  
        $this->is_detailed = $bdetailed;      
        if ($this->is_detailed) {
            $this->ColumnWidth = array(25,35,35,22,22,22,15,15,15,15,15,15,15,15,15,15);            
            $this->Columns = 16;            
            $this->ColumnLabels = array(
                'Policy No.',
                'Name of Patient',
                'Name of Member',
                'Classification',
                'Admitted',
                'Discharged',
                'Rm & Brd',
                'Drugs & Meds',
                'X-Ray/Lab/Others',
                'OR Fee',
                'Total',
                'PF Visit',
                'Surgeon',
                'Anesthesiologist',
                'Total',
                'Grand Total'
            );          
            $this->Alignment = array('C','L','L','C','C','C','R','R','R','R','R','R','R','R','R','R');                        
        }
        else {
            $this->ColumnWidth = array(35,50,50,30,30,30,30,30,30);            
            $this->Columns = 9;            
            $this->ColumnLabels = array(
                'Policy No.',
                'Name of Patient',
                'Name of Member',
                'Classification',
                'Admitted',
                'Discharged',
                'Hospital Charges',
                'Professional Fee',
                'Grand Total'
            );          
            $this->Alignment = array('C','L','L','C','C','C','R','R','R');   
        }                
        
        $this->TotalWidth = array_sum($this->ColumnWidth);        
        
        $this->RowHeight = 6;
        $this->TextHeight = 6;        
        
        $this->PageOrientation = "L";        

        $this->NoWrap = FALSE;        
    }
    
    function TransmittalHeader() {
        $cell_height = 4;
        $indention   = 3;
                
        $this->Cell(0, $cell_height, $insurance_name, 0, 1, 'C');
        $this->Cell(0, $cell_height, "TRANSMITTAL LETTER",0,1,'C');
        $this->Ln(2);
        
        $sTmp = "Transmittal No: ".$this->transmit_no;
        $cwidth = ($this->rMargin - $this->lMargin)/2;
        $this->Cell($cwidth, $cell_height, $sTmp, 0, 0, "L");
        
        $sTmp = "Date: ".strftime("%B %d, %Y");
        $this->Cell($cwidth, $cell_height, $sTmp, 0, 1, "R");
        
        $sTmp = "Classification: ".$this->getClassificationDesc();
        $this->Cell(0, $cell_height, $sTmp, 0, 1, "L");
        
        if ($this->is_detailed)
            $colwidths = array($this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2] + $this->ColumnWidth[3], $this->ColumnWidth[4] + $this->ColumnWidth[5] + $this->ColumnWidth[6] + $this->ColumnWidth[7] + $this->ColumnWidth[8], 
                               $this->ColumnWidth[9] + $this->ColumnWidth[10] + $this->ColumnWidth[11] + $this->ColumnWidth[12] + $this->ColumnWidth[13] + $this->ColumnWidth[14] + $this->ColumnWidth[15]);
        else
            $colwidths = array($this->ColumnWidth[0] + $this->ColumnWidth[1] + $this->ColumnWidth[2], $this->ColumnWidth[3] + $this->ColumnWidth[4],$this->ColumnWidth[5] + $this->ColumnWidth[6] + $this->ColumnWidth[7] + $this->ColumnWidth[8]);         
                        
        $x = $this->GetX();
        $y = $this->GetY();
        
        $this->SetX($x + $indention);
        $this->Cell($colwidths[0], $cell_height, "Hospital Name", 0, 0, "L");
        $this->Rect($x, $y, $colwidths[0], $cell_height + ($cell_height + 2));
        
        $x = $x + $colwidths[0];
        $this->SetX($x + $indention);
        $this->Cell($colwidths[1], $cell_height, "Address", 0, 0, "L");
        $this->Rect($x, $y, $colwidths[1], $cell_height + ($cell_height + 2));
        
        $x = $x + $colwidths[1];
        $this->SetX($x + $indention); 
        $this->Cell($colwidths[2], $cell_height, "Name & Signature of Hospital Representative", 0, 0, "L"); 
        $this->Rect($x, $y, $colwidths[2], $cell_height + ($cell_height + 2));                
        
        $objhosp = new Hospital_Admin();
        $hosp = $objhosp->getAllHospitalInfo();
        
        $this->Cell($colwidths[0], $cell_height+2, $hosp["hosp_name"], 0, 0, "C");
        $this->Cell($colwidths[1], $cell_height+2, $hosp["addr_no_street"].", ".$hosp["brgy_name"].", ".$hosp["mun_name"], 0, 0, "C");
        $this->Cell($colwidths[2]/2, $cell_height+2, $hosp["authrep"], 0, 0, "C");
        $this->Cell($colwidths[2]/2, $cell_height+2, $hosp["designation"], 0, 1, "C");
        
        // Second line of header ...
        if ($this->is_detailed)
            $colwidths = array($this->ColumnWidth[0] + $this->ColumnWidth[1], $this->ColumnWidth[2] + $this->ColumnWidth[3], $this->ColumnWidth[4] + $this->ColumnWidth[5] + $this->ColumnWidth[6] + $this->ColumnWidth[7],  
                               $this->ColumnWidth[8] + $this->ColumnWidth[9] + $this->ColumnWidth[10] + $this->ColumnWidth[11] + $this->ColumnWidth[12], $this->ColumnWidth[13] + $this->ColumnWidth[14] + $this->ColumnWidth[15]);         
        else
            $colwidths = array($this->ColumnWidth[0], $this->ColumnWidth[1], $this->ColumnWidth[2] + $this->ColumnWidth[3], $this->ColumnWidth[4] + $this->ColumnWidth[5] + $this->ColumnWidth[6], $this->ColumnWidth[7] + $this->ColumnWidth[8]);                 
        
        $x = $this->GetX();
        $y = $this->GetY();      
        
        $this->Cell($colwidths[0], $cell_height, "PHIC Accreditation No.", 0, 0, "C");
        $this->Rect($x, $y, $colwidths[0], $cell_height + ($cell_height + 2));
        
        $x = $x + $colwidths[0];
        $this->Cell($colwidths[1], $cell_height, "Hospital Category", 0, 0, "C");
        $this->Rect($x, $y, $colwidths[1], $cell_height + ($cell_height + 2));
        
        $x = $x + $colwidths[1];
        $this->Cell($colwidths[2], $cell_height, "Authorized Bed Capacity", 0, 0, "C");
        $this->Rect($x, $y, $colwidths[2], $cell_height + ($cell_height + 2)); 
        
        $x = $x + $colwidths[2];
        $this->Cell($colwidths[3], $cell_height, "PHIC Accreditation", 0, 0, "C");
        $this->Rect($x, $y, $colwidths[3], $cell_height + ($cell_height + 2));     
        
        $x = $x + $colwidths[3];
        $this->Cell($colwidths[4], $cell_height, "Tax Account No.", 0, 0, "C");
        $this->Rect($x, $y, $colwidths[4], $cell_height + ($cell_height + 2));
                
        $this->Ln();      
        
        $this->Cell($colwidths[0], $cell_height+2, " ", 0, 0, "C");     // PHIC Accreditation No.
        $this->Cell($colwidths[1], $cell_height+2, " ", 0, 0, "C");     // Hospital Category
        $this->Cell($colwidths[2], $cell_height+2, " ", 0, 0, "C");     // Authorized Bed Capacity   
        $this->Cell($colwidths[3], $cell_height+2, " ", 0, 0, "C");     // PHIC Accreditation No.   
        $this->Cell($colwidths[4], $cell_height+2, " ", 0, 1, "C");     // Tax Account No.  
    }
    
    function Header() { 
        $cell_height = 4;
        $indention   = 3;
                
        if ($this->is_detailed) {
            $this->SetX($this->GetX() + $this->ColumnWidth[0] + $this->ColumnWidth[1], $this->ColumnWidth[2] + $this->ColumnWidth[3]);
            $this->Cell($this->ColumnWidth[4] + $this->ColumnWidth[5], $cell_height, "Confinement Period", 1, 0, "C");                                                                             // Confinement Period 
            $this->Cell($this->ColumnWidth[6] + $this->ColumnWidth[7] + $this->ColumnWidth[8] + $this->ColumnWidth[9] + $this->ColumnWidth[10], $cell_height, "Hospital Charges", 1, 0, "C");     // Hospital Charges                        
            $this->Cell($this->ColumnWidth[11] + $this->ColumnWidth[12] + $this->ColumnWidth[13] + $this->ColumnWidth[14], $cell_height, "Hospital Charges", 1, 1, "C");                           // Professional Fee
            
            for ($i = 0; $i < $this->Columns; $i++) {
                $this->Cell($this->ColumnWidth[$i], $cell_height, $this->ColumnLabels[$i], 1, (($i == ($this->Columns-1)) ? 1 : 0), "C");
            }            
        }   
        else {            
            for ($i = 0; $i < $this->Columns; $i++) {
                $this->Cell($this->ColumnWidth[$i], $cell_height, $this->ColumnLabels[$i], 1, (($i == ($this->Columns-1)) ? 1 : 0), "C");
            }            
        }
                
        parent::Header();        
    }    
    
    function getClassificationDesc() {
        $strSQL = "select memcategory_desc from seg_memcategory 
                      where memcategory_id = $this->classification";
        
        $sDesc = '';
        if ($result=$this->Conn->Execute($strSQL)) {
            $this->Conn->SetFetchMode(ADODB_FETCH_ASSOC);
             if ($row = $result->FetchRow()) {
                $sDesc = (is_null($row["memcategory_desc"])) ? "" : $row["memcategory_desc"];                 
             }
        }
        
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
    
    function getPrincipalHolder($s_pid, $nhcareid) {
        global $db;
        
        $sprincipal = "";        
                  
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
    
        return($sprincipal);    
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

        $strSQL = "select cp.pid, h.hcare_id, cpi.insurance_nr, is_principal, memcategory_desc, cp.name_last, cp.name_first, cp.name_middle, date_format((case when admission_dt is null or admission_dt = '' then encounter_date else admission_dt end), '%b %e, %Y %l:%i%p') as date_admission, \n
                         date_format(str_to_date(ce.modify_time, '%Y-%m-%d %H:%i:%s'), '%b %e, %Y %l:%i%p') as date_discharge, acc_coverage, \n
                         med_coverage, xlo_coverage, or_fee, pf_visit, surgeon_coverage, anesth_coverage \n
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
                      where h.transmit_no = '$this->transmit_no' \n
                      order by cp.name_last, cp.name_first, cp.name_middle";
    
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
                    $member = $this->getPrincipalHolder($row["pid"], $row["hcare_id"]);                
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
                        $row["memcategory_desc"],                     
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
                    );
                }
                else {                                        
                    $this->Data[]=array(
                        $row["insurance_nr"],
                        $patient,
                        $member,
                        $row["memcategory_desc"],                     
                        $row["date_admission"],
                        $row["date_discharge"],
                        number_format($total_1, 2, '.',','),
                        number_format($total_2, 2, '.',','),
                        number_format($total_1 + $total_2, 2, '.',','), 
                    );                                                            
                }  
            }            
        }
        else
            echo $this->Conn->ErrorMsg();
    }
}

$rep = new RegGen_TransmittalLetter('Philippine Health Insurance Corporation', ($_GET['detailed'] == '1'));
$rep->transmit_no = $_GET['nr'];
$rep->Open(); 
$rep->TransmittalHeader();
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>