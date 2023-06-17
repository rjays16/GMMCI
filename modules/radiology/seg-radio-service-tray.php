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
 
 $area = $_GET['area'];
 
 #echo "area = ".$area;

 #added by VAN 03-09-2011
 global $db, $HTTP_SESSION_VARS, $allow_accessCT, $allow_accessUTZ, $allow_accessXRAY, $accessOBGYNEUTZ, $accessMRI;
 $encounter_nr = $_GET['encounter_nr'];
 $pid = $_GET['pid'];

 if (!$impression) {
    $impression = '';

	$impression = $enc_obj->getLatestImpression($pid, $encounter_nr);

 }
#----------
?>
<script type="text/javascript">
<!--
function preset(){
	var dr_nr, dept_nr, is_dr;
	var is_dr_get = '<?=$is_dr?>';
	var pdr_nr = window.parent.$('current_att_dr_nr').value;
	var pdept_nr = window.parent.$('current_dept_nr').value;

	//added by VAN 05-26-2011
	var login_user = window.parent.$('login_user').value;
	var is_dr_post = window.parent.$('is_dr').value;

	if (is_dr_get==1)
		is_dr = is_dr_get;
	else
		is_dr = is_dr_post;
	//---------------------

	// edited by VAN 03-09-2011
	var impression_prev = '<?=str_replace("\r\n",'',$impression);?>';
	var impression;

	if (impression_prev=='')
		impression = window.parent.$('impression').value;
	else
		impression = impression_prev;

	startAJAXSearch('search',0);

	if (!is_dr){
		 dr_nr = '';
		 dept_nr = '';
	}else{
		 if (is_dr_post==1)
				dr_nr = login_user;
		 else
				dr_nr = '<?=$_GET["dr_nr"]?>';

		 dept_nr = '<?=$dept_nr?>';
	}

	if (dr_nr==""){
		if (pdr_nr=="")
			dr_nr = 0
		else
			dr_nr = pdr_nr
	}//else
		//dr_nr = 0

	if (dept_nr==""){
		if (pdept_nr=="")
			dept_nr = 0
		else
			dept_nr = pdept_nr
	}else
		dept_nr = 0

 $('clinical_info').value = impression;
	xajax_setALLDepartment(dept_nr);	//set the list of ALL departments
	xajax_setDoctors(dept_nr,dr_nr);
}

// -->
</script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="js/radio-service-tray.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

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
						<table width="95%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
							<tr>
								<td class="segPanelHeader" colspan="2">
									Request Details
								</td>
							</tr>
							<tr>
								<td valign="top" width="30%" align="right"><strong>Requesting Dept</strong></td>
								<td align="left">
									<select name="request_dept" id="request_dept" onChange="jsSetDoctorsOfDept();">
									</select>
								</td>
							</tr>
							<tr>
								<td valign="top" width="30%" align="right"><strong>Requesting Doctor</strong></td>
								<td align="left">
									<select name="request_doctor_in" id="request_doctor_in" onChange="jsSetDepartmentOfDoc();">
									</select>
									<br>
									<input type="text" name="request_doctor_out" id="request_doctor_out" size=40 onBlur="trimString(this);" value="">
									<input type="hidden" name="request_doctor" id="request_doctor" value="">
									<input type="hidden" name="request_doctor_name" id="request_doctor_name" value="">
									<input type="hidden" name="is_in_house" id="is_in_house" value="">

									<script language="javascript">
									</script>
								</td>
							</tr>
							<tr>
								<td valign="top" width="30%" align="right">
									<strong>Clinical Impression</strong>
								</td>
								<td align="left">
									<textarea name="clinical_info" id="clinical_info" cols=30 rows=2 wrap="physical" onChange="trimString(this);" onBlur="trimString(this);"><?=$impression?></textarea>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<div style="padding:4px 2px; padding-left:10px; ">
						Radiology Department &nbsp;&nbsp;&nbsp;&nbsp;
						<?php
								#$allow_accessCT,$allow_accessUTZ,$allow_accessXRAY,$allow_accessMRI
								# 167 = CT
								# 164 = XRAY
								# 165 = UTZ
								# 208 = UTZ
								$dept_nr = "";
                                
                                if ($allow_accessSysAd){
                                    $allow_accessCT = 1; 
                                    $allow_accessUTZ = 1;
                                    $allow_accessOBGYNEUTZ = 1;
                                    $allow_accessMRI = 1;
                                    $allow_accessXRAY = 1;
                                }
								
                                /*if ($allow_accessCT)
									$dept_nr .= ",'167',";

								if ($allow_accessUTZ)
									$dept_nr .= ",'165',";

								if ($allow_accessMRI)
									$dept_nr .= ",'208',";

								if ($allow_accessXRAY)
									$dept_nr .= ",'164','166',";*/
                                    
                                if ($allow_accessCT){
                                    $dept_nr .= ",'167',"; 
                                }   
                                                  
                                if ($allow_accessUTZ){
                                    $dept_nr .= ",'165',";
                                }
                                  
                                if ($allow_accessOBGYNEUTZ){
                                    $dept_nr .= ",'209',";
                                }
                                  
                                if ($allow_accessMRI){
                                    $dept_nr .= ",'208',";
                                }   
                                                     
                                if ($allow_accessXRAY){
                                    $dept_nr .= ",'164', '166',";      
                                }    
								#echo $dept_belong['dept_nr'];
								$dept_nr = substr($dept_nr,1,strlen($dept_nr)-2);
								$dept_nr = str_replace(",,",",",$dept_nr);
								#echo "<br>del p = ".$dept_nr;
                                                                
                                #edited by VAN 02-24-2011
                                #to determine if the request come from the request center
                                $costcenter = 0;
                                if (empty($area))
                                    $costcenter = 1;
                                
								/*if ($dept_nr){
									$all_meds=&$dept_obj->getAllRadiologyDeptWithAccess($dept_nr);
								}else{
									if (($dept_belong['dept_nr']=='158') || ($dept_belong['dept_nr']=='167') || ($dept_belong['dept_nr']=='164') || ($dept_belong['dept_nr']=='165') || ($dept_belong['dept_nr']=='166') || ($dept_belong['dept_nr']=='208'))
										$all_meds=&$dept_obj->getAllRadiologyDeptWithAccess($dept_belong['dept_nr']);
									else
										$all_meds=&$dept_obj->getAllRadiologyDeptWithAccess($dept_nr);
								}*/
                                
                                if ($dept_nr){
                                    $all_meds=&$dept_obj->getAllRadiologyDeptWithAccess($dept_nr,$costcenter);
                                }else{
                                    if (($dept_belong['dept_nr']=='158') || ($dept_belong['dept_nr']=='167') || ($dept_belong['dept_nr']=='164') || ($dept_belong['dept_nr']=='165') || ($dept_belong['dept_nr']=='166'))
                                        $all_meds=&$dept_obj->getAllRadiologyDeptWithAccess($dept_belong['dept_nr'], $costcenter);
                                    else
                                        $all_meds=&$dept_obj->getAllRadiologyDeptWithAccess($dept_nr, $costcenter);
                                }
                                
								#echo $dept_obj->sql;
								#if (!$dept_nr)
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
						<select name="dept_nr" id="dept_nr" onChange="document.getElementById('search').value=''; startAJAXSearch('search',0);">
							<!--<option value="0">Select a Department</option>-->
							<?php
								echo $sTemp;
							?>
						</select>
					</div>
				</td>
			</tr>
			<!-- -->

			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<div style="padding:4px 2px; padding-left:10px; ">
						Search Request <input id="search" name="search" class="segInput" type="text" style="width:60%; margin-left:10px; font: bold 12px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id,0)" onKeyPress="checkEnter(event,this.id)"/>
						<input type="image" id="search_img" name="search_img" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0);return false;" align="absmiddle" />
						<!--Search Request <input id="search" name="search" class="segInput" type="text" disabled style="width:51.5%; margin-left:10px; font: bold 12px Arial" align="absmiddle" onkeyup="startAJAXSearch(this.id)" />
						<img src="../../gui/img/common/default/redpfeil_l.gif">-->
					</div>
				</td>
			</tr>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<div id="section_label" style="padding:4px 2px; padding-left:10px; " align="center">
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden; width:100%; background-color:#e5e5e5">
						<table class="segList" cellpadding="1" cellspacing="1" width="100%">
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
									<th width="23%" align="right">&nbsp;&nbsp;Code (<font style="font-size:11px">Group Code</font>)</th>
									<th style="font-size:11px" width="15%" align="right">Cash&nbsp;&nbsp;&nbsp;&nbsp;</th>
									<th style="font-size:11px" width="15%" align="right">Charge&nbsp;&nbsp;&nbsp;&nbsp;</th>
									<!--<th width="15%">Discount Type</th>-->
									<th width="8%"></th>
								</tr>
							</thead>
						</table>
						</div>
						<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:160px; width:100%; background-color:#e5e5e5">
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


	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">

	<input type="hidden" name="area" id="area" value="<?=$area?>">
	<input type="hidden" name="dr_nr" id="dr_nr" value="<?=$_GET['dr_nr']?>" />

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
