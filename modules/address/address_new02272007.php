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
define('LANG_FILE','place.php');
$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');

# Load the address object
require_once($root_path.'include/care_api_classes/class_address.php');

$address_region=new Address('region');
$region_list = $address_region->getAllAddress();

$address_prov=new Address('province');
$prov_list = $address_prov->getAllAddress();

$address_municity = new Address('municity');
$municity_list = $address_municity->getAllAddress();
$zipcode_list = $address_municity->getAllAddress();

$address_brgy = new Address('barangay');
$brgy_list = $address_brgy->getAllAddress();


//$db->debug=1;
switch($retpath)
{
	case 'list': $breakfile='address_menu.php'.URL_APPEND; break;
	case 'search': $breakfile='address_menu.php'.URL_APPEND; break;
	default: $breakfile='address_menu.php'.URL_APPEND; 
}

if(!isset($mode)){
	$mode='';
	$edit=true;		
}else{
	switch($mode)
	{
		case 'save':
		{
			#
			# Validate important data
			#
			$HTTP_POST_VARS['prov_name']=trim($HTTP_POST_VARS['prov_name']);
			$HTTP_POST_VARS['prov_name'] = $address_prov->stringTrim($HTTP_POST_VARS['prov_name']);   # burn added: August 25, 2006

			if(!empty($HTTP_POST_VARS['prov_name'])){
				#
				# Check if address exists
				#
				if($address_prov->addressExists(0,$HTTP_POST_VARS['prov_name'],FALSE,$HTTP_POST_VARS['region_nr'])){
					#
					# Do notification
					#
					$mode='province_exists';
				}else{
				    
					if($address_prov->saveAddressInfoFromArray($HTTP_POST_VARS)){
						#
						# Get the last insert ID
						#
						$insid=$db->Insert_ID();
						#
						# Resolve the ID to the primary key
						#
						$prov_nr=$address_prov->LastInsertPK('prov_nr',$insid);

						# Get the last insert 'prov_nr'
						# added burn: February 232, 2007
						$prov_nr=$address_prov->LastInsertPKAddress(); 
    					header("location:province_info.php?sid=$sid&lang=$lang&prov_nr=$prov_nr&mode=show&save_ok=1&retpath=$retpath");
						exit;
					}else{echo "$sql<br>$LDDbNoSave";}
				}
			}else{
					$mode='bad_data';
			}
			break;
		}
	} // end of switch($mode)
}

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',"$segProvince :: $segNewProvince");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('address_new.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$segProvince :: $segNewProvince");

# Coller Javascript code

ob_start();
	require("address.common.php");
	if ($xajax) {
		$xajax->printJavascript('../../classes/xajax');
	}

?>
<script type="text/javascript" language="javascript" src="../../js/jsprototype/prototype.js"></script> 

<script language="javascript">
<!-- 
	function check(d){
		if((d.prov_name.value=="")){
			alert("<?php echo "$segAlertNoProvinceName \\n $LDPlsEnterInfo"; ?>");
			d.prov_name.focus();
			return false;
		}
		if(d.region_nr.value=="0"){
			alert("<?php echo "$segAlertNoRegionName \\n $LDPlsEnterInfo"; ?>");
			d.region_nr.focus();
			return false;
		}
		return true;
	}/* end of function check */
		/*	
				This will trim the string i.e. no whitespaces in the
				beginning and end of a string AND only a single
				whitespace appears in between tokens/words 
				input: object
				output: object (string) value is trimmed
		*/
	function trimString(objct){
		objct.value = objct.value.replace(/^\s+|\s+$/g,"");
		objct.value = objct.value.replace(/\s+/g," "); 
	}/* end of function trimString */

		function ajxClearOptions() {
			var optionsList;
			var el=$("curriculum");
			if (el) {
				optionsList = el.getElementsByTagName('OPTION');
//				alert("ajxClearOptions: optionsList.length = '"+optionsList.length+"'");
				for (var i=optionsList.length-1;i>=0;i--) {
					optionsList[i].parentNode.removeChild(optionsList[i]);
				}
			}
		}/* end of function ajxClearOptions */
		 
		function ajxAddOption(text, value) {
			var grpEl = $("curriculum");
//			alert("ajxAddOption: grpEl = '"+grpEl+"'");
			if (grpEl) {
				var opt = new Option( text, value );
				grpEl.appendChild(opt);
			}
		}/* end of function ajxAddOption */

		function ajxAddOption2(group, text, value) {
			var grpEl = $(group+"Items");
			if (grpEl) {
				var opt = new Option( text, value );
				opt.label = group;
				opt.id = value;
				grpEl.appendChild(opt);
			}
		}/* end of function ajxAddOption */

	function jsGetCurriculum() {
		var aCampus=$('campus');
		var aCampusID = aCampus.options[aCampus.selectedIndex].value;
//		alert("jsGetCurriculum: aCampusID = '"+aCampusID+"'");
//		createCookie("campus",aCampusID,1);
		xajax_getCurriculum(aCampusID);		
	}

		function ajxClearAddress(objName) {
			var optionsList;
			var el=$(objName);
			if (el) {
				optionsList = el.getElementsByTagName('OPTION');
//				alert("ajxClearAddress: optionsList.length = '"+optionsList.length+"'");
				for (var i=optionsList.length-1;i>=0;i--) {
					optionsList[i].parentNode.removeChild(optionsList[i]);
				}
			}
		}/* end of function ajxClearAddress */

		function ajxAddAddress(objName, text, value) {
			var grpEl = $(objName);
//			alert("ajxAddAddress: grpEl = '"+grpEl+"'");
			if (grpEl) {
				var opt = new Option( text, value );
				grpEl.appendChild(opt);
			}
		}/* end of function ajxAddAddress */
		/*
				Resets the province's name, barangay's and 
				municipality/city's default name after selecting a region.
				input: NONE
		*/
	function setByRegion() {
//		alert("setByRegion: ");
		$('prov_nr').value = 0;
		$('mun_nr').value = 0;
		$('zipcode').value = 0;
		$('brgy_nr').value = 0;
	}
	function jsSetRegion() {
		var aRegion=$('region_nr');
		var aRegionID = aRegion.options[aRegion.selectedIndex].value;
		alert("jsSetRegion: aRegionID = '"+aRegionID+"'");
		if (aRegionID==0){
/*
			$('prov_nr').value=0; // resets the list of provinces
			$('mun_nr').value=0; // resets the list of municipalities/cities
			$('zipcode').value=0; // resets the list of zipcodes
			$('brgy_nr').value=0; // resets the list of barangays
*/
			xajax_setAll('province'); // resets the list of provinces
			xajax_setAll('municity'); // resets the list of municipalities/cities
			xajax_setAll('zipcode'); // resets the list of zipcodes
			xajax_setAll('barangay'); // resets the list of barangays
		} else {
			xajax_setRegion(aRegionID);
		}
	}
		/*
				Sets the region's name, province's name; and
				resets barangay's and municipality/city's default name 
				after selecting a province.
				input: region's ID, province's ID
		*/
	function setByProvince(regionID, provID) {
		alert("setByProvince: regionID = '"+regionID+"' \n provID = '"+provID+"' \n");
		$('region_nr').value = regionID;
		$('prov_nr').value = provID;
		$('mun_nr').value = 0;
		$('zipcode').value = 0;
		$('brgy_nr').value = 0;
	}
	function jsSetProvince() {
		var aRegion=$('region_nr');
		var aRegionID = aRegion.options[aRegion.selectedIndex].value;
		var aProvince=$('prov_nr');
		var aProvinceID = aProvince.options[aProvince.selectedIndex].value;
		alert("jsSetProvince: aRegionID = '"+aRegionID+"' \n aProvinceID = '"+aProvinceID+"'");
		if (aProvinceID==0){
/*
			$('mun_nr').value=0; // resets the list of municipalities/cities
			$('zipcode').value=0; // resets the list of zipcodes
			$('brgy_nr').value=0; // resets the list of barangays
*/
			xajax_setAll('municity',aRegionID); // resets the list of municipalities/cities
			xajax_setAll('zipcode',aRegionID); // resets the list of zipcodes
			xajax_setAll('barangay',aRegionID); // resets the list of barangays
		} else {
			xajax_setProvince(aProvinceID);
		}
	}
		/*
				Sets the region's name, province's name, municipality/city's name,
				zipcode; and resets barangay's default name after selecting a municipality/city.
				input: region's ID, province's ID, zipcode
		*/
	function setByMuniCity(regionID, provID, munID, zipcode) {
		alert("setByMuniCity: regionID = '"+regionID+"' \n provID = '"+provID+"' \n munID = '"+munID+"' \n zipcode = '"+zipcode+"' \n");
		$('region_nr').value = regionID;
		$('prov_nr').value = provID;
		$('mun_nr').value = munID;
		$('zipcode').value = zipcode;
		$('brgy_nr').value = 0;
	}
	function jsSetMuniCity() {
		var aRegion=$('region_nr');
		var aRegionID = aRegion.options[aRegion.selectedIndex].value;
		var aProvince=$('prov_nr');
		var aProvinceID = aProvince.options[aProvince.selectedIndex].value;
		var aMuniCity=$('mun_nr');
		var aMuniCityID = aMuniCity.options[aMuniCity.selectedIndex].value;
		alert("jsSetMuniCity: aRegionID = '"+aRegionID+"' \n aProvinceID = '"+aProvinceID+"' \n aMuniCityID = '"+aMuniCityID+"'");
		if (aMuniCityID==0){
/*
			$('zipcode').value=0; // resets the list of zipcodes
			$('brgy_nr').value=0; // resets the list of barangays
*/
			xajax_setAll('zipcode',0,aProvinceID); // resets the list of zipcodes
			xajax_setAll('barangay',0,aProvinceID); // resets the list of barangays
		} else {
			xajax_setMuniCity(aMuniCityID);
		}
	}
	
		/*
				Sets the region's name, province's name, municipality/city's name; 
				and resets barangay's default name after selecting a zipcode.
				input: region's ID, province's ID, municipality/city ID
		*/
	function setByZipcode(regionID, provID, munID) {	
		alert("setByZipcode: regionID = '"+regionID+"' \n provID = '"+provID+"' \n munID = '"+munID+"' \n");
		$('region_nr').value = regionID;
		$('prov_nr').value = provID;
		$('mun_nr').value = munID;
		$('brgy_nr').value = 0;
	}
	function jsSetZipcode() {
		var aRegion=$('region_nr');
		var aRegionID = aRegion.options[aRegion.selectedIndex].value;
		var aProvince=$('prov_nr');
		var aProvinceID = aProvince.options[aProvince.selectedIndex].value;
		var aZipcode=$('zipcode');
		var aZipcodeID = aZipcode.options[aZipcode.selectedIndex].value;
		alert("jsSetZipcode: aRegionID = '"+aRegionID+"' \n aProvinceID = '"+aProvinceID+"' \n aMuniCityID = ' \n aZipcodeID = '"+aZipcodeID+"'");
		if (aZipcodeID==0){
/*
			$('mun_nr').value=0; // resets the list of municipalities/cities
			$('brgy_nr').value=0; // resets the list of barangays
*/
			xajax_setAll('municity',0,aProvinceID); // resets the list of municipalities/cities
			xajax_setAll('barangay',0,aProvinceID); // resets the list of barangays
		} else {
			xajax_setZipcode(aZipcodeID);
		}
	}
		/*
				This will set the region's name, province's name, and
				municipality/city's name after selecting a barangay.
				input: region's ID, province's ID, municipality/city ID, zipcode
		*/
	function setByBarangay(regionID, provID, munID, zipcode) {
		alert("setByBarangay: regionID = '"+regionID+"' \n provID = '"+provID+"' \n munID = '"+munID+"' \n zipcode = '"+zipcode+"' \n");
		$('region_nr').value = regionID;
		$('prov_nr').value = provID;
		$('mun_nr').value = munID;
		$('zipcode').value = zipcode;
	}

	function jsSetBarangay() {
		var aRegion=$('region_nr');
		var aRegionID = aRegion.options[aRegion.selectedIndex].value;
		var aProvince=$('prov_nr');
		var aProvinceID = aProvince.options[aProvince.selectedIndex].value;
		var aMuniCity=$('mun_nr');
		var aMuniCityID = aMuniCity.options[aMuniCity.selectedIndex].value;
		var aBrgy=$('brgy_nr');
		var aBrgyID = aBrgy.options[aBrgy.selectedIndex].value;
		alert("jsSetBarangay: aRegionID = '"+aRegionID+"' \n aProvinceID = '"+aProvinceID+"' \n aMuniCityID = '"+aMuniCityID+"' \n aBrgyID = '"+aBrgyID+"'");
//		createCookie("campus",aCampusID,1);
		if (aBrgyID==0){
//			xajax_setAll('barangay',aRegionID,aProvinceID,aMuniCityID); // resets the list of barangays		
			xajax_setAll('barangay',0,0,aMuniCityID); // resets the list of barangays		
		}else{
			xajax_setBarangay(aBrgyID);
		}
	}

//	xajax_getCurriculum("<?= ($campusID?$campusID:"001") ?>");



// -->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>

<ul>
<?php
if(!empty($mode)){ 
?>
<table border=0>
  <tr>
    <td><img <?php echo createMascot($root_path,'mascot1_r.gif','0','bottom') ?>></td>
    <td valign="bottom"><br><font class="warnprompt"><b>
<?php 
	switch($mode)
	{
		case 'bad_data':
		{
			echo $segAlertNoProvinceName;
			break;
		}
		case 'province_exists':
		{
			echo "$segProvinceExists<br>$LDDataNoSave";
		}
	}
?>
	</b></font><p>
</td>
  </tr>
</table>
<?php 
} 
?>
&nbsp;<br>

<form action="<?php echo $thisfile; ?>" method="post" name="province" onSubmit="return check(this)">
<font face="Verdana, Arial" size=-1><?php echo $LDEnterAllFields ?>
<table border=0>
	<tr>
		<td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $segHouseNoStreet ?>: </td>
		<td class="adm_input" colspan="3">
	 		<input type="text" name="street_name" size=50 maxlength=60 onBlur="trimString(this)" value="<?php echo $street_name ?>"><br>
		</td>
	</tr> 
	<tr>
		<td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $segRegionName ?>: </td>
		<td class="adm_input" colspan="3">
			<select name="region_nr" id="region_nr" onChange="jsSetRegion()">
				<option value="0">-Select ALL-</option>
				<?php 
					while($addr=$region_list->FetchRow()){					
						$selected="";
						if ($region_nr==$addr['region_nr'])
							$selected="selected";
				?>
				<option value="<?= $addr['region_nr']?>" <?= $selected ?> ><?= $addr['region_name']?></option>				
				<?php 
					} # end of while loop
				?>
			</select>
		</td>
	</tr> 
	<tr>
		<td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $segProvinceName ?>: </td>
		<td class="adm_input" colspan="3">
			<select name="prov_nr" id="prov_nr" onChange="jsSetProvince()">
				<option value="0">-Select Province-</option>
				<?php 
					while($addr=$prov_list->FetchRow()){					
						$selected="";
						if ($prov_nr==$addr['prov_nr'])
							$selected="selected";
				?>
				<option value="<?= $addr['prov_nr']?>" <?= $selected ?> ><?= $addr['prov_name']?></option>				
				<?php 
					} # end of while loop
				?>
			</select>
		</td>
	</tr> 
	<tr>
		<td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $segMuniCityName ?>: </td>
		<td class="adm_input">
			<select name="mun_nr" id="mun_nr" onChange="jsSetMuniCity()">
				<option value="0">-Select Municipality/City-</option>
				<?php 
					while($addr=$municity_list->FetchRow()){					
						$selected="";
						if ($mun_nr==$addr['mun_nr'])
							$selected="selected";
				?>
				<option value="<?= $addr['mun_nr']?>" <?= $selected ?> ><?= $addr['mun_name']?></option>				
				<?php 
					} # end of while loop
				?>
			</select>
		</td>
		<td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $LDZipCode ?>: </td>
		<td class="adm_input">
			<select name="zipcode" id="zipcode" onChange="jsSetZipcode()">
				<option value="0">-Select Zip Code-</option>
				<?php 
					while($addr=$zipcode_list->FetchRow()){					
						$selected="";
						if ($mun_nr==$addr['zipcode'])
							$selected="selected";
				?>
				<option value="<?= $addr['zipcode']?>" <?= $selected ?> ><?= $addr['zipcode']?></option>				
				<?php 
					} # end of while loop
				?>
			</select>
		</td>
	</tr> 
	<tr>
		<td align=right class="adm_item"><font color=#ff0000><b>*</b></font> <?php echo $segBrgyName ?>: </td>
		<td class="adm_input" colspan="3">
			<select name="brgy_nr" id="brgy_nr" onChange="jsSetBarangay()">
				<option value="0">-Select Barangay-</option>
				<?php 
					while($addr=$brgy_list->FetchRow()){					
						$selected="";
						if ($brgy_nr==$addr['brgy_nr'])
							$selected="selected";
				?>
				<option value="<?= $addr['brgy_nr']?>" <?= $selected ?> ><?= $addr['brgy_name']?></option>				
				<?php 
					} # end of while loop
				?>
			</select>
		</td>
	</tr> 
	<tr>
		<td class=pblock>
			<input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>>
		</td>
		<td align=right colspan="3">
			<a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> border="0"></a>
		</td>
	</tr>
</table>
<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="mode" value="save">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="retpath" value="<?php echo $retpath ?>">
</form>

</ul>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign page output to the mainframe template

$smarty->assign('sMainFrameBlockData',$sTemp);
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
