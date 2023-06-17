<?php
#created by CHA 05-12-2010
#Manage anesthesia procedures
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_core.php');
require($root_path."modules/or/ajax/ajax_anesthesia_gui.common.php");
$xajax->printJavascript($root_path.'classes/xajax_0.5');

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);

#$breakfile = "labor.php";
$breakfile="javascript:window.parent.reload();javascript:window.parent.cClick();";
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

$LDTitleMgr = "OR::Anesthesia Procedure Manager";
$smarty->assign('sToolbarTitle',"$LDTitleMgr");
$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDTitleMgr");

 $smarty->assign('sOnLoadJs','onLoad="preSet(); "');
 # Collect javascript code
 ob_start();

?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css" />
<script type="text/javascript" src="<?= $root_path ?>js/listgen/listgen.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="<?=$root_path?>js/listgen/css/default/default.css"></link>
<script type="text/javascript" src="<?=$root_path?>modules/or/js/op-anesthesia-mgr.js"></script>
<script language="javascript" >
function preSet()
{
		var id = '<?echo $_GET['cat_id'];?>';
		var mode = '<?echo $_GET['mode'];?>';
		if(mode=="edit")
		{
			ListGen.create( $('anesthesia_specific_list'), {
				id:'anesthesia_specific_list',
				url:'../../../modules/or/ajax/ajax_list_anesthesia_specific.php',
				params: {id:id},
				width: 510,
				height: 120,
				autoLoad: true,
				columnModel: [
					{
						name: 'id',
						label: 'Specific Id',
						width: 100,
						sorting: ListGen.SORTING.asc,
						sortable: true
						/*styles: {
							color: '#000066',
							font: 'bold 11px Tahoma'
						},
						render: function(data,i) {
							if (data[i]['id'] == 'combined')
								return '<input id="ds"';
							else
								return data[i]['id'];
						}*/
					},
					{
						name: 'name',
						label: 'Specific Name',
						width: 200,
						sorting: ListGen.SORTING.asc,
						sortable: true
					},
					{
						name: 'options',
						label: 'Options',
						width: 100,
						sorting: ListGen.SORTING.none,
						sortable: false
					}
				]
			}
		);
	}
	else if(mode=="new")
	{
		$('anesthesia_specific_table').style.display = "";
	}
}

//-----------start CELSY--------------
function open_delete_anesth_spec(spec_id, spec_name)
{
	var reply = confirm("Are you sure you want to delete this item?");
		if(reply)
		{
			delete_specific(spec_id, spec_name);
		}
}

 function open_edit_anesth_spec(spec_id, spec_name)
 {
	 var popup_html = '<table border="0" width="100%" class="Search">'+
		'<tbody>'+
			'<tr>'+
				'<td class="segPanel">'+
					'<table border="0" width="100%" class="Search" style="font: 12px Arial;">'+
						'<tbody>'+
							'<tr>'+
								'<td style="white-space:nowrap;width:130px"><label><b>Category id:</b></label></td>'+
								'<td align="left" valign="middle">'+
									'<input class="segInput" type="text" id="new_specific_id" name="new_specific_id" style="width:187px" value="'+spec_id+'" onfocus="OLmEdit=1;" onblur="OLmEdit=0;"/>'+
								'</td>'+
							'</tr>'+
							'<tr>'+
								'<td style="white-space:nowrap;width:130px"><label><b>Category Name:</b></label></td>'+
								'<td align="left" valign="middle">'+
									'<input class="segInput" type="text" id="new_specific_name" name="new_specific_name" style="width:187px" value="'+spec_name+'" onfocus="OLmEdit=1;" onblur="OLmEdit=0;"/>'+
								'</td>'+
								'<td align="right" valign="bottom">'+
									'<button class="segButton" onclick="edit_specific_data(\''+spec_id+'\', \''+spec_name+'\');">Save</button>'+
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
			WIDTH,300, HEIGHT,60, TEXTPADDING,0, CAPTIONPADDING,2,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
			CAPTIONPADDING,2,
			CAPTION,'Edit sepecific anesthesia',
			MIDX,0, MIDY,0,
			STATUS,'Edit specific anesthesia'
		);
 }

 //-----------end CELSY--------------
</script>


<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform">');
$smarty->assign('sFormEnd','</form>');

//$smarty->assign('categoryName', '<input class="segInput" type="text" id="category_name" name="category_name" size="25" onfocus="OLmEdit=1" onblur="OLmEdit=0" value="'.$_GET['cat_name'].'"/>');
if($_GET['mode']=="edit")
{
	$smarty->assign('categoryId', '<input class="segInput" type="text" id="category_id" name="category_id" size="15" readonly="" value="'.$_GET['cat_id'].'"/>');
	$smarty->assign('categoryName', '<input class="segInput" type="text" id="category_name" name="category_name" size="25" value="'.$_GET['cat_name'].'"/>');
	$smarty->assign('specificName', '<input class="segInput" type="text" id="specific_name" name="specific_id" size="15" onfocus="OLmEdit=1" onblur="OLmEdit=0"/>');
	$smarty->assign('specificId', '<input class="segInput" type="text" id="specific_id" name="specific_name" size="25" onfocus="OLmEdit=1" onblur="OLmEdit=0"/>');
	$smarty->assign('addSpecific', '<input class="segButton" type="button" name="add_specific" value="Add" onclick="add_specific_anesthesia(\''.$_GET['mode'].'\');"/>');
	$smarty->assign('saveBtn', '<input class="segButton" type="button" name="save_specific" value="Save" onclick="update_procedure(\''.$_GET['mode'].'\');"/>');
	$smarty->assign('cancelBtn', '<input class="segButton" type="button" name="cancel" value="Cancel" onclick="javascript:window.parent.location.reload();"/>');
}
else{
	$smarty->assign('categoryId', '<input class="segInput" type="text" id="category_id" name="category_id" size="15" value=""/>');
	$smarty->assign('categoryName', '<input class="segInput" type="text" id="category_name" name="category_name" size="25" value=""/>');
	$smarty->assign('specificName', '<input class="segInput" type="text" id="specific_name" name="specific_id" size="15" onfocus="OLmEdit=1" onblur="OLmEdit=0"/>');
	$smarty->assign('specificId', '<input class="segInput" type="text" id="specific_id" name="specific_name" size="25" onfocus="OLmEdit=1" onblur="OLmEdit=0"/>');
	$smarty->assign('addSpecific', '<input class="segButton" type="button" name="add_specific" value="Add" onclick="add_specific_anesthesia(\''.$_GET['mode'].'\');"/>');
	$mode="new";
	$smarty->assign('saveBtn', '<input class="segButton" type="button" name="save_specific" value="Save" onclick="if(validate(\''.$mode.'\') && \''.$_GET['mode'].'\'==\''.$mode.'\'){save_new_procedure();} return update_procedure(\''.$_GET['mode'].'\');"/>');
	$smarty->assign('cancelBtn', '<input class="segButton" type="button" name="cancel" value="Cancel" onclick="javascript:window.parent.location.reload();"/>');
}
ob_start();

# Workaround to force display of results  form
$bShowThisForm = TRUE;

# If smarty object is not available create one
if(!isset($smarty)){
	/**
 * LOAD Smarty
 * param 2 = FALSE = dont initialize
 * param 3 = FALSE = show no copyright
 * param 4 = FALSE = load no javascript code
 */
	include_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common',FALSE,FALSE,FALSE);

	# Set a flag to display this page as standalone
	$bShowThisForm=TRUE;
}

# Assign the form template to mainframe
 $smarty->assign('sMainBlockIncludeFile','or/anesthesia/tray_gui.tpl');
 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>

