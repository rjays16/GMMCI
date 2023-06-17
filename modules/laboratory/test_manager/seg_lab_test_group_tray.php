<?php
#created by cha, june 20,2010
#manager for laboratory tests - new test group

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

$smarty->assign('sToolbarTitle',"Tests Manager::New Test Group");
$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

global $db;
if($_POST['submitted']){
	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
	if($_POST['mode']=='new')
	{

	}
}

$forms_options='<option value="0">Select Special Form</option>';
$lab_res = $lab_obj->getSpecialForms();
while($row= $lab_res->Fetchrow()){
	$forms_options.="<option value='".$row['form_id']."'>".strtoupper($row['form_name'])."</option>";
}

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
	if($('mode')){
		var mode = $('mode').value;
		if(mode=="edit"){
			$('update_button').style.display='';
			xajax_populateTestGroup($('group_id').value, '<?=$_GET['group_name']?>');
		}else
		{
			$('save_button').style.display='';
			xajax_newGroupId();
		}
	}
}

function openServicesTray()
{
	if($('new_group').value=='')
 {
	 alert('Please fill in group name.');
	 $('new_group').focus();
	 return false;
 }else{
	if($('group_id'))
	{
		var grp_id = $('group_id').value;
		var grp_name = $('new_group').value;
	}
	return overlib(
		OLiframeContent('../../../modules/laboratory/test_manager/seg_lab_test_request_tray.php?group_id='+grp_id+'&group_name='+grp_name,
		550, 350, 'fWizard', 0, 'no'),
		WIDTH,550, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="../../../images/close_red.gif" border=0 >',
		CAPTIONPADDING,2,
		CAPTION,'Add Service Item',
		MIDX,0, MIDY,0,
		STATUS,'Add Service Item');
 }
}

function emptyServicesTray()
{
	var table = $('grp-service-list').getElementsByTagName('tbody').item(0);
	table.innerHTML = '<tr id="empty_list"><td colspan="5">No services on the list..</td></tr>';
}

function cancel()
{
	if($('mode').value=="new")
		window.parent.cClick();
	if($('mode').value=="edit")
		deleteGroup($('group_id').value)
}

function deleteGroup(id)
{
	var reply = confirm("Delete this laboratory group test?");
	if(reply)
	{
		xajax_deleteTestGroup(id);
	}else
	{
		return false;
	}
}

function saveGroup()
{
 if($('new_group').value=='')
 {
	 alert('Please fill in group name.');
	 $('new_group').focus();
	 return false;
 }else
 {
	 var services = document.getElementsByName('item_code[]');
	 var order_nr = document.getElementsByName('item_order[]');
	 var srv_codes = new Array();
	 var order = new Array();
	 for(i=0;i<services.length;i++)
	 {
		 srv_codes[i] = services[i].value;
		 order[i] = order_nr[i].value;
	 }

	 if($('mode').value=="new")
		xajax_saveTestGroup($('new_group').value, srv_codes, order, $('special_form').value);
	 if($('mode').value=="edit")
		xajax_updateTestGroup($('group_id').value, $('new_group').value, srv_codes, order, $('special_form').value);
	 return false;
 }
}

function remove_item(id)
{
	var rep = confirm("Delete from list?");
	if(rep)
	{
		var table = $('grp-service-list').getElementsByTagName('tbody').item(0);
		table.removeChild($('item_row'+id));
		if (!document.getElementsByName('item_code[]') || document.getElementsByName('item_code[]').length <= 0) {
			var table1 = $('grp-service-list').getElementsByTagName('tbody').item(0);
			var row = document.createElement("tr");
			var cell = document.createElement("td");
			row.id = "empty_list";
			cell.appendChild(document.createTextNode('No services on the list..'));

			cell.colSpan = "5";
			row.appendChild(cell);
			$('grp-service-list').getElementsByTagName('tbody').item(0).appendChild(row);
		}
	}
	return false;
}

function outputResponse(rep)
{
	alert(rep);
	window.parent.$('test_grp_list').list.refresh();
	window.parent.$('test_srv_list').list.refresh();
	window.parent.cClick();
}

function listGroupServices(code, name, order_no, has_params, grp_id, grp_name)
{
	var table = $('grp-service-list');
	if(table){
		var dBody = table.select("tbody")[0];
		if ($('empty_list')) {
			table.getElementsByTagName('tbody').item(0).removeChild($('empty_list'));
		}
		if(dBody){
			var dRows = dBody.getElementsByTagName("tr");
			var order = 1;
			if($('order_cnt'))
			{
				$('order_cnt').value=parseInt($('order_cnt').value)+1;
				order = $('order_cnt').value;
			}
			if(code){
				var edit_txt = "Edit Parameters";
				var del_txt = "Remove Service";
				var copy_txt = "Copy Parameters";
				alt = (dRows.length%2>0) ? ' class="alt"':''
				rowSrc = '<tr class="'+alt+'" id="item_row'+code+'">'+
						'<td class="centerAlign">'+
							'<span style="color:#660000">'+code+'</span>'+
							'<input type="hidden" name="item_code[]" id="item_code'+code+'" value="'+code+'"/>'+
						'</td>'+
						'<td><span style="color:#660000">'+name+'</span></td>'+
						'<td class="centerAlign">'+
							'<input type="text" class="segInput" name="item_order[]" id="item_order'+code+'" value="'+order+'" style="width:60%;text-align:right"/>'+
						'</td>'+
						'<td>'+
							'&nbsp;&nbsp;<img src="../../../images/cashier_edit_3.gif" name="edit" class="link" onclick="openAddParamTray(\''+code+'\',\''+grp_id+'\',\''+grp_name+'\');return false;" onmouseover="tooltip(\''+edit_txt+'\')" onmouseout="nd();"/>';

						if(has_params=="1")
						{
							rowSrc+=
							'&nbsp;<img src="../../../images/cashier_edit.gif" name="edit" class="link" disabled="" onmouseover="tooltip(\''+copy_txt+'\')" onmouseout="nd();" style="opacity:0.5"/>';
						}else {
							rowSrc+=
							'&nbsp;<img src="../../../images/cashier_edit.gif" name="edit" class="link" onclick="openCopyParamTray(\''+code+'\',\''+grp_id+'\',\''+grp_name+'\');return false;" onmouseover="tooltip(\''+copy_txt+'\')" onmouseout="nd();"/>';
						}

						rowSrc+=
							'&nbsp;<img class="segSimulatedLink" src="../../../images/cashier_delete_small.gif" border="0" onclick="remove_item(\''+code+'\');return false;" onmouseover="tooltip(\''+del_txt+'\')" onmouseout="nd();"/>'+
						'</td>'+
					'</tr>';
			}else
			{
				rowSrc = '<tr id="empty_list"><td colspan="5">No services on the list..</td></tr>';
			}
			dBody.insert(rowSrc);
		}
	}
}

function openCopyParamTray(code, grp_id, grp_name)
{
	return overlib(
		OLiframeContent('../../../modules/laboratory/test_manager/seg_lab_test_copyservice_tray.php?service_code='+code+'&group_id='+grp_id,
		//450, 130, 'fWizard', 0, 'no'),
		450, 350, 'fWizard', 0, 'no'),
		WIDTH,450, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="../../../images/close_red.gif" border=0 >',
		CAPTIONPADDING,2,DRAGGABLE,
		CAPTION, 'Copy Service Parameter',
		MIDX,0, MIDY,0,
		STATUS, 'Copy Service Parameter');
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

$smarty->assign('groupName', '<input type="text" id="new_group" name="new_group" value="" class="segInput" style="width:200px"/>');
$smarty->assign('addItems', '<button class="segButton" onclick="openServicesTray();return false;"><img src="'.$root_path.'gui/img/common/default/cart_add.png" />Add Items</button>');
$smarty->assign('clearItems','<button class="segButton" onclick="emptyServicesTray();return false;"><img src="'.$root_path.'gui/img/common/default/cart_remove.png" />Clear Items</button>');
$smarty->assign('saveGroup','<button class="segButton" id="save_button" onclick="saveGroup();return false;" style="display:none"><img src="'.$root_path.'gui/img/common/default/cart_put.png"/>Save Group</button>');
$smarty->assign('updateGroup','<button class="segButton" id="update_button" onclick="saveGroup();return false;" style="display:none"><img src="'.$root_path.'gui/img/common/default/cart_put.png"/>Update Group</button>');
$smarty->assign('specialForms', '<select id="special_form" class="segInput" name="special_form">'.$forms_options.'</select>');
//$smarty->assign('deleteGrp','<button class="segButton" onclick="cancel();return false;"><img src="'.$root_path.'gui/img/common/default/cancel.png"/>Delete Group</button>');
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$mode = $_POST['mode']? $_POST['mode']:$_GET['mode'];
$smarty->assign('mode', '<input type="hidden" value="'.$mode.'" id="mode" name="mode"/>');
$group_id = $_POST['group_id']? $_POST['group_id']:$_GET['group_id'];
$smarty->assign('group_id', '<input type="hidden" value="'.$group_id.'" id="group_id" name="group_id"/>');
ob_start();

$bShowThisForm = TRUE;
$smarty->assign('sMainBlockIncludeFile','laboratory/test_manager/add_group_tray.tpl');
$sTemp = '';
$smarty->assign('sMainFrameBlockData',$sTemp);

$smarty->display('common/mainframe.tpl');
?>