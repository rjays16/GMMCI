<?php
#created by VAN 06-21-08
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/registration_admission/ajax/med_cert.common.php");
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
 $pid = $_GET["pid"];
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
	$x = "onLoad='preSet(\"$pid\");'";
 $smarty->assign('sOnLoadJs',$x);

 require_once($root_path.'include/care_api_classes/class_encounter.php');
 $enc_obj=new Encounter;

 require_once($root_path.'include/care_api_classes/class_person.php');
 $person_obj=new Person();



 //$result = $enc_obj->getEncounterInfo($encounter);
 //$pid = $result["pid"];

 $person = $person_obj->getAllInfoArray($pid);
# echo "sql = ".$person_obj->sql;
 extract($person);

 $name = $name_first." ".$name_2." ".$name_middle." ".$name_last;

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


<script language="javascript" >

function preSet(pid){
		startAJAXSearch('search',0,pid);
}

var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";


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

function startAJAXSearch(searchID, page, pid) {

		var med_cert = false;
		var searchEL = $(searchID);
		var keyword;

		keyword = searchEL.value;
		keyword = keyword.replace("'","^");

		if (searchEL) {

				searchEL.style.color = "#0000ff";
				if (AJAXTimerID) clearTimeout(AJAXTimerID);
				$("ajax-loading").style.display = "";
				$("product-list").style.display = "none";
				//alert(pid);
				AJAXTimerID = setTimeout("xajax_populateMedCertEncRef('"+pid+"','"+searchID+"',"+page+",'"+keyword+"',false)",100);
				//alert("here");
				lastSearch = searchEL.value;
		}
}

function endAJAXSearch(searchID) {
		var searchEL = $(searchID);
		if (searchEL) {
				$("ajax-loading").style.display = "none";
				$("product-list").style.display = "";
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

function addProductToList(listID, id, enc_nr, ref_nr, dept, enc_type, date_admitted) {

		var list=$(listID), dRows, dBody, rowSrc;
		//rowSrc = '';
		if (list) {

				dBody=list.getElementsByTagName("tbody")[0];
				dRows=dBody.getElementsByTagName("tr");

//alert('sulod listID, id, name, cash, charge, sservice = '+listID+" , "+id, name+" , "+cash+" , "+charge+" , "+sservice);
				//alert(dBody.innerHTML);
				// get the last row id and extract the current row no.
				if (id) {
						//mode = '<img name="delete'+addslashes(id)+'" id="delete'+addslashes(id)+'" src="../../images/delete.gif" style="cursor:pointer" border="0" onClick="deleteService(\''+addslashes(id)+'\'); refreshWindow(key,pagekey); "/>';
						//var editlink = '<a href="javascript:void(0);" onclick="AddItem(\''+addslashes(id)+'\',\'update\');"><img src="../../images/edit.gif" border="0"></a>';


					 var reagent ='onclick="return overlib(OLiframeContent(\'certificates/cert_med_interface.php?encounter_nr='+enc_nr+'&referral_nr='+ref_nr+'&showBrowser=1\', 720, 330, \'flab-list\', 1, \'auto\'), ' +
										'WIDTH, 330, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0>\', '+
										'CAPTIONPADDING, 4, CAPTION, \'Medical Certificate\', MIDX, 0, MIDY, 0, STATUS, \'Medical Certificate\');">';

						var reagentlink = '<td align="center"><a href="javascript:void(0);" '+reagent+'<img src="../../images/edit.gif" border="0"></a></td>';

						rowSrc = '<tr class="wardlistrow" id="row'+id+'">'+
														'<td align="center">'+enc_nr+'</span></td>'+
												 '<td align="center">'+ref_nr+'</span></td>'+
												 '<td align="center">'+dept+'</td>'+
												 '<td align="center">'+enc_type+'</td>'+
														'<td align="center">'+date_admitted+'</td>'+
														reagentlink+
											'</tr>';
						/* rowSrc = '<tr class="wardlistrow" id="row'+id+'">'+
														'<td align="left">date prepared</span></td>'+
												 '<td align="left">case nr</span></td>'+
												 '<td align="left">date admitted</td>'+
														'<td align="left">dept</td>'+
												'<td align="left">prepared by</td>'+
														'<td align="left">link</td>'+
											'</tr>';       */
											 //alert(enc_nr);
				}
				else {
						rowSrc = '<tr><td colspan="6" style="">No encounters without medical certificate...</td></tr>';
				}
				dBody.innerHTML += rowSrc;
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

function jumpToPage(el, jumpType, set,pid) {
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

function refreshWindow(key,page){
		window.location.href=window.location.href;
		//alert('key, page = '+key.value+' - '+page.value);
		//startAJAXSearch2(key.value, page.value);
}

// -->
</script>
<!--<script type="text/javascript" src="<?=$root_path?>modules/registration_admission/js/person-search-gui.js?t=<?=time()?>"></script>-->
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
<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden;overflow-x:hidden; height:370px; width:100%; background-color:#e5e5e5">
		<table border="1" class="segPanel" cellspacing="2" cellpadding="2" width="99%" align="center">
				<tr>
						<td width="20%">Hospital Number</td>
						<td><strong><?=$pid?></strong></td>
				</tr>
				<tr>
						<td>Patient's Name</td>
						<td><strong><?=$name?></strong></td>
				</tr>
		</table>
		<br />
		<div width:100%>
				<table width="200%">
						<tr>
								<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
										<div style="padding:4px 2px; padding-left:10px; ">
												Search History<input id="search" name="search" class="segInput" type="text" style="width:50%; margin-left:10px; font: bold 12px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id,0,'<?=$pid?>')" onKeyPress="checkEnter(event,this.id,'<?=$pid?>')" />
												<input type="image" id="search_img" name="search_img" src="<?= $root_path ?>images/his_searchbtn.gif" disabled onclick="startAJAXSearch('search',0,'<?=$pid?>');return false;" align="absmiddle" />
										</div>
								</td>
						</tr>
				</table>
		</div>
		<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden; overflow-x:hidden; width:100%; background-color:#e5e5e5">
		<table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
				<thead>

						<tr class="nav">
						<th colspan="9">
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

		<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll;overflow-x:hidden; height:247px; width:100%; background-color:#e5e5e5">
				<table id="product-list" class="segList" cellpadding="1" cellspacing="1" width="100%">
						<thead>
								<tr>
										<th width="16%" align="left">Case Number</th>
										<th width="14%" align="left">Referral Number</th>
										<th style="font-size:11px" width="12%" align="center">Department</th>
										<th style="font-size:11px" width="21%" align="center">Admission Type</th>
										<th style="font-size:11px" width="21%" align="center">Date Admitted</th>
										<th style="font-size:11px" width="*" align="center">Create New</th>
								</tr>
						</thead>
						<tbody>
								<tr>
								</tr>
						</tbody>
				</table>
				<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
		</div>

		<input type="hidden" name="sid" value="<?php echo $sid?>">
		<input type="hidden" name="lang" value="<?php echo $lang?>">
		<input type="hidden" name="cat" value="<?php echo $cat?>">
		<input type="hidden" name="userck" value="<?php echo $userck ?>">
		<input type="hidden" name="mode" value="search">
		<input type="hidden" name="pid" id="pid" value="<?=$pid?>"/>

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
echo '<form name=backbut onSubmit="return false">
<input type="hidden" name="sid" value="'.$sid.'">
<input type="hidden" name="lang" value="'.$lang.'">
<input type="hidden" name="userck" value="'.$userck.'">
</form>';
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