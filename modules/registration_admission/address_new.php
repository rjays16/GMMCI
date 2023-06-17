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
#echo "<br>target = ".$target;
#added by VAN 02-11-08
if ($target=='archiv')
	$requireicon = "";
else
	$requireicon = "*"; 

ob_start();

?>
	<tr>
		<!--edited by VAN 02-11-08 -->
		<!--<td class="reg_item"><font color=#ff0000><b>*</b></font> <?php echo $segHouseNoStreet ?>: </td>-->
		<?php if ($target=='archiv'){?>
			<!--<td class="reg_item"><font color=#ff0000><b><?php echo $requireicon; ?></b> </font><?php echo $segHouseNoStreet ?> </td>-->
			<td class="reg_item"><?php echo $segHouseNoStreet ?> </td>
		<?php }else{ ?>	
			<!--<td class="reg_item"><font color=#ff0000><b><?php echo $requireicon; ?></b> <?php echo $segHouseNoStreet ?> </font></td>-->
			<td class="reg_item"><?php echo $segHouseNoStreet ?></td>
		<?php } ?>
		<td class="reg_input" colspan="2" nowrap="nowrap" valign="bottom">
			<div style="margin-top:2px;vertical-align:middle; float:left">
				<input class="jedInput" type="text" name="street_name" size=42 maxlength=60 onBlur="trimString(this)" value="<?php echo $street_name ?>">
			</div>
	 		<div style="margin-left: 2px;vertical-align:middle; float:left">
				<img class="segSimulatedLink" src="<?= $root_path ?>images/btn_addresswizard.gif" border="0" alt="Address wizard" align="absmiddle" onclick="AddressWizard()" />
			</div>
		</td>
	</tr> 
	<tr>
		<!--edited by VAN 02-11-08-->
		<!--<td class="reg_item"><font color=#ff0000><b>*</b></font><?php echo $segBrgyName; ?>: -->
		<?php if ($target=='archiv'){?>
			<td class="reg_item"><font color=#ff0000><b><?php echo $requireicon; ?></b></font><?php echo $segBrgyName; ?> </td>
			<!--<td class="reg_item"><?php echo $segBrgyName; ?> </td>-->
		<?php }else{ ?>	
			<td class="reg_item"><font color=#ff0000><b><?php echo $requireicon; ?></b><?php echo $segBrgyName; ?></font> </td>
			<!--<td class="reg_item"><?php echo $segBrgyName; ?></td>-->
		<?php } ?>
		<td class="reg_input" colspan="2" nowrap="nowrap">
			<div class="ajaxInput">
				<select class="jedInput" name="brgy_nr" id="brgy_nr" onChange="jsSetBarangay()" style="visibility:hidden"></select>
			</div>
			<!--
			<input type="hidden" name="brgy_nr" id="brgy_nr" />
			<input class="jedInput" type="text" size="25" id="show_brgy"/><input class="jedButton" type="button" value=">" />
			-->
		</td>
	</tr> 
	<tr>
		<td class="reg_item"><?php echo $segMuniCityName ?> </td>
		<td class="reg_input">
			<!-- <input type="hidden" name="mun_nr" id="mun_nr" />
			<input class="jedInput" type="text" size="25" id="show_mun"/><input class="jedButton" type="button" value=">" /> -->
			<div class="ajaxInput">
				<select class="jedInput" name="mun_nr" id="mun_nr" onChange="jsSetMuniCity()" style="visibility:hidden"></select>
			</div>
		</td>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="reg_item"><?= $LDZipCode ?></td>
					<td class="reg_input">
						<div class="ajaxInput">
							<select class="jedInput" name="zipcode" id="zipcode" onChange="jsSetZipcode()" style="visibility:hidden"></select>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr> 
	<tr>
		<td class="reg_item"><?php echo $segProvinceName ?> </td>
		<td class="reg_input" colspan="2">
			<div class="ajaxInput">
				<select class="jedInput" name="prov_nr" id="prov_nr" onChange="jsSetProvince()"  style="visibility:hidden"></select>
			</div>
		</td>
	</tr> 
	<tr>
		<td class="reg_item"><?php echo $segRegionName ?> </td>
		<td class="reg_input" colspan="2">
			<div class="ajaxInput">
				<select class="jedInput" name="region_nr" id="region_nr" onChange="jsSetRegion()"  style="visibility:hidden"></select>
			</div>
		</td>
	</tr> 
<?php

$segAddressNew = ob_get_contents();
ob_end_clean();

?>
