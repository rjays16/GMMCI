<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/registration_admission/ajax/address_wizard.common.php");

//LISTGEN YEHEY
require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);

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

$title="Registration/Admission::Address Wizard";
$breakfile=$root_path."modules/registration_admission/seg-close-window.php".URL_APPEND."&userck=$userck";

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
 $smarty->assign('sOnLoadJs','onLoad="init()"');

 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--
var AJAXTimerID=0;
var lastSearch="";

function init() {
 	shortcut.add('ESC', closeMe,
		{
			'type':'keydown',
			'propagate':false,
		}
	);
	
	alst.reload();
	setTimeout("$('search').focus()",100);
}

function closeMe() {
	window.parent.cClick();
}

<?php
	$varArray = array(
		'var_pid'=>'',
		'var_rid'=>'',
		'var_encounter_nr'=>'',
		'var_discountid'=>'',
		'var_discount'=>'',
		'var_name'=>'',
		'var_addr'=>'',
		'var_clear'=>'',
		'var_enctype'=>'',
		'var_enctype_show'=>'',
		'var_include_enc'=>'0',
		#added by VAN
		'var_enctype'=>'',
		'var_location'=>'',
		'var_medico'=>'0'
	);

	foreach ($varArray as $i=>$v) {
		$value = $_REQUEST[$i];
		if (!$value) $value = $v;
		if (!is_numeric($value)) $value = "'$value'";
		echo "var $i=$value;\n";
	}
?>

function startSearch() {
	var kword = $('search').value;
	var addr = document.forms[0].addr
	for (var i=0; i<addr.length; i++) {
		if (addr[i].checked) {
			mode = addr[i].value;
			break;
		}
	}
	alst.currentPage = 0;
	alst.fetcherParams = [kword, mode];
	alst.reload();
}

function updateControls() {
	var s = $('search').value;
	$('search-btn').disabled = (s.length < 1);
}

function prepareSelect(mode, id) {
	window.parent.jsEnableAddresses(0);
	switch (mode) {
		case 'B':
			window.parent.xajax_setBarangay(id);
		break;
		case 'M':
			window.parent.xajax_setMuniCity(id);
		break;
		case 'P':
			window.parent.xajax_setProvince(id);
		break;
	}

	if (window.parent.pSearchClose) window.parent.pSearchClose();
	else if (window.parent.cClick) window.parent.cClick();
}

// -->
</script> 
<script type="text/javascript" src="<?=$root_path?>modules/registration_admission/js/address_wiz.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<?php
$listgen->printJavascript($root_path);
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
	<FORM name="top_form" action="" method="post" onsubmit="return false">
	<table width="99%" cellspacing="1" cellpadding="1" style="margin:5px">
		<tbody>
			<tr>
				<td>
					<table width="100%" border="0" cellpadding="2" cellspacing="0" style="font:bold 12px Arial; background-color:#dcdcdc; color:#2d2d2d;">
						<tr>
							<td valign="middle" style="padding-left:10px">
								<input class="jedInput" type="radio" name="addr" id="brgy" checked="checked" value="B" onclick="startSearch()"/>
								<label class="jedInput" for="brgy">Barangay</label>
							</td>
							<td valign="middle" >
								<input class="jedInput" type="radio" name="addr" id="municity" value="M" onclick="startSearch()"/>
								<label class="jedInput" for="municity">Municipality/City</label>
							</td>
							<td valign="middle" >
								<input class="jedInput" type="radio" name="addr" id="province" value="P" onclick="startSearch()"/>
								<label class="jedInput" for="province">Province</label>
							</td>
							<td valign="middle" >
								<input class="jedInput" type="radio" name="addr" id="zipcode" value="Z" onclick="startSearch()"/>
								<label class="jedInput" for="zipcode">Zip Code</label>
							</td>
							<td valign="middle" >
								<input class="jedInput" type="radio" name="addr" id="all" value="" onclick="startSearch()"/>
								<label class="jedInput" for="all">All</label>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<table width="95%" border="0" cellpadding="2" cellspacing="0" style="font:bold 12px Arial; color:#2d2d2d; margin:2px; margin-left:10px">
						<tr>
							<td width="15%" nowrap="nowrap">
								Search keyword
							</td>
							<td valign="middle" width="*">
								<input id="search" class="jedInput" type="text" style="width:60%; font: bold 12px Arial" align="absmiddle" onkeyup="updateControls(); if (event.keyCode == 13) startSearch()" onclick="updateControls()"/>
								<input class="jedInput" id="search-btn" type="button" align="absmiddle" disabled="disabled" value="Search" onclick="startSearch()"/>
								<input class="jedInput" id="refresh-btn" type="button" align="absmiddle" value="Refresh!" onclick="startSearch()"/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
<?php
	$listgen->setListSettings('MAX_ROWS','10');
	$alst = &$listgen->createList('alst',
		array('Code', 'Name', 'Full Address', 'Location', ''),
		array(0,1,0,0,NULL),
		'populateAddress');
	$alst->addMethod = 'addItem';
	$alst->fetcherParams = array();
	$alst->columnWidths = array("10%","20%","*", "20%", "5%");
	print $alst->getHTML();
?>
				</td>
			</tr>
		</tbody>
	</table>


	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">

	</FORM>
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
