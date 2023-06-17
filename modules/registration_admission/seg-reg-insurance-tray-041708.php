<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/registration_admission/ajax/reg-insurance.common.php");
require($root_path.'include/inc_environment_global.php');

#require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
#$srvObj=new SegLab();

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
$breakfile=$root_path."modules/registration_admission/seg-close-window.php".URL_APPEND."&userck=$userck";
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
 $smarty->assign('sOnLoadJs','onLoad="preSet();"');

 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--
var AJAXTimerID=0;
var lastSearch="";

function preSet(){
	document.getElementById('search').focus();
}

function startAJAXSearch(searchID) {
	var searchEL = $(searchID);
	
	if (searchEL) {
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		AJAXTimerID = setTimeout("xajax_populateInsurance('"+searchID+"','"+searchEL.value+"')",200);
		lastSearch = searchEL.value;
	}
}

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		searchEL.style.color = "";
	}
}

function enableSearch(){
	//alert(enableSearch);
	var rowSrc, list;
	document.getElementById("search").value="";
	list = $('product-list');
	dBody=list.getElementsByTagName("tbody")[0];
	rowSrc = '<tr><td colspan="9" style="">No such insurance firm exists...</td></tr>';
	dBody.innerHTML = null;
	dBody.innerHTML += rowSrc;
}

//added by VAN 04-17-08
function checkEnter(e,searchID){
	//alert('e = '+e);	
	var characterCode; //literal character code will be stored in this variable

	if(e && e.which){ //if which property of event object is supported (NN4)
		e = e;
		characterCode = e.which; //character code is contained in NN4's which property
	}else{
		e = event;
		characterCode = e.keyCode; //character code is contained in IE's keyCode property
	}

	if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
		startAJAXSearch(searchID);
	}else{
		return true;
	}		
}

// -->
</script> 
<script type="text/javascript" src="<?=$root_path?>modules/registration_admission/js/reg-insurance-tray-gui.js?t=<?=time()?>"></script>
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

	<table width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%">
		<tbody>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<div style="padding:4px 2px; padding-left:10px; ">
						Search Insurance <input id="search" name="search" class="segInput" type="text" style="width:60%; margin-left:10px; font: bold 12px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id)" onKeyPress="checkEnter(event,this.id)"/>
						<input type="image" id="search_img" name="search_img" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search');return false;" align="absmiddle" />
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:265px; width:100%; background-color:#e5e5e5">
						<table id="product-list" class="segList" cellpadding="1" cellspacing="1" width="100%">
							<thead>
								<tr>
									<th width="4%" align="left"></th>
									<th width="0.5" align="left"></th>
									<th width="*" align="left">&nbsp;Insurance Company</th>
									<th width="1" align="center">&nbsp;</th>
									<th width="25%">Insurance No.</th>
									<th width="18%">Principal Holder</th>
									<th width="2%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="9" style="font-weight:normal">No such insurance firm exists...</td>
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
