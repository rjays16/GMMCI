<?php
$root_path='../../../';
$top_dir='modules/or/';

$QuickMenu = array(
	array('icon'=>'patdata.gif',
				'url'=>$root_path.'modules/or/request/op_request_pass.php{{$URL_APPEND}}&target=or_main_new_request',
				'label'=>'Request'),

	array('icon'=>'page_gear.png',
				'url'=>$root_path.'modules/or/request/op_request_pass.php{{$URL_APPEND}}&target=or_main_list',
				'label'=>'List'),

	array('icon'=>'date_edit.png',
				'url'=>$root_path.'modules/or/request/op_request_pass.php{{$URL_APPEND}}&target=or_main_approve',
				'label'=>'Approve'),

	array('icon'=>'pre_operation.png',
				'url'=>$root_path.'modules/or/request/op_request_pass.php{{$URL_APPEND}}&target=pre_operation_main',
				'label'=>'Pre-Op'),

	array('icon'=>'or_main_post_icon.png',
				'url'=>$root_path.'modules/or/request/op_request_pass.php{{$URL_APPEND}}&target=post_operation_main',
				'label'=>'Post-Op'),

	array('icon'=>'or_deaths.png',
				'url'=>$root_path.'modules/or/request/op_request_pass.php{{$URL_APPEND}}&target=select_or_deaths',
				'label'=>'Deaths'),

	array('label'=>'|'),

//	array('icon'=>'chart.gif',
//				'url'=>$root_path.'modules/social_service/social_service_pass.php{{$URL_APPEND}}&target=reports',
//				'label'=>'Reports'),

	array('icon'=>'chart.gif',
				'url'=>$root_path.'modules/or/request/seg-OR-reports.php',
				'label'=>'Reports'),

	array('label'=>'|')
);
?>