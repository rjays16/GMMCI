<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root= directory
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path.'modules/or/ajax/op-request-new.common.php'); //load the xajax module
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/billing/class_ops.php'); //load the SegOps class
$target = $_GET['target'];

$department = new Department();
$package_id = $_GET['id'] > 0 ? $_GET['id'] : $_POST['id'];
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
										,$xajax->printJavascript($root_path.'classes/xajax-0.2.5'));
$smarty->assign('css_and_js', $css_and_js);
$breakfile="javascript:window.parent.cClick();";
$seg_ops = new SegOps();



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
	$_POST['item_list'] = $item_list;

 #---end CHa-------------------------------
	if ($seg_ops->edit_package($_POST)) {
		$smarty->assign('sysInfoMessage','Package successfully updated');
	}
	else {
		 $smarty->assign('sysErrorMessage','Error in updating the package.');
	}
}
$data = $seg_ops->get_all_package_data($package_id);
extract($data);

$smarty->assign('form_start', '<form name="package_form" method="POST" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return validate()">');
$smarty->assign('form_end', '</form>');

$smarty->assign('package_name', '<input type="text" name="package_name" value="'.$package_name.'" />');
$smarty->assign('package_price', '<input type="text" name="package_price" value="'.number_format($package_price, 2, '.', '').'" />');
$smarty->assign('issurgical', '<input style="float:left;vertical-align:middle;" type="checkbox" name="is_surgical" '.(($is_surgical == '1') ? 'checked="checked"' : '').'onclick="this.value = (this.checked) ? 1 : 0;" value="'.$is_surgical.'" /> (Check if YES.)');
$dept_temp = array();

foreach ($department->getAllMedical() as $value) {
	$dept_temp[$value['nr']] = $value['name_formal'];
}
$smarty->assign('departments', $dept_temp);
$smarty->assign('add_department', '<input type="button" onclick="queue_department()" name="add_department" value="Add Department" stlye="float:left />');
$smarty->assign('package_submit', '<input type="submit" id="package_submit" value="" />');
$smarty->assign('package_cancel', '<a href="'.$breakfile.'" id="package_cancel"></a>');
$smarty->assign('package_id', '<input type="hidden" name="id" value="'.$package_id.'" />');
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

function add_to_department(details) {

	if ((J("#assigned_department"+details.clinic_id).length) <= 0) {
	var table = $('department_table').getElementsByTagName('tbody').item(0);
	var row = document.createElement("tr");
	row.id = "assigned_department_tr"+details.clinic_id;
	var array_elements = [{type: 'img', src: '../../../images/or_main_images/delete_item.png'},
												{type: 'td_text', name: details.name}];
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
			img.setAttribute("onclick", 'remove_department('+details.clinic_id+')');
		}
		row.appendChild(cell);
	}
	$('department_table').getElementsByTagName('tbody').item(0).appendChild(row);
	var hidden_array = document.createElement('input');
	hidden_array.name = "assigned_department[]";
	hidden_array.type = "hidden";
	hidden_array.value = details.clinic_id;
	hidden_array.id = "assigned_department"+details.clinic_id;
	document.forms[0].appendChild(hidden_array);
	}
	else {
		alert('Existing');
	}
}

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

function add_to_itemlist(details)
{
	if (typeof(details)=="object")
	{
		var mode = details.mode;
		var rowSrc='';

		rowSrc='<tr id="'+mode+'itemlist'+details.id+'">'+
		'<td width="1%" align="left">'+
			'<img src="../../../images/or_main_images/delete_item.png" style="cursor:pointer;" onclick="remove_package_item(\''+mode+'\',\''+details.id+'\');" title="Delete Item">'+
			'<input type="hidden" id="'+mode+'_item_id[]" name="'+mode+'_item_id[]" value="'+details.id+'"/>'+
		'</td>'+
		'<td width="1%" align="left" nowrap="nowrap">'+
			'<div style="float:left; padding:2px">'+
				'<span id="name'+details.id+'" style="font:bold 12px Arial;color:'+(details.restricted=='1'?'#c00000':'#000066')+'">'+details.name+'</span><br />'+
				'<div id="desc'+details.id+'" style="font:normal 11px Arial; color:'+(details.restricted=='1'?'#c00000':'#404040')+'">'+details.desc+'</div>'+
			'</div>'+
		'</td>'+
		'<td width="5%" align="left" nowrap="nowrap">'+
			'<div style="float:left; padding:2px">'+
			'<span id="name'+details.id+'" style="font:bold 12px Arial;">'+details.qty+'</span><br />'+
			'</div>'+
			'<input type="hidden" id="'+mode+'_item_qty[]" name="'+mode+'_item_qty[]" value="'+details.qty+'"/>'+
			'<input type="hidden" id="'+mode+'_itm_qty'+details.id+'" name="'+mode+'_itm_qty'+details.id+'" value="'+details.qty+'"/>'+
			//'<input type="hidden" id="'+mode+'_item_unit[]" name="'+mode+'_item_unit[]" value="'+details.unit+'"/>'+
		'</td>'+
		'</tr>';

		document.getElementById('items_table').innerHTML += rowSrc;
		$('item_purpose_list').innerHTML+='<input type="hidden" id="item_purpose[]" name="item_purpose[]" value="'+mode+'"/>';
	}
}

function remove_package_item(mode,id) {

	var table = $('purpose_table').getElementsByTagName('tbody').item(0);
	table.removeChild($(mode+'itemlist'+id));
	//var table = $(''+mode+'_table').getElementsByTagName('tbody').item(0);
//	table.removeChild($(''+mode+'itemlist'+id));
}

// end CHA

xajax_get_package_clinics(<?=$package_id?>);
xajax_get_package_item_details(<?=$package_id?>);	//added by cHa, Feb 12, 2010



</script>
