<?php
	define('FPDF_FONTPATH','font/');
	require($root_path."/classes/fpdf/fpdf.php");
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
	
	class PDF extends FPDF{
		
		//Page header

		function Header(){
			global $HTTP_SESSION_VARS;
			
			$this->SetLeftMargin(5);
		
			$borderYes="1";
			$borderNo="0";
			$newLineYes="1";
			$newLineNo="0";
			$space=2;
				
			$objInfo = new Hospital_Admin();
		
			$this->Image('../../gui/img/logos/dmc_logo.jpg',20,10,20,20);
			
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
		
			$this->SetFont("Times","B","10");
		   #$this->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');
			$this->Cell(0,4,$row['hosp_country'],$borderNo,$newLineYes,'C');
			$this->Ln(1);
			#$this->Cell(0,4,'DEPARTMENT OF HEALTH', $border_0,1,'C');
			$this->Cell(0,4,$row['hosp_agency'], $border_0,1,'C');
			$this->Ln(2);
			#$this->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$borderNo,$newLineYes,'C');
			$this->Cell(0,4,$row['hosp_name'],$borderNo,$newLineYes,'C');
			#$pdf->Cell(0,4,'OUTPATIENT and PREVENTIVE CARE CENTER',$borderNo,$newLineYes,'C');
			$this->Ln(2);
			$this->SetFont("Times","B","8");
		   #$this->Cell(0,4,'JICA Bldg., JP Laurel Avenue, Davao City',$borderNo,$newLineYes,'C');
			$this->Cell(0,4,$row['hosp_addr1'],$borderNo,$newLineYes,'C');
		   $this->Ln(2);
			$this->SetFont("Times","B","10");
		   $this->Cell(0,4,'DEPARTMENT OF RADIOLOGICAL & IMAGING SCIENCES',$borderNo,$newLineYes,'C');
			$this->Ln(2);
	
			$this->SetFont("Times","B","10");
		   $this->Cell(0,4,$HTTP_SESSION_VARS['section_name']." (".$HTTP_SESSION_VARS['section_id'].")",$borderNo,$newLineYes,'C');
			$this->Ln(2);
			$this->SetFont("Times","I","10");
			$this->Cell(0,4,'OPS & IN-PATIENT REGISTER',$borderNo,$newLineYes,'C');
			$this->Ln(2);
	
			$this->SetFont("Times","B","10");
		   $this->Cell(0,4,'RAD. TECH. ON DUTY : '.$HTTP_SESSION_VARS['user'],0,$newLineNo,'C');
			$this->Ln(6);
			#echo $fromtime." - ".$totime;
	
			$fromtime = date("H:i:s",strtotime($fromtime));
			$totime = date("H:i:s",strtotime($totime));
	
			if (($fromtime=='00:00:00 AM')||($fromtime=='00:00:00 PM'))
				$fromtime = '00:00:00';
			if (($totime=='00:00:00 AM')||($totime=='00:00:00 PM'))
				$totime = '00:00:00';
	
			$this->Cell(10,4,'',0,$newLineNo,'L');	
			$this->SetFont("Times","","10");
		   $this->Cell(15,4,'DATE : ',0,$newLineNo,'L');
			$this->SetFont("Times","B","12");
		   $this->Cell(10,4,$HTTP_SESSION_VARS['date_req'],0,$newLineNo,'L');
	
			$this->Cell(230,4,'',0,$newLineNo,'');
	
			$this->SetFont("Times","","10");
		   $this->Cell(15,4,'SHIFT : ',0,$newLineNo,'L');
			$this->SetFont("Times","B","12");
		   $this->Cell(10,4,$HTTP_SESSION_VARS['shift'],0,$newLineNo,'L');
	
			$this->Ln(2);
	
	
			$this->SetFont("Times","B","10");
	
			#$fromtime = '00:00:00';
			#$totime = '00:00:00';
	
			$this->Ln($space*4);
			$this->SetFont('Arial','B',8);
			#$pdf->Cell(10,4,"","",0,'L');	
	
			$this->Cell(7,6,'',"TBRL",0,'C');
			$this->Cell(22,6,'HOSPITAL NO.',"TBRL",0,'C');
			$this->Cell(18,6,'OR NO.',"TBRL",0,'C');
			$this->Cell(13,6,'AMOUNT',"TBRL",0,'C');
			$this->Cell(18,6,'FILM NO.',"TBV",0,'C');
			$this->Cell(18,6,'REF. NO.',"TBRL",0,'C');
			$this->Cell(14,6,'TIME',"TBRL",0,'C');
			$this->Cell(50,6,'NAME OF PATIENT',"TBRL",0,'C');
			$this->Cell(10,6,'AGE',"TBRL",0,'C');
			$this->Cell(7,6,'SEX',"TBRL",0,'C');
			$this->Cell(16,6,'BIRTHDAY',"TBRL",0,'C');
			$this->Cell(40,6,'ADDRESS',"TBRL",0,'C');
			$this->Cell(40,6,'ADMITTING DIAGNOSIS',"TBRL",0,'C');
			$this->Cell(26,6,'PHYSICIAN',"TBRL",0,'C');
			$this->Cell(22,6,'WARD',"TBRL",0,'C');
			$this->Cell(25,6,'EXAMINATION',"TBRL",1,'C');
			
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