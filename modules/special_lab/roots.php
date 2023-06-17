<?php
$root_path='../../';
$top_dir='modules/special_lab/';
#echo "from = ".$_GET['view_from'];
if (($_GET['popUp']!='1')&&(empty($_GET['view_from']))){
$QuickMenu = array(
	array('icon'=>'patdata.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=specialLab&user_origin=splab',
				'label'=>'New'),

	array('icon'=>'statbel2.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php?{{$URL_APPEND}}&target=specialLab_list&user_origin=splab',
				'label'=>'List'),

	array('icon'=>'hfolder.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=specialLab_result&done=0&user_origin=splab',
				'label'=>'Undone'),

	array('icon'=>'task_tree.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=specialLab_result&done=1&user_origin=splab',
				'label'=>'Done'),

	array('label'=>'|'),

	/*array('icon'=>'chart.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=seglabreports',
				'label'=>'Reports'),
	*/
	array('label'=>'|')
);
}else{
	if ($_GET['ptype']){
		$QuickMenu = array(
			array('icon'=>'patdata.gif',
					'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=specialLab&popUp=1&user_origin=splab',
					'label'=>'New')
		 );
	}
}
?>
