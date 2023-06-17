<?php
$root_path='../../';
$top_dir='modules/laboratory/';
#echo "from = ".$_GET['view_from'];
if (($_GET['popUp']!='1')&&(empty($_GET['view_from']))){
$QuickMenu = array(
	array('icon'=>'patdata.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=seglabnew&user_origin=lab',
				'label'=>'New'),

	array('icon'=>'statbel2.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php?{{$URL_APPEND}}&target=seglabservrequest_new&user_origin=lab',
				'label'=>'List'),

	array('icon'=>'application_form_edit.png',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php?{{$URL_APPEND}}&target=samples&user_origin=lab',
				'label'=>'W Samples'),

	array('icon'=>'hfolder.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=seglabOrder&done=0&user_origin=lab',
				'label'=>'Undone'),

	array('icon'=>'task_tree.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=seglabOrder&done=1&user_origin=lab',
				'label'=>'Done'),
    
    array('icon'=>'yellowlist.gif',
                'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=seglabResult&user_origin=lab',
                'label'=>'Results'),            

	array('label'=>'|'),

	array('icon'=>'chart.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=seglabreports&user_origin=lab',
				'label'=>'Reports'),

	array('label'=>'|')
);
}else{
	if ($_GET['ptype']){
		$QuickMenu = array(
				array('icon'=>'patdata.gif',
							'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=seglabnew&popUp=1&user_origin=lab',
							'label'=>'New')
				);
	}
}
?>
