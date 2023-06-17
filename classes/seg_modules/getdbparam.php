<?php
define('DB_CONFIGFILE', 'dbinfo.conf');

class DBParam {
	function getconnectparams(&$server, &$userID, &$password, &$database) {
		//echo $server." ".$userID." ".$password." ".$database;
	
		$fstring = file_get_contents(DB_CONFIGFILE);
		if ($fstring == FALSE) die("Error in opening configuration file!");
		$line_array = explode("\n", $fstring);
		
		$server   = trim(substr($line_array[0], strpos($line_array[0], '=') + 1));
		$userID   = trim(substr($line_array[1], strpos($line_array[1], '=') + 1));
		$password = trim(substr($line_array[2], strpos($line_array[2], '=') + 1));
		$database = trim(substr($line_array[3], strpos($line_array[3], '=') + 1));		
	}
}
?>
