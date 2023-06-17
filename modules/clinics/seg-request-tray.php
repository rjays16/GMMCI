<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/clinics/ajax/lab-new.common.php");
require($root_path.'include/inc_environment_global.php');

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab();

require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

$title=$LDLab;
$breakfile=$root_path."modules/clinics/seg-close-window.php".URL_APPEND."&userck=$userck";
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
 $smarty->assign('sToolbarTitle',"Add Service Item from Request Tray");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Add Service Item from Request Tray");

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
 $lab_area = $_GET['labarea'];


?>
<script language="javascript" >
<!--
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";

/*
function prepareAddEx() {
	//alert(prepareAddEx);
	var prod = document.getElementsByName('prod[]');
	//var qty = document.getElementsByName('qty[]');
	var discount_name = document.getElementsByName('discount_name[]');
	var prcCash = document.getElementsByName('prcCash[]');
	var prcCharge = document.getElementsByName('prcCharge[]');
	var nm = document.getElementsByName('pname[]');
	
	var details = new Object();
	var list = window.opener.document.getElementById('order-list');
	var result=false;
	var msg = "";
	for (var i=0;i<prod.length;i++) {
		result = false;
		if (prod[i].checked) {
			details.id = prod[i].value;
			details.name = nm[i].value;
			details.discount_name = discount_name[i].value;
			details.prcCash = prcCash[i].value;
			details.prcCharge = prcCharge[i].value;
			result = window.opener.appendOrder(list,details);
			//msg += "     x" + discount_name[i].value + " " + nm[i].value + "\n";
			discount_name[i].value = 0;
			prod[i].checked = false;
		}
	}
	window.opener.refreshTotal();
	if (msg)
		//msg = "The following laboratory services were added to the request tray:\n" + msg;
		msg = "The following laboratory services were added to the request tray:\n";
	else
		msg = "An error has occurred! The selected laboratory services were not added...";	
	alert(msg);
}
*/

//added by VAN 03-03-08
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
		startAJAXSearch(searchID,0);
	}else{
		return true;
	}		
}
/*
function startAJAXSearch(searchID) {
	var searchEL = $(searchID);
	var keyword;
	//alert(searchEL.value);
	var aLabServ = $("parameterselect").value;
	//alert(aLabServ);
	keyword = searchEL.value;
	keyword = keyword.replace("'","^");
	
	//if (searchEL && lastSearch != searchEL.value) {
	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		//AJAXTimerID = setTimeout("xajax_populateLabServiceList('"+searchID+"','"+searchEL.value+"')",200);
		//AJAXTimerID = setTimeout("xajax_populateLabServiceList('"+aLabServ+"','"+searchID+"','"+searchEL.value+"')",200);
		AJAXTimerID = setTimeout("xajax_populateLabServiceList('"+aLabServ+"','"+searchID+"','"+keyword+"')",200);
		lastSearch = searchEL.value;
	}
}
*/

function startAJAXSearch(searchID, page) {
	var searchEL = $(searchID);
	var keyword;
	//alert(searchEL.value);
	var aLabServ = $("parameterselect").value;
    var labArea = $("labarea").value;
	
	var area = $("area").value;
	//alert(area);
	//alert(aLabServ);
	keyword = searchEL.value;
	keyword = keyword.replace("'","^");
	
	//if (searchEL && lastSearch != searchEL.value) {
	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		//AJAXTimerID = setTimeout("xajax_populateLabServiceList('"+area+"','"+aLabServ+"','"+searchID+"','"+keyword+"',"+page+"','"+labArea+"')",100);
        AJAXTimerID = setTimeout("xajax_populateLabServiceList('"+area+"','"+aLabServ+"','"+searchID+"','"+keyword+"',"+page+",'"+labArea+"')",100);
		lastSearch = searchEL.value;
	}
}

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		//$("RequestList-body").style.display = "";
		searchEL.style.color = "";
	}
}

//added by VAN 03-07-08
function setPagination(pageno, lastpage, pagen, total) {
	currentPage=parseInt(pageno);
	lastPage=parseInt(lastpage);	
	firstRec = (parseInt(pageno)*pagen)+1;
	
	//alert('currentPage, lastPage, firstRec, total = '+currentPage+", "+lastPage+", "+firstRec+", "+total);
	
	if (currentPage==lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;
	
	//$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
	//$("pageShow").innerHTML = '<span>Showing '+formatNumber((firstRec),2)+'-'+formatNumber((lastRec),2)+' out of '+formatNumber((parseInt(total)),2)+' record(s).</span>';
	
	if (parseInt(total)==0)
		$("pageShow").innerHTML = '<span>Showing '+(lastRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
	else
		$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
	
	$("pageFirst").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pagePrev").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pageNext").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pageLast").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
	
}

function jumpToPage(el, jumpType, set) {
	if (el.className=="segDisabledLink") return false;
	if (lastPage==0) return false;
	//alert(jumpType);
	//alert(currentPage+", "+lastPage);
	switch(jumpType) {
		case FIRST_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',0);
		break;
		case PREV_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',parseInt(currentPage)-1);
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(currentPage)+1);
		break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(lastPage));
		break;
	}
}


function enableSearch(){
	//alert(enableSearch);
	var rowSrc, list;
	document.getElementById("search").value="";
	list = $('product-list');
	dBody=list.getElementsByTagName("tbody")[0];
	rowSrc = '<tr><td colspan="9" style="">No such laboratory service exists...</td></tr>';
	dBody.innerHTML = null;
	dBody.innerHTML += rowSrc;
	
	$("pageShow").innerHTML = '';
	
	if (document.getElementById("parameterselect").value!="none"){
		document.getElementById("search").disabled = false;       //enable textbox for searching
		document.getElementById("search_img").disabled = false;   //enable image
	}else{
		document.getElementById("search").disabled = true;       //enable textbox for searching
		document.getElementById("search_img").disabled = true;   //enable image
	}	
}

function getDeptDocValues(encounter_nr){
	xajax_getDeptDocValues(encounter_nr);
}

function setDeptDocValues(dept_nr, doc_nr){
	document.getElementById('dept_nr').value = dept_nr;
	document.getElementById('dr_nr').value = doc_nr;
	//alert('here');
	setAllDeptDoc();
}

function setAllDeptDoc(){
	//xajax_setALLDepartment(0);	//set the list of ALL departments
	//var dr_nr = '<?=$dr_nr?>';
	var dr_nr;
	//var dept_nr = '<?=$dept_nr?>';
    var dept_nr;
    var is_dr = '<?=$is_doctor?>';
	
	//if ((dr_nr=="")||(dr_nr==0))
    if(is_dr==1)
         dr_nr = '<?=$dr_nr?>';    
    //if ((document.getElementById('dr_nr').value!='') || (document.getElementById('dr_nr').value!=0))
    else
		dr_nr = document.getElementById('dr_nr').value;
			
	//if ((dept_nr=="")||(dept_nr==0))
    if(is_dr==1)   
        dept_nr = '<?=$dept_nr?>';         
    //if ((document.getElementById('dept_nr').value!='') || (document.getElementById('dept_nr').value!=0))
    else
		dept_nr = document.getElementById('dept_nr').value;
	//alert('d = '+dr_nr);	
	//alert('d = '+document.getElementById('dept_nr').value);	
	xajax_setALLDepartment(dept_nr);	//set the list of ALL departments
	xajax_setDoctors(dept_nr,dr_nr);	//set the list of ALL doctors from ALL departments
}

function preSet(){
	var encounter_nr = window.parent.document.getElementById('encounter_nr').value;
	
	getDeptDocValues(encounter_nr);
	//alert('preset = '+document.getElementById('dept_nr').value);
	document.getElementById('parameterselect').focus();
	startAJAXSearch('search',0);
	
}

// -->
</script> 
<script type="text/javascript" src="<?=$root_path?>modules/laboratory/js/request-tray-gui.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<?php
$xajax->printJavascript($root_path.'classes/xajax');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>

	<table width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%">
		<tbody>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<div style="padding:4px 2px; padding-left:10px; ">
						<table width="95%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
							<tr>
								<td class="segPanelHeader" colspan="2">
									Request Details
								</td>
							</tr>
							<tr>
								<td valign="top" width="30%" align="right"><strong>Requesting Dept</strong></td>
								<td align="left">
									<select name="request_dept_in" id="request_dept_in" onChange="jsSetDoctorsOfDept();">
									</select>
								</td>
							</tr>
							<tr>
								<td valign="top" width="30%" align="right"><strong>Requesting Doctor</strong></td>
								<td align="left">
									<select name="request_doctor_in" id="request_doctor_in" onChange="jsSetDepartmentOfDoc();">
									</select>
							</tr>		
							<tr>		
								<td valign="top" width="30%" align="right"><strong>Non-Resident Doctor</strong></td>
								<td align="left">
									<input type="text" name="request_doctor_out" id="request_doctor_out" size=40 onBlur="trimString(this);" value="">
									<!--<input type="text" name="request_doctor" id="request_doctor" value="">
									<input type="text" name="request_doctor_name" id="request_doctor_name" value="">-->
									<input type="hidden" name="is_in_house" id="is_in_house" value="">
									<script language="javascript">
										/*
										//xajax_setALLDepartment(0);	//set the list of ALL departments
										var dr_nr = '<?=$dr_nr?>';
										var dept_nr = '<?=$dept_nr?>';
										//alert(document.getElementById('dr_nr').value);
										//alert(document.getElementById('dept_nr').value);
										if (dr_nr=="")
											//dr_nr = 0;
											dr_nr = document.getElementById('dr_nr').value;
			
										if (dept_nr=="")
											//dept_nr = 0;	
											dept_nr = document.getElementById('dept_nr').value;
			
										xajax_setALLDepartment(dept_nr);	//set the list of ALL departments
										//xajax_setDoctors(0,0);	//set the list of ALL doctors from ALL departments
										xajax_setDoctors(0,dr_nr);	//set the list of ALL doctors from ALL departments
										*/
									</script>
								</td>
							</tr>
							<tr>		
								<td valign="top" width="30%" align="right"><strong>Other Hospital Dept</strong></td>
								<td align="left">
									<input type="text" name="request_dept_out" id="request_dept_out" size=40 onBlur="trimString(this);" value="">
									<!--<input type="text" name="request_dept" id="request_dept" value="">
									<input type="text" name="request_dept_name" id="request_dept_name" value="">-->
									<!--<input type="text" name="is_in_house_dept" id="is_in_house_dept" value="">
									-->
								</td>
							</tr>
							<tr>
								<td valign="top" width="30%" align="right">
									<strong>Clinical Impression</strong>
								</td>
								<td align="left">
                                    <?php
                                           # $clinical_info = 
                                           #print_r($HTTP_SESSION_VARS);
                                           #echo "e = ".$HTTP_SESSION_VARS['sess_en'];
                                           $rs_encounter = $enc_obj->getPatientEncounter($HTTP_SESSION_VARS['sess_en']);
                                           $clinical_info = '';
                                           if ($rs_encounter['er_opd_diagnosis'])
                                                $clinical_info = $rs_encounter['er_opd_diagnosis'];
                                           elseif ($rs_encounter['chief_complaint'])     
                                                $clinical_info = $rs_encounter['chief_complaint'];
                                    ?>
                                        
									<textarea name="clinical_info" id="clinical_info" cols=30 rows=2 wrap="physical" onChange="trimString(this);" onBlur="trimString(this);"><?=$clinical_info?></textarea>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr style='display:none'>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<div style="padding:4px 2px; padding-left:10px; ">
						Laboratory Service Section &nbsp;&nbsp;&nbsp;&nbsp;<!--<select name="parameterselect" id="parameterselect" onChange="enableSearch();">-->
						<select name="parameterselect" id="parameterselect" onChange="document.getElementById('search').value=''; startAJAXSearch('search',0)">
								<!--<option value="none">All Laboratory Service Section</option>-->
								<option value="0">All Laboratory Service Section</option>
								<?php
										$all_labgrp=&$srvObj->getLabServiceGroups2();
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
					</div>
				</td>
			</tr>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<div style="padding:4px 2px; padding-left:10px; ">
						Search Laboratory Test<input id="search" name="search" class="segInput" type="text" style="width:50%; margin-left:10px; font: bold 12px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id,0)" onKeyPress="checkEnter(event,this.id)" />
						<input type="image" id="search_img" name="search_img" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0);return false;" align="absmiddle" />
						<!--Search Request <input id="search" name="search" class="segInput" type="text" disabled style="width:51.5%; margin-left:10px; font: bold 12px Arial" align="absmiddle" onkeyup="startAJAXSearch(this.id)" />
						<img src="../../gui/img/common/default/redpfeil_l.gif">-->
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden; width:100%; background-color:#e5e5e5">
					<table class="segList" cellpadding="1" cellspacing="1" width="100%">
						<thead>
								<tr class="nav">
									<th colspan="9">
										<div id="pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE)">
											<img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
											<span title="First">First</span>
										</div>
										<div id="pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE)">
											<img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
											<span title="Previous">Previous</span>
										</div>
										<div id="pageShow" style="float:left; margin-left:10px">
											<span></span>
										</div>
										<div id="pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE)">
											<span title="Last">Last</span>
											<img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
										</div>
										<div id="pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE)">
											<span title="Next">Next</span>
											<img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
										</div>
									</th>
								</tr>
								<tr>
									<th width="*" align="left">&nbsp;Name/Description</th>
									<th width="15%" align="left">&nbsp;&nbsp;Code</th>
									<th style="font-size:11px" width="20%" align="center">Cash&nbsp;&nbsp;&nbsp;&nbsp;</th>
									<th style="font-size:11px" width="20%" align="center">Charge&nbsp;&nbsp;&nbsp;&nbsp;</th>
									<!--<th style="font-size:11px" width="20%" align="center">&nbsp;&nbsp;C1&nbsp;&nbsp;</th>
									<th style="font-size:11px" width="20%" align="center">&nbsp;&nbsp;C2&nbsp;&nbsp;</th>
									<th style="font-size:11px" width="20%" align="center">&nbsp;&nbsp;C3&nbsp;&nbsp;</th>-->
									<!--<th width="15%">Discount Type</th>-->
									<th width="8%"></th>
								</tr>
							</thead>
					</table>
					</div>
					<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:160px; width:100%; background-color:#e5e5e5">
						<table id="product-list" class="segList" cellpadding="1" cellspacing="1" width="100%">
							<tbody>
								<tr>
									<td colspan="9" style="font-weight:bold">No such laboratory service exists...</td>
								</tr>
							</tbody>
						</table>
						<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
					</div>
				</td>
			</tr>
		</tbody>
	</table>


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
    <input type="hidden" name="labarea" id="labarea" value="<?=$lab_area?>" />


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
