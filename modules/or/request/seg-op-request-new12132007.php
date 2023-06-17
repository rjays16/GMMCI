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
	$lang_tables[]='search.php';
	$lang_tables[]='actions.php';
	define('LANG_FILE','or.php');
#	define('NO_2LEVEL_CHK',1);
	$local_user='ck_op_pflegelogbuch_user';   # burn added : October 2, 2007
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/inc_front_chain_lang.php');

	require($root_path.'modules/or/ajax/op-request-new.common.php');

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

$title=$LDOr;
$breakfile=$root_path.'main/op-doku.php'.URL_APPEND;
$thisfile=basename(__FILE__);

	# Create operation billing object
	require_once($root_path.'include/care_api_classes/billing/class_ops.php');
	$ops_obj = new SegOps;
	
#	global $db;
	
	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('or');

#echo "seg-op-request-new.php : b4 switch _POST : "; print_r($_POST); echo " <br><br> \n";

#echo "seg-op-request-new.php : before : popUp ='".$popUp."; _GET['popUp'] = '".$_GET['popUp']."'; _POST['popUp'] = '".$_POST['popUp']."' <br> \n";
	if (!isset($popUp) || !$popUp){
		if (isset($_GET['popUp']) && $_GET['popUp']){
			$popUp = $_GET['popUp'];
		}
		if (isset($_POST['popUp']) && $_POST['popUp']){
			$popUp = $_POST['popUp'];
		}
	}
#echo "seg-op-request-new.php : after : popUp ='".$popUp."; _GET['popUp'] = '".$_GET['popUp']."'; _POST['popUp'] = '".$_POST['popUp']."' <br> \n";

	switch($mode){
		case 'save':
	echo "seg-op-request-new.php : save mode = '".$mode."' <br> \n";
	echo "seg-op-request-new.php : _POST : "; print_r($_POST); echo " <br><br> \n";
#	exit();
	#echo "seg-op-request-new.php : _POST['findings_date'] = '".formatDate2STD($_POST['findings_date'], $date_format)."' <br> \n";				
				if(trim($_POST['request_date'])!=""){
					$_POST['request_date'] = formatDate2STD($_POST['request_date'], $date_format);
				}
				$_POST['nr'] = $_POST['op_request_nr'];
				$_POST['is_cash'] = $_POST['iscash'];
				$_POST['is_urgent'] = $_POST['priority'];
//				$_POST['hasPaid'] = 0;   # not yet paid since this is just a request
				$_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
				$_POST['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
	echo "seg-op-request-new.php : save 2: _POST : "; print_r($_POST); echo " <br><br> \n";
#	exit();
				if($refno = $ops_obj->saveOpsBilling($_POST)){
#					$errorMsg='<font style="color:#FF0000">Successfully saved!</font>';
					$smarty->assign('sWarning',"OR Request Billing successfully created.");
				}else{
					# $errorMsg = $db->ErrorMsg();
#					$errorMsg='<font style="color:#FF0000">'.$ops_obj->getErrorMsg().'</font>';
					$smarty->assign('sWarning','<strong>Error:</strong> '.$ops_obj->getErrorMsg());
#					$smarty->assign('sWarning','<strong>Error:</strong> '.$ops_obj->getErrorMsg());
				}
echo "seg-op-request-new.php : AFTER saveOpsBilling : refno  = '".$refno ."' <br> \n";			
#exit();
				break;
		case 'update':
#	echo "seg-op-request-new.php : update mode = '".$mode."' <br> \n";			
#	echo "seg-op-request-new.php : _POST 1 : "; print_r($_POST); echo " <br><br> \n";
#	echo "seg-op-request-new.php : HTTP_SESSION_VARS : "; print_r($HTTP_SESSION_VARS); echo " <br><br> \n";

	#			if($ops_obj->saveAFinding($batch_nr,$finding_nr,$findings,$radio_impression,$findings_date,$doctor_id,'Update')){
				if(trim($_POST['request_date'])!=""){
					$_POST['request_date'] = formatDate2STD($_POST['request_date'], $date_format);
				}
				$_POST['nr'] = $_POST['op_request_nr'];
				$_POST['is_cash'] = $_POST['iscash'];
				$_POST['is_urgent'] = $_POST['priority'];
				$_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
#	echo "seg-op-request-new.php : _POST 2 : "; print_r($_POST); echo " <br><br> \n";
#	exit();
				if($ops_obj->updateOpsBilling($_POST)){
#					$errorMsg='<font style="color:#FF0000">Successfully updated!</font>';
						# NOTE: October 3, 2007, i think no need 2 reload the parent window
					$reloadParentWindow='<script language="javascript">'.
								'	window.parent.jsOnClick(); '.
#								'	javascript:self.parent.location.href=self.parent.location.href;'.
								'</script>';
					$smarty->assign('sWarning',"OR Request Billing successfully updated.");					
				}else{
					$errorMsg='<font style="color:#FF0000">'.$ops_obj->getErrorMsg().'</font>';
				}
				break;
		case 'cancel':
#	echo "seg-op-request-new.php : cancel mode = '".$mode."' <br> \n";			
#	echo "seg-op-request-new.php : _POST : "; print_r($_POST); echo " <br><br> \n";
				if($ops_obj->deleteRefNo($_POST['refno'])){
/*					$errorMsg='<font style="color:#FF0000">Successfully deleted!</font>';
					echo "<script language='javascript'> alert('Successfully deleted!')</script>";
*/
					header('Location: '.$breakfile);
					exit;
				}else{
					$errorMsg='<font style="color:#FF0000">'.$ops_obj->getErrorMsg().'</font>';
				}
				break;
	}# end of switch stmt	
#	$refno='2007000004';	

	if (!isset($op_request_nr) || !$op_request_nr){
		if (isset($_GET['op_request_nr']) && $_GET['refno']){
			$op_request_nr = $_GET['op_request_nr'];
		}
		if (isset($_POST['op_request_nr']) && $_POST['op_request_nr']){
			$op_request_nr = $_POST['op_request_nr'];
		}
	}
#echo "seg-op-request-new.php : op_request_nr='".$op_request_nr."' <br> \n";	
#echo "seg-op-request-new.php : refno='".$refno."' <br> \n";

	$mode='save';   # default mode
	if ($refno = $ops_obj->encOpsNrHasOpsServ($op_request_nr)){
		$mode='update';
		if ($refNoBasicInfo = $ops_obj->getAllEncounterOpsServiceInfo($refno)){			
#echo "seg-op-request-new.php : refNoBasicInfo : "; print_r($refNoBasicInfo); echo " <br><br> \n";
			extract($refNoBasicInfo);
			if (empty($refNoBasicInfo['pid']) || !$refNoBasicInfo['pid']){
				$person_name = $refNoBasicInfo['ordername'];
			}else{
					# in case there is an updated profile of the person
#				$person_name = $refNoBasicInfo['name_first'].' '.$refNoBasicInfo['name_last'];
				$person_name = $refNoBasicInfo['person_name'];   # firstname MI lastname
			}
	#echo "seg-op-request-new.php : before : request_date='".$request_date."' <br> \n";			
			$request_date = formatDate2Local($request_date,$date_format); 
	#echo "seg-op-request-new.php : after : request_date='".$request_date."' <br> \n";			
			$OpsServDetailsInfo = $ops_obj->getOpsServDetailsInfo($refno);  # place in AJAX??? October 6, 2007

				#role_type_nr : 7,surgeon; 8,assistant surgeon; 12,anesthesiologist;	
			$operator_array = $ops_obj->getOpsPersonellNr($refno,'7');   # get surgeons
#			$operator_array = unserialize($operator);
#echo "seg-op-request-new.php : operator_array : "; print_r($operator_array); echo " <br><br> \n";
#exit();
			$assistant_array = $ops_obj->getOpsPersonellNr($refno,'8');   # get assistant
#			$assistant_array = unserialize($assistant_array);
#echo "seg-op-request-new.php : assistant_array : "; print_r($assistant_array); echo " <br><br> \n";
#exit();
			$an_doctor_array = $ops_obj->getOpsPersonellNr($refno,'12');   # get anesthesiologist
#			$an_doctor_array = unserialize($an_doctor_array);
#echo "seg-op-request-new.php : an_doctor_array : "; print_r($an_doctor_array); echo " <br><br> \n";
#exit();

			$scrub_nurse_array = unserialize($scrub_nurse); # from getAllEncounterOpsServiceInfo
#echo "seg-op-request-new.php : scrub_nurse_array : "; print_r($scrub_nurse_array); echo " <br><br> \n";
#exit();
			$rotating_nurse_array = unserialize($rotating_nurse); # from getAllEncounterOpsServiceInfo
#echo "seg-op-request-new.php : rotating_nurse_array : "; print_r($rotating_nurse_array); echo " <br><br> \n";
#exit();
			
		}#end of if-stmt
	}else{
		#save mode, first time entry; NO refno yet
		if ($refNoBasicInfo = $ops_obj->getBasicEncounterOpInfo($op_request_nr,TRUE)){
			extract($refNoBasicInfo);

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
			$orderaddress = $sAddress;
			$request_date = formatDate2Local($request_date,$date_format); 
			$operator_array = unserialize($operator);
			$assistant_array = unserialize($assistant);
			$an_doctor_array = unserialize($an_doctor);
			$scrub_nurse_array = unserialize($scrub_nurse);
			$rotating_nurse_array = unserialize($rotating_nurse);
/*
			$operator_info = $ops_obj->setPersonellNrNamePID($operator_array);
			$assistant_info = $ops_obj->setPersonellNrNamePID($assistant_array);
			$an_doctor_info = $ops_obj->setPersonellNrNamePID($an_doctor_array);
			$scrub_nurse_info = $ops_obj->setPersonellNrNamePID($scrub_nurse_array);
			$rotating_nurse_info = $ops_obj->setPersonellNrNamePID($rotating_nurse_array);
*/		}#end of if-stmt 'if ($refNoBasicInfo...'
	}

	$operator_info = $ops_obj->setPersonellNrNamePID($operator_array);
	$assistant_info = $ops_obj->setPersonellNrNamePID($assistant_array);
	$an_doctor_info = $ops_obj->setPersonellNrNamePID($an_doctor_array);
	$scrub_nurse_info = $ops_obj->setPersonellNrNamePID($scrub_nurse_array);
	$rotating_nurse_info = $ops_obj->setPersonellNrNamePID($rotating_nurse_array);


#echo "seg-op-request-new.php : mode='".$mode."' <br> \n";
#exit();
/*
echo "seg-op-request-new.php : refno='".$refno."' <br> \n";
echo "seg-op-request-new.php : request_date='".$request_date."' <br> \n";			
echo "seg-op-request-new.php : refNoBasicInfo : "; print_r($refNoBasicInfo); echo " <br><br> \n";
echo "seg-op-request-new.php : operator_array : "; print_r($operator_array); echo " <br><br> \n";
echo "seg-op-request-new.php : assistant_array : "; print_r($assistant_array); echo " <br><br> \n";
echo "seg-op-request-new.php : scrub_nurse_array : "; print_r($scrub_nurse_array); echo " <br><br> \n";
echo "seg-op-request-new.php : rotating_nurse_array : "; print_r($rotating_nurse_array); echo " <br><br> \n";
echo "seg-op-request-new.php : an_doctor_array : "; print_r($an_doctor_array); echo " <br><br> \n";
*/
#exit();
 # Title in the title bar
# $smarty->assign('sToolbarTitle',"$LDRadiology::$LDDiagnosticTest");
 $smarty->assign('sToolbarTitle',"$LDOr Billing::ICPM Encoding");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

if ($popUp!='1'){
		 # href for the close button
	 $smarty->assign('breakfile',$breakfile);
}else{
		# CLOSE button for pop-ups
	 $smarty->assign('breakfile','javascript:window.parent.pSearchClose();');
	$smarty->assign('pbBack','');
}

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDOr Billing::ICPM Encoding");

 # Assign Body Onload javascript code
 
# $onLoadJS='onLoad="preSet();"';
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

			<!-- START for settin the DATE (NOTE: should be IN this ORDER...i think soo..) -->
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
<script type="text/javascript" src="<?=$root_path?>modules/or/js/op-request-new.js?t=<?=time()?>"></script>


<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;

	function openOrderTray() {
		window.open("seg-request-tray.php<?=URL_APPEND?>&clear_ck_sid=<?=$clear_ck_sid?>","patient_select","width=720,height=500,menubar=no,resizable=no,scrollbars=yes");
	}
-->
</script>

<?php
	if ($popUp=='1'){
		#echo $reloadParentWindow;
	}
	$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->append('JavaScript',$sTemp);


	function listPersonnel($position, $pers_info, $position_title){

		if (is_array($pers_info) && !empty($pers_info)){
			$i=1;
			foreach($pers_info as $pers_nr=>$pers_pidName){
#				$list.='<input type="hidden" name="'.$position.'[]" id="'.$position.$pers_nr.'" value="'.$pers_pidName['pid'].'">';
				$list.='<input type="hidden" name="'.$position.'[]" id="'.$position.$pers_nr.'" value="'.$pers_nr.'">';
				$list.=	'['.$i.'] '.$pers_pidName['name']."<br>\n";
				$i++;
			}
			$list = '<span style="text-align:justify;color:#000000;">'."\n".$list.'</span>';	
		}else{
			$list = '<span style="text-align:center;color:#FF0000;font-weight:bold;">No '.$position_title.'</span>';
		}
		return $list;
	}

	$segSurgeons = listPersonnel('surgeon',$operator_info,'Surgeon');
	$smarty->assign('segSurgeons',$segSurgeons);

	$segAssistants = listPersonnel('assistant',$assistant_info,'Assistant Surgeon');
	$smarty->assign('segAssistants',$segAssistants);

	$segAnDoctors = listPersonnel('an_doctor',$an_doctor_info,'Anesthesiologist');
	$smarty->assign('segAnDoctors',$segAnDoctors);

	$segScrubNurses = listPersonnel('scrub_nurse',$scrub_nurse_info,'Scrub Nurse');
	$smarty->assign('segScrubNurses',$segScrubNurses);

	$segRotatingNurses = listPersonnel('rotating_nurse',$rotating_nurse_info,'Rotating Nurse');
	$smarty->assign('segRotatingNurses',$segRotatingNurses);

	$smarty->assign('segDepartment',$name_formal);

	$op_therapy=trim($op_therapy);
	if (!empty($op_therapy) && $op_therapy){
#		$smarty->assign('segTherapy','<span style="text-align:justify;color:#000000;">'.$op_therapy.'</span>');
		$smarty->assign('segTherapy',$op_therapy);
	}else{
		$smarty->assign('segTherapy','<span style="text-align:center;color:#FF0000;font-weight:bold;">No entry for operation...</span>');
	}

	$smarty->assign('sIsCash','<input type="radio" name="iscash" id="iscash1" value="1"'.(($is_cash||empty($is_cash))? " checked":"").'>Cash');
	$smarty->assign('sIsCharge','<input class="segInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0"'.(($is_cash=='0')? " checked":"").'>Charge');
	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$pid.'"/>');
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$person_name.'" style="font:bold 12px Arial;" readonly>');
/*
	$smarty->assign('sSelectEnc','<input class="segInput" name="select-enc" id="select-enc" type="image" src="../../../images/btn_encounter_small.gif" border="0" style=""
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
	$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" onclick="clearEncounter()" disabled/>');
*/
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" readonly>'.$orderaddress.'</textarea>');
	$smarty->assign('sRefNo','<input class="segInput" name="refno" id="refno" type="text" size="10" value="'.$refno.'" style="font:bold 12px Arial" readonly>');
#	$smarty->assign('sResetRefNo','<input class="segInput" type="button" value="Reset" style="font:bold 11px Arial"/>');
	
	$curDate = ($request_date)? $request_date:date("m/d/Y");
	$jsCalScript = "
		<script type=\"text/javascript\">
			Calendar.setup ({
				inputField : \"request_date\", ifFormat : \"$phpfd\", showsTime : false, button : \"request_date_trigger\", singleClick : true, step : 1
			});
		</script>
		";
	$smarty->assign('sOrderDate','<input name="request_date" id="request_date" type="text" size="10" 
											value="'.$curDate.'" style="font:bold 12px Arial"
											onFocus="this.select();"  
											onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
											onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
											onKeyUp="setDate(this,\'MM/dd/yyyy\',\'en\')">');
/*
	$smarty->assign('sOrderDate','<input name="request_date" id="request_date" type="text" size="10" 
											value="'.$curDate.'" style="font:bold 12px Arial"
											onFocus="this.select();">');
*/
#	$smarty->assign('sOrderDate','<input name="request_date" type="text" size="10" value="'.$curDate.'" style="font:bold 12px Arial">');
	$smarty->assign('sCalendarIcon','<img '. createComIcon($root_path,'show-calendar.gif','0') . ' id="request_date_trigger" align="absmiddle" style="cursor:pointer">'.$jsCalScript);
	
	$smarty->assign('sNormalPriority','<input type="radio" name="priority" value="0"'.($is_urgent? "": " checked").'>Normal');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" value="1"'.($is_urgent? " checked": "").'>Urgent');
	$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.$comments.'</textarea>');	
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"7\">List is currently empty...</td>
				</tr>");

$smarty->assign('sBtnAddItem','<a href="javascript:void(0);"
       onclick="return overlib(
        OLiframeContent(\'seg-op-tray.php\', 625, 375, \'fOrderTray\', 1, \'auto\'),
        WIDTH, 375, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
				CAPTION,\'Add procedure codes from ICPM tray\',
        MIDX,0, MIDY,0, 
        STATUS,\'Add procedure codes from ICPM tray\');"
       onmouseout="nd();">
		 	<input type="image" name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_addicpm.gif" border="0"></a>');
# <img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_additems.gif" border="0"></a>');
$smarty->assign('sBtnEmptyList','<a href="javascript:emptyTray()">
		<input type="image" name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_emptylist.gif" border="0"></a>');
#$smarty->assign('sBtnEmptyList','<a href="javascript:emptyTray()"><img src="'.$root_path.'images/btn_emptylist.gif" border="0" /></a>');
$smarty->assign('sDiscountInfo','<img src="'.$root_path.'images/discount.gif">');
/*
$smarty->assign('sBtnDiscounts','<a href="javascript:void(0);"
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
			 <img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_discounts.gif" border="0"></a>');
*/			
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

if($error=="refno_exists"){
	$smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
	$smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}


 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform" onSubmit="return checkRequestForm()">');
 $smarty->assign('sFormEnd','</form>');
?>
<?php
ob_start();
$sTemp='';
?>
	<script type="text/javascript" language="javascript">
		//preset(<?= ($is_cash=='0')? "0":"1"?>);
		xajax_populateOpsCodeListByRefNo(<?=$refno? $refno:0?>);	
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
	<input type="hidden" name="submit" value="1">
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

	<input type="hidden" name="is_cash" id="is_cash" value="<?=$is_cash?>" >
	<input type="hidden" id="op_request_nr" name="op_request_nr" value="<?=$op_request_nr?>">
	<input type="hidden" id="encounter_nr" name="encounter_nr" value="<?=$encounter_nr?>">
	<input type="hidden" name="mode" id="mode" value="<?=$mode?$mode:'save'?>">
	<input type="hidden" name="popUp" id="popUp" value="<?=$popUp?$popUp:'0'?>">
	
<?php 
#echo "seg-op-request-new.php : HTTP_SESSION_VARS : "; print_r($HTTP_SESSION_VARS); echo " <br><br> \n";
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
$smarty->assign('sContinueButton','<input type="image" src="'.$root_path.'images/btn_submit.gif" align="center">');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','or/op-request-new.tpl');
$smarty->display('common/mainframe.tpl');

?>