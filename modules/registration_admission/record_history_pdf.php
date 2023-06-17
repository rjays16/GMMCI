<?php

	#added by VAN 02-12-08
	require('./roots.php');

	include_once($root_path."classes/fpdf/pdf.class.php");
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');
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

	$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,20,20);

	$pdf->SetFont("Times","B","10");
   $pdf->Cell(0,4,'Republic of the Philippines',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$borderNo,$newLineYes,'C');
	$pdf->Cell(0,4,'OUTPATIENT and PREVENTIVE CARE CENTER',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);
	$pdf->SetFont("Times","B","8");
   $pdf->Cell(0,4,'Quezon Ave., Digos City',$borderNo,$newLineYes,'C');
   $pdf->Ln(2);
	$pdf->SetFont("Times","B","10");
   $pdf->Cell(0,4,'DB Record\'s History',$borderNo,$newLineYes,'C');
	$pdf->Ln(2);

	#echo "table, pid = ".$table." - ".$pid;
	#echo "<br>db = ".$db;

	switch($table){
   	 case 'care_person':  $sql="SELECT name_last AS \"LastName\", name_first AS \"FirstName\", history FROM care_person WHERE pid='$pid'";
		 break;
		 case 'care_encounter':  $sql=$sql="SELECT p.name_last AS  \"LastName\", p.name_first AS  \"FirstName\", e.history
	   	                                 FROM care_person AS p, care_encounter AS e WHERE p.pid=e.pid AND e.encounter_nr='$pid'";
		 break;
	}

	$result=$db->Execute($sql);
	$history=$result->FetchRow();

	$name = ucwords(strtoupper($history['LastName'])).' '.ucwords(strtoupper($history['FirstName']));

	$pdf->Ln($space*4);
	$pdf->SetFont("Times","","10");
	$pdf->Cell(270,4,'Patient Name : '.$name,"",0,'L');
	$pdf->Ln($space*4);
	$pdf->Cell(270,4,'DB Record\'s History',"",0,'L');
	$pdf->Ln($space*4);


	if (!empty($history['history'])){
		$buffer=nl2br($history['history']);
		$buffer=str_replace('<br />','|',$buffer);
	  	$dbhistory = explode('|',$buffer);
		#print_r($dbhistory);
		#$pdf->Cell(270,4,$buffer,"",0,'L');
		#echo sizeof($dbhistory);
		for($i=0; $i<sizeof($dbhistory); $i++){
			$pdf->Cell(0,4,$dbhistory[$i],"",0,'L');
			$pdf->Ln($space*2);
		}
	}

	$pdf->Output();
?>