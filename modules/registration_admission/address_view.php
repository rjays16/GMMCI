<?php
# Load the address object
require_once($root_path.'include/care_api_classes/class_address.php');

if ($brgy_nr) {
	$address_brgy = new Address('barangay');
	#$brgy_list = $address_brgy->getAllAddress();
	$brgy_info=$address_brgy->getAddressInfo($brgy_nr,TRUE);
	if ($brgy_info){
		$brgy_row=$brgy_info->FetchRow();
	}
}
else {
	$address_brgy = new Address('municity');
	$brgy_info=$address_brgy->getAddressInfo($mun_nr,TRUE);
	if ($brgy_info){
		$brgy_row=$brgy_info->FetchRow();
		$brgy_row['brgy_name'] = 'Not Provided';
	}
}

ob_start();

?>

	<tr>
		<td class="reg_item"><?php echo $segHouseNoStreet ?>: </td>
		<td class="reg_input" colspan="2">
	 		<?= strtoupper($street_name) ?>
		</td>
	</tr> 
	<tr>
		<td class="reg_item"><?php echo $segBrgyName; ?>: 
		</td>
		<td class="reg_input" colspan="2">
			<?= strtoupper($brgy_row['brgy_name']) ?>
		</td>
	</tr> 
	<tr>
		<td class="reg_item"><?php echo $segMuniCityName ?>: </td>
		<td class="reg_input">
			<?= strtoupper($brgy_row['mun_name']) ?>
		</td>
		<td>
			<span class="reg_item"><?php echo $LDZipCode ?>:</span>
			<span class="reg_input"><?= $brgy_row['zipcode'] ?></span>
		</td>
	</tr> 
	<tr>
		<td class="reg_item"><?php echo $segProvinceName ?>: </td>
		<td class="reg_input" colspan="2">
			<?= strtoupper($brgy_row['prov_name']) ?>
		</td>
	</tr> 
	<tr>
		<td class="reg_item"><?php echo $segRegionName ?>: </td>
		<td class="reg_input" colspan="2">
			<?= strtoupper($brgy_row['region_name']) ?>
		</td>
	</tr> 
<?php

$segAddressNew = ob_get_contents();
ob_end_clean();

?>
