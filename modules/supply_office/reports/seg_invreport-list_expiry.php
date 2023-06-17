<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');
require_once($root_path . 'include/care_api_classes/inventory/class_eodinventory.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

class RepGen_Inventory_list_expiry extends RepGen {
    var $area;
    var $date;
    var $shift_start;
    var $shift_end;
    var $detailed;
    var $noexp;

    function RepGen_Inventory_list_expiry ($area="", $date=FALSE, $noexpiry = FALSE) {
        global $db;
        $this->RepGen("INVENTORY",'P','Letter');
        #$this->ColumnWidth = array(25,60,20,18,18,20,100,21);
        $this->ColumnWidth = array(35,35,46,65,15);
        $this->Alignment = array('L','R','R','R','L');
        $this->PageOrientation = "P";
        $this->Headers = array(
            'DATE/TIME',
            'AREA',
            'EXPIRY DATE',
            'ITEM CODE',
            'NAME',
//            'DESC',
            'QTY',
        );
        if ($date) $this->date=date("Y-m-d",strtotime($date));
        else $this->date=date("Y-m-d");
        
        //added by bryan on 102109 along with 43rd parameter of report
        if ($noexpiry) $this->noexp=$noexpiry; 
        
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
      $this->Cell($total_w,4,'LIST OF EXPIRING MEDICINES/DRUGS',$border2,1,'C');
        
        $this->SetFont('Arial','B',9);
    #$this->Cell(17,5);
        if ($this->area)
          $this->Cell($total_w,4,$this->areaName,$border2,1,'C');
        else
          $this->Cell($total_w,4,"All areas",$border2,1,'C');

      $this->SetFont('Arial','B',9);
    #$this->Cell(17,5);
        if ($this->date) {
          $this->Cell($total_w,4,date("F j, Y",strtotime($this->date)),$border2,1,'C');
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
            
            //$this->Cell($this->ColumnWidth[0],$row,'DATE/TIME',1,0,'C',1);
            $this->Cell($this->ColumnWidth[0],$row,'AREA',1,0,'C',1);
            $this->Cell($this->ColumnWidth[1],$row,'EXPIRY DATE',1,0,'C',1);
            $this->Cell($this->ColumnWidth[2],$row,'ITEM CODE',1,0,'C',1);
            $this->Cell($this->ColumnWidth[3],$row,'NAME',1,0,'C',1);
//            $this->Cell($this->ColumnWidth[4],$row,'DESC',1,0,'C',1);
            $this->Cell($this->ColumnWidth[4],$row,'QTY',1,0,'C',1);
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
            $this->Cell($this->ColumnWidth[4], $this->RowHeight, "", 1, 0, '', 1);
            $this->Cell($this->ColumnWidth[5], $this->RowHeight, "", 1, 0, '', 1);
//            $this->Cell($this->ColumnWidth[6], $this->RowHeight, "", 1, 0, '', 1);
            
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
        $eod_obj = new EODInventory();
        /*
        if(!$this->area){
            
            $sql = "select tbl.item_code,expiry_date,b.bestellnum,b.artikelname,b.description, sum(tbl.t_qty) as total 
                    from 
                    (select item_code, area_code, expiry_date, (select sum(eod_qty) as totalqty from seg_eod_inventory as eod 
                         where eod.item_code = t.item_code and eod.area_code = t.area_code and 
                                         eod.eod_date = (select eod_date from seg_eod_inventory as eod2 
                                             where eod2.item_code = eod.item_code and eod2.area_code = eod.area_code and eod_date < '$this->date' 
                        order by eod_date desc limit 1)) as t_qty 
                    from seg_eod_inventory as t where t.eod_date < '$this->date' and t.expiry_date <= '$this->date' AND t.expiry_date <> '0000-00-00' 
                    group by item_code, area_code) as tbl 
                    JOIN care_pharma_products_main as b ON tbl.item_code=b.bestellnum
                    group by item_code";
        
            
        }
        else{
            $sql = "select t.item_code, t.area_code,c.area_name, t.expiry_date,b.bestellnum,b.artikelname,b.description, (select sum(eod_qty) as totalqty from seg_eod_inventory as eod 
                         where eod.item_code = t.item_code and eod.area_code = t.area_code and 
                                         eod.eod_date = (select eod_date from seg_eod_inventory as eod2 
                                             where eod2.item_code = eod.item_code and eod2.area_code = eod.area_code and eod_date < '$this->date' 
                        order by eod_date desc limit 1)) as t_qty 
                    from seg_eod_inventory as t 
                    JOIN care_pharma_products_main as b ON t.item_code=b.bestellnum
                    JOIN seg_areas as c ON t.area_code=c.area_code ";
                    
            $where = array();
        
            if ($this->date) {
                $where[]="t.expiry_date <= '$this->date' AND t.expiry_date <> '0000-00-00'";
            }
            
            if ($this->area) {
                $where[]="c.area_code='$this->area'";
            }
        
            $where[] = "t.eod_date < '$this->date'";
            
            if ($where)
                $sql .= "WHERE (".implode(") AND (",$where).") group by item_code, area_code";
            else $sql .= "group by item_code, area_code";
            //ORDER BY c.area_name ASC
        }
        */
        
        $sql = "select (select b.area_name from seg_areas as b WHERE b.area_code=a.area_code) as area_name, a.expiry_date, c.bestellnum, c.artikelname, c.description  
                from (seg_eod_inventory as a LEFT JOIN care_pharma_products_main as c ON a.item_code=c.bestellnum) ";
           
        $where = array();
        
        if ($this->date) {
            //$where[]="str_to_date(expiry_date, '%Y-%m-%d') < '$this->date' OR str_to_date(expiry_date, '%Y-%m-%d') = '$this->date' ";
            $where[]="a.expiry_date < '$this->date' OR a.expiry_date = '$this->date' ";                                                                                                                                                                                    
        }
        
        if ($this->area) {
            $where[]="a.area_code='$this->area'";
        }
        
        if($this->noexp!='checked')
            $where[]="a.expiry_date <> '0000-00-00'";
        
        if ($where)
            $sql .= "WHERE (".implode(") AND (",$where).") group by item_code,area_code,expiry_date";
        else $sql .= "group by item_code,area_code,expiry_date";
        die($sql);
        $result=$db->Execute($sql);
        
        $this->_total = 0;
        
        if ($result) {
            $this->Data=array();

            while ($row=$result->FetchRow()) {
                $brycount = 0;
                $brycount = $eod_obj->getCurrentEODQty($row['bestellnum'],$this->area,$this->date,$row['expiry_date']);
                if($brycount < 0) continue; 
                
                $this->Data[]=array(
                    ($this->area!='' ? $row['area_name'] : 'All Areas' ), 
                    $row['expiry_date'],
                    $row['bestellnum'],
                    $row['artikelname'],                    
//                    $row['description'],
                    number_format($brycount,0,'.',',')
                );
                $this->_total+=$row['amount_due'];
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
$rep =& new RepGen_Inventory_list_expiry($_GET['area'],$_GET['exp_date'],$_GET['expcheck']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>