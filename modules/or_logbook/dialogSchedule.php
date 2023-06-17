<?php
include("roots.php");
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');


require_once($root_path.'modules/or_logbook/ajax/op_common.php');
require_once($root_path.'include/care_api_classes/class_department.php');
//include_once($root_path.'include/care_api_classes/class_personell.php');

define('LANG_FILE','aufnahme.php');
define('NO_2LEVEL_CHK',1);
//$local_user='aufnahme_user';
require($root_path.'include/inc_front_chain_lang.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Schedule for OP appointment</title>
<script language="javascript">
<?php
require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>


<?php
#	echo '<script type="text/javascript" src="'.$root_path.'modules/or_logbook/js/oploginput.js"></script>'."\n";	

	echo '<script type="text/javascript" language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\n";
	echo '<link rel="stylesheet" type="text/css" media="all" href="'.$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\n";
	
	echo '<script language="javascript" src="'.$root_path.'js/setdatetime.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/checkdate.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/dtpick_care2x.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'modules/or_logbook/js/oploginput.js"></script>'."\n";
	$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
?>
</head>
<?php

$dept_obj = new Department;
#$pers_obj = new Personell;

if(!empty($_GET['dept'])) $rs=$dept_obj->getDeptAllInfo($_GET['dept']); 
//echo "dept=".$rs['name_formal'];

?>
<body>
<!-- Scheduling form for OR patient -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="77" valign="top"><b>Please enter the schedule of the patient for operation</b></td>
  </tr>
  <tr>
    <td>
      <label><b>Date:</b></label>
	  <?php
			$phpfd=$date_format;
			$phpfd=str_replace("dd", "%d", strtolower($phpfd));
			$phpfd=str_replace("mm", "%m", strtolower($phpfd));
			$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
	
			$schedDate= '<input name="sched_date" type="text" size="15" maxlength=10 value=""'. 
						'onFocus="this.select();"  
						id = "sched_date" name="sched_date"
						onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
						onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
						onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
						<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="sched_date_trigger" style="cursor:pointer" >
						<font size=3>['; 			
			ob_start();
				?>
				<script type="text/javascript">
					Calendar.setup ({
							inputField : "sched_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "sched_date_trigger", singleClick : true, step : 1
					});
				</script>
				<?php
					$calendarSetup = ob_get_contents();
					ob_end_clean();
			
					$schedDate .= $calendarSetup;
					/**/
					$dfbuffer="LD_".strtr($date_format,".-/","phs");
					$schedDate = $schedDate.$$dfbuffer.']';
				?>
				<?= $schedDate ?>	  
    </td>
  </tr>
  <tr>
    <td>
		<label><b>Time:</b></label>
		<input type="text" name="time" id="time" size="5" onchange="setFormatTime(this,'selAMPM');" onblur="trimString(this)" />
		<select id="selAMPM" name="selAMPM">
        	<option value="A.M.">A.M.</option>
            <option value="P.M.">P.M.</option>
        </select>
	</td>
  </tr>
  <tr>
    <td><!-- Department -->
		<label><b>Surgeon & Department:</b></label>
		<select id="doc" name="doc">
			<option value="0">-Select a Doctor-</option>
		</select>
		(<?= $rs['name_formal'] ?>)
	</td>
	<input type="hidden" id="to_dept_nr" name="to_dept_nr" value="<?=$_GET['dept']?>" />
  </tr>
  <tr>
    <td>
		<label><b>Purpose:</b></label>
		<textarea id="purpose" name="purpose" oblur= "trimString(this)" rows="3"></textarea> 
	</td>
  </tr>
  <input type="hidden" id="pid" name="pid" value="<?=$_GET['pid'] ?>" />
  <input type="hidden" id="enc_nr" name="enc_nr" value="<?= $_GET['enc_nr'] ?>" />
  <input type="hidden" id="encoder" name="encoder" value="<?= $_GET['encoder'] ?>" />
  <tr>
    <td>
		<input type="button" name="button" onclick="saveAppointment()" value="save" />
	</td>
  </tr>
<script language="javascript">
	<!--presetDoc_s(); -->
	xajax_setDoctors(<?=$_GET['dept']?>);	
</script> 
  
</table>

</body>
</html>

