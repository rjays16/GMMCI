<?php
	define('FPDF_FONTPATH','font/');
	require($root_path."/classes/fpdf/fpdf.php");
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	
	
	class PDF extends FPDF{
		
		//Page header

		function Header(){
			global $HTTP_SESSION_VARS;
			
			$borderYes="1";
			$borderNo="0";
			$newLineYes="1";
			$newLineNo="0";
			$fontsizeInput = 10;
			$space=2;
			
			$this->SetLeftMargin(26);
			$objInfo = new Hospital_Admin();
	
 			$this->Image('../../gui/img/logos/dmc_logo.jpg',50,10,20,20);
			
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
		
			$this->SetFont("Times","B",$fontsizeInput);
		   #$this->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');
			$this->Cell(0,4,$row['hosp_country'],$borderNo,$newLineYes,'C');
			$this->Ln(1);
			#$this->Cell(0,4,'DEPARTMENT OF HEALTH', $border_0,1,'C');
			$this->Cell(0,4,$row['hosp_agency'], $border_0,1,'C');
			$this->Ln(2);
			#$this->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$borderNo,$newLineYes,'C');
			$this->Cell(0,4,$row['hosp_name'],$borderNo,$newLineYes,'C');
			#$this->Cell(0,4,'OUTPATIENT and PREVENTIVE CARE CENTER',$borderNo,$newLineYes,'C');
			$this->Ln(2);
			$this->SetFont("Times","B",$fontsizeInput-2);
		   #$this->Cell(0,4,'JICA Bldg., JP Laurel Avenue, Davao City',$borderNo,$newLineYes,'C');
			$this->Cell(0,4,$row['hosp_addr1'],$borderNo,$newLineYes,'C');
		    $this->Ln(2);
			$this->SetFont("Times","B",$fontsizeInput);
		    $this->Cell(0,4,'DEPARTMENT OF PATHOLOGY AND CLINICAL LABORATORIES',$borderNo,$newLineYes,'C');
			$this->Ln(4);
			
			$this->SetFont("Times","B",$fontsizeInput+2);
			#$this->Cell(0,4,'OPD Patients\' List '.$HTTP_SESSION_VARS['fromdate']." ".$HTTP_SESSION_VARS['fromtime']." - ".$HTTP_SESSION_VARS['todate']." ".$HTTP_SESSION_VARS['totime'],$borderNo,$newLineYes,'C');
			#print_r($HTTP_SESSION_VARS);
			if ($HTTP_SESSION_VARS['fromtime']!='00:00:00')
				$fromtime = date("h:i A",strtotime($HTTP_SESSION_VARS['fromtime']));
			else
				$fromtime = "";	 
			
			if ($HTTP_SESSION_VARS['totime']!='00:00:00')
				$totime = date("h:i A",strtotime($HTTP_SESSION_VARS['totime']));
			else
				$totime= "";	
			
			if (($HTTP_SESSION_VARS['fromdate'])&&($HTTP_SESSION_VARS['todate']))
				$this->Cell(0,4,$HTTP_SESSION_VARS['patient_type'].' Patients\' List '.$HTTP_SESSION_VARS['fromdate']." ".$fromtime." - ".$HTTP_SESSION_VARS['todate']." ".$totime,$borderNo,$newLineYes,'C');
			else
				$this->Cell(0,4,$HTTP_SESSION_VARS['patient_type'].' Patients\' List (All Records)',$borderNo,$newLineYes,'C');	
			
			$this->Ln(4);
	
			$this->SetFont("Times","",$fontsizeInput+2);
	
			$this->Ln($space*4);
			$this->SetFont('Arial','B',$fontsizeInput-1);
			$this->Cell(10,4,"","",0,'L');	
			$this->Cell(32,8,'DATE/TIME',"TB",0,'L');
			$this->Cell(25,8,'REFERENCE',"TB",0,'C');
			$this->Cell(50,8,'PATIENT\'S NAME',"TB",0,'L');
			$this->Cell(25,8,'HOSP. NO.',"TB",0,'L');
			$this->Cell(25,8,'BIRTH DATE',"TB",0,'C');
			$this->Cell(12,8,'AGE',"TB",0,'C');
			$this->Cell(10,8,'SEX',"TB",0,'C');
			$this->Cell(10,8,'QTY',"TB",0,'C');
			$this->Cell(60,8,'PROCEDURE',"TB",0,'L');
			$this->Cell(35,8,'FROM',"TB",0,'L');
			$this->Cell(30,8,'PAID',"TB",0,'C');
			$this->Ln($space*6);
			
		}

		//Page footer
		function Footer(){
		   //Go to 0.5 cm from bottom
   		#$this->SetY(-7);
		$this->SetY(1);

    		$this->SetFont('Arial','I',10);
			//Page number
    		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}',0,0,'R');
		}
		
		#added by VAN 02-12-08
		#http://www.fpdf.de/downloads/addons/49/
		function WordWrap(&$text, $maxwidth){
    		$text = trim($text);
		    if ($text==='')
      	  return 0;
		   $space = $this->GetStringWidth(' ');
		   $lines = explode("\n", $text);
		   $text = '';
		   $count = 0;

		   foreach ($lines as $line){
		     $words = preg_split('/ +/', $line);
      	  $width = 0;

	        foreach ($words as $word){
	           $wordwidth = $this->GetStringWidth($word);
   	         if ($width + $wordwidth <= $maxwidth){
	               $width += $wordwidth + $space;
   	            $text .= $word.' ';
      	      }
         	   else{
	               $width = $wordwidth + $space;
   	            $text = rtrim($text)."\n".$word.' ';
      	         $count++;
         	   }
        	}
 	       $text = rtrim($text)."\n";
   	     $count++;
	    }
   	 $text = rtrim($text);
	    return $count;
		}
		
	} #end of pdf class
	
?>