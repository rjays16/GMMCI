<?php
/*
 * Class for updating  `seg_ops_serv`, `seg_ops_servdetails`, and `seg_ops_personell` tables
 * Retrieves data from `care_encounter_op` table.
 * Created by Francis 11-29-13
 * Created for Billing_new
 *
 */
require('./roots.php');
require_once($root_path."include/care_api_classes/class_hospital_admin.php");
require_once($root_path.'include/care_api_classes/class_core.php');
require_once($root_path.'include/care_api_classes/billing/class_billing_new.php');
require_once($root_path.'include/care_api_classes/billing/class_bill_info.php');

define('HOUSE_CASE_PCF', 40);

class SegOps extends Core{


		/**
		* Database table for the requested operation.
		*    - includes refno, encounter
		* @var string
		*/
		var $tb_ops_serv='seg_ops_serv';

		/*
		 * Database table for the details of the operation.
		 *    - includes ops_code, rvu, multiplier
		 * @var string
		 */
		var $tb_ops_servdetails = 'seg_ops_servdetails';

		/*
		 * Database table for the personnel involve in a paticular operation.
		 *    - includes surgeons, assistant surgeons, scrub nurses, rotating nurses
		 * @var string
		 */
		var $tb_ops_personell = 'seg_ops_personell';

		/**
		* Database table for the operation requests.
		* @var string
		*/
		var $tb_encounter_op='care_encounter_op';
		/**
		* SQL query result. Resulting ADODB record object.
		* @var object
		*/
		
		/*--Delete procedures/ICP--*/
		function delProcedure($encounter, $bill_dt, $bill_frmdte, $op_code) {
			global $db;

			$bSuccess = false;

			$strSQL = "select * from seg_misc_ops_details ".
								"   where ops_code = '".$op_code."' and exists (select * from seg_misc_ops as smo where smo.refno = seg_misc_ops_details.refno ".
								"      and smo.encounter_nr in $encounter and smo.chrge_dte >= '".$bill_frmdte."') ".
					      "      and not EXISTS(SELECT * FROM seg_ops_chrgd_accommodation AS soca WHERE soca.ops_refno = seg_misc_ops_details.refno AND
											 soca.ops_entryno = seg_misc_ops_details.entry_no AND soca.ops_code = seg_misc_ops_details.ops_code)
						 and not EXISTS(SELECT * FROM seg_ops_chrg_dr AS socd WHERE socd.ops_refno = seg_misc_ops_details.refno AND
											 socd.ops_entryno = seg_misc_ops_details.entry_no AND socd.ops_code = seg_misc_ops_details.ops_code) ".
								"      and get_lock('smops_lock', 10) ".
								"   order by entry_no desc limit 1";

			    $rs = $db->Execute($strSQL);
			    if ($rs) {
					$db->StartTrans();
					$row = $rs->FetchRow();
					if ($row) {
						$refno = $row['refno'];
						$entryno = $row['entry_no'];

						$strSQL = "delete from seg_misc_ops_details where refno = '$refno' and entry_no = $entryno and ops_code = '$op_code'";
						$bSuccess = $db->Execute($strSQL);

						$strSQL = "select RELEASE_LOCK('smops_lock')";
						$db->Execute($strSQL);

						if ($bSuccess) {
								// Delete this header if already without details ...
								$dcount = 0;
								$strSQL = "select count(*) dcount from seg_misc_ops_details where refno = '$refno'";
		 					  $rs = $db->Execute($strSQL);
		 					  if ($rs) {
									$row = $rs->FetchRow();
									$dcount = ($row) ? $row['dcount'] : 0;
									if ($dcount == 0) {
											$strSQL = "delete from seg_misc_ops where refno = '$refno'";
											$bSuccess = $db->Execute($strSQL);
									}
		 					  }
						}


						if($bSuccess) {
							$db->CompleteTrans();
							return TRUE;

						}else{
							$db->FailTrans();
							return FALSE;
						}
					}
	 	 			}else{ return FALSE;};
		}//end of delProcedure function

		// Added by James 1/6/2014
		// function delProcedure($encounter, $refno, $op_code) {
		// 	global $db;

		// 	$sql = "DELETE FROM seg_misc_ops WHERE refno='".$refno."'";
		// 	$sql2 = "DELETE FROM seg_misc_ops_details WHERE refno='".$refno."' AND ops_code='".$op_code."'";
	
		// 	//$rs = $db->Execute($sql);
		// 	$rs2 = $db->Execute($sql2);

		// 	if($rs && $rs2){
		// 		return TRUE;
		// 	}else{
		// 		return FALSE;
		// 	}
		// }

		function delProcedureversion2($details){
			global $db;

			$this->DelSql = "DELETE 
					FROM seg_misc_ops_details
					WHERE ops_code =".$db->qstr($details['code'])."
					AND entry_no = ".$db->qstr($details['opEntry'])."
					AND refno = ".$db->qstr($details['refno']);

			if($db->Execute($this->DelSql)){
				$this->sql = "SELECT refno 
								FROM seg_misc_ops_details 
								WHERE refno = ".$db->qstr($details['refno']);
				if($this->result = $db->GetOne($this->sql)){
					return true;
				}else{
					$this->DelSqlMiscParent = "DELETE FROM seg_misc_ops  
												WHERE encounter_nr IN ".$details['encounter_nr']."
												AND refno = ".$db->qstr($details['refno']);
					$this->delResult = $db->Execute($this->DelSqlMiscParent);
					if($this->delResult){
						return true;
					}else{
						return false;
					}
				}
			}else{
				return false;
			}
		}

		function fetch_case_rate($counter_nr) {
			global $db;
			$srefno = array();

			$strSQL = "SELECT package_id, rate_type ".
					  "FROM seg_billing_caserate ".
					  "WHERE bill_nr = (SELECT MAX(bill_nr) ".
					  				   "FROM seg_billing_encounter ".
					  				   "WHERE encounter_nr = '".$counter_nr."' LIMIT 1)".
					  "AND is_deleted != '1'";
						

			if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
					while ($row = $result->FetchRow()) {
						$srefno[] = $row['package_id'];
						// $srefno['rate_type'] = $row['rate_type'];
					}
				}
			} else { 
				return FALSE;
			};

  			return $srefno;
		}
		

		/*-----------------Add Procedures/ICP-------------------------------*/
		//added by Francis 11-27-2013
		function addProcedure($procData) {
			global $db;
			extract($procData);

			$bSuccess = true;

			if($encNr != ''){

				$db->StartTrans();

				$refno = $this->getMiscOpRefNo($billDate, $encNr);

				if ($refno == '') {
					$strSQL = "insert into seg_misc_ops (chrge_dte, encounter_nr, modify_id, create_id, create_dt) ".
										"   values ('".$billDate."', '".$encNr."', '".$user."', '".$user."', ".
										"          '".$billDate."')";
					if ($db->Execute($strSQL))
							$refno = $this->getMiscOpRefNo($billDate, $encNr);
					else
							$bSuccess = false;
				}

				if($bSuccess){
					$op_charge = str_replace(",", "", $charge);
					$strSQL = "insert into seg_misc_ops_details (refno, ops_code, rvu, multiplier, chrg_amnt, op_date, laterality, num_sessions, special_dates, description) ".
								"   values ('".$refno."', '".$code."', ".$rvu.", ".$multiplier.", ".$op_charge.", '".$opDate."', '".$laterality."', '".$num_sessions."', '".$special_dates."', ".$db->qstr($desc).")";
					$bSuccess = $db->Execute($strSQL);
				}

				if($bSuccess) {
					$db->CompleteTrans();
					return TRUE;
				}else{
					$db->FailTrans();
					return FALSE;
				}

			}else{return FALSE;}			

		}
		/*---------end-----Add Procedures/ICP-----------end-----------------*/

		/*------------------------Get Procedure Refno-------------------------*/
		//added by Francis 11-27-2013
		function getMiscOpRefNo($bill_frmdte, $enc_nr){
		global $db;

			$srefno = '';
			$strSQL = "select refno ".
								"   from seg_misc_ops ".
								"   where str_to_date(chrge_dte, '%Y-%m-%d %H:%i:%s') >= '".$bill_frmdte."' ".
								"      and encounter_nr = '".$enc_nr."' ".
								"   order by chrge_dte limit 1";

			if ($result = $db->Execute($strSQL)) {
					if ($result->RecordCount()) {
							while ($row = $result->FetchRow())
									$srefno = $row['refno'];
					}
			}

			return($srefno);
		}
		/*----------end-----------Get Procedure Refno------------end----------*/



		function SearchCurrentOP($enc_nr, $bill_frmdte, $bill_dt,$maxcount=100,$offset=0, $b_all = false){
				global $db;

				if(empty($maxcount)) $maxcount=100;
				if(empty($offset)) $offset=0;

		if ($b_all)
			$this->sql = "select refno, entry_no, ops_code as code, op_count, description, t.rvu, multiplier, op_charge, group_code, provider, op_date
							from
							(select od.refno, 0 as entry_no, od.ops_code, sum(od.rvu) as rvu, max(od.multiplier) as multiplier, sum(od.rvu * od.multiplier) as op_charge, group_code, 'OR' as provider,
									 (SELECT MAX(ceo.op_date) AS op_date
										FROM seg_ops_serv AS sos INNER JOIN care_encounter_op AS ceo ON sos.refno = ceo.refno
										WHERE sos.refno = os.refno) as op_date,
								 (SELECT COUNT(ops_code) AS op_count FROM seg_ops_servdetails AS od2 WHERE od2.ops_code = od.ops_code AND od2.refno = od.refno) AS op_count
								 from seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno
								 where encounter_nr = '". $enc_nr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED'
									and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."')
								 group by ops_code
							 union
							 select md.refno, md.entry_no, md.ops_code, sum(md.rvu) as rvu, max(md.multiplier) as multiplier, sum(chrg_amnt) as chrg_amnt, group_code, 'OA' as provider, md.op_date,
								(SELECT COUNT(ops_code) AS op_count FROM seg_misc_ops_details AS md2 WHERE md2.ops_code = md.ops_code AND md2.refno = md.refno) AS op_count
								from seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno
								where encounter_nr = '". $enc_nr. "' and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									 and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."')
								group by ops_code) as t inner join seg_ops_rvs as om on t.ops_code = om.code
							order by description LIMIT $offset, $maxcount";
		else
			$this->sql = "select refno, entry_no, ops_code as code, op_count, description, max(t.rvu) as rvu, max(multiplier) as multiplier, max(op_charge) as op_charge, group_code, provider, max(op_date) as op_date,description AS alt_desc
							from
							(select od.refno, 0 as entry_no, od.ops_code, od.rvu, od.multiplier, (od.rvu * od.multiplier) as op_charge, group_code, 'OR' as provider,
									 (SELECT MAX(ceo.op_date) AS op_date
										FROM seg_ops_serv AS sos INNER JOIN care_encounter_op AS ceo ON sos.refno = ceo.refno
										WHERE sos.refno = os.refno) as op_date,
								 (SELECT COUNT(ops_code) AS op_count FROM seg_ops_servdetails AS od2 WHERE od2.ops_code = od.ops_code AND od2.refno = od.refno) AS op_count
								 from seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno
								 where encounter_nr = '". $enc_nr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED'
									and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."')
									and group_code <> ''
							 union
							 select md.refno, md.entry_no, md.ops_code, md.rvu, md.multiplier, chrg_amnt, group_code, 'OA' as provider, md.op_date,
								(SELECT COUNT(ops_code) AS op_count FROM seg_misc_ops_details AS md2 WHERE md2.ops_code = md.ops_code AND md2.refno = md.refno) AS op_count
								from seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno
								where encounter_nr = '". $enc_nr. "' and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									 and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."') and group_code <> ''
							 order by rvu desc) as t inner join seg_ops_rvs as om on t.ops_code = om.code
							group by group_code
							union
							select t.refno, t.entry_no, t.ops_code, op_count, om.description, t.rvu, t.multiplier, op_charge, t.group_code, provider, t.op_date, smod.description AS alt_desc 
							from
							(select od.refno, 0 as entry_no, od.ops_code, sum(od.rvu) as rvu, max(od.multiplier) as multiplier, sum(od.rvu * od.multiplier) as op_charge, group_code, 'OR' as provider,
									 (SELECT MAX(ceo.op_date) AS op_date
										FROM seg_ops_serv AS sos INNER JOIN care_encounter_op AS ceo ON sos.refno = ceo.refno
										WHERE sos.refno = os.refno) as op_date,
								 (SELECT COUNT(ops_code) AS op_count FROM seg_ops_servdetails AS od2 WHERE od2.ops_code = od.ops_code AND od2.refno = od.refno) AS op_count
								 from seg_ops_serv as os inner join seg_ops_servdetails as od on os.refno = od.refno
								 where encounter_nr = '". $enc_nr. "' and is_cash = 0 and upper(trim(os.status)) <> 'DELETED'
									and (str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									and str_to_date(concat(date_format(os.request_date, '%Y-%m-%d'), ' ', date_format(os.request_time, '%H:%i:%s')), '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."')
									and group_code = ''
								 group by ops_code
							 union
							 select md.refno, md.entry_no, md.ops_code, sum(md.rvu) as rvu, max(md.multiplier) as multiplier, sum(chrg_amnt) as chrg_amnt, group_code, 'OA' as provider, md.op_date,
								"/*."(SELECT COUNT(ops_code) AS op_count FROM seg_misc_ops_details AS md2 WHERE md2.ops_code = md.ops_code AND md2.refno = md.refno) AS op_count"*//*commented by Nick 05-08-2014*/."
								COUNT(ops_code) AS op_count"./*added by Nick 05-08-2014*/"
								from seg_misc_ops as mo inner join seg_misc_ops_details as md on mo.refno = md.refno
								where encounter_nr = '". $enc_nr. "' and (str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') >= '". $bill_frmdte ."'
									 "./* and str_to_date(mo.chrge_dte, '%Y-%m-%d %H:%i:%s') < '". $bill_dt ."' */") and group_code = ''
								group by ops_code) as t inner join seg_ops_rvs as om on t.ops_code = om.code
							
							INNER JOIN seg_misc_ops_details AS smod ON  t.ops_code  =  smod.ops_code AND t.refno = smod.refno

							order by description LIMIT $offset, $maxcount";

				if($this->res['ssl']=$db->Execute($this->sql)){
						if($this->rec_count=$this->res['ssl']->RecordCount()) {
								return $this->res['ssl'];
						}else{return FALSE;}
				}else{return FALSE;}
		}

	function SearchAppliedOP($enc_nr = '', $searchkey = '', $maxcount = 100, $offset = 0, $b_drchrg = 0, $dr_nr = 0, $b_all = 0)
	{
		global $db;

		if (empty($maxcount)) $maxcount = 100;
		if (empty($offset)) $offset = 0;
		if (empty($b_drchrg)) $b_drchrg = 0;

		$encounterDate = date('Y-m-d', strtotime(Billing::getEncounterDate($enc_nr)));

		# convert * and ? to % and &
		$searchkey = strtr($searchkey, '*?', '%_');
		$searchkey = trim($searchkey);
		$searchkey = str_replace("^", "'", $searchkey);
		$keyword = addslashes($searchkey);

		if ($b_drchrg == 1) {
			if ($b_all) {
				$this->sql = "SELECT refno, ops_code,
											 (SELECT description FROM seg_ops_rvs AS t3
														 WHERE t3.code = t.ops_code
															AND description LIKE '%" . $keyword . "%') AS description, op_date,
												t.rvu AS rvu, multiplier, group_code, entry_no,
											 (SELECT ifnull(count(*), 0) AS count FROM seg_ops_chrg_dr AS soca
												 WHERE soca.ops_refno = t.refno AND
													soca.ops_code = t.ops_code
													AND dr_nr = " . $dr_nr . ") AS bselected
									FROM
									(SELECT sosd.refno, sosd.ops_code, ifnull(soca.rvu, sosd.rvu) AS rvu, ifnull(soca.multiplier, sosd.multiplier) AS multiplier, group_code, 0 AS entry_no,
											if(soca.ops_refno IS NULL, 0, 1) AS bselected,
									   (SELECT MAX(ceo.op_date) op_date
									       FROM care_encounter_op ceo
									       WHERE ceo.refno = sos.refno) op_date
										 FROM ((seg_ops_serv AS sos INNER JOIN seg_ops_servdetails AS sosd ON sos.refno = sosd.refno)
											INNER JOIN seg_ops_rvs AS sor ON sosd.ops_code = sor.code)
											LEFT JOIN seg_ops_chrg_dr AS soca ON soca.ops_refno = sosd.refno AND soca.ops_code = sosd.ops_code AND ops_entryno = 0 AND dr_nr = " . $dr_nr . "
										 WHERE sos.encounter_nr = '" . $enc_nr . "' AND
											(sor.description LIKE '%" . $keyword . "%' OR
											 sosd.ops_code LIKE '%" . $keyword . "%')
									 UNION
									SELECT smod.refno, smod.ops_code, ifnull(soca.rvu, smod.rvu) AS rvu, ifnull(soca.multiplier,smod.multiplier) AS multiplier, group_code, smod.entry_no,
											if(soca.ops_refno IS NULL, 0, 1) AS bselected, smod.op_date
										 FROM ((seg_misc_ops AS smo INNER JOIN seg_misc_ops_details AS smod ON smo.refno = smod.refno)
											INNER JOIN seg_ops_rvs AS sor ON smod.ops_code = sor.code)
											LEFT JOIN seg_ops_chrg_dr AS soca ON soca.ops_refno = smod.refno AND soca.ops_code = smod.ops_code AND ops_entryno = smod.entry_no AND dr_nr = " . $dr_nr . "
										 WHERE smo.encounter_nr = '" . $enc_nr . "' AND
											(sor.description LIKE '%" . $keyword . "%' OR
											 smod.ops_code LIKE '%" . $keyword . "%')) AS t
									ORDER BY description";
			} else {
				$this->sql = "SELECT refno, ops_code,
											 (SELECT description FROM seg_ops_rvs AS t3
														 WHERE t3.code = t.ops_code
															AND description LIKE '%" . $keyword . "%') AS description, op_date,
											 max(t.rvu) AS rvu, max(multiplier) AS multiplier, group_code, entry_no,
											 (SELECT ifnull(count(*), 0) AS count FROM seg_ops_chrg_dr AS soca
												 WHERE soca.ops_refno = t.refno AND
													soca.ops_code = t.ops_code
													AND dr_nr = " . $dr_nr . ") AS bselected
									FROM
									(SELECT sosd.refno, sosd.ops_code, ifnull(soca.rvu, sosd.rvu) AS rvu, ifnull(soca.multiplier, sosd.multiplier) AS multiplier, group_code, 0 AS entry_no,
											 if(soca.ops_refno IS NULL, 0, 1) AS bselected,
										   (SELECT MAX(ceo.op_date) op_date
										       FROM care_encounter_op ceo
										       WHERE ceo.refno = sos.refno) op_date
										 FROM (seg_ops_serv AS sos INNER JOIN seg_ops_servdetails AS sosd ON sos.refno = sosd.refno)
											LEFT JOIN seg_ops_chrg_dr AS soca ON soca.ops_refno = sosd.refno AND soca.ops_code = sosd.ops_code AND ops_entryno = 0 AND dr_nr = " . $dr_nr . "
										 WHERE sos.encounter_nr = '" . $enc_nr . "' AND
											sosd.ops_code LIKE '%" . $keyword . "%'
									 UNION
									SELECT smod.refno, smod.ops_code, ifnull(soca.rvu, smod.rvu) AS rvu, ifnull(soca.multiplier,smod.multiplier) AS multiplier, group_code, smod.entry_no,
											if(soca.ops_refno IS NULL, 0, 1) AS bselected, smod.op_date
										 FROM (seg_misc_ops AS smo INNER JOIN seg_misc_ops_details AS smod ON smo.refno = smod.refno)
											LEFT JOIN seg_ops_chrg_dr AS soca ON soca.ops_refno = smod.refno AND soca.ops_code = smod.ops_code AND ops_entryno = smod.entry_no AND dr_nr = " . $dr_nr . "
										 WHERE smo.encounter_nr = '" . $enc_nr . "' AND
											smod.ops_code LIKE '%" . $keyword . "%' ORDER BY rvu DESC) AS t
									GROUP BY group_code HAVING group_code <> ''
									UNION
									SELECT refno, ops_code, t.description, op_date, rvu, multiplier, group_code, entry_no, bselected
									FROM
									(SELECT sosd.refno, sosd.ops_code, sor.description, ifnull(soca.rvu, sosd.rvu) AS rvu, ifnull(soca.multiplier, sosd.multiplier) AS multiplier, group_code, 0 AS entry_no,
											 if(soca.ops_refno IS NULL, 0, 1) AS bselected,
										   (SELECT MAX(ceo.op_date) op_date
										       FROM care_encounter_op ceo
										       WHERE ceo.refno = sos.refno) op_date
										 FROM ((seg_ops_serv AS sos INNER JOIN seg_ops_servdetails AS sosd ON sos.refno = sosd.refno)
											INNER JOIN seg_ops_rvs AS sor ON sosd.ops_code = sor.code)
											LEFT JOIN seg_ops_chrg_dr AS soca ON soca.ops_refno = sosd.refno AND soca.ops_code = sosd.ops_code AND ops_entryno = 0 AND dr_nr = " . $dr_nr . "
										 WHERE sos.encounter_nr = '" . $enc_nr . "' AND
											(sor.description LIKE '%" . $keyword . "%' OR
											 sosd.ops_code LIKE '%" . $keyword . "%')
									 UNION
									SELECT smod.refno, smod.ops_code, sor.description, ifnull(soca.rvu, smod.rvu) AS rvu, ifnull(soca.multiplier,smod.multiplier) AS multiplier, group_code, smod.entry_no,
											if(soca.ops_refno IS NULL, 0, 1) AS bselected, smod.op_date
										 FROM ((seg_misc_ops AS smo INNER JOIN seg_misc_ops_details AS smod ON smo.refno = smod.refno)
											INNER JOIN seg_ops_rvs AS sor ON smod.ops_code = sor.code)
											LEFT JOIN seg_ops_chrg_dr AS soca ON soca.ops_refno = smod.refno AND soca.ops_code = smod.ops_code AND ops_entryno = smod.entry_no AND dr_nr = " . $dr_nr . "
										 WHERE smo.encounter_nr = '" . $enc_nr . "' AND
											(sor.description LIKE '%" . $keyword . "%' OR
											 smod.ops_code LIKE '%" . $keyword . "%')
										 ORDER BY description) AS t
									WHERE group_code = '' ORDER BY description";
			}
		} else {
			if ($b_all) {
				$this->sql = "SELECT refno, ops_code,
												 (SELECT description FROM seg_ops_rvs AS t3
															 WHERE t3.code = t.ops_code
																AND description LIKE '%" . $keyword . "%') AS description, op_date,
													t.rvu AS rvu, multiplier, group_code, entry_no,
												 (SELECT ifnull(count(*), 0) AS count FROM seg_ops_chrgd_accommodation AS soca
													 WHERE soca.ops_refno = t.refno AND
														soca.ops_code = t.ops_code) AS bselected
										FROM " .
					"(SELECT sosd.refno, sosd.ops_code, t.description, sosd.rvu, sosd.multiplier, group_code, 0 AS entry_no, " .
					"   (SELECT ifnull(count(*), 0) AS count FROM seg_ops_chrgd_accommodation AS soca " .
					"       WHERE soca.ops_refno = sosd.refno AND soca.ops_code = sosd.ops_code AND ops_entryno = 0) AS bselected, " .
					"   (SELECT MAX(ceo.op_date) op_date
															       FROM care_encounter_op ceo
															       WHERE ceo.refno = sos.refno) op_date " .
					"   FROM (seg_ops_serv AS sos INNER JOIN seg_ops_servdetails AS sosd ON sos.refno = sosd.refno) " .
					"   INNER JOIN seg_ops_rvs AS sor ON sosd.ops_code = sor.code " .
					"   WHERE sos.encounter_nr = '" . $enc_nr . "' AND " .
					"      (sor.description LIKE '%" . $keyword . "%' OR " .
					"       sosd.ops_code LIKE '%" . $keyword . "%') " .
					" UNION " .
					"SELECT smod.refno, smod.ops_code, sor.description, smod.rvu, smod.multiplier, group_code, smod.entry_no, " .
					"      (SELECT ifnull(count(*), 0) AS count FROM seg_ops_chrgd_accommodation AS soca " .
					"          WHERE soca.ops_refno = smod.refno AND soca.ops_code = smod.ops_code AND ops_entryno = smod.entry_no) AS bselected, " .
					"       smod.op_date " .
					"   FROM (seg_misc_ops AS smo INNER JOIN seg_misc_ops_details AS smod ON smo.refno = smod.refno) " .
					"   INNER JOIN seg_ops_rvs AS sor ON smod.ops_code = sor.code " .
					"   WHERE smo.encounter_nr = '" . $enc_nr . "' AND " .
					"      (sor.description LIKE '%" . $keyword . "%' OR " .
					"       smod.ops_code LIKE '%" . $keyword . "%') " .
					"   ORDER BY description) AS t
										 ORDER BY description";
			} else {
				$this->sql = "SELECT refno, ops_code,
												 (SELECT description FROM seg_ops_rvs AS t3
															 WHERE t3.code = t.ops_code
																AND description LIKE '%" . $keyword . "%') AS description, op_date,
												 max(t.rvu) AS rvu, max(multiplier) AS multiplier, group_code, entry_no,
												 (SELECT ifnull(count(*), 0) AS count FROM seg_ops_chrgd_accommodation AS soca
													 WHERE soca.ops_refno = t.refno AND
														soca.ops_code = t.ops_code) AS bselected
										FROM
										(SELECT sosd.refno, sosd.ops_code, sosd.rvu, sosd.multiplier, group_code, 0 AS entry_no,
										   (SELECT MAX(ceo.op_date) op_date
										       FROM care_encounter_op ceo
										       WHERE ceo.refno = sos.refno) op_date
											 FROM seg_ops_serv AS sos INNER JOIN seg_ops_servdetails AS sosd ON sos.refno = sosd.refno
											 WHERE sos.encounter_nr = '" . $enc_nr . "' AND
												sosd.ops_code LIKE '%" . $keyword . "%'
										 UNION
										SELECT smod.refno, smod.ops_code, smod.rvu, smod.multiplier, group_code, smod.entry_no, smod.op_date
											 FROM seg_misc_ops AS smo INNER JOIN seg_misc_ops_details AS smod ON smo.refno = smod.refno
											 WHERE smo.encounter_nr = '" . $enc_nr . "' AND
												smod.ops_code LIKE '%" . $keyword . "%' ORDER BY rvu DESC) AS t
										GROUP BY group_code HAVING group_code <> ''
										UNION
										SELECT refno, ops_code, t.description, op_date, rvu, multiplier, group_code, entry_no, bselected
										FROM " .
					"(SELECT sosd.refno, sosd.ops_code, sor.description, sosd.rvu, sosd.multiplier, group_code, 0 AS entry_no, " .
					"   (SELECT ifnull(count(*), 0) AS count FROM seg_ops_chrgd_accommodation AS soca " .
					"       WHERE soca.ops_refno = sosd.refno AND soca.ops_code = sosd.ops_code AND ops_entryno = 0) AS bselected, " .
					"   (SELECT MAX(ceo.op_date) op_date
																       FROM care_encounter_op ceo
																       WHERE ceo.refno = sos.refno) op_date " .
					"   FROM (seg_ops_serv AS sos INNER JOIN seg_ops_servdetails AS sosd ON sos.refno = sosd.refno) " .
					"   INNER JOIN seg_ops_rvs AS sor ON sosd.ops_code = sor.code " .
					"   WHERE sos.encounter_nr = '" . $enc_nr . "' AND " .
					"      (sor.description LIKE '%" . $keyword . "%' OR " .
					"       sosd.ops_code LIKE '%" . $keyword . "%') " .
					" UNION " .
					"SELECT smod.refno, smod.ops_code, sor.description, smod.rvu, smod.multiplier, group_code, smod.entry_no, " .
					"      (SELECT ifnull(count(*), 0) AS count FROM seg_ops_chrgd_accommodation AS soca " .
					"          WHERE soca.ops_refno = smod.refno AND soca.ops_code = smod.ops_code AND ops_entryno = smod.entry_no) AS bselected, " .
					"      smod.op_date " .
					"   FROM (seg_misc_ops AS smo INNER JOIN seg_misc_ops_details AS smod ON smo.refno = smod.refno) " .
					"   INNER JOIN seg_ops_rvs AS sor ON smod.ops_code = sor.code " .
					"   WHERE smo.encounter_nr = '" . $enc_nr . "' AND " .
					"      (sor.description LIKE '%" . $keyword . "%' OR " .
					"       smod.ops_code LIKE '%" . $keyword . "%') " .
					"   ORDER BY description) AS t
											WHERE group_code = '' ORDER BY description";
			}
		}

		if ($this->res['ssl'] = $db->SelectLimit($this->sql, $maxcount, $offset)) {
			if ($this->res['ssl']->RecordCount()) {   // fix for Bugzilla bug 68
				return $this->res['ssl'];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}


		function getOPCharge($enc_nr, $bill_dt, $nrvu, $casetyp="") {
			global $db;

			$ncharge = 0;

			$strSQL = "SELECT fn_getORCharge('{$enc_nr}', date('{$bill_dt}'), {$nrvu}, {$casetyp}) as opcharge";
			if ($result = $db->Execute($strSQL)) {
				if ($result->RecordCount()) {
					if ($row = $result->FetchRow()) {
						$ncharge = $row["opcharge"];
					}
				}
			}

			return $ncharge;
		}

		function isHouseCase($enc_nr) {
			global $db;

			$case = '';
			$sql = "select st.casetype_desc from seg_encounter_case sc
										inner join seg_type_case st on sc.casetype_id = st.casetype_id ".
						 "   where encounter_nr = '".$enc_nr."' ".
						 "   order by sc.modify_dt desc limit 1";

			if($result = $db->Execute($sql)){
					if($result->RecordCount()){
							if ($row = $result->FetchRow()) {
								$case = $row['casetype_desc'];
							}
					}
			}

			return !(strpos($case, 'HOUSE') === false);
		}

		function getOpAccommodationRefNo($bill_frmdte, $enc_nr) {
			global $db;

			$srefno = '';
			$strSQL = "select refno ".
								"   from seg_opaccommodation ".
								"   where str_to_date(chrge_dte, '%Y-%m-%d %H:%i:%s') >= '".$bill_frmdte."' ".
								"      and encounter_nr = '".$enc_nr."' ".
								"   order by chrge_dte limit 1";

			if ($result = $db->Execute($strSQL)) {
					if ($result->RecordCount()) {
							while ($row = $result->FetchRow())
									$srefno = $row['refno'];
					}
			}

			return($srefno);
		}

		function getMaxNoFromOPAccomDetails($refno) {
			global $db;

			$n = 0;
			$strSQL = "select ifnull(max(entry_no), 0) as latest_no ".
								"   from seg_opaccommodation_details as sod ".
								"   where refno = '".$refno."'";

			if ($result = $db->Execute($strSQL)) {
					if ($result->RecordCount()) {
							while ($row = $result->FetchRow())
									$n = $row['latest_no'];
					}
			}

			return($n);
		}


				
}  # end class SegOps
?>