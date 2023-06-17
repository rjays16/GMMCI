<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/pharmacy/ajax/order-psearch.common.php");
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
$local_user='ck_prod_db_user';
	$lang_tables[] = 'departments.php';
	define('LANG_FILE','konsil.php');
	define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

$thisfile=basename(__FILE__);
/*
switch($cat)
{
	case "pharma":
							$title=$LDPharmacy;
							//$breakfile=$root_path."modules/pharmacy/apotheke-datenbank-functions.php".URL_APPEND."&userck=$userck";
							$breakfile=$root_path."modules/pharmacy/seg-close-window.php".URL_APPEND."&userck=$userck";
							$imgpath=$root_path."pharma/img/";
							break;
	case "medlager":
							$title=$LDMedDepot;
							//$breakfile=$root_path."modules/med_depot/medlager-datenbank-functions.php".URL_APPEND."&userck=$userck";
							$breakfile=$root_path."modules/pharmacy/seg-close-window.php".URL_APPEND."&userck=$userck";
							$imgpath=$root_path."med_depot/img/";
							break;
	default:  
							$cat = "pharma";
							$title=$LDMedDepot;
							$breakfile=$root_path."modules/pharmacy/seg-close-window.php".URL_APPEND."&userck=$userck";
							$imgpath=$root_path."pharma/img/";
							break;
}
*/
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
 $smarty->assign('sToolbarTitle',"$title $LDPharmaDb $LDSearch");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title $LDPharmaDb $LDSearch");

 # Assign Body Onload javascript code
# $smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');

 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--
var AJAXTimerID=0;
var lastSearch="";

function startAJAXSearch(searchID) {
	var searchEL = $(searchID);
	var searchLastname = $('firstname-too').checked ? '1' : '0';
	var iscash = window.parent.document.getElementById('iscash1');
	var with_enc_only = iscash.checked? 0:1;   // NOTE : intentionally in reverse order
	
	if (searchEL && lastSearch != searchEL.value) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		$("person-list-body").style.display = "none";
//		alert("startAJAXSearch : with_enc_only='"+with_enc_only+"'");
		AJAXTimerID = setTimeout("xajax_populatePersonList('"+searchID+"','"+searchEL.value+"',"+searchLastname+","+with_enc_only+")",500);
	}
}

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		$("person-list-body").style.display = "";
		searchEL.style.color = "";
	}
}

// -->
</script> 
<script type="text/javascript" src="<?=$root_path?>modules/pharmacy/js/order-person-search-gui.js?t=<?=time()?>"></script>
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
	<table width="98%" cellspacing="2" cellpadding="2" style="margin:1%">
		<tbody>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<table width="95%" border="0" cellpadding="0" cellspacing="0" style="font:bold 12px Arial; color:#2d2d2d; margin:1%">
						<tr>
							<td width="15%">
								Search person<br />
								<a href="javascript:gethelp('person_search_tips.php')" style="text-decoration:underline">Tips & tricks</a>
							</td>
							<td valign="middle" width="*">
								<input id="search" class="segInput" type="text" style="width:60%; font: bold 12px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id)" />
								<input type="image" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search');return false;" align="absmiddle" /><br />
							</td>
						</tr>
						<tr>
							<td></td>
							<td><input type="checkbox" id="firstname-too" checked> Search for first names too.</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:290px; width:100%; background-color:#e5e5e5">
						<table id="person-list" class="segList" cellpadding="1" cellspacing="1" width="100%">
							<thead>
								<tr>
									<th width="8%">HRN</th>
									<th width="4%">Sex</th>
									<th width="18%">Lastname</th>
									<th width="18%">Firstname</th>
									<th width="10%" style="font-size:11px">Date of Birth</th>
									<th width="10%">ZIP</th>
									<th width="8%">Status</th>
									<th width="8%">Type</th>
									<th width="1%"></th>
								</tr>
							</thead>
							<tbody id="person-list-body">
								<tr>
									<td colspan="9" style="font-weight:normal">No such person exists...</td>
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
