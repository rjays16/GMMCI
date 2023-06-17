<?php
/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

include_once($root_path.'include/care_api_classes/class_department.php');
include_once($root_path.'include/care_api_classes/inventory/class_inventory.php');
include_once($root_path.'include/care_api_classes/inventory/class_issuance.php');
include_once($root_path.'include/care_api_classes/inventory/class_unit.php');
include_once($root_path.'include/care_api_classes/class_pharma_product.php');
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');

require($root_path.'/modules/repgen/themes/dmc/dmc.php');

class RepGen_list_positionmonthly extends DMCRepGen {

    var $area;
    var $item;    
    var $left;
    var $right;
    var $top;
    var $date; 
    
    function RepGen_list_positionmonthly($date='',$item='',$area='') {
        global $db;
        
        $this->DMCRepGen("RADIOLOGY", "L", "Long", $db, TRUE);
        
        //$this->Caption = "Radiology Services";
        
        $this->SetAutoPageBreak(FALSE);
        $this->LEFTMARGIN = 7;
        $this->DEFAULT_TOPMARGIN = 4;
        $this->ColumnWidth = array(10,72,17,15,18,20.5,15,18,20.5,15,18,20.5,15,18,20.5); 
        $this->Columns = 15;
        $this->TotalWidth = array_sum($this->ColumnWidth);        
        $this->RowHeight = 4.5;
        $this->TextHeight = 3.1;
        $this->Alignment = array('R','L','C','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
        $this->PageOrientation = "L";
        //if ($to) $this->to_date=$to;
        
        $this->date = '2009-04-11';
        
        $this->area = '7';
        $this->item = $item;
        
        $this->date=date("Y-m-01",strtotime($this->date));
        //if ($date) $this->date=date("Y-m-01",strtotime($date));
        //$this->date = date("Y-m-d");

        $this->NoWrap = FALSE;
        $this->colored=FALSE;
        if ($this->colored) $this->SetDrawColor(0xDD);
        
    }
    
    function Header() {
        
        $total_w = 0; 

        $this->SetFont("Arial","B","14");
        $this->Ln(6);
        #$this->Cell(17,4);
        $this->Cell($total_w,4,'PHS of MARAMAG',2,1,'C');
        $this->Ln(1);
        $this->SetFont('Arial','',12);
        $this->Cell($total_w,7,'MONTHLY STOCK POSITION REPORT OF MEDICINES',0,0,'C');
        $this->Ln(2); 
        if ($this->date) {
          $this->Cell($total_w,12,'For the month of '.date("F Y",strtotime($this->date)),$border2,1,'C');
        }
        else
          $this->Cell($total_w,14,"All dates",$border2,1,'C');
        $this->Ln(4);
       
        //draw lines
        
        $this->left = 7;
        $this->right = 320;
        
        $this->SetDrawColor(0,0,0);
        $this->Line($this->left,32,$this->right,32);
        $this->Line($this->left,75,$this->right,75);
        $this->Line(7,32,7,75);
        $this->Line(320,32,320,75);
        
        $this->Line(17,32,17,75);
        $this->Line(89,32,89,75);
        $this->Line(106,32,106,75);
        $this->Line(159.5,32,159.5,75); 
        $this->Line(213,32,213,75);
        $this->Line(266.5,32,266.5,75);
        
        $this->Line(106,38,320,38);
        $this->Line(121,38,121,75);
        $this->Line(139,38,139,75);
        $this->Line(174.5,38,174.5,75);
        $this->Line(192.5,38,192.5,75);
        $this->Line(228,38,228,75); 
        $this->Line(246,38,246,75);
        $this->Line(281.5,38,281.5,75);   
        $this->Line(299.5,38,299.5,75);     
         
        //labels
    
        $this->SetFont('Arial','B',9);
        $this->SetXY(106,32);
        $this->Cell(53.5,5,'BEGINNING BALANCE',0,0,'C');
        $this->SetXY(159.5,32);
        $this->Cell(53.5,5,'RECEIPT',0,0,'C');
        $this->SetXY(213,32);
        $this->Cell(53.5,5,'ISSUANCE',0,0,'C');
        $this->SetXY(266.5,32);
        $this->Cell(53.5,5,'ENDING BALANCE',0,0,'C');
        
        $this->SetFont('Arial','',9);
        $this->RotatedText(115,70,'QTY',90);
        $this->RotatedText(130,70,'UNIT COST',90);
        $this->RotatedText(150,70,'TOTAL COST',90);
        
        $this->RotatedText(168.5,70,'QTY',90);
        $this->RotatedText(183.5,70,'UNIT COST',90);
        $this->RotatedText(203.5,70,'TOTAL COST',90);
        
        $this->RotatedText(221,70,'QTY',90);
        $this->RotatedText(236,70,'UNIT COST',90);
        $this->RotatedText(256,70,'TOTAL COST',90);
        
        $this->RotatedText(276.5,70,'QTY',90);
        $this->RotatedText(291.5,70,'UNIT COST',90);
        $this->RotatedText(311.5,70,'TOTAL COST',90);
        
        $this->SetFont('Arial','B',9);
        $this->SetXY(17,32);
        $this->Cell(72,43,'ITEM DESCRIPTION',0,0,'C');
        
        $this->SetFont('Arial','B',10); 
        $this->SetXY(91,48);
        //$this->Cell(17,43,'ITEM DESCRIPTION',0,0,'C');
        $this->MultiCell(13, 3, 'UNIT OF MEASURE', '', 'C','');
        
        $this->top = 50;
        $this->SetXY(7,75);
        
    }
    
    function BeforeData() {
        $this->FONTSIZE = 8.7;
        if ($this->colored) {
            $this->DrawColor = array(0xDD,0xDD,0xDD);
            #$this->DrawColor = array(255,255,255);
        }
    }

    function FetchData() {
        $this->_count = 1;   
        
        $inv_obj = new Inventory();
        $prod_obj = new SegPharmaProduct();
        $unit_obj = new Unit();
        $iss_obj = new Issuance();
        
        $resultItems = $inv_obj->getItemsinArea('7');
        
        if($resultItems){
            while($row = $resultItems->FetchRow()){
                $prodinfo = $prod_obj->getProductInfo($row['item_code']);
                $prodextend = $prod_obj->getExtendedProductInfo($row['item_code']);
                
                $smallunit = $unit_obj->getUnitName($prodextend['pc_unit_id']);
                
                $isscounter = $iss_obj->countAllIssuancesThisMonth($this->date, $row['item_code'], $this->area);
                $delisscounter = $iss_obj->countAllIncomingIssuancesThisMonth($this->date, $row['item_code'], $this->area);
                $delisscounter = $iss_obj->countAllIncomingDeliveriesThisMonth($this->date, $row['item_code'], $this->area);
                
                $balcounter = $inv_obj->getInventoryAtHandbyDate($row['item_code'],$this->area,$this->date);
                
                $endbalcounter = $balcounter + $delisscounter - $isscounter;
                
                $this->Data[]=array(
                    $this->_count,
                    $prodinfo['artikelname'],
                    $smallunit,
                    ($balcounter > 0 ? $balcounter : '' ), 
                    ($balcounter > 0 ? $prodextend['avg_cost'] : '' ), 
                    ($balcounter > 0 ? ($prodextend['avg_cost'] * $balcounter) : '' ), 
                    ($delisscounter > 0 ? $delisscounter : '' ), 
                    ($delisscounter > 0 ? $prodextend['avg_cost'] : '' ), 
                    ($delisscounter > 0 ? ($prodextend['avg_cost'] * $delisscounter) : '' ), 
                    ($isscounter > 0 ? $isscounter : '' ), 
                    ($isscounter > 0 ? $prodextend['avg_cost'] : '' ), 
                    ($isscounter > 0 ? ($prodextend['avg_cost'] * $isscounter) : '' ), 
                    ($endbalcounter > 0 ? $endbalcounter : '' ), 
                    ($endbalcounter > 0 ? $prodextend['avg_cost'] : '' ), 
                    ($endbalcounter > 0 ? ($prodextend['avg_cost'] * $endbalcounter) : '' )
                );
                $this->_total+=$row['amount_due'];  
                $this->_count++;  
            }
        }
        
    }
    
    function AfterData() {
        global $db;
        
        if (!$this->CM) {
            if (!$this->_count) {
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255);
                $this->SetTextColor(0);
                $this->Cell(0, $this->RowHeight, "No meds and med supplies found...", 1, 1, 'L', 1);
            }
            
            /*
            $this->SetFont('Arial','B',9);
            if ($this->colored)    $this->SetFillColor(0xEE);
            $this->SetTextColor(0);
            $this->Cell($this->ColumnWidth[0]+$this->ColumnWidth[1], $this->RowHeight, "TOTAL", 1, 0, 'L', 1);
            $this->Cell($this->ColumnWidth[2]+$this->ColumnWidth[3], $this->RowHeight, strtoupper("Total No. of Items : $this->_count"), 1, 0, 'C', 1);
            //$this->Cell($this->ColumnWidth[3], $this->RowHeight, number_format($this->_total,2), 1, 0, 'R', 1);
            $this->Cell($this->ColumnWidth[4], $this->RowHeight, "", 1, 0, '', 1);
            $this->Cell($this->ColumnWidth[5], $this->RowHeight, "", 1, 0, '', 1);
            $this->Cell($this->ColumnWidth[6], $this->RowHeight, "", 1, 0, '', 1);
            $this->Cell($this->ColumnWidth[7], $this->RowHeight, "", 1, 0, '', 1);
            */
        }
    }
    
    function Footer()    {
        $this->SetY(-18);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
    }

}

$rep = new RepGen_list_positionmonthly($_GET['date'],$_GET['item'],$_GET['area']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>
