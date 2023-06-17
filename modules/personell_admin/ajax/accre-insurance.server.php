<?php
    #edited by VAN 04-17-08
    function populateInsurance($sElem,$keyword, $personell_nr, $page) {
        global $db;
        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('pagin_insurance_search_max_block_rows');
        $maxRows = $GLOBAL_CONFIG['pagin_insurance_search_max_block_rows'];

        $objResponse = new xajaxResponse();
        $insObj=new Insurance;
        $offset = $page * $maxRows;

        $searchkey = utf8_decode($searchkey);
        $total_srv = $insObj->countSearchSelect($keyword,$maxRows,$offset);
        #$objResponse->addAlert($insObj->sql);
        $total = $insObj->count;
        #$objResponse->addAlert('total = '.$total);

        $lastPage = floor($total/$maxRows);
        #$objResponse->addAlert('total = '.$lastPage);
        if ((floor($total%10))==0)
            $lastPage = $lastPage-1;

        if ($page > $lastPage) $page=$lastPage;
        $ergebnis=$insObj->SearchSelect($keyword,$maxRows,$offset);
        #$objResponse->addAlert("sql = ".$insObj->sql);
        $rows=0;

        $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
        $objResponse->addScriptCall("clearList","product-list");
        if ($ergebnis) {
            $rows=$ergebnis->RecordCount();
            while($result=$ergebnis->FetchRow()) {

                #added by VAN 08-14-08
                $personell_Insinfo = $insObj->getPersonnelAccreditationInfo($personell_nr, trim($result["hcare_id"]));
                $objResponse->addScriptCall("addProductToList","product-list",trim($result["hcare_id"]),trim($result["firm_id"]),trim($result["name"]), $personell_Insinfo['accreditation_nr'], $cnt);
            }#end of while
        } #end of if

        if (!$rows) $objResponse->addScriptCall("addProductToList","product-list",NULL);
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }

        return $objResponse;
    }

    #-------added by VAN 11-04-09
    function setDeactivatePersonnel($personell_nr, $deactivate){
            global $db, $HTTP_SESSION_VARS;
            $objResponse = new xajaxResponse();

            $sql_u = "SELECT * FROM care_users WHERE personell_nr='".$personell_nr."'";
            $rs_u = $db->Execute($sql_u);
            $with_account = $rs_u->RecordCount();

            $sql_perinfo = "SELECT pid FROM care_personell WHERE nr='".$personell_nr."'";
            $rs_perinfo = $db->Execute($sql_perinfo);
            $row_perinfo = $rs_perinfo->FetchRow();
            $pid = $row_perinfo['pid'];

            if ($deactivate){
                    $history = "CONCAT(history,'Deactivate Personnel: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n')";
                    $datenow = date('Y-m-d');
                    //$date_exit = date( "Y-m-d", strtotime( "$datenow -1 day" ));

                    $sql_personell = "UPDATE care_personell SET
                                                                date_exit='".$datenow."',
                                                                contract_end='".$datenow."',
                                                                status='deleted',
                                                                history = $history,
                                                                modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
                                                                modify_time = '".date('Y-m-d H:i:s')."'
                                                                WHERE nr='".$personell_nr."'";

                    $sql_personell_assign = "UPDATE care_personell_assignment SET
                                                                date_end='".$datenow."',
                                                                status='deleted',
                                                                history = $history,
                                                                modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
                                                                modify_time = '".date('Y-m-d H:i:s')."'
                                                                WHERE personell_nr='".$personell_nr."'";

                    $sql_personell_dependent = "UPDATE seg_dependents SET
                                                                                status='deleted',
                                                                                history = $history,
                                                                                modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
                                                                                modify_dt = '".date('Y-m-d H:i:s')."'
                                                                                WHERE parent_pid='".$pid."'";

                    if ($with_account){
                        $sql_update_account = "UPDATE care_users SET lockflag=1 WHERE personell_nr='".$personell_nr."'";
                    }

            }else{
                    $history = "CONCAT(history,'Activate Personnel: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n')";
                    $sql_personell = "UPDATE care_personell SET
                                                                date_exit='',
                                                                contract_end='',
                                                                status='',
                                                                history = $history,
                                                                modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
                                                                modify_time = '".date('Y-m-d H:i:s')."'
                                                                WHERE nr='".$personell_nr."'";

                    $sql_personell_assign = "UPDATE care_personell_assignment SET
                                                                date_end='',
                                                                status='',
                                                                history = $history,
                                                                modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
                                                                modify_time = '".date('Y-m-d H:i:s')."'
                                                                WHERE personell_nr='".$personell_nr."'";

                    $sql_personell_dependent = "UPDATE seg_dependents SET
                                                                                status='member',
                                                                                history = $history,
                                                                                modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
                                                                                modify_dt = '".date('Y-m-d H:i:s')."'
                                                                                WHERE parent_pid='".$pid."'";

                    if ($with_account){
                        $sql_update_account = "UPDATE care_users SET lockflag=0 WHERE personell_nr='".$personell_nr."'";
                    }
            }

            $db->BeginTrans();

            #update care_personell
            $ok = $db->Execute($sql_personell);
            #update care_personell_assign
            if ($ok)
                $ok=$db->Execute($sql_personell_assign);

            if ($ok)
                $ok=$db->Execute($sql_personell_dependent);

            if (($ok)&& ($with_account))
                $ok = $db->Execute($sql_update_account);

            if ($ok){
                    $db->CommitTrans();
                    $objResponse->alert("The personnel's employment status is successfully change.");
                    $objResponse->addScriptCall("ReloadWindow");
            }else{
                    $db->RollbackTrans();
                    $objResponse->alert("Changing personnel's employment status is failed.");
            }


            return $objResponse;
    }

    function setChangePassword($personell_nr,$password){
            global $db, $HTTP_SESSION_VARS;
            $objResponse = new xajaxResponse();


            $history = "CONCAT(history,'Change Password: ".date('Y-m-d H:i:s')." [".addslashes($_SESSION['sess_user_name'])."]\n')";
            $sql = "UPDATE care_users SET
                                                                password=md5('".$password."'),
                                                                history = $history,
                                                                modify_id = '".$HTTP_SESSION_VARS['sess_user_name']."',
                                                                modify_time = '".date('Y-m-d H:i:s')."'
                                                                WHERE personell_nr='".$personell_nr."'";
            #$objResponse->alert($password);
            #$objResponse->alert($sql);
            $db->BeginTrans();

            #update care_personell
            $ok = $db->Execute($sql);

            if ($ok){
                    $db->CommitTrans();
                    $objResponse->alert("The personnel's password status is successfully change.");
                    $objResponse->addScriptCall("ReloadWindow");
            }else{
                    $db->RollbackTrans();
                    $objResponse->alert("Changing personnel's password status is failed.");
            }

            return $objResponse;
    }
    #-------------------------------

    require_once('./roots.php');

    require($root_path.'include/inc_environment_global.php');
    require($root_path."modules/personell_admin/ajax/accre-insurance.common.php");
    #added by VAN 04-17-08
    require_once($root_path.'include/care_api_classes/class_globalconfig.php');
    require_once($root_path.'include/care_api_classes/class_insurance.php');
    $xajax->processRequests();
?>