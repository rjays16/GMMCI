<?php
  error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
//require($root_path.'/modules/repgen/repgen.inc.php');
require($root_path.'/modules/repgen/fpdf.php');
include_once($root_path."include/care_api_classes/class_hospital_admin.php");      

class PhilhealthForm3 extends FPDF {
  
  var $colored = TRUE;
  var $countpage = 1;
  var $x1 = 5;
  var $y1 = 8;
  var $col2 = 100;
  var $third = 150;
  var $tot_width = 206;
  var $case_acc_array;       //Array of class Case_AccNum
  var $patient_array;       //Array of class PatientRecord
  var $exam_array;          //Array of class PhysicalExam
  var $course_array;        //Array of class CourseWard
  var $findings_array;      //Array of class Findings
  var $op_array;            //Array of class SurgicalOp
  var $fontfamily_label = "Arial";
  var $fontfamily_ans = "Arial";
  var $fontstyle_label = "B";
  var $fontstyle_ans = '';
  var $fontsize_label = 9;
  var $fontsize_ans = 9;
  var $fontsize_philhealth = 18;
  var $fontsize_claim = 10;
  var $fontsize_patrecord = 14;
  var $fontsize_note = 7;
  var $fontsize_label2 = 10;
  var $h_cell = 2;
  var $h_multi = 3;
  var $boxwidth = 3;
  var $boxheight = 2.5;
  var $max_space = 190;
   
  function PhilhealthForm3(){
       $this->FPDF('P', 'mm', 'letter');
       $this->SetDrawColor(0,0,0);
       $this->SetMargins(5,2,1);
  }
   
  function addHead($name, $formnum, $revised, $note, $rec){

       $this->Rect($this->x1, $this->y1-2, 160, 17);
       //Font family Arial, style B, size 18
       $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_philhealth);
       //PHILHEALTH
       $this->SetXY( $this->x1, $this->y1 );
       $length = $this->GetStringWidth( $name );
       $this->Cell( $length, $this->h_cell, $name,0,0);
       //claim form 3
       $this->SetXY($this->x1,$this->y1+4);
       //Font family Arial, style B, size 10
       $this->SetFont($this->fontfamily_label, $this->fontstyle_label,$this->fontsize_claim);
       $length = $this->GetStringWidth( $formnum );
       $this->Cell($length, $this->h_cell, $formnum, 0, 0);
      
       //Patient's Clinical Record
       $this->SetX($this->x1+45);
       //Font family Arial, style B, size 14
       $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_patrecord);
       $length = $this->GetStringWidth($rec);
       $this->Cell($length, $this->h_cell, $rec, 0,0);
       //Revised May 2000
       $this->SetXY($this->x1,$this->y1+8);
       //Font family Arial, style B, size 9
       $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
       $length = $this->GetStringWidth($revised);
       $this->Cell($length, $this->h_cell, $revised, 0,0);
       //Note
       $this->SetXY($this->x1,$this->y1+12);
       //Font family Arial, style B, size 7
       $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_note);
       $length = $this->GetStringWidth($note);
       $this->Cell($length, $this->h_cell, $note, 0, 0);
   
  }

  function addDate($date){ 
 
       $this->Rect($this->x1+160, $this->y1-2, 46, 17);
       $this->SetXY($this->x1+165, $this->y1);
       //Font family Arial, style B, size 10 
       $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label2);
       $length = $this->GetStringWidth($date);
       $this->Cell($length, $this->h_cell, $date, 0, 0);

  }

  function Footer(){
      //Position at 1.5 cm from bottom
      $this->SetY(-15);
      //Arial italic 8
      $this->SetFont('Arial','I',8);
      //Text color in gray
      $this->SetTextColor(128);
      //Page number
      //$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
  }

  function addCaseAcc($casenum, $ad, $date, $time, $accnum){
  
      $height = 17;
      $this->Rect($this->x1, $this->y1+15, $this->tot_width, 18);
      $this->Line($this->x1+$this->col2, $this->y1+15, $this->x1+$this->col2, $this->y1+33);
      
      //Case Number
      $this->SetXY($this->x1+$this->col2, $this->y1+$height);
      //Font family Arial, style B, size 9
      $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
      $length = $this->GetStringWidth($casenum);
      $this->Cell($length, $this->h_cell, $casenum, 0, 0);
      $l_case = $length + 2;
      $height1 = $height + 4;
      //Admission
      $this->SetXY($this->x1+$this->col2, $this->y1+$height1);
      $length = $this->GetStringWidth($ad);
      $this->Cell($length, $this->h_cell, $ad, 0,0);
      $height2 = $height1 + 5;
      //Date
      $this->SetXY($this->x1+$this->col2, $this->y1+$height2);
      $length = $this->GetStringWidth($date);
      $this->Cell($length, $this->h_cell, $date, 0,0);
      $l_date = $length + 2;
      //Time
      $this->SetXY($this->x1+150, $this->y1+$height2);
      $length = $this->GetStringWidth($time);
      $this->Cell($length, $this->h_cell, $time, 0, 0);
      $height3 = $height2 + 4;
      $l_time = $length + 2;
      // Accredation Number
      $this->SetXY($this->x1+$this->col2, $this->y1+$height3);
      $length = $this->GetStringWidth($accnum);
      $this->Cell($length, $this->h_cell, $accnum, 0, 0);
      $l_acc = $length + 2;
      
      //container...
      if(!empty($this->case_acc_array)){
                foreach($this->case_acc_array as $objcase){
                //Font family Arial, style normal, size 9
                $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
                //Case Number
                $this->SetXY($this->x1 + ($l_case + $this->col2), $this->y1+$height);
                $length = $this->GetStringWidth($objcase->casenum);
                $this->Cell($length, $this->h_cell, $objcase->casenum,0,0,'L');
                //Date
                $this->SetXY($this->x1 + ($l_date + $this->col2), $this->y1+$height2);
                $length = $this->GetStringWidth($objcase->admission_dt);
                $this->Cell($length, $this->h_cell, $objcase->admission_dt,0,0,'L');
                //Time
                $this->SetXY($this->x1 + ($l_time + $this->col_third), $this->y1+$height2); 
                $length = $this->GetStringWidth($objcase->admission_tm);
                $this->Cell($length, $this->h_cell, $objcase->admission_tm,0,0,'L');
                //Accredation Number
                $this->SetXY($this->x1 + ($l_acc + $this->col2), $this->y1+$height3);
                $length = $this->GetStringWidth($objcase->accnum);
                $this->Cell($length, $this->h_cell, $objcase->accnum,0,0,'L');
                }
      }
      
  }

  function addHospitalName($name){
      global $db;
      $objInfo = new Hospital_Admin();
      if ($row = $objInfo->getAllHospitalInfo()) {      
            $row['hosp_name']   = strtoupper($row['hosp_name']);
          }    
      $this->Rect($this->x1, $this->y1+33, $this->tot_width, 10);
      $this->SetXY($this->x1, $this->y1+34);
      //Font family Arial, style B, size 9
      $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
      $length = $this->GetStringWidth($name);
      $this->Cell($length, $this->h_cell, $name, 0,0);
      $this->Ln(4);
      //Font family Arial, style normal, size 9
      $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
      $this->Cell($this->tot_width, $this->h_cell, $row['hosp_name'],0,0);
      //$this->Ln(4);
      //$this->Cell(10, 2, "bdfbsdb", 1,0);
  }

  function addHospitalAddress($address, $street, $city, $barangay, $province, $code){
      global $db;
      $objInfo = new Hospital_Admin();
      if($row = $objInfo->getAllHospitalInfo()){
            $row['hosp_addr1'] = strtoupper($row['hosp_addr1']);
            $row['hosp_addr2'] = strtoupper($row['hosp_addr2']);
      }
      $this->Rect($this->x1, $this->y1+43, $this->tot_width, 20);
      //address
      $this->SetXY($this->x1, $this->y1+44);
      //Font family Arial, style B, size 9
      $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
      $length = $this->GetStringWidth($address);
      $this->Cell($length, $this->h_cell, $address, 0, 0);
      //street
      $this->SetXY($this->x1, $this->y1+48);
      $length = $this->GetStringWidth($street);
      $this->Cell($length, $this->h_cell, $street, 0,0);
      $this->Ln(4);
      //Font family Arial, style normal, size 9
      $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
      $length = $this->GetStringWidth($row['hosp_addr1']);
      $this->Cell($length, $this->h_cell, $row['hosp_addr1']);
      //barangay
      $this->SetXY($this->x1+$this->col2, $this->y1+48);
      $length = $this->GetStringWidth($barangay);
      $this->Cell($length, $this->h_cell, $barangay, 0,0);
      //city
      //Font family Arial, style B, size 9
      $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
      $this->SetXY($this->x1, $this->y1+56);
      $length = $this->GetStringWidth($city);
      $this->Cell($length, $this->h_cell, $city, 0,0);
      $this->Ln(4);
      //Font family Arial, style normal, size 9
      $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
      $length = $this->GetStringWidth($row['hosp_addr2']);
      $this->Cell($length, $this->h_cell, $row['hosp_addr2']);      
      //province
      $this->SetXY($this->x1+$this->col2, $this->y1+56);
      $length = $this->GetStringWidth($province);
      $this->Cell($length, $this->h_cell, $province, 0, 0);
      //code
      $this->SetXY($this->x1+180, $this->y1+56);
      $length = $this->GetStringWidth($code);
      $this->Cell($length, $this->h_cell, $code, 0,0);
  }

  function addPatientRecord($title, $patient, $lname, $fname, $mname, $age, $sex){

      $ans = "X";
      $noans = " ";
      $height = 64;
      $this->Rect($this->x1, $this->y1+63, $this->tot_width, 32);
      //title
      $this->SetXY($this->x1+80, $this->y1+$height);
      //Font family Arial, style B, size 9
      $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
      $length = $this->GetStringWidth($title);
      $this->Cell($length, $this->h_cell, $title, 0,0);
      $height = $height + 4;     //68
      //patient
      $this->SetXY($this->x1, $this->y1+$height);
      $length = $this->GetStringWidth($patient);
      $this->Cell($length, $this->h_cell, $patient, 0,0);
      $height1 = $height + 4;    //72
      //last name
      $this->SetXY($this->x1, $this->y1+$height1);
      $length = $this->GetStringWidth($lname);
      $this->Cell($length, $this->h_cell, $lname, 0,0);
      $height2 = $height1 + 8;   //80
      //first name
      $this->SetXY($this->x1, $this->y1+$height2);
      $length = $this->GetStringWidth($fname);
      $this->Cell($length, $this->h_cell, $fname, 0,0);
      $height3 = $height2 + 8;    //88
      //middle name
      $this->SetXY($this->x1, $this->y1+$height3);
      $length = $this->GetStringWidth($mname);
      $this->Cell($length, $this->h_cell, $mname, 0,0);
      $height4 = $height3 + 4;   //94
      //age
      $this->Line($this->x1+120, $this->y1+$height, $this->x1+120, $this->y1+($height2));
      $this->SetXY($this->x1+120, $this->y1+$height);
      $length = $this->GetStringWidth($age);
      $this->Cell($length, $this->h_cell, $age, 0,0);
      //sex
      $this->Line($this->x1+160, $this->y1+$height, $this->x1+160, $this->y1+($height2));
      $this->SetXY($this->x1+160, $this->y1+$height);
      $length = $this->GetStringWidth($sex);
      $this->Cell($length, $this->h_cell, $sex, 0,0);
      
        if(!empty($this->patient_array)){
               foreach($this->patient_array as $objpat){
                 //Font family Arial, style normal, size 9
                 $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
                 //Last Name
                 $this->SetXY($this->x1, $this->y1+($height1 + 4));
                 $length = $this->GetStringWidth($objpat->pat_lname);
                 $this->Cell($length, $this->h_cell, $objpat->pat_lname,0,0,'L');
                //First Name
                 $this->SetXY($this->x1, $this->y1+($height2 + 4));
                 $length = $this->GetStringWidth($objpat->pat_fname);
                 $this->Cell($length, $this->h_cell, $objpat->pat_fname,0,0,'L');
                 //Middle Name
                 $this->SetXY($this->x1, $this->y1+$height4);
                 $length = $this->GetStringWidth($objpat->pat_mname);
                 $this->Cell($length, $this->h_cell, $objpat->pat_mname,0,0,'L');
                 //Age
                 $this->SetXY($this->x1 + ($this->col2 + 30), $this->y1+$height1);
                 $length = $this->GetStringWidth($objpat->pat_age);
                 $this->Cell($length, $this->h_cell, $objpat->pat_age,0,0,'L');
                 $box_x = 13;
                 $label_x = 20;
                 //Sex
                 if($objpat->pat_sex == "m"){
                        $this->SetXY($this->x1+($this->third + $box_x), $this->y1+$height1); //163
                        $this->Cell($this->boxwidth, $this->boxheight, $ans,1,0,'C');
                        $this->SetXY($this->x1+ ($this->third + $label_x), $this->y1+$height1); //170
                        $length = $this->GetStringWidth("Male");
                        $this->Cell($length, $this->h_cell, "Male", 0,0);
                        $this->SetXY($this->x1+($this->third + $box_x), $this->y1+($height1 + 4)); //163
                        $this->Cell($this->boxwidth, $this->boxheight, $noans,1,0,'C');
                        $this->SetXY($this->x1+($this->third + $label_x), $this->y1+($height1 + 4)); //170
                        $length = $this->GetStringWidth("Female");
                        $this->Cell($length, $this->h_cell, "Female", 0,0);
                       
                 }else{
                        $this->SetXY($this->x1+($this->third + $box_x), $this->y1+$height1); //163
                        $this->Cell($this->boxwidth, $this->boxheight, $noans,1,0,'C');
                        $this->SetXY($this->x1+($this->third + $label_x), $this->y1+$height1); //170
                        $length = $this->GetStringWidth("Male");
                        $this->Cell($length, $this->h_cell, "Male", 0,0);
                        $this->SetXY($this->x1+($this->third + $box_x), $this->y1+($height1 + 4)); //163
                        $this->Cell($this->boxwidth, $this->boxheight, $ans,1,0,'C');
                        $this->SetXY($this->x1+($this->third + $label_x), $this->y1+($height1 + 4)); //170
                        $length = $this->GetStringWidth("Female");
                        $this->Cell($length, $this->h_cell, "Female", 0,0);
                        }
                
                    
               }
        }
  }
  function addPatientSignature($signature){
      $line_start = 123;
      $line_end = 195;
      $this->Rect($this->x1+120, $this->y1+80, 86,15);
      $this->SetXY($this->x1+120,$this->y1+81);
      $length = $this->GetStringWidth("4.");
      $this->Cell($length, $this->h_cell, "4.", 0,0);
      $this->SetXY($this->x1+123, $this->y1+92);
      $this->Line($this->x1+$line_start, $this->y1+91, $this->x1+$line_end, $this->y1+91);
      //Font family Arial, style bold, size 9
      $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
      $length = $this->GetStringWidth($signature);
      $this->Cell($length, $this->h_cell, $signature, 0,0);
      
          if(!empty($this->patient_array)) {
                foreach($this->patient_array as $objpat){
                      //Font family Arial, style normal, size 9
                      $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
                      $this->SetXY($this->x1+123, $this->y1+89);
                      $this->Cell($line_end - $line_start, $this->h_cell, strtoupper($objpat->adofficer),0,0,'C');
                }
          }
  }

  function addDiagnosis($diagnosis){
  
      $this->Rect($this->x1, $this->y1+95, $this->tot_width, 15);
      $this->SetXY($this->x1, $this->y1+96);
      //Font family Arial, style bold, size 9
      $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
      $length = $this->GetStringWidth($diagnosis);
      $this->Cell($length, $this->h_cell, $diagnosis, 0,0);    
          if(!empty($this->patient_array)){
               foreach($this->patient_array as $objpat){
                     //Font family Arial, style normal, size 9
                     $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
                     $this->SetXY($this->x1, $this->y1+$this->col2);
                     $length = $this->GetStringWidth($objpat->admit_diagnosis);
                        if($length < $this->max_space){
                            $this->Cell($this->max_space, $this->h_cell, $objpat->admit_diagnosis,0,0,'L');
                        }else{
                            $this->MultiCell($this->max_space, $this->h_multi, $objpat->admit_diagnosis,0,0,'L');
                        }
                     
               }
          }
      
  }

  function addComplaint($complaint){
  
      $this->Rect($this->x1, $this->y1+110, $this->tot_width, 15);
      $this->SetXY($this->x1, $this->y1+111);
      //Font family Arial, style bold, size 9
      $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
      $length = $this->GetStringWidth($complaint);
      $this->Cell($length, $this->h_cell, $complaint, 0,0);
      $this->Ln(4);
          if(!empty($this->patient_array)){
                foreach($this->patient_array as $objpat){
                      //Font family Arial, style normal, size 9
                      $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
                      $this->SetXY($this->x1, $this->y1+115);
                      $length = $this->GetStringWidth($objpat->chief_complaint);     
                          if($length < $this->max_space){
                            $this->Cell($this->max_space, $this->h_cell, $objpat->chief_complaint,0,0,'L');
                          }else{
                            $this->MultiCell($this->max_space, $this->h_multi, $objpat->chief_complaint,0,0,'L');                
                          }                      
                }
          }    
  }

  function addAdmission($admission){
  
      $this->Rect($this->x1, $this->y1+125, $this->tot_width, 10);
      $this->SetXY($this->x1, $this->y1+126);
      //Font family Arial, style bold, size 9
      $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
      $length = $this->GetStringWidth($admission);
      $this->Cell($length, $this->h_cell, $admission, 0,0);
      $this->Ln(4);
           if(!empty($this->patient_array)){
                    foreach($this->patient_array as $objpat){
                          //Font family Arial, style normal, size 9
                          $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
                          $this->SetXY($this->x1, $this->y1+130);
                          $length = $this->GetStringWidth($objpat->admit_reason);     
                              if($length < $this->max_space){
                                $this->Cell($this->max_space, $this->h_cell, $objpat->admit_reason,0,0,'L');
                              }else{
                                $this->MultiCell($this->max_space, $this->h_multi, $objpat->admit_reason,0,0,'L');                
                              }                      
                    }
           }          
  }

  function addPresentIllness($history){
  
      $this->Rect($this->x1, $this->y1+135, $this->tot_width, 30);
      $this->SetXY($this->x1, $this->y1+136);
      //Font family Arial, style bold, size 9
      $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
      $length = $this->GetStringWidth($history);
      $this->Cell($length, $this->h_cell, $history, 0,0);
      $this->Ln(4);
          if(!empty($this->patient_array)){
                   foreach($this->patient_array as $objpat){
                          //Font family Arial, style normal, size 9
                          $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
                          $this->SetXY($this->x1, $this->y1+130);
                          $length = $this->GetStringWidth($objpat->ob_history);     
                              if($length < $this->max_space){
                                $this->Cell($this->max_space, $this->h_cell, $objpat->ob_history,0,0,'L');
                              }else{
                                $this->MultiCell($this->max_space, $this->h_multi, $objpat->ob_history,0,0,'L');                
                              }                      
                   }
          }                
  }

  function addPhysicalExam($title, $gen, $signs, $heent, $chest, $cvs, $ab, $gu, $skin, $neuro){
  
      $this->Rect($this->x1, $this->y1+165, $this->tot_width, 70);
      //Font family Arial, style bold, size 9
      $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
      //title
      $this->SetXY($this->x1, $this->y1+166);
      $length = $this->GetStringWidth($title);
      $this->Cell($length, $this->h_cell, $title, 0,0);
      //general survey
      $this->SetXY($this->x1+4, $this->y1+170);
      $length = $this->GetStringWidth($gen);
      $this->Cell($length, $this->h_cell, $gen, 0,0);
      $l_gensurv = $length + 2;
      //signs
      $this->SetXY($this->x1+4, $this->y1+174);
      $length = $this->GetStringWidth($signs);
      $this->Cell($length, $this->h_cell, $signs, 0,0);
      $this->SetX($this->x1+50);
      $length = $this->GetStringWidth("BP:");
      $this->Cell($length, $this->h_cell, "BP:");
      $l_bp = $length + 2;
      $bp_start = 57;
      $bp_end = 79;
      $this->Line($this->x1+57, $this->y1+176, $this->x1+79, $this->y1+176);
      $this->SetX($this->x1+80);
      $length = $this->GetStringWidth("HR:");
      $this->Cell($length, $this->h_cell, "HR:");
      $l_hr = $length + 2;
      $hr_start = 87;
      $hr_end = 109;
      $this->Line($this->x1+87, $this->y1+176, $this->x1+109, $this->y1+176);
      $this->SetX($this->x1+110);
      $length = $this->GetStringWidth("RR:");
      $this->Cell($length, $this->h_cell, "RR:");
      $l_rr = $length + 2;
      $rr_start = 117;
      $rr_end = 139;
      $this->Line($this->x1+117, $this->y1+176, $this->x1+139, $this->y1+176);
      $this->SetX($this->x1+140);
      $length = $this->GetStringWidth("Temperature:");
      $this->Cell($length, $this->h_cell, "Temperature");
      $l_temp = $length + 2;
      $temp_start = 162;
      $temp_end = 186;
      $this->Line($this->x1+162, $this->y1+176, $this->x1+186, $this->y1+176);
      //heent
      $this->SetXY($this->x1+4, $this->y1+178);
      $length = $this->GetStringWidth($heent);
      $this->Cell($length, $this->h_cell, $heent, 0,0);
      //chest/lungs
      $this->SetXY($this->x1+4, $this->y1+186);
      $length = $this->GetStringWidth($chest);
      $this->Cell($length, $this->h_cell, $chest, 0,0);
      //cvs
      $this->SetXY($this->x1+4, $this->y1+194);
      $length = $this->GetStringWidth($cvs);
      $this->Cell($length, $this->h_cell, $cvs, 0,0);
      //abdomen
      $this->SetXY($this->x1+4, $this->y1+202);
      $length = $this->GetStringWidth($ab);
      $this->Cell($length, $this->h_cell, $ab, 0,0);
      //gu
      $this->SetXY($this->x1+4, $this->y1+210);
      $length = $this->GetStringWidth($gu);
      $this->Cell($length, $this->h_cell, $gu, 0,0);
      //skin
      $this->SetXY($this->x1+4, $this->y1+218);
      $length = $this->GetStringWidth($skin);
      $this->Cell($length, $this->h_cell, $skin, 0,0);
      //neuro
      $this->SetXY($this->x1+4, $this->y1+226);
      $length = $this->GetStringWidth($neuro);
      $this->Cell($length, $this->h_cell, $neuro, 0,0);
  
          if(!empty($this->exam_array)){
                  foreach($this->exam_array as $objexam){
                        //Font family Arial, style normal, size 9
                        $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
                        //general survey
                        $this->SetXY($this->x1+$l_gensurv, $this->y1 +170);
                        $length = $this->GetStringWidth($objexam->gensurv);
                        $this->Cell($length, $this->h_cell, $objexam->gensurv,0,0,'L');
                        //Vital Sign -> BP
                        $this->SetXY($this->x1 + ($l_bp + 50), $this->y1+174);
                        $this->Cell($bp_end - $bp_start, $this->h_cell, $objexam->bp,0,0, 'C');
                        //Vital Sign -> HR
                        $this->SetXY($this->x1 + ($l_hr +80), $this->y1+174);
                        $this->Cell($hr_end - $hr_start, $this->h_cell, $objexam->hr,0,0,'C');
                        //Vital Sign -> RR
                        $this->SetXY($this->x1 + ($l_rr + 110), $this->y1+174);
                        $this->Cell($rr_end - $rr_start, $this->h_cell, $objexam->rr,0,0,'C');
                        //Vital Sign -> Temperature
                        $this->SetXY($this->x1 + ($l_temp + 140), $this->y1+174);
                        $this->Cell($temp_end - $temp_start, $this->h_cell, $objexam->temp,0,0,'C');
                        //HEENT
                        $this->SetXY($this->x1+4, $this->y1+182);
                        $length = $this->GetStringWidth($objexam->heent);
                        $this->Cell($length, $this->h_cell, $objexam->heent,0,0,'L');
                        //Chest/Lungs
                        $this->SetXY($this->x1+4, $this->y1+190);
                        $length = $this->GetStringWidth($objexam->chest);
                        $this->Cell($length, $this->h_cell, $objexam->chest,0,0,'L');
                        //CVS
                        $this->SetXY($this->x1+4, $this->y1+198);
                        $length = $this->GetStringWidth($objexam->cvs);
                        $this->Cell($length, $this->h_cell, $objexam->cvs,0,0,'L');
                        //Abdomen
                        $this->SetXY($this->x1+4, $this->y1+206);
                        $length = $this->GetStringWidth($objexam->abdomen);
                        $this->Cell($length, $this->h_cell, $objexam->abdomen,0,0,'L');
                        //GU (IE)
                        $this->SetXY($this->x1+4, $this->y1+214);
                        $length = $this->GetStringWidth($objexam->gu);
                        $this->Cell($length, $this->h_cell, $objexam->gu,0,0,'L');
                        //Skin/Extremities
                        $this->SetXY($this->x1+4, $this->y1+222);
                        $length = $this->GetStringWidth($objexam->skin);
                        $this->Cell($length, $this->h_cell, $objexam->skin,0,0,'L');
                        //Neuro Examination
                        $this->SetXY($this->x1+4, $this->y1+230);
                        $length = $this->GetStringWidth($objexam->neuro_exam);
                        $this->Cell($length, $this->h_cell, $objexam->neuro_exam,0,0,'L');
                        
                  }
          }
  }

  function addCourse($course){
  
      $this->Rect($this->x1, $this->y1-1, $this->tot_width, 70);
      $this->SetXY($this->x1, $this->y1);
      //Font family Arial, style bold, size 9
      $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
      $length = $this->GetStringWidth($course);
      $this->Cell($length, $this->h_cell, $course, 0,0);
          if(!empty($this->course_array)){
                  foreach($this->course_array as $objcrs){
                        //Font family Arial, style normal, size 9
                        $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
                        $this->SetXY($this->x1, $this->y1+4);
                        $length = $this->GetStringWidth($objcrs->course_in_wards);     
                              if($length < $this->max_space){
                                  $this->Cell($this->max_space, $this->h_cell, $objcrs->course_in_wards,0,0,'L');
                              }else{
                                  $this->MultiCell($this->max_space, $this->h_multi, $objcrs->course_in_wards,0,0,'L');                
                              }                      
                  }
          }          
  }

  function addFindings($findings){
    
      $this->Rect($this->x1, $this->y1+69, $this->tot_width, 70);
      $this->SetXY($this->x1, $this->y1+70);
      //Font family Arial, style bold, size 9
      $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
      $length = $this->GetStringWidth($findings);
      $this->Cell($length, $this->h_cell, $findings);
          if(!empty($this->findings_array)){
                  foreach($this->findings_array as $objfind){
                        //Font family Arial, style normal, size 9
                        $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
                        $this->SetXY($this->x1, $this->y1+74);
                        $length = $this->GetStringWidth($objfind->lab_diagnostic);     
                              if($length < $this->max_space){
                                  $this->Cell($this->max_space, $this->h_cell, $objfind->lab_diagnostic,0,0,'L');
                              }else{
                                  $this->MultiCell($this->max_space, $this->h_multi, $objfind->lab_diagnostic,0,0,'L');                
                              }                      
                  }
          }          
      
  }

  function addOperation($title, $date, $time, $sig, $diagnostics, $cond, $psig){
       
      //title
      $this->Rect($this->x1, $this->y1+139, $this->tot_width, 70);
      $this->SetXY($this->x1, $this->y1+140);
      //$this->SetFont('Arial', 'B', 9);
      $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
      $length = $this->GetStringWidth($title);
      $this->Cell($length, $this->h_cell, $title);
      //date
      $this->SetXY($this->x1, $this->y1+152);
      $length = $this->GetStringWidth($date);
      $this->Cell($length, $this->h_cell, $date);
      $l_date = $length + 2;
      //time
      $this->SetXY($this->x1+60, $this->y1+152);
      $length = $this->GetStringWidth($time);
      $this->Cell($length, $this->h_cell, $time);
      $l_time = $length + 2;
      //signature
      $line_start = 110;
      $line_end = 190;
      $this->Line($this->x1+$line_start, $this->y1+151, $this->x1+$line_end, $this->y1+151);
      $this->SetXY($this->x1+120, $this->y1+152);
      $length = $this->GetStringWidth($sig);
      $this->Cell($length, $this->h_cell, $sig);
      //diagnostics
      $this->SetXY($this->x1, $this->y1+160);
      $length = $this->GetStringWidth($diagnostics);
      $this->Cell($length, $this->h_cell, $diagnostics);
      //Condition on Discharge
      $this->SetXY($this->x1, $this->y1+175);
      $length = $this->GetStringWidth($cond);
      $this->Cell($length, $this->h_cell, $cond);
      //Physician signature
      $this->SetXY($this->x1, $this->y1+190);
      $length = $this->GetStringWidth($psig);
      $this->Cell($length, $this->h_cell, $psig);
      
          if(!empty($this->op_array)){
                  foreach($this->op_array as $objop){
                        //$this->SetFont('Arial', '', 9);
                        $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
                        //date
                        $this->SetXY($this->x1+$l_date, $this->y1+152);
                        $length = $this->GetStringWidth($objop->surg_date);
                        $this->Cell($length, $this->h_cell, $objop->surg_date, 0, 0, 'L');
                        //time
                        $this->SetXY($this->x1 + ($l_time + 60), $this->y1 + 152);
                        $length = $this->GetStringWidth($objop->surg_time);
                        $this->Cell($length, $this->h_cell, $objop->surg_time, 0, 0, 'L');
                        //signature-name
                        $this->SetXY($this->x1+$line_start, $this->y1 + 149);
                        $this->Cell($line_end - $line_start, $this->h_cell, strtoupper($objop->surg_name),0,0,'C');
                        //final diagnostics
                        $this->SetXY($this->x1, $this->y1+164);
                        $length = $this->GetStringWidth($objop->fin_diagnostic);
                            if($length < $this->max_space){
                              $this->Cell($this->max_space, $this->h_cell, $objop->fin_diagnostic, 0, 0,'L');
                            }else{
                              $this->MultiCell($this->max_space, $this->h_multi, $objop->fin_diagnostic, 0, 0, 'L');
                            } 
                        //condition on discharge
                        $this->SetXY($this->x1, $this->y1+179);
                        $length = $this->GetStringWidth($objop->cond_discharge);
                            if($length < $this->max_space){
                              $this->Cell($this->max_space, $this->h_cell, $objop->cond_discharge, 0, 0, 'L');
                            }else{
                              $this->MultiCell($this->max_space, $this->h_multi, $objop->cond_discharge,0,0,'L');
                            }
                        
                  }
          }

  }

  function addSignature($title, $patient, $mark, $witness){
  
      $this->Rect($this->x1, $this->y1+209, $this->tot_width, 50);
      //title
      $this->SetXY($this->x1, $this->y1+210);
      //$this->SetFont('Arial', 'B', 9);
      $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
      $length = $this->GetStringWidth($title);
      $this->Cell($length, $this->h_cell, $title);
      //patient
      $this->Line($this->x1+4, $this->y1+224, $this->x1+100, $this->y1+224);
      $this->SetXY($this->x1+4, $this->y1+225);
      $length = $this->GetStringWidth($patient);
      $this->Cell($length, $this->h_cell, $patient);
      //thumbmark
      $this->Rect($this->x1+30, $this->y1+228, 35, 15);
      $this->SetXY($this->x1+30, $this->y1+245);
      $length = $this->GetStringWidth($mark);
      $this->Cell($length, $this->h_cell, $mark);
      $this->SetXY($this->x1+10, $this->y1+249);
      $str = "(In case patient and representative could not write)";
      $length = $this->GetStringWidth($str);
      $this->Cell($length, $this->h_cell, $str);
      //witness
      $this->Line($this->x1+110, $this->y1+248, $this->x1+190, $this->y1+248);
      $this->SetXY($this->x1+110, $this->y1+249);
      $length = $this->GetStringWidth($witness);
      $this->Cell($length, $this->h_cell, $witness);
  }
  
}

    class Case_AccNum{
      var $casenum;
      var $admission_dt;
      var $admission_tm;
      var $accnum;
    }
    
    class PatientRecord{
      var $pat_lname;
      var $pat_fname;
      var $pat_mname;
      var $pat_age;
      var $pat_sex;
      var $adofficer;
      var $admit_diagnosis;
      var $chief_complaint;
      var $admit_reason;
      var $ob_history;      
    }
    
    class PhysicalExam{
      var $gensurv;
      var $bp;
      var $hr;
      var $rr;
      var $temp;
      var $heent;
      var $chest;
      var $cvs;
      var $abdomen;
      var $gu;
      var $skin;
      var $neuro_exam;      
    }
    
    class CourseWard{
      var $course_in_wards;
    }
    
    class Findings{
      var $lab_diagnostic;
    }
    
    class SurgicalOp{
      var $surg_date;
      var $surg_time;
      var $surg_name;
      var $fin_diagnostic;
      var $cond_discharge;
    }
    
    

$pdf = new PhilhealthForm3();
$pdf->Open(); 
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->addHead("PHILHEALTH", 
        "CLAIM FORM 3",
        "Revised May 2000",
        "Note: This form together with Claim Form 2 should be filed with PhilHealth within 60 calendar days from date of"
         ."discharge", 
         "PATIENT'S CLINICAL RECORD");
$pdf->addDate("DATE RECEIVED");
$pdf->addCaseAcc("Case No.:",
        "Admission:",
        "Date:",
        "Time:",
        "Accredation No.:");
$pdf->addHospitalName("Name of Hospital/Ambulatory Clinic");
$pdf->addHospitalAddress("Address of Hospital/Ambulatory Clinic:",
            "No., Street",
            "Municipality/City",
            "Barangay",
            "Province",
            "Zip Code");
$pdf->addPatientRecord("PATIENT'S CLINICAL RECORD",
             "1. Patient's Name",
             "Last Name",
             "First Name",
             "Middle Name",
             "2. Age",
             "3. Sex");
$pdf->addPatientSignature("Printed Name & Signature of Admitting Officer");             
$pdf->addDiagnosis("5. Admitting Diagnosis:");
$pdf->addComplaint("6. Chief Complaint:");
$pdf->addAdmission("7. Reason for Admission");
$pdf->addPresentIllness("8. Brief History of Present Illness/OB History:");
$pdf->addPhysicalExam("9. Physical Examination (Pertinent Findings per System)",
            "General Survey:",
            "Vital Signs:",
            "HEENT:",
            "Chest/Lungs:",
            "CVS:",
            "Abdomen:",
            "GU (IE):",
            "Skin/Extremities:",
            "Neuro Examination:");
$pdf->addPage();
$pdf->addCourse("10. Course in the Wards:");
$pdf->addFindings("11. Pertinent Laboratory and Pertinent Diagnostic Findings:".
          " (CBC, Urinalysis, Fecalysis, X-ray, Biopsy, etc.)");
$pdf->addOperation("12. Surgical Operation:",
           "a. Date:",
           "b. Time:",
           "Printed Name & Signature of Surgeon",
           "c. Final Diagnostics:",
           "d. Condition on Discharge:",
           "e. Signature of Attending Physician:");
$pdf->addSignature("13. Signature or Right Thumb Mark of Patient or His/Her Representative:",
              "Printed Name & Signature of Patient or His/Her Representative",
              "   Right thumb mark",
              "Printed Name & Signature of Witness to Thumb Mark");

$pdf->Output();

?>
