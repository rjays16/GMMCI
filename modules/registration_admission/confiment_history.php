<?php
/**
* added by shandy
* for cert of confiment
* 08/28/2013
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/registration_admission/ajax/med_cert.common.php");
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
$encObj=new Encounter();

require_once($root_path.'include/care_api_classes/class_person.php');
$persObj=new Person();

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
 $pid = $_GET["pid"];
$title=$LDLab;
$breakfile=$root_path."modules/registration_admission/seg-close-window.php".URL_APPEND."&userck=$userck";
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
 $x = "onLoad='preSet(\"$pid\");'";
 $smarty->assign('sOnLoadJs',$x);

 # Collect javascript code
 ob_start();

 $area = $_GET['area'];

 if($result = $persObj->getAllInfoObject($pid))
 {
		 if($person=$result->FetchRow())
		 {
				 $patient_name = "";
				 $space = "&nbsp;";
				 if (!empty($person["name_last"])) $patient_name .= $person["name_last"];
				 if (!empty($person["name_first"])) {
						if (!empty($patient_name))
								$patient_name = $patient_name.",".$space;
						$patient_name = $patient_name.$person["name_first"];
				 }
				 if (!empty($person["name_middle"])) {
						if (!empty($patient_name)) $patient_name .= $space;
						$patient_name .= $person["name_middle"];
				 }

				 list($y,$m,$d) = explode("-",$person["date_birth"]);
				 $bdate = $m."/".$d."/".$y;
				 $age = (int)$persObj->getAge($bdate);
				 if($person["sex"]=="m"||$person["sex"]=="M")
						$sex="Male";
				 else
						$sex="Female";
				 $civil_status = $person["civil_status"];
		 }
 }
 

?>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script language="javascript" >
<!--
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";



//added by VAN 03-03-08
function checkEnter(e,searchID,pid){
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
				startAJAXSearch(searchID,0,pid);
		}else{
				return true;
		}
}


function startAJAXSearch(searchID, page,pid) {

		var searchEL = $(searchID);
		var keyword;
		//var aLabServ = $("parameterselect").value;

		//var area = $("area").value;
		//alert(area);
		//alert(aLabServ);
		keyword = searchEL.value;
		keyword = keyword.replace("'","^");
		//if (searchEL && lastSearch != searchEL.value) {
		//alert("keyword = '"+keyword+"'");
		if (searchEL) {
				searchEL.style.color = "#0000ff";
				if (AJAXTimerID) clearTimeout(AJAXTimerID);
				$("ajax-loading").style.display = "";
				//raisa dito para magadd ng laman dun sa table
				AJAXTimerID = setTimeout("xajax_populateConfiCertEncRefHistory('"+pid+"','"+searchID+"',"+page+",'"+keyword+"',true)",100);
				lastSearch = searchEL.value;
		}
}

function endAJAXSearch(searchID) {
		var searchEL = $(searchID);
		//alert("here na sa endajax");
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

		
		if (parseInt(total)==0)
				$("pageShow").innerHTML = '<span>Showing '+(lastRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
		else
				$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';

		$("pageFirst").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
		$("pagePrev").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
		$("pageNext").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
		$("pageLast").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";

}

function clearList(listID) {
		// Search for the source row table element
		var list=$(listID),dRows, dBody;
		if (list) {
				dBody=list.getElementsByTagName("tbody")[0];
				if (dBody) {
						dBody.innerHTML = "";
						return true;    // success
				}
				else return false;    // fail
		}
		else return false;    // fail
}

function jumpToPage(el, jumpType, set, pid) {
		if (el.className=="segDisabledLink") return false;
		if (lastPage==0) return false;
		//alert(jumpType);
		//alert(currentPage+", "+lastPage);
		switch(jumpType) {
				case FIRST_PAGE:
						if (currentPage==0) return false;
						startAJAXSearch('search',0,pid);
				break;
				case PREV_PAGE:
						if (currentPage==0) return false;
						startAJAXSearch('search',parseInt(currentPage)-1,pid);
				break;
				case NEXT_PAGE:
						if (currentPage >= lastPage) return false;
						startAJAXSearch('search',parseInt(currentPage)+1,pid);
				break;
				case LAST_PAGE:
						if (currentPage >= lastPage) return false;
						startAJAXSearch('search',parseInt(lastPage),pid);
				break;
		}
}

function addProductToList(listID, pid, id, date_prepared, enc_nr, ref_nr, date_admitted, requested_by, create_id,cert_nr,attending_doctor) {
		var list=$(listID), dRows, dBody, rowSrc, case_nr;
		case_nr = enc_nr;
		if(ref_nr!="")
				case_nr = case_nr +" (" +ref_nr+")";
		//rowSrc = '';
		if (list) {
		 //alert(pid);
				dBody=list.getElementsByTagName("tbody")[0];
				dRows=dBody.getElementsByTagName("tr");


				if (id) {
						
					 var reagent ='onclick="return overlib(OLiframeContent(\'certificates/cert_conf_history_interface.php?encounter_nr='+enc_nr+'&referral_nr='+ref_nr+'&showBrowser=1&cert_nr='+cert_nr+'\', 750, 380, \'flab-list\', 1, \'auto\'), ' +
										'WIDTH, 380, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 >\', '+
										'CAPTIONPADDING, 4, CAPTION, \'Confiment Certificate\', MIDX, 0, MIDY, 0, STATUS, \'Medical Certificate\');">';

						var reagentlink = '<td align="center"><a href="javascript:void(0);" '+reagent+'<img src="../../images/cashier_edit.gif" border="0"></a></td>';
						
						rowSrc = '<tr class="wardlistrow" id="row'+id+'">'+
													'<td>'+date_prepared+'</span></td>'+
													'<td>'+case_nr+'</span></td>'+
													'<td>'+date_admitted+'</td>'+
													'<td>'+requested_by+'</td>'+
													'<td>'+attending_doctor+'</td>'+
													'<td>'+create_id+'</td>'+
														reagentlink+
											'</tr>';
				}
				else {
						rowSrc = '<tr><td colspan="8" style="">No encounters with Confinment Certificate...</td></tr>';
				}
				dBody.innerHTML += rowSrc;
				//alert(dBody.innerHTML);
		}
}

function enableSearch(){
		//alert(enableSearch);
		var rowSrc, list;
		document.getElementById("search").value="";
		list = $('product-list');
		dBody=list.getElementsByTagName("tbody")[0];
		rowSrc = '<tr><td colspan="8s" style="">No such encounter exists...</td></tr>';
		dBody.innerHTML = null;
		dBody.innerHTML += rowSrc;

		$("pageShow").innerHTML = '';


}

function preSet(pid){
		
		startAJAXSearch('search',0, pid);
}

function NewMedCert(pid){
		return overlib(OLiframeContent('med_cert_encounters.php?pid='+pid, 790, 380, 'historyList', 1, 'auto'),
										WIDTH, 380, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, '<img src=../../images/close.gif border=0 onClick="startAJAXSearch(\'search\', 0, '+pid+');">',
										CAPTIONPADDING, 4, CAPTION, 'List of Encounters', MIDX, 0, MIDY, 0, STATUS, 'List of Encounters');
}


function deleteConfCert(encounter_nr, pid){

	//alert(encounter_nr+" - "+cert_nr);
	var answer = confirm("Are you sure you want to delete the Confinment Certificate with a Case no. "+(encounter_nr)+"?");
	if (answer){
		xajax_deleteCertificateConf(encounter_nr,pid);
	}
}
// -->
</script>
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
												<table width="99%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
														<tr>
																<td class="segPanelHeader" colspan="6">
																		Patient Details
																</td>
														</tr>
														<tr>
																<td width="15%" align="left"><strong>Patient Name&nbsp;</strong></td>
																<td align="left" width="28%">
																		<input type="text" name="patient_name" size=40 readonly="readonly" value=<?=$patient_name?>>
																</td>
																<td width="16%" align="right"><strong>Age&nbsp;</strong></td>
																<td align="left" width="12%">
																		<input type="text" name="age" size=3 readonly="readonly" value=<?=$age?>>
																</td>
																<td width="16%" align="right"><strong>Birth Date&nbsp;&nbsp;&nbsp;</strong></td>
																<td align="left" width="*">
																		<input type="text" name="birth_date" size=10 readonly="readonly" value=<?=$bdate?>>
																</td>
														</tr>
														<tr>
																<td width="15%" align="left"><strong>HRN&nbsp;</strong></td>
																<td align="left" width="28%">
																		<input type="text" name="pid" size=15 readonly="readonly" value=<?=$pid?>>
																</td>
																<td width="16%" align="right"><strong>Sex&nbsp;</strong></td>
																<td align="left" width="12%">
																		<input type="text" name="sex" size=6 readonly="readonly" value=<?=$sex?>>
																</td>
																<td width="16%" align="right"><strong>Civil Status&nbsp;</strong></td>
																<td align="left" width="*">
																		<input type="text" name="civil_status" size=10 readonly="readonly" value=<?=$civil_status?>>
																</td>
														</tr>
												</table>
										</div>
								</td>
						</tr>
						<tr>
								<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
										
												<input id="search" name="search" type="hidden">
										
								</td>
						</tr>
						<tr>
								<td>
										<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden; width:100%; background-color:#e5e5e5">
										<table class="segList" cellpadding="1" cellspacing="1" width="100%">
												<thead>
																<tr class="nav">
																		<th colspan="8">
																				<div id="pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE,0,'<?=$pid?>')">
																						<img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
																						<span title="First">First</span>
																				</div>
																				<div id="pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE,0,'<?=$pid?>')">
																						<img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
																						<span title="Previous">Previous</span>
																				</div>
																				<div id="pageShow" style="float:left; margin-left:10px">
																						<span></span>
																				</div>
																				<div id="pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE,0,'<?=$pid?>')">
																						<span title="Last">Last</span>
																						<img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
																				</div>
																				<div id="pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE,0,'<?=$pid?>')">
																						<span title="Next">Next</span>
																						<img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
																				</div>
																		</th>
																</tr>
														</thead>
										</table>
										</div>
										<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:190px; width:100%; background-color:#e5e5e5">
												<table id="product-list" class="segList" cellpadding="1" cellspacing="1" width="100%">
														<thead>
															<tr>
																		<th width="10%" align="left">Date Prepared</th>
																		<th width="10%" align="center">Case Number</th>
																		<th style="font-size:11px" width="10%" align="center">Date Admitted</th>
																		<th style="font-size:11px" width="17%" align="center">Requested by</th>
																		<th style="font-size:11px" width="*" align="center">Attending Doctor</th>
																		<th style="font-size:11px" width="21%" align="center">Created by</th>
																		<th style="font-size:11px" width="5%" align="center">Details</th>
																</tr>
														</thead>
														<tbody>
																<tr>
																		
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

		<input type="hidden" name="area" id="area" value="<?=$area?>">
		<input type="hidden" name="dr_nr" id="dr_nr" value="<?=$_GET['dr_nr']?>" />


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
