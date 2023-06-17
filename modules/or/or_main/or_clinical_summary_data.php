<?php
	include_once($root_path.'include/care_api_classes/class_encounter.php');

	define("OPD_PATIENT", 2);

	$refno = $_GET['refno'];
	$encounter_nr = $_GET['encounter_nr'];
	$pid = $_GET['pid'];

	$prev_encounter = "";

	$op_date = "";
	$op_performed = "";
	$complication = "";

	$rundate = strftime("%Y-%m-%d %H:%M:%S", strtotime("now"));
	$opd_preop_diagnosis = "";
	$ipd_preop_diagnosis = "";

	getPostOpData($refno, $op_date, $op_performed, $complication);
	getPrevEncounterNr($encounter_nr);

	function getPrevEncounterNr($enc_nr) {
		global $db;

		$prev_enc_nr = "";
		$strSQL = "select parent_encounter_nr
						from care_encounter
						where encounter_nr = '".$enc_nr."'";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$row = $result->FetchRow();
				$prev_enc_nr = $row['parent_encounter_nr'];
			}
		}

		$prev_encounter = $prev_enc_nr;
		return($prev_enc_nr);
	}

	function getEncounterType($enc_nr) {
		$encobj = new Encounter();
		return $encobj->EncounterType($enc_nr);
	}

	function getBriefHistory($pid) {
		global $db;

//		$encobj = new Encounter();
//		$pid = $encobj->PID($enc_nr);

		$history = "";
		$strSQL = "SELECT
									encounter_nr,
									encounter_date,
									chief_complaint
								FROM care_encounter
								WHERE pid = '$pid'
										AND chief_complaint <> ''
										AND chief_complaint IS NOT NULL
								ORDER BY encounter_date DESC
								LIMIT 10";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				$i = 0;
				while ($row = $result->FetchRow()) {
					if ($history != '') $history .= "\n";
					$history .= strftime("%m-%d-%Y", strtotime($row["encounter_date"])) ."\t".$row["chief_complaint"];
					if ($i++ == 0) $opd_preop_diagnosis = $row["chief_complaint"];
				}
			}
		}

		return $history;
	}

	function getAdmImpression($enc_nr) {
		global $db;

		$impression = "";
		$strSQL = "SELECT
									er_opd_diagnosis
								FROM care_encounter
								WHERE encounter_nr = '$enc_nr'";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				if ($row = $result->FetchRow()) {
					$impression = $row["er_opd_diagnosis"];
				}
			}
		}

		$ipd_preop_diagnosis = $impression;

		return $impression;
	}

	function getMedications($enc_nr) {
		global $db;

		$prv_encnr = ($prev_encounter == '') ? getPrevEncounterNr($enc_nr) : $prev_encounter;
//		$filter = array('','');
		$filter = '';

		if ($prv_encnr != '') $filter = " or encounter_nr = '$prv_encnr'";
//		if ($prv_encnr != '') $filter[1] = " or si.encounter_nr = '$prv_encnr'";
		$medications = '';

		$strSQL = "select bestellnum, artikelname, max(flag) as flag, sum(qty) as qty ".
					" from ".
					"(select 0 as flag, pd.bestellnum, (case when (isnull(generic) or (generic = '')) then artikelname else generic end) as artikelname, sum(pd.quantity - ifnull(spri.quantity, 0)) as qty ".
					"   from ((seg_pharma_orders as ph inner join
								(select * from seg_pharma_order_items d
										where d.serve_status <> 'N' and d.request_flag is null) as pd on ph.refno = pd.refno) ".
					"         inner join care_pharma_products_main as p on pd.bestellnum = p.bestellnum and p.prod_class = 'M') ".
					"         left join seg_pharma_return_items as spri on pd.refno = spri.ref_no and pd.bestellnum = spri.bestellnum ".
					"      where (encounter_nr = '". $enc_nr. "'".$filter.") ".
					"           and str_to_date(ph.orderdate, '%Y-%m-%d %H:%i:%s') < '" . $rundate . "' " .
					"        and (pd.quantity - ifnull(spri.quantity, 0)) > 0 ".
					"   group by pd.bestellnum ".
					" union ".
					"select 1 as flag, mpd.bestellnum, (case when (isnull(generic) or (generic = '')) then artikelname else generic end) as artikelname, sum(quantity) as qty ".
					"   from (seg_more_phorder as mph inner join seg_more_phorder_details as mpd on mph.refno = mpd.refno) ".
					"      inner join care_pharma_products_main as p on mpd.bestellnum = p.bestellnum and p.prod_class = 'M' ".
					"   where (encounter_nr = '". $enc_nr. "'".$filter.") ".
					"           and str_to_date(mph.chrge_dte, '%Y-%m-%d %H:%i:%s') < '" . $rundate . "' ".
					"   group by mpd.bestellnum) as t ".
					" group by bestellnum, artikelname order by artikelname";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					if ($medications != '') $medications .= "\n";
					$medications .= $row["bestellnum"]."\t".$row["artikelname"]."\t".$row["qty"];
				}
			}
		}

		return $medications;
	}

	function getProcedures($enc_nr) {
		global $db;

		$filter = '';
		$prv_encnr = ($prev_encounter == '') ? getPrevEncounterNr($enc_nr) : $prev_encounter;
		if ($prv_encnr != '') $filter = " or encounter_nr = '$prv_encnr'";

		$procedures = '';

		$strSQL = "SELECT ops_code, description, max(rvu) as rvu, sum(op_count) as op_count
							 FROM (
								SELECT 1 as tr_id, od.ops_code, sor.description, od.rvu, COUNT(od.ops_code) AS op_count
										FROM (seg_ops_serv AS os INNER JOIN seg_ops_servdetails AS od ON os.refno = od.refno)
											 INNER JOIN seg_ops_rvs sor ON od.ops_code = sor.code
										WHERE (encounter_nr = '". $enc_nr. "'".$filter.") AND UPPER(TRIM(os.status)) <> 'DELETED'
										GROUP BY od.ops_code
								UNION
								SELECT 2 as tr_id, md.ops_code, sor.description, md.rvu, COUNT(md.ops_code) AS op_count
										FROM (seg_misc_ops as mo INNER JOIN seg_misc_ops_details as md on mo.refno = md.refno)
											 INNER JOIN seg_ops_rvs sor ON md.ops_code = sor.code
										WHERE (encounter_nr = '". $enc_nr. "'".$filter.")
										GROUP BY md.ops_code) as t
								GROUP BY ops_code";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					if ($procedures != '') $procedures .= "\n";
					$procedures .= $row["ops_code"]."\t".$row["description"]."\t".$row["rvu"]."\t".$row["op_count"];
				}
			}
		}

		return $procedures;
	}

	function getDoctorDiagnosis($enc_nr) {
		global $db;

		$final_diag = "";
		$strSQL = "SELECT
									dd.icd_code,
									icd.description
								FROM seg_doctors_diagnosis dd
									INNER JOIN care_icd10_en icd
										ON dd.icd_code = icd.diagnosis_code
								WHERE encounter_nr = '$enc_nr'
								ORDER BY description";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					if ($final_diag != '') $final_diag .= "\n";
					$final_diag .= $row["icd_code"]."\t".$row["description"];
				}
			}
		}

		return $final_diag;
	}

	function getPreOpDiagnosis() {
		$enctyp = getEncounterType($encounter_nr);
		$diagnosis = "";
		if ($enctyp == OPD_PATIENT) {
			$diagnosis = ($opd_preop_diagnosis == "") ? $ipd_preop_diagnosis : $opd_preop_diagnosis;
			if ($diagnosis == "") {
				getBriefHistory($pid);
				$diagnosis = $opd_preop_diagnosis;
			}
		}
		else {
			$diagnosis = ($ipd_preop_diagnosis == "") ? $opd_preop_diagnosis : $ipd_preop_diagnosis;
			if ($diagnosis == "") {
				getAdmImpression($encounter_nr);
				$diagnosis = $ipd_preop_diagnosis;
			}
		}

		return $diagnosis;
	}

//	function getOpDate($enc_nr) {
//		global $db;

//		$opdate = "00/00/0000";
//		$strSQL = "SELECT
//									date_operation
//								FROM seg_or_main
//								WHERE encounter_nr = '$enc_nr'";
//		if ($result = $db->Execute($strSQL)) {
//			if ($result->RecordCount()) {
//				if ($row = $result->FetchRow()) {
//					$opdate = strftime("%m/%d/%Y", strtotime($row["date_operation"]));
//				}
//			}
//		}

//		return $opdate;
//	}

	function getPostOpData($refno, &$op_date, &$op_performed, &$postop_diag) {
		global $db;

		$strSQL = "SELECT
									encounter_nr,
									date_operation,
									operation_performed,
									post_op_diagnosis
								FROM seg_or_main_post d
									INNER JOIN seg_or_main h
										ON d.or_main_refno = h.or_main_refno
								WHERE h.ceo_refno = '$refno'";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				if ($row = $result->FetchRow()) {
//					$enc_nr  = $row["encounter_nr"];
					$op_date = (is_null($row["date_operation"])) ? "0000-00-00" : $row["date_operation"];
					$op_performed = (is_null($row["operation_performed"])) ? "" : $row["operation_performed"];
					$postop_diag =  (is_null($row["post_op_diagnosis"])) ? "" : $row["post_op_diagnosis"];
				}
			}
		}
	}

	function getRecommendations($enc_nr) {
		global $db;

		$recommendation = "";
		$strSQL = "SELECT
									create_time,
									clinical_summary
								FROM seg_doctors_notes n
								WHERE n.encounter_nr = '$enc_nr'
								ORDER BY create_time DESC";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				while ($row = $result->FetchRow()) {
					if ($recommendation != '') $recommendation .= "\n";
					$recommendation .= strftime("%b %d, %Y %r", strtotime($row["create_time"]))."\t".$row["clinical_summary"];
				}
			}
		}

		return $recommendation;
	}

	function getDischargeCondition($enc_nr) {
		global $db;

		$disposition = "";
		$strSQL = "SELECT
									disp_desc
								FROM seg_encounter_disposition ed
									INNER JOIN seg_dispositions d
										ON ed.disp_code = d.disp_code
								WHERE encounter_nr = '$enc_nr'
								ORDER BY ed.modify_time DESC
								LIMIT 1";
		if ($result = $db->Execute($strSQL)) {
			if ($result->RecordCount()) {
				if ($row = $result->FetchRow()) {
					$disposition = $row["disp_desc"];
				}
			}
		}

		return $disposition;
	}
?>
