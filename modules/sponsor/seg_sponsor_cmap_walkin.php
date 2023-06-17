<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/sponsor/ajax/cmap_walkin.common.php');


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

$title="Sponsor grants";
if (!$_GET['from'])
	$breakfile=$root_path."modules/sponsor/seg-sponsor-functions.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile = $root_path.'modules/cashier/seg-cashier-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}
$thisfile='seg_sponsor_cmap_walkin.php';

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
global $db;

require_once $root_path.'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# href for the close button
$smarty->assign('breakfile',$breakfile);
$title = "CMAP :: Walkin Manager";

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
	$('show_walkin').list.refresh();
}

function initialize() {
	ListGen.create( $('show_walkin'), {
		id: 'cmap-walkin',
		url: '<?=$root_path?>modules/sponsor/ajax/ajax_walkin_list.php',
		params: { 'check':'none'},
		width: 'auto',
		height: 'auto',
		autoLoad: true,
		effects: true,
		rowHeight: 30,
		maxRows: 5,
		layout: [
			['#pagestat', '#first', '#prev', '#next', '#last', '#refresh'],
			['#thead'],
			['#tbody'],
			['#tfoot']
		],
		columnModel: [
			{
				name: 'walkin_name',
				label: 'Walkin Name',
				width: 200,
				sorting: ListGen.SORTING.none,
				sortable: true,
				styles:{
					font: 'Tahoma',
					fontSize: '11',
					fontWeight: 'bold'
				}
			},
			{
				name: 'walkin_address',
				label: 'Address',
				width: 200,
				sortable: false,
				styles:{
					color: '#660000',
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'walkin_gender',
				label: 'Sex',
				width: 60,
				sortable: false,
				styles:{
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'walkin_createdt',
				label: 'Date Registered',
				width: 150,
				sorting: ListGen.SORTING.desc,
				sortable: true,
				styles:{
					font: 'Tahoma',
					fontSize: '11',
					textAlign: 'center'
				}
			},
			{
				name: 'options',
				label: 'Options',
				width: 150,
				sortable: false
			}
		]
	});
}

function changeDateOptions(val)
{
	switch(val)
	{
		case 'specific':
			$('d_specific').style.display="";
			$('d_between1').style.display="none";
			$('d_between2').style.display="none";
			break;
		case 'between':
			$('d_specific').style.display="none";
			$('d_between1').style.display="";
			$('d_between2').style.display="";
			break;
			case 'today': case 'week': case 'month':
				$('d_specific').style.display="none";
				$('d_between1').style.display="none";
				$('d_between2').style.display="none";
			break;
	}

}

function startSearch()
{
	var chk1 = document.getElementsByName('patient_check');
	var chk3 = document.getElementsByName('date_check');
	if(chk1[0].checked==true && chk3[0].checked==false)
	{
		$('show_walkin').list.params =
		 {
				'check':'patient',
				'name':$('name').value
		 };
	}
	else if(chk1[0].checked==false && chk3[0].checked==true) {
		$('show_walkin').list.params =
		 {
			 'check':'date',
			 'date_type':$('seldate').value,
			 'date_specific':$('date_specific').value,
			 'date_between1':$('date_between1').value,
			 'date_between2':$('date_between2').value
		 };
	}
	else if(chk1[0].checked==true && chk3[0].checked==true)
	{
		$('show_walkin').list.params =
		 {
				'check':'both',
				'name':$('name').value,
				'date_type':$('seldate').value,
				'date_specific':$('date_specific').value,
				'date_between1':$('date_between1').value,
				'date_between2':$('date_between2').value
		 };
	}else {
		 $('show_walkin').list.params = { 'check':'none'};
	}
	 $('show_walkin').list.refresh();
}

function updateWalkinDetails(id) {
	overlib(
		OLiframeContent('<?= $root_path ?>modules/sponsor/seg_sponsor_cmap_walkin_register.php?id='+id+'&from=CLOSE_WINDOW',
			500, 300, 'fWizard', 0, 'no'),
			WIDTH,500, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2,
			CAPTION,'CMAP - Update Walkin details',
			MIDX,0, MIDY,0,
			STATUS,'CMAP - Update Walkin details');
	return false;
}

function deleteWalkin(id)
{
	var answer = confirm("Delete this walkin?")
	if(answer) {
		//xajax_deleteWalkin(id);
	}
	return false;
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

$options = '<option value="p_name">Walkin name</option>';
$smarty->assign('patientOptions', '<select class="segInput" id="selpatient" name="selpatient">'.$options.'</select>');
$smarty->assign('pSearchName', '<input type="text" style="width:50%;" id="name" class="segInput"/>');

$options = '<option value="today">Today</option>'.
					 '<option value="week">This Week</option>'.
					 '<option value="month">This Month</option>'.
					 '<option value="specific">Specific</option>'.
					 '<option value="between">Between</option>';
$smarty->assign('dateOptions', '<select class="segInput" id="seldate" name="seldate" onchange="changeDateOptions(this.value)">'.$options.'</select>');
$smarty->assign('dateSpecific', '<input type="text" style="width:20%;" id="date_specific" class="segInput"/>');
$smarty->assign('dateSpecificIcon', '<img src="'.$root_path.'gui/img/common/default/calendar_add.png" height="20px" id="tg_specificdate" align="absmiddle" style="cursor:pointer"  />');
$smarty->assign('dateSpecificJs', '<script type="text/javascript">
									Calendar.setup ({
										inputField : "date_specific", ifFormat : "'.$phpfd.'", showsTime : false, button : "tg_specificdate", singleClick : true, step : 1
									});
								</script>');
$smarty->assign('dateBetween1', '<input type="text" style="width:20%;" id="date_between1" class="segInput"/>');
$smarty->assign('dateBetween1Icon', '<img src="'.$root_path.'gui/img/common/default/calendar_add.png" height="20px" id="tg_between1" align="absmiddle" style="cursor:pointer"  />');
$smarty->assign('dateBetween1Js', '<script type="text/javascript">
									Calendar.setup ({
										inputField : "date_between1", ifFormat : "'.$phpfd.'", showsTime : false, button : "tg_between1", singleClick : true, step : 1
									});
								</script>');
$smarty->assign('dateBetween2', '<input type="text" style="width:20%;" id="date_between2" class="segInput"/>');
$smarty->assign('dateBetween2Icon', '<img src="'.$root_path.'gui/img/common/default/calendar_add.png" height="20px" id="tg_between2" align="absmiddle" style="cursor:pointer"  />');
$smarty->assign('dateBetween2Js', '<script type="text/javascript">
									Calendar.setup ({
										inputField : "date_between2", ifFormat : "'.$phpfd.'", showsTime : false, button : "tg_between2", singleClick : true, step : 1
									});
								</script>');

$options = '<option value="undone">Undone</option>'.
					 '<option value="done">Done</option>';
$smarty->assign('statusOptions', '<select class="segInput" id="selstatus" name="selstatus">'.$options.'</select>');

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
$smarty->assign('sMainBlockIncludeFile','sponsor/cmap_walkin.tpl');
$smarty->display('common/mainframe.tpl');


?>
