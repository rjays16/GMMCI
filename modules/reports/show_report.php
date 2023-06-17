<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    global $db;
    
    $report = $_GET['reportid'];
    $repformat = $_GET['repformat'];
    $report_name = $report;
     
    $fromdte = $_GET['from_date'];
    $todte = $_GET['to_date'];
    $from_date = strftime("%Y-%m-%d", $fromdte);
    $to_date   = strftime("%Y-%m-%d", $todte);
    
    $from_date_format = strftime("%Y-%m-%d", $fromdte);
    $to_date_format   = strftime("%Y-%m-%d", $todte);
    
    $param = $_GET['param'];

    #added by daryl
    $paramsarr = explode(",",$param); 

    if (count($paramsarr)){
        while (list($key,$val) = each($paramsarr))  {
            $val_arr = explode("--", trim($val));
            
            $id = $val_arr[0];
            $value = $val_arr[1];
            
            $param_id = substr($id, 6);

          //added by daryl
            //for Mandatory Monthly Hospital Report
            if($param_id=='MMHR_month'){
                $mmhr_month = $value;
            }
            if($param_id=='MMHR_year'){
                $mmhr_year = $value;
            }
            if($param_id=='MMHR_page'){
                $mmhr_page = $value;
            }
          //ended by daryl
        }
    }
        

    #print_r($param);
    #exit();    
    include($root_path.'modules/reports/render_report.php');
?>
