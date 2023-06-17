<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgenclass.php');                               
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';

require_once($root_path.'include/care_api_classes/class_suspected_illness.php');
$susp_obj = new SuspectedIllness();

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

class RepGen_Suspected_Illness_Report extends RepGen {

var $totalRow=0;

	function RepGen_Suspected_Illness_Report($dateFrm, $dateTo)                                                              
	{
		global $db;
		$this->RepGen("SUSPECTED ILLNESSES REPORT","L", Legal);
		$this->Headers = array(
				' ',
				'CASE NO.',
				'FULL NAME',  
				'AGE',
				'SEX',
				'ADDRESS',  
				'ADM. DATE',
				'ATTENDING PHYS.',
				'SUSPECTED ILLNESS',
				'DIAGNOSIS'
			);
		$this->colored = TRUE;
		$this->ColumnWidth = array(8,25,40,10,10,45,25,40,50,60);
		$this->RowHeight = 5;
		$this->TextHeight = 5;
		$this->TextPadding = 0.2;
		$this->LEFTMARGIN = 15;
		$this->Alignment = array('R','C','L','C','C','L','C','L','L');
																					 
		$this->total_width = array_sum($this->ColumnWidth);
		$this->NoWrap=FALSE;

		$this->date_from = date('Y-m-d',strtotime($dateFrm));
		$this->date_to = date('Y-m-d', strtotime($dateTo));              

		if ($this->colored) $this->SetDrawColor(0xDD);
	}

	function Header()
	{
		global $root_path, $db;
		$objInfo = new Hospital_Admin();

		if ($row = $objInfo->getAllHospitalInfo()) {
			$row['hosp_agency'] = strtoupper($row['hosp_agency']);
			$row['hosp_name']   = strtoupper($row['hosp_name']);
		}
		else {
			$row['hosp_country'] = "Republic of the Philippines";
			$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
			$row['hosp_addr1']   = "Quezon Ave., Digos City";
		}
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',75,8,20);
		$this->SetFont("Arial","I","9");
		$total_w = 170;
		$this->Cell(50,4);
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(50,4);
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(50,4);
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(50,4);
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
		$this->Ln(4);
		$this->SetFont('Arial','B',12);
		$this->Cell(50,5);

		$this->Cell($total_w,4,strtoupper('SUSPECTED ILLNESS REPORT'),0,1,'C');
		$this->SetFont('Arial','B',9);
		$this->Ln(4);
		$this->Cell(50,5);
		
		$dates = "From ".date("F j, Y",strtotime($this->date_from))." To ".date("F j, Y",strtotime($this->date_to));

		$this->Cell($total_w,4,$dates,0,1,'C');

		$this->Ln(15);


		$this->SetFont('Arial','B',11);
		$this->Cell($this->ColumnWidth[0],$this->RowHeight,$this->Headers[0],0,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$this->RowHeight,$this->Headers[1],0,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$this->RowHeight,$this->Headers[2],0,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$this->RowHeight,$this->Headers[3],0,0,'C',1);
		$this->Cell($this->ColumnWidth[4],$this->RowHeight,$this->Headers[4],0,0,'C',1);
		$this->Cell($this->ColumnWidth[5],$this->RowHeight,$this->Headers[5],0,0,'C',1);
		$this->Cell($this->ColumnWidth[6],$this->RowHeight,$this->Headers[6],0,0,'C',1);
		$this->Cell($this->ColumnWidth[7],$this->RowHeight,$this->Headers[7],0,0,'C',1);   
		$this->Cell($this->ColumnWidth[8],$this->RowHeight,$this->Headers[8],0,0,'C',1);
		$this->Cell($this->ColumnWidth[9],$this->RowHeight,$this->Headers[9],0,0,'C',1);
		$this->Ln();
	}

	function BeforeData()
	{
		$this->FONTSIZE = 9;
		if ($this->colored) {
				$this->DrawColor = array(255,255,255);
		}
	}
 
	function AfterData()
	{
			global $db;
			if (!$this->_count) {
					$this->SetFont('Arial','B',9);
					$this->SetFillColor(255);
					$this->SetTextColor(0);
					$this->Cell($this->total_width, $this->RowHeight, "No records found for this report...", 0, 1, 'L', 1);
			} 
			$cols = array();
	}

		function Footer()
	{
		$this->SetY(-23);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}
	
	
	function AcceptPageBreak()
	{
		return true;
	}
	
	function FetchData()
	{
		global $db;
		if (empty($this->date_to)) $end_date="NOW()";
		else $end_date=$this->date_to;
		if (empty($this->date_from)) $start_date="NOW()";
		else
		$start_date=$this->date_from;

//		$sql_code = "SELECT nd.code_illness, nd.illness_name  
//									FROM seg_notifiable_diseases AS nd 
//									WHERE nd.code_illness != 'ZZZOTHER'
//									AND nd.status IS NULL OR nd.status NOT IN ('deleted');";     
//		if($result_code = $db->Execute($sql_code)){
//			$this->_count = $result_code->RecordCount();
//			while ($row_code=$result_code->FetchRow())
//			{	    
				$sql = 'SELECT   en.code_illness,
								e.encounter_nr AS "Case No.",
								CONCAT(IF (trim(p.name_last) IS NULL,"",trim(p.name_last)),", ",
								IF(trim(p.name_first) IS NULL ,"",trim(p.name_first))," ",
								IF(trim(p.name_middle) IS NULL,"",trim(p.name_middle))) AS "Full Name",
								IF (fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS Age,
								UPPER(p.sex) AS Sex,

								CONCAT(IF (trim(p.street_name) IS NULL,"",trim(p.street_name))," ",
								IF (trim(sb.brgy_name) IS NULL,"",trim(sb.brgy_name))," ",
								IF (trim(sm.mun_name) IS NULL,"",trim(sm.mun_name))," ",
								IF (trim(sm.zipcode) IS NULL,"",trim(sm.zipcode))," ",
								IF (trim(sp.prov_name) IS NULL,"",trim(sp.prov_name))," ",
								IF (trim(sr.region_name) IS NULL,"",trim(sr.region_name))) AS "Complete Address",
																							
								e.admission_dt AS "Date Admitted",
								UPPER(IF (e.current_att_dr_nr,fn_get_personell_name(e.current_att_dr_nr),
								fn_get_personell_name(e.consulting_dr_nr))) AS "Attending Physician", 
								e.er_opd_diagnosis

								FROM seg_encounter_notifiable AS en
								LEFT JOIN care_encounter as e 
								ON e.encounter_nr = en.encounter_nr  
								LEFT JOIN care_person as p 
								ON p.pid = e.pid 
								LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
								LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr 
								LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr 
								LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
								WHERE DATE(e.admission_dt) BETWEEN '.$db->qstr($start_date).' AND '.$db->qstr($end_date).    
								//'AND en.code_illness = '.$db->qstr($row_code['code_illness']).
								'ORDER BY en.code_illness, e.admission_dt';  
								//AND en.code_illness = "INFLU"';    
				 echo $sql;
				$result=$db->Execute($sql);              
				$p_count = $result->RecordCount();
				//code for heading
				$this->SetFont('Arial','B',12);  
				$illness = $row_code['illness_name'].' No. of Cases:'.$p_count.'<br>' ;     
				//echo $illness.'<br>'.$result;
				$this->Cell(0, 12, $illness, 0, 1, 'R', 1);       

				if ($result!==FALSE) {
					$this->Data=array();
					$i=1;

					 while ($row=$result->FetchRow())
					 {
						if (stristr($row['Age'],'years')){
							$age = substr($row['Age'],0,-5);
							$age = floor($age).' y';
						}elseif (stristr($row['Age'],'year')){	
							$age = substr($row['Age'],0,-4);
							$age = floor($age).' y';
						}elseif (stristr($row['Age'],'months')){	
							$age = substr($row['Age'],0,-6);
							$age = floor($age).' m';	
						}elseif (stristr($row['Age'],'month')){	
							$age = substr($row['Age'],0,-5);
							$age = floor($age).' m';		
						}elseif (stristr($row['Age'],'days')){	
							$age = substr($row['Age'],0,-4);   					
							if ($age>30){
								$age = $age/30;
								$label = 'm';
							}else $label = 'd'; 						
							$age = floor($Age).' '.$label;		
						}elseif (stristr($row['Age'],'day')){	
							$age = substr($row['Age'],0,-3);
							$age = floor($age).' d';		
						}else{
							$age = floor($row['Age']).' y';			
						} 
								 
							$this->Data[]=array(
								$i.'.',
								$row['Case No.'],
								strtoupper(trim($row['Full Name'])),  
								$age,//$row['Age'],
								strtoupper($row['Sex']),                        
								strtoupper($row['Complete Address']),
								date("m/d/Y",strtotime($row['Date Admitted'])),    
								strtoupper($row['Attending Physician']),
								$row['code_illness'],
								strtoupper($row['er_opd_diagnosis'])
							);
							$i++;
							$this->AcceptPageBreak();  
					}
				}
				else {
					echo "error:".$db->ErrorMsg();
				}
	//		}
//		}else {
//			echo "error:".$db->ErrorMsg();
//		}
	}  
}          

$rep = new RepGen_Suspected_Illness_Report($_GET["date_from"], $_GET["date_to"]);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>
