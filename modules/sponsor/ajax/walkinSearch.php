<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require_once $root_path."include/inc_environment_global.php";
require_once $root_path."classes/json/json.php";
require_once $root_path.'include/care_api_classes/class_walkin.php';

# Send appropriate headers
Services_JSON::sendHeaders();

$data = array();

/**
* Data fetching starts here ....
*
*/
$term = $_REQUEST['term'];
$walkin = new SegWalkin;



$result = $walkin->getWalkin( array( 'key'=>$term ) );
if ($result !== FALSE) {
	foreach ($result as $row) {
		$data[] = array(
			'value' => 'W'.$row['pid'],
			'name' => strtoupper($row['fullname'])
		);
	}
}


/**
* Convert data to JSON and print
*
*/
$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
print $json->encode($data);