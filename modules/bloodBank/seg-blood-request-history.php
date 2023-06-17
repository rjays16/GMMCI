<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

	class Lab_List_Request extends RepGen {
	var $date;
	var $colored = TRUE;
	var $pid;

	function Lab_List_Request ($pid,$encounter_nr) {
		global $db;
		#$this->RepGen("PATIENT REQUEST HISTORY","P",array(215.9,93.13));
		$this->RepGen("PATIENT BLOOD REQUEST HISTORY","P","Legal");
		$this->ColumnWidth = array(10,36,22,40,40,30,15,18);
		$this->RowHeight = 4.5;
		$this->TextHeight = 4;
		$this->Alignment = array('L','L','L','L','L','L','L');
		#$this->PageOrientation = "L";
		#$this->PageFormat = "Legal";
		$this->LEFTMARGIN=3;
		$this->DEFAULT_TOPMARGIN = 2;
		$this->NoWrap = false;

		$this->SetFillColor(0xFF);
		if ($this->colored) $this->SetDrawColor(0xDD);

		if ($pid)
			$this->pid = $pid;

		if($encounter_nr)
			$this->encounter_nr = $encounter_nr;
	}

	function Header() {

		global $root_path, $db;
		$objInfo = new Hospital_Admin();
		$srvObj=new SegLab;
		$person_obj=new Person;
		$enc_obj=new Encounter;
		$pers_obj=new Personell;

		$borderYes="1";
		$borderNo="0";
		$newLineYes="1";
		$newLineNo="0";
		$space=2;

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

		$total_w = 0;
		$this->Cell(17,4);
		$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
		$this->Cell(17,4);
		$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(17,4);
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(17,4);
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(17,4);
		$this->Ln(2);

		$this->SetFont('Arial','B',10);
		$this->Cell(17,4);
		$this->Cell($total_w,4,'PATIENT BLOOD REQUEST HISTORY',$border2,1,'C');

		$list = 'name_first, name_middle, name_last, date_birth, street_name, brgy_nr, mun_nr, sex, civil_status, age';
		#getPersonInfo
		#$person_obj->getValueByList($list,$this->pid);
		#$person = $person_obj->FetchRow();
		$person = $person_obj->BasicDataArray($this->pid);
		#print_r($person);
		#echo $person_obj->sql;

		$request_name = $person['name_first']." ".$person['name_middle']." ".$person['name_last'];
		$request_name = ucwords(strtolower($request_name));
		$request_name = htmlspecialchars($request_name);

		/*if ($person['street_name'])
				$request_address = trim($person['street_name'])." ".trim($person['brgy_name'])." ".trim($person['mun_name'])." ".trim($person['prov_name'])." ".trim($person['zipcode']);
		else
				$request_address = trim($person['brgy_name'])." ".trim($person['mun_name'])." ".trim($person['prov_name'])." ".trim($person['zipcode']);

		$request_address = ucwords(strtolower($request_address));
		$request_address = htmlspecialchars($request_address);
		*/

		$street_name = $person['street_name'];
		$brgy_name = $person['brgy_name'];
		$mun_name = $person['mun_name'];
		$prov_name = $person['prov_name'];

		if ($street_name){
		if ($brgy_name!="NOT PROVIDED")
			$street_name = $street_name.", ";
		else
			$street_name = $street_name.", ";
	}#else
		#$street_name = "";



	if ((!($brgy_name)) || ($brgy_name=="NOT PROVIDED"))
		$brgy_name = "";
	else
		$brgy_name  = $brgy_name.", ";

	if ((!($mun_name)) || ($mun_name=="NOT PROVIDED"))
		$mun_name = "";
	else{
		if ($brgy_name)
			$mun_name = $mun_name;
		#else
			#$mun_name = $mun_name;
	}

	if ((!($prov_name)) || ($prov_name=="NOT PROVIDED"))
		$prov_name = "";
	#else
	#  $prov_name = $prov_name;

	if(stristr(trim($mun_name), 'city') === FALSE){
		if ((!empty($mun_name))&&(!empty($prov_name))){
			if ($prov_name!="NOT PROVIDED")
				$prov_name = ", ".trim($prov_name);
			else
				$prov_name = "";
		}else{
			#$province = trim($prov_name);
			$prov_name = "";
		}
	}else
		$prov_name = ", ".trim($prov_name);

	$request_address = $street_name.$brgy_name.$mun_name.$prov_name;

		$this->SetFont("Arial","","8");
		$this->Cell(20,4,'HOSP # : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(25,4,$person['pid'],$borderNo,$newLineNo,'L');

		$this->Cell(60,4);
		$this->SetFont("Arial","","8");
		$this->Cell(15,4,'PHIC # : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");

		$insurance = $person_obj->getInsurance_nr($this->pid);

		if (!$insurance['insurance_nr'])
			$insurance_nr = 'Not a Member';
		else
			$insurance_nr = $insurance['insurance_nr'];

		$this->Cell(10,4,$insurance_nr,$borderNo,$newLineNo,'L');

		$this->Ln(4);
		$this->SetFont("Arial","","8");
		$this->Cell(20,4,'NAME : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(100,4,mb_strtoupper($request_name),$borderNo,$newLineNo,'L');

		$this->Ln(4);
		$this->SetFont("Arial","","8");
		$this->Cell(20,4,'AGE : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(10,4,$person['age'],$borderNo,$newLineNo,'L');
		$this->Cell(30,4);
		$this->SetFont("Arial","","8");
		$this->Cell(20,4,'GENDER : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");

		if ($person['sex']=='m')
			$sex = 'MALE';
		elseif($person['sex']=='f')
			$sex = 'FEMALE';
		else
			$sex = 'unspecified';

		$this->Cell(10,4,$sex,$borderNo,$newLineNo,'L');

		$this->Cell(30,4);
		$this->SetFont("Arial","","8");
		$this->Cell(30,4,'CIVIL STATUS : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
		if (empty($person['civil_status']))
				$person['civil_status'] = 'unspecified';
		$this->Cell(10,4,mb_strtoupper($person['civil_status']),$borderNo,$newLineNo,'L');

		$this->Ln(4);
		$this->SetFont("Arial","","8");
		$this->Cell(20,4,'ADDRESS : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(60,4,$request_address,$borderNo,$newLineNo,'L');

		$this->Ln(4);
		$this->SetFont("Arial","","8");
		$this->Cell(20,4,'CASE NO. : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");

		$this->Cell(60,4,$this->encounter_nr,$borderNo,$newLineNo,'L');


		$this->Ln(5);

		# Print table header

		$this->SetFont('ARIAL','B',8);

		if ($this->colored) $this->SetFillColor(255);
		$this->SetTextColor(0);
		$row=6;

		$this->Cell($this->ColumnWidth[0],$row,'',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'REQUEST DATE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$row,'REFERENC NO.',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'REQUEST',1,0,'C',1);
		$this->Cell($this->ColumnWidth[4],$row,'REQUESTING DOCTOR',1,0,'C',1);
		$this->Cell($this->ColumnWidth[5],$row,'REQUESTED BY',1,0,'C',1);
		$this->Cell($this->ColumnWidth[6],$row,'PAYMENT',1,0,'C',1);
		$this->Cell($this->ColumnWidth[7],$row,'STATUS',1,0,'C',1);
		$this->Ln();

	}

	function Footer()
	{
		$this->SetY(-20);
		#$this->SetFont('Arial','I',8);
		#$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:s A"),0,0,'R');
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
		$srvObj=new SegLab;

		if (!$this->_count) {
			$this->SetFont('Arial','B',10);
			$this->SetFillColor(255);
			$this->SetTextColor(0);
			$this->Cell(195, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}
		/*
		else{
			$this->Ln(5);
			$this->SetFont('Arial','',8);
			$this->Cell(190,4,'________________________________________',"",1,'R');
			$this->Cell(190,4,'Person In-Charge (Signature Over Printed Name)',"",1,'R');
		}
		*/
		$cols = array();
	}

	function FetchData($pid,$encounter_nr,$ref_source) {
		global $db;
		$srvObj=new SegLab;

		$requestObj = $srvObj->getAllRequestByPid($pid,$encounter_nr,$ref_source);
		#echo "<br>sql = ".$srvObj->sql;
		$this->_count = $srvObj->count;

		if ($requestObj) {
			$i=1;
			while($result=$requestObj->FetchRow()) {

				$date_request = date("m/d/Y h:i A",strtotime($result['serv_dt']));

				if ($result['request_doc'])
					$doctor = 'DR. '.mb_strtoupper($result['request_doc']);

				if ($result['is_cash'])
					$payment_mode = 'Cash';
				else
					$payment_mode = 'Charge';

				$this->Data[]=array(
					$i,
					$date_request,
					$result['refno'],
					$result['request_item'],
					$doctor,
					$result['encoder'],
					$payment_mode,
					$result['status']
				);
				$i++;
			}
		}/*else{
			print_r($db->ErrorMsg());
			exit;
		}*/

	}
}

$pid = $_GET['pid'];
$encounter_nr = $_GET['encounter_nr'];
$ref_source = $_GET['ref_source'];

$iss = new Lab_List_Request($pid,$encounter_nr);
$iss->AliasNbPages();
$iss->FetchData($pid,$encounter_nr,$ref_source);
$iss->Report();

?>