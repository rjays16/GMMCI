<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require_once('./roots.php'); //traverse the root directory
//include_once($root_path.'/classes/fpdf/fpdf.php');
require($root_path.'/modules/repgen/repgen.inc.php');  

require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/billing/class_ops.php');

class Monthly_Medicare_Classification_Report extends Repgen {
  var $after_data;
  var $printable_width = 254;
  var $half_width = 127;
  var $date, $month;
  var $total_member, $total_dependent, $grand_total;
  
  function Monthly_Medicare_Classification_Report($month) {
    
    $this->RepGen('Operating Room MEDICARE CLASSIFICATION REPORT', 'L', array('215.9', '279.4'));
    //$this->SetMargins(120.7, 120.7, 120.7);
    $this->DEFAULT_LEFTMARGIN = 12.7;
    $this->DEFAULT_RIGHTMARGIN = 12.7;
    $this->DEFAULT_TOPMARGIN = 12.7;
    $this->ColumnWidth = array(10, 90, 30, 30, 30);
    $this->RowHeight = 5.5;
    $this->Alignment = array('C', 'L', 'C', 'C', 'R');
    $this->PageOrientation = "P";
    $this->NoWrap = false;
    if (isset($month))
    {
      $this->date = date('Y')."-".$month;
      $this->date = date('Y-m', strtotime($this->date));
      $this->month = $month;
		}
		else
		{
		  $this->date = date('Y-m');
		}
		#echo $this->date;
  }
  
  function Header() {
   global $root_path;
   /** Print Header **/
   $hospital = new Hospital_Admin();
   $hospital_info = $hospital->getAllHospitalInfo();
   $hospital_info_array = array($hospital_info['hosp_country'], strtoupper($hospital_info['hosp_agency']), 
                                strtoupper($hospital_info['hosp_name']), $hospital_info['hosp_addr1']);
   $this->SetFont("Times", "B", "10");
   $this->MultiCell(0, 5, implode("\n", $hospital_info_array), 0, 'C'); 
   $this->Image($root_path.'gui/img/logos/dmc_logo.jpg',30,5,30,30);
   $this->SetY($this->GetY()+7.08); 
   /** End: Print Header **/

   /** For title **/
   $this->SetFont('Arial', 'B', 16);
   $this->Cell(0, 6, 'OPERATING ROOM', 0, 1, 'C');
   $this->SetFont('Arial', '', 14);
   $this->Cell(0, 6, 'BPH - Malaybalay', 0, 1, 'C');
   $this->Ln(5);
   $this->Cell(0, 6, 'MEDICARE CLASSIFICATION REPORT', 0, 1, 'C');
   if (isset($this->month)) {
      $array = array(1=>'January','February','March','April','May','June','July','August','September',
                  'October','November','December');
      $this->SetFont('Arial', '', 14);
      $this->Cell(0, 6, $array[$this->month]. ' '.date('Y'), 0, 1, 'C');
      
   }   
   $this->Ln(5);
   /** End for title **/
   
   //header for columns
   $this->SetFont('Arial', '', 9);
   $this->Cell(10, 4, "", 1, 0, 'C', 1);
   $this->Cell(90, 4, "", 1, 0, 'C', 1);
   $this->Cell(30, 4, "PIHP/PHIP", 1, 0, 'C', 1);
   $this->Cell(30, 4, "PIHP/PHIP", 1, 0, 'C', 1);
   $this->Cell(30, 4, "", 1, 1, 'C', 1);
   $this->Cell(10, 4, "", 1, 0, 'C', 1);
   $this->SetFont('Arial', 'B', 9);
   $this->Cell(90, 4, "OPERATION PROCEDURES", 1, 0, 'C', 1);
   $this->SetFont('Arial', '', 9);
   $this->Cell(30, 4, "MEMBER", 1, 0, 'C', 1);
   $this->Cell(30, 4, "DEPENDENT", 1, 0, 'C', 1);
   $this->SetFont('Arial', 'B', 9);
   $this->Cell(30, 4, "TOTAL", 1, 1, 'C', 1);

}
  
  function FetchData() {
		global $db; 
		$sql1 = "select distinct od.ops_code,opr.description
		from seg_ops_serv as os inner join care_encounter_op as co on os.refno=co.refno
		left join seg_ops_servdetails as od on os.refno=od.refno
		left join seg_ops_rvs as opr on od.ops_code=opr.code
		where co.op_date like '".date('Y-m',strtotime($this->date))."-%' and od.ops_code!='' order by opr.description";
		#echo $sql1;
		$result1 = $db->Execute($sql1);
		$this->Data = array();
		$count=1;
		while($row1 = $result1->FetchRow())
		{
		  $sql2 = "select
			sum(case when os.pid=sd.parent_pid and sd.status='member' then 1 else 0 end) as member,
			sum(case when os.pid=sd.dependent_pid and sd.status!='member' then 1 else 0 end) as dependent
			from seg_ops_serv as os join care_encounter as ce on os.pid=ce.pid
			inner join seg_dependents as sd on os.pid=sd.parent_pid
			inner join care_encounter_op as co on os.refno=co.refno
			left join seg_ops_servdetails as od on os.refno=od.refno
			left join seg_ops_rvs as opr on od.ops_code=opr.code
			where od.ops_code=".$db->qstr($row1['ops_code']);
		  $result2 = $db->Execute($sql2);
		  $row2 = $result2->FetchRow();
		  $tot_mem = $row2['member'];
		  $tot_dep = $row2['dependent'];
		  $tot_mem_dep = $tot_mem + $tot_dep;
		  $this->Data[] = array ($count,$row1['description'],$row2['member'],$row2['dependent'],$tot_mem_dep);
		  $this->total_member+=$tot_mem;
		  $this->total_dependent+=$tot_dep;
		  $count++;
		}
		$this->grand_total=$this->total_member+$this->total_dependent;
		$this->SetFont('Arial', '', 9);  
		$this->_count = count($this->Data);
  }
  
  function AfterData() {
  if (!$this->_count) {
      $this->SetFont('Arial','B',9);
      $this->SetFillColor(255);
      $this->SetTextColor(0);
      $this->Cell(0, $this->RowHeight, "No operations were conducted this month.", 1, 1, 'L', 1);
    }
     # $this->Cell(0, 5, 'No operations were conducted this month.', 0, 1, 'L');
    else
    { 
  	 //print total
  	 $this->Cell(10, 4, "", 1, 0, 'C', 1);
  	 $this->SetFont('Arial', 'I', 9);
   	 $this->Cell(90, 4, "TOTAL", 1, 0, 'C', 1);
   	 $this->Cell(30, 4, $this->total_member, 1, 0, 'C', 1);
   	 $this->Cell(30, 4, $this->total_dependent, 1, 0, 'C', 1);
   	 $this->SetFont('Arial', 'B', 9);
   	 $this->Cell(30, 4, $this->grand_total, 1, 1, 'R', 1);
		}
   
    $this->Ln(10);
    $this->SetXY(20, 220);
    $this->SetFont('Arial', '', 12);   
    $this->Cell(30, 4, "Prepared by:", "", 0, 'L');
    $this->SetXY(130, 220);
    $this->Cell(30, 4, "Noted by:", "", 0, 'L');
    
    //dapat iquery sa db ang names d2
    $this->SetXY(20, 230);
    $this->SetFont('Arial', 'BU', 12);
    $this->Cell(50, 4, "Kromyko Cruzado", "", 0, 'L');
    $this->SetXY(130, 230);
    $this->Cell(50, 4, "Kromyko Cruzado", "", 0, 'L');
    
    $this->SetXY(20, 235);
    $this->SetFont('Arial', '', 12);
    $this->Cell(30, 4, "OR Senior Nurse", "", 0, 'L');
    $this->SetXY(130, 235);
    $this->Cell(30, 4, "OR/PACU Supervising Nurse", "", 0, 'L');  
  }
  
  function Footer() {
   
  }
}

$x = new Monthly_Medicare_Classification_Report($_GET['month']);
$x->AliasNbPages();
$x->FetchData();
$x->Report();

?>