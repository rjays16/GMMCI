<?php
	include("roots.php");
	require('./roots.php');
	
	#include_once($root_path."/classes/fpdf/fpdf.php");
	include_once($root_path."/classes/fpdf/pdf.class.php");
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');
	
	#require_once($root_path.'include/care_api_classes/class_oproom.php');
	require_once($root_path.'include/care_api_classes/billing/class_ops.php');
	$srvObj=new SegOps;
	require_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;
	require_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj=new Person;
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;
	require_once($root_path.'include/care_api_classes/class_personell.php');
	$pers_obj=new Personell;
	require_once($root_path.'include/care_api_classes/class_ward.php');
	$ward_obj=new Ward;
	
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	$objInfo = new Hospital_Admin();
	
	require($root_path.'classes/adodb/adodb.inc.php');
	#include($root_path.'include/inc_init_hclab_main.php');
	#include($root_path.'include/inc_seg_mylib.php');
	
	require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
	$hclabObj = new HCLAB;
	
	global $db;
	
	$pdf = new PDF("L",'mm','Legal');
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("L");
		
	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$space=2;
	
	$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',110,10,20,20);
	
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
		
	$pdf->SetFont("Times","B","10");
   #$pdf->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_country'],$borderNo,$newLineYes,'C');
	$pdf->Ln(1);
	#$pdf->Cell(0,4,'DEPARTMENT OF HEALTH', $border_0,1,'C');
	$pdf->Cell(0,4,$row['hosp_agency'], $border_0,1,'C');
	$pdf->Ln(2);
	#$pdf->Cell(0,4,'DAVAO MEDICAL CENTER',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_name'],$borderNo,$newLineYes,'C');
	#$pdf->Cell(0,4,'OUTPATIENT and PREVENTIVE CARE CENTER',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","B","8");
   #$pdf->Cell(0,4,'JICA Bldg., JP Laurel Avenue, Davao City',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_addr1'],$borderNo,$newLineYes,'C');
   $pdf->Ln(2);
	$pdf->SetFont("Times","B","10");
   $pdf->Cell(0,4,'SURGICAL DEPARTMENT',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","I","10");
	$pdf->Cell(0,4,'SURGICAL PROCEDURE STATUS REPORT',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	
	$datefrom = $_GET['fromdate'];
	$dateto = $_GET['todate'];
	$grpview = $_GET['grpview'];
	
	$report_info = $srvObj->getListLabSectionRequest_Status($datefrom, $dateto);
	#echo $srvObj->sql;
	$totalcount = $srvObj->count;
	
	$pdf->SetFont("Times","","10");
	
	$pdf->Cell(30,4,'Date',"",0,'L');
	$pdf->Cell(5,4,':',"",0,'L');
	$pdf->Cell(45,4,date("M. d, Y "),"",1,'L');
	
	$pdf->Cell(30,4,'Time',"",0,'L');
	$pdf->Cell(5,4,':',"",0,'L');
	$pdf->Cell(45,4,date("h:i:s A"),"",0,'L');
	
	if (($datefrom)&&($dateto)){
		$pdf->Ln($space*2);
		$pdf->Cell(30,4,'Start Date',"",0,'L');
		$pdf->Cell(5,4,':',"",0,'L');
		$pdf->Cell(235,4,date("F d, Y ", strtotime($datefrom)),"",0,'L');
		$pdf->Ln($space*2);
		$pdf->Cell(30,4,'End Date',"",0,'L');
		$pdf->Cell(5,4,':',"",0,'L');
		$pdf->Cell(235,4,date("F d, Y ", strtotime($dateto)),"",0,'L');
	}
	
	$pdf->Ln($space*2);
	$pdf->Cell(30,4,'Number of Records',"",0,'L');
	$pdf->Cell(5,4,':',"",0,'L');
	$pdf->Cell(235,4,$totalcount,"",0,'L');
	
	$pdf->Ln($space*4);
	$pdf->SetFont('Arial','B',8);	
	$pdf->Cell(10,4,"","",0,'L');
	$pdf->Cell(25,4,'PATIENT ID',"TB",0,'L');
	$pdf->Cell(21,4,'BATCH NO.',"TB",0,'L');
	$pdf->Cell(50,4,'PATIENT NAME',"TB",0,'L');
	$pdf->Cell(40,4,'REQUEST DATE & TIME',"TB",0,'L');
	$pdf->Cell(40,4,'OPERATION DATE & TIME',"TB",0,'L');
	$pdf->Cell(50,4,'SURGICAL DEPARTMENT',"TB",0,'L');
	$pdf->Cell(30,4,'OPERATING ROOM',"TB",0,'L');
	#$pdf->Cell(20,4,'TIME',"TB",0,'L');
	#$pdf->Cell(20,4,'ICPM CODE',"TB",0,'C');
	#$pdf->Cell(90,4,'ICPM',"TB",0,'L');
	
	#$pdf->Cell(25,4,'P. TYPE',"TB",0,'L');
	$pdf->Cell(25,4,'PATIENT TYPE',"TB",0,'L');
	$pdf->Cell(15,4,'STATUS',"TB",0,'L');
	$pdf->Cell(30,4,'DEPT/LOCATION',"TB",0,'L');
	/*
	$pdf->Cell(30,4,'GROSS AMOUNT',"TB",0,'R');
	$pdf->Cell(2,4,'',"TB",0,'R');
	$pdf->Cell(30,4,'AMOUNT PAID',"TB",0,'R');
	$pdf->Cell(2,4,'',"TB",0,'R');
	$pdf->Cell(30,4,'AMOUNT BAL.',"TB",1,'R');
	*/
	$pdf->Cell(5,4,'',"",1,'L');
	$pdf->Ln($space*2);
	if ($totalcount){
			$i=1;
			$all_total_amount = 0;
			$total_paid = 0;
			$total_amount_bal = 0;
			#$pdf->SetFont('Times','',8);	
			while ($row=$report_info->FetchRow()){
				$pdf->SetFont('Times','',10);	
				$pdf->Cell(10,4,$i.".)","",0,'L');
				$pdf->Cell(25,4,$row['patientID'],"",0,'L');
				$pdf->Cell(21,4,$row['refno'],"",0,'L');
				
				$pdf->Cell(50,4,ucwords(strtolower($row['ordername'])),"",0,'L');
				$pdf->Cell(40,4,$row['serv_dt'].'   '.date("h:i:s A",strtotime($row['serv_tm'])),"",0,'L');
				
				$pdf->Cell(40,4,$row['op_date'].'   '.date("h:i:s A",strtotime($row['op_time'])),"",0,'L');
				
				$ORoom_info = $ward_obj->getOR_RoomInfo($row['op_room']);
				#echo $ward_obj->sql;
				$x = $pdf->GetX();
				$y = $pdf->GetY();
				$pdf->SetXY($x, $y);
				#$pdf->Cell(50,4,$ORoom_info['deptname'],"",0,'L');
				$pdf->MultiCell(50, 5, $ORoom_info['deptname'], '', 'L','');
				$pdf->SetXY($x+50, $y);
				#$pdf->Cell(30,4,$ORoom_info['info']." : ".$ORoom_info['room_nr'],"",0,'L');
				$pdf->MultiCell(30, 5,$ORoom_info['info']." : ".$ORoom_info['room_nr'], '', 'L','');
				$pdf->SetXY($x+80, $y);
				#$pdf->Cell(20,4,'    '.$row['ops_code'],"",0,'L');
				#$pdf->Cell(90,4,$row['service_name'],"",0,'L');
					
				if ($row['encounter_type']==1){
					$patient_type = "ER Patient";
					#$patient_type = "ER";
					$location = "ER";
				}elseif ($row['encounter_type']==2){
					$patient_type = "Outpatient";
					#$patient_type = "OPD";
					$dept = $dept_obj->getDeptAllInfo($row['current_dept_nr']);
					$location = $dept['id'];
				}else{
					$patient_type = "Inpatient";
					#$patient_type = "IPD";
					$ward = $ward_obj->getWardInfo($row['current_ward_nr']);
					#$location = $ward['name'];
					$location = $ward['ward_id'];
				}
				$pdf->Cell(25,4,$patient_type,"",0,'L');
				
				#TPL
				if (($row['is_cash']) && ($row['is_urgent']))
					$paidstatus = 'TPL';
				//elseif (!($row['is_cash']) &&($row['is_urgent']))
				elseif (!($row['is_cash']))
					$paidstatus = 'Charge';
				elseif (($row['is_cash']) && !($row['is_urgent']))
					$paidstatus = 'Cash';
					
				$pdf->Cell(15,4,$paidstatus,"",0,'L');
				$pdf->Cell(30,4,$location,"",0,'L');
				/*
				$price = $srvObj->getSumPerTransaction($row['refno']);
				#echo "<br>".$srvObj->sql;
				if ($row['is_cash'])
					$total_amount = $price['price_cash'];
				else	
					$total_amount = $price['price_charge'];
				
				#$pdf->Cell(7,4,'Php',"",0,'L');
				$all_total_amount = $all_total_amount + $total_amount;
				$pdf->Cell(30,4,number_format($total_amount,2),"",0,'R');
				
				$paid = $srvObj->getSumPaidPerTransaction($row['refno']);
				#echo "<br>".$srvObj->sql;
				#$pdf->Cell(25,4,$paid['amount_due'],"",0,'L');
				
				$pdf->Cell(2,4,'',"",0,'R');
				#$pdf->Cell(7,4,'Php',"",0,'L');
				$total_paid = $total_paid + $paid['amount_paid'];
				$pdf->Cell(30,4,number_format($paid['amount_paid'],2),"",0,'R');
				
				#echo "price = ".$amount_bal;
				#$amount_bal = $paid['amount_due'] - $bal;
				$pdf->Cell(2,4,'',"",0,'R');
				#$pdf->Cell(7,4,'Php',"",0,'L');
				#$pdf->Cell(23,4,$paid['amount_due'] ,"",1,'R');
				
				$amount_bal = $total_amount - $paid['amount_paid'];
				$total_amount_bal = $total_amount_bal + $amount_bal;
				$pdf->Cell(30,4,number_format($amount_bal,2),"",1,'R');
				*/
				
				$list_requests = $srvObj->getRequestedServicesPerRef($row['refno']);
				#echo "<br>".$srvObj->sql;
				$count = $srvObj->count;
				$pdf->Ln($space*5);	
				if ($count){
					$pdf->SetFont('Times','',8);	
					$pdf->Cell(320,4,'',"",1,'L');
					$pdf->Cell(40,4,'',"",0,'L');
					$pdf->Cell(20,4,'ICPM CODE',"B",0,'L');
					$pdf->Cell(210,4,'ICPM',"B",0,'L');
					
					$pdf->Cell(20,4,'RVU',"B",0,'L');
					$pdf->Cell(25,4,'MULTIPLIER',"B",0,'L');
					
					#$pdf->Cell(2,4,'Php',"",0,'L');
					/*
					$pdf->Cell(25,4,'GROSS PRICE',"B",0,'R');
					$pdf->Cell(5,4,'',"B",0,'R');
					$pdf->Cell(25,4,'DISCOUNTED PRICE',"B",0,'R');
					*/
					$pdf->Cell(5,4,'',"",1,'L');
					#$pdf->Cell(300,4,'',"",1,'L');
					$pdf->Ln($space*1);
					while ($row2=$list_requests->FetchRow()){
						$pdf->Cell(40,6,'',"",0,'L');
						$pdf->Cell(20,6,$row2['code'],"",0,'L');
						#$pdf->Cell(80,4,$row2['name'],"",0,'L');
						#$pdf->Cell(210,4,$row2['name'],"",0,'L');
						$x = $pdf->GetX();
						$y = $pdf->GetY();
						$pdf->SetXY($x, $y);	
						$pdf->MultiCell(200, 6,$row2['name'], '', 'L','0');
						$pdf->SetXY($x+200, $y);	
						$pdf->Cell(20,6,$row2['rvu_unit'],"",0,'R');
						$pdf->Cell(30,6,$row2['multiplier_unit'],"",0,'R');
						
						$pdf->Cell(5,6,'',"",1,'L');	
					}
					$pdf->Cell(30,4,'',"",1,'L');
					#$pdf->Cell(320,4,'',"T",1,'L');
					$pdf->Cell(10,4,'',"",0,'L');
					$pdf->Cell(320,4,'',"T",1,'L');
				}	
				$pdf->Cell(30,4,'',"",1,'L');
				$i++;
			}	
			/*
			$pdf->SetFont('Times','B',10);
			$pdf->Ln($space*2);	
			$pdf->Cell(10,4,"","",0,'L');	
			$pdf->Cell(55,4,"TOTAL GROSS AMOUNT","",0,'L');	
			$pdf->Cell(10,4,": Php","",0,'L');	
			$pdf->Cell(2,4,"","",0,'L');	
			$pdf->Cell(20,4,number_format($all_total_amount, 2),"",1,'R');	
		
			$pdf->Cell(10,4,"","",0,'L');	
			$pdf->Cell(55,4,"TOTAL AMOUNT PAID","",0,'L');	
			$pdf->Cell(10,4,": Php","",0,'L');	
			$pdf->Cell(2,4,"","",0,'L');	
			$pdf->Cell(20,4,number_format($total_paid, 2),"",1,'R');	
		
			$pdf->Cell(10,4,"","",0,'L');	
			$pdf->Cell(55,4,"TOTAL AMOUNTT BALANCE","",0,'L');	
			$pdf->Cell(10,4,": Php","",0,'L');	
			$pdf->Cell(2,4,"","",0,'L');	
			$pdf->Cell(20,4,number_format($total_amount_bal, 2),"",0,'R');	
			*/
	}else{
		$pdf->SetFont('Times','',10);	
		$pdf->Ln($space*4);
		$pdf->Cell(337,4,'No query results available at this time...',"",0,'C');
	}
	
	$pdf->Output();	
?>