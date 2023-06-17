<?php
$root_path = '../../';
$top_dir = 'modules/sponsor/';

$QuickMenu = array(
	array('icon'=>'pill_go.png',
				'url'=>$root_path.'modules/sponsor/seg_sponsor_lingap_walkin.php{{$URL_APPEND}}',
				'label'=>'Lingap:Walkin'),

	array('icon'=>'user_add.png',
				'url'=>$root_path.'modules/sponsor/seg_sponsor_lingap_patient.php{{$URL_APPEND}}',
				'label'=>'Requests'),

	array('icon'=>'folder_user.png',
				'url'=>$root_path.'modules/sponsor/seg_sponsor_lingap_billing.php{{$URL_APPEND}}',
				'label'=>'Hosp. Bill'),

	array('icon'=>'table.png',
				'url'=>$root_path.'modules/sponsor/seg_sponsor_lingap_list.php{{$URL_APPEND}}',
				'label'=>'List'),

	array('icon'=>'report.png',
				'url'=>$root_path.'modules/sponsor/seg-lingap-reports.php{{$URL_APPEND}}',
				'label'=>'Reports'),

	array('label'=>'|'),

	array('icon'=>'user_go.png',
				'url'=>$root_path.'modules/sponsor/seg_sponsor_cmap_patient.php{{$URL_APPEND}}',
				'label'=>'CMAP'),

	array('icon'=>'table.png',
				'url'=>$root_path.'modules/sponsor/seg_sponsor_cmap_list.php{{$URL_APPEND}}',
				'label'=>'List'),

	array('icon'=>'group_key.png',
				'url'=>$root_path.'modules/sponsor/seg_sponsor_cmap_accounts.php{{$URL_APPEND}}',
				'label'=>'Accounts'),

	array('icon'=>'report.png',
				'url'=>$root_path.'modules/sponsor/seg-cmap-reports.php{{$URL_APPEND}}',
				'label'=>'Reports'),

	array('label'=>'|')
);

