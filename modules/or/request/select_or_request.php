<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root= directory
$local_user='ck_op_pflegelogbuch_user';
require($root_path.'include/inc_environment_global.php');
//require_once($root_path.'modules/or/ajax/order.common.php');
require_once($root_path.'include/inc_front_chain_lang.php');
//require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template


$target = $_GET['target'];



require_once $root_path.'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

$smarty->assign('sToolbarTitle',"OR::List of Requests"); //Assign a toolbar title
$smarty->assign('sWindowTitle',"OR::List of Requests"); //Assign a toolbar title
$breakfile=$root_path.'main/op-doku.php'.URL_APPEND;
$smarty->assign('breakfile',$breakfile); //Close button

# Collect javascript code
ob_start();
# Load the javascript code
?>

<link rel="stylesheet" href="<?= $root_path ?>modules/or/css/select_or_request.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="<?= $root_path ?>modules/or/js/flexigrid/css/flexigrid/flexigrid.css">
<script type="text/javascript" src="<?= $root_path ?>/js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>

<script type="text/javascript" src="<?= $root_path ?>modules/or/js/flexigrid/lib/jquery/jquery.js"></script>
<script type="text/javascript" src="<?= $root_path ?>modules/or/js/flexigrid/flexigrid.js"></script>
<script type="text/javascript" src="<?= $root_path ?>modules/or/js/jquery.tabs/jquery.tabs.js"></script>
<link rel="stylesheet" type="text/css" href="<?= $root_path ?>modules/or/js/jquery.tabs/jquery.tabs.css" />
<script type="text/javascript">
$J = jQuery.noConflict();
$J(document).ready(function() {
	$J('#or_request_table').flexigrid({
		url: '<?=$root_path?>modules/or/ajax/ajax_or_list.php?target=<?=$target?>',
		dataType: 'json',
		colModel : [
		 {display: 'Reference Number', width:90, name : 'refno', sortable : true, align: 'left'},
		 {display: 'Request Date', width:100, name:'request_date', sortable: true, align: 'left'},
		 {display: 'Patient ID', width:80, name:'patient_id', sortable: false, align: 'left'},
		 {display: 'Patient Name', width:150, name:'patient_name', sortable: false, align: 'left'},
		 {display: 'Department', width:100, name:'department', sortable: false, align: 'left'},
		 {display: 'Charge', width:80, name:'charge', sortable: false, align: 'left'}
		],
		height: 165,
		sortname: ["request_date"],
		domain: ['charge_request'],
		sortorder: "desc",
		useRp: true,
		rp: 5,
		resizable: true
	});
});


function showCharges( refNo,  encounterNr, wardNr, pid) {

	params = {
		refNo: refNo || '',
		encounterNr: encounterNr || '',
		wardNr: wardNr || '',
		pid: pid || ''
	};

	url = 'op_request_pass.php<?= URL_APPEND?>&refno='+params.refNo+
		'&target=or_other_charges_get&encounter_nr='+params.encounterNr+'&ward='+params.wardNr+
		'&popUp=1&area=OR&pid='+params.pid;
	overlib(
		OLiframeContent(url,
			750, 400, 'showChargesUI', 0, 'auto'),
			WIDTH,750, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2,
			CAPTION,'Process Social Service request',
			MIDX,0, MIDY,0,
			STATUS,'Process Social Service request');
	return false;
}
</script>
<?php
//$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$number_of_pages = array('5'=>'5', '10'=>'10', '15'=>'15', '20'=>'20', '25'=>'25', '30'=>'30');
$smarty->assign('number_of_pages', $number_of_pages);
$smarty->assign('page_number', '<input class="segInput" type="text" id="page_number" name="page_number" />');
$smarty->assign('search_field', '<input class="segInput" type="text" id="search_field" name="search_field" />');
$smarty->assign('search_button', '<input class="segButton" type="submit" id="search_button" value="Search" />');

$smarty->assign('return', '<a href="'.$breakfile.'" id="return_button"></a>');
$smarty->assign('sMainBlockIncludeFile','or/select_or_request.tpl'); //Assign the select_or_request template to the frameset
$smarty->display('common/mainframe.tpl'); //Display he contents of the frame

