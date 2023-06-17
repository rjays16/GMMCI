<?php
//created by cha August 12, 2010

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/prescription/ajax/seg-prescription.common.php');

define('NO_CHAIN',1);
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path.'modules/clinics/labor.php'.URL_APPEND;
$returnfile=$root_path.'modules/clinics/labor.php'.URL_APPEND;

$thisfile=basename(__FILE__);

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');


$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$thisfile='seg-clinic-prescription-templates.php';

//initialize smarty
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Toolbar title
$smarty->assign('sToolbarTitle','Prescription Writer :: Templates Manager');

# href for the return button
$smarty->assign('pbBack',$returnfile);

# href for the  button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# Window bar title
$smarty->assign('sWindowTitle',"Prescription Writer :: Templates Manager");
$smarty->assign('breakFile',$breakfile);

ob_start();
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>
<script type="text/javascript" src="js/md5.js"></script>


<script type="text/javascript">
var $J = jQuery.noConflict();

function validate()
{
	var meds_count = document.getElementsByName('item_code[]').length;

	if($('template_name').value=="") {
		alert("Please enter the template name for this prescription.");
		$('template_name').focus();
		return false;
	}
	else if(meds_count <= 0) {
		alert("Please add the medicines.");
		return false;
	}else if(meds_count > 0) {
		var item = document.getElementsByName('row_id[]');
		for(i=0;i<item.length;i++)
		{
			if($('item_qty'+item[i].value).value=="" || $('item_qty'+item[i].value).value=="0") {
				alert("Please enter the quantity.");
				$('item_qty'+item[i].value).focus();
				return false;
			}
			else if($('item_dosage'+item[i].value).value=="") {
				alert("Please enter the dosage.");
				$('item_dosage'+item[i].value).focus();
				return false;
			}
			else if($('item_pcount'+item[i].value).value=="" || $('item_pcount'+item[i].value).value=="0") {
				alert("Please enter the period count.");
				$('item_pcount'+item[i].value).focus();
				return false;
			}
		}
	}
	return true;
}

function searchTemplate()
{
	$('templates-list').list.params = {'name':$('template_search').value};
	$('templates-list').list.refresh();
}

function addTemplate()
{
	$('template_name').value="";
	$('search-meds').value="";
	clearList('prescriptionlist');
	append_empty_list();
	$J('#add-template').dialog('open');
}

function closeTemplate()
{
	$('templates-list').list.refresh();
	$J('#add-template').dialog('close');
}

function outputResponse(rep)
{
	alert(rep)
	$('templates-list').list.refresh();
}

function updateTemplate(id, name)
{
	addTemplate();
	xajax_showEditTemplate(id, name);
}

function saveTemplate()
{
	if(validate()) {
		 var code = document.getElementsByName('item_code[]');
		 var name = document.getElementsByName('item_name[]');
		 var qty = document.getElementsByName('item_qty[]');
		 var dosage = document.getElementsByName('item_dosage[]');
		 var pcount = document.getElementsByName('item_pcount[]');
		 var pinterval = document.getElementsByName('item_pinterval[]');
		 var item_code = new Array();
		 var item_name = new Array();
		 var item_qty = new Array();
		 var item_dosage = new Array();
		 var item_pcount = new Array();
		 var item_pinterval = new Array();
		 var save_details = [];

		 for(i=0;i<name.length;i++)
		 {
				item_code[i] = code[i].value;
				item_name[i] = name[i].value;
				item_qty[i] = qty[i].value;
				item_dosage[i] = dosage[i].value;
				item_pcount[i] = pcount[i].value;
				item_pinterval[i] = pinterval[i].value;
		 }
		 save_details['code'] = item_code;
		 save_details['name'] = item_name;
		 save_details['qty'] = item_qty;
		 save_details['dosage'] = item_dosage;
		 save_details['pcount'] = item_pcount;
		 save_details['pinterval'] = item_pinterval;
		 save_details['template_name'] = $('template_name').value;
		 save_details['template_owner'] = $('template_owner').value;

		 if($('modeval').value=='save') {
			 xajax_saveTemplate(save_details);
		 }
		 else if($('modeval').value=='update') {
			 xajax_updateTemplate($('template_id').value, save_details);
		 }
	}
}

function deleteTemplate(id)
{
	var rep = confirm("Delete this standard prescription from templates?")
	if(rep) {
		xajax_deleteTemplate(id);
	}
}

function addDrugToList()
{
	if($('drug_code').value=="") {
		alert("No item to be added.")
		return false;
	}
	addDrug($('drug_code').value, $('drug_name').value,0,'',0,'',$('drug_generic').value);
	$('search-meds').value="";
	$('drug_code').value =  "";
	$('drug_name').value =  "";
	$('drug_generic').value = "";
}

function addDrug(code,name,qty,dosage,pcount,pinterval,generic)
{
	var tableId = $('prescriptionlist');

	if($('item_code'+MD5(name))) {
	 alert("Item already added to list of medicines.")
	 return false;
	} else {
	 if(tableId)
	 {
			var dBody=tableId.select("tbody")[0];
			if(dBody){
				var table1 = $('prescriptionlist').getElementsByTagName('tbody').item(0);
				if ($('row_empty')) {
					table1.removeChild($('row_empty'));
				}
				var dRows = dBody.getElementsByTagName("tr");
				if(MD5(name))
				{
					alt = (dRows.length%2>0) ? ' class="alt"':''

					rowSrc = '<tr class="'+alt+'" id="prescription'+MD5(name)+'">'+
							'<td>'+
								'<span style="color:#660000;font-weight:bold">'+name.toUpperCase()+'</span>'+
								'<input type="hidden" name="item_name[]" value="'+name+'"/>'+
								'<input type="hidden" name="row_id[]" value="'+MD5(name)+'"/>'+
								'<br/>'+
								'<span style="font:normal 10px Arial">'+generic+'</span>'+
							'</td>'+
							'<td align="center">'+
								'<input type="text" class="segInput" name="item_qty[]" id="item_qty'+MD5(name)+'" value="'+qty+'" style="width:100%;text-align: right" onfocus="this.select()"/>'+
								'<input type="hidden" name="item_code[]" id="item_code'+MD5(name)+'" value="'+code+'"/>'+
							'</td>'+
							'<td align="center" style="padding: 4px">'+
								'<textarea class="segInput" type="text" name="item_dosage[]" id="item_dosage'+MD5(name)+'" onfocus="this.select()" style="width:100%;text-align: left;" rows="1">'+dosage+'</textarea>'+
							'</td>'+
							'<td align="center" nowrap="nowrap">'+
								'<input class="segInput" type="text" onfocus="this.select()" id="item_pcount'+MD5(name)+'" name="item_pcount[]" style="width:30%;text-align:right" value="'+pcount+'"/>&nbsp;'+
								'<select class="segInput" style="width:60%" name="item_pinterval[]" id="pinterval'+MD5(name)+'">'+
									'<option value="D" '+(pinterval=='D'?'selected="selected"':'')+'>day/s</option>'+
									'<option value="W" '+(pinterval=='W'?'selected="selected"':'')+'>week/s</option>'+
									'<option value="M" '+(pinterval=='M'?'selected="selected"':'')+'>month/s</option>'+
								'</select>'+
							'</td>'+
							'<td align="center">'+
								'<img class="link" src="<?=$root_path?>images/cashier_delete.gif" border="0" onclick="remove_item(\''+MD5(name)+'\');return false;"/>'+
							'</td>'+
						'</tr>';
				}else
				{
					rowSrc = '<tr id="row_empty"><td colspan="6">No medicines added...</td></tr>';
				}
				dBody.insert(rowSrc);
			}
	 }
 }
}

function remove_item(id)
{
	var rep = confirm("Delete this medicine from list?");
	if(rep) {
		var table = $('prescriptionlist').getElementsByTagName('tbody').item(0);
		table.removeChild($('prescription'+id));

		if (!document.getElementsByName('item_code[]') || document.getElementsByName('item_code[]').length <= 0) {
			append_empty_list();
		}
	}
	return false;
}

function append_empty_list()
{
	var table = $('prescriptionlist').getElementsByTagName('tbody').item(0);
	var row = document.createElement("tr");
	var cell = document.createElement("td");
	row.id = "row_empty";
	cell.appendChild(document.createTextNode('No medicines added...'));

	cell.colSpan = "6";
	row.appendChild(cell);
	$('prescriptionlist').getElementsByTagName('tbody').item(0).appendChild(row);
}

function clearList(listID)
{
	var list=$(listID),dRows, dBody;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			dBody.innerHTML = "";
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function initialize()
{
	 ListGen.create( $('templates-list'), {
		id: 'addtemplate',
		url: '<?=$root_path?>modules/prescription/ajax/populateTemplateList.ajax.php',
		width: 'auto',
		height: 'auto',
		autoLoad: true,
		effects: true,
		rowHeight: 30,
		columnModel: [
			{
				name: 'template_name',
				label: 'Name',
				width: 150,
				sortable: true,
				sorting: ListGen.SORTING.asc
			},
			{
				name: 'template_owner',
				label: 'Owner',
				width: 200,
				sortable: false
			},
			{
				name: 'template_date',
				label: 'Date Created',
				width: 150,
				sortable: true,
				sorting: ListGen.SORTING.asc
			},
			{
				name: 'options',
				label: 'Options',
				width: 150,
				sortable: false
			}
		]
	});
}

// jQuery onDomReady
$J(function(){
	//$J.fx.speeds._default = 1000;
	$J('#search-meds').autocomplete({
		minLength: 1,
		source: '<?=$root_path?>modules/prescription/ajax/suggestMedicines.ajax.php',
		select: function(event, ui) {
			// NOTE: put onSelect logic here
			if(ui.item.restricted==1 && ui.item.is_licensed==0) {
				alert("This is a dangerous drug and you don't have a license to prescribe this kind of drug.")
				return false;
			}

			$('drug_code').value =  ui.item.code;
			$('drug_name').value =  ui.item.value;
			$('drug_generic').value =  ui.item.generic;
			$J('#search-meds').val(ui.item.label);
			addDrugToList();
			return false;
		}
	})
	.data( "autocomplete" )._renderItem = function( ul, item ) {

		return $J( "<li></li>" )
			.data( "item.autocomplete", item )
			.append(
				"<a>" +
					'<span style="font-weight:bold;color:'+(item.restricted=='1'?'#ff0000':'#000066')+'">' + item.name + '</span>' +
					"<br/>" +
					'<span style="font:normal 10px Arial;color:'+(item.restricted=='1'?'#ff0000':'#404040')+'">' + item.generic + ' (' + item.availability +')</span>' +
				"</a>" )
			.appendTo( ul );
	};

	$J('#add-template').dialog({
		title: 'Add Template',
		autoOpen: false,
		width: 540,
		height: 400,
		modal: true,
		show: 'fade',
		hide: 'fade',
		resizable: false,
		closeOnEscape: true,
		close: function() {
		}
	});

});



document.observe('dom:loaded', initialize);
</script>

<?
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();

$smarty->assign('form_start','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform">');
$smarty->assign('form_end','</form>');

$smarty->assign('template_search', '<input type="text" id="template_search" name="template_search" class="segInput" style="width:60%" onkeyup="if(this.value.length>=3){searchTemplate();}return false;"/>');
$smarty->assign('search_btn', '<button class="segButton" onclick="searchTemplate();return false;" style="cursor:pointer"><img src="'.$root_path.'gui/img/common/default/zoom.png"/>Search</button>');
$smarty->assign('add_template', '<button class="segButton" onclick="addTemplate();return false;" style="cursor:pointer"><img src="'.$root_path.'gui/img/common/default/page_add.png"/>New Template</button>');
$smarty->assign('save_template', '<button class="segButton" onclick="saveTemplate();return false;" style="cursor:pointer"><img src="'.$root_path.'gui/img/common/default/disk.png"/>Save</button>');
$smarty->assign('close_template', '<button class="segButton" onclick="closeTemplate();return false;" style="cursor:pointer"><img src="'.$root_path.'gui/img/common/default/cancel.png"/>Close</button>');

$smarty->assign('template_owner','<input id="owner" type="text" class="segInput" style="width:300px;" value="'.$_SESSION['sess_user_name'].'" readonly="readonly"/>');
$smarty->assign('ownerHidden','<input id="template_owner" type="hidden" value="'.$_SESSION['sess_temp_userid'].'"/>');
$smarty->assign('template_itemname','<input id="search-meds" type="text" class="segInput" style="width:300px; padding:2px; font:bold 14px Arial; color: #006" value=""/>');
$smarty->assign('template_name','<input id="template_name" type="text" class="segInput" style="width:300px;" value=""/>');
$smarty->assign('add_drug_btn', '<button class="segButton" onclick="addDrugToList();return false;" style="cursor:pointer"><img src="'.$root_path.'gui/img/common/default/pill_add.png"/>Add</button>');
ob_start();
?>

<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" id="userck" value="<?php echo $userck?>">
<input type="hidden" name="encoder" id="encoder" value="<?php echo  str_replace(" ","+",$_COOKIE[$local_user.$sid])?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<?
$sTemp = ob_get_contents();
$sTable = ob_get_contents();
ob_end_clean();
$smarty->assign('sTable',$sTable);
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','left').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');

/**
* show Template
*/
# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','clinics/prescription-template-mgr.tpl');
$smarty->display('common/mainframe.tpl');

?>