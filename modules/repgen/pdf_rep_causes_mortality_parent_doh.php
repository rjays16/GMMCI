<?php
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class RepGen_CausesMortality extends RepGen{
var $colored = TRUE;
var $from, $to;     
var $t_male_below1, $t_female_below1, $t_male_1to4, $t_female_1to4;
var $t_male_5to9, $t_female_5to9, $t_male_10to14, $t_female_10to14;
var $t_male_15to19, $t_female_15to19,$t_male_20to44,$t_female_20to44; 
var $t_male_45to59, $t_female_45to59, $t_male_60up, $t_female_60up; 
var $t_male_total, $t_female_total, $t_total; 
var $dept_nr;                   
													 
	 function RepGen_CausesMortality ($from, $to, $dept_nr) {
		global $db;
		$this->RepGen("MEDICAL RECORDS: CAUSES OF MORTALITY");
				
		$this->ColumnWidth = array(7,50, 10,10, 10,10, 10,10, 10,10, 10,10, 10,10, 10,10, 10,10, 10,10,10, 15,13);
		$this->RowHeight = 5;
		$this->TextHeight = 5;
		$this->TextPadding = 0.2;
		$this->Alignment = array('L','L', 'C','C', 'C','C', 'C','C', 'C','C', 'C','C', 'C','C', 'C','C', 'C','C', 'C','C','C', 'C','C', 'C');
		$this->PageOrientation = "L";
		$this->NoWrap = FALSE;
		$this->LEFTMARGIN = 2;
		$this->dept_nr = $dept_nr;
		
		if ($from) $this->from=date("Y-m-d",strtotime($from));
				if ($to) $this->to=date("Y-m-d",strtotime($to));    
		
		$this->useMultiCell = TRUE;
		#$this->SetFillColor(0xFF);
		$this->SetFillColor(255);
		if ($this->colored)  $this->SetDrawColor(0xDD);
		
	}
	

	function Header() {
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
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',50,8,20);
		$this->SetFont("Arial","I","9");
		$total_w = 165;
		$this->Cell(50,4);
			#$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(50,4);
			#$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
			$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(50,4);
			#$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(50,4);
			#$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
			$this->Ln(4);
			$this->SetFont('Arial','B',12);
		$this->Cell(50,5);
		
		if ($this->dept_nr){
			if ($this->dept_nr==1)
					$deptname = "Gynecology";
			elseif ($this->dept_nr==2)
					$deptname = "Medicines";		
			elseif ($this->dept_nr==3)
					$deptname = "Obstetrics";				
			elseif ($this->dept_nr==4)
					$deptname = "Pediatrics";				
			elseif ($this->dept_nr==5)
					$deptname = "Surgery";	
			elseif ($this->dept_nr==6)
					$deptname = "ENT";    			
		}else
			$deptname = "All Department";
		
		$deptname = mb_strtoupper($deptname);
		
		$this->Cell($total_w,4,'TWENTY LEADING CAUSES OF MORTALITY ('.$deptname.')',$border2,1,'C');
		 $this->SetFont('Arial','B',9);
		$this->Cell(50,5);
		
		if ($this->from==$this->to)
			$text = "For ".date("F j, Y",strtotime($this->from));
		else
				#$text = "Full History";
			$text = "From ".date("F j, Y",strtotime($this->from))." To ".date("F j, Y",strtotime($this->to));
			
			$this->Cell($total_w,4,$text,$border2,1,'C');
		$this->Ln(5);

		# Print table header    
			$this->SetFont('Arial','B',8);
		#if ($this->colored) $this->SetFillColor(0xED);
		if ($this->colored) $this->SetFillColor(255);
		$this->SetTextColor(0);
		$row=6;
		$age = $this->ColumnWidth[2] + $this->ColumnWidth[3] + $this->ColumnWidth[4] + $this->ColumnWidth[5] + $this->ColumnWidth[6] + $this->ColumnWidth[7] + $this->ColumnWidth[8] + $this->ColumnWidth[9] + $this->ColumnWidth[10];
		$age += $this->ColumnWidth[11] + $this->ColumnWidth[12] + $this->ColumnWidth[13] + $this->ColumnWidth[14] + $this->ColumnWidth[15] + $this->ColumnWidth[16] + $this->ColumnWidth[17];
		
		$this->Cell($this->ColumnWidth[0], 4, "", "TLR", 0, 'C'); 
		$this->Cell($this->ColumnWidth[1], 4, "", "TLR", 0, 'C');
		$this->Cell($age, 4, "Age Distribution of Patients", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[18] + $this->ColumnWidth[19] + $this->ColumnWidth[20], 4, "", "TLR", 0, 'C');
		$this->Cell($this->ColumnWidth[21], 4, "", "TLR", 0, 'C');  
		$this->Cell($this->ColumnWidth[22], 4, "", "TLR", 1, 'C');  
				
		$this->Cell($this->ColumnWidth[0], 4, "#", "LR", 0, 'C'); 
		$this->Cell($this->ColumnWidth[1], 4, "Cause of Death", "LR", 0, 'C');
		
		$this->Cell($this->ColumnWidth[2]+$this->ColumnWidth[3], 4, "<1", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[4]+$this->ColumnWidth[5], 4, "1-4", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[6]+$this->ColumnWidth[7], 4, "5-9", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[8]+$this->ColumnWidth[9], 4, "10-14", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[10]+$this->ColumnWidth[11], 4, "15-19", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[12]+$this->ColumnWidth[13], 4, "20-44", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[14]+$this->ColumnWidth[15], 4, "45-59", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[16]+$this->ColumnWidth[17], 4, "60 up", "1", 0, 'C');
		
		$this->Cell($this->ColumnWidth[18]+$this->ColumnWidth[19]+$this->ColumnWidth[20], 4, "Total", "LR", 0, 'C');
				
		$this->Cell($this->ColumnWidth[21], 4, "% of", "LR", 0, 'C');
				
		$this->Cell($this->ColumnWidth[22], 4, "ICD10", "LR", 1, 'C');
			 
		$this->Cell($this->ColumnWidth[0], 4, "", "LRB", 0, 'C');  
		$this->Cell($this->ColumnWidth[1], 4, "(Underlying)", "LRB", 0, 'C');  
				
		$this->Cell($this->ColumnWidth[2], 4, "M", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[3], 4, "F", "1", 0, 'C');
				
		$this->Cell($this->ColumnWidth[4], 4, "M", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[5], 4, "F", "1", 0, 'C');
				
		$this->Cell($this->ColumnWidth[6], 4, "M", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[7], 4, "F", "1", 0, 'C');
				
		$this->Cell($this->ColumnWidth[8], 4, "M", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[9], 4, "F", "1", 0, 'C');
				
		$this->Cell($this->ColumnWidth[10], 4, "M", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[11], 4, "F", "1", 0, 'C');
				
		$this->Cell($this->ColumnWidth[12], 4, "M", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[13], 4, "F", "1", 0, 'C');
				
		$this->Cell($this->ColumnWidth[14], 4, "M", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[15], 4, "F", "1", 0, 'C');
				
		$this->Cell($this->ColumnWidth[16], 4, "M", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[17], 4, "F", "1", 0, 'C');
				
		$this->Cell($this->ColumnWidth[18], 4, "M", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[19], 4, "F", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[20], 4, "T", "1", 0, 'C');
				
		$this->Cell($this->ColumnWidth[21], 4, "Total", "LRB", 0, 'C');
				
		$this->Cell($this->ColumnWidth[22], 4, "Tab No.", "LRB", 0, 'C');
				
		$this->Ln();
	}

	function Footer(){
		$this->SetY(-7);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}
	 
	function BeforeRow() {
		$this->FONTSIZE = 8;
		if ($this->colored) {
			if (($this->ROWNUM%2)>0) 
				#$this->FILLCOLOR=array(0xee, 0xef, 0xf4);
				$this->FILLCOLOR=array(255, 255, 255);
			else
				$this->FILLCOLOR=array(255,255,255);
			$this->DRAWCOLOR = array(0xDD,0xDD,0xDD);
			#$this->DRAWCOLOR = array(255,255,255);
		}
	}
	
	function BeforeData() {
		if ($this->colored) {
			$this->DrawColor = array(0xDD,0xDD,0xDD);
			#$this->DrawColor = array(255,255,255);
		}
	}
	
	function BeforeCellRender() {
		$this->FONTSIZE = 8;
		if ($this->colored) {
			if (($this->RENDERPAGEROWNUM%2)>0) 
				#$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
				$this->RENDERCELL->FillColor=array(255, 255, 255);
			else
				$this->RENDERCELL->FillColor=array(255,255,255);
		}
	}
		
	function AfterData() {
		global $db;
		$tot = "Total =>";
		
		if (!$this->_count) {
			$this->SetFont('Arial','B',9);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(275, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}else{
						
						$this->Cell($this->ColumnWidth[0], 4, "", "LRB", 0, 'C');  
						$this->Cell($this->ColumnWidth[1], 4, $tot, "LRB", 0, 'C');  
						
						$this->Cell($this->ColumnWidth[2], 4, $this->t_male_below1, "1", 0, 'C');
						$this->Cell($this->ColumnWidth[3], 4, $this->t_female_below1, "1", 0, 'C');
						
						$this->Cell($this->ColumnWidth[4], 4, $this->t_male_1to4, "1", 0, 'C');
						$this->Cell($this->ColumnWidth[5], 4, $this->t_female_1to4, "1", 0, 'C');
						
						$this->Cell($this->ColumnWidth[6], 4, $this->t_male_5to9, "1", 0, 'C');
						$this->Cell($this->ColumnWidth[7], 4, $this->t_female_5to9, "1", 0, 'C');
						
						$this->Cell($this->ColumnWidth[8], 4, $this->t_male_10to14, "1", 0, 'C');
						$this->Cell($this->ColumnWidth[9], 4, $this->t_female_10to14, "1", 0, 'C');
						
						$this->Cell($this->ColumnWidth[10], 4, $this->t_male_15to19, "1", 0, 'C');
						$this->Cell($this->ColumnWidth[11], 4, $this->t_female_15to19, "1", 0, 'C');
						
						$this->Cell($this->ColumnWidth[12], 4, $this->t_male_20to44, "1", 0, 'C');
						$this->Cell($this->ColumnWidth[13], 4, $this->t_female_20to44, "1", 0, 'C');
						
						$this->Cell($this->ColumnWidth[14], 4, $this->t_male_45to59, "1", 0, 'C');
						$this->Cell($this->ColumnWidth[15], 4, $this->t_female_45to59, "1", 0, 'C');
						
						$this->Cell($this->ColumnWidth[16], 4, $this->t_male_60up, "1", 0, 'C');
						$this->Cell($this->ColumnWidth[17], 4, $this->t_female_60up, "1", 0, 'C');
						
						$this->Cell($this->ColumnWidth[18], 4, $this->t_male_total, "1", 0, 'C');
						$this->Cell($this->ColumnWidth[19], 4, $this->t_female_total, "1", 0, 'C');
						$this->Cell($this->ColumnWidth[20], 4, $this->t_total, "1", 0, 'C');
						
						$this->Cell($this->ColumnWidth[21], 4, "100%", "LRB", 0, 'C');
						
						$this->Cell($this->ColumnWidth[22], 4, "xxx", "LRB", 0, 'C');
						
				}
		
		$cols = array();
	}  

	function FetchData(){
		global $db;
	
		 $tot_sql = "SELECT SUM(t.total) as total FROM (SELECT SUM(CASE WHEN (p.sex='f' OR p.sex = 'm') then 1 else 0 end) AS total
									FROM care_encounter_diagnosis AS cd 
									INNER JOIN care_icd10_en AS en ON en.diagnosis_code = (SELECT IF(INSTR(cd.code_parent,'.'), SUBSTRING(cd.code_parent, 1, 3), IF(INSTR(cd.code_parent,'/'), SUBSTRING(cd.code_parent, 1, 5),cd.code_parent))) 
									INNER JOIN care_encounter AS e ON e.encounter_nr = cd.encounter_nr 
									LEFT JOIN (SELECT ser.encounter_nr,SUBSTRING(MAX(CONCAT(ser.create_time,ser.result_code)),20) AS result_code 
														FROM seg_encounter_result AS ser 
														INNER JOIN care_encounter AS em ON em.encounter_nr=ser.encounter_nr 
														WHERE (DATE(discharge_date) BETWEEN '".$this->from."' AND '".$this->to."') 
														AND em.encounter_type IN (3,4) 
														AND em.discharge_date IS NOT NULL
														GROUP BY ser.encounter_nr 
														ORDER BY ser.encounter_nr, ser.create_time DESC) AS sr ON sr.encounter_nr = cd.encounter_nr 

									INNER JOIN care_person AS p ON p.pid = e.pid 
									WHERE cd.status NOT IN ('deleted','hidden','inactive','void')
									AND e.status NOT IN ('deleted','hidden','inactive','void') 
									AND e.encounter_type IN (3,4) 
									AND (DATE(e.discharge_date) BETWEEN '".$this->from."' AND '".$this->to."') 
									AND e.discharge_date IS NOT NULL
									AND (sr.result_code = 8)
									GROUP BY cd.code_parent 
									ORDER BY SUM(CASE WHEN (sr.result_code = 8) then 1 else 0 end) DESC LIMIT 20) AS t";
										
		 
		 $tot_result=$db->Execute($tot_sql);    
		 $over_alltotal  = $tot_result->FetchRow(); 
		 
		 $sql_dept="";
		 if ($this->dept_nr){
			if ($this->dept_nr==1)
					#Gynecology
					$sql_dept = " AND (e.current_dept_nr='124' OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='124'))  "; 		
			elseif ($this->dept_nr==2)
					#Medicines
					$sql_dept = " AND (e.current_dept_nr IN (133,154,104) OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='133')OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='154') OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='104')) "; 			
			elseif ($this->dept_nr==3)
					#Obstetrics
					$sql_dept = " AND (e.current_dept_nr='139' OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='139')) "; 			
			elseif ($this->dept_nr==4)
					#Pediatrics
					$sql_dept = " AND (e.current_dept_nr IN (125) OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='125')) "; 			
			elseif ($this->dept_nr==5)
					#Surgery
					$sql_dept = " AND (e.current_dept_nr IN (117,141,136,131,122) OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='117')) "; 							
			elseif ($this->dept_nr==6)
					#ENT
					$sql_dept = " AND (e.current_dept_nr='136' OR e.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='136'))  ";     			
	}        
	
	 $sql = "
SELECT cd.code AS subcode, 
(SELECT IF(INSTR(cd.code_parent,'.'), SUBSTRING(cd.code_parent, 1, 3), IF(INSTR(cd.code_parent,'/'), SUBSTRING(cd.code_parent, 1, 5),cd.code_parent))) AS code, 
en.description AS diagnosis, 
SUM(CASE WHEN p.sex='m' AND (floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<1 OR floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age)) IS NULL) then 1 else 0 end) AS male_below1, 
SUM(CASE WHEN p.sex='f' AND (floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<1 OR floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age)) IS NULL) then 1 else 0 end) AS female_below1, 
SUM(CASE WHEN p.sex='m' AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=1 AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=4 then 1 else 0 end) AS male_1to4, 
SUM(CASE WHEN p.sex='f' AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=1 AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=4 then 1 else 0 end) AS female_1to4, 
SUM(CASE WHEN p.sex='m' AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=5 AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=9 then 1 else 0 end) AS male_5to9, 
SUM(CASE WHEN p.sex='f' AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=5 AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=9 then 1 else 0 end) AS female_5to9, 
SUM(CASE WHEN p.sex='m' AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=10 AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=14 then 1 else 0 end) AS male_10to14, 
SUM(CASE WHEN p.sex='f' AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=10 AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=14 then 1 else 0 end) AS female_10to14, 
SUM(CASE WHEN p.sex='m' AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=15 AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=19 then 1 else 0 end) AS male_15to19, 
SUM(CASE WHEN p.sex='f' AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=15 AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=19 then 1 else 0 end) AS female_15to19, 
SUM(CASE WHEN p.sex='m' AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=20 AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=44 then 1 else 0 end) AS male_20to44, 
SUM(CASE WHEN p.sex='f' AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=20 AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=44 then 1 else 0 end) AS female_20to44, 
SUM(CASE WHEN p.sex='m' AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=45 AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=59 then 1 else 0 end) AS male_45to59, 
SUM(CASE WHEN p.sex='f' AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=45 AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=59 then 1 else 0 end) AS female_45to59, 
SUM(CASE WHEN p.sex='m' AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=60 then 1 else 0 end) AS male_60up, SUM(CASE WHEN p.sex='f' AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=60 then 1 else 0 end) AS female_60up, 
SUM(CASE WHEN p.sex='m' then 1 else 0 end) AS male_total, 
SUM(CASE WHEN p.sex='f' then 1 else 0 end) AS female_total, 

SUM(CASE WHEN (p.sex='f' OR p.sex = 'm') then 1 else 0 end) AS total 

FROM care_encounter_diagnosis AS cd 
INNER JOIN care_icd10_en AS en ON en.diagnosis_code = (SELECT IF(INSTR(cd.code_parent,'.'), SUBSTRING(cd.code_parent, 1, 3), IF(INSTR(cd.code_parent,'/'), SUBSTRING(cd.code_parent, 1, 5),cd.code_parent))) 
INNER JOIN care_encounter AS e ON e.encounter_nr = cd.encounter_nr 
LEFT JOIN (SELECT ser.encounter_nr,SUBSTRING(MAX(CONCAT(ser.create_time,ser.result_code)),20) AS result_code 
					FROM seg_encounter_result AS ser 
					INNER JOIN care_encounter AS em ON em.encounter_nr=ser.encounter_nr 
					WHERE (DATE(discharge_date) BETWEEN '".$this->from."' AND '".$this->to."') 
					AND em.encounter_type IN (3,4) 
					AND em.discharge_date IS NOT NULL
		GROUP BY ser.encounter_nr 
					ORDER BY ser.encounter_nr, ser.create_time DESC) AS sr ON sr.encounter_nr = cd.encounter_nr 

INNER JOIN care_person AS p ON p.pid = e.pid 
WHERE cd.status NOT IN ('deleted','hidden','inactive','void')
AND e.status NOT IN ('deleted','hidden','inactive','void') 
AND e.encounter_type IN (3,4) 
AND (DATE(e.discharge_date) BETWEEN '".$this->from."' AND '".$this->to."') 
AND e.discharge_date IS NOT NULL
AND (sr.result_code = 8)
$sql_dept
GROUP BY cd.code_parent 
ORDER BY SUM(CASE WHEN (sr.result_code = 8) then 1 else 0 end) DESC LIMIT 20";

			#echo "sql = ".$sql;

			$result=$db->Execute($sql);
				if ($result) {
			
					$this->_count = $result->RecordCount();
						$this->Data=array();
				$i=1;  
							$percentage = 0; 
					while ($row=$result->FetchRow()) {
						
									$this->t_male_below1 += $row['male_below1'];
									$this->t_female_below1 += $row['female_below1']; 
									
									$this->t_male_1to4 += $row['male_1to4']; 
									$this->t_female_1to4 += $row['female_1to4'];
									 
									$this->t_male_5to9 += $row['male_5to9']; 
									$this->t_female_5to9 += $row['female_5to9']; 
									
									$this->t_male_10to14 += $row['male_10to14']; 
									$this->t_female_10to14 += $row['female_10to14']; 
									
									$this->t_male_15to19 += $row['male_15to19']; 
									$this->t_female_15to19 += $row['female_15to19']; 
									
									$this->t_male_20to44 += $row['male_20to44']; 
									$this->t_female_20to44 += $row['female_20to44']; 
									
									$this->t_male_45to59 += $row['male_45to59']; 
									$this->t_female_45to59 += $row['female_45to59']; 
									
									$this->t_male_60up += $row['male_60up']; 
									$this->t_female_60up += $row['female_60up']; 
									
									$this->t_male_total += $row['male_total']; 
									$this->t_female_total += $row['female_total']; 
									$this->t_total += $row['total'];
									
									$percentage = ($row['total'] / $over_alltotal['total']) * 100;
									$percentage = round($percentage,2);
									
										$this->Data[]=array(
															$i,   
													$row['diagnosis'],
													$row['male_below1'],
													$row['female_below1'],
													$row['male_1to4'],
													$row['female_1to4'],
													$row['male_5to9'],
													$row['female_5to9'],
													$row['male_10to14'],
													$row['female_10to14'],
													$row['male_15to19'],
													$row['female_15to19'],
													$row['male_20to44'],
													$row['female_20to44'],
													$row['male_45to59'],
													$row['female_45to59'],
													$row['male_60up'],
													$row['female_60up'],
													$row['male_total'],
													$row['female_total'],
													$row['total'],
													$percentage."%",
													$row['code']
										 );
											$i++;
											$percentage = 0;
					}
			
			}
			else {
				print_r($sql);
				print_r($db->ErrorMsg());
				exit;
				# Error    
			}  
	}
}

$rep = new RepGen_CausesMortality($_GET['from'], $_GET['to'], $_GET['dept_nr_sub']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>
