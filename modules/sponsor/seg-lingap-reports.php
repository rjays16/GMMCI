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
	$breakfile=$root_path."modules/sponsor/seg-sponsor-functions.php".URL_APPEND."&userck=$userck";
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/cashier/cashier-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$imgpath=$root_path."pharma/img/";
$thisfile='seg-lingap-reports.php';


// check for valid permissions
require_once $root_path.'include/care_api_classes/class_user.php';
$user = SegUser::getCurrentUser();

$permissionSet = array('_a_1_lingapreports');
$allow = $user->hasPermission($permissionSet);
if (!$allow)
{
	header('Location:'.$root_path.'main/login.php?'.
		'forward='.urlencode('modules/sponsor/'.$thisfile).
		'&break='.urlencode('modules/sponsor/seg-sponsor-functions.php'));
	exit;
}

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Lingap :: Reports");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Lingap :: Reports");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad="optTransfer.init(document.forms[0])"');

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
<script type="text/javascript" src="<?=$root_path?>js/OptionTransfer.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script language="javascript" type="text/javascript">
<!--
var URL_FORWARD = "<?= URL_APPEND."&clear_ck_sid=$clear_ck_sid" ?>";
var optTransfer = new OptionTransfer("lingap_summary_account_left","lingap_summary_account_right");
optTransfer.setAutoSort(true);
optTransfer.setDelimiter(",");
optTransfer.setStaticOptionRegex("");
optTransfer.saveNewRightOptions("lingap_summary_account");

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
	var url = 'seg-lingap-report-'+rep+'.php?'
	var query = new Array()
	var params = document.getElementsByName('param')
	for (var i=0; i<params.length; i++) {
		if (params[i].getAttribute("segOption") == rep) {
			var mit;
			if (params[i].type=='checkbox') mit=params[i].checked;
			else if (params[i].type=='radio') mit=params[i].checked;
			else mit=params[i].value;
			if (mit)
				query.push(
					encodeURIComponent(params[i].getAttribute('paramName'))
						+'='+encodeURIComponent(params[i].value) )
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

<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform" onSubmit="return validate()">
<div style="width:500px; margin-top:20px">
    <div style="width:100%">
		<strong>Select Report Type</strong>
		<select class="segInput" name="selreport" id="selreport" onchange="selOnChange()"/>
            <option value="lingap_grant">Lingap Granted to Patient</option>
			<option value="lingap_summary">Lingap Summary Report</option>
			<!--<option value="lingap_transaction">Lingap Patient Transaction History Report</option>-->
		</select>
	</div>

	<table width="100%" border="0" style="font-size: 12px; margin-top:5px" cellspacing="2" cellpadding="2">
		<tbody>
			<tr>
				<td align="left" class="segPanelHeader"><strong>Report options</strong></td>
			</tr>
			<tr>
				<td class="segPanel" style="padding:4px" align="center">
                    
                    <!------------------------------- Lingap grant report ------------------------------------->
                    <table name="selOptions" width="95%" border="0" cellpadding="2" cellspacing="0" segOption="lingap_grant" style="font-family:Arial;">
                        <tr>
                            <td align="left" width="18%" >
                                <label>Emergency Purchase:</label>
                            </td>
                            <td>
                                <select class="segInput" name="param" paramName="ep" segOption="lingap_grant">
                                    <option value=""></option>
                                    <option value="y">Yes</option>
                                    <option value="n">No</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td align="left" width="18%" >
                                <label>Date start:</label>
                            </td>
                            <td>
                                    <input class="segInput" name="param" id="lingap_grant_datefrom" type="text" size="12" value="" paramName="datefrom" segOption="lingap_grant"/>
                                    <img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_lingap_grant_datefrom" align="absmiddle" style="cursor:pointer;"  />
                                    <script type="text/javascript">
                                        Calendar.setup ({
                                            inputField : "lingap_grant_datefrom", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_lingap_grant_datefrom", singleClick : true, step : 1
                                        });
                                    </script>
                            </td>
                        </tr>
                        <tr>
                            <td align="left" >
                                <label>Date end:</label>
                            </td>
                            <td>
                                    <input class="segInput" name="param" id="lingap_grant_dateto" type="text" size="12" value="" paramName="dateto" segOption="lingap_grant"/>
                                    <img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_lingap_grant_dateto" align="absmiddle" style="cursor:pointer;"  />
                                    <script type="text/javascript">
                                        Calendar.setup ({
                                            inputField : "lingap_grant_dateto", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_lingap_grant_dateto", singleClick : true, step : 1
                                        });
                                    </script>
                            </td>
                        </tr>
                    </table>
                    <!------------------------------- End Lingap grant report ------------------------------------->
                
                
                    <!------------------------------- Lingap summary report ------------------------------------->
                    <table name="selOptions" width="95%" border="0" cellpadding="2" cellspacing="0" segOption="lingap_summary" style="font-family:Arial; display:none">
						<tr>
							<td align="left" width="18%" >
								<label>Report accounts:</label>
							</td>
							<td colspan="3">
								<table wdth="100%" cellpadding="0" cellspacing="0" border="0" style="font-family:Arial">
									<tr>
										<td align="center">
											<label>Select accounts</label>
											<select id="lingap_summary_account_left" name="lingap_summary_account_left" class="segInput" size="5" multiple="multiple" style="width:170px">
<?php

$result = $db->Execute("SELECT type_id `id`,name_long `desc` FROM seg_cashier_account_types WHERE lockflag=0 ORDER BY name_long");
if ($result) {
	while ($row=$result->FetchRow()) {
?>
												<option value="<?= $row["id"] ?>"><?= $row['desc'] ?></option>
<?php
	}
}
?>
											</select>
										</td>
										<td style="padding:0px 2px">
											<input type="button" class="segButton" value=">" style="font:bold 11px Arial;padding:0px 1px" onclick="optTransfer.transferRight()" /><br />
											<input type="button" class="segButton" value="<" style="font:bold 11px Arial;padding:0px 1px" onclick="optTransfer.transferLeft()" />
										</td>
										<td align="center">
											<label>Show these accounts</label>
											<select id="lingap_summary_account_right" name="lingap_summary_account_right" class="segInput" size="5" multiple="multiple" style="width:170px"></select>
											<br />
											<input id="lingap_summary_account" name="param" type="hidden" value="" paramName="account" segOption="lingap_summary">
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td align="left" width="18%" >
								<label>Date from:</label>
							</td>
							<td>
									<input class="segInput" name="param" id="lingap_summary_datefrom" type="text" size="12" value="" paramName="datefrom" segOption="lingap_summary"/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_lingap_summary_datefrom" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "lingap_summary_datefrom", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_lingap_summary_datefrom", singleClick : true, step : 1
										});
									</script>
							</td>
						</tr>
						<tr>
							<td align="left" >
								<label>Date to:</label>
							</td>
							<td>
									<input class="segInput" name="param" id="lingap_summary_dateto" type="text" size="12" value="" paramName="dateto" segOption="lingap_summary"/>
									<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_lingap_summary_dateto" align="absmiddle" style="cursor:pointer;"  />
									<script type="text/javascript">
										Calendar.setup ({
											inputField : "lingap_summary_dateto", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_lingap_summary_dateto", singleClick : true, step : 1
										});
									</script>
							</td>
						</tr>
					</table>
<!------------------------------- End Lingap summary report ------------------------------------->



<!------------------------------- Lingap Patient Transaction History Report ------------------------------------->
					<table name="selOptions" width="95%" border="0" cellpadding="2" cellspacing="0" segOption="lingap_transaction" style="font-family:Arial; display:none">
						<tr>
							<td width="18%" align="left" >
								<label>Select patient:</label>
							</td>
							<td>
								<input class="segInput" name="param" id="lingap_transaction_pname" type="text" size="40" value="" paramName="pname" readonly="readonly" segOption="lingap_transaction"/>
								<input class="segInput" name="param" id="lingap_transaction_pid" type="hidden" value="" paramName="pid" segOption="lingap_transaction"/>
								<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer;" onclick="selectPatient()" onmouseout="nd();" align="absmiddle" />
								<script type="text/javascript">
									function selectPatient() {
											return overlib(
												OLiframeContent('<?= $root_path ?>modules/registration_admission/seg-select-enc.php?var_pid=lingap_transaction_pid&var_name=lingap_transaction_pname',
												700, 400, 'fSelEnc', 0, 'no'),
												WIDTH,700, TEXTPADDING,0, BORDER,0,
												STICKY, SCROLL, CLOSECLICK, MODAL,
												CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
												CAPTIONPADDING,2,
												CAPTION,'Select registered person',
												MIDX,0, MIDY,0,
												STATUS,'Select registered person');
									}
								</script>
							</td>
						</tr>
						<tr>
							<td align="left" >
								<label>Date from:</label>
							</td>
							<td>
								<input class="segInput" name="param" id="lingap_transaction_datefrom" type="text" size="12" value="" paramName="datefrom" segOption="lingap_transaction"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_lingap_transaction_datefrom" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "lingap_transaction_datefrom", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_lingap_transaction_datefrom", singleClick : true, step : 1
									});
								</script>
							</td>
						</tr>
						<tr>
							<td align="left" >
								<label>Date to:</label>
							</td>
							<td>
								<input class="segInput" name="param" id="lingap_transaction_dateto" type="text" size="12" value="" paramName="dateto" segOption="lingap_transaction"/>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="tg_lingap_transaction_dateto" align="absmiddle" style="cursor:pointer;"  />
								<script type="text/javascript">
									Calendar.setup ({
										inputField : "lingap_transaction_dateto", ifFormat : "<?= $phpfd ?>", showsTime : false, button : "tg_lingap_transaction_dateto", singleClick : true, step : 1
									});
								</script>
							</td>
						</tr>
					</table>
<!------------------------------- End Lingap Patient Transaction History Report ------------------------------------->
					<table width="100%" border="0" cellpadding="2" cellspacing="0" style="font:normal 12px Tahoma;margin-top:5px">
						<tr>
							<td width="20%"></td>
							<td>
								<button class="segButton" onclick="openReport()" value="submit">
									<img src="../../gui/img/common/default/report_magnify.png" />
									View report
								</button>
							</td>
						</tr>
					</table>
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
