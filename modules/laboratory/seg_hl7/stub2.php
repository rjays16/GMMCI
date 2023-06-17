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
    
$ftp_ip       = "127.0.0.1";
$ftp_username = "hl7";
$ftp_password = "hl7";

if($ftp=ftp_connect($ftp_ip))
{
    echo "<br>it is now connected<br>";
    if(ftp_login($ftp,$ftp_username,$ftp_password))
    {
        echo "<br>it is now connected with authentication<br>";

        // Set to PASV mode
        // turn passive mode on (1)
        ftp_pasv( $ftp, 1);
        
        #$local_file = '/srv/www/html/hisdmc/modules/laboratory/seg_hl7/hl7_file_temp/sample.HL7';
        $local_file = "D://HL7Host/Outbox/HIS20120000000186.HL7";
        $server_file = 'ftp://'.$ftp_username.':'.$ftp_password.'@'.$ftp_ip.'/Inbox/HIS20120000000186.HL7';
        $dir = 'ftp://'.$ftp_username.':'.$ftp_password.'@'.$ftp_ip.'/Inbox/';
        echo $server_file;
        
        #is_dir('ftp://user:password@example.com/some/dir/path');
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                echo "<p>it is a new directory.\n";
                while (($file = readdir($dh)) !== false) { 
                    
                    if (file_exists($server_file)) {
                        $handle = fopen($server_file, "rb");
                        if (!$handle) {
                            echo "<p>Unable to open remote file.\n";
                        }else{
                            #get file content
                            #$contents = '';
                            while (!feof($handle)) {
                                $contents .= fread($handle, 8192);
                            }
                            echo "<p>connected.\n";
                            
                            /*// open some file to write to
                            $handle_loc = fopen($local_file, 'r+b');
                            
                            if(ftp_fget($ftp, $handle_loc, $server_file, FTP_BINARY, 0)) {
                                echo "<br>Successfully written to $local_file\n";
                            } else {
                                echo "<br>There was a problem\n";
                            }*/
                        
                            fclose($handle);
                        }                             
                        echo "<p>remote file exist.\n";
                        
                    }else{
                        echo "<p>remote file doesn't exist.\n";
                    }     
                }    
            }    
        }else{
            echo "<p>Is not a directory.\n";    
        }
    } 
    ftp_close($ftp);
    #echo "<br>msg = ".$contents;
}
?>
