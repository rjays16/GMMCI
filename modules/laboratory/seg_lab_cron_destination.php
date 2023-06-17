<?php
require_once('./roots.php');
include_once($root_path.'include/inc_seg_mylib.php');

# SEGHIS DATABASE (MYSQL)
class DestinationDB {
	var $seg_dbdest;
	var $bIsOk;
	var $bIsConnected;

	function DestinationDB() {
		$this->bIsConnected = false;
	}

	function isConnected() { return($this->bIsConnected); }

	function getDestConnection() {
			global $db_hclab;
			return($db_hclab);
	}

	function beginTransaction() {
			global $db_hclab;
			return($db_hclab->StartTrans());
	}

	function failTransaction() {
			global $db_hclab;
			return($db_hclab->FailTrans());
	}

	function endTransaction() {
			global $db_hclab;
			return($db_hclab->CompleteTrans());
	}

	function isOrderNoValid($srefno) {
			global $db;

			$qry = "select count(*) as rcount ".
							 "   from seg_lab_serv where refno = '".$srefno."' ".
							 "   and not exists(select * from seg_lab_results where refno = '".$srefno."')";
			$rs = $db->Execute($qry);

			$i = 0;
			if (!$rs)
					ShowErrorMsg($db->ErrorMsg());  // Displays the error message if no results could be returned
			else {
					// loop through results
					while ($row = $rs->FetchNextObject()) {
							// The field names need to be uppercase
							$i += $row->RCOUNT;
					}
			}

			return($i > 0);
	}

	# This function adds new result in SegHIS DB from HCLAB.
	function saveResultHeader($sRefno, $sTrx_dt, $sTrx_status, $sOrder_no, $sOrder_dt, $sLoc_code,
														$sDr_code, $sPid, $sPatient_caseNo, $sClinical_info, $sPriority,
														$sLab_no, $sService_code, $sTest_type, $sTg_code, $nCtl_seqNo) {
		 global $db;

		 $this->bIsOk = true;		// Initialize flag.

		 $fldArray = array('refno'=>"'$sRefno'", 'trx_dt'=>"'$sTrx_dt'", 'trx_status'=>"'$sTrx_status'",
											 'order_no'=>"'$sOrder_no'", 'order_dt'=>"'$sOrder_dt'", 'loc_code'=>"'$sLoc_code'",
											 'dr_code'=>"'$sDr_code'", 'pid'=>"'$sPid'", 'patient_caseNo'=>"'$sPatient_caseNo'",
											 'clinical_info'=>"'$sClinical_info'", 'priority'=>"'$sPriority'",
											 'lab_no'=>"'$sLab_no'", 'service_code'=>"'$sService_code'",
											 'test_type'=>"'$sTest_type'", 'tg_code'=>"'$sTg_code'", 'ctl_seqNo'=>"$nCtl_seqNo");

		 if (!($db->Replace('seg_lab_results', $fldArray, 'refno'))) {
				ShowErrorMsg('ERROR: '.$db->ErrorMsg());
				$this->bIsOk = false;
		 }
	}

	function saveResultDetails($sRefno, $nLine_no, $sTest_code, $sTest_name, $sData_type, $sResult_value,
														 $sUnit, $sResult_flag, $sRanges, $sResult_status, $sTest_comment,
														 $sMlt_code, $sMlt_name, $sReported_dt, $sPerformed_lab_code,
														 $sPerformed_lab_name, $sParent_item) {
		global $db;
		$this->bIsOk = true;		// Initialize flag.

		$fldArray = array('refno'=>"'$sRefno'", 'line_no'=>"$nLine_no", 'test_code'=>"'$sTest_code'",
											'test_name'=>"'$sTest_name'", 'data_type'=>"'$sData_type'", 'result_value'=>"'$sResult_value'",
											'unit'=>"'$sUnit'", 'result_flag'=>"'$sResult_flag'", 'ranges'=>"'$sRanges'",
											'result_status'=>"'$sResult_status'", 'test_comment'=>"'$sTest_comment'",
											'mlt_code'=>"'$sMlt_code'", 'mlt_name'=>"'$sMlt_name'",
											'reported_dt'=>"'$sReported_dt'", 'performed_lab_code'=>"'$sPerformed_lab_code'",
											'performed_lab_name'=>"'$sPerformed_lab_name'", 'parent_item'=>"'$sParent_item'");

		if (!($db->Replace('seg_lab_results_details', $fldArray, array('refno', 'line_no')))) {
			ShowErrorMsg('ERROR: '.$db->ErrorMsg());
			$this->bIsOk = false;
		}
	}

	function updateFetchTracker($oldrefno, $srefno, $dorder_dt) {
			global $db;
			$this->bIsOk = true;        // Initialize flag.

			if ($oldrefno == '')
					$this->sql = "insert into seg_lab_result_fetchtracker (order_no, order_dt) ".
											 " 	 values ('$srefno', '$dorder_dt')";
			else
					$this->sql = "update seg_lab_result_fetchtracker set ".
											 "   order_no = '$srefno', ".
											 "   order_dt = '$dorder_dt' ".
											 "   where order_no = '$oldrefno'";

			if (!($db->Execute($this->sql))) {
				ShowErrorMsg('ERROR: '.$db->ErrorMsg());
				$this->bIsOk = false;
			}
	}

	#added by VAN 08-15-2011
	function deleteResultByOrderNo($order_no){
			global $db;
			$this->bIsOk = true;        // Initialize flag.

			$this->sql = "DELETE FROM seg_lab_results WHERE refno='".$order_no."'";

			if (!($db->Execute($this->sql))) {
				ShowErrorMsg('ERROR: '.$db->ErrorMsg());
				$this->bIsOk = false;
			}

	}

	#added by VAN 08-08-09
	#to get all the order no that match in the LIS given the reference no from segHIS
	function getLabOrderNo($refno) {
			global $db;
			$qry = "SELECT lis_order_no FROM seg_lab_hclab_orderno WHERE refno='".$refno."'";

			$rs = $db->Execute($qry);

			if (!$rs) {
				ShowErrorMsg($db->ErrorMsg());  // Displays the error message if no results could be returned
			}
			return($rs);
	}

	#to get all the reference no. that match in the LIS given the order no. from LIS
	function getLabRefno($orderNo) {
			global $db;
			$qry = "SELECT refno FROM seg_lab_hclab_orderno WHERE lis_order_no='".$orderNo."'";

			$rs = $db->Execute($qry);

			if (!$rs) {
				ShowErrorMsg($db->ErrorMsg());  // Displays the error message if no results could be returned
			}
			return($rs);
	}

	#get all order no that has no result yet
	function getAllOrderNo($tracker_last_order_no=0,$tracker_orderno=0,$tracker_date='0000-00-00', $tracker_time = '00:00:00') {
			global $db;

			#EDITED BY VAN 07-22-2010
			#new $tracker_last_order_no less 25 records
			$record_num_first = 25;
			$record_num_last = 25;
			$tracker_last_order_no_less = $tracker_last_order_no - $record_num_first;
			$tracker_last_order_no_add = $tracker_last_order_no + $record_num_last;

			$this->sql = "SELECT DISTINCT l.lis_order_no
											FROM seg_lab_hclab_orderno AS l
											INNER JOIN seg_lab_serv AS s ON s.refno=l.refno
											INNER JOIN seg_lab_servdetails AS d ON d.refno=s.refno
											WHERE l.lis_order_no BETWEEN '".$tracker_last_order_no_less."' AND '".$tracker_last_order_no_add."'
											AND lis_order_no <= '".$tracker_orderno."'
											AND l.refno IN (SELECT refno
												FROM seg_lab_serv AS s
												WHERE DATE(s.serv_dt) <= '".$tracker_date."')
											AND l.lis_order_no NOT IN (SELECT order_no FROM seg_lab_results)";

			$rs = $db->Execute($this->sql);
			$this->count = $rs->RecordCount();
			if (!$rs) {
				ShowErrorMsg($db->ErrorMsg());  // Displays the error message if no results could be returned
			}

		return($rs);
	}

	#update if request is already served
	function DoneRequest($refno, $service_code, $date_served) {
			global $db;
			$this->bIsOk = true;        // Initialize flag.

			$this->qry = "UPDATE seg_lab_servdetails
										SET is_served=1, date_served='".$date_served."',status='done'
										WHERE refno='".$refno."' AND service_code='".$service_code."'";

			if (!($db->Execute($this->qry))) {
				ShowErrorMsg('ERROR: '.$db->ErrorMsg());
				$this->bIsOk = false;
			}
	}


	function getServiceCode($test_code, $mode) {
			global $db;
			if ($mode)
				#INPATIENT CODE
				$qry = "SELECT * FROM seg_lab_services
								WHERE ipdservice_code='".$test_code."'
								AND status NOT IN ('deleted','hidden','inactive','void')";
			else
				#OUTPATIENT
				$qry = "SELECT * FROM seg_lab_services
								WHERE oservice_code='".$test_code."'
								AND status NOT IN ('deleted','hidden','inactive','void')";

			$rs = $db->Execute($qry);

			if (!$rs) {
				ShowErrorMsg($db->ErrorMsg());  // Displays the error message if no results could be returned
			}
			return($rs);
	}

	function getTestCode($test_code) {
		global $db;
		$this->qry = "SELECT * FROM seg_lab_services
									WHERE service_code='".$test_code."' OR oservice_code='".$test_code."' OR ipdservice_code='".$test_code."'
									AND status NOT IN ('deleted','hidden','inactive','void')";

		$rs = $db->Execute($this->qry);
		$this->count = $rs->RecordCount();
		if (!$rs) {
			ShowErrorMsg($db->ErrorMsg());  // Displays the error message if no results could be returned
		}
		return($rs);
	}

	function getLabRefnoInfo($orderNo) {
			global $db;
			$qry = "SELECT l.*
								FROM seg_lab_hclab_orderno AS o
								INNER JOIN seg_lab_serv AS l ON l.refno=o.refno
								WHERE o.lis_order_no='".$orderNo."'";

			$rs = $db->Execute($qry);

			if (!$rs) {
				ShowErrorMsg($db->ErrorMsg());  // Displays the error message if no results could be returned
			}
			return($rs);
	}
	#-----------------------------

	#added by VAN 04-06-10
	function getLastOrderNo(){
			global $db;

			$this->sql = "SELECT * FROM seg_lab_hclab_orderno AS d
										WHERE d.lis_order_no IN (SELECT order_no FROM seg_lab_results)
										ORDER BY d.lis_order_no DESC LIMIT 1";

			$rs = $db->Execute($this->sql);

			if (!$rs) {
				ShowErrorMsg($db->ErrorMsg());  // Displays the error message if no results could be returned
		 }
		 return($rs);
	}

	function getAllOrderNoSeghis($order_array){
			global $db;

			$this->sql = "SELECT order_no FROM seg_lab_results WHERE order_no IN ($order_array)";

			$rs = $db->Execute($this->sql);

			if (!$rs) {
					ShowErrorMsg($db->ErrorMsg());  // Displays the error message if no results could be returned
			}
			return($rs);
	}

	function getLastOrderNoInHCLAB(){
			global $db;

			$this->sql = "SELECT * FROM seg_lab_hclab_orderno AS d
										ORDER BY d.lis_order_no DESC LIMIT 1";

			$rs = $db->Execute($this->sql);

			if (!$rs) {
				ShowErrorMsg($db->ErrorMsg());  // Displays the error message if no results could be returned
			}
			return($rs);
	}
}
?>
