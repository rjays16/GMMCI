<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System beta 2.0.1 - 2004-07-04
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
      
# Default value for the maximum nr of rows per block displayed, define this to the value you wish
# In normal cases this value is derived from the db table "care_config_global" using the "pagin_insurance_list_max_block_rows" element.
define('MAX_BLOCK_ROWS',30); 
define('SHOW_SEARCH_QUERY',1); # Set to 1 if you want to display the query conditions, 0 to hide

define('LANG_FILE','aufnahme.php');
$local_user='aufnahme_user';
require($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/inc_date_format_functions.php');

$thisfile=basename(__FILE__);


global $ptype, $allow_patient_register, $allow_newborn_register, $allow_er_user, $allow_opd_user, $allow_ipd_user, $allow_medocs_user, $allow_update;

if ($HTTP_POST_VARS['ptype'])
	$ptype=$HTTP_POST_VARS['ptype'];

#echo "advance ptype = ".$ptype;

/*---replaced, 2007-10-03 FDP
$breakfile='patient.php'.URL_APPEND;
-----*/
#$breakfile=$root_path.'main/startframe.php'.URL_APPEND;
#edited by VAN 04-16-08
$breakfile=$root_path.'modules/registration_admission/patient_register_archive.php'.URL_APPEND.'&ptype='.$ptype;

$newdata=1;

$dbtable='care_person';

$target='archiv';

$error=0;

if ($_GET['ptype'])
	$ptype = $_GET['ptype'];
elseif ($HTTP_SESSION_VARS['ptype'])
	$ptype = $HTTP_SESSION_VARS['ptype'];
	
$HTTP_SESSION_VARS['ptype'] = $ptype;	
	#echo "<br> advance 2 ptype = ".$ptype;	
	
# Initialize page�s control variables
if($mode=='paginate'){
	$searchkey=$HTTP_SESSION_VARS['sess_searchkey'];
	
	if (!empty($HTTP_SESSION_VARS['ptype']))
		$ptype = $HTTP_SESSION_VARS['ptype'];
	
	//$searchkey='USE_SESSION_SEARCHKEY';
	//$mode='search';
}else{
	# Reset paginator variables
	$pgx=0;
	$totalcount=0;
	$odir='';
	$oitem='';
}
require_once($root_path.'include/care_api_classes/class_paginator.php');

$pagen=new Paginator($pgx,$thisfile,$HTTP_SESSION_VARS['sess_searchkey'],$root_path);


require_once($root_path.'include/care_api_classes/class_globalconfig.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$glob_obj->getConfig('person%');

# Get the max nr of rows from global config
$glob_obj->getConfig('pagin_person_search_max_block_rows');
if(empty($GLOBAL_CONFIG['pagin_person_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS); # Last resort, use the default defined at the start of this page
	else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_person_search_max_block_rows']);

	
if (isset($mode) && ($mode=='search'||$mode=='paginate')){
/*
echo "patient_register_archive.php <br>\n HTTP_SESSION_VARS : <br>\n";
print_r($HTTP_SESSION_VARS);
echo "<br>\n";
echo " street_name = '".$street_name."' <br>\n";
echo " brgy_nr = '".$brgy_nr."' <br>\n";
echo " zipcode = '".$zipcode."' <br>\n";
echo " mun_nr = '".$mun_nr."' <br>\n";
echo " prov_nr = '".$prov_nr."' <br>\n";
echo " region_nr = '".$region_nr."' <br>\n";
*/
	//if(empty($oitem)) $oitem='name_last';			
	//if(empty($odir)) $odir='ASC'; # default, ascending alphabetic
	# Set the sort parameters
	$pagen->setSortItem($oitem);
	$pagen->setSortDirection($odir);

	if($mode=='paginate'){
		if(isset($oitem)&&!empty($oitem))	$sql=$HTTP_SESSION_VARS['sess_searchkey']." ORDER BY $oitem $odir";
			else $sql=$HTTP_SESSION_VARS['sess_searchkey'];
		$s2=$sql; # Dummy  to force the sql query to be executed
		
	}else{
	
		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');

#		$sql="SELECT pid, date_reg, name_last, name_first, date_birth, addr_zip, sex, death_date, status FROM $dbtable WHERE ";
		#edited by VAN 02-13-08
		$select_flds=" SELECT DISTINCT ps.nr AS personnelID, senior_ID, cp.pid, date_reg, name_last, name_first, date_birth, addr_zip, sex, death_date, cp.status ".
						" , sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name ";
		
		#$from_tb=" FROM $dbtable AS cp, seg_barangays AS sb, seg_municity AS sm, seg_provinces AS sp, seg_regions AS sr WHERE ";
		#edited by VAN 02-19-08
		#$from_tb=" FROM $dbtable AS cp, seg_barangays AS sb, seg_municity AS sm, seg_provinces AS sp, seg_regions AS sr,
		#           care_type_ethnic_orig AS et WHERE ";
		
		$from_tb=" FROM $dbtable AS cp
  					  LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=cp.brgy_nr
					  LEFT JOIN seg_municity AS sm ON sm.mun_nr=cp.mun_nr
					  LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
					  LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
					  LEFT JOIN care_type_ethnic_orig AS et ON cp.ethnic_orig=et.nr 
					  LEFT JOIN seg_occupation AS j ON cp.occupation=j.occupation_nr
					  LEFT JOIN seg_religion AS rl ON cp.religion=rl.religion_nr
					  LEFT JOIN seg_country AS cz ON cp.citizenship=cz.country_code
					  LEFT JOIN care_personell AS ps ON cp.pid=ps.pid 
					            AND date_exit NOT IN ('0000-00-00', DATE(NOW()))";
		
		$s2='';
							
							if(isset($pid)&&$pid)
							{
						         if($pid < $GLOBAL_CONFIG['person_id_nr_adder'])
								 {
								 		#$s2.=" pid $sql_LIKE '%$pid'";
										$s2.=" WHERE cp.pid = '$pid'";
								 }
								 else
								 {
								       #$s2.=" pid = ".$pid;
										 $s2.=" WHERE cp.pid = '".$pid."'";
								}
							}
						
							
							if(isset($name_last)&&$name_last)
							{
							     #if($s2) $s2.=" AND name_last $sql_LIKE '$name_last%'"; else $s2.=" name_last $sql_LIKE '$name_last%'";
								  if($s2) $s2.=" AND name_last $sql_LIKE '$name_last%'"; else $s2.=" WHERE name_last $sql_LIKE '$name_last%'";
							}
							
							if(!isset($date_start)) $date_start='';
							if(!isset($date_end)) $date_end='';
							
							if($date_start){
								    $date_start=@formatDate2STD($date_start,$date_format);
  								}
							if($date_end){
								    $date_end=@formatDate2STD($date_end,$date_format);
							   }

							$buffer='';
							if(($date_start)&&($date_end))
								{
									#$buffer=" date_reg >= '$date_start 00:00:00' AND date_reg <= '$date_end 23:59:59'";
									$buffer=" WHERE date_reg >= '$date_start 00:00:00' AND date_reg <= '$date_end 23:59:59'";
								}
								elseif($date_start)
								{
									#$buffer=" date_reg $sql_LIKE '$date_start%'";
									$buffer=" WHERE date_reg $sql_LIKE '$date_start%'";
									
									//if($s2) $s2.=" AND date_reg $sql_LIKE '$date_start %'"; else $s2.=" date_reg $sql_LIKE '$date_start %'";
								}
								elseif($date_end)
								{
									#$buffer=" (date_reg <= '$date_end')";
									$buffer=" WHERE (date_reg <= '$date_end')";
									
									//if($s2) $s2.=" AND (date_reg $sql_LIKE '$date_end %' OR date_reg $sql_LIKE '$date_end %')"; else $s2.=" date_reg $sql_LIKE '$date_end %'";
								}
								if($buffer){
									if($s2) $s2.=" AND $buffer";
										else $s2=$buffer;
								}
									
							if(isset($user_id)&&$user_id)
								#if($s2) $s2.=" AND modify_id $sql_LIKE '$user_id%'"; else $s2.=" modify_id $sql_LIKE '$user_id%'";
								if($s2) $s2.=" AND modify_id $sql_LIKE '$user_id%'"; else $s2.=" WHERE modify_id $sql_LIKE '$user_id%'";
								
								
							if(isset($name_first)&&$name_first)
								#if($s2) $s2.=" AND name_first $sql_LIKE '%$name_first%'"; else $s2.=" name_first $sql_LIKE '%$name_first%'";
								if($s2) $s2.=" AND name_first $sql_LIKE '$name_first%'"; else $s2.=" WHERE name_first $sql_LIKE '$name_first%'";
							if(isset($name_2)&&$name_2)
								#if($s2) $s2.=" AND name_2 $sql_LIKE '$name_2%'"; else $s2.=" name_2 $sql_LIKE '$name_2%'";
								if($s2) $s2.=" AND name_2 $sql_LIKE '$name_2%'"; else $s2.=" WHERE name_2 $sql_LIKE '$name_2%'";
								
							if(isset($name_3)&&$name_3)
								#if($s2) $s2.=" AND name_3 $sql_LIKE '$name_3%'"; else $s2.=" name_3 $sql_LIKE '$name_3%'";
								if($s2) $s2.=" AND name_3 $sql_LIKE '$name_3%'"; else $s2.=" WHERE name_3 $sql_LIKE '$name_3%'";
							if(isset($name_middle)&&$name_middle)
								#if($s2) $s2.=" AND name_middle $sql_LIKE '$name_middle%'"; else $s2.=" name_middle $sql_LIKE '$name_middle%'";
								if($s2) $s2.=" AND name_middle $sql_LIKE '$name_middle%'"; else $s2.=" WHERE name_middle $sql_LIKE '$name_middle%'";
							if(isset($name_maiden)&&$name_maiden)
								#if($s2) $s2.=" AND name_maiden $sql_LIKE '$name_maiden%'"; else $s2.=" name_maiden $sql_LIKE '$name_maiden%'";
								if($s2) $s2.=" AND name_maiden $sql_LIKE '$name_maiden%'"; else $s2.=" WHERE name_maiden $sql_LIKE '$name_maiden%'";
							if(isset($name_others)&&$name_others)
								#if($s2) $s2.=" AND name_others $sql_LIKE '$name_others%'"; else $s2.=" name_others $sql_LIKE '$name_others%'";
								if($s2) $s2.=" AND name_others $sql_LIKE '$name_others%'"; else $s2.=" WHERE name_others $sql_LIKE '$name_others%'";

							if(isset($date_birth)&&$date_birth)
							  {
							    $date_birth=@formatDate2STD($date_birth,$date_format);
								
								#if($s2) $s2.=" AND date_birth='$date_birth'"; else $s2.=" date_birth='$date_birth'";
								if($s2) $s2.=" AND date_birth='$date_birth'"; else $s2.=" WHERE date_birth='$date_birth'";
							  }
							if(isset($place_birth) && $place_birth)
							  {							
								#if($s2) $s2.=" AND place_birth='$place_birth'"; else $s2.=" place_birth='$place_birth'";
								#edited by VAN 04-16-08
								#if($s2) $s2.=" AND place_birth='$place_birth%'"; else $s2.=" WHERE place_birth='$place_birth%'";
								if($s2) $s2.=" AND place_birth LIKE '%$place_birth%'"; else $s2.=" WHERE place_birth LIKE '%$place_birth%'";
							  }							  
							if(isset($street_name) && $street_name)
							  {							
								#if($s2) $s2.=" AND street_name='$street_name'"; else $s2.=" street_name='$street_name'";
								#if($s2) $s2.=" AND street_name='$street_name'"; else $s2.=" WHERE street_name='$street_name'";
								if($s2) $s2.=" AND street_name LIKE '%$street_name%'"; else $s2.=" WHERE street_name LIKE '%$street_name%'";
							  }

							if(isset($addr_str)&&$addr_str)
								#if($s2) $s2.=" AND addr_str $sql_LIKE '%$addr_str%'"; else $s2.=" addr_str $sql_LIKE '%$addr_str%'";
								if($s2) $s2.=" AND addr_str $sql_LIKE '%$addr_str%'"; else $s2.=" WHERE addr_str $sql_LIKE '%$addr_str%'";

							if(isset($addr_str_nr)&&$addr_str_nr)
								#if($s2) $s2.=" AND addr_str_nr $sql_LIKE '%$addr_str_nr%'"; else $s2.=" addr_str_nr $sql_LIKE '%$addr_str_nr%'";
								if($s2) $s2.=" AND addr_str_nr $sql_LIKE '%$addr_str_nr%'"; else $s2.=" WHERE addr_str_nr $sql_LIKE '%$addr_str_nr%'";
							if(isset($addr_citytown_nr)&&$addr_citytown_nr)
								#if($s2) $s2.=" AND addr_citytown_nr $sql_LIKE '$addr_citytown_nr'"; else $s2.=" addr_citytown_nr $sql_LIKE '$addr_citytown_nr'";
								if($s2) $s2.=" AND addr_citytown_nr $sql_LIKE '$addr_citytown_nr'"; else $s2.=" WHERE addr_citytown_nr $sql_LIKE '$addr_citytown_nr'";
							if(isset($addr_zip)&&$addr_zip)
								#if($s2) $s2.=" AND addr_zip $sql_LIKE '%$addr_zip%'"; else $s2.=" addr_zip $sql_LIKE '%$addr_zip%'";
								if($s2) $s2.=" AND addr_zip $sql_LIKE '%$addr_zip%'"; else $s2.=" WHERE addr_zip $sql_LIKE '%$addr_zip%'";
								
							if(isset($sex)&&$sex)
								#if($s2) $s2.=" AND sex = '$sex'"; else $s2.=" sex = '$sex'";
								if($s2) $s2.=" AND sex = '$sex'"; else $s2.=" WHERE sex = '$sex'";
							if(isset($civil_status)&&$civil_status)
								#if($s2) $s2.=" AND civil_status = '$civil_status'"; else $s2.=" civil_status = '$civil_status'";
								if($s2) $s2.=" AND civil_status = '$civil_status'"; else $s2.=" WHERE civil_status = '$civil_status'";
							if(isset($phone_1)&&$phone_1)
								#if($s2) $s2.=" AND phone_1_nr $sql_LIKE '$phone_1%'"; else $s2.=" phone_1_nr $sql_LIKE '$phone_1%'";
								if($s2) $s2.=" AND phone_1_nr $sql_LIKE '$phone_1%'"; else $s2.=" WHERE phone_1_nr $sql_LIKE '$phone_1%'";
							if(isset($phone_2)&&$phone_2)
								#if($s2) $s2.=" AND phone_2_nr $sql_LIKE '$phone_2%'"; else $s2.=" phone_2_nr $sql_LIKE '$phone_2%'";
								if($s2) $s2.=" AND phone_2_nr $sql_LIKE '$phone_2%'"; else $s2.=" WHERE phone_2_nr $sql_LIKE '$phone_2%'";
							if(isset($cellphone_1)&&$cellphone_1)
								#if($s2) $s2.=" AND cellphone_1_nr $sql_LIKE '$cellphone_1%'"; else $s2.=" cellphone_1_nr $sql_LIKE '$cellphone_1%'";
								if($s2) $s2.=" AND cellphone_1_nr $sql_LIKE '$cellphone_1%'"; else $s2.=" WHERE cellphone_1_nr $sql_LIKE '$cellphone_1%'";
							if(isset($cellphone_2)&&$cellphone_2)
								#if($s2) $s2.=" AND cellphone_2_nr $sql_LIKE '$cellphone_2%'"; else $s2.=" cellphone_2_nr $sql_LIKE '$cellphone_2%'";
								if($s2) $s2.=" AND cellphone_2_nr $sql_LIKE '$cellphone_2%'"; else $s2.=" WHERE cellphone_2_nr $sql_LIKE '$cellphone_2%'";
								
							if(isset($fax)&&$fax)
								#if($s2) $s2.=" AND fax $sql_LIKE '$fax%'"; else $s2.=" fax $sql_LIKE '$fax%'";
								if($s2) $s2.=" AND fax $sql_LIKE '$fax%'"; else $s2.=" WHERE fax $sql_LIKE '$fax%'";
							if(isset($email)&&$email)
								#if($s2) $s2.=" AND email $sql_LIKE '%$email%'"; else $s2.=" email $sql_LIKE '%$email%'";
								if($s2) $s2.=" AND email $sql_LIKE '%$email%'"; else $s2.=" WHERE email $sql_LIKE '%$email%'";
							if(isset($sss_nr)&&$sss_nr)
								#if($s2) $s2.=" AND sss_nr $sql_LIKE '$sss_nr%'"; else $s2.=" sss_nr $sql_LIKE '$sss_nr%'";
								if($s2) $s2.=" AND sss_nr $sql_LIKE '$sss_nr%'"; else $s2.=" WHERE sss_nr $sql_LIKE '$sss_nr%'";
							if(isset($nat_id_nr)&&$nat_id_nr)
								#if($s2) $s2.=" AND nat_id_nr $sql_LIKE '$nat_id_nr%'"; else $s2.=" nat_id_nr $sql_LIKE '$nat_id_nr%'";
								if($s2) $s2.=" AND nat_id_nr $sql_LIKE '$nat_id_nr%'"; else $s2.=" WHERE nat_id_nr $sql_LIKE '$nat_id_nr%'";
							if(isset($religion)&&$religion)
								#if($s2) $s2.=" AND religion $sql_LIKE '$religion%'"; else $s2.=" religion $sql_LIKE '$religion%'";
								#if($s2) $s2.=" AND religion $sql_LIKE '$religion%'"; else $s2.=" WHERE religion $sql_LIKE '$religion%'";
								if($s2) $s2.=" AND rl.religion_name $sql_LIKE '%$religion%'"; else $s2.=" WHERE rl.religion_name $sql_LIKE '%$religion%'";
							
							if(isset($occupation) && $occupation)
							  {							
								#if($s2) $s2.=" AND occupation='$occupation' "; else $s2.=" occupation='$occupation'";
								if($s2) $s2.=" AND j.occupation_name LIKE '%$occupation%' "; else $s2.=" WHERE j.occupation_name LIKE '%$occupation%'";
							  }
							  
							 if(isset($citizenship) && $citizenship)
							  {							
								if($s2) $s2.=" AND cz.country_name LIKE '%$occupation%' "; else $s2.=" WHERE cz.country_name LIKE '%$occupation%'";
							  } 

							if(isset($ethnic_orig)&& $ethnic_orig)
								#edited by VAN 02-13-08
								#if($s2) $s2.=" AND ethnic_orig $sql_LIKE '$ethnic_orig%'"; else $s2.=" ethnic_orig $sql_LIKE '$ethnic_orig%'";
								#if($s2) $s2.=" AND et.name $sql_LIKE '$ethnic_orig%'"; else $s2.=" et.name $sql_LIKE '$ethnic_orig%'";
								if($s2) $s2.=" AND et.name $sql_LIKE '%$ethnic_orig%'"; else $s2.=" WHERE et.name $sql_LIKE '%$ethnic_orig%'";
							/*	
							if(isset($mother_name) && $mother_name)
								#if($s2) $s2.=" AND mother_name $sql_LIKE '$mother_name%'"; else $s2.=" mother_name $sql_LIKE '$mother_name%'";
								if($s2) $s2.=" AND mother_name $sql_LIKE '$mother_name%'"; else $s2.=" WHERE mother_name $sql_LIKE '$mother_name%'";
							*/
							/*
							if((isset($mother_fname) && $mother_fname)||(isset($mother_maidenname) && $mother_maidenname)||(isset($mother_mname) && $mother_mname)||(isset($mother_lname) && $mother_lname))
								if($s2) $s2.=" AND (mother_fname $sql_LIKE '%$mother_fname%' || mother_maidenname $sql_LIKE '%$mother_maidenname%' 
								                    || mother_mname $sql_LIKE '%$mother_mname%' || mother_lname $sql_LIKE '%$mother_lname%') "; 
								else $s2.=" WHERE (mother_fname $sql_LIKE '%$mother_fname%' || mother_maidenname $sql_LIKE '%$mother_maidenname%' 
								                    || mother_mname $sql_LIKE '%$mother_mname%' || mother_lname $sql_LIKE '%$mother_lname%') ";
							
							if((isset($father_fname) && $father_fname)||(isset($father_mname) && $father_mname)||(isset($father_lname) && $father_lname))
								if($s2) $s2.=" AND (father_fname $sql_LIKE '$father_fname%' || father_mname $sql_LIKE '$father_mname%'
								                    || father_lname $sql_LIKE '$father_lname%' ) "; 
								else $s2.=" WHERE (father_fname $sql_LIKE '$father_fname%' || father_mname $sql_LIKE '$father_mname%'
								                    || father_lname $sql_LIKE '$father_lname%' ) ";
							*/
							/*
							if(isset($father_name) && $father_name)
								if($s2) $s2.=" AND father_name $sql_LIKE '$father_name%'"; else $s2.=" WHERE father_name $sql_LIKE '$father_name%'";
							*/
							
							#mother
							if(isset($mother_fname) && $mother_fname)
								if($s2) $s2.=" AND mother_fname $sql_LIKE '$mother_fname%'"; else $s2.=" WHERE mother_fname $sql_LIKE '$mother_fname%'";
							if(isset($mother_maidenname) && $mother_maidenname)
								if($s2) $s2.=" AND mother_maidenname $sql_LIKE '$mother_maidenname%'"; else $s2.=" WHERE mother_maidenname $sql_LIKE '$mother_maidenname%'";
							if(isset($mother_mname) && $mother_mname)
								if($s2) $s2.=" AND mother_mname $sql_LIKE '$mother_mname%'"; else $s2.=" WHERE mother_mname $sql_LIKE '$mother_mname%'";
							if(isset($mother_lname) && $mother_lname)
								if($s2) $s2.=" AND mother_lname $sql_LIKE '$mother_lname%'"; else $s2.=" WHERE mother_lname $sql_LIKE '$mother_lname%'";
								
							#father
							if(isset($father_fname) && $father_name)
								if($s2) $s2.=" AND father_fname $sql_LIKE '$father_fname%'"; else $s2.=" WHERE father_fname $sql_LIKE '$father_fname%'";
							if(isset($father_mname) && $father_mname)
								if($s2) $s2.=" AND father_mname $sql_LIKE '$father_mname%'"; else $s2.=" WHERE father_mname $sql_LIKE '$father_mname%'";
							if(isset($father_lname) && $father_lname)
								if($s2) $s2.=" AND father_lname $sql_LIKE '$father_lname%'"; else $s2.=" WHERE father_lname $sql_LIKE '$father_lname%'";					
								
							
							if(isset($spouse_name) && $spouse_name)
								#if($s2) $s2.=" AND spouse_name $sql_LIKE '$spouse_name%'"; else $s2.=" spouse_name $sql_LIKE '$spouse_name%'";
								if($s2) $s2.=" AND spouse_name $sql_LIKE '$spouse_name%'"; else $s2.=" WHERE spouse_name $sql_LIKE '$spouse_name%'";

							if(isset($guardian_name) && $guardian_name)
								#if($s2) $s2.=" AND guardian_name $sql_LIKE '$guardian_name%'"; else $s2.=" guardian_name $sql_LIKE '$guardian_name%'";
								if($s2) $s2.=" AND guardian_name $sql_LIKE '$guardian_name%'"; else $s2.=" WHERE guardian_name $sql_LIKE '$guardian_name%'";

#echo "A: s2 = '".$s2."' <br> \n";
#echo "<br>brgy = ".$brgy_nr."<br>";								
#					if(isset($brgy_nr) && $brgy_nr){
							#edited by VAN 02-19-08
							if(isset($brgy_nr) && $brgy_nr && $brgy_nr!='NULL')
							  {							
								if($s2)
									#$s2.=" AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr AND cp.brgy_nr=$brgy_nr "; 
								  $s2.=" AND cp.brgy_nr=$brgy_nr "; 
								else 
									#$s2.=" sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr AND cp.brgy_nr=$brgy_nr ";
								  $s2.=" WHERE cp.brgy_nr=$brgy_nr ";
							  }
							elseif(isset($mun_nr) && $mun_nr){
								if($s2) 
									#$s2.=" AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr AND sm.mun_nr=sb.mun_nr AND sb.mun_nr=$mun_nr AND sb.brgy_nr=cp.brgy_nr "; 
									$s2.=" AND cp.mun_nr=$mun_nr "; 
								else 
									#$s2.=" sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr AND sm.mun_nr=sb.mun_nr AND sb.mun_nr=$mun_nr AND sb.brgy_nr=cp.brgy_nr ";
									$s2.=" WHERE cp.mun_nr=$mun_nr ";
							 }
							elseif(isset($prov_nr) && $prov_nr){
								if($s2) 
									#$s2.=" AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr AND sm.prov_nr=$prov_nr AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr "; 
									$s2.=" AND sm.prov_nr=$prov_nr "; 
								else 
									#$s2.=" sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr AND sm.prov_nr=$prov_nr AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr ";
									$s2.=" WHERE sm.prov_nr=$prov_nr ";
							 }
							elseif(isset($region_nr) && $region_nr){
								if($s2) 
									#$s2.=" AND sr.region_nr=sp.region_nr AND sp.region_nr=$region_nr AND sp.prov_nr=sm.prov_nr AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr "; 
									$s2.=" AND sp.region_nr=$region_nr "; 
								else 
									#$s2.=" sr.region_nr=sp.region_nr AND sp.region_nr=$region_nr AND sp.prov_nr=sm.prov_nr AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr ";
									$s2.=" WHERE sp.region_nr=$region_nr ";
							 }
							 #commented by VAN 02-19-08
							/*
							elseif(isset($ethnic_orig)&& $ethnic_orig){
								if($s2)
									#$s2.=" AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr AND cp.ethnic_orig=et.nr"; 
									$s2.=" AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr AND cp.ethnic_orig=et.nr"; 
								else	
									$s2.=" sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr AND cp.ethnic_orig=et.nr"; 
							} 
							
							else{
								if($s2) 
									$s2.=" AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr "; 
								else 
									$s2.=" sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr ";							 
							 }
							 */
#					} # end of if statement "if(isset($brgy_nr) && $brgy_nr)"
/*
					else{
						if($s2) 
							$s2.=" AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr "; 
						else 
							$s2.=" sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=cp.brgy_nr ";							 					
					}
*/
#echo "B: s2 = '".$s2."' <br> \n";
		$sql = $select_flds." ".$from_tb." ";   # burn added: March 7, 2007
		$HTTP_SESSION_VARS['sess_searchkey']=$sql.$s2;
		#$HTTP_SESSION_VARS['sess_searchkey']=$from_tb." ".$s2;
		
		#added by VAN 08-27-08
		#$fromwhere = $from_tb." ".$s2;
		
#echo "sql = '".$sql."' <br> \n";		
#echo "HTTP_SESSION_VARS['sess_searchkey'] = '".$HTTP_SESSION_VARS['sess_searchkey']."' <br> \n";
#echo "item, order = ".$oitem." - ".$odir;		
		#added by VAN 02-19-08
		if (empty($oitem))
			$oitem = 'date_reg';
		if (empty($odir))
			$odir = 'DESC';	
#echo "item, order = ".$oitem." - ".$odir;		
		#if(isset($oitem)&&!empty($oitem))	$sql=$sql.$s2." ORDER BY $oitem $odir";
		#	else $sql=$sql.$s2;
		$sql=$sql.$s2." ORDER BY name_last ASC, name_first ASC";
		//echo $sql;
	}
#echo "patient_register_archive.php : sql = '".$sql."' <br>\n";
#echo "s2 = '".$s2."' <br>\n";
	#commented by VAN 02-19-08
	#if($s2!=''){
		//echo $sql;
			//if($ergebnis=$db->Execute($sql)) 
		if($ergebnis=$db->SelectLimit($sql,$pagen->MaxCount(),$pagen->BlockStartIndex())){			
		
			$rows=$ergebnis->RecordCount();
			
#			if($rows==1&&$searchkey!='USE_SESSION_SEARCHKEY'){   # burn commented: March 8, 2007
				//* If result is single item, display the data immediately */
#				$result=$ergebnis->FetchRow();   # burn commented: March 8, 2007

#				header("Location:patient_register_show.php".URL_REDIRECT_APPEND."&target=archiv&origin=archive&pid=".$result['pid']);   # burn commented: March 8, 2007
#				header("Location:patient_register_show.php?sid=".$sid."&lang=".$lang."&origin=archive&pid=".$result['pid']."&target=archiv");   # burn added: March 8, 2007
				#$x=ob_get_contents(); 
				#ob_end_clean();
				#var_dump($x);
#				exit;   # burn commented: March 8, 2007
#			}else{   # burn commented: March 8, 2007

				$pagen->setTotalBlockCount($rows);
					
				# If more than one count all available
				if(isset($totalcount)&&$totalcount){
					$pagen->setTotalDataCount($totalcount);
				}else{
#					$sql="SELECT COUNT(pid) AS maxnr FROM $dbtable WHERE ".$s2;
					$sql="SELECT COUNT( DISTINCT  cp.pid) AS maxnr $from_tb ".$s2;
					#$sql=$from_tb." ".$s2;
					#echo "<br>sql count = ".$sql;
					
					if($result=$db->Execute($sql)){
						@$maxres=$result->FetchRow();
						$totalcount=$maxres['maxnr'];
						$pagen->setTotalDataCount($totalcount);
					}
									
				}
#			}   # burn commented: March 8, 2007
		}else{
			echo "$LDDbNoRead<p> $sql <p>";
		}
	#} #commented by VAN 02-19-08
}

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Added for the common header top block

 $smarty->assign('sToolbarTitle',$LDPatientRegister.' - '.$LDAdvancedSearch);

 # Added for the common header top block
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPatientRegister.' - '.$LDAdvancedSearch')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$$LDPatientRegister.' - '.$LDAdvancedSearch);

 $smarty->assign('sOnLoadJs','onLoad="if (window.focus) window.focus();"');

 $smarty->assign('pbHelp',"javascript:gethelp('person_archive.php')");

 $smarty->assign('pbBack',FALSE);

// #Include yahoo scripts
//ob_start();
//include_once($root_path.'modules/registration_admission/include/yh_script.php');
//$temp1 = ob_get_contents();
//ob_end_clean();
//$smarty->assign('yhScript',$temp1);
 
# Load GUI page
require('./gui_bridge/default/gui_person_reg_archive.php');

?>