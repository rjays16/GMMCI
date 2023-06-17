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
			} 
		}
        
    }
    
    #------- end of class--------

?>
