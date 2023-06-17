<?php
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

ob_start();

?>

<table border=0>
	<tr>
		<td align=right class="reg_item"><font color=#ff0000><b>*</b></font> <?php echo $segHouseNoStreet ?>: </td>
		<td class="reg_input" colspan="3">
	 		<input type="text" name="street_name" size=50 maxlength=60 onBlur="trimString(this)" value="<?php echo $street_name ?>"><br>
		</td>
	</tr> 
	<tr>
		<td align=right class="reg_item"><?php echo $segRegionName ?>: </td>
		<td class="reg_input" colspan="3">
			<select name="region_nr" id="region_nr" onChange="jsSetRegion()">
			</select>
		</td>
	</tr> 
	<tr>
		<td align=right class="reg_item"><?php echo $segProvinceName ?>: </td>
		<td class="reg_input" colspan="3">
			<select name="prov_nr" id="prov_nr" onChange="jsSetProvince()">
			</select>
		</td>
	</tr> 
	<tr>
		<td align=right class="reg_item"><?php echo $segMuniCityName ?>: </td>
		<td class="reg_input">
			<select name="mun_nr" id="mun_nr" onChange="jsSetMuniCity()">
			</select>
		</td>
		<td align=right class="reg_item"><?php echo $LDZipCode ?>: </td>
		<td class="reg_input">
			<select name="zipcode" id="zipcode" onChange="jsSetZipcode()">
			</select>
		</td>
	</tr> 
	<tr>
		<td align=right class="reg_item"><font color=#ff0000><b>*</b></font><?php echo $segBrgyName; ?>: 
		</td>
		<td class="reg_input" colspan="3">
			<select name="brgy_nr" id="brgy_nr" onChange="jsSetBarangay()">
			</select>
		</td>
	</tr> 
</table>

<?php

$segAddressNew = ob_get_contents();
ob_end_clean();

?>
