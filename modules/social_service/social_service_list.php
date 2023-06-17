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
	
	#require($root_path."modules/laboratory/ajax/lab-new.common.php");
	require($root_path."modules/social_service/ajax/social_list_common.php");
	$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/

#define('LANG_FILE','lab.php');
define('LANG_FILE','social_service.php');
define('NO_2LEVEL_CHK',1);

$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;
$breakfile = 'social_service_main.php';
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
 $smarty->assign('sToolbarTitle',"$swSocialService :: List of Classified Patients");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$swSocialService :: List of Classified Patients");

 # Assign Body Onload javascript code
 #$smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');
 #$smarty->assign('sOnLoadJs','');
 $smarty->assign('sOnLoadJs','onLoad="startAJAXSearch(\'search\', 0, 1);"');

 # Collect javascript code
 ob_start()

?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript" >
<!--

function removeRequest(id) {
	var table = document.getElementById("RequestList");
	var rowno;
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		rowno = 'row'+id;
		var rndx = rmvRow.rowIndex;
		table.deleteRow(rmvRow.rowIndex);
		window.location.reload(); 
	}
	//self.opener.location.href=self.opener.location.href;
	//window.parent.location.href=window.parent.location.href;
}

//--------------added by VAN 09-12-07------------------
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";

function startAJAXSearch(searchID, page, mod) {
	var searchEL = $(searchID);
	if (mod) 
		searchEL.value = "";
		
	document.getElementById('key').value = searchEL.value;
	document.getElementById('pagekey').value = page;	
	
	//var searchLastname = $('firstname-too').checked ? '1' : '0';
	//commented out/edited by pet (aug.6,2008) above line in accordance with VAS' search code changes
	var searchLastname = 0;
	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		$("RequestList-body").style.display = "none";
		AJAXTimerID = setTimeout("xajax_populateRequestList('"+searchID+"','"+searchEL.value+"',"+page+","+searchLastname+")",100);
		lastSearch = searchEL.value;
	}
}

function startAJAXSearch2(keyword, page, mod) {
	//alert('key, page = '+keyword+" , "+page);
	keyword = keyword.replace("'","^");
	
	//var searchLastname = $('firstname-too').checked ? '1' : '0';
	var searchLastname =0;
	if (AJAXTimerID) clearTimeout(AJAXTimerID);
	$("ajax-loading").style.display = "";
	$("RequestList-body").style.display = "none";
	//AJAXTimerID = setTimeout("xajax_populateRequestList('"+searchID+"','"+searchEL.value+"',"+page+","+searchLastname+",1)",100);
	AJAXTimerID = setTimeout("xajax_populateRequestList('search','"+keyword+"',"+page+","+searchLastname+")",100);
	lastSearch = keyword;
}

function refreshWindow(key,page){
	startAJAXSearch2(key.value, page.value, 0);
}

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		$("RequestList-body").style.display = "";
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
	
//	alert('currentPage, lastPage, firstRec, total = '+currentPage+", "+lastPage+", "+firstRec+", "+total);
	
	if (currentPage==lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;
	
	//alert('firstrec, lastrec, total = '+(firstRec)+" = "+(lastRec)+" = "+parseInt(total));
	
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
			startAJAXSearch('search',0,0);
			document.getElementById('pagekey').value=0;
		break;
		case PREV_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',currentPage-1,0);
			document.getElementById('pagekey').value=currentPage-1;
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(currentPage)+1,0);
			document.getElementById('pagekey').value=parseInt(currentPage)+1;
		break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',lastPage,0);
			document.getElementById('pagekey').value=parseInt(lastPage);
		break;
	}
}


//$objResponse->addScriptCall("addPerson","RequestList",trim($result["encounter_nr"]),$name,$grantdte,$result["discountid"]);
//function addPerson(listID, refno, name, req_date, urgency, paid) {
function addPerson(listID, pid, encounter, name, grant_dte, discountid, mss_no) {
	var list=$(listID), dRows, dBody, rowSrc;
	var i, mode, editlink;
	
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");
		//if (encounter) {
		if (pid) {
			alt = (dRows.length%2)+1;
			/*0
			editlink ='onclick="return overlib(OLiframeContent(\'social_service_show.php?sid=<?php echo "$sid&lang=$lang"?>&encounter_nr='+encounter+'&origin=patreg_reg&mode=entry&popUp=1\', 850, 450, \'flab-list\', 1, \'auto\'), ' +
					'WIDTH, 850, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=refreshWindow(key,pagekey);>\', '+
					'CAPTIONPADDING, 4, CAPTION, \'Social Service\', MIDX, 0, MIDY, 0, STATUS, \'Social Service\');">';
			*/
			if ((mss_no=="")||(mss_no==" "))
				mss_no = '<span style="color:#c00000">No MSS</span>';
				
			if ((encounter=="")||(encounter==" ")){
				encounter = 0;	
				enc_display = '<span style="color:#c00000">No case</span>';
			}else{
				enc_display = encounter;
			}	
			
			editlink ='onclick="return overlib(OLiframeContent(\'social_service_show.php?sid=<?php echo "$sid&lang=$lang"?>&pid='+pid+'&encounter_nr='+encounter+'&origin=patreg_reg&mode=entry\', 850, 450, \'flab-list\', 1, \'auto\'), ' +
					'WIDTH, 850, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=refreshWindow(key,pagekey);>\', '+
					'CAPTIONPADDING, 4, CAPTION, \'Social Service\', MIDX, 0, MIDY, 0, STATUS, \'Social Service\');">';
						
			rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+encounter+'">'+
							'<td align="center">'+mss_no+'</td>'+
							'<td align="center">'+pid+'</td>'+
							'<td align="center">'+enc_display+'</td>'+
							'<td align="left">'+name+'</td>'+
             	'<td align="center">'+grant_dte+'</td>'+
							'<td align="center" style="color:#008000">'+discountid+'</td>'+
            	'<td align="center">'+
								'<a href="social_service_pass.php?sid=<?php echo "$sid&lang=$lang"?>&pid='+pid+'&encounter_nr='+encounter+'&origin=patreg_reg&mode=entry&target=show&from=list"><img src="../../images/cashier_edit.gif" border="0"></a>'+
								//<a href="javascript:void(0);" '+editlink+'<img src="../../images/cashier_view.gif" border="0"></a>
							'</td>'+
						'</tr>';							
				
		}
		else {
			rowSrc = '<tr><td colspan="9">No requests available at this time...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}
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
//change this.. 

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab();

ob_start();
?>

<a name="pagetop"></a>
<br>
<div style="padding-left:10px">
<form action="<?php echo $thisfile?>" method="post" name="suchform" onSubmit="">
			<table width="100%" cellpadding="4" border="0">
				<tr>
					<td align="center" colspan="3">
						<!-- <span>Enter search keyword: e.g. PID, encounter no., reference no, first name, family name, or request date (date format: MM.DD.YYYY), all data (just type: *)</span> -->
						<span>Enter search keyword: e.g. HRN, Case no., Reference no, Family name,<br /> or Request date (date format: MM.DD.YYYY)</span>	
						<!-- pet replaced the line above (Aug.5, 2008) in accordance with VAS' search code changes -->
					</td>
				</tr>
				<tr>
					<td width="30%"></td>
					<td width="*" align="left" nowrap="nowrap">
						<input id="search" name="search" class="jedInput" type="text" size="30" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial" align="absmiddle" onkeyup="if (event.keyCode == 13) startAJAXSearch(this.id,0,0)" />
						<input type="image" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0,0);return false;" align="absmiddle" />
						<br />
						<a href="javascript:gethelp('person_search_tips.php')" style="text-decoration:underline">Tips & tricks</a>
            <br>
						<!-- <input type="checkbox" id="firstname-too" checked> Search for first names too.
						<br> --> <!-- pet commented out the line above (Aug.5, 2008) in accordance with VAS' search code changes -->
					</td>
					<td width="30%"></td>
				</tr>
			</table>
<!--
		<div align="center">
			<table cellpadding="4" style="display:none">
				<tr>
					<td align="center">Enter the person's Health Record No. (HRN)</td>
				</tr>
				<tr>
					<td align="center">
						<input class="segInput" id="search-pid" name="spid" type="text" size="50" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial"/>
						<input type="image" src="../../images/his_searchbtn.gif" align="absmiddle" />
					</td>
				</tr>
			</table>
		</div>
		<div align="center">
			<table cellpadding="4" style="display:none">
				<tr>
					<td align="center">Enter the search keyword (e.g. First name, or family name)</td>
				</tr>
				<tr>
					<td align="center">
						<input class="segInput" id="search-name" name="sname" type="text" size="50" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial"/>
						<input type="image" src="../../images/his_searchbtn.gif" align="absmiddle" />
					</td>
				</tr>
			</table>
		</div>
	</div>
-->
</div>
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">
	<!--added by VAN 05-14-08 -->
	<input type="hidden" name="key" id="key">
	<input type="hidden" name="pagekey" id="pagekey">

</form>

<div  style="display:block; border:1px solid #8cadc0; overflow-y:hidden; overflow-x:hidden; width:82%; background-color:#e5e5e5">
<table id="RequestList" class="jedList" width="100%" border="0" cellpadding="0" cellspacing="0">
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
	<thead>
		<!--
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
		-->
		<tr>
			<!--<th width="1%"></th>-->
			<th width="13%" align="center">MSS No.</th>
			<th width="15%" align="center">HRN</th>
			<th width="14%" align="center">Case No.</th>
			<th width="*" align="center">Patient Name</th>
			<th width="12%" align="center">Grant Date</th>
			<th width="15%" align="center">Classification</th>
			<th width="5%" align="center">Details</th>
			<!-- <th width="5%" align="center">Delete</th> -->
		</tr>
	</thead>
	<tbody id="RequestList-body">
		<?= $rows ?>
	</tbody>
</table>
<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
</div>
<br />


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
	#$smarty = new smarty_care('common');
	
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
# $smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','left').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');	
#echo "temp = ".$sTemp;
 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 #$smarty->display('common/mainframe.tpl');
 $smarty->display('common/mainframe.tpl');
?>
