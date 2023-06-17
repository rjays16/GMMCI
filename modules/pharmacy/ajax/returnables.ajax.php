<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}include/care_api_classes/pharmacy/class_return.php";
require "{$root_path}classes/json/json.php";

global $db;

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$sortName = $_REQUEST['sort'];
if (!$sortName)
	$sortName = 'Name';
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	'Name' => 'artikelname',
	'Returns' => 'returns',
	'Served' => 'served'
);
$sort = $sortMap[$sortName]." ".$sortDir;
$data = array();

$f = array(
	'OFFSET' => $offset,
	'MAXROWS' => $maxRows,
	'ENCOUNTER' => $_REQUEST['enc'],
	'NAME' => $_REQUEST['name'],
	'SORT' => $sort
);
$rh = new PharmacyReturn();
$ok = $rh->GetReturnables( $f );

$result = $rh->result;
if ($ok) {
	$total = $rh->FoundRows();
	while ( $row = $result->FetchRow() ) {
		$data[] = array(
			'Id' => $row['bestellnum'],
			'Name' => $row['artikelname'],
			'Generic' => $row['generic'],
			'Packing' => $row['packing'],
			'Returns' => $row['returns'],
			'Served' => $row['served'],
		);
	}
}
else {
	# error
	//echo $rh->sql;
}

$response = array(
	'currentPage'=>$page,
	'total'=>$total,
	'data'=>$data
 );

$json = new Services_JSON;
print $json->encode($response);