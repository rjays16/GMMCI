<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require_once('./roots.php'); //traverse the root directory
//include_once($root_path.'/classes/fpdf/fpdf.php');
require($root_path.'/modules/repgen/repgen.inc.php');  

require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/billing/class_ops.php');

class Monthly_Statistical extends Repgen {
  var $after_data;
  var $printable_width = 254;
  var $half_width = 127;
  var $tot_major, $tot_minor;
  
  function Monthly_Statistical() {
    
    $this->RepGen('Operating Room Monthly Statistical Report', 'L', array('215.9', '279.4'));
    //$this->SetMargins(120.7, 120.7, 120.7);
    $this->DEFAULT_LEFTMARGIN = 12.7;
    $this->DEFAULT_RIGHTMARGIN = 12.7;
    $this->DEFAULT_TOPMARGIN = 12.7;
    $this->ColumnWidth = array(40,15.5,15.5,15.5,15.5,11.25,11.25,11.25,11.25,11.25,11.25,11.25,11.25,15.5,15.5,31);
    $this->RowHeight = 5.5;
    $this->Alignment = array('L','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C');
    $this->PageOrientation = "L";
    $this->NoWrap = false;
    #echo $_GET['report_date'];
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
   $this->Image($root_path.'gui/img/logos/dmc_logo.jpg',70,5,30,30);
   #$this->Line(0, $this->GetY()+2.54, $this->printable_width, $this->GetY()+2.54);
   $this->SetY($this->GetY()+7.08); 
   /** End: Print Header **/

   /** For title **/
   $this->SetFont('Arial', 'BU', 16);
   $this->Cell(0, 6, 'Operating Room Monthly Statistical Report', 0, 1, 'C');
   if (isset($_GET['report_date'])) {
      $array = array(1=>'January','February','March','April','May','June','July','August','September',
                  'October','November','December');
      $this->SetFont('Arial', '', 14);
      $this->Cell(0, 6, 'For the Month of '.$array[$_GET['report_date']]. ' '.date('Y'), 0, 1, 'C');
      
   }
   
   $this->Ln(8);
   /** End for title **/

  

  $row=14;
  $this->SetFont('Arial', 'B', 8);
  $this->Cell(40,6,'','TL',0,'C',1);
  $this->Cell(31,6,'Pay',1,0,'C',1);
  $this->Cell(31,6,'Charity',1,0,'C',1);
  $this->Cell(45,6,'PHIC',1,0,'C',1);
  $this->Cell(45,6,'Indigency',1,0,'C',1);
  $this->Cell(31,6,'Total',1,0,'C',1);
  $this->Cell(31,6,'',1,1,'C',1);
  
  $this->Cell(40,4,'Type Of Operation','L',0,'C',1);
  $this->Cell(31,4,'','L',0,'C',1);
  $this->Cell(31,4,'','L',0,'C',1);
  $this->Cell(22.5,4,'Member',1,0,'C',1);
  $this->Cell(22.5,4,'Dependent',1,0,'C',1);
  $this->Cell(22.5,4,'Member',1,0,'C',1);
  $this->Cell(22.5,4,'Dependent',1,0,'C',1);
  $this->Cell(31,4,'','LRB',0,'C',1);
  $this->Cell(31,4,'Total','LRB',1,'C',1);
  
  $this->Cell(40,4,'','LB',0,'C',1);
  $this->Cell(15.5,4,'M',1,0,'C',1);
  $this->Cell(15.5,4,'F',1,0,'C',1);
  $this->Cell(15.5,4,'M',1,0,'C',1);
  $this->Cell(15.5,4,'F',1,0,'C',1);
  $this->Cell(11.25,4,'M',1,0,'C',1);
  $this->Cell(11.25,4,'F',1,0,'C',1);
  $this->Cell(11.25,4,'M',1,0,'C',1);
  $this->Cell(11.25,4,'F',1,0,'C',1);
  $this->Cell(11.25,4,'M',1,0,'C',1);
  $this->Cell(11.25,4,'F',1,0,'C',1);
  $this->Cell(11.25,4,'M',1,0,'C',1);
  $this->Cell(11.25,4,'F',1,0,'C',1);
  $this->Cell(15.5,4,'M',1,0,'C',1);
  $this->Cell(15.5,4,'F',1,0,'C',1);
  $this->Cell(31,4,'','LRB',1,'C',1);
  
  

  
  
  #$this->Ln();
}
  
  function FetchData() {
  	#echo $_GET['report_date'];
  	global $db;
    if (isset($_GET['report_date']))
    {
      $date = date('Y')."-".$_GET['report_date'];
      $date = date('Y-m', strtotime($date));
      $tot_male=0;
      $tot_female=0;
      $grand_total=0;
      $this->Data = array();                                                                    
    
      //query for 15 and above years old with major procedure
      $sql1 = "select 
			sum(case when cp.sex='m' and sa.accomodation_name='payward' and cp.age>=15 and od.rvu>=150 then 1 else 0 end) as tot_pay_m_15a,
			sum(case when cp.sex='f' and sa.accomodation_name='payward' and cp.age>=15 and od.rvu>=150 then 1 else 0 end) as tot_pay_f_15a,
			sum(case when cp.sex='m' and sa.accomodation_name='charity' and cp.age>=15 and od.rvu>=150 then 1 else 0 end) as tot_charity_m_15a,
			sum(case when cp.sex='f' and sa.accomodation_name='charity' and cp.age>=15 and od.rvu>=150 then 1 else 0 end) as tot_charity_f_15a,
			sum(case when cp.sex='m' and si.hcare_id='18' and sd.status='member' and sd.dependent_pid='' and cp.age>=15 and od.rvu>=150 then 1 else 0 end) as tot_phic_mem_m,
			sum(case when cp.sex='f' and si.hcare_id='18' and sd.status='member' and sd.dependent_pid='' and cp.age>=15 and od.rvu>=150 then 1 else 0 end) as tot_phic_mem_f,
			sum(case when cp.sex='m' and si.hcare_id='18' and sd.status!='member' and sd.dependent_pid!='' and cp.age>=15 and od.rvu>=150 then 1 else 0 end) as tot_phic_dep_m,
			sum(case when cp.sex='f' and si.hcare_id='18' and sd.status!='member' and sd.dependent_pid!='' and cp.age>=15 and od.rvu>=150 then 1 else 0 end) as tot_phic_dep_f,
			sum(case when cp.sex='m' and si.hcare_id='27' and sd.status='member' and sd.dependent_pid='' and cp.age>=15 and od.rvu>=150 then 1 else 0 end) as tot_pihp_mem_m,
			sum(case when cp.sex='f' and si.hcare_id='27' and sd.status='member' and sd.dependent_pid='' and cp.age>=15 and od.rvu>=150 then 1 else 0 end) as tot_pihp_mem_f,
			sum(case when cp.sex='m' and si.hcare_id='27' and sd.status!='member' and sd.dependent_pid!='' and cp.age>=15 and od.rvu>=150 then 1 else 0 end) as tot_pihp_dep_m,
			sum(case when cp.sex='f' and si.hcare_id='27' and sd.status!='member' and sd.dependent_pid!='' and cp.age>=15 and od.rvu>=150 then 1 else 0 end) as tot_pihp_dep_f
			from seg_ops_serv as os left join care_encounter_op as co on os.refno=co.refno
			left join seg_ops_servdetails as od on os.refno=od.refno
			inner join care_person as cp on os.pid=cp.pid
			left join care_encounter as ce on os.encounter_nr=ce.encounter_nr
			inner join seg_encounter_insurance as si on os.encounter_nr=si.encounter_nr
			left join seg_dependents as sd on os.pid=sd.parent_pid
			left join care_ward as cw on ce.current_ward_nr=cw.nr
			inner join seg_accomodation_type as sa on cw.accomodation_type=sa.accomodation_nr
			where co.op_date like '".$date."%'";
			#echo $sql1;
			$result1 = $db->Execute($sql1);
			while($row1 = $result1->FetchRow())
			{
				$tot_male=$row1['tot_pay_m_15a']+$row1['tot_charity_m_15a']+$row1['tot_phic_mem_m']+$row1['tot_phic_dep_m']+$row1['tot_pihp_mem_m']+$row1['tot_pihp_dep_m'];
				$tot_female=$row1['tot_pay_f_15a']+$row1['tot_charity_f_15a']+$row1['tot_phic_mem_f']+$row1['tot_phic_dep_f']+$row1['tot_pihp_mem_f']+$row1['tot_pihp_dep_f'];
				$grand_total=$tot_male+$tot_female;
				$this->Data[] = array ('15 and Above (Major)', $row1['tot_pay_m_15a'],
				$row1['tot_pay_f_15a'], $row1['tot_charity_m_15a'], $row1['tot_charity_f_15a'], 
				$row1['tot_phic_mem_m'], $row1['tot_phic_mem_f'],  $row1['tot_phic_dep_m'],
				$row1['tot_phic_dep_f'], $row1['tot_pihp_mem_m'], $row1['tot_pihp_mem_f'], 
				$row1['tot_pihp_dep_m'], $row1['tot_pihp_dep_f'], $tot_male, $tot_female, $grand_total);
				
				$this->tot_major =  $grand_total;
			}
			
			//query for 15 and above years old with cs procedure
			$sql2 = "select 
			sum(case when cp.sex='m' and sa.accomodation_name='payward' and cp.age>=15 and od.rvu=150 then 1 else 0 end) as tot_pay_m_15a,
			sum(case when cp.sex='f' and sa.accomodation_name='payward' and cp.age>=15 and od.rvu=150 then 1 else 0 end) as tot_pay_f_15a,
			sum(case when cp.sex='m' and sa.accomodation_name='charity' and cp.age>=15 and od.rvu=150 then 1 else 0 end) as tot_charity_m_15a,
			sum(case when cp.sex='f' and sa.accomodation_name='charity' and cp.age>=15 and od.rvu=150 then 1 else 0 end) as tot_charity_f_15a,
			sum(case when cp.sex='m' and si.hcare_id='18' and sd.status='member' and sd.dependent_pid='' and cp.age>=15 and od.rvu=150 then 1 else 0 end) as tot_phic_mem_m,
			sum(case when cp.sex='f' and si.hcare_id='18' and sd.status='member' and sd.dependent_pid='' and cp.age>=15 and od.rvu=150 then 1 else 0 end) as tot_phic_mem_f,
			sum(case when cp.sex='m' and si.hcare_id='18' and sd.status!='member' and sd.dependent_pid!='' and cp.age>=15 and od.rvu=150 then 1 else 0 end) as tot_phic_dep_m,
			sum(case when cp.sex='f' and si.hcare_id='18' and sd.status!='member' and sd.dependent_pid!='' and cp.age>=15 and od.rvu=150 then 1 else 0 end) as tot_phic_dep_f,
			sum(case when cp.sex='m' and si.hcare_id='27' and sd.status='member' and sd.dependent_pid='' and cp.age>=15 and od.rvu=150 then 1 else 0 end) as tot_pihp_mem_m,
			sum(case when cp.sex='f' and si.hcare_id='27' and sd.status='member' and sd.dependent_pid='' and cp.age>=15 and od.rvu=150 then 1 else 0 end) as tot_pihp_mem_f,
			sum(case when cp.sex='m' and si.hcare_id='27' and sd.status!='member' and sd.dependent_pid!='' and cp.age>=15 and od.rvu=150 then 1 else 0 end) as tot_pihp_dep_m,
			sum(case when cp.sex='f' and si.hcare_id='27' and sd.status!='member' and sd.dependent_pid!='' and cp.age>=15 and od.rvu=150 then 1 else 0 end) as tot_pihp_dep_f
			from seg_ops_serv as os left join care_encounter_op as co on os.refno=co.refno
			left join seg_ops_servdetails as od on os.refno=od.refno
			inner join care_person as cp on os.pid=cp.pid
			left join care_encounter as ce on os.encounter_nr=ce.encounter_nr
			inner join seg_encounter_insurance as si on os.encounter_nr=si.encounter_nr
			left join seg_dependents as sd on os.pid=sd.parent_pid
			left join care_ward as cw on ce.current_ward_nr=cw.nr
			inner join seg_accomodation_type as sa on cw.accomodation_type=sa.accomodation_nr
			where co.op_date like '".$date."%'";
			#echo $sql2;
			$result2 = $db->Execute($sql2);
			while($row2 = $result2->FetchRow())
			{
				$tot_male=$row2['tot_pay_m_15a']+$row2['tot_charity_m_15a']+$row2['tot_phic_mem_m']+$row2['tot_phic_dep_m']+$row2['tot_pihp_mem_m']+$row2['tot_pihp_dep_m'];
				$tot_female=$row2['tot_pay_f_15a']+$row2['tot_charity_f_15a']+$row2['tot_phic_mem_f']+$row2['tot_phic_dep_f']+$row2['tot_pihp_mem_f']+$row2['tot_pihp_dep_f'];
				$grand_total=$tot_male+$tot_female;
				$this->Data[] = array ('C/S', $row2['tot_pay_m_15a'],
				$row2['tot_pay_f_15a'], $row2['tot_charity_m_15a'], $row2['tot_charity_f_15a'], 
				$row2['tot_phic_mem_m'], $row2['tot_phic_mem_f'],  $row2['tot_phic_dep_m'],
				$row2['tot_phic_dep_f'], $row2['tot_pihp_mem_m'], $row2['tot_pihp_mem_f'], 
				$row2['tot_pihp_dep_m'], $row2['tot_pihp_dep_f'], $tot_male, $tot_female, $grand_total);
				$this->tot_major +=  $grand_total; 
			}
			
			//query for 15 and above years old with minor procedures
			$sql2 = "select 
			sum(case when cp.sex='m' and sa.accomodation_name='payward' and cp.age>=15 and od.rvu<150 then 1 else 0 end) as tot_pay_m_15a,
			sum(case when cp.sex='f' and sa.accomodation_name='payward' and cp.age>=15 and od.rvu<150 then 1 else 0 end) as tot_pay_f_15a,
			sum(case when cp.sex='m' and sa.accomodation_name='charity' and cp.age>=15 and od.rvu<150 then 1 else 0 end) as tot_charity_m_15a,
			sum(case when cp.sex='f' and sa.accomodation_name='charity' and cp.age>=15 and od.rvu<150 then 1 else 0 end) as tot_charity_f_15a,
			sum(case when cp.sex='m' and si.hcare_id='18' and sd.status='member' and sd.dependent_pid='' and cp.age>=15 and od.rvu<150 then 1 else 0 end) as tot_phic_mem_m,
			sum(case when cp.sex='f' and si.hcare_id='18' and sd.status='member' and sd.dependent_pid='' and cp.age>=15 and od.rvu<150 then 1 else 0 end) as tot_phic_mem_f,
			sum(case when cp.sex='m' and si.hcare_id='18' and sd.status!='member' and sd.dependent_pid!='' and cp.age>=15 and od.rvu<150 then 1 else 0 end) as tot_phic_dep_m,
			sum(case when cp.sex='f' and si.hcare_id='18' and sd.status!='member' and sd.dependent_pid!='' and cp.age>=15 and od.rvu<150 then 1 else 0 end) as tot_phic_dep_f,
			sum(case when cp.sex='m' and si.hcare_id='27' and sd.status='member' and sd.dependent_pid='' and cp.age>=15 and od.rvu<150 then 1 else 0 end) as tot_pihp_mem_m,
			sum(case when cp.sex='f' and si.hcare_id='27' and sd.status='member' and sd.dependent_pid='' and cp.age>=15 and od.rvu<150 then 1 else 0 end) as tot_pihp_mem_f,
			sum(case when cp.sex='m' and si.hcare_id='27' and sd.status!='member' and sd.dependent_pid!='' and cp.age>=15 and od.rvu<150 then 1 else 0 end) as tot_pihp_dep_m,
			sum(case when cp.sex='f' and si.hcare_id='27' and sd.status!='member' and sd.dependent_pid!='' and cp.age>=15 and od.rvu<150 then 1 else 0 end) as tot_pihp_dep_f
			from seg_ops_serv as os left join care_encounter_op as co on os.refno=co.refno
			left join seg_ops_servdetails as od on os.refno=od.refno
			inner join care_person as cp on os.pid=cp.pid
			left join care_encounter as ce on os.encounter_nr=ce.encounter_nr
			inner join seg_encounter_insurance as si on os.encounter_nr=si.encounter_nr
			left join seg_dependents as sd on os.pid=sd.parent_pid
			left join care_ward as cw on ce.current_ward_nr=cw.nr
			inner join seg_accomodation_type as sa on cw.accomodation_type=sa.accomodation_nr
			where co.op_date like '".$date."%'";
			#echo $sql2;
			$result2 = $db->Execute($sql2);
			while($row2 = $result2->FetchRow())
			{
				$tot_male=$row2['tot_pay_m_15a']+$row2['tot_charity_m_15a']+$row2['tot_phic_mem_m']+$row2['tot_phic_dep_m']+$row2['tot_pihp_mem_m']+$row2['tot_pihp_dep_m'];
				$tot_female=$row2['tot_pay_f_15a']+$row2['tot_charity_f_15a']+$row2['tot_phic_mem_f']+$row2['tot_phic_dep_f']+$row2['tot_pihp_mem_f']+$row2['tot_pihp_dep_f'];
				$grand_total=$tot_male+$tot_female;
				$this->Data[] = array ('Minor', $row2['tot_pay_m_15a'],
				$row2['tot_pay_f_15a'], $row2['tot_charity_m_15a'], $row2['tot_charity_f_15a'], 
				$row2['tot_phic_mem_m'], $row2['tot_phic_mem_f'],  $row2['tot_phic_dep_m'],
				$row2['tot_phic_dep_f'], $row2['tot_pihp_mem_m'], $row2['tot_pihp_mem_f'], 
				$row2['tot_pihp_dep_m'], $row2['tot_pihp_dep_f'], $tot_male, $tot_female, $grand_total);
				$this->tot_minor =  $grand_total; 
			}
			
			//query for 15 and above years old with OPD
			$sql2 = "select 
			sum(case when cp.sex='m' and sa.accomodation_name='payward' and cp.age>=15 and od.rvu>150 and ce.encounter_type=2 then 1 else 0 end) as tot_pay_m_15a,
			sum(case when cp.sex='f' and sa.accomodation_name='payward' and cp.age>=15 and od.rvu>150 and ce.encounter_type=2 then 1 else 0 end) as tot_pay_f_15a,
			sum(case when cp.sex='m' and sa.accomodation_name='charity' and cp.age>=15 and od.rvu>150 and ce.encounter_type=2 then 1 else 0 end) as tot_charity_m_15a,
			sum(case when cp.sex='f' and sa.accomodation_name='charity' and cp.age>=15 and od.rvu>150 and ce.encounter_type=2 then 1 else 0 end) as tot_charity_f_15a,
			sum(case when cp.sex='m' and si.hcare_id='18' and sd.status='member' and sd.dependent_pid='' and cp.age>=15 and od.rvu>150 and ce.encounter_type=2 then 1 else 0 end) as tot_phic_mem_m,
			sum(case when cp.sex='f' and si.hcare_id='18' and sd.status='member' and sd.dependent_pid='' and cp.age>=15 and od.rvu>150 and ce.encounter_type=2 then 1 else 0 end) as tot_phic_mem_f,
			sum(case when cp.sex='m' and si.hcare_id='18' and sd.status!='member' and sd.dependent_pid!='' and cp.age>=15 and od.rvu>150 and ce.encounter_type=2 then 1 else 0 end) as tot_phic_dep_m,
			sum(case when cp.sex='f' and si.hcare_id='18' and sd.status!='member' and sd.dependent_pid!='' and cp.age>=15 and od.rvu>150 and ce.encounter_type=2 then 1 else 0 end) as tot_phic_dep_f,
			sum(case when cp.sex='m' and si.hcare_id='27' and sd.status='member' and sd.dependent_pid='' and cp.age>=15 and od.rvu>150 and ce.encounter_type=2 then 1 else 0 end) as tot_pihp_mem_m,
			sum(case when cp.sex='f' and si.hcare_id='27' and sd.status='member' and sd.dependent_pid='' and cp.age>=15 and od.rvu>150 and ce.encounter_type=2 then 1 else 0 end) as tot_pihp_mem_f,
			sum(case when cp.sex='m' and si.hcare_id='27' and sd.status!='member' and sd.dependent_pid!='' and cp.age>=15 and od.rvu>150 and ce.encounter_type=2 then 1 else 0 end) as tot_pihp_dep_m,
			sum(case when cp.sex='f' and si.hcare_id='27' and sd.status!='member' and sd.dependent_pid!='' and cp.age>=15 and od.rvu>150 and ce.encounter_type=2 then 1 else 0 end) as tot_pihp_dep_f
			from seg_ops_serv as os left join care_encounter_op as co on os.refno=co.refno
			left join seg_ops_servdetails as od on os.refno=od.refno
			inner join care_person as cp on os.pid=cp.pid
			left join care_encounter as ce on os.encounter_nr=ce.encounter_nr
			inner join seg_encounter_insurance as si on os.encounter_nr=si.encounter_nr
			left join seg_dependents as sd on os.pid=sd.parent_pid
			left join care_ward as cw on ce.current_ward_nr=cw.nr
			inner join seg_accomodation_type as sa on cw.accomodation_type=sa.accomodation_nr
			where co.op_date like '".$date."%'";
			#echo $sql2;
			$result2 = $db->Execute($sql2);
			while($row2 = $result2->FetchRow())
			{
				$tot_male=$row2['tot_pay_m_15a']+$row2['tot_charity_m_15a']+$row2['tot_phic_mem_m']+$row2['tot_phic_dep_m']+$row2['tot_pihp_mem_m']+$row2['tot_pihp_dep_m'];
				$tot_female=$row2['tot_pay_f_15a']+$row2['tot_charity_f_15a']+$row2['tot_phic_mem_f']+$row2['tot_phic_dep_f']+$row2['tot_pihp_mem_f']+$row2['tot_pihp_dep_f'];
				$grand_total=$tot_male+$tot_female;
				$this->Data[] = array ('OPD', $row2['tot_pay_m_15a'],
				$row2['tot_pay_f_15a'], $row2['tot_charity_m_15a'], $row2['tot_charity_f_15a'], 
				$row2['tot_phic_mem_m'], $row2['tot_phic_mem_f'],  $row2['tot_phic_dep_m'],
				$row2['tot_phic_dep_f'], $row2['tot_pihp_mem_m'], $row2['tot_pihp_mem_f'], 
				$row2['tot_pihp_dep_m'], $row2['tot_pihp_dep_f'], $tot_male, $tot_female, $grand_total);
				$this->tot_major +=  $grand_total;
			}
			
			//query for below 15 years old with major procedures
			$sql2 = "select 
			sum(case when cp.sex='m' and sa.accomodation_name='payward' and cp.age<15 and od.rvu>=150 then 1 else 0 end) as tot_pay_m_15a,
			sum(case when cp.sex='f' and sa.accomodation_name='payward' and cp.age<15 and od.rvu>=150 then 1 else 0 end) as tot_pay_f_15a,
			sum(case when cp.sex='m' and sa.accomodation_name='charity' and cp.age<15 and od.rvu>=150 then 1 else 0 end) as tot_charity_m_15a,
			sum(case when cp.sex='f' and sa.accomodation_name='charity' and cp.age<15 and od.rvu>=150 then 1 else 0 end) as tot_charity_f_15a,
			sum(case when cp.sex='m' and si.hcare_id='18' and sd.status='member' and sd.dependent_pid='' and cp.age<15 and od.rvu>=150 then 1 else 0 end) as tot_phic_mem_m,
			sum(case when cp.sex='f' and si.hcare_id='18' and sd.status='member' and sd.dependent_pid='' and cp.age<15 and od.rvu>=150 then 1 else 0 end) as tot_phic_mem_f,
			sum(case when cp.sex='m' and si.hcare_id='18' and sd.status!='member' and sd.dependent_pid!='' and cp.age<15 and od.rvu>=150 then 1 else 0 end) as tot_phic_dep_m,
			sum(case when cp.sex='f' and si.hcare_id='18' and sd.status!='member' and sd.dependent_pid!='' and cp.age<15 and od.rvu>=150 then 1 else 0 end) as tot_phic_dep_f,
			sum(case when cp.sex='m' and si.hcare_id='27' and sd.status='member' and sd.dependent_pid='' and cp.age<15 and od.rvu>=150 then 1 else 0 end) as tot_pihp_mem_m,
			sum(case when cp.sex='f' and si.hcare_id='27' and sd.status='member' and sd.dependent_pid='' and cp.age<15 and od.rvu>=150 then 1 else 0 end) as tot_pihp_mem_f,
			sum(case when cp.sex='m' and si.hcare_id='27' and sd.status!='member' and sd.dependent_pid!='' and cp.age<15 and od.rvu>=150 then 1 else 0 end) as tot_pihp_dep_m,
			sum(case when cp.sex='f' and si.hcare_id='27' and sd.status!='member' and sd.dependent_pid!='' and cp.age<15 and od.rvu>=150 then 1 else 0 end) as tot_pihp_dep_f
			from seg_ops_serv as os left join care_encounter_op as co on os.refno=co.refno
			left join seg_ops_servdetails as od on os.refno=od.refno
			inner join care_person as cp on os.pid=cp.pid
			left join care_encounter as ce on os.encounter_nr=ce.encounter_nr
			inner join seg_encounter_insurance as si on os.encounter_nr=si.encounter_nr
			left join seg_dependents as sd on os.pid=sd.parent_pid
			left join care_ward as cw on ce.current_ward_nr=cw.nr
			inner join seg_accomodation_type as sa on cw.accomodation_type=sa.accomodation_nr
			where co.op_date like '".$date."%'";
			#echo $sql2;
			$result2 = $db->Execute($sql2);
			while($row2 = $result2->FetchRow())
			{
				$tot_male=$row2['tot_pay_m_15a']+$row2['tot_charity_m_15a']+$row2['tot_phic_mem_m']+$row2['tot_phic_dep_m']+$row2['tot_pihp_mem_m']+$row2['tot_pihp_dep_m'];
				$tot_female=$row2['tot_pay_f_15a']+$row2['tot_charity_f_15a']+$row2['tot_phic_mem_f']+$row2['tot_phic_dep_f']+$row2['tot_pihp_mem_f']+$row2['tot_pihp_dep_f'];
				$grand_total=$tot_male+$tot_female;
				$this->Data[] = array ('Below 15 (Major)', $row2['tot_pay_m_15a'],
				$row2['tot_pay_f_15a'], $row2['tot_charity_m_15a'], $row2['tot_charity_f_15a'], 
				$row2['tot_phic_mem_m'], $row2['tot_phic_mem_f'],  $row2['tot_phic_dep_m'],
				$row2['tot_phic_dep_f'], $row2['tot_pihp_mem_m'], $row2['tot_pihp_mem_f'], 
				$row2['tot_pihp_dep_m'], $row2['tot_pihp_dep_f'], $tot_male, $tot_female, $grand_total);
				$this->tot_major +=  $grand_total;
			}
			
			//query for below 15 years old with minor procedures
			$sql2 = "select 
			sum(case when cp.sex='m' and sa.accomodation_name='payward' and cp.age<15 and od.rvu<150 then 1 else 0 end) as tot_pay_m_15a,
			sum(case when cp.sex='f' and sa.accomodation_name='payward' and cp.age<15 and od.rvu<150 then 1 else 0 end) as tot_pay_f_15a,
			sum(case when cp.sex='m' and sa.accomodation_name='charity' and cp.age<15 and od.rvu<150 then 1 else 0 end) as tot_charity_m_15a,
			sum(case when cp.sex='f' and sa.accomodation_name='charity' and cp.age<15 and od.rvu<150 then 1 else 0 end) as tot_charity_f_15a,
			sum(case when cp.sex='m' and si.hcare_id='18' and sd.status='member' and sd.dependent_pid='' and cp.age<15 and od.rvu<150 then 1 else 0 end) as tot_phic_mem_m,
			sum(case when cp.sex='f' and si.hcare_id='18' and sd.status='member' and sd.dependent_pid='' and cp.age<15 and od.rvu<150 then 1 else 0 end) as tot_phic_mem_f,
			sum(case when cp.sex='m' and si.hcare_id='18' and sd.status!='member' and sd.dependent_pid!='' and cp.age<15 and od.rvu<150 then 1 else 0 end) as tot_phic_dep_m,
			sum(case when cp.sex='f' and si.hcare_id='18' and sd.status!='member' and sd.dependent_pid!='' and cp.age<15 and od.rvu<150 then 1 else 0 end) as tot_phic_dep_f,
			sum(case when cp.sex='m' and si.hcare_id='27' and sd.status='member' and sd.dependent_pid='' and cp.age<15 and od.rvu<150 then 1 else 0 end) as tot_pihp_mem_m,
			sum(case when cp.sex='f' and si.hcare_id='27' and sd.status='member' and sd.dependent_pid='' and cp.age<15 and od.rvu<150 then 1 else 0 end) as tot_pihp_mem_f,
			sum(case when cp.sex='m' and si.hcare_id='27' and sd.status!='member' and sd.dependent_pid!='' and cp.age<15 and od.rvu<150 then 1 else 0 end) as tot_pihp_dep_m,
			sum(case when cp.sex='f' and si.hcare_id='27' and sd.status!='member' and sd.dependent_pid!='' and cp.age<15 and od.rvu<150 then 1 else 0 end) as tot_pihp_dep_f
			from seg_ops_serv as os left join care_encounter_op as co on os.refno=co.refno
			left join seg_ops_servdetails as od on os.refno=od.refno
			inner join care_person as cp on os.pid=cp.pid
			left join care_encounter as ce on os.encounter_nr=ce.encounter_nr
			inner join seg_encounter_insurance as si on os.encounter_nr=si.encounter_nr
			left join seg_dependents as sd on os.pid=sd.parent_pid
			left join care_ward as cw on ce.current_ward_nr=cw.nr
			inner join seg_accomodation_type as sa on cw.accomodation_type=sa.accomodation_nr
			where co.op_date like '".$date."%'";
			#echo $sql2;
			$result2 = $db->Execute($sql2);
			while($row2 = $result2->FetchRow())
			{
				$tot_male=$row2['tot_pay_m_15a']+$row2['tot_charity_m_15a']+$row2['tot_phic_mem_m']+$row2['tot_phic_dep_m']+$row2['tot_pihp_mem_m']+$row2['tot_pihp_dep_m'];
				$tot_female=$row2['tot_pay_f_15a']+$row2['tot_charity_f_15a']+$row2['tot_phic_mem_f']+$row2['tot_phic_dep_f']+$row2['tot_pihp_mem_f']+$row2['tot_pihp_dep_f'];
				$grand_total=$tot_male+$tot_female;
				$this->Data[] = array ('Minor', $row2['tot_pay_m_15a'],
				$row2['tot_pay_f_15a'], $row2['tot_charity_m_15a'], $row2['tot_charity_f_15a'], 
				$row2['tot_phic_mem_m'], $row2['tot_phic_mem_f'],  $row2['tot_phic_dep_m'],
				$row2['tot_phic_dep_f'], $row2['tot_pihp_mem_m'], $row2['tot_pihp_mem_f'], 
				$row2['tot_pihp_dep_m'], $row2['tot_pihp_dep_f'], $tot_male, $tot_female, $grand_total);
				$this->tot_minor +=  $grand_total;
			}
			
			//query for below 15 years old with OPD
			$sql2 = "select 
			sum(case when cp.sex='m' and sa.accomodation_name='payward' and cp.age<15 and od.rvu<150 and ce.encounter_type=2 then 1 else 0 end) as tot_pay_m_15a,
			sum(case when cp.sex='f' and sa.accomodation_name='payward' and cp.age<15 and od.rvu<150 and ce.encounter_type=2 then 1 else 0 end) as tot_pay_f_15a,
			sum(case when cp.sex='m' and sa.accomodation_name='charity' and cp.age<15 and od.rvu<150 and ce.encounter_type=2 then 1 else 0 end) as tot_charity_m_15a,
			sum(case when cp.sex='f' and sa.accomodation_name='charity' and cp.age<15 and od.rvu<150 and ce.encounter_type=2 then 1 else 0 end) as tot_charity_f_15a,
			sum(case when cp.sex='m' and si.hcare_id='18' and sd.status='member' and sd.dependent_pid='' and cp.age<15 and od.rvu<150 and ce.encounter_type=2 then 1 else 0 end) as tot_phic_mem_m,
			sum(case when cp.sex='f' and si.hcare_id='18' and sd.status='member' and sd.dependent_pid='' and cp.age<15 and od.rvu<150 and ce.encounter_type=2 then 1 else 0 end) as tot_phic_mem_f,
			sum(case when cp.sex='m' and si.hcare_id='18' and sd.status!='member' and sd.dependent_pid!='' and cp.age<15 and od.rvu<150 and ce.encounter_type=2 then 1 else 0 end) as tot_phic_dep_m,
			sum(case when cp.sex='f' and si.hcare_id='18' and sd.status!='member' and sd.dependent_pid!='' and cp.age<15 and od.rvu<150 and ce.encounter_type=2 then 1 else 0 end) as tot_phic_dep_f,
			sum(case when cp.sex='m' and si.hcare_id='27' and sd.status='member' and sd.dependent_pid='' and cp.age<15 and od.rvu<150 and ce.encounter_type=2 then 1 else 0 end) as tot_pihp_mem_m,
			sum(case when cp.sex='f' and si.hcare_id='27' and sd.status='member' and sd.dependent_pid='' and cp.age<15 and od.rvu<150 and ce.encounter_type=2 then 1 else 0 end) as tot_pihp_mem_f,
			sum(case when cp.sex='m' and si.hcare_id='27' and sd.status!='member' and sd.dependent_pid!='' and cp.age<15 and od.rvu<150 and ce.encounter_type=2 then 1 else 0 end) as tot_pihp_dep_m,
			sum(case when cp.sex='f' and si.hcare_id='27' and sd.status!='member' and sd.dependent_pid!='' and cp.age<15 and od.rvu<150 and ce.encounter_type=2 then 1 else 0 end) as tot_pihp_dep_f
			from seg_ops_serv as os left join care_encounter_op as co on os.refno=co.refno
			left join seg_ops_servdetails as od on os.refno=od.refno
			inner join care_person as cp on os.pid=cp.pid
			left join care_encounter as ce on os.encounter_nr=ce.encounter_nr
			inner join seg_encounter_insurance as si on os.encounter_nr=si.encounter_nr
			left join seg_dependents as sd on os.pid=sd.parent_pid
			left join care_ward as cw on ce.current_ward_nr=cw.nr
			inner join seg_accomodation_type as sa on cw.accomodation_type=sa.accomodation_nr
			where co.op_date like '".$date."%'";
			#echo $sql2;
			$result2 = $db->Execute($sql2);
			while($row2 = $result2->FetchRow())
			{
				$tot_male=$row2['tot_pay_m_15a']+$row2['tot_charity_m_15a']+$row2['tot_phic_mem_m']+$row2['tot_phic_dep_m']+$row2['tot_pihp_mem_m']+$row2['tot_pihp_dep_m'];
				$tot_female=$row2['tot_pay_f_15a']+$row2['tot_charity_f_15a']+$row2['tot_phic_mem_f']+$row2['tot_phic_dep_f']+$row2['tot_pihp_mem_f']+$row2['tot_pihp_dep_f'];
				$grand_total=$tot_male+$tot_female;
				$this->Data[] = array ('OPD', $row2['tot_pay_m_15a'],
				$row2['tot_pay_f_15a'], $row2['tot_charity_m_15a'], $row2['tot_charity_f_15a'], 
				$row2['tot_phic_mem_m'], $row2['tot_phic_mem_f'],  $row2['tot_phic_dep_m'],
				$row2['tot_phic_dep_f'], $row2['tot_pihp_mem_m'], $row2['tot_pihp_mem_f'], 
				$row2['tot_pihp_dep_m'], $row2['tot_pihp_dep_f'], $tot_male, $tot_female, $grand_total);
				$this->tot_minor +=  $grand_total;  
			} 

		 $this->_count = count($this->Data)	;
    }
  }
  
  function AfterData() {
    if (!$this->_count)
      $this->Cell(0, 5, 'No operations were conducted this month.', 1, 1, 'L');
      
      $this->SetFont("Arial", "B", 14);
      $this->SetXY(20, 140);
      $this->Cell(50, 4, "TOTAL MAJOR : ");
      $this->Cell(20, 4, $this->tot_major);
      $this->Line(60, 144, 90, 144);  
      $this->SetXY(20, 145);
      $this->Cell(50, 4, "TOTAL MINOR : ");
      $this->Cell(20, 4, $this->tot_minor);
      $this->Line(60, 149, 90, 149);   
  }
  
  function Footer() {
   
  }
}

$x = new Monthly_Statistical();
$x->AliasNbPages();
$x->FetchData();

$x->Report();

?>