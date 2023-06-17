<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_notes_nursing.php');
 
/* Establish db connection */
/**
if(!isset($db)||!$db) include($root_path.'include/inc_db_makelink.php');
if($dblink_ok){

  $sql="SELECT plots FROM seg_clinical_chart";
  if($result=$db->Execute($sql)){
    $rows=$result->RecordCount();
    if($rows==1){
      $plots=$result->FetchRow();
      $plots = $plots['plots'];
    }
  }else{exit;} 
}else{exit;}
 **/



define('IMAGE_HEIGHT', 901);
define('IMAGE_WIDTH', 836);
header('Cache-control: private'); // IE 6 FIX  
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');  
header('Cache-Control: no-store, no-cache, must-revalidate');  
header('Cache-Control: post-check=0, pre-check=0', false);  
header('Pragma: no-cache'); 
header ("Content-type: image/png");

$image = @imagecreatetruecolor(IMAGE_WIDTH, IMAGE_HEIGHT) or die("Cannot Initialize new GD image stream");
$background = imagecolorallocate($image, 255, 255, 255); 
imagefill($image, 0, 0, $background);
$image_border = imagecolorallocate($image, 204, 204, 204);
$grid_color = imagecolorallocate($image, 180, 180, 180);
$grid_color_bold = imagecolorallocate($image, 0, 0, 0);
$temperature_text = imagecolorallocate($image, 0, 0, 0);
$pulse_text = imagecolorallocate($image, 255, 0, 0);
 
/** working area **/
//imagerectangle ($image, 0, 0,  835, 900, $image_border);
/** end **/

/** the grid area **/
imagerectangle ($image, 167, 0, 827, 900, $image_border);
/** end **/

/** y-axis setup **/
setup_y_axis();
/** end **/

/** the grid x-axis**/
$counter_x = 0;
$gap_y = 10;
$current_y = 660 - $gap_y;
$is_initial = true;
$label_digit = 35.5;
$label_digit2 = 50;
$initial_label = true;
while ($current_y >= 94) {

  if ($initial_label) {
   create_label_temperature($label_digit, $current_y + 5);
   create_label_pulse($label_digit2, $current_y + 5);
   $label_digit += 0.5;
   $label_digit2 += 10;
   $initial_label = false; 
  }
  if ($counter_x == 5 || ($counter_x == 4 && $is_initial)) {
    imageline($image, 167, $current_y, 827, $current_y, $grid_color_bold);
    create_label_temperature($label_digit, $current_y-5);
    create_label_pulse($label_digit2, $current_y-5);
    $label_digit += 0.5;
    $label_digit2 += 10;
    $counter_x = 0;
    
    $is_initial = false;
    
  }
  else {
    imageline($image, 167, $current_y, 827, $current_y, $grid_color);
  }
  $current_y = $current_y - $gap_y; 
  $counter_x++;
}
/** end **/

/** the grid y-axis **/
$gap_x = 22;
$x = 0;
$current_x = 167 + $gap_x;
$counter_y = 0;
$is_initial = true;
$time = 4;
$unit = 'M';
$minuser = 13;
$meridiem = 'PM';

while ($current_x <= 835) {
  
  if ($time == 12) {
    $unit = ($unit == 'N') ? 'M' : 'N';
    $time .= $unit;
    $minuser = 19;
  }
  imagestring($image, 2, $current_x-$minuser, 85, $time, $temperature_text);
  
  $time += 4;
  if ($time > 12) {
    $time = 4;
    $minuser = 13;
  }
  
  if ($current_x + $gap_x >= 835) {
    $meridiem = ($meridiem == 'PM') ? 'AM' : 'PM';
    imagestring($image, 2, $current_x-40, 63, $meridiem, $temperature_text);
    cut_middle_bottom($current_x);
    create_range($current_x);
    break;
  }
  if ($counter_y == 3 || ($counter_y == 2 && $is_initial)) {
    $meridiem = ($meridiem == 'PM') ? 'AM' : 'PM';
    $upper_y = ($meridiem == 'PM') ? 0 : 60; 
    $lower_y = ($meridiem == 'PM') ? 900 : 720;
    imageline($image, $current_x, $upper_y, $current_x, $lower_y, $grid_color_bold);    
    $counter_y = 0;
    
    
    imageline($image, $current_x, $lower_y, $current_x, 900, $grid_color);    

    imagestring($image, 2, $current_x-40, 63, $meridiem, $temperature_text);
    $is_initial = false;
    cut_middle_bottom($current_x);
        
    if ($meridiem == 'PM') {
      create_range($current_x);
    }
  }
  else {
    
    imageline($image, $current_x, 80, $current_x, 700, $grid_color);  
  }
 
   $current_x = $current_x + $gap_x;
   $counter_y++;
     
}
/** end **/
imagestringup($image, 3, 60, 500, 'TEMPERATURE (Black)', $temperature_text);
imagefilledellipse($image, 65, 330, 6, 6, $temperature_text);
imagestringup($image, 2, 60, 320, 'Oral', $temperature_text);
imageellipse($image, 65, 270, 6, 6, $temperature_text);
imagestringup($image, 2, 60, 260, 'Rectal', $temperature_text); 
imagestringup($image, 3, 5, 380, 'PULSE (Red)', $pulse_text);
imagefilledellipse($image, 10, 390, 6, 6, $pulse_text);
imagestring($image, 2, 10, 65, 'HOUR', $temperature_text); 
imagestring($image, 2, 10, 45, 'Day P.O. or P.P.', $temperature_text);
imagestring($image, 2, 10, 25, 'Hospital Days', $temperature_text);
imagestring($image, 2, 10, 5, 'Date', $temperature_text);

//setup_header();

/** footer labels **/
imagestring($image, 2, 10, 665, 'Respirations', $temperature_text);
imagestring($image, 2, 10, 685, 'Blood Pressure', $temperature_text);
imagestring($image, 2, 10, 705, 'Weight', $temperature_text);
imagestring($image, 2, 10, 745, 'Intake Oral', $temperature_text);
imagestring($image, 2, 40, 765, 'Parenteral', $temperature_text);
imagestring($image, 2, 40, 785, 'Total', $temperature_text);
imagestring($image, 2, 10, 805, 'Output Urine', $temperature_text);
imagestring($image, 2, 40, 825, 'Drainage', $temperature_text);
imagestring($image, 2, 40, 845, 'Emesis', $temperature_text);
imagestring($image, 2, 40, 865, 'Total', $temperature_text);
imagestring($image, 2, 10, 885, 'Stool', $temperature_text);
/** end **/

/** start plotting **/
/**
if (isset($plots))
start_plot();**/

/** end **/

$encounter_nr = $_GET['encounter_nr'];
$nursing = new NursingNotes();                           


$nursing_data = $nursing->get_all_data($part, $encounter_nr);
$graph_data = $nursing->get_graph_data($encounter_nr, 'h');
update_header($nursing_data);
start_plot($graph_data);
update_first_footer($nursing->get_all_data('ff', $encounter_nr));
update_second_footer($nursing->get_all_data('sf', $encounter_nr));
update_third_footer($nursing->get_all_data('tf', $encounter_nr));

function update_first_footer($data) {
  global $image;
  global $temperature_text;

  $respirations = explode(';', $data[0]);
  $blood_pressure = explode(';', $data[1]);

  $max_length = max(count($respirations), count($blood_pressure));
 
  $factor_y = 4;
  $factor_x = 3;
  $iterator = 0;
  while ($iterator < $max_length) {
    $respirations_temp = explode(':', $respirations[$iterator]);
    $blood_pressure_temp = explode(':', $blood_pressure[$iterator]);

    imagestring($image, 2, $respirations_temp[0]+$factor_x, $respirations_temp[1]+$factor_y, $respirations_temp[2], $temperature_text);
	$bp = explode('/', $blood_pressure_temp[2]);
    imagestring($image, 1, $blood_pressure_temp[0]+$factor_x, $blood_pressure_temp[1], $bp[0], $temperature_text);
    imagestring($image, 1, $blood_pressure_temp[0]+$factor_x, $blood_pressure_temp[1]+10, $bp[1], $temperature_text);	

    $iterator++;
  }
}

function update_second_footer($data) {
  global $image;
  global $temperature_text;

  $weight = explode(';', $data[0]);

  $max_length = count($weight);
 
  $factor_y = 4;
  $factor_x = 3;
  $iterator = 0;
  while ($iterator < $max_length) {
    $weight_temp = explode(':', $weight[$iterator]);

    imagestring($image, 2, $weight_temp[0]+$factor_x, $weight_temp[1]+$factor_y, $weight_temp[2] . ' ' .$weight_temp[3], $temperature_text);


    $iterator++;
  }
}

function update_third_footer($data) {
  global $image;
  global $temperature_text;

  $intake_oral = explode(';', $data[0]);
  $parenteral = explode(';', $data[1]);
  $output_urine = explode(';', $data[2]);
  $drainage = explode(';', $data[3]);
  $emesis = explode(';', $data[4]);
  $stools = explode(';', $data[5]);

  $max_length = max(count($intake_oral), count($parenteral), count($output_urine), count($drainage),
  					count($emesis), count($stools));
 
  $factor_y = 4;
  $factor_x = 3;
  $iterator = 0;
  while ($iterator < $max_length) {
    $intake_oral_temp = explode(':', $intake_oral[$iterator]);
	$parenteral_temp = explode(':', $parenteral[$iterator]);
	$output_urine_temp = explode(':', $output_urine[$iterator]);
	$drainage_temp = explode(':', $drainage[$iterator]);
	$emesis_temp = explode(':', $emesis[$iterator]);
	$stools_temp = explode(':', $stools[$iterator]);

    imagestring($image, 2, $intake_oral_temp[0]+$factor_x, $intake_oral_temp[1]+$factor_y, $intake_oral_temp[2], $temperature_text);
	imagestring($image, 2, $parenteral_temp[0]+$factor_x, $parenteral_temp[1]+$factor_y, $parenteral_temp[2], $temperature_text);
	$total_intake = (int)$intake_oral_temp[2] + (int)$parenteral_temp[2];
//	if (($iterator+1)%3 == 0)
	imagestring($image, 2, $parenteral_temp[0]+$factor_x, $parenteral_temp[1]+$factor_y+20, $total_intake, $temperature_text);
	imagestring($image, 2, $output_urine_temp[0]+$factor_x, $output_urine_temp[1]+$factor_y, $output_urine_temp[2], $temperature_text);
	imagestring($image, 2, $drainage_temp[0]+$factor_x, $drainage_temp[1]+$factor_y, $drainage_temp[2], $temperature_text);
	imagestring($image, 2, $emesis_temp[0]+$factor_x, $emesis_temp[1]+$factor_y, $emesis_temp[2], $temperature_text);
	
    $output_urine_total = (int)$output_urine_temp[2] + (int)$drainage_temp[2] + (int)$emesis_temp[2];
	imagestring($image, 2, $parenteral_temp[0]+$factor_x, $emesis_temp[1]+$factor_y+20, $output_urine_total, $temperature_text);
 	imagestring($image, 2, $stools_temp[0]+$factor_x, $stools_temp[1]+$factor_y, $stools_temp[2], $temperature_text);
	/*
 	if (($iterator+1)%3 == 0 || ($iterator+1) > $max_length) {
	  imagestring($image, 2, 266+$factor_x, $intake_oral_temp[1]+$factor_y, $intake_oral_total, $temperature_text);
	  imagestring($image, 2, 266+$factor_x, $parenteral_temp[1]+$factor_y, $parenteral_total, $temperature_text);
	  
  	  $intake_oral_total = 0;
	  $parenteral_total = 0;
	}*/
	
	  /*
  	elseif ($i > 2 && $i <= 5) 
  	elseif ($i > 5 && &i <= 8)
  	elseif ($i > 8 && $i <= 11)
  	elseif ($i > 11 && $i <= 14)*/
    $iterator++;
	//$intake_oral_total += (int)$intake_oral_temp[2];
	//$parenteral_total += (int)$parenteral_temp[2];
	
  }
  
  
 }


function update_header($data) {
  global $image;
  global $temperature_text;

  $record_date = explode(';', $data[0]);
  $hospital_days = explode(';', $data[1]);
  $day_po_pp = explode(';', $data[2]);
  $max_length = max(count($record_date), count($hospital_days), count($day_po_pp));
  $factor_x = 9;
  $factor_y = 4;
  $adjusting_factor_x = 3;
  $iterator = 0;
  while ($iterator < $max_length) {
    $record_date_temp = explode(':', $record_date[$iterator]);
    $hospital_days_temp = explode(':', $hospital_days[$iterator]);
    $day_po_pp_temp = explode(':', $day_po_pp[$iterator]);
    
    //imagefilledellipse($image, $cell[0]+$factor_x, $cell[1]+$factor_y, 6, 6, $temperature_text);
    imagestring($image, 3, $record_date_temp[0]+$factor_x, $record_date_temp[1]+$factor_y, $record_date_temp[2], $temperature_text);
    imagestring($image, 3, $hospital_days_temp[0]+$factor_x, $hospital_days_temp[1]+$factor_y, $hospital_days_temp[2], $temperature_text);
    imagestring($image, 3, $day_po_pp_temp[0]+$factor_x, $day_po_pp_temp[1]+$factor_y, $day_po_pp_temp[2], $temperature_text);
    $iterator++;
  }
}
function create_label_temperature($digit, $current_y) {
  global $image;
  global $temperature_text;
  
  $position_x = 49;
  if (strlen($digit) > 2)
    $position_x = 35;
    
  imagestring($image, 3, $position_x+93, $current_y-6, $digit, $temperature_text);
  imageellipse($image, 160, $current_y-3, 6, 6, $temperature_text);
  
  $fahrenheit = convert_to_fahrenheit($digit);
  $position_x = 49;
  if (strlen($fahrenheit) == 5)
    $position_x = 38;
  if (strlen($fahrenheit) == 4)
    $position_x = 45;
  if (strlen($fahrenheit) == 3) 
    $position_x = 50;
  imagestring($image, 3, $position_x+42, $current_y-6, $fahrenheit, $temperature_text);
  imageellipse($image, 118, $current_y-3, 6, 6, $temperature_text);
   
}

function create_label_pulse($digit, $current_y) {
  global $image;
  global $pulse_text;
  
  $position_x = 49;
  if (strlen($digit) > 2)
    $position_x = 41;
  imagestring($image, 3, $position_x-10, $current_y-6, $digit, $pulse_text);
}

function cut_middle_bottom($current_x) {
  global $image;
  global $grid_color;
  global $temperature_text;
  $current_x -= 33;
  imageline($image, $current_x, 720, $current_x, 900, $grid_color);
    
}

function create_range($current_x) {
  global $image;
  global $temperature_text;
  $array = array('7-3', '3-11', '11-7');
  
  $current_x -= 130;
  foreach ($array as $key => $value) {
    imagestring($image, 2, $current_x + $key + 5, 725, $value, $temperature_text);
    $current_x += 30; 
  }
}

function convert_to_fahrenheit($digit) {
  return ($digit * (9/5) + 32);
}

function setup_y_axis() {
  global $image;
  global $grid_color;
  global $temperature_text;
  imageline($image, 125, 80, 125, 660, $grid_color);
  imageline($image, 55, 80, 55, 660, $grid_color);
  imageline($image, 0, 0, 0, 900, $grid_color);
  imageline($image, 0, 660, 827, 660, $grid_color);
  imageline($image, 0, 80, 827, 80, $grid_color);
  imageline($image, 0, 60, 827, 60, $grid_color);
  imageline($image, 0, 40, 827, 40, $grid_color);
  imageline($image, 0, 20, 827, 20, $grid_color);
  
  imageline($image, 0, 0, 827, 0, $grid_color);
  imagestring($image, 3, 100, 83, 'F', $temperature_text);
  imagestring($image, 3, 145, 83, 'C', $temperature_text);
  
  /** lower part **/
  $footer_thresh = 680;
  $footer_increment = 20;   
  while ($footer_thresh <= 900) {
    imageline($image, 0, $footer_thresh, 827, $footer_thresh, $grid_color);
    $footer_thresh += $footer_increment;
  }
  /** end **/
}

function setup_header() {
  global $image;
  global $grid_color;
  global $temperature_text;
  $column = 6;
  $current_x = 230;
  $lower_base = 125;
  imageline($image, 65, 50, 65, 110, $grid_color);
}

function start_plot($plots) {
  
  global $image;
  global $temperature_text;
  global $pulse_text;
  //imagesetthickness($image, 2);
  imageantialias($image, true);
  $temperature = explode(';', $plots['temperature']);
  $pulse = explode(';', $plots['pulse']);
  
  $factor_x = 9;
  $factor_y = 4;
  $adjusting_factor_x = 3;
  
  $iterator = 0;
  $max_count = max(count($temperature), count($pulse));
  
  while ($iterator < $max_count) {
      $temperature_temp = explode(':', $temperature[$iterator]);
      $pulse_temp = explode(':', $pulse[$iterator]);
       $factor_x = 9;
      if ($temperature_temp[4]=='s') {
          $factor_x = 5;
      }
        if ($temperature_temp[2] == 'o' || $temperature_temp[2] == 'r') {
          
          if ($temperature_temp[2] == 'o') 
            imagefilledellipse($image, $temperature_temp[0]+$factor_x, $temperature_temp[1]+$factor_y, 6, 6, $temperature_text);
          elseif ($temperature_temp[2] == 'r')  
            imageellipse($image, $temperature_temp[0]+$factor_x, $temperature_temp[1]+$factor_y, 6, 6, $temperature_text);
          if (is_array($last_value2)) {
            imagesmoothline($image, $last_value2['x1'], $last_value2['y1'], $temperature_temp[0]+$factor_x, $temperature_temp[1]+$factor_y, $temperature_text);
          }
          $last_value2 = array('x1'=>$temperature_temp[0]+$factor_x, 'y1'=>$temperature_temp[1]+$factor_y);
        }
          
        
        if ($pulse_temp[2] == 'p') {
          $factor_x = ($pulse_temp[4]=='s') ? 13 : 9;
           
          imagefilledellipse($image, $pulse_temp[0]+$factor_x, $pulse_temp[1]+$factor_y, 6, 6, $pulse_text);
           if (is_array($last_value)) {
            //imageline($image, $last_value['x1'], $last_value['y1'], $pulse_temp[0]+$factor_x, $pulse_temp[1]+$factor_y, $pulse_text);
            imagesmoothline($image, $last_value['x1'], $last_value['y1'], $pulse_temp[0]+$factor_x, $pulse_temp[1]+$factor_y, $pulse_text);
          }
          $last_value = array('x1'=>$pulse_temp[0]+$factor_x, 'y1'=>$pulse_temp[1]+$factor_y);
        }
      $iterator++;
  }
}

function imagesmoothline ( $image , $x1 , $y1 , $x2 , $y2 , $color )
 {
 
  $colors = imagecolorsforindex ( $image , $color );
  if ( $x1 == $x2 )
  {
   imageline ( $image , $x1 , $y1 , $x2 , $y2 , $color ); // Vertical line
  }
  else
  {
   $m = ( $y2 - $y1 ) / ( $x2 - $x1 );
   $b = $y1 - $m * $x1;
   if ( abs ( $m ) <= 1 )
   {
    $x = min ( $x1 , $x2 );
    $endx = max ( $x1 , $x2 );
    while ( $x <= $endx )
    {
     $y = $m * $x + $b;
     $y == floor ( $y ) ? $ya = 1 : $ya = $y - floor ( $y );
     $yb = ceil ( $y ) - $y;
     $tempcolors = imagecolorsforindex ( $image , imagecolorat ( $image , $x , floor ( $y ) ) );
     $tempcolors['red'] = $tempcolors['red'] * $ya + $colors['red'] * $yb;
     $tempcolors['green'] = $tempcolors['green'] * $ya + $colors['green'] * $yb;
     $tempcolors['blue'] = $tempcolors['blue'] * $ya + $colors['blue'] * $yb;
     if ( imagecolorexact ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] ) == -1 ) imagecolorallocate ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] );
     imagesetpixel ( $image , $x , floor ( $y ) , imagecolorexact ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] ) );
     $tempcolors = imagecolorsforindex ( $image , imagecolorat ( $image , $x , ceil ( $y ) ) );
     $tempcolors['red'] = $tempcolors['red'] * $yb + $colors['red'] * $ya;
      $tempcolors['green'] = $tempcolors['green'] * $yb + $colors['green'] * $ya;
     $tempcolors['blue'] = $tempcolors['blue'] * $yb + $colors['blue'] * $ya;
     if ( imagecolorexact ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] ) == -1 ) imagecolorallocate ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] );
     imagesetpixel ( $image , $x , ceil ( $y ) , imagecolorexact ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] ) );
     $x ++;
    }
   }
   else
   {
    $y = min ( $y1 , $y2 );
    $endy = max ( $y1 , $y2 );
    while ( $y <= $endy )
    {
     $x = ( $y - $b ) / $m;
     $x == floor ( $x ) ? $xa = 1 : $xa = $x - floor ( $x );
     $xb = ceil ( $x ) - $x;
     $tempcolors = imagecolorsforindex ( $image , imagecolorat ( $image , floor ( $x ) , $y ) );
     $tempcolors['red'] = $tempcolors['red'] * $xa + $colors['red'] * $xb;
     $tempcolors['green'] = $tempcolors['green'] * $xa + $colors['green'] * $xb;
     $tempcolors['blue'] = $tempcolors['blue'] * $xa + $colors['blue'] * $xb;
     if ( imagecolorexact ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] ) == -1 ) imagecolorallocate ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] );
     imagesetpixel ( $image , floor ( $x ) , $y , imagecolorexact ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] ) );
     $tempcolors = imagecolorsforindex ( $image , imagecolorat ( $image , ceil ( $x ) , $y ) );
     $tempcolors['red'] = $tempcolors['red'] * $xb + $colors['red'] * $xa;
     $tempcolors['green'] = $tempcolors['green'] * $xb + $colors['green'] * $xa;
     $tempcolors['blue'] = $tempcolors['blue'] * $xb + $colors['blue'] * $xa;
     if ( imagecolorexact ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] ) == -1 ) imagecolorallocate ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] );
     imagesetpixel ( $image , ceil ( $x ) , $y , imagecolorexact ( $image , $tempcolors['red'] , $tempcolors['green'] , $tempcolors['blue'] ) );
     $y ++;
    }
   }
  }
 }
imagepng($image);
imagedestroy($image);
?> 
