<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

#added by VAN 06-25-08
#require($root_path."modules/registration_admission/ajax/clinics.common.php");

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


define('LANG_FILE','aufnahme.php');

global $allow_canserve;

$local_user='aufnahme_user';
require($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/inc_date_format_functions.php');

$thisfile=basename(__FILE__);
$toggle=0;

#if($HTTP_COOKIE_VARS['ck_login_logged'.$sid])
#	$breakfile=$root_path.'main/startframe.php'.URL_APPEND;
#else
#	$breakfile='aufnahme_pass.php'.URL_APPEND.'&target=entry';
if ($ptype=='er')
	$breakfile=$root_path.'modules/er/seg-er-functions.php'.URL_APPEND;
elseif ($ptype=='opd')
	$breakfile=$root_path.'modules/opd/seg-opd-functions.php'.URL_APPEND;
elseif ($ptype=='ipd')
	$breakfile=$root_path.'modules/ipd/seg-ipd-functions.php'.URL_APPEND;
elseif ($ptype=='medocs')
	$breakfile=$root_path.'modules/medocs/seg-medocs-functions.php'.URL_APPEND;
else
	$breakfile=$root_path.'main/startframe.php'.URL_APPEND;

# burn added: March 9, 2007
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
	$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
else
	$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];

$user_dept_info = $dept_obj->getUserDeptInfo($seg_user_name);
#echo $dept_obj->sql;


#if (strtolower($_REQUEST['ptype'])=='opd') {
/*
if ($allow_opd_user){
	$encounter_type_search='2';   # search under OPD Triage
#}elseif($user_dept_info['dept_nr']==149){
}elseif($allow_er_user){
	$encounter_type_search='1';   # search under ER Triage
#}elseif(($user_dept_info['dept_nr']==148)||($user_dept_info['dept_nr']==151)){
}elseif(($allow_ipd_user)||($allow_medocs_user)){
	$encounter_type_search='1,2,3,4';   # search under Admitting Section or Medical Records
}else{
	#$encounter_type_search=0;   # User has no permission to use Admission Search
	$encounter_type_search=2;   # User has no permission to use Admission Search
	$sql_ext = " AND enc.current_dept_nr='".$user_dept_info['dept_nr']."' ";
}
*/

if ($_GET['ptype'])
	$ptype = $_GET['ptype'];
elseif ($HTTP_SESSION_VARS['ptype'])
	$ptype = $HTTP_SESSION_VARS['ptype'];

$HTTP_SESSION_VARS['ptype'] = $ptype;
	#echo "ptype = ".$ptype;

if($mode=='paginate'){
	if (!empty($HTTP_SESSION_VARS['ptype']))
		$ptype = $HTTP_SESSION_VARS['ptype'];

}

if (stristr($user_dept_info['job_function_title'], 'doctor'))
	$is_doctor = 1;
else
	$is_doctor = 0;

#echo "doc = ".$is_doctor;
#if(($allow_medocs_user)||($is_doctor )){
if ((($allow_opd_user)&&($ptype=='opd'))||(($allow_phs_user)&&($ptype=='phs'))){
	$encounter_type_search='2';   # search under OPD Triage
	$forphs = 0;
	if (($allow_phs_user)&&($ptype=='phs'))
		$forphs = 1;
#}elseif($user_dept_info['dept_nr']==149){
}elseif(($allow_er_user)&&($ptype=='er')){
	$encounter_type_search='1';   # search under ER Triage
#}elseif(($user_dept_info['dept_nr']==148)||($user_dept_info['dept_nr']==151)){
}elseif(($allow_ipd_user)&&($ptype=='ipd')){
	$encounter_type_search='3,4';   # search under Admitting Section or Medical Records
}elseif(($allow_medocs_user)||($allow_opd_user)||($allow_er_user)||($allow_ipd_user)||($is_doctor)){
	#$encounter_type_search=0;   # User has no permission to use Admission Search
	$encounter_type_search='1,2,3,4';   # User has no permission to use Admission Search

	if ($user_dept_info['dept_nr'])
		$sql_ext = " AND enc.current_dept_nr='".$user_dept_info['dept_nr']."' ";
}
#print_r($HTTP_SESSION_VARS);
#echo "type = ".$encounter_type_search;
#added by VAN 07-14-08
/*
if (stristr($user_dept_info['job_function_title'], 'doctor')){
	$is_doctor = 1;
	if (($user_dept_info['admit_inpatient']==1)&&($user_dept_info['admit_outpatient']==1))
		$encounter_type_search='1,2,3,4';   # all patients
	elseif (($user_dept_info['admit_inpatient']==1)&&($user_dept_info['admit_outpatient']==0))
		$encounter_type_search='1,3,4';   # all patients
	elseif (($user_dept_info['admit_inpatient']==0)&&($user_dept_info['admit_outpatient']==1))
		$encounter_type_search='2';   # all patients
}else{
	$is_doctor = 0;
}
*/
#-----------------------

# Set value for the search mask
#$searchprompt=$LDEntryPrompt;   # transferred below

# Limit name search to at least two characters for lastname & firstname
if ($searchkey) $searchkey=strtoupper(trim($searchkey));
/*
if (!preg_match("/^[A-Z0-9|Ñ]{2}[A-Z0-9|Ñ ]*\s*,\s*[A-Z0-9|Ñ]{2}[A-Z0-9|Ñ ]*$/",$searchkey) &&
		!preg_match("/^\d{1,2}/\d{1,2}/\d{4}$/", $searchkey) &&
		!preg_match("/^\d{1,2}-\d{1,2}-\d{4}$/", $searchkey) &&
		!preg_match("/^\d+$/", $searchkey)
	) {
		echo "<b>here = ".$searchkey."<br>";
	$searchkey = "";
}
*/

# Special case for direct access from patient listings
# If forward nr ok, use it as searchkey
if(isset($fwd_nr)&&$fwd_nr&&is_numeric($fwd_nr)){
	$searchkey=$fwd_nr;
	$mode='search';
}else{
	if(!isset($searchkey)) $searchkey='';
}

if(!isset($mode)) $mode='';

# Initialize page´s control variables
if($mode=='paginate'){
	$searchkey=$HTTP_SESSION_VARS['sess_searchkey'];
}else{
	# Reset paginator variables
	$pgx=0;
	$totalcount=0;
	$odir='';
	$oitem='';
}

#added by VAN 06-11-08
#if (empty($searchkey))
#	$searchkey = date("m/d/Y");

if (empty($mode))
	$mode = 'search';

#Load and create paginator object
require_once($root_path.'include/care_api_classes/class_paginator.php');
$pagen=new Paginator($pgx,$thisfile,$HTTP_SESSION_VARS['sess_searchkey'],$root_path);

if(isset($mode)&&($mode=='search'||$mode=='paginate')&&isset($searchkey)&&($searchkey)){

	include_once($root_path.'include/inc_date_format_functions.php');

	//$db->debug=true;

	if($mode!='paginate'){
		$HTTP_SESSION_VARS['sess_searchkey']=$searchkey;
	}
		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');

		$GLOBAL_CONFIG=array();

		include_once($root_path.'include/care_api_classes/class_globalconfig.php');
		$glob_obj=new GlobalConfig($GLOBAL_CONFIG);

		# Get the max nr of rows from global config
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		if(empty($GLOBAL_CONFIG['pagin_patient_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS); # Last resort, use the default defined at the start of this page
			else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_patient_search_max_block_rows']);

		$searchkey=trim($searchkey);
		$suchwort=$searchkey;
/*
echo "searchkey = '".$searchkey."' <br> \n";
echo "suchwort = '".$suchwort."' <br> \n";
echo "is_numeric(suchwort) = '".is_numeric($suchwort)."' <br> \n";
*/
#echo "".$."' <br> \n";

#echo "suchwort = '".str_replace("T","",$suchwort)."' <br> \n";

		#added by VAN 06-25-08
#if (($user_dept_info['dept_nr']!=150)&&($user_dept_info['dept_nr']!=148)&&($user_dept_info['dept_nr']!=149)){
#if ((($user_dept_info['dept_nr']!=148)&&($user_dept_info['dept_nr']!=149)&&($user_dept_info['dept_nr']!=150)&&($user_dept_info['dept_nr']!=151))&&(!$is_doctor)){
#if (((!$allow_ipd_user)&&(!$allow_er_user)&&(!$allow_opd_user)&&(!$allow_medocs_user))&&(!$is_doctor)){
#commented by VAN 05-23-09
#if ((($allow_ipd_user)||($allow_er_user)||($allow_opd_user)||($allow_phs_user)||(!$allow_medocs_user))||(!$is_doctor)){
if ($allow_canserve){
		if (!$isServed)
			#Not yet Served
			$isServed = 3;

		if ($isServed==2)
			$served_cond = " AND is_served=1 ";
		elseif ($isServed==3)
			$served_cond = " AND is_served=0 ";
		else
			$served_cond = "";
}
		#----------------------

		#added by VAN 02-20-08
		$suchwort = str_replace("T","",$suchwort);
		if(is_numeric($suchwort)) {
			#$suchwort=(int) $suchwort;
			$numeric=1;
			if(empty($oitem)) $oitem='encounter_nr';
			if(empty($odir)) $odir='DESC'; # default, latest pid at top

			#$sql2=" WHERE ( enc.encounter_nr='$suchwort' OR enc.encounter_nr $sql_LIKE '%$suchwort' )";
			#edited by VAN 02-20-08
			/*
			$sql2=" WHERE (( enc.encounter_nr='$suchwort' OR enc.encounter_nr = '$suchwort' )
										 OR (( enc.pid='$suchwort' OR enc.pid = '%$suchwort' ) OR enc.pid='$searchkey' OR enc.pid $sql_LIKE '%$searchkey'))";
			*/
			#$sql2=" WHERE (/*enc.encounter_nr='$suchwort'  OR*/ enc.pid='$searchkey')";
			if (strlen($suchwort)<10)
					$sql2=" WHERE (enc.pid='$searchkey')";
			else
					$sql2=" WHERE (enc.encounter_nr='$suchwort')";

		} elseif ($searchkey) {

			# Try to detect if searchkey is composite of first name + last name
			if(stristr($searchkey,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}

			#$searchkey=strtr($searchkey,',',' ');
			#$cbuffer=explode(' ',$searchkey);
			if(stristr($searchkey, ',') === FALSE){
				$cbuffer=explode(' ',$searchkey);
				#$newsearchkey = $searchkey;
				$lnameOnly = 1;
				#$newquery = " OR name_last $sql_LIKE '".$newsearchkey."%'";
			}else{
				$cbuffer=explode(',',$searchkey);
				$newquery = "";
				$lnameOnly = 0;
			}

			# Remove empty variables
			for($x=0;$x<sizeof($cbuffer);$x++){
				$cbuffer[$x]=trim($cbuffer[$x]);
				if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
			}

			# Arrange the values, ln= lastname, fn=first name, bd = birthday
			if($lastnamefirst){
				$fn=$comp[1];
				$ln=$comp[0];
				$bd=$comp[2];
			}else{
				$fn=$comp[0];
				$ln=$comp[1];
				$bd=$comp[2];
			}

			#if(empty($oitem)) $oitem='name_last';
			#added by VAN 02-21-08
			if (empty($oitem))
				$oitem = 'encounter_date';
			if (empty($odir))
				$odir = 'DESC';

			# Check the size of the comp
			if(sizeof($comp)>1){
				$cntlast = sizeof($cbuffer)-1;
				if (sizeof($cbuffer) > 2){
					#$sql2=" WHERE (( reg.name_last $sql_LIKE '".strtr($ln,'+',' ')."%'
						#      		AND reg.name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') OR (name_last $sql_LIKE '".$searchkey."%' OR name_first $sql_LIKE '".$searchkey."%') )";
					#$sql2=" WHERE (((reg.name_last $sql_LIKE '%".strtr($ln,'+',' ')."%' OR reg.name_last $sql_LIKE '".strtr($comp[$cntlast],'+',' ')."%') AND reg.name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') OR (reg.name_last $sql_LIKE '".$searchkey."%' OR reg.name_first $sql_LIKE '".$searchkey."%'))";
					if ($lnameOnly)
						$sql2=" WHERE (name_last $sql_LIKE '".$searchkey."%')";
					else
						$sql2=" WHERE (((reg.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' OR reg.name_last $sql_LIKE '".strtr($comp[$cntlast],'+',' ')."%') AND reg.name_first $sql_LIKE '".strtr($fn,'+',' ')."%'))";
					$bd=$comp[sizeof($cbuffer)];

				}else{
						#$sql2=" WHERE ( reg.name_last $sql_LIKE '".strtr($ln,'+',' ')."%'
							 #   		AND reg.name_first $sql_LIKE '%".strtr($fn,'+',' ')."%')";
						#$sql2=" WHERE ((reg.name_last $sql_LIKE '%".strtr($ln,'+',' ')."%' AND reg.name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') OR (reg.name_last $sql_LIKE '".$searchkey."%' OR reg.name_first $sql_LIKE '%".$searchkey."%'))";
						if ($lnameOnly)
							$sql2=" WHERE (name_last $sql_LIKE '".$searchkey."%')";
						else
							$sql2=" WHERE ((reg.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND reg.name_first $sql_LIKE '".strtr($fn,'+',' ')."%'))";
				}
				/*
					$cntlast = sizeof($cbuffer)-1;
				if (sizeof($cbuffer) > 2){
					$sql2=" WHERE (((name_last $sql_LIKE '".strtr($ln,'+',' ')."%' OR name_last $sql_LIKE '".strtr($comp[$cntlast],'+',' ')."%') AND name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') OR (name_last $sql_LIKE '".$searchkey."%' OR name_first $sql_LIKE '".$searchkey."%'))";
					$bd=$comp[sizeof($cbuffer)];
				}else
					$sql2=" WHERE ((name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND name_first $sql_LIKE '%".strtr($fn,'+',' ')."%') OR (name_last $sql_LIKE '".$searchkey."%' OR name_first $sql_LIKE '%".$searchkey."%'))";
				*/
				if($bd){
					$stddate=formatDate2STD($bd,$date_format);
					if(!empty($stddate)){
						#$sql2.=" AND (reg.date_birth = '$stddate' OR reg.date_birth $sql_LIKE '%$bd%')";
						#$sql2.=" AND ((reg.date_birth = '$stddate' OR reg.date_birth $sql_LIKE '%$bd%')
						 #            OR (enc.encounter_date = '$stddate' OR enc.encounter_date $sql_LIKE '%$bd%'))";
												 $sql2.=" AND (DATE(enc.encounter_date) = '$stddate' )";
					}
				}

				if(empty($odir)) $odir='DESC'; # default, latest birth at top

			}else{

				#$sql2=" WHERE (reg.name_last $sql_LIKE '%".strtr($suchwort,'+',' ')."%'
					#            		OR reg.name_first $sql_LIKE '%".strtr($suchwort,'+',' ')."%'";
				#if ($lnameOnly)
				#	$sql2=" WHERE (name_last $sql_LIKE '".$searchkey."%'";
				#else
				#	$sql2=" WHERE (reg.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%'";

				$bufdate=formatDate2STD($suchwort,$date_format);

				if(!empty($bufdate)){
					#$sql2.= " OR reg.date_birth $sql_LIKE '$bufdate'";
					#$sql2.= " OR reg.date_birth = '$bufdate' OR enc.encounter_date = '$bufdate'";
					$sql2.= " WHERE DATE(enc.encounter_date) = '$bufdate'";
				}
				#$sql2.=")";
				if(empty($odir)) $odir='ASC'; # default, ascending alphabetic
			}
		}

#			$sql2.=" AND enc.pid=reg.pid
#					  AND enc.encounter_status <> 'cancelled'
#					  AND enc.is_discharged=0
#					  AND enc.status NOT IN ('void','hidden','inactive','deleted')  ORDER BY ";   # burn commented: March 9, 2007
			/*
			$sql2.=" AND enc.pid=reg.pid
						AND enc.encounter_status <> 'cancelled'
						AND enc.is_discharged=0
						AND enc.status NOT IN ('void','hidden','inactive','deleted')
						AND enc.encounter_type IN ($encounter_type_search)
						AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr
						AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=reg.brgy_nr
						ORDER BY ";   # burn added: March 9, 2007
			*/
			$sql_phs = "";
			if ($forphs){
				$qry_phs = "IF(((dep.dependent_pid IS NOT NULL) OR (ps.nr IS NOT NULL)),'PHS','') AS discountid,";
				#$sql_phs = " AND encounter_status='phs' ";
				$sql_phs = " AND (encounter_status='phs' OR IF(((dep.dependent_pid IS NOT NULL) OR (ps.nr IS NOT NULL)),'PHS','')='PHS') ";

				$left_phs = "LEFT JOIN care_personell AS ps ON reg.pid=ps.pid
													AND ((date_exit NOT IN (DATE(NOW())) AND date_exit > DATE(NOW())) OR date_exit='0000-00-00' OR date_exit IS NULL)
													AND ((contract_end NOT IN (DATE(NOW())) AND contract_end > DATE(NOW()))
													OR contract_end='0000-00-00' OR contract_end IS NULL)

											LEFT JOIN seg_dependents AS dep ON dep.dependent_pid=reg.pid AND dep.status='member'
											";
			}
			#else
				#$sql_phs = " AND encounter_status<>'phs' ";

			if ($encounter_type_search)
				$sql_enc = " AND enc.encounter_type IN ($encounter_type_search) $sql_phs ";
			else
				$sql_enc  = "";
			#print_r($user_dept_info);

			#edited by VAN 09-23-09
			#if (($user_dept_info['location_nr'])&&($ptype=='opd'))
			if ($allow_only_clinic){
				if ($forphs)
					$sql_dept = " ";
				else
					#$sql_dept = " AND current_dept_nr = '".$user_dept_info['location_nr']."' ";
					$sql_dept = " AND (current_dept_nr = '".$user_dept_info['location_nr']."' OR current_dept_nr IN (SELECT nr FROM care_department WHERE parent_dept_nr='".$user_dept_info['location_nr']."'))";
			}else
				$sql_dept  = "";
			#edited by VAN 05-13-08
			$sql2.=" AND enc.encounter_status <> 'cancelled'
						".$sql_ext."
						AND enc.is_discharged=0
						AND reg.death_date='0000-00-00'
						AND enc.status NOT IN ('void','hidden','inactive','deleted')
						/*AND enc.encounter_type IN ($encounter_type_search)*/
						$sql_enc
						$served_cond
						$sql_dept
						ORDER BY ";   # burn added: March 9, 2007

			# Filter if it is personnel nr
#			if($oitem=='encounter_nr') $sql2.='enc.'.$oitem.' '.$odir;   # burn commented: March 9, 2007
#				else $sql2.='reg.'.$oitem.' '.$odir;   # burn commented: March 9, 2007
			/*
			if ($sql_ext)
				$sql2.= " encounter_date ASC, name_first ASC";
			else
				$sql2.=$oitem.' '.$odir;   # burn added: March 9, 2007
			*/

			#edited by VAN 07-28-08
			$sql2.= " name_last ASC, name_first ASC, encounter_date DESC";

#			$dbtable='FROM care_encounter as enc,care_person as reg ';   # burn commented: March 9, 2007
			/*
			$dbtable=" FROM care_encounter as enc,care_person as reg ".
						" , seg_barangays AS sb, seg_municity AS sm, ".
						" seg_provinces AS sp, seg_regions AS sr ";   # burn added: March 9, 2007
			*/
			#edited by VAN 05-13-08
			$dbtable=" FROM care_encounter as enc
							INNER JOIN care_person as reg ON enc.pid=reg.pid
							LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=reg.brgy_nr
							LEFT JOIN seg_municity AS sm ON sm.mun_nr=reg.mun_nr
							LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
							LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
							$left_phs ";   # burn added: March 9, 2007

#			$sql='SELECT enc.encounter_nr, enc.encounter_class_nr, enc.is_discharged,
#								reg.name_last, reg.name_first, reg.date_birth, reg.addr_zip,reg.sex '.$dbtable.$sql2;   # burn commented: March 9, 2007
			#edited by VAN 09-19-2012
            #add SQL_CALC_FOUND_ROWS
            $sql=" SELECT SQL_CALC_FOUND_ROWS $qry_phs enc.encounter_nr, enc.encounter_date, enc.encounter_class_nr, enc.encounter_type, ".
					" enc.is_served, enc.date_served, ".
					" enc.admission_dt, enc.is_discharged, reg.name_last, reg.name_first, reg.name_middle,reg.date_birth, reg.addr_zip,reg.sex ".
					" , (SELECT id FROM care_department AS dept WHERE dept.nr = enc.consulting_dept_nr) AS consulting_dept_name ". # burn added: July 12, 2007
					" , (SELECT id FROM care_department AS dept WHERE dept.nr = enc.current_dept_nr) AS current_dept_name ". # burn added: July 12, 2007
					" , sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name ".$dbtable.$sql2;   # burn added: March 9, 2007
			//echo $sql;
	#echo "dbtable = ".$dbtable;
	#echo "sql2 = ".$sql2;
/*
SELECT enc.encounter_nr, enc.encounter_class_nr, enc.is_discharged,
	reg.name_last, reg.name_first, reg.date_birth, reg.addr_zip,reg.sex
	, sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name
FROM care_encounter as enc,care_person as reg
	, seg_barangays AS sb, seg_municity AS sm,
	seg_provinces AS sp, seg_regions AS sr
WHERE (reg.name_last LIKE '%%' OR reg.name_first LIKE '%%') AND enc.pid=reg.pid
	AND enc.encounter_status <> 'cancelled' AND enc.is_discharged=0
	AND enc.status NOT IN ('void','hidden','inactive','deleted')
	AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr
	AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=reg.brgy_nr
ORDER BY name_last ASC
*/
#echo "aufnahme_daten_such.php : sql = '".$sql."' <br> \n";
#exit();
#echo "LDAmbulant = '".$LDAmbulant."' <br> \n";
			if($ergebnis=$db->SelectLimit($sql,$pagen->MaxCount(),$pagen->BlockStartIndex()))
					{
                
				if ($linecount=$ergebnis->RecordCount())
				{
					#if(($linecount==1)&&$numeric&&$mode=='search')
					if(($linecount==1)&&($mode=='search'))
					{
						$zeile=$ergebnis->FetchRow();
						header('Location:aufnahme_daten_zeigen.php'.URL_REDIRECT_APPEND.'&from=such&encounter_nr='.$zeile['encounter_nr'].'&target=search&ptype='.$ptype);
						exit;
					}

					$pagen->setTotalBlockCount($linecount);

					# If more than one count all available
					if(isset($totalcount)&&$totalcount){
						$pagen->setTotalDataCount($totalcount);
					}else{
                        #edited by VAN 09-19-2012
						#Optimize queries
                        # Count total available data
						/*if($dbtype=='mysql'){
							$sql='SELECT COUNT(enc.encounter_nr) AS "count" '.$dbtable.$sql2;
						}else{
							$sql='SELECT * '.$dbtable.$sql2;
						}

						if($result=$db->Execute($sql)){
							if ($totalcount=$result->RecordCount()) {
								if($dbtype=='mysql'){
									$rescount=$result->FetchRow();
											$totalcount=$rescount['count'];
								}
									}
						}*/
                        
                        $rs= $db->execute('SELECT FOUND_ROWS();');
                        $row = $rs->FetchRow();
                        $totalcount = $row['FOUND_ROWS()'];
						$pagen->setTotalDataCount($totalcount);
					}
					# Set the sort parameters
					$pagen->setSortItem($oitem);
					$pagen->setSortDirection($odir);
				}

			}
			 else {echo "<p>".$sql."<p>$LDDbNoRead";};
}

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in the toolbar
 //$smarty->assign('sToolbarTitle',$LDPatientSearch);
#if (($user_dept_info['dept_nr']==150) || ($user_dept_info['dept_nr']==149)){
#if ($user_dept_info['dept_nr']==148){
if (($allow_ipd_user)&&($ptype=='ipd')){
	# search under ER or OPD Triage
	#$smarty->assign('sToolbarTitle',"$LDConsultation :: $LDSearch (".strtoupper($user_dept_info['name_formal']).")");   # burn added : May 15, 2007
	#$smarty->assign('sToolbarTitle',"$LDAdmission :: $LDSearch");   # burn added : May 15, 2007

	$smarty->assign('sToolbarTitle',"$LDAdmission :: $LDSearch (".strtoupper($user_dept_info['name_formal']).")");   # burn added : May 15, 2007
}else{
	#$smarty->assign('sToolbarTitle',"$LDAdmission :: $LDSearch");   # burn added : May 15, 2007
	#$smarty->assign('sToolbarTitle',"$LDAdmission :: $LDSearch (".strtoupper($user_dept_info['name_formal']).")");   # burn added : May 15, 2007
	if (empty($user_dept_info['name_formal']))
		$smarty->assign('sToolbarTitle',"$LDConsultation :: $LDSearch");   # burn added : May 15, 2007
	else
		$smarty->assign('sToolbarTitle',"$LDConsultation :: $LDSearch (".strtoupper($user_dept_info['name_formal']).")");   # burn added : May 15, 2007
}
# $smarty->assign('sToolbarTitle',"$LDAdmission :: $LDSearch");   # burn commented : May 15, 2007

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$LDPatientSearch);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('admission_how2search.php','$from')");

	# Onload Javascript code
 $smarty->assign('sOnLoadJs','onLoad="if(window.focus) window.focus();document.searchform.searchkey.select();DisabledSearch();"');

 # Hide the return button
 $smarty->assign('pbBack',FALSE);

 #added by VAN 06-25-08
 #ob_start();
 #$xajax->printJavascript($root_path.'classes/xajax');
 #$sTemp = ob_get_contents();
 #ob_end_clean();

 $smarty->append('JavaScript',$sTemp);
 #----------------

#
# Load the tabs
#
$target='search';
$parent_admit = TRUE;
include('./gui_bridge/default/gui_tabs_patadmit.php');

#
# Prepare the javascript validator
#
if(!isset($searchform_count) || !$searchform_count){
	$smarty->assign('sJSFormCheck','<script language="javascript">
	<!--

		function isValidSearch(key) {

					if (typeof(key)==\'undefined\') return false;
					var s=key.toUpperCase();
					return (
						/^[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*\s*,\s*[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*$/.test(s) ||
						/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
						/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
						/^\d+$/.test(s)
					);
				}

		function chkSearch(d){

			//if((d.searchkey.value=="") || (d.searchkey.value==" ")){
			//if((d.searchkey.value=="") || (d.searchkey.value==" ") || (d.searchkey.value.length < 4)){
			if (!isValidSearch(d.searchkey.value)) {
				d.searchkey.focus();
				return false;
			}else	{
				return true;
			}

			//return true;
		}

		function DisabledSearch(){
					var b=isValidSearch(document.getElementById(\'searchkey\').value);
					document.getElementById("searchButton").style.cursor=(b?"pointer":"default");
					document.getElementById("searchButton").disabled = !b;
		}

		//added by VAN 06-25-08
		function ToBeServed(objID, encounter_nr, dept){
			var is_served;
			if (document.getElementById(objID).checked==true)
				is_served = 1;
			else
				is_served = 0;

			xajax_savedServedPatient(encounter_nr, is_served, dept);
		}

		function refreshWindow(){
			window.location.href=window.location.href;
		}

		function UpdateQuery(objVal){
			var isServeDcond;
			xajax_populatePatientList();
			document.getElementById("isServed").value = objVal;
		}

		function onsubmitForm(){
			searchform.submit();
		}

		//---------------------
	// -->
	</script>');
}

#
# Prepare the form params
#
# Set value for the search mask
#$searchprompt=$LDEntryPrompt;   # transferred from above; burn commented : May 18, 2007
#$searchprompt="Enter the search keyword. For example: encounter number, or lastname, or firstname, or date of birth, etc.";   # burn added : May 18, 2007
$searchprompt=$LDSearchPromptCons; #added by pet, april 18, 2008, in replacement of the above text

$sTemp = 'method="post" name="searchform';
if($searchform_count) $sTemp = $sTemp."_".$searchform_count;
$sTemp = $sTemp.'" onSubmit="return chkSearch(this)"';
if(isset($search_script) && $search_script!='') $sTemp = $sTemp.' action="'.$search_script.'"';
$smarty->assign('sFormParams',$sTemp);
$smarty->assign('searchprompt',$searchprompt);

#added by VAN 06-25-08
#if (($user_dept_info['dept_nr']!=148)&&($user_dept_info['dept_nr']!=149)&&($user_dept_info['dept_nr']!=150)&&($user_dept_info['dept_nr']!=151)){
#if ((!$allow_ipd_user)&&(!$allow_er_user)&&(!$allow_opd_user)&&(!$allow_medocs_user)){
#if ((($allow_ipd_user)||($allow_er_user)||($allow_opd_user)||($allow_phs_user))&&(!$allow_medocs_user)){
if ($allow_canserve){
	if ($is_doctor){
	$smarty->assign('sClinics',false);
	}else{
	if (!($isServed))
		$isServed = 3;

	$smarty->assign('sClinics',true);

	$smarty->assign('sCheckAll','<input type="radio" name="served" id="served" value="1" '.(($isServed==1)?'checked="checked" ':'').' onClick="UpdateQuery(this.value);" >');
	$smarty->assign('LDCheckAll',"All");

	$smarty->assign('sCheckYes','<input type="radio" name="served" id="served" value="2" '.(($isServed==2)?'checked="checked" ':'').' onClick="UpdateQuery(this.value);" >');
	$smarty->assign('LDCheckYes',"Served");

	$smarty->assign('sCheckNo','<input type="radio" name="served" id="served" value="3" '.(($isServed==3)?'checked="checked" ':'').' onClick="UpdateQuery(this.value);" >');
	$smarty->assign('LDCheckNo',"Not Yet Served");
	}
}else{
	$smarty->assign('sClinics',false);
}
#--------------------------

#
# Prepare the hidden inputs
#
$smarty->assign('sHiddenInputs','<input id="searchButton" name="searchButton" type="image" '.createLDImgSrc($root_path,'searchlamp.gif','0','absmiddle').'>
		<input type="hidden" name="sid" value="'.$sid.'">
		<input type="hidden" name="lang" value="'.$lang.'">
		<input type="hidden" name="noresize" value="'.$noresize.'">
		<input type="hidden" name="target" value="'.$target.'">
		<input type="hidden" name="user_origin" value="'.$user_origin.'">
		<input type="hidden" name="origin" value="'.$origin.'">
		<input type="hidden" name="retpath" value="'.$retpath.'">
		<input type="hidden" name="aux1" value="'.$aux1.'">
		<input type="hidden" name="ipath" value="'.$ipath.'">
		<input type="hidden" name="isServed" id="isServed" value="'.(($isServed)?$isServed:3).'">
		<input type="hidden" name="mode" value="search">');
#commented by VAN 04-17-08
#$smarty->assign('sCancelButton','<a href="patient.php'.URL_APPEND.'&target=search"><img '.createLDImgSrc($root_path,'cancel.gif','0').'></a>');
#$smarty->assign('sAllButton','<img '.createLDImgSrc($root_path,'all.gif','0','absmiddle').' style="cursor:pointer" onClick="document.getElementById(\'searchkey\').value=\'*\'; searchform.submit();">');

if($mode=='search'||$mode=='paginate'){

	if ($linecount) $smarty->assign('LDSearchFound',str_replace("~no.~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.');
		else $smarty->assign('LDSearchFound',str_replace('~no.~','0',$LDSearchFound));

	if ($linecount) {

		$smarty->assign('bShowResult',TRUE);

		# Load the common icons and images
		$img_options=createComIcon($root_path,'pdata.gif','0');
		$img_male=createComIcon($root_path,'spm.gif','0');
		$img_female=createComIcon($root_path,'spf.gif','0');

		$smarty->assign('LDCaseNr',$pagen->makeSortLink($LDCaseNr,'encounter_nr',$oitem,$odir,$targetappend));
        $smarty->assign('LDCaseNr',$LDCaseNr);
#		$smarty->assign('LDCaseNr',$pagen->makeSortLink("Encounter Number",'encounter_nr',$oitem,$odir,$targetappend));
#		$smarty->assign('segEncDate',$pagen->makeSortLink("Encounter Date",'encounter_date',$oitem,$odir,$targetappend));   # burn added: May 11,, 2007
		#$smarty->assign('segEncDate',$pagen->makeSortLink("Transaction Date",'encounter_date',$oitem,$odir,$targetappend));   # burn added: May 11,, 2007
		$smarty->assign('segEncDate','Transaction Date');   # burn added: May 11,, 2007
        #$smarty->assign('segCurrentDept',$pagen->makeSortLink("Clinic/Department",'current_dept_name',$oitem,$odir,$targetappend));   # burn added: May 11,, 2007
        $smarty->assign('segCurrentDept','Clinic/Department');   # burn added: May 11,, 2007
		#$smarty->assign('LDSex',$pagen->makeSortLink($LDSex,'sex',$oitem,$odir,$targetappend));
        $smarty->assign('LDSex',$LDSex);
		#$smarty->assign('LDLastName',$pagen->makeSortLink($LDLastName,'name_last',$oitem,$odir,$targetappend));
        $smarty->assign('LDLastName',$LDLastName);
		#$smarty->assign('LDFirstName',$pagen->makeSortLink($LDFirstName,'name_first',$oitem,$odir,$targetappend));
        $smarty->assign('LDFirstName',$LDFirstName);

		#$smarty->assign('LDMiddleName',$pagen->makeSortLink("Middle Name",'name_middle',$oitem,$odir,$targetappend));
        $smarty->assign('LDMiddleName','Middle Name');

		#$smarty->assign('LDBday',$pagen->makeSortLink($LDBday,'date_birth',$oitem,$odir,$targetappend));
        $smarty->assign('LDBday',$LDBday);
		#$smarty->assign('segBrgy',$pagen->makeSortLink("Barangay",'brgy_name',$oitem,$odir,$targetappend));   # burn added: March 9, 2007
        $smarty->assign('segBrgy','Barangay');   # burn added: March 9, 2007
		#$smarty->assign('segMuni',$pagen->makeSortLink("Muni/City",'mun_name',$oitem,$odir,$targetappend));   # burn added: March 9, 2007
        $smarty->assign('segMuni','Muni/City');   # burn added: March 9, 2007
#		$smarty->assign('LDZipCode',$pagen->makeSortLink($LDZipCode,'addr_zip',$oitem,$odir,$targetappend));   # burn commented: March 9, 2007
#		$smarty->assign('LDZipCode',$pagen->makeSortLink($LDZipCode,'zipcode',$oitem,$odir,$targetappend));   # burn added: March 9, 2007
		$smarty->assign('LDOptions',$LDOptions);

		#added by VAN 06-25-08
		#only be displayed in clinics
		#if (($user_dept_info['dept_nr']!=148)&&($user_dept_info['dept_nr']!=149)&&($user_dept_info['dept_nr']!=150)&&($user_dept_info['dept_nr']!=151)){
		#if ((!$allow_ipd_user)&&(!$allow_er_user)&&(!$allow_opd_user)&&(!$allow_medocs_user)){
		#	if (!$is_doctor)
						if ($allow_canserve)
				$smarty->assign('LDServeOption',"To be Served");
		#}
		#-------------------

		$sTemp = '';
		while($zeile=$ergebnis->FetchRow()){

			$full_en=$zeile['encounter_nr'];

			$smarty->assign('toggle',$toggle);
			$toggle = !$toggle;

			$smarty->assign('sCaseNr',$full_en);
/*				# burn commented: March 13, 2007
			if($zeile['encounter_class_nr']==2){
				$smarty->assign('sOutpatientIcon','<img '.createComIcon($root_path,'redflag.gif').'>');
				$smarty->assign('LDAmbulant',$LDAmbulant);
			}else{
				$smarty->assign('sOutpatientIcon','');
				$smarty->assign('LDAmbulant','');
			}
*/
				# burn added: March 13, 2007
			if($zeile['encounter_type']==1){
				$smarty->assign('sOutpatientIcon','<img '.createComIcon($root_path,'flag_red.gif').'>');
				$smarty->assign('LDAmbulant','<font size=1 color="red">ER</font>');
			}elseif($zeile['encounter_type']==2){
				$smarty->assign('sOutpatientIcon','<img '.createComIcon($root_path,'flag_blue.gif').'>');
				#$smarty->assign('LDAmbulant','<font size=1 color="blue">Outpatient</font>');
				if ($ptype=='opd')
					$smarty->assign('LDAmbulant','<font size=1 color="blue">OPD</font>');
				elseif ($ptype=='phs')
					$smarty->assign('LDAmbulant','<font size=1 color="blue">PHS</font>');
			}elseif(($zeile['encounter_type']==3)||($zeile['encounter_type']==4)){
				$smarty->assign('sOutpatientIcon','<img '.createComIcon($root_path,'flag_green.gif').'>');
				#$smarty->assign('LDAmbulant','<font size=1 color="green">Inpatient</font>');
				$smarty->assign('LDAmbulant','<font size=1 color="green">IPD</font>');
			}

				# burn added: May 11,, 2007
			if (($zeile['encounter_type']==1)||($zeile['encounter_type']==2))
				$smarty->assign('sEncDate',@formatDate2Local($zeile['encounter_date'],$date_format,1));
			elseif(($zeile['encounter_type']==3)||($zeile['encounter_type']==4))
				$smarty->assign('sEncDate',@formatDate2Local($zeile['admission_dt'],$date_format,1));

			$smarty->assign('sCurrentDept',$zeile['current_dept_name']);

			switch(strtolower($zeile['sex'])){
				case 'f': $smarty->assign('sSex','<img '.$img_female.'>'); break;
				case 'm': $smarty->assign('sSex','<img '.$img_male.'>'); break;
				default: $smarty->assign('sSex','&nbsp;'); break;
			}
			$smarty->assign('sLastName',ucfirst($zeile['name_last']));
			$smarty->assign('sFirstName',ucfirst($zeile['name_first']));

			$smarty->assign('sMiddleName',ucfirst($zeile['name_middle']));

			#
			# If person is dead show a black cross
			#
			if($zeile['death_date']&&$zeile['death_date']!=$dbf_nodate) $smarty->assign('sCrossIcon','<img '.createComIcon($root_path,'blackcross_sm.gif','0','absmiddle').'>');
				else $smarty->assign('sCrossIcon','');

				# burn added: March 27, 2007
			$date_birth = @formatDate2Local($zeile['date_birth'],$date_format);
			$bdateMonth = substr($date_birth,0,2);
			$bdateDay = substr($date_birth,3,2);
			$bdateYear = substr($date_birth,6,4);
			if (!checkdate($bdateMonth, $bdateDay, $bdateYear)){
				# invalid birthdate
				$date_birth='';
			}

#			$smarty->assign('sBday',formatDate2Local($zeile['date_birth'],$date_format));   # burn commented: March 27, 2007
			$smarty->assign('sBday',$date_birth);   # burn added: March 27, 2007

			$smarty->assign('sBrgy',$zeile['brgy_name']);   # burn added: March 9, 2007
			$smarty->assign('sMuni',$zeile['mun_name']);   # burn added: March 9, 2007

#			$smarty->assign('sZipCode',$zeile['addr_zip']);   # burn commented: March 9, 2007
#			$smarty->assign('sZipCode',$zeile['zipcode']);   # burn added: March 9, 2007

			#echo "served = ".$zeile['is_served'];
			$dept = $user_dept_info['name_formal'];

			#added by VAN 06-25-08
			#if (($user_dept_info['dept_nr']!=148)&&($user_dept_info['dept_nr']!=149)&&($user_dept_info['dept_nr']!=150)&&($user_dept_info['dept_nr']!=151)){
			#edited by VAN 03-13-09
						#if ((!$allow_ipd_user)&&(!$allow_er_user)&&(!$allow_opd_user)&&(!$allow_medocs_user)){
				#if (!$is_doctor)

			if ($allow_canserve)
					$smarty->assign('sServeOption','<input type="checkbox" '.(($zeile['is_served'])?'checked="checked" ':'').' id="served'.$zeile['encounter_nr'].'" name="served'.$zeile['encounter_nr'].'" value="1" onclick="ToBeServed(this.id, '.$zeile['encounter_nr'].', \''.$dept.'\');">');
			#}
			#-------------------------

			$sTarget = "<a href=\"aufnahme_daten_zeigen.php".URL_APPEND."&from=such&encounter_nr=$full_en&target=search&ptype=$ptype\">";
			$sTarget=$sTarget.'<img '.$img_options.' title="'.$LDShowData.'"></a>';
			$smarty->assign('sOptions',$sTarget);

			if(!file_exists($root_path.'cache/barcodes/en_'.$full_en.'.png')){
				$smarty->assign('sHiddenBarcode',"<img src='".$root_path."classes/barcode/image.php?code=".$full_en."&style=68&type=I25&width=180&height=50&xres=2&font=5&label=2' border=0 width=0 height=0>");
			}
			#
			# Generate the row in buffer and append as string
			#
			ob_start();
				$smarty->display('registration_admission/admit_search_list_row.tpl');
				$sTemp = $sTemp.ob_get_contents();
			ob_end_clean();
		}

		#
		# Assign the rows string to template
		#
		$smarty->assign('sResultListRows',$sTemp);

		$smarty->assign('sPreviousPage',$pagen->makePrevLink($LDPrevious));
		$smarty->assign('sNextPage',$pagen->makeNextLink($LDNext));
	}
}
/*
$smarty->assign('sPostText','<a href="aufnahme_start.php'.URL_APPEND.'&mode=?">'.$LDAdmWantEntry.'</a><br>
	<a href="aufnahme_list.php'.URL_APPEND.'">'.$LDAdmWantArchive.'</a>');
*/
$smarty->assign('sPostText','<a href="aufnahme_list.php'.URL_APPEND.'">'.$LDAdmWantArchive.'</a>');

# Stop buffering, assign contents and display template

$smarty->assign('sMainIncludeFile','registration_admission/admit_search_main.tpl');

$smarty->assign('sMainBlockIncludeFile','registration_admission/admit_plain.tpl');

$smarty->display('common/mainframe.tpl');

?>
