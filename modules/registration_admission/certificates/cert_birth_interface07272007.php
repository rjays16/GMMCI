<?php
include("roots.php");
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');
include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;
	# Create the helper class for the country table 
include_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address('country');

define('LANG_FILE','aufnahme.php');
$local_user='aufnahme_user';
require($root_path.'include/inc_front_chain_lang.php');

if (isset($_GET['pid']) && $_GET['pid']){
	$pid = $_GET['pid'];
}
if (isset($_POST['pid']) && $_POST['pid']){
	$pid = $_POST['pid'];
}

include_once($root_path.'include/care_api_classes/class_cert_birth.php');
$obj_birthCert = new BirthCertificate($pid);

if ($pid){
	if (!($basicInfo=$person_obj->BasicDataArray($pid))){
		echo '<em class="warn"> sorry byt the page cannot be displayed!</em>';
		exit();
	}
#	echo "basicInfo : <br> \n"; print_r($basicInfo); echo "<br>\n";
	extract($basicInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid PID!</em>';
	exit();
}

$birthYear = intval(substr($date_birth, 0, 4)); 
$birthMonth = intval(substr($date_birth, 5, 7)); 
$birthDay = intval(substr($date_birth, 8, 10)); 
/*
echo "birthYear = '".$birthYear."' <br>\n";
echo "birthMonth = '".$birthMonth."' <br>\n";
echo "birthDay = '".$birthDay."' <br>\n";
*/
$errorMsg='';

if (trim($HTTP_POST_VARS['birth_time'])!=''){
	#convert to 24-Hour time
	$birth_time_array_tmp = explode(':',$HTTP_POST_VARS['birth_time']);
	$hr = intval($birth_time_array_tmp[0]);
	$min = $birth_time_array_tmp[1];
	if($_POST['selAMPM'] == 'P.M.'){
		if(($hr>0)&&($hr<12)){ 
		$hr = $hr+12; 
		} 
		$HTTP_POST_VARS['birth_time_tmp2'] = $hr.':'.$min.':00';
		$HTTP_POST_VARS['birth_time'] = $hr.':'.$min.':00';
	}elseif(($_POST['selAMPM'] == 'A.M.')&&($hr==12)){
		$HTTP_POST_VARS['birth_time_tmp2'] = '00:'.$min.':00';
		$HTTP_POST_VARS['birth_time'] = '00:'.$min.':00';
	}
}
/*
echo "HTTP_POST_VARS['birth_time_tmp2'] = '".$HTTP_POST_VARS['birth_time_tmp2']."' <br> \n";

echo "HTTP_POST_VARS['officer_date_sign'] = '".$HTTP_POST_VARS['officer_date_sign']."' <br> \n";
echo "_POST['officer_date_sign'] = '".$_POST['officer_date_sign']."' <br> \n";

echo "HTTP_POST_VARS : <br>"; print_r($HTTP_POST_VARS); echo "<br> \n";
echo "pid = '".$pid."' <br> \n";
*/
if (isset($_POST['mode'])){
	switch($_POST['mode']) {
		case 'save':
			$tmp=array();
			if (trim($HTTP_POST_VARS['attendant_date_sign'])!=""){
#			if (!empty(trim($HTTP_POST_VARS['attendant_date_sign']))){
				$HTTP_POST_VARS['attendant_date_sign'] = formatDate2STD($HTTP_POST_VARS['attendant_date_sign'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['informant_date_sign'])!=""){
				$HTTP_POST_VARS['informant_date_sign'] = formatDate2STD($HTTP_POST_VARS['informant_date_sign'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['encoder_date_sign'])!=""){
				$HTTP_POST_VARS['encoder_date_sign'] = formatDate2STD($HTTP_POST_VARS['encoder_date_sign'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['f_com_tax_date'])!=""){
				$HTTP_POST_VARS['f_com_tax_date'] = formatDate2STD($HTTP_POST_VARS['f_com_tax_date'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['m_com_tax_date'])!=""){
				$HTTP_POST_VARS['m_com_tax_date'] = formatDate2STD($HTTP_POST_VARS['m_com_tax_date'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['officer_date_sign'])!=""){
				$HTTP_POST_VARS['officer_date_sign'] = formatDate2STD($HTTP_POST_VARS['officer_date_sign'],$date_format,$tmp);
			}
			$HTTP_POST_VARS['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
			$HTTP_POST_VARS['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_dt']=date('Y-m-d H:i:s');
			if ($obj_birthCert->saveBirthCertificateInfoFromArray($HTTP_POST_VARS)){
				$errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
			}else{
				$errorMsg='<font style="color:#FF0000">'.$obj_birthCert->getErrorMsg().'</font>';			
			}
		break;
		case 'update':
			$tmp=array();
			if (trim($HTTP_POST_VARS['attendant_date_sign'])!=""){
#			if (!empty(trim($HTTP_POST_VARS['attendant_date_sign']))){
				$HTTP_POST_VARS['attendant_date_sign'] = formatDate2STD($HTTP_POST_VARS['attendant_date_sign'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['informant_date_sign'])!=""){
				$HTTP_POST_VARS['informant_date_sign'] = formatDate2STD($HTTP_POST_VARS['informant_date_sign'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['encoder_date_sign'])!=""){
				$HTTP_POST_VARS['encoder_date_sign'] = formatDate2STD($HTTP_POST_VARS['encoder_date_sign'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['f_com_tax_date'])!=""){
				$HTTP_POST_VARS['f_com_tax_date'] = formatDate2STD($HTTP_POST_VARS['f_com_tax_date'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['m_com_tax_date'])!=""){
				$HTTP_POST_VARS['m_com_tax_date'] = formatDate2STD($HTTP_POST_VARS['m_com_tax_date'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['officer_date_sign'])!=""){
				$HTTP_POST_VARS['officer_date_sign'] = formatDate2STD($HTTP_POST_VARS['officer_date_sign'],$date_format,$tmp);
			}
			$HTTP_POST_VARS['history'] = "Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
			$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['modify_dt']=date('Y-m-d H:i:s');
			if ($obj_birthCert->updateBirthCertificateInfoFromArray($HTTP_POST_VARS)){
				$errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated sucessfully!".'</font>';
			}else{
				$errorMsg='<font style="color:#FF0000">'.$obj_birthCert->getErrorMsg().'</font>';			
			}
		break;
	}# end of switch statement
}

#echo "obj_birthCert->sql = '".$obj_birthCert->sql."' <br> \n";

$birthCertInfo = $obj_birthCert->getBirthCertRecord($pid);
if ($birthCertInfo){
	extract($birthCertInfo);
	$marriage_type = substr($parent_marriage_info, 0, 1); 
	$parent_marriage_info_tmp = substr($parent_marriage_info, 4); 
/*
	echo "parent_marriage_info  = '".$parent_marriage_info."' <br> \n";
	echo "marriage_type  = '".$marriage_type."' <br> \n";
	echo "parent_marriage_info_tmp = '".$parent_marriage_info_tmp."' <br> \n";
*/
	$attendant_type = substr(trim($birthCertInfo['attendant_type']),0,1);
	$attendant_type_others = substr(trim($birthCertInfo['attendant_type']),4);
#	$attendant_type = substr(trim($attendant_type),0,1);
#	$attendant_type_others = substr(trim($attendant_type),4);
/*
	echo "attendant_type = '".$attendant_type."' <br> \n";
	echo "attendant_type_others = '".$attendant_type_others."' <br> \n";
	echo "f_com_tax_nr = '".$f_com_tax_nr."' <br> \n";
	echo "m_com_tax_nr = '".$m_com_tax_nr."' <br> \n";
	echo "birthCertInfo : <br>"; print_r($birthCertInfo); echo "<br> \n";
*/
}

	if ($marriage_type=="2"){
		# Not Married
		$presetJavascript = "			
				blocking('married_parents', false); 
				blocking('married_parents_not', true);";
	}else{
		# Married
		$presetJavascript = "			
				blocking('married_parents', true); 
				blocking('married_parents_not', false);";
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Certificate of Live Birth</title>
<style type="text/css">
<!--
.style2 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
body {
	background-color: #FFFFFF;
}
.style4 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style5 {	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	color: #FFFFFF;
	font-weight: bold;
}
.style6 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; }
.style8 {color: #7C3501}
.style9 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #333300; }
.style11 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #7C3501; }
.style12 {color: #000066}
.style13 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #000066; }
.style15 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #FF6600; }
.style18 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #F9FFFB; }
a {
	font-size: 10px;
	color: #006600;
	font-family: Arial, Helvetica, sans-serif;
}
a:link {
	text-decoration: none;
}
a:visited {
	text-decoration: none;
}
a:hover {
	text-decoration: none;
}
a:active {
	text-decoration: none;
}
-->
</style>

<script language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>

<?php
	echo '<script type="text/javascript" language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\n";

	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\n";
/*	
	echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
*/
	echo '<script language="javascript" src="'.$root_path.'js/setdatetime.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/checkdate.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/dtpick_care2x.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\n";
?>

<script language="javascript">

		/*	
				This will trim the string i.e. no whitespaces in the
				beginning and end of a string AND only a single
				whitespace appears in between tokens/words 
				input: object
				output: object (string) value is trimmed
		*/
	function trimString(objct){
		objct.value = objct.value.replace(/^\s+|\s+$/g,"");
		objct.value = objct.value.replace(/\s+/g," "); 
	}/* end of function trimString */

	function blocking(objectName, flag){
//		alert("objectName = '"+objectName+"' \n flag = '"+flag+"'");
		if (document.layers) {
			document.layers[objectName].display = (flag) ? '' : 'none';
		} else if (document.all) {
			document.all[objectName].style.display = (flag) ? '' : 'none';
		} else if (document.getElementById) {
			document.getElementById(objectName).style.display = (flag) ? '' : 'none';
		}
	}/* end of function blocking*/



	var seg_validTime=false;

	function setFormatTime(thisTime){
	//	var time = $('time_text_d');
		var stime = thisTime.value;
		var hour, minute;
		var ftime ="";
		var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
		var f2 = /^[0-9]\:[0-5][0-9]$/;
		
		trimString(thisTime);
	
		if (thisTime.value==''){
			seg_validTime=false;
			return;
		}
	
		stime = stime.replace(':', '');
	
		if (stime.length == 3){
			hour = stime.substring(0,1);
			minute = stime.substring(1,3);
		} else if (stime.length == 4){
			hour = stime.substring(0,2);
			minute = stime.substring(2,4);
		}else{
			alert("Invalid time format.");
			thisTime.value = "";
			seg_validTime=false;
			thisTime.focus();
			return;
		}
		
		if (hour==0){
			 hour = 12;
			 $('selAMPM').value = "A.M.";		
		}else	if((hour > 12)&&(hour < 24)){
			 hour -= 12;
			 $('selAMPM').value = "P.M.";
		}
	
		ftime =  hour + ":" + minute;
		
		if(!ftime.match(f1) && !ftime.match(f2)){
			thisTime.value = "";
			alert("Invalid time format.");
			seg_validTime=false;   
			thisTime.focus();
		}else{
			thisTime.value = ftime;
			seg_validTime=true;   
		}
	}// end of function setFormatTime
	
	var countingNumber = true;
	var wholeNumber = false;
	
	function chkInteger(obj,noZero){
//	function chkInteger(obj){
		var objValue = obj.value;
		var objName = obj.name;
		
		if (objValue=="")
			return false;

		if (	isNaN(parseInt(objValue, 10)) || (parseInt(objValue, 10) < 0) ||
				((noZero) && (parseInt(objValue, 10)==0)) ){
			switch (objName){
				case 'birth_order':
						msg=" Invalid birth order! \n A valid number is expected.";
					break;
				case 'birth_rank_tmp':
				case 'birth_rank_others':
						msg=" Invalid birth rank! \n A valid number is expected.";
						$('birth_rank').value='';
					break;
				case 'm_total_alive':
						msg=" Invalid total number of children born alive! \n A valid number is expected.";
					break;
				case 'm_still_living':
						msg=" Invalid number of children still living includin this birth! \n A number is expected.";
					break;
				case 'm_now_dead':
						msg=" Invalid number of children born alive but are now dead! \n A valid number is expected.";
					break;
				case 'm_age':
						msg=" Invalid age of the mother! \n A valid number is expected.";
					break;
				case 'f_age':
						msg=" Invalid age of the father! \n A valid number is expected.";
					break;
				case 'birth_weight':
						msg=" Invalid weight at birth! \n A valid number is expected.";
					break;					
			}
			alert(msg);
			obj.value="";
			obj.focus();
			return false;
		}
		obj.value = parseInt(objValue, 10);
		return true;
	}// end of function chkInteger

	function chkDecimal(obj){
		var objValue = obj.value;
		var objName = obj.name;
		var ms='';

		if (objValue=="")
			return false;
		if (!parseFloat(objValue)){
			switch (objName){
				case 'm_age':
						msg=" Invalid age of the mother! \n A valid number is expected.";
					break;
				case 'f_age':
						msg=" Invalid age of the father! \n A valid number is expected.";
					break;
				case 'birth_weight':
						msg=" Invalid wieght at birth! \n A valid number is expected.";
					break;					
			}
			alert(msg);
			obj.value="";
			obj.focus();
			return false;
		}
		obj.value = parseFloat(objValue);
		return true;
	}// end of function chkDecimal

	function typeOfBirth(obj){
		var objValue = obj.value;
		var objBirthRank = document.livebirth_certificate.birth_rank_tmp;
		var objBirthRankOthers = document.livebirth_certificate.birth_rank_others;
		
		alert("typeOfBirth : objValue = '"+objValue+"'; objBirthRank ='"+objBirthRank+"'; objBirthRank.length ='"+objBirthRank.length+"'");

		if (objValue==1){
			objBirthRank.value = '';
			objBirthRankOthers.value = '';
			for(var i=0; i<objBirthRank.length;i++ ){
			 	objBirthRank[i].disabled = true;
			 	objBirthRank[i].checked = false;
			} 
			objBirthRankOthers.disabled = true;
		}else{
			for(var i=0; i<objBirthRank.length;i++ ){
			 	objBirthRank[i].disabled = false;
			} 
		 	objBirthRank[0].checked = true;
//			objBirthRankOthers.disabled = false;
		}
		$('birth_type').value=objValue;
		alert("typeOfBirth : $F('birth_rank') ='"+$F('birth_rank')+"'");
	}/* end of function typeOfBirth */

 	function rankOfBirth(obj){
		var objValue = obj.value;
		var objBirthRankOthers = $('birth_rank_others');
		
		alert("rankOfBirth: objValue = '"+objValue+"'; objBirthRankOthers.value ='"+objBirthRankOthers.value+"'");

		if ((objValue!="1")&&(objValue!="2")){
			objBirthRankOthers.disabled = false;
			$('birth_rank').value='';
		}else{
			objBirthRankOthers.disabled = true;
			objBirthRankOthers.value = '';
		}
		$('birth_rank').value=objValue;
		alert("rankOfBirth : $F('birth_rank') ='"+$F('birth_rank')+"'");
	}/* end of function rankOfBirth */

	function parentName(obj){
		var parentType = obj.name.substring(0,1).toLowerCase();
//		alert("obj.value = '"+obj.name+"'; parentType = '"+ parentType+"'");
		if (parentType=="m"){
		   // mother
//			$('m_fullname').value = $F('m_name_first')+" "+$F('m_name_middle')+" "+$F('m_name_last');
			if (($F('m_name_first')=="")&&($F('m_name_middle')=="")&&($F('m_name_last')==""))
				$('m_fullname').innerHTML = "(mother's name)";
			else
				$('m_fullname').innerHTML = $F('m_name_first')+" "+$F('m_name_middle')+" "+$F('m_name_last');
		}else{
		   //father
//			$('f_fullname').value = $F('f_name_first')+" "+$F('f_name_middle')+" "+$F('f_name_last');
			if (($F('f_name_first')=="")&&($F('f_name_middle')=="")&&($F('f_name_last')==""))
				$('f_fullname').innerHTML = "(father's name)";
			else
				$('f_fullname').innerHTML = $F('f_name_first')+" "+$F('f_name_middle')+" "+$F('f_name_last');
		}
	}/* end of function parentName */

	function typeOfMarriageInfo(objValue){

		alert("typeOfMarriageInfo : $F('parent_marriage_info') = '"+$F('parent_marriage_info')+"'");
		if (objValue=="1"){   //Married
			$('f_com_tax_nr').value='';
			$('f_com_tax_date').value='';
			$('f_com_tax_place').value='';
			$('m_com_tax_nr').value='';
			$('m_com_tax_date').value='';
			$('m_com_tax_place').value='';
			$('officer_title').value='';
			$('officer_name').value='';
			$('officer_address').value='';
		}else{   //Not Married
			$('parent_marriage_info_tmp').value='';
		}
		alert("typeOfMarriageInfo : $F('parent_marriage_info') = '"+$F('parent_marriage_info')+"'");
	}/* end of function typeOfMarriageInfo */

	function typeOfMarriage(obj){
		var objValue = obj.value;

		alert("typeOfMarriage : $F('parent_marriage_info') = '"+$F('parent_marriage_info')+"'");
		if (objValue=="1"){   //Married
			blocking("married_parents", true); // display
			blocking("married_parents_not", false); // not display
			$('parent_marriage_info').value='1';
		}else{   //Not Married
			blocking("married_parents", false); // not display
			blocking("married_parents_not", true); // display
			$('parent_marriage_info').value='2';
		}
		alert("typeOfMarriage : $F('parent_marriage_info') = '"+$F('parent_marriage_info')+"'");
	}/* end of function typeOfMarriage */
 	
	function typeOfAttendant(obj){
		var objValue = obj.value;
		var objAttendantTypeOthers = $('attendant_type_others');

		alert("attendant_type : $F('attendant_type') ='"+$F('attendant_type')+"'");
		alert("typeOfAttendant:  typeOfAttendant : objValue = '"+objValue+"'; objAttendantTypeOthers.value ='"+objAttendantTypeOthers.value+"'");

		if (objValue=="5"){
			objAttendantTypeOthers.disabled = false;
		}else{
			objAttendantTypeOthers.value = "";
			objAttendantTypeOthers.disabled = true;
		}
		switch(objValue){
			case "1":
				objValue = objValue + " - Physician";
				break;
			case "2":
				objValue = objValue + " - Nurse";
				break;
			case "3":
				objValue = objValue + " - Midwife";
				break;
			case "4":
				objValue = objValue + " - Hilot";
				break;
			case "5":
				objValue = objValue + " - "+objAttendantTypeOthers.value;
				break;
		}
		$('attendant_type').value=objValue;
		
		alert("attendant_type : $F('attendant_type') ='"+$F('attendant_type')+"'");
	}/* end of function typeOfAttendant */

	function printBirthCert(id){
		if (id==0) 
			id="";
		if (window.showModalDialog){  //for IE
			window.showModalDialog("cert_birth_pdf.php?id="+id,"width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
		}else{
			window.open("cert_birth_pdf.php?id="+id,"birthCertificate","modal, width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
		}
	}/* end of function printBirthCert */

	function chckForm(d){
		var msg='';
		alert("d = '"+d+"'");
		msg= " $F('birth_rank') ='"+$F('birth_rank')+"'; \n $F('birth_rank_others') ='"+$F('birth_rank_others')+"'";
		alert(msg);
		msg = " $F('parent_marriage_info') ='"+$F('parent_marriage_info')+
				"'; \n $F('parent_marriage_info').substring(0,1) ='"+$F('parent_marriage_info').substring(0,1)+
				"'; \n $F('parent_marriage_info_tmp') ='"+$F('parent_marriage_info_tmp')+"'";
		alert(msg);
		msg = "$F('attendant_type') ='"+$F('attendant_type')+
				"'; \n $F('attendant_type').substring(0,1) ='"+$F('attendant_type').substring(0,1)+
				"'; \n $F('attendant_type_others') ='"+$F('attendant_type_others')+"'";
		alert(msg);

		var p_marriage_info=$F('parent_marriage_info');
		var a_type = $F('attendant_type');
		
		if (	($F('birth_type')!="1") && ($F('birth_rank')!="1") && 
				($F('birth_rank')!="2") && ($F('birth_rank_others')=="") ){
			alert("Please enter the rank of this child (for multiple birth).");
			$('birth_rank_others').focus();
			return false;
		}else	if ((p_marriage_info.substring(0,1)=="1") && ($F('parent_marriage_info_tmp')=="")){
			alert("Please enter the date and place of marriage.");
			$('parent_marriage_info_tmp').focus();
			return false;
		}else if ((a_type.substring(0,1)=="5") && ($F('attendant_type_others')=="")){
			alert("Please enter the type of attendant.");
			$('attendant_type_others').focus();
			return false;
		}

		msg = "2 : $F('parent_marriage_info') ='"+$F('parent_marriage_info')+"'";
		alert(msg);
		msg = "2 : $F('attendant_type') ='"+$F('attendant_type')+"'";
		alert(msg);
		return true;
	}/* end of function chckForm */
</script>
</head>

<body>
<form id="livebirth_certificate" name="livebirth_certificate" method="post" action="" onsubmit="return chckForm(this);">
<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td align="left" bgcolor="#F9FFFB">
     
        <span class="style2"><a name="goto">.</a>Republic of the Phillippines</span><BR /> 
        <span class="style6">OFFICE OF THE CIVIL REGISTRAR GENERAL</span><BR /> 
        <span class="style15">CERTIFICATE OF LIVE BIRTH</span>    </td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF">
      <table width="100%" height="44" border="0" cellpadding="0" cellspacing="2">
        <tr>
          <td width="72%" height="19" bgcolor="#F9FFFB">
              <span class="style2">Province &nbsp;&nbsp;</span> 
                <span class="style6">
                <input type="hidden" name="province" id="province" value="Davao del Sur">
					 <b>Davao del Sur</b></span> </td>
          <td width="28%" rowspan="2" align="right" bgcolor="#F9FFFB">
            <span class="style15">Registry no.</span><span class="style2"> &nbsp;&nbsp;</span>
            <input type="text" name="registry_nr" id="registry_nr" value="<?= $registry_nr ?>" onblur="trimString(this)">
          </td>
        </tr>
        <tr>
          <td height="19" bgcolor="#F9FFFB">
              <span class="style2">City/Municipality &nbsp;&nbsp;</span>
                
              <span class="style6">
              <input type="hidden" name="muncity" id="muncity" value="Davao City">
				   <b>Davao City</b></span> </td>
        </tr>
      </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="8%" class="style15">Go to</td>
        <td width="16%"><a href="#mother">Mother Profile</a></td>
        <td width="51%"><a href="#father">Father Profile</a></td>
        <td width="25%">&nbsp;</td>
      </tr>
    </table>
    </td>
  </tr>
  <tr>
    <td align="center" bordercolor="#FFCCCC" bordercolordark="#000000">
      <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td width="3%" bgcolor="#98D228" ><img src="images/brdr_03.gif" width="31" height="26" /></td>
          <td width="94%" bgcolor="#98D228"><span class="style5">CHILD</span></td>
          <td width="3%" bgcolor="#98D228">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="3" bgcolor="#98D228">
            <table width="100%" border="0" cellspacing="1" cellpadding="1">
              <tr>
                <td height="468" align="center" bgcolor="#F9FFFB"> <br />
                    <table width="90%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td height="36" colspan="3" valign="top" bgcolor="#98D228">
                          <table width="100%" border="0" cellpadding="0" cellspacing="1">
                            <tr>
                              <td width="20%" align="center" valign="middle" bgcolor="#CFEC9B" class="style9">Name</td>
                              <td width="20%" align="center" valign="top" bgcolor="#F9FFFB"><span class="style2">Given Name </span><br />
                                  <span class="style2"><b>
                                  <?= ucwords(strtolower($name_first)) ?>
                                  </b> </span>
                                  <input type="hidden" name="name_first" id="name_first" value="<?= ucwords(strtolower($name_first)) ?>" />
                              </td>
                              <td width="30%" align="center" valign="top" bgcolor="#F9FFFB"> <span class="style2">Middle Name </span><br />
                                  <span class="style2"> <b>
                                  <?= ucwords(strtolower($name_middle)) ?>
                                  </b> </span>
                                  <input type="hidden" name="name_middle" id="name_middle" value="<?= ucwords(strtolower($name_middle)) ?>" />
                              </td>
                              <td width="30%" align="center" valign="top" bgcolor="#F9FFFB"> <span class="style2">Last Name </span><br />
                                  <span class="style2"> <b>
                                  <?= ucwords(strtolower($name_last)) ?>
                                  </b> </span>
                                  <input type="hidden" name="name_last" id="name_last" value="<?= ucwords(strtolower($name_last)) ?>" />
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>

                    <table width="90%" border="0" cellpadding="0" cellspacing="1">
                      <tr>
                        <td width="25%" height="74" valign="top" bgcolor="#F9FFFB">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td valign="top" bgcolor="#F9FFFB">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td height="74" valign="top" bgcolor="#98D228">
                                      <table width="100%" border="0" cellpadding="4" cellspacing="1">
                                        <tr>
                                          <td colspan="2" bgcolor="#CFEC9B" class="style9">SEX</td>
                                        </tr>
                                        <tr>
                                          <td width="50%" align="center" bgcolor="#F9FFFB" class="style2">
                                            <center>
                                              <input type="hidden" name="sex" id="sex" value="<?= $sex ?>" />
                                              <br />
                                              <input name="radiobutton" type="radio" value="radiobutton" disabled="disabled" <?php if ($sex=='m') echo "checked"; ?> />
                                              Male
                                            </center>
                                          </td>
                                          <td width="50%" align="center" bgcolor="#F9FFFB" class="style2">
                                            <center>
                                              <br />
                                              <input name="radiobutton" type="radio" value="radiobutton" disabled="disabled" <?php if ($sex=='f') echo "checked"; ?> />
                                              Female
                                            </center>
                                          </td>
                                        </tr>
                                      </table>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                        <td width="75%" valign="top" bgcolor="#F9FFFB">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td align="right" bgcolor="#F9FFFB">
                                <table width="95%" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td height="57" valign="top" bgcolor="#98D228">
                                      <table width="100%" border="0" cellpadding="4" cellspacing="1">
                                        <tr>
                                          <td colspan="3" bgcolor="#CFEC9B" class="style9">DATE OF BIRTH</td>
                                        </tr>
                                        <tr>
                                          <td width="25%" align="center" bgcolor="#F9FFFB" class="style2">
                                            <center>
                                              Day <br />
                                              <b>
                                                <?= $birthDay ?>
                                              </b>
                                              <input type="hidden" name="birth_day" id="birth_day" value="<?= $birthDay ?>" />
                                              <select name="select4" disabled="disabled">
                                                <?php
for ($i=1; $i<=31; $i++){
	if ($birthDay==$i)
		echo "							<option value='$i' selected>$i</option> \n";
	else
		echo "							<option value='$i'>$i</option> \n";
}
?>
                                              </select>
                                            </center>
                                          </td>
                                          <td width="37%" bgcolor="#F9FFFB" class="style2">
                                            <center>
                                              Month <br />
                                              <b>
                                                <?php 
								switch($birthMonth ){
									case 1: echo"January"; break;
									case 2: echo"February"; break;									
									case 3: echo"March"; break;									
									case 4: echo"April"; break;									
									case 5: echo"May"; break;									
									case 6: echo"June"; break;									
									case 7: echo"July"; break;									
									case 8: echo"August"; break;									
									case 9: echo"September"; break;									
									case 10: echo"October"; break;									
									case 11: echo"November"; break;									
									case 12: echo"December"; break;									
								}
							?>
                                              </b>
                                              <input type="hidden" name="birth_month" id="birth_month" value="<?= $birthMonth ?>" />
                                              <select name="select4" disabled="disabled">
                                                <option value="1" <?php if ($birthMonth==1) echo "selected"; ?>>January</option>
                                                <option value="2" <?php if ($birthMonth==2) echo "selected"; ?>>February</option>
                                                <option value="3" <?php if ($birthMonth==3) echo "selected"; ?>>March</option>
                                                <option value="4" <?php if ($birthMonth==4) echo "selected"; ?>>April</option>
                                                <option value="5" <?php if ($birthMonth==5) echo "selected"; ?>>May </option>
                                                <option value="6" <?php if ($birthMonth==6) echo "selected"; ?>>June</option>
                                                <option value="7" <?php if ($birthMonth==7) echo "selected"; ?>>July</option>
                                                <option value="8" <?php if ($birthMonth==8) echo "selected"; ?>>August</option>
                                                <option value="9" <?php if ($birthMonth==9) echo "selected"; ?>>September</option>
                                                <option value="10" <?php if ($birthMonth==10) echo "selected"; ?>>October</option>
                                                <option value="11" <?php if ($birthMonth==11) echo "selected"; ?>>November</option>
                                                <option value="12" <?php if ($birthMonth==12) echo "selected"; ?>>December</option>
                                              </select>
                                            </center>
                                          </td>
                                          <td width="38%" bgcolor="#F9FFFB" class="style2">
                                            <center>
                                              Year <br />
                                              <b>
                                                <?= $birthYear ?>
                                              </b>
                                              <input type="hidden" name="birth_year" id="birth_year" value="<?= $birthYear ?>" />
                                              <select name="select4" disabled="disabled">
                                                <?php
for ($i=date(Y); $i>=1900; $i--){
	if ($birthYear==$i)
		echo "							<option value='$i' selected>$i</option>\n";
	else
		echo "							<option value='$i'>$i</option>\n";
}
?>
                                              </select>
                                            </center>
                                          </td>
                                        </tr>
                                      </table>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  
                    <table width="90%" border="0" cellspacing="1" cellpadding="0">
                      <tr>
                        <td bgcolor="#98D228">
                          <table width="100%" border="0" cellpadding="4" cellspacing="1">
                            <tr>
                              <td colspan="3" bgcolor="#CFEC9B" class="style6">PLACE OF BIRTH</td>
                            </tr>
                            <tr align="center" valign="top">
                              <td width="40%" height="71" bgcolor="#F9FFFB"> <span class="style2">Name of Hospital/Clinic/Institution/ <br />
                                House No.,Street,Barangay </span><br />
                                <span class="style6"><b>Gonzales Maranan Medical Center Incorporated</b>
                                  <input type="hidden" name="birth_place_basic" id="birth_place_basic" value="Gonzales Maranan Medical Center Incorporated" />
                                </span></td>
                              <td width="30%" bgcolor="#F9FFFB"> <span class="style2">City/Municipality </span><br />
                                  <br />
                                  <span class="style6"><b>DIGOS CITY</b>
                                  <input type="hidden" name="birth_place_mun" id="birth_place_mun" value="Davao City" />
                                </span></td>
                              <td width="30%" bgcolor="#F9FFFB"> <span class="style2">Province </span><br />
                                  <br />
                                  <span class="style6"><b>DAVAO DEL SUR</b>
                                  <input type="hidden" name="birth_place_prov" id="birth_place_prov" value="Davao del Sur" />
                                </span></td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  
                    <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
                      <tr>
                        <td height="124" valign="top" bgcolor="#98D228">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td height="124" valign="top" bgcolor="#F9FFFB">
                                <table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#F9FFFB">
                                  <tr>
                                    <td width="50%" height="122" align="left" valign="top" bgcolor="#F9FFFB">
                                      <table width="90%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td bgcolor="#98D228">
                                            <table width="100%" border="0" cellpadding="4" cellspacing="1">
                                              <tr>
                                                <td bgcolor="#CFEC9B" class="style6"> TYPE OF BIRTH</td>
                                              </tr>
                                              <tr>
                                                <td valign="top" bgcolor="#F9FFFB" class="style2">
                                                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                      <td width="31%" bgcolor="#F9FFFB">
                                                        <input type="hidden" name="birth_type" id="birth_type" value="<?php if ($birth_type) echo $birth_type; else echo "1" ?>" />
                                                        <input name="birth_type_tmp" id="radio" type="radio" value="1" onclick="typeOfBirth(this)" <?php if (($birth_type=='1')||(!$birth_type)) echo "checked"; ?> />
                                                        Single</td>
                                                      <td width="90%" rowspan="3" valign="top">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                      <td bgcolor="#F9FFFB">
                                                        <input name="birth_type_tmp" id="radio" type="radio" value="2" onclick="typeOfBirth(this)" <?php if ($birth_type=='2') echo "checked"; ?> />
                                                        Twin</td>
                                                    </tr>
                                                    <tr>
                                                      <td height="23" bgcolor="#F9FFFB">
                                                        <input name="birth_type_tmp" id="radio" type="radio" value="3" onclick="typeOfBirth(this)" <?php if ($birth_type=='3') echo "checked"; ?> />
                                                        Triplet, etc.</td>
                                                    </tr>
                                                  </table>
												  
												    <input type="hidden" name="birth_rank" id="birth_rank" value="<?= $birth_rank ?>" />
                                                  <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                                    <tr>
                                                      <td colspan="2" bgcolor="#CFEC9B"><span class="style6">IF MULTIPLE BIRTH, CHILD WAS</span></td>
                                                    </tr>
                                                    <tr>
                                                      <td width="54%">
                                                        <input name="birth_rank_tmp" id="radio" type="radio" onclick="rankOfBirth(this);" value="1" <?php if ($birth_rank=='1') echo "checked"; if (($birth_type==1)||(!$birth_type)) echo "disabled"; ?> />
                                                        First</td>
                                                      <td width="46%" valign="top">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                      <td>
                                                        <input name="birth_rank_tmp" id="radio" type="radio" onclick="rankOfBirth(this)" value="2" <?php if ($birth_rank=='2') echo "checked"; if (($birth_type==1)||(!$birth_type)) echo "disabled"; ?> />
                                                        Second</td>
                                                      <td width="46%" valign="top">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                      <td height="43">
                                                        <input name="birth_rank_tmp" id="radio" type="radio" onclick="rankOfBirth(this)" value="" <?php if (intval($birth_rank) > 2) echo "checked"; if (($birth_type==1)||(!$birth_type)) echo "disabled"; ?> />
                                                        Others, Specify
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="birth_rank_others" id="birth_rank_others" onblur="trimString(this); if(chkInteger(this,countingNumber)){ $('birth_rank').value=this.value; }" <?php if ($birth_rank > 2) echo 'value="'.$birth_rank.'"'; else echo "disabled"; ?> />
                                                      </td>
                                                      <td width="46%" valign="top">&nbsp;</td>
                                                    </tr>
                                                  </table>
                                                
												  
												  
												  
                                                </td>
                                              </tr>
                                            </table>
                                          </td>
                                        </tr>
                                      </table>
                                    </td>
                                    <td width="50%" align="right" valign="top" bgcolor="#F9FFFB">
                                      <table width="90%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td height="122" valign="top" bgcolor="#98D228">
                                            <table width="100%" border="0" cellpadding="4" cellspacing="1">
                                              <tr>
																<td width="100%" bgcolor="#CFEC9B" class="style6">BIRTH ORDER</td>
                                              </tr>
                                              <tr>
                                                <td width="100%" height="104" valign="top" bgcolor="#F9FFFB" class="style2">
                                                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                      <td bgcolor="#F9FFFB">&nbsp;&nbsp; (live births and fetal deaths including this delivery) </td>
                                                    </tr>
                                                    <tr>
                                                      <td bgcolor="#F9FFFB">
<!--
                                                        <input type="text" name="birth_order" id="birth_order" value="<?= $birth_order ?>" onblur="trimString(this); chkInteger(this,countingNumber);" />
-->
                                                        <input type="text" name="birth_order" id="birth_order" value="<?= $birth_order ?>" onblur="trimString(this);" />
                                                        &nbsp; (first, second, third, etc.) </td>
                                                    </tr>
                                                  </table>
                                                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                      <td width="100%" bgcolor="#CFEC9B" class="style6">&nbsp;WEIGHT AT BIRTH </td>
                                                    </tr>
                                                    <tr>
                                                      <td>
                                                        <input name="birth_weight" id="birth_weight" type="text" size="10" onblur="trimString(this); chkDecimal(this);" value="<?= $birth_weight ?>" />
                                                        &nbsp; grams </td>
                                                    </tr>
                                                  </table>
                                                </td>
                                              </tr>
                                            </table>
                                          </td>
                                        </tr>
                                      </table>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  <br />
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td height="37">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table>
    </td>
    </tr>
  <tr>
    <td align="center" bordercolor="#FFCCCC" bordercolordark="#000000">
      <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FEC194">
        <tr>
          <td width="2%"><img src="images/brdr_pnk_03.gif" width="31" height="26" /></td>
          <td width="65%" bgcolor="#FEC194" class="style5"><A NAME="mother">.</a>MOTHER</td>
          <td width="33%" align="right" bgcolor="#FEC194"><a href="#goto">Go to Top</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
        </tr>
        <tr>
          <td colspan="3" bgcolor="#FEC194">
            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
              <tr>
                <td bgcolor="#F9FFFB">
				<br />
                  <table width="90%" border="0" align="center" cellspacing="1">
                    <tr>
                      <td bgcolor="#FEC194">
                        <table width="100%" border="0" cellpadding="4" cellspacing="0" bordercolor="#FFECDD" class="style2">
                          <tr>
                            <td width="17%" align="center" valign="middle" bordercolor="#FFECDD" bgcolor="#FFECDD" class="style6 style8">MAIDEN NAME</td>
                            <td width="35%" align="center" bordercolor="#FFECDD" bgcolor="#FFF7F0">
                              <center>
                                <span class="style8">First Name </span><br />
                                <input name="m_name_first" type="text" class="style2" id="m_name_first" onblur="trimString(this); parentName(this);" value="<?= $m_name_first ?>" size="50" />
                              </center>
                            </td>
                            <td width="22%" align="center" bordercolor="#FFECDD" bgcolor="#FFF7F0">
                              <center>
                                <span class="style8">Middle Name </span><br />
                                <input name="m_name_middle" type="text" class="style2" id="m_name_middle" onblur="trimString(this); parentName(this);" value="<?= $m_name_middle ?>" size="30" />
                              </center>
                            </td>
                            <td width="26%" align="center" bordercolor="#FFECDD" bgcolor="#FFF7F0">
                              <center>
                                <span class="style8">Last Name </span><br />
                                <input name="m_name_last" type="text" class="style2" id="m_name_last" onblur="trimString(this); parentName(this);" value="<?= $m_name_last ?>" size="30" />
                              </center>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
				  <br />
                  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                      <td bgcolor="#FEC194">
                        <table width="100%" border="0" cellpadding="0" cellspacing="1">
                          <tr>
                            <td width="50%" bgcolor="#F9FFFB" class="style8 style2">
                              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td width="23%" align="center" bgcolor="#FFECDD"><b>CITIZENSHIP &nbsp;</b></td>
                                  <td width="77%" bgcolor="#FFF7F0">
												<input type="text" name="m_citizenship" id="m_citizenship" value="<?= $m_citizenship ?>" onblur="trimString(this);" />
                                  </td>
                                </tr>
                              </table>
                            </td>
                            <td width="50%" bgcolor="#F9FFFB" class="style8 style2">
                              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td width="18%" align="center" bgcolor="#FFECDD"><b>RELIGION &nbsp;&nbsp;</b></td>
                                  <td width="82%" bgcolor="#FFF7F0"><b>
                                    <select name="select" id="select">
                                      <?php
		# Retrieve record set of all religions
	$religion_obj = $person_obj->getReligion();
	if (empty($m_religion)|| !$m_religion) 
		$m_religion=1;   # default religion, 'Not Indicated'
	while ($result=$religion_obj->FetchRow()){
		$selected='';
		if ($m_religion==$result['religion_nr'])
			$selected='selected';
		echo '					<option value="'.$result['religion_nr'].'" '.$selected.'>'.$result['religion_name']."</option> \n";
	}
?>
                                    </select>
                                  </b></td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table><br />
                  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="51%" align="left" valign="top">
                        <table width="98%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td bgcolor="#FEC194">
                              <table width="100%" border="0" cellspacing="1" cellpadding="0">
                                <tr>
                                  <td bgcolor="#F9FFFB">
                                    <table width="100%" border="0" cellpadding="4" cellspacing="1" class="style2">
                                      <tr>
                                        <td width="70%" bgcolor="#FFECDD" class="style11">Total number of children born alive</td>
                                        <td width="30%" bgcolor="#FFF7F0">
                                          <input name="m_total_alive" id="m_total_alive" type="text" size="5" onblur="trimString(this); chkInteger(this,wholeNumber);" value="<?= $m_total_alive ?>" />
                                        </td>
                                      </tr>
                                      <tr>
                                        <td bgcolor="#FFECDD" class="style11">No. of children still living including this birth &nbsp;</td>
                                        <td bgcolor="#FFF7F0">
                                          <input name="m_still_living" id="m_still_living" type="text" size="5" onblur="trimString(this); chkInteger(this,wholeNumber);" value="<?= $m_still_living ?>" />
                                        </td>
                                      </tr>
                                      <tr>
                                        <td bgcolor="#FFECDD" class="style11">No. of children born alive but now are dead &nbsp;</td>
                                        <td bgcolor="#FFF7F0">
                                          <input name="m_now_dead" id="m_now_dead" type="text" size="5" onblur="trimString(this); chkInteger(this,wholeNumber);" value="<?= $m_now_dead ?>" />
                                        </td>
                                      </tr>
                                    </table>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </td>
                      <td width="49%" align="right" valign="top">
                        <table width="90%" border="0" cellpadding="1" cellspacing="0" class="style2">
                          <tr>
                            <td bgcolor="#FEC194">
                              <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                <tr>
                                  <td width="48%" bgcolor="#FFECDD"> <span class="style8"><b>OCCUPATION</b> &nbsp;&nbsp;</span></td>
                                  <td width="52%" bgcolor="#FFF7F0">
                                    <select name="select" id="select">
                                      <?php
		# Retrieve record set of all occupations
	$occupation_obj = $person_obj->getOccupation();
	if (empty($m_occupation)|| !$m_occupation) 
		$m_occupation=1;   # default occupation, 'Not Indicated'
	while ($result=$occupation_obj->FetchRow()){
		$selected='';
		if ($m_occupation==$result['occupation_nr'])
			$selected='selected';
		echo '					<option value="'.$result['occupation_nr'].'" '.$selected.'>'.$result['occupation_name']."</option> \n";
	}
?>
                                    </select>
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                          <tr>
                            <td bgcolor="#FEC194">
                              <table width="100%" border="0" cellspacing="0" cellpadding="4">
                                <tr>
                                  <td width="48%" bgcolor="#FFECDD"><b><span class="style8">Age at the time of this birth</span> &nbsp;</b></td>
                                  <td width="52%" bgcolor="#FFF7F0">
                                    <input name="m_age" id="m_age" type="text" size="5" onblur="trimString(this); chkInteger(this,countingNumber);" value="<?= $m_age ?>" />
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                        </td>
                    </tr>
                  </table><br />
                  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                      <td bgcolor="#FEC194">
                        <table width="100%" border="0" cellpadding="4" cellspacing="1">
                          <tr>
                            <td colspan="3" bgcolor="#FFECDD" class="style11">RESIDENCE</td>
                          </tr>
                          <tr align="center">
                            <td width="40%" bgcolor="#FFF7F0" class="style2"> Street/Baranggay <br />
                                <input type="text" name="m_residence_basic" id="m_residence_basic" onblur="trimString(this)" value="<?= $m_residence_basic ?>" size="50" />
                            </td>
                            <td width="30%" bgcolor="#FFF7F0" class="style2">City/Municipality <br />
                                <input type="text" name="m_residence_mun" id="m_residence_mun" onblur="trimString(this)" value="<?= $m_residence_mun ?>" size="30" />
                            </td>
                            <td width="30%" bgcolor="#FFF7F0" class="style2"> Province <br />
                                <input type="text" name="m_residence_prov" id="m_residence_prov" onblur="trimString(this)" value="<?= $m_residence_prov ?>" size="30" />
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
				  <br />
                </td>
              </tr>
            </table>
          </td>
          </tr>
        <tr>
          <td bgcolor="#F9FFFB">&nbsp;</td>
          <td bgcolor="#F9FFFB">&nbsp;</td>
          <td bgcolor="#F9FFFB">&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td align="center" bordercolor="#FFCCCC" bordercolordark="#000000">
      <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#A0C2DA">
        <tr>
          <td width="2%"><img src="images/brdr_blgry_03.gif" width="31" height="26" /></td>
          <td bgcolor="#A0C2DA" class="style5"><a name="father">.</a>FATHER</td>
          <td align="right" bgcolor="#A0C2DA" class="style5"><a href="#goto">Go to Top</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <tr>
          <td colspan="3" bgcolor="#A0C2DA">
            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
              <tr>
                <td bgcolor="#F9FFFB"> <br />
                    <table width="90%" border="0" align="center" cellpadding="1" cellspacing="0">
                      <tr>
                        <td bgcolor="#A0C2DA">
                          <table width="100%" border="0" cellpadding="2" cellspacing="0" bordercolor="#FFECDD" class="style2">
                            <tr>
                              <td width="17%" align="center" valign="middle" bordercolor="#FFECDD" bgcolor="#D9E7F0" class="style6 style12"> NAME</td>
                              <td width="35%" align="center" bordercolor="#EAF1F7" bgcolor="#EAF1F7">
                                <center>
                                  First Name <br />
                                  <input type="text" size="50" name="f_name_first" id="f_name_first" onblur="trimString(this); parentName(this);" value="<?= $f_name_first ?>" />
                                </center>
                              </td>
                              <td width="22%" align="center" bordercolor="#FFECDD" bgcolor="#EAF1F7">
                                <center>
                                  Middle Name <br />
                                  <input type="text" size="30" name="f_name_middle" id="f_name_middle" onblur="trimString(this); parentName(this);" value="<?= $f_name_middle ?>" />
                                </center>
                              </td>
                              <td width="26%" align="center" bordercolor="#FFECDD" bgcolor="#EAF1F7">
                                <center>
                                  Last Name <br />
                                  <input type="text" size="30" name="f_name_last" id="f_name_last" onblur="trimString(this); parentName(this);" value="<?= $f_name_last ?>" />
                                </center>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  <br />
                    <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
                      <tr>
                        <td bgcolor="#A0C2DA">
                          <table width="100%" border="0" cellpadding="2" cellspacing="1">
                            <tr>
                              <td width="50%" bgcolor="#F9FFFB" class="style8 style2">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td width="23%" align="center" bgcolor="#D9E7F0"><b><span class="style12">CITIZENSHIP</span> &nbsp;</b></td>
                                    <td width="77%" bgcolor="#EAF1F7">
                                      <input type="text" name="f_citizenship" id="f_citizenship" value="<?= $f_citizenship ?>" onblur="trimString(this);" />
                                    </td>
                                  </tr>
                                </table>
                              </td>
                              <td width="50%" bgcolor="#F9FFFB" class="style8 style2">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td width="18%" align="center" bgcolor="#D9E7F0"><b><span class="style12">RELIGION </span>&nbsp;&nbsp;</b></td>
                                    <td width="82%" bgcolor="#EAF1F7">
                                      <select name="select" id="select">
                                        <?php
		# Retrieve record set of all religions
	$religion_obj = $person_obj->getReligion();
	if (empty($f_religion)|| !$f_religion) 
		$f_religion=1;   # default religion, 'Not Indicated'
	while ($result=$religion_obj->FetchRow()){
		$selected='';
		if ($f_religion==$result['religion_nr'])
			$selected='selected';
		echo '					<option value="'.$result['religion_nr'].'" '.$selected.'>'.$result['religion_name']."</option> \n";
	}
?>
                                      </select>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  <br />
               
                    <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F9FFFB">
                      <tr>
                        <td height="32" bgcolor="#A0C2DA">
                          <table width="100%" border="0" cellspacing="1" cellpadding="2">
                            <tr>
                              <td bgcolor="#FFFFFF">
                                <table width="100%" border="0" cellpadding="2" cellspacing="0">
                                  <tr>
                                    <td width="23%" bgcolor="#D9E7F0"> 
                                      <center>
                                        <span class="style13">OCCUPATION</span> &nbsp;&nbsp;
                                      </center>
                                    </td>
                                    <td width="24%" bgcolor="#EAF1F7">
                                      <select name="select" id="select">
                                        <?php
		# Retrieve record set of all occupations
	$occupation_obj = $person_obj->getOccupation();
	if (empty($f_occupation)|| !$f_occupation) 
		$f_occupation=1;   # default occupation, 'Not Indicated'
	while ($result=$occupation_obj->FetchRow()){
		$selected='';
		if ($f_occupation==$result['occupation_nr'])
			$selected='selected';
		echo '					<option value="'.$result['occupation_nr'].'" '.$selected.'>'.$result['occupation_name']."</option> \n";
	}
?>
                                      </select>
                                    </td>
                                    <td width="26%" bgcolor="#D9E7F0" class="style13"> 
                                      <center>
                                        Age at the time of this birth 
                                      </center>
                                    </td>
                                    <td width="27%" bgcolor="#EAF1F7">
                                      <input name="f_age" id="f_age" type="text" size="5" onblur="trimString(this); chkInteger(this,countingNumber);" value="<?= $f_age ?>" />
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  <br />
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB">&nbsp;</td>
          <td width="65%" bgcolor="#F9FFFB">&nbsp;</td>
          <td width="33%" bgcolor="#F9FFFB">&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>
  
  
  <tr>
    <td align="center" class="style4">&nbsp;</td>
    </tr>
</table>
<br />
<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#A0C2DA">
  <tr>
    <td height="26" colspan="2" bgcolor="#F9FFFB" class="style6">
      <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#A0C2DA">
        <tr>
          <td width="3%"><img src="images/brdr_blgry_03.gif" width="31" height="26" /></td>
          <td width="97%" class="style5">DATE AND PLACE OF MARRIAGE OF PARENTS (If not married , accomplish Affidavit of Acknowledgment/Admission of Paternity) </td>
        </tr>
      </table>
      </td>
    </tr>
  <tr>
    <td height="40" colspan="2" bgcolor="#F9FFFB" class="style2">
		<input type="hidden" name="parent_marriage_info" id="parent_marriage_info" value="<?php if ($parent_marriage_info) echo $parent_marriage_info; else echo "1"; ?>">
	 	&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" name="marriage_type_tmp" id="marriage_type_tmp" onclick="typeOfMarriage(this)" value="1" <?php if (($marriage_type=='1')||(!$marriage_type)) echo "checked"; ?>>
		Married &nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" name="marriage_type_tmp" id="marriage_type_tmp" onclick="typeOfMarriage(this)" value="2" <?php if ($marriage_type=='2') echo "checked"; ?>>
		Not Married&nbsp;&nbsp;    </td>
  </tr>
  <tr>
    <td height="42" colspan="2" bgcolor="#F9FFFB" class="style2" id="married_parents">
		 &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
		 Date and Place of Marriage &nbsp;&nbsp;
		<input type="text" size="100" name="parent_marriage_info_tmp" id="parent_marriage_info_tmp" onblur="trimString(this); $('parent_marriage_info').value= '1 - '+this.value;" value="<?= $parent_marriage_info_tmp ?>">
    </td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#F9FFFB" class="style2">    </td>
  </tr>
  <tr>
    <td id="married_parents_not" colspan="2" bgcolor="#F9FFFB">
			<table bordercolor="#FF0000" bgcolor="#FFFFFF">
				<tr>
					<td width="35%">					</td>
					<td width="30%">					</td>
					<td width="35%">					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<b>AFFIDAVIT OF ACKNOWLEDGMENT/ADMISSION OF PATERNITY</b>					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" align="justify">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						We/I, 
						<span id="f_fullname" style="color:#FF0000;font-style:italic;">
						<?php
							$name_father=$f_name_first." ".$f_name_middle." ".$f_name_last;
							if (trim($name_father)!=""){
								echo "						$name_father";
							}else{
								echo "						(father's name)";
							}
						?>
						</span>
						and  
						<span id="m_fullname" style="color:#FF0000;font-style:italic;">
						<?php
							$name_mother=$m_name_first." ".$m_name_middle." ".$m_name_last;
							if (trim($name_father)!=""){
								echo "						$name_mother";
							}else{
								echo "						(mother's name)";
							}
						?>
						</span>
						parents/parent of the child mention in this Certificate of Live Birth, do hereby solemly swear that the information 
						contained herein are true and correct to the best of our/my knowledge and belief.					</td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td align="center">_____________________________________________</td>
					<td>&nbsp;</td>
					<td align="center">_____________________________________________</td>
				</tr>
				<tr>
					<td align="center">(Signature of Father)</td>
					<td>&nbsp;</td>
					<td align="center">(Signature of Mother)</td>
				</tr>
				<tr>
					<td>
						Community Tax No. &nbsp;&nbsp; 
						<input name="f_com_tax_nr" id="f_com_tax_nr" onblur="trimString(this)" value="<?= $f_com_tax_nr ?>" >
					</td>
					<td>&nbsp;</td>
					<td>
						Community Tax No. &nbsp;&nbsp; 
						<input name="m_com_tax_nr" id="m_com_tax_nr" onblur="trimString(this)" value="<?= $m_com_tax_nr ?>" >
					</td>
				</tr>
				<tr>
					<td>
<?php
		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
		//$phpfd=str_replace("yy","%Y", strtolower($phpfd));

	if (($f_com_tax_date!='0000-00-00') && ($f_com_tax_date!=""))
		$f_com_tax_date = @formatDate2Local($f_com_tax_date,$date_format);
	else
		$f_com_tax_date='';

	$sDateJS= '<input name="f_com_tax_date" type="text" size="15" maxlength=10 value="'.$f_com_tax_date.'"'. 
				'onFocus="this.select();" 
				id = "f_com_tax_date"
				onBlur="IsValidDate(this,\''.$date_format.'\'); "
				onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="f_com_tax_date_trigger" style="cursor:pointer" >
				<font size=3>['; 			
			ob_start();
	?>
			<script type="text/javascript">
			Calendar.setup ({
					inputField : "f_com_tax_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "f_com_tax_date_trigger", singleClick : true, step : 1
			});
			</script>
<?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();
			
		$sDateJS .= $calendarSetup;
		/**/
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDateJS = $sDateJS.$$dfbuffer.']';
#echo "$ dfbuffer ='".$dfbuffer."' &nbsp;&nbsp;";
#echo "$ $ dfbuffer ='".$$dfbuffer."' &nbsp;&nbsp;";
?>
						Date Issued &nbsp;&nbsp; <?= $sDateJS ?>
<!--
						<input name="f_com_tax_date" id="f_com_tax_date" onblur="trimString(this)" value="<?= $f_com_tax_date ?>" >
-->
					</td>
					<td>&nbsp;</td>
					<td>
<?php
		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
		//$phpfd=str_replace("yy","%Y", strtolower($phpfd));

	if (($m_com_tax_date!='0000-00-00') && ($m_com_tax_date!=""))
		$m_com_tax_date = @formatDate2Local($m_com_tax_date,$date_format);
	else
		$m_com_tax_date='';

	$sDateJS= '<input name="m_com_tax_date" type="text" size="15" maxlength=10 value="'.$m_com_tax_date.'"'. 
				'onFocus="this.select();" 
				id = "m_com_tax_date"
				onBlur="IsValidDate(this,\''.$date_format.'\'); "
				onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="m_com_tax_date_trigger" style="cursor:pointer" >
				<font size=3>['; 			
			ob_start();
	?>
			<script type="text/javascript">
			Calendar.setup ({
					inputField : "m_com_tax_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "m_com_tax_date_trigger", singleClick : true, step : 1
			});
			</script>
<?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();
			
		$sDateJS .= $calendarSetup;
		/**/
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDateJS = $sDateJS.$$dfbuffer.']';
#echo "$ dfbuffer ='".$dfbuffer."' &nbsp;&nbsp;";
#echo "$ $ dfbuffer ='".$$dfbuffer."' &nbsp;&nbsp;";
?>
						Date Issued &nbsp;&nbsp; <?= $sDateJS ?>
<!--
						<input name="m_com_tax_date" id="m_com_tax_date" onblur="trimString(this)" value="<?= $m_com_tax_date ?>" >
-->
					</td>
				</tr>
				<tr>
					<td>
						Place Issued &nbsp;&nbsp; 
						<input name="f_com_tax_place" id="f_com_tax_place" onblur="trimString(this)" value="<?= $f_com_tax_place ?>" >
					</td>
					<td>&nbsp;</td>
					<td>
						Place Issued &nbsp;&nbsp; 
						<input name="m_com_tax_place" id="m_com_tax_place" onblur="trimString(this)" value="<?= $m_com_tax_place ?>" >
					</td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
<?php
		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
		//$phpfd=str_replace("yy","%Y", strtolower($phpfd));

	if (($officer_date_sign!='0000-00-00') && ($officer_date_sign!=""))
		$officer_date_sign = @formatDate2Local($officer_date_sign,$date_format);
	else
		$officer_date_sign='';

	$sDateJS= '<input name="officer_date_sign" type="text" size="15" maxlength=10 value="'.$officer_date_sign.'"'. 
				'onFocus="this.select();" 
				id = "officer_date_sign"
				onBlur="IsValidDate(this,\''.$date_format.'\'); "
				onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="officer_date_sign_trigger" style="cursor:pointer" >
				<font size=3>['; 			
			ob_start();
	?>
			<script type="text/javascript">
			Calendar.setup ({
					inputField : "officer_date_sign", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "officer_date_sign_trigger", singleClick : true, step : 1
			});
			</script>
<?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();
			
		$sDateJS .= $calendarSetup;
		/**/
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDateJS = $sDateJS.$$dfbuffer.']';
#echo "$ dfbuffer ='".$dfbuffer."' &nbsp;&nbsp;";
#echo "$ $ dfbuffer ='".$$dfbuffer."' &nbsp;&nbsp;";
?>
					<td colspan="3" align="justify">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<b> SUBSCRIBED AND SWORN </b> to before me this 
					<?= $sDateJS ?>
					<br>
					at 
					<input name="officer_place_sign" id="officer_place_sign" size="30" onblur="trimString(this)" value="<?= $officer_place_sign ?>" >
					, Philippines.					</td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td align="center">_____________________________________________</td>
					<td>&nbsp;</td>
					<td align="center">
						<input name="officer_title" id="officer_title" onblur="trimString(this)" size="30" value="<?= $officer_title ?>" >
					</td>
				</tr>
				<tr>
					<td align="center">(Signature of Administering Officer)</td>
					<td>&nbsp;</td>
					<td align="center">(Title/Designation)</td>
				</tr>
				<tr>
					<td align="center" valign="bottom">
						<input name="officer_name" id="officer_name" onblur="trimString(this)" size="50" value="<?= $officer_name ?>" >
					</td>
					<td>&nbsp;</td>
					<td align="center">
						<textarea name="officer_address" id="officer_address" cols="50" rows="2"><?= $officer_address ?></textarea>
					</td>
				</tr>
				<tr>
					<td align="center">(Name in Print)</td>
					<td>&nbsp;</td>
					<td align="center">(Address)</td>
				</tr>
			</table>
    </td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#F9FFFB" class="style2">
      <table width="100%" border="0" cellpadding="4" cellspacing="0">
        <tr>
          <td colspan="2" bgcolor="#A0C2DA" class="style5"> ATTENDANT</td>
        </tr>
        <tr>
          <td width="35%" height="145" bgcolor="#F9FFFB">
            <table width="90%" border="0" align="center" cellpadding="0" cellspacing="1">
              <tr>
                <td bgcolor="#EAF1F7">
					 	<input type="hidden" name="attendant_type" id="attendant_type" value="<?php if ($attendant_type) echo $attendant_type.' - '.$attendant_type_others; else echo "1 - Physician"; ?>">
                  <input type="radio" name="attendant_type_tmp" id="attendant_type_tmp" onclick="typeOfAttendant(this)" value="1" <?php if (($attendant_type=='1')||(!$attendant_type)) echo "checked"; ?>>
                  1. Physician                </td>
              </tr>
              <tr>
                <td height="56" bgcolor="#EAF1F7">
                  <input type="radio" name="attendant_type_tmp" id="attendant_type_tmp" onclick="typeOfAttendant(this)" value="4" <?php if ($attendant_type=='4') echo "checked"; ?>>
                  4. Hilot (Traditional Midwife)</td>
              </tr>
            </table>
            <center>
            </center>
            </td>
          <td bgcolor="#F9FFFB">
            <table width="90%" border="0" align="center" cellpadding="0" cellspacing="1">
              <tr>
                <td bgcolor="#EAF1F7">
                  <input type="radio" name="attendant_type_tmp" id="attendant_type_tmp" onclick="typeOfAttendant(this)" value="2" <?php if ($attendant_type=='2') echo "checked"; ?>>
                  2. Nurse                </td>
                <td bgcolor="#EAF1F7">
                  <input type="radio" name="attendant_type_tmp" id="attendant_type_tmp" onclick="typeOfAttendant(this)" value="3" <?php if ($attendant_type=='3') echo "checked"; ?>>
                  3. Midwife</td>
              </tr>
              <tr>
                <td height="61" colspan="2" bgcolor="#EAF1F7">
                  <input type="radio" name="attendant_type_tmp" id="attendant_type_tmp" onclick="typeOfAttendant(this)" value="5" <?php if ($attendant_type==5) echo "checked"; ?>>
                  5. Others (Specify) &nbsp;&nbsp;
                  <input type="text" size="50" name="attendant_type_others" id="attendant_type_others" onblur="trimString(this); $('attendant_type').value= '5 - '+this.value;" value="<?php if ($attendant_type==5) echo $attendant_type_others; ?>" <?php if (($attendant_type!=5)||(!$attendant_type)) echo "disabled"; ?>>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
	  <br />
    </td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#F9FFFB">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="2%" bgcolor="#A0C2DA"><img src="images/blgry_03.gif" width="31" height="26" /></td>
    <td width="98%" bgcolor="#A0C2DA">
      <?php
	if (($birth_time !='00:00:00') && ($birth_time!="")){
		$birth_time_tmp = convert24HourTo12HourLocal($birth_time);
#		echo "birth_time_tmp ='".$birth_time_tmp."' &nbsp;&nbsp;";		
		$birth_time_array = explode(' ',$birth_time_tmp);
		$birth_time = $birth_time_array[0];
		$birth_time_period = $birth_time_array[1];
	}else{
		$birth_time = '';
	}
?>
      <span class="style5">CERTIFICATION OF BIRTH </span></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#A0C2DA">
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td bgcolor="#F9FFFB">
            <table width="100%" border="0" cellspacing="10" cellpadding="0">
              <tr>
                <td bgcolor="#FFFFFF"><span class="style2">I hereby certify that I attended the birth of the child who was born alive at &nbsp;&nbsp;
                    <input type="text" size="3" name="birth_time" id="birth_time" value="<?= $birth_time ?>" onchange="setFormatTime(this);" />
&nbsp;o'clock &nbsp;
<select id='selAMPM' name="selAMPM">
  <option value="A.M." <?php if ($birth_time_period=='AM') echo "selected"; ?>>A.M.</option>
  <option value="P.M." <?php if ($birth_time_period=='PM') echo "selected"; ?>>P.M.</option>
</select>
on the date stated above. </span></td>
              </tr>
              <tr>
                <td>
                  <table width="90%" border="0" align="center" cellspacing="2">
                    <tr>
                      <td width="50%" colspan="2" bgcolor="#EAF1F7" class="style2">Signature</td>
                      <td width="50%" rowspan="2" bgcolor="#EAF1F7" class="style2"> Address<br />
                          <textarea name="textarea" id="textarea" cols="50" rows="2"><?= $attendant_address ?>
                    </textarea>
                      </td>
                    </tr>
                    <tr>
                      <td bgcolor="#EAF1F7" class="style2"> Name in Print </td>
                      <td bgcolor="#EAF1F7" class="style2">
                        <input type="text" size="50" name="attendant_name" id="attendant_name" onblur="trimString(this)" value="<?= $attendant_name ?>" />
                      </td>
                    </tr>
                    <tr>
                      <td bgcolor="#EAF1F7" class="style2"> Title or Position </td>
                      <td bgcolor="#EAF1F7" class="style2">
                        <input type="text" size="50" name="attendant_title" id="attendant_title" onblur="trimString(this)" value="<?= $attendant_title ?>" />
                      </td>
                      <td bgcolor="#EAF1F7"> <span class="style2">
                        <?php
		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
		//$phpfd=str_replace("yy","%Y", strtolower($phpfd));

	if (($attendant_date_sign!='0000-00-00') && ($attendant_date_sign!=""))
		$attendant_date_sign = @formatDate2Local($attendant_date_sign,$date_format);
	else
		$attendant_date_sign='';

	$sDateJS= '<input name="attendant_date_sign" type="text" size="15" maxlength=10 value="'.$attendant_date_sign.'"'. 
				'onFocus="this.select();" 
				id = "attendant_date_sign"
				onBlur="IsValidDate(this,\''.$date_format.'\'); "
				onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="attendant_date_trigger" style="cursor:pointer" >
				<font size=3>['; 			
			ob_start();
	?>
                        <script type="text/javascript">
			Calendar.setup ({
					inputField : "attendant_date_sign", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "attendant_date_trigger", singleClick : true, step : 1
			});
			      </script>
                        <?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();
			
		$sDateJS .= $calendarSetup;
		/**/
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDateJS = $sDateJS.$$dfbuffer.']';
#echo "$ dfbuffer ='".$dfbuffer."' &nbsp;&nbsp;";
#echo "$ $ dfbuffer ='".$$dfbuffer."' &nbsp;&nbsp;";
?>
                        Date &nbsp;&nbsp;
                        <?= $sDateJS ?>
                      </span></td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
    </tr>
</table>
<br />
</td>
  </tr>
  <tr>
    <td colspan="2" class="style2">
      <table width="100%" border="0" cellpadding="4" cellspacing="0">
        <tr>
          <td bgcolor="#A0C2DA"><span class="style5"> INFORMANT</span></td>
        </tr>
        <tr>
          <td bgcolor="#F9FFFB">
            <table width="90%" border="0" align="center" cellspacing="2">
              <tr>
                <td width="50%" height="45" bgcolor="#EAF1F7">Signature</td>
                <td width="50%" rowspan="2" bgcolor="#EAF1F7">
                  Address <br>
		             <textarea name="informant_address" id="informant_address" cols="50" rows="2"><?= $informant_address ?></textarea>                  
                </td>
              </tr>
              <tr>
                <td height="66" bgcolor="#EAF1F7">
                  Name in Print&nbsp;&nbsp;
		             <input type="text" size="50" name="informant_name" id="informant_name" onblur="trimString(this)" value="<?= $informant_name ?>">
                </td>
              </tr>
              <tr>
                <td height="54" bgcolor="#EAF1F7">
                  Relationship to the child&nbsp;&nbsp;
                     <input type="text" size="40" name="informant_relation" id="informant_relation" onblur="trimString(this)" value="<?= $informant_relation ?>">
                </td>
                <td bgcolor="#EAF1F7">
<?php
		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
		//$phpfd=str_replace("yy","%Y", strtolower($phpfd));
	
	if (($informant_date_sign!='0000-00-00')  && ($attendant_date_sign!=""))
		$informant_date_sign = @formatDate2Local($informant_date_sign,$date_format);
	else
		$informant_date_sign='';

	$sDateJS= '<input name="informant_date_sign" type="text" size="15" maxlength=10 value="'.$informant_date_sign.'"'. 
				'onFocus="this.select();" 
				id = "informant_date_sign"
				onBlur="IsValidDate(this,\''.$date_format.'\'); "
				onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="informant_date_trigger" style="cursor:pointer" >
				<font size=3>['; 			
			ob_start();
	?>
			<script type="text/javascript">
			Calendar.setup ({
					inputField : "informant_date_sign", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "informant_date_trigger", singleClick : true, step : 1
			});
			</script>
<?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();
			
		$sDateJS .= $calendarSetup;
		/**/
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDateJS = $sDateJS.$$dfbuffer.']';
#echo "$ dfbuffer ='".$dfbuffer."' &nbsp;&nbsp;";
#echo "$ $ dfbuffer ='".$$dfbuffer."' &nbsp;&nbsp;";
?>
             Date &nbsp;&nbsp; <?= $sDateJS ?>
<!--
		             <input type="text" name="informant_date_sign" id="informant_date_sign" onblur="trimString(this)" value="<?= $informant_date_sign ?>">
-->
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td width="49%" class="style2" bgcolor="#F9FFFB">
	<br />
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
         <tr>
           <td width="32%" bgcolor="#FF6600"><span class="style18">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PREPARED BY</span></td>
           <td width="68%" bgcolor="#F9FFFB">&nbsp;</td>
         </tr>
       </table>
       <table width="100%" border="0" cellspacing="2">
        <tr>
          <td colspan="2" bgcolor="#FFF3EC">Signature</td>
        </tr>
        <tr>
          <td height="41" bgcolor="#FFF3EC">
            Name in Print &nbsp;&nbsp;</td>
          <td bgcolor="#FFF3EC">
            <input name="encoder_name" type="text" class="style2" id="encoder_name" onblur="trimString(this)" value="<?= $encoder_name ?>" size="50" />
          </td>
        </tr>
        <tr>
          <td height="41" bgcolor="#FFF3EC">
            Title or Position&nbsp;&nbsp;</td>
          <td bgcolor="#FFF3EC">
            <input name="encoder_title" type="text" class="style2" id="encoder_title" onblur="trimString(this)" value="<?= $encoder_title ?>" size="40" />
          </td>
        </tr>
        <tr>
          <td colspan="2" bgcolor="#FFF3EC">
<?php
		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
		//$phpfd=str_replace("yy","%Y", strtolower($phpfd));
	
	if (($encoder_date_sign!='0000-00-00')  && ($encoder_date_sign!=""))
		$encoder_date_sign = @formatDate2Local($encoder_date_sign,$date_format);
	else
		$encoder_date_sign='';

	$sDateJS= '<input name="encoder_date_sign" type="text" size="15" maxlength=10 value="'.$encoder_date_sign.'"'. 
				'onFocus="this.select();" 
				id = "encoder_date_sign"
				onBlur="IsValidDate(this,\''.$date_format.'\'); "
				onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="encoder_date_trigger" style="cursor:pointer" >
				<font size=3>['; 			
			ob_start();
	?>
			<script type="text/javascript">
			Calendar.setup ({
					inputField : "encoder_date_sign", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "encoder_date_trigger", singleClick : true, step : 1
			});
			</script>
<?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();
			
		$sDateJS .= $calendarSetup;
		/**/
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDateJS = $sDateJS.$$dfbuffer.']';
#echo "$ dfbuffer ='".$dfbuffer."' &nbsp;&nbsp;";
#echo "$ $ dfbuffer ='".$$dfbuffer."' &nbsp;&nbsp;";
?>
             Date &nbsp;&nbsp; <?= $sDateJS ?>          
          </td>
        </tr>
      </table>
    </td>
    <td width="51%" bgcolor="#F9FFFB" class="style2">&nbsp;</td>
  </tr>
</table>
<div align="center">
  <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td  bgcolor="#A0C2DA">
        <center>
  <?php
			if (!$birthCertInfo || empty($birthCertInfo)){
				echo '			<input type="hidden" name="mode" id="mode" value="save">'."\n";
				echo '			<input type="submit" name="Submit" value="Save">'."\n";
			}else{
				echo '			<input type="hidden" name="mode" id="mode" value="update">'."\n";
				echo '			<input type="button" name="Print" value="Print" onClick="printBirthCert('.$pid.')">'."\n &nbsp; &nbsp;";
				echo '			<input type="submit" name="Submit" value="Update">'."\n";
			}
			echo '			<input type="hidden" name="pid" id="pid" value="'.$pid.'">'."\n";
?>
  &nbsp; &nbsp;
  <input type="button" name="Cancel" value="Cancel"  onclick="window.close()" />
                </center>
        </td>
    </tr>
  </table>
</div>
</form>
<script language="javascript">
<?php
	echo $presetJavascript;
?>
</script>

</body>
</html>
