<?php
   //created by cha 10-13-09
   function searchPharmaWalkin($searchID, $page)
   {
        global $db;
        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);       
        $glob_obj->getConfig('pagin_patient_search_max_block_rows');
        $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
        $objResponse = new xajaxResponse();
        $walkinObj = new SegWalkin();
        
        $offset = $page * $maxRows;
        $total_guarantor = $walkinObj->countWalkin($searchID);
        $total = $walkinObj->count;
        $lastPage = floor($total/$maxRows);
        
        if ((floor($total%10))==0)
            $lastPage = $lastPage-1;
        
        if ($page > $lastPage) $page=$lastPage;
        $dataRow=$walkinObj->getWalkinDetails($searchID,0,$maxRows,$offset);
        #echo $dataRow;
        $rows=0;
        #echo $pharmaObj->sql;
        $objResponse->call("setPagination",$page,$lastPage,$maxRows,$total);
        $objResponse->call("clearList","WalkinList");
        if ($dataRow) {
            $rows=$dataRow->RecordCount();
            while($result=$dataRow->FetchRow())
            {
                $objResponse->call("viewPharmaWalkinList","WalkinList",trim($result["pid"]),trim($result["name"]),trim($result["address"]),trim($result["create_time"]));
                //$objResponse->alert("viewGuarantorList: ".trim($result["account_id"])." ".trim($result["account_name"])." ".trim($result["account_title"])); 
            }#end of while            
        } #end of if
        if (!$rows) $objResponse->call("viewPharmaWalkinList","WalkinList",NULL);
        $objResponse->call("endAJAXSearch",$sElem); 
            
        return $objResponse;
   }
   
   function saveNewAccount($id,$lastname,$firstname,$gender,$address,$birthdate)   
   {
        global $db;
        $objResponse = new xajaxResponse();
        $walkinObj = new SegWalkin();
        $output=$walkinObj->saveAccountDetails($id,$lastname,$firstname,$gender,$address,$birthdate);
        if($output)
        {
            $objResponse->call("refreshFrame","Save successful!");
        }
        else
        {
            $objResponse->call("refreshFrame","Save not successful!");
        }
        #echo "add query: ".$walkinObj->sql;                  
        //echo "donate time=".$donate_time;
        return $objResponse;
   }
   
   function deleteWalkin($delID)
   {
     global $db;
     $objResponse = new xajaxResponse();
     $walkinObj = new SegWalkin();
     $output = $walkinObj->deleteWalkin($delID);
     //echo $bloodObj->sql;
     if($output)
      {
        $objResponse->alert("Delete successful!");
      }
      else
      {
        $objResponse->alert("Delete not successful!");
      }  
     return $objResponse;
   }
   
   function saveEditAccount($id,$lastname,$firstname,$gender,$address,$birthdate)
   {
        global $db;
        $objResponse = new xajaxResponse();
        $walkinObj = new SegWalkin();
        $output = $walkinObj->saveEditDetails($id,$lastname,$firstname,$gender,$address,$birthdate);
        if($output)
        {
            $objResponse->call("refreshFrame","Save successful!");
        }
        else
        {
            $objResponse->call("refreshFrame","Save not successful!");
        }
        #echo "last query: ".$walkinObj->sql;                  
        return $objResponse;
   }
   
   function getPID()
   {
       	global $db;
        $objResponse = new xajaxResponse();
        $walkinObj = new SegWalkin();
        $output = $walkinObj->createPID();
        if($output)
        {
            $objResponse->call("setPID",$output);
        }
        else
        {
            $objResponse->call("setPID","null");
        }
        #echo "last query: ".$walkinObj->sql;                  
        return $objResponse;
	 }
   
   require('./roots.php');
   include_once($root_path.'include/care_api_classes/class_globalconfig.php');
   require($root_path.'include/inc_environment_global.php');    
   require($root_path.'include/care_api_classes/class_walkin.php');
   require($root_path.'modules/pharmacy/ajax/pharma-walkin.common.php');
   $xajax->processRequest();
?>
