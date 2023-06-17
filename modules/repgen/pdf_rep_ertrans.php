<?php
/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

include_once($root_path.'include/care_api_classes/class_department.php');
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');
//require($root_path.'/modules/repgen/repgen.inc.php');

require($root_path.'/modules/repgen/themes/dmc/dmc.php');

class RepGen_ER_Trans extends DMCRepGen {
//class RepGen_OPD_Trans extends RepGen {
	var $from_date;
	var $to_date;
	var $dept_nr;
	var $from_time;
	var $to_time;


	function RepGen_ER_Trans($from, $to, $dept_nr, $fromtime, $totime, $orderby) {
		global $db;

		$OB_array = array("124", "123", "139","155");

		if (in_array($dept_nr, $OB_array)){
			$this->DMCRepGen("EMERGENCY DEPARTMENT: DAILY TRANSACTIONS", "L", "A4", $db, TRUE);

		}else{
			$this->DMCRepGen("EMERGENCY DEPARTMENT: DAILY TRANSACTIONS", "L", "Letter", $db, TRUE);
		}

		$this->Caption = "Inpatient Daily Transactions";
		$this->dept_nr = $dept_nr;

		$this->orderby = $orderby;
		//Edited by Cherry 04-14-09
		//$this->SetAutoPageBreak(FALSE);
		$this->SetAutoPageBreak(TRUE, 2);
		$this->LEFTMARGIN=5;
		$this->DEFAULT_TOPMARGIN = 2;
		# 165
		#$this->ColumnWidth = array(21,41,20,18,18,15.3,50,30,10,36);
		if ($this->dept_nr) {
			if (in_array($dept_nr, $OB_array)){
				$this->ColumnWidth = array(10,20,60,20,20,12,7,11,60,35);
			}else{
				$this->ColumnWidth = array(10,20,70,20,20,12,7,11,65,35);
			}
			$this->Columns = 10;
		}else{
			#$this->ColumnWidth = array(15,21,51,15,14,15.3,60,30);
			$this->ColumnWidth = array(8,20,45,18,20,12,7,11,50,30,15,40);
			$this->Columns = 12;
		}
		$this->TotalWidth = array_sum($this->ColumnWidth);
		#$this->Columns = 10;


		if ($this->dept_nr) {
			$this->ColumnLabels = array(
				'',
				'Patient ID',
				'Fullname',
				'Time',
				'Birth Date',
				'Age',
				'Sex',
				'Status',
				'Address',
				'Department'
			);
		}else{
				$this->ColumnLabels = array(
				'',
				'Patient ID',
				'Fullname',
				'Time',
				'Birth Date',
				'Age',
				'Sex',
				'Status',
				'Address',
				'Department',
				'ICD',
				'Physician'
			);
		}

		#$this->textpadding
		$this->RowHeight = 5;
		$this->TextHeight = 3.5;

		#$this->Alignment = array('C','L','C','C','C','C','L','L','L','L');
		if ($this->dept_nr) {
			$this->Alignment = array('L','C','L','C','C','C','C','L','L','L');

		}else{
			$this->Alignment = array('L','C','L','C','C','C','C','C','L','L','L','L');
		}

		$this->PageOrientation = "L";

		if ($from) $this->from=date("Y-m-d",strtotime($from));
		if ($to) $this->to=date("Y-m-d",strtotime($to));


		$this->from_time = $fromtime;
		$this->to_time = $totime;

		$this->NoWrap = FALSE;

	}

	function Header() {
		$objInfo = new Hospital_Admin();

		if ($row = $objInfo->getAllHospitalInfo()) {
			$row['hosp_agency'] = strtoupper($row['hosp_agency']);
			$row['hosp_name']   = strtoupper($row['hosp_name']);
		}else {
			$row['hosp_country'] = "Republic of the Philippines";
			$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
			$row['hosp_addr1']   = "Quezon Ave., Digos City";
		}

		$this->SetFont("Arial","I","9");
		$this->Cell(0,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(0,4,$row['hosp_agency'],$border2,1,'C');
		$this->SetFont("Arial","B","10");
		$this->Cell(0,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont('Arial','',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("m/d/Y h:iA"),0,0,'R');
		$this->Ln(2);
		$this->SetFont("Arial","B","12");
				$this->Cell(0,4,'Emergency Daily Transactions',$border2,1,'C');
		$this->Ln(1);

		#$this->Cell(0,5,date("m/d/Y",strtotime($this->from))."  ".$this->from_time." - ".date("m/d/Y",strtotime($this->to))."  ".$this->to_time,$border2,1,'C');
		$this->Cell(0,4,date("m/d/Y",strtotime($this->from))."  ".date("h:i A",strtotime($this->from_time))." - ".date("m/d/Y",strtotime($this->to))."  ".date("h:i A",strtotime($this->to_time)),$border2,1,'C');

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
		$this->FONTSIZE = 10;
		if ($this->colored) {
			#$this->DrawColor = array(0xDD,0xDD,0xDD);
			$this->DrawColor = array(255,255,255);
		}
	}

	function FetchData() {

		if (empty($this->to)) $end_date="NOW()";
		#else $end_date="'$end_date'";
		else $end_date=$this->to;
		#if (empty($start_date)) $start_date="0000-00-00";
		if (empty($this->from)) $start_date="NOW()";
		else
		$start_date=$this->from;
		#$start_date="$start_date";

		#time
		if (empty($this->to_time)) $end_time="NOW()";
		#else $end_date="'$end_date'";
		else $end_time=$this->to_time;
		#if (empty($start_date)) $start_date="0000-00-00";
		if (empty($this->from_time)) $start_time="NOW()";
		else
		$start_time=$this->from_time;

		//Added by Cherry 04-14-09
		$sql_dept = "";
                #added by VAN 02-23-2011
                $grp_sql = " GROUP BY ce.current_dept_nr,ce.pid "; 
		if ($this->dept_nr) {
			$sql_dept = " AND ce.current_dept_nr=".$this->dept_nr;
                        #commented by VAN 02-23-2011			
                        #$grp_sql = " ";

			if ($this->orderby)
					$order_sql = " ORDER BY name_last, name_first, name_middle ";
			else
				$order_sql = " ORDER BY encounter_date ";
		}else{
                        #commented by VAN 02-23-2011			
			#$grp_sql = " GROUP BY ce.current_dept_nr,ce.pid ";
			#$order_sql = " ORDER BY encounter_date ";
			if ($this->orderby)
					$order_sql = " ORDER BY name_last, name_first, name_middle ";
			else
				$order_sql = " ORDER BY encounter_date ";
		}

		//Edited by Cherry 04-14-09
		$sql =
"SELECT distinct cp.pid, cd.name_formal,
	CONCAT(IFNULL(name_last,''),', ',IFNULL(name_first,''),' ',IFNULL(name_middle,'')) AS fullname,
	CAST(encounter_date as DATE) as consult_date,
	CAST(encounter_date AS TIME) AS consult_time,
	fn_get_age(CAST(encounter_date AS date), CAST(date_birth AS DATE)) AS age,
	UPPER(sex) AS p_sex, addr_str, cd.id,
	cp.street_name,	sb.brgy_name, sm.mun_name, sm.zipcode, sp.prov_name,
	ced.code, ced.diagnosing_clinician, cp.civil_status, cp.date_birth
FROM (care_encounter AS ce
	INNER JOIN care_person AS cp ON ce.pid = cp.pid)
		LEFT JOIN care_encounter_diagnosis AS ced ON ce.encounter_nr = ced.encounter_nr
	LEFT JOIN care_department AS cd ON ce.current_dept_nr = cd.nr
	LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
	LEFT JOIN seg_municity AS sm ON sm.mun_nr=cp.mun_nr
	LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
	LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
WHERE (encounter_date >= '$start_date'
	AND CONCAT(CAST(encounter_date AS date), ' 00:00:00') < DATE_ADD('$end_date', INTERVAL 1 DAY))
	AND CAST(encounter_date AS TIME) BETWEEN '$start_time' AND '$end_time'
	AND ce.encounter_type=1
	$sql_dept ";

		$sql .= " $grp_sql $order_sql ";
	#echo $sql;
		$result=$this->Conn->Execute($sql);
		$this->_count = $result->RecordCount();
		$this->Conn->SetFetchMode(ADODB_FETCH_ASSOC);
		if ($result) {
			$this->Data=array();
			$i=1;
			while ($row=$result->FetchRow()) {

			if ($row['street_name']){
					if ($row["brgy_name"]!="NOT PROVIDED")
						$street_name = $row['street_name'].", ";
					else
						$street_name = $row['street_name'];
			}else{
					$street_name = "";
			}


			if ((!($row["brgy_name"])) || ($row["brgy_name"]=="NOT PROVIDED"))
				$brgy_name = "";
			else
				$brgy_name  = $row["brgy_name"];

			if ((!($row["mun_name"])) || ($row["mun_name"]=="NOT PROVIDED"))
				$mun_name = "";
			else{
				if ($brgy_name)
					$mun_name = ", ".$row["mun_name"];
				else
					$mun_name = $row["mun_name"];
			}

			if ((!($row["prov_name"])) || ($row["prov_name"]=="NOT PROVIDED"))
				$prov_name = "";
			else
				$prov_name = $row["prov_name"];

			if(stristr(trim($row["mun_name"]), 'city') === FALSE){
				if ((!empty($row["mun_name"]))&&(!empty($row["prov_name"]))){
					if ($row["prov_name"]!="NOT PROVIDED")
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

			if (($row['date_birth']) && ($row['date_birth']!='0000-00-00') ){
				$bdate = date("m/d/Y",strtotime($row['date_birth']));
			}else{
				$bdate = 'unknown';
			}

			if ($row['civil_status']=='married')
				$cstatus = "M";
			elseif ($row['civil_status']=='single')
				$cstatus = "S";
			elseif ($row['civil_status']=='child')
				$cstatus = "CH";
			elseif ($row['civil_status']=='divorced')
				$cstatus = "D";
			elseif ($row['civil_status']=='widowed')
				$cstatus = "W";
			elseif ($row['civil_status']=='separated')
				$cstatus = "S";
	#$address = trim($street_name)." ".trim($brgy_name).", ".trim($mun_name)." ".trim($zipcode)." ".trim($prov_name);
	#$addr = trim($street_name)." ".trim($brgy_name).", ".trim($mun_name)." ".$province;

				if($row['diagnosing_clinician']){
					$sql = "SELECT CONCAT(IFNULL(name_last,''),', ',IFNULL(name_first,''),' ',
								IFNULL(SUBSTRING(name_middle,1,1),'')) AS physician
								FROM care_personell AS pr
								INNER JOIN care_person AS p ON p.pid=pr.pid
								WHERE pr.nr='".$row['diagnosing_clinician']."'";
					$result2=$this->Conn->Execute($sql);
					$this->_count = $result->RecordCount();
					$this->Conn->SetFetchMode(ADODB_FETCH_ASSOC);
					if ($result2) {
						$row2=$result2->FetchRow();
					}
					$physician = $row2['physician'];
				}else{
					$physician = "";
				}

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
				}else{
					$age = floor($row['age']).' y';
				}

				#$row['consult_date'],
			if ($this->dept_nr) {
				$this->Data[]=array(
					$i,
					$row['pid'],
					trim($row['fullname']),
					date("h:i A",strtotime($row['consult_time'])),
					$bdate,
					$age,
					strtoupper($row['p_sex']),
					$cstatus,
					trim($addr),
					$row['name_formal']
				);
			}else{
				$this->Data[]=array(
					$i,
					$row['pid'],
					trim($row['fullname']),
					date("h:i A",strtotime($row['consult_time'])),
					$bdate,
					$age,
					strtoupper($row['p_sex']),
					$cstatus,
					trim($addr),
					$row['name_formal'],
					$row['code'],
					$physician
				);
			}
				$i++;
			}

		}
		else
			echo $this->Conn->ErrorMsg();
	}
}

#echo "fromtime = ".$fromtime;
#echo "<br>totime = ".$totime;

$rep = new RepGen_ER_Trans($_GET['from'],$_GET['to'], $_GET['dept_nr'],$_GET['fromtime'],$_GET['totime'],$_GET['orderby']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();
?>