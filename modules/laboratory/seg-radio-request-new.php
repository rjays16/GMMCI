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
#	define('LANG_FILE','lab.php');
	$lang_tables[] = 'departments.php';
	define('LANG_FILE','konsil.php');
	define('NO_2LEVEL_CHK',1);
#	$local_user='ck_lab_user';
	$local_user='ck_radio_user';   # burn added : September 24, 2007
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/inc_front_chain_lang.php');
	require($root_path.'modules/radiology/ajax/radio-request-new.common.php');

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
	#$phpfd=str_replace("yy","%y", strtolower($phpfd));

$title=$LDRadiology;
#$breakfile=$root_path.'modules/radiology/'.$breakfile;   # burn added: August 29, 2007
$breakfile=$root_path.'modules/radiology/radiolog.php'.URL_APPEND;   # bun added: September 8, 2007
$thisfile=basename(__FILE__);

	# Create radiology object
	require_once($root_path.'include/care_api_classes/class_radiology.php');
	$radio_obj = new SegRadio;
	
	#added by VAN 06-17-08
	require_once($root_path.'include/care_api_classes/class_ward.php');
	$ward_obj = new Ward;
	
	require_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;
	#-------------------
	
	#added by VAN 06-25-08
	require_once($root_path.'include/care_api_classes/class_social_service.php');
	$objSS = new SocialService;
		
	#added by VAN 07-08-08
	require_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj = new Person;
	
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;
	#-------------------	
		
		
#	global $db;
	
	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');

#echo "nursing-station-radio-request-new.php : b4 switch _POST : "; print_r($_POST); echo " <br><br> \n";

#echo "nursing-station-radio-request-new.php : before : popUp ='".$popUp."; _GET['popUp'] = '".$_GET['popUp']."'; _POST['popUp'] = '".$_POST['popUp']."' <br> \n";
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
		
	if ($_GET['prevbatchnr'])
		$prevbatchnr = $_GET['prevbatchnr'];
		
	if ($_GET['prevrefno'])	
		$prevrefno = $_GET['prevrefno'];
	
	#echo "repeat - batch - refno = ".$repeat." - ".$prevbatchnr." - ".$prevrefno;
	
	#added by VAN 03-19-08
	$repeaterror = $_GET['repeaterror'];
	
	#added by VAN 06-25-08
	$discountid_get = $_GET['discountid'];
	
	#echo "<br>repeaterror = ".$_GET['repeaterror'];
	#echo "<br>repeat = ".$_GET['repeat'];
	
	#added by VAN 07-08-08
	if ($_GET['encounter_nr'])
		$encounter_nr = $_GET['encounter_nr'];
	
	if ($_GET['area'])
		$area = $_GET['area'];	
	
	if ($_GET['pid'])
		$pid = $_GET['pid'];
	#---------------------
    
    if ($encounter_nr){
        $patient = $enc_obj->getEncounterInfo($encounter_nr);
    }
	
	if ($repeaterror){
		#$smarty->assign('sWarning',"<strong>Error:</strong> Sorry but you are not allowed to do a repeat request!");
		$smarty->assign('sWarning','<strong>Error:</strong> Sorry but you are not allowed to do a repeat request!');
	}
	#-----------------------------
	
	#added by VAN 01-29-08
	$_POST['request_time'] = date('H:i:s');
	
	#added by VAN 06-16-08
	if (empty($_POST['is_tpl'])){
		$_POST['is_tpl'] = '0';
	}/*elseif($_POST['is_tpl']){
		$_POST['type_charge'] = '0';
	}*/
	#-----------------
	
#echo "nursing-station-radio-request-new.php : after : popUp ='".$popUp."; _GET['popUp'] = '".$_GET['popUp']."'; _POST['popUp'] = '".$_POST['popUp']."' <br> \n";
#echo "mode = ".$mode;
	switch($mode){
		case 'save':
	#echo "nursing-station-radio-request-new.php : save mode = '".$mode."' <br> \n";
	#echo "nursing-station-radio-request-new.php : _POST : "; print_r($_POST); echo " <br><br> \n";
	#echo "nursing-station-radio-request-new.php : _POST['findings_date'] = '".formatDate2STD($_POST['findings_date'], $date_format)."' <br> \n";
				if(trim($_POST['request_date'])!=""){
					$_POST['request_date'] = formatDate2STD($_POST['request_date'], $date_format);
				}
				$_POST['clinical_info'] = $_POST['clinicInfo'];	
				#$_POST['clinical_info'] = stripslashes($_POST['clinicInfo']);	
				$_POST['request_doctor'] = $_POST['requestDoc'];	
				$_POST['is_in_house'] = $_POST['isInHouse'];
				$_POST['service_code'] = $_POST['items'];
				$_POST['is_cash'] = $_POST['iscash'];
				$_POST['is_urgent'] = $_POST['priority'];
#				$_POST['hasPaid'] = 0;   # not yet paid since this is just a request
				$_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
				$_POST['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
#echo "nursing-station-radio-request-new.php : save 2: _POST : "; print_r($_POST); echo " <br><br> \n";
#echo "nursing-station-radio-request-new.php : _POST['pid'] = '".$_POST['pid']."' <br> \n";
				/*
				if ($_POST['is_urgent']){
					$rid = $radio_obj->createNewRID($_POST['pid']); 
				}
				*/
				#edited by VAN 04-28-08
				#$rid = $radio_obj->createNewRID($_POST['pid']); 
#echo "nursing-station-radio-request-new.php : rid = '".$rid."' <br> \n";
#	exit();
				#added by VAN 03-19-08
				#echo "save repeat = ".$repeat01;
				if ($repeat01){
					#-------added by VAN 01-11-08-------------
					$_POST['parent_batch_nr'] = $_POST['parent_batch_nr'];
					$_POST['parent_refno'] = $_POST['parent_refno'];
					$_POST['approved_by_head'] = $_POST['approved_by_head'];
					$_POST['remarks'] = $_POST['remarks'];
					
					#added by VAN 03-19-08
					$_POST['headID'] = $_POST['headID'];
					$_POST['headpasswd'] = $_POST['headpasswd'];
				
					#-----------------------------------------
				
					$radio_obj->getStaffInfo($_POST['headID'],$_POST['headpasswd']);
					#echo "<br>sql = ".$radio_obj->sql;
					$isCorrectInfo = $radio_obj->count;
					#echo "<br>count = ".$isCorrectInfo;
					if ($isCorrectInfo){
						#echo "<br>sulod save radio ";
						if($refno = $radio_obj->saveRadioRefNoInfoFromArray($_POST)){
							$rid = $radio_obj->createNewRID($_POST['pid']); 
							$smarty->assign('sysInfoMessage',"Radiological Request Service successfully created.");
						}else{
							$smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$radio_obj->getErrorMsg());
					}
					}else{
						header("Location: ".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_test&user_origin=lab&popUp=1&repeat=1&prevbatchnr=".$_POST['parent_batch_nr']."&prevrefno=".$_POST['parent_refno']."&repeaterror=1");
						exit;
					}
				}else{						

					if($refno = $radio_obj->saveRadioRefNoInfoFromArray($_POST)){
						$rid = $radio_obj->createNewRID($_POST['pid']); 
#						$errorMsg='<font style="color:#FF0000">Successfully saved!</font>';
						$smarty->assign('sysInfoMessage',"Radiological Request Service successfully created.");
					}else{
						# $errorMsg = $db->ErrorMsg();
	#					$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
						$smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$radio_obj->getErrorMsg());
#						$smarty->assign('sWarning','<strong>Error:</strong> '.$radio_obj->getErrorMsg());
					}
				}	
				
				break;
		case 'update':
	#echo "nursing-station-radio-request-new.php : update mode = '".$mode."' <br> \n";			
	#echo "nursing-station-radio-request-new.php : _POST : "; print_r($_POST); echo " <br><br> \n";

	#			if($radio_obj->saveAFinding($batch_nr,$finding_nr,$findings,$radio_impression,$findings_date,$doctor_id,'Update')){
				if(trim($_POST['request_date'])!=""){
					$_POST['request_date'] = formatDate2STD($_POST['request_date'], $date_format);
				}
				
				$_POST['clinical_info'] = $_POST['clinicInfo'];	
				#$_POST['clinical_info'] = stripslashes($_POST['clinicInfo']);
				$_POST['request_doctor'] = $_POST['requestDoc'];	
				$_POST['is_in_house'] = $_POST['isInHouse'];
				$_POST['service_code'] = $_POST['items'];
				$_POST['is_cash'] = $_POST['iscash'];
				$_POST['is_urgent'] = $_POST['priority'];
#				$_POST['hasPaid'] = 0;   # not yet paid since this is just a request
				$_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
   			$_POST['history'] = $radio_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");		
				/*
				if ($_POST['is_urgent']){
					$rid = $radio_obj->createNewRID($_POST['pid']); 
				}
				*/
				#edited by VAN 04-28-08
				#$rid = $radio_obj->createNewRID($_POST['pid']); 
#echo "nursing-station-radio-request-new.php : save 2: _POST : "; print_r($_POST); echo " <br><br> \n";
				#added by VAN 03-19-08
				#echo "repeat = ".$repeat01;
				if ($repeat01){
					#-------added by VAN 01-11-08-------------
					#echo "batch, head, remarks = ".$_POST['parent_batch_nr']." - ".$_POST['approved_by_head']." - ".$_POST['remarks'];
					$_POST['parent_batch_nr'] = $_POST['parent_batch_nr'];
					$_POST['parent_refno'] = $_POST['parent_refno'];
					$_POST['approved_by_head'] = $_POST['approved_by_head'];
					$_POST['remarks'] = $_POST['remarks'];
				
					#added by VAN 03-19-08
					$_POST['headID'] = $_POST['headID'];
					$_POST['headpasswd'] = $_POST['headpasswd'];
				
				#-----------------------------------------
				
					$radio_obj->getStaffInfo($_POST['headID'],$_POST['headpasswd']);
					#echo "<br>sql = ".$radio_obj->sql;
					$isCorrectInfo = $radio_obj->count;
					#echo "<br>count = ".$isCorrectInfo;
					if ($isCorrectInfo){
						if($radio_obj->updateRadioRefNoInfoFromArray($_POST)){
							$rid = $radio_obj->createNewRID($_POST['pid']); 
							$reloadParentWindow='<script language="javascript">'.
								'	window.parent.jsOnClick(); '.
								'</script>';
							$smarty->assign('sysInfoMessage',"Radiological Request Service successfully updated.");					
						}else{
							$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
						}
					}else{
						header("Location: ".$root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_test&user_origin=lab&popUp=1&repeat=1&prevbatchnr=".$_POST['parent_batch_nr']."&prevrefno=".$_POST['parent_refno']."&repeaterror=1");
						exit;
					}	
				}else{
				
					if($radio_obj->updateRadioRefNoInfoFromArray($_POST)){
						$rid = $radio_obj->createNewRID($_POST['pid']); 
#						$errorMsg='<font style="color:#FF0000">Successfully updated!</font>';
						$reloadParentWindow='<script language="javascript">'.
								'	window.parent.jsOnClick(); '.
#								'	javascript:self.parent.location.href=self.parent.location.href;'.
								'</script>';
						$smarty->assign('sysInfoMessage',"Radiological Request Service successfully updated.");					
					}else{
						$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
					}
				}	
				break;
		case 'cancel':
#	echo "nursing-station-radio-request-new.php : cancel mode = '".$mode."' <br> \n";			
#	echo "nursing-station-radio-request-new.php : _POST : "; print_r($_POST); echo " <br><br> \n";
				if($radio_obj->deleteRefNo($_POST['refno'])){
/*					$errorMsg='<font style="color:#FF0000">Successfully deleted!</font>';
					echo "<script language='javascript'> alert('Successfully deleted!')</script>";
*/
					header('Location: '.$breakfile);
					exit;
				}else{
					$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
				}
				break;
	}# end of switch stmt	
#	$refno='2007000004';	
	#echo "sql = ". $radio_obj->sql;
	if (!isset($refno) || !$refno){
		if (isset($_GET['refno']) && $_GET['refno']){
			$refno = $_GET['refno'];
		}
		if (isset($_POST['refno']) && $_POST['refno']){
			$refno = $_POST['refno'];
		}
		
		if (empty($refno)){
			$refno = $_GET['prevrefno'];
			$prevrefno = $refno;
		}	
	}
#echo "nursing-station-radio-request-new.php : refno='".$refno."' <br> \n";			
#echo "seg-radio-request-new.php : refno, batch = '$prevrefno' , '$prevbatchnr' <br> \n";			
	
	# added by VAN 01-15-08
	$refInfo = $radio_obj->getRequestInfoByPrevRef($prevrefno,$prevbatchnr);
	#print_r($refInfo);
	#echo "refno , batch = ".$refInfo['parent_refno']." - ".$refInfo['parent_batch_nr'];
	
	if ($refInfo['parent_refno'])
		//$refno = $refInfo['parent_refno'];
		$refno = $refInfo['refno'];
	
	$mode='save';   # default mode
	if ($refNoBasicInfo = $radio_obj->getBasicRadioServiceInfo($refno)){
		#echo "van:seg-radio-request-new = ".$radio_obj->sql;
		$mode='update';
		extract($refNoBasicInfo);
		if (empty($refNoBasicInfo['pid']) || !$refNoBasicInfo['pid']){
			$person_name = trim($refNoBasicInfo['ordername']);
		}else{
				# in case there is an updated profile of the person
			$person_name = trim($refNoBasicInfo['name_first']).' '.trim($refNoBasicInfo['name_last']);
		}
#echo "nursing-station-radio-request-new.php : before : request_date='".$request_date."' <br> \n";			
		$request_date = formatDate2Local($request_date,$date_format); 
#echo "nursing-station-radio-request-new.php : after : request_date='".$request_date."' <br> \n";			
	}#end of if-stmt
	#added by VAN 07-08-08
	#elseif (($pid)&&($area=="ER")){
	elseif (($pid)&&(!empty($area))){
		#echo "pid = ".$pid;
		$patientInfo = $person_obj->getAllInfoArray($pid);
		$person_name = ucwords(strtolower($patientInfo['name_first']))." ".ucwords(strtolower($patientInfo['name_last']));
		
		if ($patientInfo['street_name'])
			$addr_comma = ",";
		$orderaddress = ucwords(strtolower($patientInfo['street_name'])).$addr_comma." ".ucwords(strtolower($patientInfo['brgy_name']))." ".ucwords(strtolower($patientInfo['mun_name']));
		
		$rid = $radio_obj->RIDExists($pid);
	}
	
#echo "nursing-station-radio-request-new.php : mode='".$mode."' <br> \n";			
#echo "nursing-station-radio-request-new.php : radio_obj->sql='".$radio_obj->sql."' <br> \n";
#echo "nursing-station-radio-request-new.php : refNoBasicInfo : "; print_r($refNoBasicInfo); echo " <br><br> \n";
	
  #added by VAN 06-25-08
   if (!(trim($discountid))){
	  	$discountid = $discountid_get;
		
	 #$discount
     $socialInfo = $objSS->getSSClassInfo($discountid_get);
	 #echo "discount = ".$socialInfo['discount'];
	 if (trim($discount)==0)
	 	$discount = $socialInfo['discount'];	
   }
   #--------------------	 

 # Title in the title bar
 #$smarty->assign('sToolbarTitle',"$LDRadiology :: $LDDiagnosticTest");
 $smarty->assign('sToolbarTitle',"$LDRadiology :: New Test Request");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

if ($popUp!='1'){
		 # href for the close button
	 $smarty->assign('breakfile',$breakfile);
}else{
		# CLOSE button for pop-ups
	 #edited by VAN 07-11-08
	 #if ($area=='ER')
	 if ($area)
	 	$smarty->assign('breakfile','javascript:window.parent.cClick();');
	 else	
	 	$smarty->assign('breakfile','javascript:window.parent.pSearchClose();');
	 
	 $smarty->assign('pbBack','');
}

 # Window bar title
 #$smarty->assign('sWindowTitle',"$LDRadiology :: $LDDiagnosticTest");
 $smarty->assign('sWindowTitle',"$LDRadiology :: New Test Request");

 # Assign Body Onload javascript code
 
 #$onLoadJS='onLoad="preSet();"';
 #edited by VAN 06-14-08
 $onLoadJS='onLoad="CheckRepeatInfo();checkCash();"';
 #echo "onLoadJS = ".$onLoadJS;
 $smarty->assign('sOnLoadJs',$onLoadJS);

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

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">
<!--
.olbg {
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	background-color:#0000ff;
	border:1px solid #4d4d4d;
}
.olcg {
	background-color:#aa00aa; 
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
	background-color:#ffffcc; 
	text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
	font-family:Arial; font-size:13px; 
	font-weight:bold; 
	color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}
.olfgright {text-align: right;}
.olfgjustify {background-color:#cceecc; text-align: justify;}

a {color:#338855;font-weight:bold;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style> 

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>

			<!-- START for setting the DATE (NOTE: should be IN this ORDER...i think soo..) -->
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
			<!-- END for setting the DATE (NOTE: should be IN this ORDER...i think soo..) -->

<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/radio-request-new.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;

	//added by VAN 06-14-08
	function checkCash(){
		if ($("iscash1").checked){
			document.getElementById('is_cash').value = 1;
			//$('tplrow').style.display = '';
			$('type_charge').style.display = '';
			
		}else{
			document.getElementById('is_cash').value = 0;	
			//$('tplrow').style.display = 'none';
			//$('type_charge').style.display = '';
			$('type_charge').style.display = '';
			//document.getElementById('is_tpl').checked = false;
		}	
	}
	//----------------------
	
	// added by VAN 01-11-08
	function CheckRepeatInfo(){
		if (document.getElementById('repeat').checked){
			document.getElementById('repeatinfo01').style.display = '';
			document.getElementById('repeatinfo02').style.display = '';
			document.getElementById('repeatinfo03').style.display = '';
			
			//added by VAN 03-19-08
			document.getElementById('repeatinfo04').style.display = '';
			document.getElementById('repeatinfo05').style.display = '';
			
			document.getElementById('show-discount').value = formatNumber(0,2);
		}else	{
			document.getElementById('repeatinfo01').style.display = 'none';
			document.getElementById('repeatinfo02').style.display = 'none';
			document.getElementById('repeatinfo03').style.display = 'none';
			
			//added by VAN 03-19-08
			document.getElementById('repeatinfo04').style.display = 'none';
			document.getElementById('repeatinfo05').style.display = 'none';
		}	
	}	
		//----------------------------
	
	function eDiscount(amount,bol){
		document.getElementById('show-discount').value = amount;
		document.getElementById('show-discount').disabled = bol;
		if(bol){	
			document.getElementById('btndiscount').style.display = 'none';
		}else{
			document.getElementById('btndiscount').style.display = '';
		}
	}
	
	function saveDiscounts(){
		var refno, amtDiscount, encoderId; 
		refno = document.getElementById("refno").value;
		amtDiscount = document.getElementById("show-discount").value;
		encoderId = document.getElementById("encoder_id").value;
				
		if((amtDiscount == '')||(amtDiscount == 0)||isNaN(amtDiscount)){
			alert("Please enter discount.");
//			$('show-discount').value='0.00';
			$('show-discount').value=$F('latest_valid_show-discount');//reset to the lastest valid value
			document.getElementById('show-discount').focus();
		}else{
			//alert("save discounts value " + amtDiscount + " refno =" + refno + "\n encoder =" + encoderId );	
			if (refreshDiscount()){
				xajax_setCharityDiscounts(refno, encoderId, amtDiscount);
				$('latest_valid_show-discount').value=$F('show-discount');
				//refreshDiscount();
			}else{
				$('show-discount').value=$F('latest_valid_show-discount');//reset to the lastest valid value
				refreshDiscount();
			}
		}
	}
	
	//added by VAN 06-25-08
	function saveDiscounts2(){
		inputform.submit();
	}
	
	//added by VAN 07-10-08
	function NewRequest(){
		urlholder="seg-radio-request-new.php<?=URL_APPEND?>&user_origin=<?=$user_origin?>";
		window.location.href=urlholder;
	}

-->
</script>

<?php
#echo "nursing-station-radio-request-new.php : hasPaid='".$hasPaid."' <br> \n";			
	if ($popUp=='1'){
		echo $reloadParentWindow;
	}
	$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->append('JavaScript',$sTemp);

	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$pid.'"/>');
	$smarty->assign('sRID','<input class="segInput" id="rid" name="rid" type="text" size="10" value="'.$rid.'" style="font:bold 12px Arial;" readonly>');
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$person_name.'" style="font:bold 12px Arial;" readonly>');
/*
	$smarty->assign('sSelectEnc','<input class="segInput" name="select-enc" id="select-enc" type="image" src="../../images/btn_encounter_small.gif" border="0" style=""
       onclick="overlib(
        OLiframeContent(\'seg-radio-select-enc.php\', 700, 400, \'fSelEnc\', 1, \'auto\'),
        WIDTH,700, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
				CAPTION,\'Select registered person\',
        MIDX,0, MIDY,0, 
        STATUS,\'Select registered person\'); return false;"
       onmouseout="nd();" />');
*/
	$var_arr = array(
		"var_rid"=>"rid",
		"var_pid"=>"pid",
		"var_encounter_nr"=>"encounter_nr",
		"var_discountid"=>"discountid",
        "var_orig_discountid"=>"orig_discountid",   
		"var_discount"=>"discount",
		"var_name"=>"ordername",
		"var_addr"=>"orderaddress",
		"var_clear"=>"clear-enc"
	);
	$vas = array();
	foreach($var_arr as $i=>$v) {
		$vars[] = "$i=$v";
	}
	$var_qry = implode("&",$vars);
	/*
	$smarty->assign('sSelectEnc','<img class="segInput" name="select-enc" id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer;"
       onclick="overlib(
        OLiframeContent(\''.$root_path."modules/registration_admission/seg-select-enc.php?$var_qry&var_include_enc=1',".
				'700, 395, \'fSelEnc\', 0, \'auto\'),
        WIDTH,700, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL, 
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
				CAPTION,\'Select registered person\',
        MIDX,0, MIDY,0, 
        STATUS,\'Select registered person\'); return false;"
       onmouseout="nd();">');
	 */
	 
	 #edited by VAN 06-18-08
	 #if ($area=="ER"){
	 if ($area){
	  	$smarty->assign('sSelectEnc','<img name="select-enc" id="select-enc" src="'.$root_path.'images/btn_encounter_small.gif" border="0">');
	 }else{	
	   $smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer"
       onclick="if (warnClear()) { emptyTray(); overlib(
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
	#$smarty->assign('sSSClassID','<input type="text" name="discountid" id="discountid" size="5" value="'.$discountid.'" readonly style="font:bold 12px Arial">');
	
	#added by VAN 06-16-08
	if ($area=="ER"){
			$enctype = "ER PATIENT";	
			$location = "EMERGENCY ROOM";
			$encounter_type = 1;
		
			$medico = $enc_obj->getEncounterMedicoCases($encounter_nr, $pid);
			#echo $enc_obj->sql;
			if ($medico)
				$info = $medico->FetchRow();
				$is_medico = $info['is_medico'];
	}elseif ($area=="clinic"){
			
            if (($patient['encounter_type']==3)||($patient['encounter_type']==4)){
                if ($patient['encounter_status']=='direct_admission'){
                    $enctype = "INPATIENT (DIRECT ADMISSION)";
                }else{
                    if ($patient['encounter_type']==3)
                        $enctype = "INPATIENT (ER)";
                    elseif ($patient['encounter_type']==4)
                        $enctype = "INPATIENT (OPD)";
                }  
            }elseif($patient['encounter_type']==1){
                $enctype = "ER PATIENT"; 
            }elseif($patient['encounter_type']==2){
                $enctype = "OUTPATIENT";  
            } 
            
            $encounter_type = $patient['encounter_type'];
            #$enctype = "OUTPATIENT";
			#$encounter_type = 2;	
			$dept = $enc_obj->getEncounterDept($encounter_nr);
			$location = mb_strtoupper($dept['name_formal']);
		
			$medico = $enc_obj->getEncounterMedicoCases($encounter_nr, $pid);
			#echo $enc_obj->sql;
			if ($medico)
				$info = $medico->FetchRow();			
	}else{	
		if ($encounter_type==1){
			$enctype = "ER PATIENT";
			$location = "EMERGENCY ROOM";
		}elseif ($encounter_type==2){
			#$enctype = "OUTPATIENT (OPD)";
			$enctype = "OUTPATIENT";
			$dept = $dept_obj->getDeptAllInfo($current_dept_nr);
			$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
		}elseif (($encounter_type==3)||($encounter_type==4)){
			if ($encounter_type==3)
				$enctype = "INPATIENT (ER)";
			elseif ($encounter_type==4)
				$enctype = "INPATIENT (OPD)";
				
			$ward = $ward_obj->getWardInfo($current_ward_nr);
			#echo "sql = ".$ward_obj->sql;
			$location = strtoupper(strtolower(stripslashes($ward['name'])))."&nbsp;&nbsp;&nbsp;Room # : ".$current_room_nr;
		}else{
			$enctype = "WALK-IN";
			$dept = $dept_obj->getDeptAllInfo($current_dept_nr);
			$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
		}
	}	
	#echo "mode = ".$mode;
	#$smarty->assign('sDiscountShow','<input type="checkbox" name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' onclick="seniorCitizen()"><label class="jedInput" for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
	
	#edited by VAN 06-25-08
	#---------------------
	#added by VAN 06-25-08
	/*
	if (trim($senior_ID))
		$_POST["issc"] = 1;
	else	
		$_POST["issc"] = 0;
	*/	
	#------------------	
	
	
	#added by VAN 07-05-08
	#echo "here = ".$discountid;
	if ($_GET['view_from']=='ssview'){
		$discountid = $_GET['discountid'];
	}
	
	if((!($discountid))&&($encounter_type)){
		#echo "enc = ".$encounter_type;
		if ($encounter_type==2)
			$ss = $objSS->getPatientSocialClass($pid, 1);
		else
			$ss = $objSS->getPatientSocialClass($pid, 0);	
			
		$discountid = $ss['discountid'];	
	}
	
	$ssInfo = $objSS->getSSClassInfo($discountid);
	if ($ssInfo['parentid']){
		$discountid = $ssInfo['parentid'];
		$discount = $ssInfo['discount'];
	}	
	
	#echo "pid = ".$pid;
	if (empty($pid))
		$pid = $info['pid'];
		
		$ss_sc = $objSS->getPatientSocialClass($pid, 0);
		
		if ($ss_sc['discountid']=='SC')
			$_POST["issc"] = 1;
		else	
			$_POST["issc"] = 0;
	
		if (($_POST["issc"])&&(trim($encounter_type)==""))
			$discount = 0.20;	
	#------------
	#echo "here = ".$discountid;
	#if ((isset($_POST['select-enc']))||($mode=='update')){
	#if (((isset($_POST['select-enc']))||($mode=='update'))||($area=='ER')){
	
	#echo "here = ".$_POST["issc"];
	$smarty->assign('sDiscountShow','<input type="checkbox" disabled name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' ><label for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
	
	if (((isset($_POST['select-enc']))||($mode=='update'))||($area)){
		$smarty->assign('sClassification',(($discountid) ? $discountid : 'None'));
		$smarty->assign('sPatientType',(($enctype) ? $enctype : 'None'));
		$smarty->assign('sPatientLoc',(($location) ? $location : 'None'));
		#is_medico
		$smarty->assign('sPatientMedicoLegal',(($is_medico) ? "YES" : 'NO'));
	}
	#---------------------
	
	# added by VAN 01-14-08
	#echo "repeat = ".$repeat;
	
	if (($repeat)&&(empty($refInfo['parent_refno'])))
		$Ref = "";
	else{
		#if ($area=="ER"){
		if ($area){
	   		$Ref = $refno;
				$Ref2 = $refno;
	   }else{	
			#edited by VAN 07-05-08
			$radio_obj->getSumPaidPerTransaction($refno);
		
			if ($radio_obj->count){
				$Ref2 = $refno;
			}else{
				#echo "here = ".$discount;
				#if(($is_cash==0) || ($discount==1.00))
				if(($is_cash==0) || ($discount==1.00) || ($type_charge))
					$Ref2 = $refno;	
				else
					$Ref2 = "";	
					
			}	
			$Ref = $refno;
		}	
	}	
	#echo "refno = ".$refno."<br>";
	#commented by VAN	
	#$smarty->assign('sRefNo','<input class="segInput" name="refno" id="refno" type="text" size="10" value="'.$refno.'" readonly style="font:bold 12px Arial"/>');
	$smarty->assign('sRefNo','<input class="segInput" name="refno2" id="refno2" type="text" size="10" value="'.$Ref2.'" readonly style="font:bold 12px Arial"/><input name="refno" id="refno" type="hidden" size="10" value="'.$Ref.'"/>');
#	$smarty->assign('sResetRefNo','<input class="segInput" type="button" value="Reset" style="font:bold 11px Arial"/>');
	
	#---------------added by VAN -----------------------
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
	
	#echo "batch, head, remarks = ".$parent_batch_nr." - ".$approved_by_head." - ".$remarks;
	
	$smarty->assign('sRepeat','<input type="checkbox" name="repeat" id="repeat" value="yes" '.(($repeat=="1")?'checked="checked" ':'').' disabled>');
	#$smarty->assign('sParentBatchNr','<input class="segInput" id="parent_batch_nr" name="parent_batch_nr" type="text" size="40" value="'.$parent_batch_nr.'" style="font:bold 12px Arial;" readonly/>');
	$smarty->assign('sParentBatchNr','<input class="segInput" id="parent_refno" name="parent_refno" type="text" size="40" value="'.$parent_refno.'" style="font:bold 12px Arial;" readonly/><input id="parent_batch_nr" name="parent_batch_nr" type="hidden" size="40" value="'.$parent_batch_nr.'" style="font:bold 12px Arial;"/>');
	$smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="37" rows="2" style="font:bold 12px Arial">'.stripslashes($remarks).'</textarea>');
	$smarty->assign('sHead','<input class="segInput" id="approved_by_head" name="approved_by_head" type="text" size="40" value="'.$approved_by_head.'" style="font:bold 12px Arial;"/>');
	
	#added by VAN 03-18-08
	$smarty->assign('sHeadID','<input class="segInput" id="headID" name="headID" type="text" size="40" value="" style="font:bold 12px Arial;"/>');
	$smarty->assign('sHeadPassword','<input class="segInput" id="headpasswd" name="headpasswd" type="password" size="40" value="" style="font:bold 12px Arial;"/>');
	#-----------------------------------------------------
	
	# commented by VAN
	#$curDate = ($request_date)? $request_date:date("m/d/Y");
	
	if (($repeat)||(empty($request_date)))
		$curDate = date("m/d/Y");
	else
		$curDate = 	$request_date;
	
	$jsCalScript = "
			<script type=\"text/javascript\">
				Calendar.setup ({
					inputField : \"request_date\", ifFormat : \"$phpfd\", showsTime : false, button : \"request_date_trigger\", singleClick : true, step : 1
				});
			</script>";
#	$smarty->assign('sOrderDate','<input name="request_date" type="text" size="10" value="'.$curDate.'" style="font:bold 12px Arial">');
#	$smarty->assign('sOrderDate','<input name="request_date" id="request_date" type="text" size="10" value="'.$curDate.'" style="font:bold 12px Arial">');
	$smarty->assign('sOrderDate','<input name="request_date" id="request_date" type="text" size="10" 
											value="'.$curDate.'" style="font:bold 12px Arial"
											onFocus="this.select();"  
											onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
											onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
											onKeyUp="setDate(this,\'MM/dd/yyyy\',\'en\')">');

	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="request_date_trigger" name="request_date_trigger" align="absmiddle" style="cursor:pointer">'.$jsCalScript);
	
	$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority0" value="0"'.($is_urgent? "": " checked").'>Normal');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority1" value="1"'.($is_urgent? " checked": "").'>Urgent');
	
	#$smarty->assign('sIsCash','<input type="radio" name="iscash" id="iscash1" value="1"'.(($is_cash||empty($is_cash))? " checked":"").' onchange="if (changeTransactionType) changeTransactionType();">Cash');
	#$smarty->assign('sIsCharge','<input class="segInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0"'.(($is_cash=='0')? " checked":"").' onchange="if (changeTransactionType) changeTransactionType()">Charge');
	#edited by VAN 06-14-08
	if ($area=="ER"){
		$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($pid)?'':'checked="checked" ').' '.(($pid)?'disabled="disabled" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($pid)?'checked="checked" ':'').' '.(($pid)?'':'disabled="disabled" ').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
	}elseif ($area=="clinic"){
       /* 
		$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" checked onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" disabled onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
	    */
         
        if ($is_dr){
              # echo "type = ".$patient['encounter_type']; 
            if (($patient['encounter_type']==3)||($patient['encounter_type']==4)){
                $smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" checked onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
                $smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
            }elseif($patient['encounter_type']==1){
                #$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($pid)?'':'checked="checked" ').' '.(($pid)?'disabled="disabled" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
                #$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($pid)?'checked="checked" ':'').' '.(($pid)?'':'disabled="disabled" ').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
                $smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($pid)?'':'checked="checked" ').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
                $smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($pid)?'checked="checked" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
            }elseif($patient['encounter_type']==2){
                $smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" checked onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
                $smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" disabled onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
            }
            
        }else{
           $smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" checked onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
        $smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" disabled onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
         }    
          
    }else{
		$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($is_cash!="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($is_cash=="0")?'checked="checked" ':'').'onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
	}
		
	#added by VAN 06-14-08
	$smarty->assign('sIsTPL','<span id="tplrow"><input class="jedInput" type="checkbox" name="is_tpl" id="is_tpl" value="1" '.(($is_tpl=="1")?'checked="checked" ':'').'/><label class="jedInput" for="is_tpl" style="color:#006600">To pay later</label></span>');
	
	$result = $radio_obj->getChargeType();
	$options="";
	#echo "typ = ".$_POST['type_charge'];
	#echo "<br>typ = ".$type_charge;
	if (empty($type_charge) || ($type_charge==0))
		$type_charge = 0;
		
	while ($row=$result->FetchRow()) {
		if ($type_charge==$row['id'])
			$checked = "selected";
		else
			$checked = "";
			
		$options.='<option value="'.$row['id'].'" '.$checked.' >'.$row['charge_name'].'</option>';
	}
	
	$smarty->assign('sChargeTyp',
								"<select class=\"jedInput\" name=\"type_charge\" id=\"type_charge\">
								     $options
								 </select>");
	#-------------------------
	
	$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.$comments.'</textarea>');
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"6\">Request list is currently empty...</td>
				</tr>");
/*
$smarty->assign('sBtnAddItem','<a href="javascript:void(0);"
       onclick="return overlib(
        OLiframeContent(\'nursing-station-radio-tray.php\', 600, 515, \'fOrderTray\', 1, \'auto\'),
        WIDTH,515, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
				CAPTION,\'Add radiological service item from request tray\',
        MIDX,0, MIDY,0, 
        STATUS,\'Add radiological service item from request tray\');"
       onmouseout="nd();">
		 	<img type="image" name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_addservicecodes.gif" border="0" style="cursor:pointer;"></a>');
*/
$smarty->assign('sBtnAddItem','<img type="image" name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_additems.gif" border="0" style="cursor:pointer;"
			onclick="return overlib(
				OLiframeContent(\'seg-radio-service-tray.php?dr_nr='.$dr_nr.'&dept_nr='.$dept_nr.'\', 600, 435, \'fOrderTray\', 1, \'auto\'),
					WIDTH,435, TEXTPADDING,0, BORDER,0, 
					STICKY, SCROLL, CLOSECLICK, MODAL, 
					CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
					CAPTIONPADDING,4, 
					CAPTION,\'Add radiological service item from request tray\',
					MIDX,0, MIDY,0, 
					STATUS,\'Add radiological service item from request tray\');"
			onmouseout="nd();">');
#---May29,2008 Note: replaced btn_addservicecodes.gif above with btn_additems.gif for consistency w/ other modules---pet---

# <img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_additems.gif" border="0"></a>');
/*
$smarty->assign('sBtnEmptyList','<a href="javascript:emptyTray()">
		<img type="image" name="btnEmpty" id="btnEmpty" src="'.$root_path.'images/btn_emptylist.gif" border="0" style="cursor:pointer;"></a>');
*/
$smarty->assign('sBtnEmptyList','<img type="image" name="btnEmpty" id="btnEmpty" src="'.$root_path.'images/btn_emptylist.gif" border="0" style="cursor:pointer;" onclick="emptyTray();"></a>');

#$smarty->assign('sBtnEmptyList','<a href="javascript:emptyTray()"><img src="'.$root_path.'images/btn_emptylist.gif" border="0" /></a>');

#$smarty->assign('sAdjustedAmount','<input name="show-discount" id="show-discount" type="text" readonly style="color:#006600; font-family:Arial; font-size:15px; font-weight:bold; text-align:right" size="5" value="'.number_format($adjusted_amount, 2, '.', '').'">');
#edited by VAN 06-16-08
$smarty->assign('sAdjustedAmount','<input id="show-discount" name="show-discount" type="hidden" onBlur="formatDiscount(this.value);" readonly style="color:#006600; font-family:Arial; font-size:15px; font-weight:bold; text-align:right" size="5" value="'.number_format($adjusted_amount,2).'"/>');
#commented by VAN 06-16-08
#$smarty->assign('sDiscountInfo','<img src="'.$root_path.'images/discount.gif">');

#$smarty->assign('sBtnDiscounts', '<img name="btndiscount" id="btndiscount" onclick="saveDiscounts();" src="'.$root_path.'images/btn_discounts2.gif" border="0" style="cursor:pointer;display:none">');

#edited by VAN 06-25-08
if ($view_from=='ssview'){ 
	#echo "discountid = ".$discountid;
	if ($discountid)
		$smarty->assign('sBtnDiscounts','<img name="btndiscount" id="btndiscount" onclick="saveDiscounts2();" style="cursor:pointer" src="'.$root_path.'images/btn_discounts2.gif" border="0">');
	else
		$smarty->assign('sBtnDiscounts','<img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_discounts2.gif" border="0" style="display:none">');
}elseif ($hasPaid==1){
	#echo "sulod paid";
	$smarty->assign('sBtnDiscounts','<img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_discounts2.gif" border="0" style="display:none">');
}else{
	#echo "sulod 1";
	$smarty->assign('sBtnDiscounts','<img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_discounts2.gif" border="0" style="display:none">');
}
#-----------

/*$smarty->assign('sBtnDiscounts','<a href="javascript:void(0);"
       onclick="return overlib(
        OLiframeContent(\'seg-request-discounts.php\', 380, 125, \'if1\', 1, \'auto\'),
        WIDTH,380, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
				CAPTION,\'Change discount options\',
        REF,\'btndiscount\', REFC,\'LL\', REFP,\'UL\', REFY,2, 
        STATUS,\'Change discount options\');"
       onmouseout="nd();">
			 <img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_discounts.gif" border="0"></a>');*/
#$smarty->assign('sBtnPDF','<a href="#"><img src="'.$root_path.'images/btn_printpdf.gif" border="0"></a>');
/*
	$jsCalScript = "<script type=\"text/javascript\">
		Calendar.setup ({
			inputField : \"request_date\", ifFormat : \"$phpfd\", showsTime : false, button : \"request_date_trigger\", singleClick : true, step : 1
		});
	</script>
	";
	$smarty->assign('jsCalendarSetup', $jsCalScript);
*/
$smarty->assign('sSocialServiceNotes','<img src="'.$root_path.'images/btn_nonsocialized.gif"> <span style="font-style:italic">Nonsocialized service.</span>');

 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'?popUp='.$popUp.'" method="POST" name="inputform" id="inputform" onSubmit="return checkRequestForm()">');
 $smarty->assign('sFormEnd','</form>');
 #echo "refno & batch = ".$refno." - ".$prevbatchnr;
 
?>
<?php
ob_start();
$sTemp='';

# added by VAN 01-14-08
#echo "b4 ref, batch = ".$refno." - ".$batchnr;
if (!empty($Ref)){
	$refno = $Ref;
	#$batchnr = $Ref; 
}else{
	if ($refInfo['parent_batch_nr'])
		$batchnr = $refInfo['batch_nr'];
	else	
		$batchnr = $prevbatchnr;
}
#echo "<br>after ref, batch = ".$refno." - ".$batchnr;
?>
	<script type="text/javascript" language="javascript">
		preset(<?= ($is_cash=='0')? "0":"1"?>);
		//xajax_populateRequestListByRefNo(<?=$refno? $refno:0?>);	
		
		// edited by VAN 01-11-08
		xajax_populateRequestListByRefNo(<?=$refno? $refno:0?>,<?=$batchnr? $batchnr:0?>);	
		
		//xajax_getCharityDiscounts(<?=$refno?>);
	</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

if ($mode=='update'){
	$smarty->assign('sIntialRequestList',$sTemp);
}

ob_start();
$sTemp='';
?>
	<input type="hidden" name="submitted" value="1">
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">  
<!--  
	<input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
-->
	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
	<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
	
	<?php
		$discountInfo = $objSS->getSSClassInfo($discountid);
		
		if ($discountInfo){
			$discount = 	$discountInfo['discount'];
		}	
		#echo "sc = ".$_POST['issc'];
		#echo "<br>type = ".$encounter_type;
		
		if(($_POST['issc'])&&(trim($encounter_type)=="")){
			$discount = 0.20;
		}
	?>
	
	<input type="hidden" name="is_cash" id="is_cash" value="<?=$is_cash?>" >
	<input type="hidden" id="encounter_nr" name="encounter_nr" value="<?=$encounter_nr?>">
	<input type="hidden" name="discount2" id="discount2" value="<?=$discount2?>" >
	<input type="hidden" name="discount" id="discount" value="<?=$discount?>" >
	<input type="hidden" name="latest_valid_show-discount" id="latest_valid_show-discount" value="<?=number_format($adjusted_amount, 2, '.', '')?>" >
	
	<!-- added by VAN 06-16-08 -->
	<!--<input type="hidden" id="discountid" name="discountid" value="<?php if ($info["discountid"]) echo $info["discountid"]; else $discountid;?>">-->
	<input type="hidden" id="orig_discountid" name="orig_discountid" value="<?=$orig_discountid?>">
    <input type="hidden" id="discountid" name="discountid" value="<?=$discountid;?>">
	
	<!-- -->
	
	
	<?php 
		#----- added by VAN 01-12-08
		if ((empty($refInfo['parent_batch_nr']))&&(empty($refInfo['parent_refno']))&&(empty($Ref)))
			$mode='save';		
		else
			$mode='update';		
		#---------------------------
	?>
	
	<input type="hidden" name="mode" id="mode" value="<?=$mode?$mode:'save'?>">
	<input type="hidden" name="popUp" id="popUp" value="<?=$popUp?$popUp:'0'?>">
	<input type="hidden" name="hasPaid" id="hasPaid" value="<?=$hasPaid?$hasPaid:'0'?>">
	<input type="hidden" name="view_from" id="view_from" value="<?=$view_from?$view_from:''?>">
	<input type="hidden" name="encoder_id" id="encoder_id" value="<?php echo $HTTP_SESSION_VARS['sess_login_personell_nr']; ?>">
	
	<input type="hidden" name="repeat01" id="repeat01" value="<?= $repeat?$repeat:'0'?>">
	
	<input type="hidden" name="area" id="area" value="<?=$area?>" />

<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->assign('sHiddenInputs',$sTemp);
#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';alert($F(\'mode\'));" onsubmit="return false;" style="cursor:pointer">');
#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';alert($F(\'mode\'));" style="cursor:pointer">');
if (($mode=="update") && ($popUp!='1')){
	$sBreakImg ='cancel.gif';
	$smarty->assign('sBreakButton','<img type="image" '.createLDImgSrc($root_path,$sBreakImg,'0','center').' align="center" alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';" style="cursor:pointer">');
}elseif ($popUp!='1'){
	$sBreakImg ='close2.gif';	
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
}

#$smarty->assign('sContinueButton','<img type="image" name="btnSubmit" id="btnSubmit" src="'.$root_path.'images/btn_submitorder.gif" align="center" style="cursor:pointer" onClick="checkRequestForm();">');
#edited by VAN 06-27-08
$smarty->assign('sContinueButton','<img type="image" name="btnSubmit" id="btnSubmit" src="'.$root_path.'images/btn_submitorder.gif" align="center" style="cursor:pointer" onclick="if (confirm(\'Process this radiology request?\')) if (checkRequestForm()) document.inputform.submit()">');

if (($hasPaid)|| ((($encounter_type!='')||($encounter_type!=NULL)) && ($encounter_type!=2)) || ($type_charge) || $repeat)
	$smarty->assign('sClaimStub','<img name="claimstub" id="claimstub" onClick="viewClaimStub(\''.$is_cash.'\',\''.$refno.'\');" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'claim_stub.gif','0','left') . ' border="0">');


#added by VAN 07-10-08
#echo "from = ".$popUp;
if (($view_from!='ssview') && ($popUp!=1)){ 
	#$smarty->assign('sAddNewRequest','<a href="javascript:NewRequest();" nd();><img '.createLDImgSrc($root_path,'newrequest.gif','0','left').' border=0 alt="Enter New Radiology Request"></a>');
}
#---May29,2008 Note: replaced btn_submitrequest.gif above with btn_submitorder.gif for consistency w/ other modules---pet---

#added by VAN 03-03-08
/*
$smarty->assign('sRefreshDiscountButton','<input type="button" name="btnRefreshDiscount" id="btnRefreshDiscount" onclick="refreshDiscount()" value="Refresh Discount">');
$smarty->assign('sRefreshTotalButton','<input type="button" name="btnRefreshTotal" id="btnRefreshTotal" onclick="refreshTotal()" value="Refresh Totals">');
*/
#document.inputform.submit()
# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','radiology/radio-request-new.tpl');
$smarty->display('common/mainframe.tpl');

?>