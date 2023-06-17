<?php
#created by cha, june 16,2010
#manager for sponsors

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_core.php');

require($root_path.'modules/sponsor/ajax/cmap_account.common.php');

define('NO_2LEVEL_CHK', 1);
define('LANG_FILE', 'products.php');

$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
$thisfile=basename(__FILE__);

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

$smarty->assign('sToolbarTitle',"CMAP::Accounts Manager");
$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','CMAP::Accounts Manager')");
$smarty->assign('sWindowTitle',"CMAP::Accounts Manager");
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

function initialize()
{
	ListGen.create( $('sponsor_account_list'), {
		id:'cmap_account',
		url:'<?echo $root_path;?>modules/sponsor/ajax/ajax_list_accounts.php',
		width: 590,
		height: 165,
		autoLoad: true,
		columnModel: [
			{
				name: 'account_name',
				label: 'Name',
				width: 130,
				sorting: ListGen.SORTING.asc,
				sortable: true
			},
			{
				name: 'account_address',
				label: 'Address',
				width: 160,
				sortable: false
			},
			{
				name: 'running_balance',
				label: 'Running Balance',
				width: 130,
				sorting: ListGen.SORTING.asc,
				styles: {
					textAlign: 'center'
				},
				sortable: true
			},
			{
				name: 'options',
				label: 'Options',
				width: 100,
				styles: {
					textAlign: 'center'
				},
				sortable: false
			}
		]
	});
}

function delete_account(id){
	var rep = confirm("Delete this account?");
	if(rep){
		xajax_deleteAccount(id);
	}else{
		return false;
	}
}

function edit_account(id, name, address){
	var popup_html = '<table border="0" width="100%" class="Search">'+
		'<tbody>'+
			'<tr>'+
				'<td class="segPanel">'+
					'<table border="0" width="100%" class="Search" style="font: 12px Arial;">'+
						'<tbody>'+
							'<tr>'+
								'<td style="white-space:nowrap;width:130px"><label><b>Name:</b></label></td>'+
								'<td align="left" valign="middle">'+
									'<input class="segInput" type="text" id="update_sponsor_name" name="update_sponsor_name" style="width:187px" value="'+name+'" onfocus="OLmEdit=1;" onblur="OLmEdit=0;"/>'+
									'<input type="hidden" id="update_nr" name="update_nr" value="'+id+'"/>'+
								'</td>'+
							'</tr>'+
							'<tr>'+
								'<td style="white-space:nowrap;width:130px"><label><b>Address:</b></label></td>'+
								'<td align="left" valign="middle">'+
									'<textarea class="segInput" id="update_sponsor_address" name="update_sponsor_address" cols="28" rows="3" onfocus="OLmEdit=1;" onblur="OLmEdit=0;">'+address+'</textarea>'+
								'</td>'+
								'<td align="right" valign="bottom">'+
									'<button class="segButton" onclick="updateAccount();return false;"><img src="../../gui/img/common/default/building_edit.png"/>Update</button>'+
								'</td>'+
							'</tr>'+
						'</tbody>'+
					'</table>'+
				'</td>'+
			'</tr>'+
		'</tbody>'+
	'</table>';

	return overlib(
		 popup_html,
		WIDTH,320, HEIGHT, 100, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
		CAPTIONPADDING,2,
		CAPTION,'Update Account Information',
		MIDX,0, MIDY,0,
		STATUS,'Update Account Informa');
}

function showResponse(rep, error){
	if(error){
		alert(rep+" ERROR:"+error)
	}else{
		alert(rep)
	}
	window.location.reload();
}

function saveAccount(){
	var name = $('cmap_account_name').value;
	var address = $('cmap_account_address').value;
	if(name=="")
	{
		alert("No account name.")
		return false;
	}
	var rep = confirm("Save this account?");
	if(rep){
		xajax_saveAccount(name,address);
	}else{
		return false;
	}
}

function updateAccount(){
	var name = $('update_sponsor_name').value;
	var address = $('update_sponsor_address').value;
	var id = $('update_nr').value;
	var rep = confirm("Update this account?");
	if(rep){
		xajax_updateAccount(id, name,address);
	}else{
		return false;
	}
}

document.observe('dom:loaded', initialize);
</script>
<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform">');
$smarty->assign('sFormEnd','</form>');

$smarty->assign('accountName', '<input type="text" class="segInput" id="cmap_account_name" name="cmap_account_name" style="width:225px"/>');
$smarty->assign('accountAddress', '<textarea class="segInput" id="cmap_account_address" name="cmap_account_address" style="width:225px"></textarea>');
$smarty->assign('addBtn', '<button class="segButton" onclick="saveAccount();return false;"><img src="'.$root_path.'gui/img/common/default/award_star_gold_2.png"/>Add Sponsor</button>');

ob_start();

$bShowThisForm = TRUE;
$smarty->assign('sMainBlockIncludeFile','sponsor/cmap_accounts_mgr.tpl');
$sTemp = '';
$smarty->assign('sMainFrameBlockData',$sTemp);

$smarty->display('common/mainframe.tpl');
?>