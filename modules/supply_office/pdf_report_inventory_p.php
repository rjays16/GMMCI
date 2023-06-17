<?php
/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

require($root_path.'include/inc_environment_global.php');

require($root_path.'/modules/repgen/repgenclass.php');

class RepGen_report_inventory_p extends RepGen {

    var $area;
    var $item;    
    var $left;
    var $right;
    var $top;
    var $start_date;
    var $end_date;
    var $hospital_ids = array();
    
    function RepGen_report_inventory_p($startdate='',$enddate='',$hospitals='') {
        global $db;
        
        $this->RepGen("RADIOLOGY", "L", "Long", $db, TRUE);
        
        //$this->Caption = "Radiology Services";
        
        $this->SetAutoPageBreak(FALSE);
        $this->LEFTMARGIN = 7;
        $this->DEFAULT_TOPMARGIN = 4;
        $this->ColumnWidth = array(10,72,17,60,38.5,38.5,38.5,38.5); 
        $this->Columns = 8;
        $this->TotalWidth = array_sum($this->ColumnWidth);        
        $this->RowHeight = 4.5;
        $this->TextHeight = 4.5;
        $this->Alignment = array('R','L','C','L','R','R','R','R');
        $this->PageOrientation = "L";
        //if ($to) $this->to_date=$to;
        /*
        if($startdate) $this->start_date = $startdate;
        else $this->start_date=date("Y-m-d");
        
        if($enddate) $this->end_date = $enddate;
        else $this->end_date=date("Y-m-d");
        */
        $this->start_date='2009-06-20';
        $this->end_date='2009-06-31';
        if($hospitals){
            if (!is_array($hospitals)) $hospitals = array($hospitals);
            $this->hospital_ids = $hospitals;
        }
        else{
            $this->hospital_ids = array(2);
        }

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
        $this->Cell($total_w,7,'PEEDMO INVENTORY REPORT',0,0,'C');
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
        $this->Line(166,32,166,75); 
        $this->Line(204.5,32,204.5,75);
        $this->Line(243,32,243,75);
        $this->Line(281.5,32,281.5,75);
        //labels
    
        $this->SetFont('Arial','B',9);
        $this->SetXY(106,51);
        $this->Cell(60,5,'HOSPITAL',0,0,'C');
        $this->SetXY(166,51);
        $this->Cell(38.5,5,'BEGINNING BALANCE',0,0,'C');
        $this->SetXY(204.5,51);
        $this->Cell(38.5,5,'IN',0,0,'C');
        $this->SetXY(243,51);
        $this->Cell(38.5,5,'OUT',0,0,'C');
        $this->SetXY(281.5,51);
        $this->Cell(38.5,5,'ENDING BALANCE',0,0,'C'); 
        
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
        
        $sql = "select hosp_id,item_name, cutoff_date, unit_name, SUM(inqty) as totin, SUM(outqty) as totout FROM seg_consolidated_inventory " ;  
    
        $where = array();
        
        if($this->start_date){
            $where[]="cutoff_date >= '$this->start_date'";
        }
        
        if($this->end_date){
            $where[]="cutoff_date <= '$this->end_date'";
        }
        
        if ($this->hospital_ids) {
            foreach($this->hospital_ids as $i)
                $where[]="hosp_id = $i";
        }
        
        if ($where)
            $sql .= "WHERE (".implode(") AND (",$where).") group by hosp_id,item_name";
        else $sql .= "group by hosp_id,item_name";
        
        $result = $db->Execute($sql);
        if($result){
            while($rowInv=$result->FetchRow()){
                
                $sql2 = "select (SUM(inqty) - SUM(outqty)) as beginning FROM seg_consolidated_inventory
                            WHERE cutoff_date < '$this->start_date' AND hosp_id = ".$rowInv['hosp_id']." AND item_name = '".$rowInv['item_name']."'
                            GROUP BY item_name";
                               
                $result2 = $db->Execute($sql2);
                if($result2)
                    $row2 = $result2->FetchRow();
                
                $ending = ($row2['beginning'] + $rowInv['totin'] - $rowInv['totout']);
                
                $sql3 = "select hosp_name FROM seg_hospital_info
                            WHERE hosp_id = ".$rowInv['hosp_id'];
                               
                $result3 = $db->Execute($sql3);
                if($result3)
                    $row3 = $result3->FetchRow();
                
                $this->Data[]=array(
                    $this->_count,
                    $rowInv['item_name'],
                    $rowInv['unit_name'],
                    $row3['hosp_name'],
                    ($row2['beginning'] ? $row2['beginning'] : '' ), 
                    ($rowInv['totin'] > 0 ? $rowInv['totin'] : '' ),  
                    ($rowInv['totout'] > 0 ? $rowInv['totout'] : '' ), 
                    ($ending > 0 ? $ending : '' )
                );
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

$rep = new RepGen_report_inventory_p($_GET['startdate'],$_GET['enddate']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>
