<?php 
	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/care_api_classes/class_personell.php');
	require_once($root_path.'include/care_api_classes/curl/class_curl.php');
	$pers_obj=new Personell; 
	$curl_obj = new Rest_Curl;

	$getAllDoctors = $pers_obj->_getAllDoctor(1);

	if(is_object($getAllDoctors))
	{
		while($getDoctor = $getAllDoctors->FetchRow())
		{
			$curl_obj->checkDoctor($getDoctor['personell_nr']);
		}
	}

	echo "Task finish, please check your FIS";

?>