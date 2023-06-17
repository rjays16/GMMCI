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
	# Start Smarty templating here
 /**
 * LOAD Smarty
 */
	
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
 	
	# Create laboratory service object
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	$srvObj=new SegLab();
	
	require_once($root_path.'include/care_api_classes/class_encounter.php');
	$enc_obj=new Encounter;
	
	#include_once($root_path."include/care_api_classes/class_order.php");
	#$order_obj = new SegOrder("pharma");
	global $db;
	
	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');
	
	if (isset($_POST["submit"])) {
		$data = array(
			'refno'=>$_POST['refno'],
			'encounter_nr'=>$encounter_nr,
			'encounter_type'=>$encounter_type,
			'pid'=>$_POST['pid'],
			'is_cash'=>$_POST['is_cash'],
			'is_urgent'=>$_POST['priority'],
			'create_id'=>$_SESSION['sess_temp_userid'],   
			'modify_id'=>$_SESSION['sess_temp_userid'],   
			'modify_dt'=>date('YmdHis'),   
			'create_dt'=>date('YmdHis'),
			'history'=>"Create: ".date('Y-m-d H:i:s')." [\\".$_SESSION['sess_temp_userid']."]\\n",
			'comments'=>$_POST['comments'],
			'ordername'=>$_POST['ordername'],
			'orderaddress'=>$_POST['orderaddress']
		);
		if ($_POST['orderdate']) {
			$time = strtotime($_POST['orderdate']);
			$data["serv_dt"] = date("Ymd",$time);
		}
		if ($_POST["pid"]) $data["pid"] = $_POST["pid"];
		$srvObj->useLabServ();
		$srvObj->setDataArray($data);
		
		#update table
		$srvObj->where=" refno='".$_GET["ref"]."'";
		$saveok=$srvObj->updateDataFromInternalArray($_GET["ref"]);
		
		if ($saveok) {
			
			if ($_POST["items"]!=NULL){
				# Bulk write order items
				$bulk = array();
				foreach ($_POST["items"] as $i=>$v) {
					#$bulk[] = array($_POST["items"][$i],$_POST["qty"][$i],$_POST["pcash"][$i],$_POST["pcharge"][$i]);
					if ($_POST['is_cash'])
						#$bulk[] = array($_POST["items"][$i],$_POST["pcash"][$i],$_POST["pcash"][$i],$_POST["pcharge"][$i]);
						$bulk[] = array($_POST["items"][$i],$_POST["pcash"][$i],$_POST["pcash"][$i],$_POST["pcharge"][$i],$_POST["pC1"][$i],$_POST["pC2"][$i],$_POST["pC3"][$i]);
					else
				  		#$bulk[] = array($_POST["items"][$i],$_POST["pcharge"][$i],$_POST["pcash"][$i],$_POST["pcharge"][$i]);	
						$bulk[] = array($_POST["items"][$i],$_POST["pcharge"][$i],$_POST["pcash"][$i],$_POST["pcharge"][$i],$_POST["pC1"][$i],$_POST["pC2"][$i],$_POST["pC3"][$i]);	
				}
			
				$srvObj->clearOrderList($data['refno']);
				$srvObj->addOrders($data['refno'],$bulk);
			
				# Bulk write discounts
				$bulk = array();
				foreach ($_POST["discount"] as $i=>$v) {
					if ($v) $bulk[] = array($v);
				}
				
				if ($bulk!=NULL){
					$srvObj->clearDiscounts($data['refno']);
					$srvObj->addDiscounts($data['refno'],$bulk);
				}	
				global $db;
				print_r($db->ErrorMsg());
			}
			$smarty->assign('sWarning',"Laboratory Service item successfully created.");
		}
		else {
			echo "save false";
			$errorMsg = $db->ErrorMsg();
			if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
				$smarty->assign('sWarning','<strong>Error:</strong> A laboratory service with the same request number already exists in the database.');
			else
				$smarty->assign('sWarning',"<strong>Error:</strong> $errorMsg");
		}
	}
if ($saveok) {
	
}

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$LDLab::$LDLabNewTest");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDLab::$LDLabNewTest");

 # Assign Body Onload javascript code
 $onLoadJS='onload="preSet();refreshDiscount();"';
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
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/request-gui.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;

	function openOrderTray() {
		window.open("seg-request-tray.php<?=URL_APPEND?>&clear_ck_sid=<?=$clear_ck_sid?>","patient_select","width=720,height=500,menubar=no,resizable=no,scrollbars=yes");
	}
	
	function clearEncounter() {
		var iscash = $("iscash1").checked;
		$('ordername').value="";
		$('ordername').readOnly=!iscash;
		$('orderaddress').value="";
		$('orderaddress').readOnly=!iscash;
		$('pid').value="";
		$('clear-enc').disabled = true;
	}
	
	function emptyTray() {
		clearOrder($('order-list'));
		appendOrder($('order-list'),null);
		refreshDiscount();
	}
	
	//-----------added by VAN ---------------
	function preSet(){
		//alert("preSet");
		if ($("iscash1").checked)
			document.getElementById('is_cash').value = 1;
		else
			document.getElementById('is_cash').value = 0;	
	}
	
	function resetRefno(){
		//alert("resetRefno");
		document.getElementById('refno').value = document.getElementById('lastrefno').value;
	}
-->
</script>

<?php
	$sTemp = ob_get_contents();
if (!isset($_GET["ref"])) {
	die("No reference number specified");
	exit;
}
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Fetch order data
	$Ref = $_GET["ref"];
	$infoResult = $srvObj->getOrderInfo($Ref);
	#echo "getOrderInfo = ".$srvObj->sql;
	$saved_discounts = $srvObj->getOrderDiscounts($Ref);
	#echo "getOrderDiscounts = ".$srvObj->sql;
	if ($infoResult)	$info = $infoResult->FetchRow();
		
# Render form values
	$readOnly = (!$info['is_cash'] || $info['pid']) ? 'readonly="readonly"' : "";

	$smarty->assign('sRefNo','<input name="refno" id="refno" readonly="1" type="text" size="8" value="'.$Ref.'" style="font:bold 12px Arial"/>');
	
	if ($info['serv_dt']) {
			$time = strtotime($info['serv_dt']);
			$requestDate = date("m/d/Y",$time);
	}
	
	$smarty->assign('sOrderDate','<input name="orderdate" id="orderdate" type="text" size="10" value="'.$requestDate.'" style="font:bold 12px Arial">');
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="orderdate_trigger" align="absmiddle" style="cursor:pointer">');

	$count=0;
	$smarty->assign('sIsCash','<input type="radio" name="iscash" id="iscash1" value="1" '.(($info["is_cash"]!="0")?'checked="checked" ':'').'onchange="if (changeTransactionType) changeTransactionType()" />Cash');
	$smarty->assign('sIsCharge','<input class="segInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" '.(($info["is_cash"]=="0")?'checked="checked" ':'').'onchange="if (changeTransactionType) changeTransactionType()" />Charge');
	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value="'.$info["pid"].'"/>');
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="'.$info['ordername'].'" style="font:bold 12px Arial; float:left;" '.$readOnly.'/>');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial" '.$readOnly.'>'.$info['orderaddress'].'</textarea>');
	$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial;cursor:pointer" value="Clear" onclick="clearEncounter()"'.(($info['pid'])?'':' disabled="disabled"').' />');
	$smarty->assign('sSelectEnc','<input class="segInput" id="select-enc" type="image" src="../../images/btn_encounter_small.gif" border="0" onclick="alert(\'Hello\')" style="margin-left:2px"/>');
	$smarty->assign('sResetRefNo','<input class="segInput" type="button" disabled value="Reset" onClick ="resetRefno();" style="font:bold 11px Arial;cursor:pointer"/>');
	$smarty->assign('sNormalPriority','<input type="radio" name="priority" value="0" '.(($info["is_urgent"]!="1")?'checked="checked" ':'').'/>Normal');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" value="1" '.(($info["is_urgent"]=="1")?'checked="checked" ':'').'/>Urgent');
	$smarty->assign('sComments','<textarea class="segInput" name="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic"></textarea>');

	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"7\">Request list is currently empty...</td>
				</tr>");

	# Note: make a class function for this part later
	$result = $srvObj->getOrderitems($Ref);
	#echo "getOrderitems sql = ".$srvObj->sql;
	$rows=array();
	while ($row=$result->FetchRow()) {
		$rows[] = $row;
	}
	foreach ($rows as $i=>$row) {
		if ($row) {
			$count++;
			$alt = ($count%2)+1;
			if ($info["is_cash"])
				$prc=$row['price_cash'];
			else
				$prc=$row['price_charge'];
			$src .= '
				<tr class="wardlistrow'.$alt.'" id="row'.$row['service_code'].'">
					<input type="hidden" name="items[]" id="rowID'.$row['service_code'].'" value="'.$row['service_code'].'" />
					<input type="hidden" name="pcash[]" id="rowPrcCash'.$row['service_code'].'" value="'.$row['price_cash'].'" />
					<input type="hidden" name="pcharge[]" id="rowPrcCharge'.$row['service_code'].'" value="'.$row['price_charge'].'" />
					<input type="hidden" name="pC1[]" id="rowPrcC1'.$row['service_code'].'" value="'.$row['price_C1'].'" />
					<input type="hidden" name="pC2[]" id="rowPrcC2'.$row['service_code'].'" value="'.$row['price_C2'].'" />
					<input type="hidden" name="pC3[]" id="rowPrcC3'.$row['service_code'].'" value="'.$row['price_C3'].'" />
					<td class="centerAlign"><a href="javascript:removeItem(\''.$row['service_code'].'\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>
					<td align="centerAlign"></td>
					<td>'.$row['service_code'].'</td>
					<td>'.$row['name'].'</td>
					<td class="rightAlign" id="prc'.$row["service_code"].'">'.number_format($prc, 2).'</td>
					<td class="rightAlign" id="tot'.$row["service_code"].'">'.number_format($prc, 2).'</td>
				</tr>
';
		}
	}
	if ($src) $smarty->assign('sOrderItems',$src);

	$smarty->assign('sSelectEnc','<input class="segInput" id="select-enc" type="image" src="../../images/btn_encounter_small.gif" border="0" style=""
       onclick="overlib(
        OLiframeContent(\'seg-lab-select-enc.php\', 700, 400, \'fSelEnc\', 1, \'auto\'),
        WIDTH,700, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, DRAGGABLE,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
				CAPTION,\'Select registered person\',
        MIDX,0, MIDY,0, 
        STATUS,\'Select registered person\'); return false;"
       onmouseout="nd();" />');

	$smarty->assign('sBtnAddItem','<a href="javascript:void(0);"
       onclick="return overlib(
        OLiframeContent(\'seg-request-tray.php\', 600, 340, \'fOrderTray\', 1, \'auto\'),
        WIDTH,600, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, DRAGGABLE,
				CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
        CAPTIONPADDING,4, 
				CAPTION,\'Add laboratory service item from request tray\',
        MIDX,0, MIDY,0, 
        STATUS,\'Add laboratory service item from request tray\');"
       onmouseout="nd();">
			 <img name="btndiscount" id="btndiscount" src="'.$root_path.'images/btn_additems.gif" border="0"></a>');
	$smarty->assign('sBtnEmptyList','<a href="javascript:emptyTray()"><img src="'.$root_path.'images/btn_emptylist.gif" border="0" /></a>');
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

#-------------added by VAN----------

	$jsCalScript = "<script type=\"text/javascript\">
		Calendar.setup ({
			inputField : \"orderdate\", ifFormat : \"$phpfd\", showsTime : false, button : \"orderdate_trigger\", singleClick : true, step : 1
		});
	</script>
	";

	$smarty->assign('jsCalendarSetup', $jsCalScript);
#----------------------------------

if($error=="refno_exists"){
	$smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
	$smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}


 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform" onSubmit="return prufform(this)">');
 $smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

include_once($root_path."include/care_api_classes/class_discount.php");
$discountClass = new SegDiscount();
$src = "";

/*
if ($result = $discountClass->getAllDataObject()) {
	$posted_discounts=array();
	if ($_POST["discount"]) {
		foreach ($_POST["discount"] as $i=>$v) {
			if ($v) $posted_discounts[$v] = $v;
		}
	}

	while ($row = $result->FetchRow()) {
		echo '	<input type="hidden" id="discount_'.$row['discountid'].'" name="discount[]" discount="'.$row["discount"].'" value="'.$posted_discounts[$row["discountid"]].'" />';
	}
}
*/

if ($result = $discountClass->getAllDataObject()) {
	$posted_discounts=array();
	if ($saved_discounts) {
		foreach ($saved_discounts as $i=>$v) {
			if ($v) $posted_discounts[$v] = $v;
		}
	}

	while ($row = $result->FetchRow()) {
		echo '	<input type="hidden" id="discount_'.$row['discountid'].'" name="discount[]" discount="'.$row["discount"].'" value="'.$posted_discounts[$row["discountid"]].'" />';
	}
}

?>
	<input type="hidden" name="submit" value="1" />
  <input type="hidden" name="sid" value="<?php echo $sid?>">
  <input type="hidden" name="lang" value="<?php echo $lang?>">
  <input type="hidden" name="cat" value="<?php echo $cat?>">
  <input type="hidden" name="userck" value="<?php echo $userck?>">  
  <input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
  <input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
  <input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
  <input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
  <input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
  <input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
	
	<input type="hidden" name="editpencnum"   id="editpencnum"   value="">	
	<input type="hidden" name="editpentrynum" id="editpentrynum" value="">
	<input type="hidden" name="editpname" id="editpname" value="">
	<input type="hidden" name="editpqty"  id="editpqty"  value="">
	<input type="hidden" name="editppk"   id="editppk"   value="">
	<input type="hidden" name="editppack" id="editppack" value="">
	
	<input type="hidden" name="is_cash" id="is_cash" value=<?=$is_cash; ?> >
	<input type="hidden" name="lastrefno" id="lastrefno" value=<?=$_GET["ref"]; ?> >
	
<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';	
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
$smarty->assign('sContinueButton','<input type="image" src="'.$root_path.'images/btn_submitorder" align="center">');

$fileforward="seg-lab-request-new-list.php".URL_REDIRECT_APPEND."&user_origin=".$user_origin;

$smarty->assign('sViewRequest','<a href="'.$fileforward.'"><img '.createLDImgSrc($root_path,'showrequest.gif','0','left').' border=0 alt="View the List of Requestors"></a>');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','laboratory/form_new.tpl');
$smarty->display('common/mainframe.tpl');

?>