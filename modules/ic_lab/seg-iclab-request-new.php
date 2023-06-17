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
	define('LANG_FILE','konsil.php');
	define('NO_2LEVEL_CHK',1);

	$local_user='ck_lab_user';
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/inc_front_chain_lang.php');
	require($root_path.'modules/ic_lab/ajax/iclab-request-new.common.php');

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

	$title="Industrial Clinic Laboratory";

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

	require_once($root_path.'include/care_api_classes/class_special_lab.php');
	$splabObj = new SegSpecialLab();

	global $db;

	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');

	if (!isset($popUp) || !$popUp){
		if (isset($_GET['popUp']) && $_GET['popUp']){
			$popUp = $_GET['popUp'];
		}
		if (isset($_POST['popUp']) && $_POST['popUp']){
			$popUp = $_POST['popUp'];
		}
	}

	# added by VAN 01-11-08

	if ($_GET['repeat'])
		$repeat = $_GET['repeat'];
	else
		$repeat = $_POST['repeat'];

	$is_dr = $_GET['is_dr'];
	#echo "<br>get repeat = ".$repeat."<br>";

	#if ($_GET['prevbatchnr'])
	#	$prevbatchnr = $_GET['prevbatchnr'];

	if ($_GET['prevrefno']){
		$prevrefno = $_GET['prevrefno'];
		$sql_prev = "SELECT encounter_nr FROM seg_radio_serv WHERE refno='".$prevrefno."'";
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

		if ($patient['date_birth']!='0000-00-00')
			$dob = date("Y-m-d",strtotime($patient['date_birth']));
		else
				$dob = "unknown";

		if ($patient['sex']=='f')
			$gender = "Female";
		elseif ($patient['sex']=='m')
			$gender = "Male";
		else
			$gender = "unknown";

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

	$location = $loc_name;
	$is_medico = $patient['is_medico'];
	$is_medico = $patient['is_medico'];

	$_POST['serv_tm'] = date('H:i:s',strtotime($_POST['orderdate']));

	$_POST['is_tpl'] = '0';
	$_POST['fromBB'] = 0;

	#ref_source of industrial clinic laboratory
	#$_POST['grant_type'] = NULL;
	$_POST['ref_source'] = 'IC';
	if ($_GET['ptype'])
		$ptype = $_GET['ptype'];

	$is_rdu = 0;

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
		case 'lab' :
			$source_req = SegRequestSource::getSourceRadiology();
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
			$source_req = SegRequestSource::getSourceSpecialLab();
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



 switch($mode){
		case 'save':
				if(trim($_POST['orderdate'])!=""){
					$_POST['serv_dt'] = date("Ymd",strtotime($_POST['orderdate']));
				}

				$_POST['loc_code'] = $loc_code;
				$_POST['clinical_info'] = $_POST['clinicInfo'];
				$_POST['request_doctor'] = $_POST['requestDoc'];
				$_POST['request_dept'] = $_POST["requestDept"];
				$_POST['is_in_house'] = $_POST['isInHouse'];
				$_POST['service_code'] = $_POST['items'];
				$_POST['is_cash'] = $_POST['iscash'];
				$_POST['is_urgent'] = $_POST['priority'];
				$_POST['quantity'] = $_POST['qty'];
				$_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
				$_POST['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";

				$_POST['refno'] = $srvObj->getLastRefno();

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

					$radio_obj->getStaffInfo($_POST['headID'],$_POST['headpasswd']);
					#echo "<br>sql = ".$srvObj->sql;
					$isCorrectInfo = $srvObj->count;
					if (($isCorrectInfo)||($allow_radiorepeat)){
						if($refno = $srvObj->saveLabRefNoInfoFromArray($_POST)){
                            $saveok=$srvObj->update_LabRefno_Tracker($_POST['refno']);
                            $saveok = 1;
							#$smarty->assign('sysInfoMessage',"Industrial Clinic Laboratory Request Service successfully created.");
						}else{
                            $saveok = 0;
							#$smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$radio_obj->getErrorMsg());
					}
					}else{
						header("Location: ".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_test&user_origin=lab&popUp=1&repeat=1&prevbatchnr=".$_POST['parent_batch_nr']."&prevrefno=".$_POST['parent_refno']."&repeaterror=1");
						exit;
					}
                    
                    if (!$saveok){ 
                        $srvObj->FailTrans();
                        $smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$srvObj->sql);
                    }else{    
                        $srvObj->CompleteTrans();
                        $smarty->assign('sysInfoMessage',"Industrial Clinic Laboratory Request Service successfully created.");
                    }
                    
				}else{

                    $srvObj->startTrans();
					if($refno = $srvObj->saveLabRefNoInfoFromArray($_POST)){
						$saveok=$srvObj->update_LabRefno_Tracker($_POST['refno']);
                        #$smarty->assign('sysInfoMessage',"Industrial Clinic Laboratory Request Service successfully created.");
					}#else{
						#$smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$srvObj->getErrorMsg());
					#}
                    
                    if (!$saveok){ 
                        $srvObj->FailTrans();
                        $smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$srvObj->sql);
					}else{
                        $srvObj->CompleteTrans();
                        $smarty->assign('sysInfoMessage',"Industrial Clinic Laboratory Request Service successfully created.");
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
				$_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
				$_POST['history'] = $srvObj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");

				$_POST['is_forward']='0';
				$_POST['is_monitor']='0';

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

					$radio_obj->getStaffInfo($_POST['headID'],$_POST['headpasswd']);
					#echo "<br>sql = ".$srvObj->sql;
					$isCorrectInfo = $srvObj->count;
					#echo "<br>count = ".$isCorrectInfo;
					if (($isCorrectInfo)||($allow_radiorepeat)){
						if($srvObj->updateLabRefNoInfoFromArray($_POST)){
							$smarty->assign('sysInfoMessage',"Industrial Clinic Laboratory Request Service successfully updated.");
						}else{
							$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
						}
					}else{
						header("Location: ".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_test&user_origin=lab&popUp=1&repeat=1&prevbatchnr=".$_POST['parent_batch_nr']."&prevrefno=".$_POST['parent_refno']."&repeaterror=1");
						exit;
					}
				}else{
						if($srvObj->updateLabRefNoInfoFromArray($_POST)){
							//$reloadParentWindow='<script language="javascript">'.
//								'	window.parent.jsOnClick(); '.
//								'</script>';
							$smarty->assign('sysInfoMessage',"Industrial Clinic Laboratory Service successfully updated.");
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
 $LDICLab = "Industrial Clinic Laboratory";

 $smarty->assign('sToolbarTitle',"$LDICLab :: New Test Request");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");


 # Window bar title
 $smarty->assign('sWindowTitle',"$LDICLab :: New Test Request");

 # Assign Body Onload javascript code
 $onLoadJS='onLoad="preset();checkCash();"';
 $smarty->assign('sOnLoadJs',$onLoadJS);

 if ($popUp){
	 $smarty->assign('bHideTitleBar',TRUE);
	 $smarty->assign('bHideCopyright',TRUE);
 }
 # Collect javascript code

 ob_start();
 # Load the javascript code
 $xajax->printJavascript($root_path.'classes/xajax-0.2.5');
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
<script type="text/javascript" src="js/iclab-request-new.js?t=<?=time()?>"></script>

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
	}

	function submitform(){
		inputform.submit();
	}

	function NewRequest(){
		urlholder="seg-iclab-request-new.php<?=URL_APPEND?>&user_origin=<?=$user_origin?>";
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

	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$person_name.'" style="font:bold 12px Arial;" readonly>');

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
		"var_history"=>"btnHistory"
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
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" readonly>'.$orderaddress.'</textarea>');

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

	$smarty->assign('sRefNo','<input class="segInput" name="refno" id="refno" type="text" size="10" value="'.$Ref.'" readonly style="font:bold 12px Arial"/>');

	$dbtime_format = "Y-m-d H:i";
	$fulltime_format = "F j, Y g:ia";

	$curDate = date($dbtime_format);
	$curDate_show = date($fulltime_format);

	if (($repeat)||(empty($request_date)))
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

	$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority0" value="0"'.($is_urgent? "": " checked").'>Routine');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority1" value="1"'.($is_urgent? " checked": "").'>STAT');

	$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($is_cash!="0")?'checked="checked" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
	$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($is_cash=="0")?'checked="checked" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');

	$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" wrap="physical"  cols="30" rows="10" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.$comments.'</textarea>');
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"8\">Request list is currently empty...</td>
				</tr>");
	if (!$ischecklist){
		 $filename = 'special_lab/seg-splab-service-tray.php';
	}else{
		 $filename = 'laboratory/seg-request-tray-checklist.php';
	}
	$smarty->assign('sBtnAddItem','<img type="image" name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_additems.gif" border="0" style="cursor:pointer;"
			onclick="return overlib(
				OLiframeContent(\''.$root_path.'modules/'.$filename.'?&ref_source=IC&area='.$area.'&is_dr='.$is_dr.'&dr_nr='.$dr_nr.'&dept_nr='.$dept_nr.'&pid='.$pid.'&encounter_nr='.$encounter_nr.'\', 600, 435, \'fOrderTray\', 1, \'auto\'),
					WIDTH,435, TEXTPADDING,0, BORDER,0,
					STICKY, SCROLL, CLOSECLICK, MODAL,
					CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
					CAPTIONPADDING,4,
					CAPTION,\'Add industrial clinic laboratory service item from request tray\',
					MIDX,0, MIDY,0,
					STATUS,\'Add industrial clinic laboratory service item from request tray\');"
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

	$smarty->assign('sRDU','<input type="checkbox" '.(($is_rdu==1)?'checked="checked" ':'').' name="is_rdu" id="is_rdu" value="1" />');
	$smarty->assign('sWalkin','<input type="checkbox" '.(($is_walkin==1)?'checked="checked" ':'').' name="is_walkin" id="is_walkin" onchange="checkIfWalkin()" value="1" />');
	$smarty->assign('sPE','<input type="checkbox" '.(($is_pe==1)?'checked="checked" ':'').' name="is_pe" id="is_pe" onchange="" '.(($is_personnel)?'':'disabled="disabled" ').' value="1" />');

	$smarty->assign('sHistoryButton','<img type="image" name="btnHistory" id="btnHistory" src="'.$root_path.'images/btn_history.gif" border="0" style="cursor:pointer;" onclick="viewHistory($(\'pid\').value,$(\'encounter_nr\').value);"></a>');
	#$smarty->assign('sOtherButton','<img type="image" name="btnOther" id="btnOther" src="'.$root_path.'images/btn_add_other.gif" border="0" style="cursor:pointer;" onclick="addOtherCharges($(\'pid\').value,$(\'encounter_nr\').value,$(\'ward_nr\').value);"></a>');


		#added by VAN 07-16-2010 TEMPORARILY
	#$result = $enc_obj->getChargeType("WHERE id NOT IN ('paid','phs','charity','cmap','lingap')","ordering");
	$result = $enc_obj->getChargeType("WHERE id NOT IN (SELECT id FROM seg_type_charge)","ordering");
	$options="";
	$grant_type = $grant_type;
	#if (empty($type_charge) || ($type_charge==0))
	if (!($grant_type)){
		$grant_type = '';
		$disabled = "";
	}else
		$disabled = "disabled";

	$options = "<option value=''>PERSONAL</option>";
	if (is_object($result)){
	while ($row=$result->FetchRow()) {
		if ($grant_type==$row['id'])
			$checked = "selected";
		else
			$checked = "";

		$options.='<option value="'.$row['id'].'" '.$checked.' >'.$row['charge_name'].'</option>';
	}
	}

	$smarty->assign('sChargeTyp',
								"<select class=\"jedInput\" name=\"grant_type\" id=\"grant_type\" ".$disabled.">
										 $options
								 </select>");

	#------------end TEMPORARILY -------

	if (($parent_refno)&&($parent_batch_nr)){
		$repeat=1;
	}

	if (empty($parent_refno))
		$parent_refno = $refno;
	elseif ($prevrefno)
		$parent_refno = $prevrefno;

	#echo "batch = ".$prevbatchnr;

	if ((empty($parent_batch_nr))||($prevbatchnr))
		$parent_batch_nr = $prevbatchnr;

	$smarty->assign('sRepeat','<input type="checkbox" name="repeat" id="repeat" value="yes" '.(($repeat=="1")?'checked="checked" ':'').' disabled>');
	$smarty->assign('sParentRefno','<input class="segInput" id="parent_refno" name="parent_refno" type="text" size="40" value="'.$parent_refno.'" style="font:bold 12px Arial;" readonly/><input id="parent_batch_nr" name="parent_batch_nr" type="hidden" size="40" value="'.$parent_batch_nr.'" style="font:bold 12px Arial;"/>');
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

 ?>
<?php
ob_start();
$sTemp='';

if ($repeat){
	if ($refInfo['parent_batch_nr'])
		$batchnr = $refInfo['batch_nr'];
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

		if (refno)
			xajax_populateRequestListByRefNo(refno, ref_source, fromSS, discount, discountid);

	</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

if ($mode=='update'){
	$smarty->assign('sIntialRequestList',$sTemp);
}

if (((($hasPaid)|| (!$is_cash))&&($mode=='update'))||($mode=='update')||($repeat)){
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

	<input type="hidden" name="source_req" id="source_req" value="<?=(($repeat)||(empty($source_req)))?'IC':$source_req?>">
    
    <input type="hidden" name="login_user" id="login_user" value="<?=$personell_nr?>">
    <input type="hidden" name="is_dr" id="is_dr" value="<?=$is_dr?>">
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
$smarty->assign('sMainBlockIncludeFile','ic_lab/iclab-request-new.tpl');
$smarty->display('common/mainframe.tpl');

?>