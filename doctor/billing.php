<?php
	require('roots.php');
	require('../include/inc_environment_global.php');
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

<h4><font color="blue">BILLING</font></h4>
<?php 
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