<?php

include("roots.php");
#include_once($root_path."/classes/fpdf/fpdf.php");
include_once($root_path."/classes/fpdf/cert-pdf.class.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

require_once($root_path.'/include/care_api_classes/class_drg.php');
$objDRG= new DRG;

include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

include_once($root_path.'include/care_api_classes/class_cert_med.php');

include_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj=new Ward;

include_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();


//$_GET['encounter_nr'] = 2007500006;

if($_GET['id']){
	if(!($encInfo = $enc_obj->getEncounterInfo($_GET['id']))){
		echo '<em class="warn"> sorry byt the page cannot be displayed!</em>';
		exit();
	}
	extract($encInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
	exit();
}

$obj_medCert = new MedCertificate($encounter_nr);
$confCertInfo = $obj_medCert->getConfCertRecord($encounter_nr);
#echo "sql = ".$enc_obj->sql;

$wardName = $ward_obj->WardName($encInfo['current_ward_nr']);

//set border
$border="1";
$border2="0";
$spacing =2;
// font setup
$fontSizeLabel = 8;
$fontSizeInput = 11;
$fontSizeText = 12;
$fontSizeHeader = 14;
//fontstyle setup
$fontStyle = "Courier";
$fontStyle2 = "Courier";


//instantiate fpdf class
#$pdf  = new FPDF("P","mm","Letter");
$pdf  = new PDF("P","mm","Letter");
$pdf->AddPage("P");

if ($row = $objInfo->getAllHospitalInfo()) {
		$row['hosp_agency'] = strtoupper($row['hosp_agency']);
		$row['hosp_name']   = strtoupper($row['hosp_name']);
	}
	else {
		//$row['hosp_country'] = "Republic of the Philippines";
		//$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
		$row['hosp_name']    = "Davao Medical Center";
		//$row['hosp_addr1']   = "JICA Bldg. JP Laurel Bajada, Davao City";
	}

//Header - Republic of the Philippines / Department of Health
// $pdf->SetFont($fontStyle, "", $fonSizeInput);
// $pdf->Cell(0,4,$row['hosp_country'], $border2,1,'C');
// $pdf->Cell(0,4,$row['hosp_agency'], $border2,1,'C');

//Hospital name- Davao Medical Center
$pdf->Ln(2);
$pdf->setFont($fontStyle,"B", $fontSizeHeader+2);
$pdf->Cell(0,4,$row['hosp_name'],$border2, 1, 'C');

//Hospital Address
$pdf->Ln(2);
$pdf->setFont($fontStyle,"", $fontSizeInput);
$pdf->Cell(0,4,$row['hosp_addr1'],$border2, 1, 'C');
#added by genz
$pdf->Cell(0,4,'Tel No. 082-553-2474',$border2,1,'C');
$pdf->Cell(0,4,'"PHIC Accredited Health Care Provider"',$border2,1,'C');
#end by genz

$pdf->Ln(10);
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(130, 3 , '', "",0,'');
$pdf->Cell(25, 3 , 'HRN :', "",0,'');
$pdf->SetFont($fontStyle,"B", $fontSizeText);
$pdf->Cell(45, 3 , $encInfo['pid'], "",1,'');

//File No.. Line -2
//$pdf->Ln(2);
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(130, 3 , '', "",0,'');
$pdf->Cell(25, 3 , 'CASE NO.:', "",0,'');
$pdf->SetFont($fontStyle,"B", $fontSizeText);
$pdf->Cell(45, 3 , $encounter_nr, "",0,'');

//Date .. Line - 3
$pdf->Ln(4);
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(130, 3 , '', "",0,'');
$pdf->Cell(25, 3 , 'DATE:', "",0,'');
$pdf->SetFont($fontStyle,"B", $fontSizeText);

if($confCertInfo["modify_dt"]!=NULL){
	$date_created = date("m/d/Y",strtotime($confCertInfo["modify_dt"]));
}elseif($confCertInfo["create_dt"]!=NULL){
	$date_created = date("m/d/Y",strtotime($confCertInfo["create_dt"]));
}else
	$date_created = @formatDate2Local(date('Y-m-d'),$date_format);

$pdf->Cell(45, 3 , ''.$date_created, "",0,'');

#$pdf->Cell(45, 3 , ''.@formatDate2Local(date('Y-m-d'),$date_format), "",0,'');

//Document Title - Medical Certificate  Line 4
$pdf->Ln(18);
$pdf->SetFont($fontStyle,"B", $fontSizeHeader-2);
$pdf->Cell(0,16 , 'C E R T I F I C A T E    O F    C O N F I N E M E N T', $border2,1,'C');
#$pdf->Image($root_path.'gui/img/logos/dmhi_logo.jpg',20,10,25,30); commented by art 01/18/2014
//added by art 01/18/2014
$pdf->Image('../image/dmc_logo.jpg',10,10,30,30);
// $pdf->Image('../image/dmc_logo.jpg',170,10,20,20);
//end art

//Salutation
$pdf->Ln(6);
$pdf->SetFont($fontStyle,"B", $fontSizeText);
$pdf->Cell(0,3, 'TO WHOM IT MAY CONCERN:', $border2,1,'L');

//Content text
$sex = ($sex == "m")? "MALE":"FEMALE";
#$address = trim($street_name).", ".trim($brgy_name).", ".trim($mun_name)." ".trim($zipcode)." ".trim($prov_name);
if (trim($brgy_name)=='NOT PROVIDED')
	$brgy_name = "";
else
	$brgy_name = trim($brgy_name).", ";

if (trim($mun_name)=='NOT PROVIDED')
	$mun_name = "";

$address = trim($street_name).", ".$brgy_name.trim($mun_name)." ".trim($prov_name);


if (($encounter_type==1)||($encounter_type==2)){
	$fromDate= "".@formatDate2Local($er_opd_datetime,$date_format);
	$name_doctor = $er_opd_admitting_physician_name;
}else{
	$fromDate= "".@formatDate2Local($admission_dt,$date_format);
	$name_doctor = $attending_physician_name;
}
/*
if ((isset($_GET['doc_name'])) && (!empty($_GET['doc_name']))){
	$name_doctor = $_GET['doc_name'];
}
#$name_doctor = str_replace('dr.', "", $name_doctor);
#$name_doctor = ereg_replace("dr.", "", $name_doctor);
$name_doctor = preg_replace("/(dr.)|(Dr.)/", "", $name_doctor);
*/
$name_doctor = $confCertInfo['attending_doctor'];

$toDate= "".@formatDate2Local($discharge_dt,$date_format);

if (empty($name_doctor))
	$name_doctor = "_____________________";

if (empty($wardName)){
	#$wardName = "_____________________";
	$wardName = " ";
}else{
	$wardName = ' at '.$wardName.' ward';
}

$pdf->Ln(7);
$pdf->SetFont($fontStyle,"",$fontSizeText);
//edited by daryl
$pdf->MultiCell(180, 6,'       This is to certify that '.utf8_decode(trim(stripslashes(strtoupper($name_last)))).", ".utf8_decode(trim(stripslashes(strtoupper($name_first)))).' '.utf8_decode(trim(stripslashes(strtoupper($name_middle)))).
					' , '.$age.' old, '.stripslashes(strtoupper($sex)).' '.
					', '.mb_strtoupper($civil_status).'  and a resident of '.utf8_decode(trim(stripslashes(strtoupper($address)))).
					' is/was confined in this institution on '.date("F d, Y",strtotime($fromDate)).
					' up to the present with the following findings / diagnosis'./*'under the service of '.trim($name_doctor).' '.trim($wardName).*/".",0,'J',0);

$pdf->ln(7);
// $pdf->Cell(0,3, 'Diagnosis:', $border2,1,'L');
// $pdf->Cell(0,3, '', $border2,1,'L');
$pdf->Cell(23,3, '', $border2,0,'L');
$pdf->SetFont($fontStyle,"B",$fontSizeText);

$pdf->MultiCell(180, 5,strtoupper($confCertInfo['admitting_diagnosis']) ,0,'L',0);

$pdf->ln(5);

$pdf->SetFont($fontStyle,"",$fontSizeText);

/*
$pdf->Ln(6);

$pdf->Cell(30,3, 'Nurse on Duty  :   ', $border2,0,'L');
if (empty($confCertInfo['nurse_on_duty']))
	$name_nurse = "_____________________";

else{
	$nurseInfo = $pers_obj->getPersonellInfo($confCertInfo['nurse_on_duty']);
	$nurse_middleInitial = "";
	if (trim($nurseInfo['name_middle'])!=""){
		$thisMI=split(" ",$nurseInfo['name_middle']);
		foreach($thisMI as $value){
			if (!trim($value)=="")
				$nurse_middleInitial .= $value[0];
		}
			if (trim($nurse_middleInitial)!="")
			$nurse_middleInitial = " ".$nurse_middleInitial.".";
	}
	$name_nurse = trim($nurseInfo['name_first'])." ".trim($nurseInfo['name_2'])." ".$nurse_middleInitial." ".trim($nurseInfo['name_last']);
}

$pdf->Cell(50,3, '   '.$name_nurse, $border2,0,'L');
*/
$pdf->Ln(3);

$pdf->SetFont($fontStyle,"B",$fontSizeText);
/*
if ($confCertInfo['is_vehicular_accident'])
	$accident = 'X';
else
	$accident = '____';

$pdf->Cell(15,3, $accident, $border2,0,'C');
$pdf->SetFont($fontStyle,"",$fontSizeText);
$pdf->Cell(50,3, 'Vehicular accident', $border2,1,'L');
*/
$pdf->Ln(2);
// $pdf->SetFont($fontStyle,"B",$fontSizeText);

// if ($confCertInfo['is_medico_legal']){
// 	$medico = 'X';
// 	$nonmedico = '____';
// }else{
// 	$medico = '____';
// 	$nonmedico = 'X';
// }
// $pdf->Cell(15,3, $medico, $border2,0,'C');
// $pdf->SetFont($fontStyle,"",$fontSizeText);
// $pdf->Cell(50,3, 'Medico legal', $border2,1,'L');

// $pdf->Ln(2);
// $pdf->SetFont($fontStyle,"B",$fontSizeText);
// $pdf->Cell(15,3, $nonmedico, $border2,0,'C');
// $pdf->SetFont($fontStyle,"",$fontSizeText);
// $pdf->Cell(50,3, 'Non-Medico legal', $border2,1,'L');
// $pdf->Cell(50,3, '', $border2,1,'L');
$pdf->SetFont($fontStyle,"",$fontSizeText);

/*
#added by VAN 06-13-08
if ($confCertInfo['is_medico_legal']){
	$pdf->Ln(5);
	$medico_cases = $enc_obj->getEncounterMedicoCases($encounter_nr,$pid);
	#echo "sql = ".$enc_obj->sql;
	$NOI = '';
	if ($medico_cases){
	while($result=$medico_cases->FetchRow()) {
		$NOI .= $result['medico_cases'].", ";
	}
	}else{
		$NOI .= " ";
	}
	if ($TOI!='00:00:00'){
		if (strstr($encInfo['TOI'],'24')){
			$TOI_val = "12:".substr($encInfo['TOI'],3,2)." AM";
		}else
			$TOI_val = date("h:i A",strtotime($TOI));
	}else
		$TOI_val = "Not Indicated";

	if ($DOI!='0000-00-00')
		#$DOI_val = date("F d, Y",strtotime($DOI));
		$DOI_val = date("m-d-Y",strtotime($DOI));
	else
		$DOI_val = "Not Indicated";

	$x = $pdf->GetX();
	$y = $pdf->GetY();
	#$pdf->SetXY($x+40,y+108);
	$pdf->SetXY($x+40,y+125);
	$pdf->cell(26, 6,"NOI :","",1,"L");
	$pdf->SetXY($x+55,y+127);
	#$pdf->cell(26, 6,"NOI :     ".strtoupper(substr(trim($NOI),0,-1)),"",1,"L");
	$pdf->MultiCell(100, 4,strtoupper(substr(trim($NOI),0,-1)), '', 'L','0');
	$pdf->SetX($x+40);
	$pdf->cell(26, 6,"POI :     ".strtoupper($POI),"",1,"L");
	$pdf->SetX($x+40);
	$pdf->cell(26, 6,"TOI :     ".$TOI_val,"",1,"L");
	$pdf->SetX($x+40);
	$pdf->cell(26, 6,"DOI :     ".$DOI_val,"",1,"L");

	$pdf->Ln(10);
}
*/
#--------------------

// $pdf->Ln(7);
// $pdf->SetFont($fontStyle,"",$fontSizeText);
// //edited by daryl
// $pdf->MultiCell(180, 6,'       This is to certify that '.utf8_decode(trim(stripslashes(strtoupper($name_last)))).", ".utf8_decode(trim(stripslashes(strtoupper($name_first)))).' '.utf8_decode(trim(stripslashes(strtoupper($name_middle)))).
// 					' , '.$age.' old, '.stripslashes(strtoupper($sex)).' '.
// 					', '.mb_strtoupper($civil_status).'  and a resident of '.utf8_decode(trim(stripslashes(strtoupper($address)))).
// 					' is/was confined in this institution on '.date("F d, Y",strtotime($fromDate)).
// 					' up to the present with the following findings / diagnosis'./*'under the service of '.trim($name_doctor).' '.trim($wardName).*/".",0,'J',0);

// $pdf->ln(7);

$pdf->Ln(5);
$pdf->Cell(40,3, '', "",0,'L');
$pdf->Cell(40,3, 'Operation Performed : ', $border2,0,'L');
$pdf->SetFont($fontStyle,'U',$fontSizeText);
$pdf->Cell(18,3, '', "",0,'L');
$pdf->MultiCell(83,5, mb_strtoupper($confCertInfo['operation_performed']), $border2,1,'L');
$pdf->SetFont('','');

$pdf->Ln(5);
$pdf->Cell(40,3, '', "",0,'L');
$pdf->Cell(40,3, '            Remarks : ', $border2,0,'L');
$pdf->SetFont($fontStyle,'U',$fontSizeText);
$pdf->Cell(18,3, '', "",0,'L');
$pdf->MultiCell(83,5, mb_strtoupper($confCertInfo['purpose']), $border2,1,'L');
$pdf->SetFont('','');

$pdf->Ln(5);
$pdf->Cell(40,3, '', "",0,'L');
$pdf->Cell(40,3, '       Requested By : ', $border2,0,'L');
$pdf->SetFont($fontStyle,'U',$fontSizeText);
$pdf->Cell(18,3, '', "",0,'L');
$pdf->MultiCell(83,5, ucwords(mb_strtolower($confCertInfo['requested_by'])), $border2,1,'L');
$pdf->SetFont('','');

$pdf->Ln(5);
$pdf->Cell(40,3, '', "",0,'L');
$pdf->Cell(40,3, 'Relation to patient : ', $border2,0,'L');
$pdf->SetFont($fontStyle,'U',$fontSizeText);
$pdf->Cell(18,3, '', "",0,'L');
$pdf->Cell(83,5, ucwords(mb_strtolower($confCertInfo['relation_to_patient'])), $border2,1,'L');
$pdf->SetFont('','');

$pdf->Ln(20);


if ($confCertInfo['is_doc_sig']){
	$docInfo = $pers_obj->getPersonellInfo($confCertInfo['dr_nr']);
	$dr_middleInitial = "";
	if (trim($docInfo['name_middle'])!=""){
		$thisMI=split(" ",$docInfo['name_middle']);
		foreach($thisMI as $value){
			if (!trim($value)=="")
				$dr_middleInitial .= $value[0];
		}
			if (trim($dr_middleInitial)!="")
			$dr_middleInitial = " ".$dr_middleInitial.".";
	}
	#$name_doctor = "Dr. ".trim($docInfo['name_first'])." ".trim($docInfo['name_2'])." ".$dr_middleInitial." ".trim($docInfo['name_last']);
	$name_doctor = trim($docInfo['name_first'])." ".trim($docInfo['name_2'])." ".trim($dr_middleInitial)." ".trim($docInfo['name_last']);


	//Doctor Name
	$pdf->setFont($fontStyle,"B",$fontSizeText);
	$pdf->Cell(116,6,'',"",0,"");
	$strlen = strlen(mb_strtoupper($name_doctor));
	$pdf->Cell(2,6,"_____________________________","",0,"L");
	$pdf->Cell($strlen*3,6,mb_strtoupper($name_doctor).", MD","",0,"L");

	$pdf->Ln(5);
	$pdf->setFont($fontStyle,"",$fontSizeText);
	$pdf->Cell(116,6,'',"",0,"");
	$pdf->Cell(75,6,"Attending Physician","",0,"L");
	#$pdf->Cell(155,6,"Attending Physician","",0,"R");

	$pdf->Ln(6);
	$pdf->Cell(116,6,'',"",0,"");
	$pdf->Cell(20,6,"Lic no.","",0,"L");
	$pdf->Cell(10,6,$docInfo['license_nr'],"",0,"L");
	// $pdf->Ln(5);
	// $pdf->Cell(116,6,'',"",0,"");
	// $pdf->Cell(20,6,"PTR no.","",0,"L");
	// $pdf->Cell(10,6,$confCertInfo['ptr_no'],"",0,"L");

	#$pdf->Cell(168,6,"Lic No. _______________","",0,"R");
}else{
	$pdf->Ln(5);
	//Medical Staff Officer Name
	$pdf->setFont($fontStyle,"B",$fontSizeText);
	$pdf->Cell(116,6,'',"",0,"");
	#edited by VAN 06-30-2010
	#added by VAN 06-14-08
	if ($confCertInfo['is_doc_sig']){
			$officer_info = $pers_obj->getPersonellInfo($confCertInfo['dr_nr']);
			$officer_middleInitial = "";
			if (trim($officer_info['name_middle'])!=""){
				$thisMI=split(" ",$officer_info['name_middle']);
				foreach($thisMI as $value){
					if (!trim($value)=="")
						$officer_middleInitial .= $value[0];
				}
					if (trim($officer_middleInitial)!="")
					$officer_middleInitial = " ".$officer_middleInitial.".";
			}

			$name_officer = trim($officer_info['title'])." ".trim($officer_info['name_first'])." ".trim($officer_info['name_2'])." ".$officer_middleInitial." ".trim($officer_info['name_last']);
			$name_officer = mb_strtoupper($name_officer);
			$officer_position = $officer_info['job_position'];
	}else{
			$sig_info = $pers_obj->get_Signatory('confcert');

			if ($confCertInfo['dr_nr']){
				$officer_info = $pers_obj->getPersonellInfo($confCertInfo['dr_nr']);
				$officer_middleInitial = "";
				if (trim($officer_info['name_middle'])!=""){
					$thisMI=split(" ",$officer_info['name_middle']);
					foreach($thisMI as $value){
						if (!trim($value)=="")
							$officer_middleInitial .= $value[0];
					}
						if (trim($officer_middleInitial)!="")
						$officer_middleInitial = " ".$officer_middleInitial.".";
				}

				#$name_officer = trim($officer_info['title'])." ".trim($officer_info['name_first'])." ".trim($officer_info['name_2'])." ".$officer_middleInitial." ".trim($officer_info['name_last']);
				$name_officer = trim($officer_info['name_first'])." ".trim($officer_info['name_2'])." ".$officer_middleInitial." ".trim($officer_info['name_last']);
				#$name_officer = mb_strtoupper($name_officer);
				$name_officer2 = mb_strtoupper($name_officer);
				$officer_position2 = $officer_info['job_position'];

				$name_officer = mb_strtoupper($sig_info['name']);
				$officer_position = $sig_info['signatory_position'];

				$withfor  = 0;
				if (trim($sig_info['personell_nr'])!=trim($confCertInfo['dr_nr']))
					$withfor = 1;

			}else{
				#$sig_info = $pers_obj->get_Signatory('confcert');
				$name_officer = mb_strtoupper($sig_info['name']);
				$officer_position = $sig_info['signatory_position'];
			}
	}
	if ($withfor){
		$pdf->setFont($fontStyle,"",$fontSizeText);
		$pdf->Cell(75,6,$name_officer2,"",0,"L");
		$pdf->Ln(5);
		$pdf->Cell(116,6,'',"",0,"");
		$pdf->Cell(8,6,"for ","",0,"L");
		$pdf->setFont($fontStyle,"B",$fontSizeText);
		$pdf->Cell(75,6,$name_officer,"",1,"L");
		$pdf->setFont($fontStyle,"",$fontSizeText);
		$pdf->Cell(116,6,'',"",0,"");
		$pdf->Cell(75,6,$officer_position,"",1,"L");
	}else{
		$pdf->Cell(75,6,$name_officer,"",0,"L");
		$pdf->Ln(5);
		$pdf->setFont($fontStyle,"",$fontSizeText);
		$pdf->Cell(116,6,'',"",0,"");
		$pdf->Cell(75,6,$officer_position,"",1,"L");
	}
	$pdf->Cell(116,6,'',"",0,"");
}

// $pdf->SetFont($fontStyle,"B",$fontSizeText);
// $pdf->Ln(5);
// $pdf->Cell(30,6,"NOT VALID","",0,"R");	
// $pdf->Ln();

//edited by daryl

//$pdf->Cell(50,6,"WITHOUT ".$row['hosp_id']." SEAL","",0,"R");

// $pdf->Ln(20);
// $pdf->SetFont($fontStyle,"B",$fontSizeText);

// $pdf->Cell(5, 3 , '', "", 0,'');

// if ($confCertInfo["modify_id"])
// 	$encoder = $confCertInfo["modify_id"];
// else
// 		$encoder = $confCertInfo["create_id"];
// $pdf->Cell(0, 3 , 'Prepared by : ', "", 1,'');
// $pdf->Cell(20, 3 , ' ', "", 1,'');
// $pdf->Cell(15, 3 , ' ', "", 0,'');
// $strprepared = strlen($confCertInfo['prepared_by']);
// $pdf->Cell(10, 3 ,"______________________________", "", 0,'');
// $pdf->Cell($strprepared*3, 3 , strtoupper($confCertInfo['prepared_by']), "", 1,'');
// $pdf->ln(1);
// $pdf->SetFont($fontStyle,"",$fontSizeText);
// $pdf->Cell(20, 3 , ' ', "", 0,'');
// $pdf->Cell($strprepared*3, 3 , "", "", 0,'');
#$pdf->Cell(0, 3 , 'Encoded by : '.$encoder, "", 0,'');
// $pdf->encoder = $encoder;
//print pdf


//added by art 01/10/2014
// $pdf->code = "";
//end art
$pdf->Output();

?>