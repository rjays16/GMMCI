<?php

	#for writing in EMR
	
	#retrieve data from EMR
	try {

		$urlget = 'http://'.$EMR_address.'/X2WaveWebAPI/api/patients/'.$pid.'/case/'.$encounter_nr;
		$getencinfo = $emr_obj->consumeREADmethod($urlget);
		
		$emrdata = json_decode($getencinfo);
		
		if ($emrdata->ResponseMessage!='Case cannot be found.'){
			$method = 'PUT';
			$url = 'http://'.$EMR_address.'/X2WaveWebAPI/api/patients/updatecase/'.$encounter_nr;
		}else{
			$method = 'POST';
			$url = 'http://'.$EMR_address.'/X2WaveWebAPI/api/patients/newcase';
		}

		$data = $emr_obj->getEncounterdataArray($_POST);
		$emrresult = $emr_obj->consumeWRITEmethod($data, $url, $method);

	} catch(Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
		
?>