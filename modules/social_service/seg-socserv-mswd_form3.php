<?php
//edited by Cherry 07-20-10
	 error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'/modules/repgen/repgen.inc.php');
require($root_path.'/modules/repgen/fpdf.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_social_service.php');
include_once($root_path.'include/care_api_classes/class_encounter.php');
include_once($root_path.'include/care_api_classes/class_person.php');
include_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_ward.php');

//define('FPDF_FONTPATH','/xampp/htdocs/xampp/font/');
//require('fpdf.php');

class MSWD_Form3 extends FPDF{
	var $nodata = 0;
	var $date;
	#var $hrn = '1000068';
	var $hrn;
	var $enc;
	var $pagenum = 1;
	var $height_reg = 5;
	var $height2 = 5;
	var $half = 100;
	var $full = 200;
	var $fontfamily1 = "Arial";
	var $fontstyle = "BI";
	var $fontstylebold = "B";
	var $fontstylenormal = '';
	var $fontsize1 = 8;
	var $fontsize13 = 3;
	var $fontsize2 = 9;
	var $fontsize3 = 8.5;
	var $social_worker;
	var $informant;
	var $encounter_type;
	var $height_reg2 = 5;
	var $full1 = 197;
	var $checked = 'images/check2.png';
	var $uncheck = 'images/uncheck2.png';

	var $isPatIncluded = 0;


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

	function Row($data)
	{
		//Calculate the height of the row
		$nb=0;
		for($i=0;$i<count($data);$i++)
			$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		$h=5*$nb;
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
			$w=$this->widths[$i];
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();
			//Draw the border
			$this->Rect($x,$y,$w,$h);
			//Print the text
			$this->MultiCell($w,4,$data[$i],0,$a);
			//Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
		}
		//Go to the next line
		$this->Ln($h);
	}

	function CheckPageBreak($h)
	{
		//If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h>$this->PageBreakTrigger)
			$this->AddPage($this->CurOrientation);
	}

	function NbLines($w,$txt)
	{
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

	function MSWD_Form3($pid, $encounter_nr) {
		global $db;
		#$this->RepGen("MSWD Form No.3");
		 $this->FPDF('P', 'mm', 'legal');
		 $this->hrn = $pid;
		 #echo "hrn = ".$this->hrn;
		 $this->enc = $encounter_nr;
	}
//added by Daryl
	// set fonts and style for dependent
	//10-25-2013
private function setDataFont($isBold = false) {
	$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize1); 

	}

	function FirstPageHeader(){
		global $root_path, $db;
		$objInfo = new Hospital_Admin();


		if ($row = $objInfo->getAllHospitalInfo()) {
			$row['hosp_agency'] = strtoupper($row['hosp_agency']);
			$row['hosp_name']   = strtoupper($row['hosp_name']);
		}
		else {
			$row['hosp_country'] = "Republic of the Philippines";
			$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$row['hosp_name']    = "Gonzales Maranan Medical Center Incorporated";
			$row['hosp_addr1']   = "Quezon Ave., Digos City";
		}

		$this->SetFont($this->fontfamily1, $this->fontstyle, $this->fontsize1);
		//$this->Cell(190,2,"",0,1);
		//$this->Ln();
		//$this->SetLeftMargin(1);
		$this->Cell($this->full, $this->fontsize13, "MSWD FORM NO.3 ASSESSMENT TOOL", "TLR", 1, 'L');
		$this->Image('../../modules/social_service/image/dmc_logo.jpg',170,11,31,23);
		$this->Image('../../modules/social_service/image/Logo_DOH.jpg',20,13,25,20);

		$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize1);
		$this->Cell(75, $this->height_reg, "", "L", 0, 'L');
		$this->SetFont($this->fontfamily1, $this->fontstylebold, 11);
		$this->Cell(125, $this->height_reg, $row['hosp_country'], "R", 1, 'L');
		$this->Cell($this->full, $this->height_reg, ucwords(strtolower($row['hosp_agency'])), "LR", 1, 'C');
		$this->Cell($this->full, $this->height_reg, $row['hosp_name'], "LR", 1, 'C');
		$this->Cell($this->full, $this->height_reg, "MEDICAL SOCIAL WORK SECTION","LR",1,'C');
		$this->Cell($this->full, $this->height_reg, "", "LRB", 1);

		$objSS = new SocialService();
		$data = $objSS->getSocialServPatientEncounter($this->enc,$this->hrn);

		//print_r($data);
		$this->mss = $data['mss_no'];
		$this->informant = $data['informant_name'];
		$this->social_worker = $data['social_worker'];
		$this->info_address = $data['info_address'];
		$this->final_diagnosis = $data['final_diagnosis'];
		$this->duration_problem = $data['duration_problem'];
		$this->duration_treatment = $data['duration_treatment'];
		$this->treatment_plan = $data['treatment_plan'];
		$this->accessibility_problem = $data['accessibility_problem'];
		$this->info_agency = $data['info_agency'];
		$this->info_contact_no = $data['info_contact_no'];
		$this->address = $data['address'];
		$this->remarks = $data['remarks'];
		$this->other_source_income = $data['other_source_income'];
		$this->educational_attain = $data['educational_attain'];
		$this->employer = $data['employer'];
		$this->employer_address = $data['employer_address'];
		$this->occupation = $data['occupation'];
		$this->other_occupation = $data['other_occupation'];
		$this->monthly_income = $data['monthly_income'];
		$this->per_capita_income = $data['per_capita_income'];
		$this->light_expense = $data['ligth_expense'];
		$this->temporary_address = $data['address'];
		$this->house_type = $data['house_type'];
        $this->religionSoc = $data['religion'];
        $this->civil_status = $data['status'];
        if($data['date_interview'] && $data['date_interview'] != '0000-00-00'){
    		$this->date_interviewSoc =  date("m/d/Y",strtotime($data['date_interview']));    
		}elseif($data['create_time']){
    		$this->date_interviewSoc =  date("m/d/Y",strtotime($data['create_time'])); 
		}else{
		    $this->date_interviewSoc= date("m/d/Y"); 
		} 
        $this->source_referral = $data['source_referral'];
        $this->name_referral = $data['name_referral'];

        $this->monthly_income_remarks = $data['monthly_income_remarks'];
        $this->monthly_expenses_remarks = $data['monthly_expenses_remarks'];
        $this->relation_informant = $data['relation_informant'];

	}

	function Info1(){
		global $db;
		$enc_obj=new Encounter;
		$person_obj = new Person;
		$dep_obj = new Department;
		$ward_obj = new Ward(); 
		if($this->enc!='0'){
			$encInfo = $enc_obj->getEncounterInfo($this->enc);	
		}else{
			$encInfo = $person_obj->getAllInfoArray($this->hrn);
		}
		

		$this->name_first = $encInfo['name_first'];
		$this->name_middle = $encInfo['name_middle'];
		$this->name_last = $encInfo['name_last'];
		$this->age = $encInfo['age'];
		$this->sex = $encInfo['sex'];
		$this->status = ($this->civil_status) ? $this->civil_status : $encInfo['civil_status'];

						if (trim($encInfo['street_name'])){
													if (trim($encInfo["brgy_name"])!="NOT PROVIDED")
														$street_name = trim($encInfo['street_name']).", ";
													else
														$street_name = trim($encInfo['street_name']).", ";
											}else{
													$street_name = "";
											}

											if ((!(trim($encInfo["brgy_name"]))) || (trim($encInfo["brgy_name"])=="NOT PROVIDED"))
												$brgy_name = "";
											else
												$brgy_name  = trim($encInfo["brgy_name"]).", ";

											if ((!(trim($encInfo["mun_name"]))) || (trim($encInfo["mun_name"])=="NOT PROVIDED"))
												$mun_name = "";
											else{
												if ($brgy_name)
													$mun_name = trim($encInfo["mun_name"]);
												else
													$mun_name = trim($encInfo["mun_name"]);
											}

											if ((!(trim($encInfo["prov_name"]))) || (trim($encInfo["prov_name"])=="NOT PROVIDED"))
												$prov_name = "";
											else
												$prov_name = trim($encInfo["prov_name"]);

											if(stristr(trim($encInfo["mun_name"]), 'city') === FALSE){
												if ((!empty($encInfo["mun_name"]))&&(!empty($encInfo["prov_name"]))){
													if ($prov_name!="NOT PROVIDED")
														$prov_name = ", ".trim($prov_name);
													else
														$prov_name = trim($prov_name);
												}else{
													#$province = trim($prov_name);
													$prov_name = "";
												}
											}else
												$prov_name = "";

											$this->permanent_address = trim($street_name).trim($brgy_name).trim($mun_name).trim($prov_name);

						$this->religion = $encInfo['religion'];
						$this->date_birth = date("F j, Y", strtotime($encInfo['date_birth']));
						$this->place_birth = $encInfo['place_birth'];
						$this->er_opd_diagnosis = $encInfo['er_opd_diagnosis'];
						$this->chief_complaint = $encInfo['chief_complaint'];
						$this->encounter_type = $encInfo['encounter_type'];

		$ColumnWidth = array(35, 36, 12, 67, 28, 22);

		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell($ColumnWidth[0], $this->height_reg, "DATE OF INTERVIEW", "TLR", 0, 'L');
		$this->Cell($ColumnWidth[1], $this->height_reg, "DATE OF ADMISSION/", "TLR", 0, 'L');
		$this->Cell($ColumnWidth[2], $this->height_reg, "WARD", "TL", 0, 'L');    //12
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize2);

						if($encInfo['encounter_type']=='1'){
							$ward = "";
							$er =	$dep_obj->FormalName($encInfo['current_dept_nr']);
							$opd = "";
						}else if($encInfo['encounter_type']=='2'){
							$er = "";
							$ward = "";
							$opd = $dep_obj->FormalName($encInfo['current_dept_nr']);
						}else if($encInfo['encounter_type']=='3' || $encInfo['encounter_type']=='4'){
							if ($encInfo['current_ward_nr'])
                    			$ward_desc = $ward_obj->getWardInfo($encInfo['current_ward_nr']);
                    		$ward = $ward_desc['name'];
							$er = "";
							$opd = "";
						}

		$this->Cell($ColumnWidth[3], $this->height_reg, $ward, "TR", 0, 'L');  //ward
		$this->Line($x, $y+($this->height_reg-0.5), $x+($ColumnWidth[3]-3), $y+($this->height_reg-0.5));
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell($ColumnWidth[4], $this->height_reg, "      HOSPITAL", "TLR", 0, 'L');
		$this->Cell($ColumnWidth[5], $this->height_reg, "MSS NO.", "TLR", 1, 'L');

		$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize2);
		$this->Cell($ColumnWidth[0], $this->height_reg, $this->date_interviewSoc, "LR", 0, 'C');  //date of interview
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell($ColumnWidth[1], $this->height_reg, "CONSULTATION", "LR", 0, 'L');
		$this->Cell($ColumnWidth[2], $this->height_reg, "ER", "L", 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize2);
		$this->Cell($ColumnWidth[3], $this->height_reg, $er, "R", 0, 'L');  //er
		$this->Line($x, $y+($this->height_reg-0.5), $x+($ColumnWidth[3]-3), $y+($this->height_reg-0.5));
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell($ColumnWidth[4], $this->height_reg, "REGISTRY NO", "LR", 0, 'C');
		//$this->Cell($ColumnWidth[4], $this->height_reg, $encInfo['pid'], "LR", 0, 'C');
		$this->Cell($ColumnWidth[5], $this->height_reg, $this->mss, "LR", 1, 'C');

		$this->Cell($ColumnWidth[0], $this->height_reg, "", "LRB", 0);
		$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize2);

						if($encInfo['encounter_type']=='1' || $encInfo['encounter_type']=='2'){
							$admission_dt = date("F j, Y", strtotime($encInfo['encounter_date']));
						}else if($encInfo['encounter_type']=='3' || $encInfo['encounter_type']=='4'){
							$admission_dt = date("F j, Y", strtotime($encInfo['admission_dt']));
						}

		$this->Cell($ColumnWidth[1], $this->height_reg, $admission_dt, "LRB", 0, 'L');
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell($ColumnWidth[2], $this->height_reg, "OPD", "LB", 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize2);
		$this->Cell($ColumnWidth[3], $this->height_reg, $opd, "BR", 0, 'L');
		$this->Cell($ColumnWidth[4], $this->height_reg, $encInfo['pid'], "B", 0, 'C');
		//$this->Line($x, $y+($this->height_reg-0.5), $x+($ColumnWidth[3]-3), $y+($this->height_reg-0.5));
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		//$this->Cell($ColumnWidth[4], $this->height_reg, "", "BLR", 0, 'L');
		$this->Cell($ColumnWidth[5], $this->height_reg, "", "BLR", 1, 'L');

	}

	function DemographicData(){
		global $db;

		if($this->encounter_type=='2' || $this->enc=='0'){
																	$sql_class = "SELECT cgp.discountid, cgp.id_number, sd.parentid, sem.memcategory_id, cpi.is_principal
FROM seg_charity_grants_pid AS cgp
LEFT JOIN care_encounter AS e ON e.pid = cgp.pid
LEFT JOIN seg_discount AS sd ON sd.discountid = cgp.discountid
LEFT JOIN care_person_insurance AS cpi ON cpi.pid = cgp.pid
LEFT JOIN seg_encounter_memcategory AS sem ON sem.encounter_nr = e.encounter_nr    
WHERE cgp.pid='".$this->hrn."'
ORDER BY grant_dte DESC;";
																}else if($this->encounter_type== '1' || $this->encounter_type=='3' || $this->encounter_type=='4'){
																	$sql_class = "SELECT cg.discountid, cg.id_number, sd.parentid, sem.memcategory_id, cpi.is_principal
FROM seg_charity_grants AS cg
LEFT JOIN care_encounter AS e ON e.encounter_nr = cg.encounter_nr
LEFT JOIN seg_discount AS sd ON sd.discountid = cg.discountid
LEFT JOIN care_person_insurance AS cpi ON cpi.pid = e.pid    
LEFT JOIN seg_encounter_memcategory AS sem ON sem.encounter_nr = cg.encounter_nr  
WHERE cg.encounter_nr='".$this->enc."'
ORDER BY cg.grant_dte DESC;";
																}

																$result_class = $db->Execute($sql_class);
																$row_class = $result_class->FetchRow();



		$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize2);
		$this->Cell(200, 4, "I. DEMOGRAPHIC DATA:", 0,1,'L');
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell(128, $this->height_reg, "PATIENT'S NAME:", "TLR", 0, 'L');
		$this->Cell(15, $this->height_reg, "AGE:", "TLR", 0, 'L');
		$this->Cell(15, $this->height_reg, "GENDER", 1, 0, 'C');
		$this->Cell(42, $this->height_reg, "CIVIL STATUS", 1, 1, 'C');

		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize1);
		$this->Cell(38, $this->height_reg, "SURNAME", "L", 0, 'L');
		$this->Cell(58, $this->height_reg, "FIRST", "", 0, 'L');
		$this->Cell(32, $this->height_reg, "MIDDLE", "R", 0, 'L');
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell(15, $this->height_reg, $this->age, "LR", 0, 'C'); //Age
		$this->Cell(8, $this->height_reg, "F", "LR", 0, 'C');
		$this->Cell(7, $this->height_reg, "M", "LR", 0, 'C');
		$this->Cell(7, $this->height_reg, "S", "LR", 0, 'C');
		$this->Cell(7, $this->height_reg, "M", "LR", 0, 'C');
		$this->Cell(7, $this->height_reg, "SEP", "LR", 0, 'C');
		$this->Cell(7, $this->height_reg, "W", "LR", 0, 'C');
		$this->Cell(7, $this->height_reg, "D/A", "LR", 0, 'C');
		$this->Cell(7, $this->height_reg, "L", "LR", 1, 'C');


		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize1);
		$x1 = $this->GetX();
		$y1 = $this->GetY();
		//$this->Line($x1+1, $y1+3.5, $x1+138, $y1+3.5);
		$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize2);
		$this->Cell(38, $this->height_reg, $this->name_last, "L", 0, 'L'); //Last Name
		$this->Cell(58, $this->height_reg, $this->name_first, 0, 0, 'L');  // First Name
		$this->Cell(32, $this->height_reg, $this->name_middle, "R", 0, 'L'); // Middle Name
		$this->Cell(15, $this->height_reg, "", "RB",0);
		/*$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell(15, $this->height_reg, $row2['Age'], "LR", 0, 'C'); //Age
		$this->Cell(8, $this->height_reg, "F", "LR", 0, 'C');
		$this->Cell(7, $this->height_reg, "M", "LR", 0, 'C');
		$this->Cell(7, $this->height_reg, "S", "LR", 0, 'C');
		$this->Cell(7, $this->height_reg, "M", "LR", 0, 'C');
		$this->Cell(7, $this->height_reg, "SEP", "LR", 0, 'C');
		$this->Cell(7, $this->height_reg, "W", "LR", 0, 'C');
		$this->Cell(7, $this->height_reg, "D/A", "LR", 1, 'C');  */

		//$this->Cell(40, $this->height_reg, "SURNAME", "L", 0, 'L');
		//$this->Cell(60, $this->height_reg, "FIRST", 0, 0, 'L');
		//$this->Cell(40, $this->height_reg, "MIDDLE", "R", 0, 'L');
		//$this->Cell(10, $this->height_reg, "", "LRB", 0);

			if($this->sex == "m")
				$ans1 = "X";
			else
				$ans2 = "X";

		$this->SetFont($this->fontfamily1, $this->fontstylenormal, 16);
		$this->Cell(8, $this->height_reg, $ans2, "LRB", 0, 'C'); //Female
		$this->Cell(7, $this->height_reg, $ans1, "LRB", 0, 'C'); //Male
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
            
            if($this->status){
                $status = $this->status;
            }else{
                $status =  $row_class['status'];
            }

			if($status =="single"){
				$stat1 = "X";
			}elseif($status =="live_in"){
				$stat6 = "X";
			}elseif($status =="married"){
				$stat2 = "X";
			}elseif($status =="separated"){
				$stat3 = "X";
			}elseif($status=="widow/er"){
				$stat4 = "X";
			}elseif($status=='divorced' || $status=='annulled'){
				$stat5 = "X";
			}
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, 16); //Arial, ''
		$this->Cell(7, $this->height_reg, $stat1, "LRB", 0, 'C');  //Single
		$this->Cell(7, $this->height_reg, $stat2, "LRB", 0, 'C');  //Married
		$this->Cell(7, $this->height_reg, $stat3, "LRB", 0, 'C');  //Separated
		$this->Cell(7, $this->height_reg, $stat4, "LRB", 0, 'C');  //Widow/er
		$this->Cell(7, $this->height_reg, $stat5, "LRB", 0, 'C');  //D/A
		$this->Cell(7, $this->height_reg, $stat6, "LRB", 1, 'C');
		//address, religion, date of birth and place of birth
			if($row2['street_name']){
				$address .= $row2['street_name']." ";
			}
			if($row2['brgy_name']){
				$address .= $row2['brgy_name']." ";
			}
		$address .= $row2['mun_name'];

		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell(100, $this->height_reg, "ADDRESS", "TLR", 0, 'C');
		$this->Cell(35, $this->height_reg, "RELIGION", "TLR", 0, 'C');
		$this->Cell(65, $this->height_reg, "DATE OF BIRTH", "TLR", 1, 'L');
		$this->Cell(50, $this->height_reg, "Permanent", "L", 0, 'L');
		$this->Cell(50, $this->height_reg, "Temporary", "LR", 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
        if($this->religionSoc){
            $rel = $this->religionSoc;
            $sql_religion = "SELECT religion_name FROM seg_religion WHERE religion_nr = $rel";
            $result_re = $db->Execute($sql_religion);
            $row_re = $result_re->FetchRow();
            $religion = $row_re['religion_name'];     
        }else{
            $religion = $this->religion; 
        }
		$this->MultiCell(35, $this->height_reg, $religion, 0, 'L'); //religion
		$this->SetXY($x+35, $y);
		$this->Cell(65, $this->height_reg, $this->date_birth, "LRB", 1, 'L'); //date of birth
		$x2 = $this->GetX();
		$y2 = $this->GetY();
              
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize1);
		$this->MultiCell(50, $this->height_reg-2, $this->permanent_address, 0, 'L'); //address 
        $this->SetXY($x2+50, $y2);
        $this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize1);
        $this->MultiCell(50, $this->height_reg-2, $this->address, 0, 'L'); //temp address
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->SetXY($x2+50, $y2);
		$this->Cell(50, $this->height_reg, "", "LR", 0);
		$this->SetXY($x2+135, $y2);
		$this->Cell(65, $this->height_reg, "PLACE OF BIRTH", "TLR", 1, 'L');
		$x3 = $this->GetX();
		$y3 = $this->GetY();

		$this->SetXY($x3+50, $y3);
		$this->Cell(50, $this->height_reg, "", "LR", 0);
		$this->SetXY($x3+135, $y3);
		$this->Cell(65, $this->height_reg, $this->place_birth, "BLR", 1, 'L'); //place of birth
		$y4 = $this->GetY();
		$this->Line($x2, $y2, $x2, $y4);
		$this->Cell(50, $this->height_reg, "EDUC. ATTAINMENT", "TLR", 0, 'L');
		$this->Cell(85, $this->height_reg, "EMPLOYER", "TLR", 0, 'L');
		$this->Cell(65, $this->height_reg, "MONTHLY INCOME", "TLR", 1, 'L');

		$sql_educ = "SELECT educ_attain_name FROM seg_educational_attainment
		WHERE educ_attain_nr='$this->educational_attain'";
		$result_educ = $db->Execute($sql_educ);
		$row_educ = $result_educ->FetchRow();

		$this->Cell(50, $this->height_reg, $row_educ['educ_attain_name'], "LRB", 0, 'L'); //educational attainment
		$this->Cell(85, $this->height_reg, $this->employer, "LRB", 0, 'L');
		$this->Cell(65, $this->height_reg, "Php ".$this->monthly_income,"LRB", 1, 'R');

		$this->Cell(50, $this->height_reg, "OCCUPATION", "TLR", 0, 'L');
		$this->Cell(85, $this->height_reg, "ADDRESS", "TLR", 0, 'L');
		$this->Cell(65, $this->height_reg, "PER CAPITA INCOME", "TLR", 1, 'L');
        if($this->occupation){
            $occu_nr = $this->occupation;
        }else{
            $occu_nr = $row_class['occupation']; 
        }
        
		$sql_occupation = "SELECT source_income_desc FROM seg_source_income
											WHERE source_income_id = '$occu_nr'";
		$result_occupation = $db->Execute($sql_occupation);
		$row_occupation = $result_occupation->FetchRow();
		$this->patient_occupation = ($row_occupation['source_income_desc']!='Others') ? $row_occupation['source_income_desc'] : $this->other_occupation;

		$this->Cell(50, $this->height_reg, $this->patient_occupation , "LRB", 0, 'L');
		$this->Cell(85, $this->height_reg, $this->employer_address, "LRB", 0, 'L');
		$this->Cell(65, $this->height_reg, "Php ".$this->per_capita_income,"LRB", 1, 'R');

		$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize3);
		$this->Cell(38, $this->height_reg, "PHILHEALTH", "TLR", 0, 'L');
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell(12, $this->height_reg, "", "TLR", 0);
		$this->Cell(12, $this->height_reg, "", "TLR", 0);
		$this->Cell(12, $this->height_reg, "", "TLR", 0);
		$this->Cell(12, $this->height_reg, "NON", "TLR", 0, 'C');
		$this->Cell(12, $this->height_reg, "", "TLR", 0);
		$this->Cell(15, $this->height_reg, "", "TLR", 0);
		$this->Cell(87, $this->height_reg, "", "TLR", 0);

		$this->Ln();

		//-----------------Comment-------------------------
		$this->SetFont('Arial', '', 9);
		$this->Cell(38, $this->height_reg, "", "BLR", 0, 'L');
		$this->Cell(12, $this->height_reg, "GOV'T", "BLR", 0, 'C');
		$this->Cell(12, $this->height_reg, "PRI", "BLR", 0, 'C');
		$this->Cell(12, $this->height_reg, "IPM", "BLR", 0, 'C');
		$this->Cell(12, $this->height_reg, "PAY", "BLR", 0, 'C');
		$this->Cell(12, $this->height_reg, "OFW", "BLR", 0, 'C');
		$this->Cell(15, $this->height_reg, "OTHERS", "BLR", 0, 'C');
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(87, $this->height_reg, "MSS CLASSIFICATION", "BLR", 1, 'C');
		/*
		$this->SetFont('Arial', '', 9);
		$this->Cell(38, 4, "", "LRB", 0, 'L');
		$this->Cell(12, 4, "", "LRB", 0, 'C');
		$this->Cell(12, 4, "", "LRB", 0, 'C');
		$this->Cell(12, 4, "", "LRB", 0, 'C');
		$this->Cell(12, 4, "PAY", "LRB", 0, 'C');
		$this->Cell(12, 4, "", "LRB", 0, 'C');
		$this->Cell(15, 4, "", "LRB", 0, 'C');
		$this->Cell(87, 4, "", "LRB", 1, 'C');   */

			if(($row_class['is_principal']==1 )&& ($row_class['memcategory_id']==1)){
				$memgov = "X";
			}elseif(($row_class['is_principal'] == 1) && ($row_class['memcategory_id']==2)){
				 $mempri = "X";
			}elseif(($row2['is_principal']==1) && ($row2['memcategory_id']==3)){
				 $memofw = "X";
			}elseif(($row2['is_principal']==1) && ($row2['memcategory_id']==4)){
				 $memipm = "X";
			}
			elseif(($row2['is_principal']==1) && ($row2['memcategory_id']==5) ){
				 $memothers = "X";
			}elseif(($row2['is_principal']==1) && ($row2['memcategory_id']==6)){
				 $memnonpay = "X";
			}
		$this->SetFont('Arial', '', 8.5);
		$this->Cell(38, $this->height_reg, "MEMBER", "LRB", 0, 'L');
		$this->Cell(12, $this->height_reg, $memgov, "LRB", 0, 'C');    //member and gov't     1
		$this->Cell(12, $this->height_reg, $mempri, "LRB", 0, 'C');    //member and pri       2
		$this->Cell(12, $this->height_reg, $memipm, "LRB", 0, 'C');    //member and ipm       5
		$this->Cell(12, $this->height_reg, $memnonpay, "LRB", 0, 'C');    //member and non-pay   6
		$this->Cell(12, $this->height_reg, $memofw, "LRB", 0, 'C');    //member and ofw        ?
		$this->Cell(15, $this->height_reg, $memothers, "LRB", 0, 'C');    //member and others     3, 4

		$discountid = $row2['MSW_Class'];
		if($row2['MSW_Class']=='A' || $row2['MSW_Class']=='B' || $row2['MSW_Class']=='C1' || $row2['MSW_Class']=='C2' || $row2['MSW_Class']=='C3' || $row2['MSW_Class']=='D'){
			if($row2['MSW_Class']=="A"){
				$a = "X";
			}elseif($row2['MSW_Class']=="B"){
				$b = "X";
			}elseif($row2['MSW_Class']=="C1"){
				$c1 = "X";
			}elseif($row2['MSW_Class']=="C2"){
				$c2 = "X";
			}elseif($row2['MSW_Class']=="C3"){
				$c3 = "X";
			}elseif($row2['MSW_Class']=="D"){
				$d = "X";
			}

		}else{
			$sql_id = "SELECT d.discountid ,d.parentid FROM seg_discount AS d WHERE d.discountid = '$discountid';";
			$result_id = $db->Execute($sql_id);
			$row_id = $result_id->FetchRow();
			if($row_id['parentid']){
					if($row_id['parentid']=="A" || $row['']){
						$a = "X";
					}elseif($row_id['parentid']=="B"){
						$b = "X";
					}elseif($row_id['parentid']=="C1"){
						$c1 = "X";
					}elseif($row_id['parentid']=="C2"){
						$c2 = "X";
					}elseif($row_id['parentid']=="C3"){
						$c3 = "X";
					}elseif($row_id['parentid']=="D"){
						$d = "X";
					}
			}
		}


														if($row_class['discountid']=='A' || $row_class['parentid']=='A'){
																	$a = "X";
																}else if($row_class['discountid']=='C1' || $row_class['parentid']=='C1'){
																	$c1 = "X";
																}else if($row_class['discountid']=='C3' || $row_class['parentid']=='C3'){
																	$c3 = "X";
																}else if($row_class['discountid']=='B' || $row_class['parentid']=='B'){
																	$b = "X";
																}else if($row_class['discountid']=='C2' || $row_class['parentid']=='C2'){
																	$c2 = "X";
																}else if($row_class['discountid']=='D' || $row_class['parentid']=='D'){
																	$d = "X";
																}


		$this->Cell(22, $this->height_reg, "A", 1, 0, 'L');
		$this->Cell(5, $this->height_reg, $a, 1, 0);             //A
		$this->Cell(25, $this->height_reg, "C1", 1, 0, 'L');
		$this->Cell(5, $this->height_reg, $c1, 1, 0);            //C1
		$this->Cell(25, $this->height_reg, "C3", 1, 0, 'L');
		$this->Cell(5, $this->height_reg, $c3, 1, 1);            //C3


			if(($row_class['is_principal']==0 )&& ($row_class['memcategory_id']==1)){
				$depgov = "X";
			}elseif(($row_class['is_principal'] == 0) && ($row_class['memcategory_id']==2)){
				 $deppri = "X";
			}elseif(($row_class['is_principal']==0) && ($row_class['memcategory_id']==3 || $row_class['memcategory_id']==4)){
				 $depothers = "X";
			}elseif(($row_class['is_principal']==0) && ($row_class['memcategory_id']==5) ){
				 $depipm = "X";
			}elseif(($row_class['is_principal']==0) && ($row_class['memcategory_id']==6)){
				 $depnonpay = "X";
			}
		$this->Cell(38, $this->height_reg, "DEPENDENT", "LRB", 0, 'L');
		$this->Cell(12, $this->height_reg, $depgov, "LRB", 0, 'C');
		$this->Cell(12, $this->height_reg, $deppri, "LRB", 0, 'C');
		$this->Cell(12, $this->height_reg, $depipm, "LRB", 0, 'C');
		$this->Cell(12, $this->height_reg, $depnonpay, "LRB", 0, 'C');
		$this->Cell(12, $this->height_reg, $depofw, "LRB", 0, 'C');
		$this->Cell(15, $this->height_reg, $depothers, "LRB", 0, 'C');
		$this->Cell(22, $this->height_reg, "B", 1, 0, 'L');
		$this->Cell(5, $this->height_reg, $b, 1, 0);             //B
		$this->Cell(25, $this->height_reg, "C2", 1, 0, 'L');
		$this->Cell(5, $this->height_reg, $c2, 1, 0);            //C2
		$this->Cell(25, $this->height_reg, "D", 1, 0, 'L');
		$this->Cell(5, $this->height_reg, $d, 1, 1);            //D

		//other sectorial membership
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell(200, $this->height_reg, "OTHER SECTORIAL MEMBERSHIP", 1, 1, 'C');

		//memberships
		$this->Cell(30, $this->height_reg, "SEN. CITIZEN", "TLR", 0, 'L');
		$this->Cell(30, $this->height_reg, "BRGY OFFICIAL", "TLR", 0, 'L');
		$this->Cell(10, $this->height_reg, "BHW", "TLR", 0, 'L');
		$this->Cell(15, $this->height_reg, "PWD", "TLR", 0, 'L');
		$this->Cell(25, $this->height_reg, "INDIGENOUS", "TLR", 0, 'L');
		$this->Cell(35, $this->height_reg, "INSTITUTIONALIZED", "TLR", 0, 'L');
		$this->Cell(30, $this->height_reg, "VAWC", "TLR", 0, 'L');
		$this->Cell(25, $this->height_reg, "OTHERS", "TLR", 1, 'L');

			if($row_class['discountid'] =='SC'){
				$sc = "X";
			}elseif($row_class['discountid']=='Brgy'){
				$brgy = "X";
			}elseif($row_class['discountid']=='BHW'){
				$bhw = "X";
			}elseif($row_class['discountid']=='PWD'){
				$pwd = "X";
			}elseif($row_class['discountid']=='Indi'){
				$indi = "X";
			}elseif($row_class['discountid']=='Ins'){
				$ins = "X";
			}elseif($row_class['discountid']=='VA'){
				$va = "X";
			}elseif($row_class['discountid']=='OT'){
				$ot = "X";
			}
		$this->Cell(30, $this->height_reg, $sc, "LRB", 0, 'C');
		$this->Cell(30, $this->height_reg, $brgy, "LRB", 0, 'C');
		$this->Cell(10, $this->height_reg, $bhw, "LRB", 0, 'C');
		$this->Cell(15, $this->height_reg, $pwd, "LRB", 0, 'C');
		$this->Cell(25, $this->height_reg, $indi, "LRB", 0, 'C');
		$this->Cell(35, $this->height_reg, $ins, "LRB", 0, 'C');
		$this->Cell(30, $this->height_reg, $va, "LRB", 0, 'C');
		$this->Cell(25, $this->height_reg, $ot, "LRB", 1, 'C');
	}

	//added by Nick, 11/30/2013 8:32
	function getStatus($status){
		if($status =="single"){
            $stat = "S";
        }elseif($status =="married"){
            $stat = "M";
        }elseif($status =="separated (legally)" || $status =="separated (in fact)"){
            $stat = "SEP";
        }elseif($status=="widowed"){
            $stat = "W";
        }elseif($status=='divorced'){
            $stat = "D";
        }elseif($status=='child'){
            $stat = "C";
        }

        return $stat;
	}

	function getPatientPersonalInfo($pid){
		global $db;
		$record = array();

		$sql = "SELECT  CONCAT(cp.`name_first`,' ',cp.`name_middle`,' ' ,cp.`name_last`) AS pat_name,
						TIMESTAMPDIFF(YEAR,cp.`date_birth`,CURDATE()) AS age,
						core.`status` AS status,
						'(PATIENT)' AS relation,
						(SELECT educ.`educ_attain_name` FROM seg_educational_attainment AS educ WHERE educ.`educ_attain_nr` = core.`educational_attain`) AS educ_attain,
						(SELECT occu.`occupation_name` FROM seg_occupation AS occu WHERE occu.`occupation_nr` = cp.`occupation`) AS occupation,
						core.`income` AS income
				FROM seg_socserv_patient AS core
				INNER JOIN care_person AS cp ON core.`pid` = cp.`pid`
				WHERE core.`pid` = ".$db->qstr($pid);
		#echo $sql; exit();
		$rs = $db->Execute($sql);		
		if($rs->RecordCount()>0){
			$data = $rs->FetchRow();
				return $data;
		}else{
			return false;
		}
	}
	//end Nick


	function FamilyComposition(){
        global $db;

        $w = array(79, 10, 15, 25, 30, 23, 18);

		$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize2);   //Arial, 'B', 9
		//$this->Cell($this->full, $this->height2, "SOCIO ECONOMIC PROFILE", "TLR",1,'L');
		$this->Cell($this->full, $this->height2, " FAMILY COMPOSITION", "LRB", 1, 'C');

		//table header
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);   //Arial, '', 9
		$this->Cell(79, $this->height_reg, "NAME", "TLR", 0, 'C');
		$this->Cell(10, $this->height_reg, "AGE", "TLR", 0, 'C');
		$this->Cell(15, $this->height_reg, "STATUS", "TLR", 0, 'C');
		$this->Cell(25, $this->height_reg, "RELATION", "TLR", 0, 'C');
		$this->Cell(30, $this->height_reg, "EDUC.", "TLR", 0, 'C');
		$this->Cell(23, $this->height_reg, "OCCUPATION", "TLR", 0, 'C');
		$this->Cell(18, $this->height_reg, "MONTHLY", "TLR", 1, 'C');

		$this->Cell(79, $this->height_reg, "", "LRB", 0, 'C');
		$this->Cell(10, $this->height_reg, "", "LRB", 0, 'C');
		$this->Cell(15, $this->height_reg, "", "LRB", 0, 'C');
		$this->Cell(25, $this->height_reg, "TO PATIENT", "LRB", 0, 'C');
		$this->Cell(30, $this->height_reg, "ATTAINMENT", "LRB", 0, 'C');
		$this->Cell(23, $this->height_reg, "", "LRB", 0, 'C');
		$this->Cell(18, $this->height_reg, "INCOME", "LRB", 1, 'C');
	                
		//added by Nick, 12/1/2013 4:10 PM
		if($isPatIncluded==0){
			$this->SetDataFont();
			$x = $this->GetX();
			$y = $this->GetY();
			$patInfo = $this->getPatientPersonalInfo($this->hrn);

			if( $patInfo ){
				$isPatIncluded = 1;
				$this->Cell($w[1]+69, 4, '1. ' . $patInfo['pat_name'], 'LBT',  0, 'L');
				$this->Cell($w[2]-5, 4, $patInfo['age'], 'LBT', 0, 'C');
				$this->Cell($w[3]-10, 4, $this->getStatus($patInfo['status']), 'LBT', 0, 'C');
				$this->Cell($w[4]-5, 4, '(PATIENT)', 'LBT', 0, 'C');
				$this->Cell($w[5]+7, 4, $patInfo['educ_attain'], 'LBT', 0, 'C');
				$this->Cell($w[6]+5, 4, $this->patient_occupation, 'LBT', 0, 'C');
				$this->Cell($w[7]+18, 4, $patInfo['income'], 'LBTR', 1, 'C');
			}
		}
		//end Nick

        $cnt = 1;
        $sql_relation = "SELECT * 
                         FROM seg_social_patient_family
                         Where encounter_nr = $this->enc AND pid = $this->hrn";
        $rs_relation = $db->Execute($sql_relation);
        $c = $rs_relation->RecordCount();

        if (is_object($rs_relation)){
            while ($row_relation=$rs_relation->FetchRow()) {
//created by Daryl
//condition for multicell wordwrapping and arrangement of cell
//10-25-2013
        $this->SetDataFont();
		$x = $this->GetX();
		$y = $this->GetY();

		
		$Name_dep = ($cnt++)+$isPatIncluded.". ". $row_relation['dependent_name'];//edited by Nick, 12/1/2013 3:30 PM
        $Age_dep = $row_relation['dependent_age'];
        $status = strtolower($row_relation['dependent_status']);
     	$stat_dep = $this->getStatus($status);//edited by Nick, 12/1/2013 3:30 PM

        $rel_dep = $row_relation['relation_to_patient'];
        $educ_dep = $row_relation['dep_educ_attainment'];
        $ocu_dep = $row_relation['dependent_occupation'];
        $mon_in_dep = $row_relation['dep_monthly_income'];

		$data = array();
		$data[] = array($Name_dep, $Age_dep, $stat_dep, $rel_dep, $educ_dep, $ocu_dep, $mon_in_dep);
		
		foreach($data as $row){	
			$y1 = $this->GetY();
			$this->MultiCell($w[0], 4, $row[0], 'LRBT');	
			$y2 = $this->GetY();
			$yH = $y2 - $y1;
						
			$this->SetXY($x + $w[0], $this->GetY() - $yH);
			
			$this->Cell($w[1], $yH, $row[1], 'LBT',  0, 'C');
			$this->Cell($w[2], $yH, $row[2], 'LBT', 0, 'C');
			$this->Cell($w[3], $yH, $row[3], 'LBT', 0, 'C');
			$this->Cell($w[4], $yH, $row[4], 'LBT', 0, 'C');
			$this->Cell($w[5], $yH, $row[5], 'LBT', 0, 'C');
			$this->Cell($w[6], $yH, $row[6], 'LBT', 0, 'C');
			$this->Cell($w[7], $yH, $row[7], 'LBT', 0, 'C');
				
			$this->Ln();
		}
            }
        }
		
        for($cnt = $c+$isPatIncluded; $cnt<10; $cnt++){//edited by Nick, 12/1/2013 3:30
            $this->Cell(79, $this->height_reg, ($cnt+1).".", 1, 0, 'L');
            $this->Cell(10, $this->height_reg, "", 1, 0, 'C');
            $this->Cell(15, $this->height_reg, "", 1, 0, 'C');
            $this->Cell(25, $this->height_reg, "", 1, 0, 'C');
            $this->Cell(30, $this->height_reg, "", 1, 0, 'C');
            $this->Cell(23, $this->height_reg, "", 1, 0, 'C');
            $this->Cell(18, $this->height_reg, "", 1, 1, 'C');
        }
        

		$this->Cell($this->half, $this->height_reg, "OTHER SOURCE INCOME:", "TLR",0,'L');
		$this->Cell($this->half, $this->height_reg, "REMARKS", "TLR",1,'L');
		$this->SetWidths(array($this->half,$this->half));
		$this->Row(array($this->other_source_income,$this->monthly_income_remarks));

	}



	function MonthlyExpenses(){
		global $db;
		$ans1 = " ";
		$ans2 = " ";
		$ans3 = " ";
		$ans4 = " ";
		$ans5 = " ";
		if($this->enc!='0'){
				$sql3 = "SELECT DISTINCT sg.house_type, sg.ligth_expense, sg.food_expense, sg.transport_expense, sg.water_expense, sg.total_monthly_expense,
						sg.education_expense, sg.househelp_expense, sg.fuel_expense, sg.clothing_expense, sg.med_expenditure, sg.insurance_mortgage, sg.other_expense
						FROM seg_socserv_patient AS sg
						WHERE sg.encounter_nr = $this->enc
						";	
		}else{
				$sql3 = "SELECT DISTINCT sg.house_type, sg.ligth_expense, sg.food_expense, sg.transport_expense, sg.water_expense, sg.total_monthly_expense,
						sg.education_expense, sg.househelp_expense, sg.fuel_expense, sg.clothing_expense, sg.med_expenditure, sg.insurance_mortgage, sg.other_expense
						FROM seg_socserv_patient AS sg
						WHERE sg.encounter_nr = $this->enc AND sg.pid = $this->hrn
						";
		}


		#echo $sql3;
		$result3 = $db->Execute($sql3);
		$row3 = $result3->FetchRow();

				if($row3['house_type']==1)
					$ans1 = "X";
				else if($row3['house_type']==2)
					$ans2 = "X";
				else if($row3['house_type']==3)
					$ans3 = "X";
				else if($row3['house_type']==4)
					$ans4 = "X";
				else if($row3['house_type']==5)
					$ans5 = "X";

		//$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize2);  //Arial, 'B', 9
		//$this->Cell(200, $this->height_reg, "B. Monthly Expenses", 1,1,'L');
		$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize2);  //Arial, 'B', 9
		$this->Cell(200, $this->height_reg, "MONTHLY EXPENSES", "",1,'C');

		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2); //Arial, 'B', 9
		$this->Cell(50, $this->height_reg, "HOUSE & LOT", "TLR", 0, 'L');
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize3);
		$this->Cell(50, $this->height_reg, "LIGHT", "TLR", 0, 'L');
		$this->Cell(50, $this->height_reg, "FOOD/PROVISIONS", "TLR", 0, 'L');
		$this->Cell(50, $this->height_reg, "TRANSPORTATION", "TLR", 1, 'L');

		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell(50, $this->height_reg, "    (".$ans1.") Free", "L", 0, 'L');
		$this->Cell(50, $this->height_reg, " Php ".$row3['ligth_expense'], "BLR", 0, 'R');
		$this->Cell(50, $this->height_reg, "Php ".$row3['food_expense'], "BLR", 0, 'R');
		$this->Cell(50, $this->height_reg, "Php ".$row3['transport_expense'], "BLR", 1, 'R');

		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell(50, $this->height_reg, "    (".$ans2.") Owned", "L", 0, 'L');
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize3);
		$this->Cell(50, $this->height_reg, "WATER", "TLR", 0, 'L');
		$this->Cell(50, $this->height_reg, "EDUCATION", "TLR", 0, 'L');
		$this->Cell(50, $this->height_reg, "HOUSEHELP", "TLR", 1, 'L');

		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell(50, $this->height_reg, "    (".$ans3.") Rent", "L", 0, 'L');
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell(50, $this->height_reg, " Php ".$row3['water_expense'], "BLR", 0, 'R');
		$this->Cell(50, $this->height_reg, "Php ".$row3['education_expense'], "BLR", 0, 'R');
		$this->Cell(50, $this->height_reg, "Php ".$row3['househelp_expense'], "BLR", 1, 'R');

		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell(50, $this->height_reg, "    (".$ans4.") Shared", "L", 0, 'L');
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize3);
		$this->Cell(50, $this->height_reg, "FUEL", "TLR", 0, 'L');
		$this->Cell(50, $this->height_reg, "CLOTHING", "TLR", 0, 'L');
		$this->Cell(50, $this->height_reg, "MED. EXPENDITURE", "TLR", 1, 'L');

		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell(50, $this->height_reg, "    (".$ans5.") Monthly Amortization", "LB", 0, 'L');
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);
		$this->Cell(50, $this->height_reg, " Php ".$row3['fuel_expense'], "BLR", 0, 'R');
		$this->Cell(50, $this->height_reg, "Php ".$row3['clothing_expense'], "BLR", 0, 'R');
		$this->Cell(50, $this->height_reg, "Php ".$row3['med_expenditure'], "BLR", 1, 'R');

		$this->Cell(100, $this->height_reg, "INSURANCE/MORTGAGE: "."Php ".$row3['insurance_mortgage'], 1,0,'L');
		$this->Cell(100, $this->height_reg, "OTHERS: "."Php ".$row3['other_expense'], 1,1,'L');

		$this->Cell(200, $this->height_reg, "TOTAL MONTHLY EXPENDITURE: "."Php ".$row3['total_monthly_expense'], 1, 1, 'L');
		$this->Cell(200, $this->height_reg, "REMARKS : ".$this->monthly_expenses_remarks, 1,1,'L');

	$this->Ln();
	}



	//edited by Daryl
	//edit the page number
	//10/29/2013
	 function Footer()
	{
		$this->AliasNbPages();

 		$this->SetY(-10);
        $this->SetFont('Arial', '', 8);
        $this->Cell(0,10,'SPMC-F-MSWD-03', 0, 0, 'L');
        $this->Cell(0,10,'Page '.$this->PageNo().' of {nb}', 0, 0, 'R');
        
		}


	function MedicalData(){
		global $db;

		$this->SetFont($this->fontfamily1, $this->fontstylebold, 10);    //Arial, 'B'
		$this->Cell($this->full, $this->height_reg, "II. MEDICAL DATA", 0, 1, 'L');

		if($this->encounter_type=='2'){
			$diagnosis = $this->chief_complaint;
				}else{
			$diagnosis = $this->er_opd_diagnosis;
		}

		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize2);  //Arial, '', 9

		$this->Cell($this->half, $this->height_reg, "Admitting Diagnosis:", "TLR",0,'L');
		$this->Cell($this->half, $this->height_reg, "Final Diagnosis:", "TLR",1,'L');
		$this->SetWidths(array($this->half,$this->half));
		$this->Row(array($diagnosis,$this->final_diagnosis));

		$this->Cell($this->half, $this->height_reg, "Duration of Problem/symptoms:", "TLR",0,'L');
		$this->Cell($this->half, $this->height_reg, "Previous Treatment/Duration:", "TLR",1,'L');
		$this->SetWidths(array($this->half,$this->half));
		$this->Row(array($this->duration_problem,$this->duration_treatment));

		$this->Cell($this->half, $this->height_reg, "Present Treatment Plan:", "TLR",0,'L');
		$this->Cell($this->half, $this->height_reg, "Health Accessibility Problem:", "TLR",1,'L');
		$this->SetWidths(array($this->half,$this->half));
		$this->Row(array($this->treatment_plan,$this->accessibility_problem));

        $sql_source_referral = "SELECT source FROM seg_social_source_referral WHERE source_nr ='$this->source_referral'";
        $rs_source_referral = $db->Execute($sql_source_referral);
        $row = $rs_source_referral->FetchRow();
		$this->Cell(70, $this->height_reg, "SOURCE OF REFERRAL", "TLR", 0, 'L');
		$this->Cell(90, $this->height_reg, "AGENCY: ".$row['source'], "TLR", 0, 'L');
		$this->Cell(40, $this->height_reg, "CONTACT NO:", "TLR", 1, 'L');

		//$this->Cell(70, $this->height_reg, "Mr./Ms. ", "LR", 0, 'L');
		$this->Cell(12, $this->height_reg, "Mr./Ms. ", "L", 0, 'L');
		$x = $this->GetX();
		$y = $this->GetY();
		$this->Line($x, $y+($this->height_reg-0.5), $x+53, $y+($this->height_reg-0.5));
		$this->Cell(58, $this->height_reg, ucwords($this->name_referral), "R", 0, 'L');  //informant

		$this->Cell(90, $this->height_reg, "ADDRESS", "LR", 0, 'L');
		$this->Cell(40, $this->height_reg, $this->info_contact_no, "LR", 1, 'L');

		$this->Cell(70, $this->height_reg, "", "LRB", 0, 'L');
		$this->Cell(90, $this->height_reg, $this->info_agency, "LRB", 0, 'L');
		$this->Cell(40, $this->height_reg, "", "LRB", 1, 'L');


		$this->Cell(150, $this->height_reg, "REMARKS", "TLR",0,'L');
		$this->Cell(50, $this->height_reg, "SOCIAL WORKER", "TLR",1,'L');
		$this->SetWidths(array(150,50));
		$this->Row(array($this->remarks,$this->social_worker));

		}


	function Functioning()
	{
		$objSS = new SocialService();
		$c=1;
		$rs_assessHead = $objSS->getAssessHeader();
		if (is_object($rs_assessHead)) {
			$this->Cell($this->full, $this->height_reg, "", 0, 1, 'L');
			$this->Cell(140, 8, "", 0, 1, 'L');
			$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize2);
			$this->Cell(200, 4,"III. ASSESSMENT OF SOCIAL FUNCTIONING", 0,1,'L');	

			$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize1);
			$this->Cell(57, $this->height_reg, "Social Roles", "TLB", 0, 'L');
			$this->Cell(35, $this->height_reg, "Social Interactions", "TB", 0, 'L');
			$this->Cell(35, $this->height_reg, "Severity Index", "TB", 0, 'L');
			$this->Cell(35, $this->height_reg, "Duration Index", "TB", 0, 'L');
			$this->Cell(35, $this->height_reg, "Coping Index", "TBR", 1, 'L');

    		while($result_header = $rs_assessHead->Fetchrow()) {
    			$group = $result_header['group'];
    			if($group=='SF') {
    				$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize1);
					$this->Cell(57, $this->height_reg,$result_header['id'].'. '.strtoupper($result_header['desc']), "TLR", 0, 'L');
					$this->Cell(140, $this->height_reg, "", "TBR", 1, 'L');
		}

    			$rs_assessDetails = $objSS->getAssessDetails($result_header['id']);
            	if(is_object($rs_assessDetails)){
               		while($result_details= $rs_assessDetails->Fetchrow()){
               			if ($group=='SF') {
							$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize1);

							$data = $objSS->getPatientFunctioning($result_details['id'],$_GET['encounter_nr'],$_GET['pid']);

							if(strtoupper($result_details['desc'])!='OTHER'){
								$this->Cell(57, $this->height_reg, " - ".$result_details['desc'], "BLR", 0, 'L');
							}else{
								$this->Cell(57, $this->height_reg, " - ".$result_details['desc']." ( ".$data['others']." )", "BLR", 0, 'L');
		}

							$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize1);
							$this->Cell(35, $this->height_reg, $data['type_of_interaction'], "TBR", 0, 'L');
							$this->Cell(35, $this->height_reg, $data['severity_index'], "TBR", 0, 'L');
							$this->Cell(35, $this->height_reg, $data['duration_index'], "TBR", 0, 'L');
							$this->Cell(35, $this->height_reg, $data['coping_index'], "TBR", 1, 'L');	
		}
		}
		}
        
        }
        }
        }


	function ProblemEnvironment()
	{

		global $db;
		$objSS = new SocialService();
		$c=1;
		$rs_assessHead = $objSS->getAssessHeader();
		if (is_object($rs_assessHead)) {
			$this->Cell($this->full, $this->height_reg, "", 0, 1, 'L');
			$this->Cell(140, 8, "", 0, 1, 'L');
			$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize2);
			$this->Cell(200, 4,"IV. PROBLEMS IN THE	ENVIRONMENT", 0,1,'L');	

			$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize1);
			$this->Cell(10, $this->height_reg2, "", "TL", 0, 'L');
			$this->Cell(100, $this->height_reg2, "Economic/Basis Needs Systems Problems", "TBL", 0, 'L');
			$this->Cell(38.5, $this->height_reg2, "Severity Index", "TB", 0, 'L');
			$this->Cell(38.5, $this->height_reg2, "Duration Index", "TBR", 0, 'L');
			$this->Cell(10, $this->height_reg2, "", "TR", 1, 'L');

    		while($result_header = $rs_assessHead->Fetchrow()) {
    			$group = $result_header['group'];
    			if($group!='SF') {
					$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize1);
					$this->Cell(10, $this->height_reg2, "", "L", 0, 'L');
					$this->Cell(100, $this->height_reg2, $c.'. '.strtoupper($result_header['desc']), "LTBR", 0, 'L');
					$this->Cell(77, $this->height_reg2, "", "LTBR", 0, 'L');
					$this->Cell(10, $this->height_reg2, "", "R", 1, 'L');
					$c++;
							}

    			$rs_assessDetails = $objSS->getAssessDetails($result_header['id']);
            	if(is_object($rs_assessDetails)){
               		while($result_details= $rs_assessDetails->Fetchrow()){
               			if ($group!='SF') {
               				$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize1);
               				$data = $objSS->getPatientSocialProblem($result_details['id'],$_GET['encounter_nr'],$_GET['pid']);
               				if(strlen($result_details['desc'])<50){
								if(strtoupper($result_details['desc'])!='OTHER'){
									$this->Cell(10, $this->height_reg2, "", "L", 0, 'L');
									$this->Cell(100, $this->height_reg2," - ".$result_details['desc'], "LTBR", 0, 'L');
								} else {
									$this->Cell(10, $this->height_reg2, "", "L", 0, 'L');
									$this->Cell(100, $this->height_reg2," - ".$result_details['desc']." ( ".$data['others']." )", "LTBR", 0, 'L');
		}
								$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize1);
								$this->Cell(38.5, $this->height_reg2, $data['severity_index'], "LBR", 0, 'L');
								$this->Cell(38.5, $this->height_reg2, $data['duration_index'], "BR", 0, 'L');
								$this->Cell(10, $this->height_reg2, "", "R", 1, 'L');
							}else{
								$emp2=$result_details['desc'];
								$emp2=str_split($emp2,50);
								array($emp2[0],$emp2[1]);
								$this->Cell(10, $this->height_reg2, "", "L", 0, 'L');
								$this->Cell(100, $this->height_reg2," - ".$emp2[0], "LTR", 0, 'L');
								$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize1);
								$this->Cell(38.5, $this->height_reg2, $data['severity_index'], "LR", 0, 'L');
								$this->Cell(38.5, $this->height_reg2, $data['duration_index'], "R", 0, 'L');
								$this->Cell(10, $this->height_reg2, "", "R", 1, 'L');
								$this->Cell(10, $this->height_reg2, "", "L", 0, 'L');
								$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize1);
								$this->Cell(100, $this->height_reg2,"  ".$emp2[1], "LR", 0, 'L');
								$this->Cell(38.5, $this->height_reg2, "", "LBR", 0, 'L');
								$this->Cell(38.5, $this->height_reg2, "", "BR", 0, 'L');
								$this->Cell(10, $this->height_reg2, "", "R", 1, 'L');
		}
		}
		}
		}

		}




		$rs_findings= $objSS->getSocialFindings($_GET['pid'],$_GET['encounter_nr']);
		if(is_object($rs_findings)){
		    while($result_f = $rs_findings->Fetchrow()){
				$sdiagnosis = $result_f['social_diagnosis'];
		        $intervention = $result_f['intervention'];
		}
		}

  		if ($this->name_first AND $this->name_last AND $this->informant != ""){
  			$conforme = $this->name_first." ".$this->name_last." / ".$this->informant;	
  		}elseif ($this->name_first AND $this->name_last == ""){
  			$conforme = $this->informant;
  		}elseif ($this->informant == ""){
  			$conforme = $this->name_first." ".$this->name_last;
		}

		$this->SetFont($this->fontfamily1, $this->fontstylebold, $this->fontsize1);
		$this->Cell(197, $this->height_reg, "", "LR",1,'L');
  		$this->Cell(98.5, $this->height_reg, "ASSESSMENT FINDINGS", "TLR",0,'C');
		$this->Cell(98.5, $this->height_reg, "RECOMMENDED INTERVENTION", "TLR",1,'C');
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize1);
		$this->SetWidths(array(98.5,98.5));
		$this->Row(array($sdiagnosis,$intervention));

		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize1);
		$this->Cell(120, $this->height_reg2, "", "L", 0, 'L');
		$this->Cell(77, $this->height_reg2, "Interviewer:", "R", 1, 'L');
		$this->Cell(148, $this->height_reg2, "", "L", 0, 'C');
		$this->Cell(15, $this->height_reg2, $this->social_worker, "", 0, 'C'); //MSW output
		$this->Cell(34, $this->height_reg2, "", "R", 1, '');
		$this->Cell(137, $this->height_reg2, "", "L", 0, 'L');
		$this->Cell(40, $this->height_reg2, "Medical Social Worker", "T", 0, 'C');
		$this->Cell(20, $this->height_reg2, "", "R", 1, 'L');
		$this->SetFont($this->fontfamily1, $this->fontstylenormal, $this->fontsize1);
		$this->Cell(1, $this->height_reg2, "           Conforme: ", "L", 0, 'L');
		$this->Cell(28, $this->height_reg2, "", 0, 0, 'C');
		$this->Cell(22, $this->height_reg2, strtoupper($conforme) , "", 0, 'L'); //conforme output
		$this->Cell(146, $this->height_reg2, "", "R", 1, 'L');
		$this->Cell(24, $this->height_reg2, "", "L", 0, 'L');
		$this->Cell(50, $this->height_reg2, "Patient/Informant and Signature", "T", 0, 'C');
		$this->Cell(123, $this->height_reg2, "", "R", 1, 'L');

		$this->Cell(35, $this->height_reg2, "           Relation to Patient:  " . $this->relation_informant, "L", 0, 'L'); //RTP output
		$this->Cell(40, $this->height_reg2, "", "B", 0, 'C');
		$this->Cell(122, $this->height_reg2, "", "R", 1, 'L');

		$this->Cell(22, $this->height_reg2, "           Address:  ".$this->permanent_address , "L", 0, 'L'); //address output 
		$this->Cell(100, $this->height_reg2, "", "B", 0, 'C');
		$this->Cell(75, $this->height_reg2, "", "R", 1, 'L');

		$this->Cell(26, $this->height_reg2, "           Contact No:  " . $this->contact_no, "L", 0, 'L'); //contact output
		$this->Cell(20, $this->height_reg2, "", "B", 0, 'C');
		$this->Cell(151, $this->height_reg2, "", "R", 1, 'L');

		$this->Cell(197, $this->height_reg2, "", "LR", 1, 'L');
		$this->Cell(197, $this->height_reg2, "", "LRB", 1, 'L');
}
		}

}

$pdf = new MSWD_Form3($_GET['pid'], $_GET['encounter_nr']);
$pdf->Open();
$pdf->AddPage();
$pdf->FirstPageHeader();
$pdf->Info1();
$pdf->DemographicData();
$pdf->FamilyComposition();
$pdf->MonthlyExpenses();
$pdf->AddPage();
$pdf->MedicalData();
$pdf->Functioning();
$pdf->AddPage();
$pdf->ProblemEnvironment();
$pdf->Output();
?>
