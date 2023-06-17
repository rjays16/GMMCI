<?php
/*require_once './roots.php';
require_once $root_path.'include/inc_environment_global.php'; 
require_once $root_path.'classes/Net/Socket.php';
include_once $root_path.'include/care_api_classes/class_hospital_admin.php';
$socket = new Net_Socket() ; 
$e = $socket->connect("192.168.15.102", 9000, true, 5); 
echo $e -> message;*/


/*$s_socket_uri = 'tcp://localhost:8000';
// establish the server on the above socket
$s_socket = stream_socket_server($s_socket_uri, $errno, $errstr, 30) OR
    trigger_error("Failed to create socket: $s_socket_uri, Err($errno) $errstr", E_USER_ERROR);
$s_name = stream_socket_get_name($s_socket, false) OR
    trigger_error("Server established, yet has no name.  Fail!", E_USER_ERROR);
if (!$s_socket || !$s_name) {return false;}

#
#   Wait for connections, handle one client request at a time
#   Though to not clog up the tubes, maybe a process fork is
#   needed to handle each connection?
#
while($conn = stream_socket_accept($s_socket, 60, $peer)) {
    stream_set_blocking($conn, 0);

    // Get the client's request headers, and all POSTed values if any
    echo "Connected with $peer.  Request info...\n";
    $client_request = stream_get_contents($conn);
    if (!$client_request) {
        trigger_error("Client request is empty!");
        }
    echo $client_request."\n\n";  // just for debugging

    #<Insert request handling and logging code here>
   
    // Build headers to send to client
    $send_headers = "HTTP/1.0 200 OK\n"
        ."Server: mine\n"
        ."Content-Type: text/html\n"
        ."\n";

    // Build the page for client view
    $send_body = "<h1>hello world</h1>";

    // Make sure the communication is still active
    if ((int) fwrite($conn, $send_headers . $send_body) < 1) {
        trigger_error("Write to socket failed!");
        }

    // Response headers and body sent, time to end this connection
    stream_socket_shutdown($conn, STREAM_SHUT_WR);
    }*/

    #$addr = gethostbyname("www.yahoo.com");
    #$addr = "192.168.15.102";
    #$port = "9000";

    #$client = stream_socket_client("tcp://" . $addr . ":" . $port, $errno, $errstr, 30);

    #if ($client === false) {
    #    throw new UnexpectedValueException("Failed to connect: $errorMessage");
    #}

    #fwrite($client, "GET / HTTP/1.0\r\nHost: www.example.com\r\nAccept: */*\r\n\r\n");
    #echo stream_get_contents($client);
    #fclose($client);
    
     /* $fp = stream_socket_client("tcp://" . $addr . ":" . $port, $errno, $errstr, 30);
      if (!$fp) {
           echo "$errstr ($errno)<br />\n";
      } else {
          fwrite($fp, $msg);
      } */
      
      /*$host="192.168.15.132"; 
        $port = 2869; // open a client connection 
        $fp = fsockopen ($host, $port, $errno, $errstr); 
        if (!$fp) { 
        $result = "Error: could not open socket connection"; 
        } 
        else { // get the welcome message fgets ($fp, 1024); // write the user string to the socket 
        fputs($fp, $message); // get the result $result .= fgets ($fp, 1024); // close the connection
        fputs ($fp, "END");
        fclose ($fp); // trim the result and remove the starting ?
        $result = trim($result);
        $result = substr($result, 2); // now print it to the browser 
        } 
        echo "Server said: ".$result; */
        
        /*$host = "127.0.0.1";
        $port = 5353;
        // No Timeout 
        set_time_limit(0);
        
        $sock = socket_create(AF_INET, SOCK_STREAM, 0) or die("Socket create
        error\n");

        socket_bind($sock, $hostname, $portno) or die("Socket bind error\n");

        socket_listen($sock, 3) or die("Could not set up socket
        listener\n");

        while(1){
        $accept = socket_accept($sock) or die("Could not accept incoming
        connection\n");

        $output = "\nHTTP/1.1 200 OK

        <h1>Hello World 2011</h1>";
        socket_write($accept, $output, strlen ($output)) or die("Could not write
        output\n");

        socket_close($accept);
        }

        socket_close($sock);*/
        
        $address="192.168.1.185";
        $port='9000';
        
        if(false==($socket=  socket_create(AF_INET,SOCK_STREAM, SOL_TCP)))
        {
            echo "could not create socket";
        }
	else {
	    if (socket_connect($socket, $address, $port)) {
		echo "Socket connected!";
		#socket_close($socket);
	    }
	}
#        $errorcode = socket_last_error();
#        $errormsg = socket_strerror($errorcode);
#        socket_bind($socket, $address, $port) or die ("could not bind socket [$errorcode] $errormsg");
#        socket_listen($socket);
#        if(($client=socket_accept($socket)))
#            echo "client is here";
?>
