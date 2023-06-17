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
$breakfile=$root_path.'modules/radiology/'.$breakfile;   # burn added: August 29, 2007
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

echo "nursing-station-radio-request-new.php : _POST : "; print_r($_POST); echo " <br><br> \n";

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
#		"service_date","if_in_house","request_doctor","request_date","encoder","status",	"history",
#		"modify_id","modify_dt","create_id","create_dt"
/*
				$tmp=array();
				if(trim($_POST['request_date'])!=""){
					$_POST['request_date'] = formatDate2STD($_POST['request_date'], $date_format, $tmp);
				}
*/
				if(trim($_POST['request_date'])!=""){
					$_POST['request_date'] = formatDate2STD($_POST['request_date'], $date_format);
				}
				$_POST['request_doctor'] = trim($_POST['request_doctor']);			     	
				$_POST['service_code'] = $_POST['items'];
				$_POST['is_cash'] = $_POST['iscash'];
				$_POST['is_urgent'] = $_POST['priority'];
				$_POST['hasPaid'] = 0;   # not yet paid since thisis just a request
				$_POST['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
/*
[iscash] => 1 [pid] => 000000669275 [ordername] => Abacan Baby Boy [orderaddress] => Fr. Selga Street 
[request_dept] => 157 [request_doctor_in] => 100095 [request_doctor] => 100095 [if_in_house] => 1 
[refno] => [request_date] => 08/30/2007 [priority] => 0 [comments] => 
[pcash] => Array ( [0] => 675.00 [1] => 450.00 ) 
[pcharge] => Array ( [0] => 676.00 [1] => 450.00 ) 
[items] => Array ( [0] => Abd-Up [1] => BEA ) 
[qty] => Array ( [0] => 1 [1] => 1 ) 
[qtyAbd-Up] => 1 
[qtyBEA] => 1 
[discount] => Array ( [0] => [1] => [2] => [3] => [4] => [5] => ) 
[submit] => 1 [encoder] => [is_cash] => 1 [encounter_nr] => 
*/
	#echo "nursing-station-radio-request-new.php : 2 _POST : "; print_r($_POST); echo " <br><br> \n";
	
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
				$_POST['request_doctor'] = trim($_POST['request_doctor']);			     	
   			$_POST['service_code'] = $_POST['items'];
				$_POST['is_cash'] = $_POST['iscash'];
				$_POST['is_urgent'] = $_POST['priority'];
				$_POST['hasPaid'] = 0;   # not yet paid since thisis just a request

   			$_POST['history'] = $radio_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");		
	
				$_POST['findings_date'] = formatDate2STD($_POST['findings_date'], $date_format);# reformat FROM mm/dd/yyyy TO yyyy-mm-dd
	#			if($radio_obj->saveAFinding($batch_nr,$finding_nr,$findings,$radio_impression,$findings_date,$doctor_id,'Update')){
				if($radio_obj->saveAFinding($_POST['batch_nr'],$_POST['findings_nr'],$_POST['findings'],
							$_POST['radio_impression'],$_POST['findings_date'],$_POST['doctor_in_charge'],'Update')){
					$errorMsg='<font style="color:#FF0000">Successfully updated!</font>';
					$smarty->assign('sWarning',"Radiological Request Service successfully updated.");
				}else{
					$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
				}
				break;
		case 'cancel':
	#echo "nursing-station-radio-request-new.php : update mode = '".$mode."' <br> \n";			
	#echo "nursing-station-radio-request-new.php : _POST : "; print_r($_POST); echo " <br><br> \n";
				$_POST['request_doctor'] = trim($_POST['request_doctor']);			     	
   			$_POST['service_code'] = $_POST['items'];
				$_POST['is_cash'] = $_POST['iscash'];
   			$_POST['history'] = $radio_obj->ConcatHistory("Update: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n");		
	
				$_POST['findings_date'] = formatDate2STD($_POST['findings_date'], $date_format);# reformat FROM mm/dd/yyyy TO yyyy-mm-dd
	#			if($radio_obj->saveAFinding($batch_nr,$finding_nr,$findings,$radio_impression,$findings_date,$doctor_id,'Update')){
				if($radio_obj->saveAFinding($_POST['batch_nr'],$_POST['findings_nr'],$_POST['findings'],
							$_POST['radio_impression'],$_POST['findings_date'],$_POST['doctor_in_charge'],'Update')){
					$errorMsg='<font style="color:#FF0000">Successfully updated!</font>';
				}else{
					$errorMsg='<font style="color:#FF0000">'.$radio_obj->getErrorMsg().'</font>';
				}
				break;
	}# end of switch stmt	
	
echo "nursing-station-radio-request-new.php : refno='".$refno."' <br> \n";			
	if ($refNoInfo = $radio_obj->getAllRadioInfoByRefNo($refno)){
		$mode='update';
	}else{
		$mode='save';
	}
echo "nursing-station-radio-request-new.php : mode='".$mode."' <br> \n";			
echo "nursing-station-radio-request-new.php : refNoInfo : "; print_r($refNoInfo); echo " <br><br> \n";


	if (isset($_POST["submit"])) {
		/*
		$data = array(
			'refno'=>$_POST['refno'],
			'pid'=>$_POST['pid'],
			'ordername'=>$_POST['ordername'],
			'orderaddress'=>$_POST['orderaddress'],
			'is_cash'=>$_POST['is_cash'],
			'is_urgent'=>$_POST['priority'],
			'comments'=>$_POST['comments'],
			'create_id'=>$_SESSION['sess_temp_userid'],
			'modify_id'=>$_SESSION['sess_temp_userid'],
			'modify_time'=>date('YmdHis'),
			'create_time'=>date('YmdHis')
		);
		*/
		
		$encounter = $enc_obj->getEncounter($_POST['pid']);
		#echo "sql = ".$enc_obj->sql;
			if (($encounter['encounter_type'] == 2) || ($encounter['encounter_nr'] == NULL)){
				$encounter_nr = 0;
				$encounter_type = 5;   # walkin
			}else{
				$encounter_nr = $encounter['encounter_nr'];
				$encounter_type = $encounter['encounter_type'];	
			}
		/*
		echo "<br>refno = ".$_POST['refno'];
		echo "<br>encounter_nr = ".$encounter_nr;
		echo "<br>encounter_type = ".$encounter_type;
		echo "<br>pid = ".$_POST['pid'];
		echo "<br>is_cash = ".$_POST['is_cash'];
		echo "<br>create_id = ".$_SESSION['sess_temp_userid'];   
		echo "<br>modify_id = ".$_SESSION['sess_temp_userid'];
		echo "<br>modify_dt = ".date('YmdHis');
		echo "<br>create_dt = ".date('YmdHis');
		echo "<br>history = Create: ".date('Y-m-d H:i:s')." [\\".$_SESSION['sess_temp_userid']."]\\n";
		echo "<br>comments = ".$_POST['comments'];
		if ($_POST['request_date']) {
			$time = strtotime($_POST['request_date']);
			//$data["serv_dt"] = date("Ymd",$time);
			echo "<br>request_date = ".date("Ymd",$time);
		}
		*/
		$data = array(
			'refno'=>$_POST['refno'],
			'encounter_nr'=>$encounter_nr,
			'encounter_type'=>$encounter_type,
			'pid'=>$_POST['pid'],
			'is_cash'=>$_POST['is_cash'],
			'create_id'=>$_SESSION['sess_temp_userid'],   
			'modify_id'=>$_SESSION['sess_temp_userid'],   
			'modify_dt'=>date('YmdHis'),   
			'create_dt'=>date('YmdHis'),
			'history'=>"Create: ".date('Y-m-d H:i:s')." [\\".$_SESSION['sess_temp_userid']."]\\n",
			'comments'=>$_POST['comments']
		);
		
		if ($_POST['request_date']) {
			$time = strtotime($_POST['request_date']);
			$data["serv_dt"] = date("Ymd",$time);
		}
		
		if ($_POST["pid"]) $data["pid"] = $_POST["pid"];
		#print_r($data);
		$srvObj->useLabServ();
		$srvObj->setDataArray($data);
		$saveok=$srvObj->insertDataFromInternalArray();
		#echo "<br>sql = ".$srvObj->sql;
		if ($saveok) {
			# Bulk write order items
			$bulk = array();
			foreach ($_POST["items"] as $i=>$v) {
				#$bulk[] = array($_POST["items"][$i],$_POST["qty"][$i],$_POST["pcash"][$i],$_POST["pcharge"][$i]);
				if ($_POST['is_cash'])
					$bulk[] = array($_POST["items"][$i],$_POST["pcash"][$i]);
				else
				   $bulk[] = array($_POST["items"][$i],$_POST["pcharge"][$i]);	
			}
			
			#echo "bulk = ";
			#print_r($bulk);
			$srvObj->clearOrderList($data['refno']);
			$srvObj->addOrders($data['refno'],$bulk);
			
			#------commented by VAN-----
			/*
			# Bulk write discounts
			$bulk = array();
			foreach ($_POST["discount"] as $i=>$v) {
				if ($v) $bulk[] = array($v);
			}
			
			$srvObj->clearDiscounts($data['refno']);
			$srvObj->addDiscounts($data['refno'],$bulk);
			global $db;
			print_r($db->ErrorMsg());
			*/
			$smarty->assign('sWarning',"Radiological Request Service successfully created.");
		}
		else {
			$errorMsg = $db->ErrorMsg();
			if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
				$smarty->assign('sWarning','<strong>Error:</strong> A laboratory service with the same order number already exists in the database.');
			else
				$smarty->assign('sWarning',"<strong>Error:</strong> $errorMsg");
		} 
	}
	
if ($saveok) {
	
}

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$LDRadiology::$LDDiagnosticTest");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDRadiology::$LDDiagnosticTest");

 # Assign Body Onload javascript code
 
 $onLoadJS='onLoad="preSet();"';
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

$lastnr = $srvObj->getLastNr(date("Y-m-d"));
#echo "sql = ".$srvObj->sql;
# Render form values
if (isset($_POST["submit"]) && !$saveok) {
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
else {
	$smarty->assign('sIsCash','<input type="radio" name="iscash" id="iscash1" value="1"  checked="checked" onchange="if (changeTransactionType) changeTransactionType()" />Cash');
	$smarty->assign('sIsCharge','<input class="segInput"  style="margin-left:20px" type="radio" name="iscash" id="iscash0" value="0" onchange="if (changeTransactionType) changeTransactionType()" />Charge');
	$smarty->assign('sOrderEncID','<input id="pid" name="pid" type="hidden" value=""/>');
	$smarty->assign('sOrderName','<input class="segInput" id="ordername" name="ordername" type="text" size="40" value="" style="font:bold 12px Arial;"/>');
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
	$smarty->assign('sClearEnc','<input class="segInput" id="clear-enc" type="button" style="font:bold 11px Arial" value="Clear" onclick="clearEncounter()" disabled/>');
	$smarty->assign('sOrderAddress','<textarea class="segInput" id="orderaddress" name="orderaddress" cols="37" rows="2" style="font:bold 12px Arial"></textarea>');
	$smarty->assign('sRefNo','<input class="segInput" name="refno" type="text" size="10" value="'.$lastnr.'" style="font:bold 12px Arial"/>');
	$smarty->assign('sResetRefNo','<input class="segInput" type="button" value="Reset" style="font:bold 11px Arial"/>');
	
	$curDate = date("m/d/Y");
	$smarty->assign('sOrderDate','<input name="request_date" id="request_date" type="text" size="10" value="'.$curDate.'" style="font:bold 12px Arial">');
#	$smarty->assign('sOrderDate','<input name="request_date" type="text" size="10" value="'.$curDate.'" style="font:bold 12px Arial">');
	$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="request_date_trigger" align="absmiddle" style="cursor:pointer">');
	
	$smarty->assign('sNormalPriority','<input type="radio" name="priority" value="0" checked="checked" />Normal');
	$smarty->assign('sUrgentPriority','<input type="radio" name="priority" value="1" />Urgent');
	$smarty->assign('sComments','<textarea class="segInput" name="comments" id="comments" cols="15" rows="2" style="float:left; margin-left:5px; font-size:12px; font-weight:normal; font-style:italic"></textarea>');
	
	$smarty->assign('sOrderItems',"
				<tr>
					<td colspan=\"7\">Request list is currently empty...</td>
				</tr>");
}

$smarty->assign('sClinicalImpression',$LDClinicalImpression);
$smarty->assign('segInputClinicalImpression',
						'	<textarea name="clinical_info" id="clinical_info" cols=30 rows=2 wrap="physical" onChange="trimString(this);" onBlur="trimString(this);">'.
						stripslashes($radioRequestInfo['clinical_info']).
						'	</textarea>');

$smarty->assign('sBtnAddItem','<a href="javascript:void(0);"
       onclick="return overlib(
        OLiframeContent(\'nursing-station-radio-tray.php\', 600, 340, \'fOrderTray\', 1, \'auto\'),
        WIDTH,600, TEXTPADDING,0, BORDER,0, 
				STICKY, SCROLL, CLOSECLICK, DRAGGABLE,
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
	# burn added : August 30, 2007
#		$radioRequestInfo = $ergbis->FetchRow();
		if ($radioRequestInfo['if_in_house']=='1'){
			# get the department where the requesting doctor belongs
			$requestDocInfo = $dept_obj->getDeptofDoctor($radioRequestInfo['request_doctor']);
			# $requestDocInfo['nr'] , department number
			# $requestDocInfo['id'] , department id name
			# $requestDocInfo['name_formal'] , department full name
		}
		echo "nursing-station-radio-request-new.php : requestDocInfo=''".$requestDocInfo."' \n <br>";

ob_start();
$sTemp='';
?>
		<select name="request_dept" id="request_dept" onChange="jsSetDoctorsOfDept();">
		</select>
<?php 
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sRequestingDept',$sTemp);

ob_start();
$sTemp='';
?>
		<select name="request_doctor_in" id="request_doctor_in" onChange="jsSetDepartmentOfDoc();">
		</select>
		<br>
		<input type="text" name="request_doctor_out" id="request_doctor_out" size=40 onBlur="trimString(this);" value="<?php if($radioRequestInfo['if_in_house']=='0') echo $radioRequestInfo['request_doctor_name']; ?>"<?= $radioRequestInfo['if_in_house']? " disabled":"" ?>>
<?php 
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sRequestDoctor',$sTemp);

ob_start();
$sTemp='';
?>
		<input type="hidden" name="request_doctor" id="request_doctor" value="<?= $radioRequestInfo['request_doctor'] ?>">
		<input type="hidden" name="if_in_house" id="if_in_house" value="<?= $radioRequestInfo['if_in_house']? "1":"0" ?>">

	<script language="javascript">
<?php
				if ($radioRequestInfo['if_in_house']){
?>
					xajax_setALLDepartment(0);	//set the list of ALL departments
						//set the list of ALL doctors under $requestDocInfo['nr'] department
					xajax_setDoctors("<?= $requestDocInfo['nr'] ?>","<?= $radioRequestInfo['request_doctor'] ?>");
		
		//			xajax_setDoctors("<?= $requestDocInfo['nr'] ?>",0);	//set the list of ALL doctors under $requestDocInfo['nr'] department
		//			xajax_setDepartmentOfDoc("<?= $radioRequestInfo['request_doctor'] ?>");
<?php
				}else{
?>
					xajax_setALLDepartment(0);	//set the list of ALL departments
					xajax_setDoctors(0,0);	//set the list of ALL doctors from ALL departments
<?php
				}
?>
	</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sRequestOptions',$sTemp);
?>	
<?php
ob_start();
$sTemp='';

include_once($root_path."include/care_api_classes/class_discount.php");
$discountClass = new SegDiscount();
$src = "";
if ($result = $discountClass->getAllDataObject()) {
	while ($row = $result->FetchRow()) {
		echo '	<input type="hidden" id="discount_'.$row['discountid'].'" name="discount[]" discount="'.$row["discount"].'" value="">';
		echo "\n";
	}
}
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
<!--	
	<input type="hidden" name="editpencnum"   id="editpencnum"   value="">	
	<input type="hidden" name="editpentrynum" id="editpentrynum" value="">
	<input type="hidden" name="editpname" id="editpname" value="">
	<input type="hidden" name="editpqty"  id="editpqty"  value="">
	<input type="hidden" name="editppk"   id="editppk"   value="">
	<input type="hidden" name="editppack" id="editppack" value="">
-->	
	<input type="hidden" name="is_cash" id="is_cash" value="<?=$is_cash?>" >
		<!-- burn added : August 29, 2007 -->
	<input id="encounter_nr" name="encounter_nr" type="hidden" value="<?=$encounter_nr?>">
	<input type="hidden" name="mode" id="mode" value="<?=$mode?$mode:'save'?>">
<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';	
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
$smarty->assign('sContinueButton','<input type="image" src="'.$root_path.'images/btn_submitorder" align="center">');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','nursing/nursing-station-radio-request-new.tpl');
$smarty->display('common/mainframe.tpl');

?>