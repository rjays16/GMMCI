<?php
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');

	include_once($root_path."/classes/fpdf/pdf.class.php");
	include_once($root_path."include/care_api_classes/class_cashier.php");
	$cClass = new SegCashier();
	$ORNo = $_REQUEST['nr'];
	$Mode = $_REQUEST['mode'];
	if (!$Mode) $Mode = 'R';
	$info = $cClass->GetPayInfo( $ORNo );
	if ($Mode == 'R') {
		$rsDetails = $cClass->GetPayDetails( $ORNo );
		$items = array();
		while ($row = $rsDetails->FetchRow()) {			
			$items[] = $row;
		}
	}
	
	print_r($info);
	print_r($items);

?>