<?php
//created by cha Feb 5, 2010

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('NO_CHAIN',1);
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path.'modules/clinics/labor.php'.URL_APPEND;
$returnfile=$root_path.'modules/clinics/labor.php'.URL_APPEND;

$thisfile=basename(__FILE__);

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$thisfile='viewDrNotes.php';

//initialize smarty
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Toolbar title
$smarty->assign('sToolbarTitle','Prescription Writer :: Edit prescription');

# href for the return button
$smarty->assign('pbBack',$returnfile);

# href for the  button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# Window bar title
$smarty->assign('sWindowTitle',"Prescription Writer :: Edit prescription");

$smarty->assign('bHideTitleBar', TRUE);
$smarty->assign('bHideCopyright', TRUE);
$smarty->assign('breakFile',$breakfile);

ob_start();
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>
<script type="text/javascript">


//document.observe('dom:loaded', initialize);
</script>

<?
$sTemp = ob_get_contents();
$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
$curDate = date($dbtime_format);
$curDate_show = date($fulltime_format);
global $db;

$sql = "SELECT chief_complaint, physical_examination, clinical_summary \n".
						"FROM seg_doctors_notes WHERE encounter_nr=".$db->qstr($_GET['encounter_nr']);
$data = $db->GetRow($sql);
$smarty->assign('chief_complaint', $data["chief_complaint"]);
$smarty->assign('physical_examination', $data["physical_examination"]);
$smarty->assign('clinical_summary', $data["clinical_summary"]);

$sql = "SELECT d.icd_code, c.description FROM seg_doctors_diagnosis AS d \n".
"INNER JOIN care_icd10_en AS c ON d.icd_code=c.diagnosis_code \n".
"WHERE d.encounter_nr=".$db->qstr($_GET["encounter_nr"]);
$data = $db->Execute($sql);
$text = "";
while($row=$data->FetchRow())
{
	$text.= $row["icd_code"]."&nbsp;&nbsp;".$row["description"]."\n";
}
$smarty->assign('diagnosis', $text);

/**
* show Template
*/
# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile',$root_path.'modules/dashboard/dashlets/PatientHistory/templates/NotesView.tpl');
$smarty->display('common/mainframe.tpl');

?>