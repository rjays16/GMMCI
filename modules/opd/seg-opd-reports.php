<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
0* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','cashier.php');
$local_user='ck_cashier_user';
require_once($root_path.'include/inc_front_chain_lang.php');

# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$php_date_format = strtolower($date_format);
$php_date_format = str_replace("dd","d",$php_date_format);
$php_date_format = str_replace("mm","m",$php_date_format);
$php_date_format = str_replace("yyyy","Y",$php_date_format);
$php_date_format = str_replace("yy","y",$php_date_format);

$title='Cashier';
if (!$_GET['from'])
	$breakfile=$root_path."modules/pharmacy/seg-pharma-order-functions.php".URL_APPEND."&userck=$userck";
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/cashier/cashier-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$imgpath=$root_path."pharma/img/";
$thisfile='seg-cashier-reports.php';

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"OPD::Reports");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"OPD Reports");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""');

 # Collect javascript code
 ob_start()

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

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script language="javascript" type="text/javascript">
<!--
	var URL_FORWARD = "<?= URL_APPEND."&clear_ck_sid=$clear_ck_sid" ?>";

	function pSearchClose() {
		cClick();
	}
	
	function selOnChange() {
		var optSelected = $('selreport').options[$('selreport').selectedIndex];
		var spans = document.getElementsByName('selOptions');
		for (var i=0; i<spans.length; i++) {
			if (optSelected) {
				if (spans[i].getAttribute("segOption") == optSelected.value) {
					spans[i].style.display = "";
				}
				else
					spans[i].style.display = "none";
			}
		}
	}

	
	function openReport() {
		var rep = $('selreport').options[$('selreport').selectedIndex].value
		var url = 'seg-opd-report-'+rep+'.php?'
		var query = new Array()
		var params = document.getElementsByName('param')
		for (var i=0; i<params.length; i++) {
			if (params[i].getAttribute("segOption") == rep) {
				var mit;
				if (params[i].type=='checkbox') mit=params[i].checked;
				else if (params[i].type=='radio') mit=params[i].checked;
				else mit=params[i].value;
				if (mit) query.push(params[i].getAttribute('paramName')+'='+params[i].value)
			}
		}
		//alert(url+query.join('&'))
		window.open(url+query.join('&'),rep,"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}

-->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
# Buffer page output
#include($root_path."include/care_api_classes/class_order.php");
#$order = new SegOrder('pharma');

ob_start();
?>

<br>

<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform" onSubmit="return validate()">
<div style="margin:5px;font-weight:bold;color:#660000"><?= $sWarning ?></div>
<div style="width:50%">
	<table width="100%" border="0" style="font-size: 12px; margin-top:5px" cellspacing="2" cellpadding="2">	
		<tbody>
			<tr>
				<td align="left" class="jedPanelHeader" ><strong>Report options</strong></td>
			</tr>
			<tr>
				<td nowrap="nowrap" align="right" class="jedPanel">
					<table width="100%" border="0" cellpadding="2" cellspacing="0">
						<tr>
							<td width="20%" align="center" nowrap="nowrap"><strong>Select Report Type</strong>
								<select class="jedInput" name="selreport" id="selreport" onchange="selOnChange()"/>
									<option value="opdtrans">OPD Daily Transactions</option>
									<option value="icdstatage">OPD ICD 10 Code Statistics</option>
								</select>								
							</td>
						</tr>
						<tr>
							<td><hr width="95%" size="1" style="opacity:0.5"/></td>
						</tr>
					</table>
					
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="opdtrans" style="font:bold 12px Arial">
					<tr>
							<td width="30%" align="right">Select Department</td>
							<td>
								<select class="jedInput" name="param" id="opdtrans_dept" paramName="dept" segOption="opdtrans">
									<option value="" style="font-weight:bold">[All]</option>						
<?php
	$sql = "SELECT name_formal,id\n".
		"FROM care_department\n".
		"WHERE admit_outpatient=1\n".
		"ORDER BY name_formal";
	$department1 = $db->Execute($sql);
	while($row=$department1->FetchRow()){
		echo "									<option value=\"".$row['id']."\">".$row['name_formal']."</option>\n";
	}
?>									
								</select>
							</td>
					</tr>
					<tr>
							<td align="right" >From</td>
							<td>
									<input class="jedInput" name="param" id="opdtrans_from" type="text" size="12" value="" paramName="from" segOption="opdtrans"/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_opdtrans_from_date" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "opdtrans_from", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_opdtrans_from_date", singleClick : true, step : 1
										});
									</script>
							</td>
						</tr>
						<tr>
							<td align="right" >To</td>
							<td>
									<input class="jedInput" name="param" id="opdtrans_to" type="text" size="12" value="" paramName="to" segOption="opdtrans"/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_opdtrans_to_date" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "opdtrans_to", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_opdtrans_to_date", singleClick : true, step : 1
										});
									</script>
							</td>
						</tr>
					</table>
					
					
					<table name="selOptions" width="100%" border="0" cellpadding="2" cellspacing="0" segOption="icdstatage" style="font:bold 12px Arial; display:none">					
					<tr>
							<td align="right" >From</td>
							<td>
									<input class="jedInput" name="param" id="icdstatage_from" type="text" size="12" value="" paramName="from" segOption="icdstatage"/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_icdstatage_from_date" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "icdstatage_from", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_icdstatage_from_date", singleClick : true, step : 1
										});
									</script>
							</td>
						</tr>
						<tr>
							<td align="right" >To</td>
							<td>
									<input class="jedInput" name="param" id="icdstatage_to" type="text" size="12" value="" paramName="to" segOption="icdstatage"/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_icdstatage_to_date" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "icdstatage_to", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_icdstatage_to_date", singleClick : true, step : 1
										});
									</script>
							</td>
						</tr>
					</table>
		
					<table width="100%" border="0" cellpadding="2" cellspacing="0" style="font:normal 12px Tahoma;margin-top:5px">
						<tr>
							<td width="30%">&nbsp;</td>
							<td>
								<input type="button" value="View report"  class="jedButton" onclick="openReport()"/>
							</td>
						</tr>
					</table>
					<br />
				</td>
			</tr>
		</tbody>
	</table>
</div>

<?php

# Workaround to force display of results  form
$bShowThisForm = TRUE;

# If smarty object is not available create one
if(!isset($smarty)){
	/**
 * LOAD Smarty
 * param 2 = FALSE = dont initialize
 * param 3 = FALSE = show no copyright
 * param 4 = FALSE = load no javascript code
 */
	include_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common',FALSE,FALSE,FALSE);
	
	# Set a flag to display this page as standalone
	$bShowThisForm=TRUE;
}

?>

<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">  
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">

<input type="hidden" id="delete" name="delete" value="" />
<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump">



</form>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
