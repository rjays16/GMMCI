<?php
/**
* SegHIS - Hospital Information System (BPH Deployment)
* Enhanced by Segworks Technologies Corporation
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

include_once($root_path.'include/care_api_classes/class_area.php');
include_once($root_path.'include/care_api_classes/class_personell.php');
include_once($root_path.'include/care_api_classes/inventory/class_external_request.php');
include_once($root_path.'include/care_api_classes/inventory/class_unit.php');
include_once($root_path.'include/care_api_classes/inventory/class_supplier.php');
include_once($root_path.'include/care_api_classes/class_pharma_product.php');
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');

require($root_path.'/modules/repgen/themes/dmc/dmc.php');

class RepGen_return_slip extends DMCRepGen {

    var $area;
    var $item;    
    var $left;
    var $right;
    var $top;
    var $date;
    var $refno;
    
    function RepGen_return_slip($refno='') {
        global $db;
        
        $this->DMCRepGen("RADIOLOGY", "P", "Letter", $db, TRUE);
        
        $this->SetAutoPageBreak(FALSE);
        $this->LEFTMARGIN = 13;
        $this->DEFAULT_TOPMARGIN = 4;
        $this->ColumnWidth = array(47,20,80,39); 
        $this->Columns = 4;
        $this->TotalWidth = array_sum($this->ColumnWidth);        
        $this->RowHeight = 4.5;
        $this->TextHeight = 3.1;
        $this->Alignment = array('L','C','L','C');
        $this->PageOrientation = "P";
        
        if($date) $this->date = $date;
        else $this->date=date("Y-m-d");
        
        $this->date=date("Y-m-01",strtotime($this->date));
        $this->refno = $refno;
        //$this->refno = '2009000003';

        $this->NoWrap = FALSE;
        $this->colored = TRUE;  
    }
    
    function Header() {
        
        $total_w = 0; 

        $this->SetFont("Arial","B","12");
        $this->Ln(6);
        #$this->Cell(17,4);
        //insert information
        $objInfo = new Hospital_Admin();
    
        if ($row = $objInfo->getAllHospitalInfo()) {      
          $row['hosp_agency'] = strtoupper($row['hosp_agency']);
          $row['hosp_name']   = strtoupper($row['hosp_name']);
        }
        $this->SetFont("Arial","I","9");

        $this->Cell($total_w,4, $row['hosp_country'],$border2,1,'C');

        $this->Cell($total_w,4, $row['hosp_agency'],$border2,1,'C');
        $this->Ln(1);
        $this->SetFont("Arial","B","10");

        $this->Cell($total_w,4, $row['hosp_name'],$border2,1,'C');
        $this->Ln(4);
        $this->SetFont('Arial','B',12);

        $this->MultiCell($total_w, 3,"PROPERTY RETURN SLIP", '', 'C','');
        $this->Ln(1);

        
    }
    
    function BeforeData() {
        $this->FONTSIZE = 8.7;
        if ($this->colored) {
            $this->DrawColor = array(0,0,0);
        }
        $this->SetFont('Arial','B',11);
        $this->left = 13;
        $this->right = 199;
        
        $this->SetDrawColor(0,0,0);

        //labels
        /*
        $this->SetFont('Arial','',10);
        $this->SetXY(13,38);
        $this->Cell(0,5,'_____________________________________________',0,0,'C');
        */
        $this->SetFont('Arial','',10);
        
        $this->SetXY(13,42);
        $this->Cell(0,5,'Warehouse ______________________________ Ret No. ________________________ Date __________________',0,0,'L');
        
        
        //draw the lines
        $this->Line($this->left,52,$this->right,52);
        $this->Line($this->left,60,$this->right,60);
        
        $this->Line(13,52,13,60);
        $this->Line(60,52,60,60);
        $this->Line(80,52,80,60);
        $this->Line(160,52,160,60); 
        //$this->Line(166.5,52,166.5,60);
        $this->Line(199,52,199,60);
        
        //labels
        $this->SetFont('Arial','B',10); 
        $this->SetXY(13,54);
        $this->Cell(47,5,'STOCK NO',0,0,'C');
        $this->SetXY(60,54);
        $this->Cell(20,5,'UNIT',0,0,'C');
        $this->SetXY(80,54);
        $this->Cell(80,5,'ITEM DESCRIPTION',0,0,'C');
        $this->SetXY(160,54);
        $this->Cell(39,5,'QTY',0,0,'C');

        /*
        $this->SetXY(134,54);
        $this->Cell(32.5,5,'UNIT COST',0,0,'C');
        $this->SetXY(166.5,54);
        $this->Cell(32.5,5,'TOTAL COST',0,0,'C');
        */
        $this->top = 50;
        $this->SetXY(13,60);
        $this->SetFont('Arial','',8);
    
    }

    function FetchData() {
        $this->_count = 1;   
        
    }
    
    function AfterData() {
        global $db;  
        
        $req_obj = new SegExternalRequest();
        
       $header = $req_obj->getExternalRequestHeader($this->refno); 
        
        if ($this->_count == 1) {
            $this->SetFont('Arial','B',9);
            $this->SetFillColor(255);
            $this->SetTextColor(0);
            $this->Cell(186, $this->RowHeight, "No details found..", 1, 1, 'L', 1);
        }
            
        $this->SetFont('Arial','B',11); 
        $this->Ln(2);
        $this->Cell(0, $this->RowHeight, "Purpose / Remarks: ", 0, 0, 'L');
        $this->Ln(8);
        $this->SetFont('Arial','',10);
        $this->SetWidths(array(190));
        $this->RowNoBorder(array($header['remarks']));   
        
        $this->Ln(10);
        
        if($this->refno){  
             
            $person_obj = new Personell();  
            
            $rowPerson = $person_obj->get_Person_name($header['requestor_id']);
               
            $this->SetFont('Arial','',10);
            $this->SetWidths(array(1,58,50.5,58,7));
            $this->RowNoBorder(array('','RETURNED BY:                                                                        '.$rowPerson['name_first'].' '.$rowPerson['name_last'].'','','RECEIVED BY:                                                                        ____________________________              Name and Signature',''));
    
        }
        else {    
            $this->SetFont('Arial','',10);
            $this->SetWidths(array(1,58,50.5,58,7));
            $this->RowNoBorder(array('','RETURNED BY:                                                                        ____________________________              Name and Signature','','RECEIVED BY:                                                                        ____________________________              Name and Signature',''));
        }
    }
    
    function Footer()    {
        $this->SetY(-18);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
    }

}

$rep = new RepGen_return_slip($_GET['refno']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>