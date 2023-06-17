<?php
	# created by VAN 01-12-2012
	# using HL7 approach
	# creating a message for lab order that to be send to LIS
    
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	
	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
    $objInfo = new Hospital_Admin();
    
	# Create laboratory object
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
    $srvObj = new SegLab();
    
    # Create hl7 object
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_create_hl7_message.php');
    $HL7Obj = new seg_create_msg_HL7();
    
    # Create file
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_create_hl7_file.php');
    $fileObj = new seg_create_HL7_file();
    
    # Create file
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_transport_hl7_file.php');
    $transportObj = new seg_transport_HL7_file();
    
    $details = (object) 'details';
    
    $prefix = "HIS";
    $COMPONENT_SEPARATOR = "^";
    $REPETITION_SEPARATOR = "~";            

    $row_hosp = $objInfo->getAllHospitalInfo();
    $row_comp = $objInfo->getSystemCreatorInfo();
    
	$refno = $_GET['refno'];
    $refno = '2012000014';    # inpatient
    
    $details->refno = $refno; 
    
    # get or fetch data from seghis using the refno
    # and lab test request which is included in LIS and with specimen
    $lab_order_header_info = $srvObj->getOrderHeader($details->refno);
    #echo $srvObj->sql;
    extract($lab_order_header_info);

    #get value
    foreach($lab_order_header_info as $key => $value){
       $key = $value;
    }

    #msh
    $details->hosp_id = trim($row_hosp['hosp_id']);
    $details->system_name = trim($row_comp['system_id']);
    $details->lis_name = trim($row_comp['lis_name']);
    $details->currenttime = strftime("%Y%m%d%H%M%S");
    
    #must be changed
    $trigger_event = "O01";
    switch ($trigger_event){
        #admit notification
        case "A01" :
                    $msg_type = "ADT";
                    $event_id = $trigger_event;
                    $with_separator = 1;
                    break;
        #transfer
        case "A02" :
                    $msg_type = "ADT";
                    $event_id = $trigger_event;
                    $with_separator = 1;
                    break;                                    
        #visit notification (nonadmitted patient)            
        case "A04" :
                    $msg_type = "ADT";
                    $event_id = $trigger_event;
                    $with_separator = 1;
                    break;                        
        #update patient information
        case "A08" :
                    $msg_type = "ADT";
                    $event_id = $trigger_event;
                    $with_separator = 1;
                    break;                                    
        #observation order            
        case "O01" :
                    $msg_type = "ORM";
                    $event_id = $trigger_event;
                    $with_separator = 1;
                    break;
        #observation/result
        case "R01" :
                    $msg_type = "ORU";
                    $event_id = $trigger_event;
                    $with_separator = 1;
                    break;              
        #HIS acknowledgment of receipt of the test result
        case "ACK" :
                    $msg_type = "";
                    $event_id = $trigger_event;
                    $with_separator = 0;
                    break;                        
                    
    }
    
    if ($with_separator)
        $hl7_msg_type = $msg_type.$COMPONENT_SEPARATOR.$event_id;
    else
        $hl7_msg_type = $msg_type.$event_id;        
    
    $details->msg_type = $hl7_msg_type;
    
    
    $msg_control_id = $srvObj->getLastMsgControlID();
    $details->msg_control_id = $prefix.$msg_control_id;
    
    #pid
    $details->POH_TRX_DT = date("YmdHis",strtotime($POH_TRX_DT));
    $details->POH_ORDER_DT = date("YmdHis",strtotime($POH_ORDER_DT));
    $details->POH_TRX_ID = "N";    # new order  ; U for update
    $details->POH_TRX_STATUS = "N";   # before read by LIS default value
    $details->POH_PAT_DOB = date("YmdHis",strtotime($POH_PAT_DOB));
    $details->patient_name = trim($POH_FIRSTNAME).$COMPONENT_SEPARATOR.trim($POH_LASTNAME);
    $details->POH_MIDDLENAME = trim($POH_MIDDLENAME);
    $details->POH_PAT_SEX = trim($POH_PAT_SEX);
    #street address^other designation^city^state or province^postal code 
    $details->address = trim($POH_STREET).$COMPONENT_SEPARATOR.trim($POH_BRGY).$COMPONENT_SEPARATOR.trim($POH_CITY).$COMPONENT_SEPARATOR.trim($POH_PROVINCE).$COMPONENT_SEPARATOR.trim($POH_ZIPCODE);
    $details->POH_CIVIL_STAT = trim($POH_CIVIL_STAT);
    $details->POH_PAT_ID = trim($POH_PAT_ID);
    $details->POH_PAT_ALTID = trim($POH_PAT_ALTID);
    
    #pv1
    #must be changed
    $details->setID = 1;
    $details->POH_PAT_TYPE = trim($POH_PAT_TYPE);
    $details->location = trim($POH_LOC_NAME);
    $details->requesting_doc = trim($POH_DR_CODE).$COMPONENT_SEPARATOR.trim($POH_DR_NAME);
    $details->POH_PAT_CASENO = trim($POH_PAT_CASENO);
    
    #orc
    # NW = New
    # RP = Replacement
    # CA = Cancellation
    #must be changed        
    $details->order_control = "NW";
    
    #obr
    $details->POH_ORDER_NO = $POH_ORDER_NO;
    #order items
    $result = $srvObj->getRequestDetailsbyRefno($refno);
    #echo $srvObj->sql;
    $count = $srvObj->FoundRows();
   
    while($row_test=$result->FetchRow()){
        $service .= trim($row_test['service_code']).$COMPONENT_SEPARATOR.trim($row_test['name']).$REPETITION_SEPARATOR;
    }
    $service = trim($service);
    $service_list = substr($service,0,strlen($service)-1);
    $details->service_list = trim($service_list);
    $details->POH_PRIORITY2 = trim($POH_PRIORITY2);
    $details->POH_TRX_DT = $POH_TRX_DT;
    $details->POH_CLI_INFO = trim($POH_CLI_INFO);
    $details->doctor = trim($POH_DR_CODE).$COMPONENT_SEPARATOR.trim($POH_DR_NAME);
    $details->location_dept = $POH_LOC_CODE2.$COMPONENT_SEPARATOR.$POH_LOC_NAME2;
    
    #msa
    # AA-accept; AE=error; AR=reject
    #must change this
    $ack_code = "AA";
    $details->ack_code = $ack_code;
    #temporary
    #control id must be from hclab
    $details->msg_control_id_hclab= $details->msg_control_id;
    $details->error = "";
    
    $msh_segment = $HL7Obj->createSegmentMSH($details);
    $pid_segment = $HL7Obj->createSegmentPID($details);
    $pv1_segment = $HL7Obj->createSegmentPV1($details);
    $orc_segment = $HL7Obj->createSegmentORC($details);
    $obr_segment = $HL7Obj->createSegmentOBR($details);
    $obx_segment = $HL7Obj->createSegmentOBX($details);
    $nte_segment = $HL7Obj->createSegmentNTE($details);
    $msa_segment = $HL7Obj->createSegmentMSA($details);
    
    switch ($trigger_event){
        #admit notification
        case "A01" :
                    $filecontent = $msh_segment."\n".$pid_segment."\n".$pv1_segment;
                    break;
        #transfer
        case "A02" :
                    $filecontent = $msh_segment."\n".$pid_segment."\n".$pv1_segment;
                    break;                                    
        #visit notification (nonadmitted patient)            
        case "A04" :
                    $filecontent = $msh_segment."\n".$pid_segment."\n".$pv1_segment;
                    break;                        
        #update patient information
        case "A08" :
                    $filecontent = $msh_segment."\n".$pid_segment;
                    break;                                    
        #observation order            
        case "O01" :
                    $filecontent = $msh_segment."\n".$pid_segment."\n".$orc_segment."\n".$obr_segment;
                    break; 
        #observation/result
        case "R01" :
                    $filecontent = $msh_segment."\n".$pid_segment."\n".$orc_segment."\n".$obr_segment."\n".$obx_segment."\n".$nte_segment;
                    break;
        #HIS acknowledgment of receipt of the test result
        case "ACK" :
                    $filecontent = $msh_segment."\n".$msa_segment;
                    break;
    }
    
    $file = $details->msg_control_id;
    #create a file
    $filename_local = $fileObj->create_file_to_local($file);
    
    #Thru file sharing
    #write a file to a local directory
    $fileObj->write_file($filename_local, $filecontent); 
    
    #must be changed
    #$transfer_method = "SOCKET";
    #$transportObj
    switch ($transfer_method){
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
    }                         
    
?>
