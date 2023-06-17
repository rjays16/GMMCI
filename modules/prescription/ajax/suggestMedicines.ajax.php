<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";
require_once($root_path.'include/care_api_classes/class_pharma_product.php');
require_once($root_path.'include/care_api_classes/prescription/class_prescription_writer.php');

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$data = array();

/**
* Data fetching goes here ....
*
*/
$term = $_REQUEST['term'];
$prodObj = new SegPharmaProduct();
$result = $prodObj->searchProducts(null, $term, null, 'M');

$presObj = new SegPrescription();
$has_license = $presObj->isLicensedPersonell();

if ($result !== FALSE) {
	$total = $prodObj->FoundRows();

	while ($row = $result->FetchRow()) {

		$data[] = array(
			'value'=>$row['artikelname'],
			'code'=>$row['bestellnum'],
			'name'=>$row['artikelname'],
			'generic'=>$row['generic'],
			'availability'=>'available',
			'restricted'=>$row['is_restricted'],
			'is_licensed'=>$has_license
		);
	}
}


/**
* Convert data to JSON and print
*
*/
$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
print $json->encode($data);