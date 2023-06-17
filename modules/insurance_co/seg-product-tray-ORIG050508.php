<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/insurance_co/ajax/product-tray.common.php");
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
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

$thisfile=basename(__FILE__);

$title="Insurance Covered";
$breakfile=$root_path."modules/insurance_co/seg-close-window.php".URL_APPEND."&userck=$userck";

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
 #$smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');
 $smarty->assign('sOnLoadJs','onLoad="preset(); getServices();"');

 #$benefit = $_GET['benefit'];	
 #echo "benefit = ".$benefit;

 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--
var AJAXTimerID=0;
var lastSearch="";

function startAJAXSearch(searchID) {
	var searchEL = $(searchID);
	var areas = $('serv_areas');
	//alert("startAJAXSearch");
	//if (searchEL && lastSearch != searchEL.value) {
		//alert("startAJAXSearch");
		searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		//AJAXTimerID = setTimeout("xajax_populateProductList('"+searchID+"','"+searchEL.value+"')",200);
		AJAXTimerID = setTimeout("xajax_populateProductList('"+searchID+"','"+searchEL.value+"','"+areas.value+"')",200);
		lastSearch = searchEL.value;
	//}
}

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		searchEL.style.color = "";
	}
}

function enableSearch(){
	//alert('enableSearch');
	var areas = $('serv_areas');
	var rowSrc, rowBody;
	
	if (areas.value==0){
		$('search').readOnly = true;
		$('search').value = "";
		$('search_button').disabled = true;
	}else{
		//alert(areas.value);
		$('search').readOnly = false;
		$('search_button').disabled = false;
		$('search').value = "";
		
		if (areas.value=="OR"){
		
			rowSrc = '<tr>'+
			         	'<th width="*" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name/Description</th>'+
					     	'<th align="left">&nbsp;Code</th>'+
					     	'<th width="20%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;Maximum RVU</th>'+
					     	'<th width="1%"></th>'+
						'</tr>';
						
		}else{
		
			rowSrc = '<tr>'+
							'<th width="*" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name/Description</th>'+
					   	'<th align="left">&nbsp;Code</th>'+
					   	'<th width="20%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;Amount Limit</th>'+
					   	'<th width="1%"></th>'+
						'</tr>';	
		}		
		
		document.getElementById('header').innerHTML = rowSrc; 
	}
}

//added by VAN 05-05-08
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
		//startAJAXSearch(searchID,0);
		startAJAXSearch(searchID);
	}else{
		return true;
	}		
}


// -->
</script> 
<script type="text/javascript" src="<?=$root_path?>modules/insurance_co/js/product-tray-gui.js?t=<?=time()?>"></script>
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
						Hospital Service Area <select id="serv_areas" name="serv_areas" onChange="clearList('product-list'); enableSearch();">
										     				<!-- options here.. in javascript-->
													 </select>
					</div>
				</td>
			</tr>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<div style="padding:4px 2px; padding-left:10px; ">
						Search Item <input id="search" class="segInput" type="text" readonly="1" style="width:60%; margin-left:10px; font: bold 12px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id)" onKeyPress="checkEnter(event,this.id)" />
						<input type="image" src="<?= $root_path ?>images/his_searchbtn.gif" id="search_button" name="search_button" disabled onclick="startAJAXSearch('search');return false;" align="absmiddle" />
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:265px; width:100%; background-color:#e5e5e5">
						<table id="product-list" class="segList" cellpadding="1" cellspacing="1" width="100%">
							<thead id="header">
								<tr>
									<th width="*" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name/Description</th>
									<th align="left">&nbsp;Code</th>
									<th width="20%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;Amount Limit</th>
									<th width="1%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="4" style="font-weight:normal">No such item exists...</td>
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
	<input type="hidden" name="benefit" id="benefit" value="<?=$benefit;?>">
	<input type="hidden" name="area" id="area" value="<?=$area;?>">


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
