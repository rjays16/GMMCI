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
<!--<script type="text/javascript" src="js/pharma-walkin.js?t=<?=time()?>"></script> -->
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script language="javascript" > 
 function preSet()
 {
	 xajax_setOptions();
	 xajax_setAssessmentData($('pid').value);
 }
 
 function submit_assessment()
 {
	 var details = new Object();
	 var details2 = new Object();
		 
		details.tparent = $('tparent_select').value;
		details.sparent = $('sparent_select').value;
		details.dparent = $('dparent_select').value;
		details.cparent = $('cparent_select').value;
		
		details.tspouse = $('tspouse_select').value;
		details.sspouse = $('sspouse_select').value;
		details.dspouse = $('dspouse_select').value;
		details.cspouse = $('cspouse_select').value;
		
		details.tchild = $('tchild_select').value;
		details.schild = $('schild_select').value;
		details.dchild = $('dchild_select').value;
		details.cchild = $('cchild_select').value;
	 
		details.tsibling = $('tsibling_select').value;
		details.ssibling = $('ssibling_select').value;
		details.dsibling = $('dsibling_select').value;
		details.csibling = $('csibling_select').value;
		
		details.tother1 = $('tother1_select').value;
		details.sother1 = $('sother1_select').value;
		details.dother1 = $('dother1_select').value;
		details.cother1 = $('cother1_select').value;
		
		details.tsignificant = $('tsignificant_select').value;
		details.ssignificant = $('ssignificant_select').value;
		details.dsignificant = $('dsignificant_select').value;
		details.csignificant = $('csignificant_select').value;
		
		details.tlover = $('tlover_select').value;
		details.slover = $('slover_select').value;
		details.dlover = $('dlover_select').value;
		details.clover = $('clover_select').value;
		
		details.tfriend = $('tfriend_select').value;
		details.sfriend = $('sfriend_select').value;
		details.dfriend = $('dfriend_select').value;
		details.cfriend = $('cfriend_select').value;
		
		details.tneighbor = $('tneighbor_select').value;
		details.sneighbor = $('sneighbor_select').value;
		details.dneighbor = $('dneighbor_select').value;
		details.cneighbor = $('cneighbor_select').value;
		
		details.tmember = $('tmember_select').value;
		details.smember = $('smember_select').value;
		details.dmember = $('dmember_select').value;
		details.cmember = $('cmember_select').value;
		
		details.tother2 = $('tother2_select').value;
		details.sother2 = $('sother2_select').value;
		details.dother2 = $('dother2_select').value;
		details.cother2 = $('cother2_select').value;
		
		details.tpaid = $('tpaid_select').value;
		details.spaid = $('spaid_select').value;
		details.dpaid = $('dpaid_select').value;
		details.cpaid = $('cpaid_select').value;
		
		details.thome = $('thome_select').value;
		details.shome = $('shome_select').value;
		details.dhome = $('dhome_select').value;
		details.chome = $('chome_select').value;
		
		details.tvolunteer = $('tvolunteer_select').value;
		details.svolunteer = $('svolunteer_select').value;
		details.dvolunteer = $('dvolunteer_select').value;
		details.cvolunteer = $('cvolunteer_select').value;
		
		details.tstudent = $('tstudent_select').value;
		details.sstudent = $('sstudent_select').value;
		details.dstudent = $('dstudent_select').value;
		details.cstudent = $('cstudent_select').value;
		
		details.tother3 = $('tother3_select').value;
		details.sother3 = $('sother3_select').value;
		details.dother3 = $('dother3_select').value;
		details.cother3 = $('cother3_select').value;
		
		details.tconsumer = $('tconsumer_select').value;
		details.sconsumer = $('sconsumer_select').value;
		details.dconsumer = $('dconsumer_select').value;
		details.cconsumer = $('cconsumer_select').value;
		
		details.tinpatient = $('tinpatient_select').value;
		details.sinpatient = $('sinpatient_select').value;
		details.dinpatient = $('dinpatient_select').value;
		details.cinpatient = $('cinpatient_select').value;
		
		details.toutpatient = $('toutpatient_select').value;
		details.soutpatient = $('soutpatient_select').value;
		details.doutpatient = $('doutpatient_select').value;
		details.coutpatient = $('coutpatient_select').value;
		
		details.tprisoner = $('tprisoner_select').value;
		details.sprisoner = $('sprisoner_select').value;
		details.dprisoner = $('dprisoner_select').value;
		details.cprisoner = $('cprisoner_select').value;
		
		details.tlegal = $('tlegal_select').value;
		details.slegal = $('slegal_select').value;
		details.dlegal = $('dlegal_select').value;
		details.clegal = $('clegal_select').value;
		
		details.tillegal = $('tillegal_select').value;
		details.sillegal = $('sillegal_select').value;
		details.dillegal = $('dillegal_select').value;
		details.cillegal = $('cillegal_select').value;
		
		details.trefugee = $('trefugee_select').value;
		details.srefugee = $('srefugee_select').value;
		details.drefugee = $('drefugee_select').value;
		details.crefugee = $('crefugee_select').value;
		
		details.tother4 = $('tother4_select').value;
		details.sother4 = $('sother4_select').value;
		details.dother4 = $('dother4_select').value;
		details.cother4 = $('cother4_select').value;
		
		details.pid = $('pid').value;
		details.soc_fxn = "1";

		details2.sfood1 = $('sfood1_select').value;
		details2.dfood1 = $('dfood1_select').value;
		details2.sfood2 = $('sfood2_select').value;
		details2.dfood2 = $('dfood2_select').value;
		details2.sfood3 = $('sfood3_select').value;
		details2.dfood3 = $('dfood3_select').value;
		details2.sfood4 = $('sfood4_select').value;
		details2.dfood4 = $('dfood4_select').value;
		details2.sshelter1 = $('sshelter1_select').value;
		details2.dshelter1 = $('dshelter1_select').value;
		details2.sshelter2 = $('sshelter2_select').value;
		details2.dshelter2 = $('dshelter2_select').value;
		details2.semployment1 = $('semployment1_select').value;
		details2.demployment1 = $('demployment1_select').value;
		details2.semployment2 = $('semployment2_select').value;
		details2.demployment2 = $('demployment2_select').value; 
		details2.semployment3 = $('semployment3_select').value;
		details2.demployment3 = $('demployment3_select').value;
		details2.semployment4 = $('semployment4_select').value;
		details2.demployment4 = $('demployment4_select').value;
		details2.sresource1 = $('sresource1_select').value;
		details2.dresource1 = $('dresource1_select').value;
		details2.sresource2 = $('sresource2_select').value;
		details2.dresource2 = $('dresource2_select').value;
		details2.sresource3 = $('sresource3_select').value;
		details2.dresource3 = $('dresource3_select').value;
		details2.stransport1 = $('stransport1_select').value;
		details2.dtransport1 = $('dtransport1_select').value;
		details2.stransport2 = $('stransport2_select').value;
		details2.dtransport2 = $('dtransport2_select').value;
		details2.stransport3 = $('stransport3_select').value;
		details2.dtransport3 = $('dtransport3_select').value;
		details2.ssupport1 = $('ssupport1_select').value;
		details2.dsupport1 = $('dsupport1_select').value;
		details2.ssupport2 = $('ssupport2_select').value;
		details2.dsupport2 = $('dsupport2_select').value;
		details2.ssupport3 = $('ssupport3_select').value;
		details2.dsupport3 = $('dsupport3_select').value;
		details2.ssupport4 = $('ssupport4_select').value;
		details2.dsupport4 = $('dsupport4_select').value;
		details2.pid = $('pid').value;
		details2.soc_fxn = "2"; 
		
		xajax_ProcessAssessment(details,details2);
 }
 
</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
	<!-- <div class="bd" style="width:100%">	
		<form id="enSScode" method="POST" action="<?=$_SERVER['PHP_SELF']?>" name="suchform">
		</form>
	 </div>--> 
	 

	<div clas="bd" style=" width:100%">
	<form id="frmassess" action="Javascript:void(null);">
		<table class="segPanel" align="center" style="width:97%; margin-top:1%">
			<tr>
				<td width="35%" height="20px" style="font:bold 11px Arial;">1. Familial Roles</td>
				<td width="20%" height="20px" style="font:bold 11px Arial;"> Types of Social Interaction Problem</td>
				<td width="20%" height="20px" style="font:bold 11px Arial;"> Severity Index</td>
				<td width="18%" height="20px" style="font:bold 11px Arial;"> Duration Index</td>
				<td width="20%" height="20px" style="font:bold 11px Arial;"> Coping Index</td>
			</tr>
			<tr id="parent_function" value="parent">
				<td>Parent :</td>
				<td> <select id="tparent_select" name="tparent_select" >
							<!--<option value="0">Not Indicated</option>--> 
						</select>      
				</td>
				<td> <select id="sparent_select" name="sparent_select" >
							<!--<option value="0">Not Indicated</option>--> 
							</select>
				</td>
				<td> <select id="dparent_select" name="dparent_select" >
							<!--<option value="0">Not Indicated</option>--> 
							</select>
				</td>
				<td> <select id="cparent_select" name="cparent_select" >
							<!--<option value="0">Not Indicated</option>--> 
						 </select>
				</td> 
			</tr>
			<tr id="spouse_function" value="spouse">
				<td>Spouse :</td>
				<td> <select id="tspouse_select" name="tspouse_select" >
							<!--<option value="0">Not Indicated</option>--> 
						 </select>      
				</td>
				<td> <select id="sspouse_select" name="sspouse_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dspouse_select" name="dspouse_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="cspouse_select" name="cspouse_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>   
			</tr>
			<tr id="child_function" value="child">
				<td>Child :</td>
				<td> <select id="tchild_select" name="tchild_select" >
							<!--<option value="0">Not Indicated</option>-->>
						 </select>
				</td>
				<td> <select id="schild_select" name="schild_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dchild_select" name="dchild_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="cchild_select" name="cchild_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>  
			</tr>
			<tr id="sibling_function" value="sibling">
				<td>Sibling :</td>
				<td> <select id="tsibling_select" name="tsibling_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="ssibling_select" name="ssibling_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dsibling_select" name="dsibling_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="csibling_select" name="csibling_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr id="other_family_member_function" value="other_family_member">
				<td>Other Family Member:</td>
				<td> <select id="tother1_select" name="tother1_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="sother1_select" name="sother1_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dother1_select" name="dother1_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="cother1_select" name="cother1_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr id="significant_other_function" value="significant_other">
				<td>Significant Others :</td>
				<td> <select id="tsignificant_select" name="tsignificant_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="ssignificant_select" name="ssignificant_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dsignificant_select" name="dsignificant_select" >
							<!--<option value="0">Not Indicated</option>-->>
						 </select>
				</td>
				<td> <select id="csignificant_select" name="csignificant_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td style="font:bold 11px Arial;">2. Other Interpersonal Roles</td>
			</tr>
			<tr id="lover_function" value="lover">
				<td>Lover :</td>
				<td> <select id="tlover_select" name="tlover_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="slover_select" name="slover_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dlover_select" name="dlover_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="clover_select" name="clover_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr id="friend_function" value="friend">
				<td>Friend :</td>
				<td> <select id="tfriend_select" name="tfriend_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="sfriend_select" name="sfriend_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dfriend_select" name="dfriend_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="cfriend_select" name="cfriend_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr id="neighbor_function" value="neighbor">
				<td>Neighbor :</td>
				<td> <select id="tneighbor_select" name="tneighbor_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="sneighbor_select" name="sneighbor_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dneighbor_select" name="dneighbor_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="cneighbor_select" name="cneighbor_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>   
			</tr>
			<tr id="member_group_function" value="member_group">
				<td>Member (Group) :</td>
				<td> <select id="tmember_select" name="tmember_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="smember_select" name="smember_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dmember_select" name="dmember_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="cmember_select" name="cmember_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>     
			</tr>
			<tr id="interpersonal_other_function" value="interpersonal_other">
				<td>Other (Specify) :</td>
				<td> <select id="tother2_select" name="tother2_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="sother2_select" name="sother2_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dother2_select" name="dother2_select" >
						<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="cother2_select" name="cother2_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>   
			</tr>
			<tr>
				<td style="font:bold 11px Arial;">3. Occupational Roles</td>
			</tr>
			<tr id="worker_paid_function" value="worker_paid">
				<td>Worker-Paid economy:</td>
				<td> <select id="tpaid_select" name="tpaid_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="spaid_select" name="spaid_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dpaid_select" name="dpaid_select" >
						<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="cpaid_select" name="cpaid_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>     
			</tr>
			<tr id="worker_home_function" value="worker_home">
				<td>Worker-Home :</td>
				<td> <select id="thome_select" name="thome_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="shome_select" name="shome_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dhome_select" name="dhome_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="chome_select" name="chome_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>   
			</tr>
			<tr id="worker_volunteer_function" value="worker_volunteer">
				<td>Worker-Volunteer :</td>
				<td> <select id="tvolunteer_select" name="tvolunteer_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="svolunteer_select" name="svolunteer_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dvolunteer_select" name="dvolunteer_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="cvolunteer_select" name="cvolunteer_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>   
			</tr>
			<tr id="student_function" value="student">
				<td>Student :</td>
				<td> <select id="tstudent_select" name="tstudent_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="sstudent_select" name="sstudent_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dstudent_select" name="dstudent_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="cstudent_select" name="cstudent_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>   
			</tr>
			<tr id="occupational_role_other_function" value="occupational_role_other">
				<td>Others (Specify) :</td>
				<td> <select id="tother3_select" name="tother3_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="sother3_select" name="sother3_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dother3_select" name="dother3_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="cother3_select" name="cother3_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>   
			</tr>
			<tr>
				<td style="font:bold 11px Arial;">4. Special Life Situation Roles</td>
			</tr>
			<tr id="consumer_function" value="consumer">
				<td>Consumer :</td>
				<td> <select id="tconsumer_select" name="tconsumer_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="sconsumer_select" name="sconsumer_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dconsumer_select" name="dconsumer_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="cconsumer_select" name="cconsumer_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>   
			</tr>
			<tr id="inpatient_function" value="inpatient">
				<td>Inpatient/client :</td>
				<td> <select id="tinpatient_select" name="tinpatient_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="sinpatient_select" name="sinpatient_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dinpatient_select" name="dinpatient_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="cinpatient_select" name="cinpatient_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>   
			</tr>
			<tr id="outpatient_function" value="outpatient">
				<td>Outpatient/client :</td>
				<td> <select id="toutpatient_select" name="toutpatient_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="soutpatient_select" name="soutpatient_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="doutpatient_select" name="doutpatient_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="coutpatient_select" name="coutpatient_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>   
			</tr>
			<tr id="prisoner_function" value="prisoner">
				<td>Prisoner :</td>
				<td> <select id="tprisoner_select" name="tprisoner_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="sprisoner_select" name="sprisoner_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dprisoner_select" name="dprisoner_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="cprisoner_select" name="cprisoner_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>   
			</tr>
			<tr id="immigrant_legal_function" value="immigrant_legal">
				<td>Immigrant-legal :</td>
				<td> <select id="tlegal_select" name="tlegal_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="slegal_select" name="slegal_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dlegal_select" name="dlegal_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="clegal_select" name="clegal_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>   
			</tr>
			<tr id="immigrant_illegal_function" value="immigrant_illegal">
				<td>Immigrant-illegal :</td>
				<td> <select id="tillegal_select" name="tillegal_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="sillegal_select" name="sillegal_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dillegal_select" name="dillegal_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="cillegal_select" name="cillegal_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>   
			</tr>
			<tr id="immigrant_refugee_function" value="immigrant_refugee">
				<td>Immigrant-refugee :</td>
				<td> <select id="trefugee_select" name="trefugee_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="srefugee_select" name="srefugee_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="drefugee_select" name="drefugee_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="crefugee_select" name="crefugee_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>   
			</tr>
			<tr id="life_situation_other_function" value="life_situation_other">
				<td>Other (Specify) :</td>
				<td> <select id="tother4_select" name="tother4_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="sother4_select" name="sother4_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dother4_select" name="dother4_select" >
						<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="cother4_select" name="cother4_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>   
			</tr>
			
		</table>
		
		<table class="segPanel" align="center" style="width:97%; margin-top:1%">
			<tr>
				<td width="40%" height="20px" style="font:bold 11px Arial;">A. Economic/Basic Needs Systems Problems</td>
				<td width="30%" height="20px" style="font:bold 11px Arial;">Severity Index</td>
				<td width="30%" height="20px" style="font:bold 11px Arial;">Duration Index</td>
			</tr>
			<tr>
				<td style="font:bold 11px Arial;">1. FOOD AND NUTRITION</td>
			</tr>
			<tr>
				<td>Lack of regular food supply</td>
				<td> <select id="sfood1_select" name="sfood1_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dfood1_select" name="dfood1_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td>Nutritionally adequate food supply</td>
				<td> <select id="sfood2_select" name="sfood2_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dfood2_select" name="dfood2_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td>Documented malnutrition</td>
				<td> <select id="sfood3_select" name="sfood3_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dfood3_select" name="dfood3_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td>Other (specify)</td>
				<td> <select id="sfood4_select" name="sfood4_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dfood4_select" name="dfood4_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td style="font:bold 11px Arial;">2. SHELTER</td>  
			</tr>
			<tr>
				<td>Absence of shelter</td>
				<td> <select id="sshelter1_select" name="sshelter1_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dshelter1_select" name="dshelter1_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td>Substandard or inadequate shelter</td>
				<td> <select id="sshelter2_select" name="sshelter2_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dshelter2_select" name="dshelter2_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td style="font:bold 11px Arial;">3. EMPLOYMENT</td>
			</tr>
			<tr>
				<td>Unemployment, employment is not available in the community</td>
				<td> <select id="semployment1_select" name="semployment1_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="demployment1_select" name="demployment1_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td>Underemployment, adequate employment in the community</td>
				<td> <select id="semployment2_select" name="semployment2_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="demployment2_select" name="demployment2_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td>Inappropriate employment, lack of socially/legally acceptable employment in the community</td>
				<td> <select id="semployment3_select" name="semployment3_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="demployment3_select" name="demployment3_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td>Others (specify)</td>
				<td> <select id="semployment4_select" name="semployment4_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="demployment4_select" name="demployment4_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td style="font:bold 11px Arial;">4. ECONOMIC RESOURCES</td>
			</tr>
			<tr>
				<td>Insufficient community resources for basic sustenance</td>
				<td> <select id="sresource1_select" name="sresource1_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dresource1_select" name="dresource1_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td>Insufficient resources in the community to provide for needed services beyond sustenance</td>
				<td> <select id="sresource2_select" name="sresource2_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dresource2_select" name="dresource2_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td>Other (specify)</td>
				<td> <select id="sresource3_select" name="sresource3_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dresource3_select" name="dresource3_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td style="font:bold 11px Arial;">5. TRANSPORTATION</td>
			</tr>
			<tr>
				<td>No Personal/public transportation to job/needed services</td>
				<td> <select id="stransport1_select" name="stransport1_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dtransport1_select" name="dtransport1_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td>Other (specify)</td>
				<td> <select id="stransport2_select" name="stransport2_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dtransport2_select" name="dtransport2_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
			<td>NO PROBLEMS IN ECONOMIC/BASIC NEEDS SYSTEM</td>
				<td> <select id="stransport3_select" name="stransport3_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dtransport3_select" name="dtransport3_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td style="font:bold 11px Arial;">B. AFFECTIONAL SUPPORT SYSTEM</td>
			</tr>
			<tr>
				<td>Absence of affectional support system</td>
				<td> <select id="ssupport1_select" name="ssupport1_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dsupport1_select" name="dsupport1_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td>Support system inadequate to meet affectional needs</td>
				<td> <select id="ssupport2_select" name="ssupport2_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dsupport2_select" name="dsupport2_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td>Excessively involved support system</td>
				<td> <select id="ssupport3_select" name="ssupport3_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dsupport3_select" name="dsupport3_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
			<tr>
				<td>Others (specify)</td>
				<td> <select id="ssupport4_select" name="ssupport4_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td>
				<td> <select id="dsupport4_select" name="dsupport4_select" >
							<!--<option value="0">Not Indicated</option>-->
						 </select>
				</td> 
			</tr>
		</table>
		<table>
		<tbody>
				<tr>
					<td width="600"></td>
					<td><input type="button" id="submit" value="Submit" onclick="submit_assessment(); return false;"/><input type="button" id="cancel" value="Cancel" onclick="javascript:window.parent.cClick();"/></td>
				</tr> 
		</tbody>
	</table>
	</form>
	</div>
<!-- End of Assessment of Social Functioning-->
	 
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
