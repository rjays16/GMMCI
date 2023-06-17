<?php 
	
	require('roots.php');
	require($root_path.'language/en/lang_en_date_time.php');
	require('../include/inc_environment_global.php');
	require_once($root_path.'include/inc_date_format_functions.php');
	
	include("classes/DateDDLGenerator.class.php");
	$dategen = new DateGenerator;	
	$dategen->setToCurrentDay();
	$dategen->setToCurrentTime();
	
	include_once("classes/doctor.class.php");	
	$doctorObj = new Doctor;
	
	#echo "path = ".$root_path."<br>";
?>
<script type="text/javascript">
	function preSet(){
		//alert("preset");
		//document.getElementById('view').style.display = 'none';
		hideMake();
	}
	
	function hideMake(){
		document.getElementById('make-body').style.display = 'none';
	}
	
	function makeview(){
		//alert('trial');
		document.getElementById('make-body').style.display = '';
	}
	
	function resetForm(){
		document.getElementById('client').value='';
		document.getElementById('purpose').value='';
		document.getElementById('place').value='';
	}
	
	function validateForm(d){
		if(d.client.value==''){
			alert("Please enter the client name.");
			d.client.focus();
			return false;
		}else if(d.purpose.value==''){		
			alert("Please enter the purpose of the appointment.");
			d.purpose.focus();
			return false;
		}else if(d.place.value==''){		
			alert("Please enter the place of the appointment.");
			d.place.focus();
			return false;	
		}else{
			return true;
		}	
	}
	
	function updateAppointment(){
		document.getElementById('makerow').innerHTML = 'Edit Appointments';
		document.getElementById('mode').value = 'update';
	}
	
	<?php
		require_once($root_path.'include/inc_checkdate_lang.php'); 
	?>

</script>

<?php
	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/setdatetime.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/checkdate.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/dtpick_care2x.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\n";

?>
<title>SegHIS Doctor Dashboard</title>

<link media="all, handheld" rel="stylesheet" href="default.css" type="text/css">

<body onLoad="preSet();">

<!--<img src="images/seghis_logo.jpg">-->
<?php
	include ("include/seg_logo.inc");
	if (isset($_SESSION['sid'])){
		#print_r($_SESSION);
		include("include/page.inc");	
?>
<h4><font color="blue">APPOINTMENTS</font></h4>
<?php
	$thisfile=basename(__FILE__);
	if(!isset($currDay)||!$currDay) $currDay=date('d');
	if(!isset($currMonth)||!$currMonth) $currMonth=date('m');
	if(!isset($currYear)||!$currYear) $currYear=date('Y');
	if(isset($HTTP_SESSION_VARS['sess_parent_mod'])) $HTTP_SESSION_VARS['sess_parent_mod']='';
	
	/*
	if(!isset($mode)){
		$mode='show';
	}elseif($mode=='appt_cancel'&&!empty($nr)){
		if($appt_obj->cancelAppointment($nr,$reason,$HTTP_SESSION_VARS['sess_user_name'])){
			header("location:$thisfile".URL_REDIRECT_APPEND."&currYear=$currYear&currMonth=$currMonth&currDay=$currDay");
			exit;
		}else{
			echo "$appt_obj->sql<br>$LDDbNoUpdate";
		}	
	}
	*/
	
	$HTTP_SESSION_VARS['sess_parent_mod']='';
	$HTTP_SESSION_VARS['sess_appt_dept_nr']='';
	$HTTP_SESSION_VARS['sess_appt_doc']='';

	# Buffer calendar output
	/*generate the calendar */
	include('include/calendar_jl/class.calendar.php');
	/** CREATE CALENDAR OBJECT **/
	$Calendar = new Calendar;
	/** WRITE CALENDAR **/
	echo "<span class=\"reglink\">";
	$Calendar -> mkCalendar ($currYear, $currMonth, $currDay,$dept_nr,$aux);
	echo "</span>";
	
	$dateappt = $_POST['Year']."-".$_POST['Month']."-".$_POST['Day'];
	$timeappt = $_POST['Hour'].":".$_POST['Minutes'].":".$_POST['Seconds']." ".$_POST['Meridiem'];
	
	if ($dateappt){
		$appdate = strtotime($dateappt);
		$apptdate = date("Y-m-d",$appdate);
	}
	
	if ($timeappt){
		$apptime = strtotime($timeappt);
		$appttime = date("H:i:s",$apptime);
	}	
	
	if ($_GET['currMonth'])
		$month = $_GET['currMonth'];
	else
		$month = date('m');	
		
	if ($_GET['currDay'])
		$day = $_GET['currDay'];
	else
		$day = date('d');		

	if ($_GET['currYear'])
		$year = $_GET['currYear'];
	else
		$year = date('Y');		
	
	$date =$year."-".$month."-".$day;
	$date = date('Y-m-d',strtotime($date));
	
	#echo "get = ".$_GET['rowID'];
	
	$rsAppointment = $doctorObj->getAppointments($date);
	$count = $doctorObj->count;
	
	if ($count){
	
		$rows=array();
		while ($row=$rsAppointment->FetchRow()) {
			$rows[] = $row;
		}
	
		foreach ($rows as $i=>$row) {
			if ($row) {
				$count++;
				$alt = ($count%2)+1;
			
				$src .= '<tr class="wardlistrow'.$alt.'" id="row'.$row['id'].'">
						<td headers="header1"><a href="'.$thisfile.'?rowID='.$row['id'].'" onClick="updateAppointment();"><img src="images/eye_s.gif" width="16" height="16" border="0"></a></td>
						<td headers="header2">'.$row['apptdate'].'</td>
						<td headers="header3">'.$row['appttime'].'</td>
						<td headers="header4">'.strtoupper($row['client']).'</td>
						<td headers="header5">'.strtoupper($row['purpose']).'</td>
						<td headers="header6">'.strtoupper($row['place']).'</td>
					</tr>
				';
			}
		}
	}else{
		$src .= '<tr class="wardlistrow1">
						<td colspan="6">No available appointments at this day.</td>
					</tr>
				';
	}			
	
	$data = array(
			'apptdate'=>$apptdate,
			'appttime'=>$appttime,
			'client'=>$_POST['client'],
			'purpose'=>$_POST['purpose'],
			'place'=>$_POST['place'],   
		 );
	
	if (isset($submit)){	
		switch($mode) {      
   	    case 'save': 
					$doctorObj->useSegAppointment();
					$doctorObj->setDataArray($data);
					$saveok=$doctorObj->insertDataFromInternalArray();
					break;
			 case 'update':
					$doctorObj->useSegAppointment();
					$doctorObj->setDataArray($data);
					$saveok=$doctorObj->updateDataFromInternalArray($apptID);
					break;
 		} #end of switch statement	 
	}
?>

<br/>
  <table class="reglabel" id="view" width="50%">
		<thead>
			<tr>
				<td colspan="6"><font color="red">View Appointments</font></td>
			</tr>	
		</thead>
		<thead>
			<tr>
				<!--<img src="images/eye_s.gif" width="16" height="16"> -->
				<th width="1%" id="header1">&nbsp;</th>
				<th width="10%" id="header2">Date</th>
				<th width="10%" id="header3">Time</th>
				<th width="30%" id="header4">Client</th>
				<th width="20%" id="header5">Purpose</th>
				<th width="30%" id="header6">Place</th>
			</tr>	
		</thead>
		<tbody id="viewbody">
				<?= $src; ?>
		</tbody>
		
	</table>
<br>	
<table class="reglabel">
	<thead>
		<tr>
			<td colspan="2" class="reg2link" id="makerow"><a href="#" onClick="document.getElementById('mode').value='save';makeview();resetForm();">Make Appointment</a></td>
		</tr>	
	</thead>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onSubmit="return validateForm(this);">
	<tbody id="make-body">
		<tr>
			<td>Date&nbsp;:</td>
			<?php
				/*
				$phpfd=$date_format;
				$phpfd=str_replace("dd", "%d", strtolower($phpfd));
				$phpfd=str_replace("mm", "%m", strtolower($phpfd));
				$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
				*/
			?>
			<td>
				<!--
				<input name="apptdate" id="apptdate" type="text" size="10" 
											value="<?=date("m/d/Y",strtotime($_POST['apptdate']))?>" style="font:bold 12px Arial;text-align:right;"
											onFocus="this.select();"  
											onBlur="IsValidDate(this,\'MM/dd/yyyy\'); "
											onChange="IsValidDate(this,\'MM/dd/yyyy\'); "
											onKeyUp="setDate(this,\'MM/dd/yyyy\',\'en\')">
				 <img src="images/show-calendar.gif" width="26" height="22" id="apptdate_trigger" align="absmiddle" style="cursor:pointer">
			
	 			 <script type="text/javascript">
					Calendar.setup ({
						inputField : "apptdate", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "apptdate_trigger", singleClick : true, step : 1
					});
		 		 </script>
				-->
					<?php
						#month / day / year
						$dategen->genMonth();
						$dategen->genDay();
						$dategen->genYear();
					?>
			</td>
			
		</tr>
		<tr>
			<td>Time&nbsp;:</td>
			<td>
				<!--<input type="text" name="appttime" id="appttime" size="10" value="<?= date("h:i:s A",strtotime($_POST['appttime']))?>" style="text-align:right;font:bold 12px Arial">-->
				<?php
						#echo date("h:i:s A");
						$dategen->genHour();
						$dategen->genMinutes();
						$dategen->genSeconds();
						$dategen->genMeridiem();
				?>
			</td>
		</tr>
		<tr>
			<td>Client&nbsp;:</td>
			<td><input type="text" name="client" id="client" size="20"></td>
		</tr>
		<tr>
			<td>Purpose&nbsp;:</td>
			<td><textarea id="purpose" name="purpose" cols="20" rows="2"><?php echo $_POST['purpose']?></textarea></td>
		</tr>
		<tr>
			<td>Place&nbsp;:</td>
			<td><textarea id="place" name="place" cols="20" rows="2"><?php echo $_POST['place']?></textarea></td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="submit" id="submit" name="submit" value="Save">&nbsp;
				<input type="button" id="cancelappt" name="cancelappt" value="Cancel" onClick="document.getElementById('mode').value='';hideMake();resetForm();">
			</td>
		</tr>
	</tbody>	
	<input type="text" name="mode" id="mode">
	<input type="hidden"  name="currYear" value="<?=$currYear?>">
	<input type="hidden"  name="currMonth" value="<?=$currMonth?>">
	<input type="hidden"  name="currDay" value="<?=$currDay?>">

	</form>
</table>

<?php 
	}else{
		include("include/error.php");
	}

?>

</body>
