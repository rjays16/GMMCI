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
$breakfile=$root_path.'main/op-doku.php'.URL_APPEND;

$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

$LDTitleMgr = "OR::Anesthesia Procedure Manager";
$smarty->assign('sToolbarTitle',"$LDTitleMgr");

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
 ob_start()

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
 <script type="text/javascript" src="js/anesthesia_mgr.js?t=<?=time()?>"></script>


<script language="javascript" >
function preSet()
{
	ListGen.create( $('anesthesia_list'), {
			id:'anesthesia_mgr',
			url:'../../../modules/or/ajax/ajax_list_anesthesia.php',
			params: {'search': $('search_key').value},
			width: 'auto',
			height: 'auto',
			autoLoad: true,
			columnModel: [
				{
					name: 'name',
					label: 'Anesthesia Procedure',
					width: 300,
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

function open_tray_anesthesia(mode, id, name)
{
	if(mode=="edit")
	{
		return overlib(
			OLiframeContent('<?=$root_path?>modules/or/anesthesia_mgr/anesthesia_proc_tray.php?cat_id='+id+'&cat_name='+name+'&mode=edit', 570, 300, 'fOrderTray', 0, 'no'),
			WIDTH,570, HEIGHT,300, TEXTPADDING,0, CAPTIONPADDING,2,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
			CAPTIONPADDING,2,DRAGGABLE,
			CAPTION,'Edit anesthesia procedure',
			MIDX,0, MIDY,0,
			STATUS,'Edit anesthesia procedure'
		);
	}else if(mode=="new")
	{
		//var new_proc_htm = $('new_anesthesia').innerHTML;
		return overlib(
			OLiframeContent('<?=$root_path?>modules/or/anesthesia_mgr/anesthesia_proc_tray.php?mode=new', 570, 300, 'fOrderTray', 0, 'no'),
			WIDTH,570, HEIGHT,300, TEXTPADDING,0, CAPTIONPADDING,2,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
			CAPTIONPADDING,2,DRAGGABLE,
			CAPTION,'New anesthesia procedure',
			MIDX,0, MIDY,0,
			STATUS,'New anesthesia procedure'
		);
	}
}

function open_delete_anesthproc(id)
{
	var reply = confirm("Are you sure you want to delete this item?");
		if(reply)
		{
			delete_category(id);
		}
}

function searchAnesthesia()
{
	$('anesthesia_list').list.params={'search':$('search_key').value};
	$('anesthesia_list').list.refresh();
}

</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform">');
$smarty->assign('sFormEnd','</form>');
$mode="new";
$smarty->assign('sAddBtn', '<input type="button" class="segButton" name"add_btn" value="New Anesthesia Procedure" onclick="open_tray_anesthesia(\''.$mode.'\',\''.'\',\''.'\');"/>');
$smarty->assign('sSearchBtn', '<input type="button" class="segButton" name"search_btn" value="Search" style="color: rgb(0, 0, 128);" onclick="searchAnesthesia(); return false;"/>');
$smarty->assign('sSearchKey', '<input class="segInput" type="text" name="search_key" id="search_key" style="width:60%"/>');

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

?>

<form action="<?php echo $breakfile?>" method="post">
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" id="mode" name="mode" value="<?= $_REQUEST['mode'] ?>">
<input type="hidden" name="key" id="key">
<input type="hidden" name="pagekey" id="pagekey">
</form>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe
 $smarty->assign('sMainBlockIncludeFile','or/anesthesia/main_gui.tpl');
 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>

