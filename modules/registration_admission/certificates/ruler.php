<?php
/*		
		$this->sql ="SELECT cp.pid, enc.encounter_nr, 
							cp.name_last, cp.name_first, cp.name_2, cp.name_3, cp.name_middle,
							enc.encounter_date AS er_opd_datetime, 
							dept.name_formal,
							cp.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,
							cp.phone_1_nr, cp.phone_2_nr, cp.cellphone_1_nr, cp.cellphone_2_nr, cp.sex, cp.civil_status,
							fn_get_age(enc.encounter_date,cp.date_birth) AS age,
							cp.date_birth, cp.place_birth,
							sc.country_name AS citizenship, 
							sreli.religion_name AS religion, 
							so.occupation_name AS occupation, 
							cp.mother_name, cp.father_name, cp.spouse_name, cp.guardian_name,							
							enc.informant_name, enc.info_address, enc.relation_informant, 
							enc.encounter_type, 
							enc.referrer_dr AS opd_admitting_physician,
							enc.current_dept_nr,							
							enc.consulting_dr AS admitting_physician,
							enc.modify_id AS admitting_clerk,
							enc.create_id AS admitting_clerk_er_opd,
							enc.referrer_diagnosis AS admitting_diagnosis
						FROM $this->tb_person AS cp, $this->tb_enc AS enc, 
							$this->tb_dept AS dept,
							$this->tb_barangays AS sb, $this->tb_municity AS sm, 
							$this->tb_provinces AS sp, $this->tb_regions AS sr, 
							$this->tb_country AS sc, $this->tb_religion AS sreli, $this->tb_occupation AS so
						WHERE enc.encounter_nr='$encounter_nr'
							AND cp.pid=enc.pid AND dept.nr=enc.current_dept_nr
							AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr 
							AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr
							AND sc.country_code=cp.citizenship AND sreli.religion_nr = cp.religion 
							AND so.occupation_nr = cp.occupation " ;
*/							
							
	include("roots.php");
	include_once($root_path."/classes/fpdf/fpdf.php");
	require_once($root_path.'include/inc_environment_global.php');
	include_once($root_path.'include/inc_date_format_functions.php');

	require_once($root_path.'/include/care_api_classes/class_drg.php');
	$objDRG= new DRG;

	include_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;

/*	if ($_GET['encounter_nr']) {
		if (!($enc_info = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
			echo '<em class="warn">Sorry but the page cannot be displayed!</em>';
			exit();
		}
		#echo "enc_obj->sql = '".$enc_obj->sql."' <br> \n";		
		extract($enc_info);
	}else{
			echo '<em class="warn">Sorry but the page cannot be displayed! <br>Invalid Case Number! </em>';
			exit();	
	}
	*/
	$border="1";
	$border2="0";
	$space=2;
	$fontSizeLabel=9;
	$fontSizeInput=14;
	$fontSizeHeading=15;

	//Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, int fill [, mixed link]]]]]]])
	$pdf = new FPDF();
	$pdf->AddPage("P");
	
	$pdf->SetLeftMargin(0);


	$pdf->Ln(14);
	$pdf->SetFont("Arial","",$fontSizeLabel-2);
    $pdf->Cell(158,4,'',"",'L');	
	
	$pdf->SetFont("Arial","B",$fontSizeLabel-2);
	$pdf->Cell(40,34,'',"",'L');	
	
	
	
	$pdf->SetFont("Arial","",$fontSizeLabel-5);
	
	$pdf->Text(0,0, "|_");
	$pdf->Text(0,5, "_5");
	$pdf->Text(0,10,"_10");
	$pdf->Text(0,15,"_15");
	$pdf->Text(0,20,"_20");
	$pdf->Text(0,25,"_25");
	$pdf->Text(0,30,"_30");
	$pdf->Text(0,35,"_35");
	$pdf->Text(0,40,"_40");
	$pdf->Text(0,45,"_45");
	$pdf->Text(0,50,"_50");
	$pdf->Text(0,55,"_55");
	$pdf->Text(0,60,"_60");
	$pdf->Text(0,65,"_65");
	$pdf->Text(0,70,"_70");
	$pdf->Text(0,75,"_75");
	$pdf->Text(0,80,"_80");
	$pdf->Text(0,85,"_85");
	$pdf->Text(0,90,"_90");
	$pdf->Text(0,95,"_95");
	$pdf->Text(0,100,"_100");
	$pdf->Text(0,105,"_105");
	$pdf->Text(0,110,"_110");
	$pdf->Text(0,115,"_115");
	$pdf->Text(0,120,"_120");
	$pdf->Text(0,125,"_125");
	$pdf->Text(0,130,"_130");
	$pdf->Text(0,135,"_135");
	$pdf->Text(0,140,"_140");
	$pdf->Text(0,145,"_145");
	$pdf->Text(0,150,"_150");
	$pdf->Text(0,155,"_155");
	$pdf->Text(0,160,"_160");
	$pdf->Text(0,165,"_165");
	$pdf->Text(0,170,"_170");
	$pdf->Text(0,175,"_175");
	$pdf->Text(0,180,"_180");
	$pdf->Text(0,185,"_185");
	$pdf->Text(0,190,"_190");
	$pdf->Text(0,195,"_195");
	$pdf->Text(0,200,"_200");

$pdf->Text(0,205, "_205");
	$pdf->Text(0,210,"_210");
	$pdf->Text(0,215,"_215");
	$pdf->Text(0,220,"_220");
	$pdf->Text(0,225,"_225");
	$pdf->Text(0,230,"_230");
	$pdf->Text(0,235,"_235");
	$pdf->Text(0,240,"_240");
	$pdf->Text(0,245,"_245");
	$pdf->Text(0,250,"_250");
	$pdf->Text(0,255,"_255");
	$pdf->Text(0,260,"_260");
	$pdf->Text(0,265,"_265");
	$pdf->Text(0,270,"_270");
	$pdf->Text(0,275,"_275");
	$pdf->Text(0,280,"_280");
	$pdf->Text(0,285,"_285");
	$pdf->Text(0,290,"_290");
	$pdf->Text(0,295,"_295");
	$pdf->Text(0,300,"_300");
	$pdf->Text(0,305,"_305");
	$pdf->Text(0,310,"_310");
	$pdf->Text(0,315,"_315");
	$pdf->Text(0,320,"_320");
	$pdf->Text(0,325,"_325");
	$pdf->Text(0,330,"_330");
	$pdf->Text(0,335,"_335");
	$pdf->Text(0,340,"_340");
	$pdf->Text(0,345,"_345");
	$pdf->Text(0,350,"_350");
	$pdf->Text(0,355,"_355");
	$pdf->Text(0,360,"_360");
	$pdf->Text(0,365,"_365");
	$pdf->Text(0,370,"_370");
	$pdf->Text(0,375,"_375");
	$pdf->Text(0,380,"_380");
	$pdf->Text(0,385,"_385");
	$pdf->Text(0,390,"_390");
	$pdf->Text(0,395,"_395");
	$pdf->Text(0,400,"_400");





		
	
	$pdf->Text(5,1, "|");
	$pdf->Text(10,1,"|");
	$pdf->Text(15,1,"|");
	$pdf->Text(20,1,"|");
	$pdf->Text(25,1,"|");
	$pdf->Text(30,1,"|");
	$pdf->Text(35,1,"|");
	$pdf->Text(40,1,"|");
	$pdf->Text(45,1, "|");
	$pdf->Text(50,1,"|");
	$pdf->Text(55,1,"|");
	$pdf->Text(60,1,"|");
	$pdf->Text(65,1,"|");
	$pdf->Text(70,1,"|");
	$pdf->Text(75,1,"|");
	$pdf->Text(80,1,"|");
	$pdf->Text(85,1, "|");
	$pdf->Text(90,1,"|");
	$pdf->Text(95,1,"|");
	$pdf->Text(100,1,"|");
	
	$pdf->Text(105,1, "|");
	$pdf->Text(110,1,"|");
	$pdf->Text(115,1,"|");
	$pdf->Text(120,1,"|");
	$pdf->Text(125,1,"|");
	$pdf->Text(130,1,"|");
	$pdf->Text(135,1,"|");
	$pdf->Text(140,1,"|");
	$pdf->Text(145,1, "|");
	$pdf->Text(150,1,"|");
	$pdf->Text(155,1,"|");
	$pdf->Text(160,1,"|");
	$pdf->Text(165,1,"|");
	$pdf->Text(170,1,"|");
	$pdf->Text(175,1,"|");
	$pdf->Text(180,1,"|");
	$pdf->Text(185,1, "|");
	$pdf->Text(190,1,"|");
	$pdf->Text(195,1,"|");
	$pdf->Text(200,1,"|");

	$pdf->Text(5,4,"5");
	$pdf->Text(10,4,"10");
	$pdf->Text(15,4,"15");
	$pdf->Text(20,4,"20");
	$pdf->Text(25,4,"25");
	$pdf->Text(30,4,"30");
	$pdf->Text(35,4,"35");
	$pdf->Text(40,4,"40");
	$pdf->Text(45,4, "45");
	$pdf->Text(50,4,"50");
	$pdf->Text(55,4,"55");
	$pdf->Text(60,4,"60");
	$pdf->Text(65,4,"65");
	$pdf->Text(70,4,"70");
	$pdf->Text(75,4,"75");
	$pdf->Text(80,4,"80");
	$pdf->Text(85,4, "85");
	$pdf->Text(90,4,"90");
	$pdf->Text(95,4,"95");
	$pdf->Text(100,4,"100");
	
	$pdf->Text(105,4,"105");
	$pdf->Text(110,4,"110");
	$pdf->Text(115,4,"115");
	$pdf->Text(120,4,"120");
	$pdf->Text(125,4,"125");
	$pdf->Text(130,4,"130");
	$pdf->Text(135,4,"135");
	$pdf->Text(140,4,"140");
	$pdf->Text(145,4, "145");
	$pdf->Text(150,4,"150");
	$pdf->Text(155,4,"155");
	$pdf->Text(160,4,"160");
	$pdf->Text(165,4,"165");
	$pdf->Text(170,4,"170");
	$pdf->Text(175,4,"175");
	$pdf->Text(180,4,"180");
	$pdf->Text(185,4,"185");
	$pdf->Text(190,4,"190");
	$pdf->Text(195,4,"195");
	$pdf->Text(200,4,"200");




	
	$pdf->Output();	
	
	
?>