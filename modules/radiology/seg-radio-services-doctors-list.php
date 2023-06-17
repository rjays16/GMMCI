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
	
	require_once($root_path.'modules/radiology/ajax/radio-admin.common.php');
	$xajax->printJavascript($root_path.'classes/xajax');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
$lang_tables[]='search.php';
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);

$local_user='ck_radio_user';

$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;
$breakfile="radiolog.php".URL_APPEND;
$thisfile=basename(__FILE__);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Radiology :: Co-reader Physician Manager");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('lab_param_config.php')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Radiology :: Co-reader Physician Manager");

 # Assign Body Onload javascript code
 #$smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');
 #$smarty->assign('sOnLoadJs','');
 #$smarty->assign('sOnLoadJs','onLoad="startAJAXSearch(\'search\', 0);"');
 $smarty->assign('sOnLoadJs','onLoad="jsGetServiceFindings(suchform);"');
 
 # Collect javascript code
 ob_start()

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

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript" >
<!--

//--------------added by VAN 09-12-07------------------
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";


function startAJAXSearch(searchID, page) {
	var searchEL = $(searchID);
	
	if (searchEL.value)
		document.getElementById('key').value = searchEL.value;
	else
		document.getElementById('key').value = '*';
			
	document.getElementById('pagekey').value = page;
	
	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		$("radiogrouplistTable-body").style.display = "none";
		AJAXTimerID = setTimeout("xajax_populateRadioPartnersList('"+searchID+"','"+searchEL.value+"',"+page+")",50);
		lastSearch = searchEL.value;
	}
}

function startAJAXSearch2(keyword, page) {
	//alert('key, page = '+keyword+" - "+page);
	keyword = keyword.replace("'","^");
	
	if (AJAXTimerID) clearTimeout(AJAXTimerID);
	$("ajax-loading").style.display = "";
	$("radiogrouplistTable-body").style.display = "none";
	AJAXTimerID = setTimeout("xajax_populateRadioPartnersList('search','"+keyword+"',"+page+")",50);
	lastSearch = keyword;
}

function refreshWindow(key,page){
	startAJAXSearch2(key.value, page.value);
}

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		$("radiogrouplistTable-body").style.display = "";
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

function setPagination(pageno, lastpage, pagen, total) {
	currentPage=parseInt(pageno);
	lastPage=parseInt(lastpage);	
	firstRec = (parseInt(pageno)*pagen)+1;
	if (currentPage==lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;
	
	//$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
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
	switch(jumpType) {
		case FIRST_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',0);
		break;
		case PREV_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',currentPage-1);
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(currentPage)+1);
		break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',lastPage);
		break;
	}
}

function addRadioFindings(listID, id,grp_name, member) {
	var list=$(listID), dRows, dBody, rowSrc;
	var i;
	
	if (list) {
	   dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");
		if (id) {
			alt = (dRows.length%2)+1;
			//alert('id = '+id+" - "+addslashes(id));
			
			var info = '<a href="javascript:void(0);" onclick="AddItem(\''+id+'\',\'update\');"><img src="../../images/edit.gif" border="0"></a>';

			rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+addslashes(id)+'">'+
								'<td>&nbsp;</td>'+
								'<td>'+grp_name+'</td>'+
								'<td>'+member+'</td>'+
								'<td>&nbsp;</td>'+
								'<td>'+info+'</td>'+
								'<td align="center"><img name="delete'+id+'" id="delete'+id+'" src="../../images/delete.gif" style="cursor:pointer" border="0" onClick="deleteRadioDoctors(\''+id+'\');"/></td>'+
								'<td>&nbsp;</td>'+
								'</tr>';				
		}
		else {
			rowSrc = '<tr><td colspan="7">No radiology co-reader physician available at this time...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
	
}

function addslashes(str) {
	str=str.replace("'","\\'");
	return str;
}

function deleteRadioDoctors(id){
	//alert(id);
	var answer = confirm("Are you sure you want to delete the radiology co-reader physician?");
		if (answer){
			xajax_deleteRadioPartners(id);
		}
}

function removeRadioPartners(id) {
	//alert('remove');
	var table = document.getElementById("radiogrouplistTable");
	var rowno;
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		rowno = 'row'+id;
		var rndx = rmvRow.rowIndex;
		table.deleteRow(rmvRow.rowIndex);
		//window.location.reload(); 
		refreshWindow(key,pagekey);
	}
}

function refreshWindow(key,page){
	startAJAXSearch2(key.value, page.value, 0);
}

function AddItem(id,mode){
	//var dept_nr = $F('dept_nr');
	//alert('id = '+id);
    return overlib(
          OLiframeContent('seg-radio-services-doctor.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&id='+id+'&mode='+mode, 450, 400, 'fGroupTray', 1, 'auto'),
          						WIDTH,450, TEXTPADDING,0, BORDER,0, 
									STICKY, SCROLL, CLOSECLICK, MODAL, 
									CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="refreshWindow(key,pagekey);">',
						         CAPTIONPADDING,4, CAPTION,'Add radiological co-readers physician',
						         MIDX,0, MIDY,0, 
						         STATUS,'Add radiological co-readers physician');							
}

//added by VAN 03-17-08
function jsGetServiceFindings(d){
	startAJAXSearch('search',0);
}

function ajxClearOptions() {
	var optionsList;
	//alert(document.forms["paramselect"].parameterselect.value);	
	var el=document.forms["suchform"].parameterselect;
	if (el) {
		optionsList = el.getElementsByTagName('OPTION');
		for (var i=optionsList.length-1;i>=0;i--) {
			optionsList[i].parentNode.removeChild(optionsList[i]);
		}
	}
}/* end of function ajxClearOptions */
		 
function ajxAddOption(text, value) {
	var grpEl = document.forms["suchform"].parameterselect;
	if (grpEl) {
		var opt = new Option( text, value );
		opt.id = value;
		grpEl.appendChild(opt);
	}
	var optionsList = grpEl.getElementsByTagName('OPTION');
}/* end of function ajxAddOption */

function clearSearch(){
	document.getElementById('search').value = '';
}

//------------------------------------------
// -->
</script> 

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
# Buffer page output

# Load the insurance object
/*
require_once($root_path.'include/care_api_classes/class_insurance.php');
$ins_obj=new Insurance;
*/
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

require_once($root_path.'include/care_api_classes/class_radiology.php');
$srvObj=new SegRadio();

ob_start();
?>

<a name="pagetop"></a>
<br>
<div style="padding-left:10px">
<form action="<?php echo $thisfile?>" method="post" name="suchform" id="suchform" onSubmit="">
	<div id="tabFpanel">
		<div align="center" style="display:">
			<table width="100%" cellpadding="4">
				<tr>
					<td width="28%"><span><strong>Enter the search key</strong></span></td>
					<td>
						<input id="search" name="search" class="segInput" type="text" size="30" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id,0)" />
						<input type="image" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0);return false;" align="absmiddle" /><br />
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
<div>
	<!--<a href="seg-radio-services-groups.php"><img src="../../gui/img/control/default/en/en_add_new.gif" border=0 alt="New Entry Form" title="New Entry Form"></a>-->
	<a href="javascript:void(0);"
		    onclick="AddItem(0,0,'save');"
 		    onmouseout="nd();">
	<img name="addnew" id="addnew" src="../../gui/img/control/default/en/en_add_new.gif" border=0 alt="New Entry Form" title="New Entry Form"></a>
</div>
<br>
<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden; overflow-x:hidden; width:100%; background-color:#e5e5e5">
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
			</th>
		</tr>
	  </thead>	
	</table>
</div>
<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; overflow-x:hidden; height:305px; width:100%; background-color:#e5e5e5">
<table id="radiogrouplistTable" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<!--<th width="1%"></th>-->
			<th width="1%" align="center">&nbsp;</th>
			<th width="15%" align="left">Group Name</th>
			<th width="*" align="center">Doctor's Name</th>
			<th width="1%" align="left">&nbsp;</th>
			<th width="3%" align="center">Edit</th>
			<th width="3%" align="center">Delete</th>
			<th width="1%" align="center">&nbsp;</th>
		</tr>
	</thead>
	<tbody id="radiogrouplistTable-body">
		<?= $rows ?>
	</tbody>
</table>
<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
</div>
<br />
<hr>
<!--
<table align="center" width="90%">
<tr>
	<td align="center"><a href="insurance_co_new.php"><img src="../../gui/img/control/default/en/en_form" border=0 alt="New Entry Form" title="New Entry Form"></a></td>
</tr>
</table>
-->
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
