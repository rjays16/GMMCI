<?php
	// created by cha on 05-21-09
	//class for editing cashier OR numbers

	require_once($root_path.'include/care_api_classes/class_core.php');

	class SegCashierEditOR extends Core
	{
		var $pay_tb = "seg_pay";
		var $fld_pay = array(
			"or_no",
			"account_type",
			"cancel_date",
			"cancelled_by",
			"or_date",
			"or_name",
			"or_address",
			"encounter_nr",
			"pid",
			"amount_tendered",
			"amount_due",
			"remarks",
			"history",
			"create_id",
			"create_dt",
			"modify_id",
			"modify_dt"
		);
		var $or_array=array();

		function SegCashierEditOR()
		{
			$this->coretable = $this->pay_tb;
			$this->res = $this->or_array;
		}

		function returnOrArray()
		{
			return $this->or_array;
		}

		function getORDetails($from_ORNo, $to_ORNo)
		{
			global $db;
			$cnt_or=0;
			$cnt=0;
			$checker=0;
			$temp_or_array=array();
			$this->sql="SELECT or_no,or_name,or_date,cancel_date,cancelled_by,create_id from $this->pay_tb WHERE CAST(or_no AS UNSIGNED) BETWEEN ".((int)$from_ORNo)." AND ".((int)$to_ORNo)." ORDER BY or_no ASC LIMIT 50";
			$orno_array=array();
			$orname_array=array();
			$ordate_array=array();
			$canceldt_array=array();
			$cancelledby_array=array();
			if ($this->result=$db->Execute($this->sql)) {
				$or_details=array();
				while ($row=$this->result->FetchRow()) {
					$or_details[] = $row;
					/*
					$orno_array[$cnt] = $row['or_no'];
					$orname_array[$cnt] = $row['or_name'];
					$ordate_array[$cnt] = $row['or_date'];
					$canceldt_array[$cnt] = $row['cancel_date'];
					$cancelledby_array[$cnt] = $row['cancelled_by'];
					$cnt++;
					*/
				}
				//$or_details=array("or_no"=>$orno_array,"or_name"=>$orname_array,"or_date"=>$ordate_array,"cancel_date"=>$canceldt_array,"cancelled_by"=>$cancelledby_array);
				return $or_details;
			}
			else return array();
		}


		function saveNewOR($newOR,$oldOR,$newhist)
		{
			global $db;
			$this->sql="update $this->pay_tb set or_no='".$newOR."',history='".$newhist."' where or_no='".$oldOR."'";
			if($this->result=$db->Execute($this->sql))
			{
				return true;
			}
			else{ return false;}
		}

		function getHistory($oldOR)
		{
			global $db;
			$this->sql="SELECT history FROM $this->pay_tb WHERE or_no='".$oldOR."'";
			$this->result=$db->Execute($this->sql);
			$row=$this->result->FetchRow();
			return $row['history'];
		}

		function ifORExists($or_no)
		{
			global $db;
			$this->sql="SELECT or_no FROM $this->pay_tb WHERE or_no='".$or_no."'";
			$this->result=$db->Execute($this->sql);
			$row=$this->result->FetchRow();
			return $row['or_no'];
		}
}
?>
