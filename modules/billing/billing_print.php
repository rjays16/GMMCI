<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!--<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />-->
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Print Billing</title>
<?php 
	require('roots.php');
	//require_once($root_path.'include/inc_init_main.php');	
	require_once($root_path.'modules/billing/ajax/bill-print.common.php');
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype1.5.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/json.js"></script>
<script type="text/javascript" src="./js/billing-print.js"></script>

<?php
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
?>
<script language="javascript">
<!--
function closeWindow(){
	tmpwin = window.open("<?=$root_path?>include/blank.html", "_self");
} // window.close(); <img id="ajax-loading" src="../../images/loading6.gif" align="absmiddle" border="0" style="display:none"/>

function putJSONObject(){
	var objPerson = document.getElementById('personInfo').value;
	var objAcc = document.getElementById('acc').value;
	var objHs = document.getElementById('hospitalService').value;
	var objMed = document.getElementById('medicines').value;
	var objMedSup = document.getElementById('medSupplies').value;
	
	document.printbill.putJsonToJava(true,objPerson, objAcc, objHs, objMed);
}

-->	
</script>
<!--<img id="ajax-loading" src="../../images/loading6.gif" align="absmiddle" border="0" />	-->
</head>
<BODY onload="xajax_initMain(<?=$_GET['encounter_nr']?>);">
	<form id="frm1" name="frmHiddenInputs">
		<input type="hidden" id="personInfo" value="" />
		<input type="hidden" id="acc" value="" />
		<input type="hidden" id="hospitalService" value="" />
		<input type="hidden" id="medicines" value=""  />
		<input type="hidden" id="medSupplies" value=""  />
		
		
		<input type="button" value="OK" onclick="putJSONObject();" />
		<input type="button" value="Cancel" onclick="document.printbill.stop();" />
	</form>
	<center>

	<applet  name="printbill" 
			archive="<?=$root_path?>modules/billing/Billing.jar" 
			code="printBilling.class" 
			width="400" 
			height="100" MAYSCRIPT>
			<param id="sourceURL" name="sourceURL" value="\\192.168.2.6\EPSON_C59" />
	</applet>
	</center>
</BODY>
</html>
