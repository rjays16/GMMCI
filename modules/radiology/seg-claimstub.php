<?php

error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require($root_path . '/modules/repgen/repgen2.inc.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');
require_once($root_path . 'include/care_api_classes/class_radiology.php');
require_once($root_path . 'include/care_api_classes/class_department.php');
require_once($root_path . 'include/care_api_classes/class_person.php');
require_once($root_path . 'include/care_api_classes/class_encounter.php');
require_once($root_path . 'include/care_api_classes/class_personell.php');
require_once($root_path . 'include/care_api_classes/class_ward.php');

/**
 * SegHIS - Hospital Information System (DMC Deployment)
 * Enhanced by Segworks Technologies Corporation
 */
class Radio_List_Request extends RepGen {

    var $date;
    var $colored = TRUE;
    var $pid;
    var $refno;
    var $is_cash;
    var $discount;
    var $total_discount;
    var $total_amount;
    var $parent_refno;
    var $adjusted_amount;
    var $totdiscount;
    var $labServ, $location, $request_name, $attending_doctor, $person, $sex, $enctype;

    function Radio_List_Request($refno, $is_cash) {
        global $db;
        $this->RepGen("PATIENT'S LIST","L","Legal");
        //half of legal size paper 330.2 mm
        //$this->RepGen("CLAIM STUB", "P", array(215.9, 165.1));
        $this->RepGen("CLAIM STUB","P","Legal");
        # 165
        $this->ColumnWidth = array(25, 80, 20, 20, 25, 20);
        #$this->RowHeight = 5;
        $this->RowHeight = 4.5;
        $this->TextHeight = 4;
        $this->Alignment = array('L', 'L', 'C', 'C', 'R');
        $this->PageOrientation = "P";
        #$this->SetAutoPageBreak(FALSE);
        #$this->PageFormat = "Legal";
        $this->LEFTMARGIN = 15;
        $this->DEFAULT_TOPMARGIN = 2;
        $this->NoWrap = false;

        $this->refno = $refno;
        $this->is_cash = $is_cash;
        $this->SetFillColor(0xFF);
        if ($this->colored)
            $this->SetDrawColor(0xDD);
    }

    function BeforeRow() {
        $this->FONTSIZE = 10;
        if ($this->colored) {
            if (($this->ROWNUM % 2) > 0)
            #$this->FILLCOLOR=array(0xee, 0xef, 0xf4);
                $this->FILLCOLOR = array(255, 255, 255);
            else
                $this->FILLCOLOR = array(255, 255, 255);
            $this->DRAWCOLOR = array(0xDD, 0xDD, 0xDD);
        }
    }

    function BeforeData() {
        global $root_path, $db;
        if ($this->colored) {
            $this->DrawColor = array(0xDD, 0xDD, 0xDD);
        }
    }

    function AfterRowRender() {
        
    }

    /* ----------------------------------added by art 01/30/2014---------------------------------- */

    function incharge() {
//        $this->Ln(10);
//        $this->SetFont('Arial', '', 8);
//        $this->Cell(190, 4, 'i________________________________________', "", 1, 'R');
//        $this->Cell(190, 4, 'Person In-Charge (Signature Over Printed Name)', "", 1, 'R');
//        $this->Ln(1);
    }

    function authorization() {
        if ($this->GetY() > 111) {
            $this->page();
            $this->AddPage();
            $this->setY(50);
        }
    //     $this->SetFont('', 'UB', 8);
    //     $this->Cell(0, 4, 'AUTHORIZATION LETTER', "", 1, 'C');
    //     $this->SetFont('', 'I', 8);
    //     $this->Cell(0, 4, '(Sulat sa Pagtugot)', "", 1, 'C');
    //     $this->Cell(0, 4, 'Petsa(Date): ________________', "", 1, 'R');
    //     $this->Ln(2);
    //     $this->Cell(9, 4, 'Ako si ', "", 0, 'L');
    //     $this->SetFont('Arial', '', 8);
    //     $this->Cell(94, 4, '(I)______________________________________ (Pangalan sa Pasyente)', "", 0, 'L');
    //     $this->SetFont('', 'I', 8);
    //     $this->Cell(0, 4, 'mihatag og pagtugot ni ____________________________________', "", 1, 'L');
    //     $this->SetFont('Arial', '', 8);
    //     $this->Cell(50, 4, '(Name of Patient)', "", 0, 'R');
    //     $this->Cell(100, 4, '', "", 0, 'R');
    //     $this->Cell(0, 4, '(Name of Claimant)', "", 1, 'L');
    //     $this->SetFont('', 'I', 8);
    //     $this->Cell(72, 4, '(Pangalan sa Mokuha) nga mokuha sa resulta sa akong', "", 0, 'L');
    //     $this->SetFont('Arial', 'B', 8);
    //     $this->Cell(0, 4, '( )xray    ( )Ultrasound    ( )CT-scan    ( )MRI.', "", 1, 'L');
    //     $this->Ln(3);
    //     $this->SetFont('Arial', '', 8);
    //     $this->Cell(90, 4, '________________________________', "", 0, 'L');
    //     $this->Cell(0, 4, '________________________________', "", 1, 'R');
    //     $this->Cell(140, 4, '     Pirma o thumbmark sa Pasyente', "", 0, 'L');
    //     $this->Cell(0, 4, '     Pirma o thumbmark sa Gitugutan', "", 0, 'L');
    }

    function renderClaimStubInfo() {
        $this->SetFont("Arial", "", "8");
        $this->Cell(23, 4, 'HOSPITAL NO. : ', 0, 0, 'L');
        $this->SetFont("Arial", "B", "12");
        $this->Cell(85, 4, $this->labServ['pid'], 0, 0, 'L');
        $this->SetFont("Arial", "", "8");
        //$this->Cell(27, 4, 'LOCATION : ', 0, 0, 'L');
        $this->SetFont("Arial", "B", "9");
        $this->Cell(50, 4, $this->location, 0, 1, 'L');
//            $this->SetFont("Arial", "", "12");
//            $this->Cell(30, 4, 'HOSP # : ', 0, 0, 'L');
//            $this->SetFont("Arial", "B", "14");
//            $this->Cell(50, 4, $this->labServ['pid'], 0, 0, 'L');
//            $this->SetFont("Arial", "", "8");

        $this->SetFont("Arial", "", "8");
        $this->Cell(15, 4, 'NAME : ', 0, 0, 'L');
        $this->SetFont("Arial", "B", "9");
        $this->Cell(93, 4, mb_strtoupper($this->request_name), 0, 0, 'L');
        $this->SetFont("Arial", "", "8");
        $this->Cell(27, 4, 'REQUESTING DR. : ', 0, 0, 'L');
        $this->SetFont("Arial", "B", "9");
        $this->MultiCell(50, 4, strtoupper($this->attending_doctor), 0, 1, 'L');

//            $this->SetFont("Arial", "", "8");
//            $this->Cell(30, 4, 'REFERENCE NO. : ', 0, 0, 'L');
//            $this->SetFont("Arial", "B", "9");
//            $this->Cell(50, 4, $this->refno, 0, 0, 'L');
//            $this->SetFont("Arial", "", "8");
//            $this->Cell(30, 4, 'BIRTH DATE : ', 0, 0, 'L');
//            $this->SetFont("Arial", "B", "10");
//            $this->Cell(40, 4, date('F d, Y', strtotime($this->person['date_birth'])), 0, 0, 'L');
        $this->SetFont("Arial", "", "8");
        $this->Cell(15, 4, 'AGE : ', 0, 0, 'L');
        $this->SetFont("Arial", "B", "9");
        $this->Cell(19, 4, $this->person['age'], 0, 0, 'L');

        $this->SetFont("Arial", "", "8");
        $this->Cell(15, 4, 'GENDER : ', 0, 0, 'L');
        $this->SetFont("Arial", "B", "9");

        if ($this->person['sex'] == 'm')
            $this->sex = 'Male';
        elseif ($this->person['sex'] == 'f')
            $this->sex = 'Female';
        else
            $this->sex = 'Unspecified';

        $this->Cell(59, 4, $this->sex, 0, 0, 'L');
        $this->SetFont("Arial", "", "8");
        $this->Cell(27, 4, 'DATE OF EXAM : ', 0, 0, 'L');
        $this->SetFont("Arial", "B", "9");
        $this->Cell(50, 4, date("F j, Y", strtotime($this->labServ['request_date'])) . " at " . date("h:i A", strtotime($this->labServ['request_time'])), 0, 1, 'L');
        $this->SetFont("Arial", "", "8");
        $this->Cell(23, 4, 'PATIENT TYPE : ', 0, 0, 'L');
        $this->SetFont("Arial", "B", "9");
        $this->Cell(85, 4, $this->enctype, 0, 0, 'L');
        $this->SetFont("Arial", "", "8");
        $this->Cell(43, 4, 'EXPECTED DATE OF RESULT : ', 0, 0, 'L');
        $this->SetFont("Arial", "B", "9");
        $this->Cell(50, 4, date("F j, Y", strtotime($this->labServ['datereleased'])), 0, 0, 'L');
    }

    function renderFooterNote() {
        // $this->ln(4);
        // $this->SetFont('Arial', 'UB', 8);
        // $this->MultiCell(0, 4, 'NO CLAIM STUB - NO RELEASE OF RESULT', 0, 'C');
        // $this->SetFont('Arial', 'I', 7);
        // $note = '(Pahimangno: Kung dili ang pasyente mismo ang mokuha sa resulta, ' .
        //         'gikinahanglan ang mga mosunod: Claim Stub, Valid ID sa pasyente, ' .
        //         'Valid ID sa mokuha sa resulta ug ';
        // $this->Cell(200, 3, $note, 0, 1, 'L');
        // $this->Cell(37.5, 2, 'pinirmahan nga Sulat sa Pagtugot', 0, '', 'L');
        // $this->setFont('Arial', '', 7);
        // $this->Cell(200, 2, '(Authorization Letter)', 0, "", 'L');
    }

    function page() {
        // $this->SetAutoPageBreak(TRUE, 1);
        // $this->SetFont('Arial', 'B', 8);
        // $this->setY(-13);
        // $this->Cell(0, 4, "SPMC-F-RAD-06", "", 1, 'R');
        // $this->SetFont('Arial', '', 8);
        // $this->Cell(60, 8, 'Effectivity : October 1, 2013', 0, 0, 'L');
        // $this->Cell(80, 8, 'Revision : 0', 0, 0, 'C');
        // $this->Cell(50, 8, 'Page ' . $this->PageNo() . ' of {nb}', 0, 0, 'R');
    }

    /* ------------------------end art------------------------------- */

    function getFooter() {
        /* commented by art 01/30/2014
          $this->Ln(10);
          $this->SetFont('Arial','',8);
          $this->Cell(190,4,'________________________________________',"",1,'R');
          $this->Cell(190,4,'Person In-Charge (Signature Over Printed Name)',"",1,'R');
          $this->Ln(5); */
        /* if ($this->deptname=='XRAY')
          $dept_name = 'XRAY';
          elseif ($this->deptname=='USD')
          $dept_name = 'Ultrasound';
          elseif ($this->deptname=='CT-SCAN')
          $dept_name = 'CT-Scan'; */

        if ($this->dept_label == 'XRAY') {
            /*
              $note = "Note: Please claim your ".$this->dept_label." Result at your respective clinics.";
              $note1 = "Palihug kuha-a ang Resulta sa inyong clinic.";
              $this->Cell(190,4,$note,"",1,'L');
              $this->Cell(8,4,'',"",0,'R');
              $this->Cell(190,4,$note1,"",0,'L');
              commented by art 01/21/2014 */
        }
        /*
          $this->SetFont('Arial','',5);
          $this->Ln(27);
          $this->Cell(150, 3 , '', "", 0,'');
          $this->Cell(0, 3 ,$this->rValue.date("F d, o")." REVO", "", 0,'');
          commented by art 01/21/2014 */
        //added by art 01/30/2014
//        $this->incharge();
        $this->Ln(5);
        $this->authorization();
        $this->renderFooterNote();
        $this->page();
        //end
    }

    function BeforeRowRender() {
        global $root_path, $db;
        $objInfo = new Hospital_Admin();
        $srvObj = new SegRadio;
        $dept_obj = new Department;
        $this->person_obj = new Person;
        $enc_obj = new Encounter;
        $pers_obj = new Personell;
        $ward_obj = new Ward;

        $borderYes = "1";
        $borderNo = "0";
        $newLineYes = "1";
        $newLineNo = "0";
        $space = 2;
        #echo "<br>".$this->RENDERROW[2]->Text." = ".$this->CurrentLabSection;
        if (($this->CurrentLabSection) && ($this->RENDERROW[2]->Text)) {
            if ($this->RENDERROW[2]->Text != $this->CurrentLabSection) {
                $this->getFooter();
                $this->AddPage();
            }
        }

        if ($this->RENDERROW[2]->Text != $this->CurrentLabSection) {
            #$this->AddPage();
            #$this->Ln(10);
            #$x = $this->GetX();
            #$y = $this->GetY();
            #$this->Line($x, $y, 200, $y);
            #$this->Ln(10);
            // Output header
            if ($row = $objInfo->getAllHospitalInfo()) {
                $row['hosp_agency'] = strtoupper($row['hosp_agency']);
                $row['hosp_name'] = strtoupper($row['hosp_name']);
            } else {
                $row['hosp_country'] = "Republic of the Philippines";
                $row['hosp_agency'] = "DEPARTMENT OF HEALTH";
                $row['hosp_name'] = "Gonzales Maranan Medical Center Incorporated";
                $row['hosp_addr1'] = "Quezon Ave., Digos City";
            }

            $total_w = 0;
            $this->Image($root_path . 'gui/img/logos/dmc_logo.jpg', 20, 8, 20);
            $this->SetFont("Arial", "I", "9");

            $this->Cell($total_w, 4, $row['hosp_country'], $border2, 1, 'C');
            $this->Cell($total_w, 4, $row['hosp_agency'], $border2, 1, 'C');
            $this->Ln(2);
            $this->SetFont("Arial", "B", "10");
            $this->Cell($total_w, 4, $row['hosp_name'], $border2, 1, 'C');
            $this->SetFont("Arial", "", "9");
            $this->Cell($total_w, 4, $row['hosp_addr1'], $border2, 1, 'C');
            $this->Ln(4);
            $this->SetFont('Arial', 'B', 12);
            $this->Cell($total_w, 4, 'DEPARTMENT OF RADIOLOGICAL & IMAGING SCIENCES', $border2, 1, 'C');
            $this->Ln(2);

            $this->SetFont('Arial', 'B', 10);

            if ($this->RENDERROW[2]->Text == 'XRAY')
            //$label = 'CLAIM STUB (RECEIVED REQUEST)'; commented by art 01/21/2014
                $label = 'CLAIM STUB'; //added by art 01/21/2014
            else
            //$label = 'PATIENT REQUEST'; commented by art 01/21/2014
                $label = 'CLAIM STUB'; //added by art 01/21/2014
            $this->Cell($total_w, 4, $label, $border2, 1, 'C');

            $day = $srvObj->getDayOfWeek($this->refno);
            # echo "day = ".$day["day_name"];
            #3 working days
            switch ($day["day_name"]) {
                case 'Monday' :
                case 'Tuesday' : $day_interval = 3;
                    break;
                case 'Wednesday' :
                case 'Thursday' :
                case 'Friday' : $day_interval = 5;
                    break;
                case 'Saturday' : $day_interval = 4;
                    break;
                case 'Sunday' : $day_interval = 3;
                    break;
            }

            # echo "<br>interval = ".$day_interval;

            $this->labServ = $srvObj->getRadioServiceReqInfo($this->refno, $day_interval);

            #echo "here = ".$srvObj->sql;
            $this->labServ_details = $srvObj->getRequestInfo($this->refno);
            #print_r($this->labServ_details);
            #echo "here = ".$srvObj->sql;
            $this->parent_refno = $this->labServ['parent_refno'];

            #$this->person = $enc_obj->getEncounterInfo($this->labServ['encounter_nr']);
            if (trim($this->labServ['encounter_nr'])) {
                $this->person = $enc_obj->getEncounterInfo($this->labServ['encounter_nr']);
            } else {
                $this->person = $this->person_obj->getAllInfoArray($this->labServ['pid']);
            }
            #print_r($this->person);
            if ($this->labServ['encounter_nr'] == 0) {
                $this->request_name = $this->labServ['ordername'];
                $request_address = $this->labServ['orderaddress'];
            } else {
                #$this->request_name = $this->person['name_first']." ".$this->person['name_2']." ".$this->person['name_middle']." ".$this->person['name_last'];
                $this->request_name = $this->person['name_last'] . ", " . $this->person['name_first'] . " " . $this->person['name_middle'];
                $this->request_name = ucwords(strtolower($this->request_name));
                $this->request_name = htmlspecialchars($this->request_name);

                # $request_address = $this->person['street_name']." ".$this->person['brgy_name']." ".$this->person['mun_name']." ".$this->person['prov_name']." ".$this->person['zipcode'];
                if ($street_name)
                    $street_name = "$street_name ";
                else
                    $street_name = "";

                if ($brgy_name == 'NOT PROVIDED')
                    $brgy_name = "";

                if (!($brgy_name))
                    $brgy_name = "";
                else
                    $brgy_name = ", " . $brgy_name . ", ";

                if ($mun_name == 'NOT PROVIDED')
                    $mun_name = "";

                if ($prov_name != 'NOT PROVIDED') {
                    if (stristr(trim($mun_name), 'city') === FALSE) {
                        if (!empty($mun_name)) {
                            $province = ", " . trim($prov_name);
                        } else {
                            $province = trim($prov_name);
                            ;
                        }
                    }
                } else {
                    $province = "";
                }

                #$address = trim($street_name)." ".trim($brgy_name).", ".trim($mun_name)." ".trim($zipcode)." ".trim($prov_name);
                $address = trim($street_name) . " " . trim($brgy_name) . trim($mun_name) . " " . $province;

                $this->request_name = ucwords(strtolower($this->request_name));
                $this->request_name = htmlspecialchars($this->request_name);
            }

            if ($this->person['er_opd_diagnosis']) {
                $impression = $this->person['er_opd_diagnosis'];
            } else {
                $impression = $this->person['chief_complaint'];
            }

            $request_info = $srvObj->getRequestInfo($this->refno);

            if (empty($impression)) {
                $impression = $request_info['clinical_info'];
            }

            $doctor_nr = $this->person['current_att_dr_nr'];

            if (empty($doctor_nr)) {
                $doctor_nr = $request_info['request_doctor'];
            }

            if ($doctor_nr != '0') {
                $drInfo = $pers_obj->getPersonellInfo($doctor_nr);
                if (!empty($drInfo["name_middle"]))
                    $middleInitial = substr(trim($drInfo["name_middle"]), 0, 1) . ".";

                $name_doctor = trim(trim($drInfo["name_first"]) . " " . $middleInitial . " " . $drInfo["name_last"]); #substr(trim($drInfo["name_middle"]),0,1).$dot;
                $this->attending_doctor = ucwords(strtolower($name_doctor)) . ", MD";
                //$this->attending_doctor = $doctor['name_last'].", ".$doctor['name_first']." ".$
            }else {
                $this->attending_doctor = "";
            }

            if ($this->person['encounter_type']) {
                if ($this->person['encounter_type'] == 1) {
                    $this->enctype = "ER PATIENT";
                    $this->location = "EMERGENCY ROOM";
                } elseif ($this->person['encounter_type'] == 2) {
                    $this->enctype = "OUTPATIENT (OPD)";
                    $dept = $dept_obj->getDeptAllInfo($this->person['current_dept_nr']);
                    $this->location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
                } elseif (($this->person['encounter_type'] == 3) || ($this->person['encounter_type'] == 4)) {
                    $this->enctype = "INPATIENT";
                    $ward = $ward_obj->getWardInfo($this->person['current_ward_nr']);
                    #echo "sql = ".$ward_obj->sql;
                    $this->location = strtoupper(strtolower(stripslashes($ward['name'])));
                } else {
                    $this->enctype = "OUTPATIENT (OPD)";
                }
            } else {
                $this->enctype = "WALKIN PATIENT";
                $this->location = "";
            }


            $this->renderClaimStubInfo();

            if ($this->RENDERROW[2]->Text == 'USD') {
                $this->dept_label = "ULTRASOUND";
                if ($this->enctype == "OUTPATIENT (OPD)")
                    $this->rValue = "SPMC-F-RAD-10  ";
                if ($this->enctype == "ER PATIENT")
                    $this->rValue = "SPMC-F-RAD-11  ";
                if ($this->enctype == "INPATIENT")
                    $this->rValue = "SPMC-F-RAD-12  ";
            }

            elseif (($this->RENDERROW[2]->Text == 'XRAY') || ($this->RENDERROW[2]->Text == 'SPL')) {
                $this->dept_label = "XRAY";
                $this->rValue = "SPMC-F-RAD-06  ";
            } elseif ($this->RENDERROW[2]->Text == 'CT-SCAN') {
                $this->dept_label = "CT-SCAN";
                $this->rValue = "SPMC-F-RAD-05  ";
            } elseif ($this->RENDERROW[2]->Text == 'MRI') {
                $this->dept_label = "MRI";
                $this->rValue = "SPMC-F-RAD-09  ";
            } else {
                $this->dept_label = "XRAY";
                $this->rValue = "SPMC-F-RAD-06  ";
            }

            if ($this->dept_label == 'XRAY' || $this->dept_label == 'MRI' || $this->dept_label == 'ULTRASOUND' || $this->dept_label == 'CT-SCAN') {//added by art 01/23/2014
                $this->SetFont("Arial", "", "8");

//                $this->Cell(30, 4, 'RELEASE DATE : ', $borderNo, $newLineno, 'L');
//                $this->SetFont($fontStyle, "", $fontSizeLabel - 4);
//                $this->SetFont("Arial", "B", "9");
//                $this->Cell(50, 4, '_________________________', borderNo, $newLineNo, 'L'); //added by art 01/23/2014
                $this->rValue = "SPMC-F-RAD-06  ";
            }

            $this->Ln(4);
//            $this->SetFont("Arial", "", "8");
//            $this->Cell(30, 4, 'PAYMENT TYPE : ', $borderNo, $newLineno, 'L');
//            $this->SetFont("Arial", "B", "9");
//            if ($this->is_cash) {
//                if ($this->labServ["type_charge"])
//                    $this->Cell(85, 4, $this->labServ["charge_name"], $borderNo, $newLineYes, 'L');
//                else
//                    $this->Cell(85, 4, 'CASH', $borderNo, $newLineNo, 'L');
//            }else
//                $this->Cell(85, 4, 'CHARGE', $borderNo, $newLineNo, 'L');

            $this->Ln(4);
//           
//            $this->SetFont("Arial", "", "8");
//            $this->Cell(35, 4, 'CLINICAL IMPRESSION : ', $borderNo, $newLineNo, 'L');
//            $this->SetFont("Arial", "B", "9");
//            $this->Cell(225, 4, $impression, $borderNo, $newLineNo, 'L');
//
//            $this->SetFont('Arial', 'B', 9);
//            $this->Cell(17, 5);

            

            # Print table header

            $this->SetFont('ARIAL', 'B', 8);
            #if ($this->colored) $this->SetFillColor(0xED);
            if ($this->colored)
                $this->SetFillColor(255);
            $this->SetTextColor(0);
            $row = 6;
            #$this->Cell(0,4,'',1,1,'C');
            $this->Ln(2);
            $this->SetFont("Arial", "B", "10");

            $this->Cell(20, 4, "SECTION : " . $this->dept_label, '', 1, 'L');
            
            $this->Cell($this->ColumnWidth[0], $row, 'CODE', 1, 0, 'C', 1);
            $this->Cell($this->ColumnWidth[1], $row, 'DESCRIPTION', 1, 0, 'C', 1);
            $this->Cell($this->ColumnWidth[2], $row, 'SECTION', 1, 0, 'C', 1);
            $this->Cell($this->ColumnWidth[3], $row, 'OR NO.', 1, 0, 'C', 1);
            $this->Cell($this->ColumnWidth[4], $row, 'FILM #', 1, 0, 'C', 1);
            $this->Cell($this->ColumnWidth[5], $row, 'PRICE', 1, 0, 'C', 1);
            $this->Ln();

            $this->CurrentLabSection = $this->RENDERROW[2]->Text;
            $this->CurrentLabService = $this->RENDERROW[0]->Text;
            $this->RENDERROWX = $this->GetX();
            $this->RENDERROWY = $this->GetY();
        }
    }

    function BeforeCellRender() {
        $this->FONTSIZE = 8;
        if ($this->colored) {
            if (($this->RENDERPAGEROWNUM % 2) > 0)
            #$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
                $this->RENDERCELL->FillColor = array(255, 255, 255);
            else
                $this->RENDERCELL->FillColor = array(255, 255, 255);
        }
    }

    function AfterData() {
        global $db;
        $srvObj = new SegRadio;

        if (!$this->_count) {
            $this->SetFont('Arial', 'B', 10);
            $this->SetFillColor(255);
            $this->SetTextColor(0);
            $this->Cell(195, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
        }
        $this->getFooter();
        $cols = array();
    }

    function FetchData($refno, $is_cash) {
        global $db;
        $srvObj = new SegRadio;

        if ($is_cash)
            $mod = 1;
        else
            $mod = 0;

        $servreqObj = $srvObj->getRequestedServices($refno, $mod);
        #echo "sql = ".$srvObj->sql;
        $this->_count = $srvObj->count;

        if ($servreqObj) {
            while ($result = $servreqObj->FetchRow()) {
                if ($result['is_cash']) {
                    if ($result["request_flag"]) {
                        if ($result["request_flag"] == 'paid') {
                            $sql_paid = "SELECT pr.or_no, pr.ref_no,pr.service_code, pr.amount_due
																	FROM seg_pay_request AS pr
																	INNER JOIN seg_pay AS p ON p.or_no=pr.or_no AND p.pid='" . $result["pid"] . "'
																	WHERE pr.ref_source = 'LD' AND pr.ref_no = '" . trim($result["refno"]) . "'
																	AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00') LIMIT 1";
                            $rs_paid = $db->Execute($sql_paid);
                            if ($rs_paid) {
                                $result2 = $rs_paid->FetchRow();
                                $or_no = $result2['or_no'];
                                $price = $result2['amount_due'];
                            }
                        } elseif ($result["request_flag"] == 'charity') {
                            $sql_paid = "SELECT pr.grant_no AS or_no, pr.ref_no,pr.service_code
																	FROM seg_granted_request AS pr
																	WHERE pr.ref_source = 'LD' AND pr.ref_no = '" . trim($result["refno"]) . "'
																	LIMIT 1";

                            $rs_paid = $db->Execute($sql_paid);
                            if ($rs_paid) {
                                $result2 = $rs_paid->FetchRow();
                                $or_no = 'CLASS D';
                                $price = $result['price_cash'];
                            }
                        } elseif (($result["request_flag"] != NULL) || ($result_paid["request_flag"] != "")) {
                            if ($withOR)
                                $or_no = $off_rec;
                            else
                                $or_no = $result["charge_name"];
                        }
                    }else {
                        $service = $result['service_code'];
                        $sql_amount = "select price_cash from seg_radio_services  WHERE service_code = '$service';";
                        $res = $db->Execute($sql_amount);
                        if ($res) {
                            $amt = $res->FetchRow();
                            $price = $amt['price_cash'];
                        }
                        $or_no = "unpaid";
                    }
                } else {
                    $or_no = "charge";
                    $nocharge = 0;
                    $price = number_format($nocharge, 2, '.', ',');
                }

                $this->Data[] = array(
                    $result['service_code'],
                    $result['name'],
                    $result['name_short'],
                    $or_no,
                    $result['batch_nr'],
                    $price
                );
            }
        } else {
            #print_r($srvObj->sql);
            print_r($db->ErrorMsg());
            exit;
        }
    }

}

$refno = $_GET['refno'];
$is_cash = $_GET['is_cash'];
#echo "refno = ".$refno;
#echo "cash = ".$is_cash;
$iss = new Radio_List_Request($refno, $is_cash);
$iss->AliasNbPages();
$iss->FetchData($refno, $is_cash);
$iss->Report();
?>