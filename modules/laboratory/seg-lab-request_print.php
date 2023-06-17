<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_ward.php');

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
*/

	class Lab_List_Request extends RepGen {
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
	var $withclaimstub;

	function Lab_List_Request ($pid, $refno, $is_cash, $withclaimstub) {
		global $db;
		#$this->RepGen("PATIENT'S LIST","L","Legal");
		#$this->RepGen("CLINICAL LABORATORY SERVICES","P","Letter");
        #$this->RepGen("CLINICAL LABORATORY SERVICES","P",array(215.9,165.1));
        $this->RepGen("CLINICAL LABORATORY SERVICES","P",array(190,140));

		$this->ColumnWidth = array(85,35,28,25);
        $this->RowHeight = 4.5;
		$this->TextHeight = 4;

		$this->Alignment = array('L','L','C','C');
		$this->PageOrientation = "P";
		#$this->PageOrientation = "L";
		#$this->PageFormat = "Legal";
		$this->LEFTMARGIN=10;
		$this->TOPMARGIN = 0.1;
		$this->NoWrap = false;

		$this->pid = $pid;
		$this->refno = $refno;
		$this->is_cash = $is_cash;
		$this->withclaimstub = $withclaimstub;

		$this->SetFillColor(0xFF);
		if ($this->colored) $this->SetDrawColor(0xDD);
	}

	function Header() {

	}

	function Footer(){

	}

	function BeforeRow() {
		$this->FONTSIZE = 10;
		if ($this->colored) {
			if (($this->ROWNUM%2)>0)
				#$this->FILLCOLOR=array(0xee, 0xef, 0xf4);
				$this->FILLCOLOR=array(255,255,255);
			else
				$this->FILLCOLOR=array(255,255,255);
			$this->DRAWCOLOR = array(0xDD,0xDD,0xDD);
		}
	}

	function BeforeRowRender() {
		global $root_path, $db;
		$objInfo = new Hospital_Admin();
		$srvObj=new SegLab;
		$dept_obj=new Department;
		$person_obj=new Person;
		$enc_obj=new Encounter;
		$pers_obj=new Personell;
		$ward_obj=new Ward;

		$borderYes="1";
		$borderNo="0";
		$newLineYes="1";
		$newLineNo="0";
		$space=2;

		if (($this->RENDERROW[1]->Text != $this->CurrentLabSection) || ($this->RENDERROW[0]->Text=='FECALYSIS (KATO-THICK) - ROUTINE')||($this->RENDERROW[0]->Text=='Urinalysis - ROUTINE') || ((($this->RENDERROW[0]->Text=='FECALYSIS (KATO-THICK) - ROUTINE')||($this->RENDERROW[0]->Text=='Urinalysis - ROUTINE')) && ($this->RENDERROW[0]->Text == 'CLINICAL MICROSCOPY'))) {

			#$this->AddPage();
			#$this->Ln(10);
			$x = $this->GetX();
			$y = $this->GetY();
			$this->Line(0, $y+5, 200, $y+5);
			$this->Ln(10);
			// Output header
			if ($row = $objInfo->getAllHospitalInfo()) {
				$row['hosp_agency'] = strtoupper($row['hosp_agency']);
				$row['hosp_name']   = strtoupper($row['hosp_name']);
			}
			else {
				$row['hosp_country'] = "Republic of the Philippines";
				$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
				$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
				$row['hosp_addr1']   = "Quezon Ave., Digos City, Davao del Sur";
			}

			$labserv = $srvObj->getLabServiceReqInfo($this->refno);
			$labserv_details = $srvObj->getRequestInfo($this->refno);
			#print_r($labserv_details);
			$this->parent_refno = $labserv['parent_refno'];

		if (trim($labserv['encounter_nr']))
				$person = $enc_obj->getEncounterInfo($labserv['encounter_nr']);
		else
				$person = $person_obj->getAllInfoArray($labserv['pid']);
			#echo "<br>".$enc_obj->sql;
			$doctor = $pers_obj->get_Person_name($labserv_details['request_doctor']);

			$doctor_name = $doctor['name_first']." ".$doctor['name_2']." ".$doctor['name_last'];
			$doctor_name = ucwords(strtolower($doctor_name));
			$doctor_name = htmlspecialchars($doctor_name);

//added by daryl
        $labwalk_ = $srvObj->selectifwalkin($labserv['pid']);
        $labwalk = $labwalk_->FetchRow();
        $iflabwalk = $labwalk['ifwalk'];

    if ($iflabwalk == 1){
            
                $getwalkname =  $srvObj->selectwalkin($labserv['pid']);
                $getwalk = $getwalkname->FetchRow();

                $middlename = $getwalk['name_middle'];
                if ($middlename)
                    $middlename = $middlename.".";

                $request_name = $getwalk['name_last'].", ".$getwalk['name_first']." ".$middlename;
                $request_name = ucwords(strtolower($request_name));
                $request_name = htmlspecialchars($request_name);

                $request_name = ucwords(strtolower($request_name));
                $request_name = htmlspecialchars($request_name);

                $request_address = $getwalk['orderaddress'];
                
     } else{
                
			$middlename = $person['name_middle'];
				if ($middlename)
					$middlename = $middlename.".";

				$request_name = $person['name_last'].", ".$person['name_first']." ".$middlename;
				$request_name = ucwords(strtolower($request_name));
				$request_name = htmlspecialchars($request_name);

				$request_address = trim($person['street_name'])." ".trim($person['brgy_name'])." ".trim($person['mun_name'])." ".trim($person['prov_name']);
				$request_name = ucwords(strtolower($request_name));
				$request_name = htmlspecialchars($request_name);
        }




            

            #edited by VAN 10-18-2012
            #BUGZILLA ID #53 Laboratory Claim Stub-Location must be labeled from Internal Medicine to Medicine-Diabetic Clinic    
			#if ($labserv_details["request_dept"])
			#    $person['current_dept_nr'] = $labserv_details["request_dept"];
            #check if current dept is a sub dept of the requesting dept       
            $sql_sub = "SELECT parent_dept_nr FROM care_department WHERE nr='".$person['current_dept_nr']."'";
            $rs_sub = $db->Execute($sql_sub);
            $row_sub = $rs_sub->FetchRow();
            
            if ($row_sub['parent_dept_nr']!=$labserv_details["request_dept"])
                $person['current_dept_nr'] = $labserv_details["request_dept"];
            
			if ($person['encounter_type']==1){
				$enctype = "ER PATIENT";
				$location = "EMERGENCY ROOM";
			}elseif ($person['encounter_type']==2){
				$enctype = "OUTPATIENT (OPD)";
				$dept = $dept_obj->getDeptAllInfo($person['current_dept_nr']);
				$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
            }elseif (($person['encounter_type']==3)||($person['encounter_type']==4)){
				$enctype = "INPATIENT";
				$ward = $ward_obj->getWardInfo($person['current_ward_nr']);
				$location = strtoupper(strtolower(stripslashes($ward['name'])));
			}else{
				if ($person['current_dept_nr']){
							$enctype = "WALKIN";
							$dept = $dept_obj->getDeptAllInfo($person['current_dept_nr']);
							$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
					}else{
						$enctype = "WALKIN";
						$location = "WALKIN";
					}
			}
            
			$this->SetFont("Arial","","8");
			$this->Cell(30,4,'PRIORITY NUMBER : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","9");
			$this->Cell(70,4,$this->refno,$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","","8");
            $this->Cell(15,4,'CASE # : ',$borderNo,$newLineNo,'L');
            $this->SetFont("Arial","B","12");
            $this->Cell(20,4,$labserv['encounter_nr'],$borderNo,$newLineNo,'L');
            $this->Ln(4);
			$this->SetFont("Arial","","8");
			$this->Cell(15,4,'NAME : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","12");

			$this->Cell(85,4,$request_name,$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","","8");
			$this->Cell(10,4,'AGE : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","12");
			$this->Cell(25,4,$person['age'],$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","","8");
			$this->Cell(15,4,'HOSP # : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","12");
			$this->Cell(25,4,$labserv['pid'],$borderNo,$newLineNo,'L');
			

			$this->Ln(4);
			$this->SetFont("Arial","","8");

			$this->Cell(17,4,'ADDRESS : ',$borderNo,$newLineno,'L');
			$this->SetFont("Arial","B","9");
			$this->Cell(120,4,$request_address,$borderNo,$newLineNo,'L');
			#$this->SetFont("Arial","","8");
			#$this->Cell(10,4,'Clinic : ',$borderNo,$newLineNo,'L');
			#$this->SetFont("Arial","B","9");
			#$this->Cell(20,4,$person['name_formal'],$borderNo,$newLineYes,'L');

			$this->Ln(4);
			$this->SetFont("Arial","","8");
			$this->Cell(20,4,'IMPRESSION : ',$borderNo,$newLineno,'L');
			$this->SetFont("Arial","B","9");

			$this->Cell(125,4,$labserv_details['clinical_info'],$borderNo,$newLineNo,'L');

			$this->Ln(4);
			$this->SetFont("Arial","","8");
			$this->Cell(20,4,'COMMENTS : ',$borderNo,$newLineno,'L');
			$this->SetFont("Arial","B","9");

			$this->Cell(125,4,$labserv['comments'],$borderNo,$newLineNo,'L');

			$this->Ln(4);
			$this->SetFont("Arial","","8");
			$this->Cell(25,4,'REQUEST DATE : ',$borderNo,$newLineno,'L');
			$this->SetFont("Arial","B","9");
			$this->Cell(75,4,date("F j, Y",strtotime($labserv['serv_dt'])),$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","","8");
			$this->Cell(40,4,'REQUESTING PHYSICIAN : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","9");
			$this->Cell(50,4,'Dr. '.$doctor_name,$borderNo,$newLineNo,'L');

			$this->Ln(4);
			$this->SetFont("Arial","","8");
			$this->Cell(30,4,'PATIENT TYPE : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","9");
			$this->Cell(70,4,$enctype,$borderNo,$newLineNo,'L');

			$this->Ln(4);
			$this->SetFont("Arial","","8");
			$this->Cell(30,4,'LOCATION/CLINIC : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","9");
			
            #added by VAN 12-29-2011
            if ($labserv['is_walkin']){
              $location = "WALKIN" ; 
            }
            #-----------------------
            
            $this->Cell(60,4,$location,$borderNo,$newLineNo,'L');

			$this->Ln(4);
			$this->SetFont("Arial","","8");
			$this->Cell(30,4,'PAYMENT TYPE : ',$borderNo,$newLineno,'L');
			$this->SetFont("Arial","B","9");
			if ($labserv['is_cash']){
				$this->Cell(20,4,'CASH',$borderNo,$newLineYes,'L');
			}else
				$this->Cell(20,4,'CHARGE',$borderNo,$newLineYes,'L');

			$this->SetFont('Arial','B',9);
			$this->Cell(17,5);

			#$this->Ln(2);

			# Print table header

			$this->SetFont('ARIAL','B',8);
			#if ($this->colored) $this->SetFillColor(0xED);
			if ($this->colored) $this->SetFillColor(255);
			$this->SetTextColor(0);
			$row=6;
			#$this->Cell(0,4,'',1,1,'C');
			$this->Ln(2);
			$this->SetFont("Arial","B","10");
			$this->Cell(20,4,"SECTION : ".$this->RENDERROW[1]->Text,'',1,'L');
			#$this->Cell($this->ColumnWidth[0],$row,'CODE',1,0,'C',1);
			$this->Cell($this->ColumnWidth[0],$row,'DESCRIPTION',1,0,'C',1);
			$this->Cell($this->ColumnWidth[1],$row,'SECTION',1,0,'C',1);
			$this->Cell($this->ColumnWidth[2],$row,'OR NO.',1,0,'C',1);
			$this->Cell($this->ColumnWidth[3],$row,'W/ SAMPLE',1,0,'C',1);
			#$this->Cell($this->ColumnWidth[5],$row,'ORIG. PRICE',1,0,'C',1);
			#$this->Cell($this->ColumnWidth[6],$row,'NET PRICE',1,0,'C',1);
			$this->Ln();
			/*
			$this->Ln(6);
			$this->SetFont("Arial","B","10");
			$this->Cell(20,4,"SECTION : ".$this->RENDERROW[2]->Text,'',1,'L');
			*/
			$this->CurrentLabSection = $this->RENDERROW[1]->Text;
			$this->CurrentLabService = $this->RENDERROW[0]->Text;
			$this->RENDERROWX = $this->GetX();
			$this->RENDERROWY = $this->GetY();
		}

	}

	function BeforeData() {

	#$this->Cell(20,4,"SECTIONVAN : ",'',1,'L');
		#added by VAN 10-22-08
		global $root_path, $db;
		$srvObj=new SegLab;
		$dept_obj=new Department;
		$person_obj=new Person;
		$enc_obj=new Encounter;
		$pers_obj=new Personell;
		$ward_obj=new Ward;


	if ($this->withclaimstub){
        #$this->AddPage();
		$this->Ln(5);
		$this->SetFont('Arial','B',10);
		$this->Cell($total_w,4,'CLAIM STUB (RECEIVED REQUEST)',$border2,1,'C');
		$this->SetFont('ARIAL','B',8);
		#if ($this->colored) $this->SetFillColor(0xED);
		if ($this->colored) $this->SetFillColor(255);
		$this->SetTextColor(0);
		$row=6;

		$labserv = $srvObj->getLabServiceReqInfo($this->refno);
		#echo $srvObj->sql;
		$labserv_details = $srvObj->getRequestInfo($this->refno);
		#print_r($labserv_details);
		$this->parent_refno = $labserv['parent_refno'];

		#$person = $enc_obj->getEncounterInfo($labserv['encounter_nr']);
		if (trim($labserv['encounter_nr']))
				$person = $enc_obj->getEncounterInfo($labserv['encounter_nr']);
			else
				$person = $person_obj->getAllInfoArray($labserv['pid']);

				$doctor = $pers_obj->get_Person_name($labserv_details['request_doctor']);

				$doctor_name = $doctor['name_first']." ".$doctor['name_2']." ".$doctor['name_last'];
				$doctor_name = ucwords(strtolower($doctor_name));
				$doctor_name = htmlspecialchars($doctor_name);
			/*
				if ($labserv['encounter_nr']==0){
						$request_name = $labserv['ordername'];
						$request_address = $labserv['orderaddress'];
				}else{
				*/
			

           #added by daryl

                        $labwalk_ = $srvObj->selectifwalkin($labserv['pid']);
                        $labwalk = $labwalk_->FetchRow();
                        $iflabwalk = $labwalk['ifwalk'];

    if ($iflabwalk == 1){
                $getwalkname =  $srvObj->selectwalkin($labserv['pid']);
                $getwalk = $getwalkname->FetchRow();

                $middlename = $getwalk['name_middle'];
                if ($middlename)
                    $middlename = $middlename.".";

                $request_name = $getwalk['name_last'].", ".$getwalk['name_first']." ".$middlename;
                $request_name = ucwords(strtolower($request_name));
                $request_name = htmlspecialchars($request_name);

                $request_name = ucwords(strtolower($request_name));
                $request_name = htmlspecialchars($request_name);

            } else   {
						$middlename = $person['name_middle'];
						if ($middlename)
							$middlename = $middlename.".";

						$request_name = $person['name_last'].", ".$person['name_first']." ".$middlename;
						$request_name = ucwords(strtolower($request_name));
						$request_name = htmlspecialchars($request_name);

						$request_address = trim($person['street_name'])." ".trim($person['brgy_name'])." ".trim($person['mun_name'])." ".trim($person['prov_name']);
						$request_name = ucwords(strtolower($request_name));
						$request_name = htmlspecialchars($request_name);
            }

                    
				#}
	 
     #edited by VAN 10-18-2012
     #BUGZILLA ID #53 Laboratory Claim Stub-Location must be labeled from Internal Medicine to Medicine-Diabetic Clinic    
     #if ($labserv_details["request_dept"])
     #  $person['current_dept_nr'] = $labserv_details["request_dept"];
     #check if current dept is a sub dept of the requesting dept       
     $sql_sub = "SELECT parent_dept_nr FROM care_department WHERE nr='".$person['current_dept_nr']."'";
     $rs_sub = $db->Execute($sql_sub);
     $row_sub = $rs_sub->FetchRow();
            
     if ($row_sub['parent_dept_nr']!=$labserv_details["request_dept"])
        $person['current_dept_nr'] = $labserv_details["request_dept"];

	 if($person['encounter_type']){
		if ($person['encounter_type']==1){
			$enctype = "ER PATIENT";
			$location = "EMERGENCY ROOM";
		}elseif ($person['encounter_type']==2){
			$enctype = "OUTPATIENT (OPD)";
			$dept = $dept_obj->getDeptAllInfo($person['current_dept_nr']);
			$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
		}elseif (($person['encounter_type']==3)||($person['encounter_type']==4)){
			$enctype = "INPATIENT";
			$ward = $ward_obj->getWardInfo($person['current_ward_nr']);
			#echo "sql = ".$ward_obj->sql;
			$location = strtoupper(strtolower(stripslashes($ward['name'])));
		}else{
			$enctype = "OUTPATIENT (OPD)";
		}
			}else{
					if ($person['current_dept_nr']){
							$enctype = "WALKIN";
							$dept = $dept_obj->getDeptAllInfo($person['current_dept_nr']);
							$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
					}else{
						$enctype = "WALKIN";
						$location = "WALKIN";
					}
			}

		// $this->SetFont("Arial","","8");
		// $this->Cell(30,4,'PRIORITY NUMBER : ',$borderNo,$newLineNo,'L');
		// $this->SetFont("Arial","B","9");
		// $this->Cell(65,4,$this->refno,$borderNo,$newLineNo,'L');
		// $this->SetFont("Arial","","8");
		// $this->Cell(15,4,'HOSP # : ',$borderNo,$newLineNo,'L');
		// $this->SetFont("Arial","B","9");
		// $this->Cell(25,4,$labserv['pid'],$borderNo,$newLineNo,'L');
		// $this->SetFont("Arial","","8");
		// $this->Ln(4);
		// $this->SetFont("Arial","","8");
		// $this->Cell(15,4,'NAME : ',$borderNo,$newLineNo,'L');
		// $this->SetFont("Arial","B","9");

		// $this->Cell(100,4,mb_strtoupper($request_name),$borderNo,$newLineNo,'L');
		// $this->SetFont("Arial","","8");

		// $this->Ln(4);
		// $this->SetFont("Arial","","8");
		// $this->Cell(30,4,'PATIENT TYPE : ',$borderNo,$newLineNo,'L');
		// $this->SetFont("Arial","B","9");
		// $this->Cell(65,4,$enctype,$borderNo,$newLineNo,'L');
  //       $this->SetFont("Arial","","8");
  //       $this->Cell(25,4,'REQUEST DATE : ',$borderNo,$newLineno,'L');
  //       $this->SetFont("Arial","B","9");
  //       $this->Cell(120,4,date("F j, Y",strtotime($labserv['serv_dt']))." at ".date("h:i A",strtotime($labserv['serv_tm'])),$borderNo,$newLineNo,'L');
		// $this->Ln(4);
		// $this->SetFont("Arial","","8");
		// $this->Cell(30,4,'LOCATION/CLINIC : ',$borderNo,$newLineNo,'L');
		// $this->SetFont("Arial","B","9");
		


			$this->SetFont("Arial","","8");
			$this->Cell(30,4,'PRIORITY NUMBER : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","9");
			$this->Cell(70,4,$this->refno,$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","","8");
            $this->Cell(15,4,'CASE # : ',$borderNo,$newLineNo,'L');
            $this->SetFont("Arial","B","12");
            $this->Cell(20,4,$labserv['encounter_nr'],$borderNo,$newLineNo,'L');
            $this->Ln(4);
			$this->SetFont("Arial","","8");
			$this->Cell(15,4,'NAME : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","12");

			$this->Cell(85,4,$request_name,$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","","8");
			$this->Cell(10,4,'AGE : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","12");
			$this->Cell(25,4,$person['age'],$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","","8");
			$this->Cell(15,4,'HOSP # : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","12");
			$this->Cell(25,4,$labserv['pid'],$borderNo,$newLineNo,'L');
			

			$this->Ln(4);
			$this->SetFont("Arial","","8");

			$this->Cell(17,4,'ADDRESS : ',$borderNo,$newLineno,'L');
			$this->SetFont("Arial","B","9");
			$this->Cell(120,4,$request_address,$borderNo,$newLineNo,'L');

			// $this->SetFont("Arial","","8");
			// $this->Cell(10,4,'Clinic : ',$borderNo,$newLineNo,'L');
			// $this->SetFont("Arial","B","9");
			// $this->Cell(20,4,$person['name_formal'],$borderNo,$newLineYes,'L');
			

			$this->Ln(4);
			$this->SetFont("Arial","","8");
			$this->Cell(20,4,'IMPRESSION : ',$borderNo,$newLineno,'L');
			$this->SetFont("Arial","B","9");
			$this->Cell(125,4,$labserv_details['clinical_info'],$borderNo,$newLineNo,'L');

			$this->SetFont("Arial","","8");
			$this->Cell(15,4,'Room no.: ',$borderNo,$newLineYes,'L');
			$this->SetFont("Arial","B","9");
			$this->Cell(20,4,$person['current_room_nr'],$borderNo,$newLineYes,'L');



			$this->Ln(4);
			$this->SetFont("Arial","","8");
			$this->Cell(20,4,'Birthday : ',$borderNo,$newLineno,'L');
			$this->SetFont("Arial","B","9");
			//$this->Cell(125,4,$labserv['comments'],$borderNo,$newLineNo,'L');
			$this->Cell(125,4,date("F j, Y",strtotime($person['date_birth'])),$borderNo,$newLineNo,'L');

			$this->Ln(4);
			$this->SetFont("Arial","","8");
			$this->Cell(25,4,'REQUEST DATE : ',$borderNo,$newLineno,'L');
			$this->SetFont("Arial","B","9");
			$this->Cell(60,4,date("F j, Y",strtotime($labserv['serv_dt']))." at ".date("h:i A",strtotime($labserv['serv_tm'])),$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","","8");
			$this->Cell(37,4,'REQUESTING PHYSICIAN : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","9");
			$this->Cell(50,4,'Dr. '.$doctor_name,$borderNo,$newLineNo,'L');

			$this->Ln(4);
			$this->SetFont("Arial","","8");
			$this->Cell(30,4,'PATIENT TYPE : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","9");
			$this->Cell(70,4,$enctype,$borderNo,$newLineNo,'L');

			$this->Ln(4);
			$this->SetFont("Arial","","8");
			$this->Cell(30,4,'LOCATION/CLINIC : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","9");


        #added by VAN 12-29-2011
        if ($labserv['is_walkin']){
            $location = "WALKIN" ; 
        }
        #-----------------------
        
        $this->Cell(60,4,$location,$borderNo,$newLineNo,'L');

		$this->SetFont('Arial','B',9);
		$this->Cell(17,5);

		$this->Ln(5);

		# Print table header
		#$this->Cell(0,4,'',1,1,'C');
		$this->Cell(33,4,'CODE ','1',$newLineNo,'L');
		$this->Cell(85,4,'DESCRIPTION ','1',$newLineNo,'L');
		$this->Cell(30,4,'OR NO ','1',$newLineNo,'L');
		$this->Cell(25,4,'WITH SAMPLE ','1',$newLineNo,'L');
		$this->Ln();

		#if($result['is_cash'])
		#	$mod = 1;
		#else
		#	$mod = 0;
		$ref_source = 'LB';
		$servreqObj = $srvObj->getRequestedServices($this->refno,$ref_source);
		#echo "sql = ".$srvObj->sql;
		$this->_count = $srvObj->count;

		if ($servreqObj) {
			while($result=$servreqObj->FetchRow()) {
				if ($result['is_forward'])
					$wsample = "YES";
				else
					$wsample = "FW";

				 if ($result['is_cash']){
						if ($result["request_flag"]){
								 if ($result["request_flag"]=='paid'){
										$sql_paid = "SELECT pr.or_no, pr.ref_no,pr.service_code
																	FROM seg_pay_request AS pr
																	INNER JOIN seg_pay AS p ON p.or_no=pr.or_no AND p.pid='".$result["pid"]."'
																	WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'
																	AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00') LIMIT 1";
										$rs_paid = $db->Execute($sql_paid);
										if ($rs_paid){
											$result2 = $rs_paid->FetchRow();
											$or_no = $result2['or_no'];
										}
								 }elseif ($result["request_flag"]=='charity'){
										$sql_paid = "SELECT pr.grant_no AS or_no, pr.ref_no,pr.service_code
																	FROM seg_granted_request AS pr
																	WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'
																	LIMIT 1";
										$rs_paid = $db->Execute($sql_paid);
										if ($rs_paid){
											$result2 = $rs_paid->FetchRow();
											$or_no = 'CLASS D';
										}
								 }elseif (($result["request_flag"]!=NULL)||($result_paid["request_flag"]!="")){
										if ($withOR)
											$or_no = $off_rec;
										else
											$or_no = $result["charge_name"];
								 }
							}else
								$or_no = "unpaid";

					}else{
							$or_no = "charge";
					}

				/*
				$this->Data[]=array(
					$result['service_code'],
					$result['name'],
					$or_no,
					$wsample
				);
				*/
				#commented by genz
				// $this->Cell(33,4,$result['service_code'],'1',$newLineNo,'L');
				// $this->Cell(85,4,$result['name'],'1',$newLineNo,'L');
				// $this->Cell(30,4,$or_no,'1',$newLineNo,'L');
				// $this->Cell(25,4,$wsample,'1',$newLineNo,'L');
				// $this->Ln();
				
				#added by genz
				$x = $this->GetX();
				$y = $this->GetY();

				$this->SetXY($x, $y);
				$this->MultiCell(33, 8,$result['service_code'], '1', 'L');
				
				if(strlen($result['name']) > 50){
					$this->SetFont('Arial', 'B', 7.5);
					$labname = $result['name'];
					$a = 4;
				}
				else{
					$this->SetFont('Arial', 'B', 10);
					$labname = $result['name'];
					$a = 8;
				}

				$this->SetXY($x+33, $y);
				$this->MultiCell(85, $a, $labname, '1', 'L');
				
				$this->SetXY($x+118, $y);
				$this->MultiCell(30, 8,$or_no, '1', 'L');
				
				$this->SetXY($x+148, $y);
				$this->MultiCell(25, 8, $wsample, '1', 'L');
				#end by genz
			}
			}
		}
		#-----------------
		if ($this->colored) {
			$this->DrawColor = array(0xDD,0xDD,0xDD);

		}
	}

	function BeforeCellRender() {

		$this->FONTSIZE = 8;
		if ($this->colored) {
			if (($this->RENDERPAGEROWNUM%2)>0)
				#$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
				$this->RENDERCELL->FillColor=array(255,255,255);
			else
				$this->RENDERCELL->FillColor=array(255,255,255);
		}

	}

	function AfterData() {
		global $db;
		$srvObj=new SegLab;

		if (!$this->withclaimstub){
			if (!$this->_count) {
				$this->SetFont('Arial','B',10);
				$this->SetFillColor(255);
				$this->SetTextColor(0);
				$this->Cell(200.8, $this->RowHeight, "No records found for this report...", 1, 1, 'L', 1);
			}
		}
		$cols = array();
	}

	function FetchData($refno,$is_cash) {
		global $db;
		$srvObj=new SegLab;

				if($is_cash)
						$mod = 1;
				else
						$mod = 0;

		#$servreqObj = $srvObj->getRequestedServices($refno,$mod);
		$ref_source = 'LB';
		$servreqObj = $srvObj->getRequestedServices($refno, $ref_source);
		#echo "sql = ".$srvObj->sql;
		$this->_count = $srvObj->count;

		if ($servreqObj) {
			while($result=$servreqObj->FetchRow()) {
				if ($result['is_forward'])
					$wsample = "YES";
				else
					$wsample = "NO";

				if ($is_cash){
					$totamount = $result['price_cash_orig'];
					$amount = $result['price_cash'];
				}else{
					$totamount = $result['price_charge'];
					$amount = $result['price_charge'];
				}

				$this->discount = $totamount - $amount;

				$this->total_discount = $this->total_discount + $this->discount;
				$this->total_amount = $this->total_amount + $totamount;
				# echo "s = ".$result['is_cash'];
				if($result['is_cash']){
						if ($result["type_charge"]){
								 if ($result["type_charge"]=='paid'){
										$sql_paid = "SELECT pr.or_no, pr.ref_no,pr.service_code
																	FROM seg_pay_request AS pr
																	INNER JOIN seg_pay AS p ON p.or_no=pr.or_no AND p.pid='".$result["pid"]."'
																	WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'
																	AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00') LIMIT 1";
										$rs_paid = $db->Execute($sql_paid);
										if ($rs_paid){
											$result2 = $rs_paid->FetchRow();
											$or_no = $result2['or_no'];
										}
								 }elseif ($result["type_charge"]=='charity'){
										$sql_paid = "SELECT pr.grant_no AS or_no, pr.ref_no,pr.service_code
																	FROM seg_granted_request AS pr
																	WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'
																	LIMIT 1";
										$rs_paid = $db->Execute($sql_paid);
										if ($rs_paid){
											$result2 = $rs_paid->FetchRow();
											$or_no = 'CLASS D';
										}
								 }elseif (($result["type_charge"]!=NULL)||($result_paid["type_charge"]!="")){
										if ($withOR)
											$or_no = $off_rec;
										else
											$or_no = $result["charge_name"];
								 }
							}else
								$or_no = "unpaid";
				}else{
						if ($result["type_charge"]){
								 if ($result["type_charge"]=='paid'){
										$sql_paid = "SELECT pr.or_no, pr.ref_no,pr.service_code
																	FROM seg_pay_request AS pr
																	INNER JOIN seg_pay AS p ON p.or_no=pr.or_no AND p.pid='".$result["pid"]."'
																	WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'
																	AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00') LIMIT 1";
										$rs_paid = $db->Execute($sql_paid);
										if ($rs_paid){
											$result2 = $rs_paid->FetchRow();
											$or_no = $result2['or_no'];
										}
								 }elseif ($result["type_charge"]=='charity'){
										$sql_paid = "SELECT pr.grant_no AS or_no, pr.ref_no,pr.service_code
																	FROM seg_granted_request AS pr
																	WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'
																	LIMIT 1";
										$rs_paid = $db->Execute($sql_paid);
										if ($rs_paid){
											$result2 = $rs_paid->FetchRow();
											$or_no = 'CLASS D';
										}
								 }elseif (($result["type_charge"]!=NULL)||($result_paid["type_charge"]!="")){
										if ($withOR)
											$or_no = $off_rec;
										else
											$or_no = $result["charge_name"];
								 }
							}else
									$or_no = 'charge';
				}
				/*
				$this->Data[]=array(
					$result['service_code'],
					$result['name'],
					$result['groupnm'],
					$or_no,
					$wsample,
					number_format($totamount,2,".",","),
					number_format($amount,2,".",",")
				);
				*/
	            
				#temporarily comment by genz

				// $this->Data[]=array(
				// 	$result['name'],
				// 	$result['groupnm'],
				// 	$or_no,
				// 	$wsample
				// );
			}
		}else{
			#print_r($srvObj->sql);
			print_r($db->ErrorMsg());
			exit;
		}
	}
}

$pid = $_GET['pid'];
$is_cash = $_GET['is_cash'];
$refno = $_GET['refno'];
$withclaimstub = $_GET['withclaimstub'];

#echo 'w = '.$withclaimstub;

$iss = new Lab_List_Request($pid, $refno, $is_cash, $withclaimstub);
$iss->AliasNbPages();
$iss->FetchData($refno, $is_cash, $ispaid);
$iss->Report();

?>