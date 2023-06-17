<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/dialysis/ajax/dialysis-service-request.common.php");
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
require_once($root_path.'include/inc_front_chain_lang.php');

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

 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Other Hospital Services");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Other Hospital Services");
 
require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

 # Assign Body Onload javascript code
/*if ($_GET['type'])
	$smarty->assign('sOnLoadJs','onLoad="xajax_populateMiscServiceList(\'search\',\'\',$(\'type\').value,0);$(\'search\').focus()"');
else */
$smarty->assign('sOnLoadJs','onLoad="init()"');

if($_GET["from"]=="dialysis_request") {
	$service_type_code = array (30);
	$service_type_name = array ("Mindanao Dialysis Center");
	$service_type_options = "";
} else {
	$service_type_options = "<option value='0'> -Select service type- </option";
	$service_type_code = array (49,50,51,52,53);
	$service_type_name = array ("Physical Medicine & Rehab", "Dental", "Orthopedics", "ENT-HNS", "Pediatrics");

}

for($i=0;$i<count($service_type_code);$i++)
{
	$service_type_options.="<option value='".$service_type_code[$i]."'>".$service_type_name[$i]."</option>";
}


 $encounter_nr = $_GET['encounter_nr'];
 $pid = $_GET['pid'];

if (!$impression) {
    $impression = '';
    $impression = $enc_obj->getLatestImpression($pid, $encounter_nr);
}
//echo "Impression ".$encounter_nr." ".$pid." ".$impression;
 # Collect javascript code
 ob_start()

?>
<script type="text/javascript" src="js/misc-request-tray.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript" >
var AJAXTimerID=0;
var lastSearch="";

function init() {
    var impression_prev = '<?=str_replace("\r\n",'',$impression);?>';
    var impression;

    if (impression_prev=='')
        impression = window.parent.$('impression').value;
    else
        impression = impression_prev;
        
    $('clinical_info').value = impression;
    
	$('search').focus();
	xajax_populateMiscServiceList('search',$('search').value,$('misc_type').value,0);
}
function startAJAXSearch(searchID, forceSearch, page) {
	var searchEL = $(searchID);
	if ((searchEL && lastSearch != searchEL.value) || forceSearch) {
			searchEL.style.color = "#0000ff";
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		$("ajax-loading").style.display = "";
		AJAXTimerID = setTimeout("xajax_populateMiscServiceList('"+searchID+"','"+searchEL.value+"','"+$('misc_type').value+"','"+page+"')",200);
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
</script>
<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
<div>
	<table width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%;font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d">
		<tbody>
			<tr>
				<td class="segPanelHeader" colspan="3">Request Details</td>
			</tr>
			<tr>
				<td class="segPanel">
					<table width="100%" style="font:bold 12px Arial; background-color:#e5e5e5;">
                    <tr>
                        <td width="30%" align="left" style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d">
                            <strong>Clinical Impression :</strong> &nbsp; 
                         </td>
                         <td>
                            <textarea name="clinical_info" id="clinical_info" cols=35 rows=2 wrap="physical" onChange="trimString(this);" onBlur="trimString(this);"><?=$impression?></textarea>
                        </td>
                    </tr>
					<tr>
						<td width="30%" align="left" style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d">
						<b>Select miscellaneous service type : </b>&nbsp;
                        </td>
                        <td>
						<select class="segInput" name="misc_type" id="misc_type" onchange="startAJAXSearch('search',1,0); return false;">
							<?echo $service_type_options;?>
						</select>
						</td>
					</tr>
						<tr>
							<td width="30%" align="left" style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d">
								<b>Search services :</b>&nbsp;
							</td>
                            <td>
                                <input id="search" class="segInput" type="text" style="width:60%; font: bold 12px Arial" align="absmiddle" onkeyup="if (event.keyCode == 13) startAJAXSearch(this.id,false,0)" />
                                <input type="image" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',true,0);return false;" align="absmiddle" />
                            </td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div style="margin-top:5px; width:565px">
	<table cellpadding="1" cellspacing="1" width="100%">
		<tbody>
			<tr>   
				<td>
					<div style="margin-left:-10px;display:block; border:1px solid #8cadc0; overflow-y:scroll; height:150px; width:580px; background-color:#e5e5e5">
						<table id="service-list" class="jedList" cellpadding="0" cellspacing="0" width="100%">
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
									<th width="10%">Code</th>
									<th width="*" align="left">Name/Description</th>
									<th width="15%" align="left">Department</th>
									<th width="15%" align="center">Price</th>
									<th width="10%" align="center">Quantity</th>
									<th width="12%"></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td colspan="6" style="font-weight:normal">No such service exists...</td>
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
	<input type="hidden" id="type" name="type" value="<?= $_GET['type'] ?>">
	<input type="hidden" id="type_name" name="type_name" value="<?= $_GET['type_name'] ?>">

<?php

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
