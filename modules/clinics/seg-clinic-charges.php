<?
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('NO_2LEVEL_CHK',1);
$local_user='ck_pflege_user';

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
require_once($root_path.'modules/clinics/ajax/clinic-requests.common.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');

$dr_nr = $_GET['dr_nr'];
$dept_nr = $_GET['dept_nr'];
$is_dr = $_GET['is_dr'];
$area_type = $_GET['area_type'];
$ptype = $_GET['ptype'];
$doc_nr = $_GET['doc_nr'];
$or_no = $_GET['or_no'];

require_once($root_path.'include/care_api_classes/class_request_source.php');
$req_src_obj = new SegRequestSource();
if($ptype=='ipd') {
	$request_source = $req_src_obj->getSourceIPDClinics();
} else if($ptype=='er') {
	$request_source = $req_src_obj->getSourceERClinics();
} else if($ptype=='opd') {
	$request_source = $req_src_obj->getSourceOPDClinics();
} else if($ptype=='phs') {
	$request_source = $req_src_obj->getSourcePHSClinics();
} else if($ptype=='nursing') {
	$request_source = $req_src_obj->getSourceNursingWard();
} else if(($ptype=='ic') || ($ptype=='iclab')) {
	$request_source = $req_src_obj->getSourceIndustrialClinic();
} else if($ptype=='bb') {
	$request_source = $req_src_obj->getSourceBloodBank();
} else if($ptype=='spl') {
	$request_source = $req_src_obj->getSourceSpecialLab();
} else if($ptype=='or') {
	$request_source = $req_src_obj->getSourceOR();
} else if($ptype=='rdu') {
	$request_source = $req_src_obj->getSourceDialysis();
} else if($ptype=='doctor') {
	$request_source = $req_src_obj->getSourceDoctor();
} else if($ptype=='rd') {
	$request_source = $req_src_obj->getSourceDialysis();
} else if($ptype=='ip') {
	$request_source = $req_src_obj->getSourceInpatientPharmacy();
} else if($ptype=='mg') {
	$request_source = $req_src_obj->getSourceMurangGamot();
} else{
	$request_source = $req_src_obj->getSourceLaboratory();
}
global $db;

$smarty = new Smarty_Care('common');
$smarty->assign('sToolbarTitle',"Dialysis :: Test Request");
$smarty->assign('sWindowTitle',"Dialysis :: Test Request");

$breakfile = 'javascript:window.parent.cClick();';
$smarty->assign('breakfile', $breakfile);
$smarty->assign('ptype', '<input type="hidden" id="ptype" name="ptype" value="'.$ptype.'">');
$smarty->assign('request_source', '<input type="hidden" id="request_source" name="request_source" value="'.$request_source.'">');
ob_start();
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

<script type="text/javascript" src="js/clinic-request-tray.js"></script>
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

//Added by Jarel 11/10/2013 for autotagging
var ptype = '<?=$ptype?>';
var doc_nr = '<?=$doc_nr?>';
var or_no = '<?=$or_no?>';

function viewRequestPrintout()
{
	var enc_nr = $('encounter_nr').value;
	window.open('seg-clinic-request-printout.php?encounter_nr='+enc_nr,null,'menubar=no,directories=no,height=600,width=800,resizable=yes');
}

function openSpLabRequest()
{
	if(ptype=='doctor') autoTagging();	
	
	return overlib(
		OLiframeContent('<?=$root_path?>modules/special_lab/seg-splab-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=clinic&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dr_nr=<?=$dr_nr?>&dept_nr=<?=$dept_nr?>&user_origin=splab&ischecklist=1&ptype=<?=$ptype?>',
			800, 370, 'fGroupTray', 0, 'auto'),
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
		CAPTIONPADDING,2, CAPTION,'Special Laboratory Request',
		MIDX,0, MIDY,0,
		STATUS,'Special Laboratory Request');
}

function openICLabRequest() 
{
	if(ptype=='doctor') autoTagging();	
	
	return overlib(
		OLiframeContent('<?=$root_path?>modules/ic_lab/seg-iclab-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=clinic&area_type=<?=$area_type?>&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dr_nr=<?=$dr_nr?>&dept_nr=<?=$dept_nr?>&user_origin=iclab&ischecklist=1&ptype=<?=$ptype?>',
			800, 370, 'fGroupTray', 0, 'auto'),
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
		CAPTIONPADDING,2, CAPTION,'IC Laboratory Request',
		MIDX,0, MIDY,0,
		STATUS,'IC Laboratory Request');
}

function openLabRequest() 
{
	if(ptype=='doctor') autoTagging();	

	return overlib(
		OLiframeContent('<?=$root_path?>modules/laboratory/seg-lab-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=clinic&area_type=<?=$area_type?>&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dr_nr=<?=$dr_nr?>&dept_nr=<?=$dept_nr?>&user_origin=lab&ischecklist=1&ptype=<?=$ptype?>',
			800, 370, 'fGroupTray', 0, 'auto'),
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
		CAPTIONPADDING,2, CAPTION,'Laboratory Request',
		MIDX,0, MIDY,0,
		STATUS,'Laboratory Request');
}

function openLabResults() {
	return overlib(
		OLiframeContent('<?=$root_path?>modules/laboratory/seg-lab-request-result-patient-list.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_doctor=1&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>',
			800, 370, 'fGroupTray', 0, 'auto'),
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0>',
		CAPTIONPADDING,2, CAPTION,'Laboratory Results',
		MIDX,0, MIDY,0,
		STATUS,'Laboratory Results');
}

function openBloodRequest() {
	if(ptype=='doctor') autoTagging();	
	return overlib(
		OLiframeContent('<?=$root_path?>modules/bloodBank/seg-blood-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=clinic&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dr_nr=<?=$dr_nr?>&dept_nr=<?=$dept_nr?>&ptype=<?=$ptype?>&user_origin=blood&ischecklist=1',
			800, 370, 'fGroupTray', 0, 'auto'),
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
		CAPTIONPADDING,2, CAPTION,'Blood Bank Request',
		MIDX,0, MIDY,0,
		STATUS,'Blood Bank Request');
}

function openBloodResults() {
	return overlib(
		OLiframeContent('<?=$root_path?>modules/laboratory/seg-lab-request-result-patient-list.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_doctor=1&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>',
			800, 370, 'fGroupTray', 0, 'auto'),
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0>',
		CAPTIONPADDING,2, CAPTION,'Blood Bank Results',
		MIDX,0, MIDY,0,
		STATUS,'Blood Bank Results');
}

function openRadioRequest() {
	if(ptype=='doctor') autoTagging();	
	return overlib(
		OLiframeContent('<?=$root_path?>modules/radiology/seg-radio-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=clinic&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dr_nr=<?=$dr_nr?>&dept_nr=<?=$dept_nr?>&ischecklist=1&is_dr=<?=$is_dr?>&ptype=<?=$ptype?>',
			800, 370, 'fGroupTray', 0, 'auto'),
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
		CAPTIONPADDING,2, CAPTION,'Radiology Request',
		MIDX,0, MIDY,0,
		STATUS,'Radiology Request');
}

function openRadioResults() {
	return overlib(
		OLiframeContent('<?=$root_path?>modules/radiology/radiology_patient_request.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_doctor=1&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>',
			800, 370, 'fGroupTray', 1, 'auto'),
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0>',
		CAPTIONPADDING,4, CAPTION,'Radiology Results',
		MIDX,0, MIDY,0,
		STATUS,'Radiology Results');
}

function openPharmaRequest(area) {
	if(ptype=='doctor') autoTagging();	

	return overlib(
	OLiframeContent('<?=$root_path?>modules/pharmacy/seg-pharma-order.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&from=CLOSE_WINDOW&area='+area+'&pid=<?=$pid?>&encounterset=<?=$encounter_nr?>&is_dr=<?=$is_dr?>&billing=1&request_source=<?=$request_source?>',
		800, 370, 'fGroupTray', 0, 'auto'),
	WIDTH,410, TEXTPADDING,0, BORDER,0,
	STICKY, SCROLL, CLOSECLICK, MODAL,
	CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
	CAPTIONPADDING,2, CAPTION,'Pharmacy Request',
	MIDX,0, MIDY,0,
	STATUS,'Pharmacy Request');

}

function openMiscellaneousRequest() {
	if(ptype=='doctor' && or_no!='') autoTagging();	
	return overlib(
	OLiframeContent('<?=$root_path?>modules/dialysis/seg-misc-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&from=CLOSE_WINDOW&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&mode=new&area=<?=$ptype?>',
		800, 370, 'fGroupTray', 0, 'auto'),
	WIDTH,800, TEXTPADDING,0, BORDER,0,
	STICKY, SCROLL, CLOSECLICK, MODAL,
	CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
	CAPTIONPADDING,2, CAPTION,'Miscellaneous Request',
	MIDX,0, MIDY,0,
	STATUS,'Miscellaneous Request');
}

function initialize() {
	initializeTab(0);
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
	//alert($('is_bill_final').value);
	//$('is_bill_final').value=1;
	if($('is_bill_final').value==1) {
		document.getElementById('viewRequestPrintoutBtn').disabled = true;
		document.getElementById('openLabRequestBtn').disabled = true;
		document.getElementById('openICLabRequestBtn').disabled = true;
		document.getElementById('openBloodRequestBtn').disabled = true;
		document.getElementById('openSpLabRequestBtn').disabled = true;
		document.getElementById('openRadioRequestBtn').disabled = true;
		document.getElementById('openPharmaRequestBtnIP').disabled = true;
		document.getElementById('openPharmaRequestBtnMG').disabled = true;
		document.getElementById('openMiscellaneousRequestBtn').disabled = true;
	}

	if (AJAXTimerID) clearTimeout(AJAXTimerID);
	var enc_nr = $('encounter_nr').value;
	var pid = $('pid').value;
	var src = $('request_source').value;
	var billed = $('is_bill_final').value;
	var seldate = $('seldate').value;

	if($('is_ic').value==1) {
			switch(id)
			{
				case 0:
					AJAXTimerId = setTimeout("xajax_populateLabRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",100);
					break;
				case 1:
					AJAXTimerId = setTimeout("xajax_populateICLabRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",100);
					break;
				case 2:
					AJAXTimerId = setTimeout("xajax_populateBloodRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",100);
					break;
				case 3:
					AJAXTimerId = setTimeout("xajax_populateSpLabRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
					break;
				case 4:
					AJAXTimerId = setTimeout("xajax_populateRadioRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
					break;
				case 5:
					AJAXTimerId = setTimeout("xajax_populateIpRequests('"+enc_nr+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
					break;
				case 6:
					AJAXTimerId = setTimeout("xajax_populateMgRequests('"+enc_nr+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
					break;
				case 7:
					AJAXTimerId = setTimeout("xajax_populateMiscRequests('"+enc_nr+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
					break;
			}
	} else {
		switch(id)
		{
			case 0:
				AJAXTimerId = setTimeout("xajax_populateLabRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",100);
				break;
			case 1:
				AJAXTimerId = setTimeout("xajax_populateBloodRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",100);
				break;
			case 2:
				AJAXTimerId = setTimeout("xajax_populateSpLabRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
				break;
			case 3:
				AJAXTimerId = setTimeout("xajax_populateRadioRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
				break;
			case 4:
				AJAXTimerId = setTimeout("xajax_populateIpRequests('"+enc_nr+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
				break;
			case 5:
				AJAXTimerId = setTimeout("xajax_populateMgRequests('"+enc_nr+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
				break;
			case 6:
				AJAXTimerId = setTimeout("xajax_populateMiscRequests('"+enc_nr+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
				break;
		}
	}

	xajax_computeTotalPayment(pid, enc_nr);
}

/*
* Creted by Jarel
* Created on 11/10/2013
* Use to call ajax function for auto tagging of patient, only if request is from Doctor's Dashboard 
*/
function autoTagging(){
	xajax_autoTagging($J('#encounter_nr').val(),doc_nr,or_no);
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

var AJAXTimerID=0;
document.observe('dom:loaded', function(){
	initializeTab(0);
	//initialize();
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

$encounter_types = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)', "5"=>'DIALYSIS', "6"=>'INDUSTRIAL CLINIC');
$encounter_nr = isset($_POST['encounter_nr']) ? $_POST['encounter_nr'] : $_GET['encounter_nr'];
$seg_encounter = new Encounter();
$encounter_details = $seg_encounter->getEncounterInfo($encounter_nr);
$encounter_type = $encounter_types[$encounter_details['encounter_type']];
$smarty->assign('encounter_type', $encounter_type);
$smarty->assign('encounter_nr', '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'" />');

$is_bill_final = 0;
$sql_billed = "SELECT is_final FROM seg_billing_encounter WHERE encounter_nr ='".$encounter_nr."'";
$result_bill = $db->Execute($sql_billed);
if($row_bill = $result_bill->FetchRow())    {
	$is_bill_final = $row_bill['is_final'];
}

$smarty->assign('is_bill_final', '<input type="hidden" name="is_bill_final" id="is_bill_final" value="'.$is_bill_final.'"/>');


$service_type_code = array (49,50,51,52,53,54,"");
$service_type_name = array ("Physical Medicine & Rehab", "Dental", "Orthopedics", "ENT-HNS", "Pediatrics", "Special Lab", "Other");
$service_type_options = "<option value='0'> -Select service type- </option";
for($i=0;$i<count($service_type_code);$i++)
{
	$service_type_options.="<option value='".$service_type_code[$i]."'>".$service_type_name[$i]."</option>";
}
$smarty->assign('miscServiceTypes', $service_type_options);

$isIc = FALSE;
if(strtolower($ptype)=="ic") {
 $isIc = TRUE;
}
$smarty->assign('isIC', $isIc);
$smarty->assign('isIc_hidden', '<input type="hidden" id="is_ic" value="'.$isIc.'"/>');
$smarty->assign('dateToday', date('F d, Y'));
$smarty->assign('dateTodayValue', date('Y-m-d'));

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
$smarty->assign('sMainBlockIncludeFile','clinics/request_tray.tpl'); //Assign the or_main template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame

?>
