<?php
//created by cha august 28, 2010
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/prescription/ajax/seg-soap.common.php');

define('NO_CHAIN',1);
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path.'modules/clinics/labor.php'.URL_APPEND;
$returnfile=$root_path.'modules/clinics/labor.php'.URL_APPEND;

$thisfile=basename(__FILE__);

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');
//require_once($root_path.'include/care_api_classes/prescription/class_doctors_soap.php');
//$soapObj = new SegDoctorsSoap();

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$thisfile='seg-clinic-new-prescription.php';

//initialize smarty
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Toolbar title
$smarty->assign('sToolbarTitle','Clinics :: SOAP Entry');

# href for the return button
$smarty->assign('pbBack',$returnfile);

# href for the  button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# Window bar title
$smarty->assign('sWindowTitle',"Clinics :: SOAP Entry");
$smarty->assign('breakFile',$breakfile);

ob_start();
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path?>js/listgen/css/default/default.css" type="text/css"/>
<link rel="stylesheet" href="<?= $root_path?>modules/prescription/css/soap_entry.css" type="text/css"/>
<script type="text/javascript" src="js/md5.js"></script>

<script type="text/javascript">
var $J = jQuery.noConflict();

function saveSoap(type)
{
	var note="";
	switch(type.toLowerCase())
	{
		case 's':
			if($('subjective_text').value=="") {
				alert("Please fill an entry for subjective note.");
				$('subjective_text').focus();
				return false;
			} else {
				note = $('subjective_text').value;
			}
			break;
		case 'o':
			if($('objective_text').value=="") {
				alert("Please fill an entry for objective note.");
				$('objective_text').focus();
				return false;
			} else {
				note = $('objective_text').value;
			}
			break;
		case 'a':
			if($('assessment_text').value=="") {
				alert("Please fill an entry for assessment note.");
				$('assessment_text').focus();
				return false;
			} else {
				note = $('assessment_text').value;
			}
			break;
		case 'p':
			if($('plan_text').value=="") {
				alert("Please fill an entry for plan note.");
				$('plan_text').focus();
				return false;
			} else {
				note = $('plan_text').value;
			}
			break;
	}

	xajax_saveSoapNote(type, note, $('pid').value);
}

function showNotes(listId, date, note, noteId, is_cancel, type, bullet_color, doctor_nr)
{
	var divSrc = $(listId);
	if(divSrc) {
		//alert(doctor_nr+"|"+$('doctor_nr').value)
		if(doctor_nr!=$('doctor_nr').value) {
			var html =
				'<div class="soap-note">'+
					'<div class="soap-note-header"><img src="<?=$root_path?>gui/img/common/default/'+bullet_color+'"/><span>'+date+'</span></div>'+
					'<div class="soap-note-body"><span class="'+(is_cancel==1?'input-strike':'input-note')+'">'+note+'</span></span>'+
				'</div>';
		} else {
			var html =
				'<div class="soap-note">'+
					'<div class="soap-note-header"><img src="<?=$root_path?>gui/img/common/default/'+bullet_color+'"/><span>'+date+'</span></div>'+
					'<div class="soap-note-body"><span '+(is_cancel==1?'class="input-strike" onclick="unToggleNote(\''+noteId+'\',\''+type+'\')" title="Undo delete"':'class="input-note" onclick="toggleNote(\''+noteId+'\',\''+type+'\')" title="Delete note"')+'>'+note+'</span></div>'+
				'</div>';
		}
		divSrc.insert(html);
	}
}

function toggleNote(noteId, type)
{
	xajax_deleteSoapNote(noteId, type);
	return false;
}

function unToggleNote(noteId, type)
{
	xajax_undoDeleteSoapNote(noteId, type);
	return false;
}

function clearText(id)
{
	$(id).value="";
}

function refreshList(type)
{
	xajax_showNotes(type, $('pid').value);
}

function listDoctors(doctor_nr, doctor_name, bullet_color)
{
	var divSrc = $('doctors-list');
	if(divSrc) {
		var html =
			'<div class="doctor-list">'+
				'<input type="checkbox" id="doctor-'+doctor_nr+'" name="toggled[]" value="'+doctor_nr+'" onclick="checkToggle(this.id,\''+doctor_nr+'\');" checked="checked"/>'+
				'<img src="<?=$root_path?>gui/img/common/default/'+bullet_color+'" align=""/>'+
				'<label for="doctor-'+doctor_nr+'">'+doctor_name+'</label>'+
			'</div>';
		divSrc.insert(html);
	}
}

function checkToggle(id, doctor_nr)
{
	if($(id).checked==false) {
		//alert("untoggle")
		unToggleDoctor('untoggle');
	} else {
		//alert("toggle")
		toggleDoctor('toggle');
	}
}

function toggleDoctor(mode)
{
	var toggles = document.getElementsByName('toggled[]');
	var doctors = [];
	for(i=0;i<toggles.length;i++)
	{
		if(toggles[i].checked==true) {
			doctors[i] = toggles[i].value;
		}
	}
	xajax_toggleDoctor(doctors, $('pid').value, mode);
}

function unToggleDoctor(mode)
{
	var toggles = document.getElementsByName('toggled[]');
	var doctors = [];
	for(i=0;i<toggles.length;i++)
	{
		if(toggles[i].checked==false) {
			doctors[i] = toggles[i].value;
		}
	}
	xajax_toggleDoctor(doctors, $('pid').value, mode);
}

$J(function() {
	var icons = {
			header: "ui-icon-circle-arrow-e",
			headerSelected: "ui-icon-circle-arrow-s"
		};

		$J("#accordion").accordion(
		{
			collapsible: true,
			icons: icons,
			animated: 'bounceslide'
			//event: 'mouseover'
		});

	});

document.observe('dom:loaded', function(){
	refreshList('all');
});
</script>
<?
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
$curDate = date($dbtime_format);
$curDate_show = date($fulltime_format);

$smarty->assign('form_start','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform">');
$smarty->assign('form_end','</form>');
$smarty->assign('pid', '<input type="hidden" id="pid" name="pid" value="2053462">');
$smarty->assign('doctor_nr', '<input type="hidden" id="doctor_nr" name="doctor_nr" value="">');

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
$smarty->assign('sMainBlockIncludeFile','clinics/soap_main.tpl');
$smarty->display('common/mainframe.tpl');

?>