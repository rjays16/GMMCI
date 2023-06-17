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

	class RepGen_Lab_Patients extends RepGen {
	var $date;
	var $colored = TRUE;
	var $shift;
	var $section_name;
	var $user;
	var $date_req;
	var $patient_type;
	var $total_amount;
	var $total_gross_amount;
	var $total_total_subsidized;

	function RepGen_Lab_Patients ($req_date, $shift, $section_name, $user, $patient_type) {
		global $db;
		#$this->RepGen("PATIENT'S LIST","L","Legal");
		$this->RepGen("PATIENT'S LIST","L","A4");
		$this->SetAutoPageBreak(FALSE);
		# 165
		$this->ColumnWidth = array(20,22,50,22,20,17,7,7,45,33,25,20);
		#$this->RowHeight = 7;
		$this->RowHeight = 4.5;
		$this->TextHeight = 5;

		$this->Alignment = array('C','C','L','C','C','C','C','C','L','L','C','R');
		#$this->PageOrientation = "L";
		#$this->PageFormat = "Legal";
		$this->LEFTMARGIN=5;
		$this->DEFAULT_TOPMARGIN = 5;
		$this->NoWrap = false;

		if ($req_date)
			$this->date_req = mb_strtoupper(date("F d, Y",strtotime($req_date)));
		$this->shift = $shift;

		$this->section_name = $section_name;

		$this->user = mb_strtoupper($user);

		$this->patient_type = mb_strtoupper($patient_type);

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
			$row['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";
		}
		/*
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',50,8,30,40);
		$this->SetFont("Arial","I","9");
		$total_w = 0;
		$this->Cell(17,4);
			#$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(17,4);
		#$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
			$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(17,4);
			#$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(17,4);
			#$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
		$this->Ln(2);
		*/
		$this->SetFont("Arial","B","10");

		$this->Cell(17,4);
			$this->Cell($total_w,4,'DEPARTMENT OF PATHOLOGY AND CLINICAL LABORATORIES',$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","13");
		$this->Cell(17,4);

			$this->Cell($total_w,4,$this->section_name,$border2,1,'C');
			$this->Ln(2);
		 $this->SetFont('Arial','B',12);
		$this->Cell(17,5);
			$this->Cell($total_w,4,'PATIENTS\' LIST ('.$this->patient_type.')',$border2,1,'C');
		 $this->Ln(2);
		$this->Cell(17,5);
			$this->Cell($total_w,4,'LABORATORY STAFF ON DUTY : '.$this->user,$border2,1,'C');
		 $this->Ln(2);

		$this->Cell(5,5);
		$this->SetFont('Arial','',10);
			$this->Cell(15,4,'DATE : ',$border2,0,'L');
		$this->SetFont('Arial','B',12);
		$this->Cell(10,4,$this->date_req,0,0,'L');

		$this->Cell(180,5);
		$this->SetFont('Arial','',10);
		$this->Cell(20,4,'SHIFT : ',$border2,0,'L');
		$this->SetFont('Arial','B',12);
		$this->Cell(10,4,$this->shift,0,1,'L');
		$this->Ln(1);

		$this->SetFont('Arial','B',9);
		$this->Cell(17,5);

		$this->Ln(1);

		# Print table header

			$this->SetFont('ARIAL','B',8);
		#if ($this->colored) $this->SetFillColor(0xED);
		if ($this->colored) $this->SetFillColor(255);
		$this->SetTextColor(0);
		$row=6;
		#$this->Cell(0,4,'',1,1,'C');
		#$this->Cell($this->ColumnWidth[0],$row,'',1,0,'C',1);
		$this->Cell($this->ColumnWidth[0],$row,'DATE / TIME',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'REFERENCE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$row,'PATIENT\'S NAME',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'HRN',1,0,'C',1);
		$this->Cell($this->ColumnWidth[4],$row,"BIRTHDATE",1,0,'C',1);
		$this->Cell($this->ColumnWidth[5],$row,'AGE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[6],$row,'SEX',1,0,'C',1);
		$this->Cell($this->ColumnWidth[7],$row,'QTY',1,0,'C',1);
		$this->Cell($this->ColumnWidth[8],$row,'PROCEDURE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[9],$row,'FROM',1,0,'C',1);
		$this->Cell($this->ColumnWidth[10],$row,'OR NO.',1,0,'C',1);
		$this->Cell($this->ColumnWidth[11],$row,'PAID',1,0,'C',1);
		$this->Ln();
	}

	function Footer()
	{
		$this->SetY(-7);
		/*
		$this->SetFont('Arial','B',8);
		$this->Cell(0,10,'TOTAL GROSS AMOUNT = Php '.$this->total_gross_amount,0,0,'R');
		$this->Ln(4);
		$this->Cell(0,10,'TOTAL AMOUNT COLLECTED = Php '.$this->total_amount,0,0,'R');
		$this->Ln(4);
		$this->Cell(0,10,'TOTAL AMOUNT SUBSIDIZED = Php '.$this->total_total_subsidized,0,0,'R');
		$this->Ln(6);
		*/
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:s A"),0,0,'R');
	}

	function BeforeRow() {
		$this->FONTSIZE = 10;
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
		if ($this->colored) {
			$this->DrawColor = array(0xDD,0xDD,0xDD);
		}
	}

	function BeforeCellRender() {
		$this->FONTSIZE = 8;
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
			$this->Cell(273, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}else{
			$this->SetFont('Arial','B',8);
			$this->Cell(0,10,'TOTAL GROSS AMOUNT = Php '.$this->total_gross_amount,0,0,'R');
			$this->Ln(4);
			$this->Cell(0,10,'TOTAL AMOUNT COLLECTED = Php '.$this->total_amount,0,0,'R');
			$this->Ln(4);
			$this->Cell(0,10,'TOTAL AMOUNT SUBSIDIZED = Php '.$this->total_total_subsidized,0,0,'R');
			$this->Ln(6);
		}

		$cols = array();
	}

	function FetchData($request_date, $fromhour, $tohour, $section, $encoder, $enctype , $dept_obj, $ward_obj, $class) {
		global $db;
		$params = array();

		if($enctype)
			$params[] = $enctype;

		if ($section!='all')
			$params[] = "s.group_code='".$section."'";

		if (($class)&&($class!='all'))
			$params[] = "lr.grant_type = '".$class."' OR lr.discountid ='".$class."' OR ld.request_flag='".$class."'";
		else
			$params[] = "lr.is_cash=0 OR lr.is_tpl=1 OR lr.grant_type='' ";

		$sqlWhere=implode(") AND (",$params);
		$sql = "SELECT d.discount,lr.grant_type, ch.charge_name, lr.serv_dt, lr.serv_tm, lr.refno, lr.pid, lr.is_cash, lr.encounter_nr, \n".
								"fn_get_person_name(lr.pid) `name`, p.date_birth, p.sex, \n".
								 "IF(fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS age, \n".
									"ld.service_code, ld.price_cash, s.name AS exam, '1' AS qty, s.group_code, \n".
									"e.encounter_type,e.current_att_dr_nr,e.consulting_dr_nr, \n".
									"e.current_ward_nr, e.current_room_nr, e.current_dept_nr, lr.create_id, \n".
									"lr.modify_id, s.price_cash AS orig_cash_price, s.price_charge AS orig_charge_price \n".
									"FROM \n".
										"(SELECT * FROM seg_lab_serv lr WHERE  lr.serv_dt='".$request_date."' \n".
										"AND (lr.serv_tm  BETWEEN '".$fromhour."' AND '".$tohour."'))  lr \n".
									"INNER JOIN seg_lab_servdetails AS ld ON ld.refno=lr.refno \n".
									"INNER JOIN seg_lab_services AS s ON s.service_code=ld.service_code \n".
									"INNER JOIN seg_lab_service_groups AS g ON g.group_code=s.group_code \n".
									"LEFT JOIN care_encounter AS e ON e.encounter_nr=lr.encounter_nr \n".
									"INNER JOIN care_person AS p ON p.pid=lr.pid \n".
									"LEFT JOIN seg_type_charge AS ch ON ch.id=lr.grant_type \n".
									"LEFT JOIN seg_discount AS d ON d.discountid=lr.discountid \n".
									"WHERE ".
									 $sqlWhere.
									"ORDER BY lr.serv_dt, lr.serv_tm DESC, `name` ASC";
		/*echo "<pre>";
		print_r($sql);
		echo "</pre>";
		die("die!");*/
		$result=$db->Execute($sql);
		if ($result) {
			$this->_count = $result->RecordCount();
			$this->Data=array();
			$i=1;
			$total_gross_amount = 0;
			while ($row=$result->FetchRow()) {
				$patient = strtoupper($row['name']);
				$address = $row["street_name"].", ".$row["mun_name"];

				if ($row["encounter_nr"]){
					if ($row['encounter_type'] == 1){
						$loc_name = "ER";
					}elseif (($row['encounter_type'] == 3)||($row['encounter_type'] == 4)){
						$loc_code = $row['current_ward_nr'];
						if ($loc_code)
							$ward = $ward_obj->getWardInfo($loc_code);
						$loc_name = stripslashes($ward['name'])." Rm#".$row['current_room_nr'];
					}elseif ($row['encounter_type'] == 2){
						$loc_code = $row['current_dept_nr'];
						if ($loc_code)
							$dept = $dept_obj->getDeptAllInfo($loc_code);
						$loc_name = stripslashes($dept['id']);
					}else{
						$loc_name = "WALK-IN";
					}
				}else{
					$loc_name = "WALK-IN";
				}

				$date_requested = date("m/d/Y",strtotime($row["serv_dt"]))." ".date("h:i A",strtotime($row["serv_tm"]));

				if ($row['is_cash']){
					//added by cha, 11-23-2010
					//get seg_pay details here
					$sql_paid = "SELECT pr.or_no, pr.amount_due
																					FROM seg_pay_request AS pr
																					INNER JOIN seg_pay AS p ON p.or_no=pr.or_no
																					WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($row["refno"])."' AND pr.service_code='".$row['service_code']."'
																					AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00') LIMIT 1";
					$data_paid = $db->GetRow($sql_paid);
					$row["or_no"] = $data_paid["or_no"];
					if ($row["or_no"]){
						$or_no = $row["or_no"];
						$amount_paid = $data_paid["amount_due"];

						$dif = $row['orig_cash_price'] - $data_paid["amount_due"];

						$amount = number_format($data_paid["amount_due"],2,".",",");

					}elseif ($row["discountid"]){
						if ($row["discount"]=='1.00000000'){
							$or_no = $row["discountid"];
							$amount = "charity";
							$amount_paid = 0;
						}else{
							if ($row["type_charge"]!=0){
								$or_no = $row["charge_name"]." (".number_format($row["orig_cash_price"],2,'.',',').")";
								$amount_paid = $row["price_cash"];
								if (trim($row["discountid"]))
									$amount = number_format($amount_paid,2,'.',',')." (".$row["discountid"].")";
								else
									$amount = number_format($amount_paid,2,'.',',');
								}
							}
					}

					$gross_amount = $row['orig_cash_price'];
				}else{

					if ($row["grant_type"]!='paid'){
						$or_no = $row["charge_name"]." (".number_format($row["orig_cash_price"],2,'.',',').")";
						$amount_paid = $row["price_cash"];
						if (trim($row["discountid"]))
							$amount = number_format($amount_paid,2,'.',',')." (".$row["discountid"].")";
						else
							$amount = number_format($amount_paid,2,'.',',');
					}
					$gross_amount = $row['orig_charge_price'];
				}

				$total_amount = $total_amount + $amount_paid;
				$total_gross_amount = $total_gross_amount + $gross_amount;
				$total_subsidized = $total_gross_amount - $total_amount;

				$this->total_amount = number_format($total_amount,2,'.',',');
				$this->total_gross_amount = number_format($total_gross_amount,2,'.',',');
				$this->total_total_subsidized = number_format($total_subsidized,2,'.',',');

				if($row["age"])
					$age= $row["age"];
				else
					$age= "unknown";

				if($row["date_birth"]!='0000-00-00')
					$bdate = date("m/d/Y",strtotime($row["date_birth"]));
				else
					$bdate = "unknown";

				$sex = mb_strtoupper($row["sex"]);
				$refno = $row["refno"];
				$pid = $row["pid"];

				$current_ref = $row["refno"];
				if ($current_ref==$prev_ref){
					$patient = "";
					$date_requested = "";
					$refno="";
				}

				$this->Data[]=array(
					$date_requested,
					$refno,
					$patient,
					$pid,
					$bdate,
					$age,
					$sex,
					$row["qty"],
					$row["exam"],
					$loc_name,
					$or_no,
					$amount
				);

				$prev_ref = $row["refno"];
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

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab;
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;
require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj=new Ward;

$req_date = $_GET['req_date'];
$fromtime = $_GET['fromtime'];
$totime = $_GET['totime'];
$user = $_GET['user'];

$section = $_GET['rpt_group'];
$pat_type = $_GET['patient_type'];

$class = $_GET['class'];

#echo "patient_type = ".$patient_type;

if($section!='all'){
	$service_info = $srvObj->getAllLabGroupInfo($section);
	#echo $srvObj->sql;
	#echo "hre = ".$req_date;

	#$section_name = strtoupper($service_info['name_formal'])." (".strtoupper($service_info['dept_name']).")";
	$section_name = strtoupper($service_info['name']);
}else{
	$section_name = "ALL LABORATORY SECTION";
}
$shift = date("h:i A",strtotime($fromtime))." - ".date("h:i A",strtotime($totime));

if ($pat_type==1){
	#ER PATIENT
	$enctype = " AND e.encounter_type IN (1)";
	$patient_type = "ER";
}elseif ($pat_type==2){
	#ADMITTED PATIENT
	$enctype = " AND e.encounter_type IN (3,4)";
	$patient_type = "ADMITTED";
}elseif ($pat_type==3){
	#OUT PATIENT
	$enctype = " AND e.encounter_type IN (2)";
	$patient_type = "OPD";
}elseif ($pat_type==4){
	#WALK-IN PATIENT
	$enctype = " AND lr.encounter_nr=''";
	$patient_type = "WALKIN";
}elseif($pat_type==5){
	$enctype = " AND (e.encounter_type IN(2) OR lr.encounter_nr='')";
	$patient_type = "OPD & WALKIN";
}else{
	$enctype = "";
	$patient_type = "ALL PATIENTS";
}

$fromtime = date("H:i:s",strtotime($fromtime));
$totime = date("H:i:s",strtotime($totime));
#echo "type = ".$patient_type;
#echo "enctype = ".$enctype;
$iss = new RepGen_Lab_Patients($req_date, $shift, $section_name, $user, $patient_type);
$iss->AliasNbPages();
$iss->FetchData($req_date, $fromtime, $totime, $section, $user, $enctype, $dept_obj, $ward_obj, $class);
$iss->Report();

?>