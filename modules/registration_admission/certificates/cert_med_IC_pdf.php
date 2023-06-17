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

include_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;


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
#echo "sql = ".$enc_obj->sql;
$obj_medCert = new MedCertificate($encounter_nr);
$medCertInfo = $obj_medCert->getMedCertRecord($encounter_nr);

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
$fontStyle = "Arial";
$fontStyle2 = "Times";
 

//instantiate fpdf class
#$pdf  = new FPDF("P","mm","Letter");
$pdf  = new PDF("P","mm","Letter");
$pdf->AddPage("P");

$pdf->SetTopMargin(2);
/*
//Header - Republic of the Philippines / Department of Health
$pdf->SetFont($fontStyle, "", $fonSizeInput);
$pdf->Cell(0,4,'Republic of the Philippines', $border2,1,'C');
$pdf->Cell(0,4,'DEPARTMENT OF HEALTH', $border2,1,'C');

//Hospital name- Gonzales Maranan Medical Center Incorporated
$pdf->Ln(2);
$pdf->setFont($fontStyle,"B", $fontSizeHeader-2);
$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$border2, 1, 'C');

//Hospital Address
$pdf->Ln(2);
$pdf->setFont($fontStyle,"", $fontSizeInput);
$pdf->Cell(0,4,'Bajada, Davao City',$border2, 1, 'C');
*/

$pdf->Ln(10);

//File No.. Line -2 
$pdf->Ln(2);
$pdf->SetFont($fontStyle,"", $fontSizeLabel+3);
$pdf->Cell(130, 3 , '', "",0,'');
$pdf->Cell(25, 3 , 'HRN:', "",0,'');
$pdf->SetFont($fontStyle,"B", $fontSizeLabel+4);
$pdf->Cell(45, 3 , $pid, "",0,'');

$pdf->Ln(4);
$pdf->SetFont($fontStyle,"", $fontSizeLabel+3);
$pdf->Cell(130, 3 , '', "",0,'');
$pdf->Cell(25, 3 , 'CASE NO.:', "",0,'');
$pdf->SetFont($fontStyle,"B", $fontSizeLabel+4);
$pdf->Cell(45, 3 , $encounter_nr, "",0,'');

$toDate= "".@formatDate2Local($discharge_dt,$date_format);

//Date .. Line - 3
$pdf->Ln(4);
$pdf->SetFont($fontStyle,"", $fontSizeLabel+3);
$pdf->Cell(130, 3 , '', "",0,'');
$pdf->Cell(25, 3 , 'DATE:', "",0,'');
$pdf->SetFont($fontStyle,"B", $fontSizeLabel+4);

#$pdf->Cell(45, 3 , ''.@formatDate2Local(date('Y-m-d'),$date_format), "",0,'');

if($medCertInfo["create_dt"]!=NULL){
	$date_created = date("m/d/Y",strtotime($medCertInfo["create_dt"]));
}elseif ($medCertInfo["modify_dt"]!=NULL){
	$date_created = date("m/d/Y",strtotime($medCertInfo["modify_dt"]));
}else
	$date_created = @formatDate2Local(date('Y-m-d'),$date_format);

$pdf->Cell(45, 3 , ''.$date_created, "",0,'');

#$pdf->Cell(45, 3 , ''.$toDate, "",0,'');
		
//Document Title - Medical Certificate  Line 4
$pdf->Ln(4);
$pdf->SetFont($fontStyle,"B", $fontSizeHeader+2);
$pdf->Cell(0,16 , 'M E D I C A L    C E R T I F I C A T E', $border2,1,'C');
$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,25,30);

//Salutation
$pdf->Ln(6);
$pdf->SetFont($fontStyle,"", $fontSizeText);
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

#doctor's name was commented by VAN 04-28-08
if (($medCertInfo['consultation_date']!='0000-00-00') && ($medCertInfo['consultation_date']!=""))
	$er_opd_datetime = $medCertInfo['consultation_date'];
	
if (($encounter_type==1)||($encounter_type==2)){
	$fromDate= "".@formatDate2Local($er_opd_datetime,$date_format);
	#$name_doctor = $er_opd_admitting_physician_name;
}else{
	$fromDate= "".@formatDate2Local($admission_dt,$date_format);
	#$name_doctor = $attending_physician_name;
}
/*
if ((isset($_GET['doc_name'])) && (!empty($_GET['doc_name']))){
	$name_doctor = $_GET['doc_name'];
}
#$name_doctor = str_replace('dr.', "", $name_doctor);
#$name_doctor = ereg_replace("dr.", "", $name_doctor);
$name_doctor = preg_replace("/(dr.)|(Dr.)/", "", $name_doctor);
*/
#edited by VAN 04-28-08
/*
if ($medCertInfo['is_doc_sig']){
	$docInfo = $pers_obj->getPersonellInfo($medCertInfo['dr_nr']);
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
	$name_doctor = "Dr. ".$docInfo['name_first']." ".$docInfo['name_2']." ".$dr_middleInitial." ".$docInfo['name_last'];
}else{

}	
*/
#$toDate= "".@formatDate2Local($discharge_dt,$date_format);

$pdf->Ln(7);
$pdf->SetFont($fontStyle,"",$fontSizeText);

if (empty($age))
	$age = "___";
	
if (($encounter_type==3)||($encounter_type==4)){
	$confine = ", confined";
	$dateconfine = $fromDate.' to '.$toDate;
}else{
	$confine = "";
	$dateconfine = $fromDate;
}	

$pdf->MultiCell(180, 6,'            This is to certify that '.stripslashes(strtoupper($name_last)).", ".stripslashes(strtoupper($name_first)).' '.stripslashes(strtoupper($name_middle)).
					' , '.$age.' old, '.stripslashes(strtoupper($sex)).' '.
					', '.mb_strtoupper($civil_status).'  and a resident of '.trim(stripslashes(strtoupper($address))).
					' was examined, treated '.$confine.' in this hospital on/from '.$dateconfine.
					' with the following findings/diagnosis.',0,'J',0);	

$pdf->Ln(5);
if ($encounter_type==1){
	$pdf->Cell(0,3,'(ER Consultation)',"",1,'L');
}elseif ($encounter_type==2){
	$pdf->Cell(0,3,'(OPD Consultation)',"",1,'L');
}else{
	$pdf->Cell(0,3,'(chart/phic)',"",1,'L');
}

#added by VAN 06-12-08
#if (($encounter_type==3)||($encounter_type==4)){
if ($medCertInfo['is_medico_legal']==0){

//Diagnosis
$pdf->Ln(5);
/*
$pdf->Cell(75,6, 'burn', $border2,1,'L');
$pdf->MultiCell(75,6,'',0,'J',0);
$pdf->MultiCell(100,6,"Diagnosis : \n".$medCertInfo['diagnosis_verbatim'],0,'J',0);
*/
$pdf->Cell(10,3,'',"",0,'L');
#$pdf->MultiCell(175,6,"Diagnosis : \n".strtoupper($medCertInfo['diagnosis_verbatim']),0,'J',0);
$pdf->MultiCell(175,6,"Diagnosis : ",0,'J',0);
$pdf->Ln(5);
$pdf->SetFont($fontStyle,"B",$fontSizeText);
$pdf->Cell(30,3,'',"",0,'L');
$pdf->MultiCell(175,6,strtoupper($medCertInfo['diagnosis_verbatim']),0,'J',0);

#$pdf->MultiCell(175,6,'PREGNANCY UTERINE TERM, CEEPHALIX, DELIVERED LIVE BIRTH BABY BOY, '.
#				'OLIGOHYDRAMNIOS SEVERS G1P1 (1-0-0-1).',0,'J',0);

//Operation
$pdf->SetFont($fontStyle,"",$fontSizeText);
if (trim($medCertInfo['procedure_verbatim'])!=""){
$pdf->Ln(10);
	#$procedureVerbatim="OPERATION: \n".$medCertInfo['procedure_verbatim'];
	$procedureVerbatim="OPERATION:";
	$pdf->Cell(10,3,'',"",0,'L');
	$pdf->MultiCell(175,6,$procedureVerbatim,0,'J',0);
	$pdf->Ln(5);
	$pdf->SetFont($fontStyle,"B",$fontSizeText);
	$pdf->Cell(20,3,'',"",0,'L');
	$pdf->MultiCell(175,6,strtoupper($medCertInfo['procedure_verbatim']),0,'J',0);
}

}

#$pdf->MultiCell(180,6,'OPERATION: '.
#					'LOW SEGMENT TRANSVERSE CAESAREAN SECTION I. ',0,'J',0);
#$pdf->Cell(20, 6,"(01-09-2007)","",0,"J");
					
//Advised		
#$pdf->Ln(15);
$pdf->SetFont($fontStyle,"",$fontSizeText);
if ($medCertInfo['is_medico_legal'])
	$pdf->Ln(5);
	
#added by VAN 06-12-08
#$encounter_nr='2008000121';
#$pid = '10000434';
#if ($encounter_type==1){
#if ($medCertInfo['is_medico_legal']){
	#if ($medCertInfo['is_medico_legal']){
	#$pdf->Ln(5);
	$medico_cases = $enc_obj->getEncounterMedicoCases($encounter_nr,$pid);
	#echo "sql = ".$enc_obj->sql;
	$NOI = '';
	if ($medico_cases){
		while($result=$medico_cases->FetchRow()) {
            if ($result['medico_cases']=='Others')
                $result['medico_cases'] = $result['description'];
			$NOI .= $result['medico_cases'].", ";
		}
	}
if ($NOI){	
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
	
	if (!$medCertInfo['is_medico_legal'])
		$y = $y + 3;
	#$pdf->SetXY($x+40,y+108);
	$pdf->SetXY($x+30,$y);
	$pdf->cell(26, 6,"NOI :","",1,"L");
	#$pdf->SetXY($x+55,y+110);
	$pdf->SetXY($x+45,$y+2);
	#$pdf->cell(26, 6,"NOI :     ".strtoupper(substr(trim($NOI),0,-1)),"",1,"L");
	$pdf->MultiCell(100, 4,strtoupper(substr(trim($NOI),0,-1)), '', 'L','0');
	$pdf->SetX($x+30);
	$pdf->cell(26, 6,"POI :     ".strtoupper($POI),"",1,"L");
	$pdf->SetX($x+30);
	$pdf->cell(26, 6,"TOI :     ".$TOI_val,"",1,"L");
	$pdf->SetX($x+30);
	$pdf->cell(26, 6,"DOI :     ".$DOI_val,"",1,"L");
}

if ($medCertInfo['is_medico_legal']){	
	$pdf->Ln(7);
	#}
	$pdf->SetFont($fontStyle,"B",$fontSizeText);
	$pdf->SetX($x+10);
	$pdf->MultiCell(175,6,strtoupper($medCertInfo['diagnosis_verbatim']),0,'J',0);
	
	/*
	//Operation
	$pdf->Ln(2);
	if ($medCertInfo['procedure_verbatim']){
		$procedureVerbatim="OPERATION: \n".$medCertInfo['procedure_verbatim'];
	}
	$pdf->MultiCell(175,6,$procedureVerbatim,0,'J',0);
	*/
	$pdf->SetFont($fontStyle,"",$fontSizeText);
	$pdf->Ln(10);
	$pdf->cell(26, 6,"PROBABLE HEALING TIME WILL BE  _________________  DAYS BARRING COMPLICATIONS","",0,"L");
}else{
	$pdf->Ln(15);
	$pdf->cell(26, 6,"ADVISED TO REST FOR  _________________  DAYS","",0,"L");
}	

$pdf->Ln(10);

#if ($_GET['type']==1)
if ($medCertInfo['is_medico_legal'])
	$cert_type = 'ML';
else
	$cert_type = 'NML';	

#$pdf->Cell(15, 6, "[ ".$_GET['type']." ]","",0,"L");
$pdf->Cell(15, 6, "[ ".$cert_type." ]   ID No. ".$pid,"",0,"L");

$pdf->Ln(18);
if ($medCertInfo['is_doc_sig']){

   if (is_numeric($medCertInfo['dr_nr'])){
	$docInfo = $pers_obj->getPersonellInfo($medCertInfo['dr_nr']);
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
	$name_doctor = "".$docInfo['name_first']." ".$docInfo['name_2']." ".$dr_middleInitial." ".$docInfo['name_last'];
   }else{
   		$name_doctor = "".$medCertInfo['dr_nr'];
   }
	//Doctor Name
	$pdf->setFont($fontStyle,"B",$fontSizeText);
	$pdf->Cell(116,6,'',"",0,"");
	$pdf->Cell(75,6,strtoupper($name_doctor).", MD","",0,"L");

	$pdf->Ln(5);
	$pdf->setFont($fontStyle,"",$fontSizeText);
	$pdf->Cell(116,6,'',"",0,"");
	$pdf->Cell(75,6,"Attending Physician","",0,"L");
	#$pdf->Cell(155,6,"Attending Physician","",0,"R");

	$pdf->Ln(6);
	$pdf->Cell(116,6,'',"",0,"");
	$pdf->Cell(75,6,"Lic No. _______________","",0,"L");
	#$pdf->Cell(168,6,"Lic No. _______________","",0,"R");
}else{
	$pdf->Ln(5);
	//Medical Staff Officer Name
	$pdf->setFont($fontStyle,"B",$fontSizeText);
	$pdf->Cell(116,6,'',"",0,"");
	
	#added by VAN 06-14-08
	$officer = $pers_obj->get_Officer_Dept('Department Head', 'Administrative Officer', '151');
	$officer_info = $pers_obj->get_Person_name($officer['personell_nr']);
	
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
	$name_officer = trim($officer_info['title'])." ".trim($officer_info['name_first'])." ".$officer_middleInitial." ".trim($officer_info['name_last']);
	
	#$pdf->Cell(75,6,"Mrs. Lani P. Paler","",0,"L");
	$pdf->Cell(75,6,$name_officer,"",0,"L");

	$pdf->Ln(5);
	$pdf->setFont($fontStyle,"",$fontSizeText);
	$pdf->Cell(116,6,'',"",0,"");
	#$pdf->Cell(75,6,"Administrative Officer V","",1,"L");
	$pdf->Cell(75,6,$officer['job_position'],"",1,"L");
	$pdf->Cell(116,6,'',"",0,"");
	#$pdf->Cell(75,6,"Medical Records Dept. Head","",0,"L");
	$pdf->Cell(75,6,$officer['name_formal']." ".$officer['job_function_title'],"",0,"L");
}
$pdf->setFont($fontStyle,"B",$fontSizeText);
$pdf->Ln(5);
$pdf->Cell(30,6,"NOT VALID","",0,"R");
$pdf->Ln();
$pdf->Cell(50,6,"WITHOUT DMC SEAL","",0,"R");

$pdf->Ln(30);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(5, 3 , '', "", 0,'');

if ($medCertInfo["modify_id"])
	$encoder = $medCertInfo["modify_id"];
else
    $encoder = $medCertInfo["create_id"];		
#$pdf->Cell(0, 3 , 'Prepared by : '.strtoupper($HTTP_SESSION_VARS["sess_user_name"]), "", 0,'');
#$pdf->Cell(0, 3 , 'Encoded by : '.$encoder, "", 0,'');
$pdf->encoder = $encoder;
//print pdf
$pdf->Output();

?>