<?php
	# created by VAN 02-15-2012
	# transmission of HL7 file
    # folder at LIS server with PDF result, \\hclab\hcini\pdf
    #socket_create
    #socket_bind
    #socket_connect
    
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
        
    class seg_transport_HL7_file{

        var $is_connected;
        var $type;
        var $address;
        var $port;
        var $username;
        var $password;
        var $directory;
        var $service_timeout;
        var $directory_LIS;
        var $error;
        var $msg;
        var $socket;
        var $folder_LIS;
        var $hl7extension;
         
        # constructor
		function seg_transport_HL7_file($connectInfo){
            $this->protocol_type = $connectInfo->protocol_type;
            $this->protocol = $connectInfo->protocol;
            
            $this->address_lis = $connectInfo->address_lis;
            $this->port = $connectInfo->port;
            $this->username = $connectInfo->username;
            $this->password = $connectInfo->password;
            $this->directory = $connectInfo->directory;
            $this->service_timeout = $connectInfo->service_timeout;
            $this->directory_LIS = $connectInfo->directory_LIS;
            $this->folder_LIS = $connectInfo->folder_LIS;
            $this->hl7extension = $connectInfo->hl7extension;
            
            #tcp connection
            if ($this->protocol_type=='tcp'){
              #$this->is_connected = ($this->socket = fsockopen($this->address, $this->port, $errno, $errstr, 30)) ? true : false; 
              $this->is_connected = $this->with_connection($this->address_lis, $this->port) ? true : false; 
            
              if (!$this->is_connected)
                $this->is_connected = false;
                
            }else{
                $this->is_connected = true;     
            }
		    
        }
        
        function isConnected(){
            return $this->is_connected;
        }
        
        function socket(){
            return $this->socket;
        }
        
        function sendHL7MsgtoSocket($message){
            #create socket
            $socket = socket_create(AF_INET, SOCK_STREAM, 0) 
                or $this->error = "Could not create socket\n";
            #connect to server
            $result = socket_connect($socket, $this->address_lis, $this->port) 
                or $this->error = "Could not connect to LIS server\n";
            socket_read ($socket, 1024) 
                or $this->error = "Could not read LIS server response\n";
            #send string to server
            socket_write($socket, $message, strlen($message)) 
                or $this->error = "Could not send data to LIS server\n";
            #get server response
            $result = socket_read ($socket, 1024) 
                or $this->error = "Could not read LIS server response\n";
            #end session
            socket_write($socket, "END", 3) 
                or $this->error = "Could not end session\n";
            #close socket
            socket_close($socket);
            #clean up result
            $result = trim($result);
            $result = substr($result, 0, strlen($result)-1);
            #echo "result = ".$result;
            return $result;
        }
        
        function with_connection($address, $port){
            if (isset($port) &&
                ($this->socket=socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) &&
                (socket_connect($this->socket, $address, $port))
               ){
                    #$text="Connection successful on IP $address, port $port";
                    $result = true;
                    #socket_close($this->socket);
            }else{
                $result = false;
                $this->errortext="Unable to connect<pre>".socket_strerror(socket_last_error())."</pre>";
            }    
            
            return $result;  
        }
        
        function ftp_transfer($file, $message){
            #echo "<br>ftp transfer = ".$this->address_lis."==".$this->username."==".$this->password;
            if($ftp=ftp_connect($this->address_lis)){
                #echo "<br>address yes";
                if(ftp_login($ftp,$this->username,$this->password)){
                    #echo "<br>authentication yes";
                    // Set to PASV mode
                    // turn passive mode on (1)
                    ftp_pasv( $ftp, 1);
        
                    $dir = 'ftp://'.$this->username.':'.$this->password.'@'.$this->address_lis.'/'.$this->folder_LIS.'/';
                    #echo "s = ".$dir;
                    if (is_dir($dir)) {
                        #echo "<br>dir yes";
                        if ($dh = opendir($dir)) {
                            #echo "<br>open dir yes";
                            #create a filename in the local and delete after
                            $ourFileName = $dir.$file.$this->hl7extension;
                            #delete the file in the LIS folder and copy a new one
                            unlink($ourFileName);
                            $ourFileHandle = fopen($ourFileName, 'wb');
                            if (!$ourFileHandle) {
                                #echo "<p>Unable to open remote file.\n";
                            }else{
                                fwrite($ourFileHandle, $message);
                                fclose($ourFileHandle);
                                #echo "ok...";
                                #unlink($ourFileName);
                            }
                            closedir($dir);
                        }    
                        $ok = 1;
                    }else{
                        #echo "<br>not a dir";
                        $ok = 0;
                    }                           
            
                }
           
                ftp_close($ftp);
           }
           return $ok;
        }    
        
    }    
    #------- end of class--------

?>
