<?php

/**
*  Class for handling CMAP walk-in registration and search
*
*/

require './roots.php';
require_once $root_path.'include/care_api_classes/class_core.php';

class CmapWalkin extends Core {

	var $seg_discounts_tb = "seg_discounts";

	public function __construct()
	{
		$this->coretable = "seg_walkin";
		$this->setTable($this->coretable, $fetch_metadata=true);
	}

	/**
	* put your comment there...
	*
	*/
	public function createId() {
		return create_guid();
	}


	/**
	* put your comment there...
	*
	* @param mixed $filters
	*/
	public function search( $filters ) {
		global $db;

		$key = is_array($filters) ? "" : $filters;
		$offset = 0;
		$rowcount = 10;
		$sortSQL = "lastname, firstname ASC";

		$where = array();
		if (is_array($filters)) {
			foreach ($filters as $i=>$v) {
				switch (strtolower($i)) {
					case 'key':
						$key=$v;
					break;
					case 'pid':
						$pid=$v;
					break;
					case 'offset':
						$offset=$v;
					break;
					case 'rowcount':
						$rowcount=$v;
					break;
					case 'sortsql':
						$sortSQL = $v;
					break;
				}
			}
		}

		$name_parts = explode(",", $key);
		$lname = trim($name_parts[0]);
		if ($name_parts[1])
			$fname = explode(" ",trim($name_parts[1]));

		$this->sql = "SELECT w.id,w.lastname,w.firstname,w.address\n".
			"FROM seg_cmap_walkin w\n";
		$where = array();
		if ($lname) $where[] = "lastname REGEXP ".$db->qstr("[[:<:]]".$lname);
		if ($fname) {
			$or = array();
			foreach ($fname as $v) {
				$or[] = "firstname REGEXP ".$db->qstr("[[:<:]]".$v);
			}
			$where[] = "(".implode(") OR (",$or). ")";
		}

		if ($where) $this->sql.=" WHERE (".implode(") AND (",$where).")\n";
		$this->sql .= "ORDER BY $sortSQL\n" .
			"LIMIT $offset, $rowcount";

		$db->SetFetchMode(ADODB_FETCH_ASSOC);
		if ($this->setResult($db->Execute($this->sql)) !== false)
		{
			$result = $this->getResult();
			return $result->GetRows();
		}
		else return FALSE;

	}

	function searchWalkin($filters)
		{
			global $db;
			$cond = array();
			if(is_array($filters)){
				foreach($filters as $i=>$v)
				{
					switch(strtolower($i))
					{
						case 'id':
							$cond[] = "w.id=".$db->qstr($v);
						break;
						case 'name':
							if (strpos($v,',')!==false) {
								$split_name = explode(',', $v);
								$cond[] = "w.lastname LIKE ".$db->qstr(trim($split_name[0])."%");
								$cond[] = "w.firstname LIKE ".$db->qstr(trim($split_name[1])."%");
							}
							else {
								if ($v) {
									$cond[] = "w.lastname LIKE ".$db->qstr($v.'%')." OR w.firstname LIKE ".$db->qstr($v.'%');
								}
							}
							break;
							case 'today':
											$cond[] = "DATE(w.create_time) = DATE(NOW())";
							break;
							case 'specific':
									$cond[] = "DATE(w.create_time) = '$v'";
							break;
							case 'week':
								$cond[] = 'YEAR(w.create_time)=YEAR(NOW()) AND WEEK(w.create_time)=WEEK(NOW())';
							break;
							break;
							case 'month':
								$cond[] = 'YEAR(w.create_time)=YEAR(NOW()) AND MONTH(w.create_time)=MONTH(NOW())';
							break;
							case 'between':
								$cond[] = "DATE(w.create_time) BETWEEN '".$v[0]."' AND '".$v[1]."'";
							break;
							case 'sort':
								$sort = $v;
							break;
							case 'offset':
								$offset = $v;
							break;
						case 'maxrows':
							$maxrows = $v;
						break;
					}
				}
			}

			$cond[] = "w.is_deleted=0";
			if($cond)
				$where = "WHERE (".implode(")\n AND (",$cond).")\n";

			$this->sql =
					"SELECT SQL_CALC_FOUND_ROWS w.id, CONCAT(w.lastname, ', ', w.firstname, ' ', w.middlename) `walkin_name`, \n".
					"w.lastname, w.firstname, w.middlename, w.birthdate,\n".
					"w.address, w.birthdate, w.create_time, IF(w.gender='F', 'Female', IF(w.gender='M', 'Male', NULL)) `gender` \n".
					"FROM seg_cmap_walkin w \n".
					$where;

			if($sort) {
				$this->sql.=" ORDER BY {$sort} LIMIT $offset, $maxrows ";
			}

			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return $this->result;
			} else {
				$this->error_msg = $db->ErrorMsg();
				return FALSE;
			}
		}

		function updateWalkinDetails($data)
		{
			global $db;

			$history = "\nUPDATE : ".date("Y-m-d H:ia")." [".$_SESSION['sess_temp_userid']."]";
			$this->sql = "UPDATE seg_cmap_walkin \n".
			"SET lastname=".$db->qstr($data['lastname']).", firstname=".$db->qstr($data['firstname']).", \n".
			"middlename=".$db->qstr($data['middlename']).", birthdate=".$db->qstr($data['birthdate']).", \n".
			"address=".$db->qstr($data['address']).", gender=".$db->qstr($data['gender']).", \n".
			"history=CONCAT(history, ".$db->qstr($history)."), modify_time=NOW(), modify_id=".$db->qstr($_SESSION['sess_temp_userid'])." \n".
			"WHERE id=".$db->qstr($data['id']);

			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return TRUE;
			} else return FALSE;

		}
}

