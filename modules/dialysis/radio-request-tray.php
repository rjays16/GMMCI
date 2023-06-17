<?php
#edited by VAN 03-18-08
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'modules/radiology/ajax/radio-service-tray.common.php');
require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_radio_user';
require_once($root_path.'include/inc_front_chain_lang.php');
$thisfile=basename(__FILE__);
$title=$LDLab;
$breakfile=$root_path."modules/radiology/seg-close-window.php".URL_APPEND."&userck=$userck";

# Create radiology object
require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj = new SegRadio;

require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

#added by VAN 04-29-2010
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

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

	$smarty->assign('sOnLoadJs','onLoad="preset();"');
 # Collect javascript code
 ob_start();

global $HTTP_SESSION_VARS, $allow_accessCT, $allow_accessUTZ, $allow_accessXRAY;

require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

$seg_user_nr = $HTTP_SESSION_VARS['sess_temp_personell_nr'];

 if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];

 $dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);

$personell = $pers_obj->get_Personell_info($seg_user_nr);
# echo "s = ".$dept_obj->sql;

if (stristr($personell['job_function_title'],'doctor')===FALSE)
		$is_doctor = 0;
else
		$is_doctor = 1;


	$area = $_GET['area'] ? $_GET['area'] : "";
	$area_type = $_GET['area_type'] ? $_GET['area_type'] : "";

?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="js/radio-request-tray.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript">
function preset(){
	 startAJAXSearch('search',0);
}

function jsGetServices(){
	var area_type = $('area_type').value;
	var var_area = $('area').value;
	var searchEL = $('search');
	var dept_nr = $('dept_nr').value;
	xajax_populateRadioServiceList(dept_nr,var_area,area_type,'search',searchEL.value,0);
}
</script>
<?php
$xajax->printJavascript($root_path.'classes/xajax');
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
							<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
									Radiology Department
									<?php
											#$allow_accessCT,$allow_accessUTZ,$allow_accessXRAY
											# 167 = CT
											# 164 = XRAY
											# 165 = UTZ
											$dept_nr = "";
											if ($allow_accessCT)
												 $dept_nr .= ",'167',";

											if ($allow_accessUTZ)
												 $dept_nr .= ",'165',";

											if ($allow_accessXRAY)
												 $dept_nr .= ",'164','166',";
											#echo $dept_belong['dept_nr'];
											$dept_nr = substr($dept_nr,1,strlen($dept_nr)-2);
											$dept_nr = str_replace(",,",",",$dept_nr);
											#echo "<br>del p = ".$dept_nr;
											if ($dept_nr){
												$all_meds=&$dept_obj->getAllRadiologyDeptWithAccess($dept_nr);
											}else{
												if (($dept_belong['dept_nr']=='158') || ($dept_belong['dept_nr']=='167') || ($dept_belong['dept_nr']=='164') || ($dept_belong['dept_nr']=='165') || ($dept_belong['dept_nr']=='166'))
													$all_meds=&$dept_obj->getAllRadiologyDeptWithAccess($dept_belong['dept_nr']);
												else
													$all_meds=&$dept_obj->getAllRadiologyDeptWithAccess($dept_nr);
											}
											#echo $dept_obj->sql;
											if (!$dept_nr)
												$dept_belong['dept_nr'] = "164";
												#echo "ddd = ".$dept_belong['dept_nr'];

												if(!empty($all_meds)&&$all_meds->RecordCount()){
													while($deptrow=$all_meds->FetchRow()){
														$sTemp = $sTemp.'
															<option value="'.$deptrow['nr'].'" ';
															if(isset($dept_belong['dept_nr'])&&($dept_belong['dept_nr']==$deptrow['nr'])) $sTemp = $sTemp.'selected';
															$sTemp = $sTemp.'>'.$deptrow['name_formal'].'</option>';
													}
												}

									?>
									<select name="dept_nr" id="dept_nr" onChange="jsGetServices();">
										<?php
											echo $sTemp;
										?>
									</select>
							</td>
						</tr>
						<tr>
							<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
									Search Request <input id="search" name="search" class="segInput" type="text" style="width:60%; margin-left:10px; font: bold 12px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id,0)" onKeyPress="checkEnter(event,this.id)"/>
									<input type="image" id="search_img" name="search_img" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0);return false;" align="absmiddle" />
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
					<div style="margin-left:-10px;display:block; border:1px solid #8cadc0; overflow-y:hidden; width:580px; background-color:#e5e5e5">
						<table class="segList" cellpadding="1" cellspacing="1" width="100%">
							<thead>
								<tr class="nav" id="show_nav" style="display">
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
									<th width="23%" align="right">&nbsp;&nbsp;Code (<font style="font-size:11px">Group Code</font>)</th>
									<th style="font-size:11px" width="15%" align="right">Cash&nbsp;&nbsp;&nbsp;&nbsp;</th>
									<th style="font-size:11px" width="15%" align="right">Charge&nbsp;&nbsp;&nbsp;&nbsp;</th>
									<th width="8%"></th>
								</tr>
							</thead>
						</table>
						</div>
						<div style="margin-left:-10px;display:block; border:1px solid #8cadc0; overflow-y:scroll; height:230px; width:580px; background-color:#e5e5e5">
							<table id="request-list" class="segList" cellpadding="1" cellspacing="1" width="100%">
								<tbody>
									<tr>
										<td colspan="6" style="font-weight:bold">No such radiological service exists...</td>
									</tr>
								</tbody>
							</table>
						<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</div>

	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">

	<input type="hidden" name="area" id="area" value="<?=$area?>">
	<input type="hidden" name="dr_nr" id="dr_nr" value="<?=$_GET['dr_nr']?>" />
	<input type="hidden" name="dept_nr" id="dept_nr" value="" />

	<input type="hidden" name="area_type" id="area_type" value="<?=$_GET['area_type'] ? $_GET['area_type'] : ""?>" />
	<!--<script>
		if (window.parent.$('area_type').value!="")
			$('area_type').value =  window.parent.$('area_type').value;
	</script> -->

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
