<?php
$root_path='../../';
$top_dir='modules/radiology/';

#if ($_GET['popUp']!='1'){
if (($_GET['popUp']!='1')&&(empty($_GET['view_from']))){
$QuickMenu = array(
	array('icon'=>'patdata.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_test',
				'label'=>'New'),

	array('icon'=>'waiting.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php?{{$URL_APPEND}}&target=radiorequestlist&dept_nr=158',
				'label'=>'List'),
	
    /*array('icon'=>'disc_unrd.gif',
                'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=segradiotech&dept_nr=158',
                'label'=>'Serve'),            
    
	array('icon'=>'calmonth.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_cal&dept_nr=158',
				'label'=>'Schedule'),
	
	array('icon'=>'book_hotel.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_cal_list&dept_nr=158',
				'label'=>'Schedule List'),
    */
    array('icon'=>'book_hotel.gif',
                'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_cal_list&dept_nr=158',
                'label'=>'Serve/Schedule'),
    
    array('icon'=>'bestell.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_undone&dept_nr=158',
				'label'=>'Undone'),

	array('icon'=>'documents.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_done&dept_nr=158',
				'label'=>'Done'),

	array('icon'=>'file_update.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_unified&dept_nr=158',
				'label'=>'Unified'),

	array('label'=>'|'),

	array('icon'=>'torso.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_patient&dept_nr=158',
				'label'=>'Film'),

	array('icon'=>'torso_br.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_borrow&dept_nr=158',
				'label'=>'Borrowers'),

	array('label'=>'|'),

	array('icon'=>'chart.gif',
				'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=segradioreports',
				'label'=>'Reports'),

	array('label'=>'|')
);
}else{
		if ($_GET['ptype']){
			$QuickMenu = array(
				array('icon'=>'patdata.gif',
					'url'=>$root_path.'modules/laboratory/labor_test_request_pass.php{{$URL_APPEND}}&target=radio_test&popUp=1',
					'label'=>'New')
			);
		}
}
?>
