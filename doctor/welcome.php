<?php 
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	
	require('roots.php');
	include ("config.php");
	include("include/adodb/adodb.inc.php");
	
	require('../include/inc_environment_global.php');
	
	$db = &ADONewConnection($DBType);
	$db->Connect($DBHost, $DBUser, $DBPassword, $DBName);
?>
<title>SegHIS Doctor Dashboard</title>

<link media="all, handheld" rel="stylesheet" href="default.css" type="text/css">

<body>
<?php 
		
	include_once("classes/doctor.class.php");	
	$doctorObj = new Doctor;
	
	unset($_SESSION['date']);
	#print_r($_SESSION);
	
	if (isset($_POST['username'])){
		$username = $_POST['username'];
		$password = $_POST['password'];
	}else{
		$username = $_SESSION['username'];
		$password = $_SESSION['password'];
	}	
	
	$_SESSION['username'] = $username;
	$_SESSION['password'] = $password;
	
	$rs_doctor = $doctorObj->getDoctorUserInfo($username, md5($password));
	$recordcount = $doctorObj->count;
	
	#$_SESSION['nr'] = $rs_doctor['personell_nr'];
	#echo "<br>".serialize($rs_doctor['personell_nr'])."<br>";
	
	$sid = session_id();
	$lang = "en";

	#print_r($_SESSION);
	include ("include/seg_logo.inc");
?>

<!--<img src="images/seghis_logo.jpg">-->
<?php 
	if ($recordcount){
?>
	<br><br>
	<span class="reg3link"><a href="index.php?logout=1">LOGOUT</a></span>
	<h4><font color="#990000">WELCOME </font><img src="images/lampboard.gif" align="absmiddle" height="30" width="30" alt="welcome"></h4>
<?php 
	
		#echo "<p id=\"screen\"><font size=\"2\" face=\"Arial, Helvetica, sans-serif\"> DR. ".strtoupper($rs_doctor['name'])."</font>&nbsp;<img src=\"images/doctor.gif\" align=\"absmiddle\" height=\"15\" width=\"15\"></p>";
		echo "<p><label for=\"dr-name\"><font color=\"red\" style=\"font-weight:bold\" size=\"2\" face=\"Arial, Helvetica, sans-serif\"> DR. ".strtoupper($rs_doctor['name'])."</font>&nbsp;<img src=\"images/doctor.gif\" align=\"absmiddle\" height=\"15\" width=\"15\"></label></p>";
		$_SESSION['sid'] = $sid;
		#echo $rs_doctor['personell_nr'];
		$_SESSION['dr_nr'] = $rs_doctor['personell_nr'];
		
?>
	
		<p id=\"screen\"><img src="images/plus.gif" width="9" height="9">&nbsp;&nbsp;<span class="deflink"><a href="appointment.php?sid=<?=$sid?>&lang=<?=$lang?>&make=1&edit=0">Appointments</a></span></p>
		<!--<p id=\"screen\"><img src="images/plus.gif" width="9" height="9">&nbsp;&nbsp;<span class="deflink"><a href='patients.php?sid=<?=$sid?>&nr=<?=serialize(stripslashes($rs_doctor['personell_nr']));?>'>Patients</a></span></p>-->
		<p id=\"screen\"><img src="images/plus.gif" width="9" height="9">&nbsp;&nbsp;<span class="deflink"><a href="patients.php?sid=<?=$sid?>&lang=<?=$lang?>">Patients</a></span></p>
		<p id=\"screen\"><img src="images/plus.gif" width="9" height="9">&nbsp;&nbsp;<span class="deflink"><a href="fees.php?sid=<?=$sid?>&lang=<?=$lang?>"><span class="deflink">Fees</a></span></p>
		<p id=\"screen\"><img src="images/plus.gif" width="9" height="9">&nbsp;&nbsp;<span class="deflink"><a href="billing?sid=<?=$sid?>&lang=<?=$lang?>"><span class="deflink">Billing</a></span></p>
		<!--<p id=\"screen\"><img src="images/plus.gif" width="9" height="9">&nbsp;&nbsp;<span class="deflink"><a href="news.php?sid=<?=$sid?>&lang=<?=$lang?>">News</a></span></p>-->
		<p id=\"screen\"><img src="images/plus.gif" width="9" height="9">&nbsp;&nbsp;<span class="deflink"><a href="../modules/news/start_page.php?device=1&sid=<?=$sid?>&lang=<?=$lang?>">News</a></span></p>
		<p id=\"screen\"><img src="images/plus.gif" width="9" height="9">&nbsp;&nbsp;<span class="deflink"><a href="links.php?sid=<?=$sid?>&lang=<?=$lang?>"><span class="deflink">Links</a></span></p>
	
<?php		
	}else{
		#echo "<p id=\"screen\">Invalid userID or password. Please try again.";
		echo "<p><label for=\"error\"><font color=\"red\" style=\"font-weight:bold\">Invalid userID or password. Please try again.</font></label>";
		?>
		&nbsp;&nbsp;<img src="images/lockfolder.gif">
		<?php
		echo "</p>";
		echo "<span class=\"reg3link\"><a href=\"index.php\"><img src=\"images/redpfeil.gif\" border=\"0\" align=\"absmiddle\" alt=\"login\">&nbsp;LOGIN</a></span>";
	}
?>
<input type="hidden" name="username" id="username" value="<?=$_POST['username']?>">
<input type="hidden" name="password" id="password" value="<?=$_POST['password']?>">

</body>
