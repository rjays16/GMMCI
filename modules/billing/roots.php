<?php
	$root_path='../../';
	$top_dir='modules/billing/';
	
	$QuickMenu = array(
		/*array('label'=>'|'),

	array('icon'=>'patdata.gif', 
				'url'=>$root_path.'modules/billing/bill-pass.php{{$URL_APPEND}}&target=seg_billing',
				'label'=>'Process'),*/
#added by shand 01/02/2014
#edited by: ian 01/06/2014
	array('label'=>'|'),
	array('icon'=>'patdata.gif', 
				'url'=>$root_path.'modules/billing/bill-pass.php{{$URL_APPEND}}&target=seg_billing_PHIC',
				'label'=>'Process(New)'),
#end by shand 01/02/2014
	array('label'=>'|'),
	array('icon'=>'statbel2.gif', 
				'url'=>$root_path.'modules/billing/bill-pass.php?{{$URL_APPEND}}&target=seg_billing_list',
				'label'=>'List'),
				
	array('label'=>'|'),
	/*			
	array('icon'=>'copy_adrs.gif', 
				'url'=>$root_path.'modules/billing/bill-pass.php{{$URL_APPEND}}&target=packagemanage',
				'label'=>'Package'),
	
	array('icon'=>'sitemap_webcam.gif', 
				'url'=>$root_path.'modules/cashier/seg-cashier-pass.php{{$URL_APPEND}}&target=miscellaneous',
				'label'=>'Miscellaneous'),			
	*/			
	array('icon'=>'file_update.gif',
				'url'=>$root_path.'modules/billing/bill-pass.php{{$URL_APPEND}}&target=seg_billing_transmittal',
				'label'=>'Transmittal'),
				
	array('label'=>'|'),
	//added by mai 01-09-2015
	array('icon'=>'articles.gif',
				'url'=>$root_path.'modules/billing/bill-pass.php{{$URL_APPEND}}&target=seg_billing_promissory',
				'label'=>'Promissory Note'),
				
	array('label'=>'|'),
	//end added by mai
	//pol start
	array('icon'=>'report.png',
				'url'=>$root_path.'modules/billing/bill-pass.php?{{$URL_APPEND}}&target=seg_billing_reports',
				'label'=>'Reports'),
				
	array('label'=>'|')
	//pol end
);
?>
