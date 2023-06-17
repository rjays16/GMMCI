<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_parse_hl7_message.php');
    $parseObj = new seg_parse_msg_HL7();

    require_once($root_path.'include/care_api_classes/seg_hl7/seg_class_hl7.php');
    $hl7fxnObj = new seg_HL7();

    
    /*$hl7_msg = 'MSH|^~\\&|HCLAB||HIS||20131219091433||ORU^R01|HCL00043113771|P|2.3||||||8859
PID|1||2572224||^LERIN ROSARIO||195809210000|F
OBR|1|11618554|130243637|URINE^Urine Examination|R|20131219063631||||||||20131219072058||103006^JEA O. DANAO-UY||ER^Emergency Room - IPD||||20131219091433||||||ER^Emergency Room - IPD
OBX|1|ST|PHYSL^PHYSICAL||\"\"||||||F
OBX|2|ST|UAPP^   Appearance||SLIGHTLY CLOUDY|||N|||F|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|3|ST|UCOL^   Color||YELLOW|||N|||F|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|4|ST|CHEM^CHEMICAL||\"\"||||||F
OBX|5|ST|UPRO^   Protein||NEGATIVE|||N|||F|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|6|ST|UGLU^   Glucose||NEGATIVE|||N|||F|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|7|ST|SPECGR^   Specific Gravity||1.020|||N|||F|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|8|ST|UPH^   pH||6.0|||N|||F|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|9|ST|URIN^URINE FLOWCYTOMETRY||\"\"||||||F
OBX|10|ST|URBC^   RBC||7.0|/uL|0 - 11 /uL|N|||F|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|11|ST|UWBC^   WBC||21|/uL|0 - 17 /uL|H|||F|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|12|ST|UEC^   Epithelial Cells||20|/uL|0 - 17 /uL|H|||F|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|13|ST|UCAST^   Cast||2|/uL|0 - 1 /uL|H|||F|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|14|ST|UBACT^   Bacteria||591|/uL|0 - 278 /uL|H|||F|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
NTE|1||\\.br\\\\.br\\CONVERSION FACTOR: uL TO HPF (X0.18) , uL TO LPF (X2.9)
OBX|15|ST|CRYS^CRYSTALS||\"\"||||||F
OBX|16|ST|AU^   Amorphous Urates||!|||N|||D|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|17|ST|CALC^   Calcium Oxalates||!|||N|||D|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|18|ST|URICA^   Uric Acid||!|||N|||D|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|19|ST|APHOS^   Amorphous Phosphates||!|||N|||D|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|20|ST|TPHOS^   Triple Phosphates||!|||N|||D|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|21|ST|MUCTHR^   Mucus Threads||!|||N|||D|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|22|ST|CYLIN^   Cylindroids||!|||N|||D|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|23|ST|URAT^   Urates||!|||N|||D|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|24|ST|PARA^   Parasites||!|||N|||D|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT
OBX|25|FT|OTH^OTHERS||\"\"|||N|||D|||20131219091428|CTRLAB^CENTRAL LAB|AMBER^KHAREN GRACE R. AMBRAD, RMT';*/
	

	

	$hl7_msg = 'MSH|^~\\&|HCLAB||HIS||20140103135205||ACK^ACK|HCL0004345578|P|2.3||||||8859
			MSA|AA|HIS20120000387069||||^';

	$prefix = "HIS";
    $COMPONENT_SEPARATOR = "^";
    $REPETITION_SEPARATOR = "~";
	
    $segments = explode($parseObj->delimiter, trim($hl7_msg));
    #echo "HL7:";
    #print_r($segments);
	#echo "<br><br>Segments:";
	#$i=1;
    foreach($segments as $segment) {
	    $data = explode('|', trim($segment));
	    #print_r($data);
	    /*sample
            MSH|^~\&|SEGHIS|SPMC|HCLAB|SPMC|20120131202201| |ORM^O01|HIS00001|P|2.3<cr>
                  1    2     3     4     5     6           7    8      9      10 11 12
        */
	    if (in_array("MSH", $data)) {
		    /*$msg_type = explode($COMPONENT_SEPARATOR, $data[8]);
		    $msg_type_id = $msg_type[0];
		    $event_id = $msg_type[1];
		    $hclab_msg_control_nr = $data[9];*/
		    $msh = $parseObj->segment_msh($data);
		}

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
		if (in_array("MSA", $data)) {
			/*$msa_ack_code = $data[1];
		    $start_pos = strrpos($data[2], $prefix);
		    $start = strrpos($data[2], $prefix) + strlen($prefix);
		    $msg_control_id = substr($data[2], $start);*/
		    $msa = $parseObj->segment_msa($data);
		}

		/*sample
            PID|1|   |2000005|   |JOHNNY^LEE|SY|19800919000000|M|   |    |^BAJADA^DAVAO CITY^8000^DAVAO DEL SUR |   |    |   |    |SINGLE|    |    |    |<cr>
                1  2     3     4      5      6      7          8  9   10             11                           12  13   14  15    16    17   18   19
        */
		if (in_array("PID", $data)) {
			#$pid = $data[3];
			$pid = $parseObj->segment_pid($data);
		}


		/*sample
             OBR|1|11191479|  |24UCC^24hr Crea Clearance~CBC^COMPLETE BLOOD COUNT|R|20120127011800|  |   |    |    |    |    |DH1|   |   | 10001^DR. LEOPOLDO VEGA |    |IPD^Inpatient Department|   |   |   |    |    |    |   |   |    |<cr>
                 1   2      3                        4                            5       6         7  8   9    10   11   12  13   14  15      16                    17       18                   19  20  21   22  23    24  25  26  27

             if ORU^R01, only one test code is returned    
        */
		if (in_array("OBR", $data)) {
			#$lis_order_no = $data[3];
			#$test = explode($COMPONENT_SEPARATOR, string);
			$obr = $parseObj->segment_obr($data);
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
		if (in_array("OBX", $data)) {
			#$pid = $data[3];
			$obx = $parseObj->segment_obx($data);
		}
			    
	}

	/*
	MSH|^~\&|HCLAB||HIS||20120823115015||ACK^ACK|HCL0003068760|P|2.3||||||8859
MSA|AA|HIS20120000000001||||^
	*/
	
	#print_r($pid);
	#$msg_control_id = '20120000000001';	
	#$msg_type_id = 'ACK';
	#$event_id = 'ACK';
	$filename = 'HL7WNFS_00000007.hl7';
	#$hl7_msg = $hl7_msg;
	#$filename = 'HL7WNFS_00000014.hl7';

	$data = array
				(
			    	'msg_control_id'=>$msa['msg_control_id'],
                 	'lis_order_no'=>$obr['lis_order_no'],
                 	'msg_type_id' =>$msh['msg_type_id'],
                 	'event_id'=>$msh['event_id'],
                 	'pid'=>$pid['pid'],
                 	'hl7_msg'=>$hl7_msg,
                 	'filename'=>$filename,
                );
	
	#print_r($data);
	#save table seg_hl7_hclab_msg_receipt
	$hl7fxnObj->save_hl7_received($data);


?>
