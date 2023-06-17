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
            echo "<br>Fetching HL7 messages...";
            
            // Open a known directory, and proceed to read its contents
            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {
                    $cnt = 0;
                    while (($file = readdir($dh)) !== false) {
                        if ($file != "." && $file != ".."){
                            #get the file
                            $path_file =  $dir.$file;
                            #check if file exists
                            if (file_exists($path_file)) {
                                $handle = fopen($path_file, "rb");
                                if (!$handle) {
                                    echo "<p>Unable to open remote HL7 message file.\n";
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
                                        $details->hl7_msg = addslashes(trim($contents));
                                        $ok = $srvObj->addInfo_HL7_file_received($details);
                                       
                                        if ($ok){
                                            #delete the file
                                            unlink($path_file);
                                            $cnt++;
                                        }else{
                                           echo '<br>Fetching of HL7 laboratory messages FAILED..';  
                                        }
                                       #================= 
                                    }     
                                            
                                }    
                            } else {
                                echo '<br>The file $path_pdf does not exist..';
                            }
                        }    
                    }
                    closedir($dh);
                }
            }else{
                echo '<br>Is not a directory..'; 
            }
            
            if ($cnt){
                echo '<br>HL7 Laboratory messages were successfully fetched..'; 
                if ($cnt>1)
                    $label = "files";
                else
                    $label = "file";        
                    
                echo "<br>End of fetching HL7 lab messages. There are $cnt $label fetched...";
            }else
                echo '<br>No HL7 laboratory messages were fetched..';     
           
            #GET LAB RESULT FORMAT from LIS server PDF
            #open the directory
            #$dir = $details->directory_pdf;
            $dir = 'ftp://'.$details->username.':'.$details->password.'@'.$details->address.'/'.$row_hosp['LIS_folder_path_pdf'].'/';
            echo "<br><br>Fetching laboratory results...";
            // Open a known directory, and proceed to read its contents
            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {
                    $cnt = 0;
                    while (($file = readdir($dh)) !== false) {
                        if ($file != "." && $file != ".."){
                            #get the file
                            $path_file =  $dir.$file;
                            #check if file exists
                            if (file_exists($path_file)) {
                                $handle = fopen($path_file, "rb");
                                if (!$handle) {
                                    echo "<p>Unable to open remote lab results in pdf format.\n";
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
                                        $details->hl7_msg = addslashes(trim($contents));
                                        $ok = $srvObj->addInfo_PDF_file_received($details);
                                        
                                        if ($ok){
                                            #delete the file
                                            unlink($path_file);
                                            
                                            #check the reference request by order no from LIS
                                            $arr = explode("_",$file);
                                            $arr = explode(".",$arr[1]);
                                            $order_no = $arr[0];
                                            
                                            $refno = $srvObj->getLISOrderNo($order_no);
                                            
                                            #update the request if served and if there is a result
                                            $date_served = date("Y-m-d h:i:s");
                                            $srvObj->DoneLabRequest($refno,$date_served);
                                            $cnt++;
                                        }else{
                                           echo '<br>Fetching of lab results FAILED..'; 
                                           #echo '<br>'.$srvObj->sql; 
                                        }
                                     }   
                                            
                                }    
                            } else {
                                echo '<br>The file $path_pdf does not exist..'; 
                            }
                        }    
                    }
                    closedir($dh);
                }
            }else{
                echo '<br>Is not a directory..'; 
            }
            if ($cnt){
                echo '<br>Laboratory results were successfully fetched..';
                if ($cnt>1)
                    $label = "files";
                else
                    $label = "file";
                    
                echo "<br>End of fetching laboratory results. There are $cnt $label files fetched...";
            }else
                echo '<br>No laboratory results were fetched..';
        }#end of if(ftp_login($ftp_connection,$ftp_username,$ftp_password))     
    }#end of if($ftp=ftp_connect($ftp_ip))            
    
?>
