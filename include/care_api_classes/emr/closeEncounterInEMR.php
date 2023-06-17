<?php
	try {
		# for OPD only
		# added by VAS 11/19/2013
		# integration to EMR starts here	
		# close case in EMR
		#$emr_obj=new EMR;
	    #$objInfo = new Hospital_Admin();

		#$row_hosp = $objInfo->getAllHospitalInfo();
		#$EMR_address = $row_hosp['EMR_address'];

		$method = 'PUT';
		$url = 'http://'.$EMR_address.'/X2WaveWebAPI/api/patients/'.$pid.'/closeCase/'.$encounter_nr;
		$emr_obj->consumeWRITEmethodnoDATA($url, $method);
		#echo $url."-".$method;
		#===========================
	} catch(Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}	
?>