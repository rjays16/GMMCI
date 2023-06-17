<?php
	# created by VAN 01-12-2012
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	
    require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
    $objInfo = new Hospital_Admin();
    
    $row_hosp = $objInfo->getAllHospitalInfo();
    $details = (object) 'details';
    $details->protocol_type = $row_hosp['LIS_protocol_type'];
    $details->protocol = $row_hosp['LIS_protocol'];
    $details->address = $row_hosp['LIS_address'];
    $details->port = $row_hosp['LIS_port'];
    $details->username = $row_hosp['LIS_username'];
    $details->password = $row_hosp['LIS_password'];
    $details->directory = "\\\\".$details->address.$row_hosp['LIS_folder_path'];
    $details->service_timeout = $row_hosp['service_timeout'];    #timeout in seconds
    #$details->address = '192.168.1.29';
    #\\192.168.1.13\HL7Host\PDF
    #$details->address = '192.168.1.13';  
    $details->directory_LIS = "\\\\".$details->address.$row_hosp['LIS_folder_path_inbox'];
    #$details->directory_LIS = '\\SEGWORKS\Dropsite\Vanessa\HL7server';
    $hl7extension = ".".$row_hosp['LIS_HL7_extension'];
    $delimiter = "\015";
    
	# Create file
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_transport_hl7_file.php');
    $transportObj = new seg_transport_HL7_file($details);
    
    #==========================================03/14/2012===================================
    #check if connected to LIS server
    $is_connected = $transportObj->isConnected();
    #echo "con = ".$is_connected."<br>";
    
    require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
    $srvObj=new SegLab();
    
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_create_hl7_message.php');
    $HL7Obj = new seg_create_msg_HL7();
            
    # Create file
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_create_hl7_file.php');
    $fileObj = new seg_create_HL7_file();
    
    require_once($root_path.'include/care_api_classes/class_person.php');
    $person_obj = new Person;

    require_once($root_path.'include/care_api_classes/class_encounter.php');
    $enc_obj=new Encounter;
    
    
    $prefix = "HIS";
    $COMPONENT_SEPARATOR = "^";
    $REPETITION_SEPARATOR = "~";            

    $row_hosp = $objInfo->getAllHospitalInfo();
    $row_comp = $objInfo->getSystemCreatorInfo();
            
    #msh
    $details->system_name = trim($row_comp['system_id']);
    $details->hosp_id = trim($row_hosp['hosp_id']);
    $details->lis_name = trim($row_comp['lis_name']);
    $details->currenttime = strftime("%Y%m%d%H%M%S");
    $msg_control_id = $srvObj->getLastMsgControlID();
    $details->msg_control_id_db = $msg_control_id;
    $details->msg_control_id = $prefix.$msg_control_id;
    
    #$refno = '2012000014';
    #$refno = '2012000021';
    #$refno = '2012000118';
    $refno = $_GET['refno'];
    #echo "r = ".$refno;
    $refNoBasicInfo = $srvObj->getBasicLabServiceInfo($refno);
    extract($refNoBasicInfo);
    
    if ($encounter_nr){
        $patient = $enc_obj->getEncounterInfo($encounter_nr);
        #echo "enc = ".$enc_obj->sql;
    }else if($pid){
        $patient = $person_obj->getAllInfoArray($pid);
        #echo "pid = ".$enc_obj->sql;
    }
    
    # Observation order - event O01
        $msg_type = "ORM";
        $event_id = "O01";
        $hl7_msg_type = $msg_type.$COMPONENT_SEPARATOR.$event_id;
        $details->msg_type = $hl7_msg_type;
                                        
        #pid
        $details->POH_PAT_ID = trim($pid);
        $details->POH_PAT_ALTID = "";
        $details->patient_name = trim($patient['name_first']).$COMPONENT_SEPARATOR.trim($patient['name_last']);
        $details->pat_name = trim($patient['name_last'])." ".trim($patient['name_first']);
        $details->POH_MIDDLENAME =trim($patient['name_middle']);
        $details->POH_PAT_DOB = date("YmdHis",strtotime($patient['date_birth']));
        $details->POH_PAT_SEX = trim(strtoupper($patient['sex']));
                                        
        $details->address = trim($street_name).$COMPONENT_SEPARATOR.trim($brgy_name).$COMPONENT_SEPARATOR.trim($mun_name).$COMPONENT_SEPARATOR.trim($prov_name).$COMPONENT_SEPARATOR.trim($zipcode);
        $details->POH_CIVIL_STAT = trim(strtoupper($patient['civil_status']));
        
        switch ($patient['encounter_type']){
             case '1' :  $patient_type = "ER";
                         break;
            case '2' :
                         $patient_type = "OPD";
                         break;
            case '3' :  
                         $patient_type = "IPD";
                         break;
            case '4' :
                         $patient_type = "IPD";
                         break;
            case '5' :
                         $patient_type = "RDU";
                         break;
            case '6' :
                         $patient_type = "IC";
                         break;
            default :
                         $patient_type = "WN";  #Walk-in
                         break;
        }                
        
        #pv1
        $details->setID = "1";
        $details->POH_PAT_TYPE = mb_strtoupper($patient_type);
        $details->location = mb_strtoupper($loc_code2).$COMPONENT_SEPARATOR.mb_strtoupper($loc_name2);
        $details->requesting_doc =  $_POST['requestDoc'][0].$COMPONENT_SEPARATOR.addslashes(mb_strtoupper($_POST['requestDocName'][0]));
        $details->POH_PAT_CASENO = trim($encounter_nr);
        
        #orc
        # NW = New
        # RP = Replacement
        # CA = Cancellation
        
        /*if ($mode=='save')
            $order_control = "NW";
        elseif ($mode=='update')
            $order_control = "RP";
        elseif ($mode=='cancel')
            $order_control = "CA";        */
        $order_control = "NW";    
                
        $details->order_control = $order_control;
        
        #obr
        $row_order = $srvObj->getLabOrderNoLIMIT($refno);
        $details->POH_ORDER_NO = $row_order['lis_order_no'];
        #order items
        $result = $srvObj->getRequestDetailsbyRefnoLIS($refno);
        #echo $srvObj->sql;
        $count = $srvObj->FoundRows();
       
        while($row_test=$result->FetchRow()){
            $service .= trim($row_test['service_code']).$COMPONENT_SEPARATOR.trim($row_test['name']).$REPETITION_SEPARATOR;
        }
        $service = trim($service);
        $service_list = substr($service,0,strlen($service)-1);
        $details->service_list = trim($service_list);
        $details->POH_PRIORITY2 = trim($priority);
        $details->POH_TRX_DT =  date("YmdHis",strtotime($order_date)); 
        $details->POH_CLI_INFO = addslashes(mb_strtoupper(trim($_POST['clinicInfo'][0])));
        $details->doctor = trim($_POST['requestDoc'][0]).$COMPONENT_SEPARATOR.addslashes(mb_strtoupper(trim($_POST['requestDocName'][0])));
        
        if ($patient['encounter_type']==2)
           $location = "OPD".$COMPONENT_SEPARATOR."OUTPATIENT";
        elseif($patient['encounter_type']==1)
           $location = "ER".$COMPONENT_SEPARATOR."ER"; 
        elseif (($patient['encounter_type']==3) || ($patient['encounter_type']==4))
           $location = "IPD".$COMPONENT_SEPARATOR."INPATIENT";
        else
           $location = "WN".$COMPONENT_SEPARATOR."WALKIN";
        #$details->location_dept = mb_strtoupper($loc_code2).$COMPONENT_SEPARATOR.mb_strtoupper($loc_name2);
        $details->location_dept = $location;
                                        
        $msh_segment = $HL7Obj->createSegmentMSH($details);
        $pid_segment = $HL7Obj->createSegmentPID($details);
        $pv1_segment = $HL7Obj->createSegmentPV1($details);
        $orc_segment = $HL7Obj->createSegmentORC($details);
        $obr_segment = $HL7Obj->createSegmentOBR($details);
                                        
        $filecontent = $msh_segment."\n".$pid_segment."\n".$pv1_segment."\n".$orc_segment."\n".$obr_segment;
        #echo $filecontent;
        $file = $details->msg_control_id;
        #echo "<br><br>".$file;
        #create a file
        $filename_local = $fileObj->create_file_to_local($file);
                                        
        #Thru file sharing
        #write a file to a local directory
        $fileObj->write_file($filename_local, $filecontent);
        
        
        #==========FOR LAB RESULT========================
        $filepath = $details->directory."\\".$file.$hl7extension;
        #check if file exist
        $handle = fopen($filepath, "r");
        if ($handle){
           #echo "<br>true";     
           #get file contents
           $file_content = file_get_contents($filepath);
           
           #note the hl7 file that was received from LIS
           #usually LAB result or acknowledgement
           $fileinfoObj->filename = $file;
           $fileinfoObj->hl7_msg = $file_content;
           $ok = $srvObj->addInfo_HL7_file_received($fileinfoObj);
           
           #echo "<br>content<br><br>";
           #print_r($file_content);
           #parse the HL7 message
           $parse_whole = explode($delimiter,$file_content);
           #print_r($parse);
           for ($i=0; $i<sizeof($parse_whole);$i++){
               #echo "<br>".$i."==>";
               #print_r($parse_whole[$i]);
           }
        }
        
        #open a lab result in pdf format
        #PATIENT ID_LASTNAME FIRSTNAME MIDDLENAME_PATIENT TYPE_LIS ORDER NO_PATIENT CASENO
        #final
        ####################
        $file_pdf = $details->POH_PAT_ID."_".$details->pat_name."_".$details->POH_PAT_TYPE."_".$details->POH_ORDER_NO."_".$details->POH_PAT_CASENO.".pdf";
        $path_pdf = $details->directory_LIS."\\".$file_pdf;
        #echo $path_pdf;
        $fh = fopen($ourFileName, 'r');
        $filedata = fread($fh, filesize($path_pdf)); 
        fclose($fh);
        
        header("Content-type: application/pdf"); 
        readfile($path_pdf);
        
        #===========FOR LAB RESULT=====================
                                        
        #must be changed
        #$transfer_method = "NFS";
        #$transportObj
        #echo "mode = ".$transfer_method;
        /*switch ($transfer_method){
            #window NFS approach or network file sharing
            case "NFS" :
                        $filename_hclab = $fileObj->create_file_to_hclab($file);
                        #write a file to a hclab directory
                        $fileObj->write_file($filename_hclab, $filecontent); 
                        break;
            #TCP/IP (communication approach)                    
            case "SOCKET" :
                        echo "<br><br>UNDER CONSTRUCTION...";
                        break;                    
        }*/
    
    #==========================================03/14/2012===================================
    
    /*if ($transportObj->isConnected()){
         $text = "Connected to address $transportObj->address with port $transportObj->port ".$transportObj->msg;
         
         #$data = "ready to get/send data\0"; 
         #$obj = $transportObj->writeHL7($data);
         $file_pdf = $details->POH_PAT_ID."_".$details->pat_name."_".$details->POH_PAT_TYPE."_".$details->POH_ORDER_NO."_".$details->POH_PAT_CASENO.".pdf";
         #$details->directory_LIS = '\\\\127.0.0.1\HL7Server\hclab\hcini\pdf';
         
         $path_pdf = $details->directory_LIS.$file_pdf;
         #echo $path_pdf."<br>";die();
         if (file_exists($path_pdf)) {
            $fh = fopen($path_pdf, 'r');
            $filedata = fread($fh, filesize($path_pdf)); 
            fclose($fh);
         
            header("Content-type: application/pdf"); 
            readfile($path_pdf);
         } else {
            $text2 = "The file $path_pdf does not exist";
            echo "<html><head></head><body>".$text2."</body></html>";
         }
        
    }else{
         $text = "Unable to connect. ".$transportObj->error;   
    }
    
     socket_close($transportObj->socket); */
     #echo "<html><head></head><body>".$text."</body></html>";
    
?>
