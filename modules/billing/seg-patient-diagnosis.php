<?php
#created by VAN 06-21-08
# Modified by LST - 03.29.2009 ---- to allow user at billing department to add ICD.
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/billing/ajax/seg-patient-diagnosis.common.php");
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

$thisfile=basename(__FILE__);

$cat = "pharma";
$title="Patient Records::History";
$breakfile=$root_path."modules/registration_admission/seg-close-window.php".URL_APPEND."&userck=$userck";
$imgpath=$root_path."pharma/img/";

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
 $smarty->assign('sToolbarTitle',"$title");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad="preSet();"');
 $smarty->assign('sOnUnLoadJs',"javascript:if (window.parent.myClick) window.parent.myClick(); else window.parent.cClick();"); 

 require_once($root_path.'include/care_api_classes/class_encounter.php');
 $enc_obj=new Encounter;
	
 require_once($root_path.'include/care_api_classes/class_personell.php');
 $pers_obj=new Personell;

 require_once($root_path.'include/care_api_classes/class_department.php');
 $dept_obj=new Department;
	
 require_once($root_path.'include/care_api_classes/class_person.php');
 $person_obj=new Person();
	
 require_once($root_path.'include/care_api_classes/class_ward.php');
 $ward_obj = new Ward;    

 $pid = $_GET['pid'];
 $encounter_nr = $_GET['encounter_nr'];
 
  $frombilling = $_GET['frombilling'];
  
 $person = $person_obj->getAllInfoArray($pid);
# echo "sql = ".$person_obj->sql;
 extract($person);
 
 $name = $name_first." ".$name_2." ".$name_middle." ".$name_last;
 
 # Collect javascript code
 ob_start();  
?>
<!---------added by VAN----------->
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
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

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">
<!--
.olbg {
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	background-color:#0000ff;
	border:1px solid #4d4d4d;
}
.olcg {
	background-color:#aa00aa; 
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
	background-color:#ffffcc; 
	text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
	font-family:Arial; font-size:13px; 
	font-weight:bold; 
	color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}

a {color:#338855;font-weight:bold;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style> 

<!-- ICD10 ENTRY BLOCK -->
<style type="text/css">
/*margin and padding on body element
  can introduce errors in determining
  element position and are not recommended;
  we turn them off as a foundation for YUI
  CSS treatments. */
body {
	margin:0;
	padding:0;
}
</style>



<script language="javascript" >

function preSet() {
	startAJAXSearch('search',0);
}
	
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";
	
function startAJAXSearch(searchID, page) {
	var searchEL = $(searchID);
	var encounter;
	
	encounter_nr = document.getElementById('encounter_nr').value;
	
	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		$("DiagnosisList-body").style.display = "none";		
		AJAXTimerID = setTimeout("xajax_populateFinalDiagnosisList('"+encounter_nr+"','"+searchID+"',"+page+")",100);		 
		lastSearch = searchEL.value;
	}
}

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		$("DiagnosisList-body").style.display = "";
		searchEL.style.color = "";
	}
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

function addslashes(str) {
	str=str.replace("'","\\'");
	return str;
}

function trimString(objct){
//    alert("inside frunction trimString: objct = '"+objct+"'");
	objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g,"");
}

function editAltDesc(id) {
	$("descalt_"+id).style.display = "";
	$("descmain_"+id).style.display = "none";
	$("descalt_"+id).focus();    
}

function cancelAltDesc(id) {
	$("descalt_"+id).style.display = "none";
	$("descmain_"+id).style.display = "";
}

function isESCPressed(e) {
	var kC  = (window.event) ?    // MSIE or Firefox?
			 event.keyCode : e.keyCode;
	var Esc = (window.event) ?   
			27 : e.DOM_VK_ESCAPE // MSIE : Firefox
	return (kC==Esc);
}

function applyAltDesc(e, id) {
	var characterCode; 
	var enc_nr   = $('encounter_nr').value;
	var user_id  = $('create_id').value;
	
	if (e) {
		if(e && e.which) { //if which property of event object is supported (NN4)
			characterCode = e.which; //character code is contained in NN4's which property
		}
		else {
			characterCode = e.keyCode; //character code is contained in IE's keyCode property
		}
	}
	else
		characterCode = 13;

	if ( (characterCode == 13) || (isESCPressed(e)) ) {       
		var altdesc = $("descalt_"+id).value;
		if (altdesc != '') {
			$("descmain_"+id).innerHTML = '<a style="cursor:pointer" onclick="editAltDesc('+id+')">'+altdesc+'</a>';
			
			// At this point, save the encoded alternate description for the ICD code in table ...
			xajax_updateDiagnosis(enc_nr, id, altdesc, user_id);
			
		}        
		$("descalt_"+id).style.display = "none";
		$("descmain_"+id).style.display = "";
	}
}

function addDiagnosisToList(listID, entry_no, description) {
	var list=$(listID), dRows, dBody, rowSrc;
	var i, stmp;

	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		//alert(dBody.id);
		dRows=dBody.getElementsByTagName("tr");
		
		var rows = document.getElementsByName('rows[]');
		if (rows.length == 0) {
			clearList(list);			            
		}		
		
		if (entry_no) {            
			//alert("after diagnosis_nr, code, description, doctor =  "+diagnosis_nr+" , "+code+" , "+description+" , "+doctor);
			alt = (dRows.length%2)+1;        
			create_id = $('create_id').value;
			stmp = '<img style="cursor:pointer" title="Remove!" src="../../images/cashier_delete.gif" border="0" onclick="delSelectedDiagnosis('+entry_no+', \''+create_id+'\');"/>';
			
			rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+addslashes(entry_no)+'">'+
							'<input type="hidden" name="rows[]" id="index_'+addslashes(entry_no)+'" value="'+addslashes(entry_no)+'" />'+ 
							'<td><input style="width:95%;display:none;" type="text" id="descalt_'+addslashes(entry_no)+'" value="'+description+'" onFocus="this.select();" onblur="cancelAltDesc(\''+addslashes(entry_no)+'\');" onkeyup="applyAltDesc(event,\''+addslashes(entry_no)+'\');">'+
								'<span id="descmain_'+addslashes(entry_no)+'"><a style="cursor:pointer" onclick="editAltDesc('+addslashes(entry_no)+')">'+description+'</a></span></td>'+
							'<td align="right">'+ stmp + '</td>'+ 
					 '</tr>';    
					
			//alert(rowSrc);                                
		}
		else {
			rowSrc = '<tr><td colspan="2">No final diagnosis entered yet ...</td></tr>';
		}
		
		dBody.innerHTML += rowSrc;
		//alert(dBody.innerHTML);
	}
}

function reclassRows(list,startIndex) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var dRows = dBody.getElementsByTagName("tr");
			if (dRows) {
				for (i=startIndex;i<dRows.length;i++) {
					dRows[i].className = "wardlistrow"+(i%2+1);
				}
			}
		}
	}
}

function removeDiagnosisInList(id) {
	var destTable, destRows;
	var table = $('DiagnosisList');
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {        
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);        
		if (!document.getElementsByName("rows[]") || document.getElementsByName("rows[]").length <= 0)
			addDiagnosisToList(table, null);           
		reclassRows(table,rndx);                
	}
	else
		alert(table+' and '+rmvRow);
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

//function addICDCode(create_id) {
//	var enc_nr   = $('encounter_nr').value;
//	var enc_type = $('encounter_type').value;
//	var discharge_dt = $('discharge_dt').value;
//	var date_d = ((discharge_dt == '') || (discharge_dt == '0000-00-00 00:00:00')) ? $('admission_dt').value : discharge_dt;    
//	var icd_code = $('icdCode').value;
//	var dr_nr = $('current_dr_nr').value;    
//	
//	xajax_addCode(enc_nr, enc_type, date_d, icd_code, dr_nr, create_id, 1);
//}

function addDiagDesc(create_id) {
	var enc_nr   = $('encounter_nr').value;
//	var enc_type = $('encounter_type').value;
//	var discharge_dt = $('discharge_dt').value;
//	var date_d = ((discharge_dt == '') || (discharge_dt == '0000-00-00 00:00:00')) ? $('admission_dt').value : discharge_dt;    
//    var icd_code = $('icdCode').value;
	var diagdesc = $('diagdesc').value;
//	var dr_nr = $('current_dr_nr').value;
	
	xajax_addDiagnosis(enc_nr, diagdesc, create_id);
	$('diagdesc').value = ''; 
	startAJAXSearch('search',0);	
}

function delSelectedDiagnosis(entry_no, user_id) {
	var enc_nr = $('encounter_nr').value;
	xajax_remDiagnosis(enc_nr, entry_no, user_id);
}

function clearICDFields() {
//	$('icdCode').value = '';
	$('diagdesc').value = '';
}

// -->
</script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/medocs/js/ICDCodeParticulars.js"></script>
<?php
//$xajax->printJavascript($root_path.'classes/xajax');
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden;overflow-x:hidden; height:335px; width:99%; background-color:#e5e5e5">
	<table border="1" class="segPanel" cellspacing="2" cellpadding="2" width="99%" align="center">
		<tr>
			<td width="20%">Hospital Number</td>
			<td><strong><?=$pid?></strong></td>
		</tr>
		<tr>
			<td>Patient's Name</td>
			<td><strong><?=$name?></strong></td>
		</tr>
		<tr>
			<td>Case Number</td>
			<td><strong><?=$encounter_nr?></strong></td>
		</tr>
		<tr>
			<?php
				$encInfo = $enc_obj->getPatientEncounter($encounter_nr);                
			?>
			<td>Admitting Diagnosis</td>
			<td><textarea style="width:100%; overflow-x:hidden;" wrap="physical" readonly="readonly" cols="53" rows="3" ><?=$encInfo['er_opd_diagnosis']?></textarea></td>
		</tr>
	</table>
	<?php
		if ($frombilling){
	?>
	<table width="98%" cellpadding="0">
		<tr>
		<td width="20%">Final Diagnosis:</td>
		<td width="*" nowrap="nowrap" align="left" colspan="2">
			<input type="text" size="80" value="" id="diagdesc" name="diagdesc"/>        			
<!--		   <div id="icdAutoComplete">
				<input type="text" size="25" value="" id="icdCode" name="icdCode" onblur="trimString(this);" />
				<div id="icdContainer" style="width:35em"></div>                    
		   </div>
		</td>
		<td width="*" nowrap="nowrap" align="left">
		   <div id="icdDescAutoComplete">
				<input type="text" size="25" value="" id="icdDesc" name="icdDesc" onblur="trimString(this);" />
				<div id="icdDescContainer" style="width:40em"></div>                    
		   </div> -->
		</td>
		<td width="8%">
			<!--<input id="btnAddIcdCode" style="cursor:pointer" height="10" type="button" value="ADD" onclick="if (checkICDSpecific() && (document.getElementById('icdCode').value!='')){ addICDCode('<?= $HTTP_SESSION_VARS['sess_user_name'] ?>'); }" style="width:100%">-->
			<input id="btnAddIcdCode" style="cursor:pointer" height="10" type="button" value="ADD" onclick="if (document.getElementById('diagdesc').value!=''){ addDiagDesc('<?= $HTTP_SESSION_VARS['sess_user_name'] ?>'); }" style="width:100%">
		</td></tr>
	</table>
	<?php } ?>
	<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden; overflow-x:hidden; width:98%; background-color:#e5e5e5">
	<table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
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
				<input id="search" name="search" type="hidden" />
			</th>
		</tr>
		</thead>
	</table>
	</div>

	<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll;overflow-x:hidden; height:150px; width:98%; background-color:#e5e5e5">
		<table id="DiagnosisList" class="segList" width="200%" border="0" cellpadding="0" cellspacing="0" style="overflow:auto">
			<thead>
				<tr>
					<th width="*" align="left" colspan="2">Final Diagnosis (shown in Form 2)</th>
<!--					<th width="10%" align="left">ICD 10</th>
					<th width="50%" align="left">Diagnosis</th>
					<th colspan="2" width="*" align="left">Clinician</th>  -->                  
				</tr>
			</thead>
			<tbody id="DiagnosisList-body">                
			</tbody>
		</table>
		<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
	</div>    

	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">                   
	<input type="hidden" name="encounter_nr" id="encounter_nr" value="<?=$encounter_nr?>"/>
	<input type="hidden" name="encounter_type" id="encounter_type" value="<?= $encInfo["encounter_type"] ?>">
	<input type="hidden" name="admission_dt" id="admission_dt" value="<?= $encInfo["admission_dt"] ?>">
	<input type="hidden" name="discharge_dt" id="discharge_dt" value="<?= strftime("%Y-%m-%d", strtotime($encInfo["discharge_date"])). ' '.strftime("%H:%M:%S",  strtotime($encInfo["discharge_time"])) ?>">
	<input type="hidden" name="current_dr_nr" id="current_dr_nr" value="<?= $encInfo["current_att_dr_nr"] ?>">   
	<input type="hidden" name="gender" id="gender" value="<?= $encInfo["sex"] ?>">  
	<input type="hidden" name="create_id" id="create_id" value="<?= $HTTP_SESSION_VARS['sess_user_name'] ?>"/>   
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
//$smarty->assign('bgcolor',"class=\"yui-skin-sam\"");        // Added by LST -- 03.29.2009 
$smarty->assign('sMainFrameBlockData',$sTemp);

/**
* show Template
*/
$smarty->display('common/mainframe.tpl');
?>