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
define('AUTOSHOW_ONERESULT',1); # Defining to 1 will automatically show the admission data if the search result is one, otherwise the single result will be listed

function Cond($item,$k){
	global $where,$tab,$HTTP_POST_VARS, $sql_LIKE;
	if(empty($HTTP_POST_VARS[$item])) return false;
	else{
		$buf=" $tab.$item $sql_LIKE '%".$HTTP_POST_VARS[$item]."%'";
		if(!empty($where)) $where.=' AND '.$buf;
		 else $where=$buf;
	}
}
	
function fCond($item,$k){
	global $orwhere,$tab,$HTTP_POST_VARS, $sql_LIKE;
	if(empty($HTTP_POST_VARS[$item])) return false;
	else{
		$buf=" f.class_nr $sql_LIKE '".$HTTP_POST_VARS[$item]."%'";
		if(!empty($orwhere)) $orwhere.=' OR '.$buf;
		 else $orwhere=$buf;
	}
}


define('LANG_FILE','aufnahme.php');
$local_user='aufnahme_user';
require($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/inc_date_format_functions.php');

//$db->debug=1;

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
#Load and create paginator object
require_once($root_path.'include/care_api_classes/class_paginator.php');
$pagen=& new Paginator($pgx,$thisfile,$HTTP_SESSION_VARS['sess_searchkey'],$root_path);

$GLOBAL_CONFIG=array();
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);	
# Get the max nr of rows from global config
$glob_obj->getConfig('pagin_person_search_max_block_rows');
if(empty($GLOBAL_CONFIG['pagin_person_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS); # Last resort, use the default defined at the start of this page
	else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_person_search_max_block_rows']);

# burn added: March 9, 2007
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;
#$user_dept_info = $dept_obj->getUserDeptInfo($HTTP_SESSION_VARS['sess_user_name']);
	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	$user_dept_info = $dept_obj->getUserDeptInfo($seg_user_name);


if ($_GET['ptype'])
	$ptype = $_GET['ptype'];
elseif ($HTTP_SESSION_VARS['ptype'])
	$ptype = $HTTP_SESSION_VARS['ptype'];
	
$HTTP_SESSION_VARS['ptype'] = $ptype;

#echo "<br>4 ptype = ".$ptype;
if (($allow_opd_user)&&($ptype=='opd')){
	$encounter_type_search='2';   # search under OPD Triage
}elseif(($allow_er_user)&&($ptype=='er')){
	$encounter_type_search='1';   # search under ER Triage
}elseif(($allow_ipd_user)&&($ptype=='ipd')){	
	$encounter_type_search='3,4';   # search under IPD Triage
}elseif($allow_medocs_user){
	$encounter_type_search='1,2,3,4';   # search under ER Triage
}else{
	$encounter_type_search=0;   # User has no permission to use Admission Search
}

#echo "allow ipd = ".$allow_ipd_user;
#echo "<br>ptype = ".$ptype;
#echo "<br>enctype = ".$encounter_type_search;


if (isset($mode) && ($mode=='search'||$mode=='paginate')){


	#if(empty($oitem)) $oitem='name_last';			
	#if(empty($odir)) $odir='ASC'; # default, ascending alphabetic
	# Set the sort parameters
	$pagen->setSortItem($oitem);
	$pagen->setSortDirection($odir);

	if($mode=='paginate'){
		$sql=$HTTP_SESSION_VARS['sess_searchkey'];
		$where='?'; # Dummy char to force the sql query to be executed
		
		if (!empty($HTTP_SESSION_VARS['ptype']))
			$ptype = $HTTP_SESSION_VARS['ptype'];
	}else{


#		$select="SELECT p.name_last,p.name_first,p.date_birth,p.addr_zip, p.sex,e.encounter_nr,e.encounter_class_nr,e.is_discharged,e.encounter_date FROM ";   # burn commented: March 9, 2007
		$select= " SELECT e.encounter_nr, e.encounter_date, e.encounter_class_nr, e.encounter_type, ".
					" e.admission_dt, e.is_discharged, p.name_last, p.name_first, p.date_birth, p.addr_zip,p.sex ".
					" , (SELECT id FROM care_department AS dept WHERE dept.nr = e.consulting_dept_nr) AS consulting_dept_name ". # burn added: July 12, 2007
					" , (SELECT id FROM care_department AS dept WHERE dept.nr = e.current_dept_nr) AS current_dept_name ". # burn added: July 12, 2007
					" , sb.brgy_name, sm.zipcode, sm.mun_name, sp.prov_name, sr.region_name FROM ";   # edited by VAN 02-22-08
		/*
		$from = " FROM care_encounter as enc,care_person as reg ".
						" , seg_barangays AS sb, seg_municity AS sm, ".
						" seg_provinces AS sp, seg_regions AS sr ";			
		*/
		$where=''; 		# ANDed where condition
		$orwhere='';	# ORed where condition
		$datecond='';	# date condition
	 
		# Walk the arrays in the function to preprocess the search condition data
#		$parray=array('name_last','name_first','sex');   # burn commmented: May 8, 2007
		$parray=array('name_last','name_first','name_2','name_3','name_middle','sex');
		$tab='p';
		array_walk($parray,'Cond');
		$earray=array('encounter_nr','encounter_class_nr','current_ward_nr','referrer_diagnosis','referrer_dr','referrer_recom_therapy','referrer_notes','insurance_class_nr');
		$tab='e';
		array_walk($earray,'Cond');
		$farray=array('sc_care_class_nr','sc_room_class_nr','sc_att_dr_class_nr');
		array_walk($farray,'fCond');
	
		# Process the dates
		 if(isset($date_start)&&!empty($date_start)) $date_start=@formatDate2STD($date_start,$date_format);
		 if(isset($date_end)&&!empty($date_end)) $date_end=@formatDate2STD($date_end,$date_format);
	 	if(isset($date_birth)&&!empty($date_birth)) $date_birth=@formatDate2STD($date_birth,$date_format);
	
		if($date_start){
			if($date_end){
				$datecond="(e.encounter_date $sql_LIKE '$date_start%' OR e.encounter_date>'$date_start') AND (e.encounter_date<'$date_end' OR e.encounter_date $sql_LIKE '$date_end%')";
			}else{
				$datecond="e.encounter_date $sql_LIKE '$date_start%'";
			}
		}elseif($date_end){
			$datecond="(e.encounter_date< '$date_end' OR e.encounter_date $sql_LIKE '$date_end%')";
		}

		if($date_birth){
			$datecond="(p.date_birth $sql_LIKE '$date_birth%')";
		}
	
		if(!empty($datecond)){
			if(empty($where)) $where=$datecond;
			    else $where.=' AND '.$datecond;
		}
			
		if(!empty($orwhere)) {
			if(empty($where)) $where='('.$orwhere.')';
			    else $where.=' AND ('.$orwhere.') ';
		}
	#edited by VAN 01-02-09
#		if($name_last||$name_first||$date_birth||$sex){   # burn commented : May 8, 2007
		if($name_last||$name_first||$name_2||$name_3||$name_middle||$date_birth||$sex){
			if($encounter_nr||$encounter_class_nr||$current_ward_nr||$referrer_diagnosis||$referrer_dr||$referrer_recom_therapy||$referrer_notes||$insurance_class_nr){
				if($sc_care_class_nr||$sc_room_class_nr||$sc_att_dr_class_nr){
	 	 	      	/*
					$from=" care_person AS p, care_encounter AS e, care_encounter_financial_class AS f ";
					$where.=" AND e.encounter_nr=f.encounter_nr AND e.pid=p.pid ";
					*/
					$from=" care_person AS p 
					       INNER JOIN care_encounter AS e ON  e.pid=p.pid 
						   INNER JOIN care_encounter_financial_class AS f ON e.encounter_nr=f.encounter_nr ";
				}else{
					/*
					$from=" care_person AS p, care_encounter AS e";
					$where.=" AND p.pid=e.pid";
					*/
					$from=" care_person AS p
					        INNER JOIN care_encounter AS e ON p.pid=e.pid ";
					
				}
			}else{
				/*
				$from=" care_person AS p, care_encounter AS e";
				$where.=" AND p.pid=e.pid";
				*/
				$from=" care_person AS p
					    INNER JOIN care_encounter AS e ON p.pid=e.pid ";
			}
				
		}else{
			if($date_start||$date_end||$encounter_nr||$encounter_class_nr||$current_ward_nr||$referrer_diagnosis||$referrer_dr||$referrer_recom_therapy||$referrer_notes||$insurance_class_nr){
				if($sc_care_class_nr||$sc_room_class_nr||$sc_att_dr_class_nr){
					/*
					$from=" care_person AS p, care_encounter AS e, care_encounter_financial_class AS f";
					$where.=" AND p.pid=e.pid AND e.encounter_nr=f.encounter_nr";
					*/
					$from=" care_person AS p 
					       INNER JOIN care_encounter AS e ON  e.pid=p.pid 
						   INNER JOIN care_encounter_financial_class AS f ON e.encounter_nr=f.encounter_nr ";
				}else{
					/*
					$from="  care_person AS p, care_encounter AS e";
					$where.=" AND p.pid=e.pid";
					*/
					$from=" care_person AS p
					    INNER JOIN care_encounter AS e ON p.pid=e.pid ";
				}
			}else{
				if($sc_care_class_nr||$sc_room_class_nr||$sc_att_dr_class_nr){
					/*
					$from="  care_person AS p, care_encounter AS e, care_encounter_financial_class as f";
					$where.=" AND p.pid=e.pid AND f.encounter_nr=e.encounter_nr";
					*/
					$from=" care_person AS p 
					       INNER JOIN care_encounter AS e ON  e.pid=p.pid 
						   INNER JOIN care_encounter_financial_class AS f ON e.encounter_nr=f.encounter_nr ";
				}else{
					/*
					$from=" care_person AS p, care_encounter AS e";
					$where.=" p.pid=e.pid";
					*/
					$from=" care_person AS p
					    INNER JOIN care_encounter AS e ON p.pid=e.pid ";
				}
			}
		}
		/*
		$from.=" , seg_barangays AS sb, seg_municity AS sm, seg_provinces AS sp, seg_regions AS sr";   # burn added: March 9, 2007
		if(!empty($where)) {
			$where.=" AND sr.region_nr=sp.region_nr AND sp.prov_nr=sm.prov_nr 
						 AND sm.mun_nr=sb.mun_nr AND sb.brgy_nr=p.brgy_nr 
						 AND e.encounter_type IN ($encounter_type_search)  
						 AND e.is_discharged=0
						 ";   # burn added: March 9, 2007
		*/
		$from.=" LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr 
				 LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
				 LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr 
				 LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr ";   # edited by VAN 01-02-09
		if(!empty($where)) {
			$where.=" AND e.encounter_type IN ($encounter_type_search)  
						 AND e.is_discharged=0
						 ";   # edited by VAN 01-02-09			 
		}
		$sql="$select$from WHERE $where AND e.encounter_status <> 'cancelled' AND e.status NOT IN ('void','inactive','hidden','deleted') ORDER BY ";
		$HTTP_SESSION_VARS['sess_searchkey']=$sql;
	
	}
#echo "afnahme_list.php : A sql = '".$sql."' <br><br> \n";
	
	#added by VAN 02-22-08
	if (empty($oitem))
		$oitem = 'encounter_date';
	if (empty($odir))
		$odir = 'DESC';

	if(!empty($where)) {
		# Filter the encounter nr:
#		if($oitem=='encounter_nr'||$oitem=='encounter_date') $tab='e';   # burn commented: March 10, 2007
#			else $tab='p';   # burn commented: March 10, 2007
		//echo "$sql $tab.$oitem $odir";
#echo "afnahme_list.php : B sql tab odir = '$sql $oitem $odir' <br> \n";
#echo "afnahme_list.php : sql oitem odir = '$sql $oitem $odir' <br><br> \n";
#echo "afnahme_list.php : sql tab.oitem odir = '$sql $tab.$oitem $odir' <br> \n";
#		if($ergebnis=$db->SelectLimit("$sql $tab.$oitem $odir",$pagen->MaxCount(),$pagen->BlockStartIndex())){   # burn commented: March 10, 2007
		if($ergebnis=$db->SelectLimit("$sql $oitem $odir",$pagen->MaxCount(),$pagen->BlockStartIndex())){   # burn added: March 10, 2007
  			$rows=$ergebnis->RecordCount();			
/*
	# burn commented: March 9, 2007
	# reason: header does not work!!!			
			if(AUTOSHOW_ONERESULT){					
	        	if($rows==1){
		      		# If result is single item, display the data immediately 
				   	$result=$ergebnis->FetchRow();
				   	header("Location:aufnahme_daten_zeigen.php".URL_REDIRECT_APPEND."&target=archiv&origin=archiv&encounter_nr=".$result['encounter_nr']);
				   	exit;
	        	}
			}
*/			
			$pagen->setTotalBlockCount($rows);
					
					# If more than one count all available
					if(isset($totalcount)&&$totalcount){
						$pagen->setTotalDataCount($totalcount);
					}else{
						# Count total available data
						#$sql="$sql $tab.$oitem $odir";   # burn commented: March 10, 2007
						$sql="$sql $oitem $odir";   # burn added: March 10, 2007
						
						if($result=$db->Execute($sql)){
							$totalcount=$result->RecordCount();
						}
						$pagen->setTotalDataCount($totalcount);
					}
					# Set the sort parameters
					$pagen->setSortItem($oitem);
					$pagen->setSortDirection($odir);

			
		}else{
			echo "$LDDbNoRead<p>$sql $tab.$oitem $odir";
			$rows=0;
		}
	}
}
require_once($root_path.'include/care_api_classes/class_globalconfig.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$glob_obj->getConfig('patient%');
$glob_obj->getConfig('person%');

$thisfile=basename(__FILE__);

/*---replaced, 2007-10-03 FDP
$breakfile='patient.php';
-----*/
$breakfile=$root_path.'main/startframe.php'.URL_APPEND;
//-------------------

$newdata=1;
$target='archiv';


if(!isset($rows)||!$rows) {

	include($root_path.'include/care_api_classes/class_encounter.php');
	include($root_path.'include/care_api_classes/class_ward.php');
	include_once($root_path.'include/care_api_classes/class_insurance.php');

	# Create encounter object
	$encounter_obj=new Encounter();
	# Load the wards info 
	$ward_obj=new Ward;
	$items='nr,name';
	$ward_info=&$ward_obj->getAllWardsItemsObject($items);
	# Get all encounter classes
	$encounter_classes=$encounter_obj->AllEncounterClassesObject();
	# Get the insurance classes */
	# Create new person´s insurance object */
	$insurance_obj=new Insurance;	 
	$insurance_classes=&$insurance_obj->getInsuranceClassInfoObject('class_nr,LD_var,name');

	if(!$GLOBAL_CONFIG['patient_service_care_hide']){
		# Get the care service classes
		$care_service=$encounter_obj->AllCareServiceClassesObject();
	}
	if(!$GLOBAL_CONFIG['patient_service_room_hide']){
		# Get the room service classes 
		$room_service=$encounter_obj->AllRoomServiceClassesObject();
	}
	if(!$GLOBAL_CONFIG['patient_service_att_dr_hide']){
		# Get the attending doctor service classes 
		$att_dr_service=$encounter_obj->AllAttDrServiceClassesObject();
	}
}
# Load GUI page
require('./gui_bridge/default/gui_aufnahme_list.php');
?>