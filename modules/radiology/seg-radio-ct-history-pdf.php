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
        $this->batchNr = $batch_nr;
        $this->grp = $grp;

        if(!$refno){
            $refno = 0;
        }

        $this->refno = $refno;


        //$cthistoryInfo = $radio_obj->getCTHistoryInfoPDF($this->refno);

        $cthistoryInfo = $radio_obj->getCTHistoryInfo($pid,$refno,$grp);
        $radioRequestInfo = $radio_obj->getAllRadioInfoByBatch($batch_nr);
        $radioRequestData = $radio_obj->getRadioRequestdata($this->refno,$this->batchNr);
        
        $this->encounter_nr = $cthistoryInfo['encounter_nr'];
        $this->stat = $cthistoryInfo['priority'];
        $this->request = $radioRequestInfo['service_name'];
        $this->request_prc = number_format($radioRequestData['price_charge'], 2, '.', ',');
        //$radioRequestData['amount_due'];
        
        $this->dept = $radioRequestInfo['service_dept_name'];
        if($this->dept=="Computed Tomography"){
            $this->dept = "CT";
        }
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
		$enc_obj=new Encounter;

        //$cthistoryInfo = $radio_obj->getCTHistoryInfoPDF($this->refno);   //Added by Cherry 08-05-10
        $cthistoryInfo = $radio_obj->getCTHistoryInfo($this->pid,$this->refno,$this->grp);
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
        
        if($cthistoryInfo['transaction']){
            $transaction = $cthistoryInfo['transaction'];
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

        $reqDateTmp = $cthistoryInfo['request_date'];
        $reqDate = date('M d,Y', strtotime($reqDateTmp));

		$this->Cell(35, $rowheight, $person_age."/".$person_gender, 0,0,'L');
		$this->Line($x, $y+($rowheight - 0.5), $x+35, $y+($rowheight - 0.5));
        
        if($this->batchNr){
		$this->Cell(11, $rowheight, "DATE:", 0, 0, 'L');
			$x = $this->GetX();
			$y = $this->GetY();
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
            $this->Cell(70, $rowheight, $this->request." (".$this->dept.") ", 0,0,'L');
            $this->Line($x, $y+($rowheight - 0.5), $x+70, $y+($rowheight - 0.5));
		$this->Cell(5, $rowheight);

		//medico legal?
            if($cthistoryInfo['medico_legal']=='0'){
			$medico="";
			$non_medico="X";
		}else{
			$medico = "X";
			$non_medico = "";
		}
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(3, $rowheight-1, $medico, 1,0,'C');
		$this->SetFont('Arial', '', 9);
		$this->Cell(20, $rowheight, "Medico Legal", 0,0,'L');
		$this->Cell(5, $rowheight);
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(3, $rowheight-1, $non_medico, 1,0,'C');
		$this->SetFont('Arial', '', 9);
            $this->Cell(15, $rowheight, "Non Medicolegal", 0,0,'L');
            $this->Cell(15, $rowheight);
            
            $this->Cell(13, $rowheight, "Priority: ", 0,0,'L');
                $x = $this->GetX();
                $y = $this->GetY();
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(15, $rowheight, $stat, 0,1,'L');
            $this->Line($x, $y+($rowheight - 0.5), $x+15, $y+($rowheight - 0.5));
            $this->Cell(5, $rowheight);
        }
        

		$this->Ln();

        //edited by Francis 03-11-13
        //Clinical Impression
    //    $this->SetFont('Arial', 'B', 14);
    //    $this->Cell(6, $rowheight, "S:",0,0,'L');
        $this->SetFont('Arial', 'BU', 10);
        $this->Cell(100, $rowheight, "Clinical Impression :", 0, 0, 'L');
        //$this->Cell(100, $rowheight);
			$x = $this->GetX();
			$y = $this->GetY();

		$this->SetFont('Arial', 'B', 10);
        $cln_imp = $cthistoryInfo['cln_imp'];
		$this->Cell(70, $rowheight, "FOR TRAUMA CASES:", 0,1,'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(95, $rowheight, $cln_imp, 0, 'J');
        $yClnImp = $this->GetY();  
		$y = $y + $rowheight;

		//medico cases
        //edited by Francis 03-11-13
             $noi = $cthistoryInfo['noi'];
             $poi = $cthistoryInfo['poi'];
             $doi = $cthistoryInfo['doi'];
             $toi = $cthistoryInfo['toi'];

        if($doi=='0000-00-00 00:00:00'){
			$doi = "";
        }
        else
        {
            $doi = date("M d, Y",strtotime($doi));
		}

        if($toi=='00:00:00'){
            $meridian = "";
            $TOI_val = "";    
        }
        else{
            $meridian = date("A",strtotime($toi));

                    if (($toi=='00:00:00') || (empty($toi))){
                        $TOI_val = "";
                    }else{
                        if (strstr($toi,'24')){
                            $TOI_val = "12:".substr($toi,3,2);
                            $selected1 = "selected";
                            $selected2 = "";
                        }else
                            $TOI_val = date("h:i",strtotime($toi));
                    }    
        }

		$this->SetXY($x, $y);
		$this->SetFont('Arial', '', 9);
		$this->Cell(15, $rowheight, "NOI :", 0,0,'C');
		$this->MultiCell(55, $rowheight, $noi, 0,1,'L'); //NOI
		// $this->Line($x+15, $y+($rowheight - 0.5), $x+70, $y+($rowheight - 0.5));
        $y = $this->GetY();
		$y = $y + $rowheight;

		$this->SetXY($x, $y);
		$this->SetFont('Arial', '', 9);
		$this->Cell(15, $rowheight, "POI :", 0,0,'C');
        $this->MultiCell(55, $rowheight, $poi, 0,1,'L'); //POI
		//$this->Line($x+15, $y+($rowheight - 0.5), $x+70, $y+($rowheight - 0.5));
        $y=$this->GetY();
		$y = $y + $rowheight;

		$this->SetXY($x, $y);
		$this->SetFont('Arial', '', 9);
		$this->Cell(15, $rowheight, "DOI :", 0,0,'C');
		$this->Cell(55, $rowheight, $doi, 0,1,'L'); //DOI
		//$this->Line($x+15, $y+($rowheight - 0.5), $x+70, $y+($rowheight - 0.5));
        $y=$this->GetY();
		$y = $y + $rowheight;

		$this->SetXY($x, $y);
		$this->SetFont('Arial', '', 9);
		$this->Cell(15, $rowheight, "TOI :", 0,0,'C');
        $this->Cell(55, $rowheight, $TOI_val." ".$meridian, 0,1,'L'); //TOI
		//$this->Line($x+15, $y+($rowheight - 0.5), $x+70, $y+($rowheight - 0.5));
		$y = $y + $rowheight;

		$this->SetXY($x, $y);
		$this->Cell(70, $rowheight, "", 0, 1, 'C'); //space
		$y = $y + $rowheight;

        //added by Francis 03-11-13
        //Chief Complaints
        $chf_cmp = $cthistoryInfo['chf_cmp'];
        
        if($yClnImp < 102){
           $yClnImp = 102; 
        }
        
        $this->SetY($yClnImp+2);
        $this->SetFont('Arial', 'BU', 10);
        $this->Cell(38, $rowheight, "Chief Complaint :", 0, 1, 'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(95, $rowheight, $chf_cmp, 0,'J');
        $yChfCmp = $this->GetY();
        
		//loss of consciousness, vomiting
		if($cthistoryInfo['has_conscious']=='1'){
			$is_conscious = "X";
		}else{
			$is_conscious = "";
		}

		if($cthistoryInfo['did_vomit']=='1')
			$did_vomit = "X";
		else
			$did_vomit = "";

		$this->SetXY($x, $y);
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(5, $rowheight, "", 0);
		$this->Cell(5, $rowheight-1, $is_conscious, 1,0,'C');
		$this->SetFont('Arial', '', 9);
		$this->Cell(60, $rowheight, "loss of consciousness", 0,1,'L');
		$y = $y + $rowheight;

		$this->SetXY($x, $y);
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(5, $rowheight, "", 0);
		$this->Cell(5, $rowheight-1, $did_vomit, 1,0,'C');
		$this->SetFont('Arial', '', 9);
		$this->Cell(60, $rowheight, "vomiting", 0,1,'L');
		$y = $y + $rowheight;

		$this->SetXY($x, $y);
		$this->Cell(70, $rowheight, "", 0, 1, 'C'); //space
		$y = $y + $rowheight;

		//gcs and rls
		$gcs = $cthistoryInfo['gcs'];
		$rls = $cthistoryInfo['rls'];

		$this->SetXY($x, $y);
		$this->SetFont('Arial', '', 9);
		$this->Cell(15, $rowheight, "GCS :", 0,0,'C');
		$this->Cell(55, $rowheight, $gcs, 0, 1, 'L');
		// $this->Line($x+15, $y+($rowheight - 0.5), $x+40, $y+($rowheight - 0.5));
		$y = $y + $rowheight;

		$this->SetXY($x, $y);
		$this->SetFont('Arial', '', 9);
		$this->Cell(15, $rowheight, "RLS :", 0,0,'C');
		$this->Cell(55, $rowheight, $rls, 0, 1, 'L');
		// $this->Line($x+15, $y+($rowheight - 0.5), $x+40, $y+($rowheight - 0.5));
		$y = $y + $rowheight;

		$this->SetXY($x, $y);
		$this->Cell(70, $rowheight, "", 0, 1, 'C'); //space
		$y = $y + $rowheight;

		$this->SetXY($x, $y);
        //    $this->Cell(70, $rowheight, "IMPRESSION:", "LR", 1, 'L'); //space
        //    $y = $y + $rowheight;

        //edited by Francis 03-11-13
        //Subjective Comlaints
        
        if($yChfCmp < 160){
            $yChfCmp = 160;    
        }

		$this->SetFont('Arial', 'B', 14);
        $this->SetY($yChfCmp+2);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(6, $rowheight, "S:",0,0,'L');
        $this->SetFont('Arial', 'U', 9);
        $this->Cell(38, $rowheight, "( Subjective Complaints)", 0, 1, 'L');
        //$this->Ln();
		//$this->Cell(100, $rowheight);

        //$this->SetFont('Arial', 'B', 10);
        $subj_comp = $cthistoryInfo['subj_comp'];
		#$this->Cell(70, $rowheight, "FOR TRAUMA CASES:", "TLR",1,'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(180, $rowheight, $subj_comp, 0, 'J');  //findings
        $ySubjComp = $this->GetY();

		//impression
		//$impression = "cdscsdcdscds sddscsdcdsc dfds dscsdcscds sdcdscda";
		$this->SetXY($x, $y);
        //$this->SetFont('Arial', '', 9);
        //$this->MultiCell(70, $rowheight, $impression, "LR", 'J'); //impression
		$x1 = $this->GetX();
		$y1 = $this->GetY();
		// $this->Line($x, $y1, $x+70, $y1);

        //edited by Francis 03-11-13
        //Pertinent PE findings
        if($ySubjComp < 220){
            $y = 220;
        }
        else{
            $y = $ySubjComp;
        }
        
        $obj_comp = $cthistoryInfo['obj_comp'];
        $this->SetY($y+2);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(6, $rowheight, "O:",0,0,'L');
        $this->SetFont('Arial', 'U', 9);
        $this->Cell(38, $rowheight, "( Pertinent PE Findings )", 0, 1, 'L');
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(180, $rowheight, $obj_comp, 0, 'J');
        $yObjComp = $this->GetY(); 
        
        //edited by Francis 03-11-13
		//assessment
        
        if($yObjComp < 280){
            $y = 280;
        }
        else{
            $y = $yObjComp;
        }
        
        
		$assessment = $cthistoryInfo['assessment'];
        $this->SetY($y+2);
		$this->SetFont('Arial', 'B', 14);
		$this->Cell(6, $rowheight, "A:",0,0,'L');
        $this->SetFont('Arial', 'U', 9);
        $this->Cell(38, $rowheight, "Assessment/Working Impression:", 0, 1, 'L');
        $this->SetFont('Arial', '', 9);
		$this->MultiCell(180, $rowheight, $assessment, 0,'J'); //assessment
        $yAssessment = $this->GetY();

		//surgical procedure
		if($cthistoryInfo['had_surgery']=='0')
			$no_surgery = "X";
		else
			$no_surgery = "";

		if($cthistoryInfo['had_surgery']=='1')          
			$with_surgery = "X";
		else
			$with_surgery = "";
            
        if(($cthistoryInfo['surgery_date'])&&($cthistoryInfo['surgery_date']!="0000-00-00"))
            $surDate = date("F j, Y", strtotime($cthistoryInfo['surgery_date']));
        else
            $surDate = "";

        if($yAssessment < 340){
            $y = 340;
        }
        else{
            $y = $yAssessment;
        }

        $this->SetY($y+2);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(60, $rowheight, "SURGICAL PROCEDURE DONE:", 0,0,'L');
        $this->Cell(2, $rowheight);
        $x = $this->GetX();
        $this->Cell(35, $rowheight, "Date : ", 0, 0, 'L');
        $this->Cell(70, $rowheight, "Procedure : ", 0,1,'L');
   
        $y = $this->GetY();
        
		$this->Cell(10, $rowheight);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(5, $rowheight-1, $no_surgery, 1,0,'C');
		$this->SetFont('Arial', '', 9);
		$this->Cell(15, $rowheight, "none", 0,1,'L');
        
		$this->Cell(10, $rowheight);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(5, $rowheight-1, $with_surgery, 1, 0, 'C');
		$this->SetFont('Arial', '', 9);
		$this->Cell(45, $rowheight, "yes", 0,0,'l');
        
        $this->SetXY($x,$y);
        $this->Cell(35, $rowheight, $surDate, 0, 0,'L'); //date of surgery
        
        $this->SetXY($x+35,$y);
        $this->MultiCell(80, $rowheight, $cthistoryInfo['surgery_proc'], 0,'J'); //procedure
        $ySurg = $this->GetY();
        //$this->Ln();
		//======end surgical procedure

		//laboratory...
		// $sql_date_lab = "SELECT * FROM seg_radio_ct_history
  //                                   WHERE refno = '".$this->refno."';";
		// $result_date_lab = $db->Execute($sql_date_lab);
		// $row_date_lab = $result_date_lab->FetchRow();

		if(($cthistoryInfo['date_blood_chem'])&&($cthistoryInfo['date_blood_chem']!="0000-00-00 00:00:00")){
			$date_blood_chem = date("F j, Y", strtotime($cthistoryInfo['date_blood_chem']));
		}else{
			$date_blood_chem ="";
		}
        
        if(($cthistoryInfo['date_biopsy'])&&($cthistoryInfo['date_biopsy']!="0000-00-00 00:00:00")){
            $date_biopsy = date("F j, Y", strtotime($cthistoryInfo['date_biopsy']));
        }else{
            $date_biopsy ="";
        }
        
        if(($cthistoryInfo['date_xray'])&&($cthistoryInfo['date_xray']!="0000-00-00 00:00:00")){
            $date_xray = date("F j, Y", strtotime($cthistoryInfo['date_xray']));
        }else{
            $date_xray ="";
        }
        
        if(($cthistoryInfo['date_ultrasound']) && ($cthistoryInfo['date_ultrasound']!="0000-00-00 00:00:00")){
            $date_ultrasound = date("F j, Y", strtotime($cthistoryInfo['date_ultrasound']));
        }else{
            $date_ultrasound ="";
        }
        
        if(($cthistoryInfo['date_ct_mri'])&&($cthistoryInfo['date_ct_mri']!="0000-00-00 00:00:00")){
            $date_ct_mri = date("F j, Y", strtotime($cthistoryInfo['date_ct_mri']));
        }else{
            $date_ct_mri ="";
        }

		
/*        $sql_date_xray = "SELECT sr.refno, cr.service_date FROM seg_radio_serv AS sr
											LEFT JOIN care_test_request_radio AS cr ON cr.refno = sr.refno
											LEFT JOIN seg_radio_services AS srs ON srs.service_code = cr.service_code
											LEFT JOIN seg_radio_service_groups AS g ON g.group_code = srs.group_code
											WHERE sr.encounter_nr = '".$this->encounter_nr."'
											AND g.department_nr = '164'
											ORDER BY cr.service_date DESC;";
		$result_date_xray = $db->Execute($sql_date_xray);
		$row_date_xray = $result_date_xray->FetchRow();

		if($row_date_xray['service_date']){
			$date_xray = date("m/d/y", strtotime($row_date_xray['service_date']));
		}else{
			 $date_xray = "";
		}

		$sql_date_ultrasound= "SELECT sr.refno, cr.service_date FROM seg_radio_serv AS sr
											LEFT JOIN care_test_request_radio AS cr ON cr.refno = sr.refno
											LEFT JOIN seg_radio_services AS srs ON srs.service_code = cr.service_code
											LEFT JOIN seg_radio_service_groups AS g ON g.group_code = srs.group_code
											WHERE sr.encounter_nr = '".$this->encounter_nr."'
											AND g.department_nr = '165'
											ORDER BY cr.service_date DESC;";
		$result_date_ultrasound = $db->Execute($sql_date_ultrasound);
		$row_date_ultrasound = $result_date_ultrasound->FetchRow();

		if($row_date_ultrasound['service_date']){
			$date_ultrasound = date("m/d/y", strtotime($row_date_ultrasound['service_date']));
		}else{
			 $date_ultrasound="";
		}

			$sql_date_ct_mri= "SELECT sr.refno, cr.service_date FROM seg_radio_serv AS sr
											LEFT JOIN care_test_request_radio AS cr ON cr.refno = sr.refno
											LEFT JOIN seg_radio_services AS srs ON srs.service_code = cr.service_code
											LEFT JOIN seg_radio_service_groups AS g ON g.group_code = srs.group_code
											WHERE sr.encounter_nr = '".$this->encounter_nr."'
											AND g.department_nr = '165'
											ORDER BY cr.service_date DESC;";
		$result_date_ct_mri = $db->Execute($sql_date_ct_mri);
		$row_date_ct_mri = $result_date_ct_mri->FetchRow();

		if($row_date_ct_mri['service_date']){
			$date_ct_mri = date("m/d/y", strtotime($row_date_ct_mri['service_date']));
		}else{
			 $date_ct_mri="";
		}
*/
		$note="";

		if($cthistoryInfo['has_blood_chem']=='1'){
			 $no_blood_chem="";
			 $with_blood_chem="X";
		}else{
			 $no_blood_chem="X";
			 $with_blood_chem="";
		}

		if($cthistoryInfo['has_xray']=='1'){
			$no_xray="";
			$with_xray="X";
		}else{
			$no_xray="X";
			$with_xray="";
		}

		if($cthistoryInfo['has_ultrasound']=='1'){
			$no_ultrasound = "";
			$with_ultrasound="X";
		}else{
			$no_ultrasound="X";
			$with_ultrasound="";
		}

		if($cthistoryInfo['has_ct_mri']=='1'){
			$no_ct_mri="";
			$with_ct_mri="X";
		}else{
			$no_ct_mri="X";
			$with_ct_mri="";
		}

		if($cthistoryInfo['has_biopsy']=='1'){
			$no_biopsy="";
			$with_biopsy="X";
		}else{
			$no_biopsy="X";
			$with_biopsy="";
		}

        if(($ySurg - $y) < 15){
            $y = $y + 15;
        }
        else{
            $y = $ySurg;
        }
        
        $this->SetY($y+2);
        $this->SetFont('Arial', 'B', 9);
		$this->Cell(0, $rowheight, "LABORATORY WORK-UP DONE:", 0,1,'L');
        $this->Cell(35, $rowheight);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(25, $rowheight, "DATE :", 0,0,'L');
		$this->Cell(5, $rowheight);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(61, $rowheight, "RESULT :", 0,0,'L');
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(40, $rowheight, "Remarks/Comments :", 0,1,'L');

        //edited by Francis 03-11-13
		//blood chemistry
        $bldChmRes = $cthistoryInfo['bld_chm_res'];
        $bldChmRem = $cthistoryInfo['bld_chm_rem'];
        
        $y=$this->GetY();
        $x=$this->GetX();
        $this->SetXY($x, $y);
        $this->SetFont('Arial', '', 9);
        $this->Cell(35, $rowheight, "Blood Chemistry:", 0,0,'L');
        $this->Cell(31, $rowheight, $date_blood_chem, 0,0,'L'); // date for blood chemistry
                
        $y=$this->GetY();
        $x=$this->GetX();
        $this->SetXY($x, $y);
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(60, $rowheight, $bldChmRes, 1,'J'); //  results for blood chemistry
        $yBloodChemRes = $this->GetY();
        $this->Cell(2, $rowheight);
        
        $x=$this->GetX();
        $this->SetXY($x+125, $y);
        $this->MultiCell(60, $rowheight, $bldChmRem, 1,'J'); // comments/remarks for blood chemistry
        $yBloodChemRem = $this->GetY();
        
        $x=$this->GetX();
        $this->SetXY($x, $y+$rowheight);
        $this->SetFont('Arial', 'B', 10);
		$this->Cell(15, $rowheight);
		$this->Cell(5, $rowheight-1, $no_blood_chem, 1,0,'C');
		$this->SetFont('Arial', '', 9);
		$this->Cell(3, $rowheight);
		$this->Cell(10, $rowheight, "none", 0,1,'L');
        
        $y=$this->GetY();
        $x=$this->GetX();
        $this->SetXY($x, $y);
        $this->SetFont('Arial', 'B', 10);
		$this->Cell(15, $rowheight);
		$this->Cell(5, $rowheight-1, $with_blood_chem, 1,0,'C');
		$this->SetFont('Arial', '', 9);
		$this->Cell(3, $rowheight);
		$this->Cell(10, $rowheight, "yes", 0,1,'L');
             
		$this->Ln();


        //edited by Francis 03-11-13
		//x-ray
        $xrayRes = $cthistoryInfo['xray_res'];
        $xrayRem = $cthistoryInfo['xray_rem'];
        
        if($yBloodChemRes > $yBloodChemRem){
            $ySet = $yBloodChemRes+2;
        }
        else{
            $ySet = $yBloodChemRem+2;
        }
        if(($ySet-$y)<7){
            $y = $y+7;
        }
        else{
            $y = $ySet;
        }
        
        $x=$this->GetX();
        $this->SetXY($x, $y);
        $this->SetFont('Arial', '', 9);
        $this->Cell(35, $rowheight, "X-ray:", 0,0,'L');
        $this->Cell(31, $rowheight, $date_xray, 0,0,'L'); // date for xray
                
        $y=$this->GetY();
        $x=$this->GetX();
        $this->SetXY($x, $y);
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(60, $rowheight, $xrayRes, 1,'J'); //  results for xray
        $yXrayRes = $this->GetY();
        $this->Cell(2, $rowheight);
        
        $x=$this->GetX();
        $this->SetXY($x+125, $y);
        $this->MultiCell(60, $rowheight, $xrayRem, 1,'J'); // comments/remarks for xray
        $yXrayRem = $this->GetY();
        
        $x=$this->GetX();
        $this->SetXY($x, $y+$rowheight);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(15, $rowheight);
        $this->Cell(5, $rowheight-1, $no_xray, 1,0,'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(3, $rowheight);
        $this->Cell(10, $rowheight, "none", 0,1,'L');
        
        $y=$this->GetY();
        $x=$this->GetX();
        $this->SetXY($x, $y);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(15, $rowheight);
        $this->Cell(5, $rowheight-1, $with_xray, 1,0,'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(3, $rowheight);
        $this->Cell(10, $rowheight, "yes", 0,1,'L');            
             
        $this->Ln();
        /*
        $this->Cell(40, $rowheight, "X-ray:", 0,0,'L');
		$this->Cell(25, $rowheight, $date_xray, 0,0,'C'); // date for xray
		$this->Cell(5, $rowheight);
        $this->SetFont('Arial', '', 9);
        $this->Cell(60, $rowheight, $xrayRes, 0,0,'L');
        $this->Cell(45, $rowheight, $xrayRem, 0,1,'L');
        $this->SetFont('Arial', 'B', 10);
		$this->Cell(15, $rowheight);
		$this->Cell(5, $rowheight-1, $no_xray, 1,0,'C');
		$this->SetFont('Arial', '', 9);
		$this->Cell(3, $rowheight);
		$this->Cell(10, $rowheight, "none", 0,1,'L');
		$this->Cell(15, $rowheight);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(5, $rowheight-1, $with_xray, 1,0,'C');
		$this->SetFont('Arial', '', 9);
		$this->Cell(3, $rowheight);
		$this->Cell(10, $rowheight, "yes", 0,1,'L');
		$this->Ln();
        */

        //edited by Francis 03-11-13
		//ultrasound
        $uRes = $cthistoryInfo['ultrasound_res'];
        $uRem = $cthistoryInfo['ultrasound_rem'];
        
        if($yXrayRes > $yXrayRem){
            $ySet = $yXrayRes+2;    
        }
        else{
            $ySet = $yXrayRem+2;
        }
        if(($ySet-$y)<7){
            $y = $y+7;
        }
        else{
            $y = $ySet;
        }       
        
        $x=$this->GetX();
        $this->SetXY($x, $y);
        $this->SetFont('Arial', '', 9);
        $this->Cell(35, $rowheight, "Ultrasound:", 0,0,'L');
        $this->Cell(31, $rowheight, $date_ultrasound, 0,0,'L'); // date for ultrasound
                
        $y=$this->GetY();
        $x=$this->GetX();
        $this->SetXY($x, $y);
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(60, $rowheight, $uRes, 1,'J'); //  results for ultrasound
        $yUltraRes = $this->GetY();
        $this->Cell(2, $rowheight);
        
        $x=$this->GetX();
        $this->SetXY($x+125, $y);
        $this->MultiCell(60, $rowheight, $uRem, 1,'J'); // comments/remarks for ultrasound
        $yUltraRem = $this->GetY();
        
        $x=$this->GetX();
        $this->SetXY($x, $y+$rowheight);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(15, $rowheight);
        $this->Cell(5, $rowheight-1, $no_ultrasound, 1,0,'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(3, $rowheight);
        $this->Cell(10, $rowheight, "none", 0,1,'L');
        
        $y=$this->GetY();
        $x=$this->GetX();
        $this->SetXY($x, $y);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(15, $rowheight);
        $this->Cell(5, $rowheight-1, $with_ultrasound, 1,0,'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(3, $rowheight);
        $this->Cell(10, $rowheight, "yes", 0,1,'L');            
             
        $this->Ln();
        /*
        $this->Cell(40, $rowheight, "Ultrasound:", 0,0,'L');
		$this->Cell(25, $rowheight, $date_ultrasound, 0,0,'C'); // date for ultrasound
		$this->Cell(5, $rowheight);
        $this->SetFont('Arial', '', 9);
        $this->Cell(60, $rowheight, $uRes, 0,0,'L');
        $this->Cell(45, $rowheight, $uRem, 0,1,'L');
        $this->SetFont('Arial', 'B', 10);
		$this->Cell(15, $rowheight);
		$this->Cell(5, $rowheight-1, $no_ultrasound, 1,0,'C');
		$this->SetFont('Arial', '', 9);
		$this->Cell(3, $rowheight);
		$this->Cell(10, $rowheight, "none", 0,1,'L');
		$this->Cell(15, $rowheight);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(5, $rowheight-1, $with_ultrasound, 1,0,'C');
		$this->SetFont('Arial', '', 9);
		$this->Cell(3, $rowheight);
		$this->Cell(10, $rowheight, "yes", 0,1,'L');
		$this->Ln();
        */

        //edited by Francis 03-11-13
		//ct/mri
        $cmRes = $cthistoryInfo['ct_mri_res'];
        $cmRem = $cthistoryInfo['ct_mri_rem'];
        
        if($yUltraRes > $yUltraRem){
            $ySet = $yUltraRes+2;
        }
        else{
            $ySet = $yUltraRem+2;
        }
        if(($ySet-$y)<7){
            $y = $y+7;
        }
        else{
            $y = $ySet;
        }
        
        $x=$this->GetX();
        $this->SetXY($x, $y);
        $this->SetFont('Arial', '', 9);
        $this->Cell(35, $rowheight, "CT/MRI:", 0,0,'L');
        $this->Cell(31, $rowheight, $date_ct_mri, 0,0,'L'); // date for ctmri
                
        $y=$this->GetY();
        $x=$this->GetX();
        $this->SetXY($x, $y);
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(60, $rowheight, $cmRes, 1,'J'); //  results for ctmri
        $yCtMriRes = $this->GetY();
        $this->Cell(2, $rowheight);
        
        $x=$this->GetX();
        $this->SetXY($x+125, $y);
        $this->MultiCell(60, $rowheight, $cmRem, 1,'J'); // comments/remarks for ctmri
        $yCtMriRem = $this->GetY();
        
        $x=$this->GetX();
        $this->SetXY($x, $y+$rowheight);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(15, $rowheight);
        $this->Cell(5, $rowheight-1, $no_ct_mri, 1,0,'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(3, $rowheight);
        $this->Cell(10, $rowheight, "none", 0,1,'L');
        
        $y=$this->GetY();
        $x=$this->GetX();
        $this->SetXY($x, $y);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(15, $rowheight);
        $this->Cell(5, $rowheight-1, $with_ct_mri, 1,0,'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(3, $rowheight);
        $this->Cell(10, $rowheight, "yes", 0,1,'L');            
             
        $this->Ln();
        /*
        $this->Cell(40, $rowheight, "CT/MRI:", 0,0,'L');
		$this->Cell(25, $rowheight, $date_ct_mri, 0,0,'C'); // date for ct/mri
		$this->Cell(5, $rowheight);
        $this->SetFont('Arial', '', 9);
        $this->Cell(60, $rowheight, $cmRes, 0,0,'L');
        $this->Cell(45, $rowheight, $cmRem, 0,1,'L');
        $this->SetFont('Arial', 'B', 10);
		$this->Cell(15, $rowheight);
		$this->Cell(5, $rowheight-1, $no_ct_mri, 1,0,'C');
		$this->SetFont('Arial', '', 9);
		$this->Cell(3, $rowheight);
		$this->Cell(10, $rowheight, "none", 0,1,'L');
		$this->Cell(15, $rowheight);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(5, $rowheight-1, $with_ct_mri, 1,0,'C');
		$this->SetFont('Arial', '', 9);
		$this->Cell(3, $rowheight);
		$this->Cell(10, $rowheight, "yes", 0,1,'L');
		$this->Ln();
        */

        //edited by Francis 03-11-13
		//biopsy
        $biopsyRes = $cthistoryInfo['biopsy_res'];
        $biopsyRem = $cthistoryInfo['biopsy_rem'];
        
        if($yCtMriRes > $yCtMriRem){
            $ySet = $yCtMriRes+2;
        }
        else{
            $ySet = $yCtMriRem+2;
        }
        if(($ySet-$y)<7){
            $y = $y+7;
        }
        else{
            $y = $ySet;
        }
        
        $x=$this->GetX();
        $this->SetXY($x, $y);
        $this->SetFont('Arial', '', 9);
        $this->Cell(35, $rowheight, "Biopsy:", 0,0,'L');
        $this->Cell(31, $rowheight, $date_biopsy, 0,0,'L'); // date for biopsy
                
        $y=$this->GetY();
        $x=$this->GetX();
        $this->SetXY($x, $y);
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(60, $rowheight, $biopsyRes, 1,'J'); //  results for biopsy
        $yBiopsyRes = $this->GetY(); 
        $this->Cell(2, $rowheight);
        
        $x=$this->GetX();
        $this->SetXY($x+125, $y);
        $this->MultiCell(60, $rowheight, $biopsyRem, 1,'J'); // comments/remarks for biopsy
        $yBiopsyRem = $this->GetY();
        
        $x=$this->GetX();
        $this->SetXY($x, $y+$rowheight);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(15, $rowheight);
        $this->Cell(5, $rowheight-1, $no_biopsy, 1,0,'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(3, $rowheight);
        $this->Cell(10, $rowheight, "none", 0,1,'L');
        
        $y=$this->GetY();
        $x=$this->GetX();
        $this->SetXY($x, $y);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(15, $rowheight);
        $this->Cell(5, $rowheight-1, $with_biopsy, 1,0,'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(3, $rowheight);
        $this->Cell(10, $rowheight, "yes", 0,1,'L');            
             
        $this->Ln();
        /*
        $this->Cell(40, $rowheight, "Biopsy:", 0,0,'L');
		$this->Cell(25, $rowheight, $date_biopsy, 0,0,'C'); // date for biopsy
		$this->Cell(5, $rowheight);
        $this->SetFont('Arial', '', 9);
        $this->Cell(60, $rowheight, $biopsyRes, 0,0,'L');
        $this->Cell(45, $rowheight, $biopsyRes, 0,1,'L');
        $this->SetFont('Arial', 'B', 10);
		$this->Cell(15, $rowheight);
		$this->Cell(5, $rowheight-1, $no_biopsy, 1,0,'C');
		$this->SetFont('Arial', '', 9);
		$this->Cell(3, $rowheight);
		$this->Cell(10, $rowheight, "none", 0,1,'L');
		$this->Cell(15, $rowheight);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(5, $rowheight-1, $with_biopsy, 1,0,'C');
		$this->SetFont('Arial', '', 9);
		$this->Cell(3, $rowheight);
		$this->Cell(10, $rowheight, "yes", 0,1,'L');
		$this->Ln();
        */
        
        //edited by Francis 03-11-13
        //Name of Doctor
        if($yBiopsyRes > $yBiopsyRem){
            $ySet = $yBiopsyRes;
        }
        else{
            $ySet = $yBiopsyRem;
        }
        if(($ySet-$y)<7){
            $y = $y+7;
        }
        else{
            $y = $ySet;
        }

        $drName = $cthistoryInfo['dr_name'];
        $this->SetY($y+5);
        $this->Cell(100, $rowheight);
        $this->SetFont('Arial', 'BU', 10);
        $this->Cell(90, $rowheight, $drName, 0,1,'C');
        $this->Cell(100,4);
        $this->SetFont('Arial', '', 9);
        $this->Cell(90, 4, "Name and Signature of M.D.", 0, 1, 'C');
        
        $encoder = $cthistoryInfo['encoder'];
        $mpTotal = $cthistoryInfo['mp_total'];
        $mpAmtTmp = $cthistoryInfo['mp_amount'];
        $mpSrvReqTmp = $cthistoryInfo['mp_request'];
        $mpTransTmp = $cthistoryInfo['mp_trans_type'];

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


        //$group = $cthistoryInfo['group_code'];

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
        //$drName = $cthistoryInfo['dr_name'];
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



//$pdf = new ClinicalHistory($_GET['encounter_nr'], $_GET['pid'], $_GET['batch_nr']);
$pdf = new ClinicalHistory($_GET['pid'], $_GET['refno'], $_GET['grp'], $_GET['batch_nr']);
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->GetData();
$pdf->Output();
?>