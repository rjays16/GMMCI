<?php
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/clinics/ajax/clinic-requests.common.php');
require_once($root_path.'include/care_api_classes/dialysis/class_dialysis.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/class_social_service.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/care_api_classes/class_order.php');
require_once($root_path.'include/care_api_classes/or/class_segOr_miscCharges.php');
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
require_once($root_path.'include/care_api_classes/class_radiology.php');
require_once($root_path."include/care_api_classes/billing/class_bill_info.php");
require_once($root_path."include/care_api_classes/curl/class_curl.php");

function populateMiscRequests($encounter_nr, $ptype, $is_bill_final, $date)
{
	global $db;
	$objResponse = new xajaxResponse();
	$misc_obj = new SegOR_MiscCharges();
	//$objResponse->alert(" final bill \n".$is_bill_final);

	$sql = "SELECT refno, IF(is_cash='0','Charge','Cash') AS `charge_type`, request_source FROM seg_misc_service \n".
				" WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND DATE(chrge_dte)=DATE(".$db->qstr(date("Y-m-d",strtotime($date))).")".
				" ORDER BY chrge_dte, refno DESC";
	$result = $db->Execute($sql);
	//$objResponse->alert("misc\n".$sql);
	$objResponse->assign("misc_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if($result!==FALSE){
		while($ref = $result->FetchRow())
		{
			$objResponse->call("createTableHeader", "misc_requests", "misc-list".$ref['refno'], $ref['refno'], $ref['charge_type']);
			$res = $misc_obj->getMiscOrderItemsByRefno($ref['refno']);
			//$objResponse->alert("misc\n".$misc_obj->sql);
			if($res!==FALSE){
			 $req_flag=false;
			 $total_amount = 0;
			 while($row=$res->FetchRow())
			 {
				 switch(strtolower($row["request_flag"]))
				 {
					 case 'cmap':
							$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
							$req_flag=true;
							break;
					 case 'lingap':
							$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
							$req_flag=true;
							break;
					 case 'paid':
							$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
							$req_flag=true;
							break;
					 case 'charity':
							$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
							$req_flag=true;
							break;
					 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
				 }
        //edited by jasper 04/10/2013
		/*		 $data = array(
						'refno'=>$ref['refno'],
						'order_date'=>date('d-M-Y h:i: a',strtotime($row["chrge_dte"])),
						'status'=>$request_flag,
						'item_name'=>$row["name"],
						'item_code'=>$row["code"],
						'item_qty'=>$row["quantity"],
						'total_prc'=>$row["net_price"],
						'item_prc'=>parseFloatEx($row["net_price"]/$row["quantity"])
					);   */
                    $data = array(
                        'refno'=>$ref['refno'],
                        'order_date'=>date('d-M-Y h:i: a',strtotime($row["chrge_dte"])),
                        'status'=>$request_flag,
                        'item_name'=>$row["name"],
                        'item_code'=>$row["code"],
                        'item_qty'=>$row["quantity"],
                        'total_prc'=>parseFloatEx($row["chrg_amnt"]*$row["quantity"]),
                        'item_prc'=>parseFloatEx($row["chrg_amnt"])
                    );
					$objResponse->call("printRequestlist", "misc_requests", "misc-list".$ref['refno'], $data);

					$total_amount+=parseFloatEx($row["quantity"]*$row["chrg_amnt"]);
                    $total_amountCash+=parseFloatEx($row["net_price"]);
			 }

			 if(strtolower($ref["charge_type"])=="cash") {
				 $total_cash+=parseFloatEx($total_amountCash);
			 }
			 else if(strtolower($ref["charge_type"])=="charge") {
				 $total_charge+=parseFloatEx($total_amount);
			 }

			 #editted by CELSY 08/25/10
					if($ref["request_source"]==$ptype)
						$notPtype = false;
					else
						$notPtype = true;

			 #$objResponse->alert($ref["request_source"]."  hi  ".$ptype."  notptype ".$notPtype,"\n final bill ".$is_bill_final);
			 if($req_flag==false && $notPtype==false && $is_bill_final==0) {
				$buttons = '<button class="segButton" onclick="openEditRequest(\'misc_requests\',\''.$ref['refno'].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
									'<button class="segButton" onclick="openDeleteRequest(\'misc_requests\',\''.$ref['refno'].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
			 }else {
				$buttons = '<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
									'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
			 }

//			 if($req_flag==true)
//			 {
//				$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
//									'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
//			 }else {
//				$buttons = '<button class="segButton" onclick="openEditRequest(\'misc_requests\',\''.$ref['refno'].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
//									'<button class="segButton" onclick="openDeleteRequest(\'misc_requests\',\''.$ref['refno'].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
//			 }

			 $objResponse->assign("btn-".$ref['refno'],"innerHTML", $buttons);
		 }
		}
		$objResponse->assign("misc-total-cash", "innerHTML", number_format($total_cash, 2));
		$objResponse->assign("misc-total-charge", "innerHTML", number_format($total_charge, 2));
	}
	return $objResponse;
}

function populateIpRequests($encounter_nr,$ptype,$is_bill_final, $date)
{
	global $db;
	$objResponse = new xajaxResponse();
	$order_obj = new SegOrder();
	$filters = array('inpatient'=>$encounter_nr,'area'=>'IP', 'date'=>$date);
	$res = $order_obj->getActiveOrders($filters, 0, 10);
	//$objResponse->alert("IP order\n".$order_obj->sql);
	$objResponse->assign("ip_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if($res!==FALSE) {
		while($row=$res->FetchRow())
		{
			$result = $order_obj->getOrderItemsFullInfo($row["refno"],'');
			if($result!==FALSE) {
				$charge_type = $row["is_cash"]==0?'Charge':'Cash';
				$objResponse->call("createTableHeader", "ip_requests", "ip-list".$row["refno"], $row["refno"], $charge_type);
				$req_flag=false;
				$serv_stat=false;
				$total_amount = 0;
				while($row2=$result->FetchRow())
				{
					switch(strtolower($row2["request_flag"]))
					{
						 case 'cmap':
								$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
								$req_flag=true;
								break;
						 case 'lingap':
								$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
								$req_flag=true;
								break;
						 case 'paid':
								$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
								$req_flag=true;
								break;
						 case 'charity':
								$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
								$req_flag=true;
								break;
						 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
					}

					if($row2["serve_status"] == 'S' OR $row2["serve_status"] == 'P')
					{ 
						$serv_stat=true ;	
					}

					$data = array(
						'refno'=>$row["refno"],
						'order_date'=>date('d-M-Y h:i: a',strtotime($row["orderdate"])),
						'status'=>$request_flag,
						'is_served'=>$row2["serve_status"],
						'item_name'=>$row2["artikelname"],
						'item_code'=>$row2["bestellnum"],
						'item_qty'=>$row2["quantity"],
						'item_prc'=>$row2["force_price"],
						'total_prc'=>parseFloatEx($row2["quantity"]*$row2["force_price"])
					);
					$objResponse->call("printRequestlist", "ip_requests", "ip-list".$row["refno"], $data);

					$total_amount+=parseFloatEx($row2["quantity"]*$row2["force_price"]);

					//$objResponse->alert("req-".$serv_stat." ,notp-".$notPtype.", bill-".$is_bill_final.", status".$row["serve_status"]);
				}

				if(strtolower($charge_type)=="cash") {
					$total_cash+=parseFloatEx($total_amount);
				}
				else if(strtolower($charge_type)=="charge") {
					$total_charge+=parseFloatEx($total_amount);
				}

				//
				#editted by CELSY 08/25/10
					if($row["request_source"]==$ptype)
						$notPtype = false;
					else
						$notPtype = true;
					

				#Commented by GEnz
				//  if($req_flag==false && $notPtype==false && $is_bill_final==0) {
				// 	$buttons = '<button class="segButton" onclick="openEditRequest(\'ip_requests\',\''.$row["refno"].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
				// 						'<button class="segButton" onclick="openDeleteRequest(\'ip_requests\',\''.$row["refno"].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				//  }else
				//  {
				// 	$buttons = '<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
				// 						'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				// }
				#added by Genz
				if($serv_stat==true)
				{
					$buttons = '<button class="segButton" disabled="disabled" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
										'<button class="segButton" disabled="disabled" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
				}else{
					$buttons = '<button class="segButton" onclick="openEditRequest(\'ip_requests\',\''.$row["refno"].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" onclick="openDeleteRequest(\'ip_requests\',\''.$row["refno"].'\');return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				}
				$objResponse->assign("btn-".$row["refno"],"innerHTML", $buttons);
			}
		}
		$objResponse->assign("ip-total-cash", "innerHTML", number_format($total_cash,2));
		$objResponse->assign("ip-total-charge", "innerHTML", number_format($total_charge,2));
	}
	return $objResponse;
}

function populateMgRequests($encounter_nr, $ptype, $is_bill_final, $date)
{
	global $db;
	$objResponse = new xajaxResponse();
	$order_obj = new SegOrder();
	$filters = array('inpatient'=>$encounter_nr,'area'=>'MG', 'date'=>$date);
	$res = $order_obj->getActiveOrders($filters, 0, 10);
	//$objResponse->alert("Mg order\n".$order_obj->sql);
	$objResponse->assign("mg_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if($res!==FALSE) {
		while($row=$res->FetchRow())
		{
			$result = $order_obj->getOrderItemsFullInfo($row["refno"],'');
			$charge_type = $row["is_cash"]==0?'Charge':'Cash';
			if($result!==FALSE) {
				$objResponse->call("createTableHeader", "mg_requests", "mg-list".$row["refno"], $row["refno"], $charge_type);
				$req_flag=false;
				$total_amount = 0;
				while($row2=$result->FetchRow())
				{
					switch(strtolower($row2["request_flag"]))
					{
						 case 'cmap':
								$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
								$req_flag=true;
								break;
						 case 'lingap':
								$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
								$req_flag=true;
								break;
						 case 'paid':
								$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
								$req_flag=true;
								break;
						 case 'charity':
								$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
								$req_flag=true;
								break;
						 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
					}

					$data = array(
						'refno'=>$row["refno"],
						'order_date'=>date('d-M-Y h:i: a',strtotime($row["orderdate"])),
						'status'=>$request_flag,
						'is_served'=>$row2["serve_status"],
						'item_name'=>$row2["artikelname"],
						'item_code'=>$row2["bestellnum"],
						'item_qty'=>$row2["quantity"],
						'item_prc'=>$row2["force_price"],
						'total_prc'=>parseFloatEx($row2["quantity"]*$row2["force_price"])
					);
					$objResponse->call("printRequestlist", "mg_requests", "mg-list".$row["refno"], $data);

					$total_amount+=parseFloatEx($row2["quantity"]*$row2["force_price"]);
				}

				if(strtolower($charge_type)=="cash") {
					$total_cash+=parseFloatEx($total_amount);
				}
				else if(strtolower($charge_type)=="charge") {
					$total_charge+=parseFloatEx($total_amount);
				}
				#editted by CELSY 08/25/10
					if($row ["request_source"]==$ptype)
						$notPtype = false;
					else
						$notPtype = true;

					if($req_flag==false && $notPtype==false && $is_bill_final==0) {
					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'mg_requests\',\''.$row["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'mg_requests\',\''.$row["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				}else {

					$buttons = '<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				}

//				if($req_flag==true)
//				{
//					$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
//										'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
//				}else {
//					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'mg_requests\',\''.$row["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
//										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'mg_requests\',\''.$row["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
//				}
				$objResponse->assign("btn-".$row["refno"],"innerHTML", $buttons);
			}
		}
		$objResponse->assign("mg-total-cash", "innerHTML", number_format($total_cash,2));
		$objResponse->assign("mg-total-charge", "innerHTML", number_format($total_charge,2));
	}
	return $objResponse;
}

function populateSpLabRequests($encounter_nr, $pid, $ptype, $is_bill_final, $date)
{
	global $db;
	$objResponse = new xajaxResponse();
	$lab_obj = new SegLab();

	//get lab refno
	$sql = "SELECT refno, IF(is_cash='0','Charge','Cash') AS `charge_type`, source_req FROM seg_lab_serv WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND (ref_source='SPL') AND status <> 'deleted'".
				" AND DATE(serv_dt)=DATE(".$db->qstr(date("Y-m-d",strtotime($date))).")".
				" ORDER BY serv_dt, serv_tm, refno DESC ";

	$result = $db->Execute($sql);
	//$objResponse->alert("refno\n".$sql);
	$objResponse->assign("splab_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if($result!==FALSE){
		while($ref = $result->FetchRow())
		{
			 $sql2 = "SELECT CONCAT(serv_dt,' ',serv_tm) AS serv_dt, encounter_nr, s.name AS request_item,\n".
						" s.service_code, d.price_cash, d.price_charge, d.quantity, r.ref_source, \n".
						" d.request_flag, d.is_served \n".
						" FROM seg_lab_serv AS r \n".
						" INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno \n".
						" INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code \n".
						" WHERE r.pid=".$db->qstr($pid)." AND r.encounter_nr=".$db->qstr($encounter_nr).
						" AND r.refno=".$db->qstr($ref["refno"])." AND d.status <> 'deleted' ORDER BY s.name ASC ";
			 $res = $db->Execute($sql2);
			 if($res!==FALSE){
					$objResponse->call("createTableHeader", "splab_requests", "splab-list".$ref["refno"], $ref["refno"], $ref["charge_type"]);
					$req_flag=false;
					$total_amount = 0;
					while($row=$res->FetchRow())
					{
						switch(strtolower($row["request_flag"]))
						{
							 case 'cmap':
									$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
									$req_flag=true;
									break;
							 case 'lingap':
									$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
									$req_flag=true;
									break;
							 case 'paid':
									$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
									$req_flag=true;
									break;
							 case 'charity':
									$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
									$req_flag=true;
									break;
							 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
						}

						$data = array(
							'refno'=>$ref["refno"],
							'order_date'=>date('d-M-Y h:i: a',strtotime($row["serv_dt"]." ".$row["serv_tm"])),
							'status'=>$request_flag,
							'is_served'=>$row["is_served"],
							'item_name'=>$row["request_item"],
							'item_code'=>$row["service_code"],
							'item_qty'=>$row["quantity"],
							'item_prc'=>$row["price_cash"],
							'total_prc'=>parseFloatEx($row["quantity"]*$row["price_cash"])
						);
						$objResponse->call("printRequestlist", "splab_requests", "splab-list".$ref["refno"], $data);

						$total_amount+=parseFloatEx($row["quantity"]*$row["price_cash"]);
					}

					if(strtolower($ref["charge_type"])=="cash") {
						$total_cash+=parseFloatEx($total_amount);
					}
					else if(strtolower($ref["charge_type"])=="charge") {
						$total_charge+=parseFloatEx($total_amount);
					}

					#editted by CELSY 08/25/10
					if($ref["source_req"]==$ptype)
						$notPtype = false;
					else
						$notPtype = true;
					//$objResponse->alert($notPtype." weh\n".$ref["source_req"]."\nptype\n".$ptype);

					if($req_flag==false && $notPtype==false && $is_bill_final==0) {
					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'splab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'splab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
					 }else
					 {
						$buttons = '<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
											'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
					 }
//				 if($req_flag==true)
//				 {
//					$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
//										'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
//				 }else {
//					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'splab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
//										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'splab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
//				 }
				 $objResponse->assign("btn-".$ref["refno"],"innerHTML", $buttons);
			 }
		}
		$objResponse->assign("splab-total-cash", "innerHTML", number_format($total_cash,2));
		$objResponse->assign("splab-total-charge", "innerHTML", number_format($total_charge,2));
	}
	return $objResponse;
}


function populateLabRequests($encounter_nr,$pid, $ptype, $is_bill_final, $date)
{
	global $db;
	$objResponse = new xajaxResponse();
	$lab_obj = new SegLab();

	//get lab refno
	$sql = "SELECT refno, IF(is_cash='0','Charge','Cash') AS `charge_type`, source_req FROM seg_lab_serv WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND (ref_source='LB') AND status <> 'deleted'".
				" AND DATE(serv_dt)=DATE(".$db->qstr(date("Y-m-d",strtotime($date))).")".
				" ORDER BY serv_dt, serv_tm, refno DESC ";

	$result = $db->Execute($sql);
	//$objResponse->alert("refno\n".$sql);
	$objResponse->assign("lab_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if($result!==FALSE){
		while($ref = $result->FetchRow())
		{
			 $sql2 = "SELECT CONCAT(serv_dt,' ',serv_tm) AS serv_dt, encounter_nr, s.name AS request_item,\n".
						" s.service_code, d.price_cash, d.price_charge, d.quantity, r.ref_source, \n".
						" d.request_flag, d.is_served \n".
						" FROM seg_lab_serv AS r \n".
						" INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno \n".
						" INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code \n".
						" WHERE r.pid=".$db->qstr($pid)." AND r.encounter_nr=".$db->qstr($encounter_nr).
						" AND r.refno=".$db->qstr($ref["refno"])." AND d.status <> 'deleted' ORDER BY s.name ASC ";
			 $res = $db->Execute($sql2);
			 if($res!==FALSE){
					$objResponse->call("createTableHeader", "lab_requests", "lab-list".$ref["refno"], $ref["refno"], $ref['charge_type']);
					$req_flag=false;
					$total_amount = 0;
					while($row=$res->FetchRow())
					{
						switch(strtolower($row["request_flag"]))
						{
							 case 'cmap':
									$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
									$req_flag=true;
									break;
							 case 'lingap':
									$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
									$req_flag=true;
									break;
							 case 'paid':
									$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
									$req_flag=true;
									break;
							 case 'charity':
									$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
									$req_flag=true;
									break;
							 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
						}

						$data = array(
							'refno'=>$ref["refno"],
							'order_date'=>date('d-M-Y h:i: a',strtotime($row["serv_dt"]." ".$row["serv_tm"])),
							'status'=>$request_flag,
							'is_served'=>$row["is_served"],
							'item_name'=>$row["request_item"],
							'item_code'=>$row["service_code"],
							'item_qty'=>$row["quantity"],
							'item_prc'=>$row["price_cash"],
							'total_prc'=>parseFloatEx($row["quantity"]*$row["price_cash"])
						);
						$objResponse->call("printRequestlist", "lab_requests", "lab-list".$ref["refno"], $data);

						$total_amount+=parseFloatEx($row["quantity"]*$row["price_cash"]);
					}

					if(strtolower($ref["charge_type"])=="cash") {
						$total_cash+=parseFloatEx($total_amount);
					}
					else if(strtolower($ref["charge_type"])=="charge") {
						$total_charge+=parseFloatEx($total_amount);
					}
					#editted by CELSY 08/25/10
					if($ref["source_req"]==$ptype)
						$notPtype = false;
					else
						$notPtype = true;

					if($req_flag==false && $notPtype==false && $is_bill_final==0) {
						$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'lab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'lab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				 }else
				 {   $buttons = '<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				 }

//				 if($req_flag==true)
//				 {
//					$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
//										'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
//				 }else {
//					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'lab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
//										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'lab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
//				 }
				 $objResponse->assign("btn-".$ref["refno"],"innerHTML", $buttons);
			 }
		}
		$objResponse->assign("lab-total-cash", "innerHTML", number_format($total_cash,2));
		$objResponse->assign("lab-total-charge", "innerHTML", number_format($total_charge,2));
	}
	return $objResponse;
}

function populateICLabRequests($encounter_nr,$pid, $ptype, $is_bill_final, $date)
{
	global $db;
	$objResponse = new xajaxResponse();
	$lab_obj = new SegLab();

	//get lab refno
	$sql = "SELECT refno, IF(is_cash='0','Charge','Cash') AS `charge_type`, source_req FROM seg_lab_serv WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND (ref_source='IC') AND status <> 'deleted'".
				" AND DATE(serv_dt)=DATE(".$db->qstr(date("Y-m-d",strtotime($date))).")".
				" ORDER BY serv_dt, serv_tm, refno DESC ";

	$result = $db->Execute($sql);
	//$objResponse->alert("refno\n".$sql);
	$objResponse->assign("iclab_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if($result!==FALSE){
		while($ref = $result->FetchRow())
		{
			 $sql2 = "SELECT CONCAT(serv_dt,' ',serv_tm) AS serv_dt, encounter_nr, s.name AS request_item,\n".
						" s.service_code, d.price_cash, d.price_charge, d.quantity, r.ref_source, \n".
						" d.request_flag, d.is_served \n".
						" FROM seg_lab_serv AS r \n".
						" INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno \n".
						" INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code \n".
						" WHERE r.pid=".$db->qstr($pid)." AND r.encounter_nr=".$db->qstr($encounter_nr).
						" AND r.refno=".$db->qstr($ref["refno"])." AND d.status <> 'deleted' ORDER BY s.name ASC ";
			 $res = $db->Execute($sql2);
			 if($res!==FALSE){
					$objResponse->call("createTableHeader", "iclab_requests", "iclab-list".$ref["refno"], $ref["refno"], $ref['charge_type']);
					$req_flag=false;
					$total_amount = 0;
					while($row=$res->FetchRow())
					{
						switch(strtolower($row["request_flag"]))
						{
							 case 'cmap':
									$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
									$req_flag=true;
									break;
							 case 'lingap':
									$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
									$req_flag=true;
									break;
							 case 'paid':
									$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
									$req_flag=true;
									break;
							 case 'charity':
									$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
									$req_flag=true;
									break;
							 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
						}

						$data = array(
							'refno'=>$ref["refno"],
							'order_date'=>date('d-M-Y h:i: a',strtotime($row["serv_dt"]." ".$row["serv_tm"])),
							'status'=>$request_flag,
							'is_served'=>$row["is_served"],
							'item_name'=>$row["request_item"],
							'item_code'=>$row["service_code"],
							'item_qty'=>$row["quantity"],
							'item_prc'=>$row["price_cash"],
							'total_prc'=>parseFloatEx($row["quantity"]*$row["price_cash"])
						);
						$objResponse->call("printRequestlist", "iclab_requests", "iclab-list".$ref["refno"], $data);

						$total_amount+=parseFloatEx($row["quantity"]*$row["price_cash"]);
					}

					if(strtolower($ref["charge_type"])=="cash") {
						$total_cash+=parseFloatEx($total_amount);
					}
					else if(strtolower($ref["charge_type"])=="charge") {
						$total_charge+=parseFloatEx($total_amount);
					}
					#editted by CELSY 08/25/10
					if($ref["source_req"]==$ptype)
						$notPtype = false;
					else
						$notPtype = true;

					if($req_flag==false && $notPtype==false && $is_bill_final==0) {
						$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'iclab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'iclab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				 }else
				 {   $buttons = '<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				 }

//				 if($req_flag==true)
//				 {
//					$buttons = '<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
//										'<button class="segButton" onclick="return false;" style="cursor: pointer;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
//				 }else {
//					$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'lab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
//										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'lab_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
//				 }
				 $objResponse->assign("btn-".$ref["refno"],"innerHTML", $buttons);
			 }
		}
		$objResponse->assign("iclab-total-cash", "innerHTML", number_format($total_cash,2));
		$objResponse->assign("iclab-total-charge", "innerHTML", number_format($total_charge,2));
	}
	return $objResponse;
}

function populateBloodRequests($encounter_nr,$pid,$ptype, $is_bill_final, $date)
{
	global $db;
	$objResponse = new xajaxResponse();
	$lab_obj = new SegLab();

	//get lab refno
	$sql = "SELECT refno, IF(is_cash='0','Charge','Cash') AS `charge_type`, source_req FROM seg_lab_serv WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND (ref_source='BB') AND status <> 'deleted'".
				" AND DATE(serv_dt)=DATE(".$db->qstr(date("Y-m-d",strtotime($date))).")".
				" ORDER BY serv_dt, serv_tm, refno DESC ";

	$result = $db->Execute($sql);
	//$objResponse->alert("refno\n".$sql);
	$objResponse->assign("blood_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if($result!==FALSE){
		while($ref = $result->FetchRow())
		{
			 $sql2 = "SELECT CONCAT(serv_dt,' ',serv_tm) AS serv_dt, encounter_nr, s.name AS request_item,\n".
						" s.service_code, d.price_cash, d.price_charge, d.quantity, r.ref_source, \n".
						" d.request_flag, d.is_served \n".
						" FROM seg_lab_serv AS r \n".
						" INNER JOIN seg_lab_servdetails AS d ON d.refno=r.refno \n".
						" INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code \n".
						" WHERE r.pid=".$db->qstr($pid)." AND r.encounter_nr=".$db->qstr($encounter_nr).
						" AND r.refno=".$db->qstr($ref["refno"])." AND d.status <> 'deleted' ORDER BY s.name ASC ";
			 $res = $db->Execute($sql2);
			// $objResponse->alert("refno\n".$sql2);
			 if($res!==FALSE){
					$objResponse->call("createTableHeader", "blood_requests", "blood-list".$ref["refno"], $ref["refno"], $ref["charge_type"]);
					$req_flag=false;
					$total_amount = 0;
					while($row=$res->FetchRow())
					{
						switch(strtolower($row["request_flag"]))
						{
							 case 'cmap':
									$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
									$req_flag=true;
									break;
							 case 'lingap':
									$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
									$req_flag=true;
									break;
							 case 'paid':
									$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
									$req_flag=true;
									break;
							 case 'charity':
									$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
									$req_flag=true;
									break;
							 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
						}

						$data = array(
							'refno'=>$ref["refno"],
							'order_date'=>date('d-M-Y h:i: a',strtotime($row["serv_dt"]." ".$row["serv_tm"])),
							'status'=>$request_flag,
							'is_served'=>$row["is_served"],
							'item_name'=>$row["request_item"],
							'item_code'=>$row["service_code"],
							'item_qty'=>$row["quantity"],
							'item_prc'=>$row["price_cash"],
							'total_prc'=>parseFloatEx($row["quantity"]*$row["price_cash"])
						);
						$objResponse->call("printRequestlist", "blood_requests", "blood-list".$ref["refno"], $data);

						$total_amount+=parseFloatEx($row["quantity"]*$row["price_cash"]);
					}

					if(strtolower($ref["charge_type"])=="cash") {
						$total_cash+=parseFloatEx($total_amount);
					}
					else if(strtolower($ref["charge_type"])=="charge") {
						$total_charge+=parseFloatEx($total_amount);
					}

					#editted by CELSY 08/25/10
					if($ref["source_req"]==$ptype)
						$notPtype = false;
					else
						$notPtype = true;

					if($req_flag==false && $notPtype==false && $is_bill_final==0) {
						$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'blood_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'blood_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				 }else
				 {   $buttons = '<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
										'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
				 }
//					if($req_flag==true)
//					{
//						$buttons = '<button class="segButton" style="cursor: pointer;" onclick="return false;"><img src="../../gui/img/common/default/page_edit.png" style="opacity:0.4;" disabled=""/>Edit</button>'.
//											'<button class="segButton" style="cursor: pointer;" onclick="return false;"><img src="../../gui/img/common/default/cancel.png" style="opacity:0.4;" disabled=""/>Delete</button>';
//					}else {
//						$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'blood_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
//											'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'blood_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
//					}
					$objResponse->assign("btn-".$ref["refno"],"innerHTML", $buttons);
			 }
		}
		$objResponse->assign("blood-total-cash", "innerHTML", number_format($total_cash,2));
		$objResponse->assign("blood-total-charge", "innerHTML", number_format($total_charge,2));
	}
	return $objResponse;
}

function populateRadioRequests($encounter_nr, $pid, $ptype, $is_bill_final, $date)
{
	global $db;
	$objResponse = new xajaxResponse();
	#edited by CELSY 8/25/10
	//get radio refno
	/*$sql = "SELECT refno, IF(is_cash='0','Charge','Cash') AS `charge_type` FROM seg_radio_serv WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND status <> 'deleted' ".
				" AND DATE(request_date)=DATE(NOW())".
				" ORDER BY request_date, request_time DESC ";*/
	$sql = "SELECT refno, IF(is_cash='0','Charge','Cash') AS `charge_type`, source_req FROM seg_radio_serv WHERE encounter_nr=".$db->qstr($encounter_nr).
				" AND pid=".$db->qstr($pid)." AND status <> 'deleted' ".
				" AND DATE(request_date)=DATE(".$db->qstr(date("Y-m-d",strtotime($date))).")".
				" ORDER BY request_date, request_time, refno DESC ";
	$result = $db->Execute($sql);
	//$objResponse->alert("refno\n".$sql);
	$objResponse->assign("radio_requests", "innerHTML", "");
	$total_cash = 0;
	$total_charge = 0;
	if($result!==FALSE){
		while($ref = $result->FetchRow())
		{
			 $sql2 = "SELECT CONCAT(r.request_date,' ',r.request_time) as `orderdate`, rd.service_code, s.name,\n".
						" rd.price_cash, rd.price_charge, 1 as `quantity`, rd.request_flag, \n".
						" EXISTS(SELECT f.batch_nr FROM care_test_findings_radio AS f WHERE f.batch_nr=rd.batch_nr) as `has_result`\n".
						" FROM seg_radio_serv AS r \n".
						" INNER JOIN care_test_request_radio AS rd ON r.refno=rd.refno \n".
						" INNER JOIN seg_radio_services AS s ON s.service_code=rd.service_code \n".
						" WHERE r.pid=".$db->qstr($pid)." AND r.encounter_nr=".$db->qstr($encounter_nr).
						" AND r.refno=".$db->qstr($ref["refno"])." AND rd.status <> 'deleted' ORDER BY s.name ASC ";
			 $res = $db->Execute($sql2);
			 if($res!==FALSE){
					$objResponse->call("createTableHeader", "radio_requests", "radio-list".$ref["refno"], $ref["refno"], $ref["charge_type"]);
					$req_flag=false;
					$total_amount = 0;
					while($row=$res->FetchRow())
					{
						switch(strtolower($row["request_flag"]))
						{
							 case 'cmap':
									$request_flag = '<img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/>';
									$req_flag=true;
									break;
							 case 'lingap':
									$request_flag = '<img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/>';
									$req_flag=true;
									break;
							 case 'paid':
									$request_flag = '<img src="../../images/flag_paid.gif" title="Item paid"/>';
									$req_flag=true;
									break;
							 case 'charity':
									$request_flag = '<img src="../../images/charity_item.gif" title="Item charged to CHARITY"/>';
									$req_flag=true;
									break;
							 default: $request_flag = '<img src="../../gui/img/common/default/accept.png" title="Ready to serve"/>'; $req_flag=false; break;
						}

						$data = array(
							'refno'=>$ref["refno"],
							'order_date'=>date('d-M-Y h:i: a',strtotime($row["request_date"]." ".$row["request_time"])),
							'status'=>$request_flag,
							'is_served'=>$row["has_result"],
							'item_name'=>$row["name"],
							'item_code'=>$row["service_code"],
							'item_qty'=>$row["quantity"],
							'item_prc'=>$row["price_cash"],
							'total_prc'=>parseFloatEx($row["quantity"]*$row["price_cash"])
						);
						$objResponse->call("printRequestlist", "radio_requests", "radio-list".$ref["refno"], $data);

						$total_amount+=parseFloatEx($row["quantity"]*$row["price_cash"]);
					}

					if(strtolower($ref["charge_type"])=="cash") {
						$total_cash+=parseFloatEx($total_amount);
					}
					else if(strtolower($ref["charge_type"])=="charge") {
						$total_charge+=parseFloatEx($total_amount);
					}
					if($ref["source_req"]==$ptype)
						$notPtype = false;
					else
						$notPtype = true;
					#editted by CELSY 08/25/10
					//if($req_flag==true)
					if($req_flag==false && $notPtype==false && $is_bill_final==0) {
						$buttons = '<button class="segButton" style="cursor: pointer;" onclick="openEditRequest(\'radio_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
											'<button class="segButton" style="cursor: pointer;" onclick="openDeleteRequest(\'radio_requests\',\''.$ref["refno"].'\');return false;"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
					}else
					{
						$buttons = '<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/page_edit.png"/>Edit</button>'.
											'<button class="segButton" disabled="disabled"><img src="../../gui/img/common/default/cancel.png"/>Delete</button>';
					}
					$objResponse->assign("btn-".$ref["refno"],"innerHTML", $buttons);
			 }

		}
		$objResponse->assign("radio-total-cash", "innerHTML", number_format($total_cash,2));
		$objResponse->assign("radio-total-charge", "innerHTML", number_format($total_charge,2));
	}
	return $objResponse;
}

function deleteRequest($refno)
{
	global $db;
	$srv=new SegLab;
	$objResponse = new xajaxResponse();

	$sql = "SELECT ref_no FROM seg_pay_request
				WHERE ref_source = 'LD' AND ref_no = '$refno'
				UNION
				SELECT refno FROM seg_lab_result
				WHERE refno = '$refno'";

	 $res=$db->Execute($sql);
	 $row=$res->RecordCount();

	if ($row==0){

		$status=$srv->deleteRequestor($refno);

		if ($status) {
			$srv->deleteLabServ_details($refno);
			$objResponse->alert("The request is successfully deleted.");
		}else
			$objResponse->call("showme", $srv->sql);
	 }else{
			$objResponse->alert("The request cannot be deleted. It is already or partially paid or it has a result already.");
	 }
	$objResponse->call("refreshPage");
	return $objResponse;
}

function deleteRadioServiceRequest($ref_nr)
{
	$objResponse = new xajaxResponse();
	$radio_obj = new SegRadio;

	if ($radio_obj->deleteRefNo($ref_nr)){
		$objResponse->alert("The request is successfully deleted.");
	}else{
		$objResponse->alert("The request cannot be deleted. It is already or partially paid or it has a result already.");
	}
	$objResponse->call("refreshPage");
	return $objResponse;
}

function deleteOrder($refno)
{
	global $db;
	$objResponse = new xajaxResponse();
	$oclass = new SegOrder();
	if ($oclass->deleteOrder($refno)) {
		$objResponse->alert("The request is successfully deleted.");
	}
	else {
		$objResponse->alert("The request cannot be deleted. It is already or partially paid or it has a result already.");
	}
	$objResponse->call("refreshPage");
	return $objResponse;
}

function deleteMiscRequest($refno)
{
	global $db;
	$objResponse = new xajaxResponse();
	$misc_obj = new SegOR_MiscCharges();
	$curl_obj = new Rest_Curl();
	
	if($saveok=$misc_obj->deleteMiscOrder($refno))
	{
		$curl_obj->inpatientMiscRequest($refno);
		$objResponse->alert("Miscellenous order successfully deleted.");
	}else {
		$objResponse->alert("Miscellenous order not successfully deleted.");
	}
	$objResponse->call("refreshPage");
	return $objResponse;
}

function computeTotalPayment($pid, $encounter_nr)
{
	global $db;
	$objResponse = new xajaxResponse();
	$sql = "SELECT
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=1 AND l.ref_source='LB' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `lab_total_cash`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=0 AND l.ref_source='LB' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `lab_total_charge`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=1 AND l.ref_source='IC' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `iclab_total_cash`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=0 AND l.ref_source='IC' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `iclab_total_charge`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=1 AND l.ref_source='BB' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `bb_total_cash`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=0 AND l.ref_source='BB' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `bb_total_charge`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=1 AND l.ref_source='SPL' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `splab_total_cash`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=0 AND l.ref_source='SPL' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `splab_total_charge`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=1 AND l.ref_source='IC' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `splab_total_cash`,
	(SELECT SUM(ld.price_cash*ld.quantity)
		FROM seg_lab_servdetails AS ld
		INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno
		WHERE l.pid='$pid' AND l.is_cash=0 AND l.ref_source='IC' AND DATE(l.serv_dt)=DATE(NOW()) AND l.status<>'deleted') AS `splab_total_charge`,
	(SELECT SUM(rd.price_cash)
		FROM care_test_request_radio AS rd
		INNER JOIN seg_radio_serv AS r ON rd.refno=r.refno
		WHERE r.pid='$pid' AND r.is_cash=1 AND DATE(r.request_date)=DATE(NOW()) AND r.status<>'deleted') AS `radio_total_cash`,
	(SELECT SUM(rd.price_cash)
		FROM care_test_request_radio AS rd
		INNER JOIN seg_radio_serv AS r ON rd.refno=r.refno
		WHERE r.pid='$pid' AND r.is_cash=0 AND DATE(r.request_date)=DATE(NOW()) AND r.status<>'deleted') AS `radio_total_charge`,
	(SELECT SUM(ph.pricecash*ph.quantity)
		FROM seg_pharma_order_items AS ph
		INNER JOIN seg_pharma_orders AS p ON ph.refno=p.refno
		WHERE p.pid='$pid' AND p.is_cash=1 AND p.pharma_area='IP' AND DATE(p.orderdate)=DATE(NOW()) ) AS `ip_total_cash`,
	(SELECT SUM(ph.pricecash*ph.quantity)
		FROM seg_pharma_order_items AS ph
		INNER JOIN seg_pharma_orders AS p ON ph.refno=p.refno
		WHERE p.pid='$pid' AND p.is_cash=0 AND p.pharma_area='IP' AND DATE(p.orderdate)=DATE(NOW()) ) AS `ip_total_charge`,
	(SELECT SUM(ph.pricecash*ph.quantity)
		FROM seg_pharma_order_items AS ph
		INNER JOIN seg_pharma_orders AS p ON ph.refno=p.refno
		WHERE p.pid='$pid' AND p.is_cash=1 AND p.pharma_area='MG' AND DATE(p.orderdate)=DATE(NOW()) ) AS `mg_total_cash`,
	(SELECT SUM(ph.pricecash*ph.quantity)
		FROM seg_pharma_order_items AS ph
		INNER JOIN seg_pharma_orders AS p ON ph.refno=p.refno
		WHERE p.pid='$pid' AND p.is_cash=0 AND p.pharma_area='MG' AND DATE(p.orderdate)=DATE(NOW()) ) AS `mg_total_charge`,
	(SELECT SUM(md.adjusted_amnt)
		FROM seg_misc_service_details AS md
		INNER JOIN seg_misc_service AS m ON m.refno=md.refno
		WHERE m.encounter_nr='$encounter_nr' AND m.is_cash=1 AND DATE(m.chrge_dte)=DATE(NOW()) ) AS `misc_total_cash`,
	(SELECT SUM(md.chrg_amnt*md.quantity)
		FROM seg_misc_service_details AS md
		INNER JOIN seg_misc_service AS m ON m.refno=md.refno
		WHERE m.encounter_nr='$encounter_nr' AND m.is_cash=0 AND DATE(m.chrge_dte)=DATE(NOW()) ) AS `misc_total_charge` ";
	//$objResponse->alert($sql);
	$result = $db->Execute($sql);
	$data = $result->FetchRow();

	$total_cash = parseFloatEx($data["lab_total_cash"]+$data["iclab_total_cash"]+$data["bb_total_cash"]+$data["splab_total_cash"]+$data["radio_total_cash"]+$data["ip_total_cash"]+$data["mg_total_cash"]+$data["misc_total_cash"]);
	$total_charge = parseFloatEx($data["lab_total_charge"]+$data["iclab_total_charge"]+$data["bb_total_charge"]+$data["splab_total_charge"]+$data["radio_total_charge"]+$data["ip_total_charge"]+$data["mg_total_charge"]+$data["misc_total_charge"]);

	$objResponse->assign("overall-total-cash", "innerHTML", number_format($total_cash,2));
	$objResponse->assign("overall-total-charge", "innerHTML", number_format($total_charge,2));

	return $objResponse;
}

/*
* Creted by Jarel
* Created on 11/10/2013
* Ajax function for auto tagging of patient, 
*/
function autoTagging($enc,$doc_nr,$or_no)
{
	global $db,$HTTP_SESSION_VARS;
	$objResponse = new xajaxResponse();
	$userid=$HTTP_SESSION_VARS['sess_user_name'];

    $fldarray = array('encounter_nr' => $db->qstr($enc),
    		'doctor_nr'    => $db->qstr($doc_nr),
            'or_no'  => $db->qstr($or_no),
            'modify_id' => $db->qstr($doc_nr),
            'create_id'    => $db->qstr($doc_nr),
            'create_time'    => 'NOW()',
            'history'    => "CONCAT('Create: ',NOW(),' [$userid]\\n')",
            'is_deleted' => '0'
           );

    $bsuccess = $db->Replace('seg_doctors_co_manage', $fldarray, array('encounter_nr', 'doctor_nr'));

	return $objResponse;
}

$xajax->processRequest();
?>
