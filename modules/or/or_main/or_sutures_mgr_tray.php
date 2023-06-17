<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root= directory
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path.'modules/or/ajax/order.common.php');
require_once($root_path.'include/care_api_classes/billing/class_ops.php'); //load the SegOps class
require_once($root_path.'include/care_api_classes/or/class_or.php'); //load the SegOR class
$seg_ops = new SegOps();
$seg_or = new SegOR();
if($_GET['mode']=='')	$mode=$_POST['mode'];
else 									$mode = $_GET['mode'];


require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('or_suture_mgr_tray');

$LDTitleMgr = "OR::Suture Manager";
$smarty->assign('sToolbarTitle',"$LDTitleMgr");
// $smarty->assign('sWindowTitle',"$LDTitleMgr");
$css_and_js = array(
										'<link rel="stylesheet" href="'.$root_path.'modules/or/css/packages.css" type="text/css" />'
										,'<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
										,'<script>var J = jQuery.noConflict();</script>'
										,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jquery.tabs/jquery.tabs.pack.js"></script>'
										,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/jquery.tabs/jquery.tabs.css" />'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/iframecontentmws.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_draggable.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_filter.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_overtwo.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_scroll.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_shadow.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_modal.js"></script>'
										,'<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'
										,$xajax->printJavascript($root_path.'classes/xajax_0.5'));
$smarty->assign('css_and_js', $css_and_js);
$breakfile="javascript:window.parent.reload();javascript:window.parent.cClick();";


if (isset($_POST['is_submitted'])) {
	$mode = $_POST['mode'];
	$suture_name = $_POST['suture_name'];
	/*if(count($_POST['question'])==0){
		 $smarty->assign('sysErrorMessage','Select an OR area for the checklist.');
	}    */
	//else{
		$data = array('suture_name' => $_POST['suture_name'],
									'is_deleted' => 0,
									'id' => $_POST['id']);
		if($mode=='edit')
			$check = $seg_or->edit_suture($data);
		else if($mode=='new')
			$check = $seg_or->insert_suture($data);

		if(!$check)
			$smarty->assign('sysErrorMessage','An error has occurred');
		else{
			if($mode=='edit')
				$smarty->assign('sysInfoMessage','Successfully saved.');
			else if($mode=='new')
				$smarty->assign('sysInfoMessage','Successfully saved.');
		}
	//}
}
#$idnum = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

if($mode=='edit'){
	$id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

	$or_details = $seg_or->get_suture_data($id);
	$_POST['suture_name'] = $or_details['name'];
	#echo "suture_name = ".$or_details['name'];
	//$checklist_question = $item_details['c_question'];

}

if ($mode=='new'){
	#$result = $seg_ops->get_or_checklist_items(0);
	$suture_name='';

}

/*$id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
$or_details = $seg_or->get_suture_data($id);
if($or_details['name']){
	$suture_name = $or_details['name'];
}else{
	$suture_name = '';
}           */
if(!empty($_POST['suture_name']))
	$suture_name = $_POST['suture_name'];
else
	$suture_name = $or_details['name'];

$smarty->assign('form_start', '<form name="checklist_form" method="POST" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return validate()">');
$smarty->assign('form_end', '</form>');

/*$smarty->assign('areas', $checklist_areas);
$smarty->assign('areas_selected', $selected); */
$smarty->assign('suture_name','<input type="text" name="suture_name" id="suture_name" class="segInput" style="width:420px" value="'.$suture_name.'" onfocus="OLmEdit=1;" onblur="OLmEdit=0;"/>' );
/*$smarty->assign('detail_div', '<div id="detail_area"'.$is_detail_enabled.'>');
$smarty->assign('additional_detail', '<input type="checkbox" name="additional_detail" id="additional_detail"'.$is_detail_checked.' onClick="toggledetail();"/>');
$smarty->assign('detail', '<input type="text" name="detail_label" id="detail_label" class="segInput" style="width:420px" value="'.$detail_label.'" onfocus="OLmEdit=1;" onblur="OLmEdit=0;"/>');
$smarty->assign('mandatory','<input type="checkbox" name="mandatory" id="mandatory"'.$is_mandatory.'/>');
						 */
$smarty->assign('package_submit', '<input type="submit" id="package_submit" value="" />');
$smarty->assign('package_cancel', '<a href="'.$breakfile.'" id="package_cancel"></a>');

$smarty->assign('is_submitted', '<input type="hidden" name="is_submitted" value="TRUE" />');
//$smarty->assign('name_s', '<input type="text" name="name_s" value="'.$or_details['name'].'" />');
/*$smarty->assign('new_question', '<input type="hidden" name="new_question" id="new_question" value="" />');
$smarty->assign('new_detail', '<input type="hidden" name="new_detail" id="new_detail" value="" />');
$smarty->assign('is_detail', '<input type="hidden" name="is_detail" id="is_detail" value="" />');
$smarty->assign('is_mandatory', '<input type="hidden" name="is_mandatory" id="is_mandatory" value="" />');
				*/
$smarty->assign('id', '<input type="hidden" name="id" id="id" value="'.$id.'" />');
$smarty->assign('mode', '<input type="hidden" name="mode" id="mode" value="'.$mode.'" />');

$smarty->assign('breakfile',$breakfile); //Close button
$smarty->assign('bHideCopyright', true);
$smarty->assign('bHideTitleBar', true);


?>
<script>

function toggledetail() {
		var area = document.getElementById('detail_area');
		var cbox = document.getElementById('additional_detail');
		if(cbox.checked)
			area.style.display = 'block';
		else
			area.style.display = 'none';
}

function validate() {
 var errors_field = new Array();
 var checklist_question = document.getElementById('checklist_question');
 var detail_label = document.getElementById('detail_label');
 var additional_detail = document.getElementById('additional_detail');
 var mandatory = document.getElementById('mandatory');


 var new_question = document.getElementById('new_question');
 var new_detail = document.getElementById('new_detail');
 var is_detail = document.getElementById('is_detail');
 var is_mandatory = document.getElementById('is_mandatory');
	 if(checklist_question.value.replace(/\s+/g,'') == '')
			errors_field.push('Please enter a valid checklist question.');
	 else
			new_question.value = checklist_question.value;

	 if(detail_label.value.replace(/\s+/g,'') == '' && additional_detail.checked)
			errors_field.push('Please enter a valid detail label.');
	 else
			new_detail.value = detail_label.value;

	 if(additional_detail.checked)
			is_detail.value = 1;
	 else
			is_detail.value = 0;

	 if(mandatory.checked)
			is_mandatory.value = 1;
	 else
			is_mandatory.value = 0;

	if (errors_field.length > 0) {
		var str = errors_field.join("\n");
		alert(str);
		return false;
	}
	else{
		return true;
	}
}


</script>
<?php

$smarty->assign('sMainBlockIncludeFile','or/or_sutures_mgr_tray.tpl'); //Assign the new_package template to the frameset
$smarty->display('common/mainframe.tpl'); //Display he contents of the frame
?>