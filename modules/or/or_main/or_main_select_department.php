<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);  //set the error level reporting
require('./roots.php'); //traverse the root= directory
$local_user='ck_op_pflegelogbuch_user'; //I don't get this, but it has something to do with page authorization access
require($root_path.'include/inc_environment_global.php');   

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template
require_once($root_path.'include/care_api_classes/class_department.php'); 
$department = new Department();
$smarty = new Smarty_Care('select_or_request');
$smarty->assign('sToolbarTitle',"Operation Room :: Select Department"); //Assign a toolbar title
$smarty->assign('sWindowTitle',"Operating Room :: Select Department");
$css_and_js = array('<link rel="stylesheet" href="'.$root_path.'modules/or/css/select_or_request.css" type="text/css" />');
$smarty->assign('css_and_js', $css_and_js); 

$smarty->assign('form_start', '<form method="POST">');
 
$active_departments = $department->getAllActiveWithSurgery();
$department_table = '';
if (count($active_departments)) {
  $is_first = true;
  $background = 'even';
  foreach ($active_departments as $key => $value) {
    $is_checked = ($is_first) ? 'checked="checked"' : '';
    $background = ($background == 'even') ? 'odd' : 'even';
    $department_table .= '<tr id="'.$background.'">
                            <td align="right"><input type="radio" name="department" value="'.$value['nr'].'" '.$is_checked.' /></td>
                            <td>'.$value['name_formal'].'</td>
                         </tr>';
    $is_first = false;
  }
  
}
else {
   $department_table .= '<tr><td colspan="2">No department was created yet.</td></tr>';
}

$active_operating_rooms = $department->getAllActiveWithSurgery();
$department_table = '';
if (count($active_departments)) {
  $is_first = true;
  $background = 'even';
  foreach ($active_departments as $key => $value) {
    $is_checked = ($is_first) ? 'checked="checked"' : '';
    $background = ($background == 'even') ? 'odd' : 'even';
    $department_table .= '<tr id="'.$background.'">
                            <td align="right"><input type="radio" name="department" value="'.$value['nr'].'" '.$is_checked.' /></td>
                            <td>'.$value['name_formal'].'</td>
                         </tr>';
    $is_first = false;
  }
  
}
else {
   $department_table .= '<tr><td colspan="2">No department was created yet.</td></tr>';
}

$smarty->assign('department_table', $department_table);
$smarty->assign('form_end', '</form>');

$smarty->assign('sMainBlockIncludeFile','or/or_main_select_department.tpl');
$smarty->display('common/mainframe.tpl'); //Display he contents of the frame   

?>