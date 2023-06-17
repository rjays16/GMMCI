<?php
require('./roots.php');
require_once($root_path.'classes/xajax-0.2.5/xajax.inc.php');
$xajax = new xajax($root_path.'modules/nursing/ajax/nursing-station-new-server.php');

//register fucntion here
$xajax->registerFunction("PopulateRow");
$xajax->registerFunction("saveWardRoom");
$xajax->registerFunction("isWardIDExistsTest");

#added by VAN 06-24-08
$xajax->registerFunction("checkRoomNrExists");

//Added by Omick, April, 29 2009 12:31 ng madaling araw
$xajax->registerFunction('plot_points');
$xajax->registerFunction('update_header');
$xajax->registerFunction('submit_update_header');
$xajax->registerFunction('update_footer');
$xajax->registerFunction('submit_update_first_footer');
$xajax->registerFunction('submit_update_second_footer');
$xajax->registerFunction('submit_update_third_footer');

#added by VAN 10-30-09
$xajax->registerFunction('setMGH');
$xajax->registerFunction('cancelDischarged');

?>