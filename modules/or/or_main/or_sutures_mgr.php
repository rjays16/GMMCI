<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root= directory
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path.'modules/or/ajax/order.common.php');
require_once($root_path.'include/care_api_classes/billing/class_ops.php'); //load the SegOps class
$seg_ops = new SegOps();


$target = $_GET['target'];
$breakfile=$root_path.'main/op-doku.php'.URL_APPEND;

$smarty = new Smarty_Care('or_sutures_mgr');
$smarty->assign('sToolbarTitle',"OR::Suture Manager"); //Assign a toolbar title
$css_and_js = array('<link rel="stylesheet" href="'.$root_path.'modules/or/css/select_or_request.css" type="text/css" />',
										'<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_main.css" type="text/css" />'
										,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/flexigrid/css/flexigrid/flexigrid.css">'
										,'<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/flexigrid.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jquery.tabs/jquery.tabs.pack.js"></script>'
										,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/jquery.tabs/jquery.tabs.css" />'
										,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/jqmodal/jqModal.css">'
										,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqModal.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqDnR.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/dimensions.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/iframecontentmws.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_draggable.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_filter.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_overtwo.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_scroll.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_shadow.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_modal.js"></script>'
										,$xajax->printJavascript($root_path.'classes/xajax_0.5'));
$smarty->assign('css_and_js', $css_and_js);

$number_of_pages = array('5'=>'5', '10'=>'10', '15'=>'15', '20'=>'20', '25'=>'25', '30'=>'30');
$smarty->assign('number_of_pages', $number_of_pages);
$smarty->assign('page_number', '<input type="text" id="page_number" name="page_number" />');
$smarty->assign('search_field', '<input type="text" id="search_field" name="search_field" />');
$smarty->assign('search_button', '<input type="submit" id="search_button" value="Search" />');
$smarty->assign('new_package', '<input type="button" id="new_package" value="Add New Type of Suture" onclick="open_new_package_popup()" />');
$smarty->assign('close_new_package_popup', '<a href="javascript:void(0)" id="close_new_package_popup" class="jqmClose"></a>');
$smarty->assign('resize', '<img src="'.$root_path.'images/or_main_images/resize.gif" class="jqResize" />');

$smarty->assign('breakfile',$breakfile); //Close button
$smarty->assign('return', '<a href="'.$breakfile.'" id="return_button"></a>');
$smarty->assign('sMainBlockIncludeFile','or/or_sutures_mgr.tpl'); //Assign the select_or_request template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame



?>
<script>



$(document).ready(function() {
 $('#or_request_table').flexigrid({
	url: '<?=$root_path?>modules/or/ajax/ajax_list_sutures.php?target=<?=$target?>',
	dataType: 'json',
	colModel : [{display: 'Suture Name', width:300, name : 'checklist_question', sortable : true, align: 'left'},
						 {display: 'Edit', width:73, name:'edit', sortable: false, align: 'center'},
						 {display: 'Delete', width:73, name:'delete', sortable: false, align: 'center'},],
	height: 165,
	sortname: ["date_created"],
	domain: ['charge_request'],
	sortorder: "desc",
	useRp: true,
	rp: 5,
	resizable: true
 });
 $('#new_package_popup')
		.jqDrag('.jqDrag')
		.jqResize('.jqResize');
 $('#new_package_popup').jqm({
	 overlay: 80
	});

	$('#container').tabs();


});

function reload() {
	$("#or_request_table").flexReload();
}

function open_new_package_popup() {
	overlib(
		OLiframeContent('<?=$root_path?>modules/or/or_main/or_sutures_mgr_tray.php?mode=new', 600, 160, 'fOrderTray', 0, 'yes'),
		WIDTH,600,HEIGHT,160,TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 onclick="window.parent.reload()">',
		CAPTIONPADDING,2,DRAGGABLE,
		CAPTION,'Add Suture Name',
		MIDX,0, MIDY,0,
		STATUS,'Add Suture Name');
	return false
}
							//'<=$root_path?>modules/or/or_main/or_checklist_items_mgr_tray.php?mode=edit&id='+checklist_id+''				//modules/laboratory/seg-service-tray.php?'
function open_edit_package_popup(id) {
	overlib(
		OLiframeContent('<?=$root_path?>modules/or/or_main/or_sutures_mgr_tray.php?id='+id+'&mode=edit', 600, 160, 'fOrderTray', 0, 'yes'),
		WIDTH,600,HEIGHT,160,TEXTPADDING,0, BORDER,0,
		STICKY,  SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 onclick="window.parent.reload()">',
		CAPTIONPADDING,2,DRAGGABLE,
		CAPTION,'Edit Suture Name',
		MIDX,0, MIDY,0,
		STATUS,'Edit Suture Name');
	return false
}

function open_delete_package_popup(id){
	if(confirm("Are you sure you want to delete this?"))
		xajax_delete_suture(id);
}

//var cClick = (function() {
//	var original_cClick = cClick;
//	return function() {
//		reload();
//		original_cClick();
//	}
//})();


</script>
