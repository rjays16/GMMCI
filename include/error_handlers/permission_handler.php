<?php

class PermissionHandler{
	
	function hasSession(){
		if(trim($_SESSION['sess_user_name']) != '' && 
			trim($_SESSION['sess_user_personell_nr']) != '' &&
			trim($_SESSION['sess_login_userid']) != '' &&
			trim($_SESSION['sess_permission']) != ''
		){
			return array(
				'message'	=> "",
				'hasSession' => true
			);
		}
		include('./roots.php');	
		// var_dump($root_path); die();
		
		ob_start();
	    header('Location:'.$root_path);
	    ob_end_flush();
	    exit();
		// return array(
		// 		'message'	=> "You do not have permission to view this report. Please contact the system administrator.",
		// 		'hasSession' => false
		// 	);
	}



}


?>