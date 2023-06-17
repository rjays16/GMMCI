<?php
	#---added by VAS
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	$srvObj=new SegLab();

	include_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;


	if (eregi("seg-lab-services-admin.action.php",$PHP_SELF)) 
		die('<meta http-equiv="refresh" content="0; url=../">');

	if ($_POST['action']=='addgrp') {
		
		$dept = $dept_obj->getDeptAllInfo($dept_nr);
		#---add some code here, no duplication of data
		$srvObj->getServiceGroupInfo($_POST['gname'], $dept_nr);
		#echo "sql = ".$srvObj->sql;
		if ($srvObj->count==0){
			#if ($srvObj->saveLabServiceGroup($_POST['gname'], $dept_nr)) {			
			if ($srvObj->saveLabServiceGroup($_POST['gname'], $_POST['dept_nr'])) {			
			#if ($srvObj->saveLabServiceGroup($_POST['gname'])) {			
				echo "Service Group ".strtoupper($_POST['gname'])." in ".$dept['name_formal']." is successfully created";
				#echo "Service Group ".strtoupper($_POST['gname'])." is successfully created!";
			}
			else
				echo $srvObj->sql;
		}else{
			echo "Service Group ".strtoupper($_POST['gname'])." in ".$dept['name_formal']." already exists!";
		}		
	}
	

?>