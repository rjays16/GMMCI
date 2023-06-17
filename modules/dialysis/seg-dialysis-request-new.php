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

$thisfile='seg-dialysis-request-new.php';

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
$title = "Dialysis :: New Request";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

#save data here
if(isset($_POST["submitted"])) {

	$enc_type = $db->GetOne("SELECT type_nr FROM care_type_encounter WHERE name like 'Dialysis%' AND status <> 'deleted'");
	$last_enc = $enc_obj->getLastEncounterNr("dialysis");
	$new_encounter = $enc_obj->getNewEncounterNr($last_enc,$enc_type);
	$dept_nr = $db->GetOne("SELECT nr FROM care_department WHERE id like 'Dialysis%' AND type='1' AND status <> 'deleted'");

	$new_refno = $dialysis_obj->getNewRefno();
	$dialysis_data = array(
		'refno'=>$new_refno,
		'encounter_nr'=>$new_encounter,
		'pid' => $_POST["pid"],
		'transaction_date'=>$_POST["requestdate"],
		'visit_no'=>$visit_no,
		'requesting_doctor'=>$_POST["request_doctor"],
		'attending_nurse'=>$_POST["attending_nurse"],
		'dialysis_type'=>strtoupper($_POST["dialysis_type"]),
		'remarks'=>$_POST["remarks"],
		'create_id'=>$_SESSION["sess_user_name"],
		'create_date'=>date('Y-m-d H:i:s'),
		'modify_id'=>$_SESSION["sess_user_name"],
		'modify_date'=>date('Y-m-d H:i:s')
	);

	$encounter_data = array(
		'encounter_nr' => $new_encounter,
		'encounter_type' => $enc_type,
		'encounter_date' => $_POST["requestdate"],
		'pid' => $_POST["pid"],
		'current_dept_nr' => $dept_nr,
		'encounter_class_nr' => $enc_type,
		'encounter_status' => '',
		'current_ward_nr' => 0,
		'current_room_nr' => 0,
		'create_id' => $_SESSION["sess_temp_userid"],
		'create_date' => date('Y-m-d H:i:s'),
		'modify_id' => $_SESSION["sess_temp_userid"],
		'modify_date' => date('Y-m-d H:i:s'),
		'history' => 'Create: '.date('Y-m-d H:i:s').' = '.$_SESSION["sess_temp_userid"]
	);

	$saveok = $dialysis_obj->saveTransaction($encounter_data, $dialysis_data, $new_encounter);
	if($saveok) {
		$smarty->assign('sysInfoMessage','Dialysis transaction successfully submitted.');
	}else {
		$smarty->assign('sysErrorMessage','<strong>Error:</strong> Cannot save dialysis transactions.<br/> SQL_ERROR:'.$dialysis_obj->getErrorMsg());
	}

}


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
	//urlholder="<?php echo $root_path ?>"+"modules/dialysis/seg-dialysis-request-window.php?pid="+pid+"&encounter_nr="+encounter_nr;
	//window.open(urlholder,"dienstinfo","width=800,height=600,menubar=no,resizable=yes,scrollbars=yes");
}

function openBillingTray(encounter_nr, pid)
{
	overlib(
	OLiframeContent('../../modules/billing/billing-main.php<?= URL_REDIRECT_APPEND ?>&popUp=1&area=ER&pid='+pid+'&encounter_nr='+encounter_nr,
		800, 440, 'fGroupTray', 0, 'auto'),
		WIDTH,800, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
		CAPTIONPADDING,2, CAPTION,'Billing',
		MIDX,0, MIDY,0,
		STATUS,'Billing');
	return false;
}

function initialize() {
	ListGen.create( $('history_list'), {
		id: 'dialysis_test',
		url: '<?=$root_path?>modules/dialysis/ajax/ajax_dialysis_history.php',
		params: { 'pid':$('pid').value },
		width: 750,
		height: 200,
		autoLoad: true,
		columnModel: [
			{
				name: 'ref_no',
				label: 'Reference No.',
				width: 100,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			{
				name: 'date_visited',
				label: 'Date Visited',
				width: 100,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			{
				name: 'encounter_nr',
				label: 'Case No.',
				width: 100,
				sortable: false
			},
			{
				name: 'dialysis_type',
				label: 'Dialysis',
				width: 100,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			{
				name: 'status',
				label: 'Status',
				width: 100,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			{
				name: 'options',
				label: 'Options',
				width: 250,
				sortable: false
			}
		]
	});

	//openPatientSelect();
	xajax_getDoctors();
	xajax_getNurses();
	xajax_setVisitNo($('pid').value);
}

function setVisitNo(nr)
{
	xajax_setVisitNo($('pid').value);
	$('history_list').list.params={'pid':$('pid').value};
	$('history_list').list.refresh();
}

function openPatientSelect() {
	if ($('select-enc').hasClassName('disabled')) return false;
<?php
$var_arr = array(
	"var_pid"=>"pid",
	"var_encounter_nr"=>"encounter_nr",
	"var_name"=>"name",
	"var_addr"=>'address',
	"var_clear"=>"clear-enc",
	"var_include_walkin"=>"0",
	"var_reg_walkin"=>"0",
	"var_age"=>"age",
	"var_gender"=>"gender",
	"var_adm_diagnosis"=>"diagnosis",
	"var_enctype"=>"patient_type",
	"var_enctype_show"=>"1",
	"var_type"=>"encounter_type",
	"var_date_admitted"=>"admission_date",
	"var_location"=>"location",
	"var_dob"=>"birthdate",
	"var_civil_status"=>"civil_status",
	//"var_photo_filename"=>"photo_row"
	"var_photo_filename"=>"headpic"
);
$vas = array();
foreach($var_arr as $i=>$v) {
	$vars[] = "$i=$v";
}
$var_qry = implode("&",$vars);
?>
	overlib(
			OLiframeContent('seg-dialysis-search-person.php?<?=$var_qry?>&var_include_enc=0&from_dialysis=1',
			700, 400, 'fSelEnc', 0, 'no'),
			WIDTH,700, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2,
			CAPTION,'Select registered person',
			MIDX,0, MIDY,0,
			STATUS,'Select registered person');
	return false;
}

function validate() {

	if($('encounter_nr').value=="")
	{
		alert("Please select a patient first.");
		$('name').focus();
		return false;
	}
	else if($('request_doctor').value=="0")
	{
		alert("Please select the requesting doctor.");
		$('request_doctor').focus();
		return false;
	}
	else if($('attending_nurse').value=="0")
	{
		alert("Please select the attending nurse.");
		$('attending_nurse').focus();
		return false;
	}

	return true;
}

function changeStatus(id, refno, enc_nr){
	if($(id).value=="1") {
		var answer = confirm("Performing this action will disable any requests for Reference #"+refno+". Continue?")
		if(answer) {
			xajax_changeTransactionStatus(refno, $(id).value, "", enc_nr);
		}
	}else {
		var reason = prompt("Please log the reason to UNDONE request.");
		if(reason) {
			xajax_changeTransactionStatus(refno, $(id).value, reason, enc_nr);
		}
	}
}

function refreshHistory() {
	$('history_list').list.refresh();
}

function deleteRequest(enc_nr, pid, refno)
{
	var answer = confirm("Performing this action will disable any requests for Reference #"+refno+". Continue?")
	if(answer) {
		xajax_deleteDialysisRequest(enc_nr, pid, refno);
	}
}

function openHistoryReport()
{
	if($('pid').value=="")
	{
		alert("Please select a patient first.");
		$('name').focus();
		return false;
	}else {
		window.open('seg-dialysis-history-report.php?pid='+$('pid').value,'history_report',"width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
	}
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

$smarty->assign('sSelectEnc','<button id="select-enc" class="button" onclick="openPatientSelect();return false"><img '.createComIcon($root_path, 'user.png').' />Select</button>');
$smarty->assign('sClearEnc','<input class="segButton" id="clear-enc" type="button" value="Reset" disabled="disabled" onclick="if (confirm(\'Search for another patient?\')) resetControls()"/>');

$smarty->assign('submitBtn','<button class="segButton" onclick="if (confirm(\'Process this dialysis request?\')) return validate();"><img src="'.$root_path.'gui/img/common/default/arrow_redo.png"/>Submit</button>');

$smarty->assign('cancelBtn','<button class="segButton" onclick="return false;"><img src="'.$root_path.'gui/img/common/default/cancel.png"/>Cancel</button>');
$smarty->assign('historyBtn','<button class="segButton" onclick="openHistoryReport();return false;"><img src="'.$root_path.'gui/img/common/default/report.png"/>Print History</button>');
//$smarty->assign('requestBtn','<button class="segButton" onclick="openRequestTray();return false;"><img src="'.$root_path.'gui/img/common/default/report.png"/>Request</button>');
$smarty->assign('detailsBtn','<button class="segButton" onclick="return false;"><img src="'.$root_path.'gui/img/common/default/book_open.png"/>Details</button>');

$smarty->assign('sPatientEncNr','<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>');
$smarty->assign('sPatientID','<input id="pid" name="pid" class="clear" type="text" value="'.$_POST["pid"].'" readonly="readonly" style="color:#006600; font:bold 16px Arial;"/>');
$smarty->assign('sPatientName','<input class="segInput" id="name" name="name" type="text" size="30" style="font:bold 12px Arial; color:#0000ff" readonly="readonly" value="'.$_POST["name"].'"/>');
$smarty->assign('sPatientAge','<input class="segInput" id="age" name="age" type="text" size="15" style="font:bold 12px Arial;" readonly="readonly" value="'.$_POST["age"].'"/>');
$smarty->assign('sPatientBirthday','<input class="segInput" id="birthdate" name="birthdate" type="text" size="20" style="font:bold 12px Arial;" readonly="readonly" value="'.$_POST["birthdate"].'"/>');
$smarty->assign('sPatientGender','<input class="segInput" id="gender" name="gender" type="text" size="15" style="font:bold 12px Arial;" readonly="readonly" value="'.$_POST["gender"].'"/>');
$smarty->assign('sPatientStatus','<input class="segInput" id="civil_status" name="civil_status" type="text" size="17" style="font:bold 12px Arial;" readonly="readonly" value="'.$_POST["civil_status"].'"/>');
$smarty->assign('sAddress','<textarea class="segInput" id="address" name="address" style="width:70%;font:bold 12px Arial;border:1px solid #c3c3c3; overflow-y:scroll; float:left;" readonly="readonly">'.$_POST["address"].'</textarea>');

$smarty->assign('sPatientDiagnosis', '<textarea class="segInput" id="diagnosis" name="diagnosis" style="font:bold 12px Arial; width:95%; border:1px solid #c3c3c3; overflow-y:scroll; float:left;" readonly="readonly">'.$_POST["diagnosis"].'</textarea>');
$smarty->assign('sPatientLocation', '<input class="segInput" id="location" name="location" type="text" size="45" style="font:bold 12px Arial" readonly="readonly" value="'.$_POST["location"].'"/>');
$smarty->assign('sPatientAdmissionDate', '<input class="segInput" id="admission_date" name="admission_date" type="text" size="30" style="font:bold 12px Arial" readonly="readonly" value="'.$_POST["admission_date"].'"/>');
$smarty->assign('sPatientDischargeDate', '<input class="segInput" id="discharge_date" name="discharge_date" type="text" size="30" style="font:bold 12px Arial" readonly="readonly" value="'.$_POST["discharge_date"].'"/>');
$smarty->assign('sPatientType', '<input class="segInput" id="patient_type" name="patient_type" type="text" size="30" style="font:bold 12px Arial" readonly="readonly" value="'.$_POST["patient_type"].'"/>');

/*$new_refno = $dialysis_obj->getNewRefno();
$refno = $_POST["reference_no"] ? $_POST["reference_no"] : $new_refno;
$smarty->assign('requestReferenceNo', '<input class="segInput" id="reference_no" name="reference_no" type="text" size="30" style="font:bold 12px Arial" readonly="readonly" value="'.$refno.'"/>');*/

$new_visit_no = 0;
$visit_no = $_POST["visit_no"] ? $_POST["visit_no"] : $new_visit_no;
$smarty->assign('requestVisitNo', '<input class="clear" id="visit_no" name="visit_no" type="text" style="font:bold 12px Arial" readonly="readonly" value="'.$visit_no.'"/>');
$smarty->assign('visit_number', '<input class="clear" id="visit_number" name="visit_number" type="text" style="font:bold 12px Arial" readonly="readonly" value="'.$visit_no.'"/>');

$smarty->assign('requestDoctors', '<select class="segInput" id="request_doctor" name="request_doctor" style="font:bold 12px Arial"></select>');
$smarty->assign('requestNurses', '<select class="segInput" id="attending_nurse" name="attending_nurse" style="font:bold 12px Arial"></select>');
$smarty->assign('requestDialysisType',
				 '<input type="radio" id="dtypeb" name="dialysis_type" checked="checked" value="before"/><label>Before Dialysis</label>&nbsp;&nbsp;
				 <input type="radio" id="dtypea" name="dialysis_type" value="after"/><label>After Dialysis</label>
				 ');
//$smarty->assign('dialysis_type', '<input type="hidden" id="dialysis_type" name="dialysis_type" value="";/>');
$smarty->assign('requestRemarks','<textarea class="segInput" id="remarks" name="remarks" style="width:100%"></textarea>');
$encoder = $_POST["request_encoder"] ? $_POST["request_encoder"] : $_SESSION["sess_user_name"];
$smarty->assign('requestEncoder', '<input class="clear" id="request_encoder" name="request_encoder" type="text" size="30" style="font:bold 12px Arial" value="'.$encoder.'"/>');
$smarty->assign('requestStatus', '<select class="segInput" id="request_status" name="request_status">
				<option value="undone">Undone</option>
				<option value="done">Done</option>
				</select>
				');
$smarty->assign('requestDate','<span id="show_requestdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['requestdate'])) : $curDate_show).'</span>
<input class="jedInput" name="requestdate" id="requestdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['requestdate'])) : $curDate).'" style="font:bold 12px Arial">');

$smarty->assign('sCalendarIcon','<img '.createComIcon($root_path,'date_add.png','0').' id="requestdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
$jsCalScript = "<script type=\"text/javascript\">
Calendar.setup({
		displayArea : \"show_requestdate\",
			inputField : \"requestdate\",
			ifFormat : \"%Y-%m-%d %H:%M\",
			daFormat : \" %B %e, %Y %I:%M%P\",
			showsTime : true,
			button : \"requestdate_trigger\",
			singleClick : true,
			step : 1
});
</script>";
$smarty->assign('jsCalendarSetup', $jsCalScript);

$active_tab = 'request';
$smarty->assign('bTab'.ucfirst($active_tab),TRUE);
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('encounter_type', '<input type="hidden" name="encounter_type" id="encounter_type" />');

//include_once($root_path.'include/inc_photo_filename_resolve.php');
$photo_src = $_POST["photo_src"] ? $_POST["photo_src"] : '../../gui/img/control/default/en/en_x-blank.gif';
$smarty->assign('img_source',$photo_src);

//$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="validate();">');
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform">');
$smarty->assign('sFormEnd','</form>');

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
$smarty->assign('sMainBlockIncludeFile','dialysis/request_main.tpl');
$smarty->display('common/mainframe.tpl');

