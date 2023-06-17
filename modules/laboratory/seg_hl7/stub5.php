<?php

error_reporting(E_ALL);

/* Allow the script to hang around waiting for connections. */
set_time_limit(0);

/* Turn on implicit output flushing so we see what we're getting
 * as it comes in. */
ob_implicit_flush();

#$address = '192.168.1.154';
#$port = 9000;
$address = '192.168.1.185';
$port = 9000;                
#$address = '192.168.1.13';
#$port = 80;

/*if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
} */

#if (socket_bind($sock, $address, $port) === false) {
#    echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
#}

/*if (socket_listen($sock, 5) === false) {
    echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
} */

#echo exec('ping -n 1 -w 1 192.168.1.185');
#echo shell_exec('nmap -p 9000 192.168.1.185');


if (!$socket = @pfsockopen("$address", $port, $errno, $errstr)) 
{
  echo "<font color='red'><strong>Offline!</strong></font>".$address.", port ".$port;
}
else 
{
  echo "<font color='green'><strong>Online!/strong></font>".$address.", port ".$port;


  fclose($socket);
}
                
?>