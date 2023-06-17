<?php
    #for cron schedule
    #per minute
	# created by VAN 01-12-2012
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
    $objInfo = new Hospital_Admin();
    
    require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
    $srvObj=new SegLab();
    
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_transport_hl7_file.php');
    
    global $db;
    
    $row_hosp = $objInfo->getAllHospitalInfo();
    $details = (object) 'details';
    $details->address_lis = $row_hosp['LIS_address'];
    $details->port = $row_hosp['LIS_port'];
    $details->protocol_type = $row_hosp['LIS_protocol_type'];
    #$details->address_lis = "192.168.1.101";
    #$details->port = 9000;
    #$details->protocol_type = "tcp";
    print_r($details);
    $transportObj = new seg_transport_HL7_file($details); 
    
    #$rs = $transportObj->check_connection($details->address, $details->port);
    $text = "Checking Connection Thru Socket";
    echo "<html><head></head><body>".
            $text.
         "</body></html><br>";
    
    $message = "trial to hl7 message. check this out";
    
    if ($transportObj->isConnected()){
        echo "Connection successful on IP ".$details->address_lis.", port ".$details->port;
        
        $sql = " SELECT hl7_msg
                 FROM seg_hl7_lab_tracker
                 WHERE msg_control_id='20120000211863'";
        $rs = $db->execute($sql);
        if ($db->Affected_Rows()) {
            $row = $rs->FetchRow();
            #echo $row['hl7_msg'];
            $message = $row['hl7_msg'];
            #$filename = $row['filename']; 
            
            #send string to server
            socket_write($transportObj->socket, $message, strlen($message))  or  
                    die("<br><br>Could not send data to LIS server");
            
            #get server response
            $result = socket_read ($transportObj->socket, 1024) or 
                    die("<br><br>Could not read LIS server response\n");
            
        }else{
            $text2 = "Error : ".$sql;
            echo "<html><head></head><body>".$text2."</body></html>";
        }
        
        socket_close($transportObj->socket);        
    }else
        echo  $transportObj->errortext;
?>
