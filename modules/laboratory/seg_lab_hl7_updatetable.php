<?php
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');

	require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_hl7.php');
    $hl7fxnObj = new seg_HL7();

    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_parse_hl7_message.php');
    $parseObj = new seg_parse_msg_HL7();
	
	echo "Locking bot...";

	#$hl7fxnObj->startTrans();
	$datenow = date('Y-m-d');
	
	$details->parse_status = 'pending';

	$rs = $hl7fxnObj->getAllHL7Pending($details->parse_status);
	#echo $hl7fxnObj->sql;

	if (is_object($rs)){
		while($row = $rs->FetchRow()){

			$details->hl7_msg = $row['hl7_msg'];
			$details->filename = $row['filename'];

			$segments = explode($parseObj->delimiter, trim($details->hl7_msg));

			#set all arrays to null
            unset($msh);
            unset($msa);
            unset($pid);
            unset($obr);

            foreach($segments as $segment) {
                $data = explode('|', trim($segment));

                if (in_array("MSH", $data)) {
                    $msh = $parseObj->segment_msh($data);
                }

                if (in_array("MSA", $data)) {
                    $msa = $parseObj->segment_msa($data);
                }

                if (in_array("PID", $data)) {
                    $pid = $parseObj->segment_pid($data);
                }

                if (in_array("OBR", $data)) {
                    $obr = $parseObj->segment_obr($data);
                }    
            }    

            $arr_test = explode($parseObj->COMPONENT_SEPARATOR, trim($obr['test']));
            $testcode = $arr_test[0];

            $dataarr = array
                        (
                            'msg_control_id'=>$msa['msg_control_id'],
                            'lis_order_no'=>$obr['lis_order_no'],
                            'msg_type_id' =>$msh['msg_type_id'],
                            'event_id'=>$msh['event_id'],
                            'pid'=>$pid['pid'],
                            'test'=>$testcode,
                            'hl7_msg'=>$row['hl7_msg'],
                            'filename'=>$row['filename'],
                        );

            #save table seg_hl7_hclab_msg_receipt
            $success = $hl7fxnObj->save_hl7_received($dataarr);

            #flag parsing status to done
            if ($success){
               $details->parse_status = 'done'; 
               $ok = $hl7fxnObj->update_parse_status($details); 
               if ($ok)
               		echo "<br>Successfully parsed filename ".$row['filename'];
               else
               		echo "<br>Failed parsed filename ".$row['filename']." with Error ".$hl7fxnObj->ErrorMsg();	
            }
		}
	}	
	#--------------

	#if (!$ok) $hl7fxnObj->FailTrans();
	#	$hl7fxnObj->CompleteTrans();
	echo "<br>Lock released...";
?>
