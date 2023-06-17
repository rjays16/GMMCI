<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
	
/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

	class Lab_List_Request extends RepGen {
	var $date;
	var $colored = TRUE;
	var $shift;
	var $section_name;
	var $user;
	var $pid;
	var $refno;
	var $is_cash;
	var $section_name;

	function Lab_List_Request ($pid, $refno, $is_cash) {
		global $db;
		#$this->RepGen("PATIENT'S LIST","L","Legal");
		$this->RepGen("CLINICAL LABORATORY SERVICES","P","Letter");
		# 165
		$this->ColumnWidth = array(20,55,44,25,30,20);
		$this->RowHeight = 5;
		$this->Alignment = array('L','L','L','C','R','R');
		#$this->PageOrientation = "L";
		#$this->PageFormat = "Legal";
		$this->LEFTMARGIN=20;
		$this->DEFAULT_TOPMARGIN = 5;
		$this->NoWrap = false;
		
		$this->pid = $pid;
		$this->refno = $refno;
		$this->is_cash = $is_cash;
		
		$this->SetFillColor(0xFF);
		if ($this->colored) $this->SetDrawColor(0xDD);
	}
	
	function Header() {
		global $root_path, $db;
		$objInfo = new Hospital_Admin();
		$srvObj=new SegLab;
		$dept_obj=new Department;
		$person_obj=new Person;
		$enc_obj=new Encounter;
		$pers_obj=new Personell;
		$ward_obj=new Ward;
		
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
		
		$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',25,8,30,30);
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
		$this->SetFont("Arial","B","10");
		$this->Cell(17,4);
  		$this->Cell($total_w,4,'DEPARTMENT OF PATHOLOGY AND CLINICAL LABORATORIES',$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","13");
		$this->Cell(17,4);
				
  		$this->SetFont('Arial','B',12);
		$this->Cell(17,5);
	  	$this->Cell($total_w,4,'CLINICAL LABORATORY SERVICES',$border2,1,'C');
	  	$this->Ln(5);
		
		$labserv = $srvObj->getLabServiceReqInfo($this->refno);
		$labserv_details = $srvObj->getRequestInfo($this->refno);
		#print_r($labserv_details);
	
		$person = $enc_obj->getEncounterInfo($labserv['encounter_nr']);
		$doctor = $pers_obj->get_Person_name($labserv_details['request_doctor']);
	
		$doctor_name = $doctor['name_first']." ".$doctor['name_2']." ".$doctor['name_last'];
		$doctor_name = ucwords(strtolower($doctor_name));
		$doctor_name = htmlspecialchars($doctor_name);
		
		if ($labserv['encounter_nr']==0){
			$request_name = $labserv['ordername'];
			$request_address = $labserv['orderaddress'];
		}else{
			$request_name = $person['name_first']." ".$person['name_2']." ".$person['name_middle']." ".$person['name_last'];
			$request_name = ucwords(strtolower($request_name));
			$request_name = htmlspecialchars($request_name);
		
			$request_address = $person['street_name']." ".$person['brgy_name']." ".$person['mun_name']." ".$person['prov_name']." ".$person['zipcode'];
			$request_name = ucwords(strtolower($request_name));
			$request_name = htmlspecialchars($request_name);
		}
		
		if ($person['encounter_type']==1){
			$enctype = "ER PATIENT";
			$location = "EMERGENCY ROOM";
		}elseif ($person['encounter_type']==2){
			$enctype = "OUTPATIENT (OPD)";
			$dept = $dept_obj->getDeptAllInfo($person['current_dept_nr']);
			$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
		}elseif (($person['encounter_type']==3)||($person['encounter_type']==4)){
			$enctype = "INPATIENT";
			$ward = $ward_obj->getWardInfo($person['current_ward_nr']);
			#echo "sql = ".$ward_obj->sql;
			$location = strtoupper(strtolower(stripslashes($ward['name'])));
		}else{
			$enctype = "OUTPATIENT (OPD)";
		}
	
		$this->SetFont("Arial","","8");
	   $this->Cell(30,4,'PRIORITY NUMBER : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(60,4,$this->refno,$borderNo,$newLineNo,'L');
		$this->Ln(4);		
		$this->SetFont("Arial","","8");
   	$this->Cell(15,4,'NAME : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
	
		#$name = ucwords(strtolower($person['name_first']))." ".ucwords(strtolower($person['name_middle']))." ".ucwords(strtolower($person['name_last']));
	
		$this->Cell(60,4,$request_name,$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","","8");
		$this->Cell(10,4,'AGE : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(25,4,$person['age'],$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","","8");
		$this->Cell(15,4,'HOSP # : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(25,4,$labserv['pid'],$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","","8");
		$this->Cell(15,4,'CASE # : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(20,4,$labserv['encounter_nr'],$borderNo,$newLineNo,'L');
	
		$this->Ln(4);
		$this->SetFont("Arial","","8");
	
		$this->Cell(17,4,'ADDRESS : ',$borderNo,$newLineno,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(120,4,$request_address,$borderNo,$newLineNo,'L');	
		#$this->SetFont("Arial","","8");
		#$this->Cell(10,4,'Clinic : ',$borderNo,$newLineNo,'L');	
		#$this->SetFont("Arial","B","9");
		#$this->Cell(20,4,$person['name_formal'],$borderNo,$newLineYes,'L');	
	
		$this->Ln(4);
		$this->SetFont("Arial","","8");
		$this->Cell(20,4,'IMPRESSION : ',$borderNo,$newLineno,'L');
		$this->SetFont("Arial","B","9");
	
		$this->Cell(125,4,$labserv_details['clinical_info'],$borderNo,$newLineNo,'L');	
	
		$this->Ln(4);
		$this->SetFont("Arial","","8");
		$this->Cell(25,4,'REQUEST DATE : ',$borderNo,$newLineno,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(120,4,date("F j, Y",strtotime($labserv['serv_dt'])),$borderNo,$newLineNo,'L');	
		$this->Cell(30,4,'Dr. '.$doctor_name,$borderNo,$newLineYes,'R');
		$this->SetFont("Arial","","8");
		$this->Cell(170,4,'REQUESTING PHYSICIAN',$borderNo,$newLineNo,'R');
	
		$this->Ln(4);
		$this->SetFont("Arial","","8");
   	$this->Cell(30,4,'PATIENT TYPE : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(60,4,$enctype,$borderNo,$newLineNo,'L');
	
		$this->Ln(4);
		$this->SetFont("Arial","","8");
	   $this->Cell(30,4,'LOCATION/CLINIC : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(60,4,$location,$borderNo,$newLineNo,'L');

		$this->Ln(4);		
		$this->SetFont("Arial","","8");
		$this->Cell(30,4,'PAYMENT TYPE : ',$borderNo,$newLineno,'L');
		$this->SetFont("Arial","B","9");
		if ($is_cash)
			$this->Cell(20,4,'CASH',$borderNo,$newLineYes,'L');	
		else
			$this->Cell(20,4,'CHARGE',$borderNo,$newLineYes,'L');		
		
		$this->SetFont('Arial','B',9);
		$this->Cell(17,5);
		
		$this->Ln(5);

		# Print table header
		
		$this->Cell(20,4,"SECTION : ".$this->section_name,$borderNo,$newLineYes,'L');		
		$this->Ln(2);
    	$this->SetFont('ARIAL','B',8);
		if ($this->colored) $this->SetFillColor(0xED);
		$this->SetTextColor(0);
		$row=6;
		#$this->Cell(0,4,'',1,1,'C');
		$this->Cell($this->ColumnWidth[0],$row,'CODE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'DESCRIPTION',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$row,'GROUP SERVICE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'WITH SAMPLE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[4],$row,'ORIGINAL PRICE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[5],$row,'NET PRICE',1,0,'C',1);
		$this->Ln();
	}
	
	function Footer()
	{
		$this->SetY(-20);
		#$this->SetFont('Arial','B',8);
		#$this->Cell(0,10,'Total Amount Collected = Php '.$this->total_amount,0,0,'R');
		#$this->Ln(5);
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
			$this->Cell(200.8, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}
		
		$cols = array();
	}
	
	function FetchData($refno) {		
		global $db;
		$srvObj=new SegLab;
		
		$servreqObj = $srvObj->getRequestedServices($refno, 1);
		#echo "sql = ".$srvObj->sql;
		$this->_count = $srvObj->count;
		
		if ($servreqObj) {
			while($result=$servreqObj->FetchRow()) {
				$this->section_name = $result['groupnm'];
				$grp = $srvObj->getRequestedServices($refno, 0, $result['group_code']);
				#echo "sql = ".$srvObj->sql;
				if ($grp) {
					while($row=$grp->FetchRow()) {
						if ($row['is_forward'])
							$wsample = "YES";
						else
							$wsample = "NO";	
				
						if ($is_cash){
							$totamount = $row['price_cash_orig'];
							$amount = $row['price_cash'];
						}else{
							$totamount = $row['price_charge'];
							$amount = $row['price_charge'];	
						}	

						$this->Data[]=array(
							$row['service_code'],
							$row['name'],
							$row['groupnm'],
							$wsample,
							number_format($totamount,2,".",","),
							number_format($amount,2,".",",")
						);
						
					}
			   }		
				break;
			}
		}else{
			#print_r($srvObj->sql);
			print_r($db->ErrorMsg());
			exit;
		}
	}
}

$pid = $_GET['pid'];
$is_cash = $_GET['is_cash'];
$refno = $_GET['refno'];

$iss = new Lab_List_Request($pid, $refno, $is_cash);
$iss->AliasNbPages();
$iss->FetchData($refno);
$iss->Report();

?>