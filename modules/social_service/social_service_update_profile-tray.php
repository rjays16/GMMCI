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
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/datefuncs.js"></script>
<script language="javascript" > 
 function preSet()
 {
	 xajax_setEducationalAttainment();
	 xajax_setProfileData($('encounter_nr').value);
 }
 
 function updateProfile()
 {
	 //alert('update profile!');
	 var details = new Object();
	 
	 details.informant_name = $('resp').value;	//informant
	 details.relation_informant = $('relation').value;	//relation to patient
	 details.educational_attain = $('occupation_select').value;	//educational attainment
	 details.nr_dependents = $('nr_dep').value;	//number of dependents
	 details.nr_children = $('nr_chldren').value;	//number of children
	 details.source_income = $('s_income').value;	//source of income
	 details.monthly_income = $('m_income2').value;	//monthly income
	 details.per_capita_income = $('m_capita_income').value;	//per capita income
	 details.house_lot_type = $('house_select').value;	//type of house and lot
	 //monthly expenses
	 details.hauz_lot_expense = $('hauz_lot2').value;	// house and lot
	 details.education_expense = $('education2').value;	//education
	 details.food_expense = $('food2').value;	//food
	 details.househelp_expense = $('househelp2').value;	//house help
	 details.light_expense = $('light2').value;	//light
	 details.fuel_expense = $('fuel2').value;	//fuel
	 details.water_expense = $('water2').value;	//water
	 details.clothing_expense = $('clothing2').value;	//clothing
	 details.transport_expense = $('transport2').value;	//transportation
	 details.insurance_mortgage = $('insurance2').value;	//insurance
	 details.med_expenditure = $('med2').value;		//medical expenditure
	 details.other_expense = $('other2').value;		//other expenses
	 details.total_monthly_expense = $('m_expenses').value;	//total monthly expenses
	 
	 details.encounter_nr = $('encounter_nr').value;
	 details.pid = $('pid').value;
	 details.encoder_name = $('encoder_name').value;
	 details.encoder_id = $('encoder_id').value;
	 
	 //alert(details.occupation_select+","+details.type_house_lot);
	 xajax_UpdateProfile(details);
	 window.parent.location.reload();
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
	<form id="frmupdate" action="Javascript:void(null);">
		<table class="segPanel" align="center" style="width:97%; margin-top:1%">
			<tr>
				<td width="25%" height="20px">Informant:</td>
				<td width="25%">
				<input type="text" id="resp" name="resp" value="" /></td>
				<td width="25%" height="18px" colspan="2">Monthly Expenses:</td>
				<td width="25%" height="18px"></td>
			</tr>
			<tr>
				<td>Relation to Patient :</td>
				 <td><input type="text" id="relation" name="relation" value="" /></td>
				<td>House and Lot:</td>
				<td width="20%">Php&nbsp;<input type="text" id="hauz_lot2" onblur="assignHauz(); computeTotal();" name="hauz_lot2" value="" class="text input_mask mask_date_us" style="text-align:right" size="15" />
												<input type="hidden" id="hauz_lot" name="hauz_lot" value=""/></td>
				<td >Education :</td>
				<td>Php&nbsp;<input type="text" id="education2" name="education2" value="" onblur="assignEducation(); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="15"/>
								<input type="hidden" id="education" name="education" value=""/></td>
			</tr>
			<tr>
				<td>Educational Attainment :</td>
				<td>
						<select id="occupation_select" name="occupation_select" >
						</select>			
				</td>
				<td>Food :</td>
				<td>Php&nbsp;<input type="text" id="food2" name="food2" value="" onblur="assignFood(); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="15"/>
								<input type="hidden" id="food" name="food" value=""/></td>
				<td>Househelp :</td>
				<td>Php&nbsp;<input type="text" id="househelp2" name="househelp2" value="" onblur="assignHousehelp(); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="15"/>
								<input type="hidden" id="househelp" name="househelp" value=""/></td>
			</tr>
			<tr>
				<td>Number of Dependents :</td>
				<td><input type="text" id="nr_dep" name="nr_dep" value="" onBlur="computeCapita();" /></td>
				<td>Light :</td>
				<td>Php&nbsp;<input type="text" id="light2" name="light2" value="" onblur="assignLight(); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="15" />
										 <input type="hidden" id="light" name="light" value=""/></td>
				<td>Fuel :</td>
				<td>Php&nbsp;<input type="text" id="fuel2" name="fuel2" value="" onblur="assignFuel(); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="15"/>
								<input type="hidden" id="fuel" name="fuel" value=""/></td>
			</tr>
			<tr>
				<td>Number of Children :</td>
				<td><input type="text" id="nr_chldren" name="nr_chldren" value=""/></td>
				<td>Water :</td>
				<td>Php&nbsp;<input type="text" id="water2" name="water2" value="" onblur="assignWater(); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="15" />
								<input type="hidden" id="water" name="water" value=""/></td>
				<td>Clothing :</td>
				<td>Php&nbsp;<input type="text" id="clothing2" name="clothing2" value="" onblur="assignClothing(); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="15"/>
								<input type="hidden" id="clothing" name="clothing" value=""/></td>
			</tr>
			<tr>
				<td>Source of Income :</td>
				<td><input type="text" id="s_income" name="s_income" value=""/></td>
				<td width="15%">Transportation :</td>
				<td>Php&nbsp;<input type="text" id="transport2" name="transport2" value="" onblur="assignTransport(); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="15" />
										 <input type="hidden" id="transport" name="transport" value=""/></td>
				<td>Insurance :</td>
				<td>Php&nbsp;<input type="text" id="insurance2" name="insurance2" value="" onblur="assignInsurance(); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="15"/>
								<input type="hidden" id="insurance" name="insurance" value=""/></td>
			</tr>
			</tr>
			<tr>
				<td>Monthly Income :</td>
				<td><input type="text" id="m_income2" name="m_income2" value="" onblur="assignM_income(); computeCapita(); " class="text input_mask mask_date_us" />
					 <input type="hidden" id="m_income" name="m_income" value=""/>
				</td>
				<td>Medical Expenditure :</td>
				<td>Php&nbsp;<input type="text" id="med2" name="med2" value="" onblur="assignMed(); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="15" />
										 <input type="hidden" id="med" name="med" value=""/></td>
				<td>Other :</td>
				<td>Php&nbsp;<input type="text" id="other2" name="other2" value="" onblur="assignOther(); computeTotal();" class="text input_mask mask_date_us" style="text-align:right" size="15"/>
								<input type="hidden" id="other" name="other" value=""/></td>
			</tr>
			<tr>
				<td>Per Capita Income :</td>
				<td><input type="text" id="m_capita_income" name="m_capita_income" value="" readonly="1" class="text input_mask mask_date_us" style="text-align:right" />
					 <input type="hidden" id="m_cincome" name="m_cincome" value=""/>
				</td>
				<td colspan="3" align="right">Total Monthly Expenditure :</td>
				<td>Php&nbsp;<input type="text" id="m_expenses" name="m_expenses" value="" readonly="1" class="text input_mask mask_date_us" style="text-align:right" size="15" /></td>
			</tr>
			<tr>
				<td> Type of House and Lot :</td>
				<td>
						<select id="house_select" name="house_select" >
							<option value="0">Not Indicated</option>
							<option value="1">Free</option>
							<option value="2">Owned</option>
							<option value="3">Rent</option>
							<option value="4">Shared</option>
							<option value="5">Monthly Amortization</option>
						</select>      
				</td>
		</table>
		<table>
		<tbody>
				<tr>
					<td width="680"></td>
					<td><input type="button" id="submit" value="Submit" onclick="updateProfile(); return refreshParent();"/><input type="button" id="cancel" value="Cancel" onclick="javascript:window.parent.cClick();"/></td>
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
