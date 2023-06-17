<?php
#created by VAN 06-03-2011
require('./roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/inc_date_format_functions.php');

class SegTempWorkaround extends Core {

	function SegTempWorkaround($refno=''){
		if(!empty($refno)) $this->refno=$refno;
	}

	#added by VAN 06-02-2011
	function save_ManualPayment($refno, $manual_array){
		global $db, $HTTP_SESSION_VARS;

		extract($manual_array);

		$sql1 = "SELECT * FROM seg_payment_workaround WHERE service_area='".$service_area."'
							AND refno='".$refno."'";
		#echo $sql1;
		$res1=$db->Execute($sql1);
		if (is_object($res1))
			$row1_count=$res1->RecordCount();
		#$row1 = $res1->FetchRow();

		if ($row1_count){
			#update

			$this->sql="UPDATE seg_payment_workaround
											SET
											control_no  = '".$control_no."' ,
											approved_by = '".$approved_by."' ,
											type 	 = '".$type."' ,
											reason = '".$reason."' ,
											history = ".$this->ConcatHistory("Updated: ".date('Y-m-d H:i:s')." = ".$HTTP_SESSION_VARS['sess_user_name']."\n").",
											modify_id = '".$modify_id."',
											modify_date = '".$modify_date."',
											is_deleted  = '0'
											WHERE
											refno = '".$refno."'
											AND service_area = '".$service_area."'";
		}else{
			#insert
			$history = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";

			$this->sql="INSERT INTO seg_payment_workaround
								(service_area,
								 refno,
								 control_no,
								 approved_by,
								 type,
								 reason,
								 history,
								 create_id,
								 create_date,
								 modify_id,
								 modify_date
								)
								VALUES
								('".$service_area."',
								'".$refno."',
								'".$control_no."',
								'".$approved_by."',
								'".$type."',
								'".$reason."',
								'".$history."',
								'".$create_id."',
								'".$create_date."',
								'".$modify_id."',
								'".$modify_date."'
								);";
		}

		#echo $this->sql;
		$result=$db->Execute($this->sql);
		if($result){
			return true;
		}else return false;
	}

	function ManualPayment($refno, $manual_array){
		global $db, $HTTP_SESSION_VARS;

		extract($manual_array);


		$this->sql="UPDATE seg_payment_workaround
												SET
												is_deleted  = '1' ,
												history = ".$history.",
												modify_id = '".$modify_id."',
												modify_date = '".$modify_date."'
												WHERE
												refno = '".$refno."'
												AND service_area = '".$service_area."'";

			#echo $this->sql;
			$result=$db->Execute($this->sql);
			if($result){
				return true;
			}else return false;

	}

	function resetRequestFlag($refno, $manual_array, $table_name){
		global $db, $HTTP_SESSION_VARS;

		extract($manual_array);

		$sql1 = "SELECT * FROM seg_payment_workaround WHERE service_area='".$service_area."'
							AND refno='".$refno."'";

		$res1=$db->Execute($sql1);
		if (is_object($res1))
			$row1_count=$res1->RecordCount();

		if ($row1_count){
            #added by VAS 07-04-2012
            #add history for update
            if ($table_name=='seg_lab_servdetails'){
                $history = $this->ConcatHistory("Update request_flag [NULL] -workaround ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
                $history_cond = ", history=".$history."\n";
            }    
			$this->sql="UPDATE ".$table_name."
											SET
											request_flag  = NULL
                                            ".$history_cond."
											WHERE
											refno = '".$refno."'";
			#echo $this->sql;
			$result=$db->Execute($this->sql);
			if($result){
				return true;
			}else return false;
		}else
			return false;
	}

}
