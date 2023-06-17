<?php

		/*
		* @author Raissa 05/07/2009
		* @internal re-make of the lab results
		*/

		require('./roots.php');
		include($root_path.'include/inc_environment_global.php');
		require($root_path."modules/laboratory/ajax/lab-new.common.php");
		require_once($root_path.'include/care_api_classes/class_lab_results.php');
		require_once($root_path.'include/care_api_classes/class_core.php');
		require_once($root_path.'include/care_api_classes/class_department.php');
		require_once($root_path.'include/care_api_classes/class_personell.php');
		require_once($root_path.'include/care_api_classes/class_ward.php');
		require_once($root_path.'include/care_api_classes/alerts/class_alert.php');

		define('NO_2LEVEL_CHK',1);
		$dept_obj= new Department;
		$ward_obj = new Ward;
		$pers_obj= new Personell;
		$alert_obj = new SegAlert();
		$lab_results = new Lab_Results();

		//$xajax->printJavascript($root_path.'classes/xajax');
		 $xajax->printJavascript($root_path.'classes/xajax-0.2.5');
		global $allow_labresult, $allow_labresult_read;
		global $db, $dbf_nodate;

		$refno = $_REQUEST["refno"];
?>
<script language="javascript">

function ToBeServed(group_id, refno, service_code,pid){
		var is_served;

		is_served = 1;

		//commented out by cha, july 30, 2010
		//alert("Finalizing " + refno + "...");

		xajax_saveOfficialResult(refno, group_id, is_served, service_code, pid);
		//showPdfResult(pid,refno,group_id);
		//window.parent.location = 'seg-lab-request-order-list.php?done=1&searchkey=$pid';
}
//added by VAN 08-18-2010
function ReloadWindow(pid){
	 window.parent.location = 'seg-lab-request-order-list.php?done=1&searchkey='+pid;
}

//added by VAN 04-09-10
function showPdfResult(pid,refno,group_id){
		var x = '../../modules/repgen/pdf_lab_results.php?pid='+pid+'&refno='+refno+'&group_id='+group_id;
		window.open(x,'Rep_Gen','menubar=no,directories=no');
}
</script>
<?php
		if($_GET["service_code"])
				$service_code = urlencode($_GET["service_code"]);
		else
				$service_code = urlencode($_POST["service_code"]);

		$refno = $_POST["refno"] ? $_POST["refno"]:$_GET["refno"];
		$pid = $_POST["pid"] ? $_POST["pid"]:$_GET["pid"];
		$submit = $_POST["submit"] ? $_POST["submit"]:$_GET["submit"];
		$status = $_POST["status"] ? $_POST["status"]:$_GET["status"];
		$done = $_POST["done"] ? $_POST["done"]:$_GET["done"];

		$service_code = $_POST["service_code"] ? $_POST["service_code"]:$_GET["service_code"];

		//echo "done=".$done."status=".$status."submit=".$submit."refno=".$refno."pid=".$pid;
		$med_tech_pid = $_POST["medtech"];

		/*if($_GET["group_id"])
				$group_id = $_GET["group_id"];
		else
				$group_id = $_POST["group_id"];*/
		$group_id = $_POST["group_id"] ? $_POST["group_id"] : $_GET["group_id"];

		//$group_id=8;
		$gender_var = $_REQUEST["gender_var"];
		//$allow_labresult=1;
		//$allow_labresult_read=1;

		//if(!$allow_labresult && !$allow_labresult_read && ($HTTP_SESSION_VARS["sess_permission"]!='System_Admin'))
		//if(!$allow_labresult && !$allow_labresult_read)
		if(!$allow_labresult && !$allow_labresult_read && ($HTTP_SESSION_VARS["sess_permission"]!='System_Admin'))
		{
				echo "<b>Unauthorized Page Access</b>";
		}
		else
		{
				$scode = $service_code;
				$stat= $status;
				$res = $lab_results->getLabResult($refno,$group_id);
				$patient = $lab_results->get_patient_data($refno, $group_id);
				if($patient)
						extract($patient);
				else{
					 $sql = "SELECT * from seg_walkin WHERE pid='$pid'";
					 $rs = $db->Execute($sql);
					 if($rs && $pt = $rs->FetchRow()){
							 extract($pt);
					 }
				}
				$status = $stat;
				$service_code = $scode;
				if ($pid)
						$name_patient = mb_strtoupper($name_last).", ".mb_strtoupper($name_first)." ".mb_strtoupper($name_middle);
				else
						$name_patient = "";

				if ($street_name){
						if ($brgy_name!="NOT PROVIDED")
								$street_name = $street_name.", ";
						else
								$street_name = $street_name.", ";
				}
				if ((!($brgy_name)) || ($brgy_name=="NOT PROVIDED"))
						$brgy_name = "";
				else
						$brgy_name  = $brgy_name.", ";

				if ((!($mun_name)) || ($mun_name=="NOT PROVIDED"))
						$mun_name = "";
				else{
						if ($brgy_name)
								$mun_name = $mun_name;
				}

				if ((!($prov_name)) || ($prov_name=="NOT PROVIDED"))
						$prov_name = "";
				if(stristr(trim($mun_name), 'city') === FALSE){
						if ((!empty($mun_name))&&(!empty($prov_name))){
								if ($prov_name!="NOT PROVIDED")
										$prov_name = ", ".trim($prov_name);
								else
										$prov_name = "";
						}else{
								$prov_name = "";
						}
				}else
						$prov_name = " ";
				if(empty($address))
						$address = $street_name.$brgy_name.$mun_name.$prov_name;

				if (empty($age))
						$age = "unknown";

				$encounter_type = $patient["encounter_type"];

				 if ($encounter_type==1){
						$enctype = "ERPx";
						$location = "EMERGENCY ROOM";
				 }elseif (($encounter_type==2)||($encounter_type==5)){
						 if ($encounter_type==2)
								 $enctype = "OPDx";
						 else
								 $enctype = "PHSx";

						 $dept = $dept_obj->getDeptAllInfo($current_dept_nr);
						 $location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
				 }elseif (($encounter_type==3)||($encounter_type==4)||($encounter_type==6)){
						 if ($res['encounter_type']==3)
								$enctype = "INPx (ER)";
						 elseif ($encounter_type==4)
								$enctype = "INPx (OPD)";
						 elseif ($encounter_type==6)
								$enctype = "INPx (PHS)";

						 $ward = $ward_obj->getWardInfo($current_ward_nr);
						 $location = strtoupper(strtolower(stripslashes($ward['name'])))." Rm # : ".$current_room_nr;
					}else{
							$enctype = "WPx";
							 $location = 'WALK-IN';
					}
					$result = $pers_obj->getPersonellInfo($request_doctor);
					if (trim($result["name_middle"]))
						 $dot  = ".";

					$doctor = trim($result["name_first"])." ".substr(trim($result["name_middle"]),0,1).$dot." ".trim($result["name_last"]);
					$doctor = htmlspecialchars(mb_strtoupper($doctor));
					$doctor = trim($doctor);
					if(!empty($doctor))
						$doctor = "DR. ".$doctor;

				$date = date('Y-m-d');
				$pathologist = 0;
				$med_tech = "";
				$sql = "select service_date, med_tech_pid, pathologist_pid FROM seg_lab_resultdata WHERE refno='$refno' AND group_id='$group_id'  AND (ISNULL(`status`) OR `status`!='deleted');";
				$result = $lab_results->exec_query($sql);
				if($result!=NULL && $resdata = $result->FetchRow())
				{
					 $date = substr($resdata["service_date"], 0, -9);
					 $pathologist = $resdata["pathologist_pid"];
					 $med_tech_pid = $resdata["med_tech_pid"];
				}
				$reading = "Initial Reading";
				if($done==1){
						$reading = "Official Reading";
				}
				if(strtoupper($sex)=="M")
								$gender = "is_male";
						else
								$gender = "is_female";

				if($submit=="SAVE" || $submit=="SAVE AND DONE"){
						$lab_result = array(array(), array());
						/*$sql = "SELECT p.*
										FROM seg_lab_result_groupparams as gp
										LEFT JOIN seg_lab_result_params as p ON p.service_code = gp.service_code
										WHERE gp.group_id=$group_id AND $gender=1
										ORDER BY gp.order_nr, p.order_nr ASC";*/
						/*if($group_id=="0" || $group_id==""){
								$sql = "SELECT p.*, r.result_value, r.unit, s.name as group_name
												FROM seg_lab_result_params AS p
												LEFT JOIN seg_lab_services AS s ON s.service_code = p.service_code
												LEFT JOIN seg_lab_result AS r ON r.param_id = p.param_id AND r.refno='$refno' AND (ISNULL(r.status) OR r.status!='deleted')
												WHERE $gender=1 AND s.service_code='$service_code' ORDER BY p.order_nr ASC";
						}
						else{
								$sql = "SELECT p.*, r.result_value, r.unit, pg.name as group_name, gp.order_nr as order2
												FROM seg_lab_result_groupparams as gp
												LEFT JOIN seg_lab_result_params as p ON p.service_code = gp.service_code
												LEFT JOIN seg_lab_result as r ON p.param_id = r.param_id AND r.refno='$refno' AND (ISNULL(r.status) OR r.status!='deleted')
												LEFT JOIN seg_lab_result_paramgroups as pg ON pg.param_group_id = p.param_group_id
												WHERE gp.group_id=$group_id AND $gender=1 AND (ISNULL(p.status) OR p.status NOT IN ('deleted'))
												UNION SELECT p.*, r.result_value, r.unit, pg.name as group_name, gp.order_nr as order2
												FROM seg_lab_result_groupparams as gp
												LEFT JOIN seg_lab_result_group as g ON g.service_code = gp.service_code
												LEFT JOIN seg_lab_result_params as p ON p.service_code = g.service_code_child
												LEFT JOIN seg_lab_result as r ON p.param_id = r.param_id AND r.refno='$refno' AND (ISNULL(r.status) OR r.status!='deleted')
												LEFT JOIN seg_lab_result_paramgroups as pg ON pg.param_group_id = p.param_group_id
												WHERE gp.group_id=$group_id AND $gender=1 AND (ISNULL(p.status) OR p.status NOT IN ('deleted'))
												ORDER BY order2, order_nr ASC";
						}*/
						$sql = "SELECT * FROM \n".
																"(SELECT d.service_code,pa.param_id,gp.order_nr AS `group_order`, pa.order_nr AS `param_order`, \n".
																"p.name, p.param_group_id,pg.name AS `group_name`, p.is_numeric, p.is_boolean, p.is_longtext, p.SI_unit, p.SI_lo_normal, \n".
																"p.SI_hi_normal, p.CU_unit, p.CU_lo_normal, p.CU_hi_normal, p.is_female, p.is_male, p.is_time, p.is_multiple_choice, p.is_table, \n".
																"r.result_value, r.unit \n".
																"FROM seg_lab_result_params AS p \n".
																"LEFT JOIN seg_lab_result_param_assignment AS pa ON p.param_id=pa.param_id \n".
																"LEFT JOIN seg_lab_result_groupparams AS gp ON p.group_id=gp.group_id AND pa.service_code=gp.service_code \n".
																"LEFT JOIN seg_lab_result_paramgroups AS pg ON pg.param_group_id=p.param_group_id \n".
																"LEFT JOIN seg_lab_servdetails AS d ON d.service_code=pa.service_code AND d.refno='$refno'\n".
																"LEFT JOIN seg_lab_result AS r ON d.refno=r.refno AND r.param_id=pa.param_id AND r.status <> 'deleted' \n".
																"WHERE p.status <> 'deleted' AND p.group_id='$group_id'\n".
																" ORDER BY gp.order_nr, pa.order_nr) a \n".
														"GROUP BY a.param_id, a.param_group_id \n".
														"ORDER BY param_order,group_order";
						//echo $sql;
						$result = $lab_results->exec_query($sql);

						if($result)
						{
								//for($i=0; $val=$result->FetchRow();$i++)
								$i=0;
								while($val = $result->FetchRow())
								{
										$tmp = $val["name"];
										$lab_result[0][$i] = $val["name"];
										$tmp = str_replace(" ", "_", $tmp);
										$tmp = str_replace(".", "_", $tmp);
										//$tmp = str_replace(":", "_", $tmp);
										if($_POST[$tmp])
												$num = $i;
										$lab_result[1][$i] = $_POST[$tmp];
										#echo $val["name"]. " ". $val["param_id"]." ".$_POST[$tmp]."<br>";
										$tmp = $tmp."unit";
										$lab_result[2][$i] = $_POST[$tmp];

										$i++;
										$service_code = $val["service_code"];
								}
								/*echo "<pre>";
								print_r($lab_result);
								echo "</pre>";*/
								//die("end");
								if($status=="add")
								{
									 $db->StartTrans();

									 if(isset($_POST["is_confidential"]))
										 $conf = 1;
									 else
										 $conf = 0;
									// echo "refno=".$refno."group_id=".$group_id."date=".$_POST["date"]."medtech=".$med_tech_pid."patho=".$_POST["patholigist"]."conf=".$conf."serv_code=".$service_code."<br>";
									 $bSuccess = $lab_results->add_lab_resultdata($refno, $group_id, $_POST["date"], $med_tech_pid, $_POST["pathologist"],$conf,$service_code);
									 if($bSuccess)
									 {
										// echo "lab_result=".$lab_result."refno=".$refno."gender=".$gender."group_id=".$group_id."service_code=".$service_code."<br>";
										 $bSuccess = $lab_results->add_lab_results($lab_result, $refno, $gender, $group_id, $service_code);
										 if (!$bSuccess) {
												 $db->FailTrans();
												 echo "Error in adding data"."<br>";
												 echo "error@add_lab_results:".$lab_results->getErrorMsg()."SQL=".$lab_results->sql."<br>";
										 }
										 else{
												 $db->CompleteTrans();
												 $status="edit";
												 echo "successful save!";
										 }
									 }else
									 {
										 echo "error@add_lab_resultdata:".$lab_results->getErrorMsg()."SQL=".$lab_results->sql."<br>";
									 }

								}
								else if($status=="edit")
								{
									 $db->StartTrans();

									 if(isset($_POST["is_confidential"]))
										 $conf = 1;
									 else
										 $conf = 0;

									 $bSuccess = $lab_results->update_lab_resultdata($refno, $group_id, $_POST["date"], $med_tech_pid, $_POST["pathologist"],$conf,$service_code);
									 if($bSuccess){
										 $bSuccess = $lab_results->update_lab_results($lab_result, $refno, $gender, $group_id, $service_code);
										 if (!$bSuccess) {
												 $db->FailTrans();
												 echo "<br>Error in update";
												 echo "<br>error@update_lab_results:".$lab_results->getErrorMsg()."SQL=".$lab_results->sql."<br>";
										 }
										 else{
												 $db->CompleteTrans();
										 }
									 }else
									 {
											echo "error@update_lab_resultdata:".$lab_results->getErrorMsg()."SQL=".$lab_results->sql."<br>";
											$db->FailTrans();
									 }
								}
								//echo "code?".$service_code;
								//die("end");
								if($submit=="SAVE AND DONE" && $bSuccess){
										#$alert_obj->postAlert('LAB', 10, '', $name_patient." (".strtoupper($lab_results->get_group_name($group_id)).")", 'Laboratory result '.$status.'ed... (Official Result)', 'h', '');
										echo "<script type='text/javascript'> ToBeServed('$group_id','$refno','$service_code'); </script>";                    //</script>";
								}
								elseif($bSuccess){
										$alert_obj->postAlert('LAB', 10, '', $name_patient." (".strtoupper($lab_results->get_group_name($group_id)).")", 'Laboratory result '.$status.'ed... (Unofficial Result)', 'h', '');
								}
						}
						else{
							 echo "Failed to save.";
						}
				}
				elseif($submit=="DELETE")
				{
						$reason = $_POST['reason'];
						$db->StartTrans();
						$bSuccess = $lab_results->delete_lab_resultdata($refno, $group_id, $reason);
						if($bSuccess) $bSuccess = $lab_results->delete_lab_results($refno, $group_id, $gender);
						if (!$bSuccess){
								$db->FailTrans();
								echo "Error in deletion";
						}
						else{
								$db->CompleteTrans();
								echo "<script type='text/javascript'>window.parent.location = 'seg-lab-request-order-list.php?user_origin=lab&done=0&checkintern=1';</script>";
						}
				}
				elseif($submit=="VIEW PDF"){
						//$x = $root_path.'modules/laboratory/pdf_lab_results.php?pid='.$pid.'&refno='.$refno.'&group_id='.$group_id.'&service_code='.$service_code;
						$x = $root_path.'modules/repgen/pdf_lab_results.php?pid='.$pid.'&refno='.$refno.'&group_id='.$group_id.'&service_code='.$service_code;
						echo "<script type='text/javascript'>window.open('$x','Rep_Gen','menubar=no,directories=no');</script>";
				}
				$rd = "";
				$rd2 = "";
				if($done==1 || $allow_labresult_read)
				{
						$rd="readonly='readonly'";
						$rd2="disabled='disabled'";
				}
				$status = $stat;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo strtoupper($lab_results->get_group_name($group_id)); ?></title>
<style type="text/css">
<!--
.style2 {
		font-size: 12px;
		font-family: Verdana, Arial, Helvetica, sans-serif;
}
-->
</style>
<script type="text/javascript" src="<?= $root_path ?>datepickercontrol.js"></script>
<link type="text/css" rel="stylesheet" href="<?= $root_path ?>datepickercontrol.css">
<link rel="stylesheet" href="labresult.css" type="text/css">
<style type="text/css">
<!--
body {
		/*margin-top: 40px;*/
		background-color: white;
}
.style7 {color: #51622F}
.style8 {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
}
-->
</style>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript" >
<!--

function CheckFields(){
		var retval = false;
		var elements = document.getElementById('parameters').getElementsByTagName("input");
		for(var i=0; i<elements.length; i++){
				if(elements[i].readOnly==false){
						if(elements[i].value!='')
								retval=true;
				}
		}
		var elements = document.getElementById('parameters').getElementsByTagName("textarea");
		for(var i=0; i<elements.length; i++){
				if(elements[i].readOnly==false){
						if(elements[i].value!='')
								retval=true;
				}
		}
		if(!retval)
				alert("Please enter data in at least one field!");
		return retval;
}

function ConfirmDone(){

		if(CheckFields()){
				var answer = confirm("Are you sure that the request is already done? It can't be undone. \n Click OK if YES, otherwise CANCEL.");

				if(answer)
						return true;
				else
						return false;
		}
		return false;
}

function OnDelete()
{
		var answer = confirm("Are you sure that you want to delete data?\n Click OK if YES, otherwise CANCEL.");
		if(answer)
		{
				var answer = prompt ('Reason:','');
				if(answer)
				{
						var x = document.getElementById('reason');
						x.value = answer;
						return true;
				}
				else
						return false;
		}
		else
				return false;
}

function compNormal(val, si_lo, si_hi, si_unit, cu_lo, cu_hi, cu_unit, par_id){
		str = "reading"+par_id;
		val = val.replace(/[_]/g,' ');
		unit_str = val+"unit";
		txtVal = document.getElementById(val);
		txtUnit = document.getElementById(unit_str);
		if(txtUnit || si_lo || si_hi || cu_lo || cu_hi){
				if(txtUnit){
						if(txtUnit.value==cu_unit){
								lo = cu_lo;
								hi = cu_hi;
						}
						else{
								lo = si_lo;
								hi = si_hi;
						}
				}
				else{
						lo = si_lo;
						hi = si_hi;
				}
				if(lo || hi){
						if(txtVal.value){
								/*alert(parseFloat(lo) + " " + parseFloat(txtVal.value));*/
								if(parseFloat(txtVal.value) < parseFloat(lo)){
										document.getElementById(str).innerHTML = "<font color=red>LOW</font>";
								}
								else if(parseFloat(txtVal.value) > parseFloat(hi))
										document.getElementById(str).innerHTML = "<td><font color=red>HIGH</font></td>";
								else
										document.getElementById(str).innerHTML = "<td><font color=blue>NORMAL</font></td>";
						}
				}
		}
}

-->
</script>
</head>
<body>
<form action="lab_results.php" method="post">
<input type="hidden" name="reason" id="reason">
<input type="hidden" name="pid" value="<?= $pid ?>" >
<input type="hidden" name="refno" value="<?= $refno ?>" >
<input type="hidden" name="med_tech_pid" value="<?= $med_tech_pid ?>" >
<input type="hidden" name="done" value="<?= $done ?>" >
<table width="80%" border="0" align="center" cellpadding="1" cellspacing="0" class="carlpanel">
	<tr>
		<td><table width="100%" border="0" align="center" cellpadding="1" cellspacing="0">
			<tr>
				<td width="51%" class="carlPanelHeader"><div align="left"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo strtoupper($lab_results->get_group_name($group_id)); ?></b></div></td>
			</tr>
			<tr>
				<td valign="top" bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="0" cellpadding="1" >
						<tr>
							<td height="149" valign="top" bgcolor="#FFFFFF" class="carlpanel"><table width="100%" border="0" cellpadding="0" cellspacing="2">
									<tr>
										<td width="54%"><table height="64" border="0" cellpadding="1" cellspacing="4" class="carlpanel">
												<tr>
													<td class="carlPanelHeader">Name</td>
													<td><input name="patient_name" id="patient_name" size="60%" type="text" value="<?= $name_patient ?>" readonly="readonly"/>                          </td>
												</tr>
												<tr>
													<td class="carlPanelHeader" width="40%">Address</td>
													<td width="60%"><input name="address" id="address" type="text"  value="<?= $address ?>" readonly="readonly" size="60%" /></td>
												</tr>
												<tr>
													<td width="10%" class="carlPanelHeader">Ward</td>
													<td><input name="location" id="location" type="text" value="<?= $location ?>" readonly="readonly" size="60%"/></td>
												</tr>
										</table></td>
										<td width="46%"><table height="64" border="0" cellpadding="1" cellspacing="5" class="carlpanel">
												<tr>
													<td height="26" class="carlPanelHeader">Age</td>
													<td width="34%"><input name="age" id="age" type="text" size="5"  value="<?= $age ?>" readonly="readonly"/></td>
													<td width="16%" Class="carlPanelHeader">Date</td>
													<td><input name="date" type="text" size="8" value="<?= $date ?>" readonly="readonly"/></td>
												</tr>
												<tr>
													<td width="33%" height="24" Class="carlPanelHeader">Sex</td>
													<td colspan="2"><select name="select" disabled="disabled">
														<option value="Male" <? if($sex=="m") echo "selected='selected'"?>>Male</option>
														<option value="Female"<? if($sex=="f") echo "selected='selected'"?>>Female</option>
													</select></td>
													<td width="17%">&nbsp;</td>
												</tr>
												<tr>
													<td width="10%" class="carlPanelHeader">Physician</td>
													<td colspan=3><input name="textfield252" type="text" class="style2"  value="<?= $doctor ?>" readonly="readonly"  size="40%"/></td>
												</tr>
										</table></td>
									</tr>
									<tr>
										<td colspan="2">&nbsp;</td>
									</tr>
									<tr>
										<td colspan="2" bgcolor="#FFFFFF"  >
										<table width="100%" border="1" cellpadding="1" cellspacing="2" id="parameters" >
										<?php
												//get service codes for this request
												$services_query = "SELECT service_code FROM seg_lab_servdetails WHERE refno='$refno'";
												//echo "<br/>sql-".$services_query;
												$result = $db->Execute($services_query);
												//$serviceCodes = array();
												$serviceCodes = "";
												$count = $result->RecordCount();
												while($row=$result->FetchRow()){
													//$serviceCodes[] = $row["service_code"];
													$count--;
													if($count!=0)
														$serviceCodes.="".$db->qstr($row["service_code"]).",";
													else
														$serviceCodes.="".$db->qstr($row["service_code"]);
												}
												//echo "code=".$serviceCodes;
												/*echo "<pre>";
												print_r($serviceCodes);
												echo "</pre>"; */

												//start printing of parameters for the form
												$group_name = "";
												$with_normal = FALSE;
												$all = 3;
												if($group_id==0 || $group_id=="")
														$sql = "SELECT * FROM seg_lab_result_params WHERE service_code=$service_code
																AND (NOT ((ISNULL(SI_lo_normal) OR SI_lo_normal='') AND (ISNULL(SI_hi_normal) OR SI_hi_normal=''))
																OR NOT ((ISNULL(CU_lo_normal) OR CU_lo_normal='') AND (ISNULL(CU_hi_normal) OR CU_hi_normal='')))";
												else
														$sql = "SELECT * FROM seg_lab_result_params WHERE group_id=$group_id
																AND (NOT ((ISNULL(SI_lo_normal) OR SI_lo_normal='') AND (ISNULL(SI_hi_normal) OR SI_hi_normal=''))
																OR NOT ((ISNULL(CU_lo_normal) OR CU_lo_normal='') AND (ISNULL(CU_hi_normal) OR CU_hi_normal='')))";
												$result = $lab_results->exec_query($sql);
												if($result!=NULL){
														$with_normal = TRUE;
														$all = 6;
												}
												echo "<td colspan=3 class='carlpanel' style='font-size:12px' align='left'><b>RESULT</b></td>";
												if($with_normal){
														echo "<td class='carlpanel' style='font-size:12px' align='center'><b>FINDING</b></td><td class='carlpanel' style='font-size:12px' align='center'><b>SI NORMAL VALUES</b></td><td class='carlpanel' style='font-size:12px' align='center'><b>CU NORMAL VALUES</b></td>";
												}

												/*if($group_id==0 || $group_id==""){
														$sql = "SELECT p.*, r.result_value, r.unit, s.name as group_name
																		FROM seg_lab_result_params AS p
																		LEFT JOIN seg_lab_services AS s ON s.service_code = p.service_code
																		LEFT JOIN seg_lab_result AS r ON r.param_id = p.param_id AND r.refno='$refno' AND (ISNULL(r.status) OR r.status!='deleted')
																		WHERE $gender=1 AND s.service_code='$service_code' ORDER BY p.order_nr ASC";
												}
												else{
														$sql = "SELECT p.*, r.result_value, r.unit, pg.name as group_name, gp.order_nr as order2, IF(ISNULL(d.refno), 0, 1) AS enabled\n".
																		"FROM seg_lab_result_groupparams as gp\n".
																		"INNER JOIN seg_lab_result_params as p ON p.service_code = gp.service_code AND p.$gender=1 AND (ISNULL(p.status) OR p.status NOT IN ('deleted'))\n".
																		"LEFT JOIN seg_lab_result as r ON p.param_id = r.param_id AND r.refno='$refno' AND (ISNULL(r.status) OR r.status!='deleted')\n".
																		"LEFT JOIN seg_lab_result_paramgroups as pg ON pg.param_group_id = p.param_group_id\n".
																		"LEFT JOIN seg_lab_servdetails AS d ON d.service_code=p.service_code AND d.refno='$refno'\n".
																		"WHERE gp.group_id=$group_id \n".
																		"UNION SELECT p.*, r.result_value, r.unit, pg.name as group_name, gp.order_nr as order2, IF(ISNULL(d.refno), 0, 1) AS enabled\n".
																		"FROM seg_lab_result_groupparams as gp\n".
																		"INNER JOIN seg_lab_result_group as g ON g.service_code = gp.service_code\n".
																		"LEFT JOIN seg_lab_result_params as p ON p.service_code = g.service_code_child AND p.$gender=1 AND (ISNULL(p.status) OR p.status NOT IN ('deleted'))\n".
																		"LEFT JOIN seg_lab_result as r ON p.param_id = r.param_id AND r.refno='$refno' AND (ISNULL(r.status) OR r.status!='deleted')\n".
																		"LEFT JOIN seg_lab_result_paramgroups as pg ON pg.param_group_id = p.param_group_id\n".
																		"LEFT JOIN seg_lab_servdetails AS d ON (d.service_code=g.service_code OR d.service_code=p.service_code) AND d.refno='$refno'\n".
																		"WHERE gp.group_id=$group_id \n".
																		"ORDER BY order2, order_nr ASC";
												}*/
												$sql = "SELECT * FROM \n".
																"(SELECT pa.param_id,gp.order_nr AS `group_order`, pa.order_nr AS `param_order`, \n".
																"p.name, p.param_group_id,pg.name AS `group_name`, p.is_numeric, p.is_boolean, p.is_longtext, p.SI_unit, p.SI_lo_normal, \n".
																"p.SI_hi_normal, p.CU_unit, p.CU_lo_normal, p.CU_hi_normal, p.is_female, p.is_male, p.is_time, p.is_multiple_choice, p.is_table, \n".
																"r.result_value, r.unit \n".
																"FROM seg_lab_result_params AS p \n".
																"LEFT JOIN seg_lab_result_param_assignment AS pa ON p.param_id=pa.param_id \n".
																"LEFT JOIN seg_lab_result_groupparams AS gp ON p.group_id=gp.group_id AND pa.service_code=gp.service_code \n".
																"LEFT JOIN seg_lab_result_paramgroups AS pg ON pg.param_group_id=p.param_group_id \n".
																"LEFT JOIN seg_lab_servdetails AS d ON d.service_code=pa.service_code AND d.refno='$refno'\n".
																"LEFT JOIN seg_lab_result AS r ON d.refno=r.refno AND r.param_id=pa.param_id AND r.status <> 'deleted' \n".
																"WHERE p.status <> 'deleted' AND p.group_id='$group_id' /*AND d.service_code='$service_code'*/ \n".
																" ORDER BY gp.order_nr, pa.order_nr) a \n".
														"GROUP BY a.param_id, a.param_group_id \n".
														"ORDER BY param_order,group_order";
												//echo $group_id."<br>";
												//echo $service_code."<br>";
												#echo $sql;

												$result = $lab_results->exec_query($sql);
												while($result!=NULL && $value = $result->FetchRow())
												{
													$exist_query = "SELECT EXISTS(SELECT * FROM seg_lab_result_param_assignment WHERE \n".
													" param_id=".$db->qstr($value["param_id"])." AND service_code IN (".$serviceCodes.")) as `enable`";
													$if_exists = $db->GetOne($exist_query);

														if($group_id==0){
																$rd = "";
																$rd2 = "";
														}
														//elseif($value["enabled"]=="1"){
														elseif($if_exists=="1"){
																$rd = "";
																$rd2 = "";
														}
														else
														{
																$rd="readonly='readonly'";
																$rd2="disabled='disabled'";
														}

														//echo "<br/>paramid=".$value["param_id"]." ||rd=".$rd."|| rd2=".$rd2;
														$findings = "";
														$fld_value = $value["result_value"];
														#echo "<br>".$value["name"]." = ".$fld_value;
														if($fld_value!="")
																$status = "edit";
														$unit = $value["unit"];
														$tmp = "'". $value["name"] ."'";
														$tmp = str_replace(" ", "_", $tmp);
														$tmp2 = $value["param_id"];
														echo "<tr>";
														$td="";
														if($value["group_name"]!="" && $group_name != $value["group_name"]){
																$group_name = $value["group_name"];
															 echo "<td colspan=$all class='carlpanel' style='font-size:12px' align=left><b>".strtoupper($group_name)."</b></td></tr><tr>";
														}
														if($value["group_name"]!=""){
																$td .= "&nbsp;&nbsp;&nbsp;&nbsp;";
														}
														if($value["is_boolean"]=="1"){
																if($fld_value=="on")
																		echo "<td colspan=$all-2 class='carlpanel' style='font-size:12px' $rd>".$td."<input type=checkbox name='". $value["name"] ."' id='". $value["name"] ."' checked='true'>". $value["name"] ."</td>";
																else
																		echo "<td colspan=$all-2 class='carlpanel' style='font-size:12px' $rd>".$td."<input type=checkbox name='". $value["name"] ."'>". $value["name"] ."</td>";
														}
														else{
																echo "<td colspan=2 class='carlpanel' style='font-size:12px' $rd><b>".$td.$value["name"]."</b></td>";
														}
														if($value["is_numeric"]=="1"){
if($value["SI_unit"] || $value["CU_unit"]){
																		$unit_select = "<select name='".$value["name"]."unit' id='".$value["name"]."unit' onchange=compNormal($tmp,'".$value["SI_lo_normal"]."','".$value["SI_hi_normal"]."','".$value["SI_unit"]."','".$value["CU_lo_normal"]."','".$value["CU_hi_normal"]."','".$value["CU_unit"]."',$tmp2) $rd>";
																if($value["SI_unit"])
{
																				if($unit==$value["SI_unit"])
																						$unit_select .= "<option value='".$value["SI_unit"]."' selected='selected'>".$value["SI_unit"]."</option>";
																				else
																						$unit_select .= "<option value='".$value["SI_unit"]."'>".$value["SI_unit"]."</option>";
																}
if($value["CU_unit"])
																		{
																				if($unit==$value["CU_unit"])
																						$unit_select .= "<option value='".$value["CU_unit"]."' selected='selected'>".$value["CU_unit"]."</option>";
																				else
																						$unit_select .= "<option value='".$value["CU_unit"]."'>".$value["CU_unit"]."</option>";
																		}
																		$unit_select .= "</select>";
}
																else
																		$unit_select="";
																echo "<td class='carlpanel'><input type=text name='". $value["name"] ."'  id='". $value["name"] ."' value='$fld_value' size=8 onblur=compNormal($tmp,'".$value["SI_lo_normal"]."','".$value["SI_hi_normal"]."','".$value["SI_unit"]."','".$value["CU_lo_normal"]."','".$value["CU_hi_normal"]."','".$value["CU_unit"]."',$tmp2) $rd>$unit_select</td>";
														}
														elseif($value["is_time"]=="1"){
																echo "<td class='carlpanel'><input type=text name='". $value["name"] ."'  id='". $value["name"] ."' value='$fld_value' size=3 $rd></td>";
														}
														elseif($value["is_multiple_choice"]=="1"){
																//echo "<td colspan=2 class='carlpanel' style='font-size:12px'>".$td.$value["name"]."</td><td class='carlpanel'><input type=text name='". $value["name"] ."'  id='". $value["name"] ."' value='' size=8></td>";
														}
														elseif($value["is_longtext"]=="1"){
																echo "<td colspan=$all-2 class='carlpanel' style='font-size:12px'>".$td."<textarea cols=30 name='". $value["name"] ."' id='". $value["name"] ."' $rd>$fld_value</textarea></td>";
														}
														elseif($value["is_table"]=="1"){
																//echo "<td colspan=2 class='carlpanel' style='font-size:12px'>".$td.$value["name"]."</td><td class='carlpanel'><input type=text name='". $value["name"] ."'  id='". $value["name"] ."' value='' size=8></td>";
														}
														elseif($value["is_boolean"]!="1"){
																echo "<td class='carlpanel'><input type=text name='". $value["name"] ."'  id='". $value["name"] ."' value='$fld_value' size=8 $rd></td>";
														}
														if($with_normal){
																$readtmp = "reading".$value["param_id"];
																if($fld_value!="" && ($value["CU_lo_normal"]!="" || $value["CU_hi_normal"]!="" || $value["SI_lo_normal"]!="" || $value["SI_hi_normal"]!="")){
																		//echo "'$unit' , '".$value["CU_unit"]."', '".$value["CU_unit"]."'";
																		if($unit!=""){
																				if($unit==$value["CU_unit"]){
																						if($fld_value < $value["CU_lo_normal"])
																								$findings = "<font color=red>LOW</font>";
																						elseif($fld_value > $value["CU_hi_normal"])
																								$findings = "<font color=red>HIGH</font>";
																						else
																								$findings = "<font color=blue>NORMAL</font>";
																				}
																				else{
																						if($fld_value < $value["SI_lo_normal"])
																								$findings = "<font color=red>LOW</font>";
																						elseif($fld_value > $value["SI_hi_normal"])
																								$findings = "<font color=red>HIGH</font>";
																						else
																								$findings = "<font color=blue>NORMAL</font>";
																				}
																		}
																		else{
																				if($fld_value < $value["SI_lo_normal"])
																						$findings = "<font color=red>LOW</font>";
																				elseif($fld_value > $value["SI_hi_normal"])
																						$findings = "<font color=red>HIGH</font>";
																				else
																						$findings = "<font color=blue>NORMAL</font>";
																		}
																}
																echo "<td class='carlpanel' style='font-size:12px' id='$readtmp'>$findings</td>";
if($value["SI_lo_normal"]!=""){
																		if($value["SI_hi_normal"]!="")
																				echo "<td class='carlpanel' style='font-size:12px' $rd>".$value["SI_lo_normal"]."-".$value["SI_hi_normal"]." ".$value["SI_unit"]."</td>";
																		else
																				echo "<td class='carlpanel' style='font-size:12px' $rd> >=".$value["SI_lo_normal"]." ".$value["SI_unit"]."</td>";
																}
																elseif($value["SI_hi_normal"]!="")
																		echo "<td class='carlpanel' style='font-size:12px' $rd> <".$value["SI_hi_normal"]." ".$value["SI_unit"]."</td>";
																else
																		echo "<td class='carlpanel' style='font-size:12px' $rd></td>";
																if($value["CU_lo_normal"]!=""){
																		if($value["CU_hi_normal"]!="")
																				echo "<td class='carlpanel' style='font-size:12px' $rd>".$value["CU_lo_normal"]."-".$value["CU_hi_normal"]." ".$value["CU_unit"]."</td>";
																		else
																				echo "<td class='carlpanel' style='font-size:12px' $rd> >=".$value["CU_lo_normal"]." ".$value["CU_unit"]."</td>";
																}
																elseif($value["CU_hi_normal"]!="")
																		echo "<td class='carlpanel' style='font-size:12px' $rd> <".$value["CU_hi_normal"]." ".$value["CU_unit"]."</td>";
																else
																		echo "<td class='carlpanel' style='font-size:12px' $rd></td>";
														}
														echo "</tr>";
												}
									?>
										<tr><td colspan=2 class='carlpanel' style='font-size:12px'><b>Remarks</b></td><td colspan=5 class='carlpanel' style='font-size:12px'><b><?=strtoupper($reading)?></b></td></tr>
									</table>
									<table width="100%" border="0" cellpadding="1" cellspacing="2" >
												<tr height=18></tr>
												<tr>
													<td width="30%" class="carlPanelHeader">Mark these results as confidential? </td>
													<?php
													#die($is_confidential);
													$res = $lab_results->getLabResult($refno,$group_id);
													$is_confidential = $res['is_confidential'];
													 $chkd = " ";
													if($is_confidential)
														$chkd = "checked=checked";
													?>
													<td width="70%" class="carlpanel"><input type="checkbox" <?=$chkd?> name="is_confidential" id="is_confidential" />
													</tr>
													<tr height=18></tr>
																<tr>
															<td width="30%" class="carlPanelHeader">Medical Technologist </td>

															<td width="70%" class="carlpanel">
																<select name="medtech" id="medtech" <?php echo $rd;?>>
																<?php
#edited by VAN 03-30-10
																	$sql = "SELECT pr.pid,
																						CONCAT(IF(ISNULL(trim(cp.name_last)), '', CONCAT(trim(cp.name_last), ', ')),IF(ISNULL(trim(cp.name_first)), '', CONCAT(trim(cp.name_first), ' ')), IF(ISNULL(trim(cp.name_middle)), '', CONCAT(substring(trim(cp.name_middle),1,1), '. '))) as name
																						FROM care_person AS cp
																						INNER JOIN care_personell AS pr ON cp.pid = pr.pid
																						INNER JOIN care_personell_assignment AS a ON a.personell_nr=pr.nr
																				WHERE ((pr.job_position LIKE '%medical technologist%') OR pr.job_function_title='Medical Technologist')
																				AND (a.date_end='$dbf_nodate' OR a.date_end>='".date('Y-m-d')."')
																				AND a.status NOT IN ('deleted','hidden','inactive','void')";
																	//echo $sql;
 #die(print_r($HTTP_SESSION_VARS));
																	 $result = $lab_results->exec_query($sql);
																	 while($result!=NULL && $x = $result->FetchRow())
																	 {
																			 //echo $x["pid"]." ".$med_tech;
																			 if($x["pid"]==$med_tech_pid)
																					 $tmp = "selected='selected'";
elseif($HTTP_SESSION_VARS["sess_user_pid"]==$x["pid"])
																			 $tmp = "selected='selected'";
																	 else
																			 $tmp="";

																	 echo "<option value='". $x["pid"] ."' ". $tmp .">". mb_strtoupper($x["name"]) ."</option>";
															 }
														?>
														</select>
												</td>
												</tr>
												<tr>
													<td class="carlPanelHeader">Pathologist</td>
													<td class="carlpanel">
													<select name="pathologist" id="pathologist" <?php echo $rd;?>>
													<?php
#edited by VAN 01-09-10
														$sql = "SELECT pr.pid,
																		CONCAT(IF(ISNULL(trim(cp.name_last)), '', CONCAT(trim(cp.name_last), ', ')),IF(ISNULL(trim(cp.name_first)), '', CONCAT(trim(cp.name_first), ' ')), IF(ISNULL(trim(cp.name_middle)), '', CONCAT(substring(trim(cp.name_middle),1,1), '. '))) as name
																		FROM care_person AS cp
																		INNER JOIN care_personell AS pr ON cp.pid = pr.pid
																		INNER JOIN care_personell_assignment AS a ON a.personell_nr=pr.nr
																		WHERE (pr.job_function_title LIKE '%pathologist%'
																		OR pr.job_position LIKE '%pathologist%')
																		AND (a.date_end='$dbf_nodate' OR a.date_end>='".date('Y-m-d')."')
																		AND a.status NOT IN ('deleted','hidden','inactive','void')";

														$result = $lab_results->exec_query($sql);
														while($result!=NULL && $x = $result->FetchRow())
														{
															 if($x["pid"]==$pathologist)
																	 $tmp = "selected='selected'";
															 else
																	 $tmp="";

															 echo "<option value='". $x["pid"] ."' ". $tmp .">". mb_strtoupper($x["name"]) ."</option>";
														}
												?>
														</select></td>
												</tr>
										</table></td>
									</tr>
							</table></td>
						</tr>
				</table></td>
			</tr>
			<tr>
				<input type="hidden" name="status" id="status" value="<?= $status ?>" >
				<input type="hidden" name="group_id" id="group_id" value="<? echo $group_id; ?>" >
				<input type="hidden" name="gender_var" id="gender_var" value="<?= $gender_var ?>" >
				<input type="hidden" name="group_code"  id="group_code" value="<?= $group_code ?>" >
				<input type="hidden" name="service_code" id="service_code" value="<?= $service_code ?>" >
				<td height="26" align=center class="carlPanelHeader">
			 <?php
						if($done==0)
{?>
						&nbsp;<input type="image" value="SAVE" src="../../images/btn_save.gif" name="submit" onClick="javascript: return CheckFields();"/>
					&nbsp;&nbsp;
					<input type="image" value="SAVE AND DONE" src="../../images/btn_done.gif" name="submit" onClick="javascript: return ConfirmDone();"/>
					&nbsp;&nbsp;
					<a href='seg-lab-request-order-list.php?done=0&searchkey=<?=$pid?>>' target="contframe"><img src="../../images/his_cancel_button.gif" border="0"></img></a>
					&nbsp;&nbsp;<?php if($status=="edit"){?>
					<input type="image" value="DELETE" src="../../images/btn_delete.gif" name="submit" onClick="javascript:return OnDelete();" target="contframe"/>
					<?php }} ?>&nbsp;&nbsp;
					<input type="image" value="VIEW PDF" src="../../images/btn_printpdf.gif" name="submit" />
			</tr>
		</table></td>
	</tr>
	<tr>
</table>
</form>
</body>
</html>
<?php
		}
?>
