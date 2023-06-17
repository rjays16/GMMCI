<?php

include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

include_once($root_path.'include/care_api_classes/class_cert_med.php');


//$_GET['encounter_nr'] = 2007500006;
/*
if($_GET['id']){
	if(!($encInfo = $enc_obj->getEncounterInfo($_GET['id']))){
		echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
		exit();
	}
	extract($encInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
	exit();
}
*/
if (isset($_GET['pid']) && $_GET['pid']){
	$pid = $_GET['pid'];
}

if (isset($_GET['batch_nr']) && $_GET['batch_nr']){
	$batch_nr = $_GET['batch_nr'];
}


include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;

if ($pid){
	if (!($basicInfo=$person_obj->getAllInfoArray($pid))){
		echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
		exit();
	}
#	echo "basicInfo : <br> \n"; print_r($basicInfo); echo "<br>\n";
	extract($basicInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid PID!</em>';
	exit();
}


			# burn added : March 26, 2007
			if($date_birth){
				$segBdate = @formatDate2Local($date_birth,$date_format);
				if (!($age = $person_obj->getAge($segBdate))){
					$age = '';
					$segBdate = 'Not Available';
					$segBdateAge = $segBdate;
				}else{
#					$smarty->assign('sAge','<span class="vi_data">'.$age.' </span> year(s) old');
					$age=$age.' year(s) old';
					$segBdateAge = $segBdate.'   / '.$age;
				}
			}
	if ($sex=='f'){
		$gender = "female";
	}else if($sex=='m'){
		$gender = "male";	
	}
	$sAddress = trim($street_name);
	if (!empty($sAddress) && !empty($brgy_name))
		$sAddress= trim($sAddress.", ".$brgy_name);
	else
		$sAddress = trim($sAddress." ".$brgy_name);
	if (!empty($sAddress) && !empty($mun_name))
		$sAddress= trim($sAddress.", ".$mun_name);
	else
		$sAddress = trim($sAddress." ".$mun_name);
	if (!empty($zipcode))
		$sAddress= trim($sAddress." ".$zipcode);
	if (!empty($sAddress) && !empty($prov_name))
		$sAddress= trim($sAddress.", ".$prov_name);
	else
		$sAddress = trim($sAddress." ".$prov_name);

/*
echo "seg-radio-report-pdf.php : person_obj->sql = '".$person_obj->sql."' <br> \n";
#echo "seg-radio-report-pdf.php : basicInfo : "; print_r($basicInfo); echo " <br> \n";

echo " Patient : '".$name_last.', '.$name_first.' '.$name_middle."' <br> \n";
echo " PID : '".$pid."' <br> \n";
echo " Address : '".$sAddress."' <br> \n";
echo " Sex : '".$sex."' <br> \n";
echo " Birthdate/Age : '".$segBdate."' / '".$age."'<br> \n";
*/
# Create radiology object
require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj = new SegRadio;

include_once($root_path.'include/care_api_classes/class_personell.php');
$personell_obj=new Personell;


if ($batch_nr){
	if (!($radioResultInfo = $radio_obj->getAllRadioInfoByBatch($batch_nr))){
		echo '<em class="warn"> Cannot continue to display the page! <br> \n NO Result(s) found.</em>';
		exit();
	}
	#echo "radioResultInfo : <br> \n"; print_r($radioResultInfo); echo "<br>\n";
	extract($radioResultInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Batch Number!</em>';
	exit();
}

if ($encounter_type==1){
	$area='ER';
}elseif ($encounter_type==2){
	$area='OPD';
}elseif ($encounter_type==3){
	$area='ER - Inpatient '.$ward_id." [".$ward_name."]";
	$area="Inpatient [".$ward_name."]";
}elseif ($encounter_type==4){
	$area='OPD - Inpatient '.$ward_id." [".$ward_name."]";
	$area="Inpatient [".$ward_name."]";
}

$seg_request_date = 'No Date Requested indicated';
if($request_date && ($request_date!='0000-00-00')){
	$seg_request_date = @formatDate2Local($request_date,$date_format);
#	$request_date = @formatDate2Local($request_date,$date_format);
}
$seg_service_date = 'No Date Service indicated';
if($service_date && ($service_date!='0000-00-00')){
	$seg_service_date = @formatDate2Local($service_date,$date_format);
#	$service_date = @formatDate2Local($service_date,$date_format);
}
$findings_array = unserialize($findings);
$findings_final = $findings_array[count($findings_array)-1];
$radio_impression_array = unserialize($radio_impression);
$radio_impression_final = $radio_impression_array[count($radio_impression_array)-1];

$findings_date_array = unserialize($findings_date);
$findings_date_final = $findings_date_array[count($findings_date_array)-1];
$findings_date_final = @formatDate2Local($findings_date_final,$date_format);
$doctor_in_charge_array = unserialize($doctor_in_charge);
$doctor_in_charge_final = $doctor_in_charge_array[count($doctor_in_charge_array)-1];

if ($doctor_in_charge_final){

	if ($reportingDoctorInfo = $personell_obj->getPersonellInfo($doctor_in_charge_final)){
		$doctor_in_charge_name = trim($reportingDoctorInfo['name_first']);
		if (!empty($reportingDoctorInfo['name_middle'])){
			$doctor_in_charge_name .= ' '.substr(trim($reportingDoctorInfo['name_middle']),0,1).'.';	
		}
		if (!empty($reportingDoctorInfo['name_last'])){
			$doctor_in_charge_name .= ' '.trim($reportingDoctorInfo['name_last']);	
		}
		$doctor_in_charge_name = trim($doctor_in_charge_name.', MD');
	}
}

if (!empty($grant_no)){
	$or_no_final = "CHARITY";
	$amount_paid = "0.00";
}elseif (!empty($or_no)){
	if (floatval($amount_or) > floatval($price_net)){
		$or_no_final = $or_no.' (Subsidized)';
		$amount_paid = $price_net;
	}else{
		$or_no_final = $or_no;
		$amount_paid = $amount_or;
	}
}else{
	$or_no_final = 'Subsidized';
	$amount_paid = $price_net;
}
/*
#echo "seg-radio-report-pdf.php : personell_obj->sql = '".$personell_obj->sql."' <br> \n";
echo "seg-radio-report-pdf.php : radio_obj->sql = '".$radio_obj->sql."' <br> \n";
#echo "seg-radio-report-pdf.php : radioResultInfo : <br> \n"; print_r($radioResultInfo); echo " <br> \n";

echo " Exam taken : '".$service_code."' <br> \n";
echo " Requesting Doctor : '".$request_doctor_name."' <br> \n";
echo " Dept : '".$request_dept_name."' <br> \n";
echo " O.R. No. : '".$or_no."' <br> \n";
echo " Amount Paid (amount_or) : '".$amount_or."' <br> \n";
echo " Amount Paid (amount_charity) : '".$amount_charity."' <br> \n";
echo " Amount Paid (price_net) : '".$price_net."' <br> \n";

echo " Area : '".$encounter_type."' '".$area."' <br> \n";

echo " date_format : '".$date_format."' <br> \n";


echo " Date Requested : '".$seg_request_date."' <br> \n";
echo " Date Service : '".$seg_service_date."' <br> \n";

echo " Batch Number : '".$batch_nr."' <br> \n";
echo " Service Name : '".$service_name."' <br> \n";

echo " findings_array : <br> \n"; print_r($findings_array); echo"<br> \n";
echo " radio_impression_array : <br> \n"; print_r($radio_impression_array); echo"<br> \n";
echo " findings_date_array : <br> \n"; print_r($findings_date_array); echo"<br> \n";
echo " doctor_in_charge_array : <br> \n"; print_r($doctor_in_charge_array); echo"<br> \n";

echo " Final Findings : '".$findings_final."' <br> \n";
echo " Final Radio Impression : '".$radio_impression_final."' <br> \n";
echo " Final Findings Date : '".$findings_date_final."' <br> \n";
echo " Final Reporting Doctor : '".$doctor_in_charge_name."' ('".$doctor_in_charge_final."') <br> \n";

echo " Findings Encoder : '".$findings_encoder."' <br> \n";
*/
$obj_medCert = new MedCertificate($encounter_nr);
$medCertInfo = $obj_medCert->getMedCertRecord($encounter_nr);

//set border 
$border_0="0";
$border_1="1";
$spacing =2;
// font setup
$fontSizeLabel = 8+3;
$fontSizeInput = 11;
$fontSizeText = 12;
$fontSizeHeader = 14;
//fontstyle setup
$fontStyle = "Arial";
$fontStyle2 = "Times";
$my_add_left_margin=10; # additional left margin
 

//instantiate fpdf class
$pdf  = new FPDF();
$pdf->AddPage("P");

	//Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, int fill [, mixed link]]]]]]])
	
// Hospital Logo
$pdf->Image($root_path.'gui/img/logos/dmc_logo.jpg',20,10,25,30);

//Header - Republic of the Philippines / Department of Health
$pdf->SetFont($fontStyle, "I", $fonSizeInput);
$pdf->Cell(0,4,'Republic of the Philippines', $border_0,1,'C');
$pdf->Ln(1);
$pdf->Cell(0,4,'DEPARTMENT OF HEALTH', $border_0,1,'C');

//Hospital name- Gonzales Maranan Medical Center Incorporated
$pdf->Ln(1);
$pdf->setFont($fontStyle,"B", $fontSizeHeader-2);
$pdf->Cell(0,4,'Gonzales Maranan Medical Center Incorporated',$border_0, 1, 'C');

//Hospital Address
$pdf->Ln(1);
$pdf->setFont($fontStyle,"", $fontSizeInput);
$pdf->Cell(0,4,'Digos City',$border_0, 1, 'C');

//Department Name
$pdf->Ln(2);
$pdf->setFont($fontStyle,"B", $fontSizeInput+1);
$pdf->Cell(0,4,'Department of Radiological & Imaging Sciences',$border_0, 1, 'C');

//RID 
$pdf->Ln(15);
#$pdf->Cell(10, 3 ,'', "",0,''); # left margin
$pdf->SetFont($fontStyle,"B", $fontSizeLabel);
$pdf->Cell(12, 3 ,'RID : ', "",0,'');
$pdf->SetFont($fontStyle,"B", $fontSizeLabel+2);
$pdf->Cell(0, 3 ,$rid, "",0,'');

//Patient name and PID
$pdf->Ln(7);
#$pdf->Cell(10, 3 ,'', "",0,''); # left margin
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(17, 3 ,'Patient : ', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(123, 3 ,strtoupper($name_last.', '.$name_first.' '.$name_middle), "",0,'');

$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(12, 3 , 'PID :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(0, 3 ,$pid, "",0,'');

//Address and Sex
$pdf->Ln(5);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(18, 3 ,'Address : ', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(122, 3 ,$sAddress, "",0,'');

$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(12, 3 , 'Sex :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(0, 3 ,strtoupper($sex), "",0,'');

//Birthdate and Exam taken
$pdf->Ln(5);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(30, 3 ,'Birthdate / Age : ', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(110, 3 ,$segBdateAge, "",0,'');

$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(25, 3 , 'Exam taken :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(0, 3 ,strtoupper($service_code), "",0,'');

//Requesting Doctor and Dept
$pdf->Ln(5);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(37, 3 ,'Requesting Doctor : ', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(103, 3 ,$request_doctor_name, "",0,'');

$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(12, 3 , 'Dept :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(0, 3 ,strtoupper($request_dept_name), "",0,'');

//O.R. No, Amount Paid and Area
$pdf->Ln(5);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(20, 3 ,'O.R. No. : ', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(50, 3 ,$or_no_final, "",0,'');

$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(27, 3 ,'Amount Paid : ', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(43, 3 ,$amount_paid, "",0,'');

$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(12, 3 , 'Area :', "",0,'');
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(0, 3 ,strtoupper($area), "",0,'');


//Document Title - Roentgenological Report
if ($service_dept_nr=='165'){
	#Ultrasound
	$report="Ultrasound Report";
	$note_msg="";
}else if ($service_dept_nr=='166'){
	#Special Procedures
	$report="Special Procedures Report";
	$note_msg="";
}else if ($service_dept_nr=='167'){
	#Computed Tomography
	$report="CT Scan Report";
	$note_msg="";
}else{
	#General Radiography
	$report="Roentgenological Report";
	$note_msg="NOTE: This result is based on radiographic findings & must be correlated clinically.";
}


$pdf->Ln(12);
$pdf->SetFont($fontStyle,"B", $fontSizeHeader);
$pdf->Cell(0, 5 , strtoupper($report), $border_0,1,'C');
$pdf->Ln(2);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(0,3 , $note_msg, $border_0,1,'C');


//DATE, Batch Number and 'INITIAL READING'
$pdf->Ln(8);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(50, 3 , $seg_request_date, "", 0,'');   # date requested
#$pdf->Cell(50, 3 , $seg_service_date, "", 0,'');   # date of service
#$pdf->Cell(50, 3 , $findings_date_final, "", 0,'');   # date the final findings reported

$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(90, 3 ,"Batch # ".$batch_nr, "",0,'');

$pdf->SetFont($fontStyle,"UB", $fontSizeLabel);
$pdf->Cell(0, 3 ,'-INITIAL READING', "",0,'R');

//Service name
$pdf->Ln(8);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(0, 3 , strtoupper($service_name), "", 0,'');   # date requested

//Findings
$pdf->Ln(16);
$findings_temp = $findings_final.$findings_final.$findings_final.$findings_final.$findings_final.
						$findings_final.$findings_final.$findings_final.$findings_final.$findings_final.
						$findings_final.$findings_final.$findings_final.$findings_final.$findings_final;
$findings_temp = "Matter is the stuff which things are made of and consists of chemical substances. These are made of atoms, which are made of protons, neutrons and electrons. In this way, matter is contrasted with 'energy' inversely 'energy' is an expression of matter.
In physics, there is no broad consensus as to an exact definition of matter. Physicists generally do not use the word when precision is needed, preferring instead to speak of the more clearly defined concepts of mass, energy and particles.
A possible definition of matter which at least some physicists use [1] is that it is everything that is constituted of elementary fermions. These are the leptons, including the electron, and the quarks, including the up and down quarks of which protons and neutrons are made. Since protons, neutrons and electrons combine to form atoms, atoms, molecules and the bulk substances which they make up are all matter. Matter also includes the various baryons and mesons. Things which are not matter include light (photons) and the other gauge bosons.";
$pdf->MultiCell(175,6,"\t\t\t".$findings_final,0,'J',0);
#$pdf->MultiCell(175,6,"\t\t\t".$findings_temp,0,'J',0);

//radio_impression
$pdf->Ln(16);
$pdf->MultiCell(175,6,$radio_impression_final,0,'J',0);

//Reporting doctor
$pdf->Ln(20);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(0, 3 , strtoupper($doctor_in_charge_name), "", 0,'');
$pdf->Ln(4);
$pdf->Cell(0, 3 , strtoupper('Medical Officer'), "", 0,'');

//Encoder
$pdf->Ln(20);
$pdf->SetFont($fontStyle,"", $fontSizeLabel-4);
$pdf->Cell(150, 3 , '', "", 0,'');
$pdf->Cell(0, 3 , strtoupper($findings_encoder), "", 0,'');
$pdf->Ln(3);
$pdf->Cell(150, 3 , '', "", 0,'');
$pdf->Cell(0, 3 , 'Encoder', "", 0,'');


//print pdf
$pdf->Output();












$pdf->Ln(2);

//File No.. Line -2 
$pdf->Ln(2);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(140, 3 , '', "",0,'');
$pdf->Cell(15, 3 , 'CASE NO.:', "",0,'');
$pdf->SetFont($fontStyle,"B", $fontSizeLabel);
$pdf->Cell(45, 3 , $encounter_nr, "",0,'');

//Date .. Line - 3
$pdf->Ln(3);
$pdf->SetFont($fontStyle,"", $fontSizeLabel);
$pdf->Cell(140, 3 , '', "",0,'');
$pdf->Cell(15, 3 , 'DATE:', "",0,'');
$pdf->SetFont($fontStyle,"B", $fontSizeLabel);
$pdf->Cell(45, 3 , ''.@formatDate2Local(date('Y-m-d'),$date_format), "",0,'');
		
//Document Title - Medical Certificate  Line 4
$pdf->Ln(4);
$pdf->SetFont($fontStyle,"B", $fontSizeHeader);
$pdf->Cell(0,16 , 'M E D I C A L  C E R T I F I C A T E', $border_0,1,'C');

//Salutation
$pdf->Ln(6);
$pdf->SetFont($fontStyle,"", $fontSizeText);
$pdf->Cell(0,3, 'TO WHOM IT MAY CONCERN:', $border_0,1,'L');

//Content text
$sex = ($sex == "m")? "MALE":"FEMALE";
$address = "$street_name, $brgy_name, $mun_name $zipcode $prov_name";
if (($encounter_type==1)||($encounter_type==2)){
	$fromDate= "".@formatDate2Local($er_opd_datetime,$date_format);
	$name_doctor = $er_opd_admitting_physician_name;
}else{
	$fromDate= "".@formatDate2Local($admission_dt,$date_format);
	$name_doctor = $attending_physician_name;
}
if ((isset($_GET['doc_name'])) && (!empty($_GET['doc_name']))){
	$name_doctor = $_GET['doc_name'];
}
#$name_doctor = str_replace('dr.', "", $name_doctor);
#$name_doctor = ereg_replace("dr.", "", $name_doctor);
$name_doctor = preg_replace("/(dr.)|(Dr.)/", "", $name_doctor);

$toDate= "".@formatDate2Local($discharge_dt,$date_format);

$pdf->Ln(7);
$pdf->SetFont($fontStyle,"",$fontSizeText);
$pdf->MultiCell(180, 6,'            This is to certify that '.$name_first.' '.$name_middle.
					' '.$name_last.' , '.$age.' y.o., '.$sex.' '.
					', '.mb_strtoupper($civil_status).'  and a resident of '.$address.
					' was examined, treated, confined in this hospital on/from '.$fromDate. 
					' to  '.$toDate.
					' with the following findings/diagnosis.',0,'J',0);	

$pdf->Ln(8);
if ($encounter_type==1){
	$pdf->Cell(0,3,'(ER Consultation)',"",1,'L');
}elseif ($encounter_type==2){
	$pdf->Cell(0,3,'(OPD Consultation)',"",1,'L');
}else{
	$pdf->Cell(0,3,'(chart/phic)',"",1,'L');
}

//Diagnosis
$pdf->Ln(2);
/*
$pdf->Cell(75,6, 'burn', $border_0,1,'L');
$pdf->MultiCell(75,6,'',0,'J',0);
$pdf->MultiCell(100,6,"Diagnosis : \n".$medCertInfo['diagnosis_verbatim'],0,'J',0);
*/

$pdf->MultiCell(175,6,"Diagnosis : \n".$medCertInfo['diagnosis_verbatim'],0,'J',0);

#$pdf->MultiCell(175,6,'PREGNANCY UTERINE TERM, CEEPHALIX, DELIVERED LIVE BIRTH BABY BOY, '.
#				'OLIGOHYDRAMNIOS SEVERS G1P1 (1-0-0-1).',0,'J',0);

//Operation
$pdf->Ln(2);
if ($medCertInfo['procedure_verbatim']){
	$procedureVerbatim="OPERATION: \n".$medCertInfo['procedure_verbatim'];
}
$pdf->MultiCell(175,6,$procedureVerbatim,0,'J',0);

#$pdf->MultiCell(180,6,'OPERATION: '.
#					'LOW SEGMENT TRANSVERSE CAESAREAN SECTION I. ',0,'J',0);
#$pdf->Cell(20, 6,"(01-09-2007)","",0,"J");
					
//Advised		
$pdf->Ln(15);
$pdf->cell(26, 6,"ADVISED TO REST FOR  _________________  DAYS","",0,"L");


$pdf->Ln(15);
$pdf->Cell(15, 6, "[ ".$_GET['type']." ]","",0,"L");

//Doctor Name
$pdf->setFont($fontStyle,"B",$fontSizeText);
$pdf->Cell(100,6,'',"",0,"");
$pdf->Cell(75,6,$name_doctor.", M.D.","",0,"");

$pdf->Ln(5);
$pdf->setFont($fontStyle,"",$fontSizeText);
$pdf->Cell(116,6,'',"",0,"");
$pdf->Cell(75,6,"Attending Physician","",0,"");
#$pdf->Cell(155,6,"Attending Physician","",0,"R");

$pdf->Ln(6);
$pdf->Cell(116,6,'',"",0,"");
$pdf->Cell(75,6,"Lic No. _______________","",0,"");
#$pdf->Cell(168,6,"Lic No. _______________","",0,"R");


$pdf->Ln(8);
$pdf->Cell(30,6,"NOT VALID","",0,"R");
$pdf->Ln();
$pdf->Cell(50,6,"WITHOUT DMC SEAL","",0,"R");


//print pdf
$pdf->Output();

?>