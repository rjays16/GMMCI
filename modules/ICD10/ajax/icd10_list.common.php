<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require('./roots.php');
require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
$xajax = new xajax($root_path."modules/ICD10/ajax/icd10_list.server.php");

$xajax->register(XAJAX_FUNCTION, "deleteSelectedICDs");
?>