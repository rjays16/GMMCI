<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root= directory
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');   
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path . 'include/care_api_classes/billing/class_ops.php'); //load the segops class
require_once($root_path . 'include/care_api_classes/class_social_service.php'); //load the segops class
include_once($root_path . 'include/care_api_classes/class_order.php');  //load the SegOrder class


  

$smarty = new Smarty_Care('charge_or_request');
$smarty->assign('sToolbarTitle',"OR::Charges"); //Assign a toolbar title
$target = $_GET['target'];
$breakfile = $root_path . 'modules/or/request/select_or_request.php'.URL_APPEND.'&target='.$target; 

/** css and js scripts **/
$css_and_js = array('<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_charge.css" type="text/css" />');
$smarty->assign('css_and_js', $css_and_js);

$reference_number = isset($_GET['refno']) ? $_GET['refno'] : $_POST['refno'];  //Assign the reference number of OR Request
$seg_ops = new SegOps(); //instantiate a SegOps object
$or_details = $seg_ops->get_or_request_details($reference_number); //get the or request details


$pharma_refno = isset($_GET['pharma_refno']) ? $_GET['pharma_refno'] : $_POST['pharma_refno'];   //Assign the reference number of pharma order
$seg_order = new SegOrder('pharma');
$order_resource =  $seg_order->getOrderInfo($pharma_refno);
$order_details = $order_resource->FetchRow();



/**Start: Used for OR Request Details Fieldset **/
$smarty->assign('reference_number', $reference_number);
$smarty->assign('department', $or_details['name_formal']);
$smarty->assign('operating_room', $or_details['info']);
$smarty->assign('request_date', date('F d, Y h:ia', strtotime($or_details['request_date'])));
$smarty->assign('operation_date', date('F d, Y h:ia', strtotime($or_details['operation_date'])));
$smarty->assign('patient_id', $or_details['pid']);
$smarty->assign('patient_name', $or_details['ordername']);
$smarty->assign('patient_address', $or_details['orderaddress']);
/**End: Used for OR Request Details Fieldset **/

/** Start: Used for the OR Charges Fieldset **/
$smarty->assign('form_start', '<form name="main_or_form" method="POST" action="'.$_SERVER['PHP_SELF'].'">');

$transaction_type = ($order_details['is_cash']==1) ? 'Cash' : 'Charge';
$priority = ($order_details['is_urgent']==1) ? 'Urgent' : 'Normal';

$smarty->assign('transaction_type', $transaction_type);
$smarty->assign('priority', $priority);
$smarty->assign('requested_date', date('F d, Y h:ia', strtotime($order_details['orderdate'])));



/** End: Used for the OR Charges Fieldset **/

$social_service = new SocialService();
$social_service_details = $social_service->getLatestClassificationByPid($or_details['pid']); //get the discount and discountid

$is_sc = ($social_service_details['discountid'] == 'SC') ? 'true' : 'false';
$smarty->assign('is_submitted', '<input type="hidden" value="TRUE" name="is_submitted" />');
$smarty->assign('pharma_area', '<input type="hidden" name="pharma_area" id="pharma_area" value="OR" />');
$smarty->assign('encounter_nr', '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$or_details['encounter_nr'].'" />');
$smarty->assign('issc', '<input type="hidden" name="issc" id="issc" value="'.$is_sc.'" />');
$smarty->assign('refno', '<input type="hidden" name="refno" id="refno" value="'.$reference_number.'" />');
$smarty->assign('pharma_refno', '<input type="hidden" name="pharma_refno" id="pharma_refno" value="'.$pharma_refno.'" />');
$smarty->assign('discountid', '<input type="hidden" name="discountid" id="discountid" value="'.$social_service_details['discountid'].'" />');
$smarty->assign('discount', '<input type="hidden" name="discount" id="discount" value="'.$social_service_details['discount'].'" />');
$smarty->assign('charge_submit', '<input type="submit" id="charge_update" value="" />');
$smarty->assign('charge_cancel', '<a href="'.$breakfile.'" id="charge_cancel"></a>'); 

$smarty->assign('supplies_add_button','<img class="segSimulatedLink" id="supplies_add" src="'.$root_path.'images/btn_additems.gif" border="0" onclick="return openOrderTray();">');
$smarty->assign('form_end', '</form>');



if (isset($_POST['is_submitted'])) {

  $item_array = array();
  $status_array = array();
  $remarks_array = array();
  foreach ($_POST['items'] as $i=>$v) {
    $item_array[] = $v;
    $status_array[] = $_POST['status'][$i];
    $remarks_array[] = $_POST['remarks'][$i];
  }
  
  $seg_order->changeServeStatus($pharma_refno, $item_array, $status_array, $remarks_array);
  $smarty->assign('sysInfoMessage','Serve details updated...');
  print_r($db->ErrorMsg());

}

$result = $seg_order->getOrderItemsFullInfo($pharma_refno);
$total = 0;
$total_served = 0;
$total_items = 0;
if ($result) {
  while ($row=$result->FetchRow()) {
  
    $toggle=!$toggle;
    $rows .= '<input name="items[]" type="hidden" value="'.$row['bestellnum'].'">'.
             '<tr>
                <td style="color:#800000">'.$row['bestellnum'].'</td>'.
                '<td>'.$row['artikelname'].'</td>'.
                '<td align="right">'.$row['quantity'].'</td>'.
                '<td align="right">'.number_format($row['force_price'],2,'.',',').'</td>'.
                '<td align="right">'.number_format($row['quantity']*$row['force_price'],2,'.',',').'</td>'.
                '<td>'.
                    '<select name="status[]" class="jedinput"  style="font-size:11px">'.
                        '<option value="N"'.($row['serve_status']=='N' ? ' selected="selected"' : '').'>Not served</option>'.
                        '<option value="S"'.($row['serve_status']=='S' ? ' selected="selected"' : '').'>Served</option>'.
                    '</select>'.
                '</td>'.
                '<td style="padding:2px" align="center">'.
                    '<input type="text" name="remarks[]" class="jedInput" value="'.(htmlentities($row['serve_remarks'])).'" style="width:98"/>'.
                '</td>'.
            '</tr>';
            if ($row['serve_status']=='S') $total_served++;
            $total_items++;
            $total += $row['quantity']*$row['force_price'];
        }
        if (!$rows) $rows='<tr><td colspan="10">No items found...</td></tr>';
    }
$smarty->assign('total_price',number_format($total,2,'.',','));
$smarty->assign('rows', $rows);



$smarty->assign('sMainBlockIncludeFile','or/serve_or_charge.tpl'); //Assign the charge_or_request template to the frameset
$smarty->display('common/mainframe.tpl'); //Display he contents of the frame 




 

?>
<script>


//document.body.onLoad = refreshTotalSupplies();


</script>