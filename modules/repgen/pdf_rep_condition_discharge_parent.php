<?php
#edited by VAN 03-10-2009
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

require_once($root_path.'include/care_api_classes/class_department.php');

class RepGen_ResultOfTreatment_Discharge extends RepGen{
var $colored = TRUE;
var $from, $to;
var $dept_nr;
var $imp_male,$imp_female,$imp_total,$unimp_male,$unimp_female,$unimp_total,$total_f,$total_m;

	 function RepGen_ResultOfTreatment_Discharge ($from, $to, $dept_nr, $sclass) {
		global $db;
		$this->RepGen("MEDICAL RECORDS: LEADING CAUSES OF DISCHARGES OF MAJOR SERVICES");

		$this->ColumnWidth = array(15, 65, 13,13,14, 13,13,14, 15,15, 20);
		$this->RowHeight = 5;
		$this->TextHeight = 5;
		$this->TextPadding = 0.2;
		$this->Alignment = array('L', 'L', 'C','C','C', 'C','C','C', 'C','C', 'C' );
		$this->PageOrientation = "P";
		$this->NoWrap = FALSE;
		$this->LEFTMARGIN = 2;

		$this->SetAutoPageBreak(FALSE);
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
		if ($this->colored)	$this->SetDrawColor(0xDD);

	}


	function Header() {
		global $root_path, $db;
		$objInfo = new Hospital_Admin();
		$dept_obj = new Department();

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
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',25,8,20);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}',0,0,'R');
		$this->SetFont("Arial","I","9");
		$total_w = 200;
		#$this->Cell(50,4);
		$this->Ln(1);
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		#$this->Cell(50,4);
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","10");
		#$this->Cell(50,4);
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		#$this->Cell(50,4);
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
		$this->Ln(4);
		$this->SetFont('Arial','B',12);
		#$this->Cell(50,5);

		if ($this->dept_nr){
			#$deptinfo = $dept_obj->getDeptAllInfo($this->dept_nr);
			#$deptname = $deptinfo['name_formal'];
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

		$label = "";
		if ($this->sclass_label!='All')
			$label = '('.strtoupper($this->sclass_label).')';

		$this->Cell($total_w,4,'LEADING CAUSES OF DISCHARGES OF MAJOR SERVICES',$border2,1,'C');
		$this->Cell($total_w,4,mb_strtoupper($deptname),$border2,1,'C');
		$this->SetFont('Arial','B',9);
		#$this->Cell(50,5);

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

		$this->Cell(15, 4, "", "TLR", 0, 'C');
		$this->Cell(65, 4, "", "TLR", 0, 'C');
		$this->Cell(110, 4, "Condition on Discharge", "1", 0, 'C');
		$this->Cell(20, 4, "", "TLR", 1, 'C');

		$this->Cell(15, 4, "", "LR", 0, 'C');
		$this->Cell(65, 4, "Discharge Diagnosis (".$this->sclass_label.")", "LR", 0, 'C');

		$this->Cell(40, 4, "Improved", "1", 0, 'C');
		$this->Cell(40, 4, "Unimproved", "1", 0, 'C');
		$this->Cell(30, 4, "Total", "1", 0, 'C');

		$this->Cell(20, 4, "ICD-10 Code", "LR", 1, 'C');

		$this->Cell(15, 4, "", "BLR", 0, 'C');
		$this->Cell(65, 4, "", "BLR", 0, 'C');

		#improved
		$this->Cell(13, 4, "M", "1", 0, 'C');
		$this->Cell(13, 4, "F", "1", 0, 'C');
		$this->Cell(14, 4, "Total", "1", 0, 'C');

		#unimproved
		$this->Cell(13, 4, "M", "1", 0, 'C');
		$this->Cell(13, 4, "F", "1", 0, 'C');
		$this->Cell(14, 4, "Total", "1", 0, 'C');

		#total
		$this->Cell(15, 4, "M", "1", 0, 'C');
		$this->Cell(15, 4, "F", "1", 0, 'C');

		$this->Cell(20, 4, "", "BLR", 0, 'C');

		$this->Ln();
	}

	function Footer(){
		$this->SetY(-23);
		$this->SetFont('Arial','I',8);
		#$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
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
			$this->Cell(210, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}else{

			$this->Cell(15, 4, "", "BTLR", 0, 'C');
			$this->Cell(65, 4, "Total", "BTLR", 0, 'C');

			$this->Cell(13, 4, $this->imp_male, "1", 0, 'C');
			$this->Cell(13, 4, $this->imp_female, "1", 0, 'C');
			$this->Cell(14, 4, $this->imp_total, "1", 0, 'C');

			$this->Cell(13, 4, $this->unimp_male, "1", 0, 'C');
			$this->Cell(13, 4, $this->unimp_female, "1", 0, 'C');
			$this->Cell(14, 4, $this->unimp_total, "1", 0, 'C');

			$this->Cell(15, 4, $this->total_m, "1", 0, 'C');
			$this->Cell(15, 4, $this->total_f, "1", 0, 'C');

			$this->Cell(20, 4, "", "BLR", 0, 'C');
		}

		$cols = array();
	}

	function FetchData(){
	 global $db;
	 $dept_obj = new Department();

	 /*if ($this->from) {
				$where[]="DATE(discharge_date) BETWEEN '".$this->from."' AND '".$this->to."'";
		 }

		 if ($where)
					 $whereSQL = "AND (".implode(") AND (",$where).")";*/

	 $sql_dept="";
		 if ($this->dept_nr){
			if ($this->dept_nr==0)
						#all department
						$sql_dept = " ";
				elseif ($this->dept_nr==1)
						#Gynecology
						$sql_dept = " AND (p.current_dept_nr='124' OR p.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='124'))  ";
				elseif ($this->dept_nr==2)
						#Medicines
						$sql_dept = " AND (p.current_dept_nr IN (133,154,104) OR p.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='133')OR p.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='154') OR p.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='104')) ";
				elseif ($this->dept_nr==3)
						#Obstetrics
						$sql_dept = " AND (p.current_dept_nr='139' OR p.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='139')) ";
				elseif ($this->dept_nr==4)
						#Pediatrics
						$sql_dept = " AND (p.current_dept_nr IN (125) OR p.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='125')) ";
				elseif ($this->dept_nr==5)
						#Surgery
						$sql_dept = " AND (p.current_dept_nr IN (117,141,136,131,122) OR p.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='117')) ";
				elseif ($this->dept_nr==6)
						#ENT
						$sql_dept = " AND (p.current_dept_nr='136' OR p.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr='136'))  ";
	 }


			$sql = "SELECT p.icd AS subcode,
								(SELECT IF(INSTR(p.code_parent,'.'), SUBSTRING(p.code_parent, 1, 3), IF(INSTR(p.code_parent,'/'),
														SUBSTRING(p.code_parent, 1, 5),p.code_parent))) AS code,
								p.icd_desc AS diagnosis,
								SUM(CASE WHEN (p.result_code = 5 OR p.result_code = 6) AND p.disp_code=7 AND p.sex = 'm' THEN 1 ELSE 0 END) AS Improved_M,
								SUM(CASE WHEN (p.result_code = 5 OR p.result_code = 6) AND p.disp_code=7 AND p.sex = 'f' THEN 1 ELSE 0 END) AS Improved_F,
								SUM(CASE WHEN p.result_code = 7 AND p.disp_code=7 AND p.sex = 'm' THEN 1 ELSE 0 END) AS Unimproved_M,
								SUM(CASE WHEN p.result_code = 7 AND p.disp_code=7 AND p.sex = 'f' THEN 1 ELSE 0 END) AS Unimproved_F
								FROM seg_rep_medrec_patient_icd_tbl AS p
								WHERE DATE(p.discharge_date) BETWEEN  '".$this->from."' AND '".$this->to."'
								".$this->type_cond."
								AND p.encounter_type IN (3,4)
								AND p.discharge_date IS NOT NULL
								$sql_dept
								GROUP BY p.code_parent
								ORDER BY SUM(CASE WHEN (p.result_code = 5 OR p.result_code = 6 OR p.result_code = 7)
															AND p.disp_code=7 THEN 1 ELSE 0 END) DESC LIMIT 50";

			#echo "sql = ".$sql;

			$result=$db->Execute($sql);
			if ($result) {

				$this->_count = $result->RecordCount();
				$this->Data=array();
				$i=1;

				$this->imp_male = 0;
				$this->imp_female = 0;
				$this->imp_total = 0;

				$this->unimp_male = 0;
				$this->unimp_female = 0;
				$this->unimp_total = 0;

				$this->total_m = 0;
				$this->total_f = 0;

				while ($row=$result->FetchRow()) {

						$total_improved =  $row['Improved_M'] + $row['Improved_F'];
						$total_unimproved =  $row['Unimproved_M'] + $row['Unimproved_F'];

						$total_female =  $row['Improved_F'] + $row['Unimproved_F'];
						$total_male =  $row['Improved_M'] + $row['Unimproved_M'];

						$total =  $total_male + $total_female;

						$this->imp_male += $row['Improved_M'];
						$this->imp_female += $row['Improved_F'];
						$this->imp_total = $this->imp_total + ($row['Improved_M'] + $row['Improved_F']);

						$this->unimp_male += $row['Unimproved_M'];
						$this->unimp_female += $row['Unimproved_F'];
						$this->unimp_total =  $this->unimp_total + ($row['Unimproved_M'] + $row['Unimproved_F']);

						$this->total_m = $this->total_m + $total_male;
						$this->total_f = $this->total_f + $total_female;

						if ($row['diagnosis'])
							$diagnosis = $row['diagnosis'];
						else
							$diagnosis = $row['code'];

						$this->Data[]=array(
									$i,
									$row['diagnosis'],
									$row['Improved_M'],
									$row['Improved_F'],
									$total_improved,
									$row['Unimproved_M'],
									$row['Unimproved_F'],
									$total_unimproved,
									$total_male,
									$total_female,
							$row['code']
						 );
						$i++;
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

$rep = new RepGen_ResultOfTreatment_Discharge($_GET['from'], $_GET['to'], $_GET['dept_nr_sub'],$_GET['sclass']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>