<?php
	require('./roots.php');
	require_once($root_path.'include/care_api_classes/class_core.php');
	require($root_path.'include/care_api_classes/class_request_source.php');

	class SegOR_MiscCharges extends Core{

		var $tb_misc = "seg_misc_service";
		var $tb_misc_details = "seg_misc_service_details";

		function SegOR_MiscCharges()
		{

		}

		function saveMiscCharges($details)
		{
			global $db;
			$db->StartTrans();
			extract($details);   
			$no_error = false;
			if ((count($misc) > 0) && (count($misc)==count($quantity))) {
				 if ($this->addMiscOrder($encounter_nr, $pid, $discountid, $discount, $charge_date, $area, $is_cash)!==FALSE) {
					 $no_error = $this->addMiscOrderItemsByBulk(array('misc'=>$misc, 'quantity'=>$quantity,  'adj_amnt'=>$adj_amnt, 'price'=>$price, 'account_type'=>$account_type, 'refno'=>$refno ,'clinical_info'=>$clinical_info, 'request_flag'=>$request_flag));
				 }else $no_error = FALSE;
			}else $no_error = FALSE;

			 if ($no_error) {
				 $db->CompleteTrans();
				 return TRUE;
			 }
			 else {
				 $db->FailTrans();
				 $this->error_msg = $db->ErrorMsg();
				 return FALSE;
			 }
		}

		function updateMiscCharges($details)
		{
			 global $db;
			 $db->StartTrans();
			 extract($details);         
			 $no_error = false;
			 if ($refno) { //edit
				 if ($no_error = $this->updateMiscOrder($refno,$is_cash,$discount,$discountid)) {
					 if ($no_error = $this->deleteMiscOrderItems($refno)) {
						 if ((count($misc) > 0) && (count($misc)==count($quantity))) {
							 $no_error = $this->addMiscOrderItemsByBulk(array('misc'=>$misc, 'quantity'=>$quantity, 'adj_amnt'=>$adj_amnt, 'price'=>$price, 'account_type'=>$account_type, 'refno'=>$refno ,'clinical_info'=>$clinical_info, 'request_flag'=>$request_flag));
						 }
						 else $db->FailTrans();
					 }
					 else{
						 $db->FailTrans();
						 $no_error = false;
					 }
				 }else $no_error = false;
			 }

			 if ($no_error) {
				 $db->CompleteTrans();
				 return TRUE;
			 }
			 else {
				 $db->FailTrans();
				 return FALSE;
			 }
		}

		function saveMiscellaneous($details)
		{
			 global $db;
			 $db->StartTrans();
			 extract($details);
			 $refno = $this->getExistingRefno($encounter_nr,$area);
			 $no_error = false;
			 if(count($misc)==0)
			 {
					$saveok = $this->deleteMiscOrder($refno);
					if ($saveok)
					{
						$db->CompleteTrans();
						return true;
					}else
					{
						 $db->FailTrans();
						 return false;
					}
			 }else
			 {
					if ($refno) { //edit
					 if ($no_error = $this->updateMiscOrder($refno,$is_cash)) {
						 if ($no_error = $this->deleteMiscOrderItems($refno)) {
							 if ($no_error = (count($misc) > 0) && (count($misc)==count($quantity))) {
								 $no_error = $this->addMiscOrderItemsByBulk(array('misc'=>$misc, 'quantity'=>$quantity,  'adj_amnt'=>$adj_amnt, 'price'=>$price, 'account_type'=>$account_type, 'refno'=>$refno));
							 }
							 else $db->FailTrans();
						 }
						 else{
							 $db->FailTrans();
							 $no_error = false;
						 }
					 }else $no_error = false;
				 }
				 else { //new entry
					 if ($no_error = (count($misc) > 0) && (count($misc)==count($quantity))) {
						 if ($refno = $this->addMiscOrder($encounter_nr, $charge_date, $area, $is_cash)) {
							 $no_error = $this->addMiscOrderItemsByBulk(array('misc'=>$misc, 'quantity'=>$quantity,  'adj_amnt'=>$adj_amnt, 'price'=>$price, 'account_type'=>$account_type, 'refno'=>$refno));
						 }else $no_error = false;
					 }else $no_error = false;
				 }
				 if ($no_error) {
					 $db->CompleteTrans();
					 return true;
				 }
				 else {
					 $db->FailTrans();
					 return false;
				 }
			 }
		}//end of saveMiscCharges

		//function getMiscRefno($encounter_nr, $area)
		function getMiscRefno($charge_date)
		{
			global $db;
			$this->sql = "SELECT fn_get_new_refno_misc_srvc(".$db->qstr($charge_date).")";
			$refno = $db->GetOne($this->sql);
			if($refno!==FALSE) {
				return $refno;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}//end of getMiscRefno

		function getExistingRefno($encounter_nr, $area)
		{
			global $db;
			 $this->sql = "SELECT refno FROM seg_misc_service WHERE encounter_nr='$encounter_nr' AND area='$area'";

			$this->result = $db->Execute($this->sql);
			if ($this->result!==FALSE) {
				$row = $this->result->FetchRow();
				return $row['refno'];
			}
			else {
				return 0;
			}
		}

		function deleteMiscOrder($refno)
		{
			 global $db;
			 $this->sql = "DELETE FROM seg_misc_service WHERE refno=".$db->qstr($refno);
			 if($db->Execute($this->sql)) {
				 return true;
			 }
			 else {
				 $this->error_msg = $db->ErrorMsg();
				 return false;
			 }
	 }//end of deleteMiscOrder

	 function updateMiscOrder($refno,$is_cash,$discount,$discountid)
	 {
			 global $db;
//			 $author = $_SESSION['sess_user_name'];  // Fix for HISSPMC-299
             $author = $_SESSION['sess_temp_userid'];   
				 $this->sql = "UPDATE seg_misc_service SET modify_id='$author', modify_dt=NOW(), is_cash='$is_cash', discount='$discount', discountid='$discountid' WHERE refno = '$refno'";
				 if($db->Execute($this->sql)){
					return true;
				 }
				 else {
					$this->error_msg = $db->ErrorMsg();
					return false;
				}
	 }//end of updateMiscOrder

	 function deleteMiscOrderItems($refno)
	 {
			 global $db;
			 $this->sql = "DELETE FROM seg_misc_service_details WHERE refno='$refno' AND ISNULL(request_flag)";
			 if ($db->Execute($this->sql)) {
					return true;
			 }
			 else {
				 $this->error_msg = $db->ErrorMsg();
				 return false;
			 }
	 }//end of deleteMiscOrderItems

	 function addMiscOrderItemsByBulk($details)
	 {
			 global $db;
			 extract($details);
			 $order_items = array();
			 foreach ($misc as $key => $misc_value) {
				$items_array = array($misc_value, $account_type[$key], $adj_amnt[$key], $price[$key], $quantity[$key], $clinical_info[$key], $request_flag[$key]);
				$order_items[] = $items_array;
			 }

			 $index = 'refno, service_code, account_type, adjusted_amnt, chrg_amnt, quantity, clinical_info, request_flag';
			 $values = "'$refno', ?, ?, ?, ?, ?, ?, ?";

			 $this->sql = "INSERT INTO seg_misc_service_details ($index) VALUES ($values)";

			$result = $db->Execute($this->sql, $order_items);
			if ($result===FALSE) {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
			else {
				return TRUE;
			}
	 }//end of addMiscOrderItemsByBulk

	 //function addMiscOrder($encounter_nr, $charge_date, $area, $is_cash)
	 function addMiscOrder($encounter_nr, $pid, $discountid, $discount, $charge_date, $area, $is_cash)
	 {
			 global $db;
//			 $author = $_SESSION['sess_user_name'];  // Fix for HISSPMC-299
             $author = $_SESSION['sess_temp_userid'];
			 #----------added by CELSY 8/25/10-----------                              
			 $req_src_obj = new SegRequestSource();
			 if($area=='ipd') {
					$request_source = $req_src_obj->getSourceIPDClinics();
				} else if($area=='er') {
					$request_source = $req_src_obj->getSourceERClinics();
				} else if($area=='opd') {
					$request_source = $req_src_obj->getSourceOPDClinics();
				} else if($area=='phs') {
					$request_source = $req_src_obj->getSourcePHSClinics();
				} else if($area=='nursing' || $area=='WD') {
					$request_source = $req_src_obj->getSourceNursingWard();
				} else if(($area=='ic') || ($area=='iclab')) {
					$request_source = $req_src_obj->getSourceIndustrialClinic();
				} else if($area=='bb') {
					$request_source = $req_src_obj->getSourceBloodBank();
				} else if($area=='spl') {
					$request_source = $req_src_obj->getSourceSpecialLab();
				} else if($area=='or' || $area=='OR') {
					$request_source = $req_src_obj->getSourceOR();
				} else if($area=='rdu' || $area=='dialysis' || $area=='DIALYSIS' || $area=='rd') {
					$request_source = $req_src_obj->getSourceDialysis();
				} else if($area=='doctor') {
					$request_source = $req_src_obj->getSourceDoctor();
				} else if($area=='ip') {
					$request_source = $req_src_obj->getSourceInpatientPharmacy();
				} else if($area=='mg') {
					$request_source = $req_src_obj->getSourceMurangGamot();
				} else{
					$request_source = $req_src_obj->getSourceLaboratory();
				}
									 
			 $refno = $db->GetOne("SELECT fn_get_new_refno_misc_srvc(".$db->qstr($charge_date).")");
			 $this->sql = "INSERT INTO seg_misc_service(refno, chrge_dte, encounter_nr, pid, discountid, discount, modify_id, modify_dt, create_id, create_dt, area, is_cash, request_source) VALUES
						 ('$refno', '$charge_date', '$encounter_nr', '$pid', '$discountid', '$discount',  '$author', NOW(), '$author', NOW(), '$area', '$is_cash', '$request_source')";
//		   $this->sql = "INSERT INTO seg_misc_service(chrge_dte, encounter_nr, modify_id, modify_dt, create_id, create_dt, area, is_cash) VALUES
//						 ('$charge_date', '$encounter_nr',  '$author', NOW(), '$author', NOW(), '$area', '$is_cash')";

			 
			 #-----------------end CELSY-----------------
			 if ($result = $db->Execute($this->sql)) {
				return $refno;
			 }
			 else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			 }
	 }//end of addMiscOrder

	 function getMiscOrderItems($encounter_nr)
	 {
			 global $db;

			 $this->sql = "(SELECT /*1 as source,*/ smc.refno, smcd.request_flag, smc.encounter_nr, s.is_!socialized, smc.area, s.name, t.name_short, smcd.adjusted_amnt AS net_price, smcd.chrg_amnt, s.alt_service_code AS code, smcd.quantity, smcd.account_type,smc.create_id, smc.modify_id, smc.chrge_dte
					FROM seg_misc_service_details smcd INNER JOIN seg_misc_service smc ON (smc.refno = smcd.refno)
					INNER JOIN seg_other_services AS s ON (s.alt_service_code = smcd.service_code)
					LEFT JOIN seg_cashier_account_subtypes AS t ON (s.account_type=t.type_id)
					LEFT JOIN seg_cashier_account_types AS p ON (t.parent_type=p.type_id) WHERE smc.encounter_nr='$encounter_nr')";

			 if ($result = $db->Execute($this->sql)) {
				return $result;
			 }
			 else {
				$this->error_msg = $db->ErrorMsg();
				return false;
			 }
	 }//end of getMiscOrderItems

	 function getMiscOrderItemsByRefno($refno)
	 {
			 global $db;
			//edited by: ian villanueva
			 $this->sql = "(SELECT /*1 as source,*/ smc.refno, s.`is_not_socialized`, smcd.request_flag, smc.encounter_nr, smc.area, s.name, t.name_short, smcd.adjusted_amnt AS net_price, smcd.chrg_amnt, s.alt_service_code AS code, smcd.quantity, smcd.account_type,smc.create_id, smc.modify_id, smc.chrge_dte, smc.is_cash
					FROM seg_misc_service_details smcd INNER JOIN seg_misc_service smc ON (smc.refno = smcd.refno)
					INNER JOIN seg_other_services AS s ON (s.alt_service_code = smcd.service_code)
					LEFT JOIN seg_cashier_account_subtypes AS t ON (s.account_type=t.type_id)
					LEFT JOIN seg_cashier_account_types AS p ON (t.parent_type=p.type_id) WHERE smc.refno='$refno')";
			 if ($result = $db->Execute($this->sql)) {
				return $result;
			 }
			 else {
				 $this->error_msg = $db->ErrorMsg();
				 return false;
			 }
	 }//end of getMiscOrderItemsByRefno

	  function getOrderInfo($refno) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql="SELECT o.*, 
			 o.chrge_dte AS orderdate, fn_get_person_name(o.pid) AS ordername,fn_get_complete_address(o.pid) AS orderaddress
			 FROM $this->tb_misc AS o 
			 LEFT JOIN care_person AS p ON p.pid=o.pid 
			 WHERE o.refno=$refno";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function getOrderItemsFullInfo($refno){
		global $db;
		$refno = $db->qstr($refno);
		$this->sql="SELECT
				  smcd.service_code,
				  s.name,
				  smcd.quantity,
				  smcd.chrg_amnt,
				  smcd.adjusted_amnt
				FROM
				  seg_misc_service_details AS smcd 
				  LEFT JOIN seg_other_services AS s 
				    ON s.alt_service_code = smcd.service_code 
				    WHERE smcd.`refno` = $refno ";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }		    
	}

	function get_create_person($refno) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql="SELECT cu.name\n".
			"FROM seg_misc_service AS spo\n".
			"INNER JOIN care_users AS cu ON spo.create_id = cu.login_id\n".
			"WHERE spo.refno = $refno\n";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}


	function get_modify_person($refno) {
		global $db;
		$refno = $db->qstr($refno);
		$this->sql="SELECT cu.name\n".
			"FROM seg_misc_service AS spo\n".
			"INNER JOIN care_users AS cu ON spo.modify_id = cu.login_id\n".
			"WHERE spo.refno = $refno\n";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function getinsurance($pids) {
		global $db;
		$pid = $db->qstr($pids);
		$this->sql="SELECT c.`firm_id`,c.`hcare_id` \n".
			"FROM  care_encounter a \n".
			"INNER JOIN seg_encounter_insurance b ON a.`encounter_nr` =  b.`encounter_nr`\n".
			"INNER JOIN care_insurance_firm c ON b.`hcare_id` = c.`hcare_id`\n".
			"WHERE a.`pid` =  $pid  AND a.`is_discharged` <> 1\n";	
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	function getpatientinfo($pids) {
		global $db;
		$pid = $db->qstr($pids);
		$this->sql="SELECT ce.`current_room_nr` as room,\n".
			"p.`age` AS age,\n".
			"p.`sex` AS sex,\n".
			"p.`civil_status` AS civil\n".
			"FROM care_person AS p\n".
			"LEFT JOIN care_encounter AS ce ON p.pid=ce.pid\n".
			"WHERE p.pid=$pid";
		if($this->result=$db->Execute($this->sql)) {
			return $this->result;
		} else { return false; }
	}

	}// end of class SegOR_miscCharges
?>
