<?php
require_once('./roots.php');
include_once($root_path.'include/inc_seg_mylib.php');

# HCLAB DATABASE (ORACLE)
class SourceDB {
	var $dbsrc;
	var $bIsConnected;

	var $baseDateTime;
	var $baseORDNo;

	# define 10 minutes;
	var $max_num = 10;

	function SourceDB() {
		$this->bIsConnected = false;
	}

	function isConnected() { return($this->bIsConnected); }

	function isAllowedToFetchLabResults($db) {
		$qry = "select count(*) as rcount from seg_lab_result_fetchtracker where get_lock('mylock', 10) = 1";
		$rs = $db->Execute($qry);
		$i = 0;
		if ($rs) {
			$i = $rs->RecordCount();
		}
		return($i > 0);
	}

	function getBaseDateTime($db) {
		$this->qry = "select * from seg_lab_result_fetchtracker where get_lock('mylock', 10) = 1";
		$rs = $db->Execute($this->qry);

		if ($rs) {
			while ($row = $rs->FetchNextObject()) {
				$this->baseDateTime = $row['order_dt'];
				return $this->baseDateTime;
			}
		}
	}

	function getBasedOrderbNo(){
		global $db;

		$this->sql="SELECT * FROM seg_lab_result_fetchtracker LIMIT 1";

		$rs = $db->Execute($this->sql);
		$this->count = $rs->RecordCount();
		if (!$rs) {
			ShowErrorMsg($db->ErrorMsg());  // Displays the error message if no results could be returned
		}
		return($rs);
	}

	function releaseFetchLock($db) {
		$qry = "select RELEASE_LOCK('mylock')";
		if (!($db->Execute($qry))) {
			ShowErrorMsg($db->ErrorMsg());
		}
	}

	#edited by VAN 08-10-09
	function hasLabResultsToFetch() {
		global $db_hclab;

		#get the results for past 1 hour
		/*$this->qry = "SELECT count(*) AS counter FROM PAT_RESULTH
									WHERE DATE(PRH_TRX_DT)=DATE(NOW())
									AND TIME(PRH_TRX_DT) BETWEEN TIME(DATE_SUB(NOW(), INTERVAL ".$this->max_hour." HOUR)) AND TIME(NOW())";*/
		$this->qry = "SELECT count(*) AS counter FROM PAT_RESULTH
									WHERE TO_CHAR(PRH_TRX_DT, 'MM-DD-YYYY')=TO_CHAR(SYSDATE, 'MM-DD-YYYY')
									AND TO_CHAR(PRH_TRX_DT, 'HH24:MI:SS') BETWEEN
										TO_CHAR(SYSDATE - INTERVAL '".$this->max_num."' MINUTE, 'HH24:MI:SS')
										AND TO_CHAR(SYSDATE, 'HH24:MI:SS')";

		#echo "qry => ".$this->qry;
		$rs = $db_hclab->Execute($this->qry);
		$i = 0;
		if (!$rs)
			ShowErrorMsg($db_hclab->ErrorMsg());  // Displays the error message if no results could be returned
		else {
			// loop through results
			$this->rowres = $rs->FetchRow();
			$i = $rs->RecordCount();
		}

		return($i > 0);
	}

	#edited by VAN 08-10-09
	function getLabResults() {
		global $db_hclab;

		/*$this->qry = "SELECT PR.*,
									to_char(PRH_TRX_DT, 'YYYY-MM-DD HH:MI:SS') AS PRH_TRX_DT_2,
									to_char(PRH_ORDER_DT, 'YYYY-MM-DD  HH:MI:SS') AS PRH_ORDER_DT_2
									FROM PAT_RESULTH PR
									WHERE DATE(PRH_TRX_DT)=DATE(NOW())
									AND TIME(PRH_TRX_DT) BETWEEN TIME(DATE_SUB(NOW(), INTERVAL ".$this->max_hour." HOUR)) AND TIME(NOW())";*/
		$this->qry = "SELECT PR.*,
									to_char(PRH_TRX_DT, 'YYYY-MM-DD HH:MI:SS') AS PRH_TRX_DT_2,
									to_char(PRH_ORDER_DT, 'YYYY-MM-DD  HH:MI:SS') AS PRH_ORDER_DT_2
									FROM PAT_RESULTH PR
									WHERE TO_CHAR(PRH_TRX_DT, 'MM-DD-YYYY')=TO_CHAR(SYSDATE, 'MM-DD-YYYY')
									AND TO_CHAR(PRH_TRX_DT, 'HH24:MI:SS') BETWEEN
										TO_CHAR(SYSDATE - INTERVAL '".$this->max_num."' MINUTE, 'HH24:MI:SS')
										AND TO_CHAR(SYSDATE, 'HH24:MI:SS')";

		$rs = $db_hclab->Execute($this->qry);
		$this->count = $rs->RecordCount();
		if (!$rs) {
			ShowErrorMsg($db_hclab->ErrorMsg());  // Displays the error message if no results could be returned
		}
		return($rs);
	}

	function getLabResultDetails($s_trx_no) {
		global $db_hclab;
		$qry = "select * ".
					 "   from PAT_RESULTD ".
					 "   where PRD_TRX_NUM = '".$s_trx_no."'";

		$rs = $db_hclab->Execute($qry);
		$this->count = $rs->RecordCount();
		if (!$rs) {
			ShowErrorMsg($db_hclab->ErrorMsg());  // Displays the error message if no results could be returned
		}
		return($rs);
	}
}
?>
