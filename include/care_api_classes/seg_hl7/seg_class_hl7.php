<?php
	# created by VAN 01-12-2012
	# using HL7 approach
	
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
    require_once($root_path.'include/care_api_classes/class_core.php');
    require_once($root_path.'include/inc_date_format_functions.php');
	
	class seg_HL7 extends Core {

        function save_hl7_received($data){
            global $db;
        
            $date_created = date("Y-m-d H:i:s");

            extract($data);

            $result = $db->Replace('seg_hl7_hclab_msg_receipt',
                                                array(
                                                         'filename'=>$db->qstr($filename),
                                                         'msg_control_id'=>$db->qstr($msg_control_id),
                                                         'lis_order_no'=>$db->qstr($lis_order_no),
                                                         'msg_type_id' =>$db->qstr($msg_type_id),
                                                         'event_id'=>$db->qstr($event_id),
                                                         'pid'=>$db->qstr($pid),
                                                         'test'=>$db->qstr($test),
                                                         'hl7_msg'=>$db->qstr($hl7_msg),
                                                         'date_update'=>$db->qstr($date_created)
                                                    ),
                                                    array('filename'),
                                                    $autoquote=FALSE
                                               );
                                               
             if ($result) 
                return TRUE;
             else{
                #$this->errormsg = $filename.", ".$db->ErrorMsg();
                return FALSE;
                
             }   
        }

        function update_parse_status($details){
            global $db;
            
            $this->sql = "UPDATE seg_hl7_file_received SET 
                            parse_status = '".$details->parse_status."'
                          WHERE filename='".$details->filename."'";
            
            if ($db->Execute($this->sql)) {
                if ($db->Affected_Rows()) {
                    $ret=TRUE;
                }
            }
            if ($ret)    return TRUE;
            else return FALSE;
        }

        #get all HL7 result
        function getAllResultByOrder($pid, $lis_order_no){
            global $db;

            $this->sql = "SELECT fn_get_person_name_mname(".$db->qstr($pid).") AS patient_name, 
                            (SUBSTRING(MAX(CONCAT(h.date_update,h.hl7_msg)),20)) AS hl7_msg,
                            (SUBSTRING(MAX(CONCAT(h.date_update,h.filename)),20)) AS filename,
                            f.date_received AS date_update
                            FROM seg_hl7_hclab_msg_receipt h
                            INNER JOIN seg_hl7_file_received f ON f.filename=h.filename
                            WHERE pid=".$db->qstr($pid)." 
                            AND lis_order_no=".$db->qstr($lis_order_no)." 
                            AND msg_type_id='ORU' AND event_id='R01'
                            GROUP BY h.test, pid, lis_order_no
                            ORDER BY (SUBSTRING(MAX(CONCAT(h.date_update,h.date_update)),20)) DESC";

            if ($this->result=$db->Execute($this->sql)) {
                if ($this->count=$this->result->RecordCount()){
                    return $this->result;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }


        function getAllHL7Pending($status, $cond=''){
            global $db;

            if ($cond)
                $conds = "AND hl7_msg LIKE '%$cond%'";

            $this->sql = "SELECT * FROM seg_hl7_file_received 
                          WHERE parse_status=".$db->qstr($status)."
                          ".$conds."
                          ORDER BY date_received DESC
                          LIMIT 100";

            if ($this->result=$db->Execute($this->sql)) {
                if ($this->count=$this->result->RecordCount()){
                    return $this->result;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }


        function getAllHL7WOTest(){
            global $db;

            $this->sql = "SELECT * FROM seg_hl7_hclab_msg_receipt 
                          WHERE test IS NULL
                          ORDER BY date_update DESC
                          LIMIT 100";

            if ($this->result=$db->Execute($this->sql)) {
                if ($this->count=$this->result->RecordCount()){
                    return $this->result;
                }else{
                    return FALSE;
                }
            }else{
                return FALSE;
            }
        }    
        
    }    
    #------- end of class--------

?>
