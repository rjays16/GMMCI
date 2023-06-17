<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);

require('./roots.php');
require($root_path.'include/inc_environment_global.php');

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
$lang_tables[]='personell.php';
$lang_tables[]='prompt.php';
$lang_tables[]='products.php';
define('LANG_FILE','aufnahme.php');
$local_user='aufnahme_user';
require($root_path.'include/inc_front_chain_lang.php');


/* If patient nr is invallid jump to registration search module*/
/*if(!isset($pid) || !$pid)
{
	header('Location:patient_register_search.php'.URL_APPEND.'&origin=admit');
	exit;
}
*/
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_insurance.php');
//require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

//$db->debug=TRUE;

$thisfile=basename(__FILE__);
$returnfile=$breakfile;

$newdata=1;

# Default path for fotos. Make sure that this directory exists!
$default_photo_path=$root_path.'fotos/registration';
$photo_filename='nopic';

if(!isset($pid)) $pid=0;
if(!isset($mode)) $mode='';
if(!isset($forcesave)) $forcesave=0;
if(!isset($update)) $update=0;

if(!session_is_registered('sess_pid')) session_register('sess_pid');
if(!session_is_registered('sess_full_pid')) session_register('sess_full_pid');
if(!session_is_registered('sess_en')) session_register('sess_en');
if(!session_is_registered('sess_full_en')) session_register('sess_full_en');
if(!session_is_registered('sess_pnr')) session_register('sess_pnr');
if(!session_is_registered('sess_full_pnr')) session_register('sess_full_pnr');

$patregtable='care_person';  // The table of the patient registration data

//$dbtable='care_encounter'; // The table of admission data

/* Create new person's insurance object */
$pinsure_obj=new PersonInsurance($pid);
/* Get the insurance classes */
$insurance_classes=&$pinsure_obj->getInsuranceClassInfoObject('class_nr,name,LD_var');

/* Create new person object */
$person_obj=new Person($pid);
/* Create personell object */
$personell_obj=new Personell();
if($pid||$personell_nr){
	# Get the patient global configs
				$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
				$glob_obj->getConfig('personell_%');
				$glob_obj->getConfig('person_foto_path');

				# Check whether config path exists, else use default path
				$photo_path = (is_dir($root_path.$GLOBAL_CONFIG['person_foto_path'])) ? $GLOBAL_CONFIG['person_foto_path'] : $default_photo_path;

				if ($pid){
			# Check whether the person is currently admitted. If yes jump to display admission data
		if($mode!='save' && $personell_nr=$personell_obj->Exists($pid)){
			header('Location:personell_register_show.php'.URL_REDIRECT_APPEND.'&personell_nr='.$personell_nr.'&origin=admit&sem=isadmitted&target=personell_reg');
			exit;
		}
		# Get the related insurance data
		$p_insurance=&$pinsure_obj->getPersonInsuranceObject($pid);
		if($p_insurance==FALSE) {
			$insurance_show=TRUE;
		} else {
			if(!$p_insurance->RecordCount()) {
				$insurance_show=TRUE;
			} elseif ($p_insurance->RecordCount()==1){
				$buffer= $p_insurance->FetchRow();
				extract($buffer);
				//while(list($x,$v)=each($buffer)) {$$x=$v; }
				$insurance_show=TRUE;
				$insurance_firm_name=$pinsure_obj->getFirmName($insurance_firm_id);
			} else { $insurance_show=FALSE;}
		}

		$HTTP_POST_VARS['job_position'] = trim($HTTP_POST_VARS['job_position']);
		if(empty($HTTP_POST_VARS['job_position']))
			$HTTP_POST_VARS['job_position'] = " ";
		$HTTP_POST_VARS['license_nr'] = trim($HTTP_POST_VARS['license_nr']);
		if(empty($HTTP_POST_VARS['license_nr']))
			$HTTP_POST_VARS['license_nr'] = " ";
		$HTTP_POST_VARS['phic_nr'] = trim($HTTP_POST_VARS['phic_nr']);
		#if(empty($HTTP_POST_VARS['phic_nr']))
		#	$HTTP_POST_VARS['phic_nr'] = " ";
		$HTTP_POST_VARS['tin'] = trim($HTTP_POST_VARS['tin']);
		if(empty($HTTP_POST_VARS['tin']))
			$HTTP_POST_VARS['tin'] = " ";

		#added by cha, august 17, 2010
		$HTTP_POST_VARS['prescription_license_nr'] = trim($HTTP_POST_VARS['prescription_license_nr']);
		if(empty($HTTP_POST_VARS['prescription_license_nr']))
			$HTTP_POST_VARS['prescription_license_nr'] = " ";
		#end cha
        
        $HTTP_POST_VARS['ptr_nr'] = trim($HTTP_POST_VARS['ptr_nr']);
        if(empty($HTTP_POST_VARS['ptr_nr']))
            $HTTP_POST_VARS['ptr_nr'] = " ";
            
        $HTTP_POST_VARS['s2_nr'] = trim($HTTP_POST_VARS['s2_nr']);
        if(empty($HTTP_POST_VARS['s2_nr']))
            $HTTP_POST_VARS['s2_nr'] = " ";    

		$HTTP_POST_VARS['is_resident_dr'] = trim($HTTP_POST_VARS['is_resident_dr']);
		if(!$HTTP_POST_VARS['is_resident_dr'])
			$HTTP_POST_VARS['is_resident_dr'] = " ";

		#if(!$HTTP_POST_VARS['ward_nr'])
		 # $HTTP_POST_VARS['ward_nr'] = 0;
		if(!$HTTP_POST_VARS['is_reliever'])
			$HTTP_POST_VARS['is_reliever'] = " ";

		$job = substr($short_id,0,1);

		if (($mode=='save')){
			$error=FALSE;
			# Check some values
			if(empty($HTTP_POST_VARS['job_function_title'])
				|| empty($HTTP_POST_VARS['date_join'])
				|| empty($HTTP_POST_VARS['contract_start']))
			{
				$error=TRUE;
			}
			# Get default user if needed
			if(empty($HTTP_POST_VARS['encoder'])) $encoder=$HTTP_SESSION_VARS['sess_user_name'];
			# Start save routine if no error
			if(!$error) {
				if($update || $personell_nr){
					//echo formatDate2STD($geburtsdatum,$date_format);

					$itemno=$itemname;
					if($HTTP_POST_VARS['date_join']) $HTTP_POST_VARS['date_join']=@formatDate2STD($HTTP_POST_VARS['date_join'],$date_format);
					if($HTTP_POST_VARS['date_exit']) $HTTP_POST_VARS['date_exit']=@formatDate2STD($HTTP_POST_VARS['date_exit'],$date_format);
						else $HTTP_POST_VARS['date_exit']= DBF_NODATE;
					if($HTTP_POST_VARS['contract_start']) $HTTP_POST_VARS['contract_start']=@formatDate2STD($HTTP_POST_VARS['contract_start'],$date_format);
					if($HTTP_POST_VARS['contract_end']) $HTTP_POST_VARS['contract_end']=@formatDate2STD($HTTP_POST_VARS['contract_end'],$date_format);
						else $HTTP_POST_VARS['contract_end']= DBF_NODATE;
					#$HTTP_POST_VARS['modify_id']=$encoder;
					$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
					$HTTP_POST_VARS['modify_time']=date('YmdHis');
					$HTTP_POST_VARS['history']= $personell_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$encoder."\n");

					$HTTP_POST_VARS['job_type_nr']=$personell_obj->getJobTypeNumber($HTTP_POST_VARS['job_function_title']); # burn added: Sept. 28, 2006

					# Disable the pid variable
					if(isset($HTTP_POST_VARS['pid'])) unset($HTTP_POST_VARS['pid']);

					if (empty($HTTP_POST_VARS['other_title']))
							$HTTP_POST_VARS['other_title'] = " ";

					#print_r($HTTP_POST_VARS);
					$personell_obj->setDataArray($HTTP_POST_VARS);

					if($personell_obj->updateDataFromInternalArray($personell_nr)){
						#added by VAN 05-22-09
						if ($_POST["items"]!=NULL){
							$bulk_accre = array();
							foreach (array_unique($_POST["items"]) as $i=>$v) {
							#------------------hcare_id, accreditation_nr-----
								 $bulk_accre[] = array($_POST["items"][$i],$_POST["accre_nr"][$i]);
							}

							$personell_obj->clearAccreditationList($personell_nr);    #clear seg_encounter_insurance table
							$personell_obj->addAccreditation($personell_nr,$bulk_accre);
						}

					#-----------
					#added by VAN 05-04-2010
					if (($HTTP_POST_VARS['is_reliever']==0)&&($job=='N')){
							if ($_POST["wardlist"]!=NULL){
								$bulk_ward = array();
								foreach (array_unique($_POST["wardlist"]) as $i=>$v) {
									 $bulk_ward[] = array($_POST["wardlist"][$i]);
								}
							#print_r($bulk_ward);
							$personell_obj->clearWardList($personell_nr);
							$personell_obj->addWard($personell_nr,$bulk_ward);
						}
					}else{
						$personell_obj->clearWardList($personell_nr);
					}
					#-------end VAN 05-04-2010

					header("Location: personell_register_show.php".URL_REDIRECT_APPEND."&personell_nr=$personell_nr&origin=admit&target=personell_reg&newdata=$newdata");
					exit;
				}else{
					$error=TRUE;
				}
		 }else{
					$newdata=1;
					if(!$personell_obj->InitPersonellNrExists($GLOBAL_CONFIG['personell_nr_init'])) $HTTP_POST_VARS['nr']=$GLOBAL_CONFIG['personell_nr_init'];

					if($HTTP_POST_VARS['date_join']) $HTTP_POST_VARS['date_join']=@formatDate2STD($HTTP_POST_VARS['date_join'],$date_format);
					if($HTTP_POST_VARS['date_exit']) $HTTP_POST_VARS['date_exit']=@formatDate2STD($HTTP_POST_VARS['date_exit'],$date_format);
					if($HTTP_POST_VARS['contract_start']) $HTTP_POST_VARS['contract_start']=@formatDate2STD($HTTP_POST_VARS['contract_start'],$date_format);
					if($HTTP_POST_VARS['contract_end']) $HTTP_POST_VARS['contract_end']=@formatDate2STD($HTTP_POST_VARS['contract_end'],$date_format);

					$HTTP_POST_VARS['create_id']=$encoder;
					$HTTP_POST_VARS['create_time']=date('YmdHis');
					$HTTP_POST_VARS['history']="Create: ".date('Y-m-d H:i:s')." = ".$encoder."\n";
										$HTTP_POST_VARS['job_type_nr']=$personell_obj->getJobTypeNumber($HTTP_POST_VARS['job_function_title']); # burn added: Sept. 28, 2006
					$personell_obj->setDataArray($HTTP_POST_VARS);

					if($personell_obj->insertDataFromInternalArray()){
						# Get the PID
						$oid = $db->Insert_ID();
						#$personell_nr = $personell_obj->LastInsertPK('nr',$oid);
						#added by VAN 04-28-08
						$personell_nr = $personell_obj->LastInsertPKPersonell();

						#added by VAN 05-22-09
						if ($_POST["items"]!=NULL){
							 $bulk_accre = array();
							 foreach (array_unique($_POST["items"]) as $i=>$v) {
							 #------------------hcare_id, accreditation_nr-----
									$bulk_accre[] = array($_POST["items"][$i],$_POST["accre_nr"][$i]);
							 }

							 $personell_obj->clearAccreditationList($personell_nr);    #clear seg_encounter_insurance table
							 $personell_obj->addAccreditation($personell_nr,$bulk_accre);
						}

						#-----------

						#-----------
					#added by VAN 05-04-2010
					if (($HTTP_POST_VARS['is_reliever']==0)&&($job=='N')){
							if ($_POST["wardlist"]!=NULL){
								$bulk_ward = array();
								foreach (array_unique($_POST["wardlist"]) as $i=>$v) {
									 $bulk_ward[] = array($_POST["wardlist"][$i]);
								}

							$personell_obj->clearWardList($personell_nr);    #clear seg_encounter_insurance table
							$personell_obj->addWard($personell_nr,$bulk_ward);
						}
					}else{
						$personell_obj->clearWardList($personell_nr);
					}
					#-------end VAN 05-04-2010

						#echo "wait display = ".$personell_obj->sql;
						#header("Location: personell_register_show.php".URL_REDIRECT_APPEND."&personell_nr=$personell_nr&origin=admit&target=personell_reg&newdata=$newdata");
						header("Location: personell_register_show.php".URL_REDIRECT_APPEND."&personell_nr=$personell_nr&origin=admit&target=personell_reg&newdata=$newdata");
						exit;
					}else{
						$error=TRUE;
					}
				} // end of if(update) else()
			} // end of if($error)

			if($error){
				header("Location: $thisfile".URL_REDIRECT_APPEND."&personell_nr=$personell_nr&error=1");
				exit;
			}
		} // end of if($mode)
		else{

			$person_obj->setPID($pid);
			if($data=&$person_obj->BasicDataArray($pid)){
				//while(list($x,$v)=each($data))	$$x=$v;
				extract($data);
			}
			# Get the citytown name
			$addr_citytown_name=$person_obj->CityTownName($addr_citytown_nr);
		}
				} elseif($personell_nr) {
		# Load personnel data
		$personell_obj->loadPersonellData($personell_nr);
		if($personell_obj->is_loaded) {
			$zeile=&$personell_obj->personell_data;
			extract($zeile);
			# Get insurance firm name
			$insurance_firm_name=$pinsure_obj->getFirmName($insurance_firm_id);
			$full_pnr=$personell_nr;
		}
	}
}

# Load the wards info
$ward_obj=new Ward;
$items='nr,name';
$ward_info=&$ward_obj->getAllWardsItemsObject($items);
if($update) $breakfile='personell_register_show.php'.URL_APPEND.'&personell_nr='.$personell_nr;
	elseif($HTTP_COOKIE_VARS['ck_login_logged'.$sid]) $breakfile=$root_path.'main/spediens.php'.URL_APPEND;
		else $breakfile='personell_admin_pass.php'.URL_APPEND.'&target='.$target;

# Prepare the photo filename
require_once($root_path.'include/inc_photo_filename_resolve.php');
require('./gui_bridge/default/gui_'.$thisfile);
?>
