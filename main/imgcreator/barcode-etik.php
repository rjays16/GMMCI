<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/*
CARE2X Integrated Information System beta 2.0.1 - 2003-10-13 for Hospitals and Health Care Organizations and Services
Copyright (C) 2002,2003,2004,2005  Elpidio Latorilla & Intellin.org	

GNU GPL. For details read file "copy_notice.txt".
*/

# Define to true if you want to draw a border around the labels
define('DRAW_BORDER',TRUE);

if(!extension_loaded('gd')) dl('php_gd.dll');

define('LANG_FILE','aufnahme.php');
define('NO_CHAIN',1);
require_once($root_path.'include/inc_front_chain_lang.php');
header ('Content-type: image/png');

	
# Check if ttf is ok
require_once($root_path.'include/inc_ttf_check.php');

# Check the encounter number
if((!isset($en)||!$en)&&$HTTP_SESSION_VARS['sess_en']) $en=$HTTP_SESSION_VARS['sess_en'];

/*
if(file_exists("../cache/barcodes/pn_".$pn."_bclabel_".$lang.".png"))
{
    $im = ImageCreateFrompng("../cache/barcodes/pn_".$pn."_bclabel_".$lang.".png");
    Imagepng($im);
}
else
{
*/
    if(!isset($db) || !$db) include_once($root_path.'include/inc_db_makelink.php');
    if($dblink_ok) {
	    // get orig data
	    //$dbtable='care_patient_encounter';
/*			# burn commented : May 9, 2007
		$sql="SELECT c1.name_last, c1.name_first, c1.date_birth, c1.sex, c1.civil_status, c1.phone_1_nr,
		          c1.religion, c1.addr_str, c1.addr_str_nr, c1.addr_zip, c1.addr_citytown_nr, c1.contact_person, c1.blood_group,  
				  c2.*, ad.name
				 FROM care_encounter as c2 
				     LEFT JOIN care_person as c1 ON c1.pid=c2.pid 
					 LEFT JOIN care_address_citytown AS ad ON c1.addr_citytown_nr=ad.nr
				         WHERE c2.encounter_nr='$en'";
*/
			#burn added : May 9, 2007
		$sql="SELECT cp.pid, enc.encounter_nr, 
					cp.name_last, cp.name_first, cp.name_2, cp.name_3, cp.name_middle,
					enc.encounter_date AS er_opd_datetime, dept.name_formal, 
					cp.street_name, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name,
					cp.phone_1_nr, cp.phone_2_nr, cp.cellphone_1_nr, cp.cellphone_2_nr, 
					cp.sex, cp.civil_status, cp.blood_group, 
					IF(fn_calculate_age(enc.encounter_date,cp.date_birth),fn_get_age(enc.encounter_date,cp.date_birth),'') AS age, 
					IF(fn_calculate_age(enc.encounter_date,date_birth),date_birth,'') AS date_birth, 
					cp.place_birth, 
					sc.country_name AS citizenship, sreli.religion_name AS religion, so.occupation_name AS occupation, 
					cp.mother_fname, cp.mother_maidenname,cp.mother_mname,cp.mother_lname,
					cp.father_fname,cp.father_mname,cp.father_lname,
					cp.spouse_name, cp.guardian_name, 
					enc.informant_name, enc.info_address, enc.relation_informant, 
					enc.encounter_type, enc.encounter_class_nr, 
					enc.referrer_dept, 
					enc.referrer_dr AS er_opd_admitting_physician_nr, 
					( SELECT CONCAT(cp_2.title,' ',cp_2.name_first,' ', 
						IF(TRIM(cp_2.name_middle)<>'',CONCAT(LEFT(cp_2.name_middle,1),'. '),''), cp_2.name_last) AS fullname 
						FROM care_encounter AS enc_2, care_personell AS cpl_2, care_person AS cp_2 
						WHERE enc_2.encounter_nr='2007000008' AND cpl_2.nr = enc_2.referrer_dr 
								AND cp_2.pid=cpl_2.pid ) AS er_opd_admitting_physician_name, 
					enc.current_dept_nr, 
					enc.current_att_dr_nr AS attending_physician_nr, 
					( SELECT CONCAT(cp_2.title,' ',cp_2.name_first,' ', 
						IF(TRIM(cp_2.name_middle)<>'',CONCAT(LEFT(cp_2.name_middle,1),'. '),''), cp_2.name_last) AS fullname 
						FROM care_encounter AS enc_2, care_personell AS cpl_2, care_person AS cp_2 
						WHERE enc_2.encounter_nr='2007000008' AND cpl_2.nr = enc_2.current_att_dr_nr 
							AND cp_2.pid=cpl_2.pid ) AS attending_physician_name, 
					enc.modify_id AS admitting_clerk, 
					enc.create_id AS admitting_clerk_er_opd, 
					enc.referrer_diagnosis AS admitting_diagnosis, 
					enc.admission_dt, 
					enc.is_discharged, 
					CONCAT(enc.discharge_date,' ',enc.discharge_time) AS discharge_dt,
					enc.create_time 
				FROM care_encounter AS enc, care_department AS dept, seg_barangays AS sb, 
					seg_municity AS sm, seg_provinces AS sp, seg_regions AS sr, seg_country AS sc, 
					care_person AS cp 
						LEFT JOIN seg_religion AS sreli ON sreli.religion_nr = cp.religion 
						LEFT JOIN seg_occupation AS so ON so.occupation_nr = cp.occupation 
				WHERE enc.encounter_nr='$en' AND cp.pid=enc.pid 
					AND dept.nr=enc.current_dept_nr AND sr.region_nr=sp.region_nr 
					AND sp.prov_nr=sm.prov_nr AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr 
					AND sc.country_code=cp.citizenship ";
#echo "sql ".$sql;
	    if($ergebnis=$db->Execute($sql))
       	{
			if($ergebnis->RecordCount())
				{
					$result=$ergebnis->FetchRow();
				}
		}
		// else {print "<p>$sql$LDDbNoRead"; exit;} /* Remove comment for debugging*/
       
	   include_once($root_path.'include/inc_date_format_functions.php');
       //$date_format=getDateFormat($link,$DBLink_OK);

	   	/* Get the patient global configs */
		include_once($root_path.'include/care_api_classes/class_globalconfig.php');
        $glob_obj=new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('patient_%');
		
		# Create insurance object
		include_once($root_path.'include/care_api_classes/class_insurance.php');
		$ins_obj=new Insurance;
		
		include_once($root_path.'include/care_api_classes/class_ward.php');
		$obj=new Ward;
		# Get location data
		$location=&$obj->EncounterLocationsInfo($en);
		
	   
	//   $result['date_birth']=formatDate2Local($result['date_birth'],$date_format);
	}
	else 
		{ print "$LDDbNoLink<br>$sql<br>"; }
		
		
		
	switch ($result['encounter_class_nr'])
	{
	    case '1':    $full_en= $en + $GLOBAL_CONFIG['patient_inpatient_nr_adder'];
		                      $result['encounter_class']=$LDStationary;
		                      break;
	    case '2':   $full_en= $en + $GLOBAL_CONFIG['patient_outpatient_nr_adder'];
		                      $result['encounter_class']=$LDAmbulant;
	    default:    $full_en= $en + $GLOBAL_CONFIG['patient_inpatient_nr_adder'];
		                      $result['encounter_class']=$LDStationary;
		}
			
    if($lang=='tr') $result['sex']=strtr($result['sex'],'mfMF','ekEK');
    if($lang=='de') $result['sex']=strtr($result['sex'],'mfMF','mwMW');
	
	# Load the image generation script based on the language
	if($lang=='tr') include($root_path.'main/imgcreator/inc_etik_tr.php');
	if($lang=='ar'||$lang=='fa') include($root_path.'main/imgcreator/inc_etik_ar.php');
		else include($root_path.'main/imgcreator/inc_etik.php');
?>
