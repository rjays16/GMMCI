<?php
	
	function deleteRequest($refno){
		global $db;
		$srv=new SegLab;
		$objResponse = new xajaxResponse();
		
		#$objResponse->addAlert("ajax deleteRequest refno = $refno");
		/*
		$sql = "SELECT * FROM seg_pay_request AS pr
				INNER JOIN seg_pay AS p ON p.or_no=pr.or_no
              WHERE ref_source = 'LD' AND ref_no = '$refno' 
			 AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00')";
		 */
		 $sql = "SELECT * FROM seg_pay_request
              WHERE ref_source = 'LD' AND ref_no = '$refno'";
			  
         #$objResponse->addAlert("sql = ".$sql);
		 $res=$db->Execute($sql);
		 
		 #$row=$res->RecordCount();
		 #$objResponse->addAlert("row = ".$row);
		 					
		if ($row==0){		  
		
			$status=$srv->deleteRequestor($refno);
			
			if ($status) {
				$srv->deleteLabServ_details($refno);
				$objResponse->addScriptCall("removeRequest",$refno);
				#$objResponse->addAlert("deleteRequest sql = ".$srv->sql);
				#$objResponse->addScriptCall("reload_page");
				$objResponse->addAlert("The request is successfully deleted.");
			}else
				$objResponse->addScriptCall("showme", $srv->sql);
		 }else{
		 		$objResponse->addAlert("The request cannot be deleted. It is already or partially paid.");
		 }
		return $objResponse;
	}
											
	#added by VAN 03-08-08	
	function populateLabServiceList($area='',$group_code,$sElem,$searchkey,$page,$lab_area='') {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		#$glob_obj->getConfig('pagin_or_patient_search_max_block_rows');
		#$maxRows = $GLOBAL_CONFIG['pagin_or_patient_search_max_block_rows']; # 5 rows
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
		
		$objResponse = new xajaxResponse();
		$srv=new SegLab;
		$offset = $page * $maxRows;
		$searchkey = utf8_decode($searchkey);
		
		#$objResponse->addAlert('key = '.$searchkey);
		#--------
		if (stristr($searchkey,",")){
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
		}
		#----------------

		$total_srv = $srv->countSearchService($group_code,$searchkey,$multiple,$maxRows,$offset,$area,$lab_area);
		#$objResponse->addAlert($srv->sql);
		$total = $srv->count;
		#$objResponse->addAlert('total = '.$total);
		
		$lastPage = floor($total/$maxRows);
		
		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;
		
		if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$srv->SearchService($group_code,$searchkey,$multiple, $maxRows,$offset,$area,$lab_area);
		#$objResponse->addAlert("sql = ".$srv->sql);
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","product-list");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
					 #$objResponse->addAlert("sql = ".$result["service_code"]."  - ".$result["price_cash"]);                                             
					#check if the service is socialized
			
					if ($result["is_socialized"]){
						$sql2 = "SELECT DISTINCT * FROM seg_service_discounts 
			   	   		   WHERE service_code='".$result["service_code"]."'";
                          # $objResponse->addAlert("sql = ".$sql2);  
						$res=$db->Execute($sql2);
						$row=$res->RecordCount();
					
						if ($row!=0){
							while($rsObj=$res->FetchRow()) {
								if ($rsObj["discountid"] == C1){
									$price_C1 = $rsObj["price"];
								}	
								if ($rsObj["discountid"] == C2){
									$price_C2 = $rsObj["price"];
								}	
								if ($rsObj["discountid"] == C3){
									$price_C3 = $rsObj["price"];
								}		
							}		
						}else{
                            $price_C1 = '';
                            $price_C2 = '';  
                            $price_C3 = '';  
                            
                        }
					}else{
                       
						$price_C1 = number_format(trim($result["price_cash"]),2,'.', '');
						$price_C2 = number_format(trim($result["price_cash"]),2,'.', '');
						$price_C3 = number_format(trim($result["price_cash"]),2,'.', '');
					}	
				$objResponse->addScriptCall("addProductToList","product-list",trim($result["service_code"]),trim($result["name"]),number_format(trim($result["price_cash"]),2,'.', ''),number_format(trim($result["price_charge"]),2,'.', ''), $result["is_socialized"],$result["group_code"],$price_C1,$price_C2,$price_C3);
			}#end of while
		} #end of if

		if (!$rows) $objResponse->addScriptCall("addProductToList","product-list",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		
		return $objResponse;
	}
#---------------------
#commented by VAN 03-08-08
/*	
	function populateLabServiceList($group_code,$sElem,$keyword) {
		global $db;
		$objResponse = new xajaxResponse();
		
		$dead_stat= "'deleted','hidden','inactive','void'";
		
		#added by VAN 03-06-08
		# convert * and ? to % and &
		$keyword=strtr($keyword,'*?','%_');
		$keyword=trim($keyword);
		
		$keyword = str_replace("^","'",$keyword);

		$sql = "SELECT s.* FROM seg_lab_services AS s, seg_lab_service_groups AS g 
		   	  WHERE s.group_code=g.group_code 
				  AND s.group_code='$group_code'
				  AND (s.service_code LIKE '%".addslashes($keyword)."%' 
				  OR s.name LIKE '%".addslashes($keyword)."%')
				  AND s.status NOT IN ($dead_stat)
				  ORDER BY s.name";
				  		  
		#$objResponse->addAlert("populateLabServiceList sql = $sql");
		$ergebnis=$db->Execute($sql);
		$rows=$ergebnis->RecordCount();
		
		$objResponse->addScriptCall("clearList","product-list");
		while($result=$ergebnis->FetchRow()) {
			#--------------
			
			#check if the service is socialized
			
			if ($result["is_socialized"]){
				$sql2 = "SELECT DISTINCT * FROM seg_service_discounts 
			   	      WHERE service_code='".$result["service_code"]."'";
				$res=$db->Execute($sql2);
				$row=$res->RecordCount();
					
				if ($row!=0){
					while($rsObj=$res->FetchRow()) {
						if ($rsObj["discountid"] == C1){
							$price_C1 = $rsObj["price"];
						}	
						if ($rsObj["discountid"] == C2){
							$price_C2 = $rsObj["price"];
						}	
						if ($rsObj["discountid"] == C3){
							$price_C3 = $rsObj["price"];
						}		
					}		
				}
			}else{
				$price_C1 = number_format(trim($result["price_cash"]),2,'.', '');
				$price_C2 = number_format(trim($result["price_cash"]),2,'.', '');
				$price_C3 = number_format(trim($result["price_cash"]),2,'.', '');
			}	
			
			$objResponse->addScriptCall("addProductToList","product-list",trim($result["service_code"]),trim($result["name"]),number_format(trim($result["price_cash"]),2,'.', ''),number_format(trim($result["price_charge"]),2,'.', ''), $result["is_socialized"],$price_C1,$price_C2,$price_C3);
		}
		if (!$rows) $objResponse->addScriptCall("addProductToList","product-list",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		return $objResponse;
	}
*/	
	
	
	function populateServiceGroups($group_code) {
		global $db;
		$dbtable='seg_lab_services';
		$prctable = 'seg_pharma_prices';
		$objResponse = new xajaxResponse();
		
		if ($group_code) {
			# clean input data		
		   $sql="SELECT * FROM seg_lab_service_groups WHERE group_code='$group_code'";
		   $ergebnis=$db->Execute($sql);
			$rows=$ergebnis->RecordCount();	
			while($result=$ergebnis->FetchRow()) {
				$objResponse->addScriptCall("appendGroup",$result["group_code"],$result["name"]);
			}
		}
		return $objResponse;
	}
	
	function srvGui($grpCode, $grpName){
		$objResponse = new xajaxResponse();
		
		#$objResponse->addAlert("srvGui");
	
		$thead  =	"<thead class=\"\"><td colspan=\"4\">";
		$thead .=	"<table width=\"100%\" cellspacing=\"2\" cellpadding=\"2\" border=\"0\"><tr>";
		$thead .=    "<td width=\"*\" class=\"reg_header\">".$grpName."</td>";
		$thead .=	"<td width=\"1%\" align=\"right\" style=\"padding:2px;2px;font-weight:normal\" class=\"reg_header\">";
		$thead .=	"<span class=\"reglink\" onclick=\"toggleDisplay('grpBody".$grpCode."');\">Show/Hide</span>";
		$thead .=	"</td>";
		$thead .=    "</tr></table>";
		$thead .=	"</td></thead>";
				
		$thead1  =   "<thead id=\"grphead".$grpCode."\" class=\"reg_list_titlebar\" style=\"height:0;overflow:visible;font-weight:bold;padding:4px;\" id=\"srcRowsHeader\">";
		$thead1 .=   "<td width=\"1\"><input type=\"checkbox\" id=\"chk_all_".$grpCode."\" name=\"chk_all_".$grpCode."\" onChange=\"checkAll(this.checked);countItem('".$grpCode."', 1);\"></td>";
	
		$thead1	.=	 "<td width=\"15%\" nowrap>Code</td>";
		$thead1 .=   "<td width=\"60%\" nowrap>Description</td>";
		$thead1 .=	 "<td width=\"15%\" nowrap>Price</td>";
		$thead1	.=	 "</thead>";

		#$objResponse->addAlert("thead1->".$thead1);
	
		$tbody = "<tbody id=\"grpBody".$grpCode."\" style=\"height:0; overflow:visible\"></tbody>";
	
		#$objResponse->addAlert("grpCode->".$grpCode);
	
		$html = $thead.$thead1.$tbody;
		#$objResponse->addAlert($html);
		
		$objResponse->addAssign("srcRowsTable", "innerHTML", $html);
		
		return $objResponse;
	}

	
	function getAjxGui($group_code, $iscash, $refno, $serv_code){
		$objResponse = new xajaxResponse();
		
		$objResponse->addScriptCall("xajax_populateServices", $group_code, $iscash, $refno, $serv_code);
	
		return $objResponse;
	}
	
	
	function populateServices($group_code, $iscash, $refno, $serv_code) {
		global $db;		
		$objResponse = new xajaxResponse();
		
		if (($serv_code=="none")||($serv_code=="*")){
			$sql = "SELECT s.* FROM seg_lab_services AS s, seg_lab_service_groups AS g 
		   	     WHERE s.group_code=g.group_code 
					  AND s.group_code='$group_code'
				  	  ORDER BY s.name";
		}else{		  
			/*
			$sql = "SELECT s.* FROM seg_lab_services AS s, seg_lab_service_groups AS g 
		   	     WHERE s.group_code=g.group_code 
					  AND s.group_code='$group_code'
					  AND s.service_code LIKE '$serv_code%'
				  	  ORDER BY s.service_code";		  
			*/
			$sql = "SELECT s.* FROM seg_lab_services AS s, seg_lab_service_groups AS g 
		   	     WHERE s.group_code=g.group_code 
					  AND s.group_code='$group_code'
					  AND ((s.service_code LIKE '%$serv_code%') OR (s.name LIKE '%$serv_code%'))
				  	  ORDER BY s.name";			  
		}
		
	   #$objResponse->addAlert("populateServices sql : $sql");
		$objResponse->addScriptCall("ajxGetPrevReq",0);
		$ergebnis=$db->Execute($sql);
		$rows=$ergebnis->RecordCount();
		#$objResponse->addAlert("populateServices rows : $rows");
		
		if ($rows > 0 ){
			$objResponse->addScriptCall("ajxClearTable",$group_code);
			
			while($result=$ergebnis->FetchRow()) {
				
				$price=$iscash?$result["price_cash"]:$result["price_charge"];
				if (!$price) $price="N/A";
				else $price=number_format($price,2,'.','');
				
				if ($refno!=NULL){
					#$objResponse->addAlert("populateServices refno : NOT NULL");	
					$sql2 = "SELECT * FROM seg_lab_servdetails WHERE service_code = '".$result["service_code"]."' AND refno='".$refno."'";
	   			#$objResponse->addAlert("populateServices sql2 : $sql2");
					$res=$db->Execute($sql2);
					$row=$res->RecordCount();
					
					if ($row!=0){
						$rsObj=$res->FetchRow();
						#$objResponse->addAlert("populateServices rsObj : ".$rsObj["service_code"]);
						$servlist = $servlist.$rsObj["service_code"].",";
						#$objResponse->addAlert("populateServices servlist : ".$servlist);
						$objResponse->addScriptCall("ajxGetPrevReq",1,$servlist);
						#$objResponse->addScriptCall("ajxGetPrevReq",$rsObj["service_code"]);
						$chk = 1;
						
					}else{
						$chk = 0;	
					}	
				}else
					$chk = 0;			
				
				#$objResponse->addScriptCall("appendServiceItemToGroup",$result["group_id"],$result["service_code"],$result["name"],$price,$chk);
				
				$objResponse->addScriptCall("appendServiceItemToGroup",$result["group_code"],$result["service_code"],$result["name"],$price,$chk);
			}
		}else{
			 #$objResponse->addAlert("populateServices FALSE");
			 #$objResponse->addScriptCall("ajxGetPrevReq",0);
			 $objResponse->addScriptCall("ajxClearTable",$group_code);
			 $objResponse->addScriptCall("appendServiceItemToGroup2",$group_code);
		}	
		
		#$objResponse->addAlert(print_r($sql,TRUE));
		return $objResponse;
	}
	
	
	function addTransactionDetail($refno, $pid, $name, $price, $qty) {
		$pharma_obj=new SegPharma;
		$entryno=$pharma_obj->AddTransactionDetails($refno, $pid, $qty, $price);
		$objResponse = new xajaxResponse();
		if ($entryno) {
			$objResponse->addScriptCall("pharma_retail_gui_addDestProductRow", $pid, $name, $entryno, round($price,2), round($qty), TRUE);
			#$objResponse->addAlert($pharma_obj->sql);
		}

		return $objResponse;
	}
	
	function delTransactionDetail($refno, $entryno, $rowno) {
		$pharma_obj=new SegPharma;
		$result=$pharma_obj->RemoveTransactionDetails($refno, $entryno);
		$objResponse = new xajaxResponse();
		if ($result) {
			$objResponse->addScriptCall("pharma_retail_gui_rmvDestProductRow",$rowno);
		}
		//$objResponse->addAlert($pharma_obj->sql);
		return $objResponse;
	}
	
	function populateDetails($refno) {
		$pharma_obj=new SegPharma;
		$ergebnis=$pharma_obj->GetTransactionDetails($refno);
		$objResponse = new xajaxResponse();
		$objResponse->addScriptCall("pharma_retail_gui_clearDestRows");
		$recCount = $pharma_obj->result->RecordCount();
		$counter=0;
		if ($recCount>0) {
			while($result=$ergebnis->FetchRow()) {
				$counter++;
				$objResponse->addScriptCall("pharma_retail_gui_addDestProductRow",$result["bestellnum"],$result["artikelname"],$result["entrynum"],round($result["rpriceppk"],2),$result["qty"]-0, $counter==$recCount);
			}				
		}
		//$objResponse->addAlert(print_r($pharama_obj->sql,TRUE));
		return $objResponse;
	}
	
	function populateServiceList($keyword, $iscash) {
		global $db;
		$dbtable='care_pharma_products_main';
		$prctable = 'seg_pharma_prices';
		# clean input data		
		
		/*
		$sql="SELECT * FROM $dbtable WHERE  bestellnum LIKE '%$keyword%'
					OR artikelnum LIKE '%$keyword%'
					OR industrynum LIKE '%$keyword%'
					OR artikelname LIKE '%$keyword%'
					OR generic LIKE '%$keyword%'
					OR description LIKE '%$keyword%' ORDER BY artikelname";
		*/
			
		//$sql="SELECT * FROM $dbtable WHERE artikelname LIKE '%$keyword%' ORDER BY artikelname";
		$sql="SELECT a.*, b.ppriceppk, b.chrgrpriceppk, b.cshrpriceppk FROM $dbtable AS a LEFT JOIN $prctable AS b ON a.bestellnum=b.bestellnum WHERE artikelname REGEXP '[[:<:]]$keyword' ORDER BY artikelname";
	  $ergebnis=$db->Execute($sql);
		$rows=$ergebnis->RecordCount();
		$objResponse = new xajaxResponse();
		$objResponse->addScriptCall("pharma_retail_gui_clearSrcRows");
		
		
		while($result=$ergebnis->FetchRow()) {
/*
						$objResponse->addAlert($iscash);
			if ($iscash) {
				$objResponse->addAlert("IS CASH!!");
			}
			else {
				$objResponse->addAlert("IS CHARGE!!");
			}


			ob_start();
			var_dump($iscash);
			$sTemp = ob_get_contents();
			ob_end_clean();

			$objResponse->addAlert(print_r($sTemp,TRUE));
								*/
								
			$price=$iscash?$result["cshrpriceppk"]:$result["chrgrpriceppk"];
			if (!$price) $price="N/A";
			else $price=number_format($price,2,'.','');
			$objResponse->addScriptCall("pharma_retail_gui_addSrcProductRow",$result["bestellnum"],$result["artikelname"],  $price);
		}
		if (!$rows) $objResponse->addScriptCall("pharma_retail_gui_addSrcProductRow",NULL);
		
		//$objResponse->addAlert(print_r($sql,TRUE));
		return $objResponse;
	}
	
	function populateDiscountSelection() {
		global $db;
		$dbtable='seg_discount';
		$sql="SELECT * FROM $dbtable ORDER BY discountdesc";
	  $ergebnis=$db->Execute($sql);
		$rows=$ergebnis->RecordCount();
		$objResponse = new xajaxResponse();
		$objResponse->addScriptCall("clearDiscountOptions");
		
		$cntr=0;
		while($result=$ergebnis->FetchRow()) {
			$objResponse->addScriptCall("addDiscountOption",$result["discountid"],$result["discountdesc"], $result["discount"], !$cntr);
			$cntr++;
		}
		if (!$rows) $objResponse->addScriptCall("addDiscountOption",NULL);
		
		//$objResponse->addAlert(print_r($sql,TRUE));
		return $objResponse;
	}
	
	function addRetailDiscount($refno, $id, $desc, $discount) {
		$dscObj=new SegDiscount;
		$result=$dscObj->AddRetailDiscount($refno, $id, "pharma");
		$objResponse = new xajaxResponse();
		if ($result) {
			$objResponse->addScriptCall("gui_addRDiscountRow", $id, $desc, $discount, TRUE);
			$objResponse->addAlert("Discount added");
		}
		else {
			$objResponse->addAlert(print_r($dscObj->sql,TRUE));
		}
		
		//$objResponse->addAlert("refno:$refno, id=$id, desc=$desc, discount=$discount");
		return $objResponse;
	}
	
	function populateRetailDiscounts($refno) {
		global $db;
		$objResponse = new xajaxResponse();
		$objResponse->addScriptCall("gui_clearRDiscountRows");
		
		$dbtable='seg_discount';
		$rdtable='seg_pharma_rdiscount';
		$sql="SELECT a.* FROM $dbtable AS a, $rdtable AS b WHERE a.discountid=b.discountid AND b.refno='$refno'";
	  $ergebnis=$db->Execute($sql);
		$rows=$ergebnis->RecordCount();		
		$cntr=0;
		while($result=$ergebnis->FetchRow()) {
			//$objResponse->addAlert(print_r($result,TRUE));
			$objResponse->addScriptCall("gui_addRDiscountRow", $result['discountid'], $result['discountdesc'], $result['discount']);
			$cntr++;
		}
		return $objResponse;
	}
	
	function rmvRetailDiscount($refno, $discountid, $rowno) {
		$dscObj=new SegDiscount;
		$result=$dscObj->RemoveRetailDiscount($refno, $discountid, "pharma");
		$objResponse = new xajaxResponse();
		if ($result) {
			$objResponse->addScriptCall("gui_rmvRDiscountRow",$rowno);
		}
		else {
			$objResponse->addAlert($dscObj->sql);
		}

		return $objResponse;
	}
	
	#----------added by VAN 09-12-07
    /*
	function populateRequestList($done, $sElem,$searchkey,$page,$include_firstname,$mod, $encounter_nr='', $is_doctor=0 ) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
		
		$objResponse = new xajaxResponse();
		$srv=new SegLab;
		$dept_obj=new Department;
		$ward_obj = new Ward;
		$person_obj=new Person();
		
		$offset = $page * $maxRows;
		#$searchkey = strtr($searchkey, '*?', '%_');
		#$objResponse->addAlert('searchkey = '.utf8_decode($searchkey));
		
		#added by VAN 03-24-08
		$searchkey = utf8_decode($searchkey);
		
		if ($searchkey==NULL)
			$searchkey = 'now';
			
			#$objResponse->addAlert("mode = ".$mod);
		$total_srv = $srv->countSearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, $is_doctor, $encounter_nr);
		#$objResponse->addAlert($srv->sql);
		$total = $srv->count;
		#$objResponse->addAlert('total = '.$total);
		$lastPage = floor($total/$maxRows);
		#$objResponse->addAlert('total = '.floor($total%10));
		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;
		
		if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$srv->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, $is_doctor, $encounter_nr);
		#$objResponse->addAlert("sql = ".$srv->sql);
		$rows=0;
		
		#$objResponse->addAlert("pageno, lastpage, pagen, total = ".$page.", ".$lastPage.", ".$maxRows.", ".$total);
		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","RequestList");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
			   
				$urgency = $result["is_urgent"]?"Urgent":"Normal";
				if ($result["pid"]!=" ") 
					#$name = ucwords(strtolower(trim($result["name_first"])))." ".ucwords(strtolower(trim($result["name_middle"])))." ".ucwords(strtolower(trim($result["name_last"])));
					$name = ucwords(strtolower(trim($result["name_last"]))).", ".ucwords(strtolower(trim($result["name_first"])))." ".ucwords(strtolower(trim($result["name_middle"])));
				else
					$name = trim($result["ordername"]);
					
				if (!$name) $name='<i style="font-weight:normal">No name</i>';
				
				if ($result["serv_dt"]) {
					$date = strtotime($result["serv_dt"]);
					$time = strtotime($result["serv_tm"]);
					$requestDate = date("M d, Y",$date)." ".date("h:i A",$time);
				}
				
				#$objResponse->addAlert("type = ".$result["charge_name"]);
				
				#$objResponse->addAlert("type = ".$result2['or_no']);
				# check if this request is already paid or not
				#if ($mod){
				
					$sql = "SELECT pr.ref_no,pr.service_code, pr.or_no AS or_no 
							  FROM seg_pay_request AS pr
							  INNER JOIN seg_pay AS p ON p.or_no=pr.or_no
			  				  WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'
							  AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00')
						  UNION
  			              SELECT gr.ref_no,gr.service_code,IF(gr.grant_no,'CLASS D','') AS or_no  
						 	  FROM seg_granted_request AS gr
                       WHERE gr.ref_source = 'LD' AND gr.ref_no = '".trim($result["refno"])."'";
					
			  	   $res=$db->Execute($sql);
			       $row=$res->RecordCount();
				   $result2 = $res->FetchRow();
					
					if ($row==0){
					  $paid = 0;
					}else{
					  $paid = 1; 
					}  
				#added by VAN 06-03-08
				if ($result["date_birth"]!='0000-00-00')
					$age = $person_obj->getAge(date("m/d/Y",strtotime($result["date_birth"])),true,date("m/d/Y"));
				else
					#if ($result["age"]==0)
					$age = $result["age"];
				
				#$objResponse->addAlert("type = ".$result["encounter_type"]);
				if ($result['encounter_type']==1){
					$enctype = "ERPx";
					$location = "EMERGENCY ROOM";
				}elseif ($result['encounter_type']==2){
					#$enctype = "OUTPATIENT (OPD)";
					$enctype = "OPDx";
					$dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
					$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
				}elseif (($result['encounter_type']==3)||($result['encounter_type']==4)){
					if ($result['encounter_type']==3)
						$enctype = "INPx (ER)";
					elseif ($result['encounter_type']==4)
						$enctype = "INPx (OPD)";
				
					$ward = $ward_obj->getWardInfo($result['current_ward_nr']);
					$location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$result['current_room_nr'];
				}else{
					$enctype = "WPx";
					$location = 'WALK-IN';
				}
				
				#---------------------
				
				#added by VAN 01-14-08
				if (empty($result["parent_refno"]))
					$repeat = 0;
				else
					$repeat = 1;	
				
				
				if ($mod){
					$labresult = $srv->hasResult(trim($result["refno"]));
				
					if ($labresult)
						$labstatus = 1;
					else
						$labstatus = 0;
						
					if ($result["type_charge"]){
						$result2['or_no'] = $result['charge_name'];
					}	
				
					#$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency, $paid);
					#$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency, $labstatus, $paid, $repeat,trim($result["pid"]),floor($age),$result["sex"],$location, $enctype);
					$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency, $labstatus, $paid, $repeat,trim($result["pid"]),floor($age),$result["sex"],$location, $enctype,$result2['or_no'],$result["is_cash"]);
				}else{
					#$objResponse->addAlert("ref = ".trim($result["refno"])." - ".$result["service_code"]);
					$labresult = $srv->hasResult(trim($result["refno"]), $result["service_code"]);
				
					if ($labresult)
						$labstatus = 1;
					else
						$labstatus = 0;
						
					if ($result["type_charge"]){
						$result2['or_no'] = $result['charge_name'];
					}		
					#$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency,$labstatus, $result["service_name"], $result["service_code"]);
					#$objResponse->addAlert($result['charge_name']);
					#$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency,$labstatus, $result["service_name"], $result["service_code"], $repeat, trim($result["pid"]),floor($age),$result["sex"],$location, $enctype);
					#edited by VAN 07-03-08
					$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency,$result2['or_no'], $result["service_name"], $result["service_code"], $repeat, trim($result["pid"]),floor($age),$result["sex"],$location, $enctype);
				}
				#$count++;
			}
		}
		if (!$rows) $objResponse->addScriptCall("addPerson","RequestList",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		
		return $objResponse;
	}
    */
    
    #added by VAN 07-24-09
    
    function populateRequestList($done, $sElem,$searchkey,$page,$include_firstname,$mod, $encounter_nr='', $is_doctor=0 , $lab_area='') {
        global $db;
        $glob_obj = new GlobalConfig($GLOBAL_CONFIG);
        $glob_obj->getConfig('pagin_patient_search_max_block_rows');
        $maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
        
        $objResponse = new xajaxResponse();
        $srv=new SegLab;
        $dept_obj=new Department;
        $ward_obj = new Ward;
        $person_obj=new Person();
        
        $offset = $page * $maxRows;
        #$searchkey = strtr($searchkey, '*?', '%_');
        #$objResponse->addAlert('searchkey = '.utf8_decode($searchkey));
        
        #added by VAN 03-24-08
        $searchkey = utf8_decode($searchkey);
        
        if ($searchkey==NULL)
            $searchkey = 'now';
        #$objResponse->addAlert("lab area = ".$lab_area);
        if(empty($lab_area))
            $lab_area = 'LB';
        
            #$objResponse->addAlert("mode = ".$mod);
        #$total_srv = $srv->countSearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, $is_doctor, $encounter_nr);
        $total_srv = $srv->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, $is_doctor, $encounter_nr,0,1,$lab_area);
        #$objResponse->addAlert($srv->sql);
        #$objResponse->alert('src = '.$srv->mod);
        $total = $srv->count;
        #$objResponse->addAlert('total = '.$total);
        $lastPage = floor($total/$maxRows);
        #$objResponse->addAlert('total = '.floor($total%10));
        if ((floor($total%10))==0)
            $lastPage = $lastPage-1;
        
        if ($page > $lastPage) $page=$lastPage;
        $ergebnis=$srv->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, $is_doctor, $encounter_nr,0,0,$lab_area);
        #$objResponse->alert('search = '.$srv->count_sql); 
        #$objResponse->addAlert("sql = ".$srv->sql);
        $rows=0;
        
        #$objResponse->addAlert("pageno, lastpage, pagen, total = ".$page.", ".$lastPage.", ".$maxRows.", ".$total);
        $objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
        $objResponse->addScriptCall("clearList","RequestList");
        if ($ergebnis) {
            $rows=$ergebnis->RecordCount();
            while($result=$ergebnis->FetchRow()) {
               
                $urgency = $result["is_urgent"]?"Urgent":"Normal";
                if ($result["pid"]!=" ") 
                    #$name = ucwords(strtolower(trim($result["name_first"])))." ".ucwords(strtolower(trim($result["name_middle"])))." ".ucwords(strtolower(trim($result["name_last"])));
                    $name = ucwords(strtolower(trim($result["name_last"]))).", ".ucwords(strtolower(trim($result["name_first"])))." ".ucwords(strtolower(trim($result["name_middle"])));
                else
                    $name = trim($result["ordername"]);
                    
                if (!$name) $name='<i style="font-weight:normal">No name</i>';
                
                if ($result["serv_dt"]) {
                    $date = strtotime($result["serv_dt"]);
                    $time = strtotime($result["serv_tm"]);
                    $requestDate = date("M d, Y",$date)." ".date("h:i A",$time);
                }
                
                #$objResponse->addAlert("type = ".$result["charge_name"]);
                
                #$objResponse->addAlert("type = ".$result2['or_no']);
                # check if this request is already paid or not
                #if ($mod){
                
                    $sql = "SELECT pr.ref_no,pr.service_code, pr.or_no AS or_no 
                              FROM seg_pay_request AS pr
                              INNER JOIN seg_pay AS p ON p.or_no=pr.or_no
                                WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'
                              AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00')
                          UNION
                            SELECT gr.ref_no,gr.service_code,IF(gr.grant_no,'CLASS D','') AS or_no  
                               FROM seg_granted_request AS gr
                       WHERE gr.ref_source = 'LD' AND gr.ref_no = '".trim($result["refno"])."'";
                    
                     $res=$db->Execute($sql);
                   $row=$res->RecordCount();
                   $result2 = $res->FetchRow();
                    
                    if ($row==0){
                      $paid = 0;
                    }else{
                      $paid = 1; 
                    }  
                #added by VAN 06-03-08
                /*if ($result["date_birth"]!='0000-00-00')
                    $age = $person_obj->getAge(date("m/d/Y",strtotime($result["date_birth"])),true,date("m/d/Y"));
                else
                    #if ($result["age"]==0)
                    $age = $result["age"];
                */
                #$objResponse->addAlert("type = ".$result["encounter_type"]);
                if ($result['encounter_type']==1){
                    $enctype = "ERPx";
                    $location = "EMERGENCY ROOM";
                }elseif ($result['encounter_type']==2){
                    #$enctype = "OUTPATIENT (OPD)";
                    $enctype = "OPDx";
                    $dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
                    $location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
                }elseif (($result['encounter_type']==3)||($result['encounter_type']==4)){
                    if ($result['encounter_type']==3)
                        $enctype = "INPx (ER)";
                    elseif ($result['encounter_type']==4)
                        $enctype = "INPx (OPD)";
                
                    $ward = $ward_obj->getWardInfo($result['current_ward_nr']);
                    $location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$result['current_room_nr'];
                }else{
                    $enctype = "WPx";
                    $location = 'WALK-IN';
                }
                
                #---------------------
                
                #added by VAN 01-14-08
                if (empty($result["parent_refno"]))
                    $repeat = 0;
                else
                    $repeat = 1;    
                
                
                if ($mod){
                    $labresult = $srv->hasResult(trim($result["refno"]));
                
                    if ($labresult)
                        $labstatus = 1;
                    else
                        $labstatus = 0;
                        
                    if ($result["type_charge"]){
                        $result2['or_no'] = $result['charge_name'];
                    }    
                
                    #$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency, $paid);
                    #$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency, $labstatus, $paid, $repeat,trim($result["pid"]),floor($age),$result["sex"],$location, $enctype);
                    $objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency, $labstatus, $paid, $repeat,trim($result["pid"]),floor($age),$result["sex"],$location, $enctype,$result2['or_no'],$result["is_cash"]);
                }else{
                    #$objResponse->addAlert("ref = ".trim($result["refno"])." - ".$result["service_code"]);
                    $labresult = $srv->hasResult(trim($result["refno"]), $result["service_code"]);
                
                    if ($labresult)
                        $labstatus = 1;
                    else
                        $labstatus = 0;
                        
                    if ($result["type_charge"]){
                        $result2['or_no'] = $result['charge_name'];
                    }        
                    #$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency,$labstatus, $result["service_name"], $result["service_code"]);
                    #$objResponse->addAlert($result['charge_name']);
                    #$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency,$labstatus, $result["service_name"], $result["service_code"], $repeat, trim($result["pid"]),floor($age),$result["sex"],$location, $enctype);
                    #edited by VAN 07-03-08
                    $objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency,$result2['or_no'], $result["service_name"], $result["service_code"], $repeat, trim($result["pid"]),floor($age),$result["sex"],$location, $enctype);
                }
                #$count++;
            }
        }
        if (!$rows) $objResponse->addScriptCall("addPerson","RequestList",NULL);
        if ($sElem) {
            $objResponse->addScriptCall("endAJAXSearch",$sElem);
        }
        
        return $objResponse;
    }
    #--------------------------

	#-----------added by VAN 11-08-07-----------
	/*
	function populateOrderList($sElem,$searchkey,$page,$include_firstname) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
		
		$objResponse = new xajaxResponse();
		$srv=new SegLab;
		$offset = $page * $maxRows;
		#$objResponse->addAlert("searchkey = ".$searchkey);
		if ($searchkey==NULL)
			$searchkey = 'now';
		$total_srv = $srv->countSearchSelect_Order($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname);
		#$objResponse->addAlert("sql c1 = ".$srv->sql);
		$total = $srv->count;
		#$objResponse->addAlert("total = ".$total);
		$lastPage = floor($total/$maxRows);
		if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$srv->SearchSelect_Order($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname);
		#$objResponse->addAlert("sql c2 = ".$srv->sql);
		$rows=0;

		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","RequestList");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
			   
				$urgency = $result["is_urgent"]?"Urgent":"Normal";
				#$status = $result["status"]?"Pending":"Done";
				#$objResponse->addAlert("pid = ".$result["pid"]);
				if ($result["pid"]!=" ") 
					$name = trim($result["name_first"])." ".trim($result["name_middle"])." ".trim($result["name_last"]);
				else
					$name = trim($result["ordername"]);
					
				if (!$name) $name='<i style="font-weight:normal">No name</i>';
				
				if ($result["serv_dt"]) {
					$time = strtotime($result["serv_dt"]);
					$requestDate = date("m-d-Y",$time);
				}
				
				$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency,$result["status"]);
			}
		}
		if (!$rows) $objResponse->addScriptCall("addPerson","RequestList",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		
		return $objResponse;
	}
*/
	#----------------------------------------------
	
	#added by VAN 07-29-08
	#if user is doctor
	/*
	function populateRequestList2($encounter_nr='', $is_doctor=0, $done, $sElem,$searchkey,$page,$include_firstname,$mod) {
		global $db;
		$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('pagin_patient_search_max_block_rows');
		$maxRows = $GLOBAL_CONFIG['pagin_patient_search_max_block_rows'];
		
		$objResponse = new xajaxResponse();
		$srv=new SegLab;
		$dept_obj=new Department;
		$ward_obj = new Ward;
		$person_obj=new Person();
		
		$offset = $page * $maxRows;
		
		#added by VAN 03-24-08
		$searchkey = utf8_decode($searchkey);
		
		if ($searchkey==NULL)
			$searchkey = 'now';
		$total_srv = $srv->countSearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, $is_doctor, $encounter_nr);
		$total = $srv->count;
		$lastPage = floor($total/$maxRows);
		if ((floor($total%10))==0)
			$lastPage = $lastPage-1;
		
		if ($page > $lastPage) $page=$lastPage;
		$ergebnis=$srv->SearchSelect($searchkey,$maxRows,$offset,"name_last","ASC",$include_firstname,$mod,$done, $is_doctor, $encounter_nr);
		#$objResponse->addAlert("sql = ".$srv->sql);
		$rows=0;
		
		$objResponse->addScriptCall("setPagination",$page,$lastPage,$maxRows,$total);
		$objResponse->addScriptCall("clearList","RequestList");
		if ($ergebnis) {
			$rows=$ergebnis->RecordCount();
			while($result=$ergebnis->FetchRow()) {
			   
				$urgency = $result["is_urgent"]?"Urgent":"Normal";
				if ($result["pid"]!=" ") 
					$name = ucwords(strtolower(trim($result["name_first"])))." ".ucwords(strtolower(trim($result["name_middle"])))." ".ucwords(strtolower(trim($result["name_last"])));
				else
					$name = trim($result["ordername"]);
					
				if (!$name) $name='<i style="font-weight:normal">No name</i>';
				
				if ($result["serv_dt"]) {
					$date = strtotime($result["serv_dt"]);
					$time = strtotime($result["serv_tm"]);
					#$requestDate = date("M d, Y",$date)." ".date("h:i A",$time);
					$requestDate = date("m/d/Y",$date)." ".date("h:i A",$time);
					
					#service_date
					$serviceDate = date("m/d/Y h:i A",strtotime($result["service_date"]));
				}
				
					$sql = "SELECT pr.ref_no,pr.service_code, pr.or_no AS or_no 
							  FROM seg_pay_request AS pr
			  				  WHERE pr.ref_source = 'LD' AND pr.ref_no = '".trim($result["refno"])."'
      			     	  UNION
  			              SELECT gr.ref_no,gr.service_code,gr.grant_no AS or_no  
						  FROM seg_granted_request AS gr
                       WHERE gr.ref_source = 'LD' AND gr.ref_no = '".trim($result["refno"])."'";
					
			  	   $res=$db->Execute($sql);
			       $row=$res->RecordCount();
				   $result2 = $res->FetchRow();
					
					if ($row==0){
					  $paid = 0;
					}else{
					  $paid = 1; 
					}  
					
				#added by VAN 06-03-08
				if ($result["date_birth"]!='0000-00-00')
					$age = $person_obj->getAge(date("m/d/Y",strtotime($result["date_birth"])),true,date("m/d/Y"));
				else
					$age = $result["age"];
				
				if ($result['encounter_type']==1){
					$enctype = "ERPx";
					$location = "EMERGENCY ROOM";
				}elseif ($result['encounter_type']==2){
					$enctype = "OPDx";
					$dept = $dept_obj->getDeptAllInfo($result['current_dept_nr']);
					$location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
				}elseif (($result['encounter_type']==3)||($result['encounter_type']==4)){
					if ($result['encounter_type']==3)
						$enctype = "INPx (ER)";
					elseif ($result['encounter_type']==4)
						$enctype = "INPx (OPD)";
				
					$ward = $ward_obj->getWardInfo($result['current_ward_nr']);
					$location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$result['current_room_nr'];
				}else{
					$enctype = "WPx";
					$location = 'WALK-IN';
				}
				
				#---------------------
				
				#added by VAN 01-14-08
				if (empty($result["parent_refno"]))
					$repeat = 0;
				else
					$repeat = 1;	
				
				
				if ($mod){
					$labresult = $srv->hasResult(trim($result["refno"]));
				
					if ($labresult)
						$labstatus = 1;
					else
						$labstatus = 0;
				
					$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency, $labstatus, $paid, $repeat,trim($result["pid"]),floor($age),$result["sex"],$location, $enctype,$result2['or_no']);
				}else{
					$labresult = $srv->hasResult(trim($result["refno"]), $result["service_code"]);
				
					if ($labresult)
						$labstatus = 1;
					else
						$labstatus = 0;
					$objResponse->addScriptCall("addPerson","RequestList",trim($result["refno"]),$name,$requestDate,$urgency,$result2['or_no'], $result["service_name"], $result["service_code"], $repeat, trim($result["pid"]),floor($age),$result["sex"],$location, $enctype, $serviceDate);
				}
			}
		}
		if (!$rows) $objResponse->addScriptCall("addPerson","RequestList",NULL);
		if ($sElem) {
			$objResponse->addScriptCall("endAJAXSearch",$sElem);
		}
		
		return $objResponse;
	}
	#-------------------------
	*/
	function setALLDepartment($dept_nr=0){
      #	global $dept_obj;

		$dept_obj=new Department;
		
		$objResponse = new xajaxResponse();
		#$objResponse->addAlert("setALLDepartment");
		$rs=$dept_obj->getAllMedicalObject();
#$objResponse->addAlert("setALLDepartment : rs = '".$rs."'");		
		$objResponse->addScriptCall("ajxClearDocDeptOptions",1);
		if ($rs) {
			$objResponse->addScriptCall("ajxAddDocDeptOption",1,"-Select a Department-",0);
			while ($result=$rs->FetchRow()) {
			   $objResponse->addScriptCall("ajxAddDocDeptOption",1,trim($result["name_formal"]),trim($result["nr"]));
			}
		if($dept_nr)
				$list='';
				$objResponse->addScriptCall("ajxSetDepartment", $dept_nr, $list); # set the department
		}
		else {
			$objResponse->addAlert("setALLDepartment : Error retrieving Department information...");
		}
		return $objResponse;
	}

	function setDepartmentOfDoc($personell_nr=0) {
#		global $dept_obj;

		$dept_obj=new Department;
		
		$objResponse = new xajaxResponse();
		#$objResponse->addAlert("setDepartmentOfDoc : personell_nr ='$personell_nr'");
      if ($personell_nr!=0){
			$result=$dept_obj->getDeptofDoctor($personell_nr);
			#$objResponse->addAlert("setDepartmentOfDoc : dept_obj->sql = '$dept_obj->sql'");
			#$objResponse->addAlert("setDepartmentOfDoc : name_formal = ".$result["name_formal"]." - ".$result["nr"]);
			if ($result){
				$list = $dept_obj->getAncestorChildrenDept($result["nr"]);   # burn added : July 19, 2007
	#$objResponse->addAlert("setDepartmentOfDoc : list = '$list'; result['nr'] = '".$result['nr']."'");
				if (trim($list)!="")
					$list .= ",".trim($result["nr"]);
				else
					$list .= trim($result["nr"]);			
				$objResponse->addScriptCall("ajxSetDepartment",trim($result["nr"]),$list); # set the department
			}
			if($personell_nr)
				$objResponse->addScriptCall("ajxSetDoctor",$personell_nr); # set the doctor

		}else{
			$objResponse->addAlert("setDepartmentOfDoc : Error retrieving Department information of a doctor...");
		}	
		return $objResponse;
	}

	function setDoctors($dept_nr=0, $personell_nr=0) {
#		global $pers_obj;
		
		$objResponse = new xajaxResponse();

		$pers_obj=new Personell;
		#$objResponse->addAlert("dept : $dept_nr");
		if ($dept_nr)
			$rs=$pers_obj->getDoctorsOfDept($dept_nr);
		else
			$rs=$pers_obj->getDoctors(2);	# argument, $admit_patient NOT IN (0,1), BOTH Inpatient/ER & Outpatient

#		$objResponse->addAlert("setDoctors : dept_nr = '".$dept_nr."'");
#		$objResponse->addAlert("setDoctors : pers_obj->sql = '".$pers_obj->sql."'");
		#$objResponse->addAlert("setDoctors".$admit_inpatient."=".$dept_nr);
		
		$objResponse->addScriptCall("ajxClearDocDeptOptions",0);
		if ($rs) {
			$objResponse->addScriptCall("ajxAddDocDeptOption",0,"-Select a Doctor-",0);
			
			while ($result=$rs->FetchRow()) {
			  	#$doctor_name = trim($result["name_first"])." ".trim($result["name_2"])." ".trim($result["name_last"]);
				#$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
				
				if (trim($result["name_middle"]))
					$dot  = ".";
					
				$doctor_name = trim($result["name_last"]).", ".trim($result["name_first"])." ".substr(trim($result["name_middle"]),0,1).$dot;
				$doctor_name = ucwords(strtolower($doctor_name)).", MD";
				
				$doctor_name = htmlspecialchars($doctor_name);
				$objResponse->addScriptCall("ajxAddDocDeptOption",0,$doctor_name,trim($result["personell_nr"]));
			}
			if($personell_nr)
				$objResponse->addScriptCall("ajxSetDoctor", $personell_nr); # set the doctor
			if($dept_nr)
				$objResponse->addScriptCall("ajxSetDepartment", $dept_nr); # set the department
			$objResponse->addScriptCall("request_doc_handler"); # set the 'request_doctor_out' textbox
		}
		else {
			#$objResponse->addAlert("setDoctors : Error retrieving Doctors information...");
			$objResponse->addScriptCall("ajxAddDocDeptOption",0,"-No Doctor Available-",0);
		}
		return $objResponse;
	}
	#--------------------------------------
	
	#added by VAN 08-21-08
	function savedServedPatient($refno, $service_code,$is_served){
		global $db, $HTTP_SESSION_VARS;
		
		$objResponse = new xajaxResponse();
		$srv=new SegLab;
		#$objResponse->addAlert("ajax : refno, code = ".$refno." , ".$service_code);
		
		if ($is_served)
			$date_served = date("Y-m-d H:i:s");
		else
			$date_served = '';	
		
		$save = $srv->ServedLabRequest($refno, $service_code, $is_served, $date_served);
		#$objResponse->addAlert("sql = ".$srv->sql);
		if ($save){
			$objResponse->addScriptCall("ReloadWindow");
		}	
			
		return $objResponse;
		
	}
	#----------------------

    #added by VAN 04-13-09
    function getDeptDocValues($encounter_nr){
        global $db;
        $objResponse = new xajaxResponse();
        
        $enc_obj=new Encounter;
        
        $patient = $enc_obj->getPatientEncounter($encounter_nr);
        #$objResponse->alert('sql = '.$enc_obj->sql);
        #$objResponse->alert($patient['current_dept_nr']);
        if (($patient['encounter_type']==1)|| ($patient['encounter_type']==2)){
            $dept_nr = $patient['current_dept_nr'];
            $doc_nr = $patient['current_att_dr_nr'];
        }elseif (($patient['encounter_type']==3)|| ($patient['encounter_type']==4)){
            $dept_nr = $patient['consulting_dept_nr'];
            $doc_nr = $patient['consulting_dr_nr'];
        }else{
            $dept_nr = 0;
            $doc_nr = 0;
        }
        #$objResponse->alert('dept, dr = '.$dept_nr." - ".$doc_nr);     
        $objResponse->addScriptCall("setDeptDocValues",$dept_nr, $doc_nr, $patient['er_opd_diagnosis']);
        
        return $objResponse;
    }
    
    #added by VAN 10-02-09
    function getAllServiceOfPackage($service_code){
        global $db;
        $objResponse = new xajaxResponse();
        $srv=new SegLab;
        
        #$objResponse->alert("ajax = ".$service_code);
        $rs_group = $srv->isServiceAPackage($service_code);
        $rs_count = $srv->count;
        
        #$objResponse->alert("ajax count = ".$rs_count); 
        if ($rs_count){
          #$objResponse->alert("it is a package");
          $rs_group_inc = $srv->getAllServiceOfPackage($service_code);
          #lab exam request that is a package
          while ($row=$rs_group_inc->FetchRow()){
              #$objResponse->alert('ajax = '.$row['service_code']);
              $objResponse->addScriptCall("prepareAdd_Package",$row['service_code'],$row['name'],$row['cash'],$row['charge'],$row['sservice'],$row['group_code'],$row['priceC1'],$row['priceC2'],$row['priceC3']);
          }
          
        } else{
           #lab exam request that is not a package
           $objResponse->addScriptCall("prepareAdd_NotPackage",$service_code);
        }
        
        return $objResponse;
    }
	   #-----------------
	
	require('./roots.php');
	require($root_path.'include/inc_environment_global.php');	
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	#include_once($root_path.'include/inc_date_format_functions.php');
	require($root_path.'include/care_api_classes/class_pharma_transaction.php');
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	require($root_path.'include/care_api_classes/class_discount.php');
	require($root_path.'modules/clinics/ajax/lab-new.common.php');
	#-----------added by VAN 09-26-07-----
	require_once($root_path.'include/care_api_classes/class_department.php');
	require_once($root_path.'include/care_api_classes/class_personell.php');
	require_once($root_path.'include/care_api_classes/class_ward.php');
	
	require_once($root_path.'include/care_api_classes/class_person.php');
	#-------------------------------------
	$xajax->processRequests();
?>