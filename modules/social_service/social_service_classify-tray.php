<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'modules/social_service/ajax/social_client_common_ajx.php'); 
require($root_path.'include/inc_environment_global.php');
$xajax->printJavascript($root_path.'classes/xajax_0.5'); 
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
 $smarty->assign('sOnLoadJs','onLoad="preSet();"');

 # Collect javascript code
 #print_r($_POST);
 ob_start(); 
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="js/social_service_client.js?t=<?=time()?>"></script>  
<!--<script type="text/javascript" src="js/pharma-walkin.js?t=<?=time()?>"></script> -->
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script language="javascript" > 
 function preSet()
 {
	 xajax_listClassifications();
	 xajax_setClassification($('encounter_nr').value,$('pid').value);
	 xajax_setModifiers($('encounter_nr').value,$('pid').value);
 }
 
 function submit_classification()
 {
	 var service_code = $('service_code').value;
	 var subservice_code = $('subservice_code').value;
	 var subservice_code2 = $('subservice_code2').value;
	 var idnumber = $('idnumber').value;
	 var data = {"service_code":service_code,"subservice_code":subservice_code,"subservice_code2":subservice_code2,"idnumber":idnumber,"encounter_nr":$('encounter_nr').value,"pid":$('pid').value,
		"encoder_name":$('encoder_name').value, "encoder_id":$('encoder_id').value, "subc":$('subc').value, "withrec":$('withrec').value,
	 "personal_circumstance":$('personal_circumstance').value, "community_situation":$('community_situation').value, "nature_of_disease":$('nature_of_disease').value};
	 
	 xajax_ProcessAddSScForm(data);
	 window.parent.location.reload(); 
 }
 
 function mouseOver(tagId, id)
 {
				//alert(objID);
				var elTarget = $(tagId);
				if(elTarget){
				
						idname = "code"+id;
						desc = $(idname).value;
						if(!desc) desc="No description";
						
						return overlib( desc, CAPTION,"Code Description",  
													 TEXTPADDING, 4, CAPTIONPADDING, 2, TEXTFONTCLASS, 'oltxt', CAPTIONFONTCLASS, 'olcap', 
													WIDTH, 220,FGCLASS,'olfgjustify',FGCOLOR, '#bbddff',FIXX, 20,FIXY, 20);    
						
				}
 }
</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
<!--<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform"> 
<div style="width:98%; padding:5px 0px">
	<table border="0" cellspacing="0" cellpadding="2" width="50%" align="center" style="border:1px solid #888888"> -->
	 <div class="bd" style="width:100%">	
		<form id="enSScode" method="POST" action="<?=$_SERVER['PHP_SELF']?>" name="suchform">
		<table width="95%" class="segPanel" style="margin-top:1%">
		<tbody>
			<tr>
				<td width="40%" align="left"><span style="color:#FF0000">*</span><b>Code:</b></td>
				<td align="left">	 
				<select name="service_code" id="service_code" onchange="xajax_OnChangeOptions(this.value, document.getElementById('encounter_nr').value, document.getElementById('pid').value)">
					<!--<option value="0">-Select Code-</option>-->
				</select>
				<input type="hidden" name="withrec" id="withrec" value="" />
				<input type="hidden" name="subc" id="subc" value="" />
				</td>
			</tr>
			<!--added by VAN 07-04-08 -->
			<tr id="subclass" style="display:none">
				<td align="left"><span style="color:#FF0000">*</span><b>Sub Classification:</b></td>
				<td align="left">
				<!--<select name="subservice_code" id="subservice_code" onchange="">-->
				<select name="subservice_code" id="subservice_code" onchange="xajax_OnChangeSubOptions(this.value);">
					<!--<option value="0">-Select Code-</option>-->
				</select>
				
				</td>
			</tr>
			<!-- -->
			<tr id="subID" style="display:none">
				<td align="left"><span style=" color:#FF0000">*</span><b>ID No.:</b></td>
				<td align="left">
					<input type="text" name="idnumber" id="idnumber" value="" size="35">
				</td>
			</tr>
			<!--added by VAN 08-05-08 -->
			<tr id="other_text" style="display:none">
				<td align="left"><span style=" color:#FF0000">*</span><b>Other Classification:</b></td>
				<td align="left">
					<input type="text" name="subservice_code2" id="subservice_code2" value="" size="35">
				</td>
			</tr> 
			<tr id="personalMod" style="display:none">	
			<td align="left" valign="top"><b>Re: Personal Circumstances</b></td>
			<td align="left"><span>	 
			<select name="personal_circumstance" id="personal_circumstance" onchange="">	
			<option value="0">-Select Personal Circumstance-</option>
			<?php
				$query1 = "select mod_subcode, mod_subdesc from seg_social_service_submodifiers where mod_code='1'";
				$result1 = $db->Execute($query1);
				while($row1=$result1->FetchRow())
				{
					if ($personal_circumstance==$row1["mod_subcode"])
						echo '<option id=" id'.$row1["mod_subcode"].'" value="'.$row1["mod_subcode"].'" selected onMouseover="mouseOver(this,\''.$row1["mod_subcode"].'\');" onMouseout="return nd();">'.$row1["mod_subcode"].'</option>';
					else
						echo '<option id=" id'.$row1["mod_subcode"].'" value="'.$row1["mod_subcode"].'" onMouseover="mouseOver(this,\''.$row1["mod_subcode"].'\');" onMouseout="return nd();">'.$row1["mod_subcode"].'</option>';    
				}
			?>			
			</select>
			<?php
				$query2 = "select mod_subcode, mod_subdesc from seg_social_service_submodifiers where mod_code='1'";
				$result2 = $db->Execute($query2);
				while($row2=$result2->FetchRow())
				{
					echo '<input type="hidden" id="code'.$row2["mod_subcode"].'" name="code'.$row2["mod_subcode"].'" value="'.$row2["mod_subdesc"].'">';
				}
			?>
			</td>
		</tr>
		<tr id="communityMod" style="display:none">	
			<td align="left" valign="top"><b>Re: Community Situations</b></td>
			<td align="left"><span>	 
			<select name="community_situation" id="community_situation" onchange="">
			<option value="0">-Select Community Situations-</option>
			<?php
				$query1 = "select mod_subcode, mod_subdesc from seg_social_service_submodifiers where mod_code='2'";
				$result1 = $db->Execute($query1);
				while($row1=$result1->FetchRow())
				{
					if ($personal_circumstance==$row1["mod_subcode"])
						echo '<option id=" id'.$row1["mod_subcode"].'" value="'.$row1["mod_subcode"].'" selected onMouseover="mouseOver(this,\''.$row1["mod_subcode"].'\');" onMouseout="return nd();">'.$row1["mod_subcode"].'</option>';
					else
						echo '<option id=" id'.$row1["mod_subcode"].'" value="'.$row1["mod_subcode"].'" onMouseover="mouseOver(this,\''.$row1["mod_subcode"].'\');" onMouseout="return nd();">'.$row1["mod_subcode"].'</option>';    
				}
			?>			
			</select>
			<?php
				$query2 = "select mod_subcode, mod_subdesc from seg_social_service_submodifiers where mod_code='2'";
				$result2 = $db->Execute($query2);
				while($row2=$result2->FetchRow())
				{
					echo '<input type="hidden" id="code'.$row2["mod_subcode"].'" name="code'.$row2["mod_subcode"].'" value="'.$row2["mod_subdesc"].'">';
				}
			?>
			</td>
		</tr>
		<tr id="diseaseMod" style="display:none">	
			<td align="left" valign="top"><b>Re: Nature of Illness/Disease</b></td>
			<td align="left"><span>	 
			<select name="nature_of_disease" id="nature_of_disease" onchange="">
			<option value="0">-Select Nature of Illness-</option>
			<?php
				$query1 = "select mod_subcode, mod_subdesc from seg_social_service_submodifiers where mod_code='3'";
				$result1 = $db->Execute($query1);
				while($row1=$result1->FetchRow())
				{
					if ($personal_circumstance==$row1["mod_subcode"])
						echo '<option id=" id'.$row1["mod_subcode"].'" value="'.$row1["mod_subcode"].'" selected onMouseover="mouseOver(this,\''.$row1["mod_subcode"].'\');" onMouseout="return nd();">'.$row1["mod_subcode"].'</option>';
					else
						echo '<option id=" id'.$row1["mod_subcode"].'" value="'.$row1["mod_subcode"].'" onMouseover="mouseOver(this,\''.$row1["mod_subcode"].'\');" onMouseout="return nd();">'.$row1["mod_subcode"].'</option>';    
				}
			?>			
			</select>
			<?php
				$query2 = "select mod_subcode, mod_subdesc from seg_social_service_submodifiers where mod_code='3'";
				$result2 = $db->Execute($query2);
				while($row2=$result2->FetchRow())
				{
					echo '<input type="hidden" id="code'.$row2["mod_subcode"].'" name="code'.$row2["mod_subcode"].'" value="'.$row2["mod_subdesc"].'">';
				}
			?>
			</td>
		</tr>
		<tr id="hidden_inputs" style="display:none">
		</tr> 
		</tbody>     
	</table>
	<table>
		<tbody>
				<tr>
					<td width="440"></td>
					<td><input type="button" id="submit" value="Submit" onclick="submit_classification(); return false;"/><input type="button" id="cancel" value="Cancel" onclick="javascript:window.parent.cClick();"/></td>
				</tr> 
		</tbody>
	</table>

</form>
			</div>
<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" id="userck" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="mode" value="search">
<input type="hidden" name="key" id="key">
<input type="hidden" name="pagekey" id="pagekey"> 
<input type="hidden" name="pid" id="pid" value="<?php echo $_GET['pid']?>"> 
<input type="hidden" name="encounter_nr" id="encounter_nr" value="<?php echo $_GET['encounter_nr']?>">
<input type="hidden" name="encoder_name" id="encoder_name" value="<?php echo $HTTP_SESSION_VARS['sess_user_name']; ?>">
<input type="hidden" name="encoder_id" id="encoder_id" value="<?php echo $HTTP_SESSION_VARS['sess_login_personell_nr']; ?>"> 

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

</div>
<?php

$sTemp = ob_get_contents();
ob_end_clean();
 $smarty->assign('sHiddenInputs',$sTemp);
# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
