<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_social_service.php');

#added by VAN 07-20-2010
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

		class RepGen_Socserv_LingapRequest extends RepGen {

		 var $pid;
		 var $encounter_nr;
		 var $control_nr;
		 var $discount_id;
		 var $date;
		 var $printlab, $printradio, $printpharma;
		 var $total_orig_lab, $total_discount_lab;
		 var $total_orig_radio, $total_discount_radio;
		 var $total_orig_pharma, $total_discount_pharma;
		 var $total_orig_request, $total_discount_request;
         var $total_orig_misc, $total_discount_misc;
		 var $ss_nr;

		function RepGen_Socserv_LingapRequest($pid='',$encounter_nr='', $date='', $control_nr='')
		{
			global $db;
				$this->RepGen("PATIENT'S REQUEST FOR LINGAP");
				$this->colored = TRUE;
				$this->ColumnWidth = array(36,20,60,50,15,20);
				$this->RowHeight = 6;
				$this->Alignment = array('L','L','L','L','R','C');
				$this->PageOrientation = "P";
				if ($this->colored)    $this->SetDrawColor(0xDD);

				$this->pid = $pid;
				$this->encounter_nr = $encounter_nr;
                
				if($date) $this->date = date('Y-m-d',strtotime($date));
				else $this->date = date("Y-m-d");
				if ($this->control_nr=$control_nr) {
					$row = $db->GetRow("SELECT pid,date_generated FROM seg_social_lingap WHERE control_nr=".$db->qstr($this->control_nr));
					$this->pid = $row['pid'];
					$this->date = $row['date_generated'];
				}

				$sql_lingap = "SELECT * FROM seg_default_value WHERE name='lingap expiry' AND source='SS'";
				$rs_lingap = $db->Execute($sql_lingap);
				$row_lingap = $rs_lingap->FetchRow();

				$lingap_expiry_day=0;
				if ($row_lingap['value'])
					$lingap_expiry_day = $row_lingap['value'];
				$this->lingap_expiry_day = $lingap_expiry_day;

				if($this->control_nr=="")
					$this->saveToSocialLingap();
		}

		function Header()
		{
				global $root_path, $db;
				$objInfo = new Hospital_Admin();

                #added by VAN 11-06-2012
                if ($this->encounter_nr)
                    $cond_enc = " AND ce.encounter_nr = ".$db->qstr($this->encounter_nr);

				#edited by VAN 11-20-09
                #edited by VAN 11-06-2012
				$sql = "select ce.er_opd_diagnosis, ce.encounter_nr as case_no, ce.encounter_type, \n".
									"CONCAT(IF (trim(c.name_last) IS NULL,'',trim(c.name_last)),', ',IF(trim(c.name_first) IS NULL ,'',trim(c.name_first)),' ', \n".
									"IF(trim(c.name_middle) IS NULL,'',trim(c.name_middle))) as name, \n".
									"trim(c.street_name) as street_name,trim(sb.brgy_name) as brgy_name,trim(sm.mun_name) as mun_name,trim(sm.zipcode) as zip_code,trim(sp.prov_name) as prov_name,trim(sr.region_name) as region_name, \n".
										"cd.name_formal as department, ssp.mss_no, \n".
										"IF(fn_calculate_age(ce.encounter_date,c.date_birth),fn_get_age(ce.encounter_date,c.date_birth),age) AS age, c.date_birth \n".
									/*"from care_encounter as ce \n".
									"inner join care_person as c on c.pid=ce.pid \n".*/
                                    "from care_person as c \n".
                                    "LEFT join care_encounter as ce on c.pid=ce.pid \n".
									"LEFT join care_department as cd on cd.nr=ce.current_dept_nr \n".
									"LEFT join seg_social_patient as ssp on ssp.pid=c.pid \n".
									"LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=c.brgy_nr \n".
									"LEFT JOIN seg_municity AS sm ON sm.mun_nr=c.mun_nr \n".
									"LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr \n".
									"LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr \n".
									"where c.pid =".$db->qstr($this->pid)." 
                                    $cond_enc LIMIT 1 ";
				#echo $sql;
                /*echo "<pre>";
			print_r($sql);
			echo "</pre>";*/
				$result = $db->Execute($sql);
				$row = $result->FetchRow();
				$this->encounter_nr = $row["case_no"];
				$saddr1 = $row['street_name'].', '.$row['brgy_name'];
				$saddr2 = $row['mun_name'].', '.$row['prov_name'];
				$saddr3 = $row['region_name'].', '.$row['zip_code'];
				#$this->trimAddress($row['street_name'], $row['brgy_name'], $row['mun_name'], $row['prov_name'], $row['zipcode'], $saddr1, $saddr2, $saddr3);

				if ($rowC = $objInfo->getAllHospitalInfo()) {
						$rowC['hosp_agency'] = strtoupper($rowC['hosp_agency']);
						$rowC['hosp_name']   = strtoupper($rowC['hosp_name']);
				}
				else {
						$rowC['hosp_country'] = "Republic of the Philippines";
						$rowC['hosp_agency']  = "DEPARTMENT OF HEALTH";
						$rowC['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
						$rowC['hosp_addr1']   = "Quezon Ave., Digos City";
						//$rowC['hosp_addr12']   = "MEDICAL SOCIAL WORK SECTION";
				}

				$this->Image($root_path.'modules/social_service/image/dmc_logo.jpg',150,3,30,28);
				$this->Image($root_path.'modules/social_service/image/Logo_DOH.jpg',40,6,27,25);
				$this->SetFont("Arial","I","9");
				$total_w = 165;
				$this->Cell(17,4);
				$this->Cell($total_w,4,$rowC['hosp_country'],$border2,1,'C');
				$this->Cell(17,4);
				$this->Cell($total_w,4,$rowC['hosp_agency'],$border2,1,'C');
				$this->Ln(2);
				$this->SetFont("Arial","B","10");
				$this->Cell(17,4);
				$this->Cell($total_w,4,$rowC['hosp_name'],$border2,1,'C');
				$this->SetFont("Arial","","9");
				$this->Cell(17,4);
				$this->Cell($total_w,4,$rowC['hosp_addr1'],$border2,1,'C');
				$this->SetFont("Arial","","9");
				$this->Cell(17,4);
				$this->Cell($total_w,4,"MEDICAL SOCIAL WORK SECTION",$border2,1,'C');
				$this->Ln(6);
				$this->SetFont('Arial','B',12);
				$this->Cell(17,5);
				$this->Cell($total_w,4,"PATIENT'S REQUEST FOR LINGAP",$border2,1,'C');
				$this->SetFont('Arial','B',9);
				$this->Cell(17,4);
				$this->Ln(5);
				$this->SetTextColor(0);

				#patient lingap cntrol nr
				$sql_2 = "select control_nr from seg_social_lingap where pid=".$db->qstr($this->pid)."  ORDER BY date_generated DESC LIMIT 1";
				$result_2 = $db->Execute($sql_2);
				$row_2 = $result_2->FetchRow();
				$this->SetFont('Arial','B',9);
				$this->Cell(20, 4, "Control Nr ", "", 0, 'L');
				$this->Cell(1, 4, ":", "", 0, 'R');
				$this->SetFont('Arial','',9);
				$this->Cell(2, 4, $row_2['control_nr'], "", 0, '');

				#Date
				$this->Cell(110,4);
				$this->SetFont('Arial','B',9);
				$this->Cell(22.6, 4, "Date ", "", 0, '');
				$this->Cell(1, 4, ":", "", 0, 'R');
				$this->SetFont('Arial','',9);
				$date = date('Y-m-d H:i:s');
				$date = strftime("%b %d, %Y %I:%M %p", strtotime($date));
				$this->Cell(12, 4, $date, "", 1, '');

				#Patient HRN
				$this->SetFont('Arial','B',9);
				$this->Cell(20, 4, "HRN ", "", 0, 'L');
				$this->Cell(1, 4, ":", "", 0, 'R');
				$this->SetFont('Arial','',9);
				$this->Cell(2, 4, $this->pid, "", 0, '');

				#Case #
				$this->Cell(110,4);
				$this->SetFont('Arial','B',9);
				$this->Cell(22.6, 4, "CASE # ", "", 0, '');
				$this->Cell(1, 4, ":", "", 0, 'R');
				$this->SetFont('Arial','',9);
				$this->Cell(12, 4, $row['case_no'], "", 1, '');

				#patient name
				$this->SetFont('Arial','B',9);
				$this->Cell(20, 4, "Name ", "", 0, 'L');
				$this->Cell(1, 4, ":", "", 0, 'R');
				$this->SetFont('Arial','',9);
				$this->Cell(2,  4, strtoupper($row['name']), "", 0, '');

				#Patient Type
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

				$this->Cell(110,4);
				$this->SetFont('Arial','B',9);
				$this->Cell(22.6, 4, "Patient Type ", "", 0, '');
				$this->Cell(1, 4, ":", "", 0, 'R');
				$this->SetFont('Arial','',9);
				$this->Cell(12, 4, $patient_type, "", 1, '');

				#patient address line 1
				$this->SetFont('Arial','B',9);
				$this->Cell(20, 4, "Address ", "", 0, 'L');
				$this->Cell(1, 4, ":", "", 0, 'R');
				$this->SetFont('Arial','',9);
				$this->Cell(2,  4, strtoupper($saddr1), "", 0, '');

				#Department
				$this->Cell(110,4);
				$this->SetFont('Arial','B',9);
				$this->Cell(22.6, 4, "Dept ", "", 0, '');
				$this->Cell(1, 4, ":", "", 0, 'R');
				$this->SetFont('Arial','',9);
				$this->Cell(12, 4, $row['department'], "", 1, '');

				#patient address line 2
				$this->SetFont('Arial','B',9);
				$this->Cell(20, 4, "", "", 0, 'L');
				$this->Cell(1, 4, ":", "", 0, 'R');
				$this->SetFont('Arial','',9);
				$this->Cell(2,  4, strtoupper($saddr2), "", 0, '');

				#patient classification
				$this->Cell(110,4);
				$this->SetFont('Arial','B',9);
				$this->Cell(22.6, 4, " ", "", 0, 'L');
				$this->Cell(1, 4, "", "", 0, 'R');
				$this->SetFont('Arial','',9);
				if ($_GET['discountid']=='LINGAP')
						#$discountid = 'C1 (for LINGAP)';
						$discountid = '';
				$this->Cell(2,  4, $discountid, "", 1, '');

				//Address (line 3)
				if ($saddr3 != '') {
					$this->SetFont('Arial','B',9);
					$this->Cell(20, 4, "", "", 0, '');
					$this->Cell(1, 4, ":", "", 0, 'R');
					$this->SetFont('Arial','',9);
					$this->Cell(2, 4, strtoupper($saddr3), "", 0, '');
				}

				#patient mss no
				$this->Cell(110,4);
				$this->SetFont('Arial','B',9);
				$this->Cell(22.6, 4, "MSS # ", "", 0, 'L');
				$this->Cell(1, 4, ":", "", 0, 'R');
				$this->SetFont('Arial','',9);
				$this->Cell(2,  4, $row['mss_no'], "", 1, '');

				#added by VAN 11-20-09
				#birthdate
				$this->SetFont('Arial','B',9);
				$this->Cell(20, 4, "Birthdate ", "", 0, 'L');
				$this->Cell(1, 4, ":", "", 0, 'R');
				$this->SetFont('Arial','',9);

				if (($row['date_birth'])&&($row['date_birth']!='0000-00-00'))
					$birthdate = date('F d, Y', strtotime(($row['date_birth'])));


				$this->Cell(2,  4, $birthdate, "", 0, '');

				#age
				if (stristr($row['age'],'years')){
						$age = substr($row['age'],0,-5);
						if ($age>1)
							$labelyear = "years";
						else
							$labelyear = "year";

						$age = floor($age)." ".$labelyear;
					}elseif (stristr($row['age'],'year')){
						$age = substr($row['age'],0,-4);
						if ($age>1)
							$labelyear = "years";
						else
							$labelyear = "year";

						$age = floor($age)." ".$labelyear;

					}elseif (stristr($row['age'],'months')){
						$age = substr($row['age'],0,-6);
						if ($age>1)
							$labelmonth = "months";
						else
							$labelmonth = "month";

						$age = floor($age)." ".$labelmonth;

					}elseif (stristr($row['age'],'month')){
						$age = substr($row['age'],0,-5);

						if ($age>1)
							$labelmonth = "months";
						else
							$labelmonth = "month";

						$age = floor($age)." ".$labelmonth;

					}elseif (stristr($row['age'],'days')){
						$age = substr($row['age'],0,-4);

						if ($age>30){
							$age = $age/30;
							if ($age>1)
								$label = "months";
							else
								$label = "month";

						}else{
							if ($age>1)
								$label = "days";
							else
								$label = "day";
						}

						$age = floor($age).' '.$label;

					}elseif (stristr($row['age'],'day')){
						$age = substr($row['age'],0,-3);

						if ($age>1)
							$labelday = "days";
						else
							$labelday = "day";

						$age = floor($age)." ".$labelday;
					}else{
						if ($row['age']){
							if ($age>1)
								$labelyear = "years";
							else
								$labelyear = "year";

							$age = $age." ".$labelyear;
						}else
							$age = "0 day";
					}

				$this->Cell(110,4);
				$this->SetFont('Arial','B',9);
				$this->Cell(22.6, 4, "Age ", "", 0, 'L');
				$this->Cell(1, 4, ":", "", 0, 'R');
				$this->SetFont('Arial','',9);
				$this->Cell(2,  4,  $age." old", "", 1, '');

				#clinical impression
				$this->SetFont('Arial','B',9);
				$this->Cell(35, 4, "Clinical Impression ", "", 0, 'L');
				$this->Cell(1, 4, ":", "", 0, 'R');
				$this->SetFont('Arial','',9);
                
                $vdiagnosis = trim($row['er_opd_diagnosis']);
                if ($vdiagnosis){
                   $impression = $vdiagnosis;  
                }else{

				    /*$sql = "SELECT d.clinical_info
									    FROM seg_lab_servdetails AS d
									    INNER JOIN seg_lab_serv AS r ON r.refno=d.refno
									    WHERE r.pid =".$db->qstr($this->pid)." AND r.encounter_nr=".$db->qstr($this->encounter_nr)."
									    AND DATE(r.serv_dt)= '".date('Y-m-d',strtotime($this->date))."' and discountid='LINGAP' and d.status IN ('pending','')
									    LIMIT 1
									    UNION
									    SELECT d.clinical_info
									    FROM care_test_request_radio AS d
									    INNER JOIN seg_radio_serv AS r ON r.refno=d.refno
									    WHERE r.pid =".$db->qstr($this->pid)." AND r.encounter_nr=".$db->qstr($this->encounter_nr)."
									    AND DATE(r.request_date)= '".date('Y-m-d',strtotime($this->date))."' and discountid='LINGAP' and d.status IN ('pending','')";*/
                     #edited by VAN 02-28-2012
                     $sql = "SELECT d.clinical_info, r.serv_dt, r.serv_tm
                                        FROM seg_lab_servdetails AS d
                                        INNER JOIN seg_lab_serv AS r ON r.refno=d.refno
                                        WHERE r.pid =".$db->qstr($this->pid)." AND r.encounter_nr=".$db->qstr($this->encounter_nr)."
                                        UNION
                                        SELECT d.clinical_info, r.request_date AS serv_dt, r.request_time AS serv_tm 
                                        FROM care_test_request_radio AS d
                                        INNER JOIN seg_radio_serv AS r ON r.refno=d.refno
                                        WHERE r.pid =".$db->qstr($this->pid)." AND r.encounter_nr=".$db->qstr($this->encounter_nr)."
                                        ORDER BY serv_dt DESC, serv_tm DESC LIMIT 1 ";                   
			    #echo $sql;
                /*echo "<pre>";
			    print_r($sql);
			    echo "</pre>";*/
				    $rs_impression = $db->Execute($sql);
				    $impression='';
				    if($rs_impression->RecordCount()){
						    while($row_impression = $rs_impression->FetchRow()){
								    $impression = $impression." ".$row_impression['clinical_info'].", ";
						    }
						    $impression = trim($impression);
						    $impression = substr($impression,0,-1);
				    }else{
						    $impression = 'Clinical impression is not yet specified.';
				    }
                }   
				#echo "s = ".$impression;
				#$this->Cell(2,  4, strtoupper($impression), "", 0, '');
				$x = $this->GetX();
				$y = $this->GetY();
				$this->SetXY($x,$y);
				$this->MultiCell(150, 4, strtoupper($impression), 0, 'L');
				#----------------------

				$this->Ln(2);
		}

		function Footer()
		{		
				$this->SetY(-10);
		        $this->SetFont('Arial', '', 8);
		       $this->Cell(0,10,'SPMC-F-MSWS-08', 0, 0, 'L');
				$this->SetY(-10);
				$this->SetFont('Arial','I',8);
				$this->Cell(0,4,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'R');
				//$this->Cell(0,4,'Page '.$this->PageNo().' of {nb}. Generated: '.date("Y-m-d h:i:sa"),0,0,'L');
				
				

		}
		//  function Footer()
		// 	{
		// $this->AliasNbPages();

 		
		//}

		function BeforeData()
		{
				if ($this->colored) {
						$this->DrawColor = array(0xDD,0xDD,0xDD);
				}
				$this->ColumnFontSize = 9;
				$this->PrintData();
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

		function saveToSocialLingap()
		{
			global $db;
			$social_obj = new SocialService();
			$sql = "select pid,control_nr from seg_social_lingap where pid=".$db->qstr($this->pid)." AND DATE(date_generated)=DATE(NOW())";
		#echo $sql;
			$result = $db->Execute($sql);
			if(!$result->RecordCount())
			{
				$cntrolnr=$social_obj->getNewControlNr($row['control_nr']);
		#echo $social_obj->sql;
				#edited by VAN 01-21-10
				$sql_2 = "insert into seg_social_lingap (pid, date_generated, control_nr, create_id, create_time)".
				" values (".$db->qstr($this->pid).",'".date('Y-m-d')."','".$cntrolnr."','".$_SESSION['sess_temp_userid']."','".date('YmdHis')."')";
				#echo $sql_2;
				$result_2 = $db->Execute($sql_2);
			}

		}

		function PrintData()
		{
			global $db;
			$pers_obj=new Personell;

			$this->SetFont('Arial','B',11);
			$this->Ln(2);
			$this->Cell(30, 4, "List of Requests:", "", 1, 'L');
			#$this->Ln(1);
			$this->Cell(127,4);
			$this->SetFont('Arial','',11);
			$this->Cell(30, 4, "Original Price", "", 0, '');
			$this->Cell(30,  4, "Discounted Price", "", 1, '');

			$this->printLabRequests();
			$this->printRadioRequests();
			$this->printPharmaRequests();
            $this->printMiscRequests();
			//echo "<br>loop?";
			//die("here");
			/**/
			//compute total here
			$this->SetFont('Arial','B',11);
			$this->Ln(5);
			$this->Cell(30, 4, "Total Amount of Requests", "", 0, 'L');
			$this->Cell(98, 4);
			$this->Cell(25, 4, number_format($this->total_discount_request,2), "", 0, 'R');
			$this->Cell(30,  4, number_format($this->total_orig_request,2), "", 1, 'R');

			$this->Ln(3);
			$this->SetFont('Arial','B',8);
			$this->Cell(0,4,'* Nonsocialized service.',0,1,'L');

			$this->SetFont('Arial','B',11);
			//print encoders here
			$sql = "select name from care_users where login_id=".$db->qstr($_SESSION['sess_temp_userid']);
			$result = $db->Execute($sql);
			$row = $result->FetchRow();
			$this->Ln(20);

			if($this->control_nr) {
				$this->SetFont('Arial','U',11);
				$this->Cell(70, 4, strtoupper($row['name']), "", 0, 'C');
				$this->SetFont('Arial', '',11);
				$this->Cell(100, 4, '____________________________', "", 0, 'C');
				$this->SetFont('Arial','B',11);
				$this->Ln(4);
				$this->Cell(70, 4, "Lingap Encoder", "", 0, 'C');
				$this->Cell(100, 4, "Social Worker On-duty", "", 1, 'C');
			} else {
				$this->Cell(70, 4, strtoupper($row['name']), "", 1, 'C');
				$this->SetFont('Arial','B',11);
				$this->Cell(70, 4, "Social Worker On-duty", "", 1, 'C');
			}

			$this->Ln(10);
			$this->SetFont('Arial','',11);
			$this->Cell(70, 4, "Approved by:", "", 1, 'L');
			$this->Ln();

			$sig_info = $pers_obj->get_Signatory('lingap');
			$name_officer = mb_strtoupper($sig_info['name']);
			$officer_position = $sig_info['signatory_position'];
			$officer_title = $sig_info['signatory_title'];

			$this->SetFont('Arial','U',11);
			$this->Cell(70, 4, $name_officer, "", 1, 'C');
			$this->SetFont('Arial','B',11);
			$this->Cell(70, 4, $officer_position, "", 1, 'C');
		}
		#EDITED BY VAN 09-02-2010
		function printLabRequests()
		{
			global $db;
			#edited by VAN 11-20-09
			$sql = "select is_socialized, ss.ordername,ss.is_cash, ss.discountid, sd.service_code, sl.name, sd.price_cash, sd.price_cash_orig, sd.price_charge".
			" from seg_lab_serv as ss join seg_lab_servdetails as sd on ss.refno=sd.refno".
			" inner join seg_lab_services as sl on sl.service_code=sd.service_code where ss.pid=".$db->qstr($this->pid).
			" /*and DATE(ss.serv_dt)= '".date('Y-m-d',strtotime($this->date))."'*/
			 and DATEDIFF('".date('Y-m-d',strtotime($this->date))."',date(ss.serv_dt))<= ".$this->lingap_expiry_day."
			 and sd.request_flag IS NULL
			 and ss.is_cash = 1
			 and ss.status NOT IN ('deleted','hidden','inactive','void')
			 and sd.status NOT IN ('deleted','hidden','inactive','void')
			 and sl.status NOT IN ('deleted','hidden','inactive','void')
			 and discountid='LINGAP' and sd.status IN ('pending','') order by sl.name";
			#echo "<br><br>lab-".$sql;
			#seg_die($sql);
			$result = $db->Execute($sql);
			if($result->RecordCount())
			{
				$this->printlab=true;
				$this->SetFont('Arial','B',11);
				$this->Ln(3);
				$this->Cell(30, 4, "Laboratory", "", 1, 'L');
				$this->SetFont('Arial','',11);
				#$this->Cell(20, 4);
				$count=1;
				while($row=$result->FetchRow())
				{
					$add_label = "";
					if (!$row['is_socialized'])
						$add_label = "*";

					if($row['is_cash'])
					{
						$this->Cell(10, 4, $count.".	", "", 0, 'C');
						$yhere=$this->GetY();
						$xhere=$this->GetX();
						$this->MultiCell(70,  4, $row['name']." ".$add_label);
						//$this->Cell(110,4);
						$this->SetXY(($xhere+112),$yhere);
						$this->Cell(30,  4, $row['price_cash_orig'], 0, 0, 'R');
						$this->Cell(30, 4, $row['price_cash'], 0, 0, 'R');
						$this->total_discount_lab+=$row['price_cash_orig'];
					}
					else
					{
						$this->Cell(10, 4, $count.".	", "", 0, 'C');
						$yhere=$this->GetY();
						$xhere=$this->GetX();
						$this->MultiCell(70,  4, $row['name']." ".$add_label);
						//$this->Cell(110,4);
						$this->SetXY(($xhere+112),$yhere);
						$this->Cell(30,  4, $row['price_charge'], 0, 0, 'R');
						$this->Cell(30, 4, $row['price_cash'], 0, 0, 'R');
						$this->total_discount_lab+=$row['price_charge'];
					}
					$this->Ln();
					$this->total_orig_lab+=$row['price_cash'];
					#$this->Cell(20, 4);
					$count++;
				}
				$this->Ln(3);
				$this->SetFont('Arial','B',11);
				$this->Cell(5, 4, "Total of Laboratory", "", 0, 'L');
				$this->Cell(117, 4);
				$this->Cell(30,  4, number_format($this->total_discount_lab,2), "", 0, 'R');
				$this->Cell(30, 4, number_format($this->total_orig_lab,2), "", 1, 'R');
				$this->total_discount_request+=$this->total_discount_lab;
				$this->total_orig_request+=$this->total_orig_lab;
			}
			else
			{
				$this->SetFont('Arial','',11);
				$this->Ln(3);
				$this->Cell(30, 4, "No Laboratory requests", "", 1, 'L');
				$this->printlab=false;
			}
		}

		#EDITED BY VAN 09-02-2010
		function printRadioRequests()
		{
			global $db;
			#edited by VAN 11-20-09
			$sql = "select is_socialized, s.ordername,s.discountid,c.service_code,r.name,c.price_cash,c.price_cash_orig,c.price_charge, s.is_cash".
			" from care_test_request_radio as c join seg_radio_serv as s on c.refno=s.refno".
			" inner join seg_radio_services as r on c.service_code=r.service_code where s.pid=".$db->qstr($this->pid).
			" /*and DATE(s.request_date)='".date('Y-m-d',strtotime($this->date))."'*/
			 and DATEDIFF('".date('Y-m-d',strtotime($this->date))."',date(s.request_date))<= ".$this->lingap_expiry_day."
			 and c.request_flag IS NULL
			 and s.is_cash = 1
			 and c.status NOT IN ('deleted','hidden','inactive','void')
			 and s.status NOT IN ('deleted','hidden','inactive','void')
			 and r.status NOT IN ('deleted','hidden','inactive','void')
			 and discountid='LINGAP' and c.status IN ('pending','') order by s.ordername";
			//echo "<br><br>radio-".$sql;
			$result = $db->Execute($sql);
			if($result->RecordCount())
			{
				$this->printradio=true;
				$this->SetFont('Arial','B',11);
				$this->Ln(3);
				$this->Cell(30, 4, "Radiology", "", 1, 'L');
				$this->SetFont('Arial','',11);
				#$this->Cell(20, 4);
				$count=1;
				while($row=$result->FetchRow())
				{
					$add_label = "";
					if (!$row['is_socialized'])
						$add_label = "*";

					if($row['is_cash'])
					{
						$this->Cell(10, 4, $count.".	", "", 0, 'C');
						$yhere=$this->GetY();
						$xhere=$this->GetX();
						$this->MultiCell(70,  4, $row['name']." ".$add_label);
						#$this->Cell(2,  4, $row['name'], "", 0, '');
						#$this->Cell(110,4);
						$this->SetXY(($xhere+112),$yhere);
						$this->Cell(30,  4, $row['price_cash_orig'], "", 0, 'R');
						$this->Cell(30, 4, $row['price_cash'], "", 0, 'R');
						$this->total_discount_radio+=$row['price_cash_orig'];
					}
					else
					{
						$this->Cell(10, 4, $count.".	", "", 0, 'C');
						$yhere=$this->GetY();
						$xhere=$this->GetX();
						$this->MultiCell(70,  4, $row['name']." ".$add_label);
						#$this->Cell(2,  4, $row['name'], "", 0, '');
						#$this->Cell(110,4);
						$this->SetXY(($xhere+112),$yhere);
						$this->Cell(30,  4, $row['price_charge'], "", 0, 'R');
						$this->Cell(30, 4, $row['price_cash'], "", 0, 'R');
						$this->total_discount_radio+=$row['price_charge'];
					}
					$this->Ln();
					$this->total_orig_radio+=$row['price_cash'];
					#$this->Cell(20, 4);
					$count++;
				}
				$this->Ln();
				$this->SetFont('Arial','B',11);
				$this->Cell(5, 4, "Total of Radiology", "", 0, 'L');
				$this->Cell(117, 4);
				$this->Cell(30,  4, number_format($this->total_discount_radio,2), "", 0, 'R');
				$this->Cell(30, 4, number_format($this->total_orig_radio,2), "", 1, 'R');
				$this->total_discount_request+=$this->total_discount_radio;
				$this->total_orig_request+=$this->total_orig_radio;
			}
			else
			{
				$this->SetFont('Arial','',11);
				$this->Ln(3);
				$this->Cell(30, 4, "No Radiology requests", "", 1, 'L');
				$this->printradio=false;
			}
		}

		#EDITED BY VAN 09-02-2010
		function printPharmaRequests()
		{
			global $db;
			#edited by VAN 11-20-09
			$sql2 = "select po.ordername, po.discountid, pi.bestellnum, pm.artikelname, pi.price_orig, pi.pricecash, pi.pricecharge, pi.quantity, po.is_cash".
			" from seg_pharma_orders as po join seg_pharma_order_items as pi on po.refno=pi.refno inner join care_pharma_products_main as pm".
			" on pm.bestellnum=pi.bestellnum where po.pid=".$db->qstr($this->pid).
			" /*and DATE(po.orderdate)= '".date('Y-m-d',strtotime($this->date))."'*/
				and DATEDIFF('".date('Y-m-d',strtotime($this->date))."',date(po.orderdate))<= ".$this->lingap_expiry_day."
				and pi.request_flag IS NULL
				and po.is_cash = 1
			    and discountid='LINGAP' and po.serve_status='N' order by pm.artikelname";
			//echo "<br><br>pharma-".$sql2;
			$result2 = $db->Execute($sql2);
			if($result2->RecordCount()>0)
			{
				$this->printpharma=true;
				$this->SetFont('Arial','',10);
				$this->Ln(4);
				$this->Cell(65, 4);
				$this->Cell(10, 4, "Qty", "", 0, 'L');
				$this->Cell(20, 4, "Unit Price", "", 0, 'L');
				$this->Cell(40, 4, "Discounted Unit Price", "", 0, 'L');
				$this->Cell(20, 4, "Total Price", "", 0, 'L');
				$this->Cell(40, 4, "Discounted Total Price", "", 1, 'L');
				$this->SetFont('Arial','B',11);
				$this->Cell(30, 4, "Pharmacy (Medicines)", "", 1, 'L');
				$this->SetFont('Arial','',11);
				$count=1;
				while($row=$result2->FetchRow())
				{
					if($row['is_cash'])
					{
						$this->Cell(10, 4, $count.".	", "", 0, 'C');
						$yhere=$this->GetY();
						$xhere=$this->GetX();

						$this->SetFont('Arial','',9);
						//$this->Cell(100,  4, $row['artikelname'], 0, 0,'L');
						$this->MultiCell(100,  4, $row['artikelname'], 0);
						$this->SetFont('Arial','',11);
						$this->SetXY(($xhere+53),$yhere);
						$this->Cell(10,  4, $row['quantity'], "", 0, 'R');
						$this->Cell(20, 4, number_format($row['pricecash'],2), "", 0, 'R');
						$this->Cell(30,  4, number_format($row['price_orig'],2), "", 0, 'R');
						$this->Cell(30, 4, number_format(($row['quantity']*$row['price_orig']),2), 0, 0, 'R');
						$this->Cell(30, 4, number_format(($row['quantity']*$row['pricecash']),2), 0, 0, 'R');
						$this->total_discount_pharma+=($row['quantity']*$row['price_orig']);
					}
					else
					{
						$this->Cell(10, 4, $count.".	", "", 0, 'C');
						$yhere=$this->GetY();
						$xhere=$this->GetX();

						$this->SetFont('Arial','',9);
						//$this->Cell(100,  4, $row['artikelname'], 0, 0,'L');
						$this->MultiCell(100,  4, $row['artikelname'], 0);
						$this->SetFont('Arial','',11);
						$this->SetXY(($xhere+53),$yhere);
						$this->Cell(10,  4, $row['quantity'], "", 0, 'R');
						$this->Cell(20, 4, number_format($row['pricecash'],2), "", 0, 'R');
						$this->Cell(30,  4, number_format($row['pricecharge'],2), "", 0, 'R');
						$this->Cell(30, 4, number_format(($row['quantity']*$row['pricecharge']),2), 0, 0, 'R');
						$this->Cell(30, 4, number_format(($row['quantity']*$row['pricecash']),2), 0, 0, 'R');
						$orig=parseFloatEx($row['quantity']*$row['pricecash']);
						$this->total_discount_pharma+=($row['quantity']*$row['pricecharge']);
					}
					$this->Ln();
					$this->total_orig_pharma+=($row['quantity']*$row['pricecash']);
					$count++;
				}
				$this->Ln(2);
				$this->SetFont('Arial','B',11);
				$this->Cell(5, 4, "Total of Medicines", "", 0, 'L');
				$this->Cell(123, 4);
				$this->Cell(25,  4, number_format($this->total_discount_pharma,2), "", 0, 'R');
				$this->Cell(30, 4, number_format($this->total_orig_pharma,2), "", 1, 'R');
				$this->total_discount_request+=$this->total_discount_pharma;
				$this->total_orig_request+=$this->total_orig_pharma;
			}
			else
			{
				$this->SetFont('Arial','',11);
				$this->Ln(3);
				$this->Cell(30, 4, "No Pharmacy requests", "", 1, 'L');
				$this->printpharma=false;
			}
		}
        
        #Added by Jarel 04/11/2013
        function printMiscRequests()
        {
            global $db;
            
            $sql = "SELECT s.discountid,c.service_code,r.name,c.adjusted_amnt,c.chrg_amnt, s.is_cash
                    FROM seg_misc_service_details AS c JOIN seg_misc_service AS s ON c.refno=s.refno
                    INNER JOIN seg_other_services AS r ON c.service_code=r.alt_service_code WHERE s.pid=".$db->qstr($this->pid).
                    "and DATEDIFF('".date('Y-m-d',strtotime($this->date))."',date(s.chrge_dte))<= ".$this->lingap_expiry_day."
                    AND c.request_flag IS NULL
                    AND s.is_cash = 1 AND s.discountid='LINGAP' ORDER BY r.name";


            $result = $db->Execute($sql);
            if($result->RecordCount())
            {
                $this->printlab=true;
                $this->SetFont('Arial','B',11);
                $this->Ln(3);
                $this->Cell(30, 4, "Miscellaneous", "", 1, 'L');
                $this->SetFont('Arial','',11);
                #$this->Cell(20, 4);
                $count=1;
                while($row=$result->FetchRow())
                {

                    if($row['is_cash'])
                    {
                        $this->Cell(10, 4, $count.".    ", "", 0, 'C');
                        $yhere=$this->GetY();
                        $xhere=$this->GetX();
                        $this->MultiCell(70,  4, $row['name']);
                        //$this->Cell(110,4);
                        $this->SetXY(($xhere+112),$yhere);
                        $this->Cell(30,  4, number_format($row['chrg_amnt'],2), 0, 0, 'R');
                        $this->Cell(30, 4, number_format($row['adjusted_amnt'],2), 0, 0, 'R');
                        $this->total_discount_misc+=$row['chrg_amnt'];
                    }
                    else
                    {
                        $this->Cell(10, 4, $count.".    ", "", 0, 'C');
                        $yhere=$this->GetY();
                        $xhere=$this->GetX();
                        $this->MultiCell(70,  4, $row['name']);
                        //$this->Cell(110,4);
                        $this->SetXY(($xhere+112),$yhere);
                        $this->Cell(30,  4, number_format($row['chrg_amnt'],2), 0, 0, 'R');
                        $this->Cell(30, 4, number_format($row['adjusted_amnt'],2), 0, 0, 'R');
                        $this->total_discount_misc+=$row['chrg_amnt'];
                    }
                    $this->Ln();
                    $this->total_orig_misc+=$row['adjusted_amnt'];
                    #$this->Cell(20, 4);
                    $count++;
                }
                $this->Ln(3);
                $this->SetFont('Arial','B',11);
                $this->Cell(5, 4, "Total of Miscellaneous", "", 0, 'L');
                $this->Cell(117, 4);
                $this->Cell(30,  4, number_format($this->total_discount_misc,2), "", 0, 'R');
                $this->Cell(30, 4, number_format($this->total_orig_misc,2), "", 1, 'R');
                $this->total_discount_request+=$this->total_discount_misc;
                $this->total_orig_request+=$this->total_orig_misc;
            }
            else
            {
                $this->SetFont('Arial','',11);
                $this->Ln(3);
                $this->Cell(30, 4, "No Miscellaneous requests", "", 1, 'L');
                $this->printlab=false;
            }
        }

}
$rep =& new RepGen_Socserv_LingapRequest($_GET['pid'], $_GET['encounter_nr'], $_GET['date'], $_GET['control_nr']);
$rep->AliasNbPages();
//$rep->PrintData();
$rep->Report();

?>