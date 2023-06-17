<?php
#edited by VAN 04-22-08 pagination
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
//require($root_path."modules/laboratory/ajax/lab-new.common.php");
#require($root_path.'modules/nursing/ajax/nursing-station-radio-common.php');

require($root_path.'modules/or/ajax/op-icpm-tray.common.php');

require($root_path.'include/inc_environment_global.php');

//require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
//$srvObj=new SegLab();

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
$thisfile=basename(__FILE__);
$title=$LDLab; 
$title = "International Classification of Procedures in Medicine";
$breakfile=$root_path."modules/laboratory/seg-close-window.php".URL_APPEND."&userck=$userck";

# Create radiology object
require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj = new SegRadio;



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
 $smarty->assign('sOnLoadJs','onLoad="document.getElementById(\'search\').focus();"');  
 #$smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');
  #$smarty->assign('sOnLoadJs','onLoad="$(\'search\').focus();"');
  
 # Collect javascript code
 ob_start()

?>
<script language="javascript" >
<!--

// -->
</script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>modules/or/js/op-request-tray.js?t=<?=time()?>"></script>
<!-- <script type="text/javascript" src="<?=$root_path?>modules/laboratory/js/request-tray-gui.js?t=<?=time()?>"></script> 
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/radio-request-gui.js?t=<?=time()?>"></script>
-->
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
						Search Procedure Description/Code 
						<input id="search" name="search" class="segInput" type="text" style="width:50%; margin-left:10px; font: bold 12px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id,0)" onKeyPress="checkEnter(event,this.id)"/>
						<input type="image" id="search_img" name="search_img" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0);return false;" align="absmiddle" />
						<!--Search Request <input id="search" name="search" class="segInput" type="text" disabled style="width:51.5%; margin-left:10px; font: bold 12px Arial" align="absmiddle" onkeyup="startAJAXSearch(this.id)" />
						<img src="../../gui/img/common/default/redpfeil_l.gif">-->
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden; height:305px; width:100%; background-color:#e5e5e5">
						<table id="procedure-list" class="segList" cellpadding="1" cellspacing="1" width="100%">
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
									<th width="*" align="left">&nbsp;&nbsp;Name/Description</th>
									<th width="12%" align="left">&nbsp;&nbsp;Code </th>
									<th width="12%" align="center">RVU</th>
									<th width="12%" align="center">Multiplier</th>
									<th width="2%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="5" style="font-weight:normal">No such procedure description/code exists...</td>
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
