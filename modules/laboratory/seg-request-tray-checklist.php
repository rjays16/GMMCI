<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'modules/special_lab/ajax/splab-service-tray.common.php');
require($root_path.'include/inc_environment_global.php');

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab();

require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

$title=$LDLab;
$breakfile=$root_path."modules/laboratory/seg-close-window.php".URL_APPEND."&userck=$userck";
#$imgpath=$root_path."pharma/img/";

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$title $LDLabDb $LDSearch");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title $LDLabDb $LDSearch");

 # Assign Body Onload javascript code
 #$smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');

 $smarty->assign('sOnLoadJs','onLoad="preSet();"');

 # Collect javascript code
 ob_start();

global $HTTP_SESSION_VARS;
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

global $HTTP_SESSION_VARS;

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

$seg_user_nr = $HTTP_SESSION_VARS['sess_temp_personell_nr'];

$personell = $pers_obj->get_Personell_info($seg_user_nr);
 #echo "s = ".$dept_obj->sql;

if (stristr($personell['job_function_title'],'doctor')===FALSE)
		$is_doctor = 0;
else
		$is_doctor = 1;

 $area = $_GET['area'];
 #echo "area = ".$area;
 $dr_nr = $_GET['dr_nr'];

 $ptype = $_GET['ptype'];

 if ($_GET['ref_source'])
	$ref_source = $_GET['ref_source'];

switch ($ref_source){
	case 'LB' :
							$caption = 'No such laboratory service exists...';
							break;
	case 'BB' :
							$caption = 'No such blood bank service exists...';
							break;
	case 'SPL' :
							$caption = 'No such special laboratory service exists...';
							break;
	case 'IC' :
							$caption = 'No such industrial clinic laboratory service exists...';
							break;
	default :
							$caption = 'No such laboratory service exists...';
							break;
}

#added by VAN 03-09-2011
	global $db;
	$encounter_nr = $_GET['encounter_nr'];
	$pid = $_GET['pid'];

	if (!$impression) {
		$impression = '';

		$impression = $enc_obj->getLatestImpression($pid, $encounter_nr);

	}
#----------

?>
<script type="text/javascript" src="<?=$root_path?>modules/laboratory/js/request-tray-checklist.js?t=<?=time()?>"></script>
<!--added by VAN 07-30-2010 -->
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/special_lab/js/splab-service-tray.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" href="<?=$root_path?>modules/laboratory/css/checklist.css" type="text/css"/>
<script language="javascript" >

function checkEnter(e,searchID){
	//alert('e = '+e);
	var characterCode; //literal character code will be stored in this variable

	if(e && e.which){ //if which property of event object is supported (NN4)
		e = e;
		characterCode = e.which; //character code is contained in NN4's which property
	}else{
		//e = event;
		characterCode = e.keyCode; //character code is contained in IE's keyCode property
	}

	if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
		//startAJAXSearch(searchID,0);
		search_checklist(searchID,0);
	}else{
		return true;
	}
}
//edited by VAN 07-30-2010
function populate_checklist(val, id) {
	//added by VAN 07-29-30
	iscash = window.parent.$("iscash1").checked;
	var is_cash;
	var discountid = window.parent.$('discountid').value;
	var discount = window.parent.$('discount').value;
	var var_ptype = window.parent.$('ptype').value;
	var area = window.parent.$('area').value;
	var user_origin = window.parent.$('user_origin');
	var ref_source;
	var encounter_nr = window.parent.$('encounter_nr').value;
	var is_senior = window.parent.$('issc').checked;
	var pat_walkin = window.parent.$('is_walkin').checked;
	var area_type = window.parent.$('area_type').value;
	var is_walkin = 0;

	var source_req = window.parent.$('source_req').value;
	var patient_enctype = window.parent.$('patient_enctype').innerHTML;
	var is_charge2comp = window.parent.$('is_charge2comp');
	var compID = window.parent.$('compID');
	var urgent = window.parent.$('priority1');
	var isStat = 0;


	if (is_charge2comp)
		is_charge2comp = is_charge2comp.value;
	else
		is_charge2comp = 0;

	if (compID)
		compID = compID.value;
	else
		compID = '';

	if (patient_enctype=='IC')
		source_req = 'IC';

	if (iscash==true){
		is_cash = 1;
		//$('type_charge').style.display='none';
	}else{
		is_cash = 0;
		//$('type_charge').style.display='';
	}

	if ((encounter_nr=="") || (pat_walkin))
			is_walkin = 1;

	if (urgent.checked)
		isStat = 1;

	switch (user_origin.value){
		case 'blood' :  ref_source = 'BB'; break;
		case 'lab' 	 :	ref_source = 'LB'; break;
		case 'splab' :  ref_source = 'SPL'; break;
		case 'iclab' :  ref_source = 'IC'; break;
	}

	if(((var_ptype==1)||(area=='ER'))&&(is_cash==0))
		ptype = "ER";
	else
		ptype = "";

	if (!is_senior){
		if (encounter_nr==""){
				 discountid = '';
				 discount = 0;
		}
	}

 //alert("ptype, ref, cash, id, dis, senior, walkin = "+ptype+"','"+ref_source+"','"+is_cash+"','"+discountid+"','"+discount+"','"+is_senior+"','"+is_walkin);
	xajax_populate_lab_checklist(val,area_type, id,ptype,ref_source,is_cash,discountid,discount,is_senior,is_walkin,source_req,isStat,is_charge2comp,compID,encounter_nr);
	$('search').readOnly=false;
}

function search_checklist(id, page) {
	//alert($(id).value)
	//xajax_populate_lab_checklist($('parameterselect2').value, $(id).value);
	//edited by VAN 07-30-2010

    //added by jasper 01/29/13
    var section = '<?=$_GET['section']?>';

    //alert(section);
    if (section)
        var aLabServ=section;
    else
	var aLabServ = $("parameterselect2").value;

	populate_checklist(aLabServ, $(id).value);
}

function preSet(){
	var encounter_nr = window.parent.document.getElementById('encounter_nr').value;
	//added by VAN 07-30-2010
	var text = 'PLEASE SELECT A LABORATORY SERVICE SECTION FIRST..';
	print_checklist_message(text);

	// edited by VAN 03-09-2011
	var impression_prev = '<?=str_replace("'", '`', str_replace("\r\n",'',$impression));?>';  // added check for single quote - LST 02.02.2012
	var impression;

	if (impression_prev=='')
		impression = window.parent.$('impression').value;
	else
		impression = impression_prev;

	var user_origin = window.parent.$('user_origin').value;
	var ref_source;
	//alert(user_origin);
	if (user_origin=='lab'){
		$('section').style.display = "";
	}else{
		$('section').style.display = "none";
		switch (user_origin){
			case 'blood' :  ref_source = 'B'; break;
			case 'splab' :  ref_source = 'SPL'; break;
			case 'iclab' :  ref_source = 'IC'; break;
		}
		populate_checklist(ref_source,'');
	}
	$('clinical_info').value = impression;
	//-------------
	getDeptDocValues(encounter_nr);
	populate_checklist('0','');
}

function getDeptDocValues(encounter_nr){
	xajax_getDeptDocValues(encounter_nr);
}

function setDeptDocValues(dept_nr, doc_nr){
	setAllDeptDoc();
	document.getElementById('dept_nr').value = dept_nr;
	document.getElementById('dr_nr').value = doc_nr;
}

function setAllDeptDoc(){
	var dr_nr, dept_nr, is_dr;
	var is_dr_get = '<?=$_GET["is_dr"]?>';
	var pdr_nr = window.parent.$('current_att_dr_nr').value;
	var pdept_nr = window.parent.$('current_dept_nr').value;

	//added by VAN 05-26-2011
	var login_user = window.parent.$('login_user').value;
	var is_dr_post = window.parent.$('is_dr').value;

	var dr_nr_get = '<?=$_GET["dr_nr"]?>';

	if (is_dr_get==1)
		is_dr = is_dr_get;
	else
		is_dr = is_dr_post;
	//---------------------



	if (is_dr==0){
		 dr_nr = "";
		 dept_nr = "";
	}else{
		 if (is_dr_post==1)
				dr_nr = login_user;
		 else
				dr_nr = '<?=$_GET["dr_nr"]?>';

		 dept_nr = '<?=$_GET["dept_nr"]?>';
	}

	if (dr_nr==""){
		if (pdr_nr=="")
			dr_nr = 0
		else
			dr_nr = pdr_nr
	}//else
		//dr_nr = 0

	if (dept_nr==""){
		if (pdept_nr=="")
			dept_nr = 0
		else
			dept_nr = pdept_nr
	}//else
		//dept_nr = 0

	xajax_setALLDepartment(dept_nr);	//set the list of ALL departments
	xajax_setDoctors(dept_nr,dr_nr);
}
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
<div>
	<table width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%;font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d">
		<tbody>
			<tr>
				<td class="segPanelHeader" colspan="3">Request Details</td>
			</tr>
			<tr>
				<!--<input type="hidden" id="request_dept_in" name="request_dept_in" value=""/>
				<input type="hidden" id="request_doctor_in" name="request_doctor_in" value=""/>-->
				<td class="segPanel">
					<table width="100%" style="font:bold 12px Arial; background-color:#e5e5e5;">
						<tr>
							<td valign="top" align="left" style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d; width:170px">Requesting Dept</td>
							<td align="left"><select class="segInput" name="request_dept" id="request_dept" onChange="jsSetDoctorsOfDept();"></select></td>
						</tr>
						<tr>
							<td valign="top" align="left" style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d; width:170px">Requesting Doctor</td>
							<td align="left"><select class="segInput" name="request_doctor_in" id="request_doctor_in" onChange="jsSetDepartmentOfDoc();"></select></td>
						</tr>
						<tr>
							<td valign="top" align="left" style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d; width:170px">Non-Resident Doctor</td>
							<td align="left">
								<input class="segInput" type="text" name="request_doctor_out" id="request_doctor_out" size=40 onBlur="trimString(this);" value="">
								<input type="hidden" name="request_doctor" id="request_doctor" value="">
								<input type="hidden" name="request_doctor_name" id="request_doctor_name" value="">
								<input type="hidden" name="is_in_house" id="is_in_house" value="">
							</td>
						</tr>
						<tr>
							<td valign="top" align="left" style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d; width:170px">Clinical Impression</td>
							<td align="left">
									<textarea name="clinical_info" id="clinical_info" cols=30 rows=2 wrap="physical" onChange="trimString(this);" onBlur="trimString(this);"><?=$impression?></textarea>
							</td>
						</tr>
						<tr id="section">
							<td valign="top" align="left" style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d; width:170px" >Laboratory Service Section &nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td align="left">
									<select class="segInput" name="parameterselect2" id="parameterselect2" onChange="populate_checklist(this.value,'')">
										<option value="0">All Laboratory Service Section</option>
											<?php
													$cond = "group_code NOT IN ('SPL','IC')";

													$all_labgrp=&$srvObj->getLabServiceGroups2('', $cond);
													if(!empty($all_labgrp)&&$all_labgrp->RecordCount()){
														while($result=$all_labgrp->FetchRow()){
															if(isset($parameterselect)&&($parameterselect==$result['group_code'])){
																echo "<option value=\"".$result['group_code']."\" selected>".$result['name']." \n";
																					 }else{
																						 echo "<option value=\"".$result['group_code']."\">".$result['name']." \n";
																					 }
														}
													}
											?>
								</select>
								<img src="../../gui/img/common/default/redpfeil_l.gif">
							</td>
						</tr>
						<tr>
							<td align="left" style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d;width:170px" >Search Laboratory Test</td>
							<td align="left">
								<input class="segInput" id="search" name="search" class="segInput" type="text" style="width:270px;font: bold 12px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) search_checklist(this.id,0)" onKeyPress="checkEnter(event,this.id)"  readonly="readonly"/>
								<input type="image" id="search_img" name="search_img" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="search_checklist('search',0);return false;" align="absmiddle"/>
						</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="dashlet" style="margin-top:5px; width:565px">
	<div id="checklist-div" style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:300px; width:100%; background-color:#e5e5e5" align="left">
	</div>
</div>
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">
	<?php
						if (empty($area)){
								if ($dr_nr)
										$area = 'clinic';
						}
		?>
	<input type="hidden" name="area" id="area" value="<?=$area?>">
	<input type="hidden" name="dr_nr" id="dr_nr" value="<?=$_GET['dr_nr']?>" />
	<input type="hidden" name="dept_nr" id="dept_nr" value="" />
	<input type="hidden" name="ptype" id="ptype" value="<?=$_GET['ptype']?>"/>
	<input type="hidden" name="caption" id="caption" value="<?=$caption?>">

<?php

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
</form>
<?php if ($from=="multiple")
echo '
<form name=backbut onSubmit="return false">
<input type="hidden" name="sid" value="'.$sid.'">
<input type="hidden" name="lang" value="'.$lang.'">
<input type="hidden" name="userck" value="'.$userck.'">
</form>
';
?>
</div>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
