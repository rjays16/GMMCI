<?php
//created by cha Feb 5, 2010

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/prescription/ajax/seg-prescription.common.php');
/**
* Integrated Hospital Information System beta 2.0.0 - 2004-05-16
* GNU General Public License
* Copyright 2002,2003,2004
*
* See the file "copy_notice.txt" for the licence notice
*/
#define('LANG_FILE','specials.php');
define('NO_CHAIN',1);
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path.'modules/clinics/labor.php'.URL_APPEND;
$returnfile=$root_path.'modules/clinics/labor.php'.URL_APPEND;

$thisfile=basename(__FILE__);

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/prescription/class_prescription_writer.php');
$pres_obj = new SegPrescription();

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$thisfile='seg-clinic-new-prescription.php';

//initialize smarty
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Toolbar title
$smarty->assign('sToolbarTitle','Prescription Writer :: Edit prescription');

# href for the return button
$smarty->assign('pbBack',$returnfile);

# href for the  button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# Window bar title
$smarty->assign('sWindowTitle',"Prescription Writer :: Edit prescription");

$smarty->assign('bHideTitleBar', TRUE);
$smarty->assign('bHideCopyright', TRUE);
$smarty->assign('breakFile',$breakfile);

ob_start();
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>
<script type="text/javascript" src="js/md5.js"></script>
<script type="text/javascript">
var $J = jQuery.noConflict();

function savePrescription()
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
     save_details['clinical_impression'] = $('clinical-impression').value;
	 save_details['instructions'] = $('instructions').value;
	 save_details['encounter_nr'] = $('encounter_nr').innerHTML;
	 save_details['prescription_date'] = $('requestdate').value;

	 if( $('is_save').checked) {
		save_details['is_save'] = 1;
		save_details['template_name'] = $('template_name').value;
	 }else {
		save_details['is_save'] = 0;
	 }

	 xajax_savePrescription(save_details);
 }
}

function validate()
{
	var meds_count = document.getElementsByName('item_code[]').length;

	if($('encounter_nr').innerHTML=="") {
		alert("Please select a patient first.");
		$('name').focus();
		return false;
	}
	else if($('is_save').checked && $('template_name').value=="") {
		alert("Please enter the template name for this prescription.");
		$('template_name').focus();
		return false;
	}
	else if(meds_count <= 0) {
		alert("Please add your prescribed medicines.");
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
//			else if($('item_dosage'+item[i].value).value=="") {
//				alert("Please enter the dosage.");
//				$('item_dosage'+item[i].value).focus();
//				return false;
//			}
//			else if($('item_pcount'+item[i].value).value=="" || $('item_pcount'+item[i].value).value=="0") {
//				alert("Please enter the period count.");
//				$('item_pcount'+item[i].value).focus();
//				return false;
//			}
		}
		var isok1=0;
		var isok2=0;
		for(i=0;i<item.length;i++)
		{
			isok1=key_check($('item_qty'+item[i].value).value,$('item_qty'+item[i].value));
			if(!isok1) {
				return false;
			}
//			isok2=key_check($('item_pcount'+item[i].value).value,$('item_pcount'+item[i].value));
//			if(!isok2) {
//				return false;
//			}
		}
	}
	return true;
}

function key_check(val, id)
{
	if(isNaN(parseFloatEx(val)) || parseFloatEx(val)<0) {
		alert("Invalid input.");
		$(id).focus();
		return false;
	}
	$(id).value = parseFloatEx(val);
	return true;
}

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}

function searchDrug()
{
	if($('encounter_nr').innerHTML=="") {
		alert("Please select a patient first.");
		$('name').focus();
		return false;
	} else {
		$J('#search-meds').val('');
		$('items-list').list.params={};
		$('items-list').list.refresh();
		$J('#select-drug').dialog('open')
	}
}

function addExternalDrug()
{
	if($('search-meds').value=="") {
		alert("Please enter the name of drug.");
		$('search-meds').focus();
		return false;
	}
	else {
		addDrug('', $('search-meds').value,0,'',0,'','','Outside');
	}
}

function addTemplate(code,name,qty,dosage,pcount,pinterval,generic,availability,is_resctricted,has_license)
{
	if(is_resctricted==1 && has_license==0) {
		alert("This is a dangerous drug and you don't have a license to prescribe this kind of drug.")
		return false;
	}else {
		addDrug(code,name,qty,dosage,pcount,pinterval,generic,availability);
	}
}

function openPrescriptionTemplates()
{
	if($('encounter_nr').innerHTML=="") {
		alert("Please select a patient first.");
		$('name').focus();
		return false;
	} else {
		$J('#search-template').val('');
		$('templates-list').list.params={};
		$('templates-list').list.refresh();
		$J('#select-template').dialog('open');
	}
}

function openPatientHistory()
{
	alert("Coming soon! :)");
}

function openPatientSelect()
{
 <?php
$var_arr = array(
	"var_pid"=>"pid",
	"var_encounter_nr"=>"encounter_nr",
	"var_name"=>"name",
	"var_addr"=>"address",
	"var_age"=>"age",
	"var_gender"=>"gender",
	"var_adm_diagnosis"=>"diagnosis",
	"var_clear"=>"clear-enc",
	"var_include_walkin"=>"0",
	"var_reg_walkin"=>"0"
);
$vas = array();
foreach($var_arr as $i=>$v) {
	$vars[] = "$i=$v";
}
$var_qry = implode("&",$vars);
?>
		overlib(
				OLiframeContent('<?=$root_path?>modules/registration_admission/seg-select-enc.php?<?=$var_qry?>&var_include_enc=1',
				700, 400, 'fSelectPatient', 0, 'no'),
				WIDTH,700, TEXTPADDING,0, BORDER,0,
				FILTER,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src="<?= $root_path ?>images/close_red.gif" border="0" >',
				CAPTIONPADDING,2,
				CAPTION,'Select registered person',
				MIDX,0, MIDY,0,
				STATUS,'Select registered person');
	 return false;
}

function clearEnc()
{
	$('pid').value="";
	$('name').value="";
	$('address').value="";
	$('complaint').value="";
	$('diagnosis').value="";
	$('age').value="";
	$('gender').value="";
	$('clear-enc').disabled=true;
}

function searchTemplate()
{
	$('templates-list').list.params = {'name':$('search-template').value};
	$('templates-list').list.refresh();
}

function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g," ");
}

function addEmptyDrug()
{
	if($('search-meds').value=="") {
		alert("Please enter the name of drug.");
		$('search-meds').focus();
		return false;
	}
	else if($('drug_code').value=="" && $('search-meds').value!="") {
		alert("Item does not exist from pharmacy databank.")
		return false;
	}
	trimString($('drug_name'))
    
    //code,name,qty,dosage,pcount,pinterval,generic,availability
	addDrug(
        $('drug_code').value, //code
        $('drug_name').value, // name
        0, // qty
        '', // dosage
        '', // pcount
        '', // pinterval
        $('drug_generic').value,
        $('drug_avail').value
    );
	$('search-meds').value="";
	$('search-meds').focus();
}

function addDrug(code,name,qty,dosage,pcount,pinterval,generic,availability)
{
	var tableId = $('prescriptionlist');
	if(availability=="")
		availability = "Available";

	if($('item_code'+MD5(name))) {
        alert("Item already added to list of medicines.")
        return false;
	} else {
        if(tableId) {
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
								'<span style="color:#660000;font-weight:bold">'+generic.toUpperCase()+'</span>'+
								'<input type="hidden" name="item_name[]" value="'+name+'"/>'+
								'<input type="hidden" name="row_id[]" value="'+MD5(name)+'"/>'+
								'<br/>'+
								'<span style="font:normal 10px Arial">'+name+'</span>'+
							'</td>'+
							'<td align="center"><span style="color:#080">'+availability+'</span><br/></td>'+
							'<td align="center">'+
								'<input type="text" class="segInput" name="item_qty[]" id="item_qty'+MD5(name)+'" value="'+qty+'" style="width:100%;text-align: left" onfocus="this.select()" onblur="key_check(this.value, this.id);"/>'+
								'<input type="hidden" name="item_code[]" id="item_code'+MD5(name)+'" value="'+code+'"/>'+
							'</td>'+
							'<td align="center" style="padding: 4px">'+
								'<textarea class="segInput" type="text" name="item_dosage[]" id="item_dosage'+MD5(name)+'" onfocus="this.select()" style="width:100%;text-align: left;" rows="2">'+dosage+'</textarea>'+
							'</td>'+
							'<td align="center" nowrap="nowrap">'+
								'<input class="segInput" type="text" onfocus="this.select()" id="item_pcount'+MD5(name)+'" name="item_pcount[]" style="width:30%;text-align:left" value="'+pcount+'" />&nbsp;'+
								'<select class="segInput" style="width:60%" name="item_pinterval[]" id="pinterval'+MD5(name)+'">'+
                                    '<option value="" '+(pinterval==''?'selected="selected"':'')+'>None</option>'+
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

function remove_item(id) {
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


function disableControls() {
	$$('input, select, button, textarea').each(function(element) {
		$(element).disabled = true;
	})

	$$('img.link').each(function(element) {
		$(element).setAttribute('onclick','').removeClassName('link').addClassName('disabled');
	})
}


function append_empty_list() {
	var table = $('prescriptionlist').getElementsByTagName('tbody').item(0);
	var row = document.createElement("tr");
	var cell = document.createElement("td");
	row.id = "row_empty";
	cell.appendChild(document.createTextNode('No medicines added...'));

	cell.colSpan = "6";
	row.appendChild(cell);
	$('prescriptionlist').getElementsByTagName('tbody').item(0).appendChild(row);
}

/*function printPrescription()
{
    //added by VAN 10-01-2012
    var as_grp=0;
    if (confirm('Print as a group?')) as_grp=1; else as_grp=0;
    
	var url = "<?=$root_path?>modules/prescription/seg-clinic-print-prescription.php?encounter_nr="+$('encounter_nr').innerHTML+"&prescription_id="+$('prescription_id').value+"&as_grp="+as_grp;
	window.open(url,'Rep_Gen','menubar=no,directories=no');
}*/

function initialize()
{
	ListGen.create( $('items-list'), {
		id: 'additem',
		url: '<?=$root_path?>modules/prescription/ajax/populateDrugTable.ajax.php',
		width: '512px',
		height: 'auto',
		autoLoad: true,
		columnModel: [
			{
				name: 'drug_name',
				label: 'Drug Name',
				width: 200,
				sortable: false
			},
			{
				name: 'drug_qty',
				label: 'Quantity',
				width: 70,
				sortable: false,
				styles: {
					textAlign: 'right'
				}
			},
			{
				name: 'drug_dosage',
				label: 'Dosage',
				width: 100,
				sortable: false
			},
			{
				name: 'drug_period',
				label: 'Period',
				width: 80,
				sortable: false
			},
			{
				name: 'options',
				label: '',
				width: 70,
				sortable: false
			}
		]
	});

	ListGen.create( $('templates-list'), {
		id: 'addtemplate',
		url: '<?=$root_path?>modules/prescription/ajax/populateStandardPrescription.ajax.php',
		width: 'auto',
		height: 'auto',
		autoLoad: true,
		maxRows: 5,
		columnModel: [
			{
				name: 'template_name',
				label: 'Template',
				width: 85,
				sortable: true,
				sorting: ListGen.SORTING.asc
			},
			{
				name: 'template_owner',
				label: 'Owner',
				width: 85,
				sortable: false
			},
			{
				name: 'drug_name',
				label: 'Drug Name',
				width: 150,
				sortable: true,
				sorting: ListGen.SORTING.none
			},
			{
				name: 'drug_qty',
				label: 'Qty',
				width: 30,
				sortable: false,
				styles: {
					textAlign: 'right'
				}
			},
			{
				name: 'drug_dosage',
				label: 'Dosage',
				width: 70,
				sortable: false
			},
			{
				name: 'drug_period',
				label: 'Period',
				width: 70,
				sortable: false
			},
			{
				name: 'options',
				label: '',
				width: 130,
				sortable: false
			}
		]
	});
}

// jQuery onDomReady
$J(function(){
	$J('#search-meds').autocomplete({
		minLength: 1,
		source: '<?=$root_path?>modules/prescription/ajax/suggestMedicines.ajax.php',
//		focus: function(event, ui) {
//			$J('#search-meds').val(ui.item.name);
//			return false;
//		},
		select: function(event, ui) {
			// NOTE: put onSelect logic here
			if(ui.item.restricted==1 && ui.item.is_licensed==0) {
				alert("This is a dangerous drug and you don't have a license to prescribe this kind of drug.")
				return false;
			}
			$('drug_code').value =  ui.item.code;
			$('drug_name').value =  ui.item.value;
			$('drug_generic').value =  ui.item.generic;
			$('drug_avail').value =  ui.item.availability;
			$('items-list').list.params = {'name':ui.item.value, 'code':ui.item.code};
			$('items-list').list.refresh();

			$J('#search-meds').val(ui.item.label);
			return false;
		}
	})
	.data( "autocomplete" )._renderItem = function( ul, item ) {

		return $J( "<li></li>" )
			.data( "item.autocomplete", item )
			.append(
				"<a>" +
					'<span style="font-weight:bold;color:'+(item.restricted=='1'?'#ff0000':'#000066')+'">' + item.generic + '</span>' +
					"<br/>" +
					'<span style="font:normal 10px Arial;color:'+(item.restricted=='1'?'#ff0000':'#404040')+'">' + item.name + ' (' + item.availability +')</span>' +
				"</a>" )
			.appendTo( ul );
	};


	$J('#select-drug').dialog({
		title: 'Add drug',
		autoOpen: false,
		width: 540,
		height: 380,
		modal: true,
		show: 'fade',
		hide: 'fade',
		resizable: false,
		closeOnEscape: true,
		close: function() {
		}
	});

	$J('#select-template').dialog({
		title: 'Add standard prescription',
		autoOpen: false,
		width: 650,
		height: 380,
		modal: true,
		show: 'fade',
		hide: 'fade',
		resizable: false,
		closeOnEscape: true,
		close: function() {
		}
	});
    
    //added by VAN 11-12-2012
    $J('#printgrpDialog').dialog({
        title: 'Select Yes or No',
        autoOpen: false,
        width: 250,
        height: 100,
        modal: true,
        show: 'fade',
        hide: 'fade',
        resizable: false,
        closeOnEscape: true,
        close: function() {
        }
    });
    //------------------
    
    
    // Enter for activating input fields
    $J('#search-template').keyup( function(e) {
        if (e.keyCode === 13) {
            $J(this).next().click();
        }
        e.preventDefault();
    });
});

//added by VAN 11-12-2012
function printPrescription(){
    $J('#printgrpDialog').dialog('open');
    
    $$('button').each(function(element) {
        $(element).disabled = false;
    })
}

function printAsGrp(as_grp){
  $J('#printgrpDialog').dialog('close');  
  
  var url = "<?=$root_path?>modules/prescription/seg-clinic-print-prescription.php?encounter_nr="+$('encounter_nr').innerHTML+"&prescription_id="+$('prescription_id').value+"&as_grp="+as_grp;
  window.open(url,'Rep_Gen','menubar=no,directories=no');
}

//------------------------

document.observe('dom:loaded', initialize);
</script>

<?
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
$curDate = date($dbtime_format);
$curDate_show = date($fulltime_format);
global $db;

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform">');
$smarty->assign('sFormEnd','</form>');

$license = $pres_obj->getPrescriptionLicense();
$smarty->assign('doctorLicenseNr', '<span style="color:#0000C0">'.($license!=false?$license:'none').'</span>');
$smarty->assign('clinicalImpression', $pres_obj->getLatestClinicalImpression($_GET["encounter_nr"]));

//$smarty->assign('sPatientEnc', '<input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_GET["encounter_nr"].'"/>');
$smarty->assign('sPatientEnc', '<span style="font:bold 12px Arial; color: #006000" id="encounter_nr" name="encounter_nr">'.$_GET["encounter_nr"].'</span>');
//get patient information
$sql = "SELECT e.pid, fn_get_person_name(e.pid) AS `name`, fn_get_age(DATE(NOW()), date_birth) AS `age`, \n".
						"IF(p.sex='f','Female','Male') AS `sex`, fn_get_complete_address(e.pid)   AS `address` \n".
						"FROM care_encounter AS e \n".
						"INNER JOIN care_person AS p ON e.pid=p.pid \n".
						"WHERE e.encounter_nr=".$db->qstr($_GET["encounter_nr"]);
$info = $db->GetRow($sql);

$smarty->assign('sPatientID', '<input id="pid" name="pid" class="clear" type="text" value="'.$info["pid"].'" readonly="readonly" style="font:bold 12px Arial; color: #006000" />');
$smarty->assign('sPatientName','<input class="segInput" id="name" name="name" type="text" size="28" readonly="readonly" value="'.$info["name"].'"/>');
$smarty->assign('sPatientAddress', '<textarea class="segInput" id="address" name="address" cols="35" rows="2" readonly="readonly">'.$info['address'].'</textarea>');

//$smarty->assign('sPatientComplaint', '<textarea class="segInput" id="complaint" name="complaint"cols="34" rows="2" readonly="readonly">'.$_POST['pcomplaint'].'</textarea>');
//$smarty->assign('sPatientDiagnosis', '<textarea class="segInput" id="diagnosis" name="diagnosis" cols="34" rows="2" readonly="readonly">'.($_POST['pcomplaint']).'</textarea>');
$smarty->assign('sPatientAge', '<input type="text" class="segInput" id="age" name="age" size="10" readonly="" value="'.$info['age'].'"/>');
$smarty->assign('sPatientGender', '<input type="text" class="segInput" id="gender" name="gender" size="10" readonly="" value="'.$info['sex'].'"/>');

//$smarty->assign('sSelectEnc','<button id="select-enc" class="button" onclick="openPatientSelect();return false;"><img '.createComIcon($root_path, 'user.png').'/>Select</button>');
//$smarty->assign('sClearEnc','<button class="segButton" id="clear-enc" onclick="if (confirm(\'Search for another patient?\')) clearEnc(); return false" disabled="disabled">Clear</button>');

$smarty->assign('sRequestDate','<span id="show_requestdate" class="segInput" style="font-weight:bold; color:#0000c0; width:200px;">'.($submitted ? date($fulltime_format,strtotime($_POST['requestdate'])) : $curDate_show).'</span>
<input class="segInput" name="requestdate" id="requestdate" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['requestdate'])) : $curDate).'" style="font:bold 12px Arial">');

$smarty->assign('sCalendarIcon','<img '.createComIcon($root_path,'show-calendar.gif','0').' id="requestdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
$jsCalScript = "<script type=\"text/javascript\">
Calendar.setup({
		displayArea : \"show_requestdate\",
			inputField : \"requestdate\",
			ifFormat : \"%Y-%m-%d %H:%M\",
			daFormat : \" %B %e, %Y %I:%M%P\",
			showsTime : true,
			button : \"requestdate_trigger\",
			singleClick : true,
			step : 1
});
</script>";
$smarty->assign('jsCalendarSetup', $jsCalScript);
$smarty->assign('sSaveOptions','<select id="save_option" name="save_option" class="input">
	<option value=""><option>
</select>');
$smarty->assign('sPrescriptionTags', '<input type="text" class="segInput" name="prescription_tag" id="prescription_tag" size="60" style="font:bold 12px Arial;" value="'.$_POST['prescription_tag'].'"/>');
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
$smarty->clear_compiled_tpl('clinics/prescription.tpl');
$smarty->assign('sMainBlockIncludeFile','clinics/prescription.tpl');
$smarty->display('common/mainframe.tpl');

?>