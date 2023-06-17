<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
# Default value for the maximum nr of rows per block displayed, define this to the value you wish
# In normal cases this value is derived from the db table "care_config_global" using the "pagin_insurance_list_max_block_rows" element.
define('MAX_BLOCK_ROWS',30); 
define('NO_2LEVEL_CHK',1);

$lang_tables[]='search.php';
$lang_tables[]='actions.php';
define('LANG_FILE','lab.php');
#$local_user='ck_lab_user';
$local_user='ck_radio_user';   # burn added : September 24, 2007
require_once($root_path.'include/inc_front_chain_lang.php');

$toggle=0;

#$append=URL_APPEND."&target=$target&noresize=1&user_origin=$user_origin";
$append=URL_APPEND."&target=".$target."&noresize=1&user_origin=".$user_origin."&dept_nr=".$dept_nr;   # burn added: Oct. 3, 2006
$breakfile="radiolog.php$append";   # burn added: Oct. 2, 2006
$entry_block_bgcolor="#efefef";   # burn added: Oct. 2, 2006
$entry_border_bgcolor="#fcfcfc";   # burn added: Oct. 2, 2006
$entry_body_bgcolor="#ffffff";   # burn added: Oct. 2, 2006

$breakfile=$root_path.'modules/radiology/'.$breakfile;   # burn added: Oct. 2, 2006
# $breakfile=$root_path.'modules/nursing/'.$breakfile;   
$thisfile=basename(__FILE__);
# Data to append to url
$append='&status='.$status.'&target='.$target.'&user_origin='.$user_origin."&dept_nr=".$dept_nr;

#echo "radiology_done_request.php : target = '".$target."' <br> \n";
//echo "radiology/radiology_undone_request.php : mode = '".$mode."' <br> \n";
require($root_path.'modules/radiology/ajax/radio-done-request.common.php');

//echo $target;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('nursing');

# Title in toolbar
# $smarty->assign('sToolbarTitle', $LDTestRequest." - ".$LDSearchPatient);
 $smarty->assign('sToolbarTitle',"Radiology  :: Archive of Done Requests");


  # hide back button
 $smarty->assign('pbBack',FALSE);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('request_search.php')");

 # href for close button
/*   burn commented ; September 19, 2007
 if($HTTP_COOKIE_VARS["ck_login_logged".$sid]) $smarty->assign('breakfile',$root_path.'main/startframe.php'.URL_APPEND);
	else  $smarty->assign('breakfile',$breakfile);
*/
#$smarty->assign('breakfile',$breakfile);
if ($popUp!='1'){
		 # href for the close button
		 $smarty->assign('breakfile',$breakfile);
 }else{
		# CLOSE button for pop-ups
		$smarty->assign('breakfile','javascript:window.parent.cClick();');
		$smarty->assign('pbBack','');
 }

 # Window bar title
# $smarty->assign('sWindowTitle',$LDTestRequest." - ".$LDSearchPatient);
 $smarty->assign('sWindowTitle',"Radiology  :: Archive of Done Requests");

# Body onload javascript code
#$smarty->assign('sOnLoadJs','onLoad="document.searchform.searchkey.select()"');
$smarty->assign('sOnLoadJs','');

#added by VAN 07-28-08
require_once($root_path.'include/care_api_classes/class_encounter.php');
$encObj=new Encounter();

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj = new Ward;	

require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj=new SegRadio();

$encounter_nr = $_GET['encounter_nr'];
$is_doctor = $_GET['is_doctor'];
$personInfo = $encObj->getEncounterInfo($encounter_nr);
#echo $encObj->sql;
$rid_info = $radio_obj->getRID($personInfo['pid']);
#echo $radio_obj->sql;
$birthdate = "unknown";
if (($personInfo['date_birth'])&&($personInfo['date_birth']!='0000-00-00'))
	$birthdate = date("F d, Y",strtotime($personInfo['date_birth']));
#   $birthdate = $personInfo['date_birth'];
ob_start();

echo "<script type=\"text/javascript\" src=\"".$root_path."js/dojo/dojo.js\"></script>"."\n \r";
echo "<script type=\"text/javascript\" src=\"".$root_path."js/jsprototype/prototype1.5.js\"></script>"."\n \r";
echo "<script type=\"text/javascript\" src=\"js/radio-done-request-gui.js\"></script>";
?>
<!-- Include dojoTab Dependencies -->
<script type="text/javascript">
	dojo.require("dojo.widget.TabContainer");
	dojo.require("dojo.widget.LinkPane");
	dojo.require("dojo.widget.ContentPane");
	dojo.require("dojo.widget.LayoutContainer");
	dojo.require("dojo.event.*");
</script>
<style type="text/css">
	body{font-family : sans-serif;}
	dojoTabPaneWrapper{ padding : 10px 10px 10px;}
</style>
<!--  Dojo script function for undone request -->
<script language="javascript">
 //load eventOnClick
 dojo.addOnLoad(eventOnClick);
</script>

<?php

$xajax->printJavascript($root_path.'classes/xajax-0.2.5');

$tmp1 = ob_get_contents();
ob_end_clean();
$smarty->assign('yhScript', $tmp1);

# Collect extra javascript code

ob_start();

?>
<!-- commented by VAN 06-28-08 -->
<!--<ul>-->
		<div id="tabFpanel">
		<table border="0" cellspacing="2" cellpadding="2" width="99%" align="center">
			<tr>
				<td class="segPanelHeader" align="left" colspan="2"> Patient's Information </td>
			</tr>
			<tr>
				<td width="35%" class="segPanel"><strong>Patient Name</strong></td>
				<td class="segPanel"><?=mb_strtoupper($personInfo['name_last']).", ".mb_strtoupper($personInfo['name_first'])." ".mb_strtoupper($personInfo['name_middle'])?></td>
			</tr>
			<tr>
				<td class="segPanel"><strong>Hospital Record Number (HRN)</strong></td>
				<td class="segPanel"><?=$personInfo['pid']?></td>
			</tr>
			
			<tr>
				<td class="segPanel"><strong>RID</strong></td>
				<td class="segPanel"><?=$rid_info['rid']?></td>
			</tr>
			
			<tr>
				<td class="segPanel"><strong>Case Number</strong></td>
				<td class="segPanel"><?=$encounter_nr?></td>
			</tr>
			
			<tr>
				<td class="segPanel"><strong>Birthdate</strong></td>
				<td class="segPanel"><?=$birthdate?></td>
			</tr>
			
			<tr>
				<td class="segPanel"><strong>Age</strong></td>
				<td class="segPanel"><?=$personInfo['age']?></td>
			</tr>
			<tr>
				<td class="segPanel"><strong>Sex</strong></td>
				<?php
						if ($personInfo['sex']=='m')
							$sex = "MALE";
						else
							$sex = "FEMALE";
				?>
				<td class="segPanel"><?=$sex?></td>
			</tr>
			<tr>
				<td class="segPanel"><strong>Patient's Type</strong></td>
				<?php
					if ($personInfo['encounter_type']==1){
						$patient_type = "ERPx";
						$location = "ER";
					}elseif ($personInfo['encounter_type']==2){	
						$patient_type = "OPDPx";
						if ($personInfo['current_dept_nr'])
							$dept = $dept_obj->getDeptAllInfo($personInfo['current_dept_nr']);
				
						$location = mb_strtoupper(stripslashes($dept['name_formal']));
					}elseif (($personInfo['encounter_type']==3)||($personInfo['encounter_type']==4)||($personInfo['encounter_type']==6)){
						$patient_type = "INPx";	
						if ($personInfo['current_ward_nr'])
							$ward = $ward_obj->getWardInfo($personInfo['current_ward_nr']);
					
						$location = mb_strtoupper(stripslashes($ward['name']))." RM# ".$personInfo['current_room_nr'];		
						/*
						if ($personInfo['encounter_type']==3)
							$patient_type = "INPx (FROM ER)";
						elseif ($personInfo['encounter_type']==4)
							$patient_type = "INPx (FROM OPD)";			
						*/	
					}else{
						$patient_type = "Walkin";
						$location = "";
					}
				?>
				<td class="segPanel"><?=$patient_type?></td>
			</tr>
			<tr>
				<td class="segPanel"><strong>Patient's Location</strong></td>
				<td class="segPanel"><?=$location?></td>
			</tr>
		</table>
	</div>

	<table width=100% border=0 cellpadding="0" cellspacing="0">
		<tr bgcolor="<?php echo $entry_block_bgcolor ?>" >
			<td>
				<p><br>			
				<ul>
					<table width="474" border=0 cellpadding=10 bgcolor="<?php echo $entry_border_bgcolor ?>">
						<tr>
							<td>
<?php
#								$searchmask_bgcolor="#f3f3f3";
#								include($root_path.'include/inc_test_request_searchmask.php');
?>
								<table border=0 cellspacing=5 cellpadding=5 width="105%">			
									<tr bgcolor="#f3f3f3">
										<td>Enter the search key
										<form name="searchform" onSubmit="return false;">
											<!--<input type="text" name="searchkey" id="searchkey" size=40 maxlength=40 onChange="trimStringSearchMask();" onKeyUp="if (this.value.length >= 3){chkSearch();}" value="">-->
											<input type="text" name="searchkey" id="searchkey" size=40 maxlength=40 onChange="trimStringSearchMask();" onKeyUp="" value="">
											<br>
											<span style="font-family:Arial, Helvetica, sans-serif; font-size:11px">
												(Reference No., Batch No., RID, HRN, Name, Case no., Date of request, Birthdate)
											</span>
<!--
											<img <?php echo createLDImgSrc($root_path,'searchlamp.gif','0','absmiddle') ?> onClick="chkSearch();">
-->
											<input type="image" src="<?=$root_path?>images/his_searchbtn.gif" align="absmiddle" onClick="chkSearch();">
										</form>
										</td>
									</tr>				
								</table>
							</td>
						</tr>
					</table>
<!--
					<p>
					<a href="<?php	echo $breakfile; ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?>></a>
					<p>
-->					
					<span id='textResult'></span>
	
						<!--  Test for dojo tab event  -->	
					<div id="tbContainer" dojoType="TabContainer" style="width:auto; height:30.5em; "> 
						<div dojoType="ContentPane" widgetId="tab0" label="All" style="display:none; overflow:auto;" >
							<table id="Ttab0" cellpadding="0" cellspacing="0" border="0" class="segList" width="105%">
								<!-- List of ALL Pending Requests  -->
							</table>
						</div>
<?php 
						#Department object
						#include_once($root_path.'include/care_api_classes/class_department.php');
						#$dept_obj = new Department;
						
						$radio_sub_dept=$dept_obj->getSubDept($dept_nr);
					
						if ($dept_obj->rec_count){
							$dept_counter=2;
							while ($rowSubDept = $radio_sub_dept->FetchRow()){
								if (trim($rowSubDept['name_short'])!=''){		
									$text_name = trim($rowSubDept['name_short']);
								}elseif (trim($rowSubDept['id'])!=''){
									$text_name = trim($rowSubDept['id']);
								}else{
									$text_name = trim($rowSubDept['name_formal']);
								}
?>
						<div dojoType="ContentPane" widgetId="tab<?=$rowSubDept['nr']?>" label="<?=$text_name?>" style="display:none; overflow:auto" >
							<table id="Ttab<?=$rowSubDept['nr']?>" cellpadding="0" cellspacing="0" class="segList">
								<!-- List of Pending Requests  -->
							</table>
						</div>
<?php 
								$dept_counter++;
							} # end of while loop
						}   # end of if-stmt 'if ($dept_obj->rec_count)'
?>
					</div>
				</ul>
				<p>
			</td>
		</tr>
	</table>
<!--			
	<input type="hidden" name="skey" id="skey" value="<?= $HTTP_SESSION_VARS['sess_searchkey']? $HTTP_SESSION_VARS['sess_searchkey']:'*'?>"> 
-->
	<input type="hidden" name="skey" id="skey" value="*"> 
	<input type="hidden" name="smode" id="smode" value="<?= $mode? $mode:'search' ?>">
	<input type="hidden" name="starget" id="starget" value="<?php echo $target; ?>">
	<input type="hidden" name="thisfile" id="thisfile" value="<?php echo $thisfile; ?>">
	<input type="hidden" name="rpath" id="rpath" value="<?php echo $root_path; ?>">
	<input type="hidden" name="pgx" id="pgx" value="<?php echo $pgx; ?>">
	<input type="hidden" name="oitem" id="oitem" value="<?= $oitem? $oitem:'create_dt' ?>">
	<input type="hidden" name="odir" id="odir" value="<?= $odir? $odir:'ASC' ?>">
	<input type="hidden" name="totalcount" id="totalcount" value="<?php echo $totalcount; ?>">

	<input type="hidden" name="sid" id="sid" value="<?php echo $sid; ?>">
	<input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
	<input type="hidden" name="noresize" id="noresize" value="<?php echo $noresize; ?>">
	<input type="hidden" name="target"  id="target" value="<?php echo $target; ?>">
	<input type="hidden" name="user_origin" id="user_origin" value="<?php echo $user_origin; ?>">
	<input type="hidden" name="mode" id="mode" value="search">
	
	<input type="hidden" name="is_doctor" id="is_doctor" value="<?=($is_doctor)?1:0?>">
	<input type="hidden" name="encounter_nr" id="encounter_nr" value="<?=$encounter_nr?>">
	<input type="hidden" name="pid" id="pid" value="<?=$personInfo['pid']?>">
	<input type="hidden" name="is_perpatient" id="is_perpatient" value="1">

<!--
	<table>
		<tr align="center" style="width:auto">
			<td>
				<?php 
					$requestFileForward = $root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_test&user_origin=lab";
					echo '<a href="'.$requestFileForward.'"><img '.createLDImgSrc($root_path,'newrequest.gif','0','left').' border=0 alt="Enter New Service Request"></a>';
				?>
			</td>
		<tr>
	</table>
	-->
</ul>
<p>
<script language="javascript">
	handleOnclick();
</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

# Assign to page template object
$smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
// require($root_path.'js/floatscroll.js');

?>