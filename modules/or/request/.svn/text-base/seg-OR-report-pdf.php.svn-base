<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
require($root_path.'/modules/repgen/repgen.inc.php');

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/billing/class_ops.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
	
/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

	class OR_List_Request extends RepGen {
	var $date;
	var $colored = TRUE;
	var $_count;
	

	function OR_List_Request ($datefrom, $dateto, $grpview) {
		global $db;
		#$this->RepGen("PATIENT'S LIST","L","Legal");
		$this->RepGen("SURGICAL PROCEDURE STATUS REPORT","L","Letter");
		# 165
		$this->ColumnWidth = array(26,22,30,29,29,37,30,25,15,30);
		$this->RowHeight = 10;
		$this->Alignment = array('L','L','L','L','L','L','L','L','L');
		#$this->PageOrientation = "L";
		#$this->PageFormat = "Legal";
		$this->LEFTMARGIN=5;
		$this->DEFAULT_TOPMARGIN = 5;
		$this->NoWrap = false;
		
		$this->SetFillColor(0xFF);
		if ($this->colored) $this->SetDrawColor(0xDD);
	}
	
	function Header() {
		global $root_path, $db;
		$objInfo = new Hospital_Admin();
		
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
			$row['hosp_name']    = "DAVAO MEDICAL CENTER";
			$row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";			
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
  		#$this->Cell($total_w,4,'DAVAO MEDICAL CENTER',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(17,4);
	  	#$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
		$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","10");
		$this->Cell(17,4);
  		$this->Cell($total_w,4,'SURGICAL DEPARTMENT',$border2,1,'C');
		$this->Ln(2);
		$this->SetFont("Arial","B","13");
		$this->Cell(17,4);
				
  		$this->SetFont('Arial','B',12);
		$this->Cell(17,5);
	  	$this->Cell($total_w,4,'SURGICAL PROCEDURE STATUS REPORT',$border2,1,'C');
	  	$this->Ln(5);
		#add some code here
		$this->SetFont('Arial','B',9);
		$this->Cell(17,5);
		
		$this->Ln(5);

		# Print table header
		
		$this->SetFont('ARIAL','B',8);
		if ($this->colored) $this->SetFillColor(0xED);
		$this->SetTextColor(0);
		$row=6;
		#$this->Cell(0,4,'',1,1,'C');
		$this->Cell($this->ColumnWidth[0],$row,'PATIENT ID',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'BATCH NO.',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$row,'PATIENT NAME',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'REQUEST DATE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[4],$row,'OPERATION DATE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[5],$row,'SURGICAL DEPARTMENT',1,0,'C',1);
		$this->Cell($this->ColumnWidth[6],$row,'OPERATING ROOM',1,0,'C',1);
		$this->Cell($this->ColumnWidth[7],$row,'PATIENT TYPE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[8],$row,'STATUS',1,0,'C',1);
		$this->Cell($this->ColumnWidth[9],$row,'DEPT/LOCATION',1,0,'C',1);
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
			$this->Cell(273, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}
		
		$cols = array();
	}
	
	function FetchData($datefrom, $dateto) {		
		global $db;
		$srvObj=new SegOps;
		$ward_obj=new Ward;
		$dept_obj=new Department;
		
		$servreqObj = $srvObj->getListLabSectionRequest_Status($datefrom, $dateto);
		#echo "sql = ".$srvObj->sql;
		$this->_count = $srvObj->count;
		#echo "count = ".$this->_count;
		if ($servreqObj) {
			while($row=$servreqObj->FetchRow()) {
				$ORoom_info = $ward_obj->getOR_RoomInfo($row['op_room']);
				if ($row['encounter_type']==1){
					$patient_type = "ER Patient";
					#$patient_type = "ER";
					$location = "ER";
				}elseif ($row['encounter_type']==2){
					$patient_type = "Outpatient";
					#$patient_type = "OPD";
					$dept = $dept_obj->getDeptAllInfo($row['current_dept_nr']);
					#$location = $dept['id'];
					$location = $dept['name_formal'];
				}elseif (($row['encounter_type']==3)||($row['encounter_type']==4)){
					$patient_type = "Inpatient";
					#$patient_type = "IPD";
					$ward = $ward_obj->getWardInfo($row['current_ward_nr']);
					#$location = $ward['name'];
					$location = $ward['ward_id'];
				}else{
					$patient_type = "Walkin";
					$location = "Walkin";
				}
				
				if (($row['is_cash']) && ($row['is_urgent']))
					$paidstatus = 'TPL';
				//elseif (!($row['is_cash']) &&($row['is_urgent']))
				elseif (!($row['is_cash']))
					$paidstatus = 'Charge';
				elseif (($row['is_cash']) && !($row['is_urgent']))
					$paidstatus = 'Cash';
				
				$this->Data[]=array(
							$row['patientID'],
							$row['refno'],
							mb_strtoupper($row['ordername']),
							date("m/d/Y",strtotime($row['serv_dt'])).'   '.date("h:i A",strtotime($row['serv_tm'])),
							date("m/d/Y",strtotime($row['op_date'])).'   '.date("h:i A",strtotime($row['op_time'])),
							$ORoom_info['deptname'],
							$ORoom_info['info'],
							$patient_type,
							$paidstatus,
							$location
				);
				
			}
		}else{
			#print_r($srvObj->sql);
			#print_r($db->ErrorMsg());
			#exit;
			
		}
	}
}

$datefrom = $_GET['fromdate'];
$dateto = $_GET['todate'];
$grpview = $_GET['grpview'];

$iss = new OR_List_Request($datefrom, $dateto, $grpview);
$iss->AliasNbPages();
$iss->FetchData($datefrom, $dateto);
$iss->Report();

?>