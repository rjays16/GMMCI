<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/inventory/class_eodinventory.php'); 
require_once($root_path.'include/care_api_classes/inventory/class_inventory.php');
//require_once($root_path.'include/care_api_classes/inventory/class_adjustment.php'); 
require_once($root_path.'include/care_api_classes/class_pharma_product.php');
require_once($root_path.'include/care_api_classes/inventory/class_expiry.php');
require_once($root_path.'include/care_api_classes/class_area.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

    class RepGen_Inventory_list_meds extends RepGen {
    var $area;
    var $date;
    var $shift_start;
    var $shift_end;
    var $detailed;
    var $_total = 0;

    function RepGen_Inventory_list_meds ($area="", $date=FALSE) {
        global $db;
        $this->RepGen("INVENTORY",'P','Letter');
        #$this->ColumnWidth = array(25,60,20,18,18,20,100,21);
        $this->ColumnWidth = array(45,45,86,20);
        $this->Alignment = array('L','R','L','L');
        $this->PageOrientation = "P";
        $this->Headers = array(
            'AREA',
            'ITEM CODE',
            'NAME',
            'QTY',
        );
        if ($date) $this->date=date("Y-m-d",strtotime($date));
        else $this->date=date("Y-m-d");
        #else $this->date=date("Y-m-d");
        $this->area=$area;
        $this->RowHeight = 6;
        $this->colored=FALSE;
        if ($this->colored)    $this->SetDrawColor(0xDD);
    }
    
    function Header() {
        global $root_path, $db;
        
        if ($this->area) {
            $sql = "SELECT area_name FROM seg_areas WHERE area_code=".$db->qstr($this->area);
            $this->areaName = $db->GetOne($sql);
        }
    
    $objInfo = new Hospital_Admin();
    
    if ($row = $objInfo->getAllHospitalInfo()) {      
      $row['hosp_agency'] = strtoupper($row['hosp_agency']);
      $row['hosp_name']   = strtoupper($row['hosp_name']);
    }
    else {
      $row['hosp_country'] = "Republic of the Philippines";
      $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
      $row['hosp_name']    = "DAVAO MEDICAL CENTER";
      $row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";      
    }

        #$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',70,8,15);
    /*
        $this->SetFont("Arial","I","8");
        $total_w = 0;
        #$this->Cell(19,3);
      $this->Cell($total_w,3,'Republic of the Philippines',$border2,1,'C');
        #$this->Cell(19,3);
      $this->Cell($total_w,3,'DEPARTMENT OF HEALTH',$border2,1,'C');
      $this->Ln(1);
        $this->SetFont("Arial","B","9");
        #$this->Cell(19,4);
      $this->Cell($total_w,4,'DAVAO MEDICAL CENTER',$border2,1,'C');
        $this->SetFont("Arial","B","8");
        #$this->Cell(19,5);*/
    $total_w = 0;
    //this is responsible for the logo in the left side
    $this->Image('../../gui/img/logos/dmc_logo.jpg',20,10,20,20);
    
    $this->SetFont("Arial","I","9");
    #$this->Cell(17,4);
    $this->Cell($total_w,4, $row['hosp_country'],$border2,1,'C');
    #$this->Cell(17,4);
    $this->Cell($total_w,4, $row['hosp_agency'],$border2,1,'C');
    $this->Ln(2);
    $this->SetFont("Arial","B","10");
    #$this->Cell(17,4);
    $this->Cell($total_w,4, $row['hosp_name'],$border2,1,'C');
    $this->SetFont("Arial","","9");
    #$this->Cell(17,4);
    $this->Cell($total_w,4, $row['hosp_addr1'],$border2,1,'C');
    $this->Ln(4);
    $this->SetFont('Arial','B',12);
    #$this->Cell(17,5);
    
      $this->Cell($total_w,3,'',$border2,1,'C');
        $this->Ln(2);
        
      $this->SetFont('Arial','B',10);
    #$this->Cell(17,5);
      $this->Cell($total_w,4,'INVENTORY LIST OF SUPPLIES',$border2,1,'C');
        
        $this->SetFont('Arial','B',9);
    #$this->Cell(17,5);
        if ($this->area)
          $this->Cell($total_w,4,$this->areaName,$border2,1,'C');
        else
          $this->Cell($total_w,4,"All areas",$border2,1,'C');

      $this->SetFont('Arial','B',9);
    #$this->Cell(17,5);
        if ($this->date) {
          $this->Cell($total_w,4,date("F j, Y","As of ".strtotime($this->date)),$border2,1,'C');
        }
        else
          $this->Cell($total_w,4,"All dates",$border2,1,'C');
        
        $this->Ln(4);

        if (!$this->NoHeader) {
            # Print table header
            
            $this->SetTextColor(0);
            $row=5;            
            
            $this->SetFont('Arial','B',11);
            $this->Cell(0,$row,'INVENTORY',1,0,'C',1);
            $this->Ln($row);
            $this->SetFont('Arial','B',9);
            
            $this->Cell($this->ColumnWidth[0],$row,'AREA',1,0,'C',1);
            $this->Cell($this->ColumnWidth[1],$row,'ITEM CODE',1,0,'C',1);
            $this->Cell($this->ColumnWidth[2],$row,'NAME',1,0,'C',1);
            $this->Cell($this->ColumnWidth[3],$row,'QTY',1,0,'C',1);
            //$this->Cell($this->ColumnWidth[6],$row,'NOTES',1,0,'C',1);

            $this->Ln();
        }
    }
    
    function BeforeCell() {
        $this->FONTSIZE=8;
    }
    
    function BeforeData() {
        if ($this->colored) {
            $this->DrawColor = array(0xDD,0xDD,0xDD);
        }
    }
    
    function BeforeCellRender() {
        $this->FONTSIZE = 10;
        if ($this->colored) {
            if (($this->RENDERPAGEROWNUM%2)>0) 
                $this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
            else
                $this->RENDERCELL->FillColor=array(255,255,255);
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
            
            $this->SetFont('Arial','B',9);
            if ($this->colored)    $this->SetFillColor(0xEE);
            $this->SetTextColor(0);
            $this->Cell($this->ColumnWidth[0], $this->RowHeight, "TOTAL", 1, 0, 'L', 1);
            $this->Cell($this->ColumnWidth[1]+$this->ColumnWidth[2], $this->RowHeight, strtoupper("Total No. of Items : $this->_count"), 1, 0, 'C', 1);
            //$this->Cell($this->ColumnWidth[3], $this->RowHeight, number_format($this->_total,2), 1, 0, 'R', 1);
            $this->Cell($this->ColumnWidth[3], $this->RowHeight, "", 1, 0, '', 1);

            
            /*
            $this->Cell(0,5,'',0,1);
            
            $this->CM = TRUE;
            
            $this->Data = $this->DataCM;
            $this->_total = $this->_totalCM;
            $this->_count = $this->_countCM;

            $this->Report();
            */
        }
    }
    
    function Footer()    {
        $this->SetY(-18);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
    }
    
    function FetchData() {        
        global $db;
        //$adj_obj = new SegAdjustment();
        $eod_obj = new EODInventory();
        $prod_obj = new SegPharmaProduct();
        $expiry_obj = new Expiry();
        $area_obj = new SegArea();
        $inv_obj = new Inventory();  
        
        $sql = "select DISTINCT c.bestellnum from 
                (seg_eod_inventory as a 
                    LEFT JOIN care_pharma_products_main as c ON a.item_code=c.bestellnum
                    ) ";
        
        $where = array();
        
        if ($this->date) {
            //$where[]="str_to_date(expiry_date, '%Y-%m-%d') < '$this->date' OR str_to_date(expiry_date, '%Y-%m-%d') = '$this->date' ";
            $where[]="a.eod_date < '$this->date' OR a.eod_date = '$this->date' ";                                                                                                                                                                                    
        }
        
        if ($this->area) {
            $where[]="a.area_code='$this->area'";
        }
        
        $where[]="c.prod_class = 'S' OR c.prod_class = 'MS'";
        
        if ($where)
            $sql .= "WHERE (".implode(") AND (",$where).") group by item_code,area_code";
        else $sql .= "group by item_code,area_code";
                
        $result=$db->Execute($sql);
        
        $this->_total = 0;
        
        if ($result) {
            $this->Data=array();

            while ($row=$result->FetchRow()) {
                $this->_total = 0;
                $areaname='';
                if($this->area=='')
                {
                    
                    $resultArea = $eod_obj->getAreasContainingItem($row['bestellnum']);
                    
                    while($rowArea = $resultArea->FetchRow()){
                        $resultExpiry = $expiry_obj->getExpiriesofItem($row['bestellnum'],$rowArea['area_code']);
                         while($rowExpiry = $resultExpiry->FetchRow()){
                            $this->_total += $eod_obj->getCurrentEODQty($row['bestellnum'],$rowArea['area_code'],$this->date,$rowExpiry['expiry_date']);  
                         }
                    }
                } 
                else{
                    $areaname = $area_obj->getAreaName($this->area);
                    $this->_total += $inv_obj->getInventoryAtHandbyDateWithSerial($row['bestellnum'],$this->area,$this->date);
                    /*
                    $resultExpiry = $expiry_obj->getExpiriesofItem($row['bestellnum'],$this->area);
                     while($rowExpiry = $resultExpiry->FetchRow()){
                        $this->_total += $eod_obj->getRecentEODQty($row['bestellnum'],$this->area,$this->date,$rowExpiry['expiry_date']);  
                     }
                     */
                }   
                
                $proddetails = $prod_obj->getProductInfo($row['bestellnum']); 
                if($this->_total < 0) continue;  
                $this->Data[]=array(
                    //($this->date!='' ? date("m/d h:ia",strtotime($row['eod_date'])) : '' ),
                    //$row['area_name'],
                    ($this->area!='' ? $areaname : 'All Areas' ),
                    $row['bestellnum'],
                    $proddetails['artikelname'],                    
                    //($this->area=='' ? $row['total'] : $row['t_qty'] ),
                    number_format($this->_total,0,'.',',')
                );
                
            }
            $this->_count = count($this->Data);
            
        }
        else {
            print_r($sql);
            print_r($db->ErrorMsg());
            exit;
            # Error
        }            
    }
}
$rep =& new RepGen_Inventory_list_meds($_GET['area'],$_GET['date']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>