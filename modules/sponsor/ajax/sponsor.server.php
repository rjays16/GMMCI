<?php
  #define('__DEBUG_MODE',0);

  function populatePatientRequestList($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
    
    $objResponse = new xajaxResponse();  
    $sc = new SegSponsor();
    
    $filters = array();
    $filters["PID"] = $args["pid"];
    
    $offset = $page_num * $max_rows;
    $sortColumns = array('date','source','refno','name','qty','total');
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
    else $sort_sql = 'source ASC';
    
    $result=$sc->getRequestItems($filters, $offset, $max_rows, $sort_sql);
    if($result) {
      $found_rows = $sc->FoundRows();
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
        
        $source_text = array(
          'FB'=>'Hospital bill',
          'LD'=>'Laboratory',
          'RD'=>'Radiology',
          'PH'=>'Meds/Supplies',
          'OP'=>'Operating room',
        );
        
        while($row = $result->FetchRow()) {
          
          $DATA[$i]['source'] = $source_text[$row['source']];
          $DATA[$i]['refno'] = $row['refno'];
          $DATA[$i]['date'] = nl2br(date("d-M-Y\nh:ia", strtotime($row['date'])));
          $DATA[$i]['itemno'] = $row['itemno'];
          $DATA[$i]['name'] = $row['name'];
          $DATA[$i]['qty'] = $row['qty'];
          $DATA[$i]['total'] = $row['total'];
          $DATA[$i]['discounted'] = $row['discounted'];
          $DATA[$i]['status'] = '';
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
          $objResponse->alert($sc->sql);
        else
          $objResponse->alert("A database error has occurred. Please contact your system administrator...");
          
      $objResponse->contextAssign('dataSize', -1);
      $objResponse->contextAssign('listData', NULL);
    }  
    
    $objResponse->script('this.fetchDone()');
    return $objResponse;
  }

	
	function populatePatientBillingAccounts($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
    
	  $objResponse = new xajaxResponse();	
	  $sc = new SegSponsor();
    
    $filters = array();
    $filters["PID"] = $args["pid"];
    
	  $offset = $page_num * $max_rows;
	  $sortColumns = array('nr','date');
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
	  else $sort_sql = 'date ASC';
    
	  $result=$sc->getBillingEncounters($filters, $offset, $max_rows, $sort_sql);
	  if($result) {
		  $found_rows = $sc->FoundRows();
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

			    $DATA[$i]['nr'] = $row['nr'];
			    $DATA[$i]['date'] = nl2br(date("F j, Y\nh:ia", strtotime($row['date'])));
          $DATA[$i]['status'] = '';
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
          $objResponse->alert($sc->sql);
        else
          $objResponse->alert("A database error has occurred. Please contact your system administrator...");
          
		  $objResponse->contextAssign('dataSize', -1);
		  $objResponse->contextAssign('listData', NULL);
	  }

    $objResponse->script('this.fetchDone()');
	  return $objResponse;
  }
  
  function populateBillingBreakdown($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
    
    $objResponse = new xajaxResponse();  
    $bc = new SegBillingGrant();
    $totals=$bc->getBillAreaTotals($args['nr']);
    
    if($totals) {
      $areas = array(
        'acc'=>'Room and Accommodation',
        'med'=>'Drugs and Medicines',
        'srv'=>'X-Ray, Lab and Other Charges',
        'ops'=>'Operating/Delivery Room',
        'doc'=>'Doctor\'s Fees',
        'msc'=>'Miscellaneous Charges'
      );
      
      $data_size = sizeof($areas);
      $objResponse->contextAssign('currentPage', 0);
      $objResponse->contextAssign('lastPage', 0);
      $objResponse->contextAssign('maxRows', $max_rows);
      $objResponse->contextAssign('listSize', sizeof($areas));

      $DATA = array();
      $i=0;

      foreach ($areas as $idx=>$area) {
        $DATA[$i]['nr'] = $args['nr'];
        $DATA[$i]['code'] = $idx;
        $DATA[$i]['area'] = $area;
        
        $total=$totals[$idx.'_total'];
        $DATA[$i]['total'] = $total;
        
        $grant=$totals[$idx.'_grant'];
        $status = ((float) $grant < (float) $total) ? 0 : 1;          
        
        $DATA[$i]['status'] = $status;
        $DATA[$i]['FLAG'] = 1;
        $i++;
      } //end while

      $objResponse->contextAssign('dataSize', $data_size);
      $objResponse->contextAssign('listData', $DATA);
            
    } else {
      // error
      if (defined('__DEBUG_MODE'))
        $objResponse->alert($bc->sql);
      else
        $objResponse->alert("A database error has occurred. Please contact your system administrator...");          
      $objResponse->contextAssign('dataSize', -1);
      $objResponse->contextAssign('listData', NULL);
    }  
    
    $objResponse->script('this.fetchDone()');
    return $objResponse;
  }
  
  function populateBreakdownDetails($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
    
    $objResponse = new xajaxResponse();  
    $gc = new SegBillingGrant();
    $offset = $page_num * $max_rows;

    $sortColumns = array('`account_name`',' `total`','`grant`');
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
    else $sort_sql = '`account_name` ASC';
    $result=$gc->getBillingAccounts($args['nr'], $args['area'], $offset, $max_rows, $sort_sql);
    //$objResponse->alert($gc->sql);
    if($result) {
      $found_rows = $gc->FoundRows();
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
             
          $DATA[$i]['account_id'] = $row['account_type'];
          $DATA[$i]['account'] = $row['account_name'];
          $DATA[$i]['source'] = 'FB';
          $DATA[$i]['nr'] = $args['nr'];
          $DATA[$i]['area'] = $args['area'];
          $DATA[$i]['total'] = $row['total'];
          $DATA[$i]['grant'] = $row['grant'];

          $DATA[$i]['status'] = '';
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
          $objResponse->alert("Empty recordset: " . $gc->sql);
      }
      
    } else {
      // error
        if (defined('__DEBUG_MODE'))
          $objResponse->alert('SQL Error: '.$gc->sql);
        else
          $objResponse->alert("A database error has occurred. Please contact your system administrator...");
          
      $objResponse->contextAssign('dataSize', -1);
      $objResponse->contextAssign('listData', NULL);
    }  
    
    $objResponse->script('this.fetchDone()');
    //$objResponse->script('doneLoading()');
    return $objResponse;
  }

  function populateDetails($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
    
    $objResponse = new xajaxResponse();  
    $sc = new SegSponsor();
    $offset = $page_num * $max_rows;

    $sortColumns = array('`description`');
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
    else $sort_sql = '`description` ASC';
    $result=$sc->getRequestDetails($args['source'], $args['nr'], $offset, $max_rows, $sort_sql);
    
    if($result) {
      $found_rows = $sc->FoundRows();
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
             
          $DATA[$i]['source'] = $args['source'];
          $DATA[$i]['nr'] = $args['nr'];
          $DATA[$i]['code'] = $row['code'];
          $DATA[$i]['description'] = $row['description'];
          $DATA[$i]['quantity'] = $row['quantity'];
          $DATA[$i]['price'] = $row['price'];
          $DATA[$i]['discount'] = $row['discount'];
          $DATA[$i]['status'] = '';
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
          $objResponse->alert("Empty recordset: " . $sc->sql);
      }
      
    } else {
      // error
        if (defined('__DEBUG_MODE'))
          $objResponse->alert($sc->sql);
        else
          $objResponse->alert("A database error has occurred. Please contact your system administrator...");
          
      $objResponse->contextAssign('dataSize', -1);
      $objResponse->contextAssign('listData', NULL);
    }  
    
    $objResponse->script('this.fetchDone()');
    return $objResponse;
  }
  
  function populateGrants($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
    
    $objResponse = new xajaxResponse();
    $gc = new SegGrant();
    $offset = $page_num * $max_rows;
    
    $sortColumns = array('`create_time`','`account_name`','`amount`','`create_id`');
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
    else $sort_sql = '`account_name` ASC';
    
    $result=$gc->getGrantsPerRequest($args['src'], $args['nr'], $args['code'], $args['area'], $offset, $max_rows, $sort_sql);
    
    if($result) {
      $found_rows = $gc->FoundRows();
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
          $DATA[$i]['id'] = $row['grant_no'];
          $DATA[$i]['acct_id'] = $row['account_id'];
          $DATA[$i]['acct_name'] = $row['account_name'];
          $DATA[$i]['date'] = $row['create_time'];
          $DATA[$i]['encoder'] = $row['create_id'];
          $DATA[$i]['amount'] = $row['amount'];
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
          $objResponse->alert('Empty Recordset: '.$gc->sql);
      }
      
    } else {
      // error
        if (defined('__DEBUG_MODE'))
          $objResponse->alert($gc->sql);
        else
          $objResponse->alert("A database error has occurred. Please contact your system administrator...");
          
      $objResponse->contextAssign('dataSize', -1);
      $objResponse->contextAssign('listData', NULL);
    }  
    
    $objResponse->script('this.fetchDone()');
    return $objResponse;
  }
  
  function refreshTotalGrant($src, $nr, $code) {    
    $objResponse = new xajaxResponse();
    $gc = new SegGrant();
    if ($grant=$gc->getTotalGrant($src, $nr, $code)) {
      $objResponse->script('$(\'grant-payable\').value=numberFormat( parseFloatEx($(\'grant-total\').value)-'.$grant.', 2)');
    }
    else {
    
    }
    return $objResponse;
  }
  
  function addGrant($src, $nr, $code, $area, $id, $amt) {
    $objResponse = new xajaxResponse();
    $gc = new SegGrant();
    if ($gc->grant($src, $nr, $code, $area, $id, $amt)) {
      $objResponse->script('blst.reload()');
      $objResponse->script('dlst.reload()');
      $objResponse->script('glst.reload()');      
      $objResponse->alert('Grant request successfully saved...');
      $objResponse->script('$(\'grant-amount\').value=\'0.00\'');
      
      if ($grant=$gc->getTotalGrant($src, $nr, $code, $area)) {
        $objResponse->script('$(\'grant-payable\').value=formatNumber( parseFloatEx($(\'grant-total\').value)-'.$grant.', 2)');
        //$objResponse->alert('$(\'grant-payable\').value=numberFormat( parseFloatEx($(\'grant-total\').value)-'.$grant.', 2)');
      }
      else
        $objResponse->alert($gc->sql);
    }
    else
      $objResponse->alert('Error occurred in adding request grant...'.$gc->sql);
    return $objResponse;
  }
  
  function deleteGrant($nr) {
    $objResponse = new xajaxResponse();
    $gc = new SegGrant();
    if ($gc->delete($nr)) {
      $objResponse->script('blst.reload()');
      $objResponse->script('dlst.reload()');
      $objResponse->script('glst.reload()');      
      $objResponse->alert('Grant request successfully deleted...');
      $objResponse->script('$(\'grant-amount\').value=\'0.00\'');
    }
    else
      $objResponse->alert('Error occurred in adding request grant...'.$gc->sql);
    return $objResponse;
  }
  
  function populateBillGrantAccounts($page_num=0, $max_rows=10, $sort_obj=NULL, $args=NULL) {
    
    $objResponse = new xajaxResponse();  
    $bc = new SegBillingGrant();
    
    $offset = $page_num * $max_rows;
    $sortColumns = array('account_name','amount','is_paid');
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
    else $sort_sql = 'account_name ASC';
    
    $result=$bc->populateAutoComputedEntries($args['nr'], 'FB', $offset, $max_rows, $sort_sql);
    if($result) {
      $found_rows = $bc->FoundRows();
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
          
          $DATA[$i]['id'] = $row['account_id'];
          $DATA[$i]['name'] = $row['account_name'];
          $DATA[$i]['amount'] = $row['amount'];
          $DATA[$i]['status'] = $row['is_paid'];
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
          $objResponse->alert($bc->sql);
      }
      
    } else {
      // error
        if (defined('__DEBUG_MODE'))
          $objResponse->alert($bc->sql);
        else
          $objResponse->alert("A database error has occurred. Please contact your system administrator...");
          
      $objResponse->contextAssign('dataSize', -1);
      $objResponse->contextAssign('listData', NULL);
    }  
    
    $objResponse->script('this.fetchDone()');
    return $objResponse;
  }

 	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');
	require($root_path.'include/care_api_classes/sponsor/class_sponsor.php');
  require($root_path.'include/care_api_classes/sponsor/class_grant.php');
  require($root_path.'include/care_api_classes/sponsor/class_billing_grant.php');
	require_once($root_path.'modules/sponsor/ajax/sponsor.common.php');
	$xajax->processRequest();
?>