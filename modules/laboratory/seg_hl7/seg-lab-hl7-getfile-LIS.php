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
    
    $row_hosp = $objInfo->getAllHospitalInfo();
    $details = (object) 'details';
    $details->address = $row_hosp['LIS_address'];
    $details->username = $row_hosp['LIS_username'];
    $details->password = $row_hosp['LIS_password'];
    #$details->directory_inbox = "//".$details->address.$row_hosp['LIS_folder_path_inbox'];
    #$details->directory_pdf = "//".$details->address.$row_hosp['LIS_folder_path_pdf'];
    #$details->directory_inbox = "";
    #$details->directory_pdf = "";
    
    if($ftp_connection=ftp_connect($details->address)){
        if(ftp_login($ftp_connection,$details->username,$details->password)){
            // Set to PASV mode
            // turn passive mode on (1)
            ftp_pasv( $ftp, 1);
        
            #$dir = $details->directory_inbox;
            $dir = 'ftp://'.$details->username.':'.$details->password.'@'.$details->address.'/'.$row_hosp['LIS_folder_path_inbox'].'/';
            echo "<br>Start reading the HL7 message from LIS...";
            
            // Open a known directory, and proceed to read its contents
            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        if ($file != "." && $file != ".."){
                            #get the file
                            $path_file =  $dir.$file;
                            #check if file exists
                            if (file_exists($path_file)) {
                                $handle = fopen($path_file, "rb");
                                if (!$handle) {
                                    echo "<p>Unable to open remote file.\n";
                                }else{
                                    #check only the HL7 file
                                    if (!stristr($file,'.HL7')===FALSE){
                                        $contents = '';
                                        while (!feof($handle)) {
                                          $contents .= fread($handle, 8192);
                                        }
                                        fclose($handle);
                                        
                                        #save to database
                                        #table : seg_hl7_file_received
                                        $details->filename = $file;
                                        $details->hl7_msg = $contents;
                                        $ok = $srvObj->addInfo_HL7_file_received($details);
                                       
                                        if ($ok){
                                            #delete the file
                                            unlink($path_file);
                                        }else{
                                           $text2 = "Can't save the file to the database.";
                                           echo "<html><head></head><body>".$text2."</body></html>";  
                                        }
                                       #================= 
                                    }     
                                            
                                }    
                            } else {
                                $text2 = "The file $path_pdf does not exist";
                                echo "<html><head></head><body>".$text2."</body></html>";
                            }
                        }    
                    }
                    closedir($dh);
                }
            }else{
                $text2 = "Is not a directory";
                echo "<html><head></head><body>".$text2."</body></html>";
            }
            echo "<br>End here. Check Database...";
           
            #GET LAB RESULT FORMAT from LIS server PDF
            #open the directory
            #$dir = $details->directory_pdf;
            $dir = 'ftp://'.$details->username.':'.$details->password.'@'.$details->address.'/'.$row_hosp['LIS_folder_path_pdf'].'/';
            echo "<br>Start getting the Lab Result in PDF format from LIS...";
            // Open a known directory, and proceed to read its contents
            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        if ($file != "." && $file != ".."){
                            #get the file
                            $path_file =  $dir.$file;
                            #check if file exists
                            if (file_exists($path_file)) {
                                $handle = fopen($path_file, "rb");
                                if (!$handle) {
                                    echo "<p>Unable to open remote file.\n";
                                }else{
                                    #check only the PDF file
                                    if (!stristr($file,'.PDF')===FALSE){
                                        $contents = '';
                                        while (!feof($handle)) {
                                          $contents .= fread($handle, 8192);
                                        }
                                        fclose($handle);
                                        #save to database
                                        #table : seg_hl7_pdffile_received
                                        $details->filename = $file;
                                        $details->hl7_msg = $contents;
                                        $ok = $srvObj->addInfo_PDF_file_received($details);
                                        
                                        if ($ok){
                                            #delete the file
                                            unlink($path_file);
                                            #-do nothing
                                        }else{
                                           $text2 = "Can't save the file to the database.";
                                           echo "<html><head></head><body>".$text2."</body></html>";  
                                        }
                                     }   
                                            
                                }    
                            } else {
                                $text2 = "The file $path_pdf does not exist";
                                echo "<html><head></head><body>".$text2."</body></html>";
                            }
                        }    
                    }
                    closedir($dh);
                }
            }else{
                $text2 = "Is not a directory";
                echo "<html><head></head><body>".$text2."</body></html>";
            }
            echo "<br>End here. Check Database...";
        }#end of if(ftp_login($ftp_connection,$ftp_username,$ftp_password))     
    }#end of if($ftp=ftp_connect($ftp_ip))            
    
?>
