<?php 
	require('./roots.php');
	require($root_path.'classes/adodb/adodb.inc.php');
	include($root_path.'include/inc_init_hclab_main.php');
	#include($root_path.'include/inc_seg_mylib.php');
	
	require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
	$hclabObj = new HCLAB;
	
	echo "<br><br>ORACLE DATABASE CONNECTION : $dsn<br>";
	$objconn = $hclabObj->ConnecttoDest($dsn);	# Connect to SegAccounting
	
	if ($objconn) {
		echo "<br>connected to oracle";
		#$query = $hclabObj->getResult_to_HCLAB('527688', 'CBCPLT');
		#$query = $hclabObj->getResult_to_HCLAB('505745', 'HBHCTP');
		$query = $hclabObj->getResult_to_HCLAB('1665160', 'CBC');
		
	    echo "<br>sql = ".$hclabObj->sql;
		echo "<br>count = ".$hclabObj->count;
		
		while ($row=$query->FetchRow()) {
			echo "<br> data = ".$row['PRH_TRX_NUM']." - ".$row['PRH_PAT_NAME']." - ".$row['PRD_TEST_CODE'];
		}
		
		#$query_info = $hclabObj->getResultHeader_to_HCLAB('873579');
		$query_info = $hclabObj->getResultHeader_to_HCLAB('505745');
		
		echo "<br><br>";
		print_r($query_info);
		/*
		echo "<br><br>";
		
		$date_query = $hclabObj->getResult_Header_Current();
		echo "<br>count = ".$hclabObj->count;
		
		while ($date_row=$date_query->FetchRow()) {
			echo "<br> data = ".$date_row['PRH_TRX_NUM']." - ".$date_row['PRH_PAT_NAME']." - ".$date_row['PRH_TRX_DT']." - ".$date_row['PRH_ORDER_DT']." - ".$date_row['MonthFrom']." - ".$date_row['OrderDate'];
		}
		*/
	}else{
		echo "<br>not successfully connected";
	}	
	
?>