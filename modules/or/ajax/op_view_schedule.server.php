<?php
   /*created by cha 06-11-09*/
   
   require('./roots.php');
   include_once($root_path.'include/care_api_classes/class_globalconfig.php'); 
   require($root_path.'include/inc_environment_global.php');    
   //require($root_path.'include/care_api_classes/class_cashier_edit_or_no.php');
   require($root_path.'modules/or/ajax/op_view_schedule.common.php');
   $xajax->processRequest();
?>
