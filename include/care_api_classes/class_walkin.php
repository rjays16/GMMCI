<?php

// Class for updating `seg_walkin` table.

require("./roots.php");  
require_once($root_path.'include/care_api_classes/class_core.php');

class SegWalkin extends Core {
  
  var $seg_discounts_tb = "seg_discounts";
  var $person_tb = "care_person";

  var $fld_walkin = array(
    "pid",
    "name_last",
    "name_first",
    "name_middle",
    "date_birth",
    "address",
    "history",
    "create_id",
    "create_time",
    "modify_id",
    "modify_time"
  );
  
  function SegWalkin() {
    $this->coretable = "seg_walkin";
    $this->setTable($this->coretable);
    $this->setRefArray($this->fld_walkin);
  }
  
  function createPID() {
    global $db;
    $today = $db->qstr($today);
    $this->sql="SELECT IFNULL(MAX(CAST(pid AS UNSIGNED)+1),1000) FROM $this->coretable";
    return $db->GetOne($this->sql);
  }
  
  function getWalkin( $filters ) {
    global $db;

    $key = is_array($filters) ? "" : $filters;
    $offset = 0;
    $rowcount = 15;
    $sortSQL = "name_last ASC";
    
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

    $this->sql = "SELECT w.pid,CONCAT(w.name_last,IF(LENGTH(IFNULL(w.name_first,''))=0,'',CONCAT(', ',name_first))) AS `fullname`,w.name_last,w.name_first,w.sex,w.address,last_transaction\n".
      "FROM seg_walkin AS w\n";
    $where = array();
    if ($lname) $where[] = "name_last REGEXP ".$db->qstr("[[:<:]]".$lname);
    if ($fname) {
      $or = array();
      foreach ($fname as $v) {
        $or[] = "name_first REGEXP ".$db->qstr("[[:<:]]".$v);
      }
      $where[] = "(".implode(") OR (",$or). ")";
    }
    
    if ($where) $this->sql.=" WHERE (".implode(") AND (",$where).")\n";
    $this->sql .= "ORDER BY $sortSQL\n" . 
      "LIMIT $offset, $rowcount";

    if ($result=$db->Execute($this->sql))
      return $result;
    else return FALSE;

  }
  
  function getWalkinByName($ln, $fn, $mn) {
    global $db;
    $ln = $db->qstr($ln);
    $fn = $db->qstr($fn);
    $mn = $db->qstr($mn);
    $this->sql = "SELECT pid,name_last,name_first,name_middle,date_birth,address FROM seg_walkin WHERE name_last=$ln AND name_first=$fn AND name_middle=$mn";
    return $db->GetRow($this->sql);
  }
  
  /*-----added by CHA 10-13-2009 -------*/
  function countWalkin($searchID)
    {
        global $db;
        //echo "class id=".$searchID;
        if($searchID=='*' || $searchID=='')
        {
              $this->sql="select name_last from seg_walkin where status not in ('deleted')";
        }
        else
        {
              $this->sql="select name_last from seg_walkin where name_last like '".$searchID."%' and status not in ('deleted')";        
        }
        if ($this->result=$db->Execute($this->sql)) {
            if ($this->count=$this->result->RecordCount()) { 
                return $this->result;
            }
            else{return FALSE;}
        }else{return FALSE;}
    }
    
    function getWalkinDetails($searchID, $multiple=0, $maxcount=100, $offset=0)
    {
        global $db;
        if(empty($maxcount)) $maxcount=100;
        if(empty($offset)) $offset=0;
        if($searchID=='*' || $searchID=='')
        {
              $this->sql="select pid, concat(name_last,', ', name_first) as name, address, create_time from seg_walkin where status not in ('deleted') order by name_last, name_first";
        }
        else
        {
              $this->sql="select pid, concat(name_last,', ', name_first) as name, address, create_time from seg_walkin where name_last like '".$searchID."%' and status not in ('deleted') order by name_last, name_first";        
        }
         
         if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset))
            {
                if($this->rec_count=$this->res['ssl']->RecordCount()) 
                {
                    return $this->res['ssl'];
                }
                else{ return false; }
            }
            else{ return false; }
    }
    function saveEditDetails($id,$lastname,$firstname,$gender,$address,$birthdate)
    {
      global $db;
      $sql="select history from seg_walkin where pid=".$db->qstr($id);
      $result=$db->Execute($sql);
      $row = $result->FetchRow();
      $new_history = $row['history']." Modify ".date('Y-m-d H:i:s')." ".$_SESSION['sess_temp_userid'];
      $this->sql="update seg_walkin set name_last=".$db->qstr($lastname).", name_first=".$db->qstr($firstname).
      						", sex=".$db->qstr($gender).", address=".$db->qstr($address).", date_birth=".$db->qstr($birthdate).
      						", history=".$db->qstr($new_history).
      						", modify_time='".date('Y-m-d H:i:s')."', modify_id='".$_SESSION['sess_temp_userid'].
      						"' where pid=".$db->qstr($id);
      #echo "edit query: ".$this->sql;
      #echo "user: ".$_SESSION['sess_temp_userid'];
      if($this->result=$db->Execute($this->sql))
      {
      	return true;
      }
      else{ return false;}
		}
		
		function saveAccountDetails($id,$lastname,$firstname,$gender,$address,$birthdate)
    {
        global $db;
        $history = "Create ".date('Y-m-d H:i:s')." ".$_SESSION['sess_temp_userid']; 
        $this->sql="insert into seg_walkin (pid, name_last, name_first, sex, date_birth, address, ".
        						"history, create_id, create_time, modify_id, modify_time) values (".$db->qstr($id).
        						", ".$db->qstr($lastname).", ".$db->qstr($firstname).", ".$db->qstr($gender).
        						", ".$db->qstr($birthdate).", ".$db->qstr($address).", ".$db->qstr($history).
        						", '".$_SESSION['sess_temp_userid']."', '".date('Y-m-d H:i:s').
        						"', '".$_SESSION['sess_temp_userid']."', '".date('Y-m-d H:i:s')."')";
        #echo "add query: ".$this->sql;
        if($this->result=$db->Execute($this->sql))
        {
          return true;
        }
        else{ return false;}
    }
    
    function deleteWalkin($delID)  
    {
        global $db;
        $sql="select history from seg_walkin where pid=".$db->qstr($delID);
	      $result=$db->Execute($sql);
	      $row = $result->FetchRow();
	      $new_history = $row['history']." Delete ".date('Y-m-d H:i:s')." ".$_SESSION['sess_temp_userid'];
        $this->sql="update seg_walkin set status='deleted', history=".$db->qstr($new_history).
        					",modify_id=".$db->qstr($_SESSION['sess_temp_userid']).", modify_time=".$db->qstr(date('Y-m-d H:i:s'))." where pid=".$db->qstr($delID);
        if($this->result=$db->Execute($this->sql))
        {
            return true;
        }
        else{ return false;}
    }
  /*----end CHA -------------------------*/
  
	//created by cha, 12-14-2010
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
							$cond[] = "w.pid=".$db->qstr($v);
						break;
						case 'name':
							if (strpos($v,',')!==false) {
								$split_name = explode(',', $v);
								$cond[] = "w.name_last LIKE ".$db->qstr(trim($split_name[0])."%");
								$cond[] = "w.name_first LIKE ".$db->qstr(trim($split_name[1])."%");
							}
							else {
								if ($v) {
									$cond[] = "w.name_last LIKE ".$db->qstr($v.'%')." OR w.name_first LIKE ".$db->qstr($v.'%');
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

			$cond[] = "w.status NOT IN ('deleted')";
			if($cond)
				$where = "WHERE (".implode(")\n AND (",$cond).")\n";

			$this->sql =
					"SELECT SQL_CALC_FOUND_ROWS w.pid, CONCAT(w.name_last, ', ', w.name_first, ' ', w.name_middle) `walkin_name`, \n".
					"w.name_last, w.name_first, w.name_middle, w.date_birth,\n".
					"w.address,  w.create_time, IF(w.sex='F', 'Female', IF(w.sex='M', 'Male', NULL)) `gender` \n".
					"FROM seg_walkin w \n".
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

		//created by cha, 12-14-2010
		function updateWalkinDetails($data)
		{
			global $db;

			$history = "\nUPDATE : ".date("Y-m-d H:ia")." [".$_SESSION['sess_temp_userid']."]";
			$this->sql = "UPDATE seg_walkin \n".
			"SET name_last=".$db->qstr($data['lastname']).", name_first=".$db->qstr($data['firstname']).", \n".
			"name_middle=".$db->qstr($data['middlename']).", date_birth=".$db->qstr($data['birthdate']).", \n".
			"address=".$db->qstr($data['address']).", sex=".$db->qstr($data['gender']).", \n".
			"history=CONCAT(history, ".$db->qstr($history)."), modify_time=NOW(), modify_id=".$db->qstr($_SESSION['sess_temp_userid'])." \n".
			"WHERE pid=".$db->qstr($data['id']);

			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return TRUE;
			} else return FALSE;

		}

		function deleteWalkinData($pid)
		{
			global $db;
			$history = "\nDELETED : ".date("Y-m-d H:ia")." [".$_SESSION['sess_temp_userid']."]";
			$this->sql = "UPDATE seg_walkin \n".
					"SET status='deleted', \n".
					"history=CONCAT(history, ".$db->qstr($history)."), modify_time=NOW(), modify_id=".$db->qstr($_SESSION['sess_temp_userid'])." \n".
					"WHERE pid=".$db->qstr($pid);
			$this->result = $db->Execute($this->sql);
			if($this->result!==FALSE) {
				return TRUE;
			}
		}

}

