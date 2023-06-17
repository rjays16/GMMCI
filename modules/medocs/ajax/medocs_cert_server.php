<?php
 function getPersonnelPosition($personell_nr, $document){
     global $db;
     $objResponse = new xajaxResponse();
     $pers_obj=new Personell;
     
     $objResponse->alert('nc = '.$personell_nr." = ".$document);
 
     return $objResponse;
 }
 

require('./roots.php');
require($root_path.'modules/medocs/ajax/medocs_cert_common.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
     
$xajax->processRequest();
?>
