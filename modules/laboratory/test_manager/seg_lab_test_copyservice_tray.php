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

function copyParams()
{
	if($('service_param').value!="0")
	{
		if($('service_param').value=="NP")
		{
			alert("No parameters yet.")
			$('service_param').focus();
			return false;
		}
		else if($('service_param').value==$('service_id').value)
		{
			alert("Same service.")
			$('service_param').focus();
			return false;
		}
		else
		{
			var txt = $('service_param').options[$('service_param').selectedIndex].text;
			var rep = confirm("Copy parameters of "+txt+"?")
			if(rep)
			{
				var paramIdObj = document.getElementsByName("copy_param_id[]");
				var paramOrderObj = document.getElementsByName("copy_param_order[]");
				var param_id = new Array();
				var param_order = new Array();
				for(i=0;i<paramIdObj.length;i++)
				{
					if(paramIdObj[i].checked){
						param_id[i] = paramIdObj[i].value;
						param_order[i] = paramOrderObj[i].value;
					}
				}
				xajax_copyParams($('service_id').value, param_id,  param_order);
			}
		}
	}
	else
	{
		alert('Please select a service.');
		$('service_param').focus();
		return false;
	}
	return false;
}

function undoCopy()
{
	var rep = confirm("Delete copied parameters?")
	if(rep)
	{
		xajax_undoCopyOfParams($('service_id').value, $('group_id').value);
	}
	return false;
}

function outputResponse(rep)
{
	//alert(rep);
	window.parent.cClick();
	window.parent.reload();
}

function showServiceParams(val) {
	if(val!="NP"){
		xajax_populateParamChecklist(val);
		$('view-param-list').style.display="";
	}
	else {
		$('view-param-list').style.display="none";
		return false;
	}
}

function printChecklist(details) {
	var table = $('param-list');
	if(table){
		var dBody = table.select("tbody")[0];
		if(dBody){
			var dRows = dBody.getElementsByTagName("tr");
			if(details.param_id){
				alt = (dRows.length%2>0) ? ' class="alt"':''
				//rowSrc = '<tr class="'+alt+'" id="item_row'+details.param_id+'">'+
				rowSrc = '<tr id="item_row'+details.param_id+'">'+
						'<td class="centerAlign">'+
							'<input type="checkbox" id="copy_param_id'+details.param_id+'" name="copy_param_id[]" value="'+details.param_id+'"/>'+
						'</td>'+
						'<td><span style="color:#660000">'+details.param_name+'</span></td>'+
						'<td class="centerAlign">'+
							'<span style="color:#660000">'+details.param_order+'</span>'+
							'<input type="hidden" id="copy_param_order'+details.param_id+'" name="copy_param_order[]" value="'+details.param_order+'"/>'+
						'</td>'+
						'<td><span style="color:#660000">'+details.param_group+'</span></td>'+
					'</tr>';
			}
			dBody.insert(rowSrc);
		}
	}
}

function checkAllParams(id) {
	if(id){
		var paramObj = document.getElementsByName("copy_param_id[]");
		for(i=0;i<paramObj.length;i++)
		{
			if($(id).checked==1) {
				paramObj[i].checked=1;
			}
			else {
				paramObj[i].checked=0;
			}
		}
	}
}
//document.observe('dom:loaded', init);
</script>
<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$smarty->assign('form_start','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="copyparam">');
$smarty->assign('form_end','</form>');

$group_id = $_POST["group_id"] ? $_POST["group_id"]:$_GET["group_id"];
$service_id = $_POST["service_code"] ? $_POST["service_code"]:$_GET["service_code"];

global $db;
$options = "<option value='0'>-Select service-</option>";
$sql = "SELECT g.service_code, s.name, IF(EXISTS(SELECT pa.param_id FROM seg_lab_result_param_assignment pa WHERE \n".
	"pa.service_code=g.service_code AND pa.status <>'deleted'),1,0) AS `has_params` FROM seg_lab_result_groupparams AS g \n".
	"LEFT JOIN seg_lab_services AS s ON g.service_code=s.service_code WHERE g.group_id=".$db->qstr($group_id);
$result = $db->Execute($sql);
while($row=$result->FetchRow())
{
	if($row["has_params"]==0)
	{
		$options.="<option value='NP'>".$row["name"]."(No parameters yet)</option>";
	}
	else
		$options.="<option value='".$row["service_code"]."'>".$row["name"]."</option>";
}
$smarty->assign('serviceWithParamList', '<select class="segInput" id="service_param" name="service_param" onchange="showServiceParams(this.value);">'.$options.'</select');

$smarty->assign('copyBtn','<button class="segButton" id="copy" onclick="copyParams();return false;"><img src="'.$root_path.'gui/img/common/default/accept.png"/>Copy</button>');
$smarty->assign('undoBtn','<button class="segButton" id="undo" onclick="undoCopy();return false;"><img src="'.$root_path.'gui/img/common/default/delete.png"/>Undo</button>');
$smarty->assign('cancelBtn','<button class="segButton" id="cancel" onclick="javascript:window.parent.cClick();return false;"><img src="'.$root_path.'gui/img/common/default/cancel.png"/>Cancel</button>');

$smarty->assign('group_id', '<input type="hidden" id="group_id" name="group_id" value="'.$group_id.'"/>');
$smarty->assign('service_id', '<input type="hidden" id="service_id" name="service_id" value="'.$service_id.'"/>');

ob_start();

$bShowThisForm = TRUE;
$smarty->assign('sMainBlockIncludeFile','laboratory/test_manager/copy_service_param_tray.tpl');
$sTemp = '';
$smarty->assign('sMainFrameBlockData',$sTemp);

$smarty->display('common/mainframe.tpl');
?>