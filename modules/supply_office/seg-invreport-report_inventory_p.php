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
include_once($root_path.'include/care_api_classes/inventory/class_adjustment.php');
include_once($root_path.'include/care_api_classes/inventory/class_unit.php');
include_once($root_path.'include/care_api_classes/class_pharma_product.php');
include_once($root_path.'include/care_api_classes/class_area.php');
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');

require($root_path.'/modules/repgen/themes/dmc/dmc.php');

class RepGen_report_inventory_p extends DMCRepGen {

    var $area;
    var $item;    
    var $left;
    var $right;
    var $top;
    var $start_date;
    var $end_date;
    
    function RepGen_report_inventory_p($startdate='',$enddate='') {
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
        $this->TextHeight = 4.5;
        $this->Alignment = array('R','L','C','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
        $this->PageOrientation = "L";
        //if ($to) $this->to_date=$to;
        
        if($startdate) $this->start_date = $startdate;
        else $this->start_date=date("Y-m-d");
        
        if($enddate) $this->end_date = $enddate;
        else $this->end_date=date("Y-m-d");

        $this->NoWrap = FALSE;
        $this->colored = TRUE;

        
    }
    
    function Header() {
        
        $total_w = 0; 

        $this->SetFont("Arial","B","14");
        $this->Ln(6);
        #$this->Cell(17,4);
        $this->Cell($total_w,4,'',2,1,'C');
        $this->Ln(1);
        $this->SetFont('Arial','',12);
        $this->Cell($total_w,7,'INVENTORY REPORT',0,0,'C');
        $this->Ln(2); 
        if ($this->start_date) {
          $this->Cell($total_w,12,'From '.date("F d, Y",strtotime($this->start_date)).' to '.date("F d, Y",strtotime($this->end_date)),$border2,1,'C');
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
        $this->Cell(53.5,5,'IN',0,0,'C');
        $this->SetXY(213,32);
        $this->Cell(53.5,5,'OUT',0,0,'C');
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
            $this->DrawColor = array(0,0,0);
            #$this->DrawColor = array(255,255,255);
        }
    }

    function FetchData() {
        global $db;
        $this->_count = 1;   
        
        $inv_obj = new Inventory();
        $prod_obj = new SegPharmaProduct();
        $unit_obj = new Unit();
        $iss_obj = new Issuance();
        $adj_obj = new SegAdjustment();
        $area_obj = new SegArea();
        
        $resultItems = $inv_obj->getItemsBetweenDate($start_date, $end_date);
        
        if($resultItems){
            while($row = $resultItems->FetchRow()){
                if($row['item_code'] != ''){
                    $balcounter = 0;
                    $prodinfo = $prod_obj->getProductInfo($row['item_code']);
                    $prodextend = $prod_obj->getExtendedProductInfo($row['item_code']);
                    
                    $smallunit = $unit_obj->getUnitName($prodextend['pc_unit_id']);
                    
                    $incounter = $iss_obj->countAllIncomingDeliveriesInDates($this->start_date, $this->end_date, $row['item_code']);
                    
                    $incounter += $adj_obj->getAdjustmentIn($row['item_code'],$this->start_date,$this->end_date);
                    
                    $outcounter = $adj_obj->getAdjustmentOut($row['item_code'],$this->start_date,$this->end_date);
                    $outcounter = abs($outcounter);
                    
                    $outcounter += $adj_obj->getBillingOut($row['item_code'],$this->start_date,$this->end_date);
                    $outcounter += $adj_obj->getServiceUsageOut($row['item_code'],$this->start_date,$this->end_date);
                    
                    $outcounter += $adj_obj->getPharmaOrderOut($row['item_code'],$this->start_date,$this->end_date);
                    
                    $outcounter += $adj_obj->getEquipmentOrderOut($row['item_code'],$this->start_date,$this->end_date);
                    
                    $resultarea = $area_obj->getAreasContainingItem($row['item_code']);
                    if($resultarea){
                        while($rowarea = $resultarea->FetchRow()){
                            //$balcounter = $inv_obj->getInventoryAtHandbyDate($row['item_code'],$rowarea['area_code'],$this->start_date);
                            $balcounter = $inv_obj->getInventoryAtHandbyDateWithSerial($row['item_code'],$rowarea['area_code'],$this->start_date);
                        }                        
                    }
                    
                    $endbalcounter = $balcounter + $incounter - $outcounter;
                    
                    $this->Data[]=array(
                        $this->_count,
                        $prodinfo['artikelname'],
                        $smallunit,
                        ($balcounter >= 0 ? $balcounter : '' ), 
                        ($balcounter >= 0 ? $prodextend['avg_cost'] : '' ), 
                        ($balcounter >= 0 ? ($prodextend['avg_cost'] * $balcounter) : '' ), 
                        ($incounter > 0 ? $incounter : '' ), 
                        ($incounter > 0 ? $prodextend['avg_cost'] : '' ), 
                        ($incounter > 0 ? ($prodextend['avg_cost'] * $incounter) : '' ), 
                        ($outcounter > 0 ? $outcounter : '' ), 
                        ($outcounter > 0 ? $prodextend['avg_cost'] : '' ), 
                        ($outcounter > 0 ? ($prodextend['avg_cost'] * $outcounter) : '' ), 
                        ($endbalcounter > 0 ? $endbalcounter : '' ), 
                        ($endbalcounter > 0 ? $prodextend['avg_cost'] : '' ), 
                        ($endbalcounter > 0 ? ($prodextend['avg_cost'] * $endbalcounter) : '' )
                    );
                    $this->_total+=$row['amount_due'];  
                    $this->_count++;  
                    
                    /*activated for chron*/
                    #$sql = "INSERT INTO seg_inventory_datatbl (`item_name`,`prod_type`,`start_date`,`end_date`,`unit_name`,`beginning`,`in_qty`,`out_qty`,`ending`) VALUES ('".$prodinfo['artikelname']."','".$prodinfo['prod_class']."','".$this->start_date."','".$this->end_date."','".$smallunit."',".$balcounter.",".$incounter.",".$outcounter.",".$endbalcounter.")";
                    #$result = $db->Execute($sql);
                }
            }
        }  
    }
    
    function AfterData() {

    }
    
    function Footer()    {
        /*
        $this->SetY(-18);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
        */
    }

}

$rep = new RepGen_report_inventory_p($_GET['from_date'],$_GET['to_date']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>

