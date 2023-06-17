<?php
	# created by VAN 01-12-2012
	# using HL7 approach
	# parse a HL7 message for lab result that fetch from LIS
    
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	
	class seg_parse_msg_HL7{

        var $delimiter;
        
		# constructor
		function seg_parse_msg_HL7(){
		    $this->delimiter = "\015";
		    $this->prefix = "HIS";
		    $this->COMPONENT_SEPARATOR = "^";
		    $this->REPETITION_SEPARATOR = "~";
		}

        function parse(){
			#$fp = file_get_content('file.hl7');
			$fp = fopen('myfile.hl7', 'r'); 
			while (!feof($fp)) { 
				$line = fread($fp, 10240); 
				$data = explode('|', trim($line)); 
				/* 
				Now $data contains one record's worth of data; 
				you can write it into your database 
				*/ 
				print_r($data);
			} 
		}


		#Message Header
		/*sample
            MSH|^~\&|SEGHIS|SPMC|HCLAB|SPMC|20120131202201| |ORM^O01|HIS00001|P|2.3<cr>
                  1    2     3     4     5     6           7    8      9      10 11 12
	    */
		function segment_msh($data){
			$msg_type = explode($this->COMPONENT_SEPARATOR, $data[8]);
		    $msh['msg_type_id'] = $msg_type[0];
		    $msh['event_id'] = $msg_type[1];
		    $msh['hclab_msg_control_nr'] = $data[9];
		    $msh['date_reported'] = $data[6];

		    return $msh;
		}


		#Message Acknowledgment Segment
		/*sample
            #with error    
            MSA|AE|HIS00004|   |   |    |^Invalid Birth date format<cr>
                 1    2      3   4    5         6
                 
            #accepted
            MSA|AA|HCL10021|   |    |    |
                 1     2     3   4     5
            
            #rejected
            MSA|AR|HIS10008|    |    |    |^Order already exist. Request rejected.     
                1     2       3    4    5       6
        */
		function segment_msa($data){
			$msa['ack_code'] = $data[1];
		    $start_pos = strrpos($data[2], $this->prefix);
		    $start = strrpos($data[2], $this->prefix) + strlen($this->prefix);
		    $msa['msg_control_id'] = substr($data[2], $start);
		    
			return $msa;
		}

		/*sample
            PID|1|   |2000005|   |JOHNNY^LEE|SY|19800919000000|M|   |    |^BAJADA^DAVAO CITY^8000^DAVAO DEL SUR |   |    |   |    |SINGLE|    |    |    |<cr>
                1  2     3     4      5      6      7          8  9   10             11                           12  13   14  15    16    17   18   19
        */
		function segment_pid($data){
			$pid['pid'] = $data[3];
			$pid['name'] = $data[5];
			$pid['middle_name'] = $data[6];
			$pid['bdate'] = date('Y-m-d H:i:s',strtotime($data[7]));
			$pid['sex'] = $data[8];

			return $pid;
		}

		/*sample
             OBR|1|11191479|  |24UCC^24hr Crea Clearance~CBC^COMPLETE BLOOD COUNT|R|20120127011800|  |   |    |    |    |    |DH1|   |   | 10001^DR. LEOPOLDO VEGA |    |IPD^Inpatient Department|   |   |   |    |    |    |   |   |    |<cr>
                 1   2      3                        4                            5       6         7  8   9    10   11   12  13   14  15      16                    17       18                   19  20  21   22  23    24  25  26  27

             if ORU^R01, only one test code is returned    
        */
		function segment_obr($data){
			$obr['lis_order_no'] = $data[2];
			$obr['lab_no'] = $data[3];
			$obr['physician'] = $data[16];
			$obr['location'] = $data[18];
			
			$obr['test'] = $data[4];

			$obr['date_received'] = $data[6];

			return $obr;
		}


		/*sample
             OBX|1|ST|BIL-T^Total Bilirrubin|  |17.3|umol/L|2.5-22.2|N|  |  | F |  |    |200411201530|   |TLT^TAN LEE TING<cr>
                 1  2           3             4  5     6       7     8  9 10 11  12  13     14        15       16
             
             OBX|2|ST|TP^Total Protein||75|g/L|66-87|N|||F|||200411201530||TLT^TAN LEE TING<cr>
             OBX|3|ST|ALB^Albumin||43|g/L|33-50|N|||F|||200411201530||TLT^TAN LEE TING<cr>
             OBX|4|ST|GLOB^Globulin||32|g/L|23-45|N|||F|||200411201530||TLT^TAN LEE TING<cr>
             OBX|5|ST|ALP^Alk. Phosphatase||214|U/L|40-115|H|||F|||200411201530||TLT^TAN LEE TING<cr>
             OBX|6|ST|ALT^ALT (SGPT)||45|U/L|5-41|H|||F|||200411201530||TLT^TAN LEE TING<cr>
        */
		#multiple
        function segment_obx($data){
        	$obx['ordering'] = $data[1];
        	$obx['testservice'] = $data[3];
        	$obx['result'] = $data[5];
        	$obx['units'] = $data[6];
        	$obx['reference_range'] = $data[7];
        	$obx['result_flag'] = $data[8];
        	$obx['result_status'] = $data[11];
        	$obx['date_observed'] = $data[14];
        	$obx['medtech'] = $data[16];

        	return $obx;
        }

        /*sample
        	notes and comments
			NTE|1|   |This Patient is suspect to have dengue<cr>
			    1  2     3	 

        	NTE|1||\\\\.br\\\\CONVERSION FACTOR: uL TO HPF (X0.18) , uL TO LPF (X2.9)

        */     
        function segment_nte($data, $index){
        	$nte['comment'] = $data[3];
        	$nte['index'] = $index;
        	return $nte;
        }


		function parse_segment($hl7_msg){
			$segments = explode($this->delimiter, trim($hl7_msg));

			foreach($segments as $segment) {
			    $data = explode('|', trim($segment));
			    
			    if (in_array("MSH", $data)) {

			    }
			    	
			    #save segment to table
			}
		}

    }
    
    #------- end of class--------

?>
