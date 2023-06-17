<style>
  table tr td {
     color: #000;
     margin: 0px;
     padding: 0px;
   }
   a#add_blood_item {
     background: #F4F1EC url(../../images/laboratory/edit_blood_item.png) no-repeat;
     background-position: 0px 0px;
     width: 80px;
     height: 25px;
     border: none;
     outline: none;
     cursor: pointer;
     display: block;
     margin-top: 3px;
   }
   a#add_blood_item:hover {
     background-position: 0px -25px;
     outline: none;
     cursor: pointer; 
   }
   img.date_time_picker {
    background:  url(../../images/or_main_images/date_time_picker.png);
    width: 105px;
    height: 31px;
    opacity: 0.7;
    cursor: pointer;
    float: left;
  }
   img.date_time_picker:hover {
     opacity: 1;
   }
   div.date_display  {
    display:inline-block;
    border: 1px #7F9DB9 solid;
    height: 14px;
    padding: 2px;
    font: normal 11px Arial;
    width: 175px;
    background: #FFFFFF; 
    color: #000000;
    margin: 0px;
        
  }
  textarea, input[type=text], select {
    width: 180px;
    font: normal 11px Arial;
    margin: 0px; 
  }
   
  .error_field {
    border: 1px #FF0000 solid;
    color: #FF0000;
    
  }  
</style>
<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
   
require_once($root_path.'include/inc_front_chain_lang.php'); 
require_once($root_path.'gui/smarty_template/smarty_care.class.php'); //load the extended smarty template

$smarty = new Smarty_Care('blood_promissory_item');

$javascript_array = array('<script type="text/javascript" src="'.$root_path.'modules/or/js/flexigrid/lib/jquery/jquery.js"></script>'
                          , '<script>var J = jQuery.noConflict();</script>'
                          , '<link rel="stylesheet" type="text/css" media="all" href="'.$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'
                          , '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>');
$smarty->assign('javascript_array', $javascript_array);
  
$smarty->assign('form_start', '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">');
$smarty->assign('form_end', '</form>');



/** Add blood item **/
$smarty->assign('date_borrowed_display', '<div id="date_borrowed_display" class="date_display">'.date('F d, Y').'</div>');
$smarty->assign('date_borrowed', '<input type="hidden" name="date_borrowed" id="date_borrowed" value="'.date('Y-m-d').'" />');
$smarty->assign('date_borrowed_picker', '<img src="'.$root_path.'images/or_main_images/date_time_picker.png" id="date_borrowed_picker" class="date_time_picker" />');
$smarty->assign('date_borrowed_script', setup_calendar('date_borrowed_display', 'date_borrowed', 'date_borrowed_picker'));

$smarty->assign('no_of_units', '<input type="text" name="no_of_units" id="no_of_units" />');
$smarty->assign('serial_number', '<input type="text" name="serial_number" id="serial_number" />');

$smarty->assign('date_replaced_display', '<div id="date_replaced_display" class="date_display">'.date('F d, Y').'</div>');
$smarty->assign('date_replaced', '<input type="hidden" name="date_replaced" id="date_replaced" value="'.date('Y-m-d').'" />');
$smarty->assign('date_replaced_picker', '<img src="'.$root_path.'images/or_main_images/date_time_picker.png" id="date_replaced_picker" class="date_time_picker" />');
$smarty->assign('date_replaced_script', setup_calendar('date_replaced_display', 'date_replaced', 'date_replaced_picker'));

$smarty->assign('no_of_units_replaced', '<input type="text" name="no_of_units_replaced" id="no_of_units_replaced" />');
$smarty->assign('item_status', array('Served' => 'Served', 'Not Served' => 'Not Served', 'Cancelled' => 'Cancelled'));
$smarty->assign('remarks', '<textarea name="remarks" id="remarks"></textarea>');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
}
$smarty->assign('add_blood_item', '<a href="javascript:void(0)" onclick="edit_blood_item('.$id.')" id="add_blood_item"></a>');


/** End **/
            
$smarty->assign('bHideCopyright', true);  
$smarty->assign('bHideTitleBar', true);
$smarty->assign('sMainBlockIncludeFile','laboratory/seg_blood_promissory_item.tpl'); //Assign the or_main template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame

function setup_calendar($display_area, $input_field, $button) {
  global $root_path;
  $calendar_script = 
    '<script type="text/javascript">
       Calendar.setup ({
         displayArea : "'.$display_area.'",
         inputField : "'.$input_field.'", 
         ifFormat : "%Y-%m-%d", 
         daFormat : "%B %e, %Y", 
         showsTime : false, 
         button : "'.$button.'", 
         singleClick : true, 
         step : 2
       });
      </script>';
  return $calendar_script;
}

  
?>

<script>

function edit_blood_item(id) {
    var details = new Object();
    
    details.date_borrowed = $('date_borrowed').value; 
    details.no_of_units =  $('no_of_units').value;
    details.serial_number = $('serial_number').value;
    details.date_replaced = $('date_replaced').value;
    details.no_of_units_replaced = $('no_of_units_replaced').value;
    details.item_status = $('item_status').value;
    details.remarks =  $('remarks').value;
    if (validate(details)) {
      details.iterator = parseInt(id);
      replace_blood_item(details, 'blood_item_list');
    }
}

function edit_blood_item_popup(params) {
  overlib(
    OLiframeContent('<?=$root_path?>modules/bloodBank/edit_seg_blood_promissory_item.php?popUp=0&view_from=1&id='+params, 460, 277, 'promissory_blood_popup', 0, 'no'),
    WIDTH,460, TEXTPADDING,0, BORDER,0, 
    STICKY, SCROLL, CLOSECLICK, MODAL,
    CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
    CAPTIONPADDING,2,DRAGGABLE, 
    CAPTION,'Edit Blood Item',
    MIDX,0, MIDY,0, 
    STATUS,'Edit Blood Item');
  return false 
}


    
function replace_blood_item(details, table) {

var table1 = window.parent.$(table).getElementsByTagName('tbody').item(0);
if (window.parent.$('empty_blood_item_row')) {
  table1.removeChild(window.parent.$('empty_blood_item_row'));   
}    
var row = window.parent.document.createElement("tr");


var array_elements = [{type: 'img', src: '../../images/btn_delitem.gif', assign_function: 'remove_blood_item('+details.iterator+')'},
                      {type: 'img', src: '../../images/edit.gif', assign_function: 'edit_blood_item_popup('+details.iterator+')'},
                      {type: 'td_text', name: details.date_borrowed},
                      {type: 'td_text', name: details.no_of_units},
                      {type: 'td_text', name: details.serial_number},
                      {type: 'td_text', name: details.date_replaced},
                      {type: 'td_text', name: details.no_of_units_replaced},
                      {type: 'td_text', name: details.item_status},
                      {type: 'td_text', name: details.remarks},
                      ];


for (var i=0; i<array_elements.length; i++) {
  var cell = window.parent.document.createElement("td");
  if (array_elements[i].type == 'td_text') {
    cell.appendChild(window.parent.document.createTextNode(array_elements[i].name));
  }
  
  if (array_elements[i].type == 'img') {
    var img = '<img src="' + array_elements[i].src + '" style="cursor:pointer" onclick="' + array_elements[i].assign_function + '" />';
    cell.innerHTML = img;     
  }

  row.appendChild(cell);
}

row.id = 'blood_item'+details.iterator;

var x = window.parent.$('blood_item'+details.iterator);
window.parent.$(table).getElementsByTagName('tbody').item(0).replaceChild(row, x);


var hidden_elements = [{name: 'date_borrowed_hidden[]', value: details.date_borrowed, id: 'date_borrowed'+details.iterator},
                       {name: 'no_of_units_hidden[]', value: details.no_of_units, id: 'no_of_units'+details.iterator},
                       {name: 'serial_number_hidden[]', value: details.serial_number, id: 'serial_number'+details.iterator},
                       {name: 'date_replaced_hidden[]', value: details.date_replaced, id: 'date_replaced'+details.iterator},
                       {name: 'no_of_units_replaced_hidden[]', value: details.no_of_units_replaced, id: 'no_of_units_replaced'+details.iterator},
                       {name: 'item_status_hidden[]', value: details.item_status, id: 'item_status'+details.iterator},
                       {name: 'remarks_hidden[]', value: details.remarks, id: 'remarks'+details.iterator}
                      ];

for (var i=0; i<hidden_elements.length; i++) {
  var hidden_array = document.createElement('input');
  hidden_array.type = 'hidden';
  hidden_array.name = hidden_elements[i].name;
  hidden_array.value = hidden_elements[i].value;
  if (hidden_elements[i].id) {
    hidden_array.id = hidden_elements[i].id;
  }
  var y = window.parent.$(hidden_elements[i].id); 
  window.parent.document.forms[0].replaceChild(hidden_array, y);
}


}

function validate(e) {
  var array_elements = [ {field: J("input[@name='no_of_units']"), 
                         field_value: e.no_of_units, 
                         msg: 'Please enter the number of units',
                         is_number: true
                         },
                         {field: J("input[@name='serial_number']"), 
                         field_value: e.serial_number, 
                         msg: 'Please enter the serial number',
                        
                         },
                         {field: J("input[@name='no_of_units_replaced']"), 
                         field_value: e.no_of_units_replaced, 
                         msg: 'Please specify the number of units replaced',
                         is_number: true
                         }
                         
                         ];
  var errors = new Array();
  for (var i=0; i<array_elements.length; i++) {
    if (array_elements[i].field_value == '' || !array_elements[i].field_value || ((array_elements[i].is_number) && isNaN(array_elements[i].field_value))) {
      alert(array_elements[i].msg);
      errors.push(array_elements[i].field);
      array_elements[i].field.addClass('error_field');
    }
    else {
        
      array_elements[i].field.removeClass('error_field');
    }
  }
  if (errors.length > 0) {
    errors[0].focus();
    return false;
  }
  else {
    return true;
  }
}

<?php
if (isset($_GET['id'])) {
?>
  var id = <?=$_GET['id']?>;
  var date_borrowed_string = window.parent.$('date_borrowed'+id).value;
  var date_borrowed_array = date_borrowed_string.split('-');
  var date_replaced_string = window.parent.$('date_replaced'+id).value;
  var date_replaced_array = date_replaced_string.split('-');
  var item_status_options = $('item_status');
  var item_status = window.parent.$('item_status'+id).value;
  var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  
  
  $('no_of_units').value = window.parent.$('no_of_units'+id).value;
  $('serial_number').value = window.parent.$('serial_number'+id).value;
  $('no_of_units_replaced').value = window.parent.$('no_of_units_replaced'+id).value;
  $('date_borrowed').value = window.parent.$('date_borrowed'+id).value;
  $('date_borrowed_display').innerHTML = months[date_borrowed_array[1]-1] + ' ' + date_borrowed_array[2] + ', ' + date_borrowed_array[0];
  $('date_replaced').value = window.parent.$('date_replaced'+id).value;
  $('date_replaced_display').innerHTML = months[date_replaced_array[1]-1] + ' ' + date_replaced_array[2] + ', ' + date_replaced_array[0];
 
  
  for(var i=0; i<item_status_options.length; i++) {
    if (item_status == item_status_options.options[i].value) {
      item_status_options.options[i].selected = true;
    }
  }
   $('remarks').value = window.parent.$('remarks'+id).value;
   
   
   

<?php
}
?>                                             
</script>
