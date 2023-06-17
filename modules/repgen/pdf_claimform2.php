<?php
   error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$x1 = 5;
$y1 = 8;

class PhilHealthForm2{

    function PhilHealthForm2(){
      $this->FPDF('P', 'mm', 'letter');
      $this->SetDrawColor(0,0,0);
      $this->SetMargins(5,2,1);
    }
    
    function _construct(){
    
    }
    
    function setPos(){
    
    }
    
    function calculate(){
    
    }
    
    
}

class HospitalData extends FPDF{
  var $hospital_number;
  var $accreditation_num;
  var $hospital_name;
  var $hosp_street;
  var $hosp_barangay;
  var $hosp_city;
  var $hosp_province;
  var $zipcode;
      
      
        
}

$pdf = new PhilhealthForm2();
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->addHead("PHILHEALTH",
              "CLAIM FORM 2",
              "Revised May 2000",
              "Note: This form together with claim form 1 should not be filed with Philhealth".
              "within 60 calendar days from date of discharge",
              "HEALTH CARE",
              "PROVIDER'S CERTIFICATION",
              "Philhealth Accreditation # H10015203");
$pdf->addDateReceived("(DATE RECEIVED)");
$pdf->addPart1("PART I - HOSPITAL DATA AND CHARGES (Hospital to Fill in All items)");
$pdf->addBillNum("BILL #");
$pdf->addHospitalInfo("1. Hospital No.",
                      "2. Accreditation Category",
                      "3. Name of Hospital/Ambulatory Clinic",
                      "4. Address of Hospital/Ambulatory Clinic",
                      "No., Street",
                      "Municipality/City",
                      "Barangay",
                      "Province",
                      "Zip Code");              
$pdf->addMemberName("5. Name of Member and Identification",
                    "Last Name",
                    "Middle Name",
                    "First Name",
                    "Identification No.");
$pdf->addMemberAddress("6. Address of Member",
                      "No., Street",
                      "Municipality/City",
                      "Barangay",
                      "Province",
                      "Zip Code");
$pdf->addNamePatient("7. Name of Patient",
                    "Last Name",
                    "First Name",
                    "Middle Name",
                    "8. Age",
                    "9. Sex");
$pdf->addDiagnosis("10. Admission Diagnosis");
$pdf->addConfinement("11. Confinement Period",
                    "a. Date Admitted",
                    "b. Time Admitted",
                    "c. Date Discharged",
                    "d. Time Discharged",
                    "e. Claimed No. of Days",
                    "f. Date of Death\n(If Applicable)");
//finish.........
$pdf->addHospAmbServ("12. Hospital/Ambulatory Services",
                      "a. Room and Board",
                      "b. Drugs and Medicines (Part III for details)",
                      "c. X-ray/Lab. Test/Others (Part IV for details)",
                      "d. Operating Room Fee",
                      "e. Medicines bought & laboratory performed\n".
                      "outside hospital during confinement period",
                      "TOTAL");
$pdf->addCertification("13. CERTIFICATION of HOSPITAL/AMBULATORY CLINIC: I certify that".
                      "the services rendered are duly recorded in the patient's chart and\n".
                      "that the information given in this form are true and correct.",
                      "Signature Over Printed Name of Authorized Representative",
                      "Date Signed",
                      "Official Capacity");
$pdf->addPart2("PART II - PROFESSIONAL DATA AND CHARGES (Doctor/s to Fill in Respective Portions)"); 
$pdf->addFinDiagnosis("14. Complete Final Diagnosis");
$pdf->addCaseType("15. Case Type",
                  "Ordinary",
                  "Intensive",
                  "Catastrophic",
                  "Category D");
$pdf->addAttPhysician("16. Name of Attending Physician",
                      "Signature & Date Signed",
                      "17. PHIC Accreditation No.",
                      "18. BIR/TIN No.",
                      "19. Services Performed: Admission of patient, physical\n".
                      "examination, prescription of medicines, daily visit\n".
                      "discharging");
                      
$pdf->addSurgeon("21. Name of Surgeon",
                 "Signature & Date Signed",
                 "22. PHIC Accreditation No.",
                 "23. BIR/TIN No.",
                 "24. Services Performed:",
                 "Date of Operation");
 
$pdf->addAnesthesiologist("26. Name of Anesthesiologist",
                          "Signature &  Date Signed",
                          "27. PHIC Accreditation No.",
                          "28. BIR/TIN No.", 
                          "29. Services Performed"); 

$pdf->addPhilUse("Relative Unit Value",
                "Illness Code",
                "Reduction Code");                          
                          
$pdf->addNote("NOTE: Anyone who supplies flase or incorrect information requested by this or a related form".
              " or commits misrepresentation shall be subject to\n criminal, civil".
              " or administrative prosecution.");                                                                                  

/*$pdf->addPage();

$pdf->addPart3("PART III: DRUGS AND MEDICINES");

$pdf->addPart4("PART IV - X-RAY, LABORATORIES AND OTHERS"); 

$pdf->addPart5("PART V - CERTIFICATION of PATIENT/MEMBER");             
*/
//$pdf->Report();
$pdf->Output();   
?>
