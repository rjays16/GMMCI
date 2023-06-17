<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
define('LANG_FILE','nursing.php');
define('NO_2LEVEL_CHK',1); 
$local_user='ck_pflege_user';
require($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'modules/nursing/ajax/nursing-station-new-common.php');
$dept_obj=new Department;  
$ward_obj = new Ward;
  
$smarty = new Smarty_Care('clinical_chart');
$css_and_js = array( '<link rel="stylesheet" href="'.$root_path.'css/themes/default/default.css?t=1240682061" type="text/css">'
                    ,'<link rel="stylesheet" type="text/css" media="all" href="'.$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'
                    ,'<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'
                    ,'<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'
                    ,'<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'
                    ,'<script type="text/javascript" src="'.$root_path.'modules/nursing/js/jquery-1.2.3.pack.js"></script>'
                    ,'<script type="text/javascript" src="'.$root_path.'modules/nursing/js/jquery.maphilight.min.js"></script>'
                    ,$xajax->printJavascript($root_path.'classes/xajax-0.2.5')
                    ,'<link rel="stylesheet" href="'.$root_path.'modules/nursing/js/tooltip/style.css" type="text/css">' 
                    ,'<script type="text/javascript" src="'.$root_path.'modules/nursing/js/tooltip/script.js"></script>'
                    ,'<script type="text/javascript" src="'.$root_path.'modules/nursing/js/tooltip/jquery.js"></script>'
                    ,'<script type="text/javascript" src="'.$root_path.'modules/nursing/js/tooltip/jquery.rightClick.js"></script>'
                    ,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/nursing/css/clinical_chart.css">'
                    ,'<link rel="stylesheet" type="text/css" href="'.$root_path.'modules/or/js/jqmodal/jqModal.css">'
                    ,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqModal.js"></script>'
                    ,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/jqDnR.js"></script>'
                    ,'<script type="text/javascript" src="'.$root_path.'modules/or/js/jqmodal/dimensions.js"></script>');
$smarty->assign('css_and_js', $css_and_js);

/** Assign patient info **/
$pid = isset($_GET['pid']) ? $_GET['pid'] : $_POST['pid'];
$encounter_nr = isset($_GET['encounter_nr']) ? $_GET['encounter_nr'] : $_POST['encounter_nr']; 
$seg_person = new Encounter($encounter_nr);
$person_info = $seg_person->getEncounterInfo($encounter_nr);
$person_last_name = ucwords($person_info['name_last']);
$person_first_name = ucwords($person_info['name_first']);
$person_gender = (strnatcasecmp($person_info['sex'], 'm') == 0) ? 'Male' : 'Female';
$person_age = is_int($person_info['age']) ? $person_age . ' years old' : '-Not specified-';
if ((int)$person_info['encounter_type'] == 1){
  $location = "ER";
}
elseif ((int)$person_info['encounter_type'] == 2){  
  if ($person_info['current_dept_nr']) {
    $dept = $dept_obj->getDeptAllInfo($person_info['current_dept_nr']);
    $location = mb_strtoupper(stripslashes($dept['name_formal']));
  }
}
else {
    if ((int)$person_info['current_ward_nr']) {
      $ward = $ward_obj->getWardInfo($person_info['current_ward_nr']);
      $location = mb_strtoupper(stripslashes($ward['name']))." RM# ".$person_info['current_room_nr'];    
    }
}

$smarty->assign('person_last_name', $person_last_name);
$smarty->assign('person_first_name', $person_first_name);
$smarty->assign('person_gender', $person_gender);
$smarty->assign('person_age', $person_age);
$smarty->assign('hospital_number', $pid);
$smarty->assign('ward', $location);
$smarty->assign('clinical_chart', '<img src="'.$root_path.'modules/nursing/graph.php?encounter_nr='.$encounter_nr.'" ismap usemap="#clinical_grid" border="0" class="mapper" id="my_graph" onmouseover="hideall()" />');
$smarty->assign('pointer', '<input type="hidden" name="pointer" id="pointer" value="o" />');

/* For graph **/
$rows = 56;
$columns = 30;
$upper_left_x = 22;
$add_to_y = 10;
$y = 651;
$y_lower = 660;
$start = 35.5;
$pulse_start = 50;
while ($rows > 0) {
  $x = 168;
  $x_lower = 189;     
  $columns = 30;
  while ($columns > 0) {
    $image_area .= '<AREA SHAPE="RECT" COORDS="'.$x.','.$y.','.$x_lower.','.$y_lower.'" title="'.$start.'" onmouseover="highlight('.$x.','.$y.','.$encounter_nr.','.$start.','.$pulse_start.')" onclick="prepare_plotting('.$x.', '.$y.', '.$_GET['encounter_nr'].','.$start.','.$pulse_start.')">';
    $x += $upper_left_x;
    $x_lower += $upper_left_x;
    $columns--;
  }
  $y -= $add_to_y;
  $y_lower -= $add_to_y;
  $rows--;
  $start += 0.1;
  $pulse_start += 2;
}
/** End **/

$rows = 3;
$add_x = 132;
$add_y = 20;
$start_y = 1;
$end_y = 20;
while ($rows > 0) {
  $start_x = 168;
  $end_x = 299;
  $columns = 5;
  while ($columns > 0) {
    $header_area .= '<AREA SHAPE="RECT" COORDS="'.$start_x.','.$start_y.','.$end_x.','.$end_y.'" style="cursor:pointer" onmouseover="highlight2('.$start_x.','.$start_y.','.$encounter_nr.')" onclick="update_header()">';
    $start_x += $add_x;
    $end_x += $add_x;
    $columns--;
  }
  $start_y += $add_y;
  $end_y += $add_y;
  $rows--;
}

/** Footer **/
$rows = 2;
$add_x = 22;
$add_y = 20;
$start_y = 661;
$end_y = 681;
while ($rows > 0) {
  $start_x = 168;
  $end_x = 189;
  $columns = 30;
  while ($columns > 0)  {
    $footer_first .= '<AREA SHAPE="RECT" COORDS="'.$start_x.','.$start_y.','.$end_x.','.$end_y.'" style="cursor:pointer" onmouseover="highlight3('.$start_x.','.$start_y.','.$encounter_nr.',1)">';
    $start_x += $add_x;
    $end_x += $add_x;
    $columns--;
  }
  $start_y += $add_y;
  $end_y += $add_y;
  $rows--;
}
 
 $rows = 1;
 $add_x = 66;
 $add_y = 20;
 $start_y = 701;
 $end_y = 721;
 while ($rows > 0) {
   $start_x = 168;
   $end_x = 233;
   $columns =  10;
   while ($columns > 0) {
     $footer_second .= '<AREA SHAPE="RECT" COORDS="'.$start_x.','.$start_y.','.$end_x.','.$end_y.'" style="cursor:pointer" onmouseover="highlight3('.$start_x.','.$start_y.','.$encounter_nr.',2)">';
     $start_x += $add_x;
     $end_x += $add_x;
     $columns--;
   }
   $start_y += $add_y;
   $end_y += $add_y;
   $rows--;
 }
 
 $rows = 6;
 $add_x = 33;
 $add_y = 20;
 $start_y = 741;
 $end_y = 761;
 while ($rows > 0) {
   $start_x = 168;
   $end_x = 200;
   $columns =  20;
   while ($columns > 0) {
     if (($columns - 1)%4 != 0)
      $footer_second .= '<AREA SHAPE="RECT" COORDS="'.$start_x.','.$start_y.','.$end_x.','.$end_y.'" style="cursor:pointer" onmouseover="highlight3('.$start_x.','.$start_y.','.$encounter_nr.',3)">';
     $start_x += $add_x;
     $end_x += $add_x;
     $columns--; 
   }
   if ($rows == 2 || $rows==5) {
     $start_y += $add_y * 2;
     $end_y += $add_y * 2;
   }
   else {
     $start_y += $add_y;
     $end_y += $add_y;
   }
   
   $rows--;
 }  
/** End **/

$smarty->assign('image_area', $image_area);
$smarty->assign('header_area', $header_area); 
$smarty->assign('footer_first', $footer_first);
$smarty->assign('footer_second', $footer_second);
/** End **/
$smarty->assign('record_date', '<input type="text" name="record_date" id="record_date"/>');
$smarty->assign('rd_icon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="rd_icon" align="absmiddle" style="cursor:pointer">');
$smarty->assign('hospital_days', '<input type="text" name="hospital_days" id="hospital_days" />');
$smarty->assign('day_po_pp', '<input type="text" name="day_po_pp" id="day_po_pp" />');
$smarty->assign('resize', '<img src="'.$root_path.'images/resize.gif" class="jqResize" />');
$smarty->assign('close_popup', '<a href="javascript:void(0)" class="jqmClose close_popup"></a>');
$smarty->assign('add_header', '<input type="button" class="add_header" onclick="submit_update_header()" />');
$smarty->assign('add_first_footer', '<input type="button" class="add_header" onclick="submit_update_footer(1)" />');
$smarty->assign('add_second_footer', '<input type="button" class="add_header" onclick="submit_update_footer(2)" />');
$smarty->assign('add_third_footer', '<input type="button" class="add_header" onclick="submit_update_footer(3)" />');
$smarty->assign('mode', '<input type="hidden" name="mode" value="update" />');
$smarty->assign('x_axis', '<input type="hidden" name="x_axis" />');
$smarty->assign('y_axis', '<input type="hidden" name="y_axis" />');
$smarty->assign('temperature', '<input type="hidden" name="temperature" />');
$smarty->assign('pulse', '<input type="hidden" name="pulse" />');

/** Footer **/
$smarty->assign('respiration', '<input type="text" name="respiration" id="respiration" />');
$smarty->assign('blood_pressure1', '<input type="text" name="blood_pressure1" id="blood_pressure1" style="width:70px"/>');
$smarty->assign('blood_pressure2', '<input type="text" name="blood_pressure2" id="blood_pressure2" style="width:70px" />');
$smarty->assign('weight', '<input type="text" name="weight" id="weight" />');
$smarty->assign('weight_unit', array('kgs'=>'Kilograms', 'lbs'=>'Pounds'));
$smarty->assign('intake_oral', '<input type="text" name="intake_oral" id="intake_oral" />');
$smarty->assign('parenteral', '<input type="text" name="parenteral" id="parenteral" />');
$smarty->assign('intake_oral_total', '<input type="text" name="intake_oral_total" id="intake_oral_total" />');
$smarty->assign('output_urine', '<input type="text" name="output_urine" id="output_urine" />');
$smarty->assign('drainage', '<input type="text" name="drainage" id="drainage" />');
$smarty->assign('emesis', '<input type="text" name="emesis" id="emesis" />');
$smarty->assign('output_urine_total', '<input type="text" name="output_urine_total" id="output_urine_total" />');
$smarty->assign('stool', '<input type="text" name="stool" id="stool" />');
/** End **/
$smarty->display('nursing/clinical_chart.tpl'); //Display the contents of the frame
?>

<script>


  function refresh_graph(part) {
    var a = document.getElementById('my_graph');
    var c_currentTime = new Date();  
    var c_miliseconds = c_currentTime.getTime();  
    a.src = '<?=$root_path?>modules/nursing/graph.php?encounter_nr=<?=$encounter_nr?>&part='+part+'&t='+c_miliseconds;
   }
   
   function highlight(x, y, encounter_nr, value, pulse) {
     var factor_x = 7;
     var factor_y = 170;
     
     
     
      $('#ss').css({'left' : x + factor_x, 'top' : y + factor_y});
      $('#ss').css({'display': 'block'}); 
      $('#dd').css({'display': 'none'});
      $('#ee').css({'display': 'none'});
      $('#ff').css({'display': 'none'});
      $('#gg').css({'display': 'none'});
      

     
     
     var b = document.getElementById('pointer').value;
     var data;
     if (b=='p')
       data = pulse;
     else
       data = value;
    
     assign_temp_pulse(value, pulse);       
    
     $('#ss').attr({onclick:"prepare_plotting("+x+", "+y+", "+encounter_nr+","+data+")", 
                    onmouseout: "hide_tooltip()",
                    onmouseover: "show_tooltip("+data+")"});
   }
   
   function assign_temp_pulse(value, pulse) {
       
      $("input[@name='temperature']").val(value);
      $("input[@name='pulse']").val(pulse);
   }
   
   function highlight2(x, y, encounter_nr) {
     var factor_x = 7;
     var factor_y = 170;
     var a = document.getElementById('dd');
     a.style.display = 'block'; 
     a.style.left = x + factor_x;
     a.style.top = y + factor_y;

     a.setAttribute("onclick", "update_header("+x+","+y+","+encounter_nr+")");
   }
   
   function highlight3(x, y, encounter_nr, options) {
     var factor_x = 7;
     var factor_y = 170;
     switch (options) {
       case 1:
         $('#ee').css({'left' : x + factor_x, 'top' : y + factor_y});
         $('#ee').css({'display': 'block'});
         $('#ss').css({'display': 'none'});
         $('#dd').css({'display': 'none'});
         $('#gg').css({'display': 'none'});
         $('#ff').css({'display': 'none'});
         $('#ee').attr({onclick:"update_footer("+x+","+y+","+encounter_nr+","+options+")", 
                        onmouseout: "hidehighlight3(1)"});
       break;
       case 2:
         $('#ff').css({'left' : x + factor_x, 'top' : y + factor_y});
         $('#ff').css({'display': 'block'}); 
         $('#ee').css({'display': 'none'});
         $('#gg').css({'display': 'none'});
         $('#ss').css({'display': 'none'});
         $('#dd').css({'display': 'none'});
         $('#ff').attr({onclick:"update_footer("+x+","+y+","+encounter_nr+","+options+")", 
                        onmouseout: "hidehighlight3(2)"});
       break;
       case 3:
         $('#gg').css({'left' : x + factor_x, 'top' : y + factor_y});
         $('#gg').css({'display': 'block'});
         $('#ee').css({'display': 'none'});
         $('#ff').css({'display': 'none'});
         $('#ss').css({'display': 'none'});
         $('#dd').css({'display': 'none'});
         $('#gg').attr({onclick:"update_footer("+x+","+y+","+encounter_nr+","+options+")", 
                        onmouseout: "hidehighlight3(3)"});
       break;
     } 
   
   }
   function hidehighlight3(options) {
     switch (options) {
       case 1:
         $('#ee').css({'display': 'none'});
       break;
       
       case 2:
         $('#ff').css({'display': 'none'});
       break;
       
       case 3:
         $('#gg').css({'display': 'none'});
       break;
     }
     
   }
   function hidehighlight2() {
     var a = document.getElementById('dd'); 
     a.style.display = 'none';
   }
   
   function show_tooltip(temp) {
     var a = document.getElementById('pointer').value;
     var img = '<img src="<?=$root_path?>modules/nursing/js/tooltip/images/dot_'+a+'.png" width="7" height="7" id="picker" method="o" style="margin-right: 5px"/>';
     tooltip.show(img + temp);
   }
   
   function hide_tooltip() {
     tooltip.hide();
     $('#ss').css({'display': 'none'});
   }
   
   function update_header(x, y, encounter_nr) {
     xajax_update_header(x, y, encounter_nr);  
  }                       
  
  function update_footer(x, y, encounter_nr, options) {
    
    xajax_update_footer(x, y, encounter_nr, options);
  }
   
   $("#ss").rightClick( function(e) {

     var x = document.getElementById('pointer').value;
     var a = document.getElementById('picker');
     if (x=='o') {
       
       document.getElementById('pointer').value = 'r';
       show_tooltip($("input[@name='temperature']").val());
     }
     if (x=='r') {
       
       document.getElementById('pointer').value = 'p';
       show_tooltip($("input[@name='pulse']").val());
     }
     if (x=='p') {
       document.getElementById('pointer').value = 'o';
       show_tooltip($("input[@name='temperature']").val());
     }
     a.src = '<?=$root_path?>/modules/nursing/js/tooltip/images/dot_'+document.getElementById('pointer').value+'.png';
   });
     $('#header_popup').jqm({
        overlay: 80,
        onShow: function(h) {
          h.w.fadeIn(1000, function(){h.o.show();}); 
        },
        onHide: function(h){
          h.w.fadeOut(1000, function(){h.o.remove();});
     }});
     
     $('#footer1_popup').jqm({
        overlay: 80,
        onShow: function(h) {
          h.w.fadeIn(1000, function(){h.o.show();}); 
        },
        onHide: function(h){
          h.w.fadeOut(1000, function(){h.o.remove();});
     }});
     
     $('#footer2_popup').jqm({
        overlay: 80,
        onShow: function(h) {
          h.w.fadeIn(1000, function(){h.o.show();}); 
        },
        onHide: function(h){
          h.w.fadeOut(1000, function(){h.o.remove();});
     }});
     
     $('#footer3_popup').jqm({
        overlay: 80,
        onShow: function(h) {
          h.w.fadeIn(1000, function(){h.o.show();}); 
        },
        onHide: function(h){
          h.w.fadeOut(1000, function(){h.o.remove();});
     }});

     $().ready(function() {
       $('#header_popup')
         .jqDrag('.jqDrag')
         .jqResize('.jqResize');
       
       $('#footer1_popup')
         .jqDrag('.jqDrag')
         .jqResize('.jqResize');
       
       $('#footer2_popup')
         .jqDrag('.jqDrag')
         .jqResize('.jqResize');
       
       $('#footer3_popup')
         .jqDrag('.jqDrag')
         .jqResize('.jqResize');

     });
   function show_popup() {
      $('#header_popup').jqmShow();
   }
       
   function show_footer(options) {
     switch (options) {
       case '1':
         $('#footer1_popup').jqmShow();
       break;
       case '2':
         $('#footer2_popup').jqmShow();
       break;
	   case '3':
     
      $('#footer3_popup').jqmShow();
        // $('#footer3_popup').jqmShow();
       break;
     }
   }
   
    Calendar.setup ({
     inputField : "record_date", 
      ifFormat : "%m/%d/%Y", 
      daFormat : "%B %e, %Y", 
      showsTime : false, 
      button : "rd_icon", 
      singleClick : true, 
      step : 1
   });
   
   function assign_header(header) {
   
       if (typeof(header[0])!='undefined' && header[0]!='E') {
         $("input[@name='record_date']").val(header[0]);
       }
       else {
         $("input[@name='record_date']").val('');
       }
       if (typeof(header[1])!='undefined') {
         $("input[@name='hospital_days']").val(header[1]);
       }
       else {
         $("input[@name='hospital_days']").val('');
       }
       if (typeof(header[2])!='undefined') {
         $("input[@name='day_po_pp']").val(header[2]);
       }
       else {
         $("input[@name='day_po_pp']").val('');
       }
       
       show_popup(); 
   }
   
   function assign_footer(footer, options) {

     switch(options) {
       case '1':
         if (typeof(footer[0])!='undefined' && footer[0]!='E') {
           $("input[@name='respiration']").val(footer[0]);
         }
         else {
           $("input[@name='respiration']").val('');
           
         }
         if (typeof(footer[1])!='undefined') {
           var blood_pressure = new Array();
           blood_pressure = footer[1].split('/');

           $("input[@name='blood_pressure1']").val(blood_pressure[0]);
           $("input[@name='blood_pressure2']").val(blood_pressure[1]);
         }
         else {
           $("input[@name='blood_pressure1']").val('');
           $("input[@name='blood_pressure2']").val('');
         }
       break;
       case '2':
	     if (typeof(footer[0])!='undefined' && footer[0]!='E') {
           $("input[@name='weight']").val(footer[0]);
         }
         else {
           $("input[@name='weight']").val(''); 
         }
		 if (typeof(footer[1])!='undefined' && footer[1]!='E') {
           $("select[@name='weight_unit']").val(footer[1]);
         }
         else {
           $("aelect[@name='weight_unit']").val(''); 
         }
	   break;
	   case '3':
	     if (typeof(footer[0])!='undefined' && footer[0]!='E') {
           $("input[@name='intake_oral']").val(footer[0]);
         }
         else {
           $("input[@name='intake_oral']").val('');  
         }
         if (typeof(footer[1])!='undefined' && footer[1]!='E') {
           $("input[@name='parenteral']").val(footer[1]);
         }
         else {
           $("input[@name='parenteral']").val('');  
         }
		 if (typeof(footer[2])!='undefined' && footer[2]!='E') {
           $("input[@name='output_urine']").val(footer[2]);
         }
         else {
           $("input[@name='output_urine']").val('');  
         }
		 if (typeof(footer[3])!='undefined' && footer[3]!='E') {
           $("input[@name='drainage']").val(footer[3]);
         }
         else {
           $("input[@name='drainage']").val('');  
         }
		 if (typeof(footer[4])!='undefined' && footer[4]!='E') {
           $("input[@name='emesis']").val(footer[4]);
         }
         else {
           $("input[@name='emesis']").val('');  
         }
		 if (typeof(footer[5])!='undefined' && footer[5]!='E') {
           $("input[@name='stool']").val(footer[5]);
         }
         else {
           $("input[@name='stool']").val('');  
         }
	   break;
     }

     show_footer(options);
     
   }
   
   function submit_update_header() {
       var record_date = $("input[@name='record_date']").val();
       var hospital_days = $("input[@name='hospital_days']").val();
       var day_po_pp = $("input[@name='day_po_pp']").val();
       var encounter_nr = <?=$encounter_nr?>;
       var mode = $("input[@name='mode']").val();
       var x = $("input[@name='x_axis']").val();
       var y = $("input[@name='y_axis']").val(); 
  
       xajax_submit_update_header(x, y, record_date, hospital_days, day_po_pp, encounter_nr, mode);
   } 
   
   function submit_update_footer(params) {
     var encounter_nr = <?=$encounter_nr?>;
     var mode = $("input[@name='mode']").val();
     var x = $("input[@name='x_axis']").val();
     var y = $("input[@name='y_axis']").val();
     switch (params) {
       case 1:
         var respiration = $("input[@name='respiration']").val();
         var blood_pressure = $("input[@name='blood_pressure1']").val()+'/'+$("input[@name='blood_pressure2']").val();
         xajax_submit_update_first_footer(x, y, respiration, blood_pressure, encounter_nr, mode);
       break;
	   case 2:
	     var weight = $("input[@name='weight']").val() + ':' + $("select[@name='weight_unit']").val();
		 xajax_submit_update_second_footer(x, y, weight, encounter_nr, mode);
	   break;
	   case 3:
	     var intake_oral = $("input[@name='intake_oral']").val();
		 var parenteral = $("input[@name='parenteral']").val();
		 var output_urine = $("input[@name='output_urine']").val();
		 var drainage = $("input[@name='drainage']").val();
		 var emesis = $("input[@name='emesis']").val();
		 var stool = $("input[@name='stool']").val();

		 xajax_submit_update_third_footer(x, y, intake_oral, parenteral, output_urine, drainage, emesis, stool, encounter_nr, mode);
	   break;
     } 
   }
   
   function change_mode(mode) {
     //alert(mode);
     $("input[@name='mode']").val(mode);
   }
   
   function assign_to_axis(x, y) {
       $("input[@name='x_axis']").val(x); 
       $("input[@name='y_axis']").val(y); 
   }
   
   function prepare_plotting(x, y, encounter_nr, value) {
       var plot_what = $("input[@name='pointer']").val();
       xajax_plot_points(x, y, encounter_nr, value, plot_what);
   }
   
   function hideall() {
     $('#ss').css({'display': 'none'});
     $('#dd').css({'display': 'none'});
     $('#ee').css({'display': 'none'});
     $('#ff').css({'display': 'none'});
     $('#gg').css({'display': 'none'});
   } 

    
   
   
</script>

