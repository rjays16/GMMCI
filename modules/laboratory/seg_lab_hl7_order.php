<?php
	# created by VAN 01-12-2012
	# using HL7 approach
	# creating a message for lab order that to be send to LIS
    
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	#require_once($root_path."/classes/php_hl7/Net/HL7.php");
	require_once($root_path."/classes/php_hl7/Net/HL7/Connection.php");

	require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

	# Create laboratory object
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');


	class lab_order_Net_HL7 extends Net_HL7{

		var $refno;
		var $hosp_id;
		var $system_name;
		var $lis_name;
		var $hl7;
		var $msg_id;
		var $msg_order;
		var $srvObj;
        var $order_header_msg;

		# constructor
		function lab_order_Net_HL7($refno){
				global $db;
				$this->srvObj = new SegLab();
				$objInfo = new Hospital_Admin();

				$this->hl7 = new Net_HL7();

				$this->refno = $refno;

				$row_hosp = $objInfo->getAllHospitalInfo();
				$row_comp = $objInfo->getSystemCreatorInfo();

				# get or fetch data from seghis using the refno
				# and lab test request which is included in LIS and with specimen
				$lab_order_header_info = $this->srvObj->getOrderHeader($this->refno);
				#echo $this->srvObj->sql;
                extract($lab_order_header_info);

				#get value
				foreach($lab_order_header_info as $key => $value){
					$this->$key = $value;
				}

				$this->hosp_id = $row_hosp['hosp_id'];
				$this->system_name = $row_comp['system_id'];
				$this->lis_name = $row_comp['lis_name'];

				$this->msg_id = $this->srvObj->getLastMsgID();

				$this->msg_order  = new Net_HL7_Message();
		}

		function createOrderSegmentMSH(){

			#for MSH => message segment header
			$msh_order = new Net_HL7_Segment("MSH");

			#field separator
			$msh_order->SetField(1,$this->hl7->setFieldSeparator());
			#encoding character
			$enc_char = $this->hl7->_hl7Globals['COMPONENT_SEPARATOR'].$this->hl7->_hl7Globals['REPETITION_SEPARATOR'].
			$this->hl7->_hl7Globals['ESCAPE_CHARACTER'].$this->hl7->_hl7Globals['SUBCOMPONENT_SEPARATOR'];

			$msh_order->SetField(2,$enc_char);
			#sending application
			$msh_order->SetField(3,$this->system_name);
			#sending facility
			$msh_order->SetField(4,$this->hosp_id);
			#receiving application
			$msh_order->SetField(5,$this->lis_name);
			#receiving facility
			$msh_order->SetField(6,$this->hosp_id);
			#Date/Time of Message
			$msh_order->setField(7, strftime("%Y%m%d%H%M%S"));
			#Security
			$msh_order->SetField(8,'');
			#message type
			#ORM = order message; O01 = observation order
			#$msg_type = "ORM^O01";
			$MessageType = "ORM";
			$TriggerEvent = "O01";
			$MessageStructure = $MessageType.$this->hl7->_hl7Globals['COMPONENT_SEPARATOR'].$TriggerEvent;
			$msh_order->SetField(9,$MessageStructure);

			#message control id
			#$msh_order->SetField(10,$msh_order->getField(7) . rand(10000, 99999));
            $msh_order->SetField(10,$this->msg_id);
            
			#processing id
			$process_id = "P";
			$msh_order->SetField(11,$process_id);

			# version ID
			$msh_order->SetField(12,$this->hl7->_hl7Globals['HL7_VERSION']);

			#character set ID
			$char_id = "";
			$msh_order->SetField(18,$char_id);
			$msh_order->setField(19, $this->hl7->_hl7Globals['SEG_SEGMENT_SEPARATOR']);
			
            $this->msg_order->addSegment($msh_order);
            #----------------#MSH HEADER
		}

		function createOrderSegmentPID(){

			#for PID => patient identification segment
			$seg_order_1 = new Net_HL7_Segment("PID");

			#$this->POH_TRX_DT = date("n/j/Y g:i:s A",strtotime($this->POH_TRX_DT));
			$this->POH_TRX_DT = date("YmdHis",strtotime($this->POH_TRX_DT));
			#$this->POH_ORDER_DT = date("n/j/Y g:i:s A",strtotime($this->POH_ORDER_DT));
			$this->POH_ORDER_DT = date("YmdHis",strtotime($this->POH_ORDER_DT));

			$this->POH_TRX_ID = "N";    # new order  ; U for update
			$this->POH_TRX_STATUS = "N";   # before read by LIS default value

			#$this->POH_PAT_DOB = date("n/j/Y",strtotime($this->POH_PAT_DOB));
			$this->POH_PAT_DOB = date("YmdHis",strtotime($this->POH_PAT_DOB));

			#SET ID - patient; always 1
			$seg_order_1->setField(1, 1);
			#patient id - external; optional
			$seg_order_1->setField(2, '');
			#patient id - internal
			$seg_order_1->setField(3, $this->POH_PAT_ID);
			#alternate patient id
			$seg_order_1->setField(4, $this->POH_PAT_ALTID);
			#patient name
			$patient_name = $this->POH_FIRSTNAME.$this->hl7->_hl7Globals['COMPONENT_SEPARATOR'].$this->POH_LASTNAME;
			$seg_order_1->setField(5, $patient_name);
			#mother's maiden name - optional
			$seg_order_1->setField(6, $this->POH_MIDDLENAME);
			#date/time of birth
			$seg_order_1->setField(7, $this->POH_PAT_DOB);
			#sex
			$seg_order_1->setField(8, $this->POH_PAT_SEX);
			#patient alias
			$seg_order_1->setField(9, '');
			#race
			$seg_order_1->setField(10, '');
			#patient address
			$address = $this->POH_STREET.$this->hl7->_hl7Globals['COMPONENT_SEPARATOR'].$this->POH_BRGY.$this->hl7->_hl7Globals['COMPONENT_SEPARATOR'].
								 $this->POH_CITY.$this->hl7->_hl7Globals['COMPONENT_SEPARATOR'].$this->POH_ZIPCODE.$this->hl7->_hl7Globals['COMPONENT_SEPARATOR'].
								 $this->POH_PROVINCE;
			$seg_order_1->setField(11, $address);
			#country code
			$seg_order_1->setField(12, '');
			#home phone number
			$seg_order_1->setField(13, '');
			#business phone number
			$seg_order_1->setField(14, '');
			#primary language
			$seg_order_1->setField(15, '');
			#marital status
			$seg_order_1->setField(16, $this->POH_CIVIL_STAT);
			#religion
			$seg_order_1->setField(17, '');
			#patient account number
			$seg_order_1->setField(18, '');
			#SSS number
			$seg_order_1->setField(19, '');
			$seg_order_1->setField(20, $this->hl7->_hl7Globals['SEG_SEGMENT_SEPARATOR']);
			$this->msg_order->addSegment($seg_order_1);
			#---------------#for PID
		}

		function createOrderSegmentORC(){
			#for ORC => common order segment
			$seg_order_2 = new Net_HL7_Segment("ORC");
			# NW = New
			# RP = Replacement
			# CA = Cancellation

			#--- add condition here that identify if the it is new request, replacement or a cancellation
			#temp value
			$order_control = "NW";

			$seg_order_2->setField(1, $order_control);
			$seg_order_2->setField(20, $this->hl7->_hl7Globals['SEG_SEGMENT_SEPARATOR']);
			$this->msg_order->addSegment($seg_order_2);
			#---------------#for ORC
		}

		function createOrderSegmentOBR(){
			#for OBR => observation order segment
			$seg_order_3 = new Net_HL7_Segment("OBR");
			#set ID ? OBR => order number
			$setID = 1;    #default
			$seg_order_3->setField(1, $setID);
			#placer order number
			$seg_order_3->setField(2, $this->POH_ORDER_NO);
			#filler order number
			$seg_order_3->setField(3, '');
			#universal service id
			$result = $this->srvObj->getRequestDetailsbyRefno($this->refno);
			$count = $this->srvObj->FoundRows();

			while($row_test=$result->FetchRow()){
				$service .= $row_test['service_code'].$this->hl7->_hl7Globals['COMPONENT_SEPARATOR'].
										$row_test['name'].$this->hl7->_hl7Globals['REPETITION_SEPARATOR'];
			}

			$service = trim($service);
			$service_list = substr($service,0,strlen($service)-1);

			$seg_order_3->setField(4, $service_list);
			#priority
			# R=routine , S=STAT
			$seg_order_3->setField(5, $this->POH_PRIORITY2);
			#requested date/time
			$seg_order_3->setField(6, $this->POH_TRX_DT);
			#observation date/time
			$seg_order_3->setField(7, '');
			#observation end date/time
			$seg_order_3->setField(8, '');
			#collection volume
			$seg_order_3->setField(9, '');
			#collector identifier
			$seg_order_3->setField(10, '');
			#specimen action code
			$seg_order_3->setField(11, '');
			#danger code
			$seg_order_3->setField(12, '');

			#clinical information = optional
			$seg_order_3->setField(13, $this->POH_CLI_INFO);
			#placer = optional
			$doctor = $this->POH_DR_CODE.$this->hl7->_hl7Globals['COMPONENT_SEPARATOR'].$this->POH_DR_NAME;
			$seg_order_3->setField(14, $doctor);
			#placer location = optional
			#$location = $this->POH_LOC_CODE.$this->hl7->_hl7Globals['COMPONENT_SEPARATOR'].$this->POH_LOC_NAME;
			$location_dept = $this->POH_LOC_CODE2.$this->hl7->_hl7Globals['COMPONENT_SEPARATOR'].$this->POH_LOC_NAME2;
			$seg_order_3->setField(15, $location_dept);

			$seg_order_3->setField(16, $this->hl7->_hl7Globals['SEG_SEGMENT_SEPARATOR']);
			$this->msg_order->addSegment($seg_order_3);

			#---------------#for OBR
		}

		function createOrderMessage(){
			$this->createOrderSegmentMSH();
			$this->createOrderSegmentPID();
			$this->createOrderSegmentORC();
			$this->createOrderSegmentOBR();
			$this->order_header_msg = $this->msg_order->toString();
			print_r($order_header_msg);
		}

	}

	$refno = $_GET['refno'];
	#temp value to set the refno;
	#$refno = '2011000029';   # ER
	#$refno = '2011000031';   # walkin
	#$refno = '2012000013';   # opd
	$refno = '2012000014';    # inpatient


	$rep = new lab_order_Net_HL7($refno);
	$rep->createOrderMessage();
    
    #echo "<br>msg = ".$rep->msg_id;
    #create file
    #$ourFileName = 'd:/vanFile.txt';
    $ourFileName = "d:/HIS_HL7/".$rep->msg_id.".HL7";
    $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
    fclose($ourFileHandle);


    $filename = $ourFileName;
    #$filecontent = "magpatulog kau pls.....\n";
    $filecontent = $rep->order_header_msg;

    // Let's make sure the file exists and is writable first.
    if (is_writable($filename)) {

        // In our example we're opening $filename in append mode.
        // The file pointer is at the bottom of the file hence 
        // that's where $filecontent will go when we fwrite() it.
        if (!$handle = fopen($filename, 'a')) {
             echo "Cannot open file ($filename)";
             exit;
        }

        // Write $filecontent to our opened file.
        if (fwrite($handle, $filecontent) === FALSE) {
            echo "Cannot write to file ($filename)";
            exit;
        }
        
        echo "Success, wrote ($filecontent) to file ($filename)";
        
        fclose($handle);
                        
    } else {
        echo "The file $filename is not writable";
    }


?>