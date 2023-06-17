<?php
   error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require($root_path.'include/care_api_classes/class_encounter.php');
require($root_path.'include/care_api_classes/class_person.php');

//define('FPDF_FONTPATH','/xampp/htdocs/xampp/font/');
//require('fpdf.php');
$x1 = 5;
$y1 = 8;
$half = 103;
$third = 160;
$tot_width = 206;
//$pid = 10000004;
  class PhilhealthForm1 extends FPDF {
  var $pid = 10000190;  
  var $member_name;
  var $patient_name;
  var $is_member;
  
 
  function PhilhealthForm1(){
          global $db;
   $this->FPDF('P', 'mm', 'letter');
   $this->SetDrawColor(0,0,0);
   $this->SetMargins(5,2,1);
   // $this->pid = 10000004;
  }
  
  function addHead($phil, $formnum, $rev, $note){
  global $x1, $y1;
  $this->Rect($x1, $y1 - 2, 160, 18);
    $this->SetXY( $x1, $y1 );
    $this->SetFont('Arial','B',18);
    $length = $this->GetStringWidth( $phil );
    $this->Cell( $length, 2, $phil);
    $this->SetXY($x1, $y1 + 4);
    $this->SetFont('Arial','B', 12);
    $length = $this->GetStringWidth($formnum);
    $this->Cell($length, 4, $formnum);
    $this->SetXY($x1, $y1 + 8);
    $this->SetFont('Arial', '', 10);
    $length = $this->GetStringWidth($rev);
    $this->Cell($length, 4, $rev);
    $this->SetXY($x1, $y1 + 12);
    $this->SetFont('Arial', '', 8);
    $length = $this->GetStringWidth($note);
    $this->Cell($length, 4, $note);
  }
  
  function addDateReceived($date){
  global $x1, $y1;
    $this->Rect($x1+160, $y1-2, 46, 18);
    $this->SetXY($x1+165, $y1);
    $this->SetFont('Arial', 'B', 10);
    $length = $this->GetStringWidth($date);
    $this->Cell($length, 4, $date);
    $this->SetXY($x1+165, $y1+8);
    $this->Cell(40, 2, "", 0,0,'C');
  }
   
  function addPart1($pt1){
  global $x1, $y1;
    $this->SetFont('Arial','B',10);
  $this->SetXY($x1+5, $y1+17);
  $length = $this->GetStringWidth($pt1);
  $this->Cell($length, 2, $pt1);
  }
  
  //Function for checking who (mother/father/spouse) the patient is dependent
  function checkdependence(){
  global $db, $pid;
    
    $sql = "SELECT d.parent_pid AS Parent, d.dependent_pid AS Dependent, 
    d.relationship AS Relationship, d.status AS Status
    FROM seg_dependents AS d 
    LEFT JOIN care_encounter AS e ON e.pid = d.dependent_pid
    WHERE d.dependent_pid = $this->pid AND d.status = 'member'";
  
  $result = $db->Execute($sql);
  $row = $result->FetchRow();
  
  return $row;
  }
  
  
  function addTypeOfMembership($type, $id){
  global $x1, $y1, $half;
  global $db;
  $length2 = $length3 = 0;
  $this->SetFont('Arial', '', 8.5);
  $this->Rect($x1, $y1+21, 206, 12);
  $ans = "X";
  $noans = "  ";
  
  //Type of membership
  $this->SetXY($x1, $y1+22);
  $length = $this->GetStringWidth($type);
  $this->Cell($length, 2, $type,0,0);
  $l = $length+2;
  $length2 = $length2 + $length;
  $this->is_member = $this->checkifmember();  
  
  if($this->is_member == 1){
    
    $sql1 = "SELECT em.memcategory_id  AS Id, ci.insurance_nr AS Number
            FROM seg_encounter_memcategory AS em
            LEFT JOIN care_encounter AS e ON e.encounter_nr = em.encounter_nr 
            LEFT JOIN care_person_insurance AS ci ON ci.pid = e.pid
            WHERE e.pid = $this->pid";
    
    $result = $db->Execute($sql1);
  }
  else{
    $dependent_on = $this->checkdependence();
    $member_pid = $dependent_on['Parent'];
    $sql2 = "SELECT em.memcategory_id  AS Id, ci.insurance_nr AS Number
            FROM seg_encounter_memcategory AS em
            LEFT JOIN care_encounter AS e ON e.encounter_nr = em.encounter_nr 
            LEFT JOIN care_person_insurance AS ci ON ci.pid = e.pid
            WHERE e.pid = $member_pid ";
    $result = $db->Execute($sql2);
  }
    
    if ($result) { 
      
      $this->_count = $result->RecordCount();
       $row = $result->FetchRow(); 
        //print_r($row);
        if($row['Id'] == 1){
        //Employed
        $this->SetXY($x1+($length+2), $y1+22);
        $length = $this->GetStringWidth("Employed");
        $this->Cell($length+6, 2, "[".$ans."] "."Employed:", 0, 0);
        $length2 = $length2 + ($length+8);
        //Private Sector
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Private Sector");
        $this->Cell($length+6, 2, "(".$noans.") "."Private Sector", 0, 0);
        $length2 = $length2 + ($length+7);
        //Gov't Sector
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Gov't Sector");
        $this->Cell($length+6, 2, "(".$ans.") "."Gov't Sector");
        $length2 = $length2 + ($length+7);
        //Individually paying
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Individually paying:");
        $this->Cell($length+6, 2, "[".$noans."] "."Individually paying:");
        $length2 = $length2 + ($length+5);
        //Self-employed
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Self-employed");
        $this->Cell($length+6, 2, "(".$noans.") "."Self-employed");
        $length2 = $length2 + ($length+7);
        //OFW
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("OFW");
        $this->Cell($length+6, 2, "(".$noans.") "."OFW");
        $length2 = $length2 + ($length+7);
        //Others
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Others");
        $this->Cell($length+6, 2, "(".$noans.") "."Others");
        $length2 = $length2 + ($length+5);
        //OWWA
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("OWWA");
        $this->Cell($length+6, 2, "(".$noans.") "."OWWA");
        //Indigent
        $this->SetXY($x1+$l, $y1+26);
        $length = $this->GetStringWidth("Indigent");
        $this->Cell($length+6, 2, "[".$noans."] "."Indigent");
        //Retiree/Pensioner
        $this->SetXY($x1+$half, $y1+26);
        $length = $this->GetStringWidth("Retiree/Pensioner");
        $this->Cell($length+6, 2, "[".$noans."] "."Retiree/Pensioner:");
        $length = $length + 100;
        $length3 = $length3 + ($length+9);
        //SSS
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("SSS");
        $this->Cell($length+6, 2, "(".$noans.") "."SSS");
        $length3 = $length3 + ($length+5);
        //GSIS
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("GSIS");
        $this->Cell($length+6, 2, "(".$noans.") "."GSIS");
        $length3 = $length3 + ($length+7);
        //Military
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("Military");
        $this->Cell($length+6, 2, "(".$noans.") "."Military");
        $length3 = $length3 + ($length+7);
        //Judiciary
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("Judiciary");
        $this->Cell($length+6, 2, "(".$noans.") "."Judiciary");
        }
        
        else if($row['Id'] == 2){
          //Employed
        $this->SetXY($x1+($length+2), $y1+22);
        $length = $this->GetStringWidth("Employed");
        $this->Cell($length+6, 2, "[".$ans."] "."Employed:", 0, 0);
        $length2 = $length2 + ($length+8);
        //Private Sector
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Private Sector");
        $this->Cell($length+6, 2, "(".$ans.") "."Private Sector", 0, 0);
        $length2 = $length2 + ($length+7);
        //Gov't Sector
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Gov't Sector");
        $this->Cell($length+6, 2, "(".$noans.") "."Gov't Sector");
        $length2 = $length2 + ($length+7);
        //Individually paying
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Individually paying:");
        $this->Cell($length+6, 2, "[".$noans."] "."Individually paying:");
        $length2 = $length2 + ($length+5);
        //Self-employed
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Self-employed");
        $this->Cell($length+6, 2, "(".$noans.") "."Self-employed");
        $length2 = $length2 + ($length+7);
        //OFW
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("OFW");
        $this->Cell($length+6, 2, "(".$noans.") "."OFW");
        $length2 = $length2 + ($length+7);
        //Others
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Others");
        $this->Cell($length+6, 2, "(".$noans.") "."Others");
        $length2 = $length2 + ($length+8);
        //OWWA
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("OWWA");
        $this->Cell($length+6, 2, "(".$noans.") "."OWWA");
        //Indigent
        $this->SetXY($x1+$l, $y1+26);
        $length = $this->GetStringWidth("Indigent");
        $this->Cell($length+6, 2, "[".$noans."] "."Indigent");
        //Retiree/Pensioner
        $this->SetXY($x1+$half, $y1+26);
        $length = $this->GetStringWidth("Retiree/Pensioner");
        $this->Cell($length+6, 2, "[".$noans."] "."Retiree/Pensioner:");
        $length = $length + 100;
        $length3 = $length3 + ($length+9);
        //SSS
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("SSS");
        $this->Cell($length+6, 2, "(".$noans.") "."SSS");
        $length3 = $length3 + ($length+7);
        //GSIS
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("GSIS");
        $this->Cell($length+6, 2, "(".$noans.") "."GSIS");
        $length3 = $length3 + ($length+7);
        //Military
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("Military");
        $this->Cell($length+6, 2, "(".$noans.") "."Military");
        $length3 = $length3 + ($length+7);
        //Judiciary
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("Judiciary");
        $this->Cell($length+6, 2, "(".$noans.") "."Judiciary");
        }
        
        else if($row['Id'] == 3){
        //Employed
        $this->SetXY($x1+($length+2), $y1+22);
        $length = $this->GetStringWidth("Employed");
        $this->Cell($length+6, 2, "[".$noans."] "."Employed:", 0, 0);
        $length2 = $length2 + ($length+8);
        //Private Sector
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Private Sector");
        $this->Cell($length+6, 2, "(".$noans.") "."Private Sector", 0, 0);
        $length2 = $length2 + ($length+7);
        //Gov't Sector
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Gov't Sector");
        $this->Cell($length+6, 2, "(".$noans.") "."Gov't Sector");
        $length2 = $length2 + ($length+7);
        //Individually paying
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Individually paying:");
        $this->Cell($length+6, 2, "[".$ans."] "."Individually paying:");
        $length2 = $length2 + ($length+5);
        //Self-employed
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Self-employed");
        $this->Cell($length+6, 2, "(".$noans.") "."Self-employed");
        $length2 = $length2 + ($length+7);
        //OFW
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("OFW");
        $this->Cell($length+6, 2, "(".$noans.") "."OFW");
        $length2 = $length2 + ($length+7);
        //Others
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Others");
        $this->Cell($length+6, 2, "(".$noans.") "."Others");
        $length2 = $length2 + ($length+8);
        //OWWA
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("OWWA");
        $this->Cell($length+6, 2, "(".$ans.") "."OWWA");
        //Indigent
        $this->SetXY($x1+$l, $y1+26);
        $length = $this->GetStringWidth("Indigent");
        $this->Cell($length+6, 2, "[".$noans."] "."Indigent");
        //Retiree/Pensioner
        $this->SetXY($x1+$half, $y1+26);
        $length = $this->GetStringWidth("Retiree/Pensioner");
        $this->Cell($length+6, 2, "[".$noans."] "."Retiree/Pensioner:");
        $length = $length + 100;
        $length3 = $length3 + ($length+9);
        //SSS
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("SSS");
        $this->Cell($length+6, 2, "(".$noans.") "."SSS");
        $length3 = $length3 + ($length+7);
        //GSIS
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("GSIS");
        $this->Cell($length+6, 2, "(".$noans.") "."GSIS");
        $length3 = $length3 + ($length+7);
        //Military
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("Military");
        $this->Cell($length+6, 2, "(".$noans.") "."Military");
        $length3 = $length3 + ($length+7);
        //Judiciary
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("Judiciary");
        $this->Cell($length+6, 2, "(".$noans.") "."Judiciary");
        }
        
        else if($row['Id'] == 4){
        //Employed
        $this->SetXY($x1+($length+2), $y1+22);
        $length = $this->GetStringWidth("Employed");
        $this->Cell($length+6, 2, "[".$noans."] "."Employed:", 0, 0);
        $length2 = $length2 + ($length+8);
        //Private Sector
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Private Sector");
        $this->Cell($length+6, 2, "(".$noans.") "."Private Sector", 0, 0);
        $length2 = $length2 + ($length+7);
        //Gov't Sector
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Gov't Sector");
        $this->Cell($length+6, 2, "(".$noans.") "."Gov't Sector");
        $length2 = $length2 + ($length+7);
        //Individually paying
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Individually paying:");
        $this->Cell($length+6, 2, "[".$ans."] "."Individually paying:");
        $length2 = $length2 + ($length+5);
        //Self-employed
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Self-employed");
        $this->Cell($length+6, 2, "(".$ans.") "."Self-employed");
        $length2 = $length2 + ($length+7);
        //OFW
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("OFW");
        $this->Cell($length+6, 2, "(".$noans.") "."OFW");
        $length2 = $length2 + ($length+7);
        //Others
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Others");
        $this->Cell($length+6, 2, "(".$noans.") "."Others");
        $length2 = $length2 + ($length+8);
        //OWWA
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("OWWA");
        $this->Cell($length+6, 2, "(".$noans.") "."OWWA");
        //Indigent
        $this->SetXY($x1+$l, $y1+26);
        $length = $this->GetStringWidth("Indigent");
        $this->Cell($length+6, 2, "[".$noans."] "."Indigent");
        //Retiree/Pensioner
        $this->SetXY($x1+$half, $y1+26);
        $length = $this->GetStringWidth("Retiree/Pensioner");
        $this->Cell($length+6, 2, "[".$noans."] "."Retiree/Pensioner:");
        $length = $length + 100;
        $length3 = $length3 + ($length+9);
        //SSS
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("SSS");
        $this->Cell($length+6, 2, "(".$noans.") "."SSS");
        $length3 = $length3 + ($length+7);
        //GSIS
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("GSIS");
        $this->Cell($length+6, 2, "(".$noans.") "."GSIS");
        $length3 = $length3 + ($length+7);
        //Military
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("Military");
        $this->Cell($length+6, 2, "(".$noans.") "."Military");
        $length3 = $length3 + ($length+7);
        //Judiciary
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("Judiciary");
        $this->Cell($length+6, 2, "(".$noans.") "."Judiciary");
        }
        
        else if($row['Id'] == 5){
        //Employed
        $this->SetXY($x1+($length+2), $y1+22);
        $length = $this->GetStringWidth("Employed");
        $this->Cell($length+6, 2, "[".$noans."] "."Employed:", 0, 0);
        $length2 = $length2 + ($length+8);
        //Private Sector
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Private Sector");
        $this->Cell($length+6, 2, "(".$noans.") "."Private Sector", 0, 0);
        $length2 = $length2 + ($length+7);
        //Gov't Sector
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Gov't Sector");
        $this->Cell($length+6, 2, "(".$noans.") "."Gov't Sector");
        $length2 = $length2 + ($length+7);
        //Individually paying
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Individually paying:");
        $this->Cell($length+6, 2, "[".$noans."] "."Individually paying:");
        $length2 = $length2 + ($length+5);
        //Self-employed
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Self-employed");
        $this->Cell($length+6, 2, "(".$noans.") "."Self-employed");
        $length2 = $length2 + ($length+7);
        //OFW
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("OFW");
        $this->Cell($length+6, 2, "(".$noans.") "."OFW");
        $length2 = $length2 + ($length+7);
        //Others
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("Others");
        $this->Cell($length+6, 2, "(".$noans.") "."Others");
        $length2 = $length2 + ($length+8);
        //OWWA
        $this->SetXY($x1+$length2, $y1+22);
        $length = $this->GetStringWidth("OWWA");
        $this->Cell($length+6, 2, "(".$noans.") "."OWWA");
        //Indigent
        $this->SetXY($x1+$l, $y1+26);
        $length = $this->GetStringWidth("Indigent");
        $this->Cell($length+6, 2, "[".$ans."] "."Indigent");
        //Retiree/Pensioner
        $this->SetXY($x1+$half, $y1+26);
        $length = $this->GetStringWidth("Retiree/Pensioner");
        $this->Cell($length+6, 2, "[".$noans."] "."Retiree/Pensioner:");
        $length = $length + 100;
        $length3 = $length3 + ($length+9);
        //SSS
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("SSS");
        $this->Cell($length+6, 2, "(".$noans.") "."SSS");
        $length3 = $length3 + ($length+7);
        //GSIS
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("GSIS");
        $this->Cell($length+6, 2, "(".$noans.") "."GSIS");
        $length3 = $length3 + ($length+7);
        //Military
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("Military");
        $this->Cell($length+6, 2, "(".$noans.") "."Military");
        $length3 = $length3 + ($length+7);
        //Judiciary
        $this->SetXY($x1+$length3, $y1+26);
        $length = $this->GetStringWidth("Judiciary");
        $this->Cell($length+6, 2, "(".$noans.") "."Judiciary");
        }
        
       else{ 
         //Employed
         $this->SetXY($x1+($length+2), $y1+22);
         $length = $this->GetStringWidth("Employed");
         $this->Cell($length+6, 2, "[".$noans."] "."Employed:", 0, 0);
         $length2 = $length2 + ($length+8);
         //Private Sector
         $this->SetXY($x1+$length2, $y1+22);
         $length = $this->GetStringWidth("Private Sector");
         $this->Cell($length+6, 2, "(".$noans.") "."Private Sector", 0, 0);
         $length2 = $length2 + ($length+7);
         //Gov't Sector
         $this->SetXY($x1+$length2, $y1+22);
         $length = $this->GetStringWidth("Gov't Sector");
         $this->Cell($length+6, 2, "(".$noans.") "."Gov't Sector");
         $length2 = $length2 + ($length+7);
         //Individually paying
         $this->SetXY($x1+$length2, $y1+22);
         $length = $this->GetStringWidth("Individually paying:");
         $this->Cell($length+6, 2, "[".$noans."] "."Individually paying:");
         $length2 = $length2 + ($length+5);
         //Self-employed
         $this->SetXY($x1+$length2, $y1+22);
         $length = $this->GetStringWidth("Self-employed");
         $this->Cell($length+6, 2, "(".$noans.") "."Self-employed");
         $length2 = $length2 + ($length+7);
         //OFW
         $this->SetXY($x1+$length2, $y1+22);
         $length = $this->GetStringWidth("OFW");
         $this->Cell($length+6, 2, "(".$noans.") "."OFW");
         $length2 = $length2 + ($length+7);
         //Others
         $this->SetXY($x1+$length2, $y1+22);
         $length = $this->GetStringWidth("Others");
         $this->Cell($length+6, 2, "(".$noans.") "."Others");
         $length2 = $length2 + ($length+8);
         //OWWA
         $this->SetXY($x1+$length2, $y1+22);
         $length = $this->GetStringWidth("OWWA");
         $this->Cell($length+6, 2, "(".$noans.") "."OWWA");
         //Indigent
         $this->SetXY($x1+$l, $y1+26);
         $length = $this->GetStringWidth("Indigent");
         $this->Cell($length+6, 2, "[".$noans."] "."Indigent");
         //Retiree/Pensioner
         $this->SetXY($x1+$half, $y1+26);
         $length = $this->GetStringWidth("Retiree/Pensioner");
         $this->Cell($length+6, 2, "[".$noans."] "."Retiree/Pensioner:");
         $length = $length + 100;
         $length3 = $length3 + ($length+9);
         //SSS
         $this->SetXY($x1+$length3, $y1+26);
         $length = $this->GetStringWidth("SSS");
         $this->Cell($length+6, 2, "(".$noans.") "."SSS");
         $length3 = $length3 + ($length+7);
         //GSIS
         $this->SetXY($x1+$length3, $y1+26);
         $length = $this->GetStringWidth("GSIS");
         $this->Cell($length+6, 2, "(".$noans.") "."GSIS");
         $length3 = $length3 + ($length+7);
         //Military
         $this->SetXY($x1+$length3, $y1+26);
         $length = $this->GetStringWidth("Military");
         $this->Cell($length+6, 2, "(".$noans.") "."Military");
         $length3 = $length3 + ($length+7);
         //Judiciary
         $this->SetXY($x1+$length3, $y1+26);
         $length = $this->GetStringWidth("Judiciary");
         $this->Cell($length+6, 2, "(".$noans.") "."Judiciary");
       }  
    
  //Identification Number
   $this->SetXY($x1+3, $y1+30);
  $length = $this->GetStringWidth($id);
  $this->Cell($length, 2, $id);
  $this->SetX($x1+($length+10));
  $length = $this->GetStringWidth($row['Number']);
  $this->Cell($length, 2, $row['Number']);
  }
}  
  
  function addMemberInfo($memname, $lname, $fname, $mname, $birth, $stat, $sex){
    //care_encounter
    //care_person
  global $x1, $y1, $half, $third, $tot_width, $pid;
  global $db;
  $noans = " ";
  $ans = "X";
   $this->Rect($x1, $y1+33, $half, 28);
  $this->SetFont('Arial','B',9);
  //Member Name
  $this->SetXY($x1, $y1+34);
  $length = $this->GetStringWidth($memname);
  $this->Cell($length, 2, $memname);
  //Last Name 
  $this->SetXY($x1+3, $y1+38);
  $length = $this->GetStringWidth($lname);
  $this->Cell($length, 2, $lname);  
  //First Name
  $this->SetXY($x1+3, $y1+46);
  $length = $this->GetStringWidth($fname);
  $this->Cell($length, 2, $fname);
  //Middle Name
  $this->SetXY($x1+3, $y1+54);
  $length = $this->GetStringWidth($mname);
  $this->Cell($length, 2, $mname);

  //Birthdate
  $this->Rect($x1+$half, $y1+33, ($tot_width - $half), 12);
  $this->SetXY($x1+$half, $y1+34);
  $length = $this->GetStringWidth($birth);
  $this->Cell($length, 2, $birth); 
  
  //Civil Status
  $this->Rect($x1+$half, $y1+45, ($third-$half), 16);
  $this->SetXY($x1+$half, $y1+46);
  $length = $this->GetStringWidth($stat);
  $this->Cell($length, 2, $stat);
  
  //Sex
  $this->Rect($x1+$third, $y1+45, $tot_width - $third, 16);
  $this->SetXY($x1+$third, $y1+46);
  $length = $this->GetStringWidth($sex);
  $this->Cell($length, 2, $sex);
  
//$is_member = $this->checkifmember();
    if($this->is_member == 1) {
      
      
      $sql_1 = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName, 
                p.name_3 AS ThirdName, p.name_middle AS MiddleName, 
                p.date_birth AS DateOfBirth, p.civil_status AS CivilStatus, p.sex AS SEX
                FROM care_person AS p
                LEFT JOIN care_encounter AS e ON e.pid = p.pid
                LEFT JOIN care_person_insurance AS i ON i.pid = p.pid
                WHERE i.hcare_id = 18 AND i.is_principal = 1 AND p.pid = $this->pid";
      
      $result = $db->Execute($sql_1);
}
else{ 
      $dependent_on = $this->checkdependence();
      $member_pid = $dependent_on['Parent'];
    
      $sql_2 = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName, 
                p.name_3 AS ThirdName, p.name_middle AS MiddleName, 
                p.date_birth AS DateOfBirth, p.civil_status AS CivilStatus, p.sex AS sex
                FROM care_person AS p
                LEFT JOIN care_encounter AS e ON e.pid = p.pid
                LEFT JOIN care_person_insurance AS i ON i.pid = p.pid
                WHERE i.hcare_id = 18 AND i.is_principal = 1 AND p.pid = $member_pid";
      
      $result = $db->Execute($sql_2);
}

    
    if ($result) {
      $height = 42;            
      $this->_count = $result->RecordCount();
       $row = $result->FetchRow(); 
       $this->SetFont('Arial', '', 9);  
          //Last Name
          $this->SetXY($x1+3, $y1+$height);
          $length = $this->GetStringWidth($row['LastName']);
          $this->Cell($length, 2, $row['LastName'],0,0,'L');
          $height = $height + 8;
        //First Name 
         //$row['FirstName'];
         $this->SetXY($x1+3, $y1+$height);
         $firstname = $row['FirstName']." ".$row['SecondName']." ".$row['ThirdName'];
         $length = $this->GetStringWidth($firstname);
         $this->Cell($length, 2, $firstname, 0, 0, 'L');
         $height = $height + 8;
         
         //Middle Name 
          $this->SetXY($x1+3, $y1+$height);
          $length = $this->GetStringWidth($row['MiddleName']);
          $this->Cell($length, 2, $row['MiddleName'], 0,0, 'L');
         
         $this->member_name = $firstname." ".$row['MiddleName']." ".$row['LastName'];  
         
           //Date of Birth
         $this->SetXY($x1+($third-20), $y1+40);
         $length = $this->GetStringWidth($row['DateOfBirth']);
         $this->Cell($length, 2, date("m/d/Y",strtotime($row['DateOfBirth'])), 0, 0 ,'C');
         
         //Civil Status
         $this->SetFont('Arial', 'B', 9);
          if($row['CivilStatus']=="single"){
           $this->SetXY($x1+($half+3), $y1+50);
           $length = $this->GetStringWidth("[  ] Single");
           $this->Cell($length, 2, "[ ".$ans." ] "."Single");
           $this->SetXY($x1+($half+3), $y1+54);
           $length = $this->GetStringWidth("[  ] Married");
           $this->Cell($length, 2, "[ ".$noans."  ] "."Married");
           $this->SetXY($x1+($half+25), $y1+50);
           $length = $this->GetStringWidth("[  ] Separated");
           $this->Cell($length, 2, "[ ".$noans."  ] "."Separated");
           $this->SetXY($x1+($half+25), $y1+54);
           $length = $this->GetStringWidth("[  ] Widow/er");
           $this->Cell($length, 2, "[ ".$noans."  ] "."Widow/er");
         }
          else if($row['CivilStatus']=="married"){
            $this->SetXY($x1+($half+3), $y1+50);
            $length = $this->GetStringWidth("[  ] Single");
            $this->Cell($length, 2, "[ ".$noans."  ] "."Single");
            $this->SetXY($x1+($half+3), $y1+54);
            $length = $this->GetStringWidth("[  ] Married");
            $this->Cell($length, 2, "[ ".$ans." ] "."Married");
            $this->SetXY($x1+($half+25), $y1+50);
            $length = $this->GetStringWidth("[  ] Separated");
            $this->Cell($length, 2, "[ ".$noans."  ] "."Separated");
            $this->SetXY($x1+($half+25), $y1+54);
            $length = $this->GetStringWidth("[  ] Widow/er");
            $this->Cell($length, 2, "[ ".$noans."  ] "."Widow/er");
         }
          else if($row['CivilStatus']=="separated"){
            $this->SetXY($x1+($half+3), $y1+50);
            $length = $this->GetStringWidth("[  ] Single");
            $this->Cell($length, 2, "[ ".$noans."  ] "."Single");
            $this->SetXY($x1+($half+3), $y1+54);
            $length = $this->GetStringWidth("[  ] Married");
            $this->Cell($length, 2, "[ ".$noans."  ] "."Married");
            $this->SetXY($x1+($half+25), $y1+50);
            $length = $this->GetStringWidth("[  ] Separated");
            $this->Cell($length, 2, "[ ".$ans." ] "."Separated");
            $this->SetXY($x1+($half+25), $y1+54);
            $length = $this->GetStringWidth("[  ] Widow/er");
            $this->Cell($length, 2, "[ ".$noans."  ] "."Widow/er");
         }
          else if($row['CivilStatus']=="widow" || $row['CivilStatus']=="widower"){
            $this->SetXY($x1+($half+3), $y1+50);
            $length = $this->GetStringWidth("[  ] Single");
            $this->Cell($length, 2, "[ ".$noans."  ] "."Single");
            $this->SetXY($x1+($half+3), $y1+54);
            $length = $this->GetStringWidth("[  ] Married");
            $this->Cell($length, 2, "[ ".$noans."  ] "."Married");
            $this->SetXY($x1+($half+25), $y1+50);
            $length = $this->GetStringWidth("[  ] Separated");
            $this->Cell($length, 2, "[ ".$noans."  ] "."Separated");
            $this->SetXY($x1+($half+25), $y1+54);
            $length = $this->GetStringWidth("[  ] Widow/er");
            $this->Cell($length, 2, "[ ".$ans." ] "."Widow/er");
         }
         
         else{
            $this->SetXY($x1+($half+3), $y1+50);
            $length = $this->GetStringWidth("[  ] Single");
            $this->Cell($length, 2, "[ ".$noans." ] "."Single");
            $this->SetXY($x1+($half+3), $y1+54);
            $length = $this->GetStringWidth("[  ] Married");
            $this->Cell($length, 2, "[ ".$noans." ] "."Married");
            $this->SetXY($x1+($half+25), $y1+50);
            $length = $this->GetStringWidth("[  ] Separated");
            $this->Cell($length, 2, "[ ".$noans." ] "."Separated");
            $this->SetXY($x1+($half+25), $y1+54);
            $length = $this->GetStringWidth("[  ] Widow/er");
            $this->Cell($length, 2, "[ ".$noans." ] "."Widow/er");
         } 
         //sex
          if($row['sex']=="m"){
           $this->SetXY($x1+($third+15), $y1+50);
           $length = $this->GetStringWidth("[  ] Male");
           $this->Cell($length, 2, "[ ".$ans." ] "."Male");
           $this->SetXY($x1+($third+15), $y1+54);
           $length = $this->GetStringWidth("[  ] Female");
           $this->Cell($length, 2, "[ ".$noans."  ] "."Female");
          }
          else if($row['sex']=="f"){
           $this->SetXY($x1+($third+15), $y1+50);
           $length = $this->GetStringWidth("[  ] Male");
           $this->Cell($length, 2, "[ ".$noans."  ] "."Male");
           $this->SetXY($x1+($third+15), $y1+54);
           $length = $this->GetStringWidth("[  ] Female");
           $this->Cell($length, 2, "[ ".$ans." ] "."Female");
          }
          
          else{
             $this->SetXY($x1+($third+15), $y1+50);
             $length = $this->GetStringWidth("[  ] Male");
             $this->Cell($length, 2, "[ ".$noans." ] "."Male");
             $this->SetXY($x1+($third+15), $y1+54);
             $length = $this->GetStringWidth("[  ] Female");
             $this->Cell($length, 2, "[ ".$noans." ] "."Female");
          }
     
    }
    else {
      print_r($sql);
      print_r($db->ErrorMsg());
      exit;
      # Error
        }
        
        
}
  
  function checkifmember(){
    global $db;
$sql = "SELECT i.is_principal AS Member FROM care_person_insurance AS i 
LEFT JOIN care_encounter e ON e.pid = i.pid
WHERE e.pid = $this->pid";
$result = $db->Execute($sql);
$row = $result->FetchRow();
return $row['Member'];
  }
  
  function addAddressOfMember($addmem, $street, $city, $bar, $prov, $code){
    //care_encounter
    //care_person
  global $x1, $y1, $half, $third, $db;  
  $this->SetFont('Arial','B',9);
  $this->Rect($x1, $y1+61, $half, 20);
  $this->Rect($x1+$half, $y1+61, $half, 20);
  //Address Of Member
  $this->SetXY($x1, $y1+62);
  $length = $this->GetStringWidth($addmem);
  $this->Cell($length, 2, $addmem);
  //Street
  $this->SetXY($x1+3, $y1+66);
  $length = $this->GetStringWidth($street);
  $this->Cell($length, 2, $street);
  //City
  $this->SetXY($x1+3, $y1+74);
  $length = $this->GetStringWidth($city);
  $this->Cell($length, 2, $city);
  //Barangay
  $this->SetXY($x1+$half, $y1+66);
  $length = $this->GetStringWidth($bar);
  $this->Cell($length, 2, $bar);
  //Province
  $this->SetXY($x1+$half, $y1+74);
  $length = $this->GetStringWidth($prov);
  $this->Cell($length, 2, $prov);
  //Zip code
  $this->SetXY($x1+($third+20), $y1+74);
  $length = $this->GetStringWidth($code);
  $this->Cell($length, 2, $code);
  
  //$is_member = $this->checkifmember(); 
  
  if($this->is_member == 1){
                        
          $sql_1 = "SELECT p.street_name AS Street, sb.brgy_name AS Barangay,
                    sg.mun_name AS Municity, sg.zipcode AS Zipcode, sp.prov_name AS Province 
                    FROM care_person AS p
                    LEFT JOIN seg_barangays AS sb ON sb.brgy_nr = p.brgy_nr
                    LEFT JOIN seg_municity AS sg ON sg.mun_nr = sb.mun_nr
                    LEFT JOIN seg_provinces AS sp ON sp.prov_nr = sg.prov_nr 
                    WHERE p.pid = $this->pid";
          $result = $db->Execute($sql_1);            
  }
  else{
         $dependent_on = $this->checkdependence();
         $member_pid = $dependent_on['Parent'];
         //$pid = 10000190;
         $sql_2 =  "SELECT p.street_name AS Street, sb.brgy_name AS Barangay,
                    sg.mun_name AS Municity, sg.zipcode AS Zipcode, sp.prov_name AS Province 
                    FROM care_person AS p
                    LEFT JOIN seg_barangays AS sb ON sb.brgy_nr = p.brgy_nr
                    LEFT JOIN seg_municity AS sg ON sg.mun_nr = sb.mun_nr
                    LEFT JOIN seg_provinces AS sp ON sp.prov_nr = sg.prov_nr 
                    WHERE p.pid = $member_pid";
         $result = $db->Execute($sql_2);
  }
      
            if ($result) {
                            
                  $this->_count = $result->RecordCount();
                  $row = $result->FetchRow();
                  $this->SetFont('Arial', '', 9);
                   //print_r($row);
                  //Street
                  $height = 70;  
                  $this->SetXY($x1+3, $y1+$height);
                  $length = $this->GetStringWidth($row['Street']);
                  $this->Cell($length, 2, $row['Street']);
                  //Barangay
                  $this->SetX($x1+($half+3));
                  $length = $this->GetStringWidth($row['Barangay']);
                  $this->Cell($length, 2, $row['Barangay']);
                  //City
                  $height = $height + 8;
                  $this->SetXY($x1+3, $y1+$height);
                  $length = $this->GetStringWidth($row['Municity']);
                  $this->Cell($length, 2, $row['Municity']);
                  //Province
                  $this->SetX($x1+($half+3));
                  $length = $this->GetStringWidth($row['Province']);
                  $this->Cell($length, 2, $row['Province']);
                  //Zip Code 
                  $this->SetX($x1+($third+20));
                  $length = $this->GetStringWidth($row['Zipcode']);
                  $this->Cell($length, 2, $row['Zipcode']);
            }
  }
  
  function addNameOfSpouse($namespouse, $lname, $mname, $fname){
    //care_person
    //care_encounter
  global $x1, $y1, $half;
  global $db;
  $ans = "X";
  $noans = " ";
  $this->SetFont('Arial','B',9);
  $this->Rect($x1, $y1+81, $half, 20);
  $this->Rect($x1+$half, $y1+81, $half, 20);
 
 
  //Name of Spouse
  $this->SetXY($x1, $y1+82);
  $length = $this->GetStringWidth($namespouse);
  $this->Cell($length, 2, $namespouse);
  //Last Name
  $this->SetXY($x1+3, $y1+86);
  $length = $this->GetStringWidth($lname);
  $this->Cell($length, 2, $lname);
  //Middle Name
  $this->SetXY($x1+3, $y1+94);
  $length = $this->GetStringWidth($mname);
  $this->Cell($length, 2, $mname);
  //First Name
  $this->SetXY($x1+$half, $y1+86);
  $length = $this->GetStringWidth($fname);
  $this->Cell($length, 2, $fname);
  
  $stat = $this->checkstatus();
  if($stat == "married"){
      //Not Applicable
  $this->SetXY($x1+$half, $y1+98);
  $length = $this->GetStringWidth("[  ] Not Applicable");
  $this->Cell($length, 2, "[ ".$noans." ] "."Not Applicable");     
  }
  else{
    //Not Applicable
  $this->SetXY($x1+$half, $y1+98);
  $length = $this->GetStringWidth("[  ] Not Applicable");
  $this->Cell($length, 2, "[ ".$ans." ] "."Not Applicable"); 
  }
  
  
  }
  
  function checkstatus(){
    global $db;
    $sql = "SELECT p.civil_status AS Status  FROM care_person AS p
            WHERE p.pid = $this->pid";
    $result = $db->Execute($sql);
    $row = $result->FetchRow();
    return $row['Status']; 
  }
  
  function addNameOfPatient($npatient, $lname, $fname, $mname, $bday, $age, $sex){
    //care_person
    //care_encounter
  global $x1, $y1, $half, $third, $tot_width, $pid;
  global $db;
  $ans = "X";
  $noans = "  ";
  $this->SetFont('Arial', 'B', 9);
  $this->Rect($x1, $y1+101, $half, 28);
  $this->Rect($x1+$half, $y1+101, $half, 12);
  $this->Rect($x1+$half, $y1+113, $third - $half, 16);
  $this->Rect($x1+$third, $y1+113, $tot_width - $third, 16);
 //Name of Patient
  $this->SetXY($x1, $y1+102);
  $length = $this->GetStringWidth($npatient);
  $this->Cell($length, 2, $npatient);
 
   
$sql = "SELECT p.name_last AS LastName, p.name_first AS FirstName, p.name_2 AS SecondName, 
p.name_3 AS ThirdName, p.name_middle AS MiddleName, p.date_birth AS DateOfBirth, 
p.civil_status AS CivilStatus, p.age AS Age, p.sex AS Sex, pi.is_principal AS principal
FROM care_person AS p
LEFT JOIN care_encounter AS e ON e.pid = p.pid
LEFT JOIN care_person_insurance AS pi ON pi.pid = p.pid
LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr = e.encounter_nr
WHERE i.hcare_id = 18 AND p.pid = $this->pid";
 $current_height = 110;
 $result=$db->Execute($sql); 
    if ($result) {         
      $this->_count = $result->RecordCount();
      //while ($row=$result->FetchRow()) {
        $row = $result->FetchRow();
        //Is Principal?
        if($row['principal'] == 1){
          $this->SetXY($x1+50, $y1+102);
          $length = $this->GetStringWidth("[  ] Patient is the Member");
          $this->Cell($length, 2, "[ ".$ans." ] ". "Patient is the Member");
        }
        else{
          $this->SetXY($x1+50, $y1+102);
          $length = $this->GetStringWidth("[  ] Patient is the Member");
          $this->Cell($length, 2, "[ ".$noans." ] ". "Patient is the Member");
        }
        //Patient Last Name
        $this->SetFont('Arial', '', 9);
        $this->SetXY($x1+3, $y1+$current_height);
        $length = $this->GetStringWidth($row['LastName']);
        $this->Cell($length, 2, $row['LastName'], 0, 0, 'L');
        $current_height = $current_height + 8;
        //Patient First Name
        $this->SetXY($x1+3, $y1+$current_height);
        $firstname = $row['FirstName']." ".$row['SecondName']." ".$row['ThirdName'];  
        $length = $this->GetStringWidth($firstname);
        $this->Cell($length, 2, $firstname);
        $current_height = $current_height + 8;
        //Patient Middle Name
        $this->SetXY($x1+3, $y1+$current_height);
        $length = $this->GetStringWidth($row['MiddleName']);
        $this->Cell($length, 2, $row['MiddleName']);
        $this->patient_name = $firstname." ".$row['MiddleName']." ".$row['LastName'];
        //Patient Date of Birth......change format
        $this->SetXY($x1+($third-20), $y1+106);
        $length = $this->GetStringWidth($row['DateOfBirth']);
        $this->Cell($length, 2, date("m/d/Y",strtotime($row['DateOfBirth'])));
        //Patient Age
        $this->SetXY($x1+($half+10));
        $length = $this->GetStringWidth($row['Age']);
        $this->Cell($length, 2, $row['Age']);
        //Patient Sex
        $this->SetFont('Arial', 'B', 9);
        if($row['Sex']=="m"){
        $this->SetXY($x1+($third+15), $y1+118);
        $length = $this->GetStringWidth("[  ] Male");
        $this->Cell($length, 2, "[".$ans."] "."Male");
        $this->SetXY($x1+($third+15), $y1+122);
        $length = $this->GetStringWidth("[  ] Female");
        $this->Cell($length, 2, "[".$noans."] "."Female");
        }
        else{
        $this->SetXY($x1+($third+15), $y1+118);
        $length = $this->GetStringWidth("[  ] Male");
        $this->Cell($length, 2, "[".$noans."] "."Male");
        $this->SetXY($x1+($third+15), $y1+122);
        $length = $this->GetStringWidth("[  ] Female");
        $this->Cell($length, 2, "[".$ans."] "."Female");
        }
        
      //}
}     
  //Last Name
  $this->SetXY($x1+3, $y1+106);
  $length = $this->GetStringWidth($lname);
  $this->Cell($length, 2, $lname);
  //First Name
  $this->SetXY($x1+3, $y1+114);
  $length = $this->GetStringWidth($fname);
  $this->Cell($length, 2, $fname);
  //Middle Name
  $this->SetXY($x1+3, $y1+122);
  $length = $this->GetStringWidth($mname);
  $this->Cell($length, 2, $mname);
  //Birthday
  $this->SetXY($x1+$half, $y1+102);
  $length = $this->GetStringWidth($bday);
  $this->Cell($length, 2, $bday);
  //Age 
  $this->SetXY($x1+$half, $y1+114);
  $length = $this->GetStringWidth($age);
  $this->Cell($length, 2, $age);
  //Sex
  $this->SetXY($x1+$third, $y1+114);
  $length = $this->GetStringWidth($sex);
  $this->Cell($length, 2, $sex);

  }  

  function addRelOfPatientToMember($rel, $leg, $unemp, $parent, $unmarried){
  global $x1, $y1, $tot_width, $half;
  $ans = "";
  $this->SetFont('Arial', 'B', 8.5);
  $this->Rect($x1, $y1+129, $tot_width, 18);
  $this->Line($x1+$half, $y1+134, $x1+$half, $y1+147);
  //Relationship of Patient to Member
  $this->SetXY($x1, $y1+130);
  $length = $this->GetStringWidth($rel);
  $this->Cell($length, 2, $rel);
  //Legitimate spouse
  $this->SetXY($x1, $y1+134);
  $length = $this->GetStringWidth($leg);
  $this->Cell($length, 3, "[ ".$ans." ] ".$leg);
  //Unmarried and Unemployed
  $this->SetXY($x1, $y1+138);
  $length = $this->GetStringWidth($unemp);
  $this->MultiCell($length, 3, "[ ".$ans." ] ".$unemp);
  //Parent
  $this->SetXY($x1+$half, $y1+134);
  $length = $this->GetStringWidth($parent);
  $this->MultiCell($length, 3, "[ ".$ans." ] ".$parent);
  //Unmarried child
  $this->SetXY($x1+$half, $y1+141);
  $length = $this->GetStringWidth($unmarried);
  $this->MultiCell($length, 3, "[ ".$ans." ] ".$unmarried);
  }
  
  function addCertification($cert, $sig, $thmark, $name){
  global $x1, $y1, $half;
  $this->SetFont('Arial','B',8.5);
  $this->Rect($x1, $y1+147, $half+$half, 28);
  //Certification
  $this->SetXY($x1, $y1+148);
  $length = $this->GetStringWidth($cert);
  $this->MultiCell($length, 3, $cert);
  //Signature
  $this->SetFont('Arial', '', 7);
  $this->SetXY($x1+5, $y1+162);
  $this->Cell(60, 2, strtoupper($this->member_name), 0,0, 'C');
  $this->Line($x1+5, $y1+164, $x1+65, $y1+164);
  $this->SetXY($x1+25, $y1+165);
  $length = $this->GetStringWidth($sig);
  $this->Cell($length, 2, $sig);
  //Thumbmark
  $this->Rect($x1+85, $y1+155, 28, 16);
  $this->SetXY($x1+75, $y1+172);
  $length = $this->GetStringWidth($thmark);
  $this->Cell($length, 2, $thmark);
  //Printed Name
  $this->Line($x1+($half+25), $y1+164, $x1+195, $y1+164);
  $this->SetXY($x1+($half+30), $y1+165);
  $length = $this->GetStringWidth($name);
  $this->Cell($length, 2, $name);
  
  }
  function addPart2($pt2){
  global $x1, $y1;
  $this->SetFont('Arial','B', 10);
  //Part 2
  $this->SetXY($x1+50, $y1+176);
  $length = $this->GetStringWidth($pt2);
  $this->Cell($length, 2, $pt2);
  }
    
  function addRegisteredName($reg, $idnum){
  global $x1, $y1, $tot_width, $db;
  $this->SetFont('Arial','B',9);
  $this->Rect($x1, $y1+179, $tot_width, 12);
  //Registered Name of Employer
  $this->SetXY($x1, $y1+180);
  $length = $this->GetStringWidth($reg);
  $this->Cell($length, 2, $reg);
  $this->Ln(4);
  /*$is_member = $this->checkifmember();  
  if($is_member == 1){
      $sql_1 = "SELECT p.employer AS Employer FROM care_person AS p WHERE p.pid = $this->pid";
      $result=$db->Execute($sql_1);
          
  }
  else{
      $sql_2 = "SELECT p.employer AS Employer FROM care_person AS p WHERE p.pid = $this->member_pid";
      $result=$db->Execute($sql_2);   
  }
  
    if($result){
      $this->_count = $result->RecordCount();
      $row=$result->FetchRow();
      //print_r($row);
      $this->SetX($x1+3);
      $length = $this->GetStringWidth($row['Employer']);
      $this->Cell($length, 2, $row['Employer']);
    } */
  
  //Identification Number of Employer
  $this->SetXY($x1,$y1+188);
  $length = $this->GetStringWidth($idnum);
  $this->Cell($length, 2, $idnum);
  }

  function addAddressOfEmployer($addemp, $street, $city, $bar, $prov, $code){
  global $x1, $y1, $tot_width, $half, $third;
  $this->SetFont('Arial', 'B', 9);
  $this->Rect($x1, $y1+191, $tot_width, 20);
  //Address of Employer
  $this->SetXY($x1, $y1+192);
  $length = $this->GetStringWidth($addemp);
  $this->Cell($length, 2, $addemp);
  //Street
  $this->SetXY($x1+3, $y1+196);
  $length = $this->GetStringWidth($street);
  $this->Cell($length, 2, $street);
  //City
  $this->SetXY($x1+3, $y1+204);
  $length = $this->GetStringWidth($city);
  $this->Cell($length, 2, $city);
  //Barangay
  $this->SetXY($x1+$half, $y1+196);
  $length = $this->GetStringWidth($bar);
  $this->Cell($length, 2, $bar);
  //Province
  $this->SetXY($x1+$half, $y1+204);
  $length = $this->GetStringWidth($prov);
  $this->Cell($length, 2, $prov);
  //Zip Code
  $this->SetXY($x1+($third+20), $y1+204);
  $length = $this->GetStringWidth($code);
  $this->Cell($length, 2, $code);
  }
  

  function addCertOfEmployer($cert, $sig, $date, $cap){
  global $x1, $y1, $tot_width, $half, $third;
  $this->SetFont('Arial', 'B', 8.5);
  $this->Rect($x1, $y1+211, $tot_width, 19);
  //Certification of Employer
  $this->SetXY($x1, $y1+212);
  $length = $this->GetStringWidth($cert);
  $this->MultiCell($length, 3, $cert);
  //Signature
  $this->SetFont('Arial', '', 7);
  $this->Line($x1+3, $y1+226, $x1+85, $y1+226);
  $this->SetXY($x1+10, $y1+227);
  $length = $this->GetStringWidth($sig);
  $this->Cell($length, 2, $sig);
  //Date Signed
  $this->Line($x1+90, $y1+226, $x1+145, $y1+226);
  $this->SetXY($x1+110, $y1+227);
  $length = $this->GetStringWidth($date);
  $this->Cell($length, 2, $date);
  //Official Capacity
  $this->SetXY($x1+170, $y1+227);
  $this->Line($x1+155, $y1+226, $x1+200, $y1+226);
  $length = $this->GetStringWidth($cap);
  $this->Cell($length, 2, $cap);
  }
  
  function addMemberCopy($ack, $nmem, $npat, $nhosp, $sssnum, $cperiod, $rec){
  global $x1, $y1, $half, $third, $tot_width;
  global $db;
  
  //dash
            $this->SetXY($x1,240);
            $this->SetFont('Arial','',6);
            $this->Cell(total_w/8,3,"Cut here",0,0,'L');
            $this->SetXY($x1+12,240);
            $this->SetFont('Arial','',6);
            $PageCellSize=$this->GetStringWidth($t['p'])+2;
            $w=$this->w-$this->lMargin-$this->rMargin-$PageCellSize-($level*8)-($strsize+4);
            $nb=$w/$this->GetStringWidth('-');
            $dots=str_repeat('-',$nb);
            $this->Cell($total_w,$this->FontSize+2,$dots,0,0,'R');
            
  $this->SetFont('Arial', 'B', 8);
  //Member's Copy
  $this->SetXY($x1, $y1+238);
  $length = $this->GetStringWidth("Member's Copy");
  $this->Cell($length, 2, "Member's Copy");
  //This portion...
  $this->SetFont('Arial', 'B', 6);
  $this->SetXY($x1+80, $y1+237);
  $length = $this->GetStringWidth("This portion should be completely filled up, detached by the and given to member.");
  $this->Cell($length, 2, "This portion should be completely filled up, detached by the and given to member.");
  //Acknowledgement Receipt
  $this->SetFont('Arial', 'B', 8);
   $this->SetXY($x1+80, $y1+240);
  $length = $this->GetStringWidth($ack);
  $this->Cell($length, 2, $ack);
  //Name of Member
   //$is_member = $this->checkifmember();
   
   $this->SetXY($x1, $y1+243);
  $length = $this->GetStringWidth($nmem);
  $this->Cell($length, 2, $nmem);
  $this->Line($x1+($length+3), $y1+245, $x1+90, $y1+245);
  $this->SetFont('Arial', '', 8);
  $this->SetX($x1+($length+3));
  $length = $this->GetStringWidth($this->member_name);
  $this->Cell($length, 2, $this->member_name);
  //Name of Patient
  /*$sql = "SELECT p.name_first AS FirstName, p.name_2 AS SecondName, p.name_3 AS ThirdName, 
  p.name_middle AS MiddleName, p.name_last AS LastName FROM care_person AS p
  LEFT JOIN care_encounter AS e ON e.pid = p.pid
  WHERE p.pid = $this->pid";
  $result=$db->Execute($sql);
   */
   $this->SetXY($x1, $y1+246);
   $this->SetFont('Arial', 'B', 8);
   $length = $this->GetStringWidth($npat);
  $this->Cell($length, 2, $npat);
  $this->Line($x1+($length+4), $y1+248, $x1+90, $y1+248);
    //if ($result) {         
     // $this->_count = $result->RecordCount();
     // while ($row=$result->FetchRow()) {
     // $patient_firstname = $row['FirstName']." ".$row['SecondName']." ".$row['ThirdName'];
     // $fullname = $patient_firstname." ".$row['MiddleName']." ".$row['LastName'];
      $this->SetXY($x1+($length+4), $y1+246);
      $length = $this->GetStringWidth($this->patient_name);
      $this->SetFont('Arial', '', 8);
      $this->Cell($length, 2, $this->patient_name);
     // }
   // } 
  
  //Name of Hospital
  $objInfo = new Hospital_Admin();
    
    if ($row = $objInfo->getAllHospitalInfo()) {      
      //$row['hosp_agency'] = strtoupper($row['hosp_agency']);
      $row['hosp_name']   = strtoupper($row['hosp_name']);
    }
    //$hospname = $objInfo->getAllHospitalInfo($row['hosp_name']);
  $this->SetXY($x1, $y1+249);
  $this->SetFont('Arial', 'B', 8);
  $length = $this->GetStringWidth($nhosp);
  $this->Cell($length, 2, $nhosp);
  $this->Line($x1+($length+3), $y1+251, $x1+90, $y1+251);
  $length = $length+3;
  $this->SetXY($x1+$length, $y1+249);
  $this->SetFont('Arial', '', 8);
  $length = $this->GetStringWidth($row['hosp_name']);
  $this->Cell($length, 2, $row['hosp_name'],0,0,'L');
  //SSS/GSIS/MEC/PhilHealth No.
  $this->SetXY($x1+$half, $y1+243);
  $length = $this->GetStringWidth($sssnum);
  $this->Cell($length, 2, $sssnum);
  $this->Line($x1+($third-12), $y1+245, $x1+($tot_width - 6), $y1+245);
  //Confinement Period
  $this->SetXY($x1+$half, $y1+246);
  $length = $this->GetStringWidth($cperiod);
  $this->Cell($length, 2, $cperiod);
  $this->Line($x1+($third-12), $y1+248, $x1+($tot_width - 6), $y1+248);
  //PhilHealth Forms Received by
  $this->SetXY($x1+$half, $y1+249);
  $length = $this->GetStringWidth($rec);
  $this->Cell($length, 2, $rec);
  $this->Line($x1+($third-12), $y1+251, $x1+($tot_width - 6), $y1+251);
  
  }
}
$pdf = new PhilhealthForm1();
$pdf->Open();
$pdf->AddPage();
$pdf->addHead("PHILHEALTH",
              "CLAIM FORM 1",
              "Revised May 2000",
              "Note: This form together with Claim form 2 should be filed with ".
        "Philhealth within 60 calendar days from date of discharge");
$pdf->addDateReceived("(DATE RECEIVED)");
$pdf->addPart1("PART I - MEMBER'S CERTIFICATION (Member to Fill in All Items/Indigent to be Assisted by Hospital Representative)");
$pdf->addTypeOfMembership("1. Type of Membership", 
                          "Identification No.");

$pdf->addMemberInfo("2.Name of Member",
                      "Last Name",
                      "First Name", 
                      "Middle Name",
            "3. Date of Birth",
            "4. Civil Status",
            "5. Sex");


$pdf->addAddressOfMember("6. Address of Member", 
                     "No., Street", 
                     "Municipality/City",
                     "Barangay", 
                     "Province", 
                     "Zip Code");

$pdf->addNameOfSpouse("7. Name of Spouse", 
                      "Last Name", 
                      "Middle Name", 
                      "First Name");

$pdf->addNameOfPatient("8. Name of Patient",  
                       "Last Name",
                       "First Name",
                       "Middle Name",
             "9. Date of Birth",
             "10. Age",
             "11. Sex");

$pdf->addRelOfPatientToMember("12. Relationship of Patient to Member (Check applicable box if patient is a dependent)",
                              "Legitimate spouse who is both an NHIP Member.",
                              "Unmarried and unemployed, legitimate, legitimated, ".
                              "acknowledged \n and illegitimate or legally adopted/step".
                              "child, below 21 years old.",
                              "Parent who is 60 years old and above, not an NHIP member/ \n".
                              "retiree/Pensioner and wholly dependent on me for support.",
                              "Unmarried child 21 years old & above with physical/mental disabi-\n".
                              "lity congenital or acquired and wholly dependent on me for support.");

$pdf->addCertification("13. CERTIFICATION of MEMBER: I certify that the foregoing information are true and correct". 
            "and that the three(3) applicable monthly \n contributions had".
                      "been paid within six (6) month prior to the month of this confinement.",
                      "Signature of Member",
                      "If unable to write, affix Right thumbmark",
                      "Printed Name & Signature of Witness to Thumbmark");

$pdf->addPart2("PART II - EMPLOYER'S CERTIFICATION (For employed members only)");  
                    
//$pdf->addRelOfPatientToMember();

$pdf->addRegisteredName("14. Registered Name of Employer", "Identification No. of Employer");

$pdf->addAddressOfEmployer("15. Address of Employer (No., Street, Barangay/Municipality/City, Province, Zip Code)",
                          "No., Street",
                          "Municipality/City",
                          "Barangay",
                          "Province",
                          "Zip Code");

$pdf->addCertOfEmployer("16. CERTIFICATION of EMPLOYER: This is to certify that three (3) ".
            "applicable monthly cotributions were collected during the\n".
            "six (6) month period prior to the month of this confinement and that ".
            "the data supplied by the member on Part I are true and\n".
            "conform with our available records.",
                        "Signature Over Printed Name of Authorized Representative",
                        "Date Signed",
                        "Official Capacity");           
$pdf->addMemberCopy("ACKNOWLEDGEMENT RECEIPT",
          "Name of Member:",
          "Name of Patient:",
          "Name of Hospital",
          "SSS/GSIS/MEC/PhilHealth No.:",
          "Confinement Period:",
          "PhilHealth Forms Received by:");

//$pdf->Report();            
$pdf->Output();    
?>
