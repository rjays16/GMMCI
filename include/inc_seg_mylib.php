<?php
function ShowErrorMsg_Browser($msg) {
//	echo '<html><body>';
	echo '<table align="center"><tr>';
	echo '<td style="color:#990000; text-align:center; font-size:large; border-width:1px; '.
		 '    border-color:#000000; border-style:solid; border-radius: 20px; border-collapse: collapse; '.
		 '    -moz-border-radius: 20px; padding: 15px">';
	if ($msg == '') {
		echo '<p>Error: Database connection failed.</p>'.'<p>It is possible that the database is overloaded or otherwise not running properly.</p>'.
			 '<p>The site administrator should also check that the database details have been correctly specified in config.php</p>';	}
	else {
		echo $msg;
	}
	echo '</td></tr></table>';
//	echo '</body></html>';
}

function ShowErrorMsg_Old($msg) {
	if ($msg == '') {
		$msg = "Error: Database connection failed.\nIt is possible that the database is overloaded or otherwise not running properly.\n".
			   "The site administrator should also check that the database details have been correctly specified in config.php\n";	
	}	
	
	$i = strrpos($msg, '\n');
	if (!($i === false))  {
		$i += 1;
		if ($i != (strlen($msg) - 1))
			$bAddBrk = true;
		else
			$bAddBrk = false;
	}
	else
		$bAddBrk = true;
		
	fwrite(STDERR, $msg);
	if ($bAddBrk) fwrite(STDERR, "\n");
}

function ShowErrorMsg($msg) {
	if ($msg == '') {
		$msg = "Error: Database connection failed.\nIt is possible that the database is overloaded or otherwise not running properly.\n".
			   "The site administrator should also check that the database details have been correctly specified in config.php\n";	
	}		
	echo $msg."<br>";
}

function configure_dbconnection($db, $dbSrcType) {
	GLOBAL $unicodedb;
	#echo "<br>db, dbSrcType = '".$db."' - '".$dbSrcType."'";	
    switch ($dbSrcType) {
        case 'mysql':
        /// Set names if needed
            if ($unicodedb) {
                $db->Execute("SET NAMES 'utf8'");
            }
            break;
        case 'postgres7':
        /// Set names if needed
            if ($unicodedb) {
                $db->Execute("SET NAMES 'utf8'");
            }
            break;
        case 'mssql':
        case 'mssql_n':
        case 'odbc_mssql':
        /// No need to set charset. It must be specified in the driver conf
        /// Allow quoted identifiers
            $db->Execute('SET QUOTED_IDENTIFIER ON');
        /// Force ANSI nulls so the NULL check was done by IS NULL and NOT IS NULL
        /// instead of equal(=) and distinct(<>) simbols
            $db->Execute('SET ANSI_NULLS ON');
        /// Enable sybase quotes, so addslashes and stripslashes will use "'"
            ini_set('magic_quotes_sybase', '1');
        /// NOTE: Not 100% useful because GPC has been addslashed with the setting off
        ///       so IT'S MANDATORY TO CHANGE THIS UNDER php.ini or .htaccess for this DB
        ///       or to turn off magic_quotes to allow Moodle to do it properly
            break;
        case 'oci8po':
        /// No need to set charset. It must be specified by the NLS_LANG env. variable
        /// Enable sybase quotes, so addslashes and stripslashes will use "'"
            ini_set('magic_quotes_sybase', '1');
        /// NOTE: Not 100% useful because GPC has been addslashed with the setting off
        ///       so IT'S MANDATORY TO ENABLE THIS UNDER php.ini or .htaccess for this DB
        ///       or to turn off magic_quotes to allow Moodle to do it properly
            break;
		 case 'odbc_oracle':
        /// No need to set charset. It must be specified by the NLS_LANG env. variable
        /// Enable sybase quotes, so addslashes and stripslashes will use "'"
            ini_set('magic_quotes_sybase', '1');
        /// NOTE: Not 100% useful because GPC has been addslashed with the setting off
        ///       so IT'S MANDATORY TO ENABLE THIS UNDER php.ini or .htaccess for this DB
        ///       or to turn off magic_quotes to allow Moodle to do it properly
            break;		
		case 'odbc':
        /// No need to set charset. It must be specified by the NLS_LANG env. variable
        /// Enable sybase quotes, so addslashes and stripslashes will use "'"
            ini_set('magic_quotes_sybase', '1');
        /// NOTE: Not 100% useful because GPC has been addslashed with the setting off
        ///       so IT'S MANDATORY TO ENABLE THIS UNDER php.ini or .htaccess for this DB
        ///       or to turn off magic_quotes to allow Moodle to do it properly
            break;				
    }
}

?>