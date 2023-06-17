<?php
	#---added by VAS
	require_once($root_path.'include/care_api_classes/class_radiology.php');
	$srvObj=new SegRadio();

	include_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;


	if (eregi("seg-radio-services-admin.action.php",$PHP_SELF)) 
		die('<meta http-equiv="refresh" content="0; url=../">');

	if ($_POST['action']=='addgrp') {
		
		#echo "dept nr = ".$dept_nr;
		$dept = $dept_obj->getDeptAllInfo($dept_nr);
		#---add some code here, no duplication of data
		
		#$srvObj->getServiceGroupInfo($_POST['gname'], $_POST['gcode']);
		#echo "sql = ".$srvObj->sql;
		$srvObj->getServiceGroupInfo($_POST['gname'], $_POST['gcode'], $dept_nr);
		
		if (($srvObj->count==0)&&($_POST['gcode']!='none')){
			#if ($srvObj->saveRadioServiceGroup(strtoupper($_POST['gname']), strtoupper($_POST['gcode']), $_POST['goname'])) {			
			if ($srvObj->saveRadioServiceGroup(strtoupper($_POST['gname']), strtoupper($_POST['gcode']), $_POST['goname'], $_POST['dept_nr'])) {			
				#echo "Service Group ".strtoupper($_POST['gname'])." is successfully created!";
				echo "Service Group ".strtoupper($_POST['gname'])." in ".$dept['name_formal']." is successfully created";
			}
			else
				echo $srvObj->error;
				
		}else{
			#echo "Service Group ".strtoupper($_POST['gname'])." already exists or the code is not accepted!";
			echo "Service Group ".strtoupper($_POST['gname'])." in ".$dept['name_formal']." already exists!";
		}		
	}
	

?>