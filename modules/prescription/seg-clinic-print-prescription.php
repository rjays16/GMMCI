<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgenclass.php');
require_once($root_path.'include/care_api_classes/prescription/class_prescription_writer.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/care_api_classes/class_encounter.php';
define('FPDF_FONTPATH',$root_path.'classes/fpdf/font/'); //added by Nick, to fix error: "FPDF error: Could not include font metric file"

/**
* SegHIS - Hospital Information System (DMC Deployment)
* Enhanced by Segworks Technologies Corporation
* Prescription printout
* created by CHA, august 16, 2010
*/
#edited by VAN 12/07/2010    very minor changes :D      per Dr. Tan instruction
#edited by VAN 10-01-2012    can print the prescription either by group meds or individual
class RepGen_Prescription extends RepGen {

	var $encounter_nr;
	var $prescription_id;
	var $date;
	var $instructions;
	var $writer;

	function RepGen_Prescription($prescription_id, $encounter_nr, $as_grp)
	{
		global $db;

		if($prescription_id)
			$this->prescription_id=$prescription_id;
		if($encounter_nr)
			$this->encounter_nr=$encounter_nr;
        
        $this->as_grp = $as_grp;    

		$this->date=date("Y-m-d");

		$this->totalRow= $totalRow;
		$this->RepGen("PATIENT PRESCRIPTION","P", array(108.8,147.2));
        #$this->RepGen("PATIENT PRESCRIPTION","P", array(100,127));
        #$this->RepGen("PATIENT PRESCRIPTION","P", array(108.8,139.7));
        
		$this->SetMargins(1,1,1);
		$this->SetLineWidth(0.2);

		$this->TextPadding = 0.3;

		$this->colored = FALSE;
		//$this->ColumnWidth =  array(18,57,17,17);
		$this->RowHeight = 3;

		if ($this->colored)
			$this->SetDrawColor(0xDD);
        
        $this->Prescription = new SegPrescription();
        $this->prescriptionInfo = $this->Prescription->getPrescriptionInfo($prescription_id);
	}

	function Header()
	{
		global $root_path, $db;
		$today = date("F d, Y");
		$this->SetLineWidth(0.1);

		$this->SetFont("Arial","","8");
		/*$this->Cell(0,3,"SPMC FORM NO. 70",$border2,1,'L');
		$this->SetFont("Arial","","7");
		$this->Cell(0,3,"Revised 2012",$border2,1,'L');*/
		$this->Ln(4);

		$hospital = new Hospital_Admin();
		$hospitalInfo = $hospital->getAllHospitalInfo();
		$total_w = 0;
		#$this->Image($root_path.'gui/img/logos/dmc_logo.jpg',84,9,18);
		$this->SetFont("Arial","","12");
		$this->Cell($total_w,4,$hospitalInfo['hosp_name'],$border2,1,'C');
		$this->SetFont("Arial","","11");
		$this->Cell($total_w,3,$hospitalInfo['hosp_addr1'],$border2,1,'C');
		$this->Ln(6);

		//$this->SetLineWidth(0.1);
		$person = new Encounter();
		$person_info = $person->getEncounterInfo($this->encounter_nr,0);
        extract($person_info);
//		echo "<pre>";
//		print_r($person_info);
//		echo "</pre>";
//        die;

		/*$this->SetFont("Arial","", "9");
		$this->Cell(10,3, "HRN:", $border2, 0, "L");
		$this->SetFont("Arial","","10");
		$this->Cell(40,3, $person_info["pid"], $border2, 0, "L");

		$this->Cell(2,3,'',0,0);
		$this->SetFont("Arial","", "9");
		$this->Cell(13,3, "Case #:", $border2, 0, "L");
		$this->SetFont("Arial","","10");
		$this->Cell(0,3, $person_info["encounter_nr"], $border2, 1, "L");*/

		$this->Ln(1);
		$this->SetFont("Arial","","9");
		$this->Cell(12,3, "Name: ", $border2, 0, "L");
		$this->SetFont("Arial","","10");
		$this->Cell(0,3, 
			strtoupper($person_info['name_last'].", ".$person_info['name_first']." ".$person_info['name_middle']), 
			$border2, 1, 'L');
            
		$this->Ln(1);
		$this->SetFont("Arial","", "9");
		$this->Cell(9,3, "Age:", $border2, 0, "L");
		$this->SetFont("Arial","","10");
        
        if (stristr($age,'years')){
            $age = substr($age,0,-5);
            if ($age>1)
                $labelyear = "years";
            else
                $labelyear = "year";

            $age = floor($age)." ".$labelyear;
        }elseif (stristr($age,'year')){
            $age = substr($age,0,-4);
            if ($age>1)
                $labelyear = "years";
            else
                $labelyear = "year";

            $age = floor($age)." ".$labelyear;

        }elseif (stristr($age,'months')){
            $age = substr($age,0,-6);
            if ($age>1)
                $labelmonth = "months";
            else
                $labelmonth = "month";

            $age = floor($age)." ".$labelmonth;

        }elseif (stristr($age,'month')){
            $age = substr($age,0,-5);

            if ($age>1)
                $labelmonth = "months";
            else
                $labelmonth = "month";

            $age = floor($age)." ".$labelmonth;

        }elseif (stristr($age,'days')){
            $age = substr($age,0,-4);

            if ($age>30){
                $age = $age/30;
                if ($age>1)
                    $label = "months";
                else
                    $label = "month";

            }else{
                if ($age>1)
                    $label = "days";
                else
                    $label = "day";
            }

            $age = floor($age).' '.$label;

        }elseif (stristr($age,'day')){
            $age = substr($age,0,-3);

            if ($age>1)
                $labelday = "days";
            else
                $labelday = "day";

            $age = floor($age)." ".$labelday;
        }else{
            if ($age){
                if ($age>1)
                    $labelyear = "years";
                else
                    $labelyear = "year";

                $age = $age." ".$labelyear;
            }else
                $age = "0 day";
        }
    
		#$this->Cell(18,3, $person_info["age"], $border2, 0, "L");
        $this->Cell(18,3, $age, $border2, 0, "L");

		$this->Cell(1,3,"",0,0);
		$this->SetFont("Arial","", "9");
		$this->Cell(9,3, "Sex:", $border2, 0, "L");
		$this->SetFont("Arial","","10");
		$this->Cell(5,3, strtoupper($person_info["sex"]), $border2, 0, "L");

		$this->Cell(1,3,"",0,0);
		$this->SetFont("Arial","", "9");
		$this->Cell(11,3, "Clinic:", $border2, 0, "L");
		$this->SetFont("Arial","","10");
		$clinic_name = $db->GetOne("SELECT name_formal FROM care_department where nr=".$db->qstr($person_info["current_dept_nr"]));
		$this->Cell(0,3, ucfirst($clinic_name), $border2, 1, "L");

		/*$this->Ln(4);
		$this->SetFont("Arial","", "9");
		$this->Cell(15,3, "Address:", $border2, 0, "L");
		$this->Line($this->GetX(), $this->GetY()+3,$this->GetX()+76,$this->GetY()+3);
		$this->SetFont("Arial","","6");
		$address = $db->GetOne("SELECT fn_get_complete_address('".$person_info['pid']."')");
		$this->MultiCell(60,2, $address, $border2, 0, "L");*/

		$this->Ln(1);
		$this->SetFont("Arial","", "9");
		#$this->Cell(20,3, "Impression:", $border2, 0, "L");
        $this->Cell(20,3, "Address:", $border2, 0, "L");
		$this->SetFont("Arial","","9");
		$x = $this->GetX();
		$y = $this->GetY();
        
        $clinicalImpression = @$this->prescriptionInfo['clinical_impression'];
		#$this->MultiCell(0,3, $clinicalImpression ? $clinicalImpression : '',
		#	$border2, "L");
        
        if ($street_name){
            if ($brgy_name!="NOT PROVIDED")
                $street_name = $street_name.", ";
            else
                $street_name = $street_name.", ";
        }#else
            #$street_name = "";



        if ((!($brgy_name)) || ($brgy_name=="NOT PROVIDED"))
            $brgy_name = "";
        else
            $brgy_name  = $brgy_name.", ";

        if ((!($mun_name)) || ($mun_name=="NOT PROVIDED"))
            $mun_name = "";
        else{
            if ($brgy_name)
                $mun_name = $mun_name;
            #else
                #$mun_name = $mun_name;
        }

        if ((!($prov_name)) || ($prov_name=="NOT PROVIDED"))
            $prov_name = "";
        #else
        #    $prov_name = $prov_name;

        if(stristr(trim($mun_name), 'city') === FALSE){
            if ((!empty($mun_name))&&(!empty($prov_name))){
                if ($prov_name!="NOT PROVIDED")
                    $prov_name = ", ".trim($prov_name);
                else
                    $prov_name = "";
            }else{
                #$province = trim($prov_name);
                $prov_name = "";
            }
        }else
            $prov_name = " ";

        $address = $street_name.$brgy_name.$mun_name.$prov_name;
    
        $this->MultiCell(0,3, mb_strtoupper($address),$border2, "L");
	
		$this->Ln(4);

		#$this->SetLineWidth(0.5);
		#$this->Cell(0,1, '','T');
		#$this->SetLineWidth(0.1);

		#$this->Ln(3);
		$this->Cell(40,3,"",0,0);
		$this->SetFont("Arial","", "9");
		$this->Cell(10,3, "Date:", $border2, 0, "L");
		$this->SetFont("Arial","","10");
		$this->Cell(0,3.5, $today, $border2, 1, "C");

		$this->Image($root_path.'gui/img/common/default/rx2.png',$this->GetX(),$this->GetY()-3,5,6);
		#$this->Ln(15);
        
        if ($this->as_grp)
           $space = 5;
        else
           $space = 15;
            
        $this->Ln($space);
		//	parent::Header();
	}

	function Footer()
	{
		//$this->SetY(90);
	//	$this->SetY(-6);
	//	$this->SetFont('Arial','I',5);
	//	$this->Cell(0,1,'Page '.$this->PageNo().' of {nb}',0,0,'R');
	//	parent::Footer();
    
        $row = $this->Prescription->getPrescriberInfo($this->prescription_id);
    
		$this->SetFont('Arial', '', 12);
		$this->SetLineWidth(0.1);
        #$this->SetY(-22);
		$this->SetY(-40);
        #$this->Ln(5); 

		$border2=0;
		
		#$this->Cell(30,3,"",0,0);

		/*
		edited by Nick, 11/18/2013 4:03 PM
		condition to concat DDM if dentist, otherwise MD
		*/
		$doctorNameSuffix = "";
		$this->SetFont("Arial","","10");
		if($row['name_formal']!='Dental')
			$doctorNameSuffix = "M.D.";
		else
			$doctorNameSuffix = "D.D.M.";
		$this->Cell(0,3, $row['name'] . ', ' . $doctorNameSuffix, $border2, 1, 'R');
		//end Nick

		$this->Ln(1);
		$this->Cell(49,3,"",0,0);
		$this->SetFont("Arial","","9");

		$this->Cell(11,3, "Lic. #", $border2, 0, "L");
		$this->SetFont("Arial","","9");
		$this->Cell(0,3, $row["license_nr"], $border2, 1, 'L');

		/*$this->Ln(1);
		$this->Cell(49,3,"",0,0);
		$this->SetFont("Arial","","9");
		$this->Cell(11,3, "PTR #", $border2, 0, "L");
		$this->SetFont("Arial","","9");
		$this->Cell(0,3, $row["prescription_license_nr"], $border2, 1, 'L');*/

		$this->Ln(1);
		$this->Cell(49,3,"",0,0);
		$this->SetFont("Arial","","9");
		$this->Cell(11,3, "S2 #", $border2, 0, "L");
		$this->SetFont("Arial","","9");
		$this->Cell(0,3, $row["s2_nr"], $border2, 1, 'L');
		
		parent::Footer();
	}

	/*function AcceptPageBreak()
	{
		if($this->totalRow<20)
		{
			return false;
		}
		else
		{
			$this->totalRow=0;
			return true;
		}
	} */


	function BeforeData()
	{
		if ($this->colored) {
			$this->DrawColor = array(0xDD,0xDD,0xDD);
		}
		$this->ColumnFontSize = 6;
		global $db;
	//	if (!$this->_count) {
	//		$this->SetFont("Arial","","7");
	//		$this->SetFillColor(255);
	//		$this->SetTextColor(0);
	//		$this->Cell(107, $this->RowHeight, "No records found for this report...", 1, 1, 'L');
	//	}

		$cols = array();
	}

	function BeforeCellRender()
	{
		$this->FONTSIZE = 7;
		if ($this->colored) {
			if (($this->RENDERPAGEROWNUM%2)>0)
				$this->RENDERCELL->FillColor=array(0xee, 0xef, 0xf4);
			else
				$this->RENDERCELL->FillColor=array(255,255,255);
		}
	}

	function AfterData()
	{
		global $db;
		//$this->SetLineWidth(0.1);
		
		$details = $this->Prescription->getPrescription($this->encounter_nr, $this->prescription_id);
//        echo "<pre>";
//		print_r($pres_obj->sql);
//		echo"</pre>";
//        die;
        
        if ($details !== FALSE) {
			$cnt = $details->RecordCount();
            
            $counter = 0;
			while($row=$details->FetchRow())
			{
//				$x = $this->GetX();
//				$y = $this->GetY();
//				$this->SetLineWidth(0.5);
//				$this->Cell(0, 55, '', 'T', 0);
//				$this->SetLineWidth(0.2);
//				$this->SetXY($x,$y+5);
				
				//$this->instructions = $row['instructions'];
				//$this->writer = $row['writer'];
                $period = '';
                
                if (!empty($row['period_count'])) {
                    $period = $row['period_count'] . " ";
                }
                
                
				switch(strtolower($row['period_interval']))
				{
					case 'd': $period.='day(s)'; break;
					case 'w': $period.='week(s)'; break;
					case 'm': $period.='month(s)'; break;
                    default: $period = '';
				}
				$this->SetFont('Arial', '', 11);
                
                
                $name = $row['generic'];
                if (empty($row['generic'])) {
                    $name = $row['item_name'];
                }
                
				$this->MultiCell(0,4, 
					strtoupper($name)." #".number_format($row['quantity'],0)."", 
					$border2, 'L');
				$this->Ln(0.5);
				$this->SetFont('Arial', '', 10);
				$this->MultiCell(0,4, 
                    "Sig: ". 
                    (!empty($row['dosage']) ? $row['dosage'] : str_repeat(' ', 20)) .
                    (!empty($period) ? (" For " . $period) : ''), 
                    $border2, "L");
                $this->Ln(3);

				$cnt--;
                
                if ($this->as_grp){
                    $maxnum = 2;        
                }else{
				    $maxnum = 1;
                }   
                
                #if((++$counter % 1) == 0 && !empty($cnt))
                if((++$counter % $maxnum) == 0 && !empty($cnt))
                        $this->AddPage("P");
                     
			}

			/*$this->SetXY(4, 60);
			$this->Cell(21, 3, "Instructions: ", 0, 1, 'L');
			$this->MultiCell(38, 3, $this->instructions, 0, 'L');

			$this->Line(4, 77, 50, 77);
			$this->SetXY(15, 77);
			$this->Cell(21, 3, strtoupper($this->writer), 0, 0, 'C');
			$this->SetXY(15, 79);
			$this->Cell(21, 3, "Signature", 0, 0, 'C');           */
		}
		else {
			print_r($sql);
			print_r($db->ErrorMsg());
			exit;
			# Error
		}
	}


	function FetchData()
	{


	}


}
$rep = new RepGen_Prescription($_GET['prescription_id'], $_GET['encounter_nr'], $_GET['as_grp']);
$rep->AliasNbPages();
$rep->FetchData();
$rep->Report();

