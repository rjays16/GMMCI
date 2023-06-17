<?php
require('./roots.php');
require($root_path."/classes/fpdf/pdf.class.php");
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_person.php');                                       
require_once($root_path.'include/care_api_classes/billing/class_ops.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_radiology.php');
require_once($root_path.'include/inc_date_format_functions.php');
include_once($root_path.'include/care_api_classes/class_encounter.php');

class ClinicalHistory extends FPDF{
    var $from;
    var $to;
    //var $count_rows;
    var $refno;
    var $encounter_nr;
    var $pid;
    var $batch_nr;

    function ClinicalHistory($pid, $refno, $grp, $batch_nr){
        global $db;
        $radio_obj = new SegRadio;
        
       // echo $refno;
       // echo $pid;
        
        $this->ColumnWidth = array(50,28,28,15,71,12,70,25,53);
        $this->SetTopMargin(3);
        $this->Alignment = array('L','C','C','C','L','C','L','L','L');
        $this->FPDF("P", 'mm', 'Legal');
        $this->pid = $pid;

        if(!$refno){
            $refno = 0;
        }

        $this->refno = $refno;
        $this->batchNr = $batch_nr;
        $this->grp = $grp;

        $mrihistoryInfo = $radio_obj->getMriHistoryInfoPDF($pid,$refno,$grp);
        $radioRequestInfo = $radio_obj->getAllRadioInfoByBatch($batch_nr);
        $radioRequestData = $radio_obj->getRadioRequestdata($this->refno,$this->batchNr);
        
        $this->encounter_nr = $mrihistoryInfo['encounter_nr'];
        $this->stat = $mrihistoryInfo['priority'];
        $this->request = $radioRequestInfo['service_name'];
        $this->request_prc = number_format($radioRequestData['price_charge'], 2, '.', ',');
        //$radioRequestData['amount_due'];
        ///test
        $this->dept = $radioRequestInfo['service_dept_name'];
    }

    function Header() {

    }
    /*
    function Header() {
        global $root_path, $db;
        $rowheight = 7;
        $objInfo = new Hospital_Admin();

         if ($row = $objInfo->getAllHospitalInfo()) {
            $row['hosp_agency'] = strtoupper($row['hosp_agency']);
            $row['hosp_name']   = strtoupper($row['hosp_name']);
        }
        else {
            $row['hosp_country'] = "Republic of the Philippines";
            $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
            $row['hosp_name']    = "SOUTHERN PHILIPPINES MEDICAL CENTER";
            $row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
        }

        
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, $rowheight, $row['hosp_name'], 0,1,'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, $rowheight, "Department of Radiological and Imaging Sciences", 0,1,'C');
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, $rowheight, "CLINICAL HISTORY", 0,1,'C');
        $this->Ln();

    }
    */

    function GetData(){
        global $root_path, $db;
        
        $rowheight = 7;
        $objInfo = new Hospital_Admin();

         if ($row = $objInfo->getAllHospitalInfo()) {
            $row['hosp_agency'] = strtoupper($row['hosp_agency']);
            $row['hosp_name']   = strtoupper($row['hosp_name']);
        }
        else {
            $row['hosp_country'] = "Republic of the Philippines";
            $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
            $row['hosp_name']    = "SOUTHERN PHILIPPINES MEDICAL CENTER";
            $row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
        }

        
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, $rowheight, $row['hosp_name'], 0,1,'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, $rowheight, "Department of Radiological and Imaging Sciences", 0,1,'C');
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, $rowheight, "CLINICAL HISTORY", 0,1,'C');
        $this->Ln();
        
        $rowheight = 4;
        $radio_obj = new SegRadio;
        $enc_obj = new Encounter;

        $mrihistoryInfo = $radio_obj->getMriHistoryInfo($this->pid,$this->refno,$this->grp);   //Added by Cherry 08-05-10
        $this->encounter_nr = $mrihistoryInfo['encounter_nr'];
        $encInfo=$enc_obj->getEncounterInfo($this->encounter_nr);
        $radioRequestData = $radio_obj->getRadioRequestdata($this->refno,$this->batchNr);
        
        #echo $enc_obj->sql;
        #print_r($encInfo);

        if($encInfo['encounter_type']=='1'){
            $patient_type = "ER";
            $impression = $encInfo['chief_complaint'];
        }else if($encInfo['encounter_type']=='2'){
            $patient_type = "OPD";
            $impression = $encInfo['chief_complaint'];
        }else if($encInfo['encounter_type']=='3' || $encInfo['encounter_type']=='4'){
            $patient_type = "In-Patient";
            $impression = $encInfo['er_opd_diagnosis'];
        }else{
            $patient_type = "Walk In";
        }
        $seg_person = new Person($this->pid);
        $person_info = $seg_person->getAllInfoArray();
        //print_r($person_info);
        $middle_initial = (strnatcasecmp($person_info['name_middle'][0], $person_info['name_middle'][1]) == 0) ? ucwords(substr($person_info['name_middle'], 0, 2)) : strtoupper($person_info['name_middle'][0]);
        $person_name = $person_info['name_last'] . ', ' . $person_info['name_first'] . ' ' . $middle_initial;
        $person_gender = (strnatcasecmp($person_info['sex'], 'm') == 0) ? 'Male' : 'Female';
        $person_age = (int)$seg_person->getAge(date('m/d/Y', strtotime($person_info['date_birth'])));
        $person_age = is_int($person_age) ? $person_age . ' years old' : '';

        //added by Francis L.G 04-05-13
        if($this->stat=='1'){
            $stat = "STAT";    
        }
        else{
            $stat = "Routine";
        }
        
        if($mrihistoryInfo['transaction']){
            $transaction = $mrihistoryInfo['transaction'];
            if(strtolower($transaction)=="cash"){
                if($radioRequestData['request_flag']){
                    if(strtolower($radioRequestData['request_flag'])=="paid"){
                        if($radioRequestData['or_no']) $transaction = "PAID(OR#".$radioRequestData['or_no'].")";
                    }
                    else{
                    $transaction = "PAID(".$radioRequestData['request_flag'].")";    
                    }
                }
                else{
                    $transaction = "UNPAID";
                }
            }
        }
        else{
            $transaction = "TPL";
        }
        
        $transaction = strtoupper($transaction);
        
        //person details
        $this->SetFont('Arial', '', 9);
        $this->Cell(12, $rowheight, "NAME:", 0, 0,'L');
            $x = $this->GetX();
            $y = $this->GetY();
        $this->Cell(65, $rowheight, $person_name, 0,0,'L');
        $this->Line($x, $y+($rowheight - 0.5), $x+65, $y+($rowheight - 0.5));
        $this->Cell(17, $rowheight, "AGE/SEX:", 0, 0, 'L');
            $x = $this->GetX();
            $y = $this->GetY();
        $this->Cell(35, $rowheight, $person_age."/".$person_gender, 0,0,'L');
        $this->Line($x, $y+($rowheight - 0.5), $x+35, $y+($rowheight - 0.5));

        if($this->batchNr){
            $this->Cell(11, $rowheight, "DATE:", 0, 0, 'L');
                $x = $this->GetX();
                $y = $this->GetY();

            $reqDateTmp = $mrihistoryInfo['request_date'];
            $reqDate = date('M d,Y', strtotime($reqDateTmp));

        $this->Cell(25, $rowheight, $reqDate, 0, 0, 'L');
        $this->Line($x, $y+($rowheight - 0.5), $x+25, $y+($rowheight - 0.5));
        }

        $this->Cell(10, $rowheight, "HRN#", 0, 0, 'L');
            $x = $this->GetX();
            $y = $this->GetY();
        $this->Cell(20, $rowheight, $this->pid, 0, 1, 'L');
        $this->Line($x, $y+($rowheight-0.5), $x+20, $y+($rowheight-0.5));
        
        $this->Ln(1);
        
        if($this->batchNr){
            $this->Cell(20, $rowheight, "Patient Type:", 0, 0, 'L');
                $x = $this->GetX();
                $y = $this->GetY();
            $this->Cell(20, $rowheight, $patient_type, 0, 0, 'L');
            $this->Line($x, $y+($rowheight - 0.5), $x+20, $y+($rowheight - 0.5));
            $this->Cell(1, $rowheight);

            $this->SetFont('Arial', '', 9);
            $this->Cell(23, $rowheight, "Classification: ", 0,0,'L');
                $x = $this->GetX();
                $y = $this->GetY();
            $this->SetFont('Arial', '', 9);
            $this->Cell(25, $rowheight, $encInfo['discountid'], 0,0,'L');
            $this->Line($x, $y+($rowheight - 0.5), $x+25, $y+($rowheight - 0.5));
            $this->Cell(2, $rowheight);
            
            $this->SetFont('Arial', '', 9);
            $this->Cell(28, $rowheight, "Transaction Type: ", 0,0,'L');
                $x = $this->GetX();
                $y = $this->GetY();
            $this->Cell(45, $rowheight, $transaction, 0,0,'L');
            $this->Line($x, $y+($rowheight - 0.5), $x+45, $y+($rowheight - 0.5));
            $this->Cell(2, $rowheight);
            
            $this->Cell(13, $rowheight, "Amount: ", 0,0,'L');
                $x = $this->GetX();
                $y = $this->GetY();
            $this->Cell(17, $rowheight, $this->request_prc, 0,1,'L');
            $this->Line($x, $y+($rowheight - 0.5), $x+17, $y+($rowheight - 0.5));
            
            $this->Ln(1);
            
            $this->SetFont('Arial', '', 9);
            $this->Cell(30, $rowheight, "Radiology Request: ", 0,0,'L');
                $x = $this->GetX();
                $y = $this->GetY();
            $this->Cell(80, $rowheight, $this->request." (".$this->dept.") ", 0,0,'L');
            $this->Line($x, $y+($rowheight - 0.5), $x+80, $y+($rowheight - 0.5));
            $this->Cell(15, $rowheight);
            
            $this->Cell(15, $rowheight, "Priority: ", 0,0,'L');
                $x = $this->GetX();
                $y = $this->GetY();
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(20, $rowheight, $stat, 0,1,'L');
            $this->Line($x, $y+($rowheight - 0.5), $x+20, $y+($rowheight - 0.5));
            $this->Cell(5, $rowheight);
         }

        $this->Ln();
        
        $mri = $mrihistoryInfo['purpose'];
        if($mri=='1'){
            $purpose = " F1 (for diagnosis) MRI is initial imaging modality for diagnosis ";
        }
        else if($mri=='2'){
            $purpose = "F2 (for further investigation) Secondary imaging, diagnosis uncertain or to assess extent of severity of condition ";    
        }
        else if($mri=='3'){
            $purpose = " F3 (for monitoring) Diagnosis is confirmed, to assess progress following treatment ";    
        }
        else{
            $purpose = "";    
        }

        $y = $this->GetY();
        
        $this->SetY($y+1);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(200, $rowheight, "MRI : ".$purpose, 0, 1, 'C');
        
        $y = $this->GetY();
        
        $this->SetY($y+5);
        $drRefer = $mrihistoryInfo['dr_name'];
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(100, $rowheight, "Referring/Requesting Consultant/Department Chief Resident :", 0, 1, 'L');
        $this->Cell(3, $rowheight, "", 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(180, $rowheight, $drRefer, 0,'J');
        $y = $this->GetY();
        $this->SetY($y);
        $drSp = $mrihistoryInfo['dr_specialty'];
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(100, $rowheight, "Specialty :", 0, 1, 'L');
        $this->Cell(3, $rowheight, "", 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(180, $rowheight, $drSp, 0,'J');
        $y = $this->GetY();
        $this->SetY($y);
        $drAd = $mrihistoryInfo['dr_address'];
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(100, $rowheight, "Referrer's Address or SPMC Unit or Ward :", 0, 1, 'L');
        $this->Cell(3, $rowheight, "", 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(180, $rowheight, $drAd, 0,'J');
        $y = $this->GetY();
        $this->SetY($y);
        $drCon = $mrihistoryInfo['dr_contact_nr'];
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(100, $rowheight, "Contact Number :", 0, 1, 'L');
        $this->Cell(3, $rowheight, "", 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(180, $rowheight, $drCon, 0,'J');
        $y = $this->GetY();
        $this->SetY($y);
        $drPhone = $mrihistoryInfo['dr_phone'];
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(100, $rowheight, "Phone/Fax :", 0, 1, 'L');
        $this->Cell(3, $rowheight, "", 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(180, $rowheight, $drPhone, 0,'J');
        $y = $this->GetY();
        $this->SetY($y);
        $rDate = date('M d, Y', strtotime($mrihistoryInfo['refer_date']));
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(100, $rowheight, "Refer Date :", 0, 1, 'L');
        $this->Cell(3, $rowheight, "", 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(180, $rowheight, $rDate, 0,'J');
        $this->Ln();
        
        $y = $this->GetY();
        
        $this->SetY($y+3);
        $mProb = $mrihistoryInfo['med_prob'];
        $this->SetFont('Arial', 'BU', 10);
        $this->Cell(100, $rowheight, "INDICATION OF THE MRI EXAMINATION (MEDICAL PROBLEM) :", 0, 1, 'L');
        $this->Cell(3, $rowheight, "", 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(180, $rowheight, $mProb, 0,'J');
        $y = $this->GetY();
        
        $this->SetY($y+3);
        $iGain = $mrihistoryInfo['info_gain'];
        $this->SetFont('Arial', 'BU', 10);
        $this->Cell(100, $rowheight, "SPECIFIC INFORMATION TO GAIN FROM STUDY  :", 0, 1, 'L');
        $this->Cell(3, $rowheight, "", 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(180, $rowheight, $iGain, 0,'J');
        $y = $this->GetY();
        
        $this->SetY($y+3);
        $chief_comp = $mrihistoryInfo['chief_comp'];
        $this->SetFont('Arial', 'BU', 10);
        $this->Cell(100, $rowheight, "CHIEF COMPLAINT :", 0, 1, 'L');
        $this->Cell(3, $rowheight, "", 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(180, $rowheight, $chief_comp, 0,'J');
        $y = $this->GetY();
        
        $y = $this->GetY();
        
        //if($y < 100){
//           $y = 100; 
//        }
        
        $this->SetY($y+3);
        $hist = $mrihistoryInfo['history'];
        $this->SetFont('Arial', 'BU', 10);
        $this->Cell(100, $rowheight, "HISTORY :", 0, 1, 'L');
        $this->Cell(3, $rowheight, "", 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(180, $rowheight, $hist, 0,'J');
        $y = $this->GetY();
        
        $y = $this->GetY();
        
        //if($y < 150){
//           $y = 150; 
//        }
        
        $this->SetY($y+3);
        $phy_exam = $mrihistoryInfo['phy_exam'];
        $this->SetFont('Arial', 'BU', 10);
        $this->Cell(100, $rowheight, "PHYSICAL EXAMINATION :", 0, 1, 'L');
        $this->Cell(3, $rowheight, "", 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(180, $rowheight, $phy_exam, 0,'J');
        $y = $this->GetY();
        
        $y = $this->GetY();
        
        //if($y < 200){
//           $y = 200; 
//        }
        
        $this->SetY($y+3);
        $imp = $mrihistoryInfo['impression'];
        $this->SetFont('Arial', 'BU', 10);
        $this->Cell(38, $rowheight, "IMPRESSION :", 0, 1, 'L');
        $this->Cell(3, $rowheight, "", 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(180, $rowheight, $imp, 0,'J');
        $y = $this->GetY();
        
        $y = $this->GetY();
        
        //if($y < 250){
//           $y = 250; 
//        }
        
        $this->SetY($y+3);
        $past_med_his = $mrihistoryInfo['past_med_his'];
        $this->SetFont('Arial', 'BU', 10);
        $this->Cell(38, $rowheight, "PAST MEDICAL HISTORY :", 0, 1, 'L');
        $this->Cell(3, $rowheight, "", 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(180, $rowheight, $past_med_his, 0,'J');
        $y = $this->GetY();
      
        $y = $this->GetY();
        
       // if($y < 300){
//           $y = 300; 
//        }
        
        $this->SetY($y+3);
        $creatinine = $mrihistoryInfo['creatinine'];
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(27, $rowheight, "CREATININE : ", 0, 0, 'L');
            $x = $this->GetX();
            $y = $this->GetY();
        $this->SetFont('Arial', '', 10);
        $this->Cell(30, $rowheight, $creatinine, 0,1,'L');
        $this->Line($x, $y+($rowheight - 0.5), $x+30, $y+($rowheight - 0.5));
        $y = $this->GetY();
        
        $y = $this->GetY();
        $this->SetY($y+2);
        $bun = $mrihistoryInfo['bun'];
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(13, $rowheight, "BUN : ", 0, 0, 'L');
            $x = $this->GetX();
            $y = $this->GetY();
        $this->SetFont('Arial', '', 10);
        $this->Cell(30, $rowheight, $bun, 0,1,'L');
        $this->Line($x, $y+($rowheight - 0.5), $x+30, $y+($rowheight - 0.5));
        $y = $this->GetY();
        
        $drMri = $mrihistoryInfo['mri_dr_name'];
        $this->SetY($y+5);
        $this->Cell(90, $rowheight);
        $this->SetFont('Arial', 'BI', 9);
        $this->Cell(90, $rowheight, "MRF-1 Reviewed by :", 0,1,'L');
        $this->Cell(100, $rowheight);
        $this->SetFont('Arial', 'BU', 10);
        $this->Cell(90, $rowheight, $drMri, 0,1,'C');
        $this->Cell(100,4);
        $this->SetFont('Arial', '', 9);
        $this->Cell(90, 4, "MRI Resident-in-charge", 0, 1, 'C');

        $encoder = $mrihistoryInfo['encoder'];
        $mpTotal = $mrihistoryInfo['mp_total'];
        $mpAmtTmp = $mrihistoryInfo['mp_amount'];
        $mpSrvReqTmp = $mrihistoryInfo['mp_request'];
        $mpTransTmp = $mrihistoryInfo['mp_trans_type'];

        if($encoder){
            global $db;
            $this->sql="SELECT * FROM care_users WHERE login_id='$encoder'";
            if ($buf=$db->Execute($this->sql)){
                if($buf->RecordCount()) {
                    $encoder_info = $buf->FetchRow();
                }
            }

        }

        if($encoder_info){
            $encoder_name = $encoder_info['name'];
        }else{
            $encoder_name = "";
        }


        //$group = $mrihistoryInfo['group_code'];

        $mpAmt = explode("-", $mpAmtTmp);
        $mpSrvReq = explode(",", $mpSrvReqTmp);
        $mpTrans = explode(",", $mpTransTmp);               
        

        if($mpTotal){
            
            $this->Ln(4);
            $x=$this->GetX();
            $y=$this->GetY();
            $this->SetXY($x, $y+5);
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(35, $rowheight, "Manual Payment :", 0,1,'L');
            $this->SetFont('Arial', 'B', 11);
            $this->Ln();

            $this->Cell(20, $rowheight, "", 0,0,'L');
            $this->Cell(40, $rowheight+2, "REQUEST", 1,0,'C');
            $this->Cell(40, $rowheight+2, "PAYMENT TYPE", 1,0,'C');
            $this->Cell(40, $rowheight+2, "AMOUNT", 1,1,'C');

            $this->SetFont('Arial', '', 11);
            $this->Ln();

            for($i=0;$i<count($mpTrans);$i++){

                $amt = number_format($mpAmt[$i], 2);

                $this->Cell(20, $rowheight, "", 0,0,'L');
                $this->Cell(40, $rowheight, $mpSrvReq[$i], 0,0,'C');
                $this->Cell(40, $rowheight, $mpTrans[$i], 0,0,'C');
                $this->Cell(40, $rowheight, $amt, 0,1,'C'); 

            }
            $this->Ln();
            $this->Cell(20, $rowheight, "", 0,0,'L');
            $this->Cell(40, $rowheight, "", 0,0,'C');
            $this->Cell(40, $rowheight, "Total :", 0,0,'R');
            $this->Cell(40, $rowheight, $mpTotal, 0,1,'C'); 

            $this->Ln();
            $this->Cell(25, $rowheight, "Encoded by : ", 0,0,'L');
            $this->Cell(50, $rowheight, $encoder_name, 0,1,'L');

        }
        

    }

    //edited by Francis 03-11-13
    /*
    function Footer()
    {
        //$drName = $mrihistoryInfo['dr_name'];
        //$this->SetY(-28);
        //$this->Cell(0, $rowheight, $drName, 0,1,'R');
        $this->SetY(-23);
        $this->Line(120, 332, 205, 332);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(130,4);
        $this->Cell(50, 4, "Name and Signature of M.D.", 0, 1, 'L'); 
    }
    */

    //-------------------------------------
    
        
    
     //MultiCell with bullet
        function MultiCellBlt($w, $h, $blt, $txt, $border=0, $align='J', $fill=false)
        {
                //Get bullet width including margins
                $blt_width = $this->GetStringWidth($blt)+$this->cMargin*2;

                //Save x
                $bak_x = $this->x;

                //Output bullet
                $this->Cell($blt_width,$h,$blt,0,'',$fill);

                //Output text
                $this->MultiCell($w-$blt_width,$h,$txt,$border,$align,$fill);

                //Restore x
                $this->x = $bak_x;
        }


    //-------------------------------------
     function SetWidths($w)
     {
            //Set the array of column widths
            $this->widths=$w;
     }

     function SetAligns($a)
     {
            //Set the array of column alignments
            $this->aligns=$a;
     }
     /*
     function Row($data)
     {
        $row = 4;
            //Calculate the height of the row
            $nb=0;
            for($i=0;$i<count($data);$i++)
                    $nb=max($nb,$this->NbLines($this->ColumnWidth[$i],$data[$i]));
                    $nb2=$this->NbLines($this->ColumnWidth[4],$data[4]);
                    $nb3=$this->NbLines($this->ColumnWidth[6],$data[6]);
                    #echo "(nb_2): ".$nb2." (nb_3): ".$nb3;
                    if($nb2>$nb3){

                        $nbdiff = $nb2 - $nb3;
                        $nbdiff = $nbdiff*$row;

                        k == 1;
                    }
                    else if($nb3>$nb2){
                        $nbdiff = $nb3 - $nb2;
                         $nbdiff = $nbdiff*$row;
                        k==0;
                    }
                    else{
                        $nbdiff = 0;
                    }

                 $l_data4 = $this->GetStringWidth($data[4]);
                 $l_data6 = $this->GetStringWidth($data[6]);
                 #echo "data4: ".$l_data4." data6:".$l_data6;
                        if(($l_data4 >$l_data6) && ($l_data6 > $this->ColumnWidth[6]) && ($nb2 > $nb3)){
                            $lgreater = $l_data4;
                            $ldiff = $lgreater - $l_data6;
                            #echo intval($l);
                            #echo "l_data4: ".$l_data4." l_data6: ".$l_data6." ldiff: ".$ldiff;
                                for($cnt = 0; $cnt<intval($ldiff); $cnt++)
                                     $data[6].= " ";

                        }else if(($l_data6 > $l_data4) && ($l_data4 > $this->ColumnWidth[4]) && ($nb3 > $nb2)){

                            $lgreater = $l_data6;
                            $ldiff = $lgreater - $l_data4;
                            #echo "l_data6: ".$l_data6." l_data4: ".$l_data4." ldiff: ".$ldiff;
                                for($cnt = 0; $cnt<intval($ldiff); $cnt++)
                                    $data[4].=" ";
                        }
                 $l_data0 = $this->GetStringWidth($data[0]);
                 $l_data8 = $this->GetStringWidth($data[8]);

                    if($l_data0 > $this->ColumnWidth[0]){
                        $ldiff2 = $lgreater - $l_data0;
                        for($cnt1 = 0; $cnt1<intval($ldiff2); $cnt1++)
                            $data[0].=" ";
                    }

                    if($l_data8 > $this->ColumnWidth[8]){
                        $ldiff3 = $lgreater - $l_data8;
                        for($cnt2 = 0; $cnt2<intval($ldiff3); $cnt2++)
                            $data[8].=" ";
                    }
                                #echo $data[6];
                    //$nb = $nb*2;
                    //print_r($nb);
            $h=$row*$nb;
            //Issue a page break first if needed
            $this->CheckPageBreak($h);
            //Draw the cells of the row

            for($i=0;$i<count($data);$i++)
            {
                    $w=$this->ColumnWidth[$i];
                    $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
                    //$a = isset($this->Alignment[$i]) ? $this->Alignment[$i] : 'L';
                    //Save the current position

                    $x=$this->GetX();
                    $y=$this->GetY();
                    //Draw the border

                            $length = $this->GetStringWidth($data[$i]);
                            if($length < $this->ColumnWidth[$i]){
                                //$this->Cell($w, $h, $data[$i],1,0,'L');
                                $this->Cell($w, $h, $data[$i], 1, 0, $this->Alignment[$i]);
                            }
                            else{
                                $nbrow = 3;

                                // $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
                                //$this->MultiCell($w, $row,$data[$i],1,'L');
                                $this->MultiCell($w, $row, $data[$i], 1,$this->Alignment[$i]);

                                //$this->MultiCell($length, $row,$data[$i],1,'L');

                            }

                    //Put the position to the right of the cell
                    $this->SetXY($x+$w,$y);
            }
            //Go to the next line
            $this->Ln($h);
        }
        */
        
    //    function CheckPageBreak($h) {
    //            if($this->GetY()+$h>$this->PageBreakTrigger)
    //                    $this->AddPage($this->CurOrientation);
    //    }
        
        /*
        function NbLines($w,$txt) {
                //Computes the number of lines a MultiCell of width w will take
                $cw=&$this->CurrentFont['cw'];
                if($w==0)
                        $w=$this->w-$this->rMargin-$this->x;
                $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
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
        */
}



$pdf = new ClinicalHistory($_GET['pid'], $_GET['refno'], $_GET['grp'], $_GET['batch_nr']);
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->GetData();
$pdf->Output();
?>