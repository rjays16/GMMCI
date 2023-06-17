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
include_once($root_path.'include/care_api_classes/inventory/class_issuance.php');
include_once($root_path.'include/care_api_classes/class_pharma_product.php');
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');

require($root_path.'/modules/repgen/themes/dmc/dmc.php');

class RepGen_equip_acknowledgment extends DMCRepGen {

    var $area;
    var $item;    
    var $left;
    var $right;
    var $top;
    var $date;
    var $refno;
    
    function RepGen_equip_acknowledgment($refno='') {
        global $db;
        
        $this->DMCRepGen("RADIOLOGY", "P", "Letter", $db, TRUE);
        
        $this->SetAutoPageBreak(FALSE);
        $this->LEFTMARGIN = 13;
        $this->DEFAULT_TOPMARGIN = 4;
        $this->ColumnWidth = array(13,13,80,30,25,25); 
        $this->Columns = 4;
        $this->TotalWidth = array_sum($this->ColumnWidth);        
        $this->RowHeight = 4.5;
        $this->TextHeight = 3.1;
        $this->Alignment = array('L','C','L','L','C','L');
        $this->PageOrientation = "P";
        
        $this->refno = $refno;
        //$this->refno = '2009000001';

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

        $this->MultiCell($total_w, 3,"ACKNOWLEDGEMENT RECEIPT FOR EQUIPMENT", '', 'C','');
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
        
        $this->SetXY(13,42);
        $this->Cell(0,5,'LGU',0,0,'C');
        */
        /*
        $this->SetXY(13,55);
        $this->Cell(0,5,'Department ______________________________ PR No. ________________________ Date __________________',0,0,'L');
        */
        if($this->refno){ 
        /* 
            $req_obj = new SegExternalRequest();
            $header = $req_obj->getExternalRequestHeader($this->refno); 
            $area_obj = new SegArea();
            $person_obj = new Personell();  
               
            $this->SetFont('Arial','B',10);
            $this->SetXY(33,55);
            $this->Cell(57,5,$area_obj->getAreaName($header['area_code']),0,0,'L');
            $this->SetXY(105,55);
            $this->Cell(30,5,$header['refno'],0,0,'L');
            $erqdate=date("Y-m-d",strtotime($header['request_date'])); 
            $this->SetXY(162,55);
            $this->Cell(20,5,$erqdate,0,0,'L');
            
            $this->SetXY(13,38);
            $this->Cell(0,5,'',0,0,'C');
        */
        }
        
        //draw the lines
        $this->Line($this->left,52,$this->right,52);
        $this->Line($this->left,60,$this->right,60);
        
        $this->Line(13,52,13,60);
        $this->Line(26,52,26,60);
        $this->Line(39,52,39,60);
        $this->Line(119,52,119,60); 
        $this->Line(149,52,149,60);
        $this->Line(174,52,174,60);
        $this->Line(199,52,199,60);
         array(13,13,80,30,25,25);
        //labels
        $this->SetFont('Arial','B',10); 
        $this->SetXY(13,54);
        $this->Cell(13,5,'QTY',0,0,'C');
        $this->SetXY(26,54);
        $this->Cell(13,5,'UNIT',0,0,'C');
        $this->SetXY(39,54);
        $this->Cell(80,5,'DESCRIPTION',0,0,'C');
        $this->SetXY(119,54);
        $this->Cell(30,5,'PROPERTY NO',0,0,'C');
        $this->SetXY(149,54);
        $this->Cell(25,5,'EST. LIFE',0,0,'C');
        $this->SetXY(174,54);
        $this->Cell(25,5,'VALUE',0,0,'C');
        
        $this->top = 50;
        $this->SetXY(13,60);
        $this->SetFont('Arial','',8);
    
    }

    function FetchData() {
        $this->_count = 1;   
        global $db;
        
        $iss_obj = new Issuance();
        $unit_obj = new Unit();
        $prod_obj = new SegPharmaProduct();
        
        $result = $iss_obj->getIssuanceDetails($this->refno);
        
        if($result){
            //print_r($result);
            while($row=$result->FetchRow()){
                $prodinfo = $prod_obj->getProductInfo($row['item_code']);
                $prodextendedinfo = $prod_obj->getExtendedProductInfo($row['item_code']);
                $resultdet = $iss_obj->getCustodianDetails($row['refno'],$row['item_code'],$row['expiry_date'],$row['serial_no']);
                //echo $iss_obj->sql;
                if($rowdet = $resultdet->FetchRow()){
                    $this->Data[]=array(
                        $row['item_qty'],
                        $unit_obj->getUnitName($row['unit_id']),
                        $prodinfo['artikelname'],
                        $rowdet['property_no'],
                        $rowdet['estimated_life'],
                        $prodextendedinfo['avg_cost']*$row['item_qty']
                    );    
                }
                
                $this->_count++;
            }
        }
        
    }
    
    function AfterData() {
        global $db;  
        
        $iss_obj = new Issuance();
        
       $header = $iss_obj->getIssuanceHeader($this->refno); 
        
        
        if ($this->_count == 1) {
            $this->SetFont('Arial','B',9);
            $this->SetFillColor(255);
            $this->SetTextColor(0);
            $this->Cell(186, $this->RowHeight, "No details found..", 1, 1, 'L', 1);
        }
            
        $this->SetFont('Arial','B',11); 
        $this->Ln(2);
        
        $this->Ln(10);
        
        if($this->refno){  
             
            $person_obj = new Personell();  
            
            $rowPerson = $person_obj->get_Person_name($header['issuing_id']);
            $rowPersonTo = $person_obj->get_Person_name($header['acknowledging_id']);
               
            $this->SetFont('Arial','',10);
            $this->SetWidths(array(1,58,50.5,58,7));
            $this->RowNoBorder(array('','RECEIVED FROM:                                                                        '.$rowPerson['name_first'].' '.$rowPerson['name_last'].'','','RECEIVED BY:                                                                               '.$rowPersonTo['name_first'].' '.$rowPersonTo['name_last'].'',''));
    
        }
        else {    
            $this->SetFont('Arial','',10);
            $this->SetWidths(array(1,58,50.5,58,7));
            $this->RowNoBorder(array('','RECEIVED FROM:                                                                        ____________________________              Name and Signature                                                                     ____________________________                       Date','','RECEIVED BY:                                                                        ____________________________              Name and Signature                                                                     ____________________________                       Date',''));
        }
    }
    
    function Footer()    {
        $this->SetY(-18);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
    }

}

$rep = new RepGen_equip_acknowledgment($_GET['refno']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>

