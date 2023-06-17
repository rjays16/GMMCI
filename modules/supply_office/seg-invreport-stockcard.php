<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/inventory/class_unit.php');
require_once($root_path.'include/care_api_classes/inventory/class_adjustment.php');
require_once($root_path.'include/care_api_classes/inventory/class_eodinventory.php');
require_once($root_path.'include/care_api_classes/class_pharma_product.php');   

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

    class RepGen_Inventory_stockcard extends RepGen {
    var $area;
    var $date;
    var $shift_start;
    var $shift_end;
    var $detailed;
    var $item;
    var $_total;

    function RepGen_Inventory_stockcard ($area="", $date=FALSE, $item="") {
        global $db;
        $this->RepGen("INVENTORY",'P','Letter');
        #$this->ColumnWidth = array(25,60,20,18,18,20,100,21);
        $this->ColumnWidth = array(25,32,32,32,24,27,24);
        $this->Alignment = array('C','L','R','R','R','R','L');
        $this->PageOrientation = "P";
        $this->Headers = array(
            'DATE/TIME',
            'REF',
            'TYPE',
            'FROM/TO',
            'QTY',
            'UNIT',
            'BAL QTY',
        );
        if ($date) $this->date=date("Y-m-d",strtotime($date));
        else $this->date=date("Y-m-d");
        $this->area=$area;
        $this->item = $item;
        $this->RowHeight = 6;
        $this->colored=FALSE;
        if ($this->colored) $this->SetDrawColor(0xDD);
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
    
      $this->Cell($total_w,3,'SUPPLY OFFICE',$border2,1,'C');
        $this->Ln(2);
        
      $this->SetFont('Arial','B',10);
    #$this->Cell(17,5);
      $this->Cell($total_w,4,'STOCK CARD',$border2,1,'C');
        
        $this->SetFont('Arial','B',9);
    #$this->Cell(17,5);
        if ($this->area)
          $this->Cell($total_w,4,$this->areaName,$border2,1,'C');
        else
          $this->Cell($total_w,4,"All areas",$border2,1,'C');

      $this->SetFont('Arial','B',9);
    #$this->Cell(17,5);
        if ($this->date) {
          $this->Cell($total_w,4,"As of ".date("F j, Y",strtotime($this->date)),$border2,1,'C');
        }
        else
          $this->Cell($total_w,4,"As of ".date("F j, Y",strtotime($this->date)),$border2,1,'C');
       //
        if ($this->item)
          $this->Cell($total_w,4,"Stock No. ".$this->item,$border2,1,'L');
        else
          $this->Cell($total_w,4,"Stock No. ",$border2,1,'L');

      $this->SetFont('Arial','B',9);
        $this->Ln(4);

        if (!$this->NoHeader) {
            # Print table header
            
            $this->SetTextColor(0);
            $row=5;            
            
            $this->SetFont('Arial','B',11);
            $this->Cell(0,$row,'INVENTORY',1,0,'C',1);
            $this->Ln($row);
            $this->SetFont('Arial','B',9);

            $this->Cell($this->ColumnWidth[0],$row,'DATE/TIME',1,0,'C',1);
            $this->Cell($this->ColumnWidth[1],$row,'REF',1,0,'C',1);
            $this->Cell($this->ColumnWidth[2],$row,'TYPE',1,0,'C',1);
            $this->Cell($this->ColumnWidth[3],$row,'FROM/TO',1,0,'C',1);
            $this->Cell($this->ColumnWidth[4],$row,'QTY',1,0,'C',1);
            $this->Cell($this->ColumnWidth[5],$row,'UNIT',1,0,'C',1);
            $this->Cell($this->ColumnWidth[6],$row,'BAL QTY',1,0,'C',1); 
            
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
                $this->Cell(0, $this->RowHeight, "No supplies found...", 1, 1, 'L', 1);
            }
            
            $this->SetFont('Arial','B',9);
            if ($this->colored)    $this->SetFillColor(0xEE);
            $this->SetTextColor(0);
            $this->Cell($this->ColumnWidth[0], $this->RowHeight, "TOTAL", 1, 0, 'L', 1);
            $this->Cell($this->ColumnWidth[1]+$this->ColumnWidth[2], $this->RowHeight, strtoupper("Total No. of Adjustments : $this->_count"), 1, 0, 'C', 1);
            //$this->Cell($this->ColumnWidth[3], $this->RowHeight, number_format($this->_total,2), 1, 0, 'R', 1);
            $this->Cell($this->ColumnWidth[3], $this->RowHeight, "", 1, 0, '', 1);
            $this->Cell($this->ColumnWidth[4], $this->RowHeight, "", 1, 0, '', 1);
            $this->Cell($this->ColumnWidth[5], $this->RowHeight, "", 1, 0, '', 1);
            $this->Cell($this->ColumnWidth[6], $this->RowHeight, "", 1, 0, '', 1);
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
        $unit_obj = new Unit();
        $eod_obj = new EODInventory();
        $adj_obj = new SegAdjustment();
        $prod_obj = new SegPharmaProduct();
        
        $sql = "SELECT c.issue_date,a.refno,'Issuance - out' as reason,
                (select b.area_name from seg_areas as b WHERE c.area_code=b.area_code) as area_name,
                a.item_qty,a.unit_id
                FROM (seg_issuance_details as a
                LEFT JOIN seg_issuance as c ON a.refno=c.refno) ";
                
        $where = array();
                    
        if ($this->date) {
            $where[]="str_to_date(c.issue_date, '%Y-%m-%d') < '$this->date' OR str_to_date(c.issue_date, '%Y-%m-%d') = '$this->date' ";
        }
        
        if ($this->area) {
            $where[]="(c.src_area_code='$this->area')";
        }
        
        if($this->item){
            $where[]="(a.item_code='$this->item')";
        }
        
         if ($where)
            $sql .= "WHERE (".implode(") AND (",$where).") ";
        else $sql .= " "; 
        
        $sql .= "UNION 
                (SELECT f.issue_date,d.refno,'Issuance - in',
                (select e.area_name from seg_areas as e WHERE f.src_area_code=e.area_code) as area_name,
                d.item_qty,d.unit_id
                FROM (seg_issuance_details as d
                LEFT JOIN seg_issuance as f ON d.refno=f.refno) ";  
                
        $where2 = array();
                    
        if ($this->date) {
            $where2[]="str_to_date(f.issue_date, '%Y-%m-%d') < '$this->date' OR str_to_date(f.issue_date, '%Y-%m-%d') = '$this->date' ";
        }
        
        if ($this->area) {
            $where2[]="(f.area_code='$this->area')";
        }
        
        if($this->item){
            $where2[]="(d.item_code='$this->item')";
        }
        
         if ($where2)
            $sql .= "WHERE (".implode(") AND (",$where2)."))";
        else $sql .= ") ";      
                
        $sql .= "UNION
                (SELECT i.receipt_date,g.refno,'Delivery',
                (select h.area_name from seg_areas as h WHERE i.area_code=h.area_code) as area_name,
                g.item_qty,g.unit_id
                FROM (seg_delivery_details as g
                LEFT JOIN seg_delivery as i ON g.refno=i.refno) ";
                
        $where3 = array();
                    
        if ($this->date) {
            $where3[]="str_to_date(i.receipt_date, '%Y-%m-%d') < '$this->date' OR str_to_date(i.receipt_date, '%Y-%m-%d') = '$this->date' ";
        }
        
        if ($this->area) {
            $where3[]="(i.area_code='$this->area')";
        }
        
        if($this->item){
            $where3[]="(g.item_code='$this->item')";
        }
        
         if ($where3)
            $sql .= "WHERE (".implode(") AND (",$where3)."))";
        else $sql .= ") ";  
        
        $sql .= "UNION
                (SELECT m.adjust_date,j.refno,
                    (SELECT k.adj_reason_name from seg_inventory_adjustment_reason as k WHERE k.adj_reason_id=j.reason) as reason,   
                (select l.area_name from seg_areas as l WHERE m.area_code=l.area_code) as area_name,
                j.adj_qty,j.unit_id
                FROM (seg_inventory_adjustment_details as j
                LEFT JOIN seg_inventory_adjustment as m ON m.refno=j.refno) ";
                
        $where4 = array();
                    
        if ($this->date) {
            $where4[]="str_to_date(m.adjust_date, '%Y-%m-%d') < '$this->date' OR str_to_date(m.adjust_date, '%Y-%m-%d') = '$this->date' ";
        }
        
        if ($this->area) {
            $where4[]="(m.area_code='$this->area')";
        }
        
        if($this->item){
            $where4[]="(j.item_code='$this->item')";
        }
        
         if ($where4)
            $sql .= "WHERE (".implode(") AND (",$where4).")) ORDER BY issue_date ASC";
        else $sql .= ") ORDER BY issue_date ASC";      
        
        $result=$db->Execute($sql);

                    $this->_total = 0;
                    $counter = 0;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
                    
                    if ($result) {
                        $this->Data=array();

                        while ($row=$result->FetchRow()) {
                            
                            $temp_qty= $row['item_qty'];
                            
                            if($counter==0){
                                 $resultExpiry = $adj_obj->getExpiriesofItem($this->item,$this->area);
                                 while($rowExpiry = $resultExpiry->FetchRow()){
                                    $this->_total += $eod_obj->getRecentEODQty($this->item,$this->area,$this->date,$rowExpiry['expiry_date']);  
                                 }
                            }
                            else{
                                $rowExtendedInfo = $prod_obj->getExtendedProductInfo($item_no);
                                
                                if($unit_obj->isUnitIDBigUnit($row['unit_id'])){
                                    $temp_qty = $temp_qty * $rowExtendedInfo['qty_per_pack'];
                                }
                                
                                if($row['reason'] == 'Issuance - out' || $row['item_qty'] < 0 || $row['reason'] == 'Damage'){
                                    $this->_total = $this->_total - abs($temp_qty);
                                }
                                else{
                                    $this->_total = $this->_total + $temp_qty;    
                                }
                            }
                             
                            $this->Data[]=array(
                                date("m/d/y h:ia",strtotime($row['issue_date'])),
                                $row['refno'],
                                $row['reason'],
                                $row['area_name'],                    
                                abs($row['item_qty']),
                                $unit_obj->getUnitName($row['unit_id']), 
                                $this->_total,
                            );  
                            
                            $counter++;
                        }
                        $this->_count = count($this->Data);
                        
                    }
                    else {
                        print_r($sql);
                        print_r($db->ErrorMsg());
                        exit;
                    } 

    }
}
$rep =& new RepGen_Inventory_stockcard($_GET['area'],$_GET['date'],$_GET['item']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>


