<?
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('NO_2LEVEL_CHK',1);
$local_user='ck_pflege_user';

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
require_once($root_path.'modules/dialysis/ajax/dialysis-transaction.common.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');

require_once($root_path.'include/care_api_classes/class_request_source.php');
$req_src_obj = new SegRequestSource();
$request_source = $req_src_obj->getSourceDialysis();

$smarty = new Smarty_Care('common');
$smarty->assign('sToolbarTitle',"Dialysis :: Test Request");
$smarty->assign('sWindowTitle',"Dialysis :: Test Request");

$breakfile = 'javascript:window.parent.cClick();';
$smarty->assign('breakfile', $breakfile);
ob_start();

#added by VAN 08-24-2010
$ptype = 'rdu';
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>


<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript" src="js/request-window.js"></script>
<script type="text/javascript">
var $J = jQuery.noConflict();

/*var oldcClick = cClick;
cClick = function() {
	if (OLloaded && OLgateOK) {
		if (over && OLshowingsticky) {
			refreshPage();
		}
	}
	oldcClick();
}*/

function viewRequestPrintout()
{
	var enc_nr = $('encounter_nr').value;
	window.open('<?=$root_path?>modules/dialysis/seg-dialysis-request-printout.php?encounter_nr='+enc_nr,null,'menubar=no,directories=no,height=600,width=800,resizable=yes');
}

function openSpLabRequest()
{
	return overlib(
		OLiframeContent('<?=$root_path?>modules/special_lab/seg-splab-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=clinic&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&user_origin=splab&ptype=<?=$ptype?>&ischecklist=1',
			800, 440, 'fGroupTray', 0, 'auto'),
		WIDTH,750, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="refreshPage();">',
		CAPTIONPADDING,2, CAPTION,'Special Laboratory Request',
		MIDX,0, MIDY,0,
		STATUS,'Special Laboratory Request');
}
function openLabRequest() {
	return overlib(
		OLiframeContent('<?=$root_path?>modules/laboratory/seg-lab-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=clinic&area_type=<?=$area_type?>&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&user_origin=lab&ptype=<?=$ptype?>&ischecklist=1',
			800, 440, 'fGroupTray', 0, 'auto'),
		WIDTH,750, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="refreshPage();">',
		CAPTIONPADDING,2, CAPTION,'Laboratory Request',
		MIDX,0, MIDY,0,
		STATUS,'Laboratory Request');
}



function openLabResults() {
	return overlib(
		OLiframeContent('<?=$root_path?>modules/laboratory/seg-lab-request-result-patient-list.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_doctor=1&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&ptype=<?=$ptype?>',
			650, 400, 'fGroupTray', 0, 'auto'),
		WIDTH,650, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0>',
		CAPTIONPADDING,2, CAPTION,'Laboratory Results',
		MIDX,0, MIDY,0,
		STATUS,'Laboratory Results');
}

function openBloodRequest() {
	return overlib(
		OLiframeContent('<?=$root_path?>modules/bloodBank/seg-blood-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=clinic&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&user_origin=blood&ptype=<?=$ptype?>&ischecklist=1',
			800, 440, 'fGroupTray', 0, 'auto'),
		WIDTH,750, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="refreshPage();">',
		CAPTIONPADDING,2, CAPTION,'Blood Bank Request',
		MIDX,0, MIDY,0,
		STATUS,'Blood Bank Request');
}

function openBloodResults() {
	return overlib(
		OLiframeContent('<?=$root_path?>modules/laboratory/seg-lab-request-result-patient-list.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_doctor=1&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&ptype=<?=$ptype?>',
			650, 400, 'fGroupTray', 0, 'auto'),
		WIDTH,650, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0>',
		CAPTIONPADDING,2, CAPTION,'Blood Bank Results',
		MIDX,0, MIDY,0,
		STATUS,'Blood Bank Results');
}

function openRadioRequest() {
	return overlib(
		OLiframeContent('<?=$root_path?>modules/radiology/seg-radio-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=clinic&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&ptype=<?=$ptype?>&ischecklist=1',
			800, 440, 'fGroupTray', 0, 'auto'),
		WIDTH,750, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="refreshPage();">',
		CAPTIONPADDING,2, CAPTION,'Radiology Request',
		MIDX,0, MIDY,0,
		STATUS,'Radiology Request');
}

function openRadioResults() {
	return overlib(
		OLiframeContent('<?=$root_path?>modules/radiology/radiology_patient_request.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_doctor=1&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&ptype=<?=$ptype?>',
			750, 400, 'fGroupTray', 1, 'auto'),
		WIDTH,750, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0>',
		CAPTIONPADDING,4, CAPTION,'Radiology Results',
		MIDX,0, MIDY,0,
		STATUS,'Radiology Results');
}

function openPharmaRequest(area) {

	return overlib(
	OLiframeContent('<?=$root_path?>modules/pharmacy/seg-pharma-order.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&from=CLOSE_WINDOW&area='+area+'&pid=<?=$pid?>&encounterset=<?=$encounter_nr?>&is_dr=<?=$is_dr?>&billing=1&request_source=<?=$request_source?>',
		800, 440, 'fGroupTray', 0, 'auto'),
	WIDTH,750, TEXTPADDING,0, BORDER,0,
	STICKY, SCROLL, CLOSECLICK, MODAL,
	CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="refreshPage();">',
	CAPTIONPADDING,2, CAPTION,'Pharmacy Request',
	MIDX,0, MIDY,0,
	STATUS,'Pharmacy Request');

}

function openMiscellaneousRequest() {
	return overlib(
	OLiframeContent('<?=$root_path?>modules/dialysis/seg-misc-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&from=CLOSE_WINDOW&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&mode=new&area=dialysis',
		700, 440, 'fGroupTray', 0, 'auto'),
	WIDTH,700, TEXTPADDING,0, BORDER,0,
	STICKY, SCROLL, CLOSECLICK, MODAL,
	CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="refreshPage();">',
	CAPTIONPADDING,2, CAPTION,'Miscellaneous Request',
	MIDX,0, MIDY,0,
	STATUS,'Miscellaneous Request');
}

function initialize() {
	initializeTab(selected);
	var enc_nr = $('encounter_nr').value;
	var pid = $('pid').value;
	xajax_computeTotalPayment(pid, enc_nr);
}

function refreshPage() {
	window.location.reload();
}

function requestByDate()
{
	var seltabs = $J('#tabs').tabs();
	var selected = seltabs.tabs('option', 'selected')
	initializeTab(selected);
}

function initializeTab(id)
{
	var seldate = $('seldate').value;
	switch(id)
	{
		case 0: xajax_populateLabRequests($('encounter_nr').value, $('pid').value, seldate); break;
		case 1: xajax_populateBloodRequests($('encounter_nr').value, $('pid').value, seldate); break;
		case 2: xajax_populateSpLabRequests($('encounter_nr').value, $('pid').value, seldate); break;
		case 3: xajax_populateRadioRequests($('encounter_nr').value, $('pid').value, seldate); break;
		case 4: xajax_populateIpRequests($('encounter_nr').value, seldate); break;
		case 5: xajax_populateMgRequests($('encounter_nr').value, seldate); break;
		case 6:xajax_populateMiscRequests($('encounter_nr').value, seldate); break;
	}

	var enc_nr = $('encounter_nr').value;
	var pid = $('pid').value;
	xajax_computeTotalPayment(pid, enc_nr);
}

$J(function() {
		$J("#tabs").tabs({
			selected:0,
			select: function(event, ui) {
				var selected = ui.index;
				//alert(ui.panel.empty());
				initializeTab(selected);
			}
		});
	});

document.observe('dom:loaded', function(){
	initializeTab(0);
});
</script>
<?
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$smarty->assign('form_start', '<form name="main_or_form" method="POST" action="'.$_SERVER['PHP_SELF'].'">');
$smarty->assign('form_end', '</form>');

$pid = isset($_POST['pid']) ? $_POST['pid'] : $_GET['pid'];
$seg_person = new Person($pid);
$person_info = $seg_person->getAllInfoArray();
$middle_initial = (strnatcasecmp($person_info['name_middle'][0], $person_info['name_middle'][1]) == 0) ? ucwords(substr($person_info['name_middle'], 0, 2)) : strtoupper($person_info['name_middle'][0]);
$person_name = ucwords($person_info['name_last']) . ', ' . ucwords($person_info['name_first']) . ' ' . $middle_initial;

$person_address = implode(", ",array_filter(array($person_info['street_name'], $person_info["brgy_name"], $person_info["mun_name"])));
if ($person_info["zipcode"])
	$person_address.=" ".$person_info["zipcode"];
if ($person_info["prov_name"])
	$person_address.=" ".$person_info["prov_name"];

$smarty->assign('sPatientID','<input id="pid" name="pid" class="clear" type="text" value="'.$pid.'" readonly="readonly" style="color:#006600; font:bold 16px Arial;"/>');
$smarty->assign('patient_name', $person_name);

$encounter_types = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)', "5"=>'DIALYSIS');
$encounter_nr = isset($_POST['encounter_nr']) ? $_POST['encounter_nr'] : $_GET['encounter_nr'];
$seg_encounter = new Encounter();
$encounter_details = $seg_encounter->getEncounterInfo($encounter_nr);
$encounter_type = $encounter_types[$encounter_details['encounter_type']];
$smarty->assign('encounter_type', $encounter_type);
$smarty->assign('encounter_nr', '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'" />');

$service_type_code = array (49,50,51,52,53,54,"");
$service_type_name = array ("Physical Medicine & Rehab", "Dental", "Orthopedics", "ENT-HNS", "Pediatrics", "Special Lab", "Other");
$service_type_options = "<option value='0'> -Select service type- </option";
for($i=0;$i<count($service_type_code);$i++)
{
	$service_type_options.="<option value='".$service_type_code[$i]."'>".$service_type_name[$i]."</option>";
}
$smarty->assign('miscServiceTypes', $service_type_options);

$active_tab = 'laboratory';
$smarty->assign('bTab'.ucfirst($active_tab),TRUE);
$smarty->assign('dateToday', date('F d, Y'));
$smarty->assign('dateTodayValue', date('Y-m-d'));

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
$smarty->assign('sMainBlockIncludeFile','dialysis/request_window.tpl'); //Assign the or_main template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame

?>

