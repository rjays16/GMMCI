<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root= directory
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');   
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
include_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path . 'modules/or/ajax/order.common.php');
$department = new Department();
                                                                                        

$smarty = new Smarty_Care('select_or_request');
$smarty->assign('sToolbarTitle',"OR Main::List of OR Cases"); //Assign a toolbar title
$smarty->assign('sWindowTitle',"OR Main::List of OR Cases"); //Assign a toolbar title
$css_and_js = array('<link rel="stylesheet" href="'.$root_path.'modules/or/css/select_or_request.css" type="text/css" />'
                    ,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/flexigrid/css/flexigrid/flexigrid.css">'
                    ,'<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
                    ,'<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/flexigrid.js"></script>'
                    ,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jquery.tabs/jquery.tabs.pack.js"></script>'
                    ,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/jquery.tabs/jquery.tabs.css" />'
                    ,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/jqmodal/jqModal.css">'
                    ,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqModal.js"></script>'
                    , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/iframecontentmws.js"></script>'
                    , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws.js"></script>'
                    , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_draggable.js"></script>'
                    , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_filter.js"></script>'
                    , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_overtwo.js"></script>'
                    , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_scroll.js"></script>'
                    , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_shadow.js"></script>'
                    , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_modal.js"></script>'
                    ,$xajax->printJavascript($root_path.'classes/xajax_0.5'));
$smarty->assign('css_and_js', $css_and_js);

$list_dept = array();
$surgery_department=$department->getAllActiveWithSurgery();
foreach ($surgery_department as $dept) {
    $list_dept[$dept['nr']] = $dept['name_formal'];
}

$list_dept['all'] = 'All Department';


  
$number_of_pages = array('5'=>'5', '10'=>'10', '15'=>'15', '20'=>'20', '25'=>'25', '30'=>'30');
$smarty->assign('number_of_pages', $number_of_pages);
$smarty->assign('page_number', '<input type="text" id="page_number" name="page_number" />');
$smarty->assign('search_field', '<input type="text" id="search_field" name="search_field" />');
$smarty->assign('departments', $list_dept);
$smarty->assign('selected_department', 'all');
$smarty->assign('statuses', array('request'=>'Pending',
                                  'cancelled'=>'Cancelled',
                                  'approved'=>'Approved',
                                  'scheduled'=>'Scheduled',
                                  'pre_op'=>'Pre-operation',
                                  'post'=>'Post-operation',
                                  'dead'=>'Dead',
                                  'all_status'=>'All Status'));
$smarty->assign('search_button', '<input type="submit" id="search_button" value="Search" />');
$smarty->assign('selected_status', 'all_status');
$breakfile=$root_path.'main/op-doku.php'.URL_APPEND;
$smarty->assign('return', '<a href="'.$breakfile.'" id="return_button"></a>');
$smarty->assign('breakfile',$breakfile); //Close button
$smarty->assign('sMainBlockIncludeFile','or/or_main_cases.tpl'); //Assign the select_or_request template to the frameset
$smarty->display('common/mainframe.tpl'); //Display he contents of the frame   


?>
<script>
       
 

$(document).ready(function() {

$('#or_request_table').flexigrid
({
 url: '<?=$root_path?>modules/or/ajax/ajax_or_main_cases.php',
 dataType: 'json',
 colModel : [
             {display: 'Request Date', width:110, name:'request_date', sortable: true, align: 'left'},
             {display: 'Operation Date', width:110, name:'request_date', sortable: true, align: 'left'},
             {display: 'Patient ID', width:75, name:'patient_id', sortable: false, align: 'left'},
             {display: 'Patient Name', width:100, name:'patient_name', sortable: false, align: 'left'},
             {display: 'Department', width:100, name:'department', sortable: false, align: 'left'},
             {display: 'Status', width:55, name:'edit', sortable: false, align: 'left'},
             {display: 'Preview', width:80, name:'edit', sortable: false, align: 'left'}
             ],
sortname: ["request_date"],
domain: ['approve_or'],
sortorder: "desc",
useRp: true,
rp: 10,
resizable: true
}); 

});

function preview_or_case(refno) {
  overlib(
            OLiframeContent('<?=$root_path?>modules/or/or_main/preview_or_case.php?refno='+refno, 700, 400, 'fOrderTray', 0, 'yes'),
            WIDTH, 700, TEXTPADDING,0, BORDER,0, 
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
            CAPTIONPADDING,2,DRAGGABLE, 
            CAPTION,'View OR Case',
            MIDX,0, MIDY,0, 
            STATUS,'View OR Case');
        return false
}             
 
</script>

