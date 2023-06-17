<?php

	#for writing in EMR
	
	#retrieve data from EMR
	try {
		
		$urlget = 'http://'.$EMR_address.'/X2WaveWebAPI/api/patients/'.$pid;
		$getpatientinfo = $emr_obj->consumeREADmethod($urlget);
		
		if ($getpatientinfo!='null'){
			$method = 'PUT';
			$url = 'http://'.$EMR_address.'/X2WaveWebAPI/api/patients/update/'.$pid;
		}else{
			$method = 'POST';
			$url = 'http://'.$EMR_address.'/X2WaveWebAPI/api/patients/new';
		}
		
		$data = $emr_obj->getPatientdataArray($_POST);
		$emrresult = $emr_obj->consumeWRITEmethod($data, $url, $method);

	} catch(Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
?>