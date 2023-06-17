<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require_once('./roots.php'); //traverse the root directory
//include_once($root_path.'/classes/fpdf/fpdf.php');
require($root_path.'/modules/repgen/repgen.inc.php');  

require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/billing/class_ops.php');

class Monthly_Top_Surgical_Cases extends Repgen {
  var $after_data;
  var $printable_width = 254;
  var $half_width = 127;
  var $date, $month;
  
  function Monthly_Top_Surgical_Cases($month) {
    
    $this->RepGen('Operating Room Monthly Statistical Report', 'L', array('215.9', '279.4'));
    //$this->SetMargins(120.7, 120.7, 120.7);
    $this->DEFAULT_LEFTMARGIN = 12.7;
    $this->DEFAULT_RIGHTMARGIN = 12.7;
    $this->DEFAULT_TOPMARGIN = 12.7;
    $this->ColumnWidth = array(10, 170, 10);
    $this->RowHeight = 5.5;
    $this->Alignment = array('C', 'L', 'C');
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
   $this->Ln(10);
   $this->Cell(0, 6, 'LEADING SURGICAL CASES', 0, 1, 'C');
   if (isset($this->month)) {
      $array = array(1=>'January','February','March','April','May','June','July','August','September',
                  'October','November','December');
      $this->SetFont('Arial', '', 14);
      $this->Cell(0, 6, $array[$this->month]. ' '.date('Y'), 0, 1, 'C');
      
   }   
   $this->Ln(5);
   /** End for title **/
}
  
  function FetchData() {
		global $db;
		$sql = "select distinct od.ops_code, od.rvu, opr.description, (select count(od.ops_code)
		from seg_ops_serv as os left join seg_ops_servdetails as od on os.refno=od.refno
		left join care_encounter_op as co on os.refno=co.refno
		where od.refno=os.refno and co.op_date like '".date('Y-m',strtotime($this->date))."-%' and od.ops_code=opr.code) as `num`
		from seg_ops_serv as os left join seg_ops_servdetails as od on os.refno=od.refno
		left join care_encounter_op as co on os.refno=co.refno
		inner join seg_ops_rvs as opr on od.ops_code=opr.code
		where co.op_date like '".date('Y-m',strtotime($this->date))."-%' order by `num` desc limit 10";
	 #echo $sql;
		$result = $db->Execute($sql);
		$this->Data = array();
		$count=1;
		#echo $result;
		while($row = $result->FetchRow())
		{
		  $this->Data[] = array ($count,$row['description'], $row['num']);
		  $count++;
		}
		$this->SetFont('Arial', '', 12);  
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
     
    $this->Ln(10);
    $this->SetXY(20, 220);
    $this->SetFont('Arial', '', 12);   
    $this->Cell(30, 4, "Prepared by:", "", 0, 'L');
    $this->SetXY(130, 220);
    $this->Cell(30, 4, "Noted by:", "", 0, 'L');
    
    //dapat iquery sa db ang names d2
    $this->SetXY(20, 230);
    $this->SetFont('Arial', 'B', 12);
    $this->Cell(50, 4, "Kromyko Cruzado", "", 0, 'L');
    $this->Line(20, 234, 60, 234);
    $this->SetXY(130, 230);
    $this->Cell(50, 4, "Kromyko Cruzado", "", 0, 'L');
    $this->Line(130, 234, 190, 234); 
    
    $this->SetXY(20, 235);
    $this->SetFont('Arial', '', 12);
    $this->Cell(30, 4, "OR Senior Nurse", "", 0, 'L');
    $this->SetXY(130, 235);
    $this->Cell(30, 4, "OR/PACU Supervising Nurse", "", 0, 'L');  
  }
  
  function Footer() {
   
  }
}

$x = new Monthly_Top_Surgical_Cases($_GET['month']);
$x->AliasNbPages();
$x->FetchData();
$x->Report();

?>