<?php
# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');

	require($root_path.'include/inc_environment_global.php');

	require($root_path."modules/radiology/ajax/radio-schedule-common.php");
	$xajax->printJavascript($root_path.'classes/xajax');
	#$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
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

$breakfile = "radiolog.php".URL_APPEND;
$local_user='ck_radio_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

$thisfile=basename(__FILE__);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Radiology :: Scheduled Request List");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Radiology :: Scheduled Request List");

 #$smarty->assign('sOnLoadJs','onLoad="preSet(); startAJAXSearch(\'search\', 0);"');
 $smarty->assign('sOnLoadJs','onLoad="preSet();DisabledSearch();"');

 # Collect javascript code
 ob_start()

?>

<script type="text/javascript" src="<?=$root_path?>js/dojo/dojo.js"></script>

<!--added by VAN 02-06-08-->
<!--for shortcut keys -->
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<!-- Include dojoTab Dependencies -->
<script type="text/javascript">
    dojo.require("dojo.widget.TabContainer");
    dojo.require("dojo.widget.LinkPane");
    dojo.require("dojo.widget.ContentPane");
    dojo.require("dojo.widget.LayoutContainer");
    dojo.require("dojo.event.*");
</script>

<script language="javascript" >
<!--
function eventOnClick(){
    dojo.event.connect(dojo.widget.byId('tbContainer').tablist, "onButtonClick","handleOnclick");

}

function handleOnclick(){ 
   //populate request
   startAJAXSearch('search',0);
   //startAJAXSearch2('',0);
}    

function preSet(){
	document.getElementById('search').focus();
    
    dojo.addOnLoad(eventOnClick);

   xajax_populateScheduledList('search','Ttab0','now',0);
}

function deleteSkedRequest(refno){
	var answer = confirm("Are you sure you want to delete the scheduled request with a reference no. "+(refno)+"?");
		if (answer){
			//xajax_deleteRequest(refno);
			xajax_deleteScheduledRadioRequest(refno);
		}
}

/*
function reload_page(){
	window.location.reload();
}
*/

function removeSkedRequest(id) {
    var tab;
   
    try{
        tab = dojo.widget.byId('tbContainer').selectedChild;
    }catch(e){
        //alert("e.message = "+e.message);
        tab = 'tab0';   // use in initial loading
    }
    
	var table = document.getElementById("T"+tab);
	var rowno;
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		rowno = 'row'+id;
		var rndx = rmvRow.rowIndex;
		table.deleteRow(rmvRow.rowIndex);
		refreshWindow(key,pagekey);
	}
}

	function SearchItem(){
		startAJAXSearch('search',0);
	}

	function pSearchClose() {
		cClick();  //function in 'overlibmws.js'
	}

//----------------------------------

//--------------added by VAN 09-12-07------------------
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";

function startAJAXSearch(searchID, page) {
	var searchEL = $(searchID);
    
    var tab;
   
    try{
        tab = dojo.widget.byId('tbContainer').selectedChild;
    }catch(e){
        //alert("e.message = "+e.message);
        tab = 'tab0';   // use in initial loading
    }
    
	document.getElementById('key').value = searchEL.value;
	document.getElementById('pagekey').value = page;

	keyword = searchEL.value;
	keyword = keyword.replace("'","^");
    
	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		$("T"+tab).style.display = "none";    
		//AJAXTimerID = setTimeout("xajax_populateScheduledList('"+searchID+"','"+keyword+"',"+page+")",50);
       
       AJAXTimerID = setTimeout("xajax_populateScheduledList('"+searchID+"','T"+tab+"','"+keyword+"',"+page+")",50);
       lastSearch = searchEL.value;
	}
}

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
    var tab;
   
    try{
        tab = dojo.widget.byId('tbContainer').selectedChild;
    }catch(e){
        //alert("e.message = "+e.message);
        tab = 'tab0';   // use in initial loading
    }
    
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		$("T"+tab).style.display = "";
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
			return true;	// success
		}
		else return false;	// fail
	}
	else return false;	// fail
}

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

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
	//$("pageShow").innerHTML = '<span>Showing '+formatNumber((firstRec),2)+'-'+formatNumber((lastRec),2)+' out of '+formatNumber((parseInt(total)),2)+' record(s).</span>';

	$("pageFirst").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pagePrev").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pageNext").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pageLast").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
}

function jumpToPage(el, jumpType, set) {
	if (el.className=="segDisabledLink") return false;
	if (lastPage==0) return false;
	switch(jumpType) {
		case FIRST_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',0);
			document.getElementById('pagekey').value=0;
		break;
		case PREV_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',parseInt(currentPage)-1);
			document.getElementById('pagekey').value=currentPage-1;
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(currentPage)+1);
			document.getElementById('pagekey').value=parseInt(currentPage)+1;
		break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(lastPage));
			document.getElementById('pagekey').value=parseInt(lastPage);
		break;
	}
}

function startAJAXSearch2(keyword, page) {
    var tab;
   
    try{
        tab = dojo.widget.byId('tbContainer').selectedChild;
    }catch(e){
        //alert("e.message = "+e.message);
        tab = 'tab0';   // use in initial loading
    }
    
	//alert('key, page = '+keyword+" , "+page);
	var keyword = keyword.replace("'","^");

	//var searchLastname = $('firstname-too').checked ? '1' : '0';
	//commented out/edited by pet (aug.6,2008) above line in accordance with VAS' search code changes
	var searchLastname = 0;
	if (AJAXTimerID) clearTimeout(AJAXTimerID);
	$("ajax-loading").style.display = "";
	$("T"+tab).style.display = "none";
	AJAXTimerID = setTimeout("xajax_populateScheduledList('search','T"+tab+"','"+keyword+"',"+page+")",100);
    lastSearch = keyword;
}


//function refreshWindow(){
function refreshWindow(key,page){
    window.location.href=window.location.href;
	//alert('key, page = '+key.value+' - '+page.value);
	//startAJAXSearch2(key.value, page.value);
    startAJAXSearch2(key, page);
}

function serveRequest(batch_nr, refno, service_code,is_served){
    //alert(batch_nr+" == "+is_served);
    var answer;
    
    if (is_served==1)
        answer = confirm("Are you sure that this request is already DONE? \n Click OK if YES, otherwise CANCEL.");
    else
        answer = confirm("Are you sure to UNDO this done request? \n Click OK if YES, otherwise CANCEL."); 
        
    if (answer)
        xajax_savedServedPatient(batch_nr, refno, service_code, is_served);
}

function ReloadWindow(){  
    window.location.href=window.location.href;
  
    key = $('key').value;
    pagekey = $('pagekey').value;
    
    refreshWindow(key,pagekey);
}

function addPerson(listID, refno,batch_nr, name, code, serv_name, sked_date, sked_time, dept, rid, sked_by, skstatus,dept_short_name,pat_type, is_served, disabled_icon, bill) {
	//alert('listID, refno, name, code, sked_date, sked_time = '+listID+" , "+refno+" , "+name+" , "+code+" , "+sked_date+" , "+sked_time);
	var list=$(listID), dRows, dBody, rowSrc;
	var i, mode, editlink;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");
        
		if (refno) {
			alt = (dRows.length%2)+1;

            //added by VAN 11-04-09
            key = document.getElementById('key').value;
            pagekey = document.getElementById('pagekey').value;
            //-----------------------
            
			if (skstatus=='done')
				mode = '<img name="delete'+refno+'" id="delete'+refno+'" src="../../images/btn_donerequest.gif" align="absmiddle" border="0"/>';
			else
				mode = '<img name="delete'+refno+'" id="delete'+refno+'" src="../../images/delete.gif" style="cursor:pointer" border="0" onClick="deleteSkedRequest('+refno+'); refreshWindow(key,pagekey); "/>';

			//editlink = '';
			//seg-radio-schedule-form.php?batch_nr=2007000027&sub_dept_nr_name=Computed%20Tomography
			editlink ='onclick="return overlib(OLiframeContent(\'seg-radio-schedule-form.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp='+1+'&mode=update&update=1&sub_dept_nr_name='+dept+'&batch_nr='+refno+'\', 500, 450, \'flab-list\', 1, \'auto\'), ' +
					'WIDTH, 500, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=refreshWindow(key,pagekey);>\', '+
					'CAPTIONPADDING, 4, CAPTION, \'Update Scheduled Request\', MIDX, 0, MIDY, 0, STATUS, \'Update Scheduled Request\');">';

			//added by VAN 07-08-08
			/*serve ='onclick="return overlib(OLiframeContent(\'seg-radio-schedule-processform.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp='+1+'&mode=update&update=1&sub_dept_nr_name='+dept+'&batch_nr='+refno+'\', 500, 450, \'flab-list\', 1, \'auto\'), ' +
					'WIDTH, 500, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=refreshWindow(key,pagekey);>\', '+
					'CAPTIONPADDING, 4, CAPTION, \'Process Request\', MIDX, 0, MIDY, 0, STATUS, \'Process Request\');">';*/
            
            if (is_served==1)
                //serve = 'onclick="serveRequest(\''+refno+'\',\''+batch_nr+'\',\''+code+'\',\'0\');"><img src="../../images/cashier_ok.gif" border="0" title="Undo Served">';      
                serve = 'style="cursor:default" onclick="return false;"><img src="../../images/cashier_ok.gif" border="0" title="Undo Served">';          
                
            else{
                if (disabled_icon==1){
                    serve = 'style="cursor:default" onclick="return false;"><img src="../../images/cashier_lock.gif" border="0" title="Can\'t be catered. NOT PAID...">';
                }else{
                    //serve = 'onclick="serveRequest(\''+refno+'\',\''+batch_nr+'\',\''+code+'\',\'1\');"><img src="../../images/cashier_edit_3.gif" border="0" title="Serve">';        
                    serve = 'onclick="return overlib(OLiframeContent(\'seg-done-radtech-list.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&code='+code+'&is_served=1&refno='+refno+'&batch_nr='+batch_nr+'\', 400, 200, \'flab-list\', 1, \'auto\'), ' +
                        'WIDTH, 400, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=refreshWindow(key,pagekey);>\', '+
                        'CAPTIONPADDING, 4, CAPTION, \'Serve Request\', MIDX, 0, MIDY, 0, STATUS, \'Serve Request\');"><img src="../../images/cashier_edit_3.gif" border="0">';
                }        
            } 
            
            if ((bill.bill_nr!='')&&(bill.is_cash==0)){
                serve = 'style="cursor:default" onclick="return false;"><img src="../../images/cashier_lock.gif" border="0" title="Can\'t be catered.. This patient has a FINAL saved billing. Please call Billing to delete the BILLING.">';
            }
            
			rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+refno+'">'+
						'<td align="center">'+refno+'</td>'+
						'<td align="center">'+batch_nr+'</td>'+
						'<td align="left">'+name+'</td>'+
						'<td align="left">'+code+' ('+dept_short_name+')</td>'+
						'<td align="left">'+rid+'</td>'+
						'<td style="color:#007" align="center">'+sked_date+" "+sked_time+'</td>'+
						'<td align="left">'+sked_by+'</td>'+
						'<td align="left">'+pat_type+'</td>'+
						'<td align="center"><a href="javascript:void(0);" '+editlink+'<img src="../../images/timeplan.gif" border="0" title="Schedule"></a></td>'+
						<!--'<td align="center">'+mode+'</td>'+-->
						'<td align="center"><a href="javascript:void(0);" '+serve+'</a></td>'+
						'</tr>';

		}
		else {
			rowSrc = '<tr><td colspan="10">No scheduled requests available at this time...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
		//alert(dBody.innerHTML);
	}
}

function checkEnter(e){
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
		$('skey').value=$('search-refno').value; startAJAXSearch(this.id,0);
	}else{
		return true;
	}
}

/*function ReloadWindow(){
	window.location.href=window.location.href;
}*/

function closeWindow(key,page){
	startAJAXSearch2(key, page);
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

//------------------------------------------
// -->
</script>

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
.olfgright {text-align: right;}
.olfgjustify {background-color:#cceecc; text-align: justify;}
.olfgleft {background-color:#cceecc; text-align: left;}

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


<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
# Buffer page output
#include($root_path."include/care_api_classes/class_order.php");
#$order = new SegOrder('pharma');

require_once($root_path.'include/care_api_classes/class_radiology.php');
$srvObj=new SegRadio();

ob_start();
?>

<a name="pagetop"></a>
<br>
<div style="padding-left:10px">
<form action="<?php echo $thisfile?>" method="post" name="suchform" onSubmit="">
	<div id="tabFpanel">
		<div align="center" style="display:">
			<table width="100%" cellpadding="4">
				<tr>
					<td width="30%" align="center">
						<span>Enter the search key (HRN, Batch No., Family Name or Scheduled Date).<br> Enter dates in <font color="#0000FF"><b>MM/DD/YYYY</b></font> format.
								Enter asterisk (<b>*</b>) to show all data.</span>	<!-- "Given Name" in this text as search key removed by pet (aug.5,2008) in accordance with VAS' search code changes -->
						<br><br>
						<!--<input id="search" name="search" class="segInput" type="text" size="30" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id,0)" />-->
						<input id="search" name="search" class="segInput" type="text" size="30" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial" align="absmiddle" onChange="trimStringSearchMask(this);" onKeyUp="DisabledSearch(); if ((event.keyCode == 13)&&(isValidSearch(document.getElementById('search').value))) startAJAXSearch('search',0); " onBlur="DisabledSearch();" />
						<input type="image" class="jedInput" id="search-btn" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0);return false;" align="absmiddle" disabled="disabled" /><br />
						<!--<span><a href="javascript:gethelp('person_search_tips.php')" style="text-decoration:underline">Tips & tricks</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
									<br>-->
						<!-- <input type="checkbox" id="firstname-too" checked> Search for first names too.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<br> -->	<!-- "First Name" in this text as search key removed by pet (aug.5,2008) in accordance with VAS' search code changes -->
					</td>
				</tr>
    		</table>
		</div>
	</div>

	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="key" id="key">
	<input type="hidden" name="pagekey" id="pagekey">
</form>
</div>
<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden;overflow-x:hidden; width:95%; background-color:#e5e5e5">
<b>Refresh</b>&nbsp;&nbsp;<img src="../../images/cashier_refresh.gif" style="cursor:pointer" onclick="ReloadWindow();" />

<!-- added by VAN 08-14-2012 -->
 
<?php
  #added by VAN 11-17-2011  
  #echo "he = ".$allow_accessSysAd." = ".$allow_accessCT." = ".$allow_accessUTZ." = ".$allow_accessXRAY." = ".$allow_accessOBGYNEUTZ;
  if ($allow_accessSysAd){
     $allow_accessCT = 1; 
     $allow_accessUTZ = 1;
     $allow_accessOBGYNEUTZ = 1;
     $allow_accessMRI = 1;
     $allow_accessXRAY = 1;
  }
  
  $dept_nr_list = "";
  $waccess = 0;
  if ($allow_accessCT){
     $dept_nr_list .= ",'167',"; 
     $waccess = $waccess + 1;
  }   
                  
  if ($allow_accessUTZ){
     $dept_nr_list .= ",'165',";
     $waccess = $waccess + 1;
  }
  
  if ($allow_accessOBGYNEUTZ){
     $dept_nr_list .= ",'209',";
     $waccess = $waccess + 1;
  }
  
  if ($allow_accessMRI){
     $dept_nr_list .= ",'208',";
     $waccess = $waccess + 1;
  }   
                     
  if ($allow_accessXRAY){
     $dept_nr_list .= ",'164', '166',";      
     $waccess = $waccess + 1;
  }   
   
  
  if (($dept_belong['dept_nr'])&&($waccess==0)){
    $dept_nr_list = "'".$dept_belong['dept_nr']."'";
    $waccess = $waccess + 1;
    #die('h - '.$dept_belong['dept_nr']);
  }
      
  $dept_nr_list = substr($dept_nr_list,1,strlen($dept_nr_list)-2);
  $dept_nr_list = str_replace(",,",",",$dept_nr_list);
  
  if (empty($rows))
    $rows = '<tr><td colspan="10">No scheduled requests available at this time...</td></tr>';
?>

<!--  Tab Container for radiology request list -->
<input type="hidden" id="waccess" name="waccess" value="<?=$waccess?>">
<input type="hidden" id="dp_nr" name="dp_nr" value="<?=$dept_belong['dept_nr']?>">
<div id="tbContainer"  dojoType="TabContainer" style="width:100%; height:32em; display:block; border:1px" align="center">
<table width="100%" class="segList" border="0" cellpadding="0" cellspacing="0">
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
    </thead>
</table>

<?php  #if ($waccess>1){?>
    <div dojoType="ContentPane" widgetId="tab0" label="All" style="display:none;overflow:auto;display:block; border:1px solid #8cadc0;">
        <!--  Table:list of request -->
        <table id="Ttab0" class="segList" border="0" cellpadding="0" cellspacing="0">
            <!-- List of ALL Pending Requests  -->
            
        <thead>
        <tr>
            <!--<th width="1%"></th>-->
            <th width="10%" align="center">Ref. No.</th>
            <th width="10%" align="center">Batch No.</th>
            <th width="*" align="left">Name</th>
            <th width="12%" align="left">Service Code</th>
            <th width="10%" align="center">RID</th>
            <th width="17%" align="center">Date Scheduled</th>
            <th width="10%" align="left">Scheduled By</th>
            <th width="6%" align="left">Patient Type</th>
            <th width="5%" align="center">Details</th>
            <!--<th width="5%" align="center">Delete</th>-->
            <th width="5%" align="center">Served</th>
        </tr>
    </thead>  
    <!--<tbody id="RequestList-body">-->
    <tbody id="TBodytab0">
                <?= $rows ?>
            </tbody> 
        </table>
        <img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
    </div>
    <!-- tabcontent for radiology sub-department -->
<?php
#} #end of if ($waccess>1)                                                    
#Department object

include_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj = new Department;

$radio_sub_dept=$dept_obj->getSubDept2($dept_nr,$dept_nr_list);
#echo $dept_obj->sql;
if($dept_obj->rec_count){
    $dept_counter=2;
    while ($rowSubDept = $radio_sub_dept->FetchRow()){
        #$text_name = trim($rowSubDept['name_formal']);
        $text_name = trim($rowSubDept['name_short']);
?>
<div dojoType="ContentPane" widgetId="tab<?=$rowSubDept['nr']?>" label="<?=$text_name?>" style="display:none;overflow:auto; border:1px solid #8cadc0;" >
        <table id="Ttab<?=$rowSubDept['nr']?>" cellpadding="0" cellspacing="0" class="segList">
           <!-- List of Radiology Requests  -->
           
        <thead>
        <tr>
            <!--<th width="1%"></th>-->
            <th width="10%" align="center">Ref. No.</th>
            <th width="10%" align="center">Batch No.</th>
            <th width="*" align="left">Name</th>
            <th width="12%" align="left">Service Code</th>
            <th width="10%" align="center">RID</th>
            <th width="17%" align="center">Date Scheduled</th> 
            <th width="10%" align="left">Scheduled By</th>
            <th width="6%" align="left">Patient Type</th>
            <th width="5%" align="center">Details</th>
            <!--<th width="5%" align="center">Delete</th>-->
            <th width="5%" align="center">To be Served</th>
        </tr>
    </thead> 
    <!--<tbody id="RequestList-body">-->
    <tbody id="TBodytab<?=$rowSubDept['nr']?>">
                <?= $rows ?>
            </tbody> 
        </table>
        <img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
</div>
<?php
        $dept_counter++;
    } # end of while loop
}   # end of if-stmt 'if ($dept_obj->rec_count)'
?>
</div>

<!-- -->
</div>
</div>

<br />
<hr>

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
