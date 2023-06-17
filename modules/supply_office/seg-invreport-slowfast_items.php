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

    class RepGen_Inventory_slowfast_items extends RepGen {
    var $area;
    var $from_date;
    var $to_date;
    var $shift_start;
    var $shift_end;
    var $detailed;
    var $from_percent;
    var $to_percent;

    function RepGen_Inventory_slowfast_items ($area="", $from_date=FALSE, $to_date=FALSE, $from_percent=0, $to_percent=100) {
        global $db;
        $this->RepGen("INVENTORY",'P','Letter');
        $this->ColumnWidth = array(40,86,35,35);
        $this->Alignment = array('C','L','R','R','R','L');
        $this->PageOrientation = "P";
        $this->Headers = array(
            'DATE/TIME',
            'AREA',
            'ITEM CODE',
            'NAME',
            'DESC',
            'QTY',
        );
        $this->from_percent = $from_percent;
        $this->to_percent = $to_percent;
        if ($from_date) $this->from_date=date("Y-m-d",strtotime($from_date));
        if ($to_date) $this->to_date=date("Y-m-d",strtotime($to_date));
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
      $this->Cell($total_w,4,'SLOW AND FAST MOVING ITEMS',$border2,1,'C');
        
        $this->SetFont('Arial','B',9);
    #$this->Cell(17,5);
        if ($this->area)
          $this->Cell($total_w,4,$this->areaName,$border2,1,'C');
        else
          $this->Cell($total_w,4,"All areas",$border2,1,'C');

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
            
            $this->SetFont('Arial','B',11);
            $this->Cell(0,$row,'INVENTORY',1,0,'C',1);
            $this->Ln($row);
            $this->SetFont('Arial','B',9);
            
            $this->Cell($this->ColumnWidth[0],$row,'ITEM CODE',1,0,'C',1);
            $this->Cell($this->ColumnWidth[1],$row,'NAME',1,0,'C',1);
            $this->Cell($this->ColumnWidth[2],$row,'TIMES RELEASED',1,0,'C',1);
            $this->Cell($this->ColumnWidth[3],$row,'Percentage',1,0,'C',1);
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
            
            $this->SetFont('Arial','B',9);
            if ($this->colored)    $this->SetFillColor(0xEE);
            $this->SetTextColor(0);
            $this->Cell($this->ColumnWidth[0], $this->RowHeight, "TOTAL", 1, 0, 'L', 1);
            $this->Cell($this->ColumnWidth[1]+$this->ColumnWidth[2], $this->RowHeight, strtoupper("Total No. of Items : $this->_count"), 1, 0, 'C', 1);
            $this->Cell($this->ColumnWidth[3], $this->RowHeight, "", 1, 0, '', 1);
            $this->Cell($this->ColumnWidth[4], $this->RowHeight, "", 1, 0, '', 1);
            $this->Cell($this->ColumnWidth[5], $this->RowHeight, "", 1, 0, '', 1);
        }
    }
    
    function Footer()    {
        $this->SetY(-18);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
    }
    
    function FetchData() {        
        global $db;
        
        $sql = "SELECT c.artikelname, bestellnum, COUNT(item_qty) AS movespeed
                FROM seg_issuance_details as a
                JOIN seg_issuance as b ON a.refno=b.refno
                JOIN care_pharma_products_main as c ON a.item_code=c.bestellnum ";
                        
        $where = array();
        
        if ($this->from_date) {
            $where[]="b.issue_date>='$this->from_date'";
        }
        
        if ($this->to_date) {
            $where[]="b.issue_date<='$this->to_date'";
        }
        
        if ($this->area) {
            $where[]="b.src_area_code='$this->area'";
        }
    
        
        if ($where)
            $sql .= "WHERE (".implode(") AND (",$where).") GROUP BY bestellnum ORDER BY COUNT(bestellnum) DESC";
        else
            $sql .= " GROUP BY bestellnum ORDER BY COUNT(bestellnum) DESC";
    
        $result=$db->Execute($sql);
        
        $sql2 = "SELECT SUM(x.movespeed) as total FROM ($sql) as x";
        $rs2=$db->Execute($sql2);
        $this->_total = 0;
        if($rs2){
            $row=$rs2->FetchRow();
            $this->_total = $row["total"];
        }
        
        if ($result) {
            $this->Data=array();

            while ($row=$result->FetchRow()) {
                $account = array();
                $collection = array();
                $a_types = explode("\n",$row['a_types']);
                foreach ($a_types as $i=>$type) {
                    $type_arr = explode('|',$type);
                    if (count($type_arr) == 1) $type_arr[1] = $type_arr[0];
                    if (!in_array($type_arr[0],$account)) $account[] = $type_arr[0];
                    if (!in_array($type_arr[1],$collection)) $collection[] = $type_arr[1];
                }
               $percent = ($row['movespeed'] / $this->_total)*100;
               if($percent >= $this->from_percent && $percent <= $this->to_percent){
                $this->Data[]=array(
                    $row['bestellnum'],
                    $row['artikelname'],
                    $row['movespeed'],
                    number_format($percent, 2)
                );
               }
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
$rep =& new RepGen_Inventory_slowfast_items($_GET['area'],$_GET['from_date'],$_GET['to_date'],$_GET['from_percent'],$_GET['to_percent']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>