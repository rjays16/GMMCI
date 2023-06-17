<?php

	#for writing in EMR
	require_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;
	
	try {
		#retrieve data from EMR
		$_POST = $emr_obj->getDoctorInfo($nr);
		$urlget = 'http://'.$EMR_address.'/X2WaveWebAPI/api/doctors/'.$nr;
		$getdoctorinfo = $emr_obj->consumeREADmethod($urlget);
		
		if ($getdoctorinfo!='null'){
			$method = 'PUT';
			$url = 'http://'.$EMR_address.'/X2WaveWebAPI/api/doctors/update/'.$nr;
		}else{
			$method = 'POST';
			$url = 'http://'.$EMR_address.'/X2WaveWebAPI/api/doctors/new';
		}

		$_POST['dept_nr'] = $dept_nr;
		$dept_info = $dept_obj->getDeptAllInfo($dept_nr);
		
		$_POST['dept_name'] = $dept_info['name_formal'];
		
		$data = $emr_obj->getDoctordataArray($_POST);
		$emrresult = $emr_obj->consumeWRITEmethod($data, $url, $method);
		#echo $emrresult; exit();
	} catch(Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
		#exit();
	}	
?>