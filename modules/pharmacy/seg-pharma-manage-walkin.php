<?php
//created by cha 05-20-09

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','cashier.php');
$local_user="ck_prod_db_user";
require_once($root_path.'include/inc_front_chain_lang.php');

$title='Pharmacy';
if (!$_GET['from'])
$breakfile=$root_path."modules/pharmacy/seg-pharma-order-functions.php".URL_APPEND."&userck=$userck";  
else {
  if ($_GET['from']=='CLOSE_WINDOW')
    $breakfile = "javascript:window.parent.cClick();";
  else
    $breakfile = $root_path.'modules/pharmacy/apotheke-pass.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$imgpath=$root_path."pharma/img/";
$thisfile='seg-pharma-manage-walkin.php';
$breakfile=$root_path."modules/pharmacy/seg-pharma-order-functions.php".URL_APPEND."&userck=$userck";
//ajax
require_once($root_path."modules/pharmacy/ajax/pharma-walkin.common.php");
$xajax->printJavascript($root_path.'classes/xajax_0.5');

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Pharmacy::Walk-in manager");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Pharmacy::Walk-in manager");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad=""');

 # Collect javascript code
 ob_start();
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="js/pharma-walkin.js?t=<?=time()?>"></script> 

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform">');
$smarty->assign('sFormEnd','</form>');

$smarty->assign('sWalkinName',"<b>Walkin Lastname </b><input class=\"segInput\" type=\"text\" id=\"walkin_name\" size=\"25\" onkeypress=\"checkEnter(event)\" onkeyup=\"if (this.value.length >= 3) startAJAXSearch(0,''); return false;\"/>");


ob_start();
$sTemp='';
$sTable=''; 
?>
  <input type="hidden" name="sid" value="<?php echo $sid?>">
  <input type="hidden" name="lang" value="<?php echo $lang?>">
  <input type="hidden" name="cat" value="<?php echo $cat?>">
  <input type="hidden" name="userck" value="<?php echo $userck?>">  
  <input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
  <input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
  <input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
  <input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
  <input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
  <input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
  <input type="hidden" name="key" id="key">
  <input type="hidden" name="pagekey" id="pagekey">
   
<div style="width:80%; padding:0; text-align:left">
 <input class="segButton" type="button" value="New entry" onclick="newWalkin(); return false;" onmouseover="tooltip('Add new account');" onMouseout="return nd();" /> 
</div>
<div class="segContentPane">
	<table id="WalkinList" class="segList" width="80%" border="0" cellpadding="0" cellspacing="0">
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
	  <thead>
		  <tr>
		    <th width="10%" align="center">Walk-in No.</th>
		    <th width="20%" align="center">Name</th>
		    <th width="*" align="center">Address</th>
		    <th width="10%" align="center">Date Registered</th>
		    <th width="10%" align="center">Options</th>
		  </tr>
	  </thead>
	  <tbody id="WalkinList-body">
	    <tr><td colspan="7" style="">No walkin patient selected yet..</td></tr>
	  </tbody>
	</table>
</div>  


<?php

$sTable = ob_get_contents();
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sTable',$sTable);
$smarty->assign('sHiddenInputs',$sTemp);

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','pharmacy/manage-walkin.tpl'); 

/**
* show Template
*/
$smarty->display('common/mainframe.tpl');

?>