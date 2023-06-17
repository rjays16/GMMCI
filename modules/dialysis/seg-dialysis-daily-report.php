<?php
//created by CHA, July 29, 2010
//daily report for dialysis transaction
//same with OPD Daily Transaction Report
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_department.php');

class Dialysis_Daily_Report extends RepGen {

	var $date_from;
	var $date_to;
	var $time_from;
	var $time_to;
	var $total_width=0;

	function Dialysis_Daily_Report($dateFrm, $dateTo, $timeFrm, $timeto)
	{
		global $db;
		$this->RepGen("DIALYSIS DAILY TRANSACTION REPORT");
		$this->Headers = array(
				'',
				'Patient ID',
				'Fullname',
				'Time',
				'Age',
				'Gender',
				'Address',
				'ICD',
				'Physician'
			);
		$this->colored = TRUE;
		$this->ColumnWidth = array(5,30,50,20,20,20,50,30,50);
		$this->RowHeight = 6;
		$this->Alignment = array('L','C','L','C','C','C','L','L','L');
		$this->PageOrientation = "L";
		$this->SetMargins(1,1,1);
		$this->total_width = array_sum($this->ColumnWidth);
		$this->NoWrap=FALSE;

		$this->date_from = date('y-m-d',strtotime($dateFrm));
		$this->date_to = date('y-m-d', strtotime($dateTo));
		$this->time_from = $timeFrm;
		$this->time_to = $timeto;

		if ($this->colored) $this->SetDrawColor(0xDD);
	}

	function Header()
	{
		$this->SetFont('Arial','',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("m/d/Y h:iA"),0,0,'R');
		$this->Ln(2);
		$this->SetFont("Arial","B","12");
		$this->Cell(0,4,strtoupper('Dialysis Daily Transactions'),0,1,'C');
		$this->Ln();
		$this->Cell(0,4,date("M d, Y",strtotime($this->date_from))."  ".date("h:i A",strtotime($this->time_from))." - ".date("M d, Y",strtotime($this->date_to))."  ".date("h:i A",strtotime($this->time_to)),0,1,'C');
		$this->Cell(0,4,'Number of Records : '.$this->_count,0,1,'L');
		//parent::Header();
		$this->Ln();
		$this->SetTextColor(0);
		$row=5;
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
		//$this->Cell(0,0,0, 0,0,c,)
		$this->Ln();
	}

	function BeforeData()
	{
		$this->FONTSIZE = 9;
		if ($this->colored) {
				$this->DrawColor = array(255,255,255);
		}
	}

	/*function BeforeCellRender()
	{
			$this->FONTSIZE = 8;
			if ($this->colored) {
					if (($this->RENDERPAGEROWNUM%2)>0)
							$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
					else
							$this->RENDERCELL->FillColor=array(255,255,255);
			}
	}*/

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

	function FetchData()
	{
		global $db;
		if (empty($this->date_to)) $end_date="NOW()";
		else $end_date=$this->date_to;
		if (empty($this->date_from)) $start_date="NOW()";
		else
		$start_date=$this->date_from;

		$grp_sql = " GROUP BY ce.pid ";
		$order_sql = " ORDER BY encounter_date ";

		$sql =
			"SELECT distinct cp.pid, cd.name_formal,
				CONCAT(IFNULL(name_last,''),', ',IFNULL(name_first,''),' ',IFNULL(name_middle,'')) AS fullname,
				CAST(encounter_date as DATE) as consult_date,
				CAST(encounter_date AS TIME) AS consult_time,
				fn_get_age(CAST(encounter_date AS date), CAST(date_birth AS DATE)) AS age,
				UPPER(sex) AS p_sex, addr_str, cd.id,
				cp.street_name,	sb.brgy_name, sm.mun_name, sm.zipcode, sp.prov_name, ce.encounter_nr,
					fn_get_icd_encounter(ce.encounter_nr) AS icd_code,
					fn_get_personell_name(fn_get_icd_dr_encounter(ce.encounter_nr)) AS diagnosing_clinician

			FROM care_encounter AS ce
				INNER JOIN care_person AS cp ON ce.pid = cp.pid
					LEFT JOIN care_department AS cd ON ce.current_dept_nr = cd.nr
				LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
				LEFT JOIN seg_municity AS sm ON sm.mun_nr=cp.mun_nr
				LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
				LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
			WHERE DATE(ce.encounter_date) BETWEEN '$start_date' AND '$end_date'
				AND ce.encounter_type IN (5)
				AND ce.status NOT IN ('deleted','hidden','inactive','void')";

		$sql .= " $grp_sql $order_sql";

		//echo $sql;

		$result=$db->Execute($sql);
		$this->_count = $result->RecordCount();

		if ($result!==FALSE) {
			$this->Data=array();
			$i=1;

			 while ($row=$result->FetchRow())
			 {
				if (trim($row['street_name'])){
						if (trim($row["brgy_name"])!="NOT PROVIDED")
							$street_name = trim($row['street_name']).", ";
						else
							$street_name = trim($row['street_name']).", ";
				}else{
						$street_name = "";
				}


				if ((!(trim($row["brgy_name"]))) || (trim($row["brgy_name"])=="NOT PROVIDED"))
					$brgy_name = "";
				else
					$brgy_name  = trim($row["brgy_name"]).", ";

				if ((!(trim($row["mun_name"]))) || (trim($row["mun_name"])=="NOT PROVIDED"))
					$mun_name = "";
				else{
					if ($brgy_name)
						$mun_name = trim($row["mun_name"]);
					else
						$mun_name = trim($row["mun_name"]);
				}

				if ((!(trim($row["prov_name"]))) || (trim($row["prov_name"])=="NOT PROVIDED"))
					$prov_name = "";
				else
					$prov_name = trim($row["prov_name"]);

				if(stristr(trim($row["mun_name"]), 'city') === FALSE){
					if ((!empty($row["mun_name"]))&&(!empty($row["prov_name"]))){
						if ($prov_name!="NOT PROVIDED")
							$prov_name = ", ".trim($prov_name);
						else
							$prov_name = trim($prov_name);
					}else{
						$prov_name = "";
					}
				}else
					$prov_name = "";

				$addr = trim($street_name).trim($brgy_name).trim($mun_name).trim($prov_name);

									if (stristr($row['age'],'years')){
											$age = substr($row['age'],0,-5);
											$age = floor($age).' y';
									}elseif (stristr($row['age'],'year')){
											$age = substr($row['age'],0,-4);
											$age = floor($age).' y';
									}elseif (stristr($row['age'],'months')){
											$age = substr($row['age'],0,-6);
											$age = floor($age).' m';
									}elseif (stristr($row['age'],'month')){
											$age = substr($row['age'],0,-5);
											$age = floor($age).' m';
									}elseif (stristr($row['age'],'days')){
											$age = substr($row['age'],0,-4);

											if ($age>30){
													$age = $age/30;
													$label = 'm';
											}else
													$label = 'd';

											$age = floor($age).' '.$label;
									}elseif (stristr($row['age'],'day')){
											$age = substr($row['age'],0,-3);
											$age = floor($age).' d';
									}

					$this->Data[]=array(
						$i,
						$row['pid'],
						trim($row['fullname']),
						date("h:i A",strtotime($row['consult_time'])),
						$age,
						strtoupper($row['p_sex']),
						trim($addr),
						$row['icd_code'],
						$row['diagnosing_clinician']
					);
					$i++;
			}
		}
		else {
			echo "error:".$db->ErrorMsg();
		}
	}
}

$rep = new Dialysis_Daily_Report($_GET["date_from"], $_GET["date_to"], $_GET["time_from"], $_GET["time_to"]);
$rep->AliasNbPages();
$rep->FetchData();
//ini_set('memory_limit', '2048M');
$rep->Report();
?>
