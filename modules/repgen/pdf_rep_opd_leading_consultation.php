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
var $t_male_45to64, $t_female_45to64, $t_male_65up, $t_female_65up;
var $t_male_total, $t_female_total, $t_total;
var $dept_nr;

	 function RepGen_CausesMortality ($from, $to, $dept_nr, $sclass) {
		global $db;
		$this->RepGen("MEDICAL RECORDS: LEADING CAUSES OF CONSULTATIONS AT OPD");

		$this->ColumnWidth = array(7,50, 10,10, 10,10, 10,10, 10,10, 10,10, 10,10, 10,10, 10,10, 10,10,10, 15,13);
		$this->RowHeight = 5;
		$this->TextHeight = 5;
		$this->TextPadding = 0.2;
		$this->Alignment = array('L','L', 'C','C', 'C','C', 'C','C', 'C','C', 'C','C', 'C','C', 'C','C', 'C','C', 'C','C','C', 'C','C', 'C');
		$this->PageOrientation = "L";
		$this->NoWrap = FALSE;
		$this->LEFTMARGIN = 2;
		$this->dept_nr = $dept_nr;

		if ($sclass=='primary'){
			$this->type_cond = " AND p.type_nr='1' ";
			$this->sclass_label = "Primary";
		}elseif ($sclass=='secondary'){
			$this->type_cond = " AND p.type_nr='0' ";
			$this->sclass_label = "Secondary";
		}elseif ($sclass=='all'){
			$this->type_cond = "";
			$this->sclass_label = "All";
		}

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
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',30,8,20);
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

		$deptname = mb_strtoupper($deptname);

		$label = "";
		if ($this->sclass_label!='All')
			$label = '('.strtoupper($this->sclass_label).')';

		$this->Cell($total_w,4,'LEADING CAUSES OF CONSULTATIONS AT OPD '.$label,$border2,1,'C');
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
		$this->Cell($this->ColumnWidth[1], 4, "Diagnosis", "LR", 0, 'C');

		$this->Cell($this->ColumnWidth[2]+$this->ColumnWidth[3], 4, "<1", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[4]+$this->ColumnWidth[5], 4, "1-4", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[6]+$this->ColumnWidth[7], 4, "5-9", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[8]+$this->ColumnWidth[9], 4, "10-14", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[10]+$this->ColumnWidth[11], 4, "15-19", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[12]+$this->ColumnWidth[13], 4, "20-44", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[14]+$this->ColumnWidth[15], 4, "45-64", "1", 0, 'C');
		$this->Cell($this->ColumnWidth[16]+$this->ColumnWidth[17], 4, ">=65", "1", 0, 'C');

		$this->Cell($this->ColumnWidth[18]+$this->ColumnWidth[19]+$this->ColumnWidth[20], 4, "Total", "LR", 0, 'C');

		$this->Cell($this->ColumnWidth[21], 4, "% of", "LR", 0, 'C');

		$this->Cell($this->ColumnWidth[22], 4, "ICD10", "LR", 1, 'C');

		$this->Cell($this->ColumnWidth[0], 4, "", "LRB", 0, 'C');
		$this->Cell($this->ColumnWidth[1], 4, $label, "LRB", 0, 'C');

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

						$this->Cell($this->ColumnWidth[14], 4, $this->t_male_45to64, "1", 0, 'C');
						$this->Cell($this->ColumnWidth[15], 4, $this->t_female_45to64, "1", 0, 'C');

						$this->Cell($this->ColumnWidth[16], 4, $this->t_male_65up, "1", 0, 'C');
						$this->Cell($this->ColumnWidth[17], 4, $this->t_female_65up, "1", 0, 'C');

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

		 $tot_sql = "SELECT SUM(CASE WHEN (p.sex='f' OR p.sex = 'm') THEN 1 ELSE 0 END) AS total
									FROM seg_rep_medrec_patient_icd_tbl AS p
									WHERE p.encounter_type IN (2)
									AND DATE(p.encounter_date) BETWEEN '".$this->from."' AND '".$this->to."'
									".$this->type_cond;


		 $tot_result=$db->Execute($tot_sql);
		 $over_alltotal  = $tot_result->FetchRow();

		 $sql = "
						SELECT p.icd AS subcode,
						(SELECT IF(INSTR(p.code_parent,'.'),
												SUBSTRING(p.code_parent, 1, 3),
												IF(INSTR(p.code_parent,'/'),
												SUBSTRING(p.code_parent, 1, 5),p.code_parent))) AS code,
						p.icd_desc AS diagnosis,
						SUM(CASE WHEN p.sex='m'
											AND (floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<1
											OR floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age)) IS NULL)
								 THEN 1 ELSE 0 END) AS male_below1,
						SUM(CASE WHEN p.sex='f'
											AND (floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<1
											OR floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age)) IS NULL)
								 THEN 1 ELSE 0 END) AS female_below1,
						SUM(CASE WHEN p.sex='m'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=1
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=4
								 THEN 1 ELSE 0 END) AS male_1to4,
						SUM(CASE WHEN p.sex='f'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=1
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=4
								 THEN 1 ELSE 0 END) AS female_1to4,
						SUM(CASE WHEN p.sex='m'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=5
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=9
								 THEN 1 ELSE 0 END) AS male_5to9,
						SUM(CASE WHEN p.sex='f'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=5
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=9
								 THEN 1 ELSE 0 END) AS female_5to9,
						SUM(CASE WHEN p.sex='m'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=10
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=14
								 THEN 1 ELSE 0 END) AS male_10to14,
						SUM(CASE WHEN p.sex='f'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=10
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=14
											then 1 else 0 end) AS female_10to14,
						SUM(CASE WHEN p.sex='m'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=15
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=19
								 THEN 1 ELSE 0 END) AS male_15to19,
						SUM(CASE WHEN p.sex='f'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=15
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=19
								 THEN 1 ELSE 0 END) AS female_15to19,
						SUM(CASE WHEN p.sex='m'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=20
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=44
								 THEN 1 ELSE 0 END) AS male_20to44,
						SUM(CASE WHEN p.sex='f'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=20
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=44
								 THEN 1 ELSE 0 END) AS female_20to44,
						SUM(CASE WHEN p.sex='m'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=45
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=64
								 THEN 1 ELSE 0 END) AS male_45to64,
						SUM(CASE WHEN p.sex='f'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=45
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))<=64
								 THEN 1 ELSE 0 END) AS female_45to64,
						SUM(CASE WHEN p.sex='m'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=65
								 THEN 1 ELSE 0 END) AS male_65up,
						SUM(CASE WHEN p.sex='f'
											AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),age))>=65
								 THEN 1 ELSE 0 END) AS female_65up,
						SUM(CASE WHEN p.sex='m' THEN 1 ELSE 0 END) AS male_total,
						SUM(CASE WHEN p.sex='f' THEN 1 ELSE 0 END) AS female_total,

						SUM(CASE WHEN (p.sex='f' OR p.sex = 'm') THEN 1 ELSE 0 END) AS total

						FROM seg_rep_medrec_patient_icd_tbl AS p
						WHERE p.encounter_type IN (2)
						AND DATE(p.encounter_date) BETWEEN '".$this->from."' AND '".$this->to."'
						".$this->type_cond."
						GROUP BY p.icd
						ORDER BY count(p.icd) DESC ";

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

									$this->t_male_45to64 += $row['male_45to64'];
									$this->t_female_45to64 += $row['female_45to64'];

									$this->t_male_65up += $row['male_65up'];
									$this->t_female_65up += $row['female_65up'];

									$this->t_male_total += $row['male_total'];
									$this->t_female_total += $row['female_total'];
									$this->t_total += $row['total'];

									$percentage = ($row['total'] / $over_alltotal['total']) * 100;
									$percentage = number_format(round($percentage,2),2);

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
													$row['male_45to64'],
													$row['female_45to64'],
													$row['male_65up'],
													$row['female_65up'],
													$row['male_total'],
													$row['female_total'],
													$row['total'],
													$percentage."%",
													$row['subcode']
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

$rep = new RepGen_CausesMortality($_GET['from'], $_GET['to'], $_GET['dept_nr_sub'],$_GET['sclass']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>