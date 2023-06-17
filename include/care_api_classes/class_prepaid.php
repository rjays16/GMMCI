<?php
//Created by CHA, August 5, 2010
//Functions used for Prepaid feature - consultation only
	require_once($root_path.'include/care_api_classes/class_core.php');

	class SegPrepaid extends Core {
		var $tb_prepaid_consultation = 'seg_prepaid_consultation';
		var $fld_prepaid_consultation = array(
			'or_no',
			'pid',
			'paid_consultation',
			'used_consultation',
			'history',
			'last_transaction_date'
		);

		function SegPrepaid()
		{
			$this->coretable = $this->tb_prepaid_consultation;
			$this->ref_array = $this->fld_prepaid_consultation;
		}

		//function isPackage($service_code)
		function isConsultation($service_code)
		{
			global $db;
			$this->sql = "SELECT EXISTS(SELECT o.account_type FROM seg_other_services AS o \n".
								" INNER JOIN seg_cashier_account_subtypes as s ON o.account_type=s.type_id \n".
								" WHERE o.service_code=".$db->qstr($service_code)." \n".
								" AND s.name_long='Consultation Fees') AS `is_consultation`";
			if($is_consult = $db->GetOne($this->sql)) {
				return $is_consult;
			} else {
				return FALSE;
			}
		}

		function savePrepaidConsultation($orno, $pid, $max_usage)
		{
			global $db;
			$date = date('Y-m-d H:i:s');
			$history = "Create ".$date." [".$_SESSION["sess_temp_userid"]."]";
			$this->sql = "INSERT INTO seg_prepaid_consultation (or_no, pid, paid_consultation, used_consultation, history, \n".
								"last_transaction_date) VALUES (".$db->qstr($orno).", ".$db->qstr($pid).", ".$db->qstr($max_usage).", \n".
								"".$db->qstr($max_usage).", ".$db->qstr($history).", ".$db->qstr($date).")";
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return TRUE;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function clearPrepaidConsultation($orno, $pid)
		{
			global $db;
			$this->sql = "DELETE FROM seg_prepaid_consultation WHERE or_no=".$db->qstr($orno)." AND pid=".$db->qstr($pid);
			if($this->result=$db->Execute($this->sql)) {
				return TRUE;
			}else {
				return FALSE;
			}
		}

		function getMaxUsage($service_code)
		{
			global $db;
	//		$this->sql = "SELECT max_usage FROM seg_other_services WHERE service_code=".$db->qstr($service_code)." AND packageflag=1";
			$this->sql = "SELECT max_usage FROM seg_other_services WHERE service_code=".$db->qstr($service_code);
			if($usage = $db->GetOne($this->sql)) {
				return $usage;
			} else {
				return FALSE;
			}
		}

		function hasPrepaid($pid)
		{
			global $db;
			$this->sql = "SELECT IF(EXISTS(SELECT pc.or_no FROM seg_prepaid_consultation AS pc \n".
									"INNER JOIN seg_pay AS p ON pc.or_no=p.or_no \n".
									"WHERE pc.pid=".$db->qstr($pid)." \n".
									"AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00') \n".
									"AND pc.is_deleted=0 \n".
									"AND (pc.used_consultation!=0 OR DATE(pc.last_transaction_date)=DATE(NOW()) ) \n".
									"),1,0) AS `has_prepaid` ";
			if($has_prepaid=$db->GetOne($this->sql)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

		function hasPrepaidByOrno($orno)
		{
			global $db;
			$this->sql = "SELECT IF(EXISTS(SELECT pc.or_no FROM seg_prepaid_consultation AS pc \n".
									"INNER JOIN seg_pay AS p ON pc.or_no=p.or_no \n".
									"WHERE pc.or_no=".$db->qstr($orno)." \n".
									"),1,0) AS `has_prepaid` ";
			if($has_prepaid=$db->GetOne($this->sql)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

		function hasPrepaidUsedByOrno($orno)
		{
			global $db;
			$this->sql = "SELECT IF(EXISTS(SELECT pc.or_no FROM seg_prepaid_consultation AS pc \n".
									"INNER JOIN seg_pay AS p ON pc.or_no=p.or_no \n".
									"WHERE pc.or_no=".$db->qstr($orno)." AND (pc.used_consultation!=pc.paid_consultation)\n".
									"),1,0) AS `has_prepaid` ";
			if($has_prepaid=$db->GetOne($this->sql)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

		function getPrepConsultationDetails($pid)
		{
			global $db;
			$this->sql = "SELECT pc.* FROM seg_prepaid_consultation AS pc \n".
							"INNER JOIN seg_pay AS p ON p.or_no=pc.or_no WHERE p.pid=".$db->qstr($pid)." \n".
							"AND pc.used_consultation!=0 ORDER BY p.or_date DESC LIMIT 1 ";
			if($this->result=$db->Execute($this->sql)) {
				return $this->result->FetchRow();
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function updateUsedConsultation($pid)
		{
			global $db;
			$prepaid_data = $this->getPrepConsultationDetails($pid);
			if($prepaid_data['paid_consultation']==0) {
				return TRUE;
			}else {
				$used_consultation = ($prepaid_data['used_consultation'])-1;
				$history = $prepaid_data['history']."\n"."Update counter ".date("Y-m-d H:i:s")."[".$_SESSION['sess_temp_userid']."]";

				$this->sql = "UPDATE seg_prepaid_consultation SET used_consultation=".$db->qstr($used_consultation).", \n".
										"last_transaction_date=".$db->qstr(date("Y-m-d H:i:s")).", \n".
										"history=".$db->qstr($history)." \n".
										"WHERE pid=".$db->qstr($pid)." AND or_no=".$db->qstr($prepaid_data['or_no']);
				if($this->result=$db->Execute($this->sql)) {
					return TRUE;
				} else {
					$this->error_msg = $db->ErrorMsg();
					return FALSE;
				}
			}
		}

		function getPrepDetailsByOR($or_no)
		{
			global $db;
			$this->sql = "SELECT pc.* FROM seg_prepaid_consultation AS pc \n".
							"INNER JOIN seg_pay AS p ON p.or_no=pc.or_no WHERE p.or_no=".$db->qstr($or_no)." \n".
							"AND pc.used_consultation!=0 ORDER BY p.or_date DESC LIMIT 1 ";
			if($this->result=$db->Execute($this->sql)) {
				return $this->result->FetchRow();
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function cancelUpdateCounter($orno)
		{
			global $db;
			$prepaid_data = $this->getPrepDetailsByOR($orno);
			$used_consultation = ($prepaid_data['used_consultation'])+1;
			$history = $prepaid_data['history']."\n"."Rollback counter ".date("Y-m-d H:i:s")."[".$_SESSION['sess_temp_userid']."]";

			$this->sql = "UPDATE seg_prepaid_consultation SET used_consultation=".$db->qstr($used_consultation).", \n".
									"last_transaction_date=".$db->qstr(date("Y-m-d H:i:s")).", \n".
									"history=".$db->qstr($history)." \n".
									"WHERE pid=".$db->qstr($prepaid_data['pid'])." AND or_no=".$db->qstr($orno);
			if($this->result=$db->Execute($this->sql)) {
				return TRUE;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function cancelORPrepaid($orno)
		{
			global $db;
			$prepaid_data = $this->getPrepDetailsByOR($orno);
			$history = $prepaid_data['history']."\n"."OR Cancelled ".date("Y-m-d H:i:s")."[".$_SESSION['sess_temp_userid']."]";

			$this->sql = "UPDATE seg_prepaid_consultation SET is_deleted=1, history=".$db->qstr($history)." \n".
									"WHERE or_no=".$db->qstr($orno);
			if($this->result=$db->Execute($this->sql)) {
				return TRUE;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function unCancelORPrepaid($orno)
		{
			global $db;
			$prepaid_data = $this->getPrepDetailsByOR($orno);
			$history = $prepaid_data['history']."\n"."OR uncancelled ".date("Y-m-d H:i:s")."[".$_SESSION['sess_temp_userid']."]";

			$this->sql = "UPDATE seg_prepaid_consultation SET is_deleted=0, history=".$db->qstr($history)." \n".
									"WHERE or_no=".$db->qstr($orno);
			if($this->result=$db->Execute($this->sql)) {
				return TRUE;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function checkPrepaidBeforeUpdate($pid)
		{
			global $db;
			$this->sql = "SELECT \n".
			"	IF(EXISTS( \n".
			"		SELECT p.or_no FROM seg_pay AS p INNER JOIN seg_prepaid_consultation AS pc ON p.or_no=pc.or_no \n".
			"		WHERE pc.pid='$pid' AND (pc.used_consultation=pc.paid_consultation AND pc.used_consultation!=0)),1,0) as `not_used`, \n".
			"	IF(EXISTS( \n".
			"		SELECT p.or_no FROM seg_pay AS p INNER JOIN seg_prepaid_consultation AS pc ON p.or_no=pc.or_no \n".
			"		WHERE pc.pid='$pid' AND DATE(pc.last_transaction_date)=DATE(NOW())),1,0) as `valid_today`";
			if($this->result=$db->Execute($this->sql)) {
				return $this->result->FetchRow();
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

	}
?>
