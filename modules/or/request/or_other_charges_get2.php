<?php
#weird
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root directory
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');   
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path.'include/care_api_classes/billing/class_ops.php'); //load the SegOps class
include_once($root_path."include/care_api_classes/class_order.php");  //load the SegOrder class
require_once($root_path.'modules/or/ajax/op-request-new.common.php'); //load the xajax module
require_once($root_path.'include/care_api_classes/class_department.php'); //load the department class
require_once($root_path.'include/care_api_classes/class_oproom.php'); //load the department class
require_once($root_path.'include/inc_date_format_functions.php'); //include the date formatting functions
require_once($root_path.'include/care_api_classes/class_person.php'); 
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_social_service.php'); //load the segops class
require_once($root_path.'include/care_api_classes/class_equipment_order.php'); //load the segops class         
require_once($root_path.'include/care_api_classes/class_ward.php'); //load the segops class
$seg_department = new Department();
$seg_room = new OPRoom();
$seg_ops = new SegOps(); 


     
$smarty = new Smarty_Care('or_main_request');
$smarty->assign('sToolbarTitle',"Operating Room :: Request for Surgery"); //Assign a toolbar title
$smarty->assign('sWindowTitle',"Operating Room :: Request for Surgery"); 

$breakfile = 'javascript:window.parent.cClick();';
$smarty->assign('breakfile', $breakfile);
$smarty->assign('or_main_css', '<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_main.css" type="text/css" />');    
$javascript_array = array('<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
                          , '<script>var J = jQuery.noConflict();</script>'
                          , '<link rel="stylesheet" href="'.$root_path.'modules/or/css/select_or_request.css" type="text/css" />'
                          , '<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/flexigrid/css/flexigrid/flexigrid.css">'
                          , '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>' 
                          , '<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/flexigrid.js"></script>'
                          , '<link rel="stylesheet" href="'.$root_path.'modules/or/css/select_or_request.css" type="text/css" />'     
                          , '<link rel="stylesheet" type="text/css" media="all" href="'.$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'
                          , '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/iframecontentmws.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_draggable.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_filter.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_overtwo.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_scroll.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_shadow.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_modal.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'modules/or/js/order_equipment.js"></script>' 
                          , '<script type="text/javascript" src="'.$root_path.'modules/or/js/other_charges.js"></script>'
                          , '<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/jqmodal/jqModal.css">'
                          , '<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqModal.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqDnR.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/dimensions.js"></script>'
                          , $xajax->printJavascript($root_path.'classes/xajax-0.2.5')
                          , '<script type="text/javascript" src="'.$root_path.'modules/or/js/jquery.blockUI.js"></script>'
                          , '<script>J.blockUI({ message: "<h1>Fetching charges...</h1>" });</script>' 
                          , '<script type="text/javascript" src="'.$root_path.'modules/or/js/order-gui.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/NumberFormat154.js"></script>'  );
$smarty->assign('javascript_array', $javascript_array);
$smarty->assign('transaction_type', array('1' => 'Cash', '0' => 'Charge'));

/** Form tags **/
$smarty->assign('form_start', '<form name="main_or_form" method="POST" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return validate()">');
$smarty->assign('form_end', '</form>');
/** End Form tags **/

$transaction_date_display = isset($_POST['transaction_date']) ? date('F d, Y h:ia', strtotime($_POST['transaction_date'])) : date('F d, Y h:ia');
$transaction_date = isset($_POST['transaction_date']) ? date('Y-m-d H:i', strtotime($_POST['transaction_date'])) : date('Y-m-d H:i');
$smarty->assign('transaction_date_display', '<div id="transaction_date_display" class="date_display">'.$transaction_date_display.'</div>');
$smarty->assign('transaction_date', '<input type="hidden" name="transaction_date" id="transaction_date" value="'.$transaction_date.'" />');
$smarty->assign('transaction_date_picker', '<img src="'.$root_path.'images/or_main_images/date_time_picker.png" id="transaction_date_picker" class="date_time_picker" />');
$smarty->assign('transaction_date_calendar_script', setup_calendar('transaction_date_display', 'transaction_date', 'transaction_date_picker'));

$pid = isset($_POST['pid']) ? $_POST['pid'] : $_GET['pid'];
$seg_person = new Person($pid);
$person_info = $seg_person->getAllInfoArray();
$middle_initial = (strnatcasecmp($person_info['name_middle'][0], $person_info['name_middle'][1]) == 0) ? ucwords(substr($person_info['name_middle'], 0, 2)) : strtoupper($person_info['name_middle'][0]);
$person_name = ucwords($person_info['name_last']) . ', ' . ucwords($person_info['name_first']) . ' ' . $middle_initial; 

$person_address = implode(", ",array_filter(array($person_info['street_name'], $person_info["brgy_name"], $person_info["mun_name"])));
if ($person_info["zipcode"])
  $person_address.=" ".$person_info["zipcode"];
if ($person_info["prov_name"])
  $person_address.=" ".$person_info["prov_name"];


$smarty->assign('pid', '<input type="hidden" name="pid" value="'.$pid.'" />');
$smarty->assign('patient_name', $person_name);


$encounter_types = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)'); 
$encounter_nr = isset($_POST['encounter_nr']) ? $_POST['encounter_nr'] : $_GET['encounter_nr'];
$seg_encounter = new Encounter();
$encounter_details = $seg_encounter->getEncounterInfo($encounter_nr);
$encounter_type = $encounter_types[$encounter_details['encounter_type']];

$smarty->assign('encounter_type', $encounter_type);
$ward_nr = isset($_GET['ward']) ? $_GET['ward'] : $_POST['ward']; 
$charge_area = $seg_ops->get_charge_area($encounter_nr, $encounter_details['current_dept_nr'], $ward_nr);

/**
$or_type = $seg_ops->get_encounter_or_type($encounter_nr);
$array = array('OR' => 'OR', 'PACU' => 'OR', 'DR' => 'DR', 'NICU' => 'DR');
if ($or_type) {
  $charge_area = $array[$or_type['or_type']];
}
else {
  $ward_nr = isset($_GET['ward']) ? $_GET['ward'] : $_POST['ward'];
  $ward = $seg_ops->get_ward_name($ward_nr);
  $charge_area = $ward['ward_id'];
}**/

$smarty->assign('charge_area', $charge_area);
$smarty->assign('ward', '<input type="hidden" name="ward" value="'.$ward_nr.'" />');
$social_service = new SocialService();
                                                                  
$social_service_details = $social_service->getLatestClassificationByPid($encounter_nr);
$is_sc = ($social_service_details['discountid'] == 'SC') ? '1' : '0';    
$patient_classification = isset($social_service_details['discount_id']) ? $social_service_details['discount_id'] : 'None';
$smarty->assign('patient_classification', $patient_classification);
$seg_order = new SegOrder('pharma');
$equipment = new SegEquipmentOrder();


if (isset($_POST['submitted'])) {
  $area = $_POST['charge_area_list'];
 $pharma_refno = $seg_ops->get_pharma_order_mode($encounter_nr, $area); // edit 
 $equipment_refno = $equipment->get_equipment_refno_other($encounter_nr, $area); //edit   
 //$db->StartTrans();   //see *manolo
 extract(calculate_total_pharma_order());
 $order_refno = ($pharma_refno == 0) ? $seg_order->getLastNr(date("Y-m-d")) : $pharma_refno; 
 $order_data = array(
            'refno' => $order_refno,
            'encounter_nr' => $encounter_nr,
            'pharma_area' => $area, //edit
            'pid' => $pid,
            'ordername' => $person_name,
            'orderaddress' => $person_address,
            'orderdate' => date('Y-m-d H:i'),
            'is_cash' => $_POST['transaction_type'],
            'amount_due' => $total,
            'is_tpl'=> 1, //temp
            'discount' => $_POST['discount'],
            'discountid' => $_POST['discountid'],
            'is_urgent' =>0, //temp
            'comments' =>'', //temp
            'history' => "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_temp_userid']."\n",
            'create_id' =>$_SESSION['sess_temp_userid'],
            'modify_id' =>$_SESSION['sess_temp_userid'],
            'modify_time' =>date('YmdHis'),
            'create_time'=>date('YmdHis')
            );
  /**echo '<pre>';
  print_r($order_data);
  echo '</pre>'; **/  
  $seg_order->setDataArray($order_data);
  $order_count = count($_POST['items']);
  if (($pharma_refno == 0) && $order_count > 0) {
    $currentErrorMsg = "Unable to save order information..."; //See *manolo
    $saveok = $seg_order->insertDataFromInternalArray(); 
    process_inventory('new', $order_data['refno'], $_POST['items'], $_POST['qty'], $area);
  }
  elseif ($pharma_refno != 0) {
    $seg_order->where = "refno=".$db->qstr($pharma_refno);
    $currentErrorMsg = "Unable to save order information..."; //See *manolo
    $saveok = $seg_order->updateDataFromInternalArray($pharma_refno,FALSE);
    process_inventory('edit', $pharma_refno, $_POST['items'], $_POST['qty'], $area);
  }
 
  if ($saveok) {
   
    $order_refno = ($pharma_refno == 0) ? $order_data['refno'] : $pharma_refno;
    $currentErrorMsg = "Unable to update order details..."; //see *manolo
    
    
    $saveok = $seg_order->clearOrderList($order_refno);  //see *manolo
    
    if ($saveok) $saveok = $seg_order->addOrders($order_refno, $bulk);  //see *manolo
    
    if ($saveok) {  //see maramag for sponge thingy
 
      if (count($_POST['items']) > 0) {
        $item_array = $_POST['items'];
        $status_array = array_fill(0, count($item_array), 'S');
        $remarks_array = array_fill(0, count($item_array), '');
        $saveok = $seg_order->changeServeStatus($order_refno, $item_array, $status_array, $remarks_array);
  
      }
    } 
          
    $bulk = array();
    if ($_POST['issc']) $bulk[] = 'SC';
  
    foreach ($_POST["discount"] as $i=>$v) {
      if ($v) $bulk[] = array($v);
    }
    
    $seg_order->clearDiscounts($order_refno);
    if ($bulk) $seg_order->addDiscounts($order_refno, $bulk);
    
   // if ($mode == 'new' || $_POST['pharma_refno'] == 0) {
      //if ($saveok) echo 'whatever'; //$saveok = $seg_ops->insert_care_encounter_pharma_order($order_data['refno'], $refno);
   // }
    //if ($saveok) //$db->CompleteTrans();  //see *manolo
 }
 
 if (!$saveok && count($_POST['items'])>0) {
     /** see manolo **/   
      $errorMsg = $db->ErrorMsg();
      if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
        $smarty->assign('sysErrorMessage','<strong>Error:</strong> An item with the same order number already exists in the database.');
      else
        $smarty->assign('sysErrorMessage',"<strong>$currentErrorMsg</strong><br/><strong>DB Error:</strong> $errorMsg");
      //$db->FailTrans();
      /** see manolo **/
 }    
 
 if ($_POST['discount'] == '' || !isset($_POST['discount'])) {
   $discount = 0;
 }
 
 $equipment_array = array('request_refno' => 'others',
                          'area' => $area,
                          'order_date' => date('Y-m-d H:i'),
                          'pid' => $pid,
                          'encounter_nr' => $encounter_nr,
                          'patient_name' => $person_name,
                          'patient_address' => $person_address,
                          'discountid' => $_POST['discountid'],
                          'discount' => $discount,
                          'is_cash' => $_POST['transaction_type'],
                          'is_sc' => $is_sc,
                          'number_of_usage' => $_POST['number_of_usage'],
                          'equipments' => $_POST['equipments'],
                          'equipment_serial' => $_POST['equipment_serial'],
                          'original_price' => $_POST['original_price'],
                          'adjusted_price' => $_POST['adjusted_price'],
                          'account_total' => $_POST['account_total']
                         );
/**echo '<pre>';
print_r($equipment_array);
echo '</pre>'; **/
 $seg_equipment = new SegEquipmentOrder($equipment_array);
 
  if ($seg_equipment->calculate_total_orders()) {
     if (($equipment_refno == 0) && count($_POST['equipments']) > 0) { 
       if ($equipment_refno = $seg_equipment->add_order()) {
         if ($seg_equipment->validate()) {   
           $seg_equipment->add_order_item_by_bulk($equipment_refno);
         }
       }
     }
   }
   if ($equipment_refno != 0) {   
     $equipment_refno = $equipment_refno;
     if ($seg_equipment->delete_order_item($equipment_refno, $area)) {         
       if (count($_POST['equipments']) > 0) {
         if ($seg_equipment->calculate_total_orders()) { 
           if ($seg_equipment->update_order($equipment_refno)) {
             if ($seg_equipment->validate()) {   
               $seg_equipment->add_order_item_by_bulk($equipment_refno);
             }
           }
         }
       }
     }
   }
   

 
 
   //misc
   $array = array('charge_date' => $_POST['transaction_date'],
                  'encounter_nr' => $encounter_nr,
                  'misc' => $_POST['misc'],
                  'quantity' => $_POST['quantity'],
                  'price' => $_POST['original_misc_price'],
                  'account_type' => $_POST['account_type'],
                  'area' => $area); //edit
   $seg_ops->save_misc_charges($array);
   
   //room accommodation
   $array = array('encounter_nr' => $encounter_nr,
                  'room_nr' => $_POST['removed_room_nr'],
                  'area' => $area); //edit
   $seg_ops->remove_accommodation($array);
   
   $array = array('encounter_nr'=>$encounter_nr,
                  'ward_nr' => $_POST['ward_nr'],
                  'room_nr' => $_POST['room_nr'],
                  'room_rate' => $_POST['room_rate'],
                  'room_days' => $_POST['room_days'],
                  'room_hours' => $_POST['room_hours'],
                  'area' => $area); //edit;
   $seg_ops->save_accommodation_charges($array);
  
   
}





$smarty->assign('supplies_add_button','<a href="javascript:void(0)" id="add_item" onclick="return openOrderTray2();"></a>');
$smarty->assign('supplies_empty_button','<a href="javascript:void(0)" id="empty_item" onclick="emptyTraySupplies()"></a>');
$smarty->assign('add_equipment', '<a href="javascript:void(0)" id="add_equipment" onclick="show_popup_equipment()"></a>');
$smarty->assign('empty_equipment', '<a href="javascript:void(0)" id="empty_equipment" onclick="empty_equipment()"></a>');
$smarty->assign('add_room', '<a href="javascript:void(0)" id="add_room" onclick="show_popup_accommodation()"></a>');
$smarty->assign('empty_room', '<a href="javascript:void(0)" id="empty_room" onclick="empty_accommodation()"></a>');
$smarty->assign('add_misc', '<a href="javascript:void(0)" id="add_misc" onclick="show_popup_misc()"></a>');
$smarty->assign('empty_misc', '<a href="javascript:void(0)" id="empty_misc" onclick="empty_misc()"></a>');
//$smarty->assign('pharma_area', '<input type="hidden" name="pharma_area" id="pharma_area" value="OR" />'); //edit

$smarty->assign('discountid', '<input type="hidden" name="discountid" id="discountid" value="'.$social_service_details['discountid'].'" />');     
$smarty->assign('discount', '<input type="hidden" name="discount" id="discount" value="'.$social_service_details['discount'].'" />');  

$smarty->assign('issc', '<input type="hidden" name="issc" id="issc" value="'.$is_sc.'" />');  

$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');  
$smarty->assign('encounter_nr', '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'" />');
$smarty->assign('billdate', '<input type="hidden" name="billdate" id="billdate" value="'.date("%Y-%m-%d %H:%M:%S").'" />');

$number_of_pages = array('5'=>'5', '10'=>'10', '15'=>'15', '20'=>'20', '25'=>'25', '30'=>'30');
$smarty->assign('number_of_pages', $number_of_pages);
$smarty->assign('page_number', '<input type="text" id="page_number" name="page_number" />');
$smarty->assign('search_field', '<input type="text" id="search_field" name="search_field" />');  

$smarty->assign('other_charges_submit', '<input type="submit" id="or_main_submit" value="" />');
$smarty->assign('other_charges_cancel', '<a href="'.$breakfile.'" id="or_main_cancel"></a>');

$mode = isset($_GET['mode']) ? $_GET['mode'] : $_POST['mode']; 
$smarty->assign('mode', '<input type="hidden" name="mode" value="'.$mode.'" />'); 
//$smarty->assign('equipment_refno', '<input type="hidden" name="equipment_refno" value="'.$equipment_refno.'" />');
//$smarty->assign('pharma_refno', '<input type="hidden" name="pharma_refno" " />');


$care_ward = new Ward();
$array = $care_ward->get_all_active_wards_as_pair('nr', 'name');
$smarty->assign('ward_list', $array);

$smarty->assign('room_list', array(0=>'No room is available under this ward'));
$smarty->assign('room_rate', '<input type="text" id="room_rate" name="room_rate" class="short" />');  
$smarty->assign('room_days', '<input type="text" id="room_days" name="room_days" />');
$smarty->assign('room_hours', '<input type="text" id="room_hours" name="room_hours" />');
$smarty->assign('room_type', '<input type="hidden" id="room_type" name="room_type" />');
$smarty->assign('add_accommodation', '<a href="javascript:void(0)" id="add_accommodation" onclick="validate_accommodation()"></a>');  
$smarty->assign('add_accommodation_cancel', '<a href="javascript:void(0)" id="cancel_accommodation"></a>');

$smarty->assign('close_equipment', '<a href="javascript:void(0)" id="close_equipment" class="jqmClose"></a>');   
$smarty->assign('close_misc_charge', '<a href="javascript:void(0)" id="close_misc_charge" class="jqmClose"></a>');   
$smarty->assign('close_oxygen', '<a href="javascript:void(0)" id="close_oxygen" class="jqmClose"></a>');
$smarty->assign('close_other_accommodation', '<a href="javascript:void(0)" id="close_other_accommodation" class="jqmClose"></a>');

$smarty->assign('charge_area', $charge_area);  
$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
$smarty->assign('sMainBlockIncludeFile','or/other_clinic_charges.tpl'); //Assign the or_main template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame   
 
function setup_calendar($display_area, $input_field, $button) {
  global $root_path;
  $calendar_script = 
    '<script type="text/javascript">
       Calendar.setup ({
         displayArea : "'.$display_area.'",
         inputField : "'.$input_field.'", 
         ifFormat : "%Y-%m-%d %H:%M", 
         daFormat : "%B %e, %Y %I:%M%P", 
         showsTime : true, 
         button : "'.$button.'", 
         singleClick : true, 
         step : 1
       });
      </script>';
  return $calendar_script;
}

function calculate_total_pharma_order() {
  $bulk = array();
  $orig = $_POST['iscash'] ? $_POST['pcash'] :  $_POST['pcharge'];
  $total = 0;
  foreach ($_POST["items"] as $i=>$v) {
    $consigned = in_array($v, $_POST['consigned']) ? '1' : '0';
    $bulk[] = array(
    $_POST["items"][$i],
    $_POST["dosage"][$i], //see *manolo
    $_POST["qty"][$i],
    parseFloatEx($_POST["prc"][$i]),
    parseFloatEx($_POST["prc"][$i]), 
    $consigned, $orig[$i]);
    $total += (parseFloatEx($_POST["prc"][$i]) * (float) $_POST["qty"][$i]);
  }
  return array('total' => $total, 'bulk' => $bulk);
}

function process_inventory($mode, $pharma_refno, $current_items, $current_quantities, $area) {
  global $root_path;
  $seg_order_inventory = new SegOrder('pharma'); 
  require_once($root_path.'include/care_api_classes/inventory/class_inventory.php');
  $unit = new Unit();
  $unit->unit_id = null;
  $unit->is_unit_per_pc = 1;
  if ($mode=='new') {
    foreach ($current_items as $key => $value) {
      $inventory = new Inventory();
      $inventory->area_code = $area;
      $inventory->item_code = $value;
      $matrix = $seg_order_inventory->prepareInventoryMatrix($inventory->item_code, $inventory->area_code);
      $inventory->remInventory($current_quantities[$key], $unit, $matrix[0]);
      if ($matrix) {
        $q1 = (int)$current_quantities[$key];
        foreach ($matrix as $mat) {
          $q2 = (int)$mat[1];
          if (($q1-$q2)>0) {
            $saveok = $inventory->remInventory($q2, $unit, $mat[0]); 
            $q2 = $q2-$q1;
          }
          else {
            $saveok = $inventory->remInventory($q1, $unit, $mat[0]); 
            if (!$saveok) echo 'Error Db';//echo $db->ErrorMsg();
            break;
          }
          if (!$saveok) break;
        }
      }
    } 
  }
  if ($mode=='edit') {
  
    $items_in_dbase = $seg_order_inventory->get_order_items($pharma_refno);
    foreach ($items_in_dbase['items'] as $key => $value)  {
      $inventory = new Inventory();
      $inventory->area_code = $area;
      $inventory->item_code = $value;
      $inventory->addInventory($items_in_dbase['quantities'][$key], $unit, null, null, '');
    }
    foreach ($current_items as $key => $value) {
      $inventory = new Inventory();
      $inventory->area_code = $area;
      $inventory->item_code = $value;
      $matrix = $seg_order_inventory->prepareInventoryMatrix($inventory->item_code, $inventory->area_code);
      
      if ($matrix) {
        $q1 = (int)$current_quantities[$key];
        foreach ($matrix as $mat) {
          $q2 = (int)$mat[1];
          if (($q1-$q2)>0) {
            $saveok = $inventory->remInventory($q2, $unit, $mat[0]); 
            $q2 = $q2-$q1;
          }
          else {
            $saveok = $inventory->remInventory($q1, $unit, $mat[0]); 
            if (!$saveok) echo 'Error Db'; //$db->ErrorMsg();
            break;
          }
          if (!$saveok) break;
        }
      }
    }
  }
}


?>

<script>

J().ready(function() {
  J('#misc_charge')
    .jqDrag('.jqDrag')
    .jqResize('.jqResize');
  J('#or_main_equipment')
    .jqDrag('.jqDrag')
    .jqResize('.jqResize');
  J('#or_main_oxygen')
    .jqDrag('.jqDrag')
    .jqResize('.jqResize');
  J('#other_accommodation')
    .jqDrag('.jqDrag')
    .jqResize('.jqResize');
  
  J("#room_rate").keydown(function(e){return key_check2(e, J("#room_rate").val());});
J("#room_hours").keydown(function(e){return key_check(e, J("#room_hours").val());});
J("#room_days").keydown(function(e){return key_check(e, J("#room_days").val());});
});

J('#misc_charge').jqm({
overlay: 80
});

J('#or_main_equipment').jqm({
overlay: 80
});  

J('#or_main_oxygen').jqm({
overlay: 80
});

J('#other_accommodation').jqm({
overlay: 80
});
  
function openOrderTray2() {
        var discount = J('#discountid').val();
        var area = J('#charge_area_list').val();
       
        var url = '<?=$root_path?>modules/or/request/seg-order-tray.php?area='+area+'&d='+discount;
        overlib(
            OLiframeContent(url, 660, 360, 'fOrderTray', 0, 'no'),
            WIDTH,600, TEXTPADDING,0, BORDER,0, 
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
            CAPTIONPADDING,2, 
            CAPTION,'Add pharmacy item from Order tray',
            MIDX,0, MIDY,0, 
            STATUS,'Add product from Order tray');
        return false
}

function show_popup_misc() {
J('#misc_charge').jqmShow();
  J('#misc_charge_table').flexigrid({
  url: '<?=$root_path?>modules/or/ajax/ajax_miscellaneous.php?table=misc_list',
  dataType: 'json',
  colModel : [
            {display: 'Code', name : 'code', width : 80, sortable : true, align: 'left'},
            {display: 'Name', name : 'name', width : 180, sortable : true, align: 'left'},
            {display: 'Description', name : 'description', width : 180, sortable : true, align: 'left'},
            {display: 'Price', name : 'price', width : 50, sortable : true, align: 'left'},
            {display: 'Select', name:'select', width: 60, sortable: false, align: 'left'}
            ],
  sortname: ["name"],
  sortorder: 'asc',
  useRp: true,
  rp: 5,
  resizable: true,
  domain: ['misc_charge']
});

}

function show_oxygen() {
   var area = J('#charge_area_list').val(); 
   var myurl = '<?=$root_path?>modules/or/ajax/ajax_oxygen.php?table=equipment_list&area='+area;

     
  J('#or_oxygen_table').flexigrid({
  url: myurl,
  dataType: 'json',
  colModel : [
            {display: 'Serial Number', name : 'serial_number', width : 130, sortable : true, align: 'left'},
            {display: 'Remaining Quantity', name : 'remaining_quantity', width : 100, sortable : true, align: 'left'},
            {display: 'Expiry Date', name : 'expiry_date', width : 100, sortable : true, align: 'left'},
            {display: 'Select', name:'select', width: 140, sortable: false, align: 'left'}
            ],
  sortname: ["expiry_date"],
  sortorder: 'asc',
  useRp: true,
  rp: 5,
  resizable: true,
  domain: ['or_main_oxygen']
  });
     J('#or_oxygen_table').flexOptions({url:myurl}); 
      J('#or_oxygen_table').flexReload();   
    J('#or_main_oxygen').jqmShow(); 

}

function hide_oxygen() {
  J('#or_main_oxygen').jqmHide();
}

function show_popup_accommodation() {
  J('#other_accommodation').jqmShow();
  populate_room_list(); 
}

function show_popup_equipment() {
  J('#or_main_equipment').jqmShow();
  J('#or_equipment_table').flexigrid({
  url: '<?=$root_path?>modules/or/ajax/ajax_equipment.php?table=equipment_list',
  dataType: 'json',
  colModel : [
            {display: 'Equipment', name : 'name', width : 90, sortable : true, align: 'left'},
            {display: 'Equipment Description', name : 'name', width : 180, sortable : true, align: 'left'},
            {display: 'Unit', name : 'name', width : 30, sortable : true, align: 'left'},
            {display: 'Cash', name : 'name', width : 50, sortable : true, align: 'left'},
            {display: 'Charge', name : 'name', width : 50, sortable : true, align: 'left'},
            {display: 'Is Socialized', name : 'name', width : 20, sortable : true, align: 'left'},
            {display: 'Select', name:'select', width: 100, sortable: false, align: 'left'}
            ],
  sortname: ["equipment_name"],
  sortorder: 'desc',
  useRp: true,
  rp: 5,
  resizable: true,
  domain: ['or_main_equipment']
}); 

}

function populate_room_list() {
  xajax_populate_room_list(J("#ward_list").val(), 'room_list'); 
}

function get_room_rate() {
  xajax_get_room_rate(J("#room_list").val(), 'room_rate');
}

function key_check(e, value) {
   var character = String.fromCharCode(e.keyCode);
   var number = /^\d+$/;
   if (e.keyCode==9 || e.keyCode==116 || e.keyCode == 8 || e.keyCode == 39 || e.keyCode == 37) {
     return true;
   }
   if (character.match(number)==null) {
     return false;
   }
   else {
     return true;
   }
}

function key_check2(e, value) {
   var character = String.fromCharCode(e.keyCode);
   var number = /^\d+$/;
   var reg = /^[-+]?[0-9]+((\.)|(\.[0-9]+))?$/;                            
   if (character=='¾') {
     character = '.'; 
   }  
   var text_value = value+character; 
   //if ((e.keyCode==190 && text_value.match(reg)!=null) || (e.keyCode==9 || e.keyCode==116 || e.keyCode==46 || e.keyCode==8 || e.keyCode==16 || (e.keyCode>=36 && e.keyCode<=40))) {
   if ((e.keyCode==46 || e.keyCode==8 || e.keyCode==16 || e.keyCode==9 || (e.keyCode==191 || e.keyCode==111) || (e.keyCode>=36 && e.keyCode<=40) || (e.keyCode>=96 && e.keyCode<=105))) {
     return true;
   }
   if (character.match(number)==null) {
     return false;
   }
}

function empty_accommodation2() {
    var table1 = $('accommodation_list').getElementsByTagName('tbody').item(0)  
  table1.innerHTML = '<tr id="empty_accommodation_row"><td colspan="7">Additional accommodation empty...</td></tr>';
    J("input[@name='total_accommodation[]']").remove();
  J("input[@name='room_nr[]']").remove();
  J("input[@name='ward_nr[]']").remove();
  J("input[@name='existing_room_nr[]']").remove();
  update_total_charge_accommodation();  
}

function empty_accommodation() {
  var table1 = $('accommodation_list').getElementsByTagName('tbody').item(0)  
  table1.innerHTML = '<tr id="empty_accommodation_row"><td colspan="7">Additional accommodation empty...</td></tr>';
  
  
  var rooms = document.getElementsByName('existing_room_nr[]'); 

  for (var i=0; i<rooms.length; i++) {
    var hidden_array = document.createElement('input');
    hidden_array.type = 'hidden';
    hidden_array.name = 'removed_room_nr[]';
    hidden_array.value = rooms[i].value;
    hidden_array.id = 'removed_room_nr'+rooms[i].value;
    document.forms[0].appendChild(hidden_array);   
  }

  J("input[@name='total_accommodation[]']").remove();
  J("input[@name='room_nr[]']").remove();
  J("input[@name='ward_nr[]']").remove();
  update_total_charge_accommodation();               
}

function empty_misc() {
   var table1 = $('misc_list').getElementsByTagName('tbody').item(0);  
  table1.innerHTML = '<tr id="empty_misc_row"><td colspan="7">Miscellaneous charges empty...</td></tr>';
  J("input[@name='misc[]']").remove();
  J("input[@name='original_misc_price[]']").remove();
  J("input[@name='account_type[]']").remove();
  update_total_misc(); 
}

function change_charge_area() {
  J.blockUI({ message: "<h1>Fetching charges...</h1>" });
  empty_accommodation2();
  empty_misc();
  empty_equipment();
  xajax_set_pharma_refno('<?=$encounter_nr?>', J('#charge_area_list').val(), J('#discountid').val());
  xajax_set_equipment_refno('<?=$encounter_nr?>', 'equipment_list', J('#charge_area_list').val());
//xajax_populate_order(J('#pharma_refno').val(), J('#discountid').val());
//xajax_populate_equipment_oxygen(<?=$equipment_refno?>, 'equipment_list');   
//xajax_populate_equipment_order(<?=$equipment_refno?>, 'equipment_list', J('#charge_area_list').val());

xajax_populate_misc_order('misc_list', <?=$encounter_nr?>, J('#charge_area_list').val());
xajax_populate_accommodation('<?=$encounter_nr?>', J('#charge_area_list').val());
J.unblockUI(); 
}

xajax_set_pharma_refno('<?=$encounter_nr?>', J('#charge_area_list').val(), J('#discountid').val());
 
//xajax_populate_order(J('#pharma_refno').val(), J('#discountid').val());
//xajax_populate_equipment_oxygen(<?=$equipment_refno?>, 'equipment_list');   
//xajax_populate_equipment_order(<?=$equipment_refno?>, 'equipment_list');
xajax_set_equipment_refno(<?=$encounter_nr?>, 'equipment_list', J('#charge_area_list').val());
xajax_populate_misc_order('misc_list', <?=$encounter_nr?>, J('#charge_area_list').val());
xajax_populate_accommodation('<?=$encounter_nr?>', J('#charge_area_list').val());

update_total(); 
update_total_misc();
update_total_charge_accommodation();

J.unblockUI();
</script>
