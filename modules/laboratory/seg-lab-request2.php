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
		$this->RepGen("CLINICAL LABORATORY SERVICES","P","Letter");
		# 165
		#$this->ColumnWidth = array(20,45,38,27,25,25,20);
		#$this->ColumnWidth = array(20,100,27,28,25);
    $this->ColumnWidth = array(110,35,28,25);
		$this->RowHeight = 4.5;
		$this->TextHeight = 4;
		#$this->Alignment = array('L','L','L','C','R','R','R');
		$this->Alignment = array('L','L','C','C');
		$this->PageOrientation = "P";
		#$this->PageOrientation = "L";
		#$this->PageFormat = "Legal";
		$this->LEFTMARGIN=15;
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
		
		#echo '1 = '.$this->RENDERROW[1]->Text;
		#echo '<br>2 = '.$this->CurrentLabSection;
		
		#echo '<br>wait lng po  = '.$this->RENDERROW[1]->Text;
		
		#if (($this->RENDERROW[1]->Text != $this->CurrentLabSection) || ((($this->RENDERROW[0]->Text=='FECALYSIS (KATO-THICK) - ROUTINE')||($this->RENDERROW[0]->Text=='Urinalysis - ROUTINE')) && ($this->RENDERROW[0]->Text != $this->CurrentLabService))) {
		#if (($this->RENDERROW[1]->Text != $this->CurrentLabSection) || ($this->RENDERROW[0]->Text=='FECALYSIS (KATO-THICK) - ROUTINE')||($this->RENDERROW[0]->Text=='Urinalysis - ROUTINE')) {
		if (($this->RENDERROW[1]->Text != $this->CurrentLabSection) || ($this->RENDERROW[0]->Text=='FECALYSIS (KATO-THICK) - ROUTINE')||($this->RENDERROW[0]->Text=='Urinalysis - ROUTINE') || ((($this->RENDERROW[0]->Text=='FECALYSIS (KATO-THICK) - ROUTINE')||($this->RENDERROW[0]->Text=='Urinalysis - ROUTINE')) && ($this->RENDERROW[0]->Text == 'CLINICAL MICROSCOPY'))) {
			
			#$this->AddPage();
			#$this->Ln(10);
			$x = $this->GetX();
			$y = $this->GetY();
			$this->Line($x, $y, 200, $y);
			$this->Ln(20);
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
		/*
			#$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',25,8,30,30);
			$this->SetFont("Arial","I","9");
			$total_w = 0;
			$this->Cell(17,4);
	  		#$this->Cell($total_w,4,'Republic of the Philippines',$border2,1,'C');
			$this->Cell($total_w,4,$row['hosp_country'],$border2,1,'C');
			$this->Cell(17,4);
			#$this->Cell($total_w,4,'DEPARTMENT OF HEALTH',$border2,1,'C');
			$this->Cell($total_w,4,$row['hosp_agency'],$border2,1,'C');
		  #	$this->Ln(2);
			$this->SetFont("Arial","B","10");
			$this->Cell(17,4);
	  		#$this->Cell($total_w,4,'Gonzales Maranan Medical Center Incorporated',$border2,1,'C');
			$this->Cell($total_w,4,$row['hosp_name'],$border2,1,'C');
			$this->SetFont("Arial","","9");
			$this->Cell(17,4);
		  	#$this->Cell($total_w,4,'Bajada, Davao City',$border2,1,'C');
			$this->Cell($total_w,4,$row['hosp_addr1'],$border2,1,'C');
			$this->Ln(1);
			$this->SetFont("Arial","B","10");
			$this->Cell(17,4);
  			$this->Cell($total_w,4,'DEPARTMENT OF PATHOLOGY AND CLINICAL LABORATORIES',$border2,1,'C');
			$this->Ln(2);
			$this->SetFont("Arial","B","13");
			$this->Cell(17,4);
				
	  		$this->SetFont('Arial','B',12);
			$this->Cell(17,5);
		  	#$this->Cell($total_w,4,'CLINICAL LABORATORY SERVICES',$border2,1,'C');
		  	#$this->Ln(5);
		$this->Ln(1);
		*/
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
		/*
			if ($labserv['encounter_nr']==0){
				$request_name = $labserv['ordername'];
				$request_address = $labserv['orderaddress'];
			}else{
		*/	
				$middlename = substr($person['name_middle'],0,1);
				if ($middlename)
					$middlename = $middlename.".";
				#$request_name = $person['name_first']." ".$person['name_2']." ".$person['name_middle']." ".$person['name_last'];
				$request_name = $person['name_last'].", ".$person['name_first']." ".$middlename;
				$request_name = ucwords(strtolower($request_name));
				$request_name = htmlspecialchars($request_name);
		
				$request_address = trim($person['street_name'])." ".trim($person['brgy_name'])." ".trim($person['mun_name'])." ".trim($person['prov_name']);
				$request_name = ucwords(strtolower($request_name));
				$request_name = htmlspecialchars($request_name);
			#}
		
			if ($labserv_details["request_dept"])
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
			$this->Cell(60,4,$this->refno,$borderNo,$newLineNo,'L');
			$this->Ln(4);		
			$this->SetFont("Arial","","8");
   		$this->Cell(15,4,'NAME : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","12");
	
			#$name = ucwords(strtolower($person['name_first']))." ".ucwords(strtolower($person['name_middle']))." ".ucwords(strtolower($person['name_last']));
	
			$this->Cell(60,4,$request_name,$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","","8");
			$this->Cell(10,4,'AGE : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","12");
			$this->Cell(25,4,$person['age'],$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","","8");
			$this->Cell(15,4,'HOSP # : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","12");
			$this->Cell(25,4,$labserv['pid'],$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","","8");
			$this->Cell(15,4,'CASE # : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","12");
			$this->Cell(20,4,$labserv['encounter_nr'],$borderNo,$newLineNo,'L');
	
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
			$this->Cell(60,4,date("F j, Y",strtotime($labserv['serv_dt'])),$borderNo,$newLineNo,'L');	
			$this->SetFont("Arial","","8");
			$this->Cell(40,4,'REQUESTING PHYSICIAN : ',$borderNo,$newLineNo,'L');
            $this->SetFont("Arial","B","9");
	        $this->Cell(50,4,'Dr. '.$doctor_name,$borderNo,$newLineNo,'L');
            
			$this->Ln(4);
			$this->SetFont("Arial","","8");
	   	    $this->Cell(30,4,'PATIENT TYPE : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","9");
			$this->Cell(60,4,$enctype,$borderNo,$newLineNo,'L');
	
			$this->Ln(4);
			$this->SetFont("Arial","","8");
	        $this->Cell(30,4,'LOCATION/CLINIC : ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","B","9");
			$this->Cell(60,4,$location,$borderNo,$newLineNo,'L');

			$this->Ln(4);		
			$this->SetFont("Arial","","8");
			$this->Cell(30,4,'PAYMENT TYPE : ',$borderNo,$newLineno,'L');
			$this->SetFont("Arial","B","9");
			if ($this->is_cash){
				if ($labserv["type_charge"])
					$this->Cell(20,4,$labserv["charge_name"],$borderNo,$newLineYes,'L');	
				else
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
			/*		
        if ($labserv['encounter_nr']==0){
            $request_name = $labserv['ordername'];
            $request_address = $labserv['orderaddress'];
        }else{
        */
				
						$middlename = substr($person['name_middle'],0,1);
						if ($middlename)
							$middlename = $middlename.".";
						
				    $request_name = $person['name_last'].", ".$person['name_first']." ".$middlename;
            $request_name = ucwords(strtolower($request_name));
            $request_name = htmlspecialchars($request_name);
        
            $request_address = trim($person['street_name'])." ".trim($person['brgy_name'])." ".trim($person['mun_name'])." ".trim($person['prov_name']);
            $request_name = ucwords(strtolower($request_name));
            $request_name = htmlspecialchars($request_name);
        #}
   if ($labserv_details["request_dept"])
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
	
		$this->SetFont("Arial","","8");
	   $this->Cell(30,4,'PRIORITY NUMBER : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(85,4,$this->refno,$borderNo,$newLineNo,'L');
        $this->SetFont("Arial","","8");
        $this->Cell(15,4,'HOSP # : ',$borderNo,$newLineNo,'L');
        $this->SetFont("Arial","B","9");
        $this->Cell(25,4,$labserv['pid'],$borderNo,$newLineNo,'L');
        $this->SetFont("Arial","","8");
		$this->Ln(4);		
		$this->SetFont("Arial","","8");
        $this->Cell(15,4,'NAME : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
	
		$this->Cell(100,4,mb_strtoupper($request_name),$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","","8");
		
		$this->SetFont("Arial","","8");
		$this->Cell(25,4,'REQUEST DATE : ',$borderNo,$newLineno,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(120,4,date("F j, Y",strtotime($labserv['serv_dt']))." at ".date("h:i A",strtotime($labserv['serv_tm'])),$borderNo,$newLineNo,'L');	
		
        $this->Ln(4);
		$this->SetFont("Arial","","8");
   	    $this->Cell(30,4,'PATIENT TYPE : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(60,4,$enctype,$borderNo,$newLineNo,'L');
	
		$this->Ln(4);
		$this->SetFont("Arial","","8");
	   $this->Cell(30,4,'LOCATION/CLINIC : ',$borderNo,$newLineNo,'L');
		$this->SetFont("Arial","B","9");
		$this->Cell(60,4,$location,$borderNo,$newLineNo,'L');

		$this->SetFont('Arial','B',9);
		$this->Cell(17,5);
		
		$this->Ln(5);

		# Print table header
		#$this->Cell(0,4,'',1,1,'C');
		$this->Cell(30,4,'CODE ','1',$newLineNo,'L');
		$this->Cell(110,4,'DESCRIPTION ','1',$newLineNo,'L');
		$this->Cell(30,4,'OR NO ','1',$newLineNo,'L');
		$this->Cell(25,4,'WITH SAMPLE ','1',$newLineNo,'L');
		$this->Ln();
		
		if($this->is_cash)
      $mod = 1;
    else
      $mod = 0;    
        
		$servreqObj = $srvObj->getRequestedServices($this->refno, $mod);
		#echo "sql = ".$srvObj->sql;
		$this->_count = $srvObj->count;
		
		if ($servreqObj) {
			while($result=$servreqObj->FetchRow()) {
				if ($result['is_forward'])
					$wsample = "YES";
				else
					$wsample = "FW";	
				
         if ($this->is_cash){
				    if ($result['or_no'])
					    $or_no = $result['or_no']; 
				    elseif ($result['grant_no'])
					   # $or_no = "subsidized";	
					    $or_no = "charity";	
				    elseif($labserv["type_charge"])
							$or_no = $labserv["charge_name"];	
						else
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
				$this->Cell(30,4,$result['service_code'],'1',$newLineNo,'L');
				$this->Cell(110,4,$result['name'],'1',$newLineNo,'L');
				$this->Cell(30,4,$or_no,'1',$newLineNo,'L');
				$this->Cell(25,4,$wsample,'1',$newLineNo,'L');
				$this->Ln();
				
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
		/*
		else{
			$this->Ln(4);
			$this->SetFont("Arial","","8");
			$this->Cell(160,4,'TOTAL AMOUNT : ',$borderNo,$newLineno,'R');
			$this->Cell(10,4,'Php ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","","10");
			$this->Cell(30,4,''.number_format($this->total_amount,2),$borderNo,$newLineNo,'R');	
			$this->Ln(6);
			$this->SetFont("Arial","","8");
			$this->Cell(160,4,'DISCOUNT (FROM Social Service) : ',$borderNo,$newLineno,'R');
			$this->Cell(10,4,'Php ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","","10");
			
			$granted_discount_amount = $srvObj->getSocialDiscount($this->refno);
			if (empty($granted_discount_amount['amount'])){
				$this->adjusted_amount = 0;
			}else{
				$this->adjusted_amount = $granted_discount_amount['amount'];				
			}	
	
			if ($this->parent_refno)
				$this->totdiscount = $this->total_amount;
			else
				$this->totdiscount = $this->total_discount + $this->adjusted_amount;	
			
			$this->Cell(30,4,number_format($this->totdiscount,2),$borderNo,$newLineNo,'R');
			$this->Ln(6);
			$this->SetFont("Arial","","8");
			$this->Cell(160,4,'NET TOTAL : ',$borderNo,$newLineno,'R');
			$this->Cell(10,4,'Php ',$borderNo,$newLineNo,'L');
			$this->SetFont("Arial","UB","10");
	
			$nettotal = $this->total_amount - $this->totdiscount;
			$this->Cell(30,4,number_format($nettotal,2),$borderNo,$newLineNo,'R');
			
			$this->Ln(30);
			$this->SetFont('Arial','',8);	
			$this->Cell(200,4,'________________________________________',"",1,'R');
			$this->Cell(200,4,'Person In-Charge (Signature Over Printed Name)',"",1,'R');
		}
		*/
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
		$servreqObj = $srvObj->getRequestedServices($refno);
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
				
				if ($result['or_no'])
					$or_no = $result['or_no']; 
				elseif ($result['grant_no'])
					#$or_no = "subsidized";	
					$or_no = "charity";	
				else{
					if ($result['type_charge'])
						$or_no = $result['charge_name'];	
					else	
						$or_no = "unpaid";	
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
				$this->Data[]=array(
					$result['name'],
					$result['groupnm'],
					$or_no,
					$wsample
				);
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