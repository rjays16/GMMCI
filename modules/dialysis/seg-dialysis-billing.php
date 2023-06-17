<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/dialysis/ajax/dialysis-transaction.common.php');
//require_once $root_path.'include/care_api_classes/dialysis/class_dialysis_request.php';


/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
0* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','lab.php');

$local_user='ck_dialysis_user';
require_once $root_path.'include/inc_front_chain_lang.php';

# Create products object
$GLOBAL_CONFIG=array();

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

if(!isset($pid)) $pid=0;
if(!isset($encounter_nr)) $encounter_nr=0;

//$phpfd = config date format in PHP date() specification

if (!$_GET['from'])
	$breakfile=$root_path."modules/dialysis/seg-dialysis-menu.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile=$root_path."modules/dialysis/seg-dialysis-menu.php".URL_APPEND;
}

$thisfile='seg-dialysis-billing.php';

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once $root_path."include/care_api_classes/dialysis/class_dialysis.php";
require_once $root_path."include/care_api_classes/class_encounter.php";
$dialysis_obj = new SegDialysis();
$enc_obj = new Encounter($encounter_nr);
global $db;

require_once $root_path.'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# href for the close button
$smarty->assign('breakfile',$breakfile);
$title = "Dialysis :: List of Billed Patients";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);


# Collect javascript code
ob_start();
	 # Load the javascript code
?>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>
<script type="text/javascript" src="<?=$root_path?>modules/dialysis/js/request-main.js"></script>
<script type="text/javascript">
var oldcClick = cClick;
cClick = function() {
	if (OLloaded && OLgateOK) {
		if (over && OLshowingsticky) {
			refreshPage();
		}
	}
	oldcClick();
	return false;
}

function refreshPage() {
	//window.location.reload();
	$('show_requests').list.refresh();
}

function initialize() {
	ListGen.create( $('show_requests'), {
		id: 'requests',
		url: '<?=$root_path?>modules/dialysis/ajax/ajax_billing_list.php',
		params: { 'check':'none'},
		width: 650,
		height: 200,
		autoLoad: true,
		columnModel: [
			{
				name: 'bill_date',
				label: 'Bill Date/Time',
				width: 150,
				sorting: ListGen.SORTING.asc,
				sortable: true,
				styles:{
					font: 'Tahoma',
					fontSize: '11',
					fontWeight: 'bold'
				}
			},
			{
				name: 'patient_name',
				label: 'Patient Name',
				width: 200,
				sortable: false,
				styles:{
					color: '#660000',
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'patient_enc',
				label: 'Case No.',
				width: 100,
				sortable: false,
				styles:{
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'bill_amount',
				label: 'Billed Amount',
				width: 100,
				sortable: false,
				styles:{
					font: 'Tahoma',
					fontSize: '11',
					textAlign: 'right'
				}
			},
			{
				name: 'options',
				label: 'Options',
				width: 90,
				sortable: false
			}
		]
	});
}

function changePatientOptions(val)
{
	switch(val)
	{
		case 'p_name':
			$(val).style.display="";
			$('p_pid').style.display="none";
			$('p_enc').style.display="none";
			break;
		case 'p_pid':
			$(val).style.display="";
			$('p_name').style.display="none";
			$('p_enc').style.display="none";
			break;
		case 'p_enc':
			$(val).style.display="";
			$('p_pid').style.display="none";
			$('p_name').style.display="none";
			break;
	}
	$('name').value="";
	$('pid').value="";
	$('encounter_nr').value="";
}

function changeDateOptions(val)
{
	switch(val)
	{
		case 'specific':
			$(val).style.display="";
			$('between').style.display="none";
			break;
		case 'between':
			$(val).style.display="";
			$('specific').style.display="none";
			break;
		default:
			$('between').style.display="none";
			$('specific').style.display="none";
	}
	$('seldate_spec').value="";
	$('seldate_from').value="";
	$('seldate_to').value="";
}

function startSearch()
{
	var chk1 = document.getElementsByName('patient_check');
	var chk2 = document.getElementsByName('date_check');
	if(chk1[0].checked==true && chk2[0].checked==false)
	{
		$('show_requests').list.params =
		 {
				'check':'patient',
				'pid':$('pid').value,
				'encounter_nr':$('encounter_nr').value,
				'name':$('name').value
		 };
	}
	else if(chk1[0].checked==false && chk2[0].checked==true) {
		$('show_requests').list.params =
		 {
			 'check':'date',
			 'date':$('seldate').value,
			 'date_spec':$('seldate_spec').value,
			 'date_from':$('seldate_from').value,
			 'date_to':$('seldate_to').value
		 };
	}
	else if(chk1[0].checked==true && chk2[0].checked==true)
	{
		$('show_requests').list.params =
		 {
				'check':'both',
				'pid':$('pid').value,
				'encounter_nr':$('encounter_nr').value,
				'name':$('name').value,
				'date':$('seldate').value,
				'date_spec':$('seldate_spec').value,
				'date_from':$('seldate_from').value,
				'date_to':$('seldate_to').value
		 };
	}else {
		 $('show_requests').list.params = { 'check':'none'};
	}

	 $('show_requests').list.refresh();
}

function openRequestTray(encounter_nr,pid)
{
	overlib(
	OLiframeContent('<?=$root_path?>modules/dialysis/seg-dialysis-request-window.php?pid='+pid+'&encounter_nr='+encounter_nr,
			800, 500, 'fGroupTray', 0, 'auto'),
			WIDTH,800, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2, CAPTION,'New Test Request',
			MIDX,0, MIDY,0,
			STATUS,'New Test Request');
	return false;
}

function openDetailsTray(enc_nr, pid, refno)
{
	overlib(
	OLiframeContent('<?=$root_path?>modules/dialysis/seg-dialysis-request-view.php?pid='+pid+'&encounter_nr='+enc_nr+'&refno='+refno,
			650, 450, 'fGroupTray', 0, 'auto'),
			WIDTH,650, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2, CAPTION,'Edit Request',
			MIDX,0, MIDY,0,
			STATUS,'Edit Request');
	return false;
}

function deleteItem(bill_nr)
{
	xajax_deleteBill(bill_nr);
}

document.observe('dom:loaded', initialize);
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
$curDate = date($dbtime_format);
$curDate_show = date($fulltime_format);

$smarty->assign('patientCheck', '<input type="checkbox" id="patient_check" name="patient_check">');
$smarty->assign('dateCheck', '<input type="checkbox" id="date_check" name="date_check">');

$options = '<option value="p_name">Patient name</option>'.
					 '<option value="p_pid">Patient ID</option>'.
					 '<option value="p_enc">Patient Case#</option>';
$smarty->assign('patientOptions', '<select class="segInput" id="selpatient" name="selpatient" onchange="changePatientOptions(this.value)">'.$options.'</select>');
$smarty->assign('pSearchName', '<input type="text" size="20" id="name" class="segInput"/>');
$smarty->assign('pSearchId', '<input type="text" size="20" id="pid" class="segInput"/>');
$smarty->assign('pSearchEnc', '<input type="text" size="20" id="encounter_nr" class="segInput"/>');

$options = '<option value="today">Today</option>'.
					 '<option value="week">This week</option>'.
					 '<option value="month">This month</option>'.
					 '<option value="specific">Specific date</option>'.
					 '<option value="between">Between</option>';
$smarty->assign('dateOptions', '<select class="segInput" id="seldate" name="seldate" onchange="changeDateOptions(this.value)">'.$options.'</select>');

$smarty->assign('specificDate', '<input class="segInput" name="seldate_spec" id="seldate_spec" type="text" size="12" value=""/>
												<img src="'.$root_path.'gui/img/common/default/calendar_add.png" id="tg_seldatespec" align="absmiddle" style="cursor:pointer;"  />');
$smarty->assign('specificDate_js', '<script type="text/javascript">
														Calendar.setup ({
																inputField : "seldate_spec", ifFormat : "'.$phpfd.'", showsTime : false, button : "tg_seldatespec", singleClick : true, step : 1
														});
												</script>');

$smarty->assign('seldate_from', '<input class="segInput" name="seldate_from" id="seldate_from" type="text" size="12" value=""/>
												<img src="'.$root_path.'gui/img/common/default/calendar_add.png" id="tg_seldatefrom" align="absmiddle" style="cursor:pointer;"  />');
$smarty->assign('seldatefrom_js', '<script type="text/javascript">
														Calendar.setup ({
																inputField : "seldate_from", ifFormat : "'.$phpfd.'", showsTime : false, button : "tg_seldatefrom", singleClick : true, step : 1
														});
												</script>');

$smarty->assign('seldate_to', '<input class="segInput" name="seldate_to" id="seldate_to" type="text" size="12" value=""/>
												<img src="'.$root_path.'gui/img/common/default/calendar_add.png" id="tg_seldateto" align="absmiddle" style="cursor:pointer;"  />');
$smarty->assign('seldateto_js','<script type="text/javascript">
														Calendar.setup ({
																inputField : "seldate_to", ifFormat : "'.$phpfd.'", showsTime : false, button : "tg_seldateto", singleClick : true, step : 1
														});
												</script>');

$smarty->assign('form_start','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform">');
$smarty->assign('form_end','</form>');

ob_start();
$sTemp='';

?>
<input type="hidden" name="submitted" value="1" />
<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">
<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<input type="hidden" name="lockflag" value= "<?php echo  $lockflag?>">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
if (!$viewonly) {
	$smarty->assign('sContinueButton','<input type="image" class="segSimulatedLink" src="'.$root_path.'images/btn_submitorder.gif" align="absmiddle" alt="Submit">');
	$smarty->assign('sBreakButton','<img class="segSimulatedLink" src="'.$root_path.'images/btn_cancelorder.gif" alt="'.$LDBack2Menu.'" align="absmiddle" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;">');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','dialysis/request_billing.tpl');
$smarty->display('common/mainframe.tpl');


?>
