<?php
	require('./roots.php');
	require_once($root_path.'include/care_api_classes/class_core.php');
	require_once($root_path.'include/care_api_classes/class_encounter.php');

	class SegDialysis extends Core {

		var $tb_dialysis = "seg_dialysis_transaction";
		var $fld_dialysis =
			array (
				'refno',
				'encounter_nr',
				'pid',
				'transaction_date',
				'status',
				'is_deleted',
				'requesting_doctor',
				'attending_nurse',
				'dialysis_type',
				'remarks',
				'reason',
				'create_id',
				'create_date',
				'modify_id',
				'modify_date'
			);

		function SegDialysis()
		{
			$this->useDialysis();
		}

		function useDialysis()
		{
			$this->coretable = $this->tb_dialysis;
			$this->ref_array = $this->fld_dialysis;
		}

		function getNewRefno()
		{
			global $db;
			$ref_nr = date('Y').'000001';
			$temp_ref_nr = date('Y')."%";   # NOTE : XXXX?????? would be the format of Reference number
				$row=array();
				$this->sql="SELECT refno FROM $this->tb_dialysis WHERE refno LIKE '$temp_ref_nr' ORDER BY refno DESC";
				if($this->res['gnpn']=$db->SelectLimit($this->sql,1)){
						if($this->res['gnpn']->RecordCount()){
								$row=$this->res['gnpn']->FetchRow();
								return $row['refno']+1;
						}else{/*echo $this->sql.'no count';*/return $ref_nr;}
				}else{/*echo $this->sql.'no sql';*/return $ref_nr;}
		}

		function saveTransaction($enc_data, $dialysis_data, $new_enc)
		{
			global $db;
			$db->StartTrans();
			$enc_obj = new Encounter($new_enc);
			//save to encounter first
			$enc_obj->setDataArray($enc_data);
			if($saveok = $enc_obj->insertDataFromInternalArray()) {
				if($saveok = $enc_obj->update_Encounter_Tracker($new_enc,"dialysis")) {
					//save to dialysis
					$this->setDataArray($dialysis_data);
					if($saveok = $this->insertDataFromInternalArray()) {
						$db->CompleteTrans();
						return TRUE;
					}
				}else {
				 $db->FailTrans();
				 $this->error_msg = $db->ErrorMsg();
				 return FALSE;
				}
			}else {
				$db->FailTrans();
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function getDialysisPersonell($location_type_nr, $role)
		{
			global $db;

			if($location_type_nr) {
				$cond_location = " pa.location_type_nr=".$db->qstr($location_type_nr)." AND \n";
			}
			if($role) {
				$cond_role = " rp.role=".$db->qstr($role)." AND \n";
			}
			$this->sql = "SELECT pa.nr, pa.personell_nr, ps.job_function_title,rp.role,ps.job_position,ps.license_nr, \n".
											"ps.tin, p.name_last, p.name_first, p.name_2, p.name_middle, p.date_birth, p.sex \n".
									"FROM care_personell AS ps \n".
									"LEFT JOIN care_personell_assignment AS pa ON pa.personell_nr=ps.nr \n".
									"LEFT JOIN care_role_person AS rp ON pa.role_nr=rp.nr \n".
									"LEFT JOIN care_person AS p ON ps.pid=p.pid \n".
									"WHERE \n".
											$cond_location.
											$cond_role.
											"(pa.date_end='0000-00-00' OR pa.date_end>='2010-07-20') \n".
											"AND pa.status NOT IN ('deleted','hidden','inactive','void') \n".
									"ORDER BY p.name_last, p.name_first, p.name_middle";
			if($this->result = $db->Execute($this->sql)) {
				if($this->result->RecordCount()>0) {
					return $this->result;
				}
			}else {
				return FALSE;
			}
		}

		function updateTransactionStatus($refno, $status, $reason, $encounter_nr)
		{
			global $db;
			$db->StartTrans();
			$history = $db->GetOne("SELECT history FROM care_encounter WHERE encounter_nr=".$db->qstr($encounter_nr));

			switch($status)
			{
				case "0": $status_char="UNDONE"; break;
				case "1": $status_char="DONE"; break;
			}
			$this->sql = "UPDATE $this->tb_dialysis SET status=".$db->qstr($status_char).", reason=".$db->qstr($reason).
								", modify_id=".$db->qstr($_SESSION["sess_temp_userid"]).", modify_date=NOW() ".
								"WHERE refno=".$db->qstr($refno);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE){
				if($status=="0") {
					$new_history = $history."\n"."Cancelled ".date('Y-m-d H:i:s')."=".$_SESSION["sess_user_name"];
					$this->sql = "UPDATE care_encounter SET encounter_status='cancelled', is_discharged='0', \n".
										"discharge_date='', discharge_time='', history=".$db->qstr($new_history).", \n".
										"modify_id=".$db->qstr($_SESSION["sess_user_name"]).", modify_time=NOW() ".
										"WHERE encounter_nr=".$db->qstr($encounter_nr);
				}
				else {
					$new_history = $history."\n"."Updated ".date('Y-m-d H:i:s')."=".$_SESSION["sess_user_name"];
					$this->sql = "UPDATE care_encounter SET encounter_status='', is_discharged='1', discharge_date=".$db->qstr(date('Y-m-d')).
									", discharge_time=".$db->qstr(date('H:i:s'))." , history=".$db->qstr($new_history).", \n".
									"modify_id=".$db->qstr($_SESSION["sess_user_name"]).", modify_time=NOW() ".
									"WHERE encounter_nr=".$db->qstr($encounter_nr);
				}
				$this->result=$db->Execute($this->sql);
				if($this->result!==FALSE) {
				 $db->CompleteTrans();
				 return TRUE;
				}
				else {
				 $db->FailTrans();
				 $this->error_msg = $db->ErrorMsg();
				 return FALSE;
			 }
			}else {
				$db->FailTrans();
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function getTransactionDetails($pid, $enc_nr, $refno)
		{
			global $db;
			$this->sql = "SELECT dt.*, fn_get_person_name(dt.pid) AS `patient_name` \n".
						"FROM seg_dialysis_transaction AS dt \n".
						"INNER JOIN care_encounter AS ce ON dt.encounter_nr=ce.encounter_nr \n".
						"INNER JOIN care_person AS cp ON ce.pid=cp.pid \n".
						"WHERE ce.is_discharged!='1' AND dt.refno=".$db->qstr($refno)." AND \n".
						"dt.encounter_nr=".$db->qstr($enc_nr)." AND dt.pid=".$db->qstr($pid);
			$details = $db->GetRow($this->sql);
			if($details!==FALSE) {
				return $details;
			}else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function updateTransactionDetails($refno, $data)
		{
			global $db;
			$this->sql = "UPDATE $this->tb_dialysis SET encounter_nr=".$db->qstr($data["encounter_nr"]).", ".
				"pid=".$db->qstr($data["pid"]).", transaction_date=".$db->qstr($data["transaction_date"]).", ".
				"status=".$db->qstr($data["status"]).", ".
				"requesting_doctor=".$db->qstr($data["requesting_doctor"]).", attending_nurse=".$db->qstr($data["attending_nurse"]).", ".
				"dialysis_type=".$db->qstr($data["dialysis_type"]).", remarks=".$db->qstr($data["remarks"]).", ".
				"reason=".$db->qstr($data["reason"]).", modify_id=".$db->qstr($_SESSION["sess_temp_userid"]).", ".
				"modify_date=NOW() WHERE refno=".$db->qstr($refno);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return TRUE;
			}else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function requestChecker($encounter_nr)
		{
			global $db;
			$this->sql = "SELECT \n".
				"EXISTS(SELECT refno FROM seg_lab_serv WHERE encounter_nr=".$db->qstr($encounter_nr)." AND status <> 'deleted') AS `lab`, \n".
				"EXISTS(SELECT refno FROM seg_radio_serv WHERE encounter_nr=".$db->qstr($encounter_nr)."  AND status <> 'deleted') AS `radio`, \n".
				"EXISTS(SELECT refno FROM seg_pharma_orders WHERE encounter_nr=".$db->qstr($encounter_nr)." ) AS `pharma`, \n".
				"EXISTS(SELECT refno FROM seg_misc_service WHERE encounter_nr=".$db->qstr($encounter_nr)." ) AS `misc` ";
			$data = $db->GetRow($this->sql);
			$exists = FALSE;
			if($data["lab"]==1) {
				$exists = TRUE;
			}
			else if($data["pharma"]==1) {
				$exists = TRUE;
			}
			else if($data["radio"]==1) {
				$exists = TRUE;
			}
			else if($data["misc"]==1) {
				$exists = TRUE;
			}

			return $exists;
		}

		function deleteTransactionDetails($refno, $enc_nr)
		{
			global $db;
			$db->StartTrans();
			$this->sql = "UPDATE $this->tb_dialysis SET is_deleted='1', modify_id=".$db->qstr($_SESSION["sess_temp_userid"]).
							", modify_date=NOW() WHERE refno=".$db->qstr($refno);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				$history = $db->GetOne("SELECT history FROM care_encounter WHERE encounter_nr=".$db->qstr($enc_nr));
				$new_history = $history."\n"."Cancelled ".date('Y-m-d H:i:s')."=".$_SESSION["sess_user_name"];
				$this->sql = "UPDATE care_encounter SET encounter_status='cancelled', is_discharged='0', \n".
										"discharge_date='', discharge_time='', history=".$db->qstr($new_history).", \n".
										"modify_id=".$db->qstr($_SESSION["sess_user_name"]).", modify_time=NOW() ".
										"WHERE encounter_nr=".$db->qstr($enc_nr);
				$this->result = $db->Execute($this->sql);
				if($this->result!==FALSE) {
					$saveok=true;
				} else { $saveok=false; }
			}else { $saveok=false; }

			if($saveok) {
				$db->CompleteTrans();
				return TRUE;
			}else {
				$db->FailTrans();
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function getTransactionByPid($pid)
		{
			global $db;
			$this->sql = "SELECT dt.*, fn_get_person_name(dt.pid) AS `patient_name` \n".
						"FROM seg_dialysis_transaction AS dt \n".
						"INNER JOIN care_encounter AS ce ON dt.encounter_nr=ce.encounter_nr \n".
						"INNER JOIN care_person AS cp ON ce.pid=cp.pid \n".
						"WHERE ce.is_discharged!='1' AND dt.pid=".$db->qstr($pid);
			$details = $db->GetRow($this->sql);
			if($details!==FALSE) {
				return $details;
			}else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

}
?>
