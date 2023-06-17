<?php

  //added by raisa 01-21-09
    function populateMedCertEncRef($pid,$sElem,$page,$searchkey,$med_cert) {
        global $db;
        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('pagin_patient_search_max_block_rows');
        $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
        $objResponse = new xajaxResponse();
        $enc=new Encounter;
        $offset = $page * $maxRows;
        $searchkey = utf8_decode($searchkey);
        if(strpos($searchkey,"/"))
        {
            list($m,$d,$y) = explode("/",$searchkey);
            $searchkey = $y."-".$m."-".$d;
        }
        //$objResponse->addAlert('key = '.$pid);
        #--------
        /*if (stristr($searchkey,",")){
            $keyword_multiple = explode(",",$searchkey);
            
            for ($i=0;$i<sizeof($keyword_multiple);$i++){
                $keyword .= "'".trim($keyword_multiple[$i])."',";
            }
            #$objResponse->addAlert('keyword1 = '.$keyword);
            $word = trim($keyword);
            #$objResponse->addAlert('word = '.$word);
            $searchkey = substr($word,0,strlen($word)-1);
            #$objResponse->addAlert('keyword = '.$keyword);
            $multiple = 1;
        }else{
            $multiple = 0;
        }*/
        #----------------
        
        $total_srv = $enc->countSearchEncRefMedCertList($pid, $searchkey, $med_cert, $maxRows, $offset);
        $total_srv = 0;
        //$objResponse->addAlert($enc->sql);
        $total = $enc->count;
        //$objResponse->addAlert('total = '.$total);
        
        $lastPage = floor($total/$maxRows);
        
        if ((floor($total%10))==0)
            $lastPage = $lastPage-1;
        
        if ($page > $lastPage) $page=$lastPage;
        $ergebnis=$enc->SearchEncRefMedCertList($pid, $searchkey, $med_cert, $maxRows, $offset);
        #$objResponse->addAlert("sql = ".$enc->sql);
        $rows=0;
        $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
        $objResponse->addScriptCall("clearList","product-list");
        //$objResponse->addAlert("sql = ".$ergebnis);
        if ($ergebnis) {
            //$objResponse->addAlert("sql = ".$enc->sql);
            $rows=$ergebnis->RecordCount();
            while($result=$ergebnis->FetchRow()) {
                if($med_cert=="true")
                {
                    //$objResponse->addAlert("sa medcert");
                    $date_prepared=substr($result["create_dt"],0,10);
                    $objResponse->addScriptCall("addProductToList","product-list",$result["encounter_nr"]."-".$result["referral_nr"],$date_prepared,$result["encounter_nr"],$result["referral_nr"],$result["date_admit"],$result["dept"], $result["prepared_by"], $result["cert_nr"], $result["dr"]);
                }
                else
                {
                    //$objResponse->addAlert("sa dili medcert");
                    $date_admit=substr($result["admit_date"],0,10);
                    if($result["encounter_type"]==1)
                        $enc_type="ER";
                    else if($result["encounter_type"]==2)
                        $enc_type="OPD";
                    else if($result["encounter_type"]==3 || $result["encounter_type"]==4)
                        $enc_type="IPD";
                    $objResponse->addScriptCall("addProductToList","product-list",$result["encounter_nr"]."-".$result["referral_nr"],$result["encounter_nr"],$result["referral_nr"],$result["dept"],$enc_type,$date_admit);
                }
            }#end of while
        } #end of if
        
        if (!$rows) $objResponse->addScriptCall("addProductToList","product-list",NULL); 
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }    
        return $objResponse;         
    }
    
    
    #added by VAN 01-22-09
    require('./roots.php');
    require($root_path.'include/inc_environment_global.php');    
    require($root_path."modules/registration_admission/ajax/med_cert.common.php");
    #added by VAN 04-17-08
    require_once($root_path.'include/care_api_classes/class_globalconfig.php');
    require($root_path.'include/care_api_classes/class_encounter.php');
    
    $xajax->processRequests();       
    #-------------   
?>