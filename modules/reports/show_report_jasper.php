<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    #require_once($root_path.'include/inc_environment_global.php');
    
    global $db;
    
    $report = $_GET['reportid'];
    $repformat = $_GET['repformat'];
    
    $fromdte = $_GET['from_date'];
    $todte = $_GET['to_date'];
    $from_date = strftime("%Y-%m-%d", $fromdte);
    $to_date   = strftime("%Y-%m-%d", $todte);
    
    $param = $_GET['param'];
    
    $params = array();
    $params[] = array("from_date", $from_date, 'java.lang.String');
    $params[] = array("to_date", $to_date, 'java.lang.String');
    
    $with_icd10_class = 0;
    
    #additional parameters
    $paramsarr = explode(",",$param);
    #print_r($paramsarr);
    if (count($paramsarr)){
        while (list($key,$val) = each($paramsarr))  {
            $val_arr = explode("--", trim($val));
            
            $id = $val_arr[0];
            $value = $val_arr[1];
            
            #if parameter is time
            if (stristr($id,'time')){
              $value = date("H:i:s",strtotime($value));
            }
            
            $param_id = substr($id, 6);
            #icd 10 classification
            if ($param_id=='type_nr'){
                $with_icd10_class  = 1;
                if ($value=='all'){
                    $value1 = '0';
                    $value2 = '1';
                }else{
                    $value1 = $value;
                    $value2 = $value;
                }    
                    
                $params[] = array($param_id.'1', $value1, 'java.lang.String');
                $params[] = array($param_id.'2', $value2, 'java.lang.String');        
            }else
                $params[] = array($param_id, $value, 'java.lang.String');
            
            #$params[] = array($param_id, $value, 'java.lang.String');
        }
    }
    
    if (!$with_icd10_class){
        $params[] = array('type_nr'.'1', '1', 'java.lang.String');
        $params[] = array('type_nr'.'2', '1', 'java.lang.String');        
    }
    #echo "param<br>";
    #print_r($params);
    
    #exit();
    include($root_path.'modules/reports/render_report_jasper.php');
?>
