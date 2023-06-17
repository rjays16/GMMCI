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

	require($root_path."modules/laboratory/ajax/lab-new.common.php");
	$xajax->printJavascript($root_path.'classes/xajax');
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
$breakfile = "labor.php";

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
 $smarty->assign('sToolbarTitle',"$LDLab :: Requests Status List");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 #$smarty->assign('breakfile',$breakfile);
 #edited by VAN 07-28-08
 $popUp = $_GET['popUp'];
 $is_doctor = $_GET['is_doctor'];

 if ($popUp!='1'){
		 # href for the close button
		 $smarty->assign('breakfile',$breakfile);
 }else{
		# CLOSE button for pop-ups
		$smarty->assign('breakfile','javascript:window.parent.cClick();');
		$smarty->assign('pbBack','');
 }
 #-------------------


 # Window bar title
 $smarty->assign('sWindowTitle',"$LDLab :: Requests Status List");

 # Assign Body Onload javascript code
 #$smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');
 #$smarty->assign('sOnLoadJs','');
 $smarty->assign('sOnLoadJs','onLoad="preSet(); ShortcutKeys(); startAJAXSearch(\'search\', 0, 1);"');

#added by VAN 07-02-08
#echo "done = ".$_GET['done'];
#$done = $_GET['done'];

#added by VAN 07-28-08
require_once($root_path.'include/care_api_classes/class_encounter.php');
$encObj=new Encounter();

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj = new Ward;

$encounter_nr = $_GET['encounter_nr'];
$personInfo = $encObj->getEncounterInfo($encounter_nr);
#echo $personInfo['name_first'];
#echo $encounter_nr;

 # Collect javascript code
 ob_start()

?>

<!--added by VAN 02-06-08-->
<!--for shortcut keys -->
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript" >
<!--

function preSet(){
	document.getElementById('search').focus();
}

/*
function deleteRequest(refno){
	var answer = confirm("Are you sure you want to delete the laboratory request with a reference no. "+(refno)+"?");
		if (answer){
			xajax_deleteRequest(refno);
		}
}

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
}
*/

//---------------adde by VAN 02-06-08
function ShortcutKeys(){
		shortcut.add('Ctrl+Shift+N', NewRequest,
								{
									'type':'keydown',
									'propagate':false,
								}
						);

		shortcut.add('Ctrl+Shift+L', RequestList,
							{
								'type':'keydown',
								'propagate':false,
							}
						 )

		shortcut.add('Ctrl+Shift+S', SearchItem,
							{
								'type':'keydown',
								'propagate':false,
							}
						 )

		shortcut.add('Ctrl+Shift+M', BackMainMenu,
							{
								'type':'keydown',
								'propagate':false,
							}
						 )
	}

	function BackMainMenu(){
		urlholder="labor.php<?=URL_APPEND?>";
		window.location.href=urlholder;
	}

	function NewRequest(){
		//urlholder="seg-lab-request-new.php<?=URL_APPEND?>&user_origin=<?=$user_origin?>";
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabnew&user_origin=lab";
		window.location.href=urlholder;
	}

	function RequestList(){
		//urlholder="seg-lab-request-order-list.php<?=URL_APPEND?>&user_origin=<?=$user_origin?>";
		urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabservrequest_new&user_origin=lab";
		window.location.href=urlholder;
	}

	function SearchItem(){
		startAJAXSearch('search',0,0);
	}

//------------------------------------------

//--------------added by VAN 09-12-07------------------
var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var lastSearch="";

function startAJAXSearch(searchID, page, mod) {
	var searchEL = $(searchID);
	//var done = $F('done');
	var pid = '<?=$personInfo['pid']?>';

	var done;
	//alert("done = "+done);

	//added by VAN 07-28-08
	if ($F('is_doctor'))
		done = 1;
	else
		done = $F('done');

	if (mod)
		searchEL.value = "*";

	keyword = searchEL.value;
	keyword = keyword.replace("'","^");

	//var searchLastname = $('firstname-too').checked ? '1' : '0';
	var searchLastname = 1;
	var is_doctor = $F('is_doctor');
	var encounter_nr = $F('encounter_nr');

	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		$("RequestList-body").style.display = "none";
		//AJAXTimerID = setTimeout("xajax_populateRequestList('"+searchID+"','"+searchEL.value+"',"+page+","+searchLastname+",0)",100);
		AJAXTimerID = setTimeout("xajax_populateLabResultList("+done+", '"+searchID+"','"+keyword+"',"+page+","+searchLastname+",0,"+encounter_nr+", "+is_doctor+",'"+pid+"','LB',1)",100);
		lastSearch = searchEL.value;
	}
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

	//$("pageShow").innerHTML = '<span>Showing '+formatNumber((firstRec),0)+'-'+formatNumber((lastRec),0)+' out of '+formatNumber((parseInt(total)),0)+' record(s).</span>';
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
			startAJAXSearch('search',0,0);
		break;
		case PREV_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',currentPage-1,0);
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(currentPage)+1,0);
		break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',lastPage,0);
		break;
	}
}

function refreshWindow(){
	//window.location.href=window.location.href;
}

function viewLabResult(refno,code,pid,group_id,form_id,nth_take){     
	//window.open("../../modules/laboratory/seg-lab-request-result-summary-pdf.php?refno="+refno+"&service_code="+service_code+"&status=1&showBrowser=1","viewLaboratoryResult","width=950,height=700,fullscreen=yes,menubar=no,resizable=yes,scrollbars=yes");
	if(form_id!=0){
		window.open("../../modules/repgen/pdf_lab_results_specialforms.php?pid="+pid+"&refno="+refno+"&group_id="+group_id+"&service_code="+code+"&nth_take="+nth_take,"viewPatientResult","left=150, top=100, width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
	}else{
	window.open("../../modules/repgen/pdf_lab_results.php?pid="+pid+"&refno="+refno+"&group_id="+group_id+"&service_code="+code+"&nth_take="+nth_take,"viewPatientResult","left=150, top=100, width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
}
}

function viewResult(filename){
	 //window.open("seg-lab-result-pdf.php?refno="+refno+"&showBrowser=1","viewPatientResult","left=150, top=100, width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
     // link to view the HL7 message result - pdf format
     
     //window.open("seg-lab-result-pdf-link.php?refno="+refno+"&showBrowser=1","viewPatientResult","left=150, top=100, width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
     //window.open("seg-lab-result_pdf-view.php?refno="+refno+"&pid="+pid+"&lis="+lis_order_no+"&showBrowser=1","viewPatientResult","left=150, top=100, width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
     window.open("seg-lab-result-view.php?filename="+filename+"&showBrowser=1","viewPatientResult","left=150, top=100, width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
}

function viewParsedResult(pid, lis_order_no){
	 return overlib(
                    OLiframeContent("seg-lab-parsedresult-view.php?pid="+pid+"&lis_order_no="+lis_order_no, 800, 400, "fOrderTray", 1, "auto"),
                                                                    WIDTH,400, TEXTPADDING,0, BORDER,0,
                                                                        STICKY, SCROLL, CLOSECLICK, MODAL,
                                                                        CLOSETEXT, "<img src=../../images/close.gif border=0 >",
                                                                 CAPTIONPADDING,4, CAPTION,"Laboratory Result",
                                                                 MIDX,0, MIDY,0,
                                                                 STATUS,"Laboratory Result");
}


function addResultList(listID, service_name, service, req_date, group_id, refno, form_id, nth_take) {
                            
	var list=$(listID), dRows, dBody, rowSrc;
	var i, mode, editlink, ornum;
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");
		if (service) {
			alt = (dRows.length%2)+1;

			pid = "<?=$_GET['pid']?>";
			/*if (urgency=='Urgent')
				priority = '<font color="#FF0000">'+urgency+'</font>';
			else
				priority = urgency;
            
            if (withresult==0){
                icon_result = '<img src="../../images/cashier_view_red.gif" border="0" title="No Result fetch from the LIS yet. \nOr the result is manually generated. \nPlease ask the Laboratory for the result.">';
            }else{
                //icon_result = '<a href="javascript:void(0);" onClick=viewResult(\''+refno+'\',\''+pid+'\',\''+lis_order_no+'\');><img src="../../images/cashier_view.gif" border="0" title="Click for the Result"></a>';
                icon_result = '<a href="javascript:void(0);" onClick=viewResult(\''+filename+'\');><img src="../../images/cashier_view.gif" border="0" title="Click for the Result"></a>';
            }*/   
            // icon_result = '<a href="javascript:void(0);" onClick=viewResult(\''+filename+'\');><img src="../../images/cashier_view.gif" border="0" title="Click for the Result"></a>'+
            //               '&nbsp;<a href="javascript:void(0);" onClick=viewParsedResult(\''+pid+'\',\''+lis_order_no+'\');><img src="../../images/cashier_view_red.gif" border="0" title="Click for the Result"></a>';
			
			icon_result = '<a href="javascript:void(0);" onClick=viewLabResult(\''+refno+'\',\''+service+'\',\''+pid+'\',\''+group_id+'\',\''+form_id+'\',\''+nth_take+'\');><img src="../../images/cashier_view_red.gif" border="0" title="Click for the Result"></a>';
			
			/*rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+refno+'">'+
			         '<td style="font-size:11px">'+refno+'</td>'+
					 '<td style="font-size:11px">'+service+'</td>'+
					 '<td style="font-size:11px">'+req_date+'</td>'+
                     '<td style="font-size:11px">'+result_date+'</td>'+
					 '<td style="font-size:11px;color:#007">'+priority+'</td>'+
					 '<td align="center">'+icon_result+'</td>'+
					 '</tr>';*/
             rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+service+'">'+
                     '<td style="font-size:11px; text-align:center">'+req_date+'</td>'+
                     '<td style="font-size:11px">'+service_name+'</td>'+
                     '<td align="center">'+icon_result+'</td>'+
                     '</tr>';       
		}
		else {
			rowSrc = '<tr><td colspan="14">No requests available at this time...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}

function ReloadWindow(){
	window.location.href=window.location.href;
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

require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab();

require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
$hclabObj = new HCLAB;

$birthdate = "unknown";
if (($personInfo['date_birth'])&&($personInfo['date_birth']!='0000-00-00'))
	$birthdate = date("F d, Y",strtotime($personInfo['date_birth']));


ob_start();
?>

<a name="pagetop"></a>
<br>
<div style="padding-left:10px">
<form action="<?php echo $thisfile?>" method="post" name="suchform" onSubmit="">
	<div id="tabFpanel">
		<table border="0" cellspacing="2" cellpadding="2" width="99%" align="center">
			<tr>
				<td class="segPanelHeader" align="left" colspan="2"> Patient's Information </td>
			</tr>
			<tr>
				<td width="35%" class="segPanel"><strong>Patient Name</strong></td>
				<td class="segPanel"><?=mb_strtoupper($personInfo['name_last']).", ".mb_strtoupper($personInfo['name_first'])." ".mb_strtoupper($personInfo['name_middle'])?></td>
			</tr>
			<tr>
				<td class="segPanel"><strong>Hospital Record Number (HRN)</strong></td>
				<td class="segPanel"><?=$personInfo['pid']?></td>
			</tr>
			<tr>
				<td class="segPanel"><strong>Case Number</strong></td>
				<td class="segPanel"><?=$encounter_nr?></td>
			</tr>
			<tr>
				<td class="segPanel"><strong>Birthdate</strong></td>
				<td class="segPanel"><?=$birthdate?></td>
			</tr>
			<tr>
				<td class="segPanel"><strong>Age</strong></td>
				<td class="segPanel"><?=$personInfo['age']?></td>
			</tr>
			<tr>
				<td class="segPanel"><strong>Sex</strong></td>
				<?php
						if ($personInfo['sex']=='m')
							$sex = "MALE";
						else
							$sex = "FEMALE";
				?>
				<td class="segPanel"><?=$sex?></td>
			</tr>
			<tr>
				<td class="segPanel"><strong>Patient's Type</strong></td>
				<?php
					if ($personInfo['encounter_type']==1){
						$patient_type = "ERPx";
						$location = "ER";
					}elseif ($personInfo['encounter_type']==2){
						$patient_type = "OPDPx";
						if ($personInfo['current_dept_nr'])
							$dept = $dept_obj->getDeptAllInfo($personInfo['current_dept_nr']);

						$location = mb_strtoupper(stripslashes($dept['name_formal']));
					}else{
						$patient_type = "INPx";
						if ($personInfo['current_ward_nr'])
							$ward = $ward_obj->getWardInfo($personInfo['current_ward_nr']);

						$location = mb_strtoupper(stripslashes($ward['name']))." RM# ".$personInfo['current_room_nr'];
						/*
						if ($personInfo['encounter_type']==3)
							$patient_type = "INPx (FROM ER)";
						elseif ($personInfo['encounter_type']==4)
							$patient_type = "INPx (FROM OPD)";
						*/
					}
				?>
				<td class="segPanel"><?=$patient_type?></td>
			</tr>
			<tr>
				<td class="segPanel"><strong>Patient's Location</strong></td>
				<td class="segPanel"><?=$location?></td>
			</tr>
		</table>
	</div>
	<div id="tabFpanel">
		<div align="center" style="display:">
			<table width="100%" cellpadding="4">
				<tr>
					<td width="30%" align="center">
						<span>Enter the search key (Request Date).<br> Enter dates in <font color="#0000FF"><b>MM/DD/YYYY</b></font> format.
								Enter asterisk (<b>*</b>) to show all data.</span>
						<br><br>
						<input id="search" name="search" class="segInput" type="text" size="30" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial" align="absmiddle" onkeyup="" />
						<input type="image" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0,0);return false;" align="absmiddle" /><br />
					</td>
				</tr>
			</table>
		</div>
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
</div>
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">
</form>

<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden; overflow-x:hidden; width:90%; background-color:#e5e5e5">
<b>Refresh</b>&nbsp;&nbsp;<img src="../../images/cashier_refresh.gif" style="cursor:pointer" onclick="ReloadWindow();" />
	<table class="segList" width="100%" border="1" cellpadding="0" cellspacing="0">
		<thead>
			<tr class="nav">
			<th colspan="14">
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

<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; overflow-x:hidden; height:305px; width:90%; background-color:#e5e5e5">
<table id="RequestList" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">

	<thead>
		<tr>

			<!--<th width="12%" align="center">Batch No.</th>
			<th width="*" align="center">Service Requested</th>
			<th width="10%" align="center">Request Date</th>
            <th width="10%" align="center">Result Date</th>
			<th width="5%" align="center">Priority</th>-->
            <th width="20%" align="center">Result Received</th>
            <th width="*" align="center">Service(s) requested</th>
			<th width="3%" align="center">Results</th>

		</tr>
	</thead>

	<tbody id="RequestList-body">
		<?= $rows ?>
	</tbody>
</table>
<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
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
<input type="hidden" name="done" id="done" value="<?=$_GET['done']?>" />

<!-- added by VAN 07-28-08 -->
<input type="hidden" name="is_doctor" id="is_doctor" value="<?=($is_doctor)?1:0?>">
<input type="hidden" name="encounter_nr" id="encounter_nr" value="<?=$encounter_nr?>">

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
