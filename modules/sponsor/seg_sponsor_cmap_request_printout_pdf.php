<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once $root_path."include/care_api_classes/sponsor/class_request.php";

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

class RepGen_CMAPRequest extends RepGen {

	var $pid;
	var $date_from;
	var $date_to;
	var $source;
	var $dept_nr;
	var $total=0;
	var $form_no;
	var $ceil;
	var $maxlines=7;
	var $maxstring=35;

	function RepGen_CMAPRequest($pid='', $source='', $datefrom='', $dateto='', $dept_nr='')
	{
		global $db;
		$this->RepGen("PATIENT'S REQUEST FOR CMAP","L", array(107.95, 177.8));
		//$this->SetMargins(1,1,1);
		$this->colored = TRUE;
		$this->RowHeight = 6;
		$this->SetAutoPageBreak(false);
		if ($this->colored)    $this->SetDrawColor(0xDD);

		$this->pid = $pid;
		$this->source = $source;
		$this->date_from = date('Y-m-d', strtotime($datefrom));
		$this->date_to = date('Y-m-d', strtotime($dateto));
		$this->dept_nr= $dept_nr;
		$this->form_no = $this->SetFormNumber($source);
	}

	function Header()
	{
		global $root_path, $db;
		$objInfo = new Hospital_Admin();

		if ($rowC = $objInfo->getAllHospitalInfo()) {
			$rowC['hosp_agency'] = strtoupper($rowC['hosp_agency']);
			$rowC['hosp_name']   = strtoupper($rowC['hosp_name']);
		}
		else {
			$rowC['hosp_country'] = "Republic of the Philippines";
			$rowC['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$rowC['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
			$rowC['hosp_addr1']   = "Quezon Ave., Digos City";
		}

		$this->SetFont("Arial","","7");
		$this->SetLeftMargin(15);
		$this->SetXY($this->GetX()-5, $this->GetY());
		$this->Cell(10, 3, "Form ".$this->form_no, "", 1, 'L');
		$total_w = 170;
		$this->SetFont("Arial","B","9");
		$this->Cell(0, 3.5,$rowC['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","9");
		$this->Cell(0, 3,$rowC['hosp_addr1'],$border2,1,'C');
		$this->Ln(3);

		$this->PatientInfo();
		$this->RequestInfo();
	}

	function PatientInfo()
	{
		global $db;
		/*$sql = "SELECT ce.encounter_nr as case_no, ce.encounter_type, \n".
				"CONCAT(IF(trim(c.name_last) IS NULL,'',trim(c.name_last)),', ',IF(trim(c.name_first) IS NULL ,'',trim(c.name_first)),' ', \n".
				"IF(trim(c.name_middle) IS NULL,'',trim(c.name_middle))) as name, \n".
				"cd.name_formal as department, \n".
				"trim(c.street_name) as street_name,trim(sb.brgy_name) as brgy_name,trim(sm.mun_name) as mun_name, \n".
				"trim(sm.zipcode) as zip_code,trim(sp.prov_name) as prov_name,trim(sr.region_name) as region_name, \n".
			"IF(fn_calculate_age(ce.encounter_date,c.date_birth),fn_get_age(ce.encounter_date,c.date_birth),age) AS age, c.date_birth \n".
			"FROM care_encounter AS ce \n".
				"INNER JOIN care_person AS c ON c.pid=ce.pid \n".
				"INNER JOIN care_department AS cd ON cd.nr=ce.current_dept_nr \n".
				"LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=c.brgy_nr \n".
				"LEFT JOIN seg_municity AS sm ON sm.mun_nr=c.mun_nr \n".
				"LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr \n".
				"LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr \n".
			"WHERE c.pid =".$db->qstr($this->pid)." LIMIT 1 ";*/

			if(strpos($this->pid, 'W')!==FALSE) {
				$type = 'walkin';
				 $sql = "SELECT \n".
				 "fn_get_walkin_name(w.pid) AS name, \n".
				 "4 as `encounter_type`,fn_get_age(NOW(),w.date_birth) as age, w.date_birth, 'Walkin' as department \n".
				 "FROM seg_walkin w WHERE w.pid=".$db->qstr(substr($this->pid,1));
			} else {
				$type = 'inpatient';
					$sql = "SELECT \n".
						"CONCAT(IF(TRIM(c.name_last) IS NULL,'',TRIM(c.name_last)),', ',IF(TRIM(c.name_first) IS NULL ,'',TRIM(c.name_first)),' ',
										IF(TRIM(c.name_middle) IS NULL,'',TRIM(c.name_middle))) AS name, \n".
						"TRIM(c.street_name) AS street_name,TRIM(sb.brgy_name) AS brgy_name,TRIM(sm.mun_name) AS mun_name, \n".
						"TRIM(sm.zipcode) AS zip_code,TRIM(sp.prov_name) AS prov_name,TRIM(sr.region_name) AS region_name, \n".
						"c.date_birth, IF(fn_calculate_age(NOW(),c.date_birth),fn_get_age(NOW(),c.date_birth),age) AS age, \n".
						"IF(ce.encounter_nr IS NOT NULL, ce.encounter_nr, 'WALKIN') `case_no`,\n".
						"IF(ce.encounter_type IS NOT NULL, ce.encounter_type, 'WALKIN') `encounter_type`, \n".
						"IF(ce.current_dept_nr IS NOT NULL,
											(SELECT d.name_formal FROM care_department d WHERE d.nr=ce.current_dept_nr),
											'WALKIN') `department` \n".
						"FROM care_person AS c \n".
						"LEFT JOIN care_encounter ce ON c.pid=ce.pid \n".
						"LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=c.brgy_nr \n".
						"LEFT JOIN seg_municity AS sm ON sm.mun_nr=c.mun_nr \n".
						"LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr \n".
						"LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr  \n".
						"WHERE c.pid =".$db->qstr($this->pid)." LIMIT 1 ";
			}

			/*echo "<pre>";
			print_r($sql);
			echo "</pre>";*/
			$result = $db->Execute($sql);
			$row = $result->FetchRow();

			$this->SetFont('Arial','', 8);
		#Patient HRN
			$this->SetXY($this->GetX()-5,$this->GetY());
			$this->Cell(15, 3.5, "HRN :", "", 0, 'L');
			$this->Cell(2, 3.5, $this->pid, "", 0, '');

		#Date
			$this->SetXY($this->GetX()+90,$this->GetY());
			$this->Cell(20, 3.5, "Date :", "", 0, 'L');
			$this->Cell(2, 3.5, date('M j, Y h:i A'), "", 1, '');

		#Patient Name
			$this->SetXY($this->GetX()-5,$this->GetY());
			$this->Cell(15, 3.5, "Name :", "", 0, 'L');
			$this->Cell(2, 3.5, $row['name'], "", 0, '');

			#Patient Type
			$this->SetXY($this->GetX()+90,$this->GetY());
			$this->Cell(20, 3.5, "Patient Type :", "", 0, 'L');
			switch ($row['encounter_type']){
				case '1' : $patient_type = 'ER Patient';
									 break;
				case '2' : $patient_type = 'Outpatient';
									 break;
				case '3' :
				case '4' : $patient_type = 'Inpatient';
									 break;
				default  : $patient_type = 'Walkin';
			}
			$this->Cell(2, 3.5, $patient_type, "", 1, '');

			#Patient Address 1
			$this->SetXY($this->GetX()-5,$this->GetY());
			$this->Cell(15, 3.5, "Address :", "", 0, 'L');
			if($type=='walkin'){
				$address1 = $row['address'];
				$address2 = '';
			}
			else{
				$address1 = $row['street_name'];
				$address2 = $row['brgy_name'].", ".$row['mun_name'];
			}
			$this->Cell(2, 3.5, $address1, "", 0, '');

			#Patient Department
			$this->SetXY($this->GetX()+90,$this->GetY());
			$this->Cell(20, 3.5, "Department :", "", 0, 'L');
			$this->Cell(2, 3.5, $row['department'], "", 1, '');

			#Patient Address 2
			if($address2) {
				$this->SetXY($this->GetX()+10,$this->GetY());
				$this->Cell(2, 3.5, $address2, "", 1, '');
			}

			#Patient Birthdate
			$this->SetXY($this->GetX()-5,$this->GetY());
			$this->Cell(15, 3.5, "Birthdate :", "", 0, 'L');
			$this->Cell(2, 3.5, $row['date_birth']!='0000-00-00' ? date('M j, Y', strtotime($row['date_birth'])) : 'Unknown', "", 0, '');

			#Patient Age
			$this->SetXY($this->GetX()+90,$this->GetY());
			$this->Cell(20, 3.5, "Age :", "", 0, 'L');
			$this->Cell(2, 3.5, $row['age'], "", 1, '');
	}

	function RequestInfo()
	{
			switch($this->source)
			{
				case SegRequest::BILLING_REQUEST:
					$header = "Billing Request"; break;
				case SegRequest::PHARMACY_REQUEST:
					$header = "Pharmacy Request"; break;
				case SegRequest::LABORATORY_REQUEST:
					$header = "Laboratory Request"; break;
				case SegRequest::RADIOLOGY_REQUEST:
					$header = "Radiology Request"; break;
				case SegRequest::MISC_REQUEST:
					$header = "Miscellaneous Request"; break;
				case SegRequest::OR_REQUEST:
					$header = "Operating Room Request"; break;
				case SegRequest::PHARMACY_WALKIN_REQUEST:
					$header = "Pharmacy Walkin"; break;
				case SegRequest::IC_BILLING_REQUEST:
					$header = "IC Billing Request"; break;
				case SegRequest::MDC_REQUEST:
					$header = "MDC Request"; break;
			}
			$filters = array();
			$request = new SegRequest($this->source);
			$filters['PID'] = $this->pid;
			$filters['DATEBETWEEN'] = array($this->date_from, $this->date_to);
			$filters['FLAG'] = null;
			$filters['CASH'] = true;
			$filters['DELETED'] = 0;
			$filters['SORT'] = 'date DESC';
			if($this->source==SegRequest::MISC_REQUEST) {
				$filters['MISC_DEPT']=$this->dept_nr;
			}
			$rows = $request->get($filters);
			if($rows!==FALSE) {
				$this->Ln(1.5);
				$this->SetXY($this->GetX()-5,$this->GetY());
				$this->SetFont('Arial', 'B', 10);
				$this->Cell(50, 5, $header, "", 0, 'L');
				foreach($rows as $i=>$row) {
					$total+=parseFloatEx($row['unitPrice']);
				}
				$this->total = $total;
				$this->Cell(20, 5, "Total = ".number_format($this->total, 2)."", "", 0, 'L');

				$this->Ln(6);

				$this->ceil = $this->GetY();
				$cnt=0;
				$x = ($this->GetX()-3);
				$y = $this->GetY();
				if(!isset($this->lastIndex)) $this->lastIndex=0;
				foreach($rows as $i=>$row)
				{
					if($i < $this->lastIndex)
						continue;
					$cnt++;

					if($cnt==$this->maxlines+1) {
						$y = $this->ceil;
						$x = $x+85;
					}
					else if($cnt>($this->maxlines*2)) {
						$this->lastIndex = $i;
						$this->AddPage("L");
						$x = $this->GetX()-5;
						$y = $this->GetY();
						break;
					}

					$this->total+=parseFloatEx($row['unitPrice']);
					$this->SetXY($x,$y);
					$this->SetFont('Arial', 'B', 8);
					$this->Cell(5, 6, ($i+1).". ", "", 0, 'L');

					$this->SetFont('Arial', '', 8);
					$this->SetXY($x+5,$y+1.5);
					$this->MultiCell(58, 3, $row["itemName"], 0, "L");

					$this->SetFont('Arial', 'B', 8);
					$this->SetXY($x+67,$y);
					$this->Cell(10, 6, "".number_format($row["unitPrice"],2), "", 1, 'R');
					if(strlen($row["itemName"])>$this->maxstring)
						$y+=7.5;
					else
						$y+=6;
				}//end for
			}//end if
	}

	function setFormNumber($source)
	{
		return 1;
	}

	function Footer()
	{
			global $db;
			$encoder_type = "";

			switch($this->source)
			{
				//case SegRequest::BILLING_REQUEST:
//					break;
				case SegRequest::PHARMACY_REQUEST:
					$query = "SELECT SQL_CALC_FOUND_ROWS u.name, o.orderdate `date`
									FROM seg_pharma_order_items oi
									INNER JOIN seg_pharma_orders o ON o.refno=oi.refno
									INNER JOIN care_pharma_products_main p ON p.bestellnum=oi.bestellnum
									INNER JOIN care_users u ON u.login_id=o.create_id
									WHERE (o.pid=".$db->qstr($this->pid).") AND (DATE(o.orderdate) BETWEEN ".$db->qstr($this->date_from)." AND ".$db->qstr($this->date_to).") AND is_cash AND request_flag IS NULL
									ORDER BY DATE ASC LIMIT 1";
					$encoder_type = "encoder";
					break;

				case SegRequest::LABORATORY_REQUEST:
					$query = "SELECT
									SQL_CALC_FOUND_ROWS
									CONCAT(IFNULL(ps.name_first,''),' ', IFNULL(ps.name_middle,''),' ',IFNULL(ps.name_last,'')) `name`,
									CONCAT(l.serv_dt,' ',l.serv_tm) `date`, pr.license_nr
									FROM seg_lab_servdetails ld
									INNER JOIN seg_lab_serv l ON l.refno=ld.refno
									INNER JOIN seg_lab_services s ON s.service_code=ld.service_code
									INNER JOIN seg_lab_service_groups g ON g.group_code=s.group_code
									INNER JOIN care_personell pr ON pr.nr=ld.request_doctor
									INNER JOIN care_person ps ON ps.pid=pr.pid
									WHERE (l.pid=".$db->qstr($this->pid).") AND (DATE(CONCAT(l.serv_dt,' ',l.serv_tm)) BETWEEN ".$db->qstr($this->date_from)." AND ".$db->qstr($this->date_to).") AND l.is_cash AND ld.request_flag IS NULL
									ORDER BY DATE ASC LIMIT 1";
					$encoder_type = "physician";
					break;

				case SegRequest::RADIOLOGY_REQUEST:
					$query = "SELECT
									SQL_CALC_FOUND_ROWS
									CONCAT(IFNULL(ps.name_first,''),' ', IFNULL(ps.name_middle,''),' ',IFNULL(ps.name_last,'')) `name`,
									CONCAT(r.request_date,' ',r.request_time) `date`, pr.license_nr
									FROM care_test_request_radio rd
									INNER JOIN seg_radio_serv r ON r.refno=rd.refno
									INNER JOIN seg_radio_services s ON s.service_code=rd.service_code
									INNER JOIN seg_radio_service_groups g ON g.group_code=s.group_code
									INNER JOIN care_personell pr ON pr.nr=rd.request_doctor
									INNER JOIN care_person ps ON ps.pid=pr.pid
									WHERE (r.pid=".$db->qstr($this->pid).") AND (DATE(CONCAT(r.request_date,' ',r.request_time)) BETWEEN ".$db->qstr($this->date_from)." AND ".$db->qstr($this->date_to).") AND is_cash AND request_flag IS NULL
									ORDER BY DATE ASC LIMIT 1";
					$encoder_type = "physician";
					break;

				case SegRequest::MISC_REQUEST:
					$query = "SELECT
									SQL_CALC_FOUND_ROWS
									m.create_id `name`, m.chrge_dte `date`
									FROM seg_misc_service_details md
									INNER JOIN seg_misc_service m ON m.refno=md.refno
									INNER JOIN care_encounter e ON e.encounter_nr=m.encounter_nr
									INNER JOIN seg_other_services s ON s.alt_service_code=md.service_code
									INNER JOIN seg_cashier_account_subtypes t ON s.account_type=t.type_id
									LEFT JOIN care_department d ON d.nr=s.dept_nr
									WHERE (e.pid=".$db->qstr($this->pid).") AND (DATE(m.chrge_dte) BETWEEN ".$db->qstr($this->date_from)." AND ".$db->qstr($this->date_to).") AND is_cash AND request_flag IS NULL
									ORDER BY DATE ASC LIMIT 1";
					$encoder_type = "encoder";
					break;

				case SegRequest::OR_REQUEST:
					$query = "SELECT
								SQL_CALC_FOUND_ROWS
								op.create_id `name`, op.date_request `date`
								FROM seg_or_main op
								INNER JOIN care_encounter e ON e.encounter_nr=op.encounter_nr
								LEFT JOIN seg_packages p ON p.package_id=op.procedure_id
								WHERE (e.pid=".$db->qstr($this->pid).") AND (DATE(op.date_request) BETWEEN ".$db->qstr($this->date_from)." AND ".$db->qstr($this->date_to).") AND request_flag IS NULL
								ORDER BY DATE ASC LIMIT 1";
					$encoder_type = "encoder";
					break;
				//case SegRequest::PHARMACY_WALKIN_REQUEST:
//					break;
//				case SegRequest::IC_BILLING_REQUEST:
//					break;
//				case SegRequest::MDC_REQUEST:
//					break;
			}

			$data = $db->GetRow($query);
			$this->SetXY($this->GetX()-5,-15);
			$this->SetFont('Arial', '', 8);
			//$this->Cell(30, 3.5, "REX D. SUSON", 0, 0, "L");
			$this->Cell(30, 3.5, strtoupper($_SESSION['sess_login_username']), 0, 0, "L");
			$this->SetX($this->GetX()+80);
			//$this->Cell(30, 3.5, "ANNA-LYN C. VIRTUDAZO, M.D.", 0, 1, "L");
			$this->Cell(30, 3.5, strtoupper($data['name']), 0, 1, "L");
			$this->SetX($this->GetX()-5);
			$this->Cell(30, 3.5, "SPMC-CMAP On-duty", 0, 0, "L");
			if($encoder_type=='physician') {
			$this->SetX($this->GetX()+80);
				//$this->Cell(30, 3.5, "License No. 92756", 0, 1, "L");
				$this->Cell(30, 3.5, "License No. ".$data['license_nr'], 0, 1, "L");
			$this->SetX($this->GetX()+105);
			}
			 else $this->SetX($this->GetX()+85);
			$this->Cell(30, 3.5, ucfirst($encoder_type), 0, 1, "L");
	}

	function BeforeData()
	{
			if ($this->colored) {
					$this->DrawColor = array(0xDD,0xDD,0xDD);
			}
			$this->ColumnFontSize = 9;
	}

	function BeforeCellRender()
	{
			$this->FONTSIZE = 8;
			if ($this->colored) {
					if (($this->RENDERPAGEROWNUM%2)>0)
							$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
					else
							$this->RENDERCELL->FillColor=array(255,255,255);
			}
	}

	function AfterData()
	{

	}
}
$rep = new RepGen_CMAPRequest($_GET['pid'],  $_GET['source'], $_GET['date_from'], $_GET['date_to'], $_GET['dept_nr']);
$rep->AliasNbPages();
//$rep->PrintData();
$rep->Report();

?>