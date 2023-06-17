<?php
	# created by VAN 01-12-2012
	# using HL7 approach
	# creating a message for lab order that to be send to LIS
    
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	
	class seg_create_HL7_file{

        var $path;
        var $extension;
        
		# constructor
		function seg_create_HL7_file($connectInfo){
            #$this->local_path = 'D:/HIS_HL7/outbox/';
            $this->local_path = $connectInfo->directory_local;
            $this->hclab_path = $connectInfo->directory;
            $this->extension = ".".$connectInfo->extension;
        }
        
        function create_file_to_local($filename){
            $ourFileName = $this->local_path.$filename.$this->extension;
            #$ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
            $ourFileHandle = fopen($ourFileName, 'w');
            fclose($ourFileHandle);
            
            return ($ourFileName);
        }
        
        function create_file_to_hclab($filename){
            $ourFileName = $this->hclab_path.$filename.$this->extension;
            #$ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
            $ourFileHandle = fopen($ourFileName, 'w');
            fclose($ourFileHandle);
            
            return ($ourFileName);
        }
        
        function write_file($filename,$filecontent=''){
            // Let's make sure the file exists and is writable first.
            #if (is_writable($filename)) {

                // In our example we're opening $filename in append mode.
                // The file pointer is at the bottom of the file hence 
                // that's where $filecontent will go when we fwrite() it.
                if (!$handle = fopen($filename, 'a')) {
                     #echo "<br>Cannot open file ($filename)";
                     #exit;
                }

                // Write $filecontent to our opened file.
                if (fwrite($handle, $filecontent) === FALSE) {
                    #echo "<br>Cannot write to file ($filename)";
                    #exit;
                }
                
                #echo "<br>Success, wrote ($filecontent) to file ($filename)";
                
                fclose($handle);
                                
            #} else {
            #    echo "<br>The file $filename is not writable";
           # }
        }
        
    }    
    #------- end of class--------

?>
