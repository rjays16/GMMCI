<?php
/*
function search ($search_queries, $query){
	if(strlen($query)== 0){
		return;
	}
	$query = strtolower($query);
	
	$firstChar = $query[0];
	
	if (!preg_macth('/[0-9A-Z0-9/', $firstChar, $matches))
		return;
	
	$charQueries = $search_queries[$firstChar];
	
	$results = array();
	
	for($i = 0; $i < count ($charQueries); $i++){
		if(strcasecmp(substr($charQueries[$i], 0, strlen($query)),$query) == 0)
			$results[] = $charqueries[$i];	
	}
	return $results;
}
*/
/*
function search ($searh_queries, $query){
	global $db;
	if(strlen($query) == 0 ){
		return;
	}
	$query = strolower($query);
	
	$sql='SELECT enc.encounter_nr,enc.encounter_class_nr,enc.encounter_type, enc.is_discharged,enc.encounter_date, '.
				'\n enc.admission_dt, IF(enc.encounter_type<3,enc.encounter_date,enc.admission_dt) AS date, '.
				'\n	reg.pid, reg.name_last, reg.name_first, reg.date_birth, reg.sex, reg.death_date';
	
		$dbtable =' FROM care_encounter AS enc, care_person AS reg'; 
	
	
	
	
	return $results;
}

*/




require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'classes/json/json.php');

header('Content-type: text/plain');
//header('Content-type: text/json');


//$query = $_GET['query'];
//$query = 

$encounter_nr = $_GET['query'];
$encounter_nr = '2007000011';
//$results = search ($search_queries, $query);
//sendResults($query, $results);
$json = new Services_JSON();

//function search($search_queries,$encounter_nr){
$enc_obj=new Encounter($encounter_nr); 	
$enc_Info = $enc_obj->getEncounterInfo($encounter_nr);
//print "enc_Info=".$enc_Info;

$output = $json->encode($enc_Info);
print $output;

?>