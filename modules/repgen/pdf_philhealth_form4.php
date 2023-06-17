<?php
   error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require($root_path.'/modules/repgen/fpdf.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$x1 = 5;
$y1 = 12;

//define('FPDF_FONTPATH','/xampp/htdocs/xampp/font/');
//require('fpdf.php');

class PhilhealthForm4 extends FPDF{
  var $x1 =5;
  var $y1 =8;
  var $tot_width = 206;
  var $col2 = 100;
  var $col3 = 160;
  var $phealth_col = 166;
  var $acc_array;       // Array of Class Accreditation
  var $hospadd_array;   // Array of Class Hosp_Address
  var $meminfo_array;   // Array of Class MemberInfo
  var $memadd_array;    // Array of Class MemberAddress
  var $patinfo_array;   // Array of Class PatientInfo
  var $conf_array;      // Array of Class Confinement
  var $serv_array;      // Array of Class FacilityServ
  var $diag_array;      // Array of Class Diagnosis
  var $prov_array;      // Array of Class Provider 
  var $fontfamily_label = "Arial";
  var $fontstyle_label = "B";
  var $fontsize_label = 9;
  var $fontfamily_ans = "Arial";
  var $fontstyle_ans = '';
  var $fontsize_ans = 9;
  var $fontsize_philhealth = 18;
  var $fontsize_claim = 12;
  var $fontsize_revised_date = 10;
  var $fontsize_note = 8;
  var $fontsize_mat = 14;
  var $fontsize_part = 10;
  var $fontsize_cert = 8.5;
  var $fontsize_philuse = 10;
  var $max_space = 80;
  var $diag_space = 132;
  var $boxwidth = 3;
  var $boxheight = 2.5;
  var $h_cell = 2;
  var $h_multi = 3;
  
  function PhilhealthForm4(){
    
   $this->FPDF('P', 'mm', 'letter');
   $this->SetDrawColor(0,0,0);
   $this->SetMargins(5,2,1);

  }
  
  function AddHead($name, $formnum, $revised, $note, $mat){
    
    $this->Rect($this->x1, $this->y1 - 2, $this->col3, 18);
    $this->SetXY( $this->x1, $this->y1 );
    $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_philhealth);
    $length = $this->GetStringWidth( $name );
    $this->Cell( $length, 2, $name);
    $this->SetXY($this->x1, $this->y1 + 4);
    $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_claim);
    $length = $this->GetStringWidth($formnum);
    $this->Cell($length, 4, $formnum);
    $this->SetXY($this->x1, $this->y1 + 8);
    $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_revised_date);
    $length = $this->GetStringWidth($revised);
    $this->Cell($length, 4, $revised);
    $this->SetXY($this->x1, $this->y1 + 12);
    $this->SetFont($this->fontfamily_label, $this->fontstyle_ans, $this->fontsize_note);
    $length = $this->GetStringWidth($note);
    $this->Cell($length, 4, $note);
    $this->SetXY($this->x1+70, $this->y1);
    $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_mat);
    $length = $this->GetStringWidth($mat);
    $this->MultiCell($length, 5, $mat);       
  }
  
  function AddDateReceived($date){
    $indent = 5;
    
    $this->Rect($this->x1+ $this->col3, $this->y1-2, 46, 18);
    $this->SetXY($this->x1+ ($this->col3 + $indent), $this->y1);
    $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_revised_date);
    $length = $this->GetStringWidth($date);
    $this->Cell($length, 4, $date);
  }
  function AddTitleHeaderI($title){
    global $x1, $y1;
    
    $this->SetXY($this->x1+40, $this->y1+16);
    $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_part);
    $length = $this->GetStringWidth($title);
    $this->Cell($length, 4, $title);
  }
  
  function AddAccreditation($accnum, $category, $primary, $secondary, $tertiary, $nonhospital){
    global $x1, $y1;

    $ans = "X";
    $noans = " ";
    $height = 21;
    $this->Rect($this->x1, $this->y1+20, $this->col2, 10);
    //Accreditation Number
    $this->SetXY($this->x1, $this->y1+$height);
    $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
    $length = $this->GetStringWidth($accnum);
    $this->Cell($length, $this->h_cell, $accnum);
    $l_accnum = $length + 2;
    //2. Accreditation Category
    $this->Rect($this->x1+$this->col2,$this->y1+20,106,10);
    $this->SetX($this->x1+$this->col2);
    $length = $this->GetStringWidth($category);
    $this->Cell($length, $this->h_cell, $category);
    $l_acc_cat = $length + 2;
   
      if(!empty($this->acc_array)){
        foreach($this->acc_array as $objacc){
          $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
          $this->SetXY($this->x1 + $l_accnum, $this->y1+$height);
          $length = $this->GetStringWidth($objacc->accnum);
          $this->Cell($length, $this->h_cell, $objacc->accnum,0,0,'L');
          
            if($objacc->category == "PH"){
               //Primary
                $this->SetXY($this->x1+141, $this->y1+$height);
                //$length = $this->GetStringWidth($ans);
                $this->Cell($this->boxwidth, $this->boxheight, $ans,1,0,'C');
                $this->SetX($this->x1+145);
                $length = $this->GetStringWidth($primary);
                $this->Cell($length, $this->h_cell, $primary);
                //Secondary
                $this->SetX($this->x1+161);
                //$length = $this->GetStringWidth($ans);
                $this->Cell($this->boxwidth, $this->boxheight, $noans, 1,0,'C');
                $this->SetX($this->x1+165);
                $length = $this->GetStringWidth($secondary);
                $this->Cell($length, $this->h_cell, $secondary);
                //Tertiary
                $this->SetX($this->x1+185);
                //$length = $this->GetStringWidth($ans);
                $this->Cell($this->boxwidth, $this->boxheight, $noans, 1, 0,'C');
                $this->SetX($this->x1+190);
                $length = $this->GetStringWidth($tertiary);
                $this->Cell($length, $this->h_cell, $tertiary);
                //Non Hospital Facilities
                $this->SetXY($this->x1+103, $this->y1+24);
                //$length = $this->GetStringWidth($ans);
                $this->Cell($this->boxwidth, $this->boxheight, $noans, 1,0);
                $this->SetXY($this->x1+106, $this->y1+24);
                $length = $this->GetStringWidth($nonhospital);
                $this->MultiCell($length, $this->h_multi, $nonhospital);
            }
            else if($objacc->category == "SH"){
                //Primary
                $this->SetXY($this->x1+141, $this->y1+$height);
                $this->Cell($this->boxwidth, $this->boxheight, $noans,1,0,'C');
                $this->SetX($this->x1+145);
                $length = $this->GetStringWidth($primary);
                $this->Cell($length, $this->h_cell, $primary);
                //Secondary
                $this->SetX($this->x1+161);
                $this->Cell($this->boxwidth, $this->boxheight, $ans, 1,0,'C');
                $this->SetX($this->x1+165);
                $length = $this->GetStringWidth($secondary);
                $this->Cell($length, $this->h_cell, $secondary);
                //Tertiary
                $this->SetX($this->x1+185);
                $this->Cell($this->boxwidth, $this->boxheight, $noans, 1, 0,'C');
                $this->SetX($this->x1+190);
                $length = $this->GetStringWidth($tertiary);
                $this->Cell($length, $this->h_cell, $tertiary);
                //Non Hospital Facilities
                $this->SetXY($this->x1+103, $this->y1+24);
                $this->Cell($this->boxwidth, $this->boxheight, $noans, 1,0);
                $this->SetXY($this->x1+106, $this->y1+24);
                $length = $this->GetStringWidth($nonhospital);
                $this->MultiCell($length, $this->h_multi, $nonhospital);
            }
            else if($objacc->category == "TH"){
                //Primary
                $this->SetXY($this->x1+141, $this->y1+$height);
                $this->Cell($this->boxwidth, $this->boxheight, $noans,1,0,'C');
                $this->SetX($this->x1+145);
                $length = $this->GetStringWidth($primary);
                $this->Cell($length, $this->h_cell, $primary);
                //Secondary
                $this->SetX($this->x1+161);
                $this->Cell($this->boxwidth, $this->boxheight, $noans, 1,0,'C');
                $this->SetX($this->x1+165);
                $length = $this->GetStringWidth($secondary);
                $this->Cell($length, $this->h_cell, $secondary);
                //Tertiary
                $this->SetX($this->x1+185);
                $this->Cell($this->boxwidth, $this->boxheight, $ans, 1, 0,'C');
                $this->SetX($this->x1+190);
                $length = $this->GetStringWidth($tertiary);
                $this->Cell($length, $this->h_cell, $tertiary);
                //Non Hospital Facilities
                $this->SetXY($this->x1+103, $this->y1+24);
                $this->Cell($this->boxwidth, $this->boxheight, $noans, 1,0);
                $this->SetXY($this->x1+106, $this->y1+24);
                $length = $this->GetStringWidth($nonhospital);
                $this->MultiCell($length, $this->h_multi, $nonhospital);
            }
            else{
                //Primary
                $this->SetXY($this->x1+141, $this->y1+$height);
                $this->Cell($this->boxwidth, $this->boxheight, $noans,1,0,'C');
                $this->SetX($this->x1+145);
                $length = $this->GetStringWidth($primary);
                $this->Cell($length, $this->h_cell, $primary);
                //Secondary
                $this->SetX($this->x1+161);
                $this->Cell($this->boxwidth, $this->boxheight, $noans, 1,0,'C');
                $this->SetX($this->x1+165);
                $length = $this->GetStringWidth($secondary);
                $this->Cell($length, $this->h_cell, $secondary);
                //Tertiary
                $this->SetX($this->x1+185);
                $this->Cell($this->boxwidth, $this->boxheight, $noans, 1, 0,'C');
                $this->SetX($this->x1+190);
                $length = $this->GetStringWidth($tertiary);
                $this->Cell($length, $this->h_cell, $tertiary);
                //Non Hospital Facilities
                $this->SetXY($this->x1+103, $this->y1+24);
                $this->Cell($this->boxwidth, $this->boxheight, $ans, 1,0);
                $this->SetXY($this->x1+106, $this->y1+24);
                $length = $this->GetStringWidth($nonhospital);
                $this->MultiCell($length, $this->h_multi, $nonhospital);
            }
        
        }
      }
  
  }
  
  function AddNameFacility($name){
         global $db;

     $this->Rect($this->x1, $this->y1+30, $this->tot_width, 8);
     //name
     $this->SetXY($this->x1, $this->y1+31);
     $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
     $length = $this->GetStringWidth($name);
     $this->Cell($length, $this->h_cell, $name);
     $this->Ln(4);
     $objInfo = new Hospital_Admin();     
        if ($row = $objInfo->getAllHospitalInfo()) {      
          $row['hosp_name']   = strtoupper($row['hosp_name']);
        }
     $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
     $this->Cell($this->tot_width, $this->h_cell, $row['hosp_name']);
  }
  
  function AddAddressFacility($title, $street, $city, $barangay, $province, $code){
         global $db;
  
     $height = 39;
     $this->Rect($this->x1, $this->y1+38, $this->tot_width, 20);
     //title
     $this->SetXY($this->x1, $this->y1+$height); //39
     $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
     $length = $this->GetStringWidth($title);
     $this->Cell($length, $this->h_cell, $title);
     $height = $height + 3;
     //street
     $this->SetXY($this->x1+3, $this->y1+$height);  //42
     $length = $this->GetStringWidth($street);
     $this->Cell($length, $this->h_cell, $street);
     //barangay
     $this->SetXY($this->x1+$this->col2, $this->y1+$height);
     $length = $this->GetStringWidth($barangay);
     $this->Cell($length, $this->h_cell, $barangay);
     $height1 = $height + 8;
     //city
     $this->SetXY($this->x1+3, $this->y1+$height1);   //50
     $length = $this->GetStringWidth($city);
     $this->Cell($length, $this->h_cell, $city);
    //province
     $this->SetXY($this->x1+$this->col2, $this->y1+$height1);
     $length = $this->GetStringWidth($province);
     $this->Cell($length, $this->h_cell, $province);
    //code
     $this->SetXY($this->x1+180, $this->y1+$height1);
     $length = $this->GetStringWidth($code);
     $this->Cell($length, $this->h_cell, $code);
     
      if(!empty($this->hospadd_array)){
        foreach($this->hospadd_array as $objadd){
          $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
          //Street
          $this->SetXY($this->x1+3, $this->y1+($height + 4));
          $length = $this->GetStringWidth($objadd->h_street);
          $this->Cell($length, $this->h_cell, $objadd->h_street,0,0,'L');
          //Barangay
          $this->SetX($this->x1+$this->col2);
          $length = $this->GetStringWidth($objadd->h_brgy);
          $this->Cell($length, $this->h_cell, $objadd->h_brgy,0,0,'L');
          //City
          $this->SetXY($this->x1+3, $this->y1 + ($height1 + 4));
          $length = $this->GetStringWidth($objadd->h_city);
          $this->Cell($length, $this->h_cell, $objadd->h_city,0,0,'L');
          //Province
          $this->SetX($this->x1 + $this->col2);
          $length = $this->GetStringWidth($objadd->h_province);
          $this->Cell($length, $this->h_cell, $objadd->h_province,0,0,'L');
          //Zip Code
          $this->SetX($this->x1+180);
          $length = $this->GetStringWidth($objadd->h_zipcode);
          $this->Cell($length, $this->h_cell, $objadd->h_zipcode,0,0,'L');
        }
      }
  
  }
  
  function AddMemNameAndId($title, $lname, $fname, $mname, $idnum){
  
     $height = 59;
     $this->Rect($this->x1, $this->y1+58, $this->tot_width, 20);
     //title
     $this->SetXY($this->x1, $this->y1+$height);      //59
     $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
     $length = $this->GetStringWidth($title);
     $this->Cell($length, $this->h_cell, $title);
     $height = $height + 3;
     // last name
     $this->SetXY($this->x1+3, $this->y1+$height);   //62
     $length = $this->GetStringWidth($lname);
     $this->Cell($length, $this->h_cell, $lname);
     // first name
     $this->SetXY($this->x1+$this->col2, $this->y1+$height);
     $length = $this->GetStringWidth($fname);
     $this->Cell($length, $this->h_cell, $fname);
     $height1 = $height + 8;
     // middle name
     $this->SetXY($this->x1+3, $this->y1+$height1);   //70
     $length = $this->GetStringWidth($mname);
     $this->Cell($length, $this->h_cell, $mname);
     // Identification number
     $this->SetXY($this->x1+$this->col2, $this->y1+$height1);
     $length = $this->GetStringWidth($idnum);
     $this->MultiCell($length, $this->h_multi, $idnum);
     
        if(!empty($this->meminfo_array)){
          foreach($this->meminfo_array as $objinfo){
            $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
            //Last Name
            $this->SetXY($this->x1 + 3, $this->y1+($height + 4));
            $length = $this->GetStringWidth($objinfo->m_lname);
            $this->Cell($length, $this->h_cell, $objinfo->m_lname,0,0,'L');
            //First Name
            $this->SetX($this->x1 + $this->col2, $this->y1+($height + 4));
            $length = $this->GetStringWidth($objinfo->m_fname);
            $this->Cell($length, $this->h_cell, $objinfo->m_fname,0,0,'L');
            //Middle Name
            $this->SetXY($this->x1 + 3, $this->y1 + ($height1 + 4));
            $length = $this->GetStringWidth($objinfo->m_mname);
            $this->Cell($length, $this->h_cell, $objinfo->m_mname,0,0,'L');
            //Identification No.
            $this->SetX($this->x1 + $this->col2, $this->y1 + ($height1 + 4));
            $length = $this->GetStringWidth($objinfo->m_idnum);
            $this->Cell($length, $this->h_cell, $objinfo->m_idnum,0,0,'L');
          }
        }
  }
  
  function AddMemAddress($title, $street, $city, $barangay, $province, $code){

     $height = 79;
     $this->Rect($this->x1, $this->y1+78, $this->tot_width, 20);
     //title
     $this->SetXY($this->x1, $this->y1+$height);   //79
     $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
     $length = $this->GetStringWidth($title);
     $this->Cell($length, $this->h_cell, $title);
     $height = $height + 3;
     //street
     $this->SetXY($this->x1+3, $this->y1+$height);   //82
     $length = $this->GetStringWidth($street);
     $this->Cell($length, $this->h_cell, $street);
     //barangay
     $this->SetXY($this->x1+$this->col2, $this->y1+$height);
     $length = $this->GetStringWidth($barangay);
     $this->Cell($length, $this->h_cell, $barangay);
     $height1 = $height + 8;
     //city
     $this->SetXY($this->x1+3, $this->y1+$height1);   //90
     $length = $this->GetStringWidth($city);
     $this->Cell($length, $this->h_cell, $city);  
     //province
     $this->SetXY($this->x1+$this->col2, $this->y1+$height1);
     $length = $this->GetStringWidth($province);
     $this->Cell($length, $this->h_cell, $province);
     //code
     $this->SetXY($this->x1+180, $this->y1+$height1);
     $length = $this->GetStringWidth($code);
     $this->Cell($length, $this->h_cell, $code); 
     
         if(!empty($this->memadd_array)){
            foreach($this->memadd_array as $objadd){
              $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
              //Street
              $this->SetXY($this->x1 + 3, $this->y1 + ($height + 4));
              $length = $this->GetStringWidth($objadd->m_street);
              $this->Cell($length, $this->h_cell, $objadd->m_street,0,0,'L');
              //Barangay
              $this->SetX($this->x1 + $this->col2);
              $length = $this->GetStringWidth($objadd->m_brgy);
              $this->Cell($length, $this->h_cell, $objadd->m_brgy,0,0,'L');
              //City
              $this->SetXY($this->x1 + 3, $this->y1 + ($height1 + 4));
              $length = $this->GetStringWidth($objadd->m_city);
              $this->Cell($length, $this->h_cell, $objadd->m_city,0,0,'L');
              //Province
              $this->SetXY($this->x1 + $this->col2);
              $length = $this->GetStringWidth($objadd->m_province);
              $this->Cell($length, $this->h_cell, $objadd->m_province,0,0,'L');
              //Zip Code
              $this->SetX($this->x1 + 180);
              $length = $this->GetStringWidth($objadd->m_zipcode);
              $this->Cell($length, $this->h_cell, $objadd->m_zipcode,0,0,'L');
            }
         }    
  }
  
  function AddPatientName($title, $lname, $fname, $mname, $age, $diagnosis){

     $height = 99;
     $this->Rect($this->x1, $this->y1+98, $this->col2, 28);
     //title
     $this->SetXY($this->x1, $this->y1+$height);   //99
     $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
     $length = $this->GetStringWidth($title);
     $this->Cell($length, $this->h_cell, $title);
     // age
     $this->Rect($this->x1+$this->col2, $this->y1+98, 20, 28);
     $this->SetXY($this->x1+$this->col2, $this->y1+$height);
     $length = $this->GetStringWidth($age);
     $this->Cell($length, $this->h_cell, $age);
     //admission diagnosis
     $this->Rect($this->x1+120, $this->y1+98, 86, 28);
     $this->SetXY($this->x1+120, $this->y1+$height);
     $length = $this->GetStringWidth($diagnosis);
     $this->Cell($length, $this->h_cell, $diagnosis);
     $height1 = $height + 3;
     //last name
     $this->SetXY($this->x1+3, $this->y1+$height1);    //102
     $length = $this->GetStringWidth($lname);
     $this->Cell($length, $this->h_cell, $lname);
     $height2 = $height1 + 8;
     //first name
     $this->SetXY($this->x1+3, $this->y1+$height2);   //110
     $length = $this->GetStringWidth($fname);
     $this->Cell($length, $this->h_cell, $fname);
     $height3 = $height2 + 8;
     //middle name
     $this->SetXY($this->x1+3, $this->y1+$height3);   //118
     $length = $this->GetStringWidth($mname);
     $this->Cell($length, $this->h_cell, $mname);
     
     
        if(!empty($this->patinfo_array)){
            foreach($this->patinfo_array as $objpat){
              $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
              //Last Name
              $this->SetXY($this->x1 + 3, $this->y1+ ($height1 + 4));
              $length = $this->GetStringWidth($objpat->p_lname);
              $this->Cell($length, $this->h_cell, $objpat->p_lname, 0,0,'L');
              //Age
              $this->SetX($this->x1+ 108);
              $length = $this->GetStringWidth($objpat->p_age);
              $this->Cell($length, $this->h_cell, $objpat->p_age,0,0,'L');
              //Admission Diagnosis
              $this->SetX($this->x1 + 120);
              $length = $this->GetStringWidth($objpat->p_diagnosis);
                if($length <= $this->max_space){
                  $this->Cell($this->max_space, $this->h_cell, $objpat->p_diagnosis,0,0,'L');
                }
                else{
                  $this->MultiCell($this->max_space, $this->h_multi, $objpat->p_diagnosis,0,0,'L');
                }
              //First Name
              $this->SetXY($this->x1 + 3, $this->y1 + ($height2 + 4));
              $length = $this->GetStringWidth($objpat->p_fname);
              $this->Cell($length, $this->h_cell, $objpat->p_fname, 0,0,'L');
              //Middle Name
              $this->SetXY($this->x1 + 3, $this->y1 + ($height3 + 4));
              $length = $this->GetStringWidth($objpat->p_mname);
              $this->Cell($length, $this->h_cell, $objpat->p_mname,0,0,'L');  
            }
        }
     
  }
  
  function addCols( $tab )
{
    global $colonnes;
    
    $r1  = 10;
    $r2  = $this->w - ($r1 * 2) ;
    $y1  = 100;
    $y2  = $this->h - 50 - $y1;
    $this->SetXY( $r1, $y1 );
    $this->Rect( $r1, $y1, $r2, $y2, "D");
    $this->Line( $r1, $y1+6, $r1+$r2, $y1+6);
    $colX = $r1;
    $colonnes = $tab;
    while ( list( $lib, $pos ) = each ($tab) )
    {
        $this->SetXY( $colX, $y1+2 );
        $this->Cell( $pos, 1, $lib, 0, 0, "L");
        $colX += $pos;
        $this->Line( $colX, $y1, $colX, $y1+$y2);
    }
}

  function addCols1($tab){
    global $colonnes;
  $x1 = 5;
  $y1 = 8;
  $this->SetXY($x1, $y1+141);
  $this->Rect($x1, $y1+141, 206, 30);
  $this->Line($x1, $y1+147, 206, $y1+147);
  }

  
  function AddConfinementPeriod($title, $date_ad, $date_disch, $total_days, $date_d){
        
     $height = 127;
     $this->Rect($this->x1, $this->y1+126, $this->tot_width, 15);
     //title
     $this->SetXY($this->x1, $this->y1+$height);   //127
     $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
     $length = $this->GetStringWidth($title);
     $this->Cell($length, $this->h_cell, $title);
     $height = $height + 3;
     //a. Date Admitted
     $this->SetXY($this->x1+5, $this->y1+$height);   //130
   $length = $this->GetStringWidth($date_ad);
   $this->Cell($length, $this->h_cell, $date_ad);
   $l_admit = $length + 2;
   //b. Date Discharged
   $this->SetXY($this->x1+80, $this->y1+$height);
   $length = $this->GetStringWidth($date_disch);
   $this->Cell($length, $this->h_cell, $date_disch);
   $l_disch = $length + 2;
   $height1 = $height + 4;
   //c. Total No. of Days
   $this->SetXY($this->x1+120, $this->y1+$height1);      //134
   $length = $this->GetStringWidth($total_days);
   $this->Cell($length, $this->h_cell, $total_days);
   $height2 = $height1 + 4;
   $l_totnum = $length + 2;
   //d. Date of Death
   $this->SetXY($this->x1+120, $this->y1+$height2);     //138
   $length = $this->GetStringWidth($date_d);
   $this->Cell($length, $this->h_cell, $date_d);
   $l_death = $length + 2; 
      
      if(!empty($this->conf_array)){
            foreach($this->conf_array as $objconf){
               $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
               //Date Admitted
               $this->SetXY($this->x1 + ($l_admit + 5), $this->y1 + $height);
               $length = $this->GetStringWidth($objconf->admit_dt);
               $this->Cell($length, $this->h_cell, $objconf->admit_dt,0,0,'L');
               //Date Discharged
               $this->SetX($this->x1+ ($l_disch + 80));
               $length = $this->GetStringWidth($objconf->disch_dt);
               $this->Cell($length, $this->h_cell, $objconf->disch_dt,0,0,'L');
               //Total No. of Days
               $this->SetXY($this->x1 + ($l_totnum + 120), $this->y1 + $height1);
               $length = $this->GetStringWidth($objconf->totnum_days);
               $this->Cell($length, $this->h_cell, $objconf->totnum_days,0,0,'L');
               //Date of Death
               $this->SetXY($this->x1 + ($l_death + 120), $this->y1 + $height2);
               $length = $this->GetStringWidth($objconf->death_dt);
               $this->Cell($length, $this->h_cell, $objconf->death_dt,0,0,'L');
            }
      }
  }
  
  function AddFacilityServ($title, $tot, $med){
  
  $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
  $this->Rect($this->x1, $this->y1+141, 65, 19);
  //Facility Services
  $this->SetXY($this->x1, $this->y1+142);
  $length = $this->GetStringWidth($title);
  $this->Cell($length, $this->h_cell, $title);
  //Medicines
  $this->SetFontSize(8);
  $this->SetXY($this->x1, $this->y1+154);
  $length = $this->GetStringWidth($med);
  $this->MultiCell($length, $this->h_multi, $med); 
  //Total
  //$this->SetFont('Arial', 'B', 9);
  $this->SetXY($this->x1+52, $this->y1+150);
  $length = $this->GetStringWidth($tot);
  $this->Cell($length, $this->h_cell, $tot);

  //Table
 // $this->Rect($x1+65, $y1+141, 141, 19);
 //$this->ColumnWidth = array(30, 20, 10, 10, 10);
    //$this->RowHeight = 5.5;
    //$this->Alignment = array('C', 'C', 'C', 'C', 'C');
    //$l_length = $this->GetStringWidth("ACTUAL FACILITY");
   
   /* $this->Rect($this->x1+65, $this->y1+141, $l_length+15, 7);  
    $this->Rect($this->x1+65, $this->y1+141, 46, 7);
    $this->SetXY($this->x1+69, $this->y1+142);
    $this->Cell($l_length,3," ACTUAL FACILITY",0,0);
    $this->SetXY($this->x1+75, $this->y1+145);
    $this->Cell(10, 3,"CHARGES",0,0);
    $this->Rect($this->x1+108.5, $this->y1+141, 70, 3); 
    $length = $this->GetStringWidth("PHIC BENEFIT PAYABLE TO");
    $this->SetXY($this->x1+120, $this->y1+141);
    $this->Cell($length, 3,'PHIC BENEFIT PAYABLE TO',0,0,'C');
    $this->Rect($this->x1+108.5, $this->y1+144, 35, 4);
    $this->SetXY($this->x1+120, $this->y1+145);
    $length = $this->GetStringWidth("FACILITY");
    $this->Cell($length, 3.1,'FACILITY',0,0,'C');
    $this->Rect($this->x1+143.5, $this->y1+144, 35, 4);
    $length = $this->GetStringWidth("PATIENT");
    $this->SetXY($this->x1+154, $this->y1+145);
    $this->Cell($length, 2,'PATIENT',0,0,'C');
    $this->Rect($this->x1+178.5, $this->y1+141, 27.5, 7);
    $length = $this->GetStringWidth("REDUCTION");
    $this->SetXY($this->x1+182, $this->y1+144);
    $this->Cell($length , 2,"REDUCTION",0,0,'C');
    //$this->Line($this->x1+65, $this->y1+154.5, $this->x1+206, $this->y1+154.5);
    //$this->Line($this->x1+206, $this->y1+148, $this->x1+206, $this->y1+160);
    //Table Row
    $this->SetXY($this->x1+65, $this->y1+148);
    $this->Cell($l_length+15, 6, " ",1,0);
    $this->Cell();  */
    
     $this->ColumnWidth = array(38, 34, 34, 35); 
     
     $header = 2;
     $row = 6;
     $t_height_start = 141;
     $h_width_start = 65;
    //TABLE
       $this->SetXY($this->x1+$h_width_start, $this->y1+142);
       $length = $this->GetStringWidth("ACTUAL FACILITY");
       $this->Cell($this->ColumnWidth[0], $header, "ACTUAL FACILITY", 0, 0, 'C');
       $this->Cell($this->ColumnWidth[1] + $this->ColumnWidth[2], $header,"PHIC BENEFIT PAYABLE TO", 0, 0, 'C');
       $this->Cell($this->ColumnWidth[3], $header, "REDUCTION CODE", 0, 0, 'C');
       $this->SetXY($this->x1+$h_width_start, $this->y1+145);
       $this->Cell($this->ColumnWidth[0], $header, "CHARGES", 0, 0 ,'C');
       $this->Cell($this->ColumnWidth[1], $header, "FACILITY", 0, 0, 'C');
       $this->Cell($this->ColumnWidth[2], $header, "PATIENT", 0, 0, 'C');
       $this->Rect($this->x1+$h_width_start, $this->y1+ $t_height_start, $this->ColumnWidth[0], 7);   //65
       $h_width_start = $h_width_start + $this->ColumnWidth[0];
       $this->Rect($this->x1+$h_width_start, $this->y1+ $t_height_start, $this->ColumnWidth[1] + $this->ColumnWidth[2], 3); //103
       $this->Rect($this->x1+$h_width_start, $this->y1+ ($t_height_start + 3), $this->ColumnWidth[1], 4);
       $h_width_start = $h_width_start + $this->ColumnWidth[1];
       $this->Rect($this->x1+ $h_width_start, $this->y1+ ($t_height_start + 3), $this->ColumnWidth[2], 4);  //137
       $h_width_start = $h_width_start + $this->ColumnWidth[2];
       //print_r($h_width_start);
       $this->Rect($this->x1+$h_width_start, $this->y1+$t_height_start, $this->ColumnWidth[3], 7);         //171
  
       //Total
       if(!empty($this->serv_array)){
              foreach($this->serv_array as $objserv){
                $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
                //Row
                $this->SetXY($this->x1+65, $this->x1+151);
                $this->Cell($this->ColumnWidth[0], $row, $objserv->charges,1,0,'C');
                $this->Cell($this->ColumnWidth[1], $row, $objserv->facility,1,0,'C');
                $this->Cell($this->ColumnWidth[2], $row, $objserv->patient,1,0,'C');
                $this->Cell($this->ColumnWidth[3], $row, $objserv->reduction,1,0,'C');
              }
       }
       
       //Medicines & Supplies
       if(!empty($this->serv_array)){
              foreach($this->serv_array as $objserv){
                $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
                //Row
                $this->SetXY($this->x1+65, $this->x1+157);
                $this->Cell($this->ColumnWidth[0], $row, $objserv->charges,1,0,'C');
                $this->Cell($this->ColumnWidth[1], $row, $objserv->facility,1,0,'C');
                $this->Cell($this->ColumnWidth[2], $row, $objserv->patient,1,0,'C');
                $this->Cell($this->ColumnWidth[3], $row, $objserv->reduction,1,0,'C');
              }
       }
  }

    function AddCertification($cert, $sig, $date, $cap){

    $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_cert);
    $this->Rect($this->x1, $this->y1+160, $this->tot_width, 19);
    // Certification of Facility
    $this->SetXY($this->x1, $this->y1+161);
    $length = $this->GetStringWidth($cert);
    $this->MultiCell($length, $this->h_multi, $cert);
    //Signature
    $this->Line($this->x1+10, $this->y1+175, $this->x1+95, $this->y1+175);
    $this->SetXY($this->x1+10, $this->y1+176);
    $length = $this->GetStringWidth($sig);
    $this->Cell($length, $this->h_cell, $sig);
    // Date Signed
    $this->Line($this->x1+120, $this->y1+175, $this->x1+140, $this->y1+175);
    $this->SetXY($this->x1+120, $this->y1+176);
    $length = $this->GetStringWidth($date);
    $this->Cell($length, $this->h_cell, $date);
    //Official Capacity
    $this->Line($this->x1+170, $this->y1+175, $this->x1+195, $this->y1+175);
    $this->SetXY($this->x1+170, $this->y1+176);
    $length = $this->GetStringWidth($cap);
    $this->Cell($length, $this->h_cell, $cap);
    }

    function AddPart2($pt2){
    global $x1, $y1;
    $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_part);
    //Part II
    $this->SetXY($this->x1+35, $this->y1+180);
    $length = $this->GetStringWidth($pt2);
    $this->Cell($length, $this->h_cell, $pt2);
    }
  
  function AddFinalDiagnosis($fin, $code){
  
  $height = 185;
  $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
  $this->Rect($this->x1, $this->y1+184, 166, 25);
  //Final Diagnosis
  $this->SetXY($this->x1, $this->y1+$height);
  $length = $this->GetStringWidth($fin);
  $this->Cell($length, $this->h_cell, $fin);
  //ICD-10 Code
  $this->SetXY($this->x1+135, $this->y1+$height);
  $this->Line($this->x1+135, $this->y1+184, $this->x1+135, $this->y1+209);
  $length = $this->GetStringWidth($code);
  $this->Cell($length, $this->h_cell, $code);
  $height1 = $height + 4;
  
      if(!empty($this->diag_array)){
              foreach($this->diag_array as $objdiag){
                $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
                //Final Diagnosis
                $this->SetXY($this->x1, $this->y1 + $height1);
                $length = $this->GetStringWidth($objdiag->fin_diag);
                  if($length <= $this->diag_space){
                    $this->Cell($this->diag_space, $this->h_cell, $objdiag->fin_diag,0,0,'L');
                  }
                  else{
                    $this->MultiCell($this->diag_space, $this->h_multi, $objdiag->fin_diag,0,0,'L');
                  }
                //ICD-10 Code
                $this->SetX($this->x1+135);
                $length = $this->GetStringWidth($objdiag->icd_code);
                $this->Cell($length, $this->h_cell, $objdiag->icd_code,0,0,'L');
                
              }
      }
  }
  
  function AddProvider($name, $sig){
 
  $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
  $this->Rect($this->x1, $this->y1+209, $this->phealth_col, 10);
  //Name of Provider
  $this->SetXY($this->x1, $this->y1+210);
  $length = $this->GetStringWidth($name);
  $this->Cell($length, $this->h_cell, $name);
  //Signature
  $this->SetXY($this->x1+115, $this->y1+210);
  $length = $this->GetStringWidth($sig);
  $this->Cell($length, $this->h_cell, $sig);
  
        if(!empty($this->prov_array)){
              foreach($this->prov_array as $objprov){
                $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
                //Name of Provider
                $this->SetXY($this->x1, $this->y1 + 214);
                $length = $this->GetStringWidth($objprov->name);
                $this->Cell($length, $this->h_cell, $objprov->name, 0,0,'L'); 
              }
        }
  }
  
  function AddAccNum($accnum, $birnum){
  $height = 83;
    
  $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
  $this->Rect($this->x1, $this->y1+219, 83, 15);
  //Accreditation Number
  $this->SetXY($this->x1, $this->y1+220);
  $length = $this->GetStringWidth($accnum);
  $this->Cell($length, $this->h_cell, $accnum);
  //BIR/TIN Number
  $this->Rect($this->x1+ $height, $this->y1+219, 83, 15);
  $this->SetXY($this->x1+ $height, $this->y1+220);
  $length = $this->GetStringWidth($birnum);
  $this->Cell($length, $this->h_cell, $birnum);
  $l_num = $length + 5;
  
      if(!empty($this->prov_array)){
              foreach($this->prov_array as $objprov){
                $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
                //Accreditation Number
                $this->SetXY($this->x1, $this->y1+224);
                $length = $this->GetStringWidth($objprov->accnum_phic);
                $this->Cell($length, $this->h_cell, $objprov->accnum_phic,0,0,'L');
                //BIR/TIN No.
                $this->SetXY($this->x1 + ($l_num + $height), $this->y1 + 220);
                $length = $this->GetStringWidth($objprov->bir_tin_num);
                $this->Cell($length, $this->h_cell, $objprov->bir_tin_num,0,0,'L');
              }
      }
  }
  
  function AddServPerf($serv){
 
  $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
  $this->Rect($this->x1, $this->y1+234, 83, 15);
  //Services Performed
  $this->SetXY($this->x1, $this->y1+235);
  $length = $this->GetStringWidth($serv);
  $this->Cell($length, $this->h_cell, $serv);
  $this->Ln(4);
  
  $tablerow = 5;
  $table_x = 83;
    if(!empty($this->prov_array)){
            foreach($this->prov_array as $objprov){
               $this->SetFont($this->fontfamily_ans, $this->fontstyle_ans, $this->fontsize_ans);
               //$this->SetXY($this->x1, $this->y1+239);
               $length = $this->GetStringWidth($objprov->serv_perf);
                  
                  if($length <= $this->max_space){
                    $this->Cell($this->max_space, $this->h_cell, $objprov->serv_perf, 0,0,'L');
                  }
                  else{
                     $this->MultiCell($this->max_space, $this->h_multi, $str,1,0,'C');
                  }
               //Row
               $this->SetXY($this->x1+ $table_x, $this->y1+244); 
               $this->Cell($this->ColumnWidth[0], $tablerow, $objprov->p_charges,1,0,'R');
               $this->Cell($this->ColumnWidth[1], $tablerow, $objprov->p_provider,1,0,'R');
               $this->Cell($this->ColumnWidth[2], $tablerow, $objprov->p_patient,1,0,'R');             
            }
    }
  
       $this->ColumnWidth = array(33, 25, 25);
  
        //TABLE
       $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label); 
       $this->SetXY($this->x1+ $table_x, $this->y1+234);
       $this->Cell($this->ColumnWidth[0], $tablerow, "Actual", 0, 0, 'C');
       $this->Cell($this->ColumnWidth[1] + $this->ColumnWidth[2], $tablerow,"PHIC BENEFIT PAYABLE TO", 1, 0, 'C');
       $this->SetXY($this->x1+ $table_x, $this->y1+239);
       $this->Cell($this->ColumnWidth[0], $tablerow, "Professional Charges", 0, 0 ,'C');
       $this->Cell($this->ColumnWidth[1], $tablerow, "Provider", 1, 0, 'C');
       $this->Cell($this->ColumnWidth[2], $tablerow, "Patient", 1, 0, 'C');
       $this->Rect($this->x1+ $table_x, $this->y1+234, $this->ColumnWidth[0], 10); 
           
  }
  
  function AddPhilUse($phil, $rvcode, $icode, $redcode){
  
  //Philhealth Use
  $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
  $this->Rect($this->x1+ $this->phealth_col, $this->y1+184, 40, 65);
  $this->SetXY($this->x1+ $this->phealth_col, $this->y1+188);
  $length = $this->GetStringWidth($phil);
  $this->Cell($length, $this->h_cell, $phil);
  $this->Line($this->x1+ $this->phealth_col, $this->y1+193, $this->x1+ $this->tot_width, $this->y1+193);
  //RVS Code
  $this->SetFont($this->fontfamily_label, $this->fontstyle_label, $this->fontsize_label);
  $this->SetXY($this->x1+ $this->phealth_col, $this->y1+194);
  $length = $this->GetStringWidth($rvcode);
  $this->Cell($length, $this->h_cell, $rvcode);
  $this->Line($this->x1+ $this->phealth_col, $this->y1+209, $this->x1+ $this->tot_width, $this->y1+209);
  //Illness Code
  $this->SetXY($this->x1+ $this->phealth_col, $this->y1+210);
  $length = $this->GetStringWidth($icode);
  $this->Cell($length, $this->h_cell, $icode);
  $this->Line($this->x1+ $this->phealth_col, $this->y1+219, $this->x1+ $this->tot_width, $this->y1+219);
  //Reduction Code
  $this->SetXY($this->x1+ $this->phealth_col, $this->y1+220);
  $length = $this->GetStringWidth($redcode);
  $this->Cell($length, $this->h_cell, $redcode);
  $this->Line($this->x1+ $this->phealth_col, $this->y1+234, $this->x1+ $this->tot_width, $this->y1+234);
  }
}

  class Accreditation{
    var $accnum;
    var $category;
  }

  class Hosp_Address{
    var $h_street;
    var $h_brgy;
    var $h_city;
    var $h_province;
    var $h_zipcode;
  }
  
  class MemberInfo{
    var $m_lname;
    var $m_mname;
    var $m_fname;
    var $m_idnum;
  }
  
  class MemberAddress{
    var $m_street;
    var $m_brgy;
    var $m_city;
    var $m_province;
    var $m_zipcode;
  }
  
  class PatientInfo{
    var $p_lname;
    var $p_fname;
    var $p_mname;
    var $p_age;
    var $p_diagnosis;
  }
  
  class Confinement{
    var $admit_dt;
    var $disch_dt;
    var $totnum_days;
    var $death_dt;
  }
  
  class FacilityServ{
    var $charges;
    var $facility;
    var $patient;
    var $reduction;
  }
  
  class Diagnosis{
    var $fin_diag;
    var $icd_code;
  }
  
  class Provider{
    var $name;
    var $accnum_phic;
    var $bir_tin_num;
    var $serv_perf;
    var $p_charges;
    var $p_provider;
    var $p_patient;
  } 


$pdf = new PhilhealthForm4();
$pdf->Open();
$pdf->AddPage();

$pdf->AddHead("PHILHEALTH",
              "CLAIM FORM 4",
              "April 2003",
              "NOTE: This form together with Claim Form 1 should be filed with". 
              "Philhealth within 60 calendar days from date of discharge",
              "MATERNITY CARE\n     PACKAGE");

$pdf->AddDateReceived("(DATE RECEIVED)");
 
$pdf->AddTitleHeaderI("PART I- FACILITY DATA AND CHARGES (Facility to Fill in All Items)");

$pdf->AddAccreditation("1. PhilHealth Accreditation No.",
                      "2. Accreditation Category",
                      "Primary",
                      "Secondary",
                      "Tertiary",
                      "Non Hospital Facilities".
                      "(Lying-in clinics, Midwife-managed clinics,\n".
                      "  Birthing Homes, Ambulatory Surgical Clinics)");

$pdf->AddNameFacility("3. Name of Facility");
                    
$pdf->AddAddressFacility("4. Address of Facility",
                        "No. Street",
                        "Municipality/City",
                        "Barangay",
                        "Province",
                        "Zip Code");                    

$pdf->AddMemNameAndId("5.Name of Member and Identification",
                      "Last Name",
                      "First Name",
                      "Middle Name",
                      "Philhealth\n".
                      "Identification No."); 

$pdf->AddMemAddress("6. Address of Member",
                    "No. Street",
                    "Barangay",
                    "Municipality/City",
                    "Province",
                    "Zip Code");  

$pdf->AddPatientName("7. Name of Patient",
                    "Last Name",
                    "First Name",
                    "Middle Name",
                    "8. Age",
                    "9. Admission Diagnosis");  

$pdf->AddConfinementPeriod("10. Confinement Period",
                          "a. Date Admitted",
                          "b. Date Discharged",
                          "c. Total No. of Days",
                          "d. Date of Death\n (If Applicable)");                    

$pdf->AddFacilityServ("11. Facility Services",
            "TOTAL",
            "Medicines & Supplies bought & laboratory\n".
            "performed outside during confinement period");
            
/*$cols=array( "11. Facility Services"    => 70,
             "Actual Facility"      => 40,
             "PHIC BENEFIT PAYABLE TO"  => 80,
             ""                => 30 );
$pdf->addCols( $cols);
$cols=array( "11. Facility Services"    => "L",
             "Actual Facility"      => "C",
             "PHIC BENEFIT PAYABLE TO"  => "C",
             ""                => "C");
//$pdf->addLineFormat( $cols);
//$pdf->addLineFormat($cols);

//$y    = 109;
$line = array( "REFERENCE"    => "REF1",
               "DESIGNATION"  => "Carte Mère MSI 6378\n" .
                                 "Processeur AMD 1Ghz\n" .
                                 "128Mo SDRAM, 30 Go Disque, CD-ROM, Floppy, Carte vidéo",
               "QUANTITE"     => "1",
               "P.U. HT"      => "600.00",
               "MONTANT H.T." => "600.00",
               "TVA"          => "1" );
*/
$pdf->AddCertification("12. CERTFICATION of FACILITY: I certify that the services rendered are duly recorded in the ".
            "patient's chart and that the information given in \n".
            "this form are true and correct",
            "Signature Over Printed Name of Authorized Representative",
            "Date Signed",
            "Official Capacity");
$pdf->AddPart2("PART II - PROFESSIONAL DATA AND CHARGES (Provider/s to Fill in Respective Portions)");
$pdf->AddFinalDiagnosis("13. Complete Final Diagnosis",
            "14. ICD-10 Code:");
$pdf->AddProvider("15. Name of Provider",
          "Signature & Date Signed");
$pdf->AddAccNum("16. PHIC Accreditation No.",
        "17. BIR/TIN No.");
$pdf->AddServPerf("18. Services Performed");
$pdf->AddPhilUse("FOR PHILHEALTH USE",
        "RVS Code",
        "Illness Code",
        "Reduction Code");

                                      
$pdf->Output();                  
?>
