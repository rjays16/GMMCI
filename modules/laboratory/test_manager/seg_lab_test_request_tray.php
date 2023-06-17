<?php
#created by cha, june 20,2010
#manager for laboratory tests - add service items

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

$smarty->assign('sToolbarTitle',"Tests Manager::Add Service Item");
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
<script type="text/javascript" src="<?=$root_path?>modules/laboratory/test_manager/js/test_mgr_main.js"></script>
<script type="text/javascript">
 function initialize()
 {
	 ListGen.create( $('service_list'), {
		id: 'lab_service',
		url: '../../../modules/laboratory/test_manager/ajax/ajax_list_services.php',
		params:
		{
			'search_service':$('search').value,
			'mode':'add_service',
			'search_section':$('srv_group').value,
			'grp_id': '<?=$_GET['group_id']?>',
			'grp_name': '<?=$_GET['group_name']?>'
		},
		width: 535,
		height: 220,
		autoLoad: true,
		columnModel: [
			{
				name: 'srv_code',
				label: 'Service Code',
				width: 100,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			{
				name: 'srv_name',
				label: 'Test Service',
				width: 200,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			/*{
				name: 'srv_stat_grp',
				label: 'Has Group Assigned',
				width: 120,
				//sorting: ListGen.SORTING.asc,
				sortable: false,
				styles:{
					textAlign: 'center',
					color: '#DD0000'
				}
			},*/
			{
				name: 'srv_stat_param',
				label: 'Has Parameter',
				width: 100,
				//sorting: ListGen.SORTING.asc,
				sortable: false,
				styles:{
					textAlign: 'center',
					color: '#DD0000'
				}
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

 function searchService()
 {
	$('service_list').list.params=
	{
		'search_service':$('search').value,
		'mode':'add_service',
		'search_section':$('srv_group').value,
		'grp_id': '<?=$_GET['group_id']?>',
		'grp_name': '<?=$_GET['group_name']?>'
	};
	$('service_list').list.refresh();
 }

 function appendItem(code,name, group_id, group_name)
 {
	if (window.parent.$('item_row'+code)) {
		alert('This item is already in the list.');
		return false;
	}
	else
	{
		var table = window.parent.$('grp-service-list');
		if(table){
			var dBody = table.select("tbody")[0];
			if (window.parent.$('empty_list')) {
				table.getElementsByTagName('tbody').item(0).removeChild(window.parent.$('empty_list'));
			}
			if(dBody){
				var dRows = dBody.getElementsByTagName("tr");
				var order = 1;
				if(window.parent.$('order_cnt'))
				{
					window.parent.$('order_cnt').value=parseInt(window.parent.$('order_cnt').value)+1;
					order = window.parent.$('order_cnt').value;
				}
				if(code){
					var edit_txt = "Edit Parameters";
					var del_txt = "Remove Service";
					var copy_txt = "Copy Parameters";
					alt = (dRows.length%2>0) ? ' class="alt"':''
					rowSrc = '<tr class="'+alt+'" id="item_row'+code+'">'+
							//'<td style="height:30px" class="centerAlign"><img class="segSimulatedLink" src="../../../images/close_small.gif" border="0" onclick="remove_item(\''+code+'\')"/></td>'+
							'<td class="centerAlign">'+
								'<span style="color:#660000">'+code+'</span>'+
								'<input type="hidden" name="item_code[]" id="item_code'+code+'" value="'+code+'"/>'+
							'</td>'+
							'<td><span style="color:#660000">'+name+'</span></td>'+
							'<td class="centerAlign">'+
								'<input type="text" class="segInput" name="item_order[]" id="item_order'+code+'" value="'+order+'" style="width:60%;text-align:right"/>'+
							'</td>'+
							'<td>'+
								//'&nbsp;&nbsp;<img src="../../../images/cashier_edit.gif" name="edit" class="link" onclick="openAddParamTray(\''+code+'\',\''+group_id+'\',\''+group_name+'\');return false;" onmouseover="tooltip(\''+edit_txt+'\')" onmouseout="nd();"/>'+
								//'&nbsp;<img class="segSimulatedLink" src="../../../images/close_small.gif" border="0" onclick="remove_item(\''+code+'\');return false;" onmouseover="tooltip(\''+del_txt+'\')" onmouseout="nd();"/>'+
								'&nbsp;&nbsp;<img src="../../../images/cashier_edit_3.gif" name="edit" class="link" onclick="openAddParamTray(\''+code+'\');return false;" onmouseover="tooltip(\''+edit_txt+'\')" onmouseout="nd();"/>'+
								'&nbsp;<img src="../../../images/cashier_edit.gif" name="edit" class="link" onclick="openCopyParamTray(\''+code+'\');return false;" onmouseover="tooltip(\''+copy_txt+'\')" onmouseout="nd();"/>'+
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
 }

 document.observe('dom:loaded', initialize);
</script>
<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$smarty->assign('form_start','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="addgroup">');
$smarty->assign('form_end','</form>');

global $db;
$all_labgrp = $db->Execute("SELECT group_code, name FROM seg_lab_service_groups WHERE status NOT IN ('deleted') ORDER BY name ASC");
if(!empty($all_labgrp)&&$all_labgrp->RecordCount())
{
	$options = '<option value="0">All Laboratory Service Section</option>';
	while($result=$all_labgrp->FetchRow())
	{
		$options.="<option value=\"".$result['group_code']."\">".$result['name']." \n";
	}
}
$smarty->assign('labSections', '<select name="srv_group" id="srv_group" class="segInput" onchange="searchService();">'.$options.'</select>');
$smarty->assign('labSearchInput', '<input id="search" name="search" class="segInput" type="text" style="width:50%; '.
	'margin-left:10px; font: bold 12px Arial" align="absmiddle" />');
$smarty->assign('labSearchBtn', '<button class="segButton" onclick="searchService();return false;">'.
	'<img src="'.$root_path.'gui/img/common/default/magnifier.png"/>Search</button>');
ob_start();

$bShowThisForm = TRUE;
$smarty->assign('sMainBlockIncludeFile','laboratory/test_manager/add_request_tray.tpl');
$sTemp = '';
$smarty->assign('sMainFrameBlockData',$sTemp);

$smarty->display('common/mainframe.tpl');
?>