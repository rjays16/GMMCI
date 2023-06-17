<?php
	#---added by VAS
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	$srvObj=new SegLab();

	include_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;
	
	if (eregi("seg-lab-services-admin.action.php",$PHP_SELF)) 
		die('<meta http-equiv="refresh" content="0; url=../">');

	if ($_POST['action']=='addgrp') {
		
		#$dept = $dept_obj->getDeptAllInfo($dept_nr);
		#---add some code here, no duplication of data
		
		$srvObj->getServiceGroupInfo($_POST['gname'], $_POST['gcode']);
		#echo "sql = ".$srvObj->sql;
		if (($srvObj->count==0)&&($_POST['gcode']!='none')){
			#if ($srvObj->saveLabServiceGroup($_POST['gname'], $dept_nr)) {			
			$status = '';
			if ($srvObj->saveLabServiceGroup(strtoupper($_POST['gname']), strtoupper($_POST['gcode']), $_POST['goname'], $status)) {			
			#if ($srvObj->saveLabServiceGroup($_POST['gname'])) {			
				#echo "Service Group ".strtoupper($_POST['gname'])." in ".$dept['name_formal']." is successfully created";
				#<div align="center" style="font:bold 12px Tahoma; color:#990000; ">{{$sMessage}}</div><br />
				#echo "Service Group ".strtoupper($_POST['gname'])." is successfully created!";
				echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Service Group ".strtoupper($_POST['gname'])." is successfully created!</div><br />";
				echo "<script type=\"text/javascript\">window.location.href=window.location.href;</script>";
			}
			else
				#echo $srvObj->sql;
				echo $srvObj->error;
				
		#}elseif($_POST['gcode']!='none'){
		#	echo "Service Group ".strtoupper($_POST['gname'])." can't be created! Pls. enter new code";	
		}else{
			#echo "Service Group ".strtoupper($_POST['gname'])." in ".$dept['name_formal']." already exists!";
			#echo "Service Group ".strtoupper($_POST['gname'])." already exists or the code is not accepted!";
			echo "<br><div align=\"center\" style=\"font:bold 12px Tahoma; color:#990000; \">Service Group ".strtoupper($_POST['gname'])." already exists or the code is not accepted!</div><br />";
		}		
	}
	

?>