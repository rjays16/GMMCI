<?php
    #$remote_server = "192.168.2.33";
    $remote_server = "192.168.1.154";
    $port = "1521";
    #$port = "80";
    #$is_connected1 = ($sock = fsockopen($remote_server, 80)) ? true : false;
    $is_connected = ($sock = fsockopen($remote_server, $port)) ? true : false;
    echo "con = ".$is_connected."<br>";
    
    #$IP =  gethostbyaddr($remote_server);
    #echo "con1 = ".$IP."<br>";
    
    /*if(intval($IP)>0){ 
        $ServerIP = gethostbyaddr($IP); 
      } else { 
        $ServerIP = $IP; // A bad address. 
      }
      
    echo "<br>address = ".$ServerIP;*/
      
      
    /*exec("ping -n 4 $IP 2>&1", $output, $retval);
    if ($retval != 0) { 
        echo "<br>no!"; 
    }else{ 
        echo "<br>yes!"; 
    }*/ 
    
    /*$ping_ex = exec("ping -n 1 $IP", $ping_result, $pr);
    if (count($ping_result) > 1){
        echo "<br>on";
    } else{
        echo "<br>off";
    }   */
    
    
    /*function ping($host) {
        exec(sprintf('ping -c 1 -W 5 %s', escapeshellarg($host)), $res, $rval);
        return $rval === 0;
      }

    $hosts_to_ping = array('192.168.2.225', '127.0.0.1', '192.168.2.33');
    
    foreach ($hosts_to_ping as $host){
        echo "<br>".$host;
        $up = ping($host);
        
        if ($up){
            echo "===> on";
        } else{
            echo "===> off";
        }
    }*/     
      

      
      
    /*$address = $remote_server;
    $port = 80;*/
    
    #system("ping -c1 -q -w1", $retval);
    #system("ping $remote_server", $retval);

    #$service_port = getservbyname($this->protocol, $this->protocol_type)

    /*if (!(isset($port)&&(!strlen($port==0))))
        unset($port);
                
    if (isset($port) && ($socket=socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) 
        && ($result=socket_connect($socket, $address, $port))){
                  
        $msg = "Connection successful on IP $address, port $service_port";
        #echo "true msg => ".$this->msg;
        $is_connected = true;
        socket_close($socket);
    }else{
        if ($socket < 0) {
            $error = "socket_create() failed: reason: " . socket_strerror($socket) . "\n";
        }elseif ($result < 0) {
            $error = "socket_connect() failed.\nReason: ($result) " . socket_strerror($result) . "\n";
        } else {
        $error = socket_strerror(socket_last_error());
    }
                  
    $msg = "Attempting to connect to '$address' on port '$port'...";
    #echo "false msg => ".$this->msg;
    $is_connected = false;
   }
   echo "<br>con = ".$is_connected."<br>";*/
                #$address = '192.168.2.230';
                /*$address = '192.168.1.154';
                $port = '1521';
                
                set_time_limit (0);
                ob_implicit_flush();

                // Set the ip and port we will listen on
                // Create a TCP Stream socket

                #$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
                #echo "<br>sock = ".$this->socket;

                // Bind the socket to an address/port
                
                #socket_bind($this->socket, $this->address, $this->port); #or die('Could not bind to address '.$this->address." port ".$this->port);

                // Start listening for connections

               # socket_listen($this->socket); #or die('Could not listen to address '.$this->address." port ".$this->port);
                
                if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
                    echo "<br>socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
                }
                echo "<br>sock = ".$sock; 

                if (socket_bind($sock, $address, $port) === false) {
                    echo "<br>socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
                }

                if (socket_listen($sock, 5) === false) {
                    echo "<br>socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
                }

                # Accept incoming requests and handle them as child processes

                $client = socket_accept($sock);
                
                echo "<br>client = ".$client;

                // Read the input from the client &#8211; 1024 bytes

                $input = socket_read($client, 1024);
                echo "<br>input = ".$input;

                // Strip all white spaces from input

                $output = ereg_replace("[ \t\n\r]","",$input).chr(0);
                echo "<br>output = ".$output;

                // Display output back to client

                socket_write($client, $output);

                // Close the client (child) socket

                socket_close($client);

                // Close the master sockets

                socket_close($sock);  */
?>
