<?php
	require('./roots.php');
	require_once($root_path.'include/care_api_classes/class_core.php');

	class SegRequestCancel extends Core {

		function SegRequestCancel()
		{

		}

		function getLaboratoryRequests($filters, $sort, $offset, $maxrows)
		{
			global $db;
			$cond = array();
			if(is_array($filters)){
				foreach($filters as $i=>$v)
				{
					switch($i)
					{
						case 'NAME':
							if (strpos($v,',')!==false) {
								$split_name = explode(',', $v);
								$cond[] = "cp.name_last LIKE ".$db->qstr(trim($split_name[0])."%").
												" AND cp.name_first LIKE ".$db->qstr(trim($split_name[1])."%");
							}
							else {
								if ($v) {
									$cond[] = "cp.name_last LIKE ".$db->qstr($v.'%')." OR w.name_last LIKE ".$db->qstr($v.'%');
								}
							}
							break;
						case 'PID';
							$cond[] = "l.pid=".$db->qstr($v);
							break;
						case 'CASENR':
							$cond[] = "l.encounter_nr=".$db->qstr($v);
							break;
					}
				}
			}

			$cond[] = "ld.status NOT IN ($this->dead_stat)";
			$cond[] = "l.status NOT IN ($this->dead_stat)";

			if($cond)
				$where = "WHERE (".implode(")\n AND (",$cond).")\n";

			$this->sql = "SELECT SQL_CALC_FOUND_ROWS \n".
									"(SELECT IF(b.bill_nr,1,0) FROM seg_billing_encounter AS b WHERE b.encounter_nr=l.encounter_nr) AS `is_billed`, \n".
									"CONCAT(l.serv_dt,' ',l.serv_tm) AS `request_date`, l.pid, l.refno, l.encounter_nr, \n".
									"fn_get_person_name(l.pid) AS `patient_name`, ls.name AS `item_name`, ld.request_flag, \n".
									"ld.status AS `request_status`, ld.service_code AS `item_code` \n".
									"FROM seg_lab_servdetails AS ld \n".
									"INNER JOIN seg_lab_serv AS l ON ld.refno=l.refno \n".
									"INNER JOIN seg_lab_services AS ls ON ld.service_code=ls.service_code \n".
									"INNER JOIN care_person AS cp ON cp.pid=l.pid \n".
									$where." ORDER BY {$sort} LIMIT $offset, $maxrows ";

			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function getRadiologyRequests($filters, $sort, $offset, $maxrows)
		{
			global $db;
			$cond = array();
			if(is_array($filters)){
				foreach($filters as $i=>$v)
				{
					switch($i)
					{
						case 'NAME':
							if (strpos($v,',')!==false) {
								$split_name = explode(',', $v);
								$cond[] = "cp.name_last LIKE ".$db->qstr(trim($split_name[0])."%").
												" AND cp.name_first LIKE ".$db->qstr(trim($split_name[1])."%");
							}
							else {
								if ($v) {
									$cond[] = "cp.name_last LIKE ".$db->qstr($v.'%')." OR w.name_last LIKE ".$db->qstr($v.'%');
								}
							}
							break;
						case 'PID';
							$cond[] = "r.pid=".$db->qstr($v);
							break;
						case 'CASENR':
							$cond[] = "r.encounter_nr=".$db->qstr($v);
							break;
					}
				}
			}

			$cond[] = "rd.status NOT IN ($this->dead_stat)";
			$cond[] = "r.status NOT IN ($this->dead_stat)";

			if($cond)
				$where = "WHERE (".implode(")\n AND (",$cond).")\n";

			$this->sql = "SELECT SQL_CALC_FOUND_ROWS \n".
									"(SELECT IF(b.bill_nr,1,0) FROM seg_billing_encounter AS b WHERE b.encounter_nr=r.encounter_nr) AS `is_billed`, \n".
									"CONCAT(r.request_date,' ',r.request_time) AS `request_date`, r.pid, r.refno, r.encounter_nr, \n".
									"fn_get_person_name(r.pid) AS `patient_name`, s.name AS `item_name`, rd.request_flag, \n".
									"rd.status AS `request_status`, rd.service_code AS `item_code`\n".
									"FROM care_test_request_radio AS rd \n".
									"LEFT JOIN seg_radio_schedule AS sk ON rd.batch_nr=sk.batch_nr \n".
									"INNER JOIN seg_radio_serv AS r ON r.refno=rd.refno \n".
									"INNER JOIN seg_radio_services AS s ON s.service_code=rd.service_code \n".
									"INNER JOIN care_person AS cp ON cp.pid=r.pid \n".
									$where." ORDER BY {$sort} LIMIT $offset, $maxrows ";

			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function getPharmacyRequests($filters, $sort, $offset, $maxrows)
		{
			global $db;
			$cond = array();
			if(is_array($filters)){
				foreach($filters as $i=>$v)
				{
					switch($i)
					{
						case 'NAME':
							if (strpos($v,',')!==false) {
								$split_name = explode(',', $v);
								$cond[] = "cp.name_last LIKE ".$db->qstr(trim($split_name[0])."%").
												" OR w.name_first LIKE ".$db->qstr(trim($split_name[1])."%");
								$cond[] = "w.name_last LIKE ".$db->qstr(trim($split_name[0])."%").
												" OR cp.name_first LIKE ".$db->qstr(trim($split_name[1])."%");
							}
							else {
								if ($v) {
									$cond[] = "cp.name_last LIKE ".$db->qstr($v.'%')." OR w.name_last LIKE ".$db->qstr($v.'%');
								}
							}
							break;
						case 'PID';
							$cond[] = "p.pid=".$db->qstr($v);
							break;
						case 'CASENR':
							$cond[] = "p.encounter_nr=".$db->qstr($v);
							break;
					}
				}
			}

			if($cond)
				$where = "WHERE (".implode(")\n AND (",$cond).")\n";

			$this->sql = "SELECT SQL_CALC_FOUND_ROWS \n".
									"(SELECT IF(b.bill_nr,1,0) FROM seg_billing_encounter AS b WHERE b.encounter_nr=p.encounter_nr) AS `is_billed`, \n".
									"p.orderdate AS `request_date`, IF(ISNULL(p.pid),w.pid,p.pid) AS `pid`, p.refno, ph.bestellnum AS `item_code`, p.encounter_nr, \n".
									"fn_get_person_name(IFNULL(p.pid,CONCAT('W',p.walkin_pid))) AS `patient_name`,  \n".
									"pd.artikelname AS `item_name`, ph.request_flag, \n".
									"IF(ph.serve_status='S','serve',IF(ph.serve_status='N','not served','pending')) AS `request_status` \n".
									"FROM seg_pharma_orders AS p \n".
									"INNER JOIN seg_pharma_order_items AS ph ON p.refno=ph.refno \n".
									"INNER JOIN care_pharma_products_main AS pd ON pd.bestellnum=ph.bestellnum \n".
									"LEFT JOIN care_person AS cp ON cp.pid=p.pid \n".
									"LEFT JOIN seg_walkin AS w ON p.walkin_pid=w.pid\n".
									$where." ORDER BY {$sort} LIMIT $offset, $maxrows ";

			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function getMiscellaneousRequests($filters, $sort, $offset, $maxrows)
		{
			global $db;
			$cond = array();
			if(is_array($filters)){
				foreach($filters as $i=>$v)
				{
					switch($i)
					{
						case 'NAME':
							if (strpos($v,',')!==false) {
								$split_name = explode(',', $v);
								$cond[] = "cp.name_last LIKE ".$db->qstr(trim($split_name[0])."%").
												" AND cp.name_first LIKE ".$db->qstr(trim($split_name[1])."%");
							}
							else {
								if ($v) {
									$cond[] = "cp.name_last LIKE ".$db->qstr($v.'%');
								}
							}
							break;
						case 'PID';
							$cond[] = "cp.pid=".$db->qstr($v);
							break;
						case 'CASENR':
							$cond[] = "m.encounter_nr=".$db->qstr($v);
							break;
					}
				}
			}

			if($cond)
				$where = "WHERE (".implode(")\n AND (",$cond).")\n";

			$this->sql = "SELECT SQL_CALC_FOUND_ROWS \n".
									"(SELECT IF(b.bill_nr,1,0) FROM seg_billing_encounter AS b WHERE b.encounter_nr=m.encounter_nr) AS `is_billed`, \n".
									"m.chrge_dte AS `request_date`, ce.pid, m.refno, md.service_code AS `item_code`, m.encounter_nr, \n".
									"fn_get_person_name(ce.pid) AS `patient_name`, ot.name AS `item_name`, md.request_flag, \n".
									"'none' AS `request_status` \n".
									"FROM seg_misc_service AS m \n".
									"INNER JOIN seg_misc_service_details AS md ON m.refno=md.refno \n".
									"INNER JOIN seg_other_services AS ot ON ot.alt_service_code=md.service_code \n".
									"INNER JOIN care_encounter AS ce ON ce.encounter_nr=m.encounter_nr \n".
									"INNER JOIN care_person AS cp ON cp.pid=ce.pid \n".
									$where." ORDER BY {$sort} LIMIT $offset, $maxrows ";

			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function cancelLabRequestFlag($refno, $item_code, $reason)
		{
			global $db, $HTTP_SESSION_VARS;
            
                        #added by VAS 07-04-2012
                        #add history for update
                        $history = $this->ConcatHistory("Update request_flag [NULL] -request cancellation ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
			$this->sql = "UPDATE seg_lab_servdetails SET 
                                      request_flag=NULL, 
                                      history = $history,
                                      cancel_reason=".$db->qstr($reason)." \n".
									"WHERE refno=".$db->qstr($refno)." AND service_code=".$db->qstr($item_code);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function cancelRadioRequestFlag($refno, $item_code, $reason)
		{
			global $db;
			$this->sql = "UPDATE care_test_request_radio SET request_flag=NULL, cancel_reason=".$db->qstr($reason)." \n".
									"WHERE refno=".$db->qstr($refno)." AND service_code=".$db->qstr($item_code);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function cancelPharmaRequestFlag($refno, $item_code, $reason)
		{
			global $db;
			$this->sql = "UPDATE seg_pharma_order_items SET request_flag=NULL, cancel_reason=".$db->qstr($reason)." \n".
									"WHERE refno=".$db->qstr($refno)." AND bestellnum=".$db->qstr($item_code);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function cancelMiscRequestFlag($refno, $item_code, $reason)
		{
			global $db;
			$this->sql = "UPDATE seg_misc_service_details SET request_flag=NULL, cancel_reason=".$db->qstr($reason)." \n".
									"WHERE refno=".$db->qstr($refno)." AND service_code=".$db->qstr($item_code);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function cancelLabRequestStatus($refno, $item_code, $reason)
		{
			global $db;
			$this->sql = "UPDATE seg_lab_servdetails SET status='pending', is_served=0, cancel_reason=".$db->qstr($reason)." \n".
									"WHERE refno=".$db->qstr($refno)." AND service_code=".$db->qstr($item_code);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function cancelRadioRequestStatus($refno, $item_code, $reason)
		{
			global $db;
			$this->sql = "UPDATE care_test_request_radio SET status='pending', cancel_reason=".$db->qstr($reason)." \n".
									"WHERE refno=".$db->qstr($refno)." AND service_code=".$db->qstr($item_code);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function cancelPharmaRequestStatus($refno, $item_code, $reason)
		{
			global $db;
			$this->sql = "UPDATE seg_pharma_order_items SET serve_status='N', cancel_reason=".$db->qstr($reason)." \n".
									"WHERE refno=".$db->qstr($refno)." AND bestellnum=".$db->qstr($item_code);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function deleteLabRequestItem($refno, $item_code, $reason)
		{
			global $db;
			$this->sql = "UPDATE seg_lab_servdetails SET status='deleted', cancel_reason=".$db->qstr($reason)." \n".
									"WHERE refno=".$db->qstr($refno)." AND service_code=".$db->qstr($item_code);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function deleteRadioRequestItem($refno, $item_code, $reason)
		{
			global $db;
			$this->sql = "UPDATE care_test_request_radio SET status='deleted', cancel_reason=".$db->qstr($reason)." \n".
									"WHERE refno=".$db->qstr($refno)." AND service_code=".$db->qstr($item_code);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function deletePharmaRequestItem($refno, $item_code)
		{
			global $db;
			$this->sql = "DELETE FROM seg_pharma_order_items \n".
									"WHERE refno=".$db->qstr($refno)." AND bestellnum=".$db->qstr($item_code);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function deleteMiscRequestItem($refno, $item_code)
		{
			global $db;
			$this->sql = "DELETE FROM seg_misc_service_details \n".
									"WHERE refno=".$db->qstr($refno)." AND service_code=".$db->qstr($item_code);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

	}
?>
