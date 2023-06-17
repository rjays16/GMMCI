<?php
	# created by VAN 01-12-2012
	# using HL7 approach
	# creating a message for lab order that to be send to LIS
    
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	
	class seg_create_msg_HL7{

        var $delimiter;
        
		# constructor
		function seg_create_msg_HL7(){
		    $this->delimiter = "\015";
		}

        # Message Header
		function createSegmentMSH($details){
            # MSH indicates the beginning of a HL7 message
			
			      # 3. sending application
              $_msh_2 = $details->system_name;
            
            # 4. sending facility
              $_msh_3 = $details->hosp_id;
            
            # 5. receiving application
              $_msh_4 = $details->lis_name;
              
            # 6. receiving facility
              $_msh_5 = $details->hosp_id;
              
            # 7. date and time of message
              $_msh_6 = $details->currenttime;  
            
            # 9. message type
              $_msh_8 = $details->msg_type;  
            
            # 10. message control ID
              $_msh_9 = $details->msg_control_id;
              
            /*sample
            MSH|^~\&|SEGHIS|SPMC|HCLAB|SPMC|20120131202201| |ORM^O01|HIS00001|P|2.3<cr>
                  1    2     3     4     5     6           7    8      9      10 11 12
            */
               
            $msh = "MSH|^~\&|".$_msh_2."|".$_msh_3."|".$_msh_4."|".$_msh_5."|".$_msh_6."|".$_msh_7."|".$_msh_8."|".$_msh_9."|P|2.3".$this->delimiter;
            
            return($msh);
		}

        # Patient Identification
        # for PID => patient identification segment
		function createSegmentPID($details){
           # 1. set ID - Patient
           # always 1
           
           # 3. patient ID (internal id)
           # hospital id or hrn
             $_pid_3 = $details->POH_PAT_ID;
           
           # 4. alternate patient ID
             $_pid_4 = $details->POH_PAT_ALTID;
             
           # 5. patient name
           # first name^last name
             $_pid_5 = $details->patient_name;   
           
           # 6. mother's maiden name
             $_pid_6 = $details->POH_MIDDLENAME;   
           
           # 7. date and time of birth
           # YYYYMMDDHHMISS
             $_pid_7 = $details->POH_PAT_DOB;
           
           # 8. sex
           # F - female ; M - male ; U - Unknown; O - Other
             $_pid_8 = $details->POH_PAT_SEX;
           
           # 11. patient address
           # street address^other designation^city^state or province^postal code
             $_pid_11 = $details->address;   
           
           # 16. marital status
             $_pid_16 = $details->POH_CIVIL_STAT;   
           
           /*sample
            PID|1|   |2000005|   |JOHNNY^LEE|SY|19800919000000|M|   |    |^BAJADA^DAVAO CITY^8000^DAVAO DEL SUR |   |    |   |    |SINGLE|    |    |    |<cr>
                1  2     3     4      5      6      7          8  9   10             11                           12  13   14  15    16    17   18   19
            */
            
            $pid = "PID|1||".$_pid_3."||".$_pid_5."|".$_pid_6."|".$_pid_7."|".$_pid_8."|||".$_pid_11."|||||".$_pid_16."||||".$this->delimiter;
            
            return($pid);
        }
        
        # Patient Visit
        # for PV1 => patient visit
        function createSegmentPV1($details){ 
           # 1. Set ID - PV1
           # always 1
           $_pv1_1 = $details->setID;
           
           # 2. patient class
           # IN - Inpatient; OP - Outpatient; ER - ER Patient; WN - Walkin; RDU - RDU; IC - Industrial Clinic
           $_pv1_2 = $details->POH_PAT_TYPE;
           
           # 3. assigned patient location
           # ward^room^bed
           $_pv1_3 = $details->location;
           
           # 6. prior patient location
           # ward^room^bed
           #$_pv1_6 = $details->POH_LOC_CODE;
           
           # 7. attending doctor
           # id^name (firstname lastname)
           $_pv1_7 = $details->requesting_doc;
           
           # 19. Visit Number
           # encounter nr
           $_pv1_19 = $details->POH_PAT_CASENO;
           
           # 36. Discharge disposition
           # blank
           
           # 44. admit date time
           # YYYYMMDDHHMISS
           
           /*sample
             PV1|1|IN|WARD2^ROOM2^BED2|  |  | WARD1^ROOM1^BED1|10001^LEOPOLDO VEGA|   |   |    |    |   |   |   |   |   |    |    |20120131101913<cr>
                 1  2    3             4   5       6                7               8   9   10   11   12  13  14  15  16  17   18       19
           */
           
           $pv1 = "PV1|".$_pv1_1."|".$_pv1_2."|".$_pv1_3."|||".$_pv1_6."|".$_pv1_7."||||||||||||".$_pv1_19.$this->delimiter;
            
           return($pv1);
           
        }
        
        # Common Order Segment
        # for ORC => common order segment
        function createSegmentORC($details){
			
            # 1. order control
			# NW = New
			# RP = Replacement
			# CA = Cancellation
            
            $_orc_1 = $details->order_control;
            
            /*sample
                ORC|NW|   |   |    |    |    |    |    |    |    |    |    |    |    |    |    |    |    |    |<cr>
                    1   2   3   4    5    6     7    8    9   10   11   12    13   14   15  16   17   18   19
            */
            $orc = "ORC|".$_orc_1."|||||||||||||||||||".$this->delimiter;
            
            return($orc);    
		}

        # Observation Order Segment
        # for OBR => observation order segment
		function createSegmentOBR($details){
			# 1. set ID ? OBR
            # default 1
            
            # 2. placer order number
            # lis order number
            $_obr_2 = $details->POH_ORDER_NO;
            
            # 4. universal service id
            # order items
            # Test ID1^Test Description1~Test ID2^Test Description2
            # 24UCC^24hr Crea Clearance~CBC^COMPLETE BLOOD COUNT~CREA^Creatinine
            # ~ for multiple occurence
            $_obr_4 = $details->service_list;
            
            # 5. priority
            # R - routine; S - stat; default R
            $_obr_5 = $details->POH_PRIORITY2;
            
            # 6. requested date and time
            # YYYYMMDDHHMISS
            $_obr_6 = $details->POH_TRX_DT;
            
            # 13. relevant clinical info
            $_obr_13 = $details->POH_CLI_INFO;
            
            # 16. placer
            # physician ID^Name (Dr Firstname Lastname)
            $_obr_16 = $details->doctor;
            
            # 18. placer location
            # location id^name ====> clinic or ward
            $_obr_18 = $details->location_dept;
            
            /*sample
                 OBR|1|11191479|  |24UCC^24hr Crea Clearance~CBC^COMPLETE BLOOD COUNT|R|20120127011800|  |   |    |    |    |    |DH1|   |   | 10001^DR. LEOPOLDO VEGA |    |IPD^Inpatient Department|   |   |   |    |    |    |   |   |    |<cr>
                     1   2      3                        4                            5       6         7  8   9    10   11   12  13   14  15      16                    17       18                   19  20  21   22  23    24  25  26  27
            */
            
            $obr = "OBR|1|".$_obr_2."||".$_obr_4."|".$_obr_5."|".$_obr_6."|||||||".$_obr_13."|||".$_obr_16."||".$_obr_18."||||||||||".$this->delimiter;
            
            return($obr);    
		}
        
        # Observation/Result Segment
        # for OBX => observation/result segment
        function createSegmentOBX($details){
            # 1. set ID - OBX
            # OBX|1|...., OBX|2|...., OBX|3|
            #must be incremental
            $_obx_1 = "1";
            
            # 2. value type
            # ST = string; CE = coded entry (ex. POS^POSITIVE); TX = Long Text (ex. Microbiology Result)
            $_obx_2 = "";
            
            # 3. observation identifier
            # Test ID^Test Name (ex. TP^Total Protein)    
            $_obx_3 = "";
            
            # 5. Observation Value
            $_obx_5 = "";
            
            # 6. Units
            $_obx_6 = "";
            
            # 7. References Range
            $_obx_7 = "";
            
            # 8. Abnormal Flags
            #  N = normal; L = below low normal; H = below hight normal; LL = above low panic; HH = above high panic        
            $_obx_8 = "";
            
            # 11. Observe Result Status
            # I - Result Pending; F - final result
            $_obx_11 = "";
            
            # 14. Date and Time of the Observation
            # YYYYMMDDHISS
            $_obx_14 = "";
            
            # 16. Responsible Observer
            # Observer ID^Name
            $_obx_16 = "";
            
            /*sample
                 OBX|1|ST|BIL-T^Total Bilirrubin|  |17.3|umol/L|2.5-22.2|N|  |  | F |  |    |200411201530|   |TLT^TAN LEE TING<cr>
                     1  2           3             4  5     6       7     8  9 10 11  12  13     14        15       16
                 
                 OBX|2|ST|TP^Total Protein||75|g/L|66-87|N|||F|||200411201530||TLT^TAN LEE TING<cr>
                 OBX|3|ST|ALB^Albumin||43|g/L|33-50|N|||F|||200411201530||TLT^TAN LEE TING<cr>
                 OBX|4|ST|GLOB^Globulin||32|g/L|23-45|N|||F|||200411201530||TLT^TAN LEE TING<cr>
                 OBX|5|ST|ALP^Alk. Phosphatase||214|U/L|40-115|H|||F|||200411201530||TLT^TAN LEE TING<cr>
                 OBX|6|ST|ALT^ALT (SGPT)||45|U/L|5-41|H|||F|||200411201530||TLT^TAN LEE TING<cr>
            */
            
            $obx = "OBX|".$_obx_1."|".$_obx_2."|".$_obx_3."||".$_obx_5."|".$_obx_6."|".$_obx_7."|".$_obx_8."|||".$_obx_11."|||".$_obx_14."||".$_obx_16.$this->delimiter;
            
            return($obx);
        }
        
        # Notes and comments Segment
        # for NTE => notes and comments segment
        function createSegmentNTE($details){
           # 1. set ID - NTE
             $_nte_1 = "1";
           
           # 3. comment
             $_nte_3 = "";
           
           /*sample
                 NTE|1|   |This Patient is suspect to have dengue<cr>
            *        1  2               3
            */
            
            $nte = "NTE|".$_nte_1."||".$_nte_3.$this->delimiter;
            
            return($nte);  
        }
        
        # Message Acknowledgment
        # for MSA => message acknowledgment
        function createSegmentMSA($details){ 
           # 1. acknowledgment code
           # AA-accept; AE=error; AR=reject
             $_msa_1 = $details->ack_code;
             
           # 2. message control ID
           # message control ID of the message sent by the sendig systme
              $_msa_2 = $details->msg_control_id_hclab;
              
           # 6. error condition
              $_msa_6 = $details->error;  
           
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
           
           #with error 
           #$msa = " MSA|AE|HIS00004||||^Invalid Birth date format<cr>";
            
           $msa = "MSA|".$_msa_1."|".$_msa_2."||||".$_msa_6.$this->delimiter;
            
            return($msa);    
        }
        
    }
    
    #------- end of class--------

?>
