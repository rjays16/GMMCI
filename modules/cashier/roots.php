<?php
$root_path='../../';
$top_dir='modules/cashier/';


$QuickMenu = array(
	array(
		'icon' => 'cart_go.png',
		'url' => $root_path.'modules/cashier/seg-cashier-pass.php{{$URL_APPEND}}&target=requestlist',
		'label' => 'Requests'
	),

// array('icon' => 'user_go.png',
//		'url' => $root_path.'modules/cashier/seg-cashier-pass.php{{$URL_APPEND}}&target=walkinrequestlist',
//		'label' => 'Walk-in'
//),

	array(
		'icon' => 'page_green.png',
		'url' => $root_path.'modules/cashier/seg-cashier-pass.php{{$URL_APPEND}}&target=services',
		'label' => 'Payments'
	),

	array(
		'icon' => 'page_green.png',
		'url' => $root_path.'modules/cashier/seg-cashier-pass.php{{$URL_APPEND}}&target=vatable',
		'label' => 'Payments(VAT)'
	),

	array(
		'icon' => 'time.png',
		'url' => $root_path.'modules/cashier/seg-cashier-pass.php{{$URL_APPEND}}&target=recent',
		'label' => 'Recent'
	),


	array('label' => '|'),

	array(
		'icon' => 'note_edit.png',
		'url' => $root_path.'modules/cashier/seg-cashier-pass.php{{$URL_APPEND}}&target=memonew',
		'label' => 'Memo'
	),

	array(
		'icon' => 'folder_find.png',
		'url' => $root_path.'modules/cashier/seg-cashier-pass.php{{$URL_APPEND}}&target=memoarchives',
		'label' => 'CM Archives'
	),


	array('label' => '|'),

	array(
		'icon' => 'folder_page.png',
		'url' => $root_path.'modules/cashier/seg-cashier-pass.php{{$URL_APPEND}}&target=archives',
		'label' => 'Archives'
	),

	array(
		'icon' => 'wrench.png',
		'url' => $root_path.'modules/cashier/seg-cashier-pass.php{{$URL_APPEND}}&target=databank',
		'label' => 'Manager'
	),

	array(
		'icon' => 'report.png',
		'url' => $root_path.'modules/cashier/seg-cashier-pass.php{{$URL_APPEND}}&target=reports',
		'label' => 'Reports'
	),

	array('label' => '|')
);

