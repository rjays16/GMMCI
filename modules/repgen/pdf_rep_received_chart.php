<?php
/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/
#edited by VAN 06-11-09
#add a function in mysql
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

include_once($root_path.'include/care_api_classes/class_department.php');
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');
//require($root_path.'/modules/repgen/repgen.inc.php');

require($root_path.'/modules/repgen/themes/dmc/dmc.php');

class RepGen_OPD_Trans extends DMCRepGen {
//class RepGen_OPD_Trans extends RepGen {
	var $from_date;
	var $to_date;
	var $dept_nr;
	var $from_time;
	var $to_time;
		var $OB_array;
	var $orderby;

	function RepGen_OPD_Trans($from, $to, $dept_nr) {
		global $db;

		$this->dept_nr = $dept_nr;

		$this->DMCRepGen("REPORT OF RECEIVED PATIENT's CHART", "L", "Letter", $db, TRUE);

		$this->Caption = "Report of Received Patient's Chart";

		$this->SetAutoPageBreak(TRUE,2);
		$this->LEFTMARGIN=0.1;
		$this->DEFAULT_TOPMARGIN = 1;

		$this->ColumnWidth = array(10,20,55,17,10,15,75,40,20);
		$this->Columns = 9;

		$this->TotalWidth = array_sum($this->ColumnWidth);

		$this->ColumnLabels = array(
				'',
				'Patient ID',
				'Fullname',
				'Time',
				'Age',
				'Gender',
				'Address',
				'Department',
				'Received Date'
			);


		$this->RowHeight = 4.5;
		$this->TextHeight = 3.5;

		$this->Alignment = array('L','L','L','L','C','C','L','L','L');

		$this->PageOrientation = "L";

		if ($from) $this->from=date("Y-m-d",strtotime($from));
		if ($to) $this->to=date("Y-m-d",strtotime($to));


		$this->NoWrap = FALSE;

	}

	function Header() {
		$this->SetFont('Arial','',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("m/d/Y h:iA"),0,0,'R');
		$this->Ln(2);
		$this->SetFont("Arial","B","12");
		$this->Cell(0,4,'Report of Received Patient\'s Chart',$border2,1,'C');

		if ($this->from==$this->to)
			$this->Cell(0,4,'Received As of '.date("m/d/Y",strtotime($this->from)),$border2,1,'C');
		else
			$this->Cell(0,4,'Received from '.date("m/d/Y",strtotime($this->from))." - ".date("m/d/Y",strtotime($this->to)),$border2,1,'C');

		$this->Cell(0,4,'Number of Records : '.$this->_count,$border2,1,'L');
		#$this->Ln(1);
		$from_dt=strtotime($this->from_date);
		$to_dt=strtotime($this->to_date);
		#$this->SetFont("Arial","","8");
		if (!empty($this->from_date) && !empty($this->to_date))
		$this->Cell(0,5,
									sprintf('%s-%s',date("F j, Y",$from_dt),date("F j, Y",$to_dt)),
									$border2,1,'C');

		$this->Ln(1);

		parent::Header();

	}

	function BeforeData() {
				$this->FONTSIZE = 9;
				if ($this->colored) {
						#$this->DrawColor = array(0xDD,0xDD,0xDD);
						$this->DrawColor = array(255,255,255);
				}
		}


	function FetchData() {

		if (empty($this->to)) $end_date="NOW()";
		else $end_date=$this->to;

		if (empty($this->from)) $start_date="NOW()";
		else $start_date=$this->from;

		if ($this->dept_nr)
			$sql_dept = " AND ce.current_dept_nr='".$this->dept_nr."'";


		$sql ="SELECT distinct cp.pid, cd.name_formal,
							CONCAT(IFNULL(name_last,''),', ',IFNULL(name_first,''),' ',IFNULL(name_middle,'')) AS fullname,
							CAST(encounter_date as DATE) as consult_date,
							CAST(encounter_date AS TIME) AS consult_time,
							fn_get_age(CAST(encounter_date AS date), CAST(date_birth AS DATE)) AS age,
							UPPER(sex) AS p_sex, addr_str, cd.id,
							cp.street_name,	sb.brgy_name, sm.mun_name, sm.zipcode, sp.prov_name, ce.encounter_nr,
							ce.received_date

						FROM care_encounter AS ce
							INNER JOIN care_person AS cp ON ce.pid = cp.pid
								LEFT JOIN care_department AS cd ON ce.current_dept_nr = cd.nr
							LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
							LEFT JOIN seg_municity AS sm ON sm.mun_nr=cp.mun_nr
							LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
							LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
						WHERE DATE(ce.received_date) BETWEEN '$start_date' AND '$end_date'
						AND ce.encounter_type IN (3,4)
						AND ce.status NOT IN ('deleted','hidden','inactive','void')
						$sql_dept
						ORDER BY name_last, name_first, name_middle";

		$result=$this->Conn->Execute($sql);
		$this->_count = $result->RecordCount();

		$this->Conn->SetFetchMode(ADODB_FETCH_ASSOC);
		if ($result) {
			$this->Data=array();
			$i=1;

			 while ($row=$result->FetchRow()) {

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
							#$province = trim($prov_name);
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
							trim(mb_strtoupper($row['fullname'])),
							date("h:i A",strtotime($row['consult_time'])),
							$age,
							strtoupper($row['p_sex']),
							trim(mb_strtoupper($addr)),
							$row['name_formal'],
							date("m/d/Y",strtotime($row['received_date']))
						);

					$i++;
			}

	}else
			echo $this->Conn->ErrorMsg();
	}
}


$rep = new RepGen_OPD_Trans($_GET['from'],$_GET['to'], $_GET['dept_nr']);
$rep->AliasNbPages();
$rep->FetchData();
ini_set('memory_limit', '2048M');
$rep->Report();
?>
