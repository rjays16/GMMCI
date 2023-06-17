<?php
require_once('./roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/class_equipment.php');
//require_once($root_path.'include/care_api_classes/inventory/class_inventory.php');

class SegEquipmentOrder extends Core {
	
	var $equipment_order_details = array();
	var $amount_due;
	
	function SegEquipmentOrder($details = false) {
	if ($details)
		$this->set_order_items($details);
	}
	
	
	function set_order_items($details) {
	$this->equipment_order_details = $details;
	}
	
	function delete_order_item($equipment_refno, $area='OR') {
	global $db;
	$this->return_to_inventory($equipment_refno, $area);
	$query = "DELETE FROM seg_equipment_order_items WHERE refno='$equipment_refno'"; 
	if ($db->Execute($query)) {
		return true;
	}
	else {
		return false;
	}
	}
	
	function return_to_inventory($equipment_refno, $area='OR') {
	global $db;
	
	$unit = new Unit();
	$unit->unit_id = 2;
	$unit->is_unit_per_pc = 1;
	$area_code = $area;
	$equipment_id = 'OT';
	$inventory = new Inventory();
	$inventory->setInventoryParams($equipment_id, $area_code);
 
	$query = "SELECT number_of_usage, serial_no FROM seg_equipment_order_items WHERE refno='$equipment_refno' AND equipment_id='$equipment_id'";
	if ($result = $db->Execute($query)) {
		if ($result->RecordCount() > 0) {
		while ($row = $result->FetchRow()) {
			
			$inventory->addInventory((double)$row['number_of_usage'], $unit, NULL, $row['serial_no']);
		}
		}  
	}  
	}
	
	function diminish_from_inventory($refno, $number_of_usage, $serial_no, $area='OR') {
	 global $db;
	 
	 $unit = new Unit();
	 $unit->unit_id = 2;
	 $unit->is_unit_per_pc = 1;
	 $area_code = $area;
	 $equipment_id = 'OT';
	 $inventory = new Inventory();
	 $inventory->setInventoryParams($equipment_id, $area_code);
	
	 $inventory->remInventory((double)$number_of_usage, $unit, NULL, $serial_no); 
	}
	
	/**function get_unit($serial) { todo
	global $db;
	
	}  **/
	
	function update_order($equipment_refno) {
	global $db;
	extract($this->get_equipment_order_details());
	$query = "UPDATE seg_equipment_orders SET amount_due = {$this->amount_due} WHERE refno = '$equipment_refno'";
	if ($db->Execute($query)) {
		
		return true;
	}
	else {
		return false;
	}
	}
	
	function get_order_items($equipment_refno, $area=NULL) {
	global $db;
	if ($area) {
		$area = "AND area='$area'";
	}
	$this->sql = "SELECT seoi.equipment_id, seoi.refno, seoi.number_of_usage, seoi.original_price, seoi.discounted_price, seoi.amount, 
				cppm.artikelname as equipment_name, cppm.description as equipment_description, 
				cppm.unit as equipment_unit, seo.discount, seo.discountid, seo.is_cash, seo.create_id,seo.modified_id, seo.order_date
				FROM seg_equipment_order_items seoi 
				INNER JOIN care_pharma_products_main cppm ON (seoi.equipment_id=cppm.bestellnum)
				INNER JOIN seg_equipment_orders seo ON (seo.refno=seoi.refno)
				WHERE seoi.refno='$equipment_refno' AND seoi.equipment_id<>'OT' $area";
				
	$result = $db->Execute($this->sql);
	if ($result) {
		if ($result->RecordCount()) {
		 return $result;
		}
		else {
		return false;
		}    
	}
	else {
		return false;
	}
	}
	
	function get_order_oxygen($equipment_refno, $area='OR') {
	global $db;
	$query = "SELECT seoi.equipment_id, seoi.refno, seoi.number_of_usage, seoi.original_price, seoi.discounted_price, seoi.amount,
				seoi.serial_no, cppm.artikelname as equipment_name, cppm.description as equipment_description, 
				cppm.unit as equipment_unit, seo.discount, seo.discountid, seo.is_cash 
				FROM seg_equipment_order_items seoi 
				INNER JOIN care_pharma_products_main cppm ON (seoi.equipment_id=cppm.bestellnum)
				INNER JOIN seg_equipment_orders seo ON (seo.refno=seoi.refno)
				WHERE seoi.refno='$equipment_refno' AND seoi.equipment_id = 'OT' AND area='$area'";
	$result = $db->Execute($query);
	if ($result) {
		if ($result->RecordCount()) {
		 return $result;
		}
		else {
		return false;
		}    
	}
	else {
		return false;
	}
	}
	
	function add_order_item_by_bulk($refno) {
	global $db;
	extract($this->get_equipment_order_details());
	$order_items = array();
	foreach ($equipments as $key => $equipment_value) {
		if ($equipment_value == 'OT') {
		$items_array = array($equipment_value, $number_of_usage[$key], $original_price[$key], $adjusted_price[$key], 
							$account_total[$key], $equipment_serial[$key], $cash_price[$key], $charge_price[$key]);
		$this->diminish_from_inventory($refno, $number_of_usage[$key], $equipment_serial[$key], $area);
		}
		else
		$items_array = array($equipment_value, $number_of_usage[$key], $original_price[$key], $adjusted_price[$key], 
							$account_total[$key], '', $cash_price[$key], $charge_price[$key]);
		$order_items[] = $items_array;
	} 
	 /** echo '<pre>';
	print_r($order_items);
	echo '</pre>'; **/
	 
	$index = 'equipment_id, refno, number_of_usage, original_price, discounted_price, amount, serial_no, cash_price, charge_price';
	$values = "?, '$refno', ?, ?, ?, ?, ?, ?, ?";
	
	$query = "INSERT INTO seg_equipment_order_items ($index) VALUES ($values)";
	
	$result = $db->Execute($query, $order_items);
	if (!$result) {
		$error[] = 'Stage 1 Error';
	}
	}
	
	function add_order() {
	global $db;
	$author = $_SESSION['sess_user_name'];
	extract($this->get_equipment_order_details());
	$refno = $this->get_new_reference_number(date('Y').'000001'); 
	$query = "INSERT INTO seg_equipment_orders(refno, area, request_refno, amount_due, order_date, pid, encounter_nr,
				patient_name, patient_address, discountid, discount, is_cash, is_sc, create_id,create_date,modified_id,modified_date) 
				VALUES('$refno', '$area', '$request_refno', {$this->amount_due}, '$order_date', '$pid', '$encounter_nr', '$patient_name',
				'$patient_address', '$discountid', $discount, $is_cash, $is_sc, '$author', NOW(), '$author', NOW()) ";
	
	if ($result = $db->Execute($query)) {
		return $refno;
	}
	else {
		return false;
	}
	}
	
	function calculate_total_orders() {
	global $db;
	extract($this->get_equipment_order_details());
	
	$amount_due = 0; 
	foreach ($account_total as $value) {
		$amount_due += $value;
	}
	$amount_due = number_format($amount_due, 2, '.', ''); 
	$this->set_amount_due($amount_due);
	if ($amount_due > 0)   
		return true;         
	 else
		 return false; 
	}
	
	function set_amount_due($amount_due) {
	$this->amount_due = $amount_due;
	}
	function get_equipment_order_details() {
	return $this->equipment_order_details;
	}
	function validate() {    
	extract($this->get_equipment_order_details());
	//Todo: Validate other fields. 
	$errors = array();
	if (empty($equipments)) {
		$errors[] = 'Empty equipments';
	}
	if (empty($original_price)) {
		$errors[] = 'Original prices not set';
	}
	if (empty($adjusted_price)) {
		$errors[] = 'Adjusted prices not set';
	}
	if (empty($account_total)) {
		$errors[] = 'Account total not set';
	}
	
	if (empty($errors)) {    
		return true;
	}
	else {
		foreach($errors as $value) {
		echo $value . '<br/>';
		}
	}
	
	}
	function get_new_reference_number($reference_number){
	global $db;

	$temp_refno = date('Y')."%";
		
	$query = "SELECT refno FROM seg_equipment_orders WHERE refno LIKE '$temp_refno' ORDER BY refno DESC";
	if($result = $db->SelectLimit($query, 1)){
		if($result->RecordCount()){
		$row = $result->FetchRow();
		return $row['refno']+1;
		} 
		else {
		return $reference_number;
		}
	} 
	else {
		return $reference_number;
	}
	}
	
	function get_equipment_refno($request_refno) {
	global $db;
	$query = "SELECT refno FROM seg_equipment_orders WHERE request_refno='$request_refno'";
	$result = $db->Execute($query);
	if ($result) {
		if ($result->RecordCount()) {
		$row = $result->FetchRow();
		return $row['refno'];
		}
		else {
		return 0;
		}
	}
	else {
		return 0;
	}
	}
	
	function get_equipment_refno_other($encounter_nr, $area) {
	global $db;
	$query = "SELECT refno FROM seg_equipment_orders WHERE encounter_nr='$encounter_nr' AND area='$area'";
	
	$result = $db->Execute($query);
	if ($result) {
		if ($result->RecordCount()) {
		$row = $result->FetchRow();
		return $row['refno'];
		}
		else {
		return 0;
		}
	}
	else {
		return 0;
	}
	}
	
}
	
?>
