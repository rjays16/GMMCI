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
	var $pid;
	var $refno;
	var $is_cash;
	var $discount;
	var $total_discount;
	var $total_amount;
	var $parent_refno;
	var $adjusted_amount;
	var $totdiscount;

	function Lab_List_Request ($refno, $is_cash) {
		global $db;
		#$this->RepGen("PATIENT'S LIST","L","Legal");
		$this->RepGen("CLAIM STUB","P",array(215.9,93.13));
		# 165
		$this->ColumnWidth = array(30,100,40,25);
		#$this->RowHeight = 5;
		$this->RowHeight = 4.5;
		$this->TextHeight = 4;
		$this->Alignment = array('L','L','C','C');
		#$this->PageOrientation = "L";
		#$this->PageFormat = "Legal";
		$this->LEFTMARGIN=15;
		$this->DEFAULT_TOPMARGIN = 2;
		$this->NoWrap = false;

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

		$total_w = 0;
		$this->Ln(2);

		$this->SetFont('Arial','B',10);
		$this->Cell($total_w,4,$row['hosp_id'].' INDUSTRIAL CLINIC LABORATORY',$border2,1,'C');
		$this->Cell($total_w,4,'CLAIM STUB (RECEIVED REQUEST)',$border2,1,'C');

		$labserv = $srvObj->getLabServiceReqInfo($this->refno);
		$labserv_details = $srvObj->getRequestInfo($this->refno);
		#print_r($labserv_details);
		$this->parent_refno = $labserv['parent_refno'];

		#$person = $enc_obj->getEncounterInfo($labserv['encounter_nr']);
		if (trim($labserv['encounter_nr']))
				$person = $enc_obj->getEncounterInfo($labserv['encounter_nr']);
			else
				$person = $person_obj->getAllInfoArray($labserv['pid']);

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

		if ($labserv_details["request_dept"])
					$person['current_dept_nr'] = $labserv_details["request_dept"];

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
				if ($person['current_dept_nr']){
							$enctype = "WALKIN";
							$dept = $dept_obj->getDeptAllInfo($person['current_dept_nr']);
							$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
					}else{
						$enctype = "WALKIN";
						$location = "WALKIN";
					}
			}

		$this->SetFont("Arial","","8");
		$this->Cell(30,4,'PRIORITY NUMBER : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(85,4,$this->refno,$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","","8");
		$this->Cell(15,4,'HOSP # : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(25,4,$labserv['pid'],$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","","8");
		$this->Ln(4);
		$this->SetFont("Arial","","8");
		$this->Cell(15,4,'NAME : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");

		$this->Cell(100,4,mb_strtoupper($request_name),$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","","8");

		$this->SetFont("Arial","","8");
		$this->Cell(25,4,'REQUEST DATE : ',$borderNo,$newLineno,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(120,4,date("F j, Y",strtotime($labserv['serv_dt']))." at ".date("h:i A",strtotime($labserv['serv_tm'])),$borderNo,$newLineNo,'L');

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

		$this->SetFont('Arial','B',9);
		$this->Cell(17,5);

		$this->Ln(5);

		# Print table header

			$this->SetFont('ARIAL','B',8);
		#if ($this->colored) $this->SetFillColor(0xED);
		if ($this->colored) $this->SetFillColor(255);
		$this->SetTextColor(0);
		$row=6;
		#$this->Cell(0,4,'',1,1,'C');
		$this->Cell($this->ColumnWidth[0],$row,'CODE',1,0,'C',1);
		$this->Cell($this->ColumnWidth[1],$row,'DESCRIPTION',1,0,'C',1);
		$this->Cell($this->ColumnWidth[2],$row,'OR NO.',1,0,'C',1);
		$this->Cell($this->ColumnWidth[3],$row,'REMARKS',1,0,'C',1);
		$this->Ln();

	}

	function Footer()
	{
		$this->SetY(-20);
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
			$this->Cell(200.8, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
		}

		else{
			$this->Ln(5);
			$this->SetFont('Arial','',8);
			$this->Cell(190,4,'________________________________________',"",1,'R');
			$this->Cell(190,4,'Person In-Charge (Signature Over Printed Name)',"",1,'R');
		}

		$cols = array();
	}

	function FetchData($refno, $is_cash) {
		global $db;
		$srvObj=new SegLab;

				if($is_cash)
						$mod = 1;
				else
						$mod = 0;

		$servreqObj = $srvObj->getRequestedServices($refno, $mod);
		#echo "sql = ".$srvObj->sql;
		$this->_count = $srvObj->count;

		if ($servreqObj) {
			while($result=$servreqObj->FetchRow()) {
				if ($result['is_forward'])
					$wsample = "YES";
				else
					$wsample = "FW";

				if ($is_cash){
						if ($result['type_charge']){
								if ($result['type_charge']=='paid'){
									$sql_paid = "SELECT pr.or_no, pr.ref_no,pr.service_code
																FROM seg_pay_request AS pr
																INNER JOIN seg_pay AS p ON p.or_no=pr.or_no AND p.pid='".$result["pid"]."'
																WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'
																AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00') LIMIT 1";
									#echo $sql_paid;
									$rs_paid = $db->Execute($sql_paid);
									if ($rs_paid){
										$result2 = $rs_paid->FetchRow();
										$or_no = $result2['or_no'];
									}
								}else
									$or_no = mb_strtoupper($result['type_charge']);
						}

				}else{
					 $or_no = "CHARGE";
				}

				$this->Data[]=array(
					$result['service_code'],
					$result['name'],
					$or_no,
					''
				);
			}
		}else{
			#print_r($srvObj->sql);
			print_r($db->ErrorMsg());
			exit;
		}
	}
}

$refno = $_GET['refno'];
$is_cash = $_GET['is_cash'];

$iss = new Lab_List_Request($refno, $is_cash);
$iss->AliasNbPages();
$iss->FetchData($refno, $is_cash);
$iss->Report();

?>