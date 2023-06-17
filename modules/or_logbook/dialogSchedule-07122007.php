<?php
include("roots.php");
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');

require($root_path.'modules/or_logbook/ajax/op_common.php');

define('LANG_FILE','aufnahme.php');
define('NO_2LEVEL_CHK',1);
//$local_user='aufnahme_user';
require($root_path.'include/inc_front_chain_lang.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Schedule for OP appointment</title> <!---edited spelling from "Scedule", 11-08-2007, fdp--->
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
		
	$xajax->printJavascript($root_path.'classes/xajax');
?>

</head>

<body>
<!-- Scheduling form for OR patient -->
<table width="472" height="340" cellpadding="0" cellspacing="0">
		<tr><td width="259" height="29">
			Please enter the schedule of the patient for operation
			</td>
		</tr>
		<tr>
			<table width="320" height="400" cellpadding="1" cellspacing="0">
				<form method="POST" name="sched_form" id="sched_form" action="thisfile">
					<tr><td>
						<label for="date">Date</label>
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
				  <td width="146">
				  	<label for="time">Time</label>
				    <input type="text" name="time" id="time" onchange="setFormatTime(this,'selAMPM');" onblur="trimString(this)" value="" />
				    <select id="selAMPM" name="selAMPM">
                      <option value="A.M.">A.M.</option>
                      <option value="P.M.">P.M.</option>
                    </select>
				  </td>
						<td width="46">&nbsp;</td>
						<input type="hidden" id="pid" name="pid" value="<?=$pid?>" />	
					<tr>
				</form>	
		</table>
		</tr>
	</tr>
	<tr>
		<div>
			<input type="button" name="button" onclick="saveAppointment()" value="save" />
		</div>
	</tr>
</table>

<!-- XAJAX function for saving data -->
<script language="javascript">
		function saveAppointment(){	
			var sDate, sTime, sPID;
			sDate = document.getElementById('sched_date').value;
			sTime = document.getElementById('time').value;
			sPID = document.getElementById('pid').value;
				
			//alert("Date="+ sDate + "\n time="+ sTime + "\n sPID="+sPID);					
			xajax_saveAppointment(sPID, sDate, sTime);
		}
</script>



</body>
</html>

