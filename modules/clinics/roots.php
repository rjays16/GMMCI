<?php
$root_path='../../';
$top_dir='modules/clinics/';
#echo "from = ".$_GET['view_from'];
if (($_GET['popUp']!='1')&&(empty($_GET['view_from']))){
$QuickMenu = array(
	array('icon'=>'patdata.gif',
				'url'=>$root_path.'modules/clinics/labor_test_request_pass.php{{$URL_APPEND}}&target=seglabnew',
				'label'=>'New'),

	array('icon'=>'statbel2.gif',
				'url'=>$root_path.'modules/clinics/labor_test_request_pass.php?{{$URL_APPEND}}&target=seglabservrequest_new',
				'label'=>'List'),
	array('icon'=>'folder_user.png',
				'url'=>$root_path.'modules/clinics/labor_test_request_pass.php?{{$URL_APPEND}}&target=prescription_writer',
				'label'=>'Prescription'),
	array('icon'=>'note_add.png',
				'url'=>$root_path.'modules/clinics/labor_test_request_pass.php?{{$URL_APPEND}}&target=soap_entry',
				'label'=>'S.O.A.P'),
	array('label'=>'|')
);
}
?>