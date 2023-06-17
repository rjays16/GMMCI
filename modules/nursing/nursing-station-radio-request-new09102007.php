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
#	define('NO_2LEVEL_CHK',1);
	$local_user='ck_lab_user';
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/inc_front_chain_lang.php');
	require($root_path.'modules/nursing/ajax/nursing-station-radio-common.php');

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


	# Create products object
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	$srvObj=new SegLab();
	
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;

	# Create radiology object
	require_once($root_path.'include/care_api_classes/class_radiology.php');
	$radio_obj = new SegRadio;
	
	#include_once($root_path."include/care_api_classes/class_order.php");
	#$order_obj = new SegOrder("pharma");
	global $db;
	
	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');

#echo "nursing-station-radio-request-new.php : b4 switch _POST : "; print_r($_POST); echo " <br><br> \n";

	switch($mode){
		case 'save':
	#echo "nursing-station-radio-request-new.php : save mode = '".$mode."' <br> \n";
	#echo "nursing-station-radio-request-new.php : _POST : "; print_r($_POST); echo " <br><br> \n";
	#echo "nursing-station-radio-request-new.php : _POST['findings_date'] = '".formatDate2STD($_POST['findings_date'], $date_format)."' <br> \n";
			# table 'seg_radio_serv'
#		"refno","request_date","encounter_nr",	"pid", "ordername","orderaddress","is_cash","hasPaid",
#		"is_urgent","comments","status","history","modify_id","modify_dt","create_id","create_dt"

			# table 'seg_radio_serv'
#		"batch_nr", "refno", "dept_nr", "clinical_info", "service_code", "price_cash", "price_charge",
#		"service_date","is_in_house","request_doctor","request_date","encoder","status",	"history",
#		"modify_id","modify_dt","create_id","create_dt"
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
	echo "nursing-station-radio-request-new.php : save 2: _POST : "; print_r($_POST); echo " <br><br> \n";
#	exit();
				if($refno = $radio_obj->saveRadioRefNoInfoFromArray($_POST)){
					$errorMsg='<font style="color:#FF0000">Successfully saved!</font>';
					$smarty->assign('sWarning',"Radiological Request Service successfully created.");
				}else{
					# $errorMsg = $db->ErrorMsg();
					$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
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
				$_POST['hasPaid'] = 0;   # not yet paid since this is just a request
				$_POST['encoder'] = $HTTP_SESSION_VARS['sess_user_name'];
   			$_POST['history'] = $radio_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");		

	echo "nursing-station-radio-request-new.php : save 2: _POST : "; print_r($_POST); echo " <br><br> \n";
/*
	$current_list = $radio_obj->getListedRequestsByRefNo($_POST['refno']);
echo "nursing-station-radio-request-new.php : current_list : "; print_r($current_list); echo " <br><br> \n";	
	$current_deleted_list = $radio_obj->getListedRequestsByRefNo($_POST['refno'],"AND status IN ($radio_obj->dead_stat)");
echo "nursing-station-radio-request-new.php : current_deleted_list : "; print_r($current_deleted_list); echo " <br><br> \n";	
	$update_only_list = array_intersect($_POST['service_code'],$current_list);
echo "nursing-station-radio-request-new.php : update_only_list : "; print_r($update_only_list); echo " <br><br> \n";	
	$add_only_list = array_diff($_POST['service_code'],$current_list);
echo "nursing-station-radio-request-new.php : add_only_list 1 : "; print_r($add_only_list); echo " <br><br> \n";	
	$update_status_only_list = array_intersect($current_deleted_list,$add_only_list);
echo "nursing-station-radio-request-new.php : update_status_only_list : "; print_r($update_status_only_list); echo " <br><br> \n";	
	$add_only_list = array_diff($add_only_list,$update_status_only_list);
echo "nursing-station-radio-request-new.php : add_only_list 2 : "; print_r($add_only_list); echo " <br><br> \n";	
	$delete_only_list = array_diff($current_list,$_POST['service_code']);
echo "nursing-station-radio-request-new.php : delete_only_list : "; print_r($delete_only_list); echo " <br><br> \n";	
exit();
*/
				if($radio_obj->updateRadioRefNoInfoFromArray($_POST)){
					$errorMsg='<font style="color:#FF0000">Successfully updated!</font>';
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
*/					header('Location: '.$breakfile);
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
echo "nursing-station-radio-request-new.php : refno='".$refno."' <br> \n";			

	$mode='save';   # default mode
	if ($refNoBasicInfo = $radio_obj->getBasicRadioServiceInfo($refno)){
		$mode='update';
		extract($refNoBasicInfo);
		if (empty($refNoBasicInfo['pid']) || !$refNoBasicInfo['pid']){
			$person_name = $refNoBasicInfo['ordername'];
		}else{
				# in case there is an updated profile of the person
			$person_name = $refNoBasicInfo['name_last'].' '.$refNoBasicInfo['name_first'];
		}
echo "nursing-station-radio-request-new.php : before : request_date='".$request_date."' <br> \n";			
		$request_date = formatDate2Local($request_date,$date_format); 
echo "nursing-station-radio-request-new.php : after : request_date='".$request_date."' <br> \n";			
	}else{
		#
	}
	#end of if-stmt
echo "nursing-station-radio-request-new.php : mode='".$mode."' <br> \n";			
echo "nursing-station-radio-request-new.php : refNoInfo : "; print_r($refNoInfo); echo " <br><br> \n";

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$LDRadiology::$LDDiagnosticTest");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDRadiology::$LDDiagnosticTest");

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

<script type="text/javascript" language="javascript">
<?php
//	require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/radio-request-gui.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;

	function openOrderTray() {
		window.open("seg-request-tray.php<?=URL_APPEND?>&clear_ck_sid=<?=$clear_ck_sid?>","patient_select","width=720,height=500,menubar=no,resizable=no,scrollbars=yes");
	}
	
	//-----------added by VAN ---------------
	function preSet(){
		//alert("preSet");
		if ($("iscash1").checked)
			document.getElementById('is_cash').value = 1;
		else
			document.getElementById('is_cash').value = 0;	
	}
-->
</script>

<?php
	$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Assign prompt messages

#$lastnr = $srvObj->getLastNr(date("Y-m-d"));
#echo "sql = ".$srvObj->sql;
# Render form values
if (isset($_POST["submit"]) && !$saveok && 0) {
	$smarty->assign('sRefNo','<input name="refno" type="text" size="8" value="'.$_POST['refno'].'" style="font:bold 12px Arial"/>');
#	$smarty->assign('sOrderDate',date("m/d/Y",strtotime($_POST['request_date'])) .'<input name="request_date" type="hidden" value="'.$_POST['request_date'].'">');
#	$smarty->assign('sOrderDate',date("m/d/Y",strtotime($_POST['request_date'])) .'<input name="request_date" id="request_date" type="hidden" value="'.$_POST['request_date'].'">');
	$smarty->assign('sOrderDate','<input name="request_date" id="request_date" type="text" size="10" value="'.date("m/d/Y",strtotime($_POST['request_date'])).'" style="font:bold 12px Arial">');
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="request_date_trigger" align="absmiddle" style="cursor:pointer">');
	$count=0;
	$smarty->assign('sIsCash','<input type="radio" name="iscash" id="iscash1" value="1" '.(($_POST["iscash"]!="0")?'checked="checked" ':'').'onchange="if (changeTransactionType) changeTransactionType()" />Cash');
	$smarty->assign('sIsCharge','<input class="segInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" '.(($_POST["iscash"]=="0")?'checked="checked" ':'').'onchange="if (changeTransactionType) changeTransactionType()" />Charge');
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$_POST['ordername'].'" style="font:bold 12px Arial; float:left; "/>');
	#$smarty->assign('sSelectEnc','<input class="segInput" id="select-enc" type="image" src="../../images/btn_encounter_small.gif" border="0" onclick="alert(\'Hello\')" style="margin-left:2px"/>');
	$smarty->assign('sSelectEnc','<input class="segInput" name="select-enc" id="select-enc" type="image" src="../../images/btn_encounter_small.gif" border="0" style=""
       onclick="overlib(
        OLiframeContent(\'seg-radio-select-enc.php\', 700, 400, \'fSelEnc\', 1, \'auto\'),
        WIDTH,700, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, DRAGGABLE,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
				CAPTION,\'Select registered person\',
        MIDX,0, MIDY,0, 
        STATUS,\'Select registered person\'); return false;"
       onmouseout="nd();" />');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial">'.$_POST['orderaddress'].'</textarea>');
	$smarty->assign('sResetRefNo','<input class="segInput" type="button" value="Reset" style="font:bold 11px Arial"/>');
	$smarty->assign('sNormalPriority','<input type="radio" name="priority" value="0" '.(($_POST["priority"]!="1")?'checked="checked" ':'').'/>Normal');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" value="1" '.(($_POST["priority"]=="1")?'checked="checked" ':'').'/>Urgent');
	$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic"></textarea>');
	
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"7\">Request list is currently empty...</td>
				</tr>");

	/*
	# Note: make a class function for this part later
	$dbtable='care_pharma_products_main';
	$prctable='seg_pharma_prices';
	$src = "";
	foreach ($_POST['items'] as $i=>$v) {
		$sql="SELECT d.*,p.ppriceppk,p.chrgrpriceppk,p.cshrpriceppk\n".
			"FROM $dbtable AS d LEFT JOIN $prctable AS p ON d.bestellnum=p.bestellnum\n".
			"WHERE d.bestellnum='$v'";
		$result=$db->Execute($sql);
		if ($result) {
			$count++;
			$row=$result->FetchRow();
			$alt = ($count%2)+1;
			$qty = $_POST['qty'][$i];
			if (!is_numeric($qty)) $qty=0;
			$src .= '
				<tr class="wardlistrow'.$alt.'" id="row'.$row['bestellnum'].'">
					<input type="hidden" name="items[]" id="rowID'.$row['bestellnum'].'" value="'.$row['bestellnum'].'" />
					<input type="hidden" id="rowPrcCash'.$row['bestellnum'].'" value="'.$row['cshrpriceppk'].'" />
					<input type="hidden" id="rowPrcCharge'.$row['bestellnum'].'" value="'.$row['chrgrpriceppk'].'" />
					<td class="centerAlign"><a href="javascript:removeItem(\''.$row['bestellnum'].'\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>
					<td align="centerAlign"></td>
					<td>'.$row['bestellnum'].'</td>
					<td>'.$row['artikelname'].'</td>
					<td class="rightAlign">
						'.number_format($row['cshrpriceppk'], 2).'/'.number_format($row['chrgrpriceppk'], 2).'						
					</td>
					<td class="rightAlign">'.$qty.'</td>
					<td class="rightAlign">'.number_format($row['cshrpriceppk']*$qty, 2).'/'.number_format($row['chrgrpriceppk']*$qty, 2).'</td>
				</tr>
';
		}
	}
	$smarty->assign('sOrderItems',$src);
	*/
}
else { }
	$smarty->assign('sIsCash','<input type="radio" name="iscash" id="iscash1" value="1"'.(($is_cash||empty($is_cash))? " checked":"").' onchange="if (changeTransactionType) changeTransactionType()" />Cash');
	$smarty->assign('sIsCharge','<input class="segInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0"'.(($is_cash=='0')? " checked":"").' onchange="if (changeTransactionType) changeTransactionType()" />Charge');
	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$pid.'"/>');
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$person_name.'" style="font:bold 12px Arial;"/>');
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
	$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" onclick="clearEncounter()" disabled/>');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial">'.$orderaddress.'</textarea>');
	$smarty->assign('sRefNo','<input class="segInput" name="refno" type="text" size="10" value="'.$refno.'" readonly style="font:bold 12px Arial"/>');
#	$smarty->assign('sResetRefNo','<input class="segInput" type="button" value="Reset" style="font:bold 11px Arial"/>');
	
	$curDate = ($request_date)? $request_date:date("m/d/Y");
	$smarty->assign('sOrderDate','<input name="request_date" id="request_date" type="text" size="10" value="'.$curDate.'" style="font:bold 12px Arial">');
#	$smarty->assign('sOrderDate','<input name="request_date" type="text" size="10" value="'.$curDate.'" style="font:bold 12px Arial">');
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="request_date_trigger" align="absmiddle" style="cursor:pointer">');
	
	$smarty->assign('sNormalPriority','<input type="radio" name="priority" value="0"'.($is_urgent? "": " checked").'>Normal');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" value="1"'.($is_urgent? " checked": "").'>Urgent');
	$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic">'.$comments.'</textarea>');
	
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"7\">Request list is currently empty...</td>
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
		 	<input type="image" name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_additems.gif" border="0"></a>');
# <img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_additems.gif" border="0"></a>');
$smarty->assign('sBtnEmptyList','<a href="javascript:emptyTray()">
		<input type="image" name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_emptylist.gif" border="0"></a>');
#$smarty->assign('sBtnEmptyList','<a href="javascript:emptyTray()"><img src="'.$root_path.'images/btn_emptylist.gif" border="0" /></a>');
$smarty->assign('sDiscountInfo','<img src="'.$root_path.'images/discount.gif">');
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
#$smarty->assign('sBtnPDF','<a href="#"><img src="'.$root_path.'images/btn_printpdf.gif" border="0"></a>');

	$jsCalScript = "<script type=\"text/javascript\">
		Calendar.setup ({
			inputField : \"request_date\", ifFormat : \"$phpfd\", showsTime : false, button : \"request_date_trigger\", singleClick : true, step : 1
		});
	</script>
	";

	$smarty->assign('jsCalendarSetup', $jsCalScript);


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
		preset(<?= ($is_cash=='0')? "0":"1"?>);
		xajax_populateRequestListByRefNo(<?=$refno? $refno:0?>);	
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
	<input id="encounter_nr" name="encounter_nr" type="hidden" value="<?=$encounter_nr?>">
	<input type="hidden" name="mode" id="mode" value="<?=$mode?$mode:'save'?>">
<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->assign('sHiddenInputs',$sTemp);
#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';alert($F(\'mode\'));" onsubmit="return false;" style="cursor:pointer">');
#$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';alert($F(\'mode\'));" style="cursor:pointer">');
if ($mode=="update"){
	$sBreakImg ='cancel.gif';
	$smarty->assign('sBreakButton','<input type="image" '.createLDImgSrc($root_path,$sBreakImg,'0','center').' align="center" alt="'.$LDBack2Menu.'" onclick="$(\'mode\').value=\'cancel\';" style="cursor:pointer">');
}else{
	$sBreakImg ='close2.gif';	
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
}
$smarty->assign('sContinueButton','<input type="image" src="'.$root_path.'images/btn_submitorder" align="center">');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','nursing/nursing-station-radio-request-new.tpl');
$smarty->display('common/mainframe.tpl');

?>