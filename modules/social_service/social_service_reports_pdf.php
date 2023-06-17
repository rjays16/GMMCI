<?php
	include("roots.php");
	require('./roots.php');
	
	#include_once($root_path."/classes/fpdf/fpdf.php");
	include_once($root_path."/classes/fpdf/pdf.class.php");
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');
	
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	$objInfo = new Hospital_Admin();
	
	//For social service statistical reports
	global $db;
	$socialStat = array();
	$socialStat['title'] = array("Encircle Sat. Sun. & Holidays", 
											 "Interview w/ patients", 
											 "Family/Collateral Interview", 
											 "Referrels to Help Net, etc.", 
											 "Acted upon Referrals (outside)", 
											 "Ward Referrals (Inside)", 
											 "Case Work", 
											 "Group Work",
											 "Seminars/ Meeting Attended");	
	
	//Get Classification type for social services
	$sql_class = "SELECT discountdesc FROM seg_discount";
	if($result = $db->Execute($sql_class)){
		$myArray = array();
		if($recordCount['class'] = $result->RecordCount()){
			for($y=0; $y < $recordCount['class']; $y++){
				$myArray[$y] = $result->FetchRow();
			}
			$socialStat['class']  = $myArray;
		}	
	}
	//Specific services	
	$socialStat['specific'] = array("Home Conduction /Family Tracing", 
											  "Material Assistance\n(food, clothing)", 
											  "Financial Assistance", 
											  "Transportaion Assistance", 
											  "Public Service", 
											  "Placement/Temporary Shelter", 
											  "Discharge Planning", 
											  "Blood Assistance",
											  "HIV/AIDS intake", 
											  "MDC intake", 
											  "Family Life Counselling", 
											  "Assistance in: Medicines", 
											  array("Laboratory", "Radiology", "Special Lab."), 
											  "OPS Clinical/Surgical procedures", 
											  "Others:"
											  );
	//Place of Origin										  
	$socialStat['origin'] = array("Davao City", 
											"Davao del Sur", 
											"Digos City", 
											"Tagum City", 
											"Panabo City", 
											"IGACOS", 
											"Davao Oriental", 
											"Comval", 
											"Gen. Santos", 
											"Sarangani Province", 
											"Cotabato City", 
											"North Cotabato", 
											"Kidapawan", 
											"South Cotabato", 
											"Koranadal City", 
											"Maguindanao", 
											"Sultan Kudarat", 
											"Tacurong City", 
											"Surigao Sur", 
											"Bislig City", 
											"Bukidnon", 
											"Valencia City", 
											"Agusan Norte", 
											"Agusan Sur");
	//Source Income
	$socialStat['source_income'] = array("Farmer(Owner)", 
													 "Farmer(tenant)", 
													 "Fisherman", 
													 "Laborer(const/market/w'er. etc)", 
													 "Carpenter", 
													 "Mason", 
													 "Painter", 
													 "Plumber", 
													 "Electrician", 
													 "Driver(Private)", 
													 "Driver(Public)", 
													 "Tsisikad/pedicab", 
													 "Mechanic", 
													 "Welder", 
													 "Technician(ref, aircon, computer, etc.)", 
													 "Dressmaker/tailor", 
													 "Waiter/tress, Food Service Crew", 
													 "Janitorial Services", 
													 "Security Guard", 
													 "House helper/ Babysitter", 
													 "Laundry worker", 
													 "Masseur/masseuse", 
													 "Caregiver", 
													 "Photographer", 
													 "Caddie", 
													 "GRO", 
													 "Sari-sari Store owner", 
													 "Selling/peddling(food/dry goods)", 
													 "Supported by parents", 
													 "Supported by sublings", 
													 "Abandoned/scavenger", 
													 "Institutionalized", 
													 "Minister/Pastor/church w'er", 
													 "Practicing Professional", 
													 "PNP", 
													 "Soldiers (Military)", 
													 "Pensioner(GSIS & SSS)", 
													 "Public Officials", 
													 "BHW");
	
	
	//Instantiate PDF Class
	//Set paper size to Legal , measurement in mm,  
	$pdf = new PDF("L",'mm','Legal');
	$pdf->AliasNbPages();   #--added
	$pdf->AddPage("L");
	
	$borderYes="1";
	$borderNo="0";
	$newLineYes="1";
	$newLineNo="0";
	$space=2;
	//Display DMC logo
	$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',100,10,20,20);
	
	if ($row = $objInfo->getAllHospitalInfo()) {			
		$row['hosp_agency'] = strtoupper($row['hosp_agency']);
		$row['hosp_name']   = strtoupper($row['hosp_name']);
	}
	else {
		$row['hosp_country'] = "Republic of the Philippines";
		$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
		$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
		$row['hosp_addr1']   = "Quezon Ave., Digos City";			
	}
	
	//Header Title	
	$pdf->SetFont("Times","B","10");
   #$pdf->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_country'],$borderNo,$newLineYes,'C');
	#$pdf->Cell(0,4,'Department of Health',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_agency'],$borderNo,$newLineYes,'C');
	#$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_name'],$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,$row['hosp_addr1'],$borderNo,$newLineYes,'C');
	//Document Title
   $pdf->Ln(2);
	$pdf->SetFont("Times","B","10");
   $pdf->Cell(0,4,'STATISTICAL REPORT',$borderNo,$newLineYes,'C');
	$pdf->Ln(8);
	
	//Left side : Period covered 
	$pdf->SetFont("Times","B","12");
	$pdf->Cell(32,4,'Period Covered :',$borderNo,$newLineNo,'L');
	$pdf->Cell(201,4,'______________________________',$borderNo,$newLineNo,'');
	$pdf->Cell(32,4,'Social Worker :',$borderNo,$newLineNo,'L');
	$pdf->Cell(20,4,'______________________________',$borderNo,$newLineYes,'');
	$pdf->Ln(2*2);
	
	
	//$pdf->Cell(0,4,print_r($result['class']),$borderNo,$newLineYes,'C');
	//$pdf->Cell(0,4,print_r($socialStat['class']),$borderNo,$newLineYes,'C');
	//$pdf->Cell(0,4,$recordCount." - ".$rCount ,$borderNo,$newLineYes,'C');
	
	$rCount = 0;
	//Start table
	foreach($socialStat as $statKey=>$statValue){
		switch ($statKey){
			case 'title':				
				$caseId = 'A';
				$pdf->SetFont("Times","","10");
				$rCount = 0;
				break;
			case 'class': //PRINT PATIENTS CLASSIFICATION TABLE
				$caseId = 'C';
				$pdf->SetFont("Times","B","10");
				
				$pdf->Cell(326, 8, "PATIENTS CLASSIFICATION", "TBLR", 1, 'L');	
				$rCount = $recordCount['class'];
				$pdf->SetFont("Times","","10");	

				break;
			case 'specific': //PRINT SPECIFIC SERVICES
				$arrayLength =0;
				$rCount = 0;
				$caseId = 'B';
				$pdf->SetFont("Times","B","10");
				$pdf->Cell(326, 8, "SPECIFIC SERVICES", "TBLR", 1, 'L');	
				$pdf->SetFont("Times","","10");	
				break;
			case 'origin':
				$arrayLength =0;
				$caseId = 'B';
				$pdf->SetFont("Times","B","10");
				$pdf->Cell(326, 8, "PLACE OF ORIGIN", "TBLR", 1, 'L');	
				$pdf->SetFont("Times","","10");	
				break;
			
			case 'source_income':
				$arrayLength =0;
				$rCount = 0;
				$caseId = 'B';
				$pdf->SetFont("Times","B","10");
				$pdf->Cell(326, 8, "SOURCE OF INCOME", "TBLR", 1, 'L');	
				$pdf->SetFont("Times","","10");	
				break;
		}//end of switch statement
				
		switch($caseId){
			case 'A':
				$arrayLength = ($rCount == 0)? count($statValue) : $rCount;
				for($i=0; $i < $arrayLength ; $i++){ 
					$len = (strlen($statValue[$i]) > 40) ? strlen($statValue[$i]) : 48;
					$pdf->Cell($len+5, 4, $statValue[$i], "TBRL", 0, '');	
					//print only the first row of the table
					if($i == 0){ 
						$newLine = 0;
						$coltotal = 0;
						$colWidth = 0;
						for($k=1; $k<=32; $k++){
							$newLine = ($k==32) ? 1 : 0;
							$coltotal = ($k!=32)? $k : 'Total';
							$colWidth = ($k!=32)? 8 : 25;
							$pdf->Cell($colWidth, 4, $coltotal, "TBRL", $newLine, 'C');
						} // end for loop
					//print the second row and the succedding rows of the table
					}else{
						$newLine = 0;
						$colWidth = 0;
						for($j=1; $j<=32; $j++){
							$newLine = ($j==32) ? 1 : 0;
							$colWidth = ($j!=32)? 8 : 25;
							$pdf->Cell($colWidth, 4, ' ', "TBRL", $newLine, 'C');
						}//end for loop
					}// end if-else statement
				}// end for loop -> $statValue
				break;
			case 'B':
				$arrayLength = ($rCount == 0)? count($statValue) : $rCount;
				for($i=0; $i < $arrayLength ; $i++){ 
					$len = (strlen($statValue[$i]) > 40) ? strlen($statValue[$i]) : 48;	
					$pdf->Cell($len+5, 4, $statValue[$i], "TBRL", 0, '');	
					$newLine = 0;
					$colWidth = 0;
					for($j=1; $j<=32; $j++){
						$newLine = ($j==32) ? 1 : 0;
						$colWidth = ($j!=32)? 8 : 25;
						$pdf->Cell($colWidth, 4, ' ', "TBRL", $newLine, 'C');
					}//end for loop
				}// end for loop -> $statValue
				break;
			case 'C':
				if(is_array($statValue)){
					foreach($statValue as $key=>$value){ 
						for($i=0; $i < count($value); $i++){
							$len = (strlen($value[$i]) > 40) ? strlen($value[$i]) : 48;	
							if(!empty($value[$i])){
								$pdf->Cell($len+5, 4, $value[$i], "TBRL", 0, '');			
								$newLine = 0;
								$colWidth = 0;
								for($j=1; $j<=32; $j++){
									$newLine = ($j==32) ? 1 : 0;
									$colWidth = ($j!=32)? 8 : 25;
									$pdf->Cell($colWidth, 4, ' ', "TBRL", $newLine, 'C');
								}//end for loop
							}
						}// end for loop -> $statValue
					}//end foreach
				}//end if 
				break;
		}//end switch statement ($caseId)
		
	}// end foreach loop
	
	
	//create pdf outputs	
	$pdf->Output();	
?>