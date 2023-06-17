<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/bloodBank/ajax/blood-donor-register.common.php");
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

$title=$LDLab;
$breakfile=$root_path."modules/laboratory/seg-close-window.php".URL_APPEND."&userck=$userck";
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
 #$smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');

 //$smarty->assign('sOnLoadJs','onLoad="preSet();"');   
 # Collect javascript code
 ob_start();

	$donorId = $_GET['donorID'];
?>
<script language="javascript" >

function startAJAXUpdate(editID)
{  
	 var sex, civilstat, blood;
			//save gender
			for(i=0;i<document.suchform.donor_sex.length;i++)
			{
					if(document.suchform.donor_sex[i].checked)
					{
						 //alert('sex='+document.suchform.donor_sex[i].value); 
						 sex=document.suchform.donor_sex[i].value
					}
			}
			//save civil status
			for(i=0;i<document.suchform.donor_civilstat.length;i++)
			{
					if(document.suchform.donor_civilstat[i].checked)
					{
						 //alert('sex='+document.suchform.donor_sex[i].value); 
						 civilstat=document.suchform.donor_civilstat[i].value
					}
			}
			//save blood type
			for(i=0;i<document.suchform.donor_bloodtype.length;i++)
			{
					if(document.suchform.donor_bloodtype[i].checked)
					{
						 //alert('sex='+document.suchform.donor_sex[i].value); 
						 blood=document.suchform.donor_bloodtype[i].value
					}
			}
			
			//save to ajax
			var donor_details=new Array($('donor_lname').value,$('donor_fname').value, $('donor_mname').value, $('donor_bdate').value,$('donor_age').value,$('donor_street').value,$('donor_brgy').value,$('donor_mun').value,sex,blood,civilstat,editID);
			xajax_updateBloodDonor(donor_details);  
}

function computeAge()
{
				birthdate=$('donor_bdate').value;
				xajax_computeAge(birthdate);
}

function printAge(age)
{
		document.getElementById('donor_age').value=age;
} 

function refreshFrame(outputResponse)
{
		alert(""+outputResponse);
		window.location.reload();
}

</script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>   
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/yahoo/yahoo.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/yui-2.7/fonts/fonts-min.css"/>
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/yui-2.7/autocomplete/assets/skins/sam/autocomplete.css"/>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/connection/connection-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/animation/animation-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/datasource/datasource-min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui-2.7/autocomplete/autocomplete-min.js"></script>
<script type="text/javascript" src="js/blood-register-donor.js?t=<?=time()?>"></script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
<form action="<?= $thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform">  
<div style="padding:10px;width:95%;border:0px solid black">
		<font class="warnprompt"><br></font>
		<table cellpadding="2" cellspacing="2" border="0">
				<tbody>
				 <?
				 global $db;
				 $sql = "SELECT * FROM seg_donor_info WHERE donor_id=".$db->qstr($_GET['donorID']);
				 $result = $db->Execute($sql);
				 $row = $result->FetchRow();
					?>
				<tr>
						<td class="segPanelHeader" width="10%" nowrap="nowrap" align="left">Last name</td>
						<td class="segPanel"><input type="text" size="25" id="donor_lname" value="<?echo $row['last_name'];?>"></input></td>
				</tr>
				<tr>
						<td class="segPanelHeader" width="10%" nowrap="nowrap" align="left">First name</td>
						<td class="segPanel"><input type="text" size="25" id="donor_fname" value="<?echo $row['first_name'];?>"></input></td>
				</tr> 
				<tr>
						<td class="segPanelHeader" width="10%" nowrap="nowrap" align="left">Middle name</td>
						<td class="segPanel"><input type="text" size="25" id="donor_mname" value="<?echo $row['middle_name'];?>"></input></td>
				</tr>
				<tr>
						<td class="segPanelHeader" width="10%" nowrap="nowrap" align="left">Birthdate</td>
						<td class="segPanel"><input type="text" size="15" id="donor_bdate" onblur="computeAge()" value="<?echo $row['birth_date'];?>"></input>
								<img src="<?= $root_path ?>gui/img/common/default/show-calendar.gif" id="bdate_trigger" style="cursor:pointer">[YYYY-mm-dd]
								<script type="text/javascript">
									Calendar.setup (
									{
											inputField : "donor_bdate", 
											ifFormat : "%Y-%m-%d", 
											showsTime : false, 
											button : "bdate_trigger", 
											singleClick : true, 
											step : 1
									}
									);
									</script>
									<input type="text" size="5" id="donor_age" onfocus="computeAge()" onclick="computeAge()" value="<?echo $row['age'];?>"> year(s) old
						</td>
				</tr> 
				<tr>
					 <td class="segPanelHeader" width="10%" nowrap="nowrap" align="left">Sex</td>
						<td class="segPanel">
								<?
										if($row['sex']=='F')
										{?>
										 <input type="radio" name="donor_sex" id="donor_sex" value="M">Male
										 <input type="radio" name="donor_sex" id="donor_sex" value="F" checked>Female 
										<?}
										else if($row['sex']=='M')
										{
											?>
										 <input type="radio" name="donor_sex" id="donor_sex" value="M" checked>Male
										 <input type="radio" name="donor_sex" id="donor_sex" value="F">Female 
										<?}?>               
						</td>
				</tr>  
				<tr>
					 <td class="segPanelHeader" width="10%" nowrap="nowrap" align="left">Blood Type</td>
						<td class="segPanel">
								<?
										if($row['blood_type']=='A')
										{?>
												<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="A" checked>A
												<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="B">B
												<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="AB">AB
												<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="O">O
										<?}
										else if($row['blood_type']=='B')
										{?>
												<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="A">A
												<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="B" checked>B
												<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="AB">AB
												<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="O">O 
										<?}
										else if($row['blood_type']=='AB')
										{?>
												<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="A">A
												<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="B">B
												<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="AB" checked>AB
												<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="O">O
										<?}
										else if($row['blood_type']=='O')
										{?>
												<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="A">A
												<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="B">B
												<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="AB">AB
												<input type="radio" name="donor_bloodtype" id="donor_bloodtype" value="O" checked>O
										<?}
								?>
						</td>
				</tr>
				<tr>
					 <td class="segPanelHeader" width="10%" nowrap="nowrap" align="left">Civil Status</td>
						<td class="segPanel">
								<?
										if($row['civil_status']=='Single')
										{?>            
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Single" checked>Single
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Married">Married
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Divorced">Divorced
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Widowed">Widowed
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Separated">Separated
										<?}
										else if($row['civil_status']=='Married')
										{?>            
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Single">Single
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Married" checked>Married
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Divorced">Divorced
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Widowed">Widowed
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Separated">Separated
										<?}
										else if($row['civil_status']=='Divorced')
										{?>            
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Single">Single
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Married">Married
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Divorced" checked>Divorced
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Widowed">Widowed
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Separated">Separated
										<?}
										else if($row['civil_status']=='Widowed')
										{?>            
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Single">Single
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Married">Married
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Divorced">Divorced
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Widowed" checked>Widowed
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Separated">Separated
										<?}
										else if($row['civil_status']=='Separated')
										{?>            
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Single">Single
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Married">Married
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Divorced">Divorced
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Widowed">Widowed
												<input type="radio" name="donor_civilstat" id="donor_civilstat" value="Separated" checked>Separated
										<?}
										?>
						</td>
				</tr>
				</tbody>
				</table>
				<table class="segPanel" cellpadding="2" cellspacing="2" border="0">
				<tr>
						<td width="10%" nowrap="nowrap" align="left">House No./Street</td>
						<td><input type="text" size="25" id="donor_street" value="<?echo $row['street_name'];?>"></input></td>
				</tr>
				<?                                                 
					$sql2 = "SELECT sb.brgy_name, sm.mun_name FROM seg_barangays AS sb LEFT JOIN seg_municity AS sm ".
									"ON sb.mun_nr=sm.mun_nr WHERE sb.brgy_nr=".$db->qstr($row['brgy_nr'])." AND sm.mun_nr=".$db->qstr($row['mun_nr']);
					$result2 = $db->Execute($sql2);
					$row2 = $result2->FetchRow();
				?>
				<tr>
						<td nowrap="nowrap" align="left">Barangay's Name</td>
						<td class="yui-skin-sam">
								<div id="barangay_autocomplete"> 
										<input type="text" size="25" name="donor_brgy" id="donor_brgy" value="<?echo $row2['brgy_name'];?>"/>
										<input type="hidden" id="donor_brgy_nr" name="donor_brgy_nr"/>
										<div id="barangay_container"></div>
								</div>
						</td>
				</tr>
				<tr>
						<td nowrap="nowrap" align="left">Municipality's Name</td>
						<td class="yui-skin-sam">
						<div id="municipality_autocomplete">
										<input type="text" size="25" name="donor_mun" id="donor_mun" value="<?echo $row2['mun_name'];?>"/>
										<input type="hidden" id="donor_mun_nr" name="donor_mun_nr"/>
										<div id="municipality_container"></div>
						</div>
						</td>
				</tr>
				<tr>
						<td>
								<input height="23" border="0" align="absmiddle" width="72" type="image" alt="Update data" src="../../gui/img/control/default/en/en_update_data.gif" name="update" id="update" onclick="startAJAXUpdate(<?=$_GET['donorID']?>); return false;"/>
						</td>
						<td>
								<a href ="javascript:window.parent.cClick();" <input height="23" border="0" align="absmiddle" width="72" type="image" alt="Cancel" src="../../gui/img/control/default/en/en_cancel.gif" name="cancel" id="cancel"/></a>
						</td>
				</tr>
			</table>
</div>
</form>
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
<script>
function setMuniCity(mun_nr, mun_name) {
		document.getElementById('donor_mun_nr').value   = mun_nr;
		document.getElementById('donor_mun').value = mun_name;
}

function clearNr(id) {
	if (document.getElementById(id).value == '') {
		switch (id) {
			case "donor_brgy":
				document.getElementById('donor_brgy_nr').value = '';  
			break;
								
			case "donor_mun":
				document.getElementById('donor_mun_nr').value = '';  
			break;           
		}
	}
}

YAHOO.example.BasicRemote = function() {  
		// Use an XHRDataSource -- for barangay
		var brgyDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/system_admin/ajax/seg_brgy_query.php");
		// Set the responseType
		brgyDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
		// Define the schema of the delimited results
		brgyDS.responseSchema = {
				recordDelim: "\n",
				fieldDelim: "\t"
		};
		// Enable caching
		brgyDS.maxCacheEntries = 5;        
		 
		// Instantiate the AutoComplete
		var brgyAC = new YAHOO.widget.AutoComplete("donor_brgy", "barangay_container", brgyDS); 
		brgyAC.formatResult = function(oResultData, sQuery, sResultMatch) {      
				return "<span style=\"display:none;\">"+oResultData[0]+"</span><span style=\"float:left;width:50%\">"+oResultData[1]+"</span><span>"+oResultData[2]+"</span>";
		};                
		brgyAC.generateRequest = function(sQuery) { 
				return "?query="+sQuery; 
		};     
		
		var munName = YAHOO.util.Dom.get("donor_mun");
		var brgyName = YAHOO.util.Dom.get("donor_brgy");        
		
		// Define an event handler to populate a hidden form field 
		// when an item gets selected 
		var brgyNr = YAHOO.util.Dom.get("donor_brgy_nr");    
		var brgyHandler = function(sType, aArgs) { 
				var bmyAC  = aArgs[0]; // reference back to the AC instance 
				var belLI  = aArgs[1]; // reference to the selected LI element 
				var boData = aArgs[2]; // object literal of selected item's result data 

				// update text input control ...
				brgyNr.value = boData[0];
				brgyName.value = boData[1];
				xajax_getMuniCityandProv(brgyNr.value);        
		}; 
		brgyAC.itemSelectEvent.subscribe(brgyHandler);    
						
		// Use an XHRDataSource --- for municipality or city
		var munDS = new YAHOO.util.XHRDataSource("<?= $root_path ?>modules/system_admin/ajax/seg_municity_query.php");
		// Set the responseType
		munDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
		// Define the schema of the delimited results
		munDS.responseSchema = {
				recordDelim: "\n",
				fieldDelim: "\t"
		};
		// Enable caching
		munDS.maxCacheEntries = 5;        

		// Instantiate the AutoComplete
		var munAC = new YAHOO.widget.AutoComplete("donor_mun", "municipality_container", munDS);
		munAC.formatResult = function(oResultData, sQuery, sResultMatch) {              
				return "<span style=\"display:none;\">"+oResultData[0]+"</span><span style\"float:left;\">"+oResultData[1]+"</span>";
		};                 
		
		// Define an event handler to populate a hidden form field 
		// when an item gets selected 
		var munNr = YAHOO.util.Dom.get("donor_mun_nr"); 
		var munHandler = function(sType, aArgs) { 
				var mmyAC  = aArgs[0]; // reference back to the AC instance 
				var melLI  = aArgs[1]; // reference to the selected LI element 
				var moData = aArgs[2]; // object literal of selected item's result data 

				// update text input control ...
				munNr.value = moData[0];
				munName.value = moData[1];
				//xajax_getProvince(munNr.value);
				brgyNr.value = '';
				brgyName.value = '';           
		}; 
		munAC.itemSelectEvent.subscribe(munHandler);        
		
								
		return {
				brgyDS: brgyDS,
				munDS: munDS,
				brgyAC: brgyAC,
				munAC: munAC,
		};
}(); 
</script> 
