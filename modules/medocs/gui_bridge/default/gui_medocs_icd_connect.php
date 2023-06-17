<?php
	$db_connection = mysql_connect("192.168.2.60","hisdbuser","hisDB")
                  or die("Could not find DB");
	
	mysql_select_db("hisdb",$db_connection)
	                  or die("Could not find DB");	
	
	if(!$db_connection) {
		// Show error if we cannot connect.
		echo 'ERROR: Could not connect to the database.';
	} else {
		if(isset($_POST['queryString'])) {
			$queryString = $_POST['queryString'];
			if(strlen($queryString) >0) {
				$user_query = "SELECT * FROM care_icd10_en WHERE diagnosis_code LIKE '$queryString%' LIMIT 10";
				$query = mysql_query ($user_query, $db_connection);
				if($query) {
					// While there are results loop through them - fetching an Object (i like PHP5 btw!).
					while ($result = mysql_fetch_array ($query)){
							$pos = strpos(trim($result["description"]), " ");
							if ($pos)
								$desc = substr(trim($result["description"]),0,$pos);
							else
								$desc = trim($result["description"]);
	         			//onKeyUp="" onKeyDown=""	
							#echo '<li onClick="fill(\''.$result["diagnosis_code"].'\');">'.$result["diagnosis_code"]." : ".$desc.'</li>';
							echo '<option onClick="fill(\''.$result["diagnosis_code"].'\');" onKeyPress="alert(\'hello\');">'.$result["diagnosis_code"]." : ".$desc.'</option>';
	         		}
				} else {
					echo 'ERROR: There was a problem with the query.';
				}
			} else {
				// Dont do anything.
			} // There is a queryString.
		} else {
			echo 'There should be no direct access to this script!';
		}
	}
?>