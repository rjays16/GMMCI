<?php
$root_path='../../../';
$top_dir='modules/or/';

$QuickMenu = array(
	array('icon'=>'patdata.gif',
				'url'=>$root_path.'modules/or/request/op_request_pass.php{{$URL_APPEND}}&target=or_asu_request',
				'label'=>'Request'),

	array('icon'=>'page_gear.png',
				'url'=>$root_path.'modules/or/request/op_request_pass.php{{$URL_APPEND}}&target=or_asu_list',
				'label'=>'List'),

	array('icon'=>'page_key.png',
				'url'=>$root_path.'modules/or/request/op_request_pass.php{{$URL_APPEND}}&target=approve_asu',
				'label'=>'Approve'),

	array('icon'=>'pre_operation.png',
				'url'=>$root_path.'modules/or/request/op_request_pass.php{{$URL_APPEND}}&target=pre_operation',
				'label'=>'Pre-Op'),

	array('icon'=>'or_main_post_icon.png',
				'url'=>$root_path.'modules/or/request/op_request_pass.php{{$URL_APPEND}}&target=post_operation',
				'label'=>'Post-Op'),

	array('label'=>'|'),

	array('icon'=>'chart.gif',
				'url'=>$root_path.'modules/or/request/seg-OR-reports.php',
				'label'=>'Reports'),

	array('label'=>'|')
);
?>
