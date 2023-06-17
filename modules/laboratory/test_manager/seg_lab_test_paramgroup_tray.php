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
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/laboratory/test_manager/js/test_mgr_main.js"></script>
<script type="text/javascript">
function init()
{
	ListGen.create( $('param-group-list'), {
		id: 'test_paramgrp',
		url: '../../../modules/laboratory/test_manager/ajax/ajax_list_paramgroups.php',
		params: {'search_key':$('param_grp_name').value},
		width: 470,
		height: 140,
		autoLoad: true,
		columnModel: [
			{
				name: 'paramgrp_name',
				label: 'Name',
				width: 200,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			{
				name: 'options',
				label: 'Options',
				width: 70,
				styles: {
					textAlign: 'center'
				},
				sortable: false
			}
		]
	});
}

function searchParamGrp()
{
	$('param-group-list').list.params = {'search_key':$('param_grp_name').value};
	$('param-group-list').list.refresh();
}

function saveParamGrp()
{
	if($('param_grp_name').value=="")
	{
		alert("Please fill in group name.");
		$('param_grp_name').focus();
	}
	else
	{
		var rep = confirm("Add new group parameter?");
		if(rep)
		{
			xajax_saveParamGroup($('param_grp_name').value);
		}
	}
}

function deleteParamGrp(id)
{
	var rep = confirm("Delete group parameter?");
	if(rep)
	{
		xajax_deleteParamGroup(id);
	}else{
		return false;
	}
}

function openUpdateParamGrp(id, name)
{
	var html =
			'<div style="margin-top:10px">'+
				'<table class="segPanel" align="center" cellpadding="2" cellspacing="2" border="0" width="100%">'+
					'<tbody>'+
						'<tr>'+
							'<td align="left"><b>Group Name :</b></td>'+
							'<td><input type="text" class="segInput" id="new_grp_name" name="new_grp_name" style="width:140px" value="'+name+'" onfocus="OLmEdit=1;" onblur="OLmEdit=0;"/></td>'+
						'</tr>'+
						'<tr>'+
							'<td colspan="2"><button class="segButton" onclick="updateParamGrp(\''+id+'\');return false;"><img src="<?=$root_path;?>gui/img/common/default/monitor_edit.png"/>Update</button></td>'+
						'</tr>'+
					'</tbody>'+
				'</table>'+
			'</div>';
	return overlib(
		html,	WIDTH,300, HEIGHT,90, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="../../../images/close_red.gif" border=0 >',
		CAPTIONPADDING,2,
		CAPTION, 'Update Parameter Group',
		MIDX,0, MIDY,0,
		STATUS, 'Update Parameter Group');
}

function updateParamGrp(id)
{
	if($('new_grp_name').value=="")
	{
		alert("Please fill in group name.");
		$('new_grp_name').focus();
	}else
	{
		var rep = confirm("Update group parameter?");
		if(rep)
		{
			xajax_updateParamGroup(id,$('new_grp_name').value);
		}
	}
}

function outputResponse(rep)
{
	alert(rep);
	$('param-group-list').list.refresh();
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

$smarty->assign('saveParamGrp', '<button class="segButton" onclick="saveParamGrp();return false;"><img src="'.$root_path.'gui/img/common/default/database_save.png"/>Add Group</button>');
$smarty->assign('searchParamGrp', '<button class="segButton" onclick="searchParamGrp();return false;"><img src="'.$root_path.'gui/img/common/default/magnifier.png"/>Search</button>');
ob_start();

$bShowThisForm = TRUE;
$smarty->assign('sMainBlockIncludeFile','laboratory/test_manager/add_group_param_tray.tpl');
$sTemp = '';
$smarty->assign('sMainFrameBlockData',$sTemp);

$smarty->display('common/mainframe.tpl');
?>