<?php
	#include("roots.php");
	require('./roots.php');
	
	#include_once($root_path."/classes/fpdf/fpdf.php");
	include_once($root_path."/classes/fpdf/pdf.class.php");
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');
	
	require_once($root_path.'include/care_api_classes/class_radiology.php');
	$srvObj=new SegRadio();
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
	
	
	require($root_path.'classes/adodb/adodb.inc.php');
	
	global $db;
	
	$pdf = new PDF("P",'mm','Legal');
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("P");
		
	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$space=2;
	
	$pdf->SetLeftMargin(0);
	
	$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,20,20);
		
	$pdf->SetFont("Times","B","10");
    $pdf->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');
	$pdf->Ln(1);
	$pdf->Cell(0,4,'DEPARTMENT OF HEALTH', $border_0,1,'C');
	$pdf->Ln(2);
	$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$borderNo,$newLineYes,'C');
	#$pdf->Cell(0,4,'OUTPATIENT and PREVENTIVE CARE CENTER',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","B","8");
    $pdf->Cell(0,4,'Quezon Ave., Digos City',$borderNo,$newLineYes,'C');
    $pdf->Ln(2);
	$pdf->SetFont("Times","B","10");
    $pdf->Cell(0,4,'DEPARTMENT OF RADIOLOGICAL & IMAGING SCIENCES',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","I","10");
	$pdf->Cell(0,4,'BORROWER\'S LIST',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","B","10");
	$pdf->Cell(0,4,date("F d, Y  h:i A"),$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	
	$unreturned = $srvObj->_searchRadioBorrowers('*',0,'ORDER BY borrow_nr ASC');
	#echo $srvObj->sql;
	$totalcount = $srvObj->record_count;
	
	$pdf->Ln($space*4);
	$pdf->SetFont('Arial','B',8);
	$pdf->Cell(10,5,"","",0,'L');	
	$pdf->Cell(40,5,'BORROWER\'S NAME',"TB",0,'L');
	$pdf->Cell(25,5,'DEPARTMENT',"TB",0,'L');
	$pdf->Cell(20,5,'FILM NO.',"TB",0,'L');
	$pdf->Cell(25,5,'EXAMINATION',"TB",0,'L');
	$pdf->Cell(30,5,'PATIENT NAME',"TB",0,'L');
	$pdf->Cell(30,5,'DATE BORROWED',"TB",0,'L');
	$pdf->Cell(15,5,'# OF DAYS',"TB",0,'L');
	$pdf->Cell(25,5,'PENALTY',"TB",0,'C');

	if ($totalcount){
		while($row=$unreturned->FetchRow()){
			$pdf->Cell(10,5,"","",0,'L');	
			
			if ($row['borrower_name']!="")
				$borrowers = $row['borrower_name'];
			else
				$borrowers = $row['patient_name'];	
			
			$x = $pdf->GetX();
 			$y = $pdf->GetY();
			$pdf->SetXY($x, $y);
			$pdf->MultiCell(40, 5, strtoupper($borrowers), '', 'L','');
			#$pdf->Cell(40,5,strtoupper($borrowers),"",0,'L');
			#$pdf->Cell(25,4,$row['sub_dept_name'],"",0,'L');
			
			$pdf->SetXY($x+40, $y);
			$pdf->MultiCell(25, 5, $row['sub_dept_name'], '', 'L','');
			
			$pdf->SetXY($x+65, $y);
			$pdf->Cell(20,5,$row['batch_nr'],"",0,'L');
			$pdf->Cell(25,5,$row['service_code'],"",0,'L');
			$pdf->SetXY($x+110, $y);
			#$pdf->Cell(40,5,$row['patient_name'],"",0,'L');
			$pdf->MultiCell(30, 5, $row['patient_name'], '', 'L','');
			
			$pdf->SetXY($x+140, $y);
			#$pdf->Cell(30,5,$row['date_borrowed'],"",0,'C');
			$pdf->MultiCell(30, 5, date("m/d/Y",strtotime($row['date_borrowed'])), '', 'C','');
			
			$current_date = date('Y-m-d');
			$borrowed_date = date("Y-m-d",strtotime($row['date_borrowed']));
			
			// Extract from $current_date
			$current_year = substr($current_date,0,4);
			$current_month = substr($current_date,5,2);
			$current_day = substr($current_date,8,2);

			// Extract from $borrowed date
			$borrowed_year = substr($borrowed_date,0,4);
			$borrowed_month = substr($borrowed_date,5,2);
			$borrowed_day = substr($borrowed_date,8,2);

			// create a string yyyymmdd 20071021
			$tempMaxDate = $current_year . $current_month . $current_day;
			$tempDataRef = $borrowed_year . $borrowed_month . $borrowed_day;

			$tempDifference = $tempMaxDate-$tempDataRef;
			#echo "<br>".$tempMaxDate." - ".$tempDataRef." = ".$tempDifference;

			// If the difference is GT 10 days show the date
			if($tempDifference > 2){
				$amount = $row['price'] + ($row['price']*0.30);
			}else{
				$amount = "0.00";
			}	
			#$pdf->Cell(20,5,'P 0.00',"",0,'R');
			$total_penalty += $amount;
			
			$pdf->SetXY($x+170, $y);
			#$pdf->Cell(30,5,$row['date_borrowed'],"",0,'C');
			$pdf->MultiCell(10, 5, $tempDifference, '', 'L','');
			
			$pdf->SetXY($x+180, $y);
			$pdf->MultiCell(25, 5,"Php ".number_format($amount,2,".",","), '', 'R','');
			$pdf->Ln($space*3);
		}
		$pdf->Cell(220,5,"","B",0,'C');
		$pdf->SetFont('Times','',10);	
		$pdf->Ln($space*5);
		$pdf->Cell(10,5,"","",0,'');
		$pdf->Cell(100,5,"TOTAL PENALTY TO BE COLLECTED AS OF ".date("F d, Y")." : ","",0,'');
		$pdf->SetFont('Times','B',12);	
		$pdf->Cell(40,5,"Php ".number_format($total_penalty,2,".",","),"B",0,'R');
	}else{
		$pdf->SetFont('Times','',10);	
		$pdf->Ln($space*4);
		$pdf->Cell(190,4,'No query results available at this time...',"",0,'C');
	}
	
	$pdf->Output();	
?>