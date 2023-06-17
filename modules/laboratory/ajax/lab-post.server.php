<?php
 function populateRequestListSerialItem($refno, $service_code, $pid){
	 global $db;
	 $objResponse = new xajaxResponse();
	 $srvObj=new SegLab();

	 $row_rs = $srvObj->getTestbyRefno($refno, $service_code);
	 #$objResponse->alert($row_rs['is_serial']." , ".$row_rs['no_serial']);
	 #$objResponse->alert($srvObj->sql);
	 $norows = $srvObj->FoundRows();
	 #$objResponse->alert('norows = '.$norows);
	 if ($norows){
		 if ($row_rs['is_serial']){
			 $i=1;
			 $objResponse->call("emptyIntialRequestList");
			 for($i=1; $i<=$row_rs['no_serial']; $i++){
				 
                 #check the table seg_lab_serv_serial for catered test
                 $row_serial = $srvObj->getSerialTestCatered($refno, $service_code, $i);
                 
                 $details['service_code'] = $row_rs['service_code'];
                 $details['no_serial'] = $row_rs['no_serial'];
                 $details['index'] = $i;
                 $details['in_lis'] = $row_rs['in_lis'];
                 $details['no_repeat'] = $row_rep['no_repeat'];
                 $details['nth_take_catered'] = $row_serial['nth_take'];
                 $details['lis_order_no'] = $row_serial['lis_order_no'];
                 
                 $details['dateserved'] = '';
                 
                 if (($row_serial['create_date']!='0000-00-00 00:00:00')&&($row_serial['create_date']!=''))
                    $details['dateserved'] = date("m/d/Y h:i a", strtotime($row_serial['create_date']));
                    
                 #check if there is a result
                 $resultobj = $srvObj->getRequestResult($pid, $row_serial['lis_order_no']);
                 $result = $srvObj->count;
                 $details['has_result'] = 0;
                 if ($result)
                    $details['has_result'] = 1;
                 
                 #$objResponse->alert($row_rs['service_code']." , ".$row_rs['no_serial']." , ".$i." , ".$row_rs['in_lis']);
				 $objResponse->call("initializeTable",$details);
			 }
		 }else{
			 #set that no of take = 1 take
			 $objResponse->call("emptyIntialRequestList");
			 $row_rs['no_serial'] = 1;
			 $i = 1;
			 $objResponse->call("initializeTable",$row_rs['service_code'],$row_rs['no_serial'],$i,$row_rs['in_lis'], $row_rep['no_repeat']);

		 }
	 }else{
		 $objResponse->call("emptyIntialRequestList");
	 }

	 return $objResponse;
 }
 
 function deleterequest($refno, $service_code, $index, $lis_order_no){
     global $db;
     $objResponse = new xajaxResponse();
     $srvObj=new SegLab();
     $details = (object) 'details';
     
     $ok = $srvObj->deleteSerial($refno, $service_code, $index);
     #$objResponse->alert($srvObj->sql);
     
     if ($ok){
         #create HL7 message
         $objInfo = new Hospital_Admin();
         $row_hosp = $objInfo->getAllHospitalInfo();
         
 //Commented By Jarel Not Applicable for Manual Result
//         if ($row_hosp['connection_type']=='hl7'){
//            #validate if there a LIS posted request
//            $hl7_row = $srvObj->isExistHL7MsgLISOrder($refno, $lis_order_no);
//            if ($hl7_row['msg_control_id']){
//                #update HL7 message tracker
//                $row_comp = $objInfo->getSystemCreatorInfo();
//
//                $details->protocol_type = $row_hosp['LIS_protocol_type'];
//                $details->protocol = $row_hosp['LIS_protocol'];
//                $details->address_lis = $row_hosp['LIS_address'];
//                $details->address_local = $row_hosp['LIS_address_local'];
//                $details->port = $row_hosp['LIS_port'];
//                $details->username = $row_hosp['LIS_username'];
//                $details->password = $row_hosp['LIS_password'];
//
//                $details->folder_LIS = $row_hosp['LIS_folder_path'];
//                #LIS SERVER IP
//                $details->directory_remote = "\\\\".$details->address_lis.$row_hosp['LIS_folder_path'];
//                #HIS SERVER IP
//                $details->directory = "\\\\".$details->address_local.$row_hosp['LIS_folder_path'];
//                #HIS SERVER IP
//                $details->directory_local = "\\\\".$details->address_local.$row_hosp['LIS_folder_path_local'];
//                $details->extension = $row_hosp['LIS_HL7_extension'];
//                $details->service_timeout = $row_hosp['service_timeout'];    #timeout in seconds
//                $details->directory_LIS = "\\\\".$details->address_lis.$row_hosp['LIS_folder_path_inbox'];
//                $details->hl7extension = ".".$row_hosp['LIS_HL7_extension'];
//
//                $transfer_method = $details->protocol_type;
//
//                #msh
//                $details->system_name = trim($row_comp['system_id']);
//                $details->hosp_id = trim($row_hosp['hosp_id']);
//                $details->lis_name = trim($row_comp['lis_name']);
//                $details->currenttime = strftime("%Y%m%d%H%M%S");
//
//                $fileObj = new seg_create_HL7_file($details);
//
//                $order_control = "CA";
//                $hl7msg_row = $srvObj->isforReplaceHL7MsgLISOrder($refno,$lis_order_no,$order_control);
//
//                if ($hl7msg_row['msg_control_id']){
//                    $msg_control_id = $hl7msg_row['msg_control_id'];
//                    $forreplace = 1;
//                }else
//                    $msg_control_id = $srvObj->getLastMsgControlID();
//
//                $prefix = "HIS";
//
//                $details->msg_control_id_db = $msg_control_id;
//                $details->msg_control_id = $prefix.$msg_control_id;
//
//                # Observation order - event O01
//                $msg_type = "ORM";
//                $event_id = "O01";
//                $hl7_msg_type = $msg_type.$COMPONENT_SEPARATOR.$event_id;
//                $details->msg_type = $hl7_msg_type;
//
//                $hl7_msg = $hl7_row['hl7_msg'];
//                #$objResponse->alert($hl7_msg);
//                $hl7_msg_arr = explode("\n",$hl7_msg);
//                #$objResponse->alert(print_r($hl7_msg_arr,1));
//
//                #for MSH
//                $msh_arr = explode("|",$hl7_msg_arr[0]);
//                #date and time of message
//                $msh_arr[6] = $details->currenttime;
//                #message control ID
//                $msh_arr[9] = $details->msg_control_id;
//                $hl7_msg_arr[0] = implode("|",$msh_arr);
//
//                #for ORC
//                $orc_arr = explode("|",$hl7_msg_arr[3]);
//
//                #order_control
//                $orc_arr[1] = $order_control;
//                $hl7_msg_arr[3] = implode("|",$orc_arr);
//                #$objResponse->alert($hl7_msg_arr[3]);
//
//                #final HL7 message
//                $hl7_msg = implode("\n",$hl7_msg_arr);
//
//                $filecontent = $hl7_msg;
//                #$objResponse->alert($filecontent);
//                $file = $details->msg_control_id;
//                switch ($transfer_method){
//                    #FTP (File Transfer Protocol) approach
//                    case "ftp" :
//                                $transportObj = new seg_transport_HL7_file($details);
//                                $transportObj->ftp_transfer($file, $filecontent);
//                                break;
//
//                    #window NFS approach or network file sharing
//                    case "nfs" :
//                                #create a file
//                                $filename_local = $fileObj->create_file_to_local($file);
//                                #Thru file sharing
//                                #write a file to a local directory
//                                $fileObj->write_file($filename_local, $filecontent);
//
//                                $filename_hclab = $fileObj->create_file_to_hclab($file);
//                                #write a file to a hclab directory
//                                $fileObj->write_file($filename_hclab, $filecontent);
//                                unlink($filename_local);
//                                break;
//                    #TCP/IP (communication approach)
//                    case "tcp" :
//                                $transportObj = new seg_transport_HL7_file($details);
//
//                                #if ($transportObj->isConnected()){
//                                     #send the message
//                                     $obj = $transportObj->sendHL7MsgtoSocket($filecontent);
//
//                                     #return/print result
//                                     $text = "LIS Server said:: ".$obj;
//                                     #$text = "connected...";
//                                #}else{
//                                #     $text = "Unable to connect to LIS Server. Error: ".$transportObj->error."...";
//                                #}
//
//                                echo $text;
//                                break;
//                }
//
//                #update msg control id
//                $details->msg_control_id = $details->msg_control_id_db;
//                #if new message control id, update the tracker
//                if (!$forreplace)
//                    $hl7_ok = $srvObj->updateHL7_msg_control_id($details->msg_control_id);
//
//                #HL7 tracker
//                $srvObj->getInfo_HL7_tracker($details->msg_control_id);
//                $with_rec = $srvObj->count;
//
//                $details->lis_order_no = $lis_order_no;
//                $details->msg_type = $msg_type;
//                $details->event_id = $event_id;
//                $details->refno = $refno;
//
//                $rs_ref = $srvObj->get_OrderNo_by_Refno($refno);
//                $row_ref = $rs_ref->FetchRow();
//
//                $details->pid = $row_ref['pid'];
//                $details->encounter_nr = $row_ref['encounter_nr'];
//
//                $details->hl7_msg =  $filecontent;
//                #$objResponse->alert(print_r($details,1));
//                if ($with_rec){
//                    $hl7_ok = $srvObj->updateInfo_HL7_tracker($details);
//                }else{
//                    $hl7_ok = $srvObj->addInfo_HL7_tracker($details);
//                }
//            }
//         }
         
         $objResponse->call('reloadPage');  
     }
     return $objResponse;
     
 }

 function submitrequest($refno, $service_code, $dateserved, $index){
     global $db;
     $objResponse = new xajaxResponse();
     $srvObj=new SegLab();
     $objInfo = new Hospital_Admin();
     
     $prefix = "HIS";
     $COMPONENT_SEPARATOR = "^";
     $REPETITION_SEPARATOR = "~";

     $row_hosp = $objInfo->getAllHospitalInfo();
     $connection_type = $row_hosp['connection_type'];
     
     #$objResponse->alert("r,s,d,i ==> ".$refno.", ".$service_code.", ".$dateserved.", ".$index);
     $row_serial = $srvObj->getSerialTestCatered($refno, $service_code, $index);
     #$objResponse->alert($srvObj->sql);
     
     $isExistLIS = $srvObj->count;
     #$objResponse->alert('ex= '.$isExistLIS);
     if ($isExistLIS){
         $lis_order_no = $row_serial['lis_order_no'];
     }else{
         #get new LIS order no
         $lis_order_no = $srvObj->getLastOrderNo();
         $okHCLAB = $srvObj->insert_Orderno_HCLAB($lis_order_no, $refno);
         $ok = $srvObj->update_HCLabRefno_Tracker($lis_order_no);
     }    
     #$objResponse->alert($lis_order_no);
     #initialize all data info to be saved
     $serial_details['refno'] = $refno;
     $serial_details['service_code'] = $service_code;
     $serial_details['lis_order_no'] = $lis_order_no;
     $serial_details['nth_take'] = $index;
     $serial_details['is_served'] = 1;
     $serial_details['with_result'] = 0;
     $serial_details['is_repeated'] = 0;
    
     #saved in seg_lab_serv_serial, the FIRST take ONLY
     $serial_ok = $srvObj->saveInfoSerial($serial_details);
     $serial_ok = 1;
     if ($serial_ok){
         #update seg_lab_hclab_tracker
         #update seg_lab_hclab_orderno

//Commented By Jarel Not Applicable for Manual Result
//         if ($connection_type=='hl7'){
//            #$objResponse->alert('create hl7 here right');
//            #create HL7 message
//            $row_comp = $objInfo->getSystemCreatorInfo();
//
//            $details->protocol_type = $row_hosp['LIS_protocol_type'];
//            $details->protocol = $row_hosp['LIS_protocol'];
//            $details->address_lis = $row_hosp['LIS_address'];
//            $details->address_local = $row_hosp['LIS_address_local'];
//            $details->port = $row_hosp['LIS_port'];
//            $details->username = $row_hosp['LIS_username'];
//            $details->password = $row_hosp['LIS_password'];
//
//            $details->folder_LIS = $row_hosp['LIS_folder_path'];
//            #LIS SERVER IP
//            $details->directory_remote = "\\\\".$details->address_lis.$row_hosp['LIS_folder_path'];
//            #HIS SERVER IP
//            $details->directory = "\\\\".$details->address_local.$row_hosp['LIS_folder_path'];
//            #HIS SERVER IP
//            $details->directory_local = "\\\\".$details->address_local.$row_hosp['LIS_folder_path_local'];
//            $details->extension = $row_hosp['LIS_HL7_extension'];
//            $details->service_timeout = $row_hosp['service_timeout'];    #timeout in seconds
//            $details->directory_LIS = "\\\\".$details->address_lis.$row_hosp['LIS_folder_path_inbox'];
//            $details->hl7extension = ".".$row_hosp['LIS_HL7_extension'];
//
//            $transfer_method = $details->protocol_type;
//
//            #msh
//            $details->system_name = trim($row_comp['system_id']);
//            $details->hosp_id = trim($row_hosp['hosp_id']);
//            $details->lis_name = trim($row_comp['lis_name']);
//            $details->currenttime = strftime("%Y%m%d%H%M%S");
//
//            $fileObj = new seg_create_HL7_file($details);
//            #$objResponse->alert('lis = '.$lis_order_no);
//            $hl7msg_row = $srvObj->isforReplaceHL7MsgLISOrder($refno,$lis_order_no,'RP');
//            #$objResponse->alert($srvObj->sql);
//            $forreplace = 0;
//            if ($hl7msg_row['msg_control_id']){
//                $msg_control_id = $hl7msg_row['msg_control_id'];
//                $forreplace = 1;
//            }else{
//                $msg_control_id = $srvObj->getLastMsgControlID();
//            }
//            $details->msg_control_id_db = $msg_control_id;
//            $details->msg_control_id = $prefix.$msg_control_id;
//
//            # Observation order - event O01
//            $msg_type = "ORM";
//            $event_id = "O01";
//            $hl7_msg_type = $msg_type.$COMPONENT_SEPARATOR.$event_id;
//            $details->msg_type = $hl7_msg_type;
//
//            #get previous check-in and replace the HL7
//            $hl7prev = $srvObj->isExistHL7Msg($refno);
//            #$objResponse->alert($srvObj->sql);
//            $hl7_msg = $hl7prev['hl7_msg'];
//            #$objResponse->alert($hl7_msg);
//            $hl7_msg_arr = explode("\n",$hl7_msg);
//
//            #for MSH
//            $msh_arr = explode("|",$hl7_msg_arr[0]);
//            #date and time of message
//            $msh_arr[6] = $details->currenttime;
//            #message control ID
//            $msh_arr[9] = $details->msg_control_id;
//            $hl7_msg_arr[0] = implode("|",$msh_arr);
//
//            #for ORC
//            $orc_arr = explode("|",$hl7_msg_arr[3]);
//            #order_control
//
//            $existhl7msg_row = $srvObj->isExistHL7MsgLISOrder($refno, $lis_order_no);
//            if ($existhl7msg_row['msg_control_id']){
//                $filecontent = $existhl7msg_row['hl7_msg'];
//                if (stristr($filecontent, 'ORC|NW|')){
//                    $order_control = "RP";
//                }elseif (stristr($filecontent, 'ORC|CA|')){
//                    $order_control = "NW";
//                }else
//                    $order_control = "RP";
//            }else
//                $order_control = "NW";
//
//            $orc_arr[1] = $order_control;
//            $hl7_msg_arr[3] = implode("|",$orc_arr);
//
//            #for OBR
//            $obr_arr = explode("|",$hl7_msg_arr[4]);
//            #$objResponse->alert(print_r($obr_arr,1));
//            #lis order number
//            $obr_arr[2] = $lis_order_no;
//            #requested date and time
//            $obr_arr[6] = date("YmdHis");
//
//            #order items
//            #lookup in the seg_lab_group for serial and profile test
//            $rs_serial = $srvObj->getTestProfileInclude($service_code);
//
//            #get the patient type
//            $enc = $srvObj->getEncounterType($refno);
//            $ptype = $enc['ptype'];
//
//            while($row_serial = $rs_serial->FetchRow()){
//
//                $serial_item = $srvObj->getTestInfo($row_serial['service_code_child']);
//                #outpatient
//                if (($ptype==2) || (!$ptype))
//                    $service_code = trim($serial_item['oservice_code']);
//                else
//                #inpatient
//                    $service_code = trim($serial_item['ipdservice_code']);
//
//                if (($is_rdu)||($loc_code2=='144'))
//                    $service_code = trim($serial_item['ipdservice_code']);
//
//                $service .= $service_code.$COMPONENT_SEPARATOR.trim($serial_item['name']).$REPETITION_SEPARATOR;
//            }
//
//            $service = trim($service);
//            $service_list = substr($service,0,strlen($service)-1);
//            #print_r($service_list);
//            $details->service_list = trim($service_list);
//
//            $obr_arr[4] = $details->service_list;
//            $hl7_msg_arr[4] = implode("|",$obr_arr);
//            #$objResponse->alert(print_r($hl7_msg_arr,1));
//
//            #final HL7 message
//            $hl7_msg = implode("\n",$hl7_msg_arr);
//
//            $filecontent = $hl7_msg;
//            #$objResponse->alert($filecontent);
//            $file = $details->msg_control_id;
//            switch ($transfer_method){
//                #FTP (File Transfer Protocol) approach
//                case "ftp" :
//                            $transportObj = new seg_transport_HL7_file($details);
//                            $transportObj->ftp_transfer($file, $filecontent);
//                            break;
//
//                #window NFS approach or network file sharing
//                case "nfs" :
//                            #create a file
//                            $filename_local = $fileObj->create_file_to_local($file);
//                            #Thru file sharing
//                            #write a file to a local directory
//                            $fileObj->write_file($filename_local, $filecontent);
//
//                            $filename_hclab = $fileObj->create_file_to_hclab($file);
//                            #write a file to a hclab directory
//                            $fileObj->write_file($filename_hclab, $filecontent);
//                            unlink($filename_local);
//                            break;
//                #TCP/IP (communication approach)
//                case "tcp" :
//                            $transportObj = new seg_transport_HL7_file($details);
//
//                            #if ($transportObj->isConnected()){
//                                 #send the message
//                                 $obj = $transportObj->sendHL7MsgtoSocket($filecontent);
//
//                                 #return/print result
//                                 $text = "LIS Server said:: ".$obj;
//                                 #$text = "connected...";
//                            #}else{
//                            #     $text = "Unable to connect to LIS Server. Error: ".$transportObj->error."...";
//                            #}
//
//                            echo $text;
//                            break;
//            }
//
//            #update msg control id
//            $details->msg_control_id = $details->msg_control_id_db;
//            #if new message control id, update the tracker
//            if (!$forreplace)
//                $hl7_ok = $srvObj->updateHL7_msg_control_id($details->msg_control_id);
//
//            #HL7 tracker
//            $srvObj->getInfo_HL7_tracker($details->msg_control_id);
//            $with_rec = $srvObj->count;
//
//            $details->lis_order_no = $lis_order_no;
//            $details->msg_type = $msg_type;
//            $details->event_id = $event_id;
//            $details->refno = $refno;
//
//            $rs_ref = $srvObj->get_OrderNo_by_Refno($refno);
//            $row_ref = $rs_ref->FetchRow();
//
//            $details->pid = $row_ref['pid'];
//            $details->encounter_nr = $row_ref['encounter_nr'];
//
//            $details->hl7_msg =  $filecontent;
//            #$objResponse->alert(print_r($details,1));
//            if ($with_rec){
//                $hl7_ok = $srvObj->updateInfo_HL7_tracker($details);
//            }else{
//                $hl7_ok = $srvObj->addInfo_HL7_tracker($details);
//            }
//         } #if ($connection_type=='hl7')
         
         $objResponse->call('reloadPage');  
     }
     
     return $objResponse;
     
 }

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
require($root_path.'modules/laboratory/ajax/lab-post.common.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

#for HL7 compliant
# Create hl7 object
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_create_hl7_message.php');

# Create file
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_create_hl7_file.php');

# Create file
require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_transport_hl7_file.php');
     
$xajax->processRequest();
?>
