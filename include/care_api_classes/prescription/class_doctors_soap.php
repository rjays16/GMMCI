<?php
	require('./roots.php');
	require_once($root_path.'include/care_api_classes/class_core.php');

	class SegDoctorsSoap extends Core {

		var $tb_soap = "seg_doctors_soap";
		var $fld_soap = array (
			'id',
			'personell_nr',
			'pid',
			'soap',
			'note',
			'is_cancelled',
			'create_time',
			'create_id'
		);

		function SegDoctorsSoap()
		{
			$this->coretable = $this->tb_soap;
			$this->ref_array = $this->fld_soap;
		}

		function saveNote($data)
		{
			global $db;
			$this->setDataArray($data);
			return $this->insertDataFromInternalArray();
		}

		function getPersonellNr()
		{
			global $db;
			$this->sql = "SELECT cu.personell_nr FROM care_users AS cu\n".
				"WHERE cu.login_id=".$db->qstr($_SESSION["sess_temp_userid"]);
			$nr = $db->GetOne($this->sql);
			if($nr!==FALSE) {
				return $nr;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function getNotes($type, $pid)
		{
			global $db;
			$cond="";
			if(strtolower($type)!='all') {
				$cond = " AND soap=".$db->qstr(strtoupper($type))." \n";
			}

			$this->sql = "SELECT SQL_CALC_FOUND_ROWS d.id, d.note, d.create_time, d.is_cancelled, d.personell_nr \n".
									"FROM $this->tb_soap AS d \n".
									//"WHERE d.pid=".$db->qstr($pid)." AND d.personell_nr=".$db->qstr($doctor)." \n".
									"WHERE d.pid=".$db->qstr($pid)." \n".
									$cond." ORDER BY d.create_time DESC";
			$this->result = $db->Execute($this->sql);
			if($result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function deleteNote($id)
		{
			global $db;
			$this->sql = "UPDATE $this->tb_soap SET is_cancelled=1 WHERE id=".$db->qstr($id);
			$this->result = $db->Execute($this->sql);
			if($result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function undoDeleteNote($id)
		{
			global $db;
			$this->sql = "UPDATE $this->tb_soap SET is_cancelled=0 WHERE id=".$db->qstr($id);
			$this->result = $db->Execute($this->sql);
			if($result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function listPatientsDoctors($pid)
		{
			global $db;
			$this->sql = "SELECT DISTINCT d.personell_nr, p.pid, cp.name_last, cp.name_first, cp.name_middle\n".
				"FROM seg_doctors_soap AS d\n".
				"INNER JOIN care_personell AS p ON d.personell_nr=p.nr\n".
				"INNER JOIN care_person AS cp ON p.pid=cp.pid\n".
				"WHERE d.pid='$pid'\n".
				"ORDER BY name_last ASC";
			$this->result = $db->Execute($this->sql);
			if($result!==FALSE)
			{
				return $this->result;
			}
			else
			{
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function toggleNotes($type, $doctor, $pid)
		{
			global $db;
			$cond="";
			if(strtolower($type)!='all') {
				$cond = " AND soap=".$db->qstr(strtoupper($type))." \n";
			}

			$doctor_array = implode(',',$doctor);

			$this->sql = "SELECT SQL_CALC_FOUND_ROWS d.id, d.note, d.create_time, d.is_cancelled, d.personell_nr \n".
									"FROM $this->tb_soap AS d \n".
									"WHERE d.pid=".$db->qstr($pid)." AND d.personell_nr IN (".$doctor_array.") \n".
									$cond." ORDER BY d.create_time DESC";

			$this->result = $db->Execute($this->sql);
			if($result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function untoggleNotes($type, $doctor, $pid)
		{
			global $db;
			$cond="";
			if(strtolower($type)!='all') {
				$cond = " AND soap=".$db->qstr(strtoupper($type))." \n";
			}

			$doctor_array = implode(',',$doctor);

			$this->sql = "SELECT SQL_CALC_FOUND_ROWS d.id, d.note, d.create_time, d.is_cancelled, d.personell_nr \n".
									"FROM $this->tb_soap AS d \n".
									"WHERE d.pid=".$db->qstr($pid)." AND d.personell_nr NOT IN (".$doctor_array.") \n".
									$cond." ORDER BY d.create_time DESC";

			$this->result = $db->Execute($this->sql);
			if($result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

	}
?>
