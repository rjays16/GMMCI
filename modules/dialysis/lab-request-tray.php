<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/dialysis/ajax/dialysis-service-request.common.php");
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


?>
<script language="javascript" >
<!--
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";

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

function startAJAXSearch(searchID, page) {
	var searchEL = $(searchID);
	var keyword;
	var aLabServ = $("parameterselect2").value;

	var area = $("area").value;

	keyword = searchEL.value;
	keyword = keyword.replace("'","^");

	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		AJAXTimerID = setTimeout("xajax_populateLabServiceList('"+area+"','"+aLabServ+"','"+searchID+"','"+keyword+"',"+page+")",100);
		lastSearch = searchEL.value;
	}
}

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		searchEL.style.color = "";
	}
}

//added by VAN 03-07-08
function setPagination(pageno, lastpage, pagen, total) {
	currentPage=parseInt(pageno);
	lastPage=parseInt(lastpage);
	firstRec = (parseInt(pageno)*pagen)+1;

	if (currentPage==lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;

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

function preSet(){
	document.getElementById('parameterselect2').focus();
	startAJAXSearch('search',0);
}
</script>
<script type="text/javascript" src="js/lab-request-tray.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
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
				<td class="segPanel">
					<table width="100%" style="font:bold 12px Arial; background-color:#e5e5e5;">
						<tr>
							<td valign="top" align="left" style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d; width:170px" >Laboratory Service Section &nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td align="left">
									<select class="segInput" name="parameterselect2" id="parameterselect2" onChange="document.getElementById('search').value=''; startAJAXSearch('search',0)">
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
							</td>
						</tr>
						<tr>
							<td align="left" style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d;width:170px" >Search Laboratory Test</td>
							<td align="left">
								<input class="segInput" id="search" name="search" class="segInput" type="text" style="width:270px;font: bold 12px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id,0)" onKeyPress="checkEnter(event,this.id)" />
								<input type="image" id="search_img" name="search_img" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0);return false;" align="absmiddle"/>
						</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div style="margin-top:5px; width:565px">
	<table cellpadding="1" cellspacing="1" width="100%">
		<tbody>
			<tr>
				<td>
					<div style="margin-left:-10px;display:block; border:1px solid #8cadc0; overflow-y:hidden; width:580px; background-color:#e5e5e5">
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
									<th width="8%"></th>
								</tr>
							</thead>
					</table>
					</div>
					<div style="margin-left:-10px;display:block; border:1px solid #8cadc0; overflow-y:scroll; height:230px; width:580px; background-color:#e5e5e5">
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
