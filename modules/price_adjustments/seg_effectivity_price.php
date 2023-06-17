<?php
//created by cha 2009-04-15

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* Integrated Hospital Information System beta 2.0.0 - 2004-05-16
* GNU General Public License
* Copyright 2002,2003,2004 
*
* See the file "copy_notice.txt" for the licence notice
*/     
#define('LANG_FILE','specials.php');
define('LANG_FILE','nursing.php');
define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');
$breakfile=$root_path.'main/spediens.php'.URL_APPEND;
$returnfile=$root_path.'main/spediens.php'.URL_APPEND;
$thisfile=basename(__FILE__);


//ajax
require($root_path."modules/price_adjustments/ajax/price_adjustments.common.php");
$xajax->printJavascript($root_path.'classes/xajax_0.5');
	
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');


$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$thisfile='seg_effectivity_price.php';

//initialize smarty
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Toolbar title
$smarty->assign('sToolbarTitle','Service Price:: Adjustments');

# href for the return button
$smarty->assign('pbBack',$returnfile);

# href for the  button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','Service Price:: Adjustments')");
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('title','Service Price:: Adjustments');
$smarty->assign('breakFile',$breakfile);

	ob_start();
 ?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="js/seg-effect-price.js?t=<?=time()?>"></script>  
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
 <? 
$sTemp = ob_get_contents();
ob_end_clean();
$sTemp.="
<script type='text/javascript'>
		var init=false;
		//var refno='$refno';
</script>";

$smarty->append('JavaScript',$sTemp);
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="POST" name="inputform">');
$smarty->assign('sFormEnd','</form>');

$smarty->assign('sArea',"<select name=\"inputarea\" id=\"inputarea\" onChange=\"startAJAXList(this.id,0), initializeTempArray()\">
		<option value='0' id='optionList'>-Select an area-</option>
		<option value='1'>Laboratory</option>
		<option value='2'>Radiology</option>
		<option value='3'>Pharmacy</option>
		<option value='4'>Miscellaneous</option>
		<option value='5'>Other Fees</option>
		</select>"); 
		
$smarty->assign('sEffectiveDate','<input type="text" name="effectiveDate" id="effectiveDate">');
$smarty->assign('sEffectiveDateIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . 'id="effect_date_trigger" align="absmiddle" style="cursor:pointer">[YYYY-mm-dd]');
$jsCalScript = "<script type=\"text/javascript\">
Calendar.setup (
{
		inputField : \"effectiveDate\", 
		ifFormat : \"%Y-%m-%d\", 
		showsTime : false, 
		button : \"effect_date_trigger\", 
		singleClick : true, 
		step : 1
}
);
</script>
";    

$smarty->assign('jsCalendarSetup', $jsCalScript);
if (!$_REQUEST['mode']) $_REQUEST['mode'] = 'edit_price';  
ob_start();
$sTemp='';
$sTable=''; 
 ?>

	
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" id="userck" value="<?php echo $userck?>">  
	<input type="hidden" id="mode" name="mode" value="<?= $_REQUEST['mode'] ?>">
	<input type="hidden" name="encoder" id="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
	<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
	<input type="hidden" name="key" id="key">
	<input type="hidden" name="pagekey" id="pagekey"> 

<!--<div  style="display:block; border:1px solid #8cadc0; overflow-y:hidden; width:65%; background-color:#e5e5e5">
<table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
		<thead>
				<tr class="nav">
						<th colspan="10">
								<div id="pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE), initialize(modifiedCode,modifiedCash,modifiedCharge,modLen)">
										<img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
										<span title="First">First</span>
								</div>
								<div id="pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE), initialize(modifiedCode,modifiedCash,modifiedCharge,modLen)">
										<img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
										<span title="Previous">Previous</span>
								</div>
								<div id="pageShow" style="float:left; margin-left:10px">
										<span></span>
								</div>
								<div id="pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE), initialize(modifiedCode,modifiedCash,modifiedCharge,modLen)">
										<span title="Last">Last</span>
										<img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
								</div>
								<div id="pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE), initialize(modifiedCode,modifiedCash,modifiedCharge,modLen)">
										<span title="Next">Next</span>
										<img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
								</div>
						</th>
				</tr>
		</thead>
</table>
</div>
<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:275px; width:65%; background-color:#e5e5e5">
<table id="PriceList" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
		<thead>
				<tr>
						<th rowspan="3" width="1%"></th>
						<th rowspan="3" width="15%" align="left">Name</th>
						<th rowspan="3" width="15%" align="center">Service Code</th>
						<th rowspan="3" width="10%" align="center">Price in Cash</th>
						<th rowspan="3" width="10%" align="center">Price in Charge</th>
				</tr>
		</thead>
		<tbody id="PriceList-body">
				<tr><td colspan="6" style="">No service area selected...</td></tr>
		</tbody>
</table>
<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
</div>  -->
<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform">

<div style="width:85%">
	<ul id="request-tabs" class="segTab" style="padding-left:30px; border-left:10px solid white">
		<li <?= strtolower($_REQUEST['mode'])=='edit_price' ? 'class="segActiveTab"' : '' ?> onclick="tabClick(this)" segTab="tab0" segSetMode="edit_price">
			<h2 class="segTabText">Edit Prices</h2>
		</li>
		<li <?= strtolower($_REQUEST['mode'])=='view_history' ? 'class="segActiveTab"' : '' ?> onclick="tabClick(this)" segTab="tab1" segSetMode="view_history">
			<h2 class="segTabText">View History</h2>
		</li>
		&nbsp;
	</ul>
	
		<div class="segTabPanel" style="display:block; border:1px solid #8cadc0; height:400px; width:90%; background-color:#e5e5e5">
		<div id="tab0" class="tabFrame" <?= ($_REQUEST["mode"]=="edit_price" || !$_REQUEST['mode']) ? '' : 'style="display:none"' ?>>
			<table cellpadding="2" cellspacing="2" border="0">
				<tbody>
						<tr>
								<td align="right" width="140"><b>Area</b></td>
								<td width="80%">
									<select name="inputarea" id="inputarea" onChange="startAJAXList(this.id,0);initializeTempArray();">
									<option value='0' id='optionList'>-Select an area-</option>
									<option value='1'>Laboratory</option>
									<option value='2'>Radiology</option>
									<option value='3'>Pharmacy</option>
									<option value='4'>Miscellaneous</option>
									<option value='5'>Other Fees</option>
									</select>
								</td>
						</tr>          
						<tr>
								<td align="right" width="140"><b>Effective Date</b></td>
								<td>
										<!--<input type="text" name="effectiveDate" id="effectiveDate" onchange="checkDate(this.id);"/>   -->
										<div id="effectiveDate" style="margin-right:2px;padding:0px 2px;border:1px solid #888888;background-color:white;width:120px;min-height:18px;displaylock;float:left"></div>
										<img src="../../gui/img/common/default/show-calendar.gif" id="effect_date_trigger" align="absmiddle" style="cursor:pointer" >[YYYY-mm-dd]
									<script type="text/javascript">
									Calendar.setup (
									{
											displayArea: "effectiveDate",
											inputField : "effectiveDate", 
											ifFormat : "%Y-%m-%d", 
											showsTime : false, 
											button : "effect_date_trigger", 
											singleClick : true, 
											step : 1
									});
									</script>
								</td>
						</tr>
						<tr>
						<td align="right" width="140"><input id="save" name="save" type="image" src="../../gui/img/control/default/en/en_savedisc.gif" border=0 width="72" height="23"  alt="Save data" align="absmiddle"  onclick="startAJAXSave(this.id,0,modifiedCode,modifiedCash,modifiedCharge,modLen); return false;">  &nbsp;&nbsp;</td>
						<td width="140"><a href ="<?$returnfile?>"><img src="../../gui/img/control/default/en/en_cancel.gif"border=0 width="72" height="23" alt="Cancel" align="absmiddle"></a>
						</td>
						</tr>             
				</tbody>
			</table>

<div  style="display:block; border:1px solid #8cadc0; overflow-y:hidden; width:90%; background-color:#e5e5e5">
<table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
		<thead>
				<tr class="nav">
						<th colspan="10">
								<div id="pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE), initialize(modifiedCode,modifiedCash,modifiedCharge,modLen)">
										<img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
										<span title="First">First</span>
								</div>
								<div id="pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE), initialize(modifiedCode,modifiedCash,modifiedCharge,modLen)">
										<img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
										<span title="Previous">Previous</span>
								</div>
								<div id="pageShow" style="float:left; margin-left:10px">
										<span></span>
								</div>
								<div id="pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE), initialize(modifiedCode,modifiedCash,modifiedCharge,modLen)">
										<span title="Last">Last</span>
										<img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
								</div>
								<div id="pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE), initialize(modifiedCode,modifiedCash,modifiedCharge,modLen)">
										<span title="Next">Next</span>
										<img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
								</div>
						</th>
				</tr>
		</thead>
</table>
</div>
<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; width:90%; background-color:#e5e5e5">
<table id="PriceList" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
		<thead>
				<tr>
						<th rowspan="3" width="1%"></th>
						<th rowspan="3" width="15%" align="left">Name</th>
						<th rowspan="3" width="15%" align="center">Service Code</th>
						<th rowspan="3" width="10%" align="center">Price in Cash</th>
						<th rowspan="3" width="10%" align="center">Price in Charge</th> 
				</tr>
		</thead>
		<tbody id="PriceList-body">
				<tr><td colspan="6" style="">No service area selected...</td></tr>
		</tbody>
</table>
<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
</div>
		</div>
		
		
		<div id="tab1" class="tabFrame" <?= ($_REQUEST["mode"]=="view_history") ? '' : 'style="display:none"' ?>>
			<table cellpadding="2" cellspacing="2" border="0">
			<tr>
								<td align="right" width="200"><b>Select Effectivity Date</b></td>
								<td>
										<!--<input type="text" name="selDate" id="selDate">   -->
										<div id="selDate" style="margin-right:2px;padding:0px 2px;border:1px solid #888888;background-color:white;width:120px;min-height:18px;displaylock;float:left"></div>
										<img src="../../gui/img/common/default/show-calendar.gif" id="selDate_trigger" align="absmiddle" style="cursor:pointer">[YYYY-mm-dd]
									<script type="text/javascript">
									Calendar.setup (
									{
										displayArea: "selDate",   
										inputField : "selDate", 
											ifFormat : "%Y-%m-%d", 
											showsTime : false, 
											button : "selDate_trigger", 
											singleClick : true, 
											step : 1
									});
									</script>
								</td>
								<td align="right" width="140"><input id="save" name="save" type="image" src="../../gui/img/control/default/en/en_searchbtn.gif" border=0 width="72" height="23"  alt="Save data" align="absmiddle"  onclick="callAjax(); return false;">  &nbsp;&nbsp;</td>
						</tr>
						<!--
						<tr>
						<td align="right" width="140"><input id="save" name="save" type="image" src="../../gui/img/control/default/en/en_searchbtn.gif" border=0 width="72" height="23"  alt="Save data" align="absmiddle"  onclick="callAjax(); return false;">  &nbsp;&nbsp;</td>
						</tr>
						-->
<div class="segContentPane">
<table class="jedList" width="95%" border="0" cellpadding="0" cellspacing="0">
		<thead>
				<tr class="nav">
						<th colspan="10">
								<div id="pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE), initialize(modifiedCode,modifiedCash,modifiedCharge,modLen)">
										<img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
										<span title="First">First</span>
								</div>
								<div id="pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE), initialize(modifiedCode,modifiedCash,modifiedCharge,modLen)">
										<img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
										<span title="Previous">Previous</span>
								</div>
								<div id="pageShow" style="float:left; margin-left:10px">
										<span></span>
								</div>
								<div id="pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE), initialize(modifiedCode,modifiedCash,modifiedCharge,modLen)">
										<span title="Last">Last</span>
										<img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
								</div>
								<div id="pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE), initialize(modifiedCode,modifiedCash,modifiedCharge,modLen)">
										<span title="Next">Next</span>
										<img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
								</div>
						</th>
				</tr>
		</thead>
</table>
<table id="PriceHistory" class="jedList" width="95%" border="0" cellpadding="0" cellspacing="0">
		<thead>
				<tr>
						<th width="1%"></th>
						<th width="15%" align="left">Name</th>
						<th width="15%" align="center">Service Code</th>
						<th width="10%" align="center">Price in Cash</th>
						<th width="10%" align="center">Price in Charge</th>
						<th width="10%" align="center">Date Created</th>
						<th width="2%">Options</th>
						<th colspan="2"></th>
				</tr>
		</thead>
		<tbody id="PriceHistory-body">
			<tr><td colspan="9" style="">No date selected...</td></tr>      
		</tbody>
</table>
<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
</div>  
			</table>
		</div>  
</div>
 </form>
 
 <?
 $sTemp = ob_get_contents();
 $sTable = ob_get_contents();
ob_end_clean();
$smarty->assign('sTable',$sTable);
 $smarty->assign('sHiddenInputs',$sTemp);
 $smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','left').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
 $smarty->assign('cancelButton','<td width="140"><a href ="'.$returnfile.'"><img src="../../gui/img/control/default/en/en_cancel.gif"border=0 width="72" height="23" alt="Cancel"   align="absmiddle"></a></td>');
 /**
 * show Template
 */
 # Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','price_adjustments/price_form.tpl');

$smarty->display('common/mainframe.tpl');

?>