<?php

require_once($root_path.'include/care_api_classes/class_core.php');

class CostCenterGuiMgr extends Core{

	var $tb_gui_mgr = 'seg_gui_mgr';
	var $tb_gui_mgr_details = 'seg_gui_mgr_details';
	var $fld_gui_mgr = array(
	"ref_source",
	"section",
	"no_rows",
	"no_cols"
	);
	var $fld_gui_mgr_details = array(
	"nr",
	"service_code",
	"header_data",
	"col_order_no",
	"row_order_no",
	"name_type"
	);

	function CostCenterGuiMgr()
	{
		$this->useGuiMgr();
	}

	function useGuiMgr()
	{
		$this->coretable=$this->tb_gui_mgr;
		$this->ref_array=$this->fld_gui_mgr;
	}

	function useGuiMgrDetails()
	{
		$this->coretable=$this->tb_gui_mgr_details;
		$this->ref_array=$this->fld_gui_mgr_details;
	}

	function saveGuiMgr($data)
	{
		global $db;
		extract($data);
		if($cost_center=="LD")
			$section = $lab_section;
		else if($cost_center=="RD")
			$section = $radio_section;

		$this->sql = "INSERT INTO $this->tb_gui_mgr (ref_source, section, no_rows, no_cols) VALUES".
		"(".$db->qstr($cost_center).",".$db->qstr($section).
		",".$db->qstr($num_rows).",".$db->qstr($num_cols).
		")";
		#echo "<br>".$this->sql;
		$db->StartTrans();
		$db->Execute($this->sql);
		if($db->Affected_Rows())
		{
			$id = $db->Insert_ID();
			for($i=0;$i<count($data_values);$i++)
			{
				$cell = explode("/",$cell_id[$i]);
				if($datatype[$i]=="header")
				{
					$this->sql = "INSERT INTO $this->tb_gui_mgr_details (nr,header_data,row_order_no,col_order_no,name_type)".
					"VALUES ('".$id."',".$db->qstr($data_values[$i]).",".$db->qstr($i).",".$db->qstr($cell[1]).",'H'".
					")";
				}
				else if($datatype[$i]=="data")
				{
					$this->sql = "INSERT INTO $this->tb_gui_mgr_details (nr,service_code,row_order_no,col_order_no,name_type)".
					"VALUES ('".$id."',".$db->qstr($data_values[$i]).",".$db->qstr($i).",".$db->qstr($cell[1]).",'D'".
					")";
				}
				#echo "<br>".$this->sql;
				$db->Execute($this->sql);
				if($db->Affected_Rows())
				{
					$ok++;
				}
				else
				{
					print_r($db->ErrorMsg());
					$db->FailTrans();
					$db->CompleteTrans();
					return false;
				}
			}
			if($ok>0)
			{
				$db->CompleteTrans();
				return true;
			}
			else
			{
				$db->FailTrans();
				$db->CompleteTrans();
				return false;
			}
		}
	}

	function countGuiItems($multiple=0, $maxcount=100, $offset=0)
	{
		global $db;
		$this->sql = "SELECT * FROM $this->tb_gui_mgr ORDER BY nr ASC";
		if ($this->result=$db->Execute($this->sql))
		{
			if ($this->count=$this->result->RecordCount())
			{
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}

	function getGuiItems($multiple=0, $maxcount=100, $offset=0)
	{
		global $db;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		$this->sql="SELECT * FROM $this->tb_gui_mgr ORDER BY ref_source,section ASC";

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

	function deleteGuiItem($id)
	{
		global $db;
		$this->sql = "DELETE FROM $this->tb_gui_mgr WHERE nr=".$db->qstr($id);
		$db->Execute($this->sql);
		if($db->Affected_Rows())
		{
			return true;
		}
		else
		{
			print_r($db->ErrorMsg());
			return false;
		}
	}

	function getGuiDetailItems($id)
	{
		global $db;
		$this->sql = "SELECT gm.*, gd.* FROM seg_gui_mgr AS gm LEFT JOIN seg_gui_mgr_details AS gd ON gm.nr=gd.nr".
		" WHERE gm.nr=".$db->qstr($id)." ORDER BY gd.row_order_no, gd.col_order_no ASC";
		$this->result = $db->Execute($this->sql);
		if ($this->result)
		{
			if ($this->result->RecordCount() > 0)
					return $this->result;
			else
				return false;
		}
		else
		{
			return false;
		}
	}

	function updateGuiMgr($data)
	{
		global $db;
		extract($data);
		if($cost_center=="LD")
			$section = $lab_section;
		else if($cost_center=="RD")
			$section = $radio_section;

		$this->sql = "UPDATE $this->tb_gui_mgr SET ref_source=".$db->qstr($cost_center).", ".
		"section=".$db->qstr($section).", no_rows=".$db->qstr($num_rows).", no_cols=".$db->qstr($num_cols).
		" WHERE nr=".$db->qstr($edit_id);
		#echo "<br>".$this->sql;

		$db->StartTrans();
		#$db->Execute($this->sql);
		#if($db->Affected_Rows())
		if($db->Execute($this->sql))
		{
			$this->sql = "DELETE FROM $this->tb_gui_mgr_details WHERE nr=".$db->qstr($edit_id);
			$db->Execute($this->sql);

			#echo "<br>".$this->sql;
			if($db->Affected_Rows())
			{
				for($i=0;$i<count($data_values);$i++)
				{
					$cell = explode("/",$cell_id[$i]);
					if($datatype[$i]=="header")
					{
						$this->sql = "INSERT INTO $this->tb_gui_mgr_details (nr,header_data,row_order_no,col_order_no,name_type)".
						"VALUES ('".$edit_id."',".$db->qstr($data_values[$i]).",".$db->qstr($i).",".$db->qstr($cell[1]).",'H'".
						")";
					}
					else if($datatype[$i]=="data")
					{
						$this->sql = "INSERT INTO $this->tb_gui_mgr_details (nr,service_code,row_order_no,col_order_no,name_type)".
						"VALUES ('".$edit_id."',".$db->qstr($data_values[$i]).",".$db->qstr($i).",".$db->qstr($cell[1]).",'D'".
						")";
					}
					//echo "<br>".$this->sql;
					$db->Execute($this->sql);
					if($db->Affected_Rows())
					{
						$ok++;
					}
					else
					{
						print_r($db->ErrorMsg());
						$db->FailTrans();
						$db->CompleteTrans();
						return false;
					}
				}
				if($ok>0)
				{
					$db->CompleteTrans();
					return true;
				}
				else
				{
					$db->FailTrans();
					$db->CompleteTrans();
					return false;
				}
			}
		}
		else
		{
			print_r($db->ErrorMsg());
			$db->FailTrans();
			$db->CompleteTrans();
			return false;
		}
	}
}
?>
