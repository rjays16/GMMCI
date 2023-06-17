<?php
#created by cha, june 20,2010
#manager for laboratory tests - new test service parameters

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_core.php');

require_once($root_path.'modules/laboratory/test_manager/ajax/seg_lab_test.common.php');

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_lab_user';

require_once($root_path.'include/inc_front_chain_lang.php');
$thisfile=basename(__FILE__);

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

require_once($root_path.'include/care_api_classes/class_lab_results.php');
$lab_obj = new Lab_Results();

$smarty->assign('sToolbarTitle',"Tests Manager::New Test Service Paramater");
$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

ob_start();
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript">
function init()
{
	ListGen.create( $('test-group-list'), {
		id: 'test_grp_assigned',
		url: '../../../modules/laboratory/test_manager/ajax/ajax_list_assigned_testgroups.php',
		params: {'code':$('service_code').value},
		width: 400,
		height: 140,
		autoLoad: true,
		columnModel: [
			{
				name: 'testgrp_name',
				label: 'Name',
				width: 200,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			{
				name: 'options',
				label: 'Options',
				width: 50,
				styles: {
					textAlign: 'center'
				},
				sortable: false
			}
		]
	});
}


function assignTestGrp()
{
	if($('test_group').value=="0")
	{
		alert("Please select a test group.")
		$('test_group').focus();
		return false;
	}else
	{
		var rep = confirm("Assign test group to "+$('service_code').value+"?");
		if(rep)
		{
			xajax_addTestGrpAssignment($('test_group').value, $('service_code').value);
		}else
		{
			return false;
		}
	}
}

function removeGrpAssignment(grp_id)
{
	var rep = confirm("Remove assigned Test Group?");
	if(rep)
	{
		xajax_removeGrpAssignment(grp_id, $('service_code').value);
	}else
	{
		return false;
	}
}

function outputResponse(rep)
{
	alert(rep);
	$('test-group-list').list.refresh();
}

document.observe('dom:loaded', init);
</script>
<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$smarty->assign('form_start','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="addgroup">');
$smarty->assign('form_end','</form>');

global $db;
$data = $db->Execute("SELECT group_id, name FROM seg_lab_result_groupname WHERE status <> 'deleted' ORDER BY name ASC");
$options="<option value='0'>-Select test group-</option>";
while($row=$data->FetchRow())
{
	$options.="<option value='".$row["group_id"]."'>".$row["name"]."</option>";
}
$smarty->assign('selectTestGrp', '<select class="segInput" id="test_group" name="test_group">'.$options.'</select>');
$smarty->assign('assignTestGrp', '<button class="segButton" onclick="assignTestGrp();return false;"><img src="'.$root_path.'gui/img/common/default/add.png"/>Assign</button>');

$service_code = $_POST['service_code']? $_POST['service_code']:$_GET['service_code'];
$smarty->assign('service_code', '<input type="hidden" value="'.$service_code.'" id="service_code" name="service_code"/>');
ob_start();

$bShowThisForm = TRUE;
$smarty->assign('sMainBlockIncludeFile','laboratory/test_manager/assign_test_group_tray.tpl');
$sTemp = '';
$smarty->assign('sMainFrameBlockData',$sTemp);

$smarty->display('common/mainframe.tpl');
?>