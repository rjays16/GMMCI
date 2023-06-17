<?php
$root_path = '../../';
$top_dir = 'modules/social_service/';

$QuickMenu = array(
	array('icon'=>'patdata.gif', 
				'url'=>$root_path.'modules/social_service/social_service_pass.php{{$URL_APPEND}}&target=entry',
				'label'=>'Classify'),
				
	array('icon'=>'statbel2.gif', 
				'url'=>$root_path.'modules/social_service/social_service_pass.php?{{$URL_APPEND}}&target=list',
				'label'=>'List'),
				
	array('label'=>'|'),			
				
	array('icon'=>'chart.gif',
				'url'=>$root_path.'modules/social_service/social_service_pass.php{{$URL_APPEND}}&target=reports',
				'label'=>'Reports'),
				
	array('label'=>'|')			
);
?>