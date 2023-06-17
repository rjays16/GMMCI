<?php
require_once($root_path.'include/care_api_classes/inventory/class_delivery.php');
require_once($root_path.'include/care_api_classes/inventory/class_serial.php');
require_once($root_path."include/care_api_classes/class_pharma_product.php");
require_once($root_path.'include/care_api_classes/curl/class_curl.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

//if ($_GET["from"]=="CLOSE_WINDOW") {
// $smarty->assign('bHideTitleBar',TRUE);
// $smarty->assign('bHideCopyright',TRUE);
//}

# Title in the title bar
if (isset($_POST['source'])) $source = $_POST['source'];
$smarty->assign('sToolbarTitle',$source."::$target Delivery");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',$source."::$target Delivery");

global $db;
//$db->debug = 1;
if (isset($_POST["submitted"])) {
		$objdel = new Delivery();
		$curl_obj = new Rest_Curl();
		$pclass = new SegPharmaProduct();
		//added by bryan on 112609
		//latestrefno is obatained before saving
        $bnew = false;
		if (($_POST['old_refno'])=="") {
				$lastnrthis = $objdel->getLastNr(date("Y-m-d"));
                $bnew = true;
        }
		else
				$lastnrthis = $_POST['refno'];
        
        $user = $_SESSION['sess_user_name'];
		$data = array(
				'refno'=>$lastnrthis,
				'receipt_date'=>$_POST["delivery_date"],
				'receiving_id'=>$_SESSION['sess_user_personell_nr'],
				'area_code'=>$_POST['rcv_area'],
				'pono'=>$_POST['pono'],
				'po_no'=>$_POST['po_no'],
				'supplier_id'=>$_POST['supplier'],
				'remarks'=>$_POST['remarks'],
                'history'=>$objdel->makeHistory(($bnew) ? "Added" : "Updated"),
                'create_id'=>$user,
                'modify_id'=>$user,
                'create_dt'=>date('Y-m-d H:i:s'),
                'invoice_no'=>$_POST['invoice_no']
				);

		$objdel->useDeliveryHdr();
		$objdel->setDataArray($data);

		$objdel->startTrans();        

		if ($_POST['old_refno'] == '') {
				// Insert new delivery ...
				$saveok = $objdel->insertDataFromInternalArray();
		}
		else {
				// Update old refno.
				$saveok = $objdel->delDeliveryDetail($_POST['old_refno'], $_POST['old_deliverydte']);
				if ($saveok) {
						$objdel->setWhereCondition("refno = '".$_POST['old_refno']."'");
						$saveok = $objdel->updateDataFromInternalArray($_POST['old_refno'], FALSE);
				}
		}

		if ($saveok) {

			//added by ken: to coincide the area in HIS to FIS
			if($_POST['rcv_area'] == 'IP')
				$rcv_area = 'PHARM';
			else if($_POST['rcv_area'] == 'SUP')
				$rcv_area = 'CSR';

			//added by ken: to compile all the data needed in saving the delivery in FIS.,
			$fis_data = array();
			$data_items = array();
			$fis_items = array();
			$count=0;
				foreach ($_POST["items"] as $i=>$v) {
						$conv_qty = '';
						$conv_price = '';
						$sitem_code = $v;                                   // item code
						$nqty = str_replace(',', '', $_POST['qtys'][$i]);   // qty
						$unit_id = $_POST['unit_ids'][$i];                  // unit ids
						$uprice  = $_POST['uprices'][$i];                   // unit price
						$is_perpc = $_POST['is_unitpcs'][$i];               // is unit per pc?
						$supp_id = $_POST['supps'][$i] != 'undefined' ? $_POST['supps'][$i] : $_POST['supplier'];
						$location = $_POST['locs'][$i] != 'undefined' ? $_POST['locs'][$i] : $rcv_area;
						$order_no = $_POST['order_nos'][$i] != 'undefined' ? $_POST['order_nos'][$i] : '';
						$description = $_POST['names'][$i];
						$del_date = $_POST['dates'][$i] != 'undefined' ? $_POST['dates'][$i] : date("m/d/Y");
						$oqty = str_replace(',', '', $_POST['oqtys'][$i]);
						$po_detail = $_POST['pos'][$i];
                        $expiry = $_POST['expiry_dts'][$i];                 // Expiry date
                        $qty_per_pck = $_POST['qty_per_pck'][$i]; //quantity per packing
                        $small_unit = $_POST['small_unit'][$i]; //small unit
                        $fg_item = $_POST['fg_items'][$i]; //Free Goods Added by Jarel 06162015
                        if ($expiry == '')
                            $expiry = '0000-00-00';

                        $serial = $_POST['serial_nos'][$i];
      
                    $data = array(
								'refno'=>$lastnrthis,
								'item_code'=>$sitem_code,
								'unit_price'=>$uprice,
								'item_qty'=>$nqty,
								'unit_id'=>$unit_id,
                                'expiry_date' => $expiry,
								'is_unitperpc'=>$is_perpc,
								'qty_per_pck'=>$qty_per_pck,
								'small_unit'=>$small_unit,
								'is_fg' => $fg_item);

						$items_details = (object) array(
											'po_detail_rec'=> $po_detail,
											'stock_id' => $sitem_code,
											'item_description' => $description,
											'price' => $conv_price != '' ? $conv_price : $uprice,
											'units' => $_POST['unit_ids'][$i] == 1 ? 'pack(s)' : 'piece(s)',
                                            'req_del_date' => $del_date,
											'tax_type' => '1',
											'tax_type_name' => 'Regular',
											'quantity' => $oqty,
											'receive_qty' => $conv_qty != '' ? $conv_qty : $nqty,
											'qty_received' => '0',
											'standard_cost' => '0');
						
						if($fg_item != 1){ //free goods not to be invoiced
	                    	$fis_items[] = array(
									'item_code'=>$sitem_code,
									'quantity_received'=>$nqty
									);
                    
							$data_items[] = $items_details;

							$fis_data['trans_type'] = '25';
							$fis_data['supplier_id'] = $supp_id;
							$fis_data['delivery_date'] = strtotime($_POST["delivery_date"]);
							$fis_data['supplier_name'] = $_POST['supplier_name'];
							$fis_data['due_date'] = $del_date;
							$fis_data['order_no'] = $order_no;
							$fis_data['refno'] = $lastnrthis;
							$fis_data['location'] = $location;
							$fis_data['po_no'] = $_POST['po_no'];
							$fis_data['delivery_invoice_no'] = $_POST['invoice_no'];
						}
						//ended by ken

						$objdel->useDeliveryDetails();
                 // Associated serial no.

						// Initialize serial object ...
						if ($serial != '') {
								$objserial = new Serial();
								$objserial->serial_no = $serial;
               					$objserial->item_code = $sitem_code;
								$objserial->area_code = $_POST['rcv_area'];
								$objserial->acquisition_cost = $uprice;
								$objserial->acquisition_date = $_POST["delivery_date"];
								$objserial->property_no = '';       // Default
						}
						else
								$objserial = NULL;
						// echo print_r($fis_data, true);die;
					    $lotNo = $_POST['lot_nos'][$i];
					    $manufacturer = $_POST['manufacturer'][$i];

                        $saveok = $objdel->saveDeliveryDetail($_POST['rcv_area'], $data, $_POST["delivery_date"], $expiry, $objserial, $lotNo, $fg_item,$manufacturer);

						if (!$saveok) break;
				}
                if ($saveok){ 
                	$objdel->clearTmpTable();

                	//added by ken: to call the saving function in FIS
                	if ($_POST['old_refno'] == '') {
                		$curl_obj->saveGRN($fis_data, $data_items);	
                	}else{
                		$curl_obj->updateGRN($lastnrthis, array("delivered_items"=>$fis_items, "delivery_invoice_no"=>$_POST['invoice_no']));	
                	}
                	
                }
		}

//        $db->failTrans();
		if (!$saveok) $objdel->failTrans();
		$objdel->completeTrans();

		if ($saveok)
				$smarty->assign('sysInfoMessage','<strong>Successfully saved the delivery!</strong>');
		else
				$smarty->assign('sysErrorMessage','<strong>Error:</strong> '.(($err_msg = $objdel->LastErrorMsg()) == '' ? $objdel->getErrorMsg() : $err_msg));
}

# Assign Body Onload javascript code
$onLoadJS="onload=\"init()\"";
$smarty->assign('sOnLoadJs',$onLoadJS);
#$smarty->assign('bShowQuickKeys',!$_REQUEST['viewonly']);
$smarty->assign('bShowQuickKeys',FALSE);

# Collect javascript code
ob_start();
		 # Load the javascript code
?>
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<!-- START for setting the DATE (NOTE: should be IN this ORDER) -->
<script type="text/javascript" language="javascript">
<?php
		require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/gen_routines.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/datefuncs.js"></script>
<script type="text/javascript" src="js/seg-delivery-gui.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">

		var trayItems = 0;

		function init() {
<?php
		if (!$_REQUEST['viewonly']) {
?>
				// Edit/Submit shortcuts
				shortcut.add('F2', keyF2,
						{
								'type':'keydown',
								'propagate':false
						}
				);
				shortcut.add('F3', keyF3,
						{
								'type':'keydown',
								'propagate':false
						}
				);

<?php
		}
?>

		}

		function keyF2() {
				openItemsTray();
		}

		function keyF3() {
				if (confirm('Clear the delivery list?'))    emptyTray();
		}

		function keyF12() {
				if (validate()) document.inputform.submit()
		}
		function openItemsTray() {
				//var area = $('ori_area').value;                   +area
				var url = 'seg-delivery-tray.php?area=';
				overlib(
						OLiframeContent(url, 660, 397, 'fOrderTray', 0, 'no'),
						WIDTH,600, TEXTPADDING,0, BORDER,0,
						STICKY, SCROLL, CLOSECLICK, MODAL,
						CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
						CAPTIONPADDING,2,
						CAPTION,'Add Item from Tray',
						MIDX,0, MIDY,0,
						STATUS,'Add Item from Tray');
				return false
		}

		function validate() {

				var total_amount = document.getElementsByName('total_amount[]');
				var fg_items = document.getElementsByName('fg_items[]');

				if (!$('refno').value) {
						alert("Please enter the reference no.");
						$('refno').focus();
						return false;
				}

				if (!$('rcv_area').value) {
						alert("Please select area first.");
						$('rcv_area').focus();
						return false;
				}

				if (document.getElementsByName('items[]').length==0) {
						alert("Warning: The item list is empty...");
						return false;
				}

				 for(var i=0; i<total_amount.length; i++){
				 	if (fg_items[i].value == 0 ) {
		            	if(total_amount[i].value <= 0){
		            		alert("Unable to save zero amount.");
		            		return false;
		            	}
		            }
		        }

				return confirm('Process this delivery?');
		}

</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

if (isset($_GET["refno"])) {
		$smarty->assign('sDeliveryDetails',"
								<tr>
										<td colspan=\"13\">Delivery is currently empty ...</td>
								</tr>");

		// Populate the header array and details ...
		if (!isset($objdel)) $objdel = new Delivery();

		if ($result = $objdel->getDeliveryHeader($_GET["refno"])) {
				$lastnr = $result["refno"];
				$_POST['old_refno'] = $lastnr;
				$_REQUEST['dateset'] = $result["receipt_date"];
				$pid = $result["receiving_id"];
				$name = $result["received_by"];
				$_POST['rcv_area'] = $result["area_code"];
				$remarks = $result["remarks"];
				$pono = $result["pono"];
				$supplier = $result["supplier_id"];
				$po_no = $result['po_no']; //added by maimai 12-15-2014
				$invoice_no = $result['invoice_no'];
				if ($result = $objdel->getDeliveryDetails($_GET["refno"])) {
						$script = '<script type="text/javascript" language="javascript">';
						$script .= '$("supplier").value = "'.$supplier.'";';
						$script .= '$("po_no").value = "'.$po_no.'";';
						$items = array();
						$unitids = array();
						$expiry_dts = array();
						$serials = array();
						$is_pcs  = array();
						$qtys    = array();
						$uprices = array();

						if ($result->RecordCount()) {
								while ($row = $result->FetchRow()) {
										$items[]      = $row["item_code"];
										$uprices[]    = $row["unit_price"];
										$expiry_dts[] = $row["expiry_date"];
										$serials[]    = $row["serial_no"];
										$qtys[]       = $row["item_qty"];
										$unitids[]    = $row["unit_id"];
										$is_pcs[]     = $row["is_unitperpc"];
                                        $lotnos[] =    $row['lot_no'];
                                        $qty_per_pck[] = $row['qty_per_pck'];
                                        $small_unit[] = $row['small_unit'];
                                        $is_fg[] = $row['is_fg'];
                                        $manufacturer[] = $row['manufacturer'];
                                }

								$script .= "var items0 =['" .implode("','",$items)."'];";
								$script .= "var units0 =[" .implode(",",$unitids). "];";
								$script .= "var expirys0 = ['" .implode("','",$expiry_dts). "'];";
								$script .= "var serials0 = ['" .implode("','",$serials). "'];";
								$script .= "var ispcs0 = [" .implode(",",$is_pcs). "];";
								$script .= "var qtys0  = [" .implode(",",$qtys). "];";
								$script .= "var uprices0 =[" .implode(",",$uprices). "];";
                                $script .= "var lotnos0 = ['" .implode("','",$lotnos). "'];";
                                $script .= "var qty_per_pck0 = ['" .implode("','",$qty_per_pck). "'];";
                                $script .= "var small_unit0 = ['" .implode("','",$small_unit). "'];";
                                  $script .= "var is_fg0 = ['" .implode("','",$is_fg). "'];";
                                  $script .= "var manufacturer = ['" .implode("','",$manufacturer). "'];";

                            $script .= "xajax_goAddItem(items0, units0, expirys0, serials0, ispcs0, qtys0, uprices0, lotnos0, qty_per_pck0, small_unit0, is_fg0,manufacturer);";
								$script .= "</script>";
								$src = $script;
						}
						else
								$src = "<tr>
														<td colspan=\"13\">Delivery is currently empty ...</td>
												</tr>";

						if ($src) $smarty->assign('sDeliveryDetails',$src);
				}
		}
}
else {
		$pid  = $_SESSION['sess_user_personell_nr'];
		$name = $_SESSION['sess_login_username'];
}

# Render form values
if (isset($_POST["submitted"]) && !$saveok) {
		$smarty->assign('sDeliveryDetails',"
								<tr>
										<td colspan=\"13\">Delivery is currently empty ...</td>
								</tr>");

		if (is_array($_POST['items'])) {
				$script = '<script type="text/javascript" language="javascript">';
				$items = $_POST['items'];
				$unitids = array();
				$expiry_dts = array();
				$serials = array();
				$is_pcs  = array();
				$qtys    = array();
				$uprices = array();

				foreach ($items as $i=>$item) {
						$unitids[$i] = $_POST['unit_ids'][$i];
						$expiry_dts[$i] = $_POST['expiry_dts'][$i];
						$serials[$i] = $_POST['serial_nos'][$i];
						$is_pcs[$i]  = $_POST['is_unitpcs'][$i];
						$qtys[$i]    = $_POST['qtys'][$i];
						if (!is_numeric($qtys[$i])) $qtys[$i] = '0';
						$uprices[$i] = $_POST['uprices'][$i];
				}

				$script .= "var items0 =['" .implode("','",$items)."'];";
				$script .= "var units0 =[" .implode(",",$unitids). "];";
				$script .= "var expirys0 = [" .implode(",",$expiry_dts). "];";
				$script .= "var serials0 = [" .implode(",",$serials). "];";
				$script .= "var ispcs0 = [" .implode(",",$is_pcs). "];";
				$script .= "var qtys0  = [" .implode(",",$qtys). "];";
				$script .= "var uprices0 =[" .implode(",",$uprices). "];";
            $script .= "var lotnos0 = ['" .implode("','",$lotnos). "'];";

            $script .= "xajax_goAddItem(items0, units0, expirys0, serials0, ispcs0, qtys0, uprices0, lotnos0);";
                $script .= "</script>";
				$src = $script;
		}
		if ($src) $smarty->assign('sDeliveryDetails',$src);
}
elseif (!isset($_GET["refno"])) {
		$smarty->assign('sDeliveryDetails',"
								<tr>
										<td colspan=\"13\">Delivery is currently empty ...</td>
								</tr>");
}

# Render form elements
		$submitted = isset($_POST["submitted"]);

		require_once($root_path.'include/care_api_classes/class_access.php');
		require_once($root_path.'include/care_api_classes/class_department.php');

		$objdelivery = new Delivery();
		$curl_obj = new Rest_Curl();

		$obj = new Access();
		$dept_nr = $obj->getDeptNr($_SESSION['sess_temp_userid']);

		$per_arr = explode(" ", $HTTP_SESSION_VARS['sess_permission']);

		if (in_array("System_Admin", $per_arr) || in_array("_a_0_all", $per_arr)) $dept_nr = "''";
		$rcv_area = "<option value=\"\" $checked>- Select Area -</option>\n";
        $areaObj = new SegArea();
        $result = $areaObj->getInventoryAreas();
        if ($result) {
				foreach($result as $row) {
						$checked=(strtolower($row['area_code'])==strtolower($_GET['rcv_area'])) || (strtolower($row['area_code']) == strtolower($_POST['rcv_area'])) ? 'selected="selected"' : "";
						$rcv_area .= "<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";

						if ($checked || ($count == 0)) $s_areacode = $row['area_code'];
						if ($checked) $index = $count;
						$count++;
				}
		}
		else
				$rcv_area = "<option value=\"\" $checked>- Assigned department has no areas -</option>\n";

		$rcv_area = '<select class="segInput" name="rcv_area" id="rcv_area" onchange="jsRqstngAreaOptionChng(this, this.options[this.selectedIndex].value);">'."\n".$rcv_area."</select>\n".
								"<input type=\"hidden\" id=\"area2\" name=\"area2\" value=\"".$_GET['ori_area']."\"/>";
		$smarty->assign('sSelectArea',$rcv_area);

		$smarty->assign('sReceivingID','<input id="pid" name="pid" type="hidden" value="'.$pid.'"/>');
		$smarty->assign('sReceivingNM','<input class="segInput" id="receivingname" name="receivingname" type="text" size="40" value="'.$name.'" style="font:bold 12px Arial;" disabled />');

		if (!isset($_GET["refno"])){
				$lastnrthis = $objdelivery->getLastNr(date("Y-m-d"));
		}else{
				$lastnrthis = $_GET["refno"];
		}

		$smarty->assign('sRefNo','<input class="segInput" id="refno" name="refno" type="text" size="10" value="'.($submitted && !$saveok ? $_POST['refno'] : $lastnrthis).'" style="font:bold 12px Arial"/>');
		$smarty->assign('sResetRefNo','<input class="segButton" type="button" value="Reset" onclick="xajax_reset_referenceno()"/>');

		#added by ken for supplier field., 7/23/2014
		$supplier = "<option value=\"\">- Select Supplier -</option>\n";
		$supplier_values = $curl_obj->getAllSupplier();
		if($supplier_values){
			$supplier_values = json_decode($supplier_values, true);
			foreach($supplier_values AS $key => $value){

				$supplier .= "<option value=\"".$value['supplier_id']."\">".$value['supp_name']."</option>\n";
			}
		}
		else
			$supplier = "<option value=\"\" >- No Supplier Available -</option>\n";

		$supplier = '<select class="segInput" name="supplier" id="supplier" onchange="jsSetSupplierName(this);">'."\n".$supplier."</select>\n".
					"<input type=\"hidden\" id=\"supplier_name\" name=\"supplier_name\" value=\"\"/>";;
		#added by brywong on May 22,09
		// $smarty->assign('sPoNo','<input class="segInput" id="pono" name="pono" type="text" size="10" value="'. $pono .'" style="font:bold 12px Arial"/>');

		$po_no = $curl_obj->getPoNo();
        $po_no = json_decode($po_no, true);
        if($po_no){
        	$po_no = $po_no[0]+1;
        }
		$smarty->assign('sPoNo','<input class="segInput" id="po_no" name="po_no" type="text" size="15" value="'.$po_no.'" style="font:bold 12px Arial"/>');
		$smarty->assign('sInvoiceNo', '<input class="segInput" id="invoice_no" name="invoice_no" type="text" size="15" value="'.$invoice_no.'" style="font:bold 12px Arial">');
		$smarty->assign('sResetPoNo','<input class="segButton" type="button" value="Reset" onclick="xajax_reset_pono()"/>');
		$smarty->assign('sSupplier',$supplier);

		$dbtime_format = "Y-m-d H:i";
		$fulltime_format = "F j, Y g:ia";
		if ($_REQUEST['dateset']) {
				$curDate = date($dbtime_format,strtotime($_REQUEST['dateset']));
				$curDate_show = date($fulltime_format, strtotime($_REQUEST['dateset']));
		}
		else {
				$curDate = date($dbtime_format);
				$curDate_show = date($fulltime_format);
		}
		$smarty->assign('sDeliveryDate','<span id="show_deliverydate" class="segInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">'.($submitted && !$saveok ? date($fulltime_format,strtotime($_POST['delivery_date'])) : $curDate_show).'</span><input class="segInput" name="delivery_date" id="delivery_date" type="hidden" value="'.($submitted && !$saveok ? date($dbtime_format,strtotime($_POST['delivery_date'])) : $curDate).'" style="font:bold 12px Arial">');

		$smarty->assign('sCalendarIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="deliverydate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
		$jsCalScript = "<script type=\"text/javascript\">
				Calendar.setup ({
						displayArea : \"show_deliverydate\",
						inputField : \"delivery_date\",
						ifFormat : \"%Y-%m-%d %H:%M\",
						daFormat : \"    %B %e, %Y %I:%M%P\",
						showsTime : true,
						button : \"deliverydate_trigger\",
						singleClick : true,
						step : 1
				});
		</script>";
		$smarty->assign('jsCalendarSetup', $jsCalScript);

$smarty->assign('sRemarks','<textarea class="segInput" id="remarks" name="remarks" cols="100" rows="2" style="float:left; margin-left:0px;margin-top:3px">'.($submitted && !$saveok ? $_POST['remarks'] : $remarks).'</textarea>');
$smarty->assign('sRootPath',$root_path);
$smarty->assign('sBtnAddItem','<img class="segSimulatedLink" id="add-item" style="display:;" src="'.$root_path.'images/btn_additems.gif" border="0" onclick="return openItemsTray();">');
$smarty->assign('sBtnEmptyList','<img class="segSimulatedLink" id="clear-list" src="'.$root_path.'images/btn_emptylist.gif" border="0" onclick="if (confirm(\'Clear the delivery list?\')) emptyTray()"/>');
$smarty->assign('sSearchField','<input class="segInput" id="searchField" name="searchField" type="text" size="30" placeholder="Search Purchase Order number..." value="" style="font:bold 12px Arial; margin-left:450px;" />');
$smarty->assign('sBtnSearch','<input class="segButton" type="button" value="Search" onclick="xajax_searchPO(searchField.value)"/>');

if($error=="refno_exists"){
		$smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
		$smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}

$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&target=New&clear_ck_sid=".$clear_ck_sid.'&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

?>
<input type="hidden" name="submitted" value="1" />
<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">
<input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
<input type="hidden" name="target" value="<?php echo $target ?>">

<input type="hidden" name="editpname" id="editpname" value="">
<input type="hidden" name="editpqty"  id="editpqty"  value="">
<input type="hidden" name="editppk"   id="editppk"   value="">
<input type="hidden" name="editppack" id="editppack" value="">
<input type="hidden" name="dateset" id="dateset" value="<?= $_REQUEST['dateset'] ?>">
<input type="hidden" name="old_refno" id="old_refno" value="<?= $_GET['refno'] ?>">
<input type="hidden" name="old_deliverydte" id="old_deliverydte" value="<?= $_REQUEST['dateset'] ?>">
<input type="hidden" name="source" id="source" value="<?=$source?>">
<?php

$sTemp = ob_get_contents();
ob_end_clean();

/*
global $GPC;
echo $GPC;
echo "<hr>sid:$sid;clear:$clear_ck_sid";
*/

//$sBreakImg ='close2.gif';
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
$smarty->assign('sContinueButton','<img src="'.$root_path.'images/btn_submitorder.gif" align="center" onclick="if (validate()) document.inputform.submit()"  style="cursor:pointer" />');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','inventory/delivery-form.tpl');
$smarty->display('common/mainframe.tpl');
?>