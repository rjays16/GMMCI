<?php
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');


if(isset($_GET['patient_pid'])){
		$sum_occur_ledger = 0;
		$sum_occur_abs = 0;
		$total_pev_rem = 0;
		$patient_pid = $_GET['patient_pid'];
		#has_patient = has_patient_ledger_prompt("10000091");
		$has_patient = has_patient_ledger_prompt($patient_pid);
		if($has_patient){
			$total_pev_rem = number_format($has_patient['total_pev_rem'],2,'.',',');
			$sum_occur_ledger = $has_patient['sum_occur_ledger'];
			$sum_occur_abs = $has_patient['sum_occur_abs'];
		}

		echo "Param:"."$sum_occur_ledger".
				 "Param:"."$total_pev_rem".
				 "Param:"."$sum_occur_abs";

}else{
		$sum_occur_ledger = 0;
		$sum_occur_abs = 0;
		$total_pev_rem = 0;
		#$has_patient = has_patient_ledger_prompt("10000091");
		$has_patient = has_patient_ledger_prompt($patient_pid);
		if($has_patient){
			$total_pev_rem = number_format($has_patient['total_pev_rem'],2,'.',',');
			$sum_occur_ledger = $has_patient['sum_occur_ledger'];
			$sum_occur_abs = $has_patient['sum_occur_abs'];
		}

?>

	<script language="javascript">

		var has_occur_ledger = '<?php echo $sum_occur_ledger; ?> ';
		var total_prev_rem = '<?php echo $total_pev_rem; ?> ';
		var has_occur_abs = '<?php echo $sum_occur_abs; ?> ';

		var strMsg = "";
		if(has_occur_ledger>0 && total_prev_rem!=0)
			strMsg = "The patient has previous remaining total payment of Php "+total_prev_rem+"  \n";
		if(has_occur_abs>0)
			strMsg = strMsg + "The patient was previously absconded!! \n";
		if(strMsg>""){
			strMsg = strMsg + "";
			alert(strMsg);
		}
	</script>
<?php
}



 #added by angelo 11.11.2010
	#start---------------------------------------------------
	#function: determine whether the patient has patiend ledger history or
	#          included in dispositions which are absconded.
	#
	#
	function has_patient_ledger_prompt($pid){
		global $db;

		$sum_occur = 0;
		$strSQL = "SELECT
									SUM(total_remaining) AS total_pev_rem,
									SUM(pe.occur_ledger) AS sum_occur_ledger,
									SUM(pe.occur_abs) AS sum_occur_abs,
									pe.pid
								FROM (SELECT
												( SUM(pl.debit)-SUM(pl.credit) ) AS total_remaining,
												COUNT(pl.entry_no) AS occur_ledger,
												''		   AS occur_abs,
												pl.pid
											FROM seg_person_ledger AS pl
											WHERE pl.pid = '$pid'
													AND pl.is_deleted <> 1
											GROUP BY pl.pid UNION ALL SELECT
																									''       AS total_remaining,
																									'' 	   AS occur_ledger,
																									COUNT(sed.encounter_nr) AS occur_abs,
																									ce.pid
																								FROM seg_encounter_disposition AS sed
																									INNER JOIN seg_dispositions AS sd
																										ON sed.disp_code = sd.disp_code
																									INNER JOIN care_encounter AS ce
																										ON ce.encounter_nr = sed.encounter_nr
																											AND ce.encounter_nr NOT IN(SELECT
																																									 MAX(encounter_nr) AS max_encounter_nr
																																								 FROM care_encounter
																																								 WHERE pid = '$pid')
																											AND ce.pid = '$pid'
																								WHERE sed.disp_code NOT IN('5','10')
																								GROUP BY sed.encounter_nr,ce.pid) AS pe
								GROUP BY pid;";

		echo $strSQL;
		$result = $db->Execute($strSQL);
		if($result)
			if($row=$result->FetchRow()){
				return $row;
			}else{
				return false;
			}

	}
	#end---------------------------------------------------






?>
