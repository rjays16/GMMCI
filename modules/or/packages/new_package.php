<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root= directory
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path . 'modules/or/ajax/order.common.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/billing/class_ops.php'); //load the SegOps class
$target = $_GET['target'];

$department = new Department();
$smarty = new Smarty_Care('select_or_request');
$smarty->assign('sToolbarTitle',"OR::Packages"); //Assign a toolbar title
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

	#---added by CHa, Feb 11, 2010------------
	$item_list = array();
	#$item_purpose_list = array('pre_op', 'prep', 'anesthesia', 'op_prop', 'meds', 'others');
	#modified by angelo m. 09.13.2010
	$item_purpose_list = array('PH','LB','RD','MISC');


	for($i=0;$i<count($item_purpose_list);$i++)
	{
		$id = $item_purpose_list[$i];
		$itemid = $id.''.'_item_id';
		$qtyid = $id.''.'_item_qty';
		#$unitid = $id.''.'_item_unit';
		for($j=0;$j<count($_POST[$itemid]);$j++)
		{
			$key = $_POST[$itemid][$j];
			$qty = $_POST[$qtyid][$j];
			#$unit = $_POST[$unitid][$j];
			$item_list[] = array($key, $qty, $id);
		}
	}
	#echo print_r($item_list);
	#die("here");
	$_POST['item_list'] = $item_list;
 #---end CHa-------------------------------
	$seg_ops = new SegOps();
	if ($seg_ops->save_package($_POST)) {
		$smarty->assign('sysInfoMessage','Package successfully saved');
	}
	else {
		 $smarty->assign('sysErrorMessage','Error in saving the package.');
	}
}

$smarty->assign('form_start', '<form name="package_form" method="POST" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return validate()">');
$smarty->assign('form_end', '</form>');

$smarty->assign('package_name', '<input type="text" name="package_name" />');
$smarty->assign('package_price', '<input type="text" name="package_price" />');
$smarty->assign('issurgical', '<input style="float:left;vertical-align:middle;" type="checkbox" name="is_surgical" onclick="this.value = (this.checked) ? 1 : 0;"/> (Check if YES.)');
$dept_temp = array();

foreach ($department->getAllMedical() as $value) {
	$dept_temp[$value['nr']] = $value['name_formal'];
}
$smarty->assign('departments', $dept_temp);
$smarty->assign('add_department', '<input type="button" onclick="queue_department()" name="add_department" value="Add Department" style="float:left" />');
$smarty->assign('package_submit', '<input type="submit" id="package_submit" value="" />');
$smarty->assign('package_cancel', '<a href="'.$breakfile.'" id="package_cancel"></a>');
$smarty->assign('is_submitted', '<input type="hidden" name="is_submitted" value="TRUE" />');

$smarty->assign('breakfile',$breakfile); //Close button
$smarty->assign('bHideCopyright', true);
$smarty->assign('bHideTitleBar', true);
$smarty->assign('sMainBlockIncludeFile','or/packages/new_package.tpl'); //Assign the new_package template to the frameset
$smarty->display('common/mainframe.tpl'); //Display he contents of the frame

?>
<script>
J().ready(function() {
	J('#new_package').tabs();
	J("input[@name='package_price']").keydown(function(e){return key_check(e, this.value);});
});


function queue_department() {
	if ((J("#assigned_department"+J("select[name='departments'] :selected").val()).length) <= 0) {
	var table = $('department_table').getElementsByTagName('tbody').item(0);
	var row = document.createElement("tr");
	row.id = "assigned_department_tr"+J("select[name='departments'] :selected").val();
	var array_elements = [{type: 'img', src: '../../../images/or_main_images/delete_item.png'},
												{type: 'td_text', name: J("select[name='departments'] :selected").text()}];
	for (var i=0; i<array_elements.length; i++) {
		var cell = document.createElement("td");
		if (array_elements[i].type == 'td_text') {
			cell.appendChild(document.createTextNode(array_elements[i].name));
		}
		if (array_elements[i].type == 'img') {
			img = document.createElement("img");
			cell.appendChild(img);
			img.src = array_elements[i].src;
			img.style.cursor = "pointer";
			img.setAttribute("onclick", 'remove_department('+J("select[name='departments'] :selected").val()+')');
		}
		row.appendChild(cell);
	}
	$('department_table').getElementsByTagName('tbody').item(0).appendChild(row);
	var hidden_array = document.createElement('input');
	hidden_array.name = "assigned_department[]";
	hidden_array.type = "hidden";
	hidden_array.value = J("select[name='departments'] :selected").val();
	hidden_array.id = "assigned_department"+J("select[name='departments'] :selected").val();
	document.forms[0].appendChild(hidden_array);
	}
	else {
		alert('Existing');
	}
}

function remove_department(id) {
	var table = $('department_table').getElementsByTagName('tbody').item(0);
	table.removeChild($('assigned_department_tr'+id));
	document.forms[0].removeChild($('assigned_department'+id));
}

function key_check(e, value) {

	 var character = String.fromCharCode(e.keyCode);
	 var number = /^\d+$/;

	 //if (e.keyCode==9 || e.keyCode==116 || e.keyCode==8 || e.keyCode == 190) {
	 if ((e.keyCode==46 || e.keyCode==8 || e.keyCode==16 || e.keyCode==9 || (e.keyCode==191 || e.keyCode==111) || (e.keyCode>=36 && e.keyCode<=40) || (e.keyCode>=96 && e.keyCode<=105))) {
		 return true;
	 }
	 if (character.match(number)==null) {
		 return false;
	 }
	 else {
		 return true;
	 }
}

function validate() {
 var errors_field = new Array();

	var array_elements = [{field: J("input[@name='package_name']"),
												 field_value: J("input[@name='package_name']").val(),
												 msg: 'Please enter a valid package name',
												 is_textfield: true
												 },
												 {field: J("input[@name='package_price']"),
												 field_value: J("input[@name='package_price']").val(),
												 msg: 'Please enter a valid package price',
												 is_textfield: true
												 },
												 {field: J("input[@name='assigned_department[]']"),
												 field_value: J("input[@name='assigned_department[]']").length,
												 msg: 'Please assign this package to a department',
												 is_textfield: false
												 },
												 ];

	for (var i=0; i<array_elements.length; i++) {
		if (array_elements[i].field_value == '' || !array_elements[i].field_value || array_elements[i].length <= 0) {
			errors_field.push(array_elements[i].msg);
			if (array_elements[i].is_textfield) {
				array_elements[i].field.addClass('error_field');
			}

		}
		else {
			array_elements[i].field.removeClass('error_field');
		}
	}
	if (errors_field.length > 0) {
		var str = errors_field.join("\n");
		alert(str);
		return false;
	}
	else {
		return true;
	}

}

//added by CHA, Feb 10, 2010
function open_package_items(id)
{
		//$('item_purpose_list').innerHTML+='<input type="hidden" id="item_purpose[]" name="item_purpose[]" value="'+id+'"/>';
		overlib(
		OLiframeContent('<?=$root_path?>modules/or/packages/package_items_gui.php?mode='+id, 550, 250, 'fOrderTray', 0, 'no'),
		WIDTH,300, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 onclick="window.parent.reload()" >',
		CAPTIONPADDING,2,DRAGGABLE,
		CAPTION,'New Package Item',
		MIDX,0, MIDY,0,
		STATUS,'New Package Item');
	return false
}

function remove_package_item(mode,id) {
	var table = $('purpose_table').getElementsByTagName('tbody').item(0);
	table.removeChild($(mode+'itemlist'+id));
}
//


</script>
