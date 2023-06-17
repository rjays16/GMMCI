<?php
//define ('PATH', 'http://api.search.yahoo.com/WebSearchService/V1/webSearch');
//define hostname and path

require('./roots.php');


define ('PATH',$root_path.'modules/social_service/social_service.php');

$type = "text/xml";
$query="?";
foreach ($_GET as $key => $value){
	if(($key == "output") && ($value == "json")) {
		$type="application/json"; 
	}
	$query .= urlencode($key) ."=". urlencode($value)."&";
}
foreach ($_POST as $key => $value){
	if(($key == "output") && ($value == "json")){
		$type = "application/json";	
	}
	$query .= $key ."=". $value. "&";
}
$query .="appid= jennyhan_ac";
$url = PATH.$query;

//open the curl session
$session = curl_init($url);

curl_setopt($session, CURLOPT_HEADER, false);
curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

//make the call
$response = curl_exec($session);

header("Content-type: ".$type);
echo $response;
curl_close($session);


?>