<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require('repgen.inc.php');
include_once($root_path."/include/inc_init_main.php");
include_once($root_path."/classes/adodb/adodb.inc.php");
include_once($root_path."include/care_api_classes/class_hospital_admin.php");
require($root_path.'include/inc_environment_global.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

class RepGen_OPD_ICDStat_Age extends RepGen {
	var $Conn;
	var $from_date;
	var $to_date;

	function RepGen_OPD_ICDStat_Age($datefrom, $dateto) {
		global $dbtype, $dbhost, $dbusername, $dbpassword, $dbname;
		$this->RepGen("OUT PATIENT DEPARTMENT - Top ", "L", "Letter");
		$this->SetAutoPageBreak(FALSE);
		
		$this->Conn = &ADONewConnection($dbtype);
		$this->Conn->PConnect($dbhost,$dbusername,$dbpassword,$dbname);
		$this->Columns = 22;
		
		$this->from_date = $datefrom;
		$this->to_date = $dateto;
		
		$this->LEFTMARGIN=5;
		$this->DEFAULT_TOPMARGIN = 5;
		$this->NoWrap = false;
		
		$this->Alignment = array();
		for ($i=0;$i<$this->Columns;$i++) {
			#$this->ColumnWidth[$i] = 13;
			$this->ColumnWidth[$i] = 10.4;
			$this->Alignment[$i] = 'R';
		}
		$this->ColumnWidth[0] = 15;
		$this->ColumnWidth[1] = 48;
		$this->Alignment[0] = 'L';
		$this->Alignment[1] = 'L';
		#$this->PageOrientation = "L";
		$this->Format = "Letter";
		#$this->RowHeight = 5;
		$this->RowHeight = 4.5;
		$this->TextHeight = 5;
		
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
	}

	function Header() {	
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
		
		$this->Image('../../gui/img/logos/dmc_logo.jpg',70,5,20);
		$this->SetFont("Arial","I","9");
   	#$this->Cell(0,4,'Republic of the Philippines',$border2,1,'C');
		$this->Cell(0,4,$row['hosp_country'],$border2,1,'C');
	   #$this->Cell(0,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
		$this->Cell(0,4,$row['hosp_agency'],$border2,1,'C');
   	#$this->Ln(1);
		$this->SetFont("Arial","B","10");
    	#$this->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
		$this->Cell(0,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
   	#$this->Cell(0,4,'Davao City',$border2,1,'C');
		$this->Cell(0,4,$row['hosp_addr1'],$border2,1,'C');
	   $this->SetFont('Arial','B',11);
		$this->Ln(2);
   		$this->Cell(0,5,'Outpatient Preventive Care Center ICD 10 Statistics',$border2,1,'C');
		$from_dt=strtotime($this->from_date);
		$to_dt=strtotime($this->to_date);
		$this->SetFont("Arial","B","11");
		if (!empty($this->from_date) && !empty($this->to_date))
			$this->Cell(0,5,
				sprintf('%s-%s',date("F j, Y",$from_dt),date("F j, Y",$to_dt)),
				$border2,1,'C');
		$this->Ln(8);
		
		
		# Print table header
		
    $this->SetFont('Arial','B', 8);
		#$this->SetFillColor(150);
		$this->SetFillColor(255);
		#$this->SetTextColor(255);
		#$this->SetTextColor(0);

		$row=8;
		for ($i=0;$i<$this->Columns;$i++) {
			$this->Cell($this->ColumnWidth[$i],$this->RowHeight,$this->ColumnLabels[$i],1,0,'C',1);
		}
		
		$this->Ln();
	}
	
	function Footer() {
		$this->SetY(-7);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:s A"),0,0,'R');
	}
	
	function echo_matrix($matrix) {
		$echo="";
		foreach ($matrix as $row) {
			foreach ($row as $cell) {
				$echo.="'".substr($cell,0,8)."'_";
			}
			$echo .= "\n";
		}
		return $echo;
	}
	
	function BeforeRow() {
		if (($this->ROWNUM%2)==0) 
			#$this->FILLCOLOR=array(0xde, 0xdf, 0xe4);
			$this->FILLCOLOR=array(255, 255, 255);
		else
			$this->FILLCOLOR=array(255,255,255);
		if ($this->ROWNUM == ($this->MAXROWS-1)) {
			#$this->FILLCOLOR = array(0xc8,0xd1,0xda);
			$this->FILLCOLOR = array(255,255,255);
		}
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
		#print_r($sql);
		#exit;
		$this->Conn->SetFetchMode(ADODB_FETCH_NUM); 
		$result = $this->Conn->Execute($sql);
		if ($result) {
			$this->data=array();
			while ($row=$result->FetchRow()) {
				#print_r($row);
				#echo "<hr>";
				$this->data[]=$row;
			}
			# append addditional columns (total_m, total_f, total)
			$totals = array();
			
			
			foreach ($this->data as $j=>$row) {
				$desc=$this->data[$j][1];
				if (strlen($desc) > 30) $desc = substr($desc,0,30) . "...";
				$this->data[$j][1] = $desc;
				#$total_m = 0;
				#$total_f = 0;
				foreach ($row as $i=>$v) {
					#if ($i%2) $total_f += $v;
					#else $total_m += $v;
					$totals[$i] += $v;
				}
				#$this->data[$j][] = $total_m;
				#$this->data[$j][] = $total_f;
				#$this->data[$j][] = $total_m+$total_f;
				#$totals[++$i] += $total_m;
				#$totals[++$i] += $total_f;
				#$totals[++$i] += $total_m+$total_f;
			}
			
			# append additional row (totals)
			$totals[0] = "";
			$totals[1] = "Total";
			$this->data[] = $totals;
			$grand_total = $totals[sizeof($totals)-1];

			foreach ($this->data as $i=>$v) {
				$this->data[$i][] = sprintf("%0.2f%%",($v[sizeof($v)-1]*100.0)/$grand_total);
			}
			
			$this->Data = &$this->data;
			#echo "<code>";
			#echo str_replace("_",str_repeat("&nbsp;",2),nl2br($this->echo_matrix($this->data)));
			#exit;
			
		}
		else {
			echo $this->Conn->ErrorMsg();
		}
	}
}

$icd = new RepGen_OPD_ICDStat_Age($_GET['from'],$_GET['to']);
$icd->AliasNbPages();
$icd->FetchData();
$icd->Report();

?>
