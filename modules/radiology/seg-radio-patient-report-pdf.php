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

	class RepGen_Radio_Patients extends RepGen {
	var $date;
	var $colored = TRUE;
	var $shift;
	var $section_name;
	var $user;
	var $date_req;
	var $patient_type;
	var  $ypos;
	var $patient_num;
	var $request_num;

	function RepGen_Radio_Patients ($req_date, $shift, $section_name, $user, $radtech, $patient_type) {
		global $db;
		#$this->RepGen("OPS & IN-PATIENT REGISTER","L","Legal");
		#array(width,height);
		$this->RepGen("OPS & IN-PATIENT REGISTER","L",array(215.9,330.2));
		#$this->SetAutoPageBreak(FALSE);
		$this->ColumnWidth = array(10,15,19,18,16,20,22,11,27,30,10,9,18,33,20,19,31);
		$this->RowHeight = 6;
		$this->TextHeight = 4;

		$this->Alignment = array('L','L','L','L','L','L','L','L','L','L','L','L','L','L','L','L','L');
		#$this->PageOrientation = "L";
		#$this->PageFormat = "Legal";
		$this->LEFTMARGIN=1;
		#$this->DEFAULT_TOPMARGIN = 2;
		$this->SetAutoPageBreak(FALSE);
		$this->NoWrap = false;

		if ($req_date)
			$this->date_req = mb_strtoupper(date("F d, Y",strtotime($req_date)));

		$this->shift = $shift;
		$this->section_name = $section_name;
		$this->user = mb_strtoupper($user);
		$this->radtech = mb_strtoupper($radtech);

		if ($patient_type)
			$this->patient_type  = $patient_type;

		$this->SetFillColor(0xFF);
		if ($this->colored) $this->SetDrawColor(0xDD);
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
		/*
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',100,8,30,40);
		$this->SetFont("Arial","I","9");
		$total_w = 0;
		$this->Cell(17,4);
			#$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(17,4);
		#$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
		$this->Ln(1);
		$this->SetFont("Arial","B","10");
		$this->Cell(17,4);
		#$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(17,4);
		#$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
		$this->Ln(1);
		*/
		$this->SetFont('Arial','I',8);
		$this->Cell(250,4);
		$this->Cell(100,4,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:s A"),0,0,'L');
		$this->Ln(3);
		$this->Cell(250,4);
		$this->SetFont('Arial','B',8);
		$this->Cell(100,4,'PREPARED BY : '.$this->user,$border2,1,'L');
		$this->Ln(1);
		$this->SetFont("Arial","B","10");
		$this->Cell(17,4);

		$this->Cell($total_w,4,'DEPARTMENT OF RADIOLOGICAL & IMAGING SCIENCES',$border2,1,'C');
		#$this->Ln(1);
		#$this->SetFont("Arial","B","13");
		#$this->Cell(17,4);
		#$this->Cell($total_w,4,$this->section_name,$border2,1,'C');
		$this->Ln(1);
		$this->SetFont('Arial','B',12);
		$this->Cell(17,5);

		if ($this->patient_type==0)
			$registry = "PATIENTS REGISTRY";
		elseif ($this->patient_type==1)
			$registry = "ER PATIENTS  REGISTRY";
		elseif ($this->patient_type==2)
			$registry = "IN-PATIENTS REGISTRY";
		elseif ($this->patient_type==3)
			$registry = "OUTPATIENTS REGISTRY";
		elseif ($this->patient_type==4)
			$registry = "WALK-IN PATIENTS REGISTRY";
		elseif ($this->patient_type==5)
			$registry = "OPS & WALK-IN PATIENTS REGISTRY";
		elseif ($this->patient_type==6)
			$registry = "ER & IN PATIENTS REGISTRY";

		$this->Cell($total_w,4,$registry,$border2,1,'C');
		#$this->Ln(1);
		#$this->Cell(17,5);
		#	$this->Cell($total_w,4,'RAD. TECH ON DUTY : '.$this->user,$border2,1,'C');
		#$this->Cell($total_w,4,'PREPARED BY : '.$this->user,$border2,1,'C');
		$this->Ln(1);
		if($this->radtech){
			$this->Cell(17,5);
			$this->Cell($total_w,4,'RAD. TECH ON DUTY : '.$this->radtech,$border2,1,'C');
			$this->Ln(1);
		}

		$this->Cell(5,5);
		$this->SetFont('Arial','',10);
		$this->Cell(15,4,'DATE : ',$border2,0,'L');
		$this->SetFont('Arial','B',12);
		$this->Cell(100,4,strtoupper($this->date_req)." ".date("(l)",strtotime($this->date_req)),0,0,'L');

		$this->Cell(10,5);
		$this->SetFont('Arial','',10);
		$this->Cell(20,4,'TIME : ',$border2,0,'L');
		$this->SetFont('Arial','B',12);
		$this->Cell(100,4,$this->shift,0,0,'L');
		$this->Cell(10,5);
		$this->SetFont('Arial','',10);
		$this->Cell(40,4,'Total Amount Collected : ',$border2,0,'L');
		$this->SetFont('Arial','B',12);
		$this->Cell(40,4,'Php '.$this->total_amount,0,1,'L');

		$this->Cell(5,5);
		$this->SetFont('Arial', '', 10);
		$this->Cell(45, 4, 'Total Number of Patients : ', $border2, 0, 'L');
		$this->SetFont('Arial', 'B', 12);
		$this->Cell(100, 4, $this->patient_num, $border2,1,'L');

		$this->Cell(5,5);
		$this->SetFont('Arial', '', 10);
		$this->Cell(45, 4, 'Total Number of Requests : ', $border2, 0, 'L');
		$this->SetFont('Arial', 'B', 12);
		$this->Cell(100, 4, $this->request_num, $border2,1,'L');

		#$this->SetFont('Arial','B',10);
		#$this->Cell(50,10,'Total Amount Collected = Php '.$this->total_amount,0,1,'L');

		#$this->SetFont('Arial','B',9);
		#$this->Cell(17,5);

		$this->Ln(3);

		# Print table header

		$this->SetFont('ARIAL','B',8.5);
		#if ($this->colored) $this->SetFillColor(0xED);
		if ($this->colored) $this->SetFillColor(255);
		$this->SetTextColor(0);
		$row=6;
		$woborder = 0;
		$wborder = 1;
		/*
		$this->Cell($this->ColumnWidth[0],$row,'HRN',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'SERVICE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$row,'O.R. NO.',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'AMOUNT',1,0,'C',1);
		$this->Cell($this->ColumnWidth[4],$row,"RID",1,0,'C',1);
		$this->Cell($this->ColumnWidth[5],$row,"REF. NO.",1,0,'C',1);
		$this->Cell($this->ColumnWidth[6],$row,'TIME',1,0,'C',1);
		$this->Cell($this->ColumnWidth[7],$row,'NAME OF PATIENT',1,0,'C',1);
		$this->Cell($this->ColumnWidth[8],$row,'ADDRESS',1,0,'C',1);
		$this->Cell($this->ColumnWidth[9],$row,'AGE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[10],$row,'SEX',1,0,'C',1);
		$this->Cell($this->ColumnWidth[11],$row,'BIRTHDAY',1,0,'C',1);
		$this->Cell($this->ColumnWidth[12],$row,'ADMITTING DIAGNOSIS',1,0,'C',1);
		$this->Cell($this->ColumnWidth[13],$row,'PHYSICIAN',1,0,'C',1);
		$this->Cell($this->ColumnWidth[14],$row,'WARD ',1,0,'C',1);
		$this->Cell($this->ColumnWidth[15],$row,'EXAMINATION',1,0,'C',1);
		*/
		#$this->MultiCell(w,h,tx,bor,align);

		$x = $this->GetX();
		$y = $this->GetY();

		$this->SetXY($x, $y);
		$this->MultiCell($this->ColumnWidth[0],$row*2,'',$wborder,'L');

		$add = $this->ColumnWidth[0];
		$this->SetXY($x+$add, $y);
		$this->MultiCell($this->ColumnWidth[1],$row,'Hospital Number',$wborder,'L');

		$add = $this->ColumnWidth[0]+$this->ColumnWidth[1];
		$this->SetXY($x+$add, $y);
		$this->MultiCell($this->ColumnWidth[2],$row*2,'SERVICE',$wborder,'L');

		$add = $this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2];
		$this->SetXY($x+$add, $y);
		$this->MultiCell($this->ColumnWidth[3],$row*2,'O.R. NO.',$wborder,'L');

		$add = $this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3];
		$this->SetXY($x+$add, $y);
		$this->MultiCell($this->ColumnWidth[4],$row*2,'AMOUNT',$wborder,'L');

		$add = $this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3]+$this->ColumnWidth[4];
		$this->SetXY($x+$add, $y);
		$this->MultiCell($this->ColumnWidth[5],$row*2,'RID',$wborder,'L');

		$add = $this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3]+$this->ColumnWidth[4]+$this->ColumnWidth[5];
		$this->SetXY($x+$add, $y);
		$this->MultiCell($this->ColumnWidth[6],$row*2,'REF. NO.',$wborder,'L');

		$add = $this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3]+$this->ColumnWidth[4]+$this->ColumnWidth[5]+$this->ColumnWidth[6];
		$this->SetXY($x+$add, $y);
		$this->MultiCell($this->ColumnWidth[7],$row*2,'TIME',$wborder,'L');

		$add = $this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3]
						+$this->ColumnWidth[4]+$this->ColumnWidth[5]+$this->ColumnWidth[6]+$this->ColumnWidth[7];
		$this->SetXY($x+$add, $y);
		$this->MultiCell($this->ColumnWidth[8],$row,'NAME OF PATIENT',$wborder,'L');

		$add = $this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3]
						+$this->ColumnWidth[4]+$this->ColumnWidth[5]+$this->ColumnWidth[6]+$this->ColumnWidth[7]+$this->ColumnWidth[8];
		$this->SetXY($x+$add, $y);
		$this->MultiCell($this->ColumnWidth[9],$row*2,'ADDRESS',$wborder,'L');

		$add = $this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3]
						+$this->ColumnWidth[4]+$this->ColumnWidth[5]+$this->ColumnWidth[6]+$this->ColumnWidth[7]+$this->ColumnWidth[8]+$this->ColumnWidth[9];
		$this->SetXY($x+$add, $y);
		$this->MultiCell($this->ColumnWidth[10],$row*2,'AGE',$wborder,'L');

		$add = $this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3]+$this->ColumnWidth[4]
						+$this->ColumnWidth[5]+$this->ColumnWidth[6]+$this->ColumnWidth[7]+$this->ColumnWidth[8]+$this->ColumnWidth[9]+$this->ColumnWidth[10];
		$this->SetXY($x+$add, $y);
		$this->MultiCell($this->ColumnWidth[11],$row*2,'SEX',$wborder,'L');

		$add = $this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3]+$this->ColumnWidth[4]
						+$this->ColumnWidth[5]+$this->ColumnWidth[6]+$this->ColumnWidth[7]+$this->ColumnWidth[8]+$this->ColumnWidth[9]+$this->ColumnWidth[10]+$this->ColumnWidth[11];
		$this->SetXY($x+$add, $y);
		$this->MultiCell($this->ColumnWidth[12],$row*2,'BIRTHDAY',$wborder,'L');

		$add = $this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3]+$this->ColumnWidth[4]
						+$this->ColumnWidth[5]+$this->ColumnWidth[6]+$this->ColumnWidth[7]+$this->ColumnWidth[8]+$this->ColumnWidth[9]
						+$this->ColumnWidth[10]+$this->ColumnWidth[11]+$this->ColumnWidth[12];
		$this->SetXY($x+$add, $y);
		$this->MultiCell($this->ColumnWidth[13],$row,'ADMITTING DIAGNOSIS',$wborder,'L');

		$add = $this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3]+$this->ColumnWidth[4]
						+$this->ColumnWidth[5]+$this->ColumnWidth[6]+$this->ColumnWidth[7]+$this->ColumnWidth[8]+$this->ColumnWidth[9]
						+$this->ColumnWidth[10]+$this->ColumnWidth[11]+$this->ColumnWidth[12]+$this->ColumnWidth[13];
		$this->SetXY($x+$add, $y);
		$this->MultiCell($this->ColumnWidth[14],$row*2,'PHYSICIAN',$wborder,'L');

		$add = $this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3]+$this->ColumnWidth[4]
						+$this->ColumnWidth[5]+$this->ColumnWidth[6]+$this->ColumnWidth[7]+$this->ColumnWidth[8]+$this->ColumnWidth[9]
						+$this->ColumnWidth[10]+$this->ColumnWidth[11]+$this->ColumnWidth[12]+$this->ColumnWidth[13]+$this->ColumnWidth[14];
		$this->SetXY($x+$add, $y);
		$this->MultiCell($this->ColumnWidth[15],$row*2,'WARD',$wborder,'L');

		 $add = $this->ColumnWidth[0]+$this->ColumnWidth[1]+$this->ColumnWidth[2]+$this->ColumnWidth[3]+$this->ColumnWidth[4]
						+$this->ColumnWidth[5]+$this->ColumnWidth[6]+$this->ColumnWidth[7]+$this->ColumnWidth[8]+$this->ColumnWidth[9]
						+$this->ColumnWidth[10]+$this->ColumnWidth[11]+$this->ColumnWidth[12]+$this->ColumnWidth[13]+$this->ColumnWidth[14]+$this->ColumnWidth[15];
		$this->SetXY($x+$add, $y);
		$this->MultiCell($this->ColumnWidth[16],$row*2,'EXAMINATION',$wborder,'L');
		$y = $this->GetY();
		#$this->ypos = $y;
		#$this->Ln();

	}

	function Footer()
	{
		$this->SetY(-7);
		/*
		$this->SetFont('Arial','B',10);
		$this->Cell(0,10,'Total Amount Collected = Php '.$this->total_amount,0,0,'R');
		$this->Ln(5);
		*/
		#$this->SetFont('Arial','I',8);
		#$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:s A"),0,0,'R');
	}

	function BeforeRow() {
		$this->FONTSIZE = 8.5;

		if ($this->colored) {
			if (($this->ROWNUM%2)>0)
				#$this->FILLCOLOR=array(0xee, 0xef, 0xf4);
				$this->FILLCOLOR=array(255,255,255);
			else
				$this->FILLCOLOR=array(255,255,255);
			$this->DRAWCOLOR = array(0xDD,0xDD,0xDD);
		}
	}

	function BeforeData() {
	$this->FONTSIZE = 8.5;

		if ($this->colored) {
			$this->DrawColor = array(0xDD,0xDD,0xDD);
		}
	}

	function BeforeCellRender() {
		$this->FONTSIZE = 8.5;

		if ($this->colored) {
			if (($this->RENDERPAGEROWNUM%2)>0)
				#$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
				$this->RENDERCELL->FillColor=array(255,255,255);
			else
				$this->RENDERCELL->FillColor=array(255,255,255);
		}
	}

	function AfterData() {
		global $db;

		if (!$this->_count) {
			$this->SetFont('Arial','B',10);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			#$x = $this->SetX();
			#$this->SetY($this->ypos);
			$this->Cell(329, 10, "No records found for this report...", 1, 1, 'L', 1);
		}
		$this->SetY($this->ypos);
		$cols = array();
	}

	function GetTotalPatient($request_date, $fromhour, $tohour, $section){
		global $db;

		if ($section!=0)
			$group_cond = "AND g.department_nr='".$section."'";
		else
			$group_cond  = "";

		if ($this->patient_type==0)
		 $enc_type = "";
		elseif ($this->patient_type==1)
			$enc_type = "AND encounter_type IN (1)";
		elseif ($this->patient_type==2)
			$enc_type = "AND encounter_type IN (3,4)";
		elseif ($this->patient_type==3)
			$enc_type = "AND encounter_type IN (2)";
		elseif ($this->patient_type==4)
			$enc_type = "AND encounter_type IS NULL";
		elseif ($this->patient_type==5)
			$enc_type = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
		elseif ($this->patient_type==6)
			$enc_type = "AND encounter_type IN (1,3,4)";

		$sql_total = "SELECT p.pid FROM care_person AS p
									INNER JOIN seg_radio_serv AS rs ON rs.pid=p.pid
									INNER JOIN care_test_request_radio AS rd ON rd.refno = rs.refno
									INNER JOIN seg_radio_services AS s ON s.service_code=rd.service_code
									INNER JOIN seg_radio_service_groups AS g ON g.group_code=s.group_code
									LEFT JOIN seg_pay AS sp ON sp.pid=rs.pid AND sp.encounter_nr=rs.encounter_nr AND (ISNULL(sp.cancel_date) OR sp.cancel_date='0000-00-00 00:00:00')
									LEFT JOIN seg_pay_request AS pay ON pay.or_no=sp.or_no AND pay.ref_no=rs.refno AND ref_source='RD' AND pay.service_code=rd.service_code
									LEFT JOIN seg_granted_request AS gd ON gd.ref_no=rs.refno AND gd.ref_source='RD' AND gd.service_code=rd.service_code
									LEFT JOIN care_encounter AS e ON e.encounter_nr=rs.encounter_nr
									WHERE rs.request_date LIKE '".$request_date."'
									AND rs.request_time >= '".$fromhour."' AND rs.request_time <= '".$tohour."'
									AND (pay.or_no!='' OR gd.grant_no!='' OR pay.or_no IS NOT NULL OR gd.grant_no IS NOT NULL OR is_cash=0)
									".$group_cond."
									".$enc_type."
									GROUP BY p.pid";
		#echo $sql_total;
		$result_total=$db->Execute($sql_total);
		if ($result_total){
			$this->patient_num = $result_total->RecordCount();
		}else{
			$this->patient_num = 0;
		}


	}

	function FetchData($request_date, $fromhour, $tohour, $section, $encoder, $dept_obj, $ward_obj, $is_alphabetical) {
		global $db;

		$this->GetTotalPatient($request_date, $fromhour, $tohour, $section);

		if ($section!=0)
			$group_cond = "AND g.department_nr='".$section."'";
		else
			$group_cond  = "";

		if ($this->patient_type==0)
		 $enc_type = "";
		elseif ($this->patient_type==1)
			$enc_type = "AND encounter_type IN (1)";
		elseif ($this->patient_type==2)
			$enc_type = "AND encounter_type IN (3,4)";
		elseif ($this->patient_type==3)
			$enc_type = "AND encounter_type IN (2)";
		elseif ($this->patient_type==4)
			$enc_type = "AND encounter_type IS NULL";
		elseif ($this->patient_type==5)
			$enc_type = "AND (encounter_type IN (2) OR encounter_type IS NULL)";
		elseif ($this->patient_type==6)
			$enc_type = "AND encounter_type IN (1,3,4)";

		if ($is_alphabetical)
			$orderby = " ORDER BY p.name_last, p.name_first, p.name_middle, rs.request_time";
		else
			$orderby = " ORDER BY rs.request_time, p.name_last, p.name_first, p.name_middle";

		$sql = "SELECT rd.clinical_info, r.rid, d.name_formal, rs.pid,rs.encounter_nr, rs.refno, rs.request_date, rs.request_time, rs.is_cash,
							rd.batch_nr AS film_no, rd.service_code,rd.price_cash, s.name, p.name_last, p.name_first,
							p.name_middle, p.date_birth, p.sex,
							IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age,
							p.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, spr.prov_name,
							sr.region_name, e.encounter_type,e.current_att_dr_nr,e.consulting_dr_nr,
							e.current_ward_nr, e.current_room_nr, e.current_dept_nr, rs.create_id,
							rs.modify_id, pay.or_no, pay.amount_due, gd.grant_no, s.price_cash AS orig_cash_price,
							s.price_charge AS orig_charge_price, rs.discountid,rd.request_doctor, IF(rd.clinical_info!='',rd.clinical_info,e.er_opd_diagnosis) AS adm_diagnosis,
							CONCAT(	'Dr. ',CAST(SUBSTRING((SELECT name_first FROM care_person AS p WHERE p.pid=pr.pid),1,1) AS BINARY),
										IF(
											 (SELECT name_first FROM care_person AS p WHERE p.pid=pr.pid)='', ' ','. '
										 ),
									SUBSTRING((SELECT name_middle FROM care_person AS p WHERE p.pid=pr.pid), 1, 1),
										IF(
											 (SELECT name_middle FROM care_person AS p WHERE p.pid=pr.pid)='', ' ','. '
										 ),
									(SELECT name_last FROM care_person AS p WHERE p.pid=pr.pid)) AS dr_name
							FROM seg_radio_serv AS rs
							INNER JOIN care_test_request_radio AS rd ON rd.refno = rs.refno
							INNER JOIN seg_radio_services AS s ON s.service_code=rd.service_code
							INNER JOIN care_person AS p ON p.pid=rs.pid
							INNER JOIN seg_radio_id AS r ON r.pid=rs.pid
							INNER JOIN seg_radio_service_groups AS g ON g.group_code=s.group_code
							INNER JOIN care_department AS d ON d.nr=g.department_nr
							LEFT JOIN care_personell AS pr ON pr.nr=rd.request_doctor
							LEFT JOIN seg_pay AS sp ON sp.pid=rs.pid AND sp.encounter_nr=rs.encounter_nr
												AND (ISNULL(sp.cancel_date) OR sp.cancel_date='0000-00-00 00:00:00')
							LEFT JOIN seg_pay_request AS pay ON pay.or_no=sp.or_no AND pay.ref_no=rs.refno
												AND ref_source='RD' AND pay.service_code=rd.service_code
							LEFT JOIN seg_granted_request AS gd ON gd.ref_no=rs.refno AND gd.ref_source='RD'
												AND gd.service_code=rd.service_code
							LEFT JOIN care_encounter AS e ON e.encounter_nr=rs.encounter_nr
							LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
							LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
							LEFT JOIN seg_provinces AS spr ON spr.prov_nr=sm.prov_nr
							LEFT JOIN seg_regions AS sr ON sr.region_nr=spr.region_nr

							WHERE rs.request_date LIKE '".$request_date."'
							AND rs.request_time >= '".$fromhour."' AND rs.request_time <= '".$tohour."'
							AND (pay.or_no!='' OR gd.grant_no!='' OR pay.or_no IS NOT NULL OR gd.grant_no IS NOT NULL OR is_cash=0)
							".$group_cond."
							".$enc_type."
							GROUP BY rs.pid,rd.service_code".$orderby;
							/*GROUP BY rs.pid,rs.refno,rd.service_code".$orderby*/
		#echo "sql = ".$sql;
		$result=$db->Execute($sql);
		if ($result) {
			$this->_count = $result->RecordCount();
			$this->Data=array();
			$i=0;
			$cnt = 0;
			$tmp = 1;
			while ($row=$result->FetchRow()) {
				$patient = mb_strtoupper($row["name_last"]).", ".mb_strtoupper($row["name_first"])." ".mb_strtoupper($row["name_middle"]);
				$address = $row["street_name"].", ".$row["mun_name"];

				if ($row["encounter_nr"]){
					if ($row['encounter_type'] == 1){
						$loc_name = "ER";
					}elseif (($row['encounter_type'] == 3)||($row['encounter_type'] == 4)){
						$loc_code = $row['current_ward_nr'];
						if ($loc_code)
							$ward = $ward_obj->getWardInfo($loc_code);

						#$loc_name = stripslashes($ward['ward_id']);
						$loc_name = stripslashes($ward['name'])." Rm#".$row['current_room_nr'];;
					}elseif ($row['encounter_type'] == 2){
						$loc_code = $row['current_dept_nr'];
						if ($loc_code)
							$dept = $dept_obj->getDeptAllInfo($loc_code);
						#$loc_name = stripslashes($dept['id']);
						$loc_name = stripslashes($dept['name_formal']);
					}else{
						$loc_name = "WALK-IN";
					}
				}else{
					$loc_name = "WALK-IN";
				}

				if ($row['is_cash']){
					if ($row["or_no"]){
						$or_no = $row["or_no"];
						#$amount = number_format($row["amount_due"],2,".",",");
						$amount_paid = $row["amount_due"];

						$dif = $row['orig_cash_price'] - $row["amount_due"];

						#if ($row['orig_cash_price']!=$row["amount_due"])
							#$amount = number_format($row['orig_cash_price'],2,".",",")." - ".number_format($dif,2,".",",")." = ".number_format($row["amount_due"],2,".",",");
						#	$amount = number_format($row["amount_due"],2,".",",")." (".number_format($dif,2,".",",")." is subsidized ".$row['discountid'].")";
						#else
							$amount = number_format($row["amount_due"],2,".",",");
					}else{
						#$or_no = "SS".$row["grant_no"];
						$or_no = "CLASS ".$row['discountid'];
						#$amount = "subsidized";
						#$amount = "subsidized"." (".$row['discountid'].")";
						$amount = "charity";
						$amount_paid = 0;
					}

					/*
					if ($row["amount_due"]){
						$amount = $row["amount_due"];
					}else{
						$amount = $row["price_cash"];
					}
					*/
				}else{
					$or_no = "CHARGE";
					#$amount = $row["price_cash"];
					$amount = "charge";
					$amount_paid = 0;
				}

				$total_amount = $total_amount + $amount_paid;
				$this->total_amount = number_format($total_amount,2,'.',',');

				if($row["age"])
					$age= $row["age"];
				else
					$age= "unknown";

				if($row["date_birth"]!='0000-00-00')
					$bdate = date("m/d/Y",strtotime($row["date_birth"]));
				else
					$bdate = "unknown";

				$current_ref = $row["refno"];
				$pid = $row["pid"];
				$refno = $row["refno"];
				$sex = mb_strtoupper($row["sex"]);

				#Added by Cherry 10-22-10
				$curr_pid = $pid;

				if($row["request_time"]!="00:00:00")
					$time = date("h:i A",strtotime($row["request_time"]));
				else
					$time = "12:00 AM";

				if ($row['clinical_info'])
					 $diagnosis = $row['clinical_info'];
				else
					 $diagnosis = $row["adm_diagnosis"];

				if ($current_ref==$prev_ref){
					$refno="";
					$patient = "";
					$time = "";
					$age = "";
					$sex = "";
					$bdate = "";
					$address = "";
				}
				#$i,
				if($curr_pid != $prev_pid){
						#$cnt++;
						$i = $cnt;
						$i++;
						$cnt++;
				}else{
						$i = "";
				}

				$this->Data[]=array(
					$i,
					$pid,
					$row['name_formal'],
					$or_no,
					$amount,
					$row["rid"],
					$refno,
					$time,
					$patient,
					$address,
					$age,
					$sex,
					$bdate,
					$diagnosis,
					mb_strtoupper($row["dr_name"]),
					$loc_name,
					$row["name"]);

					$prev_ref = $row["refno"];
					#Added by Cherry 10-22-10
					$prev_pid = $pid;

					$tmp++;
			}
			/*if($row){
				$this->request_num = $tmp;
			}else{
				$this->request_num = 0;
			}               */
			$this->request_num = $this->_count;
		}
		else {
			print_r($sql);
			print_r($db->ErrorMsg());
			exit;
			# Error
		}
	}
}

require_once($root_path.'include/care_api_classes/class_radiology.php');
$srvObj=new SegRadio();
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;
require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj=new Ward;

$req_date = $_GET['req_date'];
$fromtime = $_GET['fromtime'];
$totime = $_GET['totime'];
$user = $_GET['user'];
$radtech = $_GET['radtech'];
#echo "rad = ".$radtech;
$patient_type = $_GET['pat_type'];

$section = $_GET['rpt_group'];

if ($section){
	$service_info = $srvObj->getAllRadioDeptInfo($section);
	#echo $srvObj->sql;
	#echo "hre = ".$req_date;

	#$section_name = strtoupper($service_info['name_formal'])." (".strtoupper($service_info['dept_name']).")";
	$section_name = strtoupper($service_info['name_formal']);
}else{
	$section_name = "ALL RADIOLOGY SECTION";
}
$shift = date("h:i A",strtotime($fromtime))." - ".date("h:i A",strtotime($totime));

$fromtime = date("H:i:s",strtotime($fromtime));
$totime = date("H:i:s",strtotime($totime));

$is_alphabetical = $_GET['is_alphabetical'];

$iss = new RepGen_Radio_Patients($req_date, $shift, $section_name, $user, $radtech, $patient_type);
$iss->AliasNbPages();
$iss->FetchData($req_date, $fromtime, $totime, $section, $user, $dept_obj, $ward_obj, $is_alphabetical);
$iss->Report();
?>