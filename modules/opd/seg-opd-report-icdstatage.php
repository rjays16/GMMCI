<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
include_once($root_path."include/care_api_classes/class_department");
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');

require($root_path.'/modules/repgen/repgen.inc.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

class RepGen_OPD_ICDStat_Age extends RepGen {
	var $Conn;

	function RepGen_OPD_ICDStat_Age($from, $to) {
		global $db;
		
		$this->RepGen("OUT PATIENT DEPARTMENT - ICD10 Statistics", "L", "Legal");
		$this->Caption = "Outpatient Preventive Care Center ICD 10 Statistics";
		
		$this->Conn = &$db;
		
		$this->Columns = 22;
		$this->Alignment = array();
		for ($i=0;$i<$this->Columns;$i++) {
			$this->ColumnWidth[$i] = 13;
			$this->Alignment[$i] = 'R';
		}
		$this->ColumnWidth[0] = 20;
		$this->ColumnWidth[1] = 52;
		$this->ColumnWidth[21] = 16.5;
		$this->TotalWidth = array_sum($this->ColumnWidth);
		$this->Alignment[0] = 'L';
		$this->Alignment[1] = 'L';
		$this->RowHeight = 6;
		$this->TextPadding = array('L'=>'0.5','R'=>'0.5','T'=>'0.5','B'=>'0.5');

		$this->ColumnLabels = array(
			"ICD10", "Description", 
			"<1 M", "<1 F",
			"1-4 M", "1-4 F",
			"5-9 M", "5-9 F",
			"10-14 M", "10-14 F",
			"15-19 M", "15-19 F",
			"20-44 M", "20-44 F",
			"45-59 M", "45-59 F",
			"60> M", "60> F",
			"Total M", "Total F",
			"Total", "%");
			
		if ($from) $this->from=date("Y-m-d",strtotime($from));
		if ($to) $this->to=date("Y-m-d",strtotime($to));
	}
	
	function Header() {
		$objInfo = new Hospital_Admin();		
		$this->Conn->SetFetchMode(ADODB_FETCH_ASSOC);
		if ($row = $objInfo->getAllHospitalInfo()) {			
			$row['hosp_agency'] = strtoupper($row['hosp_agency']);
			$row['hosp_name']   = strtoupper($row['hosp_name']);
		}
		else {
			$row['hosp_country'] = "Republic of the Philippines";
			$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
			$row['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";			
		}

		$this->LogoX = 132;
		$this->LogoY = 8;
		$this->Image('../../gui/img/logos/dmc_logo.jpg',$this->LogoX,$this->LogoY,16,20);
		$this->SetFont("Arial","I","9");
		$this->Cell(0,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(0,4,$row['hosp_agency'],$border2,1,'C');
		$this->SetFont("Arial","B","10");
		$this->Cell(0,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(0,4,$row['hosp_addr1'],$border2,1,'C');
	   $this->SetFont('Arial','B',11);
		$this->Ln(2);
   	$this->Cell(0,5,'Outpatient Preventive Care Center ICD 10 Statistics',$border2,1,'C');
		$from_dt=strtotime($this->from);
		$to_dt=strtotime($this->to);
		$this->SetFont("Arial","","8");
		if (!empty($this->from) && !empty($this->to_date))
			$this->Cell(0,5,
				sprintf('%s-%s',date("F j, Y",$from_dt),date("F j, Y",$to_dt)),
				$border2,1,'C');
		$this->Ln(3);
		
		parent::Header();
	}

	function FetchData($repDate=NULL) {
		if (empty($repDate))
			$repDate = date("Y-m-d");
		$sql=
"SELECT i.diagnosis_code, i.description,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND fn_get_age_bracket(P.date_birth,NOW())='<1' AND P.sex='m') AS A1M,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND fn_get_age_bracket(P.date_birth,NOW())='<1' AND P.sex='f') AS A1F,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND fn_get_age_bracket(P.date_birth,NOW())='1-4' AND P.sex='m') AS A1_4M,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND fn_get_age_bracket(P.date_birth,NOW())='1-4' AND P.sex='f') AS A1_4F,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND fn_get_age_bracket(P.date_birth,NOW())='5-9' AND P.sex='m') AS A5_9M,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND fn_get_age_bracket(P.date_birth,NOW())='5-9' AND P.sex='f') AS A5_9F,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND fn_get_age_bracket(P.date_birth,NOW())='10-14' AND P.sex='m') AS A10_14M,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND fn_get_age_bracket(P.date_birth,NOW())='10-14' AND P.sex='f') AS A10_14F,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND fn_get_age_bracket(P.date_birth,NOW())='15-19' AND P.sex='m') AS A15_19M,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND fn_get_age_bracket(P.date_birth,NOW())='15-19' AND P.sex='f') AS A15_19F,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND fn_get_age_bracket(P.date_birth,NOW())='20-44' AND P.sex='m') AS A20_44M,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND fn_get_age_bracket(P.date_birth,NOW())='20-44' AND P.sex='f') AS A20_44F,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND fn_get_age_bracket(P.date_birth,NOW())='45-59' AND P.sex='m') AS A45_59M,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND fn_get_age_bracket(P.date_birth,NOW())='45-59' AND P.sex='f') AS A45_59F,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND fn_get_age_bracket(P.date_birth,NOW())='60>' AND P.sex='m') AS A60M,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND fn_get_age_bracket(P.date_birth,NOW())='60>' AND P.sex='f') AS A60F,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND P.sex='m') AS `TOTALM`,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E, care_person as P WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr AND E.pid=P.pid AND P.sex='f') AS `TOTALF`,".
"(SELECT COUNT(1) FROM seg_encounter_icd as EI, care_encounter as E WHERE EI.diagnosis_code=i.diagnosis_code AND EI.encounter_nr=E.encounter_nr) AS `TOTAL`".
"FROM care_icd10_en as i WHERE EXISTS(SELECT 1 FROM seg_encounter_icd WHERE diagnosis_code=i.diagnosis_code) ORDER BY `TOTAL` DESC";

		$this->Conn->SetFetchMode(ADODB_FETCH_NUM); 
		$result = $this->Conn->Execute($sql);
		$this->_count = $result->RecordCount();
		if ($result) {
			$this->data=array();
			while ($row=$result->FetchRow()) {
				$this->data[]=$row;
			}

			# append addditional columns (total_m, total_f, total)
			$totals = array();
			
			foreach ($this->data as $j=>$row) {
				$desc=$this->data[$j][1];
				$this->data[$j][1] = $desc;

				foreach ($row as $i=>$v) {
					$totals[$i] += $v;
				}
			}
			
			$totals[0] = "";
			$totals[1] = "Total";
			$this->data[] = $totals;
			$grand_total = $totals[sizeof($totals)-1];

			foreach ($this->data as $i=>$v) {
				$this->data[$i][] = sprintf("%0.2f%%",($v[sizeof($v)-1]*100.0)/$grand_total);
			}

			$this->Data = &$this->data;
		}
		else {
			echo $this->Conn->ErrorMsg();
		}
	}
}

$rep =& new RepGen_OPD_ICDStat_Age($_GET['from'],$_GET['to']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

?>
