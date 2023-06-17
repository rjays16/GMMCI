<?php
	#created by CHA 07-21-09
	#Research and Query Report- for DMC

	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path.'/modules/repgen/repgen.inc.php');
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

	class RepGen_Research_And_Query extends RepGen
	{
			var $fromdate;
			var $todate;
			var $code_type;
			var $icd_code;
			var $icp_code;
			var $patient_type;


			function RepGen_Research_And_Query($fromdate, $todate, $codetype, $icd, $icp, $ptype)
			{
					$this->RepGen("Research And Query Report", "L", "Legal");
					$this->colored = FALSE;
					$this->ColumnWidth = 33;
					$this->ColumnWidth = array (25, 25, 27, 47, 20, 15, 80, 20, 35, 47);
					//print_r($this->RowHeight);
					$this->RowHeight = 5;
					//$this->RowHeight = array (5, 5, 5, 5, 5, 5, 5, 5, 5, 5);
					$this->Alignment=array('C','C','C','R','C','C','R','C','R','R');
					$this->SetAutoPageBreak(TRUE);
					/*
					$this->Headers = array(
							'CASE #', 'Date Admitted',
							'Date Discharged', 'Patient Name',
							'Age', 'Sex', 'Address',
							'Results', 'Department', 'Doctor'
						); */

					if($fromdate) $this->fromdate=$fromdate;
					if($todate) $this->todate=$todate;
					if($codetype) $this->code_type=$codetype;
					if($icd) $this->icd_code=$icd;
					if($icp) $this->icp_code=$icp;
					if($ptype) $this->patient_type=$ptype;
			}

			function Header()
			{
					global $db;
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

					$this->SetFont("Arial","I","9");
					$total_w = 300;
					$this->Cell(17,4);
					$this->Cell($total_w,4, $row['hosp_country'],$border2,1,'C');
					$this->Cell(17,4);
					$this->Cell($total_w,4, $row['hosp_agency'],$border2,1,'C');
					$this->Ln(2);
					$this->SetFont("Arial","B","10");
					$this->Cell(17,4);
					$this->Cell($total_w,4, $row['hosp_name'],$border2,1,'C');
					$this->SetFont("Arial","","9");
					$this->Cell(17,4);
					$this->Cell($total_w,4, $row['hosp_addr1'],$border2,1,'C');
					$this->Ln(4);
					$this->SetFont('Arial','B',12);
					$this->Cell(17,5);
					$this->Cell($total_w,4,'RESEARCH AND QUERY REPORT',$border2,1,'C');
					$this->SetFont('Arial','B',9);
					if ($this->fromdate==$this->todate)
						$text = "For ".date("F j, Y",strtotime($this->fromdate));
					else
						$text = "From ".date("F j, Y",strtotime($this->fromdate))." To ".date("F j, Y",strtotime($this->todate));
					$this->Cell(330,4,$text,$border2,1,'C');
					if($this->patient_type=='1') $text2="ER PATIENT";
					else if($this->patient_type=='2') $text2="OUT PATIENT";
					else if($this->patient_type=='3,4') $text2="IN PATIENT";
					else if($this->patient_type=='1,2,3,4') $text2="ALL PATIENTS";
					#echo $this->patient_type;
					# added by VAN 08-05-09

			if (($this->icp_code) || ($this->icd_code)){
					if ($this->icp_code){
						$sql_code = "SELECT * FROM care_ops301_en WHERE code='".$this->icp_code."'";
						$code = $this->icp_code;
					}else if ($this->icd_code){
						$sql_code = "SELECT * FROM care_icd10_en WHERE diagnosis_code='".$this->icd_code."'";
						$code = $this->icd_code;
					}

					if ($this->code_type=='ICD10')
							$label = "ICD : ";
					else
							$label = "ICP : ";

					$rs = $db->Execute($sql_code);
					if ($rs){
						$row = $rs->FetchRow();
						$desc = " - ".trim($row['description']);

						if (empty($desc))
								$desc = "- No description specified";
					}
			}else{
					 if ($this->code_type=='ICD10')
							$label = "ALL ICD";
					 else
							$label = "ALL ICP";

					 $code = "";
					 $desc = "";
			}

					#-------------------

					$this->Ln(3);
					$this->Cell(330,4,$text2,$border2,1,'C');
					$this->Cell(330, 4, $label." ".$code." ".$desc, $border2, 0, 'C');
					$this->Ln(5);
					$this->Cell(25, 4, "TOTAL CASES: ".$this->_count, $border2, 1, 'R');
					$this->Ln(5);
					/*for($i=0;$i<count($this->Headers);$i++)
					{
							$this->Cell($this->ColumnWidth[$i], $this->RowHeight, 'mick', 1,0,'C',1);
					}*/
					$this->Cell($this->ColumnWidth[0], $this->RowHeight, 'HRN', 1,0,'C',1);
					$this->Cell($this->ColumnWidth[1], $this->RowHeight, 'Date Admitted', 1,0,'C',1);
					$this->Cell($this->ColumnWidth[2], $this->RowHeight, 'Date Discharged', 1,0,'C',1);
					$this->Cell($this->ColumnWidth[3], $this->RowHeight, 'Patient Name', 1,0,'C',1);
					$this->Cell($this->ColumnWidth[4], $this->RowHeight, 'Age', 1,0,'C',1);
					$this->Cell($this->ColumnWidth[5], $this->RowHeight, 'Sex', 1,0,'C',1);
					$this->Cell($this->ColumnWidth[6], $this->RowHeight, 'Address', 1,0,'C',1);
					$this->Cell($this->ColumnWidth[7], $this->RowHeight, 'Result', 1,0,'C',1);
					$this->Cell($this->ColumnWidth[8], $this->RowHeight, 'Department', 1,0,'C',1);
					$this->Cell($this->ColumnWidth[9], $this->RowHeight, 'Doctor', 1,0,'C',1);
					$this->Ln(5);
			}

			function Footer()
			{
				parent::Footer();
			}

			function BeforePageBreak()
			{
				$x=$this->GetX();
				$y=$this->GetY();
			}

			function BeforeData()
			{
				if ($this->colored) {
						#$this->DrawColor = array(0xDD,0xDD,0xDD);
						$this->DrawColor = array(255,255,255);
				}
				$this->ColumnFontSize = 9;
			}

			function BeforeCellRender()
			{
				$this->FONTSIZE = 8;
				if ($this->colored) {
						if (($this->RENDERPAGEROWNUM%2)>0)
								#$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
								$this->RENDERCELL->FillColor=array(255, 255, 255);
						else
								$this->RENDERCELL->FillColor=array(255,255,255);
				}
			}

			function AfterData()
			{
				global $db;

				if (!$this->_count) {
						$this->SetFont('Arial','B',9);
						$this->SetFillColor(255);
						$this->SetTextColor(0);
						$this->Cell(330, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
				}
			}

			function FetchData()
			{
					global $db;
					if($this->patient_type=='all') $this->patient_type='1,2,3,4';
					if($this->icd_code=='all') $this->icd_code='';
					if($this->icp_code=='all') $this->icp_code='';

					#echo $this->code_type;
					if($this->code_type=='ICD10')
					{
							$sql2="select distinct ce.pid, ce.encounter_nr as CaseNo,
									ce.discharge_date as DateDischarged, ce.admission_dt as DateAdmission,
									CONCAT(IF (trim(c.name_last) IS NULL,'',trim(c.name_last)),', ',IF(trim(c.name_first) IS NULL ,'',trim(c.name_first)),' ',
									IF(trim(c.name_middle) IS NULL,'',trim(c.name_middle))) as PatientName,
									IF(fn_calculate_age(NOW(),c.date_birth),fn_get_age(NOW(),c.date_birth),age) AS Age,
									upper(c.sex) AS Sex,
									CONCAT(IF (trim(c.street_name) IS NULL,'',trim(c.street_name)),' ',
										IF (trim(sb.brgy_name) IS NULL,'',trim(sb.brgy_name)),' ',
										IF (trim(sm.mun_name) IS NULL,'',trim(sm.mun_name)),' ',
										IF (trim(sm.zipcode) IS NULL,'',trim(sm.zipcode)),' ',
										IF (trim(sp.prov_name) IS NULL,'',trim(sp.prov_name)),' ',
										IF (trim(sr.region_name) IS NULL,'',trim(sr.region_name))) AS 'Address',
									res.result_desc AS 'Results',
									cd.name_formal as 'Department',
									UPPER(IF (ce.current_att_dr_nr, fn_get_personell_name(ce.current_att_dr_nr),fn_get_personell_name(ce.consulting_dr_nr))) AS 'Doctor'
									from care_encounter as ce
									inner join care_person as c on c.pid=ce.pid
									inner join care_encounter_diagnosis as ced on ce.encounter_nr=ced.encounter_nr
									INNER JOIN care_icd10_en AS ci ON ci.diagnosis_code=ced.code
									LEFT JOIN care_department as cd on cd.nr=ce.consulting_dept_nr
									LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=c.brgy_nr
									LEFT JOIN seg_municity AS sm ON sm.mun_nr=c.mun_nr
									LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
									LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
									LEFT JOIN (SELECT DISTINCT ser.encounter_nr,
									SUBSTRING(MAX(CONCAT(ser.modify_time,ser.result_code)),20,1) AS result_code
									FROM seg_encounter_result AS ser
									INNER JOIN care_encounter AS em ON em.encounter_nr=ser.encounter_nr
									GROUP BY ser.encounter_nr
									ORDER BY ser.encounter_nr) AS sr ON sr.encounter_nr = ce.encounter_nr
									INNER JOIN seg_results AS res ON res.result_code=sr.result_code
									WHERE ce.status NOT IN ('deleted','hidden','inactive','void')
									AND cd.status NOT IN ('deleted','hidden','inactive','void')
                                    AND ced.status NOT IN ('deleted','hidden','inactive','void') 
									#AND (ce.discharge_date BETWEEN '".$this->fromdate."' and '".$this->todate."')
									#AND (ce.admission_dt BETWEEN '".$this->fromdate."' and '".$this->todate."')
                                    AND (ce.encounter_date BETWEEN '".$this->fromdate."' and '".$this->todate."')
									AND ce.encounter_type IN(".$this->patient_type.")
									AND ced.code='".$this->icd_code."'
									ORDER BY c.name_last, c.name_first, c.name_middle";
					}else if($this->code_type=='ICP')
					{
							$sql2="select distinct ce.pid, ce.encounter_nr as CaseNo,
									ce.discharge_date as DateDischarged, ce.admission_dt as DateAdmission,
									CONCAT(IF (trim(c.name_last) IS NULL,'',trim(c.name_last)),', ',IF(trim(c.name_first) IS NULL ,'',trim(c.name_first)),' ',
									IF(trim(c.name_middle) IS NULL,'',trim(c.name_middle))) as PatientName,
									IF(fn_calculate_age(NOW(),c.date_birth),fn_get_age(NOW(),c.date_birth),age) AS Age,
									upper(c.sex) AS Sex,
									CONCAT(IF (trim(c.street_name) IS NULL,'',trim(c.street_name)),' ',
										IF (trim(sb.brgy_name) IS NULL,'',trim(sb.brgy_name)),' ',
										IF (trim(sm.mun_name) IS NULL,'',trim(sm.mun_name)),' ',
										IF (trim(sm.zipcode) IS NULL,'',trim(sm.zipcode)),' ',
										IF (trim(sp.prov_name) IS NULL,'',trim(sp.prov_name)),' ',
										IF (trim(sr.region_name) IS NULL,'',trim(sr.region_name))) AS 'Address',
									res.result_desc AS 'Results',
									cd.name_formal as 'Department',
									UPPER(IF (ce.current_att_dr_nr, fn_get_personell_name(ce.current_att_dr_nr),fn_get_personell_name(ce.consulting_dr_nr))) AS 'Doctor'
									from care_encounter as ce
									inner join care_person as c on c.pid=ce.pid
									inner join care_encounter_procedure as cp on ce.encounter_nr=cp.encounter_nr
									INNER JOIN care_ops301_en AS co ON co.code=cp.code
									LEFT JOIN care_department as cd on cd.nr=ce.consulting_dept_nr
									LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=c.brgy_nr
									LEFT JOIN seg_municity AS sm ON sm.mun_nr=c.mun_nr
									LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
									LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
									LEFT JOIN (SELECT DISTINCT ser.encounter_nr,
									SUBSTRING(MAX(CONCAT(ser.modify_time,ser.result_code)),20,1) AS result_code
									FROM seg_encounter_result AS ser
									INNER JOIN care_encounter AS em ON em.encounter_nr=ser.encounter_nr
									GROUP BY ser.encounter_nr
									ORDER BY ser.encounter_nr) AS sr ON sr.encounter_nr = ce.encounter_nr
									INNER JOIN seg_results AS res ON res.result_code=sr.result_code
									WHERE ce.status NOT IN ('deleted','hidden','inactive','void')
									AND cp.status NOT IN ('deleted','hidden','inactive','void')
									AND cd.status NOT IN ('deleted','hidden','inactive','void') 
                                    AND (ce.encounter_date BETWEEN '".$this->fromdate."' and '".$this->todate."')
									AND ce.encounter_type IN(".$this->patient_type.")
									AND cp.code = '".$this->icp_code."'
									ORDER BY c.name_last, c.name_first, c.name_middle";
					}
				#echo 'sql='.$sql2;
                    $result=$db->Execute($sql2);
					if($result)
					{
						 // $this->Data=array();
							#echo '<br><br>result='.$result;
							#echo '<br><br>';
							#while($result!=null && $row=$result->FetchRow())
                            while ($row2=$result->FetchRow()) {
                                                                         if ($row2['DateDischarged']!='0000-00-00')
                                                                             $discharged_date = date("m/d/Y",strtotime($row2['DateDischarged']));
                                                                         if ($row2['DateAdmission']!='0000-00-00 00:00:00')
                                                                             $admission_date = date("m/d/Y",strtotime($row2['DateAdmission']));
									 $this->Data[]=array($row2['pid'],
											$admission_date,
											$discharged_date,
											$row2['PatientName'],
											$row2['Age'],
											$row2['Sex'],
											$row2['Address'],
											$row2['Results'],
											$row2['Department'],
											$row2['Doctor']);
                            }
                           #print_r($row2);
                           #echo "here = ".$row2['pid'];
                            
					}
					$this->_count = count($this->Data);
					$this->SetFont('Arial','',9);
					$this->Alignment=array('C','C','C','L','C','C','L','C','C','L');

			}
	}

	$rep = new RepGen_Research_And_Query($_GET['fromdate'],$_GET['todate'],$_GET['codetype'],$_GET['icd'],$_GET['icp'],$_GET['ptype']);
	$rep->AliasNbPages();
	$rep->FetchData();
	$rep->Report();
?>