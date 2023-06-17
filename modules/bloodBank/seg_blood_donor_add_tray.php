<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/bloodBank/ajax/blood-donor-register.common.php");
require($root_path.'include/inc_environment_global.php');
$xajax->printJavascript($root_path.'classes/xajax_0.5'); 
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

 $smarty->assign('sOnLoadJs','onLoad="preSet('.$_GET['donorID'].');"');   
 # Collect javascript code
 ob_start(); 
?>
<script type="text/javascript" src="<?=$root_path?>modules/laboratory/js/request-tray-gui.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script language="javascript" >
function preSet(donorID)
{
	 xajax_populateDonationList(donorID, 0);
}

var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
function setPagination(pageno, lastpage, pagen, total) {
		currentPage=parseInt(pageno);
		lastPage=parseInt(lastpage);    
		firstRec=(parseInt(pageno)*pagen)+1;
		totalRows=total;
		 
		if (currentPage==lastPage)
				lastRec = total;
		else
				lastRec = (parseInt(pageno)+1)*pagen;
		
		if (parseInt(total)==0)
		{
				$("pageShow").innerHTML = '<span>Showing '+(lastRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
		}
		else if(parseInt(total)>0)
		{
				$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
		
				$("pageFirst").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
				$("pagePrev").className = (currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
				$("pageNext").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
				$("pageLast").className = (currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
		}
		else
		{
				 $("pageShow").innerHTML = '<span>Showing 0 out of 0 record(s).</span>';
		}    
}

function jumpToPage(el, jumpType, set) {
		if (el.className=="segDisabledLink") return false;
		if (lastPage==0) return false;
		switch(jumpType) {
				case FIRST_PAGE:
						if (currentPage==0) return false;
						startAJAXSearch(0);
						document.getElementById('pagekey').value=0;
				break;
				case PREV_PAGE:
						if (currentPage==0) return false;
						startAJAXSearch(parseInt(currentPage)-1);
						document.getElementById('pagekey').value=currentPage-1;
				break;
				case NEXT_PAGE:
						if (currentPage >= lastPage) return false;
						startAJAXSearch(parseInt(currentPage)+1);
						document.getElementById('pagekey').value=parseInt(currentPage)+1;
				break;
				case LAST_PAGE:
						if (currentPage >= lastPage) return false;
						startAJAXSearch(parseInt(lastPage));
						document.getElementById('pagekey').value=parseInt(lastPage);
				break;
		}
}

function addslashes(str) {
		str=str.replace("'","\\'");
		return str;
}

function refreshFrame(outputResponse)
{
		alert(""+outputResponse);
		window.location.reload(); 
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

function endAJAXList(listID) {
		var listEL = $(listID);
		if (listEL) {
				//$("ajax-loading").style.display = "none";
				$("donorlist-body").style.display = "";
				searchEL.style.color = "";
		}
}

function addBlood(donorID)
{
	xajax_saveBloodDetails(donorID,document.getElementById('donate_qty').value,document.getElementById('donate_unit').value,document.getElementById('donate_date').value);
}

function viewDonationList(listID, donorID, itemID, date, qty, unit)
{
		var list=$(listID), dRows, dBody, rowSrc;
		var i;
		var classified, mode, editlink;
		 //alert("hello");
		if (list) {
		//alert("hi="+donorID);
				dBody=list.getElementsByTagName("tbody")[0];
				dRows=dBody.getElementsByTagName("tr");
				if (donorID) {
				//alert("donor id="+donorID);
						alt = (dRows.length%2)+1;
						 text1="Edit Item";
						 text2="Delete Item";
						 
						rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+addslashes(donorID)+'" value="'+donorID+'">'+
														'<td width="10%" align="left">'+date+'</td>'+
														'<td width="5%" align="center">'+qty+'</td>'+
														'<td width="5%" align="center">'+unit+'</td>'+
														'<td width="5%" align="center">'+
						'<input class="jedButton" type="image" id="edit" border="0" src="../../images/edit.gif" onclick="EditItem(\''+donorID+'\',\''+itemID+'\'); return false;" onmouseover="tooltip(\''+text1+'\');" onMouseout="return nd();"/> '+
						'<input class="jedButton" type="image" id="delete" border="0" src="../../images/delete.gif" onclick="DeleteItem(\''+donorID+'\',\''+itemID+'\'); return false;" onmouseover="tooltip(\''+text2+'\');" onMouseout="return nd();"/> '+  
														'</td>'+ 
														'<td>&nbsp;</td>'+ 
											'</tr>';        
				} 
				else {
						rowSrc = '<tr><td colspan="10" style="">No blood donated yet...</td></tr>';
				}
				dBody.innerHTML += rowSrc;
				//alert("dBody="+dBody.innerHTML); 
		}
		 
}

function EditItem(editID, itemID)
{
	 // xajax_getDonorDetails(editID); 
		//alert("test6="+editDonorDetails);
		return overlib(
					OLiframeContent('seg_blood_item_edit_tray.php?donorID='+editID+'&itemID='+itemID, 330, 150, 'fOrderTray', 1, 'auto'),
																	WIDTH,330, TEXTPADDING,0, BORDER,0, 
																		STICKY, SCROLL, CLOSECLICK, MODAL, 
																		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 >',
																 CAPTIONPADDING,4, CAPTION,'Edit Blood Item',
																 MIDX,0, MIDY,0, 
																 STATUS,'Edit Blood Item');
}

function DeleteItem(delID, itemID)
{
		var reply = confirm("Are you sure you want to delete this blood item?"+itemID+" "+delID);
		xajax_deleteBloodItem(delID, itemID);
		xajax_populateDonationList(delID, 0);
}

function tooltip(text) 
{
				return overlib('<span style="font:bold 11px Tahoma">'+text+'</span>',
						TEXTPADDING,4, BORDER,0,
						VAUTO, WRAP);
}
</script> 
<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform"> 
<div style="width:100%">    
				 <div style="padding:10px;width:95%;border:0px solid black">
				 <font class="warnprompt"></font>
				 <table border="0" width="100%">
				 <tbody class="submenu">
						<tr>
								<td class="segPanelHeader" align="right" width="140"><b>Donor:</b></td>
								<td class="segPanel" width="70%">        
										<input type="text" size="35" id="donor_name" value="<? global $db;
														$sql="select CONCAT(IF (trim(first_name) IS NULL,'',trim(first_name)),' ',IF(trim(middle_name) IS NULL ,'',trim(middle_name)),' ',
																					 IF(trim(last_name) IS NULL,'',trim(last_name))) as name from seg_donor_info where donor_id='".$_GET['donorID']."'";
														$result=$db->Execute($sql);
														$row=$result->FetchRow();
														echo  strtoupper($row['name']);?>" readonly=""/>
								</td>
						</tr>   
						<tr>
								<td class="segPanelHeader" align="right" width="140"><b>Date:</b></td>
								<td class="segPanel">
										<input type="text" size="15" id="donate_date" value=""></input>
										<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="donate_trigger" style="cursor:pointer">[YYYY-mm-dd]
										 <script type="text/javascript">
												Calendar.setup (
												{
														inputField : "donate_date", 
														ifFormat : "%Y-%m-%d", 
														showsTime : false, 
														button : "donate_trigger", 
														singleClick : true, 
														step : 1
												}
												);
												</script>
								</td>
						</tr>
						<tr>
							<td class="segPanelHeader" align="right" width="140"><b>Quantity:</b></td>
							<td class="segPanel" width="70%">
								<input type="text" size="5" id="donate_qty"/>
								<select name="donate_unit" id="donate_unit">
								<?php
										global $db;
										$sql="select unit_name from seg_unit";
										$result = $db->Execute($sql);
										$options_unit=""; 
										while($row=$result->FetchRow())
										{
												$options_unit.='<option value="'.$row['unit_name'].'">'.$row['unit_name'].'</option>\n';
										}
										echo $options_unit;
								?>
								</select>
							</td>
						</tr>          
				</tbody>
				</table>
				<table>
				<tr>
					<td><input id="addBlood" name="addBlood" type="image" src="../../gui/img/control/default/en/add_blood_item.png" border=0 width="72" height="23"  alt="addBlood" align="absmiddle" onclick="addBlood('<?echo $_GET['donorID'];?>'); return false;"/> <a href ="javascript:window.parent.cClick();"><input id="cancel" name="cance" type="image" src="../../gui/img/control/default/en/cancel_blood_item.png" border=0 width="72" height="23"  alt="cancel" align="absmiddle"/></a></td>
				</tr>
				</table>
				<br>
<div  style="display:block; border:1px solid #8cadc0; overflow-y:hidden; width:100%; background-color:#e5e5e5">
<table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
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
</div>
<table id="donorlist" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
		<thead>
				<tr>
						<th rowspan="3" width="10%" align="left">Donor Date</th>
						<th rowspan="3" width="5%" align="center">Quantity</th>
						<th rowspan="3" width="5%" align="center">Unit</th> 
						<th rowspan="3" width="1%"></th> 
						<th width="5%"></th>
				</tr>
		</thead>
		<tbody id="donorlist-body">
				<tr><td colspan="6" style="">No blood donated yet...</td></tr>
		</tbody>
</table>
				</div>   
</div>
</form>
	
		<input type="hidden" name="sid" value="<?php echo $sid?>">
		<input type="hidden" name="lang" value="<?php echo $lang?>">
		<input type="hidden" name="cat" value="<?php echo $cat?>">
		<input type="hidden" id="userck" name="userck" value="<?php echo $userck ?>">
		<input type="hidden" name="mode" value="search">


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

</div>
<?php

$sTemp = ob_get_contents();
ob_end_clean();
 $smarty->assign('sHiddenInputs',$sTemp);
# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
