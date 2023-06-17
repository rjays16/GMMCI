<?php
	/*--- created by cha on 04-22-09 ---*/

	require_once($root_path.'include/care_api_classes/class_core.php'); //iedit pa to
	require_once($root_path.'include/care_api_classes/class_pharma_product.php');
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
	require_once($root_path.'include/care_api_classes/class_radiology.php');

	class Price_Adjustments extends Core
	{
			 var $tb_lab_services = 'seg_lab_services';
			 var $tb_radio_services = 'seg_radio_services';
			 var $tb_pharma_services = 'care_pharma_products_main';

			 var $sql;
			 var $result;

			 var $ok;
			 var $data_array;
			 var $buffer;
			 var $row;
			 var $count;

			 var $fld_info_lab_and_radio;
			 var $fld_info_pharma_and_misc;


			 function Price_Adjustments()
			 {
					 if($service_type==1 or $service_type==2)
					 {
								$this->useLabRadioServicesInfo($service_type);
					 }
					 if($service_type==3 or $service_type==4)
					 {
							 $this->usePharmaMiscServicesInfo();
					 }
			 }

			 function useLabRadioServicesInfo($service_type)
			 {
					 if($service_type==1)
					 {
							 $this->setRefArray($this->fld_info_lab_and_radio);
							 $this->coretable=$this->tb_lab_services;
					 }
					 if($service_type==2)
					 {
							 $this->setRefArray($this->fld_info_lab_and_radio);
							 $this->coretable=$this->tb_radio_services;
					 }
			 }

			 function usePharmaMiscServicesInfo()
			 {
						$this->setRefArray($this->fld_info_pharma_and_misc);
						$this->coretable=$this->tb_pharma_services;
			 }

			 function viewPriceList($service_type, $keyword, $multiple=0, $maxcount=100, $offset=0)
			 {
						global $db;
						if(empty($maxcount)) $maxcount=100;
						if(empty($offset)) $offset=0;
						$labObj = new SegLab();
						$radObj = new SegRadio();
						$pharmaObj = new SegPharmaProduct();

						switch($service_type)
						{
								/*case 1: $this->sql="select service_code, name,
										price_cash, price_charge from $this->tb_lab_services
										where (price_cash!='\0' and price_charge!='\0')
										and (name!='-' and name!='NULL') order by name"; break;
								case 2: $this->sql="select service_code, name,
										price_cash, price_charge from $this->tb_radio_services
										where (price_cash!='\0' and price_charge!='\0')
										and (name!='-' and name!='NULL') order by name"; break;
								case 3: $this->sql="select bestellnum, artikelname,
										price_cash, price_charge from $this->tb_pharma_services
										where prod_class='M' and (price_cash!='\0' and price_charge!='\0')
										and (artikelname!='-' and artikelname!='NULL') order by artikelname"; break;
								case 4: $this->sql="select bestellnum, artikelname,
										price_cash, price_charge from $this->tb_pharma_services
										where (prod_class='S' or prod_class='E' or
										prod_class='NS' or prod_class='B' or prod_class='HS')
										and (bestellnum!='1' and bestellnum!='2' and bestellnum!='3'
										and bestellnum!='4' and bestellnum!='5')
										and (price_cash!='\0' and price_charge!='\0')
										and (artikelname!='-' and artikelname!='NULL') order by artikelname"; break;*/
								case 1: $labObj->SearchService('',$keyword,$multiple,$maxcount,$offset,''); break;
								case 2: $radObj->SearchService2($keyword,$maxcount,$offset,''); break;
								case 3: $pharmaObj->search_products_for_tray($keyword, '', '', $offset, $maxcount); break;
								case 4: $this->getMiscServiceList($keyword,$maxcount,$offset); break;
								case 5: $this->getOtherServiceList($keyword,$maxcount,$offset); break;
						}
					 if($this->rec_count>=1)
					 {
								//if($this->rec_count=$this->res['ssl']->RecordCount())
								//{
										return $this->res['ssl'];
								//}
								//else{  return false; }
						}
						else{ return false; }
			 }

			 function countPriceListRecord($service_type, $keyword, $multiple=0, $maxcount=100, $offset=0)
			 {
						global $db;
						if(empty($maxcount)) $maxcount=100;
						if(empty($offset)) $offset=0;
						$labObj = new SegLab();
						$radObj = new SegRadio();
						$pharmaObj = new SegPharmaProduct();

						switch($service_type)
						{
								/*case 1: $this->sql="select service_code, name,
										price_cash, price_charge from $this->tb_lab_services
										where (price_cash!='\0' and price_charge!='\0')
										and (name!='-' and name!='NULL') order by name"; break;
								case 2: $this->sql="select service_code, name,
										price_cash, price_charge from $this->tb_radio_services
										where (price_cash!='\0' and price_charge!='\0')
										and (name!='-' and name!='NULL') order by name"; break;
								case 3: $this->sql="select bestellnum, artikelname,
										price_cash, price_charge from $this->tb_pharma_services
										where prod_class='M' and (price_cash!='\0' and price_charge!='\0')
										and (artikelname!='-' and artikelname!='NULL') order by artikelname"; break;
								case 4: $this->sql="select bestellnum, artikelname,
										price_cash, price_charge from $this->tb_pharma_services
										where (prod_class='S' or prod_class='E' or
										prod_class='NS' or prod_class='B' or prod_class='HS')
										and (bestellnum!='1' and bestellnum!='2' and bestellnum!='3'
										and bestellnum!='4' and bestellnum!='5')
										and (price_cash!='\0' and price_charge!='\0')
										and (artikelname!='-' and artikelname!='NULL') order by artikelname"; break;
								case 5: $this->sql="select service_code, name, price from seg_other_services union
										select service_code, name, price from seg_otherhosp_services order by name"; break;*/

								case 1: $this->result = $labObj->countSearchService('',$keyword,$multiple,$maxcount,$offset,'');
									//print_r($labObj->result,true);
								 break;
								case 2: $this->result = $radObj->countSearchService2($keyword,$maxcount,$offset,'');
								//print_r($radObj->result,true);
								 break;
								case 3: $this->result = $pharmaObj->search_products_for_tray($keyword, '', '', $offset, $maxcount);
								//print_r($pharmaObj->result,true);
								 break;
								case 4: $this->countMiscServiceList($keyword,$maxcount,$offset);
								//print_r($this->result,true);
								 break;
								case 5: $this->countOtherServiceList($keyword,$maxcount,$offset);
								//print_r($this->result,true);
								break;
						}

						if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						}
						else{ return FALSE;}
			 }

			 function countOtherService($keyword='',$maxcount=100,$offset=0)
			 {
					global $db;
					if(empty($maxcount)) $maxcount=100;
					if(empty($offset)) $offset=0;
					$this->sql = "(SELECT 1 as source, s.name,s.name_short,s.price,s.service_code AS code,s.description,t.name_long AS type_name,p.name_long AS ptype_name,s.account_type,s.lockflag
					FROM seg_other_services AS s
					LEFT JOIN seg_cashier_account_subtypes AS t ON s.account_type=t.type_id
					LEFT JOIN seg_cashier_account_types AS p ON t.parent_type=p.type_id WHERE name LIKE '%$keyword%')
					union
					(SELECT 2 as source, s.name,'',s.price,s.service_code AS code,'',t.name_long AS type_name,p.name_long AS ptype_name,s.account_type,''
					FROM seg_otherhosp_services AS s
					LEFT JOIN seg_cashier_account_subtypes AS t ON s.account_type=t.type_id
					LEFT JOIN seg_cashier_account_types AS p ON t.parent_type=p.type_id
					 WHERE (s.status NOT IN ('deleted','hidden','inactive','void')) AND name LIKE '%$keyword%')";
					 if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
							return $this->result;
						}
						else{return FALSE;}
					}else{return FALSE;}
			 }

			 function getOtherService($keyword='',$maxcount=100,$offset=0)
			 {
					global $db;
					if(empty($maxcount)) $maxcount=100;
					if(empty($offset)) $offset=0;
					$this->sql = "(SELECT 1 as source, s.name,s.name_short,s.price,s.service_code AS code,s.description,t.name_long AS type_name,p.name_long AS ptype_name,s.account_type,s.lockflag
					FROM seg_other_services AS s
					LEFT JOIN seg_cashier_account_subtypes AS t ON s.account_type=t.type_id
					LEFT JOIN seg_cashier_account_types AS p ON t.parent_type=p.type_id WHERE name LIKE '%$keyword%')
					union
					(SELECT 2 as source, s.name,'',s.price,s.service_code AS code,'',t.name_long AS type_name,p.name_long AS ptype_name,s.account_type,''
					FROM seg_otherhosp_services AS s
					LEFT JOIN seg_cashier_account_subtypes AS t ON s.account_type=t.type_id
					LEFT JOIN seg_cashier_account_types AS p ON t.parent_type=p.type_id
					 WHERE (s.status NOT IN ('deleted','hidden','inactive','void')) AND name LIKE '%$keyword%')";
						if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset))
						{
								if($this->rec_count=$this->res['ssl']->RecordCount())
								{
										return $this->res['ssl'];
								}
								else{ return false; }
						}
						else{ return false; }
			 }

			 function countMiscService($keyword='',$maxcount=100,$offset=0)
			 {
					global $db;
					if(empty($maxcount)) $maxcount=100;
					if(empty($offset)) $offset=0;
					$this->sql = "select bestellnum, artikelname,
										price_cash, price_charge from care_pharma_products_main
										where (prod_class='S' or prod_class='E' or
										prod_class='NS' or prod_class='B' or prod_class='HS')
										and (bestellnum!='1' and bestellnum!='2' and bestellnum!='3'
										and bestellnum!='4' and bestellnum!='5')
										and (price_cash!='\0' and price_charge!='\0')
										and (artikelname!='-' and artikelname!='NULL')
										and (artikelname like '%$keyword%' OR bestellnum like '%$keyword%')
										order by artikelname";
					 if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
							return $this->result;
						}
						else{return FALSE;}
					}else{return FALSE;}
			 }

			 function getMiscServiceList($keyword='',$maxcount=100,$offset=0)
			 {
					global $db;
					if(empty($maxcount)) $maxcount=100;
					if(empty($offset)) $offset=0;
					$this->sql = "select bestellnum, artikelname,
										price_cash, price_charge from care_pharma_products_main
										where (prod_class='S' or prod_class='E' or
										prod_class='NS' or prod_class='B' or prod_class='HS')
										and (bestellnum!='1' and bestellnum!='2' and bestellnum!='3'
										and bestellnum!='4' and bestellnum!='5')
										and (price_cash!='\0' and price_charge!='\0')
										and (artikelname!='-' and artikelname!='NULL')
										and (artikelname like '%$keyword%' OR bestellnum like '%$keyword%')
										order by artikelname";
					 if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
						if($this->rec_count=$this->res['ssl']->RecordCount()) {
							return $this->res['ssl'];
						}else{return false;}
					}else{return false;}
			 }


			 function saveChangesToPriceList($service_type,$date,$modifiedPriceList,$max)
			 {
						$value=0;
						$class="";
						switch($service_type)
						{
								case 1: $class="LB"; break;
								case 2: $class="RD"; break;
								case 3: $class="PH"; break;
								case 4: $class="MS"; break;
								case 5: $class="O"; break;
						}
						global $db,$HTTP_SESSION_VARS;

						$cnt=0;
						$sql2="";
						$sql3="";
						$value=FALSE;
						while($cnt<$max)
						{
							$temp_createdt=date("Y-m-d H:i:s");
							$temp_createid=$HTTP_SESSION_VARS['sess_temp_userid'];
							$temp_modifydt=date("Y-m-d H:i:s");
							$temp_modifyid=$HTTP_SESSION_VARS['sess_temp_userid'];
							$temp_hist="Create ".date("Y-m-d H:i:s")." [".$HTTP_SESSION_VARS['sess_temp_userid']."]";

							$temp_refno=$this->getNewControlNr('');
							#echo $temp_refno;

							$sql3="insert into seg_hospital_service_price (refno, effectivity_date, history, create_dt, create_id, modify_dt, modify_id) values('$temp_refno', '$date', '$temp_hist', '$temp_createdt', '$temp_createid', '$temp_modifydt', '$temp_modifyid')";
						 # echo $this->sql;
							if($this->result=$db->Execute($sql3))
							{
								 $temp_srvCode=$modifiedPriceList["serviceCode"][$cnt];
								 $temp_priceCash=$modifiedPriceList["priceCash"][$cnt];
								 $temp_priceCharge=$modifiedPriceList["priceCharge"][$cnt];
								 $this->sql="insert into seg_hospital_service_price_details (refno,service_code,price_cash,price_charge,ref_source) values('$temp_refno','$temp_srvCode','$temp_priceCash','$temp_priceCharge','$class')";
								 #echo $this->sql;
								 if($this->result=$db->Execute($this->sql))
								 {
									$value=TRUE;
								 }
								 else $value=FALSE;
							}
							else $value=FALSE;
							$cnt++;
						}
						return $value;
			 }

			 function countPriceHistory($date)
			 {
					 global $db;
					 #$this->sql="select p.create_dt from seg_hospital_service_price as p
					 #     join seg_hospital_service_price_details as d on p.refno=d.refno and p.create_dt like '".$date."%'";
					 #edited by VAN 11-11-09
					 $this->sql="SELECT p.create_dt FROM seg_hospital_service_price AS p
											 INNER JOIN seg_hospital_service_price_details AS d ON p.refno=d.refno
											 WHERE DATE(p.effectivity_date)='".$date."' and d.status NOT IN ('deleted')";
					 if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()) {
								return $this->result;
						}
						else{return FALSE;}
				}else{return FALSE;}
			 }

			 function getPriceHistoryDetails($date, $multiple=0, $maxcount=100, $offset=0)
			 {
						global $db;
						#$this->sql="select d.service_code, d.ref_source, d.price_cash, d.price_charge, p.create_dt from seg_hospital_service_price as p
						#    join seg_hospital_service_price_details as d on p.refno=d.refno and p.create_dt like '".$date."%'";
						#edited by VAN 11-11-09
						$this->sql="SELECT p.refno, d.service_code, d.ref_source, d.price_cash, d.price_charge, p.create_dt, d.status
												FROM seg_hospital_service_price AS p
												INNER JOIN seg_hospital_service_price_details AS d ON p.refno=d.refno
												WHERE DATE(p.effectivity_date)='".$date."' and d.status NOT IN ('deleted')";

						if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset))
						{
								if($this->rec_count=$this->res['ssl']->RecordCount())
								{
										return $this->res['ssl'];
								}
								else{ return false; }
						}
						else{ return false; }
			 }

			 function getServiceName($service_code, $ref_source)
			 {
					 global $db;
					 $retval="";
					 if($ref_source=='O')
					 {
							 $sql1="select name from seg_other_services where service_code='".$service_code."'";
							 $sql2="select name from seg_otherhosp_services where service_code='".$service_code."'";
							 $result1=$db->Execute($sql1);
							 $result2=$db->Execute($sql2);
							 $row1 = $result1->FetchRow();
							 $row2 = $result2->FetchRow();
							 if($row1['name']=='')
							 {
									 $retval =  $row2['name'] ;
							 }
							 if($row2['name']=='')
							 {
									 $retval =  $row1['name'] ;
							 }
					 }
					 else if($ref_source=='LB')
					 {
							 $this->sql="select name from $this->tb_lab_services where service_code='".$service_code."'";
							 if($this->result=$db->Execute($this->sql))
							 {
										$row = $this->result->FetchRow();
										$retval = $row['name'] ;
							 }
					 }
					 else if($ref_source=='RD')
					 {
							 $this->sql="select name from $this->tb_radio_services where service_code='".$service_code."'";
							 if($this->result=$db->Execute($this->sql))
							 {
										$row = $this->result->FetchRow();
										$retval = $row['name'] ;
							 }
					 }
					 else if($ref_source=='PH')
					 {
							 $this->sql="select artikelname from $this->tb_pharma_services where bestellnum='".$service_code."'";
							 if($this->result=$db->Execute($this->sql))
							 {
										$row = $this->result->FetchRow();
										$retval = $row['artikelname'] ;
							 }
					 }
					 else if($ref_source=='MS')
					 {
							 $this->sql="select artikelname from $this->tb_pharma_services
												where (prod_class='S' or prod_class='E' or
												prod_class='NS' or prod_class='B' or prod_class='HS')
												and (bestellnum!='1' and bestellnum!='2' and bestellnum!='3'
												and bestellnum!='4' and bestellnum!='5')
												and bestellnum='".$service_code."'";
							 if($this->result=$db->Execute($this->sql))
							 {
										$row = $this->result->FetchRow();
										$retval = $row['artikelname'] ;
							 }
					 }
					 return $retval;
			 }

	#-----added by cha 10-27-09
	function getNewControlNr($contrlnr)
	{
		 global $db;

				$temp_cntrl_nr = date('Y')."%";   # NOTE : XXXX?????? would be the format of Reference number
				$row=array();
				$this->sql="SELECT refno FROM seg_hospital_service_price WHERE refno LIKE '$temp_cntrl_nr' ORDER BY refno DESC";
				#echo "this ".$this->sql;
				if($this->res['gnpn']=$db->SelectLimit($this->sql,1)){
						if($this->res['gnpn']->RecordCount()){
								$row=$this->res['gnpn']->FetchRow();
								return $row['refno']+1;
						}else{/*echo $this->sql.'no xount';*/return $contrlnr=date('Y')."000001";}
				}else{/*echo $this->sql.'no sql';*/return $contrlnr=date('Y')."000001";}
	}
	#-----end cha

	function updatePriceHistory($refno,$pcash,$pcharge,$effect_date,$history)
	{
		global $db,$HTTP_SESSION_VARS;
		$count = 0;
		$history.=" Modified ".date("Y-m-d H:i:s")." [".$HTTP_SESSION_VARS['sess_temp_userid']."]";

		$this->sql = "UPDATE seg_hospital_service_price SET effectivity_date=".$db->qstr($effect_date).
			", history=".$db->qstr($history).", modify_dt=".$db->qstr(date("Y-m-d H:i:s")).
			", modify_id=".$db->qstr($HTTP_SESSION_VARS['sess_temp_userid'])." where refno=".$db->qstr($refno);
		#echo $this->sql;
		$db->Execute($this->sql);
		if ($db->Affected_Rows())
		{
			$count++;
		}

		$this->sql = "UPDATE seg_hospital_service_price_details SET price_cash=".$db->qstr($pcash).", price_charge=".$db->qstr($pcharge).
		" where refno=".$db->qstr($refno);
		#echo $this->sql;
		$db->Execute($this->sql);
		if ($db->Affected_Rows())
		{
			$count++;
		}

		if($count==2) return true;
		else return false;

	}

	function deletePriceHistory($refno)
	{
		global $db,$HTTP_SESSION_VARS;
		$count = 0;
		$sql1 = "select history from seg_hospital_service_price where refno=".$db->qstr($refno);
		$result = $db->Execute($sql1);
		$row = $result->FetchRow();
		$history = $row['history']."\nDeleted ".date("Y-m-d H:i:s")." [".$HTTP_SESSION_VARS['sess_temp_userid']."]";

		$this->sql = "UPDATE seg_hospital_service_price_details SET status='deleted' where refno=".$db->qstr($refno);
		$db->Execute($this->sql);
		if ($db->Affected_Rows())
		{
			$count++;
		}

		$this->sql = "UPDATE seg_hospital_service_price SET history=".$db->qstr($history).
		",modify_dt=".$db->qstr(date("Y-m-d H:i:s")).", modify_id=".$db->qstr($HTTP_SESSION_VARS['sess_temp_userid'])." where refno=".$db->qstr($refno);
		$db->Execute($this->sql);
		if ($db->Affected_Rows())
		{
			$count++;
		}

		if($count==2) return true;
		else return false;
	}

	#added by VAN 07-14-2010
	function countPriceListHistory($area,$source){
		global $db;

		if ($area)
			$sql_cond = "AND area_code='$area'";

		if ($source){
			switch($source){
						case "1": $ref_source = "LB"; break;
						case "2": $ref_source = "RD"; break;
						case "3": $ref_source = "PH"; break;
						case "4": $ref_source = "MS"; break;
						case "5": $ref_source = "O"; break;
			}

			$sql_src = "AND ref_source='$ref_source'";
		}

		$this->sql="SELECT d.*
									FROM seg_service_pricelist AS d
									WHERE d.status=''
									$sql_cond
									$sql_src
									ORDER BY area_code, ref_source, service_code";
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}else{return FALSE;}
		}else{return FALSE;}
	}
//edited by jasper 12/05/12 added iscount variable to get total rows or limit rows
	function getPriceListHistoryDetails($area, $source, $keyword='',$multiple=0, $maxcount=100, $offset=0, $iscount){
		global $db;

		if ($area)
			$sql_cond = "AND area_code='$area'";

		 if ($source){

				switch($source){
						case "1":
											$ref_source = "LB";
											if ($keyword){
												$sql_key = " AND (d.service_code LIKE '%".$keyword."%' OR s.name LIKE '%".$keyword."%') ";
											}
											$sql_join = " INNER JOIN seg_lab_services AS s ON s.service_code=d.service_code ";
											 break;

						case "2":
											$ref_source = "RD";
											if ($keyword){
												$sql_key = " AND (d.service_code LIKE '%".$keyword."%' OR s.name LIKE '%".$keyword."%') ";
											}
											$sql_join = " INNER JOIN seg_radio_services AS s ON s.service_code=d.service_code ";
											break;

						case "3":
											$ref_source = "PH";
											if ($keyword){
												$sql_key = " AND (d.service_code LIKE '%".$keyword."%' OR s.artikelname LIKE '%".$keyword."%') ";
											}
											$sql_join = " INNER JOIN care_pharma_products_main AS s ON s.bestellnum=d.service_code ";
											break;

						case "4":
											$ref_source = "MS";
											if ($keyword){
												$sql_key = " AND (d.service_code LIKE '%".$keyword."%' OR s.artikelname LIKE '%".$keyword."%') ";
											}
											$sql_join = " INNER JOIN care_pharma_products_main AS s ON s.bestellnum=d.service_code ";
											break;

						case "5":
											$ref_source = "O";
											if ($keyword){
												$sql_key = " AND (d.service_code LIKE '%".$keyword."%' OR s.name LIKE '%".$keyword."%') ";
											}
											$sql_join = " INNER JOIN seg_other_services AS s ON s.service_code=d.service_code ";
											break;
				}

				$sql_src = "AND d.ref_source='$ref_source'";
		}

		$this->sql="SELECT SQL_CALC_FOUND_ROWS d.service_code, d.ref_source, d.price_cash, d.price_charge, d.create_dt
									FROM seg_service_pricelist AS d
									$sql_join
									WHERE d.status=''
									$sql_cond
									$sql_src
									$sql_key
									ORDER BY area_code, ref_source, service_code";

     //added by jasper 12/06/12
         if ($iscount==0) {
            if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			    if($this->rec_count=$this->res['ssl']->RecordCount()){
				    return $this->res['ssl'];
			    }else{ return false; }
		    }else{ return false; }
         }
         else {
            if ($this->result=$db->Execute($this->sql)) {
                if ($this->count=$this->result->RecordCount()) {
                    return $this->result;
                }else{return FALSE;}
            }else{return FALSE;}
         }
     //added by jasper 12/06/12
	}

	function savePriceList($service_type,$area,$modifiedPriceList,$max){
		$value=0;
		$class="";
		switch($service_type){
			case 1: $class="LB"; break;
			case 2: $class="RD"; break;
			case 3: $class="PH"; break;
			case 4: $class="MS"; break;
			case 5: $class="O"; break;
		}
		global $db,$HTTP_SESSION_VARS;

		$cnt=0;
		$sql2="";
		$sql3="";
		$value=FALSE;
		while($cnt<$max){
				$temp_createdt=date("Y-m-d H:i:s");
				$temp_createid=$HTTP_SESSION_VARS['sess_temp_userid'];
				$temp_modifydt=date("Y-m-d H:i:s");
				$temp_modifyid=$HTTP_SESSION_VARS['sess_temp_userid'];
				$temp_hist="Create ".date("Y-m-d H:i:s")." [".$HTTP_SESSION_VARS['sess_temp_userid']."]";

				$temp_srvCode=$area;
				$temp_srvCode=$modifiedPriceList["serviceCode"][$cnt];
				$temp_priceCash=$modifiedPriceList["priceCash"][$cnt];
				$temp_priceCharge=$modifiedPriceList["priceCharge"][$cnt];

				$sql1 = "SELECT d.*
									FROM seg_service_pricelist AS d
									WHERE d.area_code='$area'
									AND d.ref_source='$class' AND d.service_code='$temp_srvCode'";

				$result = $db->Execute($sql1);
				$row = $result->FetchRow();
				$rcount = $result->RecordCount();

				if ($rcount){
						$history = $row['history']."\nUpdate Price ".date("Y-m-d H:i:s")." [".$HTTP_SESSION_VARS['sess_temp_userid']."]";

						$this->sql = "UPDATE seg_service_pricelist
														SET status='',
														price_cash='".$temp_priceCash."',
														price_charge='".$temp_priceCharge."',
														history=".$db->qstr($history).",
														modify_dt=".$db->qstr(date("Y-m-d H:i:s")).",
														modify_id=".$db->qstr($HTTP_SESSION_VARS['sess_temp_userid'])."
														WHERE area_code='$area'
														AND ref_source='$class'
														AND service_code='$temp_srvCode'";
				}else{
						$this->sql="INSERT INTO seg_service_pricelist (area_code,service_code,price_cash,price_charge,ref_source,history, create_dt, create_id, modify_dt, modify_id)
													values('$area','$temp_srvCode','$temp_priceCash','$temp_priceCharge','$class', '$temp_hist', '$temp_createdt', '$temp_createid', '$temp_modifydt', '$temp_modifyid')";
				}
				#echo $this->sql;
				if($this->result=$db->Execute($this->sql)){
						$value=TRUE;
				}else
						$value=FALSE;

				$cnt++;
		}
		return $value;
	}

	function deletePriceListHistory($code,$refsource,$area){
		global $db,$HTTP_SESSION_VARS;
		$count = 0;

		$sql1 = "SELECT d.*
							FROM seg_service_pricelist AS d
							WHERE d.area_code='$area'
							AND d.ref_source='$refsource' AND d.service_code='$code'";

		$result = $db->Execute($sql1);
		$row = $result->FetchRow();
		$rcount = $result->RecordCount();

		$history = $row['history']."\nDeleted ".date("Y-m-d H:i:s")." [".$HTTP_SESSION_VARS['sess_temp_userid']."]";

		$this->sql = "UPDATE seg_service_pricelist
										SET status='deleted',
										history=".$db->qstr($history).",
										modify_dt=".$db->qstr(date("Y-m-d H:i:s")).",
										modify_id=".$db->qstr($HTTP_SESSION_VARS['sess_temp_userid'])."
										WHERE area_code='$area'
										AND ref_source='$refsource'
										AND service_code='$code'";
		if ($rcount){
			$db->Execute($this->sql);
			if ($db->Affected_Rows()){
				$count++;
			}
		}

		if($count) return true;
		else return false;
	}

	function updatePriceListHistory($code,$refsource,$area,$pcash,$pcharge)
	{
		global $db,$HTTP_SESSION_VARS;
		$count = 0;
		$history.=" Modified ".date("Y-m-d H:i:s")." [".$HTTP_SESSION_VARS['sess_temp_userid']."]";

		$this->sql = "UPDATE seg_service_pricelist
									SET price_cash=".$db->qstr($pcash).",
										price_charge=".$db->qstr($pcharge).",
										history=".$db->qstr($history).",
										modify_dt=".$db->qstr(date("Y-m-d H:i:s")).",
										modify_id=".$db->qstr($HTTP_SESSION_VARS['sess_temp_userid'])."
									WHERE area_code='$area'
									AND ref_source='$refsource'
									AND service_code='$code'";
		#echo $this->sql;
		$db->Execute($this->sql);
		if ($db->Affected_Rows())
		{
			$count++;
		}

		if($count) return true;
		else return false;

	}

	#-----------

}
?>
