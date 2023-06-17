<?php
#created by VANESSA A. SAREN
require('./roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');
require($root_path.'classes/adodb/adodb.inc.php');
include($root_path.'include/inc_init_hclab_main.php');

class HCLAB extends Core{

	var $dbdest;
	var $bIsOk;
	var $bIsConnected;

	var $result;
	var $count;
	var $sql;

	var $data_array=array();

	var $tb_pat_order_header='PAT_ORDERH';
	var $tb_pat_order_details='PAT_ORDERD';
	var $tb_pat_result_header='PAT_RESULTH';
	var $tb_pat_result_details='PAT_RESULTD';

	var $fld_pat_order_header=array(
		"POH_TRX_NUM",
		"POH_TRX_DT",
		"POH_TRX_ID",
		"POH_TRX_STATUS",
		"POH_ORDER_NO",
		"POH_ORDER_DT",
		"POH_LOC_CODE",
		"POH_LOC_NAME",
		"POH_DR_CODE",
		"POH_DR_NAME",
		"POH_PAT_ID",
		"POH_PAT_NAME",
		"POH_PAT_TYPE",
		"POH_PAT_ALTID",
		"POH_PAT_DOB",
		"POH_PAT_SEX",
		"POH_PAT_CASENO",
		"POH_CLI_INFO",
		"POH_PRIORITY"
	);

	var $fld_pat_result_header=array(
	);

	var $fld_pat_result_details=array(
	);

	/* Constructor */
	 function HCLAB() {
			$this->coretable = "PAT_ORDERH";
			$this->bIsOk = true;				// Initialize result flag.
			$this->bIsConnected = false; 		// Initialize connection indicator.

	 }/* end of function */

	function useLabOrder(){
		$this->coretable=$this->tb_pat_order_header;
		$this->ref_array=$this->fld_pat_order_header;
	}

	function useLabResultH(){
		$this->coretable=$this->tb_pat_result_header;
		$this->ref_array=$this->fld_pat_result_header;
	}

	function useLabResultD(){
		$this->coretable=$this->tb_pat_result_details;
		$this->ref_array=$this->fld_pat_result_details;
	}

	function ConnectHCLAB() {
			global $dsn, $DBType_Oracle, $DBHost_Oracle, $DBName_Oracle, $DBUser_Oracle, $DBPassword_Oracle, $DBDestPersist_Oracle;
			if ($dsn){
				$this->dbdest = &ADONewConnection("$DBType_Oracle");
				$bConnected = $this->dbdest->PConnect($DBHost_Oracle, $DBUser_Oracle, $DBPassword_Oracle, $DBName_Oracle);
			}
			return $dblink_hclab_ok;
	}

	function ConnecttoDest($sDSN = "") {
		GLOBAL $DBType_Oracle, $DBHost_Oracle, $DBName_Oracle, $DBUser_Oracle, $DBPassword_Oracle, $DBDestPersist_Oracle;

		$this->dbdest = &ADONewConnection("$DBType_Oracle");

		//if ($DBType_Oracle == "oci8") {
		if ($DBType_Oracle == "odbc_oracle") {
			if ($sDSN != "") $dsn = $sDSN;
			$bConnected = $this->dbdest->Connect($dsn, $DBUser_Oracle, $DBPassword_Oracle);
		}
		else {
			if (!isset($DBDestPersist_Oracle) or !empty($DBDestPersist_Oracle)) {      // Use persistent connection (default)
				$bConnected = $this->dbdest->PConnect($DBHost_Oracle, $DBUser_Oracle, $DBPassword_Oracle, $DBName_Oracle);
			} else {
				#echo "s = ".$DBHost_Oracle.", ".$DBUser_Oracle.", ".$DBPassword_Oracle.", ".$DBName_Oracle;
				$bConnected = $this->dbdest->PConnect($DBHost_Oracle, $DBUser_Oracle, $DBPassword_Oracle, $DBName_Oracle);
			}
		}

		if (!$bConnected) {
			// In the name of protocol correctness, monitoring and performance
			// profiling, set the appropriate error headers for machine comsumption
			if (isset($_SERVER['SERVER_PROTOCOL'])) {
				// Avoid it with cron.php. Note that we assume it's HTTP/1.x
				header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
			}
			// and then for human consumption...
			ShowErrorMsg('');

			$this->bIsConnected = true;
			return(NULL);
		}

		/// Forcing ASSOC mode for ADOdb (some DBs default to FETCH_BOTH)
		$this->dbdest->SetFetchMode(ADODB_FETCH_ASSOC);

		#error_reporting(E_ALL);       // Show errors from now on.

		/// Starting here we have a correct DB conection but me must avoid
		/// to execute any DB transaction until "set names" has been executed
		/// some lines below!
		//	configure_dbconnection($this->dbdest, $DBType_Oracle);

		$this->bIsConnected = true;
		return($this->dbdest);
	}


	function beginTransaction() {
			global $db_hclab;
			return($db_hclab->StartTrans());
	}
	function endTransaction() {
			global $db_hclab;
			return($db_hclab->CompleteTrans());
	}
	function hasTransactionFailed() { return(!$this->bIsOk); }
	function isConnected() { return($this->bIsConnected); }

	# delete a specific request in HCLAB Order header
	function deleteOrderH_to_HCLAB($refno){
		#PAT_ORDERH - header
		global $db_hclab;

		if(empty($refno) || (!$refno))
			return FALSE;

		$this->useLabOrder();

		$this->sql="DELETE FROM $this->coretable WHERE POH_TRX_NUM=$refno";

		# for oracle
		if ($db_hclab->Execute($this->sql)) {
			if ($db_hclab->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
			else return FALSE;

	}

	# to check if the request is in the HCLAB order header
	function isExists($refno) {

		global $db_hclab;

		$this->useLabOrder();
		$this->sql="select * FROM $this->coretable WHERE POH_TRX_NUM = $refno";

		# for oracle
		if($buf=$db_hclab->Execute($this->sql)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }

	}

	# add the request in the HCLAB order header
	function addOrderH_to_HCLAB($data){
		#PAT_ORDERH - header
		global $db_hclab;

		extract($data);
		$ret=FALSE;

		$this->useLabOrder();

		$index = "POH_TRX_NUM, POH_TRX_DT, POH_TRX_ID, POH_TRX_STATUS, POH_ORDER_NO,
									POH_ORDER_DT, POH_LOC_CODE, POH_LOC_NAME, POH_DR_CODE, POH_DR_NAME,
							POH_PAT_ID, POH_PAT_NAME, POH_PAT_TYPE, POH_PAT_ALTID, POH_PAT_DOB,
								POH_PAT_SEX, POH_PAT_CASENO, POH_CLI_INFO, POH_PRIORITY";

		$field_array = "(POH_TRX_NUM,POH_TRX_DT,POH_TRX_ID,POH_TRX_STATUS,POH_ORDER_NO,POH_ORDER_DT,POH_LOC_CODE,
										 POH_LOC_NAME,POH_DR_CODE,POH_DR_NAME,POH_PAT_ID,POH_PAT_NAME,POH_PAT_TYPE,POH_PAT_ALTID,
										 POH_PAT_DOB,POH_PAT_SEX,POH_PAT_CASENO,POH_CLI_INFO,POH_PRIORITY)";

		$values = " '$POH_TRX_NUM', to_date('$POH_TRX_DT', 'MM/DD/YYYY HH12:MI:SS AM'), '$POH_TRX_ID', '$POH_TRX_STATUS', '$POH_ORDER_NO',
										to_date('$POH_ORDER_DT', 'MM/DD/YYYY HH12:MI:SS AM'), '$POH_LOC_CODE', '$POH_LOC_NAME', '$POH_DR_CODE', '$POH_DR_NAME',
								'$POH_PAT_ID', '$POH_PAT_NAME', '$POH_PAT_TYPE', '$POH_PAT_ALTID', to_date('$POH_PAT_DOB', 'MM/DD/YYYY'),
							 '$POH_PAT_SEX', '$POH_PAT_CASENO', '$POH_CLI_INFO', '$POH_PRIORITY'";

		$this->sql="INSERT INTO $this->coretable ".$field_array." VALUES($values)";
		#echo "add sql = ".$this->sql;
		if ($db_hclab->Execute($this->sql)) {
			if ($db_hclab->Affected_Rows()) {
				$ret=TRUE;
			}
		}

		if ($ret)	return TRUE;
			else return FALSE;

	}

	# update the request in the HCLAB order header
	function updateOrderH_to_HCLAB($data){
		#PAT_ORDERH - header
		global $db_hclab;
		$ret=FALSE;
		extract($data);

		$this->useLabOrder();

		# for oracle
		$this->sql="UPDATE $this->coretable SET
											POH_TRX_DT=to_date('$POH_TRX_DT', 'MM/DD/YYYY HH12:MI:SS AM'),
											POH_TRX_ID='$POH_TRX_ID',
											POH_TRX_STATUS='$POH_TRX_STATUS',
											POH_ORDER_NO='$POH_ORDER_NO',
											POH_ORDER_DT=to_date('$POH_ORDER_DT', 'MM/DD/YYYY HH12:MI:SS AM'),
											POH_LOC_CODE='$POH_LOC_CODE',
											POH_LOC_NAME='$POH_LOC_NAME',
											POH_DR_CODE='$POH_DR_CODE',
											POH_DR_NAME='$POH_DR_NAME',
											POH_PAT_ID='$POH_PAT_ID',
											POH_PAT_NAME='$POH_PAT_NAME',
											POH_PAT_TYPE='$POH_PAT_TYPE',
											POH_PAT_ALTID='$POH_PAT_ALTID',
											POH_PAT_DOB=to_date('$POH_PAT_DOB', 'MM/DD/YYYY'),
											POH_PAT_SEX='$POH_PAT_SEX',
											POH_PAT_CASENO='$POH_PAT_CASENO',
											POH_CLI_INFO='$POH_CLI_INFO',
											POH_PRIORITY='$POH_PRIORITY'
											WHERE POH_TRX_NUM=$POH_TRX_NUM";

		if ($db_hclab->Execute($this->sql)) {
			if ($db_hclab->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
			else return FALSE;

	}

	# clear the list in the HCLAB order request details with the respective reference no.
	function clearOrderList_to_HCLAB($refno, $request_to_deleted='') {
		global $db_hclab;
		$ret=FALSE;

				if (empty($request_to_deleted))
						$this->sql = "DELETE FROM $this->tb_pat_order_details WHERE POD_TRX_NUM='$refno'";
				else
						$this->sql = "DELETE FROM $this->tb_pat_order_details
							WHERE POD_TRX_NUM=$refno AND POD_TEST_CODE IN (".$request_to_deleted.")";

		# for oracle

		if ($db_hclab->Execute($this->sql)) {
			if ($db_hclab->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
			else return FALSE;

	}

	# add order request details in HCLAB order details
	function addOrders_to_HCLAB($refno, $orderArray) {
		global $db_hclab;

		$field_array = "(POD_TRX_NUM,POD_TEST_CODE,POD_TEST_NAME,POD_INSURANCE_CODE)";

		for ($i=0; $i<sizeof ($orderArray);$i++){
			$ret=FALSE;
			$this->sql = "INSERT INTO $this->tb_pat_order_details ".$field_array."
										VALUES($refno,'".$orderArray[$i][0]."','".$orderArray[$i][1]."','".$orderArray[$i][2]."')";
			#echo "<br>sql = ".$this->sql;
			# for oracle
			if ($db_hclab->Execute($this->sql)) {
				if ($db_hclab->Affected_Rows()) {
					//$ret=TRUE;
				}
			}
		}
	}

	# already functioning and connected to HCLAB
	function getResult_to_HCLAB($refno, $service_code){
		global $db_hclab;

		$this->sql="SELECT PAT_RESULTH.*, PAT_RESULTD.*, to_char(PAT_RESULTD.PRD_REPORTED_DT, 'MM/DD/YYYY HH12:MI:SS AM') AS PRD_REPORTED_DT
								FROM PAT_RESULTH
						INNER JOIN PAT_RESULTD
						ON PAT_RESULTH.PRH_TRX_NUM = PAT_RESULTD.PRD_TRX_NUM
						WHERE PRD_TRX_NUM=$refno
						AND PRH_TEST_CODE='$service_code'";

		if ($this->result=$db_hclab->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
			return FALSE;
		}
	}

	function getResultHeader_to_HCLAB($refno){
		global $db_hclab;

		$this->sql="SELECT PAT_RESULTH.*, to_char(PAT_RESULTH.PRH_ORDER_DT, 'MM/DD/YYYY HH12:MI:SS AM') AS PRH_ORDER_DT
								FROM PAT_RESULTH
						WHERE PRH_TRX_NUM =$refno";

		if ($this->result=$db_hclab->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result->FetchRow();;
		} else{
			return FALSE;
		}
	}

	function getResult_Header_Current(){
		global $db_hclab;
		#ALL RESULT DETAILS
		$this->sql = "SELECT PAT_RESULTD.*
									FROM PAT_RESULTD
							ORDER BY PRD_TRX_NUM ASC";

		if ($this->result=$db_hclab->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
			return FALSE;
		}
	}


}/* end of class HCLAB */

?>