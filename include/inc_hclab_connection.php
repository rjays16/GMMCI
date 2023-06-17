<?php

		require('./roots.php');
		# ADODB connection
		require_once($root_path.'classes/adodb/adodb.inc.php');
		#require($root_path.'include/inc_environment_global.php');
		#added by VAN 11-17-09
		#connect to LIS (oracle)
		#echo "dsn = ".$dsn;

		$dblink_hclab_ok=0;
		require($root_path.'include/inc_init_hclab_main.php');
		if ($dsn){
				$db_hclab = &ADONewConnection("$DBType_Oracle");
				require_once($root_path.'include/care_api_classes/class_hclab_oracle.php');
				#$_SESSION['counterHCLAB'].=".";
				$dblink_hclab_ok = $db_hclab->PConnect($DBHost_Oracle, $DBUser_Oracle, $DBPassword_Oracle, $DBName_Oracle);
		}

		#-------------------------
?>