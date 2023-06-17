<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

	class RepGen_Laboratory_Income extends RepGen {
	#var $date;
	var $colored = FALSE;
	var $fromdate;
	var $todate;
	var $SUM_AMT_BILLED;
	var $SUM_AMT_PAID;
	var $SUM_CHARITY;
	var $SUM_PHS;
	var $SUM_LINGAP;
	var $SUM_CMAP;
	var $SUM_SOCIALIZED;
	var $SUM_PHIC;
	var $SUM_RDU;
	var $SUM_CHARGE;
	var $SUM_OTHER;

	var $no_of_services;
	var $no_of_patients;
	var $no_of_requests;

	var $pat_type;
	var $enctype;
	var $patient_type;
	var $servgroup;
	var $service_list_cond;
	var $service_list;
	var $group_cond;

	function RepGen_Laboratory_Income($fromdate, $todate, $pat_type, $servgroup, $service_list) {
		global $db;
		$this->RepGen("INCOME REPORT: LABORATORY");
		# 165
		$this->ColumnWidth = array(30,40,30,27,27,27,27,27,30,20,25,30);
		$this->RowHeight = 5.5;
		$this->LEFTMARGIN = 7;
		#$this->Alignment = array('R','C','R','R','R','R','R','R');
		$this->Alignment = array('C','C','R','R','R','R','R','R','R','R','R','R');
		$this->PageOrientation = "L";
		if ($fromdate) $this->fromdate=date("Y-m-d",strtotime($fromdate));
		if ($todate) $this->todate=date("Y-m-d",strtotime($todate));

		$this->pat_type = $pat_type;
		#added by Cherry 04-21-09
		$this->servgroup = $servgroup;

		#added by daryl
		if($service_list == 'all'){
			$this->service_list_cond = '';
		}else{
			$this->service_list_cond = 'service_code ='.$db->qstr($service_list).'AND';
		}

		$this->service_list = $service_list;

		if ($this->pat_type==0){
			$this->enctype = "";
			$this->patient_type = "ALL PATIENT";
		}elseif ($this->pat_type==1){
			#ER PATIENT
			$this->enctype = " AND encounter_type IN (1) ";
			$this->patient_type = "ER PATIENT";
		}elseif ($this->pat_type==2){
			#ADMITTED PATIENT
			$this->enctype = " AND encounter_type IN (3,4 )";
			$this->patient_type = "INPATIENT";
		}elseif ($this->pat_type==3){
			#OUT PATIENT
			$this->enctype = " AND encounter_type IN (2) ";
			$this->patient_type = "OUTPATIENT";
		}elseif ($this->pat_type==4){
			#WALKIN
			$this->enctype = " AND encounter_type IS NULL ";
			$this->patient_type = "WALKIN";
		}elseif ($this->pat_type==5){
			#OUT PATIENT AND WALKIN
			$this->enctype = " AND (encounter_type IN (2) OR encounter_type IS NULL ) ";
			$this->patient_type = "OUTPATIENT AND WALKIN";
		}
		// else if($this->pat_type==6){
		// 	#RDU
		// 	$this->enctype = " AND ((encounter_type IN (2) OR encounter_type IS NULL) AND is_rdu = 1) ";
		// 	$this->patient_type = "RDU";
		// }

		 if ($this->servgroup == 'B')
				$this->group_cond = "AND fromBB = 1 AND group_code IN ('B')";
		 elseif($this->servgroup != 'all'){
			 if($this->servgroup == 'notBB')
					$this->group_cond = "AND group_code NOT IN ('SPL','B') AND fromBB = 0";  //all except special lab and blood bank and IC
			 else
					$this->group_cond = "AND group_code = '".$this->servgroup."' ";
		 }else
				$this->group_cond = "AND group_code NOT IN ('SPL')";  //all except special lab

		$this->SetFillColor(0xFF);
		if ($this->colored) $this->SetDrawColor(0xDD);

		#echo "<br>enctype = ".$this->enctype;
		#echo "<br>grp = ".$this->group_cond;
		#echo "<br>patype = ".$this->patient_type;
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
			$row['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";
		}

	#$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',50,8,20);
		$this->SetFont("Arial","I","9");
		$total_w = 165;
		$this->Cell(45,4);
			#$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(45,4);
			#$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
			$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(45,4);
			#$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(45,4);
			#$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
		$this->Ln(4);

		$this->SetFont("Arial","B","8");
		$this->Cell(50,4);
		$this->Cell($total_w,4,'DEPARTMENT OF PATHOLOGY AND CLINICAL LABORATORIES',$border2,1,'C');
		$this->Ln(2);
		$this->SetFont('Arial','B',12);
		$sql_name="SELECT name FROM seg_lab_service_groups WHERE group_code = '".$this->servgroup."'";
		$result_name=$db->Execute($sql_name);
		$row_name = $result_name->FetchRow();
		$this->Cell(45,5);
		$this->Cell($total_w,4,$row_name['name'],$border2,1,'C');
		// $this->Cell(45,5);
		//added by daryl
		$this->SetFont("Arial","B","10");
		$result2="SELECT name FROM seg_lab_services WHERE service_code = '".$this->service_list."'";
		$result_name2=$db->Execute($result2);
		$row_name2 = $result_name2->FetchRow();
		if(!$row_name2)
			$service_name = "ALL";
		else
			$service_name = $row_name2['name'];
		$this->Cell(45,5);
		$this->Cell($total_w,4,$service_name,$border2,1,'C');
		$this->Cell(45,5);
		//ended by daryl
		$this->Cell($total_w,4,$this->patient_type.' INCOME REPORT',$border2,1,'C');
		$this->SetFont('Arial','B',9);
		$this->Cell(45,5);


		if ($this->fromdate==$this->todate)
			$text = "For ".date("F j, Y",strtotime($this->fromdate));
		else
				#$text = "Full History";
			$text = "From ".date("F j, Y",strtotime($this->fromdate))." To ".date("F j, Y",strtotime($this->todate));

		$this->Cell($total_w,4,$text,$border2,1,'C');
		$this->Ln(5);

	}

	function Footer()
	{
		$this->SetY(-23);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
	}

	function BeforeRow() {
		$this->FONTSIZE = 8;
		if ($this->colored) {
			if (($this->ROWNUM%2)>0)
				$this->FILLCOLOR=array(0xee, 0xef, 0xf4);
			else
				$this->FILLCOLOR=array(255,255,255);
			$this->DRAWCOLOR = array(0xDD,0xDD,0xDD);
		}
	}

	function BeforeData() {
		if ($this->colored) {
			$this->DrawColor = array(0xDD,0xDD,0xDD);
		}
		# Print table header
		$this->SetFont('Arial','B',8);
		if ($this->colored) $this->SetFillColor(0xED);
		$this->SetTextColor(0);
		$row=6;
		#$this->Cell(0,4,'',1,1,'C');
		$this->Cell($this->ColumnWidth[0],$row,'DATE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'SHIFT',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$row,'TOTAL AMT',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'CASH (FULL)',1,0,'C',1);
		$this->Cell($this->ColumnWidth[4],$row,'DSWD',1,0,'C',1);
		$this->Cell($this->ColumnWidth[5],$row,'PCSO',1,0,'C',1);
		$this->Cell($this->ColumnWidth[6],$row,'HMO',1,0,'C',1);
		// $this->Cell($this->ColumnWidth[7],$row,'CMAP',1,0,'C',1);
		// $this->Cell($this->ColumnWidth[8],$row,'SOCIALIZED',1,0,'C',1);
		$this->Cell($this->ColumnWidth[7],$row,'PHIC',1,0,'C',1);
		$this->Cell($this->ColumnWidth[8],$row,'NON-PHIC',1,0,'C',1);
		#$this->Cell($this->ColumnWidth[10],$row,'RDU',1,0,'C',1);
		// $this->Cell($this->ColumnWidth[11],$row,'OTHER CHARGES',1,0,'C',1);
		$this->Ln();

		#added by VAN 03-18-2011
		$this->FetchData();
	}

	function BeforeCellRender() {
		$this->FONTSIZE = 8;
		if ($this->colored) {
			if (($this->RENDERPAGEROWNUM%2)>0)
				$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
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
				$this->Cell(270, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}else{
				$this->SetFont('Arial','B',12);
				$this->Ln(4);
				$this->Cell(150, $this->RowHeight, 'TOTAL INCOME (less DSWD)', 0, 0, 'L', 1);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);

				$SUM_AMT_BILLED = $this->SUM_AMT_BILLED;
				
				#$this->Cell(30, $this->RowHeight, number_format($this->SUM_AMT_BILLED,2,'.',','), 0, 1, 'R', 1);
				$this->Cell(30, $this->RowHeight, number_format($SUM_AMT_BILLED,2,'.',','), 0, 1, 'R', 1);
				$this->Cell(150, $this->RowHeight, 'CASH PAID (Full Amount and no discount, Cash only) ', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_AMT_PAID,2,'.',','), 0, 1, 'R', 1);
				$this->Cell(150, $this->RowHeight, 'DSWD', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_CHARITY,2,'.',','), 0, 1, 'R', 1);

				// $this->Cell(150, $this->RowHeight, 'PHS', 0, 0, 'L', 0);
				// $this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				// $this->Cell(30, $this->RowHeight, number_format($this->SUM_PHS,2,'.',','), 0, 1, 'R', 1);

				$this->Cell(150, $this->RowHeight, 'HMO', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_LINGAP,2,'.',','), 0, 1, 'R', 1);

				// $this->Cell(150, $this->RowHeight, 'CMAP', 0, 0, 'L', 0);
				// $this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				// $this->Cell(30, $this->RowHeight, number_format($this->SUM_CMAP,2,'.',','), 0, 1, 'R', 1);

				$this->Cell(150, $this->RowHeight, 'CASH PAID with SOCIALIZED ITEM', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_SOCIALIZED,2,'.',','), 0, 1, 'R', 1);
				$this->Cell(150, $this->RowHeight, 'PHIC', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_PHIC,2,'.',','), 0, 1, 'R', 1);

				$this->Cell(150, $this->RowHeight, 'BILL (NOT CHARGE TO PHIC)', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_CHARGE,2,'.',','), 0, 1, 'R', 1);

				$this->Cell(150, $this->RowHeight, 'OTHER CHARGE (NOT PHIC, NOT LINGAP, NOT CMAP, NOT PERSONAL)', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_OTHER,2,'.',','), 0, 1, 'R', 1);

				#other
				// $this->Cell(10, $this->RowHeight, '', 0, 0, 'L', 0);
				// $this->Cell(140, $this->RowHeight, 'CAO', 0, 0, 'L', 0);
				// $this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				// $this->Cell(30, $this->RowHeight, number_format($this->SUM_CAO,2,'.',','), 0, 1, 'R', 1);

				// $this->Cell(10, $this->RowHeight, '', 0, 0, 'L', 0);
				// $this->Cell(140, $this->RowHeight, 'COH', 0, 0, 'L', 0);
				// $this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				// $this->Cell(30, $this->RowHeight, number_format($this->SUM_COH,2,'.',','), 0, 1, 'R', 1);

				// $this->Cell(10, $this->RowHeight, '', 0, 0, 'L', 0);
				// $this->Cell(140, $this->RowHeight, 'HACT', 0, 0, 'L', 0);
				// $this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				// $this->Cell(30, $this->RowHeight, number_format($this->SUM_HACT,2,'.',','), 0, 1, 'R', 1);

				// $this->Cell(10, $this->RowHeight, '', 0, 0, 'L', 0);
				// $this->Cell(140, $this->RowHeight, 'MISSION', 0, 0, 'L', 0);
				// $this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				// $this->Cell(30, $this->RowHeight, number_format($this->SUM_MISSION,2,'.',','), 0, 1, 'R', 1);

				// $this->Cell(10, $this->RowHeight, '', 0, 0, 'L', 0);
				// $this->Cell(140, $this->RowHeight, 'PCSO', 0, 0, 'L', 0);
				// $this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				// $this->Cell(30, $this->RowHeight, number_format($this->SUM_PCSO,2,'.',','), 0, 1, 'R', 1);

				// $this->Cell(10, $this->RowHeight, '', 0, 0, 'L', 0);
				// $this->Cell(140, $this->RowHeight, 'POPCOM', 0, 0, 'L', 0);
				// $this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				// $this->Cell(30, $this->RowHeight, number_format($this->SUM_POPCOM,2,'.',','), 0, 1, 'R', 1);

				// $this->Cell(10, $this->RowHeight, '', 0, 0, 'L', 0);
				// $this->Cell(140, $this->RowHeight, 'QCPB', 0, 0, 'L', 0);
				// $this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				// $this->Cell(30, $this->RowHeight, number_format($this->SUM_QCPB,2,'.',','), 0, 1, 'R', 1);

				// $this->Cell(10, $this->RowHeight, '', 0, 0, 'L', 0);
				// $this->Cell(140, $this->RowHeight, 'SSS', 0, 0, 'L', 0);
				// $this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				// $this->Cell(30, $this->RowHeight, number_format($this->SUM_SSS,2,'.',','), 0, 1, 'R', 1);



				/*$this->Cell(150, $this->RowHeight, 'OTHER CHARGES', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, number_format($this->SUM_OTHER,2,'.',','), 0, 1, 'R', 1);*/
				#$this->Cell(150, $this->RowHeight, 'RDU', 0, 0, 'L', 0);
				#$this->Cell(15, $this->RowHeight, '= Php', 0, 0, 'L', 1);
				#$this->Cell(30, $this->RowHeight, number_format($this->SUM_RDU,2,'.',','), 0, 1, 'R', 1);
				$this->Ln(5);
				$this->Cell(150, $this->RowHeight, 'NUMBER OF PATIENTS SERVED', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '=', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, $this->no_of_patients, 0, 1, 'R', 1);

				$this->Cell(150, $this->RowHeight, 'NUMBER OF REQUESTS', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '=', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, $this->no_of_requests, 0, 1, 'R', 1);

				$this->Cell(150, $this->RowHeight, 'NUMBER OF SERVICES REQUESTED', 0, 0, 'L', 0);
				$this->Cell(15, $this->RowHeight, '=', 0, 0, 'L', 1);
				$this->Cell(30, $this->RowHeight, $this->no_of_services, 0, 1, 'R', 1);

	}

		$cols = array();
	}

	#edited by VAN 03-18-2011
	#overhaul the code
	function FetchData() {
		global $db;

		#edited by VAN 05-12-2010
		$db->Execute("CALL sp_populate_lab_income('".$this->fromdate."','".$this->todate."')");

		# OTHER => other charges, ignore the CHARGE column

		$sql = "SELECT SQL_CALC_FOUND_ROWS
							DATE_,
							SHIFT,
							IF(DATE_FORMAT(SHIFT, '%i:%s')!='00:00',DATE_FORMAT(SHIFT, '%H'),TRUNCATE(ABS(DATE_FORMAT(SHIFT, '%H')-0.1),0) ) AS LOWERTIME,
							SUM(AMT_PAID+PHS+LINGAP+CMAP+PHIC+SOCIALIZED+OTHER) AS AMT_BILLED,
							SUM(AMT_PAID) AS AMT_PAID,
							SUM(Charity) AS Charity,
							SUM(PHS) AS PHS,
							SUM(LINGAP) AS LINGAP,
							SUM(CMAP) AS CMAP,
							SUM(PHIC) AS PHIC,
							SUM(SOCIALIZED) AS SOCIALIZED,
							SUM(OTHER) AS CHARGE,
							SUM(RDU) AS RDU
							FROM seg_rep_lab_income_tbl
							WHERE 	".$this->service_list_cond." DATE_ BETWEEN '".$this->fromdate."' AND '".$this->todate."'
							".$this->group_cond."
							".$this->enctype."
							GROUP BY DATE_,
											IF(DATE_FORMAT(SHIFT, '%i:%s')!='00:00',
												 DATE_FORMAT(SHIFT, '%H'),
												 TRUNCATE(ABS(DATE_FORMAT(SHIFT, '%H')-0.1),0) )";

		// echo "".$sql;
		$result=$db->Execute($sql);

		$sql_serv = "SELECT SQL_CALC_FOUND_ROWS
								count(service_code) AS no_of_services
								FROM seg_rep_lab_income_tbl
								WHERE ".$this->service_list_cond." DATE_ BETWEEN '".$this->fromdate."' AND '".$this->todate."'
								".$this->group_cond."
								".$this->enctype.
								"GROUP BY service_code";
		#echo "<br><br>sql_serv = ".$sql_serv;
		$result_serv = $db->Execute($sql_serv);

		if (is_object($result_serv))
			$this->no_of_services = $result_serv->RecordCount();
		#echo $this->no_of_services;
		if (!$this->no_of_services)
			$this->no_of_services = 0;

		$sql_pat = "SELECT SQL_CALC_FOUND_ROWS
								count(pid) AS no_of_services
								FROM seg_rep_lab_income_tbl
								WHERE ".$this->service_list_cond." DATE_ BETWEEN '".$this->fromdate."' AND '".$this->todate."'
								".$this->group_cond."
								".$this->enctype.
								"GROUP BY pid";
		#echo "<br>sql = ".$sql_pat;

		$result_pat=$db->Execute($sql_pat);
		#echo "<br>count = ".$result->RecordCount();
		if (is_object($result_pat))
			$this->no_of_patients = $result_pat->RecordCount();

		if (!$this->no_of_patients)
			$this->no_of_patients = 0;

		$sql_ref = "SELECT SQL_CALC_FOUND_ROWS
								count(refno) AS no_of_services
								FROM seg_rep_lab_income_tbl
								WHERE ".$this->service_list_cond." DATE_ BETWEEN '".$this->fromdate."' AND '".$this->todate."'
								".$this->group_cond."
								".$this->enctype.
								"GROUP BY refno";
		#echo "<br>sql = ".$sql_ref;
		$result_ref=$db->Execute($sql_ref);
		#echo "<br>count = ".$result->RecordCount();
		if (is_object($result_ref))
			$this->no_of_requests = $result_ref->RecordCount();

		if (!$this->no_of_requests)
			$this->no_of_requests = 0;

		#edited by Cherry 04-21-09
		if ($result) {
			$this->_count = $result->RecordCount();
			#echo  $this->_count;
			$this->Data=array();

			while ($row=$result->FetchRow()) {
					 $lowertime = $row['LOWERTIME'].":00:00";
					 $upper = $row['LOWERTIME'] + 1;
					 $uppertime = $upper.":00:00";
					 #echo "<br>time = ".$lowertime." - ".$uppertime;
					 $shift = date("h:00 A",strtotime($lowertime)).' - '.date("h:00 A",strtotime($uppertime));
					 $DATE = date("m/d/Y",strtotime($row['DATE_']));

					 $total_amt_billed += $row['AMT_BILLED'];
					 $total_amt_paid += $row['AMT_PAID'];
					 $total_amt_charity += $row['Charity'];
					 $total_amt_phs += $row['PHS'];
					 $total_amt_lingap += $row['LINGAP'];
					 // $total_amt_cmap += $row['CMAP'];
					 // $total_amt_socialized += $row['SOCIALIZED'];
					 $total_amt_phic += $row['PHIC'];
					 $total_amt_charge += $row['CHARGE'];
					 $total_amt_rdu += $row['RDU'];
					 // $total_amt_other += $row['OTHER'];

					 $total_amt_cao += $row['CAO'];
					 $total_amt_coh += $row['COH'];
					 $total_amt_hact += $row['HACT'];
					 $total_amt_mission += $row['MISSION'];
					 $total_amt_pcso += $row['PCSO'];
					 $total_amt_popcom += $row['POPCOM'];
					 $total_amt_qcpb += $row['QCPB'];
					 $total_amt_sss += $row['SSS'];


					 $this->Data[]=array(
							$DATE,
							$shift,
							number_format($row['AMT_BILLED'],2,'.',','),
							number_format($row['AMT_PAID'],2,'.',','),
							number_format($row['Charity'],2,'.',','),
							number_format($row['PHS'],2,'.',','),
							number_format($row['LINGAP'],2,'.',','),
							// number_format($row['CMAP'],2,'.',','),
							// number_format($row['SOCIALIZED'],2,'.',','),
							number_format($row['PHIC'],2,'.',','),
							//number_format($row['RDU'],2,'.',','),
							number_format($row['CHARGE'],2,'.',','),
							// number_format($row['OTHER'],2,'.',',')
					 );
			}

			$old_shift = $shift;
			$prev_date = $DATE;
	 }
				$this->SUM_AMT_BILLED = $total_amt_billed;
				$this->SUM_AMT_PAID = $total_amt_paid;
				$this->SUM_CHARITY = $total_amt_charity;
				$this->SUM_PHS = $total_amt_phs;
				$this->SUM_LINGAP = $total_amt_lingap;
				$this->SUM_CMAP = $total_amt_cmap;
				$this->SUM_SOCIALIZED = $total_amt_socialized;
				$this->SUM_PHIC = $total_amt_phic;
				$this->SUM_CHARGE = $total_amt_charge;
				$this->SUM_OTHER = $total_amt_other;
				$this->SUM_RDU = $total_amt_rdu;

				$this->SUM_CAO = $total_amt_cao;
				$this->SUM_COH = $total_amt_coh;
				$this->SUM_HACT = $total_amt_hact;
				$this->SUM_MISSION = $total_amt_mission;
				$this->SUM_PCSO = $total_amt_PCSO;
				$this->SUM_popcom = $total_amt_popcom;
				$this->SUM_QCPB = $total_amt_qcpb;
				$this->SUM_SSS = $total_amt_sss;

	}
}

$fromdate = $_GET['fromdate'];
$todate = $_GET['todate'];
$pat_type = $_GET['patient_type'];
#echo "pat = ".$pat_type;
#Added by Cherry 04-21-09
$servgroup = $_GET['serv_group'];
$service_code = $_GET['service_code']; #added by daryl
#echo "section = ".$servgroup;

$rep = new RepGen_Laboratory_Income($fromdate, $todate, $pat_type, $servgroup, $service_code);
$rep->AliasNbPages();
#$rep->FetchData();
$rep->Report();
?>
