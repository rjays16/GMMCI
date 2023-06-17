<?php
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php'); 
$xajax = new xajax($root_path.'modules/or/ajax/ajax_anesthesia_gui.server.php');
$xajax->setCharEncoding("iso-8859-1"); 
$xajax->register(XAJAX_FUNCTION,"anesthesia_procedure_save"); //added by cha
$xajax->register(XAJAX_FUNCTION,"anesthesia_edit_category_name");//added by celsy 06/21/10
$xajax->register(XAJAX_FUNCTION,"anesthesia_category_delete");//added by celsy 06/21/10
$xajax->register(XAJAX_FUNCTION,"anesthesia_specific_delete");//added by celsy 06/21/10
$xajax->register(XAJAX_FUNCTION,"anesthesia_specific_edit");//added by celsy 06/21/10    
$xajax->register(XAJAX_FUNCTION,"anesthesia_new_specific_save");//added by celsy 06/21/10
?>
