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


$printable_width = 190.5;
$half_width = 95.25;
//conversion factor: 1 Inch = 25.4mm 

$fpdf = new FPDF('P', 'mm', 'Letter');
$fpdf->SetMargins(12.7, 12.7, 12.7); 
$fpdf->AddPage('P');
//$fpdf->SetDisplayMode('real', 'default');

class MultiCell_Table
{
var $widths;
var $aligns;
var $fpdf;

function SetFpdf(&$fpdf) {
  $this->fpdf = $fpdf;
}

function SetWidths($w) {
    //Set the array of column widths
    $this->widths=$w;
}

function SetAligns($a) {
    //Set the array of column alignments
    $this->aligns=$a;
}

function Row($data) {
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=5*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->fpdf->GetX();
        $y=$this->fpdf->GetY();
        //Draw the border
        $this->fpdf->Rect($x,$y,$w,$h);
        //Print the text
        $this->fpdf->MultiCell($w,5,$data[$i],0,$a);
        //Put the position to the right of the cell
        $this->fpdf->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->fpdf->Ln($h);
}

function CheckPageBreak($h) {
    //If the height h would cause an overflow, add a new page immediately
    if($this->fpdf->GetY()+$h>$this->fpdf->PageBreakTrigger)
        $fpdf->AddPage($this->fpdf->CurOrientation);
}

function NbLines($w,$txt) {
    //Computes the number of lines a MultiCell of width w will take
    $cw=$this->fpdf->CurrentFont['cw'];
    if($w==0)
        $w=$this->fpdf->w-$this->fpdf->rMargin-$this->fpdf->x;
    $wmax=($w-2*$this->fpdf->cMargin)*1000/$this->fpdf->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}
}

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

$pre_operative_diagnosis = $or_main_info['pre_op_diagnosis'];

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
$date_of_operation = $seg_ops->get_date_of_operation($refno);
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

$anesthesia_array = $seg_ops->get_or_main_anesthesia_as_array($or_main_info['or_main_refno']);
$anesthesia = current($anesthesia_array);


 
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

/** For title **/
$fpdf->SetFont('Arial', 'B', 11);
$fpdf->Cell(0, 4, 'OPERATION ROOM RECORD', 0, 1, 'C');
$fpdf->Ln(5);
/** End for title **/

/** Patient Info **/

$seg_person = new Person($basic_info['pid']);
$person_info = $seg_person->getAllInfoArray();
$person_gender = (strnatcasecmp($person_info['sex'], 'm') == 0) ? 'Male' : 'Female';
$person_age = (int)$seg_person->getAge(date('m/d/Y', strtotime($person_info['date_birth'])));
$person_age = is_int($person_age) ? $person_age . ' years old' : '-Not specified-';

$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(12, 4, 'Name:', 0, 0, 'L');
$fpdf->SetFont('Arial', 'UB', 10);
$fpdf->Cell(70, 4, ucwords($person_info['name_last']).', '.ucwords($person_info['name_first']).' '.ucwords($person_info['name_middle']), 0, 0, 'L');
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(10, 4, 'Age:', 0, 0, 'L');
$fpdf->SetFont('Arial', 'UB', 10);
$fpdf->Cell(30, 4, $person_age, 0, 0, 'L');
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(10, 4, 'Sex:', 0, 0, 'L');
$fpdf->SetFont('Arial', 'UB', 10);
$fpdf->Cell(20, 4, $person_gender, 0, 0, 'L');
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(10, 4, 'C.S.:', 0, 0, 'L');
$fpdf->SetFont('Arial', 'UB', 10);
$fpdf->Cell(15, 4, 'No', 0, 1, 'L');

$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(23, 4, 'Ward/Room:', 0, 0, 'L');
$fpdf->SetFont('Arial', 'UB', 10);
$fpdf->Cell(50, 4, $department, 0, 0, 'L');
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(32, 4, 'Bed Number:', 0, 0, 'R');
$fpdf->SetFont('Arial', 'UB', 10);
$fpdf->Cell(17, 4, 11, 0, 0, 'L');
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(30, 4, 'Hospital Number:', 0, 0, 'L');
$fpdf->SetFont('Arial', 'UB', 10);
$fpdf->Cell(40, 4, $basic_info['pid'], 0, 1, 'L');  
$fpdf->Ln(5);         
/** End: For patient info **/

/** Pre-operative and Post-operative Diagnosis **/
$fpdf->Ln(2);
$fpdf->SetFont('Arial', '', 10);
$fpdf->Write(4, 'PRE-OPERATIVE DIAGNOSIS: ');
$fpdf->SetFont('Arial', 'U', 10);
$fpdf->Write(4, $pre_operative_diagnosis); 
$fpdf->Ln();

$fpdf->Ln(2);
$fpdf->SetFont('Arial', '', 10);
$fpdf->Write(4, 'POST-OPERATIVE DIAGNOSIS: ');
$fpdf->SetFont('Arial', 'U', 10);
$fpdf->Write(4, $post_op_diagnosis); 
$fpdf->Ln(10);

/** End: Pre-operative and Post-operative Diagnosis **/


$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(28, 4, 'Surgeon: ', 0, 0, 'L');
$fpdf->SetFont('Arial', 'BU', 10);
$fpdf->Cell(60.25, 4, $surgeon['name'], 0, 0, 'L');
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(20.25, 4, 'Assistant: ', 0, 0, 'L');
$fpdf->SetFont('Arial', 'BU', 10);
$fpdf->Cell(75.25, 4, $first_assistant['name'], 0, 1, 'L');
$fpdf->Ln(3);
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(28, 4, 'Anesthesiologist: ', 0, 0, 'L');
$fpdf->SetFont('Arial', 'BU', 10);          
$fpdf->Cell(60.25, 4, $anesthesiologist['name'], 0, 0, 'L');
$fpdf->SetFont('Arial', '', 10);          
$fpdf->Cell(40, 4, 'Time Anesthesia Began: ', 0, 0, 'L');
$fpdf->SetFont('Arial', 'BU', 10);          
$fpdf->Cell(60.25, 4, $anesthesia['time_begun'], 0, 1, 'L'); 
$fpdf->SetFont('Arial', '', 10);          
$fpdf->Cell(30, 4, '', 0, 0, 'L');        
$fpdf->Cell(58.25, 4, '', 0, 0, 'L');
$fpdf->Cell(40, 4, 'Time Anesthesia Ended: ', 0, 0, 'L');
$fpdf->SetFont('Arial', 'BU', 10);
$fpdf->Cell(60.25, 4, $anesthesia['time_ended'], 0, 1, 'L');
$fpdf->SetFont('Arial', '', 10);
$fpdf->Cell(28, 4, 'Operation Date: ', 0, 0, 'L');
$fpdf->SetFont('Arial', 'BU', 10);          
$fpdf->Cell(60.25, 4, $date_of_operation, 0, 0, 'L');
$fpdf->SetFont('Arial', '', 10);          
$fpdf->Cell(40, 4, 'Time Operation Began: ', 0, 0, 'L');
$fpdf->SetFont('Arial', 'BU', 10);          
$fpdf->Cell(58.25, 4, $post_time_started, 0, 1, 'L');
$fpdf->SetFont('Arial', '', 10);          
$fpdf->Cell(30, 4, '', 0, 0, 'L');        
$fpdf->Cell(58.25, 4, '', 0, 0, 'L');
$fpdf->Cell(40, 4, 'Time Operation Ended: ', 0, 0, 'L');
$fpdf->SetFont('Arial', 'BU', 10); 
$fpdf->Cell(60.25, 4, $post_time_finished, 0, 1, 'L');

$fpdf->Ln(6);
$rvu_table = new MultiCell_Table();
$rvu_table->SetFpdf($fpdf);
$rvu_table->SetWidths(array(100,40,40));
$fpdf->SetFont('Arial', 'B', 10); 
$rvu_table->Row(array('TITLE OF OPERATION PERFORMED', 'RVS CODE', 'RVU'));
$fpdf->SetFont('Arial', '', 10);
$rvu = $seg_ops->get_rvu($refno);
if (count($rvu) > 0) {
  foreach ($rvu as $key=> $value) {
    $rvu_table->Row(array($value['description'], $value['rvs_code'], $value['rvu']));
  }
}
$fpdf->Ln(8); 
$fpdf->SetFont('Arial', '', 10); 
$fpdf->Cell(40, 4, 'Tissue to laboratory: ', 0, 0, 'L');
$fpdf->SetFont('Arial', 'B', 10); 
$fpdf->Cell(20, 4, '[  ] Yes ', 0, 0, 'L'); 
$fpdf->Cell(40, 4, '[  ] No ', 0, 0, 'L');
$fpdf->SetFont('Arial', '', 10); 
$fpdf->Cell(40, 4, 'Specimen (if any) ', 0, 1, 'L');
$fpdf->Ln(5);
$fpdf->Cell(40, 4, 'Description of findings intra operatively: ', 0, 1, 'L');
$fpdf->SetFont('Arial', '', 10); 
$fpdf->SetY(250);
$fpdf->Cell(100, 4, 'SURGEON: ', 0, 0, 'R');
$fpdf->SetFont('Arial', 'B', 10);
$fpdf->Cell(80, 4, $surgeon['name'], 0, 1, 'C'); 
$fpdf->SetFont('Arial', '', 10); 
$fpdf->Cell(100, 5, '', 0, 0, 'C');
$fpdf->Cell(80, 5, 'Signature over printed name', 'T', 1, 'C'); 
$fpdf->Ln(8);

$fpdf->AddPage();

$fpdf->SetFont('Arial', '', 10);
$fpdf->Write(4, 'SURGICAL TECHNIQUE: ');
$fpdf->SetFont('Arial', 'U', 10);
$fpdf->Write(4, $or_technique); 
$fpdf->Ln(10);

$fpdf->SetFont('Arial', '', 10);
$fpdf->Write(4, 'OPERATION ROOM MEDICATION ORDER: ');
$fpdf->SetFont('Arial', 'U', 10);

$fpdf->Ln(10);
$fpdf->SetFont('Arial', '', 10); 
$fpdf->Cell($printable_width/3, 4, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 4, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 20, 'Sponge count verified: ', 0, 1, 'L'); 

$fpdf->Cell($printable_width/3, 5, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 5, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 5, 'Signature over printed name', 'T', 1, 'C');

$fpdf->Cell($printable_width/3, 4, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 4, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 20, 'Drains: ', 0, 1, 'L'); 

$fpdf->Cell($printable_width/3, 5, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 5, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 5, 'Kind and number', 'T', 1, 'C'); 

$fpdf->Ln(8);
$fpdf->Write(4, 'SPONGE COUNT:');
$fpdf->Ln();
$columns = 6;

$sponge_table = new MultiCell_Table();
$sponge_table->SetFpdf($fpdf);
$sponge_table->SetWidths(array_fill(0,6,$printable_width/$columns));
$sponge_table->SetAligns(array_fill(0,6,'C'));
$fpdf->SetFont('Arial', 'B', 10);
$sponge_table->Row(array('Kind of Sponge', 'Original Number', 'Total', 'Used Sponges', 'Unused Sponges', 'Remarks'));
$fpdf->SetFont('Arial', '', 10);



$sponges = $seg_ops->get_sponges($or_main_info['or_main_refno']);
if (count($sponges) > 0) {
  foreach ($sponges as $key=> $value) {
    $used_sponges = ($value['second_count_total']);
    $unused_sponges = $value['initial_count'] - $used_sponges;
    $fpdf->Cell(($printable_width/$columns), 4, $value['sponge_name'], 1, 0, 'C');
    $fpdf->Cell(($printable_width/$columns), 4, $value['initial_count'], 1, 0, 'C');
    $fpdf->Cell(($printable_width/$columns), 4, $value['initial_count'], 1, 0, 'C');
    $fpdf->Cell(($printable_width/$columns), 4, $used_sponges, 1, 0, 'C');
    $fpdf->Cell(($printable_width/$columns), 4, $unused_sponges, 1, 0, 'C');
    $fpdf->Cell(($printable_width/$columns), 4, '', 1, 1, 'C');

  }
}
$fpdf->Ln(10);
$fpdf->Cell($printable_width/3, 4, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 4, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 4, 'Verified correct and counted by: ', 0, 1, 'L');
$fpdf->Cell($printable_width/3, 4, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 4, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 4, $circulating['name'], 0, 1, 'C'); 
$fpdf->SetFont('Arial', '', 10); 
$fpdf->Cell($printable_width/3, 5, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 5, '', 0, 0, 'C');
$fpdf->Cell($printable_width/3, 5, 'Signature of Circulating Nurse', 'T', 1, 'C'); 



                         
$fpdf->Output();

?>