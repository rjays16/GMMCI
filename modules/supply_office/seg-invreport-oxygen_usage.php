<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

    class RepGen_Inventory_Oxygen_Usage extends RepGen {
    var $area;
    var $from_date;
    var $to_date;
    var $shift_start;
    var $shift_end;
    var $detailed;
    var $serial_num;

    function RepGen_Inventory_Oxygen_Usage ($serial_num=FALSE,$from_date=FALSE, $to_date=FALSE) {
        global $db;
        $this->RepGen("INVENTORY",'P','Letter');
        $this->ColumnWidth = array(29,22,62,14,18,14,18,18);
        $this->Alignment = array('C','C','L','C','C','C','C','C');
        $this->PageOrientation = "P";
        $this->Headers = array(
            'DATE/TIME',
            'OXYGEN TANK NO.',
            'NAME OF PATIENT',
            'Initial',
            'Signature',
            'Final',
            'Signature',
            'Consumed',
        );
        if($from_date) $this->from_date=date("Y-m-d",strtotime($from_date));
        if($to_date) $this->to_date=date("Y-m-d",strtotime($to_date));
        if($serial_num) $this->serial_num=$serial_num;
        $this->area=$area;
        $this->RowHeight = 6;
        $this->TextHeight = 6;
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

    $total_w = 0;
    //ito ang logo
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
      $this->Cell($total_w,4,'OXYGEN UTILIZATION',$border2,1,'C');
        
        $this->SetFont('Arial','B',9);
    #$this->Cell(17,5);

      $this->SetFont('Arial','B',9);
    #$this->Cell(17,5);
        if ($this->from_date) {
            if ($this->to_date)
                $this->Cell($total_w,4,date("F j, Y",strtotime($this->from_date))." - ".date("F j, Y",strtotime($this->to_date)),$border2,1,'C');
            else
                $this->Cell($total_w,4,"From ".date("F j, Y",strtotime($this->from_date)),$border2,1,'C');
        }
        else if($this->to_date)
            $this->Cell($total_w,4,"Until ".date("F j, Y",strtotime($this->to_date)),$border2,1,'C');
        else
          $this->Cell($total_w,4,"All dates",$border2,1,'C');
        $this->Ln(4);

        if (!$this->NoHeader) {
            # Print table header
            
            $this->SetTextColor(0);
            $row=5;            
            
            $this->SetFont('Arial','B',9);
            
            $this->Cell($this->ColumnWidth[0],$row,'DATE/TIME',1,0,'C',1);
            $this->Cell($this->ColumnWidth[1],$row,'SERIAL NO.',1,0,'C',1);
            $this->Cell($this->ColumnWidth[2],$row,'PATIENT NAME',1,0,'C',1);
            $this->Cell($this->ColumnWidth[3],$row,'Initial',1,0,'C',1);
            $this->Cell($this->ColumnWidth[4],$row,'Signature',1,0,'C',1);
            $this->Cell($this->ColumnWidth[5],$row,'Final',1,0,'C',1);
            $this->Cell($this->ColumnWidth[6],$row,'Signature',1,0,'C',1);
            $this->Cell($this->ColumnWidth[7],$row,'Consumed',1,0,'C',1);
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
                $this->Cell(0, $this->RowHeight, "No items found...", 1, 1, 'L', 1);
            }
        }
    }
    
    function Footer()    {
        $this->SetY(-18);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
    }
    
    function FetchData() {        
        global $db;
        
        $sql = "SELECT seo.order_date, si.serial_no, seo.patient_name, seoi.number_of_usage
                FROM seg_equipment_orders AS seo
                LEFT JOIN seg_equipment_order_items AS seoi ON seo.refno = seoi.refno
                LEFT JOIN seg_inventory AS si ON si.serial_no = seoi.serial_no
                WHERE seoi.equipment_id='OT' ";
        
        if ($this->serial_num) {
            $sql.=" AND seoi.serial_no='$this->serial_num'";
        }
        
        if ($this->from_date) {
            $sql.=" AND (seo.order_date>'$this->from_date 23:59:59' OR seo.order_date='$this->from_date 23:59:59')";
        }
        
        if ($this->to_date) {
            $sql.=" AND (seo.order_date<'$this->to_date 23:59:59' OR seo.order_date='$this->to_date 23:59:59')";
        }
    
        $sql .= " ORDER BY serial_no, seo.order_date ASC";
    
        $result=$db->Execute($sql);
        
        $serial = "";
        
        if ($result) {
            $this->Data=array();

            while ($row=$result->FetchRow()) {
                if(empty($serial)){
                    $serial = $row['serial_no'];
                    $sql2 = "SELECT SUM(seoi.number_of_usage) + si.qty AS total
                     FROM seg_equipment_orders AS seo, seg_inventory AS si, seg_equipment_order_items AS seoi 
                     WHERE seo.refno = seoi.refno AND seoi.equipment_id = si.item_code 
                     AND seoi.equipment_id='OT' AND si.serial_no=seoi.serial_no AND seoi.serial_no='".$serial."'";
                    $rs2=$db->Execute($sql2);
                    if($rs2 && $row2=$rs2->FetchRow())
                        $total = $row2["total"];
                    $usage=0;
                }
                elseif($serial != $row['serial_no']){
                    $serial = $row['serial_no'];
                    $sql2 = "SELECT SUM(seoi.number_of_usage) + si.qty AS total
                     FROM seg_equipment_orders AS seo, seg_inventory AS si, seg_equipment_order_items AS seoi 
                     WHERE seo.refno = seoi.refno AND seoi.equipment_id = si.item_code 
                     AND seoi.equipment_id='OT' AND si.serial_no=seoi.serial_no AND seoi.serial_no='".$serial."'";
                    $rs2=$db->Execute($sql2);
                    if($rs2 && $row2=$rs2->FetchRow())
                        $total = $row2["total"];
                    $usage=0;
                }
                else
                    $total = $total - $usage;
               $this->Data[]=array(
                    $row['order_date'],
                    $row['serial_no'],
                    strtoupper($row['patient_name']),
                    $total,
                    "",
                    $total - $row['number_of_usage'],
                    "",
                    $row['number_of_usage']
                );
                $usage=$row['number_of_usage'];
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
$rep =& new RepGen_Inventory_Oxygen_Usage($_GET['serial_no'],$_GET['from_date'],$_GET['to_date']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>