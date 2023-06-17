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
	$local_user='ck_lab_user';
	define('NO_2LEVEL_CHK',1);

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

	$title="Blood Bank";

	#$breakfile = "labor.php";
	$breakfile = $root_path."modules/laboratory/labor.php";
	$thisfile=basename(__FILE__);

	# Create radiology object
	require_once($root_path.'include/care_api_classes/class_blood_bank.php');
	$blood_Obj=new SegBloodBank();
	
	require_once($root_path.'include/care_api_classes/class_personell.php');
	$pers_obj=new Personell;

	require_once($root_path.'include/care_api_classes/class_radiology.php');	
	$radio_obj = new SegRadio;
	
	require_once($root_path.'include/care_api_classes/class_ward.php');
	$ward_obj = new Ward;
	
	require_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;
	
	#added by VAN 06-25-08
	require_once($root_path.'include/care_api_classes/class_social_service.php');
	$objSS = new SocialService;
		
	#added by VAN 07-08-08
	require_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj = new Person;
	
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;
	#-------------------	
		
		
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
	
	if ($_GET['repeat'])
		$repeat = $_GET['repeat'];
	else
		$repeat = $_POST['repeat'];	
	
    $is_dr = $_GET['is_dr'];      
		
	if ($_GET['prevbatchnr'])
		$prevbatchnr = $_GET['prevbatchnr'];
		
	if ($_GET['prevrefno'])	
		$prevrefno = $_GET['prevrefno'];
	
	$repeaterror = $_GET['repeaterror'];
	
	if ($_GET['encounter_nr'])
		$encounter_nr = $_GET['encounter_nr'];
	
	if ($_GET['area'])
		$area = $_GET['area'];	
	
	if ($_GET['pid'])
		$pid = $_GET['pid'];
    
    if ($encounter_nr){
        $patient = $enc_obj->getEncounterInfo($encounter_nr);
    }
	
	if ($repeaterror){
		$smarty->assign('sysErrorMessage','<strong>Error:</strong> Sorry but you are not allowed to do a repeat request!');
	}
	
	$_POST['request_time'] = date('H:i:s');
	
	switch($mode){
		case 'save':
				if(trim($_POST['request_date'])!=""){
					$_POST['request_date'] = formatDate2STD($_POST['request_date'], $date_format);
				}
				$_POST['clinical_info'] = $_POST['clinicInfo'];	
				$_POST['request_doctor'] = $_POST['requestDoc'];	
				$_POST['is_in_house'] = $_POST['isInHouse'];
				$_POST['service_code'] = $_POST['items'];
				$_POST['is_cash'] = $_POST['iscash'];
				$_POST['is_urgent'] = $_POST['priority'];
				$_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
				$_POST['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";

				if ($repeat01){
					$_POST['parent_batch_nr'] = $_POST['parent_batch_nr'];
					$_POST['parent_refno'] = $_POST['parent_refno'];
					$_POST['approved_by_head'] = $_POST['approved_by_head'];
					$_POST['remarks'] = $_POST['remarks'];
					
					$_POST['headID'] = $_POST['headID'];
					$_POST['headpasswd'] = $_POST['headpasswd'];
				
					#-----------------------------------------
				
					$radio_obj->getStaffInfo($_POST['headID'],$_POST['headpasswd']);
					$isCorrectInfo = $radio_obj->count;
					if ($isCorrectInfo){
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
						$smarty->assign('sysInfoMessage',"Radiological Request Service successfully created.");
					}else{
						$smarty->assign('sysErrorMessage','<strong>Error:</strong> '.$radio_obj->getErrorMsg());
					}
				}	
				
				break;
		case 'update':
				if(trim($_POST['request_date'])!=""){
					$_POST['request_date'] = formatDate2STD($_POST['request_date'], $date_format);
				}
				
				$_POST['clinical_info'] = $_POST['clinicInfo'];	
				$_POST['request_doctor'] = $_POST['requestDoc'];	
				$_POST['is_in_house'] = $_POST['isInHouse'];
				$_POST['service_code'] = $_POST['items'];
				$_POST['is_cash'] = $_POST['iscash'];
				$_POST['is_urgent'] = $_POST['priority'];
				$_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
   				$_POST['history'] = $radio_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");		

				if ($repeat01){
					$_POST['parent_batch_nr'] = $_POST['parent_batch_nr'];
					$_POST['parent_refno'] = $_POST['parent_refno'];
					$_POST['approved_by_head'] = $_POST['approved_by_head'];
					$_POST['remarks'] = $_POST['remarks'];
				
					$_POST['headID'] = $_POST['headID'];
					$_POST['headpasswd'] = $_POST['headpasswd'];
				
					$radio_obj->getStaffInfo($_POST['headID'],$_POST['headpasswd']);
					$isCorrectInfo = $radio_obj->count;
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
						$reloadParentWindow='<script language="javascript">'.
								'	window.parent.jsOnClick(); '.
								'</script>';
						$smarty->assign('sysInfoMessage',"Radiological Request Service successfully updated.");					
					}else{
						$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
					}
				}	
				break;
		case 'cancel':
				if($radio_obj->deleteRefNo($_POST['refno'])){
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
	
	$refInfo = $radio_obj->getRequestInfoByPrevRef($prevrefno,$prevbatchnr);
	if ($refInfo['parent_refno'])
		$refno = $refInfo['refno'];
	
	$mode='save';   # default mode
	if ($refNoBasicInfo = $radio_obj->getBasicRadioServiceInfo($refno)){
		$mode='update';
		extract($refNoBasicInfo);
		if (empty($refNoBasicInfo['pid']) || !$refNoBasicInfo['pid']){
			$person_name = trim($refNoBasicInfo['ordername']);
		}else{
				# in case there is an updated profile of the person
			$person_name = trim($refNoBasicInfo['name_first']).' '.trim($refNoBasicInfo['name_last']);
		}
		$request_date = formatDate2Local($request_date,$date_format); 
	}#end of if-stmt
	elseif (($pid)&&(!empty($area))){
		$patientInfo = $person_obj->getAllInfoArray($pid);
		$person_name = ucwords(strtolower($patientInfo['name_first']))." ".ucwords(strtolower($patientInfo['name_last']));
		
		if ($patientInfo['street_name'])
			$addr_comma = ",";
		$orderaddress = ucwords(strtolower($patientInfo['street_name'])).$addr_comma." ".ucwords(strtolower($patientInfo['brgy_name']))." ".ucwords(strtolower($patientInfo['mun_name']));
		
		$rid = $radio_obj->RIDExists($pid);
	}
	
   if (!(trim($discountid))){
	  	$discountid = $discountid_get;
		
     $socialInfo = $objSS->getSSClassInfo($discountid_get);
	 if (trim($discount)==0)
	 	$discount = $socialInfo['discount'];	
   }
   #--------------------	 

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$title :: Request for Blood Crossmatching");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

if ($popUp!='1'){
		 # href for the close button
	 $smarty->assign('breakfile',$breakfile);
}else{
		# CLOSE button for pop-ups
	 if ($area)
	 	$smarty->assign('breakfile','javascript:window.parent.cClick();');
	 else	
	 	$smarty->assign('breakfile','javascript:window.parent.pSearchClose();');
	 
	 $smarty->assign('pbBack','');
}

 # Window bar title
 $smarty->assign('sWindowTitle',"$title :: Request for Blood Crossmatching");

 # Assign Body Onload javascript code
 
 $onLoadJS='onLoad="CheckRepeatInfo();checkCash();"';
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
<script type="text/javascript" src="js/blood-request-new.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;

	function checkCash(){
		if ($("iscash1").checked){
			document.getElementById('is_cash').value = 1;
			$('type_charge').style.display = '';
			
		}else{
			document.getElementById('is_cash').value = 0;	
			$('type_charge').style.display = '';
		}	
	}

	function CheckRepeatInfo(){
		if (document.getElementById('repeat').checked){
			document.getElementById('repeatinfo01').style.display = '';
			document.getElementById('repeatinfo02').style.display = '';
			document.getElementById('repeatinfo03').style.display = '';
			
			document.getElementById('repeatinfo04').style.display = '';
			document.getElementById('repeatinfo05').style.display = '';
			
			document.getElementById('show-discount').value = formatNumber(0,2);
		}else	{
			document.getElementById('repeatinfo01').style.display = 'none';
			document.getElementById('repeatinfo02').style.display = 'none';
			document.getElementById('repeatinfo03').style.display = 'none';
			
			document.getElementById('repeatinfo04').style.display = 'none';
			document.getElementById('repeatinfo05').style.display = 'none';
		}	
	}	
	

	function NewRequest(){
		urlholder="seg-blood-request-new.php<?=URL_APPEND?>&user_origin=<?=$user_origin?>";
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

	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$pid.'"/>');
	#$smarty->assign('sRID','<input class="segInput" id="rid" name="rid" type="text" size="10" value="'.$rid.'" style="font:bold 12px Arial;" readonly>');
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$person_name.'" style="font:bold 12px Arial;" readonly>');
	
	$smarty->assign('sAge','<input class="segInput" id="age" name="age" type="text" size="1" value="'.$age.'" style="font:bold 12px Arial;" readonly>');
	$smarty->assign('sSex','<input class="segInput" id="sex" name="sex" type="text" size="5" value="'.$sex.'" style="font:bold 12px Arial;" readonly>');
	$smarty->assign('sCivilStatus','<input class="segInput" id="civilstat" name="civilstat" type="text" size="5" value="'.$civilstat.'" style="font:bold 12px Arial;" readonly>');
	
	if (empty($doctor_nr))
		$cond = "";
	else
		$cond = " AND personell_nr='".$doctor_nr."'";
			
	$doctors = $pers_obj->getDoctors(1, $cond);	
	
	if (is_object($doctors)){	
		while($drInfo=$doctors->FetchRow()){
	
		$middleInitial = "";
		if (trim($drInfo['name_middle'])!=""){
			$thisMI=split(" ",$drInfo['name_middle']);	
			foreach($thisMI as $value){
				if (!trim($value)=="")
					$middleInitial .= $value[0];
			}
			if (trim($middleInitial)!="")
				$middleInitial .= ". ";
		}
			
		$name_doctor = trim($drInfo["name_last"]).", ".trim($drInfo["name_first"])." ".$middleInitial; #substr(trim($drInfo["name_middle"]),0,1).$dot;
		$name_doctor = ucwords(strtolower($name_doctor)).", MD";
		
		#$listDoctors[$drInfo["personell_nr"]]=$name_doctor;
		
		$options_doctor.='<option value="'.$drInfo["personell_nr"].'">'.$name_doctor.'</option>';
		}	
 	}
 
 	$smarty->assign('sDoctor',"<select name=\"doctor_nr\" id=\"doctor_nr\">
											   <option value=\"0\">-Select a Doctor-</option>
												 $options_doctor
											 </select>");
	
	if (empty($blood_type))
		$blood_type = "";
		
	$btype_res = $blood_Obj->getAllBloodType($blood_type);
	
	if (is_object($btype_res)){	
		while ($row_btype=$btype_res->FetchRow()) {
			$options_btype.='<option value="'.$row_btype['code'].'">'.$row_btype['name'].'</option>';
		}
	}	
	$smarty->assign('sBloodType',"<select name=\"blood_type\" id=\"blood_type\">
											   <option value=\"0\">-Select a Blood Type-</option>
												 $options_btype
											 </select>");
											 
	$smarty->assign('sBloodSource','<input class="segInput" id="blood_source" name="blood_source" type="text" size="25" value="'.$blood_source.'" style="font:bold 12px Arial;">');
	
	if (empty($blood_component))
		$blood_component = "";
		
	$bcomponent_res = $blood_Obj->getAllBloodComponents($blood_component);
	
	if (is_object($bcomponent_res)){	
		while ($row_component=$bcomponent_res->FetchRow()) {
			$options_component.='<option value="'.$row_component['code'].'">'.$row_component['name'].'</option>';
		}
	}	
	$smarty->assign('sBloodComponent',"<select name=\"blood_component\" id=\"blood_component\">
											   <option value=\"0\">-Select a Blood Component-</option>
												 $options_component
											 </select>");
	
	$smarty->assign('sSerialNo','<input class="segInput" id="serial_no" name="serial_no" type="text" size="25" value="'.$serial_no.'" style="font:bold 12px Arial;">');
	
	$smarty->assign('sDateExtract','<input class="segInput" id="date_extracted" name="date_extracted" type="text" size="25" value="'.$date_extracted.'" style="font:bold 12px Arial;">');
	$smarty->assign('sDateExpiry','<input class="segInput" id="expiry_date" name="expiry_date" type="text" size="25" value="'.$expiry_date.'" style="font:bold 12px Arial;">');
	$smarty->assign('sRhType','<input class="segInput" id="RhType" name="RhType" type="text" size="25" value="'.$RhType.'" style="font:bold 12px Arial;">');
	
	$smarty->assign('sHbsAG','<input class="segInput" id="HbsAG" name="HbsAG" type="text" size="25" value="'.$HbsAG.'" style="font:bold 12px Arial;">');
	$smarty->assign('sHCV','<input class="segInput" id="HCV" name="HCV" type="text" size="25" value="'.$HCV.'" style="font:bold 12px Arial;">');
	$smarty->assign('sHIV','<input class="segInput" id="HIV" name="HIV" type="text" size="25" value="'.$HIV.'" style="font:bold 12px Arial;">');
	$smarty->assign('sVDRL','<input class="segInput" id="VDRL" name="VDRL" type="text" size="25" value="'.$VDRL.'" style="font:bold 12px Arial;">');
	$smarty->assign('sVCV','<input class="segInput" id="VCV" name="VCV" type="text" size="25" value="'.$VCV.'" style="font:bold 12px Arial;">');
	$smarty->assign('sBSMP','<input class="segInput" id="BSMP" name="BSMP" type="text" size="25" value="'.$BSMP.'" style="font:bold 12px Arial;">');
	
	$var_arr = array(
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

	 
	 if ($area){
	  	$smarty->assign('sSelectEnc','<img name="select-enc" id="select-enc" src="'.$root_path.'images/btn_encounter_small.gif" border="0">');
	 }else{	
	   $smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer"
       onclick="if (warnClear()) {  clearEncounter(); overlib(
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

	if ($area=="ER"){
			$enctype = "ER PATIENT";	
			$location = "EMERGENCY ROOM";
			$encounter_type = 1;
		
			$medico = $enc_obj->getEncounterMedicoCases($encounter_nr, $pid);
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
			$dept = $enc_obj->getEncounterDept($encounter_nr);
			$location = mb_strtoupper($dept['name_formal']);
		
			$medico = $enc_obj->getEncounterMedicoCases($encounter_nr, $pid);
			if ($medico)
				$info = $medico->FetchRow();			
	}else{	
		if ($encounter_type==1){
			$enctype = "ER PATIENT";
			$location = "EMERGENCY ROOM";
		}elseif ($encounter_type==2){
			$enctype = "OUTPATIENT";
			$dept = $dept_obj->getDeptAllInfo($current_dept_nr);
			$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
		}elseif (($encounter_type==3)||($encounter_type==4)){
			if ($encounter_type==3)
				$enctype = "INPATIENT (ER)";
			elseif ($encounter_type==4)
				$enctype = "INPATIENT (OPD)";
				
			$ward = $ward_obj->getWardInfo($current_ward_nr);
			$location = strtoupper(strtolower(stripslashes($ward['name'])))."&nbsp;&nbsp;&nbsp;Room # : ".$current_room_nr;
		}else{
			$enctype = "WALK-IN";
			$dept = $dept_obj->getDeptAllInfo($current_dept_nr);
			$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
		}
	}	
	
	if ($_GET['view_from']=='ssview'){
		$discountid = $_GET['discountid'];
	}
	
	if((!($discountid))&&($encounter_type)){
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
	
	if (empty($pid))
		$pid = $info['pid'];
		
		$ss_sc = $objSS->getPatientSocialClass($pid, 0);
		
		if ($ss_sc['discountid']=='SC')
			$_POST["issc"] = 1;
		else	
			$_POST["issc"] = 0;
	
		if (($_POST["issc"])&&(trim($encounter_type)==""))
			$discount = 0.20;	

	$smarty->assign('sDiscountShow','<input type="checkbox" disabled name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' ><label for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
	
	if (((isset($_POST['select-enc']))||($mode=='update'))||($area)){
		$smarty->assign('sClassification',(($discountid) ? $discountid : 'None'));
		$smarty->assign('sPatientType',(($enctype) ? $enctype : 'None'));
		$smarty->assign('sPatientLoc',(($location) ? $location : 'None'));
		$smarty->assign('sPatientMedicoLegal',(($is_medico) ? "YES" : 'NO'));
	}
	
	if (($repeat)&&(empty($refInfo['parent_refno'])))
		$Ref = "";
	else{
		if ($area){
	   		$Ref = $refno;
				$Ref2 = $refno;
	   }else{	
			$radio_obj->getSumPaidPerTransaction($refno);
		
			if ($radio_obj->count){
				$Ref2 = $refno;
			}else{
				if(($is_cash==0) || ($discount==1.00) || ($type_charge))
					$Ref2 = $refno;	
				else
					$Ref2 = "";	
					
			}	
			$Ref = $refno;
		}	
	}	
	$smarty->assign('sRefNo','<input class="segInput" name="refno" id="refno" type="text" size="10" value="'.$Ref2.'" readonly style="font:bold 12px Arial"/>');

	if (($parent_refno)&&($parent_batch_nr)){
		$repeat=1;
	}
	
	if (empty($parent_refno))
		$parent_refno = $refno;
	elseif ($prevrefno)	
		$parent_refno = $prevrefno;
	
	if ((empty($parent_batch_nr))||($prevbatchnr))
		$parent_batch_nr = $prevbatchnr;
	
	$smarty->assign('sRepeat','<input type="checkbox" name="repeat" id="repeat" value="yes" '.(($repeat=="1")?'checked="checked" ':'').' disabled>');
	$smarty->assign('sParentBatchNr','<input class="segInput" id="parent_refno" name="parent_refno" type="text" size="40" value="'.$parent_refno.'" style="font:bold 12px Arial;" readonly/><input id="parent_batch_nr" name="parent_batch_nr" type="hidden" size="40" value="'.$parent_batch_nr.'" style="font:bold 12px Arial;"/>');
	$smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="37" rows="2" style="font:bold 12px Arial">'.stripslashes($remarks).'</textarea>');
	$smarty->assign('sHead','<input class="segInput" id="approved_by_head" name="approved_by_head" type="text" size="40" value="'.$approved_by_head.'" style="font:bold 12px Arial;"/>');
	
	$smarty->assign('sHeadID','<input class="segInput" id="headID" name="headID" type="text" size="40" value="" style="font:bold 12px Arial;"/>');
	$smarty->assign('sHeadPassword','<input class="segInput" id="headpasswd" name="headpasswd" type="password" size="40" value="" style="font:bold 12px Arial;"/>');
	
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

	$smarty->assign('sOrderDate','<input name="request_date" id="request_date" type="text" size="10" 
											value="'.$curDate.'" style="font:bold 12px Arial"
											onFocus="this.select();"  
											onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
											onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
											onKeyUp="setDate(this,\'MM/dd/yyyy\',\'en\')">');

	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="request_date_trigger" name="request_date_trigger" align="absmiddle" style="cursor:pointer">'.$jsCalScript);
	
	$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority0" value="0"'.($is_urgent? "": " checked").'>Routine');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority1" value="1"'.($is_urgent? " checked": "").'>STAT');
	
	if ($area=="ER"){
		$smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" '.(($pid)?'':'checked="checked" ').' '.(($pid)?'disabled="disabled" ':'').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
		$smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" '.(($pid)?'checked="checked" ':'').' '.(($pid)?'':'disabled="disabled" ').' onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
	}elseif ($area=="clinic"){
         
        if ($is_dr){
            if (($patient['encounter_type']==3)||($patient['encounter_type']==4)){
                $smarty->assign('sIsCash','<input class="jedInput" type="radio" name="iscash" id="iscash1" value="1" checked onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else {return false;}" /><label for="iscash1" class="jedInput">Cash</label>');
                $smarty->assign('sIsCharge','<input class="jedInput" type="radio" name="iscash" id="iscash0" value="0" onclick="if (warnClear()) { emptyTray(); changeTransactionType(); return true;} else return false;" style="margin-left:10px" /><label class="jedInput" for="iscash0">Charge</label>');
            }elseif($patient['encounter_type']==1){
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
		
	$result = $radio_obj->getChargeType();
	$options="";
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
	$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.$comments.'</textarea>');
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"8\">Request list is currently empty...</td>
				</tr>");
/*
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

$smarty->assign('sBtnEmptyList','<img type="image" name="btnEmpty" id="btnEmpty" src="'.$root_path.'images/btn_emptylist.gif" border="0" style="cursor:pointer;" onclick="emptyTray();"></a>');
*/
$smarty->assign('sAdjustedAmount','<input id="show-discount" name="show-discount" type="hidden" onBlur="formatDiscount(this.value);" readonly style="color:#006600; font-family:Arial; font-size:15px; font-weight:bold; text-align:right" size="5" value="'.number_format($adjusted_amount,2).'"/>');

if ($view_from=='ssview'){ 
	if ($discountid)
		$smarty->assign('sBtnDiscounts','<img name="btndiscount" id="btndiscount" onclick="saveDiscounts2();" style="cursor:pointer" src="'.$root_path.'images/btn_discounts2.gif" border="0">');
	else
		$smarty->assign('sBtnDiscounts','<img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_discounts2.gif" border="0" style="display:none">');
}elseif ($hasPaid==1){
	$smarty->assign('sBtnDiscounts','<img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_discounts2.gif" border="0" style="display:none">');
}else{
	$smarty->assign('sBtnDiscounts','<img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_discounts2.gif" border="0" style="display:none">');
}

$smarty->assign('sSocialServiceNotes','<img src="'.$root_path.'images/btn_nonsocialized.gif"> <span style="font-style:italic">Nonsocialized service.</span>');

 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'?popUp='.$popUp.'" method="POST" name="inputform" id="inputform" onSubmit="return checkRequestForm()">');
 $smarty->assign('sFormEnd','</form>');
 
?>
<?php
ob_start();
$sTemp='';

if (!empty($Ref)){
	$refno = $Ref;
}else{
	if ($refInfo['parent_batch_nr'])
		$batchnr = $refInfo['batch_nr'];
	else	
		$batchnr = $prevbatchnr;
}

?>
	<script type="text/javascript" language="javascript">
		preset(<?= ($is_cash=='0')? "0":"1"?>);
		xajax_populateRequestListByRefNo(<?=$refno? $refno:0?>,<?=$batchnr? $batchnr:0?>);	
		
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
		
		if(($_POST['issc'])&&(trim($encounter_type)=="")){
			$discount = 0.20;
		}

		if (empty($orig_discountid)){
			$sql_discount = "SELECT discountid  FROM seg_charity_grants_pid   WHERE pid='".$pid."' ORDER BY grant_dte DESC LIMIT 1";
  
		   $res_discount=$db->Execute($sql_discount);
		   $discount_info=$res_discount->FetchRow();
    
		   $orig_discountid = $discount_info['discountid'];
		}
	?>
	
	<input type="hidden" name="is_cash" id="is_cash" value="<?=$is_cash?>" >
	<input type="hidden" id="encounter_nr" name="encounter_nr" value="<?=$encounter_nr?>">
	<input type="hidden" name="discount2" id="discount2" value="<?=$discount2?>" >
	<input type="hidden" name="discount" id="discount" value="<?=$discount?>" >
	<input type="hidden" name="latest_valid_show-discount" id="latest_valid_show-discount" value="<?=number_format($adjusted_amount, 2, '.', '')?>" >
	
	<input type="hidden" id="orig_discountid" name="orig_discountid" value="<?=$orig_discountid?>">
    <input type="hidden" id="discountid" name="discountid" value="<?=$discountid;?>">
	
	<?php 
		if ((empty($refInfo['parent_batch_nr']))&&(empty($refInfo['parent_refno']))&&(empty($Ref)))
			$mode='save';		
		else
			$mode='update';		
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
if (($mode=="update") && ($popUp!='1')){
	$sBreakImg ='cancel.gif';
	$smarty->assign('sBreakButton','<img type="image" '.createLDImgSrc($root_path,$sBreakImg,'0','center').' align="center" alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';" style="cursor:pointer">');
}elseif ($popUp!='1'){
	$sBreakImg ='close2.gif';	
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
}

$smarty->assign('sContinueButton','<img type="image" name="btnSubmit" id="btnSubmit" src="'.$root_path.'images/btn_submitorder.gif" align="center" style="cursor:pointer" onclick="if (confirm(\'Process this radiology request?\')) if (checkRequestForm()) document.inputform.submit()">');

if (($hasPaid)|| ((($encounter_type!='')||($encounter_type!=NULL)) && ($encounter_type!=2)) || ($type_charge) || $repeat)
	$smarty->assign('sClaimStub','<img name="claimstub" id="claimstub" onClick="viewClaimStub(\''.$is_cash.'\',\''.$refno.'\');" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'claim_stub.gif','0','left') . ' border="0">');


# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','blood/blood-request-new.tpl');
$smarty->display('common/mainframe.tpl');

?>