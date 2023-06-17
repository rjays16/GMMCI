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
	//list gen for test service parameters
	ListGen.create( $('parameter-list'), {
		id: 'test_param',
		url: '../../../modules/laboratory/test_manager/ajax/ajax_list_parameters.php',
		params: {'service_code':$('service_code').value, 'group_id': '<?= $_GET["group_id"]?>'},
		width: 500,
		height: 230,
		autoLoad: true,
		columnModel: [
			{
				name: 'param_group_id',
				label: 'Group Id',
				width: 70,
				sortable: false
			},
			{
				name: 'param_name',
				label: 'Name',
				width: 100,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			{
				name: 'data_type',
				label: 'Data Type',
				width: 70,
				sortable: false
			},
			{
				name: 'order_nr',
				label: 'Order #',
				width: 70,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			{
				name: 'gender',
				label: 'Norm Type',
				width: 70,
				sortable: false
			},
			{
				name: 'si_range',
				label: 'SI Range',
				width: 70,
				sortable: false
			},
			{
				name: 'cu_range',
				label: 'CU Range',
				width: 70,
				sortable: false
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

function openParamsTray(mode, caption, service_code, param_id, grp_id, grp_name)
{
	var params="mode="+mode;

	if(mode=='new')
		params+="&service_code="+service_code+"&grp_id="+"<?= $_GET["group_id"]?>"+"&grp_name="+"<?= $_GET["group_name"]?>";
	if(mode=='edit')
		params+="&service_code="+service_code+'&param_id='+param_id+'&grp_id='+grp_id+'&grp_name='+grp_name;

	return overlib(
		OLiframeContent('../../../modules/laboratory/test_manager/seg_lab_test_params_tray.php?'+params,
		500, 300, 'fWizard', 0, 'no'),
		WIDTH,500, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="../../../images/close_red.gif" border=0 >',
		CAPTIONPADDING,2,DRAGGABLE,
		CAPTION, caption,
		MIDX,0, MIDY,0,
		STATUS, caption);
}

function openParamGroupTray()
{
	 return overlib(
		OLiframeContent('../../../modules/laboratory/test_manager/seg_lab_test_paramgroup_tray.php',
		500, 300, 'fWizard', 0, 'no'),
		WIDTH,500, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="../../../images/close_red.gif" border=0 >',
		CAPTIONPADDING,2,DRAGGABLE,
		CAPTION, 'Add Parameter Group',
		MIDX,0, MIDY,0,
		STATUS, 'Add Parameter Group');
}

function deleteParam(param_id)
{
	var ans = confirm("Delete this parameter?");
	if(ans)
	{
		xajax_deleteTestParameter(param_id, $('service_code').value);
	}else
	{
		return false;
	}
}

function openAssignTestGrpTray(service_code)
{
	return overlib(
		OLiframeContent('../../../modules/laboratory/test_manager/seg_lab_assign_testgroup_tray.php?service_code='+service_code,
		500, 250, 'fWizard', 0, 'no'),
		WIDTH,500, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="../../../images/close_red.gif" border=0 >',
		CAPTIONPADDING,2, DRAGGABLE,
		CAPTION, 'Assign Test Group',
		MIDX,0, MIDY,0,
		STATUS, 'Assign Test Group');
}

function outputResponse(rep)
{
	alert(rep);
	$('parameter-list').list.refresh();
}

function emptyParams(service_code)
{
	if(service_code)
	{
		var rep = confirm("Clear the parameter list?");
		if(rep)
		{
			xajax_emptyParameters(service_code);
		}
		else
		{
			return false;
		}
	}
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

$service_code = $_POST['service_code']? $_POST['service_code']:$_GET['service_code'];
$smarty->assign('service_code', '<input type="hidden" value="'.$service_code.'" id="service_code" name="service_code"/>');
$mode="new";
$caption="Add Service Parameter";
$smarty->assign('addParamBtn', '<button class="segButton" onclick="openParamsTray(\''.$mode.'\',\''.$caption.'\',\''.$service_code.'\');return false;"><img src="'.$root_path.'gui/img/common/default/application_form_add.png"/>Add Parameter</button>');
$smarty->assign('addParamGroupBtn', '<button class="segButton" onclick="openParamGroupTray();return false;"><img src="'.$root_path.'gui/img/common/default/book_add.png"/>Create Param Group</button>');
$smarty->assign('assignTestGrpBtn', '<button class="segButton" onclick="openAssignTestGrpTray(\''.$service_code.'\'); return false;"><img src="'.$root_path.'gui/img/common/default/application_double.png"/>Assign Test Group</button>');
$smarty->assign('clearParamBtn', '<button class="segButton" onclick="emptyParams(\''.$service_code.'\'); return false;"><img src="'.$root_path.'gui/img/common/default/database_delete.png"/>Empty Params</button>');
ob_start();

$bShowThisForm = TRUE;
$smarty->assign('sMainBlockIncludeFile','laboratory/test_manager/add_service_param_tray.tpl');
$sTemp = '';
$smarty->assign('sMainFrameBlockData',$sTemp);

$smarty->display('common/mainframe.tpl');
?>