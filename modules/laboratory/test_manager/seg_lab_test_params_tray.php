<?php
#created by cha, june 20,2010
#manager for laboratory tests - new test service parameters

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_core.php');

require_once($root_path.'modules/laboratory/test_manager/ajax/seg_lab_test.common.php');

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_lab_user';

require_once($root_path.'include/inc_front_chain_lang.php');
$thisfile=basename(__FILE__);

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

require_once($root_path.'include/care_api_classes/class_lab_results.php');
$labres_obj = new Lab_Results();

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$lab_obj = new SegLab();

$smarty->assign('sToolbarTitle',"Tests Manager::New Test Service Paramater");
$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

ob_start();
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/or/js/flexigrid/lib/jquery/jquery.js"></script>
<script>var J = jQuery.noConflict();</script>
<script type="text/javascript">

function init()
{
	$('param_name').focus();
	if($('mode').value=="new")
		xajax_newOrderNo('<?=$_GET["service_code"]?>','<?=$_GET["grp_id"]?>');
}

function openParamsTray(mode, caption, param_id)
{
	var params="mode="+mode;
	if(mode=="edit")
		params+="&group_id="+id;

	return overlib(
		OLiframeContent('../../../modules/laboratory/test_manager/seg_lab_test_params_tray.php?'+params,
		550, 350, 'fWizard', 0, 'no'),
		WIDTH,550, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="../../../images/close_red.gif" border=0 >',
		CAPTIONPADDING,2,
		CAPTION, caption,
		MIDX,0, MIDY,0,
		STATUS, caption);
}

function validate()
{
	if($('param_name').value=='')
	{
		alert('Please fill in parameter name.');
		$('param_name').focus();
		return false;
	}
	if(!$('param_group').value)
	{
		alert('Please assign a group to this parameter.');
		$('param_group').focus();
		return false;
	}
	if($('order_no').value=='')
	{
		alert('Please fill in order number.');
		$('order_no').focus();
		return false;
	}
	/*if($('si_low').value=='' || $('si_high').value=='' || $('si_unit').value=='')
	{
		alert('Please fill in SI range.');
		$('si_low').focus();
		return false;
	}
	if($('cu_low').value=='' || $('cu_high').value=='' || $('cu_unit').value=='')
	{
		alert('Please fill in CU range.');
		$('cu_low').focus();
		return false;
	}*/
	return true;
}

function isInteger() {
	var res;
	if(res=parseInt($('order_no').value).toString())
	{
		alert(res)
		return false;
	}else{
		alert("Integer values only."+res);
		return false;
	}
	//return (s.toString().search(/^-?[0-9]+$/) == 0);
}

function addParams()
{
	if(validate())
	{
		var details = new Object();
		details.name = $('param_name').value;
		details.datatype = $('data_type').value;
		details.gender = $('gender').value;
		details.order_nr = $('order_no').value;
		details.si_low = $('si_low').value;
		details.si_high = $('si_high').value;
		details.si_unit = $('si_unit').value;
		details.cu_low = $('cu_low').value;
		details.cu_high = $('cu_high').value;
		details.cu_unit = $('cu_unit').value;
		details.service_code = $('service_code').value;
		details.param_group = $('param_group').value;
		details.test_group = $('grp_id').value;
		
		if($('mode').value=='new')
			xajax_saveTestParameter(details);
		if($('mode').value=='edit')
			xajax_updateTestParameter($('param_id').value,details);
		return false;
	}
}

function outputResponse(rep)
{
	alert(rep);
	window.parent.$('parameter-list').list.refresh();
	window.parent.cClick();
}

function assignDatatype(val)
{
	alert("assign datatype")
	var dtypes = document.getElementsByName('data_type');
	alert('datatype='+dtypes.length);
	for(i=0;i<dtypes.length;i++)
	{
		if(dtypes[i].value==val)
		{
			dtypes[i].selected = true;
			alert(val)
		}
	}
}

function assignGender(val)
{
	alert("assign gender")
	var dtypes = document.getElementsByName('gender');
	alert('gender='+dtypes.length);
	for(i=0;i<dtypes.length;i++)
	{
		if(dtypes[i].value==val)
		{
			dtypes[i].selected = true;
			alert(val)
		}
	}
}

function key_check(e, value) {
	//var value=$('order_no').value;
	var character = String.fromCharCode(e.keyCode);
	 var number = /^\d+$/;

	 //if (e.keyCode==9 || e.keyCode==116) {
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

function checkExistingParam(val) {
	return false;
	if(val!="") {
		xajax_checkExistingParam(val, $('service_code').value);
	}
}

function checkAlert(rep, message) {
	if(rep) {
		alert(message)
		$('param_name').focus();
		return false;
	}
	else {
		return false;
	}
}

document.observe('dom:loaded', init);
</script>
<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$smarty->assign('form_start','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="addparam" id="addparam">');
$smarty->assign('form_end','</form>');

$service_code = $_POST['service_code']? $_POST['service_code']:$_GET['service_code'];
$smarty->assign('service_code', '<input type="hidden" value="'.$service_code.'" id="service_code" name="service_code"/>');
$param_id = $_POST['param_id']? $_POST['param_id']:$_GET['param_id'];
$smarty->assign('param_id', '<input type="hidden" value="'.$param_id.'" id="param_id" name="param_id"/>');
$mode = $_POST['mode']? $_POST['mode']:$_GET['mode'];
$smarty->assign('mode', '<input type="hidden" value="'.$mode.'" id="mode" name="mode"/>');

$data = $lab_obj->GetParameterData($param_id,$service_code);
if($data["is_boolean"]==$data["is_numeric"] && $data["is_numeric"]==$data["is_numeric"])
{
	$datatype="4";
}
else if($data["is_boolean"])
{
	$datatype="2";
}
else if($data["is_numeric"])
{
	$datatype="1";
}
else if($data["is_longtext"])
{
	$datatype="3";
}


/*if($data["is_male"]==$data["is_female"])
{
	$gender="2";
}
else if($data["is_female"])
{
	$gender="1";
}
else if($data["is_male"])
{
	$gender="0";
}
*/

/*added by mai 09052014*/
$gender = 0;
$gender_options = array("None", "Infant", "Children", "Male", "Female");

if($data['norm_type']){
	$gender = array_search(ucfirst($data['norm_type']), $gender_options);
}
/*end added by mai*/
$smarty->assign('paramName', '<input type="text" class="segInput" id="param_name" name="param_name" value="'.$data["name"].'" onblur="checkExistingParam(this.value);"/>');
$smarty->assign('orderNumber', '<input type="text" class="segInput" id="order_no" name="order_no" value="'.$data["order_nr"].'"/>');

$options="";
for($i=0;$i<count($gender_options);$i++)
{
	if($gender==$i)
	{
		$options.="<option value='".strtolower($gender_options[$i])."' selected=''>".$gender_options[$i]."</option>";
	}else
	{
		$options.="<option value='".strtolower($gender_options[$i])."'>".$gender_options[$i]."</option>";
	}
}
/*$options='<option value="2">Both</option>'.
				'<option value="1">Female</option>'.
				'<option value="0">Male</option>';*/
$smarty->assign('gender', '<select class="segInput" id="gender" name="gender">'.$options.'</select>');

$datatype_options = array("1"=>"Numeric", "2"=>"Boolean", "3"=>"Long", "4"=>"Text");
$options="";
for($i=1;$i<=count($datatype_options);$i++)
{
	if($datatype==$i)
	{
		$options.="<option value='".$i."' selected=''>".$datatype_options[$i]."</option>";
	}else
	{
		$options.="<option value='".$i."'>".$datatype_options[$i]."</option>";
	}
}
/*$options='<option value="4">Text</option>'.
				'<option value="3">Long</option>'.
				'<option value="1">Numeric</option>'.
				'<option value="2">Boolean</option>'; */
$smarty->assign('dataTypes', '<select class="segInput" id="data_type" name="data_type">'.$options.'</select>');

$smarty->assign('siLow','<input type="text" class="segInput" id="si_low" name="si_low" style="width:45px" value="'.$data["SI_lo_normal"].'"/>');
$smarty->assign('siHigh','<input type="text" class="segInput" id="si_high" name="si_high" style="width:45px" value="'.$data["SI_hi_normal"].'"/>');
$smarty->assign('siUnit','<input type="text" class="segInput" id="si_unit" name="si_unit" style="width:45px" value="'.$data["SI_unit"].'"/>');
$smarty->assign('cuLow','<input type="text" class="segInput" id="cu_low" name="cu_low" style="width:45px" value="'.$data["CU_lo_normal"].'"/>');
$smarty->assign('cuHigh','<input type="text" class="segInput" id="cu_high" name="cu_high" style="width:45px" value="'.$data["CU_hi_normal"].'"/>');
$smarty->assign('cuUnit','<input type="text" class="segInput" id="cu_unit" name="cu_unit" style="width:45px" value="'.$data["CU_unit"].'"/>');

$options="<option>-Select group-</option>";
if($param_groups = $lab_obj->getParamGroups())
{
	while($row = $param_groups->FetchRow())
	{
		if($row["param_group_id"]==$data["param_group_id"])
			$options.="<option value='".$row["param_group_id"]."' selected=''>".$row["name"]."</option>";
		else
		$options.="<option value='".$row["param_group_id"]."'>".$row["name"]."</option>";
	}
}
$smarty->assign('paramGroups', '<select class="segInput" id="param_group" name="param_group">'.$options.'</select>');

$testgrp_name = $_POST['grp_name']? $_POST['grp_name']:$_GET['grp_name'];
$testgrp_id = $_POST['grp_id']? $_POST['grp_id']:$_GET['grp_id'];

if(!$testgrp_name || !$testgrp_id)
{
	$sql = "SELECT gn.group_id, gn.name FROM seg_lab_result_groupparams AS gp ".
				"LEFT JOIN seg_lab_result_groupname AS gn ON gp.group_id=gn.group_id ".
				"WHERE gn.status<>'deleted' AND gp.service_code=".$db->qstr($service_code);
	$data = $db->GetRow($sql);
	$testgrp_id = $data["group_id"];
	$testgrp_name = $data["name"];
}
$smarty->assign('testGroup', '<input type="text" readonly="" class="segInput" id="grp_name" name="grp_name" value="'.$testgrp_name.'"/>');
$smarty->assign('testGroupid', '<input type="hidden" id="grp_id" name="grp_id" value="'.$testgrp_id.'"/>');

/*added by mai 09082014*/
/*$options1="<option>-Select group-</option>";
if($data = $lab_obj->getGroups())
{
	while($value = $data->FetchRow())
	{
		if($value["group_id"] == $testgrp_id)
			$options1.="<option value='".$value["group_id"]."' selected=''>".$value["name"]."</option>";
		else
			$options1.="<option value='".$value["group_id"]."'>".$value["name"]."</option>";
	}
}

$smarty->assign('testGroup', '<select class="segInput" id="grp_id" name="grp_id">'.$options1.'</select>');*/
/*end added by mai*/
$smarty->assign('saveBtn', '<button class="segButton" onclick="addParams();return false;"><img src="'.$root_path.'gui/img/common/default/bullet_disk.png"/>Save</button>');
$smarty->assign('cancelBtn', '<button class="segButton" onclick="javascript: window.parent.cClick();"><img src="'.$root_path.'gui/img/common/default/cross.png"/>Cancel</button>');
ob_start();

$bShowThisForm = TRUE;
$smarty->assign('sMainBlockIncludeFile','laboratory/test_manager/params_tray.tpl');
$sTemp = '';
$smarty->assign('sMainFrameBlockData',$sTemp);

$smarty->display('common/mainframe.tpl');
?>