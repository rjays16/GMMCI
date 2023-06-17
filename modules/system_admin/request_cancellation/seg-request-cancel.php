<?php
//created by cha August 12, 2010

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/system_admin/request_cancellation/request-cancel.common.php');

define('NO_CHAIN',1);
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path.'modules/system_admin/edv.php'.URL_APPEND;
$returnfile=$root_path.'modules/system_admin/edv.php'.URL_APPEND;

$thisfile=basename(__FILE__);

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');


$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$thisfile='seg-request-cancel.php';

//initialize smarty
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Toolbar title
$smarty->assign('sToolbarTitle','Request Cancellation');

# href for the return button
$smarty->assign('pbBack',$returnfile);

# href for the  button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# Window bar title
$smarty->assign('sWindowTitle',"Request Cancellation");
$smarty->assign('breakFile',$breakfile);

ob_start();
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>
<script type="text/javascript" src="js/md5.js"></script>

<script type="text/javascript">

function initialize()
{
	ListGen.create($('request-list'),{
		id: 'requests',
		url: '<?=$root_path?>modules/system_admin/request_cancellation/ajax_list_request.php',
		params: {'cost_center':$('service_area').value, 'search_name':$('name').value},
		width: 'auto',
		height: 'auto',
		rowHeight: 20,
		effects: true,
		autoload: true,
		layout: [
			//['<h1>List of Requests</h1>'],
			['#pagestat', '#first', '#prev', '#next', '#last', '#refresh'],
			['#thead'],
			['#tbody'],
			['#tfoot']
		],
		columnModel: [
			{
				name: 'request_date',
				label: 'Date',
				width: 80,
				sortable: true,
				sorting: ListGen.SORTING.asc,
				styles:{
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'patient_id',
				label: 'PID',
				width: 60,
				sortable: false,
				styles:{
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'patient_enc',
				label: 'CASE#',
				width: 70,
				sortable: false,
				styles:{
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'refno',
				label: 'Ref No.',
				width: 80,
				sortable: true,
				sorting: ListGen.SORTING.none,
				styles:{
					font: 'Tahoma',
					fontSize: '11',
					fontWeight: 'bold'
				}
			},
			{
				name: 'patient_name',
				label: 'Name',
				width: 120,
				sortable: true,
				sorting: ListGen.SORTING.none,
				styles: {
					color: '#660000',
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'item_name',
				label: 'Item',
				width: 120,
				//sortable: false,
				sortable: true,
				sorting: ListGen.SORTING.none,
				styles:{
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'request_bill',
				label: 'Is Billed',
				width: 60,
				sortable: false,
				styles: {
					textAlign: 'center',
					font: 'Tahoma',
					fontSize: '11',
					fontWeight: 'bold'
				}
			},
			{
				name: 'request_status',
				label: 'Status',
				width: 60,
				sortable: true,
				sorting: ListGen.SORTING.none,
				styles: {
					textAlign: 'center',
					font: 'Tahoma',
					fontSize: '11',
					fontWeight: 'bold'
				}
			},
			{
				name: 'request_flag',
				label: 'Charge Type',
				width: 80,
				sortable: true,
				sorting: ListGen.SORTING.none,
				styles: {
					textAlign: 'center',
					font: 'Tahoma',
					fontSize: '11',
					fontWeight: 'bold'
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

function listRequests()
{
	$('request-list').list.params = {
			'cost_center':$('service_area').value,
			'search_name':$('name').value,
			'search_pid':$('pid').value,
			'search_encounter':$('encounter_nr').value
	};
	$('request-list').list.refresh();
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

function cancelFlag(area, refno, item_code, flag)
{
	if(flag=="") {
		alert("No request flag");
		return false;
	}
	var rep = confirm("Cancel charge type of this request item?")
	if(rep) {
		var reason = prompt("Enter reason for cancellation.")
		if(reason) {
			xajax_cancelRequestFlag(area, refno, item_code, reason);
		} else {
			alert("Invalid reason");
			return false;
		}
	}
}

function cancelStatus(area, refno, item_code)
{
	var rep = confirm("Change status of this request item to pending?")
	if(rep) {
		var reason = prompt("Enter reason for cancellation.")
		if(reason) {
			xajax_cancelStatus(area, refno, item_code, reason);
		} else {
			alert("Invalid reason");
			return false;
		}
	}
}

function deleteRequest(area, refno, item_code)
{
	var rep = confirm("Delete this request item?")
	if(rep) {
		if(area.toLowerCase()=='ph' || area.toLowerCase()=='ot') {
			xajax_deleteRequestItem(area, refno, item_code, reason);
			return false;
		}
		var reason = prompt("Enter reason for deletion.")
		if(reason) {
			xajax_deleteRequestItem(area, refno, item_code, reason);
		} else {
			alert("Invalid reason");
			return false;
		}
	}
}

function alertFlag(rep)
{
	alert(rep)
	return false;
}

document.observe('dom:loaded', initialize);
</script>

<?
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();

$smarty->assign('form_start','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform">');
$smarty->assign('form_end','</form>');

//$smarty->assign('person_search', '<input type="text" id="person_search" name="person_search" style="width:60%" class="segInput" onkeyup="if(this.value.length>=3){searchTemplate();}return false;" disabled="disabled"/>');
$smarty->assign('search_btn', '<button class="segButton" onclick="listRequests();;return false;" style="cursor:pointer"><img src="'.$root_path.'gui/img/common/default/zoom.png"/>Search</button>');

$smarty->assign('serviceArea', '<select class="segInput" id="service_area" name="service_area" style="width:60%" onchange="$(\'selpatient\').disabled=false;$(\'name\').disabled=false;">
					<option value="0">-Select an area-</option>
					<option value="LD">Laboratory</option>
					<option value="RD">Radiology</option>
					<option value="PH">Pharmacy</option>
					<option value="OT">Miscellaneous</option>
					</select>');
$options = '<option value="p_name">Patient name</option>'.
					 '<option value="p_pid">Patient ID</option>'.
					 '<option value="p_enc">Patient Case#</option>';
$smarty->assign('patientOptions', '<select class="segInput" id="selpatient" name="selpatient" onchange="changePatientOptions(this.value)" disabled="disabled">'.$options.'</select>');
$smarty->assign('pSearchName', '<input type="text" style="width:40%" id="name" class="segInput" disabled="disabled"/>');
$smarty->assign('pSearchId', '<input type="text" style="width:40%" id="pid" class="segInput"/>');
$smarty->assign('pSearchEnc', '<input type="text" style="width:40%" id="encounter_nr" class="segInput"/>');;
$smarty->assign('rootpath', $root_path);
ob_start();
?>

<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" id="userck" value="<?php echo $userck?>">
<input type="hidden" name="encoder" id="encoder" value="<?php echo  str_replace(" ","+",$_COOKIE[$local_user.$sid])?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<?
$sTemp = ob_get_contents();
$sTable = ob_get_contents();
ob_end_clean();
$smarty->assign('sTable',$sTable);
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','left').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');

/**
* show Template
*/
# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','system_admin/request_cancellation.tpl');
$smarty->display('common/mainframe.tpl');

?>