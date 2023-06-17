<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root= directory
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');   
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path.'modules/or/ajax/op-request-new.common.php'); //load the xajax module  

$css_and_js = array( '<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/or_main/calendar/super_calendar_style.css" /> '
                    , '<script type="text/javascript" src="'.$root_path.'modules/or/or_main/calendar/super_calendar.js"></script>'
                    , '<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
                    , '<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/jqmodal/jqModal.css">'
                    , '<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqModal.js"></script>'
                    , '<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqDnR.js"></script>'
                    , '<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/dimensions.js"></script>'
                    , '<link rel="stylesheet" href="'.$root_path.'modules/or/css/or_main.css" type="text/css" />'
                    , $xajax->printJavascript($root_path.'classes/xajax-0.2.5'));
$smarty = new Smarty_Care('or_main_calendar');
$smarty->assign('css_and_js', $css_and_js);

$smarty->assign('close_events', '<a href="javascript:void(0)" id="close_events" class="jqmClose"></a>');

$smarty->assign('sMainBlockIncludeFile','or/or_main_calendar.tpl'); //Assign the select_or_request template to the frameset
$smarty->display('common/mainframe.tpl'); //Display he contents of the frame 
?>

<script>
$().ready(function() {
  $('#or_main_events')
    .jqDrag('.jqDrag');

  
  $('#or_main_events').jqm({
overlay: 80,
onShow: function(h) {
  h.w.fadeIn(1000, function(){h.o.show();}); 
},
onHide: function(h){
  h.w.fadeOut(1000, function(){h.o.remove();});
  remove_events();
}});
 
});



function show_popup(month, day, year, get_what) {
  $('#or_main_events').jqmShow();
  xajax_populate_events(month, day, year, get_what);
}
</script>