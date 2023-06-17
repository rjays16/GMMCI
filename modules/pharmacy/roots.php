<?php
$root_path='../../';
$top_dir='modules/pharmacy/';

$QuickMenu = array(
	array('icon'=>'order.gif', 
				'url'=>$root_path.'modules/pharmacy/seg-pharma-select-area.php{{$URL_APPEND}}&target=ordernew',
				'label'=>'New Order'),
				
	array('icon'=>'manage_orders.gif', 
				'url'=>$root_path.'modules/pharmacy/apotheke-pass.php?{{$URL_APPEND}}&target=orderlist',
				'label'=>'Orders'),
  
  array('icon'=>'disc_unrd.gif', 
        'url'=>$root_path.'modules/pharmacy/apotheke-pass.php?{{$URL_APPEND}}&target=servelist',
        'label'=>'Serve'),
				
  array('label'=>'|'),
	
	array('icon'=>'wardstock.gif', 
				'url'=>$root_path.'modules/pharmacy/seg-pharma-select-area.php{{$URL_APPEND}}&target=newstock',
				'label'=>'Wardstock'),
				
	array('icon'=>'recent.gif', 
				'url'=>$root_path.'modules/pharmacy/apotheke-pass.php?{{$URL_APPEND}}&target=recentstock',
				'label'=>'Recent'),
        
  array('label'=>'|'),
  
  array('icon'=>'import_address.gif', 
        'url'=>$root_path.'modules/pharmacy/apotheke-pass.php?{{$URL_APPEND}}&target=returnnew',
        'label'=>'Return'),
        
  array('icon'=>'import_address_2.gif', 
        'url'=>$root_path.'modules/pharmacy/apotheke-pass.php?{{$URL_APPEND}}&target=refundnew',
        'label'=>'Refund'),
				
	array('label'=>'|'),
	
  array('icon'=>'storage.gif', 
        'url'=>$root_path.'modules/pharmacy/apotheke-pass.php?{{$URL_APPEND}}&target=databank',
        'label'=>'Databank'),

  array('icon'=>'newpatient.gif', 
        'url'=>$root_path.'modules/pharmacy/apotheke-pass.php?{{$URL_APPEND}}&target=managewalkin',
        'label'=>'Walk-in'),
        
  array('icon'=>'chart.gif', 
        'url'=>$root_path.'modules/pharmacy/apotheke-pass.php?{{$URL_APPEND}}&target=reports',
        'label'=>'Reports'),
        
	array('label'=>'|')
);

if (get_magic_quotes_gpc()) {
	define('__ALVIN_GPC_STRIPPED',1);
	$gpc_in = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
	while (list($gpc_k,$gpc_v) = each($gpc_in)) {
		foreach ($gpc_v as $gpc_key => $gpc_val) {
			if (!is_array($gpc_val)) {
				$gpc_in[$gpc_k][$gpc_key] = stripslashes($gpc_val);
				continue;
			}
			$gpc_in[] =& $in[$gpc_k][$gpc_key];
		}
	}
	unset($gpc_in);
}
