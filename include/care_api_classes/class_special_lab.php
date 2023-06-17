<?php
// Class for updating `seg_pharma_retail` and `seg_pharma_rdetails` tables.
// Created: 4-10-2006 (Lemuel S. Trazo)

require('./roots.php');
require_once($root_path.'include/care_api_classes/class_core.php');

class SegSpecialLab extends Core {

	/**
	* Database table for the discount data
	* @var string
	*/
	var $tb_splab_request='seg_lab_serv';

	var $tb_splab_request_details='seg_lab_servdetails';

	/**
	* Reference number
	* @var string
	*/
	var $refno;

	/**
	* SQL query result. Resulting ADODB record object.
	* @var object
	*/
	var $result;

	/**
	* Resulting record count
	* @var int
	*/
	var $count;

	/**
	* Fieldnames of the care_appointment table.
	* @var array
	*/
	var $fld_splab_request=array(
		"refno",
		"serv_dt",
		"serv_tm",
		"encounter_nr",
		"pid",
		"is_cash",
		"is_urgent",
		"modify_id",
		"modify_dt",
		"create_id",
		"create_dt",
		"history",
		"comments",
		"ordername",
		"orderaddress",
		"status",
		"discountid",
		"loc_code",
		"discount",
		"is_pe",
		"source_req",
		"is_repeat",
		"grant_type",
		"ref_source"
		);

	var $fld_splab_request_details=array(
		"refno",
		"service_code",
		"price_cash",
		"price_charge",
		"request_doctor",
		"request_dept",
		"is_in_house",
		"clinical_info",
		"status",
		"is_served",
		"date_served",
		"clerk_served_by",
		"clerk_served_date",
		"request_flag"
	);

	/**
	* Constructor
	* @param string refno
					*/
	function SegSpecialLab(){
		$this->setTable($this->tb_splab_request);
		$this->setRefArray($this->fld_splab_request);
	}

	/**
	* Sets the core object to point to seg_discount and corresponding field names.
	*/
	function useSegSpecialLabHeader(){
		$this->coretable=$this->tb_splab_request;
		$this->ref_array=$this->fld_splab_request;
	}

	function useSegSpecialLabDetails(){
		$this->coretable=$this->tb_splab_request_details;
		$this->ref_array=$this->fld_splab_request_details;
	}

	function SearchService($source_req='LD', $is_charge2comp=0, $compID='',$ref_source,$is_cash=1,$discountid='',$discount=0, $is_senior=0, $is_walkin=0, $sc_walkin_discount=0,$group_code,$codenum, $searchkey='',$multiple=0,$maxcount=100,$offset=0,$area=''){
		global $db, $sql_LIKE, $root_path, $date_format;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		if ($ref_source){
				if ($ref_source=='LB')
					$grp_cond = " AND s.group_code NOT IN ('SPL','IC') ";
				else{
					if ($ref_source=='BB')
						$ref_source = 'B';
					$grp_cond = " AND s.group_code='".$ref_source."' ";
				}
		}else
				#$grp_cond = "";
				$grp_cond = " AND s.group_code NOT IN ('SPL','IC') ";

		$grp_cond2 = "";
		if ($group_code)
			$grp_cond2 = " AND s.group_code='$group_code'";

		if ($area=='ER')
			$area_cond = " AND is_ER=1 ";
		else
			$area_cond = "";

		if ($discountid){
			$with_disc_query = " IF(s.is_socialized=0,
														 IF((s.in_phs=1 AND '$discountid'='PHS' AND $is_cash),(s.price_cash*(1-$discount)),IF($is_cash,IF($is_senior,s.price_cash*(1-$sc_walkin_discount),s.price_cash),s.price_charge)),
														 IF($is_cash,
																	 IF($is_senior,IF($is_cash,IF($is_walkin,(s.price_cash*(1-$sc_walkin_discount)),
																	 IF(sd.price,sd.price,(s.price_cash*(1-$discount)))),s.price_charge),
																	 IF($is_cash,
																			 IF(sd.price,sd.price,
																				 IF($is_cash,
																							(s.price_cash*(1-$discount)),
																							(s.price_charge*(1-$discount))
																				 )
																			 ),
																			 s.price_charge
																		)
															),
															s.price_charge)
													) AS net_price, s.*
											FROM seg_lab_services AS s
											INNER JOIN seg_lab_service_groups AS g ON g.group_code=s.group_code
											LEFT JOIN seg_service_discounts AS sd ON sd.service_code=s.service_code
																AND sd.service_area='LB' AND sd.discountid='$discountid' ";
		} else{
			if ($source_req=='IC'){
				if ($is_charge2comp){
					$sql_ic_row = " IF(ics.price,ics.price,IF($is_cash,s.price_cash,s.price_charge)) AS net_price, ";
					$sql_ic_join = " LEFT JOIN seg_industrial_comp_price AS ics ON ics.service_code=s.service_code
														AND ics.company_id='".$compID."' AND ics.service_area='LB'";
				}else{
					$sql_ic_row = " IF($is_cash,s.price_cash,s.price_charge) AS net_price, ";
					$sql_ic_join = " ";
				}

				$with_disc_query = 	$sql_ic_row."
															s.*
														FROM seg_lab_services AS s
														INNER JOIN seg_lab_service_groups AS g ON g.group_code=s.group_code".$sql_ic_join;
			}else{
				$with_disc_query = "  IF($is_cash,s.price_cash,s.price_charge) AS net_price, s.*
														FROM seg_lab_services AS s
														INNER JOIN seg_lab_service_groups AS g ON g.group_code=s.group_code ";
			}
		}


		if ($multiple){
			$keyword = $searchkey;

			if ($codenum)
				$cond_where = " AND (s.code_num IN (".$keyword.")) ";
			else
				$cond_where = "  AND (s.service_code IN (".$keyword.")) ";

			$this->sql = "SELECT DISTINCT SQL_CALC_FOUND_ROWS
											$with_disc_query
											WHERE  s.status NOT IN (".$this->dead_stat.")
											$grp_cond
											$grp_cond2
											$cond_where
											AND s.status NOT IN (".$this->dead_stat.")
											$area_cond
											ORDER BY s.name";
		}else{
			# convert * and ? to % and &
			$searchkey=strtr($searchkey,'*?','%_');
			$searchkey=trim($searchkey);
			#$suchwort=$searchkey;
			$searchkey = str_replace("^","'",$searchkey);
			$keyword = addslashes($searchkey);

			if (is_numeric($keyword)){
				 $cond_where = " AND (s.service_code = '".$keyword."'
										OR s.name LIKE '".$keyword."' OR s.code_num LIKE '".$keyword."') ";
			}else{
				 $cond_where = "  AND (s.service_code LIKE '%".$keyword."%'
									OR s.name LIKE '%".$keyword."%' OR s.code_num LIKE '%".$keyword."%') ";
			}

			$this->sql = "SELECT DISTINCT SQL_CALC_FOUND_ROWS
													$with_disc_query
											WHERE  s.status NOT IN (".$this->dead_stat.")
											$grp_cond
											$grp_cond2
											$cond_where
											$area_cond
											ORDER BY s.name";
		}


		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}

	#added by VAN 04-20-2010
	function isServiceAPackage($service_code){
				global $db;

				$this->sql="SELECT count(service_code_child) AS count_child
											FROM seg_lab_group AS lg
											WHERE service_code='".$service_code."'";

				if ($this->result=$db->Execute($this->sql)) {
					$row=$this->result->FetchRow();
					$this->count=$row['count_child'];
					return $this->count;
				} else{
					 return FALSE;
				}
	}

	function getAllServiceOfPackage($service_code, $is_cash=1, $discountid='',$discount=0, $is_senior=0, $is_walkin=0, $sc_walkin_discount=0){
				global $db;

				$this->sql="SELECT SQL_CALC_FOUND_ROWS lg.service_code_child AS service_code, s.service_code,
													s.name, s.price_cash, s.price_charge, s.is_socialized, s.in_phs,s.in_lis, s.oservice_code, s.group_code,
													IF(s.is_socialized=0,
														 IF((s.in_phs=1 AND '$discountid'='PHS' AND $is_cash),(s.price_cash*(1-$discount)),IF($is_cash,IF($is_senior,s.price_cash*(1-$sc_walkin_discount),s.price_cash),s.price_charge)),
														 IF($is_cash,
																IF($is_senior,IF($is_cash,IF($is_walkin,(s.price_cash*(1-$sc_walkin_discount)),
																	 IF(sd.price,sd.price,(s.price_cash*(1-$discount)))),s.price_charge),
																	 IF($is_cash,
																			 IF(sd.price,sd.price,
																				 IF($is_cash,
																							(s.price_cash*(1-$discount)),
																							(s.price_charge*(1-$discount))
																				 )
																			 ),
																			 s.price_charge
																		)
															),
															s.price_charge)
													) AS net_price
										FROM seg_lab_group AS lg
										INNER JOIN seg_lab_services AS s ON s.service_code=lg.service_code_child
										LEFT JOIN seg_service_discounts AS sd ON sd.service_code=s.service_code
												AND sd.service_area='LB' AND sd.discountid='$discountid'
										WHERE lg.service_code='".$service_code."'";

				if ($this->result=$db->Execute($this->sql)) {
					$this->count=$this->result->RecordCount();
					return $this->result;
				} else{
					 return FALSE;
				}
	}

	function ServedLabRequest($refno, $service_code, $is_served, $date_served){
		global $db, $HTTP_SESSION_VARS;
		$ret=FALSE;

		#$history = CONCAT(history,"To be Served in Special Laboratory : ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");

		$this->sql="UPDATE seg_lab_servdetails SET
						status = 'done',
						is_served='".$is_served."',
						date_served='".$date_served."',
						clerk_served_by='".$HTTP_SESSION_VARS['sess_user_name']."',
						clerk_served_date=NOW()
						WHERE refno = '".$refno."'
						AND service_code = '".$service_code."'";

		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
		else return FALSE;

	}

	function get_TestAllowedER($service_code=''){
		global $db;

		#$this->sql ="SELECT service_code FROM seg_lab_er_test WHERE service_code='$service_code'";
		$this->sql = "SELECT service_code
									FROM seg_lab_er_test
									WHERE service_code='$service_code'
									UNION
									SELECT service_code
									FROM seg_lab_services
									WHERE group_code IN (SELECT group_code FROM seg_lab_er_section)
									AND service_code='$service_code'";

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}


//added by poliam 03/28/2014
	function GetRoomType($encounter_nr){
		global $db;

		$this->sql = "SELECT cr.`type_nr`
						FROM care_encounter `ce`
						LEFT JOIN care_room `cr`
						ON cr.`ward_nr` = ce.`current_ward_nr`
						AND cr.`room_nr` = ce.`current_room_nr`
						WHERE encounter_nr =".$db->qstr($encounter_nr);

		if($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount()){
				while($row=$this->result->FetchRow()){
					return $row['type_nr'];	
				}
			}else{
				return FALSE;
			}	
		}else{
			return FALSE;
		}
	}

	function GetRoomPrice($service_code, $type, $source='LB', $is_cash){
		global $db;

		$this->sql ="SELECT cash_price, charge_price, IF($is_cash, sor.`cash_price`, sor.`charge_price`) AS net_price
						FROM seg_other_rates `sor`
						WHERE service_code = ".$db->qstr($service_code)."
						AND room_type = ".$db->qstr($type)."
						AND source = ".$db->qstr($source);

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount())
				return $this->result;
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}
//ended by poliam 03/28/2014


}
?>