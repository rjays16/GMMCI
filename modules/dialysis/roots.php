<?php
$root_path = '../../';
$top_dir = 'modules/dialysis/';

$QuickMenu = array(
	array('icon'=>'door_in.png',
				'url'=>$root_path.'modules/dialysis/seg-dialysis-request-new.php{{$URL_APPEND}}',
				'label'=>'Request'),

	array('icon'=>'group_edit.png',
				'url'=>$root_path.'modules/dialysis/seg-dialysis-request-list.php{{$URL_APPEND}}',
				'label'=>'List'),

	array('icon'=>'folder_user.png',
				'url'=>$root_path.'modules/dialysis/seg-dialysis-billing.php{{$URL_APPEND}}',
				'label'=>'Billing'),

	array('icon'=>'chart_bar.png',
				'url'=>$root_path.'modules/dialysis/seg-dialysis-reports.php{{$URL_APPEND}}',
				'label'=>'Reports'),

	array('label'=>'|')
);

