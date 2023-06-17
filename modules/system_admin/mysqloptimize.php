<?php

/*
 * Routine to be called in lynx run by cron to do optimization of tables
 * in MySQL DB.
 * 
 */
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');

    echo '<pre>' . "\n";
    set_time_limit(0);

    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $start = $time;

    //Connection variables :
//    $h = 'localhost';
//    $u = 'root';
//    $p = 'password';
    
//    $h = $dbhost;
//    $u = $dbusername;
//    $p = $dbpassword;
    
//    $target_db = 'test';
//    $target_db = $dbname;

    /*The php->mysql API needs to connect to a database even when executing scripts like this.
    If you got an error from this(permissions),
    just replace this with the name of your database*/        

//    $db_link = mysql_connect($h,$u,$p);
//    if (!mysql_select_db($target_db, $db_link)) {
//        echo 'Could not select database\n';
//        exit;
//    }
    
//    $res = mysql_db_query($target_db, 'SHOW DATABASES', $db_link) or die('Could not connect: ' . mysql_error());    
//    $res = mysql_query('SHOW DATABASES', $db_link);

    $res = $db->Execute('PURGE BINARY LOGS BEFORE DATE_SUB(DATE(NOW()), INTERVAL 4 DAY)');
    if (!$res) {
	echo "DB Error, could not purge the binary logs\n";
	echo "MySQL Error: ".$db->ErrorMsg();
    }
    
    $res = $db->Execute('SHOW DATABASES');
    if (!$res) {
        echo "DB Error, could not query the database\n";
        
//        echo 'MySQL Error: ' . mysql_error();
        
        echo 'MySQL Error: ' . $db->ErrorMsg();
        exit;
    }    
    
//    echo 'Found '. mysql_num_rows( $res ) . ' databases' . "\n";
    
    echo 'Found '. $res->RecordCount() . ' databases' . "\n";
    $dbs = array();
    
//    while ( $rec = mysql_fetch_array($res) ) {
    
    while ( $rec = $res->FetchRow()) {
        $dbs[] = $rec[0];
    }

    foreach ( $dbs as $db_name ) {        
        echo "Database : $db_name \n\n";
        
//        $res = mysql_db_query($target_db, "SHOW TABLE STATUS FROM `" . $db_name . "`", $db_link) or die('Query : ' . mysql_error());
//        $res = mysql_query("SHOW TABLE STATUS FROM `" . $db_name . "`", $db_link);
        
        $dblink = $db->Connect($dbhost, $dbusername, $dbpassword, $db_name);
        if (!$dblink) {
            echo "DB connection to ".$db_name." NOT OK!";
            continue;
        }
        
        $res = $db->Execute("SHOW TABLE STATUS FROM ". $db_name);
        $to_optimize = array();
//        while ( $rec = mysql_fetch_array($res) ) {
        while ( $rec = $res->FetchRow() ) {
            if ( $rec['Data_free'] > 0 ) {
                $to_optimize[] = $rec['Name'];
                echo $rec['Name'] . ' needs optimization' . "\n";
            }
        }
        if ( count ( $to_optimize ) > 0 ) {
            foreach ( $to_optimize as $tbl ) {
//                mysql_db_query($db_name, "OPTIMIZE TABLE `" . $tbl ."`", $db_link );
//                if (mysql_query("OPTIMIZE LOCAL TABLE " . $tbl, $db_link)) 
                if ( $db->Execute("OPTIMIZE LOCAL TABLE ".$tbl) )
                    echo "`" . $tbl ."` successfully optimized!" . "\n";
                else
                    echo "`" . $tbl ."` NOT OPTIMIZED!" . "\n";
            }
        }
    }

    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $finish = $time;
    $total_time = round(($finish - $start), 6);
    echo 'Parsed in ' . $total_time . ' secs' . "\n\n";
?>
