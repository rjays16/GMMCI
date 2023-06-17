<?
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('NO_2LEVEL_CHK',1);
$local_user='ck_pflege_user';

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
require_once($root_path.'modules/laboratory/ajax/lab-result-new.common.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
		require_once($root_path.'include/care_api_classes/class_lab_results.php');
		require_once($root_path.'include/care_api_classes/class_department.php');
		require_once($root_path.'include/care_api_classes/class_personell.php');
		require_once($root_path.'include/care_api_classes/class_ward.php');

global $db;

$smarty = new Smarty_Care('common');

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

<script type="text/javascript" src="js/lab-result-new.js"></script>
<script type="text/javascript">
var $j = jQuery.noConflict();


function openLabResults() {
	// window.open('<?=$root_path?>modules/laboratory/seg_lab_manual_result_main.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_doctor=1&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>',null,'menubar=no,directories=no,height=600,width=800,resizable=yes')
	return overlib(
		OLiframeContent('<?=$root_path?>modules/laboratory/seg_lab_manual_result_main.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_doctor=1&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>',
			775, 370, 'fGroupTray', 0, 'auto'),
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0>',
		CAPTIONPADDING,2, CAPTION,'Laboratory Results',
		MIDX,0, MIDY,0,
		STATUS,'Laboratory Results');
		}

function checkStatus(value, cu_unit, si_unit, cu_low, cu_high, si_low, si_high, param_name){
	var high, low;
	if(si_unit){
		low = parseFloat(si_low);
		high = parseFloat(si_high);
				}
	else if(cu_unit){
		low = parseFloat(cu_low);
		high = parseFloat(cu_high);
						}

	if(value < low)
		document.getElementById('status_'+param_name).innerHTML = '<font color=red>LOW</font>';
	else if(value > high)
		document.getElementById('status_'+param_name).innerHTML = '<font color=red>HIGH</font>';
						 else
		document.getElementById('status_'+param_name).innerHTML = '<font color=blue>NORMAL</font>';

	if(!value)
		document.getElementById('status_'+param_name).innerHTML = '';
					}

function deleteItem(param_id, form_id){
	var table = document.getElementById('lab-result-list'+form_id);
	var rmvRow = document.getElementById('ip_row'+param_id);
	var dBody = table.select("tbody")[0];

	if (table && rmvRow) {
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
				}

	var items = dBody.getElementsByTagName('tr');
	if (items.length == 0){
		emptyIntialRequestList(form_id);
						}
								}

function emptyIntialRequestList(form_id){
	var table = document.getElementById('lab-result-list'+form_id);
	if(table){
		table.parentNode.removeChild(table);
										 }
										 }

function initialize() {
	var enc_nr = $('encounter_nr').value;
	var pid = $('pid').value;
	var refno = '<?= $_POST["refno"] ? $_POST["refno"]:$_GET["refno"]?>';
	var service_code = '<?= $_POST["service_code"] ? $_POST["service_code"]:$_GET["service_code"]?>';

	xajax_checkResult(refno,service_code);
										 }

function refreshPage() {
	window.location.reload();
}

function ReloadWindow(pid){
	 window.parent.location = 'seg-lab-request-order-list.php?done=1&searchkey='+pid;
}

function saveResult(){
	var item = document.getElementsByName('items[]');
	var unit;
	var refno = '<?= $_POST["refno"] ? $_POST["refno"]:$_GET["refno"]?>';
	var service_code = '<?= $_POST["service_code"] ? $_POST["service_code"]:$_GET["service_code"]?>';
	var group_id = '<?= $_POST["group_id"] ? $_POST["group_id"] : $_GET["group_id"]?>';
	var pathologist = document.getElementById('pathologist').value;
	var medtech = document.getElementById('medtech').value;
	var date = document.getElementById('show_date').innerHTML;
	var confidential = false;
	var arr = new Array;
	var arr_unit = new Array;
	var arr_param = new Array;
	for(var i=0; i<item.length;i++){
		var param_id = item[i].value;
		var input = document.getElementById(item[i].value).value;
		if(document.getElementById(item[i].value+'_unit'))
			unit = document.getElementById(item[i].value+'_unit').value;
		else
			unit = '';

		arr.push(input);
		arr_unit.push(unit);
		arr_param.push(param_id);
		}
	xajax_saveResult(refno, service_code, group_id, pathologist, medtech, date, arr, arr_unit, arr_param, confidential);
}

function cancelResult(){
	var refno = '<?= $_POST["refno"] ? $_POST["refno"]:$_GET["refno"]?>';
	var service_code = '<?= $_POST["service_code"] ? $_POST["service_code"]:$_GET["service_code"]?>';
	var group_id = '<?= $_POST["group_id"] ? $_POST["group_id"] : $_GET["group_id"]?>';
		var answer = confirm("Are you sure that you want to delete data?\n Click OK if YES, otherwise CANCEL.");
	
		if(answer)
		{
				var answer = prompt ('Reason:','');
				if(answer)
				{
			xajax_deleteResult(refno, service_code, group_id, answer);
				}
				else
						return false;
		}
		else
				return false;
}

function doneResult(){
	var refno = '<?= $_POST["refno"] ? $_POST["refno"]:$_GET["refno"]?>';
	var service_code = '<?= $_POST["service_code"] ? $_POST["service_code"]:$_GET["service_code"]?>';
	var group_id = '<?= $_POST["group_id"] ? $_POST["group_id"] : $_GET["group_id"]?>';
	var pid = $('pid').value;
	var is_served = 1;

	var answer = confirm("Are you sure that the request is already done? It can't be undone. \n Click OK if YES, otherwise CANCEL.");

	if(answer)
		xajax_saveOfficialResult(refno, group_id, is_served, service_code, pid);
								else
		return false;
	
		}

function printResult(){
	var refno = '<?= $_POST["refno"] ? $_POST["refno"]:$_GET["refno"]?>';
	var service_code = '<?= $_POST["service_code"] ? $_POST["service_code"]:$_GET["service_code"]?>';
	var group_id = '<?= $_POST["group_id"] ? $_POST["group_id"] : $_GET["group_id"]?>';
	var pid = $('pid').value;
	
	window.open("../../modules/repgen/pdf_lab_results.php?pid="+pid+"&refno="+refno+"&group_id="+group_id+"&service_code="+service_code,"viewPatientResult","left=150, top=100, width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
}

document.observe('dom:loaded', initialize);
</script>
<?
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

//$smarty->assign('form_start', '<form name="main_or_form" method="POST" action="'.$_SERVER['PHP_SELF'].'">');
$smarty->assign('form_end', '</form>');

$refno = $_POST["refno"] ? $_POST["refno"]:$_GET["refno"];
$pid = isset($_POST['pid']) ? $_POST['pid'] : $_GET['pid'];
$service_code = $_POST["service_code"] ? $_POST["service_code"]:$_GET["service_code"];
$group_id = $_POST["group_id"] ? $_POST["group_id"] : $_GET["group_id"];

$lab_results = new Lab_Results();
$ward_obj = new Ward;
$pers_obj= new Personell;
$seg_person = new Person($pid);
$person_info = $lab_results->get_patient_data($refno, $group_id);
if(!$person_info)
	$person_info = $lab_results->get_patient_walkin($refno, $group_id);
$result = $pers_obj->getPersonellInfo($person_info['request_doctor']);
if (trim($result["name_middle"]))
	 $dot  = ".";

$doctor = trim($result["name_first"])." ".substr(trim($result["name_middle"]),0,1).$dot." ".trim($result["name_last"]);
$doctor = htmlspecialchars(mb_strtoupper($doctor));
$doctor = trim($doctor);
if(!empty($doctor))
	$doctor = "DR. ".$doctor;

if($person_info['current_ward_nr']){
	$ward = $ward_obj->getWardInfo($person_info['current_ward_nr']);
	$location = strtoupper(strtolower(stripslashes($ward['name'])))." Rm # : ".$person_info['current_room_nr'];
														}
														else
	$location = "WALK-IN";

$middle_initial = (strnatcasecmp($person_info['name_middle'][0], $person_info['name_middle'][1]) == 0) ? ucwords(substr($person_info['name_middle'], 0, 2)) : strtoupper($person_info['name_middle'][0]);
$person_name = ucwords($person_info['name_last']) . ', ' . ucwords($person_info['name_first']) . ' ' . $middle_initial;
if($person_info['sex'] == 'm'){
	$gender = 'Male';
														}
else if($person_info['sex'] == 'f'){
	$gender = 'Female';
														}
																else
	$gender = 'Unknown';
$person_address = implode(", ",array_filter(array($person_info['street_name'], $person_info["brgy_name"], $person_info["mun_name"])));
if ($person_info["zipcode"])
	$person_address.=" ".$person_info["zipcode"];
if ($person_info["prov_name"])
	$person_address.=" ".$person_info["prov_name"];

$medtech = $lab_results->getMedTech();
while ($res = $medtech->FetchRow()){
		$option .= "<option value='". $res["pid"] ."' ". $tmp .">". mb_strtoupper($res["name"]) ."</option>";
														}

$smarty->assign('medTech', '<select name="medtech" id="medtech">'.$option.'</select>' );

$patho = $lab_results->getPathologist();
while ($res = $patho->FetchRow()){
		$options .= "<option value='". $res["pid"] ."' ". $tmp .">". mb_strtoupper($res["name"]) ."</option>";
														}

$smarty->assign('pathologist', '<select name="pathologist" id="pathologist">'.$options.'</select>' );


$code = $lab_results->getServiceName1($refno, $service_code, $group_id);

$smarty->assign('sPatientID','<input id="encounter_nr" name="encounter_nr" class="clear" type="text" value="'.$person_info['encounter_nr'].'" readonly="readonly" style="color:#006600; font:bold 16px Arial;"/>');
$smarty->assign('patient_name', $person_name);
$smarty->assign('patient_address', ($person_address)?$person_address:$person_info['orderaddress']);
$smarty->assign('patient_ward', $location);
$smarty->assign('patient_sex', $gender);
$smarty->assign('patient_age', ($person_info['age'])?$person_info['age']:'Unknown');
$smarty->assign('patient_dr', $doctor);
$smarty->assign('service_code', $code);
$smarty->assign('encounter_nr', '<input type="hidden" name="pid" id="pid" value="'.$person_info['pid'].'" />');
$smarty->assign('saveBtn', '<button class="segButton" id="save_btn" onclick="saveResult();"><img src="'.$root_path.'gui/img/common/default/disk.png"/>Save</button>');
$smarty->assign('doneBtn', '<button class="segButton" id="done_btn" onclick="doneResult();"><img src="'.$root_path.'gui/img/common/default/tick.png"/>Done</button>');
$smarty->assign('cancelBtn', '<button class="segButton" style="display:none" id="cancel_btn" onclick="cancelResult();"><img src="'.$root_path.'gui/img/common/default/delete.png"/>Cancel</button>');
$smarty->assign('printBtn', '<button class="segButton" style="display:none" id="print_btn" onclick="printResult();"><img src="'.$root_path.'gui/img/common/default/printer.png"/>Print</button>');

$smarty->assign('dateToday', date('F d, Y'));
$smarty->assign('dateTodayValue', date('Y-m-d'));

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
$smarty->assign('sMainBlockIncludeFile','laboratory/lab_result.tpl'); //Assign the or_main template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame

?>
