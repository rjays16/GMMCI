<style>
 
   a#add_blood_item {
     background: #F4F1EC url(../../images/laboratory/add_blood_item.png) no-repeat;
     background-position: 0px 0px;
     width: 80px;
     height: 25px;
     border: none;
     outline: none;
     cursor: pointer;
     display: block;
     margin-top: 3px;
   }
   input#submit_promissory_note {
     background: url(../../images/laboratory/submit_promissory_note.png) no-repeat;
     background-position: 0px 0px;
     width: 75px;
     height: 25px;
     border: none;
     outline: none;
     cursor: pointer;
     display: block;
     margin: 3px 3px 0px 0px;
     float: left;
   }
   a#cancel_promissory_note {
     background: url(../../images/laboratory/cancel_promissory_note.png) no-repeat;
     background-position: 0px 0px;
     width: 75px;
     height: 25px;
     border: none;
     outline: none;
     cursor: pointer;
     display: block;
     margin-top:3px;
     float: left;
   }
   a#view_pdf {
     background: url(../../images/laboratory/view_promissory_note.png) no-repeat;
     background-position: 0px 0px;
     width: 150px;
     height: 25px;
     border: none;
     outline: none;
     cursor: pointer;
     display: block;
     margin:3px 0px 0px 30px;
     float: left;
   }
   a#add_blood_item:hover, input#submit_promissory_note:hover, a#cancel_promissory_note:hover, a#view_pdf:hover {
     background-position: 0px -25px;
     outline: none;
     cursor: pointer; 
   }
   table tr td {
     color: #000;
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
        
  }
  input[type=text] {
    width: 180px;
    font: normal 11px Arial; 
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
require_once($root_path.'include/care_api_classes/class_blood_bank.php');
require_once($root_path."modules/bloodBank/ajax/blood-request-list.common.php"); 

$smarty = new Smarty_Care('blood_promissory_note');
$javascript_array = array('<link rel="stylesheet" type="text/css" media="all" href="'.$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'
                          , '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/iframecontentmws.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_draggable.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_filter.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_overtwo.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_scroll.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_shadow.js"></script>'
                          , '<script type="text/javascript" src="'.$root_path.'js/overlibmws/overlibmws_modal.js"></script>'
                          , $xajax->printJavascript($root_path.'classes/xajax-0.2.5'));
$smarty->assign('javascript_array', $javascript_array);
$smarty->assign('form_start', '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">');
$smarty->assign('form_end', '</form>');

$refno = isset($_GET['refno']) ? $_GET['refno'] : $_POST['refno'];
$mode = isset($_GET['action']) ? $_GET['action'] : $_POST['action'];

$blood_bank = new SegBloodBank();
if ($details = $blood_bank->get_blood_bank_details_by_refno($refno)) {
  $smarty->assign('legal_refno', true);
  $smarty->assign('patient_info', $details);
}
else {
  $smarty->assign('legal_refno', false);
}




$smarty->assign('add_blood_item', '<a href="javascript:void(0)" id="add_blood_item" onclick="add_blood_item_popup()"></a>');
$smarty->assign('iterator', '<input type="hidden" name="iterator" id="iterator" value="0" />');

$smarty->assign('refno', '<input type="hidden" name="refno" id="refno" value="'.$refno.'" />');
$smarty->assign('submitted', '<input type="hidden" name="submitted" value="TRUE" />');
$smarty->assign('submit_promissory_note', '<input type="submit" value="" id="submit_promissory_note" />');
$smarty->assign('cancel_promissory_note', '<a href="javascript:void(0)" id="cancel_promissory_note"></a>');

$smarty->assign('mode', '<input type="hidden" name="action" value="'.$mode.'" />');
$smarty->assign('bHideCopyright', true);  
$smarty->assign('bHideTitleBar', true);

if (isset($_POST['submitted'])) {
  
  $item_count = count($_POST['date_borrowed_hidden']);
  if ($item_count > 0) {
    $blood_data = array('refno' => $_POST['refno'],
                        'borrowers_name' => $_POST['borrowers_name'],
                        'date_filed' => $_POST['date_filed'],
                        'date_borrowed' => $_POST['date_borrowed_hidden'],
                        'no_of_units' => $_POST['no_of_units_hidden'],
                        'serial_number' => $_POST['serial_number_hidden'],
                        'date_replaced' => $_POST['date_replaced_hidden'],
                        'no_of_units_replaced' => $_POST['no_of_units_replaced_hidden'],
                        'item_status' => $_POST['item_status_hidden'],
                        'remarks' => $_POST['remarks_hidden']
                      );
    if ($mode == 'new') {
      if ($blood_bank->save_promissory_note($blood_data)) $mode = 'edit';
    }
    elseif ($mode == 'edit') {
        if($blood_bank->update_promissory_note($blood_data)) $mode = 'edit';
    }
  }
}

$date_filed = isset($_POST['date_filed']) ? date('Y-m-d H:i', strtotime($_POST['date_filed'])) : date('Y-m-d H:i');
$date_filed_display = isset($_POST['date_filed']) ? date('F d, Y h:ia', strtotime($_POST['date_filed'])) : date('F d, Y h:ia');
if ($mode == 'edit') {
    if($data = $blood_bank->get_promissory_note($refno)) {
      $borrowers_name =  $data['borrowers_name'];
      $date_filed = date('Y-m-d H:i', strtotime($data['date_filed']));
      $date_filed_display =  date('F d, Y h:ia', strtotime($data['date_filed']));
      $smarty->assign('view_pdf', '<a href="javascript:void(0)" id="view_pdf" onclick="view_pdf(\''.$data['lab_serv_refno'].'\')"></a>');
    }
}

$smarty->assign('borrowers_name', '<input type="text" name="borrowers_name" id="borrowers_name" value="'.$borrowers_name.'" />');
$smarty->assign('date_filed_display', '<div id="date_filed_display" class="date_display">'.$date_filed_display.'</div>');
$smarty->assign('date_filed', '<input type="hidden" name="date_filed" id="date_filed" value="'.$date_filed.'" />');
$smarty->assign('date_filed_picker', '<img src="'.$root_path.'images/or_main_images/date_time_picker.png" id="date_filed_picker" class="date_time_picker" />');
$smarty->assign('date_filed_script', setup_calendar('date_filed_display', 'date_filed', 'date_filed_picker'));

$smarty->assign('sMainBlockIncludeFile','laboratory/seg_blood_promissory_note.tpl'); //Assign the or_main template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame

function setup_calendar($display_area, $input_field, $button) {
  global $root_path;
  $calendar_script = 
    '<script type="text/javascript">
       Calendar.setup ({
         displayArea : "'.$display_area.'",
         inputField : "'.$input_field.'", 
         ifFormat : "%Y-%m-%d %H:%M", 
         daFormat : "%B %e, %Y %I:%M%P", 
         showsTime : true, 
         button : "'.$button.'", 
         singleClick : true, 
         step : 2
       });
      </script>';
  return $calendar_script;
}

  
?>

<script>

document.body.onLoad = setTimeout(function() {document.getElementById('borrowers_name').focus()}, 100);

function view_pdf(refno) {
  window.open("pdf_seg_blood_promissory_note.php?refno="+refno,
              "PromissoryNote","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
} 

function add_blood_item_popup() {
  overlib(
    OLiframeContent('<?=$root_path?>modules/bloodBank/seg_blood_promissory_item.php?popUp=0&view_from=1', 460, 277, 'fOrderTray', 0, 'no'),
    WIDTH,460, TEXTPADDING,0, BORDER,0, 
    STICKY, SCROLL, CLOSECLICK, MODAL,
    CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
    CAPTIONPADDING,2,DRAGGABLE, 
    CAPTION,'Add Blood Item',
    MIDX,0, MIDY,0, 
    STATUS,'Add Blood Item');
  return false  
}

function edit_blood_item_popup(params) {
  overlib(
    OLiframeContent('<?=$root_path?>modules/bloodBank/edit_seg_blood_promissory_item.php?popUp=0&view_from=1&id='+params, 460, 277, 'promissory_blood_popup2', 0, 'no'),
    WIDTH,460, TEXTPADDING,0, BORDER,0, 
    STICKY, SCROLL, CLOSECLICK, MODAL,
    CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
    CAPTIONPADDING,2,DRAGGABLE, 
    CAPTION,'Edit Blood Item',
    MIDX,0, MIDY,0, 
    STATUS,'Edit Blood Item');
  return false 
} 

function populate_items(details, table) {

var table1 = $(table).getElementsByTagName('tbody').item(0);
if ($('empty_blood_item_row')) {
  table1.removeChild($('empty_blood_item_row'));   
}    
var row = document.createElement("tr");


var array_elements = [{type: 'img', src: '../../images/btn_delitem.gif', assign_function: function() {remove_blood_item(details.iterator)}},
                      {type: 'img', src: '../../images/edit.gif', assign_function: function() {edit_blood_item_popup(details.iterator)}},
                      {type: 'td_text', name: details.date_borrowed},
                      {type: 'td_text', name: details.no_of_units},
                      {type: 'td_text', name: details.serial_number},
                      {type: 'td_text', name: details.date_replaced},
                      {type: 'td_text', name: details.no_of_units_replaced},
                      {type: 'td_text', name: details.item_status},
                      {type: 'td_text', name: details.remarks},
                      ];


for (var i=0; i<array_elements.length; i++) {
  var cell = document.createElement("td");
  if (array_elements[i].type == 'td_text') {
    cell.appendChild(document.createTextNode(array_elements[i].name));
  }
  if (array_elements[i].type == 'img') {
    img = document.createElement("img");
    cell.appendChild(img);
    img.src = array_elements[i].src;
    img.style.cursor = "pointer";
    img.addEventListener("click", array_elements[i].assign_function, false);
  }
                                               
  
  row.appendChild(cell);
}
row.id = 'blood_item'+details.iterator;


$(table).getElementsByTagName('tbody').item(0).appendChild(row);


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
  document.forms[0].appendChild(hidden_array);
}
set_iterator(details.iterator);


}


function remove_blood_item(id) {
  
  var table1 = $('blood_item_list').getElementsByTagName('tbody').item(0);
  table1.removeChild($('blood_item'+id));
  document.forms[0].removeChild($('date_borrowed'+id));
  document.forms[0].removeChild($('no_of_units'+id));
  document.forms[0].removeChild($('serial_number'+id));
  document.forms[0].removeChild($('date_replaced'+id));
  document.forms[0].removeChild($('no_of_units_replaced'+id));
  document.forms[0].removeChild($('item_status'+id));
  document.forms[0].removeChild($('remarks'+id)); 
}

 

function set_iterator(iterator) {
  $('iterator').value = iterator;
}



<?php
  if ($mode=='edit') {
?>
xajax_populate_blood_bank_items(<?=$refno?>); 
<?php } ?>
</script>
