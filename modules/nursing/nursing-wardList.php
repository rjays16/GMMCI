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
	
	require_once($root_path.'modules/nursing/ajax/nursing-ward-common.php');
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

$local_user='ck_pflege_user';

require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;
$breakfile="nursing.php".URL_APPEND;
$thisfile=basename(__FILE__);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

 $WardlistTxt = 'Nursing :: Nursing Wards\' List';	

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$WardlistTxt");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('lab_param_config.php')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$WardlistTxt");

 # Assign Body Onload javascript code
 #$smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');
 #$smarty->assign('sOnLoadJs','');
 #added by VAN 04-10-08
 #$smarty->assign('sOnLoadJs','onLoad="preSet(); startAJAXSearch(\'search\', 0);"');
 
 if ($_GET['key'])
		$keyword = $_GET['key'];
	else
		$keyword = ' ';	
		
	if ($_GET['pagekey'])
		$page = $_GET['pagekey'];
	else
		$page = 0;	
		
 $smarty->assign('sOnLoadJs','onLoad="startAJAXSearch2(\''.$keyword.'\', '.$page.');"');

 # added by VAN 04-26-2010	
 #print_r($HTTP_SESSION_VARS);	
 #echo $HTTP_SESSION_VARS['sess_login_personell_nr'];
  require_once($root_path.'include/care_api_classes/class_personell.php');
  $pers_obj = new Personell();
 #get ward area of the user who login
 $rowPers = $pers_obj->get_Personell_info($HTTP_SESSION_VARS['sess_login_personell_nr']);
 #echo "s = ".$rowPers['ward_nr'];
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

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript" >
<!--

//--------------added by VAN 09-12-07------------------
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";


function preSet(){
	document.getElementById('search').focus();
}

function startAJAXSearch(searchID, page) {
	var searchEL = $(searchID);
	var personell_nr = '<?=$HTTP_SESSION_VARS['sess_login_personell_nr']?>';
	
	if (searchEL.value)
		document.getElementById('key').value = searchEL.value;
	else
		document.getElementById('key').value = '*';
			
	document.getElementById('pagekey').value = page;
	
	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		$("labgrouplistTable-body").style.display = "none";
		AJAXTimerID = setTimeout("xajax_populateWardList('"+searchID+"','"+searchEL.value+"',"+page+",'"+personell_nr+"')",50);
		lastSearch = searchEL.value;
	}
}

/*
function startAJAXSearch2(keyword, page) {
	//alert('key, page = '+keyword+" - "+page);
	keyword = keyword.replace("'","^");
	
	if (AJAXTimerID) clearTimeout(AJAXTimerID);
	//$("ajax-loading").style.display = "";
//	document.getElementById("ajax-loading").style.display = "";
//	$("twardList").style.display = "none";
	AJAXTimerID = setTimeout("xajax_PopulateRow('search','"+keyword+"',"+page+")",50);
	lastSearch = keyword;
}
*/

function startAJAXSearch2(keyword, page) {
		var personell_nr = '<?=$HTTP_SESSION_VARS['sess_login_personell_nr']?>';
	//alert('key, page = '+keyword+" - "+page);
	keyword = keyword.replace("'","^");
	
	if (AJAXTimerID) clearTimeout(AJAXTimerID);
	$("ajax-loading").style.display = "";
	$("labgrouplistTable-body").style.display = "none";
	AJAXTimerID = setTimeout("xajax_populateWardList('search','"+keyword+"',"+page+",'"+personell_nr+"')",50);
	lastSearch = keyword;
}
/*
function refreshWindow(key,page){
	startAJAXSearch2(key.value, page.value);
}
*/
function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		$("labgrouplistTable-body").style.display = "";
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

function addWardList(listID, id, ward_id, groupName, rooms, rate, is_temp_closed, nr, assigned_ward) {
	var list=$(listID), dRows, dBody, rowSrc;
	var i, wardID;
	var key, pagekey;
	var cward_nr = '<?=$rowPers['ward_nr'];?>';
	
	if (list) {
	   dBody=list.getElementsByTagName("tbody")[0];
		 dRows=dBody.getElementsByTagName("tr");
		if (id) {
			alt = (dRows.length%2)+1;
			//ward_id = '<a href="'.strtr('nursing-station-pass.php'.URL_APPEND.'&rt=pflege&edit=1&station='.$stations['ward_id'].'&location_id='.$stations['ward_id'].'&ward_nr='.$stations['nr'],' ',' ').'"><div class="wardname"><li>'.strtoupper($stations['ward_id']).'&nbsp;</div></a>';
			//wardID = '<a href='+id+'>'+ward_id+'</a>';
			key = document.getElementById('key').value;
			pagekey = document.getElementById('pagekey').value;
			//wardID = '<a href='+id+'&key='+key+'&pagekey='+pagekey+'>'+ward_id+'</a>';
			//alert(id);
			id = id+'&key='+key+'&pagekey='+pagekey;
			//alert(nr);
			
			if (is_temp_closed==1){
				wardID = '<font color="RED">'+ward_id+'</font>';
				status = '<font color="RED">Temporarily closed</font>';
			}else{
				//if ((nr==cward_nr)||(cward_nr==0)){
				if (assigned_ward!=0){
					wardID = '<a href='+id+'>'+ward_id+'</a>';
					status = '&nbsp;';
				}else{
					wardID = ward_id;
					status = '&nbsp;';
				}	
			}	
			
			rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+addslashes(id)+'">'+
								'<td>&nbsp;</td>'+
								'<td>'+wardID+'</td>'+
								'<td>'+groupName+'</td>'+
								'<td>'+rooms+'</td>'+
								'<td>'+status+'</td>'+
								'</tr>';		
			//'<td align=\'right\'>'+rate+'</td>'+							
		}
		else {
			rowSrc = '<tr><td colspan="5">No hospital wards available at this time...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}

function addslashes(str) {
	str=str.replace("'","\\'");
	return str;
}
/*
function deleteLabGroup(id){
	var answer = confirm("Are you sure you want to delete the laboratory group?");
		if (answer){
			xajax_deleteLabGroup(id);
		}
}

function removeLabGroup(id) {
	//alert('remove');
	var table = document.getElementById("labgrouplistTable");
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
	//window.location.href=window.location.href;
	//alert('key, page = '+key.value+' - '+page.value);
	startAJAXSearch2(key.value, page.value, 0);
}

function AddItem(id,mode){
//alert('id = '+id);
		return overlib(
          OLiframeContent('seg-lab-services-groups.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&id='+escape(id)+'&mode='+mode, 350, 200, 'fGroupTray', 1, 'auto'),
          						WIDTH,350, TEXTPADDING,0, BORDER,0, 
									STICKY, SCROLL, CLOSECLICK, MODAL, 
									CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="refreshWindow(key,pagekey);">',
						         CAPTIONPADDING,4, CAPTION,'Add laboratory service group',
						         MIDX,0, MIDY,0, 
						         STATUS,'Add laboratory service group');							
		
}
*/
//------------------------------------------
// -->
</script> 

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
# Buffer page output
#include($root_path."include/care_api_classes/class_order.php");
#$order = new SegOrder('pharma');

# Load the insurance object
/*
require_once($root_path.'include/care_api_classes/class_insurance.php');
$ins_obj=new Insurance;
*/
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
						<span>Enter search keyword: e.g. Ward Name or ID, all data (just type: * or space)</span>
						<br><br>
						<input id="search" name="search" class="segInput" type="text" size="30" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id,0)" />
						<input type="image" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0);return false;" align="absmiddle" /><br />
						<!--
						<span><a href="javascript:gethelp('person_search_tips.php')" style="text-decoration:underline">Tips & tricks</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                  <br>
						<input type="checkbox" id="firstname-too" checked> Search for first names too.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						-->
						<br>
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
	<!--
	<input type="text" name="key" id="key">
	<input type="text" name="pagekey" id="pagekey">
	-->
	<!---added by VAN 04-12-08 -->
	<?php
			if ($_GET['key'])
				$key = $_GET['key'];
			else
				$key = '*';
				
			if ($_GET['pagekey'])
				$pagekey = $_GET['pagekey'];
			else
				$pagekey = 0;	
					
	?>
	<input type="hidden" name="key" id="key" value="<?=$key?>">
	<input type="hidden" name="pagekey" id="pagekey" value="<?=$pagekey?>">
</form>
<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; overflow-x:hidden; height:305px; width:70%; background-color:#e5e5e5">
<table id="labgrouplistTable" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
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
			<!--<th width="1%"></th>-->
			<th width="1%" align="center">&nbsp;</th>
			<th width="22%" align="left">Ward ID</th>
			<th width="*" align="left">Ward Name</th>
			<th width="15%" align="left">&nbsp;&nbsp;&nbsp;Rooms</th>
			<!--<th width="13%" align="right">Rate&nbsp;&nbsp;&nbsp;</th>-->
			<th width="1%" align="center">&nbsp;</th>
		</tr>
	</thead>
	<tbody id="labgrouplistTable-body">
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
