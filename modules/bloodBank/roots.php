<?php
$root_path='../../';
$top_dir='modules/bloodBank/';
#echo "from = ".$_GET['view_from'];
if (($_GET['popUp']!='1')&&(empty($_GET['view_from']))){
$QuickMenu = array(
	array('icon'=>'redlist.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=blood&user_origin=blood',
				'label'=>'New'),

	array('icon'=>'statbel2.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php?{{$URL_APPEND}}&target=blood_list&user_origin=blood',
				'label'=>'List'),

	array('icon'=>'hfolder.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=blood_result&done=0&user_origin=blood',
				'label'=>'Undone'),

	array('icon'=>'task_tree.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=blood_result&done=1&user_origin=blood',
				'label'=>'Done'),

	array('label'=>'|'),

	array('icon'=>'chart.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=seglabreports&user_origin=blood',
				'label'=>'Reports'),

	array('label'=>'|')
);
}else{
	if ($_GET['ptype']){
		$QuickMenu = array(
			array('icon'=>'redlist.gif',
					'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=blood&popUp=1&user_origin=blood',
					'label'=>'New')
		 );
	}
}
?>
