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
#echo "nursing-station-radio-request-new.php : after : popUp ='".$popUp."; _GET['popUp'] = '".$_GET['popUp']."'; _POST['popUp'] = '".$_POST['popUp']."' <br> \n";

	switch($mode){
		case 'save':
	#echo "nursing-station-radio-request-new.php : save mode = '".$mode."' <br> \n";
	#echo "nursing-station-radio-request-new.php : _POST : "; print_r($_POST); echo " <br><br> \n";
	#echo "nursing-station-radio-request-new.php : _POST['findings_date'] = '".formatDate2STD($_POST['findings_date'], $date_format)."' <br> \n";
				if(trim($_POST['request_date'])!=""){
					$_POST['request_date'] = formatDate2STD($_POST['request_date'], $date_format);
				}
				$_POST['clinical_info'] = $_POST['clinicInfo'];	
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
				if ($_POST['is_urgent']){
					$rid = $radio_obj->createNewRID($_POST['pid']); 
				}
#echo "nursing-station-radio-request-new.php : rid = '".$rid."' <br> \n";
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
	#echo "nursing-station-radio-request-new.php : update mode = '".$mode."' <br> \n";			
	#echo "nursing-station-radio-request-new.php : _POST : "; print_r($_POST); echo " <br><br> \n";

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
#				$_POST['hasPaid'] = 0;   # not yet paid since this is just a request
				$_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
   			$_POST['history'] = $radio_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");		

				if ($_POST['is_urgent']){
					$rid = $radio_obj->createNewRID($_POST['pid']); 
				}
#echo "nursing-station-radio-request-new.php : save 2: _POST : "; print_r($_POST); echo " <br><br> \n";
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

	if (!isset($refno) || !$refno){
		if (isset($_GET['refno']) && $_GET['refno']){
			$refno = $_GET['refno'];
		}
		if (isset($_POST['refno']) && $_POST['refno']){
			$refno = $_POST['refno'];
		}
	}
#echo "nursing-station-radio-request-new.php : refno='".$refno."' <br> \n";			

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
#echo "nursing-station-radio-request-new.php : before : request_date='".$request_date."' <br> \n";			
		$request_date = formatDate2Local($request_date,$date_format); 
#echo "nursing-station-radio-request-new.php : after : request_date='".$request_date."' <br> \n";			
	}#end of if-stmt
#echo "nursing-station-radio-request-new.php : mode='".$mode."' <br> \n";			
#echo "nursing-station-radio-request-new.php : radio_obj->sql='".$radio_obj->sql."' <br> \n";
#echo "nursing-station-radio-request-new.php : refNoBasicInfo : "; print_r($refNoBasicInfo); echo " <br><br> \n";

 # Title in the title bar
# $smarty->assign('sToolbarTitle',"$LDRadiology::$LDDiagnosticTest");
 $smarty->assign('sToolbarTitle',"$LDRadiology::New Service Request");

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
# $smarty->assign('sWindowTitle',"$LDRadiology::$LDDiagnosticTest");
 $smarty->assign('sWindowTitle',"$LDRadiology::New Service Request");

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

	$smarty->assign('sIsCash','<input type="radio" name="iscash" id="iscash1" value="1"'.(($is_cash||empty($is_cash))? " checked":"").' onchange="if (changeTransactionType) changeTransactionType();">Cash');
	$smarty->assign('sIsCharge','<input class="segInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0"'.(($is_cash=='0')? " checked":"").' onchange="if (changeTransactionType) changeTransactionType()">Charge');
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
	$smarty->assign('sSelectEnc','<img class="segInput" name="select-enc" id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer;"
       onclick="overlib(
        OLiframeContent(\'seg-radio-select-enc.php\', 700, 400, \'fSelEnc\', 1, \'auto\'),
        WIDTH,700, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
				CAPTION,\'Select registered person\',
        MIDX,0, MIDY,0, 
        STATUS,\'Select registered person\'); return false;"
       onmouseout="nd();">');
	$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="cursor:pointer;font:bold 11px Arial" value="Clear" onclick="clearEncounter()" disabled>');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" readonly>'.$orderaddress.'</textarea>');
	$smarty->assign('sSSClassID','<input type="text" name="discountid" id="discountid" size="5" value="'.$discountid.'" readonly style="font:bold 12px Arial">');
	$smarty->assign('sRefNo','<input class="segInput" name="refno" id="refno" type="text" size="10" value="'.$refno.'" readonly style="font:bold 12px Arial"/>');
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
$smarty->assign('sBtnAddItem','<img type="image" name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_addservicecodes.gif" border="0" style="cursor:pointer;"
			onclick="return overlib(
				OLiframeContent(\'seg-radio-service-tray.php\', 600, 515, \'fOrderTray\', 1, \'auto\'),
					WIDTH,515, TEXTPADDING,0, BORDER,0, 
					STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
					CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
					CAPTIONPADDING,4, 
					CAPTION,\'Add radiological service item from request tray\',
					MIDX,0, MIDY,0, 
					STATUS,\'Add radiological service item from request tray\');"
			onmouseout="nd();">');
# <img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_additems.gif" border="0"></a>');
/*
$smarty->assign('sBtnEmptyList','<a href="javascript:emptyTray()">
		<img type="image" name="btnEmpty" id="btnEmpty" src="'.$root_path.'images/btn_emptylist.gif" border="0" style="cursor:pointer;"></a>');
*/
$smarty->assign('sBtnEmptyList','<img type="image" name="btnEmpty" id="btnEmpty" src="'.$root_path.'images/btn_emptylist.gif" border="0" style="cursor:pointer;" onclick="emptyTray();"></a>');

#$smarty->assign('sBtnEmptyList','<a href="javascript:emptyTray()"><img src="'.$root_path.'images/btn_emptylist.gif" border="0" /></a>');
$smarty->assign('sAdjustedAmount','<input name="show-discount" id="show-discount" type="text" readonly style="color:#006600; font-family:Arial; font-size:15px; font-weight:bold; text-align:right" size="5" value="'.number_format($adjusted_amount, 2, '.', '').'">');
$smarty->assign('sDiscountInfo','<img src="'.$root_path.'images/discount.gif">');
$smarty->assign('sBtnDiscounts', '<img name="btndiscount" id="btndiscount" onclick="saveDiscounts();" src="'.$root_path.'images/btn_discounts.gif" border="0" style="cursor:pointer;display:none">');
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

 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform" id="inputform" onSubmit="return checkRequestForm()">');
 $smarty->assign('sFormEnd','</form>');
?>
<?php
ob_start();
$sTemp='';
?>
	<script type="text/javascript" language="javascript">
		preset(<?= ($is_cash=='0')? "0":"1"?>);
		xajax_populateRequestListByRefNo(<?=$refno? $refno:0?>);	
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

	<input type="hidden" name="is_cash" id="is_cash" value="<?=$is_cash?>" >
	<input type="hidden" id="encounter_nr" name="encounter_nr" value="<?=$encounter_nr?>">
	<input type="hidden" name="discount" id="discount" value="<?=$discount?>" >
	<input type="hidden" name="latest_valid_show-discount" id="latest_valid_show-discount" value="<?=number_format($adjusted_amount, 2, '.', '')?>" >
	<input type="hidden" name="mode" id="mode" value="<?=$mode?$mode:'save'?>">
	<input type="hidden" name="popUp" id="popUp" value="<?=$popUp?$popUp:'0'?>">
	<input type="hidden" name="hasPaid" id="hasPaid" value="<?=$hasPaid?$hasPaid:'0'?>">
	<input type="hidden" name="view_from" id="view_from" value="<?=$view_from?$view_from:''?>">
	<input type="hidden" name="encoder_id" id="encoder_id" value="<?php echo $HTTP_SESSION_VARS['sess_login_personell_nr']; ?>">
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
$smarty->assign('sContinueButton','<img type="image" name="btnSubmit" id="btnSubmit" src="'.$root_path.'images/btn_submitrequest.gif" align="center" style="cursor:pointer" onClick="checkRequestForm();">');
#document.inputform.submit()
# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','radiology/radio-request-new.tpl');
$smarty->display('common/mainframe.tpl');

?>