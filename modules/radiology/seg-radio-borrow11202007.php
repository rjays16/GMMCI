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
	$local_user='ck_radio_user';
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/inc_front_chain_lang.php');
	require($root_path.'modules/radiology/ajax/radio-patient-common.php');

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
	
#	global $db;
	
	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');


	switch($mode){
		case 'save':
	#echo "seg-radio-patient.php : save mode = '".$mode."' <br> \n";
	#echo "seg-radio-patient.php : _POST : "; print_r($_POST); echo " <br><br> \n";
	#echo "seg-radio-patient.php : _POST['findings_date'] = '".formatDate2STD($_POST['findings_date'], $date_format)."' <br> \n";
				if(trim($_POST['request_date'])!=""){
					$_POST['request_date'] = formatDate2STD($_POST['request_date'], $date_format);
				}
				$_POST['clinical_info'] = $_POST['clinicInfo'];	
				$_POST['request_doctor'] = $_POST['requestDoc'];	
				$_POST['is_in_house'] = $_POST['isInHouse'];
				$_POST['service_code'] = $_POST['items'];
				$_POST['is_cash'] = $_POST['iscash'];
				$_POST['is_urgent'] = $_POST['priority'];
				$_POST['hasPaid'] = 0;   # not yet paid since this is just a request
				$_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
				$_POST['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
#	echo "seg-radio-patient.php : save 2: _POST : "; print_r($_POST); echo " <br><br> \n";
#	exit();
				if($refno = $radio_obj->saveRadioRefNoInfoFromArray($_POST)){
#					$errorMsg='<font style="color:#FF0000">Successfully saved!</font>';
					$smarty->assign('sWarning',"Radiological Request Service successfully created.");
				}else{
					# $errorMsg = $db->ErrorMsg();
#					$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
					$smarty->assign('sWarning','<strong>Error:</strong> '.$radio_obj->getErrorMsg());
#					$smarty->assign('sWarning','<strong>Error:</strong> '.$radio_obj->getErrorMsg());
				}
				break;
		case 'update':
	#echo "seg-radio-patient.php : update mode = '".$mode."' <br> \n";			
	#echo "seg-radio-patient.php : _POST : "; print_r($_POST); echo " <br><br> \n";

	#			if($radio_obj->saveAFinding($batch_nr,$finding_nr,$findings,$radio_impression,$findings_date,$doctor_id,'Update')){
				if(trim($_POST['request_date'])!=""){
					$_POST['request_date'] = formatDate2STD($_POST['request_date'], $date_format);
				}
				$_POST['clinical_info'] = $_POST['clinicInfo'];	
				$_POST['request_doctor'] = $_POST['requestDoc'];	
				$_POST['is_in_house'] = $_POST['isInHouse'];
				$_POST['service_code'] = $_POST['items'];
				$_POST['is_cash'] = $_POST['iscash'];
				$_POST['is_urgent'] = $_POST['priority'];
				$_POST['hasPaid'] = 0;   # not yet paid since this is just a request
				$_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
   			$_POST['history'] = $radio_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");		

#	echo "seg-radio-patient.php : save 2: _POST : "; print_r($_POST); echo " <br><br> \n";
				if($radio_obj->updateRadioRefNoInfoFromArray($_POST)){
#					$errorMsg='<font style="color:#FF0000">Successfully updated!</font>';
					$reloadParentWindow='<script language="javascript">'.
								'	window.parent.jsOnClick(); '.
#								'	javascript:self.parent.location.href=self.parent.location.href;'.
								'</script>';
					$smarty->assign('sWarning',"Radiological Request Service successfully updated.");					
				}else{
					$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
				}
				break;
		case 'cancel':
#	echo "seg-radio-patient.php : cancel mode = '".$mode."' <br> \n";			
#	echo "seg-radio-patient.php : _POST : "; print_r($_POST); echo " <br><br> \n";
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

	if (!isset($refno) || !$refno){
		if (isset($_GET['refno']) && $_GET['refno']){
			$refno = $_GET['refno'];
		}
		if (isset($_POST['refno']) && $_POST['refno']){
			$refno = $_POST['refno'];
		}
	}
#echo "seg-radio-patient.php : refno='".$refno."' <br> \n";			

if (isset($_GET['pid']) && $_GET['pid']){
	$pid = $_GET['pid'];
}
if (isset($_POST['pid']) && $_POST['pid']){
	$pid = $_POST['pid'];
}

if (isset($_GET['rid']) && $_GET['rid']){
	$rid = $_GET['rid'];
}
if (isset($_POST['rid']) && $_POST['rid']){
	$rid = $_POST['rid'];
}

if (isset($_GET['batchNo']) && $_GET['batchNo']){
	$batchNo = $_GET['batchNo'];
}
if (isset($_POST['batchNo']) && $_POST['batchNo']){
	$batchNo = $_POST['batchNo'];
}

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;

if ($pid && $rid){
	if (!($basicInfo=$person_obj->getAllInfoArray($pid))){
		echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
		exit();
	}
#	echo "basicInfo : <br> \n"; print_r($basicInfo); echo "<br>\n";
	extract($basicInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid PID or RID!</em>';
	exit();
}
#echo "seg-radio-patient.php : basicInfo='".$basicInfo."' <br> \n";			
#echo "seg-radio-patient.php : basicInfo : <br> \n"; print_r($basicInfo); echo" <br> \n";			

/*
	$mode='save';   # default mode
	if ($refNoBasicInfo = $radio_obj->getBasicRadioServiceInfo($refno)){
		$mode='update';
		extract($refNoBasicInfo);
		if (empty($refNoBasicInfo['pid']) || !$refNoBasicInfo['pid']){
			$person_name = $refNoBasicInfo['ordername'];
		}else{
				# in case there is an updated profile of the person
			$person_name = $refNoBasicInfo['name_first'].' '.$refNoBasicInfo['name_last'];
		}
#echo "seg-radio-patient.php : before : request_date='".$request_date."' <br> \n";			
		$request_date = formatDate2Local($request_date,$date_format); 
#echo "seg-radio-patient.php : after : request_date='".$request_date."' <br> \n";			
	}#end of if-stmt
*/
#echo "seg-radio-patient.php : mode='".$mode."' <br> \n";			
#echo "seg-radio-patient.php : refNoInfo : "; print_r($refNoInfo); echo " <br><br> \n";

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$LDRadiology::Patient's Records");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

	# CLOSE button for pop-ups
#$smarty->assign('breakfile','javascript:window.parent.pSearchClose();');
$smarty->assign('breakfile','javascript:closeThisWindow();');
$smarty->assign('pbBack','');


 # Window bar title
# $smarty->assign('sWindowTitle',"$LDRadiology::$LDDiagnosticTest");
 $smarty->assign('sWindowTitle',"$LDRadiology::Patient's Records");

 # Assign Body Onload javascript code
 
# $onLoadJS='onLoad="preSet();"';
 #echo "onLoadJS = ".$onLoadJS;
 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code

ob_start();
	 # Load the javascript code
    $xajax->printJavascript($root_path.'classes/xajax-0.2.5');	 
?>

<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/radio-patient-record.js?t=<?=time()?>"></script>

<!--Include dojo toolkit -->
<script type="text/javascript" src="<?=$root_path?>js/dojo/dojo.js"></script>
<!-- Include dojoTab Dependencies -->
<script type="text/javascript">
	dojo.require("dojo.widget.TabContainer");
	dojo.require("dojo.widget.LinkPane");
	dojo.require("dojo.widget.ContentPane");
	dojo.require("dojo.widget.LayoutContainer");
	dojo.require("dojo.event.*");
</script>
<script language="javascript">
	//dojo.addOnLoad(evtOnClick);
</script>

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
<?php
	if ($popUp=='1'){
		echo $reloadParentWindow;
	}
	$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->append('JavaScript',$sTemp);

			# burn added : March 26, 2007
			if($date_birth){
				$sBdayBuffer = @formatDate2Local($date_birth,$date_format);			
				if (!($age = $person_obj->getAge($sBdayBuffer))){
					$age = '';
					$sBdayBuffer = 'Not Available';
				}else{
						$smarty->assign('sAge','<span class="vi_data">'.$age.' </span> year(s) old');
				}
			}
	if ($sex=='f'){
		$gender = "female";
	}else if($sex=='m'){
		$gender = "male";	
	}
	$sAddress = trim($street_name);
	if (!empty($sAddress) && !empty($brgy_name))
		$sAddress= trim($sAddress.", ".$brgy_name);
	else
		$sAddress = trim($sAddress." ".$brgy_name);
	if (!empty($sAddress) && !empty($mun_name))
		$sAddress= trim($sAddress.", ".$mun_name);
	else
		$sAddress = trim($sAddress." ".$mun_name);
	if (!empty($zipcode))
		$sAddress= trim($sAddress." ".$zipcode);
	if (!empty($sAddress) && !empty($prov_name))
		$sAddress= trim($sAddress.", ".$prov_name);
	else
		$sAddress = trim($sAddress." ".$prov_name);

$smarty->assign('sPanelHeader','Roentgenological Record :: '.$name_first.' <span style="font-style:italic">'.$name_middle.'</span> '.$name_last);
$smarty->assign('sPID',$pid.'<input type="hidden" name="pid" id="pid" value="'.($pid? $pid:"0").'">');
$smarty->assign('sRID',$rid.'<input type="hidden" name="rid" id="rid" value="'.($rid? $rid:"0").'">');
$smarty->assign('sName',$name_first.' <span style="font-style:italic">'.$name_middle.'</span> '.$name_last);
$smarty->assign('sBirthdate',$sBdayBuffer);
$smarty->assign('sGender',$gender);
$smarty->assign('sAddress',trim($sAddress));

if (isset($_GET['available']) && $_GET['available']){
	$available = $_GET['available'];
}
if (isset($_POST['available']) && $_POST['available']){
	$available = $_POST['available'];
}

#echo "seg-radio-borrow.php :: available ='".$available."' <br> \n";

require_once($root_path.'include/care_api_classes/class_radiology.php');
$objRadio = new SegRadio;

	if ($batchNo){
		$recordBorrowObj = $objRadio->getRadioPatientRecordBorrowInfo($batchNo);
		if (($available==0) && is_object($recordBorrowObj)){
			$lastestRecordBorrowInfo = $recordBorrowObj->FetchRow();
echo "seg-radio-borrow.php :: lastestRecordBorrowInfo : <br> \n"; print_r($lastestRecordBorrowInfo); echo "<br>\n";
			extract($lastestRecordBorrowInfo);
			$mode=$status;
		}else{
#			echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
#			exit();
		}
	}else{
		echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Batch Number!</em>';
		exit();
	}
echo "seg-radio-borrow.php :: recordBorrowObj='".$recordBorrowObj."' <br> \n";
echo "seg-radio-borrow.php :: lastestRecordBorrowInfo='".$lastestRecordBorrowInfo."' <br> \n";

		# FORMATTING of Date Borrowed
	if (($date_borrowed!='0000-00-00')  && ($date_borrowed!=""))
		$date_borrowed = @formatDate2Local($date_borrowed,$date_format);
	else
		$date_borrowed=date('m/d/Y');
					
	$sDateBorrowed= '<input name="date_borrowed" type="text" size="15" maxlength=10 value="'.$date_borrowed.'"'. 
									'onFocus="this.select();"  
									id = "date_borrowed" 
									onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
									onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
									onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
									<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="date_borrowed_trigger" style="cursor:pointer" >
									<font size=2>['; 			
	ob_start();
?>
	<script type="text/javascript">
			Calendar.setup ({
					inputField : "date_borrowed", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_borrowed_trigger", singleClick : true, step : 1
			});
	</script>
<?php
	$calendarSetup = ob_get_contents();
	ob_end_clean();
				
	$sDateBorrowed .= $calendarSetup;
						
	$dfbuffer="LD_".strtr($date_format,".-/","phs");
	$sDateBorrowed = $sDateBorrowed.$$dfbuffer.']';

		# FORMATTING of Date Returned
	if (($date_returned!='0000-00-00')  && ($date_returned!=""))
		$date_returned = @formatDate2Local($date_returned,$date_format);
	else
		$date_returned='';
					
	$sDateReturned= '<input name="date_returned" type="text" size="15" maxlength=10 value="'.$date_returned.'"'. 
									'onFocus="this.select();"  
									id = "date_returned" 
									onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
									onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
									onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
									<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="date_returned_trigger" style="cursor:pointer" >
									<font size=2>['; 			
	ob_start();
?>
	<script type="text/javascript">
			Calendar.setup ({
					inputField : "date_returned", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_returned_trigger", singleClick : true, step : 1
			});
	</script>
<?php
	$calendarSetup = ob_get_contents();
	ob_end_clean();
				
	$sDateReturned .= $calendarSetup;
						
	$dfbuffer="LD_".strtr($date_format,".-/","phs");
	$sDateReturned = $sDateReturned.$$dfbuffer.']';

		# FORMATTING of Time Borrowed
	$sTimeBorrowed = "\n";
	$sTimeBorrowed .= '<input type="text" id="time_borrowed" name="time_borrowed" value="'.$time_borrowed.'" size="4" maxlength="5" onChange="setFormatTime(this,\'selAMPM_borrowed\')">&nbsp;';
	$sTimeBorrowed .= "\n".
							'<select id="selAMPM_borrowed" name="selAMPM_borrowed">'."\n".
							'	<option value="A.M.">A.M.</option>'."\n".
							'	<option value="P.M.">P.M.</option>'."\n";
	$sTimeBorrowed .= "</select> \n";
if ($time_borrowed){
	$sTimeBorrowed .= '<script language="javascript">'."\n".
							'	setFormatTime($(\'time_borrowed\'),\'selAMPM_borrowed\')'.
	 						'</script>';
}

		# FORMATTING of Time Returned
	$sTimeReturned = "\n";
	$sTimeReturned .= '<input type="text" id="time_returned" name="time_returned" value="'.$time_returned.'" size="4" maxlength="5" onChange="setFormatTime(this,\'selAMPM_returned\')">&nbsp;';
	$sTimeReturned .= "\n".
							'<select id="selAMPM_returned" name="selAMPM_returned">'."\n".
							'	<option value="A.M.">A.M.</option>'."\n".
							'	<option value="P.M.">P.M.</option>'."\n";
	$sTimeReturned .= "</select> \n";

if ($time_returned){
	$sTimeReturned .= '<script language="javascript">'."\n".
							'	setFormatTime($(\'time_returned\'),\'selAMPM_returned\')'.
	 						'</script>';
}

$sBorrower='
		<select name="borrower_dept" id="borrower_dept" onChange="jsSetDoctorsOfDept();">
		</select>
		<br>
		<select name="borrower_id" id="borrower_id" onChange="jsSetDepartmentOfDoc();">
		</select>
		<script language="javascript">
			xajax_setALLDepartment(0);	//set the list of ALL departments
			xajax_setDoctors(0,0);	//set the list of ALL doctors from ALL departments';		
if ($borrower_id){
	$sBorrower .= '
			xajax_setDepartmentOfDoc('.$borrower_id.');	//set the borrower (doctor) & his department ';
}
$sBorrower .= '
			</script>';

$smarty->assign('sPanelHeaderBorrow','Film Borrowing/Releasing Form');
$smarty->assign('sPanelHeaderReturn','Film Return Form');
/*
batch_nr
borrower_id
date_borrowed
time_borrowed
date_returned
time_returned
releaser_id
remarks
*/

$smarty->assign('sBatchNr',$batchNo."\n".
									'<input type="hidden" name="batchNo" id="batchNo" value="'.($batchNo? $batchNo:"0").'">'."\n".
									'<input type="hidden" name="borrow_nr" id="borrow_nr" value="'.($borrow_nr? $borrow_nr:"0").'">');
$smarty->assign('sBorrower',"\n".$sBorrower."\n");
$smarty->assign('sDateBorrowed',"\n".$sDateBorrowed."\n");
$smarty->assign('sTimeBorrowed',"\n".$sTimeBorrowed."\n");
$smarty->assign('sDateReturned',"\n".$sDateReturned."\n");
$smarty->assign('sTimeReturned',"\n".$sTimeReturned."\n");
$smarty->assign('sRemarks','<textarea class="segInput" name="remarks" id="remarks" cols="50" rows="3" onChange="trimString(this,true);" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.$remarks.'</textarea>');
$smarty->assign('sFilmReleaser','<input type="hidden" name="releaser_id" id="releaser_id" value="'.$releaser_id.'">
										<input type="text" name="releaser_fullname" id="releaser_fullname" value="'.$releaser_name.'" disabled>');
$smarty->assign('sNewFilmReleaser','<input type="hidden" name="releaser_id_new" id="releaser_id_new" value="'.$HTTP_SESSION_VARS['sess_temp_personell_nr'].'">
										<input type="hidden" name="releaser_fullname_new" id="releaser_fullname_new" value="'.$HTTP_SESSION_VARS['sess_temp_fullname'].'" disabled>');

$smarty->assign('sFilmReceiver','<input type="hidden" name="receiver_id" id="receiver_id" value="'.$receiver_id.'">
										<input type="text" name="receiver_fullname" id="receiver_fullname" value="'.$receiver_name.'" disabled>');
$smarty->assign('sNewFilmReceiver','<input type="hidden" name="receiver_id_new" id="receiver_id_new" value="'.$HTTP_SESSION_VARS['sess_temp_personell_nr'].'">
										<input type="hidden" name="releaser_fullname_new" id="receiver_fullname_new" value="'.$HTTP_SESSION_VARS['sess_temp_fullname'].'" disabled>');

$smarty->assign('sBorrowButton','<input type="image" name="btnBorrow" id="btnBorrow" src="'.$root_path.'images/btn_borrow.gif" align="center" onClick="if (checkBorrowForm(0)) jsSaveBorrow();">');
$image_update = createLDImgSrc($root_path,'update.gif','0');
$smarty->assign('sUpdateBorrowButton','<input type="image" name="btnUpdateBorrow" id="btnUpdateBorrow" '.$image_update.' align="center" onClick="if (checkBorrowForm(1)) jsUpdateBorrow();">');
$smarty->assign('sReturnButton','<input type="image" name="btnReturn" id="btnReturn" src="'.$root_path.'images/btn_return.gif" align="center" onClick="if (checkReturnForm()) jsSaveReturn();">');
$smarty->assign('sUpdateReturnButton','<input type="image" name="btnUpdateReturn" id="btnUpdateReturn" '.$image_update.' align="center" onClick="if (checkReturnForm()) jsSaveReturn();">');
$smarty->assign('sDoneButton','<input type="image" name="btnDone" id="btnDone" src="'.$root_path.'images/btn_done.gif" align="center" onClick="jsDoneBorrow();">');
ob_start();
$sTemp='';
?>
	<script type="text/javascript" language="javascript">
		preset();
	</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->assign('sIntialRequestList',$sTemp);

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform" onSubmit="return false;">');
$smarty->assign('sFormEnd','</form>');

if (is_object($recordBorrowObj)){
	$sTemp = '';
	$smarty->assign('sPanelHeaderRecordHistory','Borrowing History');
	ob_start();
	while($recordHistory=$recordBorrowObj->FetchRow()){
			# FORMATTING of Date Borrowed
		$date_borrowed = $recordHistory['date_borrowed'];
		if (($date_borrowed!='0000-00-00')  && ($date_borrowed!=""))
			$date_borrowed = @formatDate2Local($date_borrowed,$date_format);
		else
			$date_borrowed='';
	
			# FORMATTING of Date Returned
		$date_returned = $recordHistory['date_returned'];
		if (($date_returned!='0000-00-00')  && ($date_returned!=""))
			$date_returned = @formatDate2Local($date_returned,$date_format);
		else
			$date_returned='';

		echo'
		<tr>
			<td align="left">'.$recordHistory['borrower_name'].'</td>
			<td align="center">'.$date_borrowed.'</td>
			<td align="left">'.$recordHistory['releaser_name'].'</td>
			<td align="center">'.$date_returned.'</td>
			<td align="left">'.$recordHistory['receiver_name'].'</td>
			<td align="justify">'.$recordHistory['remarks'].'</td>
		</tr>'."\n";

		#echo "recordHistory : <br>\n "; print_r($recordHistory); echo"<br>\n";
	}
	$sTemp = ob_get_contents();
	ob_end_clean();
	
	$smarty->assign('sRecordHistory',$sTemp);
}


/*
$smarty->assign('sCurrentFilmReleaser','<input type="hidden" name="releaser_id" id="releaser_id" value="'.$HTTP_SESSION_VARS['sess_temp_personell_nr'].'">
										<input type="text" name="releaser_fullname" id="releaser_fullname" value="'.$HTTP_SESSION_VARS['sess_temp_fullname'].'" disabled>');
$smarty->assign('sPreviousFilmReleaser','<input type="hidden" name="releaser_id_old" id="releaser_id_old" value="'.$releaser_id.'">
										<input type="text" name="releaser_fullname_old" id="releaser_fullname_old" value="'.$releaser_name.'" disabled>');

$smarty->assign('sCurrentFilmReceiver','<input type="hidden" name="receiver_id" id="receiver_id" value="'.$HTTP_SESSION_VARS['sess_temp_personell_nr'].'">
										<input type="text" name="receiver_fullname" id="receiver_fullname" value="'.$HTTP_SESSION_VARS['sess_temp_fullname'].'" disabled>');
$smarty->assign('sPreviousFilmReceiver','<input type="hidden" name="receiver_id_old" id="receiver_id_old" value="'.$receiver_id.'">
										<input type="text" name="releaser_fullname_old" id="receiver_fullname_old" value="'.$receiver_name.'" disabled>');
*/

ob_start();
?>
<div align="left">
			<table border=0 cellspacing=5 cellpadding=5>			
				<tr bgcolor="#f3f3f3">
					<td>
						&nbsp;<br>												
						<font SIZE=2 FACE="Arial">Search record:</font><br>
						<form name="searchform" onSubmit="return false;">
							<input type="text" name="searchkey" id="searchkey" size=40 maxlength=40 onChange="trimString(this,true);" value="">
							<br>
							<span style="font-family:Arial, Helvetica, sans-serif; font-size:11px">
								(batch number, service code, service name, requesting doctor, date)
							</span>
							<p>
							<input type="image" src="<?=$root_path?>images/his_searchbtn.gif" align="absmiddle" onClick="$('skey').value=$('searchkey').value; handleOnclick();">
<!--
   						<img src="<?= $root_path ?>images/his_searchbtn.gif" align="absmiddle" border="0" onClick="$('skey').value=$('searchkey').value; handleOnclick();">
-->
						</form>
					</td>
				</tr>				
			</table>
</div>
<?php
	$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->assign('sSearchInput',$sTemp);

	ob_start();
?>
<!--  Tab Container for radiology request list -->
<div id="rlistContainer"  dojoType="TabContainer" style="width:88%; height:28em;" align="center">
	<div align="left">
		<span class="linkgroup" style=" font:'Courier New', Courier, mono; font-size:11.5px;">
			Selected: <span id="selectedcount">0</span>
		</span>
	</div>
	<div dojoType="ContentPane" widgetId="tab0" label="All" style="display:none;overflow:auto">
		<!--  Table:list of request -->
		<table id="Ttab0" class="segList" border="0" cellpadding="0" cellspacing="0">
			<!-- List of all radiology request -->
		</table>
		<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
	</div>
	<!-- tabcontent for radiology sub-department -->
<?php
#Department object
include_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj = new Department;

#echo "seg-radio-patient.php : dept_nr = '".$dept_nr."' <br> \n";

$radio_sub_dept=$dept_obj->getSubDept('158');   # Radiology dept no = 158

#echo "seg-radio-patient.php : radio_sub_dept = '".$radio_sub_dept."' <br> \n";

if($dept_obj->rec_count){
	$dept_counter=2;
	while ($rowSubDept = $radio_sub_dept->FetchRow()){
		if (trim($rowSubDept['name_short'])!=''){		
			$text_name = trim($rowSubDept['name_short']);
		}elseif (trim($rowSubDept['id'])!=''){
			$text_name = trim($rowSubDept['id']);
		}else{
			$text_name = trim($rowSubDept['name_formal']);
		}
?>		
	<div dojoType="ContentPane" widgetId="tab<?=$rowSubDept['nr']?>" label="<?=$text_name?>" style="display:none;overflow:auto" >
   	<table id="Ttab<?=$rowSubDept['nr']?>" cellpadding="0" cellspacing="0" class="segList">
   		<!-- List of Radiology Requests  -->
   	</table>
   	<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
	</div>
<?php 
		$dept_counter++;
	} # end of while loop
}   # end of if-stmt 'if ($dept_obj->rec_count)'
?>
</div>
<?php
	$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->assign('sTabRadiology',$sTemp);
	
	$smarty->assign('sIsCash','<input type="radio" name="iscash" id="iscash1" value="1"'.(($is_cash||empty($is_cash))? " checked":"").' onchange="if (changeTransactionType) changeTransactionType()">Cash');
	$smarty->assign('sIsCharge','<input class="segInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0"'.(($is_cash=='0')? " checked":"").' onchange="if (changeTransactionType) changeTransactionType()">Charge');
	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$pid.'"/>');
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$person_name.'" style="font:bold 12px Arial;">');
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
	$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" onclick="clearEncounter()" disabled>');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial">'.$orderaddress.'</textarea>');
	$smarty->assign('sRefNo','<input class="segInput" name="refno" id="refno" type="text" size="10" value="'.$refno.'" disabled style="font:bold 12px Arial"/>');
#	$smarty->assign('sResetRefNo','<input class="segInput" type="button" value="Reset" style="font:bold 11px Arial"/>');
	
	$curDate = ($request_date)? $request_date:date("m/d/Y");
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

	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="request_date_trigger" align="absmiddle" style="cursor:pointer">'.$jsCalScript);
	
	$smarty->assign('sNormalPriority','<input type="radio" name="priority" id="priority0" value="0"'.($is_urgent? "": " checked").'>Normal');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" id="priority1" value="1"'.($is_urgent? " checked": "").'>Urgent');
	$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.$comments.'</textarea>');
	$smarty->assign('sRecordItems',"
				<tr>
					<td colspan=\"6\">Request list is currently empty...</td>
				</tr>");

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
		 	<input type="image" name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_addservicecodes.gif" border="0"></a>');
# <img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_additems.gif" border="0"></a>');
$smarty->assign('sBtnEmptyList','<a href="javascript:emptyTray()">
		<input type="image" name="btnEmpty" id="btnEmpty" src="'.$root_path.'images/btn_emptylist.gif" border="0"></a>');
#$smarty->assign('sBtnEmptyList','<a href="javascript:emptyTray()"><img src="'.$root_path.'images/btn_emptylist.gif" border="0" /></a>');
$smarty->assign('sDiscountInfo','<img src="'.$root_path.'images/discount.gif">');
$smarty->assign('sBtnDiscounts', '<img name="btndiscount" id="btndiscount" onclick="saveDiscounts();" src="'.$root_path.'images/btn_discounts.gif" border="0" style="cursor:pointer; display:none">');
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
# $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform" onSubmit="return false;">');
# $smarty->assign('sFormEnd','</form>');
?>
<?php
ob_start();
$sTemp='';
?>
	<script type="text/javascript" language="javascript">
		//handleOnclick();
//		preset(<?= ($is_cash=='0')? "0":"1"?>);
//		xajax_populateRequestListByRefNo(<?=$refno? $refno:0?>);	
//		xajax_getCharityDiscounts(<?= $refno?>);
	</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

#$smarty->assign('sIntialRequestList',$sTemp);
/*	
if ($mode=='update'){
	$smarty->assign('sIntialRequestList',$sTemp);
}
*/
ob_start();
$sTemp='';
?>
	<input type="hidden" name="submit" value="1">
	<input type="hidden" name="sid" id="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" id="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">  
<!--  
	<input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
-->
	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">

	<input type="hidden" name="is_cash" id="is_cash" value="<?=$is_cash?>" >
	<input id="encounter_nr" name="encounter_nr" type="hidden" value="<?=$encounter_nr?>">

	<input type="hidden" name="mode" id="mode" value="<?=$mode?$mode:'save'?>">
	<input type="hidden" name="update" id="update" value="0">
	<input type="hidden" name="popUp" id="popUp" value="<?=$popUp?$popUp:'0'?>">
	<input type="hidden" name="hasPaid" id="hasPaid" value="<?=$hasPaid?$hasPaid:'0'?>">
	<input type="hidden" name="view_from" id="view_from" value="<?=$view_from?$view_from:''?>">
	<input type="hidden" name="encoder_id" id="encoder_id" value="<?php echo $HTTP_SESSION_VARS['sess_login_personell_nr']; ?>">

	<input type="hidden" name="skey" id="skey" value="*"> 
	<input type="hidden" name="smode" id="smode" value="<?=$mode?$mode:'save'?>">
	<input type="hidden" name="starget" id="starget" value="<?php echo $target; ?>">
	<input type="hidden" name="thisfile" id="thisfile" value="<?php echo $thisfile; ?>">
	<input type="hidden" name="rpath" id="rpath" value="<?php echo $root_path; ?>">
	<input type="hidden" name="pgx" id="pgx" value="<?php echo $pgx; ?>">
	<input type="hidden" name="oitem" id="oitem" value="<?= $oitem? $oitem:'batch_nr' ?>">
	<input type="hidden" name="odir" id="odir" value="<?= $odir? $odir:'ASC' ?>">

<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->assign('sHiddenInputs',$sTemp);
#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';alert($F(\'mode\'));" onsubmit="return false;" style="cursor:pointer">');
#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';alert($F(\'mode\'));" style="cursor:pointer">');
if (($mode=="update") && ($popUp!='1')){
	$sBreakImg ='cancel.gif';
	$smarty->assign('sBreakButton','<input type="image" '.createLDImgSrc($root_path,$sBreakImg,'0','center').' align="center" alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';" style="cursor:pointer">');
}elseif ($popUp!='1'){
	$sBreakImg ='close2.gif';	
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
}
$smarty->assign('sContinueButton','<input type="image" name="btnSubmit" id="btnSubmit" src="'.$root_path.'images/btn_submitrequest.gif" align="center">');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','radiology/radio-patient-borrow.tpl');
$smarty->display('common/mainframe.tpl');
?>