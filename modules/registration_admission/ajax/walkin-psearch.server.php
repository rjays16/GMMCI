<?php

  function populateWalkin($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
    
    $objResponse = new xajaxResponse();
    $wc = new SegWalkin();
    
    $offset = $page_num * $max_rows;
    $sortColumns = array('pid','sex','fullname','address','last_transaction');
    $sort = array();
    if (is_array($sort_obj)) {
      foreach ($sort_obj as $i=>$v) {
         if ($col = ($sortColumns[$i] ? $sortColumns[$i] : FALSE)) {
           if ((int)$v < 0) $sort[] = "$col DESC";
           elseif ((int)$v > 0) $sort[] = "$col ASC";
         }
      }
    }
    if ($sort) $sort_sql = implode(',', $sort);
    else $sort_sql = 'name_full ASC';
    
    $filters = array();
    $filters["KEY"] = $args[0];
    $filters["OFFSET"] = $offset;
    $filters["MAXROWS"] = $max_rows;
    
    $result=$wc->getWalkin($filters);
    if($result) {
      $found_rows = $wc->FoundRows();
      $last_page = ceil($found_rows/$max_rows)-1;
      if ($page_num > $last_page) $page_num=$last_page;
      
      if($data_size=$result->RecordCount()) {
        $temp=0;
        $i=0;
        $objResponse->contextAssign('currentPage', $page_num);
        $objResponse->contextAssign('lastPage', $last_page);
        $objResponse->contextAssign('maxRows', $max_rows);
        $objResponse->contextAssign('listSize', $found_rows);

        $DATA = array();
        
        while($row = $result->FetchRow()) {
          
          $DATA[$i]['id'] = $row['pid'];
          $DATA[$i]['sex'] = $row['sex'];
          $DATA[$i]['fullname'] = $row['fullname'];
          $DATA[$i]['address'] = $row['address'];
          
          $date = $row['lastTransaction'];
          if (!$date || $date="0000-00-00 00:00:00") 
            $DATA[$i]['lastTransaction'] = "No record";
          else
            $DATA[$i]['lastTransaction'] = nl2br(date("d-M-Y\nh:ia", strtotime($date)));
          $DATA[$i]['FLAG'] = 1;
          $i++;
        } //end while
        
        $objResponse->contextAssign('dataSize', $data_size);
        $objResponse->contextAssign('listData', $DATA);
      }
      else {
        $objResponse->contextAssign('dataSize', 0);
        $objResponse->contextAssign('listData', NULL);
        
        if (defined('__DEBUG_MODE'))
          $objResponse->alert($sc->sql);
      }
      
    } else {
      // error
        if (defined('__DEBUG_MODE'))
          $objResponse->alert($wc->sql);
        else
          $objResponse->alert("A database error has occurred. Please contact your system administrator...");
          
      $objResponse->contextAssign('dataSize', -1);
      $objResponse->contextAssign('listData', NULL);
    }  
    
    $objResponse->script('this.fetchDone()');
    return $objResponse;
  }

	require('./roots.php');
	require_once($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/care_api_classes/class_walkin.php');
	require_once($root_path."modules/registration_admission/ajax/walkin-psearch.common.php");
	
	$xajax->processRequest();
