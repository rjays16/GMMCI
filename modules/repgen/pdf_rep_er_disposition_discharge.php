<?php
#edited by VAN 03-10-2009
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

class RepGen_ER_Disposition_Discharge extends RepGen{
var $colored = TRUE;
var $from, $to;

	 function RepGen_ER_Disposition_Discharge ($from, $to, $sclass) {
		global $db;
		$this->RepGen("MEDICAL RECORDS: ER DISPOSITION ON DISCHARGE");
		$this->ColumnWidth = array(40,25,25,25,25,25,20,20,20,20,30);
		$this->RowHeight = 5;
		$this->TextHeight = 5;
		$this->TextPadding = 0.2;
		$this->Alignment = array('L', 'R','R','R','R','R','R','R','R','R','R','R');
		$this->PageOrientation = "L";
		$this->NoWrap = FALSE;
		$this->LEFTMARGIN = 2;

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
		if ($this->colored)	$this->SetDrawColor(0xDD);

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

		$this->Cell($total_w,4,'ER DISPOSITION ON DISCHARGE',$border2,1,'C');
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

		$this->Cell(40, 4, "", "TLR", 0, 'C');
		$this->Cell(235, 4, "DISPOSITION", "1", 1, 'C');

		$this->Cell(40, 4, "", "LR", 0, 'C');

		$this->Cell(25, 4, "", "LR", 0, 'C');
		$this->Cell(25, 4, "", "LR", 0, 'C');
		$this->Cell(25, 4, "", "LR", 0, 'C');
		$this->Cell(25, 4, "", "LR", 0, 'C');
		$this->Cell(25, 4, "", "LR", 0, 'C');

		$this->Cell(80, 4, "DEATHS", "LR", 0, 'C');
		$this->Cell(30, 4, "", "LR", 1, 'C');

		$this->Cell(40, 4, "TYPE OF SERVICES", "LR", 0, 'C');

		#er disposition
		$this->Cell(25, 4, "ABSCONDED", "LR", 0, 'C');
		$this->Cell(25, 4, "HAMA", "LR", 0, 'C');
		$this->Cell(25, 4, "THOC", "LR", 0, 'C');
		$this->Cell(25, 4, "DISCHARGE", "LR", 0, 'C');
		$this->Cell(25, 4, "TOTAL", "LR", 0, 'C');


		$this->Cell(20, 4, "", "1", 0, 'C');
		$this->Cell(40, 4, "ERWD", "1", 0, 'C');
		$this->Cell(20, 4, "", "1", 0, 'C');

		$this->Cell(30, 4, "OVER-ALL TOTAL", "LR", 1, 'C');
		$this->Cell(40, 4, "", "BLR", 0, 'C');

		$this->Cell(25, 4, "", "LR", 0, 'C');
		$this->Cell(25, 4, "", "LR", 0, 'C');
		$this->Cell(25, 4, "", "LR", 0, 'C');
		$this->Cell(25, 4, "", "LR", 0, 'C');
		$this->Cell(25, 4, "", "LR", 0, 'C');

		#died
		$this->Cell(20, 4, "DOA", "1", 0, 'C');
		$this->Cell(20, 4, "<48", "1", 0, 'C');
		$this->Cell(20, 4, ">=48", "1", 0, 'C');
		$this->Cell(20, 4, "TOTAL", "1", 0, 'C');

		$this->Cell(30, 4, "", "BLR", 0, 'C');

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

		if (!$this->_count) {
			$this->SetFont('Arial','B',9);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(0, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}

		$cols = array();
	}

	function FetchData(){
	 global $db;

	 if ($this->from) {
				$where[]="DATE(discharge_date) BETWEEN '".$this->from."' AND '".$this->to."'";
		 }

		 if ($where)
					 $whereSQL = "AND (".implode(") AND (",$where).")";

	$sql = "SELECT d.name_formal AS Type_Of_Service,
							SUM(CASE WHEN p.disp_code = 5 then 1 else 0 end) AS absconded,
							SUM(CASE WHEN p.disp_code = 4 then 1 else 0 end) AS hama,
							SUM(CASE WHEN ((p.disp_code = 3) || (p.disp_code = 1)) then 1 else 0 end) AS transferred,
							SUM(CASE WHEN p.disp_code = 2 then 1 else 0 end) AS discharge,
							SUM(CASE WHEN ((p.disp_code = 5)||(p.disp_code = 4)
															||((p.disp_code = 3) || (p.disp_code = 1))||(p.disp_code = 2)) then 1 else 0 end) AS total_disp,

							SUM(CASE WHEN is_DOA=1 then 1 else 0 end) AS doa,
							SUM(CASE WHEN (p.result_code=8
									AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),p.age))<48)
									THEN 1 ELSE 0 END) AS deathbelow48,
							SUM(CASE WHEN (p.result_code=8
									AND floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),p.age))>=48)
									THEN 1 ELSE 0 END) AS deathabove48,
							SUM(CASE WHEN ((is_DOA=1) OR (p.result_code=8
									AND ((floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),p.age))<48)
									OR (floor(IF(fn_calculate_age(p.death_date,p.date_birth),fn_get_age(p.death_date,p.date_birth),p.age))>=48))))
									THEN 1 ELSE 0 END) AS total_death

				FROM seg_rep_medrec_patient_icd_tbl AS p
				LEFT JOIN care_department AS d ON d.nr=p.current_dept_nr
				WHERE  p.encounter_type IN (1)
				AND p.encounter_class_nr IN (1)
				AND p.encounter_status NOT IN ('direct_admission')
				AND p.discharge_date IS NOT NULL
				$whereSQL
				GROUP BY d.name_formal
				ORDER BY count(p.encounter_nr) DESC";

			#echo "sql = ".$sql;

			$result=$db->Execute($sql);
				if ($result) {

					$this->_count = $result->RecordCount();
						$this->Data=array();

					while ($row=$result->FetchRow()) {

				$total =  $row['total_disp'] +  $row['total_death'];
				$this->Data[]=array(
							$row['Type_Of_Service'],
							$row['absconded'],
							$row['hama'],
							$row['transferred'],
							$row['discharge'],
							$row['total_disp'],
							$row['doa'],
							$row['deathbelow48'],
							$row['deathabove48'],
							$row['total_death'],
							$total
				 );

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

$rep = new RepGen_ER_Disposition_Discharge($_GET['from'], $_GET['to'],$_GET['sclass']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>
