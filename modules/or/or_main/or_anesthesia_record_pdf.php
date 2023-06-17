<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require_once('./roots.php'); //traverse the root directory
include_once($root_path.'/classes/fpdf/fpdf.php');

require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/billing/class_ops.php'); //load the SegOps class
require_once($root_path.'include/care_api_classes/class_department.php'); //load the department class
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path . 'include/care_api_classes/class_ward.php'); //load the ward class
require_once($root_path.'/modules/repgen/repgen.inc.php');  

$hospital = new Hospital_Admin();
$seg_ops = new SegOps();
$nr = $seg_ops->getOpRequestNrByRefNo($refno);
if ($seg_ops->encOpsNrHasOpsServ($nr)) {
  $basic_info = $seg_ops->getAllEncounterOpsServiceInfo($nr);
  $or_main_info = $seg_ops->get_or_main_basic_info($refno);
}
$seg_department = new Department();
$dept_nr = $basic_info['dept_nr'];
$department = $seg_department->FormalName($dept_nr);



$printable_width = 190.5;
$half_width = 95.25;
//conversion factor: 1 Inch = 25.4mm 

$fpdf = new FPDF('P', 'mm', 'Letter');
$fpdf->SetMargins(12.7, 12.7, 12.7); 
$fpdf->AddPage('P');
//$fpdf->SetDisplayMode('real', 'default');
 
/** Print Header **/
$hospital_info = $hospital->getAllHospitalInfo();
$hospital_info_array = array($hospital_info['hosp_country'], strtoupper($hospital_info['hosp_agency']), strtoupper($hospital_info['hosp_name']),
                             $hospital_info['hosp_addr1']);
$fpdf->SetFont("Times", "B", "10");
$fpdf->MultiCell(0, 5, implode("\n", $hospital_info_array), 0, 'C'); 
$fpdf->Image($root_path.'gui/img/logos/bukidnon_logo.jpg',45,5,28,28);
$fpdf->Line(0, $fpdf->GetY()+2.54, 215.8, $fpdf->GetY()+2.54);
$fpdf->SetY($fpdf->GetY()+5.08); 
/** End: Print Header **/

/** Patient Info **/

$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(12, 4, 'DATE:', 0, 0, 'L');
$fpdf->SetFont('Arial', 'UB', 10);
$fpdf->Cell(100, 4, date('Y-m-d'), 0, 0, 'L');
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(41.5, 4, 'HOSPITAL NO.:', 0, 0, 'R');
$fpdf->SetFont('Arial', 'UB', 10);
$fpdf->Cell(35, 4, $basic_info['pid'], 0, 1, 'R');
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(31, 4, 'HOSPITAL NAME:', 0, 0, 'L');
$fpdf->SetFont('Arial', 'UB', 10);
$fpdf->Cell(95, 4, strtoupper($hospital_info['hosp_name']), 0, 0, 'L');
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(27.5, 4, 'WARD/ROOM:', 0, 0, 'R');
$fpdf->SetFont('Arial', 'UB', 10);
$fpdf->Cell(35, 4, $department, 0, 1, 'R');

$seg_person = new Person($basic_info['pid']);
$person_info = $seg_person->getAllInfoArray();
$person_gender = (strnatcasecmp($person_info['sex'], 'm') == 0) ? 'Male' : 'Female';
$person_age = (int)$seg_person->getAge(date('m/d/Y', strtotime($person_info['date_birth'])));
$person_age = is_int($person_age) ? $person_age . ' years old' : '-Not specified-';
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(20, 4, 'SURNAME:', 0, 0, 'L');
$fpdf->SetFont('Arial', 'UB', 10);
$fpdf->Cell(40, 4, ucwords($person_info['name_last']), 0, 0, 'L');
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(10, 4, 'AGE:', 0, 0, 'R');
$fpdf->SetFont('Arial', 'UB', 10);
$fpdf->Cell(15, 4, $person_age, 0, 1, 'L');
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(25, 4, 'GIVEN NAME:', 0, 0, 'L');
$fpdf->SetFont('Arial', 'UB', 10);
$fpdf->Cell(35, 4, ucwords($person_info['name_first']), 0, 0, 'L');
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(10, 4, 'SEX:', 0, 0, 'R');
$fpdf->SetFont('Arial', 'UB', 10);
$fpdf->Cell(40, 4, $person_gender, 0, 1, 'L');  
$fpdf->Ln(5);         
/** End: For patient info **/

/** For title **/
$fpdf->SetFont('Arial', 'B', 11);
$fpdf->Cell(0, 4, 'OPERATING ROOM AND ANESTHESIA RECORD', 0, 1, 'C');
$fpdf->Ln(5);
/** End for title **/

/** Surgeons **/

$surgeon_array = $seg_ops->getOpsPersonellNr($refno,7);
$surgeon_info = $seg_ops->setPersonellNrNamePID($surgeon_array);
$surgeon = current($surgeon_info);

$assistant_surgeon_array = $seg_ops->getOpsPersonellNr($refno,8);
$assistant_surgeon_info = $seg_ops->setPersonellNrNamePID($assistant_surgeon_array);
$first_assistant = current($assistant_surgeon_info);
$second_assistant = next($assistant_surgeon_info);

$anesthesiologist_array = $seg_ops->getOpsPersonellNr($refno,12);
$anesthesiologist_info = $seg_ops->setPersonellNrNamePID($anesthesiologist_array);
$anesthesiologist = current($anesthesiologist_info);

$scrub_array = $seg_ops->getOpsPersonellNr($refno,9);
$scrub_info = $seg_ops->setPersonellNrNamePID($scrub_array);
$scrub = current($scrub_info);

$circulating_array = $seg_ops->getOpsPersonellNr($refno,10);
$circulating_info = $seg_ops->setPersonellNrNamePID($circulating_array);
$circulating = current($circulating_info);

$post_op_details = $seg_ops->get_or_main_post_details($or_main_info['or_main_refno']);
$post_time_started = $post_op_details['time_started'] . ' '.$post_op_details['ts_meridian'];
$post_time_finished = $post_op_details['time_finished'] . ' '.$post_op_details['tf_meridian'];
$post_op_diagnosis = $post_op_details['post_op_diagnosis']; 
$anesthetic_intra_operative = $post_op_details['intra_operative'];
$anesthetic_post_operative = $post_op_details['post_operative'];
$anesthetic_patient_status = $post_op_details['or_status'];
$operation_performed = $post_op_details['operation_performed'];
$ward = new Ward();
$my_ward = $ward->getWardInfo($post_op_details['transferred_to']);
$transferred_to = $my_ward['ward_id'] . ' - ' . $my_ward['name'];
$or_technique = $post_op_details['or_technique'];
$sponge_count = $post_op_details['sponge_count'];
$needle_count = $post_op_details['needle_count'];
$instrument_count = $post_op_details['instrument_count']; 
$fpdf->SetFont('Arial', 'B', 10);
$fpdf->Cell($printable_width/3, 4, $surgeon['name'], 'B', 0, 'C');
$fpdf->Cell($printable_width/3, 4, $first_assistant['name'], 'B', 0, 'C');
$fpdf->Cell($printable_width/3, 4, $second_assistant['name'], 'B', 1, 'C');
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell($printable_width/3, 4, 'SURGEON', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 4, 'First Assistant', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 4, 'Second Assistant', 0, 1, 'C');
$fpdf->Ln(3);
/** Surgeons **/

/** Anesthesiologist **/
$anesthesia_array = $seg_ops->get_or_main_anesthesia_as_array($or_main_info['or_main_refno']);
$anesthesia = current($anesthesia_array);
 
$fpdf->SetFont('Arial', 'B', 10);
$fpdf->Cell($printable_width/4, 4, $anesthesiologist['name'], 'B', 0, 'C');
$fpdf->Cell($printable_width/4, 4, $anesthesia['anesthesia'], 'B', 0, 'C');
$fpdf->Cell($printable_width/4, 4, $anesthesia['time_begun'], 'B', 0, 'C');
$fpdf->Cell($printable_width/4, 4, $anesthesia['time_ended'], 'B', 1, 'C');
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell($printable_width/4, 4, 'Anesthesiologist', 0, 0, 'C');
$fpdf->Cell($printable_width/4, 4, 'Anesthesia Used', 0, 0, 'C');
$fpdf->Cell($printable_width/4, 4, 'Anesthetic Began', 0, 0, 'C'); 
$fpdf->Cell($printable_width/4, 4, 'Anesthetic Ended', 0, 1, 'C');
$fpdf->Ln(3);  
/** Anesthesiologist **/

/** Nurses **/
$fpdf->SetFont('Arial', 'B', 10);
$fpdf->Cell(($printable_width/4)+10, 4, $scrub['name'], 'B', 0, 'C');
$fpdf->Cell(($printable_width/4)+10, 4, $circulating['name'], 'B', 0, 'C');
$fpdf->Cell(($printable_width/4)-10, 4, $post_time_started, 'B', 0, 'C');
$fpdf->Cell(($printable_width/4)-10, 4, $post_time_finished, 'B', 1, 'C');
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(($printable_width/4)+10, 4, 'Scrub Nurse', 0, 0, 'C');
$fpdf->Cell(($printable_width/4)+10, 4, 'Circulating Nurse', 0, 0, 'C');
$fpdf->Cell(($printable_width/4)-10, 4, 'Operation Began', 0, 0, 'C'); 
$fpdf->Cell(($printable_width/4)-10, 4, 'Operation Ended', 0, 1, 'C');
$fpdf->Ln(3); 
/** Nurses **/

/** Complications attributed to anesthesia **/
$fpdf->SetFont('Arial', 'B', 10);
$fpdf->Cell(0, 4, 'Complications attributable to anesthetic agent:');
$fpdf->Ln(5);
$fpdf->SetFont('Arial', '', 10);
$fpdf->Write(4, '1.) Intra Operative: ');
$fpdf->SetFont('Arial', 'U', 10); 
$fpdf->Write(4, $anesthetic_intra_operative);
$fpdf->Ln();
$fpdf->SetFont('Arial', '', 10);
$fpdf->Write(4, '2.) Post Operative: ');
$fpdf->SetFont('Arial', 'U', 10);
$fpdf->Write(4, $anesthetic_post_operative);
$fpdf->Ln();
$fpdf->SetFont('Arial', '', 10);
$fpdf->Write(4, '3.) Status of patient before leaving the OR theater: ');
$fpdf->SetFont('Arial', 'U', 10);
$fpdf->Write(4, $anesthetic_patient_status);
/** Complications attributed to anesthesia **/ 
$fpdf->Ln();
$fpdf->SetFont('Arial', '', 10);
$fpdf->Write(4, 'Transferred to: ');
$fpdf->SetFont('Arial', 'U', 10);
$fpdf->Write(4, $transferred_to);
$fpdf->Ln(8);
$fpdf->SetFont('Arial', '', 10);
$fpdf->Write(4, 'RVU: ');
$fpdf->Ln();
$fpdf->SetFont('Arial', 'U', 10);
$rvu = $seg_ops->get_rvu($refno);
if (count($rvu) > 0) {
  foreach ($rvu as $key=> $value) {
    $fpdf->Write(4, $value['rvu'] . ' - ' . $value['description']);
    $fpdf->Ln();
  }
}
$fpdf->Ln(2);
$fpdf->SetFont('Arial', '', 10);
$fpdf->Write(4, 'Pre-Operative Diagnosis: ');
$fpdf->SetFont('Arial', 'U', 10);
$fpdf->Write(4, $pre_operative_diagnosis); 
$fpdf->Ln();
$fpdf->SetFont('Arial', '', 10);
$fpdf->Write(4, 'Post-Operative Diagnosis: ');
$fpdf->SetFont('Arial', 'U', 10);
$fpdf->Write(4, $post_op_diagnosis); 
$fpdf->Ln();
$fpdf->SetFont('Arial', '', 10);
$fpdf->Write(4, 'Operation Performed: ');
$fpdf->SetFont('Arial', 'U', 10);
$fpdf->Write(4, $operation_performed); 
$fpdf->Ln();
$fpdf->SetFont('Arial', '', 10);
$fpdf->Write(4, 'O.R. Technique: ');
$fpdf->SetFont('Arial', 'U', 10);
$fpdf->Write(4, $or_technique);

$fpdf->Ln(10);
$fpdf->SetFont('Arial', '', 10);

$fpdf->SetFont('Arial', 'B', 10); 
$fpdf->Cell($printable_width/3, 4, $anesthesiologist['name'], 0, 0, 'C');
$fpdf->Cell($printable_width/3, 4, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 4, $surgeon['name'], 0, 1, 'C'); 
$fpdf->SetFont('Arial', '', 10); 
$fpdf->Cell($printable_width/3, 5, 'Anesthesiologist\'s Signature', 'T', 0, 'C');
$fpdf->Cell($printable_width/3, 5, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 5, 'Surgeon\'s Signature', 'T', 1, 'C'); 
$fpdf->Ln(8);


$fpdf->Cell(($printable_width/4)+13, 8, 'Sponges', 1, 0, 'C');
$fpdf->Cell(($printable_width/4)-13, 8, 'Sponge Initial Count', 1, 0, 'C');
$fpdf->Cell($printable_width/4, 4, 'First Count', 1, 0, 'C');
$fpdf->Cell($printable_width/4, 4, 'Second Count', 1, 1, 'C');
$fpdf->Cell($printable_width/4, 4, '', 0, 0, 'C');
$fpdf->Cell($printable_width/4, 4, '', 0, 0, 'C');
$fpdf->Cell(($printable_width/4)/3, 4, 'On Table', 1, 0, 'C'); 
$fpdf->Cell(($printable_width/4)/3, 4, 'On Floor', 1, 0, 'C');
$fpdf->Cell(($printable_width/4)/3, 4, 'TTL', 1, 0, 'C');
$fpdf->Cell(($printable_width/4)/3, 4, 'On Table', 1, 0, 'C'); 
$fpdf->Cell(($printable_width/4)/3, 4, 'On Floor', 1, 0, 'C');
$fpdf->Cell(($printable_width/4)/3, 4, 'TTL', 1, 1, 'C');

$sponges = $seg_ops->get_sponges($or_main_info['or_main_refno']);
if (count($sponges) > 0) {
  foreach ($sponges as $key=> $value) {
    $fpdf->Cell(($printable_width/4)+13, 4, $value['sponge_name'], 1, 0, 'C');
    $fpdf->Cell(($printable_width/4)-13, 4, $value['initial_count'], 1, 0, 'C');
    $fpdf->Cell(($printable_width/4)/3, 4, $value['first_count_on_table'], 1, 0, 'C'); 
    $fpdf->Cell(($printable_width/4)/3, 4, $value['first_count_on_floor'], 1, 0, 'C');
    $fpdf->Cell(($printable_width/4)/3, 4, $value['first_count_total'], 1, 0, 'C');
    $fpdf->Cell(($printable_width/4)/3, 4, $value['second_count_on_table'], 1, 0, 'C'); 
    $fpdf->Cell(($printable_width/4)/3, 4, $value['second_count_on_floor'], 1, 0, 'C');
    $fpdf->Cell(($printable_width/4)/3, 4, $value['second_count_total'], 1, 1, 'C');
  }
}
$fpdf->Ln(8);
$fpdf->SetFont('Arial', '', 10);
$fpdf->Write(4, 'Sponge Count: ');
$fpdf->SetFont('Arial', 'U', 10);
$fpdf->Write(4, $sponge_count); 
$fpdf->Ln();

$fpdf->SetFont('Arial', '', 10);
$fpdf->Write(4, 'Needles: ');
$fpdf->SetFont('Arial', 'U', 10);
$fpdf->Write(4, $needle_count); 
$fpdf->Ln();

$fpdf->SetFont('Arial', '', 10);
$fpdf->Write(4, 'Instruments: ');
$fpdf->SetFont('Arial', 'U', 10);
$fpdf->Write(4, $instrument_count); 
$fpdf->Ln();

$fpdf->SetFont('Arial', 'B', 10); 
$fpdf->Cell($printable_width/3, 4, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 4, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 4, $scrub['name'], 0, 1, 'C'); 
$fpdf->SetFont('Arial', '', 10); 
$fpdf->Cell($printable_width/3, 5, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 5, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 5, 'Signature of Scrub Nurse', 'T', 1, 'C');
$fpdf->Ln(10); 
$fpdf->SetFont('Arial', 'B', 10); 
$fpdf->Cell($printable_width/3, 4, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 4, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 4, $circulating['name'], 0, 1, 'C'); 
$fpdf->SetFont('Arial', '', 10); 
$fpdf->Cell($printable_width/3, 5, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 5, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 5, 'Signature of Circulating Nurse', 'T', 1, 'C'); 
                         
$fpdf->Output();

?>