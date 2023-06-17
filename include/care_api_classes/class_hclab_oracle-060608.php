<?php
#created by VANESSA A. SAREN
require('./roots.php');	
require_once($root_path.'include/care_api_classes/class_core.php');
require($root_path.'classes/adodb/adodb.inc.php');
include($root_path.'include/inc_init_hclab_main.php');
include($root_path.'include/inc_seg_mylib.php');

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
				$bConnected = $this->dbdest->Connect($DBHost_Oracle, $DBUser_Oracle, $DBPassword_Oracle, $DBName_Oracle);
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
//		configure_dbconnection($this->dbdest, $DBType_Oracle);
		
		$this->bIsConnected = true;
		return($this->dbdest);
	}	

	
	function beginTransaction() { return($this->dbdest->StartTrans()); }
	function endTransaction() { return($this->dbdest->CompleteTrans()); }
	function hasTransactionFailed() { return(!$this->bIsOk); }
	function isConnected() { return($this->bIsConnected); }
	/*
	function getSampleData() {
		$this->sql="select PRH_TRX_NUM, PRH_PAT_NAME FROM PAT_RESULTH WHERE PRH_PAT_NAME = 'GENOVIA, REBECCA'";		
		echo "<br>sql = ".$this->sql;
		if ($this->result=$this->dbdest->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
	   	return FALSE;
		}
	}
	*/
	
	# delete a specific request in HCLAB Order header
	function deleteOrderH_to_HCLAB($refno){
		#PAT_ORDERH - header
		global $db;
		
		if(empty($refno) || (!$refno))
			return FALSE;
			
		$this->useLabOrder();
		
		$this->sql="DELETE FROM $this->coretable WHERE POH_TRX_NUM=$refno";
		
	   #return $this->Transact();
		#for mysql
		/*
		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
			else return FALSE;
		*/	
		# for oracle
		
		if ($this->dbdest->Execute($this->sql)) {
			if ($this->dbdest->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
			else return FALSE;	
		
	}
	
	# to check if the request is in the HCLAB order header
	function isExists($refno) {
		
		global $db;
		
		$this->useLabOrder();
		$this->sql="select * FROM $this->coretable WHERE POH_TRX_NUM = $refno";		
		
		#echo "sql = ".$this->sql;
		#for mysql
		/*
		if($buf=$db->Execute($this->sql)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
		*/
		
		# for oracle
		
		if($buf=$this->dbdest->Execute($this->sql)) {
			if($buf->RecordCount()) {
				return true;
			} else { return false; }
		} else { return false; }
		
	}
	
	# add the request in the HCLAB order header
	function addOrderH_to_HCLAB($data){									
		#PAT_ORDERH - header
		#global $db_oracle;
		
		global $db;
		
		extract($data);
		$ret=FALSE;
		
		$this->useLabOrder();
		
		$index = "POH_TRX_NUM, POH_TRX_DT, POH_TRX_ID, POH_TRX_STATUS, POH_ORDER_NO,
                  POH_ORDER_DT, POH_LOC_CODE, POH_LOC_NAME, POH_DR_CODE, POH_DR_NAME,
		          POH_PAT_ID, POH_PAT_NAME, POH_PAT_TYPE, POH_PAT_ALTID, POH_PAT_DOB,
         	  	  POH_PAT_SEX, POH_PAT_CASENO, POH_CLI_INFO, POH_PRIORITY";
		
		#echo "date = ".$POH_ORDER_DT;
		# for oracle
		
		#$POH_TRX_DT = date("n/j/Y g:i:s A",$POH_TRX_DT);
		#$POH_ORDER_DT = date("n/j/Y g:i:s A",$POH_ORDER_DT);
		#$POH_PAT_DOB = date("n/j/Y",$POH_PAT_DOB);
		
		#for mysql
		/*
		$values = " $POH_TRX_NUM, '$POH_TRX_DT', '$POH_TRX_ID', '$POH_TRX_STATUS', '$POH_ORDER_NO',
                 '$POH_ORDER_DT', '$POH_LOC_CODE', '$POH_LOC_NAME', '$POH_DR_CODE', '$POH_DR_NAME',
		           '$POH_PAT_ID', '$POH_PAT_NAME', '$POH_PAT_TYPE', '$POH_PAT_ALTID', '$POH_PAT_DOB',
         		  '$POH_PAT_SEX', '$POH_PAT_CASENO', '$POH_CLI_INFO', '$POH_PRIORITY'";
		*/
		
		$values = " $POH_TRX_NUM, to_date('$POH_TRX_DT', 'MM/DD/YYYY HH12:MI:SS AM'), '$POH_TRX_ID', '$POH_TRX_STATUS', '$POH_ORDER_NO',
                    to_date('$POH_ORDER_DT', 'MM/DD/YYYY HH12:MI:SS AM'), '$POH_LOC_CODE', '$POH_LOC_NAME', '$POH_DR_CODE', '$POH_DR_NAME',
		            '$POH_PAT_ID', '$POH_PAT_NAME', '$POH_PAT_TYPE', '$POH_PAT_ALTID', to_date('$POH_PAT_DOB', 'MM/DD/YYYY'),
         		   '$POH_PAT_SEX', '$POH_PAT_CASENO', '$POH_CLI_INFO', '$POH_PRIORITY'";
				  
		#for mysql			  
		#$this->sql="INSERT INTO $this->coretable($index) VALUES($values)";
		
		# to oracle
		$this->sql="INSERT INTO $this->coretable VALUES($values)";
		
		#echo "insert sql = ".$this->sql;
		#return $this->Transact();
		
		#for mysql
		/*
		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
			else return FALSE;
		*/	
			
		# to oracle
		
		if ($this->dbdest->Execute($this->sql)) {
			if ($this->dbdest->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
			else return FALSE;	
	
	}
	
	# update the request in the HCLAB order header
	function updateOrderH_to_HCLAB($data){
		#PAT_ORDERH - header
		#global $db_oracle;
		global $db;
		$ret=FALSE;
		extract($data);
		
		$this->useLabOrder();
		# for oracle
		
		#$POH_TRX_DT = date("n/j/Y g:i:s A",$POH_TRX_DT);
		#$POH_ORDER_DT = date("n/j/Y g:i:s A",$POH_ORDER_DT);
		#$POH_PAT_DOB = date("n/j/Y",$POH_PAT_DOB);
		
		#for mysql
		/*
		$this->sql="UPDATE $this->coretable SET 
									   POH_TRX_DT='$POH_TRX_DT',
										POH_TRX_ID='$POH_TRX_ID',
										POH_TRX_STATUS='$POH_TRX_STATUS',
										POH_ORDER_NO='$POH_ORDER_NO',
										POH_ORDER_DT='$POH_ORDER_DT',
										POH_LOC_CODE='$POH_LOC_CODE',
										POH_LOC_NAME='$POH_LOC_NAME',
										POH_DR_CODE='$POH_DR_CODE',
										POH_DR_NAME='$POH_DR_NAME',
										POH_PAT_ID='$POH_PAT_ID',
										POH_PAT_NAME='$POH_PAT_NAME',
										POH_PAT_TYPE='$POH_PAT_TYPE',
										POH_PAT_ALTID='$POH_PAT_ALTID',
										POH_PAT_DOB='$POH_PAT_DOB', 
										POH_PAT_SEX='$POH_PAT_SEX', 
										POH_PAT_CASENO='$POH_PAT_CASENO', 
										POH_CLI_INFO='$POH_CLI_INFO', 
										POH_PRIORITY='$POH_PRIORITY' 
										WHERE POH_TRX_NUM=$POH_TRX_NUM";
		*/
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
		
		#return $this->Transact();
		#echo "update sql = ".$this->sql;
		/*
		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
			else return FALSE;
		*/	
		# for oracle
		
		if ($this->dbdest->Execute($this->sql)) {
			if ($this->dbdest->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
			else return FALSE;	
			
	}
	
	# clear the list in the HCLAB order request details with the respective reference no. 
	function clearOrderList_to_HCLAB($refno) {
		global $db;
		$ret=FALSE;
		#$refno = $dbdest->qstr($refno);
		$this->sql = "DELETE FROM $this->tb_pat_order_details WHERE POD_TRX_NUM=$refno";
		
    #return $this->Transact();
	#for mysql
	/*
	 if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
			else return FALSE;
	*/		
			
		# for oracle
		
		if ($this->dbdest->Execute($this->sql)) {
			if ($this->dbdest->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
			else return FALSE;	
			
	}
	
	# add order request details in HCLAB order details
	
	function addOrders_to_HCLAB($refno, $orderArray) {
		global $db;

		#$refno = $dbdest->qstr($refno);
		#$this->sql = "INSERT INTO $this->tb_lab_servdetails(refno,service_code,price_cash,price_charge) VALUES($refno,?,?,?)";
		#$this->sql = "INSERT INTO $this->tb_pat_order_details(POD_TRX_NUM,POD_TEST_CODE,POD_TEST_NAME,POD_INSURANCE_CODE) VALUES($refno,?,?,?)";
		print_r($orderArray);
		
		$this->sql = "INSERT INTO $this->tb_pat_order_details VALUES($refno,?,?,?)";
		#$this->sql = "INSERT INTO $this->tb_pat_order_details(POD_TRX_NUM,POD_TEST_CODE,POD_TEST_NAME) VALUES($refno,?,?)";
		
		#for mysql
		/*		
		#if($buf=$db->Execute($this->sql,$orderArray)) {
		#	if($buf->RecordCount()) {
		#		return true;
		#	} else { return false; }
		#} else { return false; }
		*/
		
		# for oracle
		/*
		#if($buf=$this->dbdest->Execute($this->sql,$orderArray)) {
		#	if($buf->RecordCount()) {
		#		return true;
		#	} else { return false; }
		# else { return false; }
		#
		*/
		define('__ALVIN_EXEC',1);
		#echo "}}".$this->sql;
		if ($this->dbdest->Execute($this->sql,$orderArray)) {
			if ($this->dbdest->Affected_Rows()) {
				echo "true";
				$ret=TRUE;
			}
		}
		define('__ALVIN_EXEC',NULL);
		
		echo "<br>error = ".$this->dbdest->ErrorMsg();
		if ($ret)	return TRUE;
			else return FALSE;	
	}
	
	/*
	function addOrders_to_HCLAB($refno, $orderArray) {
		if (!($this->dbdest->Replace($this->tb_pat_order_details, $orderArray, $refno))) {
			ShowErrorMsg('Error adding: '.$this->dbdest->ErrorMsg());
			$this->bIsOk = false;				
		}
	}
	*/
	
	/*
	function addOrderD_to_HCLAB($data){
		global $db;

		extract($data);
		
		$this->useLabOrder();
		
		$arrayItems = array();
		foreach ($items as $key => $value){
			if (floatval($pcash[$key])==0){
				$tempArray = array($value);
				array_push($arrayItems,$tempArray);
			}
		}

		if (empty($arrayItems))
			return TRUE;
			
		$index = "POD_TRX_NUM, POD_TEST_CODE, POD_TEST_NAME, POD_INSURANCE_CODE";
		$values = "$refno, ?, ?, ''";   # NOTE: 'LD'=laboratory

		$this->sql="INSERT INTO PAT_ORDERD ($index) VALUES ($values)";

		if ($db->Execute($this->sql,$arrayItems)) {
			if ($db->Affected_Rows()) {
				return TRUE;
			}else{ return FALSE; }
		}else{ return FALSE; }
		
	}# end of function saveLabRequest_to_HCLAB
*/
	
	#save all the data from HCLAB result header to SEGHIS database seg_lab_result (CRON scheduler)
	function saveResultH_to_SEGHIS(){
		#seg_lab_result - header
	
	}
	
	#save all the data from HCLAB result details to SEGHIS database seg_lab_result_details (CRON scheduler)
	function saveResultD_to_SEGHIS(){
		#seg_lab_result_details - details
	
	}
	
	# already functioning and connected to HCLAB
	
	function getResult_to_HCLAB($refno, $service_code){
		
		$this->sql="SELECT PAT_RESULTH.*, PAT_RESULTD.*, to_char(PAT_RESULTD.PRD_REPORTED_DT, 'MM/DD/YYYY HH12:MI:SS AM') AS PRD_REPORTED_DT
		            FROM PAT_RESULTH 
						INNER JOIN PAT_RESULTD 
						ON PAT_RESULTH.PRH_TRX_NUM = PAT_RESULTD.PRD_TRX_NUM 
						WHERE PRD_TRX_NUM=$refno 
						AND PRH_TEST_CODE='$service_code'";		
		
		#echo "sql = ".$this->sql;
		#$this->sql = "SELECT * FROM PAT_RESULTH WHERE rownum<=10";
		
		if ($this->result=$this->dbdest->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
	   	return FALSE;
		}
	}
	
	function getResultHeader_to_HCLAB($refno){
		$this->sql="SELECT PAT_RESULTH.*, to_char(PAT_RESULTH.PRH_ORDER_DT, 'MM/DD/YYYY HH12:MI:SS AM') AS PRH_ORDER_DT
		            FROM PAT_RESULTH 
						WHERE PRH_TRX_NUM =$refno";						
		#echo "sql = ".$this->sql;
		if ($this->result=$this->dbdest->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			#return $this->result;
			return $this->result->FetchRow();;
		} else{
	   	return FALSE;
		}
	}
	
	function getResult_Header_Current(){
		#$this->sql = "SELECT MONTHS_BETWEEN(SYSDATE-365, SYSDATE+365)
		#              FROM PAT_RESULTH";
		
		/*
		$this->sql = "SELECT SYSDATE, ADD_MONTHS (SYSDATE, 5) Add5,
						  ADD_MONTHS (SYSDATE, 13) Add13,
						  ADD_MONTHS (SYSDATE, -4) Less4, 
						  to_char(ADD_MONTHS (SYSDATE, -2), 'MM/DD/YYYY') Less2
						  FROM PAT_RESULTH";
		*/
		#TWO MONTHS AGO
		/*
		$this->sql = "SELECT PAT_RESULTH.*
						  FROM PAT_RESULTH
						  WHERE to_char(PRH_ORDER_DT, 'MM/DD/YYYY') >= to_char(ADD_MONTHS (SYSDATE, -2), 'MM/DD/YYYY')
						  AND to_char(PRH_ORDER_DT, 'MM/DD/YYYY') <= to_char(SYSDATE, 'MM/DD/YYYY')
						  ORDER BY PRH_ORDER_DT ASC";
		*/
		#1 MONTH AGO
		/*
		$this->sql = "SELECT PAT_RESULTH.*
		              FROM PAT_RESULTH
						  WHERE to_char(PRH_ORDER_DT, 'MM/DD/YYYY') >= to_char(ADD_MONTHS (SYSDATE, -1), 'MM/DD/YYYY')
						  AND to_char(PRH_ORDER_DT, 'MM/DD/YYYY') <= to_char(SYSDATE, 'MM/DD/YYYY')
						  ORDER BY PRH_ORDER_DT ASC";
		*/
		#ALL RESULT HEADER
		/*
		$this->sql = "SELECT PAT_RESULTH.*
		              FROM PAT_RESULTH
						  ORDER BY PRH_ORDER_DT ASC";
		*/
		
		#ALL RESULT DETAILS				  
		$this->sql = "SELECT PAT_RESULTD.*
		              FROM PAT_RESULTD
						  ORDER BY PRD_TRX_NUM ASC";
						  				  
		if ($this->result=$this->dbdest->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
			return $this->result;
		} else{
	   	return FALSE;
		}
	}

	
}/* end of class HCLAB */

?>