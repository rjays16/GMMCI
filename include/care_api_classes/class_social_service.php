<?php
require_once($root_path.'include/care_api_classes/class_core.php');

class SocialService extends Core{

	#var $tb_social_service = 'seg_social_service';
	var $tb_seg_discount = 'seg_discount';

	var $tb_encounter_sservice = 'seg_encounter_sservice';

	var $tb_seg_charity_grants = 'seg_charity_grants';

	#added by VAN 05-09-08
	var $tb_seg_socserv_patient = 'seg_socserv_patient';
	var $tb_seg_charity_grants_pid = 'seg_charity_grants_pid';

	var $tb_seg_social_patient = 'seg_social_patient';

	var $tb_seg_charity_amount = 'seg_charity_amount';
	var $tb_seg_override_amount = 'seg_override_amount';

    var $tb_seg_social_patient_family = 'seg_social_patient_family'; #Added by Jarel 03-01-13

	var $count;
	var $sql;
    #added by Jarel 03-01-13
    var $fld_seg_social_patient_family = array(
                                         'pid',
                                         'dependent_id',
                                         'dependent_name',
                                         'dependent_age',
                                         'dependent_status',
                                         'ralation_to_patient',
                                         'dep_educ_attainment',
                                         'dependent_occupation',
                                         'dep_monthly_income'
                                         );

	#added by VAN 07-05-08
	var $tb_seg_social_modifier = 'seg_social_service_submodifiers';
	var $fld_social_modifier = array(
						'mod_code',
						'mod_subcode',
						'mod_subdesc'
	);
	#-------------------

	var $fld_seg_discount = array(
							'discountid',
							'discountdesc',
							'discount',
							'area_used',
							'is_forall',
							'is_charity',
							'lockflag',
							'parentid',
							'modify_id',
							'modify_timestamp',
							'create_id',
							'create_timestamp'

						);

	var $fld_encounter_sservice = array(
						'encounter_nr',
						'service_code',
						'ss_notes',
						'ss_history',
						'modify_id',
						'modify_time',
						'create_id',
						'create_time'
			);

	var $fld_seg_charity_grants = array(
						'encounter_nr',
						'grant_dte',
						'sw_nr',
						'discountid',
						'discount',
						'notes',
						'personal_circumstance',
						'community_situation',
						'nature_of_disease',
						'other_name',
						'id_number'
			);

	#added by VAN 05-13-08
	var $fld_seg_charity_grants_pid = array(
						'pid',
						'grant_dte',
						'sw_nr',
						'discountid',
						'discount',
						'notes',
						'personal_circumstance',
						'community_situation',
						'nature_of_disease',
						'other_name',
						'id_number'
			);

	var $fld_seg_charity_amount = array(
						'ref_no',
						'ref_source',
						'grant_dte',
						'sw_nr',
						'amount'
					);

	var $fld_seg_override_amount = array(
						'ref_no',
						'ref_source',
						'grant_dte',
						'personnel_nr',
						'amount'
					);

	#added by VAN 05-09-08
	var $fld_seg_social_patient = array(
		'mss_no',
		'pid',
		'status',
		'history',
		'modify_id',
		'modify_time',
		'create_id',
		'create_time'
	);

	var $fld_seg_socserv_patient = array(
						'mss_no',
						'encounter_nr',
						'informant_name',
						'info_address',
						'relation_informant',
						'educational_attain',
						'source_income',
						'monthly_income',
						'nr_dependents',
						'hauz_lot_expense',
						'food_expense',
						'ligth_expense',
						'water_expense',
						'transport_expense',
						'other_expense',
						'status',
						'history',
						'modify_id',
						'modify_time',
						'create_id',
						'create_time'
					);

	function SocialService(){
		//$this->coretable = $this->tb_social_service;
		//$this->ref_array = $this->fld_social_service;
	}

	#added by VAN 05-09-08
	function useSCPatTable(){
		$this->coretable = $this->tb_seg_socserv_patient;
		$this->ref_arry = $this->fld_seg_socserv_patient;
	}

    #added by Jarel 03-01-13
    function useSSPFTable(){
        $this->coretable = $this->tb_seg_social_patient_family;
        $this->ref_arry = $this->fld_seg_social_patient_family;
    }

	function useSCRecTable(){
		$this->coretable = $this->tb_seg_social_patient;
		$this->ref_arry = $this->fld_seg_social_patient;
	}

	function useSCCATable(){
		$this->coretable = $this->tb_seg_charity_amount;
		$this->ref_array = $this->fld_seg_charity_amount;
	}

	function useOverrideTable(){
		$this->coretable = $this->tb_seg_override_amount;
		$this->ref_array = $this->fld_seg_override_amount;
	}

	function useSStable(){
		#$this->coretable = $this->tb_social_service;
		#$this->ref_array = $this->fld_social_service;
		$this->coretable = $this->tb_seg_discount;
		$this->ref_array = $this->fld_seg_discount;
	}

	#added by VAN 07-05-08
	function useSMtable(){
		#$this->coretable = $this->tb_social_service;
		#$this->ref_array = $this->fld_social_service;
		$this->coretable = $this->tb_seg_social_modifier;
		$this->ref_array = $this->fld_social_modifier;
	}
	#------------

	function useESStable(){
		$this->coretable = $this->tb_encounter_sservice;
		$this->ref_array = $this->fld_encounter_sservice;
	}

	function useSCGtable(){
		$this->coretable = $this->tb_seg_charity_grants;
		$this->ref_array = $this->fld_seg_charity_grants;
	}

	#added by VAN 05-13-08
	function useSCGPIDtable(){
		$this->coretable = $this->tb_seg_charity_grants_pid;
		$this->ref_array = $this->fld_seg_charity_grants_pid;
	}

	function updateSocialService($code,&$data){
		$this->useSStable();
		$this->data_array = $data;
		$this->where="service_code='$code'";
		return $this->updateDataFromInternalArray($code);

	}//end of updateSocialService()

	function getSSInfo($code='', $discountID='', $parentID=''){
		global $db;

		if(empty($code)){
				#edited by VAN 07-04-08
				$this->sql = "SELECT d.discountid, d.discountdesc, d.discount, d.parentid
								FROM seg_discount AS d
								WHERE d.parentid IN ('')
                                AND is_visible=1
								ORDER BY discountdesc";
				#--------------------
			#}	#commented by VAN 07-05-08

		}else{
			 $this->sql = "SELECT * FROM $this->tb_seg_discount WHERE discountid ='".$code."'
										 ORDER BY discountdesc";
		}

		if($this->res['ss'] = $db->Execute($this->sql)){
			if($this->res['ss']->RecordCount()){
				return $this->res['ss'];
			}else { return FALSE; }
		}else { return FALSE;}
	}//end function getSSInfo()

    #Added by Jarel 04/20/2013
    function getSSInfo1($isAdditional){
        global $db;

        $this->sql = "SELECT * FROM $this->tb_seg_discount WHERE is_additional_support ='".$isAdditional."'";

        if($this->res['ss'] = $db->Execute($this->sql)){
            if($this->res['ss']->RecordCount()){
                return $this->res['ss'];
            }else { return FALSE; }
        }else { return FALSE;}
    }

    function getSectoral($parent_id){
        global $db;

        $this->sql = "SELECT d.discountid, d.discountdesc, d.discount, d.parentid
                        FROM seg_discount AS d
                        WHERE d.parentid IN ('$parent_id')
                        AND is_visible=1
                        ORDER BY discountdesc";

        if($this->res['ss'] = $db->Execute($this->sql)){
            if($this->res['ss']->RecordCount()){
                return $this->res['ss'];
            }else { return FALSE; }
        }else { return FALSE;}
    }//end function getSSInfo()

    function getModifier2(){
        global $db;

        $this->sql = "SELECT * FROM seg_social_service_modifiers";

        if($this->res['ss'] = $db->Execute($this->sql)){
            if($this->res['ss']->RecordCount()){
                return $this->res['ss'];
            }else { return FALSE; }
        }else { return FALSE;}
    }//end function getSSInfo()

    function getSubModifier(){
        global $db;

        $this->sql = "SELECT * FROM seg_social_service_submodifiers
                        WHERE is_deleted=0";

        if($this->res['ss'] = $db->Execute($this->sql)){
            if($this->res['ss']->RecordCount()){
                return $this->res['ss'];
            }else { return FALSE; }
        }else { return FALSE;}
    }//end function getSSInfo()

	function getSServiceInfo($encounter_nr){
		global $db;
		$this->sql = "SELECT * FROM $this->tb_encounter_sservice WHERE encounter_nr ='$encounter_nr'";
		if($this->res['ssr'] = $db->Execute($this->sql)){
			if($this->res['ssr']->RecordCount()){
				return $this->res['ssr'];
			}else {return FALSE; }
		}else { return FALSE; }
	}// end function getSServiceInfo()

		 function getSSCInfo($encounter_nr='', $pid='', $grant_by_enc=0,$offset=0, $rows=10, $sort_order="sg.grant_dte DESC", $withdate=0, $issc=0){
				global $db;

						#edited by VAN 05-25-09
						if ($withdate){
								if ($issc==0){
										$sql_date = " AND IF(enc.encounter_type='2' OR ISNULL(enc.encounter_type) ,DATE_ADD(date(grant_dte), INTERVAL 7 DAY)>=DATE(now()),enc.is_discharged=0) ";
								}else{
										$sql_date = " ";

								}
						}else{
								$sql_date = "";
						}

						if (($encounter_nr)&&($grant_by_enc)){
								$this->sql ="SELECT SQL_CALC_FOUND_ROWS d.parentid,d.discountdesc,sg.other_name, sg.grant_dte, sg.discountid, sg.discount, cl.job_function_title, cl.nr, cp.name_first, " .
												"\n  cp.name_last, cp.name_middle, sg.personal_circumstance as pcircumstance, sg.community_situation as csituation, ".
												"\n  sg.nature_of_disease as ndesease, sg.encounter_nr, enc.encounter_type  " .
												"\n     FROM  seg_charity_grants as sg ".
												"\n     INNER JOIN care_personell as cl ON cl.nr = sg.sw_nr ".
												"\n     INNER JOIN care_person as cp ON cl.pid = cp.pid ".
												"\n     LEFT JOIN seg_discount as d ON d.discountid=sg.discountid".
												"\n     LEFT JOIN care_encounter AS enc ON enc.encounter_nr = '$encounter_nr' ".
												"\n WHERE sg.encounter_nr = '".$encounter_nr."'
													 $sql_date ORDER BY $sort_order LIMIT $offset,$rows";


						}else{

								$this->sql ="SELECT SQL_CALC_FOUND_ROWS d.parentid,enc.encounter_type, enc.is_discharged,
																d.discountdesc,sg.id_number, sg.other_name,  sg.grant_dte, sg.discountid, sg.discount, cl.job_function_title, cl.nr, cp.name_first, " .
												"\n  cp.name_last, cp.name_middle, sg.personal_circumstance as pcircumstance, sg.community_situation as csituation, ".
												"\n  sg.nature_of_disease as ndesease, sg.pid " .
												"\n     FROM  seg_charity_grants_pid as sg ".
												"\n     INNER JOIN care_personell as cl ON cl.nr = sg.sw_nr ".
												"\n     INNER JOIN care_person as cp ON cl.pid = cp.pid ".
												"\n   LEFT JOIN seg_discount as d ON d.discountid=sg.discountid".
												"\n    LEFT JOIN care_encounter AS enc ON enc.encounter_nr='$encounter_nr'".
												"\n WHERE sg.pid = '$pid' $sql_date ORDER BY $sort_order LIMIT $offset,$rows";
						}
					 #echo "query here: ".$this->sql; //edited by CHA 10-14-09
				if($this->egg['ss'] = $db->Execute($this->sql)){
						$this->count = $this->egg['ss']->RecordCount();
						if($this->egg['ss']->RecordCount()!=1){
								return $this->egg['ss'];
						}else{
								return $this->egg['ss'];

						}
				}else{ return false; }

		}//getSSCInfo

	//TODO: CHANGE the query..
	function getLCRInfo($encounter_nr, $offset=0, $rows=10, $sort_order="date_request DESC", $allowedWKCL=0, $pid=''){
		global $db;
		#edited by VAN 01-29-10
		$enc_set_sql = " pid='".$pid."' ";
		#----------------

		$this->sql=	"SELECT SQL_CALC_FOUND_ROWS lab.refno, sum(lab.price_cash*lab.quantity) as total_charge, lab.serv_dt as date_request, serv_tm AS time_request, lab.dept FROM ".
					"\n	(SELECT ls.refno,lsd.service_code,sls.name, ls.ref_source as dept ,lsd.price_cash , lsd.price_charge,lsd.quantity,ls.serv_dt, ls.serv_tm ".
					"\n		FROM (seg_lab_serv as ls ".
					"\n		INNER JOIN seg_lab_servdetails as lsd ON ls.refno = lsd.refno  AND request_flag IS NULL) ".
					"\n		INNER JOIN seg_lab_services as sls ON lsd.service_code = sls.service_code ".
					/*"\n	WHERE ls.encounter_nr = '".$encounter_nr."'  */
										"\n    WHERE ".$enc_set_sql."
												AND ls.is_cash<>'0'
												AND DATEDIFF(DATE(now()),date(ls.serv_dt))< 30
								AND ls.status NOT IN ($this->dead_stat) AND lsd.status NOT IN ($this->dead_stat) ".
									"/*AND (ls.refno NOT IN(SELECT ls.refno
												FROM seg_lab_serv as ls
												INNER JOIN seg_lab_servdetails as lsd ON ls.refno = lsd.refno
											 WHERE ".$enc_set_sql."
												AND ls.serv_dt<>date(NOW()) AND lsd.price_cash<>lsd.price_cash_orig))*/) as lab ".
					"\n GROUP BY lab.refno ".
					"\n UNION ALL ".
					"\n SELECT rad.refno, sum(rad.price_cash) as total_charge, rad.request_date as date_request, request_time AS time_request, rad.dept FROM ".
					"\n  (SELECT srs.refno, ctr.service_code , srv.name, 'RD' as dept ,ctr.price_cash, ctr.price_charge , srs.request_date, srs.request_time ".
					"\n		FROM (seg_radio_serv  as srs ".
					"\n 	INNER JOIN care_test_request_radio as ctr on ctr.refno = srs.refno  AND request_flag IS NULL) ".
					"\n	INNER JOIN seg_radio_services as srv on ctr.service_code = srv.service_code	".
					/*"\n WHERE  srs.encounter_nr = '".$encounter_nr."'  */
										"\n WHERE  ".$enc_set_sql."
														AND srs.is_cash <>'0'
														AND DATEDIFF(DATE(now()),date(srs.request_date))< 30
								AND ctr.status NOT IN ($this->dead_stat)  ".
								"/*AND (srs.refno NOT IN(SELECT srs.refno
										FROM seg_radio_serv as srs
										INNER JOIN care_test_request_radio as ctr ON srs.refno = ctr.refno
										WHERE ".$enc_set_sql."
										AND srs.request_date<>date(NOW()) AND ctr.price_cash<>ctr.price_cash_orig))*/) as rad ".
					"\n GROUP BY rad.refno".
					"\n UNION ALL".
					"\n	SELECT  ph.refno, sum(ph.itemcharge) as total_charge, date(ph.orderdate) as date_request, time(ph.orderdate) AS time_request, ph.dept FROM ".
					"\n	(SELECT spo.refno, spi.bestellnum, cpp.artikelname, spo.department as dept, spi.quantity as qty, spo.is_cash, ".
					"\n		spi.pricecash, spi.pricecharge, spo.orderdate, sum(quantity * pricecash) as itemcharge ".
					"\n		FROM (seg_pharma_orders as spo ".
					"\n			INNER JOIN seg_pharma_order_items as spi ON spo.refno = spi.refno  AND request_flag IS NULL) ".
					"\n		INNER JOIN care_pharma_products_main as cpp on spi.bestellnum = cpp.bestellnum ".
					"\n		WHERE ".$enc_set_sql."   AND spo.is_cash<>'0'
									AND DATEDIFF(DATE(now()),date(spo.orderdate))< 30
									/*AND pharma_area!='MG'*/ ".
									"/*AND (spo.refno NOT IN(SELECT spo.refno
										FROM seg_pharma_orders as spo
										INNER JOIN seg_pharma_order_items as spi ON spo.refno = spi.refno
									 WHERE ".$enc_set_sql."
										AND date(spo.orderdate)<>date(NOW()) AND spi.pricecash<>spi.price_orig))*/ group by spo.refno) as ph ".
					"\n	GROUP BY ph.refno".
                    "\n UNION ALL".
                    "\n    SELECT  ms.refno, SUM(ms.itemcharge) AS total_charge, DATE(ms.chrge_dte) AS date_request,TIME(ms.chrge_dte) AS time_request, ms.dept FROM ".
                    "\n    (SELECT sms.refno, 'M' AS dept, smsd.quantity AS qty, sms.is_cash, ".
                    "\n        sms.chrge_dte, SUM(adjusted_amnt) AS itemcharge  ".
                    "\n       FROM (seg_misc_service AS sms ".
                    "\n            INNER JOIN seg_misc_service_details AS smsd ON sms.refno = smsd.refno  AND request_flag IS NULL)".
                    "\n        WHERE ".$enc_set_sql."  AND sms.is_cash<>'0'
                                    AND DATEDIFF(DATE(NOW()),DATE(sms.chrge_dte))< 30
                                    GROUP BY sms.refno) AS ms ".
                       "\n GROUP BY ms.refno".
						"\n	ORDER BY $sort_order LIMIT $offset, $rows";

		if( $result= $db->Execute($this->sql)){
			if($result->RecordCount()){
				return $result;
			}else{ return false;}
		}else{ return false; }

	} // end of function getLCRInfo

	//save data for discount
	function saveSSDiscount(&$data){
		$this->useSCCATable();
		$this->data_array = $data;
		return $this->insertDataFromInternalArray();
	}


	function saveSServiceInfo(&$data){
		$this->useESStable();
		$this->data_array = $data;
		return $this->insertDataFromInternalArray();
	}// end function saveSServiceInfo()

	//Add social service code from manager
	function addSServiceData(&$data){
		$this->useSStable();
		$this->data_array = $data;
		return $this->insertDataFromInternalArray();
	}

	//Delete social service code
	function deleteSServiceData($code){
		global $db;
		$this->sql = "DELETE FROM $this->tb_seg_discount WHERE discountid = '".$code."'";
		if($db->Execute($this->sql)){
			return true;
		}else{ return FALSE;}
	}

	function updateSServiceData($code,&$data){
		$this->useSStable();
		$this->data_array = $data;
		$this->where = "discountid= '".$code."'";

		return $this->updateDataFromInternalArray($code, FALSE);
	}
	//Update social service code
	function updateSServiceInfo($encounter_nr, &$data){
		$this->useESStable();
		$this->data_array = $data;
		$this->where = "encounter_nr = '$encounter_nr'";
		return $this->updateDataFromInternalArray($data);
	}// end of updateSServiceInfo()

	//save soscial service classfication
	function saveSSCData(&$data){
		$this->useSCGtable();
		$this->data_array = $data;
		#echo "here";
		return $this->insertDataFromInternalArray();
	} // end functon saveSSCData

	#added by VAN 05-13-08
	function saveSSCDataByPID(&$data){
		$this->useSCGPIDtable();
		$this->data_array = $data;
		return $this->insertDataFromInternalArray();
	} // end functon saveSSCData

	function saveCharityAmnt(&$data){
		$this->useSCCATable();
		$this->data_array = $data;
		return $this->insertDataFromInternalArray();
	}

#edited by VAN 05-14-08
	function countSearchSelect($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE) {
		global $db, $sql_LIKE, $root_path, $date_format;
		#$db->debug=true;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		$suchwort=$searchkey;

		if(is_numeric($suchwort)) {
			#$suchwort=(int) $suchwort;
			$this->is_nr=TRUE;

			if(empty($oitem)) $oitem='encounter_nr';
			if(empty($odir)) $odir='DESC'; # default, latest pid at top

			$sql2W="	WHERE (ce.pid = '$suchwort')";

			$sql2WB=" WHERE (r.pid = '$suchwort')";

		} else {
			# Try to detect if searchkey is composite of first name + last name
			if(stristr($searchkey,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}

			$searchkey=strtr($searchkey,',',' ');
			$cbuffer=explode(' ',$searchkey);

			# Remove empty variables
			for($x=0;$x<sizeof($cbuffer);$x++){
				$cbuffer[$x]=trim($cbuffer[$x]);
				if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
			}

			# Arrange the values, ln= lastname, fn=first name, bd = birthday
			if($lastnamefirst){
				$fn=$comp[1];
				$ln=$comp[0];
				$rd=$comp[2];
			}else{
				$fn=$comp[0];
				$ln=$comp[1];
				$rd=$comp[2];
			}
			# Check the size of the comp
			if(sizeof($comp)>1){
				$sql2W=" WHERE (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%') ";
				$sql2WB=" WHERE (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%') ";

				if(!empty($rd)){
					$DOB=@formatDate2STD($rd,$date_format);
					if($DOB==''){
						$sql2W .= " AND grant_dte $sql_LIKE '$rd%' ";
						$sql2WB .= " AND grant_dte $sql_LIKE '$rd%' ";
					}else{
						$sql2W.=" AND date(grant_dte) = '$DOB' ";
						$sql2WB.=" AND date(grant_dte) = '$DOB' ";
					}
				}
			}else{
				# Check if * or %
				if($suchwort=='%'||$suchwort=='%%'){
					#return all the data
					$sql2W = " WHERE ce.status NOT IN ($this->dead_stat) ";
					$sql2WB = "";
				}elseif($suchwort=='now'){
					#$sql2 = "WHERE r.grant_dte = now() ";
					#edited by VAN 05-13-08
					$sql2W = "WHERE date(r.grant_dte)=date(now()) ";
					$sql2WB = "WHERE date(r.grant_dte)=date(now()) ";
				}else{
					# Check if it is a complete DOB
					$DOB=@formatDate2STD($suchwort,$date_format);
					if($DOB=='') {
						if(TRUE){
							if($fname){
								#edited by VAN 01-17-08
								$sql2W=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR r.discountid LIKE '%$suchwort%') ";
								$sql2WB=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR r.discountid LIKE '%$suchwort%') ";
							}else{
								$sql2W=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
								$sql2WB=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
							}
						}else{
							$sql2W=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
							$sql2WB=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
						}
					}else{
						$sql2W = " WHERE date(grant_dte) = '$DOB' ";
						$sql2WB = " WHERE date(grant_dte) = '$DOB' ";
					}
				}
			}
		 }
				 /*
				 if ($sql2W)
						$sql2W = $sql2W." AND DATE_ADD(date(r.grant_dte), INTERVAL 7 DAY)>=DATE(now()) OR r.discountid='SC'";
				 else
						$sql2W = " WHERE DATE_ADD(date(r.grant_dte), INTERVAL 7 DAY)>=DATE(now()) OR r.discountid='SC'";

				 if ($sql2WB)
						$sql2WB = $sql2WB." AND DATE_ADD(date(r.grant_dte), INTERVAL 7 DAY)>=DATE(now()) OR r.discountid='SC'";
				 else
					 $sql2WB = " WHERE DATE_ADD(date(r.grant_dte), INTERVAL 7 DAY)>=DATE(now()) OR r.discountid='SC'";
				 */

		$sql2 = ' AS r
					 INNER JOIN care_encounter AS ce ON ce.encounter_nr=r.encounter_nr AND is_discharged=0
					 INNER JOIN care_person AS p ON p.pid=ce.pid
										 LEFT JOIN seg_discount AS d ON d.discountid=r.discountid
					 LEFT JOIN seg_social_patient as sm on sm.pid=p.pid '.$sql2W;

		$sql2B = ' AS r
					 INNER JOIN care_person AS p ON p.pid=r.pid
										 LEFT JOIN seg_discount AS d ON d.discountid=r.discountid
					 LEFT JOIN seg_social_patient as sm on sm.pid=p.pid '.$sql2WB;


		//$this->buffer=$this->tb_lab_serv.$sql2;
		#$this->buffer=$this->tb_seg_charity_grants.$sql2;
		$this->buffer=$this->tb_seg_charity_grants.$sql2;

		# Save the query in buffer for pagination
		# Set the sorting directive

		#if(isset($oitem)&&!empty($oitem)) $sql3 =" ORDER BY is_urgent DESC,r.serv_dt ASC,refno ASC ";
		#if(isset($oitem)&&!empty($oitem)) $sql3 = "GROUP BY r.encounter_nr ".
	#															" ORDER BY r.grant_dte ASC";
	if(isset($oitem)&&!empty($oitem)) $sql3 = " GROUP BY r.encounter_nr ";

		$sql1 = "SELECT sm.mss_no, r.encounter_nr, p.pid, p.name_last, p.name_first, p.name_middle ,  r.grant_dte, ".
								"substring(max(concat(r.grant_dte, r.discountid)), 20) as discountid".
						 " FROM ". $this->buffer.$sql3;

		$sql2 = "SELECT sm.mss_no, (SELECT encounter_nr FROM care_encounter AS e WHERE e.pid=r.pid ORDER BY encounter_date DESC LIMIT 1) AS encounter_nr,
							p.pid, p.name_last, p.name_first, p.name_middle ,  r.grant_dte, ".
								/*"substring(max(concat(r.grant_dte, r.discountid)), 20) as discountid".*/
													"IF(d.parentid!='',d.parentid, substring(max(concat(r.grant_dte,r.discountid)),20)) as discountid".
						 " FROM seg_charity_grants_pid".$sql2B."
							/*AND NOT EXISTS (SELECT ep.* FROM seg_charity_grants AS ep
							INNER JOIN care_encounter AS e ON ep.encounter_nr=e.encounter_nr AND is_discharged=0
							WHERE e.pid=r.pid)   */
														GROUP BY r.pid
							ORDER BY grant_dte DESC";

		#$this->sql = $sql1." UNION ".$sql2;
				$this->sql = $sql2;

		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()) {
				return $this->result;
			}
			else{return FALSE;}
		}else{return FALSE;}
	}


	function SearchSelect($searchkey='',$maxcount=100,$offset=0,$oitem='name_last',$odir='ASC',$fname=FALSE){
		global $db, $sql_LIKE, $root_path, $date_format;
		#$db->debug=true;
		if(empty($maxcount)) $maxcount=100;
		if(empty($offset)) $offset=0;

		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
		$searchkey=trim($searchkey);
		$suchwort=$searchkey;

		if(is_numeric($suchwort)) {
			$suchwort=(int) $suchwort;
			$this->is_nr=TRUE;

			if(empty($oitem)) $oitem='encounter_nr';
			if(empty($odir)) $odir='DESC'; # default, latest pid at top

			$sql2W="	WHERE (ce.pid='$suchwort')";

			$sql2WB=" WHERE (r.pid='$suchwort')";

		} else {
			# Try to detect if searchkey is composite of first name + last name
			if(stristr($searchkey,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}

			$searchkey=strtr($searchkey,',',' ');
			$cbuffer=explode(' ',$searchkey);

			# Remove empty variables
			for($x=0;$x<sizeof($cbuffer);$x++){
				$cbuffer[$x]=trim($cbuffer[$x]);
				if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
			}

			# Arrange the values, ln= lastname, fn=first name, bd = birthday
			if($lastnamefirst){
				$fn=$comp[1];
				$ln=$comp[0];
				$rd=$comp[2];
			}else{
				$fn=$comp[0];
				$ln=$comp[1];
				$rd=$comp[2];
			}
			# Check the size of the comp
			if(sizeof($comp)>1){
				$sql2W=" WHERE (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%') ";
				$sql2WB=" WHERE (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%') ";

				if(!empty($rd)){
					$DOB=@formatDate2STD($rd,$date_format);
					if($DOB==''){
						$sql2W .= " AND grant_dte $sql_LIKE '$rd%' ";
						$sql2WB .= " AND grant_dte $sql_LIKE '$rd%' ";
					}else{
						$sql2W.=" AND date(grant_dte) = '$DOB' ";
						$sql2WB.=" AND date(grant_dte) = '$DOB' ";
					}
				}
			}else{
				# Check if * or %
				if($suchwort=='%'||$suchwort=='%%'){
					#return all the data
					$sql2W = " WHERE ce.status NOT IN ($this->dead_stat) ";
					$sql2WB = "";
				}elseif($suchwort=='now'){
					#$sql2 = "WHERE r.grant_dte = now() ";
					#edited by VAN 05-13-08
					$sql2W = "WHERE date(r.grant_dte)=date(now()) ";
					$sql2WB = "WHERE date(r.grant_dte)=date(now()) ";
				}else{
					# Check if it is a complete DOB
					$DOB=@formatDate2STD($suchwort,$date_format);
					if($DOB=='') {
						if(TRUE){
							if($fname){
								#edited by VAN 01-17-08
								$sql2W=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR r.discountid LIKE '%$suchwort%') ";
								$sql2WB=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR p.name_first $sql_LIKE '".strtr($suchwort,'+',' ')."%' OR r.discountid LIKE '%$suchwort%') ";
							}else{
								$sql2W=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
								$sql2WB=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
							}
						}else{
							$sql2W=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
							$sql2WB=" WHERE p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%' ";
						}
					}else{
						$sql2W = " WHERE date(grant_dte) = '$DOB' ";
						$sql2WB = " WHERE date(grant_dte) = '$DOB' ";
					}
				}
			}
		 }

		$sql2 = ' AS r
					 INNER JOIN care_encounter AS ce ON ce.encounter_nr=r.encounter_nr AND is_discharged=0
					 INNER JOIN care_person AS p ON p.pid=ce.pid
										 LEFT JOIN seg_discount AS d ON d.discountid=r.discountid
					 LEFT JOIN seg_social_patient as sm on sm.pid=p.pid '.$sql2W;

		$sql2B = ' AS r
					 INNER JOIN care_person AS p ON p.pid=r.pid
										 LEFT JOIN seg_discount AS d ON d.discountid=r.discountid
					 LEFT JOIN seg_social_patient as sm on sm.pid=p.pid '.$sql2WB;


		//$this->buffer=$this->tb_lab_serv.$sql2;
		#$this->buffer=$this->tb_seg_charity_grants.$sql2;
		$this->buffer=$this->tb_seg_charity_grants.$sql2;

		# Save the query in buffer for pagination
		# Set the sorting directive

		#if(isset($oitem)&&!empty($oitem)) $sql3 =" ORDER BY is_urgent DESC,r.serv_dt ASC,refno ASC ";
		#if(isset($oitem)&&!empty($oitem)) $sql3 = "GROUP BY r.encounter_nr ".
	#															" ORDER BY r.grant_dte ASC";
	if(isset($oitem)&&!empty($oitem)) $sql3 = " GROUP BY r.encounter_nr ";

		$sql1 = "SELECT sm.mss_no, r.encounter_nr, p.pid, p.name_last, p.name_first, p.name_middle ,  r.grant_dte, ".
								"substring(max(concat(r.grant_dte, r.discountid)), 20) as discountid".
						 " FROM ". $this->buffer.$sql3;

				 $sql2 = "SELECT sm.mss_no, (SELECT encounter_nr FROM care_encounter AS e WHERE e.pid=r.pid ORDER BY encounter_date DESC LIMIT 1) AS encounter_nr,
														p.pid, p.name_last, p.name_first, p.name_middle ,  r.grant_dte, ".
																/*"substring(max(concat(r.grant_dte, r.discountid)), 20) as discountid".*/
														"IF(d.parentid!='',d.parentid, substring(max(concat(r.grant_dte,r.discountid)),20)) as discountid ".
												 " FROM seg_charity_grants_pid".$sql2B."
														GROUP BY r.pid
														ORDER BY grant_dte DESC";
		#$this->sql = $sql1." UNION ".$sql2;
				$this->sql = $sql2;

		if($this->res['ssl']=$db->SelectLimit($this->sql,$maxcount,$offset)){
			if($this->rec_count=$this->res['ssl']->RecordCount()) {
				return $this->res['ssl'];
			}else{return false;}
		}else{return false;}
	}


	#added by VAN 05-09-08
	function saveSocServPatientArray(&$data){
		global $db, $HTTP_SESSION_VARS;
		$this->useSCPatTable();

		extract($data);

		$userid = $HTTP_SESSION_VARS['sess_user_name'];

        #edited By Jarel 03-06-2013
        //updated by Daryl
        // add monthly_income_remarks (field)
        //add monthly_expenses_remarks (field)
		$index = "mss_no, pid, encounter_nr,informant_name, religion, contact_no, companion, date_interview,
                  info_address, relation_informant, educational_attain, occupation, employer,
                  employer_address, source_income, monthly_income, nr_dependents, nr_children,
                  per_capita_income, hauz_lot_expense, food_expense, light_source, ligth_expense,
                  water_source, water_expense, transport_expense, other_expense, status, history,
                  create_id, create_time, education_expense, househelp_expense,
                  fuel_source, fuel_expense, clothing_expense, med_expenditure, insurance_mortgage,
                  total_monthly_expense, address, house_type, final_diagnosis, duration_problem,
                  duration_treatment, treatment_plan, accessibility_problem, name_referral, source_referral,
                  info_agency, info_contact_no, remarks, social_worker, income, other_income, other_occupation, monthly_income_remarks, monthly_expenses_remarks,
                  is_poc";

        $values = "'$mss_no','$pid', '$encounter_nr', '$informant_name', '$religion', '$contact_no', '$companion', STR_TO_DATE('$date_interview', '%m/%d/%Y'),
                    '$info_address', '$relation_informant', '$educational_attain', '$occupation', '$employer',
                    '$employer_address', '$source_income', '$monthly_income', '$nr_dependents', '$nr_children',
                    '$per_capita_income', '$hauz_lot_expense', '$food_expense', '$light_source', '$ligth_expense',
                    '$water_source', '$water_expense', '$transport_expense', '$other_expense', '$status', CONCAT('Update: ',NOW(),' [$userid]\\n'),
                    '$userid', NOW(), '$education_expense', '$househelp_expense',
                    '$fuel_source', '$fuel_expense', '$clothing_expense', '$med_expenditure', '$insurance_mortgage',
                    '$total_monthly_expense', '$address', '$house_type', '$final_diagnosis', '$duration_problem',
                    '$duration_treatment', '$treatment_plan', '$accessibility_problem', '$name_referral', '$source_referral',
                    '$info_agency', '$info_contact_no', '$remarks', '$social_worker', '$income', '$other_income', '$other_occupation','$monthly_income_remarks', '$monthly_expenses_remarks',
                    '$is_poc'";

//		$values = "'$mss_no', '$encounter_nr', '$informant_name', '$info_address',
//						'$relation_informant', '$educational_attain', '$source_income',
//						'$monthly_income', '$nr_dependents',
//						'$nr_children', $per_capita_income,
//						'$hauz_lot_expense',
//						'$food_expense', '$ligth_expense', '$water_expense',
//						'$transport_expense', '$other_expense',
//						'', CONCAT('Create: ',NOW(),' [$userid]\\n'),'$userid',NOW(),'$userid',NOW(),
//						'$address', '$house_type'";   # NOTE: 'LD'=laboratory

		$this->sql="INSERT INTO $this->coretable ($index) VALUES ($values)";

		
		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
		else return FALSE;
	}

    #Added Jarel 03-01-13
    function saveDependent(&$data){
        $this->useSSPFTable();
        global $db, $HTTP_SESSION_VARS;
        //tract($data);

        $index = "pid, encounter_nr, dependent_id, dependent_name, dependent_age, dependent_status,
                            relation_to_patient, dep_educ_attainment, dependent_occupation, dep_monthly_income";

        $this->sql="INSERT INTO $this->coretable ($index) VALUES $data";

        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret) return TRUE;
        else return FALSE;
   }

	function saveSocialPatientArray(&$data){
		global $db, $HTTP_SESSION_VARS;
		$this->useSCRecTable();

		extract($data);

		$userid = $HTTP_SESSION_VARS['sess_user_name'];

		$index = "pid, mss_no, status, history, modify_id, modify_time, create_id, create_time";
		$values = "'$pid', '$mss_no', '', CONCAT('Create: ',NOW(),' [$userid]\\n'),'$userid',NOW(),'$userid',NOW()";   # NOTE: 'LD'=laboratory

		$this->sql="INSERT INTO $this->coretable ($index) VALUES ($values)";

		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
		else return FALSE;
	}

	//Added by Cherry 07-20-10
	function getSocServPatientArray($encounter_nr){
		global $db;
		//$this->useSCRecTable();
		$this->useSCPatTable();

		$this->sql="SELECT * FROM $this->coretable
						WHERE status NOT IN ($this->dead_stat)
						 AND encounter_nr='$encounter_nr'";
		#echo $this->sql;
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			return $this->result->FetchRow();
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function updateSocServPatientArray($mss_no,$encounter_nr,&$data){
		global $db, $HTTP_SESSION_VARS;
		$this->useSCPatTable();

		extract($data);

		$userid = $HTTP_SESSION_VARS['sess_user_name'];

        #Edited by Jarel 03-06-13
		$this->sql = "UPDATE $this->coretable SET
					pid ='$pid',informant_name = '$informant_name', religion =  '$religion', contact_no = '$contact_no', companion = '$companion', date_interview = STR_TO_DATE('$date_interview', '%m/%d/%Y'),
                    info_address = '$info_address', relation_informant = '$relation_informant', educational_attain = '$educational_attain', occupation = '$occupation',
                    employer = '$employer', employer_address = '$employer_address', source_income = '$source_income', monthly_income = '$monthly_income',
                    nr_dependents = '$nr_dependents', nr_children = '$nr_children', per_capita_income = '$per_capita_income', hauz_lot_expense = '$hauz_lot_expense',
                    food_expense = '$food_expense', light_source = '$light_source', ligth_expense = '$ligth_expense', water_source = '$water_source',
                    water_expense = '$water_expense', transport_expense = '$transport_expense', other_expense = '$other_expense',  status = '$status',
                    history = CONCAT(history,'Update: ',NOW(),' [$userid]\\n'), modify_id = '$userid', modify_time = NOW(), education_expense = '$education_expense',
                    househelp_expense = '$househelp_expense', fuel_source = '$fuel_source', fuel_expense = '$fuel_expense', clothing_expense = '$clothing_expense', med_expenditure = '$med_expenditure',
                    insurance_mortgage = '$insurance_mortgage', total_monthly_expense = '$total_monthly_expense', address = '$address', house_type = '$house_type',
                    final_diagnosis = '$final_diagnosis', duration_problem = '$duration_problem', duration_treatment = '$duration_treatment', treatment_plan = '$treatment_plan',
                    accessibility_problem = '$accessibility_problem', name_referral = '$name_referral', source_referral = '$source_referral', info_agency = '$info_agency',
                    info_contact_no = '$info_contact_no', remarks = '$remarks', social_worker = '$social_worker', income = '$income',  other_income = '$other_income', other_occupation = '$other_occupation', monthly_income_remarks = '$monthly_income_remarks', monthly_expenses_remarks = '$monthly_expenses_remarks',
                    is_poc = '$is_poc'
                    WHERE mss_no='$mss_no'
                    AND encounter_nr='$encounter_nr'";

		if ($db->Execute($this->sql)) {
			if ($db->Affected_Rows()) {
				$ret=TRUE;
			}
		}
		if ($ret)	return TRUE;
		else return FALSE;
	}

	function getSocServPatient($pid){
		global $db;
		$this->useSCRecTable();

		$this->sql="SELECT * FROM $this->coretable
						WHERE status NOT IN ($this->dead_stat)
						 AND pid='$pid'";

		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
			return $this->result->FetchRow();
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function getLastMSSnr($today, $mss_init) {
		global $db;
		$this->useSCRecTable();
		$today = $db->qstr($today);
		$this->sql="SELECT IFNULL(MAX(CAST(mss_no AS UNSIGNED)+1),
								CONCAT(EXTRACT(YEAR FROM NOW()),$mss_init))
						FROM $this->coretable WHERE SUBSTRING(mss_no,1,4)=EXTRACT(YEAR FROM NOW())";

		return $db->GetOne($this->sql);
	}
	#--------------------------

	#added by VAN 05-13-08
	function getPatientSocialClass($pid, $opd=1){
		global $db;

		if ($opd){
			$this->sql ="SELECT m.mss_no AS MSS,d.parentid, d.discountdesc, sg.*, sm.*
						 FROM seg_charity_grants_pid AS sg
						 INNER JOIN seg_social_patient AS m ON sg.pid=m.pid
						 LEFT JOIN seg_socserv_patient AS sm ON sm.mss_no=m.mss_no
						 INNER JOIN seg_discount AS d ON d.discountid=sg.discountid
						 WHERE m.pid='$pid'
						 /*AND DATE(grant_dte)=DATE(NOW())*/
												 AND DATE_ADD(DATE(grant_dte), INTERVAL 7 DAY)>=DATE(NOW())
						 ORDER BY grant_dte DESC LIMIT 1";
		}else{
			$this->sql ="SELECT m.mss_no AS MSS,d.parentid, d.discountdesc, sg.*, sm.*
						FROM seg_charity_grants AS sg
						INNER JOIN care_encounter AS e ON e.encounter_nr=sg.encounter_nr
						INNER JOIN seg_social_patient AS m ON m.pid=e.pid
						LEFT JOIN seg_socserv_patient AS sm ON sm.mss_no=m.mss_no
						INNER JOIN seg_discount AS d ON d.discountid=sg.discountid
						WHERE sg.encounter_nr='".$pid."'
						ORDER BY grant_dte DESC LIMIT 1";
		}

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	function getPatientMSS($pid){
		global $db;

		$this->sql ="SELECT *
						 FROM seg_social_patient
						 WHERE pid='$pid'";

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	function getModifiers($modifier){
		global $db;

		$this->sql ="SELECT s.*, m.*
						 FROM seg_social_service_submodifiers AS s
						 INNER JOIN seg_social_service_modifiers AS m ON m.mod_code=s.mod_code
						 WHERE s.mod_code='$modifier' AND is_deleted = 0";

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount())
				return $this->result;
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	function getPatientModifier($modifier, $submodifier){
		global $db;

		$this->sql ="SELECT s.*, m.*
						 FROM seg_social_service_submodifiers AS s
						 INNER JOIN seg_social_service_modifiers AS m ON m.mod_code=s.mod_code
						 WHERE s.mod_code='$modifier' AND s.mod_subcode LIKE '$submodifier'";

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	function getLatestClassification($encounter_nr){
		global $db;

		$this->sql ="SELECT * FROM seg_charity_grants
						 WHERE encounter_nr='$encounter_nr'
						 AND status='valid'
						 ORDER BY grant_dte DESC LIMIT 1";

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

		function getLatestClassificationByPid($pid, $opd=1){
				global $db;

				if ($opd){
					$this->sql ="SELECT * FROM seg_charity_grants_pid
												 WHERE pid='$pid'
												 /*AND DATE_ADD(date(grant_dte), INTERVAL 7 DAY)>=DATE(now())*/
												 AND status='valid'
												 ORDER BY grant_dte DESC LIMIT 1";
				}else{
					$this->sql ="SELECT s.*
												FROM seg_charity_grants AS s
												INNER JOIN care_encounter AS e ON e.encounter_nr=s.encounter_nr
												WHERE s.encounter_nr='".$pid."'
												AND is_discharged=0
												AND s.status='valid'
												ORDER BY grant_dte DESC LIMIT 1";
				}

				if ($this->result=$db->Execute($this->sql)){
						if ($this->count = $this->result->RecordCount())
								return $this->result->FetchRow();
						else
								return FALSE;
				}else{
						return FALSE;
				}
		}

	function getSSClassInfo($discountID){
		global $db;

		$this->sql ="SELECT d.discountid, d.discountdesc, d.discount, d.parentid
						 FROM seg_discount AS d
									 WHERE d.discountid='$discountID'";

		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount())
				return $this->result->FetchRow();
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}
	#--------------------

	#added by VAN 07-04-08
	function getSSChildArray($discountID){
		global $db;

		#edited by VAN 07-26-08
        if(empty($discountID)){
            $this->sql = "SELECT d.discountid, d.discountdesc, d.discount, d.parentid
                                FROM seg_discount AS d
                                WHERE d.parentid NOT IN ('')
                                AND is_visible=1
                                ORDER BY discountdesc";
        }else{
            $this->sql ="SELECT d.discountid, d.discountdesc, d.discount, d.parentid
                             FROM seg_discount AS d
                             WHERE d.parentid='$discountID'
                         ORDER BY discountdesc ASC";
        }


		if ($this->result=$db->Execute($this->sql)){
			if ($this->count = $this->result->RecordCount())
				return $this->result;
			else
				return FALSE;
		}else{
			return FALSE;
		}
	}

	#Added by Cherry 08-08-10
	function getSocialServPatientEncounter($encounter_nr, $pid){
		global $db;

		$this->sql = "SELECT * FROM seg_socserv_patient
									WHERE status NOT IN ($this->dead_stat)
									AND encounter_nr='".$encounter_nr."' AND pid='".$pid."'";
		if ($this->result=$db->Execute($this->sql)) {
				if ($this->count=$this->result->RecordCount()){
				return $this->result->FetchRow();
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	#End Cherry

	function getSocialServPatient($mss_no, $with_enc=0, $encounter_nr=''){
		global $db;

		$sql_cond = "";
		if ($with_enc)
			$sql_cond = " AND encounter_nr='".$encounter_nr."'";

		$this->sql="SELECT * FROM seg_socserv_patient
						WHERE status NOT IN ($this->dead_stat)
						 AND mss_no='$mss_no' $sql_cond";

			if ($this->result=$db->Execute($this->sql)) {
				if ($this->count=$this->result->RecordCount()){
				return $this->result->FetchRow();
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	#Added by Cherry 07-23-10
	function getSocServPatientThroughEncounter($encounter_nr){
		global $db;

		$this->sql="SELECT * FROM seg_socserv_patient
								WHERE status NOT IN ($this->dead_stat)
								AND encounter_nr='".$encounter_nr."'
								";
		if ($this->result=$db->Execute($this->sql)) {
				if ($this->count=$this->result->RecordCount()){
				return $this->result->FetchRow();
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}

	}

	#Added by Cherry 07-14-10
	function deleteSocialServPatient($encounter_nr){
		global $db;

		$this->sql="DELETE FROM seg_socserv_patient
								WHERE encounter_nr = '$encounter_nr'";
			/*if($db->Execute($this->sql)){
			return true;
		}else{ return FALSE;} */
			if ($db->Execute($this->sql)) {
						#return $this->saveOpsServDetailsInfoFromArray($data);
						return TRUE;
				}else{ return FALSE; }
	}

	#--------------------

	#added by VAN 07-05-08
	function getAllModifiers(){
		global $db;

		$this->sql ="SELECT * FROM seg_social_service_modifiers
								 ORDER BY mod_code";
		#echo $this->sql;
		if ($this->result=$db->Execute($this->sql)) {
				if ($this->count=$this->result->RecordCount()){
				return $this->result;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function addModifierData(&$data){
		$this->useSMtable();
		$this->data_array = $data;
		return $this->insertDataFromInternalArray();
	}

	function getLastSubMod($modifier){
		global $db;

		$this->sql="SELECT SUBSTRING(sm.mod_subcode,POSITION('.' IN sm.mod_subcode)+1) AS subcode,
					sm.*
					FROM seg_social_service_submodifiers AS sm
					WHERE mod_code='$modifier'
					ORDER BY SUBSTRING(sm.mod_subcode,POSITION('.' IN sm.mod_subcode)+1) DESC
					LIMIT 1";

			if ($this->result=$db->Execute($this->sql)) {
				if ($this->count=$this->result->RecordCount()){
				return $this->result->FetchRow();
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function deleteModifierData($code, $mod){
		global $db;
		$this->sql = "DELETE FROM seg_social_service_submodifiers
						WHERE mod_code = '".$mod."'
						AND mod_subcode LIKE '".$code."'";

		if($db->Execute($this->sql)){
			return true;
		}else{ return FALSE;}
	}


	function modifierIsused($code){
		global $db;

		$this->sql="SELECT discountid FROM seg_charity_grants AS ge
					WHERE (personal_circumstance LIKE '".$code."'
							OR community_situation LIKE '".$code."'
								OR nature_of_disease LIKE '".$code."')
					UNION ALL
					SELECT discountid FROM seg_charity_grants_pid AS ge
					WHERE (personal_circumstance LIKE '".$code."'
							 OR community_situation LIKE '".$code."'
								OR nature_of_disease LIKE '".$code."')";

			if ($this->result=$db->Execute($this->sql)) {
			$this->count=$this->result->RecordCount();
				if ($this->count){
				return true;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	#-----------------------

	#added by VAN 08-14-08
	function getSavedBilling($encounter_nr){
		global $db;

		$this->sql ="SELECT be.*, bc.*, bd.*
							FROM seg_billing_encounter AS be
							LEFT JOIN seg_billing_coverage AS bc ON bc.bill_nr=be.bill_nr
							LEFT JOIN seg_billing_discount AS bd ON bd.bill_nr=be.bill_nr
							WHERE be.encounter_nr='".$encounter_nr."'
							GROUP BY be.bill_nr
							ORDER BY bill_dte DESC";
		#echo $this->sql;
		if ($this->result=$db->Execute($this->sql)) {
				if ($this->count=$this->result->RecordCount()){
				return $this->result;
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

		function getDiscountAllowWalkin(){
				global $db;

				$this->sql ="SELECT discountid FROM  seg_discount WHERE allow_walkin=1";
				#echo $this->sql;
				if ($this->result=$db->Execute($this->sql)) {
						if ($this->count=$this->result->RecordCount()){
								return $this->result;
						}else{
								return FALSE;
						}
				}else{
					return FALSE;
				}
		}
	#-----------------------

	#-----added by cha 10-27-09
	function getNewControlNr($contrlnr)
	{
		 global $db;

				$temp_cntrl_nr = date('Y')."%";   # NOTE : XXXX?????? would be the format of Reference number
				$row=array();
				$this->sql="SELECT control_nr FROM seg_social_lingap WHERE control_nr LIKE '$temp_cntrl_nr' ORDER BY control_nr DESC";
				#echo "this ".$this->sql;
				if($this->res['gnpn']=$db->SelectLimit($this->sql,1)){
						if($this->res['gnpn']->RecordCount()){
								$row=$this->res['gnpn']->FetchRow();
								return $row['control_nr']+1;
						}else{/*echo $this->sql.'no xount';*/return $contrlnr=date('Y')."000001";}
				}else{/*echo $this->sql.'no sql';*/return $contrlnr=date('Y')."000001";}
	}
	#-----end cha

	#added by VAN 07-19-2010
	function getProfileInfo($mss_no='',$offset=0, $rows=6, $sort_order="create_time DESC"){
		global $db;

		$this->sql = "SELECT DISTINCT SQL_CALC_FOUND_ROWS e.encounter_type, ss.*, sp.pid,
                                    e.current_dept_nr, fn_get_department_name(e.current_dept_nr) AS deptname,
                                    e.admission_dt, e.encounter_date,
                                    IF(e.encounter_date IS NULL, 'None', IF(e.encounter_date IN (3,4),e.admission_dt,e.encounter_date)) AS case_date
									FROM seg_socserv_patient AS ss
									INNER JOIN seg_social_patient AS sp ON sp.mss_no=ss.mss_no
									LEFT JOIN care_encounter AS e ON e.encounter_nr=ss.encounter_nr AND e.pid=sp.pid
									WHERE ss.mss_no='$mss_no'
									ORDER BY ss.create_time DESC";

		if( $result= $db->Execute($this->sql)){
			if($result->RecordCount()){
				return $result;
			}else{ return false;}
		}else{ return false; }
	}

	function getDiscountByPid($pid=''){
		global $db;

		$this->sql = "SELECT pid,
											SUBSTRING(MAX(CONCAT(grant_dte,grant_dte)),20) AS grant_dte,
											SUBSTRING(MAX(CONCAT(grant_dte,sw_nr)),20) AS sw_nr,
											SUBSTRING(MAX(CONCAT(grant_dte,discountid)),20) AS discountid,
											SUBSTRING(MAX(CONCAT(grant_dte,discount)),20) AS discount,
											SUBSTRING(MAX(CONCAT(grant_dte,notes)),20) AS notes,
											SUBSTRING(MAX(CONCAT(grant_dte,personal_circumstance)),20) AS personal_circumstance,
											SUBSTRING(MAX(CONCAT(grant_dte,community_situation)),20) AS community_situation,
											SUBSTRING(MAX(CONCAT(grant_dte,nature_of_disease)),20) AS nature_of_disease,
											SUBSTRING(MAX(CONCAT(grant_dte,reason)),20) AS reason,
											SUBSTRING(MAX(CONCAT(grant_dte,other_name)),20) AS other_name,
											SUBSTRING(MAX(CONCAT(grant_dte,id_number)),20) AS id_number,
                                            fn_get_personell_firstname_last(SUBSTRING(MAX(CONCAT(grant_dte,sw_nr)),20)) AS encoder
										FROM seg_charity_grants_pid
										WHERE pid='$pid' AND status NOT IN ('deleted','cancelled','expired')";

		if( $result= $db->Execute($this->sql)){
			if($result->RecordCount()){
				return $result->FetchRow();
			}else{ return false;}
		}else{ return false; }
	}

	function getDiscountByEncounter($encounter_nr=''){
		global $db;

		$this->sql = "SELECT encounter_nr,
										SUBSTRING(MAX(CONCAT(grant_dte,grant_dte)),20) AS grant_dte,
										SUBSTRING(MAX(CONCAT(grant_dte,sw_nr)),20) AS sw_nr,
										SUBSTRING(MAX(CONCAT(grant_dte,discountid)),20) AS discountid,
										SUBSTRING(MAX(CONCAT(grant_dte,discount)),20) AS discount,
										SUBSTRING(MAX(CONCAT(grant_dte,notes)),20) AS notes,
										SUBSTRING(MAX(CONCAT(grant_dte,personal_circumstance)),20) AS personal_circumstance,
										SUBSTRING(MAX(CONCAT(grant_dte,community_situation)),20) AS community_situation,
										SUBSTRING(MAX(CONCAT(grant_dte,nature_of_disease)),20) AS nature_of_disease,
										SUBSTRING(MAX(CONCAT(grant_dte,reason)),20) AS reason,
										SUBSTRING(MAX(CONCAT(grant_dte,other_name)),20) AS other_name,
										SUBSTRING(MAX(CONCAT(grant_dte,id_number)),20) AS id_number,
                                        fn_get_personell_firstname_last(SUBSTRING(MAX(CONCAT(grant_dte,sw_nr)),20)) AS encoder
									FROM seg_charity_grants
									WHERE encounter_nr='$encounter_nr' AND status NOT IN ('deleted','cancelled','expired')";

		if( $result= $db->Execute($this->sql)){
			if($result->RecordCount()){
				return $result->FetchRow();
			}else{ return false;}
		}else{ return false; }
	}

	function getExpiryLength($id='', $source=''){
		global $db;

		$this->sql = "SELECT * FROM seg_default_value WHERE id='$id' AND source='$source' LIMIT 1";

		if( $result= $db->Execute($this->sql)){
			if($result->RecordCount()){
				return $result->FetchRow();
			}else{ return false;}
		}else{ return false; }
	}

	#added by VAN 03-17-2011
	function getExpiryLengthByName($name='', $source=''){
		global $db;

		$this->sql = "SELECT * FROM seg_default_value WHERE name='$name' AND source='$source' LIMIT 1";

		if( $result= $db->Execute($this->sql)){
			if($result->RecordCount()){
				return $result->FetchRow();
			}else{ return false;}
		}else{ return false; }
	}

	#-------------

	function getLCRInforequest($pid, $encounter_nr, $offset=0, $rows=10, $sort_order="date_request DESC"){
		global $db;
		$enc_set_sql = " pid='".$pid."' AND encounter_nr = '".$encounter_nr."'";
		$this->sql=	"SELECT SQL_CALC_FOUND_ROWS lab.refno, sum(lab.price_cash*lab.quantity) as total_charge, lab.serv_dt as date_request, serv_tm AS time_request, lab.dept FROM ".
					"\n	(SELECT ls.refno,lsd.service_code,sls.name, ls.ref_source as dept ,lsd.price_cash , lsd.price_charge,lsd.quantity,ls.serv_dt, ls.serv_tm ".
					"\n		FROM (seg_lab_serv as ls ".
					"\n		INNER JOIN seg_lab_servdetails as lsd ON ls.refno = lsd.refno  AND request_flag IS NULL) ".
					"\n		INNER JOIN seg_lab_services as sls ON lsd.service_code = sls.service_code ".
					/*"\n	WHERE ls.encounter_nr = '".$encounter_nr."'  */
										"\n    WHERE ".$enc_set_sql."
												AND ls.is_cash<>'0'
												AND DATEDIFF(DATE(now()),date(ls.serv_dt))< 7
								AND ls.status NOT IN ($this->dead_stat) AND lsd.status NOT IN ($this->dead_stat) ".
									"/*AND (ls.refno NOT IN(SELECT ls.refno
												FROM seg_lab_serv as ls
												INNER JOIN seg_lab_servdetails as lsd ON ls.refno = lsd.refno
											 WHERE ".$enc_set_sql."
												AND ls.serv_dt<>date(NOW()) AND lsd.price_cash<>lsd.price_cash_orig))*/) as lab ".
					"\n GROUP BY lab.refno ".
					"\n UNION ALL ".
					"\n SELECT rad.refno, sum(rad.price_cash) as total_charge, rad.request_date as date_request, request_time AS time_request, rad.dept FROM ".
					"\n  (SELECT srs.refno, ctr.service_code , srv.name, 'RD' as dept ,ctr.price_cash, ctr.price_charge , srs.request_date, srs.request_time ".
					"\n		FROM (seg_radio_serv  as srs ".
					"\n 	INNER JOIN care_test_request_radio as ctr on ctr.refno = srs.refno  AND request_flag IS NULL) ".
					"\n	INNER JOIN seg_radio_services as srv on ctr.service_code = srv.service_code	".
					/*"\n WHERE  srs.encounter_nr = '".$encounter_nr."'  */
										"\n WHERE  ".$enc_set_sql."
														AND srs.is_cash <>'0'
														AND DATEDIFF(DATE(now()),date(srs.request_date))< 7
								AND ctr.status NOT IN ($this->dead_stat)  ".
								"/*AND (srs.refno NOT IN(SELECT srs.refno
										FROM seg_radio_serv as srs
										INNER JOIN care_test_request_radio as ctr ON srs.refno = ctr.refno
										WHERE ".$enc_set_sql."
										AND srs.request_date<>date(NOW()) AND ctr.price_cash<>ctr.price_cash_orig))*/) as rad ".
					"\n GROUP BY rad.refno".
					"\n UNION ALL".
					"\n	SELECT  ph.refno, sum(ph.itemcharge) as total_charge, date(ph.orderdate) as date_request, time(ph.orderdate) AS time_request, ph.dept FROM ".
					"\n	(SELECT spo.refno, spi.bestellnum, cpp.artikelname, spo.department as dept, spi.quantity as qty, spo.is_cash, ".
					"\n		spi.pricecash, spi.pricecharge, spo.orderdate, sum(quantity * pricecash) as itemcharge ".
					"\n		FROM (seg_pharma_orders as spo ".
					"\n			INNER JOIN seg_pharma_order_items as spi ON spo.refno = spi.refno  AND request_flag IS NULL) ".
					"\n		INNER JOIN care_pharma_products_main as cpp on spi.bestellnum = cpp.bestellnum ".
					"\n		WHERE ".$enc_set_sql."   AND spo.is_cash<>'0'
									AND DATEDIFF(DATE(now()),date(spo.orderdate))< 7
									/*AND pharma_area!='MG'*/ ".
									"/*AND (spo.refno NOT IN(SELECT spo.refno
										FROM seg_pharma_orders as spo
										INNER JOIN seg_pharma_order_items as spi ON spo.refno = spi.refno
									 WHERE ".$enc_set_sql."
										AND date(spo.orderdate)<>date(NOW()) AND spi.pricecash<>spi.price_orig))*/ group by spo.refno) as ph ".
					"\n	GROUP BY ph.refno
							ORDER BY $sort_order LIMIT $offset, $rows";

		if( $result= $db->Execute($this->sql)){
			if($result->RecordCount()){
				return $result;
			}else{ return false;}
		}else{ return false; }

	} // end of function getLCRInfo

    //added by jasper 05/10/2013
    function saveBillDiscountAmount($bill_nr, $discountid, $discount, $discountamt){
        global $db;

        $fldarray = array('bill_nr' => $db->qstr($bill_nr),
                        'discountid' => $db->qstr($discountid),
                        'discount' => $db->qstr($discount),
                        'discount_amnt' => $db->qstr($discountamt)
                       );
       $bsuccess = $db->Replace('seg_billing_discount', $fldarray, array('bill_nr'));

        /*$hasSaveBill = $this->checkHasSaveBill($bill_nr);

        if ($hasSaveBill){
                $this->sql = "UPDATE seg_billing_discount
                                                    SET discountid='$discountid',
                                                        discount_amnt='$discountamt'
                                                WHERE bill_nr='$bill_nr'";
        }else{
                $sql = "INSERT INTO seg_billing_discount(bill_nr,discountid,discount_amnt)
                                                VALUES('$bill_nr','$discountid','$discountamt')";
        }*/

        if($bsuccess){
            return true;
        }else{ return FALSE;}
    }
    //added by jasper 05/10/2013

	#added by VAN 12-08-09
	function applyBillDiscount($encounter_nr,$discount_amount){
		global $db;
		$this->sql = "UPDATE seg_charity_grants
										SET discount_amnt = '".$discount_amount."'
									WHERE encounter_nr='".$encounter_nr."' ORDER BY grant_dte DESC LIMIT 1";

		if($db->Execute($this->sql)){
			return true;
		}else{ return FALSE;}
	}

	function checkHasSaveBill($bill_nr){
			global $db;

			$this->sql = "SELECT * FROM seg_billing_discount WHERE bill_nr='$bill_nr' LIMIT 1";

			if ($this->result=$db->Execute($this->sql)){
				if ($this->count = $this->result->RecordCount()){
					$row = $this->result->FetchRow();
					return  TRUE;
				}else
					return FALSE;
			}else{
				return FALSE;
			}
	}

	#added by VAN 12-08-09
	function saveBillDiscount($bill_nr, $discountid, $discount){
		global $db;

		$hasSaveBill = $this->checkHasSaveBill($bill_nr);

		if ($hasSaveBill){
				$this->sql = "UPDATE seg_billing_discount
													SET	discountid='$discountid',
														discount='$discount'
												WHERE bill_nr='$bill_nr'";
		}else{
				$this->sql = "INSERT INTO seg_billing_discount(bill_nr,discountid,discount)
												VALUES('$bill_nr','$discountid','$discount')";
		}

		if($db->Execute($this->sql)){
			return true;
		}else{ return FALSE;}
	}

    #added by VAS 09-04-2012
    function getPatientCaseType_pid($pid){
        global $db;

        $this->sql = "SELECT p.pid, p.discountid
                        FROM seg_charity_grants_pid p
                        WHERE pid='$pid'
                        GROUP BY discountid";

        if ($this->result=$db->Execute($this->sql)){
            if ($this->count = $this->result->RecordCount()){
                $this->rowRec = $this->result->FetchRow();
                #return  $this->count;
                #return $this->rowRec['patient_type'];
                if ($this->count > 1){
                   $patient_type = 'Re-classified';
                }else{
                   $patient_type = 'Classified';
                }

                return $patient_type;
            }else
                return FALSE;
        }else{
            return FALSE;
        }
    }

    function getPatientCaseType_enc($encounter_nr){
        global $db;

        $this->sql = "SELECT p.encounter_nr, p.discountid
                        FROM seg_charity_grants p
                        WHERE encounter_nr='$encounter_nr'
                        GROUP BY discountid";

        if ($this->result=$db->Execute($this->sql)){
            if ($this->count = $this->result->RecordCount()){
                $this->rowRec = $this->result->FetchRow();
                #return  $this->count;
                #return $this->rowRec['patient_type'];
                if ($this->count > 1){
                   $patient_type = 'Re-classified';
                }else{
                   $patient_type = 'Classified';
                }

                return $patient_type;
            }else
                return FALSE;
        }else{
            return FALSE;
        }
    }
    #----------------------------

    function getCaseHistory($pid='', $offset=0, $rows=10, $sort_order="case_date"){
        global $db;

        $this->sql =" SELECT SQL_CALC_FOUND_ROWS sp.mss_no, e.pid, e.encounter_nr, e.encounter_type,
                        IF(e.encounter_date IS NULL, 'None', IF(e.encounter_date IN (3,4),e.admission_dt,e.encounter_date)) AS case_date,
                        e.current_dept_nr, fn_get_department_name(e.current_dept_nr) AS deptname,
                        IF (e.encounter_type IN (1,3,4) && e.is_discharged=1,'Yes', IF(e.encounter_type=2 && (DATEDIFF(DATE(NOW()),DATE(encounter_date))>=1),'Yes','No')) AS discharged
                        FROM care_encounter e
                        LEFT JOIN seg_social_patient AS sp ON sp.pid=e.pid
                        WHERE e.pid='$pid'
                        GROUP BY e.encounter_nr
                        ORDER BY $sort_order LIMIT $offset,$rows";

        if($this->egg['ss'] = $db->Execute($this->sql)){
            $this->count = $this->egg['ss']->RecordCount();
            if($this->egg['ss']->RecordCount()!=1){
                return $this->egg['ss'];
            }else{
                return $this->egg['ss'];

            }
        }else{ return false; }

    }//getCaseHistory

    #Added by Jarel 03-11-13
    function getDependent($pid,$enc){
        global $db;
        if($pid!='' && ($enc!='' || $enc!='0')){
            $this->sql = "Select * from seg_social_patient_family WHERE encounter_nr = '$enc'";
        }elseif($enc!='' || $enc!='0'){
            $this->sql = "Select * from seg_social_patient_family WHERE encounter_nr = '$enc'";
        }elseif( $enc=='0'){
        	$this->sql = "Select * from seg_social_patient_family WHERE encounter_nr = '$enc' AND pid='$pid'";
        }else{
            $this->sql = "SELECT * FROM seg_social_patient_family WHERE encounter_nr =
                         (SELECT ss.encounter_nr FROM seg_social_patient_family AS ss INNER JOIN care_encounter AS e ON
                         ss.encounter_nr = e.encounter_nr WHERE ss.pid = $pid ORDER BY e.encounter_date DESC LIMIT 1);";
        }

        if( $result= $db->Execute($this->sql)){
            if($result->RecordCount()){
                return $result;
            }else{ return false;}
        }else{ return false; }
    }

    #Added by Jarel 03-18-2013
    function getAssessDetails($id){
        global $db;

        $this->sql = "SELECT * FROM seg_social_service_assess_details WHERE assess_id = $id";

        if( $result= $db->Execute($this->sql)){
            if($result->RecordCount()){
                return $result;
            }else{ return false;}
        }else{ return false; }
    }

    #Added by Jarel 03-18-2013
    function getAssessHeader(){
        global $db;

        $this->sql = "Select * from seg_social_service_assess ORDER BY id";

        if( $result= $db->Execute($this->sql)){
            if($result->RecordCount()){
                return $result;
            }else{ return false;}
        }else{ return false; }

    }

    #Added by Jarel 03-19-2013
    function getPatientProblem(){
        global $db;

        $this->sql = "Select * from seg_social_service_problem_patient ORDER BY id";

        if( $result= $db->Execute($this->sql)){
            if($result->RecordCount()){
                return $result;
            }else{ return false;}
        }else{ return false; }

    }

    #Added by Jarel 03-19-2013
    function getTopicConcern(){
        global $db;

        $this->sql = "Select * from seg_social_service_topics ORDER BY id";

        if( $result= $db->Execute($this->sql)){
            if($result->RecordCount()){
                return $result;
            }else{ return false;}
        }else{ return false; }

    }

    #Added by Jarel 03-20-2013
    function saveSocialFunctioning(&$values){
        global $db, $HTTP_SESSION_VARS;

        $index = "pid, encounter_nr, social_fxn_id, interaction_id, severity_id, duration_id, coping_id, others";

        $this->sql="INSERT INTO seg_social_functioning_patient ($index) VALUES $values";

        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret) return TRUE;
        else return FALSE;
    }

    #Added by Jarel 03-21-2013
    function saveNoSocialProblem($pid,$enc,$no_social){
        global $db, $HTTP_SESSION_VARS;

        $result = $db->Replace('seg_social_no_problem',
                                    array(
                                             'pid'=>$db->qstr($pid),
                                             'encounter_nr'=>$db->qstr($enc),
                                             'no_social_problem'=>$db->qstr($no_social)
                                         ),
                                        array('pid','encounter_nr'),
                                        $autoquote=FALSE
                                   );

         if ($result)
            return TRUE;
         else
            return FALSE;
    }

    #Added by Jarel 03-21-2013
    function saveSocialProblems(&$values){
        global $db, $HTTP_SESSION_VARS;

        $index = "pid, encounter_nr, problems_fxn_id, severity_id, duration_id, others";

        $this->sql="INSERT INTO seg_social_problems_patient ($index) VALUES $values";

        if ($db->Execute($this->sql)) {
            if ($db->Affected_Rows()) {
                $ret=TRUE;
            }
        }
        if ($ret) return TRUE;
        else return FALSE;
    }

    #Added by Jarel 03-21-2013
    function saveSocialFindings(&$data){
        global $db, $HTTP_SESSION_VARS;

        extract($data);

        $result = $db->Replace('seg_social_findings',
                                            array(
                                                     'pid'=>$db->qstr($pid),
                                                     'encounter_nr'=>$db->qstr($encounter_nr),
                                                     'problem_presented'=>$db->qstr($problem_presented),
                                                     'other_problem' =>$db->qstr($other_problem),
                                                     'counseling_done' =>$db->qstr($counseling_done),
                                                     'topic_concern'=>$db->qstr($topic_concern),
                                                     'no_reason'=>$db->qstr($no_reason),
                                                     'social_diagnosis'=>$db->qstr($social_diagnosis),
                                                     'intervention'=>$db->qstr($intervention),
                                                     'action_taken'=>$db->qstr($action_taken),
                                                     'remarks'=>$db->qstr($remarks)
                                                ),
                                                array('pid','encounter_nr'),
                                                $autoquote=FALSE
                                           );

         if ($result)
            return TRUE;
         else
            return FALSE;
    }

    #Added By Jarel 03/25/2013
    function getSocialFunctioning($pid,$enc,$id){
        global $db;

        if($pid!='' && $enc!=''){
            $this->sql = "Select * from seg_social_functioning_patient WHERE encounter_nr = $enc AND social_fxn_id = $id";
        }elseif($enc!=''){
            $this->sql = "Select * from seg_social_functioning_patient WHERE encounter_nr = $enc AND social_fxn_id = $id";
        }else{
            $this->sql = "Select * from seg_social_functioning_patient WHERE pid = $pid AND social_fxn_id = $id";
        }

        if( $result= $db->Execute($this->sql)){
            if($result->RecordCount()){
                return $result;
            }else{ return false;}
        }else{ return false; }
    }

    #Added By Jarel 03/25/2013
    function getNoSocialProblem($pid,$enc){
        global $db;

        if($pid!='' && $enc!=''){
            $this->sql = "Select * from seg_social_no_problem WHERE encounter_nr = $enc";
        }elseif($enc!=''){
            $this->sql = "Select * from seg_social_no_problem WHERE encounter_nr = $enc";
        }else{
            $this->sql = "Select * from seg_social_no_problem WHERE pid = $pid";
        }

        if( $result= $db->Execute($this->sql)){
            if($result->RecordCount()){
                return $result;
            }else{ return false;}
        }else{ return false; }
    }

    #Added By Jarel 03/25/2013
    function getSocialProblems($pid,$enc,$id){
        global $db;

        if($pid!='' && $enc!=''){
            $this->sql = "Select * from seg_social_problems_patient WHERE encounter_nr = $enc AND problems_fxn_id = $id";
        }elseif($enc!=''){
            $this->sql = "Select * from seg_social_problems_patient WHERE encounter_nr = $enc AND problems_fxn_id = $id";
        }else{
            $this->sql = "Select * from seg_social_problems_patient WHERE pid = $pid AND problems_fxn_id = $id";
        }

        if( $result= $db->Execute($this->sql)){
            if($result->RecordCount()){
                return $result;
            }else{ return false;}
        }else{ return false; }
    }

    #Added By Jarel 03/25/2013
    function getSocialFindings($pid,$enc){
        global $db;

        if($pid!='' && $enc!=''){
            $this->sql = "Select * from seg_social_findings WHERE encounter_nr = $enc";
        }elseif($enc!=''){
            $this->sql = "Select * from seg_social_findings WHERE encounter_nr = $enc";
        }else{
            $this->sql = "Select * from seg_social_findings WHERE pid = $pid";
        }

        if( $result= $db->Execute($this->sql)){
            if($result->RecordCount()){
                return $result;
            }else{ return false;}
        }else{ return false; }
    }

    #Added By Jarel 03/25/2013
    function hasSocialProblem($pid,$enc){
        global $db;

        if($pid!='' && $enc!=''){
            $this->sql = "Select * from seg_social_problems_patient WHERE encounter_nr = $enc";
        }elseif($enc!=''){
            $this->sql = "Select * from seg_social_problems_patient WHERE encounter_nr = $enc";
        }else{
            $this->sql = "Select * from seg_social_problems_patient WHERE pid = $pid";
        }

         if ($this->result=$db->Execute($this->sql)) {
                    if ($this->count=$this->result->RecordCount()) {
                            return True;
                    }
                    else{return FALSE;}
         }else{return FALSE;}
    }

    #Added By Jarel 03/25/2013
    function hasSocialFunctioning($pid,$enc){
        global $db;
        if($pid!='' && $enc!=''){
            $this->sql = "Select * from seg_social_functioning_patient WHERE encounter_nr = $enc";
        }elseif($enc!=''){
            $this->sql = "Select * from seg_social_functioning_patient WHERE encounter_nr = $enc";
        }else{
            $this->sql = "Select * from seg_social_functioning_patient WHERE pid = $pid";
        }

         if ($this->result=$db->Execute($this->sql)) {
                    if ($this->count=$this->result->RecordCount()) {
                            return True;
                    }
                    else{return FALSE;}
         }else{return FALSE;}
    }

    #Added by Jarel 03-27-2013
    function saveSocialCase(&$data){
        global $db, $HTTP_SESSION_VARS;

        extract($data);

        $result = $db->Replace('seg_social_case_management',
                                            array(
                                                     'pid'=>$db->qstr($pid),
                                                     'encounter_nr'=>$db->qstr($encounter_nr),
                                                     'planning'=>$db->qstr($planning),
                                                     'provision' =>$db->qstr($provision),
                                                     'outgoing' =>$db->qstr($outgoing),
                                                     'incoming'=>$db->qstr($incoming),
                                                     'leading_reasons'=>$db->qstr($leading_reasons),
                                                     'social_work'=>$db->qstr($social_work),
                                                     'discharge_services'=>$db->qstr($discharge_services),
                                                     'case_con'=>$db->qstr($case_con),
                                                     'follow_up'=>$db->qstr($follow_up),
                                                     'coordination'=>$db->qstr($coordination),
                                                     'documentation'=>$db->qstr($documentation),
                                                     'others_coordination'=>$db->qstr($others_coordination),
                                                     'others_documentation'=>$db->qstr($others_documentation),
                                                     'remarks'=>$db->qstr($remarks)
                                                ),
                                                array('pid','encounter_nr'),
                                                $autoquote=FALSE
                                           );

         if ($result)
            return TRUE;
         else
            return FALSE;
    }

    #Added By Jarel 03/27/2013
    function getSocialCase($pid,$enc){
        global $db;

        if($pid!='' && $enc!=''){
            $this->sql = "Select * from seg_social_case_management WHERE encounter_nr = $enc";
        }elseif($enc!=''){
            $this->sql = "Select * from seg_social_case_management WHERE encounter_nr = $enc";
        }else{
            $this->sql = "Select * from seg_social_case_management WHERE pid = $pid";
        }

        if( $result= $db->Execute($this->sql)){
            if($result->RecordCount()){
                return $result;
            }else{ return false;}
        }else{ return false; }
    }

    #Added By Jarel 03/29/2013
    function hasSocialFamily($pid,$enc){
        global $db;

        if($pid!='' && $enc!=''){
            $this->sql = "Select * from seg_social_patient_family WHERE encounter_nr = $enc";
        }elseif($enc!=''){
            $this->sql = "Select * from seg_social_patient_family WHERE encounter_nr = $enc";
        }else{
            $this->sql = "Select * from seg_social_patient_family WHERE pid = $pid";
        }

         if ($this->result=$db->Execute($this->sql)) {
                    if ($this->count=$this->result->RecordCount()) {
                            return True;
                    }
                    else{return FALSE;}
         }else{return FALSE;}
    }

    #Added by Jarel 06-14-2013
    function saveConsultationFee($pid,$sw_nr){
        global $db;

        $index = "pid,grant_dte,sw_nr";

		$this->sql = "INSERT INTO seg_charity_grants_consultation ($index) VALUES ('$pid',NOW(),'$sw_nr')";

		if($db->Execute($this->sql)){
			return true;
		}else{ return FALSE;}
    }

    #Added by Jarel 06-14-2013
    function updateConsultationFee($pid){
    	global $db;

    	if($this->hasConsultation($pid)){
		$this->sql = "UPDATE seg_charity_grants_consultation
									SET	status='expired'
								WHERE pid='$pid' and status='valid'";
		}else{
			$this->sql = "DELETE s.* FROM seg_misc_service s INNER JOIN seg_misc_service_details d ON
				s.refno=d.refno WHERE s.pid='$pid' AND d.service_code='201200002338'
				AND DATE(s.chrge_dte)=DATE(NOW()) AND ISNULL(d.request_flag)";
		}

		if($db->Execute($this->sql)){
			return true;
		}else{ return FALSE;}

    }

    #Added By Jarel 03/29/2013
    function hasConsultation($pid){
        global $db;
		
		$this->sql = "SELECT * FROM seg_charity_grants_consultation WHERE pid = '$pid' AND status ='valid'
						AND DATE(grant_dte) = DATE(NOW())";
       	if ($this->result=$db->Execute($this->sql)) {
		        if ($this->count=$this->result->RecordCount()) {
		                return True;
		        }
		        else{return FALSE;}
		}else{return FALSE;}
    }

    #Added By Jarel 07/25/2013
    function hasConsultationRequest($pid){
        global $db;
		
		$this->sql ="SELECT s.*,d.* FROM seg_misc_service s INNER JOIN seg_misc_service_details d ON
				s.refno=d.refno WHERE s.pid='$pid' AND d.service_code='201200002338'
				AND DATE(s.chrge_dte)=DATE(NOW()) AND ISNULL(d.request_flag)";

       	if ($this->result=$db->Execute($this->sql)) {
		        if ($this->count=$this->result->RecordCount()) {
		                return True;
		        }
		        else{return FALSE;}
		}else{return FALSE;}
    }

    #Added By Jarel 07/25/2013
    function hasPaidConsultation($pid){
        global $db;
		
		$this->sql = "SELECT p.*,r.* FROM seg_pay p INNER JOIN seg_pay_request r ON p.or_no=r.or_no  WHERE p.pid = '$pid'
						AND DATE(p.or_date) = DATE(NOW()) AND  (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00')
						AND r.service_code='201200002338'";

       	if ($this->result=$db->Execute($this->sql)) {
		        if ($this->count=$this->result->RecordCount()) {
		                return True;
		        }
		        else{return FALSE;}
		}else{return FALSE;}
    }

   	/**
	* Created by Jarel
	* Created on 10/31/2013
   	* Used to Fetch Profile Intake Data
   	* @param string mss_no
   	* @return string Result
   	*/
   	function getSocialServPatientEncounterByMSS($mss_no){
		global $db;

		$this->sql = "SELECT * 
						FROM seg_socserv_patient
						WHERE status NOT IN ($this->dead_stat)
						AND mss_no='".$mss_no."' 
						ORDER BY create_time DESC
						LIMIT 1";
		if ($this->result=$db->Execute($this->sql)) {
				if ($this->count=$this->result->RecordCount()){
				return $this->result->FetchRow();
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}


	function getPatientFunctioning($id,$enc,$pid)
	{
		global $db;
		$this->sql ="SELECT (CASE WHEN sfp.severity_id <> '0' THEN si.severity_index ELSE '' END) AS severity_index,
 				  			(CASE WHEN sfp.duration_id <> '0' THEN di.duration_index ELSE '' END) AS duration_index,
				  			(CASE WHEN sfp.coping_id <> '0' THEN ci.coping_index ELSE '' END) AS coping_index, 
				  			(CASE WHEN sfp.interaction_id <> '0' THEN sti.type_of_interaction ELSE '' END) AS type_of_interaction, 
				  			sfp.others
				  	FROM seg_social_functioning_patient AS sfp 
					INNER JOIN seg_social_duration_index AS di 
					ON di.`duration_nr` = sfp.`duration_id`
					INNER JOIN seg_social_severity_index AS si 
					ON sfp.`severity_id` = si.`severity_nr` 
					INNER JOIN seg_social_coping_index AS ci 
					ON sfp.`coping_id` = ci.`coping_nr` 
					INNER JOIN seg_social_type_interaction AS sti 
					ON sfp.`interaction_id` = sti.`type_nr` 
					WHERE encounter_nr = ".$db->qstr($enc)."
					AND pid = ".$db->qstr($pid)."
					AND social_fxn_id = ".$db->qstr($id);

		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
				return $this->result->FetchRow();
			}else{
				return FALSE;
			}
		}else{
			return FALSE;
		}
	}

	function getPatientSocialProblem($id,$enc,$pid)
	{
		global $db;
		$this->sql = "SELECT (CASE WHEN pp.severity_id <> '0' THEN si.severity_index ELSE '' END) AS severity_index,
 				  			 (CASE WHEN pp.duration_id <> '0' THEN di.duration_index ELSE '' END) AS duration_index,
 				  			 pp.others 
 				  	FROM seg_social_problems_patient AS pp 
 				    INNER JOIN seg_social_severity_index AS si
 				    ON pp.`severity_id` = si.`severity_nr`
  				    INNER JOIN seg_social_duration_index AS di
  				    ON pp.`duration_id` = di.`duration_nr`
				    WHERE encounter_nr = ".$db->qstr($_GET['encounter_nr'])." 
  				    AND pid = ".$db->qstr($_GET['pid'])."
  				    AND problems_fxn_id = ".$db->qstr($id);
					
		if ($this->result=$db->Execute($this->sql)) {
			if ($this->count=$this->result->RecordCount()){
				return $this->result->FetchRow();
			}else{
				return FALSE;
			}
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
}// end of SocialService class

?>