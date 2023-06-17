<?php
	# Start Smarty templating here
 /**
 * LOAD Smarty
 */
	
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
 	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	
	require($root_path.'include/inc_environment_global.php');
	require($root_path.'modules/laboratory/ajax/lab-request-new.common.php'); 
	
	#-------------added by VAN ----------
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
	#$phpfd=str_replace("yy","%y", strtolower($phpfd));

	#------------------------------------
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
	define('LANG_FILE','lab.php');
	define('NO_2LEVEL_CHK',1);
	require_once($root_path.'include/inc_front_chain_lang.php');
	# Create laboratory service object
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	$srvObj=new SegLab();
	
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;
	
	require_once($root_path.'include/care_api_classes/class_personell.php');
	$pers_obj=new Personell;

	require_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;
	
	require_once($root_path.'include/care_api_classes/class_person.php');
	$person_obj=new Person();
	
	require('./roots.php');
	require($root_path.'classes/adodb/adodb.inc.php');
	include($root_path.'include/inc_init_hclab_main.php');
	include($root_path.'include/inc_seg_mylib.php');
	
	require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
	$hclabObj = new HCLAB;
	
	global $db;
		
	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');

 	# Title in the title bar
 	$smarty->assign('sToolbarTitle',"$LDLab::Patient Observation Report");

 	# href for the help button
 	$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 	# href for the close button
 	$smarty->assign('breakfile',$breakfile);

	 # Window bar title
	 $smarty->assign('sWindowTitle',"$LDLab::Patient Observation Report");

	 # Assign Body Onload javascript code
 
 	 #$onLoadJS='onUnload="alert(\'trial\');refreshWindow();"';
	 #$onLoadJS='';
	 # Render form values
	 $Ref = $_GET["ref"];
	 $service_code = $_GET['service_code'];
	
	 $onLoadJS='onLoad=preSet(\''.$Ref.'\',\''.$service_code.'\');';
 
 	 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code

ob_start();
	 # Load the javascript code
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
.olfgleft {background-color:#cceecc; text-align: left;}

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

			<!-- START for setting the DATE (NOTE: should be IN this ORDER) -->
<script type="text/javascript" language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>


<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />

<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script language="javascript" src="<?=$root_path?>js/dtpick_care2x.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/request-gui.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
</script>
<?php
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
?>
	
<?php

$sTemp = ob_get_contents();
	
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Assign prompt messages

# Render form values
	#$Ref = $_GET["ref"];
	#$service_code = $_GET['service_code'];
	
	$objconn = $hclabObj->ConnecttoDest($dsn);
	
	$infoResult = $srvObj->getOrderInfo($Ref);
	$saved_discounts = $srvObj->getOrderDiscounts($Ref);
	if ($infoResult)	$info = $infoResult->FetchRow();
	$person = $person_obj->getAllInfoArray($info["pid"]);
	
	#get service info
	$labrequestObj = $srvObj->getServiceRequestInfo($Ref, $service_code);
	
	# Render form values
	$readOnly = "readonly";
	
	/*
	if ($info["pid"]==" "){
		$request_name = $info['ordername'];
		$request_address = $info['orderaddress'];
	}else{
		$person = $person_obj->getAllInfoArray($info["pid"]);
		$request_name = $person['name_first']." ".$person["name_2"]." ".$person["name_last"];
		$request_name = ucwords(strtolower($request_name));
		$request_name = htmlspecialchars($request_name);
		
		$request_address = $person['street_name']." ".$person['brgy_name']." ".$person['mun_name'].", ".$person['prov_name'].", ".$person['region_name']." ".$person['zipcode'];
		$request_address = htmlspecialchars($request_address);
	}	
	
	#REQUEST DETAILS
	# Order No.
	$smarty->assign('sRefNo','<input class="segClearInput" name="refno" id="refno" readonly="1" type="text" size="40" value="'.$Ref.'" style="font:bold 12px Arial;color:#000066;"/>');
	
	if ($info['serv_dt']) {
			$time = strtotime($info['serv_dt']);
			$requestDate = date("m/d/Y",$time);
	}
	
	#Order Date
	$smarty->assign('sOrderDate','<input class="segClearInput" name="orderdate" id="orderdate" type="text" size="40" readonly 
											value="'.$requestDate.'" style="font:bold 12px Arial;color:#000066;">');
	
	#Location
	if (is_numeric($labrequestObj['request_dept'])){	
		$dept = $dept_obj->getDeptAllInfo($labrequestObj['request_dept']);
		$dept_name = stripslashes($dept['name_formal']);
	}else{
		$dept_name = stripslashes($row['request_dept']);
	}
			
	$smarty->assign('sLocation','<input class="segClearInput" name="location" id="location" type="text" size="40" value="'.$dept_name.'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
	#Doctor
	if (is_numeric($labrequestObj['request_doctor'])){
		$doctor = $pers_obj->getPersonellInfo($labrequestObj['request_doctor']);
		$doctor_name = stripslashes($doctor["name_first"])." ".stripslashes($doctor["name_2"])." ".stripslashes($doctor["name_last"]);
		$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
		$doctor_name = htmlspecialchars($doctor_name);
	}else{
		$doctor_name = $row['request_doctor'];
	}	
			
	$smarty->assign('sDoctor','<input class="segClearInput" name="doctor" id="doctor" type="text" size="40" value="'.$doctor_name.'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
	#Priority
	if ($info["is_urgent"])
		$priority = "Routine/Normal";
	else
		$priority = "Urgent/STAT";	
		
	$smarty->assign('sPriority','<input class="segClearInput" name="priority" id="priority" type="text" size="40" value="'.$priority.'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
	#Clinical Info
	$smarty->assign('sClinicalInfo','<textarea id="clinicalinfo" name="clinicalinfo" cols="37" rows="2" style="font:bold 12px Arial;color:#000066;" readonly>'.$labrequestObj["clinical_info"].'</textarea>');
	#Case/Visition No
	$smarty->assign('sVisit','<input class="segClearInput" name="visit" id="visit" type="text" size="40" value="" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
	#Lab No
	$smarty->assign('sLabNo','<input class="segClearInput" name="labno" id="labno" type="text" size="40" value="" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
	
	#Test
	$smarty->assign('sTestName','<input class="segClearInput" name="testname" id="testname" type="text" size="40" value="'.$labrequestObj["name"].'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
	#Test Type
	$smarty->assign('sTestType','<input class="segClearInput" name="testtype" id="testtype" type="text" size="40" value="" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
	#Test Group
	$smarty->assign('sTestGroup','<input class="segClearInput" name="testgroup" id="testgroup" type="text" size="40" value="'.$labrequestObj["group_name"].'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
	#Ctl Seq. No
	$smarty->assign('sControlNo','<input class="segClearInput" name="controlno" id="controlno" type="text" size="40" value="" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
	
	# PATIENT INFO
	#Patient ID
	$smarty->assign('sOrderEncID','<input id="pid" class="segClearInput" name="pid" type="text" size="40" value="'.$info["pid"].'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>');
	#Patient Name
	$smarty->assign('sOrderName','<input class="segClearInput" id="ordername" name="ordername" type="text" size="40" value="'.$request_name.'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>');
	
	#Patient Type
	$encounter = $enc_obj->getPatientEncounter($info['encounter_nr']);
	if ($encounter['encounter_type']==1)
		$patient_type = "ER Patient";
	elseif ($encounter['encounter_type']==2)
		$patient_type = "Outpatient";
	else
		$patient_type = "Inpatient";	
		
	$smarty->assign('sPatientType','<input class="segClearInput" id="patienttype" name="patienttype" type="text" size="40" value="'.$patient_type.'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>');
	#Birth Date
	if ($person['date_birth']) {
			$time = strtotime($person['date_birth']);
			$birthDate = date("m/d/Y",$time);
	}
	$smarty->assign('sBirthDate','<input class="segClearInput" id="bdate" name="bdate" type="text" size="40" value="'.$birthDate.'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>');
	#Sex
	if ($person['sex']=='m'){
		$gender = "Male";
	}elseif ($person['sex']=='f'){
		$gender = "Female";
	}
	
	$smarty->assign('sPatientSex','<input class="segClearInput" id="gender" name="gender" type="text" size="40" value="'.$gender.'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>');
	#Address
	$smarty->assign('sOrderAddress','<textarea  id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial;color:#000066;" readonly>'.$request_address.'</textarea>');
	
	$smarty->assign('sClassification','<input class="segClearInput" type="text" readonly id="discountid" name="discountid" value="'.$info["discountid"].'" style="font:bold 12px Arial;color:#000066;" size="40">');
	*/
	
	if ($objconn) {
		if (($Ref == "2007000001") || ($Ref == "2007000004") || ($Ref == "2007000012")){
			$patient_info = $hclabObj->getResultHeader_to_HCLAB('641297');
			
			#$patient_info = $hclabObj->getResultHeader_to_HCLAB('505745');
			#echo "sql = ".$hclabObj->sql;
		}else
			$patient_info = $hclabObj->getResultHeader_to_HCLAB('8724061');	
		
		#$patient_info = $hclabObj->getResultHeader_to_HCLAB($Ref);
		
		if ($patient_info){
		   #echo "<br>sulod";
		   /*
			$sql = "SELECT ref_no AS refno, ref_source, service_code 
		        FROM seg_pay_request
				  WHERE ref_source = 'LD'
				  AND ref_no = '".$Ref."'
				  AND service_code = '".$service_code."'
				  UNION
				  SELECT ref_no AS refno, ref_source, service_code 
				  FROM seg_granted_request
   			  WHERE ref_source = 'LD'
			     AND ref_no = '".$Ref."'
   			  AND service_code = '".$service_code."'";
			$res = $db->Execute($sql);
			$row=$res->RecordCount();
			if ($row==0){
				echo '<em class="warn">Sorry but the result can\'t be displayed. The request is not yet paid or not in the list of the granted request. Pls. settle this request accounts before you can view the Lab Results. Thank you.!</em>';
				exit();
			}else{
			*/
			#Patient Demographic Information
			#Patient ID
			$smarty->assign('sOrderEncID','<input id="pid" class="segClearInput" name="pid" type="text" size="40" value="'.$patient_info["PRH_PAT_ID"].'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>');
		
			$request_name = stripslashes($patient_info["PRH_PAT_NAME"]);
			$request_name = ucwords(strtolower($request_name));
			$request_name = htmlspecialchars($request_name);
			#Patient Name
			$smarty->assign('sOrderName','<input class="segClearInput" id="ordername" name="ordername" type="text" size="40" value="'.$request_name.'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>');
		
			if ($patient_info["PRH_PAT_TYPE"]=="IN"){
				$patient_type = "Inpatient";
			}elseif ($patient_info["PRH_PAT_TYPE"]=="OP"){
				$patient_type = "Outpatient";
			}elseif ($patient_info["PRH_PAT_TYPE"]=="ER"){
				$patient_type = "ER Patient";
			}	
			#Patient Type
			$smarty->assign('sPatientType','<input class="segClearInput" id="patienttype" name="patienttype" type="text" size="40" value="'.$patient_type.'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>');
		
			if ($patient_info["PRH_PAT_DOB"]) {
				$time = strtotime($patient_info["PRH_PAT_DOB"]);
				$birthDate = date("m/d/Y",$time);
			}
			#Birth Date
			$smarty->assign('sBirthDate','<input class="segClearInput" id="bdate" name="bdate" type="text" size="40" value="'.$birthDate.'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>');
		
			if ($patient_info["PRH_PAT_SEX"]==1){
				$gender = "Male";
			}elseif($patient_info["PRH_PAT_SEX"]==2){
				$gender = "Female";
			}elseif($patient_info["PRH_PAT_SEX"]==0){	
				$gender = "Unknown";
			}
			#Sex
			$smarty->assign('sPatientSex','<input class="segClearInput" id="gender" name="gender" type="text" size="40" value="'.$gender.'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>');
		
			$request_address = $person['street_name']." ".$person['brgy_name']." ".$person['mun_name'].", ".$person['prov_name'].", ".$person['region_name']." ".$person['zipcode'];
			$request_address = htmlspecialchars($request_address);
			#Address
			$smarty->assign('sOrderAddress','<textarea  id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial;color:#000066;" readonly>'.$request_address.'</textarea>');
			#Classification
			$smarty->assign('sClassification','<input class="segClearInput" type="text" readonly id="discountid" name="discountid" value="'.$info["discountid"].'" style="font:bold 12px Arial;color:#000066;" size="40">');
	
			# Patient Request Details
			# Order No.
			$smarty->assign('sRefNo','<input class="segClearInput" name="refno" id="refno" readonly="1" type="text" size="40" value="'.$Ref.'" style="font:bold 12px Arial;color:#000066;"/>');
		
			#Order Date
			$smarty->assign('sOrderDate','<input class="segClearInput" name="orderdate" id="orderdate" type="text" size="40" readonly 
											value="'.$patient_info["PRH_ORDER_DT"].'" style="font:bold 12px Arial;color:#000066;">');
			#Location
			$smarty->assign('sLocation','<input class="segClearInput" name="location" id="location" type="text" size="40" value="'.$patient_info["PRH_LOC_NAME"].'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
		
			$doctor_name = stripslashes($patient_info["PRH_DR_NAME"]);
			#$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
			$doctor_name = ucwords(strtolower($doctor_name));
			$doctor_name = htmlspecialchars($doctor_name);
			#Doctor
			$smarty->assign('sDoctor','<input class="segClearInput" name="doctor" id="doctor" type="text" size="40" value="'.$doctor_name.'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
		
			if ($patient_info["PRH_PRIORITY"]=="R")
				$priority = "Routine/Normal";
			elseif ($patient_info["PRH_PRIORITY"]=="U")
				$priority = "Urgent/STAT";	
			#Priority
			$smarty->assign('sPriority','<input class="segClearInput" name="priority" id="priority" type="text" size="40" value="'.$priority.'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
			#Clinical Info
			$smarty->assign('sClinicalInfo','<textarea id="clinicalinfo" name="clinicalinfo" cols="37" rows="2" style="font:bold 12px Arial;color:#000066;" readonly>'.$patient_info["PRH_CLI_INFO"].'</textarea>');
			#Case/Visition No
			$smarty->assign('sVisit','<input class="segClearInput" name="visit" id="visit" type="text" size="40" value="'.$patient_info["PRH_PAT_CASENO"].'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
			#Lab No
			$smarty->assign('sLabNo','<input class="segClearInput" name="labno" id="labno" type="text" size="40" value="'.$patient_info["PRH_LAB_NO"].'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
			#Test
			$smarty->assign('sTestName','<input class="segClearInput" name="testname" id="testname" type="text" size="40" value="'.$patient_info["PRH_TEST_NAME"].'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
			#Test Type
			$smarty->assign('sTestType','<input class="segClearInput" name="testtype" id="testtype" type="text" size="40" value="'.$patient_info["PRH_TEST_TYPE"].'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
			#Test Group
			$smarty->assign('sTestGroup','<input class="segClearInput" name="testgroup" id="testgroup" type="text" size="40" value="'.$patient_info["PRH_TG_NAME"].'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
			#Ctl Seq. No
			$smarty->assign('sControlNo','<input class="segClearInput" name="controlno" id="controlno" type="text" size="40" value="'.$patient_info["PRH_CTL_SEQNO"].'" style="font:bold 12px Arial; float:left;color:#000066;" readonly/>'); 
		
			if (($Ref == "2007000001") || ($Ref == "2007000004") || ($Ref == "2007000012")){
				$result = $hclabObj->getResult_to_HCLAB('641297','CBCPLT');
				#echo "sql = ".$hclabObj->sql;
				#$result = $hclabObj->getResult_to_HCLAB('505745', 'HBHCTP');
			}else
				$result = $hclabObj->getResult_to_HCLAB('8724061', 'ER');	
			
			#$result = $hclabObj->getResult_to_HCLAB($Ref, $service_code);
			$rowcount = $hclabObj->count;
			if ($rowcount){
				$rows=array();
				while ($row=$result->FetchRow()) {
					$rows[] = $row;
				}
				$count=0;
				#print_r($row);
				foreach ($rows as $i=>$row) {
					if ($row) {
						$count++;
						$alt = ($count%2)+1;
						$src .= 
							'<tr class="wardlistrow'.$alt.'" id="row'.$row['PRH_TRX_NUM'].'">
								<td width="8%" align="center">'.$row['PRD_TEST_CODE'].'</td>
								<td width="15%" align="left">'.$row['PRD_TEST_NAME'].'</td>
								<td width="1%" align="center">'.$row['PRD_RESULT_VALUE'].'</td>
								<td width="1%" align="center">'.$row['PRD_UNIT'].'</td>
								<td width="11%" align="center">'.$row['PRD_RANGE'].'</td>
								<td width="1%" align="center">'.$row['PRD_RESULT_FLAG'].'</td>
								<td width="1%" align="center">'.$row['PRD_RESULT_STATUS'].'</td>
								<td width="10%" align="center">'.$row['PRD_REPORTED_DT'].'</td>
								<td width="7%" align="center">'.$row['PRD_MLT_NAME'].'</td>
								<td width="7%" align="center">'.$row['PRD_PERFORMED_LAB_NAME'].'</td>
								<td width="7%" align="center">'.$row['PRD_TEST_COMMENT'].'</td>
								<td width="7%" align="center">'.$row['PRD_PARENT_ITEM'].'</td>
								<td width="7%" align="center">'.$row['PRD_LINE_NO'].'</td>
							</tr>
						';   
					}
				}
			}else{
				$src .= '<tr><td colspan="13">No laboratory results available at this time...</td></tr>';
			}	
		  #}	
		}else{
			echo '<em class="warn">Sorry but the page cannot be displayed! There is no result at all. Pending Status..</em>';
			exit();
		}	
		
	}else{
	  echo '<em class="warn">Sorry but the page cannot be displayed! There is no result at all. Pending Status..</em>';
		exit();
	}	

 if ($src) $smarty->assign('sResultItems',$src);	

 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform" id="inputform" onSubmit="return prufform()">');
 $smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

?>

<input type="hidden" name="billstatus" id="billstatus" value="">

<?php

#$smarty->assign('sViewPDF','<img name="viewfile" id="viewfile" onClick="viewPatientResult(\''.$Ref.'\',\''.$service_code.'\',\''.$info["pid"].'\');" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'viewpdf.gif','0','left') . ' border="0">');
$smarty->assign('sViewPDF','<img name="viewfile" id="viewfile" onClick="viewPatientResult(\''.$Ref.'\',\''.$service_code.'\');" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'viewpdf.gif','0','left') . ' border="0">');
$smarty->assign('sViewResultPDF','<img name="viewresult" id="viewresult" onClick="viewPatientResult_Summary(\''.$Ref.'\',\''.$service_code.'\');" style="cursor:pointer" align="absmiddle" ' . createLDImgSrc($root_path,'showreport.gif','0','left') . ' border="0">');

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';	
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);

#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" >');
#$smarty->assign('sContinueButton','<img src="'.$root_path.'images/btn_submitorder.gif" border="0" align="center">');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','laboratory/form_result.tpl');
$smarty->display('common/mainframe.tpl');

?>