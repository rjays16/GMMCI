<?php
	//include("roots.php");
	require('./roots.php');
	
	#include_once($root_path."/classes/fpdf/fpdf.php");
	include_once($root_path."/classes/fpdf/pdf.class.php");
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');
	
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	$srvObj=new SegLab;
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
	include($root_path.'include/inc_init_hclab_main.php');
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
	
	$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,20,20);
	
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
		
	$pdf->SetFont("Times","B","10");
   #$pdf->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_country'],$borderNo,$newLineYes,'C');
	$pdf->Ln(1);
	#$pdf->Cell(0,4,'DEPARTMENT OF HEALTH', $border_0,1,'C');
	$pdf->Cell(0,4,$row['hosp_agency'], $border_0,1,'C');
	$pdf->Ln(2);
	#$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_name'],$borderNo,$newLineYes,'C');
	#$pdf->Cell(0,4,'OUTPATIENT and PREVENTIVE CARE CENTER',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","B","8");
   #$pdf->Cell(0,4,'JICA Bldg., JP Laurel Avenue, Davao City',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_addr1'],$borderNo,$newLineYes,'C');
   $pdf->Ln(2);
	$pdf->SetFont("Times","B","10");
   $pdf->Cell(0,4,'DEPARTMENT OF PATHOLOGY AND CLINICAL LABORATORIES',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","I","10");
	$pdf->Cell(0,4,'LABORATORY STATUS REPORT',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	
	$grp_kind = $_GET['report_kind'];
	$grp_code = $_GET['report_group'];
	$discountID = $_GET['report_class'];
	$datefrom = $_GET['fromdate'];
	$dateto = $_GET['todate'];
	$grpview = $_GET['grpview'];
	
	#added by VAN 06-03-08
	$pat_type = $_GET['pat_type'];
	$fromtime = $_GET['fromtime'];
	$totime = $_GET['totime'];
	$user = $_GET['user'];
	
	$fromtime = date("H:i:s",strtotime($fromtime));
	$totime = date("H:i:s",strtotime($totime));
	
	if (($fromtime=='00:00:00 AM')||($fromtime=='00:00:00 PM'))
		$fromtime = '00:00:00';
	if (($totime=='00:00:00 AM')||($totime=='00:00:00 PM'))
		$totime = '00:00:00';
	
	
	$pdf->SetFont("Times","B","10");
	if ($grp_kind == 'all'){
		$pdf->Cell(0,4,'ALL LABORATORY REQUESTS',$borderNo,$newLineYes,'C');
		$pdf->Ln(4);
	}elseif ($grp_kind == 'wo_result'){
		$pdf->Cell(0,4,'LABORATORY REQUEST WITHOUT RESULTS',$borderNo,$newLineYes,'C');
		$pdf->Ln(4);
	}elseif ($grp_kind == 'w_result'){
		$pdf->Cell(0,4,'LABORATORY REQUEST WITH RESULTS',$borderNo,$newLineYes,'C');
		$pdf->Ln(4);
	}
	
	#echo "grpview = ".$grpview;
	#$report_info = $srvObj->getListLabSectionRequest($grp_kind, $grpview, $grp_code, $datefrom, $dateto, $discountID,0);
	$report_info = $srvObj->getListLabSectionRequest_Status($grp_kind, $grpview, $grp_code, $datefrom, $dateto, $discountID, $pat_type, $fromtime, $totime);
	#echo "s = ".$srvObj->sql;
	$totalcount = $srvObj->count;
	
	#echo "sql = ".$srvObj->sql;
	#echo "<br>totalcount = ".$totalcount;
	$service_info = $srvObj->getAllLabGroupInfo($grp_code);
	#echo "s = ".$srvObj->sql;    
	$pdf->SetFont("Times","","10");
	/*
	if ($grp_code!='all'){
		$pdf->Cell(270,4,'Laboratory Section : '.$service_info['name'],"",0,'L');
	}else{
		$pdf->Cell(270,4,'Laboratory Section : ALL SECTION',"",0,'L');
	}
	$pdf->Cell(60,4,'Date : '.date("M. d, Y "),"",0,'L');
	$pdf->Ln($space*2);
	if ($discountID!='all'){
		$pdf->Cell(270,4,'Classification : '.$discountID,"",0,'L');
	}else{
		$pdf->Cell(270,4,'Classification : ALL CLASS',"",0,'L');
	}
	
	$pdf->Cell(60,4,'Time : '.date("h:i:s A"),"",0,'L');
	if ($datefrom){
		$pdf->Ln($space*2);
		$pdf->Cell(270,4,'Start Date : '.$datefrom,"",0,'L');
	}
	if ($dateto){
		$pdf->Ln($space*2);
		$pdf->Cell(270,4,'End Date : '.$dateto,"",0,'L');
	}	
	$pdf->Ln($space*2);
	$pdf->Cell(270,4,'Number of Records : '.$totalcount,"",0,'L');
	$pdf->Ln($space*2);
	$pdf->Cell(270,4,'Currency : Philippine Peso (Php)',"",0,'L');
	#$pdf->Cell(60,4,'Page : '.$pdf->PageNo().' / {nb}',"",0,'L');
	*/
	
	$pdf->Cell(30,4,'Prepared By',"",0,'L');
	$pdf->Cell(5,4,':',"",0,'L');
	$pdf->Cell(235,4,strtoupper($user),"",0,'L');
	$pdf->Ln($space*2);
	
	if(($pat_type==1)||($pat_type==2)){
		$pdf->Cell(30,4,'Shift Schedule',"",0,'L');
		$pdf->Cell(5,4,':',"",0,'L');
		$pdf->Cell(235,4,date("h:s A",strtotime($fromtime))." to ".date("h:s A",strtotime($totime)),"",0,'L');
		$pdf->Ln($space*2);
	}
	
	if ($grp_code!='all'){
		#$pdf->Cell(270,4,'Laboratory Section : '.$service_info['name'],"",0,'L');
		$pdf->Cell(30,4,'Laboratory Section',"",0,'L');
		$pdf->Cell(5,4,':',"",0,'L');
		$pdf->Cell(235,4,strtoupper($service_info['name']),"",0,'L');
	}else{
		#$pdf->Cell(270,4,'Laboratory Section : ALL SECTION',"R",0,'L');
		$pdf->Cell(30,4,'Laboratory Section',"",0,'L');
		$pdf->Cell(5,4,':',"",0,'L');
		$pdf->Cell(235,4,'ALL SECTION',"",0,'L');
	}
	#$pdf->Cell(60,4,'Date : '.date("M. d, Y "),"",0,'L');
	$pdf->Cell(10,4,'Date',"",0,'L');
	$pdf->Cell(5,4,':',"",0,'L');
	$pdf->Cell(45,4,date("M. d, Y "),"",0,'L');
	
	$pdf->Ln($space*2);
	if ($discountID!='all'){
		#$pdf->Cell(270,4,'Classification : '.$discountID,"",0,'L');
		$pdf->Cell(30,4,'Classification',"",0,'L');
		$pdf->Cell(5,4,':',"",0,'L');
		#edited by VAN 07-01-08
		$classInfo = $srvObj->getChargeTypeInfo($discountID);
		#echo 'count = '.$srvObj->count;
		if ($srvObj->count)
			$pdf->Cell(235,4,strtoupper($classInfo['charge_name']),"",0,'L');
		else
			$pdf->Cell(235,4,strtoupper($discountID),"",0,'L');	
	}else{
		#$pdf->Cell(270,4,'Classification : ALL CLASS',"",0,'L');
		$pdf->Cell(30,4,'Classification',"",0,'L');
		$pdf->Cell(5,4,':',"",0,'L');
		$pdf->Cell(235,4,'ALL CLASS',"",0,'L');
	}
	
	#$pdf->Cell(60,4,'Time : '.date("h:i:s A"),"",0,'L');
	$pdf->Cell(10,4,'Time',"",0,'L');
	$pdf->Cell(5,4,':',"",0,'L');
	$pdf->Cell(45,4,date("h:i:s A"),"",0,'L');
	
	$pdf->Ln($space*2);
	if ($pat_type){
		$pdf->Cell(30,4,'Patient Type',"",0,'L');
		$pdf->Cell(5,4,':',"",0,'L');
		
		if ($pat_type==1)
			#ER PATIENT
			$pattype = "ER PATIENT";
		elseif ($pat_type==2)
			#ADMITTED PATIENT
			$pattype = "ADMITTED PATIENT";
		elseif ($pat_type==3)
			#OUT PATIENT
			$pattype = "OUTPATIENT";
		elseif ($pat_type==4)
			#WALK-IN PATIENT
				$pattype = "WALK-IN";
		elseif ($pat_type==5)
			#OPD & WALK-IN PATIENT
			$pattype = "OPD & WALK-IN";				
		
		$pdf->Cell(235,4,$pattype,"",0,'L');
	}else{
		$pdf->Cell(30,4,'Patient Type',"",0,'L');
		$pdf->Cell(5,4,':',"",0,'L');
		$pdf->Cell(235,4,'ALL PATIENT TYPE',"",0,'L');
	}
	
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
	#$pdf->Cell(270,4,'Number of Records : '.$totalcount,"",0,'L');
	$pdf->Cell(30,4,'Number of Records',"",0,'L');
	$pdf->Cell(5,4,':',"",0,'L');
	$pdf->Cell(235,4,$totalcount,"",0,'L');
	
	$pdf->Ln($space*2);
	#$pdf->Cell(270,4,'Currency : Philippine Peso (Php)',"",0,'L');
	$pdf->Cell(30,4,'Currency',"",0,'L');
	$pdf->Cell(5,4,':',"",0,'L');
	$pdf->Cell(35,4,'Philippine Peso (Php)',"",0,'L');
	
	$pdf->Ln($space*4);
	$pdf->SetFont('Arial','B',8);	
	$pdf->Cell(10,4,"","",0,'L');	
	$pdf->Cell(25,4,'PATIENT ID',"TB",0,'L');
	$pdf->Cell(20,4,'BATCH NO.',"TB",0,'L');
	$pdf->Cell(40,4,'PATIENT NAME',"TB",0,'L');
	$pdf->Cell(43,4,'ORDER DATE & TIME',"TB",0,'L');
	#$pdf->Cell(20,4,'TIME',"TB",0,'L');
	
	if ($grpview==0){
		$pdf->Cell(35,4,'TEST',"TB",0,'L');
	
		if ($grp_code=='all'){
			$pdf->Cell(20,4,'SECTION',"TB",0,'L');
		}
	}	
	
	if ($discountID=='all'){
		$pdf->Cell(30,4,'MSS/CHARGE TYPE',"TB",0,'L');
	}
	#$pdf->Cell(25,4,'P. TYPE',"TB",0,'L');
	$pdf->Cell(25,4,'PATIENT TYPE',"TB",0,'L');
	$pdf->Cell(15,4,'STATUS',"TB",0,'L');
	$pdf->Cell(30,4,'DEPT/LOCATION',"TB",0,'L');
	$pdf->Cell(30,4,'GROSS AMOUNT',"TB",0,'R');
	$pdf->Cell(2,4,'',"TB",0,'R');
	$pdf->Cell(30,4,'AMOUNT PAID',"TB",0,'R');
	$pdf->Cell(2,4,'',"TB",0,'R');
	$pdf->Cell(30,4,'AMOUNT BAL.',"TB",1,'R');
	
	#$pdf->Cell(45,4,'',"TB",1,'L');
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
				$pdf->Cell(20,4,$row['refno'],"",0,'L');
				
				$pdf->Cell(40,4,ucwords(strtolower($row['ordername'])),"",0,'L');
				$pdf->Cell(43,4,$row['serv_dt'].'   '.date("h:i:s A",strtotime($row['serv_tm'])),"",0,'L');
				#$pdf->Cell(17,4,$row['serv_tm'],"",0,'C');
				#$pdf->Cell(20,4,date("h:i:s A",strtotime($row['serv_tm'])),"",0,'L');
				
				if ($grpview==0){
					$pdf->Cell(35,4,$row['service_name'],"",0,'L');
		
					if ($grp_code=='all'){
						$pdf->Cell(20,4,$row['grp_name2'],"",0,'L');
					}
				}
					
				if ($discountID=='all'){
					/*				
					if ((empty($row['classID']))|| ($row['classID']==" ") || ($row['classID']==""))
						$classify = "NONE";
					else
						$classify = $row['classID'];	
					*/	
					#edited by VAN 07-01-08
					if (!empty($row['classID']))
						$classify = $row['classID'];	
					elseif (!empty($row['type_charge']))
						$classify = $row['type_charge'];	
						
					if ((empty($classify))|| ($classify==" ") || ($classify==""))
						$classify = "NONE";
					else{
						$classInfo = $srvObj->getChargeTypeInfo($classify);
						if ($srvObj->count)
							$classify = $classInfo['charge_name'];
					}	
					#------------------
					
					$pdf->Cell(30,4,$classify,"",0,'C');
				}
				
				if ($row['encounter_type']==1){
					$patient_type = "ER Patient";
					#$patient_type = "ER";
					$location = "ER";
				}elseif ($row['encounter_type']==2){
					$patient_type = "Outpatient";
					#$patient_type = "OPD";
					$dept = $dept_obj->getDeptAllInfo($row['current_dept_nr']);
					$location = $dept['id'];
				}elseif (($row['encounter_type']==3)||($row['encounter_type']==4)){
					if ($row['encounter_type']==3)
						$wer = "(ER)";
					elseif ($row['encounter_type']==4)	
						$wer = "(OPD)";
						
					$patient_type = "Inpatient ".$wer;
					#$patient_type = "IPD";
					$ward = $ward_obj->getWardInfo($row['current_ward_nr']);
					#$location = $ward['name'];
					$location = $ward['ward_id']." : Rm.#".$row['current_room_nr'];
				}else{
					$patient_type = "Walkin";
					$location = '';
				}
				
				$pdf->Cell(25,4,$patient_type,"",0,'L');
				
				#TPL
				#if (($row['is_cash']) && ($row['is_urgent']))
				if ($row['is_tpl'])
					$paidstatus = 'TPL';
				//elseif (!($row['is_cash']) &&($row['is_urgent']))
				elseif (!($row['is_cash']))
					$paidstatus = 'Charge';
				elseif (($row['is_cash']) && !($row['is_urgent']))
					$paidstatus = 'Cash';
					
				$pdf->Cell(15,4,$paidstatus,"",0,'L');
				$pdf->Cell(30,4,$location,"",0,'L');
				
				$price = $srvObj->getSumPerTransaction($row['refno']);
				#echo "<br>".$srvObj->sql;
				if ($row['is_cash'])
					$total_amount = $price['price_cash'];
				else	
					$total_amount = $price['price_charge'];
				
				#$pdf->Cell(7,4,'Php',"",0,'L');
				$all_total_amount = $all_total_amount + $total_amount;
				$pdf->Cell(30,4,number_format($total_amount,2),"",0,'R');
				
				$paid = $srvObj->getSumPaidPerTransaction($row['refno'],$row['patientID']);
				#echo "<br>".$srvObj->sql;
				#$pdf->Cell(25,4,$paid['amount_due'],"",0,'L');
				/*
				if ($paid['amount_tendered'])
					$amount_paid = $paid['amount_paid'];
				else
					$amount_paid = '0.00';
				*/		
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
				#$pdf->Cell(30,4,money_format('%(n', $amount_bal),"",1,'R');
				
				$list_requests = $srvObj->getRequestedServicesPerGroup($row['refno'],$grp_code);
				#echo "<br>".$srvObj->sql;
				$count = $srvObj->count;
				
				if ($count){
					$pdf->SetFont('Times','',8);	
					$pdf->Cell(320,4,'',"",1,'L');
					$pdf->Cell(40,4,'',"",0,'L');
					$pdf->Cell(80,4,'TEST',"B",0,'L');
					if ($grp_code=='all'){
						$pdf->Cell(50,4,'SECTION',"B",0,'L');
					}
					$pdf->Cell(20,4,'IS SOCIALIZED',"B",0,'L');
				
					#$pdf->Cell(2,4,'Php',"",0,'L');
					$pdf->Cell(25,4,'GROSS PRICE',"B",0,'R');
					$pdf->Cell(5,4,'',"B",0,'R');
					$pdf->Cell(25,4,'DISCOUNTED PRICE',"B",0,'R');
					$pdf->Cell(5,4,'',"",1,'L');
					#$pdf->Cell(300,4,'',"",1,'L');
					$pdf->Ln($space*1);
					while ($row2=$list_requests->FetchRow()){
						$pdf->Cell(40,4,'',"",0,'L');
						$pdf->Cell(80,4,$row2['name'],"",0,'L');
						if ($grp_code=='all'){
							$pdf->Cell(50,4,$row2['groupnm'],"",0,'L');
						}
						
						if ($row2['is_socialized'])
							$socialized = 'YES';
						else	
							$socialized = 'NO';
							
						$pdf->Cell(20,4,$socialized,"",0,'L');
						
						if ($row['is_cash'])
							$gross_amount = $row2['price_cash_orig'];
						else
							$gross_amount = $row2['price_charge'];
						
						$discounted_amount = $row2['price_cash'];	
						
						#$pdf->Cell(2,4,'Php',"",0,'L');
						$pdf->Cell(25,4,number_format($gross_amount,2),"",0,'R');
						$pdf->Cell(5,4,'',"",0,'R');
						$pdf->Cell(25,4,number_format($discounted_amount,2),"",1,'R');
					}
					
				}	
				$pdf->Cell(30,4,'',"",1,'L');
				$pdf->Cell(10,4,"","",0,'L');	
				
				if ($discountID!="all")	
					$pdf->Cell(290,4,'',"T",1,'L');
				else
					$pdf->Cell(320,4,'',"T",1,'L');
						
				$i++;
			}	
		
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
		$pdf->Cell(55,4,"TOTAL AMOUNT BALANCE","",0,'L');	
		$pdf->Cell(10,4,": Php","",0,'L');	
		$pdf->Cell(2,4,"","",0,'L');	
		$pdf->Cell(20,4,number_format($total_amount_bal, 2),"",0,'R');	
				
	}else{
		$pdf->SetFont('Times','',10);	
		$pdf->Ln($space*4);
		$pdf->Cell(337,4,'No query results available at this time...',"",0,'C');
	}
	
	$pdf->Output();	
?>