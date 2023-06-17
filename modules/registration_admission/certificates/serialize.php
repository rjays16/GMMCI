<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Untitled Document</title>
</head>

<body>
<?php
	echo "_POST['mode'] = '".$_POST['mode']."' <br> \n";
	if($_POST['mode']=='save'){
		$deathCause = array();
		$deathInterval = array();

		for($i=0;$i<9;$i++){
			$cx="cause".$i;
			$ix="interval".$i;

			echo "cx = '".$cx."'; $ $ cx = '".$$cx."'; _POST['cx'] = '".$_POST[$cx]."'; <br> \n";
			echo "ix = '".$ix."'; $ $ ix = '".$$ix."'; _POST['ix'] = '".$_POST[$ix]."'; <br> \n";
			
#			if(!empty($$cx)) $deathCause[$cx]=$$cx;
#			if(!empty($$ix)) $deathInterval[$ix]=$$ix;
			if(!empty($_POST[$cx])) $deathCause[$cx]=$_POST[$cx];
			if(!empty($_POST[$ix])) $deathCause[$ix]=$_POST[$ix];
#			if(!empty($_POST[$ix])) $deathInterval[$ix]=$_POST[$ix];

			$ref_buffer=array();
				// Serialize the data
			$ref_buffer['death_cause']=serialize($deathCause);
#			$ref_buffer['death_interval']=serialize($deathInterval);
		}
		echo "ref_buffer['death_cause'] = '".$ref_buffer['death_cause']."' <br> \n";
#		echo "ref_buffer['death_interval'] = '".$ref_buffer['death_interval']."' <br> \n";

		$tmp_death_cause = unserialize($ref_buffer['death_cause']);
#		$tmp_death_interval = unserialize($ref_buffer['death_interval']);
		
		echo "tmp_death_cause  = '".$tmp_death_cause."' <br> \n";
		echo "tmp_death_cause : <br> \n"; print_r($tmp_death_cause); echo"<br> \n";
#		echo "tmp_death_interval  = '".$tmp_death_interval."' <br> \n";
#		echo "tmp_death_interval : <br> \n"; print_r($tmp_death_interval); echo"<br> \n";
	}
?>
<br>
<form id="death_certificate" name="death_certificate" method="post">
	Cause 1 &nbsp;<input name="cause1" id="cause1" type="text" value="<?= $tmp_death_cause['cause1'] ?>">
	Interval 1 &nbsp;<input name="interval1" id="interval1" type="text" value="<?= $tmp_death_interval['interval1'] ?>">
	<br><br>
	Cause 2 &nbsp;<input name="cause2" id="cause2" type="text" value="<?= $tmp_death_cause['cause2'] ?>">
	Interval 2 &nbsp;<input name="interval2" id="interval2" type="text" value="<?= $tmp_death_interval['interval2'] ?>">
	<br><br>
	Cause 3 &nbsp;<input name="cause3" id="cause3" type="text" value="<?= $tmp_death_cause['cause3'] ?>">
	Interval 3 &nbsp;<input name="interval3" id="interval3" type="text" value="<?= $tmp_death_interval['interval3'] ?>">
	<br><br>
	Other Causes &nbsp;<input name="cause4" id="cause4" type="text" value="<?= $tmp_death_cause['cause4'] ?>">
	<br><br>
	<input type="submit" name="Submit" id="Submit" value="Submit">
	<input name="mode" id="mode" type="hidden" value="save">
</form>
<table width="200" border="1">
   <tr>
      <td rowspan="3">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
   </tr>
   <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
   </tr>
   <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
   </tr>
</table>

</body>
</html>
