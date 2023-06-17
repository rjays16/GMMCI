<?php
      //created by cha 06-11-09
    require('./roots.php');
    require_once($root_path.'classes/xajax_0.5/xajax_core/xajax.inc.php');
    $xajax = new xajax($root_path."modules/or/ajax/op_view_schedule.server.php");
    $xajax->setCharEncoding("iso-8859-1");
    $xajax->register(XAJAX_FUNCTION,"populateORPersonnel");
?>
