<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/pharmacy/ajax/pharma-ward.common.php");

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/


define('LANG_FILE','order.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$title=$LDPharmacy;
$breakfile=$root_path."modules/pharmacy/seg-pharma-order-functions.php".URL_APPEND;
$imgpath=$root_path."pharma/img/";

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
 $smarty->assign('sToolbarTitle',"Pharmacy::Wards List");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Pharmacy::Wards List");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""');

 # Collect javascript code
 ob_start()

?>
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
<style type="text/css">
<!--
.tabFrame {
	margin:5px;
}
-->
</style> 

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script language="javascript" type="text/javascript">
<!--
	function appendWard(list, details) {
		if (!list) list = $('wardList');
		if (list) {
			var dBody=list.getElementsByTagName("tbody")[0];
			if (dBody) {
				var lastRowNum = null;
						dRows = dBody.getElementsByTagName("tr");
				if (details) {
					alt = (dRows.length%2)+1;
					id = details.id;
					name = details.name;
				
					src = 
						'<tr class="wardlistrow'+alt+'">' +
						'<tr class="wardlistrow'+alt+'" id="row_'+id+'">'+
							'<td style="white-space:nowrap">'+
								'<input type="hidden" name="ids" value="'+id+'">'+
								'<span id="d_'+id+'">'+name+'</span>'+
								'<input type="text" id="i_'+id+'" class="jedInput" style="display:none;width:350px" value="'+name+'">'+
								'<input id="s_'+id+'" class="jedButton" type="button" style="color:#000060;display:none" value="Save" onclick="prepareSave(\''+id+'\')"/>'+
							'</td>'+
							'<td style="white-space:nowrap">\n'+
								'<img title="Edit" class="segSimulatedLink" src="<?= $root_path ?>images/cashier_edit.gif" border="0" align="absmiddle" onclick="prepareEdit(\''+id+'\')"/>\n'+
								'<img title="Delete" class="segSimulatedLink" src="<?= $root_path ?>images/cashier_delete.gif" border="0" align="absmiddle" onclick="if (confirm(\'Do you wish to delete this entry?\')) prepareDelete(\''+id+'\')"/>\n'+
							'</td>'+
						'</tr>';
				}
				else {
					src = "<tr><td colspan=\"8\">Ward list is empty...</td></tr>";	
				}
				dBody.innerHTML += src;
				return true;
			}
		}
		return false;
	}
	
	function showEdit(id,show) {
		if ($('d_'+id))	$('d_'+id).style.display = show ? 'none' : '';
		if ($('i_'+id))	$('i_'+id).style.display = show ? '' : 'none';
		if ($('e_'+id))	$('e_'+id).style.display = show ? 'none' : '';
		if ($('s_'+id))	$('s_'+id).style.display = show ? '' : 'none';
	}
	
	function updateWard(id,newname) {
		if ($('d_'+id))	$('d_'+id).innerHTML = newname;
		if ($('i_'+id))	$('i_'+id).value = newname;
		showEdit(id, false);
	}

	function prepareEdit(id) {
		var ids = document.getElementsByName('ids');
		for (var i=0;i<ids.length;i++) {
			if (ids[i].value != id) showEdit(ids[i].value, false);
		}
		showEdit(id, true);
	}

	function prepareSave(id) {
		if ($('i_'+id))	xajax_editWard(id, $('i_'+id).value);
	}
	
	function prepareDelete(id) {
		var name = $('d_'+id).innerHTML;
		if ($('i_'+id))	{
			xajax_deleteWard(id, $('i_'+id).value);
			alert("Ward '"+name+"' succesfully deleted...");
		}
		
	}
	
	function prepareAdd(details) {
		appendWard(null, details);
		$('neward').value='';
		alert('New ward added...');
	}
	
	function removeItem(id) {
		var destTable, destRows;
		var table = $('wardList');
		var rmvRow=document.getElementById("row_"+id);
		if (table && rmvRow) {
			var rndx = rmvRow.rowIndex-1;
			table.deleteRow(rmvRow.rowIndex);
			if (!document.getElementsByName("ids") || document.getElementsByName("ids").length <= 0)
				appendOrder(table, null);
			reclassRows(table,rndx);
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
-->
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
# Buffer page output
global $db;

include($root_path."include/care_api_classes/class_pharma_ward.php");
$wClass = new SegPharmaWard();

# Resolve current page (pagination)
$current_page = $_REQUEST['page'];
if (!$current_page) $current_page = 0;
$list_rows = 15;
switch (strtolower($_REQUEST['jump'])) {
	case 'last':
		$current_page = $_REQUEST['lastpage'];
	break;
	case 'prev':
		if ($current_page > 0) $current_page--;
	break;
	case 'next':
		if ($current_page < $_REQUEST['lastpage']) $current_page++;
	break;
	case 'first': default:
		$current_page=0;
	break;
}	

$count=0;
$rows = "";
$last_page = 0;
if ($_POST['submitted']) {
}

$result = $wClass->getAll();
if ($result) {			
	$rows_found = $wClass->FoundRows();
		/*
		if ($rows_found) {
			$last_page = floor($rows_found / $list_rows);
			$first_item = $current_page * $list_rows + 1;
			$last_item = ($current_page+1) * $list_rows;
			if ($last_item > $rows_found) $last_item = $rows_found;
			$nav_caption = "Showing ".number_format($first_item)."-".number_format($last_item)." out of ".number_format($rows_found)." record(s)";
		}
		*/
	while ($row=$result->FetchRow()) {
		$class = (($count%2)==0)?"":"wardlistrow2";
		$rows .= "		<tr class=\"$class\" id=\"row_".$row["ward_id"]."\">
				<td style=\"white-space:nowrap\">
					<input type=\"hidden\" name=\"ids\" value=\"".$row['ward_id']."\">
					<span id=\"d_".$row["ward_id"]."\">".$row["ward_name"]."</span>
					<input type=\"text\" id=\"i_".$row["ward_id"]."\" class=\"jedInput\" style=\"display:none;width:75%\" value=\"".$row["ward_name"]."\"/>
					<input id=\"s_".$row["ward_id"]."\" class=\"jedButton\" type=\"button\" style=\"color:#000060;display:none\" value=\"Save\" onclick=\"prepareSave('".$row["ward_id"]."')\"/>
				</td>
				<td style=\"white-space:nowrap\">
					<img title=\"Edit\" class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_edit.gif\" border=\"0\" align=\"absmiddle\" onclick=\"prepareEdit('".$row["ward_id"]."')\"/>
					<img title=\"Delete\" class=\"segSimulatedLink\" src=\"".$root_path."images/cashier_delete.gif\" border=\"0\" align=\"absmiddle\" onclick=\"if (confirm('Do you wish to delete this entry?')) prepareDelete('".$row["ward_id"]."')\"/>
				</td>
			</tr>\n";
		$count++;
	}
}
if (!$rows) $rows = '		<tr><td colspan="6">No details found...</td></tr>';

ob_start();
?>

<br>
<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform" onSubmit="return validate()">


<div style="width:400px">
	<div>
		<table border="0" cellpadding="0" cellspacing="3">
			<tr>
				<td>Ward name <input id="neward" type="text" class="jedInput" width="350px" /></td>
				<td><input type="button" class="jedButton" value="Add ward" onclick="if ($('neward').value) xajax_newWard($('neward').value); else { alert('Please enter the ward name.'); $('neward').focus()}"/></td>
			</tr>
		</table>
	</div>
	<div class="segContentPane">
		<table id="wardList" class="jedList" width="100%" border="0" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th width="99%" nowrap="nowrap">Ward name</th>
					<th width="*">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
<?= $rows ?>
			</tbody>
		</table>
	</div>
</div>
<br />

<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump">

</form>
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

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

$smarty->assign('sMainFrameBlockData',$sTemp);

/**
 * show Template
**/
$smarty->display('common/mainframe.tpl');
?>	