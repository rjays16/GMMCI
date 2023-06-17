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

$cert_nr = $_GET['cert_nr'];
$referral_nr = $_GET['referral_nr'];
#echo "sql = ".$enc_obj->sql;
$obj_medCert = new MedCertificate($encounter_nr);
$medCertInfo = $obj_medCert->getMedCertRecord($encounter_nr,$referral_nr,$cert_nr);

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

$pdf->SetTopMargin(2);

	if ($row = $objInfo->getAllHospitalInfo()) {
		//$row['hosp_agency'] = strtoupper($row['hosp_agency']);
		$row['hosp_name']   = strtoupper($row['hosp_name']);
	}
	else {
		// $row['hosp_country'] = "Republic of the Philippines";
		// $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
		$row['hosp_name']    = "GONZALES MARANAN MEDICAL CENTER INC.";
		$row['hosp_addr1']   = "Quezon Ave., Digos City, Davao Del Sur";
	}

	// $pdf->SetFont("Courier","","11");
	// $pdf->Cell(0,4,$row['hosp_country'],$border2,1,'C');
	#$pdf->SetFont("Courier","","11");
	//$pdf->Cell(0,4,$row['hosp_agency'],$border2,1,'C');

	$pdf->Ln(8);
	$pdf->SetFont("Courier","B","16");
	$pdf->Cell(0,4,$row['hosp_name'],$border2,1,'C');

	$pdf->SetFont("Courier","","11");
	$pdf->Cell(0,4,$row['hosp_addr1'],$border2,1,'C');

	#added by genz
	$pdf->SetFont("Courier","","11");
	$pdf->Cell(0,4,'Tel No. 082-553-2474',$border2,1,'C');

	$pdf->SetFont("Courier","","11");
	$pdf->Cell(0,4,'"PHIC Accredited Health Care Provider"',$border2,1,'C');
	#end by genz


//File No.. Line -2
$pdf->Ln(12); #edited by art 01/18/2014
$pdf->SetFont($fontStyle,"", $fontSizeLabel+3);
$pdf->Cell(142, 3 , '', "",0,'');
$pdf->Cell(25, 3 , 'HRN:', "",0,'');
$pdf->SetFont($fontStyle,"B", $fontSizeLabel+4);
$pdf->Cell(45, 3 , $pid, "",0,'');

$pdf->Ln(4);
$pdf->SetFont($fontStyle,"", $fontSizeLabel+3);
$pdf->Cell(142, 3 , '', "",0,'');
$pdf->Cell(25, 3 , 'CASE NO.:', "",0,'');
$pdf->SetFont($fontStyle,"B", $fontSizeLabel+4);
$pdf->Cell(45, 3 , $encounter_nr, "",0,'');

$toDate= "".@formatDate2Local($discharge_dt,$date_format);

//Date .. Line - 3
$pdf->Ln(4);
$pdf->SetFont($fontStyle,"", $fontSizeLabel+3);
$pdf->Cell(142, 3 , '', "",0,'');
$pdf->Cell(25, 3 , 'DATE:', "",0,'');
$pdf->SetFont($fontStyle,"B", $fontSizeLabel+4);

#$pdf->Cell(45, 3 , ''.@formatDate2Local(date('Y-m-d'),$date_format), "",0,'');
/*
if($medCertInfo["create_dt"]!=NULL){
	$date_created = date("m/d/Y",strtotime($medCertInfo["create_dt"]));
}elseif ($medCertInfo["modify_dt"]!=NULL){
	$date_created = date("m/d/Y",strtotime($medCertInfo["modify_dt"]));
}else
	$date_created = @formatDate2Local(date('Y-m-d'),$date_format);
*/
$date_created = date("m/d/Y",strtotime($medCertInfo["scheduled_date"]));

$pdf->Cell(45, 3 , ''.$date_created, "",0,'');

#$pdf->Cell(45, 3 , ''.$toDate, "",0,'');

//Document Title - Medical Certificate  Line 4
$pdf->Ln(20);
$pdf->SetFont($fontStyle,"B", $fontSizeHeader+2);
$pdf->Cell(0,16 , 'M E D I C A L    C E R T I F I C A T E', $border2,1,'C');
#$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,25,30); commented by art 01/18/2014

//added by art 01/18/2014
//$pdf->Image('../image/logo_doh.jpg',25,10,20,20);
//$pdf->Image('../image/dmc_logo.jpg',170,10,20,20);
$pdf->Image('../image/dmc_logo.jpg',10,10,30,30);
//end art

//Salutation
$pdf->Ln(6);
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->SetX(20);
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
    #edited by VAN 12-27-2012
    #with Ma'am Suzanne's approval
    if (($er_opd_datetime!='0000-00-00') && ($er_opd_datetime!=""))
        $consult_date =  $er_opd_datetime;
    else    
        $consult_date =  $admission_dt;
	#$fromDate= "".@formatDate2Local($admission_dt,$date_format);
    $fromDate= "".@formatDate2Local($consult_date,$date_format);
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

$pdf->SetStyle("p","Courier","",$fontSizeText,"0,0,0",10);
$pdf->SetStyle("b","Courier","B",$fontSizeText,"0,0,0");

$patient_name = "<b>".stripslashes(strtoupper($name_last)).", ".stripslashes(strtoupper($name_first)).' '.stripslashes(strtoupper($name_middle))."</b>";

$txt = '<p>This is to certify that '.$patient_name.' , '.$age.' old, '.stripslashes(strtoupper($sex)).' '.
		', '.mb_strtoupper($civil_status).'  and a resident of '.trim(stripslashes(strtoupper($address))).
		' was examined, treated '.$confine.' in this institution on/from '."<b>".$dateconfine."</b>".
		' with the following findings/diagnosis.</p>';

$pdf->SetLineWidth(0.1);
$pdf->SetFillColor(255, 255, 204);
$pdf->SetDrawColor(102, 0, 102);

$pdf->SetX(20);
$pdf->WriteTag(185,6,$txt, 0, "J", 0);

//$pdf->MultiCell(180, 6,$txt,0,'J',0);

//$pdf->Ln();
# Commented by genz
// if ($encounter_type==1){
// 	$pdf->Cell(0,3,'(ER Consultation)',"",1,'L');
// }elseif ($encounter_type==2){
// 	$pdf->Cell(0,3,'(OPD Consultation)',"",1,'L');
// }else{
// 	$patient_enc = $enc_obj->getPatientEncounterInsurance($encounter_nr);
// 	if ($patient_enc['hcare_id']==18)
// 		$phic_label = '(chart/phic)';
// 	else
// 		$phic_label = '(chart/non-phic)';

// 	$pdf->Cell(0,3,$phic_label,"",1,'L');
// }

#added by VAN 06-12-08
#if (($encounter_type==3)||($encounter_type==4)){
if ($medCertInfo['is_medico_legal']==0){

//Diagnosis
//$pdf->Ln(5);
//$pdf->Ln();
/*
$pdf->Cell(75,6, 'burn', $border2,1,'L');
$pdf->MultiCell(75,6,'',0,'J',0);
$pdf->MultiCell(100,6,"Diagnosis : \n".$medCertInfo['diagnosis_verbatim'],0,'J',0);
*/
//$pdf->Cell(10,3,'',"",0,'L');
#$pdf->MultiCell(175,6,"Diagnosis : \n".strtoupper($medCertInfo['diagnosis_verbatim']),0,'J',0);
#commented by genz
//$pdf->MultiCell(175,6,"Diagnosis : ",0,'J',0);
#added by genz
//$pdf->MultiCell(175,6,"",0,'J',0);
#end by genz
$pdf->Ln(5);
//$pdf->SetFont($fontStyle,"",$fontSizeText);
#$pdf->Cell(30,3,'',"",0,'L');
#$pdf->MultiCell(175,6,strtoupper($medCertInfo['diagnosis_verbatim']),0,'J',0);


if ($result_diagnosis = $objDRG->getDiagnosisCodes($encounter_nr,$encInfo['encounter_type'])){
		
		$result['diagnosis_principal']='';
		$result['diagnosis_others']='';
		$rowsDiagnosis = $result_diagnosis->RecordCount();

		while($temp=$result_diagnosis->FetchRow()){
				if ($temp['type'])
						$result['diagnosis_principal'].= $temp['code']." : ".$temp['diagnosis'].' ';
				else
						$result['diagnosis_others'].= $temp['code']." : ".$temp['diagnosis'].' ';
	



$pdf->SetFont("Courier","B","10");

$pdf->SetX($x+27);
$pdf->MultiCell(175,6,strtoupper($temp['code']." : ".$temp['diagnosis']),0,'J',0);


	}
}

$pdf->Ln();
#$pdf->MultiCell(175,6,'PREGNANCY UTERINE TERM, CEEPHALIX, DELIVERED LIVE BIRTH BABY BOY, '.
#				'OLIGOHYDRAMNIOS SEVERS G1P1 (1-0-0-1).',0,'J',0);

//Operation


if ($result_therapy = $objDRG->getProcedureCodes($encounter_nr,$encInfo['encounter_type'])){
		#echo "<br>sql 2 = ".$objDRG->sql;
		$result['therapy_principal']='';
		$result['therapy_others']='';
		$rowsTherapy = $result_therapy->RecordCount();
		#echo "   code  :   therapy <br> \n";
		while($temp=$result_therapy->FetchRow()){
				#echo $temp['code']." : ".$temp['therapy']." <br> \n";
				#commented by VAN 06-28-08
				#$listDoctors[$temp['responsible_clinician']]=$temp['responsible_clinician_name'];
				if ($temp['type']){
						$result['therapy_principal'].= $temp['code']." : ".$temp['therapy']." ";
						//$result['therapy_principal'].= $temp['responsible_dept_name']." - ".$temp['responsible_clinician_name'];
				}else{
						$result['therapy_others'].= $temp['code']." : ".$temp['therapy']." ";
						//$result['therapy_others'].= $temp['responsible_dept_name']." - ".$temp['responsible_clinician_name'];
				}
		
		
		$pdf->SetFont("Courier","B","10");
		$pdf->SetX($x+15);
		$pdf->MultiCell(175,6,strtoupper($temp['code']." : ".$temp['therapy'])." ",0,'J',0);


		}
}


$pdf->SetFont($fontStyle,"",$fontSizeText);
// if (trim($medCertInfo['procedure_verbatim'])!=""){
// 	/*
// 	$pdf->Ln(10);
// 	#$procedureVerbatim="OPERATION: \n".$medCertInfo['procedure_verbatim'];
// 	$procedureVerbatim="OPERATION:";
// 	$pdf->Cell(10,3,'',"",0,'L');
// 	$pdf->MultiCell(175,6,$procedureVerbatim,0,'J',0);
// 	$pdf->Ln(5);
// 	$pdf->SetFont($fontStyle,"B",$fontSizeText);
// 	$pdf->Cell(20,3,'',"",0,'L');
// 	$pdf->MultiCell(175,6,strtoupper($medCertInfo['procedure_verbatim']),0,'J',0);
// 	*/
// 	$withproc = 0;
// 	//Operation
// 	//$pdf->Ln(5);
// 	//$pdf->Ln();
// 	#if ($medCertInfo['procedure_verbatim']){
// 	$proc = trim($medCertInfo['procedure_verbatim']);
// 	if (!(empty($proc))){
// 		#commented by genz
// 		//$procedureVerbatim = "OPERATION:";
// 		#added by genz
// 		$procedureVerbatim = "";
// 		#ended by genz
// 		$procedureVerbatim_desc=$medCertInfo['procedure_verbatim'];
// 		$withproc = 1;
// 		$pdf->SetX($x+20);
// 		$pdf->SetFont($fontStyle,"",$fontSizeText);
// 		$pdf->MultiCell(175,6,$procedureVerbatim,0,'J',0);
// 		$pdf->Ln();
// 		$pdf->SetX($x+20);
// 		$pdf->SetFont("Courier","B","14");
// 		#$pdf->SetFont($fontStyle,"B",$fontSizeText);
// 		$pdf->MultiCell(175,6,strtoupper($procedureVerbatim_desc),0,'J',0);
// 	}

// }


}

#$pdf->MultiCell(180,6,'OPERATION: '.
#					'LOW SEGMENT TRANSVERSE CAESAREAN SECTION I. ',0,'J',0);
#$pdf->Cell(20, 6,"(01-09-2007)","",0,"J");

//Advised
#$pdf->Ln(15);
$pdf->SetFont($fontStyle,"",$fontSizeText);
if ($medCertInfo['is_medico_legal'])
	$pdf->Ln();

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

	if ($DOI!='0000-00-00' && $DOI!='1970-01-01')
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
	$pdf->Ln(10);
	#}

	$pdf->SetFont($fontStyle,"",$fontSizeText);
	$pdf->SetX($x+15);
	$pdf->MultiCell(180,4,strtoupper($medCertInfo['diagnosis_verbatim']),0,'J',0);

	$withproc = 0;
	//Operation
	$pdf->Ln();

	$proc = trim($medCertInfo['procedure_verbatim']);
	if (!(empty($proc))){

		$procedureVerbatim = "OPERATION:";
		$procedureVerbatim_desc=$medCertInfo['procedure_verbatim'];
		$withproc = 1;
		$pdf->SetX($x+20);
		$pdf->SetFont($fontStyle,"",$fontSizeText);
		$pdf->MultiCell(175,4,$procedureVerbatim,0,'J',0);
		$pdf->Ln();
		$pdf->SetX($x+20);
		$pdf->SetFont("Courier","B","14");
		#$pdf->SetFont($fontStyle,"B",$fontSizeText);
		$pdf->MultiCell(175,4,strtoupper($procedureVerbatim_desc),0,'J',0);
	}


	$pdf->SetFont($fontStyle,"B",$fontSizeText);
	$pdf->Ln(5);
	#commented by genz
	//$pdf->cell(26, 4,"PROBABLE HEALING TIME WILL BE  _________________  DAYS BARRING COMPLICATIONS","",0,"L");
	$pdf->SetX(20);
	$pdf->MultiCell(26, 4,"REMARKS:","",1,"L");
	$pdf->SetFont($fontStyle,"",$fontSizeText);
	$pdf->Ln();
	$pdf->SetX(20);
	$pdf->MultiCell(200, 4,$medCertInfo['remarks_verbatim'],"",1,"L");
}else{
	$pdf->SetFont($fontStyle,"B",$fontSizeText);
	$pdf->Ln(5);
	#commented by genz
	//$pdf->cell(26, 4,"ADVISED TO REST FOR  _________________  DAYS","",0,"L");
	$pdf->SetX(20);
	$pdf->MultiCell(26, 4,"REMARKS:","",1,"L");
	$pdf->Ln();
	$pdf->SetFont($fontStyle,"",$fontSizeText);
	$pdf->SetX(20);
	$pdf->MultiCell(200, 4,$medCertInfo['remarks_verbatim'],"",1,"L");
}

$pdf->Ln(1);

#if ($_GET['type']==1)
// if ($medCertInfo['is_medico_legal'])
// 	$cert_type = 'ML';
// else
// 	$cert_type = 'NML';

#$pdf->Cell(15, 6, "[ ".$_GET['type']." ]","",0,"L");
//$pdf->Cell(15, 6, "[ ".$cert_type." ]   ID No. ".$pid,"",0,"L");

if ($withproc)
	$pdf->Ln(10);
else
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
	$pdf->setFont($fontStyle,"BU",$fontSizeText);
	$pdf->Cell(116,6,'',"",0,"");
	$pdf->Cell(75,6,strtoupper($name_doctor).", MD","",0,"L");

	$pdf->Ln(5);
	$pdf->setFont($fontStyle,"",$fontSizeText);
	$pdf->Cell(116,6,'',"",0,"");
	$pdf->Cell(75,6,"Attending Physician","",0,"L");
	#$pdf->Cell(155,6,"Attending Physician","",0,"R");

	$pdf->Ln(6);
	$pdf->Cell(116,6,'',"",0,"");
	$pdf->Cell(75,6,"Lic No.: ".$docInfo['license_nr'],"",0,"L");
	#$pdf->Cell(168,6,"Lic No. _______________","",0,"R");
}else{
	$pdf->Ln(5);
	//Medical Staff Officer Name
	$pdf->setFont($fontStyle,"B",$fontSizeText);
	$pdf->Cell(116,6,'',"",0,"");

	#added by VAN 06-14-08
	/*$officer = $pers_obj->get_Officer_Dept('Department Head', 'Administrative Officer', '151');
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
	*/
	$sig_info = $pers_obj->get_Signatory('medcert');
	$name_officer = mb_strtoupper($sig_info['name']);
	$officer_position = $sig_info['signatory_position'];
	$officer_title = $sig_info['signatory_title'];

	$pdf->Cell(75,6,$name_officer,"",0,"L");
	$pdf->Ln(5);
	$pdf->setFont($fontStyle,"",$fontSizeText);
	$pdf->Cell(116,6,'',"",0,"");
	$pdf->Cell(75,6,$officer_position,"",1,"L");
	$pdf->Cell(116,6,'',"",0,"");
	$pdf->Cell(75,6,$officer_title,"",0,"L");
}
// $pdf->setFont($fontStyle,"B",$fontSizeText);
// $pdf->Ln(5);
// $pdf->Cell(30,6,"NOT VALID","",0,"R");
// $pdf->Ln();

// $pdf->Cell(50,6,"WITHOUT ".$row['hosp_id']." SEAL","",0,"R");

/*if ($withproc)
	$pdf->Ln(20);
else
	$pdf->Ln(30);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(5, 3 , '', "", 0,'');*/

if ($medCertInfo["modify_id"])
	$encoder = $medCertInfo["modify_id"];
else
		$encoder = $medCertInfo["create_id"];
#$pdf->Cell(0, 3 , 'Prepared by : '.strtoupper($HTTP_SESSION_VARS["sess_user_name"]), "", 0,'');
#$pdf->Cell(0, 3 , 'Encoded by : '.$encoder, "", 0,'');
$pdf->encoder = $encoder;
//print pdf


//added by art 01/10/2014
$pdf->code ="SPMC-F-HIMD-14";
//end art

$pdf->Output();

?>