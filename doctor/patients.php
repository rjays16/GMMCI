<?php 
	require('roots.php');
	require('../include/inc_environment_global.php');
	include ("config.php");
	include("include/adodb/adodb.inc.php");
	
	$db = &ADONewConnection($DBType);
	$db->Connect($DBHost, $DBUser, $DBPassword, $DBName);
?>
<title>SegHIS Doctor Dashboard</title>

<link media="all, handheld" rel="stylesheet" href="default.css" type="text/css">

<body>

<!--<img src="images/seghis_logo.jpg">-->
<?php
	include ("include/seg_logo.inc");
	if (isset($_SESSION['sid'])){
		include("include/page.inc");
?>

<h4><font color="blue">PATIENTS</font></h4>
<?php
	include_once("classes/doctor.class.php");	
	$doctor = new Doctor;
	#print_r($_SESSION);
	#$dr_nr = $_GET['nr'];
	#$doctor_nr = unserialize(stripcslashes($dr_nr));
	$rs_patient = $doctor->getPatients($_SESSION['dr_nr']);
	#echo $doctor->sql;
	$i = 1;
	while($result=$rs_patient->FetchRow()) {
		$rs_diagnosis = $doctor->getPatientDiagnosis($result['encounter_nr']);
		if ($result['name_middle'])
			$mname = strtoupper(substr($result['name_middle'],0,1)).".";
		else
			$mname = "";	
		#echo "<span><strong><font color=green>".$i.".) ".$result['name_first']." ".$result['name_2 ']." ".$result['name_3 ']." ".$result['name_middle']." ".$result['name_last']; #." - ".$result['referrer_diagnosis']."</font></span></strong>";
		echo "<span><strong><font color=green>".$i.".) ".$result['name_first']." ".$result['name_2 ']." ".$result['name_3 ']." ".$mname." ".$result['name_last'];
		echo "<br>";
		
		while($result_diagnosis=$rs_diagnosis->FetchRow()) {
			echo "<span><strong><font color=red>&nbsp;&nbsp;&nbsp<img src='images/plus.gif'>&nbsp;&nbsp;".$result_diagnosis['description']."</font></strong></span>";
			echo "<br>";
		}
		
		$i++;	
	}
 }else{
		echo "<p id=\"screen\">Session time out.";
		?>
		&nbsp;&nbsp;<img src="images/lockfolder.gif">
		<?php
		echo "</p>";
		echo "<span class=\"reg3link\"><a href=\"index.php\">LOGIN</a></span>";
 }
?>

</body>
