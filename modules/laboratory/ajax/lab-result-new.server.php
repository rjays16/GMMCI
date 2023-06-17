<?php
    function saveResult($refno, $service_code, $group_id, $pathologist, $medtech, $date, $arr, $unit, $param_id, $confidential){
        $objResponse = new xajaxResponse();
        $lab_result = new Lab_Results();
        $date = date('Y-m-d H:i:s', strtotime($date));
        if($confidential == 'true')
            $confidential = '1';
        else
            $confidential = '0';

        $checkResult = $lab_result->checkResult($refno, $service_code);

        if($checkResult->RecordCount() != 0){

            $update_result = $lab_result->update_lab_resultdata($refno, $group_id, $date, $medtech, $pathologist, $confidential, $service_code);
            if($update_result){
                $lab_result->delete_lab_result($refno, $service_code);

                for($x=0; $x<count($arr);$x++){
                    $save_results = $lab_result->add_lab_result($refno, $service_code, $param_id[$x], $arr[$x], $unit[$x]);
                    if(!$save_results)
                        $objResponse->alert('Successfully Failed!');
                }
                $objResponse->alert('Successfully Save!');
                $objResponse->call('refreshPage');
            }
            else{
                $objResponse->alert('Successfully Failed!');
            }
        }
        else{
            $save_result = $lab_result->add_lab_resultdata($refno, $group_id, $date, $medtech, $pathologist, $confidential, $service_code);
            
            if($save_result){
                for($x=0; $x<count($arr);$x++){
                    $save_results = $lab_result->add_lab_result($refno, $service_code, $param_id[$x], $arr[$x], $unit[$x]);
                    if(!$save_results)
                        $objResponse->alert('Successfully Failed!');
                }
                $objResponse->alert('Successfully Save!');
                $objResponse->call('refreshPage');
            }
            else{
                $objResponse->alert('Successfully Failed!');
            }
        }

        return $objResponse;
    }
   
    function checkResult($refno, $code){
        $objResponse = new xajaxResponse();
        $lab_result = new Lab_Results();

        $checkResult = $lab_result->checkResult($refno, $code);

        if($checkResult){
            while($row = $checkResult->FetchRow()){
                $data->param_id = $row['param_id'];
                $data->param_name = $row['name'];
                $data->form_name = $row['form_name'];
                $data->form_id = $row['form_id'];
                $data->value = $row['result_value'];               
                $data->si_unit = $row['SI_unit'];
                $data->si_low = $row['SI_lo_normal'];
                $data->si_hi = $row['SI_hi_normal'];
                $data->cu_unit = $row['CU_unit'];
                $data->cu_low = $row['CU_lo_normal'];
                $data->cu_hi = $row['CU_hi_normal'];
                $data->medtech = $row['med_tech_pid'];
                $data->patho = $row['pathologist_pid'];
                $data->confidential = $row['is_confidential'];

                if($row['SI_unit'])
                    $data->si = $row['SI_lo_normal'].' - '.$row['SI_hi_normal'].' '.$row['SI_unit'];
                else
                    $data->si = '';
                if($row['CU_unit'])
                    $data->cu = $row['CU_lo_normal'].' - '.$row['CU_hi_normal'].' '.$row['CU_unit'];
                else
                    $data->cu = '';
                if($row['is_numeric'] == 1)
                    $data->param_type = 'Numeric';
                else if($row['is_longtext'] == 1)
                    $data->param_type = 'Long Text';
                else if($row['is_boolean'] == 1)
                    $data->param_type = 'Checkbox';
                else
                    $data->param_type = 'Text';

                $objResponse->call('populate', $data);
            }
        
        }
        
        return $objResponse;
    }

    function deleteResult($refno, $code, $group, $reason){
        $objResponse = new xajaxResponse();
        $lab_result = new Lab_Results();

        $delete_result = $lab_result->delete_lab_resultdata($refno, $group, $reason);

        if($delete_result)
            $delete_data = $lab_result->delete_result($refno,$code);

        $objResponse->call('refreshPage');
        return $objResponse;
    }

    function saveOfficialResult($refno, $group_id, $is_served, $service_code='', $pid){
        global $db, $HTTP_SESSION_VARS;

        $objResponse = new xajaxResponse();
        $srv=new SegLab;

        if ($is_served)
            $date_served = date("Y-m-d H:i:s");
        else
            $date_served = '';

        $save = $srv->OfficialLabResult($refno, $group_id, $is_served, $date_served, $service_code);

        if ($save){
            $objResponse->call("ReloadWindow",$pid);
        }

        return $objResponse;

    }

    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/care_api_classes/class_globalconfig.php');
    
    require_once($root_path.'include/care_api_classes/class_lab_results.php');
    require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
    
    require($root_path.'modules/laboratory/ajax/lab-result-new.common.php');
    
    $xajax->processRequest();
?>