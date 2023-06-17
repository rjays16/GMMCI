<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root= directory
$local_user='ck_lab_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');   
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path . 'modules/laboratory/ajax/order.common.php');

$target = $_GET['target'];

$smarty = new Smarty_Care('select_or_request');
$smarty->assign('sToolbarTitle',"Laboratory::List of Requests"); //Assign a toolbar title
$css_and_js = array('<link rel="stylesheet" href="'.$root_path.'modules/or/css/select_or_request.css" type="text/css" />'
										,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/flexigrid/css/flexigrid/flexigrid.css">'
										,'<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/flexigrid.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jquery.tabs/jquery.tabs.pack.js"></script>'
										,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/jquery.tabs/jquery.tabs.css" />'
										 ,$xajax->printJavascript($root_path.'classes/xajax_0.5'));
$smarty->assign('css_and_js', $css_and_js);

$number_of_pages = array('5'=>'5', '10'=>'10', '15'=>'15', '20'=>'20', '25'=>'25', '30'=>'30');
$smarty->assign('number_of_pages', $number_of_pages);
$smarty->assign('page_number', '<input type="text" id="page_number" name="page_number" />');
$smarty->assign('search_field', '<input type="text" id="search_field" name="search_field" />');
$smarty->assign('search_button', '<input type="submit" id="search_button" value="Search" />');
#$breakfile=$root_path.'main/op-doku.php'.URL_APPEND;
$breakfile=$root_path.'modules/laboratory/labor.php'.URL_APPEND;

$smarty->assign('breakfile',$breakfile); //Close button
$smarty->assign('return', '<a href="'.$breakfile.'" id="return_button"></a>');
$smarty->assign('sMainBlockIncludeFile','or/select_or_request.tpl'); //Assign the select_or_request template to the frameset
$smarty->display('common/mainframe.tpl'); //Display he contents of the frame   

?>
<script>
			 
 

$(document).ready(function() {

$('#or_request_table').flexigrid
({
 url: '<?=$root_path?>modules/laboratory/ajax/ajax_lab_list.php',
 dataType: 'json',
 colModel : [
						 {display: 'Reference Number', width:90, name : 'refno', sortable : true, align: 'left'},
						 {display: 'Request Date', width:100, name:'request_date', sortable: true, align: 'left'},
						 {display: 'Patient ID', width:90, name:'patient_id', sortable: false, align: 'left'},
						 {display: 'Patient Name', width:100, name:'patient_name', sortable: false, align: 'left'},
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


</script>
