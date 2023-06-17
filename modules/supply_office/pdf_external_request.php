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

class RepGen_external_request extends DMCRepGen {

    var $area;
    var $item;    
    var $left;
    var $right;
    var $top;
    var $date;
    var $refno;
    
    function RepGen_external_request($refno='') {
        global $db;
        
        $this->DMCRepGen("RADIOLOGY", "P", "Letter", $db, TRUE);
        
        $this->SetAutoPageBreak(FALSE);
        $this->LEFTMARGIN = 13;
        $this->DEFAULT_TOPMARGIN = 4;
        $this->ColumnWidth = array(30.5,18,58,14.5,32.5,32.5); 
        $this->Columns = 6;
        $this->TotalWidth = array_sum($this->ColumnWidth);        
        $this->RowHeight = 4.5;
        $this->TextHeight = 4.5;
        $this->Alignment = array('L','C','L','R','R','R');
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

        $this->MultiCell($total_w, 3,"PURCHASE REQUEST", '', 'C','');
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
        $this->SetFont('Arial','',10);
        $this->SetXY(13,38);
        $this->Cell(0,5,'_____________________________________________',0,0,'C');
        
        $this->SetXY(13,42);
        $this->Cell(0,5,'Agency / Procuring Entity',0,0,'C');
        
        $this->SetXY(13,55);
        $this->Cell(0,5,'Department ______________________________ PR No. ________________________ Date __________________',0,0,'L');
        
        if($this->refno){  
            $req_obj = new SegExternalRequest();
            $header = $req_obj->getExternalRequestHeader($this->refno); 
            $area_obj = new SegArea();
            $supplier_obj =  new SegSupplier(); 
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
            $this->Cell(0,5,$supplier_obj->getSupplierName($header['procurer_nr']),0,0,'C');
    
        }
        
        //draw the lines
        $this->Line($this->left,62,$this->right,62);
        $this->Line($this->left,70,$this->right,70);
        
        $this->Line(13,62,13,70);
        $this->Line(43.5,62,43.5,70);
        $this->Line(61.5,62,61.5,70);
        $this->Line(119.5,62,119.5,70); 
        $this->Line(134,62,134,70);
        $this->Line(166.5,62,166.5,70);
        $this->Line(199,62,199,70);
        array(30.5,18,58,14.5,32.5,32.5); 
        //labels
        $this->SetFont('Arial','B',10); 
        $this->SetXY(13,64);
        $this->Cell(30.5,5,'STOCK NO',0,0,'C');
        $this->SetXY(43.5,64);
        $this->Cell(18,5,'UNIT',0,0,'C');
        $this->SetXY(61.5,64);
        $this->Cell(58,5,'ITEM DESCRIPTION',0,0,'C');
        $this->SetXY(119.5,64);
        $this->Cell(14.5,5,'QTY',0,0,'C');
        $this->SetXY(134,64);
        $this->Cell(32.5,5,'UNIT COST',0,0,'C');
        $this->SetXY(166.5,64);
        $this->Cell(32.5,5,'TOTAL COST',0,0,'C');
        
        $this->top = 50;
        $this->SetXY(13,70);
        $this->SetFont('Arial','',8);
    
    }

    function FetchData() {
        global $db;
        $this->_count = 1;   
        
        $req_obj = new SegExternalRequest();
        $unit_obj = new Unit();
        $prod_obj = new SegPharmaProduct();
        
        $details = $req_obj->getExternalRequestDetails($this->refno);
        
        if($details){
            while($row = $details->FetchRow()){
                if($row['item_code'] != ''){
                    
                    $unitname = $unit_obj->getUnitName($row['unit_id']);
                    $prodinfo = $prod_obj->getProductInfo($row['item_code']);
                    $prodextend = $prod_obj->getExtendedProductInfo($row['item_code']); 
                    
                    if($unit_obj->isUnitIDBigUnit($row['unit_id'])){
                        $smallunitconvertedqty = 0;
                        $smallunitconvertedqty = $row['item_qty'] * $prodextend['qty_per_pack'];
                    }   
                    else{
                        $smallunitconvertedqty = $row['item_qty'];
                    }
                    $reqheader = $req_obj->getExternalRequestHeader($row['issue_refno']);
                    $the_avg_cost = 0; 
                    $fc = "SELECT fn_getavgcost('".$row['item_code']."','".date("Y-m-d")."') as avg_cost";
                    //echo $fc;
                    $rs = $db->Execute($fc);
                    if($rs){
                        $rowavg =  $rs->FetchRow();
                        $the_avg_cost = $rowavg["avg_cost"]; 
                        if($the_avg_cost == NULL) $the_avg_cost = 0;
                    }
                    else $the_avg_cost = 0;  
                 
                    $this->Data[]=array(
                        $row['item_code'],
                        $unitname,
                        $prodinfo['artikelname'],
                        number_format($row['item_qty'],0,'.',','), 
                        number_format($the_avg_cost,2,'.',','), 
                        number_format(($the_avg_cost * $smallunitconvertedqty),2,'.',',')  
                    );
                }
            }
        }  
    }
    
    function AfterData() {
        global $db;  
        
        $req_obj = new SegExternalRequest();
        
       $header = $req_obj->getExternalRequestHeader($this->refno); 
        
        if (!$this->CM) {
            if (!$this->_count) {
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(0);
                $this->SetTextColor(0);
                $this->Cell(0, $this->RowHeight, "No details found..", 1, 1, 'L', 1);
            }
            
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
            $this->RowNoBorder(array('','REQUESTED BY:                                                                                       '.$rowPerson['name_first'].' '.$rowPerson['name_last'].'','','APPROVED BY:                                                                        ____________________________              Name and Signature',''));
    
        }
        else {    
            $this->SetFont('Arial','',10);
            $this->SetWidths(array(1,58,50.5,58,7));
            $this->RowNoBorder(array('','REQUESTED BY:                                                                        ____________________________              Name and Signature','','APPROVED BY:                                                                        ____________________________              Name and Signature',''));
        }
    }
    
    function Footer()    {
        $this->SetY(-18);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
    }

}

$rep = new RepGen_external_request($_GET['refno']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>

