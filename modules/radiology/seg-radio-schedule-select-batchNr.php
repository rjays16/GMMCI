<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
#require($root_path."modules/pharmacy/ajax/order-psearch.common.php");
require_once($root_path.'modules/radiology/ajax/radio-undone-request.common.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','products.php');
#$local_user='ck_prod_db_user';
$local_user='ck_radio_user';   # burn added : November 28, 2007
	$lang_tables[] = 'departments.php';
	define('LANG_FILE','konsil.php');
	define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

#echo "seg-radio-schedule-select-batchNr.php : _GET : <br> \n"; print_r($_GET);echo "<br> \n";
$thisfile=basename(__FILE__);
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

#added by VAN 03-19-08
 $smarty->assign('sOnLoadJs','onLoad="preSet();DisabledSearch();"');	
 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--
var AJAXTimerID=0;
var lastSearch="";

function preSet(){
	document.getElementById('search').focus();
}

function startAJAXSearch(searchID) { 
//	var includeEnc = window.parent.$("iscash1").checked ? '0' : '1';
	var searchEL = $(searchID);
//	var searchLastname = $('firstname-too').checked ? '1' : '0';
	var key, pgx, thisfile, rpath, sub_dept_nr;		
//alert("startAJAXSearch :: searchID = '"+searchID+"'");
	if (searchEL) {	
		rpath = document.getElementById('rpath').value;
		setPgx(0);   // resets to the first page every time a tab is clicked
		pgx = document.getElementById('pgx').value;
		thisfile = document.getElementById('thisfile').value; 
		sub_dept_nr = document.getElementById('sub_dept_nr').value; 
		oitem = 'create_dt'; 
		odir = 'ASC'; 
		
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		$("person-list-body").style.display = "none";
		AJAXTimerID = setTimeout("xajax_PopulateRadioUnscheduledRequest('"+searchID+"','"+searchEL.value.replace("'","^")+
				"','"+sub_dept_nr+"','"+pgx+"','"+thisfile+"','"+rpath+"','"+oitem+"','"+odir+"')",100);
		lastSearch = searchEL.value;
	}
}

function jsSortHandler(items,oitem,dir,sub_dept_nr){
	var key, pgx, thisfile, rpath, mode, searchEL;
		
	setOItem(items);
	setODir(dir);

//	mode = document.getElementById('smode').value;
	rpath = document.getElementById('rpath').value;
	pgx = document.getElementById('pgx').value;
//	key = document.getElementById('skey').value;
	searchEL = document.getElementById('search');
	thisfile = document.getElementById('thisfile').value; 
 	oitem = document.getElementById('oitem').value; 
	odir = document.getElementById('odir').value; 

	
	xajax_PopulateRadioUnscheduledRequest('search',searchEL.value.replace("'","^"),sub_dept_nr,pgx,thisfile,rpath,oitem,odir);
}//end of function jsSortHandler
	
function setTotalCount(val){
	$('totalcount').value=val;
}

function setPgx(val){
	$('pgx').value=val;
}

function setOItem(val){
	$('oitem').value=val;
}

function setODir(val){
	$('odir').value=val;
}

function jsRadioNoFoundUnscheduledRequest(){
	var dTable,dTBody,rowSrc;
	dTable=document.getElementById('person-list');
//alert("jsRadioNoFoundUnscheduledRequest :: dTable = '"+dTable+"'");
	if (dTable=document.getElementById('person-list')) {
		dTBody=dTable.getElementsByTagName("tbody")[0];
		rowSrc = '<tr><td colspan="10" style="">No such record exists...</td></tr>';;
		dTBody.innerHTML += rowSrc;
//alert("jsRadioNoFoundUnscheduledRequest : dTBody.innerHTML : \n"+dTBody.innerHTML);
	}
}

function jsRadioUnscheduledRequest(No,batchNo,refNo,service_code,dateRq,rid,lName,fName,bDate,rPriority, pType){
	var dTable,dTBody,rowSrc,id;
	var patient_type;

	if (dTable=document.getElementById('person-list')) {

		dTBody=dTable.getElementsByTagName("tbody")[0];

	//added by VAN 04-29-08
	//alert('pType = '+pType);
	
	if (pType==1)
		patient_type = 'ERPx';
	else if (pType==2)
		patient_type = 'OPDPx';
	else if ((pType==3)||(pType==4))
		patient_type = 'INPx';
	else
		patient_type = 'WPx';				
	
	
//alert("jsRadioUnscheduledRequest : No ='"+No+"' \nbatchNo ='"+batchNo+"'");
		if(batchNo){
//alert("jsRadioUnscheduledRequest :  if(batchNo) is true 1 : rowSrc="+rowSrc);
			id = batchNo;
				
				rowSrc = '<tr>'+
					'<td>'+No+'</td>'+
					'<td>'+
						batchNo+
						'<input type="hidden" id="batchNo'+id+'" value="'+batchNo+'">'+
					'</td>'+
					'<td>'+
						refNo+
						'<input type="hidden" id="batchNo'+id+'" value="'+refNo+'">'+
					'</td>'+
					'<td><span id="service_code'+id+'">'+service_code+'</span></td>'+
					'<td>'+dateRq+'</td>'+
					'<td>'+rid+'</td>'+
					'<td><span id="lname'+id+'">'+lName+'</span></td>'+
					'<td><span id="fname'+id+'">'+fName+'</span></td>'+
					'<td>'+patient_type+'<input type="hidden" id="patientType'+id+'" value="'+pType+'"></td>'+
					'<td>'+rPriority+'</td>'+
					'<td align="center">'+
						'<input type="button" value=">" style="color:#000066; font-weight:bold; padding:0px 2px" '+
						'onclick="prepareSelect(\''+id+'\')">'+
					'</td>'+
					'</tr>';	
					//'<td>'+bDate+'</td>'+
//alert("jsRadioUnscheduledRequest :  if(batchNo) is true 2 : rowSrc="+rowSrc);
		}else{
			rowSrc = '<tr><td colspan="10" style="">No such record exists...</td></tr>';
		}
		dTBody.innerHTML += rowSrc;
//alert("jsRadioUnscheduledRequest : dTBody.innerHTML : \n"+dTBody.innerHTML);
	}
} 

function prepareSelect(id) {
//alert("prepareSelect :: id ='"+id+"'");
	var lname = $('lname'+id).innerHTML;
	var fname = $('fname'+id).innerHTML;
	var batchNo = $('batchNo'+id).value;
	var service_code = $('service_code'+id).innerHTML;
	window.parent.$('batchNo').value = batchNo;
	window.parent.$('p_name').value = fname + " " + lname;
	window.parent.$('batchDisplay').innerHTML = batchNo;
	window.parent.$('service_code').innerHTML = service_code;
	window.parent.$('clear-batchNr').disabled=false;
	
	//added by VAN 04-29-08
	var patientType;
	
	if ($('patientType'+id).value==1)
		patientType = "ERPx";
	else if ($('patientType'+id).value==2)
		patientType = "OPDPx";	
	else if (($('patientType'+id).value==3)||($('patientType'+id).value==4))
		patientType = "INPx";	
		
	//alert(patientType);
	window.parent.$('patientType').value = $('patientType'+id).value;
	window.parent.$('ptypeDisplay').innerHTML = patientType;
	
	window.parent.cClick();
}

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
//	var searchEL = $('search');
//alert("endAJAXSearch :: searchID = '"+searchID+"' \nsearchEL = '"+searchEL+"'");
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		$("person-list-body").style.display = "";
		searchEL.style.color = "";
	}
}

function startAJAXSearch2(searchID, page) { 
	var includeEnc = window.parent.$("iscash1").checked ? '0' : '1';
	var searchEL = $(searchID);
	var searchLastname = $('firstname-too').checked ? '1' : '0';
	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		$("person-list-body").style.display = "none";
		AJAXTimerID = setTimeout("xajax_populatePersonList('"+searchID+"','"+searchEL.value+"',"+page+","+searchLastname+","+includeEnc+")",100);
//PopulateRadioUnscheduledRequest($searchkey,$sub_dept_nr, $pgx, $thisfile, $rpath, $mode, $oitem, $odir )
		lastSearch = searchEL.value;
	}
}

//added by VAN 03-19-08
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

//added by VAN 01-29-10
function isValidSearch(key) {          

    if (typeof(key)=='undefined') return false;
    var s=key.toUpperCase();
    return (
            /^[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*\s*,\s*[A-Z0-9Ñ\-\.]{2}[A-Z0-9Ñ\-\. ]*$/.test(s) ||
            /^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
            /^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
            /^\d+$/.test(s)
    );
}
		 
function DisabledSearch(){
    var b=isValidSearch(document.getElementById('search').value);
    document.getElementById("search-btn").style.cursor=(b?"pointer":"default");
    document.getElementById("search-btn").disabled = !b;
}

function trimStringSearchMask(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g, "");
	objct.value = objct.value.replace(/\s+/g, " ");
}
//--------------------------


/*
function handleOnclick(){
// 	var tab = dojo.widget.byId('tbContainer').selectedChild;
 	var tab;
	var key, pgx, thisfile, rpath, sub_dept_nr, mode;		

 	try{
		tab = dojo.widget.byId('tbContainer').selectedChild;
	}catch(e){
		//alert("e.message = "+e.message);
		tab = 'tab0';   // use in initial loading
	}

 	mode = document.getElementById('smode').value;
 	rpath = document.getElementById('rpath').value;
	setPgx(0);   // resets to the first page every time a tab is clicked
 	pgx = document.getElementById('pgx').value;
	key = document.getElementById('skey').value;
 	thisfile = document.getElementById('thisfile').value; 
 	oitem = 'create_dt'; 
	odir = 'ASC'; 
	sub_dept_nr = tab.substr(3);
//	alert("handleOnclick: tab="+ tab + "\n key="+key+ "\n pgx ="+pgx+ "\n rpath="+rpath+"\n mode="+mode+ "\n sub_dept_nr="+ sub_dept_nr+"\n oitem="+oitem+"\n odir="+odir);

	xajax_

//	xajax_ColHeaderRadioRequest('T'+tab, 'TBody'+tab, key, sub_dept_nr, pgx, thisfile, rpath, mode,oitem,odir);
 }
*/

// -->
</script> 
<!--
<script type="text/javascript" src="<?=$root_path?>modules/pharmacy/js/order-person-search-gui.js?t=<?=time()?>"></script>
-->
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<?php
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
	<table width="98%" cellspacing="2" cellpadding="2" style="margin:1%">
		<tbody>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<table width="95%" border="0" cellpadding="0" cellspacing="0" style="font:bold 12px Arial; color:#2d2d2d; margin:1%">
						<tr>
							<td width="15%">
								Search person<br />
								<!--<a href="javascript:gethelp('person_search_tips.php')" style="text-decoration:underline">Tips & tricks</a>-->
							</td>
							<td valign="middle" width="*">
								<!--<input id="search" name="search" class="segInput" type="text" style="width:60%; font: bold 12px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id,0)" onKeyPress="checkEnter(event,this.id)"/>-->
								<!--<input id="search" name="search" class="segInput" type="text" style="width:60%; font: bold 12px Arial" align="absmiddle" onKeyPress="checkEnter(event,this.id)"/>-->
								<input id="search" name="search" class="segInput" type="text" style="width:60%; background-color:#e2eaf3; border-width:thin; font:bold 13px Arial" align="absmiddle" onChange="trimStringSearchMask(this);" onKeyUp="DisabledSearch(); if ((event.keyCode == 13)&&(isValidSearch(document.getElementById('search').value))) startAJAXSearch('search',0); " onBlur="DisabledSearch();"/>
								<input type="image" class="jedInput" id="search-btn" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0);return false;" align="absmiddle" disabled="disabled" /><br />
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<!-- <input type="checkbox" id="firstname-too" checked> Search for first names too. -->
								<span id='textResult'></span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:290px; width:100%; background-color:#e5e5e5">
						<table id="person-list" class="segList" cellpadding="1" cellspacing="1" width="100%">
							<thead>
								<tr class="nav">
									<th colspan="10">
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
								
								<tr style='font:bold 11.5px Arial; color:#000000'>
									<td width="2%" align="center">No</td>
									<td width="8%" align="center">Ref. No.</td>
									<td width="15%" align="center">Service Code</td>
									<td width="8%" align="center" style="font-size:11px" nowrap="nowrap">Date Requested</td>
									<td width="8%" align="center">RID</td>
									<td width="" align="center">Lastname</td>
									<td width="" align="center">Firstname</td>
									<td width="10%" align="left" style="font-size:11px" nowrap="nowrap">Patient Type</td>
									<!--<td width="8%" align="center" style="font-size:11px" nowrap="nowrap">Birthdate</td>-->
									<td width="5%" align="center">Priority</td>
									<td width="1%" align="center"></td>
								</tr>
							</thead>
							<tbody id="person-list-body">
								<tr>
									<td colspan="10" style="font-weight:normal">No such person exists...</td>
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

	<input type="hidden" name="thisfile" id="thisfile" value="<?php echo $thisfile; ?>">
	<input type="hidden" name="rpath" id="rpath" value="<?php echo $root_path; ?>">
	<input type="hidden" name="pgx" id="pgx" value="<?php echo $pgx; ?>">
	<input type="hidden" name="oitem" id="oitem" value="<?= $oitem? $oitem:'create_dt' ?>">
	<input type="hidden" name="odir" id="odir" value="<?= $odir? $odir:'ASC' ?>">
	<input type="hidden" name="sub_dept_nr" id="sub_dept_nr" value="<?php echo $sub_dept_nr; ?>">


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