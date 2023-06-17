<?php
	# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	$_GET['popUp'] =1;
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
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid HRN or RID!</em>';
	exit();
}
#echo "seg-radio-borrow.php : basicInfo='".$basicInfo."' <br> \n";
#echo "seg-radio-borrow.php : basicInfo : <br> \n"; print_r($basicInfo); echo" <br> \n";

#echo "seg-radio-borrow.php : mode='".$mode."' <br> \n";
#echo "seg-radio-borrow.php : refNoInfo : "; print_r($refNoInfo); echo " <br><br> \n";

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
		#echo $objRadio->sql;
		if (($available==0) && is_object($recordBorrowObj)){
			$lastestRecordBorrowInfo = $recordBorrowObj->FetchRow();
#echo "seg-radio-borrow.php :: lastestRecordBorrowInfo : <br> \n"; print_r($lastestRecordBorrowInfo); echo "<br>\n";
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
#echo "seg-radio-borrow.php :: recordBorrowObj='".$recordBorrowObj."' <br> \n";
#echo "seg-radio-borrow.php :: lastestRecordBorrowInfo='".$lastestRecordBorrowInfo."' <br> \n";

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

	if (empty($date_returned))
		$date_returned = date("m/d/Y");

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

	#added by VAN 07-09-08
	if (empty($time_borrowed)){
		$time_borrowed = date("H:i");
		#$time_meridian = date("A");
	}
	#----------------------

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

	if (empty($time_returned)){
		$time_returned = date("H:i");
		#$time_meridian = date("A");
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
		<input type="checkbox" id="borrower_self" name="borrower_self" onClick="selfBorrower();"> Owner
		<script language="javascript">
			xajax_setALLDepartment(0);	//set the list of ALL departments
			xajax_setDoctors(0,0);	//set the list of ALL doctors from ALL departments';
if ($borrower_id){
	$sBorrower .= '
			xajax_setDepartmentOfDoc('.$borrower_id.');	//set the borrower (doctor) & his department ';
}else{
	$sBorrower .= '
			ajxSetDoctor(0);	//set the borrower to owner ';
}

$sBorrower .= '
			</script>';

$smarty->assign('sPanelHeaderBorrow','Film Borrowing/Releasing Form');
$smarty->assign('sPanelHeaderReturn','Film Return Form');

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

#added by VAN 07-10-08
$current_date = date('Y-m-d');
$borrowed_date = date("Y-m-d",strtotime($row['date_borrowed']));

// Extract from $current_date
$current_year = substr($current_date,0,4);
$current_month = substr($current_date,5,2);
$current_day = substr($current_date,8,2);

$date_borrowed = date("Y-m-d",strtotime($date_borrowed));

// Extract from $borrowed date
$borrowed_year = substr($date_borrowed,0,4);
$borrowed_month = substr($date_borrowed,5,2);
$borrowed_day = substr($date_borrowed,8,2);

// create a string yyyymmdd 20071021
$tempMaxDate = $current_year . $current_month . $current_day;
$tempDataRef = $borrowed_year . $borrowed_month . $borrowed_day;

$tempDifference = $tempMaxDate-$tempDataRef;
#echo "<br>".$tempMaxDate." - ".$tempDataRef." = ".$tempDifference;

// If the difference is GT 3 days show the date
if($tempDifference > 2){
	$penalty = $price + ($price*0.30);
	$penalty = number_format($penalty,2,".",",");
}else{
	$penalty = "0.00";
}
$smarty->assign('sPenalty','<input type="text" name="penalty" id="penalty" size="10" value="'.$penalty.'" readonly style="text-align:right">
							 &nbsp;&nbsp; No. of Days : '.$tempDifference.'');
#------------------


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
	$myCount=0;
	$smarty->assign('sPanelHeaderRecordHistory','Borrowing History');
	ob_start();
	while($recordHistory=$recordBorrowObj->FetchRow()){
		$history_borrower_name = $recordHistory['borrower_name'];
		if ($recordHistory['borrower_id']==0){
			$name_mi=' ';
			$name_middle = trim($name_middle);
			if ($name_middle){
				$name_mi = ' '.substr($name_middle,0,1).'. ';
			}
			$history_borrower_name = $name_first.$name_mi.$name_last;
		}
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

	$toolTipTextHandler = ' onMouseOver="return overlib($(\'toolTipText'.$myCount.'\').value, CAPTION,\'Remarks\',  '.
							'  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', '.
							'  REF,\'ref_point'.$myCount.'\', REFC,\'LR\', REFP,\'UL\', '.
							'  WIDTH, 500,FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();"';
#/REFY,-30, \'REFY,15,(UL UL)\',
#
		echo '
		<tr>
			<td align="left" id="ref_point'.$myCount.'">'.$history_borrower_name.'</td>
			<td align="center">'.$date_borrowed.'</td>
			<td align="left">'.$recordHistory['releaser_name'].'</td>
			<td align="center">'.$date_returned.'</td>
			<td align="left">'.$recordHistory['receiver_name'].'</td>
			<td align="justify" '.$toolTipTextHandler.'>
				View
				<input type="hidden" name="toolTipText'.$myCount.'" id="toolTipText'.$myCount.'" value="'.$recordHistory['remarks'].'">
			</td>
		</tr>'."\n";
		$myCount++;
		#echo "recordHistory : <br>\n "; print_r($recordHistory); echo"<br>\n";
	}
	$sTemp = ob_get_contents();
	ob_end_clean();

	$smarty->assign('sRecordHistory',$sTemp);
}

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
#echo "seg-radio-borrow.php : mode ='".$mode."' <br> \n";

	$sBreakImg ='close2.gif';
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','radiology/radio-patient-borrow.tpl');
$smarty->display('common/mainframe.tpl');
?>