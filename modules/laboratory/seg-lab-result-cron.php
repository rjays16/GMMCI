<?php
  require('./roots.php');
  require($root_path.'include/inc_environment_global.php');
    
  require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
  $objInfo = new Hospital_Admin();
  
   $row_hosp = $objInfo->getAllHospitalInfo();
   
   $connection_type = $row_hosp['connection_type'];
   if ($connection_type=='odbc'){
       echo "<br> Connecting to LIS using ODBC connection..."; 
       require_once($root_path.'modules/laboratory/seg_lab_cron.php');    
        
   }else{
       echo "<br> Connecting to LIS using HL7 connection..."; 
       require_once($root_path.'modules/laboratory/seg_hl7/seg-lab-hl7-cron.php');    
        
   }    
?>
