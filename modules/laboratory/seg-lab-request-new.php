<?php
	# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/

	$lang_tables[] = 'departments.php';
	define('LANG_FILE','lab.php');
	define('LANG_FILE','konsil.php');
	define('NO_2LEVEL_CHK',1);

	$local_user='ck_lab_user';
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/inc_front_chain_lang.php');
	require($root_path.'modules/laboratory/ajax/lab-request-new.common.php');

	$dbtable='care_config_global'; // Taboile name for global configurations
	$GLOBAL_CONFIG=array();
	$new_date_ok=0;

	# Create global config object
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/inc_date_format_functions.php');

	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('refno_%');
	if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
	$date_format=$GLOBAL_CONFIG['date_format'];

	$phpfd=$date_format;
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

	$breakfile=$root_path.'modules/laboratory/labor.php'.URL_APPEND;

	if ($popUp!='1'){
			 # href for the close button
		 #$smarty->assign('breakfile',$breakfile);
	}else{
			# CLOSE button for pop-ups
			#$smarty->assign('breakfile','javascript:window.parent.close_overlib('.$_GET['from_or'].');');
			if (($_GET['view_from']=='ssview') || ($_GET['view_from']=='override'))
				$breakfile = "";
			else
				$breakfile  = "javascript:window.parent.cClick();";
	}

	$title="Laboratory";

	# Create laboratory object
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	$srvObj=new SegLab();

	require_once($root_path.'include/care_api_classes/class_ward.php');
	$ward_obj = new Ward;

	require_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;

	require_once($root_path.'include/care_api_classes/class_social_service.php');
	$objSS = new SocialService;

	require_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj = new Person;

	require_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;

	require($root_path.'include/care_api_classes/class_insurance.php');
	$ins_obj=new PersonInsurance;

	#added by ken 10/10/2014 for requiring class curl
	require_once($root_path.'include/care_api_classes/curl/class_curl.php');
	$curl_obj=new Rest_Curl;

	#added by VAN 02-01-2012
    #for HL7 compliant
    # Create hl7 object
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_create_hl7_message.php');
    $HL7Obj = new seg_create_msg_HL7();
            
    # Create file
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_create_hl7_file.php');
                
    # Create file
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_transport_hl7_file.php');
    
    require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
    $objInfo = new Hospital_Admin();
            
    $details = (object) 'details';
        
    $prefix = "HIS";
    $COMPONENT_SEPARATOR = "^";
    $REPETITION_SEPARATOR = "~";            

    $row_hosp = $objInfo->getAllHospitalInfo();
    
    # Establish db connection
    # Added by VAN 11-18-09
    $connection_type = $row_hosp['connection_type'];
    $HTTP_SESSION_VARS['connection_type'] = $connection_type;
    if ($connection_type=='odbc'){
        require_once($root_path.'include/inc_hclab_connection.php');
        #echo $dblink_hclab_ok;
        
        require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
        $hclabObj = new HCLAB;
    }else{    
    
    
    $row_comp = $objInfo->getSystemCreatorInfo();
    
    $details->protocol_type = $row_hosp['LIS_protocol_type'];
    $details->protocol = $row_hosp['LIS_protocol'];
    $details->address_lis = $row_hosp['LIS_address'];
    $details->address_local = $row_hosp['LIS_address_local'];
    $details->port = $row_hosp['LIS_port'];
    $details->username = $row_hosp['LIS_username'];
    $details->password = $row_hosp['LIS_password'];
    
    $details->folder_LIS = $row_hosp['LIS_folder_path'];
    #LIS SERVER IP
    $details->directory_remote = "\\\\".$details->address_lis.$row_hosp['LIS_folder_path'];
    #HIS SERVER IP
    $details->directory = "\\\\".$details->address_local.$row_hosp['LIS_folder_path'];
    #HIS SERVER IP
    $details->directory_local = "\\\\".$details->address_local.$row_hosp['LIS_folder_path_local'];
    $details->extension = $row_hosp['LIS_HL7_extension'];
    $details->service_timeout = $row_hosp['service_timeout'];    #timeout in seconds
    $details->directory_LIS = "\\\\".$details->address_lis.$row_hosp['LIS_folder_path_inbox'];
    $details->hl7extension = ".".$row_hosp['LIS_HL7_extension'];
    
    #if ($details->protocol_type=='tcp')
    #    $transfer_method = 'SOCKET';
    #else    
    #    $transfer_method = 'NFS';
        
    $transfer_method = $details->protocol_type;    
    
    #msh
    $details->system_name = trim($row_comp['system_id']);
    $details->hosp_id = trim($row_hosp['hosp_id']);
    $details->lis_name = trim($row_comp['lis_name']);
    $details->currenttime = strftime("%Y%m%d%H%M%S");
    }    
    
    #----------------------

	#added by VAN 06-03-2011
	require_once($root_path.'include/care_api_classes/class_workaround.php');
	$srvTempObj=new SegTempWorkaround();

	#global $db, $db_hclab, $dblink_hclab_ok, $allow_labrepeat;
        global $db, $allow_labrepeat;
        
	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');

	#echo "IP ADDRESS = ".$_SERVER['REMOTE_ADDR'];
	#get client IP address and check if unit in ER LAB = seg_lab_er_ip
	$isERIP = $srvObj->isIPinERLab($_SERVER['REMOTE_ADDR']);

	if ($isERIP)
		$smarty->assign("sWARNERLAB","<em><font color='RED'><strong>&nbsp;".$LDERLabCaption."</strong></font></em>");

	if (!isset($popUp) || !$popUp){
		if (isset($_GET['popUp']) && $_GET['popUp']){
			$popUp = $_GET['popUp'];
		}
		if (isset($_POST['popUp']) && $_POST['popUp']){
			$popUp = $_POST['popUp'];
		}
	}

	if ($_GET['viewonly'])
		$viewonly = $_GET['viewonly'];

	# added by VAN 01-11-08

	if ($_GET['repeat'])
		$repeat = $_GET['repeat'];
	else
		$repeat = $_POST['repeat'];

	#added by VAN 05-26-2011
	#to get the personnel nr of the doctor if the user is a doctor
		if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];

	$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);

	$personell_nr = $dept_belong['personell_nr'];

	if (stristr($dept_belong['job_function_title'],'doctor')===FALSE)
		$is_doctor = 0;
	else
		$is_doctor = 1;

	#==================


	if ($_GET['is_dr'])
	$is_dr = $_GET['is_dr'];
	else
		$is_dr = $is_doctor;

	#echo "<br>get repeat = ".$repeat."<br>";

	#if ($_GET['prevbatchnr'])
	#	$prevbatchnr = $_GET['prevbatchnr'];

	if ($_GET['prevrefno']){
		$prevrefno = $_GET['prevrefno'];
        $sql_prev = "SELECT encounter_nr FROM seg_lab_serv WHERE refno='".$prevrefno."'";
		$rs_prev = $db->Execute($sql_prev);
		$row_prev = $rs_prev->FetchRow();
		$encounter_nr = $row_prev['encounter_nr'];

	}

	$dr_nr = $_GET['dr_nr'];
	$dept_nr = $_GET['dept_nr'];

	#added by VAN 03-19-08
	$repeaterror = $_GET['repeaterror'];

	#added by VAN 06-25-08
	$discountid_get = $_GET['discountid'];

	if ($_GET['encounter_nr'])
		$encounter_nr = $_GET['encounter_nr'];

	if ($_GET['area'])
		$area = $_GET['area'];

	if ($_GET['pid'])
		$pid = $_GET['pid'];

	if ($_GET['ref'])
		$refno=$_GET['ref'];

	if ($_GET['user_origin'])
		$user_origin = $_GET['user_origin'];

	if ($_GET['area_type'])
		$area_type = 	$_GET['area_type'];

	if ($_GET['is_rdu'])
		$is_rdu = 	$_GET['is_rdu'];

	if ($_GET['ischecklist'])
		$ischecklist = 	$_GET['ischecklist'];

	$smarty->assign('breakfile',$breakfile);
	$smarty->assign('pbBack','');

	if ($repeaterror){
		$smarty->assign('sysErrorMessage','<strong>Error:</strong> Sorry but you are not allowed to do a repeat request!');
	}

	if ($encounter_nr){
		$patient = $enc_obj->getEncounterInfo($encounter_nr);
		#echo "enc = ".$enc_obj->sql;
	}else if($pid){
		$patient = $person_obj->getAllInfoArray($pid);
		#echo "pid = ".$enc_obj->sql;
	}

    #get encounter info
    $billinfo = $enc_obj->hasSavedBilling($encounter_nr);
    if ($billinfo){
        $bill_nr = $billinfo['bill_nr'];
        $hasfinal_bill = $billinfo['is_final'];
        $is_maygohome = $patient['is_maygohome'];
    }
    
    $warningCaption = '';
    /*if (($bill_nr)||($is_maygohome)){
       if (($bill_nr)&&($is_maygohome)) 
            $warningCaption = "This patient has a saved billing and already advised to go home...";
       elseif (($bill_nr)&&!($is_maygohome)) 
            $warningCaption = "This patient has a saved billing...";     
       elseif (!($bill_nr)&&($is_maygohome)) 
            $warningCaption = "This patient is already advised to go home...";
    }*/
    if (($bill_nr)&&($is_maygohome)) 
        $warningCaption = "This patient has a saved billing and already advised to go home...";
    
    $smarty->assign("sWarning","<em><font color='RED'><strong>&nbsp;<span id='warningcaption'>".$warningCaption."</span></strong></font></em>"); 

	if ((($encounter_nr)||($pid))&&(!$refno)){
			$discountid = $patient['discountid'];
			$discount = $patient['discount'];
	}

			if ($patient['name_middle']){
				$name_middle = mb_strtoupper(substr($patient['name_middle'],0,1));

				if ($name_middle)
					$name_middle = " ".$name_middle.".";
				else
					$name_middle = "";
			}

		 $person_name = mb_strtoupper($patient['name_last']).", ".mb_strtoupper($patient['name_first']).$name_middle;
		 $patient_name = mb_strtoupper(trim($patient['name_last'])).", ".mb_strtoupper(trim($patient['name_first']))." ".mb_strtoupper(trim($patient['name_middle']));

		 if (trim($person_name)==',')
				$person_name = "";

		 if ($patient['street_name']=='NOT PROVIDED')
				$street_name = "";
		 else
				$street_name = $patient['street_name'];
		 if ($patient['brgy_name']=='NOT PROVIDED')
				$brgy_name = "";
		 else
				$brgy_name = $patient['brgy_name'];

		 $mun_name = $patient['mun_name'];

		 $addr = implode(", ",array_filter(array($street_name, $brgy_name, $mun_name)));
		 if ($zipcode)
			$addr.=" ".$zipcode;
		 if ($prov_name)
			$addr.=" ".$prov_name;

		 $orderaddress = trim($addr);

		 if (($patient["admission_dt"])&&(($patient["admission_dt"]!='0000-00-00 00:00:00')||(empty($patient["admission_dt"]))))
				$admission_dt = date("m/d/Y h:i A",strtotime($patient['admission_dt']));
		 else
				$admission_dt = "";

		 if (($patient["discharge_date"])&&(($patient["discharge_date"]!='0000-00-00')||(empty($patient["discharge_date"]))))
				$discharge_date = date("m/d/Y h:i A",strtotime($patient['discharge_date']));
		 else
				$discharge_date = "";

		if ($patient['date_birth']!='0000-00-00'){
			$dob = date("Y-m-d",strtotime($patient['date_birth']));
			$time_bod = strtotime($patient["date_birth"]);
			$patient_bdate = date("n/j/Y",$time_bod);
		}else{
			$dob = "unknown";
			$patient_bdate = "00/00/0000";
		}




    if (($pid == "") || (strlen($pid) < 6))
{
            $dob = "unknown";
            $patient_bdate = "00/00/0000";
}

		if ($patient['sex']=='f'){
			$gender = "Female";
			$sex = 2;
		}elseif ($patient['sex']=='m'){
			$gender = "Male";
			$sex = 1;
		}else{
			$gender = "unknown";
			$sex = 0;
		}
		$_POST['sex'] = $sex;

		if ($patient['age'])
			$age = $patient['age'];
		else
			$age = "unknown";
	#}

	$current_att_dr_nr = $patient['current_att_dr_nr'];
	$current_dept_nr = $patient['current_dept_nr'];

	if (($patient['encounter_type']==2)||($patient['encounter_type']==1))
		$impression = $patient['chief_complaint'];
	elseif (($patient['encounter_type']==3)||($patient['encounter_type']==4))
		$impression = $patient['er_opd_diagnosis'];

	#added by VAN 03-09-2011
	if (!$impression) {
		$impression = '';

		$impression = $enc_obj->getLatestImpression($patient['pid'], $patient['encounter_nr']);

	}

	$_POST['serv_tm'] = date('H:i:s',strtotime($_POST['orderdate']));

	$_POST['is_tpl'] = '0';
	$_POST['fromBB'] = 0;

	#ref_source of laboratory
	#$_POST['grant_type'] = NULL;
	$_POST['ref_source'] = 'LB';

	if ($_GET['ptype'])
		$ptype = $_GET['ptype'];

	$is_rdu = 0;

	/**
	* Edit: values for request sources retrieved from class_request_source for manageability
	* Modified by Alvin (08-25-2010)
	*/
	require_once $root_path.'include/care_api_classes/class_request_source.php';
	switch ($ptype){
		case 'er' :
			$source_req = SegRequestSource::getSourceERClinics();
			break;
		case 'ipd' :
			$source_req = SegRequestSource::getSourceIPDClinics();
			break;
		case 'opd' :
			$source_req = SegRequestSource::getSourceOPDClinics();
			break;
		case 'phs' :
			$source_req = SegRequestSource::getSourcePHSClinics();
			break;
		case 'nursing' :
			$source_req = SegRequestSource::getSourceNursingWard();
			break;
		case 'bb' :
			$source_req = SegRequestSource::getSourceBloodBank();
			break;
		case 'spl' :
			$source_req = SegRequestSource::getSourceSpecialLab();
			break;
		case 'iclab' :
		case 'ic' :
			$source_req = SegRequestSource::getSourceIndustrialClinic();
			$sql_ic = "SELECT c.*, t.*
										FROM seg_industrial_transaction AS t
										LEFT JOIN seg_industrial_company AS c ON c.company_id=t.agency_id
										WHERE encounter_nr='".$encounter_nr."'";
			$rs_ic = $db->Execute($sql_ic);
			$row_ic = $rs_ic->FetchRow();
			$is_charge2comp = $row_ic['agency_charged'];
			$compID = $row_ic['agency_id'];
			$compName = $row_ic['name'];
			$discountid = "";
			$discount = 0;
			break;
		case 'or' :
			$source_req = SegRequestSource::getSourceOR();;
			break;
		case 'rdu' :
			$source_req = SegRequestSource::getSourceDialysis();;
			$is_rdu = 1;
			break;
		case 'doctor' :
			$source_req = SegRequestSource::getSourceDoctor();;
			break;
		default :
			$source_req = SegRequestSource::getSourceLaboratory();
			break;
	}

	#$_POST["source_req"] = $source_req;

	if (empty($area_type))
		$_POST["area_type"] = NULL;

    #added by VAN 06-04-2012
    #if request is cash transaction, ignore the grant type
    if (!$_POST['iscash']){
 $_POST['request_flag'] = $_POST['grant_type'];
    }else{
        $_POST['grant_type'] = NULL;
    }    

	if (empty($_POST['request_flag']))
			$_POST['request_flag'] = NULL;

	if($_POST['hasPaid']){
		$_POST['request_flag'] = 'paid';
	}
	// for LIS header and details
		#$_POST['hclab_order'] = $GLOBAL_CONFIG['refno_hclab_init'];
		#$new_order_no = $srvObj->getOrderLastNr("'".$GLOBAL_CONFIG['refno_hclab_init']."'");
		#$_POST['new_order_no'] = $new_order_no;

		if ($patient['encounter_type']){
			$_POST['ptype'] = $patient['encounter_type'];
			$encounter_type = $patient['encounter_type'];
		}

		switch ($_POST['ptype']){
			case '1' :  $enctype = "ER PATIENT";
									$patient_type = "ER";
									$loc_code = "ER";
									$loc_name = "ER";
									break;
			case '2' :
									$enctype = "OUTPATIENT";
									$patient_type = "OP";
									$loc_code = $patient['current_dept_nr'];
									if ($loc_code)
										$dept = $dept_obj->getDeptAllInfo($loc_code);

									$loc_name = stripslashes($dept['name_formal']);
                                    
                                    #if current dept is a sub department of another department
                                    #if (!($dept['parent_dept_nr'])){
                                    #added by VAN 12-29-2011
                                    $rsdetails = $srvObj->getTestRequest($_POST['refno']);
                                        
                                        #check if the requesting dept is the parent dept of the current dept
                                        if ($dept['parent_dept_nr']!=$rsdetails['request_dept']){
                                    $loc_code2 = $rsdetails['request_dept'];
                                    if ($loc_code2)
                                        $dept2 = $dept_obj->getDeptAllInfo($loc_code2);

                                    $loc_name2 = stripslashes($dept2['name_formal']);
                                    }    
                                    #}    
                                    #---------
                                    
									break;
			case '3' :  $enctype = "INPATIENT (ER)";
									$patient_type = "IN";
									$loc_code = $patient['current_ward_nr'];
									if ($loc_code)
										$ward = $ward_obj->getWardInfo($loc_code);

									$loc_name = stripslashes($ward['name']);
									break;
			case '4' :
									$enctype = "INPATIENT (OPD)";
									$patient_type = "IN";
									$loc_code = $patient['current_ward_nr'];
									if ($loc_code)
										$ward = $ward_obj->getWardInfo($loc_code);

									$loc_name = stripslashes($ward['name']);
									break;
			case '5' :
									$enctype = "RDU";
									$patient_type = "RDU";
									$loc_code = "RDU";
									$loc_name = "RDU";
									break;
			case '6' :
									$enctype = "INDUSTRIAL CLINIC";
									$patient_type = "IC";
									$loc_code = "IC";
									$loc_name = "INDUSTRIAL CLINIC";
									break;
			default :
									$enctype = "WALK-IN";
									$patient_type = "WN";  #Walk-in
									$loc_code = "WIN";
									$loc_name = "WIN";
									break;
		}

        if (!$loc_code2)
            $loc_code2 = $loc_code;
            
        if (!$loc_name2)
            $loc_name2 = $loc_name;    
        
        #if walkin is checked
        if ($is_walkin){
            $loc_code2 = 'WIN';
            $loc_name2 = 'WIN'; 
            $patient_type = "WN";  
        }        
        
        #if from RDU
        $is_rdu = $HTTP_POST_VARS['is_rdu'];
        if (($is_rdu)||($loc_code2=='144')){
            $loc_code2 = 'DIAL';
            $loc_name2 = 'DIALYSIS';
            $patient_type = "IN"; 
        }        
        
        #echo "walkin".$_POST['is_walkin'];
        #echo "<br>loc = ".$loc_code."  >>> ".$loc_code2;
        #echo "<br>locname = ".$loc_name."  >>> ".$loc_name2."<br>";
		$location = $loc_name;
		$is_medico = $patient['is_medico'];

		$_POST['patient_type'] = $patient_type;
		$_POST['loc_code'] = $loc_code;
		$_POST['loc_name'] = $loc_name;
		$_POST['patient_bdate'] = date("n/j/Y",strtotime($_POST["date_birth"]));
		$_POST['patient_name'] = $_POST['ordername'];

		#-------------------------------------LIS ------------------------

		#added by VAN 08-02-2010
		if ($_POST["items"]!=NULL){
			 $arrayMonitorItems = array();
			 $arraySampleItems = array();
			 $arrayLISItems = array();
			 $arrayItemsList = array();
			 $with_monitor = 0;
			 $with_sample = 0;
			 $with_LIS = 0;

			 foreach ($_POST["items"] as $i=>$v) {
					#for monitoring
					$monitorid = 'monitor'.$_POST["items"][$i];
					$qhrid = 'hour'.$_POST["items"][$i];
					$takeid = 'numtake'.$_POST["items"][$i];

					if ($_POST[$monitorid]){
						$arrayMonitorItems[] = array($_POST["items"][$i], $_POST[$qhrid]);
						$with_monitor =+ 1;
					}
					#---------- for monitoring

					#with sample
					$sampleid = 'withsampleID'.$_POST["items"][$i];
					$sampleid = str_replace(".","_",$sampleid);

					$LISid = 'inLIS'.$_POST["items"][$i];
                    $LISid = str_replace(".","_",$LISid);
                    
					$oservice_code = 'oservice_code'.$_POST["items"][$i];
                    $oservice_code = str_replace(".","_",$oservice_code);
                    
                    $ipdservice_code = 'ipdservice_code'.$_POST["items"][$i];
					$ipdservice_code = str_replace(".","_",$ipdservice_code);
                    
					$nameitems = 'nameitems'.$_POST["items"][$i];
                    $nameitems = str_replace(".","_",$nameitems);
                    
					if ($_POST[$sampleid]){
						$arraySampleItems[] = $_POST["items"][$i];
						$with_sample =+ 1;
                        #print_r($arraySampleItems);

						if($_POST[$LISid]){
							#$arrayLISItems[] = $_POST["items"][$i];
							if (($_POST['ptype']==2) || (!$_POST['ptype']))
								$service_code = $_POST[$oservice_code];
							else
								#$service_code = $_POST["items"][$i];
                                $service_code = $_POST[$ipdservice_code];

                            #if request is for dialysis
                            if (($is_rdu)||($loc_code2=='144'))    
                                $service_code = $_POST[$ipdservice_code];
                            
							$arrayLISItems[] = array($service_code, $_POST[$nameitems]," ");
							$with_lis =+ 1;
						}

						#$status = 'done';
                        $status = 'pending';
						$is_served = 1;
						$clerk = $HTTP_SESSION_VARS['sess_user_name'];
						$date_served = date("Y-m-d H:i:s");
					}else{
						$status = 'pending';
						$is_served = 0;
						$clerk = "";
						$date_served = "0000-00-00 00:00:00";
					}
					#--------------- with sample

					#check if with sample or not, if with sample request is consider as DONE
					$arrayItemsList[] = array($status, $is_served, $date_served, $clerk, $date_served, $_POST["items"][$i]);

			 }
			 $_POST['arrayMonitorItems'] = $arrayMonitorItems;
			 $_POST['with_monitor'] = $with_monitor;

			 $_POST['arraySampleItems'] = $arraySampleItems;
			 $_POST['with_sample'] = $with_sample;

			 $_POST['arrayLISItems'] = $arrayLISItems;
			 $_POST['with_lis'] = $with_lis;

			 $_POST['arrayItemsList'] = $arrayItemsList;
		}

		// LIS header
		if ($with_lis){

				$order_date = date("n/j/Y",strtotime($_POST['orderdate']))." ".date("g:i:s A",strtotime($_POST['orderdate']));
				$order_date_trx = date("n/j/Y")." ".date("g:i:s A");

				if ($is_urgent){
					$priority = "U";
				}else{
					$priority = "R";
				}

				$trx_ID = "N";    # new order
				$trx_status = "N";   # before read by LIS default value

                if ($connection_type=='odbc'){
				    
                    $data_HCLAB = array(
												'POH_TRX_NUM'		=>	$refno,
												'POH_TRX_DT'		=>	$order_date,
												'POH_TRX_ID'		=>	$trx_ID,
												'POH_TRX_STATUS'=>	$trx_status,
												'POH_ORDER_DT'	=>	$order_date_trx,
												#'POH_LOC_CODE'	=>	mb_strtoupper($loc_code),
												#'POH_LOC_NAME'	=>	mb_strtoupper($loc_name),
                                                'POH_LOC_CODE'    =>    mb_strtoupper($loc_code2),
                                                'POH_LOC_NAME'    =>    mb_strtoupper($loc_name2),
												'POH_DR_CODE'		=>	$_POST['requestDoc'][0],
												'POH_DR_NAME'		=>	addslashes(mb_strtoupper($_POST['requestDocName'][0])),
												'POH_PAT_ID'		=>	$pid,
												'POH_PAT_NAME'	=>	$patient_name,
												'POH_PAT_TYPE'	=>	mb_strtoupper($patient_type),
												'POH_PAT_ALTID'	=>	" ",
												'POH_PAT_DOB'		=>	$patient_bdate,
												'POH_PAT_SEX'		=>	$sex,
												'POH_PAT_CASENO'=>	$encounter_nr,
												'POH_CLI_INFO'	=>	addslashes(mb_strtoupper($_POST['clinicInfo'][0])),
												'POH_PRIORITY'	=>	$priority
											);
			#print_r($data_HCLAB);
			        $_POST['data_HCLAB'] = $data_HCLAB;
                }
            
		}

		#---------------

		#added by VAN 06-02-2011
		if ($_POST['for_manual_payment']){
				$manual_data['service_area'] = 'LB';
				$manual_data['control_no'] = $_POST['manual_control_no'];
				$manual_data['approved_by'] = $_POST['manual_approved'];
				$manual_data['type'] = $_POST['for_manual_type'];
				$manual_data['reason'] = $_POST['manual_reason'];

				/*if ($mode=='save')
					$history_label = $srvObj->ConcatHistory("Create: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");
				elseif ($mode=='update')
					$history_label = $srvObj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");

				$manual_data['history'] = $history_label;*/
				$manual_data['create_id'] = $HTTP_SESSION_VARS['sess_user_name'];
				$manual_data['create_date'] = date("Y-m-d H:i:s");
				$manual_data['modify_id'] = $HTTP_SESSION_VARS['sess_user_name'];
				$manual_data['modify_date'] = date("Y-m-d H:i:s");
				$_POST['request_flag'] = $_POST['for_manual_type'];
		}else{
				$manual_data['service_area'] = 'LB';
				$manual_data['history'] = $srvObj->ConcatHistory("Deleted: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");
				$manual_data['modify_id'] = $HTTP_SESSION_VARS['sess_user_name'];
				$manual_data['modify_date'] = date("Y-m-d H:i:s");
		}

 $success = 0;       
 switch($mode){
		case 'save':
				if(trim($_POST['orderdate'])!=""){
					$_POST['serv_dt'] = date("Ymd",strtotime($_POST['orderdate']));
				}
				//$_POST[$sampleid], $donestatus, $_POST[$monitorid],$_POST["request_flag"][$i]
				$_POST['loc_code'] = $loc_code;
				$_POST['clinical_info'] = $_POST['clinicInfo'];
				$_POST['request_doctor'] = $_POST['requestDoc'];
				$_POST['request_dept'] = $_POST["requestDept"];
				$_POST['is_in_house'] = $_POST['isInHouse'];
				$_POST['service_code'] = $_POST['items'];
				$_POST['is_cash'] = $_POST['iscash'];
				$_POST['is_urgent'] = $_POST['priority'];
				$_POST['quantity'] = $_POST['qty'];
				#$_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
                $_POST['encoder'] = $_SESSION['sess_temp_userid'];
				#$_POST['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
                $_POST['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n";

				$_POST['refno'] = $srvObj->getLastRefno();
                
				if ((isset($POST['repeat']))&&($_POST['repeat'])){
					#-------added by VAN 01-11-08-------------
					#$_POST['parent_batch_nr'] = $_POST['parent_batch_nr'];
					$_POST['parent_refno'] = $_POST['parent_refno'];
					$_POST['approved_by_head'] = $_POST['approved_by_head'];
					$_POST['remarks'] = $_POST['remarks'];
					$_POST['is_cash'] = 1;

					#added by VAN 03-19-08
					$_POST['headID'] = $_POST['headID'];
					$_POST['headpasswd'] = $_POST['headpasswd'];

					#-----------------------------------------

					$srvObj->getStaffInfo($_POST['headID'],$_POST['headpasswd']);
					#echo "<br>sql = ".$srvObj->sql;
					$isCorrectInfo = $srvObj->count;
					#echo "<br>count = ".$isCorrectInfo;
					if (($isCorrectInfo)||($allow_radiorepeat)){
						#echo "<br>sulod save radio ";
						$srvObj->startTrans(); 
						if($refno = $srvObj->saveLabRefNoInfoFromArray($_POST)){
                            $saveok=$srvObj->update_LabRefno_Tracker($_POST['refno']);
							#$smarty->assign('sysInfoMessage',"Laboratory Request Service successfully created.");
						}#else{
							#$smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$srvObj->sql);
					    #}
					}else{
						header("Location: ".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_test&user_origin=lab&popUp=1&repeat=1&prevbatchnr=".$_POST['parent_batch_nr']."&prevrefno=".$_POST['parent_refno']."&repeaterror=1");
						exit;
					}
                    
                    if (!$saveok){ 
                        $srvObj->FailTrans();
                        $smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$srvObj->sql);
                    }else{    
                        $srvObj->CompleteTrans();
                        $smarty->assign('sysInfoMessage',"Laboratory Request Service successfully created.");
                    }
				}else{

                    $srvObj->startTrans();
                    
					if($refno = $srvObj->saveLabRefNoInfoFromArray($_POST)){
                        $success = 1;
						$saveok=$srvObj->update_LabRefno_Tracker($_POST['refno']);

						#added by VAN 06-02-2011
						if ($_POST['for_manual_payment'])
							$srvTempObj->save_ManualPayment($_POST['refno'], $manual_data);
						else{
							$srvTempObj->ManualPayment($_POST['refno'], $manual_data);
							$srvTempObj->resetRequestFlag($_POST['refno'], $manual_data, 'seg_lab_servdetails');
						}

						#$smarty->assign('sysInfoMessage',"Laboratory Request Service successfully created.");
					}#else{
						#$smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$srvObj->sql);
					#}
                    
                    if (!$saveok){ 
                        $srvObj->FailTrans();
						$smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$srvObj->sql);
                    }else{    
                    	//  if (ENABLE_FIS){
	                    // 	#added by ken 10/10/2014 for passing the data in FIS in adding a data with sample in charge trans
	                    // 	if($_POST['is_cash'] != '1'){
	                    // 		$curl_obj->inpatientLabItem($_POST['refno']);
	                    // 	}
                   	 // }

                        $srvObj->CompleteTrans();
                        $smarty->assign('sysInfoMessage',"Laboratory Request Service successfully created.");
					}
				}

				break;
		case 'update':
				if(trim($_POST['orderdate'])!=""){
					$_POST['serv_dt'] = date("Ymd",strtotime($_POST['orderdate']));
				}

				$_POST['clinical_info'] = $_POST['clinicInfo'];
				$_POST['request_doctor'] = $_POST['requestDoc'];
				$_POST['request_dept'] = $_POST["requestDept"];
				$_POST['is_in_house'] = $_POST['isInHouse'];
				$_POST['service_code'] = $_POST['items'];
				$_POST['is_cash'] = $_POST['iscash'];
				$_POST['is_urgent'] = $_POST['priority'];
				$_POST['quantity'] = $_POST['qty'];
				#$_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
				$_POST['encoder'] = $_SESSION['sess_temp_userid'];
                $_POST['modify_id'] = $_SESSION['sess_temp_userid'];
                #$_POST['history'] = $srvObj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");
                $_POST['history'] = $srvObj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_temp_userid']."\n");

				if ((isset($POST['repeat']))&&($_POST['repeat'])){
					#-------added by VAN 01-11-08-------------
					$_POST['parent_batch_nr'] = $_POST['parent_batch_nr'];
					$_POST['parent_refno'] = $_POST['parent_refno'];
					$_POST['approved_by_head'] = $_POST['approved_by_head'];
					$_POST['remarks'] = $_POST['remarks'];
					$_POST['is_cash'] = 1;
					#added by VAN 03-19-08
					$_POST['headID'] = $_POST['headID'];
					$_POST['headpasswd'] = $_POST['headpasswd'];

				#-----------------------------------------

					$srvObj->getStaffInfo($_POST['headID'],$_POST['headpasswd']);
					#echo "<br>sql = ".$srvObj->sql;
					$isCorrectInfo = $srvObj->count;
					#echo "<br>count = ".$isCorrectInfo;
					if (($isCorrectInfo)||($allow_radiorepeat)){
						if($srvObj->updateLabRefNoInfoFromArray($_POST)){
							//$reloadParentWindow='<script language="javascript">'.
//								'	window.parent.jsOnClick(); '.
//								'</script>';

							$smarty->assign('sysInfoMessage',"Laboratory Request Service successfully updated.");
						}else{
							$errorMsg='<font style="color:#FF0000">'.$srvObj->getErrorMsg().'</font>';
						}
					}else{
						header("Location: ".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_test&user_origin=lab&popUp=1&repeat=1&prevbatchnr=".$_POST['parent_batch_nr']."&prevrefno=".$_POST['parent_refno']."&repeaterror=1");
						exit;
					}
				}else{

					if($srvObj->updateLabRefNoInfoFromArray($_POST)){
                            $success = 1;
                            
							#$reloadParentWindow='<script language="javascript">'.
							#		'	window.parent.jsOnClick(); '.
							#		'</script>';

							#added by VAN 06-02-2011
							if ($_POST['for_manual_payment'])
								$saveok=$srvTempObj->save_ManualPayment($_POST['refno'], $manual_data);
							else{
								$saveok=$srvTempObj->ManualPayment($_POST['refno'], $manual_data);
								$saveok=$srvTempObj->resetRequestFlag($_POST['refno'], $manual_data, 'seg_lab_servdetails');
							}

							#added by ken 10/10/2014 for passing the data in FIS when updating the lab with charge trans
							$this_is_cash = $curl_obj->checkIsCashLab($_POST['refno']);

							// if($this_is_cash != '1'){
                    		   // $curl_obj->inpatientLabItem($_POST);
                    		// }

							$smarty->assign('sysInfoMessage',"Laboratory Service successfully updated.");
						}else{
							$errorMsg='<font style="color:#FF0000">'.$srvObj->getErrorMsg().'</font>';
					}
				}

				break;
		case 'cancel':
				if($srvObj->deleteRefNo($_POST['refno'])){
					header('Location: '.$breakfile);
					exit;
				}else{
					$errorMsg='<font style="color:#FF0000">'.$srvObj->getErrorMsg().'</font>';
				}
				break;
	}# end of switch stmt

    #added by VAN 02-01-2012
    # for HL7 compliant
    #if successfully saved or updated
    //if ($connection_type=='hl7'){
    if ($success){
//Commented By Jarel Not Applicable for Manual Result
//          $fileObj = new seg_create_HL7_file($details);
//          #print_r($arrayLISItems);
//      #check if there request item to be posted to LIS
//      if (count($arrayLISItems)){
//        #get the existing msg control id if there is any
//        $hl7msg_row = $srvObj->isforReplaceHL7Msg($refno,'RP');
//        #echo $srvObj->sql."<br>";
//        if ($hl7msg_row['msg_control_id']){
//            $msg_control_id = $hl7msg_row['msg_control_id'];
//            $forreplace = 1;
//        }else
//            $msg_control_id = $srvObj->getLastMsgControlID();
//
//        #echo "he = ".$msg_control_id." == ".$forreplace;
//        $details->msg_control_id_db = $msg_control_id;
//        $details->msg_control_id = $prefix.$msg_control_id;
//
//        # Observation order - event O01
//        $msg_type = "ORM";
//        $event_id = "O01";
//        $hl7_msg_type = $msg_type.$COMPONENT_SEPARATOR.$event_id;
//        $details->msg_type = $hl7_msg_type;
//
//        #pid
//        $details->POH_PAT_ID = trim($pid);
//        $details->POH_PAT_ALTID = "";
//            $details->patient_name = mb_strtoupper(trim($patient['name_first'])).$COMPONENT_SEPARATOR.mb_strtoupper(trim($patient['name_last']));
//            $details->POH_MIDDLENAME =mb_strtoupper(trim($patient['name_middle']));
//        $details->POH_PAT_DOB = date("YmdHis",strtotime($patient['date_birth']));
//        $details->POH_PAT_SEX = trim(strtoupper($patient['sex']));
//
//        $details->address = trim($street_name).$COMPONENT_SEPARATOR.trim($brgy_name).$COMPONENT_SEPARATOR.trim($mun_name).$COMPONENT_SEPARATOR.trim($prov_name).$COMPONENT_SEPARATOR.trim($zipcode);
//        $details->POH_CIVIL_STAT = trim(strtoupper($patient['civil_status']));
//
//        #pv1
//        $details->setID = "1";
//        $details->POH_PAT_TYPE = mb_strtoupper($patient_type);
//        #ward, room and bed
//
//            $details->location = mb_strtoupper($loc_code2).$COMPONENT_SEPARATOR.mb_strtoupper($loc_name2);
//            #echo "here = ".$details->location;
//            #$row_px = $srvObj->getPatientLocation($encounter_nr);
//            #$details->location = $row_px['current_dept_nr'].$COMPONENT_SEPARATOR.$row_px['location'];
//        $details->requesting_doc =  $_POST['requestDoc'][0].$COMPONENT_SEPARATOR.addslashes(mb_strtoupper($_POST['requestDocName'][0]));
//
//
//        $details->POH_PAT_CASENO = trim($encounter_nr);
//
//        #orc
//        # NW = New Order
//        # RP = Order Replacement
//        # CA = Cancel Order
//
//        if ($mode=='save'){
//            $order_control = "NW";
//        }elseif ($mode=='update'){
//                if ($with_lis){
//            $existhl7msg_row = $srvObj->isExistHL7Msg($refno);
//                    #edit
//                    /*if ($existhl7msg_row['msg_control_id'])
//                $order_control = "RP";
//            else
//                        $order_control = "NW";*/
//                    #if the previous msg is NW, the next request is RP
//                    #if the previous msg is CA, the next request is NW
//                    #if the previous msg is RP, the next request is still RP
//                    if ($existhl7msg_row['msg_control_id']){
//                        $filecontent = $existhl7msg_row['hl7_msg'];
//                        if (stristr($filecontent, 'ORC|NW|')){
//                            $order_control = "RP";
//                        }elseif (stristr($filecontent, 'ORC|CA|')){
//                            $order_control = "NW";
//                        }else
//                            $order_control = "RP";
//
//                    }else
//                $order_control = "NW";
//                }else
//                    $order_control = "CA";
//        }elseif ($mode=='cancel'){
//            $order_control = "CA";
//        }
        #$order_control = "NW";    
                
        $details->order_control = $order_control;
        
        #obr
        $row_order = $srvObj->getLabOrderNoLIMIT($refno);
        $details->POH_ORDER_NO = $row_order['lis_order_no'];
        #order items
        $result = $srvObj->getRequestDetailsbyRefnoLIS($refno);
        //echo $srvObj->sql;
        $count = $srvObj->FoundRows();
       
        while($row_test=$result->FetchRow()){
                #added by VAN 01-18-2013
                #for serial test
                #for the next takes, create new LIS order number but same refno
                if ($row_test['is_serial']){
                    #lookup in the seg_lab_group
                    $rs_serial = $srvObj->getTestProfileInclude($row_test['service_code']);
                    while($row_serial = $rs_serial->FetchRow()){
                        
                        #get the nth take of the request
                        $serial_row = $srvObj->getRecentSerialTestInfo($refno, $row_test['service_code']);
                        if (!$serial_row['nth_take'])
                            $serial_row['nth_take'] = 0;
                            
                        $nth_take = $serial_row['nth_take'] + 1;
                        
                        #initialize all data info to be saved
                        $serial_details['refno'] = $refno;
                        $serial_details['service_code'] = $row_test['service_code'];
                        $serial_details['lis_order_no'] = $details->POH_ORDER_NO;
                        $serial_details['nth_take'] = $nth_take;
                        $serial_details['is_served'] = 1;
                        $serial_details['with_result'] = 0;
                        $serial_details['is_repeated'] = 0;
                        
                        #saved in seg_lab_serv_serial, the FIRST take ONLY
                        $serial_ok = $srvObj->saveInfoSerial($serial_details);
                        
                        if ($serial_ok){
                            $serial_item = $srvObj->getTestInfo($row_serial['service_code_child']);
                            #outpatient
                            if (($ptype==2) || (!$ptype))
                                $service_code = trim($serial_item['oservice_code']);
                            else
                            #inpatient
                                $service_code = trim($serial_item['ipdservice_code']);

                            if (($is_rdu)||($loc_code2=='144'))
                                $service_code = trim($serial_item['ipdservice_code']);

                            $service .= $service_code.$COMPONENT_SEPARATOR.trim($serial_item['name']).$REPETITION_SEPARATOR;
                        }
                    }
                        
                }else{    
            
                #$service .= trim($row_test['service_code']).$COMPONENT_SEPARATOR.trim($row_test['name']).$REPETITION_SEPARATOR;
                #outpatient
                if (($ptype==2) || (!$ptype))
                    $service_code = trim($row_test['oservice_code']);
                else
                #inpatient
                    $service_code = trim($row_test['ipdservice_code']);
                    
                if (($is_rdu)||($loc_code2=='144'))        
                    $service_code = trim($row_test['ipdservice_code']);
                    
                $service .= $service_code.$COMPONENT_SEPARATOR.trim($row_test['name']).$REPETITION_SEPARATOR;
        }
        }
        
//Commented By Jarel Not Applicable for Manual Result
//        $service = trim($service);
//        $service_list = substr($service,0,strlen($service)-1);
//            #print_r($service_list);
//        $details->service_list = trim($service_list);
//        $details->POH_PRIORITY2 = trim($priority);
//            #$details->POH_TRX_DT =  date("YmdHis",strtotime($order_date));
//            $details->POH_TRX_DT =  date("YmdHis");
//        $details->POH_CLI_INFO = addslashes(mb_strtoupper(trim($_POST['clinicInfo'][0])));
//        $details->doctor = trim($_POST['requestDoc'][0]).$COMPONENT_SEPARATOR.addslashes(mb_strtoupper(trim($_POST['requestDocName'][0])));
//
//        if ($patient['encounter_type']==2)
//           $location1 = "OPD".$COMPONENT_SEPARATOR."OUTPATIENT";
//        elseif($patient['encounter_type']==1)
//           $location1 = "ER".$COMPONENT_SEPARATOR."ER";
//        elseif (($patient['encounter_type']==3) || ($patient['encounter_type']==4))
//           $location1 = "IPD".$COMPONENT_SEPARATOR."INPATIENT";
//        else
//           $location1 = "WN".$COMPONENT_SEPARATOR."WALKIN";
//
//            $details->location_dept = mb_strtoupper($loc_code2).$COMPONENT_SEPARATOR.mb_strtoupper($loc_name2);
//            #$details->location_dept = $location1;
//
//        $msh_segment = $HL7Obj->createSegmentMSH($details);
//        $pid_segment = $HL7Obj->createSegmentPID($details);
//        $pv1_segment = $HL7Obj->createSegmentPV1($details);
//        $orc_segment = $HL7Obj->createSegmentORC($details);
//        $obr_segment = $HL7Obj->createSegmentOBR($details);
//
//        $filecontent = $msh_segment."\n".$pid_segment."\n".$pv1_segment."\n".$orc_segment."\n".$obr_segment;
//            #echo $filecontent;
//        $file = $details->msg_control_id;
//        #create a file
//        #$filename_local = $fileObj->create_file_to_local($file);
//
//        #Thru file sharing
//        #write a file to a local directory
//        #$fileObj->write_file($filename_local, $filecontent);
        
//        switch ($transfer_method){
//            #FTP (File Transfer Protocol) approach
//            case "ftp" :
//                        $transportObj = new seg_transport_HL7_file($details);
//                        $transportObj->ftp_transfer($file, $filecontent);
//                        break;
//
//            #window NFS approach or network file sharing
//            case "nfs" :
//                        #create a file
//                        $filename_local = $fileObj->create_file_to_local($file);
//                        #Thru file sharing
//                        #write a file to a local directory
//                        $fileObj->write_file($filename_local, $filecontent);
//
//                        $filename_hclab = $fileObj->create_file_to_hclab($file);
//                        #write a file to a hclab directory
//                        $fileObj->write_file($filename_hclab, $filecontent);
//                        unlink($filename_local);
//                        break;
//            #TCP/IP (communication approach)
//            case "tcp" :
//                        $transportObj = new seg_transport_HL7_file($details);
//
//                        #if ($transportObj->isConnected()){
//                             #send the message
//                             $obj = $transportObj->sendHL7MsgtoSocket($filecontent);
//
//                             #return/print result
//                             $text = "LIS Server said:: ".$obj;
//                             #$text = "connected...";
//                        #}else{
//                        #     $text = "Unable to connect to LIS Server. Error: ".$transportObj->error."...";
//                        #}
//
//                        echo $text;
//                        break;
//        }
                                        
        #update msg control id
//        $details->msg_control_id = $details->msg_control_id_db;
//
//        #if new message control id, update the tracker
//        if (!$forreplace)
//            $hl7_ok = $srvObj->updateHL7_msg_control_id($details->msg_control_id);
//
//        #HL7 tracker
//        $srvObj->getInfo_HL7_tracker($details->msg_control_id);
//        $with_rec = $srvObj->count;
//
//        $details->lis_order_no = $row_order['lis_order_no'];
//        $details->msg_type = $msg_type;
//        $details->event_id = $event_id;
//        $details->refno = $refno;
//        $details->pid = $pid;
//        $details->encounter_nr = $encounter_nr;
//        $details->hl7_msg =  $filecontent;
        
//        if ($with_rec){
//            $hl7_ok = $srvObj->updateInfo_HL7_tracker($details);
//        }else{
//            $hl7_ok = $srvObj->addInfo_HL7_tracker($details);
//        }
//          }else{
                #cancelled
//                #validate if there a LIS posted request
//                $hl7_row = $srvObj->isExistHL7Msg($refno);
//
//                if ($hl7_row['msg_control_id']){
//                    $order_control = "CA";
//                    $hl7msg_row = $srvObj->isforReplaceHL7Msg($refno,$order_control);
//
//                    if ($hl7msg_row['msg_control_id']){
//                        $msg_control_id = $hl7msg_row['msg_control_id'];
//                        $forreplace = 1;
//                    }else
//                        $msg_control_id = $srvObj->getLastMsgControlID();
//
//                    $prefix = "HIS";
//
//                    #replace NW or RP to CA
//                    $filecontent = $hl7_row['hl7_msg'];
//                    #search for the string NW or RP in the message
//                    if (!stristr($filecontent, 'ORC|NW|') === FALSE){
//                        #replace NW to CA
//                        $filecontent = str_replace("ORC|NW|", "ORC|CA|", $filecontent);
//                    }elseif (!stristr($filecontent, 'ORC|RP|') === FALSE){
//                        #replace RP to CA
//                        $filecontent = str_replace("ORC|RP|", "ORC|CA|", $filecontent);
//                    }
//
//                    $details->msg_control_id_db = $msg_control_id;
//                    $details->msg_control_id = $prefix.$msg_control_id;
//
//                    $details->order_control = $order_control;
//
//                    $file = $details->msg_control_id;
//
//                    #create a file
//                    $filename_local = $fileObj->create_file_to_local($file);
//
//                    #Thru file sharing
//                    #write a file to a local directory
//                    $fileObj->write_file($filename_local, $filecontent);
//
//                    switch ($transfer_method){
//                        #FTP (File Transfer Protocol) approach
//                        case "ftp" :
//                                    $transportObj = new seg_transport_HL7_file($details);
//                                    $transportObj->ftp_transfer($file, $filecontent);
//                                    break;
//
//                        #window NFS approach or network file sharing
//                        case "nfs" :
//                                    #create a file
//                                    $filename_local = $fileObj->create_file_to_local($file);
//                                    #Thru file sharing
//                                    #write a file to a local directory
//                                    $fileObj->write_file($filename_local, $filecontent);
//
//                                    $filename_hclab = $fileObj->create_file_to_hclab($file);
//                                    #write a file to a hclab directory
//                                    $fileObj->write_file($filename_hclab, $filecontent);
//                                    unlink($filename_local);
//                                    break;
//                        #TCP/IP (communication approach)
//                        case "tcp" :
//                                    $transportObj = new seg_transport_HL7_file($details);
//
//                                    #if ($transportObj->isConnected()){
//                                         #send the message
//                                         $obj = $transportObj->sendHL7MsgtoSocket($filecontent);
//
//                                         #return/print result
//                                         $text = "LIS Server said:: ".$obj;
//                                         #$text = "connected...";
//                                    #}else{
//                                    #     $text = "Unable to connect to LIS Server. Error: ".$transportObj->error."...";
//                                    #}
//
//                                    echo $text;
//                                    break;
//                    }
//
//                    #update msg control id
//                    $details->msg_control_id = $details->msg_control_id_db;
//
//                    #if new message control id, update the tracker
//                    if (!$forreplace)
//                        $hl7_ok = $srvObj->updateHL7_msg_control_id($details->msg_control_id);
//
//                    #HL7 tracker
//                    $details->lis_order_no = $hl7_row['lis_order_no'];
//                    $details->msg_type = $hl7_row['msg_type'];
//                    $details->event_id = $hl7_row['event_id'];
//                    $details->refno = $refno;
//                    $details->pid = $hl7_row['pid'];
//                    $details->encounter_nr = $hl7_row['encounter_nr'];
//                    $details->hl7_msg =  $filecontent;
//
//                    if ($forreplace){
//                        $hl7_ok = $srvObj->updateInfo_HL7_tracker($details);
//                    }else{
//                        $hl7_ok = $srvObj->addInfo_HL7_tracker($details);
//                    }
//                }
                #--------------------------
          //}
      }  
    //}
    #-----------------------  
    

	if (!isset($refno) || !$refno){
		if (isset($_GET['ref']) && $_GET['ref']){
			$refno = $_GET['ref'];
		}
		if (isset($_POST['refno']) && $_POST['refno']){
			$refno = $_POST['refno'];
		}

		if (empty($refno)){
			$refno = $_GET['prevrefno'];
			$prevrefno = $refno;
		}
	}

	# added by VAN 01-15-08
	if ($prevrefno){
		$refInfo = $srvObj->getRequestInfoByPrevRef($prevrefno,$prevbatchnr);

		if ($refInfo['parent_refno'])
			//$refno = $refInfo['parent_refno'];
			$refno = $refInfo['refno'];
	}

	$mode='save';   # default mode
	if ($refNoBasicInfo = $srvObj->getBasicLabServiceInfo($refno)){
		$mode='update';
		extract($refNoBasicInfo);

		$serv_dt = formatDate2Local($serv_dt,$date_format);

	}

	 #$view_from = '';
	 if ($_GET['view_from'])
			$view_from = $_GET['view_from'];

	 if ($view_from=='ssview'){
		 if ($_GET['discountid']){
			 $discountid = $_GET['discountid'];
			 $infoSS = $objSS->getSSClassInfo($discountid);

			 if ($infoSS['parentid'])
					$discountid = $infoSS['parentid'];
			 else
					$discountid = $discountid;

			 $discount = $infoSS['discount'];
		 }
		 #$view_from = $HTTP_SESSION_VARS['view_from'];
	 }else
			$view_from = '';
 #echo $srvObj->sql;

 # Title in the title bar
 $LDLab = "Laboratory";

 $smarty->assign('sToolbarTitle',"$LDLab :: New Test Request");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");


 # Window bar title
 $smarty->assign('sWindowTitle',"$LDLab :: New Test Request");

 # Assign Body Onload javascript code
 $onLoadJS='onLoad="preset();checkCash();getTotalServCharges();"';
 $smarty->assign('sOnLoadJs',$onLoadJS);

 if ($popUp){
	 $smarty->assign('bHideTitleBar',TRUE);
	 $smarty->assign('bHideCopyright',TRUE);
 }
 # Collect javascript code

 ob_start();
 # Load the javascript code
 #$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
 $xajax->printJavascript($root_path.'classes/xajax_0.5');
?>

<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcuts.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type='text/javascript' src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.datetimepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.datetimepicker/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript">var $j = jQuery.noConflict();</script>

<script type="text/javascript" language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css">
<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script language="javascript" src="<?=$root_path?>js/dtpick_care2x.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/lab-request-new.js?t=<?=time()?>"></script>





<script type="text/javascript" src="<?=$root_path?>js/jsprototype/event.simulate.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/pharmacy/js/autocomplete.js"></script>
<link rel="stylesheet" href="<?=$root_path?>modules/pharmacy/css/autocomplete.css" type="text/css" media="screen" charset="utf-8" />
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>



<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;

	shortcut("F2",
		function(){
			var is_cash = $('is_cash').value;
			var pid = $('pid').value;
			var refno = $('refno').value;
			//alert(is_cash+", "+pid+", "+refno);
			viewPatientRequest(is_cash,pid,refno)
		}
	);

	function checkCash(){
		if ($("iscash1").checked){
			document.getElementById('is_cash').value = 1;
		}else{
			document.getElementById('is_cash').value = 0;
		}
	}

	function saveDiscounts2(){
		 var discountgiven = Math.round($('show-discount').value);

		 if ($F('view_from')=='override'){
			 if (parseFloat(discountgiven) <= 0){
				 alert('Please input a decent discount.');
			 }else{
				 if ($F('view_from')=='override'){
						if(confirm("Grant this request?")){
							usr=prompt("Please enter your username.","");
							if(usr&&usr!=""){
								pw=prompt("Please enter your password.","");
								if(pw&&pw!=""){
									xajax_updateRequest(usr, pw, $F('refno'), $F('show-discount'));
								}
							}
						}
				 }else{
						submitform();
				 }
			 }
		 }else if ($F('view_from')=='ssview'){
				submitform();
		 }
	}

	function submitform(){
		inputform.submit();
	}

	function NewRequest(){
		urlholder="seg-lab-request-new.php<?=URL_APPEND?>&user_origin=<?=$user_origin?>";
		window.location.href=urlholder;
	}

-->
</script>

<?php

	if ($popUp=='1'){
		echo $reloadParentWindow;
	}
	$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->append('JavaScript',$sTemp);

#added by daryl
    if ($person_name == ""){
       $person_name = $_POST["ordername"];;
    }else{
        $person_name =  $person_name ;
    }

    // $smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$person_name.'" style="font:bold 12px Arial;">');
    $smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" readonly="readonly" type="text" size="30" style="font:" value="'. $person_name.'" onfocus="autoSuggestWalkin(this)" autocomplete="off"/>');

	$var_arr = array(
		"var_pid"=>"pid",
		"var_encounter_nr"=>"encounter_nr",
		"var_discountid"=>"discountid",
		"var_orig_discountid"=>"orig_discountid",
		"var_discount"=>"discount",
		"var_name"=>"ordername",
		"var_addr"=>"orderaddress",
		"var_clear"=>"clear-enc",
		"var_area"=>"area",
		"var_history"=>"btnHistory",
		"var_gender"=>"gender"

	);

	$vas = array();
	foreach($var_arr as $i=>$v) {
		$vars[] = "$i=$v";
	}
	$var_qry = implode("&",$vars);

	 if ($area){
			$smarty->assign('sSelectEnc','<img name="select-enc" id="select-enc" src="'.$root_path.'images/btn_encounter_small.gif" border="0">');
	 }else{
		 $smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer"
			 onclick="if (warnClear()) {  clearEncounter(); emptyTray(); overlib(
				OLiframeContent(\''.$root_path."modules/registration_admission/seg-select-enc.php?$var_qry&var_include_enc='+($('iscash1').checked?'0':'1'),".
				'700, 400, \'fSelEnc\', 0, \'auto\'),
				WIDTH,700, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, \'<img src='.$root_path.'/images/close_red.gif border=0 >\',
				CAPTIONPADDING,2,
				CAPTION,\'Select registered person\',
				MIDX,0, MIDY,0,
				STATUS,\'Select registered person\'); } return false;"
			 onmouseout="nd();" />');
	}

	$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="cursor:pointer;font:bold 11px Arial" value="Clear" onclick="clearEncounter()" disabled>');
    $smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" readonly="readonly" cols="37" rows="2" style="font:bold 12px Arial">'.$orderaddress.'</textarea>');

	$smarty->assign('sDiscountShow','<input type="checkbox" disabled name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' ><label for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');

	$infoSS2 = $objSS->getSSClassInfo($discountid);

	if (($infoSS2['parentid'])&&($infoSS2['parentid']=='D'))
		$discountid2 = $infoSS2['parentid'];
	else
		$discountid2 = $discountid;

	$smarty->assign('sClassification',(($discountid2) ? $discountid2 : 'None'));
	$smarty->assign('sPatientType',(($enctype) ? mb_strtoupper($enctype): 'None'));
	$smarty->assign('sPatientLoc',(($location) ? mb_strtoupper($location) : 'None'));
	$smarty->assign('sPatientMedicoLegal',(($is_medico) ? "YES" : 'NO'));

	if ($_POST["ref"]!=NULL)
		$Ref = $_POST["ref"];
	elseif ($_GET["ref"]!=NULL)
		$Ref = $_GET["ref"];
	else{
		if (!$repeat)
			$Ref = $refno;
	}


	if ($repeat){
		$Ref = "";
		#$Ref2 = "";
	}else{
		if ($is_cash==0){
			$Ref = $refno;
			#$Ref2 = $refno;
		}else{
			$sql_hasPaid = "SELECT SUM(CASE WHEN(request_flag IS NOT NULL ) THEN 1 ELSE 0 END) AS withpaid
										FROM seg_lab_servdetails WHERE refno='$refno'";
			$rspaid = $db->Execute($sql_hasPaid);
			$rowpaid = $rspaid->FetchRow();
			extract($rowpaid);

			if ($withpaid){
				#$hasPaid = $withpaid;
				$hasPaid = 1;
				#$Ref2 = $refno;
			}
		}
	}

	if (($is_cash==0) && ($hasPaid==1))
		$hasPaid = 0;

	$smarty->assign('sRefNo','<input class="segInput" name="refno" id="refno" type="text" size="10" value="'.$Ref.'" readonly style="font:bold 12px Arial"/>');

	$dbtime_format = "Y-m-d H:i";
	$fulltime_format = "F j, Y g:ia";

	$curDate = date($dbtime_format);
	$curDate_show = date($fulltime_format);

	if (($repeat)||(empty($serv_dt)))
		$curDate = date($dbtime_format);
	elseif (($serv_dt!='0000-00-00')||(!empty($serv_dt))) {
		$requestDate = $serv_dt." ".$serv_tm;
		$submitted = 1;
		$_POST['orderdate'] = $requestDate;
	}

	$jsCalScript = "
			<script type=\"text/javascript\">
				Calendar.setup ({
					displayArea : \"show_orderdate\",
					inputField : \"orderdate\",
					ifFormat : \"%Y-%m-%d %H:%M\",
					daFormat : \"	%B %e, %Y %I:%M%P\",
					showsTime : true,
					button : \"orderdate_trigger\",
					singleClick : true,
					step : 1
				});
			</script>";

	$smarty->assign('sOrderDate','<span id="show_orderdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="jedInput" name="orderdate" id="orderdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="font:bold 12px Arial">');
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">'.$jsCalScript);

	#edited by VAN as DR. Vega's instruction
	#$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority0" onClick="checkPriority(); checkERIP(0);" value="0"'.($is_urgent? "": " checked").'>Routine');
	#$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority1" onClick="checkPriority(); checkERIP(1);" value="1"'.($is_urgent? " checked": "").'>STAT');
	$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority0" onClick="checkERIP(0);" value="0"'.($is_urgent? "": " checked").'>Routine');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority1" onClick="checkERIP(1);" value="1"'.($is_urgent? " checked": "").'>STAT');

	$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($is_cash!="0")?'checked="checked" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
	$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($is_cash=="0")?'checked="checked" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
    #$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($is_cash!="0")?'checked="checked" ':'').' onclick="changeTransactionType();" /><label for="iscash1" class="jedInput">Cash</label>');
    #$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($is_cash=="0")?'checked="checked" ':'').' onclick="changeTransactionType();" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');

	$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" wrap="physical"  cols="30" rows="10" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.$comments.'</textarea>');
	$smarty->assign('sOrderItems',"
				<tr>
                    <td colspan=\"12\">Request list is currently empty...</td>
				</tr>");
	if (!$ischecklist){
		 $filename = 'special_lab/seg-splab-service-tray.php';
	}else{
		 $filename = 'laboratory/seg-request-tray-checklist.php';
	}
	$smarty->assign('sBtnAddItem','<img type="image" name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_additems.gif" border="0" style="cursor:pointer;"
			onclick="return overlib(
				OLiframeContent(\''.$root_path.'modules/'.$filename.'?ref_source=LB&area='.$area.'&is_dr='.$is_dr.'&dr_nr='.$dr_nr.'&dept_nr='.$dept_nr.'&pid='.$pid.'&encounter_nr='.$encounter_nr.'\', 600, 350, \'fOrderTray\', 1, \'auto\'),
					WIDTH,390, TEXTPADDING,0, BORDER,0,
					STICKY, SCROLL, CLOSECLICK, MODAL,
					CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
					CAPTIONPADDING,4,
					CAPTION,\'Add laboratory service item from request tray\',
					MIDX,0, MIDY,0,
					STATUS,\'Add laboratory service item from request tray\');"
			onmouseout="nd();">');


	$smarty->assign('sBtnEmptyList','<img type="image" name="btnEmpty" id="btnEmpty" src="'.$root_path.'images/btn_emptylist.gif" border="0" style="cursor:pointer;" onclick="emptyTray();"></a>');

	$smarty->assign('sFree','<input type="checkbox" name="is_free" id="is_free" value="1" onClick="setDiscount();" />');
	$smarty->assign('sAdjustedAmount','<input id="show-discount" name="show-discount" type="text" onBlur="computeDiscount(this.value);formatDiscount(this.value);" onFocus="clearValue();" style="color:#006600; font-family:Arial; font-size:15px; font-weight:bold; text-align:right" size="5" onkeydown="return key_check(event, this.value)" value="'.number_format($adjusted_amount,2).'"/>');

	$smarty->assign('sBtnDiscounts','<img name="btndiscount" id="btndiscount" onclick="saveDiscounts2();" style="cursor:pointer" src="'.$root_path.'images/btn_discounts2.gif" border="0">');

	$smarty->assign('sSocialServiceNotes','<img src="'.$root_path.'images/btn_nonsocialized.gif"> <span style="font-style:italic">Nonsocialized service.</span>');

	#added by VAN 05-11-2010
	if (($admission_dt)&&(($admission_dt!='0000-00-00 00:00:00')||(empty($admission_dt))))
		$admission_dt = date("m/d/Y h:i A",strtotime($admission_dt));
	else
		$admission_dt = "";

	if (($discharge_date)&&(($discharge_date!='0000-00-00')||(empty($discharge_date))))
		$discharge_date = date("m/d/Y h:i A",strtotime($discharge_date));
	else
		$discharge_date = "";

	#$smarty->assign('sRDU','<input type="checkbox" '.(($is_rdu==1)?'checked="checked" ':'').' name="is_rdu" id="is_rdu" value="1" />');
	$smarty->assign('sAdmissionDate',$admission_dt);
	$smarty->assign('sDischargedDate',$discharge_date);
	$smarty->assign('sPatientHRN',$pid);
	$smarty->assign('sAdmDiagnosis',mb_strtoupper($impression));

	$smarty->assign('sPatientAge',$age);
	$smarty->assign('sPatientSex',$gender);
	$smarty->assign('sPatientBdate',$dob);


// added by daryl

$labwalk_ = $srvObj->selectifwalkin($pid);
$labwalk = $labwalk_->FetchRow();
$iflabwalk = $labwalk['ifwalk'];

    if ($iflabwalk > 0){
        $getwalkname =  $srvObj->selectwalkin($pid);
                while($getname = $getwalkname-> FetchRow()) {
                    if ($patient['name_middle']){
                            $name_middle = mb_strtoupper(substr($patient['name_middle'],0,1));
                         if ($name_middle)
                        $name_middle = " ".$name_middle.".";
                         else
                        $name_middle = "";
                     }
        $person_name = mb_strtoupper($getname['name_last']).", ".mb_strtoupper($getname['name_first']).$name_middle;
             if (trim($person_name)==',')
                $person_name = "";
         $smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$person_name.'" style="font:bold 12px Arial;">');
            }
}   

    $smarty->assign('sRDU','<input type="hidden" '.(($is_rdu==1)?'checked="checked" ':'').' name="is_rdu" id="is_rdu" value="1" onClick="validateRDU(); enablePhic();" />');
	$smarty->assign('sWalkin','<input type="checkbox" '.(($is_walkin==1)?'checked="checked" ':'').' name="is_walkin" id="is_walkin" onchange="checkIfWalkin()" value="1" />');
	$smarty->assign('sPE','<input type="checkbox" '.(($is_pe==1)?'checked="checked" ':'').' name="is_pe" id="is_pe" onchange="" '.(($is_personnel)?'':'disabled="disabled" ').' value="1" />');

	$smarty->assign('sHistoryButton','<img type="image" name="btnHistory" id="btnHistory" src="'.$root_path.'images/btn_history.gif" border="0" style="cursor:pointer;" onclick="viewHistory($(\'pid\').value,$(\'encounter_nr\').value);"></a>');
	#$smarty->assign('sOtherButton','<img type="image" name="btnOther" id="btnOther" src="'.$root_path.'images/btn_add_other.gif" border="0" style="cursor:pointer;" onclick="addOtherCharges($(\'pid\').value,$(\'encounter_nr\').value,$(\'ward_nr\').value);"></a>');


		#added by VAN 07-16-2010 TEMPORARILY
	$result = $ins_obj->getPersonInsuranceItems($pid);//$enc_obj->getChargeType("WHERE id NOT IN ('paid','phs','charity','cmap','lingap')","ordering");
	#$result = $enc_obj->getChargeType("WHERE id NOT IN ('paid','phs','charity')","ordering");
	$options="";
	$grant_type = $grant_type;
	#if (empty($type_charge) || ($type_charge==0))
	if (!($grant_type)){
		$grant_type = '';
		$disabled = "";
	}else{
		if ($is_cash==1)
		$disabled = "disabled";
		else
			$disabled = "";
	}

	$options = "<option value=''>PERSONAL</option>";
	while ($row=$result->FetchRow()) {
		if ($grant_type==$row['hcare_id'])
			$checked = "selected";
		else
			$checked = "";

		$options.='<option value="'.$row['hcare_id'].'" '.$checked.' >'.$row['firm_id'].'</option>';
	}

	/*$smarty->assign('sChargeTyp',
								"<select class=\"jedInput\" name=\"grant_type\" id=\"grant_type\" ".$disabled." onChange=\"checkCharge(this.value);\">
										 $options
								 </select>");*/
    $smarty->assign('sChargeTyp',
                                "<select class=\"jedInput\" name=\"grant_type\" id=\"grant_type\" ".$disabled." 
                                     onchange=\" if (warnClear()) { emptyTray(); checkCharge(this.value); changeChargeType(); return true;} else {return false;}\">
                                         $options
								 </select>");

	#------------end TEMPORARILY -------

	if ($parent_refno){
		$repeat=1;
	}

	#commented by VAN 05-26-2011
	/*if (empty($parent_refno))
		$parent_refno = $refno;
	else*/
	if ($prevrefno)
		$parent_refno = $prevrefno;

	#echo "batch = ".$prevbatchnr;

	if ((empty($parent_batch_nr))||($prevbatchnr))
		$parent_batch_nr = $prevbatchnr;

	$smarty->assign('sRepeat','<input type="checkbox" name="repeat" id="repeat" value="yes" '.(($repeat=="1")?'checked="checked" ':'').' disabled>');
	$smarty->assign('sParentRefno','<input class="segInput" id="parent_refno" name="parent_refno" type="text" size="40" value="'.$parent_refno.'" style="font:bold 12px Arial;" readonly/>');
	$smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="37" rows="2" style="font:bold 12px Arial">'.stripslashes($remarks).'</textarea>');
	$smarty->assign('sHead','<input class="segInput" id="approved_by_head" name="approved_by_head" type="text" size="40" value="'.$approved_by_head.'" style="font:bold 12px Arial;"/>');
	$smarty->assign('sHeadID','<input class="segInput" id="headID" name="headID" type="text" size="40" value="" style="font:bold 12px Arial;"/>');
	$smarty->assign('sHeadPassword','<input class="segInput" id="headpasswd" name="headpasswd" type="password" size="40" value="" style="font:bold 12px Arial;"/>');

	#added by VAN 08-23-2010
	#FOR Industrial Clinic Info
	$smarty->assign('sChargeToComp','<input type="checkbox" name="is_charge2comp" id="is_charge2comp" value="1" '.(($is_charge2comp=="1")?'checked="checked" ':'').' disabled>');
	$smarty->assign('sCompanyName',$compName);
	$smarty->assign('sCompanyID','<input class="segInput" id="compID" name="compID" type="hidden" size="10" value="'.$compID.'" style="font:bold 12px Arial;" readonly/>');

	$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'?popUp='.$popUp.'" method="POST" name="inputform" id="inputform" onSubmit="return checkRequestForm()">');
	$smarty->assign('sFormEnd','</form>');

	#added by VAN 06-02-2011

	$sql_manual = "SELECT * FROM seg_payment_workaround WHERE service_area='LB' AND refno='".$refno."' AND is_deleted=0";
	$res_manual=$db->Execute($sql_manual);
	$row_manual_count=$res_manual->RecordCount();
	$row_manual = $res_manual->FetchRow();

	$smarty->assign('sManualCheck','<input type="checkbox" name="for_manual" id="for_manual" value="1" '.(($row_manual_count)?'checked="checked" ':'').' onClick="setManualPayment();" />
																	<input type="hidden" name="for_manual_payment" id="for_manual_payment" value="">');
	$smarty->assign('sManualNumber','<input class="segInput" id="manual_control_no" name="manual_control_no" type="text" size="40" value="'.$row_manual['control_no'].'" style="font:bold 12px Arial;"/>');
	$smarty->assign('sManualApprovedby','<input class="segInput" id="manual_approved" name="manual_approved" type="text" size="40" value="'.$row_manual['approved_by'].'" style="font:bold 12px Arial;"/>');
	$smarty->assign('sManualReason','<textarea class="segInput" id="manual_reason" name="manual_reason" cols="37" rows="2" style="font:bold 12px Arial">'.$row_manual['reason'].'</textarea>');
	$smarty->assign('sManualTypeSelection','<input type="radio" class="jedInput" name="for_manual_type" id="for_manual_type1" value="paid" '.(($row_manual['type']=='paid')?'checked="checked" ':'').' onClick="setLabel()"/>&nbsp;<strong>Cash</strong>&nbsp;
																					<input type="radio" class="jedInput" name="for_manual_type" id="for_manual_type2" value="lingap" '.(($row_manual['type']=='lingap')?'checked="checked" ':'').' onClick="setLabel()"/>&nbsp;<strong>Lingap</strong>&nbsp;
																					<input type="radio" class="jedInput" name="for_manual_type" id="for_manual_type3" value="cmap" '.(($row_manual['type']=='cmap')?'checked="checked" ':'').' onClick="setLabel()"/>&nbsp;<strong>CMAP</strong>&nbsp;
																					<input type="radio" class="jedInput" name="for_manual_type" id="for_manual_type4" value="phic" '.(($row_manual['type']=='phic')?'checked="checked" ':'').' disabled onClick="setLabel()"/>&nbsp;<strong>PHIC</strong>&nbsp;');

	#--------------
	$smarty->assign('sTotalCharge', '<span id="total_charge" style="font: bold 12px Arial; color: red;">0.00</span>'); //added by maimai 11-21-2014
    #added by VAS 03/21/2012
    $phic_nr = $db->GetOne("SELECT fn_get_phic_number('".$encounter_nr."') AS `phic_nr`");
    $smarty->assign('sPhicNo', $phic_nr);
    
    $smarty->assign('sBtnCoverage','<img type="image" name="btn-coverage" id="btn-coverage" src="'.$root_path.'images/btn_coverage.gif" border="0" style="cursor:pointer;" onclick="return openCoverages();">');
    
     #added by pol
    if($encounter_nr){
        $sql_mc = "SELECT m.memcategory_desc
                        FROM seg_encounter_memcategory `e`
                        INNER JOIN seg_memcategory `m`
                        ON e.memcategory_id=m.memcategory_id
                        WHERE e.encounter_nr=".$db->qstr($encounter_nr);
        $category = $db->GetOne($sql_mc);
        
        if($category){                        
            $CategoryUi = $category;
        }else{
            $CategoryUi = 'None';    
            }
    }else{
            
            }     
    $smarty->assign('sMemCategory', $CategoryUi);
   #end pol

 ?>
<?php
ob_start();
$sTemp='';

if ($repeat){
	if ($refInfo['parent_refno'])
		$batchnr = $refInfo['refno'];
	else
		$batchnr = $prevbatchnr;
}else
	$batchnr = 0;

?>
	<script type="text/javascript" language="javascript">
		preset(<?= ($is_cash=='0')? "0":"1"?>);
		var user_origin = $('user_origin').value;
		var refno = '<?=$refno?>';
		var view_from = '<?=$view_from?>';
		var batchnr = '<?=$batchnr?>';
		var fromSS = 0;
		var discount = $('discount').value;
		var discountid = $('discountid').value;
		
		if (view_from=='ssview')
			fromSS = 1;

		switch (user_origin){
			case 'blood' :  ref_source = 'BB'; break;
			case 'lab' 	 :	ref_source = 'LB'; break;
			case 'splab' :  ref_source = 'SPL'; break;
			case 'iclab' :  ref_source = 'IC'; break;
		}

		if (refno){
			xajax_populateRequestListByRefNo(refno, ref_source, fromSS, discount, discountid);

            changeChargeType();
        }    

	</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

if ($mode=='update'){
	$smarty->assign('sIntialRequestList',$sTemp);
}

if (((($hasPaid)|| (!$is_cash))&&($mode=='update'))||($mode=='update')||($repeat)){
		#$smarty->assign('sClaimStub','<img name="claimstub" id="claimstub" onClick="viewClaimStub(\''.$is_cash.'\',\''.$refno.'\');" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'claim_stub.gif','0','left') . ' border="0">');
		$smarty->assign('sViewPDF','<img name="viewfile" id="viewfile" onClick="viewPatientRequest(\''.$info["is_cash"].'\',\''.$info["pid"].'\',\''.$Ref.'\');" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'viewpdf.gif','0','left') . ' border="0">');
		$withclaimstub = 1;
}

ob_start();
$sTemp='';

?>
	<input type="hidden" name="submitted" value="1">
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">

	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
	<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">

	<input type="hidden" name="is_cash" id="is_cash" value="<?=$is_cash?>" >
	<input type="hidden" id="encounter_nr" name="encounter_nr" value="<?=$encounter_nr?>">
	<input type="hidden" id="pid" name="pid" value="<?php if (trim($info["pid"])) echo $info["pid"]; else echo $pid;?>">
	<input type="hidden" name="discount2" id="discount2" value="<?=$discount2?>" >
	<input type="hidden" name="discount" id="discount" value="<?=$discount?>" >
	<input type="hidden" name="latest_valid_show-discount" id="latest_valid_show-discount" value="<?=number_format($adjusted_amount, 2, '.', '')?>" >

	<input type="hidden" id="gender" name="gender" value="<?=$sex;?>">
	<input type="hidden" id="date_birth" name="date_birth" value="<?=$date_birth;?>">

	<input type="hidden" id="orig_discountid" name="orig_discountid" value="<?=$orig_discountid?>">
	<input type="hidden" id="discountid" name="discountid" value="<?=$discountid;?>">

	<?php
		if (empty($Ref))
			$mode='save';
		else
			$mode='update';

		if ($_GET['view_from'])
			$view_from = $_GET['view_from'];
		elseif ($_POST['view_from'])
			$view_from = $_POST['view_from'];

		if (($encounter_type==3)||($encounter_type==4)){
			if ($loc_code){
				$ward_sql = "SELECT * FROM care_ward AS w WHERE w.nr='".$loc_code."'";
				$ward_info = $db->GetRow($ward_sql);
				if ($ward_info['accomodation_type']==1)
					#CHARITY
					$area_type = 'ch';
				elseif ($ward_info['accomodation_type']==2)
					#PAYWARD
					$area_type = 'pw';
			}
		}
	?>

	<input type="hidden" name="mode" id="mode" value="<?=$mode?$mode:'save'?>">
	<input type="hidden" name="popUp" id="popUp" value="<?=$popUp?$popUp:'0'?>">
	<input type="hidden" name="hasPaid" id="hasPaid" value="<?=$hasPaid?$hasPaid:'0'?>">
	<input type="hidden" name="view_from" id="view_from" value="<?=$view_from?$view_from:''?>">
	<input type="hidden" name="encoder_id" id="encoder_id" value="<?php echo $HTTP_SESSION_VARS['sess_login_personell_nr']; ?>">

	<input type="hidden" name="isrepeat" id="isrepeat" value="<?= $repeat?$repeat:'0'?>">

	<input type="hidden" name="area" id="area" value="<?=$area?>" />
	<input type="hidden" name="ptype" id="ptype" value="<?=$encounter_type?>" />

	<input type="hidden" id="ward_nr" name="ward_nr" value="" />
	<input type="hidden" name="area_type" id="area_type" value="<?=$area_type?>" />
	<input type="hidden" name="source" id="source" value="<?=$ptype?>">

	<input type="hidden" name="user_origin" id="user_origin" value="<?=$user_origin?>">

	<input type="hidden" name="current_att_dr_nr" id="current_att_dr_nr" value="<?=$current_att_dr_nr?>">
	<input type="hidden" name="current_dept_nr" id="current_dept_nr" value="<?=$current_dept_nr?>">

	<input type="hidden" name="impression" id="impression" value="<?=$impression?>">
	<input type="hidden" name="ischecklist" id="ischecklist" value="<?=$ischecklist?>">

	<input type="hidden" name="withclaimstub" id="withclaimstub" value="<?=$withclaimstub?>" />

	<input type="hidden" name="source_req" id="source_req" value="<?=(($repeat)||(empty($source_req)))?SegRequestSource::getSourceLaboratory():$source_req?>">

	<input type="hidden" name="isERIP" id="isERIP" value="<?=$isERIP?>">
	<input type="hidden" name="dept_area" id="dept_area" value="lab">
	<input type="hidden" name="viewonly" id="viewonly" value="<?=$viewonly?>">
	<input type="hidden" name="login_user" id="login_user" value="<?=$personell_nr?>">
	<input type="hidden" name="is_dr" id="is_dr" value="<?=$is_dr?>">

    <input type="hidden" name="is_maygohome" id="is_maygohome" value="<?=$is_maygohome?>">
    <input type="hidden" name="bill_nr" id="bill_nr" value="<?=$bill_nr?>">
    <input type="hidden" name="hasfinal_bill" id="hasfinal_bill" value="<?=$hasfinal_bill?>">
    <input type="hidden" name="ifwalk" id="ifwalk" value="<?=$iflabwalk?>">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->assign('sHiddenInputs',$sTemp);

if (($mode=="update") && ($popUp!='1')){
	$sBreakImg ='cancel.gif';
	$smarty->assign('sBreakButton','<img type="image" name="btnCancel" id="btnCancel" '.createLDImgSrc($root_path,$sBreakImg,'0','center').' align="center" alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';" style="cursor:pointer">');
}elseif ($popUp!='1'){
	$sBreakImg ='close2.gif';
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
}

$smarty->assign('sContinueButton','<img type="image" name="btnSubmit" id="btnSubmit" src="'.$root_path.'images/btn_submitorder.gif" align="center" style="cursor:pointer" onclick="if (confirm(\'Process this request?\')) if (checkRequestForm()) document.inputform.submit()">');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','laboratory/lab-request-new.tpl');
$smarty->display('common/mainframe.tpl');

?>